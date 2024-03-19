<?php
if (!class_exists('bookingpress_location')) {
	class bookingpress_location Extends BookingPress_Core {
        function __construct(){
            global $wp, $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_locations_staff_workhours, $tbl_bookingpress_locations_service_special_days, $tbl_bookingpress_locations_staff_special_days, $BookingPress;

            $tbl_bookingpress_locations = $wpdb->prefix.'bookingpress_locations';
            $tbl_bookingpress_locations_service_staff_pricing_details = $wpdb->prefix.'bookingpress_locations_service_staff_pricing_details';
            $tbl_bookingpress_locations_service_workhours = $wpdb->prefix.'bookingpress_locations_service_workhours';
            $tbl_bookingpress_locations_staff_workhours = $wpdb->prefix.'bookingpress_locations_staff_workhours';
            $tbl_bookingpress_locations_service_special_days = $wpdb->prefix.'bookingpress_locations_service_special_days';
            $tbl_bookingpress_locations_staff_special_days = $wpdb->prefix.'bookingpress_locations_staff_special_days';
            

            register_activation_hook(BOOKINGPRESS_LOCATION_DIR.'/bookingpress-location.php', array('bookingpress_location', 'install'));
            register_uninstall_hook(BOOKINGPRESS_LOCATION_DIR.'/bookingpress-location.php', array('bookingpress_location', 'uninstall'));

            add_action('user_register', array($this,'bookingpress_location_add_capabilities_to_new_user'));

            add_action('set_user_role', array($this, 'bookingpress_location_assign_caps_on_role_change'), 10, 3); 

            add_action( 'admin_notices', array( $this, 'bookingpress_location_admin_notices') );

            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if( is_plugin_active( 'bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) && !empty( $BookingPress->bpa_pro_plugin_version() ) && version_compare( $BookingPress->bpa_pro_plugin_version(), '2.6.1', '>=' ) ){

                //add value to slug
                add_action( 'init', array( $this, 'bookingpress_location_page_slugs' ), 11 );

                //add menu at WordPress sidebar
                add_action('bookingpress_add_specific_menu', array($this, 'bookingpress_add_specific_menu_func'), 10, 1);

                //Load default data variables
                add_action( 'admin_init', array( $this, 'bookingpress_location_vue_data_fields_func') );

                //Load CSS & JS at admin side
                add_action( 'admin_enqueue_scripts', array( $this, 'set_css' ), 12 );
                add_action( 'admin_enqueue_scripts', array( $this, 'set_js' ), 12 );

                add_filter( 'bookingpress_location_dynamic_view_load', array( $this, 'bookingpress_load_coupons_view_func' ), 10 );
                add_action( 'bookingpress_location_dynamic_vue_methods', array( $this, 'bookingpress_location_vue_methods_func' ) );
                add_action( 'bookingpress_location_dynamic_on_load_methods', array( $this, 'bookingpress_location_on_load_methods_func' ) );
                add_action( 'bookingpress_location_dynamic_data_fields', array( $this, 'bookingpress_location_dynamic_data_fields_func' ) );
                add_action( 'bookingpress_location_dynamic_helper_vars', array( $this, 'bookingpress_location_dynamic_helper_vars_func' ) );

                add_filter('bookingpress_modify_capability_data', array($this, 'bookingpress_modify_capability_data_func'), 11, 1);

                //Get Location details
                add_action('wp_ajax_bookingpress_get_locations', array($this, 'bookingpress_get_locations_func'));

                //Save Location details
                add_action('wp_ajax_bookingpress_add_location', array($this, 'bookingpress_save_location_details_func'));

                //Delete Location
                add_action('wp_ajax_bookingpress_delete_location', array($this, 'bookingpress_delete_location_func'));

                //Get edit location details
                add_action('wp_ajax_bookingpress_get_edit_location', array($this, 'bookingpress_get_edit_location_func'));

                //Add switch at general settings
                add_action('bookingpress_add_general_setting_section', array($this, 'bookingpress_add_general_setting_section_func'));
                add_filter('bookingpress_add_setting_dynamic_data_fields', array($this, 'bookingpress_add_setting_dynamic_data_fields_func'), 12);

                //Add menu item to top bookingpress menu
                add_action('bookingpress_add_dynamic_menu_item_to_top', array($this, 'bookingpress_add_dynamic_menu_item_to_top_func'));

                add_filter( 'bookingpress_modify_form_sequence_flag', '__return_true');

                add_action( 'bookingpress_form_sequence_list_item', array( $this, 'bookingpress_form_sequence_list_item_location') );

                add_filter( 'bookingpress_location_selection_visibility', '__return_true');

                add_filter( 'bookingpress_modify_form_sequence_arr', array( $this, 'bookingpress_customization_form_sequence_data_with_location'), 11 );

                add_action( 'bookingpress_add_customize_booking_form_tab', array( $this, 'bookingpress_add_location_step_for_customize_tab'));
                add_action( 'bookingpress_add_customize_booking_form_panel', array( $this, 'bookingpress_add_location_information_data'));

                add_filter( 'bookingpress_customize_add_dynamic_data_fields', array( $this, 'bookingpress_customize_add_dynamic_data_fields_location_func'),11 );
                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this, 'bookingpress_get_booking_form_customize_location_data_filter_func'),10,1);
                add_action( 'bookingpress_customize_dynamic_vue_methods', array( $this, 'bookingpress_customize_dynamic_vue_methods_location_func') );

                /** Bulk Delete for Location */
                add_action( 'wp_ajax_bookingpress_bulk_location', array( $this, 'bookingpress_delete_location_bulk_action') );

                add_action( 'wp_ajax_bookingpress_position_location', array( $this, 'bookingpress_update_location_position') );

                add_action( 'wp_ajax_bookingpress_check_staff_with_different_location', array( $this, 'bookingpress_check_staffmember_for_different_location') );

                /* location placeholder */
                add_filter( 'bookingpress_add_global_option_data', array( $this, 'bookingpress_add_global_option_data_func' ), 11 );
                add_action( 'bookingpress_notification_external_message_plachoders', array( $this,'bookingpress_location_notification_placeholder'));
                add_filter( 'bookingpress_add_dynamic_notification_data_fields', array( $this, 'bookingpress_add_location_field_notification_data_fields_func' ), 10 );
                add_action( 'bookingpress_add_outside_notification_placeholders', array( $this, 'bookingpress_add_outside_notification_placeholders_func'));
                add_action( 'bookingpress_add_ocalendar_outside_notification_placeholders', array( $this, 'bookingpress_add_ocalendar_outside_notification_placeholders_func'));
                add_action( 'bookingpress_add_zapier_outside_notification_placeholders', array( $this, 'bookingpress_add_zapier_outside_notification_placeholders_func'));
                add_action( 'bookingpress_zapier_add_placeholder_outside', array($this, 'bookingpress_zapier_add_placeholder_outside_func'));
                add_action( 'bookingpress_add_zoom_outside_notification_placeholders', array( $this, 'bookingpress_add_zoom_outside_notification_placeholders'));
                add_action( 'bookingpress_manage_appointment_before_filter_content', array ( $this, 'bookingpress_manage_appointments_location_filter_content') );

                add_action( 'bookingpress_generate_booking_form_customize_css', array( $this, 'bookingpress_add_location_form_customize_css' ), 10, 2);

                add_action( 'bookingpress_modify_appointment_data_fields', array( $this, 'bookingpress_add_location_filters_for_appointment') );

                add_action( 'bookingpress_appointment_add_dynamic_vue_methods', array( $this, 'bookingpress_appointment_add_dynamic_vue_methods_location_func') );

                //action for cloumn display in appointment section
                add_action( 'bookingpress_add_column_outsite', array( $this, 'bookingpress_add_column_outsite_func'));
                add_filter( 'bookingpress_appointment_add_view_field', array( $this, 'bookingpress_appointment_add_view_field_func'),10,2);
                add_action( 'add_bookingpress_appointment_details_outside', array( $this, 'add_bookingpress_appointment_details_outside_func'));

                // action for display location dropdown in share appointment
                // add_action( 'bookingpress_add_share_url_content_outside', array( $this, 'bookingpress_add_share_url_content_outside_func'));

                //add action for awating list column add
                add_action( 'bookingpress_waiting_list_outside_add_column', array( $this,'bookingpress_waiting_list_outside_add_column_func'));
                add_action( 'add_bookingpress_location_appointment_details_outside', array( $this, 'add_bookingpress_location_appointment_details_outside_func'));

                //action for email notification placed the data
                add_filter( 'bookingpress_modify_email_content_filter', array( $this, 'bookingpress_modify_email_content_filter_func' ), 10, 2 );
                add_filter( 'bookingpress_change_label_value_for_invoice', array( $this, 'bookingpress_change_label_value_for_invoice_func'), 10, 2);

                //filter zapier change the data
                add_filter('bookingpress_modify_appointment_default_field_zapier', array( $this, 'bookingpress_modify_appointment_default_field_zapier_func'),10,3);
                add_filter('bookingpress_appointment_data_relpace_outside', array( $this, 'bookingpress_appointment_data_replace_outside_func'),10,3 ); 
                add_filter( 'bookingpress_modify_placeholder_data_outside_zapier', array( $this, 'bookingpress_modify_placeholder_data_outside_zapier_func'));
            
                //search location data for appointment section
                add_action( 'bookingpress_appointment_add_post_data', array( $this, 'bookingpress_appointment_add_post_data_func' ), 10 );
                add_action( 'bookingpress_appointment_view_add_filter', array( $this, 'bookingpress_appointment_view_add_filter_func'), 10,2);
                add_action( 'bookingpress_appointment_reset_filter', array( $this, 'bookingpress_appointment_reset_filter_func'),10);
            
                /*Location chanegs for the calendar page */
                add_action( 'bookingpress_calendar_filter_content', array( $this, 'bookingpress_calendar_filter_for_location_func'));
                add_filter( 'bookingpress_modify_calendar_data_fields', array( $this, 'bookingpress_modify_calendar_location_data_fields_func' ), 10 );
                add_filter( 'bookingpress_modify_dashboard_data_fields', array( $this, 'bookingpress_modify_calendar_location_data_fields_func' ), 10 );
                add_action( 'bookingpress_add_dynamic_vue_methods_for_calendar', array($this, 'bookingpress_add_dynamic_vue_methods_for_calendar__location_func'), 10);
                add_filter( 'bookingpress_calendar_add_view_filter', array( $this, 'bookingpress_calendar_add_view_filter_location_func' ), 10, 2 );
                add_filter( 'bookingpress_modify_popover_appointment_data_query', array($this, 'bookingpress_modify_popover_appointment_location_data_query_func'), 10, 2);
                add_filter( 'bookingpress_modify_calendar_appointment_details', array($this, 'bookingpress_modify_calendar_location_appointment_details_func'), 10, 2);
                add_filter( 'bookingpress_modify_calendar_all_appointment_details', array($this, 'bookingpress_modify_calendar_all_appointment_details_func'), 10, 2);
                add_action( 'bookingpress_calendar_appointment_xhr_response', array($this, 'bookingpress_calendar_appointment_xhr_response_func'));
                add_action( 'wp_ajax_bookingpress_remove_location_file', array( $this, 'bookingpress_remove_location_file_func') );


                /** Admin panel add/edit appointment fields */

                add_filter( 'bookingpress_modify_appointment_data_fields', array( $this, 'bookingpress_add_location_for_appointment_data_fields') );
                add_filter( 'bookingpress_modify_dashboard_data_fields', array( $this, 'bookingpress_add_location_for_appointment_data_fields') );
                add_filter( 'bookingpress_modify_dashboard_data_fields', array( $this, 'bookingpress_modify_dashborard_data_fields_func') );
                add_filter( 'bookingpress_modify_calendar_data_fields', array( $this, 'bookingpress_add_location_for_appointment_data_fields') );
                add_action( 'bookingpress_add_appointment_custom_service_duration_field_section', array( $this, 'bookingpress_add_location_field_for_add_appointment'));

                add_action( 'bookingpress_before_change_backend_service', array( $this, 'bookingpress_set_visible_locations_for_backend') ); //only for service
                add_action( 'bookingpress_after_select_staff_backend', array( $this, 'bookingpress_set_visible_locations_with_staff_for_backend') );

                add_filter( 'bookingpress_modify_backend_add_appointment_entry_data', array( $this, 'bookingpress_modify_backend_add_appointment_entry_data_location'), 10, 2 );
                add_filter( 'bookingpress_modify_appointment_booking_fields', array( $this, 'bookingpress_modify_appointment_booking_fields_with_location_backend'), 11, 3);

                add_action( 'bookingpress_edit_appointment_details', array( $this, 'bookingpress_edit_appointment_details_for_location') );

                add_action( 'bookingpress_add_additional_filter_for_report', array( $this, 'bookingpress_add_location_filter_for_report') );

                add_filter( 'bookingpress_modify_report_data_fields', array( $this, 'bookingpress_modify_report_data_fields_with_locations' ));
                add_action( 'bookingpress_modify_report_search_data', array( $this, 'bookingpress_modify_report_search_data_with_locations'));
                add_action( 'bookingpress_modify_report_search_data_for_chart', array( $this, 'bookingpress_modify_report_search_data_with_locations'));
                add_action( 'bookingpress_appointment_report_view_add_filter', array( $this, 'bookingpress_appointment_report_view_add_filter_with_location'), 10, 2);

                add_action( 'bookingpress_add_external_field_for_share_url', array( $this, 'bookingpress_add_location_field_for_share_url') );
                add_action( 'bookingpress_share_url_after_select_service', array( $this, 'bookingpress_share_url_after_select_service_func') );
                add_action( 'bookingpress_share_url_after_select_staff', array( $this, 'bookingpress_share_url_after_select_staffmember') );
                //add_action( 'bookingpress_appointment_add_dynamic_vue_methods', array( $this, 'bookingpress_appointment_add_dynamic_vue_methods_func'), 11 );

                add_filter( 'bookingpress_filter_generated_share_url_externally', array( $this, 'bookingpress_generate_share_url_with_location'), 11, 2 );

                /* need help button */
                add_action( 'bpa_add_extra_tab_outside_func', array( $this,'bpa_add_extra_tab_outside_func_arr'));

                add_action( 'wp_ajax_bookingpress_upload_location', array( $this, 'bookingpress_upload_location_image') );

                
                /* add location messsage */
                add_action( 'bookingpress_add_setting_msg_outside', array( $this, 'bookingpress_add_setting_msg_outside_func'));

                /* location export appointment section*/
                add_filter('modify_export_appointment_field_list', array( $this, 'export_appointment_field_list_arr'));
                add_filter('modify_export_appointment_data_checked_fields_outside', array($this,'modify_export_appointment_data_checked_fields_outside_arr'));
                add_action('bpa_export_post_data_outside', array( $this,'bpa_export_post_data_outside_func'));
                add_filter('bookingpress_export_data_fields_data_outside', array( $this,'bookingpress_export_data_fields_data_func'),10,3);
                add_filter('bookingpress_export_appointment_data_filter', array( $this, 'bookingpress_export_appointment_data_filter_func'));

                /* Multi-Language Function Start Here */
                if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {
                    add_filter('bookingpress_modified_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
                    add_filter('bookingpress_modified_location_language_translate_fields',array($this,'bookingpress_modified_location_language_translate_fields_func'),10);
                    add_filter('bookingpress_modified_language_translate_fields_section',array($this,'bookingpress_modified_language_translate_fields_section_func'),10);
                    add_filter('bookingpress_modified_customize_form_language_translate_fields',array($this,'bookingpress_modified_customize_form_language_translate_fields_func'),10);
                }

                 /*To add location in ics file*/
                 add_filter( 'bpa_add_timezone_parameters_for_ics', array( $this, 'bpa_generate_ics_with_timzone_location_data'), 15, 2 );    

                 add_action( 'bookingpress_modify_readmore_link', array( $this, 'bookingpress_modify_readmore_link_location') );
            }

            add_action('activated_plugin',array($this,'bookingpress_is_location_addon_activated'),11,2);
	    
	                add_action( 'admin_init', array( $this, 'bookingpress_upgrade_location_addon_data'));
        }
        function bookingpress_modify_readmore_link_location(){
            ?>
            var selected_tab = sessionStorage.getItem("current_tabname");
            
            if( "location" == bpa_requested_module ){
                read_more_link = "https://www.bookingpressplugin.com/documents/location-addon/";
            }
            <?php
        }

        function bpa_generate_ics_with_timzone_location_data( $string, $appointment_data ){
            if( empty( $appointment_data ) ){
                return $string;
            }
            if( empty( $appointment_data['bookingpress_location_id'] ) ){
                return $string;
            }
            global $wpdb,$tbl_bookingpress_locations;
            $location_data = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name,bookingpress_location_address FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $appointment_data['bookingpress_location_id']), ARRAY_A ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations is a table name. false alarm
            if(empty($location_data)) {
                return $string;
            }
            else {
                $location_name = isset($location_data['bookingpress_location_name']) ? $location_data['bookingpress_location_name'] : '';
                $location_address = isset($location_data['bookingpress_location_address']) ? $location_data['bookingpress_location_address'] : '';
                $location_string =  "LOCATION:$location_address".",".$location_name."\r\n";
                $string = str_replace( "BEGIN:VEVENT\r\n", "BEGIN:VEVENT\r\n".$location_string."", $string);
            }
            return $string;
        }  

        function bookingpress_upgrade_location_addon_data(){
            global $BookingPress, $bookingpress_location_version;

            $bookingpress_location_db_version = get_option('bookingpress_location_version');

            if( version_compare( $bookingpress_location_db_version, '1.2', '<' ) ){
                $bookingpress_load_location_update_file = BOOKINGPRESS_LOCATION_DIR . '/core/views/upgrade_latest_location_data.php';
                include $bookingpress_load_location_update_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();
            }
        }
        
        function bookingpress_location_admin_notices(){
            if( !function_exists('is_plugin_active') ){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if( !is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php') ){
                echo "<div class='notice notice-warning'><p>" . esc_html__('BookingPress - Location plugin requires BookingPress Premium Plugin installed and active.', 'bookingpress-location') . "</p></div>";
            }

            if( file_exists( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) ){
                $bpa_pro_plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' );
                $bpa_pro_plugin_version = $bpa_pro_plugin_info['Version'];
                
                if( version_compare( $bpa_pro_plugin_version, '2.6.1', '<' ) ){
                    echo "<div class='notice notice-error is-dismissible'><p>".esc_html__("It's Required to update the BookingPress Premium Plugin to version 2.6.1 or higher in order to use the BookingPress Location plugin", "bookingpress-location").".</p></div>";
                }
            }
        }

        function bookingpress_is_location_addon_activated($plugin,$network_activation)
        {  
            $myaddon_name = "bookingpress-location/bookingpress-location.php";

            if($plugin == $myaddon_name)
            {

                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Location Add-on', 'bookingpress-location');
					/* translators: 1. Redirect URL link starts 2.Redirect URL link ends */
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-location'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - Text is escaped properly
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Location Add-on', 'bookingpress-location');
                    /* translators: 1. Redirect URL link starts 2.Redirect URL link ends */
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-location'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - Text is escaped properly
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_LOCATION_STORE_URL;
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
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Location Add-on', 'bookingpress-location');
                            /* translators: 1. Redirect URL link starts 2.Redirect URL link ends */
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-location'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - Text is escaped properly
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($myaddon_name, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Location Add-on', 'bookingpress-location');
                        /* translators: 1. Redirect URL link starts 2.Redirect URL link ends */
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-location'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>'); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - Text is escaped properly
                        die;
                    }
                }
            }

        }

        function bookingpress_upload_location_image(){
            global $BookingPress;

            $return_data = array(
                'error'            => 0,
                'msg'              => '',
                'upload_url'       => '',
                'upload_file_name' => '',
            );

            $bpa_check_authorization = $this->bpa_check_authentication( 'upload_location_avatar', true, 'bookingpress_upload_location' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');
                $response = array();
                $response['variant'] = 'error';
                $response['error'] = 1;
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $bookingpress_fileupload_obj = new bookingpress_fileupload_class($_FILES['file']); // phpcs:ignore

            if (! $bookingpress_fileupload_obj ) {
                $return_data['error'] = 1;
                $return_data['msg']   = $bookingpress_fileupload_obj->error_message;
            }

            $bookingpress_fileupload_obj->check_cap          = true;
            $bookingpress_fileupload_obj->check_nonce        = true;
            $bookingpress_fileupload_obj->nonce_data         = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
            $bookingpress_fileupload_obj->nonce_action       = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '';
            $bookingpress_fileupload_obj->check_only_image   = true;
            $bookingpress_fileupload_obj->check_specific_ext = false;
            $bookingpress_fileupload_obj->allowed_ext        = array();

            $file_name                = current_time('timestamp') . '_' . isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '';
            $upload_dir               = BOOKINGPRESS_TMP_IMAGES_DIR . '/';
            $upload_url               = BOOKINGPRESS_TMP_IMAGES_URL . '/';
            $bookingpress_destination = $upload_dir . $file_name;

            $upload_file = $bookingpress_fileupload_obj->bookingpress_process_upload($bookingpress_destination);
            if ($upload_file == false ) {
                $return_data['error'] = 1;
                $return_data['upload_error'] = $upload_file;
                $return_data['msg']   = ! empty($bookingpress_fileupload_obj->error_message) ? $bookingpress_fileupload_obj->error_message : esc_html__('Something went wrong while updating the file', 'bookingpress-location');
            } else {
                $return_data['error']            = 0;
                $return_data['msg']              = '';
                $return_data['upload_url']       = $upload_url . $file_name;
                $return_data['upload_file_name'] = isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '';
            }

            echo wp_json_encode($return_data);
            exit();
        }

        /**
         * Function for add language translation fields
         *
         * @param  mixed $bookingpress_all_language_translation_fields
         * @return void
        */
        function bookingpress_modified_customize_form_language_translate_fields_func($bookingpress_all_language_translation_fields){
            $bookingpress_location_language_translation_fields = array(                
				'bpa_location_title_summay' => array('field_type'=>'text','field_label'=>__('Location Title', 'bookingpress-location'),'save_field_type'=>'booking_form'),               
			);  
			$bookingpress_all_language_translation_fields['customized_form_summary_step_labels'] = array_merge($bookingpress_all_language_translation_fields['customized_form_summary_step_labels'], $bookingpress_location_language_translation_fields);
            $bookingpress_location_step_language_translation_fields = array(                
				'customized_form_location_step' => array(
					'location_title' => array('field_type'=>'text','field_label'=>__('Step location', 'bookingpress-location'),'save_field_type'=>'booking_form'),
                ),   
			);  
			$pos = 1;
			$bookingpress_all_language_translation_fields = array_slice($bookingpress_all_language_translation_fields, 0, $pos)+$bookingpress_location_step_language_translation_fields + array_slice($bookingpress_all_language_translation_fields, $pos);
            return $bookingpress_all_language_translation_fields;
        }

        /**
         * Function for add language translation fields
         *
         * @param  mixed $bookingpress_all_language_translation_fields
         * @return void
        */
        function bookingpress_modified_language_translate_fields_func($bookingpress_all_language_translation_fields){
            $bookingpress_location_language_translation_fields = array(                
                'location' => array(
                    'bookingpress_location_name' => array('field_type'=>'text','field_label'=>__('Location Name', 'bookingpress-location'),'save_field_type'=>'location'),
                    'bookingpress_location_address' => array('field_type'=>'text','field_label'=>__('Address', 'bookingpress-location'),'save_field_type'=>'location'),
                    'bookingpress_location_description' => array('field_type'=>'textarea','field_label'=>__('Description', 'bookingpress-location'),'save_field_type'=>'location'),
                )                    
            );   
            $bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_location_language_translation_fields);

           
            /* Customize section fields My Booking */
            $bookingpress_location_my_booking_language_translation_fields = array(                
				'location_title' => array('field_type'=>'text','field_label'=>__('Location Title', 'bookingpress-location'),'save_field_type'=>'booking_my_booking'),               
			); 
            $bookingpress_all_language_translation_fields['customized_my_booking_field_labels'] = array_merge($bookingpress_all_language_translation_fields['customized_my_booking_field_labels'], $bookingpress_location_my_booking_language_translation_fields);

           
            /* Customize section fields Booking Form */
            $bookingpress_location_language_translation_fields = array(                
				'bpa_location_title_summay' => array('field_type'=>'text','field_label'=>__('Location Title', 'bookingpress-location'),'save_field_type'=>'booking_form'),               
			);  
			$bookingpress_all_language_translation_fields['customized_form_summary_step_labels'] = array_merge($bookingpress_all_language_translation_fields['customized_form_summary_step_labels'], $bookingpress_location_language_translation_fields);
            $bookingpress_location_step_language_translation_fields = array(                
				'customized_form_location_step' => array(
					'location_title' => array('field_type'=>'text','field_label'=>__('Step location', 'bookingpress-location'),'save_field_type'=>'booking_form'),
                )   
			);  
			$bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_location_step_language_translation_fields);
            /* Customize section fields */

            /* New Setting Fields Added */
			$bookingpress_coupon_message_translation_fields = array(                
				'no_appointment_location_selected_for_the_booking' => array('field_type'=>'text','field_label'=>__('No location selected for the booking', 'bookingpress-location'),'save_field_type'=>'message_setting'),
			);            
            $bookingpress_all_language_translation_fields['message_setting'] = array_merge($bookingpress_all_language_translation_fields['message_setting'], $bookingpress_coupon_message_translation_fields);

			return $bookingpress_all_language_translation_fields;
        }

        /**
         * Function for add language translation fields
         *
         * @param  mixed $bookingpress_all_language_translation_fields
         * @return void
        */
        function bookingpress_modified_location_language_translate_fields_func($bookingpress_all_language_translation_fields){
            $bookingpress_location_language_translation_fields = array(                
                'location' => array(
                    'bookingpress_location_name' => array('field_type'=>'text','field_label'=>__('Location Name', 'bookingpress-location'),'save_field_type'=>'location'),
                    'bookingpress_location_address' => array('field_type'=>'text','field_label'=>__('Address', 'bookingpress-location'),'save_field_type'=>'location'),
                    'bookingpress_location_description' => array('field_type'=>'textarea','field_label'=>__('Description', 'bookingpress-location'),'save_field_type'=>'location'),
                )                    
            );   
            $bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_location_language_translation_fields);
            return $bookingpress_all_language_translation_fields;
        }

        /**
         * Function for add language new section
         *
         * @param  mixed $bookingpress_all_language_translation_fields_section
         * @return void
         */
        function bookingpress_modified_language_translate_fields_section_func($bookingpress_all_language_translation_fields_section){
            $bookingpress_location_section_added = array('location' => __('Location', 'bookingpress-location') );
            $bookingpress_all_language_translation_fields_section = array_merge($bookingpress_all_language_translation_fields_section,$bookingpress_location_section_added);

            $bookingpress_customize_location_step_added = array('customized_form_location_step' => __('Location step labels', 'bookingpress-location') );
			$bookingpress_all_language_translation_fields_section = array_merge($bookingpress_all_language_translation_fields_section,$bookingpress_customize_location_step_added);
			
            return $bookingpress_all_language_translation_fields_section;
        }
        

        function bookingpress_export_appointment_data_filter_func( $bookingpress_search_query_where ){

            global $BookingPress;

            $bookingpress_search_data = ! empty( $_REQUEST['search_data'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['search_data'] ) : array(); // phpcs:ignore

            if( !empty( $bookingpress_search_data )){

                if(!empty( $bookingpress_search_data['bookingpress_search_location']) && $bookingpress_search_data['bookingpress_search_location'] != 0 ) {
            
                    $bookingpress_location_id = $bookingpress_search_data['bookingpress_search_location'];
                    $bookingpress_search_query_where .= "AND (bookingpress_location_id = '{$bookingpress_location_id}')";
                    
                }  
            }

            return $bookingpress_search_query_where;
        }

        function bookingpress_export_data_fields_data_func( $appointment, $bookingpress_export_field, $get_appointment ){

            global $BookingPress, $wpdb, $tbl_bookingpress_locations;

            if ( in_array( 'location_name', $bookingpress_export_field ) ) {

                $bookingpress_location_id = !empty($get_appointment['bookingpress_location_id']) ? sanitize_text_field($get_appointment['bookingpress_location_id']) : '';

                if( !empty( $bookingpress_location_id ) ){
                    $bookingpress_location_name = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_location_name FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations is a table name. false alarm

                    $bookingpress_location_name = $bookingpress_location_name['bookingpress_location_name'];

                    $appointment['Location Name'] = ! empty( $bookingpress_location_name ) ? '"' . sanitize_textarea_field( $bookingpress_location_name ) . '"' : '-';
                }
            }

            return $appointment;
        }

        function bpa_export_post_data_outside_func(){ ?>
            bookingpress_search_data.bookingpress_search_location = this.bookingpress_search_location;
        <?php }

        function modify_export_appointment_data_checked_fields_outside_arr($bookingpress_appointment_vue_data_fields){

            $bookingpress_appointment_vue_data_fields_arr = array('location_name');

            $bookingpress_appointment_vue_data_fields = array_merge( $bookingpress_appointment_vue_data_fields, $bookingpress_appointment_vue_data_fields_arr);
            return $bookingpress_appointment_vue_data_fields;
        }

        function export_appointment_field_list_arr( $export_appointment_field_list ){

            $export_appointment_field_list[] = array(
                'name' => 'location_name',
                'text' => __( 'Location name', 'bookingpress-location' ),
            );

            return $export_appointment_field_list;
        }

        function bookingpress_add_setting_msg_outside_func(){ ?>

            <el-row type="flex" class="bpa-gs--tabs-pb__cb-item-row" :gutter="64">
                <el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-left">
                    <h4> <?php esc_html_e('No location selected for the booking', 'bookingpress-location'); ?></h4>    
                </el-col>
                <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16">                
                    <el-form-item prop="no_appointment_location_selected_for_the_booking">
                    <el-input class="bpa-form-control" v-model="message_setting_form.no_appointment_location_selected_for_the_booking"></el-input>        
                    </el-form-item>                        
                </el-col>
            </el-row>
        <?php }

        function bpa_add_extra_tab_outside_func_arr(){ ?>

            if( bpa_get_page == 'bookingpress_location'){
                vm.openNeedHelper('list_location', 'locations', 'Locations');
				vm.bpa_fab_floating_btn = 0;
            }

        <?php }

        function bookingpress_generate_share_url_with_location( $bpa_final_generated_url, $bpa_share_url_form_data ){

            if( !empty( $bpa_share_url_form_data ) ){

                if( !empty( $bpa_share_url_form_data['select_location_id'] ) ){
                    $bpa_final_generated_url = add_query_arg( 'loc_id', $bpa_share_url_form_data['select_location_id'], $bpa_final_generated_url );
                }
                
            }

            return $bpa_final_generated_url;
        }

        function bookingpress_share_url_after_select_staffmember(){
            ?>
            let selected_service = vm.share_url_form.selected_service_id;
            let selected_staffmember = vm.share_url_form.selected_staff_id;

            if( "" != selected_service && "" != selected_staffmember ){
                let location_details = vm.bookingpress_locaiton_lists_for_share_url;

                for( let loc_id in location_details ){
                    let cur_loc = location_details[ loc_id ];
                    let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                    let cur_loc_staff = cur_loc.bookingpress_staffmembers;

                    vm.bookingpress_locaiton_lists_for_share_url[ loc_id ].is_visible = false;

                    if( -1 < cur_loc_services.indexOf( selected_service ) && -1 < cur_loc_staff.indexOf( selected_staffmember ) ){
                        vm.bookingpress_locaiton_lists_for_share_url[ loc_id ].is_visible = 'true';
                    }
                }
            }
            <?php
        }

        function bookingpress_share_url_after_select_service_func(){
            ?>
                if( "undefined" == typeof vm.is_staff_enable || 1 != vm.is_staff_enable ){
                    let selected_service = vm.share_url_form.selected_service_id;
                    let location_details = vm.bookingpress_locaiton_lists_for_share_url;
                    for( let loc_id in location_details ){
                        let cur_loc = location_details[ loc_id ];
                        let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                        vm.bookingpress_locaiton_lists_for_share_url[ loc_id ].is_visible = false;
                        if( -1 < cur_loc_services.indexOf( selected_service )){
                            vm.bookingpress_locaiton_lists_for_share_url[ loc_id ].is_visible = 'true';
                        }
                    }

                } else if( "undefined" != typeof vm.is_staff_enable && 1 == vm.is_staff_enable && 1 == vm.is_staff_login ){
                    
                }
            <?php
        }

        function bookingpress_add_location_field_for_share_url(){
            ?>
                <div class="bpa-form-body-row">
                    <el-row>
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <el-form-item prop="selected_location_id">
                                <template #label>
                                    <span class="bpa-form-label"><?php echo esc_html__('Select Location', 'bookingpress-location'); ?></span>
                                </template>
                                <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Select Location', 'bookingpress-location'); ?>" filterable v-model="share_url_form.select_location_id" @change="bookingpress_generate_share_url" >
                                    <el-option value=""><?php esc_html_e('Select Location', 'bookingpress-location'); ?></el-option>
                                    <el-option v-for="( loc_data,loc_id ) in bookingpress_locaiton_lists_for_share_url" v-if="'true' == loc_data.is_visible" :value="loc_id" :label="loc_data.bookingpress_location_name"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>
            <?php
        }

        function bookingpress_appointment_report_view_add_filter_with_location( $appointments_search_query, $bookingpress_search_data ){
            
            if( !empty( $bookingpress_search_data['location_name'] ) ){
                $bookingpress_location_name = $bookingpress_search_data['location_name'];
                $bookingpress_search_location_id = implode( ',', $bookingpress_location_name );
                $appointments_search_query     .= " AND (bookingpress_location_id IN ({$bookingpress_search_location_id}))";
            }

            return $appointments_search_query;
        }

        function bookingpress_modify_report_search_data_with_locations(){
            ?>
            bookingpress_search_data.location_name = vm.appointment_search_location;
            <?php
        }

        function bookingpress_modify_report_data_fields_with_locations( $bookingpress_report_vue_data_fields ){

            global $wpdb, $tbl_bookingpress_locations;

            $bookingpress_report_vue_data_fields['main_col_width'] = array(
				'xs' => 12,
				'sm' => 12,
				'md' => 12,
				'lg' => 12,
				'xl' => 24
			);
			$bookingpress_report_vue_data_fields['service_col_width'] = array(
				'xs' => 12,
				'sm' => 12,
				'md' => 12,
				'lg' => 12,
				'xl' => 24
			);
			$bookingpress_report_vue_data_fields['staff_col_width'] = array(
				'xs' => 12,
				'sm' => 12,
				'md' => 12,
				'lg' => 12,
				'xl' => 24
			);

            $bpa_location_lists = $wpdb->get_results( "SELECT bookingpress_location_id,bookingpress_location_name FROM {$tbl_bookingpress_locations}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bookingpress_locations = array();
            if( !empty( $bpa_location_lists ) ){
                foreach( $bpa_location_lists as $location_data ){
                    $bookingpress_locations[] = array(
                        'location_id' => $location_data->bookingpress_location_id,
                        'location_name' => $location_data->bookingpress_location_name
                    );
                }
            }
            $bookingpress_report_vue_data_fields['search_locations'] = $bookingpress_locations;
            $bookingpress_report_vue_data_fields['appointment_search_location'] = array();

            return $bookingpress_report_vue_data_fields;
        }

        function bookingpress_add_location_filter_for_report(){
            ?>
            <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="24">
                <el-select class="bpa-form-control" v-model="appointment_search_location" multiple filterable collapse-tags placeholder="<?php esc_html_e( 'Select Locations', 'bookingpress-location'); ?>" :popper-append-to-body="false" popper-class="bpa-el-select--is-with-navbar" @change="change_appointment_report_filter">
                    <el-option v-for="(location_data,index) in search_locations" :value="location_data.location_id" :label="location_data.location_name"></el-option>
                </el-=select>
            </el-col>
            <?php
        }

        function bookingpress_edit_appointment_details_for_location(){
            ?>
                let location_id = response.data.bookingpress_location_id;
                vm.appointment_formdata.selected_location = location_id;

                if( "undefined" != typeof vm.is_staff_enable && 1 == vm.is_staff_enable ){
                    
                    let selected_service = response.data.bookingpress_service_id;
                    let selected_staffmember = response.data.bookingpress_staff_member_id
                    let location_details = vm.bookingpress_location_lists;

                    let first_loc;
                    let i = 0;
                    for( let loc_id in location_details ){
                        let cur_loc = location_details[ loc_id ];
                        let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                        let cur_loc_staff = cur_loc.bookingpress_staffmembers;

                        vm.bookingpress_location_lists[ loc_id ].is_visible = false;

                        if( -1 < cur_loc_services.indexOf( selected_service ) && -1 < cur_loc_staff.indexOf( selected_staffmember ) ){
                            vm.bookingpress_location_lists[ loc_id ].is_visible = 'true';
                            if( i == 0 ){
                                first_loc = loc_id;
                            }
                            i++;
                        }
                    }

                    if( (0 == vm.appointment_formdata.selected_location || "" == vm.appointment_formdata.selected_location) ){

                        if( "" != first_loc ){
                            vm.appointment_formdata.selected_location = first_loc;
                        } else {
                            vm.appointment_formdata.selected_location = "";
                        }
                    }
                } else {
                }
            <?php
        }

        function bookingpress_modify_appointment_booking_fields_with_location_backend( $appointment_booking_fields, $entry_data, $bookingpress_appointment_data ){

            if(!empty($entry_data['bookingpress_location_id'])){
                global $wpdb, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_locations_service_staff_pricing_details;
                $bookingpress_appointment_id = !empty( $entry_data['bookingpress_appointment_booking_id'] ) ? $entry_data['bookingpress_appointment_booking_id'] : 0;

                if( !empty( $bookingpress_appointment_id ) ){
                    $bookingpress_existing_appointment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_appointment_booking_id = %d", $bookingpress_appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name.

                    $existing_location = !empty( $bookingpress_existing_appointment_data['bookingpress_location_id'] ) ? intval( $bookingpress_existing_appointment_data['bookingpress_location_id'] ) : 0;
                    
                    $bookingpress_service_id = !empty($bookingpress_appointment_data['selected_service']) ? intval($bookingpress_appointment_data['selected_service']) : 0;
                    $bookingpress_staffmember_id = !empty($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) ? intval($bookingpress_appointment_data['bookingpress_selected_staff_member_details']['selected_staff_member_id']) : 0;
                    $bookingpress_location_id = !empty($bookingpress_appointment_data['selected_location']) ? intval($bookingpress_appointment_data['selected_location']) : 0;

                    $bookingpress_location_details = array();
                    if( !empty($bookingpress_service_id) && !empty($bookingpress_staffmember_id) && !empty($bookingpress_location_id) ){
                        $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_staffmember_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally. False Positive alarm
                    }else{
                        $bookingpress_location_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_location_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally. False Positive alarm
                    }

                    $bookingpress_service_price = !empty($bookingpress_location_details['bookingpress_service_price']) ? floatval($bookingpress_location_details['bookingpress_service_price']) : 0;
                    $bookingpress_service_capacity = !empty($bookingpress_location_details['bookingpress_service_qty']) ? intval($bookingpress_location_details['bookingpress_service_qty']) : 0;
                    $bookingpress_service_min_capacity = !empty($bookingpress_location_details['bookingpress_service_min_qty']) ? intval($bookingpress_location_details['bookingpress_service_min_qty']) : 0;
                    $bookingpress_staffmember_price = !empty($bookingpress_location_details['bookingpress_staffmember_price']) ? floatval($bookingpress_location_details['bookingpress_staffmember_price']) : 0;
                    $bookingpress_staffmember_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_qty']) : 0;
                    $bookingpress_staffmember_min_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_min_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_min_qty']) : 0;

                    $appointment_booking_fields['bookingpress_location_id'] = $bookingpress_location_id;
                    $appointment_booking_fields['bookingpress_location_service_price'] = $bookingpress_service_price;
                    $appointment_booking_fields['bookingpress_location_service_capacity'] = $bookingpress_service_capacity;
                    $appointment_booking_fields['bookingpress_location_staff_price'] = $bookingpress_staffmember_price;
                    $appointment_booking_fields['bookingpress_location_staff_capacity'] = $bookingpress_staffmember_capacity;

                    if( $existing_location != $bookingpress_location_id ){
                        $appointment_booking_fields['bookingpress_is_reschedule'] = 1;
                    }

                } else {
                    $appointment_booking_fields['bookingpress_location_id'] = $entry_data['bookingpress_location_id'];
                    $appointment_booking_fields['bookingpress_location_service_price'] = $entry_data['bookingpress_location_service_price'];
                    $appointment_booking_fields['bookingpress_location_service_capacity'] = $entry_data['bookingpress_location_service_capacity'];
                    $appointment_booking_fields['bookingpress_location_staff_price'] = $entry_data['bookingpress_location_staff_price'];
                    $appointment_booking_fields['bookingpress_location_staff_capacity'] = $entry_data['bookingpress_location_staff_capacity'];
                }
            }
            return $appointment_booking_fields;
        }

        function bookingpress_modify_backend_add_appointment_entry_data_location( $bookingpress_entry_details, $posted_data ){

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
                $bookingpress_service_min_capacity = !empty($bookingpress_location_details['bookingpress_service_min_qty']) ? intval($bookingpress_location_details['bookingpress_service_min_qty']) : 1;
                $bookingpress_staffmember_price = !empty($bookingpress_location_details['bookingpress_staffmember_price']) ? floatval($bookingpress_location_details['bookingpress_staffmember_price']) : 0;
                $bookingpress_staffmember_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_qty']) : 0;
                $bookingpress_staffmember_min_capacity = !empty($bookingpress_location_details['bookingpress_staffmember_min_qty']) ? intval($bookingpress_location_details['bookingpress_staffmember_min_qty']) : 0;

                $bookingpress_entry_details['bookingpress_location_id'] = $bookingpress_location_id;
                $bookingpress_entry_details['bookingpress_location_service_price'] = $bookingpress_service_price;
                $bookingpress_entry_details['bookingpress_location_service_capacity'] = $bookingpress_service_capacity;
                $bookingpress_entry_details['bookingpress_location_staff_price'] = $bookingpress_staffmember_price;
                $bookingpress_entry_details['bookingpress_location_staff_capacity'] = $bookingpress_staffmember_capacity;
            }

            return $bookingpress_entry_details;
        }

        function bookingpress_set_visible_locations_for_backend(){
            ?>

                vm.appointment_formdata.selected_location = "";
                if( "undefined" == typeof vm.is_staff_enable || 1 != vm.is_staff_enable ){
                    let selected_service = vm.appointment_formdata.appointment_selected_service;
                    let location_details = vm.bookingpress_location_lists;
                    for( let loc_id in location_details ){
                        let cur_loc = location_details[ loc_id ];
                        let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                        
                        vm.bookingpress_location_lists[ loc_id ].is_visible = false;
                        if( -1 < cur_loc_services.indexOf( selected_service ) ){
                            vm.bookingpress_location_lists[ loc_id ].is_visible = 'true';
                        }
                    }
                } else if( "undefined" != typeof vm.is_staff_enable && 1 == vm.is_staff_enable ){
                    let selected_staffmember = vm.appointment_formdata.selected_staffmember;
                    let location_details = vm.bookingpress_location_lists;
                    for( let loc_id in location_details ){
                        let cur_loc = location_details[ loc_id ];
                        let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                        let cur_loc_staff = cur_loc.bookingpress_staffmembers;

                        vm.bookingpress_location_lists[ loc_id ].is_visible = false;

                        if( -1 < cur_loc_services.indexOf( selected_service ) && -1 < cur_loc_staff.indexOf( selected_staffmember ) ){
                            vm.bookingpress_location_lists[ loc_id ].is_visible = 'true';
                        }
                    }
                }

            <?php
        }

        function bookingpress_set_visible_locations_with_staff_for_backend(){
            ?>
            if( "undefined" != typeof vm.is_staff_enable && 1 == vm.is_staff_enable ){

                vm.appointment_formdata.selected_location = "";
                let selected_service = vm.appointment_formdata.appointment_selected_service;
                let location_details = vm.bookingpress_location_lists;
                for( let loc_id in location_details ){
                    let cur_loc = location_details[ loc_id ];
                    let cur_loc_services = cur_loc.bookingpress_location_service_ids;
                    let cur_loc_staff = cur_loc.bookingpress_staffmembers;

                    vm.bookingpress_location_lists[ loc_id ].is_visible = false;

                    if( -1 < cur_loc_services.indexOf( selected_service ) && -1 < cur_loc_staff.indexOf( selected_staffmember ) ){
                        vm.bookingpress_location_lists[ loc_id ].is_visible = 'true';
                    }
                }
            }
            <?php
        }

        function bookingpress_add_location_for_appointment_data_fields( $bookingpress_appointment_vue_data_fields ){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;

            $bookingpress_appointment_vue_data_fields['bookingpress_appointment_locations'] = array();

            $bookingpress_locations_list = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_id ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
            $bpa_location_arr = array();
            if( !empty( $bookingpress_locations_list ) ){
                foreach( $bookingpress_locations_list as $location_data ){
                    $location_id = intval( $location_data['bookingpress_location_id'] );

                    $service_location_details =  $wpdb->get_results( $wpdb->prepare("SELECT bookingpress_service_qty, bookingpress_service_min_qty, bookingpress_service_id FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d", $location_id), ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                     

                    if( empty( $service_location_details ) ){
                        continue;
                    }

                    $location_service_ids = array();
                    $location_service_qty = array();

                    foreach( $service_location_details as $location_details ){
                        $location_service_ids[] = $location_details['bookingpress_service_id'];

                        $location_service_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_qty'];
                        $location_service_min_qty[ $location_details['bookingpress_service_id'] ] = $location_details['bookingpress_service_min_qty'];
                    }

                    $location_data['bookingpress_location_service_ids'] = $location_service_ids;
                    $location_data['bookingpress_location_service_qty'] = $location_service_qty;
                    $location_data['bookingpress_location_service_min_qty'] = $location_service_min_qty;
                    $location_data['is_visible'] = false;
                    //$location_data['is_visible_with_flag'] = true;

                    $bpa_location_arr[ $location_id ] = $location_data;
                }
            }
            
            if( !empty( $bookingpress_appointment_vue_data_fields['is_staff_enable'] ) && 1 == $bookingpress_appointment_vue_data_fields['is_staff_enable'] ){
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
                        $bpa_location_arr[ $loc_key ]['is_visible'] = 'true';
                    } else {
                        $bpa_location_arr[ $loc_key ]['is_visible'] = false;
                        $hidden_locations++;
                    }
                }

            }

            $bookingpress_appointment_vue_data_fields['bookingpress_location_lists'] = $bpa_location_arr;
            $bookingpress_appointment_vue_data_fields['bookingpress_locaiton_lists_for_share_url'] = $bpa_location_arr;
            $bookingpress_appointment_vue_data_fields['share_url_form']['selected_location_id'] = '';

            return $bookingpress_appointment_vue_data_fields;
        }

        function bookingpress_add_location_field_for_add_appointment(){
            ?>
                <el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                    <el-form-item prop="selected_location">
                        <template #label>
                            <span class="bpa-form-label"><?php esc_html_e('Select Location', 'bookingpress-location'); ?></span>
                        </template>
                        <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Select Location', 'bookingpress-location'); ?>" v-model="appointment_formdata.selected_location" name="selected_location" @change="app.$forceUpdate()">
                        
                            <el-option v-for="( loc_data,loc_id ) in bookingpress_location_lists" v-if="'true' == loc_data.is_visible" :value="loc_id" :label="loc_data.bookingpress_location_name"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            <?php
        }

        function bookingpress_remove_location_file_func(){
            global $wpdb;
            $response = array();

            $bpa_check_authorization = $this->bpa_check_authentication( 'remove_location_avatar', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            if (! empty($_POST) && ! empty($_POST['upload_file_url']) ) { // phpcs:ignore WordPress.Security.NonceVerification
                $bookingpress_uploaded_avatar_url = esc_url_raw($_POST['upload_file_url']); // phpcs:ignore
                $bookingpress_file_name_arr       = explode('/', $bookingpress_uploaded_avatar_url);
                $bookingpress_file_name           = $bookingpress_file_name_arr[ count($bookingpress_file_name_arr) - 1 ];
                if( file_exists( BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name ) ){
                    @unlink(BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name);
                }
            }
            die;
        }


        function bookingpress_appointment_view_add_filter_func( $bookingpress_search_query_where, $bookingpress_search_data){

            if(!empty( $bookingpress_search_data['bookingpress_search_location']) && $bookingpress_search_data['bookingpress_search_location'] != 0 ) {
                
                $bookingpress_location_id = $bookingpress_search_data['bookingpress_search_location'];
                $bookingpress_search_query_where .= "AND (bookingpress_location_id = '{$bookingpress_location_id}')";
                
            }

            return $bookingpress_search_query_where;
        }

        function bookingpress_appointment_reset_filter_func(){ ?>

            vm.bookingpress_search_location = '';
            vm.bookingpress_selected_location_filter = 0;

        <?php }

        function bookingpress_appointment_add_post_data_func(){ ?> 

            bookingpress_search_data.bookingpress_search_location = this.bookingpress_search_location;
        <?php }

        function bookingpress_modify_placeholder_data_outside_zapier_func( $bookingpress_dynamic_setting_data_fields ){

            global $bookingpress_global_options;

            $bookingpress_global_data = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_location_placeholders = json_decode(str_replace('%','',$bookingpress_global_data['location_placeholders']),true);      
            $bookingpress_dynamic_setting_data_fields['bookingpress_zapier_location_placeholder']   = $bookingpress_location_placeholders;
            return $bookingpress_dynamic_setting_data_fields;
        }

        function bookingpress_appointment_data_replace_outside_func( $bookingpress_data, $bookingpress_appointment_data, $bookingpress_total_field){

            global $wpdb, $tbl_bookingpress_locations;

            $location_id = !empty( $bookingpress_appointment_data['bookingpress_location_id']) ? intval( $bookingpress_appointment_data['bookingpress_location_id']) : '';
            if( !empty( $location_id)){

                $location_details = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name, bookingpress_location_address, bookingpress_location_phone_number, bookingpress_location_description FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $location_id), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_details['bookingpress_location_name'] ? esc_html( $location_details['bookingpress_location_name']) : '-';
                $location_address = $location_details['bookingpress_location_address'] ? esc_html( $location_details['bookingpress_location_address']) : '-';
                $location_phone_number = $location_details['bookingpress_location_phone_number'] ? esc_html( $location_details['bookingpress_location_phone_number']) : '-';
                $location_description = $location_details['bookingpress_location_description'] ? esc_html( $location_details['bookingpress_location_description']) : '-';

            }

            if(in_array('location_name',$bookingpress_total_field)) {
                $bookingpress_location_name = '-';
                $bookingpress_location_name = !empty($location_name) ? $location_name : '';
                $bookingpress_data['location_name'] = $bookingpress_location_name;
            }
            if(in_array('location_phone',$bookingpress_total_field)) {
                $bookingpress_location_phone_number = '-';
                $bookingpress_location_phone_number = !empty($location_phone_number) ? $location_phone_number : '';
                $bookingpress_data['location_phone'] = $bookingpress_location_phone_number;
            }
            if(in_array('location_address',$bookingpress_total_field)) {
                $bookingpress_location_address = '-';
                $bookingpress_location_address = !empty($location_address) ? $location_address : '';
                $bookingpress_data['location_address'] = $bookingpress_location_address;
            }
            if(in_array('location_description',$bookingpress_total_field)) {
                $bookingpress_location_description = '-';
                $bookingpress_location_description = !empty($location_description) ? $location_description : '';
                $bookingpress_data['location_description'] = $bookingpress_location_description;
            }

            return $bookingpress_data;
        }

        function bookingpress_modify_appointment_default_field_zapier_func( $bookingpress_appointment_default_field, $id, $type ){

            $bookingpress_location_field= array('location_name','location_phone','location_description','location_address');
            $bookingpress_appointment_default_field = array_merge($bookingpress_appointment_default_field,$bookingpress_location_field);

            return $bookingpress_appointment_default_field;
        }

        function bookingpress_change_label_value_for_invoice_func( $bookingpress_invoice_html_view, $log_detail ){
            global $wpdb, $tbl_bookingpress_locations, $BookingPressPro;

            $location_id = !empty( $log_detail['bookingpress_location_id']) ? intval( $log_detail['bookingpress_location_id']) : '';
            if( !empty( $location_id)){

                $location_details = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name, bookingpress_location_address, bookingpress_location_phone_number, bookingpress_location_description FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $location_id), ARRAY_A ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_details['bookingpress_location_name'] ? esc_html( $location_details['bookingpress_location_name']) : '-';
                $location_address = $location_details['bookingpress_location_address'] ? esc_html( $location_details['bookingpress_location_address']) : '-';
                $location_phone_number = $location_details['bookingpress_location_phone_number'] ? esc_html( $location_details['bookingpress_location_phone_number']) : '-';
                $location_description = $location_details['bookingpress_location_description'] ? esc_html( $location_details['bookingpress_location_description']) : '-';
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_name'])) {
                    $location_name = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_name'],'location','bookingpress_location_name',$location_id));  
                }
                $bookingpress_invoice_html_view  = str_replace( '{location_name}', $location_name, $bookingpress_invoice_html_view );
                $bookingpress_invoice_html_view  = str_replace( '{location_phone}', $location_phone_number, $bookingpress_invoice_html_view );
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_address'])) {
                    $location_address = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_address'],'location','bookingpress_location_address',$location_id));  
                }
                $bookingpress_invoice_html_view  = str_replace( '{location_address}', $location_address, $bookingpress_invoice_html_view );
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_description'])) {
                    $location_description = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_description'],'location','bookingpress_location_description',$location_id));  
                }
                $bookingpress_invoice_html_view  = str_replace( '{location_description}', $location_description, $bookingpress_invoice_html_view );
                
            }

            return $bookingpress_invoice_html_view;
        }

        function bookingpress_modify_email_content_filter_func( $template_content, $bookingpress_appointment_data ){

            global $wpdb, $tbl_bookingpress_locations, $BookingPressPro;
            $location_id = !empty( $bookingpress_appointment_data['bookingpress_location_id'] ) ? intval($bookingpress_appointment_data['bookingpress_location_id']) : '';

            if( !empty( $location_id )){

                $location_details = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name, bookingpress_location_address, bookingpress_location_phone_number, bookingpress_location_description FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $location_id), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_details['bookingpress_location_name'] ? esc_html( $location_details['bookingpress_location_name']) : '';
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_name'])) {
                    $location_name = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_name'],'location','bookingpress_location_name',$location_id));  
                }
                
                $location_address = $location_details['bookingpress_location_address'] ? esc_html( $location_details['bookingpress_location_address']) : '';
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_address'])) {
                    $location_address = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_address'],'location','bookingpress_location_address',$location_id));  
                }
                
                $location_phone_number = $location_details['bookingpress_location_phone_number'] ? esc_html( $location_details['bookingpress_location_phone_number']) : '';
                $location_description = $location_details['bookingpress_location_description'] ? esc_html( $location_details['bookingpress_location_description']) : '';
                if(method_exists( $BookingPressPro, 'bookingpress_pro_front_language_translation_func') && isset($location_details['bookingpress_location_description'])) {
                    $location_description = esc_html($BookingPressPro->bookingpress_pro_front_language_translation_func($location_details['bookingpress_location_description'],'location','bookingpress_location_description',$location_id));  
                }
                $template_content  = str_replace( '%location_name%', $location_name, $template_content );
                $template_content  = str_replace( '%location_phone%', $location_phone_number, $template_content );
                $template_content  = str_replace( '%location_address%', $location_address, $template_content );
                $template_content  = str_replace( '%location_description%', $location_description, $template_content );
                
                /* $location_name = $location_name['bookingpress_location_name'];
                $location_description = $location_details */
            }

            return $template_content;
        }

        function add_bookingpress_location_appointment_details_outside_func(){ ?>
            <div class="bpa-bd__item" v-if="(scope.row.bookingpress_location_full_name) && scope.row.bookingpress_location_full_name != ''">
                <div class="bpa-bd__item-head">
                    <span><?php esc_html_e('Location', 'bookingpress-location'); ?></span>
                </div>
                <div class="bpa-bd__item-body bpa-bd__item-location_nm">
                    <span class="material-icons-round">pin_drop</span><h4>{{ scope.row.bookingpress_location_full_name }}</h4>
                </div>
            </div>
        <?php }

        function bookingpress_waiting_list_outside_add_column_func(){ ?>
            <el-table-column v-cloak prop="bookingpress_location_name" min-width="30" v-if="bookingpress_total_locations != 0">
                <template slot-scope="scope">
                    <el-tooltip :content="scope.row.bookingpress_location_full_name" placement="top" v-if="(scope.row.bookingpress_location_full_name) && scope.row.bookingpress_location_full_name != ''">
                        <span class="bpa-mai-item__location-thumb">{{scope.row.bookingpress_location_name}}</span>
                    </el-tooltip>
                </template>
            </el-table-column>
        <?php }

        function add_bookingpress_appointment_details_outside_func(){ ?>
            <div class="bpa-bd__item"  v-if="(scope.row.bookingpress_location_full_name) && scope.row.bookingpress_location_full_name != ''" >
                <div class="bpa-bd__item-head">
                    <span><?php esc_html_e('Location', 'bookingpress-location'); ?></span>
                </div>
                <div class="bpa-bd__item-body bpa-bd__item-location_nm">
                    <span class="material-icons-round">pin_drop</span><h4>{{ scope.row.bookingpress_location_full_name }}</h4>
                </div>
            </div>
        <?php }

        function bookingpress_appointment_add_view_field_func( $appointment,$get_appointment ){

            global $wpdb, $tbl_bookingpress_locations;

            $location_id = !empty($get_appointment['bookingpress_location_id']) ? $get_appointment['bookingpress_location_id'] : '';

            if( !empty( $location_id )){

                $location_name = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $location_id), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_name['bookingpress_location_name'];
                $bookingpress_location_name = substr( $location_name,0,2);

                $appointment['bookingpress_location_full_name'] = $location_name;
                $appointment['bookingpress_location_name'] = $bookingpress_location_name;
            }else {
                $location_name = $wpdb->get_row("SELECT bookingpress_location_name FROM `{$tbl_bookingpress_locations}` ORDER BY bookingpress_location_id ASC LIMIT 1",ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_name['bookingpress_location_name'];
                $bookingpress_location_name = substr( $location_name,0,2);

                $appointment['bookingpress_location_full_name'] = $location_name;
                $appointment['bookingpress_location_name'] = $bookingpress_location_name;
            }

            return $appointment;
        }

        function bookingpress_add_column_outsite_func(){ ?>
            <el-table-column prop="bookingpress_location_name" min-width="30" v-if="bookingpress_total_locations != 0">
                <template slot-scope="scope">
                    <el-tooltip :content="scope.row.bookingpress_location_full_name"  placement="top" v-if="(scope.row.bookingpress_location_full_name) && scope.row.bookingpress_location_full_name != ''">
                        <span class="bpa-mai-item__location-thumb">{{scope.row.bookingpress_location_name}}</span>
                    </el-tooltip>
                </template>
            </el-table-column>
        <?php }
	
	function bookingpress_calendar_appointment_xhr_response_func() {
            ?>
            if( "" == vm.search_data['selected_location'] && vm.calendar_events_data[0] != undefined && "" != vm.calendar_events_data[0].bookingpress_location_id) {
                vm.search_data.selected_location = vm.calendar_events_data[0].bookingpress_location_id;
            }
            else if("" == vm.search_data['selected_location']){
                for (f in vm.bookingpress_location_filter) {
                    vm.search_data.selected_location = f;
                    break;
                }
            }
            vm.bpa_display_calendar_loader = 0;
            <?php
        }
        function bookingpress_modify_calendar_all_appointment_details_func($calendar_bookings_data, $posted_data)
        {
            if(isset($calendar_bookings_data[0]) && empty($posted_data['search_data']['selected_location'])) {
                $first_appointment_location_id = isset($calendar_bookings_data[0]['bookingpress_location_id']) ? $calendar_bookings_data[0]['bookingpress_location_id'] : '';
                $bookingpress_appointment_location_data = array();
                foreach ($calendar_bookings_data as $subarray){  
                    if (isset($subarray['bookingpress_location_id']) && $subarray['bookingpress_location_id'] == $first_appointment_location_id) {
                        $bookingpress_appointment_location_data[]= $subarray;       
                    }
                } 
                if(!empty($bookingpress_appointment_location_data)) {
                   $calendar_bookings_data = $bookingpress_appointment_location_data;
                }
            }
            if(!empty($posted_data) && !empty($posted_data['search_data']['selected_location'])){
                $this->bookingpress_set_location_filter_cookie($posted_data['search_data']['selected_location']);
            }
            else if(!empty($first_appointment_location_id)) {
                $this->bookingpress_set_location_filter_cookie($first_appointment_location_id);
            }
            return $calendar_bookings_data;
        }
        function bookingpress_set_location_filter_cookie($location_filter_val){
            if(!empty($location_filter_val)) {
                setcookie("bookingpress_calender_location_filter", $location_filter_val, time() + (86400 * 30), "/");
            }
        }

        function bookingpress_modify_calendar_location_appointment_details_func($calendar_bookings_data, $appointment_details){
            if(!empty($appointment_details) && !empty($appointment_details['bookingpress_location_id'])){
                $bookingpress_location_id = $appointment_details['bookingpress_location_id'];
                $bookingpress_appointment_id = $appointment_details['bookingpress_appointment_booking_id'];
                foreach($calendar_bookings_data as $calendar_booking_key => $calendar_booking_val){
					if($bookingpress_appointment_id == $calendar_booking_val['appointment_id']){
						$calendar_bookings_data[$calendar_booking_key]['bookingpress_location_id'] = $bookingpress_location_id;
					}
				}
            }
            return $calendar_bookings_data;
        }
        function bookingpress_modify_popover_appointment_location_data_query_func($appointment_query_dynamic_arr, $posted_data){
            $location_filter_val = (isset($posted_data['search_data']) && isset($posted_data['search_data']['selected_location'])) ? $posted_data['search_data']['selected_location'] : '';
            if(!empty($location_filter_val)) {
                $bookingpress_search_location_id  = $location_filter_val;
                $first_location_id = isset($posted_data['search_data']['first_location']) ? $posted_data['search_data']['first_location'] : '';
                if(!empty($first_location_id)) {
                    if($bookingpress_search_location_id==$first_location_id){
                        $bookingpress_search_location_id= implode(',', array($bookingpress_search_location_id,0));
                    }
                }
                $where_query = " AND (appointment.bookingpress_location_id IN ({$bookingpress_search_location_id}))";
                $appointment_query_dynamic_arr['where_query'] .= $where_query;
            }
			return $appointment_query_dynamic_arr;
		}

        function bookingpress_calendar_add_view_filter_location_func($bookingpress_search_query, $bookingpress_search_data) {
            if ( ! empty( $bookingpress_search_data['selected_location'] ) ) {
                $bookingpress_search_name = $bookingpress_search_data['selected_location'];
				$bookingpress_search_location_id = $bookingpress_search_name;
                $first_location_id = isset($bookingpress_search_data['first_location']) ? $bookingpress_search_data['first_location'] : '';
                if(!empty($first_location_id)) {
                    if($bookingpress_search_location_id==$first_location_id){
                        $bookingpress_search_location_id= implode(',', array($bookingpress_search_location_id,0));
                    }
                }
				$bookingpress_search_query  .= " AND (bookingpress_location_id IN ({$bookingpress_search_location_id}))";
			};
            return $bookingpress_search_query;
        }   

        function bookingpress_add_dynamic_vue_methods_for_calendar__location_func() {
            ?>
            select_calendar_location_filter(selected_val){
                const vm = this;
                vm.bpa_display_calendar_loader = 1;
                /*
                if(Array.isArray(selected_val) == false){
                    //vm.search_data.selected_location = selected_val;
                }*/
                vm.loadCalendar(vm.activeView);
            },
            <?php
        }
        function bookingpress_modify_calendar_location_data_fields_func($bookingpress_calendar_vue_data_fields){
            global $wpdb, $tbl_bookingpress_locations, $BookingPressPro, $tbl_bookingpress_appointment_bookings;
            $location_details = $wpdb->get_results( "SELECT bookingpress_location_id, bookingpress_location_name FROM `{$tbl_bookingpress_locations}` ORDER BY bookingpress_location_name ASC" ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
            $bpa_locations = array();
            $total_locations = 0;
            $bookingpress_search_selected_location = '';
            $first_location_id='';
            if( !empty( $location_details ) ){
                foreach( $location_details as $location_data ){
                    $bpa_locations[ $location_data->bookingpress_location_id ] = $location_data->bookingpress_location_name;
                    $total_locations++;
                }
                $first_location_id = min(array_keys($bpa_locations));
            }

            $bookingpress_calender_location_filter = !empty( $_COOKIE['bookingpress_calender_location_filter'] ) ? sanitize_text_field($_COOKIE['bookingpress_calender_location_filter']) : '';
            if(!empty($bookingpress_calender_location_filter)) {
                if(!array_key_exists($bookingpress_calender_location_filter, $bpa_locations)){
                    $bookingpress_calender_location_filter = '';
                }
            }
            $bookingpress_calendar_vue_data_fields['bookingpress_is_staffmember_login'] = $BookingPressPro->bookingpress_check_user_role( 'bookingpress-staffmember' );
            $bookingpress_calendar_vue_data_fields['bookingpress_location_filter'] = $bpa_locations;
            $bookingpress_calendar_vue_data_fields['bookingpress_selected_location_filter'] = 0;
            $bookingpress_calendar_vue_data_fields['bookingpress_total_locations'] = $total_locations;
            $bookingpress_calendar_vue_data_fields['bookingpress_calenar_filter_class'] = '__bpa-fsc-is-location bpa-fsc__addon-filter-belt';
            $bookingpress_calendar_vue_data_fields['bookingpress_selected_location']  = '';
            $bookingpress_calendar_vue_data_fields['search_data']['selected_location'] = $bookingpress_calender_location_filter;
            $bookingpress_calendar_vue_data_fields['search_data']['first_location'] = $first_location_id;
            return $bookingpress_calendar_vue_data_fields;
        }


        function bookingpress_modify_dashborard_data_fields_func( $bookingpress_dashboard_vue_data_fields ){
            global $wpdb, $tbl_bookingpress_locations;

            $location_details = $wpdb->get_results( "SELECT bookingpress_location_id, bookingpress_location_name FROM `{$tbl_bookingpress_locations}` ORDER BY bookingpress_location_name ASC" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bpa_locations = array();
            $bpa_locations[0] = __( 'All Locations', 'bookingpress-location');
            $total_locations = 0;
            if( !empty( $location_details ) ){
                foreach( $location_details as $location_data ){
                    $bpa_locations[ $location_data->bookingpress_location_id ] = $location_data->bookingpress_location_name;
                    $total_locations++;
                }
            }

            $bookingpress_dashboard_vue_data_fields['bookingpress_location_filter'] = $bpa_locations;
            $bookingpress_dashboard_vue_data_fields['bookingpress_selected_location_filter'] = 0;
            $bookingpress_dashboard_vue_data_fields['bookingpress_total_locations'] = $total_locations;
            $bookingpress_dashboard_vue_data_fields['bookingpress_search_location'] = '';

            return $bookingpress_dashboard_vue_data_fields;
        }

        function bookingpress_calendar_filter_for_location_func() {
            ?>
                <div class="bpa-afb__location-selection" v-if="current_screen_size != 'mobile'" :class="bookingpress_is_staffmember_login == true ? 'bpa_staff_member_logged_in' : ''">
                    <h4><?php esc_html_e( 'Select Location', 'bookingpress-location' ); ?></h4>
                    <div class="bpa-ls__body">
                        <div class="bpa-lsb__icon">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.53162 2.93677C10.7165 1.66727 13.402 1.68946 15.5664 2.99489C17.7095 4.32691 19.012 6.70418 18.9999 9.26144C18.95 11.8019 17.5533 14.19 15.8075 16.0361C14.7998 17.1064 13.6726 18.0528 12.4488 18.856C12.3228 18.9289 12.1847 18.9777 12.0415 19C11.9036 18.9941 11.7693 18.9534 11.6508 18.8814C9.78243 17.6746 8.14333 16.134 6.81233 14.334C5.69859 12.8314 5.06583 11.016 5 9.13442L5.00499 8.86069C5.09592 6.40464 6.4248 4.16093 8.53162 2.93677ZM12.9073 7.03477C12.0191 6.65723 10.995 6.86235 10.3133 7.55435C9.63159 8.24635 9.42664 9.28872 9.79416 10.1948C10.1617 11.1008 11.0292 11.6918 11.9916 11.6918C12.6221 11.6964 13.2282 11.4438 13.6748 10.9905C14.1214 10.5371 14.3715 9.92064 14.3693 9.27838C14.3726 8.29804 13.7955 7.41231 12.9073 7.03477Z" />
                                <path opacity="0.4" d="M12 22C14.7614 22 17 21.5523 17 21C17 20.4477 14.7614 20 12 20C9.23858 20 7 20.4477 7 21C7 21.5523 9.23858 22 12 22Z" fill="white"/>
                            </svg>
                        </div>
                        <div class="bpa-lsb__dropdown">
                            <el-select v-model="search_data.selected_location" class="bpa-form-control" @change="select_calendar_location_filter($event)" Placeholder="<?php esc_html_e( 'All Location', 'bookingpress-location' ); ?>" filterable collapse-tags popper-class="bpa-el-select--is-with-modal" v-if="current_screen_size != 'mobile'">
                               <el-option v-for="( location_name, location_id ) of bookingpress_location_filter" :value="location_id" :label="location_name">{{location_name}}</el-option>
                            </el-select>                                              
                        </div>
                    </div>
                </div>
                <el-select class="bpa-form-control" v-model="search_data.selected_location" @change="select_calendar_location_filter($event)" filterable collapse-tags placeholder="<?php esc_html_e('Location', 'bookingpress-location'); ?>" :popper-append-to-body="false" popper-class="bpa-el-select--is-with-navbar" v-else>
                    <el-option v-for="( location_name, location_id ) of bookingpress_location_filter" :value="location_id" :label="location_name">{{location_name}}</el-option>
                </el-select> 
            <?php 
        }
	
        function bookingpress_add_zoom_outside_notification_placeholders(){ ?>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Location', 'bookingpress-location' ); ?>" @change="bookingpress_zoom_insert_placeholder($event)" >
                        <el-option v-for="item in bookingpress_outside_location_placeholder" :key="item.value" :label="item.name" :value="item.value">
                        </el-option>
                    </el-select>                                                               
                </el-form-item>    
            </el-col>
        <?php }

        function bookingpress_add_ocalendar_outside_notification_placeholders_func(){ ?>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Location', 'bookingpress-location' ); ?>" @change="bookingpress_ocalendar_insert_placeholder($event)" >
                        <el-option v-for="item in bookingpress_outside_location_placeholder" :key="item.value" :label="item.name" :value="item.value"></el-option>
                    </el-select>                                    
                </el-form-item>    
            </el-col>	
        <?php }

        function bookingpress_zapier_add_placeholder_outside_func(){ ?>
            vm.bookingpress_zapier_location_placeholder.forEach(function(item,index,arr){
                if(item.value == field_name ){
                    vm.delete_appointment_field(item.value);
                    var data = [];                        
                    data.name = item.name;
                    data.value = item.value;
                    vm.zapier_setting_form.bookingpress_zapier_appointment_trigger_field.push(data);
                }
            });  
        <?php }

        function bookingpress_add_zapier_outside_notification_placeholders_func(){ ?>

            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Location', 'bookingpress-location' ); ?>"	@change="bookingpress_appointment_insert_field($event)" popper-class="bpa-el-select--is-with-navbar">
                    <el-option v-for="item in bookingpress_zapier_location_placeholder" :key="item.value" :label="item.name" :value="item.value">
                    </el-option>
                </el-select>
            </el-col>
        <?php }

        function bookingpress_add_outside_notification_placeholders_func(){?>
            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                <el-form-item>
                    <el-select class="bpa-form-control" placeholder="<?php esc_html_e( 'Location', 'bookingpress-location' ); ?>" @change="bookingpress_gclaendar_insert_placeholder($event)" >
                        <el-option v-for="item in bookingpress_outside_location_placeholder" :key="item.value" :label="item.name" :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>    
            </el-col>
        <?php }

        function bookingpress_add_location_field_notification_data_fields_func( $bookingpress_notification_vue_methods_data ) {

            global $bookingpress_global_options,$BookingPress;

            $bookingpress_services_details 		   = $BookingPress->get_bookingpress_service_data_group_with_category();
			$bookingpress_options                  = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_location_placeholders = json_decode( $bookingpress_options['location_placeholders'] );

            $bookingpress_notification_vue_methods_data['bookingpress_location_placeholders'] = $bookingpress_location_placeholders;

            return $bookingpress_notification_vue_methods_data;
        }

        function bookingpress_location_notification_placeholder(){ ?>
            <div class="bpa-gs__cb--item-tags-body">
                <div>
                    <span class="bpa-tags--item-sub-heading"><?php esc_html_e('Location', 'bookingpress-location'); ?></span>
                    <span class="bpa-tags--item-body" v-for="item in bookingpress_location_placeholders" @click="bookingpress_insert_placeholder(item.value); bookingpress_insert_sms_placeholder(item.value); bookingpress_insert_whatsapp_placeholder(item.value);">{{ item.name }}</span>
                </div>
            </div>
        <?php }

        function bookingpress_add_global_option_data_func( $global_data ){

            $location_data = array(
				'location_placeholders' => wp_json_encode(
					array(
						array(
							'value' => '%location_name%',
							'name'  => '%location_name%',
						),
						array(
							'value' => '%location_phone%',
							'name'  => '%location_phone%',
						),
						array(
							'value' => '%location_address%',
							'name'  => '%location_address%',
						),
						array(
							'value' => '%location_description%',
							'name'  => '%location_description%',
						),
					)
				),
            );
            $global_data = array_merge( $global_data, $location_data );
            return $global_data;

        }

        function bookingpress_add_location_filters_for_appointment( $bookingpress_appointment_vue_data_fields ){
            global $wpdb, $tbl_bookingpress_locations;

            $location_details = $wpdb->get_results( "SELECT bookingpress_location_id, bookingpress_location_name FROM `{$tbl_bookingpress_locations}` ORDER BY bookingpress_location_name ASC" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bpa_locations = array();
            $bpa_locations[0] = __( 'All Locations', 'bookingpress-location');
            $total_locations = 0;
            if( !empty( $location_details ) ){
                foreach( $location_details as $location_data ){
                    $bpa_locations[ $location_data->bookingpress_location_id ] = $location_data->bookingpress_location_name;
                    $total_locations++;
                }
            }

            $bookingpress_appointment_vue_data_fields['bookingpress_location_filter'] = $bpa_locations;
            $bookingpress_appointment_vue_data_fields['bookingpress_selected_location_filter'] = 0;
            $bookingpress_appointment_vue_data_fields['bookingpress_total_locations'] = $total_locations;
            $bookingpress_appointment_vue_data_fields['bookingpress_search_location'] = '';
            $bookingpress_appointment_vue_data_fields['rules']['selected_location'] = array(
                array(
                    'required' => true,
                    'message'  => __('Please select location', 'bookingpress-location'),
                    'trigger'  => 'change',
                ),
            );
            $bookingpress_appointment_vue_data_fields['appointment_formdata']['selected_location'] = '';
            
            return $bookingpress_appointment_vue_data_fields;
        }

        function bookingpress_add_location_form_customize_css($bookingpress_customize_css_content,$bookingpress_custom_data_arr){

            $content_color                     = $bookingpress_custom_data_arr['booking_form']['content_color'];
            $border_color                      = $bookingpress_custom_data_arr['booking_form']['border_color'];
            $shortcode_footer_background_color = $bookingpress_custom_data_arr['booking_form']['footer_background_color'];
            $sub_title_color                   = $bookingpress_custom_data_arr['booking_form']['sub_title_color'];
            $title_label_color                 = $bookingpress_custom_data_arr['booking_form']['label_title_color'];
            $title_font_family                 = $bookingpress_custom_data_arr['booking_form']['title_font_family'];
			$title_font_family          	   = $title_font_family == 'Inherit Fonts' ? 'inherit' : $title_font_family;

            $bookingpress_customize_css_content .= '.bpa-front-module-location-module .bpa-fm-li__card .bpa-front-li-card__left .bpa-front-li__default-img svg{
                fill: ' . $content_color . ' !important;
            }';

            $bookingpress_customize_css_content .= '.bpa-front-module--location-items-row .bpa-front-li__default-img{
                border-color: '. $border_color .' !important;
            }';

            $bookingpress_customize_css_content .='.bpa-is-location-val__summary{
                border-color: '. $border_color .' !important;
            }';

            $bookingpress_customize_css_content .= '.bpa-is-location-val__summary .bpa-lvs__val .bpa-lvs__val-text{
                color: '. $title_label_color .' !important;
                font-family: '.$title_font_family . '!important;
            }';
            $bookingpress_customize_css_content .= '.bpa-is-location-val__summary .bpa-lvs__label{
                background-color: '. $shortcode_footer_background_color .' !important;
                color: '.$sub_title_color.'!important;
                font-family: '.$title_font_family . '!important;
            }';

            $bookingpress_customize_css_content .= '.bpa-li-col__body .bpa-li-col__title{
                color : '.$sub_title_color.'!important;
            }';

            return $bookingpress_customize_css_content;
        }

        function bookingpress_appointment_add_dynamic_vue_methods_location_func(){
            ?>
            select_location_filter( location_id ){
                const vm = this;
                vm.bookingpress_selected_location_filter = location_id;
                vm.bookingpress_search_location = location_id;
            },
            <?php
        }

        function bookingpress_manage_appointments_location_filter_content(){
            ?>
            <?php /** location filter for appointments - start */ ?>
            <div class="bpa-grid-filter__is-location-pills" v-if="bookingpress_total_locations > 0">
                <h4><?php esc_html_e( 'Locations', 'bookingpress-location' ); ?></h4>
                <div class="bpa-ilp__items">
                    <div class="bpa-ilp__item" :class="( bookingpress_selected_location_filter == location_id ? '__bpa-is-active' : '')" v-for="( location_name, location_id ) of bookingpress_location_filter" @click="select_location_filter( location_id ); ">
                        <span>{{location_name}}</span>
                        <svg v-if="bookingpress_selected_location_filter == location_id" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_4939_15234)">
                                <path d="M6.75047 12.1277L4.14797 9.52523C3.85547 9.23273 3.38297 9.23273 3.09047 9.52523C2.79797 9.81773 2.79797 10.2902 3.09047 10.5827L6.22547 13.7177C6.51797 14.0102 6.99047 14.0102 7.28297 13.7177L15.218 5.78273C15.5105 5.49023 15.5105 5.01773 15.218 4.72523C14.9255 4.43273 14.453 4.43273 14.1605 4.72523L6.75047 12.1277Z" />
                            </g>
                            <defs>
                                <clipPath id="clip0_4939_15234">
                                    <rect width="18" height="18" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            <?php /** location filter for appointments - end */ ?>
            <?php
        }

        function bookingpress_check_staffmember_for_different_location(){

            $staff_id = !empty( $_POST['staffmember_id'] ) ? intval( $_POST['staffmember_id'] ) : 0; // phpcs:ignore
            $service_id = !empty( $_POST['service_id'] ) ? intval( $_POST['service_id'] ) : 0;  // phpcs:ignore
            $location_id = !empty( $_POST['location_id'] ) ? intval( $_POST['location_id'] ) : 0; // phpcs:ignore

            if( empty( $staff_id ) || empty( $service_id ) ){
                return true;
            }

            global $wpdb,$tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
            
            if( !empty( $location_id ) ){
                $get_staff = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_service_staff_pricing_id) as total_staff FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_id != %d", $service_id, $staff_id, $location_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
            } else {
                $get_staff = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(bookingpress_service_staff_pricing_id) as total_staff FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d", $service_id, $staff_id ) );  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
            }
            
            if( 0 < $get_staff ){
                echo 'error';
            } else {
                echo 'success';
            }
            
            die;
        }

        function bookingpress_update_location_position(){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations;
            $response = array();

            $bpa_check_authorization = $this->bpa_check_authentication( 'manage_location_position', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $old_position        = isset($_POST['old_position']) ? intval($_POST['old_position']) : $old_position; // phpcs:ignore WordPress.Security.NonceVerification
            $new_position        = isset($_POST['new_position']) ? intval($_POST['new_position']) : $new_position; // phpcs:ignore WordPress.Security.NonceVerification
            $response['variant'] = 'danger';
            $response['title']   = esc_html__('Error', 'bookingpress-location');
            $response['msg']     = esc_html__('Something went wrong..', 'bookingpress-location');
            if (isset($old_position) && isset($new_position) ) {
                if ($new_position > $old_position ) {
                    //$condition = 'BETWEEN ' . $old_position . ' AND ' . $new_position;
                    $services  = $wpdb->get_results( $wpdb->prepare( 'SELECT bookingpress_location_position, bookingpress_location_id FROM ' . $tbl_bookingpress_locations . ' WHERE bookingpress_location_position BETWEEN %d AND %d order by bookingpress_location_position ASC', $old_position, $new_position ), ARRAY_A); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared --Reason: $tbl_bookingpress_locations is a table name
                    foreach ( $services as $service ) {
                        $position = $service['bookingpress_location_position'] - 1;
                        $position = ( $service['bookingpress_location_position'] == $old_position ) ? $new_position : $position;
                        $args     = array(
                         'bookingpress_location_position' => $position,
                        );
                        $wpdb->update($tbl_bookingpress_locations, $args, array( 'bookingpress_location_id' => $service['bookingpress_location_id'] ));
                    }
                } else {
                    $services = $wpdb->get_results( $wpdb->prepare( 'SELECT bookingpress_location_position, bookingpress_location_id FROM ' . $tbl_bookingpress_locations . ' WHERE bookingpress_location_position BETWEEN %d AND %d order by bookingpress_location_position ASC', $new_position, $old_position ), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally. False Positive alarm
                    foreach ( $services as $service ) {
                        $position = $service['bookingpress_location_position'] + 1;
                        $position = ( $service['bookingpress_location_position'] == $old_position ) ? $new_position : $position;
                        $args     = array(
                            'bookingpress_location_position' => $position,
                        );
                        $wpdb->update($tbl_bookingpress_locations, $args, array( 'bookingpress_location_id' => $service['bookingpress_location_id'] ));
                    }
                }
                $response['variant'] = 'success';
                $response['title']   = esc_html__('Success', 'bookingpress-location');
                $response['msg']     = esc_html__('Service position has been changed successfully.', 'bookingpress-location');
            }
            if (isset($_POST['action']) && sanitize_text_field($_POST['action']) == 'bookingpress_position_location' ) { // phpcs:ignore WordPress.Security.NonceVerification
                wp_send_json($response);
            }
            die;
        }

        function bookingpress_customize_dynamic_vue_methods_location_func(){
            ?>
                bpa_select_location( selected_location ){
                    const vm = this
                    vm.bookingpress_shortcode_form.selected_location = selected_location
                },
            <?php
        }

        function bookingpress_customize_add_dynamic_data_fields_location_func( $bookingpress_customize_vue_data_fields ){

            $bookingpress_customize_vue_data_fields['bookingpress_shortcode_form']['selected_location'] = 'location_1';
            $bookingpress_customize_vue_data_fields['booking_form_settings']['bookingpress_location_information'] = '2';	
            return $bookingpress_customize_vue_data_fields;
        }

        function bookingpress_get_booking_form_customize_location_data_filter_func( $booking_form_settings ){

            $booking_form_settings['booking_form_settings']['bookingpress_location_information'] = '';
            return $booking_form_settings;
        }

        function bookingpress_customization_form_sequence_data_with_location( $bookingpress_form_sequence_arr ){

            global $BookingPress;
			$bookingpress_form_sequence = $BookingPress->bookingpress_get_customize_settings('bookingpress_form_sequance', 'booking_form');
			$bookingpress_form_sequence = json_decode($bookingpress_form_sequence, TRUE);

            $bookingpress_location_pos = array_search( 'location_selection', $bookingpress_form_sequence );
            $bookingpress_staff_pos = array_search('staff_selection', $bookingpress_form_sequence);
			$bookingpress_service_pos = array_search('service_selection', $bookingpress_form_sequence);

            $bookingpress_form_sequence_arr['location'] = array(
                'title' => 'location_title',
                'next_tab' => 'service_title',
                'previous_tab' => '',
                'name' => 7,
                'icon' => 'place',
                'is_visible' => 1,
                'tab_name' => 'location_selection'
            );
            
            return $bookingpress_form_sequence_arr;
        }

        function bookingpress_add_location_information_data(){ ?>
            <div class="bpa-sm--item">
                <el-row type="flex" class="bpa-customize-lp-field"> 
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">													
                        <label class="bpa-form-label"><?php //echo esc_html( $bookingpress_singular_staffmember_name ); ?> <?php esc_html_e('Location Information', 'bookingpress-location'); ?></label>
                        <el-select v-model="booking_form_settings.bookingpress_location_information" class="bpa-form-control"  popper-class="bpa-el-select--is-with-navbar bpa-el-select--is-customize-left-panel">
                            <el-option label="<?php esc_html_e('Show both phone number and address','bookingpress-location'); ?>" value="1"></el-option>														
                            <el-option label="<?php esc_html_e('Show only address','bookingpress-location'); ?>" value="2"></el-option>														
                            <el-option label="<?php esc_html_e('Show only phone number','bookingpress-location'); ?>" value="3"></el-option>
                            <el-option label="<?php esc_html_e('Don\'t show both phone number and address','bookingpress-location'); ?>" value="4"></el-option>
                        </el-select>    													
                    </el-col>
                </el-row>
            </div>
        <?php }

        function bookingpress_add_location_step_for_customize_tab(){
            ?>
            <div class="bpa-cbf--preview-step" :style="{ 'background': selected_colorpicker_values.background_color,'border-color': selected_colorpicker_values.border_color }" v-if="current_element.name == 7">
                <div class="bpa-cbf--preview-step__body-content">
                    <div class="bpa-cbf--preview--module-container __location-module">
                        <el-row>
							<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
								<div class="bpa-front-module-heading" v-text="tab_container_data.location_title" :style="{ 'color': selected_colorpicker_values.label_title_color, 'font-size': selected_font_values.title_font_size+'px', 'font-family': selected_font_values.title_font_family}"></div>                                     
							</el-col>
						</el-row>
                        <el-row :gutter="32">
                            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                <div class="bpa-front-module--service-item" :class="(bookingpress_shortcode_form.selected_location == 'location_1') ? ' __bpa-is-selected' : ''" @click="bpa_select_location('location_1')">
                                    <div class="bpa-front-si-card bpa-front-sm-card" :style="[bookingpress_shortcode_form.selected_location == 'location_1' ? { 'border-color': selected_colorpicker_values.primary_color } : { 'border-color': selected_colorpicker_values.border_color }]">
                                        <div class="bpa-front-si-card--checkmark-icon" v-if="bookingpress_shortcode_form.selected_location == 'location_1'">
											<span class="material-icons-round" :style="[bookingpress_shortcode_form.selected_location == 'location_1' ? { 'color': selected_colorpicker_values.primary_color } : { 'color': selected_colorpicker_values.content_color }]">check_circle</span>
										</div>
                                        <div class="bpa-front-si-card__left bpa-front-sm-card__left">
											<div class="bpa-front-sm__default-img" :style="{'border-color': selected_colorpicker_values.border_color}">
                                                <svg :style="{'fill':selected_colorpicker_values.content_color}" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
											</div>
										</div>
                                        <div class="bpa-li-col__body">
                                            <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}">New York</div>
                                            <div class="bpa-li-col__address" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '2'" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}">5702 Bataan Memorial</div>
                                            <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '3'">+1234567890</div>
										</div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                <div class="bpa-front-module--service-item" :class="(bookingpress_shortcode_form.selected_location == 'location_2') ? ' __bpa-is-selected' : ''" @click="bpa_select_location('location_2')">
                                    <div class="bpa-front-si-card bpa-front-sm-card" :style="[bookingpress_shortcode_form.selected_location == 'location_2' ? { 'border-color': selected_colorpicker_values.primary_color } : { 'border-color': selected_colorpicker_values.border_color }]">
                                        <div class="bpa-front-si-card--checkmark-icon" v-if="bookingpress_shortcode_form.selected_location == 'location_2'">
											<span class="material-icons-round" :style="[bookingpress_shortcode_form.selected_location == 'location_2' ? { 'color': selected_colorpicker_values.primary_color } : { 'color': selected_colorpicker_values.content_color }]">check_circle</span>
										</div>
                                        <div class="bpa-front-si-card__left bpa-front-sm-card__left">
											<div class="bpa-front-sm__default-img" :style="{'border-color': selected_colorpicker_values.border_color}">
                                                <svg :style="{'fill':selected_colorpicker_values.content_color}" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
											</div>
										</div>
                                        <div class="bpa-li-col__body">
                                            <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}">Texas</div>
                                            <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '2'">1649 W Campbell Rd</div>
                                            <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '3'">+1234567890</div>
										</div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                <div class="bpa-front-module--service-item" :class="(bookingpress_shortcode_form.selected_location == 'location_3') ? ' __bpa-is-selected' : ''" @click="bpa_select_location('location_3')">
                                    <div class="bpa-front-si-card bpa-front-sm-card" :style="[bookingpress_shortcode_form.selected_location == 'location_3' ? { 'border-color': selected_colorpicker_values.primary_color } : { 'border-color': selected_colorpicker_values.border_color }]">
                                        <div class="bpa-front-si-card--checkmark-icon" v-if="bookingpress_shortcode_form.selected_location == 'location_3'">
											<span class="material-icons-round" :style="[bookingpress_shortcode_form.selected_location == 'location_3' ? { 'color': selected_colorpicker_values.primary_color } : { 'color': selected_colorpicker_values.content_color }]">check_circle</span>
										</div>
                                        <div class="bpa-front-si-card__left bpa-front-sm-card__left">
											<div class="bpa-front-sm__default-img" :style="{'border-color': selected_colorpicker_values.border_color}">
                                                <svg :style="{'fill':selected_colorpicker_values.content_color}" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
											</div>
										</div>
                                        <div class="bpa-li-col__body">
                                            <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}">Michigan</div>
                                            <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '2'">123 W Main St</div>
                                            <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '3'">+1234567890</div>
										</div>
                                    </div>
                                </div>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                <div class="bpa-front-module--service-item" :class="(bookingpress_shortcode_form.selected_location == 'location_4') ? ' __bpa-is-selected' : ''" @click="bpa_select_location('location_4')">
                                    <div class="bpa-front-si-card bpa-front-sm-card" :style="[bookingpress_shortcode_form.selected_location == 'location_4' ? { 'border-color': selected_colorpicker_values.primary_color } : { 'border-color': selected_colorpicker_values.border_color }]">
                                        <div class="bpa-front-si-card--checkmark-icon" v-if="bookingpress_shortcode_form.selected_location == 'location_4'">
											<span class="material-icons-round" :style="[bookingpress_shortcode_form.selected_location == 'location_4' ? { 'color': selected_colorpicker_values.primary_color } : { 'color': selected_colorpicker_values.content_color }]">check_circle</span>
										</div>
                                        <div class="bpa-front-si-card__left bpa-front-sm-card__left">
											<div class="bpa-front-sm__default-img" :style="{'border-color': selected_colorpicker_values.border_color}">
                                                <svg :style="{'fill':selected_colorpicker_values.content_color}" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
											</div>
										</div>
                                        <div class="bpa-li-col__body">
                                            <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}">Washington</div>
                                            <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '2'">3920 Auburn Way N</div>
                                            <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}" v-if="booking_form_settings.bookingpress_location_information == '1' || booking_form_settings.bookingpress_location_information == '3'">+1234567890</div>
										</div>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
                <div class="bpa-front-tabs--foot" :style="{'background': selected_colorpicker_values.background_color,'border-color':selected_colorpicker_values.border_color }">   
					<el-button class="bpa-btn bpa-btn--borderless" :style="{'color': selected_colorpicker_values.sub_title_color,'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.sub_title_font_size+'px'}" v-if="current_element.previous_tab != ''">
						<span class="material-icons-round">west</span>
						{{ booking_form_settings.goback_button_text }}
					</el-button>
					<el-button class="bpa-btn bpa-btn--primary bpa-btn--front-preview" :style="{ 'background': selected_colorpicker_values.primary_color, 'border-color': selected_colorpicker_values.primary_color, color: selected_colorpicker_values.price_button_text_color,'font-size': selected_font_values.sub_title_font_size+'px','font-family': selected_font_values.title_font_family,'font-size': selected_font_values.sub_title_font_size+'px'}" v-if="current_element.next_tab != ''">
						<span class="bpa--text-ellipsis">{{ booking_form_settings.next_button_text}}: <strong>{{tab_container_data[current_element.next_tab] }}</strong></span>
						<span class="material-icons-round">east</span>
					</el-button>
				</div>
            </div>
            <?php
        }
        function bookingpress_form_sequence_list_item_location(){
            ?>
                <div class="bpa-cfs__step-val" v-else-if="form_sequence == 'location_selection'"><?php esc_html_e('Location Selection','bookingpress-location'); ?></div>
            <?php
        }

        function get_location_service_staff_details($is_edit_mode = 0, $edit_id = 0)
        {
            global $wpdb, $BookingPress, $tbl_bookingpress_services, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_services, $bookingpress_services, $tbl_bookingpress_locations_service_staff_pricing_details;

            $bookingpress_location_service_list = array();

            //Get services list
            $bookingpress_services_list = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_services} ORDER BY bookingpress_service_position ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_services is a table name. false alarm
            if(!empty($bookingpress_services_list)){
                foreach($bookingpress_services_list as $bookingpress_service_list_key => $bookingpress_service_val){
                    $bookingpress_service_id = intval($bookingpress_service_val['bookingpress_service_id']);
                    $bookingpress_service_name = stripslashes_deep($bookingpress_service_val['bookingpress_service_name']);
                    $bookingpress_service_price = floatval($bookingpress_service_val['bookingpress_service_price']);
                    $bookingpress_service_max_capacity = $bookingpress_services->bookingpress_get_service_meta( $bookingpress_service_id, 'max_capacity' );
                    $bookingpress_service_min_capacity = $bookingpress_services->bookingpress_get_service_meta( $bookingpress_service_id, 'min_capacity' );
                    if(!empty($bookingpress_service_max_capacity)){
                        $bookingpress_service_max_capacity = floatval($bookingpress_service_max_capacity);
                    }
                    if(!empty($bookingpress_service_min_capacity)){
                        $bookingpress_service_min_capacity = floatval($bookingpress_service_min_capacity);
                    }

                    $bookingpress_location_tmp_service_list = array(
                        'service_id' => $bookingpress_service_id,
                        'service_name' => $bookingpress_service_name,
                        'service_price' => $bookingpress_service_price,
                        'service_max_capacity' => $bookingpress_service_max_capacity,
                        'service_min_capacity' => $bookingpress_service_min_capacity,
                        'is_service_selected' => false,
                    );

                    //If edit mode then check service selected or not
                    if( !empty($is_edit_mode) && !empty($edit_id) ){
                        //Check service assigned or not
                        $bookingpress_is_service_assigned = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $edit_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                        if(!empty($bookingpress_is_service_assigned)){
                            $bookingpress_location_tmp_service_list['service_price'] = $bookingpress_is_service_assigned['bookingpress_service_price'];
                            $bookingpress_location_tmp_service_list['service_max_capacity'] = $bookingpress_is_service_assigned['bookingpress_service_qty'];
                            $bookingpress_location_tmp_service_list['service_min_capacity'] = $bookingpress_is_service_assigned['bookingpress_service_min_qty'];
                            $bookingpress_location_tmp_service_list['is_service_selected'] = true;
                        }
                    }

                    //Get staffmembers list
                    $bookingpress_staff_list = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_staffmember_id, bookingpress_staffmember_login, bookingpress_staffmember_firstname, bookingpress_staffmember_lastname, bookingpress_staffmember_email FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_staffmember_status = %d", 1), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers is table name defined globally.

                    $bookingpress_tmp_staff_list = array();

                    if(!empty($bookingpress_staff_list)){
                        foreach($bookingpress_staff_list as $bookingpress_staff_key => $bookingpress_staff_val){
                            //Check staff assigned or not
                            $bookingpress_staffmember_id = intval($bookingpress_staff_val['bookingpress_staffmember_id']);

                            $bookingpress_is_staff_assign = $wpdb->get_var($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_staffmembers_services} WHERE bookingpress_staffmember_id = %d AND bookingpress_service_id = %d", $bookingpress_staffmember_id, $bookingpress_service_id)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers_services is table name defined globally.

                            $bookingpress_assign_staff_details = array();
                            if($bookingpress_is_staff_assign > 0){
                                $bookingpress_is_staff_assign = true;

                                $bookingpress_assign_staff_details =  $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_staffmembers_services} WHERE bookingpress_staffmember_id = %d AND bookingpress_service_id = %d", $bookingpress_staffmember_id, $bookingpress_service_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers_services is table name defined globally.
                            }else{
                                $bookingpress_is_staff_assign = false;
                            }

                            $bookingpress_tmp_final_staff_list = array(
                                'staffmember_id' => $bookingpress_staffmember_id,
                                'staffmember_firstname' => $bookingpress_staff_val['bookingpress_staffmember_firstname'],
                                'staffmember_lastname' => $bookingpress_staff_val['bookingpress_staffmember_lastname'],
                                'staffmember_name' => $bookingpress_staff_val['bookingpress_staffmember_firstname']. ' ' .$bookingpress_staff_val['bookingpress_staffmember_lastname'],
                                'staffmember_email' => $bookingpress_staff_val['bookingpress_staffmember_email'],
                                'staffmember_price' => !empty($bookingpress_assign_staff_details['bookingpress_service_price']) ? floatval($bookingpress_assign_staff_details['bookingpress_service_price']) : $bookingpress_service_price,
                                'staffmember_capacity' => !empty($bookingpress_assign_staff_details['bookingpress_service_capacity']) ? intval($bookingpress_assign_staff_details['bookingpress_service_capacity']) : $bookingpress_service_max_capacity,
                                'staffmember_min_capacity' => !empty($bookingpress_assign_staff_details['bookingpress_service_min_capacity']) ? intval($bookingpress_assign_staff_details['bookingpress_service_capacity']) : $bookingpress_service_min_capacity,
                                'is_staff_selected' => $bookingpress_is_staff_assign,
                            );

                            //If edit mode then check service selected or not
                            if( !empty($is_edit_mode) && !empty($edit_id) ){
                                //Check service assigned or not
                                $bookingpress_is_staff_assigned = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_service_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_id = %d", $bookingpress_service_id, $bookingpress_staffmember_id, $edit_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.

                                if(!empty($bookingpress_is_staff_assigned)){
                                    $bookingpress_tmp_final_staff_list['staffmember_price'] = $bookingpress_is_staff_assigned['bookingpress_staffmember_price'];
                                    $bookingpress_tmp_final_staff_list['staffmember_capacity'] = $bookingpress_is_staff_assigned['bookingpress_staffmember_qty'];
                                    $bookingpress_tmp_final_staff_list['staffmember_min_capacity'] = $bookingpress_is_staff_assigned['bookingpress_staffmember_min_qty'];
                                    $bookingpress_tmp_final_staff_list['is_staff_selected'] = true;
                                }
                            }

                            $bookingpress_tmp_staff_list[ $bookingpress_staffmember_id ] = $bookingpress_tmp_final_staff_list;
                        }
                    }else{
                        $bookingpress_staff_list = array();
                    }
                    $bookingpress_location_tmp_service_list['staffmembers'] = $bookingpress_tmp_staff_list;

                    $bookingpress_location_service_list[ $bookingpress_service_id ] = $bookingpress_location_tmp_service_list;
                }
            }

            return $bookingpress_location_service_list;
        }

        function bookingpress_add_dynamic_menu_item_to_top_func(){
            global $bookingpress_slugs;
            $request_module = ( ! empty( $_REQUEST['page'] ) && ( $_REQUEST['page'] != 'bookingpress' ) ) ? str_replace( 'bookingpress_', '', sanitize_text_field( $_REQUEST['page'] ) ) : 'dashboard'; //// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason - $_REQUEST['action'] sanitized properly
            ?>
                <li class="bpa-nav-item <?php echo ( 'location' == $request_module ) ? '__active' : ''; ?>">
					<?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - URL is escaped properly ?>
					<a href="<?php echo add_query_arg( 'page',esc_html($bookingpress_slugs->bookingpress_location), esc_url( admin_url() . 'admin.php?page=bookingpress' ) );  // phpcs:ignore ?>" class="bpa-nav-link">
                        <div class="bpa-nav-link--icon">
                            <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24"><g><rect fill="none" height="24" width="24"/></g><g><path d="M12,2c-4.2,0-8,3.22-8,8.2c0,3.18,2.45,6.92,7.34,11.23c0.38,0.33,0.95,0.33,1.33,0C17.55,17.12,20,13.38,20,10.2 C20,5.22,16.2,2,12,2z M12,12c-1.1,0-2-0.9-2-2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2C14,11.1,13.1,12,12,12z"/></g></svg>
                        </div>                        
                        <?php esc_html_e( 'Locations', 'bookingpress-location' ); ?>
                    </a>
                </li>
            <?php
        }

        function bookingpress_add_setting_dynamic_data_fields_func($bookingpress_dynamic_setting_data_fields){

            global $BookingPress, $bookingpress_global_options;
            $bookingpress_global_data = $bookingpress_global_options->bookingpress_global_options();

            if(!empty($bookingpress_dynamic_setting_data_fields['general_setting_form'])){
                $bookingpress_dynamic_setting_data_fields['general_setting_form']['allow_staffmember_to_serve_multiple_locations'] = false;
                $bookingpress_dynamic_setting_data_fields['general_setting_form']['location_specific_capacity_price'] = false;
            }

            $bookingpress_dynamic_setting_data_fields['bookingpress_outside_location_placeholder'] = json_decode($bookingpress_global_data['location_placeholders'],true);

            if(isset($bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'])){

                $bookingpress_dynamic_setting_data_fields_other = $bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'];

                $bookingpress_dynamic_setting_data_fields_location['bookingpress_invoice_tag_list'] = array(                
                    array( 'group_tag_name' =>  __('Location','bookingpress-location'),
                        'tag_details' => array(
                            array( 'tag_name' =>  '{location_name}',
                            ),  
                            array( 'tag_name' =>  '{location_phone}',
                            ),  
                            array( 'tag_name' =>  '{location_address}',
                            ),  
                            array( 'tag_name' =>  '{location_description}',
                            ),  
                        ),    
                    ),  
                );
                $bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'] = array_merge( $bookingpress_dynamic_setting_data_fields_other, $bookingpress_dynamic_setting_data_fields_location['bookingpress_invoice_tag_list'] );
            }

            return $bookingpress_dynamic_setting_data_fields;
        }

        function bookingpress_add_general_setting_section_func(){
            ?>
                <div class="bpa-gs__cb--item">
					<div class="bpa-gs__cb--item-heading">
						<h4 class="bpa-sec--sub-heading"><?php esc_html_e( 'Location Settings', 'bookingpress-location' ); ?></h4>
					</div>
					<div class="bpa-gs__cb--item-body">
						<el-row type="flex" class="bpa-gs--tabs-pb__cb-item-row">
							<el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-left">
								<h4><?php esc_html_e( 'Allow staffmember to serve on multiple locations', 'bookingpress-location' ); ?></h4>
							</el-col>
							<el-col :xs="12" :sm="12" :md="12" :lg="8" :xl="8" class="bpa-gs__cb-item-right">
								<el-form-item>
									<el-switch class="bpa-swtich-control" v-model="general_setting_form.allow_staffmember_to_serve_multiple_locations"></el-switch>	
								</el-form-item>
							</el-col>
						</el-row>
					</div>
				</div>
            <?php
        }

        function bookingpress_get_edit_location_func(){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_services, $tbl_bookingpress_staffmembers, $bookingpress_pro_staff_members;

            $bpa_check_authorization = $this->bpa_check_authentication( 'edit_location_details', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $response['variant']   = 'error';
            $response['title']     = esc_html__('Error', 'bookingpress-location');
            $response['msg']       = esc_html__('Something went wrong..', 'bookingpress-location');
            $response['edit_data'] = array();
            
            if (! empty($_POST['edit_id']) ) { // phpcs:ignore WordPress.Security.NonceVerification
                $bookingpress_edit_id               = intval($_POST['edit_id']); // phpcs:ignore WordPress.Security.NonceVerification

                $location_details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations} WHERE bookingpress_location_id = %d", $bookingpress_edit_id), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $get_location_services = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_service_staff_pricing_id,bookingpress_service_id, bookingpress_staffmember_id, bookingpress_service_qty, bookingpress_service_min_qty, bookingpress_staff_location_qty, bookingpress_staff_location_min_qty FROM {$tbl_bookingpress_locations_service_staff_pricing_details} WHERE bookingpress_location_id = %d", $bookingpress_edit_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_staff_pricing_details is table name defined globally.
                $assigned_services = array();
                if( !empty( $get_location_services ) ){
                    foreach( $get_location_services as $ls_key => $ls_value ){
                        
                        $location_service_id = $ls_value->bookingpress_service_id;
                        
                        $location_service_name = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_name FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $location_service_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_services is table name defined globally.
                        
                        $location_staff_id = $ls_value->bookingpress_staffmember_id;
                        
                        $location_service_capacity = $ls_value->bookingpress_service_qty;
                        $location_service_min_capacity = $ls_value->bookingpress_service_min_qty;

                        if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation() ){
                            $location_service_capacity = $ls_value->bookingpress_staff_location_qty;
                            $location_service_min_capacity = $ls_value->bookingpress_staff_location_min_qty;
                        }

                        $assigned_location_service_data = array(
                            'service_id' => $location_service_id,
                            'service_name' => $location_service_name,
                            'service_capacity' => $location_service_capacity,
                            'service_min_capacity' => $location_service_min_capacity,
                        );

                        $location_staff_name = $wpdb->get_row( $wpdb->prepare( "SELECT bookingpress_staffmember_firstname,bookingpress_staffmember_lastname FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_staffmember_id = %d", $location_staff_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_staffmembers is table name defined globally.
                        
                        $assigned_location_service_data['service_staff_location_id'] = $ls_value->bookingpress_service_staff_pricing_id;
                        $bookingpress_staffmember_firstname = !empty($location_staff_name->bookingpress_staffmember_firstname) ? $location_staff_name->bookingpress_staffmember_firstname : '';
                        $bookingpress_staffmember_lastname = !empty($location_staff_name->bookingpress_staffmember_lastname) ? $location_staff_name->bookingpress_staffmember_lastname : '';
                        $location_staff_name = $bookingpress_staffmember_firstname .' '. $bookingpress_staffmember_lastname;
                        $assigned_location_service_data['staffmember_id'] = $location_staff_id;
                        $assigned_location_service_data['staffmember_name'] = $location_staff_name;
                        $assigned_location_service_data['multiple_staffs'] = false;
                        $assigned_location_service_data['staff_counter'] = 1;
                        $assigned_location_service_data['staff_extra_counter'] = 0;
                        $assigned_location_service_data['staffmember_data'] = array(
                            array(
                                'staffmember_id' => $location_staff_id,
                                'staffmember_name' => $location_staff_name
                            )
                        );

                        $assigned_services[] = $assigned_location_service_data;
                    }
                }
                $location_details['assigned_service_details'] = $assigned_services;
                
                $response['edit_data'] = $location_details;
                $response['msg']       = esc_html__('Edit data retrieved successfully', 'bookingpress-location');
                $response['variant']   = 'success';
                $response['title']     = esc_html__('Success', 'bookingpress-location');
                $response = apply_filters('bookingpress_modified_get_edit_location_response',$response,$bookingpress_edit_id);
            }

            echo wp_json_encode($response);
            exit();
        }

        function bookingpress_delete_location_bulk_action(){

            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;
            $response = array();

            $bpa_check_authorization = $this->bpa_check_authentication( 'delete_location', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }
            
            $response['variant'] = 'error';
            $response['title']   = esc_html__('Error', 'bookingpress-location');
            $response['msg']     = esc_html__('Something went wrong..', 'bookingpress-location');
            $return              = false;

            $delete_ids = !empty( $_POST['delete_ids'] ) ? sanitize_text_field( $_POST['delete_ids'] ) : ''; // phpcs:ignore
            $delete_ids = json_decode( stripslashes_deep( $delete_ids ), true );

            if( empty( $delete_ids ) ){
                $response['msg'] = esc_html__( 'Please select at-least one location to delete', 'bookingpress-location' );
                wp_send_json( $response );
                die;
            }

            $deleted_ids = array();
            foreach( $delete_ids as $location_id ){
                $bpa_location_delete = $wpdb->delete($tbl_bookingpress_locations, array( 'bookingpress_location_id' => $location_id ));
                $bpa_location_service_staff_delete = $wpdb->delete($tbl_bookingpress_locations_service_staff_pricing_details, array( 'bookingpress_location_id' => $location_id ));
                if( true == $bpa_location_delete && true == $bpa_location_service_staff_delete ){
                    $deleted_ids[] = $location_id;
                }
            }

            if( count( $delete_ids ) == count( $deleted_ids ) ){
                $response['variant'] = 'success';
                $response['title']   = esc_html__('Success', 'bookingpress-location');
                $response['msg']     = esc_html__('Location has been deleted successfully.', 'bookingpress-location');
            } else if( 0 == count( $delete_ids ) ) {
                $response['msg']     = esc_html__('Something went wrong while deleting locations.', 'bookingpress-location');
            } else {
                $response['msg']     = esc_html__('Some of the locations has not been deleted sucessfully.', 'bookingpress-location');
            }

            wp_send_json( $response );
            die;
        }

        function bookingpress_delete_location_func($delete_id){
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details,$tbl_bookingpress_appointment_bookings;
            $response              = array();

            $bpa_check_authorization = $this->bpa_check_authentication( 'delete_location', true, 'bpa_wp_nonce' );
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }
            
            $response['variant'] = 'error';
            $response['title']   = esc_html__('Error', 'bookingpress-location');
            $response['msg']     = esc_html__('Something went wrong..', 'bookingpress-location');
            $return              = false;

            if (! empty($_POST['delete_id']) || intval($delete_id) ) { // phpcs:ignore WordPress.Security.NonceVerification
                $delete_location_id = ! empty($_POST['delete_id']) ? intval($_POST['delete_id']) : intval($delete_id); // phpcs:ignore WordPress.Security.NonceVerification
                if (! empty($delete_location_id) ) {

                    $current_date                   = date('Y-m-d', current_time('timestamp'));

                    $bookingperss_appointments_data = $wpdb->get_results( $wpdb->prepare( "SELECT bookingpress_appointment_booking_id FROM {$tbl_bookingpress_appointment_bookings} WHERE bookingpress_location_id = %d AND bookingpress_appointment_date >= %s AND (bookingpress_appointment_status != '3' AND bookingpress_appointment_status != '4') ", $delete_location_id, $current_date ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
 
                    if (count($bookingperss_appointments_data) == 0 ) {

                        $wpdb->delete($tbl_bookingpress_locations, array( 'bookingpress_location_id' => $delete_location_id ));
                        $wpdb->delete($tbl_bookingpress_locations_service_staff_pricing_details, array( 'bookingpress_location_id' => $delete_location_id ));
    
                        $response['variant'] = 'success';
                        $response['title']   = esc_html__('Success', 'bookingpress-location');
                        $response['msg']     = esc_html__('Location has been deleted successfully.', 'bookingpress-location');
                        $return = true;

                        if (! empty($_POST['action']) && sanitize_text_field($_POST['action']) == 'bookingpress_delete_location' ) { // phpcs:ignore
                            echo wp_json_encode($response);
                            exit();
                        }
                    } else {

                        $bookingpress_error_msg = esc_html__(' I am sorry', 'bookingpress-location') . '! ' . esc_html__('This location can not be deleted because it has one or more appointments associated with it', 'bookingpress-location') . '.';

                        $response['variant'] = 'warning';
                        $response['title']   = esc_html__('warning', 'bookingpress-location');
                        $response['msg']     = $bookingpress_error_msg;
                        $return              = false;
                        
                        if (! empty($_POST['action']) && sanitize_text_field($_POST['action']) == 'bookingpress_delete_location' ) { // phpcs:ignore
                            echo wp_json_encode($response);
                            exit();
                        }
                       
                    }
                }
            }
            return $return;
        }

        function bookingpress_modify_capability_data_func($bpa_caps){

            $bpa_caps['bookingpress_location'][] = 'get_location_details';
            $bpa_caps['bookingpress_location'][] = 'save_location_details';
            $bpa_caps['bookingpress_location'][] = 'delete_location';
            $bpa_caps['bookingpress_location'][] = 'edit_location_details';
            $bpa_caps['bookingpress_location'][] = 'manage_location_position';
            $bpa_caps['bookingpress_location'][] = 'remove_location_avatar';
            $bpa_caps['bookingpress_location'][] = 'upload_location_avatar';

            return $bpa_caps;
        }

        function bookingpress_get_locations_func(){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations,$tbl_bookingpress_appointment_bookings;
			$response              = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'get_location_details', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-location');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

            $response['variant'] = 'error';
            $response['title'] = esc_html__( 'Error', 'bookingpress-location');
            $response['msg'] = esc_html__('Sorry. Something went wrong while processing the request', 'bookingpress-location');

            $perpage     = isset($_POST['perpage']) ? intval($_POST['perpage']) : 10; // phpcs:ignore WordPress.Security.NonceVerification
            $currentpage = isset($_POST['currentpage']) ? intval($_POST['currentpage']) : 1; // phpcs:ignore WordPress.Security.NonceVerification
            $offset      = ( ! empty($currentpage) && $currentpage > 1 ) ? ( ( $currentpage - 1 ) * $perpage ) : 0; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --Reason - $_REQUEST['search_data'] contains mixed array and it's been sanitized properly using 'appointment_sanatize_field' function

            $bookingpress_search_data  = ! empty($_REQUEST['search_data']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_REQUEST['search_data']) : array(); // phpcs:ignore
            $bookingpress_search_query = '';
            if (! empty($bookingpress_search_data) ) {
            }

            $get_total_locations = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations} {$bookingpress_search_query} ORDER BY bookingpress_location_position ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations is a table name. false alarm
            $total_locations     = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_locations} {$bookingpress_search_query} ORDER BY bookingpress_location_position ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_locations is a table name. false alarm
            $locations           = array();
            
            $current_date = date('Y-m-d', current_time('timestamp'));
            if (! empty($total_locations) ) {
                foreach( $total_locations as $get_location ){

                    $bookingpress_location_id     = intval($get_location['bookingpress_location_id']);
                    $bookingperss_appointments_data = '';
                    $bookingperss_appointments_data = $wpdb->get_results( $wpdb->prepare( 'SELECT bookingpress_appointment_booking_id  FROM ' . $tbl_bookingpress_appointment_bookings . ' WHERE bookingpress_location_id = %d AND bookingpress_appointment_date >= %s AND (bookingpress_appointment_status != "3" AND bookingpress_appointment_status != "4")', $bookingpress_location_id, $current_date ), ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_appointment_bookings is table name defined globally. False Positive alarm
                
                    $get_location['location_bulk_action'] = false; 

                    if (! empty($bookingperss_appointments_data) ) {
                        $get_location['location_bulk_action'] = true; 
                    }
                    $get_location['selected'] = false;
                    $locations[] = $get_location;

                }
                $resposne['variant'] = 'success';
                $response['title'] = esc_html__( 'Success', 'bookingpress-location' );
                $response['msg'] = esc_html__( 'Location data fetched successfully', 'bookingpress-location' );
            }
            
            $response['items'] = $locations;
            $response['total'] = count($total_locations);

            echo wp_json_encode($response);
            exit;
        }

        function bookingpress_save_location_details_func(){
            global $wpdb, $BookingPress, $tbl_bookingpress_locations, $tbl_bookingpress_services, $tbl_bookingpress_staffmembers_services, $tbl_bookingpress_locations_service_staff_pricing_details, $tbl_bookingpress_default_workhours, $tbl_bookingpress_staff_member_workhours, $tbl_bookingpress_service_workhours, $bookingpress_pro_services, $bookingpress_pro_staff_members;
			$response              = array();

			$bpa_check_authorization = $this->bpa_check_authentication( 'save_location_details', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-location');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}

            $response['variant'] = 'error';
            $response['title'] = esc_html__( 'Error', 'bookingpress-location');
            $response['msg'] = esc_html__('Something went wrong while save location details', 'bookingpress-location');
            

            $bookingpress_location_form_data = !empty($_POST['location_details']) ? $_POST['location_details'] : array(); // phpcs:ignore

            $bookingpress_location_form_service_details = !empty($_POST['location_service_details']) ? $_POST['location_service_details'] : array(); // phpcs:ignore

            if( !empty($bookingpress_location_form_data) ){

                if ( !empty( trim($bookingpress_location_form_data['location_name'] ) ) ){

                    $bookingpress_update_id = !empty($bookingpress_location_form_data['location_update_id']) ? intval($bookingpress_location_form_data['location_update_id']) : 0;

                    $bookingpress_location_img_name = !empty($bookingpress_location_form_data['location_image_name']) ? $bookingpress_location_form_data['location_image_name'] : '';
                    $bookingpress_location_img_url = !empty($bookingpress_location_form_data['location_image']) ? $bookingpress_location_form_data['location_image'] : '';

                    if(!empty($bookingpress_location_img_name) && !empty($bookingpress_location_img_url)){
                        global $BookingPress;
                        $upload_dir                 = BOOKINGPRESS_UPLOAD_DIR . '/';
                        $bookingpress_new_file_name = current_time('timestamp') . '_' . $bookingpress_location_img_name;
                        $upload_path                = $upload_dir . $bookingpress_new_file_name;

                        $bookingpress_upload_res = new bookingpress_fileupload_class( $bookingpress_location_img_url, true );
                        $bookingpress_upload_res->bookingpress_process_upload( $upload_path );

                        $location_image_new_url   = BOOKINGPRESS_UPLOAD_URL . '/' . $bookingpress_new_file_name;
                        
                        $bookingpress_file_name_arr = explode('/', $bookingpress_location_img_url);
                        $bookingpress_file_name     = $bookingpress_file_name_arr[ count($bookingpress_file_name_arr) - 1 ];
                        if( file_exists( BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name ) ){
                            @unlink(BOOKINGPRESS_TMP_IMAGES_DIR . '/' . $bookingpress_file_name);
                        }

                        $bookingpress_location_img_url = $location_image_new_url;
                        $bookingpress_location_img_name = $bookingpress_new_file_name;
                    }

                    $bookingpress_is_record_add = false;

                    $bookingpress_db_fields = array(
                        'bookingpress_location_name' => $bookingpress_location_form_data['location_name'],
                        'bookingpress_location_phone_country' => $bookingpress_location_form_data['location_phone_country'],
                        'bookingpress_location_phone_number' => $bookingpress_location_form_data['location_phone_number'],
                        'bookingpress_location_address' => $bookingpress_location_form_data['location_address'],
                        'bookingpress_location_description' => stripslashes_deep($bookingpress_location_form_data['location_description']),
                        'bookingpress_location_img_name' => $bookingpress_location_img_name,
                        'bookingpress_location_img_url' => $bookingpress_location_img_url,
                    );

                    if(empty($bookingpress_update_id)){

                        $bookingpress_location_position = 0;

                        $location  = $wpdb->get_row('SELECT * FROM ' . $tbl_bookingpress_locations . ' ORDER BY bookingpress_location_position DESC LIMIT 1', ARRAY_A);// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally. False Positive alarm
                        if (! empty($location) ) {
                            $bookingpress_location_position = $location['bookingpress_location_position'] + 1;
                        }
                        $bookingpress_db_fields['bookingpress_location_position'] = $bookingpress_location_position;
                        $wpdb->insert($tbl_bookingpress_locations, $bookingpress_db_fields);
                        $bookingpress_update_id = $wpdb->insert_id;
                        $bookingpress_is_record_add = true;

                        do_action('bookingpress_after_add_location', $bookingpress_update_id);

                        $response['variant'] = 'success';
                        $response['title'] = esc_html__( 'Success', 'bookingpress-location');
                        $response['msg'] = esc_html__('Location has been added successfully.', 'bookingpress-location');

                    }else{
                        $wpdb->update($tbl_bookingpress_locations, $bookingpress_db_fields, array('bookingpress_location_id' => $bookingpress_update_id));
                        do_action('bookingpress_after_update_location', $bookingpress_update_id);
                        $response['variant'] = 'success';
                        $response['title'] = esc_html__( 'Success', 'bookingpress-location');
                        $response['msg'] = esc_html__('Location has been updated successfully.', 'bookingpress-location');
                    }

                    //Get default workhours
                    $bookingpress_get_default_workinghours_data = $wpdb->get_results("SELECT * FROM {$tbl_bookingpress_default_workhours}", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_default_workhours is table name defined globally.

                    /* $response['variant'] = 'success';
                    $response['title'] = esc_html__( 'Success', 'bookingpress-location');
                    $response['msg'] = esc_html__('Location details successfully', 'bookingpress-location'); */
                } elseif( empty(trim($bookingpress_location_form_data['location_name']))){
                    $response['msg'] = esc_html__('Please add valid data for add location', 'bookingpress-location') . '.';
                }
            }

            if( !empty( $bookingpress_location_form_data['deleted_locations'] ) ){
                $bookingpress_location_delete_services = $bookingpress_location_form_data['deleted_locations'];
                foreach( $bookingpress_location_delete_services as $location_service_staff_id ){
                    $wpdb->delete(
                        $tbl_bookingpress_locations_service_staff_pricing_details,
                        array(
                            'bookingpress_service_staff_pricing_id' => $location_service_staff_id
                        )
                    );
                }
            }

            if( !empty($bookingpress_location_form_service_details) ){
                //$wpdb->delete( $tbl_bookingpress_locations_service_staff_pricing_details, array( 'bookingpress_location_id' => $bookingpress_update_id ) );
                //Assign staffmember if not assigned
                foreach($bookingpress_location_form_service_details as $bookingpress_location_service_details_key => $bookingpress_location_service_details_val){
                    $bookingpress_assign_service_and_staff_details_arr = array(
                        'service_id' => 0,
                        'staffmember_id' => 0,
                        'location_id' => $bookingpress_update_id,
                        'service_price' => 0,
                        'service_qty' => 0,
                        'service_min_qty' => 1,
                        'staffmember_price' => 0,
                        'staffmember_qty' => 0,
                        'staffmember_min_qty' => 1,
                    );

                    $service_staff_location_edit_id = !empty( $bookingpress_location_service_details_val['service_staff_location_id'] ) ? intval( $bookingpress_location_service_details_val['service_staff_location_id'] ) : 0;

                    $bookingpress_location_service_id = intval( $bookingpress_location_service_details_val['service_id'] );
                    /* $bookingpress_tmp_staff_id = intval( $bookingpress_location_service_details_val['staffmember_id'] ); */

                    $bookingpress_service_capacity = !empty( $bookingpress_location_service_details_val['service_capacity'] ) ? intval( $bookingpress_location_service_details_val['service_capacity'] ) : 1;

                    $bookingpress_service_min_capacity = !empty( $bookingpress_location_service_details_val['service_min_capacity'] ) ? intval( $bookingpress_location_service_details_val['service_min_capacity'] ) : 1;

                    //$bpa_service_capacity = $;
                    $bpa_service_capacity = $bookingpress_pro_services->bookingpress_get_service_max_capacity($bookingpress_location_service_id);
                    $bpa_service_min_capacity = $bookingpress_pro_services->bookingpress_get_service_min_capacity($bookingpress_location_service_id);

                    $bookingpress_assign_location_to_service = array(
                        'bookingpress_service_id' => $bookingpress_location_service_id,
                        'bookingpress_location_id' => $bookingpress_update_id,
                        'bookingpress_staff_location_qty' => $bookingpress_service_capacity,
                        'bookingpress_staff_location_min_qty' => $bookingpress_service_min_capacity,
                        'bookingpress_service_qty' => $bookingpress_service_capacity,
                        'bookingpress_service_min_qty' => $bookingpress_service_min_capacity,
                    );

                    if( $bookingpress_pro_staff_members->bookingpress_check_staffmember_module_activation()  ){
                        
                        if(  !empty( $bookingpress_location_service_details_val['staffmember_data'] ) && is_array( $bookingpress_location_service_details_val['staffmember_data'] ) ){
                            $staffmember_data = $bookingpress_location_service_details_val['staffmember_data'];
                            $staffmember_ids = array();
                            foreach( $staffmember_data as $staff_data ){
                                $staffmember_ids[] = $staff_data['staffmember_id'];
                            }
                            $bookingpress_assign_location_to_service['bookingpress_staffmember_id'] = implode( ',', $staffmember_ids );
                            $where = '';
                            if(!empty($bookingpress_assign_location_to_service['bookingpress_staffmember_id'])){
                                $where = " bookingpress_staffmember_id IN (".$bookingpress_assign_location_to_service['bookingpress_staffmember_id'].") AND ";
                            }
                            $get_staff_service = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers_services} WHERE ".$where." bookingpress_service_id = %d", $bookingpress_location_service_id )); // phpcs:ignore.
                            
                            if( !empty( $get_staff_service ) ){
                                $staffmember_row_id = $get_staff_service->bookingpress_staffmember_service_id;
                                $wpdb->update(
                                    $tbl_bookingpress_staffmembers_services,
                                    array(
                                        'bookingpress_service_capacity' => $bookingpress_service_capacity,
                                        'bookingpress_service_min_capacity' => $bookingpress_service_min_capacity,
                                    ),
                                    array(
                                        'bookingpress_staffmember_service_id' => $staffmember_row_id
                                    )
                                );
                            }
    
                        } else {
    
                            $bookingpress_staffmember_id = !empty( $bookingpress_location_service_details_val['staffmember_id'] ) ? intval( $bookingpress_location_service_details_val['staffmember_id'] ) : 0;
    
                            $bookingpress_assign_location_to_service['bookingpress_staffmember_id'] = implode(',',(array)$bookingpress_staffmember_id);
                            $where = '';
                            if(!empty($bookingpress_assign_location_to_service['bookingpress_staffmember_id'])){
                                $where = " bookingpress_staffmember_id IN (".$bookingpress_assign_location_to_service['bookingpress_staffmember_id'].") AND ";
                            }
                            $get_staff_service = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_staffmembers_services} WHERE ".$where." bookingpress_service_id = %d", $bookingpress_location_service_id )); // phpcs:ignore.
    
                            if( empty( $get_staff_service ) ){
                                $get_service_price = $wpdb->get_var( $wpdb->prepare( "SELECT bookingpress_service_price FROM {$tbl_bookingpress_services} WHERE bookingpress_service_id = %d", $bookingpress_location_service_id )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_services is table name defined globally.
    
                                foreach( (array)$bookingpress_staffmember_id as $bpa_staff_id ){
    
                                    $wpdb->insert(
                                        $tbl_bookingpress_staffmembers_services,
                                        array(
                                            'bookingpress_staffmember_id' => $bpa_staff_id,
                                            'bookingpress_service_id' => $bookingpress_location_service_id,
                                            'bookingpress_service_capacity' => $bookingpress_service_capacity,
                                            'bookingpress_service_min_capacity' => $bookingpress_service_min_capacity,
                                            'bookingpress_service_price' => $get_service_price
                                        )
                                    );
                                }
                            }   
                            
                        }
                    }


                    if( !empty( $service_staff_location_edit_id ) ){
                        $wpdb->update( $tbl_bookingpress_locations_service_staff_pricing_details, $bookingpress_assign_location_to_service, array( 'bookingpress_service_staff_pricing_id' => $service_staff_location_edit_id ) );
                    } else {
                        
                        $wpdb->insert( $tbl_bookingpress_locations_service_staff_pricing_details, $bookingpress_assign_location_to_service );
                    }
                }
            }
            
            echo wp_json_encode($response);
            die;
        }

        function set_css(){
            global $bookingpress_slugs;
			wp_register_style( 'bookingpress_location_css', BOOKINGPRESS_LOCATION_URL . '/css/bookingpress_location_admin.css', array(), BOOKINGPRESS_LOCATION_VERSION );
            if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), (array) $bookingpress_slugs ) ) {
				wp_enqueue_style( 'bookingpress_location_css' );

                if($_REQUEST['page'] == "bookingpress_location"){
                    wp_enqueue_style('bookingpress_tel_input');
                }
			}
        }

        function set_js(){
            global $bookingpress_slugs;
            if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), (array) $bookingpress_slugs ) ) {
                if($_REQUEST['page'] == "bookingpress_location"){
                    wp_enqueue_style('bookingpress_tel_input');
                }
                wp_enqueue_script('bookingpress_tel_input_js');
                wp_enqueue_script('bookingpress_tel_utils_js');
                wp_enqueue_script('bookingpress_sortable_js');
                wp_enqueue_script('bookingpress_draggable_js');
            }
        }

        function bookingpress_location_vue_data_fields_func(){
            global $bookingpress_location_vue_data_fields, $bookingpress_global_options;
            $bookingpress_options             = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_pagination          = $bookingpress_options['pagination'];
            $bookingpress_pagination_arr      = json_decode($bookingpress_pagination, true);
            $bookingpress_pagination_selected = $bookingpress_pagination_arr[0];

            $bookingpress_location_vue_data_fields = array(
                'location_bulk_action'       => 'bulk_action',
                'bulk_options'               => array(
                    array(
                        'value' => 'bulk_action',
                        'label' => __('Bulk Action', 'bookingpress-location'),
                    ),
                    array(
                        'value' => 'delete',
                        'label' => __('Delete', 'bookingpress-location'),
                    ),
                ),
                'loading'                    => false,
                'items'                      => array(),
                'multipleLocationSelection'          => array(),
                'perPage'                    => $bookingpress_pagination_selected,
                'totalItems'                 => 0,
                'pagination_selected_length' => $bookingpress_pagination_selected,
                'pagination_length'          => $bookingpress_pagination,
                'currentPage'                => 1,
                'pagination_length_val'      => '10',
                'pagination_val'             => array(
                    array(
                        'text'  => '10',
                        'value' => '10',
                    ),
                    array(
                        'text'  => '20',
                        'value' => '20',
                    ),
                    array(
                        'text'  => '50',
                        'value' => '50',
                    ),
                    array(
                        'text'  => '100',
                        'value' => '100',
                    ),
                    array(
                        'text'  => '200',
                        'value' => '200',
                    ),
                    array(
                        'text'  => '300',
                        'value' => '300',
                    ),
                    array(
                        'text'  => '400',
                        'value' => '400',
                    ),
                    array(
                        'text'  => '500',
                        'value' => '500',
                    ),
                ),
                'is_display_loader'          => '0',
                'is_disabled'                => false,
                'is_display_save_loader'     => '0',
                'is_multiple_checked'        => false,
                'open_location_modal'        => false,
                'locationShowFileList'        => false,
                'rules'                      => array(
                    'location_name'        => array(
                        array(
                            'required' => true,
                            'message'  => esc_html__('Please enter location name', 'bookingpress-location'),
                            'trigger'  => 'blur',
                        ),
                    ),
                ),
                'modal_loader'               => 1,
                'location'                    => array(
                    'location_image'          => '',
                    'location_image_name'     => '',
                    'location_images_list'    => array(),
                    'location_name'           => '',
                    'location_phone_country'  => '',
                    'location_phone_dial_code'  => '',
                    'location_phone_number'   => '',
                    'location_address'        => '',
                    'deleted_locations'        => array(),
                    'location_description'    => '',
                    'location_update_id'      => 0,
                ),
            );
        }

        function bookingpress_location_dynamic_helper_vars_func(){
            global $bookingpress_global_options;
			$bookingpress_options     = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_locale_lang = $bookingpress_options['locale'];
			?>
				var lang = ELEMENT.lang.<?php echo esc_html( $bookingpress_locale_lang ); ?>;
				ELEMENT.locale(lang)
			<?php
        }

        function bookingpress_location_dynamic_data_fields_func(){
            global $wpdb, $bookingpress_location_vue_data_fields, $BookingPress, $tbl_bookingpress_services, $bookingpress_pro_services, $tbl_bookingpress_staffmembers, $tbl_bookingpress_staffmembers_services, $bookingpress_services, $bookingpress_bring_anyone_with_you;

            $bookingpress_phone_country_option = $BookingPress->bookingpress_get_settings('default_phone_country_code', 'general_setting');
            $bookingpress_location_vue_data_fields['customer']['customer_phone_country'] = $bookingpress_phone_country_option;

            $bookingpress_location_vue_data_fields['bookingpress_tel_input_props'] = array(
                'defaultCountry' => $bookingpress_phone_country_option,
                'inputOptions' => array(
                    'placeholder' => '',
                ),
                'validCharactersOnly' => true,
            );

            $bookingpress_location_vue_data_fields['is_bring_anyone_with_you_enable'] = $bookingpress_bring_anyone_with_you->bookingpress_check_bring_anyone_module_activation();

            $bookingpress_allow_staff_to_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
            $bookingpress_location_vue_data_fields['staff_to_multiple_locations'] = $bookingpress_allow_staff_to_multiple_location;

            $bookingpress_location_service_list = $this->get_location_service_staff_details();            
            $bookingpress_location_vue_data_fields['location_services_list'] = $bookingpress_location_service_list;

            $bookingpress_location_specific_capacity_price_enabled = 'false';//$BookingPress->bookingpress_get_settings('location_specific_capacity_price', 'general_setting');
            $bookingpress_location_vue_data_fields['location_specific_capacity_price'] = $bookingpress_location_specific_capacity_price_enabled;
            
            $bookingpress_location_vue_data_fields['location_default_img_url'] = esc_html(BOOKINGPRESS_LOCATION_URL."/images/location-default-img.jpg");
            $bookingpress_location_vue_data_fields['items'] = array();

            $bookingpress_location_vue_data_fields['location_assigned_services'] = array();

            $bookingpress_location_vue_data_fields['open_assign_service_location_modal'] = false;

            $bookingpress_services_data = $BookingPress->get_bookingpress_service_data_group_with_category();
			if(!empty($bookingpress_services_data)){
				foreach($bookingpress_services_data as $k => $v){
					$bookingpress_category_services = !empty($v['category_services']) ? $v['category_services'] : array();
					if(!empty($bookingpress_category_services)){
						foreach($bookingpress_category_services as $k2 => $v2){
							$bookingpress_service_id = $v2['service_id'];
							$bookingpress_service_max_capacity = $bookingpress_pro_services->bookingpress_get_service_max_capacity($bookingpress_service_id);
							$bookingpress_services_data[$k]['category_services'][$k2]['service_max_capacity'] = $bookingpress_service_max_capacity;
							$bookingpress_services_data[$k]['category_services'][$k2]['service_price_without_currency'] =  $v2['service_price_without_currency'];
						}
					}
				}
			}

			$bookingpress_location_vue_data_fields['bookingpress_service_list'] = $bookingpress_services_data;

            $bookingpress_location_vue_data_fields['assign_location_service_form'] = array(
                'assign_service_id' => '',
                'assign_service_name' => '',
                'assign_service_capacity' => 1,
                'assign_service_min_capacity' => 1,
                'assign_service_staffmember' => '',
                'is_edit_location_service' => 0,
                'assigned_service_list' => array(),
                'assigned_staffmember_list' => array(
                    'staffmember_id' => '',
                    'staffmember_name' => ''
                )
            );

            $bookingpress_location_vue_data_fields = apply_filters( 'bookingpress_modify_location_vue_fields_data', $bookingpress_location_vue_data_fields );

            echo wp_json_encode($bookingpress_location_vue_data_fields);
        }

        function bookingpress_location_on_load_methods_func(){
            ?>
                this.loadLocations();
            <?php
        }

        function bookingpress_location_vue_methods_func(){
            global $bookingpress_notification_duration;
            ?>
                async loadLocations() {
                    const vm = this
                    vm.is_display_loader = '1'
                    //var bookingpress_search_data = { 'selected_category_id': this.search_service_category, 'service_name': this.search_service_name }
                    var bookingpress_search_data = { }
                    var postData = { action:'bookingpress_get_locations', perpage:this.perPage, currentpage:this.currentPage, search_data: bookingpress_search_data, _wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' };
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
                    .then( function (response) {
                        vm.is_display_loader = '0'
                        this.items = response.data.items;
                        this.totalItems = response.data.total;
                        setTimeout(function(){
                            vm.bookingpress_remove_focus_for_popover();
                        },1000);
                    }.bind(this) )
                    .catch( function (error) {
                        vm2.is_display_loader = '0'
                        console.log(error);
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    });
                },
                open_add_location_modal(action = 'add') {
                    const vm = this;
                    vm.open_location_modal = true;
                    if(action ==  'add') {
                        vm.modal_loader = 0
                    }
                    <?php do_action('bookingpress_open_location_modal_after'); ?>
                },
                closeLocationModal(){
                    const vm = this;

                    vm.$refs['location'].resetFields()
                    vm.open_location_modal = false;

                    vm.location.location_image = "";
                    vm.location.location_image_name = "";
                    vm.location.location_name = "";
                    vm.location.location_images_list = [];
                    vm.location.location_phone_country = "";
                    vm.location.location_phone_dial_code = "";
                    vm.location.location_phone_number = "";
                    vm.location.location_address = "";
                    vm.location.location_description = "";
                    vm.location.location_update_id = 0;
                    vm.location_assigned_services = [];
                },
                bookingpress_upload_location_func(response, file, fileList){
                    const vm2 = this
                    if(response != ''){
                        vm2.location.location_image = response.upload_url
                        vm2.location.location_image_name = response.upload_file_name
                    }
                },
                bookingpress_image_upload_limit(files, fileList){
                    const vm2 = this
                    if(vm2.location.location_image != ''){
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Multiple files not allowed', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    }
                },
                bookingpress_image_upload_err(err, file, fileList){
                    const vm2 = this
                    var bookingpress_err_msg = '<?php esc_html_e('Something went wrong', 'bookingpress-location'); ?>';
                    if(err != '' || err != undefined){
                        bookingpress_err_msg = err
                    }
                    vm2.$notify({
                        title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                        message: bookingpress_err_msg,
                        type: 'error',
                        customClass: 'error_notification',
                        duration:<?php echo intval($bookingpress_notification_duration); ?>,
                    });
                },
                bookingpress_remove_location_img(){
                    const vm2 = this
                    var upload_url = vm2.location.location_image
                    var upload_filename = vm2.location.location_image_name

                    var postData = { action:'bookingpress_remove_location_file', upload_file_url: upload_url,_wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' };
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
                    .then( function (response) {
                        vm2.location.location_image = ''
                        vm2.location.location_image_name = ''
                        vm2.$refs.avatarRef.clearFiles()
                    }.bind(vm2) )
                    .catch( function (error) {
                        console.log(error);
                    });
                },
                checkUploadedFile(file){
                    const vm2 = this
                    if(file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/webp'){
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Please upload jpg/png file only', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });                    
                        return false
                    }else{
                        var bpa_image_size = parseInt(file.size / 1000000);
                        if(bpa_image_size > 1){
                            vm2.$notify({
                                title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                message: '<?php esc_html_e('Please upload maximum 1 MB file only', 'bookingpress-location'); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });                    
                            return false
                        }
                    }
                },
                bookingpress_phone_country_change_func(bookingpress_country_obj){
                    const vm = this
                    var bookingpress_selected_country = bookingpress_country_obj.iso2
                    let exampleNumber = window.intlTelInputUtils.getExampleNumber( bookingpress_selected_country, true, 1 );
                    if( '' != exampleNumber ){
                        vm.bookingpress_tel_input_props.inputOptions.placeholder = exampleNumber;
                    }
                    vm.location.location_phone_country = bookingpress_selected_country
                    vm.location.location_phone_dial_code = bookingpress_country_obj.dialCode;
                },
                bookingpress_save_location(){
                    const vm = this;
                    this.$refs["location"].validate((valid) => {
                        if (valid) {
                            var postdata = [];
                            postdata.location_details = vm.location;
                            postdata.action = 'bookingpress_add_location';
                            vm.is_disabled = true
                            vm.is_display_save_loader = '1'
                            vm.savebtnloading = true
                            postdata.location_service_details = vm.location_assigned_services;
                            <?php do_action('bookingpress_add_location_more_postdata'); ?>
                            postdata._wpnonce = '<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>';
                            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
                            .then(function(response){
                                if(response.data.variant != 'error'){
                                    vm.closeLocationModal();
                                    vm.loadLocations();
                                }
                                vm.is_disabled = false
                                vm.is_display_save_loader = '0'                            
                                vm.$notify({
                                    title: response.data.title,
                                    message: response.data.msg,
                                    type: response.data.variant,
                                    customClass: response.data.variant+'_notification',
                                    duration:<?php echo intval($bookingpress_notification_duration); ?>,
                                });
                                vm.savebtnloading = false
                            }).catch(function(error){
                                console.log(error);
                                vm.$notify({
                                    title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                    message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                                    type: 'error',
                                    customClass: 'error_notification',
                                    duration:<?php echo intval($bookingpress_notification_duration); ?>,
                                });
                            });
                        } else {
                            return false;
                        }
                    });
                },
                updateLocationPosition( currentElement ){
                    var new_index = currentElement.newIndex;
                    var old_index = currentElement.oldIndex;
                    var service_id = currentElement.item.dataset.service_id;
                    const vm = this;
                    var postData = { action: 'bookingpress_position_location', old_position: old_index, new_position: new_index, currentPage : this.currentPage, perPage: this.perPage,_wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' };
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify(     postData ) )
                    .then(function(response){
                        
                    }).catch(function(error){
                        console.log(error);
                        vm.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    });
                },
                handleLocationSelectionChange( e, isChecked, location_id ){
                    const vm = this                                
                    vm.location_bulk_action = 'bulk_action';
                    if(isChecked){
                        vm.multipleLocationSelection.push(location_id);
                    }else{
                        var removeIndex = vm.multipleLocationSelection.indexOf(location_id);
                        if(removeIndex > -1){
                            vm.multipleLocationSelection.splice(removeIndex, 1);
                        }
                    }
                    if( vm.multipleLocationSelection.length == vm.totalItems ){
                        vm.is_multiple_checked = true;
                    } else {
                        vm.is_multiple_checked = false;
                    }
                },
                clearBulkLocationSelection(){
                    const vm = this
                    vm.location_bulk_action = 'bulk_action';
                    vm.multipleLocationSelection = []
                    vm.items.forEach(function(selectedVal, index, arr) {            
                        selectedVal.selected = false;
                    })
                    vm.is_multiple_checked = false;
                },
                selectAllLocations( isChecked ){
                    const vm = this                
                    let selected_location_parent = '';
                    if( isChecked ){
                        vm.items.forEach( ( selectedVal, index ) =>{
                            if( selectedVal.location_bulk_action == false) {
                                vm.multipleLocationSelection.push(selectedVal.bookingpress_location_id);
                                selectedVal.selected = true;
                            }
                        });
                    } else {
                        vm.clearBulkLocationSelection();
                    }
                },
               
                handleSelectionChange(val) {
					this.multipleLocationSelection = val;
					this.bulk_action = 'bulk_action';
				},
				handleSizeChange(val) {
					this.perPage = val
					this.loadLocations()
				},
				handleCurrentChange(val) {
					this.currentPage = val;
					this.loadLocations()
				},
				changeCurrentPage(perPage) {
					var total_item = this.totalItems;
					var recored_perpage = perPage;
					var select_page =  this.currentPage;				
					var current_page = Math.ceil(total_item/recored_perpage);
					if(total_item <= recored_perpage ) {
						current_page = 1;
					} else if(select_page >= current_page ) {
						
					} else {
						current_page = select_page;
					}
					return current_page;
				},
				changePaginationSize(selectedPage) { 	
					var total_recored_perpage = selectedPage;
					var current_page = this.changeCurrentPage(total_recored_perpage);										
					this.perPage = selectedPage;					
					this.currentPage = current_page;	
					this.loadLocations()
				},
                clearBulkAction(){
                    const vm = this
                    vm.bulk_action = 'bulk_action';
                    vm.multipleLocationSelection = []
                    vm.items.forEach(function(selectedVal, index, arr) {            
                        selectedVal.selected = false;
                    })
                    vm.is_multiple_checked = false;
                },
                editLocation(edit_id){
                    const vm2 = this
                    vm2.location.location_update_id = edit_id
                    vm2.open_add_location_modal('edit');
                    var location_action = { action: 'bookingpress_get_edit_location', edit_id: edit_id, _wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' }
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( location_action ) )
                    .then(function(response){
                        if(response.data.variant == 'success'){
                            vm2.location.location_image = response.data.edit_data.bookingpress_location_img_url;
                            vm2.location.location_image_name = response.data.edit_data.bookingpress_location_img_name;
                            vm2.location.location_name = response.data.edit_data.bookingpress_location_name;
                            vm2.location.location_phone_country = response.data.edit_data.bookingpress_location_phone_country;
                            vm2.location.location_phone_number = response.data.edit_data.bookingpress_location_phone_number;
                            vm2.location.location_phone_dial_code = response.data.edit_data.bookingpress_location_dial_code;
                            vm2.location.location_address = response.data.edit_data.bookingpress_location_address;
                            vm2.location.location_description = response.data.edit_data.bookingpress_location_description;

                            vm2.location_assigned_services = response.data.edit_data.assigned_service_details;
                        } else {
                            vm2.$notify({
                                title: response.data.title,
                                message: response.data.msg,
                                type: response.data.variant,
                                customClass: response.data.variant+'_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                        }
                        <?php do_action('bookingpress_edit_location_more_vue_data'); ?>
                    }).catch(function(error){
                        console.log(error)
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    });
                },
                deleteLocation(delete_id){
                    const vm2 = this
                    var location_action = { action: 'bookingpress_delete_location', delete_id: delete_id,_wpnonce:'<?php echo esc_html(wp_create_nonce('bpa_wp_nonce')); ?>' }
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( location_action ) )
                    .then(function(response){
                        vm2.$notify({
                            title: response.data.title,
                            message: response.data.msg,
                            type: response.data.variant,
                            customClass: response.data.variant+'_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        vm2.loadLocations()
                    }).catch(function(error){
                        console.log(error)
                        vm2.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    });
                },
                bulk_actions_location(){
                    const vm = this;
                    
                    if( "bulk_action" == vm.location_bulk_action ){
                        vm.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Please select any action...', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                    } else if( "delete" == vm.location_bulk_action ){
                        if( 0 < this.multipleLocationSelection.length ){
                            let location_ids = [];
                            this.multipleLocationSelection.forEach( (element,index) =>{
                                let location_id = element;
                                location_ids.push( location_id );
                            });
                            location_ids = JSON.stringify( location_ids );
                            let location_delete_data = {
                                action: 'bookingpress_bulk_location',
                                delete_ids: location_ids,
                                bulk_action: 'delete',
                                _wpnonce: '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce') ); ?>'
                            };
                            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( location_delete_data ) )
                            .then( function(response){
                                vm.$notify({
                                    title: response.data.title,
                                    message: response.data.msg,
                                    type: response.data.variant,
                                    customClass: response.data.variant+'_notification',
                                    duration:<?php echo intval($bookingpress_notification_duration); ?>,

                                });
                                vm.loadLocations();
                                vm.multipleSelection = [];
                                vm.totalItems = vm.items.length
                            }).catch( function(error){
                                console.log(error);
                                vm.$notify({
                                    title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                    message: '<?php esc_html_e('Something went wrong..', 'bookingpress-location'); ?>',
                                    type: 'error',
                                    customClass: 'error_notification',
                                    duration:<?php echo intval($bookingpress_notification_duration); ?>,
                                });
                            });
                        }

                    } else {

                    }
                },
                bookingpress_location_add_service_model( currentElement ){
                    const vm = this;

                    vm.open_assign_service_location_modal = true;
                    if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
                        vm.bpa_adjust_popup_position( currentElement, 'div#assign_location_service .el-dialog.bpa-dialog-assign-service__is-location' );
                    }
                },
                bookingpress_set_assign_staffmember( selected_value ){
                    const vm = this;
                    
                    let selected_service_details = vm.location_services_list[ selected_value ];
                    
                    if( "undefined" != typeof selected_service_details ){
                        let capacity = selected_service_details.service_max_capacity;
                        let min_capacity = selected_service_details.service_min_capacity;
                        vm.assign_location_service_form.assign_service_name = selected_service_details.service_name;
                        vm.assign_location_service_form.assign_service_capacity = capacity;
                        vm.assign_location_service_form.assign_service_min_capacity = min_capacity;
                        vm.assign_location_service_form.assigned_staffmember_list = selected_service_details.staffmembers;
                    }
                },
                async bookingpress_save_assign_location_service(){
                    const vm = this;
                    let service_form = vm.assign_location_service_form;
                    let error = 0;
                    if( "" == service_form.assign_service_id ){
                        vm.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Please select service', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        return false;
                    }

                    if( 1 > service_form.assign_service_capacity ){
                        vm.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Please set capacity', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        return false;
                    }

                    if( "undefined" == typeof service_form.assign_service_min_capacity ){
                        vm.$notify({
                            title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                            message: '<?php esc_html_e('Please set min capacity', 'bookingpress-location'); ?>',
                            type: 'error',
                            customClass: 'error_notification',
                            duration:<?php echo intval($bookingpress_notification_duration); ?>,
                        });
                        return false;
                    }

                    if( vm.is_bring_anyone_with_you_enable == 1 ){
                        if( service_form.assign_service_min_capacity > service_form.assign_service_capacity ){

                            vm.$notify({
                                title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                message: '<?php esc_html_e('Service min capacity should not be greater than max capacity.', 'bookingpress-location'); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                            return false;
                        }
                    }

                    let check_for_multiple_service = true;

                    <?php do_action( 'bookingpress_save_assign_location_validation_outside' ); ?>


                    if( service_form.is_edit_location_service == false ){
                        let new_assigned_service = {};
                        new_assigned_service.service_id = service_form.assign_service_id;
                        new_assigned_service.service_name = service_form.assign_service_name;
                        new_assigned_service.service_capacity = service_form.assign_service_capacity;
                        new_assigned_service.service_min_capacity = service_form.assign_service_min_capacity;
                        
                        <?php do_action( 'bookingpress_save_assigned_location_service_staff_for_add_location') ; ?>

                        vm.location_assigned_services.push(new_assigned_service);
                    } else {
                        let edit_location_index = service_form.edit_location_service_index;
                        vm.location_assigned_services[ edit_location_index ].service_capacity = service_form.assign_service_capacity;
                        vm.location_assigned_services[ edit_location_index ].service_min_capacity = service_form.assign_service_min_capacity;
                        vm.location_assigned_services[ edit_location_index ].service_id = service_form.assign_service_id;
                        vm.location_assigned_services[ edit_location_index ].service_name = service_form.assign_service_name;

                        <?php do_action( 'bookingpress_save_assigned_location_service_staff_for_edit_location' ); ?>
                    }

                    vm.bookingpress_close_assign_location_modal();
                },
                bookingpress_close_assign_location_modal(){
                    const vm = this;
                    vm.assign_location_service_form.assign_service_id = "";
                    vm.assign_location_service_form.assign_service_capacity = 1;
                    vm.assign_location_service_form.assign_service_min_capacity = 1;
                    vm.assign_location_service_form.assign_service_name = "";
                    vm.assign_location_service_form.is_edit_location_service = false;
                    <?php do_action( 'bookingpress_reset_assign_service_dynamic_data_onclose'); ?>
                    vm.open_assign_service_location_modal = false;
                },
                async bookingpress_check_staff_with_location( staff_id, service_id, location_id ){
                    const vm = this;
                    var postData = {
                        "action": "bookingpress_check_staff_with_different_location",
                        "staffmember_id": staff_id,
                        "service_id": service_id,
                        "location_id": location_id
                    };
                    return axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postData ) )
                    .then( function(response){
                        if( response.data == "error" ){
                            return false;
                        } else {
                            return true;
                        }
                    }.bind(this))
                    .catch( function(error){
                        console.log( error )
                    });

                    return false;
                },
                bookingpress_edit_assigned_location_service( location_service_id, currentElement ){
                    const vm = this;
                    let location_service_data = vm.location_assigned_services;
                    for( let index in location_service_data ){
                        let elm = location_service_data[ index ];
                        if( elm.service_staff_location_id == location_service_id ){
                            vm.assign_location_service_form.assign_service_id = elm.service_id;
                            vm.assign_location_service_form.assign_service_capacity = elm.service_capacity;
                            vm.assign_location_service_form.assign_service_min_capacity = elm.service_min_capacity;
                            vm.assign_location_service_form.assign_service_name = elm.service_name;
                            vm.assign_location_service_form.assign_service_staffmember = parseInt( elm.staffmember_id );
                            vm.assign_location_service_form.assigned_staffmember_list = vm.location_services_list[ elm.service_id ].staffmembers;
                            vm.assign_location_service_form.is_edit_location_service = true;
                            vm.assign_location_service_form.edit_location_service_id = location_service_id;
                            vm.assign_location_service_form.edit_location_service_index = index;
                            break;
                        }
                    }
                    vm.open_assign_service_location_modal = true;
                    if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
                        vm.bpa_adjust_popup_position( currentElement, 'div#assign_location_service .el-dialog.bpa-dialog-assign-service__is-location' );
                    }
                },
                bookingpress_delete_assigned_location_service( location_service_id ){
                    const vm = this;
                    let location_service_data = vm.location_assigned_services;
                    
                    for( let index in location_service_data ){
                        let elm = location_service_data[ index ];
                        if( elm.service_staff_location_id == location_service_id ){
                            vm.location_assigned_services.splice( index, 1 );
                            vm.location.deleted_locations.push( location_service_id );
                        }
                    }
                },
            <?php
            do_action('bookingpress_add_location_dynamic_vue_methods');
        }

        function bookingpress_load_coupons_view_func() {
			$bookingpress_load_file_name = BOOKINGPRESS_LOCATION_VIEWS_DIR . '/manage_location.php';
			require $bookingpress_load_file_name;
		}

        function bookingpress_location_page_slugs(){
            global $bookingpress_slugs;
            $bookingpress_slugs->bookingpress_location = 'bookingpress_location';
        }

        function bookingpress_add_specific_menu_func($bookingpress_slugs){
            global $BookingPress;

            add_submenu_page( $bookingpress_slugs->bookingpress, __( 'Locations', 'bookingpress-location' ), __( 'Locations', 'bookingpress-location' ), 'bookingpress_location', $bookingpress_slugs->bookingpress_location, array( $BookingPress, 'route' ) );
        }

        public static function install(){
            global $wpdb, $bookingpress_location_version, $BookingPress;
            $bookingpress_location_tmp_version = get_option('bookingpress_location_version');

            //Update booking form sequence
            $tbl_bookingpress_customize_settings   = $wpdb->prefix . 'bookingpress_customize_settings';
            $booking_form_sequence_settings = $BookingPress->bookingpress_get_customize_settings('bookingpress_form_sequance', 'booking_form');

            
            $bookingpress_booking_form_customize_setting = array(
                'bookingpress_location_information'	=> 2,
                'bpa_location_title_summay'	=> __('Location', 'bookingpress-location'),
                'cart_location_title' => __('Location','bookingpress-location'),
            );
            foreach($bookingpress_booking_form_customize_setting as $key => $val){
                $bookingpress_bd_data = array(
                    'bookingpress_setting_name' => $key,
                    'bookingpress_setting_value' => $val,
                    'bookingpress_setting_type' => 'booking_form',
                );
                $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_bd_data);        
            }

            $bookingpress_mybooking_data = array(
                'bookingpress_setting_name' => 'location_title',
                'bookingpress_setting_value' => 'Location',
                'bookingpress_setting_type' => 'booking_my_booking',
            );
            $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_mybooking_data);

            $BookingPress->bookingpress_update_settings('no_appointment_location_selected_for_the_booking', 'message_setting', __('Please select any location to book an appointment. ','bookingpress-location'));
            
            if($booking_form_sequence_settings == '["service_selection","staff_selection"]'){
                $booking_form_sequence_settings = '["location_selection","service_selection","staff_selection"]';
                $wpdb->update($tbl_bookingpress_customize_settings, array('bookingpress_setting_value' => $booking_form_sequence_settings), array('bookingpress_setting_name' => 'bookingpress_form_sequance'));
            }else if($booking_form_sequence_settings == '["staff_selection","service_selection"]'){
                $booking_form_sequence_settings = '["location_selection","staff_selection", "service_selection"]';
                $wpdb->update($tbl_bookingpress_customize_settings, array('bookingpress_setting_value' => $booking_form_sequence_settings), array('bookingpress_setting_name' => 'bookingpress_form_sequance'));
            }

            if (!isset($bookingpress_location_tmp_version) || $bookingpress_location_tmp_version == '') {


                $myaddon_name = "bookingpress-location/bookingpress-location.php";
                
                // activate license for this addon
                $posted_license_key = trim( get_option( 'bkp_license_key' ) );
			    $posted_license_package = '22059';

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
                    $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-location' );
                } else {
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string = wp_remote_retrieve_body( $response );
                    if ( false === $license_data->success ) {
                        switch( $license_data->error ) {
                            case 'expired' :
                    			/* translators: the expiry date. */
                                $message = sprintf(__( 'Your license key expired on %s.','bookingpress-location' ),date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = __( 'Your license key has been disabled.','bookingpress-location' );
                                break;
                            case 'missing' :
                                $message = __( 'Invalid license.','bookingpress-location' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = __( 'Your license is not active for this URL.','bookingpress-location' );
                                break;
                            case 'item_name_mismatch' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-location');
                                break;
                            case 'invalid_item_id' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-location');
                                    break;
                            case 'no_activations_left':
                                $message = __( 'Your license key has reached its activation limit.','bookingpress-location' );
                                break;
                            default :
                                $message = __( 'An error occurred, please try again.','bookingpress-location' );
                                break;
                        }

                    }

                }

                if ( ! empty( $message ) ) {
                    update_option( 'bkp_location_license_data_activate_response', $license_data_string );
                    update_option( 'bkp_location_license_status', $license_data->license );
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Location Add-on', 'bookingpress-location');
                    /* translators: 1. Redirect URL link starts. 2. Redirect URL Link ends */
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-location'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');  // phpcs:ignore
                    die;
                }
                
                if($license_data->license === "valid")
                {
                    update_option( 'bkp_location_license_key', $posted_license_key );
                    update_option( 'bkp_location_license_package', $posted_license_package );
                    update_option( 'bkp_location_license_status', $license_data->license );
                    update_option( 'bkp_location_license_data_activate_response', $license_data_string );
                }





                update_option('bookingpress_location_version', $bookingpress_location_version);

                require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				@set_time_limit( 0 );

				$charset_collate = '';
				if ( $wpdb->has_cap( 'collation' ) ) {
					if ( ! empty( $wpdb->charset ) ) {
						$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
					}
					if ( ! empty( $wpdb->collate ) ) {
						$charset_collate .= " COLLATE $wpdb->collate";
					}
				}

                $tbl_bookingpress_locations = $wpdb->prefix.'bookingpress_locations';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations}`(
					`bookingpress_location_id` int(11) NOT NULL AUTO_INCREMENT,
					`bookingpress_location_name` varchar(255) NOT NULL,
					`bookingpress_location_phone_country` varchar(5) DEFAULT NULL,
					`bookingpress_location_phone_number` varchar(20) DEFAULT NULL,
					`bookingpress_location_address` varchar(255) DEFAULT NULL,
					`bookingpress_location_description` TEXT DEFAULT NULL,
					`bookingpress_location_img_name` TEXT DEFAULT NULL,
                    `bookingpress_location_img_url` TEXT DEFAULT NULL,
                    `bookingpress_location_position` INT(11) NOT NULL,
					`bookingpress_location_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`bookingpress_location_id`)
				) {$charset_collate}";
				$bookingpress_dbtbl_create[ $tbl_bookingpress_locations ] = dbDelta( $sql_table );

                $tbl_bookingpress_locations_service_staff_pricing_details = $wpdb->prefix.'bookingpress_locations_service_staff_pricing_details';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations_service_staff_pricing_details}`(
					`bookingpress_service_staff_pricing_id` int(11) NOT NULL AUTO_INCREMENT,
					`bookingpress_service_id` int(11) DEFAULT NULL,
                    `bookingpress_staffmember_id` varchar(255) DEFAULT NULL,
                    `bookingpress_location_id` int(11) DEFAULT NULL,
                    `bookingpress_staff_location_qty` INT(11) NOT NULL DEFAULT 1,
                    `bookingpress_staff_location_min_qty` INT(11) NOT NULL DEFAULT 1,
                    `bookingpress_service_qty` int(11) DEFAULT NULL,
                    `bookingpress_service_min_qty` int(11) NOT NULL DEFAULT 1,
					`bookingpress_location_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`bookingpress_service_staff_pricing_id`)
				) {$charset_collate}";
				$bookingpress_dbtbl_create[ $tbl_bookingpress_locations_service_staff_pricing_details ] = dbDelta( $sql_table );
                    
                $tbl_bookingpress_locations_service_workhours = $wpdb->prefix.'bookingpress_locations_service_workhours';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations_service_workhours}`(
                    `bookingpress_location_service_workhour_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `bookingpress_location_id` INT(11) NOT NULL,
                    `bookingpress_service_id` INT(11) NOT NULL,
                    `bookingpress_location_service_workday_key` VARCHAR(15) NOT NULL,
                    `bookingpress_location_service_workhour_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_workhour_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_workhour_is_break` TINYINT(1) NOT NULL DEFAULT 0,
                    `bookingpress_location_service_workhour_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`bookingpress_location_service_workhour_id`)
                ){$charset_collate}";
                $bookingpress_dbtbl_create[ $tbl_bookingpress_locations_service_workhours ] = dbDelta( $sql_table );

                $tbl_bookingpress_locations_staff_workhours = $wpdb->prefix.'bookingpress_locations_staff_workhours';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations_staff_workhours}`(
                    `bookingpress_location_staff_workhour_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `bookingpress_location_id` INT(11) NOT NULL,
                    `bookingpress_staffmember_id` INT(11) NOT NULL,
                    `bookingpress_location_staff_workday_key` VARCHAR(15) NOT NULL,
                    `bookingpress_location_staff_workhour_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_workhour_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_workhour_is_break` TINYINT(1) NOT NULL DEFAULT 0,
                    `bookingpress_location_staff_workhour_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`bookingpress_location_staff_workhour_id`)
                ){$charset_collate}";
                $bookingpress_dbtbl_create[ $tbl_bookingpress_locations_staff_workhours ] = dbDelta( $sql_table );

                $tbl_bookingpress_locations_service_special_days = $wpdb->prefix.'bookingpress_locations_service_special_days';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations_service_special_days}`(
                    `bookingpress_location_service_special_day_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `bookingpress_location_id` INT(11) NOT NULL,
                    `bookingpress_service_id` INT(11) NOT NULL,
                    `bookingpress_location_service_special_day_start_date` DATE DEFAULT NULL,
                    `bookingpress_location_service_special_day_end_date` DATE DEFAULT NULL,
                    `bookingpress_location_service_special_day_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_special_day_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_special_day_has_break` TINYINT(1) NOT NULL DEFAULT 0,
                    `bookingpress_location_service_special_day_break_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_special_day_break_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_service_special_day_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`bookingpress_location_service_special_day_id`)
                ){$charset_collate}";
                $bookingpress_dbtbl_create[ $tbl_bookingpress_locations_service_special_days ] = dbDelta( $sql_table );

                $tbl_bookingpress_locations_staff_special_days = $wpdb->prefix.'bookingpress_locations_staff_special_days';
                $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_locations_staff_special_days}`(
                    `bookingpress_location_staff_special_day_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `bookingpress_location_id` INT(11) NOT NULL,
                    `bookingpress_staffmember_id` INT(11) NOT NULL,
                    `bookingpress_location_staff_special_day_service_id` VARCHAR(255) DEFAULT NULL,
                    `bookingpress_location_staff_special_day_start_date` DATE DEFAULT NULL,
                    `bookingpress_location_staff_special_day_end_date` DATE DEFAULT NULL,
                    `bookingpress_location_staff_special_day_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_special_day_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_special_day_has_break` TINYINT(1) NOT NULL DEFAULT 0,
                    `bookingpress_location_staff_special_day_break_start_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_special_day_break_end_time` TIME DEFAULT NULL,
                    `bookingpress_location_staff_special_day_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`bookingpress_location_staff_special_day_id`)
                ){$charset_collate}";
                $bookingpress_dbtbl_create[ $tbl_bookingpress_locations_staff_special_days ] = dbDelta( $sql_table );

                $BookingPress->bookingpress_update_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting', 'false');
                //$BookingPress->bookingpress_update_settings('location_specific_capacity_price', 'general_setting', 'false');

                //Add column location id to service workhour table
                /* $tbl_bookingpress_service_workhours = $wpdb->prefix . 'bookingpress_service_workhours';
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_service_workhours} ADD bookingpress_location_id int(11) DEFAULT 0 AFTER bookingpress_service_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_service_workhours is a table name. false alarm */

                //Add column location id to staffmember workhour table
                /* $tbl_bookingpress_staff_member_workhours = $wpdb->prefix . 'bookingpress_staff_member_workhours';
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_staff_member_workhours} ADD bookingpress_location_id int(11) DEFAULT 0 AFTER bookingpress_staffmember_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staff_member_workhours is a table name. false alarm */

                //Add Location title in customize settings
                $wpdb->insert($tbl_bookingpress_customize_settings, array(
                    'bookingpress_setting_name' => 'location_title',
                    'bookingpress_setting_value' => __('Location', 'bookingpress-location'),
                    'bookingpress_setting_type' => 'booking_form',
                ));

                //Add columns to entries table for store appointment related location details
                $tbl_bookingpress_entries = $wpdb->prefix . 'bookingpress_entries';
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_location_id int(11) DEFAULT 0 AFTER bookingpress_complete_payment_token" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_location_service_price float DEFAULT 0 AFTER bookingpress_location_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_location_service_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_service_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_location_staff_price float DEFAULT 0 AFTER bookingpress_location_service_capacity" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_location_staff_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_staff_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_entries is a table name. false alarm


                //Add columns to appointment booking table for store appointment related location details
                $tbl_bookingpress_appointment_bookings = $wpdb->prefix . 'bookingpress_appointment_bookings';
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_location_id int(11) DEFAULT 0 AFTER bookingpress_complete_payment_token" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_location_service_price float DEFAULT 0 AFTER bookingpress_location_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_location_service_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_service_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_location_staff_price float DEFAULT 0 AFTER bookingpress_location_service_capacity" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_location_staff_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_staff_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_appointment_bookings is a table name. false alarm


                //Add columns to payment table for store payment related location details
                $tbl_bookingpress_payment_logs         = $wpdb->prefix . 'bookingpress_payment_transactions';
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_location_id int(11) DEFAULT 0 AFTER bookingpress_complete_payment_token" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_location_service_price float DEFAULT 0 AFTER bookingpress_location_id" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_location_service_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_service_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_location_staff_price float DEFAULT 0 AFTER bookingpress_location_service_capacity" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_location_staff_capacity SMALLINT DEFAULT 0 AFTER bookingpress_location_staff_price" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
            }

            //Assign capabilities to all admin users
            $args  = array(
                'role'   => 'administrator',
                'fields' => 'id',
            );
            $users = get_users($args);

            if (count($users) > 0 ) {
                $bookingpressroles = array(
                    'bookingpress_location' => esc_html__('Location', 'bookingpress-location')
                );
                foreach ( $users as $key => $user_id ) {
                    $userObj           = new WP_User($user_id);
                    foreach ( $bookingpressroles as $bookingpressrole => $bookingpress_roledescription ) {
                        $userObj->add_cap($bookingpressrole);
                    }
                }
                    unset($bookingpressrole);
                    unset($bookingpressroles);
                    unset($bookingpress_roledescription);
            }
        }

        function bookingpress_location_add_capabilities_to_new_user($user_id){
            if ($user_id == '') {
                return;
            }
            if (user_can($user_id, 'administrator')) {
                $bookingpressroles = array(
                    'bookingpress_location' => esc_html__('Location', 'bookingpress-location')
                );
                $userObj = new WP_User($user_id);
                foreach ($bookingpressroles as $bookingpress_role => $bookingpress_role_desc) {
                    $userObj->add_cap($bookingpress_role);
                }
                unset($bookingpress_role);
                unset($bookingpress_roles);
                unset($bookingpress_role_desc);
            }
        }

        function bookingpress_location_assign_caps_on_role_change( $user_id, $role, $old_roles ){
            global $BookingPress;
            if(!empty($user_id) && $role == "administrator"){
                $bookingpressroles = array(
                    'bookingpress_location' => esc_html__('Location', 'bookingpress-location')
                );
                $userObj = new WP_User($user_id);
                foreach ($bookingpressroles as $bookingpress_role => $bookingpress_role_desc) {
                    $userObj->add_cap($bookingpress_role);
                }
                unset($bookingpress_role);
                unset($bookingpress_roles);
                unset($bookingpress_role_desc);
            }
        }

        public static function uninstall(){
            delete_option('bookingpress_location_version');

            delete_option( 'bkp_location_license_key');
            delete_option( 'bkp_location_license_package');
            delete_option( 'bkp_location_license_status' );
            delete_option( 'bkp_location_license_data_activate_response');


            $args  = array(
                'role'   => 'administrator',
                'fields' => 'id',
            );
            $users = get_users($args);
            if (count($users) > 0 ) {
                $bookingpressroles = array('bookingpress_location' => esc_html__('Location', 'bookingpress-location'));
                foreach ( $users as $key => $user_id ) {
                    $userObj           = new WP_User($user_id);
                    foreach ( $bookingpressroles as $bookingpressrole => $bookingpress_roledescription ) {
                        if($userObj->has_cap($bookingpressrole)){
                            $userObj->remove_cap($bookingpressrole, true);
                        }
                    }
                }
            }
        }

        public function is_addon_activated(){
            $bookingpress_location_version = get_option('bookingpress_location_version');
            return !empty($bookingpress_location_version) ? 1 : 0;
        }
    }
    
	global $bookingpress_location;
	$bookingpress_location = new bookingpress_location();
}