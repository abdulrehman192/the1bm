<?php
/*
Plugin Name: BookingPress - Google Captcha Integration Addon
Description: Extension for BookingPress plugin to captcha validation.
Version: 1.3
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-google-captcha
Domain Path: /languages
*/

define('BOOKINGPRESS_GOOGLE_CAPTCHA_DIR_NAME', 'bookingpress-google-captcha');
define('BOOKINGPRESS_GOOGLE_CAPTCHA_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_GOOGLE_CAPTCHA_DIR_NAME);

if (file_exists( BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . '/autoload.php';
}