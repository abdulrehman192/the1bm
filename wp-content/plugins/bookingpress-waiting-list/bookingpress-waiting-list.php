<?php
/*
Plugin Name: BookingPress - Waiting List Addon
Description: Extension for BookingPress plugin Add your customers to a waiting list on fully booked slot/days.
Version: 1.5
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-waiting-list
Domain Path: /languages
*/

define('BOOKINGPRESS_WAITING_LIST_DIR_NAME', 'bookingpress-waiting-list');
define('BOOKINGPRESS_WAITING_LIST_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_WAITING_LIST_DIR_NAME);
define('BOOKINGPRESS_WAITING_VIEW_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_WAITING_LIST_DIR_NAME.'/core/views/');
 
global $wpdb;
$bpa_lite_plugin_version = get_option('bookingpress_version');
$bpa_pro_plugin_version = get_option('bookingpress_pro_version');

if(file_exists(BOOKINGPRESS_WAITING_LIST_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_WAITING_LIST_DIR . '/autoload.php';
}