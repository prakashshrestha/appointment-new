<?php  
	if(!session_id()) { @session_start(); }
	
	include_once(dirname(dirname(dirname(__FILE__))).'/objects/plivo.php');
	require_once dirname(dirname(dirname(__FILE__))).'/assets/Twilio/autoload.php'; 
	use Twilio\Rest\Client;
	
  $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));	
	if (file_exists($root.'/wp-load.php')) {
		require_once($root.'/wp-load.php');	
	}
	
	$plugin_url = plugins_url('',  dirname(__FILE__));
	$base =   dirname(dirname(dirname(__FILE__)));

	include_once($base.'/objects/class_location.php');
	include_once($base.'/objects/class_service.php');
	include_once($base.'/objects/class_provider.php');
	include_once($base.'/objects/class_clients.php');
	include_once($base.'/objects/class_booking.php');
	include_once($base.'/objects/class_payments.php');
	include_once($base.'/objects/class_email_templates.php');
	
	/* declare classes */
	$oct_location = new octabook_location();
	$oct_service = new octabook_service();
	$oct_staff = new octabook_staff();
	$oct_client_info = new octabook_clients();	
	$oct_booking = new octabook_booking();
	$oct_payments = new octabook_payments();
	$oct_email_templates = new octabook_email_template();

/* booking complete code here START */
if(isset($_SESSION['oct_detail']) && $_SESSION['oct_detail']!=''){
	$preff_username = $_SESSION['oct_detail']['preff_username'];
	$preff_password = $_SESSION['oct_detail']['preff_password'];
	$first_name = $_SESSION['oct_detail']['first_name'];
	$last_name = $_SESSION['oct_detail']['last_name'];
	$user_phone = $_SESSION['oct_detail']['user_phone'];
	$user_gender = $_SESSION['oct_detail']['user_gender'];
	$user_address = $_SESSION['oct_detail']['user_address'];
	$user_city = $_SESSION['oct_detail']['user_city'];
	$user_state = $_SESSION['oct_detail']['user_state'];
	$user_notes = $_SESSION['oct_detail']['user_notes'];
	$user_ccode = $_SESSION['oct_detail']['user_ccode'];

	if(isset($_SESSION['oct_detail']['serialize_extra_details'])){
		$extra_details = $_SESSION['oct_detail']['serialize_extra_details'];
	}else{
		$extra_details = array();
	}
	
	$serialize_extra_details = serialize($extra_details);
	
	if($_SESSION['oct_detail']['payment_method'] == 'stripe'){
		$oct_payments->transaction_id = $_SESSION['oct_detail']['stripe_trans_id'];
	}else if($_SESSION['oct_detail']['payment_method'] == 'paypal'){
		$oct_payments->transaction_id = $_SESSION['oct_detail']['paypal_transaction_id'];
	} else if($_SESSION['oct_detail']['payment_method'] == 'payumoney'){
		$oct_payments->transaction_id = $_SESSION['payu_transaction_id'];
	} else if($_SESSION['oct_detail']['payment_method'] == 'paytm'){
		$oct_payments->transaction_id = $_SESSION['oct_detail']['paytm_transaction_id'];
	}
	else{
		$oct_payments->transaction_id = '';
	}
	
	
	if($_SESSION['oct_detail']['oct_user_type'] == 'guest'){
		/* existing user code here */
		$user_id = 0;
	}else if($_SESSION['oct_detail']['oct_user_type'] == 'existing'){
		/* existing user code here */
		$user_id = get_current_user_id();
		
		update_user_meta( $user_id, 'oct_client_phone', $user_phone );
		update_user_meta( $user_id, 'oct_client_gender', $user_gender );
		update_user_meta( $user_id, 'oct_client_address', $user_address );
		update_user_meta( $user_id, 'oct_client_city', $user_city );
		update_user_meta( $user_id, 'oct_client_state', $user_state );
		update_user_meta( $user_id, 'oct_client_notes', $user_notes );
		update_user_meta( $user_id, 'oct_client_extra_details', $serialize_extra_details );
		update_user_meta( $user_id, 'oct_client_ccode', $user_ccode );
	}else{
		/* New user code here */
		
		$username = $_SESSION['oct_detail']['username'];
		
		/* insert data in user table */
		$oct_user_info = array(
						'user_login'    =>   $username,
						'user_email'    =>   $preff_username,
						'user_pass'     =>   $preff_password,
						'first_name'    =>   $first_name,
						'last_name'     =>   $last_name,
						'nickname'      =>  '',
						'role' => 'subscriber'
						);
		   
		$new_oct_user = wp_insert_user( $oct_user_info );
			
		$user = new WP_User($new_oct_user);
		$user->add_cap('read');
		$user->add_cap('oct_client'); 
		$user->add_role('oct_users');
		$user_id = $new_oct_user;
		$user_login = $preff_username;
		$booking_locations = array();
		foreach($_SESSION['oct_cart_item'] as $cart_itemloc){
			$citemloc = unserialize($cart_itemloc);
			$booking_locations[] = $citemloc['selected_location'];
		}
		
		
		add_user_meta( $new_oct_user, 'oct_client_locations', '#'.implode('#',$booking_locations).'#');
		add_user_meta( $new_oct_user, 'oct_client_phone', $user_phone );
		add_user_meta( $new_oct_user, 'oct_client_gender', $user_gender );
		add_user_meta( $new_oct_user, 'oct_client_address', $user_address );
		add_user_meta( $new_oct_user, 'oct_client_city', $user_city );
		add_user_meta( $new_oct_user, 'oct_client_state', $user_state );
		add_user_meta( $new_oct_user, 'oct_client_notes', $user_notes );
		add_user_meta( $new_oct_user, 'oct_client_ccode', $user_ccode );
		add_user_meta( $new_oct_user, 'oct_client_extra_details', $serialize_extra_details );
		
		/* Set cookie of user after booking */
		wp_set_current_user( $user_id, $user_login );
		wp_set_auth_cookie( $user_id );
	}
	$oct_booking->get_last_order_id();
	$next_order_id = $oct_booking->last_order_id + 1;
	
	/* Adding Appointments Into Google Calendar START */
	if (!function_exists('octabook_addevent_googlecalender_provider')) {
		function octabook_addevent_googlecalender_provider($provider_id,$provider_gc_id,$gc_token,$summary,$location,$description,$event_color,$date,$start,$end,$GcclientID,$GcclientSecret,$GcEDvalue,$providerTZ,$oct_gc_frontend_url){
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
	
	$GcclientID = get_option('oct_gc_client_id');
	$GcclientSecret = get_option('oct_gc_client_secret');
	$GcEDvalue = get_option('oct_gc_status');
		
	$service = new octabook_service();
	$ordercounter = 0;
	foreach($_SESSION['oct_cart_item'] as $cart_item){
		$citem = unserialize($cart_item);
		$service->id=$citem['selected_service'];
		$serviceInfo = $service->readOne();
		$service_title = $service->service_title;
		
		$gc_token = get_option('oct_gc_token');
		$summary = $service_title."-".$first_name." ".$last_name;
		$description = 'Service='.$service_title.', Name='.$first_name." ".$last_name.', Email='.$preff_username.', Phone='.$user_ccode.''.$user_phone;
		$event_color = '9';
		
		$date = date_i18n('Y-m-d', $citem['selected_datetime']);
		$start = date_i18n('H:i:s',$citem['selected_datetime']);
		$event_endtime = date_i18n('H:i:s',$citem['selected_enddatetime']);
		$end = $event_endtime;
		
		if(get_option('timezone_string') != ''){
			$providerTZ = get_option('timezone_string');
		}else{
			$gmt_offset = get_option('gmt_offset');
			$hr_minute = explode('.', $gmt_offset);
			if (isset($hr_minute[1])) {
				if ($hr_minute[1] == '5') {
					$gmt_offset = $hr_minute[0].'.30';
				}else if ($hr_minute[1] == '75') {
					$gmt_offset = $hr_minute[0].'.45';
				}else{
					$gmt_offset = $hr_minute[0].'.0';
				}
			}else{
				$gmt_offset = $hr_minute[0].'.0';
			}
			$gmt_offset_array = explode(".",$gmt_offset);
			$seconds = ($gmt_offset_array[0] * 60 * 60) + ($gmt_offset_array[1] * 60);
			$get_tz = timezone_name_from_abbr('', $seconds, 1);
			if($get_tz === false){ $get_tz = timezone_name_from_abbr('', $seconds, 0); }  
			$providerTZ = $get_tz;
		}
		$provider_gc_id = get_option('oct_gc_id');
		$provider_id = '';
		$oct_gc_frontend_url = get_option('oct_gc_frontend_url');
		$oct_gc_token = get_option('oct_gc_token');																											 
																						 
		if($gc_token != '' && $GcEDvalue == 'Y' && $GcclientID!='' && $GcclientSecret!=''){
			$event_Status = octabook_addevent_googlecalender_provider($provider_id,$provider_gc_id,$gc_token,$summary,$location,$description,$event_color,$date,$start,$end,$GcclientID,$GcclientSecret,$GcEDvalue,$providerTZ,$oct_gc_frontend_url);
			$gc_event_id = $event_Status['id'];
		}else{
			$gc_event_id = '';
		}
		
		$citem = unserialize($cart_item);
		$staff_provider_id = $citem['selected_staff'];
		$staff_gc_token = $oct_staff->get_staff_option("gc_token",$staff_provider_id);
		$staff_GcEDvalue = $oct_staff->get_staff_option("gc_status",$staff_provider_id);
		$staff_GcclientID = $oct_staff->get_staff_option("gc_client_id",$staff_provider_id);
		$staff_GcclientSecret = $oct_staff->get_staff_option("gc_client_secret",$staff_provider_id);
		$staff_provider_gc_id = $oct_staff->get_staff_option("gc_id",$staff_provider_id);
		$staff_oct_gc_frontend_url = $oct_staff->get_staff_option("gc_frontend_url",$staff_provider_id);
		
		$staff_gc_event_id = "";
		if($staff_gc_token != '' && $staff_GcEDvalue == 'Y' && $staff_GcclientID!='' && $staff_GcclientSecret!=''){
			$staff_event_Status = octabook_addevent_googlecalender_provider($staff_provider_id,$staff_provider_gc_id,$staff_gc_token,$summary,$location,$description,$event_color,$date,$start,$end,$staff_GcclientID,$staff_GcclientSecret,$staff_GcEDvalue,$providerTZ,$staff_oct_gc_frontend_url);
			 $staff_gc_event_id = $staff_event_Status['id'];
		}else{
			 $staff_gc_event_id = '';
		}
																								
		$oct_booking->location_id = $citem['selected_location'];
		$oct_booking->order_id = $next_order_id;
		$oct_booking->client_id = $user_id;
		$oct_booking->service_id = $citem['selected_service'];
		$oct_booking->provider_id = $citem['selected_staff'];
		$oct_booking->booking_price = $citem['total_price'];
		$start_y_m_d = date_i18n('Y-m-d', $citem['selected_datetime']);
		$end_y_m_d = date_i18n('Y-m-d', $citem['selected_enddatetime']);
		$end_booking_date_time = "";
		if($start_y_m_d != $end_y_m_d){
			$end_booking_date_time = date_i18n('Y-m-d H:i:s', strtotime('-1 minutes',$citem['selected_enddatetime']));
		}else{
			$end_booking_date_time = date_i18n('Y-m-d H:i:s', $citem['selected_enddatetime']);
		}
		$oct_booking->booking_datetime = date_i18n('Y-m-d H:i:s', $citem['selected_datetime']);
		$oct_booking->booking_endtime = $end_booking_date_time;
		if(get_option('octabook_appointment_auto_confirm')=='E'){
			$oct_booking->booking_status = 'C';
		}else{
			$oct_booking->booking_status = 'A';	
		}
		$oct_booking->reject_reason = '';
		$oct_booking->cancel_reason = '';
		$oct_booking->confirm_note = '';
		$oct_booking->reschedule_note = '';
		$oct_booking->reminder = '0';
		$oct_booking->notification = '0';
		$oct_booking->gc_event_id = $gc_event_id;
		$oct_booking->gc_staff_event_id = $staff_gc_event_id;																								 
		$oct_booking->lastmodify = date_i18n('Y-m-d H:i:s');
		$oct_booking->add_bookings();
		
		/* add addons */
		foreach($citem['each_addon_price'] as $arrkey => $aitem){
			$oct_booking->order_id = $next_order_id;
			$oct_booking->service_id = $citem['selected_service'];
			$oct_booking->addons_service_id = $aitem['addonid'];
			$oct_booking->associate_service_id = $citem['service_addons'][$arrkey]['maxqty'];
			$oct_booking->addons_amount = $aitem['addon_price'];
			$oct_booking->insert_booking_addons();
		}
		
		
		/* Add Order Client Info & Payment Info Once */
		if($ordercounter==0){
			$personal_info_arr = array('ccode'=>$user_ccode,'dob'=>'','zip'=>'','skype'=>'','age'=>'','phone1'=>$user_phone, 'gender'=>$user_gender, 'first_name'=>$first_name, 'last_name'=>$last_name, 'address'=>$user_address, 'city'=>$user_city, 'state'=>$user_state, 'notes'=>$user_notes);
	
			$personal_info_merged_arr = array_merge($personal_info_arr,unserialize($extra_details));
			
			$personal_info_serialize_arr = serialize($personal_info_merged_arr);
			
			$oct_client_info->order_id = $next_order_id;
			$oct_client_info->clientName = $first_name." ".$last_name;
			$oct_client_info->client_email = $preff_username;
			$oct_client_info->client_phone = $user_ccode.''.$user_phone;
			$oct_client_info->client_personal_info = $personal_info_serialize_arr;
			$oct_client_info->add_client_info();
			
			$oct_payments->payment_method = $_SESSION['oct_detail']['payment_method'];
			$oct_payments->location_id = $citem['selected_location'];;
			$oct_payments->client_id = $user_id;
			$oct_payments->order_id = $next_order_id;
			$oct_payments->amount = $_SESSION['oct_sub_total'];
			$oct_payments->discount = $_SESSION['oct_detail']['discount'];
			$oct_payments->taxes = $_SESSION['oct_taxvat'];
			$oct_payments->partial = $_SESSION['oct_partialdeposit'];
			$oct_payments->net_total = $_SESSION['oct_nettotal'];
			$oct_payments->add_payments();
		}			
		$ordercounter++;
		
	}
	/*  Adding Appointments Into Google Calendar END  */
		
	
	
	
	unset($_SESSION['oct_cart_item']);
	unset($_SESSION['oct_partialdeposit']);
	unset($_SESSION['oct_partialdeposit_remaining']);
	unset($_SESSION['oct_nettotal']);
	unset($_SESSION['oct_taxvat']);
	unset($_SESSION['oct_sub_total']);
	unset($_SESSION['oct_coupon_id']);
	unset($_SESSION['oct_coupon_code']);
	unset($_SESSION['oct_coupon_discount']);
		
	/* Send Email To Custom, Staff, Admin */
	function set_content_type() {			
		return 'text/html';		
	}
			
	$oct_booking->order_id=$next_order_id;        
	$client_bookings= $oct_booking->get_all_bookings_by_order_id();		
	$sender_name = get_option('octabook_email_sender_name');		
	$sender_email_address = get_option('octabook_email_sender_address');		
	$headers = "From: $sender_name <$sender_email_address>" . "\r\n";
	
	$client_name = $first_name." ".$last_name;
	$company_name = get_option('octabook_company_name');
	$company_address = get_option('octabook_company_address');
	$company_city = get_option('octabook_company_city');
	$company_state = get_option('octabook_company_state');
	$company_zip = get_option('octabook_company_zip');
	$company_country = get_option('octabook_company_country');
	$company_phone = get_option('octabook_company_country_code').get_option('octabook_company_phone');
	$company_email = get_option('octabook_company_email');
	$admin_manager_name = ucwords(get_option('octabook_email_sender_name'));
	$company_logo = $business_logo = site_url()."/wp-content/uploads/".get_option('octabook_company_logo');
	
	
	
	
	/* main loop for content and mail start here */           
	$booking_counter = 1; 
	$booking_counter_txt = '';
	$booking_details = '';
	$booking_details_sms = '';
	
	foreach($client_bookings as $single_booking){
			
		$oct_service->id = $single_booking->service_id;                    
		$oct_staff->id = $single_booking->provider_id;                    
		$oct_service->readOne();                    
		$staffinfo = $oct_staff->readOne();   
		$location_details = '';
		if($single_booking->location_id!=0 || $single_booking->location_id!=''){
			$oct_location->id = $single_booking->location_id;
			$locationinfo = $oct_location->readOne();
			if(sizeof((array)$locationinfo)>0){
				$location_details .= "<br/><span><strong>".__('Location','oct')."</strong>: ".stripslashes_deep($locationinfo[0]->location_title)."</span><br/><br/><span><strong>".__('Location Address','oct')."</strong>: ".stripslashes_deep($locationinfo[0]->address)."</span><br/><br/>";
			}
		}
		
		$addons_detail = '';
		$addon_titles = '';
		$addon_prices = '';
		$addon_qty = '';
		$oct_booking->order_id =  $single_booking->order_id;
		$serviceaddons_info = $oct_booking->select_addonsby_orderidand_serviceid();	
		$totalserviceaddons = sizeof((array)$serviceaddons_info);
		if($totalserviceaddons>0){
			$addoncounter = 1;
			foreach($serviceaddons_info as $serviceaddon_info){				
				$oct_service->addon_id = $serviceaddon_info->addons_service_id;
				$addon_info = $oct_service->readOne_addon();
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
		



		
		$datetime = explode(' ',$single_booking->booking_datetime);        
		$booking_id=base64_encode($next_order_id);        
		$encoded_cinfo_sp=base64_encode($booking_id."-confirm");        
		$appoint_confirm_link_sp =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".$encoded_cinfo_sp;                        
		$encoded_rinfo_sp=base64_encode($booking_id."-reject");        
		$appoint_reject_link_sp =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".$encoded_rinfo_sp; 

		/*Client Cancel Link */
		$encoded_cinfo_client=base64_encode($booking_id."-clientcancel");        

		$appoint_cancel_link_client =plugins_url('',dirname(__FILE__))."/lib/booking_crc_email.php?".$encoded_cinfo_client;
		
		if(isset($_SESSION['booking_type']) || get_option('octabook_auto_confirm_appointment')=='Y' ){
		$confirm_link_sp='';
		}else{
		$confirm_link_sp="<a style='text-decoration: none;color: #FFF;background-color: #348eda;	border: solid #348eda;border-width: 10px 30px; line-height: 1;	font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block; border-radius: 10px;'  id='email-btn-primary' class='email-btn-primary' href='".$appoint_confirm_link_sp."-".base64_encode($single_booking->provider_id."+".$single_booking->id)."' >".__('Confirm','oct')."</a>";     
		}		
		$reject_link_sp ="<a style='text-decoration: none;color: #FFF;background-color: red;border: solid red;border-width: 10px 30px;line-height: 1;font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block;border-radius: 10px;'  id='email-btn-secondary' class='email-btn-secondary' href='".$appoint_reject_link_sp."-".base64_encode($single_booking->provider_id."+".$single_booking->id)."' >".__('Reject','oct')."</a>";

		$cancel_link_client ="<a style='text-decoration: none;color: #FFF;background-color: red;border: solid red;border-width: 10px 30px;line-height: 1;font-weight: bold;margin-right: 10px;text-align: center;cursor: pointer;display: inline-block;border-radius: 10px;'  id='email-btn-secondary' class='email-btn-secondary' href='".$appoint_cancel_link_client."-".base64_encode($single_booking->client_id."+".$single_booking->id)."' >".__('Cancel','oct')."</a>";
		
		$booking_details .= $location_details;
		
		$client_extra_email_detail = "";
		$client_extra_sms_detail = "";
		
		$extra_details = unserialize($serialize_extra_details);
		$user_extra_info = get_user_meta($user_id,'oct_client_extra_details');
	
								 if($user_extra_info != '') { 
									foreach($user_extra_info as $extra_details){
										$unser_date = unserialize($extra_details);
										$sec_unser_data = unserialize($unser_date);
										foreach($sec_unser_data as $key=>$val){
										$booking_details .=	 "<div class='col-xs-12 np'><b> ".$key."</b> - ".$val."</div><br/>";
										$client_extra_email_detail .="<span><strong>".$key."</strong>: ".$val."</span><br/><br/>";
										$client_extra_sms_detail .= $key.": ".$val." ";
										}
									}
								}
								
		$booking_details .= "<span><strong>".__('For','oct')."</strong>: ".stripslashes_deep($oct_service->service_title)."</span><br/><br/>
								<span><strong>".__('With','oct')."</strong>: ".ucwords(stripslashes_deep($staffinfo[0]['staff_name']))."</span><br/><br/>
								<span><strong>".__('On','oct')."</strong>: ".date_i18n(get_option('date_format'),strtotime($datetime[0]))."</span><br/><br/>
								<span><strong>".__('At','oct')."</strong>: ".date_i18n(get_option('time_format'),strtotime($datetime[1]))."</span><br/><br/>
								<span>".$cancel_link_client."</span><br/>".$addons_detail."</span><br/>";


		$booking_details_sms .= ' With :'.ucwords(stripslashes_deep($staffinfo[0]['staff_name'])).' On : '.date_i18n(get_option('date_format'),strtotime($datetime[0])).' At : '.date_i18n(get_option('time_format'),strtotime($datetime[1])).' For: '.$oct_service->service_title.', ';
		
		if(sizeof((array)$client_bookings) > 1) {
			$booking_counter_txt = "#".$booking_counter."<br/>";
		}
		
		$arr_providers_booking_details[$staffinfo[0]['id']][] = $location_details."<br/>".$booking_counter_txt."
		<span><strong>".__('For','oct')."</strong> :".stripslashes_deep($oct_service->service_title)."</span><br/><br/>
								<span><strong>".__('With','oct')."</strong> :".ucwords(stripslashes_deep($staffinfo[0]['staff_name']))."</span><br/><br/>
								<span><strong>".__('On','oct')."</strong> :".date_i18n(get_option('date_format'),strtotime($datetime[0]))."</span><br/><br/>
								<span><strong>".__('At','oct')."</strong> :".date_i18n(get_option('time_format'),strtotime($datetime[1]))."</span><br/><br/>".$addons_detail."<span>".$confirm_link_sp."</span>	<span>".$reject_link_sp."</span><br/>";
		
		
		$booking_counter++;
	}
	
	/* Mail content loop end here */

	/* Send Email To Client */
		if(get_option('octabook_client_email_notification_status')=='E'){
		$oct_clientemail_templates = new octabook_email_template();
		$msg_template = $oct_clientemail_templates->email_parent_template;	
		if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
			$oct_clientemail_templates->email_template_name = "CC"; 
		}else{
			$oct_clientemail_templates->email_template_name = "AC";
		}
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
			$search = array('{{customer_name}}','{{booking_details}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');      
			$replace_with = array($client_name,$booking_details,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
			$email_client_message = str_replace($search,$replace_with,$email_content);	
			$email_client_message = str_replace('###msg_content###',$email_client_message,$msg_template);		
			add_filter( 'wp_mail_content_type', 'set_content_type' );
			$status = wp_mail($preff_username,$email_subject,$email_client_message,$headers);  
		
			
			
			/* Shreya code*/
			
			
		
            
            
  
            /*$phpmailer->addAddress('vinod@broadview-innovations.com', 'Vinod');
            $phpmailer->Subject = $email_subject;
            $phpmailer->isHTML(true);
            $phpmailer->Body = $email_client_message;
            
            $phpmailer->to = "";
            $phpmailer->subject = "";
            
            if(!$phpmailer ->Send()) {
               echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $phpmailer->ErrorInfo;
                exit;
            }
*/
			
			/* */
			
			
			
			
			
			
			
			
			
			
			
			
		}       
	}
	
	/* Send email to admin/service provider when booking is complete */
	$client_full_detail='<br/>';
	$sms_client_full_detail=' ';
	if(ucwords($client_name)!=''){ 
		$client_full_detail .= "<span><strong>".__('Client Name','oct')."</strong>: ".ucwords($client_name)."</span><br/><br/>";
		$sms_client_full_detail .= __('Client Name','oct').": ".ucwords($client_name)." ";
	}if($preff_username!=''){ 
		$client_full_detail .= "<span><strong>".__('Client Email','oct')."</strong>: ".$preff_username."</span><br/><br/>";
		$sms_client_full_detail .= __('Client Email','oct').": ".$preff_username." ";
	}	
	if($user_gender!=''){
		if($user_gender == "M"){
			$gender_display = "Male";
		}else{
			$gender_display = "Female";
		}
		$client_full_detail .="<span><strong>".__('Gender','oct')."</strong>: ".$gender_display."</span><br/><br/>";
		$sms_client_full_detail .= __('Gender','oct').": ".$gender_display." ";
	}
	if($user_phone!=''){
		$client_full_detail .="<span><strong>".__('Client Phone','oct')."</strong>: ".$user_phone."</span><br/><br/>";
		$sms_client_full_detail .= __('Client Phone','oct').": ".$user_phone." ";
	}
	if($user_address!=''){
		$client_full_detail .="<span><strong>".__('Address','oct')."</strong>: ".$user_address."</span><br/><br/>";
		$sms_client_full_detail .= __('Address','oct').": ".$user_address." ";
	}
	if($user_city!=''){
		$client_full_detail .="<span><strong>".__('City','oct')."</strong>: ".$user_city."</span><br/><br/>";
		$sms_client_full_detail .= __('City','oct').": ".$user_city." ";
	}
	if($user_state!=''){
		$client_full_detail .="<span><strong>".__('State','oct')."</strong>: ".$user_state."</span><br/><br/>";
		$sms_client_full_detail .= __('State','oct').": ".$user_state." ";
	}
	if($user_notes!=''){
		$client_full_detail .="<span><strong>".__('Notes','oct')."</strong>: ".$user_notes."</span><br/><br/>";
		$sms_client_full_detail .= __('Notes','oct').": ".$user_notes." ";
	}
	$client_detail= $client_full_detail.$client_extra_email_detail;
	$sms_client_detail= $sms_client_full_detail.$client_extra_sms_detail;
	
	/* Send Email To Staff */
	if(get_option('octabook_service_provider_email_notification_status')=='E'){	
		$oct_staffemail_templates = new octabook_email_template();	
		$msg_template = $oct_staffemail_templates->email_parent_template;
		if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
			$oct_staffemail_templates->email_template_name = "CS"; 
		}else{
			$oct_staffemail_templates->email_template_name = "AS";  
		}
		$template_detail = $oct_staffemail_templates->readOne();        
		if($template_detail[0]->email_message!=''){            
			$email_content = $template_detail[0]->email_message;        
		}else{            
			$email_content = $template_detail[0]->default_message;
		}        
		$email_subject = $template_detail[0]->email_subject;   
		$email_staff_message = '';
		
		if($template_detail[0]->email_template_status=='e'){
			foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
				$oct_staff->id = $provider_id;
				$staffinfo = $oct_staff->readOne();
				$strtoprint = "";
				foreach ($bookingstrarr as $bookingsss) {
					$strtoprint .= $bookingsss;
				}

				$search = array('{{service_provider_name}}','{{booking_details}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
				
				$replace_with = array($staffinfo[0]['staff_name'],$strtoprint,$client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);

				$email_staff_message = str_replace($search,$replace_with,$email_content);
				$email_staff_message = str_replace('###msg_content###',$email_staff_message,$msg_template);
				add_filter( 'wp_mail_content_type', 'set_content_type' );
				$status = wp_mail($staffinfo[0]['email'],$email_subject,$email_staff_message,$headers);
			}
		}
	}
	
	/* Send Email To Admin */	
	$arr_admin_booking_detail='';        
	foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
			for($i=0;$i<sizeof((array)$bookingstrarr);$i++){                
				$arr_admin_booking_detail .=$bookingstrarr[$i];               
			}
	}	
	$arr_admin_bookingfulldetail=$arr_admin_booking_detail; 		
	if(get_option('octabook_admin_email_notification_status')=='E'){
		$oct_adminemail_templates = new octabook_email_template();
		$msg_template = $oct_adminemail_templates->email_parent_template;
		
		if(isset($_SESSION['booking_type']) ||get_option('octabook_appointment_auto_confirm')=='E'){
			$oct_adminemail_templates->email_template_name = "CA"; 
		}else{
			$oct_adminemail_templates->email_template_name = "AA";  
		}
		$template_detail = $oct_adminemail_templates->readOne();        
		if($template_detail[0]->email_message!=''){            
			$email_content = $template_detail[0]->email_message;        
		}else{            
			$email_content = $template_detail[0]->default_message;
		}        
		$email_subject = $template_detail[0]->email_subject;
		$email_admin_message = '';
		$admin_sender_name = get_option('octabook_email_sender_name');

	
		$search = array('{{admin_manager_name}}','{{booking_details}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{service_provider_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
		
		$replace_with = array($admin_sender_name,$arr_admin_bookingfulldetail,$client_detail,$company_name,'','',$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
		$email_admin_message = str_replace($search,$replace_with,$email_content);
		$email_admin_message = str_replace('###msg_content###',$email_admin_message,$msg_template);
		add_filter( 'wp_mail_content_type', 'set_content_type' );		
		$status = wp_mail(get_option('octabook_email_sender_address'),$email_subject,$email_admin_message,$headers); 	
	}
	
	/******************* Send Sms code START *********************/
	if(get_option("octabook_sms_reminder_status") == "E"){
		/** TWILIO **/
		if(get_option("octabook_sms_noti_twilio") == "E"){
			$obj_sms_template = new octabook_sms_template();
			$twillio_sender_number = get_option('octabook_twilio_number');
			$AccountSid = get_option('octabook_twilio_sid');
			$AuthToken =  get_option('octabook_twilio_auth_token'); 
			
			/* Send SMS To Client */
			if(get_option('octabook_twilio_client_sms_notification_status') == "E"){
				$twilliosms_client = new Client($AccountSid, $AuthToken);
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','CC');
				}else{
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','AC');
				}
				
				if($template1[0]->sms_template_status == "e" && $user_phone!=''){
					if($template1[0]->sms_message == ""){
						$message = strip_tags($template1[0]->default_message);
					}else{
						$message = strip_tags($template1[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					$client_sms_body = str_replace($search,$replace_with,$message);
					$response =$twilliosms_client->messages->create(
						$user_ccode.''.$user_phone,
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
				foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
					$oct_staff->id = $provider_id;
					$staffinfo = $oct_staff->readOne();
					if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
						$template = $obj_sms_template->gettemplate_sms("SP",'e','CS');
					}else{
						$template = $obj_sms_template->gettemplate_sms("SP",'e','AS');
					}
					
					if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
						if($template[0]->sms_message == ""){
							$message = strip_tags($template[0]->default_message);
						}else{
							$message = strip_tags($template[0]->sms_message);
						}
						$sender_name = get_option('octabook_email_sender_name');
						$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
						$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);

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
			}
			/* Send SMS To Admin */
			if(get_option('octabook_twilio_admin_sms_notification_status') == "E"){		   
				$twilliosms_admin = new Client($AccountSid, $AuthToken);
				
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template = $obj_sms_template->gettemplate_sms("AM",'e','CA');
				}else{
					$template = $obj_sms_template->gettemplate_sms("AM",'e','AA');
				}
				if($template[0]->sms_template_status == "e" && get_option('octabook_twilio_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$admin_ccode = get_option('octabook_twilio_ccode');
					$admin_phone = get_option('octabook_twilio_admin_phone_no');
					$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
					$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					
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
				
		/** PLIVO **/
		if(get_option("octabook_sms_noti_plivo") == "E"){
			$obj_sms_template = new octabook_sms_template();
			$plivo_sender_number = get_option('octabook_plivo_number');	
			$auth_sid = get_option('octabook_plivo_sid');
			$auth_token = get_option('octabook_plivo_auth_token');
			
			/* Send SMS To Client */
			if(get_option('octabook_plivo_client_sms_notification_status') == "E"){
				$p_client = new Plivo\RestAPI($auth_sid, $auth_token, '', '');
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','CC');
				}else{
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','AC');
				}
				if($template1[0]->sms_template_status == "e" && $user_phone!=''){
					if($template1[0]->sms_message == ""){
						$message = strip_tags($template1[0]->default_message);
					}else{
						$message = strip_tags($template1[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					$client_sms_body = str_replace($search,$replace_with,$message);
					
					$clientparams = array(
						'src' => $plivo_sender_number,
						'dst' => $user_ccode.''.$user_phone,
						'text' => $client_sms_body,
						'method' => 'POST'
					);
					$response = $p_client->send_message($clientparams);
				}
			}
			/* Send SMS To Staff */
			if(get_option('octabook_plivo_service_provider_sms_notification_status') == "E"){
				$p_staff = new Plivo\RestAPI($auth_sid, $auth_token, '', '');
				foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
					$oct_staff->id = $provider_id;
					$staffinfo = $oct_staff->readOne();
					if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
						$template = $obj_sms_template->gettemplate_sms("SP",'e','CS');
					}else{
						$template = $obj_sms_template->gettemplate_sms("SP",'e','AS');
					}
					
					if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
						if($template[0]->sms_message == ""){
							$message = strip_tags($template[0]->default_message);
						}else{
							$message = strip_tags($template[0]->sms_message);
						}
						$sender_name = get_option('octabook_email_sender_name');
						$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
						$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);

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
			}
			/* Send SMS To Admin */
			if(get_option('octabook_plivo_admin_sms_notification_status') == "E"){		   
				$p_admin = new Plivo\RestAPI($auth_sid, $auth_token, '', '');
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template = $obj_sms_template->gettemplate_sms("AM",'e','CA');
				}else{
					$template = $obj_sms_template->gettemplate_sms("AM",'e','AA');
				}
				if($template[0]->sms_template_status == "e" && get_option('octabook_plivo_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$admin_ccode = get_option('octabook_plivo_ccode');
					$admin_phone = get_option('octabook_plivo_admin_phone_no');
					$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
					$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					
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
		
		/** NEXMO **/
		if(get_option("octabook_sms_noti_nexmo") == "E"){
			$obj_sms_template = new octabook_sms_template();
			include_once(dirname(dirname(dirname(__FILE__))).'/objects/class_nexmo.php');
			$nexmo_client = new octabook_nexmo();
			$nexmo_client->octabook_nexmo_apikey = get_option('octabook_nexmo_apikey');
			$nexmo_client->octabook_nexmo_api_secret = get_option('octabook_nexmo_api_secret');
			$nexmo_client->octabook_nexmo_form = get_option('octabook_nexmo_form');
			
			/* Send SMS To Client */
			if(get_option('octabook_nexmo_send_sms_client_status') == "E"){
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','CC');
				}else{
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','AC');
				}
				if($template1[0]->sms_template_status == "e" && $user_phone!=''){
					if($template1[0]->sms_message == ""){
						$message = strip_tags($template1[0]->default_message);
					}else{
						$message = strip_tags($template1[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					$client_sms_body = str_replace($search,$replace_with,$message);
					$response = $nexmo_client->send_nexmo_sms($user_ccode.''.$user_phone,$client_sms_body);
				}
			}
			/* Send SMS To Staff */
			if(get_option('octabook_nexmo_send_sms_sp_status') == "E"){
				foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
					$oct_staff->id = $provider_id;
					$staffinfo = $oct_staff->readOne();
					if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
						$template = $obj_sms_template->gettemplate_sms("SP",'e','CS');
					}else{
						$template = $obj_sms_template->gettemplate_sms("SP",'e','AS');
					}
					
					if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
						if($template[0]->sms_message == ""){
							$message = strip_tags($template[0]->default_message);
						}else{
							$message = strip_tags($template[0]->sms_message);
						}
						$sender_name = get_option('octabook_email_sender_name');
						$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
						$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);

						$staff_sms_body = str_replace($search,$replace_with,$message);
						$response = $nexmo_client->send_nexmo_sms($staffinfo[0]['phone'],$staff_sms_body);
					}
				}
			}
			/* Send SMS To Admin */
			if(get_option('octabook_nexmo_send_sms_admin_status') == "E"){
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template = $obj_sms_template->gettemplate_sms("AM",'e','CA');
				}else{
					$template = $obj_sms_template->gettemplate_sms("AM",'e','AA');
				}
				if($template[0]->sms_template_status == "e" && get_option('octabook_nexmo_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
					$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					
					$admin_sms_body = str_replace($search,$replace_with,$message);
					$response = $nexmo_client->send_nexmo_sms(get_option('octabook_nexmo_ccode').get_option('octabook_nexmo_admin_phone_no'),$admin_sms_body);
				}
			}
		}
		
		/** TEXTLOCAL **/
		if(get_option("octabook_sms_noti_textlocal") == "E"){
			$obj_sms_template = new octabook_sms_template();
			$textlocal_apikey = get_option('octabook_textlocal_apikey');
			$textlocal_sender = get_option('octabook_textlocal_sender');
			
			/* Send SMS To Client */
			if(get_option('octabook_textlocal_client_sms_notification_status') == "E"){
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','CC');
				}else{
					$template1 = $obj_sms_template->gettemplate_sms("C",'e','AC');
				}
				if($template1[0]->sms_template_status == "e" && $user_phone!=''){
					if($template1[0]->sms_message == ""){
						$message = strip_tags($template1[0]->default_message);
					}else{
						$message = strip_tags($template1[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{customer_name}}','{{booking_details}}','{{booking_detail}}','{{company_name}}','{{service_provider_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					$replace_with = array($client_name,$booking_details_sms,$booking_details_sms,$company_name,'',$sender_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					$client_sms_body = str_replace($search,$replace_with,$message);
					
					$textlocal_numbers = $user_ccode.''.$user_phone;
					$textlocal_sender = urlencode($textlocal_sender);
					$client_sms_body = rawurlencode($client_sms_body);
					
					$data = array('apikey' => $textlocal_apikey, 'numbers' => $textlocal_numbers, "sender" => $textlocal_sender, "message" => $client_sms_body);
					
					$ch = curl_init('https://api.textlocal.in/send/');
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
				}
			}
			/* Send SMS To Staff */
			if(get_option('octabook_textlocal_service_provider_sms_notification_status') == "E"){
				foreach($arr_providers_booking_details as $provider_id => $bookingstrarr){
					$oct_staff->id = $provider_id;
					$staffinfo = $oct_staff->readOne();
					if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
						$template = $obj_sms_template->gettemplate_sms("SP",'e','CS');
					}else{
						$template = $obj_sms_template->gettemplate_sms("SP",'e','AS');
					}
					
					if($template[0]->sms_template_status == "e" && $staffinfo[0]['phone']!=''){
						if($template[0]->sms_message == ""){
							$message = strip_tags($template[0]->default_message);
						}else{
							$message = strip_tags($template[0]->sms_message);
						}
						$sender_name = get_option('octabook_email_sender_name');
						$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
						$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);

						$staff_sms_body = str_replace($search,$replace_with,$message);
						
						$textlocal_numbers = $staffinfo[0]['phone'];
						$textlocal_sender = urlencode($textlocal_sender);
						$staff_sms_body = rawurlencode($staff_sms_body);
						
						$data = array('apikey' => $textlocal_apikey, 'numbers' => $textlocal_numbers, "sender" => $textlocal_sender, "message" => $staff_sms_body);
						
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
			/* Send SMS To Admin */
			if(get_option('octabook_textlocal_admin_sms_notification_status') == "E"){
				if(isset($_SESSION['booking_type']) || get_option('octabook_appointment_auto_confirm')=='E'){
					$template = $obj_sms_template->gettemplate_sms("AM",'e','CA');
				}else{
					$template = $obj_sms_template->gettemplate_sms("AM",'e','AA');
				}
				if($template[0]->sms_template_status == "e" && get_option('octabook_textlocal_admin_phone_no')!=''){
					if($template[0]->sms_message == ""){
						$message = strip_tags($template[0]->default_message);
					}else{
						$message = strip_tags($template[0]->sms_message);
					}
					$sender_name = get_option('octabook_email_sender_name');
					$search = array('{{admin_manager_name}}','{{service_provider_name}}','{{booking_details}}','{{booking_detail}}','{{appoinment_client_detail}}','{{company_name}}','{{customer_name}}','{{admin_manager_name}}','{{company_address}}','{{company_city}}','{{company_state}}','{{company_zip}}','{{company_country}}','{{company_phone}}','{{company_email}}','{{company_logo}}');
					
					$replace_with = array($sender_name,$staffinfo[0]['staff_name'],$booking_details_sms,$booking_details_sms,$sms_client_detail,$company_name,'',$admin_manager_name,$company_address,$company_city,$company_state,$company_zip,$company_country,$company_phone,$company_email,$company_logo);
					
					$admin_sms_body = str_replace($search,$replace_with,$message);

					$textlocal_numbers = get_option('octabook_textlocal_ccode').get_option('octabook_textlocal_admin_phone_no');
					$textlocal_sender = urlencode($textlocal_sender);
					$admin_sms_body = rawurlencode($admin_sms_body);
					
					$data = array('apikey' => $textlocal_apikey, 'numbers' => $textlocal_numbers, "sender" => $textlocal_sender, "message" => $admin_sms_body);
					
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
	}
	/******************* Send Sms code END *********************/
	
	
	if($_SESSION['oct_detail']['payment_method'] == 'paypal'){
		$thankyou_url = get_option('octabook_thankyou_page');
		if($thankyou_url!=''){
			?>
			<script>
				window.location.href = '<?php echo $thankyou_url; ?>';
			</script>
			<?php
		}else{
			?>
			<script>
				window.location.href = '<?php echo site_url().'/oct-thankyou/'; ?>';
			</script>
			<?php
		}
	}
	if($_SESSION['oct_detail']['payment_method'] == 'payumoney'){
		$thankyou_url = get_option('octabook_thankyou_page');
		if($thankyou_url!=''){
			?>
			<script>
				window.location.href = '<?php echo $thankyou_url; ?>';
			</script>
			<?php
		}else{
			?>
			<script>
				window.location.href = '<?php echo site_url().'/oct-thankyou/'; ?>';
			</script>
			<?php
		}
	}
	if($_SESSION['oct_detail']['payment_method'] == 'paytm'){
		$thankyou_url = get_option('octabook_thankyou_page');
		if($thankyou_url!=''){
			?>
			<script>
				window.location.href = '<?php echo $thankyou_url; ?>';
			</script>
			<?php
		}else{
			?>
			<script>
				window.location.href = '<?php echo site_url().'/oct-thankyou/'; ?>';
			</script>
			<?php
		}
		/* $thankyou_url = get_option('octabook_thankyou_page');
		if($thankyou_url!=''){
			echo $thankyou_url;
		}else{
			echo site_url().'/oct-thankyou/';
		} */
	}
}
/* booking complete code here END */
