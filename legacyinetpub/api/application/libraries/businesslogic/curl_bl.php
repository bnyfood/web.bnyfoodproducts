<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curl_bl{
	public function __construct(){
		$this->CI =& get_instance();

	}

	function curl_sms($msisdn,$message,$sender,$force){


		require_once __DIR__ . "/../../../resources/api/sms.php";

		$apiKey = SMS_API_KEY;
		$apiSecretKey = SMS_SECRET_KEY;

		$sms = new SMS($apiKey, $apiSecretKey); 

		$body = [
		    'msisdn' => $msisdn,
		    'message' => $message,
		     'sender' => $sender,
		    // 'scheduled_delivery' => '',
		     'force' => $force
		];
		$res = $sms->sendSMS($body);

		if ($res->httpStatusCode == 201) {
		    echo "Succes";
		    var_dump($res);
		} else {
		    echo "Error";
		    var_dump($res);
		}
	}
	
	
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */