<?php

global $BookingPress, $bookingpress_tip_version, $wpdb, $tbl_bookingpress_customize_settings;
$bookingpress_old_tip_version = get_option('bookingpress_tip_addon', true);

if (version_compare($bookingpress_old_tip_version, '1.4', '<') ) {

    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
    $booking_form = array(
        'tip_label_txt'	=> __('Give a tip', 'bookingpress-tip'),
        'tip_button_txt'	=> __('Apply', 'bookingpress-tip'),
        'tip_placeholder_txt'	=> __('Enter tip amount', 'bookingpress-tip'),
        'tip_applied_title' => __('Tip Applied','bookingpress-tip'),
        'tip_error_msg'     => __('Please enter tip amount', 'bookingpress-tip'),
    );
    foreach($booking_form as $key => $value) {
        $bookingpress_customize_settings_db_fields = array(
            'bookingpress_setting_name'  => $key,
            'bookingpress_setting_value' => $value,
            'bookingpress_setting_type'  => 'package_booking_form',
        );
        $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
    }

}


$bookingpress_tip_new_version = '1.4';
update_option('bookingpress_tip_addon', $bookingpress_tip_new_version);
update_option('bookingpress_tip_addon_date_' . $bookingpress_tip_new_version, current_time('mysql'));