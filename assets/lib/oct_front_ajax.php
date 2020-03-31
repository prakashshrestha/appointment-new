<?php  
	if(!session_id()) { @session_start(); }
    $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));	
	if (file_exists($root.'/wp-load.php')) {
		require_once($root.'/wp-load.php');	
	}		
	
	$plugin_url = plugins_url('',  dirname(__FILE__));
	$base =   dirname(dirname(dirname(__FILE__)));
	$uploaded_images_path = dirname(dirname(dirname(plugins_url( '/',dirname(__FILE__))))).'/uploads/';
	$currency_symbol = get_option('octabook_currency_symbol');

	include_once($base.'/objects/class_general.php');
	include_once($base.'/objects/class_location.php');
	include_once($base.'/objects/class_service.php');
	include_once($base.'/objects/class_service_schedule_price.php');
	include_once($base.'/objects/class_category.php');
	include_once($base.'/objects/class_provider.php');
	include_once($base.'/objects/class_front_octabook_first_step.php');
	include_once($base.'/objects/class_coupons.php');
	include_once($base.'/objects/class_clients.php');
	include_once($base.'/objects/class_booking.php');
	include_once($base.'/objects/class_payments.php');
	include_once($base.'/objects/class_email_templates.php');
	
	/* declare classes */
	$oct_general = new octabook_general();	
	$oct_location = new octabook_location();
	$oct_service = new octabook_service();
	$oct_service_schedule_price = new octabook_service_schedule_price();
	$oct_category = new octabook_category();
	$oct_staff = new octabook_staff();
	$first_step = new octabook_first_step();
	$oct_coupons = new octabook_coupons();
	$oct_client_info = new octabook_clients();	
	$oct_booking = new octabook_booking();
	$oct_payments = new octabook_payments();
	$oct_email_templates = new octabook_email_template();
	
/* Get Location By Zip Code */
if(isset($_POST['action'],$_POST['zipcode']) && $_POST['action']=='oct_get_location' && $_POST['zipcode']!='')
{


	$areazipcodes = explode(',',get_option('octabook_booking_zipcodes'));
	if(in_array($_POST['zipcode'],$areazipcodes)){
		echo 'found';
	}else{
		echo 'notfound';
	}	
}
 
/* Get Service By Location ID */
if(isset($_POST['action'],$_POST['location_id']) && $_POST['action']=='oct_get_location_services' && $_POST['location_id']!='')
{
	$oct_service->location_id = $_POST['location_id'];
	$octservices = $oct_service->readAll('');
	$services_categories = array();
	$location_services = array();
	foreach($octservices as $octservice){
		if(!in_array($octservice->category_id, $services_categories)){
			$services_categories[] = $octservice->category_id;
		}
		$location_services[] = $octservice->id;
	}
	if(sizeof((array)$services_categories)>0){
		?> <div class="row"> <?php
		foreach($services_categories as $services_category){		
			$oct_category->id = $services_category;
			$oct_category->readOne();
			?>
			  	
			<?php
			$oct_service->service_category = $services_category;
			$cat_services = $oct_service->readAll_category_services();
			
			foreach($cat_services as $cat_service){
				if(in_array($cat_service->id,$location_services)){
				 ?>
				 <div class="select_custom one-third" data-sid="<?php echo $cat_service->id;?>">
                    <h4 class="oct-box-country"><?php echo $cat_service->service_title; ?></h4>
				</div>
					
				 <?php
				}														
			}

		}
		?> </div> <?php
	}else{ ?>
		<li class="data-list select_custom" data-sid="">
			<div class="oct-value oct-no-found"><?php echo __("No service found for this location.","oct");?></div>
		</li>
	<?php }
}


/* Get Service Detail by Service ID */
if(isset($_POST['action'],$_POST['sid']) && $_POST['action']=='oct_get_service_detail' && $_POST['sid']!='')
{ 
	
	
	$serviceextinfo = array();
	$service_desc_status = get_option('octabook_show_service_desc');
	$service_description = '';
	
	/* Get Sevice Description If Enable */
	if($service_desc_status=='E'){	
			$oct_service->id = $_POST['sid'];	
			$oct_service->readOne('');
			$hours = '';
			$minutes = '';
			if(floor($oct_service->duration/60)!=0){ 
				$hours 	=  floor($oct_service->duration/60); 
				$hours .=  __("Hrs","oct");
			} 
			if($oct_service->duration%60 !=0){ 
				$minutes =  $oct_service->duration%60; 
				$minutes .=  __(" Mins","oct");
			
			}
			$offerclass = '';
			if($oct_service->offered_price!=''){ 
				$offerclass =  'td-line-through'; 
			}
			$offerpriceshow = '';
			if($oct_service->offered_price!=''){ 
				$offerpriceshow = '<h5 class="service-price np nm oct-sm-6 oct-md-3  oct-xs-12" title="Offer Price"><strong>'. __("Offered Price -","oct").'</strong><span class="oct-offered-price">'.$oct_general->oct_price_format($oct_service->offered_price).'</span></h5>';
			}
			
			
			$service_image = '';
			if($oct_service->image==''){ 
				$service_image =  $plugin_url.'/images/service.png';
			}else{
				$service_image =  $uploaded_images_path.$oct_service->image;
			}
			
				
			
			$service_description ='<div class="service-details-container fullwidth">
			<div class="oct-desc-header fullwidth">
			<a href="javascript:void(0)" id="close_service_details"><i class="icon-close-custom icons oct-close-desc"></i></a>
			<h5 class="service-duration np nm oct-sm-6 oct-md-4 oct-xs-12"><strong>'. __("Service duration - ","oct").'</strong><span class="oct-service-duration"><i class="icon-clock icons"></i>'.$hours.' '.$minutes.'</span></h5>
			<h5 class="service-price actual-price np nm oct-sm-6 oct-md-3 oct-xs-12" title="Actual Price"><strong>'.__("Service price - ","oct").'</strong><span class="oct-actual-price '.$offerclass.'">'. $oct_general->oct_price_format($oct_service->amount).'</span></h5>'.$offerpriceshow.'</div>
			<div class="oct-sm-12 oct-xs-12 np">
			<div class="oct-service-desc"><div class="oct-service-image-main pull-left"><img class="oct-service-img" src="'.$service_image.'" /></div><div class="oct-service-desc-p">'.$oct_service->service_description.'</div></div></div></div>';
	}

	/* Get Service Addons */
	$oct_service->selected_service_id = $_POST['sid'];
	$serviceaddons = $oct_service->get_all_addons();
	
	$serviceaddoninfo = '';
	if(sizeof((array)$serviceaddons)>0){
		$serviceaddoninfo = '<h3 class="block-title"><i class="icon-puzzle icons fs-20"></i>'.__("Extra Services","oct").'</h3><div class="pr oct-sm-12 oct-xs-12 np"><div class="oct-extra-services-list oct-common-box"><ul class="addon-service-list fullwidth np">';
		
		foreach($serviceaddons as $serviceaddon){
			$addonimagepath = plugins_url( 'images/addon/sample.png',dirname(__FILE__));
			if(isset($serviceaddon->image) && $serviceaddon->image!=''){
				$uplodpathinfo = wp_upload_dir();
				$addonimagepath = $uplodpathinfo['baseurl'].$serviceaddon->image;			
			}
			$addonquantitybuttons = '';
			if(isset($serviceaddon->multipleqty) && $serviceaddon->multipleqty=='Y'){
				$addonquantitybuttons = '<div class="oct-addon-count oct-addon-count'.$serviceaddon->id.' border-c add_minus_button"><div class="oct-btn-group"><button data-addonmax="'.$serviceaddon->maxqty.'" data-addonid="'.$serviceaddon->id.'" id="minus'.$serviceaddon->maxqty.'" data-qtyaction="minus" class="minus oct-btn-left oct-small-btn oct_addonqty" type="button">-</button><input type="text" value="1" id="addonqty_'.$serviceaddon->id.'" class="oct-btn-text addon_qty" /><button data-addonmax="'.$serviceaddon->maxqty.'" data-addonid="'.$serviceaddon->id.'" id="add'.$serviceaddon->maxqty.'" data-qtyaction="add" class="add oct-btn-right pull-right oct-small-btn oct_addonqty" type="button">+</button></div></div>';		
			}
			
			
			$serviceaddoninfo .='<li class="oct-sm-6 oct-md-4 oct-lg-3 oct-xs-12 mb-15"><input type="checkbox" name="addon-checkbox'.$serviceaddon->id.'" data-saddonid="'.$serviceaddon->id.'" data-saddonmaxqty="'.$serviceaddon->multipleqty.'" class="addon-checkbox" id="oct-addon-'.$serviceaddon->id.'" /><label class="oct-addon-ser border-c" data-addonid="'.$serviceaddon->id.'" for="oct-addon-'.$serviceaddon->id.'"><span></span><div class="addon-price fullwidth">'.$oct_general->oct_price_format($serviceaddon->base_price).'</div><div class="oct-addon-img"><img src="'.$addonimagepath.'" /></div></label>'.$addonquantitybuttons.'<div class="addon-name fullwidth text-center">'.$serviceaddon->addon_service_name.'</div></li>';			
				
		}
		$serviceaddoninfo .= '</ul></div><a id="oct-continue-addon-service" href="javascript:void(0)">Continue</a></div>';
		

	}
	$serviceextinfo['description'] = $service_description;
	$serviceextinfo['addonsinfo'] = $serviceaddoninfo;
	
	echo json_encode($serviceextinfo);
	
}

/* GET Service Providers */
if(isset($_POST['action'],$_POST['sid']) && $_POST['action']=='oct_get_service_providers' && $_POST['sid']!='')
{
	$default_service_img = $plugin_url."/images/staff.png";
	$oct_staff->service_id = $_POST['sid'];
	$service_staffs = $oct_staff->read_staffs_by_service_id();
	$provider_avatar_view = get_option('octabook_show_provider_avatars');
	if($provider_avatar_view=='E'){ ?>
		<div class="oct-service-staff-list oct-common-box">
			<ul class="staff-list fullwidth np">
				<?php 
				if(sizeof((array)$service_staffs)>0){
				$uplodpathinfo = wp_upload_dir();
				foreach($service_staffs as $octstaff){ 
				$staffimagepath = plugins_url( 'images/provider/staff.png',dirname(__FILE__));
				if(isset($octstaff['image']) && $octstaff['image']!=''){	
					$staffimagepath = $uplodpathinfo['baseurl'].$octstaff['image'];			
				}
				?>
				<li data-staffid="<?php echo $octstaff['id'];?>" class="oct-staff-box oct-sm-4 oct-md-3 oct-lg-3 oct-xs-12 mb-15">
					<input type="radio" name="provider_list" class="staff-radio" id="oct-staff-<?php echo $octstaff['id'];?>" />
					<label class="oct-staff border-c" for="oct-staff-<?php echo $octstaff['id'];?>">
						<span class="br-100"></span>
						<div class="oct-staff-img ">
							<img class="br-100" src="<?php echo $staffimagepath;?>" /> 
						</div>
						<div class="staff-name fullwidth text-center"><?php echo $octstaff['staff_name'];?></div>
					</label>											
				</li>
				<?php }
			}else{ ?>
			<li class="oct-staff-box oct-sm-12 oct-md-12 oct-lg-12 oct-xs-12 mb-12">
				<span class="oct-error oct-show"><?php echo __("No provider found with enable status","oct");?></span>							
			</li>												
			<?php }			
			?>
		</ul>
	</div>
	<?php }else{ ?>
	<div id="cus-select-staff" class="cus-select-staff fullwidth custom-input nmt">
		<div class="common-selection-main staff-selection">
			<div class="selected-is select-staff" title="<?php echo __("Choose service provider","oct");?>">
				<div class="data-list" id="selected_custom_staff">
					<div class="oct-value"><?php echo __("Choose service provider","oct");?></div>
				</div>
			</div>
			<ul id="oct_staffs" class="common-data-dropdown staff-dropdown custom-dropdown">												
				<?php
				if(sizeof((array)$service_staffs)>0){
					foreach($service_staffs as $octstaff){ ?>
							<li class="data-list select_staff" data-staffid="<?php echo $octstaff['id'];?>">
								<div class="oct-value" ><?php echo $octstaff['staff_name']; ?></div>
							</li>
						 <?php														
					 }	
				}else{ ?>
				<li class="data-list select_custom" data-sid="">
					<div class="oct-value" ><?php echo __("No provider found with enable status","oct");?></div>
				</li>
				<?php } ?>															
			</ul>
		</div>
	</div>
	<i class="bottom-line"></i>		
	<?php } 
}
/* Get Provider Time Slots */
if(isset($_POST['action'],$_POST['selstaffid']) && $_POST['action']=='oct_get_provider_slots' && $_POST['selstaffid']!='')
{
	$selecteddate = date_i18n('Y-m-d',$_POST['seldate']);
	$selectedstaffid = $_POST['selstaffid'];
	
	$service_id = $_POST['service_id'];
	
	$oct_service->id = $service_id;
	$oct_service->readOne();
	
	$service_duration = $oct_service->duration;
	
	$octstaff = new octabook_staff();
	$octstaff->id = $selectedstaffid;
	$provider_result = $octstaff->readOne();
	
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
	
	/* overlapping code here */
	$not_available_slots_timestemp = array();
	$newtimeslot_array = array();
	if(isset($_SESSION['oct_cart_item'])){
		foreach($_SESSION['oct_cart_item'] as $oct_cart){
			$oct_cart_unserialize = unserialize($oct_cart);
			if($oct_cart_unserialize['selected_staff'] == $_POST['selstaffid']){
				$cart_id = $oct_cart_unserialize['id'];
				$start_time = $oct_cart_unserialize['selected_datetime'];
				$end_time = $oct_cart_unserialize['selected_enddatetime'];
				$newtimeslot_array[] = array('starttime'=>$start_time,'endtime'=>$end_time);
				if($booking_dayclosing == "D"){
					if ($service_duration > $time_interval) {
						$service_duration_check_minutes = $service_duration - $time_interval;
						$previous_start = strtotime("-$service_duration_check_minutes minutes", $start_time);
						$not_available_slots_timestemp[] = $previous_start;
						$previous_end = $start_time;
						$newtimeslot_array[] = array('starttime'=>$previous_start,'endtime'=>$previous_end);
					}
				}
			}
		}
	}
	
	/* Get Google Calendar Bookings of Provider */
	$providerTwoSync = 'Y';
	$providerCalenderBooking = array();
	if($providerTwoSync=='Y'){
		$curlevents = curl_init();
		curl_setopt_array($curlevents, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $plugin_url.'/GoogleCalendar/event.php?cdate='.$start_date,
			CURLOPT_FRESH_CONNECT =>true,
			CURLOPT_USERAGENT => 'OctaBook'
		));
		$response = curl_exec($curlevents);    
		curl_close($curlevents);
		$provider_events = array();
		$provider_events = json_decode($response);

		$providerCalenderBooking = array();
		if(isset($provider_events)){
			foreach($provider_events as $providerevent){ 
				$startdate = date_i18n('Y-m-d', strtotime($providerevent->start)); 
				$starttime = date_i18n('H:i:s', strtotime($providerevent->start));
				$enddate = date_i18n('Y-m-d', strtotime($providerevent->end));
				$endtime = date_i18n('H:i:s', strtotime($providerevent->end));

				$GCslotstart = mktime(date_i18n('H',strtotime($starttime)),date_i18n('i',strtotime($starttime)),date_i18n('s',strtotime($starttime)),date_i18n('n',strtotime($startdate)),date_i18n('j',strtotime($startdate)),date_i18n('Y',strtotime($startdate))); 

				$GCslotend = mktime(date_i18n('H',strtotime($endtime)),date_i18n('i',strtotime($endtime)),date_i18n('s',strtotime($endtime)),date_i18n('n',strtotime($enddate)),date_i18n('j',strtotime($enddate)),date_i18n('Y',strtotime($enddate)));

				$providerCalenderBooking[] = array('start'=>$GCslotstart,'end'=>$GCslotend);
			}
		}
	}
	/*****************************************************************/
	/*****************************************************************/
	
	if($time_schedule['off_day']!=true  && isset($time_schedule['slots']) && sizeof((array)$time_schedule['slots'])>0 && $allbreak_counter != sizeof((array)$time_schedule['slots'])){
		foreach($time_schedule['slots']  as $slot) {
			/* echo $slot; */
			$curreslotstr = strtotime(date_i18n('Y-m-d H:i:s',strtotime($start_date.' '.$slot)));
			$gccheck = 'N';
			/*Checking in GC booked Slots */
			if(sizeof((array)$providerCalenderBooking)>0){
				for($i = 0; $i < sizeof((array)$providerCalenderBooking); $i++) {
					if($curreslotstr >= $providerCalenderBooking[$i]['start'] && $curreslotstr < $providerCalenderBooking[$i]['end']){
						$providerCalenderBooking[$i]['start'];
						$gccheck = 'Y';
					}
				}
			}
			
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
			
			$slots_timestamp = strtotime($selecteddate." ".$slot);
			
			$complete_time_slot = mktime(date('H',strtotime($slot)),date('i',strtotime($slot)),date('s',strtotime($slot)),date('n',strtotime($time_schedule['date'])),date('j',strtotime($time_schedule['date'])),date('Y',strtotime($time_schedule['date']))); 
			
			/*  check oveerlapping slots with cart */
			$session_check = 'N';
			/* Check Session Slots */
			if(sizeof((array)$newtimeslot_array)>0 && get_option('octabook_multiple_booking_sameslot')=='D'){
				foreach($newtimeslot_array as $session_slot){
				
					if ($slots_timestamp>=$session_slot['starttime'] && $slots_timestamp<$session_slot['endtime'] && get_option('octabook_hide_booked_slot')=='E'){
						$session_check = 'Y';
					}else if($slots_timestamp>=$session_slot['starttime'] && $slots_timestamp<$session_slot['endtime']){
						if(in_array($session_slot['starttime'],$not_available_slots_timestemp)){
						?>
							<li class="time-slot br-5 oct-booked" style="background-color: #808080 !important;">
								<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>
								<br>Not Available
							</li>
						<?php    
						}else{
						?>
							<li class="time-slot br-5 oct-booked" style="background-color: #f43166 !important;">
								<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>
								<br>Available
							</li>
						<?php    
						}
						$session_check = 'Y';
					}					
				}
			}
			if($session_check=='Y'){
				$slot_counter++; 
					continue;
			}
			
			/* Check for the multiple booking sameslot Enable */
			if(get_option('octabook_multiple_booking_sameslot') == "D"){	
				if(get_option('octabook_hide_booked_slot')=='E' && (in_array($complete_time_slot,$time_schedule['booked']) || $gccheck=='Y')) {
					continue;
				}
			}
			$timestamp = strtotime(date_i18n(get_option('date_format'),strtotime($selecteddate))." ".date_i18n(get_option('time_format'),strtotime($slot)));
			$date = date("Y-m-d H:i:s", $timestamp);
			$date2 = substr( $date, 0, -1 );
			global $wpdb;
			$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."oct_bookings 
			Where booking_datetime='".$date2."'");
			$counted = count((array)$result);
			if(isset($time_schedule['booked']) && (in_array($complete_time_slot,$time_schedule['booked']) || $gccheck=='Y') && (get_option('octabook_multiple_booking_sameslot')=='D') ) {
				
			?>
				<li class="time-slot br-2 oct-booked" style="background-color: #808080 !important;">
					<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>
					<br>Not Available
				</li>
			<?php
				}elseif(get_option('octabook_multiple_booking_sameslot')=='E' && $counted >= get_option('octabook_slot_max_booking_limit') && get_option('octabook_slot_max_booking_limit')>0){
				?>
				<li class="time-slot br-2 oct-booked" style="background-color: #808080 !important;">
				<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>
				<br>Available
				</li>
				<?php
				}
			else {
				
			?>
				<li class="time-slot br-5 time_slotss oct_select_slot" data-slot_db_date="<?php echo date_i18n('Y-m-d',strtotime($selecteddate)); ?>" data-slot_db_time="<?php echo date_i18n("H:i:s",strtotime($slot)); ?>" data-displaydate="<?php echo date_i18n(get_option('date_format'),strtotime($selecteddate)); ?>" data-displaytime="<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>" >
					<?php echo date_i18n(get_option('time_format'),strtotime($slot)); ?>
					<br>Available
				</li>					
			<?php 
			} $slot_counter++; 
		} 			
		if($allbreak_counter == sizeof((array)$time_schedule['slots']) && sizeof((array)$time_schedule['slots'])!=0){ ?>
			<li class="time-slot br-5 oct-none-available fullwidth"><?php echo "None of time slot available for "; echo date_i18n(get_option('date_format'),strtotime($time_schedule['date']))?><?php echo " Please check another dates";?><br>Not Available</li>
		<?php }else if(isset($time_schedule['offtimes'],$time_schedule['slots']) && $allofftime_counter > sizeof((array)$time_schedule['offtimes']) && sizeof((array)$time_schedule['slots'])==$allofftime_counter){ ?>
			<li class="time-slot br-5 oct-none-available"><?php echo "None of time slot available for "; echo date_i18n(get_option('date_format'),strtotime($time_schedule['date']))?><?php echo " Please check another dates";?><br>Not Available</li>
		<?php }
		else if($gccheck == 'Y') {
			?>
			<li class="time-slot br-5 oct-none-available"><?php echo "None of time slot available for "; echo date_i18n(get_option('date_format'),strtotime($time_schedule['date']))?><?php echo " Please check another dates";?><br>Not Available</li>
		<?php } /* */
		
		} else {
			?>
			<li class="time-slot br-5 oct-none-available"><?php echo "None of time slot available for "; echo date_i18n(get_option('date_format'),strtotime($time_schedule['date']))?><?php echo " Please check another dates";?><br>Not Available</li>
		<?php }
}


/* Previous/Next Month Calendar */
if(isset($_POST['action'],$_POST['calmonth'],$_POST['calyear']) && $_POST['action']=='oct_cal_next_prev' && $_POST['calmonth']!='' && $_POST['calyear']!='')
{
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $base.'/assets/tooltipster.bundle.min.css'?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $base.'/assets/tooltipster-sideTip-shadow.min.css' ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $base.'/assets/js/tooltipster.bundle.min.js' ?>">
	
	
<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('.ct-tooltip').tooltipster({
		animation: 'grow',
		delay: 20,
		theme: 'tooltipster-shadow',
		trigger: 'hover'
	});
	jQuery('.ct-tooltipss').tooltipster({
		animation: 'grow',
		delay: 20,
		theme: 'tooltipster-shadow',
		trigger: 'hover'
	});
	jQuery('.ct-tooltip-services').tooltipster({
		animation: 'grow',
		side: 'top',
		interactive: 'true',
		theme: 'tooltipster-shadow',
		trigger: 'hover',
		delayTouch: 300,
		maxWidth: 400,
		contentAsHTML: 'true'
	});
});
</script>
<?php
	$selectedstaffid = $_POST['selected_staff'];
	$service_id = $_POST['service_id'];
	$oct_service->id = $service_id;
	$oct_service->readOne();
	
	$month= $_POST['calmonth'];
	$year= $_POST['calyear'];
	$currentdate = mktime(12, 0, 0,$month, 1,$year);	
	$calnextmonth = strtotime('+1 month',$currentdate);
	$calprevmonth=strtotime('-1 month', $currentdate);
	$oct_maxadvance_booktime = get_option('octabook_maximum_advance_booking');
	$calmaxdate = strtotime('+'.$oct_maxadvance_booktime.' month',strtotime(date_i18n("Y-m-d")));	
	$monthdays = date_i18n("t", $currentdate);
	$offset = date_i18n("w", $currentdate) - get_option('start_of_week');
	$rows = 1;
	$in_offset = 0;
	
	$prevmonthlink =  strtotime(date_i18n("Y-m-d",$currentdate));
	$currrmonthlink =  strtotime(date_i18n("Y-m-d"));
	?>
	<div class="calendar-header">
		<?php if($currrmonthlink < $prevmonthlink){ ?>
		<a class="previous-date oct_month_change" data-curmonth="<?php echo date_i18n('m');?>" data-curyear="<?php echo date_i18n('Y');?>" data-calyear="<?php echo date_i18n("Y", $calprevmonth); ?>" data-calmonth="<?php echo date_i18n("m", $calprevmonth); ?>" data-calaction="prev" href="javascript:void(0)"><i class="icon-arrow-left icons"></i></a>
		<?php }else{ ?>
			<a data-curmonth="<?php echo date_i18n('m');?>" data-curyear="<?php echo date_i18n('Y');?>" class="previous-date" href="javascript:void(0)"><i class="icon-arrow-left icons"></i></a>
		<?php } ?>
		<div class="calendar-title"><?php echo date_i18n('F',$currentdate); ?></div>		
		<div class="calendar-year"><?php echo date_i18n('Y',$currentdate); ?></div>
		
		<?php
		if(date_i18n('M',$calmaxdate) == date_i18n('M',$currentdate) && date_i18n('Y',$calmaxdate) == date_i18n('Y',$currentdate)){ ?>
		<a class="next-date" href="javascript:void(0)"><i class="icon-arrow-right icons"></i></a>
		<?php }else{ ?>
		<a class="next-date oct_month_change" data-calyear="<?php echo date_i18n("Y", $calnextmonth); ?>" data-calmonth="<?php echo date_i18n("m", $calnextmonth); ?>" data-calaction="next" href="javascript:void(0)"><i class="icon-arrow-right icons"></i></a>
		<?php } ?>	
	</div><!-- end calendar-header -->	
			<div class="calendar-body fullwidth">
				<div class="weekdays fullwidth">
					<?php if(get_option('start_of_week') == '1') {
						$in_offset = $offset;
						?>						
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
					<?php } elseif(get_option('start_of_week') == '2') { 
						$in_offset = 7+$offset;
						?>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
						<div class="oct-day oct-last-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
					<?php } elseif(get_option('start_of_week') == '3') { 
						$in_offset = 7+$offset;
						?>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
					<?php } elseif(get_option('start_of_week') == '4') { 
						$in_offset = 7+$offset;
						?>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
					<?php } elseif(get_option('start_of_week') == '5') { 	
						$in_offset = 7+$offset;
						?>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
					<?php } elseif(get_option('start_of_week') == '6') { 
						$in_offset = 7+$offset;
						?>
						<div class="oct-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>	
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
					<?php } else { 
						$in_offset = $offset;
						?>												
						<div class="oct-day ">
							<span><?php echo __("Sun","oct");?></span>
						</div>												
						<div class="oct-day">
							<span><?php echo __("Mon","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Tue","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Wed","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Thu","oct");?></span>
						</div>
						<div class="oct-day">
							<span><?php echo __("Fri","oct");?></span>
						</div>
						<div class="oct-day oct-last-day">
							<span><?php echo __("Sat","oct");?></span>
						</div>
					<?php } ?>													
				</div><!-- end row -->
				
			<div class="dates">	
			<?php /*<div class="dates">*/
			if($in_offset >= 7){
				$in_offset = $in_offset - 7;
			}								  
			  for($i = 1; $i <= $in_offset; $i++)
			  {	?>
				<div class="oct-week inactive"></div>
			  <?php
			  } 
			  $rowtemparray = array();
			  $k = 0;
			  for($day = 1; $day <= $monthdays; $day++)
			  {
				$selected_dates = $day."-".$month."-".$year;
				$calsel_date = strtotime($selected_dates);
				
				/* code of available slot & booked slot*/
				$slots_array = json_decode(get_provider_slot_count($selectedstaffid,$service_id,$calsel_date));
				$total_slot = $slots_array->Count_Total_Slots;
				$available_slot = $slots_array->Avail_Slots;
				$book_slot = (int)$total_slot-(int)$available_slot;
				if($available_slot=='')
				{
					$available_slot=0;
				}
				$calcurr_date = strtotime(date_i18n('d-m-Y'));
				?>
				
				<?php
				if( ($day + $offset - 1) % 7 == 0 && $day != 1){
				   $k = $k+7;
				  ?>
				  </div> 
				  <!--<div class="oct-week"></div> -->
				  
				  <div class="oct-show-time curr_selected_row<?php echo $k;?>">
						<div class="time-slot-container">
							<div class="oct-slot-legends">
								<ul class="oct-legends-ul">
									<li><span class="oct-slot-legends-box oct-available-new"></span><?php echo __("Available","oct");?></li>
									<li><span class="oct-slot-legends-box oct-selected-new"></span><?php echo __("Selected","oct");?></li>
									<li><span class="oct-slot-legends-box oct-not-available-new"></span><?php echo __("Not Available","oct");?></li><br>
								</ul>
							</div>
							<ul class="list-inline time-slot-ul oct_day_slots"></ul>
						</div>
					</div>
				  <div class="dates">												  
				  <?php
				  $rows++;
				}		
				?>
				<div data-seldate="<?php echo $calsel_date;?>" data-calrowid="<?php if($day < 35){echo $k+7; }else{echo $k;} ?>"  class="oct-week ct-tooltip-services tooltipstered <?php if($calsel_date==$calcurr_date){ echo 'by_default_today_selected';} if($calsel_date<$calcurr_date || $calsel_date>$calmaxdate){ echo 'inactive';} ?> oct-slots-count" title="<?php echo 'Available Slot-'.$available_slot."</br>".'Booked Slot-'.$book_slot; ?>"><a href="javascript:void(0)"><span><?php echo $day; ?></span></a></div>  
				<?php
			}
			
			/* while( ($day + $offset) <= $rows * 7)
			{
				?>
				<div class="oct-week "></div>									
				<?php
				 $day++;
			} */
			?>	
			</div>
			<div class="oct-show-time curr_selected_row<?php echo $k+7;?>">
				<div class="time-slot-container">
					<div class="oct-slot-legends">
						<ul class="oct-legends-ul">
							<li><span class="oct-slot-legends-box oct-available-new"></span><?php echo __("Available","oct");?></li>
							<li><span class="oct-slot-legends-box oct-selected-new"></span><?php echo __("Selected","oct");?></li>
							<li><span class="oct-slot-legends-box oct-not-available-new"></span><?php echo __("Not Available","oct");?></li><br>
						</ul>
					</div>
					<ul class="list-inline time-slot-ul oct_day_slots"></ul>
				</div>
			</div>
			<div class="today-date">
				<a class="oct-button oct-button  today_btttn oct-lg-offset-1" data-smonth="<?php echo $month;?>" data-syear="<?php echo $year;?>"><?php echo __("Today","oct");?></a>
				<div class="oct-selected-date-view oct-lg-pull-1 oct-hide">
					<span class="custom-check">
						<span class="add_date oct-date-selected"></span>
						<span class="add_time oct-time-selected"></span>
					</span>
				</div>
			</div>
			<!-- end calendar-wrapper -->
		</div>
	</div>
<?php	
}

/* Set Item Into Cart */
if((isset($_POST['action'],$_POST['selected_location'],$_POST['selected_service'],$_POST['selected_staff'],$_POST['service_addon_st'],$_POST['selected_datetime']) && $_POST['action']=='add_item_into_cart' && $_POST['selected_location']!='' && $_POST['selected_service']!='' && $_POST['selected_staff']!='' && $_POST['selected_datetime']!='') || (isset($_POST['action']) && $_POST['action']=='refresh_sidebar'))
{	
	$oct_booking_summary = '';
if($_POST['action']!='refresh_sidebar'){
	$itemrandom_number = rand(1000, 9999);
	/* Booking Summary HTML */
	$oct_booking_summary = '<div class="booking-list br-3 fullwidth">
						<a class="oct-delete-booking-box oct_remove_item" data-cartitemid="'.$itemrandom_number.'" href="javascript:void(0)">'.__("Delete","oct").'</a>
						<div class="right-booking-details oct-md-12 oct-sm-12 oct-xs-12 np pull-left">';
	
	/* POST Data Variables */	
	$locationid = $_POST['selected_location'];
	$serviceid = $_POST['selected_service'];
	$staffid = $_POST['selected_staff'];
	$selected_datetime = strtotime($_POST['selected_datetime']);
	$service_addon_st = $_POST['service_addon_st'];
	$total_price = 0;
	$service_amount = 0;
	$oct_mulitlocation_status = get_option('octabook_multi_location');
	/* Get Location Information If Enabled */
	if(($locationid!=0 || $locationid!='X') && $oct_mulitlocation_status=='E'){
		$oct_location->id = $locationid;
		$oct_locationinfo = $oct_location->readOne();
		$oct_booking_summary .= '<div class="common-style location-title fullwidth"><i class="icon-location-pin icons"></i>'.$oct_locationinfo[0]->address.', '.$oct_locationinfo[0]->city.' '.$oct_locationinfo[0]->state.' '.$oct_locationinfo[0]->zip.','.$oct_locationinfo[0]->country.' </div>';
		
	}
	
	/* Get Service Info */
	$oct_service->id = $serviceid;
	$oct_service->readOne();
	$service_title = $oct_service->service_title;
	$service_duration = $oct_service->duration;
	$service_amount = $oct_service->amount;
	if($oct_service->offered_price!=''){
		$service_amount = $oct_service->offered_price;
	}
	$service_starttime = $selected_datetime;
	$service_endtime = strtotime('+'.$service_duration.' minutes',$selected_datetime);
	
	
	/* Get Selected Provider Information */
	$oct_staff->id = $staffid;
	$staff_info = $oct_staff->readOne();
	
	
	/* Check If Service Slot Specific Price is Enabled */
	if($staff_info[0]["schedule_type"]=='W'){	
	$weekid = 1;	
	}else{
	$weekid = $first_step->get_week_of_month_by_date(date_i18n('Y-m-d',$selected_datetime));
	}
	$weekdayid = date_i18n('N',$selected_datetime);
	$oct_service_schedule_price->provider_id = $staffid;
	$oct_service_schedule_price->service_id = $serviceid;
	$oct_service_schedule_price->weekid = $weekid;
	$oct_service_schedule_price->weekdayid = $weekdayid;
	$serviceprice_infos = $oct_service_schedule_price->readOne_ssp();

	if(sizeof((array)$serviceprice_infos)>0){
		foreach($serviceprice_infos as $serviceprice_info){
			$ssp_starttime = $serviceprice_info->ssp_starttime;
			$ssp_startend = $serviceprice_info->ssp_endtime;
			if(strtotime(date_i18n('H:i:s',$service_starttime)) >= strtotime($ssp_starttime) && strtotime(date_i18n('H:i:s',$service_endtime)) <= strtotime($ssp_startend)){
				$service_amount = $serviceprice_info->price;
			}		
		}
	}
	
	/* Service Booking Summary */
	$oct_booking_summary .= '<div class="common-style fullwidth">
								<i class="icon-settings icons"></i><div class="oct-xs-9 np oct-left-text service-title">'.$service_title.'</div>
								<div class="oct-xs-3 np oct-right-text text-right service-price">'.$service_amount.$oct_general->oct_price_format($service_amount).'</div>
							</div>';
	
	
	
	
	
	/* If Selected Service Addon is Enabled Get Selected Addons Information */
	$addon_price = 0;
	$service_addon_total = 0;
	$service_extra_addon_total = 0;
	$oct_selected_service_addons = '';
	$eachaddonprice = array();
	$service_addons = array();
	if($_POST['service_addon_st']=='E'){
	  if(isset($_POST['serviceaddons']) && sizeof((array)$_POST['serviceaddons'])>0){
		foreach($_POST['serviceaddons'] as $selectedaddon){
			$addon_id = $selectedaddon['addonid'];
			$addon_qty = $selectedaddon['maxqty'];
			$oct_service->addon_id = $addon_id;
			$oct_addoninfo = $oct_service->readOne_addon();
			$addon_price = $oct_addoninfo[0]-> 	base_price;
			
			if($oct_addoninfo[0]->multipleqty=='Y'){
				$oct_service->addon_service_id = $addon_id;
				$get_addonpricingrules = $oct_service->readall_qty_addon();	
				if(sizeof((array)$get_addonpricingrules)>0){
					foreach($get_addonpricingrules as $get_addonpricingrule){
						if($get_addonpricingrule->rules=='E' && $get_addonpricingrule->unit==$addon_qty){
							$addon_price = $get_addonpricingrule->rate;
						}elseif($get_addonpricingrule->rules=='G' && $get_addonpricingrule->unit<=$addon_qty){
							
								$addon_price = $get_addonpricingrule->rate;							
						}
					}
				}
			}else{
				$addon_qty = 1;
			}
			
			$eachaddonprice[] = array('addonid'=>$addon_id,'addon_price'=>$addon_price); 
			$service_addons[] = array('addonid'=>$addon_id,'maxqty'=>$addon_qty); 
			$service_addon_total = (double)$addon_qty*(double)$addon_price;
			$service_extra_addon_total += (double)$service_addon_total;	
			$oct_selected_service_addons .= '<li class="oct-es">
											<i class="icon-minus icons oct-delete-icon"></i><div class="oct-xs-9 np oct-left-text service-title">'.$oct_addoninfo[0]->addon_service_name.'</div><div class="oct-xs-3 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($service_addon_total).'</div>
											<a data-cartitemid="'.$itemrandom_number.'" data-addonid="'.$addon_id.'" class="oct-delete-confirm oct_remove_addon" href="javascript:void(0)">'.__("Delete","oct").'</a>
										</li>';			
		}
	}	
	$oct_booking_summary .= '<div class="common-style fullwidth">
								<i class="icon-settings icons"></i><div class="oct-xs-9 np oct-left-text service-title">'.__("Extra Services","oct").'</div>
								<div class="oct-xs-3 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($service_extra_addon_total).'</div>								
								<div class="oct-extra-services-main mb-5 fullwidth">
									<ul class="extra-services-items fullwidth">';	
									
	$oct_booking_summary .= $oct_selected_service_addons;			
		
	$oct_booking_summary .='</ul>
								</div>
							</div>';		
	}
		
		
	
	/* Display Staff Information */
	$oct_booking_summary .='<div class="common-style date fullwidth"><i class="icon-user icons"></i>'.$staff_info[0]["staff_name"].'</div>';
	
	/* Booking Date & Time Information */
	$oct_booking_summary .='<div class="common-style date fullwidth"><i class="icon-calendar icons"></i>'.date_i18n(get_option('date_format'),$selected_datetime).'</div>	<div class="common-style time fullwidth"><i class="icon-clock icons"></i>'.date_i18n(get_option('time_format'),$service_starttime).' '.__("to","oct").' '.date_i18n(get_option('time_format'),$service_endtime).'</div>';
	
	/* Booking Item Total Price */
	$total_item_price = $service_amount+$service_extra_addon_total;
	$oct_booking_summary .='<div class="price last-item fullwidth">
								<div class="oct-xs-8 np oct-left-text">'.__("Item Price","oct").'</div>
								<div class="oct-xs-4 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($total_item_price).'</div>
							</div>';
	
	$oct_booking_summary .= '</div>
						<div class="delete pull-right oct-delete-booking" title="'.__("Delete Service","oct").'"><span></span></div>
					</div>';
					
	

	
		
	
	/* Set Session OF Cart Item */		
	$oct_cart_item = array();
	$oct_cart_item = array('id'=>$itemrandom_number,'selected_location'=>$locationid,'selected_service'=>$serviceid,'selected_staff'=>$staffid,'selected_datetime'=>$selected_datetime,'selected_enddatetime'=>$service_endtime,'total_price'=>$total_item_price,'service_price'=>$service_amount,'total_addon_price'=>$service_addon_total,'each_addon_price'=>$eachaddonprice,'service_addon_status'=>$_POST['service_addon_st'],'service_addons'=>$service_addons);
	
	$_SESSION['oct_cart_item'][$itemrandom_number] = serialize($oct_cart_item); 
	
	if(isset($_SESSION['oct_sub_total'])){
		 $_SESSION['oct_sub_total'] = $_SESSION['oct_sub_total']+$total_item_price;
	}else{
		$_SESSION['oct_sub_total'] = $total_item_price;
	}
}		
	
	

	$oct_amount_summary = '';
	$oct_partial_deposit_summary = '';
	$oct_partial_deposit_status = get_option('octabook_partial_deposit_status');
	$oct_taxvat_status = get_option('octabook_taxvat_status');
	if(isset($_SESSION['oct_sub_total'])){
		
		/* if($_POST['action']!='refresh_sidebar'){ */		
			/* Tax Wat Information */
			$oct_taxvat = 0;		
			if($oct_taxvat_status=='E'){
				$oct_taxvat_type = get_option('octabook_taxvat_type');
				$oct_taxvat_amount = get_option('octabook_taxvat_amount');		
				if($oct_taxvat_type=='P'){
					if($oct_taxvat_amount!=''){
						$oct_taxvat = $_SESSION['oct_sub_total']*$oct_taxvat_amount/100;
					}
				}else{
					if($oct_taxvat_amount!=''){
						$oct_taxvat = $oct_taxvat_amount;
					}	
				}
				
			}	
			$_SESSION['oct_taxvat'] = $oct_taxvat;	
			
			
			/* Partial Deposit Information */
			$oct_partialdeposit = 0;
			$oct_partialdeposit_remaining = 0;		
			if($oct_partial_deposit_status=='E'){
				$oct_partial_deposit_type = get_option('octabook_partial_deposit_type');
				$oct_partial_deposit_amount = get_option('octabook_partial_deposit_amount');		
				if($oct_partial_deposit_type=='P'){
					if($oct_partial_deposit_amount!=''){
						$oct_partialdeposit = ($_SESSION['oct_sub_total']+$_SESSION['oct_taxvat'])*$oct_partial_deposit_amount/100;
						$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
					}
				}else{
					if($oct_partial_deposit_amount!=''){
						$oct_partialdeposit = $oct_partial_deposit_amount;
						$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
					}	
				}
				
			}
			
			$_SESSION['oct_partialdeposit'] = $oct_partialdeposit;  
			$_SESSION['oct_partialdeposit_remaining'] = $oct_partialdeposit_remaining;  
			/* $_SESSION['oct_nettotal'] = $oct_taxvat+$_SESSION['oct_sub_total']; */
			
		/* } */
		
		$subtotalamount = $_SESSION['oct_sub_total'];
		$_SESSION['oct_nettotal'] = $oct_taxvat+$_SESSION['oct_sub_total'];
		if(isset($_SESSION['oct_coupon_discount'])){ 
			/* $subtotalamount = $_SESSION['oct_sub_total']+$_SESSION['oct_coupon_discount']; */
			$subtotalamount = $_SESSION['oct_sub_total'];
			$discounted_amount = $_SESSION['oct_sub_total']-$_SESSION['oct_coupon_discount'];
			$_SESSION['oct_nettotal'] = $discounted_amount + $_SESSION['oct_taxvat'];
			
		}
		
		/* Partial Deposit Information */
			$oct_partialdeposit = 0;
			$oct_partialdeposit_remaining = 0;		
			if($oct_partial_deposit_status=='E'){
				$oct_partial_deposit_type = get_option('octabook_partial_deposit_type');
				$oct_partial_deposit_amount = get_option('octabook_partial_deposit_amount');		
				if($oct_partial_deposit_type=='P'){
					if($oct_partial_deposit_amount!=''){
						$oct_partialdeposit = ($_SESSION['oct_nettotal'])*$oct_partial_deposit_amount/100;
						$oct_partialdeposit_remaining = $_SESSION['oct_nettotal']-$oct_partialdeposit;
					}
				}else{
					if($oct_partial_deposit_amount!=''){
						$oct_partialdeposit = $oct_partial_deposit_amount;
						$oct_partialdeposit_remaining = $_SESSION['oct_nettotal']-$oct_partialdeposit;
					}	
				}
				
			}
			
			$_SESSION['oct_partialdeposit'] = $oct_partialdeposit;  
			$_SESSION['oct_partialdeposit_remaining'] = $oct_partialdeposit_remaining;  
		$oct_amount_summary .= '<div class="oct-xs-12 np">
									<div class="common-amount-text">'.__("Sub Total","oct").'</div>
									<div class="common-amount-price">'.$oct_general->oct_price_format($subtotalamount).'</div>
								</div>													
								<div class="clear"></div>';
		
		if(isset($_SESSION['oct_coupon_discount'])){ 	
		$oct_amount_summary .='<div class="oct-xs-12 np">
									<div class="common-amount-text">'.__("Coupon Discount","oct").'</div>
									<div class="common-amount-price discount-price">-'.$oct_general->oct_price_format($_SESSION['oct_coupon_discount']).'</div>
								</div>';
		}	
		
		if($oct_taxvat_status=='E'){
			$oct_amount_summary .= '<div class="oct-xs-12 np">
									<div class="common-amount-text">'.__("Tax Amount","oct").'</div>
									<div class="common-amount-price">'.$oct_general->oct_price_format($_SESSION['oct_taxvat']).'</div>
								</div>';
		}
			
		$oct_amount_summary .= '<div class="oct-xs-12 npl npr hr-both">
									<div class="common-amount-text total-amount">'.__("Payable Amount","oct").'</div>
									<div class="common-amount-price total-price">'.$oct_general->oct_price_format($_SESSION['oct_nettotal']).'</div>
								</div>';
		
		
		$oct_partial_deposit_summary = '';
		if($oct_partial_deposit_status=='E'){
		$oct_partial_deposit_message = get_option('octabook_partial_deposit_message');
		$oct_partial_deposit_summary = '<div class="partial-amount-message">'.$oct_partial_deposit_message.'</div>
								<div class="oct-form-row">
									<div class="oct-xs-12 np">
										<div class="common-amount-text">'.__("Partial Deposit","oct").'</div>
										<div class="common-amount-price ">'.$oct_general->oct_price_format($_SESSION['oct_partialdeposit']).'</div>
									</div>
								</div>
								<div class="oct-form-row">
									<div class="oct-xs-12 np">
										<div class="common-amount-text">'.__("Remaining Deposit","oct").'</div>
										<div class="common-amount-price">'.$oct_general->oct_price_format($_SESSION['oct_partialdeposit_remaining']).'</div>
									</div>
								</div>';	
		}
	
	}

	$octabook_show_coupons = get_option('octabook_show_coupons');
	
	?>
	<div class="oct-sidebar-header">
		<h3 class="header3"><?php echo __("Booking Summary","oct");?><div class="oct-cart-items-count"> <i class="icon-bag icons fs-22 pull-right pr"><span class="oct_badge"><?php if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){ echo sizeof((array)$_SESSION['oct_cart_item']); }else{ echo '0'; } ?></span></i></div></h3>
	</div>
	<div id="oct_booking_summary" class="sidebar-box <?php if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){ echo 'oct_cart_item_exist'; }else{ echo 'oct_cart_item_not_exist'; } ?>">
		<?php 
		
		/* Loop Existing Cart Items */
		if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){
			foreach($_SESSION['oct_cart_item'] as $cart_item_detail){
				$cart_item = unserialize($cart_item_detail);
				
				/* POST Data Variables */	
				$locationid = $cart_item['selected_location'];
				$serviceid = $cart_item['selected_service'];
				$staffid = $cart_item['selected_staff'];
				$selected_datetime = $cart_item['selected_datetime'];
				$selected_enddatetime = $cart_item['selected_enddatetime'];
				$cartitem_id = $cart_item['id'];
				$service_addon_st = $cart_item['service_addon_status'];
				$service_addons = $cart_item['service_addons'];
				$total_price = 0;			
				$service_amount = 0;				
							
				/* Booking Summary HTML */
				$oct_booking_summary = '<div class="booking-list br-3 fullwidth">
				<a class="oct-delete-booking-box oct_remove_item" data-cartitemid="'.$cartitem_id.'" href="javascript:void(0)">'.__("Delete","oct").'</a>
				<div class="right-booking-details oct-md-12 oct-sm-12 oct-xs-12 np pull-left">';
				
				
				$oct_mulitlocation_status = get_option('octabook_multi_location');
				/* Get Location Information If Enabled */
				if(($locationid!=0 || $locationid!='X') && $oct_mulitlocation_status=='E'){
					$oct_location->id = $locationid;
					$oct_locationinfo = $oct_location->readOne();
					$oct_booking_summary .= '<div class="common-style location-title fullwidth"><i class="icon-location-pin icons"></i>'.$oct_locationinfo[0]->address.', '.$oct_locationinfo[0]->city.' '.$oct_locationinfo[0]->state.' '.$oct_locationinfo[0]->zip.','.$oct_locationinfo[0]->country.' </div>';
					
				}
				/* Get Service Info */
				$oct_service->id = $serviceid;
				$oct_service->readOne();
				$service_title = $oct_service->service_title;
				$service_duration = $oct_service->duration;
				$service_amount = $oct_service->amount;
				if($oct_service->offered_price!=''){
					$service_amount = $oct_service->offered_price;
				}
				$service_starttime = $selected_datetime;
				$service_endtime = strtotime('+'.$service_duration.' minutes',$selected_datetime);	
				
				
				/* Get Selected Provider Information */
				$oct_staff->id = $staffid;
				$staff_info = $oct_staff->readOne();
				
				
				/* Check If Service Slot Specific Price is Enabled */
				if($staff_info[0]["schedule_type"]=='W'){	
				$weekid = 1;	
				}else{
				$weekid = $first_step->get_week_of_month_by_date(date_i18n('Y-m-d',$selected_datetime));
				}
				$weekdayid = date_i18n('N',$selected_datetime);
				$oct_service_schedule_price->provider_id = $staffid;
				$oct_service_schedule_price->service_id = $serviceid;
				$oct_service_schedule_price->weekid = $weekid;
				$oct_service_schedule_price->weekdayid = $weekdayid;
				$serviceprice_infos = $oct_service_schedule_price->readOne_ssp();
				if(sizeof((array)$serviceprice_infos)>0){
					foreach($serviceprice_infos as $serviceprice_info){
						$ssp_starttime = $serviceprice_info->ssp_starttime;
						$ssp_startend = $serviceprice_info->ssp_endtime;
						if(strtotime(date_i18n('H:i:s',$service_starttime)) >= strtotime($ssp_starttime) && strtotime(date_i18n('H:i:s',$service_endtime)) <= strtotime($ssp_startend)){
							$service_amount = $serviceprice_info->price;
						}		
					}
				}
				/* Service Booking Summary */
					$oct_booking_summary .= '<div class="common-style fullwidth">
						<i class="icon-settings icons"></i><div class="oct-xs-9 np oct-left-text service-title">'.$service_title.'</div>
						<div class="oct-xs-3 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($service_amount).'</div>
					</div>';
				
				/* If Selected Service Addon is Enabled Get Selected Addons Information */
				$addon_price = 0;
				$service_addon_total = 0;
				$service_extra_addon_total = 0;
				$oct_selected_service_addons = '';
				$eachaddonprice = array();
				if($service_addon_st=='E' && sizeof((array)$service_addons)>0){		
					foreach($service_addons as $selectedaddon){
					
						$addon_id = $selectedaddon['addonid'];
						$addon_qty = $selectedaddon['maxqty'];
						$oct_service->addon_id = $addon_id;
						$oct_addoninfo = $oct_service->readOne_addon();
						$addon_price = $oct_addoninfo[0]->base_price;
					
						if($oct_addoninfo[0]->multipleqty=='Y'){
							$oct_service->addon_service_id = $addon_id;
							$get_addonpricingrules = $oct_service->readall_qty_addon();	
						
							if(sizeof((array)$get_addonpricingrules)>0){
								foreach($get_addonpricingrules as $get_addonpricingrule){
									if($get_addonpricingrule->rules=='E' && $get_addonpricingrule->unit==$addon_qty){
										$addon_price = $get_addonpricingrule->rate;
										
									}elseif($get_addonpricingrule->rules=='G' && $get_addonpricingrule->unit<=$addon_qty){
											$addon_price = $get_addonpricingrule->rate;								
									}
								}
							}
						}
						$eachaddonprice[] = array('addonid'=>$addon_id,'addon_price'=>$addon_price); 
						$service_addon_total = (double)$addon_qty*(double)$addon_price;
						$service_extra_addon_total += (double)$service_addon_total;
						$oct_selected_service_addons .= '<li class="oct-es">
														<i class="icon-minus icons oct-delete-icon"></i><div class="oct-xs-9 np oct-left-text service-title">'.$oct_addoninfo[0]->addon_service_name.'</div><div class="oct-xs-3 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($service_addon_total).'</div>
														<a data-cartitemid="'.$cartitem_id.'" data-addonid="'.$addon_id.'" class="oct-delete-confirm oct_remove_addon" href="javascript:void(0)">'.__("Delete","oct").'</a>
													</li>';			
					}
					
				$oct_booking_summary .= '<div class="common-style fullwidth">
											<i class="icon-settings icons"></i><div class="oct-xs-9 np oct-left-text service-title">'.__("Extra Services","oct").'</div>
											<div class="oct-xs-3 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($service_extra_addon_total).'</div>								
											<div class="oct-extra-services-main mb-5 fullwidth">
												<ul class="extra-services-items fullwidth">';	
												
				$oct_booking_summary .= $oct_selected_service_addons;			
					
				$oct_booking_summary .='</ul>
											</div>
										</div>';		
				}
				
				/* Display Staff Information */
				$oct_booking_summary .='<div class="common-style date fullwidth"><i class="icon-user icons"></i>'.$staff_info[0]["staff_name"].'</div>';
				
				/* Booking Date & Time Information */
				$oct_booking_summary .='<div class="common-style date fullwidth"><i class="icon-calendar icons"></i>'.date_i18n(get_option('date_format'),$selected_datetime).'</div>	<div class="common-style time fullwidth"><i class="icon-clock icons"></i>'.date_i18n(get_option('time_format'),$service_starttime).' '.__("to","oct").' '.date_i18n(get_option('time_format'),$service_endtime).'</div>';
				
				
				/* Booking Item Total Price */
				$total_item_price = $service_amount+$service_extra_addon_total;
				$oct_booking_summary .='<div class="price last-item fullwidth">
											<div class="oct-xs-8 np oct-left-text">'.__("Item Price","oct").'</div>
											<div class="oct-xs-4 np oct-right-text text-right service-price">'.$oct_general->oct_price_format($total_item_price).'</div>
										</div>';
				
				$oct_booking_summary .= '</div>
									<div class="delete pull-right oct-delete-booking" title="'.__("Delete Service","oct").'"><span></span></div>
								</div>';			
				
				if(get_option('booking_cart_description')=='E')
					{
					echo $oct_booking_summary;
					}
			}				
		}else { ?>
			<h2 class="oct-empty-cart"><i class="icon-handbag icons"></i> <?php echo __("Your Cart is Empty!","oct");   ?></h2> <?php die(); ?>
			
		<?php } ?>				
	</div>
	
	<div class="oct-button-container text-center oct-add-more-btn">
		<a class="oct-button pull-left" id="btn-more-bookings" href="javascript:void(0)"><i class="icon-arrow-left icons"></i><?php echo __("Add more","oct");?></a>					
	</div>				
	
	
	<div class="oct-checkout-content">				
		
		
		<div class="sidebar-box">	
			<div class="clear"></div>
			<div id="oct_amount_summary" class="oct-total-amount">
				<?php echo $oct_amount_summary;?>
				<div class="clear"></div>
			</div>
			
		</div>
		
		<?php if($octabook_show_coupons=='E'){ ?>	
		<div class="oct-discount-partial fullwidth">
			<div class="discount-coupons fullwidth">
				<?php if(!isset($_SESSION['oct_coupon_discount'])){ ?>
				<div class="oct-form-row oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 np">
					<div class="pr coupon-input">
						<input type="text" class="custom-input coupon-input-text" id="oct-coupon" />
						<a href="javascript:void(0);" id="oct_apply_coupon" data-action="apply" class="oct-link apply-coupon" ><?php echo __("Apply","oct");?></a>
						<label class="custom oct-coupon-label"><?php echo __("Have a Promocode?","oct");?></label>
						<i class="bottom-line"></i>
					</div>
					<span class="oct-error oct_promocode_error"><?php echo __("Invalid Coupon code","oct");?></span>
				</div>
				<?php } ?>
				<!-- display coupon -->
				<?php if(isset($_SESSION['oct_coupon_discount'])){ ?>
				<div class="display-coupon-code">
					<div class="oct-form-row fullwidth">	
						<div class="oct-xs-7">
							<label class="oct-relative oct_promocode_success"><?php echo __("Applied Promocode","oct");?></label>
						</div>
						<div class="oct-xs-5 pull-right">
							<div class="coupon-value-main">
								<span class="coupon-value br-5 "><?php echo $_SESSION['oct_coupon_code'];?></span>
								<i class="icon-close icons br-100"  data-action="reverse" id="remove_applied_coupon"  title="Remove applied coupon" ></i>
						
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>						
		</div>
		<?php } ?>
		
		<?php if($oct_partial_deposit_status=='E'){ ?>			
		<div class="oct-discount-partial fullwidth">
			<div id="oct_partial_deposit_summary" class="partial-amount-wrapper br-5 cb">
				<?php echo $oct_partial_deposit_summary;?>
			</div>
		</div>
		<?php } ?>
		<div class="oct-button-container text-center fullwidth">
			<a class="oct-button btn-x-large" id="btn-third-step" href="javascript:void(0)"><?php echo __("Checkout","oct");?></a>
		</div>
	</div>
	<?php
				if(get_option('octabook_payment_method_Payumoney') == 'E'){
				?>
            <form action="https://secure.payu.in/_payment" method="post" name="payuForm" id="payuForm">
            <!--form action="https://sandboxsecure.payu.in/_payment" method="post" name="payuForm" id="payuForm"-->
				<input type="hidden" name="key" id="payu_key" value="" />
				<input type="hidden" name="hash" id="payu_hash" value=""/>
				<input type="hidden" name="txnid" id="payu_txnid" value="" />
				<input type="hidden" name="amount" id="payu_amount" value="" />
				<input type="hidden" name="firstname" id="payu_fname" value="" />
				<input type="hidden" name="email" id="payu_email" value="" />
				<input type="hidden" name="phone" id="payu_phone" value="" />
				<input type="hidden" name="productinfo" id="payu_productinfo" value="" />
				<input type="hidden" name="surl" id="payu_surl" value="" />
				<input type="hidden" name="furl" id="payu_furl" value="" />
				<input type="hidden" name="service_provider" id="payu_service_provider" value="" />
			</form>
			
<?php	
}
	if(get_option('octabook_payment_method_Paytm') == 'E'){
		?>
		<form method="post" action="" name="oct_paytm_form" id="oct_paytm_form">
			<input type="hidden" id="oct_CHECKSUMHASH" name="CHECKSUMHASH" value="">
		</form>
		<?php
	}

}

			?>
<?php
/* Delete Cart Item */
if(isset($_POST['action'],$_POST['cartitemid']) && $_POST['action']=='oct_delete_cart_item' && $_POST['cartitemid']!='' && isset($_SESSION['oct_cart_item'])){
	
	$cartitem_id = $_POST['cartitemid'];
	$deleteitem_info  = unserialize($_SESSION['oct_cart_item'][$cartitem_id]);
	$_SESSION['oct_sub_total'] = $_SESSION['oct_sub_total']-$deleteitem_info['total_price'];
	
	/* Tax Wat Information */
	$oct_taxvat = 0;
	$oct_taxvat_status = get_option('octabook_taxvat_status');
	if($oct_taxvat_status=='E'){
		$oct_taxvat_type = get_option('octabook_taxvat_type');
		$oct_taxvat_amount = get_option('octabook_taxvat_amount');		
		if($oct_taxvat_type=='P'){
			if($oct_taxvat_amount!=''){
				$oct_taxvat = $_SESSION['oct_sub_total']*$oct_taxvat_amount/100;
			}
		}else{
			if($oct_taxvat_amount!=''){
				$oct_taxvat = $oct_taxvat_amount;
			}	
		}
		
	}	
	$_SESSION['oct_taxvat'] = $oct_taxvat;	
	
	
	/* Partial Deposit Information */
	$oct_partialdeposit = 0;
	$oct_partialdeposit_remaining = 0;
	$oct_partial_deposit_status = get_option('octabook_partial_deposit_status');
	if($oct_partial_deposit_status=='E'){
		$oct_partial_deposit_type = get_option('octabook_partial_deposit_type');
		$oct_partial_deposit_amount = get_option('octabook_partial_deposit_amount');		
		if($oct_partial_deposit_type=='P'){
			if($oct_partial_deposit_amount!=''){
				$oct_partialdeposit = ($_SESSION['oct_sub_total']+$_SESSION['oct_taxvat'])*$oct_partial_deposit_amount/100;
				$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
			}
		}else{
			if($oct_partial_deposit_amount!=''){
				$oct_partialdeposit = $oct_partial_deposit_amount;
				$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
			}	
		}
		
	}
	
	$_SESSION['oct_partialdeposit'] = $oct_partialdeposit;  
	$_SESSION['oct_partialdeposit_remaining'] = $oct_partialdeposit_remaining;  
	$_SESSION['oct_nettotal'] = $oct_taxvat+$_SESSION['oct_sub_total'];	
	/* $_SESSION['service_addon_total']=$service_addon_total; */
	unset($_SESSION['oct_cart_item'][$cartitem_id]);
	
	if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])==0){	
		unset($_SESSION['oct_partialdeposit']);
		unset($_SESSION['oct_partialdeposit_remaining']);
		unset($_SESSION['oct_nettotal']);
		unset($_SESSION['oct_taxvat']);
		unset($_SESSION['oct_sub_total']);
		unset($_SESSION['service_addon_total']);
		unset($_SESSION['oct_coupon_id']);
		unset($_SESSION['oct_coupon_code']);
		unset($_SESSION['oct_coupon_discount']);
	}	
}

/* Delete Addon From Cart Item Item */
if(isset($_POST['action'],$_POST['addonid'],$_POST['cartitemid']) && $_POST['action']=='oct_delete_addon' && $_POST['cartitemid']!='' && $_POST['addonid']!=''){
	
	$cartitem_id = $_POST['cartitemid'];
	$deleteitem_info  = unserialize($_SESSION['oct_cart_item'][$cartitem_id]);
	
	$addondelete_itemtotal = $deleteitem_info['total_price'];
	$removeaddonprice = 0;
	if(sizeof((array)$deleteitem_info['each_addon_price'])>0){
		$removeaddonpri = 0;
		$removeaddon_qty = 0;
		foreach($deleteitem_info['each_addon_price'] as $addonarraykey => $addonprices){
			if($addonprices['addonid']==$_POST['addonid'] && $addonprices['addon_price']!=''){
				$removeaddonpri = $addonprices['addon_price'];
			}
			if($addonprices['addonid']==$_POST['addonid']){
				unset($deleteitem_info['each_addon_price'][$addonarraykey]);
			}
		}
		
		foreach($deleteitem_info['service_addons'] as $saddonarraykey => $serviceaddons){
			if($serviceaddons['addonid']==$_POST['addonid']){
				$removeaddon_qty = $serviceaddons['maxqty'];
				unset($deleteitem_info['service_addons'][$saddonarraykey]);
			}
		}
		
		$removeaddonprice = $removeaddon_qty * $removeaddonpri;
		$addondelete_itemtotal = $deleteitem_info['total_price']-$removeaddonprice;
		
	}

	$_SESSION['oct_cart_item'][$cartitem_id] = serialize(array('id'=>$deleteitem_info['id'],'selected_location'=>$deleteitem_info['selected_location'],'selected_service'=>$deleteitem_info['selected_service'],'selected_staff'=>$deleteitem_info['selected_staff'],'selected_datetime'=>$deleteitem_info['selected_datetime'],'selected_enddatetime'=>$deleteitem_info['selected_enddatetime'],'total_price'=>$addondelete_itemtotal,'service_price'=>$deleteitem_info['service_price'],'total_addon_price'=>$deleteitem_info['total_addon_price'],'each_addon_price'=>$deleteitem_info['each_addon_price'],'service_addon_status'=>$deleteitem_info['service_addon_status'],'service_addons'=>$deleteitem_info['service_addons']));
	
	$_SESSION['oct_sub_total'] = $_SESSION['oct_sub_total']-$removeaddonprice;
	/* Tax Wat Information */
	$oct_taxvat = 0;
	$oct_taxvat_status = get_option('octabook_taxvat_status');
	if($oct_taxvat_status=='E'){
		$oct_taxvat_type = get_option('octabook_taxvat_type');
		$oct_taxvat_amount = get_option('octabook_taxvat_amount');		
		if($oct_taxvat_type=='P'){
			if($oct_taxvat_amount!=''){
				$oct_taxvat = $_SESSION['oct_sub_total']*$oct_taxvat_amount/100;
			}
		}else{
			if($oct_taxvat_amount!=''){
				$oct_taxvat = $oct_taxvat_amount;
			}	
		}
		
	}	
	$_SESSION['oct_taxvat'] = $oct_taxvat;	
	
	
	/* Partial Deposit Information */
	$oct_partialdeposit = 0;
	$oct_partialdeposit_remaining = 0;
	$oct_partial_deposit_status = get_option('octabook_partial_deposit_status');
	if($oct_partial_deposit_status=='E'){
		$oct_partial_deposit_type = get_option('octabook_partial_deposit_type');
		$oct_partial_deposit_amount = get_option('octabook_partial_deposit_amount');		
		if($oct_partial_deposit_type=='P'){
			if($oct_partial_deposit_amount!=''){
				$oct_partialdeposit = ($_SESSION['oct_sub_total']+$_SESSION['oct_taxvat'])*$oct_partial_deposit_amount/100;
				$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
			}
		}else{
			if($oct_partial_deposit_amount!=''){
				$oct_partialdeposit = $oct_partial_deposit_amount;
				$oct_partialdeposit_remaining = $_SESSION['oct_sub_total']+$_SESSION['oct_taxvat']-$oct_partialdeposit;
			}	
		}
		
	}	
	$_SESSION['oct_partialdeposit'] = $oct_partialdeposit;  
	$_SESSION['oct_partialdeposit_remaining'] = $oct_partialdeposit_remaining;  
	$_SESSION['oct_nettotal'] = $oct_taxvat+$_SESSION['oct_sub_total'];	
}

/* Apply/Reverse Coupon */
if(isset($_POST['action'],$_POST['couponaction']) && $_POST['action']=='oct_coupon_ar'){
	
	
	if($_POST['couponaction']=='apply'){
		$couponcode = $_POST['coupon_code'];
		$oct_coupons->coupon_code = $couponcode;
		$checkcouponinfos = $oct_coupons->oct_check_applied_coupon();
		
		$bookinglocations = array();
		if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){
			foreach($_SESSION['oct_cart_item'] as $cart_item_detail){
				$cart_item = unserialize($cart_item_detail);
				$bookinglocations[] = $cart_item['selected_location'];
				
			}	
		}
		$couponcodelocations = array();
		if(sizeof((array)$checkcouponinfos)>0){
			foreach($checkcouponinfos as $checkcouponinfo){
				$couponcodelocations[] = $checkcouponinfo->location_id;
				
			}
			
		}
		$couponexistance = array_intersect($couponcodelocations,$bookinglocations);
	
		if(sizeof((array)$couponexistance)>0){
			$couponcunter = 0;
			$coupon_location = '';
			$coupon_id = '';
			$coupon_type = '';
			$coupon_value = 0;
			$coupon_used = 0;
			foreach($couponexistance as $couponexistances){
				if($couponcunter==0){
					$coupon_location = $couponexistances;					
				}
				$couponcunter++;
			}	
			foreach($checkcouponinfos as $coupon_detail){
				if($coupon_location==$coupon_detail->location_id){
						$coupon_id = $coupon_detail->id;
						$coupon_type = $coupon_detail->coupon_type;
						$coupon_value = $coupon_detail->coupon_value;
						$coupon_used = $coupon_detail->coupon_used;
				}
			}
			if($coupon_id!='' && $coupon_location!=''){
				if($coupon_type=='P'){	
					$coupon_discount = $_SESSION['oct_sub_total']*$coupon_value/100;
					
					$discountedsubtotal = $_SESSION['oct_sub_total']-$coupon_discount;
					
					$vat_tax = $_SESSION['oct_taxvat'];
					if($discountedsubtotal<0){						
						$_SESSION['oct_coupon_discount'] = $coupon_discount;
						$_SESSION['oct_nettotal'] = 0;
					}else{
						
						$_SESSION['oct_nettotal'] = $discountedsubtotal+$vat_tax;
						$_SESSION['oct_coupon_discount'] = $coupon_discount;	
					}					
					$_SESSION['oct_coupon_id'] = $coupon_id;
					$_SESSION['oct_coupon_code'] = $couponcode;
				}else{	
					
					$discountedsubtotal = $_SESSION['oct_sub_total']-$coupon_value;
					$vat_tax = $_SESSION['oct_taxvat'];
				   
					if($discountedsubtotal<0){
						$_SESSION['oct_coupon_discount'] = $coupon_value;
						$_SESSION['oct_nettotal'] = 0;
					}else{
						$_SESSION['oct_nettotal'] = $discountedsubtotal+$vat_tax;
						$_SESSION['oct_coupon_discount'] = $coupon_value;
					}					
					$_SESSION['oct_coupon_id'] = $coupon_id;
					$_SESSION['oct_coupon_code'] = $couponcode;
					
				}
				$oct_coupons->id = $coupon_id;
				$oct_coupons->coupon_used = $coupon_used+1;
				$checkcouponinfos = $oct_coupons->oct_update_coupon_used();
				
				echo 'ok';die(); 
			}else{
				echo 'error1';die();
			}	
		}else{
			echo 'error2';die();
		}
	/* Reverse Coupon Code */ 	
	}else{
		if(isset($_SESSION['oct_coupon_id']) && $_SESSION['oct_coupon_id']!=''){
			$oct_coupons->id = $_SESSION['oct_coupon_id'];
			$checkcouponinfos = $oct_coupons->readOne_by_coupon_id();	
			$coupon_id = $checkcouponinfos[0]->id;
			$coupon_used = $checkcouponinfos[0]->coupon_used;
			
			$oct_coupons->id = $coupon_id;
			$oct_coupons->coupon_used = $coupon_used-1;
			$checkcouponinfos = $oct_coupons->oct_update_coupon_used();
				
			$_SESSION['oct_nettotal'] = $_SESSION['oct_sub_total']+$_SESSION['oct_coupon_discount'];
			unset($_SESSION['oct_coupon_id']);
			unset($_SESSION['oct_coupon_code']);
			unset($_SESSION['oct_coupon_discount']);
		}
	}	
} 


/* register , login and booking complete code here START */
if(isset($_POST['action']) && $_POST['action']=='check_existing_username'){
	$email =$_POST['email'];
	$exists = email_exists( $email );
	if (!email_exists($email)) {
		echo "true";
	} else {
		echo "false";
	}
}
if(isset($_POST['action']) && $_POST['action']=='get_existing_user_data'){
	$loginemail = $_POST['uname'];
	$loginpass = $_POST['pwd'];
	
	$user = get_user_by( 'email', $loginemail );
	if ( $user && wp_check_password( $loginpass, $user->data->user_pass, $user->ID) ){
		$user_id = $user->data->ID;
		$user_login = $user->data->user_login;
		wp_set_current_user( $user_id, $user_login );
		wp_set_auth_cookie( $user_id );
		
		$current_user = wp_get_current_user();
		$current_user_name = $current_user->user_login;
		$current_user_email = $current_user->user_email;
		$firstname = $current_user->user_firstname;
		$user_pass = $current_user->user_pass;
		$lastname = $current_user->user_lastname;
		$current_user_id = $current_user->ID ;
		
		$_SESSION['client_oct_name'] = $current_user_name;
		$_SESSION['client_oct_email'] = $current_user_email;
		$_SESSION['client_first_name'] = $firstname;
		$_SESSION['client_last_name'] = $lastname;
		$_SESSION['client_oct_ID'] = $current_user_id;
		
		$current_user_meta = get_user_meta($current_user_id);
		
		$get_data_1  = array("gender"=>$current_user_meta['oct_client_gender'][0] ,"user_id"=>$current_user_id ,"user_email"=>$current_user_email,"password"=>$user_pass,"first_name"=>$firstname,"last_name"=>$lastname,"phone"=>$current_user_meta['oct_client_phone'][0],"address"=>$current_user_meta['oct_client_address'][0],"city"=>$current_user_meta['oct_client_city'][0],"state"=>$current_user_meta['oct_client_state'][0],"notes"=>$current_user_meta['oct_client_notes'][0],"ccode"=>$current_user_meta['oct_client_ccode'][0]);
		
		$get_data_2 = unserialize(unserialize($current_user_meta['oct_client_extra_details'][0]));
	
		$get_data = array_merge($get_data_1, unserialize($get_data_2));
		
		echo $get_userdetails = json_encode($get_data);
	}else{
	   echo "Invalid Username or Password";
	}
}
if(isset($_POST['action']) && $_POST['action']=='oct_logout_user'){
	wp_logout();
	unset($_SESSION['client_oct_name']);
	unset($_SESSION['client_oct_email']);
	unset($_SESSION['client_first_name']);
	unset($_SESSION['client_last_name']);
	unset($_SESSION['client_oct_ID']);
}
if(isset($_POST['action']) && $_POST['action']=='oct_booking_complete'){
	$preff_username = $_POST['username'];
	$preff_password = $_POST['pwd'];
	$first_name = $_POST['fname'];
	$last_name = $_POST['lname'];
	$user_phone = $_POST['phone'];
	$user_gender = $_POST['gender'];
	$user_address = $_POST['address'];
	$user_city = $_POST['city'];
	$user_state = $_POST['state'];
	$user_notes = $_POST['notes'];
	$user_ccode = $_POST['ccode'];
	$username = $_POST['fname'].rand(0,999);
	
	if(isset($_POST['dynamic_field_add'])){
		$extra_details = $_POST['dynamic_field_add'];
	}else{
		$extra_details = array();
	}
	$serialize_extra_details = serialize($extra_details);
	
	$stripe_trans_id = '';
	if($_POST['payment_method'] == 'stripe'){
		if (isset($_POST['st_token']) && $_POST['st_token']!='' && $_SESSION['oct_nettotal']!=0) {
			require_once($base.'/assets/stripe/Stripe.php');
			$partialdeposite_status = get_option('octabook_partial_deposit_status');
			if($partialdeposite_status=='E'){
				$stripe_amt = number_format($_SESSION['oct_partialdeposit'],2,".",',');
			}else{
				$stripe_amt = number_format($_SESSION['oct_nettotal'],2,".",',');
			}
			
			Stripe::setApiKey(get_option("octabook_stripe_secretKey"));
			$error = '';
			$success = '';
			try { 
				$striperesponse = Stripe_Charge::create(array("amount" => round($stripe_amt*100),
									"currency" => get_option('octabook_currency'),
									"card" => $_POST['st_token'],
									"description"=>$first_name.' , '.$preff_username
									));
				$stripe_trans_id = 	$striperesponse->id;
			}
			catch (Exception $e) {
				$error = $e->getMessage();
				echo $error;die;
			}
		}
	}
	
	if(isset($_SESSION['oct_coupon_discount']) && $_SESSION['oct_coupon_discount'] != 'undefined' && $_SESSION['oct_coupon_discount'] != '' ){
		$total_discount = @number_format($_SESSION['oct_coupon_discount'],2,".",',');
	}else{
		$total_discount = 0;
	}
	
	$oct_detail = array('preff_username' => $preff_username, 'preff_password' => $preff_password, 'first_name' => $first_name, 'last_name' => $last_name, 'user_phone' => $user_phone, 'user_gender' => $user_gender, 'user_address' => $user_address, 'user_city' => $user_city, 'user_state' => $user_state, 'user_notes' => $user_notes, 'user_ccode' => $user_ccode, 'serialize_extra_details' => $serialize_extra_details, 'oct_user_type' => $_POST['oct_user_type'], 'payment_method' => $_POST['payment_method'], 'username' => $username, 'discount' => @number_format($total_discount, 2, ".", ','));
	
	$_SESSION['oct_detail']=$oct_detail;
	
	/*paypal payment method*/
	if($_POST['payment_method'] == 'paypal'){
		header('location:'.$plugin_url.'/lib/paypal_payment_process.php');
		exit(0);
	}
	/*Stripe payment method*/
	if($_POST['payment_method'] == 'stripe'){
		$_SESSION['oct_detail']['stripe_trans_id'] = $stripe_trans_id;
		header('location:'.$plugin_url.'/lib/oct_front_booking_complete.php');
		exit(0);
	}
	/*Pay Locally payment method*/
	if($_POST['payment_method'] == 'pay_locally'){
		header('location:'.$plugin_url.'/lib/oct_front_booking_complete.php');
		exit(0);
	}
}
/* register , login and booking complete code here END */
/* Function of check available & Booked slot */
function get_provider_slot_count($staff_id,$service_id,$date_timestamp){
	$selecteddate = date_i18n('Y-m-d',$date_timestamp);
	$selectedstaffid = $staff_id;
	
	$service_id = $service_id;
	
	/* $oct_service->id = $service_id; */
	/* $oct_service->readOne(); */
	
	$service_duration = $oct_service->duration;
	
	$octstaff = new octabook_staff();
	$octstaff->id = $selectedstaffid;
	$provider_result = $octstaff->readOne();
	
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
	
	/* overlapping code here */
	$newtimeslot_array = array();
	if(isset($_SESSION['oct_cart_item'])){
		foreach($_SESSION['oct_cart_item'] as $oct_cart){
			$oct_cart_unserialize = unserialize($oct_cart);
			if($oct_cart_unserialize['selected_staff'] == $_POST['selstaffid']){
				$cart_id = $oct_cart_unserialize['id'];
				$start_time = $oct_cart_unserialize['selected_datetime'];
				$end_time = $oct_cart_unserialize['selected_enddatetime'];
				$newtimeslot_array[] = array('starttime'=>$start_time,'endtime'=>$end_time);
				if($booking_dayclosing == "D"){
					if ($service_duration > $time_interval) {
						$service_duration_check_minutes = $service_duration - $time_interval;
						$previous_start = strtotime("-$service_duration_check_minutes minutes", $start_time);
						$previous_end = $start_time;
						$newtimeslot_array[] = array('starttime'=>$previous_start,'endtime'=>$previous_end);
					}
				}
			}
		}
	}
	
	/* Get Google Calendar Bookings of Provider */
	$providerTwoSync = 'Y';
	$providerCalenderBooking = array();
	if($providerTwoSync=='Y'){
		$curlevents = curl_init();
		curl_setopt_array($curlevents, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $plugin_url.'/GoogleCalendar/event.php?cdate='.$start_date,
			CURLOPT_FRESH_CONNECT =>true,
			CURLOPT_USERAGENT => 'OctaBook'
		));
		$response = curl_exec($curlevents);    
		curl_close($curlevents);
		$provider_events = array();
		$provider_events = json_decode($response);

		$providerCalenderBooking = array();
		if(isset($provider_events)){
			foreach($provider_events as $providerevent){ 
				$startdate = date_i18n('Y-m-d', strtotime($providerevent->start)); 
				$starttime = date_i18n('H:i:s', strtotime($providerevent->start));
				$enddate = date_i18n('Y-m-d', strtotime($providerevent->end));
				$endtime = date_i18n('H:i:s', strtotime($providerevent->end));

				$GCslotstart = mktime(date_i18n('H',strtotime($starttime)),date_i18n('i',strtotime($starttime)),date_i18n('s',strtotime($starttime)),date_i18n('n',strtotime($startdate)),date_i18n('j',strtotime($startdate)),date_i18n('Y',strtotime($startdate))); 

				$GCslotend = mktime(date_i18n('H',strtotime($endtime)),date_i18n('i',strtotime($endtime)),date_i18n('s',strtotime($endtime)),date_i18n('n',strtotime($enddate)),date_i18n('j',strtotime($enddate)),date_i18n('Y',strtotime($enddate)));

				$providerCalenderBooking[] = array('start'=>$GCslotstart,'end'=>$GCslotend);
			}
		}
	}
	/*****************************************************************/

	$pass_array = array();
	if($time_schedule['off_day']!=true  && isset($time_schedule['slots']) && sizeof((array)$time_schedule['slots'])>0 && $allbreak_counter != sizeof((array)$time_schedule['slots'])){
		$pass_array["Count_Total_Slots"] = count($time_schedule['slots']);
		$Avail_slots = 0;
		foreach($time_schedule['slots']  as $slot) {
			/* echo $slot; */
			$curreslotstr = strtotime(date_i18n('Y-m-d H:i:s',strtotime($start_date.' '.$slot)));
			$gccheck = 'N';
			/*Checking in GC booked Slots */
			if(sizeof((array)$providerCalenderBooking)>0){
				for($i = 0; $i < sizeof((array)$providerCalenderBooking); $i++) {
					if($curreslotstr >= $providerCalenderBooking[$i]['start'] && $curreslotstr < $providerCalenderBooking[$i]['end']){
						$providerCalenderBooking[$i]['start'];
						$gccheck = 'Y';
					}
				}
			}
			
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
			
			$slots_timestamp = strtotime($selecteddate." ".$slot);
			
			$complete_time_slot = mktime(date('H',strtotime($slot)),date('i',strtotime($slot)),date('s',strtotime($slot)),date('n',strtotime($time_schedule['date'])),date('j',strtotime($time_schedule['date'])),date('Y',strtotime($time_schedule['date']))); 
			
			/*  check oveerlapping slots with cart */
			$session_check = 'N';
			/* Check Session Slots */
			if(sizeof((array)$newtimeslot_array)>0 && get_option('octabook_multiple_booking_sameslot')=='D'){
				foreach($newtimeslot_array as $session_slot){
				
					if ($slots_timestamp>=$session_slot['starttime'] && $slots_timestamp<$session_slot['endtime'] && get_option('octabook_hide_booked_slot')=='E'){
						$session_check = 'Y';
					}else if($slots_timestamp>=$session_slot['starttime'] && $slots_timestamp<$session_slot['endtime']){
						$session_check = 'Y';
					}					
				}
			}
			if($session_check=='Y'){
				$slot_counter++; 					continue;
			}
			
			/* Check for the multiple booking sameslot Enable */
			if(get_option('octabook_multiple_booking_sameslot') == "D"){	
				if(get_option('octabook_hide_booked_slot')=='E' && (in_array($complete_time_slot,$time_schedule['booked']) || $gccheck=='Y')) {
					continue;
				}
			}
			$timestamp = strtotime(date_i18n(get_option('date_format'),strtotime($selecteddate))." ".date_i18n(get_option('time_format'),strtotime($slot)));
			$date = date("Y-m-d H:i:s", $timestamp);
			$date2 = substr( $date, 0, -1 );
			global $wpdb;
			$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."oct_bookings 
			Where booking_datetime='".$date2."'");
			$counted = count((array)$result);
			if(isset($time_schedule['booked']) && (in_array($complete_time_slot,$time_schedule['booked']) || $gccheck=='Y') && (get_option('octabook_multiple_booking_sameslot')=='D') ) {
				}elseif(get_option('octabook_multiple_booking_sameslot')=='E' && $counted >= get_option('octabook_slot_max_booking_limit') && get_option('octabook_slot_max_booking_limit')>0){
				}
			else {
				$Avail_Slots++;
			} $slot_counter++; 
		}
		$pass_array["Avail_Slots"] = $Avail_Slots;
		} else {
			$pass_array = array("Count_Total_Slots" => 0,"Avail_Slots"=>0);
			}
	return json_encode($pass_array);
}