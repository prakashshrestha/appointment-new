<?php 
class octabook_update {

	  public function __construct() {
	  
		  $root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		  
		  if (file_exists($root.'/wp-load.php')) {
		   require_once($root.'/wp-load.php');
		  }
		  global $wpdb; 	  
		  
			$version1_0 = get_option('octabook_version');
			if(!isset($version1_0)) {
				add_option('octabook_version','2.6');
			}
			
			
			$version1_1 = get_option('octabook_version');
			if($version1_1 < 1.1){
				add_option('octabook_cancelation_policy_status','D');
				add_option('octabook_cancelation_policy_header','');
				add_option('octabook_cancelation_policy_text','');
				add_option('octabook_allow_terms_and_conditions','D');
				add_option('octabook_allow_terms_and_conditions_url','');
				add_option('octabook_allow_privacy_policy','D');
				add_option('octabook_allow_privacy_policy_url','');
				update_option('octabook_version','1.1');
			}
			$version2_0 = get_option('octabook_version');
			if($version2_0 < 2.0){
				add_option('octabook_payment_method_Paytm','D');
				add_option('octabook_paytm_testing_mode','D');
				add_option('octabook_paytm_merchantkey','');
				add_option('octabook_paytm_merchantid','');
				add_option('octabook_paytm_website','');
				add_option('octabook_paytm_channelid','');
				add_option('octabook_paytm_industryid','');
				
				add_option('octabook_payment_method_Payumoney','D');
				add_option('octabook_payumoney_testing_mode','D');
				add_option('octabook_payumoney_merchantkey','');
				add_option('octabook_payumoney_saltkey','');
				
				add_option('octabook_textlocal_admin_sms_notification_status','D');
				add_option('octabook_textlocal_service_provider_sms_notification_status','D');
				add_option('octabook_textlocal_client_sms_notification_status','D');
				add_option('octabook_sms_noti_textlocal','D');
				add_option('octabook_textlocal_apikey','');
				add_option('octabook_textlocal_sender','');
				add_option('octabook_textlocal_ccode','+1');
				add_option('octabook_textlocal_ccode_alph','us');
				add_option('octabook_textlocal_admin_phone_no','');

				add_option('octabook_msg91_admin_sms_notification_status','D');
				add_option('octabook_msg91_service_provider_sms_notification_status','D');
				add_option('octabook_msg91_client_sms_notification_status','D');
				add_option('octabook_sms_noti_msg91','D');
				add_option('octabook_msg91_apikey','');
				add_option('octabook_msg91_sender','');
				add_option('octabook_msg91_ccode','+1');
				add_option('octabook_msg91_ccode_alph','us');
				add_option('octabook_msg91_admin_phone_no','');
				
				$wpdb->query("ALTER TABLE ".$wpdb->prefix."oct_payments CHANGE `payment_method` `payment_method` ENUM('paypal','pay_locally','Free','payumoney','paytm','stripe');");
				update_option('octabook_version','2.0');
			}
			$version2_1 = get_option('octabook_version');
			if($version2_1 < 2.1){
				update_option('octabook_version','2.1');
			}
			$version2_2 = get_option('octabook_version');
			if($version2_2 < 2.2){
				update_option('octabook_version','2.2');
			}
			$version2_3 = get_option('octabook_version');
			if($version2_3 < 2.3){
				$wpdb->query("ALTER TABLE `".$wpdb->prefix."oct_order_client_info` CHANGE `client_phone` `client_phone` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
				
				$wpdb->query("UPDATE `".$wpdb->prefix."oct_email_templates` SET `email_subject`='Appointment Rescheduled' WHERE `email_template_name`='RSC' AND `user_type`='C'");
				
				update_option('octabook_version','2.3');
			}
			$version2_4 = get_option('octabook_version');
			if($version2_4 < 2.4){
				update_option('octabook_version','2.4');
			}
			$version2_5 = get_option('octabook_version');
			if($version2_5 < 2.5){
				add_option('octabook_booking_page','');
				add_option( 'octabook_api_key', '', '', 'yes' );
				update_option('octabook_version','2.5');
			}
				$version2_6 = get_option('octabook_version');
			if($version2_6 < 2.6){
				update_option('octabook_version','2.6');
			}
	 }
}
?>