<?php
global $BookingPress, $bookingpress_location_version, $wpdb;
$bookingpress_old_location_version = get_option('bookingpress_location_version', true);

if( version_compare( $bookingpress_old_location_version, '1.1', '<') ){
    global $tbl_bookingpress_locations, $wpdb;
    $bookingpress_locations_list = $wpdb->get_results("SELECT bookingpress_location_id,bookingpress_location_position FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_id ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
    if(!empty($bookingpress_locations_list)) {
        $position=0;
        foreach( $bookingpress_locations_list as $location_data ){
            $location_id = intval( $location_data['bookingpress_location_id'] );
            $args     = array(
                'bookingpress_location_position' => $position,
            );
            $wpdb->update($tbl_bookingpress_locations, $args, array( 'bookingpress_location_id' => $location_id ));
            $position++;
        }
    }
}

if( version_compare ( $bookingpress_old_location_version, '1.2', '<')){

    global $wpdb, $tbl_bookingpress_locations_service_staff_pricing_details;

    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_locations_service_staff_pricing_details} ADD bookingpress_staff_location_min_qty int(11) DEFAULT 1 AFTER bookingpress_staff_location_qty" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations_service_staff_pricing_details is a table name. false alarm

    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_locations_service_staff_pricing_details} ADD bookingpress_service_min_qty int(11) DEFAULT 1 AFTER bookingpress_service_qty" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations_service_staff_pricing_details is a table name. false alarm

}

$bookingpress_location_new_version = '1.2';
update_option('bookingpress_location_version', $bookingpress_location_new_version);
update_option('bookingpress_location_updated_date_' . $bookingpress_location_new_version, current_time('mysql'));

