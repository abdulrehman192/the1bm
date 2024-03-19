<?php
global $wpdb, $BookingPress, $bookingpress_custom_service_duration;

$bookingpress_custom_service_duration_old_version = get_option('bookingpress_custom_service_duration_version', true);

if (version_compare($bookingpress_custom_service_duration_old_version, '1.6', '<') ) {
    global $BookingPress;
    $bookingpress_db_fields = array(
        'bookingpress_setting_name'  => 'no_custom_service_duration_selected_title',
        'bookingpress_setting_value' => 'Please select custom service duration value',
        'bookingpress_setting_type'  => 'booking_form',
    );    
    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
    $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_db_fields);
}

$bookingpress_new_custom_service_duration_version = '1.8';
update_option( 'bookingpress_custom_service_duration_version', $bookingpress_new_custom_service_duration_version );
update_option( 'bookingpress_custom_service_duration_updated_date_' . $bookingpress_new_custom_service_duration_version, current_time('mysql') );