<div class="bpa-form-row">
    <el-row>
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <div class="bpa-db-sec-heading">
                <el-row type="flex" align="middle">
                    <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="bpa-db-sec-left">
                            <h2 class="bpa-page-heading"><?php esc_html_e( 'Location', 'bookingpress-location' ); ?></h2>
                        </div>
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                        <div class="bpa-hw-right-btn-group">
                            <el-button class="bpa-btn bpa-btn__filled-light" @click="open_location_modal(event)">
                                <span class="material-icons-round">add</span>
                                <?php esc_html_e( 'Add New', 'bookingpress-location' ); ?>
                            </el-button>
                        </div>
                    </el-col>
                </el-row>
            </div>    
            <div class="bpa-default-card bpa-db-card bpa-grid-list-container bpa-dc__staff--assigned-service bpa__is-Location-specific-price-deactivated">                        
                <el-row class="bpa-dc--sec-sub-head">
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <h2 class="bpa-sec--sub-heading"><?php esc_html_e( 'All Locations', 'bookingpress-location' ); ?></h2>
                    </el-col>
                </el-row>
                <div class="bpa-as__body">
                    <el-row type="flex" class="bpa-as__empty-view" v-if="save_location_data.length == 0">
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <div class="bpa-data-empty-view">
                                <div class="bpa-ev-left-vector">
                                    <picture>
                                        <source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
                                        <img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
                                    </picture>
                                </div>				
                                <div class="bpa-ev-right-content">					
                                    <h4><?php esc_html_e( 'No Locations Assigned', 'bookingpress-location' ); ?></h4>
                                </div>				
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                            <div class="bpa-lspd__body">
                                <el-row :gutter="32">
                                    <el-col :xs="12" :sm="12" :md="12" :lg="08" :xl="08" v-for="(saved_location_details, key) in save_location_data">
                                        <div class="bpa-lspd__item">
                                            <div class="bpa-lspd__item-val">
                                                <h4>{{ saved_location_details.location_name }}</h4>
                                                <span> <?php echo esc_html__('Min Capacity:', 'bookingpress-location'); ?> <strong>{{saved_location_details.location_min_capacity}}</strong></span>
                                                </br/>
                                                <span v-if="is_bring_anyone_with_you_enable == 1"> <?php echo esc_html__(' Max Capacity:','bookingpress-location'); ?> <strong>{{saved_location_details.location_max_capacity}}</strong></span>
                                            </div>
                                            <div class="bpa-card__item">
                                                <el-button class="bpa-btn bpa-btn--icon-without-box" @click="bookingpress_edit_location(key)">
                                                    <span class="material-icons-round">mode_edit</span>
                                                </el-button>
                                                <el-button type="text" slot="reference" class="bpa-btn bpa-btn--icon-without-box __danger" @click="bookingpress_delete_location(key)">
                                                    <span class="material-icons-round">delete</span>
                                                </el-button>
                                            </div>
                                        </div>
                                    </el-col>
                                </el-row>
                            </div>
                        </el-col>
                    </el-row>
                </div>						
            </div>
        </el-col>
    </el-row>
</div>