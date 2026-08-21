<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_auth_bl{
	public function __construct(){
		$this->CI =& get_instance();


		$this->CI->load->library('util/encryption_util');
		$this->CI->load->library('util/json_util');
		$this->CI->load->library('business_logic/api_log_bl');
		$this->CI->load->model('api_authen_model');
		$this->CI->load->model('api_log_model');



	}

	function get_header(){

		$header = $this->CI->input->request_headers();
	    //print_r($header);
	    //echo $header['Token'];
	   /* $headers = apache_request_headers();

		foreach ($headers as $header => $value) {
		    echo "$header: $value <br />\n";
		}*/

		  $http_method = $_SERVER['REQUEST_METHOD'];
		  $api_token = $header['Token'];

		  $arr_header = array(
		  	'http_method' => $http_method,
		  	'api_token' => $api_token
		  );

		  return $arr_header;
	}

	function create_token($user_login){

		$ip = $this->get_client_ip();
		$authenid = $user_login['ApiAuthenID'];
		$datenow = date('Y-m-d h:i:s');
		$makeencode =  $ip.'\|/'.$datenow.'\|/'.API_TOKEN_KEY.'\|/'.$authenid;
		$key_encript = $this->CI->encryption_util->encrypt_decrypt('encrypt',$makeencode,API_TOKEN_KEY2,API_TOKEN_KEY3);

		return $key_encript;

	}

	function authen_token_test($token){
		 $data_json = $this->CI->json_util->make_json('Check Authen','Success','','Authen Success',$token); 
		 return json_decode($data_json['view'],true);
	}

	function authen_token($token){


		$de_cript = $this->CI->encryption_util->encrypt_decrypt('decrypt',$token,API_TOKEN_KEY2,API_TOKEN_KEY3);
		//echo ">>".$de_cript;
		if(strlen($de_cript) > 0){
			$arr_decript = explode('\|/',$de_cript);
			$ip = $this->get_client_ip();

			$arr_date = explode(' ',$arr_decript[1]);
			$datenow = date('Y-m-d');

			if(($arr_decript[0] == $ip) and ($arr_date[0] == $datenow) and ($arr_decript[2] == API_TOKEN_KEY)){

				$chk_token = $this->CI->api_authen_model->select_by_token_id($token,$arr_decript[3],false);	
				if(!empty($chk_token)){
					$token_date = $chk_token['token_cdate'];

	    			$start_date = new DateTime($token_date);
					$since_start = $start_date->diff(new DateTime(Date('Y-m-d h:i:s')));
					
					$token_expire = $since_start->i;
					//echo $token_expire.' minutes<br>';

					if($token_expire <= TOKEN_PERIOD_LIMIT){

						$arr_call_cnt = $this->CI->api_log_model->check_call_cnt($token,API_SAMPLING_LIMIT);

						if(!empty($arr_call_cnt)){
							if($arr_call_cnt['access_cnt'] <= API_FRQUENCY_LIMIT){

						       $data_json = $this->CI->json_util->make_json('Check Authen','Success','','Authen Success',$token); 
						       $this->CI->api_log_bl->create_log('Check Authen','Success',current_url(),$data_json['log'],$token,$arr_decript[3]); 
						       return json_decode($data_json['view'],true);

							}else{

						       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API FRQUENCY OVER LIMIT',$token); 
						       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,$arr_decript[3]); 
						       return json_decode($data_json['view'],true);
	                           //sleep(10);
								usleep(1000*($arr_call_cnt['access_cnt']-API_FRQUENCY_LIMIT)*1000);
								
							}
						}else{ //if(!empty($arr_call_cnt))

					       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API FRQUENCY LOG Not Found',$token); 
					       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,$arr_decript[3]); 
						    return json_decode($data_json['view'],true);
						}

					}else{ //if($token_expire <= TOKEN_PERIOD_LIMIT)

				       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API Token Expire',$token); 
				       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,$arr_decript[3]); 
						return json_decode($data_json['view'],true);
					}

				}else{

			       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API Token Not Found',$token); 
			       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,$arr_decript[3]); 
					return json_decode($data_json['view'],true);
				}		

			}else{

		       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API Token Invalid',$token); 
		       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,$arr_decript[3]); 
				return json_decode($data_json['view'],true);
			}
		}else{ //if(strlen($de_cript) > 0)

	       $data_json = $this->CI->json_util->make_json('Check Authen','FailToken','','Authen API Token Invalid',$token); 
	       $this->CI->api_log_bl->create_log('Check Authen','FailToken',current_url(),$data_json['log'],$token,""); 
			return json_decode($data_json['view'],true);
		}	

	}

	function de_token($token){

		$de_cript = $this->CI->encryption_util->encrypt_decrypt('decrypt',$token,API_TOKEN_KEY2,API_TOKEN_KEY3);
		return $de_cript;
	}

	
	
	function check_api_exists($token,$url){

		//check user call time
		$arr_call_cnt = $this->CI->api_log_model->check_call_cnt($token,API_FRQUENCY_LIMIT);
		//print_r($arr_call_cnt);
		//Number time to access in 10 minutes
		if($arr_call_cnt['access_cnt'] >= API_FRQUENCY_LIMIT ){

			$arr_res = array(
			  'State' => 'Authen',		
	          'Status' => 'Fail',
	          'Data' => '',
	          'Description' => 'Authen access more than '.AUTH_NUMBER_ACCESS." in 10 minutes",
	          'Token' => $token
	        );
	        $data_json = json_encode($arr_res,JSON_UNESCAPED_UNICODE);

			$this->CI->api_log_bl->create_log('Authen','Fail',$url,$data_json,$token);

			echo $data_json;
			return false;
		}else{
			$arr_chk_tokentime = $this->CI->api_authen_model->chk_tokentime($token);
			if(!empty($arr_chk_tokentime)){
				//Token time >= 3 Days
				if($arr_chk_tokentime['tokentime'] >= 0){
					$arr_res = array(
			          'Status' => 'Authen',
			          'Status' => 'Fail',
			          'Data' => '',
			          'Description' => 'Authen access token timeout',
			          'Token' => $token
			        );
			        $data_json = json_encode($arr_res,JSON_UNESCAPED_UNICODE);

					$this->CI->api_log_bl->create_log('Authen','Fail',$url,$data_json,$token);

					echo $data_json;
					return false;
				}else{
					return true;
				}

			}else{

				$arr_res = array(
		          'Status' => 'Authen',
		          'Status' => 'Fail',
		          'Data' => '',
		          'Description' => 'Authen access invalid',
		          'Token' => $token
		        );
		        $data_json = json_encode($arr_res,JSON_UNESCAPED_UNICODE);

				$this->CI->api_log_bl->create_log('Authen','Fail',$url,$data_json,$token);

				echo $data_json;
				return false;
			} 
		}

		
	   
	}

	private function get_client_ip()
	{
		$client_ip = '';
		
		if (isset($_SERVER['REMOTE_ADDR'])){
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}else{
			//redirect('http://www.phuketgazette.net/pagenotfound.asp');
			echo '<br/><br/> client_ip : '.$client_ip.'   CAN NOT GET client ip !!!.<br/>';
		}
		
		return $client_ip;
	}
	

	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */