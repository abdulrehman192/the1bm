<?php
if (!class_exists('bookingpress_location_customize')) {
	class bookingpress_location_customize Extends BookingPress_Core {
        function __construct(){ 
            global $BookingPress;

            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if( is_plugin_active( 'bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) && !empty( $BookingPress->bpa_pro_plugin_version() ) && version_compare( $BookingPress->bpa_pro_plugin_version(), '2.6.1', '>=' ) ){

                //Add customize booking form data variables
                add_filter( 'bookingpress_customize_add_dynamic_data_fields', array( $this, 'bookingpress_modify_customize_data_fields_func' ), 11 );
                add_filter('bookingpress_get_booking_form_customize_data_filter',array($this, 'bookingpress_get_booking_form_customize_data_filter_func'),11,1);

                add_action('bookingpress_add_customize_preview_tab', array($this, 'bookingpress_add_customize_preview_tab_func'));

                add_action('bookingpress_add_label_settings_dynamically', array($this, 'bookingpress_add_label_settings_dynamically_func'));

                add_action('bookingpress_add_booking_form_summary_label_data', array($this, 'bookingpress_add_customize_extra_section_func'));

                add_action('bookingpress_add_cart_label_outside', array($this, 'bookingpress_add_cart_label_outside_func'));

                /* my booking page show location information */
                add_action( 'bpa_add_label_outside_my_booking_form', array( $this, 'bpa_add_label_outside_my_booking_form_func'));
                add_action( 'add_additional_information_outside' ,array( $this, 'add_additional_information_outside_func'));
                add_filter( 'bookingpress_modify_my_appointments_data_externally', array( $this, 'bookingpress_modify_my_appointments_data_externally_func'));
            }
        }

        function bookingpress_modify_my_appointments_data_externally_func($bookingpress_appointments_data){
            global $BookingPress, $wpdb, $tbl_bookingpress_locations;
            if( !empty( $bookingpress_appointments_data['bookingpress_location_id'] ) ){

                $location_name = $wpdb->get_row( $wpdb->prepare("SELECT bookingpress_location_name FROM `{$tbl_bookingpress_locations}` WHERE bookingpress_location_id = %d", $bookingpress_appointments_data['bookingpress_location_id']), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

                $location_name = $location_name['bookingpress_location_name'];

                $bookingpress_appointments_data['bookingpress_location_name'] = $location_name;
            }

            return $bookingpress_appointments_data;
        }

        function add_additional_information_outside_func(){ 
            global $BookingPress;
            $bookingpress_location_name = $BookingPress->bookingpress_get_customize_settings('location_title', 'booking_my_booking');
            ?>
            <div class="bpa-vac-bd__row">
                <div class="bpa-bd__item" v-if="scope.row.bookingpress_location_id != '' && scope.row.bookingpress_location_id != 0">
                    <div class="bpa-item--label"><?php echo esc_html($bookingpress_location_name); ?>:</div>
                    <div class="bpa-item--val">{{ scope.row.bookingpress_location_name }}</div>
                </div>
            </div>
        <?php }

        function bpa_add_label_outside_my_booking_form_func(){ ?>

            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Location title', 'bookingpress-location'); ?></label>
                <el-input v-model="my_booking_field_settings.location_title" class="bpa-form-control"></el-input>
            </div>

        <?php }

        function bookingpress_add_cart_label_outside_func(){ ?>

            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Cart location title', 'bookingpress-location'); ?></label>
                <el-input v-model="cart_container_data.cart_location_title" class="bpa-form-control"></el-input>
            </div>

        <?php }

        function bookingpress_add_customize_extra_section_func(){ ?>
            <div class="bpa-sm--item">
                <label class="bpa-form-label"><?php esc_html_e('Location Title', 'bookingpress-location'); ?></label>
                <el-input v-model="summary_container_data.bpa_location_title_summay" class="bpa-form-control"></el-input>
            </div>
        <?php }

        function bookingpress_add_label_settings_dynamically_func(){
            ?>
                <h5><?php esc_html_e('Location step labels', 'bookingpress-location'); ?></h5>                                        
                <div class="bpa-sm--item">
                    <label class="bpa-form-label"><?php esc_html_e('Step location', 'bookingpress-location'); ?></label>
                    <el-input v-model="tab_container_data.location_title " class="bpa-form-control"></el-input>
                </div>
            <?php
        }

        function bookingpress_add_customize_preview_tab_func(){
            require BOOKINGPRESS_LOCATION_VIEWS_DIR.'/manage_customize_location_preview.php';
        }
        
        function bookingpress_get_booking_form_customize_data_filter_func($booking_form_settings){
			$booking_form_settings['booking_form_settings']['bookingpress_form_sequance'] = '';
            $booking_form_settings['tab_container_data']['location_title'] = '';
            $booking_form_settings['summary_container_data']['bpa_location_title_summay'] = '';
            $booking_form_settings['cart_container_data']['cart_location_title'] = 'Location';

			return $booking_form_settings;
		}

        function bookingpress_modify_customize_data_fields_func($bookingpress_customize_vue_data_fields){
            $bookingpress_customize_vue_data_fields['is_location_activated'] = '1';

            $bookingpress_customize_vue_data_fields['booking_form_sequence'] = array('location_selection', 'service_selection', 'staff_selection');

            $bookingpress_customize_vue_data_fields['tab_container_data']['location_title'] = __('Location', 'bookingpress-location');

            $bookingpress_customize_vue_data_fields['my_booking_field_settings']['location_title'] = __('Location','bookingpress-location');

            return $bookingpress_customize_vue_data_fields;
        }
    }

    global $bookingpress_location_customize;
	$bookingpress_location_customize = new bookingpress_location_customize();
}