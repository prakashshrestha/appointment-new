<?php
session_start();
global $current_user;
$octabook_sampledata =get_option('octabook_sample_dataids');
$oct_currency_symbol = get_option('octabook_currency_symbol');
	$user_sp = '';
	$user_sp_manager = '';
	$current_user = wp_get_current_user();
	$info = get_userdata( $current_user->ID );

	if(current_user_can('oct_staff') && !current_user_can('manage_options')) {
	  $user_sp = 'Y';
	}if(current_user_can('oct_manager') && !current_user_can('manage_options')) {
	  $user_sp_manager = 'Y';
	}
	if ( class_exists( 'WooCommerce' ) && current_user_can('oct_staff') ) {
		$user_sp = 'Y';	
	}
	if ( class_exists( 'WooCommerce' ) && current_user_can('oct_manager') ) {
		$user_sp_manager = 'Y';				
	}
	$plugin_url_for_ajax = plugins_url('',  dirname((__FILE__)));
	if(current_user_can('manage_options')){ 		
		if(get_option('octabook_multi_location')=='E'){
			$location = new octabook_location();
			$location_sortby = get_option('octabook_location_sortby');
			$oct_locations = $location->readAll('','','');
			
			/* if(sizeof((array)$oct_locations)==0 && isset($_GET['page']) && $_GET['page']!='dashboard_submenu'){
				 	/* header('Location:'.site_url().'/wp-admin/admin.php?page=location_submenu');  
				  header('Location:'.site_url().'/wp-admin/admin.php?page=dashboard_submenu');
				}
			$temp_locatio_name = array();
			if((!isset($_SESSION['oct_location']) || $_SESSION['oct_location']==0) && $_GET['page']!='location_submenu'){$_SESSION['oct_location'] = $oct_locations[0]->id; 
			  header('Location:'.site_url().'/wp-admin/admin.php?page=location_submenu'); 
			/*  header('Location:'.site_url().'/wp-admin/admin.php?page=dashboard_submenu'); 
			}	 */				
				
		}else{					
			if(!isset($_SESSION['oct_location']) || $_SESSION['oct_location']!=0){ $_SESSION['oct_location'] = 0; header('Location:'.site_url().'/wp-admin/admin.php?page=services_submenu');}					
		}
		}else{
		 if(get_option('octabook_multi_location')=='E' && ($user_sp_manager=='Y' || $user_sp=='Y')){
				$currentuser_location = get_user_meta($current_user->ID,'staff_location');
				
				if(!isset($_SESSION['oct_location']) || ($_SESSION['oct_location']!=$currentuser_location[0])){
					if($user_sp=='Y'){$_SESSION['oct_booking_filterstaff']=$current_user->ID;}else{unset($_SESSION['oct_booking_filterstaff']);}
					$_SESSION['oct_location'] = $currentuser_location[0];
					header('Location:'.site_url().'/wp-admin/admin.php?page=appointments_submenu');
				}
			}else{
				if(!isset($_SESSION['oct_location'])){$_SESSION['oct_location']=0;}
			}		
		
		}		
				
/* Service Validation Messages */				
	$categorytitle_err_msg = __('Please enter category title','oct');			
	$servicetitle_err_msg = __('Please enter service title','oct');			
	$servicedescription_err_msg = __('Please enter service description','oct');			
	$serviceprice_err_msg = __('Please enter service price','oct');			
	$servicepricedigit_err_msg = __('Please enter price in digits','oct');		
	$serviceofferpricegreater_err_msg = __('Offered price should be less then default price','oct');	
	$servicecategory_err_msg = __('Please select service category','oct');		
	$servicehrsrange_err_msg = __('Please enter minimum 1 hours maximum 23 hours','oct');		
	$servicemins_err_msg = __('Please enter value minimum 5 minutes','oct');		
	$serviceminsrange_err_msg = __('Please enter minimum 5 mintues maximum 59 mintues','oct');	
	$servicenumpatt_err_msg = __('Please enter value in digits only','oct');	
	
	/* Service Addon Validation Messages */					
	$serviceaddontitle_err_msg = __('Please enter addon title','oct');				
	$serviceaddonmaxqty_err_msg = __('Please enter valid max addon quantity','oct');			
	$serviceaddon_price_err_msg = __('Please enter addon price','oct');	
	$serviceaddon_validprice_err_msg = __('Please enter valid addon price','oct');	
	$serviceaddon_qty_err_msg = __('Please enter addon pricing quantity','oct');	
	$serviceaddon_validqty_err_msg = __('Please enter valid addon pricing quantity','oct');	

/* Location Validation Messages */	
	$locationtiitle_err_msg = __('Please enter location title','oct');			
	$locationemail_err_msg = __('Please enter email','oct');			
	$locationinvalidemail_err_msg = __('Please enter valid email','oct');			
	$locationphone_err_msg = __('Please enter phone','oct');			
	$locationvalidphone_err_msg = __('Please enter valid phone number','oct');			
	$locationinvalidphone_err_msg = __('Please enter valid phone','oct');			
	$locationaddress_err_msg = __('Please enter  address','oct');			
	$locationcity_err_msg = __('Please enter city','oct');	
	$locationstate_err_msg = __('Please enter state','oct');	
	$locationzip_err_msg = __('Please enter zip/postal code','oct');	
	$locationcountry_err_msg = __('Please enter country','oct');	
	
/* Staff Validation Messages */	
	$staffusername_err_msg = __('Please enter username','oct');	
	$staffusernameexist_err_msg = __('Username exist or not valid','oct');	
	$staffpassword_err_msg = __('Please enter password','oct');	
	$staffemail_err_msg = __('Please enter email','oct');	
	$staffemailexist_err_msg = __('Email exist or not valid','oct');	
	$stafffullname_err_msg = __('Please enter fullname','oct');	
	$staffselect_err_msg = __('Please select existing user','oct');	
	$staffvalidphone_err_msg = __('Please enter valid phone number.','oct');	
/* Coupon Validation Messages */
	$cuponcode_err_msg = __('Please enter promocode','oct');	
	$cuponvalue_err_msg = __('Please enter promocode value','oct');	
	$cuponvalueinvalid_err_msg = __('Please enter valid promocode value','oct');	
	$cuponlimit_err_msg = __('Please enter promocode limit','oct');	
	$cuponlimitinvalid_err_msg = __('Please enter valid promocode limit','oct');	
/* SMS Notifications Validation Messages */
	$twilliosid_err_msg = __('Please enter twillio account SID','oct');	
	$twillioauthtoken_err_msg = __('Please enter twillio auth token','oct');	
	$twilliosendernum_err_msg = __('Please enter twillio sender number','oct');	
	$twillioadminnum_err_msg = __('Please enter twillio admin account number','oct');	
	
	$plivosid_err_msg = __('Please enter plivo account SID','oct');	
	$plivoauthtoken_err_msg = __('Please enter plivo auth token','oct');	
	$plivosendernum_err_msg = __('Please enter plivo sender number','oct');	
	$plivoadminnum_err_msg = __('Please enter plivo admin account number','oct');
	
	$nexmoapi_err_msg = __('Please enter nexmo API','oct');	
	$nexmoapisecert_err_msg = __('Please enter nexmo API Secert','oct');	
	$nexmofromnum_err_msg = __('Please enter nexmo from number','oct');	
	$nexmoadminnum_err_msg = __('Please enter nexmo admin account number','oct');
			
	
/* Object Content For Appointment Calender */
$language = get_locale();
$ak_wplang = explode('_',$language);
$wpTimeFormatorg = get_option('time_format'); 
$arr = str_split($wpTimeFormatorg);
$slashcounter = 0;
$wpTimeFormat='';
	foreach($arr as $singlechar){
		if($singlechar=='\\'){
			$slashcounter=1;
			$wpTimeFormat .="[";
			continue;
		}elseif($slashcounter!=1 && ($singlechar=='g' || $singlechar=='G' || $singlechar=='i')){
			if($singlechar=='g'){ $wpTimeFormat .='h'; }
			if($singlechar=='G'){ $wpTimeFormat .='H'; }
			if($singlechar=='i'){ $wpTimeFormat .='mm'; }
		}elseif($slashcounter==1){
			$wpTimeFormat .=$singlechar."]";
			$slashcounter=0;
		}else{
			$wpTimeFormat .=$singlechar;  
		}
   } 	
	
/* Existing Custom Form Fields */ 
$octabook_custom_formfields = json_decode(stripslashes(get_option('octabook_custom_form')),true); 
$octabook_custom_formfields_val = '';
$totallength = sizeof((array)$octabook_custom_formfields);
if($totallength>0){
	$lengthcounter = 1;
	foreach($octabook_custom_formfields as $octabook_custom_formfield){
		if($totallength==$lengthcounter){
			$octabook_custom_formfields_val .= json_encode($octabook_custom_formfield);
		}else{
			$octabook_custom_formfields_val .= json_encode($octabook_custom_formfield).',';
		}
		$lengthcounter++;
	} 
}	
	
function wp_date_format() {
	$dateFormat = get_option('date_format');
	
	$chars = array(
		// Day
		'd' => 'DD',
		'j' => 'DD',
		// Month
		'm' => 'MM',
		'F' => 'MMMM',
		// Year
		'Y' => 'YYYY',
		'y' => 'YYYY',
	);
	return strtr( (string) $dateFormat, $chars );
}

function wp_time_format() {
	$timeFormat = get_option('time_format');
	
	$chars = array(
		// Day
		'g' => 'hh',
		'H' => 'h',
		// Month
		'a' => 'a',
		'A' => 'A',
		// Year
		'i' => 'mm',
	);
	return strtr( (string) $timeFormat, $chars );
}

$Today = __('Today',"oct");
$Yesterday = __('Yesterday',"oct");
$Last_7_Days = __('Last 7 Days',"oct");
$Last_30_Days = __('Last 30 Days',"oct");
$This_Month = __('This Month',"oct");
$Last_Month = __('Last Month',"oct");

$Apply = __('Apply',"oct");
$Cancel = __('Cancel',"oct");
$From = __('From',"oct");
$To = __('To',"oct");
$Custom_range = __('Custom Range',"oct");

$dateformatsss= wp_date_format();
$timeformatsss= wp_time_format();
$date_time_format = wp_date_format()." ".wp_time_format();	
?>	

<script>
var date_format_for_js = '<?php  echo $dateformatsss; ?>';
var time_format_for_js = '<?php  echo $timeformatsss; ?>';
var date_time_format_for_js = '<?php  echo $date_time_format; ?>';

var labels_for_daterange_picker = {
        'Today': '<?php echo $Today; ?>',
        'Yesterday': '<?php echo $Yesterday; ?>',
        'Last_7_Days': '<?php echo $Last_7_Days; ?>',
        'Last_30_Days': '<?php echo $Last_30_Days; ?>',
        'This_Month': '<?php echo $This_Month; ?>',
        'Last_Month': '<?php echo $Last_Month; ?>',
        'applyLabel': '<?php echo $Apply; ?>',
        'cancelLabel': '<?php echo $Cancel; ?>',
        'fromLabel': '<?php echo $From; ?>',
        'toLabel': '<?php echo $To; ?>',
        'customRangeLabel': '<?php echo $Custom_range; ?>'
    };

var header_object ={'plugin_path':'<?php echo $plugin_url_for_ajax; ?>','site_url':'<?php echo site_url();?>','defaultmedia':'<?php echo $plugin_url_for_ajax.'/assets/images/';?>','ak_wp_lang':'<?php echo $ak_wplang[0]; ?>','cal_first_day':'<?php echo get_option('start_of_week'); ?>','time_format':'<?php echo $wpTimeFormat; ?>','mb_status':'<?php echo get_option('octabook_guest_user_checkout'); ?>','multilocation_st':'<?php echo get_option('octabook_multi_location');?>','reviews_st':'<?php echo get_option('octabook_reviews_status');?>','full_cal_defaultdate':'<?php echo date_i18n('Y-m-d');?>','octabook_plivo_ccode_alph':'<?php echo get_option('octabook_plivo_ccode_alph');?>','octabook_twilio_ccode_alph':'<?php echo get_option('octabook_twilio_ccode_alph');?>','octabook_nexmo_ccode_alph':'<?php echo get_option('octabook_nexmo_ccode_alph');?>','octabook_textlocal_ccode_alph':'<?php echo get_option('octabook_textlocal_ccode_alph');?>','octabook_custom_formfields_val':'<?php echo $octabook_custom_formfields_val;?>'};

var admin_validation_err_msg = {'categorytitle_err_msg':'<?php echo $categorytitle_err_msg;?>',
								'servicetitle_err_msg':'<?php echo $servicetitle_err_msg;?>',
								'servicedescription_err_msg':'<?php echo $servicedescription_err_msg;?>',
								'serviceprice_err_msg':'<?php echo $serviceprice_err_msg;?>',
								'servicepricedigit_err_msg':'<?php echo $servicepricedigit_err_msg;?>',
								'serviceofferpricegreater_err_msg':'<?php echo $serviceofferpricegreater_err_msg;?>',
								'servicecategory_err_msg':'<?php echo $servicecategory_err_msg;?>',
								'servicehrsrange_err_msg':'<?php echo $servicehrsrange_err_msg;?>',
								'serviceminsrange_err_msg':'<?php echo $serviceminsrange_err_msg;?>',
								'servicemins_err_msg':'<?php echo $servicemins_err_msg;?>',
								'servicenumpatt_err_msg':'<?php echo $servicenumpatt_err_msg;?>',
								'serviceaddontitle_err_msg':'<?php echo $serviceaddontitle_err_msg;?>',
								'serviceaddonmaxqty_err_msg':'<?php echo $serviceaddonmaxqty_err_msg;?>',
								'serviceaddon_price_err_msg':'<?php echo $serviceaddon_price_err_msg;?>',
								'serviceaddon_validprice_err_msg':'<?php echo $serviceaddon_validprice_err_msg;?>',
								'serviceaddon_qty_err_msg':'<?php echo $serviceaddon_qty_err_msg;?>',
								'serviceaddon_validqty_err_msg':'<?php echo $serviceaddon_validqty_err_msg;?>',								
								
								'locationtiitle_err_msg':'<?php echo $locationtiitle_err_msg;?>',
								'locationemail_err_msg':'<?php echo	$locationemail_err_msg;?>',
								'locationinvalidemail_err_msg':'<?php echo $locationinvalidemail_err_msg;?>', 	
								'locationphone_err_msg':'<?php echo	$locationphone_err_msg;?>',	
								'locationvalidphone_err_msg':'<?php echo	$locationvalidphone_err_msg;?>',	
								'locationinvalidphone_err_msg':'<?php echo $locationinvalidphone_err_msg;?>',		
								'locationaddress_err_msg':'<?php echo $locationaddress_err_msg;?>', 	
								'locationcity_err_msg':'<?php echo $locationcity_err_msg;?>', 
								'locationstate_err_msg':'<?php echo $locationstate_err_msg;?>',
								'locationzip_err_msg':'<?php echo $locationzip_err_msg;?>',
								'locationcountry_err_msg':'<?php echo $locationcountry_err_msg;?>',
								
								'staffusername_err_msg':'<?php echo $staffusername_err_msg;?>',
								'staffusernameexist_err_msg':'<?php echo $staffusernameexist_err_msg;?>',
								'staffpassword_err_msg':'<?php echo $staffpassword_err_msg;?>',
								'staffemail_err_msg':'<?php echo $staffemail_err_msg;?>',
								'staffemailexist_err_msg':'<?php echo $staffemailexist_err_msg;?>',
								'stafffullname_err_msg':'<?php echo $stafffullname_err_msg;?>',
								'staffselect_err_msg':'<?php echo $staffselect_err_msg; ?>',
								'staffvalidphone_err_msg':'<?php echo $staffvalidphone_err_msg; ?>',
								
								'cuponcode_err_msg':'<?php echo $cuponcode_err_msg; ?>',
								'cuponvalue_err_msg':'<?php echo $cuponvalue_err_msg; ?>',
								'cuponvalueinvalid_err_msg':'<?php echo $cuponvalueinvalid_err_msg; ?>',
								'cuponlimit_err_msg':'<?php echo $cuponlimit_err_msg; ?>',
								'cuponlimitinvalid_err_msg':'<?php echo $cuponlimitinvalid_err_msg; ?>',
								
								'twilliosid_err_msg':'<?php echo $twilliosid_err_msg; ?>',
								'twillioauthtoken_err_msg':'<?php echo $twillioauthtoken_err_msg; ?>',
								'twilliosendernum_err_msg':'<?php echo $twilliosendernum_err_msg; ?>',
								'twillioadminnum_err_msg':'<?php echo $twillioadminnum_err_msg; ?>',
								
								'plivosid_err_msg':'<?php echo $plivosid_err_msg; ?>',
								'plivoauthtoken_err_msg':'<?php echo $plivoauthtoken_err_msg; ?>',
								'plivosendernum_err_msg':'<?php echo $plivosendernum_err_msg; ?>',
								'plivoadminnum_err_msg':'<?php echo $plivoadminnum_err_msg; ?>',
								
								'nexmoapi_err_msg':'<?php echo $nexmoapi_err_msg; ?>',
								'nexmoapisecert_err_msg':'<?php echo $nexmoapisecert_err_msg; ?>',
								'nexmofromnum_err_msg':'<?php echo $nexmofromnum_err_msg; ?>',
								'nexmoadminnum_err_msg':'<?php echo $nexmoadminnum_err_msg; ?>',
								
								'Pending':'<?php echo __("Pending","oct");?>',
								'Confirmed':'<?php echo __("Confirmed","oct");?>',
								'Rejected':'<?php echo __("Rejected","oct");?>',
								'Rescheduled':'<?php echo __("Rescheduled","oct");?>',
								'Cancel_By_Client':'<?php echo __("Cancelled By Client","oct");?>',
								'Cancelled_by_Service_Provider':'<?php echo __("Cancelled by Service Provider","oct");?>',
								'Completed':'<?php echo __("Appointment Completed","oct");?>',
								'Appointment_Marked_as_no_show':'<?php echo __("Mark As No Show","oct");?>',
								
			
}
var appearance_setting = {"default_country_code":"<?php echo get_option('octabook_default_country_short_code'); ?>"};	
/* var formoptions = { "formhtml": }; */
</script>	
	
<?php echo '<style>
#oct #oct-top-nav .navbar .nav > li > a{
	color: '.get_option('octabook_admin_color_text').' !important;
}
/* Primary Color */
#oct #oct-main-navigation{
	background: '.get_option('octabook_admin_color_primary').' !important;
}
#oct .loader .oct-second{
	border: 3px solid '.get_option('octabook_admin_color_primary').' !important;
		border-bottom-color: transparent !important;
}
#oct #oct-notifications .get_notification_rem{
	border-color: '.get_option('octabook_admin_color_secondary').';
}
#oct .oct-notification-main .notification-header #oct-close-notifications:hover{
	background-color: '.get_option('octabook_admin_color_primary').' !important;
}
#oct .tooltip-arrow{
	border-right-color: '.get_option('octabook_admin_color_primary').' !important;
}

/* calendar page */
#oct .fc-toolbar {
	border-top: 1px solid '.get_option('octabook_admin_color_primary').' !important;
	border-left: 1px solid '.get_option('octabook_admin_color_primary').' !important;
	border-right: 1px solid '.get_option('octabook_admin_color_primary').' !important;
}
#oct .fc-toolbar {
	background-color: '.get_option('octabook_admin_color_primary').' !important;
}
#oct #oct-dashboard .oct-dash-icon.today{
	background-color: '.get_option('octabook_admin_color_primary').' !important;
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}
#oct .oct-notification-main .notification-header a.oct-clear-all{
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}
/* Secondary color */
#oct #oct-top-nav .navbar .nav > li > a:hover,
#oct #oct-top-nav .navbar .nav > .active > a,
#oct #oct-top-nav .navbar .nav > .active > a:focus{
	background: '.get_option('octabook_admin_color_secondary').' !important;
}

#oct a#oct-notifications i.icon-bell.oct-pulse.oct-new-booking,
#oct a#oct-notifications i.icon-bell{
	color: '.get_option('octabook_admin_color_secondary').' !important;
}
#oct .loader .oct-third{
	border: 3px solid '.get_option('octabook_admin_color_secondary').' !important;
		border-top-color: transparent !important; 
}
#oct  #oct-main-navigation .navbar .nav.oct-nav-tab li:before,
#oct  #oct-main-navigation .navbar .nav.oct-nav-tab li:after{
	border-left-color: '.get_option('octabook_admin_color_secondary').' !important;
	border-right-color: '.get_option('octabook_admin_color_secondary').' !important;
}	

#oct a#oct-notifications:hover i.fa-angle-down,
#oct .fc button:hover,
#oct button.fc-today-button:hover{
	color: '.get_option('octabook_admin_color_secondary').' !important;
}


/* admin color bg text  and  Secondary color */
#oct #oct-dashboard .oct-dash-icon.this-year,
#oct .oct-notification-main .notification-header{
	background-color: '.get_option('octabook_admin_color_secondary').' !important;
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}

#oct #oct-top-nav .navbar .nav > .active > a,
#oct #oct-top-nav .navbar .nav > .active > a:focus{
	background-color: '.get_option('octabook_admin_color_secondary').' !important;
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}
#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a:focus{
	background: '.get_option('octabook_admin_color_secondary').' !important;
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}

#oct #oct-dashboard .oct-top-menus-stats.nav .bg-radius,
#oct .tooltip .tooltiptext{
	background-color: '.get_option('octabook_admin_color_secondary').' !important;
}
#oct #oct-dashboard #oct-today-stats li.active a,
#oct #oct-dashboard .oct-today-bookings .oct-no-today-booking-message .btn-active{
	background: '.get_option('octabook_admin_color_secondary').' !important;
}
/* admin color bg text */
#oct #oct-main-navigation .navbar .nav > li > a,
#oct .noti_color,
#oct a#oct-notifications i.fa-angle-down,
#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover i.icon-bell.oct-pulse.oct-new-booking,
#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li span.oct-map-location,
#oct #oct-main-navigation .navbar .nav.oct-nav-tab li.right-location button.btn{
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}

#oct .fc button,
#oct .oct-notification-main .notification-header #oct-close-notifications{
	color: '.get_option('octabook_admin_color_bg_text').' !important;
}

#oct .loader .oct-first{
	border: 3px solid '.get_option('octabook_admin_color_bg_text').' !important;
		border-right-color: transparent !important;
}


/* Desktops and laptops ----------- */
@media only screen  and (min-width : 768px) and (max-width : 1250px) {
	#oct #oct-main-navigation .navbar{
		background-color: '.get_option('octabook_admin_color_primary').' !important;
	}
	
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
		background-color: unset !important;
	}
		
}


/* iPads (portrait and landscape) ----------- */
@media only screen and (min-width : 768px) and (max-width : 1024px) {
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
	}
	
}
/* iPads (landscape) ----------- */
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (orientation : landscape) {
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		background-color: '.get_option('octabook_admin_color_secondary').' ;
		color: '.get_option('octabook_admin_color_text').' !important;
	}

}
/* iPads (portrait) ----------- */
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (orientation : portrait) {
	#oct #oct-top-nav .navbar-header,
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus,
	#oct #oct-top-nav .navbar-nav > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
	}
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus{
		background: unset !important;
	}
}	
/********** iPad 3 **********/
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (orientation : landscape) and (-webkit-min-device-pixel-ratio : 2) {
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		background-color: '.get_option('octabook_admin_color_secondary').' ;
		color: '.get_option('octabook_admin_color_text').' !important;
	}
}
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (orientation : portrait) and (-webkit-min-device-pixel-ratio : 2) {	
	#oct #oct-top-nav .navbar-header,
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus,
	#oct #oct-top-nav .navbar-nav > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
	}
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus{
		background: unset !important;
	}
}
/* Smartphones (landscape) ----------- */
@media only screen and (max-width: 767px) {
	#oct #oct-main-navigation .navbar{
		background-color: '.get_option('octabook_admin_color_primary').' !important;
	}
	
	#oct #oct-top-nav .navbar-header,
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus,
	#oct #oct-top-nav .navbar-nav > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
		background-color: unset !important;
	}
	/*
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus{
		background: unset !important;
	}
	*/
	
}	
/* Smartphones (portrait and landscape) ----------- */
@media only screen and (min-width : 320px) and (max-width : 480px) {
	
	#oct #oct-top-nav .navbar-header,
	#oct #oct-main-navigation .navbar-header,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus,
	#oct #oct-top-nav .navbar-nav > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > li > a:hover,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > li > a:hover {
		color: '.get_option('octabook_admin_color_secondary').' !important;
		background-color: unset !important;
	}
	/*
	#oct #oct-main-navigation .navbar .nav.oct-nav-tab > .active > a,
	#oct #oct-main-navigation .navbar .nav.user-nav-bar > .active > a,
	#oct #oct-top-nav .navbar .nav > .active > a:focus{
		background: unset !important;
	}
	*/
}
</style>' ;

if(is_rtl()){
	echo "<script type='text/javascript'>
	jQuery(document).ready(function(){
		jQuery('#oct').addClass('octdbrtl');
	});
	
	</script>";	
}




 
?>

<!-- main wrapper -->
<div class="oct-wrapper" id="oct">
<!-- all alerts, success messages -->
<div class="oct-alert-msg-show-main mainheader_message">		
	<div class="oct-all-alert-messags alert alert-success mainheader_message_inner">
		<a href="#" class="close" data-dismiss="alert">&times;</a>
		<strong><?php echo __("Success!","oct");?></strong> <span id="oct_sucess_message"><?php echo __("Updated successfully","oct");?></span>
	</div>
</div>	
<div class="oct-alert-msg-show-main mainheader_message_fail">		
	<div class="oct-all-alert-messags alert alert-danger mainheader_message_inner_fail">
		<a href="#" class="close" data-dismiss="alert">&times;</a>
		<strong><?php echo __("Failed!","oct");?></strong> <span id="oct_sucess_message_fail"><?php echo __("Updated successfully","oct");?></span>
	</div>
</div>	
<!-- loader -->
<div class="oct-loading-main" >
	<div class="loader">
		<span class="oct-first"></span>
		<span class="oct-second"></span>
		<span class="oct-third"></span>
	</div>
</div>

	<header class="oct-header">
	<?php if(!isset($current_user->caps['oct_client']) || isset($current_user->caps['administrator'])){ ?>
		<div id="oct-top-nav" class="navbar-inner">
            <nav role="navigation" class="navbar">
                <!-- Brand and toggle get grouped for better mobile display -->
				<div class="containerd">
                <div class="navbar-header">
                    <button type="button" data-target="#navbarCollapsetop" data-toggle="collapse" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <i class="fa fa-cog"></i>
                    </button>
                    <a href="<?php echo site_url(); ?>" class="navbar-brand"><img src="<?php echo $plugin_url_for_ajax; ?>/assets/images/OB-logo.png" /></a>
                </div>
                <!-- Collection of nav links and other content for toggling -->
                <div id="navbarCollapsetop" class="collapse navbar-collapse">
				
					<!--<ul class="nav navbar-nav">
						<li><a href="?page=frontend_shortcode_submenu" class="btn btn-link btn-no-bg">How to use shortcode?</a></li>
					</ul> 
					<ul class="nav navbar-nav">
						<li><a href="?page=whats_new_submenu" class="btn btn-link btn-no-bg">Version <?php //echo get_option('octabook_version');?></a></li>
					</ul>-->
					
                   
                </div>
				</div>
            </nav>
        </div><!-- top bar end here -->		


		<!-- recent notifications listing -->
		<div class="oct-overlay-notification"></div>
		<div id="oct-notification-container">
			<div class="oct-notifications-inner">
				<div class="oct-notification-main">
					<div class="oct-notification-main">
						<h4 class="notification-header"><a data-booking_id="All" href="javascript:void(0)" class="btn btn-link pull-left oct-clear-all oct_unread_notification"><?php echo __("Clear All","oct");?></a><?php echo __("Booking notifications","oct");?>
						<a id="oct-close-notifications" class="pull-right" href="javascript:void(0);" title="<?php echo __("Close Notifications","oct");?>"><i>×</i></a></h4>
						<div class="oct-recent-booking-container">
							<ul class="oct-recent-booking-list ">
								<div class="oct-load-bar">
									  <div class="oct-bar"></div>
									  <div class="oct-bar"></div>
									  <div class="oct-bar"></div>
								</div>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end recent notifications -->
		
		<div id="oct-main-navigation" class="navbar-inner">
			<nav role="navigation" class="navbar">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
						<span class="sr-only"><?php echo __("Toggle navigation","oct");?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<!-- <a href="oct-admin1.html" class="navbar-brand"><img src="images/logo-octabook.png" /></a> -->
				</div>
				<!-- Collection of nav links and other content for toggling -->
				<div id="navbarCollapse" class="collapse navbar-collapse np">
		<?php if($user_sp=='Y' && $user_sp_manager=='') {	?>
		<ul class="nav navbar-nav oct-nav-tab pl-20">
		<?php if(is_rtl()){  ?>

			<li class="<?php if($_GET['page']=='appointments_submenu' || $_GET['page']=='octabook_menu'){ echo 'active'; } ?>"><a href="?page=appointments_submenu"><i class="far fa-calendar-alt icons"></i><span> <?php echo __('Appointments',"oct"); ?></span></a></li>
			
			<li class="<?php if($_GET['page']=='provider_submenu'){ echo 'active'; } ?>"><a href="?page=sp_settings_submenu"><i class="fas fa-cogs icons"></i><span> <?php echo __('Settings',"oct"); ?></span></a></li> 
		<?php }else{ ?>
			<li class="<?php if($_GET['page']=='provider_submenu'){ echo 'active'; } ?>"><a href="?page=provider_submenu"><i class="fas fa-cogs icons"></i><span> <?php echo __('Settings',"oct"); ?></span></a></li>
			
			<li class="<?php if($_GET['page']=='appointments_submenu' || $_GET['page']=='octabook_menu'){ echo 'active'; } ?>"><a href="?page=appointments_submenu"><i class="far fa-calendar-alt icons"></i> <span><?php echo __('Appointments',"oct"); ?></span></a></li>
			
			<!--Code By Ajay-->
			<li class="<?php if($_GET['page']=='google_calander_submenu' || $_GET['page']=='octabook_menu'){ echo 'active'; }?>"><a href="?page=google_calander_submenu"><i class="far fa-calendar-check icons"></i><span><?php echo __('Google Calender',"oct"); ?></span></a></li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<ul class="nav navbar-nav oct-nav-tab w-100"> 
		<?php if(is_rtl()){ 
		global $wpdb;		
		if(current_user_can('manage_options')){			
			$query="select count(id) as count_ser from ".$wpdb->prefix."oct_services";
			$res_service_count = $wpdb->get_var($query);
			if(isset($octabook_sampledata) && $octabook_sampledata!=''){?>
				<li ><a id="octabook_sampledata" data-method="Remove" href="javascript:void(0)"><i class="fa fa-remove"></i><span><?php echo __('Remove Sample Data',"oct"); ?></span></a></li>
			<?php }else{
				if($res_service_count == 0){
				?>
				<li ><a id="octabook_sampledata" data-method="Add"  href="javascript:void(0)"><i class="fa fa-download"></i><span> <?php echo __('Sample Data',"oct"); ?></span></a></li>		
				<?php 
				}
			}
		}	?>
		
		<li class="<?php if($_GET['page']=='export_submenu'){ echo 'active'; } ?>"><a href="?page=export_submenu"><i class="fa fa-file-pdf-o"></i><span><?php echo __('Export',"oct"); ?></span></a></li>
		<?php if(current_user_can('manage_options')){ ?>
		<li class="<?php if($_GET['page']=='settings_submenu'){ echo 'active'; } ?>"><a href="?page=settings_submenu"><i class="fa fa-cog"></i><span><?php echo __('Settings',"oct"); ?></span></a></li>
		<?php }?>
		<?php if(get_option('octabook_reviews_status')=='E'){?>
		<li class="<?php if($_GET['page']=='reviews_submenu'){ echo 'active'; } ?>"><a href="?page=reviews_submenu"><i class="fa fa-star"></i><span> <?php echo __('Reviews',"oct"); ?></span></a></li><?php } ?>
		
		<li class="<?php  if($_GET['page']=='payments_submenu'){ echo 'active'; } ?>"><a href="?page=payments_submenu"><i class="fa fa-money"></i><span> <?php echo __('Payments',"oct"); ?></span></a></li>
		
		<li class="<?php if($_GET['page']=='clients_submenu' || $_GET['page']=='guest_clients_submenu'){ echo 'active'; } ?>"><a href="?page=clients_submenu"><i class="fa fa-user-o"></i><span> <?php echo __('Customers',"oct"); ?></span></a></li>
		
		<li class="<?php if($_GET['page']=='provider_submenu'){ echo 'active'; } ?>"><a href="?page=provider_submenu"><i class="icon-user icons"></i><span> <?php echo __('Staff',"oct"); ?></span></a></li>
		
		<li class="<?php if($_GET['page']=='services_submenu' || $_GET['page']=='service_addons'){ echo 'active'; } ?>"><a href="?page=services_submenu"><i class="fa fa-tasks"></i><span> <?php echo __('Services',"oct"); ?></span></a></li>
		
		
		<?php if(get_option('octabook_multi_location')=='E' && current_user_can('manage_options')){ ?>
		<li class="<?php if($_GET['page']=='location_submenu'){ echo 'active'; } ?>"><a href="?page=location_submenu"><i class="icon-location-pin icons"></i><span> <?php echo __('Locations',"oct"); ?> </span></a></li>
		<?php } ?>
		
		<li class="<?php if($_GET['page']=='appointments_submenu'){ echo 'active'; } ?>"><a href="?page=appointments_submenu"><i class="fa fa-calendar"></i><span> <?php echo __('Appointments',"oct"); ?></span></a></li>
		
		<li class="<?php if($_GET['page']=='dashboard_submenu'){ echo 'active'; } ?>"><a href="?page=dashboard_submenu"><i class="icon-speedometer icons"></i><span> <?php echo __('Dashboard',"oct"); ?></span></a></li>
	

		<?php }else{ ?>
		 
		<li class="<?php if($_GET['page']=='dashboard_submenu'){ echo 'active'; } ?>"><a href="?page=dashboard_submenu" class="bg-none"  title=""> <i class="fas fa-tachometer-alt icons"></i><span> <?php echo __('Dashboard',"oct"); ?></span> </a></li>
		
		<li class="<?php if($_GET['page']=='appointments_submenu'){ echo 'active'; } ?>"><a href="?page=appointments_submenu"><i class="far fa-calendar-alt icons"></i> <span> <?php echo __('Appointments',"oct"); ?></span></a></li>
		
		<?php if(get_option('octabook_multi_location')=='E' && current_user_can('manage_options')){ ?>
		<li class="<?php if($_GET['page']=='location_submenu'){ echo 'active'; } ?>"><a href="?page=location_submenu"><i class="fas fa-map-marker-alt icons"></i><span> <?php echo __('Locations',"oct"); ?> </span></a></li><?php } ?>
		
		<li class="<?php if($_GET['page']=='services_submenu' || $_GET['page']=='service_addons'){ echo 'active'; } ?>"><a href="?page=services_submenu"><i class="fas fa-tasks icons"></i><span> <?php echo __('Services',"oct"); ?></span></a></li>
		
		
		<li class="<?php if($_GET['page']=='provider_submenu'){ echo 'active'; } ?>"><a href="?page=provider_submenu"><i class="fas fa-user-tie icons"></i><span> <?php echo __('Staff',"oct"); ?></span></a></li>
		
		
		<li class="<?php if($_GET['page']=='clients_submenu' ){ echo 'active'; } ?>"><a href="?page=clients_submenu"><i class="fas fa-user icons"></i></i><span> <?php echo __('Customers',"oct"); ?></span></a></li>
		
		<li class="<?php  if($_GET['page']=='payments_submenu'){ echo 'active'; } ?>"><a href="?page=payments_submenu"><i class="fas fa-money-bill-wave icons" style="width: 24px;"></i><span> <?php echo __('Payments',"oct"); ?></span></a></li>
		
		<?php if(get_option('octabook_reviews_status')=='E'){?>
		<li class="<?php if($_GET['page']=='reviews_submenu'){ echo 'active'; } ?>"><a href="?page=reviews_submenu"><i class="fa fa-star icons"></i><span> <?php echo __('Reviews',"oct"); ?></span></a></li><?php } ?>

		<li class="<?php if($_GET['page']=='export_submenu'){ echo 'active'; } ?>"><a href="?page=export_submenu" class="bg-none" title=""> <i class="fas fa-file-pdf icons"></i><span> <?php echo __('Export',"oct"); ?></span> </a></li>


		<?php if(current_user_can('manage_options')){ ?>
		
		<li class="<?php if($_GET['page']=='settings_submenu'){ echo 'active'; } ?>"><a href="?page=settings_submenu" class="bg-none" title=""> <i class="fas fa-cog icons"></i><span> <?php echo __('Settings',"oct"); ?></span> </a></li>
		
		<?php } ?>


		<?php 

		} ?>
	
<?php 
			global $wpdb;
			if(current_user_can('manage_options')){				
				$query="select count(id) as count_ser from ".$wpdb->prefix."oct_services";
				$res_service_count = $wpdb->get_var($query);
			
				if(isset($octabook_sampledata) && $octabook_sampledata!=''){
				?>
				<li ><a class="cd-popup-trigger" href="javascript:void(0)"><i class="fas fa-times icons"></i><span> <?php echo __('Remove Sample Data',"oct"); ?></span></a></li>
				<?php }else{
					if($res_service_count == 0){
					?>
					<li ><a id="octabook_sampledata" data-method="Add"  href="javascript:void(0)"><i class="fa fa-download icons"></i><span> <?php echo __('Sample Data',"oct"); ?></span></a></li>		
					<?php 
					}
				}
			}
		 ?>

<?php if($user_sp_manager=='Y' || current_user_can('manage_options')){?>
				<?php
							$booking = new octabook_booking();
							?>
							<li class="right-location"><a id="oct-notifications" class="btn btn-link btn-no-bg" href="javascript:void(0);">
								<i class="icon-bell"></i>
								<div class="oct-bell-notification">Notification</div>
								<span class="total_notification noti_color oct_notification_count" id="oct-notification-top"></span>
								<!-- <i class="fa fa-angle-down not-arrow"></i> --></a>
							</li>				
	 <?php } ?>

	 	<?php if($user_sp_manager=='Y' || current_user_can('manage_options')){?>
						
							<?php if(get_option('octabook_multi_location')=='E' && current_user_can('manage_options')){ ?>
							<li class="location-select right-location">
								<a href="" class="oct-map-select"><i class="fas fa-map-marker-alt icons"></i><span class="oct-map-location">
								<select name="oct_selected_location" class="selectpicker oct_selected_location" data-size="10" style="display: none;">
								<?php foreach($oct_locations as $oct_location){ ?>
										<option value="<?php echo $oct_location->id; ?>" <?php if($oct_location->id==$_SESSION['oct_location']){ echo "selected";} ?>><?php echo $oct_location->location_title; ?></option>
								<?php  }  ?>
								</select></span></a>
							</li><?php } }?>

							
	</ul>
		<?php } ?>
		
				</div>		
			</nav>
		</div> <!-- top bar end here -->			
		<?php } ?>
		<!-- Alert Box For Remove Sample Data -->
		<div class="cd-popup" role="alert" style="z-index: 999;">
			<div class="cd-popup-container">
				<p><?php echo __("Are you sure you want to delete Sample data, It will remove all data related to sample data?","oct");?></p>
				<ul class="cd-buttons">
					<li><a id="octabook_sampledata" data-method="Remove" href="#0"><?php echo __("Yes","oct");?></a></li>
					<li><a class="remove_popup_sample_data" href="#0"><?php echo __("No","oct");?></a></li>
				</ul>
				<a href="#0" class="cd-popup-close img-replace"></a>
			</div> <!-- cd-popup-container -->
		</div> <!-- cd-popup -->
		
		<!-- Alert Box For Remove Sample Data -->
				<!-- show pop details click on appointment from listing -->
		<div id="booking-details" class="modal fade booking-details-calendar" tabindex="-1" role="dialog" aria-hidden="true"> <!-- modal pop up start -->
		<div class="vertical-alignment-helper">
			<div class="modal-dialog modal-md vertical-align-center">
				<div class="modal-content">
					<div class="modal-header">
					
						<button type="button" class="close close_booking_detail_modal" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" style="margin-top: 10px; margin-bottom: 0;"><?php echo __("Booking Details","oct");?> </h4>
						<ul class="oct-booking-date-time">
							<li class="oct-second-child oct_booking_datetime"></li>
						</ul>
						
					</div>
					<div class="modal-body">
						<ul class="list-unstyled oct-cal-booking-details">
							<li>
								<label><?php echo __("Booking Status","oct");?></label>
								<div class="oct-booking-status"><span class="badge animated pulse span-scroll" style="background-color: #31bf57;"></span></div>
							</li>
							
							<li>
								<label><?php echo __("Service","oct");?></label>
								<span class="oct_servicetitle span-scroll span_indent"> </span>
							</li>
							<li>
								<label><?php echo __("Provider","oct");?></label>
								<span class="calendar_providername span-scroll span_indent"></span>
							</li>
							<li>
								<label><?php echo __("Price","oct");?></label>
								<span class="span-scroll span_indent"><span class=""><?php echo $oct_currency_symbol;?></span><span class="price"> </span></span>
							</li>
							
							<li><h5 class="oct-customer-details-hr" style="font-weight: 600;"><?php echo __("Customer","oct");?></h5>
							</li>
							<li>
								<label><?php echo __("Name","oct");?></label>
								<span class="client_name span-scroll span_indent"></span>
							</li>
							<li>
								<label><?php echo __("Email","oct");?></label>
								<span class="client_email span-scroll span_indent"></span>
							</li>
							<li style="margin-bottom:15px;">
								<label><?php echo __("Full Address","oct");?></label>
								<a class="client_fulladdress" href="" target="_blank"><span class="client_fulladdress_span"></span></a>
							</li>
							<li>
								<label><?php echo __("Phone","oct");?></label>
								<span class="client_phone span-scroll span_indent"></span>
							</li>
							<li>
								<label><?php echo __("Payment","oct");?></label>
								<span class="client_payment span-scroll span_indent"></span>
							</li>
							<li>
								<label><?php echo __("Notes","oct");?></label>
								<span class="client_notes span-scroll span_indent"></span>
							</li>
							<li>
								<span class="client_details"></span>
							</li>	
							
						</ul>
					</div>
					<div class="modal-footer">
						<div class="oct-col12 oct-footer-popup-btn">
							<div id="oct_reschedule_btn" class="col-md-6 col-sm-6 np">
								<a class="btn btn-info" id="edit-booking-details" href="javascript:void(0)" data-target="edit-booking-details-view" data-toggle="modal" aria-hidden="true"><?php echo __("Update Appointment","oct");?> <i class="fa fa-angle-double-right"></i></a>				
							</div>
							
							<span id="oct_confirm_btn" class="col-md-2 col-sm-2 col-xs-4 np oct-w-32">
								<a id="oct-confirm-appointment-cal-popup" class="btn btn-link oct-small-btn" rel="popover" data-placement='top' title="Confirm note"><i class="fa fa-thumbs-up fa-2x"></i><br /><?php echo __("Confirm","oct");?></a>	
								<div id="popover-confirm-appointment-cal-popup" style="display: none;">
									<div class="arrow"></div>
									<table class="form-horizontal" cellspacing="0">
										<tbody>
											<tr>
												<td><textarea class="form-control" id="oct_booking_confirmnote" name="" placeholder="<?php echo __("Appointment Confirm Note","oct");?>" required="required" ></textarea></td>
											</tr>
											<tr>
												<td>
													<button id="oct_booking_confirm" data-method='C' value="Delete" class="btn btn-success btn-sm oct_crc_appointment" type="submit"><?php echo __("Confirm","oct");?></button>
													<button id="oct-close-confirm-appointment-cal-popup" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- end pop up -->
							</span>	
							<span id="oct_reject_btn" class="col-md-2 col-sm-2 col-xs-4 np oct-w-32">
								<a id="oct-reject-appointment-cal-popup" class="btn btn-link oct-small-btn" rel="popover" data-placement='top' title="<?php echo __("Reject reason?","oct");?>"><i class="fas fa-thumbs-down fa-2x"></i><br /><?php echo __("Reject","oct");?></a>
								
								<div id="popover-reject-appointment-cal-popup" style="display: none;">
									<div class="arrow"></div>
									<table class="form-horizontal" cellspacing="0">
										<tbody>
											<tr>
												<td><textarea class="form-control" id="oct_booking_rejectnote" name="" placeholder="<?php echo __("Appointment Reject Reason","oct");?>" required="required" ></textarea></td>
											</tr>
											<tr>
												<td>
													<button id="oct_booking_reject" data-method='R'  value="Appointment Rejected By Service Provider" class="btn btn-danger btn-sm oct_crc_appointment" type="submit"><?php echo __("Reject","oct");?></button>
													<button id="oct-close-reject-appointment-cal-popup" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- end pop up -->
							</span>	
							<span id="oct_cancel_btn" class="col-md-2 col-sm-2 col-xs-4 np oct-w-32">
								<a id="oct-cancel-appointment-cal-popup" class="btn btn-link oct-small-btn" rel="popover" data-placement='top' title="<?php echo __("Cancel reason?","oct");?>"><i class="fas fa-thumbs-down fa-2x"></i><br /><?php echo __("Cancel","oct");?></a>
								
								<div id="popover-cancel-appointment-cal-popup" style="display: none;">
									<div class="arrow"></div>
									<table class="form-horizontal" cellspacing="0">
										<tbody>
											<tr>
												<td><textarea class="form-control" id="oct_booking_cancelnote" name="" placeholder="<?php echo __("Appointment Cancel Reason","oct");?>" required="required" ></textarea></td>
											</tr>
											<tr>
												<td>
													<button id="oct_booking_cancel" data-method='CS'  value="Cancel By Service Provider" class="btn btn-success btn-sm oct_crc_appointment" type="submit"><?php echo __("Ok","oct");?></button>
													<button id="oct-close-reject-appointment-cal-popup" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- end pop up -->
							</span>	
							
							<span id="oct_delete_btn" class="col-md-2 col-sm-2 col-xs-4 np oct-w-32">
								<a id="oct-delete-appointment-cal-popup" class="btn btn-link oct-small-btn" rel="popover" data-placement='top' title="<?php echo __("Delete this appointment?","oct");?>"><i class="fas fa-trash fa-2x"></i><br /> <?php echo __("Delete","oct");?></a>
							
							<div id="popover-delete-appointment-cal-popup" style="display: none;">
								<div class="arrow"></div>
								<table class="form-horizontal" cellspacing="0">
									<tbody>
										<tr>
											<td>
												<button id="oct_booking_delete"  value="Delete" class="btn btn-danger btn-sm" type="submit"><?php echo __("Delete","oct");?></button>
												<button id="oct-close-del-appointment-cal-popup" class="btn btn-default btn-sm" href="javascript:void(0)"><?php echo __("Cancel","oct");?></button>
											</td>
										</tr>
									</tbody>
								</table>
							</div><!-- end pop up -->
						  </span>	
						</div>
					</div>
				</div>
			</div>
		
		</div></div><!-- end details of booking -->
		
		<div id="edit-booking-details-view" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title"><?php echo __("Appointment Details","oct");?></h4>
					</div>
					<div class="modal-body">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#edit-appointment-details"><?php echo __("Appointment Details","oct");?></a></li>
							<!-- <li><a data-toggle="tab" href="#edit-customer-details">Customer Details</a></li> -->
						</ul>
						<div class="tab-content">
							<div id="edit-appointment-details" class="tab-pane fade in active">
								<table>
									<tbody>
										<tr>
											<td><label><?php echo __("Provider","oct");?></label></td>
											<td>
												<div class="form-group">
													<select disabled="disabled" id="oct_booking_provider" class="selectpicker form-control" data-size="5" data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true"  >						
													</select>
												</div>
											</td>
										</tr>
										
										<tr>
											<td><label><?php echo __("Service","oct");?></label></td>
											<td>
												<div class="form-group">
													<select disabled="disabled" data-size="5" id="oct_booking_service" class="selectpicker form-control" data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true"  >					
													</select>
												</div>
											</td>
										</tr>
										<tr>
											<td></td>
											<td>
												<div class="oct-col6 oct-w-50">
													<div class="form-control">
														<span><?php echo $oct_currency_symbol;?></span><span id="oct_service_price"></span>
													</div>
												</div>	
												<div class="oct-col6 oct-w-50 float-right">
													<div class="form-control">
														<i class="far fa-clock"></i><span id="oct_service_duration"></span>
														<input type="hidden" id="oct_service_duration_val" value=""/>
													</div>
												</div>
												
											</td>
										</tr>
										<tr>
											<td><label for="oct-service-duration"><?php echo __("Date & Time","oct");?></label></td>
											<td>
												<div class="oct-col6 oct-w-50">
													<input class="form-control" placeholder="Select Date" data-sel_date="" data-selstaffid="" id="oct_booking_datetime" value='' />
												</div>
												<div class="oct-col6 oct-w-50 float-right">
													<select id="oct_booking_time" class="selectpicker" data-size="5" style="display: none;" >
														
													</select>
												</div>
												
											</td>
										</tr>
										<tr>
											<td><?php echo __("Reschedule Note","oct");?></td>
											<td><textarea id="oct_booking_rsnotes" class="form-control"></textarea></td>
										</tr>
											
									</tbody>
								</table>	
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="oct-col12 oct-footer-popup-btn">
							<div class="oct-col6">
								<button type="button" id="oct_reschedule_booking" class="btn btn-info"><?php echo __("Reschedule Appointment","oct");?></button>
							</div>						
						</div>
					</div>
				</div>
			</div>
		</div><!-- end details of booking -->
	</header>