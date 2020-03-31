<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] ); 
 	if( file_exists( $parse_uri[0] . 'wp-load.php' ) ){
		require_once( $parse_uri[0] . 'wp-load.php' );
	}

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
require_once(dirname(dirname(dirname(__FILE__))).'/objects/class_provider.php');
$staff = new octabook_staff();
$staff_id = $_GET['pid'];
$staff_redirecturi = $staff->get_staff_option("gc_admin_url",$staff_id);
function delete_google_cal_event($calendarId,$provider_access_token,$eventid,$staff_redirecturi,$GcclientID,$GcclientSecret,$GcEDvalue){
	
	
	$clientP = new Google_Client();
	$clientP->setApplicationName("OctaBook Google Calender");
	$clientP->setClientId($GcclientID);
	$clientP->setClientSecret($GcclientSecret);
	$clientP->setRedirectUri($staff_redirecturi);
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
	
	$GcclientID = $staff->get_staff_option("gc_client_id",$staff_id);
	$GcclientSecret = $staff->get_staff_option("gc_client_secret",$staff_id);
	$GcEDvalue = $staff->get_staff_option("gc_status",$staff_id);
	$provider_gc_id = $staff->get_staff_option("gc_id",$staff_id);
	$provider_gc_data = $staff->get_staff_option("gc_token",$staff_id);
	if($provider_gc_id!='' && $provider_gc_data!=''){
		$provider_events  =	delete_google_cal_event($provider_gc_id,$provider_gc_data,$_REQUEST['eid'],$staff_redirecturi,$GcclientID,$GcclientSecret,$GcEDvalue);	
	}	
} ?>