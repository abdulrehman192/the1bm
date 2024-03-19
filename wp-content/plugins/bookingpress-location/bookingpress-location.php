<?php
/*
Plugin Name: BookingPress - Location Addon
Description: Extension for BookingPress plugin to location integration.
Version: 1.2
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-location
Domain Path: /languages
*/

define('BOOKINGPRESS_LOCATION_DIR_NAME', 'bookingpress-location');
define('BOOKINGPRESS_LOCATION_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_LOCATION_DIR_NAME);

if (file_exists( BOOKINGPRESS_LOCATION_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_LOCATION_DIR . '/autoload.php';
}