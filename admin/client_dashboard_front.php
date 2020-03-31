<?php 
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$plugin_url = plugins_url('',  dirname(__FILE__));


echo "<style>
	#oct.oct-client .client_logout .clogout{
		color: ".get_option('octabook_bg_text_color')." !important;
	}
	#oct .oct-button{
		color : ".get_option('octabook_bg_text_color')." !important;
		background-color: ".get_option('octabook_primary_color')." !important;
	}
	
	#oct .oct-button:hover,
	#oct.oct-client .client_top_bar{
		color: ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_secondary_color')." !important;
	}	
	#oct.oct-client #octclient_list .list_wrapper .list_header{
		color: ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-loader .oct-first{
		border: 3px solid ".get_option('octabook_bg_text_color')." !important;
	}
	#oct .oct-loader .oct-second{
		border: 3px solid ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-loader .oct-third{
		border: 3px solid ".get_option('octabook_secondary_color')." !important;
	}
	


</style>";


if(is_user_logged_in()){
global $current_user;
 $current_user = wp_get_current_user();

$booking = new octabook_booking();


$booking->client_id = $current_user->ID;
$current_user_bookings = $booking->get_distinct_bookings_of_client();

if(sizeof((array)$current_user_bookings) > 0) {



	?>
			
<div id="oct" class="oct-wrapper oct-client">
<div class="loader">
	<!-- <div class="loader"><?php //echo __("Loading...","oct");?></div> -->
	<div class="oct-loader">
		<span class="oct-first"></span>
		<span class="oct-second"></span>
		<span class="oct-third"></span>
	</div>
</div>
	<div class="client_top_bar">
		<div class="oct_clients_header">
			<span class="client_name oct-sm-9 np"><?php echo $current_user->display_name; ?></span>
			<span class="client_logout oct-sm-3  text-right np"><a href="<?php echo wp_logout_url(home_url()); ?>" class="clogout"><i class="icon-power icons icon-space"></i><?php echo __('Logout','oct');?></a></span>
		
		</div>
	</div>
	<div class="oct_clients_inner">
		<h3 class="oct-xs-12"><?php echo __('Appointment Details','oct');?></h3>	
		<form type="" method="POST" action="" id="octclient_list" >
			<div class="table-responsive">
				<table id="client-dashboard-table" class="table table-striped table-bordered" cellspacing="0" style="width:99%">
					<thead>
						<tr>
							<th><?php echo __("Order #","oct");?></th>
							<th><?php echo __("Provider Name","oct");?></th>
							<th><?php echo __("Service","oct");?></th>
							<th><?php echo __("Date","oct");?></th>
							<th><?php echo __("Time","oct");?></th>
							<th><?php echo __("Status","oct");?></th>
							<th class="thd-w180"><?php echo __("Action","oct");?></th>
						</tr>
					</thead> <?php 
				foreach($current_user_bookings as $curr_user_booking){
				
					$oct_bookings = new octabook_booking();
                    $provider = new octabook_staff();
                    $service = new octabook_service();
                    $oct_bookings->client_id = $current_user->ID;
					$oct_bookings->order_id = $curr_user_booking->order_id;
                    $order_bookings=$oct_bookings->get_client_bookings_by_order_id();										                  
                    foreach($order_bookings as $client_bookings){      
					
					$booking_dt = date_i18n('Y-m-d H:i:s',strtotime($client_bookings->booking_datetime));
					$curr_dt = date_i18n('Y-m-d H:i:s');
					$date1=strtotime($booking_dt);
					$date2=strtotime($curr_dt);
					$cancelationtime=get_option('octabook_cancellation_buffer_time');
					$diff  = abs($date1 - $date2);
					$remaining_mins   = round($diff / 60);
					
					
					
					if($cancelationtime > $remaining_mins){
					$cancelation_buffer="disabled='disabled'";
					$cancelation_buffer_msg="<a data-toggle='tooltip' class='tooltipLink' 
					data-original-title='You are now unable to cancel appointment'><span class='glyphicon glyphicon-exclamation-sign' style='color:red'></span></a>";
					}else{
						$cancelation_buffer="";
						$cancelation_buffer_msg="";
					}


					
                    $provider->id=$client_bookings->provider_id;
                    $staffinfo = $provider->readOne();            
                    $service->id=$client_bookings->service_id;
                    $service->readOne();
                    if($client_bookings->booking_status=='A' || $client_bookings->booking_status==''){
                        $status= "<span style='color:#46B64A;font-weight:bold;'>Active</span>";
                    }elseif($client_bookings->booking_status=='C'){
                        $status= "<span style='color:#46B64A;font-weight:bold;'>Confirmed</span>";
                    }
                    elseif($client_bookings->booking_status=='R'){
                        $status= "<span style='color:#EE403F;font-weight:bold;'>Rejected</span>";
                    }elseif($client_bookings->booking_status=='CC' || $client_bookings->booking_status=='CS'){
                        $status= "<span style='color:#EE403F;font-weight:bold;'>Cancelled</span>";
                    }
                    if($client_bookings->booking_status=='CC' || $client_bookings->booking_status=='CS' || $client_bookings->booking_status=='R'){ $btnview="disabled=disabled";}else{$btnview="";}
                    ?>
				<tbody>
					<tr>
						<td class="oct_cl_order_data"><?php echo __($client_bookings->order_id,"oct");?></td>
						<td class="oct_cl_provider_data"><?php echo __(stripslashes_deep($staffinfo[0]['staff_name']),"oct");?></td>
						<td class="oct_cl_service_data"><?php echo __(stripslashes_deep($service->service_title),"oct");?></td>
						<td class="oct_cl_provider_data"><?php echo __(date_i18n(get_option('date_format'),strtotime($client_bookings->booking_datetime)),"oct");?></td>
						<td class="oct_cl_provider_data"><?php echo __(date_i18n(get_option('time_format'),strtotime($client_bookings->booking_datetime)),"oct");?></td>
						<td class="oct_cl_status_data" id='st<?php echo $client_bookings->id; ?>'><?php echo __($status,"oct");?></td>	
						<td>
						<?php if($cancelation_buffer=='' && $btnview=='' ){ ?>
						
						<a id="oct-cancel-book<?php echo $client_bookings->id; ?>" class="oct-cancel-book-popover oct-button btn-x-small oct_cl_button oct_client_cancel" data-poid="oct-popover-cancel-book<?php echo $client_bookings->id; ?>" rel="popover" data-placement='left' title="<?php echo __("Cancel reason?","oct");?>"><?php echo __("Cancel","oct");?></a>
						
						<div id="oct-popover-cancel-book<?php echo $client_bookings->id; ?>" style="display: none;">
							<div class="arrow"></div>
							<table class="form-horizontal" cellspacing="0">
								<tbody>
									<tr>
										<td><textarea class="form-control cancel_reason_input_txt nm" id="cancel_reason_txt<?php echo $client_bookings->id;?>" name="" placeholder="<?php echo __("Appointment Cancel Reason","oct");?>"></textarea></td>
									</tr>
									<tr>
										<td>
											<button id="oct_booking_cancel" data-method='CS'  value="Cancel By Service Provider" class="btn btn-success btn-sm oct_client_save_cancel_reason" type="submit" data-curr_client_bookingid = "<?php echo $client_bookings->id; ?>"><?php echo __("Save","oct");?></button>
											
											<a id="oct-close-cancel-appointment-cd-popup" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
										</td>
									</tr>
								</tbody>
							</table>
						</div><!-- end pop up -->
						
						<?php } ?>
						
						<a href="<?php echo $plugin_url;?>/assets/lib/admin_general_ajax.php?general_ajax_action=client_download_invoice&order_id=<?php echo $curr_user_booking->order_id;?>&client_id=<?php echo $current_user->ID; ?>&key=<?php echo 'O'.base64_encode($curr_user_booking->order_id+1247);?>b" class='oct-button btn-x-small oct_cl_button'><i class="icon-cloud-download icons icon-space"></i><?php echo __("Invoice","oct");?></a>
						</td>
						
						
					</tr>
					
					
					
				</div>
				</tbody>
				   <?php
                    }
				} ?>
				</table>
			</div>
			
							
			</div>
		</form>
	</div>
</div>
<?php }else{

	echo __('No Appointment Found','oct');
} }else{
	?>
	<div id="oct" class="oct-wrapper oct-client oct-client-login">
	<div class="loader">
		<!-- <div class="loader"><?php //echo __("Loading...","oct");?></div> -->
		<div class="oct-loader">
			<span class="oct-first"></span>
			<span class="oct-second"></span>
			<span class="oct-third"></span>
		</div>
	</div>
		<div class="oct_clients_inner">
			<div class="oct_client_login_main ">
				<div class="oct_login_inner">
					<h4 class="oct_login_p"><?php echo __('You Must Login to check your appointments','oct'); ?></h4>
					<div class="oct_form_row">
						<label for="oct_client_username_l"><?php echo __('UserName','oct');?></label>
						<input type="text" class="form-control" id="oct_client_username_l" name="oct_client_username" value="" />
						<label id="client_login_username-error"  class="error" ></label>
					</div>
					<div class="oct_form_row">
						<label for="oct_client_password_l"><?php echo __('Password','oct');?></label>
						<input type="password" class="form-control" id="oct_client_password_l" name="oct_client_password" value=""/>
						<label id="client_login_password-error" class="error" ></label>
					</div>
					<div class="oct_form_row">
						<label id="client_login-error" class="error" ></label>
					</div>
					<div class="oct_form_row">
						<button class="oct_client_login oct-button nm"><?php echo __('Login','oct');?> </button> <a target="_blank" class="oct_forgot_pass oct-link" href="<?php echo home_url();?>/wp-login.php?action=lostpassword"><?php echo __('Forgot Password?','oct');?></a>
					</div>
				</div>
			</div>
	</div>
</div>
<?php
} ?>
 <script type="text/javascript">
    var objs_booking_list = {"plugin_path":"<?php echo $plugin_url;?>"};
	var appearance_setting = {"default_country_code":"<?php echo get_option('octabook_default_country_short_code'); ?>"};
</script>
<div id="category_manage_modal"> 
</div>