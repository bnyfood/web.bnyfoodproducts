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
			$this->remember_url_before_login();
			redirect(base_url().'users/login_with_google','refresh');
	   	//echo "NO>>user_id>>:".$user_id.">>usergroup_id>>:".$usergroup_id.">>>>:".$token.">>shop_id>>:".$shop_id;
	   }

	   // Ensure countdown has a start; do not reset on every page (idle until refresh/login)
	   // TEMP test mode: always reset to short TTL so popup can be tested quickly
	   if (defined('SESSION_COUNTDOWN_TEST_SEC') && (int)SESSION_COUNTDOWN_TEST_SEC > 0) {
			$this->touch_session_expiry();
	   } else {
			$expire_at = (int)$this->CI->session->userdata(SESSION_PREFIX.'session_expire_at');
			if ($expire_at <= 0) {
				$this->touch_session_expiry();
			}
	   }
	}

	/**
	 * Reset session expiry to now + sess_expiration seconds.
	 */
	function touch_session_expiry(){
		// TEMP short TTL for re-login popup testing
		if (defined('SESSION_COUNTDOWN_TEST_SEC') && (int)SESSION_COUNTDOWN_TEST_SEC > 0) {
			$ttl = (int)SESSION_COUNTDOWN_TEST_SEC;
			$this->CI->session->set_userdata(SESSION_PREFIX.'session_expire_at', time() + $ttl);
			return $ttl;
		}
		// Align with API TOKEN_PERIOD_LIMIT (180 minutes) when available
		$ttl = 180 * 60;
		if (defined('TOKEN_PERIOD_LIMIT')) {
			$ttl = (int)TOKEN_PERIOD_LIMIT * 60;
		} else {
			$cfg = (int)$this->CI->config->item('sess_expiration');
			if ($cfg > 0) {
				$ttl = $cfg;
			}
		}
		$this->CI->session->set_userdata(SESSION_PREFIX.'session_expire_at', time() + $ttl);
		return $ttl;
	}

	/**
	 * Seconds remaining until client-side session timeout (0 if expired/missing).
	 */
	function get_session_remaining(){
		$expire_at = (int)$this->CI->session->userdata(SESSION_PREFIX.'session_expire_at');
		if ($expire_at <= 0) {
			return 0;
		}
		$left = $expire_at - time();
		return ($left > 0) ? $left : 0;
	}

	function is_session_alive(){
		$user_id = $this->CI->session->userdata(SESSION_PREFIX.'user_id');
		$usergroup_id = $this->CI->session->userdata(SESSION_PREFIX.'usergroup_id');
		$token_en = get_cookie(COOKIE_PREFIX.'token');
		$token = $this->CI->encryption_util->decrypt_ssl($token_en);
		if (empty($user_id) || $usergroup_id == '0' || empty($token)) {
			return false;
		}
		return $this->get_session_remaining() > 0;
	}

	/**
	 * Keep the page the user was on so Google re-login can return there.
	 * Cookie (not session) — session is often already empty when this runs.
	 */
	private function remember_url_before_login(){
		if ($this->CI->input->is_ajax_request()) {
			return;
		}

		$uri = trim((string)$this->CI->uri->uri_string(), '/');
		if ($uri === '') {
			return;
		}

		$skip_prefixes = array(
			'users/login_with_google',
			'users/logined_with_google',
			'users/login_with_google_pop',
			'users/logout',
			'users/google_login',
			'users/login',
			'users/login_phone',
			'users/session_status',
			'users/session_refresh',
			'users/ajax_relogin',
		);
		foreach ($skip_prefixes as $prefix) {
			if (strpos($uri, $prefix) === 0) {
				return;
			}
		}

		$qs = $this->CI->input->server('QUERY_STRING');
		$path = $uri;
		if (!empty($qs)) {
			$path .= '?'.$qs;
		}

		$this->CI->input->set_cookie(array(
			'name'   => COOKIE_PREFIX.'redirect_after_login',
			'value'  => $path,
			'expire' => 3600,
			'path'   => '/',
			'secure' => FALSE
		));
	}

}
