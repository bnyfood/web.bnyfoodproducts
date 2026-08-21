<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_log_bl{
	public function __construct(){
		$this->CI =& get_instance();
		
		$this->CI->load->model('api_log_model');


	}

	
	
	function create_log($api_state,$api_status,$api_url,$api_return,$token,$ApiAuthenID=NULL){
					
	   $data_api = array(
	   	'ApiAuthenID' => $ApiAuthenID,
	   	'ApiState' => $api_state,
	   	'ApiStatus' => $api_status,
	   	'ApiCallUrl' => $api_url,
	   	'ApiReturn' => $api_return,
	   	'ApiToken' => $token,
	   	'ApiCdate' => DATE_TIME_NOW
	   );

	   $this->CI->api_log_model->insert($data_api);
	}
	

	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */