<?php
class octabook_settings{
	
   /** Appearance Settings **/
   public $octabook_primary_color;
   public $octabook_secondary_color;
   public $octabook_text_color;
   public $octabook_bg_text_color;  
   public $octabook_admin_color_primary;  
   public $octabook_admin_color_secondary;  
   public $octabook_admin_color_text;  
   public $octabook_admin_color_bg_text;  
   public $octabook_guest_user_checkout;    
   public $octabook_show_provider;
   public $octabook_show_provider_avatars;
   public $octabook_show_services;
   public $octabook_show_service_desc;
   public $octabook_show_coupons;
   public $octabook_hide_booked_slot;
   public $octabook_cart;
   public $octabook_max_cartitem_limit;
   public $octabook_reviews_status;
   public $octabook_auto_confirm_reviews;
   public $octabook_frontend_custom_css;
   
   public $octabook_countrycodes_withflags;    
   public $octabook_default_country_short_code;
   public $octabook_thankyou_page;
   public $octabook_thankyou_page_rdtime;
   public $octabook_booking_page;
   public $octabook_frontend_loader;
   
 
   
   /* octabook General Settings */
   public $octabook_multi_location;
   public $octabook_api_key;
   public $octabook_zipcode_booking;
   public $octabook_booking_zipcodes;
   public $octabook_booking_time_interval;
   public $octabook_minimum_advance_booking;
   public $octabook_maximum_advance_booking;
   public $octabook_booking_padding_time;
   public $octabook_cancellation_buffer_time;
   public $octabook_reschedule_buffer_time;
   public $octabook_currency;
   public $octabook_currency_symbol;
   public $octabook_currency_symbol_position;
   public $octabook_price_format_decimal_places;
   public $octabook_price_format_comma_separator;      
   public $octabook_multiple_booking_sameslot;  	
   public $octabook_slot_max_booking_limit;
   public $octabook_appointment_auto_confirm;
   public $octabook_dayclosing_overlap;   
   public $octabook_client_as_wordpress_user_role;
   public $octabook_taxvat_status;
   public $octabook_taxvat_type;
   public $octabook_taxvat_amount;
   public $octabook_pd_type;
   public $octabook_partial_deposit_amount;
   public $octabook_partial_deposit_type;
   public $octabook_partial_deposit_status;
   public $octabook_partial_deposit_message;
   public $octabook_location_sortby;
   public $booking_cart_description;
   public $octabook_datepicker_format;
   public $octabook_cancelation_policy_status;
   public $octabook_cancelation_policy_header;
   public $octabook_cancelation_policy_text;
   public $octabook_allow_terms_and_conditions;
   public $octabook_allow_terms_and_conditions_url;
   public $octabook_allow_privacy_policy;
   public $ct_privacy_policy_url;
	
    /*Company Settings*/
	public $octabook_company_name;
	public $octabook_company_email;
	public $octabook_company_address;
	public $octabook_company_city;
	public $octabook_company_state;
	public $octabook_company_zip;
	public $octabook_company_country;
	public $octabook_company_logo;
	public $octabook_company_country_code;
	public $octabook_company_phone;
	public $default_company_country_flag;
	
  
	/* Payment Settings */
	public $octabook_payment_gateways_status; 
	public $octabook_locally_payment_status;  
    public $octabook_payment_method_Paypal;
    public $octabook_payment_method_Stripe;
	public $octabook_payment_method_2Checkout;
	public $octabook_payment_method_Authorizenet;   
   /* Paypal */
   public $octabook_paypal_title;
   public $octabook_paypal_direct_cc_dc_payment;
   public $octabook_paypal_description;
   public $octabook_paypal_merchant_email;
   public $octabook_paypal_api_username;
   public $octabook_paypal_api_password;
   public $octabook_paypal_api_signature;
   public $octabook_paypal_testing_mode; 
   public $octabook_paypal_guest_checkout;   
   /* Stripe */
   public $octabook_stripe_secretKey;
   public $octabook_stripe_publishableKey; 
   /* Authorize.Net */
   public $octabook_authorizenet_title;
   public $octabook_authorizenet_desc;
   public $octabook_authorizenet_api_loginid;
   public $octabook_authorizenet_transaction_key;
   public $octabook_authorizenet_testing_mode;
    /* 2Checkout */
	public $octabook_2checkout_publishablekey;
	public $octabook_2checkout_privateKey;
	public $octabook_2checkout_sellerid;
	public $octabook_2checkout_testing_mode;
	
	/* Payumoney */
	public $octabook_payumoney_testing_mode;
	public $octabook_payment_method_Payumoney;
	public $octabook_payumoney_merchantkey;
	public $octabook_payumoney_saltkey;
	
	/* Paytm */
	public $octabook_paytm_testing_mode;
	public $octabook_payment_method_Paytm;
	public $octabook_paytm_merchantkey;
	public $octabook_paytm_merchantid;
	public $octabook_paytm_website;
	public $octabook_paytm_industryid;
	public $octabook_paytm_channelid;
	
	/* Email Settings */
	public $octabook_email_sender_name;
	public $octabook_email_sender_address;
	public $octabook_admin_email_notification_status;
	public $octabook_manager_email_notification_status;
	public $octabook_service_provider_email_notification_status;
	public $octabook_client_email_notification_status;
	public $octabook_email_reminder_buffer;	
	
   /* SMS Reminder Settings */
   public $octabook_sms_reminder_status;
   
   public $octabook_sms_noti_twilio;
   public $octabook_twilio_sid;
   public $octabook_twilio_auth_token;
   public $octabook_twilio_number;
   public $octabook_twilio_client_sms_notification_status;
   public $octabook_twilio_service_provider_sms_notification_status;
   public $octabook_twilio_admin_sms_notification_status;
   public $octabook_twilio_admin_phone_no;
   public $octabook_twilio_ccode;
   public $octabook_twilio_ccode_alph;

   
   public $octabook_sms_noti_plivo;
   public $octabook_plivo_number;
   public $octabook_plivo_sid;
   public $octabook_plivo_auth_token;
   public $octabook_plivo_service_provider_sms_notification_status;
   public $octabook_plivo_client_sms_notification_status;
   public $octabook_plivo_admin_sms_notification_status;
   public $octabook_plivo_admin_phone_no;
   public $octabook_plivo_ccode;
   public $octabook_plivo_ccode_alph;
   
   
   public $octabook_sms_noti_nexmo;
   public $octabook_nexmo_apikey;
   public $octabook_nexmo_api_secret;
   public $octabook_nexmo_form;
   public $octabook_nexmo_send_sms_client_status;
   public $octabook_nexmo_send_sms_sp_status;
   public $octabook_nexmo_send_sms_admin_status;
   public $octabook_nexmo_admin_phone_no;
   public $octabook_nexmo_ccode;
   public $octabook_nexmo_ccode_alph;
   public $octabook_sms_noti_textlocal;
   public $octabook_textlocal_apikey;
   public $octabook_textlocal_sender;
   public $octabook_textlocal_service_provider_sms_notification_status;
   public $octabook_textlocal_client_sms_notification_status;
   public $octabook_textlocal_admin_sms_notification_status;
   public $octabook_textlocal_admin_phone_no;
   public $octabook_textlocal_ccode;
   public $octabook_textlocal_ccode_alph;
   
   

   
   /* Constructor Function to set the default values */
  public function __construct() {
			
			if(!get_option('octabook_booking_time_interval')) {
					$admin_email = get_option('admin_email');
					$octabook_options = array(					
					   /* Appearance Settings */
					   'octabook_primary_color'=>'#6ba5e3',
					   'octabook_secondary_color'=>'#00152b',
					   'octabook_text_color'=>'#3d3d3d',
					   'octabook_bg_text_color'=>'#FFFFFF',  
					   'octabook_admin_color_primary'=>'#6ba5e3',					   
					   'octabook_admin_color_secondary'=>'#00152b',					   
					   'octabook_admin_color_text'=>'#3d3d3d',					   
					   'octabook_admin_color_bg_text'=>'#FFFFFF',					   
					   'octabook_firststep_indications'=>'E',
					   'octabook_datepicker_format'=>'m-d-Y',					  					 
					   'octabook_guest_user_checkout'=>'E',
					   'octabook_single_column_view'=>'D',	
					   'octabook_timeslots_legends'=>'E',
					   'octabook_countrycodes_withflags'=>'E',
					   'octabook_default_country_short_code'=>'us',
					   
										
						'octabook_show_provider'=>'E',
						'octabook_show_provider_avatars'=>'E',
						'octabook_show_services'=>'E',
						'octabook_show_service_desc'=>'E',
						'octabook_show_coupons'=>'E',
						'octabook_hide_booked_slot'=>'E',
						'octabook_cart'=>'E',
						'octabook_max_cartitem_limit'=>'5',
						'octabook_reviews_status'=>'D',
						'octabook_auto_confirm_reviews'=>'D',
						'octabook_frontend_custom_css'=>'',
						'octabook_frontend_loader'=>'',
						
						/* Default General Settings */
						'octabook_multi_location'=>'E',
						'octabook_api_key'=>'',
						'octabook_zipcode_booking'=>'D',
						'octabook_booking_zipcodes'=>'',
						'octabook_booking_time_interval'=>'30',
						'octabook_minimum_advance_booking'=>'360',
						'octabook_maximum_advance_booking'=>'6',
						'octabook_booking_padding_time'=>'',
						'octabook_cancellation_buffer_time'=>'',
						'octabook_reschedule_buffer_time'=>'',
						'octabook_currency'=>'USD',
						'octabook_currency_symbol'=>'$',
						'octabook_currency_symbol_position'=>'B',
						'octabook_price_format_decimal_places'=>'2',						
						'octabook_price_format_comma_separator'=>'N',						
						'octabook_multiple_booking_sameslot'=>'E',									'octabook_slot_max_booking_limit'=>'0',						
						'octabook_appointment_auto_confirm'=>'D',
						'octabook_dayclosing_overlap'=>'D',
						'octabook_thankyou_page'=>'',
						'octabook_thankyou_page_rdtime'=>'5000',
						'octabook_booking_page'=>'',
						'octabook_main_container_background'=>'transparent linear-gradient(to right, #EDEDED 0%, rgba(237, 237, 237, 0.72) 50%, #F6F6F6 50%, #F6F6F6 100%) repeat scroll 0% 0% !important',
						'octabook_client_as_wordpress_user_role'=>'appointment_client',
						'octabook_taxvat_status'=>'D',
						'octabook_taxvat_type'=>'P',
						'octabook_taxvat_amount'=>'',
						'octabook_pd_type'=>'P',
						'octabook_partial_deposit_amount'=>'',
						'octabook_partial_deposit_type'=>'P',
						'octabook_partial_deposit_status'=>'D',
						'octabook_partial_deposit_message'=>'You only need to pay a deposit to confirm your booking. The remaining amount needs to be paid on arrival.',
						'octabook_location_sortby'=>'state',
						'booking_cart_description'=>'E',
						'octabook_cancelation_policy_status'=>'D',
						'octabook_cancelation_policy_header'=>'',
						'octabook_cancelation_policy_text'=>'',
						'octabook_allow_terms_and_conditions'=>'D',
						'octabook_allow_terms_and_conditions_url'=>'',
						'octabook_allow_privacy_policy'=>'D',
						'octabook_allow_privacy_policy_url'=>'',
						
												
						/* Default Company Settings */
						'octabook_company_name'=>'',
						'octabook_company_email'=>'',
						'octabook_company_address'=>'',
						'octabook_company_city'=>'',
						'octabook_company_state'=>'',
						'octabook_company_zip'=>'',
						'octabook_company_country'=>'',
						'octabook_company_logo'=>'',
						'octabook_company_country_code'=>'+1',
						'octabook_company_phone'=>'',
						'default_company_country_flag'=>'us',

						
						/* Payment Settings */
						'octabook_payment_method_Paypal'=>'D', 
						'octabook_payment_method_Stripe'=>'D',						
						'octabook_payment_method_Authorizenet'=>'D',
						'octabook_locally_payment_status'=>'E',
						'octabook_payment_gateways_status'=>'D',	
						'octabook_payment_method_2Checkout'=>'D',
						'octabook_payment_method_Paytm'=>'D',
						
						/* Paypal */
						'octabook_paypal_direct_cc_dc_payment'=>'N',
						'octabook_paypal_title'=>'Paypal',
						'octabook_paypal_description'=>'you can pay with your credit card if you don\'t have a paypal account',
						'octabook_paypal_merchant_email'=>'you@youremail.com',
						'octabook_paypal_testing_mode'=>'D',						
						'octabook_paypal_guest_checkout'=>'D',						
						'octabook_paypal_api_username'=>'',
						'octabook_paypal_api_password'=>'',
						'octabook_paypal_api_signature'=>'',						
						/* Stripe */
						'octabook_stripe_secretKey'=>'',
						'octabook_stripe_publishableKey'=>'',		
						/* Authorize.Net */
						'octabook_authorizenet_title'=>'Authorize.Net',
						'octabook_authorizenet_desc'=>'',
						'octabook_authorizenet_api_loginid'=>'',
						'octabook_authorizenet_transaction_key'=>'',
						'octabook_authorizenet_testing_mode'=>'D',
						/* 2Checkout */
						'octabook_2checkout_publishablekey'=>'',
						'octabook_2checkout_privateKey'=>'',
						'octabook_2checkout_sellerid'=>'',
						'octabook_2checkout_testing_mode'=>'D',
						/* Payumoney */
						'octabook_payment_method_Payumoney'=>'D',
						'octabook_payumoney_merchantkey'=>'',
						'octabook_payumoney_saltkey'=>'',
						'octabook_payumoney_testing_mode'=>'D',
						
						/* Paytm */
						'octabook_payment_method_Paytm'=>'D',
						'octabook_paytm_merchantkey'=>'',
						'octabook_paytm_merchantid'=>'',
						'octabook_paytm_website'=>'',
						'octabook_paytm_channelid'=>'',
						'octabook_paytm_industryid'=>'',
						'octabook_paytm_testing_mode'=>'D',
						
						/* Email Settings */ 
						'octabook_email_sender_name'=>'',
						'octabook_email_sender_address'=>$admin_email,
						'octabook_admin_email_notification_status'=>'E',
						'octabook_manager_email_notification_status'=>'E',
						'octabook_service_provider_email_notification_status'=>'E',
						'octabook_client_email_notification_status'=>'E',
						'octabook_email_reminder_buffer'=>'',
						/* SMS Reminder Settings */
						'octabook_sms_reminder_status'=>'D',
						
						'octabook_sms_noti_twilio'=>'D',
						'octabook_twilio_number'=>'',
						'octabook_twilio_sid'=>'',
						'octabook_twilio_auth_token'=>'',
						'octabook_twilio_client_sms_notification_status'=>'D',
						'octabook_twilio_service_provider_sms_notification_status'=>'D',
						'octabook_twilio_admin_sms_notification_status'=>'D',
						'octabook_twilio_admin_phone_no'=>'',
						'octabook_twilio_ccode'=>'+1',
						'octabook_twilio_ccode_alph'=>'us',
			
						'octabook_sms_noti_plivo'=>'D',
						'octabook_plivo_number'=>'',
						'octabook_plivo_sid'=>'',
						'octabook_plivo_auth_token'=>'',
						'octabook_plivo_service_provider_sms_notification_status'=>'D',
						'octabook_plivo_client_sms_notification_status'=>'D',
						'octabook_plivo_admin_sms_notification_status'=>'D',
						'octabook_plivo_admin_phone_no'=>'',
						'octabook_plivo_ccode'=>'+1',
						'octabook_plivo_ccode_alph'=>'us',

						'octabook_sms_noti_nexmo'=>'D',
						'octabook_nexmo_apikey'=>'',
						'octabook_nexmo_api_secret'=>'',
						'octabook_nexmo_form'=>'',
						'octabook_nexmo_send_sms_client_status'=>'D',
						'octabook_nexmo_send_sms_sp_status'=>'D',
						'octabook_nexmo_send_sms_admin_status'=>'D',
						'octabook_nexmo_admin_phone_no'=>'',
						'octabook_nexmo_ccode'=>'+1',
						
						'octabook_sms_noti_textlocal'=>'D',
						'octabook_textlocal_apikey'=>'',
						'octabook_textlocal_sender'=>'',
						'octabook_textlocal_service_provider_sms_notification_status'=>'D',
						'octabook_textlocal_client_sms_notification_status'=>'D',
						'octabook_textlocal_admin_sms_notification_status'=>'D',
						'octabook_textlocal_admin_phone_no'=>'',
						'octabook_textlocal_ccode'=>'+1',
						'octabook_textlocal_ccode_alph'=>'us',

						'octabook_sms_noti_msg91'=>'D',
						'octabook_msg91_apikey'=>'',
						'octabook_msg91_sender'=>'',
						'octabook_msg91_service_provider_sms_notification_status'=>'D',
						'octabook_msg91_client_sms_notification_status'=>'D',
						'octabook_msg91_admin_sms_notification_status'=>'D',
						'octabook_msg91_admin_phone_no'=>'',
						'octabook_msg91_ccode'=>'+1',
						'octabook_msg91_ccode_alph'=>'us',
						
						/* GC Settings */
						
						'oct_gc_status'=>'N',
						'oct_gc_two_way_sync_status'=>'N',
						'oct_gc_token'=>'',
						'oct_gc_id'=>'',
						'oct_gc_client_id'=>'',
						'oct_gc_client_secret'=>'',
						'oct_gc_frontend_url'=>'',
						'oct_gc_admin_url'=>''
						
					);	
						
					foreach($octabook_options as $option_key => $option_value) {
						add_option($option_key,$option_value);
					}
			
			}
   
   }
   

	
	/** ReadAll Settings **/
	function readAll(){
	
			/** Default Appearance Settings **/
			$this->octabook_primary_color = get_option('octabook_primary_color');
			$this->octabook_secondary_color = get_option('octabook_secondary_color');
			$this->octabook_text_color = get_option('octabook_text_color');
			$this->octabook_bg_text_color = get_option('octabook_bg_text_color');					
			$this->octabook_admin_color_primary = get_option('octabook_admin_color_primary');
			$this->octabook_admin_color_secondary = get_option('octabook_admin_color_secondary');
			$this->octabook_admin_color_text = get_option('octabook_admin_color_text');
			$this->octabook_admin_color_bg_text = get_option('octabook_admin_color_bg_text');
			$this->octabook_guest_user_checkout = get_option('octabook_guest_user_checkout');				
			$this->octabook_show_provider = get_option('octabook_show_provider');
			$this->octabook_show_provider_avatars = get_option('octabook_show_provider_avatars');
			$this->octabook_show_services = get_option('octabook_show_services');
			$this->octabook_show_service_desc = get_option('octabook_show_service_desc');
			$this->octabook_show_coupons = get_option('octabook_show_coupons');				
			$this->octabook_hide_booked_slot = get_option('octabook_hide_booked_slot');
			
			$this->octabook_countrycodes_withflags = get_option('octabook_countrycodes_withflags');				
			$this->octabook_default_country_short_code = get_option('octabook_default_country_short_code');	
			$this->octabook_cart = get_option('octabook_cart');	
			$this->octabook_max_cartitem_limit = get_option('octabook_max_cartitem_limit');	
			$this->octabook_reviews_status = get_option('octabook_reviews_status');	
			$this->octabook_auto_confirm_reviews = get_option('octabook_auto_confirm_reviews');	
			$this->octabook_frontend_custom_css = get_option('octabook_frontend_custom_css');	
			$this->octabook_frontend_loader = get_option('octabook_frontend_loader');	
			/*** End ***/
			
			/* General Settings */
			$this->octabook_multi_location = get_option('octabook_multi_location');
			$this->octabook_api_key = get_option('octabook_api_key');
			$this->octabook_zipcode_booking = get_option('octabook_zipcode_booking');
			$this->octabook_booking_zipcodes = get_option('octabook_booking_zipcodes');
			$this->octabook_booking_time_interval = get_option('octabook_booking_time_interval');
			$this->octabook_minimum_advance_booking = get_option('octabook_minimum_advance_booking');
			$this->octabook_maximum_advance_booking = get_option('octabook_maximum_advance_booking');
			$this->octabook_booking_padding_time = get_option('octabook_booking_padding_time');
			$this->octabook_cancellation_buffer_time = get_option('octabook_cancellation_buffer_time');
			$this->octabook_reschedule_buffer_time = get_option('octabook_reschedule_buffer_time');
			$this->octabook_currency = get_option('octabook_currency');
			$this->octabook_currency_symbol = get_option('octabook_currency_symbol');
			$this->octabook_currency_symbol_position = get_option('octabook_currency_symbol_position');
			$this->octabook_price_format_decimal_places = get_option('octabook_price_format_decimal_places');
			$this->octabook_price_format_comma_separator = get_option('octabook_price_format_comma_separator');
			$this->octabook_multiple_booking_sameslot = get_option('octabook_multiple_booking_sameslot');	
			$this->octabook_slot_max_booking_limit = get_option('octabook_slot_max_booking_limit');
			$this->octabook_appointment_auto_confirm = get_option('octabook_appointment_auto_confirm');
			$this->octabook_dayclosing_overlap = get_option('octabook_dayclosing_overlap');
			$this->octabook_thankyou_page = get_option('octabook_thankyou_page');
			$this->octabook_thankyou_page_rdtime = get_option('octabook_thankyou_page_rdtime');
			$this->octabook_booking_page = get_option('octabook_booking_page');
			$this->octabook_main_container_background = get_option('octabook_main_container_background');	
			$this->octabook_taxvat_status = get_option('octabook_taxvat_status');
			$this->octabook_pd_type = get_option('octabook_pd_type');
			$this->octabook_partial_deposit_amount = get_option('octabook_partial_deposit_amount');
			$this->octabook_partial_deposit_type= get_option('octabook_partial_deposit_type');
			$this->octabook_partial_deposit_status= get_option('octabook_partial_deposit_status');
			$this->octabook_partial_deposit_message= get_option('octabook_partial_deposit_message');
			$this->octabook_taxvat_type = get_option('octabook_taxvat_type');
			$this->octabook_taxvat_amount = get_option('octabook_taxvat_amount');
			$this->octabook_location_sortby = get_option('octabook_location_sortby');
			$this->booking_cart_description = get_option('booking_cart_description');
			$this->octabook_datepicker_format = get_option('date_format');
			$this->octabook_cancelation_policy_status = get_option('octabook_cancelation_policy_status');
			$this->octabook_cancelation_policy_header = get_option('octabook_cancelation_policy_header');
			$this->octabook_cancelation_policy_text = get_option('octabook_cancelation_policy_text');
			$this->octabook_allow_terms_and_conditions = get_option('octabook_allow_terms_and_conditions');
			$this->octabook_allow_terms_and_conditions_url = get_option('octabook_allow_terms_and_conditions_url');
			$this->octabook_allow_privacy_policy = get_option('octabook_allow_privacy_policy');
			$this->octabook_allow_privacy_policy_url = get_option('octabook_allow_privacy_policy_url');
						
			/*** End ***/
				
			/** Company Settings **/
			$this->octabook_company_name = get_option('octabook_company_name');
			$this->octabook_company_email = get_option('octabook_company_email');
			$this->octabook_company_address = get_option('octabook_company_address');
			$this->octabook_company_city = get_option('octabook_company_city');
			$this->octabook_company_state = get_option('octabook_company_state');
			$this->octabook_company_zip = get_option('octabook_company_zip');
			$this->octabook_company_country = get_option('octabook_company_country');
			$this->octabook_company_logo = get_option('octabook_company_logo');
			$this->octabook_company_country_code = get_option('octabook_company_country_code');
			$this->octabook_company_phone = get_option('octabook_company_phone');
			$this->default_company_country_flag = get_option('default_company_country_flag');
			/*** End ***/
				
			/** Payment Settings **/
			$this->octabook_payment_method_Paypal = get_option('octabook_payment_method_Paypal');
			$this->octabook_payment_method_Stripe = get_option('octabook_payment_method_Stripe');
			$this->octabook_payment_method_Authorizenet = get_option('octabook_payment_method_Authorizenet');
			$this->octabook_payment_method_2Checkout = get_option('octabook_payment_method_2Checkout');
			$this->octabook_locally_payment_status= get_option('octabook_locally_payment_status');
			$this->octabook_payment_gateways_status= get_option('octabook_payment_gateways_status');			
			$this->octabook_payment_method_Payumoney= get_option('octabook_payment_method_Payumoney');			
			$this->octabook_payment_method_Paytm= get_option('octabook_payment_method_Paytm');			
			//Paypal
			$this->octabook_paypal_direct_cc_dc_payment = get_option('octabook_paypal_direct_cc_dc_payment');
			$this->octabook_paypal_title = get_option('octabook_paypal_title');
			$this->octabook_paypal_description = get_option('octabook_paypal_description');
			$this->octabook_paypal_merchant_email = get_option('octabook_paypal_merchant_email');
			$this->octabook_paypal_testing_mode = get_option('octabook_paypal_testing_mode');
			$this->octabook_paypal_guest_checkout = get_option('octabook_paypal_guest_checkout');			
			$this->octabook_paypal_api_username = get_option('octabook_paypal_api_username');
			$this->octabook_paypal_api_password = get_option('octabook_paypal_api_password');
			$this->octabook_paypal_api_signature = get_option('octabook_paypal_api_signature');		
			//Stripe
			$this->octabook_stripe_secretKey =  get_option('octabook_stripe_secretKey');
			$this->octabook_stripe_publishableKey =  get_option('octabook_stripe_publishableKey');			
			//Authorize.Net
			$this->octabook_authorizenet_title = get_option('octabook_authorizenet_title');
			$this->octabook_authorizenet_desc = get_option('octabook_authorizenet_desc');
			$this->octabook_authorizenet_api_loginid = get_option('octabook_authorizenet_api_loginid');
			$this->octabook_authorizenet_transaction_key = get_option('octabook_authorizenet_transaction_key');
			$this->octabook_authorizenet_testing_mode = get_option('octabook_authorizenet_testing_mode');
			/* 2Checkout */		
			$this->octabook_2checkout_publishablekey = get_option('octabook_2checkout_publishablekey');
			$this->octabook_2checkout_privateKey = get_option('octabook_2checkout_privateKey');
			$this->octabook_2checkout_sellerid = get_option('octabook_2checkout_sellerid');
			$this->octabook_2checkout_testing_mode = get_option('octabook_2checkout_testing_mode');
			/* Payumoney */
			$this->octabook_payumoney_merchantkey = get_option('octabook_payumoney_merchantkey');
			$this->octabook_payumoney_saltkey = get_option('octabook_payumoney_saltkey');
			$this->octabook_payumoney_testing_mode = get_option('octabook_payumoney_testing_mode');
			/* Paytm */
			$this->octabook_paytm_merchantkey = get_option('octabook_paytm_merchantkey');
			$this->octabook_paytm_merchantid = get_option('octabook_paytm_merchantid');
			$this->octabook_paytm_website = get_option('octabook_paytm_website');
			$this->octabook_paytm_channelid = get_option('octabook_paytm_channelid');
			$this->octabook_paytm_industryid = get_option('octabook_paytm_industryid');
			$this->octabook_paytm_testing_mode = get_option('octabook_paytm_testing_mode');

			/* Email Settings */
			$this->octabook_admin_eamil_address = get_option('octabook_admin_eamil_address');
			$this->octabook_email_sender_name = get_option('octabook_email_sender_name');
			$this->octabook_email_sender_address = get_option('octabook_email_sender_address');
			$this->octabook_admin_email_notification_status = get_option('octabook_admin_email_notification_status');
			$this->octabook_manager_email_notification_status = get_option('octabook_manager_email_notification_status');			
			$this->octabook_service_provider_email_notification_status = get_option('octabook_service_provider_email_notification_status');
			$this->octabook_client_email_notification_status = get_option('octabook_client_email_notification_status');
			$this->octabook_email_reminder_buffer = get_option('octabook_email_reminder_buffer'); 
			
		
			/* Social Login Settings */
			$this->octabook_fb_social_login_status = get_option('octabook_fb_social_login_status');
			$this->octabook_fb_appid = get_option('octabook_fb_appid');
			$this->octabook_fb_appsecret = get_option('octabook_fb_appsecret');
			
			/* SMS Reminder Settings */
			$this->octabook_sms_reminder_status = get_option('octabook_sms_reminder_status');

			$this->octabook_sms_noti_twilio = get_option('octabook_sms_noti_twilio');
			$this->octabook_twilio_number = get_option('octabook_twilio_number');
			$this->octabook_twilio_sid = get_option('octabook_twilio_sid');
			$this->octabook_twilio_auth_token = get_option('octabook_twilio_auth_token');
			$this->octabook_twilio_client_sms_notification_status = get_option('octabook_twilio_client_sms_notification_status');
			$this->octabook_twilio_service_provider_sms_notification_status = get_option('octabook_twilio_service_provider_sms_notification_status');
			$this->octabook_twilio_admin_sms_notification_status = get_option('octabook_twilio_admin_sms_notification_status');
			$this->octabook_twilio_admin_phone_no = get_option('octabook_twilio_admin_phone_no');
			$this->octabook_twilio_ccode = get_option('octabook_twilio_ccode');
			$this->octabook_twilio_ccode_alph = get_option('octabook_twilio_ccode_alph');
						
			$this->octabook_sms_noti_plivo = get_option('octabook_sms_noti_plivo');
			$this->octabook_plivo_number = get_option('octabook_plivo_number');
			$this->octabook_plivo_sid = get_option('octabook_plivo_sid');
			$this->octabook_plivo_auth_token = get_option('octabook_plivo_auth_token');
			$this->octabook_plivo_service_provider_sms_notification_status = get_option('octabook_plivo_service_provider_sms_notification_status');
			$this->octabook_plivo_client_sms_notification_status = get_option('octabook_plivo_client_sms_notification_status');
			$this->octabook_plivo_admin_sms_notification_status = get_option('octabook_plivo_admin_sms_notification_status');
			$this->octabook_plivo_admin_phone_no = get_option('octabook_plivo_admin_phone_no');
			$this->octabook_plivo_ccode = get_option('octabook_plivo_ccode');
			$this->octabook_plivo_ccode_alph = get_option('octabook_plivo_ccode_alph');

			$this->octabook_sms_noti_nexmo = get_option('octabook_sms_noti_nexmo');
			$this->octabook_nexmo_apikey = get_option('octabook_nexmo_apikey');
			$this->octabook_nexmo_api_secret = get_option('octabook_nexmo_api_secret');
			$this->octabook_nexmo_form = get_option('octabook_nexmo_form');
			$this->octabook_nexmo_send_sms_client_status = get_option('octabook_nexmo_send_sms_client_status');
			$this->octabook_nexmo_send_sms_sp_status = get_option('octabook_nexmo_send_sms_sp_status');
			$this->octabook_nexmo_send_sms_admin_status = get_option('octabook_nexmo_send_sms_admin_status');
			$this->octabook_nexmo_admin_phone_no = get_option('octabook_nexmo_admin_phone_no');
			$this->octabook_nexmo_ccode = get_option('octabook_nexmo_ccode');
			$this->octabook_nexmo_ccode_alph = get_option('octabook_nexmo_ccode_alph');
			
			$this->octabook_sms_noti_textlocal = get_option('octabook_sms_noti_textlocal');
			$this->octabook_textlocal_apikey = get_option('octabook_textlocal_apikey');
			$this->octabook_textlocal_sender = get_option('octabook_textlocal_sender');
			$this->octabook_textlocal_service_provider_sms_notification_status = get_option('octabook_textlocal_service_provider_sms_notification_status');
			$this->octabook_textlocal_client_sms_notification_status = get_option('octabook_textlocal_client_sms_notification_status');
			$this->octabook_textlocal_admin_sms_notification_status = get_option('octabook_textlocal_admin_sms_notification_status');
			$this->octabook_textlocal_admin_phone_no = get_option('octabook_textlocal_admin_phone_no');
			$this->octabook_textlocal_ccode = get_option('octabook_textlocal_ccode');
			$this->octabook_textlocal_ccode_alph = get_option('octabook_textlocal_ccode_alph');

			$this->octabook_sms_noti_msg91 = get_option('octabook_sms_noti_msg91');
			$this->octabook_msg91_apikey = get_option('octabook_msg91_apikey');
			$this->octabook_msg91_sender = get_option('octabook_msg91_sender');
			$this->octabook_msg91_service_provider_sms_notification_status = get_option('octabook_msg91_service_provider_sms_notification_status');
			$this->octabook_msg91_client_sms_notification_status = get_option('octabook_msg91_client_sms_notification_status');
			$this->octabook_msg91_admin_sms_notification_status = get_option('octabook_msg91_admin_sms_notification_status');
			$this->octabook_msg91_admin_phone_no = get_option('octabook_msg91_admin_phone_no');
			$this->octabook_msg91_ccode = get_option('octabook_msg91_ccode');
			$this->octabook_msg91_ccode_alph = get_option('octabook_msg91_ccode_alph');
			
			
	
	}

}
?>