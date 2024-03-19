<?php
/*
Plugin Name: BookingPress Tip Addon
Description: Extension for BookingPress plugin to apply tip on appointment booking
Version: 1.4
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-tip
Domain Path: /languages
*/

define('BOOKINGPRESS_TIP_DIR_NAME', 'bookingpress-tip');
define('BOOKINGPRESS_TIP_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_TIP_DIR_NAME);

if (file_exists( BOOKINGPRESS_TIP_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_TIP_DIR . '/autoload.php';
}