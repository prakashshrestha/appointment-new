<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
include_once "header.php";
global $wpdb;
$page_title = "Clients";

$plugin_url_for_ajax = plugins_url('',  dirname(__FILE__));
$user_ID = get_current_user_id();


$oct_bookings = new octabook_booking();
$clients = new octabook_clients();

if(isset($_SESSION['oct_all_loc_clients']) && $_SESSION['oct_all_loc_clients']=='Y'){
	$clients->location_id = 'All';
	$all_clients_info = $clients->get_registered_clients();
}else{
	$clients->location_id = $_SESSION['oct_location'];
	/* $all_clients_info = get_users( array( 'role' => 'oct_users' ,'meta_key' => 'oct_client_locations' ,'meta_value' => '#'.$_SESSION['oct_location'].'#')); */
	$all_clients_info = $clients->get_all_registered_clients_by_location_id($_SESSION['oct_location']);
}
/** Code For Guest User **/
$all_guesuser_info = $clients->get_all_guest_users_orders();
?>
<div id="oct-customers-listing" class="panel tab-content">
	<div class="panel panel-default">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#registered-customers-listing"><?php echo __("Registered Customers","oct");?></a></li>
			<li><a data-toggle="tab" href="#guest-customers-listing"><?php echo __("Guest Customers","oct");?></a></li>
			<?php if(current_user_can('manage_options')){ ?>
			<li class="pull-right">
				<div class="oct-custom-checkbox">
					<ul class="oct-checkbox-list">
						<li>
							<input <?php if(isset($_SESSION['oct_all_loc_clients']) && $_SESSION['oct_all_loc_clients']=='Y'){ echo "checked='checked'"; } ?> type="checkbox" id="oct_all_locations_customers" />
							<label for="oct_all_locations_customers" style="margin-right: 15px;"><?php echo __("All Locations Customers","oct");?><span></span></label>
						</li>
					</ul>
				</div>
			</li><?php } ?>
		</ul>
		<div class="tab-content">
			<div id="registered-customers-listing" class="tab-pane fade in active">
				<h3><?php echo __("Registered Customers","oct");?></h3>  <div class=""></div>
					<div id="accordion" class="panel-group">
						<table id="registered-client-table" class="display responsive nowrap table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th><?php echo __("Client Name","oct");?></th>
										<th><?php echo __("Email","oct");?></th>
										<th><?php echo __("Phone","oct");?></th>
										<th class="thd-w200"><?php echo __("Bookings","oct");?></th>
									</tr>
								</thead>
								<tbody id="oct_registered_list" >
									<?php 
										$oct_bookings = new octabook_booking(); 
										foreach($all_clients_info as $client_info){
												$oct_bookings->location_id=$_SESSION['oct_location'];
												$oct_bookings->client_id = $client_info->ID;
												$all_booking = $oct_bookings->get_client_all_bookings_by_client_id();
												
												if(sizeof((array)$all_booking)>0){
													$allboking = sizeof((array)$all_booking)-1;
													$clientlastoid = $all_booking[$allboking]->order_id;
													$clients->order_id = $clientlastoid;
													$order_client_info = $clients->get_client_info_by_order_id();
												}
											?>
												<tr id="client_detail<?php echo $client_info->ID; ?>">
													<td><?php echo __(stripslashes_deep($client_info->display_name),"oct");?></td>
													<td><?php echo __($client_info->user_email,"oct");?></td>
													<td><?php echo __($client_info->client_phone,"oct");?></td>
																
													
													
												<td class="oct-bookings-td">
													<a class="btn btn-primary oct_show_bookings " data-method="registered" data-client_id='<?php echo $client_info->ID; ?>' href="#registered-details" data-toggle="modal"><i class="icon-calendar icons icon-space"></i> <?php echo __("Bookings","oct");?><span class="badge"><?php echo sizeof((array)$all_booking); ?></span></a>
																										
													<a data-poid="popover-delete-reg-client<?php echo $client_info->ID;?>" class="col-sm-offset-1 btn btn-danger oct-delete-popover " rel="popover" data-placement='bottom' title="<?php echo __("Delete this Client?","oct");?>"> <i class="fa fa-trash icon-space " title="<?php echo __("Delete Client","oct");?>"></i><?php echo __("Delete","oct");?></a>
													<div id="popover-delete-reg-client<?php echo $client_info->ID;?>" style="display: none;">
														<div class="arrow"></div>
														<table class="form-horizontal" cellspacing="0">
															<tbody>
																<tr>
																	<td>
																		<button data-method="registered" data-client_id="<?php echo $client_info->ID;?>" value="Delete" class="btn btn-danger btn-sm oct_delete_client" type="submit"><?php echo __("Yes","oct");?></button>
																		<button class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>											
													</td>
												</tr>
										   <?php  } ?>
								</tbody>
							</table>
						
						<div id="registered-details" class="modal fade booking-details-modal">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title"><?php echo __("Registered Customers Bookings","oct");?></h4>
									</div>
									<div class="modal-body">
										<div class="table-responsive"> 
										<table id="registered-client-booking-details"  class="display responsive table table-striped table-bordered" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th style="width: 9px !important;"><?php echo __("#","oct");?></th>
														<th style="width: 48px !important;"><?php echo __("Client Name","oct");?></th>
														<th style="width: 67px !important;"><?php echo __("Service Provider","oct");?></th>
														<th style="width: 73px !important;"><?php echo __("Service","oct");?></th>
														<th style="width: 44px !important;"><?php echo __("Appt. Date","oct");?></th>
														<th style="width: 44px !important;"><?php echo __("Appt. Time","oct");?></th>
														<th style="width: 39px !important;"><?php echo __("Status","oct");?></th>
														<th style="width: 70px !important;"><?php echo __("Payment Method","oct");?></th>
														<th style="width: 257px !important;"><?php echo __("More Details","oct");?></th>
													</tr>
												</thead>
												<tbody id="oct_client_bookingsregistered"></tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				
			</div>
			<div id="guest-customers-listing" class="tab-pane fade">
				<h3><?php echo __("Guest Customers","oct");?></h3>
				<div id="accordion" class="panel-group">

					<table id="guest-client-table" class="display responsive table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								
								<th><?php echo __("Client Name","oct");?></th>
								<th><?php echo __("Email","oct");?></th>
								<th><?php echo __("Phone","oct");?></th>
								<th class="thd-w200"><?php echo __("Bookings","oct");?></th>
								
							</tr>
						</thead>
						<tbody id="oct_guest_list">
							
							<?php foreach($all_guesuser_info as $client_info){						
									$oct_bookings->order_id = $client_info->order_id;				
									$all_booking=$oct_bookings->get_guest_users_booking_by_order_id();
								?>
									<tr id="client_detail<?php echo $client_info->order_id; ?>">
										<td><?php echo __(stripslashes_deep($client_info->client_name),"oct");?></td>
										<td><?php echo __($client_info->client_email,"oct");?></td>
										<td><?php echo __($client_info->client_phone,"oct");?></td>
																
										<td class="oct-bookings-td"> 
										<a class="btn btn-primary oct_show_bookings" data-method="guest" data-client_id='<?php echo $client_info->order_id; ?>' href="#guest-details" data-toggle="modal"><i class="icon-calendar icons icon-space"></i><?php echo __("Bookings","oct");?><span class="badge"><?php echo sizeof((array)$all_booking); ?></span></a>
																										
										<a data-poid="popover-delete-guest-client<?php echo $client_info->order_id; ?>" class="col-sm-offset-1 btn btn-danger oct-delete-popover" rel="popover" data-placement='bottom' title="<?php echo __("Delete this Client?","oct");?>"> <i class="fa fa-trash icon-space " title="<?php echo __("Delete Client","oct");?>"></i><?php echo __("Delete","oct");?></a>
										
										<div id="popover-delete-guest-client<?php echo $client_info->order_id; ?>" style="display: none;">
											<div class="arrow"></div>
											<table class="form-horizontal" cellspacing="0">
												<tbody>
													<tr>
														<td>
															<button data-method="guest" data-client_id="<?php echo $client_info->order_id;?>" value="Delete" class="btn btn-danger btn-sm oct_delete_client" type="submit"><?php echo __("Yes","oct");?></button>
															<button class="btn btn-default btn-sm oct-close-popover-delete" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>									
									</tr>
									   <?php  } ?>
							</tbody>
					</table>
						
					<div id="guest-details" class="modal fade booking-details-modal">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title"><?php echo __("Guest Customers Bookings","oct");?></h4>
								</div>
								<div class="modal-body">
									<div class="table-responsive">
										<div class="table-responsive"> 
										<table id="guest-client-booking-details" class="display responsive table table-striped table-bordered" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th style="width: 9px !important;"><?php echo __("#","oct");?></th>
													<th style="width: 48px !important;"><?php echo __("Client Name","oct");?></th>
													<th style="width: 67px !important;"><?php echo __("Service Provider","oct");?></th>
													<th style="width: 73px !important;"><?php echo __("Service","oct");?></th>
													<th style="width: 44px !important;"><?php echo __("Appt. Date","oct");?></th>
													<th style="width: 44px !important;"><?php echo __("Appt. Time","oct");?></th>
													<th style="width: 39px !important;"><?php echo __("Status","oct");?></th>
													<th style="width: 70px !important;"><?php echo __("Payment Method","oct");?></th>
													<th style="width: 257px !important;"><?php echo __("More Details","oct");?></th>
												</tr>
											</thead>
											<tbody id="oct_client_bookingsguest">
											</tbody>
										</table>
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
<?php 
	include_once "footer.php";
?>
 <script type="text/javascript">
   var ob_client_listing = {"plugin_path":"<?php echo $plugin_url_for_ajax;?>",   "message_deleteclient":"<?php echo __("Booking(s) for this client will be deleted as well, Do you want to delete it?")?>",   "message_recdelete":"<?php echo __("Record deleted!")?>"   };
</script>