<el-tab-pane name="6">                                         
    <template #label>
        <a :class="formActiveTab == '6' ? 'bpa_center_container_tab_title' : ''" :style="[ formActiveTab == '6' ? { 'color': selected_colorpicker_values.primary_color,'font-family': selected_font_values.title_font_family} : {'color': selected_colorpicker_values.sub_title_color,'font-size': selected_font_values.content_font_size+'px','font-family': selected_font_values.title_font_family} ]">
            <span class="material-icons-round" :style="[ formActiveTab == '6' ? { 'background': selected_colorpicker_values.primary_color, 'border-color': selected_colorpicker_values.primary_color  } : {'color': selected_colorpicker_values.content_color,'font-size': selected_font_values.color_font_size+'px'} ]">place</span>
            {{ tab_container_data.location_title }}
        </a>
    </template>
    
    <div class="bpa-cbf--preview-step" :style="{ 'background': selected_colorpicker_values.background_color }">
        <div class="bpa-cbf--preview-step__body-content">					
            <div class="bpa-cbf--preview--module-container">
                <el-row>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <div class="bpa-front-module-heading" v-text="tab_container_data.location_title" :style="{ 'color': selected_colorpicker_values.label_title_color, 'font-size': selected_font_values.title_font_size+'px', 'font-family': selected_font_values.title_font_family}"></div>
                        <div class="bpa-front-module--location-items-row">
                            <div class="bpa-fm-li--col-item">
                                <div class="bpa-fm-li__card" :style="{ 'border-color': selected_colorpicker_values.primary_color }">
                                    <div class="bpa-li-col__image">
                                        <img src="<?php echo esc_url(BOOKINGPRESS_LOCATION_URL . '/images/location-placeholder.jpg'); ?>" alt="">
                                    </div>
                                    <div class="bpa-li-col__body">
                                        <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}"><?php esc_html_e( 'United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '31 4th Drive, Westfield,wi, 53964  United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '+1-202-555-0101', 'bookingpress-location' ); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bpa-fm-li--col-item">
                                <div class="bpa-fm-li__card">
                                    <div class="bpa-li-col__image">
                                        <img src="<?php echo esc_url(BOOKINGPRESS_LOCATION_URL . '/images/location-placeholder.jpg'); ?>" alt="">
                                    </div>
                                    <div class="bpa-li-col__body">
                                        <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}"><?php esc_html_e( 'United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '31 4th Drive, Westfield,wi, 53964  United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '+1-202-555-0101', 'bookingpress-location' ); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bpa-fm-li--col-item">
                                <div class="bpa-fm-li__card">
                                    <div class="bpa-li-col__image">
                                        <img src="<?php echo esc_url(BOOKINGPRESS_LOCATION_URL . '/images/location-placeholder.jpg'); ?>" alt="">
                                    </div>
                                    <div class="bpa-li-col__body">
                                        <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}"><?php esc_html_e( 'United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '31 4th Drive, Westfield,wi, 53964  United States', 'bookingpress-location' ); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="bpa-fm-li--col-item">
                                <div class="bpa-fm-li__card">
                                    <div class="bpa-li-col__image">
                                        <img src="<?php echo esc_url(BOOKINGPRESS_LOCATION_URL . '/images/location-placeholder.jpg'); ?>" alt="">
                                    </div>
                                    <div class="bpa-li-col__body">
                                        <div class="bpa-li-col__title" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-family': selected_font_values.title_font_family,'font-size': selected_font_values.title_font_size+'px'}"><?php esc_html_e( 'United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '31 4th Drive, Westfield,wi, 53964  United States', 'bookingpress-location' ); ?></div>
                                        <div class="bpa-li-col__address bpa-li-col__phone-no" :style="{ 'color': selected_colorpicker_values.sub_title_color, 'font-size': selected_font_values.content_font_size+'px', 'font-family': selected_font_values.title_font_family}"><?php esc_html_e( '+1-202-555-0101', 'bookingpress-location' ); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </div>					
        </div>
    </div>    
</el-tab-pane>