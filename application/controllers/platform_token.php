<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Token keeper for Shopee / Lazada / TikTok.
 *
 * Task Scheduler should run /refresh every 5 minutes.
 * Each run: SQL calendar refresh if due, then a live API ping.
 * If the access token is dead (even when refresh-token days still look fine),
 * it refreshes immediately — Lazada is not gated on the 10-day calendar.
 *
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_token/refresh
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_token/status
 *
 * status shows SQL leftover plus last live ping (api_ok). If that ping is older
 * than 4 minutes, status runs the same keep+probe path as /refresh so this URL
 * can be the scheduled job. /refresh always keeps.
 *
 * One platform:
 *   .../platform_token/refresh/shopee
 *   .../platform_token/refresh/lazada
 *   .../platform_token/refresh/tiktok
 *
 * Query secret is accepted for simple schtasks URLs, but the header is preferred.
 */
class Platform_token extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('businesslogic/platform_token_bl');
	}

	function index()
	{
		$this->status();
	}

	function status()
	{
		if (!$this->job_ok()) {
			return;
		}
		$probe = $this->platform_token_bl->last_probe();
		$age = time() - (isset($probe['at']) ? (int)$probe['at'] : 0);
		if (empty($probe) || $age > 240) {
			set_time_limit(90);
			$this->platform_token_bl->refresh_due('');
		}
		$this->json_out(array(
			'ok' => true,
			'checked_at' => date('Y-m-d H:i:s'),
			'tokens' => $this->platform_token_bl->snapshot()
		));
	}

	function refresh()
	{
		if (!$this->job_ok()) {
			return;
		}
		set_time_limit(90);
		$only = strtolower(trim((string)$this->uri->segment(3)));
		if ($only !== '' && $only !== 'shopee' && $only !== 'lazada' && $only !== 'tiktok') {
			$this->json_out(array('ok' => false, 'error' => 'unknown_platform'), 400);
			return;
		}
		$out = $this->platform_token_bl->refresh_due($only);
		$hit = 0;
		$fail = 0;
		foreach ($out as $row) {
			if (!empty($row['hit_platform'])) {
				$hit++;
			}
			if (isset($row['action']) && $row['action'] === 'failed') {
				$fail++;
			}
		}
		$this->json_out(array(
			'ok' => ($fail === 0),
			'checked_at' => date('Y-m-d H:i:s'),
			'hit_platform' => $hit,
			'tokens' => $out
		));
	}

	private function job_ok()
	{
		$secret = defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '';
		$header = $this->input->get_request_header('X-BNY-Ads-Secret', true);
		$query = $this->input->get('secret');
		$given = ($header !== false && $header !== null && $header !== '') ? $header : $query;
		if ($secret === '' || !is_string($given) || !hash_equals((string)$secret, (string)$given)) {
			$this->output->set_status_header(401);
			$this->output->set_content_type('application/json', 'utf-8');
			$this->output->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
			return false;
		}
		return true;
	}

	private function json_out($data, $code = 200)
	{
		if ($code !== 200) {
			$this->output->set_status_header($code);
		}
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data));
	}
}