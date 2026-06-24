<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Класс для работы с ACF
 */
class EM_ACF {

    /**
     * Хуки
     */
    public static function init() {
        // Проверяем что ACF на месте
        add_action('admin_notices', array(__CLASS__, 'check_acf_dependency'));
        if (class_exists('ACF')) {
            add_action('acf/init', array(__CLASS__, 'register_fields'));
        }
    }

    /**
     * Если ACF не активен, то показываем ошибку
     */
    public static function check_acf_dependency() {
        // Проверяем, активен ли ACF
        if (!class_exists('ACF')) { ?>
            <div class="notice notice-error">
                <p><?php esc_html_e('Для работы Employee Manager требуется плагин ACF.', 'employee-manager'); ?></p>
            </div>
        <?php
        }
    }

    /**
     * ACF группы
     */
    public static function register_fields() {
        // Регистрируем группу полей для сотрудников
        acf_add_local_field_group(array(
            'key' => 'group_employee_fields',
            'title' => __('Данные сотрудника', 'employee-manager'),
            'fields' => array(
                // ФИО
                array(
                    'key' => 'field_full_name',
                    'label' => __('ФИО', 'employee-manager'),
                    'name' => 'full_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                // Должность
                array(
                    'key' => 'field_position',
                    'label' => __('Должность', 'employee-manager'),
                    'name' => 'position',
                    'type' => 'text',
                    'required' => 1,
                ),
                // Отдел
                array(
                    'key' => 'field_department',
                    'label' => __('Отдел', 'employee-manager'),
                    'name' => 'department',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'development' => __('Разработка', 'employee-manager'),
                        'marketing' => __('Маркетинг', 'employee-manager'),
                        'sales' => __('Продажи', 'employee-manager'),
                        'hr' => __('HR', 'employee-manager'),
                    ),
                    'default_value' => 'development',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                ),
                // Дата приема
                array(
                    'key' => 'field_hire_date',
                    'label' => __('Дата приема', 'employee-manager'),
                    'name' => 'hire_date',
                    'type' => 'date_picker',
                    'required' => 1,
                    'display_format' => 'd/m/Y',
                    'return_format' => 'd/m/Y',
                    'first_day' => 1,
                ),
                // Статус
                array(
                    'key' => 'field_status',
                    'label' => __('Статус', 'employee-manager'),
                    'name' => 'status',
                    'type' => 'select',
                    'required' => 1,
                    'choices' => array(
                        'active' => __('Работает', 'employee-manager'),
                        'fired' => __('Уволен', 'employee-manager'),
                        'vacation' => __('В отпуске', 'employee-manager'),
                    ),
                    'default_value' => 'active',
                    'allow_null' => 0,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'value',
                    'ajax' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'employees',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
    }
}
