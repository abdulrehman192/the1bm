<?php
if (!class_exists('bookingpress_location_booking_form')) {
	class bookingpress_location_booking_form Extends BookingPress_Core {
        function __construct(){ 
            global $BookingPress, $bookingpress_pro_appointment_bookings;


            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if( is_plugin_active( 'bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) && !empty( $BookingPress->bpa_pro_plugin_version() ) && version_compare( $BookingPress->bpa_pro_plugin_version(), '2.6.1', '>=' ) ){
                //Add Booking Form Step
                add_filter('bookingpress_frontend_apointment_form_add_dynamic_data', array( $this, 'bookingpress_add_location_step_in_sidebar'), 10 );

                /* BPA Filter */
                add_filter('bookingpress_bpa_modify_sidebar_step_data', array( $this, 'bookingpress_bpa_add_location_step_in_sidebar'), 10 );

                add_filter('bookingpress_frontend_apointment_form_add_dynamic_data', array($this, 'bookingpress_modify_front_booking_form_data_vars_func'),101 );
                
                add_action('bookingpress_add_front_side_sidebar_step_content', array($this, 'bookingpress_add_front_side_sidebar_step_content_func'), 10, 3);
    
                add_filter( 'bookingpress_add_appointment_booking_vue_methods', array( $this, 'bookingpress_add_appointment_booking_vue_methods_func' ), 11, 1 );
    
                add_filter('bookingpress_after_selecting_staffmember', array($this, 'bookingpress_select_staffmember_func'), 10, 1);
    
                add_filter( 'bookingpress_dynamic_add_params_for_timeslot_request', array( $this, 'bookingpress_dynamic_add_params_for_timeslot_request_method_func' ) );
    
                //Modify staffmember workhours timeslots as per location
                add_filter( 'bookingpress_retrieve_pro_modules_timeslots', array( $this, 'bookingpress_retrieve_location_staffmember_timings_func' ), 9, 6 );

                add_filter( 'bookingpress_modify_default_off_days', array( $this, 'bookingpress_modify_default_off_days_with_location'), 4, 4 );
    
                //Frontend insert data modification details
                add_filter('bookingpress_modify_entry_data_before_insert', array($this, 'bookingpress_modify_entry_data_before_insert_func'), 12, 2);
                add_filter('bookingpress_modify_appointment_booking_fields_before_insert', array($this, 'bookingpress_modify_appointment_booking_fields_before_insert_func'), 12, 2);
                add_filter('bookingpress_modify_payment_log_fields_before_insert', array($this, 'bookingpress_modify_payment_log_fields_before_insert_func'), 12, 2);
    
                add_filter( 'bookingpress_modify_all_retrieved_services', array( $this, 'bookingpress_modify_service_array_with_locations' ) , 12, 4);
    
                add_filter( 'bookingpress_modify_select_service_category', array( $this, 'bookingpress_hide_services_without_location_on_all_services') );
    
                add_action( 'bookingpress_modify_working_hours', array( $this, 'bookingpress_add_break_with_locations'), 20, 3 );
    
                add_filter( 'bookingpress_before_selecting_booking_service', array( $this, 'bookingpress_hide_show_location_based_on_service'), 12 );
    
                add_filter( 'bookingpress_reset_custom_duration_data', array( $this, 'bookingpress_hide_show_location_based_on_guest') );
    
                add_filter( 'bookingpress_after_selecting_anystaffmember', array( $this, 'bookingpress_after_selecting_anystaffmember' ) );
    
                add_action('bookingpress_add_summary_content_outside', array($this,'bookingpress_add_summary_content_outside_func'));
    
                add_action('bookingpress_cart_content_add_outside', array($this,'bookingpress_cart_content_add_outside_func'));
                
                add_action('bookingpress_cart_mobile_content_add_outside',array( $this,'bookingpress_cart_mobile_content_add_outside_func'));
    
                add_filter('bookingpress_add_custom_service_duration_data', array($this,'bookingpress_add_location_data_in_cart'));
    
                add_filter( 'bookingpress_modify_cart_xhr_response_data', array( $this, 'bookingpress_modify_cart_data_xhr_after_add_to_cart') );
    
                add_filter( 'bookingpress_modify_data_before_add_more_services_to_cart', array( $this, 'bookingpress_display_staff_service_based_on_location_add_to_cart_func'));
    
                add_filter( 'bookingpress_modify_data_after_empty_cart', array( $this, 'bookingpress_reset_location_field_display_after_empty_cart') );
    
                add_filter( 'bookingpress_retrieve_capacity', array( $this, 'bookingpress_set_location_capacity_for_service' ), 20, 2 );
    
                add_action( 'init', array( $this, 'bookingpress_remove_any_ajax_request') );
    
                add_filter( 'bookingpress_any_staff_modify_xhr_request_data', array( $this, 'bookingpress_add_location_with_anystaff_posted_data') );
    
                add_action( 'bookingpress_validate_booking_form', array( $this, 'bookingpress_validate_form_with_location') );
    
                add_filter( 'bookingpress_front_booking_dynamic_on_load_methods', array( $this, 'bookingpress_check_location_with_share_url') );
                
                add_filter('bookingpress_bpa_get_services_where_clause',array($this,'bookingpress_bpa_get_services_where_clause_func'),10,2);
                add_filter('bookingpress_bpa_get_staff_where_clause',array($this,'bookingpress_bpa_get_staff_where_clause_func'),10,2);

            }


        }

        
        /**
         * BPA function for update sidebar step data
         *
         * @param  mixed $bookingpress_front_vue_data_fields
         * @return void
        */
        function bookingpress_bpa_add_location_step_in_sidebar($bookingpress_front_vue_data_fields){
            global $wpdb, $BookingPress,$bookingpress_pro_staff_members;
            if(!empty($bookingpress_front_vue_data_fields)){
                $bookingpress_sidebar_step_data = !empty($bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data']) ? $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data'] : array();

                $location_title = $BookingPress->bookingpress_get_customize_settings('location_title','booking_form');
                $location_title = !empty($location_title) ? stripslashes_deep($location_title) : '';
                $bookingpress_front_vue_data_fields['location_title'] = $location_title;

                if(!empty($bookingpress_sidebar_step_data)){
                    $bookingpress_sidebar_step_data['location'] = array(
                        'tab_name' => $location_title,
                        'tab_value' => 'location',
                        'tab_icon' => 'place',
                        'is_display_step' => 1,
                        'next_tab_name' => 'basic_details',
                        'previous_tab_name' => 'datetime',
                        'validate_fields' => array(),
                        'validation_msg' => array(),
                        'is_allow_navigate' => 0,
                    );

                    $bookingpress_new_sidebar_step_data = array();
                    $booking_form_sequence = $BookingPress->bookingpress_get_customize_settings('bookingpress_form_sequance','booking_form');
                    if(!empty($booking_form_sequence)){
                        $booking_form_sequence = json_decode($booking_form_sequence, TRUE);

                        $is_staff_module_activated = $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation();
                        if(!$is_staff_module_activated){
                            $bookingpress_staff_pos = array_search('staff_selection', $booking_form_sequence);
                            unset($booking_form_sequence[$bookingpress_staff_pos]);
                            $booking_form_sequence = array_values($booking_form_sequence);
                        }else{
                            $bookingpress_staff_pos = array_search('staff_selection', $booking_form_sequence);
                        }

                        $bookingpress_location_pos = array_search('location_selection', $booking_form_sequence);

                        $bookingpress_service_pos = array_search('service_selection', $booking_form_sequence);

                        $bookingpress_front_vue_data_fields['is_staff_first_step'] = 0;

                        if($bookingpress_location_pos == 0){
                            $bookingpress_new_sidebar_step_data['location'] = $bookingpress_sidebar_step_data['location'];
                        }else if($bookingpress_service_pos == 0){
                            $bookingpress_new_sidebar_step_data['service'] = $bookingpress_sidebar_step_data['service'];
                        }else if($bookingpress_staff_pos == 0 && $is_staff_module_activated == 1){
                            $bookingpress_new_sidebar_step_data['staffmembers'] = $bookingpress_sidebar_step_data['staffmembers'];
                            $bookingpress_front_vue_data_fields['is_staff_first_step'] = 1;
                        }
                        if($bookingpress_location_pos == 1){
                            $bookingpress_new_sidebar_step_data['location'] = $bookingpress_sidebar_step_data['location'];
                        }else if($bookingpress_service_pos == 1){
                            $bookingpress_new_sidebar_step_data['service'] = $bookingpress_sidebar_step_data['service'];
                        }else if($bookingpress_staff_pos == 1 && $is_staff_module_activated == 1){
                            $bookingpress_new_sidebar_step_data['staffmembers'] = $bookingpress_sidebar_step_data['staffmembers'];
                        }
                        if($bookingpress_location_pos == 2){
                            $bookingpress_new_sidebar_step_data['location'] = $bookingpress_sidebar_step_data['location'];
                        }else if($bookingpress_service_pos == 2){
                            $bookingpress_new_sidebar_step_data['service'] = $bookingpress_sidebar_step_data['service'];
                        }else if($bookingpress_staff_pos == 2 && $is_staff_module_activated == 1){
                            $bookingpress_new_sidebar_step_data['staffmembers'] = $bookingpress_sidebar_step_data['staffmembers'];
                        }

                        foreach($bookingpress_sidebar_step_data as $key => $value){
                            if($key == "location" || $key == "service" || $key == "staffmembers"){
                                continue;
                            }else{
                                $bookingpress_new_sidebar_step_data[$key] = $value;
                            }
                        }

                        $bookingpress_first_tab_name = $bookingpress_second_tab_name = $bookingpress_third_tab_name = $bookingpress_fourth_tab_name = "";                        
                        $bookingpress_first_tab_arr = current($bookingpress_new_sidebar_step_data);
                        $bookingpress_first_tab_name = $bookingpress_first_tab_arr['tab_value'];
                        $bookingpress_second_tab_arr = next($bookingpress_new_sidebar_step_data);
                        $bookingpress_second_tab_name = $bookingpress_second_tab_arr['tab_value'];
                        $bookingpress_third_tab_arr = next($bookingpress_new_sidebar_step_data);
                        $bookingpress_third_tab_name = $bookingpress_third_tab_arr['tab_value'];
                        $bookingpress_fourth_tab_arr = next($bookingpress_new_sidebar_step_data);
                        $bookingpress_fourth_tab_name = $bookingpress_fourth_tab_arr['tab_value'];
                        $bookingpress_new_sidebar_step_data[$bookingpress_first_tab_name]['next_tab_name'] = $bookingpress_second_tab_name;
                        $bookingpress_new_sidebar_step_data[$bookingpress_first_tab_name]['previous_tab_name'] = '';
                        $bookingpress_new_sidebar_step_data[$bookingpress_first_tab_name]['is_allow_navigate'] = 1;
                        $bookingpress_new_sidebar_step_data[$bookingpress_second_tab_name]['next_tab_name'] = $bookingpress_third_tab_name;
                        $bookingpress_new_sidebar_step_data[$bookingpress_second_tab_name]['previous_tab_name'] = $bookingpress_first_tab_name;
                        $bookingpress_new_sidebar_step_data[$bookingpress_second_tab_name]['is_allow_navigate'] = 0;
                        $bookingpress_new_sidebar_step_data[$bookingpress_third_tab_name]['next_tab_name'] = $bookingpress_fourth_tab_name;
                        $bookingpress_new_sidebar_step_data[$bookingpress_third_tab_name]['previous_tab_name'] = $bookingpress_second_tab_name;
                        $bookingpress_new_sidebar_step_data[$bookingpress_third_tab_name]['is_allow_navigate'] = 0;
                        $bookingpress_new_sidebar_step_data[$bookingpress_fourth_tab_name]['previous_tab_name'] = $bookingpress_third_tab_name;
                        $bookingpress_front_vue_data_fields['bookingpress_current_tab'] = $bookingpress_first_tab_name;

                    }

                    if(!empty($bookingpress_new_sidebar_step_data)){
                        $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data'] = $bookingpress_new_sidebar_step_data;
                    }
                }

                $bookingpress_front_vue_data_fields['location_default_img_url'] = BOOKINGPRESS_LOCATION_URL.'/images/location-placeholder.jpg';

                //Get all locations list
                $bookingpress_locations_list = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_id ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                $bookingpress_front_vue_data_fields['bookingpress_locations_list'] = $bookingpress_locations_list;
                $bookingpress_front_vue_data_fields['bookingpress_all_locations_list'] = $bookingpress_locations_list;
            }
            return $bookingpress_front_vue_data_fields; 
        }


		/**
		 * bpa function for get services
		 *
		 * @param  mixed $user_detail
		 * @return void
		*/
		function bookingpress_bpa_get_locations_func($user_detail=array()){
			global $BookingPress,$tbl_bookingpress_services,$wpdb,$tbl_bookingpress_servicesmeta,$bookingpress_pro_staff_members,$tbl_bookingpress_categories,$BookingPressPro,$tbl_bookingpress_locations,$tbl_bookingpress_locations_service_staff_pricing_details;
			
			$result = array();
			$result["total_records"] = 0;
			$result["locations"] = array();
			$response = array('status' => 0, 'message' => '', 'response' => array('result' => $result));


            if(class_exists('BookingPressPro') && method_exists( $BookingPressPro, 'bookingpress_bpa_check_valid_connection_callback_func') && $BookingPressPro->bookingpress_bpa_check_valid_connection_callback_func()){
            
                $bookingpress_location_id = isset($user_detail['location_id']) ? intval($user_detail['location_id']) : '';
                $bookingpress_service_id = isset($user_detail['service_id']) ? intval($user_detail['service_id']) : '';					
                $bookingpress_staffmember_id = isset($user_detail['staffmember_id']) ? intval($user_detail['staffmember_id']) : '';					
                
                $perpage     = isset($user_detail['per_page']) ? intval($user_detail['per_page']) : 10;
                $currentpage = isset($user_detail['current_page']) ? intval($user_detail['current_page']) : 1;
                $offset      = ( ! empty($currentpage) && $currentpage > 1 ) ? ( ( $currentpage - 1 ) * $perpage ) : 0;

                $where_clause = '';
                $join_query = "";

                $filter_pass_data = array('service_id'=>$bookingpress_service_id,'staffmember_id'=>$bookingpress_staffmember_id,'location_id'=>$bookingpress_location_id);
                
                $join_query .= " INNER JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} as location_staff_service ON location_staff_service.bookingpress_location_id = bookingpress_locations.bookingpress_location_id ";
                
                $bookingpress_disable_serevice = $wpdb->get_row($wpdb->prepare("SELECT GROUP_CONCAT(bookingpress_service_id) disable_serevices FROM {$tbl_bookingpress_servicesmeta} WHERE bookingpress_servicemeta_name = %s AND bookingpress_servicemeta_value = %s", 'show_service_on_site','false'), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                if(!empty($bookingpress_disable_serevice)){
                    $where_clause .= $wpdb->prepare( ' AND location_staff_service.bookingpress_service_id NOT IN (%s)', $bookingpress_disable_serevice);
                }

                $bpa_location_where_clause = "";
                if(!empty($bookingpress_location_id) && $bookingpress_location_id != 0){
                    $where_clause .= $wpdb->prepare( ' AND bookingpress_locations.bookingpress_location_id = %d', $bookingpress_location_id);
                }                    
                if(!empty($bookingpress_staffmember_id) && $bookingpress_staffmember_id != 0){
                    $where_clause .=  $wpdb->prepare( ' AND location_staff_service.bookingpress_staffmember_id = %d', $bookingpress_staffmember_id);
                    $bpa_location_where_clause .= $wpdb->prepare( " AND lc.bookingpress_staffmember_id = %d", $bookingpress_staffmember_id );
                }
                if(!empty($bookingpress_service_id) && $bookingpress_service_id != 0){
                    $where_clause .=  $wpdb->prepare( ' AND location_staff_service.bookingpress_service_id = %d', $bookingpress_service_id);
                    $bpa_location_where_clause .= $wpdb->prepare( " AND lc.bookingpress_service_id = %d", $bookingpress_service_id );
                }                        

                $bookingpress_total_locations = $wpdb->get_results("SELECT COUNT(DISTINCT bookingpress_locations.bookingpress_location_id) FROM {$tbl_bookingpress_locations} as bookingpress_locations $join_query WHERE 1 = 1 $where_clause GROUP BY bookingpress_locations.bookingpress_location_id ORDER BY bookingpress_locations.bookingpress_location_position ASC "); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
                $bookingpress_locations_list = $wpdb->get_results("SELECT bookingpress_locations.bookingpress_location_id,bookingpress_locations.bookingpress_location_name,bookingpress_locations.bookingpress_location_phone_country,bookingpress_locations.bookingpress_location_phone_number,bookingpress_locations.bookingpress_location_address,bookingpress_locations.bookingpress_location_description,bookingpress_locations.bookingpress_location_img_name,bookingpress_locations.bookingpress_location_img_url,bookingpress_locations.bookingpress_location_position FROM {$tbl_bookingpress_locations} as bookingpress_locations $join_query WHERE 1 = 1 $where_clause GROUP BY bookingpress_locations.bookingpress_location_id  ORDER BY bookingpress_locations.bookingpress_location_position ASC LIMIT {$offset} , {$perpage}", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $bookingpress_staffmember_module_activation = $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation();
                $bpa_location_arr = array();
                if( !empty( $bookingpress_locations_list ) ){
                    $total_locations = count( $bookingpress_locations_list );
                    $hidden_locations = 0;
                    foreach( $bookingpress_locations_list as $loc_key => $location_data ){
                        
                        $location_id = intval( $location_data['bookingpress_location_id'] );
                        if($bookingpress_staffmember_module_activation){
                            $staff_details = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id,bookingpress_staff_location_qty, bookingpress_staff_location_min_qty FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id > %d GROUP BY bookingpress_staffmember_id", $location_id, 0 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally. 
                            
                           
                            $location_staffmember_ids = array();
                            $location_staffmember_qty = array(); 
                            if( !empty( $staff_details ) ){                                    
                                foreach( $staff_details as $sfkey => $sfdata ){                                       

                                    $location_staffmember_ids[] = $sfdata->bookingpress_staffmember_id;                    
                                    $location_staffmember_qty[  $sfdata->bookingpress_staffmember_id ] = $sfdata->bookingpress_staff_location_qty;
                                    $location_staffmember_min_qty[  $sfdata->bookingpress_staffmember_id ] = $sfdata->bookingpress_staff_min_location_qty;
                                    //$location_data[ 'bookingpress_staffmembers'][] = $sfdata->bookingpress_staffmember_id;
                                }
                                $location_data['is_visible'] = true;
                            } else {                                    
                                continue;
                            }     
                            $location_data['bookingpress_location_staffmember_ids'] = $location_staffmember_ids;
                            $location_data['bookingpress_location_staffmember_qty'] = $location_staffmember_qty;                   
                            $location_data['bookingpress_location_staffmember_min_qty'] = $location_staffmember_min_qty;
                        }         
                        $service_location_details =  $wpdb->get_results( $wpdb->prepare("SELECT lc.bookingpress_service_qty, lc.bookingpress_service_id, lc.bookingpress_service_min_qty FROM  {$tbl_bookingpress_locations_service_staff_pricing_details} lc RIGHT JOIN {$tbl_bookingpress_services} ls ON lc.bookingpress_service_id = ls.bookingpress_service_id WHERE lc.bookingpress_location_id = %d " . $bpa_location_where_clause, $location_id), ARRAY_A); //phpcs:ignore                            
                        if( empty( $service_location_details ) ){
                            $hidden_locations++;
                            continue;
                        }               
                        $location_service_ids = array();
                        $location_service_qty = array();
                        $location_service_min_qty = array();            
                        foreach( $service_location_details as $location_details ){
                            $location_service_ids[] = $location_details['bookingpress_service_id'];                    
                            $location_service_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_qty'];
                            $location_service_min_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_min_qty'];
                        }                    
                        $location_data['bookingpress_location_service_ids'] = $location_service_ids;
                        $location_data['bookingpress_location_service_qty'] = $location_service_qty;
                        $location_data['bookingpress_location_service_min_qty'] = $location_service_min_qty;
                        $location_data['is_visible'] = true;
                        $location_data['is_visible_with_flag'] = true;                                              
                        $location_data = apply_filters('bookingpress_modified_location_data_for_front_booking_form',$location_data);                    
                        $bpa_location_arr[] = $location_data;

                    }               
                }

                $result["total_records"] = (!empty($bookingpress_total_locations))?count($bookingpress_total_locations):0;
                $result["locations"] = $bpa_location_arr;

                $response = array('status' => 1, 'message' => '', 'response' => array('result' => $result));

            }
			
			
			return $response;

		}
        
        /**
         * Function for bpa get staff where 
         *
         * @param  mixed $bookingpress_bpa_get_services_extra_query
         * @param  mixed $bookingpress_location_id
         * @return void
         */
        function bookingpress_bpa_get_staff_where_clause_func($bookingpress_bpa_get_services_extra_query,$user_detail){
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details,$BookingPressPro;


            if(class_exists('BookingPressPro') && method_exists( $BookingPressPro, 'bookingpress_bpa_check_valid_connection_callback_func') && $BookingPressPro->bookingpress_bpa_check_valid_connection_callback_func()){

                $bookingpress_service_id = isset($user_detail['service_id']) ? intval($user_detail['service_id']) : '';
                $bookingpress_staffmember_id = isset($user_detail['staffmember_id']) ? intval($user_detail['staffmember_id']) : '';
                $bookingpress_location_id = isset($user_detail['location_id']) ? intval($user_detail['location_id']) : '';

                $join_query = (isset($bookingpress_bpa_get_services_extra_query['join_query']))?$bookingpress_bpa_get_services_extra_query['join_query']:'';
                $where_clause = (isset($bookingpress_bpa_get_services_extra_query['where_clause']))?$bookingpress_bpa_get_services_extra_query['where_clause']:'';
                $bookingpress_bpa_get_services_extra_query['join_query'] .= " INNER JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} as location_staff_service ON location_staff_service.bookingpress_staffmember_id = bookingpress_staffmembers.bookingpress_staffmember_id ";
                if(!empty($bookingpress_location_id) && $bookingpress_location_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_location_id = %d', $bookingpress_location_id);
                }
                if(!empty($bookingpress_staffmember_id) && $bookingpress_staffmember_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_staffmember_id = %d', $bookingpress_staffmember_id);
                }
                if(!empty($bookingpress_service_id) && $bookingpress_service_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_service_id = %d', $bookingpress_service_id);
                }                                                          

            }

            return $bookingpress_bpa_get_services_extra_query;

        }
        
        /**
         * Function for bpa get service where 
         *
         * @param  mixed $bookingpress_bpa_get_services_extra_query
         * @param  mixed $bookingpress_location_id
         * @return void
         */
        function bookingpress_bpa_get_services_where_clause_func($bookingpress_bpa_get_services_extra_query,$user_detail){

            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details,$BookingPressPro;
            if(class_exists('BookingPressPro') && method_exists( $BookingPressPro, 'bookingpress_bpa_check_valid_connection_callback_func') && $BookingPressPro->bookingpress_bpa_check_valid_connection_callback_func()){

                $bookingpress_service_id = isset($user_detail['service_id']) ? intval($user_detail['service_id']) : '';
                $bookingpress_staffmember_id = isset($user_detail['staffmember_id']) ? intval($user_detail['staffmember_id']) : '';
                $bookingpress_location_id = isset($user_detail['location_id']) ? intval($user_detail['location_id']) : '';

                $join_query = (isset($bookingpress_bpa_get_services_extra_query['join_query']))?$bookingpress_bpa_get_services_extra_query['join_query']:'';
                $where_clause = (isset($bookingpress_bpa_get_services_extra_query['where_clause']))?$bookingpress_bpa_get_services_extra_query['where_clause']:'';
                
                $bookingpress_bpa_get_services_extra_query['join_query'] .= " INNER JOIN {$tbl_bookingpress_locations_service_staff_pricing_details} as location_staff_service ON location_staff_service.bookingpress_service_id = bookingpress_services.bookingpress_service_id ";

                if(!empty($bookingpress_location_id) && $bookingpress_location_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_location_id = %d', $bookingpress_location_id);
                }    
                if(!empty($bookingpress_staffmember_id) && $bookingpress_staffmember_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_staffmember_id = %d', $bookingpress_staffmember_id);
                }
                if(!empty($bookingpress_service_id) && $bookingpress_service_id != 0){
                    $bookingpress_bpa_get_services_extra_query['where_clause'] .= $wpdb->prepare( ' AND location_staff_service.bookingpress_service_id = %d', $bookingpress_service_id);
                }                                    

            }
            
            return $bookingpress_bpa_get_services_extra_query;

        }

        function bookingpress_check_location_with_share_url( $bookingpress_dynamic_on_load_methods_data ){

            $is_location_from_share_url = !empty( $_GET['loc_id'] ) ? 1 : 0;
            $location_share_url_id = !empty( $_GET['loc_id'] ) ? intval( $_GET['loc_id'] ) : 0;
            
            $bookingpress_dynamic_on_load_methods_data .= '
                let bookingpress_location_from_share_url = ' . $is_location_from_share_url . ';

                if( 1 == bookingpress_location_from_share_url ){
                    let location_id = '.$location_share_url_id.';

                    this.appointment_step_form_data.selected_location = location_id;
                    
                    this.appointment_step_form_data.selected_location = parseInt(location_id);
                    let selected_location_name = vm.bookingpress_locations_list[ location_id ].bookingpress_location_name;
                    this.appointment_step_form_data.selected_location_name = selected_location_name;
                }
            ';

            return $bookingpress_dynamic_on_load_methods_data;
        }

        function bookingpress_validate_form_with_location( $posted_data ){
            global $bookingpress_pro_appointment_bookings, $bookingpress_pro_staff_members, $wpdb, $tbl_bookingpress_locations_service_staff_pricing_details;
            $loc_response = array();

            if( empty( $posted_data['appointment_data']['selected_location'] ) ){
                $loc_response['variant'] = 'error';
                $loc_response['title']   = esc_html__('Error', 'bookingpress-location');
                $loc_response['msg']     = esc_html__('No location selected', 'bookingpress-location');
                return wp_json_encode($loc_response);
            }
            if(!empty($posted_data) && empty($posted_data['appointment_data']['cart_items']) ){

                $appointment_service_id     = intval($posted_data['appointment_data']['selected_service']);
                $appointment_location_id    = intval( $posted_data['appointment_data']['selected_location'] );
                
                if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                    $bookingpress_selected_staffmember_id = sanitize_text_field($posted_data['appointment_data']['bookingpress_selected_staff_member_details']['selected_staff_member_id']);

                    $is_service_exists_with_location = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_location_id) as total_location FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_staffmember_id = %d", $appointment_location_id, $appointment_service_id, $bookingpress_selected_staffmember_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
 
                    if( 1 > $is_service_exists_with_location ){
                        $loc_response['variant'] = 'error';
                        $loc_response['title']   = esc_html__('Error', 'bookingpress-location');
                        $loc_response['msg']     = esc_html__('Selected service and staff are not available with the selected location', 'bookingpress-location');
                        return wp_json_encode($loc_response);
                    }

                } else {
                    global $tbl_bookingpress_locations_service_staff_pricing_details;

                    $is_service_exists_with_location = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_location_id) as total_location FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $appointment_location_id, $appointment_service_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                    if( 1 > $is_service_exists_with_location ){
                        $loc_response['variant'] = 'error';
                        $loc_response['title']   = esc_html__('Error', 'bookingpress-location');
                        $loc_response['msg']     = esc_html__('Selected service is not available with the selected location', 'bookingpress-location');
                        return wp_json_encode($loc_response);
                    }

                }

            } else if(!empty($posted_data) && empty($posted_data['appointment_data']['cart_items']) ){
                
            }

        }

        function bookingpress_add_location_with_anystaff_posted_data( $bookingpress_any_staff_modify_xhr_request_data ){

            $bookingpress_any_staff_modify_xhr_request_data = '
                postData.location_id = vm.appointment_step_form_data.selected_location;
            ';

            return $bookingpress_any_staff_modify_xhr_request_data;
        }

        function bookingpress_remove_any_ajax_request(){
            global $BookingPress,$bookingpress_pro_appointment_bookings, $bookingpress_pro_staff_members;
            if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                //$is_any_option_enabled = $BookingPress->bookingpress_get_settings( 'show_bookingslots_in_client_timezone', 'general_setting' );
                $form_sequence = $BookingPress->bookingpress_get_customize_settings( 'bookingpress_form_sequance', 'booking_form' );

                $form_sequence = json_decode( $form_sequence, true );

                $loc_pos = array_search( 'location_selection', $form_sequence );
                $stf_pos = array_search( 'staff_selection', $form_sequence );
                
                if( $loc_pos < $stf_pos ){   
                    remove_action( 'wp_ajax_bookingpress_get_any_staffmember_id', array( $bookingpress_pro_appointment_bookings, 'bookingpress_get_any_staffmember_id_func' ) );
                    remove_action( 'wp_ajax_nopriv_bookingpress_get_any_staffmember_id', array( $bookingpress_pro_appointment_bookings, 'bookingpress_get_any_staffmember_id_func' ) );


                    add_action( 'wp_ajax_bookingpress_get_any_staffmember_id', array( $this, 'bookingpress_get_any_staffmember_id_with_location') );
                    add_action( 'wp_ajax_nopriv_bookingpress_get_any_staffmember_id', array( $this, 'bookingpress_get_any_staffmember_id_with_location') );
                }
            }
        }

        function bookingpress_get_any_staffmember_id_with_location(){
            global $BookingPress, $wpdb, $bookingpress_pro_staff_members, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_services, $tbl_bookingpress_appointment_bookings, $bookingpress_pro_appointment_bookings, $tbl_bookingpress_locations_service_staff_pricing_details;
			$response              = array();
			$wpnonce               = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
			$bpa_verify_nonce_flag = wp_verify_nonce( $wpnonce, 'bpa_wp_nonce' );
			if ( ! $bpa_verify_nonce_flag ) {
				$response['variant'] = 'error';
				$response['title']   = esc_html__( 'Error', 'bookingpress-location' );
				$response['msg']     = esc_html__( 'Sorry, Your request can not be processed due to security reason.', 'bookingpress-location' );
				echo wp_json_encode( $response );
				die();
			}

            $response['variant'] = 'error';
			$response['title']   = esc_html__( 'Error', 'bookingpress-location' );
			$response['msg']     = esc_html__( 'Something went wrong while processing with request', 'bookingpress-location' );
			$response['staffmember_id'] = 0;

            $check_capacity = false;
			$bring_capacity = 1;
			if( !empty( $_POST['selected_bring_members'] ) && 1 < $_POST['selected_bring_members'] ){
				$check_capacity = true;
				$bring_capacity = intval( $_POST['selected_bring_members'] );
			}

            $location_id = !empty( $_POST['location_id'] ) ? intval( $_POST['location_id'] ) : '';

            /** return default staff id if the location is not exists */
            if( empty( $location_id ) ){
                $bookingpress_pro_appointment_bookings->bookingpress_get_any_staffmember_id_func();
                die;
            }
            
            $bookingpress_selected_service_id = !empty($_POST['service_id']) ? intval($_POST['service_id']) : 0;
            
            $bookingpress_staffmember_id = 0;
            $bookingpress_current_date = date('Y-m-d', current_time('timestamp'));

            $bookingpress_week_start_date = $bookingpress_current_date;
            $bookingpress_week_end_date = date( 'Y-m-d', strtotime( "+1 week", strtotime( $bookingpress_current_date ) ) );

            $bookingpress_any_staff_selected_rule = $BookingPress->bookingpress_get_settings('bookingpress_staffmember_auto_assign_rule', 'staffmember_setting');

            $where_clause = " AND 1=1 ";
            if( true == $check_capacity ){
                $where_clause .= $wpdb->prepare( " AND bookingpress_service_capacity >= %d", $bring_capacity );
            }

            if( "least_assigned_by_day" == $bookingpress_any_staff_selected_rule || "most_assigned_by_day" == $bookingpress_any_staff_selected_rule ){
                $ordby = "ASC";
                $minmax = "min";
                if( "most_assigned_by_day" == $bookingpress_any_staff_selected_rule ){
                    $ordby = "DESC";
                    $minmax = "max";
                }
                $get_staff_with_serviceloc = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $location_id, $bookingpress_selected_service_id )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                /** return default staff id if the data not exists */
                if( empty( $get_staff_with_serviceloc ) ){
                    $bookingpress_pro_appointment_bookings->bookingpress_get_any_staffmember_id_func();
                    die;
                }

                if( count( $get_staff_with_serviceloc ) == 1 ){
                    $bookingpress_staffmember_id = $get_staff_with_serviceloc[0]->bookingpress_staffmember_id;
                } else {
                    $bookingpress_staff_ids = array();
                    foreach( $get_staff_with_serviceloc as $staff_id_data ){
                        $bookingpress_staff_ids[] = $staff_id_data->bookingpress_staffmember_id;
                    }

                    $where_clause .= ' AND bpsf.bookingpress_staffmember_id IN ('. implode( ',', $bookingpress_staff_ids ).')';
                    if(!empty($bookingpress_selected_service_id)){

                        $bookingpress_search_query       = 'WHERE 1=1 ';
                        $bookingpress_search_query_where = "AND (bookingpress_service_id = {$bookingpress_selected_service_id} ) ";
                        $bookingpress_search_query_where .= "AND ( bookingpress_appointment_date LIKE '{$bookingpress_current_date}' OR bookingpress_appointment_date IS NULL ) AND ( bookingpress_appointment_status IS NULL OR bookingpress_appointment_status = 1 OR bookingpress_appointment_status = 2 )";

                        $bookingpress_search_query_where .= ' AND bookingpress_staff_member_id IN ('. implode( ',', $bookingpress_staff_ids ).')';

                        $bookingpress_total_appointments = $wpdb->get_var("SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query} {$bookingpress_search_query_where} ORDER BY bookingpress_appointment_date $ordby"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm

                        if($bookingpress_total_appointments == 0){
                            $bookingpress_assigned_service_details = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ".$tbl_bookingpress_staffmembers." bpsf ON bpss.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d " . $where_clause, $bookingpress_selected_service_id, 1), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm
                            //SELECT * FROM wp_bookingpress_staffmembers_services bpss LEFT JOIN wp_bookingpress_staffmembers bpsf ON bpss.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = 1 AND bpsf.bookingpress_staffmember_status = 1  AND 1=1  AND bookingpress_staffmember_id IN (1,3)
                            
                            if(!empty($bookingpress_assigned_service_details)){
                                $staff_member_ids = array();
                                foreach($bookingpress_assigned_service_details as $k2 => $v2){
                                    $bookingpress_staffmember_id =  $v2['bookingpress_staffmember_id'];
                                    $total_booked_appointment = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( bookingpress_appointment_booking_id ) as total_booked_appointment FROM ".$tbl_bookingpress_appointment_bookings." WHERE bookingpress_appointment_date LIKE '{$bookingpress_current_date}' AND bookingpress_staff_member_id = %d", $bookingpress_staffmember_id ) ); //phpcs:ignore
                                    $staff_member_ids[ $bookingpress_staffmember_id ] = $total_booked_appointment;
                                }
                                
                                $filter_appointment_staffmember = array_keys( $staff_member_ids, $minmax( $staff_member_ids ) );

                                if( count( $filter_appointment_staffmember ) > 0 ){
                                    $bookingpress_staffmember_id = array_rand( $staff_member_ids );
                                } else {
                                    $bookingpress_staffmember_id = $filter_appointment_staffmember;
                                }
                            }
                        } else {
                            $bookingpress_is_staffmember_assigned = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(bpss.bookingpress_staffmember_service_id) as total FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d" . $where_clause, $bookingpress_selected_service_id, 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: 
                            
                            if($bookingpress_is_staffmember_assigned['total'] != 0 && $bookingpress_is_staffmember_assigned['total'] > 1){
                                $bookingpress_assigned_service_details = $wpdb->get_results($wpdb->prepare( "SELECT bpsfs.* FROM ".$tbl_bookingpress_staffmembers_services." bpsfs LEFT JOIN ".$tbl_bookingpress_staffmembers." bpsf ON bpsfs.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpsfs.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d " . $where_clause, $bookingpress_selected_service_id, 1), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm

                                
                                if(!empty($bookingpress_assigned_service_details))
                                {
                                    $bookingpress_staff_counter = array();
                                    foreach($bookingpress_assigned_service_details as $k2 => $v2){
                                        $current_staffmember_id = $v2["bookingpress_staffmember_id"];
                                        $bookingpress_least_assigned_staff_details = $wpdb->get_row( $wpdb->prepare( "SELECT count(bpa.bookingpress_appointment_booking_id) as total_booked_appointment FROM {$tbl_bookingpress_appointment_bookings} as bpa WHERE bpa.bookingpress_staff_member_id = %d AND ( bpa.bookingpress_appointment_date LIKE %s OR bpa.bookingpress_appointment_date IS NULL ) AND ( bpa.bookingpress_appointment_status IS NULL OR bpa.bookingpress_appointment_status = %d OR bpa.bookingpress_appointment_status = %d ) ", $current_staffmember_id, $bookingpress_current_date, 1, 2 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers & $tbl_bookingpress_staffmembers are table name.
                                        $bookingpress_staff_counter[$current_staffmember_id] = $bookingpress_least_assigned_staff_details['total_booked_appointment'];
                                    }

                                    
                                    if(is_array($bookingpress_staff_counter) && count($bookingpress_staff_counter)> 0 ) 
                                    {
                                        $min = $minmax($bookingpress_staff_counter);
                                        $index = array_search($min, $bookingpress_staff_counter);
                                        $bookingpress_staffmember_id =  $index;
                                    }
                                }
                            } else if( $bookingpress_is_staffmember_assigned['total'] == 1 ) {
                                $bookingpress_is_staffmember_assigned = $wpdb->get_row($wpdb->prepare( "SELECT bpss.bookingpress_staffmember_id FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d" . $where_clause, $bookingpress_selected_service_id, 1 ), ARRAY_A); // phpcs:ignore
                                $bookingpress_staffmember_id =  $bookingpress_is_staffmember_assigned['bookingpress_staffmember_id'];
                            }
                        }
                        
                    } else {
                        $bookingpress_least_assigned_staff_details = $wpdb->get_row( $wpdb->prepare( "SELECT SUM( ( CASE WHEN bpa.bookingpress_appointment_booking_id IS NOT NULL THEN 1 ELSE 0 END ) ) as total_booked_appointment, bpsf.bookingpress_staffmember_id as bookingpress_staff_member_id, bpa.bookingpress_appointment_status FROM {$tbl_bookingpress_appointment_bookings} bpa RIGHT JOIN {$tbl_bookingpress_staffmembers} bpsf ON bpa.bookingpress_staff_member_id = bpsf.bookingpress_staffmember_id RIGHT JOIN {$tbl_bookingpress_staffmembers_services} bpss ON bps.bookingpress_staffmember_id = bpss.bookingpress_staffmember_id WHERE ( bpa.bookingpress_appointment_date LIKE %s OR bpa.bookingpress_appointment_date IS NULL ) AND ( bpa.bookingpress_appointment_status IS NULL OR bpa.bookingpress_appointment_status = %d OR bpa.bookingpress_appointment_status = %d ) AND bpsf.bookingpress_staffmember_id != 0 AND bpsf.bookingpress_staffmember_status = %d {$where_clause} GROUP BY bpa.bookingpress_staff_member_id ORDER BY total_booked_appointment $ordby", $bookingpress_current_date, 1, 2, 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers & $tbl_bookingpress_staffmembers are table name.


                        if(!empty($bookingpress_least_assigned_staff_details)){
                            $bookingpress_staffmember_id = $bookingpress_least_assigned_staff_details['bookingpress_staff_member_id'];
                        } else {
                            /** Fetch all staff members with assigned services */
                            $bookingpress_all_staffmembers = $wpdb->get_results( $wpdb->prepare( "SELECT bpsf.bookingpress_staffmember_id FROM {$tbl_bookingpress_staffmembers} bpsf RIGHT JOIN {$tbl_bookingpress_staffmembers_services} bpss ON bpsf.bookingpress_staffmember_id=bpss.bookingpress_staffmember_id WHERE bpsf.bookingpress_staffmember_status = %d {$where_clause} GROUP BY bpsf.bookingpress_staffmember_id", 1)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers & $tbl_bookingpress_staffmembers_services  is a table name. false alarm

                            $all_staff_ids = array();
                            foreach( $bookingpress_all_staffmembers as $bpa_staffmembers ){
                                $all_staff_ids[] = $bpa_staffmembers->bookingpress_staffmember_id;
                            }
                            
                            if( !empty( $all_staff_ids ) ){
                                if( count( $all_staff_ids ) == 1 ){
                                    $bookingpress_staffmember_id = $all_staff_ids[0];
                                } else {
                                    $min = $minmax($all_staff_ids);
                                    $index = array_search($min, $all_staff_ids);
                                    $bookingpress_staffmember_id =  $all_staff_ids[ $index ];
                                }
                            }
                        }
                    }
                }
            } else if( "least_assigned_by_week" == $bookingpress_any_staff_selected_rule || "most_assigned_by_week" == $bookingpress_any_staff_selected_rule ) {
                
                $get_staff_with_serviceloc = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $location_id, $bookingpress_selected_service_id )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                /** return default staff id if the data not exists */
                if( empty( $get_staff_with_serviceloc ) ){
                    $bookingpress_pro_appointment_bookings->bookingpress_get_any_staffmember_id_func();
                    die;
                }

                if( count( $get_staff_with_serviceloc ) == 1 ){
                    $bookingpress_staffmember_id = $get_staff_with_serviceloc[0]->bookingpress_staffmember_id;
                } else {
                    $bookingpress_staff_ids = array();
                    foreach( $get_staff_with_serviceloc as $staff_id_data ){
                        $bookingpress_staff_ids[] = $staff_id_data->bookingpress_staffmember_id;
                    }

                    $where_clause .= ' AND bpsf.bookingpress_staffmember_id IN ('. implode( ',', $bookingpress_staff_ids ).')';
                    
                    $ordby = "ASC";
                    $minmax = "min";
                    if( "most_assigned_by_week" == $bookingpress_any_staff_selected_rule ){
                        $ordby = "DESC";
                        $minmax = "max";
                    }

                    if(!empty($bookingpress_selected_service_id)){

						$bookingpress_search_query       = 'WHERE 1=1 ';
						$bookingpress_search_query_where .= "AND (bookingpress_service_id = {$bookingpress_selected_service_id} ) ";
						$bookingpress_search_query_where .= $wpdb->prepare( "AND ( bookingpress_appointment_date >= %s AND bookingpress_appointment_date <= %s ) AND ( bookingpress_appointment_status IS NULL OR bookingpress_appointment_status = 1 OR bookingpress_appointment_status = 2 )", $bookingpress_week_start_date, $bookingpress_week_end_date );

						$bookingpress_total_appointments = $wpdb->get_var("SELECT COUNT(bookingpress_appointment_booking_id) FROM {$tbl_bookingpress_appointment_bookings} {$bookingpress_search_query} {$bookingpress_search_query_where} ORDER BY bookingpress_appointment_date $ordby"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
						
						if($bookingpress_total_appointments == 0){
							$bookingpress_assigned_service_details = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ".$tbl_bookingpress_staffmembers." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d" . $where_clause, $bookingpress_selected_service_id, 1), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm
							if(!empty($bookingpress_assigned_service_details)){
								foreach($bookingpress_assigned_service_details as $k2 => $v2){
									$bookingpress_staffmember_id =  $v2['bookingpress_staffmember_id'];
								}
							}
						} else {
							// FETCH ALL STAFF MEMBER'S COUNT FOR BOOKED APPOINTMENT NOT ONLY BOOKED ONES
							$bookingpress_is_staffmember_assigned = $wpdb->get_row( $wpdb->prepare( "SELECT COUNT(bookingpress_staffmember_service_id) as total FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ".$tbl_bookingpress_staffmembers." bpsf ON bpss.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d" . $where_clause, $bookingpress_selected_service_id, 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm

							if($bookingpress_is_staffmember_assigned['total'] != 0 && $bookingpress_is_staffmember_assigned['total'] > 1){
								$bookingpress_assigned_service_details = $wpdb->get_results($wpdb->prepare( "SELECT * FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id=bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d", $bookingpress_selected_service_id, 1 ), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm

								if(!empty($bookingpress_assigned_service_details)){
									$bookingpress_staff_counter = array();
									foreach($bookingpress_assigned_service_details as $k2 => $v2){
										$current_staffmember_id = $v2["bookingpress_staffmember_id"];
										$bookingpress_least_assigned_staff_details = $wpdb->get_row( $wpdb->prepare( "SELECT count(bpa.bookingpress_appointment_booking_id) as total_booked_appointment FROM {$tbl_bookingpress_appointment_bookings} as bpa WHERE bpa.bookingpress_staff_member_id = %d AND ( bpa.bookingpress_appointment_date >= %s AND bpa.bookingpress_appointment_date <= %s ) AND ( bpa.bookingpress_appointment_status IS NULL OR bpa.bookingpress_appointment_status = %d OR bpa.bookingpress_appointment_status = %d )  ", $current_staffmember_id, $bookingpress_week_start_date, $bookingpress_week_start_date, 1, 2 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers & $tbl_bookingpress_staffmembers are table name.
										$bookingpress_staff_counter[$current_staffmember_id] = $bookingpress_least_assigned_staff_details['total_booked_appointment'];
									}

									if(is_array($bookingpress_staff_counter) && count($bookingpress_staff_counter)> 0 ) {
										$min = $minmax($bookingpress_staff_counter);
										$index = array_search($min, $bookingpress_staff_counter);
										$bookingpress_staffmember_id =  $index;
									}
								}
							} else if( $bookingpress_is_staffmember_assigned['total'] == 1 ) {
								$bookingpress_is_staffmember_assigned = $wpdb->get_row($wpdb->prepare( "SELECT bpss.bookingpress_staffmember_id FROM ".$tbl_bookingpress_staffmembers_services." bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d" . $where_clause, $bookingpress_selected_service_id, 1 ), ARRAY_A); // phpcs:ignore
								$bookingpress_staffmember_id =  $bookingpress_is_staffmember_assigned['bookingpress_staffmember_id'];
							}
						}
					} else {
						
						$bookingpress_least_weekly_assigned_staff_details = $wpdb->get_row( $wpdb->prepare( "SELECT SUM( ( CASE WHEN bpa.bookingpress_appointment_booking_id IS NOT NULL THEN 1 ELSE 0 END ) ) as total_booked_appointment, bpsf.bookingpress_staffmember_id as bookingpress_staff_member_id, bpa.bookingpress_appointment_status FROM {$tbl_bookingpress_appointment_bookings} bpa RIGHT JOIN {$tbl_bookingpress_staffmembers} bpsf ON bpa.bookingpress_staff_member_id = bpsf.bookingpress_staffmember_id WHERE ( ( bpa.bookingpress_appointment_date >= %s AND bpa.bookingpress_appointment_date <= %s ) OR bpa.bookingpress_appointment_date IS NULL ) AND ( bpa.bookingpress_appointment_status IS NULL OR bpa.bookingpress_appointment_status = %d OR bpa.bookingpress_appointment_status = %d ) AND bpsf.bookingpress_staffmember_id != 0 AND bpsf.bookingpress_staffmember_status = %d {$where_clause} GROUP BY bpa.bookingpress_staff_member_id ORDER BY total_booked_appointment ASC", $bookingpress_week_start_date, $bookingpress_week_end_date, 1, 2, 1 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers & $tbl_bookingpress_appointment_bookings are table name.
					}
					if(!empty($bookingpress_least_weekly_assigned_staff_details)){
						$bookingpress_staffmember_id = $bookingpress_least_weekly_assigned_staff_details['bookingpress_staff_member_id'];
					}
    
                    
                }

            } else if($bookingpress_any_staff_selected_rule == "most_expensive") {
                $get_staff_with_serviceloc = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $location_id, $bookingpress_selected_service_id )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                /** return default staff id if the data not exists */
                if( empty( $get_staff_with_serviceloc ) ){
                    $bookingpress_pro_appointment_bookings->bookingpress_get_any_staffmember_id_func();
                    die;
                }

                if( count( $get_staff_with_serviceloc ) == 1 ){
                    $bookingpress_staffmember_id = $get_staff_with_serviceloc[0]->bookingpress_staffmember_id;
                } else {
                    $bookingpress_staff_ids = array();
                    foreach( $get_staff_with_serviceloc as $staff_id_data ){
                        $bookingpress_staff_ids[] = $staff_id_data->bookingpress_staffmember_id;
                    }

                    $where_clause .= ' AND bpsf.bookingpress_staffmember_id IN ('. implode( ',', $bookingpress_staff_ids ).')';
                    
                    if(!empty($bookingpress_selected_service_id)){
						$bookingpress_assigned_staffmembers_details = $wpdb->get_row( $wpdb->prepare( "SELECT bpss.bookingpress_staffmember_id FROM {$tbl_bookingpress_staffmembers_services} bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d "  . $where_clause . " ORDER BY bookingpress_service_price DESC", $bookingpress_selected_service_id, 1 ), ARRAY_A ); // phpcs:ignore
					}else{
						$bookingpress_assigned_staffmembers_details = $wpdb->get_row( $wpdb->prepare( "SELECT bpss.bookingpress_staffmember_id FROM {$tbl_bookingpress_staffmembers_services} bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpsf.bookingpress_staffmember_status = %d ORDER BY bookingpress_service_price DESC", 1), ARRAY_A); // phpcs:ignore
					}
					if(!empty($bookingpress_assigned_staffmembers_details)){
						$bookingpress_staffmember_id = $bookingpress_assigned_staffmembers_details['bookingpress_staffmember_id'];
					}
                }
            } else if($bookingpress_any_staff_selected_rule == "least_expensive") {
                
                $get_staff_with_serviceloc = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $location_id, $bookingpress_selected_service_id )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                /** return default staff id if the data not exists */
                if( empty( $get_staff_with_serviceloc ) ){
                    $bookingpress_pro_appointment_bookings->bookingpress_get_any_staffmember_id_func();
                    die;
                }

                if( count( $get_staff_with_serviceloc ) == 1 ){
                    $bookingpress_staffmember_id = $get_staff_with_serviceloc[0]->bookingpress_staffmember_id;
                } else {
                    $bookingpress_staff_ids = array();
                    foreach( $get_staff_with_serviceloc as $staff_id_data ){
                        $bookingpress_staff_ids[] = $staff_id_data->bookingpress_staffmember_id;
                    }

                    $where_clause .= ' AND bpsf.bookingpress_staffmember_id IN ('. implode( ',', $bookingpress_staff_ids ).')';
                    
                    if(!empty($bookingpress_selected_service_id)){
						$bookingpress_assigned_staffmembers_details = $wpdb->get_row($wpdb->prepare("SELECT bpss.bookingpress_staffmember_id FROM {$tbl_bookingpress_staffmembers_services} bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpss.bookingpress_service_id = %d AND bpsf.bookingpress_staffmember_status = %d " . $where_clause . " ORDER BY bookingpress_service_price ASC", $bookingpress_selected_service_id, 1), ARRAY_A); // phpcs:ignore
					}else{
						$bookingpress_assigned_staffmembers_details = $wpdb->get_row($wpdb->prepare("SELECT bpss.bookingpress_staffmember_id FROM {$tbl_bookingpress_staffmembers_services} bpss LEFT JOIN ". $tbl_bookingpress_staffmembers ." bpsf ON bpss.bookingpress_staffmember_id = bpsf.bookingpress_staffmember_id WHERE bpsf.bookingpress_staffmember_status = %d {$where_clause} ORDER BY bookingpress_service_price ASC", 1), ARRAY_A); // phpcs:ignore
					}
					if(!empty($bookingpress_assigned_staffmembers_details)){
						$bookingpress_staffmember_id = $bookingpress_assigned_staffmembers_details['bookingpress_staffmember_id'];
					}
                }
            }

            $response['variant'] = 'success';
            $response['title'] = esc_html__( 'Success', 'bookingpress-location' );
            $response['msg']     = esc_html__( 'Data retrieved successfully', 'bookingpress-location' );
            $response['staffmember_id'] = $bookingpress_staffmember_id;

            echo wp_json_encode($response);
			exit;
        }

        function bookingpress_set_location_capacity_for_service( $max_service_capacity, $selected_service_id ){

            $location_id = !empty( $_POST['appointment_data_obj']['selected_location'] ) ? intval( $_POST['appointment_data_obj']['selected_location'] ) : 0; // phpcs:ignore

            if( 1 > $location_id ){
                return $max_service_capacity;
            }

            global $bookingpress_pro_staff_members, $tbl_bookingpress_locations_service_staff_pricing_details, $wpdb;

            if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                return $max_service_capacity;
            }

            $get_service_qty = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_qty FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d", $location_id, $selected_service_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.


            if( !empty( $get_service_qty ) ){
                $max_service_capacity = $get_service_qty;
            }

            return $max_service_capacity;
        }

        function bookingpress_reset_location_field_display_after_empty_cart( $bookingpress_after_empty_cart ){

            $bookingpress_after_empty_cart .= 'if( 1 == vm.bookingpress_sidebar_step_data.location.is_first_step_with_cart ){
                vm.bookingpress_sidebar_step_data.location.is_first_step_with_cart = 0;
                vm.bookingpress_sidebar_step_data.location.is_first_step = 1;

                let next_tab = vm.bookingpress_sidebar_step_data.location.next_tab_name;
                vm.bookingpress_sidebar_step_data[next_tab].is_first_step = 0;
                vm.bookingpress_sidebar_step_data[next_tab].is_first_step_from_cart = 0;
            }
            
            vm.bookingpress_sidebar_step_data.location.is_display_step = 1;
            vm.appointment_step_form_data.bookingpress_selected_cart_location = "";
            vm.appointment_step_form_data.selected_location = "";
            ';

            return $bookingpress_after_empty_cart;
        }

        function bookingpress_display_staff_service_based_on_location_add_to_cart_func( $bookingpress_before_add_more_service_to_cart ){

            $bookingpress_before_add_more_service_to_cart .= '
                let bookingpress_selected_location  = vm.appointment_step_form_data.bookingpress_selected_cart_location;

                /** if staff exists, first step, and visible */
                if( 1 == vm.is_staffmember_activated && 1 == vm.bookingpress_sidebar_step_data.staffmembers.is_first_step ){
                    let selected_location_data = vm.bookingpress_locations_list[ bookingpress_selected_location ];
                    if( "undefined" != typeof selected_location_data.bookingpress_staffmembers ){
                        let loc_staff_details = selected_location_data.bookingpress_staffmembers;
                        vm.bookingpress_staffmembers_details.forEach( function( element,index ){
                            let staff_id = element.bookingpress_staffmember_id;
                            vm.bookingpress_staffmembers_details[index].is_display_staff_with_flag = true;
                            if( 0 > loc_staff_details.indexOf( staff_id ) ){
                                vm.bookingpress_staffmembers_details[index].is_display_staff_with_flag = false;
                            }
                        });
                    }
                }

                /** if service step is first */
                if( 1 == vm.bookingpress_sidebar_step_data.service.is_first_step ){
                    let selected_location_data = vm.bookingpress_locations_list[ bookingpress_selected_location ];
                    if( "undefined" != typeof selected_location_data.bookingpress_location_service_ids ){
                        let loc_service_details = selected_location_data.bookingpress_location_service_ids;
                        for( let service_id in vm.bookingpress_all_services_data ){
                            let service_details = vm.bookingpress_all_services_data[ service_id ];
                            vm.bookingpress_all_services_data[ service_id ].is_visible_with_flag = true;
                            if( 0 > loc_service_details.indexOf( service_id ) ){
                                vm.bookingpress_all_services_data[ service_id ].is_visible_with_flag = false;
                            }
                        }
                    }
                }

                let selected_category = vm.appointment_step_form_data.selected_category;
                if( "" != selected_category ){
                    vm.bpa_select_category( selected_category );
                }


            ';

            return $bookingpress_before_add_more_service_to_cart;
        }

        function bookingpress_modify_cart_data_xhr_after_add_to_cart( $bookingpress_modify_cart_xhr_response_data ){

            $bookingpress_modify_cart_xhr_response_data .= '
                if( vm.appointment_step_form_data.cart_items.length > 0 ){

                    let selected_location = vm.appointment_step_form_data.selected_location;
                    vm.appointment_step_form_data.bookingpress_selected_cart_location = selected_location;
                    vm.bookingpress_sidebar_step_data.location.is_display_step = 0;
                    vm.bookingpress_sidebar_step_data.location.is_display_on_reset_cart = 1;
                    console.log( vm.bookingpress_sidebar_step_data.location.is_first_step );

                    if( 1 == vm.bookingpress_sidebar_step_data.location.is_first_step ){

                        vm.bookingpress_sidebar_step_data.location.is_first_step = 0;
                        vm.bookingpress_sidebar_step_data.location.is_first_step_with_cart = 1;

                        /** change next step from the location */
                        
                        let next_tab = vm.bookingpress_sidebar_step_data.location.next_tab_name;
                        vm.bookingpress_sidebar_step_data[next_tab].is_first_step = 1;
                        vm.bookingpress_sidebar_step_data[next_tab].is_first_step_from_cart = 1;
                        
                    } else {

                        /** change next & previous step from the location */
                        let loc_next_tab = vm.bookingpress_sidebar_step_data.location.next_tab_name;
                        let loc_prev_tab = vm.bookingpress_sidebar_step_data.location.previous_tab_name;

                        vm.bookingpress_sidebar_step_data[ loc_prev_tab ].next_tab_name = loc_next_tab;
                        vm.bookingpress_sidebar_step_data[ loc_next_tab ].previous_tab_name = loc_prev_tab;

                        vm.bookingpress_sidebar_step_data[ loc_prev_tab ].loc_next_tab = true;
                        vm.bookingpress_sidebar_step_data[ loc_next_tab ].loc_prev_tab = true;

                    }


                    if( "" != selected_location ){
                        for( let cat_id in vm.bookingpress_all_categories ){
                            if( 0 == vm.bookingpress_all_categories[cat_id].bookingpress_category_id ){
                                continue;
                            }
                            let total_service = vm.bookingpress_all_categories[cat_id].service_ids.length;
                            console.log( total_service );
                            let service_ids = vm.bookingpress_all_categories[cat_id].service_ids;
                            console.log( service_ids );
                            let hidden_services = 0;
    
                            service_ids.forEach( (selm, sindex)=>{
                                if( vm.bookingpress_all_services_data[selm].is_visible_with_flag == false || vm.bookingpress_all_services_data[selm].is_visible == false ) {
                                    hidden_services++;
                                }
                            });
                            
                            if( total_service == hidden_services ){
                                vm.bookingpress_all_categories[cat_id].is_visible = false;
                                vm.bookingpress_all_categories[cat_id].is_hidden_with_location_cart = true;
                            }
                        }
                    }
                
                } else {

                    if( "undefined" != typeof vm.bookingpress_sidebar_step_data.location.is_display_on_reset_cart && 1 == vm.bookingpress_sidebar_step_data.location.is_display_on_reset_cart ){

                        for( let cat_id in vm.bookingpress_all_categories ){
                            console.log( vm.bookingpress_all_categories[cat_id] );
                            if( vm.bookingpress_all_categories[cat_id].is_visible == false && vm.bookingpress_all_categories[cat_id].is_hidden_with_location_cart == true ){
                                vm.bookingpress_all_categories[cat_id].is_visible = true;
                                vm.bookingpress_all_categories[cat_id].is_hidden_with_location_cart = false;
                            }
                        }

                        for( let steps in vm.bookingpress_sidebar_step_data ){
                            let currentStep = vm.bookingpress_sidebar_step_data[steps];

                            if( "undefined" != typeof currentStep.loc_next_tab && true == currentStep.loc_next_tab ){
                                vm.bookingpress_sidebar_step_data[steps].next_tab_name = "location";
                                delete vm.bookingpress_sidebar_step_data[steps].loc_next_tab;
                            }

                            if( "undefined" != typeof currentStep.loc_prev_tab && true == currentStep.loc_prev_tab ){
                                vm.bookingpress_sidebar_step_data[steps].previous_tab_name = "location";
                                delete vm.bookingpress_sidebar_step_data[steps].loc_prev_tab;
                            }
                        }

                        if( 1 == vm.is_staffmember_activated && 1 == vm.bookingpress_sidebar_step_data.staffmembers.is_first_step ){
                            vm.bookingpress_staffmembers_details.forEach( function( element,index ){
                                let staff_id = element.bookingpress_staffmember_id;
                                if( true == element.is_display_staff && false == element.is_display_staff_with_flag ){
                                    vm.bookingpress_staffmembers_details[index].is_display_staff_with_flag = true;
                                }
                            });
                        }

                        if( 1 == vm.bookingpress_sidebar_step_data.service.is_first_step ){
                            for( let service_id in vm.bookingpress_all_services_data ){
                                let service_data = vm.bookingpress_all_services_data[ service_id ];
                                if( true == service_data.is_visible && false == service_data.is_visible_with_flag ){
                                    service_data.is_visible_with_flag = true;
                                }
                            }
                        }

                        let selected_category = vm.appointment_step_form_data.selected_category;
                        vm.bpa_select_category( selected_category );
                    }
                }
            ';

            return $bookingpress_modify_cart_xhr_response_data;
        }

        function bookingpress_add_location_data_in_cart( $bookingpress_location_data ){ 

            $bookingpress_location_data .='
                var bookingpress_selected_location = vm5.appointment_step_form_data.selected_location;
                let bookingpress_selected_location_name = vm5.bookingpress_locations_list[ bookingpress_selected_location ].bookingpress_location_name;
                
                currentValue.bookingpress_selected_location_id = bookingpress_selected_location;
                currentValue.bookingpress_selected_location_name = bookingpress_selected_location_name;
                ';
                
            return $bookingpress_location_data;
        }

        function bookingpress_cart_content_add_outside_func(){ ?>
            <div class="bpa-cart-iev__h-item">
                <div class="bpa-cart-iev__hi-label" v-if="cart_location_title !=''">{{cart_location_title}}:</div>
                
                <div class="bpa-cart-iev__hi-val">{{bookingpress_cart_details.bookingpress_selected_location_name}}</div>
            </div>
        <?php }

        function bookingpress_cart_mobile_content_add_outside_func(){ ?>
            <div class="bpa-bo__item">
                <div class="bpa-boi__left" v-if="cart_location_title != ''">{{cart_location_title}}:</div>
                <div class="bpa-boi__right">{{bookingpress_cart_details.bookingpress_selected_location_name}}</div>
            </div>
        <?php }

        function bookingpress_add_summary_content_outside_func(){  ?>  

            <div class="bpa-is-location-val__summary" v-if='bpa_location_title != ""'>
                <div class="bpa-lvs__label">{{bpa_location_title}}</div>
                <div class="bpa-lvs__val">
                    <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
                    <div class="bpa-lvs__val-text">{{appointment_step_form_data.selected_location_name}}</div>
                </div>
            </div>

        <?php }

        function bookingpress_after_selecting_anystaffmember( $bookingpress_after_selecting_anystaffmember ){

            $bookingpress_after_selecting_anystaffmember = '
                let selected_staff  = vm.appointment_step_form_data.selected_staff_member_id.toString();
                let selected_service = vm.appointment_step_form_data.selected_service.toString();
                
                let all_locations = vm.bookingpress_locations_list;
                for( let locid in all_locations ){
                    let loc_data = all_locations[locid];
                    let loc_services = loc_data.bookingpress_location_service_ids;
                    let loc_staff = loc_data.bookingpress_staffmembers;
                    if( "undefined" != typeof loc_services && "undefined" != typeof loc_staff ){
                        vm.bookingpress_locations_list[ locid ].is_visible = false;
                        if( -1 < loc_services.indexOf( selected_service ) && -1 < loc_staff.indexOf( selected_staff ) ){
                            vm.bookingpress_locations_list[ locid ].is_visible = true;
                        }
                    }
                }

                vm.isLoadLocationLoader = 0;
            ';

            return $bookingpress_after_selecting_anystaffmember;
        }

        function bookingpress_hide_show_location_based_on_service( $bookingpress_before_selecting_booking_service_data ){

            $bookingpress_before_selecting_booking_service_data .= '
                let selected_location = vm.appointment_step_form_data.selected_location;

                /** Check if the staff is activated, service is selected, staff is hidden and the current tab is location */
                if( "" != selected_service_id && 1 == vm.is_staffmember_activated && 0 == vm.bookingpress_sidebar_step_data.staffmembers.is_display_step && "location" == vm.bookingpress_current_tab ){
                    let selected_staff = vm.appointment_step_form_data.selected_staff_member_id
                    if( "" == selected_location && "" == selected_staff ){
                        /** if location is not selected then call any staff member */
                        vm.isLoadLocationLoader = 1;
                        const d = await this.bookingpress_select_staffmember("any_staff", 1 );
                    }

                } else {
                    if( "" == selected_location && "" != selected_service_id ){
                        let all_locations = vm.bookingpress_locations_list;
                        selected_service_id = selected_service_id.toString()
                        if( 1 == vm.is_staffmember_activated ){
                            let selected_staff = vm.appointment_step_form_data.selected_staff_member_id;
                            if( 0 == vm.bookingpress_sidebar_step_data.staffmembers.is_display_step ){
                                if( "" != selected_staff ){
                                    for( let locid in all_locations ){
                                        let loc_data = all_locations[locid];
                                        let loc_services = loc_data.bookingpress_location_service_ids;
                                        let loc_staff = loc_data.bookingpress_staffmembers;
                                        
                                        if( "undefined" != typeof loc_services && "undefined" != typeof loc_staff ){
                                            vm.bookingpress_locations_list[ locid ].is_visible = false;
                                            if( -1 <  loc_services.indexOf( selected_service_id ) && -1 < loc_staff.indexOf( selected_staff ) ){
                                                vm.bookingpress_locations_list[ locid ].is_visible = true;
                                            }
                                        }
                                    }
                                } else {
                                    let location_details = vm.bookingpress_all_services_data[ selected_service_id ].locations;
                                    for( let locid in all_locations ){
                                        let loc_data = all_locations[locid];
                                        vm.bookingpress_locations_list[locid].is_visible = false;
                                        if( "undefined" != typeof location_details[ locid ] ){
                                            vm.bookingpress_locations_list[locid].is_visible = true;
                                        }
                                    }
                                }
                            } else {
                                let location_details = vm.bookingpress_all_services_data[ selected_service_id ].locations;
                                for( let locid in all_locations ){
                                    let loc_data = all_locations[locid];
                                    vm.bookingpress_locations_list[locid].is_visible = false;
                                    if( "undefined" != typeof location_details[ locid ] ){
                                        vm.bookingpress_locations_list[locid].is_visible = true;
                                    }
                                }    
                            }
                        }else{
                            /* When location is second step display location based on selected service id*/
                            let all_locations = vm.bookingpress_locations_list;
                            selected_service_id = selected_service_id.toString()
                            let location_details = vm.bookingpress_all_services_data[ selected_service_id ].locations;
                            for( let locid in all_locations ){
                                let loc_data = all_locations[locid];
                                vm.bookingpress_locations_list[locid].is_visible = false;
                                if( "undefined" != typeof location_details[ locid ] ){
                                    vm.bookingpress_locations_list[locid].is_visible = true;
                                    if( 1 == vm.is_bring_anyone_with_you_activated ){

                                        let service_location_min_qty = vm.bookingpress_locations_list[ locid ].bookingpress_location_service_min_qty[ selected_service_id ];
                                        service_location_min_qty = parseInt( service_location_min_qty );
                                        vm.appointment_step_form_data.service_min_capacity = service_location_min_qty;
                                        vm.appointment_step_form_data.bookingpress_selected_bring_members = vm.appointment_step_form_data.service_min_capacity;
                                    }
                                }
                            }
                        }
                    } else{
                        if( "" != selected_location ){
                            let selected_staff = vm.appointment_step_form_data.selected_staff_member_id;
                           
                            if( 1 == vm.is_staffmember_activated ){
                                
                                if( "" == selected_staff ){
                                    let location_data = vm.bookingpress_all_locations_list[ selected_location ];
                                    let staff_min_capacities = [];

                                    console.log( location_data.bookingpress_location_staffwise_qty );
                                    vm.bookingpress_staffmembers_details.forEach( ( element, index ) => {
                                        vm.bookingpress_staffmembers_details[index].is_display_staff = false;
                                        let staff_id = element.bookingpress_staffmember_id;
                                        if( element.staffmember_visibility == "public" && "undefined" != typeof element.assigned_service_price_details[selected_service_id] && "undefined" != typeof vm.bookingpress_locations_list && "undefined" != typeof vm.bookingpress_locations_list[selected_location].bookingpress_staffmembers && -1 < vm.bookingpress_locations_list[selected_location].bookingpress_staffmembers.indexOf( staff_id ) ){
                                            vm.bookingpress_staffmembers_details[index].is_display_staff = true;
                                            if( 1 == vm.is_bring_anyone_with_you_activated ){
                                                
                                                console.log( staff_id );
                                                if( "undefined" != typeof location_data.bookingpress_location_staffwise_qty[ staff_id ] ){
                                                    
                                                    let staffwise_qty = location_data.bookingpress_location_staffwise_qty[ staff_id ];
													if( "undefined" != typeof staffwise_qty[ selected_service_id ] ){
                                                    	let staffwise_min_capacity = staffwise_qty[ selected_service_id ].min_capacity;
                                                    	staff_min_capacities.push( parseInt( staffwise_min_capacity ) );
													}
                                                }
                                            }
                                        }
                                        
                                        let all_locations = vm.bookingpress_locations_list;
                                        selected_service_id = selected_service_id.toString()
                                        if( "" != selected_service_id){
                                            /*This condition is added when location step is second and already one location is selected*/
                                            let all_locations = vm.bookingpress_locations_list;
                                            selected_service_id = selected_service_id.toString()
                                            let location_details = vm.bookingpress_all_services_data[ selected_service_id ].locations;
                                            for( let locid in all_locations ){
                                                let loc_data = all_locations[locid];
                                                vm.bookingpress_locations_list[locid].is_visible = false;
                                                if( "undefined" != typeof location_details[ locid ] ){
                                                    vm.bookingpress_locations_list[locid].is_visible = true;
                                                }
                                            }
                                        } 
                                    });

                                    if( 1 == vm.is_bring_anyone_with_you_activated ){
                                        service_location_min_qty = Math.min( ...staff_min_capacities );
                                        vm.appointment_step_form_data.service_min_capacity = service_location_min_qty;
                                        vm.appointment_step_form_data.bookingpress_selected_bring_members = vm.appointment_step_form_data.service_min_capacity;
                                        console.log( vm.appointment_step_form_data.bookingpress_selected_bring_members );
                                    }
                                } else {
                                    let location_data = vm.bookingpress_all_locations_list[ selected_location ];
                                    
                                    let staffwise_loc_capacity = location_data.bookingpress_location_staffwise_qty[ selected_staff ];
                                     if( "undefined" != typeof staffwise_loc_capacity[ selected_service_id ] ){
                                            let service_min_capacity = staffwise_loc_capacity[ selected_service_id ].min_capacity;
                                            let service_max_capacity = staffwise_loc_capacity[ selected_service_id ].max_capacity;

                                            vm.appointment_step_form_data.service_min_capacity = parseInt( service_min_capacity );
                                            vm.appointment_step_form_data.bookingpress_selected_bring_members = vm.appointment_step_form_data.service_min_capacity;
                                    		vm.appointment_step_form_data.service_max_capacity = parseInt( service_max_capacity );
									}

                                }
                                

                            } else {
                                
                                /** set the service max capacity to location qty */
                                let service_location_qty = vm.bookingpress_locations_list[ selected_location ].bookingpress_location_service_qty[ selected_service_id ];
                                service_location_qty = parseInt( service_location_qty );
                                vm.appointment_step_form_data.service_max_capacity = service_location_qty;

                                /** set the service min capacity to location qty */
                                if( 1 == vm.is_bring_anyone_with_you_activated ){

                                    let service_location_min_qty = vm.bookingpress_locations_list[ selected_location ].bookingpress_location_service_min_qty[ selected_service_id ];
                                    service_location_min_qty = parseInt( service_location_min_qty );
                                    vm.appointment_step_form_data.service_min_capacity = service_location_min_qty;
                                    vm.appointment_step_form_data.bookingpress_selected_bring_members = vm.appointment_step_form_data.service_min_capacity;
                                }

                                /* open no of person drawer if the capacity is more than 1 */
                                if( 1 == vm.is_bring_anyone_with_you_activated ){
                                    if( 1 < service_location_qty ){
                                        is_move_to_next = "false";
                                        vm.bookingpress_open_extras_drawer = "true";
                                        vm.isServiceLoadTimeLoader = "1";
                                    } else {
                                        /** check if extra is enabled otherwise */
                                    }
                                }

                                if( "" != selected_service_id){
                                    /*This condition is added when location step is second and already one location is selected*/
                                    let all_locations = vm.bookingpress_locations_list;
                                    selected_service_id = selected_service_id.toString()
                                    let location_details = vm.bookingpress_all_services_data[ selected_service_id ].locations;
                                    for( let locid in all_locations ){
                                        let loc_data = all_locations[locid];
                                        vm.bookingpress_locations_list[locid].is_visible = false;
                                        if( "undefined" != typeof location_details[ locid ] ){
                                            vm.bookingpress_locations_list[locid].is_visible = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            ';

            global $bookingpress_pro_staff_members, $bookingpress_pro_appointment_bookings;
            if( !$bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                remove_filter( 'bookingpress_before_selecting_booking_service', array( $bookingpress_pro_appointment_bookings, 'bookingpress_set_service_max_capacity'), 11 );

                $bookingpress_before_selecting_booking_service_data .= '
                    if( selected_service_id != "" ){
                        let sidebar_step_data = vm.bookingpress_sidebar_step_data;

                        if( sidebar_step_data.service.is_first_step == 1 && "" == selected_location ){
                            let max_capacity = 1;
                            let min_capacity = 1;
                            let location_details = vm.bookingpress_locations_list;
                            let location_capacities = [];
                            let location_min_capacities = [];

                            for( let x in location_details ){
                                let current_loc = location_details[x];
                                if( current_loc.is_visible == true  ){
                                    let loc_service_cap = current_loc.bookingpress_location_service_qty[ selected_service_id ] || 1;
                                    location_capacities.push( loc_service_cap );

                                    if( vm.is_bring_anyone_with_you_activated == 1 ){
                                        let loc_service_min_cap = current_loc.bookingpress_location_service_min_qty[ selected_service_id ] || 1;
                                        location_min_capacities.push( loc_service_min_cap );
                                    }
                                }
                            }

                            max_capacity = Math.max.apply(null, location_capacities);
							vm.appointment_step_form_data.service_max_capacity = parseInt(max_capacity);

                            if( vm.is_bring_anyone_with_you_activated == 1 ){

                                min_capacity = Math.min.apply(null, location_min_capacities);
							    vm.appointment_step_form_data.service_min_capacity = parseInt(min_capacity);
                                vm.appointment_step_form_data.bookingpress_selected_bring_members = vm.appointment_step_form_data.service_min_capacity;
                                
                            }
                        } else {
                        }

                        if( vm.is_bring_anyone_with_you_activated == 1 && 1 < vm.appointment_step_form_data.service_max_capacity ){
                            is_move_to_next = "false";
                            vm.bookingpress_open_extras_drawer = "true";
                            vm.isServiceLoadTimeLoader = "1";
                        }
                    }
                ';

            }

            return $bookingpress_before_selecting_booking_service_data;
        }

        function bookingpress_hide_show_location_based_on_guest( $bookingpress_reset_custom_duration_data ){

            global $bookingpress_pro_staff_members;
            if( !$bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                $bookingpress_reset_custom_duration_data .= '
                    const vm_loc = this;
                    let number_of_guests = vm_loc.appointment_step_form_data.bookingpress_selected_bring_members;

                    let selected_s_id = vm_loc.appointment_step_form_data.selected_service;
                    let location_details = vm_loc.bookingpress_locations_list;

                    for( let x in location_details ){
                        let current_loc = location_details[x];
                        vm_loc.bookingpress_locations_list[x].is_visible_with_flag = true;
                        if( current_loc.is_visible == true && ("undefined" == typeof current_loc.bookingpress_location_service_qty[ selected_s_id ] || current_loc.bookingpress_location_service_qty[ selected_s_id ] < number_of_guests) ){
                            vm_loc.bookingpress_locations_list[x].is_visible_with_flag = false;
                        }

                        if( current_loc.is_visible == true && ("undefined" == typeof current_loc.bookingpress_location_service_min_qty[ selected_s_id ] || current_loc.bookingpress_location_service_min_qty[ selected_s_id ] > number_of_guests) ){
                            vm_loc.bookingpress_locations_list[x].is_visible_with_flag = false;
                        }
                    }
                ';
            }

            return $bookingpress_reset_custom_duration_data;
        }

        function bookingpress_add_break_with_locations( $break_days, $bookingpress_selected_service, $bookingpress_selected_staffmember_id ){

            global $wpdb, $bookingpress_pro_staff_members, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_servicesmeta;
            
            if( empty( $_POST['appointment_data_obj']['selected_location'] ) || empty( $bookingpress_selected_service ) ){ // phpcs:ignore
                return $break_days;
            }

            $location_id = intval( $_POST['appointment_data_obj']['selected_location'] ); // phpcs:ignore

            /** Check for staff member's location wise working days */
            if($bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() && !empty($bookingpress_selected_staffmember_id)){

            }

            /** Check for service location wise working days */
            if( !empty($bookingpress_selected_service)){
                // Get service working hours days
                $bookingpress_service_workhour_enable = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_servicemeta_value FROM {$tbl_bookingpress_servicesmeta} WHERE bookingpress_service_id = %d AND bookingpress_servicemeta_name = 'bookingpress_configure_specific_service_workhour'", $bookingpress_selected_service ) );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_servicesmeta is table name defined globally. False Positive alarm

                if(!empty($bookingpress_service_workhour_enable) && $bookingpress_service_workhour_enable->bookingpress_servicemeta_value ){
                    $bookingpress_new_disable_dates_arr = array();
                    $bookingpress_location_wise_working_days = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations_service_workhours} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d AND bookingpress_location_service_workhour_is_break = %d", $bookingpress_selected_service, $location_id, 0 ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is table name defined globally. False alarm

                    if( !empty( $bookingpress_location_wise_working_days ) ){
                        $is_location_service_monday_break             = 0;
                        $is_location_service_tuesday_break            = 0;
                        $is_location_service_wednesday_break          = 0;
                        $is_location_service_thursday_break           = 0;
                        $is_location_service_friday_break             = 0;
                        $is_location_service_saturday_break           = 0;
                        $is_location_service_sunday_break             = 0;

                        foreach( $bookingpress_location_wise_working_days as $location_service_workhour_key => $location_service_workhour_val ){
                            $bookingpress_location_service_start_time = $location_service_workhour_val['bookingpress_location_service_workhour_start_time'];
                            $bookingpress_location_service_end_time   = $location_service_workhour_val['bookingpress_location_service_workhour_end_time'];

                            if( null == $bookingpress_location_service_start_time || null == $bookingpress_location_service_end_time  ){
                                if( 'monday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_monday_break = 1;
                                } else if( 'tuesday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_tuesday_break = 1;
                                } else if( 'wednesday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_wednesday_break = 1;
                                } else if( 'thursday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_thursday_break = 1;
                                } else if( 'friday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_friday_break = 1;
                                } else if( 'saturday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_saturday_break = 1;
                                } else if( 'sunday' == strtolower( $location_service_workhour_val['bookingpress_location_service_workday_key'] ) ){
                                    $is_location_service_sunday_break = 1;
                                } 
                            }
                        }

                        $break_days['monday'] = $is_location_service_monday_break;
                        $break_days['tuesday'] = $is_location_service_tuesday_break;
                        $break_days['wednesday'] = $is_location_service_wednesday_break;
                        $break_days['thursday'] = $is_location_service_thursday_break;
                        $break_days['friday'] = $is_location_service_friday_break;
                        $break_days['saturday'] = $is_location_service_saturday_break;
                        $break_days['sunday'] = $is_location_service_sunday_break;

                        return $break_days;
                    }
                }
            }

            return $break_days;
        }

        function bookingpress_hide_services_without_location_on_all_services(  $bookingpress_modify_select_service_category ){

            $bookingpress_modify_select_service_category.= '
                
                let selected_location = vm.appointment_step_form_data.selected_location;

                if( 0 == selected_cat_id ){
                    if( selected_location != "" && "undefined" != typeof vm.bookingpress_all_services_data[ bpa_service_id ].locations ){
                        if( "undefined" == typeof vm.bookingpress_all_services_data[ bpa_service_id ].locations[ selected_location ] ){
                            vm.bookingpress_all_services_data[ bpa_service_id ].is_visible_with_flag = false;
                        } else {
                            vm.bookingpress_all_services_data[ bpa_service_id ].is_visible_with_flag = true;
                        }
                    }
                } else {
                    if( "" != selected_location ){
                        for( let sid in vm.bookingpress_all_services_data ){
                            let cur_service = vm.bookingpress_all_services_data[ sid ];
                            if( cur_service.bookingpress_category_id == selected_cat_id && "undefined" != typeof cur_service.locations && "undefined" != typeof cur_service.locations[ selected_location ] ){
                                vm.bookingpress_all_services_data[ sid ].is_visible_with_flag = true;
                            } else {
                                vm.bookingpress_all_services_data[ sid ].is_visible_with_flag = false;
                            }
                        }
                    }
                }

            
            ';

            return $bookingpress_modify_select_service_category;
        }


        function bookingpress_modify_service_array_with_locations( $bpa_all_services, $service, $selected_service, $bookingpress_category ){

            if( !empty( $bpa_all_services ) ){
                global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
                
                foreach( $bpa_all_services as $bpskey => $bpsvalue ){

                    if( true == $bpsvalue['is_disabled'] ){
                        $bpa_all_services[$bpskey]['is_visible_with_flag'] = false;   
                        continue;
                    }

                    if( true == $bpsvalue['is_visible'] ){
                        
                        $bookingpress_service_id = $bpsvalue['bookingpress_service_id'];
                        $assigned_locations = array();
                        $assigned_staffwise_location = array();
                        if( !empty( $bpsvalue['assigned_staffmembers'] ) ){

                            foreach( $bpsvalue['assigned_staffmembers'] as $assigned_staff ){
                                $get_service_location_id = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_location_id, bookingpress_staffmember_id, bookingpress_service_qty,bookingpress_service_min_qty FROM `$tbl_bookingpress_locations_service_staff_pricing_details` WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d" , $bookingpress_service_id, $assigned_staff ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                                if( !empty( $get_service_location_id ) ){
                                    foreach( $get_service_location_id as $location_data ){
                                        $location_id = $location_data->bookingpress_location_id;
                                        $assigned_locations[ $location_id ] = $location_data->bookingpress_service_qty;
                                        if( empty( $assigned_staffwise_location[ $location_id ] ) ){
                                            $assigned_staffwise_location[ $location_id ] = array(
                                                $location_data->bookingpress_staffmember_id
                                            );
                                        } else {
                                            $assigned_staffwise_location[ $location_id ][] = $location_data->bookingpress_staffmember_id;
                                        }
                                    }
                                }
                            }
                        } else {
                            $get_service_location_id = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_location_id, bookingpress_staffmember_id, bookingpress_service_qty, bookingpress_service_min_qty FROM `$tbl_bookingpress_locations_service_staff_pricing_details` WHERE bookingpress_service_id = %d" , $bookingpress_service_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                            
                            if( !empty( $get_service_location_id ) ){
                                foreach( $get_service_location_id as $location_data ){
                                    $location_id = $location_data->bookingpress_location_id;
                                    $assigned_locations[ $location_id ] = $location_data->bookingpress_service_qty;
                                    if( empty( $assigned_staffwise_location[ $location_id ] ) ){
                                        $assigned_staffwise_location[ $location_id ] = array(
                                            $location_data->bookingpress_staffmember_id
                                        );
                                    } else {
                                        $assigned_staffwise_location[ $location_id ][] = $location_data->bookingpress_staffmember_id;
                                    }
                                }
                            } else {
                                
                                $bpa_all_services[ $bpskey ]['is_visible_with_flag'] = false;
                            }
                            
                        }

                        $bpa_all_services[ $bpskey ]['locations'] = $assigned_locations;
                        $bpa_all_services[ $bpskey ]['locations_staffs'] = $assigned_staffwise_location;
                    }
                }
            }
            

            return $bpa_all_services;
        }

        function bookingpress_modify_payment_log_fields_before_insert_func($payment_log_data, $entry_data){
            if(!empty($payment_log_data)){
                $payment_log_data['bookingpress_location_id'] = $entry_data['bookingpress_location_id'];
                $payment_log_data['bookingpress_location_service_price'] = $entry_data['bookingpress_location_service_price'];
                $payment_log_data['bookingpress_location_service_capacity'] = $entry_data['bookingpress_location_service_capacity'];
                $payment_log_data['bookingpress_location_staff_price'] = $entry_data['bookingpress_location_staff_price'];
                $payment_log_data['bookingpress_location_staff_capacity'] = $entry_data['bookingpress_location_staff_capacity'];
            }
            return $payment_log_data;
        }

        function bookingpress_modify_appointment_booking_fields_before_insert_func($appointment_booking_fields, $entry_data ){
            if(!empty($entry_data['bookingpress_location_id'])){
                $appointment_booking_fields['bookingpress_location_id'] = $entry_data['bookingpress_location_id'];
                $appointment_booking_fields['bookingpress_location_service_price'] = $entry_data['bookingpress_location_service_price'];
                $appointment_booking_fields['bookingpress_location_service_capacity'] = $entry_data['bookingpress_location_service_capacity'];
                $appointment_booking_fields['bookingpress_location_staff_price'] = $entry_data['bookingpress_location_staff_price'];
                $appointment_booking_fields['bookingpress_location_staff_capacity'] = $entry_data['bookingpress_location_staff_capacity'];
            }
            return $appointment_booking_fields;
        }

        function bookingpress_modify_entry_data_before_insert_func($bookingpress_entry_details, $posted_data){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
            if(!empty($posted_data)){
                $bookingpress_service_id = !empty($posted_data['selected_service']) ? intval($posted_data['selected_service']) : 0;
                $bookingpress_staffmember_id = !empty($posted_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($posted_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;
                $bookingpress_location_id = !empty($posted_data['selected_location']) ? intval($posted_data['selected_location']) : 0;

                $bookingpress_location_details = array();
                if( !empty($bookingpress_service_id) && !empty($bookingpress_staffmember_id) && !empty($bookingpress_location_id) ){
                    $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_staffmember_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                }else{
                    $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                }

                $bookingpress_service_price = !empty($bookingpress_location_details['bookingpress_service_price']) ? floatval($bookingpress_location_details['bookingpress_service_price']) : 0;
                $bookingpress_service_capacity = !empty($bookingpress_location_details['bookingpress_service_qty']) ? intval($bookingpress_location_details['bookingpress_service_qty']) : 0;
                $bookingpress_service_min_capacity = !empty($bookingpress_location_details['bookingpress_service_min_qty']) ? intval($bookingpress_location_details['bookingpress_service_min_qty']) : 0;
                $bookingpress_staffmember_price = !empty($bookingpress_location_details['bookingpress_staffmember_price']) ? floatval($bookingpress_location_details['bookingpress_staffmember_price']) : 0;
                $bookingpress_staffmember_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_qty']) : 0;
                $bookingpress_staffmember_min_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_min_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_min_qty']) : 0;

                $bookingpress_entry_details['bookingpress_location_id'] = $bookingpress_location_id;
                $bookingpress_entry_details['bookingpress_location_service_price'] = $bookingpress_service_price;
                $bookingpress_entry_details['bookingpress_location_service_capacity'] = $bookingpress_service_capacity;
                $bookingpress_entry_details['bookingpress_location_staff_price'] = $bookingpress_staffmember_price;
		        $bookingpress_entry_details['bookingpress_location_staff_capacity'] = $bookingpress_staffmember_capacity;
                //$bookingpress_entry_details['bookingpress_location_staff_min_capacity'] = $bookingpress_staffmember_min_capacity;
            }
            return $bookingpress_entry_details;
        }

        function bookingpress_modify_recalculate_amount_before_calculation_func($final_payable_amount, $bookingpress_appointment_details){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
            if( !empty($final_payable_amount) && !empty($bookingpress_appointment_details['selected_location']) ){
                $bookingpress_service_id = !empty($bookingpress_appointment_details['selected_service']) ? intval($bookingpress_appointment_details['selected_service']) : 0;
                $bookingpress_staffmember_id = !empty($bookingpress_appointment_details['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_details['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;
                $bookingpress_location_id = !empty($bookingpress_appointment_details['selected_location']) ? intval($bookingpress_appointment_details['selected_location']) : 0;

                $bookingpress_location_details = array();
                if( !empty($bookingpress_service_id) && !empty($bookingpress_staffmember_id) && !empty($bookingpress_location_id) ){
                    $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_staffmember_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                    $final_payable_amount = !empty($bookingpress_location_details['bookingpress_staffmember_price']) ? floatval($bookingpress_location_details['bookingpress_staffmember_price']) : $final_payable_amount;
                }else{
                    $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                    $final_payable_amount = !empty($bookingpress_location_details['bookingpress_service_price']) ? floatval($bookingpress_location_details['bookingpress_service_price']) : $final_payable_amount;
                }
            }
            return $final_payable_amount;
        }

        function bookingpress_dynamic_add_params_for_timeslot_request_method_func($bookingpress_dynamic_add_params_for_timeslot_request){
            $bookingpress_dynamic_add_params_for_timeslot_request .= 'postData.bookingpress_location_id = vm.appointment_step_form_data.selected_location;';
            return $bookingpress_dynamic_add_params_for_timeslot_request;
        }

        function bookingpress_retrieve_location_staffmember_timings_func( $service_timings_data, $selected_service_id, $selected_date, $minimum_time_required, $service_max_capacity, $bookingpress_show_time_as_per_service_duration ){

            global $bookingpress_pro_staff_members;

            if( !empty( $service_timings_data['service_timings'] ) || true == $service_timings_data['is_daysoff'] || empty( $selected_service_id ) ){ // phpcs:ignore
				return $service_timings_data;
			}

            global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_services, $bookingpress_pro_staff_members, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_locations_staff_workhours, $tbl_bookingpress_locations_service_special_days, $tbl_bookingpress_locations_staff_special_days;

            $bookingpress_location_id = !empty($_POST['appointment_data_obj']['selected_location']) ? intval($_POST['appointment_data_obj']['selected_location']) : 0; // phpcs:ignore
            if(empty($bookingpress_location_id)){
                //If no location selected then return the default data
                return $service_timings_data;
            }

            $current_day  = ! empty( $selected_date ) ? ucfirst( date( 'l', strtotime( $selected_date ) ) ) : ucfirst( date( 'l', current_time( 'timestamp' ) ) );
			$current_date = ! empty($selected_date) ? date('Y-m-d', strtotime($selected_date)) : date('Y-m-d', current_time('timestamp'));
			
			$bookingpress_timezone = isset($_POST['client_timezone_offset']) ? sanitize_text_field( $_POST['client_timezone_offset'] ) : '';  // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
			
			$bookingpress_timeslot_display_in_client_timezone = $BookingPress->bookingpress_get_settings( 'show_bookingslots_in_client_timezone', 'general_setting' );
			$display_slots_in_client_timezone = false;
			
			// 04May 2023 Changes
			$client_timezone_string = !empty( $_COOKIE['bookingpress_client_timezone'] ) ? sanitize_text_field($_COOKIE['bookingpress_client_timezone']) : '';
            if( 'true' == $bookingpress_timeslot_display_in_client_timezone && !empty( $client_timezone_string ) ){
                $client_timezone_offset = $BookingPress->bookingpress_convert_timezone_to_offset( $client_timezone_string, $bookingpress_timezone );
                $wordpress_timezone_offset = $BookingPress->bookingpress_convert_timezone_to_offset( wp_timezone_string() );                
                if( $client_timezone_offset  == $wordpress_timezone_offset ){
                    $bookingpress_timeslot_display_in_client_timezone = 'false';
                }
            }
			// 04May 2023 Changes


			if( isset($bookingpress_timezone) && '' !== $bookingpress_timezone && !empty($bookingpress_timeslot_display_in_client_timezone) && ($bookingpress_timeslot_display_in_client_timezone == 'true')){	
				$display_slots_in_client_timezone = true;
			}

			$bookingpress_current_time = date( 'H:i',current_time('timestamp'));
			$bpa_current_datetime = date( 'Y-m-d H:i:s',current_time('timestamp'));

			$bpa_current_date = date('Y-m-d', current_time('timestamp'));

			if( strtotime( $bpa_current_date ) > strtotime( $selected_date ) && false == $display_slots_in_client_timezone ){
                return $service_timings_data;
            }

			$bookingpress_hide_already_booked_slot = $BookingPress->bookingpress_get_customize_settings( 'hide_already_booked_slot', 'booking_form' );
			$bookingpress_hide_already_booked_slot = ( $bookingpress_hide_already_booked_slot == 'true' ) ? 1 : 0;

			$change_store_date = ( !empty( $_POST['bpa_change_store_date'] ) && 'true' == $_POST['bpa_change_store_date'] ) ? true : false; // phpcs:ignore
			
			$bpa_current_time = date( 'H:i',current_time('timestamp'));

			$bookingpress_current_time_timestamp = current_time('timestamp');

            if (! empty($selected_service_id) ) {
				$service_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $selected_service_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_services is a table name
				if (! empty($service_data) ) {
					$service_time_duration      = esc_html($service_data['bookingpress_service_duration_val']);
					$service_time_duration_unit = esc_html($service_data['bookingpress_service_duration_unit']);
					if ($service_time_duration_unit == 'h' ) {
						$service_time_duration = $service_time_duration * 60;
					} elseif($service_time_duration_unit == 'd') {           
						$service_time_duration = $service_time_duration * 24 * 60;
					}
					$default_timeslot_step = $service_step_duration_val = $service_time_duration;
				}
			}

			$bpa_fetch_updated_slots = false;
            if( isset( $_POST['bpa_fetch_data'] ) && 'true' == $_POST['bpa_fetch_data'] ){ // phpcs:ignore
                $bpa_fetch_updated_slots = true;
            }
			$service_step_duration_val = apply_filters( 'bookingpress_modify_service_timeslot', $service_step_duration_val, $selected_service_id, $service_time_duration_unit, $bpa_fetch_updated_slots );

			$bookingpress_show_time_as_per_service_duration = $BookingPress->bookingpress_get_settings( 'show_time_as_per_service_duration', 'general_setting' );
            if ( ! empty( $bookingpress_show_time_as_per_service_duration ) && $bookingpress_show_time_as_per_service_duration == 'false' ) {
                $bookingpress_default_time_slot = $BookingPress->bookingpress_get_settings( 'default_time_slot', 'general_setting' );
                $default_timeslot_step      = $bookingpress_default_time_slot;
            } else {
				$default_timeslot_step		= $service_step_duration_val;
			}

			$workhour_data = array();

            $bookingpress_selected_staffmember_id = !empty( $_POST['appointment_data_obj']['selected_staff_member_id'] ) ? intval( $_POST['appointment_data_obj']['selected_staff_member_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.

            $bpa_staff_on_multiple_locations = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');

            /** Staff member location wise related calculations start */
            if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() && !empty( $bookingpress_selected_staffmember_id ) ){
                

                if( 'true' == $bpa_staff_on_multiple_locations  ){
                    /** Staff member location wise special day start */
                    $bookingpress_special_day_details = $this->bookingpress_get_location_staff_special_days( $bookingpress_location_id, $selected_service_id, $selected_date, $bookingpress_selected_staffmember_id );
    
                    if( !empty( $bookingpress_special_day_details ) ){
                        
                        $service_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_special_day_details['bookingpress_lcs_sp_start_time'])), $selected_service_id );
    
                        $service_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_special_day_details['bookingpress_lcs_sp_end_time'])), $selected_service_id );
    
                        
                        if( '00:00' == $service_end_time ){
                            $service_end_time = '24:00';
                        }
        
                        if ($service_start_time != null && $service_end_time != null ) {
                            
                            while ( $service_current_time <= $service_end_time ) {
                                if ($service_current_time > $service_end_time ) {
                                    
                                    break;
                                }
                                
                                
                                $service_tmp_date_time = $selected_date .' '.$service_current_time;
                                $service_tmp_end_time = date( 'Y-m-d', ( strtotime($service_tmp_date_time) + ( $service_step_duration_val * 60 ) ) );
                                if( $service_tmp_end_time > $selected_date  ){
                                    if( 1440 < $service_step_duration_val && $service_time_duration_unit != 'd' ){
                                        break;
                                    }
                                }
                                
                                $service_tmp_current_time = $service_current_time;
                                
                                if ($service_current_time == '00:00' ) {
                                    $service_current_time = date('H:i', strtotime($service_current_time) + ( $service_step_duration_val * 60 ));
                                } else {
                                    $service_tmp_time_obj = new DateTime($selected_date .' ' . $service_current_time);
                                    $service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
                                    $service_current_time = $service_tmp_time_obj->format('H:i');
        
                                    $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                    if( $service_current_date > $selected_date ){
                                        if( $service_end_time == '24:00' && strtotime($service_current_date.' '.$service_current_time) > strtotime( $service_current_date . ' 00:00' ) ){
                                            break;
                                        }
                                    }
                                }
        
                                $break_start_time      = '';
                                $break_end_time        = '';
                                /** service special days break hour logic start */
        
                                if( !empty( $bookingpress_special_day_details['special_day_breaks'] ) ){
                                    $service_special_day_breaks = $bookingpress_special_day_details['special_day_breaks'];
                                    $service_break_hour_data = array();
                                    foreach( $service_special_day_breaks as $ss_daybreak_data ){
                                        $temp_break_start_time = date('H:i', strtotime( $ss_daybreak_data['break_start_time'] ) );
                                        $temp_break_end_time = date('H:i', strtotime( $ss_daybreak_data['break_end_time'] ) );
                                        if( ( $temp_break_start_time >= $service_tmp_current_time && $temp_break_end_time <= $service_current_time ) || ( $temp_break_start_time < $service_current_time && $temp_break_end_time > $service_tmp_current_time ) ){
                                            $break_start_time = $temp_break_start_time;
                                            $break_end_time = $temp_break_end_time;
                                            $service_current_time = $break_start_time;
                                        }
                                    }
        
                                }
                                
                                /** service special days break hour logic end */
        
                                if ($service_current_time < $service_start_time || $service_current_time == $service_start_time ) {
                                    $service_current_time = $service_end_time;
                                }
                                
                                $is_booked_for_minimum = false;
                                if( 'disabled' != $minimum_time_required ){
                                    $bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
                                    $bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
                                    $bookingpress_time_diff = round( abs( current_time('timestamp') - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
                                    
                                    if( $bookingpress_time_diff <= $minimum_time_required ){
                                        $is_booked_for_minimum = true;
                                        //$booked_with_minimum_required++;
                                        //continue;
                                    }
                                }
        
                                $bookingpress_timediff_in_minutes = round(abs(strtotime($service_current_time) - strtotime($service_tmp_current_time)) / 60, 2);
                                $is_already_booked = 0;
                                if ($is_already_booked == 1 && $bookingpress_hide_already_booked_slot == 1 ) {
                                    continue;
                                } else {
                                    if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $service_current_time <= $service_end_time ) {
                                        if ($bpa_current_date == $selected_date ) {
                                            if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {
                                                $service_timing_arr = array(
                                                    'start_time' => $service_tmp_current_time,
                                                    'end_time'   => $service_current_time,
                                                    'break_start_time' => $break_start_time,
                                                    'break_end_time' => $break_end_time,
                                                    'store_start_time' => $service_tmp_current_time,
                                                    'store_end_time' => $service_current_time,
                                                    'store_service_date' => $selected_date,
                                                    'is_booked'  => 0,
                                                    'max_capacity' => $service_max_capacity,
                                                    'total_booked' => 0
                                                );
                                                if( $display_slots_in_client_timezone ){
        
                                                    $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                    $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                    
                                                    
                                                    $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                    $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                    
                                                    $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                    $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
        
                                                    $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
        
                                                    if( $change_store_date ) {
        
                                                        $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                        
                                                        $service_timing_arr['store_service_date'] = $store_selected_date;
                                                        
                                                        $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                        if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                            continue;
                                                        }
                                                    }
        
                                                    if( $selected_date < $booking_timeslot_start_date){
                                                        break;
                                                    }
                                                }
                                                $workhour_data[] = $service_timing_arr;
                                            }else {
                                                $service_timings_data['is_daysoff'] = true;
                                            }
                                        } else {
                                            if( !$is_booked_for_minimum ){
                                                $service_timing_arr = array(
                                                    'start_time' => $service_tmp_current_time,
                                                    'end_time'   => $service_current_time,
                                                    'break_start_time' => $break_start_time,
                                                    'break_end_time' => $break_end_time,
                                                    'store_start_time' => $service_tmp_current_time,
                                                    'store_end_time' => $service_current_time,
                                                    'store_service_date' => $selected_date,
                                                    'is_booked'  => 0,
                                                    'max_capacity' => $service_max_capacity,
                                                    'total_booked' => 0
                                                );
                                                if( $display_slots_in_client_timezone ){
        
                                                    $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                    $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                    
                                                    
                                                    $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                    $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                    
                                                    $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                    $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
        
                                                    $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
        
                                                    if( $change_store_date ) {
        
                                                        $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                        
                                                        $service_timing_arr['store_service_date'] = $store_selected_date;
                                                        
                                                        $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                        if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                            continue;
                                                        }
                                                    }
                                                    if( $selected_date < $booking_timeslot_start_date){
                                                        break;
                                                    }
                                                }
                                                $workhour_data[] = $service_timing_arr;
                                            }else {
                                                $service_timings_data['is_daysoff'] = true;
                                            }
                                        }
                                    } else {
                                        if($service_current_time >= $service_end_time){
                                            break;
                                        }
                                    }
                                }
        
                                if (! empty($break_end_time) ) {
                                    $service_current_time = $break_end_time;
                                }
                
                                if ($service_current_time == $service_end_time ) {
                                    break;
                                }
        
                                if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
                                    $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_tmp_current_time);
                                    $service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
                                    $service_current_time = $service_tmp_time_obj->format('H:i');
                                    
                                    $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                    if( $service_current_date > $selected_date ){
                                        break;
                                    }
                                }
                                
                            }
                            if( empty( $workhour_data ) ){
                                $service_timings_data['is_daysoff'] = true;
                            }
                            $service_timings_data['service_timings'] = $workhour_data;
                            return $service_timings_data;
                        }
                    }
                    /** Staff member location wise special day end */
    
    
                    /** Staff member location wise working hour start */
                    $bookingpress_location_staff_default_workhours = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations_staff_workhours} WHERE bookingpress_staffmember_id = %d AND bookingpress_location_id = %d AND bookingpress_location_staff_workhour_is_break = 0 AND bookingpress_location_staff_workday_key = %s", $bookingpress_selected_staffmember_id, $bookingpress_location_id, $current_day  ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                    
                    $bookingpress_location_staff_default_workhours = apply_filters( 'bookingpress_modify_location_staff_workhours', $bookingpress_location_staff_default_workhours, $bookingpress_selected_staffmember_id, $current_day );
    
                    if( !empty( $bookingpress_location_staff_default_workhours ) ){
                        if( empty( $bookingpress_location_staff_default_workhours['bookingpress_location_staff_workhour_start_time'] ) ){
                            $service_timings_data['is_daysoff'] = true;
                            return $service_timings_data;
                        }
    
                        $service_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_location_staff_default_workhours['bookingpress_location_staff_workhour_start_time'])), $selected_service_id );
                        $service_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_location_staff_default_workhours['bookingpress_location_staff_workhour_end_time'])), $selected_service_id );
    
                        if( '00:00' == $service_end_time ){
                            $service_end_time = '24:00';
                        }
    
                        if ($service_start_time != null && $service_end_time != null ) {
                            while ( $service_current_time <= $service_end_time ) {
                                if ($service_current_time > $service_end_time ) {
                                    break;
                                }
    
                                $service_tmp_date_time = $selected_date .' '.$service_current_time;
                                $service_tmp_end_time = date( 'Y-m-d', ( strtotime($selected_date. ' ' . $service_current_time ) + ( $service_step_duration_val * 60 ) ) );
    
                                if( $service_tmp_end_time > $selected_date  ){
                                    if( 1440 < $service_step_duration_val && $service_time_duration_unit != 'd' ){
                                        break;
                                    }
                                }
    
                                $service_tmp_current_time = $service_current_time;
    
                                if ($service_current_time == '00:00'  ) {
                                    $service_current_time = date('H:i', strtotime($service_current_time) + ( $service_step_duration_val * 60 ));
                                } else {
                                    $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_current_time);
                                    $service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
                                    $service_current_time = $service_tmp_time_obj->format('H:i');
                                    $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                    if( $service_current_date > $selected_date ){
                                        if( $service_end_time == '24:00' && strtotime($service_current_date.' '.$service_current_time) > strtotime( $service_current_date . ' 00:00' ) ){
                                            break;
                                        }
                                    }
                                }
    
                                $break_start_time = '';
                                $break_end_time = '';
                                /** Location wise Service working hour break calculation start  */
                                $check_break_existance = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_location_staff_workhour_start_time, bookingpress_location_staff_workhour_end_time FROM {$tbl_bookingpress_locations_staff_workhours} WHERE bookingpress_location_staff_workday_key = %s AND bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_workhour_is_break = %d AND bookingpress_location_staff_workhour_start_time BETWEEN %s AND %s", ucfirst( $current_day ), $bookingpress_location_id, $bookingpress_selected_staffmember_id, 1, $service_tmp_current_time, $service_current_time ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_workhours is table name defined globally.
    
                                if( !empty( $check_break_existance ) ){
                                    $break_start_time     = date('H:i', strtotime($check_break_existance->bookingpress_location_staff_workhour_start_time));
                                    $break_end_time       = date('H:i', strtotime($check_break_existance->bookingpress_location_staff_workhour_end_time));
                                    $service_current_time = $break_start_time;
                                }
                                /** Location wise Service working hour break calculation end  */
    
                                if ($service_current_time < $service_start_time || $service_current_time == $service_start_time ) {
                                    $service_current_time = $service_end_time;
                                }
    
                                $bookingpress_timediff_in_minutes = round(abs(strtotime($service_current_time) - strtotime($service_tmp_current_time)) / 60, 2);
    
                                $is_already_booked = 0;
                                $is_booked_for_minimum = false;
                                if( 'disabled' != $minimum_time_required ){
                                    $bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
                                    $bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
                                    $bookingpress_time_diff = round( abs( $bookingpress_current_time_timestamp - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
                                    if( $bookingpress_time_diff <= $minimum_time_required ){
                                        $is_booked_for_minimum = true;
                                    }
                                }
    
    
                                if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $service_current_time <= $service_end_time ) {
                                    if ($bpa_current_date == $current_date ) {
                                        if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {
                                            $service_timing_arr = array(
                                                'start_time' => $service_tmp_current_time,
                                                'end_time'   => $service_current_time,
                                                'break_start_time' => $break_start_time,
                                                'break_end_time' => $break_end_time,
                                                'store_start_time' => $service_tmp_current_time,
                                                'store_end_time' => $service_current_time,
                                                'store_service_date' => $selected_date,
                                                'is_booked'  => $is_already_booked,
                                                'max_capacity' => $service_max_capacity,
                                                'total_booked' => 0
                                            );
                                            if( $display_slots_in_client_timezone ){
    
                                                $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                
                                                
                                                $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                
                                                $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                                $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                                if( $change_store_date ) {
    
                                                    $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                    
                                                    $service_timing_arr['store_service_date'] = $store_selected_date;
                                                    
                                                    $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                    if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                        continue;
                                                    }
                                                }
                                                if( $selected_date < $booking_timeslot_start_date){
                                                    break;
                                                }
                                            }
                                            $workhour_data[] = $service_timing_arr;
                                        } else {
                                            $service_timings_data['is_daysoff'] = true;
                                        }
                                    } else {
                                        
                                        if(  !$is_booked_for_minimum ){
                                            $service_timing_arr = array(
                                                'start_time' => $service_tmp_current_time,
                                                'end_time'   => $service_current_time,
                                                'break_start_time' => $break_start_time,
                                                'break_end_time' => $break_end_time,
                                                'store_start_time' => $service_tmp_current_time,
                                                'store_end_time' => $service_current_time,
                                                'store_service_date' => $selected_date,
                                                'is_booked'  => $is_already_booked,
                                                'max_capacity' => $service_max_capacity,
                                                'total_booked' => 0
                                            );
                                            if( $display_slots_in_client_timezone ){
    
                                                $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                
                                                
                                                $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                
                                                $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                                $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                                if( $change_store_date ) {
    
                                                    $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                    
                                                    $service_timing_arr['store_service_date'] = $store_selected_date;
                                                    
                                                    $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                    if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                        continue;
                                                    }
                                                }
                                                if( $selected_date < $booking_timeslot_start_date){
                                                    break;
                                                }
                                            }
                                            $workhour_data[] = $service_timing_arr;
                                        } else {
                                            $service_timings_data['is_daysoff'] = true;
                                        }
                                    }
                                } else {
                                    if($service_current_time >= $service_end_time){
                                        break;
                                    }
                                }
    
                                if (! empty($break_end_time) ) {
                                    $service_current_time = $break_end_time;
                                }
    
                                if ($service_current_time == $service_end_time ) {
                                    break;
                                }
    
                                if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
    
                                    $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_tmp_current_time);
                                    $service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
                                    $service_current_time = $service_tmp_time_obj->format('H:i');
                                    
                                    $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                    if( $service_current_date > $selected_date ){
                                        break;
                                    }
                                }
                            }
    
                            if( empty( $workhour_data ) ){
                                $service_timings_data['is_daysoff'] = true;
                            }
                            $service_timings_data['service_timings'] = $workhour_data;
                            return $service_timings_data;
                        }
                    }
                    /** Staff member location wise working hour end */
                } else {
                    $service_timings_data_staff = $bookingpress_pro_staff_members->bookingpress_retrieve_staffmember_timings( $service_timings_data, $selected_service_id, $selected_date, $minimum_time_required, $service_max_capacity, $bookingpress_show_time_as_per_service_duration );

                    if( !empty( $service_timings_data_staff['service_timings'] ) || true == $service_timings_data_staff['is_daysoff'] || empty( $selected_service_id ) ){
                        remove_filter( 'bookingpress_retrieve_pro_modules_timeslots', array( $bookingpress_pro_staff_members, 'bookingpress_retrieve_staffmember_timings' ), 10, 6 );
                        return $service_timings_data_staff;
                    }
                }

            }
            /** Staff member location wise related calculations end */

            /** Service related calculation start */
            if( !empty( $selected_service_id ) ){
                /** Service location wise special day start */
                $bookingpress_special_day_details = $this->bookingpress_get_location_service_special_days($bookingpress_location_id, $selected_service_id, $selected_date);
                
                if( !empty( $bookingpress_special_day_details ) ){
                    
                    $service_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_special_day_details['bookingpress_lcs_sp_start_time'])), $selected_service_id );

                    $service_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_special_day_details['bookingpress_lcs_sp_end_time'])), $selected_service_id );

                    
                    if( '00:00' == $service_end_time ){
                        $service_end_time = '24:00';
                    }
    
                    if ($service_start_time != null && $service_end_time != null ) {
                        
                        while ( $service_current_time <= $service_end_time ) {
                            if ($service_current_time > $service_end_time ) {
                                
                                break;
                            }
                            
                            
                            $service_tmp_date_time = $selected_date .' '.$service_current_time;
                            $service_tmp_end_time = date( 'Y-m-d', ( strtotime($service_tmp_date_time) + ( $service_step_duration_val * 60 ) ) );
                            if( $service_tmp_end_time > $selected_date  ){
                                if( 1440 < $service_step_duration_val && $service_time_duration_unit != 'd' ){
                                    break;
                                }
                            }
                            
                            $service_tmp_current_time = $service_current_time;
                            
                            if ($service_current_time == '00:00' ) {
                                $service_current_time = date('H:i', strtotime($service_current_time) + ( $service_step_duration_val * 60 ));
                            } else {
                                $service_tmp_time_obj = new DateTime($selected_date .' ' . $service_current_time);
                                $service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
                                $service_current_time = $service_tmp_time_obj->format('H:i');
    
                                $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                if( $service_current_date > $selected_date ){
                                    if( $service_end_time == '24:00' && strtotime($service_current_date.' '.$service_current_time) > strtotime( $service_current_date . ' 00:00' ) ){
                                        break;
                                    }
                                }
                            }
    
                            $break_start_time      = '';
                            $break_end_time        = '';
                            /** service special days break hour logic start */
    
                            if( !empty( $bookingpress_special_day_details['special_day_breaks'] ) ){
                                $service_special_day_breaks = $bookingpress_special_day_details['special_day_breaks'];
                                $service_break_hour_data = array();
                                foreach( $service_special_day_breaks as $ss_daybreak_data ){
                                    $temp_break_start_time = date('H:i', strtotime( $ss_daybreak_data['break_start_time'] ) );
                                    $temp_break_end_time = date('H:i', strtotime( $ss_daybreak_data['break_end_time'] ) );
                                    if( ( $temp_break_start_time >= $service_tmp_current_time && $temp_break_end_time <= $service_current_time ) || ( $temp_break_start_time < $service_current_time && $temp_break_end_time > $service_tmp_current_time ) ){
                                        $break_start_time = $temp_break_start_time;
                                        $break_end_time = $temp_break_end_time;
                                        $service_current_time = $break_start_time;
                                    }
                                }
    
                            }
                            
                            /** service special days break hour logic end */
    
                            if ($service_current_time < $service_start_time || $service_current_time == $service_start_time ) {
                                $service_current_time = $service_end_time;
                            }
                            
                            $is_booked_for_minimum = false;
                            if( 'disabled' != $minimum_time_required ){
                                $bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
                                $bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
                                $bookingpress_time_diff = round( abs( current_time('timestamp') - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
                                
                                if( $bookingpress_time_diff <= $minimum_time_required ){
                                    $is_booked_for_minimum = true;
                                    //$booked_with_minimum_required++;
                                    //continue;
                                }
                            }
    
                            $bookingpress_timediff_in_minutes = round(abs(strtotime($service_current_time) - strtotime($service_tmp_current_time)) / 60, 2);
                            $is_already_booked = 0;
                            if ($is_already_booked == 1 && $bookingpress_hide_already_booked_slot == 1 ) {
                                continue;
                            } else {
                                if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $service_current_time <= $service_end_time ) {
                                    if ($bpa_current_date == $selected_date ) {
                                        if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {
                                            $service_timing_arr = array(
                                                'start_time' => $service_tmp_current_time,
                                                'end_time'   => $service_current_time,
                                                'break_start_time' => $break_start_time,
                                                'break_end_time' => $break_end_time,
                                                'store_start_time' => $service_tmp_current_time,
                                                'store_end_time' => $service_current_time,
                                                'store_service_date' => $selected_date,
                                                'is_booked'  => 0,
                                                'max_capacity' => $service_max_capacity,
                                                'total_booked' => 0
                                            );
                                            if( $display_slots_in_client_timezone ){
    
                                                $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                
                                                
                                                $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                
                                                $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                                $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                                if( $change_store_date ) {
    
                                                    $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                    
                                                    $service_timing_arr['store_service_date'] = $store_selected_date;
                                                    
                                                    $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                    if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                        continue;
                                                    }
                                                }
    
                                                if( $selected_date < $booking_timeslot_start_date){
                                                    break;
                                                }
                                            }
                                            $workhour_data[] = $service_timing_arr;
                                        }else {
                                            $service_timings_data['is_daysoff'] = true;
                                        }
                                    } else {
                                        if( !$is_booked_for_minimum ){
                                            $service_timing_arr = array(
                                                'start_time' => $service_tmp_current_time,
                                                'end_time'   => $service_current_time,
                                                'break_start_time' => $break_start_time,
                                                'break_end_time' => $break_end_time,
                                                'store_start_time' => $service_tmp_current_time,
                                                'store_end_time' => $service_current_time,
                                                'store_service_date' => $selected_date,
                                                'is_booked'  => 0,
                                                'max_capacity' => $service_max_capacity,
                                                'total_booked' => 0
                                            );
                                            if( $display_slots_in_client_timezone ){
    
                                                $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                                $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                                
                                                
                                                $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                                $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                                
                                                $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                                $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                                $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                                if( $change_store_date ) {
    
                                                    $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                    
                                                    $service_timing_arr['store_service_date'] = $store_selected_date;
                                                    
                                                    $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                    if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                        continue;
                                                    }
                                                }
                                                if( $selected_date < $booking_timeslot_start_date){
                                                    break;
                                                }
                                            }
                                            $workhour_data[] = $service_timing_arr;
                                        }else {
                                            $service_timings_data['is_daysoff'] = true;
                                        }
                                    }
                                } else {
                                    if($service_current_time >= $service_end_time){
                                        break;
                                    }
                                }
                            }
    
                            if (! empty($break_end_time) ) {
                                $service_current_time = $break_end_time;
                            }
            
                            if ($service_current_time == $service_end_time ) {
                                break;
                            }
    
                            if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
                                $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_tmp_current_time);
                                $service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
                                $service_current_time = $service_tmp_time_obj->format('H:i');
                                
                                $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                if( $service_current_date > $selected_date ){
                                    break;
                                }
                            }
                            
                        }
                        if( empty( $workhour_data ) ){
                            $service_timings_data['is_daysoff'] = true;
                        }
                        $service_timings_data['service_timings'] = $workhour_data;
                        return $service_timings_data;
                    }
                }
                /** Service location wise special day end */


                /** Service location wise work hour start */
                $bookingpress_location_service_default_workhours = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_workhours} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d AND bookingpress_location_service_workhour_is_break = 0 AND bookingpress_location_service_workday_key = %s", $selected_service_id, $bookingpress_location_id, $current_day), ARRAY_A);  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations_service_workhours is table name.

                $bookingpress_location_service_default_workhours = apply_filters( 'bookingpress_modify_location_service_workhours', $bookingpress_location_service_default_workhours, $selected_service_id, $current_day );

                if( !empty( $bookingpress_location_service_default_workhours ) ){

                    if( empty( $bookingpress_location_service_default_workhours['bookingpress_location_service_workhour_start_time'] ) ){
                        $service_timings_data['is_daysoff'] = true;
                        return $service_timings_data;
                    }

                    $service_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_location_service_default_workhours['bookingpress_location_service_workhour_start_time'])), $selected_service_id );
                    $service_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_location_service_default_workhours['bookingpress_location_service_workhour_end_time'])), $selected_service_id );

                    if( '00:00' == $service_end_time ){
                        $service_end_time = '24:00';
                    }

                    if ($service_start_time != null && $service_end_time != null ) {
                        while ( $service_current_time <= $service_end_time ) {
                            if ($service_current_time > $service_end_time ) {
                                break;
                            }

                            $service_tmp_date_time = $selected_date .' '.$service_current_time;
                            $service_tmp_end_time = date( 'Y-m-d', ( strtotime($selected_date. ' ' . $service_current_time ) + ( $service_step_duration_val * 60 ) ) );

                            if( $service_tmp_end_time > $selected_date  ){
                                if( 1440 < $service_step_duration_val && $service_time_duration_unit != 'd' ){
                                    break;
                                }
                            }
    
                            $service_tmp_current_time = $service_current_time;
    
                            if ($service_current_time == '00:00'  ) {
                                $service_current_time = date('H:i', strtotime($service_current_time) + ( $service_step_duration_val * 60 ));
                            } else {
                                $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_current_time);
                                $service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
                                $service_current_time = $service_tmp_time_obj->format('H:i');
                                $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                if( $service_current_date > $selected_date ){
                                    if( $service_end_time == '24:00' && strtotime($service_current_date.' '.$service_current_time) > strtotime( $service_current_date . ' 00:00' ) ){
                                        break;
                                    }
                                }
                            }

                            $break_start_time = '';
                            $break_end_time = '';
                            /** Location wise Service working hour break calculation start  */
                            $check_break_existance = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_location_service_workhour_start_time, bookingpress_location_service_workhour_end_time FROM {$tbl_bookingpress_locations_service_workhours} WHERE bookingpress_location_service_workday_key = %s AND bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_location_service_workhour_is_break = %d AND bookingpress_location_service_workhour_start_time BETWEEN %s AND %s", ucfirst( $current_day ), $bookingpress_location_id, $selected_service_id, 1, $service_tmp_current_time, $service_current_time ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_workhours is table name defined globally.

                            if( !empty( $check_break_existance ) ){
                                $break_start_time     = date('H:i', strtotime($check_break_existance->bookingpress_location_service_workhour_start_time));
                                $break_end_time       = date('H:i', strtotime($check_break_existance->bookingpress_location_service_workhour_end_time));
                                $service_current_time = $break_start_time;
                            }
                            /** Location wise Service working hour break calculation end  */

                            if ($service_current_time < $service_start_time || $service_current_time == $service_start_time ) {
                                $service_current_time = $service_end_time;
                            }

                            $bookingpress_timediff_in_minutes = round(abs(strtotime($service_current_time) - strtotime($service_tmp_current_time)) / 60, 2);

                            $is_already_booked = 0;
                            $is_booked_for_minimum = false;
                            if( 'disabled' != $minimum_time_required ){
                                $bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
                                $bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
                                $bookingpress_time_diff = round( abs( $bookingpress_current_time_timestamp - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
                                if( $bookingpress_time_diff <= $minimum_time_required ){
                                    $is_booked_for_minimum = true;
                                }
                            }


                            if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $service_current_time <= $service_end_time ) {
                                if ($bpa_current_date == $current_date ) {
                                    if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {
                                        $service_timing_arr = array(
                                            'start_time' => $service_tmp_current_time,
                                            'end_time'   => $service_current_time,
                                            'break_start_time' => $break_start_time,
                                            'break_end_time' => $break_end_time,
                                            'store_start_time' => $service_tmp_current_time,
                                            'store_end_time' => $service_current_time,
                                            'store_service_date' => $selected_date,
                                            'is_booked'  => $is_already_booked,
                                            'max_capacity' => $service_max_capacity,
                                            'total_booked' => 0
                                        );
                                        if( $display_slots_in_client_timezone ){
    
                                            $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                            $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                            
                                            
                                            $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                            $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                            
                                            $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                            $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                            $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                            if( $change_store_date ) {
    
                                                $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                
                                                $service_timing_arr['store_service_date'] = $store_selected_date;
                                                
                                                $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                    continue;
                                                }
                                            }
                                            if( $selected_date < $booking_timeslot_start_date){
                                                break;
                                            }
                                        }
                                        $workhour_data[] = $service_timing_arr;
                                    } else {
                                        $service_timings_data['is_daysoff'] = true;
                                    }
                                } else {
                                    
                                    if(  !$is_booked_for_minimum ){
                                        $service_timing_arr = array(
                                            'start_time' => $service_tmp_current_time,
                                            'end_time'   => $service_current_time,
                                            'break_start_time' => $break_start_time,
                                            'break_end_time' => $break_end_time,
                                            'store_start_time' => $service_tmp_current_time,
                                            'store_end_time' => $service_current_time,
                                            'store_service_date' => $selected_date,
                                            'is_booked'  => $is_already_booked,
                                            'max_capacity' => $service_max_capacity,
                                            'total_booked' => 0
                                        );
                                        if( $display_slots_in_client_timezone ){
    
                                            $booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
                                            $booking_timeslot_end = $selected_date .' '.$service_current_time.':00';
                                            
                                            
                                            $booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
                                            $booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
                                            
                                            $service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
                                            $service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
    
                                            $booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );
    
                                            if( $change_store_date ) {
    
                                                $store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
                                                
                                                $service_timing_arr['store_service_date'] = $store_selected_date;
                                                
                                                $store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
                                                if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) || $store_selected_date != $selected_date ){
                                                    continue;
                                                }
                                            }
                                            if( $selected_date < $booking_timeslot_start_date){
                                                break;
                                            }
                                        }
                                        $workhour_data[] = $service_timing_arr;
                                    } else {
                                        $service_timings_data['is_daysoff'] = true;
                                    }
                                }
                            } else {
                                if($service_current_time >= $service_end_time){
                                    break;
                                }
                            }
    
                            if (! empty($break_end_time) ) {
                                $service_current_time = $break_end_time;
                            }
            
                            if ($service_current_time == $service_end_time ) {
                                break;
                            }
    
                            if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
    
                                $service_tmp_time_obj = new DateTime($selected_date . ' ' . $service_tmp_current_time);
                                $service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
                                $service_current_time = $service_tmp_time_obj->format('H:i');
                                
                                $service_current_date = $service_tmp_time_obj->format('Y-m-d');
                                if( $service_current_date > $selected_date ){
                                    break;
                                }
                            }
                        }

                        if( empty( $workhour_data ) ){
                            $service_timings_data['is_daysoff'] = true;
                        }
                        $service_timings_data['service_timings'] = $workhour_data;
                        return $service_timings_data;
                    }

                }

                /** Service location wise work hour end */
            }
            /** Service related calculation end */

            return $service_timings_data;
        }

        function bookingpress_modify_default_off_days_with_location( $default_off_days, $selected_service, $selected_service_duration, $selected_staffmember ){
            global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_locations_staff_workhours, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_locations_staff_special_days, $bookingpress_pro_staff_members;

            $appointment_data = !empty( $_POST['appointment_data_obj'] ) ? $_POST['appointment_data_obj'] : array(); //phpcs:ignore

            $location_id = !empty( $appointment_data['selected_location'] ) ? intval( $appointment_data['selected_location']) : 0;
            
            if( empty( $location_id ) ){
                return $default_off_days;
            }

            $staff_working_hours = $bookingpress_pro_staff_members->get_bookingpress_staffmembersmeta( $selected_staffmember, 'bookingpress_configure_specific_workhour' );
            
            if( !empty( $selected_staffmember ) && 'true' == $staff_working_hours ){
                
                $bpa_staff_on_multiple_locations = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
                
                if( 'true' == $bpa_staff_on_multiple_locations  ){
                    $default_off_days['skip_check'] = true;
                    
                    $staff_workdays = $wpdb->get_results( $wpdb->prepare( "SELECT LOWER( bookingpress_location_staff_workday_key ) AS bookingpress_location_staff_workday_key FROM {$tbl_bookingpress_locations_staff_workhours} WHERE bookingpress_staffmember_id = %d AND bookingpress_location_id = %d AND bookingpress_location_staff_workhour_is_break = %d AND ( bookingpress_location_staff_workhour_start_time IS NULL OR ( ABS( TIME_TO_SEC( TIMEDIFF( bookingpress_location_staff_workhour_start_time, ( CASE WHEN bookingpress_location_staff_workhour_end_time = '00:00:00' THEN '24:00:00' ELSE bookingpress_location_staff_workhour_end_time END ) ) ) DIV 60 ) < %d ) ) ", $selected_staffmember, $location_id, 0, $selected_service_duration ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staff_member_workhours is table name defined globally. False Positive alarm

                    if( empty( $staff_workdays ) ){
                        return $default_off_days;
                    }
    
                    $total_off_days = count( $staff_workdays );
                    $counter = 0;
                    while( 0 < $total_off_days ){
    
                        $default_off_days['off_days'][] = $staff_workdays[ $counter ]['bookingpress_location_staff_workday_key'];
    
                        $total_off_days--;
                        $counter++;
                    }
                }
                
            }
                
            return $default_off_days;
        }

        function bookingpress_get_location_staff_special_days( $location_id, $service_id, $selected_date, $staffmember_id ){
            global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_locations_staff_special_days;

            $bookingpress_special_days = array();

            $where_clause = $wpdb->prepare( " AND ( bookingpress_location_staff_special_day_service_id IS NULL OR bookingpress_location_staff_special_day_service_id = %d OR bookingpress_location_staff_special_day_service_id LIKE %s OR bookingpress_location_staff_special_day_service_id LIKE %s OR bookingpress_location_staff_special_day_service_id LIKE %s )", $service_id, "$service_id,%", "%,$service_id", "%,$service_id,%" ); ////phpcs:ignore
            
            $bpa_special_days = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_location_staff_special_day_start_time, bookingpress_location_staff_special_day_end_time FROM `{$tbl_bookingpress_locations_staff_special_days}` WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_special_day_has_break = %d AND bookingpress_location_staff_special_day_start_date <= %s AND bookingpress_location_staff_special_day_end_date >= %s {$where_clause}", $location_id, $staffmember_id, 0, $selected_date, $selected_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_special_days is table name defined globally.

            if( !empty( $bpa_special_days ) ){
                $bookingpress_special_days[ 'bookingpress_lcs_sp_start_time'] = $bpa_special_days->bookingpress_location_staff_special_day_start_time;
                $bookingpress_special_days[ 'bookingpress_lcs_sp_end_time'] = $bpa_special_days->bookingpress_location_staff_special_day_end_time;
            }

            $bpa_special_day_break = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_location_staff_special_day_break_start_time, bookingpress_location_staff_special_day_break_end_time FROM `{$tbl_bookingpress_locations_staff_special_days}` WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_special_day_has_break = %d AND bookingpress_location_staff_special_day_start_date <= %s AND bookingpress_location_staff_special_day_end_date >= %s {$where_clause}", $location_id, $staffmember_id, 1, $selected_date, $selected_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_special_days is table name defined globally.

            if( !empty( $bpa_special_day_break ) ){
                $bpa_special_day_break_details = array();
                foreach( $bpa_special_day_break as $lc_sp_break_data ){
                    $bpa_special_day_break_details[] = array(
                        'break_start_time' => $lc_sp_break_data->bookingpress_location_staff_special_day_break_start_time,
                        'break_end_time' =>  $lc_sp_break_data->bookingpress_location_staff_special_day_break_end_time
                    );
                }
                $bookingpress_special_days['special_day_breaks'] = $bpa_special_day_break_details;
            }

            return $bookingpress_special_days;
        }

        function bookingpress_get_location_service_special_days( $location_id, $service_id, $selected_date ){
            global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_locations_service_special_days;

            $bookingpress_special_days = array();

            $bpa_special_days = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_location_service_special_day_start_time,bookingpress_location_service_special_day_end_time FROM `{$tbl_bookingpress_locations_service_special_days}` WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d AND bookingpress_location_service_special_day_has_break = %d AND bookingpress_location_service_special_day_start_date <= %s AND bookingpress_location_service_special_day_end_date >= %s", $service_id, $location_id, 0, $selected_date, $selected_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_special_days is table name defined globally.
            
            if( !empty( $bpa_special_days ) ){
                $bookingpress_special_days['bookingpress_lcs_sp_start_time'] = $bpa_special_days->bookingpress_location_service_special_day_start_time;
                $bookingpress_special_days['bookingpress_lcs_sp_end_time'] = $bpa_special_days->bookingpress_location_service_special_day_end_time;
            }

            $bpa_special_day_break = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_location_service_special_day_break_start_time,bookingpress_location_service_special_day_break_end_time FROM `{$tbl_bookingpress_locations_service_special_days}` WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d AND bookingpress_location_service_special_day_has_break = %d AND bookingpress_location_service_special_day_start_date <= %s AND bookingpress_location_service_special_day_end_date >= %s", $service_id, $location_id, 1, $selected_date, $selected_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_special_days is table name defined globally.

            if( !empty( $bpa_special_day_break ) ){
                $bpa_special_day_break_details = array();
                foreach( $bpa_special_day_break as $lc_sp_break_data ){
                    $bpa_special_day_break_details[] = array(
                        'break_start_time' => $lc_sp_break_data->bookingpress_location_service_special_day_break_start_time,
                        'break_end_time' =>  $lc_sp_break_data->bookingpress_location_service_special_day_break_end_time
                    );
                }
                $bookingpress_special_days['special_day_breaks'] = $bpa_special_day_break_details;
            }

			$selected_date = date('Y-m-d H:i:s', strtotime($selected_date));

            return $bookingpress_special_days;
        }

        function bookingpress_retrieve_location_staffmember_timings_func_old( $service_timings_data, $selected_service_id, $selected_date, $minimum_time_required, $service_max_capacity, $bookingpress_show_time_as_per_service_duration ){
            
            if( !empty( $service_timings_data['service_timings'] ) || true == $service_timings_data['is_daysoff'] || empty( $selected_service_id ) ){
				return $service_timings_data;
			}

			global $wpdb, $BookingPress, $BookingPressPro, $bookingpress_pro_staff_members, $tbl_bookingpress_staff_member_workhours, $tbl_bookingpress_staffmembers_meta, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_staffmembers_daysoff, $tbl_bookingpress_services;

            $bookingpress_location_id = !empty($_POST['appointment_data_obj']['selected_location']) ? intval($_POST['appointment_data_obj']['selected_location']) : 0; // phpcs:ignore
            if(empty($bookingpress_location_id)){
                //If no location selected then return the default data
                return $service_timings_data;
            }

            $bpa_current_date = date('Y-m-d', current_time('timestamp'));

			$bookingpress_selected_staffmember_id = !empty( $_POST['staffmember_id'] ) ? intval( $_POST['staffmember_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
			
			if( empty( $bookingpress_selected_staffmember_id ) ){
				$bookingpress_selected_staffmember_id = !empty( $_POST['bookingpress_selected_staffmember']['selected_staff_member_id'] ) ? intval( $_POST['bookingpress_selected_staffmember']['selected_staff_member_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
				
				if( empty( $bookingpress_selected_staffmember_id ) ){

					if( empty( $_POST['appointment_data_obj'] ) ){ // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
						$_POST['appointment_data_obj'] = !empty( $_POST['appointment_data'] ) ? array_map( array($BookingPress, 'appointment_sanatize_field'), $_POST['appointment_data'] ) : array();  // phpcs:ignore
					}
					$bookingpress_selected_staffmember_id = !empty( $_POST['appointment_data_obj']['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) ? intval( $_POST['appointment_data_obj']['bookingpress_selected_staff_member_details']['selected_staff_member_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
					if( empty( $bookingpress_selected_staffmember_id ) ){
						return $service_timings_data;
					}
				}
			}

			$display_slots_in_client_timezone = false;

			$bookingpress_timezone = isset($_POST['client_timezone_offset']) ? sanitize_text_field( $_POST['client_timezone_offset'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing --Reason Nonce already verified from the caller function.
			
			$bookingpress_timeslot_display_in_client_timezone = $BookingPress->bookingpress_get_settings( 'show_bookingslots_in_client_timezone', 'general_setting' );

			$store_current_date = date('Y-m-d', current_time('timestamp' ) );
			$store_current_time = date('H:i', current_time('timestamp' ) );
			
			if( isset($bookingpress_timezone) && '' !== $bookingpress_timezone && !empty($bookingpress_timeslot_display_in_client_timezone) && ($bookingpress_timeslot_display_in_client_timezone == 'true')){
				$display_slots_in_client_timezone = true;
			}
			
			$bookingpress_current_time = date( 'H:i',current_time('timestamp'));
			$bpa_current_datetime = date( 'Y-m-d H:i:s',current_time('timestamp'));

			$bookingpress_hide_already_booked_slot = $BookingPress->bookingpress_get_customize_settings( 'hide_already_booked_slot', 'booking_form' );
			$bookingpress_hide_already_booked_slot = ( $bookingpress_hide_already_booked_slot == 'true' ) ? 1 : 0;

			$current_day  = ! empty( $selected_date ) ? ucfirst( date( 'l', strtotime( $selected_date ) ) ) : ucfirst( date( 'l', current_time( 'timestamp' ) ) );
			$current_date = ! empty($selected_date) ? date('Y-m-d', strtotime($selected_date)) : date('Y-m-d', current_time('timestamp'));

			$bpa_current_time = date( 'H:i',current_time('timestamp'));

			$change_store_date = ( !empty( $_POST['bpa_change_store_date'] ) && 'true' == $_POST['bpa_change_store_date'] ) ? true : false; // phpcs:ignore

			$service_time_duration     = $BookingPress->bookingpress_get_default_timeslot_data();
			$service_step_duration_val = $service_time_duration['default_timeslot'];
			
			if (! empty($selected_service_id) ) {
				$service_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $selected_service_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_services is a table name
				if (! empty($service_data) ) {
					$service_time_duration      = esc_html($service_data['bookingpress_service_duration_val']);
					$service_time_duration_unit = esc_html($service_data['bookingpress_service_duration_unit']);
					if ($service_time_duration_unit == 'h' ) {
						$service_time_duration = $service_time_duration * 60;
					} elseif($service_time_duration_unit == 'd') {           
						$service_time_duration = $service_time_duration * 24 * 60;
					}
					$default_timeslot_step = $service_step_duration_val = $service_time_duration;
				}
			}
			
			$bpa_fetch_updated_slots = false;
            if( isset( $_POST['bpa_fetch_data'] ) && 'true' == $_POST['bpa_fetch_data'] ){ // phpcs:ignore
                $bpa_fetch_updated_slots = true;
            }
			$service_step_duration_val = apply_filters( 'bookingpress_modify_service_timeslot', $service_step_duration_val, $selected_service_id, $service_time_duration_unit, $bpa_fetch_updated_slots );

			$bookingpress_show_time_as_per_service_duration = $BookingPress->bookingpress_get_settings( 'show_time_as_per_service_duration', 'general_setting' );
            if ( ! empty( $bookingpress_show_time_as_per_service_duration ) && $bookingpress_show_time_as_per_service_duration == 'false' ) {
                $bookingpress_default_time_slot = $BookingPress->bookingpress_get_settings( 'default_time_slot', 'general_setting' );
                $default_timeslot_step      = $bookingpress_default_time_slot;
            } else {
				$default_timeslot_step      = $service_step_duration_val;
			}


			$workhour_data = array();

			/** Check for Staff Member Special Days */
			$bookingpress_staffmember__special_day_details = $BookingPressPro->bookingpress_get_staffmember_special_days(  $bookingpress_selected_staffmember_id, $selected_service_id, $selected_date );
			
			if( !empty( $bookingpress_staffmember__special_day_details ) ){
				
				$staffmember_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_staffmember__special_day_details['special_day_start_time'])), $selected_service_id );
				
				$staffmember_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_staffmember__special_day_details['special_day_end_time'])), $selected_service_id );

				if ($service_start_time != null && $staffmember_end_time != null ) {
					while ( $staffmember_current_time <= $staffmember_end_time ) {
						if ($staffmember_current_time > $staffmember_end_time ) {
							break;
						}

						$service_tmp_date_time = $selected_date .' '.$staffmember_current_time;
						$service_tmp_end_time = date( 'Y-m-d', ( strtotime($selected_date. ' ' . $staffmember_current_time ) + ( $service_step_duration_val * 60 ) ) );

						if( $service_tmp_end_time > $selected_date  ){
							break;
						}

						$service_tmp_current_time = $staffmember_current_time;

						if ($staffmember_current_time == '00:00' ) {
							$staffmember_current_time = date('H:i', strtotime($staffmember_current_time) + ( $service_step_duration_val * 60 ));
						} else {
							$service_tmp_time_obj = new DateTime($staffmember_current_time);
							$service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
							$staffmember_current_time = $service_tmp_time_obj->format('H:i');
						}

						$break_start_time      = '';
						$break_end_time        = '';
						
						/** Staffmember special days break start */
						global $tbl_bookingpress_staffmembers_special_day_breaks, $tbl_bookingpress_staffmembers_special_day;

						$check_break_existance = $wpdb->get_row( $wpdb->prepare("SELECT bssdb.bookingpress_special_day_break_start_time,bssdb.bookingpress_special_day_break_end_time,bssdw.bookingpress_special_day_service_id FROM `{$tbl_bookingpress_staffmembers_special_day_breaks}` bssdb LEFT JOIN `{$tbl_bookingpress_staffmembers_special_day}` bssdw ON bssdb.bookingpress_special_day_id = bssdw.bookingpress_staffmember_special_day_id WHERE bssdw.bookingpress_staffmember_id = %d AND bssdb.bookingpress_special_day_break_start_time BETWEEN %s AND %s", $bookingpress_selected_staffmember_id,$service_tmp_current_time,$staffmember_current_time) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers_special_day_breaks is a table name and $tbl_bookingpress_staffmembers_special_day is a table name. false alarm

						if( !empty( $check_break_existance ) ){
							$bookingpress_special_day_service_ids = !empty( $check_break_existance->bookingpress_special_day_service_id ) ? explode( ',' , $check_break_existance->bookingpress_special_day_service_id ) : array();
							
							if( empty( $bookingpress_special_day_service_ids ) || ( !empty( $bookingpress_special_day_service_ids ) && in_array( $selected_service_id, $bookingpress_special_day_service_ids ) ) ){
								$break_start_time = date('H:i', strtotime( $check_break_existance->bookingpress_special_day_break_start_time ) );
								$break_end_time = date('H:i', strtotime( $check_break_existance->bookingpress_special_day_break_end_time ) );
								$staffmember_current_time = $break_start_time;
							}
						}

						/** Staffmember special days break end */

						if ($staffmember_current_time < $service_start_time || $staffmember_current_time == $service_start_time ) {
							$staffmember_current_time = $staffmember_end_time;
						}

						$bookingpress_timediff_in_minutes = round(abs(strtotime($staffmember_current_time) - strtotime($service_tmp_current_time)) / 60, 2);
						$is_already_booked = 0;
						$is_booked_for_minimum = false;
						if( 'disabled' != $minimum_time_required ){
							$bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
							$bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
							$bookingpress_time_diff = round( abs( current_time('timestamp') - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
							
							if( $bookingpress_time_diff <= $minimum_time_required ){
								$is_booked_for_minimum = true;
							}
						}
						
						if ($is_already_booked == 1 && $bookingpress_hide_already_booked_slot == 1 ) {
							continue;
						} else {
							if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $staffmember_current_time <= $staffmember_end_time ) {
								if ( $bpa_current_date == $selected_date ) {
									if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {

										$service_timing_arr = array(
											'start_time' => $service_tmp_current_time,
											'end_time'   => $staffmember_current_time,
											'break_start_time' => $break_start_time,
											'break_end_time' => $break_end_time,
											'store_start_time' => $service_tmp_current_time,
											'store_end_time' => $staffmember_current_time,
											'store_service_date' => $selected_date,
											'is_booked'  => $is_already_booked,
											'max_capacity' => $service_max_capacity,
											'total_booked' => 0
										);
										if( $display_slots_in_client_timezone ){

											$booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
											$booking_timeslot_end = $selected_date .' '.$staffmember_current_time.':00';
											
											
											$booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
											$booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
											
											$service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
											$service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );

											$booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );

											if( $change_store_date ) {

												$store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
												
												$service_timing_arr['store_service_date'] = $store_selected_date;
												
												$store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
												if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) ){
													continue;
												}
											}
											if( $selected_date < $booking_timeslot_start_date){
												break;
											}
										}
										$workhour_data[] = $service_timing_arr;
									} else {
										$service_timings_data['is_daysoff'] = true;
									}
								} else {
									if( !$is_booked_for_minimum ){
										$service_timing_arr = array(
											'start_time' => $service_tmp_current_time,
											'end_time'   => $staffmember_current_time,
											'break_start_time' => $break_start_time,
											'break_end_time' => $break_end_time,
											'store_start_time' => $service_tmp_current_time,
											'store_end_time' => $staffmember_current_time,
											'store_service_date' => $selected_date,
											'is_booked'  => $is_already_booked,
											'max_capacity' => $service_max_capacity,
											'total_booked' => 0
										);
										if( $display_slots_in_client_timezone ){

											$booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
											$booking_timeslot_end = $selected_date .' '.$staffmember_current_time.':00';
											
											
											$booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
											$booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
											
											$service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
											$service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );

											$booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );

											if( $change_store_date ) {

												$store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
												
												$service_timing_arr['store_service_date'] = $store_selected_date;
												
												$store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
												if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) ){
													continue;
												}
											}
											if( $selected_date < $booking_timeslot_start_date){
												break;
											}
										}
										$workhour_data[] = $service_timing_arr;
									}else {
										$service_timings_data['is_daysoff'] = true;
									}
								}
							} else {
								if($staffmember_current_time >= $staffmember_end_time){
                                    break;
                                }
							}
						}

						if (! empty($break_end_time) ) {
							$staffmember_current_time = $break_end_time;
						}
		
						if ($staffmember_current_time == $staffmember_end_time ) {
							break;
						}
						
						if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
							$service_tmp_time_obj = new DateTime($service_tmp_current_time);
							$service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
							$staffmember_current_time = $service_tmp_time_obj->format('H:i');
						}
					}
					$service_timings_data['service_timings'] = $workhour_data;
					//die;
					return $service_timings_data;
				}
			}

			$is_staffmember_workhour_enable = $bookingpress_pro_staff_members->get_bookingpress_staffmembersmeta($bookingpress_selected_staffmember_id, 'bookingpress_configure_specific_workhour');

			if( "true" == $is_staffmember_workhour_enable ){
				$bookingpress_staffmember_workhours = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_staff_member_workhours} WHERE bookingpress_staffmember_id = %d AND bookingpress_staffmember_workhours_is_break = 0 AND bookingpress_staffmember_workday_key = %s AND bookingpress_location_id = %d", $bookingpress_selected_staffmember_id, ucfirst($current_day), $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is a table name. false alarm
				
				if( !empty( $bookingpress_staffmember_workhours ) ){
					$staffmember_current_time = $service_start_time = apply_filters( 'bookingpress_modify_service_start_time', date('H:i', strtotime($bookingpress_staffmember_workhours['bookingpress_staffmember_workhours_start_time'])), $selected_service_id );
					$staffmember_end_time     = apply_filters( 'bookingpress_modify_service_end_time', date('H:i', strtotime($bookingpress_staffmember_workhours['bookingpress_staffmember_workhours_end_time'])), $selected_service_id );
					
					if ($service_start_time != null && $staffmember_end_time != null ) {
						
						while ( $staffmember_current_time <= $staffmember_end_time ) {
							if ($staffmember_current_time > $staffmember_end_time ) {
								break;
							}

							$service_tmp_date_time = $selected_date .' '.$staffmember_current_time;
							$service_tmp_end_time = date( 'Y-m-d', ( strtotime($selected_date. ' ' . $staffmember_current_time ) + ( $service_step_duration_val * 60 ) ) );

							if( $service_tmp_end_time > $selected_date  ){
								break;
							}

							$service_tmp_current_time = $staffmember_current_time;
							
							if ($staffmember_current_time == '00:00' ) {
								$staffmember_current_time = date('H:i', strtotime($staffmember_current_time) + ( $service_step_duration_val * 60 ));
							} else {
								$service_tmp_time_obj = new DateTime($staffmember_current_time);
								$service_tmp_time_obj->add(new DateInterval('PT' . $service_step_duration_val . 'M'));
								$staffmember_current_time = $service_tmp_time_obj->format('H:i');
							}
	
							if ($staffmember_current_time < $service_start_time || $staffmember_current_time == $service_start_time ) {
								$staffmember_current_time = $staffmember_end_time;
							}

							$break_start_time = '';
							$break_end_time = '';
							/** Staff member work hour break time logic start */

							$staffmember_workhour_breaks_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_staffmember_workhours_start_time, bookingpress_staffmember_workhours_end_time FROM {$tbl_bookingpress_staff_member_workhours} WHERE bookingpress_staffmember_workday_key = %s AND bookingpress_staffmember_workhours_is_break = %d AND bookingpress_staffmember_id = %d AND bookingpress_staffmember_workhours_start_time BETWEEN %s AND %s", ucfirst($current_day), 1, $bookingpress_selected_staffmember_id, $service_tmp_current_time, $staffmember_current_time)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is table name.

							if( !empty( $staffmember_workhour_breaks_data ) ){
								$break_start_time = date('H:i', strtotime( $staffmember_workhour_breaks_data->bookingpress_staffmember_workhours_start_time ) );
								$break_end_time = date('H:i', strtotime( $staffmember_workhour_breaks_data->bookingpress_staffmember_workhours_end_time ) );
								$staffmember_current_time = $break_start_time;
							}

							/** Staff member work hour break time logic end */

							$bookingpress_timediff_in_minutes = round(abs(strtotime($staffmember_current_time) - strtotime($service_tmp_current_time)) / 60, 2);
							$is_booked_for_minimum = false;
							if( 'disabled' != $minimum_time_required ){
								$bookingpress_slot_start_datetime       = $selected_date . ' ' . $service_tmp_current_time . ':00';
								$bookingpress_slot_start_time_timestamp = strtotime( $bookingpress_slot_start_datetime );
								$bookingpress_time_diff = round( abs( current_time('timestamp') - $bookingpress_slot_start_time_timestamp ) / 60, 2 );
								
								if( $bookingpress_time_diff <= $minimum_time_required ){
									$is_booked_for_minimum = true;
								}
							}
							
							if ($break_start_time != $service_tmp_current_time && $bookingpress_timediff_in_minutes >= $service_step_duration_val && $staffmember_current_time <= $staffmember_end_time ) {
								if ($bpa_current_date == $selected_date ) {
									if ($service_tmp_current_time > $bpa_current_time && !$is_booked_for_minimum ) {

										$service_timing_arr = array(
											'start_time' => $service_tmp_current_time,
											'end_time'   => $staffmember_current_time,
											'break_start_time' => $break_start_time,
											'break_end_time' => $break_end_time,
											'store_start_time' => $service_tmp_current_time,
											'store_end_time' => $staffmember_current_time,
											'is_booked' => 0,
											'store_service_date' => $selected_date,
											'max_capacity' => $service_max_capacity,
											'total_booked' => 0
										);
										
										//$service_timing_arr = apply_filters( 'bookingpress_calculate_time_with_client_timezone', $service_timing_arr, $selected_date );

										/** timeslot in client timezone */
										if( $display_slots_in_client_timezone ){

											$booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
											$booking_timeslot_end = $selected_date .' '.$staffmember_current_time.':00';
											
											
											$booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
											$booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
											
											$service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
											$service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );

											$booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );

											if( $change_store_date ) {

												$store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
												
												$service_timing_arr['store_service_date'] = $store_selected_date;
												
												$store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
												if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) ){
													continue;
												}
											}
											if( $selected_date < $booking_timeslot_start_date){
												break;
											}
										}
										$workhour_data[] = $service_timing_arr;
									}else {
										$service_timings_data['is_daysoff'] = true;
									}
								} else {
									if( !$is_booked_for_minimum ){
										$service_timing_arr = array(
											'start_time' => $service_tmp_current_time,
											'end_time'   => $staffmember_current_time,
											'break_start_time' => $break_start_time,
											'break_end_time' => $break_end_time,
											'store_start_time' => $service_tmp_current_time,
											'store_end_time' => $staffmember_current_time,
											'store_service_date' => $selected_date,
											'is_booked' => 0,
											'max_capacity' => $service_max_capacity,
											'total_booked' => 0
										);
	
										if( $display_slots_in_client_timezone ){
	
											$booking_timeslot_start = $selected_date.' '.$service_tmp_current_time.':00';
											$booking_timeslot_end = $selected_date .' '.$staffmember_current_time.':00';
											
											
											$booking_timeslot_start = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_start, $bookingpress_timezone);	
											$booking_timeslot_end = apply_filters( 'bookingpress_appointment_change_to_client_timezone', $booking_timeslot_end, $bookingpress_timezone);
											
											$service_timing_arr['start_time'] = date('H:i', strtotime($booking_timeslot_start) );
											$service_timing_arr['end_time'] = date('H:i', strtotime( $booking_timeslot_end ) );
	
											$booking_timeslot_start_date = date('Y-m-d', strtotime( $booking_timeslot_start ) );

											if( $change_store_date ) {

												$store_selected_date = apply_filters( 'bookingpress_appointment_change_date_to_store_timezone', $selected_date, $service_timing_arr['start_time'], $bookingpress_timezone );
												
												$service_timing_arr['store_service_date'] = $store_selected_date;
												
												$store_selection_datetime = $store_selected_date . ' ' . $service_tmp_current_time;
												if( strtotime( $store_selection_datetime ) < current_time('timestamp' ) ){
													continue;
												}
											}
											if( $selected_date < $booking_timeslot_start_date){
												break;
											}
										}
										$workhour_data[] = $service_timing_arr;
									}
								}
							} else {
								if($staffmember_current_time >= $staffmember_end_time){
									break;
								}
							}

							if (! empty($break_end_time) ) {
								$staffmember_current_time = $break_end_time;
							}
			
							if ($staffmember_current_time == $staffmember_end_time ) {
								break;
							}

							if(!empty($default_timeslot_step) && $default_timeslot_step != $service_step_duration_val && empty($break_start_time)){
								$service_tmp_time_obj = new DateTime($service_tmp_current_time);
								$service_tmp_time_obj->add(new DateInterval('PT' . $default_timeslot_step . 'M'));
								$staffmember_current_time = $service_tmp_time_obj->format('H:i');
							}
						}

						$service_timings_data['service_timings'] = $workhour_data;

						return $service_timings_data;
					}
				}

			}
			
			return $service_timings_data;
        }

        function bookingpress_get_service_capacity_func($max_service_capacity, $selected_service_id){
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
            $bookingpress_posted_data = !empty($_POST['appointment_data_obj']) ? $_POST['appointment_data_obj'] : array(); // phpcs:ignore
            if(!empty($bookingpress_posted_data['selected_location'])){
                $bookingpress_location_id = !empty($bookingpress_posted_data['selected_location']) ? intval($bookingpress_posted_data['selected_location']) : 0;
                $bookingpress_staffmember_id = !empty($bookingpress_posted_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_posted_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;

                $bookingpress_where_condition = "1=1";
                $bookingpress_where_condition .= " AND bookingpress_location_id = ".$bookingpress_location_id." AND bookingpress_service_id = ".$selected_service_id;

                if(!empty($bookingpress_staffmember_id)){
                    $bookingpress_where_condition .= " AND bookingpress_location_id = ".$bookingpress_location_id;
                }

                $bookingpress_service_staff_location_wise_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE {$bookingpress_where_condition}"), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                if( !empty($bookingpress_staffmember_id) && !empty($bookingpress_service_staff_location_wise_details['bookingpress_staffmember_qty']) ){
                    $max_service_capacity = $bookingpress_service_staff_location_wise_details['bookingpress_staffmember_qty'];
                }else{
                    $max_service_capacity = $bookingpress_service_staff_location_wise_details['bookingpress_service_qty'];
                }
            }else if(!empty($_POST['action']) && ($_POST['action'] == "bookingpress_front_get_timings")){ // phpcs:ignore
                $bookingpress_posted_data = !empty($_POST) ? $_POST : array(); // phpcs:ignore
                $bookingpress_location_id = !empty($bookingpress_posted_data['bookingpress_location_id']) ? intval($bookingpress_posted_data['bookingpress_location_id']) : 0;
                $bookingpress_staffmember_id = !empty($bookingpress_posted_data['bookingpress_selected_staffmember']['selected_staff_member_id']) ? intval($bookingpress_posted_data['bookingpress_selected_staffmember']['selected_staff_member_id']) : 0;

                $bookingpress_where_condition = "1=1";
                $bookingpress_where_condition .= " AND bookingpress_location_id = ".$bookingpress_location_id." AND bookingpress_service_id = ".$selected_service_id;

                if(!empty($bookingpress_staffmember_id)){
                    $bookingpress_where_condition .= " AND bookingpress_location_id = ".$bookingpress_location_id;
                }

                $bookingpress_service_staff_location_wise_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE {$bookingpress_where_condition}"), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                if( !empty($bookingpress_staffmember_id) && !empty($bookingpress_service_staff_location_wise_details['bookingpress_staffmember_qty']) ){
                    $max_service_capacity = $bookingpress_service_staff_location_wise_details['bookingpress_staffmember_qty'];
                }else{
                    $max_service_capacity = $bookingpress_service_staff_location_wise_details['bookingpress_service_qty'];
                }
            }
            return $max_service_capacity;
        }

        function bookingpress_select_staffmember_func($bookingpress_after_selecting_staffmember){
            /* $bookingpress_after_selecting_staffmember .= '
                if(vm.bookingpress_current_tab == "location" && vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].previous_tab_name == "staffmembers"){
                    let selected_staffmember = vm.appointment_step_form_data.bookingpress_selected_staff_member_details.selected_staff_member_id;
                    var bookingpress_allowed_locations_details = vm.bookingpress_front_staff_wise_location_details[selected_staffmember];
                    vm.bookingpress_locations_list = [];
                    vm.bookingpress_all_locations_list.forEach(function(currentValue, index, arr){
                        var tmp_bookingpress_location_id = currentValue.bookingpress_location_id;
                        if(tmp_bookingpress_location_id.includes(bookingpress_allowed_locations_details)){
                            vm.bookingpress_locations_list.push(currentValue);
                        }
                    });
                }
            '; */

            $bookingpress_after_selecting_staffmember .= '
                let staff_id = selected_staffmember_id;
                
                let location = vm.appointment_step_form_data.selected_location || "";

                let selected_service_id = vm.appointment_step_form_data.selected_service;

                if("location" == vm.bookingpress_sidebar_step_data.staffmembers.next_tab_name ){
                    vm.appointment_step_form_data.selected_location = "";
                    for( let x in vm.bookingpress_locations_list ){
                        let loc_staff = vm.bookingpress_locations_list[ x ].bookingpress_staffmembers;
                        if( "" == selected_service_id ){
                            vm.bookingpress_locations_list[ x ].is_visible = false;
                            if( -1 < loc_staff.indexOf( staff_id ) ){
                                vm.bookingpress_locations_list[ x ].is_visible = true;
                            }
                        } else {
                            let loc_services = vm.bookingpress_locations_list[ x ].bookingpress_location_service_ids;
                            vm.bookingpress_locations_list[ x ].is_visible = false;
                            if( -1 < loc_staff.indexOf( staff_id ) && -1 < loc_services.indexOf( selected_service_id ) ){
                                vm.bookingpress_locations_list[ x ].is_visible = true;
                            }
                        }
                    }
                }
            ';

            return $bookingpress_after_selecting_staffmember;
        }

        function bookingpress_add_appointment_booking_vue_methods_func( $bookingpress_vue_methods_data ){

            $bookingpress_vue_methods_data .= '
                bookingpress_select_location( bookingpress_selected_location, bpa_use_flag = false ){
                    const vm = this;
                    /* vm.services_data = []; */
                    vm.appointment_step_form_data.selected_location = parseInt(bookingpress_selected_location);
                    let selected_location_name = vm.bookingpress_locations_list[ bookingpress_selected_location ].bookingpress_location_name;
                    vm.appointment_step_form_data.selected_location_name = selected_location_name;
                    
                    /** if service and staff is not selected ( location is before service and staff )  */
                    let selected_service = vm.appointment_step_form_data.selected_service;
                    if( "" != selected_service && 1 == vm.bookingpress_sidebar_step_data.location.is_first_step ){
                        if( 1 == vm.bookingpress_sidebar_step_data.service.is_display_step ){
                            vm.appointment_step_form_data.selected_service = "";
                            selected_service = "";
                        }
                    }

                    if( 1 == vm.bookingpress_sidebar_step_data.location.is_first_step && 1 == vm.is_staffmember_activated && "staffmembers" == vm.bookingpress_sidebar_step_data.location.next_tab_name ){
                        vm.appointment_step_form_data.selected_staffmember_id = "";
                        vm.appointment_step_form_data.bookingpress_selected_staff_member_details.selected_any_staffmember = "false";
                        vm.appointment_step_form_data.bookingpress_selected_staff_member_details.selected_staff_member_id = "";
                        vm.appointment_step_form_data.bookingpress_selected_staff_member_details.staff_member_id = "";

                        let selected_location_data = vm.bookingpress_locations_list[ bookingpress_selected_location ];
                        if( "undefined" != typeof selected_location_data.bookingpress_staffmembers ){
                            let loc_staff_details = selected_location_data.bookingpress_staffmembers;
                            let consider_service_staff = false;
                            if( "" != selected_service ){
                                consider_service_staff = true;
                            }
                            vm.bookingpress_staffmembers_details.forEach( function( element,index ){
                                let staff_id = element.bookingpress_staffmember_id;
                                vm.bookingpress_staffmembers_details[index].is_display_staff = true;
                                if( 0 > loc_staff_details.indexOf( staff_id ) ){
                                    vm.bookingpress_staffmembers_details[index].is_display_staff = false;
                                } else {
                                    if( true == consider_service_staff ){
                                        let selected_service_data = vm.bookingpress_all_services_data[ selected_service ].locations_staffs;
                                        let assigned_staffs_with_loc_service = selected_service_data[ bookingpress_selected_location ];
                                        if( 0 > assigned_staffs_with_loc_service.indexOf( staff_id ) ){
                                            vm.bookingpress_staffmembers_details[index].is_display_staff = false;
                                        } else{
                                            vm.bookingpress_staffmembers_details[index].is_display_staff = true;
                                        }
                                    } else {
                                        vm.bookingpress_staffmembers_details[index].is_display_staff = true;
                                    }
                                }
                            });
                        }
                    }
                    
                    if( "" == selected_service ){
                        vm.bookingpress_all_categories.forEach( (element,index) =>{
                            vm.bookingpress_all_categories[ index ].is_visible_with_flag = true;
                        });

                        /** Display Services based on the selected location */

                        let all_services = vm.bpasortedServices;
                        let first_service_category;
                        let first_service_index;
                        let first_uncategoried_service;
                        let lc = 0;
                        let luc = 0;
                        for( let x in all_services ){
                            let service_data = all_services[x];

                            if( "undefined" != service_data.is_location_visible ){
                                vm.bpasortedServices[x].is_location_visible = true;
                                vm.bpasortedServices[x].is_visible_with_flag = true;
                            }

                            if( "undefined" != typeof service_data.locations ){
                                
                                if( "undefined" == typeof service_data.locations[ bookingpress_selected_location ] ){
                                    vm.bpasortedServices[x].is_visible_with_flag = false;
                                    vm.bpasortedServices[x].is_location_visible = false;
                                } else {
                                    if( lc == 0 ){
                                        first_service_category = service_data.bookingpress_category_id;
                                        first_service_index = parseInt( x );
                                    }
                                    if( luc == 0 && service_data.bookingpress_category_id > 0 && first_service_category == 0 ){
                                        first_service_category = service_data.bookingpress_category_id;
                                        first_service_index = parseInt( x );
                                        luc++;
                                    }
                                    lc++;
                                }
                            }
                        }
                        
                        let service_details = all_services[first_service_index];
                        let category_id = service_details.bookingpress_category_id;
                        
                        vm.appointment_step_form_data.selected_category = category_id;
                        let total_services = vm.appointment_step_form_data.total_services;
                        vm.bpa_select_category( category_id, service_details.bookingpress_category_name );

                        /* Hide categories that has no services after selecting the location */
                        let all_categories = vm.bookingpress_all_categories;
                        let total_categories;
                        let hidden_categories;
                        all_categories.forEach( (element,index) =>{
                            if( element.category_id != 0 ){   
                                vm.bookingpress_all_categories[ index ].is_visible = true;
                                vm.bookingpress_all_categories[ index ].is_visible_with_flag = true;
                                let total_services = element.total_services;
                                let service_ids = element.service_ids;
                                let hidden_services = 0;
                                service_ids.forEach( (selm, sindex)=>{
                                    if( vm.bookingpress_all_services_data[selm].is_visible_with_flag == false && "undefined" != typeof vm.bookingpress_all_services_data[selm].is_location_visible && false == vm.bookingpress_all_services_data[selm].is_location_visible ) {
                                        hidden_services++;
                                    }
                                });
                               if( hidden_services == total_services ){
                                    vm.bookingpress_all_categories[ index ].is_visible = false;
                                    vm.bookingpress_all_categories[ index ].is_visible_with_flag = false;
                                }
                            }
                        });
                        /* Hide categories that has no services after selecting the location */
                        
                        vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data.location.next_tab_name, vm.bookingpress_sidebar_step_data.location.next_tab_name, vm.bookingpress_sidebar_step_data.location.previous_tab_name, 0);
                    } else {
                        
                    }
                },bpasortedlocationlist(bookingpress_location_list){
                    let bookingpress_all_services_data_loc = [];
                    for( let i in bookingpress_location_list ){
                        bookingpress_all_services_data_loc.push( bookingpress_location_list[i] );
                    }
                    return bookingpress_all_services_data_loc.sort( (a, b) =>{
                        return ( parseInt( a.bookingpress_location_position ) < parseInt( b.bookingpress_location_position ) ) ? -1 : 1;
                    });
                },';
         
            return $bookingpress_vue_methods_data;
        }

        function bookingpress_add_appointment_booking_vue_methods_func_old($bookingpress_vue_methods_data){
            $bookingpress_vue_methods_data .= '
                bookingpress_select_location(bookingpress_selected_location){
                    const vm = this;
                    vm.services_data = [];
                    vm.appointment_step_form_data.selected_location = parseInt(bookingpress_selected_location);

                    vm.bookingpress_locations_list.forEach(function(currentValue, index, arr){
                        if(currentValue.bookingpress_location_id == bookingpress_selected_location){
                            vm.appointment_step_form_data.selected_location_name = currentValue.bookingpress_location_name;
                        }
                    });

                    if( "location" == vm.bookingpress_current_tab && vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name == "service" ){
                        for( let x in vm.bpa_services_data_from_categories ){
                            let service_details = vm.bpa_services_data_from_categories[x];
                            for( let n in service_details ){
                                let current_service = service_details[n];
                                if( current_service.location_details.includes( bookingpress_selected_location ) ){
                                    let category_id = current_service.bookingpress_category_id;
                                    vm.appointment_step_form_data.selected_category = category_id;													
                                    let total_services = vm.appointment_step_form_data.total_services;
                                    vm.selectStepCategory( category_id, current_service.bookingpress_category_name, total_services );
                                    break;
                                }
                            }
                        }

                        vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].previous_tab_name, 0);
                    }

                    if(vm.bookingpress_current_tab == "location" && vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name == "staffmembers" && vm.is_staff_first_step == 0){
                        var bookingpress_selected_service_id = vm.appointment_step_form_data.selected_service;
                        if(bookingpress_selected_service_id != ""){
                            var bookingpress_staff_wise_location_lists = [];
                            var bookingpress_allowed_staffmembers = [];
                            
                            for( let x in vm.bpa_services_data_from_categories ){
                                let service_details = vm.bpa_services_data_from_categories[x];
                                for( let n in service_details ){
                                    let current_service = service_details[n];
                                    if( current_service.bookingpress_service_id == bookingpress_selected_service_id ){
                                        bookingpress_staff_wise_location_lists = current_service.staff_wise_locations;
                                        break;
                                    }
                                }
                            }

                            Object.keys(bookingpress_staff_wise_location_lists).forEach(function(key){
                                if(bookingpress_staff_wise_location_lists[key].includes(bookingpress_selected_location)){
                                    bookingpress_allowed_staffmembers.push(key);
                                }
                            });

                            vm.bookingpress_staffmembers_details.forEach(function(currentValue, index, arr){
                                if(!bookingpress_allowed_staffmembers.includes(currentValue.bookingpress_staffmember_id)){
                                    vm.bookingpress_staffmembers_details.splice(index, 1);
                                }
                            });

                            vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].previous_tab_name, 0);
                        }else if(bookingpress_selected_service_id == ""){
                            var bookingpress_allowed_staffmembers = [];
                            vm.bookingpress_staffmembers_details = [];
                            Object.keys(vm.bookingpress_front_staff_wise_location_details).forEach(function(key){
                                if(vm.bookingpress_front_staff_wise_location_details[key].includes(bookingpress_selected_location)){
                                    bookingpress_allowed_staffmembers.push(key);
                                }
                            });

                            vm.bpa_all_staff_details.forEach(function(currentValue, index, arr){
                                if(bookingpress_allowed_staffmembers.includes(currentValue.bookingpress_staffmember_id)){
                                    vm.bookingpress_staffmembers_details.push(currentValue);
                                }
                            });

                            vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].previous_tab_name, 0);
                        }
                    }


                    if(vm.bookingpress_current_tab == "location" && vm.bookingpress_sidebar_step_data[vm.bookingpress_current_tab].next_tab_name == "staffmembers" && vm.is_staff_first_step == 1){
                        var bookingpress_allowed_staffmembers = [];
                        vm.bookingpress_staffmembers_details = [];
                        Object.keys(vm.bookingpress_front_staff_wise_location_details).forEach(function(key){
                            if(vm.bookingpress_front_staff_wise_location_details[key].includes(bookingpress_selected_location)){
                                bookingpress_allowed_staffmembers.push(key);
                            }
                        });

                        vm.bpa_all_staff_details.forEach(function(currentValue, index, arr){
                            if(bookingpress_allowed_staffmembers.includes(currentValue.bookingpress_staffmember_id)){
                                vm.bookingpress_staffmembers_details.push(currentValue);
                            }
                        });

                        vm.bookingpress_step_navigation(vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].next_tab_name, vm.bookingpress_sidebar_step_data["location"].previous_tab_name, 0);
                    }

                },
            ';
            return $bookingpress_vue_methods_data;
        }

        function bookingpress_add_location_step_in_sidebar( $bookingpress_front_vue_data_fields ){
            global $BookingPress;

            
            if(!empty($bookingpress_front_vue_data_fields)){
                $bookingpress_sidebar_step_data = !empty($bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data']) ? $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data'] : array();
                $location_title = $BookingPress->bookingpress_get_customize_settings('location_title','booking_form');
                $location_title = !empty($location_title) ? stripslashes_deep($location_title) : '';
                $bookingpress_front_vue_data_fields['location_title'] = $location_title;

                $bookingpress_select_location_message = $BookingPress->bookingpress_get_settings('no_appointment_location_selected_for_the_booking', 'message_setting');

                $is_display_step = 1;
                $is_location_from_share_url = !empty( $_GET['loc_id'] ) ? 1 : 0;
                $location_share_url_id = !empty( $_GET['loc_id'] ) ? intval( $_GET['loc_id'] ) : 0;
                $is_allow_modify = !empty( $_GET['allow_modify'] ) ? $_GET['allow_modify'] : 0; // phpcs:ignore

                if( !empty( $is_location_from_share_url ) && !empty( $location_share_url_id ) && empty( $is_allow_modify ) ){
                    $is_display_step = 0;
                }

                $location_arr = array(
                    'tab_name' => $location_title,
                    'tab_value' => 'location',
                    'tab_icon' => '<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>' ,
                    'next_tab_name' => 'basic_details',
                    'previous_tab_name' => 'datetime',
                    'validate_fields' => array(
                        'selected_location'
                    ),
                    'validation_msg' => array(
                        'selected_location' => $bookingpress_select_location_message
                    ),
                    'is_allow_navigate' => 1,
                    'is_navigate_to_next' => '',
                    'auto_focus_tab_callback' => array(),
                    'is_display_step' => $is_display_step,
                    'sorting_key' => 'location_selection'
                );

                $bookingpress_sidebar_step_data['location'] = $location_arr;

                $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data'] = $bookingpress_sidebar_step_data;
            }

            $bookingpress_front_vue_data_fields['isLoadLocationLoader'] = 0;
            return $bookingpress_front_vue_data_fields;
        }

        function bookingpress_modify_front_booking_form_data_vars_func( $bookingpress_front_vue_data_fields ){

            global $wpdb, $BookingPress, $bookingpress_location_version, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_staffmembers, $bookingpress_pro_staff_members, $tbl_bookingpress_services, $bookingpress_appointment_bookings;
            wp_register_style( 'bookingpress-pro-location-front', BOOKINGPRESS_LOCATION_URL . '/css/bookingpress_location_front.css', array(), $bookingpress_location_version );
            wp_enqueue_style( 'bookingpress-pro-location-front' );

            wp_register_style( 'bookingpress-pro-location-front-rtl', BOOKINGPRESS_LOCATION_URL . '/css/bookingpress_location_front_rtl.css', array(), $bookingpress_location_version );

            if (is_rtl() ) {
                 wp_enqueue_style( 'bookingpress-pro-location-front-rtl' ); 
            }

            

            if(!empty($bookingpress_front_vue_data_fields)){

                $bookingpress_location_information = $BookingPress->bookingpress_get_customize_settings('bookingpress_location_information','booking_form');
                $bookingpress_location_title = $BookingPress->bookingpress_get_customize_settings('bpa_location_title_summay','booking_form');
                $bookingpress_location_cart_title = $BookingPress->bookingpress_get_customize_settings('cart_location_title','booking_form');

                $bookingpress_location_information = !empty( $bookingpress_location_information ) ? intval($bookingpress_location_information) : 2;
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['location_information'] = $bookingpress_location_information;
                $bookingpress_front_vue_data_fields['bpa_location_title'] = $bookingpress_location_title;
                $bookingpress_front_vue_data_fields['cart_location_title'] = $bookingpress_location_cart_title;

                $bookingpress_front_vue_data_fields['location_default_img_url'] = BOOKINGPRESS_LOCATION_URL.'/images/location-placeholder.jpg';

                $bookingpress_front_vue_data_fields['bookingpress_cart_reset_staff'] = false;

                $bpa_location_where_clause = "";
                
                
                if( !empty( $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'] ) && ( empty( $bookingpress_appointment_bookings->bookingpress_selected_service_param ) || ( !empty( $bookingpress_appointment_bookings->bookingpress_selected_service_param ) && false == $bookingpress_appointment_bookings->bookingpress_selected_service_param ) ) ){
                    $selected_service_id = $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'];
                    $bpa_location_where_clause = $wpdb->prepare( "AND lc.bookingpress_service_id = %d", $selected_service_id );
                }

                //Get all locations list
                $bookingpress_locations_list = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_position ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                if( empty( $bookingpress_locations_list ) ){
                    $bookingpress_front_vue_data_fields['bookingpress_display_no_service_placeholder'] = true;
                }
                                
                $bpa_all_services_data = (isset($bookingpress_front_vue_data_fields['bookingpress_all_services_data']))?$bookingpress_front_vue_data_fields['bookingpress_all_services_data']:array();

                $bpa_location_arr = array();
                $bpa_location_staff_capacity = array();
                $bpa_location_service_capacity = array();
                if( !empty( $bookingpress_locations_list ) ){
                    $total_locations = count( $bookingpress_locations_list );
                    $hidden_locations = 0;
                    foreach( $bookingpress_locations_list as $location_data ){
                        $location_id = intval( $location_data['bookingpress_location_id'] );

                        /** Fetch services of locations */
                        //$service_location_details = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_service_qty,GROUP_CONCAT(bookingpress_service_id) service_ids FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d " . $bpa_location_where_clause, $location_id), ARRAY_A);

                        $service_location_details =  $wpdb->get_results( $wpdb->prepare("SELECT lc.* FROM  {$tbl_bookingpress_locations_service_staff_pricing_details} lc RIGHT JOIN {$tbl_bookingpress_services} ls ON lc.bookingpress_service_id = ls.bookingpress_service_id WHERE lc.bookingpress_location_id = %d " . $bpa_location_where_clause, $location_id), ARRAY_A); //phpcs:ignore

                        
                        
                        if( empty( $service_location_details ) ){
                            $hidden_locations++;
                            continue;
                        } else {
                            $total_services = count( $service_location_details );
                            $total_disabled_services = 0;
                            foreach( $service_location_details as $tmp_sdata ){
                                $bpa_service_id = $tmp_sdata['bookingpress_service_id'];
                                if( !empty( $bpa_all_services_data[ $bpa_service_id ] ) && true == $bpa_all_services_data[ $bpa_service_id ]['is_disabled'] ){
                                    $total_disabled_services++;
                                }

                                $bpa_location_service_capacity[ $bpa_service_id ]['min_capacity'] = $tmp_sdata['bookingpress_service_min_qty'];
                                $bpa_location_service_capacity[ $bpa_service_id ]['max_capacity'] = $tmp_sdata['bookingpress_service_qty'];

                                if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                                    /** Staff Member wise data should be assigned here */
                                    $staff_id = $tmp_sdata['bookingpress_staffmember_id'];
                                    $bpa_location_staff_capacity[ $staff_id ][ $bpa_service_id ]['min_capacity'] = $tmp_sdata['bookingpress_staff_location_min_qty'];
                                    $bpa_location_staff_capacity[ $staff_id ][ $bpa_service_id ]['max_capacity'] = $tmp_sdata['bookingpress_staff_location_qty'];
                                }
                            }
                            if( $total_services == $total_disabled_services ){
                                $hidden_locations++;
                                continue;
                            }
                        }

                        $location_service_ids = array();
                        $location_service_qty = array();
                        $location_service_min_qty = array();

                        foreach( $service_location_details as $location_details ){
                            $location_service_ids[] = $location_details['bookingpress_service_id'];

                            $location_service_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_qty'];
                            $location_service_min_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_min_qty'];
                        }

                        $location_data['bookingpress_location_service_ids'] = $location_service_ids;
                        $location_data['bookingpress_location_service_qty'] = $location_service_qty;
                        $location_data['bookingpress_location_service_min_qty'] = $location_service_min_qty;
                        $location_data['bookingpress_location_staffwise_qty'] = $bpa_location_staff_capacity;
                        $location_data['bookingpress_location_servicewise_qty'] = $bpa_location_service_capacity;
                        $location_data['is_visible'] = true;
                        $location_data['is_visible_with_flag'] = true;
                                                
                        $location_data = apply_filters('bookingpress_modified_location_data_for_front_booking_form',$location_data);

                        $bpa_location_arr[ $location_id ] = $location_data;
                    }


                    if( $total_locations == $hidden_locations ){
                        $bookingpress_front_vue_data_fields['bookingpress_display_no_service_placeholder'] = true;
                    }
                }
                if( !empty( $bookingpress_front_vue_data_fields['is_staffmember_activated'] ) && 1 == $bookingpress_front_vue_data_fields['is_staffmember_activated'] ){
                    global $tbl_bookingpress_locations_service_staff_pricing_details;
                    $total_locations = count( $bpa_location_arr );
                    $hidden_locations = 0;
                    
                    foreach( $bpa_location_arr as $loc_key => $location_data ){
                        $loc_id = $location_data['bookingpress_location_id'];
                        $bpa_location_arr[ $loc_key ][ 'bookingpress_staffmembers'] = array();
                        $staff_details = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id > %d GROUP BY bookingpress_staffmember_id", $loc_id, 0 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                        
                        if( !empty( $staff_details ) ){
                            foreach( $staff_details as $sfkey => $sfdata ){
                                $bpa_location_arr[ $loc_key ][ 'bookingpress_staffmembers'][] = $sfdata->bookingpress_staffmember_id;
                            }
                            $bpa_location_arr[ $loc_key ]['is_visible'] = true;
                        } else {
                            $bpa_location_arr[ $loc_key ]['is_visible'] = false;
                            $hidden_locations++;
                        }

                        /* $assigned_staffs = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_service_staff_pricing_id) as total_assigned_staff FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id > %d", $loc_id, 0 ) );
                        if( 1 > $assigned_staffs ){
                            $bpa_location_arr[ $loc_key ]['is_visible'] = false;
                            $hidden_locations++;
                        } */
                    }

                    if( $total_locations == $hidden_locations ){
                        $bookingpress_front_vue_data_fields['bookingpress_display_no_service_placeholder'] = true;
                    }


                    /* $all_staffmember_details = $bookingpress_front_vue_data_fields['bookingpress_staffmembers_details'];
                    
                    foreach( $all_staffmember_details as $staff_key => $staff_details ){
                        $staffmember_id = $staff_details['bookingpress_staffmember_id'];
                        $get_staff_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_staffmember_id > %d", $staffmember_id) );
                    } */

                }


                $bookingpress_front_vue_data_fields['bookingpress_locations_list'] = $bpa_location_arr;
                $bookingpress_front_vue_data_fields['bookingpress_all_locations_list'] = $bpa_location_arr;

                //Set appointment step form data of location
                //--------------------------------------------------

                $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_location'] = '';
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_location_name'] = '';

                //--------------------------------------------------
                
                if(!empty($bookingpress_front_vue_data_fields['bpa_services_data_from_categories'])){
                    $service_data_from_categories = $bookingpress_front_vue_data_fields['bpa_services_data_from_categories'];
                    foreach( $service_data_from_categories as $category_id => $category_service_data ){
                        foreach( $category_service_data as $cs_key => $cs_value ){
                            $service_id = intval($cs_value['bookingpress_service_id']);

                            $bpservice_location_details = $wpdb->get_row($wpdb->prepare("SELECT GROUP_CONCAT(bookingpress_location_id) location_ids FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d", $service_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                            

                            if(!empty($bpservice_location_details['location_ids'])){
                                $bpservice_location_details = explode(',', $bpservice_location_details['location_ids']);
                            }else{
                                $bpservice_location_details = array();
                            }

                            $service_data_from_categories[$category_id][$cs_key]['location_details'] = $bpservice_location_details;

                            //if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                                /** Staff Member wise data should be assigned here */   
                            //}
                        }
                    }
                    
                    $bookingpress_front_vue_data_fields['bpa_services_data_from_categories'] = $service_data_from_categories;
                }

                if( !empty( $bookingpress_front_vue_data_fields['bookingpress_cart_addon'] ) && 1 == $bookingpress_front_vue_data_fields['bookingpress_cart_addon'] ){
                    $bookingpress_front_vue_data_fields['appointment_step_form_data']['bookingpress_selected_cart_location'] = '';
                }

                $bookingpress_is_hide_category_service_selection = $BookingPress->bookingpress_get_customize_settings('hide_category_service_selection', 'booking_form');
                $bpa_sidebar_steps = $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data'];
                if( 'true' == $bookingpress_is_hide_category_service_selection && 1 == $bpa_sidebar_steps['service']['is_display_step'] ){
                    $selected_service = $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'];
                    $all_services_data = $bookingpress_front_vue_data_fields['bookingpress_all_services_data'];
                    //$new_selected_service = '';
                    if( empty( $selected_service ) && !empty( $all_services_data ) ){
                        foreach( $all_services_data as $service_id => $service_data ) {
                            if( true == $service_data['is_visible'] && true == $service_data['is_visible_with_flag'] && !empty( $service_data['locations'] ) ){
                                $selected_service = $service_id;          
                                break;
                            }
                        }
                    }

                    if( !empty( $selected_service ) ){
                        $bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'] = $selected_service;
                        $bookingpress_front_vue_data_fields['bookingpress_sidebar_step_data']['service']['is_display_step'] = 0;
                    }
                }
            }

            return $bookingpress_front_vue_data_fields;
        }

        function bookingpress_add_front_side_sidebar_step_content_func($bookingpress_goback_btn_text, $bookingpress_next_btn_text, $bookingpress_third_tab_name){
            require BOOKINGPRESS_LOCATION_VIEWS_DIR.'/manage_location_booking_form.php';
        }
    }

    global $bookingpress_location_booking_form;
	$bookingpress_location_booking_form = new bookingpress_location_booking_form();
}