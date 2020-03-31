<?php 

  class octabook_nexmo{  
	 var $octabook_nexmo_apikey; 	 
	 var $octabook_nexmo_api_secret; 
	 var $octabook_nexmo_form; 
     
	 public function send_nexmo_sms($phone,$octabook_nexmo_text) {
		 $nexmo_api_key=$this->octabook_nexmo_apikey;
		 $octabook_nexmo_api_secret=$this->octabook_nexmo_api_secret;
		 $octabook_nexmo_form=$this->octabook_nexmo_form;
		 $queryinfo = array('api_key' => $nexmo_api_key, 'api_secret' => $octabook_nexmo_api_secret, 'to' => $phone, 'from' => $octabook_nexmo_form, 'text' => $octabook_nexmo_text);
		$url = 'https://rest.nexmo.com/sms/json?' . http_build_query($queryinfo);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		return $response;
	 } 
  }
?>