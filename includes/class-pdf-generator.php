<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Герерация PDF
 */
class EM_PDF_Generator {

    public static function init() {
        add_action('admin_post_em_download_pdf', array(__CLASS__, 'download_pdf'));
        add_action('admin_post_nopriv_em_download_pdf', array(__CLASS__, 'download_pdf'));
        add_action('admin_post_em_preview_pdf', array(__CLASS__, 'preview_pdf'));
        add_action('admin_post_nopriv_em_preview_pdf', array(__CLASS__, 'preview_pdf'));
        add_action('admin_post_em_download_single_pdf', array(__CLASS__, 'download_single_pdf'));
        add_action('admin_post_nopriv_em_download_single_pdf', array(__CLASS__, 'download_single_pdf'));
        add_action('admin_post_em_preview_single_pdf', array(__CLASS__, 'preview_single_pdf'));
        add_action('admin_post_nopriv_em_preview_single_pdf', array(__CLASS__, 'preview_single_pdf'));
    }

    /**
     * Фильтры
     */
    private static function get_employees() {

        // nonce
        if (!isset($_GET['em_pdf_nonce']) || !wp_verify_nonce($_GET['em_pdf_nonce'], 'em_download_pdf')) {
            wp_die('Некорректный запрос');
        }


        // Чтобы PDF содержал только отфильтрованных сотрудников

        $args = array(
            'post_type' => 'employees',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $meta_query = array();

        if (isset($_GET['filter_department']) && !empty($_GET['filter_department'])) {
            $meta_query[] = array(
                'key' => 'department',
                'value' => sanitize_text_field($_GET['filter_department']),
                'compare' => '=',
            );
        }

        if (isset($_GET['filter_position']) && !empty($_GET['filter_position'])) {
            $meta_query[] = array(
                'key' => 'position',
                'value' => sanitize_text_field($_GET['filter_position']),
                'compare' => 'LIKE',
            );
        }

        if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
            $meta_query[] = array(
                'key' => 'status',
                'value' => sanitize_text_field($_GET['filter_status']),
                'compare' => '=',
            );
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        return get_posts($args);
    }


    public static function download_pdf() {
        $employees = self::get_employees();
        self::render_list_pdf($employees, true);
    }

    public static function preview_pdf() {
        $employees = self::get_employees();
        self::render_list_pdf($employees, false);
    }

    /**
     * Helper: Convert image attachment to base64-encoded data URI
     */
    private static function get_image_data_uri($thumbnail_id) {
        if (!$thumbnail_id) {
            return '';
        }

        // Try 1: Use get_attached_file to get local file path
        $attached_file = get_attached_file($thumbnail_id);
        if ($attached_file && file_exists($attached_file) && is_readable($attached_file)) {
            $file_data = file_get_contents($attached_file);
            if ($file_data !== false) {
                // Get correct MIME type
                $file_info = getimagesize($attached_file);
                if ($file_info && !empty($file_info['mime'])) {
                    return 'data:' . $file_info['mime'] . ';base64,' . base64_encode($file_data);
                }
                // Fallback MIME type
                $ext = strtolower(pathinfo($attached_file, PATHINFO_EXTENSION));
                $mime_types = array(
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                );
                $mime = isset($mime_types[$ext]) ? $mime_types[$ext] : 'image/jpeg';
                return 'data:' . $mime . ';base64,' . base64_encode($file_data);
            }
        }

        // Try 2: Use wp_get_attachment_url and allow_url_fopen
        $photo_url = wp_get_attachment_url($thumbnail_id);
        if ($photo_url && ini_get('allow_url_fopen')) {
            $file_data = file_get_contents($photo_url);
            if ($file_data !== false) {
                $mime = get_post_mime_type($thumbnail_id);
                if (!$mime) {
                    $ext = strtolower(pathinfo(parse_url($photo_url, PHP_URL_PATH), PATHINFO_EXTENSION));
                    $mime_types = array(
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                    );
                    $mime = isset($mime_types[$ext]) ? $mime_types[$ext] : 'image/jpeg';
                }
                return 'data:' . $mime . ';base64,' . base64_encode($file_data);
            }
        }

        // If everything fails
        return '';
    }

    /**
     * Рендерим PDF списка
     */
    public static function render_list_pdf($employees, $download = true) {
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('fontDir', EM_PLUGIN_DIR . 'vendor/dompdf/dompdf/lib/fonts/');
        $options->set('fontCache', EM_PLUGIN_DIR . 'vendor/dompdf/dompdf/lib/fonts/');

        // Process employees to include photo data URIs
        $processed_employees = array();
        foreach ($employees as $employee) {
            $thumbnail_id = get_post_thumbnail_id($employee->ID);
            $processed_employees[] = array(
                'post' => $employee,
                'photo_data' => $thumbnail_id ? self::get_image_data_uri($thumbnail_id) : '',
                'thumbnail_id' => $thumbnail_id,
            );
        }

        $dompdf = new \Dompdf\Dompdf($options);

        ob_start();
        include EM_PLUGIN_DIR . 'templates/pdf-template.php';
        $html = ob_get_clean();

        // Для поддержки кириллицы
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('spisok-sotrudnikov.pdf', array('Attachment' => $download));
        exit;
    }

    /**
     * Отдельный сотрудник
     */
    private static function get_single_employee() {

        if (!isset($_GET['em_pdf_nonce']) || !wp_verify_nonce($_GET['em_pdf_nonce'], 'em_download_single_pdf')) {
            wp_die('Некорректный запрос');
        }

        if (!isset($_GET['employee_id'])) {
            wp_die('Не указан ID сотрудника');
        }

        $employee = get_post(intval($_GET['employee_id']));

        if (!$employee || $employee->post_type !== 'employees') {
            wp_die('Сотрудник не найден');
        }

        return $employee;
    }


    /**
     * PDF одного сотрудника
     */

    public static function download_single_pdf() {
        $employee = self::get_single_employee();
        self::render_single_pdf($employee, true);
    }

    public static function preview_single_pdf() {
        $employee = self::get_single_employee();
        self::render_single_pdf($employee, false);
    }

    public static function render_single_pdf($employee, $download = true) {
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isFontSubsettingEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('fontDir', EM_PLUGIN_DIR . 'vendor/dompdf/dompdf/lib/fonts/');
        $options->set('fontCache', EM_PLUGIN_DIR . 'vendor/dompdf/dompdf/lib/fonts/');

        // Process employee photo to base64 data URI
        $thumbnail_id = get_post_thumbnail_id($employee->ID);
        $photo_data = $thumbnail_id ? self::get_image_data_uri($thumbnail_id) : '';

        $dompdf = new \Dompdf\Dompdf($options);

        ob_start();
        include EM_PLUGIN_DIR . 'templates/pdf-single-template.php';
        $html = ob_get_clean();

        // Поддержка кириллицы
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('sotrudnik-' . sanitize_title(get_field('full_name', $employee->ID)) . '.pdf', array('Attachment' => $download));
        exit;
    }
}