<?php 
if(!session_id()) { @session_start(); }
include_once(dirname(dirname(__FILE__)).'/objects/class_general.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_location.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_service.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_service_schedule_price.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_category.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_provider.php');
include_once(dirname(dirname(__FILE__)).'/objects/class_front_octabook_first_step.php');

$plugin_url_for_ajax = plugins_url('', dirname(__FILE__));
$oct_mulitlocation_status = get_option('octabook_multi_location');
$oct_zipcode_booking_status = get_option('octabook_zipcode_booking');
$provider_avatar_view = get_option('octabook_show_provider_avatars');

$oct_general = new octabook_general();
$oct_location = new octabook_location();
$oct_service = new octabook_service();
$oct_service_schedule_price = new octabook_service_schedule_price();
$oct_category = new octabook_category();
$oct_staff = new octabook_staff();
$first_step = new octabook_first_step();

$octabook_api_key = get_option('octabook_api_key');
if($octabook_api_key != ""){ 
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&key=<?php echo $octabook_api_key;?>" ></script>

<?php
}

$onload_location_id = 0;
if($oct_mulitlocation_status=='E'){
	$locations = $oct_location->readAll_enable_locations();
	$counter = 0;
	foreach($locations as $location){
		if($counter==0){
			$onload_location_id = $location->id;
			break;
		}				
	}
}


$oct_service->location_id = $onload_location_id;
$octservices = $oct_service->readAll('');
$services_categories = array();
$location_services = array();
foreach($octservices as $octservice){
	if(!in_array($octservice->category_id, $services_categories)){
		$services_categories[] = $octservice->category_id;
	}
	$location_services[] = $octservice->id;
}

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$_SESSION['booking_home'] = $actual_link;

/* Load All Providers */
$oct_staff->location_id = $onload_location_id;
$octstaffs = $oct_staff->readAll();

echo "<style>
	#oct .oct-button{
		color : ".get_option('octabook_bg_text_color')." !important;
		background-color: ".get_option('octabook_primary_color')." !important;
	}
	.oct-loader-first-step {
	
    height: 25px;
    width: 25px;
    -webkit-animation: rotate 1s infinite linear;
    -moz-animation: rotate 1s infinite linear;
    -ms-animation: rotate 1s infinite linear;
    animation: rotate 1s infinite linear;
    border: 3px solid #333;
    border-top-color: transparent !important;
    border-radius: 50%;
    display: inline-block;
    position: absolute;
    right: 10%;
    top: 22%;
    display:none;
	}

	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
	#oct .oct-extra-services-main ul.extra-services-items li i.icon-minus.icons{
		color : ".get_option('octabook_secondary_color')." !important;
	}
	#oct .oct-extra-services-main ul.extra-services-items li a.oct-delete-confirm,
	#oct aside#content-sidebar .sidebar-box .booking-list a.oct-delete-booking-box{
		color : ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-extra-services-main ul.extra-services-items li a.oct-delete-confirm:hover,
	#oct aside#content-sidebar .sidebar-box .booking-list a.oct-delete-booking-box:hover{
		color : ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_secondary_color')." !important;
	}
	
	
	#oct .oct-button:hover{
		color : ".get_option('octabook_bg_text_color')." !important;
		background-color: ".get_option('octabook_secondary_color')." !important;
	}
	
	#oct .oct-link,
	#oct .oct-complete-booking-main .oct-link,
	#oct label span.oct-logged-in-user,
	#oct a.oct-logout-user b:hover,
	#oct .service-details.oct-show .oct-close-desc:hover{
		color : ".get_option('octabook_secondary_color')." !important;
	}
	#oct{
		color : ".get_option('octabook_text_color')." !important;
	}
	#oct h3.block-title{
		color : ".get_option('octabook_secondary_color')." !important;
	}
	
	#oct .oct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked label span,
	#oct .oct-service-staff-list ul.staff-list li input[type='checkbox']:checked label span{
		color : ".get_option('octabook_bg_text_color')." !important;
	}	
	#oct .oct-addon-count .oct-btn-group .oct-btn-text{
		color : ".get_option('octabook_text_color')." !important;
	}
	#oct a.oct-logout-user b,
	#oct .oct-complete-booking-main .oct-link,
	#oct .oct-discount-coupons a.oct-apply-coupon.oct-link {
		color : ".get_option('octabook_primary_color')." !important;
	}
	#oct a,
	#oct .oct-button#btn-more-bookings,
	#oct aside#content-sidebar .sidebar-box .booking-list .provider-info .provider-title,
	#oct aside#content-sidebar .sidebar-box .booking-list .right-booking-details .price,
	#oct aside#content-sidebar .sidebar-box .booking-list .right-booking-details .common-style{
		color : ".get_option('octabook_text_color')." !important;
	}
	#oct i.bottom-line:after,
	#oct i.bottom-line:before,
	#oct i.icon-close-custom:hover:before,
	#oct i.icon-close-custom:hover:after{
		background: ".get_option('octabook_secondary_color')." !important;
	}	
	
	#oct .oct-booking-step{
		border-bottom-color: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-booking-step ul li.active,
	#oct .oct-booking-step ul li span.sep.active {
		color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct .oct-booking-step ul li {
		color: ".get_option('octabook_primary_color')." !important;
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
	#oct button{
		color : ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-custom-radio ul.oct-radio-list label span{
		border-color: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-title-header,
	#oct .oct-sidebar-header{
		color : ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_secondary_color')." !important;
		border-color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct .calendar-header{
		background-color: ".get_option('octabook_primary_color')." !important;
	}
	#oct .calendar-header a.previous-date,
	#oct .calendar-header a.next-date {
		color : ".get_option('octabook_bg_text_color')." !important;
	}
	#oct .custom-checkbox input[type='checkbox']:checked + label .check-icon:before{
		border-left: 2px solid ".get_option('octabook_secondary_color')." !important;
		border-bottom: 2px solid ".get_option('octabook_secondary_color')." !important;
	}
	#oct .custom-checkbox input[type='checkbox']:checked + label .check-icon {
		border: 1px solid ".get_option('octabook_secondary_color')." !important;
	}
	#oct .today-date .oct-selected-date-view .custom-check:before{
		border-left: 2px solid ".get_option('octabook_secondary_color')." !important;
		border-bottom: 2px solid ".get_option('octabook_secondary_color')." !important;
	}
	#oct .calendar-body .dates .oct-week.by_default_today_selected,
	.time-slot-container ul li.time-slot,
	.oct-slot-legends .oct-available-new{
		background: ".get_option('octabook_primary_color')." !important;
	}
	#oct button:hover{
		color: ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_secondary_color')." !important;
	}
	#oct a:hover,
	#oct .oct-button#btn-more-bookings:hover,
	#oct .oct-link:hover,
	#oct .oct-complete-booking-main .oct-link:hover,
	#oct .oct-discount-coupons a.oct-apply-coupon.oct-link:hover {
		color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct .weekdays,
	#oct .calendar-wrapper .calendar-header a.next-date:hover,
	#oct .calendar-wrapper .calendar-header a.previous-date:hover,
	#oct .calendar-body .oct-week:hover{
		background: ".get_option('octabook_secondary_color')." !important;
	} 
	#oct .calendar-body .oct-week:hover span{
		color: ".get_option('octabook_bg_text_color')." !important;
	}
	.oct-slot-legends .oct-selected-new {
		background: ".get_option('octabook_secondary_color')." !important;
	}	
	#oct .calendar-body .dates .oct-week.active {
		background-color: ".get_option('octabook_secondary_color')." !important;
		border-bottom-color: ".get_option('octabook_secondary_color')." !important;
	}
	.time-slot-container ul li.time-slot:hover,
	.time-slot-container ul li.time-slot.oct-booked,
	.time-slot-container ul li.time-slot.oct-slot-selected,
	.oct-show-time.shown {
		background-color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list .right-booking-details .delete:hover,
	#oct .panel-login .panel-heading .col-xs-6.active{
		color: ".get_option('octabook_bg_text_color')." !important;
		background: ".get_option('octabook_primary_color')." !important;
	}
	#oct .oct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked + label .addon-price {
		color: ".get_option('octabook_bg_text_color')." !important;
		background-color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct .oct-extra-services-list ul.addon-service-list li input[type='checkbox']:checked + .oct-addon-ser,
	#oct .oct-service-staff-list ul.staff-list li input[type='radio']:checked + .oct-staff .oct-staff-img img {
		border-color: ".get_option('octabook_secondary_color')." !important;
		box-shadow: 0 0 2px 0px ".get_option('octabook_secondary_color')." !important;
	}
	#oct .oct-service-staff-list ul.staff-list li .oct-staff span{
		background-color: ".get_option('octabook_secondary_color')." !important;
	}
	#oct .oct-custom-radio ul.oct-radio-list input[type='radio']:checked + label span {
		border: 5px solid ".get_option('octabook_secondary_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list .right-booking-details .delete,
	#oct #navbar .booking-steps > li.is-complete,
	#oct .oct-link{
		color: ".get_option('octabook_primary_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list:hover .delete span:after{
		color: ".get_option('octabook_bg_text_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list .right-booking-details .delete:hover{
		color: ".get_option('octabook_primary_color')." !important;
		background: ".get_option('octabook_secondary_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list .delete span:before{
		border-right: 40px solid ".get_option('octabook_secondary_color')." !important;
	}
	#oct aside#content-sidebar .sidebar-box .booking-list:hover{
		border-color: ".get_option('octabook_secondary_color')." !important;
	}
	
	#oct .oct-main-right .oct-sidebar-header h3.header3 {
		color: ".get_option('octabook_bg_text_color')." !important;
	}	
	".get_option('octabook_frontend_custom_css')."

	.oct_tmtslt_ul li{
		width:28%;
		display:inline-block;
		text-align:center;
		position:relative;
		padding:10px;
		cursor:pointer;
		border-radius:10px;
		

	}



	.oct_tmtslt_ul li:hover{
	color:#fff;
	}

	

	

		.oct_tmtslt_ul li.confirm-slot-final{
        border-radius:10px;
		text-align:left ;

	}

	.confirm-slot-final-txt{
	background: #C44A5D;
	border-radius:9px;
    border:5px solid #fff;
    color: #f5efe0;
    font-weight: 400;
    font-size: 16px;
    position: absolute;
    top: 0px;
    left: 85px;
    z-index: 99999;
    height: 58px;
    width: 81px;
    line-height: 45px;
    text-align: center;
 
}

.oct_service_li_tilte{
vertical-align: middle;
    width: 18px;
    display:inline-block;
    position: relative;
    top: -2px;
    left: -4px;
}


	
	</style>";
	
	if(is_rtl()){
		echo "<style>
			#oct aside#content-sidebar .sidebar-box .booking-list .delete span:before{
				border-left: 40px solid ".get_option('octabook_secondary_color')." !important;
				border-right: unset !important;
			}
		</style>";
	}
	if(is_rtl()){
		echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery('#oct').addClass('octrtl');
		});
		</script>";
	}
	
?>

<script src="https://js.stripe.com/v2/" type="text/javascript"></script>
<script>
var oct_stripeObj = { 'pubkey': '<?php echo get_option('octabook_stripe_publishableKey'); ?>'};
</script>
 <div class="oct-wrapper"  id="oct"> <!-- main wrapper -->
<div class="loader">
	<div class="oct-loader">
		<span class="oct-first"></span>
		<span class="oct-second"></span>
		<span class="oct-third"></span>
	</div>
</div>
	<div class="containerd">
		<div class="oct-main-wrapper">
<section>
	<main id="oct-main">	
		<!-- main inner content -->
		<section class="oct-display-middle oct-main-left oct-md-8 oct-sm-7 oct-xs-12 np pull-left <?php if(!isset($_SESSION['oct_cart_item']) || (isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])==0)){ echo 'no-sidebar-right'; } ?> oct_remove_left_sidebar_class">
			<div class="oct-main-inner fullwidth">
			<div class="hide-data visible cb oct_login_form_check_validate" id="oct_second_step">
				<div class="oct-second-step form-inner visible" >
		
					<div class="oct-booking-step" data-current="2">
						<ul class="oct-list-inline nm">
							<li class="oct_stand oct_frt active"><?php echo __("Choose Location ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_snd"> <?php echo __("Which service you would like us to provide? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_frth"> <?php echo __("To whom you want to select as service provider? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_fth"> <?php echo __("When would you like us to come? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>
							
							<li class="oct_stand oct_sth"><?php echo __("Info and Checkout","oct");?><span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_svth"><?php echo __("Done","oct");?></li>
						</ul>
						<div class="oct-loader-first-step"></div>
					</div>
					<div class="oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 pull-left">

						<h3 class="block-title"><i class="icon-user icons fs-15"></i> <?php echo __("User Information","oct");?></h3>
						<?php
						$current_user = wp_get_current_user();
						$current_user_id = $current_user->ID ;
						$current_user_meta = get_user_meta($current_user_id);
						$user_extra_info = get_user_meta($current_user_id,'oct_client_extra_details');
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
						?>
						<div class="existing-user-success-login-message row fullwidth" <?php if(!is_user_logged_in()){ echo 'style="display:none;"'; } ?>>
							<div class="oct-form-row oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12">
								<label class="oct-relative custom  mb-30"><?php echo __("You are logged in as","oct");?> <b><span class="oct-logged-in-user" id="logged_in_user_name"><?php if($current_user->user_firstname != ''){ echo $current_user->user_firstname; } if($current_user->user_lastname != ''){ echo ' '.$current_user->user_lastname; } ?></span></b>  <a href="javascript:void(0);" class="oct-logout-user" id="oct_log_out_user"><b><?php echo __("Logout","oct");?></b></a></label> 
							</div>
						</div>
						<div class="common-inner oct-info-input">
						    <div class="oct-main-inner" id="user-login">
								<div class="user-login-main oct-form-row fullwidth" <?php if(is_user_logged_in()){ echo 'style="display:none;"'; } ?>>
									<div class="oct-custom-radio oct-user-login-radio fullwidth">
										<ul class="oct-radio-list">	
											<li class="oct-first-radio oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
												<input id="oct-existing-user" class="input-radio existing-user new_and_existing_user_radio_btn" name="user-selection" value="Existing User" type="radio" <?php if(is_user_logged_in()){ echo 'checked="checked"'; } ?> />
												<label class="oct-relative" for="oct-existing-user"><span></span><?php echo __("Existing User","oct");?></label>
											</li>
											<li class="oct-second-radio oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
												<input id="oct-new-user" class="input-radio new-user new_and_existing_user_radio_btn" name="user-selection" value="New User" type="radio" <?php if(!is_user_logged_in()){ echo 'checked="checked"'; } ?> />
												<label class="oct-relative" for="oct-new-user"><span></span><?php echo __("New User","oct");?></label>
											</li>
											<?php
											if(get_option('octabook_guest_user_checkout') == "E"){
												?>
												<li class="oct-third-radio oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
													<input id="oct-guest-user" class="input-radio guest-user new_and_existing_user_radio_btn" name="user-selection" value="Guest User" type="radio" />
													<label class="oct-relative" for="oct-guest-user"><span></span><?php echo __("Guest User","oct"); ?></label>
												</li>
												<?php 
											} ?>
										</ul>
									</div>
									<!-- user login form --> 
									<form method="post" id="oct_login_form_check_validate">
										<div class="existing-user-login row fullwidth">
											<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
												<div class="pr">
													<input type="text" class="custom-input" name="oct_existing_login_username_input" id="oct_existing_login_username" />
													<label class="custom"><?php echo __("Email","oct");?></label>
													<i class="bottom-line"></i>
												</div> 
												<label class="oct-relative oct-error"><?php echo __("Please enter Email","oct");?></label>
											</div>
											<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
												<div class="pr">
													<input type="password" name="oct_existing_login_password_input" class="custom-input" id="oct_existing_login_password" />
													<label class="custom"><?php echo __("Password","oct");?></label>
													<i class="bottom-line"></i>
												</div> 
												
												<label class="oct-relative oct-error"><?php echo __("Please enter Password","oct");?></label>
											</div>
											<label id="invalid_un_pwd" style="color: red;top: 55px;bottom: 15px;font-size: 14px;display:none"><?php echo __("Invalid Email or Password","oct");?></label>
											<div class="oct-xs-12 oct-md-12 oct-form-row oct-fw">
												<a href="javascript:void(0);" id="oct_existing_login_btn" class="oct-button nm float-left" title="Login account"><i class="fa fa-lock"></i> <?php echo __("Login","oct");?></a>
												<span class="oct-forget-pass">
													<a href="<?php echo home_url();?>/wp-login.php?action=lostpassword" class="oct-link" title="Forget Password"><?php echo __("Forget Password","oct");?></a>
												</span>
											</div>
										</div>
									</form>
								</div>
							</div> 
							<div class="oct-main-inner" id="new-user">
								<form method="post" id="oct_newuser_form_validate">
									<!-- new user fields -->
									<!-- new user area content prefered username and password -->
									<div class="new-user-area row hide_new_user_login_details" <?php if(is_user_logged_in()){ echo 'style="display:none;"'; } ?>>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_preferred_username" id="new_user_preferred_username" value="<?php if($current_user->user_email != ''){ echo $current_user->user_email; } ?>" />
												<label class="custom"><?php echo __("Preferred Email","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Preferred Email","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="password" class="custom-input" name="new_user_preferred_password" id="new_user_preferred_password" value="<?php if($current_user->user_pass != ''){ echo $current_user->user_pass; } ?>" />
												<label class="custom"><?php echo __("Preferred Password","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Preferred Password","oct");?></label>
										</div>
									</div>
									<!-- common inputs -->
									<div class="row oct-common-inputs fullwidth new-user-personal-detail-area">
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_firstname" id="new_user_firstname" value="<?php if($current_user->user_firstname != ''){ echo $current_user->user_firstname; } ?>" />
												<label class="custom"><?php echo __("First Name","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter First Name","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_lastname" id="new_user_lastname" value="<?php if($current_user->user_lastname != ''){ echo $current_user->user_lastname; } ?>" />
												<label class="custom"><?php echo __("Last Name","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Last Name","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
											<input type="hidden" class="input_flg" value="<?php echo get_option('octabook_company_country_code'); ?>">
												<input type="text" type="tel" name="oct-phone" class="custom-input oct-phone-input" value="<?php if(isset($current_user_meta['oct_client_phone']) && $current_user_meta['oct_client_phone'][0] != ''){ echo $current_user_meta['oct_client_phone'][0]; } ?>" data-ccode="<?php if(isset($current_user_meta['oct_client_ccode']) && $current_user_meta['oct_client_ccode'][0] != ''){ echo $current_user_meta['oct_client_ccode'][0]; } ?>" id="oct-front-phone" />
												<label class="custom oct-phone-label"><?php echo __("Phone number","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Phone number","oct");?></label>
											<label id="oct-front-phone-error" class="error" for="oct-front-phone" ><?php echo __("Please Enter Only Numeric value","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="fullwidth">
												<label class="oct-relative"><?php echo __("Gender","oct");?></label>
												<div class="oct-custom-radio">
													<ul class="oct-radio-list">	
														<li class="oct-first-radio oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
															<input id="oct-male" class="input-radio new_user_gender" name="oct-gender" <?php if(isset($current_user_meta['oct_client_gender']) && $current_user_meta['oct_client_gender'][0] != '' && $current_user_meta['oct_client_gender'][0] == 'M'){ echo 'checked="checked"'; } ?> type="radio" value="M" />
															<label for="oct-male" class="oct-relative"><span></span><?php echo __("Male","oct");?></label>
														</li>
														<li class="oct-second-radio oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
															<input id="oct-female" class="input-radio new_user_gender" name="oct-gender" <?php if(isset($current_user_meta['oct_client_gender']) && $current_user_meta['oct_client_gender'][0] != '' && $current_user_meta['oct_client_gender'][0] == 'F'){ echo 'checked="checked"'; } ?> type="radio" value="F" />
															<label for="oct-female" class="oct-relative"><span></span><?php echo __("Female","oct");?></label>
														</li>
													</ul>
												</div>
											</div> 											
										</div>
										<div class="oct-button-container text-center">
										<a class="oct-button get_current_location" id="btn-get_location" href="javascript:void(0)"><?php echo __("Get My Location","oct");?></a>
										</div>
										<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_street_address" id="new_user_street_address" value="<?php if(isset($current_user_meta['oct_client_address']) && $current_user_meta['oct_client_address'][0] != ''){ echo $current_user_meta['oct_client_address'][0]; } ?>" />
												<label class="custom"><?php echo __("Street Address","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Street Address","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_city" id="new_user_city" value="<?php if(isset($current_user_meta['oct_client_city']) && $current_user_meta['oct_client_city'][0] != ''){ echo $current_user_meta['oct_client_city'][0]; } ?>" />
												<label class="custom"><?php echo __("Town/City","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Town/City","oct");?></label>
										</div>
										<div class="oct-form-row oct-md-6 oct-lg-6 oct-sm-12 oct-xs-12">
											<div class="pr">
												<input type="text" class="custom-input" name="new_user_state" id="new_user_state" value="<?php if(isset($current_user_meta['oct_client_state']) && $current_user_meta['oct_client_state'][0] != ''){ echo $current_user_meta['oct_client_state'][0]; } ?>" />
												<label class="custom"><?php echo __("State","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter State","oct");?></label>
										</div>
										<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
											<div class="pr">
												<textarea class="custom-input" name="new_user_notes" id="new_user_notes"><?php if(isset($current_user_meta['oct_client_notes']) && $current_user_meta['oct_client_notes'][0] != ''){ echo $current_user_meta['oct_client_notes'][0]; } ?></textarea>
												<label class="custom"><?php echo __("Special Notes","oct");?></label>
												<i class="bottom-line"></i>
											</div> 
											<label class="oct-relative oct-error"><?php echo __("Please enter Special Notes","oct");?></label>
										</div>
										<!-----Custom form fields start------>
										<div class="oct-form-builder-form-fields">
											<?php
											if(get_option('octabook_custom_form') != FALSE){
												$oct_formfields  = json_decode(stripslashes(get_option('octabook_custom_form')),true);
												if(sizeof((array)$oct_formfields) > 0){
													foreach($oct_formfields as $oct_formfield) {
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='radio-group') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12">
																<div class="fullwidth">
																	<label class="oct-relative"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<div class="oct-custom-radio">
																		<ul class="oct-radio-list">
																			<?php 
																			if(isset($oct_formfield['values']) && sizeof((array)$oct_formfield['values'])>0){
																				foreach($oct_formfield['values'] as $singleInput) {	?>
																					<li class="oct-sm-6 oct-md-4 oct-lg-4 oct-xs-12">
																						<input data-fieldname="radio_group"  data-fieldlabel="<?php echo $oct_formfield['label']; ?>" id="<?php echo $singleInput['value']; ?>" class="input-radio get_custom_field <?php echo $oct_formfield['className']; ?>" name="<?php echo $oct_formfield['name']; ?>" value="<?php echo $singleInput['value']; ?>" data-required="<?php echo $req_field; ?>" <?php if(isset($singleInput['selected']) && $singleInput['selected']){ echo 'checked="checked"'; } ?> type="radio" <?php   if($singleInput['label'] == $user_extra_info_array[$oct_formfield['label']]) ?>/>
																						<label for="<?php echo $singleInput['value']; ?>" class="oct-relative"><span></span><?php echo __($singleInput['label'],"oct");?></label>
																					</li>
																					<?php
																				}
																			}
																			?>
																		</ul>
																	</div>
																</div>
															</div>
															<?php
														}
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='checkbox-group') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<label class="oct-relative"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<div class="oct-custom-checkbox">
																		<ul class="custom-checkbox-list">
																			<?php
																			if(isset($oct_formfield['values']) && sizeof((array)$oct_formfield['values'])>0){
																				foreach($oct_formfield['values'] as $singleInput) {
																					?>
																					<li class="custom-checkbox ccb-absolute oct-sm-6 oct-md-4 oct-lg-4 oct-xs-12">
																						<input id="<?php echo $singleInput['value']; ?>" name="<?php echo $oct_formfield['name']; ?>" <?php if(isset($singleInput['selected']) && $singleInput['selected']){ echo 'checked'; } ?> class="input-checkbox get_custom_field <?php echo $oct_formfield['className']; ?>" data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php echo $oct_formfield['label']; ?>" value="<?php echo $singleInput['value']; ?>" type="checkbox"  />
																						<label for="<?php echo $singleInput['value']; ?>" class="oct-relative"><span class="check-icon"></span><span class="label-text"><?php echo __($singleInput['label'],"oct");?></span></label>
																					</li>
																					<?php
																				}
																			}
																			?>
																		</ul>
																	</div>
																</div>
															</div>
															<?php
														}												   
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='checkbox') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<label class="oct-relative"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<div class="oct-custom-checkbox">
																		<ul class="custom-checkbox-list">
																			<li class="custom-checkbox ccb-absolute oct-sm-6 oct-md-4 oct-lg-4 oct-xs-12">
																				<input id="<?php if(isset($oct_formfield['name']) && $oct_formfield['name'] != ""){ echo $oct_formfield['name']; } ?>" name="<?php if(isset($oct_formfield['name']) && $oct_formfield['name'] != ""){ echo $oct_formfield['name']; } ?>" class="input-checkbox get_custom_field <?php if(isset($oct_formfield['className']) && $oct_formfield['className'] != ""){ echo $oct_formfield['className']; } ?>" data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php if(isset($oct_formfield['label']) && $oct_formfield['label'] != ""){ echo $oct_formfield['label']; } ?>" value="<?php if(isset($oct_formfield['value']) && $oct_formfield['value'] != ""){ echo $oct_formfield['value']; } ?>" type="checkbox" <?php   if($oct_formfield['value'] == $user_extra_info_array[$oct_formfield['label']]){echo "checked";} ?> />
																				<label for="<?php echo $oct_formfield['name']; ?>" class="oct-relative"><span class="check-icon"></span><span class="label-text"><?php if(isset($oct_formfield['label']) && $oct_formfield['label'] != ""){ echo __($oct_formfield['label'],"oct"); } ?></span></label>
																			</li>
																		</ul>
																	</div>
																</div>
															</div>
															<?php
														}
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='text') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<input type="<?php echo $oct_formfield['subtype']; ?>" data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php echo $oct_formfield['label']; ?>" class="custom-input get_custom_field <?php echo $oct_formfield['className']; ?>" name="<?php echo $oct_formfield['name']; ?>" id="<?php echo $oct_formfield['name']; ?>" value="<?php   echo $user_extra_info_array[$oct_formfield['label']]; ?>" />
																	<label class="custom"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<i class="bottom-line"></i>
																</div> 
																<label class="oct-relative oct-error"><?php echo __("Please enter ".$oct_formfield['label'],"oct");?></label>
															</div>
															<?php
														}
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='number') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<input type="number" data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php echo $oct_formfield['label']; ?>" class="custom-input get_custom_field <?php echo $oct_formfield['className']; ?>" name="<?php echo $oct_formfield['name']; ?>" id="<?php echo $oct_formfield['name']; ?>" value="<?php   echo $user_extra_info_array[$oct_formfield['label']]; ?>" />
																	<label class="custom"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<i class="bottom-line"></i>
																</div> 
																<label class="oct-relative oct-error"><?php echo __("Please enter ".$oct_formfield['label'],"oct");?></label>
															</div>
															<?php
														}
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='select') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<label class="oct-relative"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<select class="octcust-select get_custom_field <?php echo $oct_formfield['className']; ?>" data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php echo $oct_formfield['label']; ?>">
																		<?php
																		if(isset($oct_formfield['values']) && sizeof((array)$oct_formfield['values'])>0){
																			foreach($oct_formfield['values'] as $singleInput) {
																				?>
																				<option value="<?php echo $singleInput['value']; ?>" id="<?php echo $oct_formfield['name']; ?>" name="<?php echo $oct_formfield['name']; ?>"<?php   if($singleInput['value'] == $user_extra_info_array[$oct_formfield['label']]){echo "selected";} ?>><?php echo $singleInput['value']; ?></option>
																				<?php
																			}
																		}
																		?>
																	</select>				
																</div>
															</div>
															<?php
														}
														if(isset($oct_formfield['type']) && $oct_formfield['type']=='textarea') {
															if(isset($oct_formfield['required']) && $oct_formfield['required'] == true){
																$req_field = 'Y';
															}else{
																$req_field = 'N';
															}
															?>
															<div class="oct-form-row oct-sm-12 oct-xs-12 oct-fw">
																<div class="pr">
																	<textarea data-required="<?php echo $req_field; ?>" data-fieldlabel="<?php echo $oct_formfield['label']; ?>" class="custom-input get_custom_field <?php echo $oct_formfield['className']; ?>" name="<?php echo $oct_formfield['name']; ?>" id="<?php echo $oct_formfield['name']; ?>" ><?php   echo $user_extra_info_array[$oct_formfield['label']]; ?></textarea>
																	<label class="custom"><?php echo __($oct_formfield['label'],"oct");?></label>
																	<i class="bottom-line"></i>
																</div> 
																<label class="oct-relative oct-error"><?php echo __("Please enter ".$oct_formfield['label'],"oct");?></label>
															</div>
															<?php
														}
													}
												}
											}
											?>
										</div>	
										<!-----Custom form fields end------>
									</div>
								</form>
							</div><!-- oct main inner end -->	
						</div>
						<?php if(get_option('octabook_payment_gateways_status')=='E'){ $paystripe = false; ?>
						<div class="common-inner">
							<h3 class="block-title"><i class="icon-wallet icons fs-15"></i> <?php echo __("Payment Methods","oct");?></h3>
							<div class="oct-custom-radio" id="oct-payments">
								<ul class="oct-radio-list payment_checkbox">
									<?php if(get_option('octabook_payment_gateways_status')=='D'){ ?>
										<input type="hidden" id="pay-locally" class="input-radio oct_payment_method" checked="checked"  name="oct-payment-options" value="pay_locally" />
									<?php } ?>									
									<?php if(get_option('octabook_payment_gateways_status')=='E' && get_option('octabook_locally_payment_status')=='E'){ ?>
									<li class="oct-pay-locally oct-lg-4 oct-md-4 oct-sm-6 oct-xs-12 np">
										<input type="radio" id="pay-locally" class="input-radio oct_payment_method" checked="checked"  name="oct-payment-options" value="pay_locally" />
										<label for="pay-locally" class="oct-relative"><span></span><?php echo __("I will pay locally","oct");?></label>
									</li>
									<?php } ?>
									<?php if(get_option('octabook_payment_gateways_status')=='E' && get_option('octabook_payment_method_Paypal')=='E'){ ?>
									<li class="oct-paypal-payments oct-lg-4 oct-md-4 oct-sm-6 oct-xs-12 np">
										<input type="radio" id="paypal" checked="checked" class="input-radio oct_payment_method" name="oct-payment-options" value="paypal" />
										<label for="paypal" class="oct-relative"><span></span><?php echo __("Paypal","oct");?> <img class="oct-paypal-image" src="<?php echo plugins_url().'/octabook/assets/images/paypal.png';?>" title="Paypal" /></label>
									</li><?php } ?>
									<?php if(get_option('octabook_payment_gateways_status')=='E' && get_option('octabook_payment_method_Stripe')=='E'){ $paystripe = true; ?>
									<li class="oct-stripe-payments oct-lg-4 oct-md-4 oct-sm-6 oct-xs-12 np">
										<input type="radio" id="stripe-payments" checked="checked" class="input-radio oct_payment_method" name="oct-payment-options" value="stripe" />
										<label for="stripe-payments" class="oct-relative"><span></span><?php echo __("Pay with card now","oct");?> <i class="icon-credit-card icons"></i></label>
									</li><?php } ?>
									<?php if(get_option('octabook_payment_gateways_status')=='E' && get_option('octabook_payment_method_Payumoney')=='E'){ $paystripe = false; ?>
									<li class="oct-payumoney-payments oct-lg-4 oct-md-4 oct-sm-6 oct-xs-12 np">
										<input type="radio" id="payumoney-payments" checked="checked" class="input-radio oct_payment_method" name="oct-payment-options" value="payumoney" />
										<label for="payumoney-payments" class="oct-relative"><span></span><?php echo __("Pay with payumoney","oct");?> <i class="icon-credit-card icons"></i></label>
									</li><?php } ?>
									<?php if(get_option('octabook_payment_gateways_status')=='E' && get_option('octabook_payment_method_Paytm')=='E'){ $paystripe = false; ?>
									<li class="oct-payumoney-payments oct-lg-4 oct-md-4 oct-sm-6 oct-xs-12 np">
										<input type="radio" id="paytm-payments" checked="checked" class="input-radio oct_payment_method" name="oct-payment-options" value="paytm" />
										<label for="paytm-payments" class="oct-relative"><span></span><?php echo __("Pay with paytm","oct");?> <i class="icon-credit-card icons"></i></label>
									</li><?php } ?>
								</ul>
								<label class="oct-relative oct-error payment_error_msg"><?php echo __("Select Atleast one payment method","oct");?></label>
							</div>
							
							<?php if(get_option('octabook_payment_method_Stripe')=='E'){ ?>
							<!-- stripe payment card inputs -->
							<div class="oct-stripe-wrapper" id="stripe-payment-main" style="<?php   if($paystripe){echo "display: block";}else{echo "display: none";} ?>">
								<div class="oct-stripe-card">
									<div class="stripe-header">
										<div class="card-header"><?php echo __("Card Details","oct");?></div>
										<img class="card-sample-img pull-right" src="<?php echo plugins_url().'/octabook/assets/images/cards/cards.png' ?>" />
									</div>
									<label class="show_card_payment_error oct-relative oct-error"><?php echo __("Card number is invalid","oct");?> </label>
									<div class="card-input-container pr oct-sm-10 oct-md-9 oct-xs-12 res-nplr">
										<div class="oct-form-row oct-xs-12 np">
										<label class="oct-relative fs-13"><?php echo __("Card number","oct");?></label>
											<div class="oct-card-number-main">
												<i class="icon-credit-card icons"></i>
												<input type="text" id="card-number" name="oct-town-city" class="cc-number input-card custom-input" type="tel" maxlength="20" size="20" placeholder="XXXX XXXX XXXX XXXX" />			
												<span class="card-type" aria-hidden="true"></span>	
												<i class="bottom-line"></i>
											</div>
										</div>
										<div class="oct-form-row oct-md-7 oct-sm-8 oct-xs-12 res-npr npl">
											<label class="oct-relative fs-13"><?php echo __("Expiry (MM/YYYY)","oct");?></label>
											<div class="expiry-month-year oct-xs-5 np pr">
												<i class="icon-calendar icons"></i>
												<input type="tel" id="card-expiry" class="cc-exp-month expiry-month nmt custom-input" maxlength="2" placeholder="MM" />
												<em class="card-mon-year-separator"></em>
												<i class="bottom-line"></i>
											</div>
											<div class="expiry-month-year expiry_year oct-sm-5 oct-xs-6 npr pr pull-right">
												<input type="tel" class="cc-exp-year expiry-year nmt custom-input" maxlength="4" placeholder="YYYY" /> 
												<i class="bottom-line"></i>
											</div>
										</div>
										<div class="cvc-code oct-form-row oct-md-5 oct-sm-4 oct-xs-12 res-npl npr">
										<label class="oct-relative fs-13"><?php echo __("CVC","oct");?></label>
											<div class="oct-cvc-code-main pr">
												<i class="icon-lock icons"></i>
												<input id="cvc-code" type="password" size="4" maxlength="4" class="cc-cvc cvc-code oct-cvc-code custom-input" />
												<div class="card-cvc-clue pull-right">
													<img class="cvc-image-hint" src="<?php echo plugins_url().'/octabook/assets/images/oct-cvv.png';?>" /><?php echo __("The last 3 digit printed on the signature panel on the back of your credit card.","oct");?> 
												</div>
												<i class="bottom-line"></i>
											</div>
										</div>
									</div>
									<div class="oct-form-row pull-right oct-md-3 oct-sm-2 np res-hidden secure-img">
										<img src="<?php echo plugins_url().'/octabook/assets/images/oct-secure.png' ?>" />
									</div>
									
								</div>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
						
						<?php if(get_option('octabook_allow_terms_and_conditions')=='E' || get_option('octabook_allow_privacy_policy')=='E' || get_option('octabook_cancelation_policy_status')=='E'){ ?>
						<div class="oct-complete-booking-main cb fullwidth  mt-20">
							<?php if(get_option('octabook_cancelation_policy_status')=='E'){ ?>
							<div class="oct-complete-booking oct-md-12 np">
								<h5 class="oct-cancel-booking"><?php echo __("Cancellation Policy","oct");?></h5>
								<div class="oct-cancel-policy">
									<p><?php echo get_option('octabook_cancelation_policy_header');?>
									<span class="show-more-toggler oct-link"><?php echo __("Show More","oct");?></span></p>
									<ul class="bullet-more" style="display: none;">
										<li><?php echo get_option('octabook_cancelation_policy_text');?></li>
									</ul>
								</div>
							</div>
							<?php } ?>
							<?php if(get_option('octabook_allow_terms_and_conditions')=='E' || get_option('octabook_allow_privacy_policy')=='E'){ ?>							
							<div class="oct-terms-agree oct-md-12 oct-xs-12 mt-20 mb-20 cb np">
								<div class="oct-custom-checkbox">
									<ul class="custom-checkbox-list">
										<li class="custom-checkbox ccb-absolute fullwidth oct-termcondition-area">
											<input name="oct-accept-conditions" class="input-radio" id="oct-accept-conditions" type="checkbox">
											<label for="oct-accept-conditions" class="oct-relative">
												<span class="check-icon"></span><span class="label-text"> <?php echo __("I have read and accepted the","oct");?>
												<?php if(get_option('octabook_allow_terms_and_conditions')=='E'){ ?>
												<a href="<?php echo urldecode(get_option('octabook_allow_terms_and_conditions_url'));?>" target="_blank" class="oct-link"><?php echo __("Terms &amp; Conditions","oct");?></a><?php } ?> 
												<?php if(get_option('octabook_allow_terms_and_conditions')=='E' && get_option('octabook_allow_privacy_policy')=='E'){ ?>
												<?php echo __("and","oct");?>
												<?php } ?>
												<?php if(get_option('octabook_allow_privacy_policy')=='E'){ ?> 
												<a href="<?php echo urldecode(get_option('octabook_allow_privacy_policy_url'));?>" target="_blank" class="oct-link"><?php echo __("Privacy Policy","oct");?></a><?php } ?>.</span>
											</label>
										</li>
									</ul>
								</div>
								<label class="oct-relative oct-error oct_terms_and_condition_error"><?php echo __("Please Accept term & conditions.","oct");?></label>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>	
			
			<div class="hide-data visible cb" id="oct_third_step">
				<div class="oct-third-step form-inner visible" >
					<div class="oct-booking-step" data-current="3">
						<ul class="oct-list-inline nm">

							<li class="oct_stand oct_frt active"><?php echo __("Choose Location ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_snd"> <?php echo __("Which service you would like us to provide? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_frth"> <?php echo __("To whom you want to select as service provider? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_fth"> <?php echo __("When would you like us to come? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>
							
							<li class="oct_stand oct_sth"><?php echo __("Info and Checkout","oct");?><span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_svth"><?php echo __("Done","oct");?></li>				
						</ul>
						<div class="oct-loader-first-step"></div>
					</div>
					<div class="oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 pull-left">
						<!-- <h3>3. Done</h3> -->
						<div class="common-inner">
							<div class="booking-thankyou">
								<h1 class="header1"><?php echo __("Congratulations","oct");?></h1>
								<h3 class="header3"><?php echo __("Your payment was successful.","oct");?></h3>
								<p class="thankyou-text"><?php echo __("You will be notified with details of appointment(s).","oct");?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="visible cb" id="oct_first_step" >
				<div class="oct-first-step form-inner visible" >
					<input type="hidden" name="oct_selected_location" id="oct_selected_location" value="X" />	
					<input type="hidden" name="oct_selected_service" id="oct_selected_service" value="0" />	
					<input type="hidden" name="oct_selected_staff" id="oct_selected_staff" value="0" />	
					<input type="hidden" name="oct_service_addon_st" id="oct_service_addon_st" value="D" />	
					<input type="hidden" name="oct_selected_datetime" id="oct_selected_datetime" value="0" />	
					<div class="oct-booking-step" data-current="1">

						<ul class="oct-list-inline nm">
							
							<li class="oct_stand oct_frt active"><?php echo __("Choose Location ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_snd"> <?php echo __("Which service you would like us to provide? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_frth"> <?php echo __("To whom you want to select as service provider? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_fth"> <?php echo __("When would you like us to come? ","oct");?> <span class="sep"><i class="icon-arrow-right icons"></i></span></li>
							
							<li class="oct_stand oct_sth"><?php echo __("Info and Checkout","oct");?><span class="sep"><i class="icon-arrow-right icons"></i></span></li>

							<li class="oct_stand oct_svth"><?php echo __("Done","oct");?></li>
						</ul>
						<div class="oct-loader-first-step"></div>
					</div>
					
					<div class="oct-form-common oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 pull-left">
						<div class="common-inner">
						<?php if($oct_zipcode_booking_status=='E'){ ?>						
							<div class="pr oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 np">
								<div class="oct-form-row fullwidth">
									<h3 class="block-title"><i class="icon-location-pin icons fs-20"></i><?php echo __("Where would you like us to provide service?","oct");?></h3>
									<div class="pr oct-sm-12 oct-xs-12 oct-xs-12 np">
										<div class="pr">
											<input type="text" class="custom-input" id="oct_zip_code" />
											<label class="custom"><?php echo __("Your area code or zip code","oct");?></label>
											<i class="bottom-line"></i>
										</div> 
										<span id="oct_location_error" class="oct-error"><?php echo __("Please enter area code or zip code","oct");?></span>
										<span id="oct_location_success" class="oct-success"><?php echo __("We cover your location area","oct");?></span>
									</div> 
								</div> 
							</div>	
						<?php } ?>
						<?php if($oct_mulitlocation_status=='E'){ ?>
							<div class="pr oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 np oct_booking_form_field" id="oct_chosltn" >
							<div class="oct-form-row fullwidth">
								<h3 class="block-title"><i class="icon-location-pin icons fs-15"></i> <?php echo __("Choose Location","oct");?></h3>
								
								<span id="oct_location_error" class="oct-error"><?php echo __("Please select location","oct");?></span>
								<div class="pr oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 np">
									<div id="cus-select1" class="cus-location fullwidth custom-input nmt">
										<div class="common-selection-main location-selection">
											<div class="row">
												<?php 
													foreach($locations as $location){ ?>
														<div class="one-third select_location" value="<?php echo $location->id;?>">
						                                    <h4 class="oct-box-country"><?php echo $location->location_title;?></h4>
														</div>

												<?php } ?>
												<div class="clearfix"></div>

											</div>
										</div>
									</div>
									<!-- <i class="bottom-line"></i> -->
								</div>	
							</div> 
							</div> 
						<?php } ?>
 


							
							<div class="oct-form-row fullwidth oct_booking_form_field" id="oct_likepvdr"> <!-- start select service -->
								<?php if($oct_mulitlocation_status=='E'){ ?>
									<a href="javascript:void(0)" onclick="goToStep('oct_chosltn')">< Back</a>
								<?php } ?>

								<h3 class="block-title"><i class="icon-grid icons fs-20"></i> <?php echo __("Which service you would like us to provide?","oct");?></h3>
								<span id="oct_service_error" class="oct-error oct-hide"><?php echo __("Please check service area.","oct");?></span>
								<div class="pr oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12 np">
									<div id="cus-select1" class="cus-select fullwidth custom-input nmt">
										<div class="common-selection-main service-selection">
											<!-- <div class="selected-is select-custom" title="<?php echo __("Choose Your Selection","oct");?>">
												<div class="data-list" id="selected_custom">
													<div class="oct-value"><?php echo __("Please choose service","oct");?></div>
												</div>
											</div> -->
											<div id="oct_services">												
												<?php
											if(sizeof((array)$services_categories)>0){
												?> <div class="row"> <?php
												foreach($services_categories as $services_category){			
													$oct_category->id = $services_category;
													$oct_category->readOne();
													?>
													<?php
														$oct_service->service_category = $services_category;
														$cat_services = $oct_service->readAll_category_services();
														?>
														
															<?php 
																foreach($cat_services as $cat_service){
															if(in_array($cat_service->id,$location_services)){
															 ?>
																	<div class="select_custom one-third" data-sid="<?php echo $cat_service->id;?>">
									                                    <h4 class="oct-box-country"><?php echo $cat_service->service_title; ?></h4>
																	</div>

															<?php } }?>
															<div class="clearfix"></div>

														</div>
														<?php
															
														}
														?> </div> <?php
												  
												}else{ ?>
												<li class="data-list select_custom" data-sid="">
													<div class="oct-value" ><?php echo __("No service found for this location.","oct");?></div>
												</li>
												<?php } ?>															
											</div>
										</div>
									</div>
									<i class="bottom-line"></i>
								</div> 
								
								
								<div id="oct_service_detail" class="pr oct-sm-12 oct-xs-12 np oct-hide service-details">
								</div>
							</div>  <!-- end select service -->
							
							<!-- Service Addons Container -->

							<div id="oct_service_addons" class="oct-form-row fullwidth oct-hide"></div> 
							<!-- End Service Addons Container -->
							
							
							
							<div class="oct-form-row fullwidth oct_booking_form_field" id="oct_srvdr"> <!-- Select staff start -->
								<a href="javascript:void(0)" onclick="goToStep('oct_likepvdr')">< Back</a>
								<h3 class="block-title"><i class="icon-user icons fs-20"></i><?php echo __("To whom you want to select as service provider?","oct");?></h3>
								<div class="pr oct-sm-12 oct-xs-12 np" id="oct_staff_info">
									<?php if($provider_avatar_view=='E'){ ?>
									<div class="oct-service-staff-list oct-common-box">
										<ul class="staff-list fullwidth np">
											<?php 
											if(sizeof((array)$octstaffs)>0){
												$uplodpathinfo = wp_upload_dir();
												foreach($octstaffs as $octstaff){ 
												$staffimagepath = plugins_url( 'assets/images/provider/staff.png',dirname(__FILE__));
												if(isset($octstaff['image']) && $octstaff['image']!=''){	
													$staffimagepath = $uplodpathinfo['baseurl'].$octstaff['image'];			
												}
																					
												?>
												<li data-staffid="<?php echo $octstaff['id'];?>" class="oct-staff-box oct-sm-6 oct-md-3 oct-lg-3 oct-xs-12 mb-15">
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
											<?php }	?>
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
												if(sizeof((array)$octstaffs)>0){
													foreach($octstaffs as $octstaff){ ?>
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
									<?php } ?>									
								</div>
							</div>
							<div class="oct-form-row fullwidth oct_booking_form_field" id="oct_ext">
								<span id="oct_staff_error" class="oct-error"><?php echo __("Please choose service provider","oct");?></span> 
							</div> <!-- Select staff end -->
							
							<div class="oct-form-row fullwidth oct_booking_form_field"  id="oct_clndr"><!-- Calendar start -->	
								<a href="javascript:void(0)" onclick="goToStep('oct_srvdr')">< Back</a>
								<h3 class="block-title"><i claoct_booking_form_fieldss="icon-calendar icons fs-20"></i><?php echo __("When would you like us to come?","oct");?></h3>
								<?php 		
								$month= date_i18n('m');
								$year= date_i18n('Y');
								$currentdate = mktime(12, 0, 0,$month, 1,$year);	
								/* $currentdate = strtotime(date_i18n('Y-m-d')); */
								$calnextmonth = strtotime('+1 month',$currentdate);
								$calprevmonth=strtotime('-1 month', $currentdate);
								$oct_maxadvance_booktime = get_option('octabook_maximum_advance_booking');
								$calmaxdate = strtotime('+'.$oct_maxadvance_booktime.' month',$currentdate);	
								$monthdays = date_i18n("t", $currentdate);
								$offset = date_i18n("w", $currentdate) - get_option('start_of_week');
								$rows = 1;		
								$in_offset = 0;
								$prevmonthlink =  strtotime(date("Y-m-d",$currentdate));
								$currrmonthlink =  strtotime(date("Y-m-d"));
								?>					
								
								<div class="pr oct-sm-12 oct-xs-12 oct-datetime-seleoct-main np">
									<div class="oct-datetime-select">
										<div class="calendar-wrapper">
											<div class="calendar-header">
												<?php if($currrmonthlink < $prevmonthlink){ ?>
												<a class="previous-date oct_month_change" data-curmonth="<?php echo date_i18n('m');?>" data-curyear="<?php echo date_i18n('Y');?>"  data-calyear="<?php echo date("Y", $calprevmonth); ?>" data-calmonth="<?php echo date("m", $calprevmonth); ?>" data-calaction="prev" href="javascript:void(0)"><i class="icon-arrow-left icons"></i></a>
												<?php }else{ ?>
													<a data-curmonth="<?php echo date_i18n('m');?>" data-curyear="<?php echo date_i18n('Y');?>" class="previous-date" href="javascript:void(0)"><i class="icon-arrow-left icons"></i></a>
												<?php } ?>
												<div class="calendar-title"><?php echo date_i18n('F'); ?></div>		
												<div class="calendar-year"><?php echo date_i18n('Y'); ?></div>
												
												<?php
												if(date('M',$calmaxdate) == date('M',$currentdate) && date('Y',$calmaxdate) == date('Y',$currentdate)){ ?>
												<a class="next-date" href="javascript:void(0)"><i class="icon-arrow-right icons"></i></a>
												<?php }else{?>
												<a class="next-date oct_month_change" data-calyear="<?php echo date("Y", $calnextmonth); ?>" data-calmonth="<?php echo date("m", $calnextmonth); ?>" data-calaction="next" href="javascript:void(0)"><i class="icon-arrow-right icons"></i></a>
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
												</div>
												
												<!-- end row -->
												
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
												$calcurr_date = strtotime(date_i18n('d-m-Y'));
												?>
												
												<?php
												if( ($day + $offset - 1) % 7 == 0 && $day != 1){
												   $k = $k+7;
												  ?>
												  </div> 
												  <div class="oct-week"></div> 
												  
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
												<div data-seldate="<?php echo $calsel_date;?>" data-calrowid="<?php if($day < 35){echo $k+7; }else{echo $k;} ?>"  class="oct-week <?php if($calsel_date==$calcurr_date){ echo 'by_default_today_selected';} if($calsel_date<$calcurr_date){ echo 'inactive';} ?>"><a href="javascript:void(0)"><span><?php echo $day; ?></span></a></div> 
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
								</div>								
							</div> 								
						</div><!-- Calendar end -->

                       <div class="oct-form-row fullwidth oct_booking_form_field" id="oct_tmtslt">
                       	<a href="javascript:void(0)" onclick="jQuery('.oct-show-time').hide(); goToStep('oct_clndr');">< Back</a>
						  <div class="oct-show-time">
								<div class="time-slot-container">
									<div class="oct-slot-legends">
									</div>
									<ul class="list-inline time-slot-ul oct_day_slots oct_tmtslt_ul" id="oct_tmtslt_ul">
									</ul>

								</div>
												
							</div>
						</div>





						<span id="oct_datetime_error" class="oct-error"><?php echo __("Please select date & time","oct");?></span>	
						<div class="oct-button-container text-right fullwidth oct_booking_form_field"  id="oct_nxt">
							<a class="oct-button btn-x-medium" id="btn-second-step" href="javascript:void(0)"><i class="icon-arrow-left icons oct-rtl-icon"></i><?php echo __("Next","oct");?><i class="icon-arrow-right icons oct-ltr-icon"></i></a>
						</div>
					</div>
				</div>
			</div>		
		</div>
		</section> <!-- main view content end here -->
		
		<aside id="content-sidebar" class="oct-display-middle oct-main-right oct-md-4 oct-sm-5 oct-lg-4 oct-xs-12 np pull-right <?php if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){ echo 'cart-item-sidebar'; }else{ echo 'no-cart-item-sidebar';} ?> oct_remove_right_sidebar_class">
			<div id="oct_booking_sidebar" class="content-summary" data-cartitems="<?php if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){ echo sizeof((array)$_SESSION['oct_cart_item']); }else{ echo '0';} ?>">
				<?php    
				if(isset($_SESSION['oct_cart_item']) && sizeof((array)$_SESSION['oct_cart_item'])>0){				
				$octabook_show_coupons = get_option('octabook_show_coupons');
				$oct_taxvat_status = get_option('octabook_taxvat_status');
				$oct_partial_deposit_status = get_option('octabook_partial_deposit_status');
				
				?>
				<div class="oct-sidebar-header">
					<h3 class="header3"><?php echo __("Booking Summary","oct");?><div class="oct-cart-items-count"> <i class="icon-bag icons fs-22 pull-right pr"><span class="oct_badge"><?php echo sizeof((array)$_SESSION['oct_cart_item']);?></span></i></div></h3>
				</div>
				<div id="oct_booking_summary" class="sidebar-box">					
					<?php 	
					
					foreach($_SESSION['oct_cart_item'] as $cart_item_detail){
						$cart_item = unserialize($cart_item_detail);
						
						/* POST Data Variables */	
						$locationid = $cart_item['selected_location'];
						$serviceid = $cart_item['selected_service'];
						$staffid = $cart_item['selected_staff'];
						$selected_datetime = $cart_item['selected_datetime'];
						$cartitem_id = $cart_item['id'];
						$service_addon_st = $cart_item['service_addon_status'];
						$service_addons = $cart_item['service_addons'];
						$total_price = 0;
						$service_amount = 0;
						
						
						
						/* Booking Summary HTML */
						$oct_booking_summary = '<div class="booking-list br-3 fullwidth">
						<a class="oct-delete-booking-box oct_remove_item" data-cartitemid="'.$cartitem_id.'" href="javascript:void(0)">'.__("Delete","oct").'</a>
						<div class="right-booking-details oct-md-12 oct-sm-12 oct-xs-12 np pull-left">';
						
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
						$oct_booking_summary .= '<div class="common-style fullwidth oct_booking_form_field" id="oct_extsrv">

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
					
					
					//echo $oct_booking_summary;?>
				</div>
				
				<div class="oct-button-container text-center oct-add-more-btn">
					<a class="oct-button pull-left" id="btn-more-bookings" href="javascript:void(0)"><i class="icon-arrow-left icons"></i><?php echo __("Add more","oct");?></a>					
				</div>				
				
				
				<div class="oct-checkout-content">				
					
					
					<div class="sidebar-box">	
						<div class="clear"></div>
						<div id="oct_amount_summary" class="oct-total-amount">
							<?php if(isset($_SESSION['oct_sub_total'])){ ?>
							<div class="oct-xs-12 np">
								<div class="common-amount-text"><?php echo __("Sub Total","oct"); ?></div>
								<div class="common-amount-price"><?php echo $oct_general->oct_price_format($_SESSION['oct_sub_total']); ?></div>
							</div>	
							<?php } ?>
							<?php if(isset($_SESSION['oct_coupon_discount'])){ ?>	
								<div class="oct-xs-12 np">
									<div class="common-amount-text"><?php echo __("Coupon Discount","oct"); ?></div>
										<div class="common-amount-price discount-price"><?php echo '-'.$oct_general->oct_price_format($_SESSION['oct_coupon_discount']); ?></div>
								</div>
							<?php } ?>		
							
							<div class="clear"></div>
							<?php if($oct_taxvat_status=='E' && isset($_SESSION['oct_taxvat'])){ ?>
								<div class="oct-xs-12 np">
										<div class="common-amount-text"><?php echo __("Tax Amount","oct"); ?></div>
										<div class="common-amount-price"><?php echo $oct_general->oct_price_format($_SESSION['oct_taxvat']); ?></div>
									</div>
							<?php } ?>
							<?php if(isset($_SESSION['oct_nettotal'])){ ?>	
							<div class="oct-xs-12 npl npr hr-both">
								<div class="common-amount-text total-amount"><?php echo __("Payable Amount","oct"); ?></div>
								<div class="common-amount-price total-price"><?php echo $oct_general->oct_price_format($_SESSION['oct_nettotal']); ?></div>
							</div>	
							<?php } ?>
							<div class="clear"></div>
						</div>
						
					</div>
					
					
					<?php if($octabook_show_coupons=='E'){ ?>	
					<div class="oct-discount-partial fullwidth">
						<div class="discount-coupons fullwidth">
							<?php if(!isset($_SESSION['oct_coupon_discount'])){ ?>
							<div class="oct-form-row oct-md-12 oct-lg-12 oct-sm-12 oct-xs-12">
								<div class="pr coupon-input">
										<input type="text" class="custom-input coupon-input-text" id="oct-coupon" />
										<a href="javascript:void(0);" data-action="apply" id="oct_apply_coupon" class="oct-link apply-coupon" ><?php echo __("Apply","oct");?></a>
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
											<span class="coupon-value br-2 "><?php echo $_SESSION['oct_coupon_code'];?></span>
											<i class="icon-close icons br-100" data-action="reverse" id="remove_applied_coupon"  title="Remove applied coupon" ></i>
									
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>						
					</div>
					<?php } ?>
					
					
					<?php if($oct_partial_deposit_status=='E'){ 
					$oct_partial_deposit_message = get_option('octabook_partial_deposit_message');
					?>			
					<div class="oct-discount-partial fullwidth">
						<div id="oct_partial_deposit_summary" class="partial-amount-wrapper br-2 cb">
							<div class="partial-amount-message"><?php echo $oct_partial_deposit_message; ?></div>
							<?php if(isset($_SESSION['oct_partialdeposit'])){ ?>
							<div class="oct-form-row">
								<div class="oct-xs-12 np">
									<div class="common-amount-text"><?php echo __("Partial Deposit","oct"); ?></div>
									<div class="common-amount-price "><?php echo $oct_general->oct_price_format($_SESSION['oct_partialdeposit']); ?></div>
								</div>
							</div>
							<?php } ?>
							<?php if(isset($_SESSION['oct_partialdeposit_remaining'])){ ?>
							<div class="oct-form-row">
								<div class="oct-xs-12 np">
									<div class="common-amount-text"><?php echo __("Remaining Deposit","oct"); ?></div>
									<div class="common-amount-price"><?php echo $oct_general->oct_price_format($_SESSION['oct_partialdeposit_remaining']); ?></div>
								</div>
							</div>
							<?php } ?>
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
          <!-- <form action="https://secure.payu.in/_payment" method="post" name="payuForm" id="payuForm">-->
           <form action="https://sandboxsecure.payu.in/_payment" method="post" name="payuForm" id="payuForm">
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
			?>
			<?php
			if(get_option('octabook_payment_method_Paytm') == 'E'){
				?>
				<form method="post" action="" name="oct_paytm_form" id="oct_paytm_form">
					<input type="hidden" id="oct_CHECKSUMHASH" name="CHECKSUMHASH" value="">
				</form>
				<?php
			}
			?>
				<?php 				
				}else{ ?>
					<div class="oct-sidebar-header">
						<h3 class="header3"><?php echo __("Booking Summary","oct");?><div class="oct-cart-items-count"> <i class="icon-bag icons fs-22 pull-right pr"><span class="oct_badge">0</span></i></div></h3>
					</div>	
					<h2 class="oct-empty-cart"><i class="icon-handbag icons"></i> <?php echo __("Your Cart is Empty!","oct");?></h2>
				<?php } ?>
			</div>
		</aside>
	</div>
</div>
</section>
</div>

<?php
$oct_terms_and_condition_status = 'D';
if(get_option('octabook_allow_terms_and_conditions')=='E' || get_option('octabook_allow_privacy_policy')=='E'){
	$oct_terms_and_condition_status = 'E';
}
?>

<script>
	var octmain_obj={"plugin_path":"<?php echo $plugin_url_for_ajax; ?>","location_err_msg":"<?php echo __("We are not provide service in your area zipcode","oct"); ?>","location_search_msg":"<?php echo __("Searching...","oct"); ?>","multilication_status":"<?php echo $oct_mulitlocation_status; ?>","zipwise_status":"<?php echo $oct_zipcode_booking_status; ?>","multilocation_status":"<?php echo $oct_mulitlocation_status;?>","Choose_service":"<?php echo __("Please choose service","oct"); ?>","Choose_zipcode":"<?php echo __("Please check your area zipcode","oct"); ?>","thankyou_url":"<?php echo get_option('octabook_thankyou_page'); ?>","Choose_provider":"<?php echo __("Choose service provider","oct");?>","Choose_location":"<?php echo __("Please choose location","oct");?>","oct_payment_gateways_st":"<?php echo get_option('octabook_payment_gateways_status');?>","oct_terms_and_condition_status":"<?php echo $oct_terms_and_condition_status;?>","oct_home_page_link":"<?php echo $actual_link;?>","octabook_thankyou_page_rdtime":"<?php echo $octabook_thankyou_page_rdtime;?>","plugin_url":"<?php echo plugins_url(); ?>"};
	
	var octmain_error_obj={
		"Please_Enter_Email":"<?php echo __("Please Enter Email","oct"); ?>",
		"Please_Enter_Valid_Email":"<?php echo __("Please Enter Valid Email","oct"); ?>",
		"Email_already_exist":"<?php echo __("Email already exist","oct"); ?>",
		"Please_Enter_Password":"<?php echo __("Please Enter Password","oct"); ?>",
		"Please_enter_minimum_8_Characters":"<?php echo __("Please enter minimum 8 Characters","oct"); ?>",
		"Please_enter_maximum_30_Characters":"<?php echo __("Please enter maximum 30 Characters","oct"); ?>",
		"Please_Enter_First_Name":"<?php echo __("Please Enter First Name","oct"); ?>",
		"Please_Enter_Last_Name":"<?php echo __("Please Enter Last Name","oct"); ?>",
		"Please_Enter_Phone_Number":"<?php echo __("Please Enter Phone Number","oct"); ?>",
		"Please_Enter_Valid_Phone_Number":"<?php echo __("Please Enter Valid Phone Number","oct"); ?>",
		"Please_enter_minimum_10_Characters":"<?php echo __("Please enter minimum 10 Characters","oct"); ?>",
		"Please_enter_maximum_14_Characters":"<?php echo __("Please enter maximum 14 Characters","oct"); ?>",
		"Please_Enter_Address":"<?php echo __("Please Enter Address","oct"); ?>",
		"Please_Enter_City":"<?php echo __("Please Enter City","oct"); ?>",
		"Please_Enter_State":"<?php echo __("Please Enter State","oct"); ?>",
		"Please_Enter_Notes":"<?php echo __("Please Enter Notes","oct"); ?>",
		"Please_Enter":"<?php echo __("Please Enter","oct"); ?>"
	};
</script>