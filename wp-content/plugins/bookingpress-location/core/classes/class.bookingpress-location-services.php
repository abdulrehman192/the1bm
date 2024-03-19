<?php
if (!class_exists('bookingpress_location_services')) {
	class bookingpress_location_services Extends BookingPress_Core {
        function __construct(){

            global $BookingPress;

            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if( is_plugin_active( 'bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) && !empty( $BookingPress->bpa_pro_plugin_version() ) && version_compare( $BookingPress->bpa_pro_plugin_version(), '2.6.1', '>=' ) ){
                add_action('bookingpress_add_service_extra_section', array($this, 'bookingpress_add_content_after_basic_details_func'),12);
    
                add_filter( 'bookingpress_modify_service_data_fields', array( $this, 'bookingpress_modify_service_data_fields_func' ), 11 );
    
                add_filter( 'bookingpress_modify_staffmember_data_fields', array( $this, 'bookingpress_modify_staff_data_fields_func'), 11 );
    
                add_action( 'bookingpress_add_service_dynamic_vue_methods', array( $this, 'bookingpress_add_service_dynamic_vue_methods_func' ), 10 );
    
                add_filter( 'bookingpress_after_add_update_service', array( $this, 'bookingpress_save_service_details' ), 12, 3 );
    
                add_action( 'bookingpress_add_posted_data_for_save_service', array( $this, 'bookingpress_add_posted_data_for_save_service_func' ), 11 );
    
                add_action('bookingpress_add_dynamic_content_for_add_staff', array($this, 'bookingpress_add_dynamic_content_for_add_staff_func'));
    
                add_action( 'bookingpress_after_open_assign_staffmember_model', array( $this, 'bookingpress_assign_default_location_for_staff_model') );            
    
                add_action('bookingpress_service_dialog_outside', array($this, 'bookingpress_service_dialog_outside_func'));
    
                add_action('bookingpress_modify_get_staffmember_assigend_service_request', array($this, 'bookingpress_modify_get_staffmember_assigend_service_request_func'));
    
                add_filter( 'bookingpress_modify_staffmember_service_data', array( $this,'bookingpress_assign_location_to_staffmember'), 10, 2);
    
                add_action( 'bookingpress_service_staff_extra_column_outside', array( $this, 'bookingpress_service_staff_section_location_column'));
    
                add_action( 'bookingpress_service_staff_extra_column_value_outside', array( $this, 'bookingpress_service_staff_section_location_column_value') );
    
                add_filter( 'bookingpress_modify_edit_service_data', array( $this, 'bookingpress_add_location_data_for_services' ), 11, 2 );
    
                add_action( 'bookingpress_edit_service_more_vue_data', array( $this, 'bookingpress_add_location_data_for_service_xhr_response') );
    
                add_action( 'bookingpress_after_close_add_service_form', array( $this, 'bookingpress_reset_service_model_location_data') );
    
                add_action( 'bookingpress_service_edit_assigned_staffmember', array( $this, 'bookingpress_service_set_locations_for_assigned_staffmember') );
    
                add_action( 'bookingpress_before_save_assign_staffmember_data', array( $this, 'bookingpress_validate_staff_assign_to_service') );
    
                add_action( 'bookingpress_modify_staff_with_formatting', array( $this, 'bookingpress_update_location_details_for_staffmembers') );
    
                add_action( 'bookingpress_staff_assigned_services_column_name', array( $this, 'bookingpress_staff_assigned_services_location_column') );
                
                add_action( 'bookingpress_staff_assigned_services_column_value', array( $this, 'bookingpress_staff_assigned_services_location_column_value') );
    
                add_action( 'bookingpress_modify_staff_member_service_details', array( $this, 'bookingpress_modify_staff_member_service_details_for_locations'), 10, 2 );
    
                add_action( 'bookignpress_get_assigned_service_data_filter', array( $this, 'bookignpress_get_staff_assigned_service_details_with_location'), 10, 2 );
    
                add_action( 'bookingpress_before_validate_existing_staff_list', array( $this, 'bookingpress_remove_staff_list_without_locations') );
    
                add_action( 'bookingpress_staff_assign_service_outside_control', array( $this, 'bookingpress_staff_assign_service_location_input') );
    
                add_action( 'bookingpress_modify_assign_service_form_for_staffmember', array( $this, 'bookingpress_add_location_to_assigned_service_for_staffmember'));
    
                add_action( 'bookingpress_modify_assign_service_form_for_edit_staffmember', array( $this, 'bookingpress_add_location_to_assigned_service_for_edit_staffmember'));
    
                add_filter( 'bookingpress_modify_staff_assign_service_list', array( $this, 'bookingpress_modify_staff_assign_services_with_location') );
    
                add_filter( 'bookingpress_staff_members_save_external_details', array( $this, 'bookingpress_save_staffmember_services_with_locations' ) );
    
                add_action( 'bookingpress_admin_vue_data_variables_script', array( $this, 'bookingpress_shift_management_cls'), 11 );
    
                add_filter( 'bookingpress_myservices_modify_data_fields', array( $this, 'bookingpress_modify_myservices_data_fields_for_locations'), 10, 2 );
    
                add_action( 'bookingpress_myservices_staff_panel_external_content', array( $this, 'bookingpress_staff_panel_myservices_location_tab') );
            }

        }

        function bookingpress_modify_myservices_data_fields_for_locations( $bookingpress_myservices_data_fields_arr, $bookingpress_staffmember_id ){
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;

            $assigned_services = $bookingpress_myservices_data_fields_arr['assigned_services_details'];
            
            foreach( $assigned_services as $key => $service_data ){
                $service_id = $service_data['bookingpress_service_id'];
                $assigned_locations = $wpdb->get_results( $wpdb->prepare( "SELECT bplc.bookingpress_location_name FROM {$tbl_bookingpress_locations} bplc LEFT JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} bplcs ON bplc.bookingpress_location_id = bplcs.bookingpress_location_id WHERE bplcs.bookingpress_service_id = %d AND bplcs.bookingpress_staffmember_id = %d GROUP BY bookingpress_location_name", $service_id, $bookingpress_staffmember_id ) ); // phpcs:ignore
                if( !empty( $assigned_locations ) ){
                    $staff_locations = array();
                    foreach( $assigned_locations as $service_location_data ){
                        $staff_locations[] = $service_location_data->bookingpress_location_name;
                    }
                    $assigned_services[ $key ]['bookingpress_locations'] = $staff_locations;
                }
            }
            $bookingpress_myservices_data_fields_arr['assigned_services_details'] = $assigned_services;
            /* echo "<pre>";
            print_r( $bookingpress_myservices_data_fields_arr['assigned_services_details'] );
            echo "</pre>"; */
            return $bookingpress_myservices_data_fields_arr;
        }

        function bookingpress_staff_panel_myservices_location_tab(){
            /** staff panel location details on service page */
            ?>
            <div class="bpa-ms-item__location-row" v-if="'undefined' != typeof service_data.bookingpress_locations && 0 < service_data.bookingpress_locations.length ">
                <h5><?php esc_html_e('Location', 'bookingpress-location'); ?>:</h5>
                <div class="bpa-lr__item-wrap">
                    <div class="bpa-lr__item" v-for="(location_name, index) in service_data.bookingpress_locations">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p>{{location_name}}</p>
                    </div>
                    <?php /*<div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Telangana', 'bookingpress-location' ); ?></p>
                    </div>
                    <div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Maharashtra', 'bookingpress-location' ); ?></p>
                    </div>
                    <div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Tamil Nadu', 'bookingpress-location' ); ?></p>
                    </div>
                    <div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Telangana', 'bookingpress-location' ); ?></p>
                    </div>
                    <div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Maharashtra', 'bookingpress-location' ); ?></p>
                    </div>
                    <div class="bpa-lr__item">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4940_15772)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.99967 13.3335H11.9997C12.3663 13.3335 12.6663 13.6335 12.6663 14.0002C12.6663 14.3668 12.3663 14.6668 11.9997 14.6668H3.99967C3.63301 14.6668 3.33301 14.3668 3.33301 14.0002C3.33301 13.6335 3.63301 13.3335 3.99967 13.3335ZM7.99967 4.66683C7.26634 4.66683 6.66634 5.26683 6.66634 6.00016C6.66634 6.7335 7.26634 7.3335 7.99967 7.3335C8.3533 7.3335 8.69243 7.19302 8.94248 6.94297C9.19253 6.69292 9.33301 6.35379 9.33301 6.00016C9.33301 5.64654 9.19253 5.3074 8.94248 5.05735C8.69243 4.80731 8.3533 4.66683 7.99967 4.66683ZM7.99967 1.3335C10.1797 1.3335 12.6663 2.9735 12.6663 6.10016C12.6663 8.08683 11.2463 10.1802 8.40634 12.3602C8.16634 12.5468 7.83301 12.5468 7.59301 12.3602C4.75301 10.1735 3.33301 8.08683 3.33301 6.10016C3.33301 2.9735 5.81967 1.3335 7.99967 1.3335Z" />
                            </g>
                            <defs>
                            <clipPath id="clip0_4940_15772">
                            <rect width="16" height="16" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <p><?php esc_html_e( 'Tamil Nadu', 'bookingpress-location' ); ?></p>
                    </div> */ ?>
                </div>
            </div>
            <?php
        }

        function bookingpress_shift_management_cls(){
            ?>
                bookingpress_return_data['bpa_sm_shift_management_cls'] = 'bpa-form-row--is-location';
                bookingpress_return_data['bpa_service_shift_management_external_class'] += ' bpa-dialog__multi-location-enabled ';
                bookingpress_return_data['bpa_service_shift_management_full_width_title_cls'] += ' bpa-mle__default-card '
            <?php
        }

        function bookingpress_save_staffmember_services_with_locations( $response ){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations_service_staff_pricing_details, $bookingpress_services;

            $staffmember_id = !empty( $response['staffmember_id'] ) ? intval( $response['staffmember_id'] ) : 0;

            if( !empty( $staffmember_id ) ){
                $bookingpress_assigned_service_list = ! empty( $_REQUEST['service_details']['assigned_service_list'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['service_details']['assigned_service_list'] ) : array();// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_POST contains mixed array and will be sanitized using 'appointment_sanatize_field' function

                if( !empty( $bookingpress_assigned_service_list ) ){
                    foreach ( $bookingpress_assigned_service_list as $bookingpress_service_key => $bookingpress_service_val ) {

                        $assigned_service_id = intval( $bookingpress_service_val['assign_service_id'] );
                        $wpdb->delete(
                            $tbl_bookingpress_locations_service_staff_pricing_details,
                            array(
                                'bookingpress_service_id' => $assigned_service_id,
                                'bookingpress_staffmember_id' => $staffmember_id
                            )
                        );
                        $locations = !empty( $bookingpress_service_val['locations'] ) ? $bookingpress_service_val['locations'] : array();
                        if( !empty( $locations ) ){
                            foreach( $locations as $location_details ){
                                $location_id = $location_details['location_id'];
                                /* $location_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( bookingpress_service_staff_pricing_id ) FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_staffmember_id = %d", $location_id, $assigned_service_id, $staffmember_id ) );
                                if( 1 > $location_exists ){ */
                                    $bpa_service_capacity = $bookingpress_services->bookingpress_get_service_meta( $assigned_service_id, 'max_capacity' );
                                    $bpa_service_min_capacity = $bookingpress_services->bookingpress_get_service_meta( $assigned_service_id, 'min_capacity' );
                                    $bpa_location_staff_tbl_data = array(
                                        'bookingpress_location_id' => $location_id,
                                        'bookingpress_service_id' => $assigned_service_id,
                                        'bookingpress_staffmember_id' => $staffmember_id,
                                        'bookingpress_staff_location_qty' => intval( $bookingpress_service_val['assign_service_capacity'] ),
                                        'bookingpress_staff_location_min_qty' => intval( $bookingpress_service_val['assign_service_min_capacity'] ),
                                        'bookingpress_service_qty' => ( !empty( $bpa_service_capacity ) ? $bpa_service_capacity : 1 ), 
                                        'bookingpress_service_min_qty' => ( !empty( $bpa_service_min_capacity ) ? $bpa_service_min_capacity : 1 ), 
                                    );

                                    $wpdb->insert( $tbl_bookingpress_locations_service_staff_pricing_details, $bpa_location_staff_tbl_data );
                                /* } */
                            }
                        }
                    }
                }
            }

            return $response;
        }

        function bookingpress_modify_staff_assign_services_with_location( $bookingpress_assign_service_list ){
            global $wpdb, $tbl_bookingpress_locations;

            if( !empty( $bookingpress_assign_service_list ) ){
                foreach( $bookingpress_assign_service_list as $askey => $assigned_service_data ){
                    if( !empty( $assigned_service_data['assign_service_location'] ) ){
                        $service_location = $assigned_service_data['assign_service_location'];
                        if( is_array( $service_location ) ){
                            $total_locations = count( $service_location );
                            $bookingpress_assign_service_list[$askey]['location_counter'] = $total_locations;
                            $bookingpress_assign_service_list[$askey]['location_remaining_counter'] = ( ($total_locations - 2) > 0 ) ? ( $total_locations - 2 ) : 0;
                            $bpa_service_locations = array();
                            foreach( $service_location as $sloc ){
                                $bpa_service_locations[] = array( 
                                    'location_id' => $sloc,
                                    'location_name' => $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_location_name FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $sloc ) ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                                );
                            }
                            $bookingpress_assign_service_list[$askey]['locations'] = $bpa_service_locations;
                        } else {
                            $bookingpress_assign_service_list[$askey]['location_counter'] = 1;
                            $bookingpress_assign_service_list[$askey]['location_remaining_counter'] = 0;
                            $bookingpress_assign_service_list[$askey]['locations'] = array(
                                array(
                                    'location_id' => $service_location,
                                    'location_name' => $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_location_name FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $service_location ) ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                                )
                            );
                        }
                    }
                }
            }

            return $bookingpress_assign_service_list;
        }

        function bookingpress_add_location_to_assigned_service_for_edit_staffmember(){
            ?>
            if( vm.assign_service_form.selected_location.length == 0 ){
                vm.$notify({
                    title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                    message: '<?php esc_html_e( 'Please select the location.', 'bookingpress-location' ); ?>',
                    type: 'error',
                    customClass: 'error_notification',
                });
                close_assign_service_model = false;
                return false;
            }
            vm.assign_service_form.assigned_service_list[index].assign_service_location = vm.assign_service_form.selected_location;
            <?php
        }

        function bookingpress_add_location_to_assigned_service_for_staffmember(){

            global $bookingpress_notification_duration;
            ?>  
                
                if( vm.assign_service_form.selected_location.length == 0 ){
                    vm.$notify({
                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                        message: '<?php esc_html_e( 'Please select the location.', 'bookingpress-location' ); ?>',
                        type: 'error',
                        customClass: 'error_notification',
					});
                    return false;
                }
                bpa_assigned_service_data.assign_service_location = vm.assign_service_form.selected_location;
            <?php
        }

        function bookingpress_staff_assign_service_location_input(){
            global $BookingPress;
            $bookingpress_allow_staff_to_service_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
            ?>
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <el-form-item>
                    <template #label>
                        <span class="bpa-form-label"><?php esc_html_e( 'Location', 'bookingpress-location' ); ?></span>
                    </template>
                    <el-select class="bpa-form-control" v-model="assign_service_form.selected_location" filterable <?php echo ( "true" == $bookingpress_allow_staff_to_service_multiple_location ) ? 'multiple collapse-tags' : ''; ?> placeholder="<?php esc_html_e('Select Location', 'bookingpress-location'); ?>">
                        <el-option :label="location_details.bookingpress_location_name" :value="location_details.bookingpress_location_id" v-for="(location_details,key) in bookingpress_location_list"></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <?php
        }

        function bookingpress_remove_staff_list_without_locations(){
            ?>
            if( is_exist == 1 ){
                let new_assign_staff_member_list = [];
                vm.assign_staff_member_list.forEach(function(currentValue, index, arr){
                    if(currentValue.staffmember_id == vm.assign_staff_member_details.assigned_staffmember_id && vm.assign_staff_member_details.assigned_staffmember_is_edit == 0){
                        is_exist = 0;
                    } else {
                        new_assign_staff_member_list.push( currentValue );
                    }
                });

                vm.assign_staff_member_list = new_assign_staff_member_list;
            }

            if( "undefined" != typeof vm.save_location_data && "undefined" != typeof vm.assign_staff_member_list ){
                let location_ids = [];
                for( let l in vm.save_location_data ){
                    let current_loc_id = vm.save_location_data[ l ].selected_location;
                    location_ids.push( current_loc_id );
                }
                for( let x in vm.assign_staff_member_list ){
                    vm.assign_staff_member_list[x].available_locations = location_ids;
                }
            }

            <?php
        }

        function bookignpress_get_staff_assigned_service_details_with_location( $service_data, $staffmember_id ){
            global $wpdb, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_locations, $BookingPress;
            $service_id = $service_data['assign_service_id'];
            
            $service_with_location = $wpdb->get_results( $wpdb->prepare( "SELECT bplss.bookingpress_location_id, bplc.bookingpress_location_name FROM {$tbl_bookingpress_locations_service_staff_pricing_details} bplss LEFT JOIN {$tbl_bookingpress_locations} bplc ON bplc.bookingpress_location_id = bplss.bookingpress_location_id WHERE bplss.bookingpress_service_id = %d AND bplss.bookingpress_staffmember_id = %d  GROUP BY bplss.bookingpress_location_id", $service_id, $staffmember_id ) ); //phpcs:ignore

            $bookingpress_allow_staff_to_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');            

            $selected_location = '';
            $use_multiple = false;
            if( 'true' == $bookingpress_allow_staff_to_multiple_location ){
                $selected_location = array();
                $use_multiple = true;
            }

            $service_location_counter = 0;
            $service_location_details = array();
            if( !empty( $service_with_location ) ){
                foreach( $service_with_location as $key => $location ){
                    $location_id = $location->bookingpress_location_id;
                    if( empty($location_id) ){
                        continue;
                    }
                    $location_name = $location->bookingpress_location_name;
                    $service_location_details[] = array(
                        'location_id' => $location_id,
                        'location_name' => $location_name
                    );
                    $service_location_counter++;
                }
            }

            $service_data['locations'] = $service_location_details;
            $service_data['location_counter'] = $service_location_counter;
            $service_data['location_remaining_counter'] = ( 2 < $service_location_counter ) ? ( $service_location_counter  - 2) : 0;

            if( $use_multiple == true ){
                foreach( $service_location_details as $location_details ){
                    $selected_location[] = $location_details['location_id'];
                }
            } else {
                if( is_array( $service_location_details ) && !empty( $service_location_details ) ){
                    $selected_location = $service_location_details[0]['location_id'];
                }
            }

            $service_data['selected_location'] = $selected_location;

            /* if( $service_location_counter == 0 ){
                $service_data['assign_service_display'] = false;
            } */

            return $service_data;
        }

        function bookingpress_modify_staff_member_service_details_for_locations( $bookingpress_staff_member_services_details, $staffmember_id ){

            if( !empty( $bookingpress_staff_member_services_details ) ){
                global $wpdb, $tbl_bookingpress_locations_service_staff_pricing_details;
                foreach( $bookingpress_staff_member_services_details as $staff_service_key => $staff_service_details ){
                    $assigned_service_id = $staff_service_details['bookingpress_service_id'];
                    
                    $is_service_staff_location = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( bookingpress_service_staff_pricing_id ) as total_assigned_services FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d", $assigned_service_id, $staffmember_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                    
                    if( 1 > $is_service_staff_location ){
                        unset( $bookingpress_staff_member_services_details[ $staff_service_key ] );
                    }
                }
            }

            return array_values( $bookingpress_staff_member_services_details );
        }

        function bookingpress_staff_assigned_services_location_column_value(){
            ?>
                <el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
                    <div class="bpa-card__item">
                        <h4 class="bpa-card__item__heading is--body-heading" v-for="(location_data,index) in assigned_service_details.locations" v-if="index < 2">
                            {{location_data.location_name}}
                            <el-popover placement="top-start" title="<?php esc_html_e('Locations', 'bookingpress-location'); ?>" width="280" trigger="hover" popper-class="bpa-card-item-extra-popover" v-if="index == 1 && 2 < assigned_service_details.location_counter && 0 < assigned_service_details.location_remaining_counter" style="display:inline-block;width: auto" >
                                <div class="bpa-card-item-extra-content">
                                    <div class="bpa-cec__item" v-for="(ilocation_data,inner_index) in assigned_service_details.locations" v-if="inner_index > 1">{{ ilocation_data.location_name }}</div>
                                </div>
                                <div slot="reference" class="bpa-card__item-extra-tooltip">
                                    <el-link class="bpa-iet__label">{{assigned_service_details.location_remaining_counter}}</el-link>
                                </div>
                            </el-popover>
                        </h4>
                    </div>
                </el-col>
            <?php
        }

        function bookingpress_staff_assigned_services_location_column(){
            ?>
            <el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
                <div class="bpa-card__item">
                    <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Locations', 'bookingpress-location' ); ?></h4>
                </div>
            </el-col>
            <?php
        }

        function bookingpress_update_location_details_for_staffmembers( $bookingpress_assign_staff_list ){

            global $tbl_bookingpress_locations, $wpdb;
            if( !empty( $bookingpress_assign_staff_list ) ){
                foreach( $bookingpress_assign_staff_list as $staff_list_key => $staff_list_data ){
                    $staff_locations = $staff_list_data['staffmember_location'];
                    
                    if( !empty( $staff_locations ) ){

                        $staff_location_data = array();
                        $staff_location_counter = is_array( $staff_locations ) ? count( $staff_locations ) : 1;
                        $bookingpress_assign_staff_list[ $staff_list_key ]['location_counter'] = $staff_location_counter;
                        $bookingpress_assign_staff_list[ $staff_list_key ]['location_remaining_counter'] = ($staff_location_counter > 2 ) ? $staff_location_counter - 2 : 0;
                        
                        if( is_array( $staff_locations ) ){
                            foreach( $staff_locations as $location_id ){
                                $staff_location_data[] = array(
                                    'location_id' => $location_id,
                                    'location_name' => $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_location_name FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $location_id ) ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                                );
                            }
                            $bookingpress_assign_staff_list[ $staff_list_key ]['locations'] = $staff_location_data;
                            $bookingpress_assign_staff_list[ $staff_list_key ]['staff_display_row'] = 'true';
                        } else {
                            $bookingpress_assign_staff_list[ $staff_list_key ]['locations'] = array(
                                array(
                                    'location_id' => $staff_locations,
                                    'location_name' => $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_location_name FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $staff_locations ) )  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                                )
                            );
                            $bookingpress_assign_staff_list[ $staff_list_key ]['staff_display_row'] = 'true';
                        }
                    }
                }
            }

            return $bookingpress_assign_staff_list;
        }

        /**
         * function to validate staff member with location while assigning to service
         *
         * @return void
         */
        function bookingpress_validate_staff_assign_to_service(){
            global $bookingpress_notification_duration;
            ?>
            if( "undefined" != typeof vm.assign_staff_member_details.assigned_location_id ){
                if( "string" == typeof vm.assign_staff_member_details.assigned_location_id && ("" == vm.assign_staff_member_details.assigned_location_id || 0 == vm.assign_staff_member_details.assigned_location_id) ){
                    vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
						message: '<?php esc_html_e( 'Please select location', 'bookingpress-location' ); ?>',
						type: 'error',
						customClass: 'error_notification',
						duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
					});
                    return false;
                } else if( "object" == typeof vm.assign_staff_member_details.assigned_location_id && 0 == vm.assign_staff_member_details.assigned_location_id.length ){
                    vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
						message: '<?php esc_html_e( 'Please select at least one location', 'bookingpress-location' ); ?>',
						type: 'error',
						customClass: 'error_notification',
						duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
					});
                    return false;
                }
            }
            <?php
        }
        
        /**
         * function to set location field value while editing staff member in add/edit service model
         *
         * @return void
         */
        function bookingpress_service_set_locations_for_assigned_staffmember(){
            ?>
                console.log( vm.staff_to_multiple_locations );
                console.log( vm.staff_to_multiple_locations == 'true' );
                console.log( vm.staff_to_multiple_locations == true );
                if( vm.staff_to_multiple_locations == 'true' ){
                    console.log( "INSIDE" );
                    if( "undefined" != typeof currentValue.locations && "undefined" != typeof currentValue.location_counter && currentValue.location_counter > 0 ){
                        let location_ids = [];
                        for( let location_data of currentValue.locations ){
                            location_ids.push( location_data.location_id );
                        }
                        vm.assign_staff_member_details.assigned_location_id = location_ids;
                    }
                } else {
                    console.log( "INSIDE 2" );
                    if( "undefiend" != typeof currentValue.locations ){
                        vm.assign_staff_member_details.assigned_location_id = currentValue.locations[0].location_id;
                    }
                }
            <?php
        }
        
        /**
         * function to reset location data while add/edit service model close
         *
         * @return void
         */
        function bookingpress_reset_service_model_location_data(){
            ?>
                vm2.save_location_data = [];
            <?php
        }
        
        /**
         * filter service data and add location details while editing appointments
         *
         * @param  mixed $response
         * @param  mixed $service_id
         * @return void
         */
        function bookingpress_add_location_data_for_services( $response, $service_id ){
            
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;

            $service_location_data = $wpdb->get_results( $wpdb->prepare( "SELECT bpl.bookingpress_location_id,bpl.bookingpress_location_name,bpls.bookingpress_service_qty,bpls.bookingpress_service_min_qty FROM {$tbl_bookingpress_locations} bpl LEFT JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} bpls ON bpl.bookingpress_location_id=bpls.bookingpress_location_id WHERE bpls.bookingpress_service_id = %d GROUP BY bpl.bookingpress_location_id", $service_id ) ); //phpcs:ignore

            $response['locations'] = array();
            if( !empty( $service_location_data ) ){
                foreach( $service_location_data as $location_data ){
                    $response['locations'][ $location_data->bookingpress_location_id ] = array(
                        'location_name' => $location_data->bookingpress_location_name,
                        'location_capacity' => $location_data->bookingpress_service_qty,
                        'location_min_capacity' => $location_data->bookingpress_service_min_qty
                    );
                }
            }

            return $response;
        }
        
        /**
         * bookingpress_add_location_data_for_service_xhr_response
         *
         * @return void
         */
        function bookingpress_add_location_data_for_service_xhr_response(){
            ?>
                vm2.save_location_data = [];
                let location_service_data = response.data.locations;
                for( let index in location_service_data ){
                    let element = location_service_data[index];
                    vm2.save_location_data.push({
                        location_name: element.location_name,
                        selected_location: index,
                        location_max_capacity: element.location_capacity,
                        location_min_capacity: element.location_min_capacity
                    });
                }
            <?php
        }

        function bookingpress_service_staff_section_location_column(){
            ?>
                <el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
                    <div class="bpa-card__item">
                        <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Location', 'bookingpress-location' ); ?></h4>
                    </div>
                </el-col>
            <?php
        }

        function bookingpress_service_staff_section_location_column_value(){
            ?>
            <el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
                <div class="bpa-card__item">
                    <h4 class="bpa-card__item__heading is--body-heading" v-for="(location_data,index) in assign_staffmember_data.locations" v-if="index < 2">
                        {{location_data.location_name}}
                        <el-popover placement="top-start" title="<?php esc_html_e('Locations', 'bookingpress-location'); ?>" width="280" trigger="hover" popper-class="bpa-card-item-extra-popover" v-if="index == 1 && 2 < assign_staffmember_data.location_counter && 0 < assign_staffmember_data.location_remaining_counter" style="display:inline-block;width: auto" >
                            <div class="bpa-card-item-extra-content">
                                <div class="bpa-cec__item" v-for="(ilocation_data,inner_index) in assign_staffmember_data.locations" v-if="inner_index > 1">{{ ilocation_data.location_name }}</div>
                            </div>
                            <div slot="reference" class="bpa-card__item-extra-tooltip">
                                <el-link class="bpa-iet__label">{{assign_staffmember_data.location_remaining_counter}}</el-link>
                            </div>
                        </el-popover>
                    </h4>
                </div>
            </el-col>
            <?php
        }

        function bookingpress_assign_default_location_for_staff_model(){
            ?>
            vm.assign_staff_member_details.assigned_location_id = "0";
            <?php
        }

        function bookingpress_add_dynamic_content_for_add_staff_func(){
            global $BookingPress;
            $bookingpress_allow_staff_to_service_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
            ?>
                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                    <el-form-item>
                        <template #label>
                            <span class="bpa-form-label"><?php esc_html_e( 'Select Location', 'bookingpress-location' ); ?></span>
                        </template>
                        <?php if($bookingpress_allow_staff_to_service_multiple_location == 'false'){ ?>
                            <el-select class="bpa-form-control" v-model="assign_staff_member_details.assigned_location_id" filterable>
                                <el-option label="<?php esc_html_e( 'Select Location', 'bookingpress-location' ); ?>" value="0"></el-option>
                        <?php } else{ ?>
                            <el-select class="bpa-form-control" v-model="assign_staff_member_details.assigned_location_id" filterable multiple collapse-tags placeholder="<?php esc_html_e('Select Location', 'bookingpress-location'); ?>">
                        <?php } ?>
                            <el-option :label="location_details.location_name" :value="location_details.selected_location" v-for="(location_details, key) in save_location_data"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            <?php
        }

        function bookingpress_add_posted_data_for_save_service_func(){
            ?>
                if( vm2.save_location_data.length == 0 ){
                    
                    vm.$notify({
                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                        message: '<?php esc_html_e( 'Please select the location.', 'bookingpress-location' ); ?>',
                        type: 'error',
                        customClass: 'error_notification',
					});
                    return false;
                }
                postdata.location_details = vm2.save_location_data;
            <?php
        }

        function bookingpress_save_service_details($response, $service_id, $posted_data){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;

            $wpdb->delete( $tbl_bookingpress_locations_service_staff_pricing_details, array( 'bookingpress_service_id' => $service_id ) );
            
            if( !empty( $service_id ) && !empty( $posted_data['location_details'] ) ){
                $prepare_location_service_staff_data = array();
                $location_details = $posted_data['location_details'];
                $staff_member_details = isset($posted_data['bookingpress_assign_staffmember_data']) ? $posted_data['bookingpress_assign_staffmember_data'] : array();

                foreach( $location_details as $key => $location_data ){
                    $location_id = $location_data['selected_location'];
                    $service_qty = $location_data['location_max_capacity'];
                    $service_min_qty = $location_data['location_min_capacity'];
                    $service_staff_data = array(
                        'bookingpress_service_id' => $service_id,
                        'bookingpress_location_id' => $location_id,
                        'bookingpress_service_qty' => $service_qty,
                        'bookingpress_service_min_qty' => $service_min_qty,
                    );
                    if( !empty( $staff_member_details ) ){
                        foreach( $staff_member_details as $staff_key => $staff_data ){
                            $staff_locations = $staff_data['locations'];
                            foreach( $staff_locations as $sf_location ){
                                if( $sf_location['location_id'] == $location_id ){
                                    $service_staff_data['bookingpress_staffmember_id'] = $staff_data['staffmember_id'];
                                    $service_staff_data['bookingpress_staff_location_qty'] = $staff_data['staffmember_max_capacity'];
                                    $service_staff_data['bookingpress_staff_location_min_qty'] = $staff_data['staffmember_min_capacity'];
				    $prepare_location_service_staff_data[] = $service_staff_data;
                                }
                            }
                        }
                    } else {
                        $service_staff_data['bookingpress_staffmember_id'] = 0;
                        $service_staff_data['bookingpress_staff_location_qty'] = 1;
                        $service_staff_data['bookingpress_staff_location_min_qty'] = 1;
                        $prepare_location_service_staff_data[] = $service_staff_data;
                    }
                    
                }

                if( !empty( $prepare_location_service_staff_data ) ){
                    foreach( $prepare_location_service_staff_data as $bpa_lsf_data ){
                        $wpdb->insert(
                            $tbl_bookingpress_locations_service_staff_pricing_details,
                            $bpa_lsf_data
                        );
                    }
                }
            }

            return $response;
        }

        function bookingpress_add_service_dynamic_vue_methods_func(){
            ?>
                is_location_price_validate(evt) {
                    if(evt != '') {
                        const regex = /^(?!.*(,,|,\.|\.,|\.\.))[\d.,]+$/gm;
                        let m;
                        if((m = regex.exec(evt)) == null ) {
                            this.location_form_data.location_price = '';
                        }
                    }
                },
                saveLocationDetails(){
                    const vm = this;
                    var location_form = 'location_form'
                    vm.$refs[location_form].validate((valid) => {
                        if (valid) {
                            var is_error_generate = 0;
                            var bookingpress_selected_location_id = vm.location_form_data.selected_location;

                            if( vm.location_form_data.is_location_edit == false ){

                                if(vm.save_location_data.length > 0){
                                    vm.save_location_data.forEach(function(currentValue, index, arr){
                                        if(currentValue.selected_location == bookingpress_selected_location_id){
                                            vm.$notify({
                                                title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                                message: '<?php esc_html_e( 'Location already added', 'bookingpress-location' ); ?>',
                                                type: 'error',
                                                customClass: 'error_notification',
                                            });
                                            is_error_generate = 1;
                                        }
                                    });
                                }

                                if( vm.location_form_data.location_min_capacity == undefined || vm.location_form_data.location_max_capacity == undefined ){
                                    vm.$notify({
                                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                        message: '<?php esc_html_e( 'Please select min and max capacity.', 'bookingpress-location' ); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                        
                                    });
                                    is_error_generate = 1;
                                    return false;
                                }

                                if( vm.is_bring_anyone_with_you_enable == 1  && ( vm.location_form_data.location_min_capacity > vm.location_form_data.location_max_capacity) ){

                                    vm.$notify({
                                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                        message: '<?php esc_html_e( 'Location min capacity should not be greater than max capacity.', 'bookingpress-location' ); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                    });
                                    is_error_generate = 1;
                                    return false;
                                }

                                if(is_error_generate == 0){
                                    let service_location_save_data = {};
                                    Object.assign(service_location_save_data, {selected_location: bookingpress_selected_location_id});
                                    Object.assign(service_location_save_data, {location_max_capacity: vm.location_form_data.location_max_capacity});
                                    Object.assign(service_location_save_data, {location_min_capacity: vm.location_form_data.location_min_capacity});
                                    Object.assign(service_location_save_data, {location_price: vm.location_form_data.location_price});
                                    
                                    //Get location name
                                    vm.bookingpress_locations.forEach(function(currentValue, index, arr){
                                        if(currentValue.bookingpress_location_id == bookingpress_selected_location_id){
                                            Object.assign(service_location_save_data, {location_name: currentValue.bookingpress_location_name});
                                        }
                                    });
                                    
                                    vm.save_location_data.push(service_location_save_data);
                                    
                                }
                            } else {
                                vm.save_location_data.forEach( (element,index) => {
                                    if( index != vm.location_form_data.location_edit_index && bookingpress_selected_location_id == element.selected_location ){
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Location already added', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                        });
                                        is_error_generate = 1;
                                        return false;
                                    } else {
                                        
                                    }
                                });
                                if( 0 < is_error_generate ){
                                    return false;
                                } else {

                                    if( vm.location_form_data.location_min_capacity == undefined || vm.location_form_data.location_max_capacity == undefined ){
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Please select min and max capacity.', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            
                                        });
                                        return false;
                                    }

                                    if( vm.is_bring_anyone_with_you_enable == 1  && ( vm.location_form_data.location_min_capacity > vm.location_form_data.location_max_capacity) ){

                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Location min capacity should not be greater than max capacity.', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                        });
                                        return false;
                                    }
                                    vm.save_location_data[ vm.location_form_data.location_edit_index ].selected_location = bookingpress_selected_location_id;
                                    vm.save_location_data[ vm.location_form_data.location_edit_index ].location_max_capacity = vm.location_form_data.location_max_capacity;
                                    vm.save_location_data[ vm.location_form_data.location_edit_index ].location_min_capacity = vm.location_form_data.location_min_capacity;
                                    vm.bookingpress_locations.forEach( (elm, ind) => {
                                        if( elm.bookingpress_location_id == bookingpress_selected_location_id ){
                                            vm.save_location_data[ vm.location_form_data.location_edit_index ].location_name = elm.bookingpress_location_name;
                                        }
                                    });
                                }
                            }

                            vm.close_location_modal();
                        }
                    });
                },
                close_location_modal(){
                    const vm = this;
                    vm.bookingpress_open_add_location_modal = false;
                    vm.location_form_data.selected_location = '';
                    vm.location_form_data.location_max_capacity = 1;
                    vm.location_form_data.location_min_capacity = 1;
                    vm.location_form_data.location_price = '';
                    vm.location_form_data.is_location_edit = false;
                    vm.location_form_data.location_edit_index = '';
                },
                open_location_modal(currentElement){
                    const vm = this
                    vm.close_location_modal()
                    vm.bookingpress_update_index = ''
                    var dialog_pos = currentElement.target.getBoundingClientRect();
                    vm.extra_service_modal_pos = (dialog_pos.top - 90)+'px'
				    vm.extra_service_modal_pos_right = '-'+(dialog_pos.right - 430)+'px';
                    vm.bookingpress_open_add_location_modal = true

                    if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
                        vm.bpa_adjust_popup_position( currentElement, 'div#location_modal .el-dialog.bpa-dialog--add-location');
                    }
                },
                bookingpress_delete_location(delete_index){
                    const vm = this
                    vm.save_location_data.splice(delete_index, 1);
                },
                bookingpress_edit_location(edit_index){
                    const vm = this
                    var bookingpress_edit_data = vm.save_location_data[edit_index];
                    if(bookingpress_edit_data != null){
                        vm.location_form_data.selected_location = bookingpress_edit_data.selected_location;
                        vm.location_form_data.location_max_capacity = bookingpress_edit_data.location_max_capacity;
                        vm.location_form_data.location_min_capacity = bookingpress_edit_data.location_min_capacity;
                        vm.location_form_data.location_price = bookingpress_edit_data.location_price;
                        vm.location_form_data.is_location_edit = true;
                        vm.location_form_data.location_edit_index = edit_index;
                    }
                    vm.bookingpress_open_add_location_modal = true
                },
                bookingpress_edit_service_location_details(){
                    /*const vm = this;
                    vm.save_location_data = {};
                    
                    let bookingpress_service_id = vm.service.service_update_id;
                    
                    var postData = { action: "bookingpress_retrieve_location_for_services", service_id: bookingpress_service_id };
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
                    .then( function( response ){
                        
                    }.bind(this) )
                    .catch( function (error) {
                    });*/

                    /*vm2.save_location_data = [];
                    var bookingpress_service_id = vm2.service.service_update_id;
                    if(vm2.bookingpress_location_assigned_service_staff_data.length > 0){
                        vm2.bookingpress_location_assigned_service_staff_data.forEach(function(currentValue, index, arr){
                            if(bookingpress_service_id == currentValue.bookingpress_service_id){
                                var bookingpress_location_id = currentValue.bookingpress_location_id;
                                var bookingpress_location_name = '';
                                vm2.bookingpress_locations.forEach(function(currentValue2, index2, arr2){
                                    if(currentValue2.bookingpress_location_id == bookingpress_location_id){
                                        bookingpress_location_name = currentValue2.bookingpress_location_name;
                                    }
                                });

                                vm2.save_location_data.push({
                                    location_max_capacity: currentValue.bookingpress_service_qty,
                                    location_min_capacity: currentValue.bookingpress_service_min_qty,
                                    location_price: currentValue.bookingpress_service_price,
                                    selected_location: currentValue.bookingpress_location_id,
                                    location_name: bookingpress_location_name,
                                });
                            }
                        });
                    }*/
                },
                bpa_retrieve_assigned_staff( selected_location){
                    const vm = this;
                    let assigned_staff_member_list = vm.assign_staff_member_list;
                    assigned_staff_member_list.forEach( (element, index) =>{
                        if( "undefined" != typeof element.location && "undefined" != typeof element.location[ selected_location ] ){
                            element.staff_display_row = true;
                        } else {
                            element.staff_display_row = false;
                        }
                    });
                },
            <?php
        }

        function bookingpress_modify_staff_data_fields_func( $bookingpress_staff_member_vue_data_fields ){
            global $wpdb, $tbl_bookingpress_locations, $BookingPress;
            
            $bpa_all_locations = $wpdb->get_results( "SELECT bookingpress_location_id, bookingpress_location_name FROM {$tbl_bookingpress_locations}", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bookingpress_staff_member_vue_data_fields['bookingpress_location_list'] = $bpa_all_locations;
            $bookingpress_staff_member_vue_data_fields['assign_service_form']['selected_location'] = array();

            $bookingpress_allow_staff_to_service_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');

            if( 'true' == $bookingpress_allow_staff_to_service_multiple_location ){
                $bookingpress_staff_member_vue_data_fields['bpa_staff_sm_shift_management_cls'] = 'bpa-form-row--is-location';
            }

            $bookingpress_staff_member_vue_data_fields['staff_to_multiple_locations'] = $bookingpress_allow_staff_to_service_multiple_location;

            return $bookingpress_staff_member_vue_data_fields;
        }

        function bookingpress_modify_service_data_fields_func($bookingpress_services_vue_data_fields){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details, $bookingpress_bring_anyone_with_you;

            $bookingpress_is_location_sepcific_cap_price = 'false';//$BookingPress->bookingpress_get_settings('location_specific_capacity_price', 'general_setting');
            $bookingpress_services_vue_data_fields['bookingpress_is_location_specific_capacity_price_configured'] = $bookingpress_is_location_sepcific_cap_price;
            $bookingpress_services_vue_data_fields['is_bring_anyone_with_you_enable'] = $bookingpress_bring_anyone_with_you->bookingpress_check_bring_anyone_module_activation();

            $bookingpress_services_vue_data_fields['bookingpress_open_add_location_modal'] = false;
            $bookingpress_services_vue_data_fields['location_form_data'] = array(
                'selected_location' => '',
                'location_max_capacity' => 1,
                'location_min_capacity' => 1,
                'location_price' => '',
                'is_location_edit' => false,
                'location_edit_index' => ''
            );
            $bookingpress_services_vue_data_fields['location_form_rules'] = array(
                'selected_location' => array(
					array(
						'required' => true,
						'message'  => esc_html__( 'Please select location', 'bookingpress-location' ),
						'trigger'  => 'blur',
					),
				),
            );

            if($bookingpress_is_location_sepcific_cap_price == 'true'){
                $bookingpress_services_vue_data_fields['location_form_rules']['location_max_capacity'] = array(
                    array(
                        'required' => true,
                        'message'  => esc_html__( 'Please enter maximum capacity', 'bookingpress-location' ),
                        'trigger'  => 'blur',
                    ),
                );

                $bookingpress_services_vue_data_fields['location_form_rules']['location_min_capacity'] = array(
                    array(
                        'required' => true,
                        'message'  => esc_html__( 'Please enter minimum capacity', 'bookingpress-location' ),
                        'trigger'  => 'blur',
                    ),
                );

                $bookingpress_services_vue_data_fields['location_form_rules']['location_price'] = array(
                    array(
                        'required' => true,
                        'message'  => esc_html__( 'Please enter location price', 'bookingpress-location' ),
                        'trigger'  => 'blur',
                    ),
                );
            }

            $bookingpress_services_vue_data_fields['save_location_data'] = array();

            $bookingpress_services_vue_data_fields['staffmember_selected_location'] = '';
            if(!empty($bookingpress_services_vue_data_fields['bookingpress_locations'])){
                $bookingpress_services_vue_data_fields['staffmember_selected_location'] = $bookingpress_services_vue_data_fields['bookingpress_locations'][0]['bookingpress_location_id'];
            }

            $bookingpress_location_assigned_service_staff_details = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
            $bookingpress_services_vue_data_fields['bookingpress_location_assigned_service_staff_data'] = $bookingpress_location_assigned_service_staff_details;

            $bookingpress_allow_staff_to_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
            $bookingpress_services_vue_data_fields['staff_to_multiple_locations'] = $bookingpress_allow_staff_to_multiple_location;

            return $bookingpress_services_vue_data_fields;
        }

        function bookingpress_add_content_after_basic_details_func(){
            require BOOKINGPRESS_LOCATION_VIEWS_DIR.'/manage_service_content.php';
        }

        function bookingpress_service_dialog_outside_func(){
            require BOOKINGPRESS_LOCATION_VIEWS_DIR.'/manage_service_content_outside_dialog.php';
        }

        function bookingpress_modify_get_staffmember_assigend_service_request_func(){
            ?>
                postdata.location_id = vm2.staffmember_selected_location;
                /*postdata.action = 'bookingpress_get_location_staffmember_service_data';*/
            <?php
        }

        function bookingpress_assign_location_to_staffmember( $bookingpress_assigned_staffmembers,$bookingpress_staffmember_val ){

            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_staffmembers;

           /*  echo "<pre>";
            print_r( $bookingpress_staffmember_val );
            print_r( $bookingpress_assigned_staffmembers );
            echo "</pre>"; */

            $assigned_staff_to_locations = array();
            $bookingpress_service_id = $bookingpress_staffmember_val['bookingpress_service_id'];
            $assigned_staffmember_id = $bookingpress_staffmember_val['bookingpress_staffmember_id'];

            $assigned_location_details = $wpdb->get_results( $wpdb->prepare( "SELECT bpl.bookingpress_location_name,bpl.bookingpress_location_id FROM {$tbl_bookingpress_locations} bpl LEFT JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} bplss ON bpl.bookingpress_location_id=bplss.bookingpress_location_id WHERE bplss.bookingpress_service_id = %d AND bplss.bookingpress_staffmember_id = %d", $bookingpress_service_id, $assigned_staffmember_id ) ); //phpcs:ignore
            if( !empty( $assigned_location_details ) ){
                
                $staff_assigned_locations = array();
                $assigned_location_counter = 0;
                foreach( $assigned_location_details as $assigned_location_data ){
                    $location_id = $assigned_location_data->bookingpress_location_id;
                    $location_name = $assigned_location_data->bookingpress_location_name;

                    $staff_assigned_locations[] = array(
                        'location_id' => $location_id,
                        'location_name' => $location_name
                    );
                    $assigned_location_counter++;
                }
                $bookingpress_assigned_staffmembers['locations'] = $staff_assigned_locations;
                $bookingpress_assigned_staffmembers['location_counter'] = $assigned_location_counter;
                $bookingpress_assigned_staffmembers['location_remaining_counter'] = $assigned_location_counter - 2;
            } else {
                $bookingpress_assigned_staffmembers['staff_display_row'] = false;
                $bookingpress_assigned_staffmembers['locations'] = array();
                $bookingpress_assigned_staffmembers['location_counter'] = 0;
                $bookingpress_assigned_staffmembers['location_remaining_counter'] = 0;
            }

            /* foreach( $bookingpress_assigned_staffmembers as $assigned_staff_details ){
                $staff_id = $assigned_staff_details['staffmember_id'];

            } */

            /* $bookingpress_service_id = ! empty( $_POST['service_id'] ) ? intval( $_POST['service_id'] ) : 0;
            $staffmember_id = $bookingpress_assigned_staffmembers['staffmember_id'];
            $location_id = !empty( $_POST['location_id'] ) ? intval( $_POST['location_id'] ) : 0;
            if( !empty( $bookingpress_service_id ) && !empty( $staffmember_id ) ){

                $get_location_info = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $tbl_bookingpress_locations_service_staff_pricing_details . " bplcss LEFT JOIN " . $tbl_bookingpress_locations . " bplct ON bplcss.bookingpress_location_id=bplct.bookingpress_location_id WHERE bplcss.bookingpress_service_id = %d AND bplcss.bookingpress_staffmember_id = %d", $bookingpress_service_id, $staffmember_id ), ARRAY_A );
                $staff_locations = array();
                if( !empty( $get_location_info ) ){
                    foreach( $get_location_info as $location_data ){
                        $bookingpress_assigned_staffmembers['staffmember_price'] = $location_data['bookingpress_staffmember_price'];
                        if( $location_id == $location_data['bookingpress_location_id'] ){
                            $bookingpress_assigned_staffmembers['staff_display_row'] = true;
                        } else {
                            $bookingpress_assigned_staffmembers['staff_display_row'] = false;
                        }
                        $staff_locations[ $location_data['bookingpress_location_id'] ] = $location_data;
                    }
                }
                $bookingpress_assigned_staffmembers['location'] = $staff_locations;

            } */

            return $bookingpress_assigned_staffmembers;
        }

    }

    global $bookingpress_location_services;
	$bookingpress_location_services = new bookingpress_location_services();
}