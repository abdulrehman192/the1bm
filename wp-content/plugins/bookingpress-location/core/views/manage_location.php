<?php
	global $bookingpress_ajaxurl, $bookingpress_common_date_format, $bookingpress_global_options;
	$bookingpress_global_options_arr = $bookingpress_global_options->bookingpress_global_options();
	$bookingpress_singular_staffmember_name = !empty($bookingpress_global_options_arr['bookingpress_staffmember_singular_name']) ? $bookingpress_global_options_arr['bookingpress_staffmember_singular_name'] : esc_html_e('Staff Member', 'bookingpress-location');
	$bookingpress_plural_staffmember_name = !empty($bookingpress_global_options_arr['bookingpress_staffmember_plural_name']) ? $bookingpress_global_options_arr['bookingpress_staffmember_plural_name'] : esc_html_e('Staff Members', 'bookingpress-location');
?>
<el-main class="bpa-main-listing-card-container bpa-mlc__location-container bpa-default-card bpa--is-page-scrollable-tablet" id="all-page-main-container">
	<el-row type="flex" class="bpa-mlc-head-wrap __services-screen">
		<el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" class="bpa-mlc-left-heading">
			<h1 class="bpa-page-heading"><?php esc_html_e( 'Manage Locations', 'bookingpress-location' ); ?></h1>
		</el-col>
		<el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12">
			<div class="bpa-hw-right-btn-group">
				<el-button class="bpa-btn bpa-btn--primary" @click="open_add_location_modal('add')">
					<span class="material-icons-round">add</span>
					<?php esc_html_e( 'Add New', 'bookingpress-location' ); ?>
				</el-button>
			</div>
		</el-col>
	</el-row>
	<div class="bpa-back-loader-container" id="bpa-page-loading-loader">
		<div class="bpa-back-loader"></div>
	</div>
	<div id="bpa-main-container">
		<el-row type="flex" v-if="items.length == 0">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<div class="bpa-data-empty-view">
					<div class="bpa-ev-left-vector">
						<picture>
							<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
							<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
						</picture>
					</div>
					<div class="bpa-ev-right-content">
						<h4><?php esc_html_e( 'No Record Found!', 'bookingpress-location' ); ?></h4>
						
						<el-button class="bpa-btn bpa-btn--primary bpa-btn__medium" @click="open_add_location_modal('add')"> 
							<span class="material-icons-round">add</span>
							<?php esc_html_e( 'Add New', 'bookingpress-location' ); ?>
						</el-button>
					</div>
				</div>
			</el-col>
		</el-row>
		<el-container class="bpa-grid-list-container bpa-grid-list--service-page"> <!-- reputelog - need to change -->
			<div class="bpa-back-loader-container" v-if="is_display_loader == '1'">
				<div class="bpa-back-loader"></div>
			</div>
			<el-row v-if="items.length > 0">
				<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
					<div class="bpa-card bpa-card__heading-row">
						<el-row type="flex">
							<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
								<div class="bpa-card__item bpa-card__item--ischecbox">
									<el-checkbox v-model="is_multiple_checked" @change="selectAllLocations($event)"></el-checkbox><?php /* @change="selectAllServices($event)"*/ ?>
									<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Name', 'bookingpress-location' ); ?></h4>
								</div>
							</el-col>
							<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="8">
								<div class="bpa-card__item">
									<h4 class="bpa-card__item__heading"><?php esc_html_e('Phone Number', 'bookingpress-location'); ?></h4>
								</div>
							</el-col>
							<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="8">
								<div class="bpa-card__item">
									<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Address', 'bookingpress-location'); ?><h4>
								</div>
							</el-col>
						</el-row>
					</div>
				</el-col>
				<draggable :list="items" class="list-group" ghost-class="ghost" @start="dragging = true" @end="updateLocationPosition($event)" > <?php /* @end="updateServicePos($event)" :disabled="!enabled" */ ?>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="items_data in items" :data-location_id="items_data.bookingpress_location_id" > <!-- :data-service_id="items_data.service_id -->
						<div class="bpa-card bpa-card__body-row list-group-item">
							<div class="bpa-card__item--drag-icon-wrap">
								<span class="material-icons-round">drag_indicator</span>
							</div>
							<el-row type="flex">
								<el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
									<div class="bpa-card__item bpa-card__item--ischecbox">

										<el-tooltip effect="dark" content="" placement="top" v-if="items_data.location_bulk_action">
                                            <div slot="content">
                                                <span><?php esc_html_e('One or more appointments are associated with this location,', 'bookingpress-location'); ?></span><br/>
                                                <span><?php esc_html_e('so you will not be able to delete it', 'bookingpress-location'); ?></span>
                                            </div>
                                            <el-checkbox v-model="items_data.selected" :disabled=items_data.location_bulk_action @change="handleSelectionChange(event, $event, items_data.service_id)"></el-checkbox>
                                        </el-tooltip>
										<el-checkbox v-model="items_data.selected" :disabled=items_data.location_bulk_action @change="handleLocationSelectionChange(event, $event, items_data.bookingpress_location_id)" v-else></el-checkbox><?php ?>
										<img :src="items_data.bookingpress_location_img_url" alt="location-thumbnail" class="bpa-card__item--service-thumbnail" v-if="items_data.bookingpress_location_img_url != ''">
										<img :src="location_default_img_url" alt="location-thumbnail" class="bpa-card__item--service-thumbnail" v-else />
										<h4 class="bpa-card__item__heading is--body-heading"> <span v-html="items_data.bookingpress_location_name"></span> <span class="bpa-card__item--id">(<?php esc_html_e( 'ID', 'bookingpress-location' ); ?>: {{ items_data.bookingpress_location_id }} )</span></h4>
									</div>
								</el-col> 
								<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="8">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading is--body-heading">{{ items_data.bookingpress_location_phone_number }}</h4>
									</div>
								</el-col>
								<el-col :xs="24" :sm="6" :md="6" :lg="6" :xl="8">
									<div class="bpa-card__item">
										<h4 class="bpa-card__item__heading is--body-heading">{{ items_data.bookingpress_location_address }}</h4>
									</div>
								</el-col> 
							</el-row>
							<div class="bpa-table-actions-wrap">
								<div class="bpa-table-actions">
									<el-tooltip effect="dark" content="" placement="top" open-delay="300">
										<div slot="content">
											<span><?php esc_html_e( 'Edit', 'bookingpress-location' ); ?></span>
										</div>
										<el-button class="bpa-btn bpa-btn--icon-without-box" @click.native.prevent="editLocation(items_data.bookingpress_location_id)">
											<span class="material-icons-round">mode_edit</span>
										</el-button>
									</el-tooltip>
									<el-tooltip effect="dark" content="" placement="top" open-delay="300">
										<div slot="content">
											<span><?php esc_html_e( 'Delete', 'bookingpress-location' ); ?></span>
										</div>
										<el-popconfirm 
											confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-location' ); ?>' 
											cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to delete this location?', 'bookingpress-location' ); ?>" 
											@confirm="deleteLocation(items_data.bookingpress_location_id)" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small">
											<el-button type="text" slot="reference" class="bpa-btn bpa-btn--icon-without-box __danger">
												<span class="material-icons-round">delete</span>
											</el-button>
										</el-popconfirm>
									</el-tooltip>
								</div>
							</div>
						</div>
					</el-col>
				</draggable>
			</el-row>
		</el-container>
		<el-row class="bpa-pagination" v-if="items.length > 0">
			<el-container v-if="multipleLocationSelection.length > 0" class="bpa-default-card bpa-bulk-actions-card">
				<el-button class="bpa-btn bpa-btn--icon-without-box bpa-bac__close-icon" @click="clearBulkAction">
					<span class="material-icons-round">close</span>
				</el-button>
				<el-row type="flex" class="bpa-bac__wrapper">
					<el-col class="bpa-bac__left-area" :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
						<span class="material-icons-round">check_circle</span>
						<p>{{ multipleLocationSelection.length }}<?php esc_html_e(' Items Selected', 'bookingpress-location'); ?></p>
					</el-col>
					<el-col class="bpa-bac__right-area" :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
						<el-select class="bpa-form-control" v-model="location_bulk_action" placeholder="<?php esc_html_e('Select', 'bookingpress-location'); ?>"
						popper-class="bpa-dropdown--bulk-actions">
							<el-option v-for="item in bulk_options" :key="item.value" :label="item.label" :value="item.value"></el-option>
						</el-select>
						<el-button @click="bulk_actions_location" class="bpa-btn bpa-btn--primary bpa-btn__medium">
							<?php esc_html_e('Go', 'bookingpress-location'); ?>
						</el-button>
					</el-col>
				</el-row>
			</el-container>
		</el-row>
		<?php /*<el-row v-if="items.length > 0">
            <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<el-container class="bpa-table-container">
					<div class="bpa-back-loader-container" v-if="is_display_loader == '1'">
						<div class="bpa-back-loader"></div>
					</div>
					<el-table ref="multipleTable" :data="items" @selection-change="handleSelectionChange" style="width: 100%">
						<el-table-column type="selection"></el-table-column>
						<el-table-column prop="bookingpress_location_name" label="<?php esc_html_e( 'Name', 'bookingpress-location' ); ?>" row-class-name="bpa-cm__code-col">
							<template slot-scope="scope">
								<template v-if="scope.row.bookingpress_location_img_url != ''">
									<el-image class="bpa-table-column-avatar" :src="scope.row.bookingpress_location_img_url"></el-image>
								</template>
								<template v-else>
									<el-image class="bpa-table-column-avatar" :src="location_default_img_url"></el-image>
								</template>
								<label>{{ scope.row.bookingpress_location_name }}</label>
							</template>
						</el-table-column>
						<el-table-column prop="bookingpress_location_phone_number" label="<?php esc_html_e( 'Phone Number', 'bookingpress-location' ); ?>">
							<template slot-scope="scope">
								<label>
									{{ scope.row.bookingpress_location_phone_number }}
								</label>
							</template>
						</el-table-column>
						<el-table-column prop="bookingpress_location_address" label="<?php esc_html_e( 'Address', 'bookingpress-location' ); ?>">
							<template slot-scope="scope">
								<label>{{ scope.row.bookingpress_location_address }}</label>
								<div class="bpa-table-actions-wrap">
									<div class="bpa-table-actions">
										<el-tooltip effect="dark" content="" placement="top" open-delay="300">
											<div slot="content">
												<span><?php esc_html_e('Edit', 'bookingpress-location'); ?></span>
											</div>
											<el-button class="bpa-btn bpa-btn--icon-without-box" @click.native.prevent="editLocation(scope.row.bookingpress_location_id)">
												<span class="material-icons-round">mode_edit</span>
											</el-button>
										</el-tooltip>
										<el-tooltip effect="dark" content="" placement="top" open-delay="300">
											<div slot="content">
												<span><?php esc_html_e('Delete', 'bookingpress-location'); ?></span>
											</div>
											<el-popconfirm 
												cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?>' 
												confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-location' ); ?>' 
												icon="false" 
												title="<?php esc_html_e( 'Are you sure you want to delete this location?', 'bookingpress-location' ); ?>" 
												@confirm="deleteLocation(scope.row.bookingpress_location_id)"
												confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
												cancel-button-type="bpa-btn bpa-btn__small">
												<el-button type="text" slot="reference" class="bpa-btn bpa-btn--icon-without-box __danger">
													<span class="material-icons-round">delete</span>
												</el-button>
											</el-popconfirm>
										</el-tooltip>
									</div>
								</div>
							</template>
						</el-table-column>
					</el-table>
				</el-container>
			</el-col>
		</el-row>

		<el-row class="bpa-pagination" type="flex" v-if="items.length > 0"> <!-- Pagination -->
            <el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" >
                <div class="bpa-pagination-left">
                    <p><?php esc_html_e('Showing', 'bookingpress-location'); ?>&nbsp;<strong><u>{{ items.length }}</u></strong>&nbsp;<p><?php esc_html_e('out of', 'bookingpress-location'); ?></p>&nbsp;<strong>{{ totalItems }}</strong></p>
                    <div class="bpa-pagination-per-page">
                        <p><?php esc_html_e('Per Page', 'bookingpress-location'); ?></p>
                        <el-select v-model="pagination_length_val" placeholder="Select" @change="changePaginationSize($event)" class="bpa-form-control" popper-class="bpa-pagination-dropdown">
                            <el-option v-for="item in pagination_val" :key="item.text" :label="item.text" :value="item.value"></el-option>
                        </el-select>
                    </div>
                </div>
            </el-col>
            <el-col :xs="24" :sm="24" :md="24" :lg="12" :xl="12" class="bpa-pagination-nav">
                <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="currentPage" layout="prev, pager, next" :total="totalItems" :page-sizes="pagination_length" :page-size="perPage"></el-pagination>
            </el-col>        
        </el-row> */ ?>
	</div>
</el-main>


<!-- Location Add Modal -->
<el-dialog custom-class="bpa-dialog bpa-dialog--fullscreen bpa-dialog--fullscreen__location bpa--is-page-scrollable-tablet" title="" :visible.sync="open_location_modal" top="32px" fullscreen=true :close-on-press-escape="close_modal_on_esc">
    <div class="bpa-dialog-heading">
        <el-row type="flex">
            <el-col :xs="12" :sm="12" :md="16" :lg="16" :xl="16">
                <h1 class="bpa-page-heading" v-if="location.location_update_id == 0">
                    <?php esc_html_e('Add Location', 'bookingpress-location'); ?></h1>
                <h1 class="bpa-page-heading" v-else><?php esc_html_e('Edit Location', 'bookingpress-location'); ?></h1>
            </el-col>
            <el-col :xs="12" :sm="12" :md="7" :lg="7" :xl="7" class="bpa-dh__btn-group-col">
                <el-button class="bpa-btn bpa-btn--primary" :class="(is_display_save_loader == '1') ? 'bpa-btn--is-loader' : ''" :disabled="is_disabled" @click="bookingpress_save_location">
                      <span class="bpa-btn__label"><?php esc_html_e('Save', 'bookingpress-location'); ?></span>
                      <div class="bpa-btn--loader__circles">                    
                          <div></div>
                          <div></div>
                          <div></div>
                      </div>
                </el-button>    
                <el-button class="bpa-btn" @click="closeLocationModal()">
                    <?php esc_html_e('Cancel', 'bookingpress-location'); ?></el-button>
		 <?php do_action('bookingpress_location_header_extra_button'); ?>    
            </el-col>
        </el-row>
    </div>
    <div class="bpa-dialog-body">
        <div class="bpa-back-loader-container" v-if="is_display_loader == '1'">
            <div class="bpa-back-loader"></div>
        </div>
        <div class="bpa-form-row">
            <el-row>
                <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                    <div class="bpa-db-sec-heading">
                        <el-row type="flex" align="middle">
                            <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
                                <div class="db-sec-left">
                                    <h2 class="bpa-page-heading"><?php esc_html_e('Basic Details', 'bookingpress-location'); ?></h2>
                                    
                                </div>
                            </el-col>
                            
                        </el-row>
                    </div>
                    <div class="bpa-default-card bpa-db-card">
                        <el-form ref="location" :rules="rules" :model="location" label-position="top" @submit.native.prevent>
                            <template>
                                <el-row :gutter="24">
                                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="bpa-form-group">
                                        <el-upload class="bpa-upload-component" ref="avatarRef"
                                            action="<?php echo wp_nonce_url(admin_url('admin-ajax.php') . '?action=bookingpress_upload_location', 'bookingpress_upload_location');//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason - esc_html is already used by wp_nonce_url function and it's false positive ?>"
                                            :on-success="bookingpress_upload_location_func"
                                            :file-list="location.location_images_list" multiple="false"
                                            :show-file-list="locationShowFileList" limit="1"
                                            :on-exceed="bookingpress_image_upload_limit"
                                            :on-error="bookingpress_image_upload_err"
                                            :on-remove="bookingpress_remove_location_img"
                                            :before-upload="checkUploadedFile" drag>
                                            <span
                                                class="material-icons-round bpa-upload-component__icon">cloud_upload</span>
                                            <div class="bpa-upload-component__text" v-if="location.location_image == ''"><?php esc_html_e('Please upload jpg/png/webp file', 'bookingpress-location'); ?></div>
                                        </el-upload>
                                        <div class="bpa-uploaded-avatar__preview" v-if="location.location_image != ''">
                                            <button class="bpa-avatar-close-icon" @click="bookingpress_remove_location_img">
                                                <span class="material-icons-round">close</span>
                                            </button>
                                            <el-avatar shape="square" :src="location.location_image" class="bpa-uploaded-avatar__picture"></el-avatar>
                                        </div>
									</el-col>
								</el-row>
								<div class="bpa-form-body-row">
									<el-row :gutter="32">
										<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
											<el-form-item prop="location_name">
												<template #label>
													<span class="bpa-form-label"><?php esc_html_e( 'Location Name:', 'bookingpress-location' ); ?></span>
												</template>
												<el-input class="bpa-form-control" v-model="location.location_name" placeholder="<?php esc_html_e( 'Enter location name', 'bookingpress-location' ); ?>">
												</el-input>
											</el-form-item>
										</el-col>
										<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
											<el-form-item prop="location_phone_number">
                                                <template #label>
                                                    <span class="bpa-form-label"><?php esc_html_e('Phone', 'bookingpress-location'); ?></span>
                                                </template>
                                                <vue-tel-input v-model="location.location_phone_number" class="bpa-form-control --bpa-country-dropdown" @country-changed="bookingpress_phone_country_change_func($event)" v-bind="bookingpress_tel_input_props" ref="bpa_tel_input_field">
                                                    <template v-slot:arrow-icon>
                                                        <span class="material-icons-round">keyboard_arrow_down</span>
                                                    </template>
                                                </vue-tel-input>
                                            </el-form-item>
										</el-col>
										<el-col :xs="24" :sm="24" :md="24" :lg="08" :xl="08">
											<el-form-item prop="">
												<template #label>
													<span class="bpa-form-label"><?php esc_html_e( 'Address:', 'bookingpress-location' ); ?></span>
												</template>
												<el-input class="bpa-form-control" v-model="location.location_address" placeholder="<?php esc_html_e( 'Enter address', 'bookingpress-location' ); ?>">
												</el-input>
											</el-form-item>
										</el-col>
									</el-row>
								</div>
								<div class="bpa-form-body-row">
									<el-row>																		
										<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
											<el-form-item>
												<template #label>
													<span
														class="bpa-form-label"><?php esc_html_e( 'Description:', 'bookingpress-location' ); ?> </span>
												</template>
												<el-input class="bpa-form-control" v-model="location.location_description" type="textarea" :rows="5" placeholder="<?php esc_html_e( 'Description', 'bookingpress-location' ); ?>">
												</el-input>
											</el-form-item>
										</el-col>
									</el-row>
								</div>
								<?php /** Service Selection */ ?>
								<div class="bpa-form-row">
									<el-row>
										<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
											<div class="bpa-db-sec-heading">
												<el-row type="flex" align="middle">
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-db-sec-left">
															<h2 class="bpa-page-heading"><?php esc_html_e( 'Assigned Service', 'bookingpress-location'); ?></h2>
														</div>
													</el-col>
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-hw-right-btn-group">
															<el-button class="bpa-btn bpa-btn__filled-light" @click="bookingpress_location_add_service_model(event)">
																<span class="material-icons-round">add</span>
																<?php esc_html_e( 'Add New', 'bookingpress-location' ); ?>
															</el-button>
														</div>
													</el-col>
												</el-row>
											</div>
											<div class="bpa-default-card bpa-db-card bpa-grid-list-container bpa-dc__staff--assigned-service">
												<div class="bpa-as__body">
													<el-row type="flex" class="bpa-as__empty-view" v-if="location_assigned_services.length == 0">
														<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
															<div class="bpa-data-empty-view">
																<div class="bpa-ev-left-vector">
																	<picture>
																		<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
																		<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
																	</picture>
																</div>				
																<div class="bpa-ev-right-content">					
																	<h4><?php esc_html_e( 'No Services Assigned', 'bookingpress-location' ); ?></h4>
																</div>				
															</div>
														</el-col>
													</el-row>
													<el-row v-else>
														<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
															<div class="bpa-card bpa-card__heading-row">
																<el-row type="flex">
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Service Name', 'bookingpress-location' ); ?></h4>
																		</div>
																	</el-col>
																	<?php do_action( 'bookingpress_location_assigned_service_dynamic_column'); ?>
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07" v-if="is_bring_anyone_with_you_enable == 1">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Min Capacity', 'bookingpress-location' ); ?></h4>
																		</div>
																	</el-col>
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Max Capacity', 'bookingpress-location' ); ?></h4>
																		</div>
																	</el-col>
																	<el-col :xs="3" :sm="3" :md="3" :lg="3" :xl="3">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading"><?php esc_html_e( 'Action', 'bookingpress-location' ); ?></h4>
																		</div>
																	</el-col>
																</el-row>
															</div>
														</el-col>
														<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-for="assigned_service_details in location_assigned_services">
															<div class="bpa-card bpa-card__body-row list-group-item">
																<el-row type="flex">
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading is--body-heading">{{ assigned_service_details.service_name }}</h4>
																		</div>
																	</el-col>
																	<?php do_action( 'bookingpress_location_assigned_service_dynamic_column_value') ?>
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07" v-if="is_bring_anyone_with_you_enable == 1">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading is--body-heading">{{ assigned_service_details.service_min_capacity }}</h4>
																		</div>
																	</el-col>
																	<el-col :xs="07" :sm="07" :md="07" :lg="07" :xl="07">
																		<div class="bpa-card__item">
																			<h4 class="bpa-card__item__heading is--body-heading">{{ assigned_service_details.service_capacity }}</h4>
																		</div>
																	</el-col>
																	<el-col :xs="3" :sm="3" :md="3" :lg="3" :xl="3">
																		<div>
																			<el-tooltip effect="dark" content="" placement="top" open-delay="300">
																				<div slot="content">
																					<span><?php esc_html_e( 'Edit', 'bookingpress-location' ); ?></span>
																				</div>
																				<el-button class="bpa-btn bpa-btn--icon-without-box" @click="bookingpress_edit_assigned_location_service( assigned_service_details.service_staff_location_id, event )">
																					<span class="material-icons-round">mode_edit</span>
																				</el-button>
																			</el-tooltip>
																			<el-tooltip effect="dark" content="" placement="top" open-delay="300">
																				<div slot="content">
																					<span><?php esc_html_e( 'Delete', 'bookingpress-location' ); ?></span>
																				</div>
																				<el-button class="bpa-btn bpa-btn--icon-without-box __danger" @click="bookingpress_delete_assigned_location_service( assigned_service_details.service_staff_location_id )">
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
										</el-col>
									</el-row>
									<br/><br/><br/>
								</div>
								<?php /** Service Selection */ ?>								
							</template>
						</el-form>
					</div>
				</el-col>
			</el-row>
		</div>
	</div>
</el-dialog>

<el-dialog id="assign_location_service" custom-class="bpa-dialog bpa-dailog__small bpa-dialog-assign-service__is-location" title="" :visible.sync="open_assign_service_location_modal" :close-on-press-escape="close_modal_on_esc">
	<div class="bpa-dialog-heading">
		<el-row type="flex">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<h1 class="bpa-page-heading"><?php esc_html_e('Add Service', 'bookingpress-location'); ?></h1>
			</el-col>
		</el-row>
	</div>
	<div class="bpa-dialog-body">
		<el-container class="bpa-grid-list-container bpa-add-categpry-container">
			<div class="bpa-form-row">
				<el-row>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<el-form label-position="top" @submit.native.prevent>
							<div class="bpa-form-body-row">
								<el-row>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
										<el-form-item>
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Select Service', 'bookingpress-location' ); ?></span>
											</template> 
											<el-select v-model="assign_location_service_form.assign_service_id" class="bpa-form-control" filterable collapse-tags placeholder="<?php esc_html_e( 'Select Service', 'bookingpress-location' ); ?>" :popper-append-to-body="false" popper-class="bpa-el-select--is-with-navbar" @change="bookingpress_set_assign_staffmember($event)" ><!-- @change="bookingpress_set_assign_service_name($event)" -->
												<el-option-group v-for="item in bookingpress_service_list" :key="item.category_name" :label="item.category_name">
													<el-option v-for="cat_services in item.category_services" :key="cat_services.service_id" :label="cat_services.service_name" :value="cat_services.service_id"></el-option>
												</el-option-group>
											</el-select>
										</el-form-item>
									</el-col>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="is_bring_anyone_with_you_enable == 1">
										<el-form-item>
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Min Capacity', 'bookingpress-location' ); ?></span>
											</template>
											<el-input-number class="bpa-form-control bpa-form-control--number" :min="1" :max="999" v-model="assign_location_service_form.assign_service_min_capacity" step-strictly></el-input-number>
										</el-form-item>
									</el-col>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
										<el-form-item>
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Max Capacity', 'bookingpress-location' ); ?></span>
											</template>
											<el-input-number class="bpa-form-control bpa-form-control--number" :min="1" :max="999" v-model="assign_location_service_form.assign_service_capacity" step-strictly></el-input-number>
										</el-form-item>
									</el-col>
									<?php do_action( 'bookingpress_add_dynamic_content_for_add_location_service_staff'); ?>
								</el-row>
							</div>
						</el-form>
					</el-col>
				</el-row>
			</div>
		</el-container>
	</div>
	<div class="bpa-dialog-footer">
		<div class="bpa-hw-right-btn-group">
			<el-button class="bpa-btn bpa-btn__small bpa-btn--primary" @click="bookingpress_save_assign_location_service()"><?php esc_html_e( 'Save', 'bookingpress-location' ); ?></el-button>
			<el-button class="bpa-btn bpa-btn__small" @click="bookingpress_close_assign_location_modal()"><?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?></el-button>
		</div>
	</div>
</el-dialog>
<?php do_action( 'bookingpress_manage_location_view_bottom'); ?>