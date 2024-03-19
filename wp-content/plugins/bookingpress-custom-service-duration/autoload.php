<?php

if (is_ssl()) {
    define('BOOKINGPRESS_CUSTOM_SERVICE_DURATION_URL', str_replace('http://', 'https://', WP_PLUGIN_URL . '/' . BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR_NAME));
} else {
    define('BOOKINGPRESS_CUSTOM_SERVICE_DURATION_URL', WP_PLUGIN_URL . '/' . BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR_NAME);
}

define( 'BOOKINGPRESS_CUSTOM_SERVICE_DURATION_STORE_URL', 'https://www.bookingpressplugin.com/' );

if(file_exists(BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . "/core/classes/class.bookingpress-custom-service-duration.php") ){
	require_once BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . "/core/classes/class.bookingpress-custom-service-duration.php";
}

global $bookingpress_custom_service_duration_version;
$bookingpress_custom_service_duration_version = '1.8';

define('BOOKINGPRESS_CUSTOM_SERVICE_DURATION_VERSION', $bookingpress_custom_service_duration_version);

load_plugin_textdomain( 'bookingpress-custom-service-duration', false, 'bookingpress-custom-service-duration/languages/' );

if ( ! class_exists( 'bookingpress_pro_updater' ) ) {
	require_once BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . '/core/classes/class.bookingpress_pro_plugin_updater.php';
}

function bookingpress_custom_service_duration_plugin_updater() {

	$plugin_slug_for_update = 'bookingpress-custom-service-duration/bookingpress-custom-service-duration.php';

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'bkp_custom_service_duration_license_key' ) );
	$package = trim( get_option( 'bkp_custom_service_duration_license_package' ) );

	// setup the updater
	$edd_updater = new bookingpress_pro_updater(
		BOOKINGPRESS_CUSTOM_SERVICE_DURATION_STORE_URL,
		$plugin_slug_for_update,
		array(
			'version' => BOOKINGPRESS_CUSTOM_SERVICE_DURATION_VERSION,  // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => $package,       // ID of the product
			'author'  => 'Repute Infosystems', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'bookingpress_custom_service_duration_plugin_updater' );
?>
