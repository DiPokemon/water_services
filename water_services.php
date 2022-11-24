<?php
/*
Plugin Name: Услуги
Description: Блок услуги + слайдер на мобильных
Version: 1.0
*/

global $wpdb;

if ( ! defined( 'ABSPATH' ) ) exit;



/**************
 * Константы
 **************/
define( 'WATER_SERVICES_PLUGIN_DB_VERSION', '1.0' );
define( 'WATER_SERVICES_PLUGIN_NAME',     'water_services' );
define( 'WATER_SERVICES_PLUGIN_NAME_RU',  'Услуги' );
define( 'WATER_SERVICES_DB_TABLE_NAME',    $wpdb->prefix . WATER_SERVICES_PLUGIN_NAME );
define( 'WATER_SERVICES_PLUGIN_DIR',       plugin_dir_path( __FILE__ ) );
define( 'WATER_SERVICES_PLUGIN_ADMIN_URL', admin_url('?page=' . WATER_SERVICES_PLUGIN_NAME) );



/**************
 * Class
 **************/
require_once dirname(__FILE__) . '/inc/class-main.php';
require_once dirname(__FILE__) . '/inc/class-model.php';
$water_main_class = new WaterServices( __FILE__ );



/**************
 * Run
 **************/

// Правила активации:
// register_activation_hook() должен вызываться из основного файла плагина, из того где расположена директива Plugin Name
register_activation_hook(__FILE__, array($water_main_class, 'activate'));

function services_plugin_load_scripts()
{    
  wp_enqueue_script('init_services_slider', '/wp-content/plugins/water_services/static/js/init_services_slider.js', array('slick'), NULL, true);
} 
add_action('wp_enqueue_scripts', 'services_plugin_load_scripts', 10);