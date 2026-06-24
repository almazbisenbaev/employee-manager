<?php
if (!defined('ABSPATH')) {
    exit;
}

class EM_Assets {

    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_frontend_assets'));
    }

    public static function enqueue_frontend_assets() {

        // Подключаем наш CSS файл только на архиве сотрудников
        if (is_post_type_archive('employees')) {

            wp_enqueue_style(
                'employee-manager-css',
                EM_PLUGIN_URL . 'assets/css/employee-manager.css',
                array(),
                EM_PLUGIN_VERSION
            );

            wp_enqueue_script(
                'employee-manager-js',
                EM_PLUGIN_URL . 'assets/js/employee-manager.js',
                array(),
                EM_PLUGIN_VERSION,
                true
            );

            // Передаем переменные из PHP в JS
            wp_localize_script('employee-manager-js', 'emData', array(
                'previewPdfUrl' => esc_url(add_query_arg(array(
                    'action' => 'em_preview_pdf',
                    'em_pdf_nonce' => wp_create_nonce('em_download_pdf'),
                    'filter_department' => isset($_GET['filter_department']) ? sanitize_text_field($_GET['filter_department']) : '',
                    'filter_position' => isset($_GET['filter_position']) ? sanitize_text_field($_GET['filter_position']) : '',
                    'filter_status' => isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '',
                ), admin_url('admin-post.php'))),
                'textListPreview' => __('Предпросмотр списка PDF', 'employee-manager'),
                'textEmployeePreview' => __('Предпросмотр сотрудника', 'employee-manager')
            ));

        }
    }

}
