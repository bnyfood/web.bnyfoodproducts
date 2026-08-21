<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_bl{
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->helper('cookie');
		$this->CI->load->library('util/encryption_util');
	}

	
	function check_session_exists(){

		//$this->CI->config->set_item('sess_expiration', 7200);
		
		$user_id = $this->CI->session->userdata(SESSION_PREFIX.'user_id');
		$usergroup_id = $this->CI->session->userdata(SESSION_PREFIX.'usergroup_id');
		$shop_id = $this->CI->session->userdata(SESSION_PREFIX.'shop_id');
		//$token = $this->CI->session->userdata('token');
		$token_en = get_cookie(COOKIE_PREFIX.'token');
		$token = $this->CI->encryption_util->decrypt_ssl($token_en);
		//echo ">>>>".$token."<<<<";
		$user_id_de = $this->CI->encryption_util->decrypt_ssl($user_id);
		$usergroup_id_de = $this->CI->encryption_util->decrypt_ssl($usergroup_id);
		$shop_id_de = $this->CI->encryption_util->decrypt_ssl($shop_id);
		//echo "YES>>user_id>>:".$user_id_de.">>usergroup_id>>:".$usergroup_id_de.">>>>:".$token.">>shop_id>>:".$shop_id_de;
		
	   if((empty($user_id)) or ($usergroup_id == '0') or(empty($token)))
	   {
			redirect(base_url().'users/login_with_google','refresh');
	   	//echo "NO>>user_id>>:".$user_id.">>usergroup_id>>:".$usergroup_id.">>>>:".$token.">>shop_id>>:".$shop_id;
	   } 
	   
	}

}	