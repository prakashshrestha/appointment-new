<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] ); 
 	if( file_exists( $parse_uri[0] . 'wp-load.php' ) ){
		require_once( $parse_uri[0] . 'wp-load.php' );
	}
/* error_reporting(E_ALL);
ini_set('display_errors', 1);*/
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
		
		$GcclientID = get_option('oct_gc_client_id');
		$GcclientSecret = get_option('oct_gc_client_secret');
		$GcEDvalue = get_option('oct_gc_status');
		
		$client = new Google_Client();
		$client->setApplicationName("OctaBook Google Calender");
		$client->setClientId($GcclientID);
		$client->setClientSecret($GcclientSecret);
		$client->setRedirectUri(get_option('oct_gc_admin_url'));
		$client->setDeveloperKey($GcclientID);
		$client->setScopes(array('https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/calendar','https://www.google.com/calendar/feeds/'));
		$client->setAccessType('offline');
		$client->setApprovalPrompt( 'force' );
		
		$service = new Google_CalendarService($client);
		$provider_gc_data = get_option('oct_gc_token');
		if(sizeof((array)$provider_gc_data) > 0){
			$accesstoken = json_decode($provider_gc_data);
			$client->setAccessToken($provider_gc_data);
			if ($accesstoken) {
				if ($client->isAccessTokenExpired()) {
					$client->refreshToken($accesstoken->refresh_token);				
				}
			}

			/* $client->revokeToken($provider_gc_data[0]); */					
			if ($client->getAccessToken()){
				$calendarList = $service->calendarList->listCalendarList();
				$allCalenders = array();
				foreach($calendarList['items'] as $singleItem){
					if($singleItem['accessRole']=='owner'){
						$allCalenders[]=array($singleItem['id']=>$singleItem['summary'].'##==##'.$singleItem['id']);
					}
				}	
			}
		}else{
			$allCalenders = array();
		}
echo json_encode($allCalenders);die();		
?>