<?php
/*
Plugin Name: OctaBook
Plugin URI: http://skymoonlabs.com/
Description: OctaBook is an online appointment booking wordpress plugin, your website visitor can see the available time for service provider and can book their appointment instantly,due its shopping cart feature one user can book multiple appointments at once. you can use this shortcode for booking page in frontend [octabook].
Version: 2.6
Author: Skymoonlabs
Author URI: http://skymoonlabs.com/
*/

	add_action('init', 'octabook_init');
	add_action( 'admin_enqueue_scripts', 'octabook_admin_scripts');
	add_action('admin_menu','octabook_admin_menu');
	add_filter('wp_head', 'viewport_meta_octabook');
	
	/* lower letters Capital Shortcode */
	add_shortcode('octabook','oct_front');
	add_shortcode('"octabook"','oct_front');
	add_shortcode("'octabook'",'oct_front');
	/* Capital letters Capital Shortcode */
	add_shortcode('OctaBook','oct_front');
	add_shortcode('"OctaBook"','oct_front');
	add_shortcode("'OctaBook'",'oct_front');

	/* Customer Forntend Dashboard Area Shortcode */
	add_shortcode('octabook_client_appointments','octabook_client_frontend');
	add_shortcode("'octabook_client_appointments'",'octabook_client_frontend');
	add_shortcode('"octabook_client_appointments"','octabook_client_frontend');
	/* Multi Language */
	add_action( 'plugins_loaded', 'octabook_load_textdomain' );
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'oct_action_links' );
	
	add_action('wp_ajax_check_username_bd','check_username_oct_callback');
	add_action('wp_ajax_check_email_bd','check_email_oct_callback');
	add_action('wp_ajax_check_generatecoupon_bd','check_generatecoupon_oct_callback');
	add_action( 'wp_ajax_nopriv_check_username_bd', 'check_username_oct_callback' );
	add_action( 'wp_ajax_nopriv_check_email_bd', 'check_email_oct_callback' );

	


	/* function view port meta in case its not defined */
	function viewport_meta_octabook() { ?>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
	<?php }
	
	/* set plugin textdomain */
	function octabook_load_textdomain() {
		$locale = apply_filters('plugin_locale', get_locale(),'oct');
		load_textdomain('oct', WP_LANG_DIR.'/oct-'.$locale.'.mo');
		load_plugin_textdomain( 'oct', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
	}

	
	/* plugin settings link */
	function oct_action_links( $links ) {
	  // $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=settings_submenu') .'">Settings</a>';
	   return $links;
	}
	/* octabook Admin Menu Icon */
	function octabook_adminmenu_icon(){
		echo '<style>li#toplevel_page_octabook_menu .dashicons-admin-generic:before {
			content: "" !important;	background: url("'.plugins_url("assets/images/menu.png",__FILE__).'")			no-repeat;		position: relative;	top: 7px;}			
			li#toplevel_page_verify .dashicons-admin-generic:before {
			content: "" !important;	background: url("'.plugins_url("assets/images/menu.png",__FILE__).'") no-repeat;		position: relative;	top: 7px;}
			li#toplevel_page_provider_submenu .dashicons-admin-generic:before {
			content: "" !important;	background: url("'.plugins_url("assets/images/menu.png",__FILE__).'") no-repeat;		position: relative;	top: 7px;}</style>';
	}
	
   /* Plugin init function 
   */	
   function octabook_init(){	 		 
   global $wpdb;	  	
		/* Check plugin updtes */
		include_once('objects/class_autoupdate.php');
		$wptuts_plugin_current_version = '2.6';
		$wptuts_plugin_remote_path = 'http://skymoonlabs.com/octabook/update.php?cv='.$wptuts_plugin_current_version;
		$wptuts_plugin_slug = plugin_basename(__FILE__);
		new octabook_auto_update ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
		/* Load octabook Admin Menu Icon */
		add_action( 'admin_print_scripts','octabook_adminmenu_icon');		
		
		$host =  $_SERVER['HTTP_HOST'];		 
		$host_uri = $_SERVER['REQUEST_URI'];		 
		$cur_rul= $host.$host_uri;		 		 
		if(isset($_SESSION['booking_home']) and $_SESSION['booking_home']!=''){			
			$redirect_url = $_SESSION['booking_home'];		 
		} else {			
			$redirect_url = site_url();		 
		}		 		 
	   
	  if(get_option('octabook_thankyou_page')==$cur_rul  || is_numeric(strpos($cur_rul,'oct-thankyou'))){
			$octabook_thankyou_page_rdtime = get_option("octabook_thankyou_page_rdtime");
			ob_start();
			echo '<script>setTimeout(function(){ window.location = "'.$redirect_url.'"; }, '.$octabook_thankyou_page_rdtime.');</script>';		 
	  }
		
		/* Thankyou page creation */
		
		$the_page_title = 'Thank you';
		$the_page_name = 'oct-thankyou';

		$the_page = get_page_by_title( $the_page_title );

		if ( ! $the_page ) {
			 /* Create post object */
			 $_p = array();
			 $_p['post_title'] = $the_page_title;
			 $_p['post_name'] = $the_page_name;
			 $_p['post_content'] = "
			 <div class='th-wrapper'>
				<div class='th-div'>
				<span style='display:block;'>Thankyou! for booking appointment.<br/>You will be notified by email with details of appointment(s).</span><br/><br/>					
				</div>
			 </div>
			 ";
			 $_p['post_status'] = 'publish';
			 $_p['post_type'] = 'page';
			 $_p['comment_status'] = 'closed';
			 $_p['ping_status'] = 'closed';
			 $_p['post_category'] = array(1); /* the default 'Uncatrgorised' */

			 /* Insert the post into the database */
			 $the_page_id = wp_insert_post( $_p );
		}
			
			/* Thankyou page creation */
	$the_page_title = 'Bookings';
	$the_page_name = 'oct-bookings';
	$the_page = get_page_by_title( $the_page_title );
	if ( ! $the_page ) {
		/* Create post object */
		$_p = array();
		$_p['post_title'] = $the_page_title;
		$_p['post_name'] = $the_page_name;
		$_p['post_content'] = "<div class='th-wrapper'>
		<div class='th-div'>
		[octabook_client_appointments]
		</div>
		</div>";
		$_p['post_status'] = 'publish';
		$_p['post_type'] = 'page';
		$_p['comment_status'] = 'closed';
		$_p['ping_status'] = 'closed';
		$_p['post_category'] = array(1); /* the default 'Uncatrgorised' */

		/* Insert the post into the database */
		$the_page_id = wp_insert_post( $_p );
	}
			

			/* add data base tables */
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
	
				/* include all objects files while loding */
				 include_once('objects/class_update.php');
				 include_once('objects/class_general.php');
				 include_once('objects/class_location.php');
				 include_once('objects/class_category.php');
				 include_once('objects/class_service.php');
				 include_once('objects/class_schedule.php');
				 include_once('objects/class_schedule_breaks.php');
				 include_once('objects/class_schedule_dayoffs.php');
				 include_once('objects/class_provider.php');
				 include_once('objects/class_settings.php');
				 include_once('objects/class_email_templates.php');
				 include_once('objects/class_sms_templates.php');
				 include_once('objects/class_booking.php');
				 include_once('objects/class_order.php');
				 include_once('objects/class_front_octabook_first_step.php');
				 include_once('objects/class_clients.php');
				 include_once('objects/class_payments.php');
				 include_once('objects/class_image_upload.php');
				 include_once('objects/class_reviews.php');
				 include_once('objects/class_coupons.php');
				 include_once('objects/class_email_template_settings.php');
				 include_once('objects/class_service_schedule_price.php');
				 include_once('objects/class_loyalty_points.php');
			
			
				/* Set default settings options via class constructor */
				$general = new octabook_general();
				$location = new octabook_location();
				$settings = new octabook_settings(); 			
				$email_templates = new octabook_email_template(); 
				$sms_templates = new octabook_sms_template();
				$service = new octabook_service();
				$category = new octabook_category();
				$coupon = new octabook_coupons();
				$bookings = new octabook_booking();
				$order_client_info = new octabook_order();
				$payments = new octabook_payments();
				$schedule_offdays = new octabook_schedule_offdays();
				$schedule = new octabook_schedule();
				$schedule_breaks = new octabook_schedule_breaks();
				$ssp = new octabook_service_schedule_price();
				$reviews = new octabook_reviews();
				$loyalty_points = new octabook_loyalty_points();
				$staff = new octabook_staff();
			
				
				
				$service_table_create = $service->create_table();
				$location_table_create = $location->create_table();
				$provider_service_table_create = $service->create_table_provider_service();
				$addons_booking_table_create = $service->create_table_addons_booking();
				$addons_service_table_create = $service->create_table_addons();
				$addon_pricing_table_create = $service->create_table_addon_pricing();
				$category_table_create = $category->create_table();
				$coupon_table_create = $coupon->create_table();
				$email_templates_table_create = $email_templates->create_table();
				$sms_templates_table_create = $sms_templates->create_table();
				$bookings_table_create = $bookings->create_table();
				$client_orderinfo_table_create = $order_client_info->create_table();
				$payments_table_create = $payments->create_table();
				$schedule_offdays_table_create = $schedule_offdays->create_table();
				$schedule_table_create = $schedule->create_table();
				$schedule_breaks_table_create = $schedule_breaks->create_table();
				$schedule_offtimes_table_create = $schedule_breaks->create_table_offtimes();
				$staff_gc_table_create = $staff->create_staff_gc();
				$ssp_table_create = $ssp->create_table();
				$reviews_table_create = $reviews->create_table();
				$loyalty_points_table_create = $loyalty_points->create_table();
				//$email_template_settings = new octabook_email_template_settings();				
				$tablecreation=array($service_table_create,$provider_service_table_create,$category_table_create,$coupon_table_create,$email_templates_table_create,$sms_templates_table_create,$bookings_table_create,$client_orderinfo_table_create,$payments_table_create,$schedule_offdays_table_create,$schedule_table_create,$schedule_breaks_table_create,$location_table_create,$schedule_offtimes_table_create,$ssp_table_create,$reviews_table_create,$loyalty_points_table_create,$addons_booking_table_create,$addons_service_table_create,$addon_pricing_table_create,$staff_gc_table_create);
				if ( is_multisite()) {
	
						$current_blog = $wpdb->blogid;

						$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
						for($i=0;$i<sizeof((array)$tablecreation);$i++){
						foreach ( $blog_ids as $blog_id) {
							switch_to_blog( $blog_id );
							$tablecreation[$i];
							restore_current_blog();
							}
						}
					} 
					
				$email_templates_create = $email_templates->octabook_create_email_template();
				$sms_templates_create   = $sms_templates->octabook_create_sms_template();
				$oct_update = new octabook_update();  /* update constructor */
	}
	
	/* Username checking  */	
	function check_username_oct_callback(){
	global $wpdb;

	if (validate_username($_POST['username']) && username_exists($_POST['username']) == Null && ctype_alnum($_POST['username']) ) {
    echo json_encode("true");
	} else {
		echo json_encode("Username is already exists or not alphanumeric.");
	}die();

	}	

	
	/* email checking  */	
	function check_email_oct_callback(){
	global $wpdb;
	if(isset($_POST['add_provider']) && $_POST['add_provider']=='yes'){
	$admin_email=get_option('admin_email');
	$cmp_result = strcmp($admin_email,$_POST['email']);

	if($cmp_result==0){ echo "true"; }else{
		if (!email_exists($_POST['email'])) {
		echo "true";
		} else {
			echo json_encode("Email is already exists.");
		}die();
	}
	}else{
			if (!email_exists($_POST['email'])) {
			echo "true";
			} else {
				echo json_encode("Email is already exists.");
			}die();
	}

	}
	/* Coupon checking  */	
	function check_generatecoupon_oct_callback(){
		global $wpdb;
		$coupon = new octabook_coupons();
			if(isset($_POST['update_coupon_code'],$_POST['oct_coupon_code'])){	
				if($_POST['update_coupon_code']!='ongenration'){
					if($_POST['oct_coupon_code']!=$_POST['update_coupon_code']){
							$coupon->coupon_code=$_POST['oct_coupon_code'];
							$coupon_info = $coupon->readOne();
						if(sizeof((array)$coupon_info)>0){
							echo json_encode("Coupon code already exists.");
						}else{
							echo "true";
						}
						}else{
						echo "true";
						}
					}else{
						$coupon->coupon_code=$_POST['oct_coupon_code'];
							$coupon_info = $coupon->readOne();
							if(sizeof((array)$coupon_info)>0){
							echo json_encode("Coupon code already exists.");
					}else{
						echo "true";
					}
				}
			}
		die();

	}

	 
	/* Admin Menu  */	
	function octabook_admin_menu(){
		/* WooCommerce condition */
		if ( class_exists( 'WooCommerce' )) {
			$cuser = wp_get_current_user();
			$cuser->add_cap('view_admin_dashboard');
		}
		if(current_user_can('oct_client') && !current_user_can('oct_provider') && !current_user_can('manage_options')) {
			
			add_menu_page('octabook','OctaBook', 'oct_client','octabook_menu','oct_current_user_bookings','','80.01');
			
		} else {
			if(current_user_can('manage_options')) {
			add_menu_page('octabook','OctaBook', 'manage_options','octabook_menu','octabook_settings_page','','80.01');
			}elseif(current_user_can('oct_manager')){
			add_menu_page('octabook','OctaBook', 'oct_manager','octabook_menu','octabook_settings_page','','80.01');
			}else{
			add_menu_page('octabook','OctaBook', 'oct_staff','provider_submenu','oct_provider','','80.001');
			}
		}
		/* adding submenu */
		if(current_user_can('oct_staff')) {		
			add_submenu_page(null,'Calender','Calender','oct_staff','appointments_submenu','oct_appointments');
			add_submenu_page(null,'Provider','Provider','oct_staff','provider_submenu','oct_provider');
			add_submenu_page(null,'Google Calender','Google Calender','oct_staff','google_calander_submenu','oct_calander');
			if(current_user_can('oct_manager')){
			add_submenu_page(null,'Dashboard','Dashboard','oct_manager','dashboard_submenu','oct_dashboard');
			add_submenu_page(null,'Provider','Provider','oct_manager','provider_submenu','oct_provider');
			add_submenu_page(null,'Services','Services','oct_manager','services_submenu','oct_services');
			add_submenu_page(null,'Service Addons','Service Addons','oct_manager','service_addons','oct_service_addons');
			add_submenu_page(null,'Payments','Payments','oct_manager','payments_submenu','oct_payments');
			add_submenu_page(null,'Clients','Clients','oct_manager','clients_submenu','oct_clients');	
			add_submenu_page(null,'Export','Export','oct_manager','export_submenu','oct_export');
			add_submenu_page(null,'Reviews','Reviews','oct_manager','reviews_submenu','oct_reviews');
			}

			
		}else{
		add_submenu_page(null,'Calender','Calender','manage_options','appointments_submenu','oct_appointments');
		add_submenu_page(null,'Locations','Locations','manage_options','location_submenu','oct_locations');
		add_submenu_page(null,'Dashboard','Dashboard','manage_options','dashboard_submenu','oct_dashboard');
		add_submenu_page(null,'Provider','Provider','manage_options','provider_submenu','oct_provider');
		add_submenu_page(null,'Services','Services','manage_options','services_submenu','oct_services');
		add_submenu_page(null,'Service Addons','Service Addons','manage_options','service_addons','oct_service_addons');
		add_submenu_page(null,'Payments','Payments','manage_options','payments_submenu','oct_payments');
		add_submenu_page(null,'Settings','Settings','manage_options','settings_submenu','oct_settings');	
		add_submenu_page(null,'Clients','Clients','manage_options','clients_submenu','oct_clients');	
		add_submenu_page(null,'Export','Export','manage_options','export_submenu','oct_export');
		add_submenu_page(null,'Reviews','Reviews','manage_options','reviews_submenu','oct_reviews');
		add_submenu_page(null,'Whats_New','Whats_New','manage_options','whats_new_submenu','oct_whats_new');
		add_submenu_page(null,'Forntend_Shortcode','Forntend_Shortcode','manage_options','frontend_shortcode_submenu','oct_front_shortcode');
		}
	}
	
	/* Admin Menu functions */
	function octabook_settings_page(){ include_once 'admin/dashboard.php';}
	function oct_provider(){ include_once 'admin/staff.php';	}
	function oct_appointments(){	include_once 'admin/calendar.php';	}
	function oct_calander(){	include_once 'admin/staff_google_calander.php';	}
	function oct_dashboard(){	include_once 'admin/dashboard.php';	}
	function oct_locations(){	include_once 'admin/locations.php';	}	
	function oct_services(){	include_once 'admin/services.php'; }
	function oct_service_addons(){include_once 'admin/service_addons.php'; }
	function oct_settings(){	include_once 'admin/general_settings.php';}	
	function oct_payments(){	include_once 'admin/payments.php';}
	function oct_clients(){include_once 'admin/clients.php';	}	
	function oct_guest_clients(){include_once 'admin/list_guest_client.php'; }
	function oct_export(){include_once 'admin/export.php';}
	function oct_current_user_bookings() { include_once 'admin/client_dashboard.php';}
	function oct_invoice() {  include_once 'admin/download_invoice.php';}	
	function oct_sp_settings(){include_once 'admin/service_provider_settings.php';}
	function oct_reviews(){include_once 'admin/reviews.php';}
	function oct_whats_new(){include_once 'admin/octabook-welcome.php';}
	function oct_front_shortcode(){include_once 'admin/front_shortcode.php';}


	
	/* Shortcode Function */
	function oct_front(){
			
			wp_enqueue_script('jquery');
			
			wp_register_style('oct_frontend', plugins_url('assets/oct-frontend.css', __FILE__) );	
			wp_register_style('oct_responsive', plugins_url('assets/oct-responsive.css', __FILE__) );	
			wp_register_style('oct_common', plugins_url('assets/oct-common.css', __FILE__) );	
			wp_register_style('oct_reset_min', plugins_url('assets/oct-reset.min.css', __FILE__) );	
			wp_register_style('oct_jquery_ui_min', plugins_url('assets/jquery-ui.min.css', __FILE__) );	
			wp_register_style('oct_intlTelInput', plugins_url('assets/intlTelInput.css', __FILE__) );
			wp_register_style('oct_tooltipster', plugins_url('assets/tooltipster.bundle.min.css', __FILE__) );	
			wp_register_style('oct_tooltipster_sideTip_shadow', plugins_url('assets/tooltipster-sideTip-shadow.min.css', __FILE__) );
			wp_register_style('oct_simple_line_icons', plugins_url('assets/line-icons/simple-line-icons.css', __FILE__) );
			
			if(is_rtl()){
				wp_register_style('oct_rtl_css', plugins_url('assets/oct-rtl.css', __FILE__) );
				wp_enqueue_style('oct_rtl_css');
			}
			
			wp_enqueue_style('oct_frontend');
			wp_enqueue_style('oct_responsive');
			wp_enqueue_style('oct_common');
			wp_enqueue_style('oct_reset_min');
			wp_enqueue_style('oct_jquery_ui_min');
			wp_enqueue_style('oct_intlTelInput');
			wp_enqueue_style('oct_tooltipster');
			wp_enqueue_style('oct_tooltipster_sideTip_shadow');
			wp_enqueue_style('oct_simple_line_icons');
		
			wp_register_script('octabook_validate_js',plugins_url('assets/js/jquery.validate.min.js',  __FILE__) );
			wp_register_script('octabook_jquery_ui_js',plugins_url('assets/js/jquery-ui.min.js',  __FILE__) );
			wp_register_script('octabook_intlTelInput_js',plugins_url('assets/js/intlTelInput.js',  __FILE__) );
			wp_register_script('octabook_tooltipster_js',plugins_url('assets/js/tooltipster.bundle.min.js',  __FILE__) );
			wp_register_script('octabook_payment_js',plugins_url('assets/js/jquery.payment.min.js',  __FILE__) );
			wp_register_script('octabook_common_front_js',plugins_url('assets/js/common-front.js',  __FILE__) );
			
			wp_enqueue_script('octabook_validate_js');	
			wp_enqueue_script('octabook_jquery_ui_js');	
			wp_enqueue_script('octabook_intlTelInput_js');	
			wp_enqueue_script('octabook_tooltipster_js');	
			wp_enqueue_script('octabook_payment_js');	
			wp_enqueue_script('octabook_common_front_js');	
			include_once 'frontend/oct_firstep.php';
			$output = ob_get_clean();
			return $output;
		}
	
	 /* octabook Client Forntend Login And Appointment Section */
		function octabook_client_frontend(){

			wp_enqueue_script('jquery');
			wp_register_script('octabook_main_js',plugins_url('assets/js/oct-client-dashboard-front.js',  __FILE__) );
			wp_register_script('bootstrap_clientDB_min',plugins_url('assets/js/bootstrap.min.js',  __FILE__) );
						
			wp_register_script('dataTables_responsive_clientDB_min',plugins_url('assets/js/datatable/dataTables.responsive.min.js',  __FILE__) );
			
			
			wp_register_script('jquery_dataTables_clientDB_min',plugins_url( '/assets/js/datatable/jquery.dataTables.min.js',  __FILE__) );
			wp_register_script('dataTables_bootstrap_clientDB_min',plugins_url( '/assets/js/datatable/dataTables.bootstrap.min.js',  __FILE__) );
			wp_register_script('dataTables_buttons_clientDB_min',plugins_url( '/assets/js/datatable/dataTables.buttons.min.js',  __FILE__) );
			wp_register_script('jszip_clientDB_min',plugins_url( '/assets/js/datatable/jszip.min.js',  __FILE__) );
			wp_register_script('pdfmake_clientDB_min',plugins_url('/assets/js/datatable/pdfmake.min.js',  __FILE__) );
			wp_register_script('vfs_clientDB_fonts',plugins_url( '/assets/js/datatable/vfs_fonts.js',  __FILE__) );
			wp_register_script('buttons_html5_clientDB_min',plugins_url( '/assets/js/datatable/buttons.html5.min.js',  __FILE__) );
			
			
			
			
			wp_enqueue_script('octabook_main_js' );	
			wp_enqueue_script('bootstrap_clientDB_min' );	
			wp_enqueue_script('jquery_dataTables_clientDB_min' );	
			wp_enqueue_script('dataTables_responsive_clientDB_min' );	
			wp_enqueue_script('dataTables_bootstrap_clientDB_min' );
			wp_enqueue_script('dataTables_buttons_clientDB_min' );
			wp_enqueue_script('jszip_clientDB_min' );
			wp_enqueue_script('pdfmake_clientDB_min' );
			wp_enqueue_script('vfs_clientDB_fonts' );
			wp_enqueue_script('buttons_html5_clientDB_min' );
			
			wp_register_style('octabook_main_client_frontend', plugins_url('assets/oct-client-dashboard-front.css', __FILE__) );	
					
			wp_register_style('octabook_main_client_simple_line_icons', plugins_url('assets/line-icons/simple-line-icons.css', __FILE__) );
			wp_register_style('bootstarp_clientDB_min_css', plugins_url('assets/bootstrap/bootstrap.min.css', __FILE__) );
			wp_register_style('octabook_main_client_reset_min', plugins_url('assets/oct-reset.min.css', __FILE__) );
			
			
			wp_register_style('jquery_dataTables_clientDB_min', plugins_url('assets/jquery.dataTables.min.css', __FILE__) );
			wp_register_style('responsive_dataTables_clientDB_min', plugins_url('assets/responsive.dataTables.min.css', __FILE__) );
			wp_register_style('dataTables_bootstrap_clientDB_min', plugins_url('assets/dataTables.bootstrap.min.css', __FILE__) );
			wp_register_style('buttons_dataTables_clientDB_min', plugins_url('assets/buttons.dataTables.min.css', __FILE__) );
			if(is_rtl()){
				wp_register_style('oct_rtl_css', plugins_url('assets/oct-rtl.css', __FILE__) );
				wp_enqueue_style('oct_rtl_css');
			}			
			wp_enqueue_style('octabook_main_client_frontend' );
			wp_enqueue_style('octabook_main_client_simple_line_icons' );
			wp_enqueue_style('octabook_main_client_reset_min' );
			wp_enqueue_style('jquery_dataTables_clientDB_min' );
			wp_enqueue_style('responsive_dataTables_clientDB_min' );
			wp_enqueue_style('dataTables_bootstrap_clientDB_min' );
			wp_enqueue_style('buttons_dataTables_clientDB_min' );
			wp_enqueue_style('bootstarp_clientDB_min_css' );
			
			
			include_once 'admin/client_dashboard_front.php';
		}

	
	/* style n scripts for octabook admin panel */
	function octabook_admin_scripts($hook) {
		ob_start();
		global $submenu;
		global $wp_styles;
		$parent='';
		$oct_pages = array();	 
		if ( (is_array( $submenu ) && isset( $submenu[$parent] )) || $hook=='toplevel_page_octabook_menu' ) {
			$oct_pages[] = 'toplevel_page_octabook_menu';
			$oct_pages[] = 'toplevel_page_provider_submenu';
			$oct_pages[] = 'toplevel_page_verify';
			if(!empty($submenu)){
				foreach ($submenu[$parent] as $item) {	$oct_pages[] = 'admin_page_'.$item[2];}
			}
		}
		if( !in_array($hook,$oct_pages) )
		return;
		
		
		$octabook_plugin_url = plugins_url('',  __FILE__);
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_register_script('jquery_ui_min',$octabook_plugin_url . '/assets/js/jquery-ui.min.js','','',true);
		wp_register_script('octabook_validate_js',plugins_url('assets/js/jquery.validate.min.js',  __FILE__) ,'','',true);
		wp_register_script('moment_min',$octabook_plugin_url . '/assets/js/moment.min.js','','',true);
		wp_register_script('oct_common_admin_jquery',$octabook_plugin_url . '/assets/js/oct-common-admin-jquery.js','','',true);
		wp_register_script('form_builder_min',$octabook_plugin_url . '/assets/js/form-builder.min.js','','',true);
		wp_register_script('form_render_min',$octabook_plugin_url . '/assets/js/form-render.min.js','','',true);
		wp_register_script('intlTelInput',$octabook_plugin_url . '/assets/js/intlTelInput.js','','',true);
		wp_register_script('bootstrap_min',$octabook_plugin_url . '/assets/js/bootstrap.min.js','','',true);
		wp_register_script('bootstrap_toggle_min',$octabook_plugin_url . '/assets/js/bootstrap-toggle.min.js','','',true);
		wp_register_script('bootstrap_select_min',$octabook_plugin_url . '/assets/js/bootstrap-select.min.js','','',true);
		wp_register_script('bootstrap_daterangepicker_js',$octabook_plugin_url . '/assets/js/daterangepicker.js','','',true);
		wp_register_script('chart',$octabook_plugin_url . '/assets/js/Chart.js','','',true);
		wp_register_script('canvas',$octabook_plugin_url . '/assets/js/canvasjs.min.js','','',true);
		wp_register_script('jquery_minicolors_min',$octabook_plugin_url . '/assets/js/jquery.minicolors.min.js','','',true);
		wp_register_script('jquery_jcrop',$octabook_plugin_url . '/assets/js/jquery.Jcrop.min.js','','',true);
		wp_register_script('jquery_dataTables_min',$octabook_plugin_url . '/assets/js/datatable/jquery.dataTables.min.js','','',true);
		wp_register_script('dataTables_responsive_min',$octabook_plugin_url . '/assets/js/datatable/dataTables.responsive.min.js','','',true);
		wp_register_script('dataTables_bootstrap_min',$octabook_plugin_url . '/assets/js/datatable/dataTables.bootstrap.min.js','','',true);
		wp_register_script('dataTables_buttons_min',$octabook_plugin_url . '/assets/js/datatable/dataTables.buttons.min.js','','',true);
		wp_register_script('jszip_min',$octabook_plugin_url . '/assets/js/datatable/jszip.min.js','','',true);
		wp_register_script('pdfmake_min',$octabook_plugin_url . '/assets/js/datatable/pdfmake.min.js','','',true);
		wp_register_script('vfs_fonts',$octabook_plugin_url . '/assets/js/datatable/vfs_fonts.js','','',true);
		wp_register_script('buttons_html5_min',$octabook_plugin_url . '/assets/js/datatable/buttons.html5.min.js','','',true);
		wp_register_script('bootstrap_editable_min',$octabook_plugin_url . '/assets/js/bootstrap-editable.min.js','','',true);
		// wp_register_script('pace_min',$octabook_plugin_url . '/assets/js/pace.min.js','','',true);
		wp_register_script('fontawesome_min',$octabook_plugin_url . '/assets/font-awesome/js/fontawesome.min.js','','',true);
		wp_register_script('fontawesome_all_min',$octabook_plugin_url . '/assets/font-awesome/js/all.min.js','','',true);

		
		if(is_rtl()){
			wp_register_style('oct_admin_rtl_bootstrap_css', plugins_url('assets/bootstrap/bootstrap-rtl.min.css', __FILE__) );
			wp_register_style('oct_admin_rtl_responsive_css', plugins_url('assets/oct-admin-rtl-responsive.css', __FILE__) );
			wp_enqueue_style('oct_admin_rtl_bootstrap_css');
			wp_enqueue_style('oct_admin_rtl_responsive_css');
			wp_register_style('oct_admin_rtl_css', plugins_url('assets/oct-admin-rtl.css', __FILE__) );
			wp_enqueue_style('oct_admin_rtl_css');
			wp_register_style('oct_main_rtl_css', plugins_url('assets/rtl.css', __FILE__) );
			wp_enqueue_style('oct_main_rtl_css');
		}
		wp_enqueue_script('jquery_ui_min');
		wp_enqueue_script('moment_min');
		wp_enqueue_script('octabook_validate_js');		
		wp_enqueue_script('form_builder_min');
		wp_enqueue_script('form_render_min');
		wp_enqueue_script('intlTelInput');
		wp_enqueue_script('bootstrap_min');
		wp_enqueue_script('bootstrap_toggle_min');
		wp_enqueue_script('bootstrap_select_min');
		wp_enqueue_script('bootstrap_daterangepicker_js');
		wp_enqueue_script('chart');
		wp_enqueue_script('canvas');
		wp_enqueue_script('jquery_minicolors_min');
		wp_enqueue_script('jquery_jcrop');
		wp_enqueue_script('jquery_dataTables_min');
		wp_enqueue_script('dataTables_responsive_min');
		wp_enqueue_script('dataTables_bootstrap_min');
		wp_enqueue_script('dataTables_buttons_min');
		wp_enqueue_script('jszip_min');
		wp_enqueue_script('pdfmake_min');
		wp_enqueue_script('vfs_fonts');
		wp_enqueue_script('buttons_html5_min');
		wp_enqueue_script('bootstrap_editable_min');
		wp_enqueue_script('oct_common_admin_jquery');
		wp_enqueue_script('pace_min');
		wp_enqueue_script('fontawesome_min');
		wp_enqueue_script('fontawesome_all_min');
		
		$lang_full_name=get_locale();
		$lang = "";
		
		if($lang_full_name == "af"){
			$lang = "af";
		}else if($lang_full_name == "ar" || $lang_full_name == "ary"){
			$lang = "ar";
		}else if($lang_full_name == "as"){
			$lang = "ar-sa";
		}else if($lang_full_name == "az" || $lang_full_name == "azb"){
			$lang = "az";
		}else if($lang_full_name == "bel"){
			$lang = "be";
		}else if($lang_full_name == "bg_BG"){
			$lang = "bg";
		}else if($lang_full_name == "bn_BD"){
			$lang = "bn";
		}else if($lang_full_name == "bo"){
			$lang = "bo";
		}else if($lang_full_name == "bs_BA"){
			$lang = "bs";
		}else if($lang_full_name == "ca"){
			$lang = "ca";
		}else if($lang_full_name == "cs_CZ"){
			$lang = "cs";
		}else if($lang_full_name == "cy"){
			$lang = "cy";
		}else if($lang_full_name == "da_DK"){
			$lang = "da";
		}else if($lang_full_name == "de_DE_formal" || $lang_full_name == "de_DE" || $lang_full_name == "de_CH" || $lang_full_name == "de_CH_informal"){
			$lang = "de";
		}else if($lang_full_name == "el"){
			$lang = "el";
		}else if($lang_full_name == "en_ZA" || $lang_full_name == "en_CA" || $lang_full_name == "en_NZ" || $lang_full_name == "en_AU" || $lang_full_name == "en_GB"){
			$lang = "es-us";
		}else if($lang_full_name == "eo"){
			$lang = "eo";
		}else if($lang_full_name == "es_VE" || $lang_full_name == "es_CL" || $lang_full_name == "es_GT" || $lang_full_name == "es_CO" || $lang_full_name == "es_MX" || $lang_full_name == "es_CR" || $lang_full_name == "es_PE" || $lang_full_name == "es_AR" || $lang_full_name == "es_ES"){
			$lang = "es";
		}else if($lang_full_name == "et"){
			$lang = "et";
		}else if($lang_full_name == "eu"){
			$lang = "eu";
		}else if($lang_full_name == "fa_IR"){
			$lang = "fa";
		}else if($lang_full_name == "fi"){
			$lang = "fi";
		}else if($lang_full_name == "fr_FR" || $lang_full_name == "fr_CA" || $lang_full_name == "fr_BE"){
			$lang = "fr";
		}else if($lang_full_name == "gl_ES"){
			$lang = "gl";
		}else if($lang_full_name == "gu"){
			$lang = "gu";
		}else if($lang_full_name == "he_IL"){
			$lang = "he";
		}else if($lang_full_name == "hi_IN"){
			$lang = "hi";
		}else if($lang_full_name == "hr"){
			$lang = "hr";
		}else if($lang_full_name == "hu_HU"){
			$lang = "hu";
		}else if($lang_full_name == "hy"){
			$lang = "hy-am";
		}else if($lang_full_name == "id_ID"){
			$lang = "id";
		}else if($lang_full_name == "is_IS"){
			$lang = "is";
		}else if($lang_full_name == "it_IT"){
			$lang = "it";
		}else if($lang_full_name == "ja"){
			$lang = "ja";
		}else if($lang_full_name == "jv_ID"){
			$lang = "jv";
		}else if($lang_full_name == "ka_GE"){
			$lang = "ka";
		}else if($lang_full_name == "kk"){
			$lang = "kk";
		}else if($lang_full_name == "km"){
			$lang = "km";
		}else if($lang_full_name == "ko_KR"){
			$lang = "ko";
		}else if($lang_full_name == "lo"){
			$lang = "lo";
		}else if($lang_full_name == "lt_LT"){
			$lang = "lt";
		}else if($lang_full_name == "lv"){
			$lang = "lv";
		}else if($lang_full_name == "mk_MK"){
			$lang = "mk";
		}else if($lang_full_name == "ml_IN"){
			$lang = "ml";
		}else if($lang_full_name == "mn"){
			$lang = "mn";
		}else if($lang_full_name == "mr"){
			$lang = "mr";
		}else if($lang_full_name == "ms_MY"){
			$lang = "ms";
		}else if($lang_full_name == "my_MM"){
			$lang = "my";
		}else if($lang_full_name == "nb_NO"){
			$lang = "nb";
		}else if($lang_full_name == "ne_NP"){
			$lang = "ne";
		}else if($lang_full_name == "nl_NL" || $lang_full_name == "nl_NL_formal" || $lang_full_name == "nl_BE"){
			$lang = "nl";
		}else if($lang_full_name == "nn_NO"){
			$lang = "nn";
		}else if($lang_full_name == "pa_IN"){
			$lang = "pa-in";
		}else if($lang_full_name == "pl_PL"){
			$lang = "pl";
		}else if($lang_full_name == "pt_BR" || $lang_full_name == "pt_PT_ao90" || $lang_full_name == "pt_PT"){
			$lang = "pt";
		}else if($lang_full_name == "ro_RO"){
			$lang = "ro";
		}else if($lang_full_name == "ru_RU"){
			$lang = "ru";
		}else if($lang_full_name == "si_LK"){
			$lang = "si";
		}else if($lang_full_name == "sk_SK"){
			$lang = "sk";
		}else if($lang_full_name == "sl_SI"){
			$lang = "sl";
		}else if($lang_full_name == "sq"){
			$lang = "sq";
		}else if($lang_full_name == "sr_RS"){
			$lang = "sr";
		}else if($lang_full_name == "sv_SE"){
			$lang = "sv";
		}else if($lang_full_name == "szl"){
			$lang = "sw";
		}else if($lang_full_name == "ta_IN"){
			$lang = "ta";
		}else if($lang_full_name == "te"){
			$lang = "te";
		}else if($lang_full_name == "th"){
			$lang = "th";
		}else if($lang_full_name == "tl"){
			$lang = "tlh";
		}else if($lang_full_name == "tr_TR"){
			$lang = "tr";
		}else if($lang_full_name == "tt_RU"){
			$lang = "tet";
		}else if($lang_full_name == "ug_CN"){
			$lang = "ug-cn";
		}else if($lang_full_name == "uk"){
			$lang = "uk";
		}else if($lang_full_name == "ur"){
			$lang = "ur";
		}else if($lang_full_name == "uz_UZ"){
			$lang = "uz";
		}else if($lang_full_name == "vi"){
			$lang = "vi";
		}else if($lang_full_name == "zh_HK"){
			$lang = "zh-hk";
		}else if($lang_full_name == "zh_TW"){
			$lang = "zh-tw";
		}else if($lang_full_name == "zh_CN"){
			$lang = "zh-cn";
		}else{
			$lang = "";
		}
		
		if($lang != ""){
			wp_register_script('lang',$octabook_plugin_url . "/assets/js/language/".$lang.".js",'','',true);
			wp_enqueue_script('lang');
		}
		
		
		wp_register_style( 'oct_admin_style',$octabook_plugin_url . '/assets/oct-admin-style.css');		
		wp_register_style( 'oct_admin_common', $octabook_plugin_url . '/assets/oct-admin-common.css' );
		wp_register_style( 'oct_admin_responsive',$octabook_plugin_url .'/assets/oct-admin-responsive.css' );
		wp_register_style( 'oct_admin_reset',$octabook_plugin_url .'/assets/oct-reset.min.css' );
		
		wp_register_style( 'bootstarp_min_css',$octabook_plugin_url . '/assets/bootstrap/bootstrap.min.css');
		wp_register_style( 'bootstrap_daterangepicker', $octabook_plugin_url . '/assets/daterangepicker.css' );
		wp_register_style('octabook_phone_codes', plugins_url('assets/intlTelInput.css', __FILE__) );
		wp_register_style('bootstrap_theme_min', plugins_url('assets/bootstrap/bootstrap-theme.min.css', __FILE__) );
		wp_register_style('bootstrap_toggle_min', plugins_url('assets/bootstrap/bootstrap-toggle.min.css', __FILE__) );
		wp_register_style('bootstrap_select_min', plugins_url('assets/bootstrap/bootstrap-select.min.css', __FILE__) );
		wp_register_style('jquery_jcrop', plugins_url('assets/jquery.Jcrop.min.css', __FILE__) );
		wp_register_style('jquery_minicolors', plugins_url('assets/jquery.minicolors.css', __FILE__) );
		wp_register_style('jquery_dataTables_min', plugins_url('assets/jquery.dataTables.min.css', __FILE__) );
		wp_register_style('responsive_dataTables_min', plugins_url('assets/responsive.dataTables.min.css', __FILE__) );
		wp_register_style('dataTables_bootstrap_min', plugins_url('assets/dataTables.bootstrap.min.css', __FILE__) );
		wp_register_style('buttons_dataTables_min', plugins_url('assets/buttons.dataTables.min.css', __FILE__) );
		wp_register_style('bootstrap_editable', plugins_url('assets/bootstrap-editable.css', __FILE__) );
		wp_register_style('jquery_ui_min', plugins_url('assets/jquery-ui.min.css', __FILE__) );
		wp_register_style('form_builder_min', plugins_url('assets/form-builder.min.css', __FILE__) );
		wp_register_style('form_render_min', plugins_url('assets/form-render.min.css', __FILE__) );
		wp_register_style('font_awesome_min', plugins_url('assets/font-awesome/css/fontawesome.min.css', __FILE__) );
		wp_register_style('font_all_min', plugins_url('assets/font-awesome/css/all.min.css', __FILE__) );
		wp_register_style('simple_line_icons', plugins_url('assets/line-icons/simple-line-icons.css', __FILE__) );
			
		
		wp_enqueue_style( 'oct_admin_style' );
		wp_enqueue_style( 'oct_admin_common' );
		wp_enqueue_style( 'oct_admin_responsive' );
		wp_enqueue_style( 'oct_admin_reset' );
		wp_enqueue_style( 'bootstarp_min_css' );
		wp_enqueue_style( 'bootstrap_daterangepicker' );
		wp_enqueue_style( 'octabook_phone_codes' );
		wp_enqueue_style( 'bootstrap_theme_min' );
		wp_enqueue_style( 'bootstrap_toggle_min' );
		wp_enqueue_style( 'bootstrap_select_min' );
		wp_enqueue_style( 'jquery_jcrop' );
		wp_enqueue_style( 'jquery_minicolors' );
		wp_enqueue_style( 'jquery_dataTables_min' );
		wp_enqueue_style( 'responsive_dataTables_min' );
		wp_enqueue_style( 'dataTables_bootstrap_min' );
		wp_enqueue_style( 'buttons_dataTables_min' );
		wp_enqueue_style( 'bootstrap_editable' );
		wp_enqueue_style( 'jquery_ui_min' );
		wp_enqueue_style( 'form_builder_min' );
		wp_enqueue_style( 'form_render_min' );
		wp_enqueue_style( 'font_awesome_min' );
		wp_enqueue_style( 'font_all_min' );
		wp_enqueue_style( 'simple_line_icons' );
		
	
		if ( 'admin_page_appointments_submenu' == $hook || 'toplevel_page_octabook_menu' == $hook ) {
			 wp_register_script('moment_min_js',$octabook_plugin_url . '/assets/js/moment.min.js');
			 wp_enqueue_script('moment_min_js');
			 wp_register_script('fc_min_js',$octabook_plugin_url . '/assets/js/fullcalendar.min.js');
			 wp_enqueue_script('fc_min_js');
			 wp_register_script('fc_language_js',$octabook_plugin_url . '/assets/js/lang-all.js');
			 wp_enqueue_script('fc_language_js');
			 wp_register_style( 'fc_min_css',$octabook_plugin_url . '/assets/fullcalendar.min.css');
			 wp_enqueue_style( 'fc_min_css' );
			
						
	/* 		if(current_user_can('manage_options') || current_user_can('oct_provider')) {
				
				wp_register_script('octabook_appointment_calendar',$octabook_plugin_url . '/assets/js/octabook_appointment_calendar.js');					
				wp_enqueue_script('octabook_appointment_calendar');
			 } */
		}
			
		if('admin_page_clients_submenu'==$hook){
			/* wp_register_script('client_listing_modal_js',$octabook_plugin_url . '/assets/js/oct_client_listing_modal.js');
			wp_enqueue_script('client_listing_modal_js'); */
		}
	
		if('admin_page_appearance_submenu'==$hook || 'admin_page_add_service_submenu'==$hook || 'admin_page_update_service_submenu'==$hook) {
				wp_register_script('jscolor_js',$octabook_plugin_url . '/assets/js/jscolor.js');
				wp_enqueue_script('jscolor_js');
		}
	 
	}

	/* add new role oct_staff */
	function oct_staff_role(){
		add_role('oct_staff','oct staff',['read' => true]);
	}
	add_action('admin_init', 'oct_staff_role');