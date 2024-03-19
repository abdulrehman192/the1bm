<?php
global $BookingPress, $bookingpress_google_captcha_version, $wpdb;
$bookingpress_old_gc_version = get_option('bookingpress_google_captcha_version', true);

$bookingpress_gc_new_version = '1.3';
update_option('bookingpress_google_captcha_version', $bookingpress_gc_new_version);
update_option('bookingpress_google_captcha_updated_date_' . $bookingpress_gc_new_version, current_time('mysql'));