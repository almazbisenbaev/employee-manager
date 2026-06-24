<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Регистрируем пост тайп
 */
class EM_Post_Type {

    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_filter('archive_template', array(__CLASS__, 'load_archive_template'));
        add_action('pre_get_posts', array(__CLASS__, 'filter_employees_query'));
    }


    public static function filter_employees_query($query) {
        if ($query->is_main_query() && !is_admin() && is_post_type_archive('employees')) {
            $meta_query = array();

            if (isset($_GET['filter_department']) && !empty($_GET['filter_department'])) {
                $meta_query[] = array(
                    'key'     => 'department',
                    'value'   => sanitize_text_field($_GET['filter_department']),
                    'compare' => '=',
                );
            }

            if (isset($_GET['filter_position']) && !empty($_GET['filter_position'])) {
                $meta_query[] = array(
                    'key'     => 'position',
                    'value'   => sanitize_text_field($_GET['filter_position']),
                    'compare' => 'LIKE',
                );
            }

            if (isset($_GET['filter_status']) && !empty($_GET['filter_status'])) {
                $meta_query[] = array(
                    'key'     => 'status',
                    'value'   => sanitize_text_field($_GET['filter_status']),
                    'compare' => '=',
                );
            }

            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }

        }
    }


    public static function load_archive_template($template) {
        if (is_post_type_archive('employees')) {
            $plugin_template = EM_PLUGIN_DIR . 'templates/archive-employees.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }


    public static function register_post_type() {
        $labels = array(
            'name'               => __('Сотрудники', 'employee-manager'),
            'singular_name'      => __('Сотрудник', 'employee-manager'),
            'menu_name'          => __('Сотрудники', 'employee-manager'),
            'name_admin_bar'     => __('Сотрудник', 'employee-manager'),
            'add_new'            => __('Добавить нового', 'employee-manager'),
            'add_new_item'       => __('Добавить нового сотрудника', 'employee-manager'),
            'new_item'           => __('Новый сотрудник', 'employee-manager'),
            'edit_item'          => __('Редактировать', 'employee-manager'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'employees'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'show_in_rest'       => true,
            'supports'           => array('title', 'editor', 'thumbnail'),
            'menu_icon'          => 'dashicons-groups',
        );

        register_post_type('employees', $args);
    }
}
