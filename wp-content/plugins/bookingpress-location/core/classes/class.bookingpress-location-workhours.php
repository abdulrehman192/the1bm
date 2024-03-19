<?php
if (!class_exists('bookingpress_location_workhours')) {
	class bookingpress_location_workhours Extends BookingPress_Core {
        function __construct(){
            global $BookingPress;
            if( !function_exists('is_plugin_active') ){
                include_once ABSPATH . 'wp-admin/includes/plugin.php';
            }

            if( is_plugin_active( 'bookingpress-appointment-booking-pro/bookingpress-appointment-booking-pro.php' ) && !empty( $BookingPress->bpa_pro_plugin_version() ) && version_compare( $BookingPress->bpa_pro_plugin_version(), '2.6.1', '>=' ) ){
                //Service Shift Management Related Hooks
                //-----------------------------------------------------------------------
                //Add location content to shift management modal for service
                add_action('bookingpress_add_service_shift_management_content', array($this, 'bookingpress_add_service_shift_management_content_func'));
    
                add_filter( 'bookingpress_modify_service_shift_management_data', array( $this, 'bookingpress_retrieve_service_shift_management_with_location') );
    
                add_action( 'bookingpress_modify_service_shift_management_xhr_response', array( $this, 'bookingpress_modify_service_shift_management_xhr_response_func') );
    
                add_action( 'bookingpress_modify_save_service_workhours_postdata', array( $this, 'bookingpress_modify_save_service_shiftmanagement') );
    
                add_filter( 'bookingpress_save_external_service_shift_management_data', array( $this, 'bookingpress_save_service_shift_management_with_location_callback' ) );
    
                //Modify service module data fields
                add_filter( 'bookingpress_modify_service_data_fields', array( $this, 'bookingpress_modify_service_data_fields_func' ), 11 );
    
                add_action( 'bookingpress_service_workhour_external_content', array( $this, 'bookingpress_display_location_wise_service_workhours') );
    
                add_action( 'bookingpress_add_service_dynamic_external_vue_methods', array( $this, 'bookingpress_add_service_dynamic_external_vue_methods_for_locations') );
    
                add_action( 'bookingpress_staff_member_external_vue_methods', array( $this, 'bookingpress_staff_member_dynamic_external_vue_methods_for_locations') );
                add_action( 'bookingpress_timesheet_dynamic_vue_methods', array( $this, 'bookingpress_staff_member_dynamic_external_vue_methods_for_locations') );
    
                add_action( 'bookingpress_save_external_break_data', array( $this, 'bpa_add_service_shift_save_break_data_with_location') );
    
                add_action( 'bookingpress_save_external_special_data', array( $this, 'bpa_add_service_shift_save_special_days_data_with_location') );
    
                add_action( 'bookingpress_service_special_days_external_content', array( $this, 'bookingpress_display_location_wise_service_special_days') );
    
                add_filter( 'bookingpress_validate_service_specia_days_where_caluse', array( $this, 'bookingpress_modify_validate_service_special_days_where_caluse_with_location') );
    
                add_action( 'wp_ajax_bookingpress_format_service_special_days_data_with_location', array( $this, 'bookingpress_format_service_special_days_data_with_location_func' ) );
    
                add_action( 'wp_ajax_bookingpress_format_staffmember_special_days_data_with_location', array( $this, 'bookingpress_format_staff_special_days_data_with_location_func') );

                add_action( 'bookingpress_reset_assign_service_modal_outside', array( $this, 'bookingpress_reset_assign_service_modal_outside' ) );
    
                //-----------------------------------------------------------------------
    
    
                //Staffmember Shift Management Related Hooks
                //-----------------------------------------------------------------------
                global $BookingPress;
                $bookingpress_allow_staff_to_service_multiple_location = $BookingPress->bookingpress_get_settings('allow_staffmember_to_serve_multiple_locations', 'general_setting');
    
                if($bookingpress_allow_staff_to_service_multiple_location == 'true'){
                    add_action('bookingpress_add_staffmember_shift_management_content', array($this, 'bookingpress_add_staffmember_shift_management_content_func'));
    
                    add_filter( 'bookingpress_modify_staffmember_data_fields', array( $this, 'bookingpress_modify_staffmember_data_fields_func' ), 11 );
                    add_filter( 'bookingpress_modify_timesheet_data_fields', array( $this, 'bookingpress_modify_staffmember_data_fields_func' ), 11 );
    
                    add_action( 'bookingpress_staff_work_hour_content_outside', array( $this, 'bookingpress_staff_shift_location_wise_workhours') );
    
                    add_action('bookingpress_after_open_staff_shift_mgmt_modal', array($this, 'bookingpress_after_open_staff_shift_mgmt_modal_func'));
    
                    add_action('bookingpress_save_staff_member', array($this, 'bookingpress_save_staff_member_func'));
    
                    add_action( 'bookingpress_staff_shift_management_modify_xhr_postdata', array( $this, 'bookingpress_add_location_args_on_staff_shift_xhr_data'));
    
                    add_action( 'bookingpress_save_external_staff_break_data', array( $this, 'bookingpress_save_staff_break_data_with_location') );
    
                    add_filter( 'bookingpress_staff_members_save_external_details', array( $this, 'bookingpress_save_staff_workhour_with_location') );
    
                    add_filter( 'bookingpress_modify_staff_shift_managment_data', array( $this, 'bookingpress_fetch_location_wise_staff_workhours'));
                    
                    add_action( 'bookingpress_modify_staff_shift_management_xhr_response', array( $this, 'bookingpress_set_location_wise_staff_workhours') );
    
                    add_action( 'bookingpress_staff_special_days_external_content', array( $this, 'bookingpress_staffwise_special_days_content') );
    
                    add_action( 'bookingpress_save_staff_external_special_data', array( $this, 'bookingpress_save_staff_external_special_days') );
    
                    add_filter( 'bookingpress_validate_staff_specia_days_where_caluse', array( $this, 'bookingpress_modify_validate_staff_special_days_where_caluse_with_location' ) );

                    add_action( 'bookingpress_staff_workinghour_post_data', array( $this, 'bookingpress_staff_workinghour_location_details') );
                }
    
                //-----------------------------------------------------------------------
            }


        }

        function bookingpress_reset_assign_service_modal_outside(){
            ?>
                vm.assign_service_form.assign_service_location = '';
                vm.assign_service_form.selected_location = [];
            <?php
        }

        function bookingpress_save_staff_external_special_days(){
            global $bookingpress_notification_duration;
            ?>
            if( "undefined" == typeof vm.display_staff_working_hours || vm.display_staff_working_hours == false ){
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                this.$refs[staffmember_special_day_form].validate((valid) => {
                    if (valid) {
                        vm.disable_staff_special_day_btn = true;
                        var is_exit = 0;
                        if(vm.staffmember_special_day_form.special_day_workhour != undefined && vm.staffmember_special_day_form.special_day_workhour!= '') {
                            vm.staffmember_special_day_form.special_day_workhour.forEach(function(item, index, arr){
                                if(is_exit == 0 && (item.start_time == '' || item.end_time == '' || item.start_time == undefined || item.end_time == undefined)) {
                                    is_exit = 1;
                                    vm.$notify({
                                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                        message: '<?php esc_html_e( 'Please Enter Start Time and End Time', 'bookingpress-location' ); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                        duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                    });                                
                                }
                            });
                        } 
                        if(vm.location_wise_staffmember_special_day_arr[selected_location] != undefined && vm.location_wise_staffmember_special_day_arr[selected_location] != '' ) {

                            vm.location_wise_staffmember_special_day_arr[selected_location].forEach(function(item, index, arr) {

                                if((vm.staffmember_special_day_form.special_day_date[0] == item.special_day_start_date || vm.staffmember_special_day_form.special_day_date[0] == item.special_day_end_date || ( vm.staffmember_special_day_form.special_day_date[0] >= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[0] <= item.special_day_end_date ) || vm.staffmember_special_day_form.special_day_date[1] == item.special_day_end_date || vm.staffmember_special_day_form.special_day_date[1] == item.special_day_start_date || (vm.staffmember_special_day_form.special_day_date[1] >= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[1] <= item.special_day_end_date) || (vm.staffmember_special_day_form.special_day_date[0] <= item.special_day_start_date && vm.staffmember_special_day_form.special_day_date[1] >= item.special_day_end_date) ) && vm.location_wise_staff_special_day_edit_index != index && is_exit == 0) {										
                                    is_exit = 0;
                                    if( vm.staffmember_special_day_form.special_day_service.length > 0 && item.special_day_service.length > 0) {
                                        item.special_day_service.forEach(function(item2,index2,arr2) {
                                            if( is_exit == 0 ) {
                                                if(vm.staffmember_special_day_form.special_day_service.includes(item2)) {
                                                    is_exit = 1;
                                                }
                                            } 
                                        });	
                                    } else {
                                        is_exit = 1;
                                    }

                                    if(is_exit ==  1) {
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Special days already exists', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                        vm.disable_staff_special_day_btn = false;
                                    }
                                }							
                            });	
                        }
                        let staff_daysoff_arr = vm.staffmember_dayoff_arr;
                        if( "undefined" != typeof update_from_panel && true == update_from_panel ){
                            staff_daysoff_arr = vm.bookingpress_staffmembers_daysoff_details;
                        }
                        if(staff_daysoff_arr != '') {
                            console.log( staff_daysoff_arr );
                            staff_daysoff_arr.forEach(function(item, index, arr)
                            {
                                if (item.dayoff_date >= vm.staffmember_special_day_form.special_day_date[0] && item.dayoff_date <= vm.staffmember_special_day_form.special_day_date[1] ) {									
                                    vm.$notify({
                                        title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                        message: '<?php esc_html_e('Holiday is already exists', 'bookingpress-location'); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                        duration:<?php echo intval($bookingpress_notification_duration); ?>,
                                    });
                                    is_exit = 1
                                }
                            });
                        }
                        if(is_exit == 0) {
                            var postdata = [];
                            postdata.action = 'bookingpress_validate_staffmember_special_day'
                            postdata.selected_date_range= vm.staffmember_special_day_form.special_day_date;
                            postdata.special_day_workhour= vm.staffmember_special_day_form.special_day_workhour;
                            let from_staff_panel = false;
                            if( "undefined" != typeof update_from_panel && true == update_from_panel ){
                                postdata.update_from_panel = true;
                                postdata.action = 'bookingpress_validate_staff_member_special_day';
                                from_staff_panel = true;
                            } else {
                                postdata.staffmember_id = vm.staff_members.update_id;
                            }
                            postdata.selected_location = selected_location;
                            postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
                            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
                            .then(function(response){
                                if(response.data.variant != 'undefined' && response.data.variant == 'warnning') {
                                    vm.$confirm(response.data.msg, 'Warning', {
                                        confirmButtonText:  '<?php esc_html_e( 'Ok', 'bookingpress-location' ); ?>',
                                        cancelButtonText:  '<?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?>',
                                        type: 'warning',
                                        customClass: 'bpa_custom_warning_notification',
                                    }).then(() => {
                                        if(vm.location_wise_staff_special_day_edit_index > -1 ){
                                            vm.edit_staffmember_special_day_with_locations( from_staff_panel );
                                        } else {
                                            vm.add_staffmember_special_day_with_locations( from_staff_panel );
                                        }
                                        vm.disable_staff_special_day_btn = false;
                                    });				
                                }else if(response.data.variant != 'undefined' && response.data.variant  == 'success') {
                                    if(vm.location_wise_staff_special_day_edit_index > -1 ){
                                        vm.edit_staffmember_special_day_with_locations( from_staff_panel );
                                    } else {
                                        vm.add_staffmember_special_day_with_locations( from_staff_panel );
                                    }
                                    vm.disable_staff_special_day_btn = false;
                                }
                            }).catch(function(error){
                                vm.$notify({
                                    title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                    message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-location' ); ?>',
                                    type: 'error_notification',
                                });
                            });
                        }	
                    } else {
                        return false;
                    }
                });
            }
            <?php
        }


        function bookingpress_staffwise_special_days_content(){
            ?>
            <div class="bpa-default-card bpa-grid-list-container bpa-dc__staff--assigned-service bpa-sm__special-days-card bpa_location_special_days" v-if="display_staff_working_hours == false">
                <div class="bpa-sm__sd-body-row--parent" v-for="(location_details,index) in bookingpress_locations" v-if="location_details.bookingpress_location_id == bookingpress_shift_mgmt_selected_location">
                    <el-row class="bpa-dc--sec-sub-head" v-if="location_wise_staffmember_special_day_arr.length != 0">
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <h2 class="bpa-sec--sub-heading"><?php esc_html_e( 'All Special Days', 'bookingpress-location' ); ?></h2>
                        </el-col>
                    </el-row>
                    <div class="bpa-as__body bpa-sm__doc-body">
                        <el-row type="flex" class="bpa-as__empty-view" v-if="location_wise_staffmember_special_day_arr[bookingpress_shift_mgmt_selected_location].length == 0">
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <div class="bpa-data-empty-view">
                                    <div class="bpa-ev-left-vector">
                                        <picture>
                                            <source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
                                            <img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
                                        </picture>
                                    </div>				
                                    <div class="bpa-ev-right-content">					
                                        <h4><?php esc_html_e( 'No Special Days Available', 'bookingpress-location' ); ?></h4>
                                    </div>				
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="bpa-assigned-service-body" v-if="location_wise_staffmember_special_day_arr[bookingpress_shift_mgmt_selected_location].length > 0">
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <div class="bpa-card bpa-card__heading-row">
                                    <el-row type="flex">
                                        <el-col :xs="8" :sm="8" :md="8" :lg="8" :xl="8">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Date', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Workhours', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Breaks', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Action', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="(staffmember_special_day, sp_staff_index) in location_wise_staffmember_special_day_arr[bookingpress_shift_mgmt_selected_location]">
                                <div class="bpa-card bpa-card__body-row">
                                    <el-row type="flex">
                                        <el-col :xs="8" :sm="8" :md="8" :lg="8" :xl="8">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading is--body-heading">{{ staffmember_special_day.special_day_formatted_start_date }} - {{ staffmember_special_day.special_day_formatted_end_date }}</h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading is--body-heading">( {{staffmember_special_day.formatted_start_time}} - {{staffmember_special_day.formatted_end_time}} )</h4>
                                            </div>
                                        </el-col>	
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item"> 
                                                <span v-if="staffmember_special_day.special_day_workhour != undefined && staffmember_special_day.special_day_workhour != ''">		
                                                    <h4 class="bpa-card__item__heading is--body-heading" v-for="special_day_workhours in staffmember_special_day.special_day_workhour" v-if="special_day_workhours.formatted_start_time != undefined && special_day_workhours.formatted_start_time != '' && special_day_workhours.formatted_end_time != undefined && special_day_workhours.formatted_end_time != '' && special_day_workhours.start_time != '' && special_day_workhours.end_time != ''"> 
                                                    ( {{ special_day_workhours.formatted_start_time }} - {{special_day_workhours.formatted_end_time}} )
                                                    </h4>
                                                </span>
                                                <span v-else>-</span>	
                                            </div>
                                        </el-col>
                                        <el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
                                            <div>
                                                <el-tooltip effect="dark" content="" placement="top" open-delay="300">
                                                    <div slot="content">
                                                        <span><?php esc_html_e( 'Edit', 'bookingpress-location' ); ?></span>
                                                    </div>
                                                    <el-button class="bpa-btn bpa-btn--icon-without-box" @click="show_edit_staff_special_day_div_with_location(sp_staff_index, event)">
                                                        <span class="material-icons-round">mode_edit</span>
                                                    </el-button>
                                                </el-tooltip>
                                                <el-tooltip effect="dark" content="" placement="top" open-delay="300">
                                                    <div slot="content">
                                                        <span><?php esc_html_e( 'Delete', 'bookingpress-location' ); ?></span>
                                                    </div>
                                                    <el-button class="bpa-btn bpa-btn--icon-without-box __danger" @click="delete_staff_special_day_div_with_location(sp_staff_index)">
                                                        <span class="material-icons-round">delete</span>
                                                    </el-button>
                                                </el-tooltip>
                                            </div>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </div>
            <?php
        }

        function bookingpress_modify_save_service_shiftmanagement(){
            ?>
                postdata.bpa_action = 'bookingpress_save_service_shift_management_with_location';
                postdata.location_wise_special_day = JSON.stringify( vm2.location_wise_special_day_data_arr );
            <?php
        }

        function bookingpress_save_service_shift_management_with_location_callback( $response ){
            global $wpdb, $BookingPress, $bookingpress_services, $bookingpress_pro_services, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_locations_service_special_days;

            if( !isset( $_POST['bpa_action'] ) || empty( $_POST['bpa_action'] ) || 'bookingpress_save_service_shift_management_with_location' != $_POST['bpa_action'] ){ // phpcs:ignore
                return $response;
            }

            $posted_data = json_decode( stripslashes_deep( $_POST['services_data'] ), true ); // phpcs:ignore

            $location_wise_special_days = ! empty($_POST['location_wise_special_day']) ? json_decode( stripslashes_deep( $_POST['location_wise_special_day'] ), true ) : array(); // phpcs:ignore
                        
			$service_id  = ! empty( $posted_data['service_update_id'] ) ? intval( $posted_data['service_update_id'] ) : 0; // phpcs:ignore
            
            if ( ! empty( $service_id ) && ! empty( $posted_data ) ) {
                
                if( !empty( $posted_data['location_wise_workhours'] ) ){
                    $location_wise_workhours = $posted_data['location_wise_workhours'];
                    
                    $bookingpress_configure_specific_service_workhour = ! empty( $posted_data['bookingpress_configure_specific_service_workhour'] ) ? $posted_data['bookingpress_configure_specific_service_workhour'] : 'false';

                    if ( ! empty( $bookingpress_configure_specific_service_workhour ) && $bookingpress_configure_specific_service_workhour == 'true' ) {

                        $bookingpress_workhour_days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

                        foreach( $location_wise_workhours as $location_id => $location_workhour_data ){
                            
                            $wpdb->delete( $tbl_bookingpress_locations_service_workhours, array( 'bookingpress_location_id' => $location_id, 'bookingpress_service_id' => $service_id ) );

                            foreach ( $bookingpress_workhour_days as $workhour_key => $workhour_val ) {
                                $workhour_start_time = ! empty( $posted_data['location_wise_workhours'][$location_id][ $workhour_val ]['start_time'] ) ? $posted_data['location_wise_workhours'][$location_id][ $workhour_val ]['start_time'] : '09:00:00';
                                $workhour_end_time   = ! empty( $posted_data['location_wise_workhours'][$location_id][ $workhour_val ]['end_time'] ) ? $posted_data['location_wise_workhours'][$location_id][ $workhour_val ]['end_time'] : '17:00:00';

                                if ( $workhour_start_time == 'Off' ) {
                                    $workhour_start_time = null;
                                }
                                if ( $workhour_end_time == 'Off' ) {
                                    $workhour_end_time = null;
                                }

                                $bookingpress_db_fields = array(
                                    'bookingpress_service_id' => $service_id,
                                    'bookingpress_location_id' => $location_id,
                                    'bookingpress_location_service_workday_key' => $workhour_val,
                                    'bookingpress_location_service_workhour_start_time' => $workhour_start_time,
                                    'bookingpress_location_service_workhour_end_time' => $workhour_end_time,
                                );

                                $wpdb->insert( $tbl_bookingpress_locations_service_workhours, $bookingpress_db_fields );

                                if( !empty( $posted_data['location_wise_break_timings'] ) && !empty( $posted_data['location_wise_break_timings'][ $location_id ] ) && !empty( $posted_data['location_wise_break_timings'][ $location_id ][ $workhour_val ] ) ){
                                    /* $break_start_time = $posted_data['location_wise_break_timings'][ $location_id ][$workhour_val]['start_time'];
                                    $break_end_time = $posted_data['location_wise_break_timings'][ $location_id ][$workhour_val]['end_time']; */

                                    $bookingpress_workhour_break_details = $posted_data['location_wise_break_timings'][ $location_id ][$workhour_val];
                                    foreach( $bookingpress_workhour_break_details as $workhour_day_val => $workhour_break_data ){
                                        $start_time = $workhour_break_data['start_time'];
                                        $end_time = $workhour_break_data['end_time'];

                                        $bookingpress_location_break_db_fields = array(
                                            'bookingpress_service_id' => $service_id,
                                            'bookingpress_location_id' => $location_id,
                                            'bookingpress_location_service_workday_key' => $workhour_val,
                                            'bookingpress_location_service_workhour_is_break' => 1,
                                            'bookingpress_location_service_workhour_start_time' => $start_time,
                                            'bookingpress_location_service_workhour_end_time' => $end_time
                                        );

                                        $wpdb->insert( $tbl_bookingpress_locations_service_workhours, $bookingpress_location_break_db_fields );
                                    }
                                }

                            }
                        }
                    }
                }
            }

            if( !empty( $service_id ) && !empty( $location_wise_special_days ) ){
                
                foreach( $location_wise_special_days as $location_id => $special_days_details ){
                    $wpdb->delete( $tbl_bookingpress_locations_service_special_days, array( 'bookingpress_service_id' => $service_id, 'bookingpress_location_id' => $location_id ) );
                    if( !empty( $special_days_details ) ){
                        foreach( $special_days_details as $special_days_data ){
                            $location_special_day_start_date = $special_days_data['special_day_start_date'];
                            $location_special_day_end_date = $special_days_data['special_day_end_date'];
                            $location_special_day_start_time = $special_days_data['start_time'];
                            $location_special_day_end_time = $special_days_data['end_time'];

                            $special_days_db_fields = array(
                                'bookingpress_location_id' => $location_id,
                                'bookingpress_service_id' => $service_id,
                                'bookingpress_location_service_special_day_start_date' => $location_special_day_start_date,
                                'bookingpress_location_service_special_day_end_date' => $location_special_day_end_date,
                                'bookingpress_location_service_special_day_start_time' => $location_special_day_start_time,
                                'bookingpress_location_service_special_day_end_time' => $location_special_day_end_time
                            );

                            $wpdb->insert( $tbl_bookingpress_locations_service_special_days, $special_days_db_fields );

                            $location_special_days_breaks = $special_days_data['special_day_workhour'];
                            if( !empty( $location_special_days_breaks ) ){
                                foreach( $location_special_days_breaks as $location_special_day_breaks ){
                                    $location_sp_break_start_time = $location_special_day_breaks['start_time'];
                                    $location_sp_break_end_time = $location_special_day_breaks['end_time'];

                                    $special_days_break_db_fields = array(
                                        'bookingpress_location_id' => $location_id,
                                        'bookingpress_service_id' => $service_id,
                                        'bookingpress_location_service_special_day_start_date' => $location_special_day_start_date,
                                        'bookingpress_location_service_special_day_end_date' => $location_special_day_end_date,
                                        'bookingpress_location_service_special_day_has_break' => 1,
                                        'bookingpress_location_service_special_day_break_start_time' => $location_sp_break_start_time,
                                        'bookingpress_location_service_special_day_break_end_time' => $location_sp_break_end_time,
                                    );

                                    $wpdb->insert( $tbl_bookingpress_locations_service_special_days, $special_days_break_db_fields );
                                }
                            }
                        }
                    }
                }
            }
            

            return $response;
        }

        function bookingpress_modify_service_shift_management_xhr_response_func(){
            ?>
                if( "undefined" != typeof response.data.location_wise_workhours && response.data.location_wise_workhours != "" ){
                    vm.service.location_wise_workhours = response.data.location_wise_workhours;
                }

                if( "undefined" != typeof response.data.location_wise_break_hours && response.data.location_wise_break_hours != "" ){
                    vm.service.location_wise_break_timings = response.data.location_wise_break_hours;
                }

                if( "undefined" != typeof response.data.location_wise_special_days && response.data.location_wise_special_days != "" ){
                    vm.location_wise_special_day_data_arr = response.data.location_wise_special_days;
                }
            <?php
        }

        function bookingpress_retrieve_service_shift_management_with_location( $response ){
            global $wpdb, $bookingpress_global_options, $BookingPress, $bookingpress_services,$bookingpress_settings, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_workhours, $tbl_bookingpress_locations_service_special_days, $bookingpress_pro_services;


            $bookingpress_options = $bookingpress_global_options->bookingpress_global_options();
            
            /* $response                    = array();			 */
			/* $bpa_check_authorization = $this->bpa_check_authentication( 'get_service_shift_managment', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');
				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-location');
				$response['msg'] = $bpa_error_msg;
				wp_send_json( $response );
				die;
			} */

            /* $response['workhour_service_data'] = array();
			$response['workhour_data']         = array();
			$response['special_day_data']      = array();
			$response['data']                  = array();
			$response['selected_workhours']    = array();
			$response['default_break_times']   = array(); */
            //$response['location_wise_shift_management_data'] = array();

            $service_id = ! empty( $_REQUEST['service_id'] ) ? intval( $_REQUEST['service_id'] ) : '';

            
            $service_workhours_data = $response;
            $response['location_wise_workhours'] = $response['workhour_service_data'];
            /* echo "<pre>";
            print_r( $response['location_wise_workhours'] );
            die; */
            //return $response;
            $is_special_workhour_configure = ! empty( $_REQUEST['is_special_workhour_configure'] ) ? sanitize_text_field( $_REQUEST['is_special_workhour_configure']) : '';
            
            $all_location_ids = $wpdb->get_results( "SELECT bookingpress_location_id FROM `{$tbl_bookingpress_locations}`" ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
            
            if( !empty( $all_location_ids ) && !empty( $service_id ) && 'true' == $is_special_workhour_configure ){
                $bpa_location_wise_workhours = array();
                $temp_location_wise_workhours = array();
                $bpa_location_wise_break_timings = array();
                foreach( $all_location_ids as $location_data ){
                    $location_id = $location_data->bookingpress_location_id;
                    $bookingpress_days_arr = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
                    foreach( $bookingpress_days_arr as $day_key => $days_val ){
                        $location_work_hour_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations_service_workhours} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_location_service_workday_key = %s AND bookingpress_location_service_workhour_is_break = %d", $location_id, $service_id, $days_val, 0 ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_workhours is table name defined globally.
                        
                        if( !empty( $location_work_hour_data ) ){

                            $selected_start_time = $location_work_hour_data->bookingpress_location_service_workhour_start_time;
                            $selected_end_time = $location_work_hour_data->bookingpress_location_service_workhour_end_time;
                            
                            if ( $selected_start_time == null ) {
                                $selected_start_time = 'Off';
                            }
                            if ( $selected_end_time == null ) {
                                $selected_end_time = 'Off';
                            }
                            
                            $bpa_location_wise_workhours[ $location_id ][ $days_val ][ 'start_time'] = $selected_start_time;
                            $bpa_location_wise_workhours[ $location_id ][ $days_val ][ 'end_time'] = $selected_end_time;
                        } else {
                            /** Assign Default Service work hour */
                            $service_specific_work_hours = $response['workhour_service_data'];
                            if( !empty( $service_specific_work_hours )  ){
                                $bpa_location_wise_workhours[ $location_id ][ $days_val ] = $service_specific_work_hours[ $days_val ];
                            }
                        }

                        $location_break_hour_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tbl_bookingpress_locations_service_workhours} WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_location_service_workday_key = %s AND bookingpress_location_service_workhour_is_break = %d", $location_id, $service_id, $days_val, 1 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_workhours is table name defined globally.
                        if( !empty( $location_break_hour_data ) ){
                            $break_counter = 0;
                            foreach( $location_break_hour_data as $location_break_hour_timings ){
                                $lc_break_start_time = $location_break_hour_timings->bookingpress_location_service_workhour_start_time;
                                $lc_break_end_time =  $location_break_hour_timings->bookingpress_location_service_workhour_end_time;

                                $lc_break_timings_data = array(
                                    'start_time'            => $lc_break_start_time,
                                    'formatted_start_time'  => date( $bookingpress_options['wp_default_time_format'], strtotime( $lc_break_start_time ) ),
                                    'end_time'              => $lc_break_end_time,
                                    'formatted_end_time'    => date( $bookingpress_options['wp_default_time_format'], strtotime( $lc_break_end_time ) )
                                );

                                $bpa_location_wise_break_timings[ $location_id ][ $days_val ][ $break_counter ] = $lc_break_timings_data;

                                $break_counter++;
                            }
                        } else {
                            if( !empty( $response['workhour_data'] ) ){
                                $service_specific_workhour_data = $response['workhour_data'];
                                foreach( $service_specific_workhour_data as $key => $ss_break_details ){
                                    if( $ss_break_details[ 'day_name'] == $days_val ){
                                        $bpa_location_wise_break_timings[ $location_id ][ $days_val ] = $ss_break_details['break_times'];
                                    }
                                }
                            } else {
                                $bpa_location_wise_break_timings[ $location_id ][ $days_val ] = array();
                            }
                        }
                    }
                }
                $response['location_wise_break_hours'] = $bpa_location_wise_break_timings;
                $response['location_wise_workhours'] = $bpa_location_wise_workhours;
                
                if( empty( $bpa_location_wise_workhours ) && !empty( $service_workhours_data['workhour_service_data'] ) ){    
                    foreach( $all_location_ids as $location_data ){
                        $location_id = $location_data->bookingpress_location_id;
                        $bpa_location_wise_workhours[ $location_id ] = $service_workhours_data['workhour_service_data'];
                    }
                }
                $response['location_wise_workhours'] = $bpa_location_wise_workhours;
            } else {

                if( !empty( $all_location_ids ) ){
                    $bpa_location_wise_workhours = array();
                    
                    foreach( $all_location_ids as $location_data ){
                        $location_id = $location_data->bookingpress_location_id;
                        $bpa_location_wise_workhours[ $location_id ] = $service_workhours_data['selected_workhours'];
                    }
                }
                $response['location_wise_workhours'] = $bpa_location_wise_workhours;
                //echo wp_json_encode( $service_workhours_data );
            }

            /** Location wise special days start */
            if( !empty( $all_location_ids ) && !empty( $service_id ) ){
                $bpa_location_wise_special_days = array();
                foreach( $all_location_ids as $location_data ){
                    $location_id = $location_data->bookingpress_location_id;
                    $location_wise_db_special_days_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_service_special_days WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_location_service_special_day_has_break = %d", $location_id, $service_id, 0 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_special_days is table name defined globally.
                    if( !isset( $bpa_location_wise_special_days[ $location_id] ) ){
                        $bpa_location_wise_special_days[ $location_id ] = array();
                    }
                    if( !empty( $location_wise_db_special_days_details ) ){
                        $location_wise_sp_details_break_details = array();
                        $location_wise_sp_details_break_details[ $location_id ] = array();
                        foreach( $location_wise_db_special_days_details as $location_wise_sp_details ){
                            
                            $bpa_lc_spd_start_date = $location_wise_sp_details->bookingpress_location_service_special_day_start_date;
                            $bpa_lc_spd_end_date = $location_wise_sp_details->bookingpress_location_service_special_day_end_date;

                            $bpa_lc_spd_start_time = $location_wise_sp_details->bookingpress_location_service_special_day_start_time;
                            $bpa_lc_spd_end_time = $location_wise_sp_details->bookingpress_location_service_special_day_end_time;

                            /** Retrieve associated break hours */
                            $location_wise_db_special_days_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_service_special_days WHERE bookingpress_location_id = %d AND bookingpress_service_id = %d AND bookingpress_location_service_special_day_has_break = %d AND bookingpress_location_service_special_day_start_date = %s AND bookingpress_location_service_special_day_end_date = %s", $location_id, $service_id, 1, $bpa_lc_spd_start_date, $bpa_lc_spd_end_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_service_special_days is table name defined globally.

                            $bpa_lc_special_breaks = array();
                            if( !empty( $location_wise_db_special_days_details ) ){
                                $break_counter = 1;
                                foreach( $location_wise_db_special_days_details as $lc_spd_break_data ){
                                    $break_start_time = $lc_spd_break_data->bookingpress_location_service_special_day_break_start_time;
                                    $break_end_time = $lc_spd_break_data->bookingpress_location_service_special_day_break_end_time;

                                    $bpa_lc_special_breaks[] = array(
                                        'start_time' => $break_start_time,
                                        'end_time' => $break_end_time,
                                        'start_times' => '',
                                        'end_times' => '',
                                        'id' => $break_counter,
                                        'formatted_start_time' => date( $bookingpress_options['wp_default_time_format'], strtotime( $break_start_time ) ),
                                        'formatted_end_time' => date( $bookingpress_options['wp_default_time_format'], strtotime( $break_end_time ) ),
                                    );
                                    $break_counter++;
                                }
                            }

                            $bpa_location_wise_special_days[ $location_id ][] = array(
                                'special_day_start_date' => $bpa_lc_spd_start_date,
                                'special_day_end_date' => $bpa_lc_spd_end_date,
                                'start_time' => $bpa_lc_spd_start_time,
                                'end_time' => $bpa_lc_spd_end_time,
                                'special_day_formatted_start_date' => date( $bookingpress_options['wp_default_date_format'], strtotime($bpa_lc_spd_start_date)),
                                'special_day_formatted_end_date' => date( $bookingpress_options['wp_default_date_format'], strtotime( $bpa_lc_spd_end_date )),
                                'formatted_start_time' => date( $bookingpress_options['wp_default_time_format'], strtotime( $bpa_lc_spd_start_time ) ),
                                'formatted_end_time' => date( $bookingpress_options['wp_default_time_format'], strtotime( $bpa_lc_spd_end_time ) ),
                                'special_day_workhour' => $bpa_lc_special_breaks
                            );
                            
                        }
                    }
                }
                $response['location_wise_special_days'] = $bpa_location_wise_special_days;
            }
            /** Location wise special days end */
 
            return $response;
        }

        function bookingpress_add_location_args_on_staff_shift_xhr_data(){
            ?>
            if( "undefined" != typeof staff_members_action ) {
                staff_members_action.bpa_fetch_location_wise_data = !vm2.display_staff_working_hours;
                staff_members_action.update_from_panel = true;
            } else {
                postdata.bpa_fetch_location_wise_data = !vm.display_staff_working_hours;
            }
            <?php
        }

        function bookingpress_fetch_location_wise_staff_workhours( $response ){

            if( !empty( $_POST['bpa_fetch_location_wise_data'] ) && true == $_POST['bpa_fetch_location_wise_data'] ){ // phpcs:ignore
                global $wpdb, $tbl_bookingpress_locations_staff_workhours, $tbl_bookingpress_locations, $bookingpress_global_options, $tbl_bookingpress_locations_staff_special_days, $tbl_bookingpress_staffmembers;

                $bookingpress_options = $bookingpress_global_options->bookingpress_global_options();

                $staffmember_id = !empty( $_POST['staffmember_id'] ) ? intval( $_POST['staffmember_id'] ) : 0; // phpcs:ignore

                if( empty( $staffmember_id ) && !empty( $_REQUEST['update_from_panel']) && true == $_REQUEST['update_from_panel'] ){
                    $bookingpress_user_id  = get_current_user_id();
                    $bookingpress_existing_staffmember_details = $wpdb->get_row( $wpdb->prepare( "SELECT `bookingpress_staffmember_id` FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_wpuser_id = %d AND bookingpress_staffmember_status = 1 ORDER BY bookingpress_staffmember_id DESC", $bookingpress_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
                    if ( ! empty( $bookingpress_existing_staffmember_details ) ) {
                        $staffmember_id = ! empty( $bookingpress_existing_staffmember_details['bookingpress_staffmember_id'] ) ? $bookingpress_existing_staffmember_details['bookingpress_staffmember_id'] : '';
                    }
                }

                $db_locations = $wpdb->get_results( "SELECT bookingpress_location_id FROM $tbl_bookingpress_locations" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.
                $bookingpress_workhour_days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

                if( !empty( $staffmember_id ) && !empty( $db_locations )){
                    $staffwise_location_details = array();
                    $staffwise_location_break_details = array();

                    $staffwise_location_special_days = array();

                    foreach( $db_locations as $location_details ){
                        $location_id = $location_details->bookingpress_location_id;
                        foreach( $bookingpress_workhour_days as $days_val ){

                            /** Location wise staff's working hour start */
                            $staff_workhour_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_staff_workhours WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_workday_key = %s AND bookingpress_location_staff_workhour_is_break = %d", $location_id, $staffmember_id, $days_val, 0 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_workhours is table name defined globally.
                            
                            if( empty( $staff_workhour_data ) ){
                                $default_selected_data = json_decode( json_encode( $response['selected_workhours'][$days_val] ) );
                                $staff_workhour_data = new stdClass();
                                $staff_workhour_data->bookingpress_location_staff_workhour_start_time = $response['selected_workhours'][$days_val]['start_time'];
                                $staff_workhour_data->bookingpress_location_staff_workhour_end_time = $response['selected_workhours'][$days_val]['end_time'];
                            }
                            
                            $staff_workhour_start_time = !empty( $staff_workhour_data->bookingpress_location_staff_workhour_start_time ) ? $staff_workhour_data->bookingpress_location_staff_workhour_start_time : 'Off';
                            $staff_workhour_end_time = !empty( $staff_workhour_data->bookingpress_location_staff_workhour_end_time ) ? $staff_workhour_data->bookingpress_location_staff_workhour_end_time : 'Off';
                            
                            $staffwise_location_details[ $location_id ][ $days_val ] ['start_time'] = $staff_workhour_start_time;
                            $staffwise_location_details[ $location_id ][ $days_val ] ['end_time'] = $staff_workhour_end_time;

                            $staff_break_details = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_staff_workhours WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_workday_key = %s AND bookingpress_location_staff_workhour_is_break = %d", $location_id, $staffmember_id, $days_val, 1 ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_workhours is table name defined globally.

                            if( !empty( $staff_break_details ) ){
                                foreach( $staff_break_details as $staff_break_data ){
                                    $break_start_time = $staff_break_data->bookingpress_location_staff_workhour_start_time;
                                    $break_end_time = $staff_break_data->bookingpress_location_staff_workhour_end_time;

                                    $formatted_start_time = date( $bookingpress_options['wp_default_time_format'], strtotime( $break_start_time ) );
                                    $formatted_end_time = date( $bookingpress_options['wp_default_time_format'], strtotime( $break_end_time ) );

                                    $staffwise_location_break_details[ $location_id ][ $days_val ][] = array(
                                        'start_time' => $break_start_time,
                                        'end_time' => $break_end_time,
                                        'formatted_start_time' => $formatted_start_time,
                                        'formatted_end_time' => $formatted_end_time
                                    );
                                }
                            } else {
                                $staffwise_location_break_details[ $location_id ][ $days_val ] = array();
                            }

                            /** Location wise staff's working hour end */
                        }

                        /** Location wise staff's special days start */
                        

                        $staff_special_days_without_break = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_staff_special_days WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_special_day_has_break = %d", $location_id, $staffmember_id, 0 )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_special_days is table name defined globally.
                        
                        if( !empty( $staff_special_days_without_break ) ){
                            $staff_special_day_details = array();
                            foreach( $staff_special_days_without_break as $staff_special_day_data ){
                                
                                $ssd_start_time = $staff_special_day_data->bookingpress_location_staff_special_day_start_time;
                                $ssd_end_time = $staff_special_day_data->bookingpress_location_staff_special_day_end_time;

                                $ssd_formatted_start_time= date( $bookingpress_options['wp_default_time_format'], strtotime( $ssd_start_time ) );
                                $ssd_formatted_end_time= date( $bookingpress_options['wp_default_time_format'], strtotime( $ssd_end_time ) );

                                $ssd_start_date = $staff_special_day_data->bookingpress_location_staff_special_day_start_date;
                                $ssd_end_date = $staff_special_day_data->bookingpress_location_staff_special_day_end_date;

                                $ssd_formatted_start_date = date( $bookingpress_options['wp_default_date_format'], strtotime( $ssd_start_date ) );
                                $ssd_formatted_end_date = date( $bookingpress_options['wp_default_date_format'], strtotime( $ssd_end_date ) );

                                $ssd_special_day_id = $staff_special_day_data->bookingpress_location_staff_special_day_id;

                                $staff_special_day_details_temp = array(
                                    'start_time' => $ssd_start_time,
                                    'end_time' => $ssd_end_time,
                                    'formatted_start_time' => $ssd_formatted_start_time,
                                    'formatted_end_time' => $ssd_formatted_end_time,
                                    'special_day_start_date' => $ssd_start_date,
                                    'special_day_end_date' => $ssd_end_date,
                                    'special_day_formatted_start_date' => $ssd_formatted_start_date,
                                    'special_day_formatted_end_date' => $ssd_formatted_end_date,
                                );

                                $ssd_services = !empty( $staff_special_day_data->bookingpress_location_staff_special_day_service_id ) ? explode( ',', $staff_special_day_data->bookingpress_location_staff_special_day_service_id ) : array();
                                if( !empty( $ssd_services ) ){
                                    $staff_special_day_details_temp['special_day_service'] = $ssd_services;
                                }

                                /** Staff special day break timings start */
                                $staff_sd_breaks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tbl_bookingpress_locations_staff_special_days WHERE bookingpress_location_id = %d AND bookingpress_staffmember_id = %d AND bookingpress_location_staff_special_day_has_break = %d AND bookingpress_location_staff_special_day_start_date = %s AND bookingpress_location_staff_special_day_end_date = %s", $location_id, $staffmember_id, 1, $ssd_start_date, $ssd_end_date ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations_staff_special_days is table name defined globally.

                                if( !empty( $staff_sd_breaks ) ) {
                                    $staff_break_hours = array();
                                    foreach( $staff_sd_breaks as $ssd_break_data ){
                                        $ssd_break_start_time = $ssd_break_data->bookingpress_location_staff_special_day_break_start_time;
                                        $ssd_break_end_time =  $ssd_break_data->bookingpress_location_staff_special_day_break_end_time;

                                        $ssd_break_formatted_start_time = date( $bookingpress_options['wp_default_time_format'], strtotime( $ssd_break_start_time ) );
                                        $ssd_break_formatted_end_time = date( $bookingpress_options['wp_default_time_format'], strtotime( $ssd_break_end_time ) );

                                        $staff_break_hours[] = array(
                                            'start_time' => $ssd_break_start_time,
                                            'end_time' => $ssd_break_end_time,
                                            'formatted_start_time' => $ssd_break_formatted_start_time,
                                            'formatted_end_time' => $ssd_break_formatted_end_time
                                        );
                                    }

                                    if( !empty( $staff_break_hours ) ){
                                        $staff_special_day_details_temp['special_day_workhour'] = $staff_break_hours;
                                    }
                                }

                                $staff_special_day_details[] = $staff_special_day_details_temp;
                                /** Staff special day break timings end */
                            }
                            /* echo "<pre>";
                            print_r( $staff_special_day_details );
                            echo "</pre>"; */
                            $staffwise_location_special_days[ $location_id ] = $staff_special_day_details;
                        } else {
                            $staffwise_location_special_days[ $location_id ] = array();
                        }
                    
                        /** Location wise staff's special days start */
                    }
                    $response['location_wise_staff_workhours'] = $staffwise_location_details;
                    $response['location_wise_staff_breakhours'] = $staffwise_location_break_details;
                    $response['location_wise_staff_sepcial_days'] = $staffwise_location_special_days;
                }
            }


            return $response;
        }

        function bookingpress_set_location_wise_staff_workhours(){
            ?>

            if( "undefined" == typeof vm && "undefined" != typeof vm2 ){
                vm = vm2;
            }

            if( "undefined" != typeof response.data.location_wise_staff_workhours && "" != response.data.location_wise_staff_workhours ){
                vm.location_wise_workhours_timings = response.data.location_wise_staff_workhours;
            }

            if( "undefined" != typeof response.data.location_wise_staff_breakhours && "" != response.data.location_wise_staff_breakhours ){
                vm.location_wise_default_break_timings = response.data.location_wise_staff_breakhours;
            }

            if( "undefined" != typeof response.data.location_wise_staff_sepcial_days && "" != response.data.location_wise_staff_sepcial_days ){
                vm.location_wise_staffmember_special_day_arr = response.data.location_wise_staff_sepcial_days;
            }
            <?php
        }

        function bookingpress_staff_workinghour_location_details(){
            ?>
                postdata.location_wise_staff_workhours = JSON.stringify( vm2.location_wise_workhours_timings );
                postdata.location_wise_staff_breakhours = JSON.stringify( vm2.location_wise_default_break_timings );
                postdata.location_wise_staff_special_days = JSON.stringify( vm2.location_wise_staffmember_special_day_arr );
                postdata.update_from_panel = true;
            <?php
        }

        function bookingpress_save_staff_member_func(){
            ?>
                
                if( "undefined" != typeof postdata.bookingpress_action && postdata.bookingpress_action == 'bookingpress_shift_managment'){
                    postdata.location_wise_staff_workhours = JSON.stringify( vm2.location_wise_workhours_timings );
                    postdata.location_wise_staff_breakhours = JSON.stringify( vm2.location_wise_default_break_timings );
                    postdata.location_wise_staff_special_days = JSON.stringify( vm2.location_wise_staffmember_special_day_arr );
                }
            <?php
        }

        function bookingpress_staff_shift_location_wise_workhours(){
            /**
             * bookingpress_shift_mgmt_selected_location
             * v-for="(location_details, key) in bookingpress_locations"
             * */
            ?>
            <div class="bpa-sm__wh-items" v-if="bookingpress_configure_specific_workhour == true && display_staff_working_hours == false">
                <div class="bpa-sm__wh-body-row--parent" v-for="(location_details, key) in bookingpress_locations" v-if="location_details.bookingpress_location_id == bookingpress_shift_mgmt_selected_location">
                    <div class="bpa-sm__wh-body-row" v-for="work_hours_day in work_hours_days_arr">
                        <el-row class="bpa-sm__wh-item-row" :gutter="24" :id="'weekday_'+work_hours_day.day_key">
                            <el-col :xs="24" :sm="24" :md="18" :lg="20" :xl="22">
                                <el-row type="flex" class="bpa-sm__wh-body-left">
                                    <el-col :xs="24" :sm="24" :md="6" :lg="6" :xl="2">
                                        <span class="bpa-form-label" v-if="work_hours_day.day_name == 'Monday'"><?php esc_html_e('Monday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Tuesday'"><?php esc_html_e('Tuesday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Wednesday'"><?php esc_html_e('Wednesday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Thursday'"><?php esc_html_e('Thursday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Friday'"><?php esc_html_e('Friday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Saturday'"><?php esc_html_e('Saturday', 'bookingpress-location'); ?></span>
                                        <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Sunday'"><?php esc_html_e('Sunday', 'bookingpress-location'); ?></span>
                                        <span v-else>{{ work_hours_day.day_name }}</span>
                                    </el-col>
                                    <el-col :xs="24" :sm="24" :md="18" :lg="18" :xl="22">
                                        <el-row :gutter="24">
                                            <el-col :xs="8" :sm="8" :md="12" :lg="12" :xl="12">
                                                <el-select v-model="location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].start_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'Start Time', 'bookingpress-location' ); ?>"
                                                    @change="bookingpress_set_workhour_value($event,work_hours_day.day_name)" filterable>
                                                    <span slot="prefix" class="material-icons-round">access_time</span>
                                                    <el-option v-for="work_timings in work_hours_day.worktimes" :label="work_timings.formatted_start_time" :value="work_timings.start_time" v-if="work_timings.start_time != location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].end_time || location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].end_time == 'Off'"></el-option>
                                                </el-select>
                                            </el-col>
                                            <el-col :xs="8" :sm="8" :md="12" :lg="12" :xl="12" v-if="location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].start_time != 'Off'">
                                                <el-select v-model="location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].end_time" class="bpa-form-control bpa-form-control__left-icon" 
                                                    placeholder="<?php esc_html_e( 'End Time', 'bookingpress-location' ); ?>"
                                                    @change="bookingpress_check_workhour_value($event,work_hours_day.day_name)"  filterable>
                                                    <span slot="prefix" class="material-icons-round">access_time</span>
                                                    <el-option v-for="work_timings in work_hours_day.worktimes" :label="work_timings.formatted_end_time" :value="work_timings.end_time" v-if="(work_timings.end_time > location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].start_time ||  work_timings.end_time == '24:00:00')"></el-option>				
                                                </el-select>
                                            </el-col>
                                        </el-row>
                                        <el-row  v-if=" 'undefined' != typeof location_wise_default_break_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name] && location_wise_default_break_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name] != '' && location_wise_workhours_timings[ bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].start_time != 'Off'">
                                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                                <div class="bpa-break-hours-wrapper">
                                                    <h4><?php esc_html_e( 'Breaks', 'bookingpress-location' ); ?></h4>
                                                    <div class="bpa-bh--items">
                                                        <div class="bpa-bh__item" v-for="(break_data,index) in location_wise_default_break_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name]">
                                                            <p @click="edit_workhour_data(event,break_data.start_time, break_data.end_time, work_hours_day.day_name,index)">{{ break_data.formatted_start_time }} to {{ break_data.formatted_end_time }}</p>
                                                            <span class="material-icons-round" slot="reference" @click="bookingpress_remove_workhour(break_data.start_time, break_data.end_time, work_hours_day.day_name)">close</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </el-col>
                                        </el-row>
                                    </el-col>
                                </el-row>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="6" :lg="4" :xl="2" v-if="location_wise_workhours_timings[bookingpress_shift_mgmt_selected_location][work_hours_day.day_name].start_time != 'Off'">
                                <el-button class="bpa-btn bpa-btn__medium bpa-btn--full-width" :class="(break_selected_day == work_hours_day.day_name && open_add_break_modal == true) ? 'bpa-btn--primary' : ''" @click="open_add_break_modal_func(event, work_hours_day.day_name)">
                                    <?php esc_html_e( 'Add Break', 'bookingpress-location' ); ?>
                                </el-button>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </div>
            <?php
        }

        function bookingpress_after_open_staff_shift_mgmt_modal_func(){
            ?>
                setTimeout(function(){
                    if(document.getElementsByClassName("bpa-dialog__shift-management") != null){
                        document.getElementsByClassName("bpa-dialog__shift-management")[0].classList.add("bpa-dialog__multi-location-enabled");
                    }

                    if(document.getElementById("bpa-db-card-is-full-width-title") != null){
                        document.getElementById("bpa-db-card-is-full-width-title").classList.add("bpa-mle__default-card");
                    }
                }, 500);
            <?php
        }

        function bookingpress_modify_staffmember_data_fields_func($bookingpress_staff_member_vue_data_fields){
            global $wpdb, $tbl_bookingpress_locations;
            
            //Get location details
            $bookingpress_location_data = $wpdb->get_results("SELECT bookingpress_location_id, bookingpress_location_name FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_id ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bookingpress_staff_member_vue_data_fields['bookingpress_locations'] = $bookingpress_location_data;
            $bookingpress_staff_member_vue_data_fields['bookingpress_shift_mgmt_selected_location'] = '';
            if(!empty($bookingpress_location_data)){
                //Default first location selected
                $bookingpress_staff_member_vue_data_fields['bookingpress_shift_mgmt_selected_location'] = $bookingpress_location_data[0]['bookingpress_location_id'];
            }

            $bookingpress_staff_member_vue_data_fields['display_staff_working_hours'] = false;

            $bpa_location_wise_workhours = array();
            $bpa_location_wise_service_break_timings = array();
            $bpa_location_wise_special_days = array();
            foreach( $bookingpress_location_data as $location_data ){
                $location_id = $location_data['bookingpress_location_id'];
                $bpa_location_wise_workhours[ $location_id ] = array(
                    'Monday'    => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Tuesday'   => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Wednesday' => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Thursday'  => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Friday'    => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Saturday'  => array(
                        'start_time' => 'Off',
                        'end_time'   => 'Off',
                    ),
                    'Sunday'    => array(
                        'start_time' => 'Off',
                        'end_time'   => 'Off',
                    ),
                );

                $bpa_location_wise_service_break_timings[ $location_id ] = array(
                    'Monday'    => array(),
                    'Tuesday'   => array(),
                    'Wednesday' => array(),
                    'Thursday'  => array(),
                    'Friday'    => array(),
                    'Saturday'  => array(),
                    'Sunday'    => array(),
                );

                $bpa_location_wise_special_days[ $location_id ] = array();
            }    

            $bookingpress_staff_member_vue_data_fields['location_wise_workhours_timings'] = $bpa_location_wise_workhours;
            $bookingpress_staff_member_vue_data_fields['location_wise_default_break_timings'] = $bpa_location_wise_service_break_timings;

            $bookingpress_staff_member_vue_data_fields['location_wise_staffmember_special_day_arr'] = $bpa_location_wise_special_days;
            $bookingpress_staff_member_vue_data_fields['location_wise_staff_special_day_edit_index'] = -1;


            return $bookingpress_staff_member_vue_data_fields;
        }

        function bookingpress_add_staffmember_shift_management_content_func(){
            ?>
                <div class="bpa-mle-dc__item">
                    <h3 class="bpa-page-heading bpa-mle-dc--heading"><?php esc_html_e( 'Select Locations', 'bookingpress-location' ); ?></h3>
                    <div class="bpa-cmc--tab-menu">                                
                        <div class="bpa-cms-tm__body">
                            <el-button class="bpa-btn bpa-btn--icon-without-box" @click="bpa_move_staff_location_nav_prev()"><span class="material-icons-round">west</span></el-button>
                            <el-radio-group v-model="bookingpress_shift_mgmt_selected_location" id="bpa_staff_shift_management_locations">
                                <el-radio-button :label="location_details.bookingpress_location_id" v-for="(location_details, key) in bookingpress_locations">{{ location_details.bookingpress_location_name }}</el-radio-button>
                            </el-radio-group>
                            <el-button class="bpa-btn bpa-btn--icon-without-box" @click="bpa_move_staff_location_nav_next()"><span class="material-icons-round">east</span></el-button>
                        </div>
                    </div> 
                </div>
            <?php
        }

        function bookingpress_modify_service_data_fields_func( $bookingpress_services_vue_data_fields ) {
            global $wpdb, $tbl_bookingpress_locations, $tbl_bookingpress_locations_service_staff_pricing_details;

            //Get location details
            $bookingpress_location_data = $wpdb->get_results("SELECT bookingpress_location_id, bookingpress_location_name FROM {$tbl_bookingpress_locations} ORDER BY bookingpress_location_id ASC", ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Reason: $tbl_bookingpress_locations is table name defined globally.

            $bookingpress_services_vue_data_fields['bookingpress_locations'] = $bookingpress_location_data;
            $bookingpress_services_vue_data_fields['bookingpress_shift_mgmt_selected_location'] = '';

            
            if(!empty($bookingpress_location_data)){
                //Default first location selected
                $bookingpress_services_vue_data_fields['bookingpress_shift_mgmt_selected_location'] = $bookingpress_location_data[0]['bookingpress_location_id'];
            }

            $bookingpress_services_vue_data_fields['display_service_workhours'] = false;

            $bpa_location_wise_workhours = array();
            $bpa_location_wise_service_break_timings = array();
            $bpa_location_wise_special_days = array();
            foreach( $bookingpress_location_data as $location_data ){
                $location_id = $location_data['bookingpress_location_id'];
                $bpa_location_wise_workhours[ $location_id ] = array(
                    'Monday'    => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Tuesday'   => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Wednesday' => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Thursday'  => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Friday'    => array(
                        'start_time' => '09:00:00',
                        'end_time'   => '17:00:00',
                    ),
                    'Saturday'  => array(
                        'start_time' => 'Off',
                        'end_time'   => 'Off',
                    ),
                    'Sunday'    => array(
                        'start_time' => 'Off',
                        'end_time'   => 'Off',
                    ),
                );

                $bpa_location_wise_service_break_timings[ $location_id ] = array(
                    'Monday'    => array(),
                    'Tuesday'   => array(),
                    'Wednesday' => array(),
                    'Thursday'  => array(),
                    'Friday'    => array(),
                    'Saturday'  => array(),
                    'Sunday'    => array(),
                );

                $bpa_location_wise_special_days[ $location_id ] = array();
            }    

            $bookingpress_services_vue_data_fields['service']['location_wise_workhours'] = $bpa_location_wise_workhours;
            $bookingpress_services_vue_data_fields['service']['location_wise_break_timings'] = $bpa_location_wise_service_break_timings;

            $bookingpress_services_vue_data_fields['location_wise_special_day_data_arr'] = $bpa_location_wise_special_days;

            $bookingpress_services_vue_data_fields['location_wise_special_day_edit_index'] = -1;

            return $bookingpress_services_vue_data_fields;
        }

        function bookingpress_modify_validate_staff_special_days_where_caluse_with_location( $bookingpress_search_query_where ){

            if( !empty( $_REQUEST['selected_location'] ) ){
                global $wpdb;
                $bookingpress_search_query_where .= $wpdb->prepare( ' AND bookingpress_location_id = %d', intval( $_REQUEST['selected_location'] ) ); //phpcs:ignore
            }

            return $bookingpress_search_query_where;
        }

        function bookingpress_modify_validate_service_special_days_where_caluse_with_location( $bookingpress_search_query_where ){

            if( !empty( $_REQUEST['selected_location'] ) ){
                global $wpdb;
                $bookingpress_search_query_where .= $wpdb->prepare( ' AND bookingpress_location_id = %d ', intval( $_REQUEST['selected_location']) ); //phpcs:ignore
            }

            return $bookingpress_search_query_where;
        }

        function bookingpress_format_staff_special_days_data_with_location_func(){
            global $wpdb, $bookingpress_global_options,$BookingPress;
			$response                    = array();

            if( !empty( $_POST['update_from_panel'] ) ){ // phpcs:ignore
                $bpa_check_authorization = $this->bpa_check_authentication( 'timesheet_add_staffmember_special_days', true, 'bpa_wp_nonce' );
            } else {
                $bpa_check_authorization = $this->bpa_check_authentication( 'format_staffmember_daysoff_data', true, 'bpa_wp_nonce' );
            }
            
            if( preg_match( '/error/', $bpa_check_authorization ) ){
                $bpa_auth_error = explode( '^|^', $bpa_check_authorization );
                $bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

                $response['variant'] = 'error';
                $response['title'] = esc_html__( 'Error', 'bookingpress-location');
                $response['msg'] = $bpa_error_msg;

                wp_send_json( $response );
                die;
            }

            $response['daysoff_details'] = '';

            $bookingpress_global_settings = $bookingpress_global_options->bookingpress_global_options();
            $bookingpress_date_format = $bookingpress_global_settings['wp_default_date_format'];
            $bookingpress_time_format = $bookingpress_global_settings['wp_default_time_format'];
            $bookingpress_special_days_data  = !empty( $_POST['special_days_data'] ) ? array_map( array( $BookingPress, 'appointment_sanatize_field'), $_POST['special_days_data']) : array(); //phpcs:ignore

            $bookingpress_selected_location =  !empty( $_POST['selected_location'] ) ? intval( $_POST['selected_location'] ) : 0; // phpcs:ignore

            if( !empty( $bookingpress_special_days_data ) && is_array( $bookingpress_special_days_data ) ){
                foreach( $bookingpress_special_days_data as $k => $v ){
                    $bookingpress_special_days_data[ $k ]['special_day_formatted_start_date'] = date( $bookingpress_date_format, strtotime( $v['special_day_start_date'] ) );
                    $bookingpress_special_days_data[ $k ]['special_day_formatted_end_date'] = date( $bookingpress_date_format, strtotime( $v['special_day_end_date'] ) );
                    $bookingpress_special_days_data[ $k ]['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $v['start_time'] ) );
                    $bookingpress_special_days_data[ $k ]['formatted_end_time'] = date( $bookingpress_time_format, strtotime( $v['end_time'] ) );

                    if( !empty( $v['special_day_workhour'] ) ){
                        foreach( $v['special_day_workhour']  as $k2 => $v2 ){
                            $bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $v2['start_time'] ) );
                            $bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_end_time'] = date( $bookingpress_time_format, strtotime( $v2['end_time'] ) );
                        }
                    }
                }
                $response['variant']         = 'success';
				$response['title']           = esc_html__( 'Success', 'bookingpress-location' );
				$response['msg']             = esc_html__( 'Details formatted successfully', 'bookingpress-location' );
				$response['daysoff_details'] = $bookingpress_special_days_data;
            }

            echo wp_json_encode( $response );
			exit;
        }

        function bookingpress_format_service_special_days_data_with_location_func(){
            global $wpdb, $bookingpress_global_options,$BookingPress;
			$response                    = array();
			$bpa_check_authorization = $this->bpa_check_authentication( 'format_service_special_days', true, 'bpa_wp_nonce' );           
			if( preg_match( '/error/', $bpa_check_authorization ) ){
				$bpa_auth_error = explode( '^|^', $bpa_check_authorization );
				$bpa_error_msg = !empty( $bpa_auth_error[1] ) ? $bpa_auth_error[1] : esc_html__( 'Sorry. Something went wrong while processing the request', 'bookingpress-location');

				$response['variant'] = 'error';
				$response['title'] = esc_html__( 'Error', 'bookingpress-location');
				$response['msg'] = $bpa_error_msg;

				wp_send_json( $response );
				die;
			}
			$response['daysoff_details'] = '';
			$bookingpress_global_settings   = $bookingpress_global_options->bookingpress_global_options();
			$bookingpress_date_format       = $bookingpress_global_settings['wp_default_date_format'];
			$bookingpress_time_format       = $bookingpress_global_settings['wp_default_time_format'];
			$bookingpress_special_days_data = ! empty( $_POST['special_days_data'] ) ? array_map(array($BookingPress,'appointment_sanatize_field'),$_POST['special_days_data']) : array(); //phpcs:ignore
            
            if ( ! empty( $bookingpress_special_days_data ) && is_array( $bookingpress_special_days_data ) ) {
				foreach ( $bookingpress_special_days_data as $k => $v ) {
					$bookingpress_special_days_data[ $k ]['special_day_formatted_start_date'] = date( $bookingpress_date_format, strtotime( $v['special_day_start_date'] ) );
					$bookingpress_special_days_data[ $k ]['special_day_formatted_end_date']   = date( $bookingpress_date_format, strtotime( $v['special_day_end_date'] ) );
					$bookingpress_special_days_data[ $k ]['formatted_start_time']             = date( $bookingpress_time_format, strtotime( $v['start_time'] ) );
					$bookingpress_special_days_data[ $k ]['formatted_end_time']               = date( $bookingpress_time_format, strtotime( $v['end_time'] ) );
					if ( ! empty( $v['special_day_workhour'] ) ) {
						foreach ( $v['special_day_workhour'] as $k2 => $v2 ) {
							$bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_start_time'] = date( $bookingpress_time_format, strtotime( $v2['start_time'] ) );
							$bookingpress_special_days_data[ $k ]['special_day_workhour'][ $k2 ]['formatted_end_time']   = date( $bookingpress_time_format, strtotime(  $v2['end_time'] ) );
						}
					}
				}
				$response['variant']         = 'success';
				$response['title']           = esc_html__( 'Success', 'bookingpress-location' );
				$response['msg']             = esc_html__( 'Details formatted successfully', 'bookingpress-location' );
				$response['daysoff_details'] = $bookingpress_special_days_data;
			}
            
			echo wp_json_encode( $response );
			exit;
        }

        function bpa_add_service_shift_save_special_days_data_with_location(){
            global $bookingpress_notification_duration;
            ?>
            if( "undefined" != typeof vm.display_service_workhours && vm.display_service_workhours == false ){
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                this.$refs[special_day_form].validate((valid) => {
                    if( valid ){
                        const vm = this;
                        vm.disable_service_special_day_btn = true;
                        var is_exit = 0;
                        if(vm.special_day_form.special_day_workhour != undefined && vm.special_day_form.special_day_workhour != '') {
                            vm.special_day_form.special_day_workhour.forEach(function(item, index, arr) {
                                if(is_exit == 0 && (item.start_time == '' || item.end_time == '' || item.end_time == undefined || item.start_time == undefined)) {
                                    is_exit = 1;
                                    vm.$notify({
                                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                        message: '<?php esc_html_e( 'Please Enter Start Time and End Time', 'bookingpress-location' ); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                        duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                    });                                
                                }
                            });
                        }
                        if(vm.location_wise_special_day_data_arr[selected_location] != undefined && vm.location_wise_special_day_data_arr[selected_location] != '' ) {
                            vm.location_wise_special_day_data_arr[selected_location].forEach(function(item, index, arr) {
                                
                                if((vm.special_day_form.special_day_date[0] == item.special_day_start_date || vm.special_day_form.special_day_date[0] == item.special_day_end_date || ( vm.special_day_form.special_day_date[0] >= item.special_day_start_date && vm.special_day_form.special_day_date[0] <= item.special_day_end_date ) || vm.special_day_form.special_day_date[1] == item.special_day_end_date || vm.special_day_form.special_day_date[1] == item.special_day_start_date || (vm.special_day_form.special_day_date[1] >= item.special_day_start_date && vm.special_day_form.special_day_date[1] <= item.special_day_end_date) || (vm.special_day_form.special_day_date[0] <= item.special_day_start_date && vm.special_day_form.special_day_date[1] >= item.special_day_end_date)) && vm.location_wise_special_day_edit_index != index ) {
                                    is_exit = 1;
                                    vm.$notify({
                                        title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                        message: '<?php esc_html_e( 'Special days already exists', 'bookingpress-location' ); ?>',
                                        type: 'error',
                                        customClass: 'error_notification',
                                        duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                    });
                                }
                            });
                        }

                        if(is_exit == 0) {
                            var postdata = [];
                            postdata.action = 'bookingpress_validate_service_special_days'
                            postdata.service_id = vm.service.service_update_id
                            postdata.selected_date= vm.special_day_form.special_day_date;
                            postdata.selected_location = selected_location;
                            postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
                            axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
                            .then(function(response){
                                if(response.data.variant != 'undefined' && response.data.variant == 'warnning') {					
                                    vm.$confirm(response.data.msg, 'Warning', {
                                        confirmButtonText: '<?php esc_html_e( 'Ok', 'bookingpress-location' ); ?>',
                                        cancelButtonText: '<?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?>',
                                        type: 'warning'
                                    }).then(() => {		
                                        if(vm.location_wise_special_day_edit_index > -1 ){
                                            vm.edit_special_days_with_location();
                                        } else {
                                            vm.add_special_days_with_location();
                                        }
                                    });				
                                }else if(response.data.variant != 'undefined' && response.data.variant  == 'success') {
                                    if(vm.location_wise_special_day_edit_index > -1 ){
                                        vm.edit_special_days_with_location();
                                    } else {
                                        vm.add_special_days_with_location();
                                    }
                                    vm.special_days_add_modal = false
                                }
                                vm.disable_service_special_day_btn = false;
                            }).catch(function(error){
                                vm.$notify({
                                    title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                    message: '<?php esc_html_e( 'Something went wrong..', 'bookingpress-location' ); ?>',
                                    type: 'error_notification',
                                });
                            });	
                        }
                    }
                });
            }
            <?php
        }

        function bpa_add_service_shift_save_break_data_with_location(){
            global $bookingpress_notification_duration;
            ?>
            if( "undefined" != typeof vm.display_service_workhours && vm.display_service_workhours == false ){
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                vm.$refs['break_timings'].validate((valid) => {
                    if(valid) {	
                        var update = 0;
                        if(vm.break_timings.start_time > vm.break_timings.end_time) {
                            vm.$notify({
                                title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                message: '<?php esc_html_e( 'Start time is not greater than End time', 'bookingpress-location' ); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                            });
                        }else if(vm.break_timings.start_time == vm.break_timings.end_time) {					
                            vm.$notify({
                                title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                message: '<?php esc_html_e( 'Start time & End time are not same', 'bookingpress-location' ); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                            });				
                        } else if( vm.service.location_wise_break_timings[ selected_location ][ vm.break_selected_day ] != '' ) {
                            /*vm.service.selected_break_timings[vm.break_selected_day].forEach(function(currentValue, index, arr) {
                            });*/
                            vm.service.location_wise_break_timings[ selected_location ][ vm.break_selected_day ].forEach( function( currentValue, index, arr){
                                if( is_edit == 0 ){
                                    if( vm.service.location_wise_workhours[selected_location][vm.break_selected_day].start_time > vm.break_timings.start_time || vm.service.location_wise_workhours[selected_location][vm.break_selected_day].end_time < vm.break_timings.end_time) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Please enter valid time for break', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                        }else if(currentValue['start_time'] == vm.break_timings.start_time && currentValue['end_time'] == vm.break_timings.end_time && (vm.break_timings.edit_index != index || vm.is_edit_break == 0) ) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Break time already added', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                    }else if(((currentValue['start_time'] < vm.break_timings.start_time  && currentValue['end_time'] > vm.break_timings.start_time) || (currentValue['start_time'] < vm.break_timings.end_time  && currentValue['end_time'] > vm.break_timings.end_time) || (currentValue['start_time'] > vm.break_timings.start_time && currentValue['end_time'] <= vm.break_timings.end_time) || (currentValue['start_time'] >= vm.break_timings.start_time && currentValue['end_time'] < vm.break_timings.end_time )) && (vm.break_timings.edit_index != index || vm.is_edit_break == 0)) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Break time already added', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                    }
                                }
                            });
                            if( is_edit == 0 ) {
									var formatted_start_time = formatted_end_time = '';									
									vm.default_break_timings.forEach(function(currentValue, index, arr) {
										if(currentValue.start_time == vm.break_timings.start_time) {
											formatted_start_time = currentValue.formatted_start_time;
										}
										if(currentValue.end_time == vm.break_timings.end_time) {
											formatted_end_time = currentValue.formatted_end_time;
										}
									});
									if(vm.break_selected_day != '' && vm.is_edit_break != 0) {
										vm.service.location_wise_break_timings[selected_location][vm.break_selected_day].forEach(function(currentValue, index, arr) {
											if(index == vm.break_timings.edit_index) {
												currentValue.start_time = vm.break_timings.start_time;
												currentValue.end_time = vm.break_timings.end_time;
												currentValue.formatted_start_time = formatted_start_time;
												currentValue.formatted_end_time = formatted_end_time;
											}
										});   
									}else {
										vm.service.location_wise_break_timings[selected_location][vm.break_selected_day].push({ start_time: vm.break_timings.start_time, end_time: vm.break_timings.end_time,formatted_start_time:formatted_start_time,formatted_end_time:formatted_end_time });                                    
									}
									vm.close_add_break_model()
								}
                        } else {
                            if(vm.service.location_wise_workhours[selected_location][vm.break_selected_day].start_time > vm.break_timings.start_time || vm.service.location_wise_workhours[selected_location][vm.break_selected_day].end_time < vm.break_timings.end_time) {
                                vm.$notify({
                                    title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                    message: '<?php esc_html_e( 'Please enter valid time for break', 'bookingpress-location' ); ?>',
                                    type: 'error',
                                    customClass: 'error_notification',
                                    duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                });				
                            }else{ 
                                var formatted_start_time = formatted_end_time = '';
                                vm.default_break_timings.forEach(function(currentValue, index, arr) {
                                    if(currentValue.start_time == vm.break_timings.start_time) {
                                        formatted_start_time = currentValue.formatted_start_time;
                                    }
                                    if(currentValue.end_time == vm.break_timings.end_time) {
                                        formatted_end_time = currentValue.formatted_end_time;
                                    }

                                });
                                //vm.service.selected_break_timings[vm.break_selected_day].push({ start_time:vm.break_timings.start_time,end_time: vm.break_timings.end_time, formatted_start_time: formatted_start_time,formatted_end_time:formatted_end_time });

                                vm.service.location_wise_break_timings[ selected_location ][vm.break_selected_day].push({ start_time:vm.break_timings.start_time,end_time: vm.break_timings.end_time, formatted_start_time: formatted_start_time,formatted_end_time:formatted_end_time });

                                vm.close_add_break_model();
                            }
                        }
                    }
                });
            }
            <?php
        }

        function bookingpress_save_staff_break_data_with_location(){
            global $bookingpress_notification_duration;
            ?>
            if( "undefined" != typeof vm.display_staff_working_hours && vm.display_staff_working_hours == false ){
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                vm.$refs['break_timings'].validate((valid) => {                        
                    if(valid) {
                        var update = 0;
                        if(vm.break_timings.start_time > vm.break_timings.end_time) {
                            vm.$notify({
                                title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                message: '<?php esc_html_e('Start time is not greater than End time', 'bookingpress-location'); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                        }else if(vm.break_timings.start_time == vm.break_timings.end_time) {                    
                            vm.$notify({
                                title: '<?php esc_html_e('Error', 'bookingpress-location'); ?>',
                                message: '<?php esc_html_e('Start time & End time are not same', 'bookingpress-location'); ?>',
                                type: 'error',
                                customClass: 'error_notification',
                                duration:<?php echo intval($bookingpress_notification_duration); ?>,
                            });
                        } else if( vm.location_wise_default_break_timings[ selected_location][vm.break_selected_day] != '' ) {
                            vm.location_wise_default_break_timings[ selected_location ][ vm.break_selected_day ].forEach( function ( currentValue, index, arr){
                                if( is_edit == 0 ){
                                    if( vm.location_wise_default_break_timings[ selected_location ][ vm.break_selected_day ].start_time > vm.break_timings.start_time || vm.location_wise_default_break_timings[ selected_location ][ vm.break_selected_day ].end_time < vm.break_timings.end_time) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Please enter valid time for break', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                        }else if(currentValue['start_time'] == vm.break_timings.start_time && currentValue['end_time'] == vm.break_timings.end_time && (vm.break_timings.edit_index != index || vm.is_edit_break == 0) ) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Break time already added', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                    }else if(((currentValue['start_time'] < vm.break_timings.start_time  && currentValue['end_time'] > vm.break_timings.start_time) || (currentValue['start_time'] < vm.break_timings.end_time  && currentValue['end_time'] > vm.break_timings.end_time) || (currentValue['start_time'] > vm.break_timings.start_time && currentValue['end_time'] <= vm.break_timings.end_time) || (currentValue['start_time'] >= vm.break_timings.start_time && currentValue['end_time'] < vm.break_timings.end_time )) && (vm.break_timings.edit_index != index || vm.is_edit_break == 0)) {
                                        is_edit = 1;
                                        vm.$notify({
                                            title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                            message: '<?php esc_html_e( 'Break time already added', 'bookingpress-location' ); ?>',
                                            type: 'error',
                                            customClass: 'error_notification',
                                            duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                        });
                                    }
                                }
                            });
                            if( is_edit == 0 ) {
                                var formatted_start_time = formatted_end_time = '';									
                                vm.default_break_timings.forEach(function(currentValue, index, arr) {
                                    if(currentValue.start_time == vm.break_timings.start_time) {
                                        formatted_start_time = currentValue.formatted_start_time;
                                    }
                                    if(currentValue.end_time == vm.break_timings.end_time) {
                                        formatted_end_time = currentValue.formatted_end_time;
                                    }
                                });
                                if(vm.break_selected_day != '' && vm.is_edit_break != 0) {
                                    vm.location_wise_default_break_timings[selected_location][vm.break_selected_day].forEach(function(currentValue, index, arr) {
                                        if(index == vm.break_timings.edit_index) {
                                            currentValue.start_time = vm.break_timings.start_time;
                                            currentValue.end_time = vm.break_timings.end_time;
                                            currentValue.formatted_start_time = formatted_start_time;
                                            currentValue.formatted_end_time = formatted_end_time;
                                        }
                                    });   
                                }else {
                                    vm.location_wise_default_break_timings[selected_location][vm.break_selected_day].push({ start_time: vm.break_timings.start_time, end_time: vm.break_timings.end_time,formatted_start_time:formatted_start_time,formatted_end_time:formatted_end_time });                                    
                                }
                                vm.close_add_break_model()
                            }
                        } else {
                            if(vm.location_wise_default_break_timings[selected_location][vm.break_selected_day].start_time > vm.break_timings.start_time || vm.location_wise_default_break_timings[selected_location][vm.break_selected_day].end_time < vm.break_timings.end_time) {
                                vm.$notify({
                                    title: '<?php esc_html_e( 'Error', 'bookingpress-location' ); ?>',
                                    message: '<?php esc_html_e( 'Please enter valid time for break', 'bookingpress-location' ); ?>',
                                    type: 'error',
                                    customClass: 'error_notification',
                                    duration:<?php echo intval( $bookingpress_notification_duration ); ?>,
                                });
                            } else {
                                var formatted_start_time = formatted_end_time = '';
                                vm.default_break_timings.forEach(function(currentValue, index, arr) {
                                    if(currentValue.start_time == vm.break_timings.start_time) {
                                        formatted_start_time = currentValue.formatted_start_time;
                                    }
                                    if(currentValue.end_time == vm.break_timings.end_time) {
                                        formatted_end_time = currentValue.formatted_end_time;
                                    }
                                });

                                vm.location_wise_default_break_timings[ selected_location ][vm.break_selected_day].push({ start_time:vm.break_timings.start_time,end_time: vm.break_timings.end_time, formatted_start_time: formatted_start_time,formatted_end_time:formatted_end_time });

                                vm.close_add_break_model()
                            }
                        }
                    }
                });
            }
            <?php
        }

        function bookingpress_save_staff_workhour_with_location( $response ){

            global $wpdb, $tbl_bookingpress_locations_staff_workhours, $tbl_bookingpress_locations_staff_special_days, $tbl_bookingpress_staffmembers;
            $staffmember_id = !empty( $_POST['update_id'] ) ? intval( $_POST['update_id'] ) : ''; // phpcs:ignore
            /** Location wise staff's working hours start */

            
            if( empty( $staffmember_id ) && !empty( $_REQUEST['update_from_panel']) && true == $_REQUEST['update_from_panel'] ){
                $bookingpress_user_id  = get_current_user_id();
				$bookingpress_existing_staffmember_details = $wpdb->get_row( $wpdb->prepare( "SELECT `bookingpress_staffmember_id` FROM {$tbl_bookingpress_staffmembers} WHERE bookingpress_wpuser_id = %d AND bookingpress_staffmember_status = 1 ORDER BY bookingpress_staffmember_id DESC", $bookingpress_user_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared --Reason: $tbl_bookingpress_staffmembers is a table name. false alarm
				if ( ! empty( $bookingpress_existing_staffmember_details ) ) {
                    $staffmember_id = ! empty( $bookingpress_existing_staffmember_details['bookingpress_staffmember_id'] ) ? $bookingpress_existing_staffmember_details['bookingpress_staffmember_id'] : '';
				}
            }
            
            
            $location_wise_staff_break_details = !empty( $_POST['location_wise_staff_breakhours'] ) ? json_decode( stripslashes_deep( $_POST['location_wise_staff_breakhours'] ), true ) : array(); ////phpcs:ignore

            if( !empty( $_POST['location_wise_staff_workhours'] ) && !empty( $staffmember_id ) ){ // phpcs:ignore
                $staff_workhour_details = json_decode( stripslashes_deep( $_POST['location_wise_staff_workhours'] ), true ); // phpcs:ignore
                $configure_staff_specific_workhours = !empty( $_POST['bookingpress_configure_specific_workhour'] ) ? $_POST['bookingpress_configure_specific_workhour'] : 'false'; // phpcs:ignore
                if( !empty( $staff_workhour_details ) && 'true' == $configure_staff_specific_workhours ){
                    $bookingpress_workhour_days = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
                    foreach( $staff_workhour_details as $location_id => $staff_workhour_data ){
                        $wpdb->delete( $tbl_bookingpress_locations_staff_workhours, array( 'bookingpress_location_id' => $location_id, 'bookingpress_staffmember_id' => $staffmember_id ) );
                        foreach ( $bookingpress_workhour_days as $workhour_key => $workhour_val ) {
                            
                            $workhour_start_time = ! empty( $staff_workhour_details[$location_id][ $workhour_val ]['start_time'] ) ? $staff_workhour_details[$location_id][ $workhour_val ]['start_time'] : '09:00:00';
                            $workhour_end_time   = ! empty( $staff_workhour_details[$location_id][ $workhour_val ]['end_time'] ) ? $staff_workhour_details[$location_id][ $workhour_val ]['end_time'] : '17:00:00';

                            if ( $workhour_start_time == 'Off' ) {
                                $workhour_start_time = null;
                            }
                            if ( $workhour_end_time == 'Off' ) {
                                $workhour_end_time = null;
                            }

                            $bookingpress_db_fields = array(
                                'bookingpress_staffmember_id' => $staffmember_id,
                                'bookingpress_location_id' => $location_id,
                                'bookingpress_location_staff_workday_key' => $workhour_val,
                                'bookingpress_location_staff_workhour_start_time' => $workhour_start_time,
                                'bookingpress_location_staff_workhour_end_time' => $workhour_end_time,
                            );

                            $wpdb->insert( $tbl_bookingpress_locations_staff_workhours, $bookingpress_db_fields );

                            if( !empty( $location_wise_staff_break_details[ $location_id ][ $workhour_val ] ) ){
                                $break_details = $location_wise_staff_break_details[ $location_id ][ $workhour_val ];
                                foreach( $break_details as $staff_break_details ){
                                    $break_start_time = !empty( $staff_break_details['start_time'] ) ? $staff_break_details['start_time'] : '';
                                    $break_end_time = !empty( $staff_break_details['end_time'] ) ? $staff_break_details['end_time'] : '';

                                    $bookingpress_break_db_fields = array(
                                        'bookingpress_staffmember_id' => $staffmember_id,
                                        'bookingpress_location_id' => $location_id,
                                        'bookingpress_location_staff_workday_key' => $workhour_val ,
                                        'bookingpress_location_staff_workhour_start_time' => $break_start_time,
                                        'bookingpress_location_staff_workhour_end_time' => $break_end_time,
                                        'bookingpress_location_staff_workhour_is_break' => 1
                                    );

                                    $wpdb->insert( $tbl_bookingpress_locations_staff_workhours, $bookingpress_break_db_fields );
                                }
                            }
                        }
                    }
                }
            }
            /** Location wise staff's working hours end */

            /** Location wise staff's special days start */
            $bpa_location_wise_staff_special_days = !empty( $_POST['location_wise_staff_special_days'] ) ? json_decode( stripslashes_deep( $_POST['location_wise_staff_special_days'] ), true ) : array(); //phpcs:ignore
            if( !empty( $bpa_location_wise_staff_special_days ) ){
                foreach( $bpa_location_wise_staff_special_days as $location_id => $special_days_data ){
                    if( !empty( $staffmember_id ) && !empty( $location_id ) ){
                        $wpdb->delete( $tbl_bookingpress_locations_staff_special_days, array( 'bookingpress_location_id' => $location_id, 'bookingpress_staffmember_id' => $staffmember_id ) );
                        if( !empty( $special_days_data ) ){
                            foreach( $special_days_data as $staff_special_day_details ){

                                $bpa_location_staff_db_fields = array(
                                    'bookingpress_location_id' => $location_id,
                                    'bookingpress_staffmember_id' => $staffmember_id,
                                    'bookingpress_location_staff_special_day_start_date' => $staff_special_day_details['special_day_start_date'],
                                    'bookingpress_location_staff_special_day_end_date' => $staff_special_day_details['special_day_end_date'],
                                    'bookingpress_location_staff_special_day_start_time' => $staff_special_day_details['start_time'],
                                    'bookingpress_location_staff_special_day_end_time' => $staff_special_day_details['end_time'],
                                );

                                if( !empty( $staff_special_day_details['special_day_service'] ) ){
                                    $bpa_location_staff_db_fields['bookingpress_location_staff_special_day_service_id'] = implode( ',', $staff_special_day_details['special_day_service'] );
                                }

                                $wpdb->insert(
                                    $tbl_bookingpress_locations_staff_special_days,
                                    $bpa_location_staff_db_fields
                                );

                                if( !empty( $staff_special_day_details['special_day_workhour'] ) ){
                                    $staff_special_day_breaks = $staff_special_day_details['special_day_workhour'];
                                    foreach( $staff_special_day_breaks  as $staff_sd_break ){
                                        $bpa_location_staff_break_fields = array(
                                            'bookingpress_location_id' => $location_id,
                                            'bookingpress_staffmember_id' => $staffmember_id,
                                            'bookingpress_location_staff_special_day_start_date' => $staff_special_day_details['special_day_start_date'],
                                            'bookingpress_location_staff_special_day_end_date' => $staff_special_day_details['special_day_end_date'],
                                            'bookingpress_location_staff_special_day_break_start_time' => $staff_sd_break['start_time'],
                                            'bookingpress_location_staff_special_day_break_end_time' => $staff_sd_break['end_time'],
                                            'bookingpress_location_staff_special_day_has_break' => 1,
                                        );

                                        if( !empty( $bpa_location_staff_db_fields['bookingpress_location_staff_special_day_service_id'] ) ){
                                            $bpa_location_staff_break_fields['bookingpress_location_staff_special_day_service_id'] = $bpa_location_staff_db_fields['bookingpress_location_staff_special_day_service_id'];
                                        }

                                        $wpdb->insert(
                                            $tbl_bookingpress_locations_staff_special_days,
                                            $bpa_location_staff_break_fields
                                        );
                                    }
                                }

                            }
                        }
                    }
                }
            }
            /** Location wise staff's special days end */

            return $response;
        }

        function bookingpress_staff_member_dynamic_external_vue_methods_for_locations(){
            ?>
            bookingpress_edit_assigned_service( edit_assigned_service_id, currentElement ){
                const vm = this
                vm.bookingpress_reset_assign_service_modal();
                
                var dialog_pos = currentElement.target.getBoundingClientRect();
                vm.assign_service_modal_pos = (dialog_pos.top - 110)+'px'
                vm.assign_service_modal_pos_right = '-'+(dialog_pos.right - 515)+'px';
                vm.open_assign_service_modal = true
                edit_assigned_service_id = ''+edit_assigned_service_id;
                let location_details = [];
                vm.assign_service_form.assigned_service_list.forEach(function(currentValue, index, arr){
                    if(edit_assigned_service_id == currentValue.assign_service_id){
                        vm.assign_service_form.assign_service_id = edit_assigned_service_id
                        vm.assign_service_form.assign_service_name = currentValue.assign_service_name
                        vm.assign_service_form.assign_service_price = currentValue.assign_service_price
                        vm.assign_service_form.assign_service_capacity = currentValue.assign_service_capacity
                        vm.assign_service_form.assign_service_min_capacity = currentValue.assign_service_min_capacity
                        vm.assign_service_form.bookingpress_custom_durations_data = currentValue.bookingpress_custom_durations_data
                        vm.assign_service_form.is_service_edit = '1'
                        if(vm.assign_service_form.bookingpress_custom_durations_data !== 'undefined' && vm.assign_service_form.bookingpress_custom_durations_data != '' && vm.assign_service_form.bookingpress_custom_durations_data != null) {								
                            vm.is_display_default_price_field = false;
                        } else {
                            vm.is_display_default_price_field = true;
                        }
                        
                        for( let lc in currentValue.locations ){
                            location_details.push( currentValue.locations[ lc ].location_id );
                        }
                    
                    }
                });
                
                if( "undefined" != typeof location_details && location_details.length > 0 ){
                    if( 'true' == vm.staff_to_multiple_locations ){
                        vm.assign_service_form.selected_location = location_details;
                    } else {
                        vm.assign_service_form.selected_location = location_details[0];
                    }
                } else {
                    if( 'true' == vm.staff_to_multiple_locations ){
                        vm.assign_service_form.selected_location = [];
                    } else {
                        vm.assign_service_form.selected_location = "";
                    }
                }

                if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
                    vm.bpa_adjust_popup_position( currentElement, '.el-dialog.bpa-dialog--add-assign-service');
                }
            },
            show_edit_staff_special_day_div_with_location( special_day_index, currentElement){
                const vm = this;

                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                let selected_sp_day = vm.location_wise_staffmember_special_day_arr[ selected_location ][ special_day_index ];


                vm.staffmember_special_day_form.special_day_date = [selected_sp_day.special_day_start_date,selected_sp_day.special_day_end_date];
                vm.staffmember_special_day_form.start_time = selected_sp_day.start_time;
                vm.staffmember_special_day_form.end_time = selected_sp_day.end_time;
                vm.staffmember_special_day_form.special_day_workhour = typeof(selected_sp_day.special_day_workhour) !== 'undefined' ? selected_sp_day.special_day_workhour : [];
                vm.staffmember_special_day_form.special_day_service = selected_sp_day.special_day_service;
                
                vm.location_wise_staff_special_day_edit_index = special_day_index;
                vm.special_days_add_modal = true;

                var dialog_pos = currentElement.target.getBoundingClientRect();
                vm.special_days_modal_pos = (dialog_pos.top - 90)+'px'
                vm.special_days_modal_pos_right = '-'+(dialog_pos.right - 400)+'px';
                
                if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
                    vm.bpa_adjust_popup_position( currentElement, 'div#special_days_add_modal .el-dialog.bpa-dialog--special-days');
                }
                
            },
            delete_staff_special_day_div_with_location( special_day_index ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;

                let selected_sp_day = vm.location_wise_staffmember_special_day_arr[ selected_location ][ special_day_index ];
                if( "undefined" != typeof selected_sp_day ){
                    vm.location_wise_staffmember_special_day_arr[ selected_location ].splice( special_day_index, 1 );
                }
            },
            bpa_move_staff_location_nav_prev(){
                let element = document.getElementById( "bpa_staff_shift_management_locations" );

                let scrollLeft = element.scrollLeft;

                element.scrollTo({
                    left: ( scrollLeft - 150 ),
                    behavior: "smooth",
                });
                
            },
            bpa_move_staff_location_nav_next(){
                let element = document.getElementById( "bpa_staff_shift_management_locations" );

                let scrollLeft = element.scrollLeft;

                element.scrollTo({
                    left: ( scrollLeft + 150 ),
                    behavior: "smooth",
                });
            },
            edit_staffmember_special_day_with_locations( from_staff_panel = false ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                let special_day_index = vm.location_wise_staff_special_day_edit_index;
                
                let special_day_date = vm.staffmember_special_day_form.special_day_date;
                
				let special_day_start_time = vm.staffmember_special_day_form.start_time;
				let special_day_end_time = vm.staffmember_special_day_form.end_time;
				let special_day_workhour = vm.staffmember_special_day_form.special_day_workhour;
                let item = vm.location_wise_staffmember_special_day_arr[ selected_location ][ special_day_index ];
                
                item.special_day_start_date = special_day_date[0];
                item.special_day_end_date = special_day_date[1];
                item.start_time = special_day_start_time;
                item.end_time = special_day_end_time;
                item.special_day_workhour = special_day_workhour;

                if( true == from_staff_panel ){
                    vm.close_special_days_func();
                } else {   
                    vm.closeStaffmemberSpecialday();
                }
                vm.bookingpress_staff_format_special_day_time_with_location( from_staff_panel );
                vm.location_wise_staff_special_day_edit_index = -1;

            },
            add_staffmember_special_day_with_locations( from_staff_panel = false ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                let ilength = parseInt(vm.location_wise_staffmember_special_day_arr[selected_location].length) + 1;
                let empSpecialDayData = {};					
				Object.assign(empSpecialDayData, {id: ilength})
				Object.assign(empSpecialDayData, {special_day_start_date: vm.staffmember_special_day_form.special_day_date[0]})
				Object.assign(empSpecialDayData, {special_day_end_date: vm.staffmember_special_day_form.special_day_date[1]})
				Object.assign(empSpecialDayData, {start_time: vm.staffmember_special_day_form.start_time})
				Object.assign(empSpecialDayData, {end_time: vm.staffmember_special_day_form.end_time})
                Object.assign(empSpecialDayData, {special_day_service: vm.staffmember_special_day_form.special_day_service})
				Object.assign(empSpecialDayData, {special_day_workhour: vm.staffmember_special_day_form.special_day_workhour})					
				vm.location_wise_staffmember_special_day_arr[selected_location].push(empSpecialDayData)
				vm.bookingpress_staff_format_special_day_time_with_location( from_staff_panel );
                if( true == from_staff_panel){
                    vm.close_special_days_func();
                } else {
                    vm.closeStaffmemberSpecialday();
                }
            },
            bookingpress_staff_format_special_day_time_with_location( from_staff_panel ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                
                var postdata = [];
                postdata.action = 'bookingpress_format_staffmember_special_days_data_with_location'
                postdata.special_days_data= vm.location_wise_staffmember_special_day_arr[selected_location];
                postdata.selected_location = selected_location;
                postdata.update_from_panel = from_staff_panel;
                postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
                axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
                .then(function(response){
                    if(response.data.variant == "success"){
                        vm.location_wise_staffmember_special_day_arr[selected_location] = response.data.daysoff_details
                    }
                    if( true == from_staff_panel){
                        vm.close_special_days_func();
                    } else {
                        vm.closeStaffmemberSpecialday();
                    }
                }).catch(function(error){
                    console.log(error);
                });
            },
            <?php
        }

        function bookingpress_add_service_dynamic_external_vue_methods_for_locations(){
            ?>
            edit_workhour_data_with_location( currentElement,break_start_time, break_end_time, day_name,index ){
                console.log( arguments );
            },
            bpa_move_service_location_nav_prev(){
                let element = document.getElementById( "bpa_service_shift_management_locations" );

                let scrollLeft = element.scrollLeft;

                element.scrollTo({
                    left: ( scrollLeft - 150 ),
                    behavior: "smooth",
                });
                
            },
            bpa_move_service_location_nav_next(){
                let element = document.getElementById( "bpa_service_shift_management_locations" );

                let scrollLeft = element.scrollLeft;

                element.scrollTo({
                    left: ( scrollLeft + 150 ),
                    behavior: "smooth",
                });
            },
            delete_special_day_div_with_location( special_day_index ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;

                let selected_sp_day = vm.location_wise_special_day_data_arr[ selected_location ][ special_day_index ];
                if( "undefined" != typeof selected_sp_day ){
                    vm.location_wise_special_day_data_arr[ selected_location ].splice( special_day_index, 1 );
                }
            },
            show_edit_special_day_div_with_location( special_day_index, currentElement ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;

                let selected_sp_day = vm.location_wise_special_day_data_arr[ selected_location ][ special_day_index ];

                vm.special_day_form.special_day_date = [selected_sp_day.special_day_start_date,selected_sp_day.special_day_end_date];
                vm.special_day_form.start_time = selected_sp_day.start_time;
                vm.special_day_form.end_time = selected_sp_day.end_time;
                vm.special_day_form.special_day_workhour = typeof(selected_sp_day.special_day_workhour) !== 'undefined' ? selected_sp_day.special_day_workhour : [];

                vm.location_wise_special_day_edit_index = special_day_index;
                vm.special_days_add_modal = true;

                var dialog_pos = currentElement.target.getBoundingClientRect();
				vm.special_days_modal_pos = (dialog_pos.top - 90)+'px'
				vm.special_days_modal_pos_right = '-'+(dialog_pos.right - 400)+'px';
				
				if( typeof vm.bpa_adjust_popup_position != 'undefined' ){
					vm.bpa_adjust_popup_position( currentElement, 'div#special_days_add_modal .el-dialog.bpa-dialog--special-days');
				}

            },
            edit_special_days_with_location(){
                const vm = this

                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
				let special_day_index = vm.location_wise_special_day_edit_index;
				let special_day_date = vm.special_day_form.special_day_date;
				let special_day_start_time = vm.special_day_form.start_time;
				let special_day_end_time = vm.special_day_form.end_time;																	
				let special_day_workhour = vm.special_day_form.special_day_workhour;

                let item = vm.location_wise_special_day_data_arr[ selected_location ][ special_day_index ];
                
                item.special_day_start_date = special_day_date[0];
                item.special_day_end_date = special_day_date[1];
                item.start_time = special_day_start_time;
                item.end_time = special_day_end_time;
                item.special_day_workhour = special_day_workhour;

				vm.closeSpecialday();
				vm.bookingpress_service_format_special_day_time_with_location();
                vm.location_wise_special_day_edit_index = -1;
            },
            add_special_days_with_location(){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
                let ilength = parseInt(vm.location_wise_special_day_data_arr[selected_location].length) + 1;
				let empSpecialDayData = {};					
				Object.assign(empSpecialDayData, {id: ilength})
				Object.assign(empSpecialDayData, {special_day_start_date: vm.special_day_form.special_day_date[0]})
				Object.assign(empSpecialDayData, {special_day_end_date: vm.special_day_form.special_day_date[1]})
				Object.assign(empSpecialDayData, {start_time: vm.special_day_form.start_time})
				Object.assign(empSpecialDayData, {end_time: vm.special_day_form.end_time})
				Object.assign(empSpecialDayData, {special_day_workhour: vm.special_day_form.special_day_workhour})					
				vm.location_wise_special_day_data_arr[selected_location].push(empSpecialDayData)
				vm.bookingpress_service_format_special_day_time_with_location();
				vm.closeSpecialday();
            },
            bookingpress_service_format_special_day_time_with_location(){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;
				var postdata = [];
				postdata.action = 'bookingpress_format_service_special_days_data_with_location';
				postdata.special_days_data= vm.location_wise_special_day_data_arr[selected_location];
                postdata.selected_location = selected_location;
				postdata._wpnonce = '<?php echo esc_html( wp_create_nonce( 'bpa_wp_nonce' ) ); ?>';
				axios.post( appoint_ajax_obj.ajax_url, Qs.stringify( postdata ) )
				.then(function(response){
					if(response.data.variant == "success"){
						vm.location_wise_special_day_data_arr[selected_location] = response.data.daysoff_details
					}
				}).catch(function(error){
					console.log(error);
				});
            },

            delete_service_breakhour_with_location( start_time, end_time, selected_day ){
                const vm = this;
                let selected_location = vm.bookingpress_shift_mgmt_selected_location;

                vm.service.location_wise_break_timings[selected_location][selected_day].forEach(function(currentValue, index, arr){
                    if(currentValue.start_time == start_time && currentValue.end_time == end_time){
						vm.service.location_wise_break_timings[selected_location][selected_day].splice(index, 1);
					}
                });
            },
            <?php
        }

        function bookingpress_display_location_wise_service_special_days(){
            ?>
            <div class="bpa-default-card bpa-grid-list-container bpa-dc__staff--assigned-service bpa-sm__special-days-card bpa_location_special_days" v-if="display_service_workhours == false">
                <div class="bpa-sm__sd-body-row--parent" v-for="(location_details,index) in bookingpress_locations" v-if="location_details.bookingpress_location_id == bookingpress_shift_mgmt_selected_location">
                    <el-row class="bpa-dc--sec-sub-head" v-if="location_wise_special_day_data_arr[bookingpress_shift_mgmt_selected_location].length != 0">
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				            <h2 class="bpa-sec--sub-heading"><?php esc_html_e( 'All Special Days', 'bookingpress-location' ); ?></h2>
			            </el-col>
                    </el-row>
                    <div class="bpa-as__body bpa-sm__doc-body">
                        <el-row type="flex" class="bpa-as__empty-view" v-if="location_wise_special_day_data_arr[bookingpress_shift_mgmt_selected_location].length == 0">
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <div class="bpa-data-empty-view">
                                    <div class="bpa-ev-left-vector">
                                        <picture>
                                            <source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
                                            <img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
                                        </picture>
                                    </div>				
                                    <div class="bpa-ev-right-content">					
                                        <h4><?php esc_html_e( 'No Special Days Available', 'bookingpress-location' ); ?></h4>
                                    </div>				
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="bpa-assigned-service-body" v-if="location_wise_special_day_data_arr[bookingpress_shift_mgmt_selected_location].length > 0">
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                <div class="bpa-card bpa-card__heading-row">
                                    <el-row type="flex">
                                        <el-col :xs="8" :sm="8" :md="8" :lg="8" :xl="8">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Date', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Workhours', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Breaks', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                        <el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading"><?php esc_html_e( 'Action', 'bookingpress-location' ); ?></h4>
                                            </div>
                                        </el-col>
                                    </el-row>	
                                </div>
                            </el-col>
                            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="(special_day_data, sp_sd_index) in location_wise_special_day_data_arr[bookingpress_shift_mgmt_selected_location]">
                                <div class="bpa-card bpa-card__body-row list-group-item">
                                    <el-row type="flex">
                                        <el-col :xs="8" :sm="8" :md="8" :lg="8" :xl="8">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading is--body-heading">{{ special_day_data.special_day_formatted_start_date }} - {{ special_day_data.special_day_formatted_end_date }}</h4>
                                            </div>
                                        </el-col>								
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item">
                                                <h4 class="bpa-card__item__heading is--body-heading">( {{special_day_data.formatted_start_time}} - {{special_day_data.formatted_end_time}} )</h4>
                                            </div>
                                        </el-col>	
                                        <el-col :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
                                            <div class="bpa-card__item"> 
                                                <span v-if="special_day_data.special_day_workhour != undefined && special_day_data.special_day_workhour != ''">	
                                                    <h4 class="bpa-card__item__heading is--body-heading" v-for="special_day_workhours in special_day_data.special_day_workhour" v-if="special_day_workhours.formatted_start_time != undefined && special_day_workhours.formatted_start_time != '' && special_day_workhours.formatted_end_time != undefined && special_day_workhours.formatted_end_time != '' && special_day_workhours.start_time != '' && special_day_workhours.end_time != ''">
                                                    ( {{ special_day_workhours.formatted_start_time }} - {{special_day_workhours.formatted_end_time}} )
                                                    </h4>
                                                </span>	
                                                <span v-else>-</span>	
                                            </div>
                                        </el-col>
                                        <el-col :xs="4" :sm="4" :md="4" :lg="4" :xl="4">
                                            <div>
                                                <el-tooltip effect="dark" content="" placement="top" open-delay="300">
                                                    <div slot="content">
                                                        <span><?php esc_html_e( 'Edit', 'bookingpress-location' ); ?></span>
                                                    </div>
                                                    <el-button class="bpa-btn bpa-btn--icon-without-box" @click="show_edit_special_day_div_with_location(sp_sd_index,event)">
                                                      <span class="material-icons-round">mode_edit</span>
                                                    </el-button>
                                                </el-tooltip>
                                                <el-tooltip effect="dark" content="" placement="top" open-delay="300">
                                                    <div slot="content">
                                                        <span><?php esc_html_e( 'Delete', 'bookingpress-location' ); ?></span>
                                                    </div>
                                                    <el-button class="bpa-btn bpa-btn--icon-without-box __danger" @click="delete_special_day_div_with_location(sp_sd_index)">
                                                        <span class="material-icons-round">delete</span>
                                                    </el-button>
                                                </el-tooltip>
                                            </div>
                                        </el-col>
                                    </el-row>	
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                </div>
            </div>
            <?php
        }

        function bookingpress_display_location_wise_service_workhours(){
            ?>
                <div class="bpa-sm__wh-items" v-if="service.bookingpress_configure_specific_service_workhour == true && display_service_workhours == false">
                    <div class="bpa-sm__wh-body-row--parent" v-for="(location_details, index) in bookingpress_locations" v-if="location_details.bookingpress_location_id == bookingpress_shift_mgmt_selected_location">
                        <div class="bpa-sm__wh-body-row" v-for="work_hours_day in work_hours_days_arr">
                            <el-row class="bpa-sm__wh-item-row" :gutter="24" :id="'weekday_'+work_hours_day.day_name">
                                <el-col :xs="22" :sm="22" :md="20" :lg="20" :xl="22">
                                    <el-row type="flex" class="bpa-sm__wh-body-left">
                                        <el-col :xs="24" :sm="24" :md="4" :lg="4" :xl="2">
                                            <span class="bpa-form-label" v-if="work_hours_day.day_name == 'Monday'"><?php esc_html_e('Monday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Tuesday'"><?php esc_html_e('Tuesday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Wednesday'"><?php esc_html_e('Wednesday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Thursday'"><?php esc_html_e('Thursday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Friday'"><?php esc_html_e('Friday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Saturday'"><?php esc_html_e('Saturday', 'bookingpress-location'); ?></span>
                                            <span class="bpa-form-label" v-else-if="work_hours_day.day_name == 'Sunday'"><?php esc_html_e('Sunday', 'bookingpress-location'); ?></span>
                                            <span v-else>{{ work_hours_day.day_name }}</span>
                                        </el-col>
                                        <el-col :xs="24" :sm="24" :md="20" :lg="20" :xl="22">
                                            <el-row :gutter="24">
                                                <el-col :xs="8" :sm="8" :md="12" :lg="12" :xl="12">
                                                    <el-select v-model="service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].start_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'Start Time', 'bookingpress-location' ); ?>" filterable> <!-- @change="bookingpress_set_workhour_value($event,work_hours_day.day_name)" -->
                                                        <span slot="prefix" class="material-icons-round">access_time</span>
                                                        <el-option v-for="work_timings in work_hours_day.worktimes" :label="work_timings.formatted_start_time" :value="work_timings.start_time" v-if="work_timings.start_time != service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].end_time || service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].end_time == 'Off'"></el-option>
                                                    </el-select>
                                                </el-col>
                                                <el-col :xs="8" :sm="8" :md="12" :lg="12" :xl="12" v-if="service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].start_time != 'Off'">
                                                    <el-select v-model="service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].end_time" class="bpa-form-control bpa-form-control__left-icon" placeholder="<?php esc_html_e( 'End Time', 'bookingpress-location' ); ?>" filterable> <!-- @change="bookingpress_check_workhour_value($event,work_hours_day.day_name)" -->
                                                            <span slot="prefix" class="material-icons-round">access_time</span>
                                                            <el-option v-for="work_timings in work_hours_day.worktimes" :label="work_timings.formatted_end_time" :value="work_timings.end_time" v-if="(  work_timings.end_time > service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].start_time ||  work_timings.end_time == '24:00:00')"></el-option>
                                                    </el-select>
                                                </el-col>
                                            </el-row>
                                            <el-row v-if="service.location_wise_break_timings[location_details.bookingpress_location_id][work_hours_day.day_name].length > 0 && service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].start_time != 'Off'">
                                                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                                                    <div class="bpa-break-hours-wrapper">
                                                        <h4><?php esc_html_e( 'Breaks', 'bookingpress-location' ); ?></h4>
                                                        <div class="bpa-bh--items">
                                                            <div class="bpa-bh__item" v-for="(break_data,index) in service.location_wise_break_timings[location_details.bookingpress_location_id][work_hours_day.day_name]">
                                                                <p>{{ break_data.formatted_start_time }} to {{ break_data.formatted_end_time }}</p><!-- @click="edit_workhour_data(event,break_data.start_time, break_data.end_time, work_hours_day.day_name,index)" -->
                                                                <span class="material-icons-round" slot="reference" @click="delete_service_breakhour_with_location(break_data.start_time, break_data.end_time, work_hours_day.day_name)">close</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </el-col>
                                            </el-row>
                                    </el-row>
                                </el-col>
                                <el-col :xs="24" :sm="24" :md="4" :lg="4" :xl="2" v-if="service.location_wise_workhours[location_details.bookingpress_location_id][work_hours_day.day_name].start_time != 'Off'">
                                    <el-button class="bpa-btn bpa-btn__medium bpa-btn--full-width" :class="(break_selected_day == work_hours_day.day_name && open_add_break_modal == true) ? 'bpa-btn--primary' : ''" @click="open_add_break_modal_func(event, work_hours_day.day_name)">
                                        <?php esc_html_e( 'Add Break', 'bookingpress-location' ); ?>
                                    </el-button>
                                </el-col>
                            </el-row>
                        </div>
                    </div>
                </div>
            <?php
        }

        function bookingpress_add_service_shift_management_content_func(){
            ?>
                <div class="bpa-mle-dc__item">
                    <h3 class="bpa-page-heading bpa-mle-dc--heading"><?php esc_html_e( 'Select Locations', 'bookingpress-location' ); ?></h3>
                    <div class="bpa-cmc--tab-menu">
                        <div class="bpa-cms-tm__body">
                            <el-button class="bpa-btn bpa-btn--icon-without-box" @click="bpa_move_service_location_nav_prev()"><span class="material-icons-round">west</span></el-button>
                            <el-radio-group v-model="bookingpress_shift_mgmt_selected_location" id="bpa_service_shift_management_locations">
                                <el-radio-button :label="location_details.bookingpress_location_id" v-for="(location_details, key) in bookingpress_locations">{{ location_details.bookingpress_location_name }}</el-radio-button>
                            </el-radio-group>
                            <el-button class="bpa-btn bpa-btn--icon-without-box" @click="bpa_move_service_location_nav_next()"><span class="material-icons-round">east</span></el-button>
                        </div>
                    </div>
                </div>
            <?php
        }
    }

    global $bookingpress_location_workhours;
	$bookingpress_location_workhours = new bookingpress_location_workhours();
}