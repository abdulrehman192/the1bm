<?php
if (!class_exists('bookingpress_custom_service_duration') && class_exists('BookingPress_Core')) {

    class bookingpress_custom_service_duration Extends BookingPress_Core {

        function __construct() {
            global $wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_custom_staffmembers_service_durations;

            $tbl_bookingpress_custom_service_durations = $wpdb->prefix . 'bookingpress_custom_service_durations';   
            $tbl_bookingpress_custom_staffmembers_service_durations = $wpdb->prefix . 'bookingpress_custom_staffmembers_service_durations';   
            
            add_action( 'admin_notices', array( $this, 'bookingpress_custom_service_duration_admin_notices') );          

            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }            

            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {                

                add_action('admin_enqueue_scripts', array( $this, 'set_custom_service_duration_css' ), 11 );                

                add_filter('bookingpress_modify_capability_data', array($this, 'bookingpress_modify_capability_data_func'), 11, 1);

                add_action('bookingpress_add_service_extra_section',array($this,'bookingpress_add_service_extra_section_func'),11);

                add_action('bookingpress_add_custom_service_duration_field',array($this,'bookingpress_add_custom_service_duration_field_func'));

                add_action('bookingpress_add_service_dynamic_vue_methods', array( $this, 'bookingpress_add_service_dynamic_vue_methods_func' ), 11 );

                add_filter('bookingpress_modify_service_data_fields', array( $this, 'bookingpress_modify_service_data_fields_func' ), 10 );

                add_filter('bookingpress_after_add_update_service', array( $this, 'bookingpress_save_service_details' ), 10, 3 );

                add_action('bookingpress_edit_service_more_vue_data',array($this,'bookingpress_edit_service_more_vue_data_func'));

                add_action('bookingpress_after_reset_add_service_form', array( $this, 'bookingpress_after_reset_add_service_form_func' ), 10 );

                add_action('wp_ajax_bookingpress_get_custom_duration_options', array( $this, 'bookingpress_get_custom_duration_options_func' ), 10,2 );

                add_action('bookingpress_before_save_service_validation',array($this,'bookingpress_before_save_service_validation_func'));

                add_filter('bookingpress_modify_edit_service_data',array($this,'bookingpress_modify_edit_service_data_func'),11,2);

                add_action('bookingpress_add_staff_custom_service_duration_field',array($this,'bookingpress_add_staff_custom_service_duration_field_func'));

                add_filter('bookingpress_staff_member_vue_dynamic_data_fields', array( $this, 'bookingpress_staff_member_vue_dynamic_data_fields_func' ));

                add_action('bookingpress_assign_custom_services',array($this,'bookingpress_assign_custom_services_func'));

                add_filter('bookingpress_staff_members_save_external_details',array($this,'bookingpress_staff_members_save_external_details_func')); 

                add_filter('bookignpress_get_assigned_service_data_filter',array($this,'bookignpress_get_assigned_service_data_filter_func'));

                add_filter('bookingpress_modify_staffmember_service_data',array($this,'bookingpress_modify_staffmember_service_data_func'),11,2);

                add_action('bookingpress_before_save_assign_staffmember_data',array($this,'bookingpress_before_save_assign_staffmember_data'),11);

                add_action('bookingpress_after_open_assign_staffmember_model',array($this,'bookingpress_after_open_assign_staffmember_model_func'));

                add_action('bookingpress_after_open_add_service_model',array($this,'bookingpress_after_open_add_service_model_func'));

                add_action('bookingpress_after_delete_service', array( $this, 'bookingpress_after_delete_service_func' ), 11);
                
                add_action('bookingpress_add_custom_duration_section_front_side',array( $this,'bookingpress_add_custom_duration_section_front_side_func' ), 11);

                add_action('bookingpress_add_front_side_sidebar_step_content',array($this,'bookingpress_add_front_side_sidebar_step_content_func'),11,3);

                add_action('bookingpress_add_frontend_css',array($this,'bookingpress_add_front_side_sidebar_step_content_func'),11,3);

                add_filter('bookingpress_frontend_apointment_form_add_dynamic_data', array( $this, 'bookingpress_frontend_add_appointment_data_variables_func' ), 11,1);

                add_filter( 'bookingpress_dynamic_validation_for_step_change', array( $this, 'bookingpress_dynamic_validation_for_step_change_for_custom_duration'));

                add_filter('bookingpress_add_pro_booking_form_methods', array( $this, 'bookingpress_add_pro_booking_form_methods_func' ), 10, 1 );

                add_filter('bookingpress_before_selecting_booking_service', array( $this, 'bookingpress_before_selecting_booking_service_func'),11 );

                add_filter('bookingpress_modify_disable_date_data',array($this,'bookingpress_modify_disable_date_data_func'),11);

                add_filter('bookingpress_disable_date_vue_data_modify',array($this,'bookingpress_disable_date_vue_data_modify_func'),11);

                add_filter('bookingpress_after_selecting_booking_service',array($this,'bookingpress_after_selecting_booking_service_func'),11);

                add_filter('bookingpress_retrieve_pro_modules_timeslots', array( $this, 'bookingpress_restrict_pro_module_timeslots'), 9, 6 );

                add_filter('bookingpress_modify_recalculate_amount_before_calculation',array($this,'bookingpress_modify_recalculate_amount_before_calculation_func'),11,2);

                add_filter('bookingpress_dynamic_add_params_for_timeslot_request', array( $this, 'bookingpress_dynamic_add_params_for_timeslot_request_service_extra' ),11);

                add_filter('bookingpress_modify_service_timeslot', array( $this, 'bookingpress_modify_service_timeslot_with_custom_duration' ), 9, 4 );

                add_filter('bookingpress_modify_entry_data_before_insert',array($this,'bookingpress_modify_entry_data_before_insert_func'),11,2);

                add_filter('bookingpress_modify_appointment_booking_fields_before_insert',array($this,'bookingpress_modify_appointment_booking_fields_before_insert_func'),11,2);

                add_filter('bookingpress_modify_calculated_appointment_details',array($this,'bookingpress_modify_calculated_appointment_details_func'),11);

                add_filter('bookingpress_modify_appointment_data_fields',array($this,'bookingpress_modify_appointment_data_fields_func'),11);

                add_action('bookingpress_add_appointment_custom_service_duration_field_section',array($this,'bookingpress_add_appointment_custom_service_duration_field_section'));

                add_action('bookingpress_appointment_add_dynamic_vue_methods',array($this,'bookingpress_appointment_add_dynamic_vue_methods'),11);

                add_action('bookingpress_add_dynamic_vue_methods_for_calendar',array($this,'bookingpress_appointment_add_dynamic_vue_methods'),11);

                add_action('bookingpress_dashboard_modify_dynamic_vue_methods', array($this, 'bookingpress_appointment_add_dynamic_vue_methods'),11);

                add_action('bookingpress_before_change_backend_service',array($this,'bookingpress_before_change_backend_service_func'));               

                add_action('bookingpress_additional_disable_dates',array($this,'bookingpress_additional_disable_dates_func'));

                add_action('bookingpress_set_additional_appointment_xhr_data',array($this,'bookingpress_set_additional_appointment_xhr_data_func'));

                add_action('bookingpress_get_front_timing_set_additional_appointment_xhr_data',array($this,'bookingpress_get_front_timing_set_additional_appointment_xhr_data_func'));

                add_filter('bookingpress_modify_backend_add_appointment_entry_data',array($this,'bookingpress_modify_backend_add_appointment_entry_data_func'),11,2);

                add_filter('bookingpress_admin_side_filter_custom_duration_data',array($this,'bookingpress_admin_side_filter_custom_duration_data_func'),11,3);

                add_action('bookingress_backend_after_add_service_extra',array($this,'bookingress_backend_after_add_service_extra_func'));

                add_action('bookingress_backend_after_remove_service_extra',array($this,'bookingress_backend_after_remove_service_extra_func'));

                add_action('bookingpress_after_insert_entry_data_from_backend',array($this,'bookingpress_after_insert_entry_data_from_backend_func'),12,2);

                add_filter('bookingpress_modify_appointment_booking_fields',array($this,'bookingpress_modify_appointment_booking_fields_func'),12,3);

                add_action('bookingpress_edit_appointment_details',array($this,'bookingpress_edit_appointment_details_func'));

                add_filter('bookingpress_modify_edit_appointment_data',array($this,'bookingpress_modify_edit_appointment_data_func'),12);

                add_action('bookingpress_add_responsive_custom_duration_section_front_side',array($this,'bookingpress_add_responsive_custom_duration_section_front_side_func'));

                add_action('bookingpress_add_appointment_model_reset',array($this,'bookingpress_add_appointment_model_reset_func'),11);

                add_action( 'bookingpress_dashboard_add_appointment_model_reset', array($this, 'bookingpress_add_appointment_model_reset_func'),11);

                add_action('bookingpress_calendar_add_appointment_model_reset', array( $this, 'bookingpress_add_appointment_model_reset_func' ),11);

                add_filter( 'bookingpress_modify_calendar_data_fields', array( $this, 'bookingpress_modify_calendar_data_fields_func' ), 11 );

                add_action( 'bookingpress_calendar_add_appointment_model_reset', array( $this, 'bookingpress_calendar_add_appointment_model_reset_func' ));

                add_filter('bookingpress_front_modify_cart_data_filter',array($this,'bookingpress_front_modify_cart_data_filter_func'));

                add_filter('bookingpress_add_custom_service_duration_data',array($this,'bookingpress_add_custom_service_duration_data_func'));

                add_filter('bookingpress_reset_custom_duration_data',array($this,'bookingpress_reset_custom_duration_data_func'));

                add_filter('bookingpress_modify_cart_entry_data_before_insert', array($this, 'bookingpress_modify_cart_entry_data_before_insert_func'), 10,3);

                add_filter( 'bookingpress_modify_dashboard_data_fields', array( $this, 'bookingpress_modify_dashboard_data_fields_func' ),11 );

                add_action('bookingpress_add_customize_extra_section', array($this, 'bookingpress_add_customize_extra_section_func'),11);

                add_filter( 'bookingpress_customize_add_dynamic_data_fields', array( $this, 'bookingpress_modify_customize_data_fields_func'), 11);

                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this, 'bookingpress_get_booking_form_customize_data_filter_func'),11,1);

                add_filter('bookingpress_my_appointment_modify_data_for_rescheduling',array($this,'bookingpress_my_appointment_modify_data_for_rescheduling_func'),11,2);

                add_action('bookingpress_modify_rescheduled_appointment_xhr_data',array($this,'bookingpress_modify_rescheduled_appointment_xhr_data_func'),11);

                add_action('bookingpress_modify_assign_services_xhr_data',array($this,'bookingpress_modify_assign_services_xhr_data_func'),11);

                add_action('bookingpress_modify_rescheduled_front_timing_xhr',array($this,'bookingpress_modify_rescheduled_front_timing_xhr_func'),11);

                add_filter('bookingpress_front_appointment_add_dynamic_data',array($this,'bookingpress_front_appointment_add_dynamic_data_func'),11);

                add_action('bookingpress_duplicate_more_details', array($this, 'bookingpress_duplicate_more_details_func'), 11, 2);

                add_action( 'bookingpress_modify_all_retrieved_services',array( $this, 'bookingpress_enable_custom_duration_on_service'), 10, 4 );

                //--Add New Action for after change service unit
                add_action('bookingpress_after_change_service_unit',array($this,'bookingpress_after_change_service_unit_fun'),10);

                //Add For multiple Day Disable Date Get
                add_filter( 'bookingpress_get_multiple_days_disable_dates', array( $this, 'bookingpress_get_multiple_days_disable_dates_func' ), 10, 5);                

                //--Add New Action For Day Calendar Custom duration droupdown
                add_action('bookingpress_add_custom_duration_day_section_front_side',array( $this,'bookingpress_add_custom_duration_day_section_front_side_func' ), 11);

                //-- Add New Action For Day Custom Duration Count
                add_filter( 'bookingpress_modify_service_duration_value_before', array( $this, 'bookingpress_modify_service_duration_value_before_fun'), 10, 3);

                //-- Add new action for disable date time loader when day custom duration added
                add_filter('bookingpress_disable_date_send_data_before',array($this,'bookingpress_disable_date_send_data_before_fun'),10,1);

                //add_filter('bookingpress_after_selecting_booking_service', array($this, 'bookingpress_after_selecting_booking_service_func'), 11, 1);

                add_action('bookingpress_add_service_validation',array($this,'bookingpress_add_service_validation_func'));

                /*Multi language Treanslation */
                if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {
					add_filter('bookingpress_modified_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
                	add_filter('bookingpress_modified_customize_form_language_translate_fields',array($this,'bookingpress_modified_customize_form_language_translate_fields_func'),10);
					add_filter('bookingpress_modified_language_translate_fields_section',array($this,'bookingpress_modified_language_translate_fields_section_func'),10);
				}
                
                add_action( 'wp_ajax_bookingpress_get_disable_date', array( $this, 'bookingpress_get_disable_dates_with_custom_service_duration'), 4 );
                add_action( 'wp_ajax_nopriv_bookingpress_get_disable_date', array( $this, 'bookingpress_get_disable_dates_with_custom_service_duration'), 4 );

                add_filter('bookingpress_after_timeslot_data_load_for_single_date',array($this,'bookingpress_after_timeslot_data_load_for_single_date_func'),10,1);

			}   
            add_action( 'admin_init', array( $this, 'bookingpress_custom_duration_upgrade_data' ) ); 
	    add_action('activated_plugin',array($this,'bookingpress_is_custom_service_duration_addon_activated'),11,2);        
		}


        function bookingpress_after_timeslot_data_load_for_single_date_func($bookingpress_after_timeslot_data_load_for_single_date){
            $bookingpress_after_timeslot_data_load_for_single_date.='
                if(typeof postData.enable_custom_service_duration != "undefined"){
                    if(postData.enable_custom_service_duration == true || postData.enable_custom_service_duration == "true"){
                        set_waiting_time_limit = 0;
                    }
                }                  
            ';
            return $bookingpress_after_timeslot_data_load_for_single_date;
        }
        

        function bookingpress_modified_language_translate_fields_func($bookingpress_all_language_translation_fields){
			$bookingpress_custom_service_language_translation_fields = array(                
				'customized_form_custom_duration_labels' => array(
					'custom_service_duration_title' => array('field_type'=>'text','field_label'=>__('Service duration title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_service_description_title' => array('field_type'=>'text','field_label'=>__('Service duration description', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_please_select_title' => array('field_type'=>'text','field_label'=>__('Please select placeholder', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_duration_title' => array('field_type'=>'text','field_label'=>__('Custom duration title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_price_title' => array('field_type'=>'text','field_label'=>__('Price title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'no_custom_service_duration_selected_title' => array('field_type'=>'text','field_label'=>__('No custom service duration selected', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                )   
			);  
			
            $bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_custom_service_language_translation_fields);
            return $bookingpress_all_language_translation_fields;
		}

		function bookingpress_modified_customize_form_language_translate_fields_func($bookingpress_all_language_translation_fields){
			$bookingpress_custom_service_language_translation_fields = array(                
				'customized_form_custom_duration_labels' => array(
					'custom_service_duration_title' => array('field_type'=>'text','field_label'=>__('Service duration title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_service_description_title' => array('field_type'=>'text','field_label'=>__('Service duration description', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_please_select_title' => array('field_type'=>'text','field_label'=>__('Please select placeholder', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_duration_title' => array('field_type'=>'text','field_label'=>__('Custom duration title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'custom_price_title' => array('field_type'=>'text','field_label'=>__('Price title', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
                    'no_custom_service_duration_selected_title' => array('field_type'=>'text','field_label'=>__('No custom service duration selected', 'bookingpress-custom-service-duration'),'save_field_type'=>'booking_form'),
				)   
			);  
			$pos = 5;
			$bookingpress_all_language_translation_fields = array_slice($bookingpress_all_language_translation_fields, 0, $pos)+$bookingpress_custom_service_language_translation_fields + array_slice($bookingpress_all_language_translation_fields, $pos);
			return $bookingpress_all_language_translation_fields;
		}

        function bookingpress_modified_language_translate_fields_section_func($bookingpress_all_language_translation_fields_section){
			/* Function to add cart step heading */
            $bookingpress_custom_duration_step_section_added = array('customized_form_custom_duration_labels' => __('Custom Duration Labels', 'bookingpress-custom-service-duration') );
			$bookingpress_all_language_translation_fields_section = array_merge($bookingpress_all_language_translation_fields_section,$bookingpress_custom_duration_step_section_added);
			return $bookingpress_all_language_translation_fields_section;
		}

		/**
		 * Function for added validation for bookingpress pro version.
		 *
		 * @return void
		 */
		function bookingpress_add_service_validation_func() {
			global $wpdb;	
            $bpa_pro_plugin_version = (float)get_option('bookingpress_pro_version');            
            $enable_custom_service_duration = (boolean)(isset($_POST['enable_custom_service_duration']))?$_POST['enable_custom_service_duration']:false; // phpcs:ignore
            $service_duration_unit = (isset($_POST['service_duration_unit']))?sanitize_text_field($_POST['service_duration_unit']):''; // phpcs:ignore
			if($bpa_pro_plugin_version < 2.2 && $enable_custom_service_duration && $service_duration_unit == 'd'){				
                $response            = array();
                $response['variant'] = 'error';
                $response['title']   = esc_html__('Error', 'bookingpress-custom-service-duration');
                $response['msg']     = esc_html__('To enable custom duration in Days, you need to update BookingPress pro plugin to version 2.2 or higher.', 'bookingpress-custom-service-duration');
                wp_send_json($response);
                die();
            }
		}

        /**
         * Function for disable day calendar loader when custom duration add
         *
         * @return void
         */
        function bookingpress_disable_date_send_data_before_fun($bookingpress_disable_date_send_data){
            $bookingpress_disable_date_send_data= '
            if(typeof vm.is_display_custom_duration_loader !== "undefined" && typeof vm.is_display_custom_duration_day_loader !== "undefined"){
                if(vm.appointment_step_form_data.selected_service_duration_unit == "d" && (vm.is_display_custom_duration_loader == true || vm.is_display_custom_duration_day_loader == true)){    
                    vm.isLoadDateTimeCalendarLoad = "0";
                }
            }   

            ';
            return $bookingpress_disable_date_send_data;        
        }

        /**
         * Function for modified service duration for booking
         *
         * @param  mixed $service_duration_val
         * @param  mixed $service_id
         * @param  mixed $bookingpress_appointment_data
         * @return void
         */
        function bookingpress_modify_service_duration_value_before_fun( $service_duration_val, $service_id,$bookingpress_appointment_data ){

            global $BookingPress;
            $enable_custom_service_duration = (isset($bookingpress_appointment_data['enable_custom_service_duration']))?$bookingpress_appointment_data['enable_custom_service_duration']:'';
            $custom_service_duration_real_value = (isset($bookingpress_appointment_data['custom_service_duration_real_value']))?$bookingpress_appointment_data['custom_service_duration_real_value']:'';                
            $bookingpress_selected_service_duration = (isset($bookingpress_appointment_data['selected_service_duration']))?$bookingpress_appointment_data['selected_service_duration']:'';
            $bookingpress_selected_service_duration_unit = (isset($bookingpress_appointment_data['selected_service_duration_unit']))?$bookingpress_appointment_data['selected_service_duration_unit']:'';            
            if($enable_custom_service_duration && !empty($bookingpress_selected_service_duration) && $bookingpress_selected_service_duration_unit == 'd'  && !empty($custom_service_duration_real_value)){                
                $service_duration_val = $bookingpress_selected_service_duration;
            }
            return $service_duration_val;

        }
                
        /**
         * Function For added day calendar service custom duration
         *
         * @return void
         */
        function bookingpress_add_custom_duration_day_section_front_side_func(){            
        ?>    
        <div class="bpa-dt__custom-duration-is-day-service" :class="(is_display_custom_duration_loader == true) ? 'bpa-cd-day-service__loader-active' : '' " v-if="bookingpress_current_tab == 'datetime' && appointment_step_form_data.selected_service_duration_unit == 'd' ">
            <div class="bpa-front-loader-container" v-if="is_display_custom_duration_loader == true">
                <div class="bpa-front-loader">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" width="256" height="256" viewBox="0 0 256 256" style="width:100%;height:100%">
                        <defs>
                            <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_1_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z " to="M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keyTimes="0;0.5037594;0.5263158;0.5789474;0.6691729;0.6992481;0.7593985;0.7669173;1" values="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M303.49 386.7 C303.49,386.7 284.88,386.7 284.88,386.7 C284.88,386.7 284.88,402.72 284.88,402.72 C284.88,402.72 293.41,402.87 293.41,402.87 C293.41,402.87 293.07,405.24 293.07,405.24 C293.07,405.24 296.63,405.24 296.63,405.24 C296.63,405.24 296.82,402.57 296.82,402.57 C296.82,402.57 304.49,401.98 304.49,401.98 C304.49,401.98 303.49,386.7 303.49,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,398.12 265.56,398.12 C265.56,398.12 266.75,407.02 266.75,407.02 C266.75,407.02 294.78,405.83 294.78,405.83 C294.78,405.83 298.34,405.83 298.34,405.83 C298.34,405.83 332.75,406.72 332.75,406.72 C332.75,406.72 332.45,399.46 332.45,399.46 C332.45,399.46 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,442.32 265.56,442.32 C265.56,442.32 266.75,448.4 266.75,448.4 C266.75,448.4 283.8,447.51 283.8,447.51 C283.8,447.51 312.06,447.21 312.06,447.21 C312.06,447.21 332.75,448.1 332.75,448.1 C332.75,448.1 332.45,443.65 332.45,443.65 C332.45,443.65 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.86,453.14 265.86,453.14 C265.86,453.14 276.98,456.11 276.98,456.11 C276.98,456.11 277.28,447.51 277.28,447.51 C277.28,447.51 319.47,447.81 319.47,447.81 C319.47,447.81 318.81,456.11 318.81,456.11 C318.81,456.11 329.63,454.92 329.63,454.92 C329.63,454.92 330.97,386.7 330.97,386.7z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.63,448.83 295.63,448.83 C295.63,448.83 295.71,448.75 295.71,448.75 C295.71,448.75 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                            <clipPath id="_R_G_L_1_C_0">
                                <path id="_R_G_L_1_C_0_P_0" fill-rule="nonzero"/>
                            </clipPath>
                            <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_0_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z " to="M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keyTimes="0;0.1804511;0.2180451;0.2481203;0.2631579;0.2706767;0.2781955;0.2857143;0.3157895;0.3308271;0.3533835;0.3834586;0.406015;0.4135338;0.4210526;0.4511278;0.4736842;0.4887218;0.4962406;1" values="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M310.92 429.74 C310.92,429.74 310.97,429.75 310.97,429.75 C310.97,429.75 310.93,429.74 310.93,429.74 C310.93,429.74 310.91,429.77 310.91,429.77 C310.91,429.77 310.94,429.77 310.94,429.77 C310.94,429.77 310.99,429.77 310.99,429.77 C310.99,429.77 311.09,429.7 311.09,429.7 C311.09,429.7 310.99,429.73 310.99,429.73 C310.99,429.73 310.9,434.91 310.9,434.91 C310.9,434.91 312.25,433.8 312.25,433.8 C312.25,433.8 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 303.93,428.18 303.93,428.18 C303.93,428.18 303.66,428.14 303.66,428.14 C303.66,428.14 303.84,428.16 303.84,428.16 C303.84,428.16 303.52,428.11 303.52,428.11 C303.52,428.11 303.67,428.12 303.67,428.12 C303.67,428.12 303.58,428.1 303.58,428.1 C303.58,428.1 303.49,428.3 303.49,428.11 C303.49,427.93 303.63,428.09 303.63,428.09 C303.63,428.09 303.45,428.1 303.45,428.1 C303.45,428.1 303.76,428.04 303.76,428.04 C303.76,428.04 303.73,428 303.73,428 C303.73,428 303.69,427.98 303.69,427.98 C303.69,427.98 303.71,428.13 303.71,428.13 C303.71,428.13 303.76,428.08 303.76,428.08 C303.76,428.08 303.8,428.06 303.8,428.06 C303.8,428.06 303.8,428.11 303.8,428.11 C303.8,428.11 303.58,428.16 303.58,428.16 C303.58,428.16 310.92,429.75 310.92,429.75 C310.92,429.75 310.91,429.75 310.91,429.75 C310.91,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.9,429.75 310.9,429.75 C310.9,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.92,429.74 310.92,429.74z ;M299.65 434.12 C299.65,434.12 299.7,434.13 299.7,434.13 C299.7,434.13 299.66,434.11 299.66,434.11 C299.66,434.11 299.64,434.14 299.64,434.14 C299.64,434.14 299.66,434.14 299.66,434.14 C299.66,434.14 299.72,434.15 299.72,434.15 C299.72,434.15 299.81,434.08 299.81,434.08 C299.81,434.08 299.72,434.11 299.72,434.11 C299.72,434.11 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 300.06,430.31 300.06,430.31 C300.06,430.31 299.78,430.27 299.78,430.27 C299.78,430.27 299.96,430.29 299.96,430.29 C299.96,430.29 299.65,430.25 299.65,430.25 C299.65,430.25 299.8,430.25 299.8,430.25 C299.8,430.25 299.7,430.24 299.7,430.24 C299.7,430.24 299.61,430.43 299.61,430.25 C299.61,430.06 299.75,430.22 299.75,430.22 C299.75,430.22 299.57,430.23 299.57,430.23 C299.57,430.23 299.89,430.17 299.89,430.17 C299.89,430.17 299.85,430.13 299.85,430.13 C299.85,430.13 299.82,430.12 299.82,430.12 C299.82,430.12 299.83,430.26 299.83,430.26 C299.83,430.26 299.89,430.21 299.89,430.21 C299.89,430.21 299.93,430.19 299.93,430.19 C299.93,430.19 299.93,430.25 299.93,430.25 C299.93,430.25 299.7,430.29 299.7,430.29 C299.7,430.29 299.65,434.13 299.65,434.13 C299.65,434.13 299.64,434.13 299.64,434.13 C299.64,434.13 299.66,434.13 299.66,434.13 C299.66,434.13 299.63,434.13 299.63,434.13 C299.63,434.13 299.65,434.13 299.65,434.13 C299.65,434.13 299.65,434.12 299.65,434.12z ;M292.83 434.12 C292.83,434.12 292.81,434.11 292.81,434.11 C292.81,434.11 292.84,434.12 292.84,434.12 C292.84,434.12 292.82,434.15 292.82,434.15 C292.82,434.15 292.85,434.15 292.85,434.15 C292.85,434.15 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 292.89,430.31 292.89,430.31 C292.89,430.31 292.69,430.25 292.69,430.25 C292.69,430.25 292.72,430.28 292.72,430.28 C292.72,430.28 292.63,430.26 292.63,430.26 C292.63,430.26 292.65,430.43 292.65,430.25 C292.65,430.06 292.56,430.15 292.56,430.15 C292.56,430.15 292.61,430.23 292.61,430.23 C292.61,430.23 292.93,430.17 292.93,430.17 C292.93,430.17 292.89,430.13 292.89,430.13 C292.89,430.13 292.85,430.12 292.85,430.12 C292.85,430.12 292.87,430.26 292.87,430.26 C292.87,430.26 292.93,430.21 292.93,430.21 C292.93,430.21 292.96,430.19 292.96,430.19 C292.96,430.19 292.96,430.25 292.96,430.25 C292.96,430.25 292.77,430.22 292.77,430.22 C292.77,430.22 292.83,434.13 292.83,434.13 C292.83,434.13 292.82,434.13 292.82,434.13 C292.82,434.13 292.84,434.13 292.84,434.13 C292.84,434.13 292.81,434.13 292.81,434.13 C292.81,434.13 292.83,434.13 292.83,434.13 C292.83,434.13 292.83,434.12 292.83,434.12z ;M286.91 434.04 C286.91,434.04 286.89,434.02 286.89,434.02 C286.89,434.02 286.92,434.03 286.92,434.03 C286.92,434.03 286.9,434.06 286.9,434.06 C286.9,434.06 286.92,434.06 286.92,434.06 C286.92,434.06 294.61,434.08 294.61,434.08 C294.61,434.08 298.39,434.03 298.39,434.03 C298.39,434.03 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.91,428.68 291.91,428.68 C291.91,428.68 291.82,428.67 291.82,428.67 C291.82,428.67 291.88,428.65 291.88,428.46 C291.88,428.28 291.78,428.37 291.78,428.37 C291.78,428.37 291.84,428.44 291.84,428.44 C291.84,428.44 292.15,428.39 292.15,428.39 C292.15,428.39 292.12,428.35 292.12,428.35 C292.12,428.35 292.08,428.33 292.08,428.33 C292.08,428.33 292.1,428.48 292.1,428.48 C292.1,428.48 292.15,428.42 292.15,428.42 C292.15,428.42 292.19,428.41 292.19,428.41 C292.19,428.41 292.19,428.46 292.19,428.46 C292.19,428.46 291.97,428.51 291.97,428.51 C291.97,428.51 287.14,434.07 287.14,434.07 C287.14,434.07 286.89,434.05 286.89,434.05 C286.89,434.05 286.92,434.05 286.92,434.05 C286.92,434.05 286.89,434.05 286.89,434.05 C286.89,434.05 286.91,434.05 286.91,434.05 C286.91,434.05 286.91,434.04 286.91,434.04z ;M286.7 429.47 C286.7,429.47 286.88,429.37 286.88,429.37 C286.88,429.37 286.52,429.45 286.52,429.45 C286.52,429.45 286.83,429.85 286.83,429.85 C286.83,429.85 286.14,434.18 286.14,434.18 C286.14,434.18 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.08 298.37,434.08 C298.37,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.38,430.31 298.38,430.31 C298.38,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.99,426.42 291.99,426.42 C291.99,426.42 291.87,426.34 291.87,426.34 C291.87,426.34 292.01,426.25 292.01,426.07 C292.01,425.88 292.05,425.99 292.05,425.99 C292.05,425.99 291.97,425.95 291.97,425.95 C291.97,425.95 292.39,425.98 292.39,425.98 C292.39,425.98 292.27,426.05 292.27,426.05 C292.27,426.05 292.35,425.99 292.35,425.99 C292.35,425.99 292.32,426 292.32,426 C292.32,426 292.4,426 292.4,426 C292.4,426 292.4,426.06 292.4,426.06 C292.4,426.06 292.39,426.05 292.39,426.05 C292.39,426.05 292.62,426.45 292.62,426.45 C292.62,426.45 286.78,429.41 286.78,429.41 C286.78,429.41 286.55,429.2 286.55,429.2 C286.55,429.2 286.62,429.38 286.62,429.38 C286.62,429.38 286.51,429.44 286.51,429.44 C286.51,429.44 286.46,429.37 286.46,429.37 C286.46,429.37 286.7,429.47 286.7,429.47z ;M286.5 424.9 C286.5,424.9 286.87,424.72 286.87,424.72 C286.87,424.72 286.13,424.87 286.13,424.87 C286.13,424.87 286.76,425.64 286.76,425.64 C286.76,425.64 285.37,434.3 285.37,434.3 C285.37,434.3 294.63,434.09 294.63,434.09 C294.63,434.09 298.37,434.09 298.37,434.09 C298.37,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.36,430.31 298.36,430.31 C298.36,430.31 294.59,430.33 294.59,430.33 C294.59,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.06,424.16 292.06,424.16 C292.06,424.16 291.91,424.01 291.91,424.01 C291.91,424.01 292.13,423.86 292.13,423.68 C292.13,423.49 292.32,423.6 292.32,423.6 C292.32,423.6 292.1,423.46 292.1,423.46 C292.1,423.46 292.62,423.57 292.62,423.57 C292.62,423.57 292.43,423.75 292.43,423.75 C292.43,423.75 292.62,423.64 292.62,423.64 C292.62,423.64 292.54,423.53 292.54,423.53 C292.54,423.53 292.65,423.57 292.65,423.57 C292.65,423.57 292.62,423.72 292.62,423.72 C292.62,423.72 292.58,423.64 292.58,423.64 C292.58,423.64 293.27,424.39 293.27,424.39 C293.27,424.39 286.43,424.75 286.43,424.75 C286.43,424.75 286.2,424.35 286.2,424.35 C286.2,424.35 286.31,424.72 286.31,424.72 C286.31,424.72 286.13,424.83 286.13,424.83 C286.13,424.83 286.02,424.68 286.02,424.68 C286.02,424.68 286.5,424.9 286.5,424.9z ;M285.53 417.93 C285.53,417.93 285.61,418.01 285.61,418.01 C285.61,418.01 285.39,417.97 285.39,417.97 C285.39,417.97 285.68,418.12 285.68,418.12 C285.68,418.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.38,434.11 298.38,434.11 C298.38,434.11 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.71,430.31 298.71,430.31 C298.71,430.31 293.3,430.31 293.3,430.31 C293.3,430.31 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.26,417.75 291.26,417.56 C291.26,417.38 291.34,417.38 291.34,417.38 C291.34,417.38 291.45,417.54 291.45,417.54 C291.45,417.54 291.21,417.5 291.21,417.5 C291.21,417.5 291.32,417.45 291.32,417.45 C291.32,417.45 291.28,417.51 291.28,417.51 C291.28,417.51 291.5,417.56 291.5,417.56 C291.5,417.56 291.52,417.54 291.52,417.54 C291.52,417.54 291.45,417.6 291.45,417.6 C291.45,417.6 291.43,417.67 291.43,417.67 C291.43,417.67 291.41,417.89 291.41,417.89 C291.41,417.89 291.24,417.95 291.24,417.95 C291.24,417.95 285.98,417.86 285.98,417.86 C285.98,417.86 286.02,417.69 286.02,417.69 C286.02,417.69 285.92,417.77 285.92,417.77 C285.92,417.77 285.81,417.62 285.81,417.62 C285.81,417.62 285.53,417.93 285.53,417.93z ;M284.93 404.18 C284.93,404.18 281.14,411.97 281.14,411.97 C281.14,411.97 273.88,412.04 273.88,412.04 C273.88,412.04 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.08 298.36,434.08 C298.36,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.35,430.31 298.35,430.31 C298.35,430.31 294.59,430.32 294.59,430.32 C294.59,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 291.91,415.81 291.91,415.81 C291.91,415.81 291.8,415.82 291.8,415.82 C291.8,415.82 291.88,415.73 291.88,415.73 C291.88,415.73 291.9,415.66 291.9,415.66 C291.9,415.66 291.8,415.65 291.8,415.65 C291.8,415.65 291.73,415.73 291.73,415.73 C291.73,415.73 291.87,415.58 291.87,415.58 C291.87,415.58 291.87,415.71 291.87,415.71 C291.87,415.71 291.83,415.72 291.83,415.72 C291.83,415.72 291.82,415.71 291.82,415.71 C291.82,415.71 291.66,414.92 291.66,414.92 C291.66,414.92 291.45,413.38 291.45,413.38 C291.45,413.38 291.09,411.81 291.09,411.81 C291.09,411.81 291.05,411.77 291.05,411.77 C291.05,411.77 289.08,410.26 289.08,410.26 C289.08,410.26 284.93,404.18 284.93,404.18z ;M298.66 404.21 C298.66,404.21 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.76,430.32 298.76,430.32 C298.76,430.32 294.62,430.33 294.62,430.33 C294.62,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 300.75,413.19 300.75,413.19 C300.75,413.19 300.74,413.2 300.74,413.2 C300.74,413.2 300.68,413.28 300.68,413.28 C300.68,413.28 300.74,413.15 300.74,413.15 C300.74,413.15 300.76,413.19 300.76,413.19 C300.76,413.19 300.77,413.17 300.77,413.17 C300.77,413.17 303.55,406.44 303.55,406.44 C303.55,406.44 302.85,404.47 302.85,404.47 C302.85,404.47 301.29,403.47 301.29,403.47 C301.29,403.47 301.18,403.32 301.18,403.32 C301.18,403.32 298.66,404.21 298.66,404.21z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.07 294.61,434.07 C294.61,434.07 298.36,434.07 298.36,434.07 C298.36,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.73,430.31 298.73,430.31 C298.73,430.31 293.3,430.33 293.3,430.33 C293.3,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.59,416.02 302.59,416.02 C302.59,416.02 302.55,415.98 302.55,415.98 C302.55,415.98 302.63,415.99 302.63,415.99 C302.63,415.99 306.67,409.55 306.67,409.55 C306.67,409.55 306.65,409.61 306.65,409.61 C306.65,409.61 306.59,409.55 306.59,409.55 C306.59,409.55 306.69,409.72 306.69,409.72 C306.69,409.72 306.58,409.57 306.58,409.57 C306.58,409.57 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.29,420.1 306.29,420.1 C306.29,420.1 301.7,423.39 301.7,423.39 C301.7,423.39 298.38,430.31 298.38,430.31 C298.38,430.31 293.4,430.32 293.4,430.32 C293.4,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.63,417.02 302.63,417.02 C302.63,417.02 302.61,416.97 302.61,416.97 C302.61,416.97 302.63,416.9 302.63,416.9 C302.63,416.9 307.12,415.55 307.12,415.55 C307.12,415.55 307.51,415.47 307.51,415.47 C307.51,415.47 307.52,415.47 307.52,415.47 C307.52,415.47 309.01,412.56 309.01,412.56 C309.01,412.56 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.08 294.6,434.08 C294.6,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.05,420.2 306.05,420.2 C306.05,420.2 301.63,423.29 301.63,423.29 C301.63,423.29 298.57,430.33 298.57,430.33 C298.57,430.33 293.35,430.32 293.35,430.32 C293.35,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.72,417.16 306.72,417.16 C306.72,417.16 307.56,417.29 307.56,417.29 C307.56,417.29 307.59,417.33 307.59,417.33 C307.59,417.33 308.54,413.47 308.54,413.47 C308.54,413.47 306.71,408.22 306.71,408.22 C306.71,408.22 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.62,434.09 294.62,434.09 C294.62,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.8,420.3 305.8,420.3 C305.8,420.3 301.55,423.2 301.55,423.2 C301.55,423.2 298.74,430.31 298.74,430.31 C298.74,430.31 293.34,430.32 293.34,430.32 C293.34,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.32,418.77 306.32,418.77 C306.32,418.77 307.34,417.78 307.34,417.78 C307.34,417.78 307.74,418.52 307.74,418.52 C307.74,418.52 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.09 294.6,434.09 C294.6,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.98,420.3 305.98,420.3 C305.98,420.3 301.72,423.59 301.72,423.59 C301.72,423.59 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 296.68,421.72 296.68,421.72 C296.68,421.72 300.57,423.18 300.57,423.18 C300.57,423.18 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.41,419.97 306.41,419.97 C306.41,419.97 301.7,423.64 301.7,423.64 C301.7,423.64 298.69,430.31 298.69,430.31 C298.69,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 294.58,430.33 294.58,430.33 C294.58,430.33 298.38,430.31 298.38,430.31 C298.38,430.31 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.73,439.55 293.73,439.55 C293.73,439.55 298.46,439.54 298.46,439.54 C298.46,439.54 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.43,419.98 306.43,419.98 C306.43,419.98 301.75,423.57 301.75,423.57 C301.75,423.57 298.73,430.27 298.73,430.27 C298.73,430.27 293.72,430.3 293.72,430.3 C293.72,430.3 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.7,430.31 293.7,430.31 C293.7,430.31 298.74,430.26 298.74,430.26 C298.74,430.26 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                            <clipPath id="_R_G_L_0_C_0">
                                <path id="_R_G_L_0_C_0_P_0" fill-rule="nonzero"/>
                            </clipPath>
                            <animate attributeType="XML" attributeName="opacity" dur="2s" from="0" to="1" xlink:href="#time_group"/>
                        </defs>
                        <g id="_R_G">
                            <g id="_R_G_L_1_G" transform=" translate(127.638, 127.945) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                <g clip-path="url(#_R_G_L_1_C_0)">
                                    <path id="_R_G_L_1_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M328 398.61 C328,398.61 328,446.23 328,446.23 C328,449.7 325.2,452.5 321.75,452.5 C321.75,452.5 274.25,452.5 274.25,452.5 C270.8,452.5 268,449.7 268,446.23 C268,446.23 268,398.61 268,398.61 C268,395.15 270.8,392.35 274.25,392.35 C274.25,392.35 283.46,392.26 283.46,392.26 C283.46,392.26 283.46,390.38 283.46,390.38 C283.46,389.76 284.08,388.5 285.33,388.5 C286.58,388.5 287.21,389.75 287.21,390.38 C287.21,390.38 287.21,397.89 287.21,397.89 C287.21,398.53 286.59,399.78 285.33,399.78 C284.08,399.78 283.46,398.53 283.46,397.9 C283.46,397.9 283.46,396.02 283.46,396.02 C283.46,396.02 275.5,396.1 275.5,396.1 C273.43,396.1 271.75,397.79 271.75,399.86 C271.75,399.86 271.75,444.98 271.75,444.98 C271.75,447.06 273.43,448.74 275.5,448.74 C275.5,448.74 320.5,448.74 320.5,448.74 C322.57,448.74 324.25,447.06 324.25,444.98 C324.25,444.98 324.25,399.86 324.25,399.86 C324.25,397.79 322.57,396.1 320.5,396.1 C320.5,396.1 312.62,396.1 312.62,396.1 C312.62,396.1 312.63,397.06 312.63,397.99 C312.63,398.61 312,399.86 310.75,399.86 C309.5,399.86 308.88,398.61 308.88,397.98 C308.88,397.98 308.87,396.1 308.87,396.1 C308.87,396.1 301.88,396.1 301.88,396.1 C300.84,396.1 300,395.26 300,394.23 C300,393.19 300.84,392.35 301.88,392.35 C301.88,392.35 308.87,392.35 308.87,392.35 C308.87,392.35 308.87,390.47 308.87,390.47 C308.87,389.83 309.5,388.5 310.75,388.5 C312,388.5 312.62,389.84 312.62,390.47 C312.62,390.47 312.62,392.35 312.62,392.35 C312.62,392.35 321.75,392.35 321.75,392.35 C325.2,392.35 328,395.15 328,398.61z "/>
                                </g>
                            </g>
                            <g id="_R_G_L_0_G" transform=" translate(125.555, 126.412) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                <g clip-path="url(#_R_G_L_0_C_0)">
                                    <path id="_R_G_L_0_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M305.86 420.29 C305.86,420.29 307.11,419.04 307.11,415.28 C307.11,409.01 303.36,407.76 298.36,407.76 C298.36,407.76 287.11,407.76 287.11,407.76 C287.11,407.76 287.11,434.08 287.11,434.08 C287.11,434.08 294.61,434.08 294.61,434.08 C294.61,434.08 294.61,441.6 294.61,441.6 C294.61,441.6 298.36,441.6 298.36,441.6 C298.36,441.6 298.36,434.08 298.36,434.08 C302.71,434.08 305.73,434.08 307.98,431.3 C309.07,429.95 309.62,428.24 309.61,426.5 C309.61,425.58 309.51,424.67 309.3,424.05 C308.73,422.65 308.36,421.55 305.86,420.29z  M302.11 430.32 C302.11,430.32 298.36,430.32 298.36,430.32 C298.36,430.32 298.36,426.56 298.36,426.56 C298.36,424.48 300.03,422.8 302.11,422.8 C304.13,422.8 305.86,424.43 305.86,426.56 C305.86,428.78 304.03,430.32 302.11,430.32z  M299.07 419.95 C298.43,420.26 297.82,420.63 297.26,421.05 C295.87,422.1 294.61,423.58 294.61,426.56 C294.61,426.56 294.61,430.32 294.61,430.32 C294.61,430.32 290.86,430.32 290.86,430.32 C290.86,430.32 290.86,411.52 290.86,411.52 C290.86,411.52 298.36,411.52 298.36,411.52 C301.35,411.52 303.36,412.77 303.36,415.28 C303.36,417.58 301.65,418.68 299.07,419.95z "/>
                                </g>
                            </g>
                        </g>
                        <g id="time_group"/>
                    </svg>
                </div>
            </div>              
            <?php //echo $this->bookingpress_add_custom_duration_section_front_side_func(); ?>  
            
            <div v-if="typeof appointment_step_form_data.enable_custom_service_duration !== 'undefined' && ( appointment_step_form_data.enable_custom_service_duration == 'true'|| appointment_step_form_data.enable_custom_service_duration == true)">
                <div class="bpa-front--dt__custom-duration-is-full" v-if="typeof appointment_step_form_data.custom_service_duration_value !== 'undefined' && appointment_step_form_data.custom_service_duration_value == ''">
                    <div class="bpa-front-cdf__icon">
                        <svg width="53" height="52" viewBox="0 0 53 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="bpa-front-cdf__icon-bg" d="M53 33.7139C53 43.7567 43.5541 51.8979 33.3949 51.8979C23.2357 51.8979 15 43.7567 15 33.7139C15 23.6711 25.6561 12.8979 35.8153 12.8979C45.9745 12.8979 53 23.6711 53 33.7139Z" /> 
                            <g filter="url(#filter0_d_2605_6524)">
                                <path d="M37.5519 9.48846H3V44.1506H37.5519V9.48846Z" fill="white"/>
                            </g>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M32.197 6.07112C32.7117 6.07112 33.1283 5.6545 33.1283 5.13977V0.931347C33.1283 0.416616 32.7117 0 32.197 0C31.6822 0 31.2656 0.416616 31.2656 0.931347V5.13977C31.2656 5.6545 31.6822 6.07112 32.197 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M20.7243 6.07112C21.239 6.07112 21.6557 5.6545 21.6557 5.13977V0.931347C21.6557 0.416616 21.239 0 20.7243 0C20.2096 0 19.793 0.416616 19.793 0.931347V5.13977C19.793 5.6545 20.2096 6.07112 20.7243 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" fill-rule="evenodd" clip-rule="evenodd" d="M37.5519 9.4886V4.03637C37.5519 2.95257 36.6749 2.07556 35.5911 2.07556H4.96081C3.87852 2.07556 3 2.95257 3 4.03637V9.4886H37.5519ZM33.8217 5.46116C33.8217 6.35901 33.0939 7.08686 32.196 7.08686C31.2982 7.08686 30.5703 6.35901 30.5703 5.46116C30.5703 4.5633 31.2982 3.83545 32.196 3.83545C33.0939 3.83545 33.8217 4.5633 33.8217 5.46116ZM20.7253 7.08686C21.6232 7.08686 22.351 6.35901 22.351 5.46116C22.351 4.5633 21.6232 3.83545 20.7253 3.83545C19.8275 3.83545 19.0996 4.5633 19.0996 5.46116C19.0996 6.35901 19.8275 7.08686 20.7253 7.08686ZM10.8784 5.46116C10.8784 6.35901 10.1505 7.08686 9.25266 7.08686C8.35481 7.08686 7.62695 6.35901 7.62695 5.46116C7.62695 4.5633 8.35481 3.83545 9.25266 3.83545C10.1505 3.83545 10.8784 4.5633 10.8784 5.46116Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M9.25166 6.07112C9.76639 6.07112 10.183 5.6545 10.183 5.13977V0.931347C10.183 0.416616 9.76639 0 9.25166 0C8.73693 0 8.32031 0.416616 8.32031 0.931347V5.13977C8.32031 5.6545 8.73693 6.07112 9.25166 6.07112Z" />
                            <path d="M25.88 21.6296H32.4492V15.0603H25.88V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 21.6296H23.707V15.0603H17.1378V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 21.6296H14.9668V15.0603H8.39756V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M25.88 29.9683H32.4492V23.3991H25.88V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 29.9683H23.707V23.3991H17.1378V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M8.39756 29.9683H14.9668V23.3991H8.39756V29.9683Z" />
                            <path d="M10.7715 25.744L12.5934 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M12.5934 25.744L10.7715 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M25.88 38.3079H32.4492V31.7386H25.88V38.3079Z" stroke="#F0E0DF" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 38.3079H23.707V31.7386H17.1378V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 38.3079H14.9668V31.7386H8.39756V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M36.9588 21.31H33.7617V23.4157H36.9588V21.31Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M37.4509 21.8565H33.2682C32.7927 21.8565 32.4062 21.4701 32.4062 20.9946C32.4062 20.5191 32.7927 20.1327 33.2682 20.1327H37.4509C37.9264 20.1327 38.3128 20.5191 38.3128 20.9946C38.3128 21.4701 37.9264 21.8565 37.4509 21.8565Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M27.6055 24.5042L25.8027 26.3069L27.0739 27.5782L28.8767 25.7754L27.6055 24.5042Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.1741 24.4304L25.6563 26.9482C25.4027 27.2018 24.9907 27.2018 24.7371 26.9482C24.4835 26.6946 24.4835 26.2825 24.7371 26.0289L27.2549 23.5111C27.5085 23.2575 27.9206 23.2575 28.1741 23.5111C28.4292 23.7647 28.4292 24.1768 28.1741 24.4304Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.322 44.122C41.2901 44.122 46.1283 39.2838 46.1283 33.3156C46.1283 27.3474 41.2901 22.5093 35.322 22.5093C29.3538 22.5093 24.5156 27.3474 24.5156 33.3156C24.5156 39.2838 29.3538 44.122 35.322 44.122Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3229 43.5408C40.9701 43.5408 45.548 38.9629 45.548 33.3156C45.548 27.6684 40.9701 23.0905 35.3229 23.0905C29.6756 23.0905 25.0977 27.6684 25.0977 33.3156C25.0977 38.9629 29.6756 43.5408 35.3229 43.5408Z" />
                            <path d="M35.3224 42.3859C40.3319 42.3859 44.3928 38.325 44.3928 33.3155C44.3928 28.306 40.3319 24.2451 35.3224 24.2451C30.3129 24.2451 26.252 28.306 26.252 33.3155C26.252 38.325 30.3129 42.3859 35.3224 42.3859Z" fill="#F6F6F6"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 26.2884C35.2413 26.2884 35.1387 26.1858 35.1387 26.0605V25.5246C35.1387 25.3978 35.2413 25.2967 35.3666 25.2967C35.4934 25.2967 35.5945 25.3993 35.5945 25.5246V26.0605C35.5945 26.1858 35.4919 26.2884 35.3666 26.2884Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 41.4433C35.2413 41.4433 35.1387 41.3407 35.1387 41.2154V40.6795C35.1387 40.5542 35.2413 40.4516 35.3666 40.4516C35.4934 40.4516 35.5945 40.5542 35.5945 40.6795V41.2154C35.5945 41.3407 35.4919 41.4433 35.3666 41.4433Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M30.197 28.4288C30.1382 28.4288 30.0808 28.4062 30.0355 28.3624L29.6566 27.9835C29.5676 27.8945 29.5676 27.7495 29.6566 27.6605C29.7457 27.5714 29.8906 27.5714 29.9797 27.6605L30.3585 28.0394C30.4476 28.1284 30.4476 28.2733 30.3585 28.3624C30.3148 28.4077 30.2559 28.4288 30.197 28.4288Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.9138 39.1444C40.855 39.1444 40.7976 39.1217 40.7523 39.078L40.3734 38.6991C40.2844 38.61 40.2844 38.4651 40.3734 38.3761C40.4625 38.287 40.6074 38.287 40.6965 38.3761L41.0753 38.7549C41.1644 38.844 41.1644 38.9889 41.0753 39.078C41.0301 39.1233 40.9712 39.1444 40.9138 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M29.8182 39.1444C29.7593 39.1444 29.7019 39.1217 29.6566 39.078C29.5676 38.9889 29.5676 38.844 29.6566 38.7549L30.0355 38.3761C30.1246 38.287 30.2695 38.287 30.3585 38.3761C30.4476 38.4651 30.4476 38.61 30.3585 38.6991L29.9797 39.078C29.9344 39.1233 29.8755 39.1444 29.8182 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.5349 28.4287C40.4761 28.4287 40.4187 28.406 40.3734 28.3623C40.2844 28.2732 40.2844 28.1283 40.3734 28.0392L40.7523 27.6604C40.8414 27.5713 40.9863 27.5713 41.0753 27.6604C41.1644 27.7494 41.1644 27.8943 41.0753 27.9834L40.6965 28.3623C40.6512 28.4076 40.5923 28.4287 40.5349 28.4287Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.0568 33.5971H27.5209C27.3941 33.5971 27.293 33.4945 27.293 33.3692C27.293 33.2439 27.3956 33.1412 27.5209 33.1412H28.0568C28.1836 33.1412 28.2847 33.2439 28.2847 33.3692C28.2847 33.496 28.1836 33.5971 28.0568 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M43.213 33.5971H42.6771C42.5519 33.5971 42.4492 33.4945 42.4492 33.3692C42.4492 33.2439 42.5519 33.1412 42.6771 33.1412H43.213C43.3383 33.1412 43.4409 33.2439 43.4409 33.3692C43.4409 33.496 43.3383 33.5971 43.213 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.9141 33.2836H34.8242V28.5574C34.8242 28.257 35.0688 28.0125 35.3691 28.0125C35.6695 28.0125 35.9141 28.257 35.9141 28.5574V33.2836Z" />
                            <path d="M35.369 34.5334C36.0593 34.5334 36.6188 33.9738 36.6188 33.2835C36.6188 32.5933 36.0593 32.0337 35.369 32.0337C34.6787 32.0337 34.1191 32.5933 34.1191 33.2835C34.1191 33.9738 34.6787 34.5334 35.369 34.5334Z" fill="#2B4183"/>
                            <defs>
                                <filter id="filter0_d_2605_6524" x="0.897293" y="7.38576" width="41.5618" height="41.6711" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dx="1.4018" dy="1.4018"/>
                                    <feGaussianBlur stdDeviation="1.75226"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2605_6524"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2605_6524" result="shape"/>
                                </filter>
                            </defs>
                        </svg>
                    </div>
                    <div class="bpa-front--dt-ts__sub-heading">{{custom_service_duration_title}}</div>
                    <div class="bpa-front-cdf__desc">{{custom_service_description_title}}</div>
                    <el-form>
                        <div class="bpa-front-module--bd-form">
                            <el-row class="bpa-bd-fields-row">
                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                    <template>
                                        <div class="bpa-bdf--single-col-item">
                                            <el-form-item>
                                                <el-select v-model="appointment_step_form_data.custom_service_duration_value" popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" class="bpa-front-form-control" :placeholder="custom_please_select_title" @change="bookingpress_change_custom_duration_first($event)">
                                                    <el-option :label="custom_please_select_title" value=""></el-option>
                                                    <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value" ></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </div>
                                    </template>
                                </el-col>
                            </el-row>
                        </div>
                    </el-form>
                </div>
                <div class="bpa-front--dt__custom-duration-card" v-else-if="is_display_custom_duration_loader == false">
                    <div class="bpa-front-cdc__left">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-.22-13h-.06c-.4 0-.72.32-.72.72v4.72c0 .35.18.68.49.86l4.15 2.49c.34.2.78.1.98-.24.21-.34.1-.79-.25-.99l-3.87-2.3V7.72c0-.4-.32-.72-.72-.72z"/></svg>
                        <el-select popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" v-model="appointment_step_form_data.custom_service_duration_value" class="bpa-front-form-control" @change="bookingpress_change_custom_duration($event)">
                            <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value"></el-option>
                        </el-select>
                    </div>
                    <div class="bpa-front-cdc__right">
                        <div class="bpa-front-cdc__right-title">{{custom_price_title}}<div class="bpa-front-cdc__price-val">{{appointment_step_form_data.selected_service_price}}</div></div>
                    </div>
                </div>
                <div class="bpa-front-day-loader-container bpa-cd__is-day-service-loader" :class="(is_display_custom_duration_day_loader == true) ? 'bpa-cd-day-service__loader-active' : '' " v-if="is_display_custom_duration_day_loader == true">
                    <div class="bpa-front-loader">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" width="256" height="256" viewBox="0 0 256 256" style="width:100%;height:100%">
                            <defs>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_1_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z " to="M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keyTimes="0;0.5037594;0.5263158;0.5789474;0.6691729;0.6992481;0.7593985;0.7669173;1" values="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M303.49 386.7 C303.49,386.7 284.88,386.7 284.88,386.7 C284.88,386.7 284.88,402.72 284.88,402.72 C284.88,402.72 293.41,402.87 293.41,402.87 C293.41,402.87 293.07,405.24 293.07,405.24 C293.07,405.24 296.63,405.24 296.63,405.24 C296.63,405.24 296.82,402.57 296.82,402.57 C296.82,402.57 304.49,401.98 304.49,401.98 C304.49,401.98 303.49,386.7 303.49,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,398.12 265.56,398.12 C265.56,398.12 266.75,407.02 266.75,407.02 C266.75,407.02 294.78,405.83 294.78,405.83 C294.78,405.83 298.34,405.83 298.34,405.83 C298.34,405.83 332.75,406.72 332.75,406.72 C332.75,406.72 332.45,399.46 332.45,399.46 C332.45,399.46 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,442.32 265.56,442.32 C265.56,442.32 266.75,448.4 266.75,448.4 C266.75,448.4 283.8,447.51 283.8,447.51 C283.8,447.51 312.06,447.21 312.06,447.21 C312.06,447.21 332.75,448.1 332.75,448.1 C332.75,448.1 332.45,443.65 332.45,443.65 C332.45,443.65 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.86,453.14 265.86,453.14 C265.86,453.14 276.98,456.11 276.98,456.11 C276.98,456.11 277.28,447.51 277.28,447.51 C277.28,447.51 319.47,447.81 319.47,447.81 C319.47,447.81 318.81,456.11 318.81,456.11 C318.81,456.11 329.63,454.92 329.63,454.92 C329.63,454.92 330.97,386.7 330.97,386.7z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.63,448.83 295.63,448.83 C295.63,448.83 295.71,448.75 295.71,448.75 C295.71,448.75 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_1_C_0">
                                    <path id="_R_G_L_1_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_0_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z " to="M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keyTimes="0;0.1804511;0.2180451;0.2481203;0.2631579;0.2706767;0.2781955;0.2857143;0.3157895;0.3308271;0.3533835;0.3834586;0.406015;0.4135338;0.4210526;0.4511278;0.4736842;0.4887218;0.4962406;1" values="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M310.92 429.74 C310.92,429.74 310.97,429.75 310.97,429.75 C310.97,429.75 310.93,429.74 310.93,429.74 C310.93,429.74 310.91,429.77 310.91,429.77 C310.91,429.77 310.94,429.77 310.94,429.77 C310.94,429.77 310.99,429.77 310.99,429.77 C310.99,429.77 311.09,429.7 311.09,429.7 C311.09,429.7 310.99,429.73 310.99,429.73 C310.99,429.73 310.9,434.91 310.9,434.91 C310.9,434.91 312.25,433.8 312.25,433.8 C312.25,433.8 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 303.93,428.18 303.93,428.18 C303.93,428.18 303.66,428.14 303.66,428.14 C303.66,428.14 303.84,428.16 303.84,428.16 C303.84,428.16 303.52,428.11 303.52,428.11 C303.52,428.11 303.67,428.12 303.67,428.12 C303.67,428.12 303.58,428.1 303.58,428.1 C303.58,428.1 303.49,428.3 303.49,428.11 C303.49,427.93 303.63,428.09 303.63,428.09 C303.63,428.09 303.45,428.1 303.45,428.1 C303.45,428.1 303.76,428.04 303.76,428.04 C303.76,428.04 303.73,428 303.73,428 C303.73,428 303.69,427.98 303.69,427.98 C303.69,427.98 303.71,428.13 303.71,428.13 C303.71,428.13 303.76,428.08 303.76,428.08 C303.76,428.08 303.8,428.06 303.8,428.06 C303.8,428.06 303.8,428.11 303.8,428.11 C303.8,428.11 303.58,428.16 303.58,428.16 C303.58,428.16 310.92,429.75 310.92,429.75 C310.92,429.75 310.91,429.75 310.91,429.75 C310.91,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.9,429.75 310.9,429.75 C310.9,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.92,429.74 310.92,429.74z ;M299.65 434.12 C299.65,434.12 299.7,434.13 299.7,434.13 C299.7,434.13 299.66,434.11 299.66,434.11 C299.66,434.11 299.64,434.14 299.64,434.14 C299.64,434.14 299.66,434.14 299.66,434.14 C299.66,434.14 299.72,434.15 299.72,434.15 C299.72,434.15 299.81,434.08 299.81,434.08 C299.81,434.08 299.72,434.11 299.72,434.11 C299.72,434.11 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 300.06,430.31 300.06,430.31 C300.06,430.31 299.78,430.27 299.78,430.27 C299.78,430.27 299.96,430.29 299.96,430.29 C299.96,430.29 299.65,430.25 299.65,430.25 C299.65,430.25 299.8,430.25 299.8,430.25 C299.8,430.25 299.7,430.24 299.7,430.24 C299.7,430.24 299.61,430.43 299.61,430.25 C299.61,430.06 299.75,430.22 299.75,430.22 C299.75,430.22 299.57,430.23 299.57,430.23 C299.57,430.23 299.89,430.17 299.89,430.17 C299.89,430.17 299.85,430.13 299.85,430.13 C299.85,430.13 299.82,430.12 299.82,430.12 C299.82,430.12 299.83,430.26 299.83,430.26 C299.83,430.26 299.89,430.21 299.89,430.21 C299.89,430.21 299.93,430.19 299.93,430.19 C299.93,430.19 299.93,430.25 299.93,430.25 C299.93,430.25 299.7,430.29 299.7,430.29 C299.7,430.29 299.65,434.13 299.65,434.13 C299.65,434.13 299.64,434.13 299.64,434.13 C299.64,434.13 299.66,434.13 299.66,434.13 C299.66,434.13 299.63,434.13 299.63,434.13 C299.63,434.13 299.65,434.13 299.65,434.13 C299.65,434.13 299.65,434.12 299.65,434.12z ;M292.83 434.12 C292.83,434.12 292.81,434.11 292.81,434.11 C292.81,434.11 292.84,434.12 292.84,434.12 C292.84,434.12 292.82,434.15 292.82,434.15 C292.82,434.15 292.85,434.15 292.85,434.15 C292.85,434.15 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 292.89,430.31 292.89,430.31 C292.89,430.31 292.69,430.25 292.69,430.25 C292.69,430.25 292.72,430.28 292.72,430.28 C292.72,430.28 292.63,430.26 292.63,430.26 C292.63,430.26 292.65,430.43 292.65,430.25 C292.65,430.06 292.56,430.15 292.56,430.15 C292.56,430.15 292.61,430.23 292.61,430.23 C292.61,430.23 292.93,430.17 292.93,430.17 C292.93,430.17 292.89,430.13 292.89,430.13 C292.89,430.13 292.85,430.12 292.85,430.12 C292.85,430.12 292.87,430.26 292.87,430.26 C292.87,430.26 292.93,430.21 292.93,430.21 C292.93,430.21 292.96,430.19 292.96,430.19 C292.96,430.19 292.96,430.25 292.96,430.25 C292.96,430.25 292.77,430.22 292.77,430.22 C292.77,430.22 292.83,434.13 292.83,434.13 C292.83,434.13 292.82,434.13 292.82,434.13 C292.82,434.13 292.84,434.13 292.84,434.13 C292.84,434.13 292.81,434.13 292.81,434.13 C292.81,434.13 292.83,434.13 292.83,434.13 C292.83,434.13 292.83,434.12 292.83,434.12z ;M286.91 434.04 C286.91,434.04 286.89,434.02 286.89,434.02 C286.89,434.02 286.92,434.03 286.92,434.03 C286.92,434.03 286.9,434.06 286.9,434.06 C286.9,434.06 286.92,434.06 286.92,434.06 C286.92,434.06 294.61,434.08 294.61,434.08 C294.61,434.08 298.39,434.03 298.39,434.03 C298.39,434.03 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.91,428.68 291.91,428.68 C291.91,428.68 291.82,428.67 291.82,428.67 C291.82,428.67 291.88,428.65 291.88,428.46 C291.88,428.28 291.78,428.37 291.78,428.37 C291.78,428.37 291.84,428.44 291.84,428.44 C291.84,428.44 292.15,428.39 292.15,428.39 C292.15,428.39 292.12,428.35 292.12,428.35 C292.12,428.35 292.08,428.33 292.08,428.33 C292.08,428.33 292.1,428.48 292.1,428.48 C292.1,428.48 292.15,428.42 292.15,428.42 C292.15,428.42 292.19,428.41 292.19,428.41 C292.19,428.41 292.19,428.46 292.19,428.46 C292.19,428.46 291.97,428.51 291.97,428.51 C291.97,428.51 287.14,434.07 287.14,434.07 C287.14,434.07 286.89,434.05 286.89,434.05 C286.89,434.05 286.92,434.05 286.92,434.05 C286.92,434.05 286.89,434.05 286.89,434.05 C286.89,434.05 286.91,434.05 286.91,434.05 C286.91,434.05 286.91,434.04 286.91,434.04z ;M286.7 429.47 C286.7,429.47 286.88,429.37 286.88,429.37 C286.88,429.37 286.52,429.45 286.52,429.45 C286.52,429.45 286.83,429.85 286.83,429.85 C286.83,429.85 286.14,434.18 286.14,434.18 C286.14,434.18 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.08 298.37,434.08 C298.37,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.38,430.31 298.38,430.31 C298.38,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.99,426.42 291.99,426.42 C291.99,426.42 291.87,426.34 291.87,426.34 C291.87,426.34 292.01,426.25 292.01,426.07 C292.01,425.88 292.05,425.99 292.05,425.99 C292.05,425.99 291.97,425.95 291.97,425.95 C291.97,425.95 292.39,425.98 292.39,425.98 C292.39,425.98 292.27,426.05 292.27,426.05 C292.27,426.05 292.35,425.99 292.35,425.99 C292.35,425.99 292.32,426 292.32,426 C292.32,426 292.4,426 292.4,426 C292.4,426 292.4,426.06 292.4,426.06 C292.4,426.06 292.39,426.05 292.39,426.05 C292.39,426.05 292.62,426.45 292.62,426.45 C292.62,426.45 286.78,429.41 286.78,429.41 C286.78,429.41 286.55,429.2 286.55,429.2 C286.55,429.2 286.62,429.38 286.62,429.38 C286.62,429.38 286.51,429.44 286.51,429.44 C286.51,429.44 286.46,429.37 286.46,429.37 C286.46,429.37 286.7,429.47 286.7,429.47z ;M286.5 424.9 C286.5,424.9 286.87,424.72 286.87,424.72 C286.87,424.72 286.13,424.87 286.13,424.87 C286.13,424.87 286.76,425.64 286.76,425.64 C286.76,425.64 285.37,434.3 285.37,434.3 C285.37,434.3 294.63,434.09 294.63,434.09 C294.63,434.09 298.37,434.09 298.37,434.09 C298.37,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.36,430.31 298.36,430.31 C298.36,430.31 294.59,430.33 294.59,430.33 C294.59,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.06,424.16 292.06,424.16 C292.06,424.16 291.91,424.01 291.91,424.01 C291.91,424.01 292.13,423.86 292.13,423.68 C292.13,423.49 292.32,423.6 292.32,423.6 C292.32,423.6 292.1,423.46 292.1,423.46 C292.1,423.46 292.62,423.57 292.62,423.57 C292.62,423.57 292.43,423.75 292.43,423.75 C292.43,423.75 292.62,423.64 292.62,423.64 C292.62,423.64 292.54,423.53 292.54,423.53 C292.54,423.53 292.65,423.57 292.65,423.57 C292.65,423.57 292.62,423.72 292.62,423.72 C292.62,423.72 292.58,423.64 292.58,423.64 C292.58,423.64 293.27,424.39 293.27,424.39 C293.27,424.39 286.43,424.75 286.43,424.75 C286.43,424.75 286.2,424.35 286.2,424.35 C286.2,424.35 286.31,424.72 286.31,424.72 C286.31,424.72 286.13,424.83 286.13,424.83 C286.13,424.83 286.02,424.68 286.02,424.68 C286.02,424.68 286.5,424.9 286.5,424.9z ;M285.53 417.93 C285.53,417.93 285.61,418.01 285.61,418.01 C285.61,418.01 285.39,417.97 285.39,417.97 C285.39,417.97 285.68,418.12 285.68,418.12 C285.68,418.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.38,434.11 298.38,434.11 C298.38,434.11 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.71,430.31 298.71,430.31 C298.71,430.31 293.3,430.31 293.3,430.31 C293.3,430.31 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.26,417.75 291.26,417.56 C291.26,417.38 291.34,417.38 291.34,417.38 C291.34,417.38 291.45,417.54 291.45,417.54 C291.45,417.54 291.21,417.5 291.21,417.5 C291.21,417.5 291.32,417.45 291.32,417.45 C291.32,417.45 291.28,417.51 291.28,417.51 C291.28,417.51 291.5,417.56 291.5,417.56 C291.5,417.56 291.52,417.54 291.52,417.54 C291.52,417.54 291.45,417.6 291.45,417.6 C291.45,417.6 291.43,417.67 291.43,417.67 C291.43,417.67 291.41,417.89 291.41,417.89 C291.41,417.89 291.24,417.95 291.24,417.95 C291.24,417.95 285.98,417.86 285.98,417.86 C285.98,417.86 286.02,417.69 286.02,417.69 C286.02,417.69 285.92,417.77 285.92,417.77 C285.92,417.77 285.81,417.62 285.81,417.62 C285.81,417.62 285.53,417.93 285.53,417.93z ;M284.93 404.18 C284.93,404.18 281.14,411.97 281.14,411.97 C281.14,411.97 273.88,412.04 273.88,412.04 C273.88,412.04 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.08 298.36,434.08 C298.36,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.35,430.31 298.35,430.31 C298.35,430.31 294.59,430.32 294.59,430.32 C294.59,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 291.91,415.81 291.91,415.81 C291.91,415.81 291.8,415.82 291.8,415.82 C291.8,415.82 291.88,415.73 291.88,415.73 C291.88,415.73 291.9,415.66 291.9,415.66 C291.9,415.66 291.8,415.65 291.8,415.65 C291.8,415.65 291.73,415.73 291.73,415.73 C291.73,415.73 291.87,415.58 291.87,415.58 C291.87,415.58 291.87,415.71 291.87,415.71 C291.87,415.71 291.83,415.72 291.83,415.72 C291.83,415.72 291.82,415.71 291.82,415.71 C291.82,415.71 291.66,414.92 291.66,414.92 C291.66,414.92 291.45,413.38 291.45,413.38 C291.45,413.38 291.09,411.81 291.09,411.81 C291.09,411.81 291.05,411.77 291.05,411.77 C291.05,411.77 289.08,410.26 289.08,410.26 C289.08,410.26 284.93,404.18 284.93,404.18z ;M298.66 404.21 C298.66,404.21 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.76,430.32 298.76,430.32 C298.76,430.32 294.62,430.33 294.62,430.33 C294.62,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 300.75,413.19 300.75,413.19 C300.75,413.19 300.74,413.2 300.74,413.2 C300.74,413.2 300.68,413.28 300.68,413.28 C300.68,413.28 300.74,413.15 300.74,413.15 C300.74,413.15 300.76,413.19 300.76,413.19 C300.76,413.19 300.77,413.17 300.77,413.17 C300.77,413.17 303.55,406.44 303.55,406.44 C303.55,406.44 302.85,404.47 302.85,404.47 C302.85,404.47 301.29,403.47 301.29,403.47 C301.29,403.47 301.18,403.32 301.18,403.32 C301.18,403.32 298.66,404.21 298.66,404.21z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.07 294.61,434.07 C294.61,434.07 298.36,434.07 298.36,434.07 C298.36,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.73,430.31 298.73,430.31 C298.73,430.31 293.3,430.33 293.3,430.33 C293.3,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.59,416.02 302.59,416.02 C302.59,416.02 302.55,415.98 302.55,415.98 C302.55,415.98 302.63,415.99 302.63,415.99 C302.63,415.99 306.67,409.55 306.67,409.55 C306.67,409.55 306.65,409.61 306.65,409.61 C306.65,409.61 306.59,409.55 306.59,409.55 C306.59,409.55 306.69,409.72 306.69,409.72 C306.69,409.72 306.58,409.57 306.58,409.57 C306.58,409.57 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.29,420.1 306.29,420.1 C306.29,420.1 301.7,423.39 301.7,423.39 C301.7,423.39 298.38,430.31 298.38,430.31 C298.38,430.31 293.4,430.32 293.4,430.32 C293.4,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.63,417.02 302.63,417.02 C302.63,417.02 302.61,416.97 302.61,416.97 C302.61,416.97 302.63,416.9 302.63,416.9 C302.63,416.9 307.12,415.55 307.12,415.55 C307.12,415.55 307.51,415.47 307.51,415.47 C307.51,415.47 307.52,415.47 307.52,415.47 C307.52,415.47 309.01,412.56 309.01,412.56 C309.01,412.56 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.08 294.6,434.08 C294.6,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.05,420.2 306.05,420.2 C306.05,420.2 301.63,423.29 301.63,423.29 C301.63,423.29 298.57,430.33 298.57,430.33 C298.57,430.33 293.35,430.32 293.35,430.32 C293.35,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.72,417.16 306.72,417.16 C306.72,417.16 307.56,417.29 307.56,417.29 C307.56,417.29 307.59,417.33 307.59,417.33 C307.59,417.33 308.54,413.47 308.54,413.47 C308.54,413.47 306.71,408.22 306.71,408.22 C306.71,408.22 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.62,434.09 294.62,434.09 C294.62,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.8,420.3 305.8,420.3 C305.8,420.3 301.55,423.2 301.55,423.2 C301.55,423.2 298.74,430.31 298.74,430.31 C298.74,430.31 293.34,430.32 293.34,430.32 C293.34,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.32,418.77 306.32,418.77 C306.32,418.77 307.34,417.78 307.34,417.78 C307.34,417.78 307.74,418.52 307.74,418.52 C307.74,418.52 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.09 294.6,434.09 C294.6,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.98,420.3 305.98,420.3 C305.98,420.3 301.72,423.59 301.72,423.59 C301.72,423.59 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 296.68,421.72 296.68,421.72 C296.68,421.72 300.57,423.18 300.57,423.18 C300.57,423.18 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.41,419.97 306.41,419.97 C306.41,419.97 301.7,423.64 301.7,423.64 C301.7,423.64 298.69,430.31 298.69,430.31 C298.69,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 294.58,430.33 294.58,430.33 C294.58,430.33 298.38,430.31 298.38,430.31 C298.38,430.31 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.73,439.55 293.73,439.55 C293.73,439.55 298.46,439.54 298.46,439.54 C298.46,439.54 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.43,419.98 306.43,419.98 C306.43,419.98 301.75,423.57 301.75,423.57 C301.75,423.57 298.73,430.27 298.73,430.27 C298.73,430.27 293.72,430.3 293.72,430.3 C293.72,430.3 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.7,430.31 293.7,430.31 C293.7,430.31 298.74,430.26 298.74,430.26 C298.74,430.26 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_0_C_0">
                                    <path id="_R_G_L_0_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate attributeType="XML" attributeName="opacity" dur="2s" from="0" to="1" xlink:href="#time_group"/>
                            </defs>
                            <g id="_R_G">
                                <g id="_R_G_L_1_G" transform=" translate(127.638, 127.945) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_1_C_0)">
                                        <path id="_R_G_L_1_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M328 398.61 C328,398.61 328,446.23 328,446.23 C328,449.7 325.2,452.5 321.75,452.5 C321.75,452.5 274.25,452.5 274.25,452.5 C270.8,452.5 268,449.7 268,446.23 C268,446.23 268,398.61 268,398.61 C268,395.15 270.8,392.35 274.25,392.35 C274.25,392.35 283.46,392.26 283.46,392.26 C283.46,392.26 283.46,390.38 283.46,390.38 C283.46,389.76 284.08,388.5 285.33,388.5 C286.58,388.5 287.21,389.75 287.21,390.38 C287.21,390.38 287.21,397.89 287.21,397.89 C287.21,398.53 286.59,399.78 285.33,399.78 C284.08,399.78 283.46,398.53 283.46,397.9 C283.46,397.9 283.46,396.02 283.46,396.02 C283.46,396.02 275.5,396.1 275.5,396.1 C273.43,396.1 271.75,397.79 271.75,399.86 C271.75,399.86 271.75,444.98 271.75,444.98 C271.75,447.06 273.43,448.74 275.5,448.74 C275.5,448.74 320.5,448.74 320.5,448.74 C322.57,448.74 324.25,447.06 324.25,444.98 C324.25,444.98 324.25,399.86 324.25,399.86 C324.25,397.79 322.57,396.1 320.5,396.1 C320.5,396.1 312.62,396.1 312.62,396.1 C312.62,396.1 312.63,397.06 312.63,397.99 C312.63,398.61 312,399.86 310.75,399.86 C309.5,399.86 308.88,398.61 308.88,397.98 C308.88,397.98 308.87,396.1 308.87,396.1 C308.87,396.1 301.88,396.1 301.88,396.1 C300.84,396.1 300,395.26 300,394.23 C300,393.19 300.84,392.35 301.88,392.35 C301.88,392.35 308.87,392.35 308.87,392.35 C308.87,392.35 308.87,390.47 308.87,390.47 C308.87,389.83 309.5,388.5 310.75,388.5 C312,388.5 312.62,389.84 312.62,390.47 C312.62,390.47 312.62,392.35 312.62,392.35 C312.62,392.35 321.75,392.35 321.75,392.35 C325.2,392.35 328,395.15 328,398.61z "/>
                                    </g>
                                </g>
                                <g id="_R_G_L_0_G" transform=" translate(125.555, 126.412) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_0_C_0)">
                                        <path id="_R_G_L_0_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M305.86 420.29 C305.86,420.29 307.11,419.04 307.11,415.28 C307.11,409.01 303.36,407.76 298.36,407.76 C298.36,407.76 287.11,407.76 287.11,407.76 C287.11,407.76 287.11,434.08 287.11,434.08 C287.11,434.08 294.61,434.08 294.61,434.08 C294.61,434.08 294.61,441.6 294.61,441.6 C294.61,441.6 298.36,441.6 298.36,441.6 C298.36,441.6 298.36,434.08 298.36,434.08 C302.71,434.08 305.73,434.08 307.98,431.3 C309.07,429.95 309.62,428.24 309.61,426.5 C309.61,425.58 309.51,424.67 309.3,424.05 C308.73,422.65 308.36,421.55 305.86,420.29z  M302.11 430.32 C302.11,430.32 298.36,430.32 298.36,430.32 C298.36,430.32 298.36,426.56 298.36,426.56 C298.36,424.48 300.03,422.8 302.11,422.8 C304.13,422.8 305.86,424.43 305.86,426.56 C305.86,428.78 304.03,430.32 302.11,430.32z  M299.07 419.95 C298.43,420.26 297.82,420.63 297.26,421.05 C295.87,422.1 294.61,423.58 294.61,426.56 C294.61,426.56 294.61,430.32 294.61,430.32 C294.61,430.32 290.86,430.32 290.86,430.32 C290.86,430.32 290.86,411.52 290.86,411.52 C290.86,411.52 298.36,411.52 298.36,411.52 C301.35,411.52 303.36,412.77 303.36,415.28 C303.36,417.58 301.65,418.68 299.07,419.95z "/>
                                    </g>
                                </g>
                            </g>
                            <g id="time_group"/>
                        </svg>
                    </div>
                </div>                 
            </div>            


        </div>
        <?php 
           
        }

      
        /**
         * Function for get day calendar custom duration
         *
         * @param  mixed $response
         * @param  mixed $bookingpress_selected_date
         * @param  mixed $bookingpress_selected_service
         * @param  mixed $bookingpress_appointment_data
         * @param  mixed $whole_day
         * @return void
         */
        function bookingpress_get_multiple_days_disable_dates_func( $response, $bookingpress_selected_date, $bookingpress_selected_service, $bookingpress_appointment_data, $whole_day = false ){ 

            global $bookingpress_pro_payment_gateways,$bookingpress_service_extra,$tbl_bookingpress_services,$bookingpress_bring_anyone_with_you,$bookingpress_services,$tbl_bookingpress_staffmembers_services,$wpdb,$BookingPress,$tbl_bookingpress_appointment_bookings,$bookingpress_bring_anyone_with_you;            
            $enable_custom_service_duration = (isset($bookingpress_appointment_data['enable_custom_service_duration']))?$bookingpress_appointment_data['enable_custom_service_duration']:'';
            $bookingpress_custom_service_durations_slot = array();
            if($enable_custom_service_duration){
                $bookingpress_service_id = $bookingpress_selected_service;                
                if(!empty($bookingpress_service_id)) {
                    $custom_service_max_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_max_duration');
                    $custom_service_min_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_min_duration');
                    $service_duration_unit = !empty($bookingpress_appointment_data['selected_service_duration_unit']) ? sanitize_text_field($bookingpress_appointment_data['selected_service_duration_unit']) : '';
                    $selected_service_duration = !empty($bookingpress_appointment_data['selected_service_duration']) ? intval($bookingpress_appointment_data['selected_service_duration']) : "" ;
                    $service_price_without_currency = !empty($bookingpress_appointment_data['service_price_without_currency']) ? ($bookingpress_appointment_data['service_price_without_currency']) : "" ;
                    if(!isset($bookingpress_appointment_data['selected_service']) && isset($_POST['selected_service'])) { //phpcs:ignore WordPress.Security.NonceVerification
                        $bookingpress_appointment_data['selected_service'] = intval($_POST['selected_service']); //phpcs:ignore WordPress.Security.NonceVerification
                    }                       
                $bookingpress_service_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_service_duration_unit,bookingpress_service_duration_val,bookingpress_service_price FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $bookingpress_service_id ),ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_services is table name defined globally. False Positive alarm         
                    $service_duration_unit = $bookingpress_service_data['bookingpress_service_duration_unit'];
                    $bookingpress_appointment_data['selected_service_duration_unit'] = $service_duration_unit;                                        
                    $selected_service_duration = intval($bookingpress_service_data['bookingpress_service_duration_val']);

                    //$bookingpress_appointment_data['selected_service_duration'] = $selected_service_duration; 

                    $service_price_without_currency = $bookingpress_service_data['bookingpress_service_price'];
                    $bookingpress_appointment_data['service_price_without_currency'] = $service_price_without_currency;                    
                    if($service_duration_unit == 'd'){
                        $selected_service_duration = $selected_service_duration * 60 * 24; 
                        $service_slot_step = $selected_service_duration;
                        if(!empty($custom_service_max_duration) && !empty($service_duration_unit) && !empty($service_slot_step)) {                            
                                while($service_slot_step <= $custom_service_max_duration ) {
                                    if(empty($custom_service_min_duration) || $custom_service_min_duration <= $service_slot_step ) {
                                        $bookingpress_appointment_data['custom_service_duration_real_value'] = $service_slot_step; 
                                        $service_slot_step_value = $service_slot_step;
                                        //$service_slot_step_value = apply_filters( 'bookingpress_modify_service_timeslot',$service_slot_step,$bookingpress_service_id,$service_duration_unit);
                                        //$service_slot_step_value = $bookingpress_service_extra->bookingpress_modify_service_timeslot_with_service_extras( $service_slot_step, $bookingpress_service_id, $service_duration_unit );
                                        $service_duration_text = $this->bookingpress_get_duration_text_using_minute($service_slot_step_value,$service_duration_unit);
    
                                        $appointment_calculate_step_form_data = $bookingpress_pro_payment_gateways->bookingpress_recalculate_appointment_data_func($bookingpress_appointment_data);                                
                                        $appointment_calculate_step_form_data = !empty($appointment_calculate_step_form_data) ? json_decode($appointment_calculate_step_form_data,true) :  array();                                
                                        $bookingpress_custom_service_durations_slot[] = array (
                                            'value' => $service_slot_step_value,
                                            'text'  => $service_duration_text,
                                            'service_price_without_currency' => $appointment_calculate_step_form_data['appointment_data']['service_price_without_currency'],
                                            //'service_price' => $appointment_calculate_step_form_data['appointment_data']['selected_service_price'],
                                            'service_price' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($appointment_calculate_step_form_data['appointment_data']['bookingpress_custom_service_duration_price']),
                                            'real_value' => $service_slot_step,
                                            'real_price' => $appointment_calculate_step_form_data['appointment_data']['bookingpress_custom_service_duration_price'],
                                            'service_duration_unit' => 'd',
                                        );                                
                                    }                               
                                    $service_slot_step += $selected_service_duration;                                
                                }                            
                        }                        
                    }   

                }
                

            }
            
            if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookingpress_get_disable_date') {
                if( !empty( $bookingpress_custom_service_durations_slot ) ){
                        $response['prevent_next_month_check'] = false;
                        $response['empty_front_timings'] = false;
                }
                $response['bookingpress_custom_service_durations_slot'] = $bookingpress_custom_service_durations_slot;
            }            
            return $response;
        }

        public function bookingpress_after_change_service_unit_fun(){
        ?>
           const vm = this;
           if(vm.service.service_duration_unit == 'd'){
                vm.service_custom_duration_form.service_duration_unit = 'd';                
                vm.max_duration_time_options = ''; 
           }else{
                if(vm.service_custom_duration_form.service_duration_unit == 'd'){
                    vm.service_custom_duration_form.service_duration_unit = vm.service.service_duration_unit;
                }
           }
        <?php
        }        


        function bookingpress_is_custom_service_duration_addon_activated($plugin,$network_activation)
        { 
            $myaddon_name = "bookingpress-custom-service-duration/bookingpress-custom-service-duration.php";

            if($plugin == $myaddon_name)
            { 
                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($plugin, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($plugin, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_CUSTOM_SERVICE_DURATION_STORE_URL;
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
                            deactivate_plugins($plugin, FALSE);
                            $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($plugin, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                }
            }
        }
	
	/**
         * Enable flag for custom service duration for each services
         *
         * @param  mixed $bpa_all_services
         * @param  mixed $service
         * @param  mixed $selected_service
         * @param  mixed $bookingpress_category
         * @return void
         */
        function bookingpress_enable_custom_duration_on_service( $bpa_all_services, $service, $selected_service, $bookingpress_category ){

            foreach( $bpa_all_services as $service_id => $service_data ){
                $service_meta = $service_data['services_meta'];
                if( isset( $service_meta['enable_custom_service_duration'] ) ){
                    $bpa_all_services[ $service_id ]['enable_custom_service_duration'] = $service_meta['enable_custom_service_duration'];
                }
            }

            return $bpa_all_services;
        }

        function bookingpress_get_disable_dates_with_custom_service_duration(){

            $start_ms = microtime( true );
            global $wpdb, $BookingPress, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_payment_logs;
            $response              = array();
            $wpnonce               = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
            $bpa_verify_nonce_flag = wp_verify_nonce($wpnonce, 'bpa_wp_nonce');

            if (! $bpa_verify_nonce_flag ) {
                $response['variant']      = 'error';
                $response['title']        = esc_html__('Error', 'bookingpress-custom-service-duration');
                $response['msg']          = esc_html__('Sorry, Your request can not be processed due to security reason.', 'bookingpress-custom-service-duration');
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

            $bookingpress_custom_service_durations_slot_fetch = (isset($_POST['bookingpress_custom_service_durations_slot_fetch']))?$_POST['bookingpress_custom_service_durations_slot_fetch']:'';

            if( !empty( $_POST['action'] ) && 'bookingpress_get_disable_date' == $_POST['action'] && !empty( $_POST['appointment_data_obj'] ) && !empty( $_POST['appointment_data_obj']['enable_custom_service_duration'] ) && 'true' == $_POST['appointment_data_obj']['enable_custom_service_duration'] && (empty( $_POST['appointment_data_obj']['custom_service_duration_value']) || (isset($_POST['appointment_data_obj']['appointment_update_id']) && !empty($bookingpress_custom_service_durations_slot_fetch))) ){ // phpcs:ignore 
                
                global $bookingpress_appointment_bookings, $bookingpress_pro_appointment_bookings;

                remove_action( 'wp_ajax_bookingpress_get_disable_date', array( $bookingpress_appointment_bookings, 'bookingpress_get_disable_date_func' ) );
				remove_action( 'wp_ajax_nopriv_bookingpress_get_disable_date', array( $bookingpress_appointment_bookings, 'bookingpress_get_disable_date_func' ) );

                remove_action( 'wp_ajax_bookingpress_get_disable_date', array( $bookingpress_pro_appointment_bookings, 'bookingpress_get_disable_dates_for_days_services' ), 5 );
                remove_action( 'wp_ajax_nopriv_bookingpress_get_disable_date', array( $bookingpress_pro_appointment_bookings, 'bookingpress_get_disable_dates_for_days_services' ), 5 );

                $bookingpress_selected_date = !empty($_REQUEST['selected_date']) ? sanitize_text_field($_REQUEST['selected_date']) : '';

                $bookingpress_appointment_data = !empty($_POST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_data_obj'] ) : array(); // phpcs:ignore

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

				if(!empty($bookingpress_selected_date)){
					$bookingpress_selected_date = date('Y-m-d', strtotime($bookingpress_selected_date));
				}
				
				if( "NaN-NaN-NaN" == $bookingpress_selected_date || '1970-01-01' == $bookingpress_selected_date || !preg_match('/(\d{4}\-\d{2}\-\d{2})/', $bookingpress_selected_date ) ){
					$bookingpress_selected_date = date('Y-m-d', current_time('timestamp') );
				}
				
				$bookingpress_selected_service= !empty($_REQUEST['selected_service']) ? intval($_REQUEST['selected_service']) : '';
			
				if(empty($bookingpress_selected_service)){
					$bookingpress_selected_service = $bookingpress_appointment_data['selected_service'];
				}

                $bookingpress_start_date = date('Y-m-d', current_time('timestamp') );

                /** get maximum period available from booking */
				$get_period_available_for_booking = $BookingPress->bookingpress_get_settings('period_available_for_booking', 'general_setting');

				/** Modify get available time of booking if the service expiration time is set */
				$get_period_available_for_booking = apply_filters( 'bookingpress_modify_max_available_time_for_booking', $get_period_available_for_booking, $bookingpress_start_date, $bookingpress_selected_service );

                if( !empty( $bookingpress_selected_service ) ){

                    $response['prevent_next_month_check'] = false;


					$bookingpress_start_date_with_time = date('Y-m-d H:i:s', current_time( 'timestamp') );

					/** apply filter to modify start date. in case of Minimum Time Required Booking */
					$bookingpress_start_date = apply_filters( 'bookingpress_modify_disable_date_start_date', $bookingpress_start_date, $bookingpress_selected_service, $bookingpress_start_date_with_time );

					$bookingpress_temp_end_date = date('Y-m-d', strtotime('last day of this month', strtotime( $bookingpress_start_date )));

					$bookingpress_end_date = date('Y-m-d', strtotime( '+' . $get_period_available_for_booking . ' days') );

					$next_month = date( 'm', strtotime( $bookingpress_temp_end_date . '+1 day' ) );
					$next_year = date( 'Y', strtotime( $bookingpress_temp_end_date . '+1 day' ) );
					
					$bookingpress_selected_staffmember_id = !empty($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : '';

					/** Get the default days off in the above limit */
					$bpa_retrieves_default_disabled_dates = $BookingPress->bookingpress_retrieve_off_days( $bookingpress_start_date, ( $get_period_available_for_booking + 1 ), $bookingpress_selected_service, $selected_service_duration_in_min, $bookingpress_selected_staffmember_id );

					/** loop through each days until the limit has been reached
					 * for lite - it'll check for the next 365 days
					 * for pro - it'll check up to the X number of days defined in the settings
					 */
					$bpa_begin_date = new DateTime( $bookingpress_start_date );
					$bpa_end_date = new DateTime( date('Y-m-d', strtotime($bookingpress_end_date . '+1 day')) );
					
					$bpa_interval = DateInterval::createFromDateString('1 day');
					$period = new DatePeriod($bpa_begin_date, $bpa_interval, $bpa_end_date);

					//$bookingpress_selected_date = $bookingpress_end_date;
					$front_timings_data = array();

					$count = 0;
					$stop_date = '';

                    foreach( $period as $dt ){
                        $bpa_check_date = $dt->format('Y-m-d');
    
                        if( in_array( $bpa_check_date, $bpa_retrieves_default_disabled_dates ) ){
                            continue;
                        }

                        $bookingpress_time_slots = $bookingpress_appointment_bookings->bookingpress_retrieve_timeslots( $bpa_check_date, true );

                        $bookingpress_selected_date = $bpa_check_date;
                        break;
                    }
                }

                $bookingpress_disable_date = array();
                foreach( $bpa_retrieves_default_disabled_dates as $disabled_dates ){
                    $bookingpress_disable_date[] = date('c', strtotime( $disabled_dates ) );
                }

                $attributes = array();

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

                $response = apply_filters( 'bookingpress_modify_disable_date_data', $response );

                $end_ms = microtime( true );
                $response['time_taken'] = ( $end_ms - $start_ms ) . ' seconds';

                wp_send_json($response);
                exit;

            }

        }

        function bookingpress_restrict_pro_module_timeslots( $service_timings_data, $selected_service_id, $selected_date, $minimum_time_required, $service_max_capacity, $bookingpress_show_time_as_per_service_duration ){

            if( isset( $_POST['bpa_fetch_data'] ) && 'true' == $_POST['bpa_fetch_data'] ){ //phpcs:ignore WordPress.Security.NonceVerification
                return $service_timings_data; //phpcs:ignore WordPress.Security.NonceVerification
            }
            
            if( !empty( $_POST['action'] ) && 'bookingpress_get_disable_date' == $_POST['action'] && !empty( $_POST['appointment_data_obj'] ) && !empty( $_POST['appointment_data_obj']['enable_custom_service_duration'] ) && 'true' == $_POST['appointment_data_obj']['enable_custom_service_duration'] && empty( $_POST['appointment_data_obj']['custom_service_duration_value'] ) ){ // phpcs:ignore 

                $backtrace_summary = wp_debug_backtrace_summary( null, 0, false );
                $check_key = 'bookingpress_appointment_bookings->bookingpress_check_booked_appointments';

                if( in_array( $check_key, $backtrace_summary ) ){
                    return $service_timings_data;
                }

                $service_timings_data['is_daysoff'] = true;
                $service_timings_data['is_custom_duration'] = true;
            }
            return $service_timings_data;
        }

        function bookingpress_modify_service_timeslot_with_custom_duration( $default_time_slot, $service_id, $service_time_duration_unit, $bpa_fetch_updated_slots = false ){
            if( isset( $_POST['bpa_fetch_data'] ) && 'true' == $_POST['bpa_fetch_data'] && false == $bpa_fetch_updated_slots ){ //phpcs:ignore WordPress.Security.NonceVerification 
                return $default_time_slot;
            }
                
            if( !empty( $_POST['enable_custom_service_duration'] ) && 'true' == $_POST['enable_custom_service_duration'] && !empty( $_POST['custom_service_duration_value'] ) ){ //phpcs:ignore WordPress.Security.NonceVerification
                global $bookingpress_service_extra;
                $default_time_slot = intval( $_POST['custom_service_duration_value'] ); //phpcs:ignore WordPress.Security.NonceVerification
                remove_filter( 'bookingpress_modify_service_timeslot', array( $bookingpress_service_extra, 'bookingpress_modify_service_timeslot_with_service_extras'), 10 );
            }

            if( !empty( $_POST['appointment_data_obj']['enable_custom_service_duration'] ) && 'true' == $_POST['appointment_data_obj']['enable_custom_service_duration'] && !empty( $_POST['appointment_data_obj']['custom_service_duration_value'] ) ){ //phpcs:ignore WordPress.Security.NonceVerification
                global $bookingpress_service_extra;
                $default_time_slot = intval( $_POST['appointment_data_obj']['custom_service_duration_value'] ); //phpcs:ignore WordPress.Security.NonceVerification
                remove_filter( 'bookingpress_modify_service_timeslot', array( $bookingpress_service_extra, 'bookingpress_modify_service_timeslot_with_service_extras'), 10 );
            }

            return $default_time_slot;
        }

        public function is_addon_activated(){
            $bookingpress_c_d_version = get_option('bookingpress_custom_service_duration_version');
            return !empty($bookingpress_c_d_version) ? 1 : 0;
        }

        function bookingpress_custom_duration_upgrade_data() {
            global $BookingPress;
            $bookingpress_db_csd_version = get_option('bookingpress_custom_service_duration_version', true);
            if( version_compare( $bookingpress_db_csd_version, '1.8', '<' ) ){
                $bookingpress_load_csd_upgrade_file = BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . '/core/views/upgrade_custom_service_duration_latest_data.php';
                include $bookingpress_load_csd_upgrade_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();
            }
        }

        function bookingpress_custom_service_duration_admin_notices(){
            if( !function_exists('is_plugin_active') ){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            if( !is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php') ){
                echo "<div class='notice notice-warning'><p>" . esc_html__('BookingPress - Custom Service Duration plugin requires Bookingpress Premium Plugin installed and active.', 'bookingpress-custom-service-duration') . "</p></div>";
            }
            if( file_exists( WP_PLUGIN_DIR . '/bookingpress-cart/bookingpress-cart.php' ) ){
                $bpa_cart_plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/bookingpress-cart/bookingpress-cart.php' );
                $bpa_cart_plugin_version = $bpa_cart_plugin_info['Version'];
                if( version_compare( $bpa_cart_plugin_version, '1.4', '<' ) ){
                    echo "<div class='notice notice-error is-dismissible'><p>".esc_html__("It's highly recommended to update the BookingPress cart plugin to version 1.4 or higher in order to use the BookingPress Custom Service Duration plugin", "bookingpress-custom-service-duration")."</p></div>";
                }                
            }
            if( file_exists( WP_PLUGIN_DIR . '/bookingpress-invoice/bookingpress-invoice.php' ) ){
                $bpa_invoice_plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/bookingpress-invoice/bookingpress-invoice.php' );
                $bpa_invoice_plugin_version = $bpa_invoice_plugin_info['Version'];
                if( version_compare( $bpa_invoice_plugin_version, '1.4', '<' ) ){
                    echo "<div class='notice notice-error is-dismissible'><p>".esc_html__("It's highly recommended to update the BookingPress invoice plugin to version 1.4 or higher in order to use the BookingPress Custom Service Duration plugin", "bookingpress-custom-service-duration")."</p></div>";
                }
            }
            if( file_exists( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) ){
                $bpa_pro_plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' );
                $bpa_pro_plugin_version = $bpa_pro_plugin_info['Version'];
                if( version_compare( $bpa_pro_plugin_version, '2.1', '<' ) ){
                    echo "<div class='notice notice-error is-dismissible'><p>".esc_html__("It's Required to update the BookingPress Premium Plugin to version 2.1 or higher in order to use the BookingPress Custom Service Duration plugin", "bookingpress-custom-service-duration").".</p></div>";
                }
            }
            
        }        

        function bookingpress_modify_capability_data_func($bpa_caps){
            $bpa_caps['bookingpress_settings'][] = 'get_service_custom_durations';
            return $bpa_caps;

        }         

	    function set_custom_service_duration_css() {
            global $bookingpress_slugs;
            wp_register_style( 'bookingpress_custom_service_duration_admin_css', BOOKINGPRESS_CUSTOM_SERVICE_DURATION_URL . '/css/bookingpress_custom_service_duration_admin.css', array(),BOOKINGPRESS_CUSTOM_SERVICE_DURATION_VERSION );
            if ( isset( $_REQUEST['page'] ) && (sanitize_text_field( $_REQUEST['page'] ) == 'bookingpress_services' || $_REQUEST['page'] ) == 'bookingpress_staff_members') { // phpcs:ignore
                wp_enqueue_style('bookingpress_custom_service_duration_admin_css');
            }            
        }   
        
        function bookingpress_add_front_side_sidebar_step_content_func($bookingpress_goback_btn_text = '', $bookingpress_next_btn_text= '', $bookingpress_third_tab_name= ''){
            global $bookingpress_custom_service_duration_version;
            wp_register_style( 'bookingpress-custom-service-duration-front', BOOKINGPRESS_CUSTOM_SERVICE_DURATION_URL . '/css/bookingpress-custom-service-duration-front.css', array(), $bookingpress_custom_service_duration_version );
            wp_enqueue_style( 'bookingpress-custom-service-duration-front' );

            wp_register_style( 'bookingpress-custom-service-duration-front-rtl', BOOKINGPRESS_CUSTOM_SERVICE_DURATION_URL . '/css/bookingpress-custom-service-duration-front-rtl.css', array(), $bookingpress_custom_service_duration_version );

            if (is_rtl() ) {
                 wp_enqueue_style( 'bookingpress-custom-service-duration-front-rtl' ); 
            }
        } 

        function bookingpress_add_service_extra_section_func() {
            ?>

            <div class="bpa-form-row bpa-sm__working-hours bpa-fr__multislot-module">
                <el-row>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <div class="bpa-db-sec-heading">
                            <el-row type="flex" align="middle">
                                <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                    <div class="db-sec-left">
                                        <h2 class="bpa-page-heading"><?php esc_html_e('Custom Duration & Pricing', 'bookingpress-custom-service-duration'); ?></h2>                                    
                                    </div>
                                </el-col>
                                <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                    <div class="bpa-hw-right-btn-group">
                                        <el-button class="bpa-btn bpa-btn--icon-without-box __is-label" @click="openNeedHelper('list_custom_duration', 'custom_duration', 'Custom Service Duration')">
                                            <span class="material-icons-round">help</span>
                                            <?php esc_html_e('Need help?', 'bookingpress-custom-service-duration'); ?>
                                        </el-button>
                                    </div>
                                </el-col>                            
                            </el-row>
                        </div>
                        <div class="bpa-default-card bpa-db-card bpa-db-card-is-full-width-title bpa--multislot-module-card">
                            <template>
                                <div class="bpa-sm__wh-head-row">
                                    <div class="bpa-wh__head-row-title">
                                        <span class="bpa-form-label"><?php esc_html_e( 'Enable Custom Duration and Pricing', 'bookingpress-custom-service-duration' ); ?></span>
                                    </div>
                                    <div class="bpa-wh__head-row-swtich">
                                        <el-switch class="bpa-swtich-control" @change="bookingpress_enable_custom_service_duration"v-model="service.enable_custom_service_duration"></el-switch>
                                    </div>
                                </div>                                
                                <div class="bpa-sm__wh-items bpa-mmc__slot-items">                   
                                   <?php do_action('bookingpress_add_extra_custom_duration_section'); ?>
                                   <el-form ref="service_custom_duration_form" :rules="service_custom_duration_rules" :model="service_custom_duration_form" label-position="top" @submit.native.prevent>
                                        <div class="bpa-mmc__slot-item bpa-mmc__slot-item-no-flex">
                                            <el-row :gutter="32">
                                                <el-col :xs="13" :sm="13" :md="12" :lg="12" :xl="12">
                                                    <el-form-item>
                                                        <template #label>
                                                            <span class="bpa-form-label"><?php esc_html_e( 'Unit Duration:', 'bookingpress-custom-service-duration' ); ?></span>
                                                        </template>
                                                        <el-row :gutter="10">
                                                            <el-col :xs="18" :sm="18" :md="18" :lg="16" :xl="18">
                                                                <el-input-number class="bpa-form-control bpa-form-control--number" :min="1" v-model="service_custom_duration_form.service_duration_val" id="service_duration_val" name="service_duration_val" step-strictly @change="bookingpress_change_duration_action"></el-input-number>
                                                            </el-col>
                                                            <el-col :xs="6" :sm="6" :md="6" :lg="8" :xl="6">
                                                                <el-select class="bpa-form-control" v-model="service_custom_duration_form.service_duration_unit" popper-class="bpa-el-select--is-with-modal bpa-service-number-control-dropdown bpa-el-select--is-sm-modal" @change="bookingpress_change_duration_unit_action">
                                                                    <el-option v-if="service.service_duration_unit != 'd'" key="m" label="<?php esc_html_e( 'Mins', 'bookingpress-custom-service-duration' ); ?>" value="m"></el-option>
                                                                    <el-option v-if="service.service_duration_unit != 'd'" key="h" label="<?php esc_html_e( 'Hours', 'bookingpress-custom-service-duration' ); ?>" value="h"></el-option>
                                                                    <el-option v-if="service.service_duration_unit == 'd'" key="d" label="<?php esc_html_e( 'Days', 'bookingpress-custom-service-duration' ); ?>" value="d"></el-option>
                                                                </el-select>
                                                            </el-col>
                                                        </el-row>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :xs="13" :sm="13" :md="12" :lg="12" :xl="12">
                                                    <el-form-item prop ="service_price">
                                                        <template #label>
                                                            <span class="bpa-form-label"><?php esc_html_e( 'Unit Price:', 'bookingpress-custom-service-duration' ); ?>({{service_price_currency}})</span>
                                                        </template>
                                                        <el-input class="bpa-form-control" @input="isPriceNumberValidate($event)" v-model="service_custom_duration_form.service_price" id="service_price" name="service_price" placeholder="0.00" v-if="price_number_of_decimals != '0'" ></el-input>
                                                        <el-input class="bpa-form-control" @input="isPriceValidateZeroDecimal($event)" v-model="service_custom_duration_form.service_price" id="service_price" name="service_price" placeholder="0" v-else></el-input>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </div>    
                                        <div class="bpa-mmc__slot-item bpa-mmc__slot-item-no-flex">   
                                            <el-row :gutter="32">
                                                <el-col :xs="13" :sm="12" :md="12" :lg="12" :xl="12">
                                                    <el-form-item prop="min_duration">
                                                        <template #label>
                                                            <span class="bpa-form-label"><?php esc_html_e( 'Min Duration:', 'bookingpress-custom-service-duration' ); ?></span>
                                                        </template>
                                                        <el-select class="bpa-form-control" v-model="service_custom_duration_form.min_duration" >
                                                            <el-option  label="<?php esc_html_e( 'Select duration', 'bookingpress-custom-service-duration'); ?>" value="" ></el-option>
                                                            <el-option v-for="(item,index) in max_duration_time_options" :key="item.text" :label="item.text" :value="item.value" v-if="index!=0"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                </el-col>
                                                <el-col :xs="13" :sm="12" :md="12" :lg="12" :xl="12">
                                                    <el-form-item prop="max_duration">
                                                        <template #label>
                                                            <span class="bpa-form-label"><?php esc_html_e( 'Max Duration:', 'bookingpress-custom-service-duration' ); ?></span>
                                                        </template>
                                                        <el-select class="bpa-form-control" v-model="service_custom_duration_form.max_duration" @change="bookingpress_change_service_max_duration">
                                                            <el-option  label="<?php esc_html_e( 'Select duration', 'bookingpress-custom-service-duration' ); ?>" value="" ></el-option>
                                                            <el-option v-for="(item,index) in max_duration_time_options" :key="item.text" :label="item.text" :value="item.value" v-if="index!=0"></el-option>
                                                        </el-select>
                                                    </el-form-item>
                                                </el-col>
                                            </el-row>
                                        </div>
                                        <div class="bpa-mmc__slot-item" v-for="(custom_durations, index) in service.service_custom_duration"> 
                                            <div class="bpa-mmc__slot-item-left">
                                                <el-row :gutter="32">
                                                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                                                        <el-form-item v-if="index > 0">
                                                            <template #label>
                                                                <span class="bpa-form-label"><?php esc_html_e( 'Duration Upto:', 'bookingpress-custom-service-duration' ); ?></span>
                                                            </template>
                                                            <el-select class="bpa-form-control" v-model="custom_durations.service_duration">
                                                                <el-option  label="<?php esc_html_e( 'Select duration', 'bookingpress-custom-service-duration' ); ?>" value="" ></el-option>
                                                                <el-option v-for="(item,index2) in max_duration_time_options" :key="item.text" :label="item.text" :value="item.
                                                                value" v-if="item.value <= service_custom_duration_form.max_duration && index2 !=0"></el-option>
                                                            </el-select>
                                                        </el-form-item>
                                                        <el-form-item v-else>
                                                            <template #label>
                                                                <span class="bpa-form-label"><?php esc_html_e( 'Duration Upto:', 'bookingpress-custom-service-duration' ); ?></span>
                                                            </template>
                                                            <el-select class="bpa-form-control" v-model="custom_durations.service_duration">
                                                                <el-option  label="<?php esc_html_e( 'Select duration', 'bookingpress-custom-service-duration' ); ?>" value="" ></el-option>
                                                                <el-option v-for="(item,index2) in max_duration_time_options" :key="item.text" :label="item.text" :value="item.value" v-if="item.value <= service_custom_duration_form.max_duration && index2 !=0 "></el-option>
                                                            </el-select>
                                                        </el-form-item>
                                                    </el-col>
                                                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                                                        <el-form-item>
                                                            <template #label>
                                                                <span class="bpa-form-label"><?php esc_html_e( 'Price Per Unit:', 'bookingpress-custom-service-duration' ); ?>({{service_price_currency}})</span>													
                                                            </template>
                                                            <el-input class="bpa-form-control" id="service_price" name="service_price" v-model="service.service_custom_duration[index].service_price" placeholder="0.00"></el-input>	
                                                        </el-form-item>
                                                    </el-col>
                                                </el-row>
                                            </div>
                                            <div class="bpa-mmc__slot-item-right" @click="bookingpress_remove_custom_service_duration_period(custom_durations.id)" v-if="index > 0">
                                                <span class="material-icons-round">remove_circle</span>
                                            </div>
                                            <div class="bpa-mmc__slot-item-right" @click='bookingpress_add_custom_service_duration_period' v-else>
                                                <span class="material-icons-round">add_circle</span>
                                            </div>
                                        </div>                                   
                                    </el-form>
                                </div>
                            </template>
                        </div>
                    </el-col>
                </el-row>
            </div>
            <?php
        }

        function bookingpress_add_custom_service_duration_field_func() {
            ?> 

            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="(custom_duration_data, index) in assign_staff_member_details.service_custom_duration" v-else>
                <el-row class="bpa-cda__form-control-row">
                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                        <el-form-item>
                            <template #label>
                                <span class="bpa-form-label"><?php esc_html_e( 'Duration', 'bookingpress-custom-service-duration' ); ?></span>
                            </template>
                            <el-input class="bpa-form-control" v-model="custom_duration_data.service_duration_text" disabled></el-input>                            
                        </el-form-item>												
                    </el-col>
                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                        <el-form-item>
                            <template #label>
                                <span class="bpa-form-label"><?php esc_html_e( 'Price', 'bookingpress-custom-service-duration' ); ?>({{service_price_currency}})</span>
                            </template>
                            <el-input @input="bookingpress_update_input()" v-model="assign_staff_member_details.service_custom_duration[index].service_price" class="bpa-form-control" placeholder="0.00" ></el-input>
                        </el-form-item> 
                    </el-col>
                </el-row>
            </el-col>
            <?php
        }

        function bookingpress_add_service_dynamic_vue_methods_func() {            
            global $bookingpress_notification_duration;

			?>
            bookingpress_add_custom_service_duration_period() {
                const vm = this;
                var ilength = 1;
                if(vm.service.service_custom_duration != '' ) {            
                    ilength = parseInt(vm.service.service_custom_duration.length) + 1;
                }
                let service_duration_data = {};
                Object.assign(service_duration_data, {id: ilength})
                Object.assign(service_duration_data, {service_duration: ''})
                Object.assign(service_duration_data, {service_price: ''})
                vm.service.service_custom_duration.push(service_duration_data);
            },
            bookingpress_remove_custom_service_duration_period(id) {
                const vm = this;
                vm.service.service_custom_duration.forEach(function(item, index, arr) {
                    if(id == item.id ) {
                        vm.service.service_custom_duration.splice(index,1);
                    }
                })
            },
            bookingpress_update_input(){
                const vm = this;
                vm.$forceUpdate();
            },
            bookingpress_change_duration_unit_action(event){
                const vm = this;
                var is_set_duration = vm.bookingpress_check_is_custom_duration_set();                
                if(is_set_duration == true) {
                    var msg = '<?php esc_html_e( 'If you are change the service duration then custom duration will be reset.', 'bookingpress-custom-service-duration' ); ?>';
                    vm.$confirm(msg, 'Warning', {
                    confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-custom-service-duration' ); ?>',
                    cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-custom-service-duration' ); ?>',
                    type: 'warning'
                    }).then(() => {
                        vm.bookingpress_get_time_slot_option();
                        vm.bookingpress_reset_custom_duration();
                        vm.service_custom_duration_form.max_duration = '';
                        vm.service_custom_duration_form.min_duration = '';
                    }).catch(() => {
                        vm.service_custom_duration_form.service_duration_unit = vm.service_custom_duration_form.dummy_service_duration_unit;
                    });
                } else {
                    vm.bookingpress_get_time_slot_option();
                    vm.service_custom_duration_form.max_duration = '';
                    vm.service_custom_duration_form.min_duration = '';
                }    
            },
            bookingpress_change_duration_action(currentValue, oldValue) {                
                const vm = this;
                var is_set_duration = vm.bookingpress_check_is_custom_duration_set();                
                if(is_set_duration == true) {                
                    var msg = '<?php esc_html_e( 'If you are change the service duration then custom duration will be reset.', 'bookingpress-custom-service-duration' ); ?>';
                    vm.$confirm(msg, 'Warning', {
                    confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-custom-service-duration' ); ?>',
                    cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-custom-service-duration' ); ?>',
                    type: 'warning'
                    }).then(() => {
                        vm.bookingpress_get_time_slot_option();
                        vm.bookingpress_reset_custom_duration();
                        vm.service_custom_duration_form.max_duration = '';
                        vm.service_custom_duration_form.min_duration = '';
                    }).catch(() => {
                        vm.service_custom_duration_form.service_duration_val = oldValue;
                    });
                } else {
                    vm.bookingpress_get_time_slot_option();
                    vm.service_custom_duration_form.max_duration = '';
                    vm.service_custom_duration_form.min_duration = '';
                }    
			},
            bookingpress_get_time_slot_option() {
                const vm = this;
                var postdata = [];
                postdata.action = 'bookingpress_get_custom_duration_options';
                postdata.service_duration_unit = vm.service_custom_duration_form.service_duration_unit;
                postdata.service_duration_val = vm.service_custom_duration_form.service_duration_val;
                postdata.main_service_duration_val = vm.service.service_duration_val;

                postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
                axios.post( appoint_ajax_obj.ajax_url, Qs.stringify(postdata))
                .then(function(response) {
                    vm.max_duration_time_options = response.data.service_max_duration;                    
                }).catch(function(error){
                    console.log(error);
                    vm.$notify({
                        title: '<?php esc_html_e( 'Error', 'bookingpress-custom-service-duration' ); ?>',
                        message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-custom-service-duration' ); ?>',
                        type: 'error',
                        customClass: 'error_notification',
                    });
                }); 

            },
            isPriceNumberValidate(evt) {
                const regex = /^(?!.*(,,|,\.|\.,|\.\.))[\d.,]+$/gm;
                let m;
                if((m = regex.exec(evt)) == null ) {
                    this.service_custom_duration_form.service_price = '';
                }
                var price_number_of_decimals = this.price_number_of_decimals;                
                if((evt != null && evt.indexOf(".")>-1 && (evt.split('.')[1].length > price_number_of_decimals))){
                    this.service_custom_duration_form.service_price = evt.slice(0, -1);
                }                
            },
            isPriceValidateZeroDecimal(evt){
                const vm = this;                
                if (/[^0-9]+/.test(evt)){
                    vm.service_custom_duration_form.service_price = evt.slice(0, -1);
                }
            },
            bookingpress_change_service_max_duration(event){
                const vm = this;
                var is_set_duration = vm.bookingpress_check_is_custom_duration_set();                
                if(vm.service_custom_duration_form.dummy_max_duration != '' && is_set_duration == true) {
                    var msg = '<?php esc_html_e( 'If you are change the service duration then custom duration will be reset.', 'bookingpress-custom-service-duration' ); ?>';
                    vm.$confirm(msg, 'Warning', {
                    confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-custom-service-duration' ); ?>',
                    cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-custom-service-duration' ); ?>',
                    type: 'warning'
                    }).then(() => {
                        vm.service_custom_duration_form.dummy_max_duration = event;
                        vm.bookingpress_reset_custom_duration();
                       
                    }).catch(() => {
                        vm.service_custom_duration_form.max_duration = vm.service_custom_duration_form.dummy_max_duration;                        
                    });
                } else {
                    vm.service_custom_duration_form.dummy_max_duration = event;
                }
            },
            bookingpress_reset_custom_duration(){
                const vm = this;
                vm.service.service_custom_duration.forEach(function(item,index,arr){ 
                    vm.service.service_custom_duration[index]['service_duration'] = '';
                });
            },
            bookingpress_check_is_custom_duration_set(){
                const vm = this;
                var is_set = false; 
                vm.service.service_custom_duration.forEach(function(item,index,arr){                     
                    if(item.service_duration != '') {                        
                        is_set = true;
                        return;
                    }
                });
                if(is_set == true) {
                    return true; 
                } else {
                    return false; 
                }                
            },
            bookingpress_enable_custom_service_duration(event){
                const vm = this;
                if(event == false) {                   
                    vm.$refs.service_custom_duration_form.clearValidate();
                }
            },
            <?php
        }

        function bookingpress_modify_service_data_fields_func($bookingpress_services_vue_data_fields) {
       
            $bookingpress_services_vue_data_fields['service']['enable_custom_service_duration'] = false;            
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['max_duration'] = '';
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['min_duration'] = '';
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['dummy_max_duration'] = '';
            $bookingpress_services_vue_data_fields['service']['max_duration'] = '';
            $bookingpress_services_vue_data_fields['service']['min_duration'] = '';
            $service_duration_val = $bookingpress_services_vue_data_fields['service']['service_duration_val']; 
            $service_duration_unit = $bookingpress_services_vue_data_fields['service']['service_duration_unit'];        
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['service_duration_unit'] = $bookingpress_services_vue_data_fields['service']['service_duration_unit'] != 'd' ? $bookingpress_services_vue_data_fields['service']['service_duration_unit'] : '';
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['service_duration_val'] = $bookingpress_services_vue_data_fields['service']['service_duration_val'];
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['dummy_service_duration_unit'] = $bookingpress_services_vue_data_fields['service']['service_duration_unit'];
            $bookingpress_services_vue_data_fields['service_custom_duration_form']['service_price'] = '';
            $max_duration_time_options = $this->bookingpress_get_custom_duration_options_func($service_duration_unit,$service_duration_val);
            $bookingpress_services_vue_data_fields['max_duration_time_options'] = $max_duration_time_options;            
            if(!empty($max_duration_time_options) && isset($max_duration_time_options[0])) {
                unset($max_duration_time_options[0]);
                array_values($max_duration_time_options);
            }
            $bookingpress_services_vue_data_fields['duration_time_options_arr'] = $max_duration_time_options;
            $bookingpress_services_vue_data_fields['service']['service_custom_duration'] = array(
                array(
                    'id' => 1,
                    'service_duration' => '',
                    'service_price' => '',                    
                ),    
            );
            $bookingpress_services_vue_data_fields['service_custom_duration_rules'] = array(
                'service_price'        => array(
                    array(
                        'required' => true,
                        'message'  => esc_html__('Please enter price', 'bookingpress-custom-service-duration'),
                        'trigger'  => 'blur',
                    ),
                ),
                'max_duration'  => array(
                    array(
                        'required' => true,
                        'message'  => esc_html__('Please select the max duration', 'bookingpress-custom-service-duration'),
                        'trigger'  => 'change',
                    ),
                )
            );
            return $bookingpress_services_vue_data_fields;
        }

        function bookingpress_save_service_details( $response, $service_id, $posted_data ) {

            global $bookingpress_services,$BookingPress,$wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_custom_staffmembers_service_durations; 

            $enable_custom_service_duration = ! empty( $posted_data['enable_custom_service_duration'] ) ? $posted_data['enable_custom_service_duration'] : 'false';
            if ( ! empty( $enable_custom_service_duration ) ) {
                $bookingpress_services->bookingpress_add_service_meta( $service_id, 'enable_custom_service_duration', $enable_custom_service_duration );
            }
            $max_duration = ! empty( $posted_data['max_duration'] ) ? intval($posted_data['max_duration']) : '';
            if ( ! empty( $max_duration ) ) {
                $bookingpress_services->bookingpress_add_service_meta( $service_id, 'custom_service_max_duration', $max_duration );
            }
            $min_duration = ! empty( $posted_data['min_duration'] ) ? intval($posted_data['min_duration']) : '';
            $bookingpress_services->bookingpress_add_service_meta( $service_id, 'custom_service_min_duration', $min_duration );
            
            $wpdb->delete( $tbl_bookingpress_custom_service_durations, array( 'bookingpress_service_id' => $service_id));
            $wpdb->delete( $tbl_bookingpress_custom_staffmembers_service_durations, array('bookingpress_service_id' => $service_id));

            $service_custom_durations = ! empty($posted_data['service_custom_duration']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $posted_data['service_custom_duration']) : array(); // phpcs:ignore

            if ( ! empty( $service_custom_durations ) && $enable_custom_service_duration == 'true' ) {           
                foreach ( $service_custom_durations as $key => $val ) {
                    $service_duration = !empty($val['service_duration']) ? intval($val['service_duration']) : 0;
                    $service_price = !empty($val['service_price']) ? floatval($val['service_price']) : 0;  

                    if($service_duration != 0 && $service_price != '') {
                        $bookingpress_db_fields = array(
                            'bookingpress_service_id' => $service_id,
                            'bookingpress_service_duration_val' => $service_duration,
                            'bookingpress_service_duration_price' => $service_price,
                        );
                        $wpdb->insert( $tbl_bookingpress_custom_service_durations, $bookingpress_db_fields );                        
                        $bookingpress_insert_id = $wpdb->get_var( $wpdb->prepare( 'SELECT bookingpress_custom_service_duration_id  FROM ' . $tbl_bookingpress_custom_service_durations . ' WHERE bookingpress_service_id = %d AND bookingpress_service_duration_val = %d', $service_id,$service_duration ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm

                        if(!empty($posted_data['bookingpress_assign_staffmember_data']) ) {

                            foreach($posted_data['bookingpress_assign_staffmember_data'] as $key2 => $val2 ) {
                                $staffmember_custom_services = !empty($val2['staffmember_custom_service']) ? $val2['staffmember_custom_service'] : array();
                                if(!empty($staffmember_custom_services) && isset($staffmember_custom_services[0])) {
                                    unset($staffmember_custom_services[0]);                               
                                }
                                $is_exit = 0;
                                if(!empty($staffmember_custom_services )) {
                                    foreach($staffmember_custom_services as $key3 => $val3) { 
                                        if($val3['service_duration'] == $service_duration ) {
                                            $bookingpress_db_fields_list = array(
                                                'bookingpress_custom_service_duration_id' => $bookingpress_insert_id,
                                                'bookingpress_service_id' => $service_id,
                                                'bookingpress_staffmember_id' => intval($val2['staffmember_id']),
                                                'bookingpress_staffmember_price' => $val3['service_price'],
                                                'bookingpress_staffmember_duration_created_date' => current_time( 'mysql' ),
                                            );
                                            $wpdb->insert( $tbl_bookingpress_custom_staffmembers_service_durations, $bookingpress_db_fields_list );
                                            $is_exit = 1;
                                        }    
                                    } 
                                } 
                                if($is_exit == 0) {
                                    $bookingpress_db_fields_list = array(
                                        'bookingpress_custom_service_duration_id' => $bookingpress_insert_id,
                                        'bookingpress_service_id' => $service_id,
                                        'bookingpress_staffmember_id' => intval($val2['staffmember_id']),
                                        'bookingpress_staffmember_price' => $service_price,
                                        'bookingpress_staffmember_duration_created_date' => current_time( 'mysql' ),
                                    );
                                    $wpdb->insert( $tbl_bookingpress_custom_staffmembers_service_durations, $bookingpress_db_fields_list );  
                                }
                            }
                        }
                    }
                }                      
            }
            return $response;
        }

        function bookingpress_edit_service_more_vue_data_func() {
            ?>
               vm2.service.enable_custom_service_duration =  (response.data.enable_custom_service_duration == 'true') ? true : false;
               vm2.service_custom_duration_form.max_duration = response.data.custom_service_max_duration != 'undefined' && response.data.custom_service_max_duration != null ? parseInt(response.data.custom_service_max_duration) : '' ;

               vm2.service_custom_duration_form.min_duration = response.data.custom_service_min_duration != 'undefined' && response.data.custom_service_min_duration != null && response.data.custom_service_min_duration != '' ? parseInt(response.data.custom_service_min_duration) : '' ;

               vm2.service_custom_duration_form.service_price = vm2.service.service_price
               vm2.service_custom_duration_form.service_duration_val = vm2.service.service_duration_val
               vm2.service_custom_duration_form.service_duration_unit = vm2.service.service_duration_unit;
               vm2.bookingpress_get_time_slot_option()

               vm2.service.service_custom_duration = response.data.service_custom_duration;
            <?php
        }

        function bookingpress_after_reset_add_service_form_func() {
            global $BookingPress,$bookingpress_notification_duration;
            $bookingpress_default_time_duration_data = $BookingPress->bookingpress_get_default_timeslot_data();
            $bookingpress_default_time_duration      = ! empty($bookingpress_default_time_duration_data['time_duration']) ? $bookingpress_default_time_duration_data['time_duration'] : 30;
            $bookingpress_default_time_unit          = ! empty($bookingpress_default_time_duration_data['time_unit']) ? $bookingpress_default_time_duration_data['time_unit'] : 'm';
            ?>
            this.service.enable_custom_service_duration = false;	
            this.service_custom_duration_form.max_duration = '';
            this.service_custom_duration_form.min_duration = '';
            this.service_custom_duration_form.service_duration_val = '<?php echo esc_html($bookingpress_default_time_duration); ?>'
            this.service_custom_duration_form.service_duration_unit = '<?php echo esc_html($bookingpress_default_time_unit); ?>'            
            <?php            
        }

        function bookingpress_get_custom_duration_options_func($service_duration_unit='',$service_duration_val='') {
			$response = array();

            if(!empty($_REQUEST['action']) && sanitize_text_field($_REQUEST['action']) == 'bookingpress_get_custom_duration_options') {
                $bpa_check_authorization = $this->bpa_check_authentication( 'get_service_custom_durations', true, 'bpa_wp_nonce' );            
                if( preg_match( '/error/', $bpa_check_authorization ) ){
                    $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                    $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-custom-service-duration');

                    $response['variant'] = 'error';
                    $response['title'] = esc_html__( 'Error', 'bookingpress-custom-service-duration');
                    $response['msg'] = $bpa_error_msg;

                    wp_send_json( $response );
                    die;
                }
            }            
            $max_duration_time_options = array();
            $service_duration_unit = ! empty( $_REQUEST['service_duration_unit'] ) ? sanitize_text_field( $_REQUEST['service_duration_unit'] ) : $service_duration_unit;
            $service_duration_val = ! empty( $_REQUEST['service_duration_val'] ) ? sanitize_text_field( $_REQUEST['service_duration_val'] ) : $service_duration_val;

            if(!empty($service_duration_unit)  && !empty($service_duration_val)) {  
                if($service_duration_unit == 'h' ) {
                    $service_duration_val = $service_duration_val * 60 ; 
                }
                $service_duration_step = $service_duration_val;
                if($service_duration_unit != 'd') {
                    while( $service_duration_val <= 1440 ) {                        
                        if($service_duration_val <= 1440) {                            
                            $bookingpress_service_duration = '';
                            $min = $hours = 0;
                            if(60 <= $service_duration_val)  {
                                $hours = floor($service_duration_val / 60);                
                                $min = $service_duration_val - ($hours * 60);    
                            } else {
                                $min = $service_duration_val;
                            }                            
                            if($hours > 0){
                                if($hours == 1){
                                    $bookingpress_service_duration .= $hours.' '.__('Hour', 'bookingpress-custom-service-duration').' ';
                                }
                                else{
                                    $bookingpress_service_duration .= $hours.' '.__('Hours', 'bookingpress-custom-service-duration').' ';
                                }
                            }
                            if($min > 0){
                                if($min == 1){
                                    $bookingpress_service_duration .= $min.' '.__('Min', 'bookingpress-custom-service-duration');
                                }
                                else{
                                    $bookingpress_service_duration .= $min.' '.__('Mins', 'bookingpress-custom-service-duration');
                                }
                            }

                            $max_duration_time_options[] =  array(
                                'text'  => $bookingpress_service_duration,
                                'value' => $service_duration_val,
                            );
                            $service_duration_val += $service_duration_step;
                        }                
                    }                   
                } else {
                    while( $service_duration_val <= 30 ) {    
                        $bookingpress_service_duration = '';                            
                        $bookingpress_service_duration .= $service_duration_val.' '.__('Days', 'bookingpress-custom-service-duration');
                        $max_duration_time_options[] =  array(
                            'text'  => $bookingpress_service_duration,
                            'value' => $service_duration_val * 24 * 60,
                        );
                        $service_duration_val += $service_duration_step;
                    }
                }                
            }
            if(!empty($_REQUEST['action']) && sanitize_text_field($_REQUEST['action']) == 'bookingpress_get_custom_duration_options') {
                $response['service_max_duration'] = $max_duration_time_options;
                wp_send_json( $response );
                die;
            } else {
                return $max_duration_time_options;
            }                        
        }

        function bookingpress_before_save_service_validation_func() {
            global $bookingpress_notification_duration ;
            ?>
            if(vm2.service.enable_custom_service_duration == true){
                var valid_data = false;
                if(vm2.service_custom_duration_form.service_duration_unit == '') {
                    error_msg= '<?php esc_html_e( 'Please select unit duration', 'bookingpress-custom-service-duration' ); ?>';
                } else {
                    vm2.$refs["service_custom_duration_form"].validate((valid) => {
                        if (valid) {
                            vm2.service.service_price = vm2.service_custom_duration_form.service_price;
                            vm2.service.service_duration_val = vm2.service_custom_duration_form.service_duration_val; 
                            vm2.service.service_duration_unit = vm2.service_custom_duration_form.service_duration_unit;                         
                            vm2.service.max_duration = vm2.service_custom_duration_form.max_duration;
                            vm2.service.min_duration = vm2.service_custom_duration_form.min_duration;
                            valid_data = true;                        
                        } 
                    });
                    if(valid_data == false) {
                        return false;
                    }

                    var valid_duration = true;
                    var error_msg = '';
                    let service_duration_data = [];

                    if(vm2.service.service_custom_duration != '') {
                        vm2.service.service_custom_duration.forEach(function(currentVal,index,arr){                      
                            if(error_msg == '') {
                                if(service_duration_data == "" || ( service_duration_data != "" && service_duration_data.indexOf(currentVal.service_duration) == '-1')) {
                                    service_duration_data.push(currentVal.service_duration);
                                } else {
                                    error_msg = '<?php esc_html_e( 'Same custom duration not allowed please set proper custom duration', 'bookingpress-custom-service-duration' ); ?>';
                                }   
                                if(vm2.service_custom_duration_form.max_duration < currentVal.service_duration) {
                                    error_msg= '<?php esc_html_e( 'service custom duration not greater than max duration', 'bookingpress-custom-service-duration' ); ?>';
                                }
                                if(currentVal.service_duration != '' && currentVal.service_price == '') {
                                    error_msg = '<?php esc_html_e( 'Please input the cutom service duration price...', 'bookingpress-custom-service-duration' ); ?>';
                                }
                            } 
                        });
                    }
                }
                if(error_msg != '') {
                    vm.$notify({
                        title: '<?php esc_html_e( 'Error', 'bookingpress-custom-service-duration' ); ?>',
                        message: error_msg ,
                        type: 'error',
                        customClass: 'error_notification',
                        duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                    });
                    return false;
                }
            }
            <?php
        }

        function bookingpress_modify_edit_service_data_func($response,$service_id) {
            global $wpdb,$tbl_bookingpress_custom_service_durations;

            $bookingpress_custom_service_durations = $wpdb->get_results( $wpdb->prepare( 'SELECT bookingpress_service_duration_val,bookingpress_service_duration_price FROM ' . $tbl_bookingpress_custom_service_durations . ' WHERE bookingpress_service_id = %d ORDER BY bookingpress_service_duration_val ASC', $service_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm
            $custom_service_durations = array();

            if ( ! empty( $bookingpress_custom_service_durations ) ) {

                foreach ( $bookingpress_custom_service_durations as $key => $val ) {
                    $service_durations = array();
                    $i = 1;
                    $bookingpress_service_duration_val= ! empty( $val['bookingpress_service_duration_val'] ) ? intval( $val
                    ['bookingpress_service_duration_val'] ) : '';
                    $bookingpress_service_duration_price= isset( $val['bookingpress_service_duration_price'] ) && $val['bookingpress_service_duration_price'] != '' ? floatval( $val
                    ['bookingpress_service_duration_price'] ) : '';
                    $service_durations['id'] = $i;
                    $service_durations['service_duration'] = $bookingpress_service_duration_val;
                    $service_durations['service_price'] = $bookingpress_service_duration_price;
                    $custom_service_durations[] = $service_durations;
                    $i++;
                }
            }
            if(empty($custom_service_durations)) {
                $custom_service_durations[] = array(
                    'id' => 1,
                    'service_duration' => '',
                    'service_price' => '',                    
                );
            } 

            $response['service_custom_duration'] = $custom_service_durations;
            return $response;
        }
        
        function bookingpress_add_staff_custom_service_duration_field_func() {
            ?>
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="(custom_duration_data, index) in assign_service_form.bookingpress_custom_durations_data" v-else>
                <el-row class="bpa-cda__form-control-row">
                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                        <el-form-item>
                            <template #label>
                                <span class="bpa-form-label"><?php esc_html_e( 'Duration', 'bookingpress-custom-service-duration' ); ?></span>
                            </template>
                            <el-input v-model="assign_service_form.bookingpress_custom_durations_data[index].staff_duration_text" class="bpa-form-control" disabled ></el-input>
                        </el-form-item>												
                    </el-col>
                    <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                        <el-form-item>
                            <template #label>
                                <span class="bpa-form-label"><?php esc_html_e( 'Price', 'bookingpress-custom-service-duration' ); ?>({{bookingpress_currency}})</span>
                            </template>
                            <el-input v-model="assign_service_form.bookingpress_custom_durations_data[index].staff_service_price" class="bpa-form-control" placeholder="0.00" ></el-input>
                        </el-form-item> 
                    </el-col>
                </el-row>
            </el-col>
            <?php
        }

        function bookingpress_get_duration_text_using_minute($service_duration_val,$service_duration_unit) {
            $service_duration_text = '';
            if($service_duration_unit != 'd') {                
                $hours = 0;
                if($service_duration_val >= 60) { 
                    $hours = floor($service_duration_val / 60);                
                    $min = $service_duration_val - ($hours * 60);
                } else {
                    $min = $service_duration_val;
                }
                if($hours > 0){
                    if($hours == 1 ){
                        $service_duration_text .= $hours.' '.__('Hour', 'bookingpress-custom-service-duration').' ';
                    }
                    else{
                        $service_duration_text .= $hours.' '.__('Hours', 'bookingpress-custom-service-duration').' ';
                    }
                }
                if($min > 0){
                    if($min == 1){
                        $service_duration_text .= $min.' '.__('Min', 'bookingpress-custom-service-duration');
                    }else{
                        $service_duration_text .= $min.' '.__('Mins', 'bookingpress-custom-service-duration');
                    }
                }
            } else {
                $service_duration_val  = ($service_duration_val/(60*24));
                $service_duration_text = $service_duration_val.' '.__('Days', 'bookingpress-custom-service-duration');                                    
            }
            return $service_duration_text;
        } 

        function bookingpress_staff_member_vue_dynamic_data_fields_func($bookingpress_staff_member_vue_data_fields) {
            global $wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_services,$BookingPress,$tbl_bookingpress_staffmembers_services,$bookingpress_services;
            /*
            $bookingpress_custom_service_durations = $wpdb->get_results( 'SELECT bookingpress_service_id,bookingpress_service_duration_val,bookingpress_service_duration_price,bookingpress_custom_service_duration_id FROM ' . $tbl_bookingpress_custom_service_durations.' ORDER BY bookingpress_service_duration_val ASC', ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm
            */

            $bookingpress_custom_durations = array();            
            $bookingpress_services_data = $wpdb->get_results('SELECT * FROM ' . $tbl_bookingpress_services, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm

            foreach($bookingpress_services_data as $key2 => $val2) { 

                $bookingpress_service_id = $bookingpress_service_id = intval($val2['bookingpress_service_id']);
                $service_duration_unit = $val2['bookingpress_service_duration_unit'];

                $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');

                if(!empty($enable_custom_service_duration) && $enable_custom_service_duration == 'true') {

                    $bookingpress_custom_service_durations = $wpdb->get_results($wpdb->prepare('SELECT bookingpress_service_id,bookingpress_service_duration_val,bookingpress_service_duration_price,bookingpress_custom_service_duration_id FROM ' . $tbl_bookingpress_custom_service_durations.' WHERE bookingpress_service_id = %d ORDER BY bookingpress_service_duration_val ASC',$bookingpress_service_id),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm

                    $bookingpress_services_duration_val = !empty($val2['bookingpress_service_duration_val']) ? intval($val2['bookingpress_service_duration_val']) : '';
                    $bookingpress_service_price = isset($val2['bookingpress_service_price']) ? $val2['bookingpress_service_price'] : '';                    
                    if($service_duration_unit == 'h') {
                        $bookingpress_services_duration_val = $bookingpress_services_duration_val * 60;
                    } elseif($service_duration_unit == 'd') {
                        $bookingpress_services_duration_val = $bookingpress_services_duration_val * 60 * 24 ;
                    }
                    $service_duration_text = $this->bookingpress_get_duration_text_using_minute($bookingpress_services_duration_val,$service_duration_unit);
                    $bookingpress_custom_durations[$bookingpress_service_id][] = array(
                        'staff_service_price'=> $bookingpress_service_price,
                        'staff_service_duration' => $bookingpress_services_duration_val,
                        'staff_duration_text' => $service_duration_text,
                        'bookingpress_custom_service_duration_id' => '',
                    );  
                    
                    if(!empty($bookingpress_custom_service_durations)) {
                        foreach($bookingpress_custom_service_durations as $key => $val) {
                            $bookingpress_service_duration_val = !empty($val['bookingpress_service_duration_val']) ? intval($val['bookingpress_service_duration_val']) : '';
                            $bookingpress_service_price = isset($val['bookingpress_service_duration_price']) ? $val['bookingpress_service_duration_price'] : '';
                            $service_duration_text = $this->bookingpress_get_duration_text_using_minute($bookingpress_services_duration_val,$service_duration_unit);
                            
                            /*
                            if($service_duration_unit == 'h') {
                                //$bookingpress_service_duration_val = $bookingpress_service_duration_val * 60;
                            } elseif($service_duration_unit == 'd') {
                                //$bookingpress_service_duration_val = $bookingpress_service_duration_val * 60 * 24 ;
                            }
                            */

                            $service_duration_text = $this->bookingpress_get_duration_text_using_minute($bookingpress_service_duration_val,$service_duration_unit);
                            $bookingpress_custom_durations[$bookingpress_service_id][] = array(
                                'staff_service_price'=> floatval($bookingpress_service_price),
                                'staff_service_duration' => $bookingpress_service_duration_val,
                                'staff_duration_text' => $service_duration_text,
                                'bookingpress_custom_service_duration_id' => $val['bookingpress_custom_service_duration_id'],
                            );                            
                        }                    
                    }                  
                }
            }

            $bookingpress_staff_member_vue_data_fields['bookingpress_custom_durations'] = $bookingpress_custom_durations;   
            $bookingpress_staff_member_vue_data_fields['assign_service_form']['bookingpress_custom_durations_data'] = array() ;
            $bookingpress_staff_member_vue_data_fields['is_active_service_custom_duration']  = $this->is_addon_activated();

            return $bookingpress_staff_member_vue_data_fields;
        }

        function bookingpress_assign_custom_services_func() {
            ?>      
            vm.is_display_default_price_field = true;                  
            for(x in vm.bookingpress_custom_durations){
                if(x == selected_value) {
                    vm.assign_service_form.bookingpress_custom_durations_data = vm.bookingpress_custom_durations[x];
                    vm.is_display_default_price_field = false;
                } 
            };
            <?php
        }

        function bookingpress_staff_members_save_external_details_func($response) {
            global $wpdb,$tbl_bookingpress_custom_staffmembers_service_durations,$BookingPress; 

            if(!empty($_POST['bookingpress_action']) && $_POST['bookingpress_action'] != 'bookingpress_shift_managment' ) { // phpcs:ignore 

                $staffmember_id = !empty($response['staffmember_id']) ? intval($response['staffmember_id']) :'';
                $wpdb->delete(
                    $tbl_bookingpress_custom_staffmembers_service_durations,
                    array(
                        'bookingpress_staffmember_id' => $staffmember_id,
                    )
                );

                if ( ! empty( $_REQUEST['service_details']['assigned_service_list'] ) ) {
                    $bookingpress_assigned_service_list = ! empty( $_REQUEST['service_details']['assigned_service_list'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['service_details']['assigned_service_list'] ) : array();// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_POST contains mixed array and will be sanitized using 'appointment_sanatize_field' function                
                    foreach( $bookingpress_assigned_service_list as $bookingpress_service_key => $bookingpress_service_val ) {
                        $bookingpress_service_id = $bookingpress_service_val['assign_service_id'];
                        if(!empty($bookingpress_service_val['bookingpress_custom_durations_data'])) { 
                            $bookingpress_custom_durations_data = $bookingpress_service_val['bookingpress_custom_durations_data'];
                            if(!empty($bookingpress_custom_durations_data) && isset($bookingpress_custom_durations_data[0])) {
                                unset($bookingpress_custom_durations_data[0]);
                            }
                            if(!empty($bookingpress_custom_durations_data)) {
                                foreach ( $bookingpress_custom_durations_data as $key => $value ) {
                                    $bookingpress_db_fields_list = array(
                                        'bookingpress_custom_service_duration_id' => $value['bookingpress_custom_service_duration_id'],
                                        'bookingpress_service_id' => $bookingpress_service_id,
                                        'bookingpress_staffmember_id' => $staffmember_id,
                                        'bookingpress_staffmember_price' => $value['staff_service_price'],
                                        'bookingpress_staffmember_duration_created_date' => current_time( 'mysql' ),
                                    );
                                    $wpdb->insert( $tbl_bookingpress_custom_staffmembers_service_durations, $bookingpress_db_fields_list );                  
                                }  
                            }
                        }
                    }                                
                }
            }    
            return $response;            
        }

        function bookignpress_get_assigned_service_data_filter_func($staff_service_data){

            

            global $wpdb,$tbl_bookingpress_custom_staffmembers_service_durations,$tbl_bookingpress_services,$tbl_bookingpress_custom_service_durations,$BookingPress;

            $service_id = !empty($staff_service_data['assign_service_id']) ? intval($staff_service_data['assign_service_id']) : 0 ;
            $staffmember_id = ! empty( $_REQUEST['staff_member_id'] ) ? intval( $_REQUEST['staff_member_id'] ) : '';            
            

            if(!empty($service_id) && !empty($staffmember_id)) {

                $bookingpress_custom_service_durations = $wpdb->get_results( $wpdb->prepare( 'SELECT sd.bookingpress_service_duration_val,ssd.bookingpress_custom_service_duration_id,ssd.bookingpress_staffmember_price,ssd.bookingpress_service_id FROM ' . $tbl_bookingpress_custom_staffmembers_service_durations . ' as ssd LEFT JOIN '.$tbl_bookingpress_custom_service_durations.' as sd ON ssd.bookingpress_custom_service_duration_id = sd.bookingpress_custom_service_duration_id WHERE ssd.bookingpress_service_id = %d AND ssd.bookingpress_staffmember_id = %d ORDER BY sd.bookingpress_service_duration_val ASC', $service_id,$staffmember_id ),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_staffmembers_service_durations is a table name. false alarm

                $bookingpress_services = $wpdb->get_row($wpdb->prepare('SELECT bookingpress_service_duration_unit,bookingpress_service_duration_val FROM ' . $tbl_bookingpress_services.' WHERE bookingpress_service_id = %d',$service_id),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_services is a table name. false
                
                $bookingpress_service_duration_unit = $bookingpress_services['bookingpress_service_duration_unit'];
                $bookingpress_service_duration_value = $bookingpress_services['bookingpress_service_duration_val'];

                if($bookingpress_service_duration_unit == 'h' ) {
                    $bookingpress_service_duration_value = $bookingpress_service_duration_value * 60; 
                } else if($bookingpress_service_duration_unit == 'd'){
                   $bookingpress_service_duration_value = $bookingpress_service_duration_value * 60 * 24; 
                }
                $service_duration_text = $this->bookingpress_get_duration_text_using_minute($bookingpress_service_duration_value,$bookingpress_service_duration_unit);
                $staff_service_data['staff_duration_text'] = $service_duration_text;
                $bookingpress_custom_durations_data = array(); 
                $bookingpress_custom_durations_data[] = array(
                    'staff_service_price'=> $staff_service_data['assign_service_price'],
                    'staff_service_duration' => intval($bookingpress_service_duration_value),
                    'staff_duration_text' => $service_duration_text,
                    'staff_service_formatted_price' =>  $BookingPress->bookingpress_price_formatter_with_currency_symbol($staff_service_data['assign_service_price']),
                    'bookingpress_custom_service_duration_id' => 0,
                );
           
                if(!empty($bookingpress_custom_service_durations)) {
                    foreach($bookingpress_custom_service_durations as $key => $val ) {
                        $bookingpress_custom_service_duration_id = !empty($val['bookingpress_custom_service_duration_id']) ? intval($val['bookingpress_custom_service_duration_id']) : '';
                        $bookingpress_service_duration_val = intval($val['bookingpress_service_duration_val']);
                        $service_duration_text = $this->bookingpress_get_duration_text_using_minute($bookingpress_service_duration_val,$bookingpress_service_duration_unit);           
                        $bookingpress_custom_durations_data[] = array(
                            'staff_service_price'=> $val['bookingpress_staffmember_price'],
                            'staff_service_duration' => intval($bookingpress_service_duration_val),
                            'staff_duration_text' => $service_duration_text,
                            'staff_service_formatted_price' =>  $BookingPress->bookingpress_price_formatter_with_currency_symbol($val['bookingpress_staffmember_price']),
                            'bookingpress_custom_service_duration_id' =>$bookingpress_custom_service_duration_id,
                        ); 
                    }
                    $staff_service_data['bookingpress_custom_durations_data'] = $bookingpress_custom_durations_data;
                } else {
                    $staff_service_data['bookingpress_custom_durations_data'] = array();
                }
            }
            return $staff_service_data;
        }

        function bookingpress_modify_staffmember_service_data_func($bookingpress_assigned_staffmembers_details,$bookingpress_staffmember_val) {

            global $BookingPress,$wpdb,$tbl_bookingpress_custom_staffmembers_service_durations,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_services;

            $bookingpress_staffmember_id    = !empty($bookingpress_staffmember_val['bookingpress_staffmember_id']) ? intval($bookingpress_staffmember_val['bookingpress_staffmember_id']) : 0;
            $bookingpress_service_id = ! empty( $_REQUEST['service_id'] ) ? intval( $_REQUEST['service_id'] ) : 0;
            $custom_services = array();
            $staffmember_price = ! empty( $bookingpress_assigned_staffmembers_details['staffmember_price'] ) ? ( $bookingpress_assigned_staffmembers_details['staffmember_price'] ) : 0;

            $service_duration_data = $wpdb->get_row( $wpdb->prepare( 'SELECT bookingpress_service_duration_val,bookingpress_service_duration_unit FROM ' . $tbl_bookingpress_services . ' WHERE bookingpress_service_id = %d', $bookingpress_service_id ),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm
            $service_duration = !empty($service_duration_data['bookingpress_service_duration_val']) ? intval($service_duration_data['bookingpress_service_duration_val']) : '';
            if($service_duration_data['bookingpress_service_duration_unit'] == 'd') {                
                $service_duration = $service_duration * 60 * 24;
            } elseif($service_duration_data['bookingpress_service_duration_unit'] == 'h') {
                $service_duration = $service_duration * 60;
            }
            $bookingpress_assigned_staffmembers_details['staff_duration_text'] = $this->bookingpress_get_duration_text_using_minute($service_duration,$service_duration_data['bookingpress_service_duration_unit']);
            $custom_services[] = array(
                'id' => 0,
                'service_duration' => $service_duration,
                'service_price' => $staffmember_price,
                'service_formatted_price' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($staffmember_price),
            );
            if(!empty($bookingpress_staffmember_id) && !empty($bookingpress_service_id)) {

                $staffmember_custom_services = $wpdb->get_results( $wpdb->prepare( 'SELECT sd.bookingpress_service_duration_val,ssd.bookingpress_custom_service_duration_id,ssd.bookingpress_staffmember_price FROM ' . $tbl_bookingpress_custom_staffmembers_service_durations . ' as ssd LEFT JOIN '.$tbl_bookingpress_custom_service_durations.' as sd ON ssd.bookingpress_custom_service_duration_id = sd.bookingpress_custom_service_duration_id WHERE ssd.bookingpress_service_id = %d AND ssd.bookingpress_staffmember_id = %d ORDER BY sd.bookingpress_service_duration_val ASC', $bookingpress_service_id,$bookingpress_staffmember_id ),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_staffmembers_service_durations is a table name. false alarm

                if(!empty($staffmember_custom_services)) {						
                    $i = 1;                   
                    foreach($staffmember_custom_services as $key => $val) {
                        $bookingpress_custom_service_duration_id = intval($val['bookingpress_custom_service_duration_id']);
                        $service_duration = intval($val['bookingpress_service_duration_val']);
                        $custom_services[] = array(
                            'id' => $i,
                            'service_duration' => intval($service_duration),
                            'service_price' => $val['bookingpress_staffmember_price'],
                            'service_formatted_price' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($val['bookingpress_staffmember_price']),
                        );
                        $i++;
                    }
                }                
            }
            $bookingpress_assigned_staffmembers_details['staffmember_custom_service'] = $custom_services;
            return $bookingpress_assigned_staffmembers_details;
        }

        function bookingpress_after_open_add_service_model_func() {
            ?>
            if(action == 'add') {
                let service_duration_data = {};
                Object.assign(service_duration_data, {id: 1})
                Object.assign(service_duration_data, {service_duration: ''})
                Object.assign(service_duration_data, {service_price: ''})
                vm.service.service_custom_duration = [];
                vm.service.service_custom_duration.push(service_duration_data);
                vm.service_custom_duration_form.service_price = '';
            }
            
            <?php
        }

        function bookingpress_before_save_assign_staffmember_data() {
            global $bookingpress_notification_duration;
            ?>
            if(vm.assign_staff_member_details.service_custom_duration != 'undefined' && vm.service.enable_custom_service_duration !== 'undefined' && vm.service.enable_custom_service_duration == true ) {                

                var is_valid = true;
                var is_duplicate_validation = true;
                let service_duration_data = [];
                vm.assign_staff_member_details.service_custom_duration.forEach(function(currentValue, index, arr){

                    if(is_duplicate_validation == true && (service_duration_data == "" || ( service_duration_data != "" && service_duration_data.indexOf(currentValue.service_duration) == '-1'))) {
                        service_duration_data.push(currentValue.service_duration);
                    } else {
                        is_duplicate_validation = false;
                    }
                    if(currentValue.service_price == '') {
                        is_valid = false
                    }
                });
                if(is_valid == true && valid == false) {
                    valid = true
                }
                if(vm.assign_staff_member_details.assigned_staffmember_id == '' || vm.assign_staff_member_details.assigned_staffmember_max_capacity == undefined) {
                    valid = false;
                }
                if(is_duplicate_validation == false) {
                    vm.$notify({
						title: '<?php esc_html_e( 'Error', 'bookingpress-custom-service-duration' ); ?>',
						message: '<?php esc_html_e( 'Same custom duration not allowed please set proper custom duration', 'bookingpress-custom-service-duration' ); ?>',
						type: 'error',
						customClass: 'error_notification',
						duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
					});
                    return false;
                }
            }
            <?php
        }

        function bookingpress_after_open_assign_staffmember_model_func() {
            ?>
            let bkp_service_custom_duration = [];
            vm.service_custom_duration_form.service_duration_val = parseInt(vm.service_custom_duration_form.service_duration_val);   
            var service_duration_val = vm.service_custom_duration_form.service_duration_val;
            if(vm.service_custom_duration_form.service_duration_unit == 'h') {
                service_duration_val = service_duration_val * 60;
            } else if(vm.service_custom_duration_form.service_duration_unit == 'd') {
                service_duration_val = service_duration_val * 60 * 24;
            }
            var staff_service_duration_text = '';
            if(vm.max_duration_time_options != '' && typeof vm.max_duration_time_options !== 'undefined'){
            vm.max_duration_time_options.forEach(function(item2,index2,arr2) {
                if(service_duration_val == item2.value) {
                    staff_service_duration_text = item2.text;
                }
            });
            bkp_service_custom_duration[0] = {
                id:0,
                service_duration: service_duration_val,
                service_price: vm.service_custom_duration_form.service_price,
                service_duration_text : staff_service_duration_text,
            };
            let n = 1;
            let service_duration_text =  '';
            for( let x in vm.service.service_custom_duration ){
                let current_obj = vm.service.service_custom_duration[x];
                if(current_obj.service_duration != '') {
                    vm.max_duration_time_options.forEach(function(item2,index2,arr2) {
                        if( current_obj.service_duration == item2.value) {
                            service_duration_text = item2.text;
                        }
                    });
                    bkp_service_custom_duration[n] = {
                        id: current_obj.id,
                        service_duration: current_obj.service_duration,
                        service_price: current_obj.service_price,
                        service_duration_text : service_duration_text,
                    };
                    n++;                    
                }   
            }
            vm.assign_staff_member_details.service_custom_duration = bkp_service_custom_duration;            
            }
            <?php
        }
        function bookingpress_after_delete_service_func( $service_id ) {
			global $wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_custom_staffmembers_service_durations;
			$wpdb->delete( $tbl_bookingpress_custom_service_durations, array( 'bookingpress_service_id' => $service_id ), array( '%d' ) );
            $wpdb->delete( $tbl_bookingpress_custom_staffmembers_service_durations, array( 'bookingpress_service_id' => $service_id ), array( '%d' ) );
		}       

        function bookingpress_add_custom_duration_section_front_side_func() {
            ?>
            <div v-if="typeof appointment_step_form_data.enable_custom_service_duration !== 'undefined' && ( appointment_step_form_data.enable_custom_service_duration == 'true'|| appointment_step_form_data.enable_custom_service_duration == true)">
                <div class="bpa-front-loader-container" v-if="is_display_custom_duration_loader == true">
                    <div class="bpa-front-loader">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" width="256" height="256" viewBox="0 0 256 256" style="width:100%;height:100%">
                            <defs>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_1_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z " to="M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keyTimes="0;0.5037594;0.5263158;0.5789474;0.6691729;0.6992481;0.7593985;0.7669173;1" values="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M303.49 386.7 C303.49,386.7 284.88,386.7 284.88,386.7 C284.88,386.7 284.88,402.72 284.88,402.72 C284.88,402.72 293.41,402.87 293.41,402.87 C293.41,402.87 293.07,405.24 293.07,405.24 C293.07,405.24 296.63,405.24 296.63,405.24 C296.63,405.24 296.82,402.57 296.82,402.57 C296.82,402.57 304.49,401.98 304.49,401.98 C304.49,401.98 303.49,386.7 303.49,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,398.12 265.56,398.12 C265.56,398.12 266.75,407.02 266.75,407.02 C266.75,407.02 294.78,405.83 294.78,405.83 C294.78,405.83 298.34,405.83 298.34,405.83 C298.34,405.83 332.75,406.72 332.75,406.72 C332.75,406.72 332.45,399.46 332.45,399.46 C332.45,399.46 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,442.32 265.56,442.32 C265.56,442.32 266.75,448.4 266.75,448.4 C266.75,448.4 283.8,447.51 283.8,447.51 C283.8,447.51 312.06,447.21 312.06,447.21 C312.06,447.21 332.75,448.1 332.75,448.1 C332.75,448.1 332.45,443.65 332.45,443.65 C332.45,443.65 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.86,453.14 265.86,453.14 C265.86,453.14 276.98,456.11 276.98,456.11 C276.98,456.11 277.28,447.51 277.28,447.51 C277.28,447.51 319.47,447.81 319.47,447.81 C319.47,447.81 318.81,456.11 318.81,456.11 C318.81,456.11 329.63,454.92 329.63,454.92 C329.63,454.92 330.97,386.7 330.97,386.7z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.63,448.83 295.63,448.83 C295.63,448.83 295.71,448.75 295.71,448.75 C295.71,448.75 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_1_C_0">
                                    <path id="_R_G_L_1_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_0_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z " to="M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keyTimes="0;0.1804511;0.2180451;0.2481203;0.2631579;0.2706767;0.2781955;0.2857143;0.3157895;0.3308271;0.3533835;0.3834586;0.406015;0.4135338;0.4210526;0.4511278;0.4736842;0.4887218;0.4962406;1" values="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M310.92 429.74 C310.92,429.74 310.97,429.75 310.97,429.75 C310.97,429.75 310.93,429.74 310.93,429.74 C310.93,429.74 310.91,429.77 310.91,429.77 C310.91,429.77 310.94,429.77 310.94,429.77 C310.94,429.77 310.99,429.77 310.99,429.77 C310.99,429.77 311.09,429.7 311.09,429.7 C311.09,429.7 310.99,429.73 310.99,429.73 C310.99,429.73 310.9,434.91 310.9,434.91 C310.9,434.91 312.25,433.8 312.25,433.8 C312.25,433.8 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 303.93,428.18 303.93,428.18 C303.93,428.18 303.66,428.14 303.66,428.14 C303.66,428.14 303.84,428.16 303.84,428.16 C303.84,428.16 303.52,428.11 303.52,428.11 C303.52,428.11 303.67,428.12 303.67,428.12 C303.67,428.12 303.58,428.1 303.58,428.1 C303.58,428.1 303.49,428.3 303.49,428.11 C303.49,427.93 303.63,428.09 303.63,428.09 C303.63,428.09 303.45,428.1 303.45,428.1 C303.45,428.1 303.76,428.04 303.76,428.04 C303.76,428.04 303.73,428 303.73,428 C303.73,428 303.69,427.98 303.69,427.98 C303.69,427.98 303.71,428.13 303.71,428.13 C303.71,428.13 303.76,428.08 303.76,428.08 C303.76,428.08 303.8,428.06 303.8,428.06 C303.8,428.06 303.8,428.11 303.8,428.11 C303.8,428.11 303.58,428.16 303.58,428.16 C303.58,428.16 310.92,429.75 310.92,429.75 C310.92,429.75 310.91,429.75 310.91,429.75 C310.91,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.9,429.75 310.9,429.75 C310.9,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.92,429.74 310.92,429.74z ;M299.65 434.12 C299.65,434.12 299.7,434.13 299.7,434.13 C299.7,434.13 299.66,434.11 299.66,434.11 C299.66,434.11 299.64,434.14 299.64,434.14 C299.64,434.14 299.66,434.14 299.66,434.14 C299.66,434.14 299.72,434.15 299.72,434.15 C299.72,434.15 299.81,434.08 299.81,434.08 C299.81,434.08 299.72,434.11 299.72,434.11 C299.72,434.11 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 300.06,430.31 300.06,430.31 C300.06,430.31 299.78,430.27 299.78,430.27 C299.78,430.27 299.96,430.29 299.96,430.29 C299.96,430.29 299.65,430.25 299.65,430.25 C299.65,430.25 299.8,430.25 299.8,430.25 C299.8,430.25 299.7,430.24 299.7,430.24 C299.7,430.24 299.61,430.43 299.61,430.25 C299.61,430.06 299.75,430.22 299.75,430.22 C299.75,430.22 299.57,430.23 299.57,430.23 C299.57,430.23 299.89,430.17 299.89,430.17 C299.89,430.17 299.85,430.13 299.85,430.13 C299.85,430.13 299.82,430.12 299.82,430.12 C299.82,430.12 299.83,430.26 299.83,430.26 C299.83,430.26 299.89,430.21 299.89,430.21 C299.89,430.21 299.93,430.19 299.93,430.19 C299.93,430.19 299.93,430.25 299.93,430.25 C299.93,430.25 299.7,430.29 299.7,430.29 C299.7,430.29 299.65,434.13 299.65,434.13 C299.65,434.13 299.64,434.13 299.64,434.13 C299.64,434.13 299.66,434.13 299.66,434.13 C299.66,434.13 299.63,434.13 299.63,434.13 C299.63,434.13 299.65,434.13 299.65,434.13 C299.65,434.13 299.65,434.12 299.65,434.12z ;M292.83 434.12 C292.83,434.12 292.81,434.11 292.81,434.11 C292.81,434.11 292.84,434.12 292.84,434.12 C292.84,434.12 292.82,434.15 292.82,434.15 C292.82,434.15 292.85,434.15 292.85,434.15 C292.85,434.15 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 292.89,430.31 292.89,430.31 C292.89,430.31 292.69,430.25 292.69,430.25 C292.69,430.25 292.72,430.28 292.72,430.28 C292.72,430.28 292.63,430.26 292.63,430.26 C292.63,430.26 292.65,430.43 292.65,430.25 C292.65,430.06 292.56,430.15 292.56,430.15 C292.56,430.15 292.61,430.23 292.61,430.23 C292.61,430.23 292.93,430.17 292.93,430.17 C292.93,430.17 292.89,430.13 292.89,430.13 C292.89,430.13 292.85,430.12 292.85,430.12 C292.85,430.12 292.87,430.26 292.87,430.26 C292.87,430.26 292.93,430.21 292.93,430.21 C292.93,430.21 292.96,430.19 292.96,430.19 C292.96,430.19 292.96,430.25 292.96,430.25 C292.96,430.25 292.77,430.22 292.77,430.22 C292.77,430.22 292.83,434.13 292.83,434.13 C292.83,434.13 292.82,434.13 292.82,434.13 C292.82,434.13 292.84,434.13 292.84,434.13 C292.84,434.13 292.81,434.13 292.81,434.13 C292.81,434.13 292.83,434.13 292.83,434.13 C292.83,434.13 292.83,434.12 292.83,434.12z ;M286.91 434.04 C286.91,434.04 286.89,434.02 286.89,434.02 C286.89,434.02 286.92,434.03 286.92,434.03 C286.92,434.03 286.9,434.06 286.9,434.06 C286.9,434.06 286.92,434.06 286.92,434.06 C286.92,434.06 294.61,434.08 294.61,434.08 C294.61,434.08 298.39,434.03 298.39,434.03 C298.39,434.03 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.91,428.68 291.91,428.68 C291.91,428.68 291.82,428.67 291.82,428.67 C291.82,428.67 291.88,428.65 291.88,428.46 C291.88,428.28 291.78,428.37 291.78,428.37 C291.78,428.37 291.84,428.44 291.84,428.44 C291.84,428.44 292.15,428.39 292.15,428.39 C292.15,428.39 292.12,428.35 292.12,428.35 C292.12,428.35 292.08,428.33 292.08,428.33 C292.08,428.33 292.1,428.48 292.1,428.48 C292.1,428.48 292.15,428.42 292.15,428.42 C292.15,428.42 292.19,428.41 292.19,428.41 C292.19,428.41 292.19,428.46 292.19,428.46 C292.19,428.46 291.97,428.51 291.97,428.51 C291.97,428.51 287.14,434.07 287.14,434.07 C287.14,434.07 286.89,434.05 286.89,434.05 C286.89,434.05 286.92,434.05 286.92,434.05 C286.92,434.05 286.89,434.05 286.89,434.05 C286.89,434.05 286.91,434.05 286.91,434.05 C286.91,434.05 286.91,434.04 286.91,434.04z ;M286.7 429.47 C286.7,429.47 286.88,429.37 286.88,429.37 C286.88,429.37 286.52,429.45 286.52,429.45 C286.52,429.45 286.83,429.85 286.83,429.85 C286.83,429.85 286.14,434.18 286.14,434.18 C286.14,434.18 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.08 298.37,434.08 C298.37,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.38,430.31 298.38,430.31 C298.38,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.99,426.42 291.99,426.42 C291.99,426.42 291.87,426.34 291.87,426.34 C291.87,426.34 292.01,426.25 292.01,426.07 C292.01,425.88 292.05,425.99 292.05,425.99 C292.05,425.99 291.97,425.95 291.97,425.95 C291.97,425.95 292.39,425.98 292.39,425.98 C292.39,425.98 292.27,426.05 292.27,426.05 C292.27,426.05 292.35,425.99 292.35,425.99 C292.35,425.99 292.32,426 292.32,426 C292.32,426 292.4,426 292.4,426 C292.4,426 292.4,426.06 292.4,426.06 C292.4,426.06 292.39,426.05 292.39,426.05 C292.39,426.05 292.62,426.45 292.62,426.45 C292.62,426.45 286.78,429.41 286.78,429.41 C286.78,429.41 286.55,429.2 286.55,429.2 C286.55,429.2 286.62,429.38 286.62,429.38 C286.62,429.38 286.51,429.44 286.51,429.44 C286.51,429.44 286.46,429.37 286.46,429.37 C286.46,429.37 286.7,429.47 286.7,429.47z ;M286.5 424.9 C286.5,424.9 286.87,424.72 286.87,424.72 C286.87,424.72 286.13,424.87 286.13,424.87 C286.13,424.87 286.76,425.64 286.76,425.64 C286.76,425.64 285.37,434.3 285.37,434.3 C285.37,434.3 294.63,434.09 294.63,434.09 C294.63,434.09 298.37,434.09 298.37,434.09 C298.37,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.36,430.31 298.36,430.31 C298.36,430.31 294.59,430.33 294.59,430.33 C294.59,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.06,424.16 292.06,424.16 C292.06,424.16 291.91,424.01 291.91,424.01 C291.91,424.01 292.13,423.86 292.13,423.68 C292.13,423.49 292.32,423.6 292.32,423.6 C292.32,423.6 292.1,423.46 292.1,423.46 C292.1,423.46 292.62,423.57 292.62,423.57 C292.62,423.57 292.43,423.75 292.43,423.75 C292.43,423.75 292.62,423.64 292.62,423.64 C292.62,423.64 292.54,423.53 292.54,423.53 C292.54,423.53 292.65,423.57 292.65,423.57 C292.65,423.57 292.62,423.72 292.62,423.72 C292.62,423.72 292.58,423.64 292.58,423.64 C292.58,423.64 293.27,424.39 293.27,424.39 C293.27,424.39 286.43,424.75 286.43,424.75 C286.43,424.75 286.2,424.35 286.2,424.35 C286.2,424.35 286.31,424.72 286.31,424.72 C286.31,424.72 286.13,424.83 286.13,424.83 C286.13,424.83 286.02,424.68 286.02,424.68 C286.02,424.68 286.5,424.9 286.5,424.9z ;M285.53 417.93 C285.53,417.93 285.61,418.01 285.61,418.01 C285.61,418.01 285.39,417.97 285.39,417.97 C285.39,417.97 285.68,418.12 285.68,418.12 C285.68,418.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.38,434.11 298.38,434.11 C298.38,434.11 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.71,430.31 298.71,430.31 C298.71,430.31 293.3,430.31 293.3,430.31 C293.3,430.31 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.26,417.75 291.26,417.56 C291.26,417.38 291.34,417.38 291.34,417.38 C291.34,417.38 291.45,417.54 291.45,417.54 C291.45,417.54 291.21,417.5 291.21,417.5 C291.21,417.5 291.32,417.45 291.32,417.45 C291.32,417.45 291.28,417.51 291.28,417.51 C291.28,417.51 291.5,417.56 291.5,417.56 C291.5,417.56 291.52,417.54 291.52,417.54 C291.52,417.54 291.45,417.6 291.45,417.6 C291.45,417.6 291.43,417.67 291.43,417.67 C291.43,417.67 291.41,417.89 291.41,417.89 C291.41,417.89 291.24,417.95 291.24,417.95 C291.24,417.95 285.98,417.86 285.98,417.86 C285.98,417.86 286.02,417.69 286.02,417.69 C286.02,417.69 285.92,417.77 285.92,417.77 C285.92,417.77 285.81,417.62 285.81,417.62 C285.81,417.62 285.53,417.93 285.53,417.93z ;M284.93 404.18 C284.93,404.18 281.14,411.97 281.14,411.97 C281.14,411.97 273.88,412.04 273.88,412.04 C273.88,412.04 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.08 298.36,434.08 C298.36,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.35,430.31 298.35,430.31 C298.35,430.31 294.59,430.32 294.59,430.32 C294.59,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 291.91,415.81 291.91,415.81 C291.91,415.81 291.8,415.82 291.8,415.82 C291.8,415.82 291.88,415.73 291.88,415.73 C291.88,415.73 291.9,415.66 291.9,415.66 C291.9,415.66 291.8,415.65 291.8,415.65 C291.8,415.65 291.73,415.73 291.73,415.73 C291.73,415.73 291.87,415.58 291.87,415.58 C291.87,415.58 291.87,415.71 291.87,415.71 C291.87,415.71 291.83,415.72 291.83,415.72 C291.83,415.72 291.82,415.71 291.82,415.71 C291.82,415.71 291.66,414.92 291.66,414.92 C291.66,414.92 291.45,413.38 291.45,413.38 C291.45,413.38 291.09,411.81 291.09,411.81 C291.09,411.81 291.05,411.77 291.05,411.77 C291.05,411.77 289.08,410.26 289.08,410.26 C289.08,410.26 284.93,404.18 284.93,404.18z ;M298.66 404.21 C298.66,404.21 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.76,430.32 298.76,430.32 C298.76,430.32 294.62,430.33 294.62,430.33 C294.62,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 300.75,413.19 300.75,413.19 C300.75,413.19 300.74,413.2 300.74,413.2 C300.74,413.2 300.68,413.28 300.68,413.28 C300.68,413.28 300.74,413.15 300.74,413.15 C300.74,413.15 300.76,413.19 300.76,413.19 C300.76,413.19 300.77,413.17 300.77,413.17 C300.77,413.17 303.55,406.44 303.55,406.44 C303.55,406.44 302.85,404.47 302.85,404.47 C302.85,404.47 301.29,403.47 301.29,403.47 C301.29,403.47 301.18,403.32 301.18,403.32 C301.18,403.32 298.66,404.21 298.66,404.21z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.07 294.61,434.07 C294.61,434.07 298.36,434.07 298.36,434.07 C298.36,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.73,430.31 298.73,430.31 C298.73,430.31 293.3,430.33 293.3,430.33 C293.3,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.59,416.02 302.59,416.02 C302.59,416.02 302.55,415.98 302.55,415.98 C302.55,415.98 302.63,415.99 302.63,415.99 C302.63,415.99 306.67,409.55 306.67,409.55 C306.67,409.55 306.65,409.61 306.65,409.61 C306.65,409.61 306.59,409.55 306.59,409.55 C306.59,409.55 306.69,409.72 306.69,409.72 C306.69,409.72 306.58,409.57 306.58,409.57 C306.58,409.57 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.29,420.1 306.29,420.1 C306.29,420.1 301.7,423.39 301.7,423.39 C301.7,423.39 298.38,430.31 298.38,430.31 C298.38,430.31 293.4,430.32 293.4,430.32 C293.4,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.63,417.02 302.63,417.02 C302.63,417.02 302.61,416.97 302.61,416.97 C302.61,416.97 302.63,416.9 302.63,416.9 C302.63,416.9 307.12,415.55 307.12,415.55 C307.12,415.55 307.51,415.47 307.51,415.47 C307.51,415.47 307.52,415.47 307.52,415.47 C307.52,415.47 309.01,412.56 309.01,412.56 C309.01,412.56 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.08 294.6,434.08 C294.6,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.05,420.2 306.05,420.2 C306.05,420.2 301.63,423.29 301.63,423.29 C301.63,423.29 298.57,430.33 298.57,430.33 C298.57,430.33 293.35,430.32 293.35,430.32 C293.35,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.72,417.16 306.72,417.16 C306.72,417.16 307.56,417.29 307.56,417.29 C307.56,417.29 307.59,417.33 307.59,417.33 C307.59,417.33 308.54,413.47 308.54,413.47 C308.54,413.47 306.71,408.22 306.71,408.22 C306.71,408.22 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.62,434.09 294.62,434.09 C294.62,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.8,420.3 305.8,420.3 C305.8,420.3 301.55,423.2 301.55,423.2 C301.55,423.2 298.74,430.31 298.74,430.31 C298.74,430.31 293.34,430.32 293.34,430.32 C293.34,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.32,418.77 306.32,418.77 C306.32,418.77 307.34,417.78 307.34,417.78 C307.34,417.78 307.74,418.52 307.74,418.52 C307.74,418.52 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.09 294.6,434.09 C294.6,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.98,420.3 305.98,420.3 C305.98,420.3 301.72,423.59 301.72,423.59 C301.72,423.59 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 296.68,421.72 296.68,421.72 C296.68,421.72 300.57,423.18 300.57,423.18 C300.57,423.18 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.41,419.97 306.41,419.97 C306.41,419.97 301.7,423.64 301.7,423.64 C301.7,423.64 298.69,430.31 298.69,430.31 C298.69,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 294.58,430.33 294.58,430.33 C294.58,430.33 298.38,430.31 298.38,430.31 C298.38,430.31 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.73,439.55 293.73,439.55 C293.73,439.55 298.46,439.54 298.46,439.54 C298.46,439.54 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.43,419.98 306.43,419.98 C306.43,419.98 301.75,423.57 301.75,423.57 C301.75,423.57 298.73,430.27 298.73,430.27 C298.73,430.27 293.72,430.3 293.72,430.3 C293.72,430.3 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.7,430.31 293.7,430.31 C293.7,430.31 298.74,430.26 298.74,430.26 C298.74,430.26 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_0_C_0">
                                    <path id="_R_G_L_0_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate attributeType="XML" attributeName="opacity" dur="2s" from="0" to="1" xlink:href="#time_group"/>
                            </defs>
                            <g id="_R_G">
                                <g id="_R_G_L_1_G" transform=" translate(127.638, 127.945) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_1_C_0)">
                                        <path id="_R_G_L_1_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M328 398.61 C328,398.61 328,446.23 328,446.23 C328,449.7 325.2,452.5 321.75,452.5 C321.75,452.5 274.25,452.5 274.25,452.5 C270.8,452.5 268,449.7 268,446.23 C268,446.23 268,398.61 268,398.61 C268,395.15 270.8,392.35 274.25,392.35 C274.25,392.35 283.46,392.26 283.46,392.26 C283.46,392.26 283.46,390.38 283.46,390.38 C283.46,389.76 284.08,388.5 285.33,388.5 C286.58,388.5 287.21,389.75 287.21,390.38 C287.21,390.38 287.21,397.89 287.21,397.89 C287.21,398.53 286.59,399.78 285.33,399.78 C284.08,399.78 283.46,398.53 283.46,397.9 C283.46,397.9 283.46,396.02 283.46,396.02 C283.46,396.02 275.5,396.1 275.5,396.1 C273.43,396.1 271.75,397.79 271.75,399.86 C271.75,399.86 271.75,444.98 271.75,444.98 C271.75,447.06 273.43,448.74 275.5,448.74 C275.5,448.74 320.5,448.74 320.5,448.74 C322.57,448.74 324.25,447.06 324.25,444.98 C324.25,444.98 324.25,399.86 324.25,399.86 C324.25,397.79 322.57,396.1 320.5,396.1 C320.5,396.1 312.62,396.1 312.62,396.1 C312.62,396.1 312.63,397.06 312.63,397.99 C312.63,398.61 312,399.86 310.75,399.86 C309.5,399.86 308.88,398.61 308.88,397.98 C308.88,397.98 308.87,396.1 308.87,396.1 C308.87,396.1 301.88,396.1 301.88,396.1 C300.84,396.1 300,395.26 300,394.23 C300,393.19 300.84,392.35 301.88,392.35 C301.88,392.35 308.87,392.35 308.87,392.35 C308.87,392.35 308.87,390.47 308.87,390.47 C308.87,389.83 309.5,388.5 310.75,388.5 C312,388.5 312.62,389.84 312.62,390.47 C312.62,390.47 312.62,392.35 312.62,392.35 C312.62,392.35 321.75,392.35 321.75,392.35 C325.2,392.35 328,395.15 328,398.61z "/>
                                    </g>
                                </g>
                                <g id="_R_G_L_0_G" transform=" translate(125.555, 126.412) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_0_C_0)">
                                        <path id="_R_G_L_0_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M305.86 420.29 C305.86,420.29 307.11,419.04 307.11,415.28 C307.11,409.01 303.36,407.76 298.36,407.76 C298.36,407.76 287.11,407.76 287.11,407.76 C287.11,407.76 287.11,434.08 287.11,434.08 C287.11,434.08 294.61,434.08 294.61,434.08 C294.61,434.08 294.61,441.6 294.61,441.6 C294.61,441.6 298.36,441.6 298.36,441.6 C298.36,441.6 298.36,434.08 298.36,434.08 C302.71,434.08 305.73,434.08 307.98,431.3 C309.07,429.95 309.62,428.24 309.61,426.5 C309.61,425.58 309.51,424.67 309.3,424.05 C308.73,422.65 308.36,421.55 305.86,420.29z  M302.11 430.32 C302.11,430.32 298.36,430.32 298.36,430.32 C298.36,430.32 298.36,426.56 298.36,426.56 C298.36,424.48 300.03,422.8 302.11,422.8 C304.13,422.8 305.86,424.43 305.86,426.56 C305.86,428.78 304.03,430.32 302.11,430.32z  M299.07 419.95 C298.43,420.26 297.82,420.63 297.26,421.05 C295.87,422.1 294.61,423.58 294.61,426.56 C294.61,426.56 294.61,430.32 294.61,430.32 C294.61,430.32 290.86,430.32 290.86,430.32 C290.86,430.32 290.86,411.52 290.86,411.52 C290.86,411.52 298.36,411.52 298.36,411.52 C301.35,411.52 303.36,412.77 303.36,415.28 C303.36,417.58 301.65,418.68 299.07,419.95z "/>
                                    </g>
                                </g>
                            </g>
                            <g id="time_group"/>
                        </svg>
                    </div>
                </div>

                <div class="bpa-front--dt__custom-duration-is-full" v-if="typeof appointment_step_form_data.custom_service_duration_value !== 'undefined' && appointment_step_form_data.custom_service_duration_value == ''">
                    <div class="bpa-front-cdf__icon">
                        <svg width="53" height="52" viewBox="0 0 53 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="bpa-front-cdf__icon-bg" d="M53 33.7139C53 43.7567 43.5541 51.8979 33.3949 51.8979C23.2357 51.8979 15 43.7567 15 33.7139C15 23.6711 25.6561 12.8979 35.8153 12.8979C45.9745 12.8979 53 23.6711 53 33.7139Z" /> 
                            <g filter="url(#filter0_d_2605_6524)">
                                <path d="M37.5519 9.48846H3V44.1506H37.5519V9.48846Z" fill="white"/>
                            </g>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M32.197 6.07112C32.7117 6.07112 33.1283 5.6545 33.1283 5.13977V0.931347C33.1283 0.416616 32.7117 0 32.197 0C31.6822 0 31.2656 0.416616 31.2656 0.931347V5.13977C31.2656 5.6545 31.6822 6.07112 32.197 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M20.7243 6.07112C21.239 6.07112 21.6557 5.6545 21.6557 5.13977V0.931347C21.6557 0.416616 21.239 0 20.7243 0C20.2096 0 19.793 0.416616 19.793 0.931347V5.13977C19.793 5.6545 20.2096 6.07112 20.7243 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" fill-rule="evenodd" clip-rule="evenodd" d="M37.5519 9.4886V4.03637C37.5519 2.95257 36.6749 2.07556 35.5911 2.07556H4.96081C3.87852 2.07556 3 2.95257 3 4.03637V9.4886H37.5519ZM33.8217 5.46116C33.8217 6.35901 33.0939 7.08686 32.196 7.08686C31.2982 7.08686 30.5703 6.35901 30.5703 5.46116C30.5703 4.5633 31.2982 3.83545 32.196 3.83545C33.0939 3.83545 33.8217 4.5633 33.8217 5.46116ZM20.7253 7.08686C21.6232 7.08686 22.351 6.35901 22.351 5.46116C22.351 4.5633 21.6232 3.83545 20.7253 3.83545C19.8275 3.83545 19.0996 4.5633 19.0996 5.46116C19.0996 6.35901 19.8275 7.08686 20.7253 7.08686ZM10.8784 5.46116C10.8784 6.35901 10.1505 7.08686 9.25266 7.08686C8.35481 7.08686 7.62695 6.35901 7.62695 5.46116C7.62695 4.5633 8.35481 3.83545 9.25266 3.83545C10.1505 3.83545 10.8784 4.5633 10.8784 5.46116Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M9.25166 6.07112C9.76639 6.07112 10.183 5.6545 10.183 5.13977V0.931347C10.183 0.416616 9.76639 0 9.25166 0C8.73693 0 8.32031 0.416616 8.32031 0.931347V5.13977C8.32031 5.6545 8.73693 6.07112 9.25166 6.07112Z" />
                            <path d="M25.88 21.6296H32.4492V15.0603H25.88V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 21.6296H23.707V15.0603H17.1378V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 21.6296H14.9668V15.0603H8.39756V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M25.88 29.9683H32.4492V23.3991H25.88V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 29.9683H23.707V23.3991H17.1378V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M8.39756 29.9683H14.9668V23.3991H8.39756V29.9683Z" />
                            <path d="M10.7715 25.744L12.5934 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M12.5934 25.744L10.7715 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M25.88 38.3079H32.4492V31.7386H25.88V38.3079Z" stroke="#F0E0DF" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 38.3079H23.707V31.7386H17.1378V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 38.3079H14.9668V31.7386H8.39756V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M36.9588 21.31H33.7617V23.4157H36.9588V21.31Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M37.4509 21.8565H33.2682C32.7927 21.8565 32.4062 21.4701 32.4062 20.9946C32.4062 20.5191 32.7927 20.1327 33.2682 20.1327H37.4509C37.9264 20.1327 38.3128 20.5191 38.3128 20.9946C38.3128 21.4701 37.9264 21.8565 37.4509 21.8565Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M27.6055 24.5042L25.8027 26.3069L27.0739 27.5782L28.8767 25.7754L27.6055 24.5042Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.1741 24.4304L25.6563 26.9482C25.4027 27.2018 24.9907 27.2018 24.7371 26.9482C24.4835 26.6946 24.4835 26.2825 24.7371 26.0289L27.2549 23.5111C27.5085 23.2575 27.9206 23.2575 28.1741 23.5111C28.4292 23.7647 28.4292 24.1768 28.1741 24.4304Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.322 44.122C41.2901 44.122 46.1283 39.2838 46.1283 33.3156C46.1283 27.3474 41.2901 22.5093 35.322 22.5093C29.3538 22.5093 24.5156 27.3474 24.5156 33.3156C24.5156 39.2838 29.3538 44.122 35.322 44.122Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3229 43.5408C40.9701 43.5408 45.548 38.9629 45.548 33.3156C45.548 27.6684 40.9701 23.0905 35.3229 23.0905C29.6756 23.0905 25.0977 27.6684 25.0977 33.3156C25.0977 38.9629 29.6756 43.5408 35.3229 43.5408Z" />
                            <path d="M35.3224 42.3859C40.3319 42.3859 44.3928 38.325 44.3928 33.3155C44.3928 28.306 40.3319 24.2451 35.3224 24.2451C30.3129 24.2451 26.252 28.306 26.252 33.3155C26.252 38.325 30.3129 42.3859 35.3224 42.3859Z" fill="#F6F6F6"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 26.2884C35.2413 26.2884 35.1387 26.1858 35.1387 26.0605V25.5246C35.1387 25.3978 35.2413 25.2967 35.3666 25.2967C35.4934 25.2967 35.5945 25.3993 35.5945 25.5246V26.0605C35.5945 26.1858 35.4919 26.2884 35.3666 26.2884Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 41.4433C35.2413 41.4433 35.1387 41.3407 35.1387 41.2154V40.6795C35.1387 40.5542 35.2413 40.4516 35.3666 40.4516C35.4934 40.4516 35.5945 40.5542 35.5945 40.6795V41.2154C35.5945 41.3407 35.4919 41.4433 35.3666 41.4433Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M30.197 28.4288C30.1382 28.4288 30.0808 28.4062 30.0355 28.3624L29.6566 27.9835C29.5676 27.8945 29.5676 27.7495 29.6566 27.6605C29.7457 27.5714 29.8906 27.5714 29.9797 27.6605L30.3585 28.0394C30.4476 28.1284 30.4476 28.2733 30.3585 28.3624C30.3148 28.4077 30.2559 28.4288 30.197 28.4288Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.9138 39.1444C40.855 39.1444 40.7976 39.1217 40.7523 39.078L40.3734 38.6991C40.2844 38.61 40.2844 38.4651 40.3734 38.3761C40.4625 38.287 40.6074 38.287 40.6965 38.3761L41.0753 38.7549C41.1644 38.844 41.1644 38.9889 41.0753 39.078C41.0301 39.1233 40.9712 39.1444 40.9138 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M29.8182 39.1444C29.7593 39.1444 29.7019 39.1217 29.6566 39.078C29.5676 38.9889 29.5676 38.844 29.6566 38.7549L30.0355 38.3761C30.1246 38.287 30.2695 38.287 30.3585 38.3761C30.4476 38.4651 30.4476 38.61 30.3585 38.6991L29.9797 39.078C29.9344 39.1233 29.8755 39.1444 29.8182 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.5349 28.4287C40.4761 28.4287 40.4187 28.406 40.3734 28.3623C40.2844 28.2732 40.2844 28.1283 40.3734 28.0392L40.7523 27.6604C40.8414 27.5713 40.9863 27.5713 41.0753 27.6604C41.1644 27.7494 41.1644 27.8943 41.0753 27.9834L40.6965 28.3623C40.6512 28.4076 40.5923 28.4287 40.5349 28.4287Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.0568 33.5971H27.5209C27.3941 33.5971 27.293 33.4945 27.293 33.3692C27.293 33.2439 27.3956 33.1412 27.5209 33.1412H28.0568C28.1836 33.1412 28.2847 33.2439 28.2847 33.3692C28.2847 33.496 28.1836 33.5971 28.0568 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M43.213 33.5971H42.6771C42.5519 33.5971 42.4492 33.4945 42.4492 33.3692C42.4492 33.2439 42.5519 33.1412 42.6771 33.1412H43.213C43.3383 33.1412 43.4409 33.2439 43.4409 33.3692C43.4409 33.496 43.3383 33.5971 43.213 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.9141 33.2836H34.8242V28.5574C34.8242 28.257 35.0688 28.0125 35.3691 28.0125C35.6695 28.0125 35.9141 28.257 35.9141 28.5574V33.2836Z" />
                            <path d="M35.369 34.5334C36.0593 34.5334 36.6188 33.9738 36.6188 33.2835C36.6188 32.5933 36.0593 32.0337 35.369 32.0337C34.6787 32.0337 34.1191 32.5933 34.1191 33.2835C34.1191 33.9738 34.6787 34.5334 35.369 34.5334Z" fill="#2B4183"/>
                            <defs>
                                <filter id="filter0_d_2605_6524" x="0.897293" y="7.38576" width="41.5618" height="41.6711" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dx="1.4018" dy="1.4018"/>
                                    <feGaussianBlur stdDeviation="1.75226"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2605_6524"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2605_6524" result="shape"/>
                                </filter>
                            </defs>
                        </svg>
                    </div>
                    <div class="bpa-front--dt-ts__sub-heading">{{custom_service_duration_title}}</div>
                    <div class="bpa-front-cdf__desc">{{custom_service_description_title}}</div>
                    <el-form>
                        <div class="bpa-front-module--bd-form">
                            <el-row class="bpa-bd-fields-row">
                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                    <template>
                                        <div class="bpa-bdf--single-col-item">
                                            <el-form-item>
                                                <el-select v-model="appointment_step_form_data.custom_service_duration_value" popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" class="bpa-front-form-control" :placeholder="custom_please_select_title" @change="bookingpress_change_custom_duration_first($event)">
                                                    <el-option :label="custom_please_select_title" value=""></el-option>
                                                    <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value" ></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </div>
                                    </template>
                                </el-col>
                            </el-row>
                        </div>
                    </el-form>
                </div>
                <div class="bpa-front--dt__custom-duration-card" v-else-if="is_display_custom_duration_loader == false">
                    <div class="bpa-front-cdc__left">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-.22-13h-.06c-.4 0-.72.32-.72.72v4.72c0 .35.18.68.49.86l4.15 2.49c.34.2.78.1.98-.24.21-.34.1-.79-.25-.99l-3.87-2.3V7.72c0-.4-.32-.72-.72-.72z"/></svg>
                        <el-select popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" v-model="appointment_step_form_data.custom_service_duration_value" class="bpa-front-form-control" @change="bookingpress_change_custom_duration($event)">
                            <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value"></el-option>
                        </el-select>
                    </div>
                    <div class="bpa-front-cdc__right">
                        <div class="bpa-front-cdc__right-title">{{custom_price_title}}<div class="bpa-front-cdc__price-val">{{appointment_step_form_data.selected_service_price}}</div></div>
                    </div>
                </div>
                <div class="bpa-front-day-loader-container" :class="(is_display_custom_duration_day_loader == true) ? 'bpa-cd-day-service__loader-active' : '' " v-if="is_display_custom_duration_day_loader == true">
                    <div class="bpa-front-loader">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" width="256" height="256" viewBox="0 0 256 256" style="width:100%;height:100%">
                            <defs>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_1_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z " to="M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keyTimes="0;0.5037594;0.5263158;0.5789474;0.6691729;0.6992481;0.7593985;0.7669173;1" values="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M303.49 386.7 C303.49,386.7 284.88,386.7 284.88,386.7 C284.88,386.7 284.88,402.72 284.88,402.72 C284.88,402.72 293.41,402.87 293.41,402.87 C293.41,402.87 293.07,405.24 293.07,405.24 C293.07,405.24 296.63,405.24 296.63,405.24 C296.63,405.24 296.82,402.57 296.82,402.57 C296.82,402.57 304.49,401.98 304.49,401.98 C304.49,401.98 303.49,386.7 303.49,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,398.12 265.56,398.12 C265.56,398.12 266.75,407.02 266.75,407.02 C266.75,407.02 294.78,405.83 294.78,405.83 C294.78,405.83 298.34,405.83 298.34,405.83 C298.34,405.83 332.75,406.72 332.75,406.72 C332.75,406.72 332.45,399.46 332.45,399.46 C332.45,399.46 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,442.32 265.56,442.32 C265.56,442.32 266.75,448.4 266.75,448.4 C266.75,448.4 283.8,447.51 283.8,447.51 C283.8,447.51 312.06,447.21 312.06,447.21 C312.06,447.21 332.75,448.1 332.75,448.1 C332.75,448.1 332.45,443.65 332.45,443.65 C332.45,443.65 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.86,453.14 265.86,453.14 C265.86,453.14 276.98,456.11 276.98,456.11 C276.98,456.11 277.28,447.51 277.28,447.51 C277.28,447.51 319.47,447.81 319.47,447.81 C319.47,447.81 318.81,456.11 318.81,456.11 C318.81,456.11 329.63,454.92 329.63,454.92 C329.63,454.92 330.97,386.7 330.97,386.7z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.63,448.83 295.63,448.83 C295.63,448.83 295.71,448.75 295.71,448.75 C295.71,448.75 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_1_C_0">
                                    <path id="_R_G_L_1_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_0_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z " to="M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keyTimes="0;0.1804511;0.2180451;0.2481203;0.2631579;0.2706767;0.2781955;0.2857143;0.3157895;0.3308271;0.3533835;0.3834586;0.406015;0.4135338;0.4210526;0.4511278;0.4736842;0.4887218;0.4962406;1" values="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M310.92 429.74 C310.92,429.74 310.97,429.75 310.97,429.75 C310.97,429.75 310.93,429.74 310.93,429.74 C310.93,429.74 310.91,429.77 310.91,429.77 C310.91,429.77 310.94,429.77 310.94,429.77 C310.94,429.77 310.99,429.77 310.99,429.77 C310.99,429.77 311.09,429.7 311.09,429.7 C311.09,429.7 310.99,429.73 310.99,429.73 C310.99,429.73 310.9,434.91 310.9,434.91 C310.9,434.91 312.25,433.8 312.25,433.8 C312.25,433.8 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 303.93,428.18 303.93,428.18 C303.93,428.18 303.66,428.14 303.66,428.14 C303.66,428.14 303.84,428.16 303.84,428.16 C303.84,428.16 303.52,428.11 303.52,428.11 C303.52,428.11 303.67,428.12 303.67,428.12 C303.67,428.12 303.58,428.1 303.58,428.1 C303.58,428.1 303.49,428.3 303.49,428.11 C303.49,427.93 303.63,428.09 303.63,428.09 C303.63,428.09 303.45,428.1 303.45,428.1 C303.45,428.1 303.76,428.04 303.76,428.04 C303.76,428.04 303.73,428 303.73,428 C303.73,428 303.69,427.98 303.69,427.98 C303.69,427.98 303.71,428.13 303.71,428.13 C303.71,428.13 303.76,428.08 303.76,428.08 C303.76,428.08 303.8,428.06 303.8,428.06 C303.8,428.06 303.8,428.11 303.8,428.11 C303.8,428.11 303.58,428.16 303.58,428.16 C303.58,428.16 310.92,429.75 310.92,429.75 C310.92,429.75 310.91,429.75 310.91,429.75 C310.91,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.9,429.75 310.9,429.75 C310.9,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.92,429.74 310.92,429.74z ;M299.65 434.12 C299.65,434.12 299.7,434.13 299.7,434.13 C299.7,434.13 299.66,434.11 299.66,434.11 C299.66,434.11 299.64,434.14 299.64,434.14 C299.64,434.14 299.66,434.14 299.66,434.14 C299.66,434.14 299.72,434.15 299.72,434.15 C299.72,434.15 299.81,434.08 299.81,434.08 C299.81,434.08 299.72,434.11 299.72,434.11 C299.72,434.11 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 300.06,430.31 300.06,430.31 C300.06,430.31 299.78,430.27 299.78,430.27 C299.78,430.27 299.96,430.29 299.96,430.29 C299.96,430.29 299.65,430.25 299.65,430.25 C299.65,430.25 299.8,430.25 299.8,430.25 C299.8,430.25 299.7,430.24 299.7,430.24 C299.7,430.24 299.61,430.43 299.61,430.25 C299.61,430.06 299.75,430.22 299.75,430.22 C299.75,430.22 299.57,430.23 299.57,430.23 C299.57,430.23 299.89,430.17 299.89,430.17 C299.89,430.17 299.85,430.13 299.85,430.13 C299.85,430.13 299.82,430.12 299.82,430.12 C299.82,430.12 299.83,430.26 299.83,430.26 C299.83,430.26 299.89,430.21 299.89,430.21 C299.89,430.21 299.93,430.19 299.93,430.19 C299.93,430.19 299.93,430.25 299.93,430.25 C299.93,430.25 299.7,430.29 299.7,430.29 C299.7,430.29 299.65,434.13 299.65,434.13 C299.65,434.13 299.64,434.13 299.64,434.13 C299.64,434.13 299.66,434.13 299.66,434.13 C299.66,434.13 299.63,434.13 299.63,434.13 C299.63,434.13 299.65,434.13 299.65,434.13 C299.65,434.13 299.65,434.12 299.65,434.12z ;M292.83 434.12 C292.83,434.12 292.81,434.11 292.81,434.11 C292.81,434.11 292.84,434.12 292.84,434.12 C292.84,434.12 292.82,434.15 292.82,434.15 C292.82,434.15 292.85,434.15 292.85,434.15 C292.85,434.15 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 292.89,430.31 292.89,430.31 C292.89,430.31 292.69,430.25 292.69,430.25 C292.69,430.25 292.72,430.28 292.72,430.28 C292.72,430.28 292.63,430.26 292.63,430.26 C292.63,430.26 292.65,430.43 292.65,430.25 C292.65,430.06 292.56,430.15 292.56,430.15 C292.56,430.15 292.61,430.23 292.61,430.23 C292.61,430.23 292.93,430.17 292.93,430.17 C292.93,430.17 292.89,430.13 292.89,430.13 C292.89,430.13 292.85,430.12 292.85,430.12 C292.85,430.12 292.87,430.26 292.87,430.26 C292.87,430.26 292.93,430.21 292.93,430.21 C292.93,430.21 292.96,430.19 292.96,430.19 C292.96,430.19 292.96,430.25 292.96,430.25 C292.96,430.25 292.77,430.22 292.77,430.22 C292.77,430.22 292.83,434.13 292.83,434.13 C292.83,434.13 292.82,434.13 292.82,434.13 C292.82,434.13 292.84,434.13 292.84,434.13 C292.84,434.13 292.81,434.13 292.81,434.13 C292.81,434.13 292.83,434.13 292.83,434.13 C292.83,434.13 292.83,434.12 292.83,434.12z ;M286.91 434.04 C286.91,434.04 286.89,434.02 286.89,434.02 C286.89,434.02 286.92,434.03 286.92,434.03 C286.92,434.03 286.9,434.06 286.9,434.06 C286.9,434.06 286.92,434.06 286.92,434.06 C286.92,434.06 294.61,434.08 294.61,434.08 C294.61,434.08 298.39,434.03 298.39,434.03 C298.39,434.03 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.91,428.68 291.91,428.68 C291.91,428.68 291.82,428.67 291.82,428.67 C291.82,428.67 291.88,428.65 291.88,428.46 C291.88,428.28 291.78,428.37 291.78,428.37 C291.78,428.37 291.84,428.44 291.84,428.44 C291.84,428.44 292.15,428.39 292.15,428.39 C292.15,428.39 292.12,428.35 292.12,428.35 C292.12,428.35 292.08,428.33 292.08,428.33 C292.08,428.33 292.1,428.48 292.1,428.48 C292.1,428.48 292.15,428.42 292.15,428.42 C292.15,428.42 292.19,428.41 292.19,428.41 C292.19,428.41 292.19,428.46 292.19,428.46 C292.19,428.46 291.97,428.51 291.97,428.51 C291.97,428.51 287.14,434.07 287.14,434.07 C287.14,434.07 286.89,434.05 286.89,434.05 C286.89,434.05 286.92,434.05 286.92,434.05 C286.92,434.05 286.89,434.05 286.89,434.05 C286.89,434.05 286.91,434.05 286.91,434.05 C286.91,434.05 286.91,434.04 286.91,434.04z ;M286.7 429.47 C286.7,429.47 286.88,429.37 286.88,429.37 C286.88,429.37 286.52,429.45 286.52,429.45 C286.52,429.45 286.83,429.85 286.83,429.85 C286.83,429.85 286.14,434.18 286.14,434.18 C286.14,434.18 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.08 298.37,434.08 C298.37,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.38,430.31 298.38,430.31 C298.38,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.99,426.42 291.99,426.42 C291.99,426.42 291.87,426.34 291.87,426.34 C291.87,426.34 292.01,426.25 292.01,426.07 C292.01,425.88 292.05,425.99 292.05,425.99 C292.05,425.99 291.97,425.95 291.97,425.95 C291.97,425.95 292.39,425.98 292.39,425.98 C292.39,425.98 292.27,426.05 292.27,426.05 C292.27,426.05 292.35,425.99 292.35,425.99 C292.35,425.99 292.32,426 292.32,426 C292.32,426 292.4,426 292.4,426 C292.4,426 292.4,426.06 292.4,426.06 C292.4,426.06 292.39,426.05 292.39,426.05 C292.39,426.05 292.62,426.45 292.62,426.45 C292.62,426.45 286.78,429.41 286.78,429.41 C286.78,429.41 286.55,429.2 286.55,429.2 C286.55,429.2 286.62,429.38 286.62,429.38 C286.62,429.38 286.51,429.44 286.51,429.44 C286.51,429.44 286.46,429.37 286.46,429.37 C286.46,429.37 286.7,429.47 286.7,429.47z ;M286.5 424.9 C286.5,424.9 286.87,424.72 286.87,424.72 C286.87,424.72 286.13,424.87 286.13,424.87 C286.13,424.87 286.76,425.64 286.76,425.64 C286.76,425.64 285.37,434.3 285.37,434.3 C285.37,434.3 294.63,434.09 294.63,434.09 C294.63,434.09 298.37,434.09 298.37,434.09 C298.37,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.36,430.31 298.36,430.31 C298.36,430.31 294.59,430.33 294.59,430.33 C294.59,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.06,424.16 292.06,424.16 C292.06,424.16 291.91,424.01 291.91,424.01 C291.91,424.01 292.13,423.86 292.13,423.68 C292.13,423.49 292.32,423.6 292.32,423.6 C292.32,423.6 292.1,423.46 292.1,423.46 C292.1,423.46 292.62,423.57 292.62,423.57 C292.62,423.57 292.43,423.75 292.43,423.75 C292.43,423.75 292.62,423.64 292.62,423.64 C292.62,423.64 292.54,423.53 292.54,423.53 C292.54,423.53 292.65,423.57 292.65,423.57 C292.65,423.57 292.62,423.72 292.62,423.72 C292.62,423.72 292.58,423.64 292.58,423.64 C292.58,423.64 293.27,424.39 293.27,424.39 C293.27,424.39 286.43,424.75 286.43,424.75 C286.43,424.75 286.2,424.35 286.2,424.35 C286.2,424.35 286.31,424.72 286.31,424.72 C286.31,424.72 286.13,424.83 286.13,424.83 C286.13,424.83 286.02,424.68 286.02,424.68 C286.02,424.68 286.5,424.9 286.5,424.9z ;M285.53 417.93 C285.53,417.93 285.61,418.01 285.61,418.01 C285.61,418.01 285.39,417.97 285.39,417.97 C285.39,417.97 285.68,418.12 285.68,418.12 C285.68,418.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.38,434.11 298.38,434.11 C298.38,434.11 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.71,430.31 298.71,430.31 C298.71,430.31 293.3,430.31 293.3,430.31 C293.3,430.31 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.26,417.75 291.26,417.56 C291.26,417.38 291.34,417.38 291.34,417.38 C291.34,417.38 291.45,417.54 291.45,417.54 C291.45,417.54 291.21,417.5 291.21,417.5 C291.21,417.5 291.32,417.45 291.32,417.45 C291.32,417.45 291.28,417.51 291.28,417.51 C291.28,417.51 291.5,417.56 291.5,417.56 C291.5,417.56 291.52,417.54 291.52,417.54 C291.52,417.54 291.45,417.6 291.45,417.6 C291.45,417.6 291.43,417.67 291.43,417.67 C291.43,417.67 291.41,417.89 291.41,417.89 C291.41,417.89 291.24,417.95 291.24,417.95 C291.24,417.95 285.98,417.86 285.98,417.86 C285.98,417.86 286.02,417.69 286.02,417.69 C286.02,417.69 285.92,417.77 285.92,417.77 C285.92,417.77 285.81,417.62 285.81,417.62 C285.81,417.62 285.53,417.93 285.53,417.93z ;M284.93 404.18 C284.93,404.18 281.14,411.97 281.14,411.97 C281.14,411.97 273.88,412.04 273.88,412.04 C273.88,412.04 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.08 298.36,434.08 C298.36,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.35,430.31 298.35,430.31 C298.35,430.31 294.59,430.32 294.59,430.32 C294.59,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 291.91,415.81 291.91,415.81 C291.91,415.81 291.8,415.82 291.8,415.82 C291.8,415.82 291.88,415.73 291.88,415.73 C291.88,415.73 291.9,415.66 291.9,415.66 C291.9,415.66 291.8,415.65 291.8,415.65 C291.8,415.65 291.73,415.73 291.73,415.73 C291.73,415.73 291.87,415.58 291.87,415.58 C291.87,415.58 291.87,415.71 291.87,415.71 C291.87,415.71 291.83,415.72 291.83,415.72 C291.83,415.72 291.82,415.71 291.82,415.71 C291.82,415.71 291.66,414.92 291.66,414.92 C291.66,414.92 291.45,413.38 291.45,413.38 C291.45,413.38 291.09,411.81 291.09,411.81 C291.09,411.81 291.05,411.77 291.05,411.77 C291.05,411.77 289.08,410.26 289.08,410.26 C289.08,410.26 284.93,404.18 284.93,404.18z ;M298.66 404.21 C298.66,404.21 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.76,430.32 298.76,430.32 C298.76,430.32 294.62,430.33 294.62,430.33 C294.62,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 300.75,413.19 300.75,413.19 C300.75,413.19 300.74,413.2 300.74,413.2 C300.74,413.2 300.68,413.28 300.68,413.28 C300.68,413.28 300.74,413.15 300.74,413.15 C300.74,413.15 300.76,413.19 300.76,413.19 C300.76,413.19 300.77,413.17 300.77,413.17 C300.77,413.17 303.55,406.44 303.55,406.44 C303.55,406.44 302.85,404.47 302.85,404.47 C302.85,404.47 301.29,403.47 301.29,403.47 C301.29,403.47 301.18,403.32 301.18,403.32 C301.18,403.32 298.66,404.21 298.66,404.21z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.07 294.61,434.07 C294.61,434.07 298.36,434.07 298.36,434.07 C298.36,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.73,430.31 298.73,430.31 C298.73,430.31 293.3,430.33 293.3,430.33 C293.3,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.59,416.02 302.59,416.02 C302.59,416.02 302.55,415.98 302.55,415.98 C302.55,415.98 302.63,415.99 302.63,415.99 C302.63,415.99 306.67,409.55 306.67,409.55 C306.67,409.55 306.65,409.61 306.65,409.61 C306.65,409.61 306.59,409.55 306.59,409.55 C306.59,409.55 306.69,409.72 306.69,409.72 C306.69,409.72 306.58,409.57 306.58,409.57 C306.58,409.57 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.29,420.1 306.29,420.1 C306.29,420.1 301.7,423.39 301.7,423.39 C301.7,423.39 298.38,430.31 298.38,430.31 C298.38,430.31 293.4,430.32 293.4,430.32 C293.4,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.63,417.02 302.63,417.02 C302.63,417.02 302.61,416.97 302.61,416.97 C302.61,416.97 302.63,416.9 302.63,416.9 C302.63,416.9 307.12,415.55 307.12,415.55 C307.12,415.55 307.51,415.47 307.51,415.47 C307.51,415.47 307.52,415.47 307.52,415.47 C307.52,415.47 309.01,412.56 309.01,412.56 C309.01,412.56 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.08 294.6,434.08 C294.6,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.05,420.2 306.05,420.2 C306.05,420.2 301.63,423.29 301.63,423.29 C301.63,423.29 298.57,430.33 298.57,430.33 C298.57,430.33 293.35,430.32 293.35,430.32 C293.35,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.72,417.16 306.72,417.16 C306.72,417.16 307.56,417.29 307.56,417.29 C307.56,417.29 307.59,417.33 307.59,417.33 C307.59,417.33 308.54,413.47 308.54,413.47 C308.54,413.47 306.71,408.22 306.71,408.22 C306.71,408.22 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.62,434.09 294.62,434.09 C294.62,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.8,420.3 305.8,420.3 C305.8,420.3 301.55,423.2 301.55,423.2 C301.55,423.2 298.74,430.31 298.74,430.31 C298.74,430.31 293.34,430.32 293.34,430.32 C293.34,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.32,418.77 306.32,418.77 C306.32,418.77 307.34,417.78 307.34,417.78 C307.34,417.78 307.74,418.52 307.74,418.52 C307.74,418.52 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.09 294.6,434.09 C294.6,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.98,420.3 305.98,420.3 C305.98,420.3 301.72,423.59 301.72,423.59 C301.72,423.59 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 296.68,421.72 296.68,421.72 C296.68,421.72 300.57,423.18 300.57,423.18 C300.57,423.18 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.41,419.97 306.41,419.97 C306.41,419.97 301.7,423.64 301.7,423.64 C301.7,423.64 298.69,430.31 298.69,430.31 C298.69,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 294.58,430.33 294.58,430.33 C294.58,430.33 298.38,430.31 298.38,430.31 C298.38,430.31 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.73,439.55 293.73,439.55 C293.73,439.55 298.46,439.54 298.46,439.54 C298.46,439.54 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.43,419.98 306.43,419.98 C306.43,419.98 301.75,423.57 301.75,423.57 C301.75,423.57 298.73,430.27 298.73,430.27 C298.73,430.27 293.72,430.3 293.72,430.3 C293.72,430.3 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.7,430.31 293.7,430.31 C293.7,430.31 298.74,430.26 298.74,430.26 C298.74,430.26 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_0_C_0">
                                    <path id="_R_G_L_0_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate attributeType="XML" attributeName="opacity" dur="2s" from="0" to="1" xlink:href="#time_group"/>
                            </defs>
                            <g id="_R_G">
                                <g id="_R_G_L_1_G" transform=" translate(127.638, 127.945) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_1_C_0)">
                                        <path id="_R_G_L_1_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M328 398.61 C328,398.61 328,446.23 328,446.23 C328,449.7 325.2,452.5 321.75,452.5 C321.75,452.5 274.25,452.5 274.25,452.5 C270.8,452.5 268,449.7 268,446.23 C268,446.23 268,398.61 268,398.61 C268,395.15 270.8,392.35 274.25,392.35 C274.25,392.35 283.46,392.26 283.46,392.26 C283.46,392.26 283.46,390.38 283.46,390.38 C283.46,389.76 284.08,388.5 285.33,388.5 C286.58,388.5 287.21,389.75 287.21,390.38 C287.21,390.38 287.21,397.89 287.21,397.89 C287.21,398.53 286.59,399.78 285.33,399.78 C284.08,399.78 283.46,398.53 283.46,397.9 C283.46,397.9 283.46,396.02 283.46,396.02 C283.46,396.02 275.5,396.1 275.5,396.1 C273.43,396.1 271.75,397.79 271.75,399.86 C271.75,399.86 271.75,444.98 271.75,444.98 C271.75,447.06 273.43,448.74 275.5,448.74 C275.5,448.74 320.5,448.74 320.5,448.74 C322.57,448.74 324.25,447.06 324.25,444.98 C324.25,444.98 324.25,399.86 324.25,399.86 C324.25,397.79 322.57,396.1 320.5,396.1 C320.5,396.1 312.62,396.1 312.62,396.1 C312.62,396.1 312.63,397.06 312.63,397.99 C312.63,398.61 312,399.86 310.75,399.86 C309.5,399.86 308.88,398.61 308.88,397.98 C308.88,397.98 308.87,396.1 308.87,396.1 C308.87,396.1 301.88,396.1 301.88,396.1 C300.84,396.1 300,395.26 300,394.23 C300,393.19 300.84,392.35 301.88,392.35 C301.88,392.35 308.87,392.35 308.87,392.35 C308.87,392.35 308.87,390.47 308.87,390.47 C308.87,389.83 309.5,388.5 310.75,388.5 C312,388.5 312.62,389.84 312.62,390.47 C312.62,390.47 312.62,392.35 312.62,392.35 C312.62,392.35 321.75,392.35 321.75,392.35 C325.2,392.35 328,395.15 328,398.61z "/>
                                    </g>
                                </g>
                                <g id="_R_G_L_0_G" transform=" translate(125.555, 126.412) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_0_C_0)">
                                        <path id="_R_G_L_0_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M305.86 420.29 C305.86,420.29 307.11,419.04 307.11,415.28 C307.11,409.01 303.36,407.76 298.36,407.76 C298.36,407.76 287.11,407.76 287.11,407.76 C287.11,407.76 287.11,434.08 287.11,434.08 C287.11,434.08 294.61,434.08 294.61,434.08 C294.61,434.08 294.61,441.6 294.61,441.6 C294.61,441.6 298.36,441.6 298.36,441.6 C298.36,441.6 298.36,434.08 298.36,434.08 C302.71,434.08 305.73,434.08 307.98,431.3 C309.07,429.95 309.62,428.24 309.61,426.5 C309.61,425.58 309.51,424.67 309.3,424.05 C308.73,422.65 308.36,421.55 305.86,420.29z  M302.11 430.32 C302.11,430.32 298.36,430.32 298.36,430.32 C298.36,430.32 298.36,426.56 298.36,426.56 C298.36,424.48 300.03,422.8 302.11,422.8 C304.13,422.8 305.86,424.43 305.86,426.56 C305.86,428.78 304.03,430.32 302.11,430.32z  M299.07 419.95 C298.43,420.26 297.82,420.63 297.26,421.05 C295.87,422.1 294.61,423.58 294.61,426.56 C294.61,426.56 294.61,430.32 294.61,430.32 C294.61,430.32 290.86,430.32 290.86,430.32 C290.86,430.32 290.86,411.52 290.86,411.52 C290.86,411.52 298.36,411.52 298.36,411.52 C301.35,411.52 303.36,412.77 303.36,415.28 C303.36,417.58 301.65,418.68 299.07,419.95z "/>
                                    </g>
                                </g>
                            </g>
                            <g id="time_group"/>
                        </svg>
                    </div>
                </div>                 
            </div>                    
            <?php
        }

        function bookingpress_frontend_add_appointment_data_variables_func($bookingpress_front_vue_data_fields){
            global  $bookingpress_services,$BookingPress;
            $bookingpress_front_vue_data_fields['is_custom_duration_addon'] = $this->is_addon_activated();
            $bookingpress_all_service_data = $bookingpress_front_vue_data_fields['all_services_data'];
            $bookingpress_all_services_data_with_meta = (isset($bookingpress_front_vue_data_fields['bookingpress_all_services_data']))?$bookingpress_front_vue_data_fields['bookingpress_all_services_data']:'';            
            foreach( $bookingpress_all_service_data as $key => $value ) {
                $bookingpress_service_id = !empty($value['bookingpress_service_id']) ?  intval($value['bookingpress_service_id']) : 0 ;

                if($bookingpress_service_id && !empty($bookingpress_all_services_data_with_meta) && isset($bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'])){
                    $enable_custom_service_duration = $bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'];
                    $enable_custom_service_duration = (($enable_custom_service_duration == 1) || ($enable_custom_service_duration == 'true'))?'true':'false';
                }else{
                    $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                }                

                $enable_custom_service_duration = !empty($enable_custom_service_duration) && $enable_custom_service_duration == 'true' ? true : false;
                $bookingpress_front_vue_data_fields['all_services_data'][$key]['enable_custom_service_duration'] = $enable_custom_service_duration;
            }            
            $bookingpress_front_vue_data_fields['appointment_step_form_data']['custom_service_duration_value'] = '';
            $bookingpress_front_vue_data_fields['appointment_step_form_data']['custom_service_duration_real_value'] = '';
            $bookingpress_front_vue_data_fields['appointment_step_form_data']['custom_service_real_price'] = '';

            $bookingpress_front_vue_data_fields['is_display_custom_duration_loader'] = false;
            $bookingpress_front_vue_data_fields['is_display_custom_duration_day_loader'] = false;

            $bookingpress_front_vue_data_fields['bookingpress_custom_service_durations_slot'] = array();
            //$bookingpress_front_vue_data_fields['is_display_time_slot_area'] = false;

            if(!empty($bookingpress_front_vue_data_fields['services_data'])) {
                foreach($bookingpress_front_vue_data_fields['services_data'] as $key => $val) {
                    $bookingpress_service_id = !empty($val['bookingpress_service_id']) ?  intval($val['bookingpress_service_id']) : 0 ;

                    if($bookingpress_service_id && !empty($bookingpress_all_services_data_with_meta) && isset($bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'])){
                        $enable_custom_service_duration = $bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'];
                        $enable_custom_service_duration = (($enable_custom_service_duration == 1) || ($enable_custom_service_duration == 'true'))?'true':'false';                        
                    }else{
                        $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                    }  

                    /*
                    $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                    */

                    $enable_custom_service_duration = !empty($enable_custom_service_duration) && $enable_custom_service_duration == 'true' ? true : false;
                    $bookingpress_front_vue_data_fields['services_data'][$key]['enable_custom_service_duration'] = $enable_custom_service_duration;
                }
            }

            if(!empty($bookingpress_front_vue_data_fields['bpa_services_data_from_categories'])) {
                foreach($bookingpress_front_vue_data_fields['bpa_services_data_from_categories'] as $key => $val) {
                    foreach($val as $key2 => $val2) {
                        $bookingpress_service_id = !empty($val2['bookingpress_service_id']) ?  intval($val2['bookingpress_service_id']) : 0 ;
                        
                        if($bookingpress_service_id && !empty($bookingpress_all_services_data_with_meta) && isset($bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'])){
                            $enable_custom_service_duration = $bookingpress_all_services_data_with_meta[$bookingpress_service_id]['services_meta']['enable_custom_service_duration'];
                            $enable_custom_service_duration = (($enable_custom_service_duration == 1) || ($enable_custom_service_duration == 'true'))?'true':'false';                        
                        }else{
                            $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                        } 

                        /*
                        $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                        */
                        
                        $enable_custom_service_duration = !empty($enable_custom_service_duration) && $enable_custom_service_duration == 'true' ? true : false;
                        $bookingpress_front_vue_data_fields['bpa_services_data_from_categories'][$key][$key2]['enable_custom_service_duration'] = $enable_custom_service_duration;
                    }
                }
            }

            $custom_service_duration_title = $BookingPress->bookingpress_get_customize_settings('custom_service_duration_title', 'booking_form');			
            $custom_service_description_title = $BookingPress->bookingpress_get_customize_settings('custom_service_description_title', 'booking_form');
            $custom_please_select_title = $BookingPress->bookingpress_get_customize_settings('custom_please_select_title', 'booking_form');
            $custom_price_title = $BookingPress->bookingpress_get_customize_settings('custom_price_title', 'booking_form');
            $custom_duration_title = $BookingPress->bookingpress_get_customize_settings('custom_duration_title', 'booking_form');
            $no_custom_service_duration_selected_title = $BookingPress->bookingpress_get_customize_settings('no_custom_service_duration_selected_title', 'booking_form');

            $bookingpress_front_vue_data_fields['custom_service_duration_title'] = !empty($custom_service_duration_title) ? stripslashes_deep($custom_service_duration_title) : '';
            $bookingpress_front_vue_data_fields['custom_service_description_title'] = !empty($custom_service_description_title) ? stripslashes_deep($custom_service_description_title) : '';
            $bookingpress_front_vue_data_fields['custom_please_select_title'] = !empty($custom_please_select_title) ? stripslashes_deep($custom_please_select_title) : '';
            $bookingpress_front_vue_data_fields['custom_price_title'] = !empty($custom_price_title) ? stripslashes_deep($custom_price_title) : '';
            $bookingpress_front_vue_data_fields['custom_duration_title'] = !empty($custom_duration_title) ? stripslashes_deep($custom_duration_title) : '';

            if(!empty($bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'])) {
                $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_front_vue_data_fields['appointment_step_form_data']['selected_service'],'enable_custom_service_duration');                
                if(!empty($enable_custom_service_duration) && $enable_custom_service_duration == 'true') {
                    $bookingpress_front_vue_data_fields['appointment_step_form_data']['enable_custom_service_duration'] = true;
                    $bookingpress_custom_service_durations_slot = $this->bookingpress_modify_disable_date_data_func($bookingpress_front_vue_data_fields,$bookingpress_front_vue_data_fields['appointment_step_form_data']);
                    $bookingpress_front_vue_data_fields['bookingpress_custom_service_durations_slot'] = $bookingpress_custom_service_durations_slot;
                }
            }

            $bookingpress_front_vue_data_fields['custom_service_duration_error_msg'] = !empty($no_custom_service_duration_selected_title) ? stripslashes_deep($no_custom_service_duration_selected_title) : 'Please select custom service duration value';

            return $bookingpress_front_vue_data_fields;
        }

        function bookingpress_dynamic_validation_for_step_change_for_custom_duration( $bookingpress_dynamic_validation_for_step_change ){

            /* $bookingpress_dynamic_validation_for_step_change .= '
                let current_step = vm.bookingpress_current_tab;
                let is_custom_service_duration = vm.appointment_step_form_data.enable_custom_service_duration;
                if( "datetime" == current_step && current_step != next_tab && current_tab != previous_tab){
                    if( true == is_custom_service_duration && "" == vm.appointment_step_form_data.custom_service_duration_value ){
                        vm.bookingpress_set_error_msg( vm.custom_service_duration_error_msg );
                        bookingpress_is_validate = 1;
                    }
                }
            '; */

            return $bookingpress_dynamic_validation_for_step_change;
        }

        function bookingpress_add_pro_booking_form_methods_func($bookingpress_vue_methods_data) { 

            $bookingpress_vue_methods_data .='
            bookingpress_change_custom_duration(event) {
                const vm = this;              
               
                vm.bookingpress_custom_service_durations_slot.forEach(function(item,index,arr){
                    if(item.value == event ) {
                        vm.appointment_step_form_data.selected_service_price = item.service_price;
                        vm.appointment_step_form_data.service_price_without_currency = item.service_price_without_currency;                         
                        vm.appointment_step_form_data.custom_service_duration_real_value = item.real_value;
                        vm.appointment_step_form_data.custom_service_real_price = item.real_price;
                        if(vm.appointment_step_form_data.selected_service_duration_unit == "d"){

                            vm.appointment_step_form_data.selected_service_price = vm.bookingpress_price_with_currency_symbol(item.service_price_without_currency);
                            vm.appointment_step_form_data.selected_service_duration = (item.real_value)/(24*60);
                            vm.appointment_step_form_data.selected_date = "";
                        }                        
                    }
                });
                if(vm.appointment_step_form_data.selected_service_duration_unit == "d"){ 
                    vm.is_display_custom_duration_day_loader = true;                   
                    vm.v_calendar_attributes = [];
                    vm.bookingpress_booking_before_block_date = [];
                    vm.bookingpress_disable_date();                                        
                }else{
                    vm.get_date_timings();
                }                 
            },
            bookingpress_change_custom_duration_first(event){
                const vm = this;                
                vm.bookingpress_custom_service_durations_slot.forEach(function(item,index,arr){
                    if(item.value == event ) {
                        vm.appointment_step_form_data.selected_service_price = item.service_price;
                        vm.appointment_step_form_data.service_price_without_currency = item.service_price_without_currency; 
                        vm.appointment_step_form_data.custom_service_duration_real_value = item.real_value;
                        vm.appointment_step_form_data.custom_service_real_price = item.real_price;
                        if(vm.appointment_step_form_data.selected_service_duration_unit == "d"){

                            vm.appointment_step_form_data.selected_service_price = vm.bookingpress_price_with_currency_symbol(item.service_price_without_currency);
                            vm.appointment_step_form_data.selected_service_duration = (item.real_value)/(24*60);
                            vm.appointment_step_form_data.selected_date = "";
                        }
                    }
                });
                if(vm.appointment_step_form_data.selected_service_duration_unit == "d"){
                    vm.is_display_custom_duration_loader = true;
                    vm.v_calendar_attributes = [];
                    vm.bookingpress_booking_before_block_date = [];
                    vm.bookingpress_disable_date();                    
                }else{
                    vm.is_display_custom_duration_loader = true;
                    vm.get_date_timings();
                }                
            },';
            return $bookingpress_vue_methods_data;
        }

        function bookingpress_before_selecting_booking_service_func($bookingpress_before_selecting_booking_service_data) {
            $bookingpress_before_selecting_booking_service_data.='

                if( typeof vm.appointment_step_form_data.cart_items == "undefined" ){
                    vm.all_services_data.forEach(function(currentvalue5,index5,arr5) {
                        if(selected_service_id != "" && currentvalue5.bookingpress_service_id == selected_service_id ) {                            
                            vm.appointment_step_form_data.enable_custom_service_duration = currentvalue5.enable_custom_service_duration;
                            vm.appointment_step_form_data.custom_service_duration_value = "";
                            vm.appointment_step_form_data.custom_service_duration_real_value = "";
                            vm.appointment_step_form_data.custom_service_real_price = "";
                        }                    
                    });
                } else {
                    if( 0 > vm.appointment_step_form_data.cart_item_edit_index ){                        
                        vm.all_services_data.forEach(function(currentvalue5,index5,arr5) {
                            if(selected_service_id != "" && currentvalue5.bookingpress_service_id == selected_service_id ) {
                                vm.appointment_step_form_data.enable_custom_service_duration = currentvalue5.enable_custom_service_duration;
                                vm.appointment_step_form_data.custom_service_duration_value = "";
                                vm.appointment_step_form_data.custom_service_duration_real_value = "";
                                vm.appointment_step_form_data.custom_service_real_price = "";

                            }                    
                        });
                    } else {
                        /** Reputelog - need to check custom service duration with cart add-on while edit appointment from cart */
                        let cart_edit_index_id = vm.appointment_step_form_data.cart_item_edit_index;
                        let cart_item = vm.appointment_step_form_data.cart_items[ cart_edit_index_id ];
                        let bookingpress_edit_cart_service_id = typeof cart_item.bookingpress_service_id != "undefined" ?cart_item.bookingpress_service_id:0;

                        if(bookingpress_edit_cart_service_id != selected_service_id){

                            vm.all_services_data.forEach(function(currentvalue5,index5,arr5) {
                                if(selected_service_id != "" && currentvalue5.bookingpress_service_id == selected_service_id ){

                                    vm.appointment_step_form_data.cart_items[cart_edit_index_id].enable_custom_service_duration = currentvalue5.enable_custom_service_duration;                                    
                                    vm.appointment_step_form_data.enable_custom_service_duration = currentvalue5.enable_custom_service_duration;
                                    vm.appointment_step_form_data.custom_service_duration_value = "";
                                    vm.appointment_step_form_data.custom_service_duration_real_value = "";
                                    vm.appointment_step_form_data.custom_service_real_price = "";
    
                                }                    
                            });                            

                        }else{

                            vm.appointment_step_form_data.enable_custom_service_duration = typeof cart_item.enable_custom_service_duration != "undefined" ? cart_item.enable_custom_service_duration : false;
                            vm.appointment_step_form_data.custom_service_duration_value = parseInt( cart_item.custom_service_duration_value );
                            vm.appointment_step_form_data.custom_service_duration_real_value = parseInt( cart_item.custom_service_duration_value );
                            vm.appointment_step_form_data.custom_service_real_price = parseFloat( cart_item.custom_service_real_price );

                        }

                    }
                }
            ';
            return $bookingpress_before_selecting_booking_service_data;
        }

        /* function bookingpress_modify_disable_date_data_func( $response,$appointment_data_obj = array() ){

            $appointment_data_obj = !empty($_REQUEST['appointment_data_obj']) ? $_REQUEST['appointment_data_obj'] : $appointment_data_obj;
            $bookingpress_custom_service_durations_slot = false;
            if( !empty($appointment_data_obj['enable_custom_service_duration']) && $appointment_data_obj['enable_custom_service_duration'] == 'true' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookingpress_get_disable_date' ) {
                $response['prevent_next_month_check'] = true;
                $response['empty_front_timings'] = false;                    
            }

            return $response;
        } */

        function bookingpress_modify_disable_date_data_func_old($response,$appointment_data_obj = array()) {

            global $bookingpress_services,$BookingPress,$bookingpress_pro_payment_gateways,$tbl_bookingpress_services,$wpdb, $bookingpress_service_extra, $BookingPress;
            $bookingpress_custom_service_durations_slot = array();

			$appointment_data_obj = !empty($_REQUEST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['appointment_data_obj'] ) : $appointment_data_obj; // phpcs:ignore

            if( !empty($appointment_data_obj['enable_custom_service_duration']) && $appointment_data_obj['enable_custom_service_duration'] == 'true') {                                
                $bookingpress_service_id = !empty($_REQUEST['selected_service']) ? intval($_REQUEST['selected_service']) : '';

                $bookingpress_service_id = !empty($_REQUEST['service_id']) ? intval($_REQUEST['service_id']) : '';

                $bookingpress_service_id = !empty($bookingpress_service_id) ? $bookingpress_service_id : $appointment_data_obj['selected_service'];

                if(!empty($bookingpress_service_id)) {
                    
                    $custom_service_max_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_max_duration');

                    $custom_service_min_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_min_duration');

                    $service_duration_unit = !empty($appointment_data_obj['selected_service_duration_unit']) ? sanitize_text_field($appointment_data_obj['selected_service_duration_unit']) : '';

                    $selected_service_duration = !empty($appointment_data_obj['selected_service_duration']) ? intval($appointment_data_obj['selected_service_duration']) : "" ;

                    $service_price_without_currency = !empty($appointment_data_obj['service_price_without_currency']) ? ($appointment_data_obj['service_price_without_currency']) : "" ;

                    if(!isset($appointment_data_obj['selected_service']) && isset($_POST['selected_service'])) { // phpcs:ignore 
                        $appointment_data_obj['selected_service'] = intval($_POST['selected_service']); // phpcs:ignore
                    }
                    
                    // Get service data
                    $bookingpress_service_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_service_duration_unit,bookingpress_service_duration_val,bookingpress_service_price FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $bookingpress_service_id ),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm                    
                    
         
                    //if(empty($service_duration_unit)) {
                        $service_duration_unit = $bookingpress_service_data['bookingpress_service_duration_unit'];
                        $appointment_data_obj['selected_service_duration_unit'] = $service_duration_unit;
                    //}
                    //if(empty($selected_service_duration)) {
                        $selected_service_duration = intval($bookingpress_service_data['bookingpress_service_duration_val']);
                        $appointment_data_obj['selected_service_duration'] = $selected_service_duration;
                    //}
                    //if(empty($service_price_without_currency)) {
                        $service_price_without_currency = $bookingpress_service_data['bookingpress_service_price'];
                        $appointment_data_obj['service_price_without_currency'] = $service_price_without_currency;
                    //}

                    if($service_duration_unit == 'h' ) {
                        $selected_service_duration = $selected_service_duration * 60; 
                    } else if($service_duration_unit == 'd'){
                        //$selected_service_duration = $selected_service_duration * 60 * 24; 
                    }
                    
                    $service_slot_step = $selected_service_duration;
                    if(!empty($custom_service_max_duration) && !empty($service_duration_unit) && !empty($service_slot_step)) {
                        if($service_duration_unit != 'd') {
                            while($service_slot_step <= $custom_service_max_duration ) {
                                if(empty($custom_service_min_duration) || $custom_service_min_duration <= $service_slot_step ) {
                                    $appointment_data_obj['custom_service_duration_real_value'] = $service_slot_step; 
                                    $service_slot_step_value = $service_slot_step;
                                    //$service_slot_step_value = apply_filters( 'bookingpress_modify_service_timeslot',$service_slot_step,$bookingpress_service_id,$service_duration_unit);
                                    $service_slot_step_value = $bookingpress_service_extra->bookingpress_modify_service_timeslot_with_service_extras( $service_slot_step, $bookingpress_service_id, $service_duration_unit );
                                    $service_duration_text = $this->bookingpress_get_duration_text_using_minute($service_slot_step_value,$service_duration_unit);

                                    $appointment_calculate_step_form_data = $bookingpress_pro_payment_gateways->bookingpress_recalculate_appointment_data_func($appointment_data_obj);                                
                                    $appointment_calculate_step_form_data = !empty($appointment_calculate_step_form_data) ? json_decode($appointment_calculate_step_form_data,true) :  array();                                
                                    $bookingpress_custom_service_durations_slot[] = array (
                                        'value' => $service_slot_step_value,
                                        'text'  => $service_duration_text,
                                        'service_price_without_currency' => $appointment_calculate_step_form_data['appointment_data']['service_price_without_currency'],
                                        'service_price' => $appointment_calculate_step_form_data['appointment_data']['selected_service_price'],
                                        'real_value' => $service_slot_step,
                                        'real_price' => $appointment_calculate_step_form_data['appointment_data']['bookingpress_custom_service_duration_price'],
                                    );                                
                                }                               
                                $service_slot_step += $selected_service_duration;                                
                            }
                        }
                    }
                }
            }

            if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookingpress_get_disable_date') {
                if( !empty( $bookingpress_custom_service_durations_slot ) ){
                        $response['prevent_next_month_check'] = true;
                        $response['empty_front_timings'] = false;
                }
                $response['bookingpress_custom_service_durations_slot'] = $bookingpress_custom_service_durations_slot;
                return $response;
            } else {
                return $bookingpress_custom_service_durations_slot;
            }
        }

        function bookingpress_modify_disable_date_data_func($response,$appointment_data_obj = array()) {

            global $bookingpress_services,$BookingPress,$bookingpress_pro_payment_gateways,$tbl_bookingpress_services,$wpdb, $bookingpress_service_extra, $BookingPress;
            $bookingpress_custom_service_durations_slot = array();

			$appointment_data_obj = !empty($_REQUEST['appointment_data_obj']) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['appointment_data_obj'] ) : $appointment_data_obj; // phpcs:ignore

            if( !empty($appointment_data_obj['enable_custom_service_duration']) && $appointment_data_obj['enable_custom_service_duration'] == 'true') {                                
                $bookingpress_service_id = !empty($_REQUEST['selected_service']) ? intval($_REQUEST['selected_service']) : '';

                $bookingpress_service_id = !empty($_REQUEST['service_id']) ? intval($_REQUEST['service_id']) : '';

                $bookingpress_service_id = !empty($bookingpress_service_id) ? $bookingpress_service_id : $appointment_data_obj['selected_service'];

                if(!empty($bookingpress_service_id)) {
                    
                    $custom_service_max_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_max_duration');

                    $custom_service_min_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'custom_service_min_duration');

                    $service_duration_unit = !empty($appointment_data_obj['selected_service_duration_unit']) ? sanitize_text_field($appointment_data_obj['selected_service_duration_unit']) : '';

                    $selected_service_duration = !empty($appointment_data_obj['selected_service_duration']) ? intval($appointment_data_obj['selected_service_duration']) : "" ;

                    $service_price_without_currency = !empty($appointment_data_obj['service_price_without_currency']) ? ($appointment_data_obj['service_price_without_currency']) : "" ;

                    if(!isset($appointment_data_obj['selected_service']) && isset($_POST['selected_service'])) {//phpcs:ignore WordPress.Security.NonceVerification
                        $appointment_data_obj['selected_service'] = intval($_POST['selected_service']); //phpcs:ignore WordPress.Security.NonceVerification
                    }
                    
                    // Get service data
                    $bookingpress_service_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_service_duration_unit,bookingpress_service_duration_val,bookingpress_service_price FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $bookingpress_service_id ),ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm                    
                    
         
                    //if(empty($service_duration_unit)) {
                        $service_duration_unit = $bookingpress_service_data['bookingpress_service_duration_unit'];
                        $appointment_data_obj['selected_service_duration_unit'] = $service_duration_unit;
                    //}
                    //if(empty($selected_service_duration)) {
                        $selected_service_duration = intval($bookingpress_service_data['bookingpress_service_duration_val']);
                        $appointment_data_obj['selected_service_duration'] = $selected_service_duration;
                    //}
                    //if(empty($service_price_without_currency)) {
                        $service_price_without_currency = $bookingpress_service_data['bookingpress_service_price'];
                        $appointment_data_obj['service_price_without_currency'] = $service_price_without_currency;
                    //}

                    if($service_duration_unit == 'h' ) {
                        $selected_service_duration = $selected_service_duration * 60; 
                    } else if($service_duration_unit == 'd'){
                        $selected_service_duration = $selected_service_duration * 60 * 24; 
                    }

                    //$service_base_price = $appointment_data_obj['base_price_without_currency'];

                    $service_extras_price = 0;
                    $service_selected_qty = 1;

                    if( !empty( $appointment_data_obj['bookingpress_selected_extra_details'] ) ){
                        foreach( $appointment_data_obj['bookingpress_selected_extra_details'] as $service_extra_id => $service_extra_details ){
                            if( 'true' == $service_extra_details['bookingpress_is_selected'] ){
                                $service_extras_price += ( $service_extra_details['bookingpress_extra_price'] * $service_extra_details['bookingpress_selected_qty'] );
                            }
                        }
                    }

                    if( !empty( $appointment_data_obj['bookingpress_selected_bring_members'] ) ){
                        $service_selected_qty = $appointment_data_obj['bookingpress_selected_bring_members'];
                    }

                    /* echo 'final extra amount ====> ' . $service_extras_price;
                    echo "<pre>";
                    print_r( $appointment_data_obj );
                    echo "</pre>";
                    die; */
                    
                    $service_slot_step = $selected_service_duration;
                    if(!empty($custom_service_max_duration) && !empty($service_duration_unit) && !empty($service_slot_step)) {
                        if($service_duration_unit != 'd') {
                            while($service_slot_step <= $custom_service_max_duration ) {
                                if(empty($custom_service_min_duration) || $custom_service_min_duration <= $service_slot_step ) {
                                    $appointment_data_obj['custom_service_duration_real_value'] = $service_slot_step; 
                                    $service_slot_step_value = $service_slot_step;
                                    //$service_slot_step_value = apply_filters( 'bookingpress_modify_service_timeslot',$service_slot_step,$bookingpress_service_id,$service_duration_unit);
                                    $service_slot_step_value = $bookingpress_service_extra->bookingpress_modify_service_timeslot_with_service_extras( $service_slot_step, $bookingpress_service_id, $service_duration_unit );
                                    $service_duration_text = $this->bookingpress_get_duration_text_using_minute($service_slot_step_value,$service_duration_unit);
                                    //echo $service_slot_step_value.' -- '. $service_duration_text.' -<br/>';
                                    
                                    /*$appointment_calculate_step_form_data = $bookingpress_pro_payment_gateways->bookingpress_recalculate_appointment_data_func($appointment_data_obj);
                                    $appointment_calculate_step_form_data = !empty($appointment_calculate_step_form_data) ? json_decode($appointment_calculate_step_form_data,true) :  array();
                                    /* echo "<pre>";
                                    print_r( $appointment_calculate_step_form_data['appointment_data']['bookingpress_custom_service_duration_price'] );
                                    echo "</pre>"; */
                                    /**die; */

                                    $custom_service_duration_price = $service_price_without_currency;

                                    //echo '1  ---> ' . $custom_service_duration_price;
                                    $custom_service_duration_price = $this->bookingpress_modify_recalculate_amount_before_calculation_func($service_price_without_currency,$appointment_data_obj);
                                    /* echo '<br/>2 after function ---> '. $custom_service_duration_price; */

                                    $custom_service_duration_real_price = $custom_service_duration_price;

                                    $custom_service_duration_price = $custom_service_duration_price * $service_selected_qty;
                                    //echo '<br/>3 after quantity ---> '.$custom_service_duration_price;
                                    
                                    $custom_service_duration_price += $service_extras_price;
                                    //echo '<br/>4 after extra ---> '.$custom_service_duration_price . '<br/>';

                                    $bookingpress_custom_service_durations_slot[] = array (
                                        'value' => $service_slot_step_value,
                                        'text'  => $service_duration_text,
                                        'service_price_without_currency' => $custom_service_duration_price,
                                        'service_price' => $BookingPress->bookingpress_price_formatter_with_currency_symbol($custom_service_duration_price),
                                        'real_value' => $service_slot_step,
                                        'real_price' => $custom_service_duration_real_price,
                                        'service_duration_unit' => '',
                                    );
                                    //echo '<br/><br/><br/>';
                                }
                                $service_slot_step += $selected_service_duration;                                
                            }
                            //die;
                        }
                    }
                }
            }

            if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'bookingpress_get_disable_date') {
                if( !empty( $bookingpress_custom_service_durations_slot ) ){
                        $response['prevent_next_month_check'] = true;
                        $response['empty_front_timings'] = false;                
                }                
                if(isset($response['bookingpress_custom_service_durations_slot'])){
                    if(!empty($response['bookingpress_custom_service_durations_slot'])){
                        $bookingpress_custom_service_durations_slot = $response['bookingpress_custom_service_durations_slot'];
                    }
                }
                $response['bookingpress_custom_service_durations_slot'] = $bookingpress_custom_service_durations_slot;
                return $response;
            } else {
                return $bookingpress_custom_service_durations_slot;
            }
        }

        function bookingpress_disable_date_vue_data_modify_func($bookingpress_disable_date_vue_data) {
            $bookingpress_disable_date_vue_data.= '
                if(response.data.bookingpress_custom_service_durations_slot !== "undefined"){
                    vm.bookingpress_custom_service_durations_slot = response.data.bookingpress_custom_service_durations_slot;
                }  
                setTimeout(function(){
                    vm.is_display_custom_duration_loader = false; 
                    vm.is_display_custom_duration_day_loader = false; 
                },1500);  
                                           
            ';            
            return $bookingpress_disable_date_vue_data;            
        }

        function bookingpress_after_selecting_booking_service_func($bookingpress_after_selecting_booking_service_data) {
            $bookingpress_after_selecting_booking_service_data .= '
                setTimeout(function(){
                    vm.is_display_custom_duration_loader = false;
                },1500);
            ';
            return $bookingpress_after_selecting_booking_service_data;
        }

        function bookingpress_modify_recalculate_amount_before_calculation_func($final_payable_amount,$bookingpress_appointment_details) {
            global $wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_custom_staffmembers_service_durations,$BookingPress,$bookingpress_pro_staff_members,$tbl_bookingpress_services,$tbl_bookingpress_staffmembers_services;           

            if(!empty( $bookingpress_appointment_details['enable_custom_service_duration']) && $bookingpress_appointment_details['enable_custom_service_duration']  == 'true') {

                $custom_service_duration_value = $bookingpress_appointment_details['custom_service_duration_real_value'] ? $bookingpress_appointment_details['custom_service_duration_real_value'] : '';             

                $bpa_service_id= !empty($_REQUEST['service_id']) ? intval($_REQUEST['service_id']) : 0;
                $bookingpress_service_id = !empty($bookingpress_appointment_details['selected_service']) ? intval($bookingpress_appointment_details['selected_service']) : $bpa_service_id;

                $selected_service_duration = !empty($bookingpress_appointment_details['selected_service_duration']) ? intval($bookingpress_appointment_details['selected_service_duration']) : "" ;

                $selected_service_duration_unit = !empty($bookingpress_appointment_details['selected_service_duration_unit']) ? $bookingpress_appointment_details['selected_service_duration_unit'] : "" ;
                $services_data = array();
                if(!empty($bookingpress_service_id)) {
                    $services_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_service_price FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $bookingpress_service_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm
                }
                $service_price = $services_data['bookingpress_service_price'];
                $bookingpress_selected_staffmember = !empty($bookingpress_appointment_details['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_details['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;

                if(!empty($bookingpress_selected_staffmember) && $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation()){
                    $bookingpress_staffmember_assigned_service_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers_services} WHERE bookingpress_staffmember_id = %d AND bookingpress_service_id = %d", $bookingpress_selected_staffmember, $bookingpress_service_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers_services is a table name. false alarm
					if ( ! empty( $bookingpress_staffmember_assigned_service_details ) && isset($bookingpress_staffmember_assigned_service_details['bookingpress_service_price']) ) {
						$service_price = floatval( $bookingpress_staffmember_assigned_service_details['bookingpress_service_price'] );
					}
                }
                if($selected_service_duration_unit == 'h' ) {
                    $selected_service_duration = $selected_service_duration * 60; 
                } else if($selected_service_duration_unit == 'd'){
                    $selected_service_duration = $selected_service_duration * 60 * 24; 
                }
                
                if(!empty($custom_service_duration_value)) {

                    if(!empty($bookingpress_selected_staffmember) && $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {
                        $bookingpress_service_duration_data = $wpdb->get_row($wpdb->prepare("SELECT ssd.bookingpress_staffmember_price FROM ".$tbl_bookingpress_custom_service_durations." as sd LEFT JOIN ".$tbl_bookingpress_custom_staffmembers_service_durations." as ssd ON sd.bookingpress_custom_service_duration_id = ssd.bookingpress_custom_service_duration_id WHERE bookingpress_service_duration_val <= %d AND sd.bookingpress_service_id = %d AND bookingpress_staffmember_id = %d order by bookingpress_service_duration_val DESC",$custom_service_duration_value,$bookingpress_service_id,$bookingpress_selected_staffmember),ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm

                    } else {

                        $bookingpress_service_duration_data = $wpdb->get_row($wpdb->prepare("SELECT sd.bookingpress_service_duration_price FROM ".$tbl_bookingpress_custom_service_durations." as sd  WHERE bookingpress_service_duration_val <= %s AND sd.bookingpress_service_id = %d order by bookingpress_service_duration_val DESC",$custom_service_duration_value,$bookingpress_service_id),ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm
                    }
       
                    if(!empty($bookingpress_service_duration_data)) {
                        $bookingpress_service_duration_price = '';
                        if(!empty($bookingpress_selected_staffmember) && $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation()  && isset( $bookingpress_service_duration_data['bookingpress_staffmember_price'])) {
                            $bookingpress_service_duration_price = $bookingpress_service_duration_data['bookingpress_staffmember_price'];
                        } else{
                            $bookingpress_service_duration_price = $bookingpress_service_duration_data['bookingpress_service_duration_price'];
                        }
                        $bookingpress_appointment_details['service_price_without_currency'] = $bookingpress_service_duration_price;
                    } else {                        
                        if($custom_service_duration_value >= $selected_service_duration) {
                            $custom_service_duration_value = intval($custom_service_duration_value);
                            $selected_service_duration = intval($selected_service_duration);
                            $number_of_time = $custom_service_duration_value / $selected_service_duration; 
                            $service_price = $service_price * $number_of_time;
                            $bookingpress_appointment_details['service_price_without_currency'] = $service_price;;
                        } 
                    }                    
                }

                $final_payable_amount = $bookingpress_appointment_details['service_price_without_currency'];
            }            

            return floatval($final_payable_amount);
        }

        function bookingpress_dynamic_add_params_for_timeslot_request_service_extra( $bookingpress_dynamic_add_params_for_timeslot_request ){
			$bookingpress_dynamic_add_params_for_timeslot_request .= 'postData.enable_custom_service_duration = vm.appointment_step_form_data.enable_custom_service_duration;';
            $bookingpress_dynamic_add_params_for_timeslot_request .= 'postData.custom_service_duration_value = vm.appointment_step_form_data.custom_service_duration_value;';            
			return $bookingpress_dynamic_add_params_for_timeslot_request;
		}

        function bookingpress_modify_entry_data_before_insert_func($bookingpress_entry_details, $posted_data) {
            global $wpdb,$tbl_bookingpress_services;
            if(empty($posted_data['cart_items'])) {
                if(isset($posted_data['enable_custom_service_duration']) && ( $posted_data['enable_custom_service_duration']  == 'true' )) {

                    $bookingpress_service_id = !empty($posted_data['selected_service']) ? $posted_data['selected_service'] : 0;
                    $bookingpress_service_duration_unit = '';
                    if(!empty($bookingpress_service_id) && empty($posted_data['selected_service_duration_unit'])) {
                        $bookingpress_service_duration_unit = $wpdb->get_var($wpdb->prepare('SELECT bookingpress_service_duration_unit FROM ' . $tbl_bookingpress_services.' WHERE bookingpress_service_id = %d',$bookingpress_service_id)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm
                    } else {
                        $bookingpress_service_duration_unit = $posted_data['selected_service_duration_unit'];
                    }
                    $bookingpress_entry_details['bookingpress_enable_custom_duration'] = 1;
                    if(isset($posted_data['custom_service_real_price'])) {
                        $bookingpress_entry_details['bookingpress_service_price'] = floatval($posted_data['custom_service_real_price']);
                    }
                    if(!empty($posted_data['custom_service_duration_real_value']) && !empty($bookingpress_service_duration_unit)) {
                        $service_duration_unit = $posted_data['selected_service_duration_unit'];
                        $service_duration_value = intval($posted_data['custom_service_duration_real_value']);                    
                        if($service_duration_unit == 'h') {
                            $service_duration_value = $service_duration_value / 60;
                        } elseif($service_duration_unit == 'd') {
                            $service_duration_value = $service_duration_value / 1440;
                        }                    
                        $bookingpress_entry_details['bookingpress_service_duration_val'] = $service_duration_value;
                    }
                    if(isset($posted_data['custom_service_duration_value'])) {
                        $bookingpress_entry_details['bookingpress_custom_duration_val'] = intval($posted_data['custom_service_duration_value']);
                    }    
                }
            }
            return $bookingpress_entry_details;
        }

        function bookingpress_modify_cart_entry_data_before_insert_func($bookingpress_entry_details,$post,$posted_data ) {
            if(!empty($post['cart_items'])) {
                if(isset($posted_data['enable_custom_service_duration']) && ( $posted_data['enable_custom_service_duration']  == 'true')) {
                    $bookingpress_entry_details['bookingpress_enable_custom_duration'] = 1;
                    if(isset($posted_data['custom_service_real_price'])) {
                        $bookingpress_entry_details['bookingpress_service_price'] = floatval($posted_data['custom_service_real_price']);                    
                    } 
                   
                    $bookingpress_entry_details['bookingpress_service_duration_val'] = $posted_data['bookingpress_service_duration_val'];                    
                    if(isset($posted_data['custom_service_duration_value'])) {
                        $bookingpress_entry_details['bookingpress_custom_duration_val'] = $posted_data['custom_service_duration_value'];
                    }
                }
            }            
            return $bookingpress_entry_details;
        }

        function bookingpress_modify_appointment_booking_fields_before_insert_func($appointment_booking_fields, $entry_data ) {
            global $wpdb,$tbl_bookingpress_appointment_meta;
            
            $appointment_booking_fields['bookingpress_enable_custom_duration'] = $entry_data['bookingpress_enable_custom_duration'];            
            if(!empty($entry_data['bookingpress_entry_id']) && isset($entry_data['bookingpress_custom_duration_val'])) {
                $custom_service_duration = $entry_data['bookingpress_custom_duration_val'];            
                $bookingpress_db_fields = array(
                    'bookingpress_entry_id' => $entry_data['bookingpress_entry_id'],
                    'bookingpress_appointment_id' => 0,
                    'bookingpress_appointment_meta_value' => $custom_service_duration,
                    'bookingpress_appointment_meta_key' => 'appointment_custom_duration_value',
                );
                $wpdb->insert($tbl_bookingpress_appointment_meta, $bookingpress_db_fields);                
            }
            return $appointment_booking_fields;
        }

        function bookingpress_modify_calculated_appointment_details_func($bookingpress_appointment_details) {
            global $wpdb,$BookingPress,$tbl_bookingpress_extra_services;

            if(!empty($bookingpress_appointment_details) && !empty($bookingpress_appointment_details['cart_items']) ) {
                $service_price_without_currency = $bookingpress_appointment_details['service_price_without_currency'];
                $service_price_without_currency  = apply_filters( 'bookingpress_modify_recalculate_amount_before_calculation', $service_price_without_currency, $bookingpress_appointment_details );
                $bookingpress_appointment_details['bookingpress_custom_service_duration_price'] = $service_price_without_currency;

                $total_payable_amount = $final_payable_amount = $service_price_without_currency;

				// Calculate Bring anyone with you module price
				$bookingpress_bring_anyone_module_price_arr = array();
				$bookingpress_selected_members              = ! empty( $bookingpress_appointment_details['bookingpress_selected_bring_members'] ) ? intval( $bookingpress_appointment_details['bookingpress_selected_bring_members'] ) - 1  : 0;

				if ( $bookingpress_selected_members > 0 ) {
					$bookingpress_bring_anyone_with_you_price = $final_payable_amount * $bookingpress_selected_members;
					array_push( $bookingpress_bring_anyone_module_price_arr, $bookingpress_bring_anyone_with_you_price );
				}

				// -------------------------------------------------------------------------------------------------------------

				// Calculate selected extra service prices
				// -------------------------------------------------------------------------------------------------------------
				$bookingpress_extra_service_price_arr = array();
				$bookingpress_extra_service_details = !empty($bookingpress_appointment_details['bookingpress_selected_extra_details']) ? array_map( array( $BookingPress, 'appointment_sanatize_field'), $bookingpress_appointment_details['bookingpress_selected_extra_details'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason $_POST contains mixed array and will be sanitized using 'appointment_sanatize_field' function
				if( is_array($bookingpress_extra_service_details) && !empty($bookingpress_extra_service_details) ){
					foreach($bookingpress_extra_service_details as $k => $v){
						if($v['bookingpress_is_selected'] == "true"){
							$bookingpress_extra_service_id = intval($k);
							$bookingpress_extra_service_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_extra_services} WHERE bookingpress_extra_services_id = %d", $bookingpress_extra_service_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_extra_services is a table name. false alarm

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

				// Add bring anyone with you price to final price
				if ( ! empty( $bookingpress_bring_anyone_module_price_arr ) && is_array( $bookingpress_bring_anyone_module_price_arr ) ) {
					foreach ( $bookingpress_bring_anyone_module_price_arr as $k2 => $v2 ) {
						$total_payable_amount = $final_payable_amount = $final_payable_amount + $v2;
					}
				}
                  
				// -------------------------------------------------------------------------------------------------------------

				// Add extra service price to final price
				if ( ! empty( $bookingpress_extra_service_price_arr ) && is_array( $bookingpress_extra_service_price_arr ) ) {
					foreach ( $bookingpress_extra_service_price_arr as $k => $v ) {
						$total_payable_amount = $final_payable_amount = $final_payable_amount + $v;
					}
				}

                $bookingpress_appointment_details['service_price_without_currency'] =  $total_payable_amount;
                $bookingpress_appointment_details['selected_service_price'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($total_payable_amount);

            }  else { 
                
                if(isset($bookingpress_appointment_details['custom_service_duration_value']) && isset($bookingpress_appointment_details['custom_service_duration_real_value'])) {
                    $bookingpress_appointment_details['custom_service_duration_value'] = intval($bookingpress_appointment_details['custom_service_duration_value']);
                    $bookingpress_appointment_details['custom_service_duration_real_value'] = intval($bookingpress_appointment_details['custom_service_duration_real_value']);                
                }
            }           
            return $bookingpress_appointment_details;
        }

        function bookingpress_modify_appointment_data_fields_func($bookingpress_appointment_vue_data_fields) {
            global $bookingpress_services;

            if(!empty($bookingpress_appointment_vue_data_fields['appointment_services_list']) ) {                

                foreach($bookingpress_appointment_vue_data_fields['appointment_services_list'] as $key => $value ) {
                    if(!empty($value['category_services'])) {
                        foreach($value['category_services'] as $key2 => $value2 ) {
                            $bookingpress_service_id = !empty($value2['service_id']) ? intval($value2['service_id']) : 0;
                            $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                            $enable_custom_service_duration = !empty($enable_custom_service_duration ) && $enable_custom_service_duration == 'true' ? true : false;
                            $bookingpress_appointment_vue_data_fields['appointment_services_list'][$key]['category_services'][$key2]['enable_custom_service_duration'] =$enable_custom_service_duration;
                        }                            
                    }
                }                
            }

            $bookingpress_appointment_vue_data_fields['appointment_formdata']['enable_custom_service_duration'] = false;
            $bookingpress_appointment_vue_data_fields['appointment_formdata']['custom_service_duration_value'] = '';
            $bookingpress_appointment_vue_data_fields['appointment_formdata']['custom_service_real_price'] = '';
            $bookingpress_appointment_vue_data_fields['appointment_formdata']['custom_service_duration_real_value'] = '';

            $bookingpress_appointment_vue_data_fields['rules']['custom_service_duration_value'] = 
                array(
                    array(
                        'required' => true,
                        'message'  => esc_html__('Please select duration', 'bookingpress-custom-service-duration'),
                        'trigger'  => 'change',
                    ),
                );            
            $bookingpress_appointment_vue_data_fields['bookingpress_custom_service_durations_slot'] = array();
            return $bookingpress_appointment_vue_data_fields;
        }

        function bookingpress_add_appointment_custom_service_duration_field_section() {
            ?>
            <el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8" v-if="typeof(appointment_formdata.enable_custom_service_duration) !== 'undefined' && appointment_formdata.enable_custom_service_duration == true">
                <el-form-item prop="custom_service_duration_value">                    
                    <template #label>
                        <span class="bpa-form-label"><?php esc_html_e('Select Duration', 'bookingpress-custom-service-duration'); ?></span>
                    </template>
                    <el-select class="bpa-form-control" placeholder="<?php esc_html_e('Select Duration', 'bookingpress-custom-service-duration'); ?>" v-model="appointment_formdata.custom_service_duration_value" @change="bookingpress_change_custom_duration">
                        <el-option v-for="custom_duration in bookingpress_custom_service_durations_slot" :label="custom_duration.text+' ( '+custom_duration.service_price+' )'" :value="custom_duration.value" ></el-option>
                    </el-select>
                </el-form-item>
            </el-col>
            <?php
        }

        function bookingpress_appointment_add_dynamic_vue_methods() {
            ?>            
            bookingpress_change_custom_duration(event) {
                const vm = this;
                vm.bookingpress_custom_service_durations_slot.forEach(function(item,index,arr){
                    if(item.value == event ) {
                        vm.appointment_formdata.selected_service_price = item.service_price
                        vm.appointment_formdata.service_price_without_currency = item.service_price_without_currency
                        vm.appointment_formdata.custom_service_duration_real_value = item.real_value;
                        vm.appointment_formdata.custom_service_real_price = item.real_price;                    }
                });
                vm.select_appointment_booking_date(vm.appointment_formdata.appointment_booked_date);
                vm.bookingpress_admin_get_final_step_amount();
            },
            <?php
        }

        function bookingpress_before_change_backend_service_func() {
            ?>
            services_lists.forEach( function( categories ) {                    
                let category_service_list = categories.category_services;
                category_service_list.forEach( function( services ){
                    let service_id = services.service_id;
                    if( service_id == selected_service ) {
                        vm.appointment_formdata.enable_custom_service_duration = services.enable_custom_service_duration
                        vm.appointment_formdata.selected_service_duration_unit =  services.service_duration_unit
                        vm.appointment_formdata.selected_service_duration = services.service_duration
                        vm.appointment_formdata.service_price_without_currency = services.service_price_without_currency                        
                        vm.appointment_formdata.custom_service_duration_value = ''
                    }
                });
            });
            <?php
        }

        function bookingpress_additional_disable_dates_func() {
        ?>
            
            var final_data_length = 55;
            if(typeof vm.bookingpress_custom_service_durations_slot != 'undefined'){
                var final_data_length = vm.bookingpress_custom_service_durations_slot.length;
            }            
            if((app.appointment_formdata.enable_custom_service_duration == true || app.appointment_formdata.enable_custom_service_duration == 'true')){
                if(typeof response.data.bookingpress_custom_service_durations_slot != 'undefined'){
                    vm.bookingpress_custom_service_durations_slot = response.data.bookingpress_custom_service_durations_slot;
                }
            }else{
                vm.bookingpress_custom_service_durations_slot = response.data.bookingpress_custom_service_durations_slot;
            }            
            if(final_data_length == 0 && (app.appointment_formdata.enable_custom_service_duration == true || app.appointment_formdata.enable_custom_service_duration == 'true')){
                if(vm.appointment_formdata.appointment_booked_date != ""){                                        
                    vm2.bookingpress_appointment_get_disable_dates( false );                    
                }
            }            
            setTimeout(function(){
                vm.bookingpress_admin_get_final_step_amount();
            },500);
            <?php
        }

        function bookingpress_set_additional_appointment_xhr_data_func() {
            ?>
                postData.enable_custom_service_duration = vm.appointment_formdata.enable_custom_service_duration;
                postData.custom_service_duration_value = vm.appointment_formdata.custom_service_duration_value; 
               
                if(typeof vm.bookingpress_custom_service_durations_slot != 'undefined' && vm.bookingpress_custom_service_durations_slot.length == 0){                  
                    postData.bookingpress_custom_service_durations_slot_fetch = 'yes';
                }                           
            <?php
        }

        function bookingpress_get_front_timing_set_additional_appointment_xhr_data_func() {
            ?>
            postData.enable_custom_service_duration = vm.appointment_formdata.enable_custom_service_duration;
            postData.custom_service_duration_value = vm.appointment_formdata.custom_service_duration_value;            
            <?php
        }

        function bookingpress_modify_backend_add_appointment_entry_data_func( $bookingpress_entry_details, $bookingpress_appointment_data) {
            if(!empty($bookingpress_appointment_data['enable_custom_service_duration']) && $bookingpress_appointment_data['enable_custom_service_duration'] == 'true') {

                $bookingpress_entry_details['bookingpress_enable_custom_duration'] = 1;   
                if(!empty($bookingpress_appointment_data['custom_service_real_price'])) {
                    $bookingpress_entry_details['bookingpress_service_price'] = floatval($bookingpress_appointment_data['custom_service_real_price']);
                 }
                 if(!empty($bookingpress_appointment_data['custom_service_duration_real_value']) && !empty($bookingpress_appointment_data['selected_service_duration_unit'])) {
                     $service_duration_unit = $bookingpress_appointment_data['selected_service_duration_unit'];
                     $service_duration_value = intval($bookingpress_appointment_data['custom_service_duration_real_value']);                    
                     if($service_duration_unit == 'h') {
                         $service_duration_value = $service_duration_value / 60;
                     } elseif($service_duration_unit == 'd') {
                         $service_duration_value = $service_duration_value / 1440;
                     }        
                     $bookingpress_entry_details['bookingpress_service_duration_val'] = $service_duration_value;
                 }
            } 
            return $bookingpress_entry_details;
        }

        function bookingpress_admin_side_filter_custom_duration_data_func($bookingpress_subtotal_price,$bookingpress_appointment_formdata,$bookingpress_selected_service_details) {
            global $wpdb,$tbl_bookingpress_custom_service_durations,$bookingpress_pro_staff_members,$BookingPress,$tbl_bookingpress_services,$tbl_bookingpress_custom_staffmembers_service_durations;

            if(!empty($bookingpress_appointment_formdata['enable_custom_service_duration']) && $bookingpress_appointment_formdata['enable_custom_service_duration']  == 'true') {

                $custom_service_duration_value = !empty($bookingpress_appointment_formdata['custom_service_duration_real_value']) ? $bookingpress_appointment_formdata['custom_service_duration_real_value'] : '';

                $bookingpress_service_id = !empty($bookingpress_appointment_formdata['appointment_selected_service']) ? intval($bookingpress_appointment_formdata['appointment_selected_service']) : 0 ;

                $selected_service_duration = !empty($bookingpress_appointment_formdata['selected_service_duration']) ? intval($bookingpress_appointment_formdata['selected_service_duration']) : "" ;

                $selected_service_duration_unit = !empty($bookingpress_appointment_formdata['selected_service_duration_unit']) ? $bookingpress_appointment_formdata['selected_service_duration_unit'] : "" ;

                $service_price = $bookingpress_subtotal_price;

                if(empty($selected_service_duration)) {
                    $selected_service_duration = !empty($bookingpress_selected_service_details['bookingpress_service_duration_val']) ? $bookingpress_selected_service_details['bookingpress_service_duration_val'] : "" ;
                }
                if(empty($selected_service_duration_unit)) {
                    $selected_service_duration_unit = !empty($bookingpress_selected_service_details['bookingpress_service_duration_unit']) ? $bookingpress_selected_service_details['bookingpress_service_duration_unit'] : "" ;
                }
                if(empty($selected_service_duration_unit)) {
                    $selected_service_duration_unit = !empty($bookingpress_selected_service_details['bookingpress_service_duration_unit']) ? $bookingpress_selected_service_details['bookingpress_service_duration_unit'] : "" ;
                }

                if($selected_service_duration_unit == 'h' ) {
                    $selected_service_duration = $selected_service_duration * 60; 
                } else if($selected_service_duration_unit == 'd'){
                    $selected_service_duration = $selected_service_duration * 60 * 24; 
                }

                if(!empty($custom_service_duration_value)) {

                    $bookingpress_selected_staffmember = !empty($bookingpress_appointment_formdata['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_formdata['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;

                    if(!empty($bookingpress_selected_staffmember) && $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ) {
                        $bookingpress_service_duration_data = $wpdb->get_row($wpdb->prepare("SELECT ssd.bookingpress_staffmember_price FROM ".$tbl_bookingpress_custom_service_durations." as sd LEFT JOIN ".$tbl_bookingpress_custom_staffmembers_service_durations." as ssd ON sd.bookingpress_custom_service_duration_id = ssd.bookingpress_custom_service_duration_id WHERE bookingpress_service_duration_val <= %d AND sd.bookingpress_service_id = %d AND bookingpress_staffmember_id = %d order by bookingpress_service_duration_val DESC",$custom_service_duration_value,$bookingpress_service_id,$bookingpress_selected_staffmember),ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm
                    } else {
                        $bookingpress_service_duration_data = $wpdb->get_row($wpdb->prepare("SELECT sd.bookingpress_service_duration_price FROM ".$tbl_bookingpress_custom_service_durations." as sd  WHERE bookingpress_service_duration_val <= %s AND sd.bookingpress_service_id = %d order by bookingpress_service_duration_val DESC",$custom_service_duration_value,$bookingpress_service_id),ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_custom_service_durations is a table name. false alarm
                    }
                     
                    if(!empty($bookingpress_service_duration_data)) {
                        $bookingpress_service_duration_price = 0;
                        if(!empty($bookingpress_selected_staffmember) && $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation()  && isset( $bookingpress_service_duration_data['bookingpress_staffmember_price'])) {
                            $bookingpress_service_duration_price = $bookingpress_service_duration_data['bookingpress_staffmember_price'];
                        } else{
                            $bookingpress_service_duration_price = $bookingpress_service_duration_data['bookingpress_service_duration_price'];
                        }
                        $bookingpress_appointment_formdata['selected_service_price'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_service_duration_price);
                        $bookingpress_appointment_formdata['service_price_without_currency'] = floatval($bookingpress_service_duration_price);
                    } else {
                        if($custom_service_duration_value >= $selected_service_duration) {                               
                            $custom_service_duration_value = intval($custom_service_duration_value);
                            $selected_service_duration = intval($selected_service_duration);
                            $number_of_time = $custom_service_duration_value / $selected_service_duration;
                            $service_price  = $service_price * $number_of_time;
                            $bookingpress_appointment_formdata['service_price_without_currency'] = floatval($service_price);
                            $bookingpress_appointment_formdata['selected_service_price'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($service_price);
                        }
                    }                    
                }
                
                $bookingpress_subtotal_price = floatval($bookingpress_appointment_formdata['service_price_without_currency']);
            }
            return $bookingpress_subtotal_price;
        }

        function bookingress_backend_after_add_service_extra_func() {
            ?>
            vm.appointment_formdata.custom_service_duration_value = '';
            <?php
        }

        function bookingress_backend_after_remove_service_extra_func() {
            ?>  
            vm.appointment_formdata.custom_service_duration_value = '';
            <?php
        }

        function bookingpress_after_insert_entry_data_from_backend_func($entry_id, $bookingpress_appointment_data) {            
            global $wpdb,$tbl_bookingpress_appointment_meta;
            if(!empty($bookingpress_appointment_data['custom_service_duration_value'])) {
                $custom_service_duration = $bookingpress_appointment_data['custom_service_duration_value'];            
                $bookingpress_db_fields = array(
                    'bookingpress_entry_id' => $entry_id,
                    'bookingpress_appointment_id' => 0,
                    'bookingpress_appointment_meta_value' => $custom_service_duration,
                    'bookingpress_appointment_meta_key' => 'appointment_custom_duration_value',
                );
                $wpdb->insert($tbl_bookingpress_appointment_meta, $bookingpress_db_fields);
            }
        }

        function bookingpress_modify_appointment_booking_fields_func($appointment_booking_fields, $entry_data, $bookingpress_appointment_data) {      
            global $wpdb,$tbl_bookingpress_appointment_meta;
            
            $appointment_booking_fields['bookingpress_enable_custom_duration'] = $entry_data['bookingpress_enable_custom_duration'];

            if(!empty($bookingpress_appointment_data['custom_service_real_price'])) {
                $appointment_booking_fields['bookingpress_service_price'] = floatval($bookingpress_appointment_data['custom_service_real_price']);
            }
            if(!empty($bookingpress_appointment_data['custom_service_duration_real_value']) && !empty($bookingpress_appointment_data['selected_service_duration_unit'])) {
                $service_duration_unit = $bookingpress_appointment_data['selected_service_duration_unit'];
                $service_duration_value = intval($bookingpress_appointment_data['custom_service_duration_real_value']);                    
                if($service_duration_unit == 'h') {
                    $service_duration_value = $service_duration_value / 60;
                } elseif($service_duration_unit == 'd') {
                    $service_duration_value = $service_duration_value / 1440;
                }        
                $appointment_booking_fields['bookingpress_service_duration_val'] = $service_duration_value;
            }

            if(!empty($bookingpress_appointment_data['appointment_update_id']) && !empty($bookingpress_appointment_data['custom_service_duration_value'])) {

                $updated_id = intval($bookingpress_appointment_data['appointment_update_id']);
                $custom_service_duration = $bookingpress_appointment_data['custom_service_duration_value'];
                $get_form_fields_meta = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_appointment_meta_key = %s AND bookingpress_appointment_id = %d", 'appointment_custom_duration_value', $updated_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_meta is a table name. false alarm

                if( 1 > $get_form_fields_meta ){
                    $wpdb->insert(
                        $tbl_bookingpress_appointment_meta,
                        array(
                            'bookingpress_appointment_meta_key' => 'appointment_custom_duration_value',
                            'bookingpress_appointment_meta_value' => $custom_service_duration,
                            'bookingpress_appointment_id' => $updated_id
                        )
                    );
                } else {
                    $bookingpress_db_fields = array(
                        'bookingpress_appointment_meta_value' => $custom_service_duration,
                    );
                    $wpdb->update($tbl_bookingpress_appointment_meta, $bookingpress_db_fields, array('bookingpress_appointment_id' => $updated_id, 'bookingpress_appointment_meta_key' => 'appointment_custom_duration_value'));
                }
            }    

            return $appointment_booking_fields;
        }

        function bookingpress_edit_appointment_details_func() {
            ?>            
            if(response.data.bookingpress_enable_custom_duration != 'undefined' && response.data.bookingpress_enable_custom_duration == 1) {
                vm.appointment_formdata.enable_custom_service_duration = true
            } else {
                vm.appointment_formdata.enable_custom_service_duration = false;
            }
            if(response.data.appointment_custom_duration_value != 'undefined') {
                vm.appointment_formdata.custom_service_duration_value = response.data.appointment_custom_duration_value;                
            }
            vm.appointment_formdata.custom_service_duration_real_value = response.data.custom_service_duration_real_value;
            vm.appointment_formdata.custom_service_real_price = response.data.custom_service_real_price; 
            vm.appointment_formdata.selected_service_duration_unit = response.data.bookingpress_service_duration_unit
            vm.appointment_formdata.selected_service_duration_val= response.data.bookingpress_service_duration_val

            <?php
        }

        function bookingpress_modify_edit_appointment_data_func($appointment_data){   
            global $wpdb,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_appointment_meta;

            $bookingpress_appointment_id = !empty($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0; // phpcs:ignore
            if(!empty($bookingpress_appointment_id)) {

                $bookingpress_appointment_meta_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_appointment_meta_value,bookingpress_appointment_meta_key FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_appointment_id = %d AND bookingpress_appointment_meta_key = %s", $bookingpress_appointment_id,'appointment_custom_duration_value' ), ARRAY_A );// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_meta is a table name. false alarm.
                
                if( !empty($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value']) ) {
                    $appointment_data['appointment_custom_duration_value']  = intval($bookingpress_appointment_meta_data['bookingpress_appointment_meta_value']);
                }
            }

            if(!empty($appointment_data['bookingpress_service_duration_val'])) {                
                $service_duration_val = intval($appointment_data['bookingpress_service_duration_val']);
                if($appointment_data['bookingpress_service_duration_unit'] == 'h') {
                    $service_duration_val = $service_duration_val * 60;                    
                } elseif($appointment_data['bookingpress_service_duration_unit'] == 'd') {
                    $service_duration_val = $service_duration_val * 60 * 24;
                }
                $appointment_data['custom_service_duration_real_value'] = $service_duration_val;
                $appointment_data['custom_service_real_price'] = floatval($appointment_data['bookingpress_service_price']); 
            }
            return $appointment_data;
        }

        function bookingpress_add_responsive_custom_duration_section_front_side_func() {
        ?>
            <div v-if="typeof appointment_step_form_data.enable_custom_service_duration !== 'undefined' && ( appointment_step_form_data.enable_custom_service_duration == 'true'|| appointment_step_form_data.enable_custom_service_duration == true)">
                <!--
                <div class="bpa-front-loader-container" v-if="is_display_custom_duration_loader == true">
                    <div class="bpa-front-loader">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid meet" width="256" height="256" viewBox="0 0 256 256" style="width:100%;height:100%">
                            <defs>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_1_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z " to="M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keyTimes="0;0.5037594;0.5263158;0.5789474;0.6691729;0.6992481;0.7593985;0.7669173;1" values="M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M294.33 386.7 C294.33,386.7 291.96,386.7 291.96,386.7 C291.96,386.7 291.67,391.89 291.67,391.89 C291.67,391.89 292.41,396.34 292.41,396.34 C292.41,396.34 292.11,401.09 292.11,401.09 C292.11,401.09 295.67,401.09 295.67,401.09 C295.67,401.09 295.82,396.05 295.82,396.05 C295.82,396.05 295.97,391.75 295.97,391.75 C295.97,391.75 294.33,386.7 294.33,386.7z ;M303.49 386.7 C303.49,386.7 284.88,386.7 284.88,386.7 C284.88,386.7 284.88,402.72 284.88,402.72 C284.88,402.72 293.41,402.87 293.41,402.87 C293.41,402.87 293.07,405.24 293.07,405.24 C293.07,405.24 296.63,405.24 296.63,405.24 C296.63,405.24 296.82,402.57 296.82,402.57 C296.82,402.57 304.49,401.98 304.49,401.98 C304.49,401.98 303.49,386.7 303.49,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,398.12 265.56,398.12 C265.56,398.12 266.75,407.02 266.75,407.02 C266.75,407.02 294.78,405.83 294.78,405.83 C294.78,405.83 298.34,405.83 298.34,405.83 C298.34,405.83 332.75,406.72 332.75,406.72 C332.75,406.72 332.45,399.46 332.45,399.46 C332.45,399.46 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.56,442.32 265.56,442.32 C265.56,442.32 266.75,448.4 266.75,448.4 C266.75,448.4 283.8,447.51 283.8,447.51 C283.8,447.51 312.06,447.21 312.06,447.21 C312.06,447.21 332.75,448.1 332.75,448.1 C332.75,448.1 332.45,443.65 332.45,443.65 C332.45,443.65 330.97,386.7 330.97,386.7z ;M330.97 386.7 C330.97,386.7 263.64,386.7 263.64,386.7 C263.64,386.7 265.86,453.14 265.86,453.14 C265.86,453.14 276.98,456.11 276.98,456.11 C276.98,456.11 277.28,447.51 277.28,447.51 C277.28,447.51 319.47,447.81 319.47,447.81 C319.47,447.81 318.81,456.11 318.81,456.11 C318.81,456.11 329.63,454.92 329.63,454.92 C329.63,454.92 330.97,386.7 330.97,386.7z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.63,448.83 295.63,448.83 C295.63,448.83 295.71,448.75 295.71,448.75 C295.71,448.75 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z ;M330.93 386.68 C330.93,386.68 263.6,386.68 263.6,386.68 C263.6,386.68 265.82,453.13 265.82,453.13 C265.82,453.13 295.78,456.98 295.78,456.98 C295.78,456.98 295.89,452.83 295.89,452.83 C295.89,452.83 296.26,452.98 296.26,452.98 C296.26,452.98 295.78,457.13 295.78,457.13 C295.78,457.13 329.59,454.91 329.59,454.91 C329.59,454.91 330.93,386.68 330.93,386.68z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_1_C_0">
                                    <path id="_R_G_L_1_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate repeatCount="indefinite" dur="2.2166667s" begin="0s" xlink:href="#_R_G_L_0_C_0_P_0" fill="freeze" attributeName="d" attributeType="XML" from="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z " to="M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keyTimes="0;0.1804511;0.2180451;0.2481203;0.2631579;0.2706767;0.2781955;0.2857143;0.3157895;0.3308271;0.3533835;0.3834586;0.406015;0.4135338;0.4210526;0.4511278;0.4736842;0.4887218;0.4962406;1" values="M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M306.79 419.97 C306.79,419.97 306.83,419.98 306.83,419.98 C306.83,419.98 306.8,419.97 306.8,419.97 C306.8,419.97 306.78,420 306.78,420 C306.78,420 306.8,420 306.8,420 C306.8,420 306.86,420 306.86,420 C306.86,420 306.95,419.93 306.95,419.93 C306.95,419.93 306.86,419.96 306.86,419.96 C306.86,419.96 306.84,420.21 306.84,420.21 C306.84,420.21 306.89,420.1 306.89,420.1 C306.89,420.1 306.83,420.1 306.83,420.1 C306.83,420.1 306.5,420.99 306.83,420.17 C307.17,419.36 306.69,420.75 306.69,419.9 C306.69,419.04 306.89,420.14 306.89,420.14 C306.89,420.14 306.93,420.01 306.93,420.01 C306.93,420.01 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 302.88,423.24 302.88,423.24 C302.88,423.24 302.6,423.2 302.6,423.2 C302.6,423.2 302.79,423.22 302.79,423.22 C302.79,423.22 302.47,423.18 302.47,423.18 C302.47,423.18 302.62,423.19 302.62,423.19 C302.62,423.19 302.53,423.17 302.53,423.17 C302.53,423.17 302.43,423.36 302.43,423.18 C302.43,422.99 302.57,423.16 302.57,423.16 C302.57,423.16 302.4,423.16 302.4,423.16 C302.4,423.16 302.71,423.1 302.71,423.1 C302.71,423.1 302.68,423.07 302.68,423.07 C302.68,423.07 302.76,423.09 302.76,423.09 C302.76,423.09 302.66,423.2 302.66,423.2 C302.66,423.2 302.71,423.14 302.71,423.14 C302.71,423.14 302.75,423.12 302.75,423.12 C302.75,423.12 302.75,423.18 302.75,423.18 C302.75,423.18 302.53,423.22 302.53,423.22 C302.53,423.22 306.79,419.98 306.79,419.98 C306.79,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.8,419.98 306.8,419.98 C306.8,419.98 306.77,419.98 306.77,419.98 C306.77,419.98 306.79,419.98 306.79,419.98 C306.79,419.98 306.79,419.97 306.79,419.97z ;M310.92 429.74 C310.92,429.74 310.97,429.75 310.97,429.75 C310.97,429.75 310.93,429.74 310.93,429.74 C310.93,429.74 310.91,429.77 310.91,429.77 C310.91,429.77 310.94,429.77 310.94,429.77 C310.94,429.77 310.99,429.77 310.99,429.77 C310.99,429.77 311.09,429.7 311.09,429.7 C311.09,429.7 310.99,429.73 310.99,429.73 C310.99,429.73 310.9,434.91 310.9,434.91 C310.9,434.91 312.25,433.8 312.25,433.8 C312.25,433.8 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 303.93,428.18 303.93,428.18 C303.93,428.18 303.66,428.14 303.66,428.14 C303.66,428.14 303.84,428.16 303.84,428.16 C303.84,428.16 303.52,428.11 303.52,428.11 C303.52,428.11 303.67,428.12 303.67,428.12 C303.67,428.12 303.58,428.1 303.58,428.1 C303.58,428.1 303.49,428.3 303.49,428.11 C303.49,427.93 303.63,428.09 303.63,428.09 C303.63,428.09 303.45,428.1 303.45,428.1 C303.45,428.1 303.76,428.04 303.76,428.04 C303.76,428.04 303.73,428 303.73,428 C303.73,428 303.69,427.98 303.69,427.98 C303.69,427.98 303.71,428.13 303.71,428.13 C303.71,428.13 303.76,428.08 303.76,428.08 C303.76,428.08 303.8,428.06 303.8,428.06 C303.8,428.06 303.8,428.11 303.8,428.11 C303.8,428.11 303.58,428.16 303.58,428.16 C303.58,428.16 310.92,429.75 310.92,429.75 C310.92,429.75 310.91,429.75 310.91,429.75 C310.91,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.9,429.75 310.9,429.75 C310.9,429.75 310.93,429.75 310.93,429.75 C310.93,429.75 310.92,429.74 310.92,429.74z ;M299.65 434.12 C299.65,434.12 299.7,434.13 299.7,434.13 C299.7,434.13 299.66,434.11 299.66,434.11 C299.66,434.11 299.64,434.14 299.64,434.14 C299.64,434.14 299.66,434.14 299.66,434.14 C299.66,434.14 299.72,434.15 299.72,434.15 C299.72,434.15 299.81,434.08 299.81,434.08 C299.81,434.08 299.72,434.11 299.72,434.11 C299.72,434.11 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 300.06,430.31 300.06,430.31 C300.06,430.31 299.78,430.27 299.78,430.27 C299.78,430.27 299.96,430.29 299.96,430.29 C299.96,430.29 299.65,430.25 299.65,430.25 C299.65,430.25 299.8,430.25 299.8,430.25 C299.8,430.25 299.7,430.24 299.7,430.24 C299.7,430.24 299.61,430.43 299.61,430.25 C299.61,430.06 299.75,430.22 299.75,430.22 C299.75,430.22 299.57,430.23 299.57,430.23 C299.57,430.23 299.89,430.17 299.89,430.17 C299.89,430.17 299.85,430.13 299.85,430.13 C299.85,430.13 299.82,430.12 299.82,430.12 C299.82,430.12 299.83,430.26 299.83,430.26 C299.83,430.26 299.89,430.21 299.89,430.21 C299.89,430.21 299.93,430.19 299.93,430.19 C299.93,430.19 299.93,430.25 299.93,430.25 C299.93,430.25 299.7,430.29 299.7,430.29 C299.7,430.29 299.65,434.13 299.65,434.13 C299.65,434.13 299.64,434.13 299.64,434.13 C299.64,434.13 299.66,434.13 299.66,434.13 C299.66,434.13 299.63,434.13 299.63,434.13 C299.63,434.13 299.65,434.13 299.65,434.13 C299.65,434.13 299.65,434.12 299.65,434.12z ;M292.83 434.12 C292.83,434.12 292.81,434.11 292.81,434.11 C292.81,434.11 292.84,434.12 292.84,434.12 C292.84,434.12 292.82,434.15 292.82,434.15 C292.82,434.15 292.85,434.15 292.85,434.15 C292.85,434.15 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 292.89,430.31 292.89,430.31 C292.89,430.31 292.69,430.25 292.69,430.25 C292.69,430.25 292.72,430.28 292.72,430.28 C292.72,430.28 292.63,430.26 292.63,430.26 C292.63,430.26 292.65,430.43 292.65,430.25 C292.65,430.06 292.56,430.15 292.56,430.15 C292.56,430.15 292.61,430.23 292.61,430.23 C292.61,430.23 292.93,430.17 292.93,430.17 C292.93,430.17 292.89,430.13 292.89,430.13 C292.89,430.13 292.85,430.12 292.85,430.12 C292.85,430.12 292.87,430.26 292.87,430.26 C292.87,430.26 292.93,430.21 292.93,430.21 C292.93,430.21 292.96,430.19 292.96,430.19 C292.96,430.19 292.96,430.25 292.96,430.25 C292.96,430.25 292.77,430.22 292.77,430.22 C292.77,430.22 292.83,434.13 292.83,434.13 C292.83,434.13 292.82,434.13 292.82,434.13 C292.82,434.13 292.84,434.13 292.84,434.13 C292.84,434.13 292.81,434.13 292.81,434.13 C292.81,434.13 292.83,434.13 292.83,434.13 C292.83,434.13 292.83,434.12 292.83,434.12z ;M286.91 434.04 C286.91,434.04 286.89,434.02 286.89,434.02 C286.89,434.02 286.92,434.03 286.92,434.03 C286.92,434.03 286.9,434.06 286.9,434.06 C286.9,434.06 286.92,434.06 286.92,434.06 C286.92,434.06 294.61,434.08 294.61,434.08 C294.61,434.08 298.39,434.03 298.39,434.03 C298.39,434.03 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.47,430.31 298.47,430.31 C298.47,430.31 294.44,430.33 294.44,430.33 C294.44,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.91,428.68 291.91,428.68 C291.91,428.68 291.82,428.67 291.82,428.67 C291.82,428.67 291.88,428.65 291.88,428.46 C291.88,428.28 291.78,428.37 291.78,428.37 C291.78,428.37 291.84,428.44 291.84,428.44 C291.84,428.44 292.15,428.39 292.15,428.39 C292.15,428.39 292.12,428.35 292.12,428.35 C292.12,428.35 292.08,428.33 292.08,428.33 C292.08,428.33 292.1,428.48 292.1,428.48 C292.1,428.48 292.15,428.42 292.15,428.42 C292.15,428.42 292.19,428.41 292.19,428.41 C292.19,428.41 292.19,428.46 292.19,428.46 C292.19,428.46 291.97,428.51 291.97,428.51 C291.97,428.51 287.14,434.07 287.14,434.07 C287.14,434.07 286.89,434.05 286.89,434.05 C286.89,434.05 286.92,434.05 286.92,434.05 C286.92,434.05 286.89,434.05 286.89,434.05 C286.89,434.05 286.91,434.05 286.91,434.05 C286.91,434.05 286.91,434.04 286.91,434.04z ;M286.7 429.47 C286.7,429.47 286.88,429.37 286.88,429.37 C286.88,429.37 286.52,429.45 286.52,429.45 C286.52,429.45 286.83,429.85 286.83,429.85 C286.83,429.85 286.14,434.18 286.14,434.18 C286.14,434.18 294.61,434.08 294.61,434.08 C294.61,434.08 298.37,434.08 298.37,434.08 C298.37,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.38,430.31 298.38,430.31 C298.38,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 291.99,426.42 291.99,426.42 C291.99,426.42 291.87,426.34 291.87,426.34 C291.87,426.34 292.01,426.25 292.01,426.07 C292.01,425.88 292.05,425.99 292.05,425.99 C292.05,425.99 291.97,425.95 291.97,425.95 C291.97,425.95 292.39,425.98 292.39,425.98 C292.39,425.98 292.27,426.05 292.27,426.05 C292.27,426.05 292.35,425.99 292.35,425.99 C292.35,425.99 292.32,426 292.32,426 C292.32,426 292.4,426 292.4,426 C292.4,426 292.4,426.06 292.4,426.06 C292.4,426.06 292.39,426.05 292.39,426.05 C292.39,426.05 292.62,426.45 292.62,426.45 C292.62,426.45 286.78,429.41 286.78,429.41 C286.78,429.41 286.55,429.2 286.55,429.2 C286.55,429.2 286.62,429.38 286.62,429.38 C286.62,429.38 286.51,429.44 286.51,429.44 C286.51,429.44 286.46,429.37 286.46,429.37 C286.46,429.37 286.7,429.47 286.7,429.47z ;M286.5 424.9 C286.5,424.9 286.87,424.72 286.87,424.72 C286.87,424.72 286.13,424.87 286.13,424.87 C286.13,424.87 286.76,425.64 286.76,425.64 C286.76,425.64 285.37,434.3 285.37,434.3 C285.37,434.3 294.63,434.09 294.63,434.09 C294.63,434.09 298.37,434.09 298.37,434.09 C298.37,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.36,430.31 298.36,430.31 C298.36,430.31 294.59,430.33 294.59,430.33 C294.59,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.06,424.16 292.06,424.16 C292.06,424.16 291.91,424.01 291.91,424.01 C291.91,424.01 292.13,423.86 292.13,423.68 C292.13,423.49 292.32,423.6 292.32,423.6 C292.32,423.6 292.1,423.46 292.1,423.46 C292.1,423.46 292.62,423.57 292.62,423.57 C292.62,423.57 292.43,423.75 292.43,423.75 C292.43,423.75 292.62,423.64 292.62,423.64 C292.62,423.64 292.54,423.53 292.54,423.53 C292.54,423.53 292.65,423.57 292.65,423.57 C292.65,423.57 292.62,423.72 292.62,423.72 C292.62,423.72 292.58,423.64 292.58,423.64 C292.58,423.64 293.27,424.39 293.27,424.39 C293.27,424.39 286.43,424.75 286.43,424.75 C286.43,424.75 286.2,424.35 286.2,424.35 C286.2,424.35 286.31,424.72 286.31,424.72 C286.31,424.72 286.13,424.83 286.13,424.83 C286.13,424.83 286.02,424.68 286.02,424.68 C286.02,424.68 286.5,424.9 286.5,424.9z ;M285.53 417.93 C285.53,417.93 285.61,418.01 285.61,418.01 C285.61,418.01 285.39,417.97 285.39,417.97 C285.39,417.97 285.68,418.12 285.68,418.12 C285.68,418.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.38,434.11 298.38,434.11 C298.38,434.11 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.71,430.31 298.71,430.31 C298.71,430.31 293.3,430.31 293.3,430.31 C293.3,430.31 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.26,417.75 291.26,417.56 C291.26,417.38 291.34,417.38 291.34,417.38 C291.34,417.38 291.45,417.54 291.45,417.54 C291.45,417.54 291.21,417.5 291.21,417.5 C291.21,417.5 291.32,417.45 291.32,417.45 C291.32,417.45 291.28,417.51 291.28,417.51 C291.28,417.51 291.5,417.56 291.5,417.56 C291.5,417.56 291.52,417.54 291.52,417.54 C291.52,417.54 291.45,417.6 291.45,417.6 C291.45,417.6 291.43,417.67 291.43,417.67 C291.43,417.67 291.41,417.89 291.41,417.89 C291.41,417.89 291.24,417.95 291.24,417.95 C291.24,417.95 285.98,417.86 285.98,417.86 C285.98,417.86 286.02,417.69 286.02,417.69 C286.02,417.69 285.92,417.77 285.92,417.77 C285.92,417.77 285.81,417.62 285.81,417.62 C285.81,417.62 285.53,417.93 285.53,417.93z ;M284.93 404.18 C284.93,404.18 281.14,411.97 281.14,411.97 C281.14,411.97 273.88,412.04 273.88,412.04 C273.88,412.04 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.08 298.36,434.08 C298.36,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.35,430.31 298.35,430.31 C298.35,430.31 294.59,430.32 294.59,430.32 C294.59,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 291.91,415.81 291.91,415.81 C291.91,415.81 291.8,415.82 291.8,415.82 C291.8,415.82 291.88,415.73 291.88,415.73 C291.88,415.73 291.9,415.66 291.9,415.66 C291.9,415.66 291.8,415.65 291.8,415.65 C291.8,415.65 291.73,415.73 291.73,415.73 C291.73,415.73 291.87,415.58 291.87,415.58 C291.87,415.58 291.87,415.71 291.87,415.71 C291.87,415.71 291.83,415.72 291.83,415.72 C291.83,415.72 291.82,415.71 291.82,415.71 C291.82,415.71 291.66,414.92 291.66,414.92 C291.66,414.92 291.45,413.38 291.45,413.38 C291.45,413.38 291.09,411.81 291.09,411.81 C291.09,411.81 291.05,411.77 291.05,411.77 C291.05,411.77 289.08,410.26 289.08,410.26 C289.08,410.26 284.93,404.18 284.93,404.18z ;M298.66 404.21 C298.66,404.21 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.76,430.32 298.76,430.32 C298.76,430.32 294.62,430.33 294.62,430.33 C294.62,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 300.75,413.19 300.75,413.19 C300.75,413.19 300.74,413.2 300.74,413.2 C300.74,413.2 300.68,413.28 300.68,413.28 C300.68,413.28 300.74,413.15 300.74,413.15 C300.74,413.15 300.76,413.19 300.76,413.19 C300.76,413.19 300.77,413.17 300.77,413.17 C300.77,413.17 303.55,406.44 303.55,406.44 C303.55,406.44 302.85,404.47 302.85,404.47 C302.85,404.47 301.29,403.47 301.29,403.47 C301.29,403.47 301.18,403.32 301.18,403.32 C301.18,403.32 298.66,404.21 298.66,404.21z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.07 294.61,434.07 C294.61,434.07 298.36,434.07 298.36,434.07 C298.36,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 307.04,419.79 307.04,419.79 C307.04,419.79 301.92,423.68 301.92,423.68 C301.92,423.68 298.73,430.31 298.73,430.31 C298.73,430.31 293.3,430.33 293.3,430.33 C293.3,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.59,416.02 302.59,416.02 C302.59,416.02 302.55,415.98 302.55,415.98 C302.55,415.98 302.63,415.99 302.63,415.99 C302.63,415.99 306.67,409.55 306.67,409.55 C306.67,409.55 306.65,409.61 306.65,409.61 C306.65,409.61 306.59,409.55 306.59,409.55 C306.59,409.55 306.69,409.72 306.69,409.72 C306.69,409.72 306.58,409.57 306.58,409.57 C306.58,409.57 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.09 294.61,434.09 C294.61,434.09 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.29,420.1 306.29,420.1 C306.29,420.1 301.7,423.39 301.7,423.39 C301.7,423.39 298.38,430.31 298.38,430.31 C298.38,430.31 293.4,430.32 293.4,430.32 C293.4,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 302.63,417.02 302.63,417.02 C302.63,417.02 302.61,416.97 302.61,416.97 C302.61,416.97 302.63,416.9 302.63,416.9 C302.63,416.9 307.12,415.55 307.12,415.55 C307.12,415.55 307.51,415.47 307.51,415.47 C307.51,415.47 307.52,415.47 307.52,415.47 C307.52,415.47 309.01,412.56 309.01,412.56 C309.01,412.56 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.08 294.6,434.08 C294.6,434.08 298.37,434.07 298.37,434.07 C298.37,434.07 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.05,420.2 306.05,420.2 C306.05,420.2 301.63,423.29 301.63,423.29 C301.63,423.29 298.57,430.33 298.57,430.33 C298.57,430.33 293.35,430.32 293.35,430.32 C293.35,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.72,417.16 306.72,417.16 C306.72,417.16 307.56,417.29 307.56,417.29 C307.56,417.29 307.59,417.33 307.59,417.33 C307.59,417.33 308.54,413.47 308.54,413.47 C308.54,413.47 306.71,408.22 306.71,408.22 C306.71,408.22 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.62,434.09 294.62,434.09 C294.62,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.8,420.3 305.8,420.3 C305.8,420.3 301.55,423.2 301.55,423.2 C301.55,423.2 298.74,430.31 298.74,430.31 C298.74,430.31 293.34,430.32 293.34,430.32 C293.34,430.32 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 300.2,418.16 300.2,418.16 C300.2,418.16 306.32,418.77 306.32,418.77 C306.32,418.77 307.34,417.78 307.34,417.78 C307.34,417.78 307.74,418.52 307.74,418.52 C307.74,418.52 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.6,434.09 294.6,434.09 C294.6,434.09 298.35,434.08 298.35,434.08 C298.35,434.08 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 305.98,420.3 305.98,420.3 C305.98,420.3 301.72,423.59 301.72,423.59 C301.72,423.59 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 297.55,418.67 297.55,418.67 C297.55,418.67 296.68,421.72 296.68,421.72 C296.68,421.72 300.57,423.18 300.57,423.18 C300.57,423.18 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 294.61,434.08 294.61,434.08 C294.61,434.08 298.36,434.09 298.36,434.09 C298.36,434.09 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.41,419.97 306.41,419.97 C306.41,419.97 301.7,423.64 301.7,423.64 C301.7,423.64 298.69,430.31 298.69,430.31 C298.69,430.31 294.56,430.33 294.56,430.33 C294.56,430.33 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 294.58,430.33 294.58,430.33 C294.58,430.33 298.38,430.31 298.38,430.31 C298.38,430.31 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.73,439.55 293.73,439.55 C293.73,439.55 298.46,439.54 298.46,439.54 C298.46,439.54 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.43,419.98 306.43,419.98 C306.43,419.98 301.75,423.57 301.75,423.57 C301.75,423.57 298.73,430.27 298.73,430.27 C298.73,430.27 293.72,430.3 293.72,430.3 C293.72,430.3 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.7,430.31 293.7,430.31 C293.7,430.31 298.74,430.26 298.74,430.26 C298.74,430.26 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z ;M301.92 404.95 C301.92,404.95 293.25,405.03 293.25,405.03 C293.25,405.03 285.98,405.1 285.98,405.1 C285.98,405.1 284.05,419.12 284.05,419.12 C284.05,419.12 285.37,434.3 285.37,434.3 C285.37,434.3 293.25,442.25 293.25,442.25 C293.25,442.25 298.5,442.3 298.5,442.3 C298.5,442.3 299.74,434.68 299.74,434.68 C299.74,434.68 303.69,434.6 303.69,434.6 C303.69,434.6 306.59,433.87 306.59,433.87 C306.59,433.87 311.49,430.09 311.49,430.09 C311.49,430.09 310.52,426.16 310.86,425.35 C311.19,424.53 310.82,424.83 310.82,423.97 C310.82,423.12 310.56,422.53 310.56,422.53 C310.56,422.53 308.71,419.49 308.71,419.49 C308.71,419.49 306.45,419.99 306.45,419.99 C306.45,419.99 301.77,423.53 301.77,423.53 C301.77,423.53 298.75,430.25 298.75,430.25 C298.75,430.25 293.3,430.28 293.3,430.28 C293.3,430.28 293.04,430.16 293.04,430.16 C293.04,430.16 291.91,428.46 291.91,428.46 C291.91,428.46 292.21,418.97 292.21,418.97 C292.21,418.97 291.95,418.04 291.95,418.04 C291.95,418.04 291.91,416.23 291.91,416.04 C291.91,415.86 292.25,414.59 292.25,414.59 C292.25,414.59 293.88,413.41 293.88,413.41 C293.88,413.41 294.99,412.85 294.99,412.85 C294.99,412.85 297.18,412.81 297.18,412.81 C297.18,412.81 299.59,413 299.59,413 C299.59,413 301.89,414.22 301.89,414.22 C301.89,414.22 302.37,415.82 302.37,415.82 C302.37,415.82 301.74,416.82 301.74,416.82 C301.74,416.82 292.58,424.16 292.58,424.16 C292.58,424.16 293.3,430.28 293.3,430.28 C293.3,430.28 298.75,430.25 298.75,430.25 C298.75,430.25 301.74,423.57 301.74,423.57 C301.74,423.57 306.45,419.97 306.45,419.97 C306.45,419.97 308.08,414.37 308.08,414.37 C308.08,414.37 310.3,409.7 310.3,409.7 C310.3,409.7 301.92,404.95 301.92,404.95z " keySplines="0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0.167 0.167 0.833 0.833;0 0 0 0" calcMode="spline"/>
                                <clipPath id="_R_G_L_0_C_0">
                                    <path id="_R_G_L_0_C_0_P_0" fill-rule="nonzero"/>
                                </clipPath>
                                <animate attributeType="XML" attributeName="opacity" dur="2s" from="0" to="1" xlink:href="#time_group"/>
                            </defs>
                            <g id="_R_G">
                                <g id="_R_G_L_1_G" transform=" translate(127.638, 127.945) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_1_C_0)">
                                        <path id="_R_G_L_1_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M328 398.61 C328,398.61 328,446.23 328,446.23 C328,449.7 325.2,452.5 321.75,452.5 C321.75,452.5 274.25,452.5 274.25,452.5 C270.8,452.5 268,449.7 268,446.23 C268,446.23 268,398.61 268,398.61 C268,395.15 270.8,392.35 274.25,392.35 C274.25,392.35 283.46,392.26 283.46,392.26 C283.46,392.26 283.46,390.38 283.46,390.38 C283.46,389.76 284.08,388.5 285.33,388.5 C286.58,388.5 287.21,389.75 287.21,390.38 C287.21,390.38 287.21,397.89 287.21,397.89 C287.21,398.53 286.59,399.78 285.33,399.78 C284.08,399.78 283.46,398.53 283.46,397.9 C283.46,397.9 283.46,396.02 283.46,396.02 C283.46,396.02 275.5,396.1 275.5,396.1 C273.43,396.1 271.75,397.79 271.75,399.86 C271.75,399.86 271.75,444.98 271.75,444.98 C271.75,447.06 273.43,448.74 275.5,448.74 C275.5,448.74 320.5,448.74 320.5,448.74 C322.57,448.74 324.25,447.06 324.25,444.98 C324.25,444.98 324.25,399.86 324.25,399.86 C324.25,397.79 322.57,396.1 320.5,396.1 C320.5,396.1 312.62,396.1 312.62,396.1 C312.62,396.1 312.63,397.06 312.63,397.99 C312.63,398.61 312,399.86 310.75,399.86 C309.5,399.86 308.88,398.61 308.88,397.98 C308.88,397.98 308.87,396.1 308.87,396.1 C308.87,396.1 301.88,396.1 301.88,396.1 C300.84,396.1 300,395.26 300,394.23 C300,393.19 300.84,392.35 301.88,392.35 C301.88,392.35 308.87,392.35 308.87,392.35 C308.87,392.35 308.87,390.47 308.87,390.47 C308.87,389.83 309.5,388.5 310.75,388.5 C312,388.5 312.62,389.84 312.62,390.47 C312.62,390.47 312.62,392.35 312.62,392.35 C312.62,392.35 321.75,392.35 321.75,392.35 C325.2,392.35 328,395.15 328,398.61z "/>
                                    </g>
                                </g>
                                <g id="_R_G_L_0_G" transform=" translate(125.555, 126.412) scale(3.37139, 3.37139) translate(-297.638, -420.945)">
                                    <g clip-path="url(#_R_G_L_0_C_0)">
                                        <path id="_R_G_L_0_G_G_0_D_0_P_0" class="bpa-front-loader-cl-primary" fill-opacity="1" fill-rule="nonzero" d=" M305.86 420.29 C305.86,420.29 307.11,419.04 307.11,415.28 C307.11,409.01 303.36,407.76 298.36,407.76 C298.36,407.76 287.11,407.76 287.11,407.76 C287.11,407.76 287.11,434.08 287.11,434.08 C287.11,434.08 294.61,434.08 294.61,434.08 C294.61,434.08 294.61,441.6 294.61,441.6 C294.61,441.6 298.36,441.6 298.36,441.6 C298.36,441.6 298.36,434.08 298.36,434.08 C302.71,434.08 305.73,434.08 307.98,431.3 C309.07,429.95 309.62,428.24 309.61,426.5 C309.61,425.58 309.51,424.67 309.3,424.05 C308.73,422.65 308.36,421.55 305.86,420.29z  M302.11 430.32 C302.11,430.32 298.36,430.32 298.36,430.32 C298.36,430.32 298.36,426.56 298.36,426.56 C298.36,424.48 300.03,422.8 302.11,422.8 C304.13,422.8 305.86,424.43 305.86,426.56 C305.86,428.78 304.03,430.32 302.11,430.32z  M299.07 419.95 C298.43,420.26 297.82,420.63 297.26,421.05 C295.87,422.1 294.61,423.58 294.61,426.56 C294.61,426.56 294.61,430.32 294.61,430.32 C294.61,430.32 290.86,430.32 290.86,430.32 C290.86,430.32 290.86,411.52 290.86,411.52 C290.86,411.52 298.36,411.52 298.36,411.52 C301.35,411.52 303.36,412.77 303.36,415.28 C303.36,417.58 301.65,418.68 299.07,419.95z "/>
                                    </g>
                                </g>
                            </g>
                            <g id="time_group"/>
                        </svg>
                    </div>
                </div> -->
                <div class="bpa-front--dt__custom-duration-is-full" v-if="typeof appointment_step_form_data.custom_service_duration_value !== 'undefined' && appointment_step_form_data.custom_service_duration_value == '' ">
                    <div class="bpa-front-cdf__icon">
                    <svg width="53" height="52" viewBox="0 0 53 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="bpa-front-cdf__icon-bg" d="M53 33.7139C53 43.7567 43.5541 51.8979 33.3949 51.8979C23.2357 51.8979 15 43.7567 15 33.7139C15 23.6711 25.6561 12.8979 35.8153 12.8979C45.9745 12.8979 53 23.6711 53 33.7139Z" /> 
                            <g filter="url(#filter0_d_2605_6524)">
                                <path d="M37.5519 9.48846H3V44.1506H37.5519V9.48846Z" fill="white"/>
                            </g>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M32.197 6.07112C32.7117 6.07112 33.1283 5.6545 33.1283 5.13977V0.931347C33.1283 0.416616 32.7117 0 32.197 0C31.6822 0 31.2656 0.416616 31.2656 0.931347V5.13977C31.2656 5.6545 31.6822 6.07112 32.197 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M20.7243 6.07112C21.239 6.07112 21.6557 5.6545 21.6557 5.13977V0.931347C21.6557 0.416616 21.239 0 20.7243 0C20.2096 0 19.793 0.416616 19.793 0.931347V5.13977C19.793 5.6545 20.2096 6.07112 20.7243 6.07112Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" fill-rule="evenodd" clip-rule="evenodd" d="M37.5519 9.4886V4.03637C37.5519 2.95257 36.6749 2.07556 35.5911 2.07556H4.96081C3.87852 2.07556 3 2.95257 3 4.03637V9.4886H37.5519ZM33.8217 5.46116C33.8217 6.35901 33.0939 7.08686 32.196 7.08686C31.2982 7.08686 30.5703 6.35901 30.5703 5.46116C30.5703 4.5633 31.2982 3.83545 32.196 3.83545C33.0939 3.83545 33.8217 4.5633 33.8217 5.46116ZM20.7253 7.08686C21.6232 7.08686 22.351 6.35901 22.351 5.46116C22.351 4.5633 21.6232 3.83545 20.7253 3.83545C19.8275 3.83545 19.0996 4.5633 19.0996 5.46116C19.0996 6.35901 19.8275 7.08686 20.7253 7.08686ZM10.8784 5.46116C10.8784 6.35901 10.1505 7.08686 9.25266 7.08686C8.35481 7.08686 7.62695 6.35901 7.62695 5.46116C7.62695 4.5633 8.35481 3.83545 9.25266 3.83545C10.1505 3.83545 10.8784 4.5633 10.8784 5.46116Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M9.25166 6.07112C9.76639 6.07112 10.183 5.6545 10.183 5.13977V0.931347C10.183 0.416616 9.76639 0 9.25166 0C8.73693 0 8.32031 0.416616 8.32031 0.931347V5.13977C8.32031 5.6545 8.73693 6.07112 9.25166 6.07112Z" />
                            <path d="M25.88 21.6296H32.4492V15.0603H25.88V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 21.6296H23.707V15.0603H17.1378V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 21.6296H14.9668V15.0603H8.39756V21.6296Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M25.88 29.9683H32.4492V23.3991H25.88V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 29.9683H23.707V23.3991H17.1378V29.9683Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M8.39756 29.9683H14.9668V23.3991H8.39756V29.9683Z" />
                            <path d="M10.7715 25.744L12.5934 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M12.5934 25.744L10.7715 27.6218" stroke="white" stroke-width="0.376038" stroke-miterlimit="10"/>
                            <path d="M25.88 38.3079H32.4492V31.7386H25.88V38.3079Z" stroke="#F0E0DF" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M17.1378 38.3079H23.707V31.7386H17.1378V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path d="M8.39756 38.3079H14.9668V31.7386H8.39756V38.3079Z" stroke="#E6F1ED" stroke-width="0.412535" stroke-miterlimit="10"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M36.9588 21.31H33.7617V23.4157H36.9588V21.31Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M37.4509 21.8565H33.2682C32.7927 21.8565 32.4062 21.4701 32.4062 20.9946C32.4062 20.5191 32.7927 20.1327 33.2682 20.1327H37.4509C37.9264 20.1327 38.3128 20.5191 38.3128 20.9946C38.3128 21.4701 37.9264 21.8565 37.4509 21.8565Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M27.6055 24.5042L25.8027 26.3069L27.0739 27.5782L28.8767 25.7754L27.6055 24.5042Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.1741 24.4304L25.6563 26.9482C25.4027 27.2018 24.9907 27.2018 24.7371 26.9482C24.4835 26.6946 24.4835 26.2825 24.7371 26.0289L27.2549 23.5111C27.5085 23.2575 27.9206 23.2575 28.1741 23.5111C28.4292 23.7647 28.4292 24.1768 28.1741 24.4304Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.322 44.122C41.2901 44.122 46.1283 39.2838 46.1283 33.3156C46.1283 27.3474 41.2901 22.5093 35.322 22.5093C29.3538 22.5093 24.5156 27.3474 24.5156 33.3156C24.5156 39.2838 29.3538 44.122 35.322 44.122Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3229 43.5408C40.9701 43.5408 45.548 38.9629 45.548 33.3156C45.548 27.6684 40.9701 23.0905 35.3229 23.0905C29.6756 23.0905 25.0977 27.6684 25.0977 33.3156C25.0977 38.9629 29.6756 43.5408 35.3229 43.5408Z" />
                            <path d="M35.3224 42.3859C40.3319 42.3859 44.3928 38.325 44.3928 33.3155C44.3928 28.306 40.3319 24.2451 35.3224 24.2451C30.3129 24.2451 26.252 28.306 26.252 33.3155C26.252 38.325 30.3129 42.3859 35.3224 42.3859Z" fill="#F6F6F6"/>
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 26.2884C35.2413 26.2884 35.1387 26.1858 35.1387 26.0605V25.5246C35.1387 25.3978 35.2413 25.2967 35.3666 25.2967C35.4934 25.2967 35.5945 25.3993 35.5945 25.5246V26.0605C35.5945 26.1858 35.4919 26.2884 35.3666 26.2884Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.3666 41.4433C35.2413 41.4433 35.1387 41.3407 35.1387 41.2154V40.6795C35.1387 40.5542 35.2413 40.4516 35.3666 40.4516C35.4934 40.4516 35.5945 40.5542 35.5945 40.6795V41.2154C35.5945 41.3407 35.4919 41.4433 35.3666 41.4433Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M30.197 28.4288C30.1382 28.4288 30.0808 28.4062 30.0355 28.3624L29.6566 27.9835C29.5676 27.8945 29.5676 27.7495 29.6566 27.6605C29.7457 27.5714 29.8906 27.5714 29.9797 27.6605L30.3585 28.0394C30.4476 28.1284 30.4476 28.2733 30.3585 28.3624C30.3148 28.4077 30.2559 28.4288 30.197 28.4288Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.9138 39.1444C40.855 39.1444 40.7976 39.1217 40.7523 39.078L40.3734 38.6991C40.2844 38.61 40.2844 38.4651 40.3734 38.3761C40.4625 38.287 40.6074 38.287 40.6965 38.3761L41.0753 38.7549C41.1644 38.844 41.1644 38.9889 41.0753 39.078C41.0301 39.1233 40.9712 39.1444 40.9138 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M29.8182 39.1444C29.7593 39.1444 29.7019 39.1217 29.6566 39.078C29.5676 38.9889 29.5676 38.844 29.6566 38.7549L30.0355 38.3761C30.1246 38.287 30.2695 38.287 30.3585 38.3761C30.4476 38.4651 30.4476 38.61 30.3585 38.6991L29.9797 39.078C29.9344 39.1233 29.8755 39.1444 29.8182 39.1444Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M40.5349 28.4287C40.4761 28.4287 40.4187 28.406 40.3734 28.3623C40.2844 28.2732 40.2844 28.1283 40.3734 28.0392L40.7523 27.6604C40.8414 27.5713 40.9863 27.5713 41.0753 27.6604C41.1644 27.7494 41.1644 27.8943 41.0753 27.9834L40.6965 28.3623C40.6512 28.4076 40.5923 28.4287 40.5349 28.4287Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M28.0568 33.5971H27.5209C27.3941 33.5971 27.293 33.4945 27.293 33.3692C27.293 33.2439 27.3956 33.1412 27.5209 33.1412H28.0568C28.1836 33.1412 28.2847 33.2439 28.2847 33.3692C28.2847 33.496 28.1836 33.5971 28.0568 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M43.213 33.5971H42.6771C42.5519 33.5971 42.4492 33.4945 42.4492 33.3692C42.4492 33.2439 42.5519 33.1412 42.6771 33.1412H43.213C43.3383 33.1412 43.4409 33.2439 43.4409 33.3692C43.4409 33.496 43.3383 33.5971 43.213 33.5971Z" />
                            <path class="bpa-front-cdf__icon-vector-primary-color" d="M35.9141 33.2836H34.8242V28.5574C34.8242 28.257 35.0688 28.0125 35.3691 28.0125C35.6695 28.0125 35.9141 28.257 35.9141 28.5574V33.2836Z" />
                            <path d="M35.369 34.5334C36.0593 34.5334 36.6188 33.9738 36.6188 33.2835C36.6188 32.5933 36.0593 32.0337 35.369 32.0337C34.6787 32.0337 34.1191 32.5933 34.1191 33.2835C34.1191 33.9738 34.6787 34.5334 35.369 34.5334Z" fill="#2B4183"/>
                            <defs>
                                <filter id="filter0_d_2605_6524" x="0.897293" y="7.38576" width="41.5618" height="41.6711" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dx="1.4018" dy="1.4018"/>
                                    <feGaussianBlur stdDeviation="1.75226"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_2605_6524"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_2605_6524" result="shape"/>
                                </filter>
                            </defs>
                        </svg>
                    </div>
                    <div class="bpa-front--dt-ts__sub-heading">{{custom_service_duration_title}}</div>
                    <div class="bpa-front-cdf__desc">{{custom_service_description_title}}</div>
                    <el-form>
                        <div class="bpa-front-module--bd-form">
                            <el-row class="bpa-bd-fields-row">
                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                    <template>
                                        <div class="bpa-bdf--single-col-item">
                                            <el-form-item>
                                                <el-select v-model="appointment_step_form_data.custom_service_duration_value" popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" class="bpa-front-form-control" :placeholder="custom_please_select_title" @change="bookingpress_change_custom_duration_first($event)">
                                                    <el-option :label="custom_please_select_title" value=""></el-option>
                                                    <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value" ></el-option>
                                                </el-select>
                                            </el-form-item>
                                        </div>
                                    </template>
                                </el-col>
                            </el-row>
                        </div>
                    </el-form>
                </div>
                <div class="bpa-front-dt__custom-duration-sm-wrapper" v-else-if="is_display_custom_duration_loader == false">
                    <div class="bpa-front--dt__ts-heading">
                        <div class="bpa-front-module-heading">{{custom_duration_title}}</div>
                    </div>
                    <div class="bpa-front--dt__custom-duration-card">												
                        <div class="bpa-front-cdc__left">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-.22-13h-.06c-.4 0-.72.32-.72.72v4.72c0 .35.18.68.49.86l4.15 2.49c.34.2.78.1.98-.24.21-.34.1-.79-.25-.99l-3.87-2.3V7.72c0-.4-.32-.72-.72-.72z"/></svg>
                            <el-select popper-class="bpa-custom-dropdown bpa-custom-duration-dropdown" class="bpa-front-form-control" v-model="appointment_step_form_data.custom_service_duration_value" class="bpa-front-form-control" @change="bookingpress_change_custom_duration($event)">
                                <el-option v-for="duration_slot_data in bookingpress_custom_service_durations_slot" :label="duration_slot_data.text" :value="duration_slot_data.value"></el-option>
                            </el-select>
                        </div>
                        <div class="bpa-front-cdc__right">														
                            <div class="bpa-front-cdc__right-title">{{custom_price_title}}<div class="bpa-front-cdc__price-val">{{appointment_step_form_data.selected_service_price}}</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        function bookingpress_after_insert_entry_data_from_frontend_func($entry_id,$bookingpress_appointment_data) {

            global $wpdb,$tbl_bookingpress_appointment_meta;

            if(!empty($entry_id)) {
                if(!empty($bookingpress_appointment_data['custom_service_duration_value'])) {
                    $custom_service_duration = $bookingpress_appointment_data['custom_service_duration_value'];            
                    $bookingpress_db_fields = array(
                        'bookingpress_entry_id' => $entry_id,
                        'bookingpress_appointment_id' => 0,
                        'bookingpress_appointment_meta_value' => $custom_service_duration,
                        'bookingpress_appointment_meta_key' => 'appointment_custom_duration_value',
                    );
                    $wpdb->insert($tbl_bookingpress_appointment_meta, $bookingpress_db_fields);
                }
            }
        }

        function bookingpress_add_appointment_model_reset_func() {
            ?>
            vm2.appointment_formdata.enable_custom_service_duration= false;
            vm2.appointment_formdata.custom_service_duration_value = '';
            vm2.appointment_formdata.custom_service_real_price = '';
            vm2.appointment_formdata.custom_service_duration_real_value = '';
            vm2.bookingpress_custom_service_durations_slot = [];
            <?php
        }

        function bookingpress_modify_calendar_data_fields_func($bookingpress_calendar_vue_data_fields) {
            global $bookingpress_services;

            if(!empty($bookingpress_calendar_vue_data_fields['appointment_services_list']) ) {                

                foreach($bookingpress_calendar_vue_data_fields['appointment_services_list'] as $key => $value ) {
                    if(!empty($value['category_services'])) {
                        foreach($value['category_services'] as $key2 => $value2 ) {
                            $bookingpress_service_id = !empty($value2['service_id']) ? intval($value2['service_id']) : 0;
                            $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                            $enable_custom_service_duration = !empty($enable_custom_service_duration ) && $enable_custom_service_duration == 'true' ? true : false;
                            $bookingpress_calendar_vue_data_fields['appointment_services_list'][$key]['category_services'][$key2]['enable_custom_service_duration'] =$enable_custom_service_duration;
                        }                            
                    }
                }                
            }

            $bookingpress_calendar_vue_data_fields['appointment_formdata']['enable_custom_service_duration'] = false;
            $bookingpress_calendar_vue_data_fields['appointment_formdata']['custom_service_duration_value'] = '';
            $bookingpress_calendar_vue_data_fields['appointment_formdata']['custom_service_real_price'] = '';
            $bookingpress_calendar_vue_data_fields['appointment_formdata']['custom_service_duration_real_value'] = '';

            $bookingpress_calendar_vue_data_fields['rules']['custom_service_duration_value'] = 
                array(
                    array(
                        'required' => true,
                        'message'  => esc_html__('Please select duration', 'bookingpress-custom-service-duration'),
                        'trigger'  => 'change',
                    ),
                );            
            $bookingpress_calendar_vue_data_fields['bookingpress_custom_service_durations_slot'] = array();
            return $bookingpress_calendar_vue_data_fields;


            return $bookingpress_calendar_vue_data_fields;
        }

        function bookingpress_calendar_add_appointment_model_reset_func() {
            ?>
                vm.appointment_formdata.enable_custom_service_duration= false;
                vm.appointment_formdata.custom_service_duration_value = '';
                vm.appointment_formdata.custom_service_real_price = '';
                vm.appointment_formdata.custom_service_duration_real_value = '';
                vm.bookingpress_custom_service_durations_slot = [];
            <?php
        }

        function bookingpress_front_modify_cart_data_filter_func($bookingpress_appointment_details) {
            if(isset($bookingpress_appointment_details['custom_service_duration_value'])) {
                $bookingpress_appointment_details['custom_service_duration_value'] = !empty( $bookingpress_appointment_details['custom_service_duration_value'] ) ? intval($bookingpress_appointment_details['custom_service_duration_value']) : '';
            }
            return $bookingpress_appointment_details;
        }

        function bookingpress_add_custom_service_duration_data_func($custom_duration_data) {            
            $custom_duration_data .='
            if(currentValue.enable_custom_service_duration != "undefiend" && ( currentValue.enable_custom_service_duration == true || currentValue.enable_custom_service_duration == "true")) {
                var custom_service_duration_real_value = vm5.appointment_step_form_data.custom_service_duration_real_value; 
                var bookingpress_service_duration_unit = currentValue.bookingpress_service_duration_unit;
                var bookingpress_service_duration_val = currentValue.bookingpress_service_duration_val;                
                if(bookingpress_service_duration_unit == "h") {
                    custom_service_duration_real_value = custom_service_duration_real_value / 60; 
                } else if(bookingpress_service_duration_unit == "d") {
                    custom_service_duration_real_value = custom_service_duration_real_value /1440 ; 
                }
                currentValue.bookingpress_service_duration_val = custom_service_duration_real_value;
                currentValue.custom_service_real_price = vm5.appointment_step_form_data.custom_service_real_price;
                bookingpress_service_original_price = bookingpress_service_price = parseFloat(vm5.appointment_step_form_data.custom_service_real_price);                
                currentValue.custom_service_duration_value = vm5.appointment_step_form_data.custom_service_duration_value;
            }';
            return $custom_duration_data;
        }

        function bookingpress_reset_custom_duration_data_func($bookingpress_reset_custom_duration_data) {

            $bookingpress_reset_custom_duration_data .= ' 
                vm.appointment_step_form_data.custom_service_duration_value = "";
                vm.appointment_step_form_data.custom_service_duration_real_value = "";
                vm.appointment_step_form_data.custom_service_real_price ="" ;
                vm.appointment_step_form_data.selected_date = "";
                vm.appointment_step_form_data.selected_start_time = "";
            ';
            
            return $bookingpress_reset_custom_duration_data;
        }

        function bookingpress_modify_dashboard_data_fields_func($bookingpress_dashboard_vue_data_fields) {

            global $bookingpress_services;
            if(!empty($bookingpress_dashboard_vue_data_fields['appointment_services_list']) ) {
                foreach($bookingpress_dashboard_vue_data_fields['appointment_services_list'] as $key => $value ) {
                    if(!empty($value['category_services'])) {
                        foreach($value['category_services'] as $key2 => $value2 ) {
                            $bookingpress_service_id = !empty($value2['service_id']) ? intval($value2['service_id']) : 0;
                            $enable_custom_service_duration = $bookingpress_services->bookingpress_get_service_meta($bookingpress_service_id,'enable_custom_service_duration');
                            $enable_custom_service_duration = !empty($enable_custom_service_duration ) && $enable_custom_service_duration == 'true' ? true : false;
                            $bookingpress_dashboard_vue_data_fields['appointment_services_list'][$key]['category_services'][$key2]['enable_custom_service_duration'] =$enable_custom_service_duration;
                        }                            
                    }
                }                
            }
            $bookingpress_dashboard_vue_data_fields['appointment_formdata']['enable_custom_service_duration'] = false;
            $bookingpress_dashboard_vue_data_fields['appointment_formdata']['custom_service_duration_value'] = '';
            $bookingpress_dashboard_vue_data_fields['appointment_formdata']['custom_service_real_price'] = '';
            $bookingpress_dashboard_vue_data_fields['appointment_formdata']['custom_service_duration_real_value'] = '';
            $bookingpress_dashboard_vue_data_fields['rules']['custom_service_duration_value'] = 
                array(
                    array(
                        'required' => true,
                        'message'  => esc_html__('Please select duration', 'bookingpress-custom-service-duration'),
                        'trigger'  => 'change',
                    ),
                );            
            $bookingpress_dashboard_vue_data_fields['bookingpress_custom_service_durations_slot'] = array();
            return $bookingpress_dashboard_vue_data_fields;
        }       
        function bookingpress_add_customize_extra_section_func() {
            ?>
                <h5><?php esc_html_e('Custom Duration Labels', 'bookingpress-custom-service-duration'); ?></h5>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Service duration title', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.custom_service_duration_title" class="bpa-form-control"></el-input>
                </div>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Service duration description', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.custom_service_description_title" class="bpa-form-control"></el-input>
                </div>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Please select placeholder', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.custom_please_select_title" class="bpa-form-control"></el-input>
                </div>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Custom duration title', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.custom_duration_title" class="bpa-form-control"></el-input>
                </div>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Price title', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.custom_price_title" class="bpa-form-control"></el-input>
                </div>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('No custom service duration selected', 'bookingpress-custom-service-duration'); ?></label>
                    <el-input v-model="timeslot_container_data.no_custom_service_duration_selected_title" class="bpa-form-control"></el-input>
                </div>
            <?php
        }

        function bookingpress_modify_customize_data_fields_func($bookingpress_customize_vue_data_fields) {

            $bookingpress_customize_vue_data_fields['timeslot_container_data']['custom_service_duration_title'] = '';
            $bookingpress_customize_vue_data_fields['timeslot_container_data']['custom_service_description_title'] = '';
            $bookingpress_customize_vue_data_fields['timeslot_container_data']['custom_please_select_title'] = '';
            $bookingpress_customize_vue_data_fields['timeslot_container_data']['custom_duration_title'] = '';
            $bookingpress_customize_vue_data_fields['timeslot_container_data']['custom_price_title'] = '';
            $bookingpress_customize_vue_data_fields['timeslot_container_data']['no_custom_service_duration_selected_title'] = '';

            return $bookingpress_customize_vue_data_fields;
        }  

        function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings) {

            $booking_form_settings['timeslot_container_data']['custom_service_duration_title'] = '';
			$booking_form_settings['timeslot_container_data']['custom_service_description_title'] = '';
            $booking_form_settings['timeslot_container_data']['custom_please_select_title'] = '';
            $booking_form_settings['timeslot_container_data']['custom_duration_title'] = '';
			$booking_form_settings['timeslot_container_data']['custom_price_title'] = '';
            $booking_form_settings['timeslot_container_data']['no_custom_service_duration_selected_title'] = '';
            
            return $booking_form_settings;
        }

        function bookingpress_my_appointment_modify_data_for_rescheduling_func($response,$reschedule_appointment_id) {
            global $tbl_bookingpress_appointment_bookings,$wpdb,$tbl_bookingpress_appointment_meta;

            if(!empty($reschedule_appointment_id)) {
                $bookingpress_appointment_data = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_enable_custom_duration FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $reschedule_appointment_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm

                $enable_custom_service_duration = !empty($bookingpress_appointment_data['bookingpress_enable_custom_duration']) && $bookingpress_appointment_data['bookingpress_enable_custom_duration'] == 1 ? 'true' : 'false';

                $response['enable_custom_service_duration'] =  $enable_custom_service_duration;
                
                $bookingpress_appointment_custom_duration = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_appointment_meta_value FROM {$tbl_bookingpress_appointment_meta} WHERE bookingpress_appointment_id = %d AND bookingpress_appointment_meta_key = %s", $reschedule_appointment_id,'appointment_custom_duration_value' ));// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason $tbl_bookingpress_appointment_meta is a table name. false alarm.
                $response['custom_service_duration_value'] = $bookingpress_appointment_custom_duration;
            }    
            return $response;
        }

        function bookingpress_modify_rescheduled_appointment_xhr_data_func() {
            ?>
            vm.reschedule_custom_service_duration_value = response.data.custom_service_duration_value;

            vm.reschedule_enable_custom_service_duration = response.data.enable_custom_service_duration;
            disablePostData.custom_service_duration_value = vm.reschedule_custom_service_duration_value;
            disablePostData.enable_custom_service_duration = vm.reschedule_enable_custom_service_duration;
            <?php
        }

        function bookingpress_modify_assign_services_xhr_data_func() {
            ?>
                vm2.assign_staff_member_list.forEach(function(item,index,arr) {
                    if(item.staffmember_custom_service != 'undefined' && item.staffmember_custom_service != '' && item.staffmember_custom_service != null) {
                        item.staffmember_custom_service.forEach(function(item2,index2,arr2) {									
                            vm2.max_duration_time_options.forEach(function(item3,index3,arr3) {
                                if( item3.value == item2.service_duration) {
                                    vm2.assign_staff_member_list[index]['staffmember_custom_service'][index2]['service_duration_text'] = item3.text;
                                }
                            });	
                        });	
                    }
                });
            <?php
        }        

        function bookingpress_modify_rescheduled_front_timing_xhr_func() {            
            ?>
            postData.custom_service_duration_value = vm.reschedule_custom_service_duration_value;
            postData.enable_custom_service_duration = vm.reschedule_enable_custom_service_duration;
            <?php
        }

        function bookingpress_front_appointment_add_dynamic_data_func( $bookingpress_front_appointment_vue_data_fields ) {

            $bookingpress_front_appointment_vue_data_fields['reschedule_custom_service_duration_value'] =  '';
            $bookingpress_front_appointment_vue_data_fields['reschedule_enable_custom_service_duration'] =  '';

            return $bookingpress_front_appointment_vue_data_fields; 
        }

        function bookingpress_duplicate_more_details_func($duplicated_service_id, $original_service_id){
            
			global $wpdb,$tbl_bookingpress_custom_service_durations,$tbl_bookingpress_custom_staffmembers_service_durations,$bookingpress_services;
			//Duplicate Custom Duration details
			$bookingpress_duplicate_custom_services_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_custom_service_durations} WHERE bookingpress_service_id = %d", $original_service_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_custom_service_durations is table name.

			if(!empty($bookingpress_duplicate_custom_services_data)){
				foreach($bookingpress_duplicate_custom_services_data as $assigned_custom_service_key => $assigned_custom_service_val){

					$bookingpress_custom_service_duration_id = $assigned_custom_service_val['bookingpress_custom_service_duration_id'];
					$bookingpress_duplicate_custom_services = array(
						'bookingpress_service_duration_val' => $assigned_custom_service_val['bookingpress_service_duration_val'],
						'bookingpress_service_id' => $duplicated_service_id,
						'bookingpress_service_duration_price' => $assigned_custom_service_val['bookingpress_service_duration_price'],
						'bookingpress_custom_duration_created_date' => current_time( 'mysql' )
					);
					$wpdb->insert($tbl_bookingpress_custom_service_durations, $bookingpress_duplicate_custom_services);

					$bookingpress_assigned_custom_staffmember_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_custom_staffmembers_service_durations} WHERE bookingpress_service_id = %d AND bookingpress_custom_service_duration_id = %d", $original_service_id,$bookingpress_custom_service_duration_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_custom_service_durations is table name.

					$bookingpress_dummy_custom_service_duration_id = $wpdb->get_var($wpdb->prepare("SELECT bookingpress_custom_service_duration_id FROM {$tbl_bookingpress_custom_service_durations} WHERE bookingpress_service_id = %d ORDER BY bookingpress_custom_service_duration_id DESC", $duplicated_service_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_custom_staffmembers_service_durations is table name.

					if(!empty($bookingpress_assigned_custom_staffmember_data)) {
						foreach($bookingpress_assigned_custom_staffmember_data as $assigned_custom_staff_key => $assigned_custom_staff_val) {
							$bookingpress_duplicate_custom_staff_services = array(
								'bookingpress_staffmember_id' => $assigned_custom_staff_val['bookingpress_staffmember_id'],
								'bookingpress_service_id' => $duplicated_service_id,
								'bookingpress_custom_service_duration_id' => $bookingpress_dummy_custom_service_duration_id,								
								'bookingpress_staffmember_price' => $assigned_custom_staff_val['bookingpress_staffmember_price'],
								'bookingpress_staffmember_duration_created_date' =>  current_time( 'mysql' )
							);		
							$wpdb->insert($tbl_bookingpress_custom_staffmembers_service_durations, $bookingpress_duplicate_custom_staff_services);
						}
					}
				}
			}
			
			//Duplicate service capacity
			$bookingpress_original_max_capacity = $bookingpress_services->bookingpress_get_service_meta( $original_service_id, 'enable_custom_service_duration' );
			$bookingpress_services->bookingpress_add_service_meta( $duplicated_service_id, 'enable_custom_service_duration', $bookingpress_original_max_capacity );
        }
    }

	global $bookingpress_custom_service_duration;
	$bookingpress_custom_service_duration = new bookingpress_custom_service_duration();
}