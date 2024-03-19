<?php

if (is_ssl()) {
    define('BOOKINGPRESS_LOCATION_URL', str_replace('http://', 'https://', WP_PLUGIN_URL . '/' . BOOKINGPRESS_LOCATION_DIR_NAME));
} else {
    define('BOOKINGPRESS_LOCATION_URL', WP_PLUGIN_URL . '/' . BOOKINGPRESS_LOCATION_DIR_NAME);
}

define( 'BOOKINGPRESS_LOCATION_VIEWS_DIR', BOOKINGPRESS_LOCATION_DIR . '/core/views' );
define( 'BOOKINGPRESS_LOCATION_VIEWS_URL', BOOKINGPRESS_LOCATION_DIR . '/core/views' );

if(file_exists(BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location.php") ){
	require_once BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location.php";
}

if(file_exists(BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-workhours.php") ){
	require_once BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-workhours.php";
}

if(file_exists(BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-services.php") ){
	require_once BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-services.php";
}

if(file_exists(BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-customize.php") ){
	require_once BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-customize.php";
}

if(file_exists(BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-booking-form.php") ){
	require_once BOOKINGPRESS_LOCATION_DIR . "/core/classes/class.bookingpress-location-booking-form.php";
}

global $bookingpress_location_version;
$bookingpress_location_version = '1.2';
define('BOOKINGPRESS_LOCATION_VERSION', $bookingpress_location_version);

load_plugin_textdomain( 'bookingpress-location', false, 'bookingpress-location/languages/' );


define( 'BOOKINGPRESS_LOCATION_STORE_URL', 'https://www.bookingpressplugin.com/' );

if ( ! class_exists( 'bookingpress_pro_updater' ) ) {
	require_once BOOKINGPRESS_LOCATION_DIR . '/core/classes/class.bookingpress_pro_plugin_updater.php';
}

function bookingpress_location_plugin_updater() {

	$plugin_slug_for_update = 'bookingpress-location/bookingpress-location.php';

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'bkp_location_license_key' ) );
	$package = trim( get_option( 'bkp_location_license_package' ) );

	// setup the updater
	$edd_updater = new bookingpress_pro_updater(
		BOOKINGPRESS_LOCATION_STORE_URL,
		$plugin_slug_for_update,
		array(
			'version' => BOOKINGPRESS_LOCATION_VERSION,  // current version number
			'license' => $license_key,             // license key (used get_option above to retrieve from DB)
			'item_id' => $package,       // ID of the product
			'author'  => 'Repute Infosystems', // author of this plugin
			'beta'    => false,
		)
	);

}
add_action( 'init', 'bookingpress_location_plugin_updater' );


?>