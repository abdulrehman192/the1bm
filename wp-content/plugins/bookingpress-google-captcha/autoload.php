<?php

if (is_ssl()) {
    define('BOOKINGPRESS_GOOGLE_CAPTCHA_URL', str_replace('http://', 'https://', WP_PLUGIN_URL . '/' . BOOKINGPRESS_GOOGLE_CAPTCHA_DIR_NAME));
} else {
    define('BOOKINGPRESS_GOOGLE_CAPTCHA_URL', WP_PLUGIN_URL . '/' . BOOKINGPRESS_GOOGLE_CAPTCHA_DIR_NAME);
}

define('BOOKINGPRESS_GOOGLE_CAPTCHA_LIBRARY_DIR', BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . '/lib');
define('BOOKINGPRESS_GOOGLE_CAPTCHA_LIBRARY_URL', BOOKINGPRESS_GOOGLE_CAPTCHA_URL . '/lib');

if(file_exists(BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . "/core/classes/class.bookingpress-google-captcha.php") ){
	require_once BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . "/core/classes/class.bookingpress-google-captcha.php";
}

global $bookingpress_google_captcha_version;
$bookingpress_google_captcha_version = '1.3';
define('BOOKINGPRESS_GOOGLE_CAPTCHA_VERSION', $bookingpress_google_captcha_version);

load_plugin_textdomain( 'bookingpress-google-captcha', false, 'bookingpress-google-captcha/languages/' );

define( 'BOOKINGPRESS_GOOGLE_CAPTCHA_STORE_URL', 'https://www.bookingpressplugin.com/' );

if ( ! class_exists( 'bookingpress_pro_updater' ) ) {
	require_once BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . '/core/classes/class.bookingpress_pro_plugin_updater.php';
}

function bookingpress_google_captcha_plugin_updater() {
	
	$plugin_slug_for_update = 'bookingpress-google-captcha/bookingpress-google-captcha.php';
	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'bkp_google_captcha_license_key' ) );
	$package = trim( get_option( 'bkp_google_captcha_license_package' ) );

	// setup the updater
	$edd_updater = new bookingpress_pro_updater(
		BOOKINGPRESS_GOOGLE_CAPTCHA_STORE_URL,
		$plugin_slug_for_update,
		array(
			'version' => BOOKINGPRESS_GOOGLE_CAPTCHA_VERSION,  // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => $package,       // ID of the product
			'author'  => 'Repute Infosystems', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'bookingpress_google_captcha_plugin_updater' );

?>