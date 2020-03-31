<?php 

/**
 * Class octabook Uninstall
 * Uninstalling octabook deletes user roles, tables, and options.
 *
 * @author      TeamBI
 */
 
	class octabook_uninstall{


		  /* remove all roles */
		  function remove_oct_roles() {
			   remove_role('oct_staff'); 
			   remove_role('oct_manager'); 
			   remove_role('oct_client');
		  }  
		  
		  
		  /* Remove database tables */
		  function remove_oct_mysql_tables() {			
				global $wpdb;				
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_bookings" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_categories" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_coupons" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_email_templates" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_locations" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_order_client_info" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_payments" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_providers_services" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_schdeule_offtimes" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_schedule" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_schedule_breaks" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_schedule_dayoffs" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_services" );
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_service_schedule_price");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_sms_templates");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_addon_service_rate");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_booking_addons");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_loyalty_points");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_reviews");
				$wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."oct_services_addon");
		  }
		  /* remove wordpress options by octabook */
		  function remove_oct_wp_options() {
			global $wpdb;
			$wpdb->query("DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'octabook_%'");
		   }
		   
		   
		   /* remove octabook pages */
		  function remove_oct_wp_pages() {
				$pageTY = get_page_by_title( 'thankyou' );
				if($pageTY!=''){
					wp_trash_post($pageTY->ID);
				}
		  }		  
		 
	  
	}
?>