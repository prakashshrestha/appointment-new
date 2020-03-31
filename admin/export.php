<?php 
	include(dirname(__FILE__).'/header.php');
	$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
	
	/* Create Location */
	$location = new octabook_location();
	$category = new octabook_category();
	$staff = new octabook_staff();
	$service = new octabook_service();
	$general = new octabook_general();
	$bookings = new octabook_booking();
	$clients = new octabook_clients();
	$oct_multilocation = get_option('octabook_multi_location');
	
	if(isset($_SESSION['oct_all_loc_export']) && $_SESSION['oct_all_loc_export']=='Y'){
		$oct_export_location = 'All';
	}else{
		$oct_export_location = $_SESSION['oct_location'];
	}
	/* Staff Filter Dropdown Content */
	$staff->location_id = $oct_export_location;
	$oct_all_staff = $staff->readAll_with_disables('Export');
	/* Service Filter Dropdown Content */
	$service->location_id = $oct_export_location;
	$oct_allservices = $service->readAll('Export');
	/* Get All Categories */
	$category->location_id = $oct_export_location;
	$all_categories = $category->readAll('Export');
	
	/* Read All Booking of Location */
	$bookings->location_id = $oct_export_location;
	$all_bookings = $bookings->readAll('','','','','Export');
	/* Get All Locations Info */
	$oct_locations = $location->readAll('','','');
?>
<div id="oct-export-details" class="panel tab-content">
	<div class="panel panel-default">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#booking-info-export"><?php echo __("Booking Information","oct");?></a></li>
			<li><a data-toggle="tab" href="#staff-info-export"><?php echo __("Staff Information","oct");?></a></li>
			<li><a data-toggle="tab" href="#services-info-export"><?php echo __("Services Information","oct");?></a></li>
			<li><a data-toggle="tab" href="#category-info-export"><?php echo __("Category Information","oct");?></a></li>
			<?php if($oct_multilocation=='E'){ ?>
			<?php if(current_user_can('manage_options')){?>
			<li><a data-toggle="tab" href="#location-info-export"><?php echo __("Locations Information","oct");?></a></li><?php } ?><?php } ?>
			<?php if(current_user_can('manage_options')){?>
			<li class="pull-right">
				<div class="oct-custom-checkbox">
					<ul class="oct-checkbox-list">
						<li>
							<input <?php if(isset($_SESSION['oct_all_loc_export']) && $_SESSION['oct_all_loc_export']=='Y'){ echo "checked='checked'"; } ?> type="checkbox" id="oct_all_exportdata" />
							<label for="oct_all_exportdata"><?php echo __("All Locations Export","oct");?> <span></span></label>
						</li>
					</ul>
				</div>
			</li><?php } ?>			
		</ul>
		
		<div class="tab-content">
			<!-- booking infomation export -->
			<div id="booking-info-export" class="tab-pane fade in active">
				<h3><?php echo __("Booking Information","oct");?></h3>
				<div id="accordion" class="panel-group">
					
					<form id="" name="" class="" method="post">
						
						<div class="col-md-4 col-sm-6 col-xs-12 col-lg-4 mb-10">
							<label><?php echo __("Select option to show bookings","oct");?></label>
							<div id="oct_reportrange" class="form-control" >
								<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
								<span></span> <i class="fas fa-caret-down"></i>
							</div>
							<input type="hidden" id="oct_booking_startdate" value="" />
							<input type="hidden" id="oct_booking_enddate" value="" />
							
						</div>
							
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
							<label><?php echo __("Select Service","oct");?></label><br />
						
							<select id="oct_booking_service" class="selectpicker" data-size="10" style="display: none;" data-live-search="true">
								<option value=""><?php echo __("All Services","oct");?></option>
								<?php foreach($all_categories as $oct_category){ 
									  $service->service_category = $oct_category->id;
									  $oct_services = $service->readAll_category_services(); ?>
								<optgroup label="<?php echo $oct_category->category_title;?>"> 	
								<?php foreach($oct_services as $oct_service){ ?>							
									<option value="<?php echo $oct_service->id; ?>"><?php echo $oct_service->service_title; ?></option>
								<?php } ?>
								</optgroup> 
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">		
							<label><?php echo __("Staff member","oct");?></label><br />
							<select id="oct_booking_staff" class="selectpicker mb-10" data-size="10" style="display: none;">
								<option value=""><?php echo __("All staff members","oct");?></option>
								<?php foreach($oct_all_staff as $oct_staff){ ?>
								<option value="<?php echo $oct_staff['id']; ?>"><?php echo $oct_staff['staff_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-2 col-sm-6 col-xs-12 col-lg-2 mb-10">
							<button type="button" id="oct_filtered_bookings" class="form-group btn btn-info oct-btn-width oct-submit-btn mt-20" name=""><?php echo __("Submit","oct");?></button>
						</div>
						<hr id="hr" />
						<div class="table-responsive">
							<table id="oct_export_bookings" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
								<thead>
									<tr>	
										<th><?php echo __("#","oct");?></th>
										<th><?php echo __("Service","oct");?></th>
										<th><?php echo __("Provider","oct");?></th>
										<th><?php echo __("App. Date","oct");?></th>
										<th><?php echo __("App. Time","oct");?></th>
										<th><?php echo __("App. Price","oct");?></th>
										<th><?php echo __("Customer","oct");?></th>
										<th><?php echo __("Phone","oct");?></th>
										<th><?php echo __("Status","oct");?></th>
									</tr>
								</thead>
								<tbody id="oct_export_bookings_data">
									<?php foreach($all_bookings as $single_booking){ 
										/* Staff Info */
										$staff->id=$single_booking->provider_id;
										$staff_info = $staff->readOne();
										/* Service Info */										
										$service->id=$single_booking->service_id;
										$service->readOne(); 
										/* Client Info */	
										$clients->order_id=$single_booking->order_id;
										$client_info = $clients->get_client_info_by_order_id();
																	
										?>
									<tr>	
										<td><?php echo $single_booking->order_id;?></td>
										<td><?php echo __(stripslashes_deep($service->service_title),"oct");?></td>
										<td><?php echo __(stripslashes_deep($staff_info[0]['staff_name']),"oct");?></td>
										<td><?php echo __(date_i18n(get_option('date_format'),strtotime($single_booking->booking_datetime)),"oct");?></td>
										<td><?php echo __(date_i18n(get_option('time_format'),strtotime($single_booking->booking_datetime)),"oct");?> <?php echo __('to',"oct");?> <?php echo __(date_i18n(get_option('time_format'),strtotime($single_booking->booking_endtime)),"oct");?></td>
										<td><?php echo $general->oct_price_format($single_booking->booking_price);?></td>
										<td><?php echo __(stripslashes_deep($client_info[0]->client_name),"oct");?></td>
										<td><?php if($client_info[0]->client_phone!=''){ echo $client_info[0]->client_phone;} else{echo '-';} ?></td>
										<td><?php if($single_booking->booking_status=='C'){  echo __('Confirmed',"oct"); }
										if($single_booking->booking_status=='R'){  echo __('Rejected',"oct"); }
										if($single_booking->booking_status=='CC'){  echo __('Cancelled by client',"oct"); }
										if($single_booking->booking_status=='A' || $single_booking->booking_status=='' ){ echo __('Active',"oct"); }
										if($single_booking->booking_status=='CS'){ echo __('Cancelled by service provider',"oct");}
										if($single_booking->booking_status=='CO'){ echo __('Completed',"oct"); $booking_st = ''; }
										if($single_booking->booking_status=='MN'){ echo __('Marked as No-Show',"oct"); }
										if($single_booking->booking_status=='RS'){ echo __('Rescheduled',"oct"); }										?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>	
						</div>	
					</form>	
				</div>
			</div>
			<!-- service provicer information export -->
			<div id="staff-info-export" class="tab-pane fade">
				<h3><?php echo __("Staff Information","oct");?></h3>
				<div id="accordion" class="panel-group">
					
					<form id="" name="" class="" method="post">
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
							<label><?php echo __("Select Staff to export","oct");?></label><br />
						
							<select id="oct_staff_filter" class="selectpicker" data-size="10" style="display: none;" data-live-search="true">
								<option value=""><?php echo __("All staff members","oct");?></option>
								<?php foreach($oct_all_staff as $oct_staff){ ?>
								<option value="<?php echo $oct_staff['staff_name']; ?>"><?php echo $oct_staff['staff_name']; ?></option>
								<?php } ?>
							</select>
						</div>
						<hr id="hr" />
						<div class="table-responsive">
							<table id="staff-info-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
								<thead>
									<tr>	
										<th><?php echo __("#","oct");?></th>
										<th><?php echo __("Name","oct");?></th>
										<th><?php echo __("Email","oct");?></th>
										<th><?php echo __("Phone","oct");?></th>
										<th><?php echo __("Schedule Type","oct");?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($oct_all_staff as $oct_staff){ ?>
									<tr>
										<td><?php echo $oct_staff['id']; ?></td>
										<td><?php echo $oct_staff['staff_name']; ?></td>
										<td><?php echo $oct_staff['email']; ?></td>
										<td><?php echo $oct_staff['phone']; ?></td>
										<td><?php if($oct_staff['schedule_type']=='W'){ echo __('Weekly',"oct"); }else{ echo __('Monthly',"oct"); }  ?></td>
									</tr>	
								<?php } ?>	
								</tbody>
							</table>
						</div>	
					</form>	
				</div>
			</div>
			<!-- services  infomation export -->
			<div id="services-info-export" class="tab-pane fade">
				<h3><?php echo __("Services Information","oct");?></h3>
				<div id="accordion" class="panel-group">
					<form id="" name="" class="" method="post">
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">		
							<label><?php echo __("Select service to export","oct");?></label><br />
							<select id="oct_service_filter" class="selectpicker mb-10" data-size="10" style="display: none;">
								<option value=""><?php echo __("All services","oct");?></option>
								<?php foreach($all_categories as $oct_category){ 
									  $service->service_category = $oct_category->id;
									  $oct_services = $service->readAll_category_services(); ?>
								<optgroup label="<?php echo $oct_category->category_title;?>"> 	
								<?php foreach($oct_services as $oct_service){ ?>							
									<option value="<?php echo $oct_service->service_title; ?>"><?php echo $oct_service->service_title; ?></option>
								 
								<?php } ?>
								</optgroup> 
								<?php }?>
							</select>
						</div>
						<hr id="hr" />
						<div class="table-responsive">
							<table id="services-info-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
								<thead>
									<tr>	
										<th><?php echo __("#","oct");?></th>
										<th><?php echo __("Service Title","oct");?></th>
										<th><?php echo __("Service Category","oct");?></th>
										<th><?php echo __("Duration","oct");?></th>
										<th><?php echo __("Price","oct");?></th>
										<th><?php echo __("Offered Price","oct");?></th>
										<th><?php echo __("Description","oct");?></th>
										
									</tr>
								</thead>
								<tbody>
								<?php foreach($oct_allservices as $oct_singleservice){
											$category->id=$oct_singleservice->category_id;
											$category->readOne(); ?>
									<tr>	
										<td><?php echo $oct_singleservice->id;?></td>
										<td><?php echo $oct_singleservice->service_title;?></td>
										<td><?php echo $category->category_title;?></td>
										<td><?php if(floor($oct_singleservice->duration/60)!=0){ echo floor($oct_singleservice->duration/60); echo __(" Hours","oct"); } ?>  <?php  if($oct_singleservice->duration%60 !=0){ echo $oct_singleservice->duration%60; echo __(" Mintues","oct");} ?></td>
										<td><?php echo $general->oct_price_format($oct_singleservice->amount);?></td>
										<td><?php if($oct_singleservice->offered_price>0){ echo $general->oct_price_format($oct_singleservice->offered_price);}else{ echo '-';}?></td>
										<td><?php echo $oct_singleservice->service_description;?></td>
									</tr>
								<?php } ?>		
								</tbody>
							</table>	
						</div>	
					</form>	
				</div>
			</div>
			<!-- category infomation export -->
			<div id="category-info-export" class="tab-pane fade">
				<div id="accordion" class="panel-group">
					<form id="" name="" class="" method="post">
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
							<label><?php echo __("Select Category to export","oct");?></label><br />
						
							<select id="oct_category_filter" class="selectpicker" data-size="10" style="display: none;" data-live-search="true">
								<option value=""><?php echo __("All Categories members","oct");?></option>
								<?php  foreach($all_categories as $oct_category){  ?>
								<option value="<?php echo $oct_category->category_title;?>"><?php echo $oct_category->category_title;?></option>
								<?php } ?>
							</select>
						</div>
						<hr id="hr" />
						<div class="table-responsive">
							<table id="category-info-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
								<thead>
									<tr>	
										<th><?php echo __("#","oct");?></th>
										<th><?php echo __("Category Title","oct");?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($all_categories as $oct_category){ ?>
									<tr>
										<td><?php echo $oct_category->id;?></td>
										<td><?php echo $oct_category->category_title;?></td>
									</tr>
								<?php } ?>					
								</tbody>
							</table>
						</div>	
					</form>	
				</div>
			</div>
			<!-- Locations infomation export -->
			<?php if($oct_multilocation=='E' && current_user_can('manage_options')){ ?>
			<div id="location-info-export" class="tab-pane fade">
				<div id="accordion" class="panel-group">
					<form id="" name="" class="" method="post">
						<div class="col-md-3 col-sm-6 col-xs-6 col-lg-3 mb-10">
							<label><?php echo __("Select Location to export","oct");?></label><br />
						
							<select id="oct_location_filter" class="selectpicker" data-size="10" style="display: none;" data-live-search="true">
								<option value=""><?php echo __("All Locations members","oct");?></option>
								<?php foreach($oct_locations as $oct_location){  ?> 
								<option value="<?php echo $oct_location->location_title;?>"><?php echo $oct_location->location_title;?></option>
								<?php } ?>
							</select>
						</div>
						<hr id="hr" />
						<div class="table-responsive">
							<table id="location-info-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
								<thead>
									<tr>	
										<th><?php echo __("#","oct");?></th>
										<th><?php echo __("Title","oct");?></th>
										<th><?php echo __("Description","oct");?></th>
										<th><?php echo __("Email","oct");?></th>
										<th><?php echo __("Phone","oct");?></th>
										<th><?php echo __("Address","oct");?></th>
										<th><?php echo __("City","oct");?></th>
										<th><?php echo __("Zip","oct");?></th>
										<th><?php echo __("State","oct");?></th>
										<th><?php echo __("Country","oct");?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($oct_locations as $oct_location){  ?>
									<tr>
										<td><?php echo $oct_location->id;?></td>
										<td><?php echo $oct_location->location_title;?></td>
										<td><?php echo $oct_location->description;?></td>
										<td><?php echo $oct_location->email;?></td>
										<td><?php echo $oct_location->phone;?></td>
										<td><?php echo $oct_location->address;?></td>
										<td><?php echo $oct_location->city;?></td>
										<td><?php echo $oct_location->zip;?></td>
										<td><?php echo $oct_location->state;?></td>
										<td><?php echo $oct_location->country;?></td>
									</tr>
								<?php } ?>					
								</tbody>
							</table>
						</div>	
					</form>	
				</div>
			</div>
			<?php } ?>
			
		</div>
	</div>	
</div>	


 
		
<?php 
	include(dirname(__FILE__).'/footer.php');
?>
