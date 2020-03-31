<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] ); 
 	if( file_exists( $parse_uri[0] . 'wp-load.php' ) ){
		require_once( $parse_uri[0] . 'wp-load.php' );
	}

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';


function delete_google_cal_event($calendarId,$provider_access_token,$eventid){
	$GcclientID = get_option('oct_gc_client_id');
	$GcclientSecret = get_option('oct_gc_client_secret');
	$GcEDvalue = get_option('oct_gc_status');
	
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
		$allevents_provider = $calP->events->delete($calendarId,$eventid);
	}				
}
/* Trigger Delete Event Function - Google Calender */
if(isset($_REQUEST['eid'],$_REQUEST['pid']) && $_REQUEST['eid']!='' && $_REQUEST['pid']!=''){	
	$provider_gc_id = get_option('oct_gc_id');
	$provider_gc_data = get_option('oct_gc_token');
	if($provider_gc_id!='' && $provider_gc_data!=''){
		$provider_events  =	delete_google_cal_event($provider_gc_id,$provider_gc_data,$_REQUEST['eid']);	
	}	
} ?>