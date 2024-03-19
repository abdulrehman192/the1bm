</div>		
<div v-cloak v-if="is_waiting_list == 1">
<el-row v-if="items.length > 0" class="bpa-waitinglist-appointment-list">
	<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
		<el-container class="bpa-table-container">
			<div class="bpa-back-loader-container" v-if="is_display_loader == '1'">
				<div class="bpa-back-loader"></div>
			</div>			
			<div class="bpa-tc__wrapper" v-if="current_screen_size == 'desktop'">
				<el-table v-cloak @selection-change="handleWaitingSelectionChange" :row-class-name="tableRowClassName" ref="multipleTable" class="bpa-manage-appointment-items" :data="items"  fit="false" @row-click="bookingpress_full_row_clickable" @expand-change="bookingpress_row_expand">
					<el-table-column v-cloak type="expand">
						<template slot-scope="scope">
							<div class="bpa-view-appointment-card">
								<div class="bpa-vac--head">
									<div class="bpa-vac--head__left">
										<span><?php esc_html_e('Booking ID', 'bookingpress-waiting-list'); ?>: #{{ scope.row.booking_id }}</span>
										<div class="bpa-left__service-detail">
											<h2>{{ scope.row.service_name }}</h2>
											<span class="bpa-sd__price" v-if="scope.row.bookingpress_is_deposit_enable == '1'">{{ scope.row.bookingpress_deposit_amt_with_currency }}</span>
											<span class="bpa-sd__price" v-else>{{ scope.row.bookingpress_final_total_amt_with_currency }}</span>
										</div>
									</div>
									<div class="bpa-hw-right-btn-group bpa-vac--head__right">
										<el-button class="bpa-btn" v-if="bookingpress_manage_appointment == 1 && scope.row.appointment_refund_status == 1 && scope.row.appointment_status != '3'">
											<span class="material-icons-round">close</span>
											<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
										</el-button>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Close', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to cancel this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="bookingpress_cancel_waiting_appointment(scope.row.appointment_id, '3')" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small" 
											v-else-if="bookingpress_manage_appointment == 1 && scope.row.appointment_status != '3'">
											<el-button type="text" slot="reference" class="bpa-btn" v-if="scope.row.appointment_status != '3'">
												<span class="material-icons-round">close</span>
												<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
											</el-button>
										</el-popconfirm>&nbsp;
										
									</div>
								</div>
								<div class="bpa-vac--body">
									<el-row :gutter="56">
										<el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="18">
											<div class="bpa-vac-body--appointment-details">
												<el-row :gutter="40">
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__basic-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Basic Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Date', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_date }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Time', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_time }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.appointment_note != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.note}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.appointment_note }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="(scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != '') || (scope.row.bookingpress_staff_email_address != '')">
																<div class="bpa-bd__item-head">
																	<span><?php echo esc_html($bookingpress_singular_staffmember_name); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-if="scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != ''">{{ scope.row.bookingpress_staff_firstname }} {{ scope.row.bookingpress_staff_lastname }}</h4>
																	<h4 v-else>{{ scope.row.bookingpress_staff_email_address }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.bookingpress_selected_extra_members > 0">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('No. Of Person', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.bookingpress_selected_extra_members }}</h4>
																</div>
															</div>
															<?php do_action('add_bookingpress_location_appointment_details_outside'); ?>
														</div>
													</el-col>
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__customer-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Customer Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item"  v-if="scope.row.customer_name != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.fullname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_first_name != ''">
																<div class="bpa-bd__item-head">
																<span>{{form_field_data.firstname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_first_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head" v-if="scope.row.customer_last_name != ''">
																	<span>{{form_field_data.lastname}}</span>
																</div>
																<div class="bpa-bd__item-body" >
																	<h4>{{ scope.row.customer_last_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.email_address}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_email }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_phone != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.phone_number}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_phone }}</h4>
																</div>
															</div>
														</div>
													</el-col>
												</el-row>
											</div>
											<div class="bpa-vac-body--service-extras" v-if="scope.row.bookingpress_extra_service_data.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Extras', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-se__items">
													<div class="bpa-se__item" v-for="extra_details in scope.row.bookingpress_extra_service_data">
														<p>{{ extra_details.extra_name }}</p>
														<p class="bpa-se__item-duration"><span class="material-icons-round">schedule</span> {{ extra_details.extra_service_duration }}</p>
														<p class="bpa-se__item-qty"><span><?php esc_html_e('Qty:', 'bookingpress-waiting-list'); ?></span> {{ extra_details.selected_qty }}</p>
														<p>{{ extra_details.extra_service_price_with_currency }}</p>
													</div>
												</div>
											</div>
											<div class="bpa-vac-body--custom-fields" v-if="scope.row.custom_fields_values.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Custom Fields', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-cf__body">
													<el-row>
														<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" v-for="custom_fields in scope.row.custom_fields_values">
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span v-html="custom_fields.label"></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-html="custom_fields.value"></h4>
																</div>
															</div>																
														</el-col>
													</el-row>
												</div>
											</div>
											<?php do_action('bookingpress_backend_display_guest_data'); ?>
										</el-col>
										<el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="6" v-if="bookingpress_payments == 1">
											<div class="bpa-vac-body--payment-details">
												<h4><?php esc_html_e('Payment Details', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-pd__body">
													<div class="bpa-pd__item bpa-pd-method__item">
														<span><?php esc_html_e('Payment Method', 'bookingpress-waiting-list'); ?></span>
														<p>-</p>
													</div>
													<div class="bpa-pd__item">
														<span><?php esc_html_e('Status', 'bookingpress-waiting-list'); ?></span>
														<p :class="((scope.row.appointment_status == '2') ? 'bpa-cl-pt-orange' : '') || (scope.row.appointment_status == '3' ? 'bpa-cl-black-200' : '') || (scope.row.appointment_status == '1' ? 'bpa-cl-pt-blue' : '') || (scope.row.appointment_status == '4' ? 'bpa-cl-danger' : '') || (scope.row.appointment_status == '5' ? 'bpa-cl-pt-brown' : '') || (scope.row.appointment_status == '6' ? 'bpa-cl-pt-main-green' : '')">{{ scope.row.appointment_status_label }}</p>
													</div>

													<div class="bpa-pd__item" v-if="scope.row.bookingpress_tax_amt != '0' && (scope.row.price_display_setting != 'include_taxes' || (scope.row.price_display_setting == 'include_taxes' && scope.row.display_tax_amount_in_order_summary == 'true' ) )">
														<span><?php esc_html_e('Tax', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.bookingpress_tax_amt_with_currency }}</p>
													</div>
													<?php do_action('bookingpress_modify_payment_appointment_section'); ?>																							
													<div class="bpa-pd__item bpa-pd-total__item">
														<span>
															<?php esc_html_e('Total Amount', 'bookingpress-waiting-list'); ?>
															<div class="bpa-vac-pd-total__tax-include-label" v-if="scope.row.price_display_setting == 'include_taxes'">{{ scope.row.included_tax_label }}</div>
														</span>
														<p class="bpa-cl-pt-main-green">{{ scope.row.bookingpress_final_total_amt_with_currency }}</p>
													</div>
												</div>									
											</div>
										</el-col>
									</el-row>										
								</div>
							</div>
						</template>
					</el-table-column>
					<el-table-column v-cloak type="selection"></el-table-column>					
					<el-table-column v-cloak prop="booking_id" min-width="30" label="<?php esc_html_e( 'ID', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">
							<span>#{{ scope.row.booking_id }}</span>
						</template>
					</el-table-column>
					<?php do_action('bookingpress_waiting_list_outside_add_column'); ?>
					<el-table-column v-cloak prop="appointment_date" min-width="120" label="<?php esc_html_e( 'Date', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">
							<label class="bpa-item__date-col">{{ scope.row.appointment_date }}</label>
							<el-tooltip content="<?php esc_html_e('Rescheduled', 'bookingpress-waiting-list'); ?>" placement="top" v-if="scope.row.is_rescheduled == 1">
								<span class="material-icons-round bpa-rescheduled-appointment-icon" v-if="scope.row.is_rescheduled == 1">update</span>
							</el-tooltip>
						</template>
					</el-table-column>
					<el-table-column v-cloak prop="customer_name" min-width="90" label="<?php esc_html_e( 'Customer', 'bookingpress-waiting-list' ); ?>"></el-table-column>
					<?php if ( ! $BookingPressPro->bookingpress_check_user_role( 'bookingpress-staffmember' ) ) { ?>
					<el-table-column v-cloak prop="staff_member_name" min-width="90" label="<?php echo esc_html($bookingpress_singular_staffmember_name); ?>" v-if="is_staffmember_activated == 1"></el-table-column>
					<?php } ?>
					<el-table-column v-cloak prop="service_name" min-width="110" label="<?php esc_html_e( 'Service', 'bookingpress-waiting-list' ); ?>"></el-table-column>
					<el-table-column v-cloak prop="appointment_duration" min-width="70" label="<?php esc_html_e( 'Duration', 'bookingpress-waiting-list' ); ?>"></el-table-column>
					<el-table-column v-cloak prop="appointment_status" min-width="80" label="<?php esc_html_e( 'Status', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">				    
							<div class="bpa-table-status-dropdown-wrapper" :class="(scope.row.change_status_loader == 1) ? '__bpa-is-loader-active' : ''">
								<div class="bpa-tsd--loader" v-if=" scope.row.change_status_loader == 1" :class="(scope.row.change_status_loader == 1) ? '__bpa-is-active' : ''">
									<div class="bpa-btn--loader__circles">
										<div></div>
										<div></div>
										<div></div>
									</div>
								</div>
								<el-tag v-if="scope.row.is_avaliable_status == 0" class="bpa-front-pill --warning">{{ scope.row.appointment_status_label }}</el-tag>
								<el-tag v-if="scope.row.is_avaliable_status == 1" class="bpa-front-pill"><?php esc_html_e( 'Available', 'bookingpress-waiting-list' ); ?></el-tag>
							</div>
							<div class="bpa-table-actions-wrap" v-if="bookingpress_manage_appointment == 1"> 
								<div class="bpa-table-actions">									
									<el-tooltip v-if="scope.row.is_avaliable_status == 1" effect="dark" content="" placement="top" open-delay="300">
										<div slot="content">
											<span><?php esc_html_e( 'Approve', 'bookingpress-waiting-list' ); ?></span>
										</div>
										<el-button @click="approveAppointment(scope.$index, scope.row)" class="bpa-btn bpa-btn--icon-without-box">
											<span class="material-icons-round">done</span>
										</el-button>
									</el-tooltip>										
									<el-tooltip effect="dark" content="" placement="top" open-delay="300" v-if="bookingpress_delete_appointment == 1">
										<div slot="content">
											<span><?php esc_html_e( 'Delete', 'bookingpress-waiting-list' ); ?></span>
										</div>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to delete this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="deleteAppointment(scope.$index, scope.row)" 
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
						</template>
					</el-table-column>
				</el-table>
			</div>
			<div class="bpa-tc__wrapper" v-if="current_screen_size == 'tablet'">
				<el-table @selection-change="handleWaitingSelectionChange" v-cloak :row-class-name="tableRowClassName" ref="multipleTable" class="bpa-manage-appointment-items" :data="items"  fit="false" @row-click="bookingpress_full_row_clickable" @expand-change="bookingpress_row_expand">
					<el-table-column type="expand">
						<template  slot-scope="scope">
							<div class="bpa-view-appointment-card">
								<div class="bpa-vac--head">
									<div class="bpa-vac--head__left">
										<span><?php esc_html_e('Booking ID', 'bookingpress-waiting-list'); ?>: #{{ scope.row.booking_id }}</span>
										<div class="bpa-left__service-detail">
											<h2>{{ scope.row.service_name }}</h2>
											<span class="bpa-sd__price" v-if="scope.row.bookingpress_is_deposit_enable == '1'">{{ scope.row.bookingpress_deposit_amt_with_currency }}</span>
											<span class="bpa-sd__price" v-else>{{ scope.row.bookingpress_final_total_amt_with_currency }}</span>
										</div>
									</div>
									<div class="bpa-hw-right-btn-group bpa-vac--head__right">
										<el-button @click="bookingpress_open_refund_model(event,scope.row.appointment_id,scope.row.payment_id,scope.row.appointment_currency_symbol,scope.row.appointment_partial_refund)" class="bpa-btn" v-if="bookingpress_manage_appointment == 1 && scope.row.appointment_refund_status == 1 && scope.row.appointment_status != '3'">
											<span class="material-icons-round">close</span>
											<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
										</el-button>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Close', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to cancel this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="bookingpress_cancel_waiting_appointment(scope.row.appointment_id, '3')" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small" 
											v-else-if="bookingpress_manage_appointment == 1 && scope.row.appointment_status != '3'">
											<el-button type="text" slot="reference" class="bpa-btn" v-if="scope.row.appointment_status != '3'">
												<span class="material-icons-round">close</span>
												<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
											</el-button>
										</el-popconfirm>&nbsp;
										<?php
											do_action('bookingpress_add_dynamic_buttons_for_view_appointments');
										?>
									</div>
								</div>
								<div class="bpa-vac--body">
									<el-row :gutter="56">
										<el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="18">
											<div class="bpa-vac-body--appointment-details">
												<el-row :gutter="40">
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__basic-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Basic Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Date', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_date }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Time', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_time }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.appointment_note != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.note}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.appointment_note }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="(scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != '') || (scope.row.bookingpress_staff_email_address != '')">
																<div class="bpa-bd__item-head">
																	<span><?php echo esc_html($bookingpress_singular_staffmember_name); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-if="scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != ''">{{ scope.row.bookingpress_staff_firstname }} {{ scope.row.bookingpress_staff_lastname }}</h4>
																	<h4 v-else>{{ scope.row.bookingpress_staff_email_address }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.bookingpress_selected_extra_members > 0">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('No. Of Person', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.bookingpress_selected_extra_members }}</h4>
																</div>
															</div>
															<?php do_action('add_bookingpress_location_appointment_details_outside'); ?>
														</div>
													</el-col>
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__customer-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Customer Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item"  v-if="scope.row.customer_name != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.fullname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_first_name != ''">
																<div class="bpa-bd__item-head">
																<span>{{form_field_data.firstname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_first_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head" v-if="scope.row.customer_last_name != ''">
																	<span>{{form_field_data.lastname}}</span>
																</div>
																<div class="bpa-bd__item-body" >
																	<h4>{{ scope.row.customer_last_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.email_address}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_email }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_phone != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.phone_number}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_phone }}</h4>
																</div>
															</div>
														</div>
													</el-col>
												</el-row>
											</div>
											<div class="bpa-vac-body--service-extras" v-if="scope.row.bookingpress_extra_service_data.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Extras', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-se__items">
													<div class="bpa-se__item" v-for="extra_details in scope.row.bookingpress_extra_service_data">
														<p>{{ extra_details.extra_name }}</p>
														<p class="bpa-se__item-duration"><span class="material-icons-round">schedule</span> {{ extra_details.extra_service_duration }}</p>
														<p class="bpa-se__item-qty"><span><?php esc_html_e('Qty:', 'bookingpress-waiting-list'); ?></span> {{ extra_details.selected_qty }}</p>
														<p>{{ extra_details.extra_service_price_with_currency }}</p>
													</div>
												</div>
											</div>
											<div class="bpa-vac-body--custom-fields" v-if="scope.row.custom_fields_values.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Custom Fields', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-cf__body">
													<el-row>
														<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" v-for="custom_fields in scope.row.custom_fields_values">
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span v-html="custom_fields.label"></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-html="custom_fields.value"></h4>
																</div>
															</div>																
														</el-col>
													</el-row>
												</div>
											</div>
											<?php do_action('bookingpress_backend_display_guest_data'); ?>
										</el-col>
										<el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="6" v-if="bookingpress_payments == 1">
											<div class="bpa-vac-body--payment-details">
												<h4><?php esc_html_e('Payment Details', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-pd__body">
													<div class="bpa-pd__item bpa-pd-method__item">
														<span><?php esc_html_e('Payment Method', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.payment_method_label }}</p>
													</div>
													<div class="bpa-pd__item">
														<span><?php esc_html_e('Status', 'bookingpress-waiting-list'); ?></span>
														<p :class="((scope.row.appointment_status == '2') ? 'bpa-cl-pt-orange' : '') || (scope.row.appointment_status == '3' ? 'bpa-cl-black-200' : '') || (scope.row.appointment_status == '1' ? 'bpa-cl-pt-blue' : '') || (scope.row.appointment_status == '4' ? 'bpa-cl-danger' : '') || (scope.row.appointment_status == '5' ? 'bpa-cl-pt-brown' : '') || (scope.row.appointment_status == '6' ? 'bpa-cl-pt-main-green' : '')">{{ scope.row.appointment_status_label }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_deposit_amt != '0'">
														<span><?php esc_html_e('Deposit', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.bookingpress_deposit_amt_with_currency }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_tax_amt != '0' && (scope.row.price_display_setting != 'include_taxes' || (scope.row.price_display_setting == 'include_taxes' && scope.row.display_tax_amount_in_order_summary == 'true' ) )">
														<span><?php esc_html_e('Tax', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.bookingpress_tax_amt_with_currency }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_applied_coupon_code != ''">
														<span><?php esc_html_e('Coupon', 'bookingpress-waiting-list'); ?> ( {{ scope.row.bookingpress_applied_coupon_code }} )</span>
														<p>{{ scope.row.bookingpress_coupon_discount_amt_with_currency }}</p>
													</div>
													<?php do_action('bookingpress_modify_payment_appointment_section') ?>
													<div class="bpa-pd__item bpa-pd-total__item">
														<span>
															<?php esc_html_e('Total Amount', 'bookingpress-waiting-list'); ?>
															<div class="bpa-vac-pd-total__tax-include-label" v-if="scope.row.price_display_setting == 'include_taxes'">{{ scope.row.included_tax_label }}</div>
														</span>
														<p class="bpa-cl-pt-main-green">{{ scope.row.bookingpress_final_total_amt_with_currency }}</p>
													</div>
												</div>									
											</div>
										</el-col>
									</el-row>										
								</div>
							</div>
						</template>
					</el-table-column>
					<el-table-column type="selection"></el-table-column>					
					<el-table-column prop="booking_id" min-width="30" label="<?php esc_html_e( 'ID', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">
							<span>#{{ scope.row.booking_id }}</span>
						</template>
					</el-table-column>
					<?php do_action('bookingpress_waiting_list_outside_add_column'); ?>
					<el-table-column prop="appointment_date" min-width="100" label="<?php esc_html_e( 'Date', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">
							<label class="bpa-item__date-col">
								{{ scope.row.appointment_date }}
								<el-tooltip content="<?php esc_html_e('Rescheduled', 'bookingpress-waiting-list'); ?>" placement="top" v-if="scope.row.is_rescheduled == 1">
									<span class="material-icons-round bpa-rescheduled-appointment-icon" v-if="scope.row.is_rescheduled == 1">update</span>
								</el-tooltip>
							</label>
							<label class="bpa-item__date-col bpa-item__dt-col-duration-md">
								<span class="material-icons-round">schedule</span>
								{{ scope.row.appointment_duration }}
							</label>
						</template>
					</el-table-column>
					<el-table-column prop="service_name" min-width="100" label="<?php esc_html_e( 'Service', 'bookingpress-waiting-list' ); ?>"></el-table-column>
					<el-table-column prop="appointment_status" min-width="90" label="<?php esc_html_e( 'Status', 'bookingpress-waiting-list' ); ?>">
						<template slot-scope="scope">				    
							<div class="bpa-table-status-dropdown-wrapper" :class="(scope.row.change_status_loader == 1) ? '__bpa-is-loader-active' : ''">
								<div class="bpa-tsd--loader" v-if=" scope.row.change_status_loader == 1" :class="(scope.row.change_status_loader == 1) ? '__bpa-is-active' : ''">
									<div class="bpa-btn--loader__circles">
										<div></div>
										<div></div>
										<div></div>
									</div>
								</div>
								<el-tag v-if="scope.row.is_avaliable_status == 0" class="bpa-front-pill --warning">{{ scope.row.appointment_status_label }}</el-tag>
								<el-tag v-if="scope.row.is_avaliable_status == 1" class="bpa-front-pill"><?php esc_html_e( 'Available', 'bookingpress-waiting-list' ); ?></el-tag>
							</div>
							<div class="bpa-table-actions-wrap" v-if="bookingpress_manage_appointment == 1"> 
								<div class="bpa-table-actions">									
									<el-tooltip v-if="scope.row.is_avaliable_status == 1" effect="dark" content="" placement="top" open-delay="300">
										<div slot="content">
											<span><?php esc_html_e( 'Approve', 'bookingpress-waiting-list' ); ?></span>
										</div>
										<el-button @click="approveAppointment(scope.$index, scope.row)" class="bpa-btn bpa-btn--icon-without-box">
											<span class="material-icons-round">done</span>
										</el-button>
									</el-tooltip>										
									<el-tooltip effect="dark" content="" placement="top" open-delay="300" v-if="bookingpress_delete_appointment == 1">
										<div slot="content">
											<span><?php esc_html_e( 'Delete', 'bookingpress-waiting-list' ); ?></span>
										</div>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to delete this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="deleteAppointment(scope.$index, scope.row)" 
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
						</template>
					</el-table-column>
				</el-table>
			</div>
			<div class="bpa-tc__wrapper bpa-manage-appointment-container--sm" v-if="current_screen_size == 'mobile'">
				<el-table @selection-change="handleWaitingSelectionChange" v-cloak :row-class-name="tableRowClassName" ref="multipleTable" class="bpa-manage-appointment-items" :data="items"  fit="false" :show-header="false" @row-click="bookingpress_full_row_clickable" @expand-change="bookingpress_row_expand">
					<el-table-column type="expand">
						<template slot-scope="scope">
							<div class="bpa-view-appointment-card">
								<div class="bpa-vac--head">
									<div class="bpa-vac--head__left">
										<span><?php esc_html_e('Booking ID', 'bookingpress-waiting-list'); ?>: #{{ scope.row.booking_id }}</span>
										<div class="bpa-left__service-detail">
											<h2>{{ scope.row.service_name }}</h2>
											<span class="bpa-sd__price" v-if="scope.row.bookingpress_is_deposit_enable == '1'">{{ scope.row.bookingpress_deposit_amt_with_currency }}</span>
											<span class="bpa-sd__price" v-else>{{ scope.row.bookingpress_final_total_amt_with_currency }}</span>
										</div>
									</div>
									<div class="bpa-hw-right-btn-group bpa-vac--head__right">
										<el-button @click="bookingpress_open_refund_model(event,scope.row.appointment_id,scope.row.payment_id,scope.row.appointment_currency_symbol,scope.row.appointment_partial_refund)" class="bpa-btn" v-if="bookingpress_manage_appointment == 1 && scope.row.appointment_refund_status == 1 && scope.row.appointment_status != '3'">
											<span class="material-icons-round">close</span>
											<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
										</el-button>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Close', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to cancel this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="bookingpress_cancel_waiting_appointment(scope.row.appointment_id, '3')" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small" 
											v-else-if="bookingpress_manage_appointment == 1 && scope.row.appointment_status != '3'">
											<el-button type="text" slot="reference" class="bpa-btn" v-if="scope.row.appointment_status != '3'">
												<span class="material-icons-round">close</span>
												<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>
											</el-button>
										</el-popconfirm>&nbsp;
										<?php
											do_action('bookingpress_add_dynamic_buttons_for_view_appointments');
										?>
									</div>
								</div>
								<div class="bpa-vac--body">
									<el-row :gutter="56">
										<el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="18">
											<div class="bpa-vac-body--appointment-details">
												<el-row :gutter="40">
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__basic-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Basic Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Date', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_date }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('Time', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.view_appointment_time }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.appointment_note != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.note}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.appointment_note }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="(scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != '') || (scope.row.bookingpress_staff_email_address != '')">
																<div class="bpa-bd__item-head">
																	<span><?php echo esc_html($bookingpress_singular_staffmember_name); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-if="scope.row.bookingpress_staff_firstname != '' && scope.row.bookingpress_staff_lastname != ''">{{ scope.row.bookingpress_staff_firstname }} {{ scope.row.bookingpress_staff_lastname }}</h4>
																	<h4 v-else>{{ scope.row.bookingpress_staff_email_address }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.bookingpress_selected_extra_members > 0">
																<div class="bpa-bd__item-head">
																	<span><?php esc_html_e('No. Of Person', 'bookingpress-waiting-list'); ?></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.bookingpress_selected_extra_members }}</h4>
																</div>
															</div>
															<?php do_action('add_bookingpress_location_appointment_details_outside'); ?>
														</div>
													</el-col>
													<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12">
														<div class="bpa-ad__customer-details">
															<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Customer Details', 'bookingpress-waiting-list'); ?></h4>
															<div class="bpa-bd__item"  v-if="scope.row.customer_name != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.fullname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_first_name != ''">
																<div class="bpa-bd__item-head">
																<span>{{form_field_data.firstname}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_first_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head" v-if="scope.row.customer_last_name != ''">
																	<span>{{form_field_data.lastname}}</span>
																</div>
																<div class="bpa-bd__item-body" >
																	<h4>{{ scope.row.customer_last_name }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.email_address}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_email }}</h4>
																</div>
															</div>
															<div class="bpa-bd__item" v-if="scope.row.customer_phone != ''">
																<div class="bpa-bd__item-head">
																	<span>{{form_field_data.phone_number}}</span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4>{{ scope.row.customer_phone }}</h4>
																</div>
															</div>
														</div>
													</el-col>
												</el-row>
											</div>
											<div class="bpa-vac-body--service-extras" v-if="scope.row.bookingpress_extra_service_data.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Extras', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-se__items">
													<div class="bpa-se__item" v-for="extra_details in scope.row.bookingpress_extra_service_data">
														<p>{{ extra_details.extra_name }}</p>
														<p class="bpa-se__item-duration"><span class="material-icons-round">schedule</span> {{ extra_details.extra_service_duration }}</p>
														<p class="bpa-se__item-qty"><span><?php esc_html_e('Qty:', 'bookingpress-waiting-list'); ?></span> {{ extra_details.selected_qty }}</p>
														<p>{{ extra_details.extra_service_price_with_currency }}</p>
													</div>
												</div>
											</div>
											<div class="bpa-vac-body--custom-fields" v-if="scope.row.custom_fields_values.length > 0">
												<h4 class="bpa-vac__sec-heading"><?php esc_html_e('Custom Fields', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-cf__body">
													<el-row>
														<el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" v-for="custom_fields in scope.row.custom_fields_values">
															<div class="bpa-bd__item">
																<div class="bpa-bd__item-head">
																	<span v-html="custom_fields.label"></span>
																</div>
																<div class="bpa-bd__item-body">
																	<h4 v-html="custom_fields.value"></h4>
																</div>
															</div>																
														</el-col>
													</el-row>
												</div>
											</div>
											<?php do_action('bookingpress_backend_display_guest_data'); ?>
										</el-col>
										<el-col :xs="24" :sm="24" :md="24" :lg="8" :xl="6" v-if="bookingpress_payments == 1">
											<div class="bpa-vac-body--payment-details">
												<h4><?php esc_html_e('Payment Details', 'bookingpress-waiting-list'); ?></h4>
												<div class="bpa-pd__body">
													<div class="bpa-pd__item bpa-pd-method__item">
														<span><?php esc_html_e('Payment Method', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.payment_method_label }}</p>
													</div>
													<div class="bpa-pd__item">
														<span><?php esc_html_e('Status', 'bookingpress-waiting-list'); ?></span>
														<p :class="((scope.row.appointment_status == '2') ? 'bpa-cl-pt-orange' : '') || (scope.row.appointment_status == '3' ? 'bpa-cl-black-200' : '') || (scope.row.appointment_status == '1' ? 'bpa-cl-pt-blue' : '') || (scope.row.appointment_status == '4' ? 'bpa-cl-danger' : '') || (scope.row.appointment_status == '5' ? 'bpa-cl-pt-brown' : '') || (scope.row.appointment_status == '6' ? 'bpa-cl-pt-main-green' : '')">{{ scope.row.appointment_status_label }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_deposit_amt != '0'">
														<span><?php esc_html_e('Deposit', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.bookingpress_deposit_amt_with_currency }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_tax_amt != '0' && (scope.row.price_display_setting != 'include_taxes' || (scope.row.price_display_setting == 'include_taxes' && scope.row.display_tax_amount_in_order_summary == 'true' ) )">
														<span><?php esc_html_e('Tax', 'bookingpress-waiting-list'); ?></span>
														<p>{{ scope.row.bookingpress_tax_amt_with_currency }}</p>
													</div>
													<div class="bpa-pd__item" v-if="scope.row.bookingpress_applied_coupon_code != ''">
														<span><?php esc_html_e('Coupon', 'bookingpress-waiting-list'); ?> ( {{ scope.row.bookingpress_applied_coupon_code }} )</span>
														<p>{{ scope.row.bookingpress_coupon_discount_amt_with_currency }}</p>
													</div>
													<?php do_action('bookingpress_modify_payment_appointment_section') ?>
													<div class="bpa-pd__item bpa-pd-total__item">
														<span>
															<?php esc_html_e('Total Amount', 'bookingpress-waiting-list'); ?>
															<div class="bpa-vac-pd-total__tax-include-label" v-if="scope.row.price_display_setting == 'include_taxes'">{{ scope.row.included_tax_label }}</div>
														</span>
														<p class="bpa-cl-pt-main-green">{{ scope.row.bookingpress_final_total_amt_with_currency }}</p>
													</div>
												</div>									
											</div>
										</el-col>
									</el-row>										
								</div>
							</div>
						</template>
					</el-table-column>
					<el-table-column type="selection"></el-table-column>
					<el-table-column>
						<template slot-scope="scope">
							<div class="bpa-ap-item__mob">
								<div class="bpa-api--head">
									<h4>{{ scope.row.service_name }}</h4>
									<div class="bpa-api--head-apointment-details">
										<p><span class="material-icons-round">today</span>{{ scope.row.appointment_date }}</p>
										<p><span class="material-icons-round">schedule</span>{{ scope.row.appointment_duration }}</p>
									</div>
								</div>
								<div class="bpa-mpay-item--foot">
									<el-tag v-if="scope.row.is_avaliable_status == 0" class="bpa-front-pill --warning">{{ scope.row.appointment_status_label }}</el-tag>
									<el-tag v-if="scope.row.is_avaliable_status == 1" class="bpa-front-pill"><?php esc_html_e( 'Available', 'bookingpress-waiting-list' ); ?></el-tag>
									<div class="bpa-mpay-fi__actions bpa-mac-fi__actions" v-if="bookingpress_manage_appointment == 1">
										<el-button @click="approveAppointment(scope.$index, scope.row)" class="bpa-btn bpa-btn__small bpa-btn__filled-light">
											<span class="material-icons-round">done</span>
										</el-button>
										<el-popconfirm 
											cancel-button-text='<?php esc_html_e( 'Cancel', 'bookingpress-waiting-list' ); ?>' 
											confirm-button-text='<?php esc_html_e( 'Delete', 'bookingpress-waiting-list' ); ?>' 
											icon="false" 
											title="<?php esc_html_e( 'Are you sure you want to delete this appointment?', 'bookingpress-waiting-list' ); ?>" 
											@confirm="deleteAppointment(scope.$index, scope.row)" 
											confirm-button-type="bpa-btn bpa-btn__small bpa-btn--danger" 
											cancel-button-type="bpa-btn bpa-btn__small">
											<el-button type="text" slot="reference" class="bpa-btn bpa-btn__small bpa-btn__filled-light __danger">
												<span class="material-icons-round">delete</span>
											</el-button>
										</el-popconfirm>
									</div>
								</div>
							</div>
						</template>
					</el-table-column>
				</el-table>
			</div>
		</el-container>
	</el-col>
</el-row>
<div id="bpa-loader-div">
	<el-row type="flex" v-show="items.length == 0 && is_display_loader == '0'">
		<el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
			<div class="bpa-data-empty-view">
				<div class="bpa-ev-left-vector">
					<picture>
						<source srcset="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.webp' ); ?>" type="image/webp">
						<img src="<?php echo esc_url( BOOKINGPRESS_IMAGES_URL . '/data-grid-empty-view-vector.png' ); ?>">
					</picture>
				</div>
				<div class="bpa-ev-right-content">
					<h4><?php esc_html_e( 'No Record Found!', 'bookingpress-waiting-list' ); ?></h4>					
				</div>
			</div>
		</el-col>
	</el-row>
</div>
</div>
<el-container v-if="( bookingpress_manage_appointment == 1 && is_waiting_list == 1) && multipleSelectionWaiting.length > 0" class="bpa-default-card bpa-bulk-actions-card waiting-bpa-bulk-actions-card">
		<el-button class="bpa-btn bpa-btn--icon-without-box bpa-bac__close-icon" @click="closeBulkAction">
			<span class="material-icons-round">close</span>
		</el-button>
		<el-row type="flex" class="bpa-bac__wrapper">
			<el-col class="bpa-bac__left-area" :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
				<span class="material-icons-round">check_circle</span>
				<p>{{ multipleSelectionWaiting.length }}<?php esc_html_e( ' Items Selected', 'bookingpress-waiting-list' ); ?></p>
			</el-col>
			<el-col class="bpa-bac__right-area" :xs="24" :sm="12" :md="12" :lg="12" :xl="12">

					<el-select class="bpa-form-control" v-model="bulk_action" placeholder="<?php esc_html_e( 'Select', 'bookingpress-waiting-list' ); ?>">
						<el-option-group v-for="bulk_option_data in bulk_options" :key="bulk_option_data.label" :label="bulk_option_data.label" :value="bulk_option_data.label" v-if="bulk_option_data.value != 'change_status'">
							<el-option v-for="bulk_action_data in bulk_option_data.bulk_actions" :label="bulk_action_data.text" :value="bulk_action_data.value" v-if="bulk_action_data.value == 'delete' || bulk_action_data.value == 'bulk_action'"></el-option>
						</el-option-group>
					</el-select>					
					
				<el-button @click="waiting_bulk_actions()" class="bpa-btn bpa-btn--primary bpa-btn__medium">
					<?php esc_html_e( 'Go', 'bookingpress-waiting-list' ); ?>
				</el-button>
			</el-col>
		</el-row>
</el-container>

