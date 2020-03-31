<?php    

include(dirname(__FILE__).'/header.php');
$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));


$plugin_url = plugins_url('',  dirname(__FILE__));

$site_url = site_url();

$client_booking_redirect_url = get_option("octabook_booking_page");

if($client_booking_redirect_url == ""){
	wp_redirect( site_url()."/oct-bookings/", 301 );
}else{
	wp_redirect( $client_booking_redirect_url, 301 );
}

	global $current_user;
	$current_user = wp_get_current_user();
	
	$location = new octabook_location();
	$category = new octabook_category();
	$staff = new octabook_staff();
	$service = new octabook_service();
	$general = new octabook_general();
	$bookings = new octabook_booking();
	$clients = new octabook_clients();
	$loyalty_points = new octabook_loyalty_points();
	$oct_multilocation = get_option('octabook_multi_location');
	
	$curr_bal = 0;
	$loyalty_points->client_id =  $current_user->ID;
	$loyalty_points->get_client_balance();
	if(isset($loyalty_points->balance) && $loyalty_points->balance!=''){
		$curr_bal = $loyalty_points->balance;
	}	

$bookings->client_id=$current_user->ID;
$current_user_bookings=$bookings->get_distinct_bookings_of_client();	
$total_rows = sizeof((array)$current_user_bookings);
if($total_rows > 0){ ?>
<div id="oct-user-appointments">

	<div class="panel-body">	
		<div class="tab-content">
			<h4 class="header4"><?php echo __("My Appointments","oct");?>
			<!-- <span class="pull-right header3"><?php //echo $curr_bal;?> : <?php //echo __("Loyalty Points","oct");?></span> --> <span><a class="btn btn-default" href="<?php echo site_url();?>">Back to site</a></span></h4>
		<form>
					<div class="table-responsive">
						<table id="user-profile-booking-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th><?php echo __("Order #","oct");?></th>
									<th><?php echo __("Order Date","oct");?></th>
									<th><?php echo __("Order Time","oct");?></th>
									<th><?php echo __("Show All Bookings","oct");?></th>
									<th><?php echo __("Actions","oct");?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							
							for($i=0;$i<=$total_rows-1;$i++){	
									$bookings->client_id = $current_user->ID;
									$bookings->order_id = $current_user_bookings[$i]->order_id;
									$order_bookings = $bookings->get_client_bookings_by_order_id();
									?>								
										<tr data-oid="<?php echo ($current_user_bookings[$i]->order_id);?>">
										<td><?php echo __($current_user_bookings[$i]->order_id,"oct");?></td>
										<td><?php echo date_i18n(get_option('date_format'),strtotime($current_user_bookings[$i]->lastmodify));?></td>
										<td><?php echo date_i18n(get_option('time_format'),strtotime($current_user_bookings[$i]->lastmodify));?></td>
										<td>
										
											<a href="#user-booking-details" data-client_id="<?php echo $current_user->ID;?>" data-order_id="<?php echo ($current_user_bookings[$i]->order_id);?>" data-toggle="modal" data-target="#user-booking-details" class="oct-my-booking-user btn btn-info octabook_client_bookings"><i class="fa fa-eye icon-space"></i><?php echo __("My Bookings","oct");?> <span class="badge br-10"><?php echo sizeof((array)$order_bookings);?></span></a>
										</td>	
										<td>
										
											<a href="<?php echo $plugin_url_for_ajax;?>/assets/lib/admin_general_ajax.php?general_ajax_action=client_download_invoice&order_id=<?php echo ($current_user_bookings[$i]->order_id);?>&client_id=<?php echo $current_user->ID;?>&key=<?php echo 'O'.base64_encode($current_user_bookings[$i]->order_id+1247);?>b" class="btn btn-primary"><i class="fa fa-download icon-space"></i><?php echo __("Download Invoice","oct");?></a>
										</td>
								</tr>
									<?php } ?>															
							</tbody>
						</table>
					</div>	
				<div id="user-booking-details" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
					<div>
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close oct_client_bookingclose" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title"><?php echo __("My Bookings","oct");?></h4>
								</div>
								<div class="modal-body">
									<div class="table-responsive">
										<table id="user-all-bookings-details" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th><?php echo __("Order#","oct");?></th>
													<th><?php echo __("Provider","oct");?></th>
													<th><?php echo __("Service","oct");?></th>
													<th  width="155px;"><?php echo __("Booking Date & Time","oct");?></th>
													<th><?php echo __("Status","oct");?></th>
													<th><?php echo __("Status Note","oct");?></th>
													<th width="140px;"><?php echo __("Action","oct");?></th>
												</tr>
													
											</thead>											
											<tbody id="oct_client_orderbookings"></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				
				</div>
				</form>
		</div>
	</div>
<?php }else{ ?> 
<div><?php echo __("No Appointment Found.","oct");?></div>
<?php 
}
	include(dirname(__FILE__).'/footer.php');
?>
