<?php

if (!class_exists('bookingpress_google_captcha')) {
	class bookingpress_google_captcha {
		function __construct() {
            register_activation_hook(BOOKINGPRESS_GOOGLE_CAPTCHA_DIR.'/bookingpress-google-captcha.php', array('bookingpress_google_captcha', 'install'));
            register_uninstall_hook(BOOKINGPRESS_GOOGLE_CAPTCHA_DIR.'/bookingpress-google-captcha.php', array('bookingpress_google_captcha', 'uninstall'));

            add_action( 'admin_notices', array( $this, 'bookingpress_google_captcha_admin_notices') );
            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')){
                add_filter('bookingpress_add_setting_dynamic_data_fields', array($this, 'bookingpress_add_setting_dynamic_data_fields_func'), 11);

                add_filter('bookingpress_customize_add_dynamic_data_fields',array($this,'bookingpress_customize_add_dynamic_data_fields_func'));

                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this,'bookingpress_get_booking_form_customize_data_filter_func'));
                add_filter('bookingpress_frontend_apointment_form_add_dynamic_data', array($this, 'bookingpress_frontend_apointment_form_add_dynamic_data_func'), 10, 1);

                add_filter('bookingpress_add_appointment_booking_on_load_methods',array($this,'bookingpress_add_appointment_booking_on_load_methods_func')) ;

                add_action('bookingpress_validate_booking_form', array($this, 'bookingpress_validate_google_cpatcha'), 11, 1);

                add_filter('bookingpress_before_book_appointment',array($this,'bookingpress_regenerate_google_captcha'));

                add_filter('bookingpress_add_appointment_booking_vue_methods', array($this, 'bookingpress_add_appointment_booking_vue_methods_func'), 11, 1);
                add_action('bookingpress_add_field_after_appointment_booking_form',array($this,'bookingpress_add_field_after_appointment_booking_form_func'));            

                add_action('bookingpress_add_integration_settings_section',array($this,'bookingpress_add_integration_settings_section_func'));
                add_filter('bookingpress_available_integration_addon_list',array($this,'bookingpress_available_integration_addon_list_func'));

                add_action( 'bookingpress_load_integration_settings_data', array( $this, 'bookingpress_load_integration_settings_data_func' ) );

                add_filter('bookingpress_addon_list_data_filter',array($this,'bookingpress_addon_list_data_filter_func'));

                add_action('bookingpress_add_frontend_js',array($this,'bookingpress_recaptcha_add_js'));

                add_action('bookingpress_modify_readmore_link', array($this, 'bookingpress_modify_readmore_link_func'), 14);

                //add action for need help link
                add_action( 'bpa_add_extra_tab_outside_func', array( $this,'bpa_add_extra_tab_outside_func_arr'));
			}
            
	        add_action( 'admin_init', array( $this, 'bookingpress_upgrade_google_captcha_data' ) );
	    
            add_action('activated_plugin',array($this,'bookingpress_is_google_captcha_addon_activated'),11,2);
		}
	function bpa_add_extra_tab_outside_func_arr(){ ?>

            var bpa_get_setting_tab = bpa_get_url_param.get('setting_tab');
            if( bpa_get_page == 'bookingpress_settings'){
                
                if( selected_tab_name == 'integration_settings' && vm.bpa_integration_active_tab == 'google_captcha'){
                    vm.openNeedHelper("list_google_captcha_settings", "google_captcha_settings", "Google captcha Settings");
                    vm.bpa_fab_floating_btn = 0;

                } else if ( null == selected_tab_name && 'integration_settings' == bpa_get_setting_page && 'google_captcha' == bpa_get_setting_tab ){
                    vm.openNeedHelper("list_google_captcha_settings", "google_captcha_settings", "Google captcha Settings");
                    vm.bpa_fab_floating_btn = 0;
                }
            }

        <?php }
        function bookingpress_modify_readmore_link_func(){
            ?>
                var selected_tab = sessionStorage.getItem("current_tabname");
                if(selected_tab == "integration_settings"){
                    if(vm.bpa_integration_active_tab == ""){
                        read_more_link = "https://www.bookingpressplugin.com/documents/google-recaptcha-integration/";
                    }
                    if(vm.bpa_integration_active_tab == "google_captcha"){
                        read_more_link = "https://www.bookingpressplugin.com/documents/google-recaptcha-integration/";
                    }
                }
            <?php
        }
	
	    function bookingpress_upgrade_google_captcha_data(){
            global $BookingPress, $bookingpress_google_captcha_version;
            $bookingpress_db_gc_version = get_option( 'bookingpress_google_captcha_version' );

            if( version_compare( $bookingpress_db_gc_version, '1.3', '<' ) ){
                $bookingpress_load_gc_update_file = BOOKINGPRESS_GOOGLE_CAPTCHA_DIR . '/core/views/upgrade_latest_google_captcha_data.php';
                include $bookingpress_load_gc_update_file;
                $BookingPress->bookingpress_send_anonymous_data_cron();
            }
        }
	
        function bookingpress_is_google_captcha_addon_activated($plugin,$network_activation)
        {              
            $myaddon_name = "bookingpress-google-captcha/bookingpress-google-captcha.php";

            if($plugin == $myaddon_name)
            {

                if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Google Captcha Add-on', 'bookingpress-google-captcha');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-google-captcha'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }

                $license = trim( get_option( 'bkp_license_key' ) );
                $package = trim( get_option( 'bkp_license_package' ) );

                if( '' === $license || false === $license ) 
                {
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Google Captcha Add-on', 'bookingpress-google-captcha');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-google-captcha'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                else
                {
                    $store_url = BOOKINGPRESS_GOOGLE_CAPTCHA_STORE_URL;
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
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Google Captcha Add-on', 'bookingpress-google-captcha');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-google-captcha'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }

                    }
                    else
                    {
                        deactivate_plugins($myaddon_name, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Google Captcha Add-on', 'bookingpress-google-captcha');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-google-captcha'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                }
            }
        }

        function bookingpress_recaptcha_add_js()
        {
            if (! wp_script_is('jquery', 'enqueued') ) {
                wp_enqueue_script('jquery');
            }
        }

        function bookingpress_addon_list_data_filter_func($bookingpress_body_res){
            global $bookingpress_slugs;
            if(!empty($bookingpress_body_res)) {
                foreach($bookingpress_body_res as $bookingpress_body_res_key =>$bookingpress_body_res_val) {
                    $bookingpress_setting_page_url = add_query_arg('page', $bookingpress_slugs->bookingpress_settings, esc_url( admin_url() . 'admin.php?page=bookingpress' ));
                    $bookingpress_config_url = add_query_arg('setting_page', 'integration_settings', $bookingpress_setting_page_url);
                    $bookingpress_config_url = add_query_arg('setting_tab', 'google_captcha', $bookingpress_config_url);
                    if($bookingpress_body_res_val['addon_key'] == 'bookingpress_google_captcha') {
                        $bookingpress_body_res[$bookingpress_body_res_key]['addon_configure_url'] = $bookingpress_config_url;
                    }
                }
            }
            return $bookingpress_body_res;
        } 

        public static function install(){
            global $wpdb, $tbl_bookingpress_customize_settings, $bookingpress_google_captcha_version,$BookingPress;                        
            $bookingpress_g_captcha_version = get_option('bookingpress_google_captcha_version');
            if (!isset($bookingpress_g_captcha_version) || $bookingpress_g_captcha_version == '') {
		
		$myaddon_name = "bookingpress-google-captcha/bookingpress-google-captcha.php";
		
                // activate license for this addon
                $posted_license_key = trim( get_option( 'bkp_license_key' ) );
			    $posted_license_package = '4869';

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
                    $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-google-captcha' );
                } else {
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                    $license_data_string = wp_remote_retrieve_body( $response );
                    if ( false === $license_data->success ) {
                        switch( $license_data->error ) {
                            case 'expired' :
                                $message = sprintf(
                                    __( 'Your license key expired on %s.','bookingpress-google-captcha' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;
                            case 'revoked' :
                                $message = __( 'Your license key has been disabled.','bookingpress-google-captcha' );
                                break;
                            case 'missing' :
                                $message = __( 'Invalid license.','bookingpress-google-captcha' );
                                break;
                            case 'invalid' :
                            case 'site_inactive' :
                                $message = __( 'Your license is not active for this URL.','bookingpress-google-captcha' );
                                break;
                            case 'item_name_mismatch' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-google-captcha');
                                break;
                            case 'invalid_item_id' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-google-captcha');
                                    break;
                            case 'no_activations_left':
                                $message = __( 'Your license key has reached its activation limit.','bookingpress-google-captcha' );
                                break;
                            default :
                                $message = __( 'An error occurred, please try again.','bookingpress-google-captcha' );
                                break;
                        }

                    }

                }

                if ( ! empty( $message ) ) {
                    update_option( 'bkp_google_captcha_license_data_activate_response', $license_data_string );
                    update_option( 'bkp_google_captcha_license_status', $license_data->license );
                    deactivate_plugins($myaddon_name, FALSE);
                    $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                    $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Google Captcha Add-on', 'bookingpress-google-captcha');
					$bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-google-captcha'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
					wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                    die;
                }
                
                if($license_data->license === "valid")
                {
                    update_option( 'bkp_google_captcha_license_key', $posted_license_key );
                    update_option( 'bkp_google_captcha_license_package', $posted_license_package );
                    update_option( 'bkp_google_captcha_license_status', $license_data->license );
                    update_option( 'bkp_google_captcha_license_data_activate_response', $license_data_string );
                }  

                update_option('bookingpress_google_captcha_version', $bookingpress_google_captcha_version);

                $bookingpress_get_customize_text = $BookingPress->bookingpress_get_customize_settings('enable_google_captcha', 'booking_form');
                if(empty($bookingpress_get_customize_text)){
                    $bookingpress_customize_settings_db_fields = array(
                        'bookingpress_setting_name'  => 'enable_google_captcha',
                        'bookingpress_setting_value' => 'false',
                        'bookingpress_setting_type'  => 'booking_form',
                    );

                    $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields);
                }
            }
        }

        public static function uninstall(){
            delete_option('bookingpress_google_captcha_version');


            delete_option('bkp_google_captcha_license_key');
            delete_option('bkp_google_captcha_license_package');
            delete_option('bkp_google_captcha_license_status');
            delete_option('bkp_google_captcha_license_data_activate_response');

        }
        public function is_addon_activated(){
            $bookingpress_captcha_version = get_option('bookingpress_google_captcha_version');
            return !empty($bookingpress_captcha_version) ? 1 : 0;
        }
        function bookingpress_add_field_after_appointment_booking_form_func($bookingpress_uniq_id){       
            global $BookingPress;                 
            $bookingpresss_enable_google_captcha = $BookingPress->bookingpress_get_customize_settings('enable_google_captcha', 'booking_form');                                           
            $google_captcha_site_key = $BookingPress->bookingpress_get_settings('google_captcha_site_key','google_captcha_setting');            
            if($bookingpresss_enable_google_captcha == 'true' && !empty($google_captcha_site_key)) {
                ?>
                <el-row>
                    <el-input type="hidden" name="bookingpress_booking_form_<?php echo $bookingpress_uniq_id; ?>" v-model="appointment_step_form_data.bookingpress_captcha_<?php echo $bookingpress_uniq_id; ?>" id="bookingpress_captcha_<?php echo $bookingpress_uniq_id; ?>"></el-input> 
                <el-row>
                <?php   
            }
        }        
     
        function bookingpress_regenerate_google_captcha($bookingpress_before_book_appointment_data) {
            $bookingpress_before_book_appointment_data .= 'const cdata = await this.bookingpress_reload_captcha();
                let updateData = JSON.parse( postData.appointment_data );
                for (let bookingpress_grecaptcha_field_v3 in window["bookingpress_recaptcha_v3"]) {
                    updateData[ bookingpress_grecaptcha_field_v3 ] = cdata;
                }
                postData.appointment_data = JSON.stringify( updateData );
            ';
            return $bookingpress_before_book_appointment_data;
        }
        
        

        function bookingpress_frontend_apointment_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){
            global $BookingPress;       
            $bookingpresss_enable_google_captcha = $BookingPress->bookingpress_get_customize_settings('enable_google_captcha', 'booking_form');       
            if($bookingpresss_enable_google_captcha == 'true') {
                $bookingpress_front_vue_data_fields['google_captcha_site_key'] = $BookingPress->bookingpress_get_settings('google_captcha_site_key','google_captcha_setting');                
            }
            $bookingpress_front_vue_data_fields['enable_google_captcha'] = $bookingpresss_enable_google_captcha;
            return $bookingpress_front_vue_data_fields;
        }

        function bookingpress_customize_add_dynamic_data_fields_func($bookingpress_customize_vue_data_fields) {
            $bookingpress_customize_vue_data_fields['is_gcaptcha_activated'] = $this->is_addon_activated();
            $bookingpress_customize_vue_data_fields['booking_form_settings']['enable_google_captcha'] = '';                        
            return $bookingpress_customize_vue_data_fields;
        }

        function bookingpress_google_captcha_admin_notices(){
            if( !function_exists('is_plugin_active') ){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if( !is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php') ){
                echo "<div class='notice notice-warning'><p>" . esc_html__('BookingPress - Google Captcha Integration Plugin requires Bookingpress Premium Plugin installed and active.', 'bookingpress-google-captcha') . "</p></div>";
            }
        }
        function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings){
            $booking_form_settings['booking_form_settings']['enable_google_captcha'] = false;
            return $booking_form_settings;
        }

        function bookingpress_available_integration_addon_list_func($bookingpress_integration_addon_list) {
            $bookingpress_integration_addon_list[] = 'google-captcha';
            return  $bookingpress_integration_addon_list;
        }

        function bookingpress_load_integration_settings_data_func() {
            ?>
            vm.getSettingsData('google_captcha_setting','bookingpress_google_captcha');                             
            setTimeout(function() {
                if(vm.$refs.bookingpress_google_captcha != undefined){
                    vm.$refs.bookingpress_google_captcha.clearValidate();
                }                
            }, 2000);
           
            <?php            
        }

        function bookingpress_add_setting_dynamic_data_fields_func($bookingpress_dynamic_setting_data_fields){
            global $BookingPress;
            $bookingpress_dynamic_setting_data_fields['bookingpress_tab_list'][] = array(
                'tab_value' => 'google_captcha',
                'tab_name' => esc_html__('Google Captcha', 'bookingpress-google-captcha'),
            );   

            $bookingpress_dynamic_setting_data_fields['bookingpress_gcaptcha_language_list'] = $this->bookingpress_captcha_language_list();
            $bookingpress_dynamic_setting_data_fields['bookingpress_google_captcha'] = array(
                'google_captcha_site_key' => $BookingPress->bookingpress_get_settings('google_captcha_site_key', 'google_captcha_setting'),
                'google_captcha_secret_key' => $BookingPress->bookingpress_get_settings('google_captcha_secret_key', 'google_captcha_setting'),
                'google_captcha_language' => $BookingPress->bookingpress_get_settings('google_captcha_language', 'google_captcha_setting'),
                'google_captcha_failed_msg' => $BookingPress->bookingpress_get_settings('google_captcha_failed_msg', 'google_captcha_setting'),
            );
            $bookingpress_dynamic_setting_data_fields['bookingpress_configure_captcha_rules'] = array(                
                    'google_captcha_site_key' => array(
                        array(
                            'required' => true,
                            'message'  => __( 'Please Enter the site Key', 'bookingpress-google-captcha' ),
                            'trigger'  => 'change',
                        ),
                    ),
                    'google_captcha_secret_key'  => array(
                        array(
                            'required' => true,
                            'message'  => __( 'Please Enter the Secret Key', 'bookingpress-google-captcha' ),
                            'trigger'  => 'change',
                        ),
                    ),
                    'google_captcha_failed_msg' => array(
                        array(
                            'required' => true,
                            'message'  => __( 'Please enter reCAPTCHA Failed Message', 'bookingpress-google-captcha' ),
                            'trigger'  => 'change',
                        ),
                    ),
                );

            
            return $bookingpress_dynamic_setting_data_fields;
        }    

        function bookingpress_captcha_language_list() {     

            $bookingpress_rc_lang['en'] = __('English (US)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ar'] = __('Arabic', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['bn'] = __('Bengali', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['bg'] = __('Bulgarian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ca'] = __('Catalan', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['zh-CN'] = __('Chinese(Simplified)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['zh-TW'] = __('Chinese(Traditional)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['hr'] = __('Croatian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['cs'] = __('Czech', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['da'] = __('Danish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['nl'] = __('Dutch', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['en-GB'] = __('English (UK)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['et'] = __('Estonian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['fil'] = __('Filipino', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['fi'] = __('Finnish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['fr'] = __('French', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['fr-CA'] = __('French (Canadian)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['de'] = __('German', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['gu'] = __('Gujarati', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['de-AT'] = __('German (Autstria)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['de-CH'] = __('German (Switzerland)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['el'] = __('Greek', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['iw'] = __('Hebrew', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['hi'] = __('Hindi', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['hu'] = __('Hungarian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['id'] = __('Indonesian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['it'] = __('Italian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ja'] = __('Japanese', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['kn'] = __('Kannada', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ko'] = __('Korean', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['lv'] = __('Latvian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['lt'] = __('Lithuanian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ms'] = __('Malay', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ml'] = __('Malayalam', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['mr'] = __('Marathi', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['no'] = __('Norwegian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['fa'] = __('Persian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['pl'] = __('Polish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['pt'] = __('Portuguese', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['pt-BR'] = __('Portuguese (Brazil)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['pt-PT'] = __('Portuguese (Portugal)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ro'] = __('Romanian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ru'] = __('Russian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['sr'] = __('Serbian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['sk'] = __('Slovak', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['sl'] = __('Slovenian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['es'] = __('Spanish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['es-149'] = __('Spanish (Latin America)', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['sv'] = __('Swedish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ta'] = __('Tamil', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['te'] = __('Telugu', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['th'] = __('Thai', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['tr'] = __('Turkish', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['uk'] = __('Ukrainian', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['ur'] = __('Urdu', 'bookingpress-google-captcha');
            $bookingpress_rc_lang['vi'] = __('Vietnamese', 'bookingpress-google-captcha');

             $bookingpress_language  = array(); 
            foreach($bookingpress_rc_lang as $bookingpress_rc_lang_key => $bookingpress_rc_val) {
                $bookingpress_language[] = array(
                    'key' => $bookingpress_rc_val,
                    'value' => $bookingpress_rc_lang_key,
               );
            }
            return $bookingpress_language;
        }

        function bookingpress_add_integration_settings_section_func() {
            ?>
            <el-row type="flex" class="bpa-mlc-head-wrap-settings bpa-gs-tabs--pb__heading __bpa-is-groupping" v-if="bpa_integration_active_tab == 'google_captcha'">
                <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12" class="bpa-gs-tabs--pb__heading--left">
                    <h1 class="bpa-page-heading"><?php esc_html_e( 'Google Captcha', 'bookingpress-google-captcha' ); ?></h1>
                </el-col>
                <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
                    <div class="bpa-hw-right-btn-group bpa-gs-tabs--pb__btn-group">												
                        <el-button class="bpa-btn bpa-btn--primary" @click="saveSettingsData('bookingpress_google_captcha','google_captcha_setting')" :class="(is_display_save_loader == '1') ? 'bpa-btn--is-loader' : ''"  :disabled="is_disabled" >
                            <span class="bpa-btn__label"><?php esc_html_e( 'Save', 'bookingpress-google-captcha' ); ?></span>
                            <div class="bpa-btn--loader__circles">				    
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </el-button>
                    </div>
                </el-col>
            </el-row>
            <el-form id="bookingpress_google_captcha" ref="bookingpress_google_captcha" :rules="bookingpress_configure_captcha_rules" :model="bookingpress_google_captcha" label-position="top" @submit.native.prevent v-show="bpa_integration_active_tab == 'google_captcha'">
                <div class="bpa-gs__cb--item">                
                    <div class="bpa-gs__cb--item-body bpa-gs__integration-cb--item-body">
                        <el-row class="bpa-gs--tabs-pb__cb-item-row">
                            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                                <h4><?php esc_html_e( 'Site Key', 'bookingpress-google-captcha' ); ?></h4>
                            </el-col> 
                            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                                <el-form-item prop="google_captcha_site_key">
                                    <el-input class="bpa-form-control" v-model="bookingpress_google_captcha.google_captcha_site_key" placeholder="<?php esc_html_e( 'Enter site key', 'bookingpress-google-captcha' ); ?>"></el-input>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row class="bpa-gs--tabs-pb__cb-item-row">
                            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                                <h4><?php esc_html_e( 'Site Secret', 'bookingpress-google-captcha' ); ?></h4>
                            </el-col> 
                            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                                <el-form-item prop="google_captcha_secret_key">
                                    <el-input class="bpa-form-control" v-model="bookingpress_google_captcha.google_captcha_secret_key" placeholder="<?php esc_html_e( 'Enter secret key', 'bookingpress-google-captcha' ); ?>"></el-input>
                                </el-form-item>
                            </el-col>
                        </el-row >
                        <el-row class="bpa-gs--tabs-pb__cb-item-row">
                            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                                <h4><?php esc_html_e( 'reCAPTCHA Language', 'bookingpress-google-captcha' ); ?></h4>
                            </el-col> 
                            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                                <el-form-item prop="google_captcha_language">
                                    <el-select class="bpa-form-control" v-model="bookingpress_google_captcha.google_captcha_language">
                                        <el-option v-for="item in bookingpress_gcaptcha_language_list" :key="item.value" :label="item.key" :value="item.value">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row class="bpa-gs--tabs-pb__cb-item-row">
                            <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" class="bpa-gs__cb-item-left">
                                <h4><?php esc_html_e( 'reCAPTCHA Failed Message', 'bookingpress-google-captcha' ); ?></h4>
                            </el-col> 
                            <el-col :xs="12" :sm="12" :md="12" :lg="16" :xl="16" class="bpa-gs__cb-item-right">
                                <el-form-item prop="google_captcha_language">
                                    <el-input class="bpa-form-control" v-model="bookingpress_google_captcha.google_captcha_failed_msg" ></el-input>           
                                </el-form-item>
                            </el-col>
                        </el-row>  
                    </div>  
                </div>    
            </el-form>
            <?php
        }
        function bookingpress_add_appointment_booking_on_load_methods_func($bookingpress_appointment_data){

            $bookingpress_appointment_data .= 'this.initMap();';            
            return $bookingpress_appointment_data;
        }
        function bookingpress_add_appointment_booking_vue_methods_func($bookingpress_vue_methods_data){                       

            global $BookingPress;
            $bookingpress_google_captcha_site_key = $BookingPress->bookingpress_get_settings('google_captcha_site_key','google_captcha_setting');
            $bookingpress_google_captcha_language = $BookingPress->bookingpress_get_settings('google_captcha_language','google_captcha_setting');                
            $bookingpress_g_recaptcha_response = !empty($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';            

            $script_url = "https://www.google.com/recaptcha/api.js?hl=".$bookingpress_google_captcha_language."&render=".$bookingpress_google_captcha_site_key;

            $bookingpress_vue_methods_data .= '            
            loadCaptchaJs(is_listing = false){
                const vm = this
                var script_url = "'.$script_url.'"
                if(is_listing == false)
                {
                    script_url = script_url+"&onload=render_bookingpress_captcha_v3"
                }
                var script = document.createElement("script")
                script.src = script_url
                script.async = true
                var bookingpress_captcha_v3 =  "bookingpress_captcha_"+bookingpress_uniq_id_js_var
                vm[bookingpress_captcha_v3] = "'.$bookingpress_g_recaptcha_response.'"
                var dsize = "normal"
                window.addEventListener("load", function() { (function($) {                    
                    jQuery(document).ready(function (){
                    if( !window["bookingpress_recaptcha_v3"] ){
                        window["bookingpress_recaptcha_v3"] = {}
                    }
                    window["bookingpress_recaptcha_v3"][bookingpress_captcha_v3] = {
                        size : dsize
                    };
                }); })(jQuery); })
                document.head.appendChild(script)
            },
            initMap(){
                const vm = this
                var bookingpress_grecaptcha_site_key = "'.$bookingpress_google_captcha_site_key.'"                                    
                var bookingpress_grecaptcha_language = "'.$bookingpress_google_captcha_language.'"                          
                
                if(vm.enable_google_captcha == "true" && bookingpress_grecaptcha_site_key != "" && bookingpress_grecaptcha_language != "") {                                        
                    vm.loadCaptchaJs()
                }                
                window.render_bookingpress_captcha_v3 = function() {                       
                    if (typeof window["bookingpress_captcha_v3"] != "undefined" && typeof grecaptcha != "undefined") {                                        
                        grecaptcha.ready(function() {
                            grecaptcha.execute(bookingpress_grecaptcha_site_key).then(function(bookingpress_recaptcha_token) {                                
                                for (var bookingpress_grecaptcha_field_v3 in window["bookingpress_recaptcha_v3"]) {                                    
                                    var bookingpress_grecaptcha_fields_v3 = bookingpress_grecaptcha_field_v3
                                    var bookingpress_grecaptcha_size = window["bookingpress_recaptcha_v3"][bookingpress_grecaptcha_field_v3]["size"]                      
                                    bookingpress_grecaptcha_fields_v3 = grecaptcha.render(bookingpress_grecaptcha_field_v3, {
                                        "sitekey": bookingpress_grecaptcha_site_key,
                                        "size": bookingpress_grecaptcha_size,
                                    })
                                    vm["appointment_step_form_data"][bookingpress_grecaptcha_field_v3] = bookingpress_recaptcha_token
                                }
                            });  
                        }); 
                    }else{
                        var bookingpress_captcha_int = 0
                        var bookingpress_captcha_interval = setInterval(function(){                            
                            if (typeof(window["bookingpress_recaptcha_v3"]) != "undefined"){                                
                                grecaptcha.ready(function() {                                    
                                    grecaptcha.execute(bookingpress_grecaptcha_site_key).then(function(bookingpress_recaptcha_token) {                                    
                                        for (var bookingpress_grecaptcha_field_v3 in window["bookingpress_recaptcha_v3"]) {
                                            var bookingpress_grecaptcha_fields_v3 = bookingpress_grecaptcha_field_v3;
                                            var bookingpress_grecaptcha_size = window["bookingpress_recaptcha_v3"][bookingpress_grecaptcha_field_v3]["size"]                                                                 
                                            bookingpress_grecaptcha_fields_v3 = grecaptcha.render(bookingpress_grecaptcha_field_v3, {
                                                "sitekey": bookingpress_grecaptcha_site_key,
                                                "size": bookingpress_grecaptcha_size,
                                            })
                                            vm["appointment_step_form_data"][bookingpress_grecaptcha_field_v3] = bookingpress_recaptcha_token
                                            clearInterval(bookingpress_captcha_interval)
                                        }
                                    });  
                                }); 
                            }else{
                                bookingpress_captcha_int++;
                                if(bookingpress_captcha_int == 10){
                                    clearInterval(bookingpress_captcha_interval)
                                }
                            }
                        }, 1500)
                    }
                }    
            },
            bookingpress_reload_captcha() {
                var bookingpress_grecaptcha_site_key = "'.$bookingpress_google_captcha_site_key.'";
                if (typeof(window["bookingpress_recaptcha_v3"]) != "undefined" && typeof(grecaptcha) != "undefined") {
                    return new Promise( (res,rej) => {
                        grecaptcha.ready( () =>{
                            grecaptcha.execute(bookingpress_grecaptcha_site_key).then(function(bookingpress_recaptcha_token) {
                                return res(bookingpress_recaptcha_token);
                            })
                        } );
                    } );
                }
            },';

            return $bookingpress_vue_methods_data;        
        }     
        function bookingpress_validate_google_cpatcha($posted_data) {
            global $BookingPress;
            $bookingpresss_enable_google_captcha = $BookingPress->bookingpress_get_customize_settings('enable_google_captcha', 'booking_form');            
            $bookingpress_google_captch_site_key = $BookingPress->bookingpress_get_settings('google_captcha_site_key','google_captcha_setting');
            $bookingpress_google_secret_key = $BookingPress->bookingpress_get_settings('google_captcha_secret_key','google_captcha_setting');              
            $bookingpress_google_captcha_failed_msg = $BookingPress->bookingpress_get_settings('google_captcha_failed_msg','google_captcha_setting');                                              
            if(!empty($bookingpress_google_captch_site_key) && !empty($bookingpress_google_secret_key) && ($bookingpresss_enable_google_captcha == 'true')){                                           
                $bookingpress_form_random_key = !empty($posted_data['appointment_data']['bookingpress_uniq_id']) ? esc_html($posted_data['appointment_data']['bookingpress_uniq_id']) : '';                
                if(isset($posted_data['appointment_data']['bookingpress_captcha_'.$bookingpress_form_random_key])){                    
                    require_once(BOOKINGPRESS_GOOGLE_CAPTCHA_LIBRARY_DIR . '/recaptchalib/recaptchalib.php');           
                    $bookingpress_recaptcha = new Bookingpress_ReCaptcha($bookingpress_google_secret_key);                    
                    $bookingpress_recaptcha_response = $bookingpress_recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $posted_data['appointment_data']['bookingpress_captcha_'.$bookingpress_form_random_key]);                     
                    if ($bookingpress_recaptcha_response->success != 1 && !empty($bookingpress_recaptcha_response->errorCodes)) {
                        $bookingpress_recptcha_invalid_message = !empty($bookingpress_google_captcha_failed_msg) ? $bookingpress_google_captcha_failed_msg : __('Google reCAPTCHA is Invalid or Expired. Please reload page and try again', 'bookingpress-google-captcha').'.';                        
                        $response['variant'] = 'error';
                        $response['title']   = esc_html__( 'Error', 'bookingpress-google-captcha' );
                        $response['msg'] = $bookingpress_recptcha_invalid_message;
                        echo json_encode( $response );
                        exit();
                    }
                }
            }
                                
        }                        
    }
    global $bookingpress_google_captcha;
	$bookingpress_google_captcha = new bookingpress_google_captcha();
}