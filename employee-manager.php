<?php
/**
 * Plugin Name: Employee Manager
 * Plugin URI: https://google.com
 * Description: Плагин для управления списком сотрудников с фильтрацией и экспортом в PDF
 * Version: 1.0.0
 * Author: Almaz
 * Author URI: https://google.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: employee-manager
 */

if (!defined('ABSPATH')) {
    exit;
}

define('EM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EM_PLUGIN_VERSION', '1.0.0');

// Подключаем composer autoloader
require_once EM_PLUGIN_DIR . 'vendor/autoload.php';


require_once EM_PLUGIN_DIR . 'includes/class-post-type.php';
require_once EM_PLUGIN_DIR . 'includes/class-acf.php';
require_once EM_PLUGIN_DIR . 'includes/class-assets.php';
require_once EM_PLUGIN_DIR . 'includes/class-pdf-generator.php';

function em_init_plugin() {

    // Пост тайпы
    EM_Post_Type::init();
    
    // Группы ACF
    EM_ACF::init();
    
    // CSS и JS
    EM_Assets::init();
    
    // PDF генератор
    EM_PDF_Generator::init();
}

add_action('plugins_loaded', 'em_init_plugin');
