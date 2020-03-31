<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

include(dirname(__FILE__).'/header.php');
$plugin_url_for_ajax = plugins_url('',  dirname(__FILE__));

$staff = new octabook_staff();
$service = new octabook_service();
$category = new octabook_category();
$clients = new octabook_clients();
$oct_bookings = new octabook_booking();

/* Get Service info */
$serviceprice='';
$serviceduration='';
$serviceduration_val='';
$servicecounter = 0;
$category->location_id = $_SESSION['oct_location'];
$all_categories = $category->readAll();
/* Get Provider Info */
$staff->location_id = $_SESSION['oct_location'];
$oct_all_staff = $staff->readAll_with_disables();
/* Get Register Clients */
$clients->location_id = $_SESSION['oct_location'];
$all_clients_info = $clients->get_registered_clients();
foreach($all_clients_info as $client)
{
	$oct_bookings->client_id=$client->ID;
	$oct_bookings->get_register_client_last_order_id();
	$clients->order_id=$oct_bookings->order_id;
	$client_info = $clients->get_client_info_by_order_id();
	$client->phone = $client_info[0]->client_phone;
}

?>
<script type="text/javascript">
    var calenderObj = {"plugin_path":"<?php echo $plugin_url_for_ajax;?>","ak_wp_lang":"<?php echo $ak_wplang[0]; ?>",'cal_first_day':"<?php echo get_option('start_of_week'); ?>",
	'time_format':"<?php echo $wpTimeFormat; ?>"};
</script>
	
<div id="oct-calendar-all">
	<div class="panel-body">
	<div class="ct-legends-main">
        <div class="ct-legends-inner col-md-12">
            <h4><?php echo __("Legends","oct");?>:</h4>
            <ul class="list-inline">
            				
                <li><i class="fas fa-check txt-success icon-space"></i><?php echo __("Confirmed","oct");?></li>
                <li><i class="fas fa-pencil-alt txt-info  icon-space"></i><?php echo __("Rescheduled","oct");?></li>
                <li><i class="fas fa-ban txt-danger icon-space"></i><?php echo __("Rejected","oct");?></li>
                <li><i class="fas fa-times txt-primary icon-space"></i><?php echo __("Cancelled By Client","oct");?></li>
                <li><i class="fas fa-info-circle txt-warning icon-space"></i><?php echo __("Pending","oct");?></li>
								<li><i class="far fa-thumbs-up txt-success icon-space"></i><?php echo __("Appointment Completed","oct");?></li>
                <li><i class="fas fa-thumbs-down txt-danger icon-space"></i><?php echo __("Mark As No Show","oct");?></li>
           </ul>
        </div>
    </div>
	<hr id="hr" />
	<div class="oct-calendar-top-bar">
		<div class="col-md-4 col-sm-6 col-xs-12 col-lg-4 mb-10">
			<label class="custom-width"><?php echo __("Select Option To Show Bookings","oct");?></label>
			<div id="oct_reportrange" class="form-control custom-width" >
				<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
				<span></span> <i class="fas fa-caret-down"></i>
			</div>
			<input type="hidden" id="oct_booking_startdate" value="" />
			<input type="hidden" id="oct_booking_enddate" value="" />
		</div>
			
		<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
			<label><?php echo __("Select Service","oct");?></label><br />
		
			<select id="oct_booking_filterservice" class="selectpicker" data-size="10" style="display: none;" data-live-search="true">
				<option value=""><?php echo __("All Services","oct");?></option>
				<?php foreach($all_categories as $oct_category){ 
					  $service->service_category = $oct_category->id;
					  $oct_services = $service->readAll_category_services(); ?>
				<optgroup label="<?php echo $oct_category->category_title;?>"> 	
				<?php foreach($oct_services as $oct_service){ ?>							
					<option <?php if(isset($_SESSION['oct_booking_filterservice']) && $_SESSION['oct_booking_filterservice']==$oct_service->id){ echo "selected='selected'"; } ?> value="<?php echo $oct_service->id; ?>"><?php echo $oct_service->service_title; ?></option>
				 
				<?php } ?>
				</optgroup> 
				<?php } ?>
			</select>
		</div>
		<?php

		if($user_sp_manager=='Y' || current_user_can('manage_options')){ ?>
		<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">		
			<label><?php echo __("Staff Member","oct");?></label><br />
			<select id="oct_booking_filterprovider" class="selectpicker mb-10" data-size="10" style="display: none;">
				<option value=""><?php echo __("All Service Provider","oct");?></option>
				<?php foreach($oct_all_staff as $oct_staff){ ?>
				<option <?php if(isset($_SESSION['oct_booking_filterstaff']) && $_SESSION['oct_booking_filterstaff']==$oct_staff['id']){ echo "selected='selected'"; } ?>  value="<?php echo $oct_staff['id']; ?>"><?php echo $oct_staff['staff_name']; ?></option>
				<?php } ?>
			</select>
		</div><?php } ?>
		<div class="col-md-2 col-sm-6 col-xs-12 col-lg-2 pull-right mb-10">
			<button type="button" id="oct_filter_appointments" class="form-group btn btn-info oct-btn-width oct-submit-btn mt-20" name=""><?php echo __("Submit","oct");?></button>
		</div>
	</div>
	
	</div>

	<div id="oct_calendar" class=""></div> 
	
	<div id="add-new-booking-details" class="modal fade show-popup" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog modal-md vertical-align-center">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" id="manual_appt" aria-hidden="true">×</button>
						<h4 class="modal-title"><?php echo __("Book Manual Appointment","oct");?></h4>
					</div>
					<div class="modal-body">
						<ul class="nav nav-tabs">
							<li class="active" id="add_app_det"><a data-toggle="tab" href="#add-appointment-details"><?php echo __("Appointment Details","oct");?></a></li>
							<li id="add_cust_det"><a data-toggle="tab" href="#add-customer-details"><?php echo __("Customer Details","oct");?></a></li>
						</ul>
						<div class="tab-content">
							<div id="add-appointment-details" class="tab-pane fade in active">
							<form id="booking_appt_form">
								<table>
									<tbody>
										<tr>
											<td><label><?php echo __("Provider","oct");?></label></td>
											<td>
												<div class="form-group">
													<select id="oct_booking_provider_manual" data-size="5" class="selectpicker form-control" data-live-search="true" data-live-search-placeholder="<?php echo __("Search","oct");?>" data-actions-box="true"  >
														<?php foreach($oct_all_staff as $oct_staff){ ?>
														<option value="<?php echo $oct_staff['id']; ?>"><?php echo $oct_staff['staff_name']; ?></option>
														<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<tr>	
											<td><label><?php echo __("Service","oct");?></label></td>
											<td>
												<div class="form-group">
													<select id="oct_booking_service_manual" data-size="5" class="selectpicker form-control" data-live-search="true" data-live-search-placeholder="<?php echo __("Search","oct");?>" data-actions-box="true"  >
														<?php foreach($all_categories as $oct_category){ 
														  $service->service_category = $oct_category->id;
														  $oct_services = $service->readAll_category_services(); ?>
															<optgroup label="<?php echo $oct_category->category_title;?>"> 	
															<?php foreach($oct_services as $oct_service){ 
															if($servicecounter==0){ 
															$serviceduration_val=$oct_service->duration; 
															$serviceprice=$oct_service->amount; 
															if(floor($oct_service->duration/60)!=0){ $serviceduration .= floor($oct_service->duration/60); $serviceduration .= __(" Hrs","oct"); } 
															if($oct_service->duration%60 !=0){  $serviceduration .= $oct_service->duration%60; $serviceduration .= __(" Mins","oct"); }
															
															} ?>							
																<option value="<?php echo $oct_service->id; ?>"><?php echo $oct_service->service_title; ?></option>
															 
															<?php $servicecounter++; } ?>
															</optgroup> 
															<?php } ?>
													</select>
												</div>
											</td>
										</tr>
										<tr>
									
											<td></td>
											<td>
												<div class="oct-col6 oct-w-50">
													<div class="form-control">
														<span><?php echo $oct_currency_symbol;?></span><span id="oct_service_price_manual"><?php echo $serviceprice;?></span>
													</div>
												</div>	
												<div class="oct-col6 oct-w-50 float-right">
													<div class="form-control">
														<i class="far fa-clock"></i><span id="oct_service_duration_manual"><?php echo $serviceduration;?></span>
														<input type="hidden" id="oct_service_duration_val_manual" value="<?php echo $serviceduration_val;?>"/>
													</div>
												</div>
												
											</td>
										</tr>
										<tr>
											<td><label for="oct-service-duration"><?php echo __("Date & Time","oct");?></label></td>
											<td>
												<div class="oct-col6 oct-w-50">
													<input id="oct_booking_date_manual" class="form-control"/>
												</div>
												<div class="oct-col6 oct-w-50 float-right">
													<select id="oct_booking_time_manual" class="selectpicker" data-size="5" >
													</select>
												</div>
												
											</td>
										</tr>
										<tr>
											<td colspan="2" style="text-align:center;font-weight: bold;display: none;">You have event for following time, still you want to do overlap booking?</td>
										</tr>
										<tr>
											<td><?php echo __("Notes","oct");?></td>
											<td><textarea class="form-control notes" id="oct_booking_note_manual" name="manual_notes"></textarea></td>
										</tr>
											
									</tbody>
								</table>
								
								<div class="modal-footer">
								<div class="oct-col12 oct-footer-popup-btn">
									<div class="col-xs-12 ta-c">
										<a data-toggle="tab" id="customer_add_new" href="#add-customer-details" name="submit" class="btn btn-success oct-next-add-booking" type="submit"><?php echo __("Continue","oct");?></a>
									</div>
								</div>
							</div>
							</form>	
							</div>
							
							
							<div id="add-customer-details" class="tab-pane fade">
								<div class="oct-search-customer-main" id="searchcustomerdiv">
									<div class="search-container">
										<select id="oct_booking_client_manual" class="selectpicker form-control" data-live-search="true" data-live-search-placeholder="<?php echo __("Search","oct");?>" data-actions-box="true"  >
											<option value=""><?php echo __("-- Select customer --","oct");?></option>
											<?php foreach($all_clients_info as $clients_info){ ?>
											<option value="<?php echo $clients_info->ID;?>"><?php echo $clients_info->display_name.' ('.$clients_info->phone.')'; ?></option>
										<?php } ?>
										 </select>
										 <div class="oct-searching-customer" id="loading"><i class="fa fa-circle-o-notch fa-spin"></i><?php echo __("Please Wait...","oct");?></div>
									</div>
								</div>
								<hr id="hr" />
								<div class="new-customer-details" id="">
								
								
								<form id="manual_booking_form" action="" method="post">
									<table>
										<tbody>	
										<?php
										if(get_option('octabook_guest_user_checkout')=='D'){ ?>
										<tr id="client_username">
											<td><?php echo __("Username","oct");?></td>
											<td><input type="text" class="form-control" name="oct_mb_username" id="oct_clientusername_manual" /></td>
										</tr>
										<tr id="client_password">
											<td><?php echo __("Password","oct");?></td>
											<td><input type="password" class="form-control client_pass" name="oct_mb_password" id="oct_clientpassword_manual" /></td>
										</tr>
										<?php } ?>
										
										<tr>
											<td><?php echo __("Name","oct");?></td>
											<td><input type="text" class="form-control client_display" id="oct_clientname_manual" name="oct_mb_clientname" placeholder="<?php echo __("Customer Name","oct");?>" /></td>
										</tr>
										<tr>
											<td><?php echo __("Email","oct");?></td>
											<td><input data-emailtype="N" type="email" class="form-control client_email_dis" name="oct_mb_clientemail" data-value="" id="oct_clientemail_manual" placeholder="andrew@example.com" /></td>
										</tr>
										<tr>
											<td><?php echo __("Phone","oct");?></td>
											<td><input type="tel" class="form-control client_phone_dis phone_number" id="oct_clientphone_manual" name="oct_mb_clientphone" /></td>
										</tr>
										<tr>
											<td><?php echo __("Address","oct");?></td>
											<td>
												<div class="oct-col12"><textarea id="oct_clientaddress_manual" class="form-control" name="oct_mb_clientaddress"></textarea></div>
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<div class="oct-col6 oct-w-50">
													<input type="text" class="form-control" id="oct_clientcity_manual" name="oct_mb_clientcity" placeholder="<?php echo __("City","oct");?>" />
												</div>
												<div class="oct-col6 oct-w-50 float-right">
													<input type="text" class="form-control" id="oct_clientstate_manual" name="oct_mb_clientstate" placeholder="<?php echo __("State","oct");?>" />
												</div>
											</td>
										</tr>
										<tr>
											<td></td>	
											<td>	
												<div class="oct-col6 oct-w-50">
													<input type="text" class="form-control" id="oct_clientzip_manual" name="oct_mb_clientzip" placeholder="<?php echo __("Zip","oct");?>" />
												</div>	
												<div class="oct-col6 oct-w-50 float-right">
													<input type="text" class="form-control" id="oct_clientcountry_manual" name="oct_mb_clientcountry" placeholder="<?php echo __("Country","oct");?>" />
													
												</div>	
												
											</td>
										</tr>
										</tbody>
									</table>
									
									<div class="modal-footer">
										<div class="oct-col12 oct-footer-popup-btn">
											
											<div class="col-xs-12 ta-c">
												<a id="oct_book_manual_appointment" href="javascript:void(0)" class="btn btn-success"><?php echo __("Book Appointment","oct");?></a>
											</div>
										</div>
									</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- end details of booking -->
		
	</div>
<?php 
	include(dirname(__FILE__).'/footer.php');
?>