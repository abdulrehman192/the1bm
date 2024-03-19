<?php

global $BookingPress, $bookingpress_waiting_list_version, $wpdb;
$bookingpress_old_waiting_list_version = get_option('bookingpress_waiting_list_version', true);


$bookingpress_waiting_list_new_version = '1.5';
update_option('bookingpress_waiting_list_version', $bookingpress_waiting_list_new_version);
update_option('bookingpress_waiting_list_version_date_' . $bookingpress_waiting_list_new_version, current_time('mysql'));