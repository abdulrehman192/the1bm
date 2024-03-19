<el-dialog id="location_modal" custom-class="bpa-dialog bpa-dailog__small bpa-dialog--add-location" :visible.sync="bookingpress_open_add_location_modal" :visible.sync="centerDialogVisible" :close-on-press-escape="close_modal_on_esc">
	<div class="bpa-dialog-heading">
		<el-row type="flex">
			<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
				<h1 class="bpa-page-heading" v-if="location_form_data.is_location_edit == true"><?php esc_html_e( 'Edit Location', 'bookingpress-location' ); ?></h1>
				<h1 class="bpa-page-heading" v-else><?php esc_html_e( 'Add Location', 'bookingpress-location' ); ?></h1>
			</el-col>
		</el-row>
	</div>
	<div class="bpa-dialog-body">
		<el-container class="bpa-grid-list-container bpa-add-categpry-container">
			<div class="bpa-form-row">
				<el-row>
					<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
						<el-form ref="location_form" :rules="location_form_rules" :model="location_form_data" label-position="top" @submit.native.prevent>
							<div class="bpa-form-body-row">
								<el-row>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
										<el-form-item prop="selected_location">
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Select Location', 'bookingpress-location' ); ?></span>
											</template>
											<el-select class="bpa-form-control" v-model="location_form_data.selected_location" filterable>
                                                <el-option label="<?php esc_html_e( 'Select Location', 'bookingpress-location' ); ?>" value=""></el-option>
                                                <el-option :label="location_details.bookingpress_location_name" :value="location_details.bookingpress_location_id" v-for="(location_details, key) in bookingpress_locations"></el-option>
                                            </el-select>
										</el-form-item> 
									</el-col>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="is_bring_anyone_with_you_enable == 1">
										<el-form-item prop="location_min_capacity">
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Min Capacity', 'bookingpress-location' ); ?></span>
											</template>
											<el-input-number class="bpa-form-control bpa-form-control--number" :min="1" v-model="location_form_data.location_min_capacity" id="location_min_capacity" name="location_min_capacity" step-strictly></el-input-number>
										</el-form-item>
									</el-col>
									<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
										<el-form-item prop="location_max_capacity">
											<template #label>
												<span class="bpa-form-label"><?php esc_html_e( 'Max Capacity', 'bookingpress-location' ); ?></span>
											</template>
											<el-input-number class="bpa-form-control bpa-form-control--number" :min="1" v-model="location_form_data.location_max_capacity" id="location_max_capacity" name="location_max_capacity" step-strictly></el-input-number>
										</el-form-item>
									</el-col>
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
			<el-button class="bpa-btn bpa-btn__small" @click="close_location_modal()"><?php esc_html_e( 'Cancel', 'bookingpress-location' ); ?></el-button>
			<el-button class="bpa-btn bpa-btn__small bpa-btn--primary" @click="saveLocationDetails()" v-if="location_form_data.is_location_edit == false"><?php esc_html_e( 'Add', 'bookingpress-location' ); ?></el-button>
			<el-button class="bpa-btn bpa-btn__small bpa-btn--primary" @click="saveLocationDetails()" v-else><?php esc_html_e( 'Update', 'bookingpress-location' ); ?></el-button>
		</div>
	</div>
</el-dialog>