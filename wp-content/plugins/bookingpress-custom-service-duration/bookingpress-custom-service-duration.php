<?php
/*
Plugin Name: BookingPress - Custom Service Duration Addon
Description: Extension for BookingPress plugin allow customers to choose custom service duration
Version: 1.8
Requires at least: 5.0
Requires PHP:      5.6
Plugin URI: https://www.bookingpressplugin.com/
Author: Repute InfoSystems
Author URI: https://www.bookingpressplugin.com/
Text Domain: bookingpress-custom-service-duration
Domain Path: /languages
*/

define('BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR_NAME', 'bookingpress-custom-service-duration');
define('BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR', WP_PLUGIN_DIR . '/' . BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR_NAME);


register_activation_hook( __FILE__, 'bookingpress_install_custom_service_addon' );
register_uninstall_hook(__FILE__, 'bookingpress_uninstall_custom_service_addon');

global $wpdb;

$bpa_lite_plugin_version = get_option('bookingpress_version');
$bpa_pro_plugin_version = get_option('bookingpress_pro_version');

function bookingpress_install_custom_service_addon() {
    global $wpdb;
    $bpa_lite_plugin_version = get_option('bookingpress_version');
    $bpa_pro_plugin_version = get_option('bookingpress_pro_version');
    
    if(empty($bpa_lite_plugin_version) || empty($bpa_pro_plugin_version) || (!empty($bpa_lite_plugin_version) && version_compare( $bpa_lite_plugin_version, '1.0.48', '<' )) || (!empty($bpa_pro_plugin_version) && version_compare( $bpa_pro_plugin_version, '1.4', '<' )) ){

        $myaddon_name = "bookingpress-custom-service-duration/bookingpress-custom-service-duration.php";        
        deactivate_plugins($myaddon_name, FALSE);
        $bpa_lite_dact_message = $bpa_pro_dact_message = $bpa_dact_message = '';

        if(empty($bpa_lite_plugin_version) || version_compare( $bpa_lite_plugin_version, '1.0.48', '<' )) {
            $bpa_dact_message = __('BookingPress lite version 1.0.48', 'bookingpress-custom-service-duration');
        }
        if(!empty($bpa_dact_message) && (empty($bpa_pro_plugin_version) || version_compare( $bpa_pro_plugin_version, '1.4', '<' ))) {
            $bpa_dact_message .= $bpa_lite_dact_message.' '.__('and','bookingpress-custom-service-duration').' ';
        }
        if( empty($bpa_pro_plugin_version) || version_compare( $bpa_pro_plugin_version, '1.4', '<' )) {
            $bpa_dact_message.= __('BookingPress Premium version 1.4', 'bookingpress-custom-service-duration');
        }
        $bpa_dact_message .=' '.__('required to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
        
        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>'); //phpcs:ignore
        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>'); //phpcs:ignore
        die;
    }else{ 
        $bookingpress_c_d_version = get_option('bookingpress_custom_service_duration_version');

        if (!isset($bookingpress_c_d_version) || $bookingpress_c_d_version == '') {
            
            $myaddon_name = "bookingpress-custom-service-duration/bookingpress-custom-service-duration.php";
    
            // activate license for this addon
            $posted_license_key = trim( get_option( 'bkp_license_key' ) );
            $posted_license_package = '11808';

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
                $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-custom-service-duration' );
            } else {
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                $license_data_string = wp_remote_retrieve_body( $response );
                if ( false === $license_data->success ) {
                    switch( $license_data->error ) {
                        case 'expired' :
                            $message = sprintf(
                                __( 'Your license key expired on %s.','bookingpress-custom-service-duration' ),
                                date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                            );
                            break;
                        case 'revoked' :
                            $message = __( 'Your license key has been disabled.','bookingpress-custom-service-duration' );
                            break;
                        case 'missing' :
                            $message = __( 'Invalid license.','bookingpress-custom-service-duration' );
                            break;
                        case 'invalid' :
                        case 'site_inactive' :
                            $message = __( 'Your license is not active for this URL.','bookingpress-custom-service-duration' );
                            break;
                        case 'item_name_mismatch' :
                            $message = __('This appears to be an invalid license key for your selected package.','bookingpress-custom-service-duration');
                            break;
                        case 'invalid_item_id' :
                                $message = __('This appears to be an invalid license key for your selected package.','bookingpress-custom-service-duration');
                                break;
                        case 'no_activations_left':
                            $message = __( 'Your license key has reached its activation limit.','bookingpress-custom-service-duration' );
                            break;
                        default :
                            $message = __( 'An error occurred, please try again.','bookingpress-custom-service-duration' );
                            break;
                    }

                }

            }

            if ( ! empty( $message ) ) {
                update_option( 'bkp_custom_service_duration_license_data_activate_response', $license_data_string );
                update_option( 'bkp_custom_service_duration_license_status', $license_data->license );
                deactivate_plugins($myaddon_name, FALSE);
                $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress Custom Service Duration Add-on', 'bookingpress-custom-service-duration');
                $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-custom-service-duration'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                die;
            }
            
            if($license_data->license === "valid")
            {
                update_option( 'bkp_custom_service_duration_license_key', $posted_license_key );
                update_option( 'bkp_custom_service_duration_license_package', $posted_license_package );
                update_option( 'bkp_custom_service_duration_license_status', $license_data->license );
                update_option( 'bkp_custom_service_duration_license_data_activate_response', $license_data_string );
            }

            $bookingpress_custom_service_duration_version = 1.8;

            update_option('bookingpress_custom_service_duration_version', $bookingpress_custom_service_duration_version);
            $tbl_bookingpress_appointment_bookings = $wpdb->prefix . 'bookingpress_appointment_bookings';
            $tbl_bookingpress_entries = $wpdb->prefix . 'bookingpress_entries';            
            $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';            
            $tbl_bookingpress_custom_service_durations = $wpdb->prefix . 'bookingpress_custom_service_durations';   
            $tbl_bookingpress_custom_staffmembers_service_durations = $wpdb->prefix . 'bookingpress_custom_staffmembers_service_durations';   
            
            $is_exist_bookingpress_enable_custom_duration_col = $wpdb->get_results( $wpdb->prepare( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'bookingpress_enable_custom_duration'", DB_NAME, $tbl_bookingpress_appointment_bookings ) );
            $is_exist_entries_custom_duration_col = $wpdb->get_results( $wpdb->prepare( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'bookingpress_enable_custom_duration'", DB_NAME, $tbl_bookingpress_entries ) );            
            $is_exist_entries_custom_duration_val_col = $wpdb->get_results( $wpdb->prepare( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND column_name = 'bookingpress_custom_duration_val'", DB_NAME, $tbl_bookingpress_entries ) );

            if(empty($is_exist_entries_custom_duration_col)) {
                $wpdb->query( "ALTER TABLE `{$tbl_bookingpress_entries}` ADD COLUMN `bookingpress_enable_custom_duration`  TINYINT NULL DEFAULT 0 AFTER `bookingpress_due_amount`"); //phpcs:ignore  
            }
            if(empty($is_exist_bookingpress_enable_custom_duration_col)) {
                $wpdb->query( "ALTER TABLE `{$tbl_bookingpress_appointment_bookings}` ADD COLUMN `bookingpress_enable_custom_duration`  TINYINT NULL DEFAULT 0 AFTER `bookingpress_due_amount`");   //phpcs:ignore        
            }
            if(empty($is_exist_entries_custom_duration_val_col)) {
                $wpdb->query( "ALTER TABLE `{$tbl_bookingpress_entries}` ADD COLUMN `bookingpress_custom_duration_val` int(11) DEFAULT NULL AFTER `bookingpress_due_amount`");   //phpcs:ignore
            }

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';                
            $charset_collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) {
                if ( ! empty( $wpdb->charset ) ) {
                    $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
                }
                if ( ! empty( $wpdb->collate ) ) {
                    $charset_collate .= " COLLATE $wpdb->collate";
                }
            }
            $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_custom_service_durations}`(
                `bookingpress_custom_service_duration_id` int(11) NOT NULL AUTO_INCREMENT,
                `bookingpress_service_id` int(11) NOT NULL,
                `bookingpress_service_duration_val` int(11) NOT NULL,
                `bookingpress_service_duration_price` float NOT NULL,
                `bookingpress_custom_duration_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`bookingpress_custom_service_duration_id`)
            ) {$charset_collate}";
            $bookingpress_dbtbl_create[ $tbl_bookingpress_custom_service_durations ] = dbDelta( $sql_table );
            $sql_table = "CREATE TABLE IF NOT EXISTS `{$tbl_bookingpress_custom_staffmembers_service_durations}`(
                `bookingpress_staffmember_duration_id` int(11) NOT NULL AUTO_INCREMENT,
                `bookingpress_custom_service_duration_id` int(11) NOT NULL,
                `bookingpress_service_id` int(11) NOT NULL,
                `bookingpress_staffmember_id` int(11) NOT NULL,
                `bookingpress_staffmember_price` float NOT NULL,
                `bookingpress_staffmember_duration_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`bookingpress_staffmember_duration_id`)
            ) {$charset_collate}";
            $bookingpress_dbtbl_create[ $tbl_bookingpress_custom_staffmembers_service_durations ] = dbDelta( $sql_table );            

            $booking_form = array(
                'custom_service_duration_title' => __('Service Duration','bookingpress-custom-service-duration'),
                'custom_service_description_title' => __('Please select appropriate timeslot','bookingpress-custom-service-duration').'.',
                'custom_please_select_title' => __('Please select','bookingpress-custom-service-duration'),
                'custom_price_title' => __('Price','bookingpress-custom-service-duration').':',
                'custom_duration_title' => __('Custom Duration','bookingpress-custom-service-duration'),    
                'no_custom_service_duration_selected_title' => __('Please select custom service duration value','bookingpress-custom-service-duration'),                    
            );
            foreach($booking_form as $key => $value) {
                $bookingpress_customize_settings_db_fields = array(
                    'bookingpress_setting_name'  => $key,
                    'bookingpress_setting_value' => $value,
                    'bookingpress_setting_type'  => 'booking_form',
                );
                $wpdb->insert( $tbl_bookingpress_customize_settings, $bookingpress_customize_settings_db_fields );
            }
        }        
    }
}

function bookingpress_uninstall_custom_service_addon() {
    global $wpdb;
    $tbl_bookingpress_custom_service_durations = $wpdb->prefix . 'bookingpress_custom_service_durations';   
    $tbl_bookingpress_custom_staffmembers_service_durations = $wpdb->prefix . 'bookingpress_custom_staffmembers_service_durations';   
    $tbl_bookingpress_servicesmeta = $wpdb->prefix . 'bookingpress_servicesmeta';  
    
    if( is_multisite() ){
        $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
        if ( $blogs ) {
            foreach ( $blogs as $blog ) {
                switch_to_blog( $blog['blog_id'] );                
                $wpdb->query( "DROP TABLE IF EXISTS $tbl_bookingpress_custom_service_durations" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $wpdb->query( "DROP TABLE IF EXISTS $tbl_bookingpress_custom_staffmembers_service_durations" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

                $wpdb->delete( $tbl_bookingpress_servicesmeta, array('bookingpress_servicemeta_name' => 'enable_custom_service_duration'));
                $wpdb->delete( $tbl_bookingpress_servicesmeta, array('bookingpress_servicemeta_name' => 'custom_service_max_duration'));

                delete_option('bookingpress_custom_service_duration_version');                
            }
            restore_current_blog();
        }
    } else {

        $wpdb->query( "DROP TABLE IF EXISTS $tbl_bookingpress_custom_service_durations" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $wpdb->query( "DROP TABLE IF EXISTS $tbl_bookingpress_custom_staffmembers_service_durations" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        delete_option('bookingpress_custom_service_duration_version');
        
        $wpdb->delete( $tbl_bookingpress_servicesmeta, array('bookingpress_servicemeta_name' => 'enable_custom_service_duration'));
        $wpdb->delete( $tbl_bookingpress_servicesmeta, array('bookingpress_servicemeta_name' => 'custom_service_max_duration'));

    }

    delete_option('bkp_custom_service_duration_license_key');
    delete_option('bkp_custom_service_duration_license_package');
    delete_option('bkp_custom_service_duration_license_status');
    delete_option('bkp_custom_service_duration_license_data_activate_response');
}

if (!empty($bpa_lite_plugin_version) && version_compare( $bpa_lite_plugin_version, '1.0.48', '>=' ) && !empty($bpa_pro_plugin_version) && version_compare( $bpa_pro_plugin_version, '1.4', '>=' )  && file_exists(BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . '/autoload.php')) {
    require_once BOOKINGPRESS_CUSTOM_SERVICE_DURATION_DIR . '/autoload.php';
}
