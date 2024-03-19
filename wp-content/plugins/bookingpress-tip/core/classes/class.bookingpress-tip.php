<?php 
if (!class_exists('bookingpress_tip') && class_exists('BookingPress_Core') ) {
    class bookingpress_tip Extends BookingPress_Core {
            function __construct(){
                
                register_activation_hook(BOOKINGPRESS_TIP_DIR.'/bookingpress-tip.php', array('bookingpress_tip', 'install'));
                register_uninstall_hook(BOOKINGPRESS_TIP_DIR.'/bookingpress-tip.php', array('bookingpress_tip', 'uninstall'));

                //Admiin notices
                add_action('admin_notices', array($this, 'bookingpress_admin_notices'));
                if( !function_exists('is_plugin_active') ){
                    include_once ABSPATH . 'wp-admin/includes/plugin.php';
                }

                if(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')) {    
                    
                    add_action('wp_head', array( $this, 'set_front_css' ),11 );
                    add_action('admin_enqueue_scripts', array( $this, 'set_css'),11);

                    add_action('bookingpress_add_customize_extra_section',array($this, 'bookingpress_add_customize_extra_section_func'));
                    add_filter('bookingpress_frontend_apointment_form_add_dynamic_data',array($this,'bookingpress_frontend_apointment_form_add_dynamic_data_func'));

                    add_filter('bookingpress_get_booking_form_customize_data_filter',array($this,'bookingpress_get_booking_form_customize_data_filter_func'));
                    add_filter('bookingpress_customize_add_dynamic_data_fields',array($this,'bookingpress_customize_add_dynamic_data_fields_func'));
                    add_filter('bookingpress_before_save_customize_booking_form',array($this,'bookingpress_before_save_customize_booking_form_func'),11);

                    add_action('bookingpress_admin_panel_vue_methods', array($this, 'bookingpress_admin_panel_vue_methods_func'));
                    add_action('wp_ajax_bookingpress_apply_tip_amount_backend', array($this, 'bookingpress_apply_tip_amount_backend_func'));

                    add_action('bookingpress_add_content_after_subtotal_data_backend', array($this, 'bookingpress_add_content_after_subtotal_data_func_data'));
                    add_action('bookingpress_add_content_after_subtotal_data_frontend',array($this,'bookingpress_add_content_after_subtotal_data_func'));

                    add_filter('bookingpress_add_appointment_booking_vue_methods', array( $this, 'bookingpress_booking_dynamic_vue_methods_func' ), 10, 1);

                    add_action( 'wp_ajax_nopriv_bookingpress_apply_tip_amount', array( $this, 'bookingpress_apply_tip_amount_func' ) );
                    add_action( 'wp_ajax_bookingpress_apply_tip_amount', array( $this, 'bookingpress_apply_tip_amount_func' ) );

                    add_filter('bookingpress_modify_calculated_appointment_details', array($this, 'bookingpress_modify_calculated_appointment_details_func'));

                    add_filter('bookingpress_modify_entry_data_before_insert', array($this, 'bookingpress_modify_entry_data_func'), 10, 2);
                    
                    add_filter('bookingpress_modify_payment_log_fields_before_insert', array($this, 'bookingpress_modify_payment_log_fields_before_insert_func'), 10, 2);
                    add_filter('bookingpress_modify_payment_log_fields', array($this, 'bookingpress_modify_payment_log_fields_before_insert_func'), 10, 2);

                    add_filter('bookingpress_modify_appointment_booking_fields', array($this,'bookingpress_modify_appointment_booking_fields_func'),10,3);
                    add_filter('bookingpress_modify_appointment_booking_fields_before_insert', array($this, 'bookingpress_modify_appointment_booking_fields_before_insert_func'), 10, 2);

                    add_filter('bookingpress_modify_appointment_data', array($this, 'bookingpress_modify_appointment_data_func'), 10);

                    add_action('bookingpress_modify_payment_appointment_section', array( $this, 'bookingpress_modify_payment_section_func'));

                    add_action('bookingpress_modify_payment_managepayment_section', array( $this, 'bookingpress_modify_payment_managepayment_section_func'));

                    add_filter('bookingpress_modify_outside_total_amount', array( $this, 'bookingpress_modify_outside_total_amount_func'), 10,3);

                    add_filter('bookingpress_return_calculated_details_modify_outside', array( $this, 'bookingpress_return_calculated_details_modify_outside_func'),10,4);
                    
                    add_filter('bookingpress_modify_payments_listing_data', array($this, 'bookingpress_modify_payments_listing_data_func'));

                    add_action('bookingpress_frontend_payment_section_modified', array($this, 'bookingpress_frontend_payment_section_modified_func'));

                    add_filter('bookingpress_modify_backend_appointment_data', array($this, 'bookingpress_modify_backend_appointment_data_func'), 10, 2);
                    add_filter('bookingpress_modify_backend_subtotal_price', array($this, 'bookingpress_modify_backend_subtotal_price_func'), 10, 2);

                    add_filter('bookingpress_modify_backend_add_appointment_entry_data', array($this,'bookingpress_modify_backend_add_appointment_entry_data_func'),10,2);

                    /* edit dashboard filter */
                    add_action( 'bookingpress_dashboard_modify_dynamic_vue_methods', array($this, 'bookingpress_appointment_add_dynamic_vue_methods_func') );
                    add_filter( 'bookingpress_modify_dashboard_data_fields', array( $this, 'bookingpress_modify_dashboard_data_fields_func' ) );

                    /* for calendar  */
                    add_filter( 'bookingpress_modify_calendar_data_fields', array( $this, 'bookingpress_modify_calendar_data_fields_func' ), 10 );

                    add_action('bookingpress_edit_appointment_details', array($this, 'bookingpress_edit_appointment_details_func'));

                    add_action('bookingpress_after_update_appointment', array($this, 'bookingpress_after_update_appointment_func'), 10, 1);

                    add_filter('bookingpress_modify_appointment_data_fields',array($this,'bookingpress_modify_appointment_data_fields_func'));

                    add_filter('bookingpress_modify_my_appointments_data_externally', array($this,'bookingpress_modify_my_appointments_data_externally_func'),10 ,1);
                    
                    add_filter( 'bookingpress_add_global_option_data', array( $this, 'bookingpress_add_global_option_data_func' ),11);
                    add_filter( 'bookingpress_modify_email_content_filter', array( $this, 'bookingpress_modify_email_content_filter_func' ), 11, 2 );
                    add_filter('bookingpress_add_setting_dynamic_data_fields', array( $this,'bookingpress_add_setting_dynamic_data_fields_func'),11);

                    /* for invoice addon */
                    add_filter('bookingpress_change_total_amount_outside', array( $this, 'bookingpress_change_total_amount_outside_func'),10,3);
                    add_filter('bookingpress_change_paid_amount_outside', array( $this, 'bookingpress_change_paid_amount_outside_func'),10,3);
                    add_filter('bookingpress_change_label_value_for_invoice', array( $this, 'bookingpress_change_label_value_for_invoice_func'),10,2);
                    /* for calendaer addon */

                    /* for zapier addon */
                    add_filter('bookingpress_appointment_data_relpace_outside', array($this, 'bookingpress_appointment_data_relpace_outside_func'),10,3); 
                    add_filter('bookingpress_modify_appointment_default_field_zapier', array($this, 'bookingpress_modify_appointment_default_field_zapier_func'),10,3);

                    /* for payment link */
                    add_action('bookingpress_payment_add_content_after_subtotal_data_frontend', array($this,'bookingpress_payment_add_content_after_subtotal_data_frontend_func'));
                    add_filter('bookingpress_add_complete_payment_method', array($this,'bookingpress_add_complete_payment_method_func'),10,1);
                    add_action('bookingpress_modify_insert_data', array($this,'bookingpress_modify_insert_data_func'),10,3);
                    add_filter('modify_complate_payment_data_after_entry_create', array($this,'modify_complate_payment_data_after_entry_create_func'),10,2);

                    /* add capability for tip addon */
                    add_filter('bookingpress_modify_capability_data', array($this, 'bookingpress_modify_capability_data_func'), 11, 1);

                    /* manage frontside totalpayable amount */
                    add_filter('bookingpress_modify_total_payable_amount_value_front', array($this,'bookingpress_total_amount_cal_outside_arr'),10,1);

                    /* refund code */
                    add_filter('bookingpress_modify_refund_data_amount', array($this,'bookingpress_modify_refund_data_func'),10,2);

                    /* add filter  */
                    add_filter('bookingpress_total_amount_modify_outside_arr', array($this, 'bookingpress_total_amount_modify_outside_func'));

                    /* modify refund data */
                    add_filter('bookingpress_modify_refund_data_before_refund', array($this, 'bookingpress_modify_refund_data_before_refund_func'));

                    add_action('bookingpress_add_appointment_model_reset',array($this,'bookingpress_add_appointment_model_reset_func'),11);

                    add_action( 'bookingpress_dashboard_add_appointment_model_reset', array($this, 'bookingpress_add_appointment_model_reset_func'),11);                    

                    add_action('bookingpress_calendar_add_appointment_model_reset', array( $this, 'bookingpress_add_appointment_model_reset_func'),11);

                    add_action('bookingpress_admin_appointment_before_apply_coupon_code', array( $this, 'bookingpress_admin_appointment_before_apply_coupon_code_func'),11);

                    if(is_plugin_active('bookingpress-multilanguage/bookingpress-multilanguage.php')) {
                        add_filter('bookingpress_modified_language_translate_fields',array($this,'bookingpress_modified_language_translate_fields_func'),10);
                        add_filter('bookingpress_modified_customize_form_language_translate_fields',array($this,'bookingpress_modified_customize_form_language_translate_fields_func'),10);
                        add_filter('bookingpress_modified_language_translate_fields_section',array($this,'bookingpress_modified_tip_language_translate_fields_section_func'),10);
                    }

                    /* Add New FIlter For Package Order Backend Start */
                    add_filter('bookingpress_modify_package_order_vue_fields_data',array($this,'bookingpress_modify_package_order_vue_fields_data_func'));
                    add_action('bookingpress_package_order_add_content_after_subtotal_data_backend', array($this, 'bookingpress_package_order_add_content_after_subtotal_data_backend_func_data'));
                    add_action('bookingpress_package_order_add_dynamic_vue_methods', array( $this, 'bookingpress_package_order_add_dynamic_vue_methods_func' ), 10, 1);
                    add_action('bookingpress_add_package_order_model_reset',array($this,'bookingpress_add_package_order_model_reset'),10);
                    add_filter('bookingpress_modify_backend_add_package_order_entry_data', array($this,'bookingpress_modify_backend_add_package_order_entry_data_func'),10,2);

                    add_action('bookingpress_edit_package_order_details', array($this, 'bookingpress_edit_package_order_details_func'));
                    add_action('bookingpress_add_package_content_after_subtotal_data_frontend',array($this,'bookingpress_add_package_content_after_subtotal_data_frontend_func'));
                    add_filter('bookingpress_frontend_package_order_form_add_dynamic_data',array($this,'bookingpress_frontend_package_order_form_add_dynamic_data_func'));
                    add_filter('bookingpress_package_front_booking_dynamic_vue_methods',array($this,'bookingpress_package_front_booking_dynamic_vue_methods_func'),15,1);
                    add_filter('bookingpress_modify_frontend_add_package_order_entry_data', array($this,'bookingpress_modify_frontend_add_package_order_entry_data_func'),10,2);

                    //New filter
                    add_filter('bookingpress_modify_package_order_payment_log_fields_before_insert', array($this,'bookingpress_modify_package_order_payment_log_fields_before_insert_func'),10,2);
                    /* Add New FIlter For Package Order Backend Over */

                    add_filter('bookingpress_reset_package_order_popup_data',array($this,'bookingpress_reset_package_order_popup_data_func'),10,1);

                    /* Tip Customization Message Dynamic Add */
                    add_action('bookingpress_add_package_label_settings_dynamically',array($this,'bookingpress_add_package_label_settings_dynamically_func'),20);
                    add_filter('bookingpress_modified_package_customization_fields', array($this, 'bookingpress_modified_package_customization_fields_func'), 10,1);
                    add_filter('bookingpress_customized_package_booking_all_language_translation_fields',array($this,'bookingpress_customized_package_booking_all_language_translation_fields'),10,1);
                    add_filter('bookingpress_customize_package_booking_language_translate_fields_modified',array($this,'bookingpress_customize_package_booking_language_translate_fields_modified_func'),10,2);

                }
                add_action('activated_plugin',array($this,'bookingpress_is_tip_addon_activated'),11,2);

                add_action('admin_init', array( $this, 'bookingpress_update_tip_data') );
            }

            function bookingpress_customize_package_booking_language_translate_fields_modified_func($bookingpress_customize_package_booking_language_translate_fields,$bookingpress_all_language_translation_fields){

                $bookingpress_package_tips_fields = array(                    
                        'tip_label_txt' => array('field_type'=>'text','field_label'=>__('Tip label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_placeholder_txt' => array('field_type'=>'text','field_label'=>__('Tip placeholder', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_button_txt' => array('field_type'=>'text','field_label'=>__('Tip apply button label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_applied_title' => array('field_type'=>'text','field_label'=>__('Tip applied title', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),
                        'tip_error_msg' => array('field_type'=>'text','field_label'=>__('Tip Error Message', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),                                    
                );                                
                $bookingpress_customize_package_booking_language_translate_fields['package_customized_form_tip_input_labels'] = $bookingpress_package_tips_fields;                
                return $bookingpress_customize_package_booking_language_translate_fields;

            }

            function bookingpress_customized_package_booking_all_language_translation_fields($bookingpress_customized_package_booking_all_language_translation_fields){

                $bookingpress_package_tips_fields = array(
                    'package_customized_form_tip_input_labels' => array(
                        'tip_label_txt' => array('field_type'=>'text','field_label'=>__('Tip label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_placeholder_txt' => array('field_type'=>'text','field_label'=>__('Tip placeholder', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_button_txt' => array('field_type'=>'text','field_label'=>__('Tip apply button label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_applied_title' => array('field_type'=>'text','field_label'=>__('Tip applied title', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),
                        'tip_error_msg' => array('field_type'=>'text','field_label'=>__('Tip Error Message', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                    ) 
                );
                $bookingpress_customized_package_booking_all_language_translation_fields = array_merge($bookingpress_customized_package_booking_all_language_translation_fields,$bookingpress_package_tips_fields);

                return $bookingpress_customized_package_booking_all_language_translation_fields;
            }

            function bookingpress_modified_package_customization_fields_func($bookingpress_modified_package_customization_fields){

                global $BookingPress;

                $tip_label_txt = $BookingPress->bookingpress_get_customize_settings('tip_label_txt', 'package_booking_form');
                $bookingpress_modified_package_customization_fields['tip_label_txt'] = $tip_label_txt;
                $tip_placeholder_txt = $BookingPress->bookingpress_get_customize_settings('tip_placeholder_txt', 'package_booking_form');
                $bookingpress_modified_package_customization_fields['tip_placeholder_txt'] = $tip_placeholder_txt;
                $tip_button_txt = $BookingPress->bookingpress_get_customize_settings('tip_button_txt', 'package_booking_form');
                $bookingpress_modified_package_customization_fields['tip_button_txt'] = $tip_button_txt;
                $tip_applied_title = $BookingPress->bookingpress_get_customize_settings('tip_applied_title', 'package_booking_form');
                $bookingpress_modified_package_customization_fields['tip_applied_title'] = $tip_applied_title;
                $tip_error_msg = $BookingPress->bookingpress_get_customize_settings('tip_error_msg', 'package_booking_form');
                $bookingpress_modified_package_customization_fields['tip_error_msg'] = $tip_error_msg;

                return $bookingpress_modified_package_customization_fields;
            }            

            function bookingpress_add_package_label_settings_dynamically_func(){
            ?>
                <h5><?php esc_html_e('Tip Inputs labels', 'bookingpress-tip'); ?></h5>
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Tip label', 'bookingpress-tip'); ?></label>
                    <el-input v-model="package_booking_form_settings.tip_label_txt " class="bpa-form-control"></el-input>
                </div> 
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Tip placeholder', 'bookingpress-tip'); ?></label>
                    <el-input v-model="package_booking_form_settings.tip_placeholder_txt " class="bpa-form-control"></el-input>
                </div> 
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Tip apply button label', 'bookingpress-tip'); ?></label>
                    <el-input v-model="package_booking_form_settings.tip_button_txt " class="bpa-form-control"></el-input>
                </div> 
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Tip applied title', 'bookingpress-tip'); ?></label>
                    <el-input v-model="package_booking_form_settings.tip_applied_title " class="bpa-form-control"></el-input>
                </div> 
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Tip Error Message', 'bookingpress-tip'); ?></label>
                    <el-input v-model="package_booking_form_settings.tip_error_msg " class="bpa-form-control"></el-input>
                </div>                            
            <?php                
            }

            function bookingpress_reset_package_order_popup_data_func($bookingpress_reset_package_order_popup_data){

                $bookingpress_reset_package_order_popup_data.='
                    if(typeof vm.package_step_form_data.tip_amount !== "undefined"){
                        vm.package_step_form_data.tip_amount = "";
                    }
                    if(typeof vm.package_step_form_data.tip_amount_with_currency !== "undefined"){
                        vm.package_step_form_data.tip_amount_with_currency = "";
                    }                    
                    if(typeof vm.tip_apply_code_msg !== "undefined"){
                        vm.tip_apply_code_msg = "";
                    }                    
                    if(typeof vm.bpa_tip_apply_disabled !== "undefined"){
                        vm.bpa_tip_apply_disabled = 0;
                    } 
                    if(typeof vm.tip_applied_status !== "undefined"){
                        vm.tip_applied_status = "error";
                    } 
                    if(typeof vm.tip_apply_amount !== "undefined"){
                        vm.tip_apply_amount = "";
                    }                                                                     
                ';

                return $bookingpress_reset_package_order_popup_data;
            }

            function bookingpress_modify_package_order_payment_log_fields_before_insert_func($payment_log_data, $entry_data){

                $bookingpress_tip_amount = (isset($entry_data['bookingpress_tip_amount']))?floatval($entry_data['bookingpress_tip_amount']):0;
                $payment_log_data['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                
                return $payment_log_data;
            }

            /**
             * Function for modify package order entry detail
             *
             * @param  mixed $bookingpress_entry_details
             * @param  mixed $bookingpress_appointment_data
             * @return void
             */
            function bookingpress_modify_frontend_add_package_order_entry_data_func($bookingpress_entry_details, $bookingpress_package_data){

                $bookingpress_tip_amount = !empty($bookingpress_package_data['tip_amount']) ? intval($bookingpress_package_data['tip_amount']) : '';
                if( !empty( $bookingpress_tip_amount)){
                    $bookingpress_entry_details['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                }

                return $bookingpress_entry_details;
            }

            function bookingpress_package_front_booking_dynamic_vue_methods_func($bookingpress_vue_methods_data){
                global $wpdb, $BookingPress;
                $bookingpress_create_nonce      = wp_create_nonce( 'bpa_wp_nonce' );
                
                $bookingpress_after_tip_added = '';
                //$bookingpress_after_tip_added = apply_filters('bookingpress_after_tip_added', $bookingpress_after_tip_added);

                $bookingpress_vue_methods_data .= '

                    isNumber: function(evt) {
                        const vm = this;
                        this.package_step_form_data.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                    },

                    bookingpress_remove_tip_amount(){
                        const vm = this;
                        vm.package_step_form_data.tip_amount = "";
                        vm.package_step_form_data.tip_amount_with_currency = "";
                        vm.tip_apply_code_msg = "";
                        vm.bpa_tip_apply_disabled = 0;
                        vm.tip_applied_status = "error";
                        vm.tip_apply_amount = "";

                        vm.bookingpress_front_get_package_final_step_amount()
                    },

                    bookingpress_edit_tip_amount(){
                        const vm = this;
                        vm.tip_apply_code_msg = "";
                        vm.bpa_tip_apply_disabled = 0;
                        vm.tip_applied_status = "error";
                        vm.tip_apply_amount = "";
                        vm.bookingpress_front_get_package_final_step_amount();
                    },

                    bookingpress_apply_tip_amount(){
                        const vm = this;
                        vm.tip_apply_loader = "1";
                        var bookingpress_apply_tip_data = {};
                        bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount";
                        bookingpress_apply_tip_data.appointment_details = JSON.stringify( vm.package_step_form_data );

                        var bkp_wpnonce_pre = "' . $bookingpress_create_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }
                        bookingpress_apply_tip_data._wpnonce = bkp_wpnonce_pre_fetch;
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                        .then( function (response) {
                                vm.tip_apply_loader = "0";
                                vm.tip_applied_status = response.data.variant;
                                if(response.data.variant == "error"){
                                    vm.tip_apply_code_msg = vm.tip_error_msg;
                                } else {
                                    //vm.package_step_form_data.tip_amount = parseFloat(response.data.final_payable_amount);                                    
                                    vm.tip_apply_code_msg = response.data.msg;
                                    vm.tip_apply_amount = "+"+response.data.tip_amount_with_currency;
		                            vm.bpa_tip_apply_disabled = 1                                    
                                }                                
                                vm.bookingpress_front_get_package_final_step_amount();
                            }.bind(this) )
                            .catch( function (error) {
                                vm.bookingpress_set_error_msg(error);
                            });
                    },
                ';

                return $bookingpress_vue_methods_data;
            }

            
            function bookingpress_frontend_package_order_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){

                global $BookingPress;

                $tip_message_label = $BookingPress->bookingpress_get_customize_settings('tip_label_txt', 'package_booking_form');
                $bookingpress_front_vue_data_fields['tip_label_txt'] = $tip_message_label;

                $tip_placeholder_txt = $BookingPress->bookingpress_get_customize_settings('tip_placeholder_txt', 'package_booking_form');
                $bookingpress_front_vue_data_fields['tip_placeholder_txt'] = $tip_placeholder_txt;

                $tip_apply_btn_label = $BookingPress->bookingpress_get_customize_settings('tip_button_txt', 'package_booking_form');
                $bookingpress_front_vue_data_fields['tip_button_txt'] = $tip_apply_btn_label;

                $tip_apply_label_txt = $BookingPress->bookingpress_get_customize_settings('tip_applied_title', 'package_booking_form');
                $bookingpress_front_vue_data_fields['tip_applied_title'] = $tip_apply_label_txt;

                $tip_error_msg = $BookingPress->bookingpress_get_customize_settings('tip_error_msg', 'package_booking_form');
                $bookingpress_front_vue_data_fields['tip_error_msg'] = $tip_error_msg;

                $bookingpress_front_vue_data_fields['is_tip_activated'] = $this->bookingpress_check_tip_module_activation();
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['tip_amount'] = '';
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['tip_amount_with_currency'] = '';
                $bookingpress_front_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_front_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_front_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_front_vue_data_fields['tip_applied_status'] = 'error';

                return $bookingpress_front_vue_data_fields;
            }

            function bookingpress_add_package_content_after_subtotal_data_frontend_func() { ?>
                <div class="bpp-fm--bs-amount-item bpp-is-coupon-applied" v-if="tip_applied_status == 'success'">
                <div class="bpp-bs-ai__item">
                    {{tip_applied_title}}
                </div>
                <div class="bpp-bs-ai__item bpp-is-tip__price">
                    <div class="bpp-is-tip-edit-icon">
                        <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" @click="bookingpress_edit_tip_amount">
                            <g clip-path="url(#clip0_3299_6763)">
                                <path d="M2 11.6393V13.666C2 13.8526 2.14667 13.9993 2.33333 13.9993H4.36C4.44667 13.9993 4.53333 13.966 4.59333 13.8993L11.8733 6.62598L9.37333 4.12598L2.1 11.3993C2.03333 11.466 2 11.546 2 11.6393Z" fill="#727E95"/>
                                <path d="M13.8059 3.75305L12.2459 2.19305C11.9859 1.93305 11.5659 1.93305 11.3059 2.19305L10.0859 3.41305L12.5859 5.91305L13.8059 4.69305C14.0659 4.43305 14.0659 4.01305 13.8059 3.75305Z" fill="#727E95"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_3299_6763">
                                    <rect width="16" height="16" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    +{{ package_step_form_data.tip_amount_with_currency }}
                </div>
                </div>
                <div class="bpp-fm--bs__coupon-module-textbox" v-if="tip_applied_status == 'error'">
                <div class="bpp-cmt__left">
                    <span class="bpp-front-form-label">{{tip_label_txt}}</span>
                </div>
                <div class="bpp-cmt__right bpp-package-tip">                    
                    <el-input class="bpp-front-form-control" v-model="package_step_form_data.tip_amount" :placeholder="tip_placeholder_txt" @input="isNumber($event)" :disabled="bpa_tip_apply_disabled"></el-input>
                    <div class="bpp-bs__coupon-validation --is-error" v-if="tip_applied_status == 'error' && tip_apply_code_msg != ''">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 7c.55 0 1 .45 1 1v4c0 .55-.45 1-1 1s-1-.45-1-1V8c0-.55.45-1 1-1zm-.01-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-3h-2v-2h2v2z"></path></svg>
                        <p>{{ tip_apply_code_msg }}</p>
                    </div>
                    <div class="bpp-bs__coupon-validation --is-success" v-if="tip_applied_status == 'success' && tip_apply_code_msg != ''">
                        <span class="material-icons-round">check_circle</span>
                        <p>{{ tip_apply_code_msg }}</p>
                    </div>
                    <el-button class="bpp-front-btn bpp-front-btn--primary" :class="(tip_apply_loader == '1') ? 'bpp-front-btn--is-loader' : ''" :disabled="bpa_tip_apply_disabled" @click="bookingpress_apply_tip_amount">
                        <span class="bpp-btn__label" v-if="bpa_tip_apply_disabled == 0">{{tip_button_txt}}</span>
                        <span class="bpp-btn__label" v-else><?php esc_html_e( 'Applied', 'bookingpress-tip' ); ?></span>
                        <div class="bpp-front-btn--loader__circles">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </el-button>
                </div>
                </div>
            <?php }
                        
            /**
             * Function for edit package order tip value add
             *
             * @return void
             */
            function bookingpress_edit_package_order_details_func(){
            ?>            
            if(response.data.bookingpress_tip_amount != ""){
                var bookingpress_tip_amount_details = JSON.parse(response.data.bookingpress_tip_amount);
                vm2.package_formdata.tip_amount = bookingpress_tip_amount_details;                    
            }
            <?php 
            }

            /**
             * Function for modify package order entry detail
             *
             * @param  mixed $bookingpress_entry_details
             * @param  mixed $bookingpress_appointment_data
             * @return void
             */
            function bookingpress_modify_backend_add_package_order_entry_data_func($bookingpress_entry_details, $bookingpress_package_data){

                $bookingpress_tip_amount = !empty($bookingpress_package_data['tip_amount']) ? intval($bookingpress_package_data['tip_amount']) : '';
                if( !empty( $bookingpress_tip_amount)){
                    $bookingpress_entry_details['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                }

                return $bookingpress_entry_details;
            }

            /**
             * Function for reset package order popup
             *
             * @return void
             */
            function bookingpress_add_package_order_model_reset(){
            ?>
                if(typeof vm2.package_formdata.tip_amount !== 'undefined'){
                    vm2.package_formdata.tip_amount = 0;
                }
                if(typeof vm2.tip_applied_status !== 'undefined'){
                    vm2.tip_applied_status = '';
                    vm2.bpa_tip_apply_disabled = 0;
                    vm2.package_formdata.tip_amount_with_currency = '';
                    vm2.tip_apply_code_msg = '';
                }                
            <?php 
            }
            
            /**
             * Function for add package order
             *
             * @param  mixed $bookingpress_vue_methods_data
             * @return void
             */
            function bookingpress_package_order_add_dynamic_vue_methods_func($bookingpress_vue_methods_data){
                global $wpdb, $BookingPress;
                $bookingpress_create_nonce      = wp_create_nonce( 'bpa_wp_nonce' );

                ?>
                bookingpress_package_remove_tip_amount(){
                    const vm = this;
                    vm.tip_apply_code_msg = "";                    
                    vm.bpa_tip_apply_disabled = 0;
                    vm.tip_applied_status = "error";
                    vm.tip_apply_amount = "";
                    vm.bookingpress_admin_get_package_final_step_amount();
                },                
                isNumberPackage(evt) {
                    const vm = this;
                    vm.package_formdata.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                },
                bookingpress_apply_tip_amount(){                    
                    const vm = this
                    vm.tip_apply_loader = "1"
                    var bookingpress_apply_tip_data = {};
                    bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount_backend"
                    bookingpress_apply_tip_data.tip_amount = vm.package_formdata.tip_amount;
                    bookingpress_apply_tip_data.selected_service = vm.package_formdata.appointment_selected_package;
                    var finaltipamount = parseFloat(vm.package_formdata.tip_amount);
                    var total_amount_pass = parseFloat(vm.package_formdata.total_amount) - finaltipamount;
                    bookingpress_apply_tip_data.payable_amount = total_amount_pass;
                    bookingpress_apply_tip_data._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) )?>';
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                    .then( function (response) {
                        vm.tip_apply_loader = "0"
                        vm.tip_applied_status = response.data.variant;
                        if(response.data.variant == "error"){
                            vm.tip_apply_code_msg = response.data.msg
                        } else {
                            vm.tip_apply_code_msg = response.data.msg;
                            vm.package_formdata.tip_amount_with_currency = response.data.tip_amount_with_currency;
                            vm.bpa_tip_apply_disabled = 1
                        }                            
                        vm.bookingpress_admin_get_package_final_step_amount();
                    }.bind(this) )
                        .catch( function (error) {
                            vm.bookingpress_set_error_msg(error)
                    });
                    this.$forceUpdate();
                },
                <?php 
               
            }

            function bookingpress_package_order_add_content_after_subtotal_data_backend_func_data() { 
            ?>
                <div class="bpa-aaf-pd__tip-module">
                    <div class="bpa-aaf--bs__tip-module-textbox" v-if="tip_applied_status != 'success'">
                        <el-row>
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <span class="bpa-form-label"><?php esc_html_e( 'Give a tip', 'bookingpress-tip' ); ?></span>
                                <el-input class="bpa-form-control" v-model="package_formdata.tip_amount" @input="isNumberPackage($event)" placeholder="<?php esc_html_e( 'Enter tip amount', 'bookingpress-tip' ); ?>" :disabled="bpa_tip_apply_disabled"></el-input>
                                <div class="bpa-bs__tip-validation --is-error" v-if="tip_applied_status == 'error' && tip_apply_code_msg != ''">
                                    <span class="material-icons-round">error_outline</span>
                                    <p>{{ tip_apply_code_msg }}</p>
                                </div>
                                <div class="bpa-bs__tip-validation --is-success" v-if="tip_applied_status == 'success' && tip_apply_code_msg != ''">
                                    <span class="material-icons-round">check_circle</span>
                                    <p>{{ tip_apply_code_msg }}</p>
                                </div>
                                <el-button class="bpa-btn bpa-btn__medium bpa-btn--primary" @click="bookingpress_apply_tip_amount" :disabled="bpa_tip_apply_disabled">
                                    <span class="bpa-btn__label" v-if="bpa_tip_apply_disabled == 0"><?php esc_html_e( 'Add', 'bookingpress-tip' ); ?></span>
                                    <span class="bpa-btn__label" v-else><?php esc_html_e( 'Applied', 'bookingpress-tip' ); ?></span>
                                    <div class="bpa-btn--loader__circles">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                </el-button>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="bpa-fm--bs-amount-item bpa-is-tip-applied bpa-is-hide-stroke" v-if="tip_applied_status == 'success'">
                        <el-row>
                            <el-col :xs="12" :sm="12" :md="24" :lg="22" :xl="22">
                                <h4><?php esc_html_e( 'Tip Applied', 'bookingpress-tip' ); ?></h4>
                            </el-col>
                            <el-col :xs="12" :sm="12" :md="24" :lg="2" :xl="2">
                                <div class="bpa-ta__amount">
                                    <a class="bpa-taa__edit-icon" href="#" @click="bookingpress_package_remove_tip_amount"><span class="material-icons-round">mode_edit</span></a>
                                    <h4 class="is-price">+{{ package_formdata.tip_amount_with_currency }}</h4>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            <?php 
            }

            /**
             * Package order field added 
             *
             * @param  mixed $bookingpress_package_vue_data_fields
             * @return void
             */
            function bookingpress_modify_package_order_vue_fields_data_func( $bookingpress_package_vue_data_fields ) {
                $bookingpress_package_vue_data_fields['package_formdata']['tip_amount'] = '';
                $bookingpress_package_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_package_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_package_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_package_vue_data_fields['tip_applied_status'] = '';
                return $bookingpress_package_vue_data_fields; 
            }

            /*Multi language addon filter */
            function bookingpress_modified_language_translate_fields_func($bookingpress_all_language_translation_fields){
			    $bookingpress_tip_language_translation_fields = array(                
                    'customized_form_tip_input_labels' => array(
                        'tip_label_txt' => array('field_type'=>'text','field_label'=>__('Tip label', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_placeholder_txt' => array('field_type'=>'text','field_label'=>__('Tip placeholder', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_button_txt' => array('field_type'=>'text','field_label'=>__('Tip apply button label', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_applied_title' => array('field_type'=>'text','field_label'=>__('Tip applied title', 'bookingpress-tip'),'save_field_type'=>'booking_form'),
                        'tip_error_msg' => array('field_type'=>'text','field_label'=>__('Tip Error Message', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                    ),
                    'package_customized_form_tip_input_labels' => array(
                        'tip_label_txt' => array('field_type'=>'text','field_label'=>__('Tip label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_placeholder_txt' => array('field_type'=>'text','field_label'=>__('Tip placeholder', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_button_txt' => array('field_type'=>'text','field_label'=>__('Tip apply button label', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                        'tip_applied_title' => array('field_type'=>'text','field_label'=>__('Tip applied title', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),
                        'tip_error_msg' => array('field_type'=>'text','field_label'=>__('Tip Error Message', 'bookingpress-tip'),'save_field_type'=>'package_booking_form'),               
                    )                     
                );  
                $bookingpress_all_language_translation_fields = array_merge($bookingpress_all_language_translation_fields,$bookingpress_tip_language_translation_fields);
                return $bookingpress_all_language_translation_fields;
            }

            function bookingpress_modified_customize_form_language_translate_fields_func($bookingpress_all_language_translation_fields){
                $bookingpress_tip_language_translation_fields = array(                
                    'customized_form_tip_input_labels' => array(
                        'tip_label_txt' => array('field_type'=>'text','field_label'=>__('Tip label', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_placeholder_txt' => array('field_type'=>'text','field_label'=>__('Tip placeholder', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_button_txt' => array('field_type'=>'text','field_label'=>__('Tip apply button label', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                        'tip_applied_title' => array('field_type'=>'text','field_label'=>__('Tip applied title', 'bookingpress-tip'),'save_field_type'=>'booking_form'),
                        'tip_error_msg' => array('field_type'=>'text','field_label'=>__('Tip Error Message', 'bookingpress-tip'),'save_field_type'=>'booking_form'),               
                    )   
                ); 
                $pos = 2;
                $bookingpress_all_language_translation_fields = array_slice($bookingpress_all_language_translation_fields, 0, $pos)+$bookingpress_tip_language_translation_fields + array_slice($bookingpress_all_language_translation_fields, $pos);
                return $bookingpress_all_language_translation_fields;
            }


            function bookingpress_modified_tip_language_translate_fields_section_func($bookingpress_all_language_translation_fields_section){
                /* Function to add tip step heading */
                $bookingpress_tip_input_section_added = array('customized_form_tip_input_labels' => __('Tip Inputs labels', 'bookingpress-tip') );
                $bookingpress_tip_input_section_added = array('package_customized_form_tip_input_labels' => __('Tip Inputs labels', 'bookingpress-tip') );
                $bookingpress_all_language_translation_fields_section = array_merge($bookingpress_all_language_translation_fields_section,$bookingpress_tip_input_section_added);
                return $bookingpress_all_language_translation_fields_section;
            }            

            function bookingpress_update_tip_data(){
                global $BookingPress, $bookingpress_tip_version;
                $bookingpress_db_tip_version = get_option('bookingpress_tip_addon', true);
    
                if( version_compare( $bookingpress_db_tip_version, '1.4', '<' ) ){
                    $bookingpress_load_tip_update_file = BOOKINGPRESS_TIP_DIR . '/core/views/upgrade_latest_tip_data.php';
                    include $bookingpress_load_tip_update_file;
                    $BookingPress->bookingpress_send_anonymous_data_cron();
                }
            }

            function bookingpress_admin_appointment_before_apply_coupon_code_func(){
            ?>
                if(typeof vm.appointment_formdata.tip_amount !== 'undefined'){
                    var total_amount = vm.appointment_formdata.total_amount - vm.appointment_formdata.tip_amount;
                    vm.appointment_formdata.total_amount = total_amount;
                }                
            <?php 
            }

            function bookingpress_add_appointment_model_reset_func() {
            ?>
                if(typeof vm2.appointment_formdata.tip_amount !== 'undefined'){
                    vm2.appointment_formdata.tip_amount = 0;
                }
                if(typeof vm2.tip_applied_status !== 'undefined'){
                    vm2.tip_applied_status = '';
                    vm2.bpa_tip_apply_disabled = 0;
                    vm2.appointment_formdata.tip_amount_with_currency = '';
                    vm2.tip_apply_code_msg = '';
                }
            <?php
            }

            function bookingpress_modify_refund_data_before_refund_func($bookingpress_refund_data) {
                if( !empty($bookingpress_refund_data['refund_type']) && $bookingpress_refund_data['refund_type'] == 'full' && !empty($bookingpress_refund_data['bookingpress_tip_amount'])) {                    
                    $bookingpress_refund_data['refund_type'] = 'partial';                    
                    $bookingpress_refund_data['refund_amount'] = $bookingpress_refund_data['default_refund_amount'] - $bookingpress_refund_data['bookingpress_tip_amount'];
                }
                return $bookingpress_refund_data;
            }

            function bookingpress_total_amount_modify_outside_func( $bookingpress_total_amount_modify_outside ){ 

                $bookingpress_total_amount_modify_outside .= '

                    if(vm.appointment_step_form_data.tip_amount != ""){

                        if( vm.appointment_step_form_data.coupon_code != "" && vm.appointment_step_form_data.coupon_discount_amount != ""){

                            vm.appointment_step_form_data.total_payable_amount = vm.appointment_step_form_data.total_payable_amount + parseInt(vm.appointment_step_form_data.tip_amount);
                            vm.appointment_step_form_data.tip_amount_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.tip_amount);
                            vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.total_payable_amount );
                            subtotal_price = vm.appointment_step_form_data.total_payable_amount;

                        } else {
                            vm.appointment_step_form_data.total_payable_amount = parseInt(total_payable_amount) + parseInt(vm.appointment_step_form_data.tip_amount);
                            vm.appointment_step_form_data.tip_amount_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.tip_amount);
                            vm.appointment_step_form_data.total_payable_amount_with_currency = vm.bookingpress_price_with_currency_symbol( vm.appointment_step_form_data.total_payable_amount );
                            subtotal_price = parseInt(total_payable_amount) + parseInt(vm.appointment_step_form_data.tip_amount);

                        }
                    } 
                ';

                return $bookingpress_total_amount_modify_outside;
            }

            function bookingpress_modify_refund_data_func($bookingpress_appointment_payment_logs_data , $payment_id){

                global $wpdb, $tbl_bookingpress_payment_logs;

                if( !empty($payment_id) ){
                    
                    $bookingpress_appointment_payment_logs= $wpdb->get_row($wpdb->prepare("SELECT bookingpress_tip_amount FROM {$tbl_bookingpress_payment_logs} WHERE bookingpress_payment_log_id = %d", $payment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm

                    if( !empty($bookingpress_appointment_payment_logs['bookingpress_tip_amount'])){

                        $tip_amount = $bookingpress_appointment_payment_logs['bookingpress_tip_amount'];

                        $bookingpress_appointment_payment_logs_data = $bookingpress_appointment_payment_logs_data - $tip_amount;
                    }
                }
                return $bookingpress_appointment_payment_logs_data;
            }

            function bookingpress_is_tip_addon_activated($plugin,$network_activation){
                $myaddon_name = "bookingpress-tip/bookingpress-tip.php";

                if($plugin == $myaddon_name){

                    if(!(is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')))
                    {
                        deactivate_plugins($plugin, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress tip Add-on', 'bookingpress-tip');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-tip'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }

                    $license = trim( get_option( 'bkp_license_key' ) );
                    $package = trim( get_option( 'bkp_license_package' ) );

                    if( '' === $license || false === $license ) 
                    {
                        deactivate_plugins($plugin, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress tip Add-on', 'bookingpress-tip');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-tip'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                    else
                    {
                        $store_url = BOOKINGPRESS_TIP_STORE_URL;
                        $api_params = array(
                            'edd_action' => 'check_license',
                            'license' => $license,
                            'item_id'  => $package,
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
                                $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress tip Add-on', 'bookingpress-tip');
                                $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-tip'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                                wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                                die;
                            }

                        }
                        else
                        {
                            deactivate_plugins($plugin, FALSE);
                            $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$plugin);
                            $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress tip Add-on', 'bookingpress-tip');
                            $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-tip'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                            wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                            die;
                        }
                    }
                }
            }   

            function set_front_css(){                
                global $BookingPress;
                wp_register_style( 'bookingpress_tip_front_css', BOOKINGPRESS_TIP_URL . '/css/bookingpress_tip_front.css', array(), BOOKINGPRESS_TIP_VERSION );
                if ( $BookingPress->bookingpress_is_front_page() ) {
                    wp_enqueue_style( 'bookingpress_tip_front_css' );
                }
            }

            function set_css(){
                global $bookingpress_slugs;
			    wp_register_style('bookingpress_tip_admin_css',BOOKINGPRESS_TIP_URL . '/css/bookingpress_tip_admin.css',array(),BOOKINGPRESS_TIP_VERSION);						
    			if ( isset( $_REQUEST['page'] ) && in_array( sanitize_text_field( $_REQUEST['page'] ), (array) $bookingpress_slugs ) ) {
	    			wp_enqueue_style( 'bookingpress_tip_admin_css' );
                }
			}            

            function bookingpress_total_amount_cal_outside_arr($total_amount_calc){ 
                $total_amount_calc = true;
                return $total_amount_calc;
            }

            function bookingpress_modify_capability_data_func($bpa_caps){                
                $bpa_caps['bookingpress'][] = 'bookingpress_apply_tip_amount_backend';
                $bpa_caps['bookingpress_calendar'][] = 'bookingpress_apply_tip_amount_backend';
                $bpa_caps['bookingpress_appointments'][] = 'bookingpress_apply_tip_amount_backend';
                return $bpa_caps;
            }

            function modify_complate_payment_data_after_entry_create_func( $bookingpress_complete_payment_data_vars, $bookingpress_appointment_details ){

                global $BookingPress;

                $tip_message_label = $BookingPress->bookingpress_get_customize_settings('tip_label_txt', 'booking_form');
			    $tip_placeholder_txt = $BookingPress->bookingpress_get_customize_settings('tip_placeholder_txt', 'booking_form');
			    $tip_apply_btn_label = $BookingPress->bookingpress_get_customize_settings('tip_button_txt', 'booking_form');
			    $tip_applied_title = $BookingPress->bookingpress_get_customize_settings('tip_applied_title', 'booking_form');
			    
			    $bookingpress_complete_payment_data_vars['tip_label_txt'] = !empty($tip_message_label) ? stripslashes_deep($tip_message_label) : '';			
			    $bookingpress_complete_payment_data_vars['tip_placeholder_txt'] = !empty($tip_placeholder_txt) ? stripslashes_deep($tip_placeholder_txt) : '';
			    $bookingpress_complete_payment_data_vars['tip_button_txt'] = !empty($tip_apply_btn_label) ? stripslashes_deep($tip_apply_btn_label) : '';
			    $bookingpress_complete_payment_data_vars['tip_applied_title'] = !empty($tip_apply_btn_label) ? stripslashes_deep($tip_applied_title) : '';

                $bookingpress_complete_payment_data_vars['appointment_step_form_data']['tip_amount'] = '';
                $bookingpress_complete_payment_data_vars['tip_apply_loader'] = 0;
                $bookingpress_complete_payment_data_vars['tip_apply_code_msg'] = '';
                $bookingpress_complete_payment_data_vars['bpa_tip_apply_disabled'] = 0;
                $bookingpress_complete_payment_data_vars['tip_applied_status'] = 'error';
                $bookingpress_complete_payment_data_vars['is_tip_already_applied'] = '0';

                if( !empty($bookingpress_appointment_details['bookingpress_tip_amount'])){
                    $bookingpress_complete_payment_data_vars['appointment_step_form_data']['tip_amount'] = $bookingpress_appointment_details['bookingpress_tip_amount'];
                    $bookingpress_complete_payment_data_vars['appointment_step_form_data']['tip_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_appointment_details['bookingpress_tip_amount']);
                    $bookingpress_complete_payment_data_vars['is_tip_already_applied'] = '1';
                    $bookingpress_complete_payment_data_vars['tip_applied_status'] = 'success';
                }

                return $bookingpress_complete_payment_data_vars;
            }

            function bookingpress_modify_insert_data_func( $bookingpress_payment_id, $bookingpress_appointment_id, $bookingpress_final_payment_data ){

                global $wpdb,$tbl_bookingpress_payment_logs, $tbl_bookingpress_appointment_bookings;
                
                if( !empty($bookingpress_payment_id) && !empty($bookingpress_appointment_id)){

                    $bookingpress_tip_amount = !empty($bookingpress_final_payment_data['tip_amount']) ? $bookingpress_final_payment_data['tip_amount'] : 0;
                    $bookingpress_final_total_amt = !empty($bookingpress_final_payment_data['total_payable_amount']) ? $bookingpress_final_payment_data['total_payable_amount'] : 0;

                    $bookingpress_update_payment_logs_data = array(
                        'bookingpress_tip_amount' => $bookingpress_tip_amount,
                        'bookingpress_payment_amount' => $bookingpress_final_total_amt,
                        'bookingpress_paid_amount' => $bookingpress_final_total_amt,
                        'bookingpress_total_amount' => $bookingpress_final_total_amt,
                    );

                    $bookingpress_update_bookingappointment_data = array(
                        'bookingpress_tip_amount' => $bookingpress_tip_amount,
                        'bookingpress_paid_amount' => $bookingpress_final_total_amt,
                        'bookingpress_total_amount' => $bookingpress_final_total_amt,
                    );
                    
                    $wpdb->update($tbl_bookingpress_payment_logs, $bookingpress_update_payment_logs_data, array('bookingpress_payment_log_id' => $bookingpress_payment_id));
                    $wpdb->update($tbl_bookingpress_appointment_bookings, $bookingpress_update_bookingappointment_data, array('bookingpress_appointment_booking_id' => $bookingpress_appointment_id));
                }
            }	

            function bookingpress_add_complete_payment_method_func($bookingpress_add_complete_payment_method_data ){
                
                $bookingpress_nonce = esc_html(wp_create_nonce('bpa_wp_nonce'));

                $bookingpress_add_complete_payment_method_data .= '

                    isNumber: function(evt) {
                        const vm = this;
                        this.appointment_step_form_data.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                    },

                    bookingpress_remove_tip_amount(){
                        const vm = this
                        vm.appointment_step_form_data.tip_amount = ""
                        vm.appointment_step_form_data.tip_amount_with_currency = ""
                        vm.tip_apply_code_msg = ""
                        vm.bookingpress_recalculate_payable_amount()
                        vm.bpa_tip_apply_disabled = 0
                        vm.tip_applied_status = "error"
                        vm.tip_apply_amount = ""
                        /* vm.bookingpress_get_final_step_amount() */
                    },

                    bookingpress_edit_tip_amount(){

                        const vm = this;
                        vm.tip_apply_code_msg = "";
                        vm.appointment_step_form_data.applied_tip_amount = vm.appointment_step_form_data.tip_amount;
                        vm.appointment_step_form_data.tip_amount = "";
                        vm.appointment_step_form_data.tip_amount_with_currency = "$";
                        vm.bpa_tip_apply_disabled = 0;
                        vm.tip_applied_status = "error";
                        vm.tip_apply_amount = "";
                        vm.bookingpress_recalculate_payable_amount();
                    },

                    bookingpress_apply_tip_amount(){
                        const vm = this
                        vm.tip_apply_loader = "1"
                        var bookingpress_apply_tip_data = {};
                        bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount";
                        bookingpress_apply_tip_data.appointment_details = JSON.stringify( vm.appointment_step_form_data );
                        
                        var bkp_wpnonce_pre = "' . $bookingpress_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }

                        bookingpress_apply_tip_data._wpnonce = bkp_wpnonce_pre_fetch;
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                        .then( function (response) {
                            vm.tip_apply_loader = "0"
                            vm.tip_applied_status = response.data.variant;
                            if(response.data.variant == "error"){
                                vm.tip_apply_code_msg = response.data.msg
                            } else {
                                vm.tip_apply_code_msg = response.data.msg
                                vm.tip_apply_amount = "+"+response.data.tip_amount_with_currency
		                        vm.bpa_tip_apply_disabled = 1
                                vm.bookingpress_recalculate_payable_amount();
                                /* vm.bookingpress_get_final_step_amount() */
                            }
                        }.bind(this) )
                        .catch( function (error) {
                            vm.bookingpress_set_error_msg(error)
                        });
                    },';
                
                
                return $bookingpress_add_complete_payment_method_data;
            }

            function bookingpress_payment_add_content_after_subtotal_data_frontend_func(){ ?>

                <div class="bpa-fm--bs-amount-item bpa-is-coupon-applied" v-if="tip_applied_status == 'success'">
                    <div class="bpa-bs-ai__item">
                        {{tip_applied_title}}
                    </div>
                    <div class="bpa-bs-ai__item bpa-is-tip__price">
                        <div class="bpa-is-tip-edit-icon">
                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" @click="bookingpress_edit_tip_amount">
                                <g clip-path="url(#clip0_3299_6763)">
                                    <path d="M2 11.6393V13.666C2 13.8526 2.14667 13.9993 2.33333 13.9993H4.36C4.44667 13.9993 4.53333 13.966 4.59333 13.8993L11.8733 6.62598L9.37333 4.12598L2.1 11.3993C2.03333 11.466 2 11.546 2 11.6393Z" fill="#727E95"/>
                                    <path d="M13.8059 3.75305L12.2459 2.19305C11.9859 1.93305 11.5659 1.93305 11.3059 2.19305L10.0859 3.41305L12.5859 5.91305L13.8059 4.69305C14.0659 4.43305 14.0659 4.01305 13.8059 3.75305Z" fill="#727E95"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_3299_6763">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        +{{ appointment_step_form_data.tip_amount_with_currency }}
                    </div>
                </div>
                <div class="bpa-fm--bs__coupon-module-textbox" v-if="tip_applied_status == 'error' && ( typeof appointment_step_form_data.is_waiting_list == 'undefined' || appointment_step_form_data.is_waiting_list == false )">
                    <div class="bpa-cmt__left">
                        <span class="bpa-front-form-label">{{tip_label_txt}}</span>
                    </div>
                    <div class="bpa-cmt__right">                    
                        <el-input class="bpa-front-form-control" v-model="appointment_step_form_data.tip_amount" :placeholder="tip_placeholder_txt" @input="isNumber($event)" :disabled="bpa_tip_apply_disabled"></el-input>
                        <div class="bpa-bs__coupon-validation --is-error" v-if="tip_applied_status == 'error' && tip_apply_code_msg != ''">
                            <span class="material-icons-round">error_outline</span>
                            <p>{{ tip_apply_code_msg }}</p>
                        </div>
                        <div class="bpa-bs__coupon-validation --is-success" v-if="tip_applied_status == 'success' && tip_apply_code_msg != ''">
                            <span class="material-icons-round">check_circle</span>
                            <p>{{ tip_apply_code_msg }}</p>
                        </div>
                        <el-button class="bpa-front-btn bpa-front-btn--primary" :class="(tip_apply_loader == '1') ? 'bpa-front-btn--is-loader' : ''" :disabled="bpa_tip_apply_disabled" @click="bookingpress_apply_tip_amount">
                            <span class="bpa-btn__label" v-if="bpa_tip_apply_disabled == 0">{{tip_button_txt}}</span>
                            <span class="bpa-btn__label" v-else><?php esc_html_e( 'Applied', 'bookingpress-tip' ); ?></span>
                            <div class="bpa-front-btn--loader__circles">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </el-button>
                    </div>
                </div>
            <?php }

            function bookingpress_modify_appointment_default_field_zapier_func( $bookingpress_appointment_default_field, $id, $type ){
                $tip_amount = array('tip_amt');
                $bookingpress_appointment_default_field = array_merge( $tip_amount, $bookingpress_appointment_default_field);
                return $bookingpress_appointment_default_field;

            }

            function bookingpress_appointment_data_relpace_outside_func( $bookingpress_data, $bookingpress_appointment_data, $bookingpress_total_field ){

                global $BookingPress;

                if(in_array('tip_amt',$bookingpress_total_field)) {

                    $bookingpress_currency_name = !empty( $bookingpress_appointment_data['bookingpress_service_currency'] ) ? $bookingpress_appointment_data['bookingpress_service_currency'] : '';
                    $bookingpress_currency_symbol = $BookingPress->bookingpress_get_currency_symbol( $bookingpress_currency_name );
                    $bookingpress_tip_amount = !empty($bookingpress_appointment_data['bookingpress_tip_amount']) ? esc_html($bookingpress_appointment_data['bookingpress_tip_amount']) : '';
                    $bookingpress_tip_amount_with_currency  = $BookingPress->bookingpress_price_formatter_with_currency_symbol( $bookingpress_tip_amount , $bookingpress_currency_symbol );     
                    $bookingpress_data['tip_amt'] = $bookingpress_tip_amount_with_currency;
                }     

                return $bookingpress_data;
            }

            function bookingpress_add_global_option_data_func( $global_data ){

                $bookingpress_appointment_placeholders = json_decode($global_data['appointment_placeholders'], TRUE);
                $bookingpress_appointment_placeholders[] = array(
                    'value' => '%tip_amt%',
                    'name' => '%tip_amt%',
                );
                $global_data['appointment_placeholders'] = wp_json_encode($bookingpress_appointment_placeholders);  
                return $global_data;
            }

            function bookingpress_change_label_value_for_invoice_func( $bookingpress_invoice_html_view, $log_detail ){

                global $BookingPress;

                $bookingpress_currency_name   = !empty($log_detail['bookingpress_payment_currency']) ? esc_html($log_detail['bookingpress_payment_currency']) : '';
                $bookingpress_currency_symbol = $BookingPress->bookingpress_get_currency_symbol( $bookingpress_currency_name );

                //fetch tip amount
                $bookingpress_tip_amount = !empty($log_detail['bookingpress_tip_amount']) ? esc_html( $log_detail['bookingpress_tip_amount'] ) : 0;               
                $bookingpress_final_tip_amount  = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_tip_amount, $bookingpress_currency_symbol);

                $bookingpress_invoice_html_view = str_replace('{tip_amt}', $bookingpress_final_tip_amount, $bookingpress_invoice_html_view);

                return $bookingpress_invoice_html_view;
            }

            function bookingpress_change_total_amount_outside_func( $bookingpress_total_amount, $log_detail){

                $bookingpress_tip_amount = !empty($log_detail['bookingpress_tip_amount']) ? $log_detail['bookingpress_tip_amount'] : '';

                if( !empty( $bookingpress_tip_amount) ){
                    $bookingpress_total_amount = $bookingpress_total_amount + $bookingpress_tip_amount;
                }
                return $bookingpress_total_amount;
            }

            function bookingpress_change_paid_amount_outside_func( $bookingpress_paid_amount, $log_detail){

                $bookingpress_tip_amount = !empty($log_detail['bookingpress_tip_amount']) ? $log_detail['bookingpress_tip_amount'] : '';
                if( !empty( $bookingpress_tip_amount) ){
                    $bookingpress_paid_amount = $bookingpress_paid_amount + $bookingpress_tip_amount;
                }
                return $bookingpress_paid_amount;
            }

            function bookingpress_add_setting_dynamic_data_fields_func( $bookingpress_dynamic_setting_data_fields ){    

                if(isset($bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'])){

                    $bookingpress_dynamic_setting_data_fields_other = $bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'];

                    $bookingpress_dynamic_setting_data_fields_tip['bookingpress_invoice_tag_list'] = array(                
                        array( 'group_tag_name' =>  __('Tip','bookingpress-tip'),
                            'tag_details' => array(
                                array( 'tag_name' =>  '{tip_amt}',
                                ),  
                            ),    
                        ),  
                    );
                    $bookingpress_dynamic_setting_data_fields['bookingpress_invoice_tag_list'] = array_merge( $bookingpress_dynamic_setting_data_fields_other, $bookingpress_dynamic_setting_data_fields_tip['bookingpress_invoice_tag_list'] );
                }
                return $bookingpress_dynamic_setting_data_fields;
            }

            function bookingpress_modify_email_content_filter_func( $template_content, $bookingpress_appointment_data ) {
                global $BookingPress;
                if(!empty($bookingpress_appointment_data)){
                    
                    $bookingpress_tip_amount = !(empty($bookingpress_appointment_data['bookingpress_tip_amount'])) ? $bookingpress_appointment_data['bookingpress_tip_amount'] : '-';
                    $template_content = str_replace('%tip_amt%', $bookingpress_tip_amount, $template_content);
                }
                return $template_content;
            }

            function bookingpress_modify_appointment_data_fields_func( $bookingpress_appointment_vue_data_fields ) {
                $bookingpress_appointment_vue_data_fields['appointment_formdata']['tip_amount'] = '';
                $bookingpress_appointment_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_appointment_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_appointment_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_appointment_vue_data_fields['tip_applied_status'] = '';
                return $bookingpress_appointment_vue_data_fields; 
            }

            function bookingpress_after_update_appointment_func($bookingpress_appointment_id){
                global $wpdb, $BookingPress, $BookingPressPro, $tbl_bookingpress_appointment_bookings, $tbl_bookingpress_payment_logs;
    
                $bookingpress_appointment_updated_data = !empty($_POST['appointment_data']) ? array_map(array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_data']) : array(); // phpcs:ignore
                if(!empty($bookingpress_appointment_updated_data)){
                    $bookingpress_appointment_payment_logs= $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_payment_logs} WHERE bookingpress_appointment_booking_ref = %d", $bookingpress_appointment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                    if(!empty($bookingpress_appointment_payment_logs)){

                        
                        $bookingpress_tip_amount = !empty($bookingpress_appointment_updated_data['tip_amount']) ? $bookingpress_appointment_updated_data['tip_amount'] : array();

                        $bookingpress_update_payment_logs_data = array(
                            'bookingpress_tip_amount' => $bookingpress_tip_amount,
                        );
    
                        $affected_rows = $wpdb->update($tbl_bookingpress_payment_logs, $bookingpress_update_payment_logs_data, array('bookingpress_payment_log_id' => $bookingpress_appointment_payment_logs['bookingpress_payment_log_id']));
                    }
                }
            }

            function bookingpress_modify_calendar_data_fields_func( $bookingpress_calendar_vue_data_fields ) {

                $bookingpress_calendar_vue_data_fields['appointment_formdata']['tip_amount'] = '';
                $bookingpress_calendar_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_calendar_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_calendar_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_calendar_vue_data_fields['tip_applied_status'] = '';

                return $bookingpress_calendar_vue_data_fields;
            }

            function bookingpress_modify_dashboard_data_fields_func( $bookingpress_dashboard_vue_data_fields ){

                $bookingpress_dashboard_vue_data_fields['appointment_formdata']['tip_amount'] = '';
                $bookingpress_dashboard_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_dashboard_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_dashboard_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_dashboard_vue_data_fields['tip_applied_status'] = '';

                return $bookingpress_dashboard_vue_data_fields;
            }
            

            function bookingpress_appointment_add_dynamic_vue_methods_func(){?>
                isNumber: function(evt) {
                        const vm = this
                        this.appointment_formdata.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                    },

                bookingpress_apply_tip_amount(){
                    const vm = this
                    vm.tip_apply_loader = "1"
                    var bookingpress_apply_tip_data = {};
                    bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount_backend"
                    bookingpress_apply_tip_data.tip_amount = vm.appointment_formdata.tip_amount
                    bookingpress_apply_tip_data.selected_service = vm.appointment_formdata.appointment_selected_service
                    bookingpress_apply_tip_data.payable_amount = vm.appointment_formdata.total_amount
                    bookingpress_apply_tip_data._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>'
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                    .then( function (response) {
                        vm.tip_apply_loader = "0"
                        vm.tip_applied_status = response.data.variant;
                        if(response.data.variant == "error"){
                            vm.tip_apply_code_msg = response.data.msg
                        } else {
                            vm.tip_apply_code_msg = response.data.msg
                                vm.appointment_formdata.tip_amount_with_currency = response.data.tip_amount_with_currency;
                                vm.bpa_tip_apply_disabled = 1
                            }
                            //vm.bookingpress_calculate_prices()
                            vm.bookingpress_admin_get_final_step_amount();
                        }.bind(this) )
                        .catch( function (error) {
                            vm.bookingpress_set_error_msg(error)
                        });
                        this.$forceUpdate();
                    }, 

            <?php }

            function bookingpress_edit_appointment_details_func(){?>

                const vm3 = this

                if(response.data.bookingpress_tip_amount != ""){
                    var bookingpress_tip_amount_details = JSON.parse(response.data.bookingpress_tip_amount);
                        vm3.appointment_formdata.tip_amount = bookingpress_tip_amount_details;
                        //vm3.bookingpress_apply_tip_amount();
                }

        <?php }

            function bookingpress_frontend_payment_section_modified_func( $bookingpress_customize_settings_details ){   
                global $BookingPress;

                $bookingpress_tip_label = $BookingPress->bookingpress_get_customize_settings('tip_label_txt', 'booking_form');
                ?>
                <div class="bpa-vac-pd__item" v-if="scope.row.bookingpress_tip_amount != '0' && scope.row.bookingpress_tip_amount != '' ">
                    <div class="bpa-vac-pd__label"><?php echo esc_html($bookingpress_tip_label); ?>:</div>
                    <div class="bpa-vac-pd__val">+{{scope.row.bookingpress_tip_amount_currency}}</div>
                </div>

            <?php  }

            function bookingpress_modify_my_appointments_data_externally_func( $bookingpress_appointments_data ){

                global $BookingPress;

                if( !empty($bookingpress_appointments_data)){
                    $bookingpress_tip_amount = !empty($bookingpress_appointments_data['bookingpress_tip_amount']) ? $bookingpress_appointments_data['bookingpress_tip_amount'] : 0;
					$bookingpress_appointments_data['bookingpress_tip_amount_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_tip_amount);
                }
                return $bookingpress_appointments_data;
            }

            function bookingpress_modify_payments_listing_data_func( $payment_logs_data ){
                global $BookingPress;
                if(!empty($payment_logs_data) && is_array($payment_logs_data) ){
                    foreach($payment_logs_data as $k => $v){       
                        foreach( $v['appointment_details'] as $key => $val ){
                            $bookingpress_tip_amount = $val['bookingpress_tip_amount'];
                            $currency_name = $val['bookingpress_service_currency'];
                            $currency_symbol = $BookingPress->bookingpress_get_currency_symbol($currency_name);
                        }
                        $payment_logs_data[$k]['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                        $payment_logs_data[$k]['bookingpress_tip_amount_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_tip_amount,$currency_symbol);
                    }
                }
                return $payment_logs_data;
            }

            function bookingpress_modify_payment_managepayment_section_func(){ ?>

                <div class="bpa-psw--body-row" v-if="scope.row.bookingpress_tip_amount != '' && scope.row.bookingpress_tip_amount != '0' ">													
                    <div class="bpa-psw__item" v-if="scope.row.bookingpress_tip_amount != '' && scope.row.bookingpress_tip_amount != '0'">
                        <div class="bpa-psw__item-title">
                            <p><?php esc_html_e('Tip', 'bookingpress-tip'); ?></p>
                        </div>
                        <div class="bpa-psw__item-amount">
                            <p>+{{scope.row.bookingpress_tip_amount_currency}}</p>
                        </div>
                    </div>
                </div>

            <?php }

            function bookingpress_modify_payment_section_func(){ ?>
                    
                <div class="bpa-pd__item" v-if="scope.row.bookingpress_tip_amount != '0' && scope.row.bookingpress_tip_amount != '' ">
                    <span><?php esc_html_e('Tip', 'bookingpress-tip'); ?></span>
                    <p>{{ scope.row.bookingpress_tip_amount_with_currency }}</p>
                </div>
            <?php }

            function bookingpress_modify_outside_total_amount_func( $retrun_calculate_data, $bookingpress_payment_id, $bookingpress_selected_currency ){

                global $tbl_bookingpress_payment_logs, $wpdb, $BookingPress;
                /* $bookingpess_retrun_final_amount = $retrun_calculate_data['final_total_amount']; */
                $bookingpess_retrun_final_amount = $retrun_calculate_data['total_amount'];
                $payment_log_details = $wpdb->get_row($wpdb->prepare("SELECT bookingpress_tip_amount FROM {$tbl_bookingpress_payment_logs} WHERE bookingpress_payment_log_id = %d", $bookingpress_payment_id), ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_payment_logs is a table name. false alarm
                if(!empty($payment_log_details['bookingpress_tip_amount'])){
                    $bookingpress_tip_amount = $payment_log_details['bookingpress_tip_amount'];
                    if( $retrun_calculate_data['is_deposit_enable'] == 1 ){
                        if(!empty($bookingpress_tip_amount) || !empty( $retrun_calculate_data['due_amount']) ){

                            $bookingpress_due_amount = $retrun_calculate_data['due_amount'] + $bookingpress_tip_amount;
                            $retrun_calculate_data['due_amount'] = $bookingpress_due_amount;
                            $retrun_calculate_data['due_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_due_amount, $bookingpress_selected_currency);
                        }
                    }
                    $final_calculate_tip_amount = $bookingpess_retrun_final_amount + $bookingpress_tip_amount;
                    $retrun_calculate_data['bookingpress_tip_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol( $bookingpress_tip_amount, $bookingpress_selected_currency );
                    $retrun_calculate_data['total_amount'] = $final_calculate_tip_amount;
                    $retrun_calculate_data['total_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($final_calculate_tip_amount, $bookingpress_selected_currency);
                }
                return $retrun_calculate_data;
            }


            function bookingpress_return_calculated_details_modify_outside_func($bookingpress_return_calculated_details, $bookingpress_calculated_payment_details, $bookingpress_appointment_id, $bookingpress_payment_id){

                $bookingpress_tip_amount = !empty($bookingpress_calculated_payment_details['bookingpress_tip_amount_with_currency']) ? $bookingpress_calculated_payment_details['bookingpress_tip_amount_with_currency'] : 0;
                $bookingpress_return_calculated_details['bookingpress_tip_amount_with_currency'] = $bookingpress_tip_amount;
                return $bookingpress_return_calculated_details;
            }


            function bookingpress_modify_appointment_data_func($bookingpress_appointment_data){
                global $bookingpress_pro_appointment, $tbl_bookingpress_payment_logs, $wpdb, $BookingPress;

                if(!empty($bookingpress_appointment_data) && is_array($bookingpress_appointment_data) ){
                    foreach($bookingpress_appointment_data as $k => $v){

                        $bookingpress_appointment_id = $v['appointment_id'];
                        $bookingpress_payment_log_id = $v['payment_id'];

                        $bookingpress_payment_details = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$tbl_bookingpress_payment_logs} WHERE bookingpress_payment_log_id = %d", $bookingpress_payment_log_id), ARRAY_A);                        
                        if(!empty($bookingpress_payment_details)){
                            $bookingpress_appointment_data[$k]['bookingpress_tip_amount'] = $bookingpress_payment_details['bookingpress_tip_amount'];
                            $currency_name = $bookingpress_payment_details['bookingpress_payment_currency'];
                            $currency_symbol = $BookingPress->bookingpress_get_currency_symbol($currency_name);
                            $bookingpress_appointment_data[$k]['bookingpress_tip_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol($bookingpress_payment_details['bookingpress_tip_amount'],$currency_symbol);
                        }
                    }
                }
                return $bookingpress_appointment_data;
            }

            function bookingpress_modify_payment_log_fields_before_insert_func( $payment_log_data, $entry_data){
                $bookingpress_tip_amount = !empty($entry_data['bookingpress_tip_amount'] ) ? $entry_data['bookingpress_tip_amount'] : 0;
                $payment_log_data['bookingpress_tip_amount'] = $bookingpress_tip_amount;

                return $payment_log_data;
            }

            function bookingpress_modify_appointment_booking_fields_before_insert_func( $appointment_booking_fields, $entry_data){
                
                $bookingpress_tip_amount = !empty($entry_data['bookingpress_tip_amount'] ) ? $entry_data['bookingpress_tip_amount'] : 0;
                $appointment_booking_fields['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                return $appointment_booking_fields;
            }

            function bookingpress_modify_appointment_booking_fields_func( $appointment_booking_field, $entry_data, $bookingpress_appointment_data ){
                         
                $bookingpress_tip_amount = !empty($entry_data['bookingpress_tip_amount']) ? $entry_data['bookingpress_tip_amount'] : 0;
                $appointment_booking_field['bookingpress_tip_amount'] = $bookingpress_tip_amount;

                if( !empty($bookingpress_appointment_data)){
                    $bookingpress_tip_amount = !empty($bookingpress_appointment_data['tip_amount']) ? $bookingpress_appointment_data['tip_amount'] : 0;
                    $appointment_booking_field['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                    //$appointment_booking_field['bookingpress_tip_amount_with_currency'] = $bookingpress_tip_amount;
                }

                return $appointment_booking_field;
            }   

            function bookingpress_modify_entry_data_func($bookingpress_entry_details, $posted_data){

                $bookingpress_tip_amount = !empty($posted_data['tip_amount']) ? $posted_data['tip_amount'] : 0;
                $bookingpress_entry_details['bookingpress_tip_amount'] = $bookingpress_tip_amount;

                return $bookingpress_entry_details;
            }

            function bookingpress_add_content_after_subtotal_data_func_data() { ?>
                <div class="bpa-aaf-pd__tip-module">
                    <div class="bpa-aaf--bs__tip-module-textbox" v-if="tip_applied_status != 'success'">
                        <el-row>
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <span class="bpa-form-label"><?php esc_html_e( 'Give a tip', 'bookingpress-tip' ); ?></span>
                                <el-input class="bpa-form-control" v-model="appointment_formdata.tip_amount" @input="isNumber($event)" placeholder="<?php esc_html_e( 'Enter tip amount', 'bookingpress-tip' ); ?>" :disabled="bpa_tip_apply_disabled"></el-input>
                                <div class="bpa-bs__tip-validation --is-error" v-if="tip_applied_status == 'error' && tip_apply_code_msg != ''">
                                    <span class="material-icons-round">error_outline</span>
                                    <p>{{ tip_apply_code_msg }}</p>
                                </div>
                                <div class="bpa-bs__tip-validation --is-success" v-if="tip_applied_status == 'success' && tip_apply_code_msg != ''">
                                    <span class="material-icons-round">check_circle</span>
                                    <p>{{ tip_apply_code_msg }}</p>
                                </div>
                                <el-button class="bpa-btn bpa-btn__medium bpa-btn--primary" @click="bookingpress_apply_tip_amount" :disabled="bpa_tip_apply_disabled">
                                    <span class="bpa-btn__label" v-if="bpa_tip_apply_disabled == 0"><?php esc_html_e( 'Add', 'bookingpress-tip' ); ?></span>
                                    <span class="bpa-btn__label" v-else><?php esc_html_e( 'Applied', 'bookingpress-tip' ); ?></span>
                                    <div class="bpa-btn--loader__circles">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                </el-button>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="bpa-fm--bs-amount-item bpa-is-tip-applied bpa-is-hide-stroke" v-if="tip_applied_status == 'success'">
                        <el-row>
                            <el-col :xs="12" :sm="12" :md="24" :lg="22" :xl="22">
                                <h4><?php esc_html_e( 'Tip Applied', 'bookingpress-tip' ); ?></h4>
                            </el-col>
                            <el-col :xs="12" :sm="12" :md="24" :lg="2" :xl="2">
                                <div class="bpa-ta__amount">
                                    <a class="bpa-taa__edit-icon" href="#" @click="bookingpress_remove_tip_amount"><span class="material-icons-round">mode_edit</span></a>
                                    <h4 class="is-price">+{{ appointment_formdata.tip_amount_with_currency }}</h4>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            <?php }

            function bookingpress_add_content_after_subtotal_data_func(){
                    global $bookingpress_tip_applied_status;
                 ?>            
                <div class="bpa-fm--bs-amount-item bpa-is-coupon-applied" v-if="tip_applied_status == 'success'">
                        <?php $bookingpress_tip_applied_status = 1; ?>
                    <div class="bpa-bs-ai__item">
                        {{tip_applied_title}}
                    </div>
                    <div class="bpa-bs-ai__item bpa-is-ca__price bpa-is-tip__price">
                        <div class="bpa-is-tip-edit-icon">
                            <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" @click="bookingpress_edit_tip_amount">
                                <g clip-path="url(#clip0_3299_6763)">
                                    <path d="M2 11.6393V13.666C2 13.8526 2.14667 13.9993 2.33333 13.9993H4.36C4.44667 13.9993 4.53333 13.966 4.59333 13.8993L11.8733 6.62598L9.37333 4.12598L2.1 11.3993C2.03333 11.466 2 11.546 2 11.6393Z" fill="#727E95"/>
                                    <path d="M13.8059 3.75305L12.2459 2.19305C11.9859 1.93305 11.5659 1.93305 11.3059 2.19305L10.0859 3.41305L12.5859 5.91305L13.8059 4.69305C14.0659 4.43305 14.0659 4.01305 13.8059 3.75305Z" fill="#727E95"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_3299_6763">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        +{{ appointment_step_form_data.tip_amount_with_currency}}
                    </div>
                </div>			
                <!-- <div class="bpa-fm--bs__coupon-module-textbox" v-if="tip_applied_status == 'error' && appointment_step_form_data.is_waiting_list == false "> -->
                    
                <div class="bpa-fm--bs__coupon-module-textbox" v-if="tip_applied_status == 'error' && ( typeof appointment_step_form_data.is_waiting_list == 'undefined' || appointment_step_form_data.is_waiting_list == false )">
                <?php $bookingpress_tip_applied_status = 0; ?>
                    <div class="bpa-cmt__left">
                        <span class="bpa-front-form-label">{{tip_label_txt}}</span>
                    </div>
                    <div class="bpa-cmt__right">
                        <el-input class="bpa-front-form-control" v-model="appointment_step_form_data.tip_amount" @input="isNumber($event)" :placeholder="tip_placeholder_txt" :disabled="bpa_tip_apply_disabled"></el-input>
                        <div class="bpa-bs__coupon-validation --is-error" v-if="tip_applied_status == 'error' && tip_apply_code_msg != ''">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 7c.55 0 1 .45 1 1v4c0 .55-.45 1-1 1s-1-.45-1-1V8c0-.55.45-1 1-1zm-.01-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-3h-2v-2h2v2z"/></svg>
                            <p>{{ tip_apply_code_msg }}</p>
                        </div>
                        <div class="bpa-bs__coupon-validation --is-success" v-if="tip_applied_status == 'success' && tip_apply_code_msg != ''">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
                            <p>{{ tip_apply_code_msg }}</p>
                        </div>
                        <el-button class="bpa-front-btn bpa-front-btn--primary" :class="(tip_apply_loader == '1') ? 'bpa-front-btn--is-loader' : ''" @click="bookingpress_apply_tip_amount" :disabled="bpa_tip_apply_disabled">
                            <span class="bpa-btn__label" v-if="bpa_tip_apply_disabled == 0">{{tip_button_txt}}</span>
                            <span class="bpa-btn__label" v-else><?php esc_html_e( 'Applied', 'bookingpress-tip' ); ?></span>
                            <div class="bpa-front-btn--loader__circles">
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </el-button>
                    </div>
                </div>
            <?php }

            function bookingpress_apply_tip_amount_func(){
                global $wpdb, $BookingPress;
                $wpnonce               = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( $_REQUEST['_wpnonce'] ) : '';
                $bpa_verify_nonce_flag = wp_verify_nonce( $wpnonce, 'bpa_wp_nonce' );
                $response              = array();
                if ( ! $bpa_verify_nonce_flag ) {
                    $response                = array();
                    $response['variant']     = 'error';
                    $response['title']       = esc_html__( 'Error', 'bookingpress-tip' );
                    $response['msg']         = esc_html__( 'Sorry, Your request can not be processed due to security reason.', 'bookingpress-tip' );
                    echo wp_json_encode( $response );
                    die();
                }
                $response                         = array();
                $response['variant']              = 'error';
                $response['title']                = __( 'Error', 'bookingpress-tip' );
                $response['msg']                  = __( 'Something went wrong..', 'bookingpress-tip' );
                $tip_error_msg = $BookingPress->bookingpress_get_customize_settings('tip_error_msg', 'booking_form');

                if( !empty( $_POST['appointment_details'] ) && !is_array( $_POST['appointment_details'] ) ){
                    $_POST['appointment_details'] = json_decode( stripslashes_deep( $_POST['appointment_details'] ), true );
                }
                $bookingpress_tip_amount      = ! empty( $_POST['appointment_details']['tip_amount'] ) ? intval( $_POST['appointment_details']['tip_amount'] ) : 0;
                $bookingpress_selected_service = ! empty( $_POST['appointment_details']['selected_service'] ) ? intval( $_POST['appointment_details']['selected_service'] ) : 0;
                $bookingpress_payable_amount   = ! empty( $_POST['appointment_details']['total_payable_amount'] ) ? floatval( $_POST['appointment_details']['total_payable_amount'] ) : 0; 
                $bookingpress_appointment_details = !empty( $_POST['appointment_details'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field' ), $_POST['appointment_details'] ) : array(); 

                if(!empty($bookingpress_appointment_details['tip_amount'])){
                        $bookingpress_after_tip_amounts = $this->bookingpress_calculate_bookingpress_tip_amount( $bookingpress_tip_amount, $bookingpress_payable_amount );
                        $response['variant']     = $bookingpress_after_tip_amounts['tip_status'];
                        $response['title']       = ( $bookingpress_after_tip_amounts['tip_status'] == 'error' ) ? __( 'Error', 'bookingpress-tip' ) : __( 'Success', 'bookingpress-tip' );
                        $response['msg']         = $bookingpress_after_tip_amounts['msg'];

                    if ( is_array( $bookingpress_after_tip_amounts ) && ! empty( $bookingpress_after_tip_amounts ) && $bookingpress_after_tip_amounts['tip_status'] == 'success') {
                        $response['final_payable_amount'] = ! empty( $bookingpress_after_tip_amounts['final_payable_amount'] ) ? floatval( $bookingpress_after_tip_amounts['final_payable_amount'] ) : 0;
                        $tip_amount_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol( intval( $bookingpress_tip_amount ) );	
                        $response['tip_amount_with_currency']  = $tip_amount_with_currency;
                    } else {
                        $response['variant']              = 'error';
						$response['title']                = __( 'Error', 'bookingpress-tip' );
						/* $response['msg']                  = __( 'Please enter tip amount', 'bookingpress-tip' ); */
                        $response['msg']                  = !empty($tip_error_msg) ? $tip_error_msg : '__("Please enter tip amount,"bookingpress-tip")';

                    
                    }
                } else {
                    $response['variant']              = 'error';
					$response['title']                = __( 'Error', 'bookingpress-tip' );
					/* $response['msg']                  = __( 'Please enter tip amount', 'bookingpress-tip' ); */
                    $response['msg']                  = !empty($tip_error_msg) ? $tip_error_msg : '__("Please enter tip amount,"bookingpress-tip")';

                }
                echo wp_json_encode( $response );
                exit();
            }

            function bookingpress_calculate_bookingpress_tip_amount( $bookingpress_tip_amount, $bookingpress_payable_amount ){

                $error_msg = __('Please add tip amount','bookingpress-tip');

                $response                  = array();
			    $response['tip_status'] = 'error';
			    $response['msg']           = $error_msg;
			    $response['final_payable_amount']   = array();

                if( !empty($bookingpress_tip_amount) ){
                    $final_payable_amount = $bookingpress_tip_amount + $bookingpress_payable_amount;
                    $response['tip_status'] = 'success';
                    $response['msg']           = __( 'Tip applied successfully', 'bookingpress-tip' );
                    $response['final_payable_amount'] = $final_payable_amount;
                }else {
                    $response['msg'] = $error_msg;
                }

                return $response;

            }
            function bookingpress_modify_calculated_appointment_details_func( $bookingpress_appointment_details ){

                global $BookingPress;

                $tip_amount = ! empty( $bookingpress_appointment_details['tip_amount'] ) ? intval( $bookingpress_appointment_details['tip_amount'] ) : 0;
                $final_payable_amount = ! empty( $bookingpress_appointment_details['bpa_final_payable_amount'] ) ? floatval( $bookingpress_appointment_details['bpa_final_payable_amount'] ) : 0;

                if( !empty($tip_amount)){

                    $bookingpress_after_tip_amounts = $this->bookingpress_calculate_bookingpress_tip_amount( $tip_amount, $final_payable_amount );

                    if( is_array( $bookingpress_after_tip_amounts) && !empty( $bookingpress_after_tip_amounts) ){

                        $total_payable_amount = $final_payable_amount = ! empty( $bookingpress_after_tip_amounts['final_payable_amount'] ) ? floatval( $bookingpress_after_tip_amounts['final_payable_amount'] ) : 0;
                        $tip_amount_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol( $tip_amount );
                        $bookingpress_service_price = $BookingPress->bookingpress_price_formatter_with_currency_symbol( $total_payable_amount );
                        $bookingpress_appointment_details['tip_amount_with_currency'] = $tip_amount_with_currency;
                        $bookingpress_appointment_details['total_payable_amount_with_currency'] = $bookingpress_service_price;
                        $bookingpress_appointment_details['total_payable_amount'] = $total_payable_amount;
                        $bookingpress_appointment_details['bpa_final_payable_amount'] = $total_payable_amount;
                        
                    }
                } else {
                    if( !empty( $bookingpress_appointment_details['applied_tip_amount'] ) ){
                        $bookingpress_appointment_details['total_payable_amount'] = $bookingpress_appointment_details['bpa_final_payable_amount'];

                        $bookingpress_appointment_details['total_payable_amount_with_currency'] = $BookingPress->bookingpress_price_formatter_with_currency_symbol( $bookingpress_appointment_details['total_payable_amount'] );
                        $bookingpress_appointment_details['applied_tip_amount'] = '';
                    }
                }

                return $bookingpress_appointment_details;
            }
            
            function bookingpress_booking_dynamic_vue_methods_func($bookingpress_vue_methods_data){
                global $wpdb, $BookingPress;
                $bookingpress_create_nonce      = wp_create_nonce( 'bpa_wp_nonce' );
                
                $bookingpress_after_tip_added = '';
                $bookingpress_after_tip_added = apply_filters('bookingpress_after_tip_added', $bookingpress_after_tip_added);

                $bookingpress_vue_methods_data .= '

                    isNumber: function(evt) {
                        const vm = this;
                        this.appointment_step_form_data.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                    },

                    bookingpress_remove_tip_amount(){
                        const vm = this;
                        vm.appointment_step_form_data.tip_amount = "";
                        vm.appointment_step_form_data.tip_amount_with_currency = "";
                        vm.tip_apply_code_msg = "";
                        vm.bpa_tip_apply_disabled = 0;
                        vm.tip_applied_status = "error";
                        vm.tip_apply_amount = "";

                        vm.bookingpress_get_final_step_amount()
                    },

                    bookingpress_edit_tip_amount(){
                        const vm = this;
                        vm.tip_apply_code_msg = "";
                        vm.bpa_tip_apply_disabled = 0;
                        vm.tip_applied_status = "error";
                        vm.tip_apply_amount = "";
                        vm.bookingpress_get_final_step_amount();
                    },

                    bookingpress_apply_tip_amount(){
                        const vm = this;
                        vm.tip_apply_loader = "1";
                        var bookingpress_apply_tip_data = {};
                        bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount";
                        bookingpress_apply_tip_data.appointment_details = JSON.stringify( vm.appointment_step_form_data );

                        var bkp_wpnonce_pre = "' . $bookingpress_create_nonce . '";
                        var bkp_wpnonce_pre_fetch = document.getElementById("_wpnonce");
                        if(typeof bkp_wpnonce_pre_fetch=="undefined" || bkp_wpnonce_pre_fetch==null)
                        {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre;
                        }
                        else {
                            bkp_wpnonce_pre_fetch = bkp_wpnonce_pre_fetch.value;
                        }

                        bookingpress_apply_tip_data._wpnonce = bkp_wpnonce_pre_fetch;
                        axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                        .then( function (response) {
                                vm.tip_apply_loader = "0";
                                vm.tip_applied_status = response.data.variant;
                                if(response.data.variant == "error"){
                                    vm.tip_apply_code_msg = response.data.msg;
                                } else {
                                    vm.tip_apply_code_msg = response.data.msg;
                                    vm.tip_apply_amount = "+"+response.data.tip_amount_with_currency;
		                            vm.bpa_tip_apply_disabled = 1
                                    '.$bookingpress_after_tip_added.'
                                }
                                /* vm.bookingpress_recalculate_payable_amount(); */
                                vm.bookingpress_get_final_step_amount();
                            }.bind(this) )
                            .catch( function (error) {
                                vm.bookingpress_set_error_msg(error);
                            });
                    },
                ';

                return $bookingpress_vue_methods_data;
            }

            function bookingpress_admin_panel_vue_methods_func(){?>

                isNumber: function(evt) {
                    const vm = this;
                    this.appointment_formdata.tip_amount = event.target.value.replace(/[^0-9]/g, "");
                },

                bookingpress_remove_tip_amount(){
                    const vm = this;
                    vm.tip_apply_code_msg = "";
                    //vm.bookingpress_calculate_prices();
                    vm.bpa_tip_apply_disabled = 0;
                    vm.tip_applied_status = "error";
                    vm.tip_apply_amount = "";
                    vm.bookingpress_admin_get_final_step_amount();
                },

                bookingpress_apply_tip_amount(){
                    const vm = this;
                    vm.tip_apply_loader = "1";
                    var bookingpress_apply_tip_data = {};
                    bookingpress_apply_tip_data.action = "bookingpress_apply_tip_amount_backend"
                    bookingpress_apply_tip_data.tip_amount = vm.appointment_formdata.tip_amount
                    bookingpress_apply_tip_data.selected_service = vm.appointment_formdata.appointment_selected_service
                    bookingpress_apply_tip_data.payable_amount = vm.appointment_formdata.total_amount
                    bookingpress_apply_tip_data._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>'
                    axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( bookingpress_apply_tip_data ) )
                    .then( function (response) {
                        vm.tip_apply_loader = "0"
                        vm.tip_applied_status = response.data.variant;
                        if(response.data.variant == "error"){
                            vm.tip_apply_code_msg = response.data.msg
                        } else {
                            vm.tip_apply_code_msg = response.data.msg
                            vm.appointment_formdata.tip_amount_with_currency = response.data.tip_amount_with_currency;
                            vm.bpa_tip_apply_disabled = 1
                        }
                        //vm.bookingpress_calculate_prices()
                        vm.bookingpress_admin_get_final_step_amount();
                    }.bind(this) )
                    .catch( function (error) {
                        vm.bookingpress_set_error_msg(error)
                    });
                    this.$forceUpdate();
                },

            <?php }

            function bookingpress_apply_tip_amount_backend_func(){

                global $wpdb, $BookingPress;
                $response = array();

                $bpa_check_authorization = $this->bpa_check_authentication('bookingpress_apply_tip_amount_backend', true, 'bpa_wp_nonce');
                if( preg_match( '/error/', $bpa_check_authorization ) ){
                    $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                    $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-tip');
                    $response['variant'] = 'error';
                    $response['title'] = esc_html__( 'Error', 'bookingpress-tip');
                    $response['msg'] = $bpa_error_msg;
                    wp_send_json( $response );
                    die;
                }
                
                $bookingpress_tip_amount = ! empty( $_POST['tip_amount'] ) ? intval( $_POST['tip_amount'] ) : 0;
                $bookingpress_payable_amount   = ! empty( $_POST['payable_amount'] ) ? floatval( $_POST['payable_amount'] ) : 0; 

                    $bookingpress_after_tip_amounts = $this->bookingpress_calculate_bookingpress_tip_amount( $bookingpress_tip_amount, $bookingpress_payable_amount );

                    $response['variant']     = $bookingpress_after_tip_amounts['tip_status'];
                    $response['title']       = ( $bookingpress_after_tip_amounts['tip_status'] == 'error' ) ? __( 'Error', 'bookingpress-tip' ) : __( 'Success', 'bookingpress-tip' );
                    $response['msg']         = $bookingpress_after_tip_amounts['msg'];

                    if ( is_array( $bookingpress_after_tip_amounts ) && ! empty( $bookingpress_after_tip_amounts) && $bookingpress_after_tip_amounts['tip_status'] == 'success' ) {
                        $response['final_payable_amount'] = ! empty( $bookingpress_after_tip_amounts['final_payable_amount'] ) ? floatval( $bookingpress_after_tip_amounts['final_payable_amount'] ) : 0;
                        $tip_amount_with_currency = $BookingPress->bookingpress_price_formatter_with_currency_symbol( intval( $bookingpress_tip_amount ) );	
                        $response['tip_amount_with_currency']  = $tip_amount_with_currency;
                    } else {
                        $response['variant']              = 'error';
                        $response['title']                = __( 'Error', 'bookingpress-tip' );
                        $response['msg']                  = __( 'Please enter tip amount', 'bookingpress-tip' );
                    }
                
                echo wp_json_encode( $response );

                exit();
             
            }

            function bookingpress_modify_backend_appointment_data_func( $bookingpress_appointment_formdata, $bookingpress_subtotal_price){

                if( !empty( $bookingpress_appointment_formdata['tip_amount'] )){
                    
                    $bookingpress_appointment_formdata['tip_amount'] = $bookingpress_appointment_formdata['tip_amount'];
                }

                return $bookingpress_appointment_formdata;
            }
            
            function bookingpress_modify_backend_subtotal_price_func( $bookingpress_subtotal_price, $bookingpress_appointment_formdata ){

                if( !empty( $bookingpress_appointment_formdata['tip_amount']) ){
                    $bookingpress_subtotal_price = $bookingpress_subtotal_price + $bookingpress_appointment_formdata['tip_amount'];
                }
                /* if( !empty( $bookingpress_appointment_formdata['bookingpress_tip_amount']) ){
                    $bookingpress_subtotal_price = $bookingpress_subtotal_price + $bookingpress_appointment_formdata['bookingpress_tip_amount'];
                } */
                return $bookingpress_subtotal_price;
            }

            function bookingpress_modify_backend_add_appointment_entry_data_func($bookingpress_entry_details, $bookingpress_appointment_data){

                $bookingpress_tip_amount = !empty($bookingpress_appointment_data['tip_amount']) ? intval($bookingpress_appointment_data['tip_amount']) : '';
                if( !empty( $bookingpress_tip_amount)){
                    $bookingpress_entry_details['bookingpress_tip_amount'] = $bookingpress_tip_amount;
                }

                return $bookingpress_entry_details;
            }

            function bookingpress_before_save_customize_booking_form_func($booking_form_settings){

                $bokkingpress_tip_label = ! empty($_POST['front_label_edit_data']['tip_label_txt']) ? sanitize_text_field($_POST['front_label_edit_data']['tip_label_txt']) : '';            
                $booking_form_settings['front_label_edit_data']['tip_label_txt'] = $bokkingpress_tip_label; 

                $bokkingpress_tip_placeholder = ! empty($_POST['front_label_edit_data']['tip_placeholder_txt']) ? sanitize_text_field($_POST['front_label_edit_data']['tip_placeholder_txt']) : '';            
                $booking_form_settings['front_label_edit_data']['tip_placeholder_txt'] = $bokkingpress_tip_placeholder; 

                $bokkingpress_tip_btn_apply = ! empty($_POST['front_label_edit_data']['tip_button_txt']) ? sanitize_text_field($_POST['front_label_edit_data']['tip_button_txt']) : '';            
                $booking_form_settings['front_label_edit_data']['tip_button_txt'] = $bokkingpress_tip_btn_apply; 

                $bokkingpress_tip_applied_txt = ! empty($_POST['front_label_edit_data']['tip_applied_title']) ? sanitize_text_field($_POST['front_label_edit_data']['tip_applied_title']) : '';            
                $booking_form_settings['front_label_edit_data']['tip_applied_title'] = $bokkingpress_tip_applied_txt; 

                return $booking_form_settings;

            }

            function bookingpress_customize_add_dynamic_data_fields_func($bookingpress_customize_vue_data_fields){

                $bookingpress_customize_vue_data_fields['is_tip_activated'] = $this->bookingpress_check_tip_module_activation();
                $bookingpress_customize_vue_data_fields['front_label_edit_data']['tip_label_txt'] = '';                        
                $bookingpress_customize_vue_data_fields['front_label_edit_data']['tip_placeholder_txt'] = '';                        
                $bookingpress_customize_vue_data_fields['front_label_edit_data']['tip_button_txt'] = '';                        
                $bookingpress_customize_vue_data_fields['front_label_edit_data']['tip_applied_title'] = '';   
                $bookingpress_customize_vue_data_fields['front_label_edit_data']['tip_error_msg'] = '';                        
                     
                return $bookingpress_customize_vue_data_fields;
            }


            public function bookingpress_check_tip_module_activation(){

                $bookingpress_tip_version = get_option('bookingpress_tip_addon');
                return !empty($bookingpress_tip_version) ? 1 : 0;
            }

            function bookingpress_get_booking_form_customize_data_filter_func($bookingpress_booking_form_data){
                
                $bookingpress_booking_form_data['front_label_edit_data']['tip_label_txt'] =  __('Give a tip', 'bookingpress-tip');   
                $bookingpress_booking_form_data['front_label_edit_data']['tip_placeholder_txt'] =  __('Enter tip amount', 'bookingpress-tip');   
                $bookingpress_booking_form_data['front_label_edit_data']['tip_button_txt'] =  __('Apply', 'bookingpress-tip');   
                $bookingpress_booking_form_data['front_label_edit_data']['tip_applied_title'] =  __('Tip Applied', 'bookingpress-tip'); 
                $bookingpress_booking_form_data['front_label_edit_data']['tip_error_msg'] =  __('Please enter tip amount', 'bookingpress-tip');   
  
                return $bookingpress_booking_form_data;
            }

            function bookingpress_frontend_apointment_form_add_dynamic_data_func($bookingpress_front_vue_data_fields){

                global $BookingPress;

                $tip_message_label = $BookingPress->bookingpress_get_customize_settings('tip_label_txt', 'booking_form');
                $bookingpress_front_vue_data_fields['tip_label_txt'] = $tip_message_label;

                $tip_placeholder_txt = $BookingPress->bookingpress_get_customize_settings('tip_placeholder_txt', 'booking_form');
                $bookingpress_front_vue_data_fields['tip_placeholder_txt'] = $tip_placeholder_txt;

                $tip_apply_btn_label = $BookingPress->bookingpress_get_customize_settings('tip_button_txt', 'booking_form');
                $bookingpress_front_vue_data_fields['tip_button_txt'] = $tip_apply_btn_label;

                $tip_apply_label_txt = $BookingPress->bookingpress_get_customize_settings('tip_applied_title', 'booking_form');
                $bookingpress_front_vue_data_fields['tip_applied_title'] = $tip_apply_label_txt;

                $tip_error_msg = $BookingPress->bookingpress_get_customize_settings('tip_error_msg', 'booking_form');
                $bookingpress_front_vue_data_fields['tip_error_msg'] = $tip_error_msg;

                $bookingpress_front_vue_data_fields['is_tip_activated'] = $this->bookingpress_check_tip_module_activation();
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['tip_amount'] = '';
                $bookingpress_front_vue_data_fields['appointment_step_form_data']['tip_amount_with_currency'] = '';
                $bookingpress_front_vue_data_fields['tip_apply_loader'] = 0;
                $bookingpress_front_vue_data_fields['tip_apply_code_msg'] = '';
                $bookingpress_front_vue_data_fields['bpa_tip_apply_disabled'] = 0;
                $bookingpress_front_vue_data_fields['tip_applied_status'] = 'error';

                return $bookingpress_front_vue_data_fields;
            }

            function bookingpress_add_customize_extra_section_func(){ ?>
                <h5><?php esc_html_e('Tip Inputs labels', 'bookingpress-tip'); ?></h5>
                    <div class="bpa-sm--item">
                        <label class="bpa-form-label"><?php esc_html_e('Tip label', 'bookingpress-tip'); ?></label>
                        <el-input v-model="front_label_edit_data.tip_label_txt " class="bpa-form-control"></el-input>
                    </div> 
                    <div class="bpa-sm--item">
                        <label class="bpa-form-label"><?php esc_html_e('Tip placeholder', 'bookingpress-tip'); ?></label>
                        <el-input v-model="front_label_edit_data.tip_placeholder_txt " class="bpa-form-control"></el-input>
                    </div> 
                    <div class="bpa-sm--item">
                        <label class="bpa-form-label"><?php esc_html_e('Tip apply button label', 'bookingpress-tip'); ?></label>
                        <el-input v-model="front_label_edit_data.tip_button_txt " class="bpa-form-control"></el-input>
                    </div> 
                    <div class="bpa-sm--item">
                        <label class="bpa-form-label"><?php esc_html_e('Tip applied title', 'bookingpress-tip'); ?></label>
                        <el-input v-model="front_label_edit_data.tip_applied_title " class="bpa-form-control"></el-input>
                    </div> 
                    <div class="bpa-sm--item">
                        <label class="bpa-form-label"><?php esc_html_e('Tip Error Message', 'bookingpress-tip'); ?></label>
                        <el-input v-model="front_label_edit_data.tip_error_msg " class="bpa-form-control"></el-input>
                    </div> 
            <?php }

            function bookingpress_admin_notices(){
                if(!is_plugin_active('bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php')){
                    echo "<div class='notice notice-warning'><p>" . __('Bookingpress - Tip addon requires Bookingpress Premium Plugin installed and active.', 'bookingpress-tip') . "</p></div>";
                }
            }

            public static function install(){

                global $wpdb, $bookingpress_tip_version, $tbl_bookingpress_customize_settings,$tbl_bookingpress_entries,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_payment_logs;

                $bookingpress_tip_version_addon = get_option('bookingpress_tip_addon');

                if (!isset($bookingpress_tip_version_addon) || $bookingpress_tip_version_addon == '') {

                    $myaddon_name = "bookingpress-tip/bookingpress-tip.php";
                
                    // activate license for this addon
                    $posted_license_key = trim( get_option( 'bkp_license_key' ) );
                    $posted_license_package = '18508';
    
                    $api_params = array(
                        'edd_action' => 'activate_license',
                        'license'    => $posted_license_key,
                        'item_id'  => $posted_license_package,
                        //'item_name'  => urlencode( BOOKINGPRESS_ITEM_NAME ), // the name of our product in EDD
                        'url'        => home_url()
                    );
    
                    // Call the custom API.
                    $response = wp_remote_post( BOOKINGPRESS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
    
                    // make sure the response came back okay
                    $message = "";
                    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                        $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.','bookingpress-tip' );
                    } else {
                        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
                        $license_data_string = wp_remote_retrieve_body( $response );
                        if ( false === $license_data->success ) {
                            switch( $license_data->error ) {
                                case 'expired' :
                                    $message = sprintf(
                                        __( 'Your license key expired on %s.','bookingpress-tip' ),
                                        date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                    );
                                    break;
                                case 'revoked' :
                                    $message = __( 'Your license key has been disabled.','bookingpress-tip' );
                                    break;
                                case 'missing' :
                                    $message = __( 'Invalid license.','bookingpress-tip' );
                                    break;
                                case 'invalid' :
                                case 'site_inactive' :
                                    $message = __( 'Your license is not active for this URL.','bookingpress-tip' );
                                    break;
                                case 'item_name_mismatch' :
                                    $message = __('This appears to be an invalid license key for your selected package.','bookingpress-tip');
                                    break;
                                case 'invalid_item_id' :
                                        $message = __('This appears to be an invalid license key for your selected package.','bookingpress-tip');
                                        break;
                                case 'no_activations_left':
                                    $message = __( 'Your license key has reached its activation limit.','bookingpress-tip' );
                                    break;
                                default :
                                    $message = __( 'An error occurred, please try again.','bookingpress-tip' );
                                    break;
                            }
    
                        }
    
                    }
    
                    if ( ! empty( $message ) ) {
                        update_option( 'bkp_tip_license_data_activate_response', $license_data_string );
                        update_option( 'bkp_tip_license_status', $license_data->license );
                        deactivate_plugins($myaddon_name, FALSE);
                        $redirect_url = network_admin_url('plugins.php?deactivate=true&bkp_license_deactivate=true&bkp_deactivate_plugin='.$myaddon_name);
                        $bpa_dact_message = __('Please activate license of BookingPress premium plugin to use BookingPress tip Add-on', 'bookingpress-tip');
                        $bpa_link = sprintf( __('Please %s Click Here %s to Continue', 'bookingpress-tip'), '<a href="javascript:void(0)" onclick="window.location.href=\'' . $redirect_url . '\'">', '</a>');
                        wp_die('<p>'.$bpa_dact_message.'<br/>'.$bpa_link.'</p>');
                        die;
                    }
                    
                    if($license_data->license === "valid")
                    {
                        update_option( 'bkp_tip_license_key', $posted_license_key );
                        update_option( 'bkp_tip_license_package', $posted_license_package );
                        update_option( 'bkp_tip_license_status', $license_data->license );
                        update_option( 'bkp_tip_license_data_activate_response', $license_data_string );
                    }

                    update_option('bookingpress_tip_addon', $bookingpress_tip_version);

                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} ADD bookingpress_tip_amount INT DEFAULT 0 AFTER bookingpress_appointment_status" );
                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} ADD bookingpress_tip_amount INT DEFAULT 0 AFTER bookingpress_appointment_status" );
                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} ADD bookingpress_tip_amount INT DEFAULT 0 AFTER bookingpress_payment_response" );

                    $bookingpress_booking_form_customize_setting = array(
                        'tip_label_txt'	=> __('Give a tip', 'bookingpress-tip'),
                        'tip_button_txt'	=> __('Apply', 'bookingpress-tip'),
                        'tip_placeholder_txt'	=> __('Enter tip amount', 'bookingpress-tip'),
                        'tip_applied_title' => __('Tip Applied','bookingpress-tip'),
                        'tip_error_msg'     => __('Please enter tip amount', 'bookingpress-tip'),

                    );
                    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
                    foreach($bookingpress_booking_form_customize_setting as $key => $val){
                        $bookingpress_bd_data = array(
                            'bookingpress_setting_name' => $key,
                            'bookingpress_setting_value' => $val,
                            'bookingpress_setting_type' => 'booking_form',
                        );
                        $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_bd_data);        
                    }

                    $bookingpress_booking_form_customize_setting = array(
                        'tip_label_txt'	=> __('Give a tip', 'bookingpress-tip'),
                        'tip_button_txt'	=> __('Apply', 'bookingpress-tip'),
                        'tip_placeholder_txt'	=> __('Enter tip amount', 'bookingpress-tip'),
                        'tip_applied_title' => __('Tip Applied','bookingpress-tip'),
                        'tip_error_msg'     => __('Please enter tip amount', 'bookingpress-tip'),

                    );
                    $tbl_bookingpress_customize_settings = $wpdb->prefix . 'bookingpress_customize_settings';
                    foreach($bookingpress_booking_form_customize_setting as $key => $val){
                        $bookingpress_bd_data = array(
                            'bookingpress_setting_name' => $key,
                            'bookingpress_setting_value' => $val,
                            'bookingpress_setting_type' => 'package_booking_form',
                        );
                        $wpdb->insert($tbl_bookingpress_customize_settings, $bookingpress_bd_data);        
                    }                    

                }
            }

            public static function uninstall() {

                global $wpdb,$tbl_bookingpress_entries,$tbl_bookingpress_appointment_bookings,$tbl_bookingpress_payment_logs;

                if( is_multisite() ) {
                    $blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
                    if ( $blogs ) {
                        foreach ( $blogs as $blog ) {
                            switch_to_blog( $blog['blog_id'] );
                            delete_option('bookingpress_tip_addon');        
                            delete_option( 'bkp_tip_license_key');
                            delete_option( 'bkp_tip_license_package');
                            delete_option( 'bkp_tip_license_status');
                            delete_option( 'bkp_tip_license_data_activate_response');
                            
                            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} DROP COLUMN bookingpress_tip_amount" ); 
                            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} DROP COLUMN bookingpress_tip_amount" ); 
                            $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} DROP COLUMN bookingpress_tip_amount" );     
    
                        }
                        restore_current_blog();
                    }
                } else {
                    delete_option('bookingpress_tip_addon');    
                    delete_option( 'bkp_tip_license_key');
                    delete_option( 'bkp_tip_license_package');
                    delete_option( 'bkp_tip_license_status');
                    delete_option( 'bkp_tip_license_data_activate_response');
                    
                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_entries} DROP COLUMN bookingpress_tip_amount" ); 
                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_appointment_bookings} DROP COLUMN bookingpress_tip_amount" ); 
                    $wpdb->query( "ALTER TABLE {$tbl_bookingpress_payment_logs} DROP COLUMN bookingpress_tip_amount" ); 
                    
                }
            }
        }
    }
    global $bookingpress_tip;
    $bookingpress_tip = new bookingpress_tip;