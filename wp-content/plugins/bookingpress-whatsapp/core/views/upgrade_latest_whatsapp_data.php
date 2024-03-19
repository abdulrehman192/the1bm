<?php

global $BookingPress, $wpdb, $bookingpress_whatsapp_version;
$bookingpress_whatsapp_old_version = get_option( 'bookingpress_whatsapp_gateway' );

if (version_compare($bookingpress_whatsapp_old_version, '1.3', '<') ) {
    $tbl_bookingpress_notifications = $wpdb->prefix . 'bookingpress_notifications';
    
    $wpdb->query("ALTER TABLE {$tbl_bookingpress_notifications} ADD bookingpress_whatsapp_admin_number VARCHAR(60) NULL DEFAULT NULL AFTER bookingpress_send_whatsapp_notification");  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_notifications is a table name. false alarm
}

$bookingpress_whatsapp_new_version = '1.8';
update_option('bookingpress_whatsapp_gateway', $bookingpress_whatsapp_new_version);
update_option('bookingpress_whatsapp_gateway_updated_date_' . $bookingpress_whatsapp_new_version, current_time('mysql'));