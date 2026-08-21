<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Alert badges: token expire from platform_token, status change from platform_status scan.
 */
class Alerts_bl
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('businesslogic/platform_token_bl');
		$this->CI->load->library('businesslogic/platform_status_bl');
	}

	function summary($force = false)
	{
		if ($force) {
			$this->CI->platform_token_bl->refresh_due('');
			$this->CI->platform_status_bl->scan();
		}
		$token = $this->CI->platform_token_bl->alert_summary();
		$status = $this->CI->platform_status_bl->alert_slice();
		$token_shops = isset($token['token_shops']) ? (int)$token['token_shops'] : 0;
		$status_n = isset($status['issue_n']) ? (int)$status['issue_n'] : 0;
		$topics = 0;
		if ($token_shops > 0) {
			$topics++;
		}
		if ($status_n > 0) {
			$topics++;
		}
		$checked = '';
		if (!empty($token['checked_at'])) {
			$checked = $token['checked_at'];
		}
		if (!empty($status['checked_at']) && ($checked === '' || strcmp($status['checked_at'], $checked) > 0)) {
			$checked = $status['checked_at'];
		}
		return array(
			'ok' => true,
			'checked_at' => $checked,
			'topics' => $topics,
			'token_shops' => $token_shops,
			'shops' => isset($token['shops']) ? $token['shops'] : array(),
			'status_issues' => $status_n,
			'status' => $status
		);
	}
}
