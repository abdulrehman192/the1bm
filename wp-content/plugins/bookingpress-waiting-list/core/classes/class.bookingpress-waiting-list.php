<?php
if (!class_exists('bookingpress_waiting_list') && class_exists('BookingPress_Core')) {

    class bookingpress_waiting_list Extends BookingPress_Core {

        function __construct() {  

            global $default_waiting_list_max_slot;
            $default_waiting_list_max_slot = 0;

            register_activation_hook(BOOKINGPRESS_WAITING_LIST_DIR.'/bookingpress-waiting-list.php', array('bookingpress_waiting_list', 'install'));
            register_uninstall_hook(BOOKINGPRESS_WAITING_LIST_DIR.'/bookingpress-waiting-list.php', array('bookingpress_waiting_list', 'uninstall'));             

            add_action('admin_notices', array( $this, 'bookingpress_waiting_list_admin_notices'));
            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {                                
                                
                add_action('admin_enqueue_scripts', array( $this, 'set_css'),11);
                add_action('wp_head', array( $this, 'set_front_css' ),11 );

                add_filter( 'bookingpress_add_global_option_data', array( $this, 'bookingpress_add_global_option_data_func' ), 11 );
                add_action('bookingpress_add_default_notification_section',array($this,'bookingpress_add_default_notification_section_func'),11);
                add_filter( 'add_bookingpress_default_notification_status', array( $this, 'add_bookingpress_default_notification_status_func' ), 11, 2 );
                add_action('bookingpress_add_timeslot_detail_after',array($this,'bookingpress_add_waiting_list_counter_func'),11);

                add_filter( 'bookingpress_frontend_apointment_form_add_dynamic_data', array( $this, 'bookingpress_frontend_add_appointment_data_variables' ), 12, 1 );                               
                
                add_filter('bookingpress_modify_single_time_slot_data',array($this,'bookingpress_modify_waiting_slot_data_func'),11,3);                                                
                

                add_filter('bookingpress_disable_timeslot_select_data',array($this,'bookingpress_disable_timeslot_select_data_fun'),11,1);
                add_filter('bookingpress_dynamic_time_select_after',array($this,'bookingpress_dynamic_time_select_after'),15,1);

                add_filter( 'bookingpress_modify_check_payment_method',array($this,'bookingpress_modify_check_payment_method_fun'), 10, 2);
                add_filter( 'bookingpress_modify_check_duplidate_appointment_time_slot',array($this,'bookingpress_modify_check_duplidate_appointment_time_slot_fun'), 10, 4);
                
                add_filter( 'bookingpress_no_payment_getway_submit_form_data',array($this,'bookingpress_no_payment_getway_submit_form_data_fun'), 10, 2);
                add_filter( 'bookingpress_check_for_modified_empty_payment_getway',array($this,'bookingpress_check_for_modified_empty_payment_getway_fun'), 10, 2);

                //add_filter( 'bookingpress_modify_entry_data_before_insert',array($this,'bookingpress_modify_entry_data_before_insert_fun'), 10, 2);
                add_filter('bookingpress_modify_entry_data_before_insert',array($this,'bookingpress_modify_entry_data_before_insert_func'),11,2);

                //Add Filter For Modified Notifications...
                add_filter('bookingpress_modify_send_email_notification_type',array($this,'bookingpress_modify_send_email_notification_type_func'),11,2);

                //Add Service Extra fields
                add_action('bookingpress_add_service_deposit_field_inside',array($this,'bookingpress_add_service_deposit_field_inside_fun'));
                add_filter('bookingpress_modify_edit_service_data', array($this, 'bookingpress_modify_edit_service_data_func'), 10, 2);	
                add_filter( 'bookingpress_modify_service_data_fields', array( $this, 'bookingpress_modify_service_data_fields_func' ),15);                 
                add_action( 'bookingpress_edit_service_more_vue_data', array( $this, 'bookingpress_edit_service_more_vue_data_func' ), 10 );
                add_filter( 'bookingpress_after_add_update_service', array( $this, 'bookingpress_save_service_details' ), 10, 3 ); 
                
                //Cart Entry Data modified start here...
                add_filter( 'bookingpress_modify_cart_entry_data_before_insert',array($this,'bookingpress_modify_cart_entry_data_before_insert_fun'),10,3);

                //Modify Appointment Booking Fields Before Insert
                add_filter( 'bookingpress_modify_appointment_booking_fields_before_insert', array( $this, 'bookingpress_modify_appointment_booking_fields_before_insert_fun' ), 10, 2); 

                //BookingPress Complete Payment Daynamic Data
                add_filter('modify_complate_payment_data_after_entry_create', array($this, 'modify_complate_payment_data_after_entry_create_func'), 15, 2);

                //Call Function After Cancel Appointment                
                add_action( 'bookingpress_after_cancel_appointment_without_check_payment', array( $this, 'bookingpress_after_cancel_appointment_fun'), 15 ); 
                add_action( 'bookingpress_after_cancel_appointment', array( $this, 'bookingpress_after_cancel_appointment_fun'), 15 );
                add_action( 'bookingpress_after_change_appointment_status', array( $this, 'bookingpress_after_cancel_appointment_fun'), 10, 2 );

                //Customization Added...
                add_action('bookingpress_add_bookingform_label_data',array($this,'bookingpress_add_bookingform_label_data_func'));
                add_filter('bookingpress_customize_add_dynamic_data_fields',array($this,'bookingpress_customize_add_dynamic_data_fields_func'),10);
                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this, 'bookingpress_get_booking_form_customize_data_filter_func'),10,1);
                //add_filter('bookingpress_customize_add_dynamic_data_fields',array($this,'bookingpress_customize_add_dynamic_data_fields_func'),10);

                add_filter('bookingpress_before_save_customize_booking_form',array($this, 'bookingpress_before_save_customize_booking_form_func'));
                add_action('bookingpress_before_save_customize_form_settings',array($this,'bookingpress_before_save_customize_form_settings_func')); 

                //Waiting Payment Shortcode Add Here...
                add_shortcode('bookingpress_waiting_payment', array( $this, 'bookingpress_waiting_payment_form' ));
                add_filter('bookingpress_after_admin_appointments_list_load',array($this,'bookingpress_after_appointments_list_file_load_fun'),10,1);
                add_filter('bookingpress_before_admin_appointments_list_load',array($this,'bookingpress_before_appointments_list_load_fun'),10,1);

                add_action('wp_ajax_bookingpress_final_waiting_payment', array($this, 'bookingpress_final_waiting_payment_func'));
                add_action('wp_ajax_nopriv_bookingpress_final_waiting_payment', array($this, 'bookingpress_final_waiting_payment_func'));

                //Waiting Payment Dynamic Data Fields
                add_filter('bookingpress_waiting_payment_dynamic_data_fields', array($this, 'bookingpress_waiting_payment_dynamic_data_fields_func'), 10, 1);

                //Add a global email variable here
                
                add_filter( 'bookingpress_add_dynamic_notification_data_fields', array( $this, 'bookingpress_add_dynamic_notification_data_fields_func' ), 20 );                
                add_action('bookingpress_notification_external_message_plachoders',array($this,'bookingpress_notification_external_message_plachoders_fun'));


                //Add a filter data for appointment

                add_action('bookingpress_admin_panel_vue_methods', array($this, 'bookingpress_admin_panel_vue_methods_func'));
                add_action('bookingpress_appointment_add_post_data',array($this,'bookingpress_appointment_add_post_data_fun'),10);

                add_filter( 'bookingpress_modify_appointment_data_fields', array( $this, 'bookingpress_modify_appointment_data_fields_func' ), 20 ,1);
                add_action('wp_ajax_bookingpress_get_waiting_appointments',array($this,'bookingpress_get_waiting_appointments_fun'),5);
                //add_action('bookingpress_appointment_add_dynamic_vue_methods',array($this,'bookingpress_appointment_add_dynamic_vue_methods'),5);

                add_action('bookingpress_modify_appointment_send_data',array($this,'bookingpress_modify_appointment_send_data_fun'));
                add_action('bookingpress_modify_appointment_success_response_data',array($this,'bookingpress_modify_appointment_success_response_data_fun'));

                add_filter('bookingpress_appointment_view_add_filter',array($this,'bookingpress_appointment_view_add_filter_fun'),15,2);

                add_filter('bookingpress_appointment_report_view_add_filter',array($this,'bookingpress_appointment_report_view_add_filter_fun'),15,2);

                add_action('wp_ajax_bookingpress_approve_waiting_appointment', array( $this, 'bookingpress_approve_waiting_appointment_fun' ));

                //Add date & time summary message for waiting list
                add_action('bookingpress_add_content_after_date_time_summary',array($this,'bookingpress_add_content_after_date_time_summary_fun'));

                //Add cart waiting list summary
                add_action('bookingpress_cart_item_list_service_time_after',array($this,'bookingpress_add_cart_waiting_list_summary_fun'));

                //New Hook Added
                add_action('bookingpress_customize_color_setting_after',array($this,'bookingpress_customize_color_setting_after_fun'));

                //New Hook Added
                add_filter('bookingpress_generate_booking_form_customize_css',array($this,'bookingpress_customize_css_content_modify_fun'),11,2);

                //Replace waiting payment url content
                add_filter('bookingpress_modify_email_notification_content', array( $this, 'bookingpress_modify_email_content_func' ), 12, 4);

                //Remove Data In The Report
                add_filter('bookingpress_modify_appointment_data', array($this, 'bookingpress_modify_appointment_data_func'), 15, 1);

                //New Hook Added
                add_filter('bookingpress_before_appointment_confirm_booking', array($this, 'bookingpress_before_appointment_confirm_booking_func'), 15, 8);

                //Remove Waiting List Appointment on dashboard upcomming appointment
                add_filter('bookingpress_dashboard_upcoming_appointments_data_filter', array($this, 'bookingpress_dashboard_upcoming_appointments_data_filter_func'), 15, 1);

                //--Add new hook in pro version for reset color option.
                add_action('bookingpress_reset_color_option_after',array($this,'bookingpress_reset_color_option_after_fun'),10);

                //--Add new hook in pro version for reset color option.
                add_filter('bookingpress_modify_appointment_status_cls',array($this,'bookingpress_modify_appointment_status_cls_fun'),10,2);

                //--Add new action on pro version booking form for display day timeslot
                add_action('bookingpress_add_day_detail_after',array($this,'bookingpress_add_day_list_counter_func'),11);

                add_filter('bookingpress_disable_date_vue_data_modify',array($this,'bookingpress_disable_date_vue_data_modify_fun'),15);
                
                add_filter( 'bookingpress_get_multiple_days_disable_dates', array( $this, 'bookingpress_get_multiple_days_disable_dates_func' ), 15, 5);

                add_filter('bookingpress_modify_my_appointments_data_externally',array($this,'bookingpress_modify_my_appointments_data_externally_fun'),15,1);

                //--Add new hook for day select before
                add_filter('bookingpress_day_select_before',array($this,'bookingpress_day_select_before_fun'),10,1);                

                //--Add new hook for google calendar,zoom,outlookz,zapier add waiting list
                add_filter('bookingpress_check_status_for_appointment_integration', array($this, 'bookingpress_check_status_for_appointment_integration_func'), 15, 4);

                //--Add new hook for aweber addon
                add_filter('bookingpress_check_waiting_after_front_book_for_integration', array($this, 'bookingpress_check_waiting_after_front_book_for_integration_func'), 15, 2);

                add_filter('bookingpress_return_calculated_details_modify_outside',array($this,'bookingpress_return_calculated_details_modify_outside_fun'),15,4);

                //--add disable timeslot for waiting list
                add_filter('bookingpress_change_hide_already_booked_slot_for_service',array($this,'bookingpress_change_hide_already_booked_slot_for_service_fun'),10,2);

                // Remove Day Selected waiting date.
                add_filter( 'bookingpress_before_selecting_booking_service', array( $this, 'bookingpress_before_selecting_booking_service_fun') );

                add_action('bookingpress_after_open_add_service_model', array($this,'bookingpress_after_open_add_service_model_fun'));

                add_filter( 'bookingpress_allow_to_disable_booked_date', array( $this, 'bookingpress_allow_to_disable_booked_date_fun'),10,2);

                add_filter( 'bookingpress_add_single_disable_date_when_no_timeslot', array( $this, 'bookingpress_add_single_disable_date_when_no_timeslot_fun'),10,3);
                

                /*Multi language Treanslation */
                if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {
					add_filter('bookingpress_modified_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
                	add_filter('bookingpress_modified_customize_form_language_translate_fields',array($this,'bookingpress_modified_customize_form_language_translate_fields_func'),10);
					add_filter('bookingpress_modified_language_translate_fields_section',array($this,'bookingpress_modified_language_translate_fields_section_func'),10);
				}

                add_action('wp_ajax_bookingpress_get_disable_date', array( $this, 'bookingpress_get_disable_date_func_with_waiting_list' ), 5);
                add_action('wp_ajax_nopriv_bookingpress_get_disable_date', array( $this, 'bookingpress_get_disable_date_func_with_waiting_list' ), 5);
			}            
            add_action('admin_init', array( $this, 'bookingpress_update_waiting_list_data') );
	    add_action('activated_plugin',array($this,'bookingpress_is_waiting_list_addon_activated'),11,2);
		}

        function bookingpress_get_disable_date_func_with_waiting_list(){

            $start_ms = microtime( true );
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_payment_logs, $bookingpress_services, $bookingpress_appointment_bookings;
            $response              = array();
            $wpnonce               = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
            $bpa_verify_nonce_flag = wp_verify_nonce($wpnonce, 'bpa_wp_nonce');

            if (! $bpa_verify_nonce_flag ) {
                $response['variant']      = 'error';
                $response['title']        = esc_html__('Error', 'bookingpress-waiting-list');
                $response['msg']          = esc_html__('Sorry, Your request can not be processed due to security reason.', 'bookingpress-waiting-list');
                $response['redirect_url'] = '';
                wp_send_json($response);
                die();
            }

            $response['variant']    = 'error';
            $response['title']      = 'Error';
            $response['msg']        = 'Something went wrong....';

            //$consider_selected_date = false;

            if( !empty( $_POST['appointment_data_obj'] ) && !is_array( $_POST['appointment_data_obj'] ) ){
                $_POST['appointment_data_obj'] = json_decode( stripslashes_deep( $_POST['appointment_data_obj'] ), true ); //phpcs:ignore
                $_REQUEST['appointment_data_obj'] = $_POST['appointment_data_obj'] =  !empty($_POST['appointment_data_obj']) ? array_map(array($this,'bookingpress_boolean_type_cast'), $_POST['appointment_data_obj'] ) : array(); // phpcs:ignore
            }

            $bookingpress_appointment_data = !empty($_POST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_data_obj'] ) : array(); // phpcs:ignore
            $bookingpress_selected_date = !empty($_REQUEST['selected_date']) ? sanitize_text_field($_REQUEST['selected_date']) : '';

            $selected_service_duration_unit = $bookingpress_appointment_data['selected_service_duration_unit'];
            $selected_service_duration = $bookingpress_appointment_data['selected_service_duration'];

            $selected_service_duration_in_min = '';
            if( 'm' == $selected_service_duration_unit ){
                $selected_service_duration_in_min = $selected_service_duration;
            } else {
                if( 'h' == $selected_service_duration_unit ){
                    $selected_service_duration_in_min = ( MINUTE_IN_SECONDS * $selected_service_duration );
                }
            }

            if( 'd' == $selected_service_duration_unit ){
                $response['check_for_multiple_days_event'] = true;
            }
            if(!empty($bookingpress_selected_date)){
                $bookingpress_selected_date = date('Y-m-d', strtotime($bookingpress_selected_date));
            }
            
            if( "NaN-NaN-NaN" == $bookingpress_selected_date || '1970-01-01' == $bookingpress_selected_date || !preg_match('/(\d{4}\-\d{2}\-\d{2})/', $bookingpress_selected_date ) ){
                $bookingpress_selected_date = date('Y-m-d', current_time('timestamp') );
            }
            
            $bookingpress_selected_service= !empty($_REQUEST['selected_service']) ? intval($_REQUEST['selected_service']) : '';

            $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'waiting_list_max_slot');

            if( !empty( $bookingpress_waiting_list_max_slot ) ){

                if(empty($bookingpress_selected_service)){
                    $bookingpress_selected_service = $bookingpress_appointment_data['selected_service'];
                }
            
                if(empty($bookingpress_appointment_data['selected_service_duration_unit']) || empty($bookingpress_appointment_data['selected_service_duration']) ){
                    $bookingpress_service_data = $BookingPress->get_service_by_id($bookingpress_selected_service);
                    if(!empty($bookingpress_service_data['bookingpress_service_duration_unit'])){
                        $bookingpress_appointment_data['selected_service_duration_unit'] = $bookingpress_service_data['bookingpress_service_duration_unit'];
                        $bookingpress_appointment_data['selected_service_duration'] = intval($bookingpress_service_data['bookingpress_service_duration_val']);
                    }
                }
            
                if(empty($bookingpress_selected_date)){
                    $bookingpress_selected_date = !empty( $bookingpress_appointment_data['selected_date'] ) ? $bookingpress_appointment_data['selected_date'] : date('Y-m-d', current_time('timestamp') );
                }
                /* if( true == $consider_selected_date && !empty( $bpa_selected_date ) ){
                    $bookingpress_selected_date = $bpa_selected_date;
                } */
            
                if( "NaN-NaN-NaN" == $bookingpress_selected_date || '1970-01-01' == $bookingpress_selected_date || !preg_match('/(\d{4}\-\d{2}\-\d{2})/', $bookingpress_selected_date ) ){
                    $bookingpress_selected_date = date('Y-m-d', current_time('timestamp') );
                }
    
                /** get maximum period available from booking */
                $get_period_available_for_booking = $BookingPress->bookingpress_get_settings('period_available_for_booking', 'general_setting');
                if( empty( $get_period_available_for_booking ) || !$BookingPress->bpa_is_pro_active() ){
                    $get_period_available_for_booking = 365;
                }
    
                /** Modify get available time of booking if the service expiration time is set */
                $get_period_available_for_booking = apply_filters( 'bookingpress_modify_max_available_time_for_booking', $get_period_available_for_booking, $bookingpress_start_date, $bookingpress_selected_service );
				
				$posted_disabled_dates = !empty( $_POST['disabled_dates'] ) ? json_decode( stripslashes_deep( $_POST['disabled_dates'] ), true ) : array();

				if( !empty( $posted_disabled_dates ) ){
					$posted_disabled_dates = array_filter( $posted_disabled_dates );                
				}
    
                if( !empty( $bookingpress_selected_service ) ){
                    $response['prevent_next_month_check'] = false;

                    $bookingpress_start_date = date('Y-m-d', current_time('timestamp') );

                    $bookingpress_start_date_with_time = date('Y-m-d H:i:s', current_time( 'timestamp') );

                    /** apply filter to modify start date. in case of Minimum Time Required Booking */
                    $bookingpress_start_date = apply_filters( 'bookingpress_modify_disable_date_start_date', $bookingpress_start_date, $bookingpress_selected_service, $bookingpress_start_date_with_time );

                    /* if( true == $consider_selected_date && !empty( $bpa_selected_date ) ){
                        $bookingpress_start_date = $bpa_selected_date;
                    } */

                    $bookingpress_temp_end_date = date('Y-m-d', strtotime('last day of this month', strtotime( $bookingpress_start_date )));

                    $bookingpress_end_date = date('Y-m-d', strtotime( '+' . $get_period_available_for_booking . ' days') );

                    $next_month = date( 'm', strtotime( $bookingpress_temp_end_date . '+1 day' ) );
                    $next_year = date( 'Y', strtotime( $bookingpress_temp_end_date . '+1 day' ) );
                    
                    $bookingpress_selected_staffmember_id = !empty($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : '';

                    /** Get the default days off in the above limit */
                    $bpa_retrieves_default_disabled_dates = $BookingPress->bookingpress_retrieve_off_days( $bookingpress_start_date, ( $get_period_available_for_booking + 1 ), $bookingpress_selected_service, $selected_service_duration_in_min, $bookingpress_selected_staffmember_id );
					
					$bpa_retrieves_default_disabled_dates = array_merge( $bpa_retrieves_default_disabled_dates, $posted_disabled_dates );

                    if( 'd' != $selected_service_duration_unit ){
                            

                        /** loop through each days until the limit has been reached
                         * for lite - it'll check for the next 365 days
                         * for pro - it'll check up to the X number of days defined in the settings
                         */
                        $bpa_begin_date = new DateTime( $bookingpress_start_date );
                        $bpa_end_date = new DateTime( date('Y-m-d', strtotime($bookingpress_end_date . '+1 day')) );
                        
                        $bpa_interval = DateInterval::createFromDateString('1 day');
                        $period = new DatePeriod($bpa_begin_date, $bpa_interval, $bpa_end_date);

                        $bookingpress_selected_date = $bookingpress_end_date;
                        $front_timings_data = array();

                        $count = 0;
                        $stop_date = '';

                        
                        foreach( $period as $dt ){
                            $bpa_check_date = $dt->format('Y-m-d');
        
                            if( in_array( $bpa_check_date, $bpa_retrieves_default_disabled_dates ) ){
                                continue;
                            }

                            /** Stop the loop if the time slot is available & date is equals to the last day of the available date month */
                            if( !empty( $stop_date ) && $bpa_check_date > date( 'Y-m-d', strtotime( 'last day of this month', strtotime( $stop_date) ) ) ){
                                $last_date = date('Y-m-d', strtotime( 'last day of this month', strtotime( $stop_date ) ) );
                                $next_month = date('m', strtotime( $last_date . '+1 day') );
                                $next_year = date('Y', strtotime( $last_date . '+1 day') );
                                break;
                            }

                            $bookingpress_time_slots = $bookingpress_appointment_bookings->bookingpress_retrieve_timeslots( $bpa_check_date, true );

                            $bookingpress_timeslots_data = array_merge(
                                $bookingpress_time_slots['morning_time'],
                                $bookingpress_time_slots['afternoon_time'],
                                $bookingpress_time_slots['evening_time'],
                                $bookingpress_time_slots['night_time']
                            );

                            if( empty( $bookingpress_timeslots_data ) ){
                                $bpa_retrieves_default_disabled_dates[] = date('Y-m-d H:i:s', strtotime( $bpa_check_date ) );
                            } else {

                                $total_count = count( $bookingpress_timeslots_data );
                                $total_disable_count = 0;
                                foreach( $bookingpress_timeslots_data as $timeslot_dates ){
                                    if( !empty( $timeslot_dates['waiting_slot_disable'] ) && 1 == $timeslot_dates['waiting_slot_disable'] ){
                                        $total_disable_count++;
                                    }
                                }

                                if( $total_disable_count >= $total_count ){
                                    $bpa_retrieves_default_disabled_dates[] = date('Y-m-d H:i:s', strtotime( $bpa_check_date ) );
                                } else {
                                    if( $count < 1 ){
                                        $date1 = new DateTime( $bookingpress_start_date );
                                        $date2 = new DateTime( $bpa_check_date );
                                        $interval = $date1->diff($date2);
                                        $interval_in_days = $interval->days;
                                        if( $interval_in_days < $get_period_available_for_booking ){
                                            $bookingpress_selected_date = $bpa_check_date;
                                            /** Check if the selected date is greater than end date in respect to service expiration */
                                            $front_timings_data = $bookingpress_appointment_bookings->bookingpress_retrieve_timeslots( $bpa_check_date, true );
                                            $stop_date = $bpa_check_date;
                                            $count++;
                                        }
                                    }
                                }
                            }                        
                        }

                        $bookingpress_disable_date = array();
                        foreach( $bpa_retrieves_default_disabled_dates as $disabled_dates ){
                            $bookingpress_disable_date[] = date('c', strtotime( $disabled_dates ) );
                        }

                        $attributes = array();
						
						if( !empty( $get_period_available_for_booking ) ){           
							$bookingpress_current_date = date('Y-m-d', current_time('timestamp') );
							$max_available_date = date('Y-m-d', strtotime( $bookingpress_current_date . '+' . $get_period_available_for_booking . ' days') );
							$response['max_available_date'] = $max_available_date;
							$response['max_available_month'] = date('m', strtotime( $max_available_date ) );
							$response['max_available_year'] = date('Y', strtotime( $max_available_date ) );
							if( $max_available_date < $response['selected_date'] ){
								$response['front_timings'] = array();
								$response['next_month'] = $next_month;
								wp_send_json( $response );
								die;
							}
						}

                        $response['variant']    = 'success';
                        $response['title']      = 'Success';
                        $response['msg']        = 'Data reterive successfully';                            
                        $response['days_off_disabled_dates']  =  implode(',',$bookingpress_disable_date );
                        $response['days_off_disabled_dates_string']  =  implode(',',$bpa_retrieves_default_disabled_dates );
                        $response['selected_date']  = date('Y-m-d', strtotime($bookingpress_selected_date));
                        $response['next_month'] = $next_month;
                        $response['vcal_attributes'] = $attributes;
                        $response['max_capacity_capacity'] = $max_service_capacity;
                        $response['front_timings'] = $front_timings_data;
                        $response['next_year'] = $next_year;
                        $response['msg']        = 'Data reterive successfully';
                    } else {
                        $multiple_day_response = array();
					    $multiple_day_response = apply_filters( 'bookingpress_get_multiple_days_disable_dates', $multiple_day_response, $bookingpress_selected_date, $bookingpress_selected_service, $bookingpress_appointment_data );

                        $response = $this->bookingpress_get_multiple_days_disable_dates_func( $multiple_day_response, $bookingpress_selected_date, $bookingpress_selected_service, $bookingpress_appointment_data );
                    }
                }

                $end_ms = microtime( true );
                $response['time_taken'] = ( $end_ms - $start_ms ) . ' seconds';

               
                wp_send_json($response);
                die;
            }

        
        }
                
        
        function bookingpress_add_single_disable_date_when_no_timeslot_fun($bookingpress_add_single_disable_date,$bookingpress_selected_service,$front_timings){
            global $bookingpress_services;
            if($bookingpress_selected_service && !empty($front_timings)){
                $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'waiting_list_max_slot');
                if($bookingpress_waiting_list_max_slot){
                    $bookingpress_add_single_disable_date = false; 
                }
            }
            return $bookingpress_add_single_disable_date;
        }

        /**
         * Function for remove disable date when timeslot in waiting
         *
         * @param  mixed $bookingpress_allow_to_disable_booked_date
         * @param  mixed $bookingpress_selected_service
         * @param  mixed $appointment_data_obj
         * @return void
         */
        function bookingpress_allow_to_disable_booked_date_fun($bookingpress_allow_to_disable_booked_date,$bookingpress_selected_service){
            global $bookingpress_services;
            if($bookingpress_selected_service){
                $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'waiting_list_max_slot');
                if($bookingpress_waiting_list_max_slot){
                    $bookingpress_allow_to_disable_booked_date = false; 
                }
            }            
            return $bookingpress_allow_to_disable_booked_date;
        }

        function bookingpress_modified_language_translate_fields_func($bookingpress_all_language_translation_fields){
			$bookingpress_waiting_list_language_translation_fields = array(                
				'customized_form_waiting_list_labels' => array(
					'waiting_slot_label' => array('field_type'=>'text','field_label'=>__('Waiting Slot Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_book_button_label' => array('field_type'=>'text','field_label'=>__('Waiting List Button Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_payable_amount_label' => array('field_type'=>'text','field_label'=>__('Waiting List Payable Amount Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_position_label' => array('field_type'=>'text','field_label'=>__('Waiting Position label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_payment_success_message' => array('field_type'=>'text','field_label'=>__('Waiting Payment Success Message', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                )
			);  
			$bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_waiting_list_language_translation_fields);
            return $bookingpress_all_language_translation_fields;
		}

		function bookingpress_modified_customize_form_language_translate_fields_func($bookingpress_all_language_translation_fields){
			$bookingpress_waiting_list_language_translation_fields = array(                
				'customized_form_waiting_list_labels' => array(
					'waiting_slot_label' => array('field_type'=>'text','field_label'=>__('Waiting Slot Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_book_button_label' => array('field_type'=>'text','field_label'=>__('Waiting List Button Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_payable_amount_label' => array('field_type'=>'text','field_label'=>__('Waiting List Payable Amount Label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_position_label' => array('field_type'=>'text','field_label'=>__('Waiting Position label', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                    'waiting_payment_success_message' => array('field_type'=>'text','field_label'=>__('Waiting Payment Success Message', 'bookingpress-waiting-list'),'save_field_type'=>'booking_form'),
                )
			);  
			$pos = 5;
			$bookingpress_all_language_translation_fields = array_slice($bookingpress_all_language_translation_fields, 0, $pos)+$bookingpress_waiting_list_language_translation_fields + array_slice($bookingpress_all_language_translation_fields, $pos);
			return $bookingpress_all_language_translation_fields;
		}

        public function bookingpress_modified_language_translate_fields_section_func($bookingpress_all_language_translation_fields_section){
            /* Function to add waiting list step heading */
            $bookingpress_waiting_list_section_added = array('customized_form_waiting_list_labels' => __('Waiting List labels', 'bookingpress-waiting-list') );
			$bookingpress_all_language_translation_fields_section = array_merge($bookingpress_all_language_translation_fields_section,$bookingpress_waiting_list_section_added);
			return $bookingpress_all_language_translation_fields_section;
		}           
        
        function bookingpress_update_waiting_list_data(){
            global $BookingPress,$bookingpress_waiting_list_version;
            $bookingpress_db_waiting_list_version = get_option('bookingpress_waiting_list_version', true);

            if( version_compare( $bookingpress_db_waiting_list_version, '1.5', '<' ) ){

                $bookingpress_load_waiting_update_file = BOOKINGPRESS_WAITING_VIEW_DIR . 'upgrade_latest_waiting_list_data.php';
                include $bookingpress_load_waiting_update_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();

            }
        }

        /**
         * Function for clear waitinglist data in add service popup
         *
         * @return void
         */
        function bookingpress_after_open_add_service_model_fun() {
        ?>
            if(action == 'add') {
                vm.service.waiting_list_max_slot = 0;        
            }            
        <?php
        } 

		/**
		 * Function to reset selected waiting date
		 *
		 * @param  mixed $bookingpress_before_selecting_booking_service_data
		 * @return void
		 */
		function bookingpress_before_selecting_booking_service_fun( $bookingpress_before_selecting_booking_service_data ){

			$bookingpress_before_selecting_booking_service_data .= 'vm.appointment_step_form_data.selected_waiting_date = "";';

			return $bookingpress_before_selecting_booking_service_data;
		}        

        /**
         * Function for show disable timeslot
         *
         * @param  mixed $bookingpress_hide_already_booked_slot
         * @param  mixed $selected_service_id
         * @return void
        */
        public function bookingpress_change_hide_already_booked_slot_for_service_fun($bookingpress_hide_already_booked_slot, $selected_service_id){
            global $bookingpress_services;
            if($selected_service_id){
                $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($selected_service_id, 'waiting_list_max_slot');
                if($bookingpress_waiting_list_max_slot){
                    $bookingpress_hide_already_booked_slot = 0; 
                }
            }            
            return $bookingpress_hide_already_booked_slot;
        }
        
        function bookingpress_is_waiting_list_addon_activated($plugin,$network_activation)
        {  
            $myaddon_name = "bookingpress-waiting-list/bookingpress-waiting-list.php";

            if($plugin == $myaddon_name)
            {

                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Waiting List Add-on', 'bookingpress-waiting-list');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-waiting-list'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Waiting List Add-on', 'bookingpress-waiting-list');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-waiting-list'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_WAITING_LIST_STORE_URL;
                    $api_params = array(
                        'edd_action' => 'check_license',
                        'license' => $license,
                        'item_id'  => $package,
                        //'item_name' => urlencode( $item_name ),
                        'url' => home_url()
                    );
                    $response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
                    if ( is_wp_error( $response ) ) {
                        return false;
                    }
        
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string =  wp_remote_retrieve_body( $response );
        
                    $message = '';

                    if ( true === $license_data->success ) 
                    {
                        if($license_data->license != "valid")
                        {
                            deactivate_plugins($myaddon_name, FALSE);
                            $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Waiting List Add-on', 'bookingpress-waiting-list');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-waiting-list'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($myaddon_name, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Waiting List Add-on', 'bookingpress-waiting-list');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-waiting-list'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                }
            }

        }

        /**
         * Function for add admin waiting appoitment without payment empty variable set
         *
         * @param  mixed $bookingpress_return_calculated_details
         * @param  mixed $bookingpress_calculated_payment_details
         * @param  mixed $bookingpress_appointment_id
         * @param  mixed $bookingpress_payment_id
         * @return void
         */
        public function bookingpress_return_calculated_details_modify_outside_fun($bookingpress_return_calculated_details, $bookingpress_calculated_payment_details, $bookingpress_appointment_id, $bookingpress_payment_id){
            if(!$bookingpress_payment_id){

                $bookingpress_return_calculated_details['is_rescheduled'] = (isset($bookingpress_return_calculated_details['is_rescheduled']))?$bookingpress_return_calculated_details['is_rescheduled']:'';
                $bookingpress_return_calculated_details['bookingpress_selected_gateway'] = (isset($bookingpress_return_calculated_details['bookingpress_selected_gateway']))?$bookingpress_return_calculated_details['bookingpress_selected_gateway']:'';
                $bookingpress_return_calculated_details['bookingpress_selected_gateway_label'] = (isset($bookingpress_return_calculated_details['bookingpress_selected_gateway_label']))?$bookingpress_return_calculated_details['bookingpress_selected_gateway_label']:'';
                $bookingpress_return_calculated_details['bookingpress_payment_status'] = (isset($bookingpress_return_calculated_details['bookingpress_payment_status']))?$bookingpress_return_calculated_details['bookingpress_payment_status']:'';
                $bookingpress_return_calculated_details['subtotal_amt'] = (isset($bookingpress_return_calculated_details['subtotal_amt']))?$bookingpress_return_calculated_details['subtotal_amt']:'';
                $bookingpress_return_calculated_details['subtotal_amt_with_currency'] = (isset($bookingpress_return_calculated_details['subtotal_amt_with_currency']))?$bookingpress_return_calculated_details['subtotal_amt_with_currency']:'';
                $bookingpress_return_calculated_details['deposit_price'] = (isset($bookingpress_return_calculated_details['deposit_price']))?$bookingpress_return_calculated_details['deposit_price']:'';
                $bookingpress_return_calculated_details['deposit_price_with_currency'] = (isset($bookingpress_return_calculated_details['deposit_price_with_currency']))?$bookingpress_return_calculated_details['deposit_price_with_currency']:'';
                $bookingpress_return_calculated_details['bookingpress_tax_amount'] = (isset($bookingpress_return_calculated_details['bookingpress_tax_amount']))?$bookingpress_return_calculated_details['bookingpress_tax_amount']:'';
                $bookingpress_return_calculated_details['bookingpress_tax_amount_with_currency'] = (isset($bookingpress_return_calculated_details['bookingpress_tax_amount_with_currency']))?$bookingpress_return_calculated_details['bookingpress_tax_amount_with_currency']:'';
                $bookingpress_return_calculated_details['paid_total_amount'] = (isset($bookingpress_return_calculated_details['paid_total_amount']))?$bookingpress_return_calculated_details['paid_total_amount']:'';
                $bookingpress_return_calculated_details['price_display_setting'] = (isset($bookingpress_return_calculated_details['price_display_setting']))?$bookingpress_return_calculated_details['price_display_setting']:'';
                $bookingpress_return_calculated_details['display_tax_amount_in_order_summary'] = (isset($bookingpress_return_calculated_details['display_tax_amount_in_order_summary']))?$bookingpress_return_calculated_details['display_tax_amount_in_order_summary']:'';
                $bookingpress_return_calculated_details['applied_coupon'] = (isset($bookingpress_return_calculated_details['applied_coupon']))?$bookingpress_return_calculated_details['applied_coupon']:'';
                $bookingpress_return_calculated_details['coupon_discount_amt'] = (isset($bookingpress_return_calculated_details['coupon_discount_amt']))?$bookingpress_return_calculated_details['coupon_discount_amt']:'';
                $bookingpress_return_calculated_details['coupon_discount_amt_with_currency'] = (isset($bookingpress_return_calculated_details['coupon_discount_amt_with_currency']))?$bookingpress_return_calculated_details['coupon_discount_amt_with_currency']:'';
                $bookingpress_return_calculated_details['final_total_amount'] = (isset($bookingpress_return_calculated_details['final_total_amount']))?$bookingpress_return_calculated_details['final_total_amount']:'';
                $bookingpress_return_calculated_details['final_total_amount_with_currency'] = (isset($bookingpress_return_calculated_details['final_total_amount_with_currency']))?$bookingpress_return_calculated_details['final_total_amount_with_currency']:'';
                $bookingpress_return_calculated_details['is_cart'] = (isset($bookingpress_return_calculated_details['is_cart']))?$bookingpress_return_calculated_details['is_cart']:'';
                $bookingpress_return_calculated_details['is_deposit_enable'] = (isset($bookingpress_return_calculated_details['is_deposit_enable']))?$bookingpress_return_calculated_details['is_deposit_enable']:'';
                $bookingpress_return_calculated_details['extra_services_details'] = (isset($bookingpress_return_calculated_details['extra_services_details']))?$bookingpress_return_calculated_details['extra_services_details']:'';
                $bookingpress_return_calculated_details['selected_extra_members'] = (isset($bookingpress_return_calculated_details['selected_extra_members']))?$bookingpress_return_calculated_details['selected_extra_members']:'';
                $bookingpress_return_calculated_details['included_tax_label'] = (isset($bookingpress_return_calculated_details['included_tax_label']))?$bookingpress_return_calculated_details['included_tax_label']:'';
            }
            return $bookingpress_return_calculated_details;
        }

        /**
         * Function for Mailchamp & Aweber Integration
         *
         * @param  mixed $bookingpress_waiting_ap
         * @param  mixed $appointment_data
         * @return void
         */
        public function bookingpress_check_waiting_after_front_book_for_integration_func($bookingpress_waiting_ap,$appointment_data){

            $is_waiting_list = (isset($appointment_data['is_waiting_list']))?$appointment_data['is_waiting_list']:0;
            if($is_waiting_list){
                $bookingpress_waiting_ap = true;
            }
            return $bookingpress_waiting_ap;

        }
        
        /**
         * Function for remove waiting appointment from Google Calendar, Mailchimp, Outlook Calendar, Zapier, Zoom Addon
         *
         * @param  mixed $bookingpress_return
         * @param  mixed $appointment_id
         * @param  mixed $bookingpress_appointment_status
         * @param  mixed $appointment_new_status
         * @return void
         */
        public function bookingpress_check_status_for_appointment_integration_func($bookingpress_return, $appointment_id, $bookingpress_appointment_status = '', $appointment_new_status=''){
            global $wpdb,$tbl_bookingpress_appointment_bookings;
            if(empty($bookingpress_appointment_status)){
                $bookingpress_appointment_status = $wpdb->get_var($wpdb->prepare("SELECT bookingpress_appointment_status FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $appointment_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
            }            
            if($bookingpress_appointment_status == 7){
                $bookingpress_return = true;
            }
            return $bookingpress_return;
        }
        
        /**
         * Function for add day click event when day appointment 
         *
         * @param  mixed $bookingpress_day_select_before
         * @return void
         */
        public function bookingpress_day_select_before_fun($bookingpress_day_select_before){
            global $BookingPress;
            $is_cart_addon_active  = $this->is_cart_addon_active();              
            $waiting_book_button_label = $BookingPress->bookingpress_get_customize_settings('waiting_book_button_label','booking_form');
            $waiting_payable_amount_label = $BookingPress->bookingpress_get_customize_settings('waiting_payable_amount_label','booking_form');

            $bookingpress_waiting_day_select_after = '';
            $bookingpress_waiting_day_select_after = apply_filters('bookingpress_waiting_day_select_after',$bookingpress_waiting_day_select_after);

            $bookingpress_day_select_before.='
                vm.appointment_step_form_data.is_waiting_list = 0;
                vm.appointment_step_form_data.selected_waiting_date = ""; 
                if( max_available_date < day.id || (day.date < vm.jsCurrentDateFormatted) ){
                    return false;
                    vm.appointment_step_form_data.is_waiting_list = 0;
                    vm.appointment_step_form_data.selected_waiting_date = "";                    
                }else{                    
                    if("undefined" != typeof vm.only_waiting_dates && vm.only_waiting_dates.includes(day.id)){
                                                
                        vm.appointment_step_form_data.is_waiting_list = 1;
                        vm.bookingpress_book_appointment_btn_text = "'.$waiting_book_button_label.'";
                        vm.bookingpress_total_amount_text = "'.$waiting_payable_amount_label.'";  
                        var waiting_count = parseInt(vm.waiting_attributes[day.id].waiting_count)+1;
                        var waiting_number_disp = String(waiting_count).padStart(2, "0");
                        vm.appointment_step_form_data.waiting_number_disp = waiting_number_disp;
                        vm.appointment_step_form_data.waiting_number = waiting_count;
                        vm.appointment_step_form_data.selected_date = day.id;
                        vm.appointment_step_form_data.selected_waiting_date = day.id;
                        vm.bookingpress_select_multi_day_range(day);
                        vm.get_date_timings( day.id );                        
                        
                        '.$bookingpress_waiting_day_select_after.'

                        vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name, vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name, vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].previous_tab_name);                        
                    }else{

                        if(("undefined" != typeof vm.v_calendar_disable_dates && ( vm.v_calendar_disable_dates.includes(day.id) || vm.v_calendar_disable_dates.includes(day.id+ " 00:00:00") ) ) || ("undefined" != typeof vm.remove_in_waiting_list_date && vm.remove_in_waiting_list_date.includes(day.id)) ){

                        }else{
                            vm.appointment_step_form_data.is_waiting_list = 0;
                            app.bookingpress_book_appointment_btn_text = app.bookingpress_book_appointment_btn_text_org;
                            app.bookingpress_total_amount_text = app.bookingpress_total_amount_text_org;                        
                        }

                    }
                }
            ';
            return $bookingpress_day_select_before;

        }
        
        /**
         * Function For add day waiting count
         *
         * @return void
        */
        public function bookingpress_add_day_list_counter_func(){
            global $BookingPress;
            $waiting_slot_label = $BookingPress->bookingpress_get_customize_settings('waiting_slot_label','booking_form');            
        ?>
            <span v-if="(( ('undefined' != typeof only_waiting_dates &&  only_waiting_dates.includes(day.id)) || 'undefined' != typeof waiting_attributes[day.id]))" :class="(!only_waiting_dates.includes(day.id))?'bpa-front-dt__day-waiting-slot-disable':''" class="bpa-front-dt__day-slot-label"><?php echo esc_html($waiting_slot_label); ?> ({{waiting_attributes[day.id].waiting_count}})</span>        
        <?php
        }
        
        /**
         * function for add disable vue data for day service
         *
         * @param  mixed $bookingpress_disable_date_vue_data
         * @return void
        */
        function bookingpress_disable_date_vue_data_modify_fun($bookingpress_disable_date_vue_data){
            $bookingpress_disable_date_vue_data.=' 
                vm.waiting_attributes = response.data.waiting_attributes; 
                vm.only_waiting_dates = response.data.only_waiting_dates;
                vm.remove_in_waiting_list_date = response.data.remove_in_waiting_list_date;
            ';
            return $bookingpress_disable_date_vue_data; 
        }
        
        /**
         * Function for remove rescheduling button on waiting list appointment
         *
         * @param  mixed $bookingpress_appointments_data
         * @return void
         */
        function bookingpress_modify_my_appointments_data_externally_fun($bookingpress_appointments_data){                        
            if($bookingpress_appointments_data['bookingpress_appointment_status'] == 7){
                $bookingpress_appointments_data['allow_rescheduling'] = 0;
                if($bookingpress_appointments_data['allow_cancelling']){
                    $bookingpress_appointments_data['hide_action_wrapper'] = 1;
                    $bookingpress_appointments_data['bpa_display_invoice_btn'] = false;
                }
            }
            return $bookingpress_appointments_data;

        }
        
        /**
         * Function for get waiting day appointment
         *
         * @param  mixed $response
         * @param  mixed $bookingpress_selected_date
         * @param  mixed $bookingpress_selected_service
         * @param  mixed $bookingpress_appointment_data
         * @param  mixed $whole_day
         * @return void
         */
        function bookingpress_get_multiple_days_disable_dates_func( $response, $bookingpress_selected_date, $bookingpress_selected_service, $bookingpress_appointment_data, $whole_day = false ){            
            global $bookingpress_bring_anyone_with_you,$bookingpress_services,$tbl_bookingpress_staffmembers_services,$wpdb,$BookingPress,$tbl_bookingpress_appointment_bookings,$bookingpress_bring_anyone_with_you;

            $month_check = '';
            $all_waiting_dates = array();
            $only_waiting_dates = array();
            $remove_in_waiting_list_date = array('none');
            $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'waiting_list_max_slot');
            if($bookingpress_waiting_list_max_slot){

                /* New Logic Start Here */                
                
                $is_cart_addon_active  = $this->is_cart_addon_active();
                $appointment_data_obj = !empty($_POST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_data_obj'] ) : array(); // phpcs:ignore
                if($is_cart_addon_active){                                
                    if(isset($appointment_data_obj['cart_items'])){
                        $cart_items = $appointment_data_obj['cart_items'];
                        $is_waiting_list = $appointment_data_obj['is_waiting_list'];
                        $cart_item_edit_index = (isset($appointment_data_obj['cart_item_edit_index']))?$appointment_data_obj['cart_item_edit_index']:'';
                        if(!empty($cart_items)){
                            if(!$is_waiting_list && $cart_item_edit_index != 0){                               
                                $response['waiting_attributes'] = $all_waiting_dates;
                                $response['only_waiting_dates'] = $only_waiting_dates; 
                                $response['remove_in_waiting_list_date'] = $remove_in_waiting_list_date;           
                                return $response;                                
                            }
                        }                    
                    }
                }
                                
                /* New Logic Over Here */

                $bookingpress_disable_date = array();
                $days_off_disabled_dates = $response['days_off_disabled_dates'];
                $days_off_disabled_dates_string = $response['days_off_disabled_dates_string'];
                $attributes = $response['vcal_attributes'];
                if(!empty($days_off_disabled_dates)){
                    $bookingpress_disable_date = explode(',',$days_off_disabled_dates);
                }          
                $bookingpress_staffmember_id = !empty( $_POST['appointment_data_obj']['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) ? intval( $_POST['appointment_data_obj']['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) : ''; // phpcs:ignore
                if( empty( $bookingpress_staffmember_id ) && !empty( $_POST['bookingpress_selected_staffmember']['selected_staff_member_id'] ) ){ // phpcs:ignore
                    $bookingpress_staffmember_id = intval( $_POST['bookingpress_selected_staffmember']['selected_staff_member_id'] ); // phpcs:ignore
                }

                $where_staff_memb_cond = '';
                if(!empty($bookingpress_staffmember_id)){
                    $where_staff_memb_cond = '  AND bookingpress_staff_member_id = '.$bookingpress_staffmember_id;
                }                
                if( !empty( $bookingpress_selected_service ) ){
                    
                    $bookingpress_selected_staffmember_id = !empty($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : '';				                    
                    $bookingpress_start_date = date('Y-m-d', current_time('timestamp'));                    
                    if( true == $whole_day && !empty( $bookingpress_selected_date ) ){
                        $bookingpress_start_date = $bookingpress_selected_date;
                    }    
                    $bookingpress_end_date = date('Y-m-d', strtotime('last day of this month', strtotime( $bookingpress_start_date )));                    
                    $next_month = date( 'm', strtotime( $bookingpress_end_date . '+1 day' ) );
                    $next_year = date( 'Y', strtotime( $bookingpress_end_date . '+1 day' ) );                                          
                    $bookingpress_total_booked_appointment_where_clause = '';                    
                    $bookingpress_shared_service_timeslot = $BookingPress->bookingpress_get_settings('share_timeslot_between_services', 'general_setting');
                    if( 'true' != $bookingpress_shared_service_timeslot ){
                        $bookingpress_shared_service_timeslot .= $wpdb->prepare( ' AND bookingpress_service_id = %d ', $bookingpress_selected_service );
                        $bookingpress_total_booked_appointment_where_clause =  $wpdb->prepare( ' AND bookingpress_service_id = %d ', $bookingpress_selected_service );
                        $bookingpress_total_booked_appointment_where_clause = apply_filters( 'bookingpress_total_booked_appointment_where_clause', $bookingpress_total_booked_appointment_where_clause );
                    }                        
                    $max_service_capacity = 1;
                    if($bookingpress_staffmember_id){
                        $max_service_capacity = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_capacity FROM {$tbl_bookingpress_staffmembers_services} WHERE bookingpress_staffmember_id = %d AND bookingpress_service_id = %d", $bookingpress_staffmember_id,  $bookingpress_selected_service ) );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers_services is table name defined globally. False Positive alarm
                    }else{
                        $max_service_capacity = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'max_capacity');
                    }   
                    $end_date_with_year_limit = date('Y-m-d',strtotime($bookingpress_start_date . " + 365 day")); 
                    $max_service_capacity = apply_filters( 'bookingpress_retrieve_capacity', $max_service_capacity, $bookingpress_selected_service );
                    $bookingpress_total_appointment = $wpdb->get_results($wpdb->prepare("SELECT bookingpress_staff_member_id,bookingpress_appointment_date,bookingpress_service_duration_val,bookingpress_service_duration_unit,SUM(bookingpress_selected_extra_members) as bookingpress_total_person FROM " . $tbl_bookingpress_appointment_bookings . " WHERE bookingpress_service_duration_unit = 'd' AND (bookingpress_appointment_status = %s) AND bookingpress_appointment_date BETWEEN %s AND %s ".$bookingpress_total_booked_appointment_where_clause . ' '.$where_staff_memb_cond.' GROUP BY bookingpress_appointment_date','7',$bookingpress_start_date, $end_date_with_year_limit), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm                    
                    $multiple_days_event_waitng_count = array();
                    if( !empty( $bookingpress_total_appointment ) ){
                        foreach( $bookingpress_total_appointment as $key => $value ){

                            $booked_appointment_date = $value['bookingpress_appointment_date'];		                            
                            $service_duration_val = $value['bookingpress_service_duration_val'];                            
                            if( empty( $multiple_days_event_waitng_count[ $booked_appointment_date ] ) ){
                                $multiple_days_event_waitng_count[ $booked_appointment_date ] = !empty( $value['bookingpress_total_person'] ) ? $value['bookingpress_total_person'] : 1;
                            } else {
                                if( !empty( $value['bookingpress_total_person'] ) ){
                                    $multiple_days_event_waitng_count[ $booked_appointment_date ] += $value['bookingpress_total_person'];
                                } else {
                                    $multiple_days_event_waitng_count[ $booked_appointment_date ]++;
                                }
                            }
                            for( $d = 1; $d < $service_duration_val; $d++ ){
                                $booked_day_plus = date( 'Y-m-d', strtotime( $booked_appointment_date . '+' . $d . ' days' ));
                                
                                if( empty( $multiple_days_event_waitng_count[ $booked_day_plus ] ) ){
                                    $multiple_days_event_waitng_count[ $booked_day_plus ]  = !empty( $value['bookingpress_total_person'] ) ? $value['bookingpress_total_person'] : 1;
                                } else {
                                    $multiple_days_event_waitng_count[ $booked_day_plus ]++;
                                }
                            }                            
                            for( $dm = $service_duration_val - 1; $dm > 0; $dm-- ){
                                $booked_day_minus = date( 'Y-m-d', strtotime( $booked_appointment_date . '-' . $dm . ' days' ));
                                if( empty( $multiple_days_event_waitng_count[ $booked_day_minus ] ) ){
                                    $multiple_days_event_waitng_count[ $booked_day_minus ]  = !empty( $value['bookingpress_total_person'] ) ? $value['bookingpress_total_person'] : 1;
                                } else {
                                    if( !empty( $value['bookingpress_total_person'] ) ){
                                        $multiple_days_event_waitng_count[ $booked_day_minus ] +=  $value['bookingpress_total_person'];
                                    } else {
                                        $multiple_days_event_waitng_count[ $booked_day_minus ]++;
                                    }
                                }
                            }                            
                        }
                    }

                    $max_service_capacity = 1;
                    if($bookingpress_staffmember_id){
                        $max_service_capacity = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_capacity FROM {$tbl_bookingpress_staffmembers_services} WHERE bookingpress_staffmember_id = %d  AND bookingpress_service_id = %d",$bookingpress_staffmember_id,  $bookingpress_selected_service ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers_services is table name defined globally. False Positive alarm
                    }else{
                        $max_service_capacity = $bookingpress_services->bookingpress_get_service_meta($bookingpress_selected_service, 'max_capacity');
                    } 
                    $waiting_book_date = array();                     
                                                                               
                    $bookingpress_total_appointment = $wpdb->get_results($wpdb->prepare("
                    SELECT bookingpress_staff_member_id,bookingpress_appointment_date,bookingpress_service_duration_val,bookingpress_service_duration_unit,SUM(bookingpress_selected_extra_members) as bookingpress_total_person FROM " .$tbl_bookingpress_appointment_bookings . " WHERE bookingpress_service_duration_unit = 'd' AND (bookingpress_appointment_status = %s OR bookingpress_appointment_status = %s) AND bookingpress_appointment_date BETWEEN %s AND %s ".$bookingpress_total_booked_appointment_where_clause . ' '.$where_staff_memb_cond.' GROUP BY bookingpress_appointment_date','1','2',$bookingpress_start_date, $end_date_with_year_limit), ARRAY_A); // phpcs:ignore
                    $multiple_days_event = array();
                    if( !empty( $bookingpress_total_appointment ) ){
                        foreach( $bookingpress_total_appointment as $key => $value ){
                            $booked_appointment_date = $value['bookingpress_appointment_date'];	
                            //$waiting_book_date[] = $booked_appointment_date;	
                            $service_duration_val = $value['bookingpress_service_duration_val'];     
                            $bookingpress_service_duration_unit = $value['bookingpress_service_duration_unit'];                                 
                                if( empty( $multiple_days_event[ $booked_appointment_date ] ) ){
                                    $multiple_days_event[ $booked_appointment_date ] = !empty( $value['bookingpress_total_person'] ) ? $value['bookingpress_total_person'] : 1;
                                } else {
                                    if( !empty( $value['bookingpress_total_person'] ) ){
                                        $multiple_days_event[ $booked_appointment_date ] += $value['bookingpress_total_person'];
                                    } else {
                                        $multiple_days_event[ $booked_appointment_date ]++;
                                    }
                                }

                                for( $d = 1; $d < $service_duration_val; $d++ ){                                                                   
                                    $booked_day_plus = date( 'Y-m-d', strtotime( $booked_appointment_date . '+' . $d . ' days' ));  
                                    //$waiting_book_date[] =  $booked_day_plus;                                    
                                    if( empty( $multiple_days_event[ $booked_day_plus ] ) ){
                                        $multiple_days_event[ $booked_day_plus ]  = !empty( $value['bookingpress_total_person'] ) ? $value['bookingpress_total_person'] : 1;
                                    } else {
                                        $multiple_days_event[ $booked_day_plus ]++;
                                    }
                                }
                                                                

                                                       
                        }
                    }                                       
                    if( !empty( $multiple_days_event ) ){                       
                        foreach( $multiple_days_event as $md_date => $md_cap ){
                            if( $md_cap >= $max_service_capacity){
                                $waiting_book_date[] = $md_date;	
                            }                            
                        }
                    }                    
                    if(empty($waiting_book_date)){
                        $waiting_book_date = array('none');
                    }else{
                        
                        $service_duration_val = $value['bookingpress_service_duration_val']; 
                        $service_duration_val_before = $service_duration_val;                        
                        $bpa_selected_service_duration_unit = (isset($appointment_data_obj['selected_service_duration_unit']))?$appointment_data_obj['selected_service_duration_unit']:'';
                        if($bpa_selected_service_duration_unit == 'd'){
                            $bpa_selected_service_duration = (isset($appointment_data_obj['selected_service_duration']))?$appointment_data_obj['selected_service_duration']:'';
                            if(!empty($bpa_selected_service_duration)){
                                $service_duration_val_before = $bpa_selected_service_duration;
                            }
                        }
                        $service_duration_val_before = apply_filters('bookingpress_modify_service_duration_value_before',$service_duration_val_before, $bookingpress_selected_service,$bookingpress_appointment_data);                        
                        if( $service_duration_val_before > 1){
                            foreach($waiting_book_date as $waiting_date){
                                $all_date_in = true;
                                for( $d = 1; $d < $service_duration_val_before; $d++ ){ 
                                    $booked_day_plus = date( 'Y-m-d', strtotime( $waiting_date . '+' . $d . ' days' )); 
                                    if(!in_array($booked_day_plus,$waiting_book_date)){
                                        $remove_in_waiting_list_date[] = $waiting_date;                                        
                                        break; 
                                    }
                                }
                            }                        
                        }                                                
                    }
                    
                    
                    // $bookingpress_disable_date = $BookingPress->bookingpress_get_default_dayoff_dates('','',$bookingpress_selected_service,$bookingpress_selected_staffmember_id);				
                    // $bookingpress_disable_date = apply_filters('bookingpress_modify_disable_dates', $bookingpress_disable_date, $bookingpress_selected_service, $bookingpress_selected_date, $bookingpress_appointment_data);                
                    // $bookingpress_formated_disable_date = array();
                    // foreach($bookingpress_disable_date as $disdate){
                    //     $bookingpress_formated_disable_date[] = date('Y-m-d',strtotime($disdate));
                    // }
                    // if(empty($bookingpress_formated_disable_date)){
                    //     $bookingpress_formated_disable_date = array('none');
                    // }
                    

                    $all_booked_dates = array();                
                    $new_attributes = array();
                    $only_waiting_dates = array();
                    $bookingpress_site_date = date('Y-m-d', current_time( 'timestamp') );                                        
                    foreach($waiting_book_date as $datekey){
                        if(!in_array($datekey,$remove_in_waiting_list_date)){
                            $waiting_count = 0;
                            $waiting_date  = $datekey;                                                  
                            if(isset($multiple_days_event_waitng_count[$datekey])){
                                $waiting_count = $multiple_days_event_waitng_count[$datekey];
                            }
                            $disable_waiting_list = false;
                            if($waiting_count >= $bookingpress_waiting_list_max_slot){
                                $disable_waiting_list = true;
                            }
                            $waiting_count = sprintf("%02d", $waiting_count);
                            if(!$disable_waiting_list){
                                $only_waiting_dates[] = $waiting_date;
                            }                                                                
                            $all_waiting_dates[$datekey] = array('date' => $datekey, 'waiting_count' => $waiting_count,'disable_waiting_list' => $disable_waiting_list);    
                        }
                    }

                    // foreach($attributes as $datekey=>$atrval){
                    //     if(!in_array($datekey,$bookingpress_formated_disable_date) && strtotime($datekey) > strtotime($bookingpress_site_date) ){                        
                    //     //if(in_array($datekey,$waiting_book_date)){                                                    
                    //         $waiting_count = 0;
                    //         $checked_is_waiting_list = (int)$atrval;
                    //         if($checked_is_waiting_list == 0){
                    //             $waiting_date  = $datekey;                                                  
                    //             if(isset($multiple_days_event_waitng_count[$datekey])){
                    //                 $waiting_count = $multiple_days_event_waitng_count[$datekey];
                    //             }
                    //             $disable_waiting_list = false;
                    //             if($waiting_count >= $bookingpress_waiting_list_max_slot){
                    //                 $disable_waiting_list = true;
                    //             }
                    //             $waiting_count = sprintf("%02d", $waiting_count);
                    //             if(!$disable_waiting_list){
                    //                 $only_waiting_dates[] = $waiting_date;
                    //             }                                                                
                    //             $all_waiting_dates[$datekey] = array('date' => $datekey, 'waiting_count' => $waiting_count,'disable_waiting_list' => $disable_waiting_list);
                    //         }
                    //     }
                    // }                    

                }                   
            }
            $selected_waiting_date = (isset($bookingpress_appointment_data['selected_waiting_date']))?$bookingpress_appointment_data['selected_waiting_date']:'';
            if(!empty($selected_waiting_date)){
                $response['selected_date'] = $selected_waiting_date;   
            }
            $response['waiting_attributes'] = $all_waiting_dates;
            $response['only_waiting_dates'] = $only_waiting_dates; 
            $response['remove_in_waiting_list_date'] = $remove_in_waiting_list_date;            
            return $response;
        }
        
        /**
         * Function for add class in my bookings when waiting status
         *
         * @param  mixed $bookingpress_payment_status_class
         * @param  mixed $bookingpress_appointments_data
         * @return void
         */
        function bookingpress_modify_appointment_status_cls_fun($bookingpress_payment_status_class, $bookingpress_appointments_data){                        
            if($bookingpress_appointments_data['bookingpress_appointment_status'] == 7){
                $bookingpress_payment_status_class = '__bpa-is-waiting';
            }
            return $bookingpress_payment_status_class;
        }
        
        /**
         * Function for reset color default option add
         *
         * @return void
         */
        function bookingpress_reset_color_option_after_fun(){
        ?>
            vm.waiting_list_container_data.waiting_list_text_color = '#F5AE41';
        <?php
        }
        
        /**
         * Function for remove waiting appointment from dashboard
         *
         * @param  mixed $search_where
         * @return void
         */
        public function bookingpress_dashboard_upcoming_appointments_data_filter_func($search_where){
            $search_where.=' AND (bookingpress_appointment_status <> 7) ';
            return $search_where;
        }
        
        /**
         * Function for remove waiting appointment in report
         *
         * @param  mixed $appointments_search_query
         * @param  mixed $bookingpress_search_data
         * @return void
         */
        public function bookingpress_appointment_report_view_add_filter_fun($appointments_search_query, $bookingpress_search_data){
            global $BookingPress;            
            $appointments_search_query .= " AND (bookingpress_appointment_status <> 7) ";                      
            return $appointments_search_query;
        }
                
        /**
         * Function for remove waiting appointment in report
         *
         * @param  mixed $bookingpress_search_query_where
         * @param  mixed $bookingpress_search_data
         * @return void
         */
        public function bookingpress_appointment_view_add_filter_fun($bookingpress_search_query_where, $bookingpress_search_data){            
            if($bookingpress_search_data['is_waiting_list'] == 0){
                $bookingpress_search_query_where .= " AND (bookingpress_appointment_status <> 7) ";
            }
            return $bookingpress_search_query_where;
        }

        /**
         * Function For Add Waiting List Appointment Once Waiting List Payment Done
         * bookingpress_before_appointment_confirm_booking_func
         *
         * @param  mixed $is_waiting_booking
         * @param  mixed $entry_id
         * @param  mixed $payment_gateway_data
         * @param  mixed $payment_status
         * @param  mixed $transaction_id_field
         * @param  mixed $payment_amount_field
         * @param  mixed $is_front
         * @param  mixed $is_cart_order
         * @return void
        */
        public function bookingpress_before_appointment_confirm_booking_func($is_waiting_booking,$entry_id,$payment_gateway_data,$payment_status,$transaction_id_field,$payment_amount_field,$is_front,$is_cart_order){
            
            global $wpdb, $BookingPress, $tbl_bookingpress_entries, $tbl_bookingpress_customers, $bookingpress_email_notifications, $bookingpress_debug_payment_log_id, $bookingpress_customers, $bookingpress_coupons, $tbl_bookingpress_appointment_meta, $tbl_bookingpress_appointment_bookings, $bookingpress_other_debug_log_id, $tbl_bookingpress_payment_logs,$bookingpress_dashboard;
            $bookingpress_is_appointment_exists = (int)$wpdb->get_var($wpdb->prepare("SELECT bookingpress_appointment_booking_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d AND bookingpress_waiting_payment_token != ''", $entry_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
            if($bookingpress_is_appointment_exists > 0){

                $bookingpress_confirm_booking_received_data = array(
                    'entry_id' => $entry_id,
                    'payment_gateway_data' => wp_json_encode($payment_gateway_data),
                    'payment_status' => $payment_status,
                    'transaction_id_field' => $transaction_id_field,
                    'payment_amount_field' => $payment_amount_field,
                    'is_front' => $is_front,
                    'is_cart_order' => $is_cart_order,
                );                                
				$bookingpress_get_appointment_details = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_entry_id,bookingpress_appointment_booking_id, bookingpress_is_cart, bookingpress_order_id,bookingpress_customer_email FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d AND bookingpress_waiting_payment_token != ''", $entry_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
				$bookingpress_customer_email = !empty($bookingpress_get_appointment_details['bookingpress_customer_email']) ? ($bookingpress_get_appointment_details['bookingpress_customer_email']) : '';
				$bookingpress_is_cart = !empty($bookingpress_get_appointment_details['bookingpress_is_cart']) ? intval($bookingpress_get_appointment_details['bookingpress_is_cart']) : 0;
				$bookingpress_order_id = !empty($bookingpress_get_appointment_details['bookingpress_order_id']) ? intval($bookingpress_get_appointment_details['bookingpress_order_id']) : 0;
				$transaction_id = ( ! empty( $transaction_id_field ) && ! empty( $payment_gateway_data[ $transaction_id_field ] ) ) ? $payment_gateway_data[ $transaction_id_field ] : '';				
                

                $bookingpress_entry_id = !empty($bookingpress_get_appointment_details['bookingpress_entry_id']) ? intval($bookingpress_get_appointment_details['bookingpress_entry_id']) : 0;
                $entry_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_entries} WHERE bookingpress_entry_id = %d", $bookingpress_entry_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_entries is table name defined globally. False Positive alarm
                if ( ! empty( $entry_data ) ) {                

					$bookingpress_entry_user_id                  = $entry_data['bookingpress_customer_id'];
					$bookingpress_customer_name                  = $entry_data['bookingpress_customer_name'];
					$bookingpress_customer_phone                 = $entry_data['bookingpress_customer_phone'];
					$bookingpress_customer_firstname             = $entry_data['bookingpress_customer_firstname'];
					$bookingpress_customer_lastname              = $entry_data['bookingpress_customer_lastname'];
					$bookingpress_customer_country               = $entry_data['bookingpress_customer_country'];
					$bookingpress_customer_phone_dial_code       = $entry_data['bookingpress_customer_phone_dial_code'];
					$bookingpress_customer_email                 = $entry_data['bookingpress_customer_email'];
					$bookingpress_customer_timezone				 = $entry_data['bookingpress_customer_timezone'];
					$bookingpress_customer_dst_timezone			 = $entry_data['bookingpress_dst_timezone'];
					$bookingpress_service_id                     = $entry_data['bookingpress_service_id'];
					$bookingpress_service_name                   = $entry_data['bookingpress_service_name'];
					$bookingpress_service_price                  = $entry_data['bookingpress_service_price'];
					$bookingpress_service_currency               = $entry_data['bookingpress_service_currency'];
					$bookingpress_service_duration_val           = $entry_data['bookingpress_service_duration_val'];
					$bookingpress_service_duration_unit          = $entry_data['bookingpress_service_duration_unit'];
					$bookingpress_payment_gateway                = $entry_data['bookingpress_payment_gateway'];
					$bookingpress_appointment_date               = $entry_data['bookingpress_appointment_date'];
					$bookingpress_appointment_time               = $entry_data['bookingpress_appointment_time'];
					$bookingpress_appointment_end_time           = $entry_data['bookingpress_appointment_end_time'];
					$bookingpress_appointment_internal_note      = $entry_data['bookingpress_appointment_internal_note'];
					$bookingpress_appointment_send_notifications = $entry_data['bookingpress_appointment_send_notifications'];
					$bookingpress_appointment_status             = $entry_data['bookingpress_appointment_status'];
					$bookingpress_coupon_details                 = $entry_data['bookingpress_coupon_details'];
					$bookingpress_coupon_discounted_amount       = $entry_data['bookingpress_coupon_discount_amount'];
					$bookingpress_deposit_payment_details        = $entry_data['bookingpress_deposit_payment_details'];
					$bookingpress_deposit_amount                 = $entry_data['bookingpress_deposit_amount'];
					$bookingpress_selected_extra_members         = $entry_data['bookingpress_selected_extra_members'];
					$bookingpress_extra_service_details          = $entry_data['bookingpress_extra_service_details'];
					$bookingpress_staff_member_id                = $entry_data['bookingpress_staff_member_id'];
					$bookingpress_staff_member_price             = $entry_data['bookingpress_staff_member_price'];
					$bookingpress_staff_first_name               = $entry_data['bookingpress_staff_first_name'];
					$bookingpress_staff_last_name                = $entry_data['bookingpress_staff_last_name'];
					$bookingpress_staff_email_address            = $entry_data['bookingpress_staff_email_address'];
					$bookingpress_staff_member_details           = $entry_data['bookingpress_staff_member_details'];
					$bookingpress_paid_amount                    = $entry_data['bookingpress_paid_amount'];
					$bookingpress_due_amount                     = $entry_data['bookingpress_due_amount'];
					$bookingpress_total_amount                   = $entry_data['bookingpress_total_amount'];
					$bookingpress_tax_percentage                 = $entry_data['bookingpress_tax_percentage'];
					$bookingpress_tax_amount                     = $entry_data['bookingpress_tax_amount'];
					$bookingpress_price_display_setting          = $entry_data['bookingpress_price_display_setting'];
					$bookingpress_display_tax_order_summary      = $entry_data['bookingpress_display_tax_order_summary'];
					$bookingpress_included_tax_label             = $entry_data['bookingpress_included_tax_label'];                     
                    $bookingpress_is_cart                        = $entry_data['bookingpress_is_cart'];

                    $bookingpress_ap_status = 1;                    
                    $payment_gateway = $bookingpress_payment_gateway;
                    $bookingpress_ap_status = $BookingPress->bookingpress_get_settings('appointment_status', 'general_setting');
                    if (!empty($payment_gateway) && $payment_gateway == 'on-site' ) {
                        $bookingpress_ap_status = $BookingPress->bookingpress_get_settings('onsite_appointment_status', 'general_setting');
                    }								
                    $bookingpress_email_notification_type = '';
                    if ( $bookingpress_ap_status == '2' ) {
                        $bookingpress_email_notification_type = 'Appointment Pending';
                    } elseif ( $bookingpress_ap_status == '1' ) {
                        $bookingpress_email_notification_type = 'Appointment Approved';
                    } elseif ( $bookingpress_ap_status == '3' ) {
                        $bookingpress_email_notification_type = 'Appointment Canceled';
                    } elseif ( $bookingpress_ap_status == '4' ) {
                        $bookingpress_email_notification_type = 'Appointment Rejected';
                    }
					$payable_amount = ( ! empty( $payment_amount_field ) && ! empty( $payment_gateway_data[ $payment_amount_field ] ) ) ? $payment_gateway_data[ $payment_amount_field ] : $bookingpress_paid_amount;
                    $payer_email = ! empty( $payment_gateway_data['payer_email'] ) ? $payment_gateway_data['payer_email'] : $bookingpress_customer_email;
                    $bookingpress_customer_id = $bookingpress_wpuser_id = $bookingpress_is_customer_create = 0;
                    if($bookingpress_payment_gateway == "on-site"){
                        $payment_status =  2;
                    }
                    
                    $bookingpress_last_invoice_id = $BookingPress->bookingpress_get_settings( 'bookingpress_last_invoice_id', 'invoice_setting' );
                    $bookingpress_last_invoice_id++;
                    $BookingPress->bookingpress_update_settings( 'bookingpress_last_invoice_id', 'invoice_setting', $bookingpress_last_invoice_id );    
                    $bookingpress_last_invoice_id = apply_filters('bookingpress_modify_invoice_id_externally', $bookingpress_last_invoice_id);

                    $payment_log_data = array(
                        'bookingpress_invoice_id'              => $bookingpress_last_invoice_id,
                        'bookingpress_order_id'                => $entry_data['bookingpress_order_id'],
                        'bookingpress_is_cart'                 => $entry_data['bookingpress_is_cart'], 
                        'bookingpress_appointment_booking_ref' => (isset($bookingpress_get_appointment_details['bookingpress_appointment_booking_id']))?$bookingpress_get_appointment_details['bookingpress_appointment_booking_id']:0,
                        'bookingpress_customer_id'             => $bookingpress_customer_id,
                        'bookingpress_customer_name'           => $bookingpress_customer_name,     
                        'bookingpress_customer_firstname'      => $bookingpress_customer_firstname,
                        'bookingpress_customer_lastname'       => $bookingpress_customer_lastname,
                        'bookingpress_customer_phone'          => $bookingpress_customer_phone,
                        'bookingpress_customer_country'        => $bookingpress_customer_country,
                        'bookingpress_customer_phone_dial_code' => $bookingpress_customer_phone_dial_code,
                        'bookingpress_customer_email'          => $bookingpress_customer_email,
                        'bookingpress_service_id'              => $bookingpress_service_id,
                        'bookingpress_service_name'            => $bookingpress_service_name,
                        'bookingpress_service_price'           => $bookingpress_service_price,
                        'bookingpress_payment_currency'        => $bookingpress_service_currency,
                        'bookingpress_service_duration_val'    => $bookingpress_service_duration_val,
                        'bookingpress_service_duration_unit'   => $bookingpress_service_duration_unit,
                        'bookingpress_appointment_date'        => $bookingpress_appointment_date,
                        'bookingpress_appointment_start_time'  => $bookingpress_appointment_time,
                        'bookingpress_appointment_end_time'    => $bookingpress_appointment_end_time,
                        'bookingpress_payment_gateway'         => $bookingpress_payment_gateway,
                        'bookingpress_payer_email'             => $payer_email,
                        'bookingpress_transaction_id'          => $transaction_id,
                        'bookingpress_payment_date_time'       => current_time( 'mysql' ),
                        'bookingpress_payment_status'          => $payment_status,
                        'bookingpress_payment_amount'          => $payable_amount,
                        'bookingpress_payment_currency'        => $bookingpress_service_currency,
                        'bookingpress_payment_type'            => '',
                        'bookingpress_payment_response'        => '',
                        'bookingpress_additional_info'         => '',
                        'bookingpress_coupon_details'          => $bookingpress_coupon_details,
                        'bookingpress_coupon_discount_amount'  => $bookingpress_coupon_discounted_amount,
                        'bookingpress_tax_percentage'          => $bookingpress_tax_percentage,
                        'bookingpress_tax_amount'              => $bookingpress_tax_amount,
                        'bookingpress_price_display_setting'   => $bookingpress_price_display_setting,
                        'bookingpress_display_tax_order_summary' => $bookingpress_display_tax_order_summary,
                        'bookingpress_included_tax_label'      => $bookingpress_included_tax_label,
                        'bookingpress_deposit_payment_details' => $bookingpress_deposit_payment_details,
                        'bookingpress_deposit_amount'          => $bookingpress_deposit_amount,
                        'bookingpress_staff_member_id'         => $bookingpress_staff_member_id,
                        'bookingpress_staff_member_price'      => $bookingpress_staff_member_price,
                        'bookingpress_staff_first_name'        => $bookingpress_staff_first_name,
                        'bookingpress_staff_last_name'         => $bookingpress_staff_last_name,
                        'bookingpress_staff_email_address'     => $bookingpress_staff_email_address,
                        'bookingpress_staff_member_details'    => $bookingpress_staff_member_details,
                        'bookingpress_paid_amount'             => $bookingpress_paid_amount,
                        'bookingpress_due_amount'              => $bookingpress_due_amount,
                        'bookingpress_total_amount'            => $bookingpress_total_amount,
                        'bookingpress_created_at'              => current_time( 'mysql' ),
                    );

                    /* Condition add if payment done with deposit then payment status consider as '4' */
                    //----------------------------------------------
                    $bookingpress_deposit_payment_details_data = json_decode($bookingpress_deposit_payment_details, TRUE);
                    if(!empty($bookingpress_deposit_payment_details_data)){
                        $payment_log_data['bookingpress_payment_status'] = 4;
                        $payment_log_data['bookingpress_mark_as_paid'] = 0;
                    }
                    //----------------------------------------------                         
                    $payment_log_data = apply_filters( 'bookingpress_modify_payment_log_fields_before_insert', $payment_log_data, $entry_data );

                    do_action( 'bookingpress_payment_log_entry', $bookingpress_payment_gateway, 'before insert payment', 'bookingpress pro', $payment_log_data, $bookingpress_debug_payment_log_id );
                    $payment_log_id = $BookingPress->bookingpress_insert_payment_logs( $payment_log_data );					
                    if(isset($entry_data['bookingpress_waiting_payment_token'])){
                        $entry_data['bookingpress_waiting_payment_token'] = '';
                    }                    
                    $bookingpress_appointment_booking_fields = apply_filters( 'bookingpress_modify_appointment_booking_fields_before_insert', $bookingpress_appointment_booking_fields, $entry_data );
                    $bookingpress_appointment_booking_fields = array(						
						'bookingpress_coupon_details'                => $bookingpress_coupon_details,
						'bookingpress_coupon_discount_amount'        => $bookingpress_coupon_discounted_amount,
						'bookingpress_tax_percentage'                => $bookingpress_tax_percentage,
						'bookingpress_tax_amount'                    => $bookingpress_tax_amount,
						'bookingpress_deposit_payment_details'       => $bookingpress_deposit_payment_details,
						'bookingpress_deposit_amount'                => $bookingpress_deposit_amount,
						'bookingpress_paid_amount'                   => $bookingpress_paid_amount,
						'bookingpress_due_amount'                    => $bookingpress_due_amount,
						'bookingpress_total_amount'                  => $bookingpress_total_amount,
                        'bookingpress_waiting_payment_token'         => '',
                        'bookingpress_appointment_status'            => $bookingpress_ap_status,
                        'bookingpress_payment_id'                    => $payment_log_id
					);                    
                    
                }

				if($bookingpress_is_cart){

                    $wpdb->update($tbl_bookingpress_entries, 
                        array('bookingpress_waiting_payment_token'=>''), 
                        array('bookingpress_entry_id' => $bookingpress_entry_id)
                    );  
                    $bookingpress_appointment_data_update = $bookingpress_appointment_booking_fields;
					$wpdb->update($tbl_bookingpress_appointment_bookings, $bookingpress_appointment_data_update , array('bookingpress_order_id' => $bookingpress_order_id) );						
					$bookingpress_inserted_appointment_ids = $wpdb->get_results($wpdb->prepare("SELECT bookingpress_appointment_booking_id,bookingpress_customer_email FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_order_id = %d", $bookingpress_order_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
					foreach($bookingpress_inserted_appointment_ids as $k2 => $v2){
						$entry_id = $v2['bookingpress_appointment_booking_id'];
						$bookingpress_customer_email = !empty($v2['bookingpress_customer_email']) ? $v2['bookingpress_customer_email'] : '';
						do_action('bookingpress_after_change_appointment_status', $entry_id, $bookingpress_ap_status);
						$bookingpress_email_notifications->bookingpress_send_after_payment_log_entry_email_notification( $bookingpress_email_notification_type, $entry_id,$bookingpress_customer_email );
					}
				}else{  
                    $wpdb->update($tbl_bookingpress_entries, 
                        array('bookingpress_waiting_payment_token'=>''), 
                        array('bookingpress_entry_id' => $bookingpress_entry_id)
                    );                                      
                    $bookingpress_appointment_data_update = $bookingpress_appointment_booking_fields;
					$wpdb->update($tbl_bookingpress_appointment_bookings, $bookingpress_appointment_data_update, array('bookingpress_appointment_booking_id' => $entry_id) );					
					do_action('bookingpress_after_change_appointment_status', $entry_id, $bookingpress_ap_status);
					$bookingpress_email_notifications->bookingpress_send_after_payment_log_entry_email_notification( $bookingpress_email_notification_type, $entry_id, $bookingpress_customer_email );
				}
                $is_waiting_booking = true;

                //$bookingpress_get_appointment_details['bookingpress_appointment_booking_id']
                do_action('after_waiting_appoitment_approve',$bookingpress_get_appointment_details['bookingpress_appointment_booking_id']);

            }else{
                if($is_cart_order){
                    $entry_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_entry_id FROM {$tbl_bookingpress_entries} WHERE bookingpress_waiting_payment_token != '' AND bookingpress_order_id = %d", $entry_id ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_entries is table name defined globally. False Positive alarm
                }else{
                    $entry_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_entry_id FROM {$tbl_bookingpress_entries} WHERE bookingpress_waiting_payment_token != '' AND bookingpress_entry_id = %d", $entry_id ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_entries is table name defined globally. False Positive alarm
                }                
                if(!empty($entry_data)){                                        
                    $bookingpress_is_appointment_exists = $wpdb->get_var($wpdb->prepare("SELECT bookingpress_appointment_booking_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_entry_id = %d ", $entry_data['bookingpress_entry_id']));// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                    if(!$bookingpress_is_appointment_exists){
                        $this->bookingpress_confirm_booking($entry_id, array(), '2', '', '', 1, $is_cart_order);
                        $is_waiting_booking = true;
                    }
                }
            }
            return $is_waiting_booking;
        }
		
		/**
		 *  Function for generate waiting payment url
         *  bookingpress_generate_waiting_payment_url
		 *
		 * @param  mixed $appointment_id
		 * @param  mixed $payment_unique_token
		 * @return void
		 */
		function bookingpress_generate_waiting_payment_url($appointment_id, $payment_unique_token){
			global $BookingPress;
			$bookingpress_generated_complete_payment_url = "";
			if( !empty($appointment_id) && !empty($payment_unique_token) ){
				$bookingpress_page_id = $BookingPress->bookingpress_get_settings('waiting_list_payment_page_id','general_setting');
				if(!empty($bookingpress_page_id)){
					$bookingpress_generated_complete_payment_url = get_permalink($bookingpress_page_id);
					$bookingpress_generated_complete_payment_url = add_query_arg('bkp_pay', $payment_unique_token, $bookingpress_generated_complete_payment_url);
				}
			}
			return $bookingpress_generated_complete_payment_url;
		}
		
		/**
		 * Function for add waiting payment url in email body variable.
         * bookingpress_modify_email_content_func
		 *
		 * @param  mixed $template_content
		 * @param  mixed $bookingpress_appointment_data
		 * @param  mixed $notification_name
		 * @return void
		 */
		function bookingpress_modify_email_content_func($template_content, $bookingpress_appointment_data, $notification_name = '',$template_type=''){

			global $BookingPress;
			$bookingpress_complete_payment_url = "";
			$bookingpress_appointment_id = intval($bookingpress_appointment_data['bookingpress_appointment_booking_id']);
			$bookingpress_payment_uniq_token = !empty($bookingpress_appointment_data['bookingpress_waiting_payment_token']) ? $bookingpress_appointment_data['bookingpress_waiting_payment_token'] : '';
			if(!empty($bookingpress_payment_uniq_token)){
				$bookingpress_complete_payment_url = $this->bookingpress_generate_waiting_payment_url($bookingpress_appointment_id, $bookingpress_payment_uniq_token);
			}
			$template_content = str_replace('%waitinglist_complete_payment_url%', $bookingpress_complete_payment_url, $template_content);
			return $template_content;
		}
        
        /**
         * Function for waiting payment form submit
         * bookingpress_final_waiting_payment_func
         *
         * @return void
         */
        public function bookingpress_final_waiting_payment_func(){

            global $tbl_bookingpress_entries,$tbl_bookingpress_payment_logs,$tbl_bookingpress_appointment_bookings,$wpdb, $BookingPress, $bookingpress_pro_payment, $tbl_bookingpress_payment_logs, $bookingpress_pro_payment_gateways,$bookingpress_deposit_payment;
            $response              = array();
			$wpnonce               = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
			$bpa_verify_nonce_flag = wp_verify_nonce( $wpnonce, 'bpa_wp_nonce' );
			if ( ! $bpa_verify_nonce_flag ) {
				$response['variant'] = 'error';
				$response['title']   = esc_html__( 'Error', 'bookingpress-waiting-list' );
				$response['msg']     = esc_html__( 'Sorry, Your request can not be processed due to security reason.', 'bookingpress-waiting-list' );
				echo wp_json_encode( $response );
				die();
			}
            $response['variant'] = '';
            $response['title']   = esc_html__( 'Error', 'bookingpress-waiting-list' );
            $response['msg'] = esc_html__('Something went wrong while completing the payment', 'bookingpress-waiting-list');

            if( !empty( $_POST['waiting_payment_data'] ) && !is_array( $_POST['waiting_payment_data'] ) ){
                $_POST['waiting_payment_data'] = json_decode( stripslashes_deep( $_POST['waiting_payment_data'] ), true );  // phpcs:ignore
                $_POST['waiting_payment_data'] =  !empty($_POST['waiting_payment_data']) ? array_map(array($this,'bookingpress_boolean_type_cast'), $_POST['waiting_payment_data'] ) : array(); // phpcs:ignore		
            }
            $bookingpress_final_payment_data = !empty($_POST['waiting_payment_data']) ? $_POST['waiting_payment_data'] : ''; // phpcs:ignore
            
            if(!empty($bookingpress_final_payment_data['appointment_id'])){

                $bookingpress_appointment_id = intval($bookingpress_final_payment_data['appointment_id']);
                $bookingpress_payment_id = intval($bookingpress_final_payment_data['payment_id']);
                $response = $this->bookingpress_check_timeslot_is_avaliable_or_not($bookingpress_appointment_id);

                if( $response['variant'] == 'error'){
                    echo wp_json_encode( $response );
                    die();    
                }
                if(!empty($bookingpress_appointment_id)){
                                        
                    $bookingpress_entry_data = array();
                    $bookingpress_get_appointment_record = $wpdb->get_row($wpdb->prepare( "SELECT bookingpress_entry_id,bookingpress_service_name FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d",$bookingpress_appointment_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                    
                    $bookingpress_service_name = "";
                    $bookingpress_is_cart = !empty($bookingpress_get_appointment_record['bookingpress_is_cart']) ? 1 : 0;
                    if(!$bookingpress_is_cart){
                        $bookingpress_service_name = $bookingpress_get_appointment_record['bookingpress_service_name'];
                    }
                    $bookingpress_final_payable_amount = $bookingpress_final_payment_data['total_payable_amount'];
                    $bookingpress_applied_coupon_code = !empty($bookingpress_final_payment_data['coupon_code']) ? $bookingpress_final_payment_data['coupon_code'] : '';
                    $bookingpress_is_new_coupon_apply = 0;
                    if(!empty($bookingpress_applied_coupon_code)){
                        $bookingpress_is_new_coupon_apply = 1;
                        $bookingpress_final_payable_amount = $bookingpress_final_payment_data['total_payable_amount'];
                    }
                    $bookingpress_deposit_payment_details = '';
                    $bookingpress_deposit_amount = '';
                    $bookingpress_final_payable_amount = number_format($bookingpress_final_payable_amount, 2);
                    $bookingpress_final_payable_amount = str_replace(',', '', $bookingpress_final_payable_amount);
                    $bookingpress_final_payable_amount = floatval($bookingpress_final_payable_amount);
                    $payment_gateway = $bookingpress_final_payment_data['selected_payment_method'];
                    $bookingpress_notify_url   = BOOKINGPRESS_HOME_URL . '/?bookingpress-listener=bpa_pro_' . $payment_gateway . '_url';
                    $bookingpress_currency_name   = $BookingPress->bookingpress_get_settings( 'payment_default_currency', 'payment_setting' );
                    $bookingpress_currency_code = $BookingPress->bookingpress_get_currency_code( $bookingpress_currency_name );
                    $bookingpress_after_canceled_payment_page_id = $BookingPress->bookingpress_get_customize_settings( 'after_failed_payment_redirection', 'booking_form' );
				    $bookingpress_after_canceled_payment_url     = get_permalink( $bookingpress_after_canceled_payment_page_id );
                    $bpa_waiting_payment_page_id = $BookingPress->bookingpress_get_settings('waiting_list_payment_page_id', 'general_setting');
                    $bookingpress_waiting_payment_page_url = get_permalink( $bpa_waiting_payment_page_id );
                    $bookingpress_waiting_payment_page_url = add_query_arg('bpa_complete_payment', 1, $bookingpress_waiting_payment_page_url);
                    $bookingpress_deposit_details = array();
                    if(isset($bookingpress_final_payment_data['bookingpress_deposit_amt_without_currency']) && $bookingpress_final_payment_data['bookingpress_deposit_amt_without_currency']){
                        $bookingpress_deposit_amt_without_currency = (float)$bookingpress_final_payment_data['bookingpress_deposit_amt_without_currency'];
                        if($bookingpress_deposit_amt_without_currency){                            
                            $bookingpress_deposit_payment_method = (isset($bookingpress_final_payment_data['bookingpress_deposit_payment_method']))?$bookingpress_final_payment_data['bookingpress_deposit_payment_method']:'';
                            if($bookingpress_deposit_payment->bookingpress_check_deposit_payment_module_activation() && !empty($bookingpress_deposit_payment_method) && ($bookingpress_deposit_payment_method == "deposit_or_full_price") ){

                                $bookingpress_deposit_due_amt_without_currency = !empty($bookingpress_final_payment_data['bookingpress_deposit_due_amt_without_currency']) ? $bookingpress_final_payment_data['bookingpress_deposit_due_amt_without_currency'] : 0;
                                $bookingpress_deposit_selected_type = !empty($bookingpress_final_payment_data['deposit_payment_type']) ? $bookingpress_final_payment_data['deposit_payment_type'] : '';
                                $bookingpress_deposit_selected_amount = $bookingpress_deposit_amt_without_currency;
                                $$bookingpress_due_amount_tmp = $bookingpress_deposit_due_amt_without_currency;                                  
                                $bookingpress_deposit_due_amt_without_currency = $bookingpress_final_payment_data['bookingpress_deposit_due_amt_without_currency'];
                                $bookingpress_deposit_details = array(
                                    'deposit_selected_type' => $bookingpress_deposit_selected_type,
                                    'deposit_amount' => $bookingpress_deposit_selected_amount,
                                    'deposit_due_amount' => $bookingpress_deposit_due_amt_without_currency,
                                );
                                $bookingpress_entry_data['bookingpress_deposit_payment_details'] = wp_json_encode($bookingpress_deposit_details);
                                $bookingpress_entry_data['bookingpress_deposit_amount'] = $bookingpress_deposit_selected_amount;
                                $bookingpress_entry_data['bookingpress_due_amount'] = $bookingpress_deposit_due_amt_without_currency;                                

                            }
                        }
                    }                    
                    if(isset($bookingpress_final_payment_data['applied_coupon_res']) && !empty($bookingpress_final_payment_data['applied_coupon_res'])){

						$bookingpress_applied_coupon_details = array(
							'coupon_status' => $bookingpress_final_payment_data['applied_coupon_res']['coupon_status'],
							'msg' => $bookingpress_final_payment_data['applied_coupon_res']['msg'],
							'coupon_data' => $bookingpress_final_payment_data['applied_coupon_res']['coupon_data'],
						);            
                        $bookingpress_entry_data['bookingpress_coupon_details'] = wp_json_encode($bookingpress_applied_coupon_details);
                        $bookingpress_entry_data['bookingpress_coupon_discount_amount'] = $bookingpress_final_payment_data['coupon_discount_amount'];

                    }           

                    $bookingpress_appointment_bookings_data['bookingpress_paid_amount']  = $bookingpress_final_payable_amount;
                    $bookingpress_appointment_bookings_data['bookingpress_total_amount'] = $bookingpress_final_payable_amount;
                    $bookingpress_entry_data['bookingpress_paid_amount'] = $bookingpress_final_payable_amount;
                    $bookingpress_entry_data['bookingpress_total_amount'] = $bookingpress_final_payable_amount;                    
                    $bookingpress_entry_data['bookingpress_payment_gateway'] = $bookingpress_final_payment_data['selected_payment_method'];
                    
                    $bookingpress_entry_data = apply_filters( 'bookingpress_modify_entry_data_before_insert', $bookingpress_entry_data, $bookingpress_final_payment_data );
                                        
                    $bpa_complete_payment_page_id = $BookingPress->bookingpress_get_settings('waiting_list_payment_page_id', 'general_setting');
                    $bookingpress_complete_payment_page_url = get_permalink( $bpa_complete_payment_page_id );
                    $bookingpress_complete_payment_page_url = add_query_arg('bpa_complete_payment', 1, $bookingpress_complete_payment_page_url);

                    $bookingpress_final_payment_request_data = array(
                        'service_data' => array(
                            'bookingpress_service_name' => $bookingpress_service_name
                        ),
                        'payable_amount' => floatval($bookingpress_final_payment_data['total_payable_amount']),
                        'customer_details' => array(
                            'customer_firstname' => $bookingpress_final_payment_data['form_fields']['customer_firstname'],
                            'customer_lastname' => $bookingpress_final_payment_data['form_fields']['customer_lastname'],
                            'customer_email' => $bookingpress_final_payment_data['form_fields']['customer_email'],
                            'customer_username' => $bookingpress_final_payment_data['form_fields']['customer_email'],
                        ),
                        'currency' => $bookingpress_currency_name,
                        'currency_code' => $bookingpress_currency_code,
                        'card_details' => array(
                            'card_holder_name' => $bookingpress_final_payment_data['card_holder_name'],
                            'card_number' => $bookingpress_final_payment_data['card_number'],
                            'expire_month' => $bookingpress_final_payment_data['expire_month'],
                            'expire_year' => $bookingpress_final_payment_data['expire_year'],
                            'cvv' => $bookingpress_final_payment_data['cvv'],
                        ),
                        'entry_id' => $bookingpress_appointment_id,
                        'booking_form_redirection_mode' => '',
                        'approved_appointment_url' => $bookingpress_complete_payment_page_url,
                        'canceled_appointment_url' => $bookingpress_after_canceled_payment_url,
                        'pending_appointment_url' => $bookingpress_complete_payment_page_url,
                        'notify_url' => $bookingpress_notify_url,
                        'recurring_details' => '',
                    );
                    
                    $bookingpress_entry_id = $bookingpress_get_appointment_record['bookingpress_entry_id'];                                                                        
                    $wpdb->update($tbl_bookingpress_entries, 
                        $bookingpress_entry_data, 
                        array('bookingpress_entry_id' => $bookingpress_entry_id)
                    );  

                    if($bookingpress_final_payable_amount == 0){
                        $bookingpress_payment_gateway_data = array('bookingpress_payment_gateway' => $payment_gateway);
                        $bookingpress_pro_payment_gateways->bookingpress_confirm_booking($bookingpress_appointment_id, $bookingpress_payment_gateway_data, '1', '', '', 1);
                        $wpdb->update($tbl_bookingpress_payment_logs, array('bookingpress_payment_gateway' => $payment_gateway), array('bookingpress_payment_log_id' => $bookingpress_payment_id));
                        $bookingpress_success_payment_message = $BookingPress->bookingpress_get_customize_settings('waiting_payment_success_message','booking_form');
                        $response['title']   = esc_html__( 'Success', 'bookingpress-waiting-list' );
                        $response['msg'] = $bookingpress_success_payment_message;
                    }else{
                        if( 'manual' == $payment_gateway || '' == $payment_gateway ){
                            $no_payment_method_is_selected_for_the_booking = $BookingPress->bookingpress_get_settings('no_payment_method_is_selected_for_the_booking', 'message_setting');
                            $response['variant'] = 'error';
                            $response['title']   = esc_html__( 'Error', 'bookingpress-waiting-list' );
                            $response['msg'] = $no_payment_method_is_selected_for_the_booking;
                        } else {
                            if($payment_gateway != 'on-site'){
                                $response = apply_filters( 'bookingpress_' . $payment_gateway . '_submit_form_data', $response, $bookingpress_final_payment_request_data );                                
                                if(!empty($response['variant']) && $response['variant'] != 'error' ){
                                    $bookingpress_appointment_status = $BookingPress->bookingpress_get_settings('appointment_status', 'general_setting');                                    
                                    $bookingpress_appointment_bookings_data['bookingpress_appointment_status'] = $bookingpress_appointment_status;
                                    if(!empty( $bookingpress_get_appointment_record )){                                        
                              
                                    }                                    
                                    $bookingpress_success_payment_message = $BookingPress->bookingpress_get_customize_settings('waiting_payment_success_message','booking_form');                                                                                                                                               
                                    $response['title']   = esc_html__( 'Success', 'bookingpress-waiting-list' );
                                    $response['msg'] = $bookingpress_success_payment_message;
                                }
                            }else{
                                if($payment_gateway == 'on-site'){                                    
                                    $bookingpress_success_payment_message = $BookingPress->bookingpress_get_customize_settings('waiting_payment_success_message','booking_form');
                                    $booked_appointment_details = $wpdb->get_row($wpdb->prepare('SELECT bookingpress_is_cart FROM ' . $tbl_bookingpress_appointment_bookings . ' WHERE bookingpress_appointment_booking_id = %d', $bookingpress_appointment_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                                    $bookingpress_is_cart = (isset($booked_appointment_details->bookingpress_is_cart))?$booked_appointment_details->bookingpress_is_cart:0;                                                                        
                                    $bookingpress_appointment_status = $BookingPress->bookingpress_get_settings('onsite_appointment_status', 'general_setting');                                    
                                    if(!empty( $bookingpress_get_appointment_record )){                                        
                                        $bookingpress_entry_id = $bookingpress_get_appointment_record['bookingpress_entry_id'];                                                                        
                                        $wpdb->update($tbl_bookingpress_entries, 
                                            $bookingpress_entry_data, 
                                            array('bookingpress_entry_id' => $bookingpress_entry_id)
                                        );                                
                                    }
                                    $bookingpress_pro_payment_gateways->bookingpress_confirm_booking($bookingpress_appointment_id, array(), '2', '', '', 1, $bookingpress_is_cart);
                                    $response['variant'] = 'success';
                                    $response['title'] = esc_html__( 'Success', 'bookingpress-waiting-list' );
                                    $response['msg'] = $bookingpress_success_payment_message;
                                }
                            }
                        }
                    }
                }
            }            
            echo wp_json_encode($response);
            die;
        }
        
        /**
         * Function for add waiting list customize css
         * bookingpress_customize_css_content_modify_fun
         *
         * @param  mixed $bookingpress_customize_css_content
         * @param  mixed $bookingpress_custom_data_arr
         * @return void
         */
        public function bookingpress_customize_css_content_modify_fun($bookingpress_customize_css_content,$bookingpress_custom_data_arr){            
            global $BookingPress;            
            $waiting_list_text_color = $BookingPress->bookingpress_get_customize_settings('waiting_list_text_color','booking_form');            
            if(empty($waiting_list_text_color)){
                $waiting_list_text_color = '#F5AE41';         
            }
			$hex               = $waiting_list_text_color;
			list($r, $g, $b)   = sscanf($hex, '#%02x%02x%02x');
			$box_shadow_color  = "0 4px 8px rgba($r,$g,$b,0.06), 0 8px 16px rgba($r,$g,$b,0.16)";
            $bookingpress_customize_css_content.='
                :root {
                --bpa-pt-secondary-orange: '.$waiting_list_text_color.';                
                --bpa-pt-secondary-orange-alpha-08: rgba('.$r.','.$g.','.$b.',0.06);                
                --bpa-cl-white: #ffffff;                
                }  
                .bpa-front--dt__calendar.bpa-front-v-cal__is-only-days .vc-day .bpa-multi__day-waiting-select{
                    background-color: rgba('.$r.','.$g.','.$b.',0.15) !important;
                    border-color: '.$waiting_list_text_color.' !important; 
                }              
                .bpa-front--dt__time-slots .bpa-front--dt__ts-body .bpa-front--dt__ts-body--row .bpa-front--dt__ts-body--items .bpa-front--dt__ts-body--item span.bpa-front__waiting-counter{
                  color: '.$waiting_list_text_color.' !important;                
                }                
                .bpa-front--dt__time-slots .bpa-front--dt__ts-body .bpa-front--dt__ts-body--row .bpa-front--dt__ts-body--items .bpa-front-bi__waiting:hover,                
                .bpa-front--dt__time-slots .bpa-front--dt__ts-body .bpa-front--dt__ts-body--row .bpa-front--dt__ts-body--items .bpa-front-bi__waiting.__bpa-is-selected{                
                  background-color: rgba('.$r.','.$g.','.$b.',0.06) !important;                
                  border-color: '.$waiting_list_text_color.' !important;                  
                }                
                .bpa-front-tabs .bpa-front--dt__calendar .vc-day .vc-day-content.bpa-front-bi__waiting .bpa-front-dt__day-slot-label{                
                    color: '.$waiting_list_text_color.' !important;                
                }                
                .bpa-front-tabs .bpa-front--dt__calendar .vc-day .vc-highlights + .vc-day-content.bpa-front-bi__waiting{                
                    color: var(--bpa-cl-white) !important;                
                 }
            ';
            return $bookingpress_customize_css_content;
        }
        
        /**
         * Function for add waiting position label in cart
         * bookingpress_add_cart_waiting_list_summary_fun
         *
         * @return void
        */
        public function bookingpress_add_cart_waiting_list_summary_fun(){
            global $BookingPress;
            $waiting_position_label = $BookingPress->bookingpress_get_customize_settings('waiting_position_label','booking_form');               
        ?>        
        <div v-if="(typeof appointment_step_form_data.is_waiting_list  != 'undefined' && appointment_step_form_data.is_waiting_list && appointment_step_form_data.waiting_number_disp)" class="bpa-front-bs__waiting-note">
            <svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.8219 9.79737C12.8219 7.93766 11.3066 6.41423 9.44687 6.41018C7.5831 6.40613 6.05968 7.93361 6.06373 9.80143C6.06778 11.6692 7.575 13.1765 9.44282 13.1765C11.3066 13.1765 12.8219 11.6611 12.8219 9.79737ZM9.05791 9.29902C9.05791 9.06807 9.05791 8.84118 9.05791 8.61024C9.06196 8.34283 9.21187 8.1686 9.43472 8.16455C9.66566 8.1605 9.82368 8.33877 9.82368 8.61429C9.82368 8.97894 9.82773 9.33954 9.81962 9.70419C9.81962 9.79737 9.84799 9.84599 9.92497 9.89056C10.1478 10.0162 10.3707 10.1377 10.5894 10.2714C10.8123 10.4011 10.8771 10.6158 10.7596 10.8103C10.6462 11.0048 10.4314 11.0534 10.2045 10.9318C9.89661 10.7576 9.58868 10.5712 9.2767 10.4011C9.11463 10.3119 9.04576 10.1742 9.04576 9.99185C9.05791 9.75686 9.05791 9.52591 9.05791 9.29902Z" />
                <path d="M5.40721 10.2043H3.81891C3.64986 10.2043 3.51226 10.0667 3.51226 9.89769C3.51226 9.72864 3.64986 9.59104 3.81891 9.59104H5.39935C5.4308 9.06029 5.56054 8.55314 5.77283 8.09316H3.81891C3.64986 8.09316 3.51226 7.95556 3.51226 7.78651C3.51226 7.61746 3.64986 7.47986 3.81891 7.47986H6.11487C6.63382 6.71716 7.39652 6.13138 8.28895 5.84438H3.81891C3.64986 5.84438 3.51226 5.70678 3.51226 5.53773C3.51226 5.36868 3.64986 5.23108 3.81891 5.23108H8.84328C9.01233 5.23108 9.14994 5.36868 9.14994 5.53773C9.14994 5.58098 9.13814 5.62422 9.12241 5.66354C9.27574 5.64781 9.42907 5.63995 9.58632 5.63995C9.79469 5.63995 10.0031 5.65567 10.2036 5.68712V2.10166C10.2036 1.30358 9.5588 0.658821 8.76072 0.658821H2.43112C1.63304 0.658821 0.988281 1.30358 0.988281 2.10166V11.7297C0.988281 12.5278 1.63304 13.1726 2.43112 13.1726H7.04662C6.1306 12.4806 5.51336 11.4152 5.40721 10.2043ZM3.81891 3.06879H8.84328C9.01233 3.06879 9.14994 3.20639 9.14994 3.37544C9.14994 3.54449 9.01233 3.68209 8.84328 3.68209H3.81891C3.64986 3.68209 3.51226 3.54449 3.51226 3.37544C3.51226 3.20639 3.64986 3.06879 3.81891 3.06879ZM2.4036 10.2594C2.20309 10.2594 2.04191 10.0982 2.04191 9.89769C2.04191 9.69719 2.20309 9.536 2.4036 9.536C2.6041 9.536 2.76529 9.69719 2.76529 9.89769C2.76529 10.0982 2.6041 10.2594 2.4036 10.2594ZM2.4036 8.1482C2.20309 8.1482 2.04191 7.98701 2.04191 7.78651C2.04191 7.58601 2.20309 7.42482 2.4036 7.42482C2.6041 7.42482 2.76529 7.58601 2.76529 7.78651C2.76529 7.98701 2.6041 8.1482 2.4036 8.1482ZM2.4036 5.89942C2.20309 5.89942 2.04191 5.73823 2.04191 5.53773C2.04191 5.33723 2.20309 5.17604 2.4036 5.17604C2.6041 5.17604 2.76529 5.33723 2.76529 5.53773C2.76529 5.73823 2.6041 5.89942 2.4036 5.89942ZM2.4036 3.73713C2.20309 3.73713 2.04191 3.57594 2.04191 3.37544C2.04191 3.17494 2.20309 3.01375 2.4036 3.01375C2.6041 3.01375 2.76529 3.17494 2.76529 3.37544C2.76529 3.57594 2.6041 3.73713 2.4036 3.73713Z" />
            </svg>
            <div class="bpa-front-wn--label"><?php  echo esc_html($waiting_position_label); ?> ({{appointment_step_form_data.waiting_number_disp}})</div>
        </div>
        <?php
        }
        
        /**
         * Function for add color setting in customize
         * bookingpress_customize_color_setting_after_fun
         *
         * @return void
        */
        public function bookingpress_customize_color_setting_after_fun(){            
        ?>    
        <div class="bpa-tp__body-item">    
            <label class="bpa-form-label"><?php esc_html_e('Waiting List Color', 'bookingpress-waiting-list'); ?></label>
            <el-color-picker class="bpa-customize-tp__color-picker" v-model="waiting_list_container_data.waiting_list_text_color"></el-color-picker>
        </div>
        <?php    
        }

                
        /**
         * Function for add front css
         * set_front_css
         *
         * @return void
        */
        function set_front_css(){                
            global $BookingPress;
            wp_register_style( 'bookingpress_waiting_list_front_css', BOOKINGPRESS_WAITING_LIST_URL . '/css/bookingpress_waiting_list_front.css', array(), BOOKINGPRESS_WAITING_LIST_VERSION );
            if ( $BookingPress->bookingpress_is_front_page() ) {
                wp_enqueue_style( 'bookingpress_waiting_list_front_css' );
            }
        }
        
        /**
         * Function for add extra message content in date & time tab in booking form
         * bookingpress_add_content_after_date_time_summary_fun
         *
         * @return void
         */
        public function bookingpress_add_content_after_date_time_summary_fun(){
            global $BookingPress;
            $waiting_position_label = $BookingPress->bookingpress_get_customize_settings('waiting_position_label','booking_form');            
        ?>
            <div v-if="(typeof appointment_step_form_data.is_waiting_list  != 'undefined' && appointment_step_form_data.is_waiting_list && appointment_step_form_data.waiting_number_disp)" class="bpa-front-bs__waiting-note">
                <svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.8219 9.79737C12.8219 7.93766 11.3066 6.41423 9.44687 6.41018C7.5831 6.40613 6.05968 7.93361 6.06373 9.80143C6.06778 11.6692 7.575 13.1765 9.44282 13.1765C11.3066 13.1765 12.8219 11.6611 12.8219 9.79737ZM9.05791 9.29902C9.05791 9.06807 9.05791 8.84118 9.05791 8.61024C9.06196 8.34283 9.21187 8.1686 9.43472 8.16455C9.66566 8.1605 9.82368 8.33877 9.82368 8.61429C9.82368 8.97894 9.82773 9.33954 9.81962 9.70419C9.81962 9.79737 9.84799 9.84599 9.92497 9.89056C10.1478 10.0162 10.3707 10.1377 10.5894 10.2714C10.8123 10.4011 10.8771 10.6158 10.7596 10.8103C10.6462 11.0048 10.4314 11.0534 10.2045 10.9318C9.89661 10.7576 9.58868 10.5712 9.2767 10.4011C9.11463 10.3119 9.04576 10.1742 9.04576 9.99185C9.05791 9.75686 9.05791 9.52591 9.05791 9.29902Z" />
                    <path d="M5.40721 10.2043H3.81891C3.64986 10.2043 3.51226 10.0667 3.51226 9.89769C3.51226 9.72864 3.64986 9.59104 3.81891 9.59104H5.39935C5.4308 9.06029 5.56054 8.55314 5.77283 8.09316H3.81891C3.64986 8.09316 3.51226 7.95556 3.51226 7.78651C3.51226 7.61746 3.64986 7.47986 3.81891 7.47986H6.11487C6.63382 6.71716 7.39652 6.13138 8.28895 5.84438H3.81891C3.64986 5.84438 3.51226 5.70678 3.51226 5.53773C3.51226 5.36868 3.64986 5.23108 3.81891 5.23108H8.84328C9.01233 5.23108 9.14994 5.36868 9.14994 5.53773C9.14994 5.58098 9.13814 5.62422 9.12241 5.66354C9.27574 5.64781 9.42907 5.63995 9.58632 5.63995C9.79469 5.63995 10.0031 5.65567 10.2036 5.68712V2.10166C10.2036 1.30358 9.5588 0.658821 8.76072 0.658821H2.43112C1.63304 0.658821 0.988281 1.30358 0.988281 2.10166V11.7297C0.988281 12.5278 1.63304 13.1726 2.43112 13.1726H7.04662C6.1306 12.4806 5.51336 11.4152 5.40721 10.2043ZM3.81891 3.06879H8.84328C9.01233 3.06879 9.14994 3.20639 9.14994 3.37544C9.14994 3.54449 9.01233 3.68209 8.84328 3.68209H3.81891C3.64986 3.68209 3.51226 3.54449 3.51226 3.37544C3.51226 3.20639 3.64986 3.06879 3.81891 3.06879ZM2.4036 10.2594C2.20309 10.2594 2.04191 10.0982 2.04191 9.89769C2.04191 9.69719 2.20309 9.536 2.4036 9.536C2.6041 9.536 2.76529 9.69719 2.76529 9.89769C2.76529 10.0982 2.6041 10.2594 2.4036 10.2594ZM2.4036 8.1482C2.20309 8.1482 2.04191 7.98701 2.04191 7.78651C2.04191 7.58601 2.20309 7.42482 2.4036 7.42482C2.6041 7.42482 2.76529 7.58601 2.76529 7.78651C2.76529 7.98701 2.6041 8.1482 2.4036 8.1482ZM2.4036 5.89942C2.20309 5.89942 2.04191 5.73823 2.04191 5.53773C2.04191 5.33723 2.20309 5.17604 2.4036 5.17604C2.6041 5.17604 2.76529 5.33723 2.76529 5.53773C2.76529 5.73823 2.6041 5.89942 2.4036 5.89942ZM2.4036 3.73713C2.20309 3.73713 2.04191 3.57594 2.04191 3.37544C2.04191 3.17494 2.20309 3.01375 2.4036 3.01375C2.6041 3.01375 2.76529 3.17494 2.76529 3.37544C2.76529 3.57594 2.6041 3.73713 2.4036 3.73713Z" />
                </svg>
                <div class="bpa-front-wn--label"><?php  echo esc_html($waiting_position_label); ?> ({{appointment_step_form_data.waiting_number_disp}})</div>
            </div>            
        <?php
        }
        
        /**
         * Function for approve waiting appointment in admin appointment tab
         * bookingpress_approve_waiting_appointment_fun
         *
         * @return void
        */
        public function bookingpress_approve_waiting_appointment_fun(){
            global $wpdb,$BookingPress,$bookingpress_dashboard,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_entries;
            $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : '';
            $wpnonce               = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
            $bpa_verify_nonce_flag = wp_verify_nonce($wpnonce, 'bpa_wp_nonce');
            $response = array();
            $response['variant']  = 'error';
            $response['title']    = esc_html__('Error', 'bookingpress-waiting-list');
            $response['msg']      = esc_html__('Sorry, Your request can not be processed due to security reason.', 'bookingpress-waiting-list');
            if(!$bpa_verify_nonce_flag){

                $response['formdata'] = '';
                $response['variant']  = 'error';
                $response['title']    = esc_html__('Error', 'bookingpress-waiting-list');
                $response['msg']      = esc_html__('Sorry, Your request can not be processed due to security reason.', 'bookingpress-waiting-list');
                echo wp_json_encode($response);
                exit;
            } 
            if($appointment_id){

                $response = $this->bookingpress_check_timeslot_is_avaliable_or_not($appointment_id);
                if($response['variant'] == 'success'){

                    $response['title'] = esc_html__('Success', 'bookingpress-waiting-list');
                    $response['msg'] = esc_html__('The Appointment successfully booked from waiting list.', 'bookingpress-waiting-list');                     
                    //Admin Appointment Approve Functionlity
                    $booked_appointment_details = $wpdb->get_row($wpdb->prepare('SELECT bookingpress_entry_id,bookingpress_customer_id,bookingpress_payment_id FROM ' . $tbl_bookingpress_appointment_bookings . ' WHERE bookingpress_appointment_booking_id = %d', $appointment_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

                    

                    $bookingpress_booked_appointment_customer_id = $booked_appointment_details['bookingpress_customer_id'];
                    $bookingpress_payment_id = $booked_appointment_details['bookingpress_payment_id'];
                    $bookingpress_customer_data = $BookingPress->get_customer_details($bookingpress_booked_appointment_customer_id);
                    $customer_email = ! empty($bookingpress_customer_data['bookingpress_user_email']) ? $bookingpress_customer_data['bookingpress_user_email'] : '';
                    $appointment_update_data = array(
                        'bookingpress_appointment_status' => 1,
                    );
                    $appointment_where_condition = array(
                        'bookingpress_appointment_booking_id' => $appointment_id,
                    );
                    $wpdb->update($tbl_bookingpress_appointment_bookings, $appointment_update_data, $appointment_where_condition);                    
                    if( wp_next_scheduled ( 'bookingpress_send_email_for_change_approved_status', array( 'Appointment Approved', $appointment_id, $customer_email ) ) ){
                        wp_clear_scheduled_hook('bookingpress_send_email_for_change_approved_status', array( 'Appointment Approved', $appointment_id, $customer_email ) );
                    }
                    if($bookingpress_payment_id == 0){

                        $bookingpress_entry_id = $booked_appointment_details['bookingpress_entry_id'];
                        $entry_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_entries} WHERE bookingpress_entry_id = %d", $bookingpress_entry_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_entries is table name defined globally. False Positive alarm                                                                      
                        if ( ! empty( $entry_data ) ) {
                        
                            $bookingpress_entry_user_id                  = $entry_data['bookingpress_customer_id'];
                            $bookingpress_customer_name                  = $entry_data['bookingpress_customer_name'];
                            $bookingpress_customer_phone                 = $entry_data['bookingpress_customer_phone'];
                            $bookingpress_customer_firstname             = $entry_data['bookingpress_customer_firstname'];
                            $bookingpress_customer_lastname              = $entry_data['bookingpress_customer_lastname'];
                            $bookingpress_customer_country               = $entry_data['bookingpress_customer_country'];
                            $bookingpress_customer_phone_dial_code       = $entry_data['bookingpress_customer_phone_dial_code'];
                            $bookingpress_customer_email                 = $entry_data['bookingpress_customer_email'];
                            $bookingpress_customer_timezone				 = $entry_data['bookingpress_customer_timezone'];
                            $bookingpress_customer_dst_timezone			 = $entry_data['bookingpress_dst_timezone'];
                            $bookingpress_service_id                     = $entry_data['bookingpress_service_id'];
                            $bookingpress_service_name                   = $entry_data['bookingpress_service_name'];
                            $bookingpress_service_price                  = $entry_data['bookingpress_service_price'];
                            $bookingpress_service_currency               = $entry_data['bookingpress_service_currency'];
                            $bookingpress_service_duration_val           = $entry_data['bookingpress_service_duration_val'];
                            $bookingpress_service_duration_unit          = $entry_data['bookingpress_service_duration_unit'];
                            $bookingpress_payment_gateway                = $entry_data['bookingpress_payment_gateway'];
                            $bookingpress_appointment_date               = $entry_data['bookingpress_appointment_date'];
                            $bookingpress_appointment_time               = $entry_data['bookingpress_appointment_time'];
                            $bookingpress_appointment_end_time           = $entry_data['bookingpress_appointment_end_time'];
                            $bookingpress_appointment_internal_note      = $entry_data['bookingpress_appointment_internal_note'];
                            $bookingpress_appointment_send_notifications = $entry_data['bookingpress_appointment_send_notifications'];
                            $bookingpress_appointment_status             = $entry_data['bookingpress_appointment_status'];
                            $bookingpress_coupon_details                 = $entry_data['bookingpress_coupon_details'];
                            $bookingpress_coupon_discounted_amount       = $entry_data['bookingpress_coupon_discount_amount'];
                            $bookingpress_deposit_payment_details        = $entry_data['bookingpress_deposit_payment_details'];
                            $bookingpress_deposit_amount                 = $entry_data['bookingpress_deposit_amount'];
                            $bookingpress_selected_extra_members         = $entry_data['bookingpress_selected_extra_members'];
                            $bookingpress_extra_service_details          = $entry_data['bookingpress_extra_service_details'];
                            $bookingpress_staff_member_id                = $entry_data['bookingpress_staff_member_id'];
                            $bookingpress_staff_member_price             = $entry_data['bookingpress_staff_member_price'];
                            $bookingpress_staff_first_name               = $entry_data['bookingpress_staff_first_name'];
                            $bookingpress_staff_last_name                = $entry_data['bookingpress_staff_last_name'];
                            $bookingpress_staff_email_address            = $entry_data['bookingpress_staff_email_address'];
                            $bookingpress_staff_member_details           = $entry_data['bookingpress_staff_member_details'];
                            $bookingpress_paid_amount                    = $entry_data['bookingpress_paid_amount'];
                            $bookingpress_due_amount                     = $entry_data['bookingpress_due_amount'];
                            $bookingpress_total_amount                   = $entry_data['bookingpress_total_amount'];
                            $bookingpress_tax_percentage                 = $entry_data['bookingpress_tax_percentage'];
                            $bookingpress_tax_amount                     = $entry_data['bookingpress_tax_amount'];
                            $bookingpress_price_display_setting          = $entry_data['bookingpress_price_display_setting'];
                            $bookingpress_display_tax_order_summary      = $entry_data['bookingpress_display_tax_order_summary'];
                            $bookingpress_included_tax_label             = $entry_data['bookingpress_included_tax_label'];                     
                            $bookingpress_is_cart                        = $entry_data['bookingpress_is_cart'];    
                        
                            $bookingpress_last_invoice_id = $BookingPress->bookingpress_get_settings( 'bookingpress_last_invoice_id', 'invoice_setting' );
                            $bookingpress_last_invoice_id++;
                            $BookingPress->bookingpress_update_settings( 'bookingpress_last_invoice_id', 'invoice_setting', $bookingpress_last_invoice_id );    
                            $bookingpress_last_invoice_id = apply_filters('bookingpress_modify_invoice_id_externally', $bookingpress_last_invoice_id);

                            $payment_log_data = array(
                                        'bookingpress_invoice_id'              => $bookingpress_last_invoice_id,
                                        'bookingpress_appointment_booking_ref' => $appointment_id,
                                        'bookingpress_customer_id'             => $bookingpress_entry_user_id,
                                        'bookingpress_customer_name'           => $bookingpress_customer_name,     
                                        'bookingpress_customer_firstname'      => $bookingpress_customer_firstname,
                                        'bookingpress_customer_lastname'       => $bookingpress_customer_lastname,
                                        'bookingpress_customer_phone'          => $bookingpress_customer_phone,
                                        'bookingpress_customer_country'        => $bookingpress_customer_country,
                                        'bookingpress_customer_phone_dial_code' => $bookingpress_customer_phone_dial_code,
                                        'bookingpress_customer_email'          => $bookingpress_customer_email,
                                        'bookingpress_service_id'              => $bookingpress_service_id,
                                        'bookingpress_service_name'            => $bookingpress_service_name,
                                        'bookingpress_service_price'           => $bookingpress_service_price,
                                        'bookingpress_payment_currency'        => $bookingpress_service_currency,
                                        'bookingpress_service_duration_val'    => $bookingpress_service_duration_val,
                                        'bookingpress_service_duration_unit'   => $bookingpress_service_duration_unit,
                                        'bookingpress_appointment_date'        => $bookingpress_appointment_date,
                                        'bookingpress_appointment_start_time'  => $bookingpress_appointment_time,
                                        'bookingpress_appointment_end_time'    => $bookingpress_appointment_end_time,
                                        'bookingpress_payment_gateway'         => 'on-site',
                                        'bookingpress_payer_email'             => $bookingpress_customer_email,
                                        'bookingpress_transaction_id'          => '',
                                        'bookingpress_payment_date_time'       => current_time( 'mysql' ),
                                        'bookingpress_payment_status'          => 2,
                                        'bookingpress_payment_amount'          => $bookingpress_total_amount,
                                        'bookingpress_payment_currency'        => $bookingpress_service_currency,
                                        'bookingpress_payment_type'            => '',
                                        'bookingpress_payment_response'        => '',
                                        'bookingpress_additional_info'         => '',
                                        'bookingpress_coupon_details'          => $bookingpress_coupon_details,
                                        'bookingpress_coupon_discount_amount'  => $bookingpress_coupon_discounted_amount,
                                        'bookingpress_tax_percentage'          => $bookingpress_tax_percentage,
                                        'bookingpress_tax_amount'              => $bookingpress_tax_amount,
                                        'bookingpress_price_display_setting'   => $bookingpress_price_display_setting,
                                        'bookingpress_display_tax_order_summary' => $bookingpress_display_tax_order_summary,
                                        'bookingpress_included_tax_label'      => $bookingpress_included_tax_label,
                                        'bookingpress_deposit_payment_details' => $bookingpress_deposit_payment_details,
                                        'bookingpress_deposit_amount'          => $bookingpress_deposit_amount,
                                        'bookingpress_staff_member_id'         => $bookingpress_staff_member_id,
                                        'bookingpress_staff_member_price'      => $bookingpress_staff_member_price,
                                        'bookingpress_staff_first_name'        => $bookingpress_staff_first_name,
                                        'bookingpress_staff_last_name'         => $bookingpress_staff_last_name,
                                        'bookingpress_staff_email_address'     => $bookingpress_staff_email_address,
                                        'bookingpress_staff_member_details'    => $bookingpress_staff_member_details,
                                        'bookingpress_paid_amount'             => $bookingpress_paid_amount,
                                        'bookingpress_due_amount'              => $bookingpress_due_amount,
                                        'bookingpress_total_amount'            => $bookingpress_total_amount,
                                        'bookingpress_created_at'              => current_time( 'mysql' ),
                                        'bookingpress_payment_status'          => 2,
                        
                            );    
                        
                        }
                        
                        $payment_log_data = apply_filters( 'bookingpress_modify_payment_log_fields_before_insert', $payment_log_data, $entry_data );

                        $payment_log_id = $BookingPress->bookingpress_insert_payment_logs( $payment_log_data );
                        
                        if($payment_log_id){
                            $wpdb->update($tbl_bookingpress_appointment_bookings, array('bookingpress_payment_id'=>$payment_log_id), array('bookingpress_appointment_booking_id' => $appointment_id) );                            
                        }                    
                        
                        //over                        
                        do_action('bookingpress_after_change_appointment_status', $appointment_id, 1);
                    }
                    
                    do_action('after_waiting_appoitment_approve',$appointment_id);
                    //Added payment record when approve records

                    
                }else{
                    $response['title']    = esc_html__('Error', 'bookingpress-waiting-list');
                    $response['msg']      = esc_html__('The Appointment is not available for book.', 'bookingpress-waiting-list');                    
                    $bookingpress_dashboard->bookingpress_change_upcoming_appointment_status( $appointment_id, 1 );

                }
                wp_send_json($response);
                die;

            }

        }
        
        /**
         * Function for add css in admin 
         * set_css
         *
         * @return void
         */
        function set_css(){            
            global $bookingpress_slugs;            
            wp_register_style('bookingpress_waiting_list_admin_css',BOOKINGPRESS_WAITING_LIST_URL . '/css/bookingpress_waiting_list_admin.css',array(),BOOKINGPRESS_WAITING_LIST_VERSION);
            if ( isset( $_REQUEST['page'] ) &&  $_REQUEST['page'] == 'bookingpress_appointments' ) {                
                wp_enqueue_style( 'bookingpress_waiting_list_admin_css' );
            }
        }
		
		/**
		* This function is used to get the appointment form field data for waiting list
		 *
		 * @param  mixed $bookingpress_appointment_id
		 * @return void
		 */
		function bookingpress_get_appointment_form_field_data($bookingpress_appointment_id) {
            global $wpdb,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_appointment_meta;

            $bookingpress_appointment_form_fields = array();
            if(!empty($bookingpress_appointment_id)) {
                $bookingpress_appointment_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE `bookingpress_appointment_booking_id` = %d ", $bookingpress_appointment_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_bookings is a table name. false alarm.
                
                if(!empty($bookingpress_appointment_data)) {
                    if(!empty($bookingpress_appointment_data['bookingpress_is_cart']) && $bookingpress_appointment_data['bookingpress_is_cart'] == 1 ) {
                        $bookingpress_order_id = !empty($bookingpress_appointment_data['bookingpress_order_id']) ? intval($bookingpress_appointment_data['bookingpress_order_id']) : 0;
                        
                        if(!empty($bookingpress_order_id)) {
                            $bookingpress_appointment_meta_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_appointment_meta_value,bookingpress_appointment_meta_key FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_order_id = %d AND bookingpress_appointment_meta_key = %s ORDER BY bookingpress_appointment_meta_created_date DESC", $bookingpress_order_id,'appointment_details' ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_meta is a table name. false alarm.
                        }
                        $bookingpress_appointment_meta_data = !empty($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value']) ? json_decode($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value'],true) : array();                       
                        $bookingpress_appointment_form_fields = !empty($bookingpress_appointment_meta_data['form_fields']) ? stripslashes_deep($bookingpress_appointment_meta_data['form_fields']) : array();
                        
                    } else {                            
                        $bookingpress_appointment_meta_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_appointment_meta_value,bookingpress_appointment_meta_key FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_appointment_id = %d AND bookingpress_appointment_meta_key = %s ORDER BY bookingpress_appointment_meta_created_date DESC", $bookingpress_appointment_id,'appointment_form_fields_data' ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_meta is a table name. false alarm.
                        $bookingpress_appointment_meta_data = !empty($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value']) ? json_decode($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value'],true) : array();                                      
                        $bookingpress_appointment_form_fields = !empty($bookingpress_appointment_meta_data['form_fields']) ? stripslashes_deep($bookingpress_appointment_meta_data['form_fields']) : array();
                    }
                }                
            }
			return $bookingpress_appointment_form_fields;
        }
		
		/**
		 * Function for get appointment data for waiting list appointment for appointment tab in admin 
         * bookingpress_modify_appointment_data_func
		 *
		 * @param  mixed $bookingpress_appointment_data
		 * @return void
		 */
		function bookingpress_modify_appointment_data_func($bookingpress_appointment_data){
			
			global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_appointment_bookings, $bookingpress_pro_staff_members, $bookingpress_global_options, $tbl_bookingpress_payment_logs, $tbl_bookingpress_form_fields;
			$bookingpress_global_data = $bookingpress_global_options->bookingpress_global_options();
			$default_date_format = $bookingpress_global_data['wp_default_date_format'];           
			$default_time_format = $bookingpress_global_data['wp_default_time_format'];

			if(!empty($bookingpress_appointment_data) && is_array($bookingpress_appointment_data) ){
				foreach($bookingpress_appointment_data as $k => $v){

                    if($v['appointment_status'] != 7){                        
                        break;
                    }
					$bookingpress_appointment_id = $v['appointment_id'];
					$bookingpress_payment_log_id = $v['payment_id'];

                    $bookingpress_get_appointment_record = $wpdb->get_row($wpdb->prepare( "
                    SELECT bookingpress_selected_extra_members,bookingpress_tax_amount,bookingpress_paid_amount,bookingpress_included_tax_label,bookingpress_total_amount,bookingpress_is_cart,bookingpress_extra_service_details FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d",$bookingpress_appointment_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

					$bookingpress_currency_name = (isset($v['bookingpress_service_currency']))?$v['bookingpress_service_currency']:'';
					$bookingpress_selected_currency = $BookingPress->bookingpress_get_currency_symbol($bookingpress_currency_name);
					
					if(!empty($bookingpress_appointment_data)){

                        $bookingpress_extra_total = 0;
                        $bookingpress_extra_service_details = !empty($bookingpress_get_appointment_record['bookingpress_extra_service_details']) ? json_decode($bookingpress_get_appointment_record['bookingpress_extra_service_details'], TRUE) : array();
                        $bookingpress_extra_service_data = array();
                        if(!empty($bookingpress_extra_service_details)){                           
                            foreach($bookingpress_extra_service_details as $k3 => $v3){
                                $bookingpress_extra_total = $bookingpress_extra_total + $v3['bookingpress_final_payable_price'];
                                $bookingpress_extra_service_price = ($v3['bookingpress_extra_service_details']['bookingpress_extra_service_price']) * ($v3['bookingpress_selected_qty']);
                                $bookingpress_extra_service_data[] = array(
                                    'selected_qty' => $v3['bookingpress_selected_qty'],
                                    'extra_name' => $v3['bookingpress_extra_service_details']['bookingpress_extra_service_name'],
                                    'extra_service_duration' => $v3['bookingpress_extra_service_details']['bookingpress_extra_service_duration']." ".$v3['bookingpress_extra_service_details']['bookingpress_extra_service_duration_unit'],
                                    'extra_service_price' => $bookingpress_extra_service_price,
                                    'extra_service_price_with_currency' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_extra_service_price, $bookingpress_selected_currency),
                                );
                            }                                                        
                        }    
                        $bookingpress_appointment_data[$k]['bookingpress_extra_service_data'] = $bookingpress_extra_service_data;
                        $appointment_id = $v['appointment_id'];
						$bookingpress_appointment_data[$k]['is_rescheduled'] = 0;
						$bookingpress_appointment_data[$k]['payment_method'] = '-';
						$bookingpress_appointment_data[$k]['payment_method_label'] = ' - ';
						
						$bookingpress_appointment_data[$k]['bookingpress_payment_status'] = '';

						$bookingpress_appointment_data[$k]['bookingpress_subtotal_amt'] = '';
						$bookingpress_appointment_data[$k]['bookingpress_subtotal_amt_with_currency'] = '';

						$bookingpress_appointment_data[$k]['bookingpress_deposit_amt'] = '';
						$bookingpress_appointment_data[$k]['bookingpress_deposit_amt_with_currency'] = '';

						$bookingpress_appointment_data[$k]['bookingpress_tax_amt'] = $bookingpress_get_appointment_record['bookingpress_tax_amount'];
						$bookingpress_appointment_data[$k]['bookingpress_tax_amt_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_get_appointment_record['bookingpress_tax_amount'], $bookingpress_selected_currency);

						$bookingpress_appointment_data[$k]['appointment_payment'] = $bookingpress_get_appointment_record['bookingpress_paid_amount'];						

						$bookingpress_appointment_data[$k]['price_display_setting'] = '';
						$bookingpress_appointment_data[$k]['display_tax_amount_in_order_summary'] = '';
						$bookingpress_appointment_data[$k]['included_tax_label'] = $bookingpress_get_appointment_record['bookingpress_included_tax_label'];

                        $bookingpress_appointment_data[$k]['bookingpress_selected_extra_members'] = $bookingpress_get_appointment_record['bookingpress_selected_extra_members'];

						$bookingpress_appointment_data[$k]['bookingpress_applied_coupon_code'] = '';
						$bookingpress_appointment_data[$k]['bookingpress_coupon_discount_amt'] = '';
						$bookingpress_appointment_data[$k]['bookingpress_coupon_discount_amt_with_currency'] = '';

                        //$BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_extra_service_price, $bookingpress_selected_currency)

						$bookingpress_appointment_data[$k]['bookingpress_final_total_amt'] = $bookingpress_get_appointment_record['bookingpress_total_amount'];
						$bookingpress_appointment_data[$k]['bookingpress_final_total_amt_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_get_appointment_record['bookingpress_total_amount'], $bookingpress_selected_currency);
						$bookingpress_appointment_data[$k]['bookingpress_is_cart'] = $bookingpress_get_appointment_record['bookingpress_is_cart'];
						$bookingpress_appointment_data[$k]['bookingpress_is_deposit_enable'] = 0;


						$bookingpress_appointment_data[$k]['bookingpress_staff_firstname'] = !empty($bookingpress_get_appointment_record['bookingpress_staff_first_name']) ? $bookingpress_get_appointment_record['bookingpress_staff_first_name'] : '';
						$bookingpress_appointment_data[$k]['bookingpress_staff_lastname'] = !empty($bookingpress_get_appointment_record['bookingpress_staff_last_name']) ? $bookingpress_get_appointment_record['bookingpress_staff_last_name']: '';
						$bookingpress_appointment_data[$k]['bookingpress_staff_email_address'] = !empty($bookingpress_get_appointment_record['bookingpress_staff_email_address']) ? $bookingpress_get_appointment_record['bookingpress_staff_email_address'] : '';                        

                        
                        $bookingpress_appointment_data[$k]  = apply_filters('bookingpress_waiting_list_appointment_data', $bookingpress_appointment_data[$k], $v);

						//Get custom fields value
						$bookingpress_meta_value = $this->bookingpress_get_appointment_form_field_data($bookingpress_appointment_id);
                        $bookingpress_meta_value = apply_filters('bookingpress_removed_repeater_data_in_custom_fields', $bookingpress_meta_value, $bookingpress_appointment_id);
                        
                        $bookingpress_appointment_data[$k]['bookingpress_guest_data'] = apply_filters('bookingpress_get_appointment_guest_data', $bookingpress_appointment_id);
                        
						$bookingpress_appointment_custom_meta_values = array();
						if(!empty($bookingpress_meta_value)){
							foreach($bookingpress_meta_value as $k4 => $v4) {

								$bookingpress_form_field_data= $wpdb->get_row($wpdb->prepare("SELECT bookingpress_field_label,bookingpress_field_type,bookingpress_field_options,bookingpress_field_values FROM {$tbl_bookingpress_form_fields} WHERE bookingpress_field_meta_key = %s AND bookingpress_field_type != %s AND bookingpress_field_type != %s AND bookingpress_field_type != %s", $k4, '2_col', '3_col', '4_col'), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_form_fields is table name.								
								
								$bookingpress_field_label = !empty($bookingpress_form_field_data['bookingpress_field_label']) ? stripslashes_deep($bookingpress_form_field_data['bookingpress_field_label']) : '';
								if(!empty($bookingpress_field_label)){
									$bookingpress_field_type = $bookingpress_form_field_data['bookingpress_field_type'];
									if( !empty($bookingpress_field_type) && 'checkbox' == $bookingpress_field_type ){
										$bookingpress_appointment_custom_meta_values[] = array('label' => $bookingpress_field_label, 'value' => is_array($v4) ? implode(',', $v4) : '' );
									} elseif(!empty($bookingpress_field_type) && !empty($v4) && 'date' == $bookingpress_field_type ) {
										$bookingpress_field_options = json_decode($bookingpress_form_field_data['bookingpress_field_options'],true);
										if(!empty($bookingpress_field_options['enable_timepicker']) && $bookingpress_field_options['enable_timepicker'] == 'true') {
											$default_date_time_format = $default_date_format.' '.$default_time_format;
											$bookingpress_appointment_custom_meta_values[] = array('label' => $bookingpress_field_label, 'value' => date($default_date_time_format,strtotime($v4)));
										} else {
											$bookingpress_appointment_custom_meta_values[] = array('label' => $bookingpress_field_label, 'value' => date($default_date_format,strtotime($v4)));
										}
									} else if( !empty( $bookingpress_field_type ) && 'file' == $bookingpress_field_type ) {
										$file_name_data = explode( '/', $v4 );
										$file_name = end( $file_name_data );
										
										$bookingpress_appointment_custom_meta_values[] = array(
											'label' => $bookingpress_field_label,
											'value' => '<a href="' . esc_url( $v4 ) . '" target="_blank">'.$file_name.'</a>'
										);
									} else {
										$bookingpress_appointment_custom_meta_values[] = array('label' => $bookingpress_field_label, 'value' => $v4);
									}
								}
							}
						}
						$bookingpress_appointment_data[$k]['custom_fields_values'] = $bookingpress_appointment_custom_meta_values;						
					}
				}
			}
			return $bookingpress_appointment_data;
		}


        
        /**
         * Function for get waiting appointment list for admin appointment tab
         *
         * @param  mixed $bookingpress_query_data
         * @return void
         */
        public function bookingpress_get_waiting_appointments_fun($bookingpress_query_data){

            global $BookingPress,$wpdb, $tbl_bookingpress_services,$tbl_bookingpress_appointment_bookings,
            $tbl_bookingpress_payment_logs,$tbl_bookingpress_customers,$bookingpress_global_options,$tbl_bookingpress_form_fields;

            $response              = array();
            $bpa_check_authorization = $this->bpa_check_authentication( 'retrieve_appointments', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-waiting-list');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-waiting-list');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $perpage     = isset($_POST['perpage']) ? intval($_POST['perpage']) : 10; // phpcs:ignore WordPress.Security.NonceVerification
            $currentpage = isset($_POST['currentpage']) ? intval($_POST['currentpage']) : 1; // phpcs:ignore WordPress.Security.NonceVerification
            $offset      = ( ! empty($currentpage) && $currentpage > 1 ) ? ( ( $currentpage - 1 ) * $perpage ) : 0;
            $bookingpress_search_data        = ! empty($_REQUEST['search_data']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['search_data']) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST['search_data'] contains array and sanitized properly using appointment_sanatize_field function
            $bookingpress_search_query       = '';
            $bookingpress_search_query_where = 'WHERE 1=1 ';

            $perpage     = isset($_POST['perpage']) ? intval($_POST['perpage']) : 10;  // phpcs:ignore
            $currentpage = isset($_POST['currentpage']) ? intval($_POST['currentpage']) : 1;  // phpcs:ignore
            $offset      = ( ! empty($currentpage) && $currentpage > 1 ) ? ( ( $currentpage - 1 ) * $perpage ) : 0;
            $bookingpress_search_data        = ! empty($_REQUEST['search_data']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['search_data']) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST['search_data'] contains array and sanitized properly using appointment_sanatize_field function
            $bookingpress_search_query       = '';
            $bookingpress_search_query_where = 'WHERE 1=1 ';            
            if($bookingpress_search_data['is_waiting_list'] == 1){

                if (! empty($bookingpress_search_data['search_appointment']) ) {
                    $bookingpress_search_string = $bookingpress_search_data['search_appointment'];
                    $bookingpress_search_result = $wpdb->get_results($wpdb->prepare('SELECT bookingpress_customer_id  FROM ' . $tbl_bookingpress_customers . " WHERE bookingpress_user_firstname LIKE %s OR bookingpress_user_lastname LIKE %s OR bookingpress_user_login LIKE %s AND (bookingpress_user_type = 1 OR bookingpress_user_type = 2)", '%' . $bookingpress_search_string . '%', '%' . $bookingpress_search_string . '%', '%' . $bookingpress_search_string . '%'), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_customers is table name defined globally. False Positive alarm
                    if (! empty($bookingpress_search_result) ) {
                        $bookingpress_customer_ids = array();
                        foreach ( $bookingpress_search_result as $item ) {
                            $bookingpress_customer_ids[] = $item['bookingpress_customer_id'];
                        }
                        $bookingpress_search_user_id      = implode(',', $bookingpress_customer_ids);
                        $bookingpress_search_query_where .= "AND (bookingpress_customer_id IN ({$bookingpress_search_user_id}))";
                    } else {
                        $bookingpress_search_query_where .= "AND (bookingpress_service_name LIKE '%{$bookingpress_search_string}%')";
                    }
                }
                if (! empty($bookingpress_search_data['selected_date_range']) ) {
                    $bookingpress_search_date         = $bookingpress_search_data['selected_date_range'];
                    $start_date                       = date('Y-m-d', strtotime($bookingpress_search_date[0]));
                    $end_date                         = date('Y-m-d', strtotime($bookingpress_search_date[1]));
                    $bookingpress_search_query_where .= "AND (bookingpress_appointment_date BETWEEN '{$start_date}' AND '{$end_date}')";
                }
                if (! empty($bookingpress_search_data['customer_name']) ) {
                    $bookingpress_search_name         = $bookingpress_search_data['customer_name'];
                    $bookingpress_search_customer_id  = implode(',', $bookingpress_search_name);
                    $bookingpress_search_query_where .= "AND (bookingpress_customer_id IN ({$bookingpress_search_customer_id}))";
                }
                if (! empty($bookingpress_search_data['service_name']) ) {
                    $bookingpress_search_name         = $bookingpress_search_data['service_name'];
                    $bookingpress_search_service_id   = implode(',', $bookingpress_search_name);
                    $bookingpress_search_query_where .= "AND (bookingpress_service_id IN ({$bookingpress_search_service_id}))";
                }
                if (! empty($bookingpress_search_data['appointment_status'] && $bookingpress_search_data['appointment_status'] != 'all') ) {
                    $bookingpress_search_name         = $bookingpress_search_data['appointment_status'];
                    $bookingpress_search_query_where .= "AND (bookingpress_appointment_status = '{$bookingpress_search_name}')";
                }
                $bookingpress_search_query_where = apply_filters('bookingpress_appointment_view_add_filter', $bookingpress_search_query_where, $bookingpress_search_data);               
                $bookingpress_search_query_where.= ' AND bookingpress_appointment_status = 7 ';
                $get_total_appointments = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query}{$bookingpress_search_query_where} ", ARRAY_A);  // phpcs:ignore
                $total_appointments = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query} {$bookingpress_search_query_where} order by bookingpress_appointment_date DESC,bookingpress_appointment_time,bookingpress_service_id,bookingpress_staff_member_id,bookingpress_appointment_end_time DESC LIMIT {$offset} , {$perpage}", ARRAY_A); //phpcs:ignore                  
                $appointments  = $bookingpress_formdata = array();

                if(!empty($total_appointments)){
                    $counter = 1;
                    $bookingpress_global_options_arr       = $bookingpress_global_options->bookingpress_global_options();
                    $bookingpress_default_date_format = $bookingpress_global_options_arr['wp_default_date_format'];
                    $bookingpress_default_time_format = $bookingpress_global_options_arr['wp_default_time_format'];
                    $bookingpress_default_date_time_format = $bookingpress_default_date_format . ' ' . $bookingpress_default_time_format;
                    $bookingpress_appointment_status_arr = $bookingpress_global_options_arr['appointment_status'];                    
                    $bookingpress_form_field_data = $wpdb->get_results("SELECT `bookingpress_form_field_name`,`bookingpress_field_label` FROM {$tbl_bookingpress_form_fields}",ARRAY_A); //phpcs:ignore 
                    foreach($bookingpress_form_field_data as $key=> $value) {                    
                        $bookingpress_formdata[$value['bookingpress_form_field_name']] = stripslashes_deep($value['bookingpress_field_label']);
                    }                
                    $unique_data_old = '';
                    $unique_class = 'bpa-is-waiting-list__even';
                    foreach ( $total_appointments as $get_appointment ) {

                        $appointment = array();                        
                        $appointment['id']  = $counter;
                        $appointment_id  = intval($get_appointment['bookingpress_appointment_booking_id']);

                        $bookingpress_selected_extra_members  = intval($get_appointment['bookingpress_selected_extra_members']);

                        $appointment['is_avaliable_status'] = 0;
                        $unique_data = $get_appointment['bookingpress_staff_member_id'].'-'.$get_appointment['bookingpress_service_id'].'-'.$get_appointment['bookingpress_appointment_date'].'-'.$get_appointment['bookingpress_appointment_time'].'-'.$get_appointment['bookingpress_appointment_end_time'];                        
                        $bookingpress_timeslot_avaliable_count = $this->bookingpress_check_timeslot_is_avaliable_or_not($appointment_id,true);
                        if($unique_data != $unique_data_old){
                            $unique_data_old = $unique_data;                            
                            if($unique_class == 'bpa-is-waiting-list__even'){
                                $unique_class = 'bpa-is-waiting-list__odd';
                            }else{
                                $unique_class = 'bpa-is-waiting-list__even';
                            }
                        }
                        $appointment['unique_class']  = $unique_class;                
                        if($bookingpress_timeslot_avaliable_count < $bookingpress_selected_extra_members){
                            $appointment['is_avaliable_status'] = 0;
                        }else{
                            $appointment['is_avaliable_status'] = 1;
                        }

                        $appointment['appointment_id'] = $appointment_id;
                        $appointment['payment_id'] = $get_appointment['bookingpress_payment_id'];
                        $payment_log   = $wpdb->get_row($wpdb->prepare('SELECT bookingpress_invoice_id, bookingpress_customer_firstname,bookingpress_customer_lastname,bookingpress_customer_email, bookingpress_payment_gateway FROM ' . $tbl_bookingpress_payment_logs . ' WHERE bookingpress_appointment_booking_ref = %d', $appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_payment_logs is table name defined globally. False Positive alarm
                        $appointment_date_time  = $get_appointment['bookingpress_appointment_date'] . ' ' . $get_appointment['bookingpress_appointment_time'];
                        $appointment['created_date']     = date_i18n($bookingpress_default_date_time_format, strtotime($get_appointment['bookingpress_created_at']));
                        $appointment['appointment_date'] = date_i18n($bookingpress_default_date_time_format, strtotime($appointment_date_time));
                        $appointment['booking_id'] = !empty($get_appointment['bookingpress_booking_id']) ? $get_appointment['bookingpress_booking_id'] : 1;
                        $customer_email = ! empty($get_appointment['bookingpress_customer_email']) ? $get_appointment['bookingpress_customer_email'] : '';
                        $customer_phone = ! empty($get_appointment['bookingpress_customer_phone']) ? $get_appointment['bookingpress_customer_phone'] : '';
                        $appointment['customer_first_name'] = !empty($get_appointment['bookingpress_customer_firstname']) ? stripslashes_deep($get_appointment['bookingpress_customer_firstname']) :'';
                        $appointment['customer_last_name'] = !empty($get_appointment['bookingpress_customer_lastname']) ? stripslashes_deep($get_appointment['bookingpress_customer_lastname']) :'';
                        $appointment['customer_name'] = !empty($get_appointment['bookingpress_customer_name']) ? stripslashes_deep($get_appointment['bookingpress_customer_name']) : $appointment['customer_first_name'].' '.$appointment['customer_last_name'];
                        $appointment['customer_name'] = !empty(trim($appointment['customer_name'])) ? ($appointment['customer_name']) : stripslashes_deep($customer_email);
                        $appointment['customer_email'] = stripslashes_deep($customer_email);
                        $appointment['customer_phone'] = stripslashes_deep($customer_phone);
                        $appointment['service_name']  = stripslashes_deep($get_appointment['bookingpress_service_name']);
                        $appointment['appointment_note']  = stripslashes_deep($get_appointment['bookingpress_appointment_internal_note']);
                        $service_duration             = esc_html($get_appointment['bookingpress_service_duration_val']);
                        $service_duration_unit        = esc_html($get_appointment['bookingpress_service_duration_unit']);
                        if ($service_duration_unit == 'm' ) {
                            if($service_duration == 1){
                                $service_duration .= ' ' . esc_html__('Min', 'bookingpress-waiting-list');
                            }
                            else{
                                $service_duration .= ' ' . esc_html__('Mins', 'bookingpress-waiting-list');
                            }
                        } else if( 'd' == $service_duration_unit ){
                            if( 1 == $service_duration ){
                                $service_duration .= ' ' . esc_html__('Day', 'bookingpress-waiting-list');
                            } else {   
                                $service_duration .= ' ' . esc_html__('Days', 'bookingpress-waiting-list');
                            }
                        } else {
                            $service_duration .= ' ' . esc_html__('Hours', 'bookingpress-waiting-list');
                        }
                        $appointment['appointment_duration'] = $service_duration;
                        $currency_name                       = $get_appointment['bookingpress_service_currency'];
                        $currency_symbol                     = $BookingPress->bookingpress_get_currency_symbol($currency_name);
                        if ($get_appointment['bookingpress_service_price'] == '0' ) {
                            //$payment_amount = '';
                            $payment_amount = $BookingPress->bookingpress_price_formatter_with_currency_symbol(0, $currency_symbol);
                        } else {
                            $payment_amount = $BookingPress->bookingpress_price_formatter_with_currency_symbol($get_appointment['bookingpress_paid_amount'], $currency_symbol);
                        }
                        $appointment['appointment_payment'] = $payment_amount;
                        $bookingpress_appointment_status = esc_html($get_appointment['bookingpress_appointment_status']);
                        $bookingpress_appointment_status_label = $bookingpress_appointment_status;
                        foreach($bookingpress_appointment_status_arr as $status_key => $status_val){
                            if($bookingpress_appointment_status == $status_val['value']){
                                $bookingpress_appointment_status_label = $status_val['text'];
                                break;
                            }    
                        }                        
                        $appointment['appointment_status']  = $bookingpress_appointment_status;
                        $appointment['appointment_status_label'] =  __('On waiting list', 'bookingpress-waiting-list');
                        $bookingpress_view_appointment_date = date_i18n($bookingpress_default_date_format, strtotime($get_appointment['bookingpress_appointment_date']));
                        $bookingpress_view_appointment_time = date($bookingpress_default_time_format, strtotime($get_appointment['bookingpress_appointment_time']))." ".esc_html__('To', 'bookingpress-waiting-list')." ".date($bookingpress_default_time_format, strtotime($get_appointment['bookingpress_appointment_end_time']));
                        $appointment['view_appointment_date'] = $bookingpress_view_appointment_date;
                        $appointment['view_appointment_time'] = $bookingpress_view_appointment_time;
                        $bookingpress_payment_method = ( !empty( $payment_log) && $payment_log['bookingpress_payment_gateway']  == 'on-site' ) ? 'On Site': (!empty($payment_log['bookingpress_payment_gateway']) ? $payment_log['bookingpress_payment_gateway'] : '' ); 
                        $appointment['payment_method'] = $bookingpress_payment_method;
                        $appointment = apply_filters('bookingpress_appointment_add_view_field', $appointment, $get_appointment);
                        $bookingpress_booking_start_timestamp = strtotime( $get_appointment['bookingpress_appointment_date'] . ' ' . $get_appointment['bookingpress_appointment_time'] );
                        $appointment['is_past_appointment'] = current_time('timestamp') > $bookingpress_booking_start_timestamp;
                        $appointment['change_status_loader'] = '0';
                        $appointments[] = $appointment;
                        $counter++;

                    }
                }

                $appointments = apply_filters('bookingpress_modify_appointment_data', $appointments);
                if(empty($appointments)){
                    $appointments = array();
                }
                $data['items']       = $appointments;
                $data['form_field_data'] = $bookingpress_formdata;
                $data['items']       = $appointments;

                $data ['totalItems'] = count($get_total_appointments);
                wp_send_json($data);
                exit;                  
            }
        }
        
        /**
         * bookingpress_modify_appointment_data_fields_func
         *
         * @param  mixed $bookingpress_appointment_vue_data_fields
         * @return void
         */
        public function bookingpress_modify_appointment_data_fields_func($bookingpress_appointment_vue_data_fields){            
            $bookingpress_appointment_vue_data_fields['is_waiting_list'] = 0;

            $bookingpress_appointment_vue_data_fields['multipleSelectionWaiting'] = [];

            $bookingpress_appointment_vue_data_fields['appointment_step_form_data']['selected_waiting_date'] = "";
            $bookingpress_appointment_vue_data_fields['is_blank_display_loader'] = 0;
            return $bookingpress_appointment_vue_data_fields;
        }
        
        /**
         * bookingpress_admin_panel_vue_methods_func
         *
         * @return void
        */
        public function bookingpress_appointment_add_post_data_fun(){
        ?>
            var vm3 = this;
            vm3.is_waiting_list;
            bookingpress_search_data['is_waiting_list'] = vm3.is_waiting_list;
        <?php    
        }

        public function bookingpress_modify_appointment_send_data_fun(){
        ?>
            if(bookingpress_search_data['is_waiting_list'] == '1'){
                postData.action = 'bookingpress_get_waiting_appointments';
            }            
        <?php
        }

        public function bookingpress_modify_appointment_success_response_data_fun(){
        ?>
        vm3.is_blank_display_loader = 0;
        <?php
        }

        public function bookingpress_admin_panel_vue_methods_func(){
            global $bookingpress_notification_duration;
        ?>
        bookingpress_cancel_waiting_appointment(update_id, selectedValue){
            const vm2 = this
                vm2.items.forEach(function(currentValue, index, arr){
                    if(update_id == currentValue.appointment_id){
                        vm2.items[index].change_status_loader = 1;
                    }
                });
                var postData = { action:'bookingpress_change_upcoming_appointment_status', update_appointment_id: update_id, appointment_new_status: selectedValue, _wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' };
                axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
                .then( function (response) {
                    if(response.data == "0" || response.data == 0){
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-waiting-list'); ?>',
                            message: '<?php esc_html_e('Appointment already booked for this slot', 'bookingpress-waiting-list'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        vm2.change_appointment_tab(1);
                        return false;
                    }else{
                        vm2.$notify({
                            title: '<?php esc_html_e('Success', 'bookingpress-waiting-list'); ?>',
                            message: '<?php esc_html_e('Appointment status changed successfully', 'bookingpress-waiting-list'); ?>',
                            type: 'success',
                            customClass: 'success_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        vm2.change_appointment_tab(1);
                    }
                }.bind(this) )
                .catch( function (error) {
                    vm2.$notify({
                        title: '<?php esc_html_e('Error', 'bookingpress-waiting-list'); ?>',
                        message: '<?php esc_html_e('Something went wrong..', 'bookingpress-waiting-list'); ?>',
                        type: 'error',
                        customClass: 'error_notification',
                        duration:<?php echo intval($bookingpress_notification_duration); ?>,                        
                    });
                });                        
        },
        handleWaitingSelectionChange(val) {
            const appointment_items_obj = val
            this.multipleSelectionWaiting = [];
            Object.values(appointment_items_obj).forEach(val => {
                this.multipleSelectionWaiting.push({appointment_id : val.appointment_id})
                this.bulk_action = 'bulk_action';
            });
        },        
        waiting_bulk_actions() {                
                const vm = new Vue()
                const vm2 = this
                if(vm2.bulk_action == "bulk_action")
                {
                    vm2.$notify({
                        title: '<?php esc_html_e('Error', 'bookingpress-waiting-list'); ?>',
                        message: '<?php esc_html_e('Please select any action...', 'bookingpress-waiting-list'); ?>',
                        type: 'error',
                        customClass: 'error_notification',
                        duration:<?php echo intval($bookingpress_notification_duration); ?>,
                    });
                }else{
                    if(this.multipleSelectionWaiting.length > 0 && this.bulk_action == "delete"){
                        var appointment_delete_data = {
                            action:'bookingpress_bulk_appointment',
                            app_delete_ids: this.multipleSelectionWaiting,
                            bulk_action: 'delete',
                            _wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>',
                        }
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( appointment_delete_data ) )
                        .then(function(response){
                            vm2.$notify({
                                title: response.data.title,
                                message: response.data.msg,
                                type: response.data.variant,
                                customClass: response.data.variant+'_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                            vm2.loadAppointments();
                            vm2.multipleSelectionWaiting = [];
                            vm2.totalItems = vm2.items.length
                        }).catch(function(error){
                            console.log(error);
                            vm2.$notify({
                                title: '<?php esc_html_e('Error', 'bookingpress-waiting-list'); ?>',
                                message: '<?php esc_html_e('Something went wrong..', 'bookingpress-waiting-list'); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                        });
                    }
                }
        },
        tableRowClassName({row, rowIndex}) {
            if(row.unique_class){
                return row.unique_class;
            }
            return '';
        },  
        change_appointment_tab(tab_status){
            var vm3 = this;
            vm3.is_waiting_list = tab_status;
            vm3.items = [];
            vm3.totalItems = 0;
            vm3.currentPage = 1;
            vm3.is_blank_display_loader = 1;
            vm3.loadAppointments();
            vm3.multipleSelectionWaiting = [];
            vm3.multipleSelection = [];
        },
        approveAppointment(index, row){
            const vm2 = this;
            var appointment_id = row.appointment_id
            var appointment_approve_data = { action: 'bookingpress_approve_waiting_appointment', appointment_id: appointment_id,_wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' }
            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( appointment_approve_data ) )
            .then(function(response){
                vm2.$notify({
                    title: response.data.title,
                    message: response.data.msg,
                    type: response.data.variant,
                    customClass: 'error_notification',
                    duration:<?php echo intval($bookingpress_notification_duration); ?>,
                });
                vm2.loadAppointments();
            }).catch(function(error){                
                
                vm2.$notify({
                    title: '<?php esc_html_e('Error', 'bookingpress-waiting-list'); ?>',
                    message: '<?php esc_html_e('Something went wrong..', 'bookingpress-waiting-list'); ?>',
                    type: 'error',
                    customClass: 'error_notification',
                    duration:<?php echo intval($bookingpress_notification_duration); ?>,
                });
            });
        },
        <?php
        }
        
        /**
         * Function for add notification message place holder
         * bookingpress_notification_external_message_plachoders_fun
         *
         * @return void
         */
        public function bookingpress_notification_external_message_plachoders_fun(){
        ?>
        <div class="bpa-gs__cb--item-tags-body">
            <div>
                <span class="bpa-tags--item-sub-heading"><?php esc_html_e('Waiting List', 'bookingpress-waiting-list'); ?></span>
                <span class="bpa-tags--item-body" v-for="item in bookingpress_waitinglist_placeholders" @click="bookingpress_insert_placeholder(item.value); bookingpress_insert_sms_placeholder(item.value); bookingpress_insert_whatsapp_placeholder(item.value);">{{ item.name }}</span>
            </div>
        </div>
        <?php
        }
        
        /**
         * Function for add dynamic notification data fields
         * bookingpress_add_dynamic_notification_data_fields_func
         *
         * @param  mixed $bookingpress_notification_vue_methods_data
         * @return void
         */
        public function bookingpress_add_dynamic_notification_data_fields_func($bookingpress_notification_vue_methods_data){            
            global $bookingpress_global_options,$BookingPress,$bookingpress_pro_staff_members;
            $bookingpress_options                  = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_waitinglist_placeholders = json_decode( $bookingpress_options['waiting_list_placeholders'] );
            $bookingpress_notification_vue_methods_data['bookingpress_waitinglist_placeholders'] = $bookingpress_waitinglist_placeholders;
            return $bookingpress_notification_vue_methods_data;
        }
        
        /**
         * Function for add data before appointment in admin appointment list 
         * bookingpress_before_appointments_list_load_fun
         *
         * @param  mixed $bookingpress_after_appointments_list_files
         * @return void
        */
        public function bookingpress_before_appointments_list_load_fun($bookingpress_after_appointments_list_files){                       
            $bookingpress_admin_appointment_tabs = BOOKINGPRESS_WAITING_VIEW_DIR . '/bookingpress_admin_appointment_tabs.php';
            if(file_exists($bookingpress_admin_appointment_tabs)){
                $bookingpress_after_appointments_list_files[] = $bookingpress_admin_appointment_tabs;
            }            
            return $bookingpress_after_appointments_list_files;
        }
        
        /**
         * Function for add data after appointment in admin appointment list 
         * bookingpress_after_appointments_list_file_load_fun
         *
         * @param  mixed $bookingpress_after_appointments_list_files
         * @return void
        */
        public function bookingpress_after_appointments_list_file_load_fun($bookingpress_after_appointments_list_files){                       
            $bookingpress_manage_waiting_appointment = BOOKINGPRESS_WAITING_VIEW_DIR . '/bookingpress_manage_waiting_appointment.php';
            if(file_exists($bookingpress_manage_waiting_appointment)){
                $bookingpress_after_appointments_list_files[] = $bookingpress_manage_waiting_appointment;
            }            
            return $bookingpress_after_appointments_list_files;
        }

        
        /**
         * Function for calculate payment detail for waiting list payment form
         * bookingpress_calculate_payment_details_with_appointment
         *
         * @param  mixed $bookingpress_appointment_details
         * @return void
         */
        function bookingpress_calculate_payment_details_with_appointment($bookingpress_appointment_details){


            global $BookingPress, $wpdb, $tbl_bookingpress_payment_logs, $tbl_bookingpress_appointment_bookings, $bookingpress_pro_staff_members, $bookingpress_global_options;
            $payment_logs_data = array();
            if(!empty($bookingpress_appointment_details)){

                $bookingpress_global_settings = $bookingpress_global_options->bookingpress_global_options();
                $bookingpress_default_date_format = $bookingpress_global_settings['wp_default_date_format'];
                $bookingpress_default_time_format = $bookingpress_global_settings['wp_default_time_format'];        
                $payment_logs_data = array();  

                $bookingpress_tax_percentage = $bookingpress_appointment_details['bookingpress_tax_percentage'];
                $bookingpress_tax_amount = floatval($bookingpress_appointment_details['bookingpress_tax_amount']);
                $bookingpress_coupon_details = ( !empty( $bookingpress_appointment_details['bookingpress_coupon_details'] )  ) ? json_decode($bookingpress_appointment_details['bookingpress_coupon_details'], TRUE) : array();
                $bookingpress_coupon_discount_amount = floatval($bookingpress_appointment_details['bookingpress_coupon_discount_amount']);
                $bookingpress_deposit_details = $bookingpress_appointment_details['bookingpress_deposit_payment_details'];
                $bookingpress_deposit_amount = $bookingpress_appointment_details['bookingpress_deposit_amount'];
                $bookingpress_paid_amount = $bookingpress_appointment_details['bookingpress_paid_amount'];
                $bookingpress_due_amount = $bookingpress_appointment_details['bookingpress_due_amount'];
                $bookingpress_total_amount = $bookingpress_appointment_details['bookingpress_total_amount'];
                $bookingpress_is_cart = intval($bookingpress_appointment_details['bookingpress_is_cart']);
                $bookingpress_order_id = intval($bookingpress_appointment_details['bookingpress_order_id']);
        
                $bookingpress_currency_name = $bookingpress_appointment_details['bookingpress_service_currency'];
                $bookingpress_selected_currency = $BookingPress->bookingpress_get_currency_symbol($bookingpress_currency_name);
                $bookingpress_selected_gateway = '';
                $bookingpress_payment_status = '';

                $bookingpress_selected_gateway_label = $bookingpress_selected_gateway;                
                $payment_logs_data['selected_gateway'] = $bookingpress_selected_gateway;
                $payment_logs_data['selected_gateway_label'] = $bookingpress_selected_gateway_label;
                $payment_logs_data['payment_status'] = $bookingpress_payment_status;
                
                $payment_logs_data['is_cart'] = $bookingpress_is_cart;
                $payment_logs_data['order_id'] = $bookingpress_order_id;
                if($bookingpress_is_cart == 1){
                    $payment_logs_data['staff_member_name'] = ' - ';
                    $payment_logs_data['payment_service'] = ' - ';
                    $payment_logs_data['appointment_date'] = ' - ';
                }
                //Returns tax amount
                $payment_logs_data['tax_amount'] = $bookingpress_tax_amount;
                $payment_logs_data['tax_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_tax_amount, $bookingpress_selected_currency);
                $bookingpress_is_deposit_enable = 0;
                $bookingpress_subtotal_amount = 0;

                if(isset($bookingpress_appointment_details['bookingpress_enable_custom_duration'])  && $bookingpress_appointment_details['bookingpress_enable_custom_duration'] == 1) {
                    if( !empty( $bookingpress_appointment_details['bookingpress_staff_member_details'] ) && !empty( $bookingpress_appointment_details['bookingpress_staff_member_price'] ) ){                 
                        $bookingpress_appointment_details['bookingpress_staff_member_price'] = $bookingpress_appointment_details['bookingpress_service_price'];									
                    }
                }
                
                if( !empty( $bookingpress_appointment_details['bookingpress_staff_member_details'] ) && !empty( $bookingpress_appointment_details['bookingpress_staff_member_price'] ) ){
                    $bookingpress_appointment_details['bookingpress_service_price'] = $bookingpress_appointment_details['bookingpress_staff_member_price'];
                }
                
                $bookingpress_tmp_subtotal_amount = $bookingpress_service_price = $bookingpress_appointment_details['bookingpress_service_price'];
                $bookingpress_service_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_service_price, $bookingpress_selected_currency);
                $bookingpress_appointment_details['bookingpress_service_price_with_currency'] = $bookingpress_service_with_currency;
                
                $bookingpress_appointment_details['bookingpress_appointment_date'] = date_i18n($bookingpress_default_date_format, strtotime($bookingpress_appointment_details['bookingpress_appointment_date']));
                $bookingpress_appointment_details['bookingpress_appointment_time'] = date($bookingpress_default_time_format, strtotime($bookingpress_appointment_details['bookingpress_appointment_time']));
                $bookingpress_appointment_details['bookingpress_appointment_end_time'] = date($bookingpress_default_time_format, strtotime($bookingpress_appointment_details['bookingpress_appointment_end_time']));
                $bookingpress_appointment_details['bookingpress_selected_extra_members'] = intval($bookingpress_appointment_details['bookingpress_selected_extra_members']);
                
                //Check Deposit Applied or Not
                $bookingpress_appointment_details['bookingpress_deposit_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_deposit_amount, $bookingpress_selected_currency);
                $bookingpress_deposit_details = !empty($bookingpress_appointment_details['bookingpress_deposit_payment_details']) ? json_decode($bookingpress_appointment_details['bookingpress_deposit_payment_details'], TRUE) : array();
                if(!empty($bookingpress_deposit_details) && !empty($bookingpress_deposit_details['deposit_selected_type']) && ($bookingpress_selected_gateway != 'on-site') ){
                    $bookingpress_is_deposit_enable = $bookingpress_appointment_details['is_deposit_applied'] = 1;
                }
                
                $bookingpress_staffmember_id = $bookingpress_appointment_details['bookingpress_staff_member_id'];
                $bookingpress_staff_avatar_url = "";
                if(!empty($bookingpress_staffmember_id)){
                    $bookingpress_staffmember_avatar = $bookingpress_pro_staff_members->get_bookingpress_staffmembersmeta($bookingpress_staffmember_id, 'staffmember_avatar_details');
                    $bookingpress_staffmember_avatar = !empty($bookingpress_staffmember_avatar) ? maybe_unserialize($bookingpress_staffmember_avatar) : array();
                    if (!empty($bookingpress_staffmember_avatar[0]['url'])) {
                        $bookingpress_staff_avatar_url = $bookingpress_staffmember_avatar[0]['url'];
                    }else{
                        $bookingpress_staff_avatar_url = BOOKINGPRESS_IMAGES_URL . '/default-avatar.jpg';
                    }
                
                    $bookingpress_tmp_subtotal_amount = isset($bookingpress_appointment_details['bookingpress_staff_member_price']) ? floatval($bookingpress_appointment_details['bookingpress_staff_member_price']) : $bookingpress_tmp_subtotal_amount;
                }
                
                $bookingpress_appointment_details['staff_avatar_url'] = $bookingpress_staff_avatar_url;
                
                $bookingpress_selected_bring_anyone_members = intval($bookingpress_appointment_details['bookingpress_selected_extra_members']) - 1;
                if(!empty($bookingpress_selected_bring_anyone_members)){
                    $bookingpress_tmp_subtotal_amount = $bookingpress_tmp_subtotal_amount + ($bookingpress_tmp_subtotal_amount * $bookingpress_selected_bring_anyone_members);
                }
                
                $bookingpress_extra_total = 0;
                $bookingpress_extra_service_details = !empty($bookingpress_appointment_details['bookingpress_extra_service_details']) ? json_decode($bookingpress_appointment_details['bookingpress_extra_service_details'], TRUE) : array();
                $bookingpress_extra_service_data = array();
                if(!empty($bookingpress_extra_service_details)){
                    foreach($bookingpress_extra_service_details as $k3 => $v3){
                        $bookingpress_extra_total = $bookingpress_extra_total + $v3['bookingpress_final_payable_price'];
                        $bookingpress_extra_service_price = ($v3['bookingpress_extra_service_details']['bookingpress_extra_service_price']) * ($v3['bookingpress_selected_qty']);
                        $bookingpress_extra_service_data[] = array(
                            'selected_qty' => $v3['bookingpress_selected_qty'],
                            'extra_name' => $v3['bookingpress_extra_service_details']['bookingpress_extra_service_name'],
                            'extra_service_price' => $bookingpress_extra_service_price,
                            'extra_service_price_with_currency' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_extra_service_price, $bookingpress_selected_currency),
                        );
                    }
                }
                $bookingpress_appointment_details['extra_service_details'] = $bookingpress_extra_service_data;                
                $bookingpress_tmp_subtotal_amount = $bookingpress_tmp_subtotal_amount + $bookingpress_extra_total;                
                $bookingpress_subtotal_amount = $bookingpress_subtotal_amount + $bookingpress_tmp_subtotal_amount;

                $payment_logs_data['appointment_details'] = $bookingpress_appointment_details;
                $payment_logs_data['is_deposit_enable'] = $bookingpress_is_deposit_enable;
                
                $bookingpress_included_tax_label = !empty($bookingpress_appointment_details['bookingpress_included_tax_label']) ? $bookingpress_appointment_details['bookingpress_included_tax_label'] : '';
                $payment_logs_data['included_tax_label'] = $bookingpress_included_tax_label;
        
                $bookingpress_tax_amount_in_order_summary = !empty($bookingpress_appointment_details['bookingpress_display_tax_order_summary']) ? 'true' : 'false';
                $payment_logs_data['display_tax_amount_in_order_summary'] = $bookingpress_tax_amount_in_order_summary;
                $bookingpress_payment_status ='';            
                $bookingpress_price_display_setting = !empty($bookingpress_appointment_details['bookingpress_price_display_setting']) ? $bookingpress_appointment_details['bookingpress_price_display_setting'] : 'exclude_taxes';
                $payment_logs_data['price_display_setting'] = $bookingpress_price_display_setting;


                if( !empty($bookingpress_price_display_setting) && $bookingpress_price_display_setting == "exclude_taxes" ){
                    $bookingpress_final_total_amount = ($bookingpress_subtotal_amount + $bookingpress_tax_amount) - $bookingpress_coupon_discount_amount;	
                }else{
                    $bookingpress_final_total_amount = $bookingpress_subtotal_amount - $bookingpress_coupon_discount_amount;
                }
        
                $bookingpress_deposit_amount = 0;
                
                $currency_name   = $bookingpress_appointment_details['bookingpress_service_currency'];
                $currency_symbol = $BookingPress->bookingpress_get_currency_symbol($bookingpress_selected_currency);
        
                $payment_logs_data['deposit_amount'] = $bookingpress_deposit_amount;
                $payment_logs_data['deposit_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_deposit_amount, $bookingpress_selected_currency);
        
                $bookingpress_due_amount = 0;
                if($bookingpress_deposit_amount != 0){
                    $bookingpress_due_amount = $bookingpress_final_total_amount - $bookingpress_deposit_amount;
                }
                $payment_logs_data['due_amount'] = floatval($bookingpress_due_amount);
                $payment_logs_data['due_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_due_amount, $bookingpress_selected_currency);        
                $payment_logs_data['subtotal_amount'] = $bookingpress_subtotal_amount;
                $payment_logs_data['subtotal_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_subtotal_amount, $bookingpress_selected_currency);                
                $payment_logs_data['payment_numberic_amount'] = $bookingpress_paid_amount;
                $payment_logs_data['payment_amount'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_paid_amount, $bookingpress_selected_currency);                        
                $payment_logs_data['total_amount'] = $bookingpress_final_total_amount;
                $payment_logs_data['total_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_final_total_amount, $bookingpress_selected_currency);
                
            }
            return $payment_logs_data;
        }
                
        /**
         * Function for add dynamic vue data for waiting list form
         * bookingpress_waiting_payment_dynamic_data_fields_func
         *
         * @param  mixed $bookingpress_dynamic_data_fields
         * @return void
         */
        function bookingpress_waiting_payment_dynamic_data_fields_func($bookingpress_dynamic_data_fields){

            global $bookingpress_global_settings,$bookingpress_services,$bookingpress_deposit_payment, $wpdb, $BookingPress, $bookingpress_front_vue_data_fields, $tbl_bookingpress_customers, $tbl_bookingpress_categories, $tbl_bookingpress_services, $tbl_bookingpress_servicesmeta, $tbl_bookingpress_form_fields, $bookingpress_global_options, $bookingpress_coupons, $bookingpress_deposit_payment, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_extra_services, $tbl_bookingpress_payment_logs, $bookingpress_pro_payment, $BookingPressPro;            
            $bookingpress_global_settings = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_default_date_format = $bookingpress_global_settings['wp_default_date_format'];
            $bookingpress_waiting_payment_data_vars['waiting_payment_success_msg'] = '';
            $bookingpress_waiting_payment_data_vars['payment_already_paid_msg'] = '';
            $bookingpress_waiting_payment_data_vars['is_display_complete_payment_error'] = '0';
            $bookingpress_waiting_payment_data_vars['is_complete_payment_error_msg'] = '';
            $bookingpress_payment_already_paid_message = $BookingPress->bookingpress_get_settings('payment_already_paid_message', 'message_setting');
            $on_site_payment = $BookingPress->bookingpress_get_settings('on_site_payment', 'payment_setting');
            $paypal_payment  = $BookingPress->bookingpress_get_settings('paypal_payment', 'payment_setting');
            $bookingpress_waiting_payment_data_vars['on_site_payment'] = $on_site_payment;
            $bookingpress_waiting_payment_data_vars['paypal_payment']  = $paypal_payment;
            $bookingpress_total_configure_gateways = 0;
            $bookingpress_is_only_onsite_enabled   = 0;
            if (( $on_site_payment == 'true' || $on_site_payment == '1' ) && ( $paypal_payment == 'true' || $paypal_payment == '1' ) ) {
                $bookingpress_total_configure_gateways = 2;
                $bookingpress_is_only_onsite_enabled   = 0;
            } elseif (( $on_site_payment == 'true' || $on_site_payment == '1' ) && ( $paypal_payment == 'false' || empty($paypal_payment) ) ) {
                $bookingpress_total_configure_gateways = 1;
                $bookingpress_is_only_onsite_enabled   = 1;
            } elseif (( $on_site_payment == 'false' || empty($on_site_payment) ) && ( $paypal_payment == 'true' || $paypal_payment == '1' ) ) {
                $bookingpress_total_configure_gateways = 1;
                $bookingpress_is_only_onsite_enabled   = 0;
            }
            if(empty($bookingpress_waiting_payment_data_vars['appointment_step_form_data'])){
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data'] = array();
            }            
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_payment_method'] = '';
            $bookingpress_waiting_payment_data_vars['total_configure_gateways'] = $bookingpress_total_configure_gateways;
            $bookingpress_waiting_payment_data_vars['is_only_onsite_enabled']   = $bookingpress_is_only_onsite_enabled;

            if ($bookingpress_is_only_onsite_enabled == 1 ) {
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_payment_method'] = 'on-site';
            }
            if ($bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_payment_method'] == '' && ( $paypal_payment == 'true' ) ) {
                $bookingpress_waiting_payment_data_vars['paypal_payment'] = 'paypal';
            }

            if(!empty($_GET['bkp_pay'])){
                $bookingpress_other_gateways_tmp_data = array(
                    'bookingpress_activate_payment_gateway_counter' => $bookingpress_total_configure_gateways,
                );
                $bookingpress_other_gateways_tmp_data = apply_filters('bookingpress_frontend_apointment_form_add_dynamic_data', $bookingpress_other_gateways_tmp_data);
                foreach($bookingpress_other_gateways_tmp_data as $tmp_key => $tmp_val){
                    $bookingpress_waiting_payment_data_vars[$tmp_key] = $tmp_val;    
                }
            }

            $bookigpress_time_format_for_booking_form =  $BookingPress->bookingpress_get_customize_settings('bookigpress_time_format_for_booking_form','booking_form');
            $bookigpress_time_format_for_booking_form =  !empty($bookigpress_time_format_for_booking_form) ? $bookigpress_time_format_for_booking_form : '2';
            $bookingpress_waiting_payment_data_vars['bookigpress_time_format_for_booking_form'] = $bookigpress_time_format_for_booking_form;

            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['form_fields'] = array(
                'customer_email' => '',
                'customer_name' => '',
                'customer_firstname' => '',
                'customer_lastname' => '',  
            );

            $bookingpress_waiting_payment_data_vars['is_coupon_activated'] = $bookingpress_coupons->bookingpress_check_coupon_module_activation();
            $bookingpress_waiting_payment_data_vars['bookingpress_is_deposit_payment_activate'] = $bookingpress_deposit_payment->bookingpress_check_deposit_payment_module_activation();
            $bookingpress_waiting_payment_data_vars['is_tax_activated']    = '';


            $bookingpress_waiting_payment_data_vars['isLoadBookingLoader'] = '0';
            $bookingpress_waiting_payment_data_vars['isBookingDisabled'] = false;
            if($bookingpress_total_configure_gateways == 0){
                $bookingpress_waiting_payment_data_vars['isBookingDisabled'] = true;
            }

            $bookingpress_subtotal_text = $BookingPress->bookingpress_get_customize_settings( 'subtotal_text', 'booking_form' );
            $bookingpress_subtotal_text = !empty($bookingpress_subtotal_text) ? stripslashes_deep($bookingpress_subtotal_text) : esc_html__('Subtotal', 'bookingpress-waiting-list');
            $bookingpress_waiting_payment_data_vars['subtotal_text'] = $bookingpress_subtotal_text;
            
            
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['coupon_code']          = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount'] = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount_with_currency'] = '';

			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['card_holder_name'] = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['card_number']      = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['expire_month']     = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['expire_year']      = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['cvv']              = '';

			$bookingpress_waiting_payment_data_vars['coupon_code_msg']           = '';
			$bookingpress_waiting_payment_data_vars['coupon_applied_status']     = 'error';
			$bookingpress_waiting_payment_data_vars['coupon_discounted_amount']  = 0;
			$bookingpress_waiting_payment_data_vars['coupon_apply_loader']       = '0';
			$bookingpress_waiting_payment_data_vars['bpa_coupon_apply_disabled'] = 0;

            $coupon_code_title = $BookingPress->bookingpress_get_customize_settings('coupon_code_title', 'booking_form');
			$coupon_code_field_title = $BookingPress->bookingpress_get_customize_settings('coupon_code_field_title', 'booking_form');
			$coupon_apply_button_label = $BookingPress->bookingpress_get_customize_settings('coupon_apply_button_label', 'booking_form');
			$couon_applied_title = $BookingPress->bookingpress_get_customize_settings('couon_applied_title', 'booking_form');
			$bookingpress_waiting_payment_data_vars['coupon_code_title'] = !empty($coupon_code_title) ? stripslashes_deep($coupon_code_title) : '';			
			$bookingpress_waiting_payment_data_vars['coupon_code_field_title'] = !empty($coupon_code_field_title) ? stripslashes_deep($coupon_code_field_title) : '';
			$bookingpress_waiting_payment_data_vars['coupon_apply_button_label'] = !empty($coupon_apply_button_label) ? stripslashes_deep($coupon_apply_button_label) : '';
			$bookingpress_waiting_payment_data_vars['couon_applied_title'] = !empty($couon_applied_title) ? stripslashes_deep($couon_applied_title) : '';


            $deposit_paying_amount_title = $BookingPress->bookingpress_get_customize_settings('deposit_paying_amount_title', 'booking_form');
			$deposit_heading_title = $BookingPress->bookingpress_get_customize_settings('deposit_heading_title', 'booking_form');
			$deposit_remaining_amount_title = $BookingPress->bookingpress_get_customize_settings('deposit_remaining_amount_title', 'booking_form');
			$deposit_title = $BookingPress->bookingpress_get_customize_settings('deposit_title', 'booking_form');
			$full_payment_title = $BookingPress->bookingpress_get_customize_settings('full_payment_title', 'booking_form');						
            $bookingpress_waiting_payment_data_vars['deposit_paying_amount_title'] = !empty($deposit_paying_amount_title) ? stripslashes_deep($deposit_paying_amount_title) : '';		
			$bookingpress_waiting_payment_data_vars['deposit_heading_title'] = !empty($deposit_heading_title) ? stripslashes_deep($deposit_heading_title) : '';			
			$bookingpress_waiting_payment_data_vars['deposit_remaining_amount_title'] = !empty($deposit_remaining_amount_title) ? stripslashes_deep($deposit_remaining_amount_title) : '';
			$bookingpress_waiting_payment_data_vars['deposit_title'] = !empty($deposit_title) ? stripslashes_deep($deposit_title) : '';
			$bookingpress_waiting_payment_data_vars['full_payment_title'] = !empty($full_payment_title) ? stripslashes_deep($full_payment_title) : '';

            //Load data calculations
            $bookingpress_payment_token = !empty($_GET['bkp_pay']) ? sanitize_text_field($_GET['bkp_pay']) : ''; 
			
			$bookingpress_payment_request_exists = $wpdb->get_var($wpdb->prepare("SELECT bookingpress_appointment_booking_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_waiting_payment_token = %s", $bookingpress_payment_token));// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
            $bookingpress_appointment_details = array();
            $bookingpress_service_price = 0;
            $bookingpress_selected_extra_members = array();
			if($bookingpress_payment_request_exists > 0){
                $bookingpress_appointment_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_waiting_payment_token = %s", $bookingpress_payment_token), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm

                $bookingpress_is_cart = !empty($bookingpress_appointment_details['bookingpress_is_cart']) ? intval($bookingpress_appointment_details['bookingpress_is_cart']) : 0;
                $bookingpress_order_id = !empty($bookingpress_appointment_details['bookingpress_order_id']) ? intval($bookingpress_appointment_details['bookingpress_order_id']) : 0;
                $bookingpress_payment_id = !empty($bookingpress_appointment_details['bookingpress_payment_id']) ? intval($bookingpress_appointment_details['bookingpress_payment_id']) : 0;

                $bookingpress_calculated_payment_details = $this->bookingpress_calculate_payment_details_with_appointment($bookingpress_appointment_details);
                
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['is_cart'] = $bookingpress_is_cart;
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['cart_order_id'] = $bookingpress_order_id;
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['appointment_id'] = $bookingpress_appointment_details['bookingpress_appointment_booking_id'];
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['payment_id'] = $bookingpress_appointment_details['bookingpress_payment_id'];

				$bookingpress_global_data = $bookingpress_global_options->bookingpress_global_options();
                $bookingpress_time_format = $bookingpress_global_data['wp_default_time_format'];

				$bookingpress_selected_currency = $bookingpress_appointment_details['bookingpress_service_currency'];
				$bookingpress_selected_currency = $BookingPress->bookingpress_get_currency_symbol($bookingpress_selected_currency);
				$bookingpress_selected_service_name = $bookingpress_appointment_details['bookingpress_service_name'];
				$bookingpress_selected_service_price = $bookingpress_appointment_details['bookingpress_service_price'];
				$bookingpress_selected_date = $bookingpress_appointment_details['bookingpress_appointment_date'];
				$bookingpress_selected_start_time = $bookingpress_appointment_details['bookingpress_appointment_time'];
				$bookingpress_selected_end_time = $bookingpress_appointment_details['bookingpress_appointment_end_time'];
				$bookingpress_customer_email = $bookingpress_appointment_details['bookingpress_customer_email'];
				$bookingpress_customer_name = $bookingpress_appointment_details['bookingpress_customer_name'];
				$bookingpress_customer_firstname = $bookingpress_appointment_details['bookingpress_customer_firstname'];
				$bookingpress_customer_lastname = $bookingpress_appointment_details['bookingpress_customer_lastname'];
				$bookingpress_total_payable_amount = $bookingpress_appointment_details['bookingpress_paid_amount'];

                //$bookingpress_selected_service_name = $bookingpress_appointment_details['bookingpress_service_name'];
                /* TRanslate service name in Waiting payment page */
                if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {					
                    if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func')) {
                        $bookingpress_selected_service_name = $BookingPressPro->bookingpress_pro_front_language_translation_func($bookingpress_selected_service_name,'service','bookingpress_service_name',$bookingpress_appointment_details['bookingpress_service_id']);  
                    }
                }       
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_service_name'] = $bookingpress_selected_service_name;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_date'] = $bookingpress_selected_date;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_start_time'] = $bookingpress_selected_start_time;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_end_time'] = $bookingpress_selected_end_time;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_formatted_start_time'] = date($bookingpress_time_format, strtotime($bookingpress_selected_start_time));
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_formatted_end_time'] = date($bookingpress_time_format, strtotime($bookingpress_selected_end_time));
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['customer_email'] = $bookingpress_customer_email;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['customer_firstname'] = $bookingpress_customer_firstname;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['customer_lastname'] = $bookingpress_customer_lastname;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['form_fields']['customer_firstname'] = $bookingpress_customer_firstname;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['form_fields']['customer_lastname'] = $bookingpress_customer_lastname;
				$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['form_fields']['customer_email'] = $bookingpress_customer_email;
				

                $bookingpress_payment_method = "on-site";
                if(!empty($bookingpress_payment_id)){
                    $bookingpress_payment_details = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_payment_gateway FROM {$tbl_bookingpress_payment_logs} WHERE bookingpress_payment_log_id = %d", $bookingpress_payment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_payment_logs is table name defined globally. False Positive alarm
                    if(!empty($bookingpress_payment_details['bookingpress_payment_gateway'])){
                        $bookingpress_payment_method = $bookingpress_payment_details['bookingpress_payment_gateway'];
                    }
                }

                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_payment_method'] = $bookingpress_payment_method;                
                $bookingpress_waiting_payment_data_vars['cart_items'] = array();
                $bookingpress_waiting_payment_data_vars['is_cart_enabled'] = '0';

                if($bookingpress_is_cart){

                    $bookingpress_waiting_payment_data_vars['is_cart_enabled'] = '1';
                    $bookingpress_deposit_details = !empty($bookingpress_appointment_details['bookingpress_deposit_payment_details']) ? json_decode($bookingpress_appointment_details['bookingpress_deposit_payment_details'], TRUE) : array();
                    $bookingpress_deposit_selected_type = !empty($bookingpress_deposit_details['deposit_selected_type']) ? $bookingpress_deposit_details['deposit_selected_type'] : 'percentage';
                    $bookingpress_deposit_amount = !empty($bookingpress_calculated_payment_details['deposit_amount']) ? $bookingpress_calculated_payment_details['deposit_amount'] : 0;
                    $bookingpress_deposit_amount_with_currency = !empty($bookingpress_calculated_payment_details['deposit_amount_with_currency']) ? $bookingpress_calculated_payment_details['deposit_amount_with_currency'] : 0;
                    $bookingpress_due_amount = !empty($bookingpress_calculated_payment_details['due_amount']) ? $bookingpress_calculated_payment_details['due_amount'] : 0;
                    $bookingpress_due_amount_with_currency = !empty($bookingpress_calculated_payment_details['due_amount_with_currency']) ? $bookingpress_calculated_payment_details['due_amount_with_currency'] : 0;
                    $bookingpress_subtotal_amount = !empty($bookingpress_calculated_payment_details['subtotal_amount']) ? $bookingpress_calculated_payment_details['subtotal_amount'] : 0;
                    $bookingpress_subtotal_amount_with_currency = !empty($bookingpress_calculated_payment_details['subtotal_amount_with_currency']) ? $bookingpress_calculated_payment_details['subtotal_amount_with_currency'] : 0;
                    $bookingpress_total_amount = !empty($bookingpress_calculated_payment_details['total_amount']) ? $bookingpress_calculated_payment_details['total_amount'] : 0;
                    $bookingpress_total_amount_with_currency = !empty($bookingpress_calculated_payment_details['total_amount_with_currency']) ? $bookingpress_calculated_payment_details['total_amount_with_currency'] : 0;

                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_type'] = $bookingpress_deposit_selected_type;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount'] = $bookingpress_deposit_amount;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['bookingpress_deposit_amt'] = $bookingpress_deposit_amount_with_currency;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['service_price_without_currency'] = $bookingpress_subtotal_amount;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_service_price'] = $bookingpress_subtotal_amount_with_currency;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount'] = !empty($bookingpress_due_amount) ? $bookingpress_due_amount : $bookingpress_total_amount ;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount_with_currency'] = !empty($bookingpress_due_amount) ? $bookingpress_due_amount_with_currency : $bookingpress_total_amount_with_currency ;
                    

                    //If coupon applied then display multiple appointment details
                    $bookingpress_cart_appointment_details = (isset($bookingpress_calculated_payment_details['appointment_details']))?$bookingpress_calculated_payment_details['appointment_details']:array();
                        if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {					
                            if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func')) {    
                                $bookingpress_cart_appointment_details['bookingpress_service_name'] = $BookingPressPro->bookingpress_pro_front_language_translation_func($bookingpress_cart_appointment_details['bookingpress_service_name'],'service','bookingpress_service_name',$bookingpress_cart_appointment_details['bookingpress_service_id']);
                            }
                        }
                        $bookingpress_waiting_payment_data_vars['cart_items'][] = array(
                            'bookingpress_service_name' => $bookingpress_cart_appointment_details['bookingpress_service_name'],
                            'bookingpress_selected_date' => $bookingpress_cart_appointment_details['bookingpress_appointment_date'],
                            'bookingpress_selected_start_time' => $bookingpress_cart_appointment_details['bookingpress_appointment_time'],
                            'bookingpress_selected_end_time' => $bookingpress_cart_appointment_details['bookingpress_appointment_end_time'],
                        );
                
                }else{
                    //If staffmember selected then apply staffmember price
                    if(!empty($bookingpress_appointment_details['bookingpress_staff_member_id'])){
                        $bookingpress_selected_service_price = $bookingpress_appointment_details['bookingpress_staff_member_price'];
                    }
                    if(!empty($bookingpress_appointment_details['bookingpress_enable_custom_duration']) && $bookingpress_appointment_details['bookingpress_enable_custom_duration'] == 1){
                        $bookingpress_selected_service_price = $bookingpress_appointment_details['bookingpress_service_price'];
                    }

                    //Calculate service extra price
                    $bookingpress_extra_service_price_arr = array();
                    if(!empty($bookingpress_appointment_details['bookingpress_extra_service_details'])){
                        $bookingpress_extra_service_details = json_decode($bookingpress_appointment_details['bookingpress_extra_service_details'], TRUE);
                        $bookingpress_extra_service_details = array_map( array( $BookingPress, 'appointment_sanatize_field'), $bookingpress_extra_service_details ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_POST contains mixed array and will be sanitized using 'appointment_sanatize_field' function
                        if( is_array($bookingpress_extra_service_details) && !empty($bookingpress_extra_service_details) ){
                            foreach($bookingpress_extra_service_details as $k => $v){
                                if($v['bookingpress_is_selected'] == "true" || $v['bookingpress_is_selected'] == "1"){
                                    $bookingpress_extra_service_id = intval($k);                                    
                                    $bookingpress_extra_service_details = $v['bookingpress_extra_service_details'];
                                    if(!empty($bookingpress_extra_service_details)){
                                        $bookingpress_extra_service_price = ! empty( $bookingpress_extra_service_details['bookingpress_extra_service_price'] ) ? floatval( $bookingpress_extra_service_details['bookingpress_extra_service_price'] ) : 0;
                                        $bookingpress_selected_qty = !empty($v['bookingpress_selected_qty']) ? intval($v['bookingpress_selected_qty']) : 1;
                                        if(!empty($bookingpress_selected_qty)){
                                            $bookingpress_final_price = $bookingpress_extra_service_price * $bookingpress_selected_qty;
                                            array_push($bookingpress_extra_service_price_arr, $bookingpress_final_price);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    //Add bring anyone module price
                    if(!empty($bookingpress_appointment_details['bookingpress_selected_extra_members'])){
                        $bookingpress_selected_service_price = ($bookingpress_selected_service_price * intval($bookingpress_appointment_details['bookingpress_selected_extra_members']));
                    }
                    if ( ! empty( $bookingpress_extra_service_price_arr ) && is_array( $bookingpress_extra_service_price_arr ) ) {
                        foreach ( $bookingpress_extra_service_price_arr as $k => $v ) {
                            $bookingpress_selected_service_price = $bookingpress_selected_service_price + $v;
                        }
                    }
                    if(!empty($bookingpress_appointment_details['bookingpress_tax_amount'])){
                        $bookingpress_waiting_payment_data_vars['is_tax_activated'] = '1';
                        $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['tax_amount'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_appointment_details['bookingpress_tax_amount'], $bookingpress_selected_currency);
                        $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['tax_amount_without_currency'] = $bookingpress_appointment_details['bookingpress_tax_amount'];
                    }

                    if(!empty($bookingpress_appointment_details['bookingpress_deposit_amount'])){
                        $bookingpress_deposit_details = !empty($bookingpress_appointment_details['bookingpress_deposit_payment_details']) ? json_decode($bookingpress_appointment_details['bookingpress_deposit_payment_details'], TRUE) : array();
                        if(!empty($bookingpress_deposit_details['deposit_amount'])){
                            $bookingpress_deposit_selected_type = $bookingpress_deposit_details['deposit_selected_type'];
                            $bookingpress_deposit_amount = floatval($bookingpress_deposit_details['deposit_amount']);
                            $bookingpress_due_amount = floatval($bookingpress_deposit_details['deposit_due_amount']);
                            if(!empty($bookingpress_due_amount)){
                                $bookingpress_total_payable_amount = $bookingpress_due_amount;
                            }

                            $bookingpress_deposit_tmp_total = $bookingpress_deposit_amount + $bookingpress_due_amount;

                            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_type'] = $bookingpress_deposit_selected_type;
                            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount'] = $bookingpress_deposit_amount;
                            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['bookingpress_deposit_amt'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_deposit_amount, $bookingpress_selected_currency);
                        }
                    }

                    $bookingpress_waiting_payment_data_vars['is_coupon_already_applied'] = '0';
                    if(!empty($bookingpress_appointment_details['bookingpress_coupon_discount_amount'])){

                        $bookingpress_applied_coupon_amount = floatval($bookingpress_appointment_details['bookingpress_coupon_discount_amount']);
                        $bookingpress_applied_coupon_data = !empty($bookingpress_appointment_details['bookingpress_coupon_details']) ? json_decode($bookingpress_appointment_details['bookingpress_coupon_details'], TRUE) : '';
                        if(!empty($bookingpress_applied_coupon_data)){
                            $bookingpress_waiting_payment_data_vars['coupon_applied_status'] = "success";
                            if(!empty($bookingpress_applied_coupon_data['coupon_data'])){
                                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['coupon_code'] = $bookingpress_applied_coupon_data['coupon_data']['bookingpress_coupon_code'];
                            }else{
                                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['coupon_code'] = $bookingpress_applied_coupon_data['bookingpress_coupon_code'];
                            }
                            $bookingpress_waiting_payment_data_vars['is_coupon_already_applied'] = '1';                            
                            $bookingpress_waiting_payment_data_vars['coupon_discounted_amount'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_applied_coupon_amount, $bookingpress_selected_currency);;
                            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['coupon_discount_amount_with_currecny'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_applied_coupon_amount, $bookingpress_selected_currency);;
                        }

                    }

                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['service_price_without_currency'] = $bookingpress_selected_service_price;
                    $bookingpress_selected_service_price_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_selected_service_price, $bookingpress_selected_currency);
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_service_price'] = $bookingpress_selected_service_price_with_currency;

                    $bookingpress_total_payable_amount_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_total_payable_amount, $bookingpress_selected_currency);
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount'] = $bookingpress_total_payable_amount;
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['total_payable_amount_with_currency'] = $bookingpress_total_payable_amount_with_currency;
                }
                
            }else{
                $bookingpress_waiting_payment_data_vars['payment_already_paid_msg'] = $BookingPress->bookingpress_get_customize_settings('waiting_payment_success_message','booking_form');
            }

            /** Deposit payment integration Start **/

            $bookingpress_total_amount = isset($bookingpress_appointment_details['bookingpress_total_amount'])?$bookingpress_appointment_details['bookingpress_total_amount']:0;            
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['bookingpress_total_amount'] = $bookingpress_total_amount;

            $bookingpress_waiting_payment_data_vars['bookingpress_current_datetime'] = current_time( 'mysql' );			
			$bookingpress_waiting_payment_data_vars['bookingpress_is_deposit_payment_activate'] = $bookingpress_deposit_payment->bookingpress_check_deposit_payment_module_activation();
			$bookingpress_deposit_payment_method = "allow_customer_to_pay_full_amount";
			if(!empty($BookingPress->bookingpress_get_settings( 'bookingpress_allow_customer_to_pay', 'payment_setting' ))){
				$bookingpress_deposit_payment_method = $BookingPress->bookingpress_get_settings( 'bookingpress_allow_customer_to_pay', 'payment_setting' );
			}            
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['bookingpress_deposit_payment_method'] = $bookingpress_deposit_payment_method;
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_type'] = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount'] = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount_percentage'] = '';
			$bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_formatted_amount'] = '';

            $bookingpress_service_id = isset($bookingpress_appointment_details['bookingpress_service_id'])?$bookingpress_appointment_details['bookingpress_service_id']:0;

			if($bookingpress_deposit_payment->bookingpress_check_deposit_payment_module_activation()){
				
                if( ($bookingpress_deposit_payment_method == "deposit_or_full_price" || $bookingpress_deposit_payment_method == "allow_customer_to_pay_full_amount") && !empty($bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id, 'deposit_type')) ){
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_type'] = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id, 'deposit_type');
                    $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount'] = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id, 'deposit_amount');
                    if($bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_type'] != "percentage"){
                        $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_formatted_amount'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount']);
                    }else{
                        $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount_percentage'] = $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['deposit_payment_amount'];
                    }
                }
				
			}
            
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['selected_payment_method'] = '';
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['bookingpress_deposit_payment_method'] = 'deposit_or_full_price';
            //New Variable Added
            if(isset($bookingpress_appointment_details['bookingpress_enable_custom_duration'])  && $bookingpress_appointment_details['bookingpress_enable_custom_duration'] == 1) {
                if( !empty( $bookingpress_appointment_details['bookingpress_staff_member_details'] ) && !empty( $bookingpress_appointment_details['bookingpress_staff_member_price'] ) ){                 
                    $bookingpress_appointment_details['bookingpress_staff_member_price'] = $bookingpress_appointment_details['bookingpress_service_price'];									
                }
            }
            if( !empty( $bookingpress_appointment_details['bookingpress_staff_member_details'] ) && !empty( $bookingpress_appointment_details['bookingpress_staff_member_price'] ) ){
                $bookingpress_service_price = $bookingpress_appointment_details['bookingpress_staff_member_price'];
            }    

            $bookingpress_tmp_subtotal_amount = $bookingpress_service_price;
            if(isset($bookingpress_appointment_details['bookingpress_selected_extra_members'])){
                $bookingpress_selected_bring_anyone_members = intval($bookingpress_appointment_details['bookingpress_selected_extra_members']) - 1;
                if(!empty($bookingpress_selected_bring_anyone_members)){
                    $bookingpress_tmp_subtotal_amount = $bookingpress_tmp_subtotal_amount + ($bookingpress_tmp_subtotal_amount * $bookingpress_selected_bring_anyone_members);
                }  
            }
            
            $bookingpress_extra_total = 0;
            $bookingpress_extra_service_details = !empty($bookingpress_appointment_details['bookingpress_extra_service_details']) ? json_decode($bookingpress_appointment_details['bookingpress_extra_service_details'], TRUE) : array();
            $bookingpress_extra_service_data = array();
            if(!empty($bookingpress_extra_service_details)){
                foreach($bookingpress_extra_service_details as $k3 => $v3){
                    $bookingpress_extra_total = $bookingpress_extra_total + $v3['bookingpress_final_payable_price'];
                    $bookingpress_extra_service_price = ($v3['bookingpress_extra_service_details']['bookingpress_extra_service_price']) * ($v3['bookingpress_selected_qty']);
                    $bookingpress_extra_service_data[] = array(
                        'selected_qty' => $v3['bookingpress_selected_qty'],
                        'extra_name' => $v3['bookingpress_extra_service_details']['bookingpress_extra_service_name'],
                        'extra_service_price' => $bookingpress_extra_service_price,
                        'extra_service_price_with_currency' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_extra_service_price, $bookingpress_selected_currency),
                    );
                }
            }
            $bookingpress_appointment_details['extra_service_details'] = $bookingpress_extra_service_data;
            $bookingpress_tmp_subtotal_amount = $bookingpress_tmp_subtotal_amount + $bookingpress_extra_total;            
            $bookingpress_decimal_points = $BookingPress->bookingpress_get_settings('price_number_of_decimals', 'payment_setting');
            $bookingpress_decimal_points = intval($bookingpress_decimal_points);
            $bookingpress_waiting_payment_data_vars['bookingpress_decimal_points'] = $bookingpress_decimal_points;

            $bookingpress_currency_separator = $BookingPress->bookingpress_get_settings('price_separator', 'payment_setting');
            $bookingpress_waiting_payment_data_vars['bookingpress_currency_separator'] = $bookingpress_currency_separator;

            $bookingpress_currency_name = $BookingPress->bookingpress_get_settings('payment_default_currency', 'payment_setting');
            $bookingpress_waiting_payment_data_vars['bookingpress_currency_name'] = $bookingpress_currency_name;
            $bookingpress_waiting_payment_data_vars['bookingpress_currency_symbol'] = $BookingPress->bookingpress_get_currency_symbol($bookingpress_currency_name);

            $bookingpress_price_symbol_position = $BookingPress->bookingpress_get_settings('price_symbol_position', 'payment_setting');
            $bookingpress_waiting_payment_data_vars['bookingpress_currency_symbol_position'] = $bookingpress_price_symbol_position;

            $bookingpress_custom_comma_separator = $BookingPress->bookingpress_get_settings('custom_comma_separator', 'payment_setting');
            $bookingpress_custom_thousand_separator = $BookingPress->bookingpress_get_settings('custom_dot_separator', 'payment_setting');
            $bookingpress_waiting_payment_data_vars['bookingpress_custom_comma_separator'] = $bookingpress_custom_comma_separator;
            $bookingpress_waiting_payment_data_vars['bookingpress_custom_thousand_separator'] = $bookingpress_custom_thousand_separator;

            if(!empty($bookingpress_appointment_details['bookingpress_tax_amount'])){
                $bookingpress_waiting_payment_data_vars['is_tax_activated'] = '1';
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['tax_amount'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_appointment_details['bookingpress_tax_amount'], $bookingpress_waiting_payment_data_vars['bookingpress_currency_symbol']);
                $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['tax_amount_without_currency'] = $bookingpress_appointment_details['bookingpress_tax_amount'];
            }

            $bookingpress_waiting_payment_data_vars['is_display_waiting_payment_error'] = 0;
            $bookingpress_waiting_payment_data_vars['is_waitng_payment_error_msg'] = '';                                  

			/** Deposit payment integration Over **/
            $bookingpress_waiting_payment_data_vars = apply_filters('modify_complate_payment_data_after_entry_create',$bookingpress_waiting_payment_data_vars, $bookingpress_appointment_details);


            $bookingpress_waiting_payment_data_vars = apply_filters('modify_waiting_payment_data_after_entry_create',$bookingpress_waiting_payment_data_vars, $bookingpress_appointment_details);

            $bookingpress_dynamic_data_fields = wp_json_encode($bookingpress_waiting_payment_data_vars);
            return $bookingpress_dynamic_data_fields;
        }
        
        /**
         * Function for add waiting payment form
         *
         * @param  mixed $atts
         * @param  mixed $content
         * @param  mixed $tag
         * @return void
        */
        public function bookingpress_waiting_payment_form($atts, $content, $tag){           
           
            global $wpdb, $BookingPress, $bookingpress_global_options;
            $BookingPress->set_front_css(1);
            $BookingPress->set_front_js(1);
            $BookingPress->bookingpress_load_booking_form_custom_css();
            
            $bookingpress_uniq_id = uniqid();
            $bookingpress_vue_root_element_id = '#bookingpress_booking_form_' . $bookingpress_uniq_id;
            $bookingpress_script_return_data = '';
            $bookingpress_nonce = esc_html(wp_create_nonce('bpa_wp_nonce'));
            
            //Start here
            $bookingpress_customize_settings = $BookingPress->bookingpress_get_customize_settings(
                array(
                    'service_title',
                    'datetime_title',
                    'basic_details_title',
                    'summary_title',
                    'category_title',
                    'service_heading_title',
                    'timeslot_text',
                    'summary_content_text',
                    'service_duration_label',
                    'service_price_label',
                    'paypal_text',
                    'locally_text',
                    'total_amount_text',
                    'service_text',
                    'customer_text',
                    'date_time_text',
                    'payment_method_text',
                    'morning_text',
                    'afternoon_text',
                    'evening_text',
                    'night_text',
                    'goback_button_text',
                    'next_button_text',
                    'book_appointment_btn_text',
                    'book_appointment_hours_text',
                    'book_appointment_min_text',
                    'book_appointment_day_text',
                    'booking_form_tabs_position',
                    'hide_category_service_selection',                    
                    'title_font_family',
                    'content_font_family',
                    'display_service_description',
                    'all_category_title',
                    'complete_payment_deposit_amt_title',
                    'make_payment_button_title',
                    'appointment_details'
                ),
                'booking_form'
            );

            $bookingpress_fourth_tab_name = stripslashes_deep($bookingpress_customize_settings['summary_title']);
            $bookingpress_book_appointment_btn_text = stripslashes_deep($bookingpress_customize_settings['book_appointment_btn_text']);
            $bookingpress_summary_content_text = stripslashes_deep($bookingpress_customize_settings['summary_content_text']);
            $bookingpress_service_text = stripslashes_deep($bookingpress_customize_settings['service_text']);
            $bookingpress_date_time_text = stripslashes_deep($bookingpress_customize_settings['date_time_text']);
            $bookingpress_customer_text = stripslashes_deep($bookingpress_customize_settings['customer_text']);
            $bookingpress_total_amount_text = stripslashes_deep($bookingpress_customize_settings['total_amount_text']);
            $bookingpress_book_appointment_btn_text = stripslashes_deep($bookingpress_customize_settings['book_appointment_btn_text']);
            $bookingpress_book_appointment_hours_label = stripslashes_deep($bookingpress_customize_settings['book_appointment_hours_text']);
            $bookingpress_book_appointment_min_label = stripslashes_deep($bookingpress_customize_settings['book_appointment_min_text']);
            $bookingpress_book_appointment_day_label = stripslashes_deep($bookingpress_customize_settings['book_appointment_day_text']);
            $bookingpress_payment_method_text = stripslashes_deep($bookingpress_customize_settings['payment_method_text']);
            $bookingpress_locally_text = stripslashes_deep($bookingpress_customize_settings['locally_text']);
            $bookingpress_paypal_text = stripslashes_deep($bookingpress_customize_settings['paypal_text']);
            $bookingpress_appointment_details_title_text = stripslashes_deep($bookingpress_customize_settings['appointment_details']);
            $complete_payment_deposit_title = stripslashes_deep($bookingpress_customize_settings['complete_payment_deposit_amt_title']);
            $make_payment_button_title = stripslashes_deep($bookingpress_customize_settings['make_payment_button_title']);
            $bookingpress_global_options_arr = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_default_date_format = $BookingPress->bookingpress_check_common_date_format($bookingpress_global_options_arr['wp_default_date_format']);

            if(!empty($bookingpress_default_date_format)){
                $bookingpress_default_date_format = strtoupper($bookingpress_default_date_format);
            }
            $bookingpress_formatted_timeslot = $bookingpress_global_options_arr['bpa_time_format_for_timeslot'];
            $bookingpress_payment_token = !empty($_GET['bkp_pay']) ? sanitize_text_field($_GET['bkp_pay']) : '';

            $bookingpress_dynamic_data_fields = '';
            $bookingpress_dynamic_data_fields = apply_filters('bookingpress_waiting_payment_dynamic_data_fields', $bookingpress_dynamic_data_fields);        
            $bookingpress_after_selecting_payment_method_data = '';
            $bookingpress_after_selecting_payment_method_data = apply_filters('bookingpress_after_selecting_payment_method', $bookingpress_after_selecting_payment_method_data);            
            $bookingpress_add_complete_payment_method_data = '';
            $bookingpress_add_complete_payment_method_data = apply_filters('bookingpress_add_complete_payment_method', $bookingpress_add_complete_payment_method_data);

			//tip related filter add 
			$bookingpress_total_amount_payable_modify_outside = '';
			$bookingpress_total_amount_payable_modify_outside = apply_filters( 'bookingpress_total_amount_modify_outside_arr', $bookingpress_total_amount_payable_modify_outside );

            $bookingpress_modified_waiting_list_subtotal_amount_outside = "";
            $bookingpress_modified_waiting_list_subtotal_amount_outside = apply_filters( 'bookingpress_modified_waiting_list_subtotal_amount_outside', $bookingpress_modified_waiting_list_subtotal_amount_outside );


            $bookingpress_get_waiting_final_step_amount_after = '';
			$bookingpress_get_waiting_final_step_amount_after = apply_filters( 'bookingpress_get_waiting_final_step_amount_after', $bookingpress_get_waiting_final_step_amount_after );


            ob_start();
            $bookingpress_complete_payment_shortcode_file = BOOKINGPRESS_WAITING_VIEW_DIR . '/frontend/bookingpress_waiting_payment_form.php';
            include $bookingpress_complete_payment_shortcode_file;
            $content .= ob_get_clean();         
            $bookingpress_create_nonce      = wp_create_nonce( 'bpa_wp_nonce' );
            $bookingpress_script_return_data .= 'var app = new Vue({
				el: "' . $bookingpress_vue_root_element_id . '",
				data(){
					var bookingpress_return_data = ' . $bookingpress_dynamic_data_fields . ';
                    bookingpress_return_data["is_booking_form_empty_loader"] = "1";
                    bookingpress_return_data["appointment_step_form_data"]["bookingpress_uniq_id"] = "' . $bookingpress_uniq_id . '";
					var bookingpress_captcha_key = "bookingpress_captcha_' . $bookingpress_uniq_id . '";
					bookingpress_return_data["appointment_step_form_data"][bookingpress_captcha_key] = "";

					return bookingpress_return_data
				},
                filters: {
					bookingpress_format_date: function(value){
						var default_date_format = "' . $bookingpress_default_date_format . '";
						return moment(String(value)).format(default_date_format)
					},
					bookingpress_format_time: function(value){
						var default_time_format = "' . $bookingpress_formatted_timeslot . '";
						return moment(String(value), "HH:mm:ss").format(default_time_format)
					}
				},
                beforeCreate(){
					this.is_booking_form_empty_loader = "1";
				},
				created(){
					this.bookingpress_load_complete_payment_form();
				},
				mounted(){
                    this.loadSpamProtection();
                    this.expirationDate();
                    this.select_payment_method(this.appointment_step_form_data.selected_payment_method);                    
                    //this.bookingpress_recalculate_payable_amount();                                       
				},
				methods: {
                    bookingpress_load_complete_payment_form(){
                        const vm = this;
                        setTimeout(function(){
                            vm.is_booking_form_empty_loader = "0";
                            setTimeout(function(){
                                if(document.getElementById("bpa-front-tabs") != null){
                                    document.getElementById("bpa-front-tabs").style.display = "flex";
                                }
                                if(document.getElementById("bpa-front-data-empty-view") != null){
                                    document.getElementById("bpa-front-data-empty-view").style.display = "flex";
                                }
                                if(document.getElementById("bpa-complete-payment-message") != null){
                                    document.getElementById("bpa-complete-payment-message").style.visibility = "visible";
                                }
                                if(document.getElementById("bpa-payment-already-completed-message") != null){
                                    document.getElementById("bpa-payment-already-completed-message").style.visibility = "visible";
                                }
                            }, 500);
                        }, 2000);
                    },
					generateSpamCaptcha(){
						const vm = this;
                        var bkp_wpnonce_pre = "' . $bookingpress_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }
						var postData = { action: "bookingpress_generate_spam_captcha", _wpnonce:bkp_wpnonce_pre_fetch };
							axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
						.then( function (response) {
							if(response.variant != "error" && (response.data.captcha_val != "" && response.data.captcha_val != undefined)){
								//vm.appointment_step_form_data.spam_captcha = response.data.captcha_val;
							}else{
                                var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                                if(typeof bkp_wpnonce_pre_fetch!="undefined" && bkp_wpnonce_pre_fetch!=null && response.data.updated_nonce!="")
                                {
                                    document.getElementById("_wpnonce").value = response.data.updated_nonce;
                                } else {
                                    vm.$notify({
                                        title: response.data.title,
                                        message: response.data.msg,
                                        type: response.data.variant,
                                        customClass: "error_notification"
                                    });
                                }
							}
						}.bind(this) )
						.catch( function (error) {
							console.log(error);
						});
					},
					loadSpamProtection(){
						const vm = this;
						vm.generateSpamCaptcha();
					},
                    select_payment_method(payment_method){
                        const vm = this
                        vm.appointment_step_form_data.selected_payment_method = payment_method
                        var bookingpress_allowed_payment_gateways_for_card_fields = [];
                        ' . $bookingpress_after_selecting_payment_method_data . '
                        if(bookingpress_allowed_payment_gateways_for_card_fields.includes(payment_method)){
                            vm.is_display_card_option = 1;
                        }else{
                            vm.is_display_card_option = 0;
                            vm.isBookingDisabled = false
                        }                                                   
                        vm.bookingpress_get_final_step_amount();                        
                    },
                    inputFormat() {
                        let text = this.appointment_step_form_data.card_number.split(" ").join("")
                        //this.cardVdid is not formated in 4 spaces
                        this.cardVadid = text
                        if (text.length > 0) {
                            //regExp 4 in 4 number add an space between
                            text = text.match(new RegExp(/.{1,4}/, "g")).join(" ")
                                                            //accept only numbers
                                .replace(new RegExp(/[^\d]/, "ig"), " ");
                        }
                        //this.appointment_step_form_data.card_number is formated on 4 spaces
                        this.appointment_step_form_data.card_number = text
                        //after formatd they callback cardType for choose a type of the card
                        this.GetCardType(this.cardVadid)
                    },
                    //loop for the next 9 years for expire data on credit card
                    expirationDate() {
                        let yearNow = new Date().getFullYear()
                        for (let i = yearNow; i < yearNow + this.timeToExpire; i++) {
                            this.years.push({ year: i })
                        }
                    },
                    validCreditCard(value) {
                        let inputValidate = document.getElementById("cardNumber")
                        // luhn algorithm
                        let numCheck = 0,
                            bEven = false;
                        value = value.toString().replace(new RegExp(/\D/g, ""));
                        for (let n = value.length - 1; n >= 0; n--) {
                            let cDigit = value.charAt(n),
                                digit = parseInt(cDigit, 10);

                            if (bEven && (digit *= 2) > 9) digit -= 9;
                            numCheck += digit;
                            bEven = !bEven;
                        }
                        let len = value.length;
                        //true: return valid number
                        //this.cardType return true if have an valid number on regx array
                        if (numCheck % 10 === 0 && len === 16 && this.cardType) {
                            inputValidate.classList.remove("notValid")
                            inputValidate.classList.add("valid")
                            this.isBookingDisabled = false
                        }
                        //false: return not valid number
                        else if (!(numCheck % 10 === 0) && len === 16) {
                            inputValidate.classList.remove("valid")
                            inputValidate.classList.add("notValid")
                            this.isBookingDisabled = true
                            //if not have number on input
                        } else {
                            inputValidate.classList.remove("valid")
                            inputValidate.classList.remove("notValid")
                            this.isBookingDisabled = false
                        }

                    },
                    //get the name of the card name 
                    GetCardType(number) {
                        this.regx.forEach((item) => {
                            if (number.match(item.re) != null) {
                                this.cardType = item.logo
                                //cClass add a class with the name of cardName to manipulate with css
                                this.cClass = item.name.toLowerCase()
                            } else if (!number) {
                                this.cardType = ""
                                this.cClass = ""
                            }
                        })
                        //after choose a cardtype return the number for the luhn algorithm 
                        this.validCreditCard(number)
                    },
                    //mouse down on btn
                    mouseDw() {
                        this.btnClassName = "btn__active"
                    },
                    //mouse up on btn
                    mouseUp() {
                        this.btnClassName = ""
                    },
                    blr() {
                        let cr = document.getElementsByClassName("card--credit__card")[0];
                        if( null != cr && "undefined" != typeof cr.classList ){
                            cr.classList.remove("cvv-active")
                        }
                    },
                    bookingpress_waiting_payment(){
                        const vm = this;
                        vm.isLoadBookingLoader = "1";
                        vm.isBookingDisabled = true;
                        var bookingpress_postdata = { action: "bookingpress_final_waiting_payment", _wpnonce: "'.$bookingpress_nonce.'" }
                        bookingpress_postdata.waiting_payment_data = JSON.stringify( vm.appointment_step_form_data );
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_postdata ) )
                        .then( function (response){
                            vm.isLoadBookingLoader = "0";
                            vm.isBookingDisabled = false;
                            var bookingpress_uniq_id = vm.appointment_step_form_data.bookingpress_uniq_id;
                            if(response.data.variant != "error"){
                                if(response.data.variant == "redirect"){
                                    document.body.innerHTML += response.data.redirect_data;
                                    var scripts = document.querySelectorAll("script");
                                    var text = scripts[scripts.length - 1].textContent;
                                    eval(text);
                                }else if(response.data.variant == "redirect_url" && typeof response.data.is_transaction_completed != "undefined" && response.data.is_transaction_completed == "1"){
                                    vm.waiting_payment_success_msg = response.data.msg;
                                }else if(response.data.variant == "redirect_url"){
                                    window.location.href = response.data.redirect_data
                                }else{
                                    vm.waiting_payment_success_msg = response.data.msg;
                                }
                                setTimeout(function(){
                                    if(document.getElementById("bpa-complete-payment-message") != null){
                                        document.getElementById("bpa-complete-payment-message").style.visibility = "visible";
                                    }
                                }, 1000);
                            }else{
                                let error_msg = response.data.msg;
                                vm.bookingpress_set_waiting_payment_error_msg( error_msg );
                            }
                        }
                        .bind( this ) )
                        .catch( function (error) {
                            console.log(error);
                        });
                    },
                    bookingpress_set_waiting_payment_error_msg( error_msg ){
                        const vm = this
                        let container = vm.$el;
                        let pos = 0;
                        if( null != container ){
                            pos = container.getBoundingClientRect().top + window.scrollY;
                        }
                        vm.is_display_waiting_payment_error = "1" 
                        vm.is_waitng_payment_error_msg = error_msg
                        window.scrollTo({
                            top: pos,
                            behavior: "smooth",
                        });
                        setTimeout(function(){
                            vm.bookingpress_remove_waiting_payment_error_msg()
                        },5000);
                    },
                    bookingpress_remove_waiting_payment_error_msg(){
                        const vm = this
                        vm.is_display_waiting_payment_error = "0"
                        vm.is_waitng_payment_error_msg = ""
                    },                    
                    bookingpress_recalculate_payable_amount(){
                        
                        const vm = this
                        var bookingpress_recalculate_data = {};
                        bookingpress_recalculate_data.action = "bookingpress_recalculate_appointment_data";
                        bookingpress_recalculate_data.appointment_details = JSON.stringify( vm.appointment_step_form_data );

                        var bkp_wpnonce_pre = "' . $bookingpress_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }

                        bookingpress_recalculate_data._wpnonce = bkp_wpnonce_pre_fetch;
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_recalculate_data ) )
                        .then( function (response) {
                            var newdata = response.data.appointment_data;
                            vm.appointment_step_form_data = response.data.appointment_data;
                            if(newdata.tax_amount_without_currency){
                                vm.appointment_step_form_data.tax_amount = vm.bookingpress_price_with_currency_symbol( newdata.tax_amount_without_currency );
                            }
                            setTimeout(function(){
                                vm.bookingpress_get_final_step_amount();
                            });                            
                        }.bind(this) )
                        .catch( function (error) {
                            vm.bookingpress_set_error_msg(error)
                        });

                    },
                    bookingpress_set_error_msg(error_msg){
                        const vm = this
                        let container = vm.$el;
                        let pos = 0;
                        if( null != container ){
                            pos = container.getBoundingClientRect().top + window.scrollY;
                        }
                        vm.is_display_error = "1"
                        vm.is_error_msg = error_msg
                        window.scrollTo({
                            top: pos,
                            behavior: "smooth",
                        });
                        setTimeout(function(){
                            vm.bookingpress_remove_error_msg()
                        },3000);
                    }, 
                    bookingpress_remove_error_msg(){
                        const vm = this
                        vm.is_display_error = "0"
                        vm.is_error_msg = ""
                    },

                    bookingpress_complete_payment_apply_coupon(){

                        const vm = this
                        vm.coupon_apply_loader = "1"
                        var bookingpress_apply_coupon_data = {};
                        bookingpress_apply_coupon_data.action = "bookingpress_apply_coupon_code";
                        bookingpress_apply_coupon_data.appointment_details = JSON.stringify( vm.appointment_step_form_data );
    
                        var bkp_wpnonce_pre = "' . $bookingpress_create_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }
                        bookingpress_apply_coupon_data._wpnonce = bkp_wpnonce_pre_fetch;
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_coupon_data ) )
                        .then( function (response) {
                            vm.coupon_apply_loader = "0"
                            vm.coupon_applied_status = response.data.variant;
                            if(response.data.variant == "error"){
                                vm.coupon_code_msg = response.data.msg
                                vm.appointment_step_form_data.coupon_discount_amount = 0;
                            }else{
                                vm.coupon_code_msg = response.data.msg
                                vm.coupon_discounted_amount = "-" + response.data.discounted_amount
                                vm.bpa_coupon_apply_disabled = 1
                                vm.appointment_step_form_data.applied_coupon_res = { "coupon_data": response.data.coupon_data };
                            }
                            
                            if(response.data.coupon_discount_amount > 0 )
                            {
                                vm.appointment_step_form_data.coupon_discount_amount = response.data.coupon_discount_amount;
                                vm.appointment_step_form_data.coupon_discount_amount_with_currecny = response.data.coupon_discount_amount_with_currecny;
                                vm.appointment_step_form_data.total_payable_amount_with_currency = response.data.total_payable_amount_with_currency;
                                vm.appointment_step_form_data.total_payable_amount = response.data.total_payable_amount;							
                            }
                            vm.bookingpress_get_final_step_amount()
                        
                        }.bind(this) )
                        .catch( function (error) {
                            vm.bookingpress_set_error_msg(error)
                        });
                    },
                    bookingpress_remove_coupon_code(){
                        const vm = this
                        vm.appointment_step_form_data.coupon_code = ""
                        vm.coupon_code_msg = ""                        
                        vm.bpa_coupon_apply_disabled = 0
                        vm.coupon_applied_status = "error"
                        vm.coupon_discounted_amount = ""
                        vm.appointment_step_form_data.coupon_discount_amount = 0;                        
                        vm.bookingpress_get_final_step_amount()                        
                    },
                    ' . $bookingpress_add_complete_payment_method_data . '
                    bookingpress_price_with_currency_symbol( price_amount ){
                        const vm = this;
                        if( "String" == typeof price_amount ){
                            price_amount = parseFloat( price_amount );
                        }
                        
                        let currency_separator = vm.bookingpress_currency_separator;
                        let decimal_points = vm.bookingpress_decimal_points;

                        if( "comma-dot" == currency_separator ){
                            price_amount = vm.bookingpress_number_format( price_amount, decimal_points, ".", "," );
                        } else if( "dot-comma" == currency_separator ){
                            price_amount = vm.bookingpress_number_format( price_amount, decimal_points, ",", "." );
                        } else if( "space-dot" == currency_separator ){
                            price_amount = vm.bookingpress_number_format( price_amount, decimal_points, ".", " " );
                        } else if( "space-comma" == currency_separator ){
                            price_amount = vm.bookingpress_number_format( price_amount, decimal_points, ",", " " );
                        } else if( "custom" == currency_separator ){
                            let custom_comma_separator = vm.bookingpress_custom_comma_separator;
                            let custom_thousand_separator = vm.bookingpress_custom_thousand_separator;
                            price_amount = vm.bookingpress_number_format( price_amount, decimal_points, custom_comma_separator, custom_thousand_separator );
                        }

                        let currency_symbol = vm.bookingpress_currency_symbol;
                        let currency_symbol_pos = vm.bookingpress_currency_symbol_position;

                        if( "before" == currency_symbol_pos ){
                            price_amount = currency_symbol + price_amount;
                        } else if( "before_with_space" == currency_symbol_pos ){
                            price_amount = currency_symbol + " " + price_amount;
                        } else if( "after" == currency_symbol_pos ){
                            price_amount = price_amount + currency_symbol;
                        } else if( "after_with_space" == currency_symbol_pos ){
                            price_amount = price_amount + " " + currency_symbol;
                        }

                        
                        return price_amount;

                    },
                    bookingpress_number_format( number, decimals, decPoint, thousandsSep ){
                        number = (number + "").replace(/[^0-9+\-Ee.]/g, "")
                        const n = !isFinite(+number) ? 0 : +number
                        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
                        const sep = (typeof thousandsSep === "undefined") ? "," : thousandsSep
                        const dec = (typeof decPoint === "undefined") ? "." : decPoint
                        let s = ""
                        const toFixedFix = function (n, prec) {
                            if (("" + n).indexOf("e") === -1) {
                            return +(Math.round(n + "e+" + prec) + "e-" + prec)
                            } else {
                            const arr = ("" + n).split("e")
                            let sig = ""
                            if (+arr[1] + prec > 0) {
                                sig = "+"
                            }
                            return (+(Math.round(+arr[0] + "e" + sig + (+arr[1] + prec)) + "e-" + prec)).toFixed(prec)
                            }
                        }
                        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
                        s = (prec ? toFixedFix(n, prec).toString() : "" + Math.round(n)).split(".")
                        if (s[0].length > 3) {
                            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
                        }
                        if ((s[1] || "").length < prec) {
                            s[1] = s[1] || ""
                            s[1] += new Array(prec - s[1].length + 1).join("0")
                        }
                        return s.join(dec)
                    },                 
                    bookingpress_get_final_step_amount() {
                        const vm = this;
                            var payment_method = vm.appointment_step_form_data.selected_payment_method;
    
                            var total_payable_amount = vm.appointment_step_form_data.service_price_without_currency;
                            var tax_amount = vm.appointment_step_form_data.tax_amount_without_currency;
                            if( "" == tax_amount ){
                                tax_amount = 0;
                            }                
                            '.$bookingpress_modified_waiting_list_subtotal_amount_outside.'               
                            let total_payable_amount_without_tax = parseFloat(total_payable_amount);
                            if(typeof tax_amount != "undefined"){
                                total_payable_amount = parseFloat(total_payable_amount) + parseFloat(tax_amount);
                            }
                            let is_cart_addon = false;                            
                            var coupon_code = vm.appointment_step_form_data.coupon_code;
                            var selected_service = vm.appointment_step_form_data.selected_service;
                            var selected_staff_member_id = vm.appointment_step_form_data.selected_staff_member_id;		
                            
                            vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( total_payable_amount );
                            vm.appointment_step_form_data.total_payable_amount = total_payable_amount;
    
                            
                            //var subtotal_price =  vm.bookingpress_price_with_currency_symbol( total_payable_amount, true );
                            subtotal_price = total_payable_amount;
                            
                                if( 1 == vm.is_coupon_activated ){
                                    if(vm.appointment_step_form_data.coupon_code != ""){
                                        if(typeof vm.appointment_step_form_data.coupon_discount_amount != "undefined"){
    
                                            vm.appointment_step_form_data.total_payable_amount = total_payable_amount - vm.appointment_step_form_data.coupon_discount_amount;
                                            
                                            vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.total_payable_amount );
                                            
                                            subtotal_price = total_payable_amount - vm.appointment_step_form_data.coupon_discount_amount;
                                        }
                                    } else {
                                        vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( total_payable_amount );
                                        vm.appointment_step_form_data.total_payable_amount = total_payable_amount;
    
                                        subtotal_price = total_payable_amount;
                                    }
                                }
                                
                                '.$bookingpress_total_amount_payable_modify_outside.'
                                
                                //If deposit payment module enabled then calculate deposit amount
                                var deposit_method = vm.appointment_step_form_data.bookingpress_deposit_payment_method;
                                var deposit_type = vm.appointment_step_form_data.deposit_payment_type;
                                var deposit_value = vm.appointment_step_form_data.deposit_payment_amount;
                                var bookingpress_deposit_amt = 0;
                                var bookingpress_deposit_due_amt = 0;                                

                                if(payment_method != "" && 1 == vm.bookingpress_is_deposit_payment_activate){ 
                                    if(payment_method != "on-site"){                                        
                                        if(deposit_method == "deposit_or_full_price"){
                                            if( true == is_cart_addon ){
                                                //subtotal_price = vm.bookingpress_price_with_currency_symbol( total_payable_amount_without_tax, true );
                                            }
                                            if(deposit_type == "percentage"){
                                                bookingpress_deposit_amt = subtotal_price * ( parseFloat(deposit_value) / 100);
                                                bookingpress_deposit_due_amt = parseFloat(subtotal_price - bookingpress_deposit_amt);
                                                //bookingpress_deposit_amt = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_amt, true );                                                
                                            } else if(deposit_type == "fixed") {
                                                bookingpress_deposit_amt = deposit_value
                                                bookingpress_deposit_due_amt = subtotal_price - bookingpress_deposit_amt;
                                            }
                                        } else if(deposit_method == "allow_customer_to_pay_full_amount") {
                                            bookingpress_deposit_amt = subtotal_price
                                            bookingpress_deposit_due_amt = subtotal_price - bookingpress_deposit_amt;
                                        }
    
                                        vm.appointment_step_form_data.bookingpress_deposit_amt = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_amt );
                                        vm.appointment_step_form_data.bookingpress_deposit_amt_without_currency = bookingpress_deposit_amt;
                                        vm.appointment_step_form_data.bookingpress_deposit_due_amt = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_due_amt );
                                        vm.appointment_step_form_data.bookingpress_deposit_due_amt_without_currency = bookingpress_deposit_due_amt;
                                        vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_amt );
                                        vm.appointment_step_form_data.total_payable_amount = bookingpress_deposit_amt;
    
                                        //26 April 2023 changes
                                        if( 1 == is_cart_addon ){
                                            if( "allow_customer_to_pay_full_amount" == deposit_method ){
                                                vm.appointment_step_form_data.bookingpress_deposit_due_amount_total = bookingpress_deposit_due_amt + tax_amount;
                                                vm.appointment_step_form_data.bookingpress_deposit_due_amount_total_with_currency = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_due_amt + tax_amount );
                                            } else {
    
                                                if( 1 == vm.is_tax_activated ){
                                                    //let tax_method = vm.appointment_step_form_data.tax_price_display_options;
    
                                                    /* if( "exclude_taxes" == tax_method ){ */
                                                        let bpa_deposit_due_amount_total = ( parseFloat( total_payable_amount ) - parseFloat( vm.appointment_step_form_data.bookingpress_deposit_total ) );	
                                                        
                                                        if( 1 == vm.is_coupon_activated){
                                                            let coupon_discount = vm.appointment_step_form_data.coupon_discount_amount;
                                                            vm.appointment_step_form_data.bookingpress_deposit_due_amount_total = bpa_deposit_due_amount_total - coupon_discount;
                                                            vm.appointment_step_form_data.bookingpress_deposit_due_amount_total_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.bookingpress_deposit_due_amount_total );
                                                        } else {
                                                            vm.appointment_step_form_data.bookingpress_deposit_due_amount_total = bpa_deposit_due_amount_total;
                                                            vm.appointment_step_form_data.bookingpress_deposit_due_amount_total_with_currency = vm.bookingpress_price_with_currency_symbol( bpa_deposit_due_amount_total );
                                                        }
                                                } else {	
                                                    let bpa_deposit_due_amount_total = ( parseFloat( total_payable_amount ) - parseFloat( vm.appointment_step_form_data.bookingpress_deposit_total ) );
                                                    vm.appointment_step_form_data.bookingpress_deposit_due_amount_total = bpa_deposit_due_amount_total;
                                                    vm.appointment_step_form_data.bookingpress_deposit_due_amount_total_with_currency = vm.bookingpress_price_with_currency_symbol( bpa_deposit_due_amount_total );
                                                }
                                            }
                                        }
                                        //26 April 2023 changes
                                        
                                    }
                                    else
                                    {
                                        vm.appointment_step_form_data.bookingpress_deposit_amt = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_amt );
                                        vm.appointment_step_form_data.bookingpress_deposit_amt_without_currency = bookingpress_deposit_amt;
                                        vm.appointment_step_form_data.bookingpress_deposit_due_amt = vm.bookingpress_price_with_currency_symbol( bookingpress_deposit_due_amt );
                                        vm.appointment_step_form_data.bookingpress_deposit_due_amt_without_currency = bookingpress_deposit_due_amt;
                                        vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( subtotal_price );
                                        vm.appointment_step_form_data.total_payable_amount = subtotal_price;
                                    }
                                }		
                                
                                '.$bookingpress_get_waiting_final_step_amount_after.'
                    },
                    //Add New mwthod here
				},
			});';

            $bpa_script_data = "window.addEventListener('DOMContentLoaded', function() {
                {$bookingpress_script_return_data}
            });";

            wp_add_inline_script('bookingpress_elements_locale', $bpa_script_data, 'after');

            $bookingpress_custom_css = $BookingPress->bookingpress_get_customize_settings('custom_css', 'booking_form');
            wp_add_inline_style( 'bookingpress_front_custom_css', $bookingpress_custom_css, 'after' );
            return do_shortcode( $content );
        }
        
		/**
		 * Function for add customize form settings data to save request before data save
         * 
		 * @return void
		 */
        function bookingpress_before_save_customize_form_settings_func(){
        ?>
            postData.waiting_list_container_data = vm2.waiting_list_container_data;
        <?php
        }
        
		/**
		 * Function for execute code brfore save customize booking form data
		 *
		 * @param  mixed $booking_form_settings
		 * @return void
		 */
        function bookingpress_before_save_customize_booking_form_func($booking_form_settings_data){
            
            global $BookingPress;
            $booking_form_settings_data['waiting_list_container_data'] = !empty($_POST['waiting_list_container_data']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_POST['waiting_list_container_data']) : array(); // phpcs:ignore
            return $booking_form_settings_data;

        }

		/**
		 * Function for add data variables for customize page
		 *
		 * @param  mixed $booking_form_settings
		 * @return void
		*/        
		function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings){

			$booking_form_settings['waiting_list_container_data']['waiting_slot_label'] = __('Waiting Slot', 'bookingpress-waiting-list');
            $booking_form_settings['waiting_list_container_data']['waiting_book_button_label'] = __('Add To Waiting', 'bookingpress-waiting-list');            
            $booking_form_settings['waiting_list_container_data']['waiting_payable_amount_label'] = __('Final Amount', 'bookingpress-waiting-list');    			
            $booking_form_settings['waiting_list_container_data']['waiting_position_label'] = __('Your waiting position is', 'bookingpress-waiting-list');            
            $booking_form_settings['waiting_list_container_data']['waiting_payment_success_message'] = __('Your appointment successfully booked.', 'bookingpress-waiting-list');

            return $booking_form_settings;

		}
		
		/**
		 * Function for add dynamic field to customize page
		 *
		 * @param  mixed $bookingpress_customize_vue_data_fields
		 * @return void
		 */
        function bookingpress_customize_add_dynamic_data_fields_func($bookingpress_customize_vue_data_fields) {

            global $BookingPress;
            $waiting_slot_label = $BookingPress->bookingpress_get_customize_settings('waiting_slot_label','booking_form');
            $waiting_book_button_label = $BookingPress->bookingpress_get_customize_settings('waiting_book_button_label','booking_form');
            $waiting_payable_amount_label = $BookingPress->bookingpress_get_customize_settings('waiting_payable_amount_label','booking_form');
            $waiting_position_label = $BookingPress->bookingpress_get_customize_settings('waiting_position_label','booking_form');
            $waiting_list_text_color = $BookingPress->bookingpress_get_customize_settings('waiting_list_text_color','booking_form');
            $waiting_payment_success_message = $BookingPress->bookingpress_get_customize_settings('waiting_payment_success_message','booking_form');

            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_slot_label'] = (!empty($waiting_slot_label))?stripslashes_deep($waiting_slot_label):'';
            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_book_button_label'] = (!empty($waiting_book_button_label))?stripslashes_deep($waiting_book_button_label):'';
            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_payable_amount_label'] = (!empty($waiting_payable_amount_label))?stripslashes_deep($waiting_payable_amount_label):'';
            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_position_label'] = (!empty($waiting_position_label))?stripslashes_deep($waiting_position_label):'';
            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_list_text_color'] = (!empty($waiting_list_text_color))?stripslashes_deep($waiting_list_text_color):'';
            $bookingpress_customize_vue_data_fields['waiting_list_container_data']['waiting_payment_success_message'] = (!empty($waiting_payment_success_message))?stripslashes_deep($waiting_payment_success_message):'';
            $bookingpress_sidebar_step_data = $bookingpress_customize_vue_data_fields['bookingpress_form_sequance_arr'];

            return $bookingpress_customize_vue_data_fields;

		}        
                
        /**
         * Function For add field HTML to customize page
         *
         * @return void
         */
        public function bookingpress_add_bookingform_label_data_func(){
        ?>
            <h5><?php esc_html_e('Waiting List labels', 'bookingpress-waiting-list'); ?></h5>                                                    
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Waiting Slot Label', 'bookingpress-waiting-list'); ?></label>
                <el-input v-model="waiting_list_container_data.waiting_slot_label" class="bpa-form-control"></el-input>
            </div>         
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Waiting List Button Label', 'bookingpress-waiting-list'); ?></label>
                <el-input type="textarea" :rows="2" v-model="waiting_list_container_data.waiting_book_button_label"> </el-input>
            </div> 
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Waiting List Payable Amount Label', 'bookingpress-waiting-list'); ?></label>
                <el-input type="textarea" :rows="2" v-model="waiting_list_container_data.waiting_payable_amount_label"> </el-input>
            </div> 
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Waiting Position label', 'bookingpress-waiting-list'); ?></label>
                <el-input type="textarea" :rows="2" v-model="waiting_list_container_data.waiting_position_label"> </el-input>
            </div>             
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Waiting Payment Success Message', 'bookingpress-waiting-list'); ?></label>
                <el-input type="textarea" :rows="2" v-model="waiting_list_container_data.waiting_payment_success_message"> </el-input>
            </div>             
        <?php
        }
        
        /**
         * Function for after cancel appointment.
         *
         * @param  mixed $appointment_id
         * @param  mixed $appointment_status
         * @return void
        */
        public function bookingpress_after_cancel_appointment_fun($appointment_id, $appointment_status = '3'){
            
            global $wpdb,$tbl_bookingpress_appointment_bookings,$BookingPress,$bookingpress_email_notifications;           
            if($appointment_id){

                $bookingpress_get_appointment_record = $wpdb->get_row($wpdb->prepare( "SELECT bookingpress_appointment_booking_id, bookingpress_appointment_status, bookingpress_service_id, bookingpress_appointment_date, bookingpress_appointment_time, bookingpress_appointment_end_time, bookingpress_staff_member_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d",$appointment_id), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                if(!empty($bookingpress_get_appointment_record)){                    

                    if($bookingpress_get_appointment_record['bookingpress_appointment_status'] == 3 || $bookingpress_get_appointment_record['bookingpress_appointment_status'] == 4){                    
                        $bookingpress_appointment_booking_id = $bookingpress_get_appointment_record['bookingpress_appointment_booking_id'];
                        $bookingpress_service_id = $bookingpress_get_appointment_record['bookingpress_service_id'];
                        $bookingpress_appointment_date = $bookingpress_get_appointment_record['bookingpress_appointment_date'];
                        $bookingpress_appointment_time = $bookingpress_get_appointment_record['bookingpress_appointment_time'];
                        $bookingpress_appointment_end_time = $bookingpress_get_appointment_record['bookingpress_appointment_end_time'];
                        $bookingpress_staff_member_id = $bookingpress_get_appointment_record['bookingpress_staff_member_id'];
                        $where_clause = '';
                        if( !empty( $bookingpress_staff_member_id ) ){
                            $where_clause .= $wpdb->prepare( "AND bookingpress_staff_member_id = %d", $bookingpress_staff_member_id );
                        }
                        $bookingpress_avaliable_time_slot = $this->bookingpress_check_timeslot_is_avaliable_or_not($appointment_id,true,$bookingpress_get_appointment_record['bookingpress_appointment_status']);                        
                        $bookingpress_all_customers = $wpdb->get_results($wpdb->prepare("SELECT bookingpress_appointment_booking_id,bookingpress_customer_email FROM {$tbl_bookingpress_appointment_bookings}  WHERE bookingpress_service_id = %d AND bookingpress_appointment_date = %s  AND (bookingpress_appointment_time >= %s AND bookingpress_appointment_end_time <= %s)  AND (bookingpress_appointment_status = %s) AND (bookingpress_selected_extra_members <= %s) {$where_clause}", $bookingpress_service_id, $bookingpress_appointment_date, $bookingpress_appointment_time,$bookingpress_appointment_end_time, '7',$bookingpress_avaliable_time_slot),ARRAY_A); // phpcs:ignore                                                 
                        if(!empty($bookingpress_all_customers)){
                            $i=2;
                            foreach($bookingpress_all_customers as $all_custmr){
                                $bookingpress_email_notifications->bookingpress_send_email_notification('customer', 'Set Appointment From Waiting', $all_custmr['bookingpress_appointment_booking_id'], $all_custmr['bookingpress_customer_email']);                                
                                //do_action('bookingpress_send_custom_status_sms_notification',$all_custmr['bookingpress_appointment_booking_id'],'Set Appointment From Waiting');
                                //do_action('bookingpress_send_custom_status_whatsapp_notification',$all_custmr['bookingpress_appointment_booking_id'],'Set Appointment From Waiting');
                                if($i == 2){                                    
                                    $bookingpress_admin_emails = esc_html($BookingPress->bookingpress_get_settings('admin_email', 'notification_setting'));
                                    $bookingpress_cc_emails = array();
                                    $bookingpress_cc_emails = apply_filters('bookingpress_add_cc_email_address', $bookingpress_cc_emails, 'Set Appointment From Waiting');
                                    $bookingpress_admin_emails = explode(',', $bookingpress_admin_emails);
                                    $bookingpress_admin_emails = apply_filters('bookingpress_filter_admin_email_data',  $bookingpress_admin_emails, $all_custmr['bookingpress_appointment_booking_id'], 'Set Appointment From Waiting');                                                                                                            
                                    if(!is_array($bookingpress_admin_emails)){
                                        $bookingpress_admin_emails = array($bookingpress_admin_emails);
                                    }                                    
                                    foreach ( $bookingpress_admin_emails as $admin_email_key => $admin_email_val ) {                                                                                  
                                        $bookingpress_email_notifications->bookingpress_send_email_notification('employee', 'Set Appointment From Waiting', $all_custmr['bookingpress_appointment_booking_id'], $admin_email_val, $bookingpress_cc_emails);                                        
                                        do_action('bookingpress_send_custom_status_sms_notification',$all_custmr['bookingpress_appointment_booking_id'],'Set Appointment From Waiting');        
                                        do_action('bookingpress_send_custom_status_whatsapp_notification',$all_custmr['bookingpress_appointment_booking_id'],'Set Appointment From Waiting');
                                    }
                                }                                
                                $i = $i+2;                              
                            }
                        }                                                
                    }
                    
                }
            }
            
        }

        
        /**
         * Function for check waiting list timeslot.
         *
         * @param  mixed $bookingpress_appointment_id
         * @param  mixed $get_avaliable_appointment_count
         * @param  mixed $bookingpress_appointment_status
         * @return void
         */
        function bookingpress_check_timeslot_is_avaliable_or_not($bookingpress_appointment_id,$get_avaliable_appointment_count=false,$bookingpress_appointment_status = 7){

            global $wpdb,$tbl_bookingpress_appointment_bookings,$BookingPress,$tbl_bookingpress_staffmembers_services,$bookingpress_services,$BookingPress;
            $response = array();
            $bookingpress_avaliable_appointment_timeslot_count = 0;

            $bookingpress_timeslot_is_not_avaliable = 0;            
            $bookingpress_get_appointment_record = $wpdb->get_row($wpdb->prepare( "
            SELECT bookingpress_selected_extra_members,bookingpress_appointment_booking_id, bookingpress_service_duration_unit,bookingpress_service_duration_val,bookingpress_appointment_status, 
            bookingpress_service_id, bookingpress_appointment_date, bookingpress_appointment_time,
            bookingpress_appointment_end_time, bookingpress_staff_member_id
            FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d",$bookingpress_appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
            if(!empty($bookingpress_get_appointment_record)){
                if($bookingpress_get_appointment_record['bookingpress_appointment_status'] == $bookingpress_appointment_status){                 

                     $bookingpress_appointment_booking_id = $bookingpress_get_appointment_record['bookingpress_appointment_booking_id'];
                     $bookingpress_service_id = $bookingpress_get_appointment_record['bookingpress_service_id'];
                     $bookingpress_appointment_date = $bookingpress_get_appointment_record['bookingpress_appointment_date'];
                     $bookingpress_appointment_time = $bookingpress_get_appointment_record['bookingpress_appointment_time'];
                     $bookingpress_appointment_end_time = $bookingpress_get_appointment_record['bookingpress_appointment_end_time'];
                     $bookingpress_staff_member_id = $bookingpress_get_appointment_record['bookingpress_staff_member_id'];
                     
                     $bookingpress_service_duration_val = $bookingpress_get_appointment_record['bookingpress_service_duration_val'];                     
                     $bookingpress_service_duration_unit = $bookingpress_get_appointment_record['bookingpress_service_duration_unit'];

                     if($bookingpress_staff_member_id){
                         $bookingpress_service_capacity = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_capacity FROM $tbl_bookingpress_staffmembers_services WHERE bookingpress_staffmember_id = %d  AND bookingpress_service_id = %d", $bookingpress_staff_member_id,  $bookingpress_service_id ) );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers_services is table name defined globally. False Positive alarm
                     }else{
                         $bookingpress_service_capacity = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id, 'max_capacity');
                     }
                     //Add For Multi Day Services Booking Avaliability                                          
                     $bookingpress_shared_service_timeslot = $BookingPress->bookingpress_get_settings('share_timeslot_between_services', 'general_setting');
                     if($bookingpress_shared_service_timeslot == 'true'){                                            
                         $is_appointment_exists = $wpdb->get_var($wpdb->prepare("SELECT SUM(bookingpress_selected_extra_members) as total  FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_date = %s AND (bookingpress_appointment_time >= %s AND bookingpress_appointment_end_time <= %s) AND (bookingpress_appointment_status = %s OR bookingpress_appointment_status = %s)", $bookingpress_appointment_date, $bookingpress_appointment_time,$bookingpress_appointment_end_time, '1', '2'));// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                     }else{ 
                         $where_clause = '';
                         if( !empty( $bookingpress_staff_member_id ) ){
                             $where_clause .= $wpdb->prepare( "AND bookingpress_staff_member_id = %d", $bookingpress_staff_member_id );
                         }
                         $is_appointment_exists = $wpdb->get_var($wpdb->prepare("SELECT SUM(bookingpress_selected_extra_members) as total FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_service_id = %d AND bookingpress_appointment_date = %s  AND (bookingpress_appointment_time >= %s AND bookingpress_appointment_end_time <= %s) AND (bookingpress_appointment_status = %s OR bookingpress_appointment_status = %s ) {$where_clause}", $bookingpress_service_id, $bookingpress_appointment_date, $bookingpress_appointment_time,$bookingpress_appointment_end_time, '1', '2')); // phpcs:ignore
                     }                    
                     $is_appointment_exists = (int)$is_appointment_exists;
                     if($is_appointment_exists >= $bookingpress_service_capacity){
                        if($get_avaliable_appointment_count){
                            return $bookingpress_avaliable_appointment_timeslot_count;
                         }
                         $response = array();
                         $response['variant'] = 'error';
                         $response['title']   = esc_html__( 'Error', 'bookingpress-waiting-list' );
                         $response['msg']     = esc_html__( 'Sorry, Your request can not be processed because of appoinment not avaliable for same timeslot.', 'bookingpress-waiting-list' );
                         return $response;                         
                     }else{
                        $bookingpress_avaliable_appointment_timeslot_count =  $bookingpress_service_capacity - $is_appointment_exists;
                        $bookingpress_get_appointment_capacity = $bookingpress_get_appointment_record['bookingpress_selected_extra_members'];
                        if($bookingpress_avaliable_appointment_timeslot_count < $bookingpress_get_appointment_capacity){
                            if($get_avaliable_appointment_count){
                                return $bookingpress_avaliable_appointment_timeslot_count;
                             }
                             $response = array();
                             $response['variant'] = 'error';
                             $response['title']   = esc_html__( 'Error', 'bookingpress-waiting-list' );
                             $response['msg']     = esc_html__( 'Sorry, Your request can not be processed because of appoinment not avaliable for same timeslot.', 'bookingpress-waiting-list' );
                             return $response;                            
                        }

                     }

                }
             }             
             if($get_avaliable_appointment_count){
                return $bookingpress_avaliable_appointment_timeslot_count;
             }
             $response = array();
             $response['variant'] = 'success';
             $response['title']   = '';
             $response['msg']     = '';
             return $response;
        }
        
        /**
         * Function for modify complete payment data after entry create
         *
         * @param  mixed $bookingpress_waiting_payment_data_vars
         * @param  mixed $bookingpress_appointment_details
         * @return void
         */
        function modify_complate_payment_data_after_entry_create_func($bookingpress_waiting_payment_data_vars, $bookingpress_appointment_details){
            $bookingpress_waiting_payment_data_vars['appointment_step_form_data']['is_waiting_list'] = 0;
            return $bookingpress_waiting_payment_data_vars;
        }
        
        /**
         *Function for modify appointment booking fields before insert fun
         *
         * @param  mixed $appointment_booking_fields
         * @param  mixed $entry_data
         * @return void
         */
        function bookingpress_modify_appointment_booking_fields_before_insert_fun($appointment_booking_fields, $entry_data){            
            if(isset($entry_data['bookingpress_waiting_payment_token']) && !empty($entry_data['bookingpress_waiting_payment_token'])){
                $appointment_booking_fields['bookingpress_waiting_payment_token'] = $entry_data['bookingpress_waiting_payment_token'];
            }            
            return $appointment_booking_fields;
        }
        
        /**
         * Function for save service details
         *
         * @param  mixed $response
         * @param  mixed $service_id
         * @param  mixed $posted_data
         * @return void
         */
        function bookingpress_save_service_details( $response, $service_id, $posted_data ){
            global $BookingPress, $bookingpress_services;
            if ( ! empty( $service_id ) && ! empty( $posted_data ) ) {
				$service_waiting_list_max_slot = ! empty( $posted_data['waiting_list_max_slot'] ) ? $posted_data['waiting_list_max_slot'] : 0;                                
				if ( ! empty( $service_waiting_list_max_slot ) || $service_waiting_list_max_slot == 0) {                                        
					$bookingpress_services->bookingpress_add_service_meta( $service_id, 'waiting_list_max_slot', $service_waiting_list_max_slot );
				}	
            }
            return $response;
        }
        
        /**
         * Function for edit service more vue data
         *
         * @return void
         */
        function bookingpress_edit_service_more_vue_data_func(){
        ?>
            vm2.service.waiting_list_max_slot = (response.data.waiting_list_max_slot !== undefined) ? response.data.waiting_list_max_slot : 0;
        <?php
        }
        
        /**
         * Function for modify edit service data
         *
         * @param  mixed $response
         * @param  mixed $service_id
         * @return void
         */
        function bookingpress_modify_edit_service_data_func( $response,$service_id ) {
            global $bookingpress_services, $default_waiting_list_max_slot;
            $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($service_id, 'waiting_list_max_slot');
            if(!empty($bookingpress_waiting_list_max_slot) || $bookingpress_waiting_list_max_slot == 0){
                $bookingpress_waiting_list_max_slot = (int)$bookingpress_waiting_list_max_slot;
            }
            $response['waiting_list_max_slot'] = (empty($bookingpress_waiting_list_max_slot) && $bookingpress_waiting_list_max_slot != 0)?$default_waiting_list_max_slot:$bookingpress_waiting_list_max_slot;
            return $response;
        }
        
        /**
         * Function for modify service data fields
         *
         * @param  mixed $bookingpress_services_vue_data_fields
         * @return void
        */
        public function bookingpress_modify_service_data_fields_func($bookingpress_services_vue_data_fields){            
            global $bookingpress_services, $default_waiting_list_max_slot;                      
            $bookingpress_services_vue_data_fields['service']['waiting_list_max_slot'] = $default_waiting_list_max_slot;
            $bookingpress_services_vue_data_fields['is_waiting_list_activated']        = 1; 
            return $bookingpress_services_vue_data_fields;
        }
        
        /**
         * Function for add new fields with service deposit in service
         *
         * @return void
        */
        public function bookingpress_add_service_deposit_field_inside_fun(){        
        ?>    
            <el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                <el-form-item>
                    <template #label>
                        <span class="bpa-form-label"><?php esc_html_e( 'Waiting List Max Time Slot Limit', 'bookingpress-waiting-list' ); ?></span>            
                    </template>
                    <el-input-number class="bpa-form-control bpa-form-control--number"  :min="0" :max="999" v-model="service.waiting_list_max_slot" id="waiting_list_max_slot" name="waiting_list_max_slot" step-strictly></el-input-number>
                    <span class="bpa-sm__field-helper-label"><?php esc_html_e( 'Add 0 to remove waiting list.', 'bookingpress-waiting-list' ); ?></span>
                </el-form-item>
            </el-col>        
        <?php    
        }
        
        /**
         * Function for modify cart entry data before insert
         *
         * @param  mixed $bookingpress_entry_details
         * @param  mixed $posted_data
         * @param  mixed $v
         * @return void
         */
        public function bookingpress_modify_cart_entry_data_before_insert_fun($bookingpress_entry_details, $posted_data,$v){
            if(isset($posted_data['is_waiting_list'])){
                if($posted_data['is_waiting_list'] == '1'){
                    $bookingpress_entry_details['bookingpress_appointment_status'] = 7;
                    $bookingpress_entry_details['bookingpress_deposit_payment_details'] = wp_json_encode(array());
                    $bookingpress_entry_details['bookingpress_deposit_amount'] = 0;
                    $bookingpress_entry_details['bookingpress_waiting_payment_token'] = uniqid("bpa", true);
                    $bookingpress_entry_details['bookingpress_mark_as_paid'] = 0;
                    $bookingpress_entry_details['bookingpress_is_cart'] = 0;
                }
            }
            return $bookingpress_entry_details;
        }
        
        /**
         * Function for modify entry data before insert
         *
         * @param  mixed $bookingpress_entry_details
         * @param  mixed $posted_data
         * @return void
         */
        public function bookingpress_modify_entry_data_before_insert_fun($bookingpress_entry_details, $posted_data){

        }
        
        /**
         * Function for change or remove payment method
         *
         * @param  mixed $payment_gateway
         * @param  mixed $bookingpress_appointment_data
         * @return void
         */
        public function bookingpress_check_for_modified_empty_payment_getway_fun($payment_gateway, $bookingpress_appointment_data){
            if(isset($bookingpress_appointment_data['is_waiting_list'])){
                if($bookingpress_appointment_data['is_waiting_list'] == '1'){
                    $payment_gateway = "no_payment_getway";
                }
            }
            return $payment_gateway;
        }

		        
        /**
         * Function for add waiting list appointment
         *
         * @param  mixed $entry_id
         * @param  mixed $payment_gateway_data
         * @param  mixed $payment_status
         * @param  mixed $transaction_id_field
         * @param  mixed $payment_amount_field
         * @param  mixed $is_front
         * @param  mixed $is_cart_order
         * @return void
         */
        public function bookingpress_confirm_booking( $entry_id, $payment_gateway_data, $payment_status, $transaction_id_field = '', $payment_amount_field = '', $is_front = 2, $is_cart_order = 0 ) {
			global $bookingpress_pro_payment_gateways,$wpdb, $BookingPress, $tbl_bookingpress_entries, $tbl_bookingpress_customers, $bookingpress_email_notifications, $bookingpress_debug_payment_log_id, $bookingpress_customers, $bookingpress_coupons, $tbl_bookingpress_appointment_meta, $tbl_bookingpress_appointment_bookings, $bookingpress_other_debug_log_id, $tbl_bookingpress_payment_logs,$bookingpress_dashboard;

			$bookingpress_confirm_booking_received_data = array(
				'entry_id' => $entry_id,
				'payment_gateway_data' => wp_json_encode($payment_gateway_data),
				'payment_status' => $payment_status,
				'transaction_id_field' => $transaction_id_field,
				'payment_amount_field' => $payment_amount_field,
				'is_front' => $is_front,
				'is_cart_order' => $is_cart_order,
			);
			do_action( 'bookingpress_other_debug_log_entry', 'appointment_debug_logs', 'Booking form confirm booking data', 'bookingpress_complete_appointment', $bookingpress_confirm_booking_received_data, $bookingpress_other_debug_log_id );

			$bookingpress_is_appointment_exists = $wpdb->get_var($wpdb->prepare("SELECT bookingpress_appointment_booking_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d AND bookingpress_complete_payment_token != ''", $entry_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
			
			if ( ! empty( $entry_id ) && empty($is_cart_order) ) {

				$entry_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_entries} WHERE bookingpress_entry_id = %d", $entry_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm

				if ( ! empty( $entry_data ) ) {
					$bookingpress_entry_user_id                  = $entry_data['bookingpress_customer_id'];
					$bookingpress_customer_name                  = $entry_data['bookingpress_customer_name'];
					$bookingpress_customer_phone                 = $entry_data['bookingpress_customer_phone'];
					$bookingpress_customer_firstname             = $entry_data['bookingpress_customer_firstname'];
					$bookingpress_customer_lastname              = $entry_data['bookingpress_customer_lastname'];
					$bookingpress_customer_country               = $entry_data['bookingpress_customer_country'];
					$bookingpress_customer_phone_dial_code       = $entry_data['bookingpress_customer_phone_dial_code'];
					$bookingpress_customer_email                 = $entry_data['bookingpress_customer_email'];
					$bookingpress_customer_timezone				 = $entry_data['bookingpress_customer_timezone'];
					$bookingpress_customer_dst_timezone			 = $entry_data['bookingpress_dst_timezone'];
					$bookingpress_service_id                     = $entry_data['bookingpress_service_id'];
					$bookingpress_service_name                   = $entry_data['bookingpress_service_name'];
					$bookingpress_service_price                  = $entry_data['bookingpress_service_price'];
					$bookingpress_service_currency               = $entry_data['bookingpress_service_currency'];
					$bookingpress_service_duration_val           = $entry_data['bookingpress_service_duration_val'];
					$bookingpress_service_duration_unit          = $entry_data['bookingpress_service_duration_unit'];
					$bookingpress_payment_gateway                = $entry_data['bookingpress_payment_gateway'];
					$bookingpress_appointment_date               = $entry_data['bookingpress_appointment_date'];
					$bookingpress_appointment_time               = $entry_data['bookingpress_appointment_time'];
					$bookingpress_appointment_end_time           = $entry_data['bookingpress_appointment_end_time'];
					$bookingpress_appointment_internal_note      = $entry_data['bookingpress_appointment_internal_note'];
					$bookingpress_appointment_send_notifications = $entry_data['bookingpress_appointment_send_notifications'];
					$bookingpress_appointment_status             = $entry_data['bookingpress_appointment_status'];
					$bookingpress_coupon_details                 = $entry_data['bookingpress_coupon_details'];
					$bookingpress_coupon_discounted_amount       = $entry_data['bookingpress_coupon_discount_amount'];
					$bookingpress_deposit_payment_details        = $entry_data['bookingpress_deposit_payment_details'];
					$bookingpress_deposit_amount                 = $entry_data['bookingpress_deposit_amount'];
					$bookingpress_selected_extra_members         = $entry_data['bookingpress_selected_extra_members'];
					$bookingpress_extra_service_details          = $entry_data['bookingpress_extra_service_details'];
					$bookingpress_staff_member_id                = $entry_data['bookingpress_staff_member_id'];
					$bookingpress_staff_member_price             = $entry_data['bookingpress_staff_member_price'];
					$bookingpress_staff_first_name               = $entry_data['bookingpress_staff_first_name'];
					$bookingpress_staff_last_name                = $entry_data['bookingpress_staff_last_name'];
					$bookingpress_staff_email_address            = $entry_data['bookingpress_staff_email_address'];
					$bookingpress_staff_member_details           = $entry_data['bookingpress_staff_member_details'];
					$bookingpress_paid_amount                    = $entry_data['bookingpress_paid_amount'];
					$bookingpress_due_amount                     = $entry_data['bookingpress_due_amount'];
					$bookingpress_total_amount                   = $entry_data['bookingpress_total_amount'];
					$bookingpress_tax_percentage                 = $entry_data['bookingpress_tax_percentage'];
					$bookingpress_tax_amount                     = $entry_data['bookingpress_tax_amount'];
					$bookingpress_price_display_setting          = $entry_data['bookingpress_price_display_setting'];
					$bookingpress_display_tax_order_summary      = $entry_data['bookingpress_display_tax_order_summary'];
					$bookingpress_included_tax_label             = $entry_data['bookingpress_included_tax_label'];

					$payable_amount = ( ! empty( $payment_amount_field ) && ! empty( $payment_gateway_data[ $payment_amount_field ] ) ) ? $payment_gateway_data[ $payment_amount_field ] : $bookingpress_paid_amount;

					$bookingpress_customer_id = $bookingpress_wpuser_id = $bookingpress_is_customer_create = 0;
					$bookingpress_customer_details = $bookingpress_customers->bookingpress_create_customer( $entry_data, $bookingpress_entry_user_id, $is_front );					
					if ( ! empty( $bookingpress_customer_details ) ) {
						$bookingpress_customer_id = $bookingpress_customer_details['bookingpress_customer_id'];
						$bookingpress_wpuser_id   = $bookingpress_customer_details['bookingpress_wpuser_id'];
						$bookingpress_is_customer_create = !empty($bookingpress_customer_details['bookingpress_is_customer_create']) ? $bookingpress_customer_details['bookingpress_is_customer_create'] : 0;
					}

					if ( ! empty( $_REQUEST['appointment_data']['form_fields'] ) && ! empty( $bookingpress_customer_id ) ) {
						$bookingpress_pro_payment_gateways->bookingpress_insert_customer_field_data( $bookingpress_customer_id, array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['appointment_data']['form_fields'] ) ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST['appointment_data']['form_fields'] has already been sanitized.
					}

					$appointment_booking_fields = array(
						'bookingpress_entry_id'                      => $entry_id,
						'bookingpress_payment_id'                    => 0,
						'bookingpress_customer_id'                   => $bookingpress_customer_id,
						'bookingpress_customer_name'      			 => $bookingpress_customer_name, 
						'bookingpress_customer_firstname' 			 => $bookingpress_customer_firstname,
						'bookingpress_customer_lastname'  			 => $bookingpress_customer_lastname,
						'bookingpress_customer_phone'     			 => $bookingpress_customer_phone,
						'bookingpress_customer_country'   			 => $bookingpress_customer_country,
						'bookingpress_customer_phone_dial_code'      => $bookingpress_customer_phone_dial_code,
						'bookingpress_customer_email'     			 => $bookingpress_customer_email, 
						'bookingpress_service_id'                    => $bookingpress_service_id,
						'bookingpress_service_name'                  => $bookingpress_service_name,
						'bookingpress_service_price'                 => $bookingpress_service_price,
						'bookingpress_service_currency'              => $bookingpress_service_currency,
						'bookingpress_service_duration_val'          => $bookingpress_service_duration_val,
						'bookingpress_service_duration_unit'         => $bookingpress_service_duration_unit,
						'bookingpress_appointment_date'              => $bookingpress_appointment_date,
						'bookingpress_appointment_time'              => $bookingpress_appointment_time,
						'bookingpress_appointment_end_time'          => $bookingpress_appointment_end_time,
						'bookingpress_appointment_internal_note'     => $bookingpress_appointment_internal_note,
						'bookingpress_appointment_send_notification' => $bookingpress_appointment_send_notifications,
						'bookingpress_appointment_status'            => $bookingpress_appointment_status,
						'bookingpress_appointment_timezone'			 => $bookingpress_customer_timezone,
						'bookingpress_dst_timezone'				     => $bookingpress_customer_dst_timezone,
						'bookingpress_coupon_details'                => $bookingpress_coupon_details,
						'bookingpress_coupon_discount_amount'        => $bookingpress_coupon_discounted_amount,
						'bookingpress_tax_percentage'                => $bookingpress_tax_percentage,
						'bookingpress_tax_amount'                    => $bookingpress_tax_amount,
						'bookingpress_price_display_setting'         => $bookingpress_price_display_setting,
						'bookingpress_display_tax_order_summary'     => $bookingpress_display_tax_order_summary,
						'bookingpress_included_tax_label'            => $bookingpress_included_tax_label,
						'bookingpress_deposit_payment_details'       => $bookingpress_deposit_payment_details,
						'bookingpress_deposit_amount'                => $bookingpress_deposit_amount,
						'bookingpress_selected_extra_members'        => $bookingpress_selected_extra_members,
						'bookingpress_extra_service_details'         => $bookingpress_extra_service_details,
						'bookingpress_staff_member_id'               => $bookingpress_staff_member_id,
						'bookingpress_staff_member_price'            => $bookingpress_staff_member_price,
						'bookingpress_staff_first_name'               => $bookingpress_staff_first_name,
						'bookingpress_staff_last_name'                => $bookingpress_staff_last_name,
						'bookingpress_staff_email_address'           => $bookingpress_staff_email_address,
						'bookingpress_staff_member_details'          => $bookingpress_staff_member_details,
						'bookingpress_paid_amount'                   => $bookingpress_paid_amount,
						'bookingpress_due_amount'                    => $bookingpress_due_amount,
						'bookingpress_total_amount'                  => $bookingpress_total_amount,
						'bookingpress_created_at'         			 => current_time('mysql'),
					);


					$appointment_booking_fields = apply_filters( 'bookingpress_modify_appointment_booking_fields_before_insert', $appointment_booking_fields, $entry_data );

					do_action( 'bookingpress_payment_log_entry', $bookingpress_payment_gateway, 'before insert appointment', 'bookingpress pro', $appointment_booking_fields, $bookingpress_debug_payment_log_id );

					$inserted_booking_id = $BookingPress->bookingpress_insert_appointment_logs( $appointment_booking_fields );
				
					//Update appointment id in appointment_meta table
					$wpdb->update( $tbl_bookingpress_appointment_meta, array('bookingpress_appointment_id' => $inserted_booking_id), array('bookingpress_entry_id' => $entry_id) );

					// Update coupon usage counter if coupon code use
					if ( ! empty( $bookingpress_coupon_details ) ) {
						$bookingpress_coupon_data = json_decode( $bookingpress_coupon_details, true );
						if ( ! empty( $bookingpress_coupon_data ) && is_array( $bookingpress_coupon_data ) ) {
							$coupon_id = $bookingpress_coupon_data['coupon_data']['bookingpress_coupon_id'];
							$bookingpress_coupons->bookingpress_update_coupon_usage_counter( $coupon_id );
						}
					}
                    if ( ! empty( $inserted_booking_id ) ) {

						$bookingpress_last_invoice_id = $BookingPress->bookingpress_get_settings( 'bookingpress_last_invoice_id', 'invoice_setting' );
						$bookingpress_last_invoice_id++;
						$BookingPress->bookingpress_update_settings( 'bookingpress_last_invoice_id', 'invoice_setting', $bookingpress_last_invoice_id );
						$bookingpress_last_invoice_id = apply_filters('bookingpress_modify_invoice_id_externally', $bookingpress_last_invoice_id);
                        $wpdb->update($tbl_bookingpress_appointment_bookings, array('bookingpress_booking_id' => $bookingpress_last_invoice_id), array('bookingpress_appointment_booking_id' => $inserted_booking_id));

                        $bookingpress_email_notification_type = '';
                        if ( $bookingpress_appointment_status == '2' ) {
                            $bookingpress_email_notification_type = 'Appointment Pending';
                        } elseif ( $bookingpress_appointment_status == '1' ) {
                            $bookingpress_email_notification_type = 'Appointment Approved';
                        } elseif ( $bookingpress_appointment_status == '3' ) {
                            $bookingpress_email_notification_type = 'Appointment Canceled';
                        } elseif ( $bookingpress_appointment_status == '4' ) {
                            $bookingpress_email_notification_type = 'Appointment Rejected';
                        }
                        $bookingpress_email_notification_type = apply_filters('bookingpress_modify_send_email_notification_type',$bookingpress_email_notification_type,$bookingpress_appointment_status);                                    
                        $bookingpress_email_notifications->bookingpress_send_after_payment_log_entry_email_notification( $bookingpress_email_notification_type, $inserted_booking_id, $bookingpress_customer_email );
                        
                        do_action( 'bookingpress_after_book_appointment', $inserted_booking_id, $entry_id, $payment_gateway_data );

                    }
				}
			}else if(!empty($entry_id) && !empty($is_cart_order) ){
				$entry_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_entries} WHERE bookingpress_order_id = %d", $entry_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm

				if ( ! empty( $entry_data ) ) {
					$bookingpress_inserted_appointment_ids = array();
					$bookingpress_customer_id = $bookingpress_wpuser_id = $bookingpress_is_customer_create = 0;
					foreach($entry_data as $k => $v){
						$bookingpress_entry_id                       = $v['bookingpress_entry_id'];
						$bookingpress_order_id                       = $v['bookingpress_order_id'];
						$bookingpress_entry_user_id                  = $v['bookingpress_customer_id'];
						$bookingpress_customer_name                  = $v['bookingpress_customer_name'];
						$bookingpress_customer_phone                 = $v['bookingpress_customer_phone'];
						$bookingpress_customer_firstname             = $v['bookingpress_customer_firstname'];
						$bookingpress_customer_lastname              = $v['bookingpress_customer_lastname'];
						$bookingpress_customer_country               = $v['bookingpress_customer_country'];
						$bookingpress_customer_phone_dial_code       = $v['bookingpress_customer_phone_dial_code'];
						$bookingpress_customer_email                 = $v['bookingpress_customer_email'];
						$bookingpress_customer_timezone              = $v['bookingpress_customer_timezone'];
						$bookingpress_dst_timezone				     = $v['bookingpress_dst_timezone'];
						$bookingpress_service_id                     = $v['bookingpress_service_id'];
						$bookingpress_service_name                   = $v['bookingpress_service_name'];
						$bookingpress_service_price                  = $v['bookingpress_service_price'];
						$bookingpress_service_currency               = $v['bookingpress_service_currency'];
						$bookingpress_service_duration_val           = $v['bookingpress_service_duration_val'];
						$bookingpress_service_duration_unit          = $v['bookingpress_service_duration_unit'];
						$bookingpress_payment_gateway                = $v['bookingpress_payment_gateway'];
						$bookingpress_appointment_date               = $v['bookingpress_appointment_date'];
						$bookingpress_appointment_time               = $v['bookingpress_appointment_time'];
						$bookingpress_appointment_end_time           = $v['bookingpress_appointment_end_time'];
						$bookingpress_appointment_internal_note      = $v['bookingpress_appointment_internal_note'];
						$bookingpress_appointment_send_notifications = $v['bookingpress_appointment_send_notifications'];
						$bookingpress_appointment_status             = $v['bookingpress_appointment_status'];
						$bookingpress_coupon_details                 = $v['bookingpress_coupon_details'];
						$bookingpress_coupon_discounted_amount       = $v['bookingpress_coupon_discount_amount'];
						$bookingpress_deposit_payment_details        = $v['bookingpress_deposit_payment_details'];
						$bookingpress_deposit_amount                 = $v['bookingpress_deposit_amount'];
						$bookingpress_selected_extra_members         = $v['bookingpress_selected_extra_members'];
						$bookingpress_extra_service_details          = $v['bookingpress_extra_service_details'];
						$bookingpress_staff_member_id                = $v['bookingpress_staff_member_id'];
						$bookingpress_staff_member_price             = $v['bookingpress_staff_member_price'];
						$bookingpress_staff_first_name               = $v['bookingpress_staff_first_name'];
						$bookingpress_staff_last_name                = $v['bookingpress_staff_last_name'];
						$bookingpress_staff_email_address            = $v['bookingpress_staff_email_address'];
						$bookingpress_staff_member_details           = $v['bookingpress_staff_member_details'];
						$bookingpress_paid_amount                    = $v['bookingpress_paid_amount'];
						$bookingpress_due_amount                     = $v['bookingpress_due_amount'];
						$bookingpress_total_amount                   = $v['bookingpress_total_amount'];
						$bookingpress_tax_percentage                 = $v['bookingpress_tax_percentage'];
						$bookingpress_tax_amount                     = $v['bookingpress_tax_amount'];
						$bookingpress_price_display_setting          = $v['bookingpress_price_display_setting'];
						$bookingpress_display_tax_order_summary      = $v['bookingpress_display_tax_order_summary'];
						$bookingpress_included_tax_label             = $v['bookingpress_included_tax_label'];

						$payable_amount = ( ! empty( $payment_amount_field ) && ! empty( $payment_gateway_data[ $payment_amount_field ] ) ) ? $payment_gateway_data[ $payment_amount_field ] : $bookingpress_paid_amount;

						$bookingpress_customer_details = $bookingpress_customers->bookingpress_create_customer( $v, $bookingpress_entry_user_id, $is_front, 0, $bookingpress_customer_timezone );
						if ( ! empty( $bookingpress_customer_details ) ) {
							$bookingpress_customer_id = $bookingpress_customer_details['bookingpress_customer_id'];
							$bookingpress_wpuser_id   = $bookingpress_customer_details['bookingpress_wpuser_id'];
							$bookingpress_is_customer_create = !empty($bookingpress_customer_details['bookingpress_is_customer_create']) ? $bookingpress_customer_details['bookingpress_is_customer_create'] : 0;
						}

						if ( ! empty( $_REQUEST['appointment_data']['form_fields'] ) && ! empty( $bookingpress_customer_id ) ) {
							$bookingpress_pro_payment_gateways->bookingpress_insert_customer_field_data( $bookingpress_customer_id, array_map( array( $BookingPress, 'appointment_sanatize_field'), $_REQUEST['appointment_data']['form_fields'] ) ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST['appointment_data']['form_fields'] has already been sanitized.
						}

						$appointment_booking_fields = array(
							'bookingpress_entry_id'                      => $bookingpress_entry_id,
							'bookingpress_order_id'                      => $bookingpress_order_id,
							'bookingpress_is_cart'                       => 1,
							'bookingpress_payment_id'                    => 0,
							'bookingpress_customer_id'                   => $bookingpress_customer_id,
							'bookingpress_customer_name'      			 => $bookingpress_customer_name, 
							'bookingpress_customer_firstname' 			 => $bookingpress_customer_firstname,
							'bookingpress_customer_lastname'  			 => $bookingpress_customer_lastname,
							'bookingpress_customer_phone'     			 => $bookingpress_customer_phone,
							'bookingpress_customer_country'   			 => $bookingpress_customer_country,
							'bookingpress_customer_phone_dial_code'      => $bookingpress_customer_phone_dial_code,
							'bookingpress_customer_email'     			 => $bookingpress_customer_email, 
							'bookingpress_service_id'                    => $bookingpress_service_id,
							'bookingpress_service_name'                  => $bookingpress_service_name,
							'bookingpress_service_price'                 => $bookingpress_service_price,
							'bookingpress_service_currency'              => $bookingpress_service_currency,
							'bookingpress_service_duration_val'          => $bookingpress_service_duration_val,
							'bookingpress_service_duration_unit'         => $bookingpress_service_duration_unit,
							'bookingpress_appointment_date'              => $bookingpress_appointment_date,
							'bookingpress_appointment_time'              => $bookingpress_appointment_time,
							'bookingpress_appointment_end_time'          => $bookingpress_appointment_end_time,
							'bookingpress_appointment_internal_note'     => $bookingpress_appointment_internal_note,
							'bookingpress_appointment_send_notification' => $bookingpress_appointment_send_notifications,
							'bookingpress_appointment_status'            => $bookingpress_appointment_status,
							'bookingpress_appointment_timezone'			 => $bookingpress_customer_timezone,
							'bookingpress_dst_timezone'				     => $bookingpress_dst_timezone,
							'bookingpress_coupon_details'                => $bookingpress_coupon_details,
							'bookingpress_coupon_discount_amount'        => $bookingpress_coupon_discounted_amount,
							'bookingpress_tax_percentage'                => $bookingpress_tax_percentage,
							'bookingpress_tax_amount'                    => $bookingpress_tax_amount,
							'bookingpress_price_display_setting'         => $bookingpress_price_display_setting,
							'bookingpress_display_tax_order_summary'     => $bookingpress_display_tax_order_summary,
							'bookingpress_included_tax_label'            => $bookingpress_included_tax_label,
							'bookingpress_deposit_payment_details'       => $bookingpress_deposit_payment_details,
							'bookingpress_deposit_amount'                => $bookingpress_deposit_amount,
							'bookingpress_selected_extra_members'        => $bookingpress_selected_extra_members,
							'bookingpress_extra_service_details'         => $bookingpress_extra_service_details,
							'bookingpress_staff_member_id'               => $bookingpress_staff_member_id,
							'bookingpress_staff_member_price'            => $bookingpress_staff_member_price,
							'bookingpress_staff_first_name'               => $bookingpress_staff_first_name,
							'bookingpress_staff_last_name'                => $bookingpress_staff_last_name,
							'bookingpress_staff_email_address'           => $bookingpress_staff_email_address,
							'bookingpress_staff_member_details'          => $bookingpress_staff_member_details,
							'bookingpress_paid_amount'                   => $bookingpress_paid_amount,
							'bookingpress_due_amount'                    => $bookingpress_due_amount,
							'bookingpress_total_amount'                  => $bookingpress_total_amount,
							'bookingpress_created_at'         			 => current_time('mysql'),
						);

						$appointment_booking_fields = apply_filters( 'bookingpress_modify_appointment_booking_fields_before_insert', $appointment_booking_fields, $v );
						do_action( 'bookingpress_payment_log_entry', $bookingpress_payment_gateway, 'before insert appointment', 'bookingpress pro', $appointment_booking_fields, $bookingpress_debug_payment_log_id );
						$inserted_booking_id = $BookingPress->bookingpress_insert_appointment_logs( $appointment_booking_fields );
						array_push($bookingpress_inserted_appointment_ids, $inserted_booking_id);

						//Update appointment id in appointment_meta table
						$wpdb->update( $tbl_bookingpress_appointment_meta, array('bookingpress_appointment_id' => $inserted_booking_id), array('bookingpress_entry_id' => $v['bookingpress_entry_id']) );
						
					}
					// Update coupon usage counter if coupon code use
					if ( ! empty( $bookingpress_coupon_details ) ) {
						$bookingpress_coupon_data = json_decode( $bookingpress_coupon_details, true );
						if ( ! empty( $bookingpress_coupon_data ) && is_array( $bookingpress_coupon_data ) ) {
							$coupon_id = !empty($bookingpress_coupon_data['coupon_data']['bookingpress_coupon_id']) ? $bookingpress_coupon_data['coupon_data']['bookingpress_coupon_id'] :'';
							$coupon_id =( $coupon_id == '' && !empty($bookingpress_coupon_data['bookingpress_coupon_id'])) ? $bookingpress_coupon_data['bookingpress_coupon_id'] : $coupon_id;
							if($coupon_id != '') {
								$bookingpress_coupons->bookingpress_update_coupon_usage_counter( $coupon_id );
							}
						}
					}
                    if ( ! empty( $bookingpress_inserted_appointment_ids ) ) {
						$bookingpress_last_invoice_id = $BookingPress->bookingpress_get_settings( 'bookingpress_last_invoice_id', 'invoice_setting' );
						$bookingpress_last_invoice_id++;
						$BookingPress->bookingpress_update_settings( 'bookingpress_last_invoice_id', 'invoice_setting', $bookingpress_last_invoice_id );
						$bookingpress_last_invoice_id = apply_filters('bookingpress_modify_invoice_id_externally', $bookingpress_last_invoice_id);
                        foreach($bookingpress_inserted_appointment_ids as $k2 => $v2){                            

                            $wpdb->update($tbl_bookingpress_appointment_bookings, array('bookingpress_booking_id' => $bookingpress_last_invoice_id), array('bookingpress_appointment_booking_id' => $v2));
                            $bookingpress_email_notification_type = '';
                            if ( $bookingpress_appointment_status == '2' ) {
                                $bookingpress_email_notification_type = 'Appointment Pending';
                            } elseif ( $bookingpress_appointment_status == '1' ) {
                                $bookingpress_email_notification_type = 'Appointment Approved';
                            } elseif ( $bookingpress_appointment_status == '3' ) {
                                $bookingpress_email_notification_type = 'Appointment Canceled';
                            } elseif ( $bookingpress_appointment_status == '4' ) {
                                $bookingpress_email_notification_type = 'Appointment Rejected';
                            }
                            $bookingpress_email_notification_type = apply_filters('bookingpress_modify_send_email_notification_type',$bookingpress_email_notification_type,$bookingpress_appointment_status);
                            foreach($bookingpress_inserted_appointment_ids as $k2 => $v2){
                                do_action( 'bookingpress_after_book_appointment', $v2, $entry_id, $payment_gateway_data );
                                $bookingpress_email_notifications->bookingpress_send_after_payment_log_entry_email_notification( $bookingpress_email_notification_type, $v2, $bookingpress_customer_email );
                            }

                        }                      
                    }
				}
			}
			return 0;
		}

                
        /**
         * Function for without payment getway book appointment
         *
         * @param  mixed $response
         * @param  mixed $bookingpress_return_data
         * @param  mixed $bookingpress_appointment_data
         * @return void
        */
        public function bookingpress_no_payment_getway_submit_form_data_fun($response, $bookingpress_return_data){

            global $BookingPress, $bookingpress_pro_payment_gateways;
            
            $appointment_data = (isset($_REQUEST['appointment_data']))?$_REQUEST['appointment_data']:'';// phpcs:ignore
            $bookingpress_appointment_data            = array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['appointment_data'] );// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_REQUEST contains mixed array and will be sanitized using 'appointment_sanatize_field' function

            if(isset($bookingpress_appointment_data['is_waiting_list'])){
                if($bookingpress_appointment_data['is_waiting_list'] == '1'){

                    $entry_id = ! empty( $bookingpress_return_data['entry_id'] ) ? $bookingpress_return_data['entry_id'] : 0;
                    $bookingpress_is_cart = !empty($bookingpress_return_data['is_cart']) ? 1 : 0;

                    
                    $this->bookingpress_confirm_booking($entry_id, array(), '2', '', '', 1, $bookingpress_is_cart);
					$bookingpress_appointment_status = $BookingPress->bookingpress_get_settings( 'appointment_status', 'general_setting' );					
					$redirect_url = $bookingpress_return_data['pending_appointment_url'];
					
                    $bookingpress_redirect_url = $redirect_url;
                    if ( ! empty( $bookingpress_redirect_url ) ) {
                     
						$response['variant']       = 'redirect_url';
						$response['title']         = '';
						$response['msg']           = '';
						$response['is_redirect']   = 1;
						$response['redirect_data'] = $bookingpress_redirect_url;                          

                    }else{

                        $response['variant'] = 'success';
                        $response['title']   = esc_html__( 'Success', 'bookingpress-waiting-list' );
                        $response['msg']     = esc_html__( 'Thank you! Your waiting list appointment is complete. An email with details of your booking has been sent to you.', 'bookingpress-waiting-list' );    

                    }                
                }

            }            
            return $response;

        }

        public function bookingpress_modify_check_duplidate_appointment_time_slot_fun($bookingpress_check_duplidate_appointment_time, $posted_data){

            if(isset($posted_data['appointment_data']['is_waiting_list'])){
                if($posted_data['appointment_data']['is_waiting_list'] == 1){
                    $bookingpress_check_duplidate_appointment_time = false;
                }
            }         
            return $bookingpress_check_duplidate_appointment_time;

        }

        public function bookingpress_modify_check_payment_method_fun($bookingpress_checked_payment_getway, $posted_data){

            if(isset($posted_data['appointment_data']['is_waiting_list'])){
                if($posted_data['appointment_data']['is_waiting_list'] == 1){
                    $bookingpress_checked_payment_getway = false;
                }
            }
            return $bookingpress_checked_payment_getway;
        }

        public function bookingpress_dynamic_time_select_after($bookingpress_dynamic_time_select_after){

            $bookingpress_dynamic_time_select_after.='

                vm.appointment_step_form_data.is_waiting_list = 0;
                app.bookingpress_book_appointment_btn_text = app.bookingpress_book_appointment_btn_text_org;
                app.bookingpress_total_amount_text = app.bookingpress_total_amount_text_org;

            ';
            return $bookingpress_dynamic_time_select_after;

        }

        function is_cart_addon_active(){
            $bookingpress_cart_addon  = 0;
            if(is_plugin_active('bookingpress-cart/bookingpress-cart.php')){
                $bookingpress_cart_addon = 1;
            }
            return $bookingpress_cart_addon;            
        }

        public function bookingpress_disable_timeslot_select_data_fun($bookingpress_disable_timeslot_select_data){

            global $BookingPress;
            $is_cart_addon_active  = $this->is_cart_addon_active();              
            $waiting_book_button_label = $BookingPress->bookingpress_get_customize_settings('waiting_book_button_label','booking_form');
            $waiting_payable_amount_label = $BookingPress->bookingpress_get_customize_settings('waiting_payable_amount_label','booking_form');

            $bookingpress_disable_timeslot_select_data.='                
                var is_cart_addon_active = '.$is_cart_addon_active.';  

                if(!time_details.waiting_slot_disable && time_details.is_waiting_slot != false){

                    var vm = this; 
                    vm.bookingpress_book_appointment_btn_text = "'.$waiting_book_button_label.'";
                    vm.bookingpress_total_amount_text = "'.$waiting_payable_amount_label.'";

                    vm.appointment_step_form_data.is_waiting_list = 1;
                    var waiting_slot_counter = time_details.waiting_slot_counter;
                    if(waiting_slot_counter){
                        waiting_slot_counter = parseInt(waiting_slot_counter);
                    }
                    var waiting_number = waiting_slot_counter+1;
                    var waiting_number_disp = String(waiting_number).padStart(2, "0");
                    vm.appointment_step_form_data.waiting_number_disp = waiting_number_disp;
                    vm.appointment_step_form_data.waiting_number = waiting_number;

                    vm.appointment_step_form_data.selected_start_time = time_details.start_time;
                    vm.appointment_step_form_data.selected_end_time = time_details.end_time;    
                    if( "" != time_details.formatted_end_time && "" != time_details.formatted_start_time ) {                    
                        vm.appointment_step_form_data.selected_formatted_start_time = time_details.formatted_start_time;
                        vm.appointment_step_form_data.selected_formatted_end_time = time_details.formatted_end_time;
                    }                
                    if("" != time_details.store_start_time && "" != time_details.store_end_time && "" != time_details.store_service_date ){
                        vm.appointment_step_form_data.store_start_time = time_details.store_start_time;
                        vm.appointment_step_form_data.store_end_time = time_details.store_end_time;
                        vm.appointment_step_form_data.client_offset = vm.bookingpress_timezone_offset;
                        vm.appointment_step_form_data.store_selected_date = time_details.store_service_date;
                    }                            
                    vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name, vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name, vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].previous_tab_name)

                }

            ';            
            return $bookingpress_disable_timeslot_select_data;

        }


        public static function install(){
            global $wpdb,$tbl_bookingpress_entries,$bookingpress_waiting_list_version,$BookingPress,$tbl_bookingpress_appointment_bookings;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';                                                 
            $bookingpress_waiting_list_version_db = get_option('bookingpress_waiting_list_version');  
            if (!isset($bookingpress_waiting_list_version_db) || $bookingpress_waiting_list_version_db == '') {

                $myaddon_name = "bookingpress-waiting-list/bookingpress-waiting-list.php";
                
                // activate license for this addon
                $posted_license_key = trim( get_option( 'bkp_license_key' ) );
			    $posted_license_package = '20497';

                $api_params = array(
                    'edd_action' => 'activate_license',
                    'license'    => $posted_license_key,
                    'item_id'  => $posted_license_package,
                    //'item_name'  => urlencode( BOOKINGPRESS_ITEM_NAME ), // the name of our product in EDD
                    'url'        => home_url()
                );

                // Call the custom API.
                $response = wp_remote_post( BOOKINGPRESS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                //echo "<pre>";print_r($response); echo "</pre>"; exit;

                // make sure the response came back okay
                $message = "";
                if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                    $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-waiting-list' );
                } else {
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string = wp_remote_retrieve_body( $response );
                    if ( false === $license_data->success ) {
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    __( 'Your license key expired on %s.','bookingpress-waiting-list' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = __( 'Your license key has been disabled.','bookingpress-waiting-list' );
                                break;
                            case 'missing' :
                                $message = __( 'Invalid license.','bookingpress-waiting-list' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = __( 'Your license is not active for this URL.','bookingpress-waiting-list' );
                                break;
                            case 'item_name_mismatch' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-waiting-list');
                                break;
                            case 'invalid_item_id' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-waiting-list');
                                    break;
                            case 'no_activations_left':
                                $message = __( 'Your license key has reached its activation limit.','bookingpress-waiting-list' );
                                break;
                            default :
                                $message = __( 'An error occurred, please try again.','bookingpress-waiting-list' );
                                break;
                        }

                    }

                }

                if ( ! empty( $message ) ) {
                    update_option( 'bkp_waiting_list_license_data_activate_response', $license_data_string );
                    update_option( 'bkp_waiting_list_license_status', $license_data->license );
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Waiting List Add-on', 'bookingpress-waiting-list');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-waiting-list'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                
                if($license_data->license === "valid")
                {
                    update_option( 'bkp_waiting_list_license_key', $posted_license_key );
                    update_option( 'bkp_waiting_list_license_package', $posted_license_package );
                    update_option( 'bkp_waiting_list_license_status', $license_data->license );
                    update_option( 'bkp_waiting_list_license_data_activate_response', $license_data_string );
                }



                    update_option('bookingpress_waiting_list_version', $bookingpress_waiting_list_version);                    
                    
                    $post_table = $wpdb->posts;
                    $post_author = get_current_user_id();

                    $bookingpress_waiting_payment_content = '[bookingpress_waiting_payment]';
                    $bookingpress_waiting_payment_details = array(
                        'post_title'   => esc_html__('Waiting List Payment', 'bookingpress-waiting-list'),
                        'post_name'    => 'bookingpress-waiting-payment',
                        'post_content' => $bookingpress_waiting_payment_content,
                        'post_status'  => 'publish',
                        'post_parent'  => 0,
                        'post_author'  => 1,
                        'post_type'    => 'page',
                        'post_author'   => $post_author,
                        'post_date'     => current_time( 'mysql' ),
                        'post_date_gmt' => current_time( 'mysql', 1 ),
                    );
                    $wpdb->insert( $post_table, $bookingpress_waiting_payment_details );
                    $bookingpress_post_id = $wpdb->insert_id;
                    $BookingPress->bookingpress_update_settings('waiting_list_payment_page_id', 'general_setting', $bookingpress_post_id);

                    $bookingpress_waiting_payment_entry_exists = $wpdb->get_row("SHOW COLUMNS FROM {$tbl_bookingpress_entries} LIKE 'bookingpress_waiting_payment_token'");// phpcs:ignore 
                    if(empty($bookingpress_waiting_payment_entry_exists)){
                        $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_waiting_payment_token VARCHAR(200) DEFAULT NULL AFTER bookingpress_complete_payment_token" );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_entries is table name defined globally. False Positive alarm
                    }
                    $bookingpress_waiting_payment_appointment_exists = $wpdb->get_row("SHOW COLUMNS FROM {$tbl_bookingpress_appointment_bookings} LIKE 'bookingpress_waiting_payment_token'");// phpcs:ignore 
                    if(empty($bookingpress_waiting_payment_appointment_exists)){
                        $wpdb->query("ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_waiting_payment_token VARCHAR(200) DEFAULT NULL AFTER bookingpress_complete_payment_token"); // phpcs:ignore
                    }
                    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
                    $booking_form = array(
                        'waiting_slot_label' => __('Waiting List', 'bookingpress-waiting-list'),                            
                        'waiting_book_button_label' => __('Add To Waiting', 'bookingpress-waiting-list'),
                        'waiting_payable_amount_label' => __('Final Amount', 'bookingpress-waiting-list'),
                        'waiting_position_label' => __('Your waiting position is', 'bookingpress-waiting-list'), 
                        'waiting_list_text_color' => '#F5AE41',
                        'waiting_payment_success_message' => __('Payment completed.', 'bookingpress-waiting-list'),
                    );
                    foreach($booking_form as $key => $value) {
                        $bookingpress_get_customize_text = $BookingPress->bookingpress_get_customize_settings($key, 'booking_form');
                        if(empty($bookingpress_get_customize_text)){
                            $bookingpress_customize_settings_db_fields = array(
                                'bookingpress_setting_name'  => $key,
                                'bookingpress_setting_value' => $value,
                                'bookingpress_setting_type'  => 'booking_form',
                            );
                            $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
                        }
                    }

                    $tbl_bookingpress_notifications = $wpdb->prefix.'bookingpress_notifications';          
                    $bookingpress_get_custom_notification_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_notification_name FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s",'Appointment Waiting List','customer'), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    if(empty($bookingpress_get_custom_notification_data)){
                        $bookingpress_customer_notification_data = array(
                            'bookingpress_notification_name'   => 'Appointment Waiting List',
                            'bookingpress_notification_receiver_type' => 'customer',
                            'bookingpress_notification_status' => 1,
                            'bookingpress_notification_type'   => 'default',
                            'bookingpress_notification_subject' => 'Appointment Waiting List',
                            'bookingpress_notification_message' => 'Dear %customer_full_name%,<br>You have successfully add appointment on waiting list.<br>Thank you for choosing us,<br>%company_name%',
                            'bookingpress_created_at' => current_time( 'mysql' ),
                        );                    
                        $wpdb->insert( $tbl_bookingpress_notifications, $bookingpress_customer_notification_data );
                    }
                    //Set Waiting Appointment

                    $bookingpress_get_custom_notification_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_notification_name FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s",'Set Appointment From Waiting','customer'), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    if(empty($bookingpress_get_custom_notification_data)){

                        $bookingpress_customer_notification_data = array(
                            'bookingpress_notification_name'   => 'Set Appointment From Waiting',
                            'bookingpress_notification_receiver_type' => 'customer',
                            'bookingpress_notification_status' => 1,
                            'bookingpress_notification_type' => 'default',
                            'bookingpress_notification_subject' => 'The time slot from the waiting list is now available.',
                            'bookingpress_notification_message' => 'Dear %customer_full_name%,<br>The time slot on %appointment_date_time% for %service_name% is now available for booking.<br> Please use the link below to approve this appointment: %waitinglist_complete_payment_url% <br>Please note that the availability of the requested timeslot is not guaranteed, because this message is sent out to all customers in the appointment waitlist.',
                            'bookingpress_created_at'  => current_time( 'mysql' ),
                        );
                        $wpdb->insert( $tbl_bookingpress_notifications, $bookingpress_customer_notification_data ); 

                    }                        
                    
                    $bookingpress_get_custom_notification_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_notification_name FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s",'Appointment Waiting List','employee'), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    if(empty($bookingpress_get_custom_notification_data)){                        
                        $bookingpress_employee_notification_data = array(
                            'bookingpress_notification_name'   => 'Appointment Waiting List',
                            'bookingpress_notification_receiver_type' => 'employee',
                            'bookingpress_notification_status' => 1,
                            'bookingpress_notification_type'   => 'default',
                            'bookingpress_notification_subject' =>'Appointment Waiting List',
                            'bookingpress_notification_message' =>  'Hi administrator,<br>You have one confirmed %service_name% appointment. The appointment is added on waiting to your schedule.<br>Thank you,<br>%company_name%',
                            'bookingpress_created_at'          => current_time( 'mysql' ),
                        );
                        $wpdb->insert( $tbl_bookingpress_notifications, $bookingpress_employee_notification_data );
                    }

                    $bookingpress_get_custom_notification_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_notification_name FROM {$tbl_bookingpress_notifications} WHERE bookingpress_notification_name = %s AND bookingpress_notification_receiver_type = %s",'Set Appointment From Waiting','employee'), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_notifications is table name defined globally. False Positive alarm
                    if(empty($bookingpress_get_custom_notification_data)){                        
                        //Set Waiting Appointment
                        $bookingpress_employee_notification_data = array(
                            'bookingpress_notification_name' => 'Set Appointment From Waiting',
                            'bookingpress_notification_receiver_type' => 'employee',
                            'bookingpress_notification_status' => 1,
                            'bookingpress_notification_type' => 'default',
                            'bookingpress_notification_subject' => 'The time slot from the waiting list is now available.',
                            'bookingpress_notification_message' => 'Hi %staff_full_name%,<br>The time slot on %appointment_date_time% for %service_name% is now available for booking.',
                            'bookingpress_created_at' => current_time( 'mysql' ),
                        );
                        $wpdb->insert( $tbl_bookingpress_notifications, $bookingpress_employee_notification_data );

                    }

                    //Regenerate Bookingpress CSS Start
                    
                    $bookingpress_custom_data_arr = array();
                    $bookingpress_background_color = $BookingPress->bookingpress_get_customize_settings('background_color', 'booking_form');
                    $bookingpress_footer_background_color = $BookingPress->bookingpress_get_customize_settings('footer_background_color', 'booking_form');
                    $bookingpress_primary_color = $BookingPress->bookingpress_get_customize_settings('primary_color', 'booking_form');
                    $bookingpress_content_color = $BookingPress->bookingpress_get_customize_settings('content_color', 'booking_form');
                    $bookingpress_label_title_color = $BookingPress->bookingpress_get_customize_settings('label_title_color', 'booking_form');
                    $bookingpress_title_font_family = $BookingPress->bookingpress_get_customize_settings('title_font_family', 'booking_form');        
                    $bookingpress_sub_title_color = $BookingPress->bookingpress_get_customize_settings('sub_title_color', 'booking_form');
                    $bookingpress_price_button_text_color = $BookingPress->bookingpress_get_customize_settings('price_button_text_color', 'booking_form');    
                    $bookingpress_primary_background_color = $BookingPress->bookingpress_get_customize_settings('primary_background_color', 'booking_form');
                    $bookingpress_border_color= $BookingPress->bookingpress_get_customize_settings('border_color', 'booking_form');
                    
                    $bookingpress_background_color = !empty($bookingpress_background_color) ? $bookingpress_background_color : '#fff';
                    $bookingpress_footer_background_color = !empty($bookingpress_footer_background_color) ? $bookingpress_footer_background_color : '#f4f7fb';
                    $bookingpress_primary_color = !empty($bookingpress_primary_color) ? $bookingpress_primary_color : '#12D488';
                    $bookingpress_content_color = !empty($bookingpress_content_color) ? $bookingpress_content_color : '#727E95';
                    $bookingpress_label_title_color = !empty($bookingpress_label_title_color) ? $bookingpress_label_title_color : '#202C45';
                    $bookingpress_title_font_family = !empty($bookingpress_title_font_family) ? $bookingpress_title_font_family : '';    
                    $bookingpress_sub_title_color = !empty($bookingpress_sub_title_color) ? $bookingpress_sub_title_color : '#535D71';
                    $bookingpress_price_button_text_color = !empty($bookingpress_price_button_text_color) ? $bookingpress_price_button_text_color : '#fff';    
                    $bookingpress_primary_background_color = !empty($bookingpress_primary_background_color) ? $bookingpress_primary_background_color : '#e2faf1';
                    $bookingpress_border_color = !empty($bookingpress_border_color) ? $bookingpress_border_color : '#CFD6E5';
                    
                    $bookingpress_custom_data_arr['action'][] = 'bookingpress_save_my_booking_settings';
                    $bookingpress_custom_data_arr['action'][] = 'bookingpress_save_booking_form_settings';

                    $my_booking_form = array(
                        'background_color' => $bookingpress_background_color,
                        'row_background_color' => $bookingpress_footer_background_color,
                        'primary_color' => $bookingpress_primary_color,
                        'content_color' => $bookingpress_content_color,
                        'label_title_color' => $bookingpress_label_title_color,
                        'title_font_family' => $bookingpress_title_font_family,        
                        'sub_title_color'   => $bookingpress_sub_title_color,
                        'price_button_text_color' => $bookingpress_price_button_text_color,        
                        'border_color'         => $bookingpress_border_color,
                    );
                    $booking_form = array(
                        'background_color' => $bookingpress_background_color,
                        'footer_background_color' => $bookingpress_footer_background_color,
                        'primary_color' => $bookingpress_primary_color,
                        'primary_background_color'=> $bookingpress_primary_background_color,
                        'label_title_color' => $bookingpress_label_title_color,
                        'title_font_family' => $bookingpress_title_font_family,                
                        'content_color' => $bookingpress_content_color,                
                        'price_button_text_color' => $bookingpress_price_button_text_color,
                        'sub_title_color' => $bookingpress_sub_title_color,
                        'border_color'         => $bookingpress_border_color,
                    );
                    $bookingpress_custom_data_arr['booking_form'] = $booking_form;
                    $bookingpress_custom_data_arr['my_booking_form'] = $my_booking_form;

                    $BookingPress->bookingpress_generate_customize_css_func($bookingpress_custom_data_arr);

                    //Regenerate Bookingpress CSS Over


            }
                            
		}

        public static function uninstall(){            
            global $wpdb,$tbl_bookingpress_entries,$BookingPress,$tbl_bookingpress_appointment_bookings;
            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} DROP COLUMN bookingpress_waiting_payment_token" ); //phpcs:ignore
            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} DROP COLUMN bookingpress_waiting_payment_token"); //phpcs:ignore
            delete_option('bookingpress_waiting_list_version');


            delete_option('bkp_waiting_list_license_key');
            delete_option('bkp_waiting_list_license_package');
            delete_option('bkp_waiting_list_license_status');
            delete_option('bkp_waiting_list_license_data_activate_response');
        }  

        public function is_addon_activated(){
            $bookingpress_waiting_list_addon  = 0;
            if(is_plugin_active('bookingpress-waiting-list/bookingpress-waiting-list.php')){
                $bookingpress_waiting_list_addon = 1;
            }                        
            return $bookingpress_waiting_list_addon;
        }

        public function bookingpress_waiting_list_admin_notices(){
            if( !function_exists('is_plugin_active') ){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            if( !is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php') ){
                echo "<div class='notice notice-warning'><p>" . esc_html__('BookingPress - Waiting List Plugin requires Bookingpress Premium Plugin installed and active.', 'bookingpress-waiting-list') . "</p></div>";
            }
        }
                      
        public function bookingpress_add_global_option_data_func( $global_data ) {
            global $wpdb, $BookingPress, $tbl_bookingpress_form_fields;
            $bookingpress_global_appointment_status = !empty($global_data['appointment_status']) ? $global_data['appointment_status'] : array();
            if(!empty($bookingpress_global_appointment_status)){
                array_push($bookingpress_global_appointment_status, array('value' => '7', 'text' => __('On waiting list', 'bookingpress-waiting-list')));
                //array_push($bookingpress_global_appointment_status, array('value' => '8', 'text' => esc_html__('Cancel Waiting List', 'bookingpress-waiting-list')));
                $global_data['appointment_status'] = $bookingpress_global_appointment_status;
            }
            $data = array(
				'waiting_list_placeholders' => wp_json_encode(
					array(
						array(
							'value' => '%waitinglist_complete_payment_url%',
							'name'  => '%waitinglist_complete_payment_url%',
						)
					)
				)
            );
            $global_data = array_merge( $global_data, $data );
            return $global_data;
        }

        public function bookingpress_add_default_notification_section_func() {
        ?>
            <div class="bpa-en-left_item-body--list__item" :class="bookingpress_active_email_notification == 'appointment_waiting_list' ? '__bpa-is-active' : ''" ref="appointmentwaitinglist" @click='bookingpress_select_email_notification("<?php esc_html_e('Appointment Wiaiting List Notification', 'bookingpress-waiting-list'); ?>","Appointment Waiting List", "appointment_waiting_list")'>
                <span class="material-icons-round --bpa-item-status is-enabled" v-if="default_notification_status['customer']['appointment_waiting_list'] == true || default_notification_status['employee']['appointment_waiting_list'] == true">circle</span>
                <span class="material-icons-round --bpa-item-status" v-else>circle</span>
                <p><?php esc_html_e( 'On Waiting List', 'bookingpress-waiting-list' ); ?></p>
            </div>
            <div class="bpa-en-left_item-body--list__item" :class="bookingpress_active_email_notification == 'set_appointment_from_waiting' ? '__bpa-is-active' : ''" ref="setappointmentfromwaiting" @click='bookingpress_select_email_notification("<?php esc_html_e('Waiting Slot Availability Notification', 'bookingpress-waiting-list'); ?>","Set Appointment From Waiting", "set_appointment_from_waiting")'>
                <span class="material-icons-round --bpa-item-status is-enabled" v-if="default_notification_status['customer']['set_appointment_from_waiting'] == true || default_notification_status['employee']['set_appointment_from_waiting'] == true">circle</span>
                <span class="material-icons-round --bpa-item-status" v-else>circle</span>
                <p><?php esc_html_e('Waiting Slot Availability', 'bookingpress-waiting-list'); ?></p>
            </div>            
        <?php
        }

        public function add_bookingpress_default_notification_status_func($bookingpress_default_notification_status_data, $bookingpres_default_notification_data){

			global $BookingPress;			
            foreach($bookingpres_default_notification_data as  $bookingpress_default_notification_val){

                $bookingpress_notification_value         = ( $bookingpress_default_notification_val['bookingpress_notification_status'] == 1 ) ? true : false;
                $bookingpress_notification_receiver_type = $bookingpress_default_notification_val['bookingpress_notification_receiver_type'];
                if('Appointment Waiting List' == $bookingpress_default_notification_val['bookingpress_notification_name']) {
                    $bookingpress_default_notification_status_data[ $bookingpress_notification_receiver_type ]['appointment_waiting_list'] = $bookingpress_notification_value;
                }
                if('Set Appointment From Waiting' == $bookingpress_default_notification_val['bookingpress_notification_name']) {
                    $bookingpress_default_notification_status_data[ $bookingpress_notification_receiver_type ]['set_appointment_from_waiting'] = $bookingpress_notification_value;
                }              

			}		
            return $bookingpress_default_notification_status_data;

        }



        public function bookingpress_add_waiting_list_counter_func(){           
            global $BookingPress;
            $waiting_slot_label = $BookingPress->bookingpress_get_customize_settings('waiting_slot_label','booking_form');            
            $waiting_slot_label = (!empty($waiting_slot_label))?stripslashes_deep($waiting_slot_label):$waiting_slot_label;
        ?>
            <div class="bpa-front__waiting-counter" v-if="time_details.waiting_slot !== 'undefined' && (time_details.is_waiting_slot == true || time_details.waiting_slot_disable == true)"><?php echo esc_html($waiting_slot_label); ?> ({{time_details.waiting_slot_counter}})</div>
        <?php
        }

        function bookingpress_frontend_add_appointment_data_variables($bookingpress_front_vue_data_fields) {

            $bookingpress_front_vue_data_fields['appointment_step_form_data']['is_waiting_list'] = false;

            $bookingpress_front_vue_data_fields['only_waiting_dates'] = array();
            $bookingpress_front_vue_data_fields['waiting_attributes'] = array();
            $bookingpress_front_vue_data_fields['remove_in_waiting_list_date'] = array();

            $bookingpress_site_date = date('Y-m-d H:i:s', current_time( 'timestamp') );
            $bookingpress_site_date = apply_filters( 'bookingpress_modify_current_date', $bookingpress_site_date );
            $bookingpress_site_current_date = date( 'Y-m-d', strtotime( $bookingpress_site_date ) ) . ' 00:00:00';

            $bookingpress_front_vue_data_fields['bookingpress_compare_curent_date'] = $bookingpress_site_current_date;
            $bookingpress_front_vue_data_fields['bookingpress_book_appointment_btn_text_org'] = (isset($bookingpress_front_vue_data_fields['bookingpress_book_appointment_btn_text']))?$bookingpress_front_vue_data_fields['bookingpress_book_appointment_btn_text']:'';
            $bookingpress_front_vue_data_fields['bookingpress_total_amount_text_org'] = (isset($bookingpress_front_vue_data_fields['bookingpress_total_amount_text']))?$bookingpress_front_vue_data_fields['bookingpress_total_amount_text']:'';            
            return $bookingpress_front_vue_data_fields;

        }

        function bookingpress_modify_entry_data_before_insert_func($bookingpress_entry_details, $posted_data) {
            
            if(isset($posted_data['is_waiting_list'])){
                if($posted_data['is_waiting_list'] == '1'){
                    $bookingpress_entry_details['bookingpress_appointment_status'] = 7;
                    $bookingpress_entry_details['bookingpress_waiting_payment_token'] = uniqid("bpa", true);
                    $bookingpress_entry_details['bookingpress_mark_as_paid'] = 0;
                    $bookingpress_entry_details['bookingpress_payment_gateway'] = ' - ';
                }
                
                /*
                if(empty($posted_data['cart_items'])) {
                    if( isset($posted_data['is_waiting_list']) && $posted_data['is_waiting_list'] == 'true') {  
                        $bookingpress_entry_details['bookingpress_appointment_status'] = '7';
                    }
                }
                */                
            }
            return $bookingpress_entry_details;
        }

        function bookingpress_modify_waiting_slot_data_func($service_time_arr, $selected_service_id, $selected_date) {

            if( empty( $selected_service_id ) || empty( $selected_date ) ){
                return $service_time_arr;
            }

            global $wpdb, $tbl_bookingpress_appointment_bookings, $BookingPress, $bookingpress_services, $default_waiting_list_max_slot;
            $is_cart_addon_active  = $this->is_cart_addon_active();
            $appointment_data_obj = !empty($_POST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_data_obj'] ) : array(); // phpcs:ignore
            if($is_cart_addon_active){                                
                if(isset($appointment_data_obj['cart_items'])){
                    $cart_items = $appointment_data_obj['cart_items'];
                    $is_waiting_list = $appointment_data_obj['is_waiting_list'];
                    $cart_item_edit_index = (isset($appointment_data_obj['cart_item_edit_index']))?$appointment_data_obj['cart_item_edit_index']:'';
                    if(!empty($cart_items)){
                        if(!$is_waiting_list && $cart_item_edit_index != 0){
                            $service_time_arr['is_waiting_slot'] = false;
                            $service_time_arr['waiting_slot_disable'] = false;                            
                            return $service_time_arr;
                        }
                    }                    
                }
            }
            $service_time_arr['is_waiting_slot'] = false;
            $service_time_arr['waiting_slot_disable'] = false;
            $service_time_arr['waiting_slot_counter'] = 0;    // add this parameter in waiting list addon.
            if($service_time_arr['disable_flag_timeslot'] == true || $service_time_arr['is_booked'] == true) {                                
                $total_booked = $service_time_arr['total_booked'];
                $max_capacity = $service_time_arr['max_capacity'];                
                if($total_booked > $max_capacity && $total_booked != 0){                      
                    //if(!empty($selected_service_id) && !empty($selected_date)){
                    $bookingpress_waiting_list_off = '';                       
                    if(empty($bookingpress_waiting_list_off)){

                        $bookingpress_waiting_list_max_slot = $bookingpress_services->bookingpress_get_service_meta($selected_service_id, 'waiting_list_max_slot');

                        if(empty($bookingpress_waiting_list_max_slot) && $bookingpress_waiting_list_max_slot != 0){
                            $bookingpress_waiting_list_max_slot = $default_waiting_list_max_slot;
                        }
                        $max_total_capacity = $service_time_arr['max_total_capacity'];
                        $total_booked = $service_time_arr['total_booked'];                            
                        if($total_booked >= $max_total_capacity || $service_time_arr['max_capacity'] == 0){    
                                                        
                            if($bookingpress_waiting_list_max_slot != 0){   
                                $is_appointment_exists  = 0;
                                $appointment_start_time = $service_time_arr['start_time'].':00';
                                $appointment_end_time   = $service_time_arr['end_time'].':00';
                                $bookingpress_shared_service_timeslot = $BookingPress->bookingpress_get_settings('share_timeslot_between_services', 'general_setting');
                                if($bookingpress_shared_service_timeslot == 'true'){    
                                    $is_appointment_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_appointment_booking_id) as total FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_service_id = %d AND bookingpress_appointment_date = %s AND bookingpress_appointment_time LIKE %s AND (bookingpress_appointment_status = 7)", $selected_service_id, $selected_date, $appointment_start_time ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm                            
                                    $service_time_arr['waiting_slot_counter'] = $is_appointment_exists;
                                }else{        
                                    $bookingpress_staffmember_id = !empty( $appointment_data_obj['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) ? intval( $appointment_data_obj['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) : '';  //phpcs:ignore                                      
                                    $selected_staff_member_id_sec = (isset($_POST['bookingpress_selected_staffmember']['selected_staff_member_id']))?sanitize_text_field($_POST['bookingpress_selected_staffmember']['selected_staff_member_id']):'';//phpcs:ignore
                                    if( empty( $bookingpress_staffmember_id ) && !empty( $selected_staff_member_id_sec ) ){
                                        $bookingpress_staffmember_id = intval( $selected_staff_member_id_sec );
                                    }    
                                    if($bookingpress_staffmember_id){
                                        $service_start_time = $service_time_arr['start_time'].':00';
                                        $service_end_time = $service_time_arr['end_time'].':00';
                                        $is_appointment_exists  = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_date LIKE %s AND bookingpress_appointment_time = %s AND bookingpress_appointment_end_time = %s AND bookingpress_service_id = %d AND bookingpress_staff_member_id = %d AND bookingpress_appointment_status = 7", '%'.$selected_date.'%', $appointment_start_time, $appointment_end_time, $selected_service_id, $bookingpress_staffmember_id ) );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                                        $service_time_arr['waiting_slot_counter'] = $is_appointment_exists;       
                                    }else{                                        
                                        $is_appointment_exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_appointment_booking_id) as total FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_service_id = %d AND bookingpress_appointment_date = %s AND bookingpress_appointment_time LIKE %s AND (bookingpress_appointment_status = 7)", $selected_service_id, $selected_date, $appointment_start_time ) );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm                                                             
                                        $service_time_arr['waiting_slot_counter'] = $is_appointment_exists;
                                    }                                    
                                }
                                if(empty($is_appointment_exists)){  $is_appointment_exists = 0; }                                                                                                            
                                $num_padded = sprintf("%02d", $is_appointment_exists);
                                $service_time_arr['waiting_slot_counter'] = $num_padded;
                                $service_time_arr['is_waiting_slot'] = true;
                                if($is_appointment_exists >= $bookingpress_waiting_list_max_slot){
                                    $service_time_arr['waiting_slot_disable'] = true;
                                    $service_time_arr['is_waiting_slot'] = false;
                                }else{
                                    $service_time_arr['waiting_slot_disable'] = false; 
                                }                                                                                                                                                                        
                            }else{
                                $service_time_arr['waiting_slot_disable'] = true;
                            }
                        }

                    }                        
                    //}
                }                
            }
            return $service_time_arr;
        }

        function bookingpress_modify_send_email_notification_type_func($bookingpress_email_notification_type,$bookingpress_appointment_status) {
            if($bookingpress_appointment_status == '7') {
                $bookingpress_email_notification_type = 'Appointment Waiting List';
            }
            return $bookingpress_email_notification_type;
        }
    }

	global $bookingpress_waiting_list;
	$bookingpress_waiting_list = new bookingpress_waiting_list();
}