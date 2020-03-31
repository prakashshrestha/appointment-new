<?php 
session_start();
$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require_once dirname(dirname(dirname(__FILE__))).'/assets/Twilio/autoload.php'; 
	use Twilio\Rest\Client;
if (file_exists($root.'/wp-load.php')) {
	require_once($root.'/wp-load.php');
}

if ( ! defined( 'ABSPATH' ) ) exit;  /* direct access prohibited  */

	$category = new octabook_category();
	$oct_location = new octabook_location();
	$location = new octabook_location();
	$oct_service = new octabook_service();
	$service = new octabook_service();
	$general = new octabook_general();
	$payments= new octabook_payments();
	$staff = new octabook_staff();
	$oct_staff = new octabook_staff();
	$order_info = new octabook_order();
	$oct_booking = new octabook_booking();
	$oct_bookings = new octabook_booking();
	$provider = new octabook_staff();
	$clients = new octabook_clients();
	$coupons = new octabook_coupons();
	$reviews = new octabook_reviews();
	$email_template = new octabook_email_template();
	$loyalty_points = new octabook_loyalty_points();
	include_once(dirname(dirname(dirname(__FILE__))).'/objects/class_sms_templates.php');
	$obj_sms_template = new octabook_sms_template();
	
	$plugin_url_for_ajax = plugins_url('',dirname(dirname(__FILE__)));
	
	
	/* Email Content Type Header */ 
	function set_content_type() {			
		return 'text/html';		
	}

	function update_google_cal_event($calendarId,$provider_access_token,$eventid,$date,$start,$end,$providerTZ,$GcclientID,$GcclientSecret,$GcEDvalue){
		$clientP = new Google_Client();
		$clientP->setApplicationName("OctaBook Google Calender");
		$clientP->setClientId($GcclientID);
		$clientP->setClientSecret($GcclientSecret);
		$clientP->setRedirectUri(get_option('oct_gc_admin_url'));
		$clientP->setDeveloperKey($GcclientID);
		$clientP->setScopes('https://www.googleapis.com/auth/calendar');
		$clientP->setAccessType('offline');
		$calP = new Google_CalendarService($clientP); 

		$clientP->setAccessToken($provider_access_token);
		$accesstoken = json_decode($provider_access_token);
		if($provider_access_token){
			if ($clientP->isAccessTokenExpired()) {
				$clientP->refreshToken($accesstoken->refresh_token);
			}
		}
		if ($clientP->getAccessToken()){
			$eventP = new Google_Event();      
			$startTP = new Google_EventDateTime();
			$endTP = new Google_EventDateTime();
			$eventd = $calP->events->get($calendarId,$eventid);
			$location = '';
			$summary = $eventd['summary'];
			$colorid = $eventd['colorId'];
			$description = $eventd['description']; 
			$startTP->setTimeZone($providerTZ);
			$startTP->setDateTime($date."T".$start);
			$endTP->setTimeZone($providerTZ);
			$endTP->setDateTime($date."T".$end);
			$eventP->setStart($startTP);
			$eventP->setEnd($endTP);
			$eventP->setSummary($summary);
			$eventP->setColorId($colorid);
			$eventP->setLocation($location);
			$eventP->setDescription($description); 

			$updatedEvent = $calP->events->update($calendarId,$eventid,$eventP);
			if(isset($updatedEvent)){
				return $updatedEvent;
			}else{
				return '';
			}
		}
	}
/* Set Location Session */	
if(isset($_POST['general_ajax_action'],$_POST['location_id']) && $_POST['general_ajax_action']=='set_location_session' && $_POST['location_id']!='' ){
	$_SESSION['oct_location'] = $_POST['location_id'];
}	
/* Get Payments By Range */
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_payments_byrange'){
	$payments->location_id = $_SESSION['oct_location'];
	$all_payments=$payments->get_payments_byrange($_POST['payment_start'].' 00:00:00',$_POST['payment_end'].' 23:59:59');
	
	foreach($all_payments as $payment){ 
				$order_info->order_id = $payment->order_id;
				$order_info->readOne_by_order_id();
			 ?>
			<tr>
				<td><?php echo $order_info->client_name;?></td>	
				<?php if($payment->payment_method == 'paypal') { ?>
					<td><?php echo __("Paypal","oct");?></td>
				<?php }
				else if($payment->payment_method == 'pay_locally') { ?>
					<td><?php echo __("Pay Locally","oct");?></td>
				<?php }
				else if($payment->payment_method == 'Free') { ?>
					<td><?php echo __("Free","oct");?></td>
				<?php }
				else if($payment->payment_method == 'stripe') { ?>
					<td><?php echo __("Stripe","oct");?></td>
				<?php }
				else if($payment->payment_method == 'authorizenet') { ?>
					<td><?php echo __("Authorize.Net","oct");?></td>
				<?php }
				else if($payment->payment_method == 'payumoney') { ?>
					<td><?php echo __("Payumoney","oct");?></td>
				<?php }
				else if($payment->payment_method == 'paytm') { ?>
					<td><?php echo __("Paytm","oct");?></td>
				<?php }
				else{ 
					echo '<td>&nbsp;</td>';
				}	 ?>		
				<td><?php echo $general->oct_price_format($payment->amount);?></td>
				<td><?php echo $general->oct_price_format($payment->discount);?></td>
				<td><?php echo $general->oct_price_format($payment->taxes);?></td>
				<td><?php echo $general->oct_price_format($payment->partial);?></td>
				<td><?php echo $general->oct_price_format($payment->net_total);?></td>
			</tr>	
			<?php }	
	
}	

/** Get Registerd User All Bookings **/
if(isset($_POST['general_ajax_action'],$_POST['method']) && $_POST['general_ajax_action']=='get_client_bookings' && $_POST['method']=='registered'){
								$oct_bookings->client_id=$_POST['listing_client_id'];	
								$oct_bookings->location_id=$_SESSION['oct_location'];	
								$order_ids=$oct_bookings->get_order_ids_by_client_id();	
								
								foreach($order_ids as $order_id){	
								
									$clients->order_id=$order_id->order_id;
									$stmt[]= $clients->get_client_info_by_order_id();
								}
								$oct_bookings->client_id=$_POST['listing_client_id'];
								$oct_bookings->location_id=$_SESSION['oct_location'];
								$bookings=$oct_bookings->get_client_all_bookings_by_client_id();
								$client_totoal_bookings=sizeof((array)$bookings);

								for($i=0;$i<=$client_totoal_bookings-1;$i++){
									
									$provider->id=$bookings[$i]->provider_id;
									$staff_info = $provider->readOne();
								
									$service->id=$bookings[$i]->service_id;
									$service->readOne(); 
									$client_other_detail=unserialize($stmt[$i][0]->client_personal_info);
									
									$payments->order_id = $bookings[$i]->order_id;
									$payments->read_one_by_order_id(); 
									?>
									<tr>
									<td><?php echo __($bookings[$i]->order_id,"oct");?></td>
									<td><?php echo __(stripslashes_deep($stmt[$i][0]->client_name),"oct");?></td>
									<td><?php echo __(stripslashes_deep($staff_info[0]['staff_name']),"oct");?></td>
									<td><?php echo __(stripslashes_deep($service->service_title),"oct");?></td>
									<td><?php echo __(date_i18n(get_option('date_format'),strtotime($bookings[$i]->booking_datetime)),"oct");?></td>
									<td><?php echo __(date_i18n(get_option('time_format'),strtotime($bookings[$i]->booking_datetime)),"oct");?></td>
									<td>
									 <?php 
									 if($bookings[$i]->booking_status=='C'){  echo __('Confirmed',"oct"); }
									 if($bookings[$i]->booking_status=='R'){  echo __('Rejected',"oct"); }
									 if($bookings[$i]->booking_status=='CC'){  echo __('Cancelled by client',"oct"); }
									 if($bookings[$i]->booking_status=='A' || $bookings[$i]->booking_status=='' ){  
										echo __('Active',"oct"); }
									 if($bookings[$i]->booking_status=='CS'){ echo __('Cancelled by service provider',"oct");}
									 if($bookings[$i]->booking_status=='CO'){ echo __('Completed',"oct"); $booking_st = ''; }
									 if($bookings[$i]->booking_status=='MN'){ echo __('Marked as No-Show',"oct"); } ?>				
												</td>
									<?php if($payments->payment_method == 'paypal') { ?>
									<td><?php echo __("Paypal","oct");?></td>
									<?php }
									else if($payments->payment_method == 'pay_locally') { ?>
										<td><?php echo __("Pay Locally","oct");?></td>
									<?php }
									else if($payments->payment_method == 'Free') { ?>
										<td><?php echo __("Free","oct");?></td>
									<?php }
									else if($payments->payment_method == 'stripe') { ?>
										<td><?php echo __("Stripe","oct");?></td>
									<?php }
									else if($payments->payment_method == 'authorizenet') { ?>
										<td><?php echo __("Authorize .Net","oct");?></td>
									<?php }
									else if($payments->payment_method == 'payumoney') { ?>
										<td><?php echo __("Payumoney","oct");?></td>
									<?php }
									else if($payments->payment_method == 'paytm') { ?>
										<td><?php echo __("Paytm","oct");?></td>
									<?php }
									else{ 
										echo '<td>&nbsp;</td>';
									} ?>	
								<td>
								<?php if($client_other_detail['address']!='') { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("Address","oct");?></b> - <?php echo __($client_other_detail['address'],"oct"); 
								?></div><?php
								} ?>
								
								<?php if($client_other_detail['gender']!='') { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("Gender","oct");?></b> - <?php echo __($client_other_detail['gender'],"oct"); 
								?></div><?php
								} ?>
								
								<?php if($client_other_detail['phone1']!='') { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("Phone","oct");?></b> - <?php echo __($client_other_detail['ccode'].' '.$client_other_detail['phone1'],"oct"); 
								?></div><?php
								} ?>
								
								<?php if($client_other_detail['age']!='') { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("Age","oct");?></b> - <?php echo __($client_other_detail['age'],"oct"); 
								?></div><?php
								} ?>
				
								<?php if($client_other_detail['dob']!='') { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("DOB","oct");?></b> - <?php echo __($client_other_detail['dob'],"oct"); 
								?></div><?php
								} ?>
								<?php if($client_other_detail['zip']!='') { 
								?>
								<div class="col-xs-12 np"><b><?php
								echo __("Zip","oct");?></b> - <?php echo __($client_other_detail['zip'],"oct"); 
								?></div><?php
								} ?>
								<?php if(stripslashes_deep($client_other_detail['city']!='')) { ?>
								<div class="col-xs-12 np"><b><?php
								echo __("City","oct");?></b> - <?php echo __(stripslashes_deep($client_other_detail['city']),"oct"); 
								?></div><?php
								}?>
								<?php if($client_other_detail['skype']!='') { ?>
								<div class="col-xs-12 np"><b><?php
									echo __("Skype id","oct");?></b> - <?php echo __($client_other_detail['skype'],"oct"); 
								?></div><?php	
								}?>
								<?php if($client_other_detail['notes']!='') { ?>
								<div class="col-xs-12 np"><b><?php
									echo __("Notes","oct");?></b> - <?php echo __($client_other_detail['notes'],"oct"); 
								?></div><?php	
								}
								$user_extra_info = get_user_meta($_POST['listing_client_id'],'oct_client_extra_details');
								 if($user_extra_info != '') { 
									foreach($user_extra_info as $user_extra_info2){
										$unser_date = unserialize($user_extra_info2);
										
										$sec_unser_data = unserialize($unser_date);
										foreach($sec_unser_data as $key=>$val){
											?>
												<div class="col-xs-12 np"><b><?php echo $key;?></b> - <?php echo $val; 
												?></div><?php									
										}
									}
								}
									?>
									 
								<?php if($client_other_detail['phone1']=='' && $client_other_detail['age']=='' && $client_other_detail['dob']=='' && $client_other_detail['zip']==''&& $client_other_detail['city']==''&&$client_other_detail['skype']=='' && $client_other_detail['notes']=='' && $client_other_detail['address']=='' && $client_other_detail['gender']=='') {  echo "-"; } ?>
								
								</td>
									</tr>
								<?php }

}
/** Delete Registered Client & releated Info **/
if (isset($_POST['general_ajax_action'],$_POST['delete_id']) && $_POST['delete_id'] != '' && $_POST['general_ajax_action']=='delete_registered_client') {
		$clientlocations = explode(',',get_usermeta($_POST['delete_id'],'oct_client_locations'));
			foreach($clientlocations as $arrkey => $arrvalue){
				if($arrvalue=='#'.$_SESSION['oct_location'].'#'){
					$client_deleted_loc = $arrkey;
				}		
			}
		 $oct_bookings->client_id = $_POST['delete_id'];
		 $oct_bookings->location_id = $_SESSION['oct_location'];
     $all_booking = $oct_bookings->get_client_all_bookings_by_client_id();
			foreach($all_booking as $client_info){
				$clientlastoid = $client_info->order_id;
				$payments->order_id = $clientlastoid; 
				$payments->delete_payments_by_order_id();
				$order_info->order_id = $clientlastoid; ;   
				$order_info->delete_order_client_info_by_order_id();
				$oct_bookings->order_id = $clientlastoid;   
				$oct_bookings->delete_users_booking_by_order_id();
		}
		if(sizeof((array)$clientlocations)==1){
			$clients->id = $_POST['delete_id'];
			$clients->delete_register_users_booking_by_id();   
		}else{
			unset($clientlocations[$client_deleted_loc]);
			update_usermeta($_POST['delete_id'],'oct_client_locations',implode(',',$clientlocations));
		}
	
}


/** Get Guest Client Bookings **/
if(isset($_POST['general_ajax_action'],$_POST['method']) && $_POST['general_ajax_action']=='get_client_bookings' && $_POST['method']=='guest'){
					$order_info->order_id = $_POST['listing_client_id'];
					$guesuser_order_details = $order_info->get_guest_users_record_with_order_id();
					foreach($guesuser_order_details as $guesuser_order_detail) {			
									$provider->id=$guesuser_order_detail->provider_id;
									$staff_info = $provider->readOne();
								
									$service->id=$guesuser_order_detail->service_id;
									$service->readOne(); 
									$client_other_detail=unserialize($guesuser_order_detail->client_personal_info);
									$payments->order_id = $guesuser_order_detail->order_id;
									$payments->read_one_by_order_id(); 
									?>
						<tr>
									<td><?php echo __($guesuser_order_detail->order_id,"oct");?></td>
									<td><?php echo __(stripslashes_deep($guesuser_order_detail->client_name),"oct");?></td>
									<td><?php echo __(stripslashes_deep($staff_info[0]['staff_name']),"oct");?></td>
									<td><?php echo __(stripslashes_deep($service->service_title),"oct");?></td>
									
									<td><?php echo __(date_i18n(get_option('date_format'),strtotime($guesuser_order_detail->booking_datetime)),"oct");?></td>
									<td><?php echo __(date_i18n(get_option('time_format'),strtotime($guesuser_order_detail->booking_datetime)),"oct");?></td>
									
									<td>
										
									 <?php 
									 if($guesuser_order_detail->booking_status=='C'){  echo __('Confirmed',"oct"); }
									 if($guesuser_order_detail->booking_status=='R'){  echo __('Rejected',"oct"); }
									 if($guesuser_order_detail->booking_status=='CC'){  echo __('Cancelled by client',"oct"); }
									 if($guesuser_order_detail->booking_status=='A' || $guesuser_order_detail->booking_status=='' ){  echo __('Active',"oct"); }
									 if($guesuser_order_detail->booking_status=='CS'){ echo __('Cancelled by service provider',"oct");}
									 if($guesuser_order_detail->booking_status=='CO'){ echo __('Completed',"oct"); $booking_st = ''; }
									 if($guesuser_order_detail->booking_status=='MN'){  echo __('Marked as No-Show',"oct"); } ?>				
									</td>
									<?php if($payments->payment_method == 'paypal') { ?>
									<td><?php echo __("Paypal","oct");?></td>
									<?php }
									elseif($payments->payment_method == 'pay_locally') { ?>
										<td><?php echo __("Pay Locally","oct");?></td>
									<?php }
									elseif($payments->payment_method == 'Free') { ?>
										<td><?php echo __("Free","oct");?></td>
									<?php }
									elseif($payments->payment_method == 'stripe') { ?>
										<td><?php echo __("Stripe","oct");?></td>
									<?php }
									else if($payments->payment_method == 'authorizenet') { ?>
										<td><?php echo __("Authorize .Net","oct");?></td>
									<?php }
									else{ 
										echo '<td>&nbsp;</td>';
									} ?>
									<td>
									<?php 
									$user_extra_info = $wpdb->get_results("SELECT *  FROM  ".$wpdb->prefix."oct_order_client_info  WHERE order_id =".$_POST['listing_client_id']);
									  if($user_extra_info != '') { 
									 foreach($user_extra_info as $user_extra_info2){
									   $unser_date = unserialize($user_extra_info2->client_personal_info);
									  foreach($unser_date as $key=>$val){
									   if($key == 'ccode' || $key == 'dob' || $key == 'zip' || $key == 'skype' || $key == 'age'){
									   ?>
										<?php 
									   }else{
										?>
										<div class="col-xs-12 np"><b><?php echo $key;?></b> - <?php echo $val; 
										?></div><?php
									   }  
									  } 
									 }
									} 
									?>
									<?php if($client_other_detail['phone1']=='' && $client_other_detail['age']=='' && $client_other_detail['dob']=='' && $client_other_detail['zip']==''&& $client_other_detail['city']==''&&$client_other_detail['skype']=='' && $client_other_detail['notes']=='' && $client_other_detail['address']=='' && $client_other_detail['gender']=='') {  echo "-"; } ?>							
							</td>
					</tr>
		<?php }
}

/** Delete Guest User Info & Releated Data like-Bookings,payments.order client info **/
if (isset($_POST['general_ajax_action'],$_POST['delete_id']) && $_POST['delete_id'] != '' && $_POST['general_ajax_action']=='delete_guest_client') {
		$payments->order_id = $_POST['delete_id']; 
		$payments->delete_payments_by_order_id();
		$order_info->order_id = $_POST['delete_id'] ;   
		$order_info->delete_order_client_info_by_order_id();
		$oct_bookings->order_id =$_POST['delete_id'];   
		$oct_bookings->delete_users_booking_by_order_id();
}

/** Get All Locations Customers Registered/Guest **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_all_locations_customers'){
	if(isset($_POST['alc']) && $_POST['alc']=='Y'){
		$_SESSION['oct_all_loc_clients'] = 'Y';
	}else{
		unset($_SESSION['oct_all_loc_clients']);
	}
}
/** Get All Locations Payments **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_all_locations_payments'){
	if(isset($_POST['alp']) && $_POST['alp']=='Y'){
		$_SESSION['oct_all_loc_payments'] = 'Y';
	}else{
		unset($_SESSION['oct_all_loc_payments']);
	}
}

/** Get Export Filtered Bookings Detail **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='filtered_bookings'){
	/* Read All Booking of Location */
	if(isset($_SESSION['oct_all_loc_export']) && $_SESSION['oct_all_loc_export']=='Y'){
		$oct_bookings->location_id = 'All';
	}else{
		$oct_bookings->location_id = $_SESSION['oct_location'];
	}
	//$oct_bookings->location_id = $_SESSION['oct_location'];
	$all_bookings = $oct_bookings->readAll($_POST['booking_start'],$_POST['booking_end'],$_POST['booking_service'],$_POST['booking_staff'],'Export');
	foreach($all_bookings as $single_booking){ 
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
			if($single_booking->booking_status=='MN'){ echo __('Marked as No-Show',"oct"); } ?></td>
		</tr>
<?php }	
}
/** Get All Locations Export Data **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_all_exportdata'){
	if(isset($_POST['aled']) && $_POST['aled']=='Y'){
		$_SESSION['oct_all_loc_export'] = 'Y';
	}else{
		unset($_SESSION['oct_all_loc_export']);
	}
}

/* Get Calender Upcomming Appointments */
if(isset($_GET['general_ajax_action']) && $_GET['general_ajax_action']=='get_upcoming_appointments'){
	
		$oct_bookings->location_id = $_SESSION['oct_location'];
		
		$start_date= '';
		$end_date = '';
		$service_id = '';
		$provider_id = ''; 
		if(isset($_SESSION['oct_booking_filtersd'],$_SESSION['oct_booking_filtered']) && $_SESSION['oct_booking_filtersd']!='' && $_SESSION['oct_booking_filtered']!=''){
			$start_date = $_SESSION['oct_booking_filtersd'];
			$end_date = $_SESSION['oct_booking_filtered'];
		}
		if(isset($_SESSION['oct_booking_filterstaff']) && $_SESSION['oct_booking_filterstaff']!=''){
			$provider_id = $_SESSION['oct_booking_filterstaff'];
		}
		if(isset($_SESSION['oct_booking_filterservice']) && $_SESSION['oct_booking_filterservice']!=''){
			$service_id = $_SESSION['oct_booking_filterservice'];
		}
		
		if(($start_date!='' && $end_date!='') || $service_id!='' || $provider_id!=''){
		$all_upcoming_appointments = $oct_bookings->readAll($start_date,$end_date,$service_id,$provider_id);	
		}else{
		$all_upcoming_appointments = $oct_bookings->read_all_upcoming_bookings();		
		}
		$appointment_array_for_cal = array();
		foreach( $all_upcoming_appointments as $app) {
		$appointment_id  = $app->id;
		$booking_status =$app->booking_status;
		$service_start_time =  date_i18n('Y-m-d H:i:s',strtotime($app->booking_datetime));	
		$service_end_time = date_i18n('Y-m-d H:i:s',strtotime($app->booking_endtime)); 
		
		$oct_bookings->booking_id = $appointment_id;		
		$oct_bookings->readOne_by_booking_id();  
		
		$order_info->order_id = $oct_bookings->order_id;	
		$order_info->readOne_by_order_id(); 		
		$customer_phone = $order_info->client_phone;
		$customer_name = ucfirst($order_info->client_name);
		$customer_name = iconv('UTF-8','UTF-8',$customer_name);
		$customer_name = $customer_name;
		
		$service->id = $app->service_id;
		$service->readOne();		
		$serviceTitle = stripslashes_deep($service->service_title);
		$serviceTitle = iconv('UTF-8','UTF-8',$serviceTitle);
		$serviceTitle = ucfirst($serviceTitle);
		$color_tag = $service->color_tag;
	
		$provider->id=$oct_bookings->provider_id;
		$staff_info = $provider->readOne();		
		$provider_name = ucfirst($staff_info[0]['staff_name']);
		$provider_name = iconv('UTF-8','UTF-8',$provider_name);
		$provider_name = $provider_name;
		$provider_email = $staff_info[0]['email'];		
		$provider_phone = $staff_info[0]['phone'];				
		$appointment_array_for_cal[]= array(
						"id"=>"$appointment_id",
						"color_tag"=>"$color_tag",
						"title"=>"$serviceTitle",
						"start"=>"$service_start_time",
						"end"=>"$service_end_time",
						"event_status"=>"$booking_status",
						
						"provider"=>"$provider_name",
						"provider_email"=>"$provider_email",
						"provider_phone"=>"$provider_phone",
						"client_name"=>"$customer_name",
						"client_phone"=>"$customer_phone"					
						);
   }   
   
 $json_encoded_string_for_cal  =  json_encode($appointment_array_for_cal);
echo $json_encoded_string_for_cal;die();
}

/** Get Single Booking Detail **/
if(isset($_POST['general_ajax_action'],$_POST['appointment_id']) && $_POST['general_ajax_action']=='get_appointment_detail' && $_POST['appointment_id']!=''){
	$oct_bookings->booking_id=$_POST['appointment_id']; 
    $oct_bookings->readOne_by_booking_id();
	
    $clients->order_id=$oct_bookings->order_id;
	$client_info = $clients->get_client_info_by_order_id();
	$client_id=$client_info[0]->id;
	
	$oct_bookings->get_order_ids_by_client_id(); 
    $oct_bookings->id = $oct_bookings->client_id; 
    /*Get Service Name*/
	$service->id= $oct_bookings->service_id;
	$service->readone();
	$service_title=stripslashes_deep($service->service_title);
	$service_id=$oct_bookings->service_id;
	$service_color=$service->color_tag;
	$service_price=$service->amount;	
	$service_duration=$service->duration;
	$servicedurationstrinng = '';
	if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
	if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }

	$user_extra_info = get_user_meta($oct_bookings->id,'oct_client_extra_details');
	
								 if($user_extra_info != '') { 
									foreach($user_extra_info as $extra_details){
										$unser_date = unserialize($extra_details);
										$sec_unser_data = unserialize($unser_date);
										foreach($sec_unser_data as $key=>$val){
										$booking_details .=	 "<div class='col-xs-12 np'> <label>".$key." </label> <span class='span-scroll span_indent'> ".$val." </span></div><br/>";
										
										} 
									}
								}
	$_SESSION['booking_details']=$booking_details;
	
    /*Get Client Name*/
	$clients->order_id=$oct_bookings->order_id;
	$client_info = $clients->get_client_info_by_order_id();
	$clientname= $client_info[0]->client_name;
	$clientemail=$client_info[0]->client_email;
	$clientphone=$client_info[0]->client_phone;
	$client_personal_info=unserialize($client_info[0]->client_personal_info);
	
	$client_notes = '';
	if(isset($client_personal_info['notes'])){
		$client_notes = $client_personal_info['notes'];
	}
	
	$client_address = '';
	if(isset($client_personal_info['address'])){
		$client_address = $client_personal_info['address'];
	}
	$client_city = '';
	if(isset($client_personal_info['city'])){
		$client_city=$client_personal_info['city'];
	}
	$client_state = '';
	if(isset($client_personal_info['state'])){
		$client_state=$client_personal_info['state'];
	}
	$client_zip = '';
	if(isset($client_personal_info['zip'])){
		$client_zip=$client_personal_info['zip'];
	}
	$client_country = '';
	if(isset($client_personal_info['country'])){
		$client_country=$client_personal_info['country'];
	}
	$client_ccode = '';
	if(isset($client_personal_info['ccode'])){
		$client_ccode=$client_personal_info['ccode'];
	}
	$full_address = $client_address." ".$client_city." ".$client_state;
    /*Get Provider Name*/
	$staff->id=$oct_bookings->provider_id;
	$staff_info = $staff->readOne();   
	$provider_name = ucfirst($staff_info[0]['staff_name']);   
	$provider_id = $oct_bookings->provider_id;   
    /*Get Payment Method*/   
	$payments->order_id = $oct_bookings->order_id;
	$payments->read_one_by_order_id();	
	$payments->payment_method;
	if($payments->payment_method == 'paypal') { $pay_type = __('Paypal','oct'); }
	elseif($payments->payment_method == 'pay_locally') { $pay_type = __('Pay Locally','oct'); }
	elseif($payments->payment_method == 'Free') {  $pay_type = __('Free','oct');}
	elseif($payments->payment_method == 'stripe'){ $pay_type = __('Stripe','oct'); }
	elseif($payments->payment_method =='authorizenet'){$pay_type = __('Authorize .Net','oct');}
	else{$pay_type = '-';} 
	
	if($oct_bookings->booking_status=='A' || $oct_bookings->booking_status==''){
		$bookingstatus =  "Active";
	}elseif($oct_bookings->booking_status=='C'){
		$bookingstatus = "Confirm";
	}elseif($oct_bookings->booking_status=='R'){
		$bookingstatus = "Reject";
	}elseif($oct_bookings->booking_status=='RS'){
		$bookingstatus = "Rescheduled";
	}elseif($oct_bookings->booking_status=='CC'){
		$bookingstatus =  "Cancel By Client";
	}elseif($oct_bookings->booking_status=='CS'){
		$bookingstatus = "Cancel By Service Provider";
	}elseif($oct_bookings->booking_status=='CO'){
		$bookingstatus =  "Completed";
	}else{
		$oct_bookings->booking_status=='MN';
		$bookingstatus =  "Mark As No Show";
   }
       $appointment_detail = array();     
       $appointment_detail['id']= $oct_bookings->booking_id;
       $appointment_detail['service_title']= $service_title;
       $appointment_detail['booking_price']=$oct_bookings->booking_price;   
       $appointment_detail['appointment_startdate']= date_i18n(get_option('date_format'),strtotime($oct_bookings->booking_datetime));
       $appointment_detail['appointment_starttime']= date_i18n(get_option('time_format'),strtotime($oct_bookings->booking_datetime));
	   $appointment_detail['appointment_endate']= date_i18n(get_option('date_format'),strtotime($oct_bookings->booking_endtime));
       $appointment_detail['appointment_endtime']= date_i18n(get_option('time_format'),strtotime($oct_bookings->booking_endtime));
	   $octabook_api_key = get_option('octabook_api_key');
	   $appointment_detail['booking_date']=date_i18n('m/d/Y',strtotime($oct_bookings->booking_datetime));
	   $appointment_detail['sel_date']=date_i18n('Y-m-d',strtotime($oct_bookings->booking_datetime));
	   $appointment_detail['booking_status']=$bookingstatus;
     $appointment_detail['provider_id']= $provider_id;
     $appointment_detail['provider_name']= $provider_name;
     $appointment_detail['service_id']=$service_id;
     $appointment_detail['service_price']=$service_price;
	   $appointment_detail['service_duration']=$service_duration;
	   $appointment_detail['service_duration_string']=$servicedurationstrinng;
     $appointment_detail['reject_reason']= $oct_bookings->reject_reason;
     $appointment_detail['cancel_reason']= $oct_bookings->cancel_reason;
     $appointment_detail['confirm_note']= $oct_bookings->confirm_note;
     $appointment_detail['reschedule_note']= $oct_bookings->reschedule_note;
     $appointment_detail['payment_type']=$pay_type;
	   $appointment_detail['client_name']=$clientname;
     $appointment_detail['client_phone']= $clientphone;
     $appointment_detail['client_email']= $clientemail;
		 $appointment_detail['client_full_address']=$full_address;
		 $appointment_detail['map_client_address']="https://www.google.com/maps/embed/v1/place?q=".$full_address."&key=".$octabook_api_key."";
	   $appointment_detail['client_address']=$client_address;
	   $appointment_detail['client_notes']=$client_notes;
	   $appointment_detail['client_city']=$client_city;
	   $appointment_detail['client_zip']=$client_zip;
	   $appointment_detail['client_country']=$client_country;
	   $appointment_detail['client_ccode']=$client_ccode;
	   $appointment_detail['client_state']=$client_state;
     $appointment_detail['cust_detail'] = array($_SESSION['booking_details']);
     echo json_encode($appointment_detail);die();
}
/** Get Services By Staff -- Rescheduled/Manual Booking **/
if(isset($_POST['general_ajax_action'],$_POST['staff_id']) && $_POST['general_ajax_action']=='get_services_by_staff' && $_POST['staff_id']!=''){
	$service->provider_id = $_POST['staff_id'];
	$oct_providerservices = $service->readall_services_of_provider();
	$prevcateid = '';
	$temp_cate = array();
	foreach($oct_providerservices as $providerservice){
		if($prevcateid != $providerservice->category_id && $prevcateid !=''){	
			$prevcateid =$providerservice->category_id;
			echo  '</optgroup> ';							
		}
		if(!in_array($providerservice->category_id,$temp_cate)){
			$temp_cate[]= $providerservice->category_id;		
			echo '<optgroup label="'.$providerservice->category_title.'">';
		}			
		echo '<option value="'.$providerservice->service_id.'">'.$providerservice->service_title.'</option>';						
	}
}
/* Get Service Info By Service id */
if(isset($_POST['general_ajax_action'],$_POST['service_id']) && $_POST['general_ajax_action']=='get_services_info' && $_POST['service_id']!=''){
	$service->id= $_POST['service_id'];
	$service->readone();
	$servicedurationstrinng = '';
	if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
	if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }
	
	$service_info = array();     
    $service_info['service_price']= $service->amount;
    $service_info['service_duration']= $servicedurationstrinng;
    $service_info['service_duration_val']= $service->duration;
	echo json_encode($service_info);die();
}
/* Reschedule Appointment */
if(isset($_POST['general_ajax_action'],$_POST['booking_id']) && $_POST['general_ajax_action']=='reschedule_appointment' && $_POST['booking_id']!=''){
	$oct_bookings->booking_id = $_POST['booking_id']; 
  $oct_bookings->readOne_by_booking_id();
	$bookingold_startdt = date_i18n(get_option('date_format'),strtotime($oct_bookings->booking_datetime));
  $bookingold_enddt = date_i18n(get_option('time_format'),strtotime($oct_bookings->booking_datetime));
	$gcevent_id = $oct_bookings->gc_event_id;
	$gc_staff_event_id = $oct_bookings->gc_staff_event_id; /*For Staff Google Calander*/
	$staff_id = $oct_bookings->provider_id;
	
	
								/***********************Calendar code start****************************/
	
	if(isset($gcevent_id) && $gcevent_id!='') {
		$provider_gc_id = get_option('oct_gc_id');
		$provider_gc_data = get_option('oct_gc_token');
		$GcclientID = get_option('oct_gc_client_id');
		$GcclientSecret = get_option('oct_gc_client_secret');
		$GcEDvalue = get_option('oct_gc_status');
		$admin_url = get_option('oct_gc_admin_url');
		if($provider_gc_id!='' && $provider_gc_data!=''){
			if(get_option('timezone_string') != ''){
				$providerTZ = get_option('timezone_string');
			}else{
				$gmt_offset = get_option('gmt_offset');
				$hr_minute = explode('.', $gmt_offset);
				if (isset($hr_minute[1])) {
					if ($hr_mint[1] == '5') {
						$gmt_offset = $hr_mint[0].'.30';
					}else{
						$gmt_offset = $hr_mint[0].'.45';
					}
				}else{
					$gmt_offset = $hr_mint[0];
				}
				$seconds = $gmt_offset * 60 * 60;
				$get_tz = timezone_name_from_abbr('', $seconds, 1);
				if($get_tz === false){ $get_tz = timezone_name_from_abbr('', $seconds, 0); }
				$providerTZ = $get_tz;
			}
			
			$date =date_i18n('Y-m-d',strtotime($_POST['booking_date']));
			$start = date_i18n('H:i:s',strtotime($_POST['booking_time']));
			
			$event_endtime = date_i18n('H:i:s',strtotime("+".$_POST['service_duration']." minutes", strtotime(date_i18n('Y-m-d',strtotime($_POST['booking_date'])).' '.date_i18n('H:i:s',strtotime($_POST['booking_time'])))));
			$end = $event_endtime;

			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/Google_Client.php";
			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/contrib/Google_CalendarService.php";

			$provider_events  = update_google_cal_event($provider_gc_id,$provider_gc_data,$gcevent_id,$date,$start,$end,$providerTZ,$GcclientID,$GcclientSecret,$GcEDvalue,$admin_url);
		}
	}
	/*For Staff Google Calander START*/
if(isset($gc_staff_event_id) && $gc_staff_event_id!='') {
		$provider_gc_id = $staff->get_staff_option("gc_id",$staff_id);
		$provider_gc_data = $staff->get_staff_option("gc_token",$staff_id);
		$GcclientID = $staff->get_staff_option("gc_client_id",$staff_id);
		$GcclientSecret = $staff->get_staff_option("gc_client_secret",$staff_id);
		$GcEDvalue = $staff->get_staff_option("gc_status",$staff_id);
		$admin_url = $staff->get_staff_option("gc_admin_url",$staff_id);
		if($provider_gc_id!='' && $provider_gc_data!=''){
			
			if(get_option('timezone_string') != ''){
				$providerTZ = get_option('timezone_string');
			}else{
					$gmt_offset = get_option('gmt_offset');
					$hr_minute = explode('.', $gmt_offset);
					if (isset($hr_minute[1])) {
						if ($hr_mint[1] == '5') {
							$gmt_offset = $hr_mint[0].'.30';
						}else{
							$gmt_offset = $hr_mint[0].'.45';
						}
					}else{
						$gmt_offset = $hr_mint[0];
					}
					$seconds = $gmt_offset * 60 * 60;
					$get_tz = timezone_name_from_abbr('', $seconds, 1);
					if($get_tz === false){ $get_tz = timezone_name_from_abbr('', $seconds, 0);}
					$providerTZ = $get_tz;
				}
			$date =date_i18n('Y-m-d',strtotime($_POST['booking_date']));
			$start = date_i18n('H:i:s',strtotime($_POST['booking_time']));
			
			$event_endtime = date_i18n('H:i:s',strtotime("+".$_POST['service_duration']." minutes", strtotime(date_i18n('Y-m-d',strtotime($_POST['booking_date'])).' '.date_i18n('H:i:s',strtotime($_POST['booking_time'])))));
			$end = $event_endtime;
		
			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/Google_Client.php";
			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/contrib/Google_CalendarService.php";

			$provider_events = update_google_cal_event($provider_gc_id,$provider_gc_data,$gc_staff_event_id,$date,$start,$end,$providerTZ,$GcclientID,$GcclientSecret,$GcEDvalue,$admin_url);	
		}
}
				/*For Staff Google Calander END*/

										/***********************Calendar code end******************************/

	$oct_bookings->booking_datetime = date_i18n('Y-m-d',strtotime($_POST['booking_date'])).' '.date_i18n('H:i:s',strtotime($_POST['booking_time']));
	$oct_bookings->booking_endtime = date_i18n('Y-m-d H:i:s',strtotime("+".$_POST['service_duration']." minutes", strtotime(date_i18n('Y-m-d',strtotime($_POST['booking_date'])).' '.date_i18n('H:i:s',strtotime($_POST['booking_time'])))));	
	$oct_bookings->reschedule_note = $_POST['reschedule_note'];
	$oct_bookings->booking_status = 'RS';
	$oct_bookings->reschedule_appointment();
}
/* Get Register Client Information for Manual Booking Popup */
if(isset($_POST['general_ajax_action'],$_POST['client_id']) && $_POST['general_ajax_action']=='get_client_info'){
	$oct_bookings->client_id = $_POST['client_id'];
	$oct_bookings->get_register_client_last_order_id();
	/*Get Client Name*/
	$clients->order_id=$oct_bookings->order_id;
	$client_info = $clients->get_client_info_by_order_id();
	$clientinfo = array();
	$clientinfo['client_name']= $client_info[0]->client_name;
	$clientinfo['client_email']=$client_info[0]->client_email;
	$client_personal_info=unserialize($client_info[0]->client_personal_info);
	/* $client_personal_info['ccode'] */
	$clientinfo['client_phone']= $client_info[0]->client_phone;
	if(isset($client_personal_info['notes'])){
		$clientinfo['client_notes'] = $client_personal_info['notes'];
	}else{
		$clientinfo['client_notes'] = '';
	}
	
	if(isset($client_personal_info['address'])){
		$clientinfo['client_address'] = $client_personal_info['address'];
	}else{
		$clientinfo['client_address'] = '';
	}
	
	if(isset($client_personal_info['city'])){
		$clientinfo['client_city'] = $client_personal_info['city'];
	}else{
		$clientinfo['client_city'] = '';
	}
	
	if(isset($client_personal_info['state'])){
		$clientinfo['client_state']= $client_personal_info['state'];		
	}else{
		$clientinfo['client_state'] = '';
	}
	
	if(isset($client_personal_info['zip'])){
		$clientinfo['client_zip']=$client_personal_info['zip'];		
	}else{
		$clientinfo['client_zip'] = '';
	}
	
	if(isset($client_personal_info['country'])){
		$clientinfo['client_country']=$client_personal_info['country'];	
	}else{
		$clientinfo['client_country'] = '';
	}
	
	if(isset($client_personal_info['ccode'])){
		$clientinfo['client_ccode']=$client_personal_info['ccode'];	
	}else{
		$clientinfo['client_ccode'] = '';
	}	
	echo json_encode($clientinfo);die();	
}
/* Filter Appointments On Appointup Page */
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='filter_appointments'){
	if($_POST['startdate']!=''){$_SESSION['oct_booking_filtersd'] = $_POST['startdate'];}else{ if(isset($_SESSION['oct_booking_filtersd'])){ unset($_SESSION['oct_booking_filtersd']); }}
	if($_POST['enddate']!=''){$_SESSION['oct_booking_filtered'] = $_POST['enddate'];}else{ if(isset($_SESSION['oct_booking_filtered'])){ unset($_SESSION['oct_booking_filtered']); }}
	if($_POST['staff_id']!=''){$_SESSION['oct_booking_filterstaff'] = $_POST['staff_id'];}else{ if(isset($_SESSION['oct_booking_filterstaff'])){ unset($_SESSION['oct_booking_filterstaff']); }}
	if($_POST['service_id']!=''){$_SESSION['oct_booking_filterservice'] = $_POST['service_id'];}else{ if(isset($_SESSION['oct_booking_filterservice'])){ unset($_SESSION['oct_booking_filterservice']); }}
}
/* Confirm,Reject,Cancel Appointment From Appointment Calender **/
if(isset($_POST['general_ajax_action'],$_POST['booking_id'],$_POST['method']) && $_POST['general_ajax_action']=='c_r_cs_cc_appointment' && $_POST['booking_id']!=''){
	$booking_id=$_POST['booking_id'];
	$booking_method=$_POST['method'];
	
	/* Update Booking Status */
	if($booking_method=='C'){
	$oct_bookings->confirm_note = $_POST['action_content'];
	}
	if($booking_method=='R'){	
	$oct_bookings->reject_reason = $_POST['action_content'];
	}
	if($booking_method=='CS' || $booking_method=='CC'){				
	$oct_bookings->cancel_reason = $_POST['action_content'];
	}
	$oct_bookings->booking_id = $booking_id;
	$oct_bookings->booking_status = $booking_method;
	$oct_bookings->update_booking_status_by_id();
	
	/* Get booking-details */
	$oct_bookings->booking_id = $booking_id; 
	$oct_bookings->readOne_by_booking_id();

	$booking_date_start = $oct_bookings->booking_datetime;
	$booking_date_end = $oct_bookings->booking_endtime;
	$price = $oct_bookings->booking_price;
	
	$gc_event_id = $oct_bookings->gc_event_id;
	/** delete event google calendar code **/
	if(isset($gc_event_id) && $gc_event_id != ''){
		$curldeleteevent = curl_init();
		curl_setopt_array($curldeleteevent, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $plugin_url_for_ajax.'/assets/GoogleCalendar/deleteevent.php?eid='.$gc_event_id.'&pid=0',
			CURLOPT_FRESH_CONNECT =>true,
			CURLOPT_USERAGENT => 'OctaBook'
		));
		$respdelete = curl_exec($curldeleteevent);
		curl_close($curldeleteevent); 
	}
	/** delete event google calendar code **/
}
/* Delete Appointment,Order Payment,Order Client Info */
if(isset($_POST['general_ajax_action'],$_POST['booking_id']) && $_POST['general_ajax_action']=='delete_appointment' && $_POST['booking_id']!=''){
	;
	$oct_bookings->booking_id = $_POST['booking_id'];
	$oct_bookings->readOne_by_booking_id();
	$oct_bookings->order_id =  $oct_bookings->order_id;
	$order_all_bookings = $oct_bookings->get_all_bookings_by_order_id();
	$gc_event_id = $oct_bookings->gc_event_id;
	$gc_staff_event_id = $oct_bookings->gc_staff_event_id;
	$provider_id = $oct_bookings->provider_id;
	
	/** delete event google calendar code **/
	if(isset($gc_event_id) && $gc_event_id != ''){
		$curldeleteevent = curl_init();
		curl_setopt_array($curldeleteevent, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $plugin_url_for_ajax.'/assets/GoogleCalendar/deleteevent.php?eid='.$gc_event_id.'&pid=0',
		CURLOPT_FRESH_CONNECT =>true,
		CURLOPT_USERAGENT => 'OctaBook'
		));
		$respdelete = curl_exec($curldeleteevent);
		curl_close($curldeleteevent); 
	}
			/*For Staff Google Calander START(DELETE)*/
			
	if(isset($gc_staff_event_id) && $gc_staff_event_id != ''){
	$curldeleteevent = curl_init();
	curl_setopt_array($curldeleteevent, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => $plugin_url_for_ajax.'/assets/GoogleCalendar/deleteevent_provider.php?eid='.$gc_staff_event_id.'&pid='.$provider_id,
	CURLOPT_FRESH_CONNECT =>true,
	CURLOPT_USERAGENT => 'OctaBook'
	));
	$respdelete = curl_exec($curldeleteevent);
	curl_close($curldeleteevent); 
	}
		/*For Staff Google Calander END(DELETE)*/
		
	/** delete event google calendar code **/	
	if(sizeof((array)$order_all_bookings)<=1){
		 $order_info->order_id = $oct_bookings->order_id;   
		 $order_info->delete_order_client_info_by_order_id();
		 $payments->order_id = $oct_bookings->order_id; 
		 $payments->delete_payments_by_order_id();	
	}
	$oct_bookings->booking_id = $_POST['booking_id'];
	$oct_bookings->delete_booking();
	
}
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='book_manual_appointment'){	
		
		$oct_bookings->get_last_order_id();
		$last_order_id = $oct_bookings->last_order_id;
		if($last_order_id=='') {
			$last_order_id = 1000;
		}
		$next_order_id = $last_order_id +1;		
		$booking_datetime= date_i18n('Y-m-d H:i:s',strtotime($_POST['booking_date'].' '.$_POST['booking_time']));
		$strtotime_of_start_time = strtotime($_POST['booking_date'].' '.$_POST['booking_time']);
		$strtotime_of_end_time = strtotime(date_i18n('Y-m-d H:i:s',strtotime("+".$_POST['service_duration']." minutes", strtotime(date_i18n('Y-m-d',strtotime($_POST['booking_date'])).' '.date_i18n('H:i:s',strtotime($_POST['booking_time']))))));
		$start_y_m_d = date_i18n('Y-m-d', $strtotime_of_start_time);
		$end_y_m_d = date_i18n('Y-m-d', $strtotime_of_end_time);
		$end_booking_date_time = "";
		if($start_y_m_d != $end_y_m_d){
			$end_booking_date_time = date_i18n('Y-m-d H:i:s', strtotime('-1 minutes',$strtotime_of_end_time));
		}else{
			$end_booking_date_time = date_i18n('Y-m-d H:i:s', $strtotime_of_end_time);
		}
		$booking_endtime = $end_booking_date_time;
		$booking_status = 'A';
		if(get_option('octabook_appointment_auto_confirm')=='E'){
			$booking_status = 'C';
		}
		$taxvat = 0;
		if(get_option('octabook_taxvat_status')=='E'){
			if(get_option('octabook_taxvat_type')=='P'){ $taxvat = $_POST['service_price']*get_option('octabook_taxvat_amount')/100;}
			if(get_option('octabook_taxvat_type')=='F'){ $taxvat = $_POST['service_price']+get_option('octabook_taxvat_amount');}
		}
		$booking_price = $taxvat+$_POST['service_price'];
		
		
		if(get_option('octabook_guest_user_checkout')=='E' && $_POST['client_id']==''){
			$client_id = 0;
		}elseif($_POST['client_id']!=''){
			$client_id = $_POST['client_id'];			
		}else{
			$client_data = array('ID' => '','user_pass' => $_POST['client_password'],'user_login' => $_POST['client_username'],'display_name' => $_POST['client_name'],'first_name' => $_POST['client_name'],'last_name' => $_POST['client_name'],'user_email' => $_POST['client_email'],'role' => 'subscriber' );
			$client_id = wp_insert_user( $client_data );
			$user = new WP_User($client_id);
			$user->add_cap('oct_client');
			add_user_meta($client_id, 'oct_client_locations','#'.$_SESSION['oct_location'].'#');
		}
		$ccode = '';
		$user_extra_info = get_user_meta($client_id,'oct_client_extra_details');
		$user_extra_info_array = array();
		if($user_extra_info != '') { 
			foreach($user_extra_info as $extra_details){
				$unser_date = unserialize($extra_details);
				$sec_unser_data = unserialize($unser_date);
				foreach($sec_unser_data as $key=>$val){
				$user_extra_info_array[$key] = $val;
				} 
			}
		}
		$client_personal_info_array = array("phone1"=>$_POST['client_phone'],"address"=>$_POST['client_address'],"zip"=>$_POST['client_zip'],"city"=>$_POST['client_city'],"skype"=>'',"notes"=>$_POST['booking_note'],"age"=>'',"dob"=>'',"ccode"=>$ccode,"gender"=>'',"state"=>$_POST['client_state'],"country"=>$_POST['client_country']);
		$client_otherinfo = serialize(array_merge($client_personal_info_array,$user_extra_info_array));
		
		/* Adding Appointments Into Google Calendar START */
	if (!function_exists('octabook_addevent_googlecalender_provider')) {
		function octabook_addevent_googlecalender_provider($provider_id,$provider_gc_id,$gc_token,$summary,$location,$description,$event_color,$date,$start,$end,$GcclientID,$GcclientSecret,$GcEDvalue,$providerTZ){
			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/Google_Client.php";
			require_once dirname(dirname(dirname(__FILE__)))."/assets/GoogleCalendar/google-api-php-client/src/contrib/Google_CalendarService.php";

			$clientP = new Google_Client();
			$clientP->setApplicationName("OctaBook Google Calender");
			$clientP->setClientId($GcclientID);
			$clientP->setClientSecret($GcclientSecret);   
			$clientP->setRedirectUri(get_option('oct_gc_frontend_url'));
			$clientP->setDeveloperKey($GcclientID);
			$clientP->setScopes( 'https://www.googleapis.com/auth/calendar' );
			$clientP->setAccessType('offline');

			$calP = new Google_CalendarService($clientP);

			$clientP->setAccessToken($gc_token);
			$accesstoken = json_decode($gc_token);  

			if ($gc_token) {
				if ($clientP->isAccessTokenExpired()) {
					$clientP->refreshToken($accesstoken->refresh_token);
				}
			}
			if ($clientP->getAccessToken()){
				$startTP = new Google_EventDateTime();
				$endTP = new Google_EventDateTime();
				$eventP = new Google_Event();
				$calendarId = $provider_gc_id;
				$startTP->setTimeZone($providerTZ);
				$startTP->setDateTime($date."T".$start);
				$endTP->setTimeZone($providerTZ);
				$endTP->setDateTime($date."T".$end);
				$eventP->setSummary($summary);
				$eventP->setColorId($event_color);
				$eventP->setLocation($location);
				$eventP->setDescription($description);
				$eventP->setStart($startTP);
				$eventP->setEnd($endTP);

				$insert = $calP->events->insert($provider_gc_id,$eventP);
			}

			if(isset($insert)){
				return $insert;
			}else{
				return '';
			}  
		}
	}
	
	$GcclientID =  get_option('oct_gc_client_id');
	$GcclientSecret = get_option('oct_gc_client_secret');
	$GcEDvalue = get_option('oct_gc_status');
		
	$service = new octabook_service();
	$service->id=$_POST['service_id'];
	$serviceInfo = $service->readOne();
	$service_title = $service->service_title;
	
	$gc_token = get_option('oct_gc_token');
	$summary = $service_title."-".$_POST['client_name'];
	$description = 'Service='.$service_title.', Name='.$_POST['client_name'].', Email='.$_POST['client_email'].', Phone='.$_POST['client_phone'];
	$event_color = '9';
	
	$date = date_i18n('Y-m-d', strtotime($booking_datetime));
	$start = date_i18n('H:i:s', strtotime($booking_datetime));
	$end = date_i18n('H:i:s', strtotime($booking_datetime));
	if(get_option('timezone_string') != ''){
		$providerTZ = get_option('timezone_string');
	}else{
		$gmt_offset = get_option('gmt_offset');
		$hr_minute = explode('.', $gmt_offset);
		if (isset($hr_minute[1])) {
			if ($hr_minute[1] == '5') {
				$gmt_offset = $hr_minute[0].'.30';
			}else{
				$gmt_offset = $hr_minute[0].'.45';
			}
		}else{
			$gmt_offset = $hr_minute[0];
		}
		$seconds = $gmt_offset * 60 * 60;
		$get_tz = timezone_name_from_abbr('', $seconds, 1);
		if($get_tz === false){ $get_tz = timezone_name_from_abbr('', $seconds, 0); }
		$providerTZ = $get_tz;
	}
	$provider_gc_id = get_option('oct_gc_id');
	$provider_id = '';
	if($gc_token != '' && $GcEDvalue == 'Y' && $GcclientID!='' && $GcclientSecret!=''){
		$event_Status = octabook_addevent_googlecalender_provider($provider_id,$provider_gc_id,$gc_token,$summary,$location,$description,$event_color,$date,$start,$end,$GcclientID,$GcclientSecret,$GcEDvalue,$providerTZ);
		 $gc_event_id = $event_Status['id'];
	}else{
		 $gc_event_id = '';
	}
	
	global $wpdb;
	$wpdb->query("insert into ".$wpdb->prefix."oct_bookings set location_id='".$_SESSION['oct_location']."',order_id='".$next_order_id."',client_id='".$client_id."',service_id='".$_POST['service_id']."',provider_id='".$_POST['provider_id']."',booking_price='".$booking_price."',booking_datetime='".$booking_datetime."',booking_endtime='".$booking_endtime."',booking_status='".$booking_status."',lastmodify='".date_i18n('Y-m-d H:i:s')."',gc_event_id='".$gc_event_id."'");
	/*  Adding Appointments Into Google Calendar END  */
		
		
		
		
	$booking_id = $wpdb->insert_id;
	$wpdb->query("insert into ".$wpdb->prefix."oct_payments set location_id='".$_SESSION['oct_location']."',order_id='".$next_order_id."',client_id='".$client_id."',payment_method='pay_locally',amount='".$_POST['service_price']."',taxes='".$taxvat."', 	net_total='".$booking_price."',lastmodify='".date_i18n('Y-m-d H:i:s')."'");
	
	$wpdb->query("insert into ".$wpdb->prefix."oct_order_client_info set order_id='".$next_order_id."',client_name='".$_POST['client_name']."',client_email='".$_POST['client_email']."',client_phone='".$_POST['client_phone']."',client_personal_info='".$client_otherinfo."'");
		
	echo $booking_id; die();
		
}
/** Sending Email For Booking Actions **/
if(isset($_POST['booking_id'],$_POST['method']) && $_POST['booking_id']!='' && $_POST['method']!=''){	
	/* Get booking-details */
	$company_name = get_option('octabook_company_name');
	$company_address = get_option('octabook_company_address');
	$company_city = get_option('octabook_company_city');
	$company_state = get_option('octabook_company_state');
	$company_zip = get_option('octabook_company_zip');
	$company_country = get_option('octabook_company_country');
	$company_phone = get_option('octabook_company_country_code').get_option('octabook_company_phone');
	$company_email = get_option('octabook_company_email');
	$company_logo = $business_logo = site_url()."/wp-content/uploads/".get_option('octabook_company_logo');
	$sender_name = get_option('octabook_email_sender_name');
	$sender_email_address = get_option('octabook_email_sender_address');
	$headers = "From: $sender_name <$sender_email_address>" . "\r\n";
	
	$booking_method = $_POST['method'];
	$booking_id		= $_POST['booking_id'];
	
	$oct_bookings->booking_id = $booking_id; 
	$oct_bookings->readOne_by_booking_id();

	$booking_datetime = $oct_bookings->booking_datetime;
	$booking_reject_reason = $oct_bookings->reject_reason;
	$booking_cancel_reason = $oct_bookings->cancel_reason;
	$booking_confirm_note = $oct_bookings->confirm_note;
	$booking_order_id = $oct_bookings->order_id;
	
	
	
	
	$booking_date_start = $oct_bookings->booking_datetime;
	$booking_date_end = $oct_bookings->booking_endtime;
	$price = $oct_bookings->booking_price;
							
	$service->id = $oct_bookings->service_id;                    
	$provider->id = $oct_bookings->provider_id;                    
	$service->readOne();                    
	$staffinfo = $provider->readOne();
	
	
	
	
	$location_title = '';
	$location_description = '';
	$location_email = '';
	$location_phone = '';
	$location_address = '';
	$location_city = '';
	$location_state = '';
	$location_zip = '';
	$location_country = '';

	if($oct_bookings->location_id!=0 || $oct_bookings->location_id!=''){
		$location->id = $oct_bookings->location_id;
		$locationinfo = $location->readOne();
		if(sizeof((array)$locationinfo)>0){
			$location_title = stripslashes_deep($locationinfo[0]->location_title);
			$location_description = stripslashes_deep($locationinfo[0]->description);
			$location_email = $locationinfo[0]->email;				
			$location_phone = $locationinfo[0]->phone;
			$location_address = stripslashes_deep($locationinfo[0]->address);
			$location_city = stripslashes_deep($locationinfo[0]->city);
			$location_state = stripslashes_deep($locationinfo[0]->state);
			$location_zip = stripslashes_deep($locationinfo[0]->zip);
			$location_country = stripslashes_deep($locationinfo[0]->country);
		}
	}
	
	
	$addons_detail = '';
	$addon_titles = '';
	$addon_prices = '';
	$addon_qty = '';
	$oct_bookings->order_id =  $oct_bookings->order_id;
	$serviceaddons_info = $oct_bookings->select_addonsby_orderidand_serviceid();	
	$totalserviceaddons = sizeof((array)$serviceaddons_info);
	if($totalserviceaddons>0){
		$addoncounter = 1;
		foreach($serviceaddons_info as $serviceaddon_info){				
			$service->addon_id = $serviceaddon_info->addons_service_id;
			$addon_info = $service->readOne_addon();
			if($addoncounter==$totalserviceaddons){
				$addon_titles .= stripslashes_deep($addon_info[0]->addon_service_name); 
				$addon_prices .= $serviceaddon_info->addons_service_rat; 
				$addon_qty .= $serviceaddon_info->associate_service_d; 
			}else{
				$addon_titles .= stripslashes_deep($addon_info[0]->addon_service_name).','; 
				$addon_prices .= $serviceaddon_info->addons_service_rat.',';
				$addon_qty .= $serviceaddon_info->associate_service_d.',';
			}				
			$addoncounter++;
		}			
		$addons_detail .="<br/><span><strong>".__('Addon Tittle(s)','oct')."</strong>: ".$addon_titles."</span><br/><br/><span><strong>".__('Addon Price(s)','oct')."</strong>: ".$addon_prices."</span><br/><br/><span><strong>".__('Addon Quantity(s)','oct')."</strong>: ".$addon_qty."</span><br/><br/>";			
	}
	
	$payments->order_id = $oct_bookings->order_id;
	$payments->read_one_by_order_id();	
	
	/* Refund/Add Loyalty Points ON Cancel/Reject/Cancel BY Client Booking */
	if(($booking_method=='R' || $booking_method=='CS' || $booking_method=='CC') && $payments->payment_method != 'pay_locally'){
		$curr_bal = 0;
		$loyalty_points->client_id =  $oct_bookings->client_id;
		$loyalty_points->get_client_balance();
		if(isset($loyalty_points->balance) && $loyalty_points->balance!=''){
			$curr_bal = $loyalty_points->balance;
		}	
		$oct_bookings->order_id = $oct_bookings->order_id;
		$totalorder_bookings = $oct_bookings->count_order_bookings();
		$refund_points = $payments->net_total/$totalorder_bookings;
		$loyalty_points->booking_id = $oct_bookings->booking_id;
		$loyalty_points->client_id = $oct_bookings->client_id;
		$loyalty_points->credit = $refund_points;
		$loyalty_points->balance =$curr_bal + $refund_points;
		$loyalty_points->debit = 0;
		$loyalty_points->credit_debit_loyalty_points();
	}
	
		
	$order_info->order_id = $oct_bookings->order_id;
	$order_info->readOne_by_order_id();		
	$client_name = ucwords($order_info->client_name);
	$client_email = $order_info->client_email;
	$client_phone = $order_info->client_phone;
	$client_personal_info  = unserialize($order_info->client_personal_info);
	$client_address = '';
	if(isset($client_personal_info['address'])){
		 $client_address = $client_personal_info['address'];
	}
	$client_city = '';
	if(isset($client_personal_info['city'])){
		 $client_city = $client_personal_info['city'];
	}
	$client_zip = '';
	if(isset($client_personal_info['zip'])){
		 $client_zip = $client_personal_info['zip'];
	}
	$client_gender = '';
	if(isset($client_personal_info['gender'])){
		 $client_gender = $client_personal_info['gender'];
	}
	$counter_extra++;
	$client_dateofbirth = '';
	if(isset($client_personal_info['dob'])){
		 $client_dateofbirth = $client_personal_info['dob'];
	}
	$client_age = '';
	if(isset($client_personal_info['age'])){
		 $client_age = $client_personal_info['age'];
	}
	$client_skype = '';
	if(isset($client_personal_info['skype'])){
		 $client_skype = $client_personal_info['skype'];
	}
	$client_state = '';
	if(isset($client_personal_info['state'])){
		 $client_state = $client_personal_info['state'];
	}
	$client_ccode = '';
	if(isset($client_personal_info['ccode'])){
		 $client_ccode = $client_personal_info['ccode'];
	}
	$client_custom_detail = array_slice($client_personal_info,12);
	
	$booking_details = "<br/><span><strong>".__('For','oct')."</strong>: ".stripslashes_deep($service->service_title)."</span><br/><br/>
						<span><strong>".__('With','oct')."</strong>: ".ucwords(stripslashes_deep($staffinfo[0]['staff_name']))."</span><br/><br/>
						<span><strong>".__('On','oct')."</strong>: ".date_i18n(get_option('date_format'),strtotime($oct_bookings->booking_datetime))."</span><br/><br/>
						<span><strong>".__('At','oct')."</strong>: ".date_i18n(get_option('time_format'),strtotime($oct_bookings->booking_datetime))."</span><br/>";						


	$client_full_detail='<br/>';
	if($client_name!=''){ 
		$client_full_detail .="<span><strong>".__('Client Name','oct')."</strong>: ".$client_name."</span><br/><br/>";
	}
	if($client_email!=''){ 
		$client_full_detail .="<span><strong>".__('Client Email','oct')."</strong>: ".$client_email."</span><br/><br/>";
	}	
	if($client_phone!=''){ 
		$client_full_detail .="<span><strong>".__('Client Phone','oct')."</strong>: ".$client_ccode.$client_phone."</span><br/><br/>";
	}	
	if($client_gender!=''){
		if($client_gender == "M"){
			$gender_display = "Male";
		}else{
			$gender_display = "Female";
		}
		$client_full_detail .="<span><strong>".__('Gender','oct')."</strong>: ".$gender_display."</span><br/><br/>";
	}
	if($client_dateofbirth!=''){
		$client_full_detail .="<span><strong>".__('DOB','oct')."</strong>: ".$client_dateofbirth."</span><br/><br/>";
	}
	if($client_age!=''){
		$client_full_detail .="<span><strong>".__('Age','oct')."</strong>: ".$client_age."</span><br/><br/>";
	}
	if($client_address!=''){
		$client_full_detail .="<span><strong>".__('Address','oct')."</strong>: ".$client_address."</span><br/><br/>";
	}
	if($client_city!=''){
		$client_full_detail .="<span><strong>".__('City','oct')."</strong>: ".$client_city."</span><br/><br/>";
	}
	if($client_state!=''){
		$client_full_detail .="<span><strong>".__('State','oct')."</strong>: ".$client_state."</span><br/><br/>";
	}
	if($client_zip!=''){
		$client_full_detail .="<span><strong>".__('Zip','oct')."</strong>: ".$client_zip."</span><br/><br/>";
	}
	if($client_skype!=''){
		$client_full_detail .="<span><strong>".__('Skype','oct')."</strong>: ".$client_skype."</span><br/><br/>";
	}
	if($client_notes!=''){
		$client_full_detail .="<span><strong>".__('Notes','oct')."</strong>: ".$client_notes."</span><br/><br/>";
	}
	foreach($client_custom_detail as $key=>$val){
		if($val != ''){
			$client_full_detail .="<span><strong>".$key."</strong>: ".$val."</span><br/><br/>";
		}
	}
	$sms_client_info = 'client name: '.$client_name.' clinet email : '.$client_email;
    /* Confirm Link */        
	$appoint_confirm_link_sp =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".base64_encode(base64_encode($oct_bookings->order_id)."-confirm");  
	/* Reject Link */     
	$appoint_reject_link_sp =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".base64_encode(base64_encode($oct_bookings->order_id)."-reject");
	/* Client Cancel Link */
	$appoint_cancel_link_client =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".base64_encode(base64_encode($oct_bookings->order_id)."-clientcancel");
	
	if(get_option('octabook_auto_confirm_appointment')=='Y' ){
	$confirm_link_sp='';
	}else{
	$confirm_link_sp="<a style='text-decoration: none;color: #FFF;background-color: #348eda;	border: solid #348eda;border-width: 10px 30px; line-height: 1;	font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block; border-radius: 10px;'  id='email-btn-primary' class='email-btn-primary' href='".$appoint_confirm_link_sp."-".base64_encode($oct_bookings->provider_id."+".$booking_id)."' >".__('Confirm','oct')."</a>";     
	}		
	$reject_link_sp ="<a style='text-decoration: none;color: #FFF;background-color: red;border: solid red;border-width: 10px 30px;line-height: 1;font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block;border-radius: 10px;'  id='email-btn-secondary' class='email-btn-secondary' href='".$appoint_reject_link_sp."-".base64_encode($oct_bookings->provider_id."+".$booking_id)."' >".__('Reject','oct')."</a>";

	$cancel_link_client ="<a style='text-decoration: none;color: #FFF;background-color: red;border: solid red;border-width: 10px 30px;line-height: 1;font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block;border-radius: 10px;'  id='email-btn-secondary' class='email-btn-secondary' href='".$appoint_cancel_link_client."-".base64_encode($oct_bookings->client_id."+".$booking_id)."' >".__('Cancel','oct')."</a>";



	
		
	$search = array('{{company_name}}','{{service_name}}','{{service_provider_name}}','{{customer_name}}','{{client_address}}','{{client_city}}','{{client_zip}}','{{client_phone}}','{{client_email}}','{{client_gender}}','{{client_dateofbirth}}','{{client_age}}','{{client_skype}}','{{client_state}}','{{appointment_id}}','{{appointment_date}}','{{appointment_time}}','{{net_amount}}','{{discount_amount}}','{{payment_method}}','{{taxes_amount}}','{{partial_amount}}','{{provider_email}}','{{provider_phone}}','{{provider_appointment_reject_link}}','{{provider_appointment_confirm_link}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}','{{appointment_confirm_note}}','{{appointment_reschedle_note}}','{{appointment_previous_date}}','{{appointment_previous_time}}','{{admin_manager_name}}','{{client_appointment_cancel_link}}','{{booking_details}}','{{appoinment_client_detail}}','{{addons_details}}','{{location_title}}','{{location_description}}','{{location_email}}','{{location_phone}}','{{location_address}}','{{location_city}}','{{location_state}}','{{location_zip}}','{{location_country}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');      
			
				
	$replace_with = array($company_name,stripslashes_deep($service->service_title),ucwords(stripslashes_deep($staffinfo[0]['staff_name'])),$client_name,$client_address,$client_city,$client_zip,$client_phone,$client_email,$client_gender,$client_dateofbirth,$client_age,$client_skype,$client_state,$booking_id,date_i18n(get_option('date_format'),strtotime($booking_datetime)),date_i18n(get_option('time_format'),strtotime($booking_datetime)),$payments->net_total,$payments->discount,$payments->payment_method,$payments->taxes,$payments->partial,$staffinfo[0]['email'],$staffinfo[0]['phone'],$reject_link_sp,$confirm_link_sp,$booking_reject_reason,$booking_cancel_reason,$booking_confirm_note,'','','',$sender_name,$cancel_link_client,$booking_details,$client_full_detail,$addons_detail,$location_title,$location_description,$location_email,$location_phone,$location_address,$location_city,$location_state,$location_zip,$location_country,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo); 
		
	
	$booking_details_sms .= ' With :'.ucwords(stripslashes_deep($staffinfo[0]['staff_name'])).' On : '.date_i18n(get_option('date_format'),strtotime($booking_datetime)).' At : '.date_i18n(get_option('time_format'),strtotime($booking_datetime)).' For: '.$oct_service->service_title.', ';
	/******************* Send Email Notification *********************/
	
	/* Send email to Client when booking is complete */	
	if(get_option('octabook_client_email_notification_status')=='E'){	
		$oct_clientemail_templates = new octabook_email_template();
		$msg_template = $oct_clientemail_templates->email_parent_template;	
		$oct_clientemail_templates->email_template_name = $booking_method."C";
		$template_detail = $oct_clientemail_templates->readOne();        
		if($template_detail[0]->email_message!=''){            
			$email_content = $template_detail[0]->email_message;        
		}else{            
			$email_content = $template_detail[0]->default_message;        
		}        
		$email_subject = $template_detail[0]->email_subject;
		$email_client_message = '';
		/* Sending email to client when New Appointment request Sent */ 		  
		if($template_detail[0]->email_template_status=='e'){
			$email_client_message = str_replace($search,$replace_with,$email_content);	
			$email_client_message = str_replace('###msg_content###',$email_client_message,$msg_template);		
			add_filter( 'wp_mail_content_type', 'set_content_type' );				
			$status = wp_mail($client_email,$email_subject,$email_client_message,$headers);           
		}       
	}
	/* Send email to service provider when booking is complete */	
	if(get_option('octabook_service_provider_email_notification_status')=='E'){	
		$oct_staffemail_templates = new octabook_email_template();	
		$msg_template = $oct_staffemail_templates->email_parent_template;
		$oct_staffemail_templates->email_template_name = $booking_method."S";  
		$template_detail = $oct_staffemail_templates->readOne();        
		if($template_detail[0]->email_message!=''){            
			$email_content = $template_detail[0]->email_message;        
		}else{            
			$email_content = $template_detail[0]->default_message;
		}        
		$email_subject = $template_detail[0]->email_subject;   
		$email_staff_message = '';
		
		if($template_detail[0]->email_template_status=='e'){								
			$email_staff_message = str_replace($search,$replace_with,$email_content);	
			$email_staff_message = str_replace('###msg_content###',$email_staff_message,$msg_template);		
			add_filter( 'wp_mail_content_type', 'set_content_type' );  				
			$status = wp_mail($staffinfo[0]['email'],$email_subject,$email_staff_message,$headers);  
		}
	}	
	/* Send email to Admin when booking is complete */
	if(get_option('octabook_admin_email_notification_status')=='E'){
		$oct_adminemail_templates = new octabook_email_template();
		$msg_template = $oct_adminemail_templates->email_parent_template;
		$oct_adminemail_templates->email_template_name = $booking_method."A";  		
		$template_detail = $oct_adminemail_templates->readOne();        
		if($template_detail[0]->email_message!=''){            
			$email_content = $template_detail[0]->email_message;        
		}else{            
			$email_content = $template_detail[0]->default_message;
		}        
		$email_subject = $template_detail[0]->email_subject;
		$email_admin_message = '';				
		$email_admin_message = str_replace($search,$replace_with,$email_content);	
		$email_admin_message = str_replace('###msg_content###',$email_admin_message,$msg_template);
		add_filter( 'wp_mail_content_type', 'set_content_type' );		
		$status = wp_mail(get_option('octabook_email_sender_address'),$email_subject,$email_admin_message,$headers);	
	}
	/* Send Email Notification End Here */
	
	/******************* Send SMS Notification *********************/
	if(get_option("octabook_sms_reminder_status") == "E"){			
	
		/*******************  SMS sending code via Plivo  **************/
		if(get_option('octabook_sms_noti_plivo')=="E"){
			
			include_once(dirname(dirname(dirname(__FILE__))).'/objects/plivo.php');
			$plivo_sender_number = get_option('octabook_plivo_number');	
			$auth_sid = get_option('octabook_plivo_sid');
			$auth_token = get_option('octabook_plivo_auth_token');	
			/* Send SMS To Client */
			if(get_option('octabook_plivo_client_sms_notification_status') == "E"){				
				$p_client = new Plivo\RestAPI($auth_sid, $auth_token, '', '');					
				$template = $obj_sms_template->gettemplate_sms("C",'e',$booking_method.'C');					
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$booking_reject_reason,$booking_cancel_reason);
					
					$client_sms_body = str_replace($search,$replace_with,$message);
					$clientparams = array(
					'src' => $plivo_sender_number,
					'dst' => $client_ccode.''.$client_phone,
					'text' => $client_sms_body,
					'method' => 'POST'
					);
					$response = $p_client->send_message($clientparams);
				} 
			}
			/* Send SMS To Staff */
			if(get_option('octabook_plivo_service_provider_sms_notification_status') == "E"){		
				$p_staff = new Plivo\RestAPI($auth_sid, $auth_token, '', '');					
				$template = $obj_sms_template->gettemplate_sms("SP",'e',$booking_method.'S');					
				if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}		

					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);	
					
					$staff_sms_body = str_replace($search,$replace_with,$message);
					$staffparams = array(
					'src' => $plivo_sender_number,
					'dst' => $staffinfo[0]['phone'],
					'text' => $staff_sms_body,
					'method' => 'POST'
					);
					$response = $p_staff->send_message($staffparams);
				} 
			}
			/* Send SMS To Admin */
			if(get_option('octabook_plivo_admin_sms_notification_status') == "E"){					
				$p_admin = new Plivo\RestAPI($auth_sid, $auth_token, '', '');					
				$template = $obj_sms_template->gettemplate_sms("AM",'e',$booking_method.'A');	
				$admin_ccode = get_option('octabook_plivo_ccode');
				$admin_phone = get_option('octabook_plivo_admin_phone_no');
				if($template[0]->sms_template_status == "e" && get_option('octabook_plivo_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}		

					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);	
					
					$admin_sms_body = str_replace($search,$replace_with,$message);
					$adminparams = array(
					'src' => $plivo_sender_number,
					'dst' => $admin_ccode.''.$admin_phone,
					'text' => $admin_sms_body,
					'method' => 'POST'
					);
					$response = $p_admin->send_message($adminparams);
				} 
			}			
			
		}
		/* Plivo SMS Sending End Here */
		
		
		/*******************  SMS sending code via Twilio  **************/
		if(get_option('octabook_sms_noti_twilio')=="E"){
		  
		   $twillio_sender_number = get_option('octabook_twilio_number');
		   $AccountSid = get_option('octabook_twilio_sid');
		   $AuthToken =  get_option('octabook_twilio_auth_token'); 
		   /* Send SMS To Client */
		   if(get_option('octabook_twilio_client_sms_notification_status') == "E"){				   
			$twilliosms_client = new Client($AccountSid, $AuthToken);
			$template = $obj_sms_template->gettemplate_sms("C",'e',$booking_method.'C');					
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$booking_reject_reason,$booking_cancel_reason);
					
					$client_sms_body = str_replace($search,$replace_with,$message);					
					$response =$twilliosms_client->messages->create(
						$client_ccode.''.$client_phone,
						array(
							'from' => $twillio_sender_number,
							'body' => $client_sms_body 
						)
					);
					
				}		
		   }
		   /* Send SMS To Staff */
		   if(get_option('octabook_twilio_service_provider_sms_notification_status') == "E"){		   
			$twilliosms_staff = new Client($AccountSid, $AuthToken);
			$template = $obj_sms_template->gettemplate_sms("SP",'e',$booking_method.'S');					
				if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$staff_sms_body = str_replace($search,$replace_with,$message);					
					$response =$twilliosms_staff->messages->create(
						$staffinfo[0]['phone'],
						array(
							'from' => $twillio_sender_number,
							'body' => $staff_sms_body 
						)
					);
				}		
		   }
		   /* Send SMS To Admin */
		   if(get_option('octabook_twilio_admin_sms_notification_status') == "E"){		   
			$twilliosms_admin = new Client($AccountSid, $AuthToken);
			$template = $obj_sms_template->gettemplate_sms("AM",'e',$booking_method.'A');
			$admin_ccode = get_option('octabook_twilio_ccode');
			$admin_phone = get_option('octabook_twilio_admin_phone_no');
				if($template[0]->sms_template_status == "e" && get_option('octabook_twilio_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$admin_sms_body = str_replace($search,$replace_with,$message);					
					$response =$twilliosms_admin->messages->create(
						$admin_ccode.''.$admin_phone,
						array(
							'from' => $twillio_sender_number,
							'body' => $admin_sms_body 
						)
					);
					
				}		
		   }				
		}
		/* Twilio SMS Sending End Here */
		
		/*******************  SMS sending code via Neximo  **************/
		if(get_option('octabook_sms_noti_nexmo')=="E"){
		  include_once(dirname(dirname(dirname(__FILE__))).'/objects/class_nexmo.php');
		  $nexmo_client = new octabook_nexmo();
		  $nexmo_client->octabook_nexmo_apikey = get_option('octabook_nexmo_apikey');
		  $nexmo_client->octabook_nexmo_api_secret = get_option('octabook_nexmo_api_secret');
		  $nexmo_client->octabook_nexmo_form = get_option('octabook_nexmo_form');
		  /* Send SMS To Client */
		  if(get_option('octabook_nexmo_send_sms_client_status') == "E"){
			$template = $obj_sms_template->gettemplate_sms("C",'e',$booking_method.'C');					
			 if($template[0]->sms_template_status == "e" && $client_phone!=''){
				if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$booking_reject_reason,$booking_cancel_reason);
					
					$client_sms_body = str_replace($search,$replace_with,$message);
					$nexmo_client->send_nexmo_sms($client_ccode.$client_phone,$client_sms_body);
			}
		  }
		  /* Send SMS To Staff */
		  if(get_option('octabook_nexmo_send_sms_sp_status') == "E"){
			$template = $obj_sms_template->gettemplate_sms("SP",'e',$booking_method.'S');					
				if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$staff_sms_body = str_replace($search,$replace_with,$message);
					$nexmo_client->send_nexmo_sms($staffinfo[0]['phone'],$client_sms_body);
				}
		  }
		  /* Send SMS To Admin */
		  if(get_option('octabook_nexmo_send_sms_admin_status') == "E"){
			$template = $obj_sms_template->gettemplate_sms("AM",'e',$booking_method.'A');					
				if($template[0]->sms_template_status == "e" && get_option('octabook_nexmo_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$staff_sms_body = str_replace($search,$replace_with,$message);
					$nexmo_client->send_nexmo_sms(get_option('octabook_nexmo_ccode').get_option('octabook_nexmo_admin_phone_no'),$client_sms_body);
				}
		  }
		  
		}
		/* Nexmo SMS Sending End Here */
		/* Textlocal SMS sending Start */
		if(get_option('octabook_sms_noti_textlocal')=="E"){
			$textlocal_api_key = get_option('octabook_textlocal_apikey');
			$textlocal_sender = get_option('octabook_textlocal_sender');
			/*$client_phone = get_option('octabook_textlocal_admin_phone_no');*/
		
		  /* Send SMS To Client */
		  if(get_option('octabook_textlocal_client_sms_notification_status') == "E"){
			$template = $obj_sms_template->gettemplate_sms("C",'e',$booking_method.'C');
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
					if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
					else
					{
						$message = strip_tags($template[0]->sms_message);
					}
			}
			$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
			$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$booking_reject_reason,$booking_cancel_reason);
			
			$message = str_replace($search,$replace_with,$message);
			$textlocal_numbers = $user_ccode.''.$client_phone;
			$data = 'apikey=' . $textlocal_api_key . '&numbers=' . $textlocal_numbers . "&sender=" . $textlocal_sender . "&message=" . $message;
			
			$ch = curl_init('https://api.textlocal.in/send/');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
		  }
		
		  /* Send SMS To Admin */
		  if(get_option('octabook_textlocal_admin_sms_notification_status') == "E"){
			$textlocal_api_key = get_option('octabook_textlocal_apikey');
			$textlocal_sender = get_option('octabook_textlocal_sender');
			$client_phone = get_option('octabook_textlocal_ccode').get_option('octabook_textlocal_admin_phone_no');
			$template = $obj_sms_template->gettemplate_sms("AM",'e',$booking_method.'A');					
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
				if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
				else
					{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$message = str_replace($search,$replace_with,$message);
					
					$data = 'apikey=' . $textlocal_api_key . '&numbers=' . $client_phone . "&sender=" . $textlocal_sender . "&message=" . $message;
					
					$ch = curl_init('https://api.textlocal.in/send/');
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					
				}
		  }
		  /*Send SMS To Service Provider*/
		 if(get_option('octabook_textlocal_service_provider_sms_notification_status') == "E"){
		  $textlocal_api_key = get_option('octabook_textlocal_apikey');
			$textlocal_sender = get_option('octabook_textlocal_sender');
			$template = $obj_sms_template->gettemplate_sms("SP",'e',$booking_method.'S');
				if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
				if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
				else
					{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$message = str_replace($search,$replace_with,$message);
        	$data = 'apikey=' . $textlocal_api_key . '&numbers=' . $staffinfo[0]['phone'] . "&sender=" . $textlocal_sender . "&message=" . $message;
        			
					$ch = curl_init('https://api.textlocal.in/send/');
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					
				}
		  }
		  
		}

		/* MSG91 SMS sending Start */
		if(get_option('octabook_sms_noti_msg91')=="E"){
			$msg91_api_key = get_option('octabook_msg91_apikey');
			$msg91_sender = get_option('octabook_msg91_sender');
			// $client_phone = get_option('octabook_msg91_admin_phone_no');
		
		  /* Send SMS To Client */
		  if(get_option('octabook_msg91_client_sms_notification_status') == "E"){
			$template = $obj_sms_template->gettemplate_sms("C",'e',$booking_method.'C');
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
					if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
					else
					{
						$message = strip_tags($template[0]->sms_message);
					}
			}
			$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
			$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$booking_reject_reason,$booking_cancel_reason);
			
			$message = str_replace($search,$replace_with,$message);
			$msg91_numbers = $user_ccode.''.$client_phone;
			$user_ccode = '91';
			$data = "{ \"sender\": \"$msg91_sender\", \"route\": \"4\", \"country\": \"$user_ccode\", \"sms\": [ { \"message\": \"$message\", \"to\": [ \"$client_phone\" ] } ] }";
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_HTTPHEADER => array(
				"authkey: $msg91_api_key",
				"content-type: application/json"
			),
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
		  }
		
		  /* Send SMS To Admin */
		  if(get_option('octabook_msg91_admin_sms_notification_status') == "E"){
			$msg91_api_key = get_option('octabook_msg91_apikey');
			$msg91_sender = get_option('octabook_msg91_sender');
			$client_phone = get_option('octabook_msg91_admin_phone_no');
			$template = $obj_sms_template->gettemplate_sms("AM",'e',$booking_method.'A');					
				if($template[0]->sms_template_status == "e" && $client_phone!=''){
				if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
				else
					{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$message = str_replace($search,$replace_with,$message);
					
					$user_ccode = '91';
					$data = "{ \"sender\": \"$msg91_sender\", \"route\": \"4\", \"country\": \"$user_ccode\", \"sms\": [ { \"message\": \"$message\", \"to\": [ \"$client_phone\" ] } ] }";
					
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $data,
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_HTTPHEADER => array(
						"authkey: $msg91_api_key",
						"content-type: application/json"
					),
					));
					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);
					
				}
		  }
		  /*Send SMS To Service Provider*/
		 if(get_option('octabook_msg91_service_provider_sms_notification_status') == "E"){
		  $msg91_api_key = get_option('octabook_msg91_apikey');
			$msg91_sender = get_option('octabook_msg91_sender');
			$template = $obj_sms_template->gettemplate_sms("SP",'e',$booking_method.'S');
				if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
				if($template[0]->sms_message == "")
					{
						$message = strip_tags($template[0]->default_message);
					}
				else
					{
						$message = strip_tags($template[0]->sms_message);
					}
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}','{{appoinment_client_detail}}','{{appointment_reject_reason}}','{{appointment_cancel_reason}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo,$sms_client_info,$booking_reject_reason,$booking_cancel_reason);
					
					$message = str_replace($search,$replace_with,$message);
					$user_ccode = '91';
					$data = "{ \"sender\": \"$msg91_sender\", \"route\": \"4\", \"country\": \"$user_ccode\", \"sms\": [ { \"message\": \"$message\", \"to\": [ \"$staffinfo[0]['phone']\" ] } ] }";
					
					$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $data,
					CURLOPT_SSL_VERIFYHOST => 0,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_HTTPHEADER => array(
						"authkey: $msg91_api_key",
						"content-type: application/json"
					),
					));
					$response = curl_exec($curl);
					$err = curl_error($curl);
					curl_close($curl);
					
				}
		  }
		  
		}
	}
	/* Send SMS Notification End Here */	
}
/** Get Service Chart Analytics Info **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='view_chart_analytics'){
	
	global $current_user;
	$current_user = wp_get_current_user();
	$info = get_userdata( $current_user->ID );
	/* Coupon Detail */
	$coupons->location_id = $_SESSION['oct_location'];
	$couponsinfo = $coupons->readAll();
	if(isset($info->caps['administrator'])){
	/* Service Detail */
	$service->location_id= $_SESSION['oct_location'];
	$servicesInfo =  $service->readAll();
	/* Staff Detail */
	$staff->location_id =$_SESSION['oct_location'];
	$staffsinfo = $staff->readAll_with_disables();   
	
	}else{
	/* Service Detail */
	$service->provider_id=  $current_user->ID;
	$servicesInfo =  $service->readall_services_of_provider();
	/* Staff Detail */
	$staff->id =  $current_user->ID;
	$staffsinfo = $staff->readOne();   
	}
	
	$chart_data_array = array();
	if(isset($_POST['method']) && $_POST['method']=='service'){
		foreach($servicesInfo as $serviceInfo){
			$oct_bookings->service_id = $serviceInfo->id;
			$totalbookings = $oct_bookings->readall_bookings_by_service_id();
			if($totalbookings>0){
				$chart_data_array[]=array(
						"x"=>$totalbookings,
						"y"=>"$serviceInfo->service_title"
				);
			}
		}
	}
	if(isset($_POST['method']) && $_POST['method']=='provider'){
		foreach($staffsinfo as $staff_info){
		$oct_bookings->provider_id = $staff_info['id'];
		$totalbookings = $oct_bookings->readall_bookings_by_provider_id();
			if($totalbookings>0){
				$chart_data_array[]=array(
						"x"=>$totalbookings,
						"y"=>ucfirst($staff_info['staff_name'])
					);
			}
		}
	
	}
	if(isset($_POST['method']) && $_POST['method']=='coupon'){
		foreach($couponsinfo as $couponinfo){
			if($couponinfo->coupon_used>0){
				$chart_data_array[]=array(
					"x"=>"$couponinfo->coupon_used",
					"y"=>"$couponinfo->coupon_code"
				);
			}	
		}	
	}
	$json_chart_data =  json_encode($chart_data_array);	
	echo $json_chart_data;
	die();
}

if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='view_chart_analytics_dropdown'){	
	global $current_user;
	$current_user = wp_get_current_user();
	$info = get_userdata( $current_user->ID );

	if(isset($info->caps['administrator'])){
	/* Service Detail */
	$service->location_id= $_SESSION['oct_location'];
	$servicesInfo =  $service->readAll();
	/* Staff Detail */
	$staff->location_id =$_SESSION['oct_location'];
	$staffsinfo = $staff->readAll_with_disables();   
	
	}else{
	/* Service Detail */
	$service->provider_id=  $current_user->ID;
	$servicesInfo =  $service->readall_services_of_provider();
	/* Staff Detail */
	$staff->id =  $current_user->ID;
	$staffsinfo = $staff->readOne();   
	}

	/*Get Todays service and Providers Data for map */
	if(isset($_POST['method']) && $_POST['method']=='today'){
		
		$today_chart_service_data_array = array();
		$today_chart_provider_data_array = array();
		foreach($servicesInfo as $serviceInfo){
				$oct_bookings->service_id = $serviceInfo->id;
				$dateParam = date('Y-m-d');
				$oct_bookings->firstWeek = $dateParam;
				$oct_bookings->endWeek = $dateParam;
				$totalbookings = $oct_bookings->readall_bookings_by_service_id_for_date();
				if($totalbookings>0){
					$today_chart_service_data_array[] = array(
							"x"=>$totalbookings,
							"y"=>"$serviceInfo->service_title"
					);
				}
		}

			if(!empty($today_chart_service_data_array)) {
				$json_chart_data =  $today_chart_service_data_array;	
			}
			
			echo $json = json_encode($json_chart_data);
			die();
	}
	
	/*Get Week service and Providers Data for map */

	if(isset($_POST['method']) && $_POST['method']=='week'){
	$week_chart_service_data_array = array();
		foreach($servicesInfo as $serviceInfo){
				$oct_bookings->service_id = $serviceInfo->id;
				$dateParam = date('Y-m-d H:i:s');
				$week = date('w', strtotime($dateParam));
				$date = new DateTime($dateParam);
				$firstWeek = $date->modify("-".$week." day")->format("Y-m-d");
				$endWeek = $date->modify("+6 day")->format("Y-m-d");
				$oct_bookings->firstWeek = $firstWeek;
				$oct_bookings->endWeek = $endWeek;
				$totalbookings = $oct_bookings->readall_bookings_by_service_id_for_date();
				if($totalbookings>0){
					$week_chart_service_data_array[] =array(
							"x"=>$totalbookings,
							"y"=>"$serviceInfo->service_title"
					);
				}
		}

		if(!empty($week_chart_service_data_array)) {
				$json_chart_data =  $week_chart_service_data_array;	
		
		}

		echo $json = json_encode($json_chart_data);
		
		die();
	}
	
	
	/*Get Month service and Providers Data for map */
	
	
	if(isset($_POST['method']) && $_POST['method']=='month'){
		$month_chart_service_data_array = array();
		foreach($servicesInfo as $serviceInfo){
				$oct_bookings->service_id = $serviceInfo->id;
				$first_day_this_month = date('Y-m-01');
				$last_day_this_month  = date('Y-m-t');
				$oct_bookings->firstWeek = $first_day_this_month;
				$oct_bookings->endWeek = $last_day_this_month;
				$totalbookings = $oct_bookings->readall_bookings_by_service_id_for_date();
				if($totalbookings>0){
					$month_chart_service_data_array[] =array(
							"x"=>$totalbookings,
							"y"=>"$serviceInfo->service_title"
					);
				}
		}
			if(!empty($month_chart_service_data_array)) {
					$json_chart_data =  $month_chart_service_data_array;	
			}

			echo $json = json_encode($json_chart_data);
			die();
	}
}

if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='view_chart_analytics_dropdown_provider'){	
	global $current_user;
	$current_user = wp_get_current_user();
	$info = get_userdata( $current_user->ID );

	if(isset($info->caps['administrator'])){
	/* Service Detail */
	$service->location_id= $_SESSION['oct_location'];
	$servicesInfo =  $service->readAll();
	/* Staff Detail */
	$staff->location_id =$_SESSION['oct_location'];
	$staffsinfo = $staff->readAll_with_disables();   
	
	}else{
	/* Service Detail */
	$service->provider_id=  $current_user->ID;
	$servicesInfo =  $service->readall_services_of_provider();
	/* Staff Detail */
	$staff->id =  $current_user->ID;
	$staffsinfo = $staff->readOne();   
	}

	/*Get Todays service and Providers Data for map */
	if(isset($_POST['method']) && $_POST['method']=='today'){

		$today_chart_provider_data_array = array();
		foreach($staffsinfo as $staff_info){
			$oct_bookings->provider_id = $staff_info['id'];
			$dateParam = date('Y-m-d');
			$oct_bookings->firstWeek = $dateParam;
			$oct_bookings->endWeek = $dateParam;
			$totalbookings = $oct_bookings->readall_bookings_by_provider_id_for_date();
		
				if($totalbookings>0){
					$today_chart_provider_data_array[] = array(
							"x"=>$totalbookings,
							"y"=>ucfirst($staff_info['staff_name'])
						);
				}
			} 
			if(!empty($today_chart_provider_data_array)){
				$json_chart_data =  $today_chart_provider_data_array;	
			} 
			echo $json = json_encode($json_chart_data);
			die();
	}
	
	/*Get Week service and Providers Data for map */

	if(isset($_POST['method']) && $_POST['method']=='week'){
	$week_chart_provider_data_array = array();
	foreach($staffsinfo as $staff_info){
			$oct_bookings->provider_id = $staff_info['id'];
			$dateParam = date('Y-m-d H:i:s');
			$week = date('w', strtotime($dateParam));
			$date = new DateTime($dateParam);
			$firstWeek = $date->modify("-".$week." day")->format("Y-m-d");
			$endWeek = $date->modify("+6 day")->format("Y-m-d");
			$oct_bookings->firstWeek = $firstWeek;
			$oct_bookings->endWeek = $endWeek;
			$totalbookings = $oct_bookings->readall_bookings_by_provider_id_for_date();
				if($totalbookings>0){
					$week_chart_provider_data_array[] =array(
							"x"=>$totalbookings,
							"y"=>ucfirst($staff_info['staff_name'])
						);
				}
			}
			if(!empty($week_chart_provider_data_array)){
				$json_chart_data =  $week_chart_provider_data_array;	
			} 
		echo $json = json_encode($json_chart_data);
		die();
	}
	
	/*Get Month service and Providers Data for map */
	
	if(isset($_POST['method']) && $_POST['method']=='month'){
	  $month_chart_provider_data_array = array();
	
 		foreach($staffsinfo as $staff_info){
			$oct_bookings->provider_id = $staff_info['id'];
			$dateParam = date('Y-m-d H:i:s');
			$week = date('w', strtotime($dateParam));
			$date = new DateTime($dateParam);
			$firstWeek = $date->modify("-".$week." day")->format("Y-m-d");
			$endWeek = $date->modify("+6 day")->format("Y-m-d");
			$oct_bookings->firstWeek = $firstWeek;
			$oct_bookings->endWeek = $endWeek;
			$totalbookings = $oct_bookings->readall_bookings_by_provider_id_for_date();
				if($totalbookings>0){
					$month_chart_provider_data_array[] =array(
							"x"=>$totalbookings,
							"y"=>ucfirst($staff_info['staff_name'])
						);
				}
			} 
		 	if(!empty($month_chart_provider_data_array)){
					$json_chart_data = $month_chart_provider_data_array;	
			}
			echo $json = json_encode($json_chart_data);
			die();
	}
}





/** Get Notification Count **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_notification_count'){
		if(isset($_SESSION['oct_location'])){
		$oct_bookings->location_id = $_SESSION['oct_location'];
			if($oct_bookings->get_notifications_count()>0){
				echo '<span id="oct-notification-top" class="get_notification_rem">'.$oct_bookings->get_notifications_count().'</span>';
			}
		}
}	
/** Get Notification Bookings **/
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_notification_bookings'){
		$oct_bookings->location_id = $_SESSION['oct_location'];
		$notificationbookings = $oct_bookings->get_notifications_bookings();
		function time_elapsed_string($datetime, $full = false) {
			$now = new DateTime;
			$ago = new DateTime($datetime);
			$diff = $now->diff($ago);

			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;

			$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}
		if(sizeof((array)$notificationbookings)>0){			
			foreach($notificationbookings as $notificationbooking){								
					$service->id= $notificationbooking->service_id;
					$service->readone();
					$service_title=stripslashes_deep($service->service_title);
					$servicedurationstrinng = '';
					if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
					if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }
					$staff->id=$notificationbooking->provider_id;
					$staff_info = $staff->readOne();   
					$provider_name = ucfirst($staff_info[0]['staff_name']);				
					$clients->order_id=$notificationbooking->order_id;
					$client_info = $clients->get_client_info_by_order_id();
					$clientname= $client_info[0]->client_name;
					?>					
					<li class="oct-today-list" id="oct_notification<?php echo $notificationbooking->id;?>" data-bookingid = '<?php echo $notificationbooking->id;?>' data-toggle="modal" data-target="#booking-details">
								<div class="list-inner">
						<span class="booking-text"><?php if($notificationbooking->booking_status=='A' || $notificationbooking->booking_status==''){
							echo '<span class="oct-label btn-info br-2">'.__('Active','oct').'</span>';
							}elseif($notificationbooking->booking_status=='C'){
								echo '<span class="oct-label btn-success br-2">'.__('Confirm','oct').'</span>';
							}elseif($notificationbooking->booking_status=='R'){
								echo '<span class="oct-label btn-danger br-2 ">'.__('Reject','oct').'</span>';
							}elseif($notificationbooking->booking_status=='RS'){
								echo '<span class="oct-label btn-primary br-2">'.__('Rescheduled','oct').'</span>';
							}elseif($notificationbooking->booking_status=='CC'){
								echo '<span class="oct-label btn-default br-2">'.__('Cancel By Client','oct').'</span>';
							}elseif($notificationbooking->booking_status=='CS'){
								echo '<span class="oct-label btn-default br-2">'.__('Cancel By Service Provider','oct').'</span>';
							}elseif($notificationbooking->booking_status=='CO'){
								echo '<span class="oct-label btn-success br-2">'.__('Completed','oct').'</span>';
							}else{
								echo '<span class="oct-label btn-danger br-2">'.__('Mark As No Show','oct').'</span>';
							} ?><span class="oct-noti-text">  <?php echo $clientname;?> <?php echo __('for a','oct');?> <?php echo __(stripslashes_deep($service_title),"oct");?> <?php echo __('on','oct');?> <?php echo date_i18n('d-M-Y',strtotime($notificationbooking->booking_datetime)); ?> <?php echo __('@','oct');?> <?php echo date_i18n(get_option('time_format'),strtotime($notificationbooking->booking_datetime)); ?> <?php echo __('with','oct');?><b> <?php echo __(stripslashes_deep($provider_name),"oct");?></b></span></span>
						<span class="booking-time">
						<?php echo time_elapsed_string($notificationbooking->lastmodify); //echo $timeago;?> </span><?php  if(!$notificationbooking->notification) { ?><a data-booking_id="<?php echo $notificationbooking->id;?>" class="pull-right oct-mark-read oct_unread_notification" href="javascript:void(0);"><?php echo __("mark as read","oct");?></a> <?php   } ?>
						</div>
					</li>					
				<?php									
			}			
		}else{ ?>
			<div class="list-inner oct-no-notification">
				<i class="fa fa-clock-o fa-3x"></i>
				<div class="booking-text">
					<h4><?php echo __('No notification found','oct');?></h4>
				</div>
			
			</div>
			<?php 		
		}
}	
/** Mark Notification As Readed **/
if(isset($_POST['general_ajax_action'],$_POST['booking_id']) && $_POST['general_ajax_action']=='remove_notifications_bookings' && $_POST['booking_id']!=''){
		$oct_bookings->location_id = $_SESSION['oct_location'];
		$oct_bookings->booking_id = $_POST['booking_id'];
		$oct_bookings->remove_notifications_bookings();
}
/** Client Area Order Bookings Detail **/
if(isset($_POST['general_ajax_action'],$_POST['order_id'],$_POST['client_id']) && $_POST['general_ajax_action']=='get_client_order_bookings' && $_POST['order_id']!='' && $_POST['client_id']!=''){
		$oct_bookings->client_id = $_POST['client_id'];
		$oct_bookings->order_id = $_POST['order_id'];
		$clientorderbookings = $oct_bookings->get_client_bookings_by_order_id();
		
		foreach($clientorderbookings as $clientorderbooking){								
					
					$service->id= $clientorderbooking->service_id;
					$service->readone();
					$service_title=stripslashes_deep($service->service_title);
					$servicedurationstrinng = '';
					if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
					if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }
					$staff->id=$clientorderbooking->provider_id;
					$staff_info = $staff->readOne();   
					$provider_name = ucfirst($staff_info[0]['staff_name']);				
					
					if($clientorderbooking->booking_status=='A' || $clientorderbooking->booking_status==''){
						$bookingstatus =  __('Active','oct');
						$statusNote = '-';
					}elseif($clientorderbooking->booking_status=='C'){
						$bookingstatus = __("Confirm",'oct');
						$statusNote = $clientorderbooking->confirm_note;
					}elseif($clientorderbooking->booking_status=='R'){
						$bookingstatus = __("Reject",'oct');
						$statusNote = $clientorderbooking->reject_reason;
					}elseif($clientorderbooking->booking_status=='RS'){
						$bookingstatus = __("Rescheduled",'oct');
						$statusNote = $clientorderbooking->reschedule_note;
					}elseif($clientorderbooking->booking_status=='CC'){
						$bookingstatus =  __("Cancel By Client",'oct');
						$statusNote = $clientorderbooking->cancel_reason;
					}elseif($clientorderbooking->booking_status=='CS'){
						$bookingstatus = __("Cancel By Service Provider",'oct');
						$statusNote = $clientorderbooking->cancel_reason;
					}elseif($clientorderbooking->booking_status=='CO'){
						$bookingstatus =  __("Completed",'oct');
						$statusNote = '-';
					}else{					
						$bookingstatus =  __("Mark As No Show",'oct');
						$statusNote = '-';
					}
					/* Cancelation Buffer Calculation */
					if(strtotime(date_i18n('Y-m-d',strtotime($clientorderbooking->booking_datetime)))>=strtotime(date_i18n('Y-m-d'))){
					$booking_dt = strtotime(date_i18n('Y-m-d H:i:s',strtotime($clientorderbooking->booking_datetime)));
					}else{
					$booking_dt = strtotime(date_i18n('Y-m-d H:i:s'));
					}
					$curr_dt = strtotime(date_i18n('Y-m-d H:i:s'));
					 $remaining_mins  = round(abs($booking_dt - $curr_dt)/60);
					//$remaining_mins   = round($diff / 60);
			?>
				<tr>				
					<td><?php echo $clientorderbooking->order_id;?></td>
					<td><?php echo $provider_name;?></td>
					<td><?php echo $service_title;?></td>
					<td><?php echo date_i18n(get_option('date_format'),strtotime($clientorderbooking->booking_datetime)); ?> <?php echo date_i18n(get_option('time_format'),strtotime($clientorderbooking->booking_datetime)); ?></td>
					<td><?php echo $bookingstatus;?></td>
					<td><?php echo $statusNote;?></td>
					<td>
					<?php if($remaining_mins > get_option('octabook_reschedule_buffer_time') ){ ?>
					<a href="javascript:void(0);" data-bookingid="<?php echo $clientorderbooking->id;?>" data-toggle="modal" data-target="#edit-booking-details-view" class=" btn btn-success oct-today-list" title="<?php echo __("Reschedule","oct"); ?>"><i class="fa fa-repeat"></i></a><?php } ?>
					
					<?php if($remaining_mins > get_option('octabook_cancellation_buffer_time')  && ($clientorderbooking->booking_status=='C' || $clientorderbooking->booking_status=='A')){ ?>
					<a id="cancel-appointment-cal-popup<?php echo $clientorderbooking->id;?>" class="btn oct-small-btn btn-danger oct_client_cancel_appointmentpopup" rel="popover" data-placement='bottom' title="<?php echo __("Cancel reason?","oct");?>"><i class="fas fa-ban"></i></a>
					
					<div id="popover-cancel-appointment-cal-popup<?php echo $clientorderbooking->id;?>" style="display: none;">
						<div class="arrow"></div>
						<table class="form-horizontal" cellspacing="0">
							<tbody>
								<tr>
									<td><textarea class="form-control" id="oct_booking_cancelnote<?php echo $clientorderbooking->id;?>" name="" placeholder="<?php echo __("Appointment Cancel Reason","oct");?>" required="required" ></textarea></td>
								</tr>
								<tr>
									<td>
										<a href="javascript:void(0);" id="oct_booking_cancel" data-booking_id="<?php echo $clientorderbooking->id;?>" data-method='CC'  data-sp='Y' value="Cancel By Client" class="btn btn-danger btn-sm oct_crc_appointment" type="submit"><?php echo __("Ok","oct");?></a>
										<a class="btn btn-default btn-sm oct_cancel_clientcancel" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>	<?php } ?>	
					<?php if(get_option('octabook_reviews_status')=='E'){ 
						/* Get Booking Review */
						$reviews->booking_id = $clientorderbooking->id;
						$reviewinfo = $reviews->readOne_by_booking_id();	?>
					<a id="client-review-popup<?php echo $clientorderbooking->id;?>" class="btn btn-info oct-add-review-client-btn" rel="popover" data-placement='bottom'  title="<?php echo __("Add Review","oct");?>"><i class="fa fa-star"></i></a>
						<div id="popover-client-review-popup<?php echo $clientorderbooking->id;?>" style="display: none;">
						<div class="arrow"></div>
						<table class="form-horizontal" cellspacing="0">
							<tbody>
								<tr>
									<td>
										<fieldset class="rating">
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='5'){ echo "checked='checked'";} ?> type="radio" id="star5<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="5" /><label class="full" for="star5<?php echo $clientorderbooking->id;?>" title="<?php echo __("Awesome - 5 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='4.5'){ echo "checked='checked'";} ?> type="radio" id="star4half<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="4.5" /><label class="half" for="star4half<?php echo $clientorderbooking->id;?>" title="<?php echo __("Pretty good - 4.5 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='4'){ echo "checked='checked'";} ?> type="radio" id="star4<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="4" /><label class="full" for="star4<?php echo $clientorderbooking->id;?>" title="<?php echo __("Pretty good - 4 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='3.5'){ echo "checked='checked'";} ?> type="radio" id="star3half<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="3.5" /><label class="half" for="star3half<?php echo $clientorderbooking->id;?>" title="<?php echo __("Meh - 3.5 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='3'){ echo "checked='checked'";} ?> type="radio" id="star3<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="3" /><label class="full" for="star3<?php echo $clientorderbooking->id;?>" title="<?php echo __("Meh - 3 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='2.5'){ echo "checked='checked'";} ?> type="radio" id="star2half<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="2.5" /><label class="half" for="star2half<?php echo $clientorderbooking->id;?>" title="<?php echo __("Kinda bad - 2.5 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='2'){ echo "checked='checked'";} ?> type="radio" id="star2<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="2" /><label class="full" for="star2<?php echo $clientorderbooking->id;?>" title="<?php echo __("Kinda bad - 2 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='1.5'){ echo "checked='checked'";} ?> type="radio" id="star1half<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="1.5" /><label class="half" for="star1half<?php echo $clientorderbooking->id;?>" title="<?php echo __("Meh - 1.5 stars","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='1'){ echo "checked='checked'";} ?> type="radio" id="star1<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="1" /><label class="full" for="star1<?php echo $clientorderbooking->id;?>" title="<?php echo __("Sucks big time - 1 star","oct");?>"></label>
										  <input <?php if(isset($reviewinfo[0]->rating) && $reviewinfo[0]->rating=='0.5'){ echo "checked='checked'";} ?> type="radio" id="starhalf<?php echo $clientorderbooking->id;?>" name="octabook_rating<?php echo $clientorderbooking->id;?>" value="0.5" /><label class="half" for="starhalf<?php echo $clientorderbooking->id;?>" title="<?php echo __("Sucks big time - 0.5 stars","oct");?>"></label>
										 
										</fieldset>
									</td>
								</tr>
								<tr>
									<td>
										<label><?php echo __("Write Review","oct");?></label>
										<textarea id="octabook_review_desc<?php echo $clientorderbooking->id;?>" class="review-textarea form-control"><?php if(isset($reviewinfo[0]->description)){ echo $reviewinfo[0]->description;} ?></textarea>
									</td>
								</tr>
								<tr>
									<td>
										<a href="javascript:void(0);" id="oct_booking_submitreview" data-booking_id="<?php echo $clientorderbooking->id;?>" data-method='<?php if(isset($reviewinfo[0]->description,$reviewinfo[0]->rating)){echo "U";}else{ echo "C";} ?>'  data-pid="<?php echo $clientorderbooking->provider_id;?>"  data-cid="<?php echo $clientorderbooking->client_id;?>" data-review_id="<?php if(isset($reviewinfo[0]->description,$reviewinfo[0]->rating)){echo $reviewinfo[0]->id;}else{ echo "0";} ?>"  class="btn btn-success"><?php if(isset($reviewinfo[0]->description,$reviewinfo[0]->rating)){ echo __("Update Review","oct"); }else{ echo __("Submit Review","oct"); } ?></a>
										
										<a class="btn btn-default oct_cancel_review_pop" href="javascript:void(0)"><?php echo __("Cancel","oct");?></a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>	
					<?php } ?>	
						
						
						
					</td>
				</tr>	
		<?php }
}
/* Create/Update Client Review */
if(isset($_POST['general_ajax_action'],$_POST['review_booking_id'],$_POST['client_id'],$_POST['provider_id']) && $_POST['general_ajax_action']=='octabook_clientreview' && $_POST['review_booking_id']!='' && $_POST['client_id']!='' && $_POST['provider_id']!=''){
		if($_POST['method']=='C'){
		$oct_bookings->booking_id = $_POST['review_booking_id'];
		$oct_bookings->readOne_by_booking_id();
		$reviews->location_id = $oct_bookings->location_id;
		$reviews->booking_id = $_POST['review_booking_id'];
		$reviews->provider_id = $_POST['provider_id'];
		$reviews->client_id = $_POST['client_id'];
		}
		$reviews->status = 'A';
		$reviews->rating = $_POST['rating'];
		$reviews->description = $_POST['description'];
		if($_POST['method']=='C'){
			$reviews->create();
		}else{
			$reviews->id = $_POST['review_id'];
			$reviews->update();
		}
}


/** Download Client Invoice--From Client Dashboard **/
if(isset($_GET['general_ajax_action'],$_GET['order_id'],$_GET['client_id'],$_GET['key']) && $_GET['general_ajax_action']=='client_download_invoice' && $_GET['order_id']!='' && $_GET['client_id']!='' && $_GET['key']!=''){
	
	$keystring =  substr($_GET['key'], 1, -1);
	$decodedkey = base64_decode($keystring);
	$validatekey = $decodedkey-1247;
	if($validatekey!=$_GET['order_id']){
		echo __("Invalid key supplied.","oct"); die();
	}
	
	include(dirname(dirname(dirname(__FILE__))).'/assets/pdf/tfpdf/tfpdf.php');
	
	$oct_bookings->order_id = $_GET['order_id'];
	$oct_bookings->client_id = $_GET['client_id'];
	$clientorderbookings = $oct_bookings->get_client_bookings_by_order_id();
	
		
					
	$invoice_number = strtoupper(date_i18n('M',strtotime($clientorderbookings[0]->lastmodify))).'-'.$_GET['client_id'];
	$invoice_date = date_i18n(get_option('date_format'),strtotime($clientorderbookings[0]->lastmodify));	
	
	/*Client info*/
	$order_info->order_id = $_GET['order_id'];
	$order_info->readOne_by_order_id();
	$client_personal_info=unserialize($order_info->client_personal_info);

	/*Payment Info */
	$payments->order_id = $_GET['order_id'];
	$payments->read_one_by_order_id();	
	$payments->payment_method;
	if($payments->payment_method == 'paypal') { $pay_type = __('Paypal','oct'); }
	elseif($payments->payment_method == 'pay_locally') { $pay_type = __('Pay Locally','oct'); }
	elseif($payments->payment_method == 'Free') {  $pay_type = __('Free','oct');}
	elseif($payments->payment_method == 'stripe'){ $pay_type = __('Stripe','oct'); }
	elseif($payments->payment_method =='authorizenet'){$pay_type = __('Authorize .Net','oct');}
	else{$pay_type = '-';} 
	$net_amount = $general->oct_price_format_for_pdf($payments->net_total);
	$discount = $general->oct_price_format_for_pdf($payments->discount);
	$taxes = $general->oct_price_format_for_pdf($payments->taxes);
	$partial = $general->oct_price_format_for_pdf($payments->partial);
	
	
	$backgroundimage=$plugin_url_for_ajax."/assets/images/client_inv.jpg";
	if(get_option('octabook_company_logo')==''){
		$companylogo=$plugin_url_for_ajax."/assets/images/company.png";
	}else{
		$companylogo=site_url()."/wp-content/uploads".get_option('octabook_company_logo');	
	}
	
	$pdf = new tFPDF();
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->SetFont('DejaVu','',14);
	$pdf->SetMargins(0,0);
	$pdf->SetTopMargin(0);
	$pdf->SetAutoPageBreak(true,0);
	$pdf->AddPage();
	$pdf->SetFillColor(242,242,242);
    $pdf->SetTextColor(102,103,102);
    $pdf->SetDrawColor(128,255,0);
    $pdf->SetLineWidth(0);
   
	$pdf->Cell(210,297,'',0,1,'C',true);
	$pdf->Image($backgroundimage,0,0,210);
	
	$pdf->Image($companylogo,20,15,20); 
	$pdf->SetFont('DejaVu','',9);
	$pdf->Text(130,12,get_option('octabook_company_name'));
	$pdf->Text(130,17,get_option('octabook_company_address'));
	$pdf->Text(130,22,get_option('octabook_company_city').",".get_option('octabook_company_state'));
	$pdf->Text(130,27,get_option('octabook_company_country'));
	$pdf->Text(130,30,get_option('octabook_company_phone'));
	$pdf->Text(130,33,get_option('octabook_company_email'));
	
	$pdf->SetFont('DejaVu','',15);
	$pdf->Text(21,56,__("INVOICE TO:","oct"));
	
	$pdf->SetFont('DejaVu','',12);
	$pdf->Text(21,63,ucwords($order_info->client_name));
	
	$pdf->SetFont('DejaVu','',8);
	if(isset($client_personal_info['address'])){
	$pdf->Text(21,68,$client_personal_info['address']);
	}
	if(isset($client_personal_info['city'],$client_personal_info['state'])){
	$pdf->Text(21,72,$client_personal_info['city'].",".$client_personal_info['state']);
	}
	if(isset($client_personal_info['country'])){
	$pdf->Text(21,76,$client_personal_info['country']);	
	}
	$pdf->Text(30,82,$order_info->client_phone);
	$pdf->Text(30,88,$order_info->client_email);
	
	$pdf->SetFont('DejaVu','',28);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text(95,62,__("INVOICE #","oct").strtoupper(date_i18n('M',strtotime($clientorderbookings[0]->lastmodify)))."-".sprintf("%04d",$_GET['order_id']));


	$pdf->SetFont('DejaVu','',7);
	$pdf->SetTextColor(102,103,102);
	$pdf->Text(109 - $pdf->GetStringWidth(__("Invoice Date","oct"))/2,77,__("Invoice Date","oct"));
	$pdf->Text(182 - $pdf->GetStringWidth(__("Payment Method","oct"))/2,77,__("Payment Method","oct"));
	
	$pdf->SetFont('DejaVu','',10);
   
	$pdf->Text(109 - $pdf->GetStringWidth(date_i18n(get_option('date_format'),strtotime($clientorderbookings[0]->lastmodify)))/2,82,date_i18n(get_option('date_format'),strtotime($clientorderbookings[0]->lastmodify)));
	$pdf->Text(181 - $pdf->GetStringWidth(strtoupper($pay_type))/2,82,strtoupper($pay_type));
	$pdf->SetFont('DejaVu','',13.5);
	$pdf->Text(20,107,__("Description","oct"));
	$pdf->Text(60,107,__("Duration","oct"));
	$pdf->Text(90,107,__("Provider","oct"));
	$pdf->Text(120,107,__("Date","oct"));
	$pdf->Text(150,107,__("Time","oct"));
	$pdf->Text(180,107,__("Price","oct"));
	
	$pdf->SetFont('DejaVu','',8);
	
	$addondetails_startpoint = 120;
	foreach($clientorderbookings as $clientorderbooking){								
		$service->id= $clientorderbooking->service_id;
		$service->readone();	
		$servicedurationstrinng = '';
		if(floor($service->duration/60)!=0){ $servicedurationstrinng .= floor($service->duration/60); $servicedurationstrinng .= __(" Hrs","oct"); } 
		if($service->duration%60 !=0){  $servicedurationstrinng .= $service->duration%60; $servicedurationstrinng .= __(" Mins","oct"); }
		$staff->id=$clientorderbooking->provider_id;
		$staff_info = $staff->readOne();   
	  
	$pdf->Text(20,$addondetails_startpoint,$service->service_title);
	 $pdf->Text(60,$addondetails_startpoint,$servicedurationstrinng);
	$pdf->Text(90,$addondetails_startpoint,ucfirst($staff_info[0]['staff_name']));
	$pdf->Text(120,$addondetails_startpoint,date_i18n('M d,Y',strtotime($clientorderbooking->booking_datetime)));
	$pdf->Text(150,$addondetails_startpoint,date_i18n(get_option('time_format'),strtotime($clientorderbooking->booking_datetime)).'-'.date_i18n(get_option('time_format'),strtotime($clientorderbooking->booking_endtime)));

	$pdf->Text(180,$addondetails_startpoint,iconv("UTF-8", "windows-1252",$general->oct_price_format_for_pdf($clientorderbooking->booking_price)));
	$addondetails_startpoint=$addondetails_startpoint+5;
	}
	
	
	$pdf->SetFont('DejaVu','',8);
	$pdf->Text(145,170,__("Total","oct"));
	$pdf->Text(145,175,__("Tax","oct"));
	$pdf->Text(145,180,__("Discount","oct"));	   
	   
	$pdf->SetFont('DejaVu','',8);
	$pdf->Text(180,170,iconv("UTF-8", "windows-1252",$general->oct_price_format_for_pdf($payments->amount)));
	$pdf->Text(180,175,iconv("UTF-8", "windows-1252",$general->oct_price_format_for_pdf($payments->taxes)));
	$pdf->Text(180,180,iconv("UTF-8", "windows-1252",$general->oct_price_format_for_pdf($payments->discount)));
	
	$pdf->SetFont('DejaVu','',15);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text(140,193,__("TOTAL:","oct"));
	$pdf->Text(175,193,iconv("UTF-8", "windows-1252",$general->oct_price_format_for_pdf($payments->net_total)));

	$pdf->SetFont('DejaVu','',12);
	$pdf->SetTextColor(102,103,102);
	
	$pdf->SetFont('DejaVu','',14);
	$pdf->Text(23,195,__("THANK YOU FOR YOUR BUSINESS!","oct"));

	$pdf->SetFont('DejaVu','',14);
	$pdf->Text(145,210,__("For ","oct").get_option('octabook_company_name'));
	
	$pdf->SetFont('DejaVu','',8);
	$pdf->Text(153,225,__("Company Director","oct"));

	$pdf->Output("#".$invoice_number.".pdf","D");
			/*
	ob_start();
	header('Content-type: application/pdf');
	$pdf->Output('','I');
	ob_end_flush(); */
}

/* octabook Add/Remove Sample Data */
if(isset($_REQUEST['general_ajax_action'],$_REQUEST['method']) && $_REQUEST['general_ajax_action']=='octabook_sampledata' && $_REQUEST['method']!=''){
	if($_REQUEST['method']=='Add'){
		
		$locationsinfo = array(array('location_title'=>'California','description'=>'California','email'=>'California@California.com','phone'=>'7739477310','address'=>'1625 E 75th St','city'=>'California','state'=>'Los Angles','zip'=>'60649','country'=>'USA'),array('location_title'=>'Singapore ','description'=>'Singapore','email'=>'Singapore@Singapore.com','phone'=>'8884081113','address'=>'514 S. MAGNOLIA ST.','city'=>'Rome','state'=>'Rome','zip'=>'32806','country'=>'Italy'));
		
		$staffsinfo = 	array(array('staff_name'=>'John','username'=>'john'.rand(10,1000),'email'=>'john@demo.com','description'=>'John staff description'),array('staff_name'=>'Johndoe','username'=>'johndoe'.rand(10,1000),'email'=>'Johndoe@demo.com','description'=>'Johndoe staff description'));
		
		$staffsinfo2 = 	array(array('staff_name'=>'Divina Lapine','username'=>'divinalapine'.rand(10,1000),'email'=>'divinalapine@demo.com','description'=>'Divina Lapine staff description'),array('staff_name'=>'Alia Gile','username'=>'aliagile'.rand(10,1000),'email'=>'aliagile@demo.com','description'=>'Alia Gile staff description'));
		
		$staffsinfo3 = 	array(array('staff_name'=>'Sam Kelly','username'=>'samkelly'.rand(10,1000),'email'=>'samkelly@demo.com','description'=>'Sam Kelly staff description'),array('staff_name'=>'Dorothy	Blake','username'=>'dorothyblake'.rand(10,1000),'email'=>'dorothyblake@demo.com','description'=>'Dorothy	Blake staff description'));
		
		$servicesinfo = array(array('service_title'=>'Cosmetic Dentistry','description'=>'Cosmetic dentistry is generally used to refer to any dental work that improves the appearance (though not necessarily the functionality) of teeth, gums and/or bite. It primarily focuses on improvement dental aesthetics in color, position, shape, size, alignment and overall smile appearance.'),array('service_title'=>'Routine Tooth Extractions','description'=>'Routine Extractions. There are instances when a tooth cannot be restored. Extensive decay as a result of chronic neglect or trauma that results in the inadvertent fracture of teeth are two leading causes for a tooth to be deemed non-salvageable.'));
		
		$servicesinfo2 = array(array('service_title'=>'Composite Bonding','description'=>'Composite bonding refers to the repair of decayed, damaged or discolored teeth using material that resembles the color of tooth enamel. Your dentist drills out the tooth decay and applies the composite onto the tooths surface, then culpts it into the right shape before curing it with a high-intensity light.'),array('service_title'=>'Dental Veneers','description'=>'Typically manufactured from medical-grade ceramic, dental veneers are made individually for each patient to resemble ones natural teeth.'));
		
		$servicesinfo3 = array(array('service_title'=>'Teeth Whitening','description'=>'One of the most basic cosmetic dentistry procedures, teeth whitening or teeth bleaching can be performed at your dentists office. Whitening should occur after plaque, tartar and other debris are cleaned from the surface of each tooth, restoring their natural appearance.'),array('service_title'=>'Implants','description'=>'Dental implants are used to replace teeth after tooth loss. The dentist inserts a small titanium screw into the jaw at the site of the missing tooth, which serves as the support for a crown.'));
		
		$addonsinfo = array(array('addon_title'=>'Teeth Whitening','price'=>'20','max_qty'=>5),array('addon_title'=>'Surgical tooth extractions','price'=>'100','max_qty'=>10));
		
		$addonsinfo2 = array(array('addon_title'=>'Composite Bonding','price'=>'20','max_qty'=>5),array('addon_title'=>'Dental Veneers','price'=>'100','max_qty'=>10));
		
		$addonsinfo3 = array(array('addon_title'=>'Teeth Whitening','price'=>'20','max_qty'=>5),array('addon_title'=>'Implants','price'=>'100','max_qty'=>10));
		
		$categoriesinfo = array(array('category_title'=>'Cosmetic Dentistry'),array('category_title'=>'Routine Tooth Extractions'));
		
		$categoriesinfo2 = array(array('category_title'=>'Composite Bonding'),array('category_title'=>'Dental Veneers'));
		
		$categoriesinfo3 = array(array('category_title'=>'Teeth Whitening'),array('category_title'=>'Implants'));
		
		
		/* Order Client Info Data */
		
		$oct_clientinfo = array(array('client_name'=>'John Deo','client_email'=>'johndeo@example.com','client_phone'=>'+17567436945'),array('client_name'=>'John Martin','client_email'=>'johnmartin@example.com','client_phone'=>'+17567436949'));
		
		$oct_clientinfo2 = array(array('client_name'=>'Olivia	Terry','client_email'=>'oliviaterry@example.com','client_phone'=>'+17567436945'),array('client_name'=>'Leonard	North','client_email'=>'leonardnorth@example.com','client_phone'=>'+17567436949'));
		
		$oct_clientinfo3 = array(array('client_name'=>'Jessica Walker','client_email'=>'jessicawalker@example.com','client_phone'=>'+17567436945'),array('client_name'=>'James McGrath','client_email'=>'jamesmcgrath@example.com','client_phone'=>'+17567436949'));
	
		$locationsids = array();	
		$servicesids = array();	
		$categoriesids = array();	
		$staffsids = array();
		$bdclientids = array();
		$bookingsids = array();
		$paymentsids = array();
		$orderids = array();
		
		/*Adding Locations */
		foreach($locationsinfo as $locationinfo){
			if(get_option('octabook_multi_location')=='E'){	
				$wpdb->query("insert into ".$wpdb->prefix."oct_locations set location_title='".$locationinfo['location_title']."',description='".$locationinfo['description']."',email='".$locationinfo['email']."',phone='".$locationinfo['phone']."',address='".$locationinfo['address']."',city='".$locationinfo['city']."',state='".$locationinfo['state']."',zip='".$locationinfo['zip']."',country='".$locationinfo['country']."',status='E'");
				$locationsids[] = $wpdb->insert_id;
			}else{
				$locationsids[] = 0;
			}
		}	
		
		/* Adding Categories 1 */
		$catecounter = 0;
		foreach($categoriesinfo as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		/* Adding Categories 2 */
		
		$catecounter = 0;
		foreach($categoriesinfo2 as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids2[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		/* Adding Categories 3 */
		
		$catecounter = 0;
		foreach($categoriesinfo3 as $categoryinfo){
						
			$wpdb->query("insert into ".$wpdb->prefix."oct_categories set location_id='".$locationsids[$catecounter]."',category_title='".$categoryinfo['category_title']."'");
			$categoriesids3[] =  $wpdb->insert_id;
			$catecounter++;
		}
		
		
		/* Add Staff Members1 */
		$staffcounter =0;
		foreach($staffsinfo as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		
		/* Add Staff Members2 */
		$staffcounter =0;
		foreach($staffsinfo2 as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids2[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
		
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		
		/* Add Staff Members3 */
		$staffcounter =0;
		foreach($staffsinfo3 as $staffinfo){
		$userdata = array('user_login'=>$staffinfo['username'],'user_email'=>$staffinfo['email'],'user_pass'=>$staffinfo['staff_name'],'first_name'=>$staffinfo['staff_name'],		'last_name'=>'','nickname'=>'','role'=>'subscriber');					
		$user_id = wp_insert_user($userdata);
		$staffsids3[] = $user_id;	
		$user = new WP_User($user_id);
		$user->add_cap('oct_staff');
		add_user_meta($user_id, 'staff_location',$locationsids[$staffcounter]);
		add_user_meta($user_id, 'staff_phone','');
		add_user_meta($user_id, 'staff_description',$staffinfo['description']);
		add_user_meta($user_id, 'schedule_type','W');
		add_user_meta($user_id, 'staff_image','');
		add_user_meta($user_id, 'staff_status','E');
		add_user_meta($user_id, 'staff_timezone','');
		add_user_meta($user_id, 'staff_timezoneID','');
		
			/*Adding Provider Schedule */
			for($dayid=1;$dayid<=7;$dayid++){
				$wpdb->query("insert into ".$wpdb->prefix."oct_schedule set provider_id='".$user_id."',weekday_id='".$dayid."',daystart_time='08:00:00',dayend_time='17:00:00',week_id='1'");
			}
			$staffcounter++;		
		}
		

		/* Adding Services */
		$servcounter = 0;
		foreach($servicesinfo as $serviceinfo){
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids[$servcounter]."',service_id='".$servicesids[$servcounter]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids[$servcounter]."','".$addonsinfo[$servcounter]['addon_title']."','".$addonsinfo[$servcounter]['price']."','".$addonsinfo[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
		}
		
		/* Adding Services2 */
		$servcounter = 0;
		$category_count = 2;
		foreach($servicesinfo2 as $serviceinfo){
		
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids2[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids2[$servcounter]."',service_id='".$servicesids[$category_count]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids[$category_count]."','".$addonsinfo2[$servcounter]['addon_title']."','".$addonsinfo2[$servcounter]['price']."','".$addonsinfo2[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
			$category_count++;
		}
		
		/* Adding Services2 */
		$servcounter = 0;
		$category_count = 4;
		foreach($servicesinfo3 as $serviceinfo){
			$wpdb->query("insert into ".$wpdb->prefix."oct_services set location_id='".$locationsids[$servcounter]."',color_tag='#".rand(100000,999999)."',service_title='".$serviceinfo['service_title']."',category_id='".$categoriesids3[$servcounter]."',duration='30',amount='50',service_description='".$serviceinfo['description']."',service_status='Y'");
			$servicesids[] =  $wpdb->insert_id;			
			
			/*Link Service With Staff Member*/
			$wpdb->query("insert into ".$wpdb->prefix."oct_providers_services set provider_id='".$staffsids3[$servcounter]."',service_id='".$servicesids[$category_count]."'");
			
			/* Service Addons */
			$wpdb->query("INSERT INTO ".$wpdb->prefix."oct_services_addon (id,service_id,addon_service_name,base_price,maxqty,image,multipleqty,status,position,predefine_image,predefine_image_title,location_id)values('','".$servicesids[$category_count]."','".$addonsinfo3[$servcounter]['addon_title']."','".$addonsinfo3[$servcounter]['price']."','".$addonsinfo3[$servcounter]['max_qty']."','','Y','E','','','','".$locationsids[$servcounter]."')");
			
			$servcounter++;
			$category_count++;
		}

		/* Adding Clients */
		$clientcounter = 0;
		
		foreach($oct_clientinfo as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'John Deo'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Cosmetic Dentistry'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Routine Tooth Extractions'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<=3;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			 	if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				} 
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
				
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		
		foreach($oct_clientinfo2 as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'Olivia	Terry'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Composite Bonding'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Dental Nenners'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids2[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<=3;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
			
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		foreach($oct_clientinfo3 as $oct_clientsinfo){
			
			if($oct_clientsinfo['client_name'] == 'Jessica Walker'){	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='California@California.com'";
				$res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Teeth Whitening'";
				$res_service = $wpdb->get_results($query);
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1s = date_i18n('Y-m-d H:i:s');
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s)))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}else{	
				/* Get Locations id */
				$query = "select * from ".$wpdb->prefix."oct_locations where email='Singapore@Singapore.com'";
	      $res = $wpdb->get_results($query);
				/* Get service id */
				$query = "select * from ".$wpdb->prefix."oct_services where service_title='Implants'";
				$res_service = $wpdb->get_results($query);
				
				/* Get provider id */
				$query = "select * from ".$wpdb->prefix."oct_providers_services where service_id='".$res_service[0]->id."'";
				$res_provider = $wpdb->get_results($query);
				
				$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 month", strtotime(date_i18n('Y-m-d',strtotime($todaydate)).' '.date_i18n('H:i:s',$todaydate))));
				$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
			}
			
			/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			
			$orderids3[] =$order_id;
			$oct_user_info = array(
					'user_login'    =>   $oct_clientsinfo['client_name'],
					'user_email'    =>   $oct_clientsinfo['client_email'],
					'user_pass'     =>   '12345678',
					'first_name'    =>   $oct_clientsinfo['client_name'],
					'last_name'     =>   '',
					'nickname'      =>  '',
					'role' => 'subscriber'
					);	
			$new_oct_user = wp_insert_user( $oct_user_info );
			$bdclientids[] =  $new_oct_user;
			$user = new WP_User($new_oct_user);
			$user->add_cap('read');
			$user->add_cap('oct_client'); 
			$user->add_role('oct_users');
			$user_id = $new_oct_user;
			$user_login = $preff_username;
			add_user_meta( $new_oct_user, 'oct_client_locations','#'.$res[0]->id.'#');
			
			$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
			$add = $wpdb->query($query1);
			if($add){
				echo "addedd client";
			}else{
				echo "not client";
			}
			
			for($i=0;$i<2;$i++){
				/* Get order id of user */
			$client_info_table = $wpdb->prefix .'oct_order_client_info';
			$sql_id="SELECT max(order_id) as max FROM ".$client_info_table;
			$get_order_id=$wpdb->get_var($sql_id);
			if($get_order_id == 0){
				$order_id = 1000;
			}else{
			$order_id = $get_order_id + 1;
			}
			if($i < 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',strtotime($bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				 } elseif($i <= 1){
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+1 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}else{
					$bookdate1s = date_i18n('Y-m-d H:i:s');
					$bookdate1 = date_i18n('Y-m-d H:i:s',strtotime("+2 days", strtotime(date_i18n('Y-m-d',strtotime($bookdate1s)).' '.date_i18n('H:i:s',$bookdate1s))));
					$bookend = date_i18n('Y-m-d H:i:s',strtotime("+30 minutes", strtotime(date_i18n('Y-m-d',strtotime($bookdate1)).' '.date_i18n('H:i:s',strtotime($bookdate1)))));
				}
				
				$query1="INSERT INTO ".$wpdb->prefix."oct_order_client_info (`id`, `order_id`, `client_name`, `client_email`, `client_phone`, `client_personal_info`) VALUES ('', '".$order_id."', '".$oct_clientsinfo['client_name']."', '".$oct_clientsinfo['client_email']."', '".$oct_clientsinfo['client_phone']."', '');";
				$add = $wpdb->query($query1);
				if($add){
					echo "addedd client";
				}else{
					echo "not client";
				}
				foreach($res_provider as $re_provider){
			
				$query2 = "INSERT INTO ".$wpdb->prefix."oct_bookings (`id`, `location_id`, `order_id`, `client_id`, `service_id`, `provider_id`, `booking_price`, `booking_datetime`, `booking_endtime`, `booking_status`, `reject_reason`, `cancel_reason`, `confirm_note`, `reschedule_note`, `reminder`, `notification`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$order_id."', '".$user_id."', '".$res_service[0]->id."', '".$re_provider->provider_id."', '50', '".$bookdate1."', '".$bookend."', 'C', '', '', '', '', '0', '0', NOW());";
				$add1 = $wpdb->query($query2);
				$bookingsids[] = $wpdb->insert_id;
				}
				$query3 = "INSERT INTO ".$wpdb->prefix."oct_payments (`id`, `location_id`, `client_id`, `order_id`, `payment_method`, `transaction_id`, `amount`, `discount`, `taxes`, `partial`, `net_total`, `lastmodify`) VALUES ('', '".$res[0]->id."', '".$user_id."', '".$order_id."', 'pay_locally', '', '50', '0', '0', '0', '50', '')";
				$add2 = $wpdb->query($query3);
				$paymentsids[] = $wpdb->insert_id;
				
			}
			
			$clientcounter++;
		}
		
		
		$sampledataids = array('locationsids'=>implode(',',$locationsids),'servicesids'=>implode(',',$servicesids),'categoriesids'=>implode(',',$categoriesids),'categoriesids2'=>implode(',',$categoriesids2),'categoriesids3'=>implode(',',$categoriesids3),'staffsids'=>implode(',',$staffsids),'staffsids2'=>implode(',',$staffsids2),'staffsids3'=>implode(',',$staffsids3),'bdclientids'=>implode(',',$bdclientids),'bookingsids'=>implode(',',$bookingsids),'paymentsids'=>implode(',',$paymentsids),'orderids'=>implode(',',$orderids),'orderids2'=>implode(',',$orderids2),'orderids3'=>implode(',',$orderids3));
		add_option('octabook_sample_dataids',serialize($sampledataids));	
		update_option('octabook_sample_status','N');
		$_SESSION['oct_location'] =0;
			
	}else{ 
	
	/* Remove Sample Data */
		$sampledata_info = unserialize(get_option('octabook_sample_dataids'));
		$locationsids = explode(",",$sampledata_info['locationsids']);
		$categoriesids = explode(",",$sampledata_info['categoriesids']);
		$categoriesids2 = explode(",",$sampledata_info['categoriesids2']);
		$categoriesids3 = explode(",",$sampledata_info['categoriesids3']);
		$servicesids = explode(",",$sampledata_info['servicesids']);
		$staffsids = explode(",",$sampledata_info['staffsids']);
		$staffsids2 = explode(",",$sampledata_info['staffsids2']);
		$staffsids3 = explode(",",$sampledata_info['staffsids3']);
		$bdclientids = explode(",",$sampledata_info['bdclientids']);
		$bookingsids = explode(",",$sampledata_info['bookingsids']);
		$paymentsids = explode(",",$sampledata_info['paymentsids']);
		$orderids = explode(",",$sampledata_info['orderids']);
		$orderids2 = explode(",",$sampledata_info['orderids2']);
		$orderids3 = explode(",",$sampledata_info['orderids3']);
		/* Delete Sample Locations */
		foreach($locationsids as $location_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_locations where id='".$location_id."'");
		}
		/* Delete Sample Categories */
		foreach($categoriesids as $category_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_categories where id='".$category_id."'");
		}
		foreach($categoriesids2 as $category_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_categories where id='".$category_id."'");
		}
		foreach($categoriesids3 as $category_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_categories where id='".$category_id."'");
		}
		/* Delete Sample Services */
		foreach($servicesids as $service_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_services where id='".$service_id."'");
			/* Delete Sample Service & Provider Releation */
			$wpdb->query("Delete from ".$wpdb->prefix."oct_providers_services where service_id='".$service_id."'");
			/* Delete Sample Service Addons */
			$wpdb->query("Delete from ".$wpdb->prefix."oct_services_addon where service_id='".$service_id."'");
		}
		/* Delete Sample Staff */
		foreach($staffsids as $staff_id){
			$wpdb->query("Delete from ".$wpdb->prefix."users where ID='".$staff_id."'");
			/* Delete Staff Meta */
			$wpdb->query("Delete from ".$wpdb->prefix."usermeta where user_id='".$staff_id."'");
			/* Delete Staff Schedule */
			$wpdb->query("Delete from ".$wpdb->prefix."oct_schedule where provider_id='".$staff_id."'");
		}
		/* Delete Sample Staff */
		foreach($staffsids2 as $staff_id){
			$wpdb->query("Delete from ".$wpdb->prefix."users where ID='".$staff_id."'");
			/* Delete Staff Meta */
			$wpdb->query("Delete from ".$wpdb->prefix."usermeta where user_id='".$staff_id."'");
			/* Delete Staff Schedule */
			$wpdb->query("Delete from ".$wpdb->prefix."oct_schedule where provider_id='".$staff_id."'");
		}
			/* Delete Sample Staff */
		foreach($staffsids3 as $staff_id){
			$wpdb->query("Delete from ".$wpdb->prefix."users where ID='".$staff_id."'");
			/* Delete Staff Meta */
			$wpdb->query("Delete from ".$wpdb->prefix."usermeta where user_id='".$staff_id."'");
			/* Delete Staff Schedule */
			$wpdb->query("Delete from ".$wpdb->prefix."oct_schedule where provider_id='".$staff_id."'");
		}
		
		/* Delete Sample Staff */
		foreach($bdclientids as $client_id){
			$wpdb->query("Delete from ".$wpdb->prefix."users where ID='".$client_id."'");
			/* Delete Staff Meta */
			$wpdb->query("Delete from ".$wpdb->prefix."usermeta where user_id='".$client_id."'");
		}
		
		/* Delete Sample Staff */
		foreach($bookingsids as $booking_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_bookings where id='".$booking_id."'");
		}		
		/* Delete Sample Payments */
		foreach($paymentsids as $payments_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_payments where id='".$payments_id."'");
		}
		/* Order ID */
		foreach($orderids as $order_id){
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where order_id='".$order_id."'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='John Deo'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='John Martin'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='Olivia	Terry'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='Leonard	North'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='Jessica Walker'");
			$wpdb->query("Delete from ".$wpdb->prefix."oct_order_client_info where client_name='James McGrath'");
			
		}
		delete_option('octabook_sample_dataids');
		/* update_option('octabook_sample_status','N'); */
		$check_for_location = $wpdb->get_results("select  from ".$wpdb->prefix."oct_locations");
	}
	
}

/* octabook Publish/Hide/Delete Review */
if(isset($_POST['general_ajax_action'],$_POST['method'],$_POST['review_id']) && $_POST['general_ajax_action']=='publish_hide_delete_review' && $_POST['method']!='' && $_POST['review_id']!=''){
	if($_POST['method']=='delete'){		
		$reviews->id = $_POST['review_id'];
		$reviews->delete();
	}else{
		$reviews->status = $_POST['method'];
		$reviews->id = $_POST['review_id'];
		$reviews->update_review_status();	
	}	
}

if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='save_custom_form'){
   update_option('octabook_custom_form',$_POST['formdata']);
}
/* Client Dashboard Login */
if(isset($_POST['general_ajax_action'],$_POST['username'],$_POST['password']) && $_POST['general_ajax_action']=='client_dashboard_login'){
  $creds                  = array();
	$creds['user_login']    = $_POST['username'];
	$creds['user_password'] = $_POST['password'];
	$creds['remember']      = true;
	$user                   = wp_signon($creds, false);
	if (is_wp_error($user)) {
		echo __("Invalid Username or Password.", "oct");
	} else {
		echo '1';die();
	}
}

if(isset($_POST['general_ajax_action'],$_POST['user_type']) && $_POST['general_ajax_action']=='refresh_register_client_datatable' && $_POST['user_type']=='registered'){ 

	if(isset($_SESSION['oct_all_loc_clients']) && $_SESSION['oct_all_loc_clients']=='Y'){
		$clients->location_id = 'All';
		$all_clients_info = $clients->get_registered_clients();
	}else{
		$clients->location_id = $_SESSION['oct_location'];
		$all_clients_info = get_users( array( 'role' => 'oct_users' ,'meta_key' => 'oct_client_locations' ,'meta_value' => '#'.$_SESSION['oct_location'].'#'));
	}
?>
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
<?php 
}

if(isset($_POST['general_ajax_action'],$_POST['user_type']) && $_POST['general_ajax_action']=='refresh_register_client_datatable' && $_POST['user_type']=='guest'){
	
	
	if(isset($_SESSION['oct_all_loc_clients']) && $_SESSION['oct_all_loc_clients']=='Y'){
		$clients->location_id = 'All';
		
	}else{
		$clients->location_id = $_SESSION['oct_location'];		
	}
	$all_guesuser_info = $clients->get_all_guest_users_orders();
?>
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
				</div> <?php
}
/* Get Register Client Information for Manual Booking Popup */
if(isset($_POST['general_ajax_action']) && $_POST['general_ajax_action']=='get_povider_slot_option'){
	$selecteddate = date_i18n('Y-m-d',strtotime($_POST['select_date']));
	$selectedstaffid = $_POST['provider_id'];
	
	$service_id = $_POST['service_id'];
	
	$oct_service->id = $service_id;
	$oct_service->readOne();
	
	$service_duration = $oct_service->duration;
	
	$oct_staff = new octabook_staff();
	$oct_staff->id = $selectedstaffid;
	$provider_result = $oct_staff->readOne();
	
	$first_step = new octabook_first_step();
	$time_interval = get_option('octabook_booking_time_interval');	
	$time_slots_schedule_type = strtolower($provider_result[0]['schedule_type']);
	$advance_bookingtime = get_option('octabook_minimum_advance_booking');
	$booking_paddingtime = get_option('octabook_booking_padding_time');
	$booking_dayclosing = get_option('octabook_dayclosing_overlap');
	
	$time_schedule = $first_step->get_day_time_slot_by_provider_id($selectedstaffid,$time_slots_schedule_type,$selecteddate,$time_interval,$service_id);
	
	$allofftime_counter = "";
	$allbreak_counter = 0;	
	$slot_counter = 0;
	
	$start_date = $selecteddate;
	
	if($time_schedule['off_day']!=true  && isset($time_schedule['slots']) && sizeof((array)$time_schedule['slots'])>0 && $allbreak_counter != sizeof((array)$time_schedule['slots'])){
		foreach($time_schedule['slots']  as $slot) {
			$curreslotstr = strtotime(date_i18n('Y-m-d H:i:s',strtotime($start_date.' '.$slot)));
			
			$ifbreak = 'N';
			/* Need to check if the appointment slot come under break time. */
			foreach($time_schedule['breaks'] as $daybreak) {
				if(strtotime($slot) >= strtotime($daybreak['break_start']) && strtotime($slot) < strtotime($daybreak['break_end'])) {
				   $ifbreak = 'Y';   
				}
			}
			/* if yes its break time then we will not show the time for booking  */
			if($ifbreak=='Y') { $allbreak_counter++; continue; } 
			
			$ifofftime = 'N';									
			foreach($time_schedule['offtimes'] as $offtime) {
				if(strtotime($selecteddate.' '.$slot) >= strtotime($offtime['offtime_start']) && strtotime($selecteddate.' '.$slot) < strtotime($offtime['offtime_end'])) {
				   $ifofftime = 'Y';
				}
			 }
			/* if yes its offtime time then we will not show the time for booking  */
			if($ifofftime=='Y') { $allofftime_counter++; continue; }
			
			if(get_option('octabook_multiple_booking_sameslot') == "D"){	
				if(get_option('octabook_hide_booked_slot')=='E' && (in_array($curreslotstr,$time_schedule['booked']))) {
					continue;
				}
			}
			
			if(get_option('octabook_multiple_booking_sameslot')=='D'){
				if(in_array($curreslotstr,$time_schedule['booked'])){
					if(get_option('octabook_hide_booked_slot')=='D'){
						?>
						<option disabled><?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?></option>
						<?php     
					}
				}else{
					?>
					<option value="<?php echo date_i18n("H:i",strtotime($slot)); ?>"><?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?></option>
					<?php   
				}
			}else{
				?>
				<option value="<?php echo date_i18n("H:i",strtotime($slot)); ?>"><?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?></option>
				<?php    
			}
			
		}
	}
}