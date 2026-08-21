<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Poll Shopee / Lazada / TikTok return, refund, and cancel lists.
 * Inserts into return tables, then sets the existing CN flags
 * (Shopee is_return, Lazada returned/canceled row, TikTok CANCELLED row)
 * so tax invoice vs credit note stays on the original SQL.
 *
 * Task Scheduler: every 15 minutes.
 *
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_returns/poll
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_returns/poll/shopee
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_returns/poll/lazada
 *   curl.exe -s -H "X-BNY-Ads-Secret: <ADS_WEBHOOK_SECRET>" https://www.bnyfoodproducts.com/platform_returns/poll/tiktok
 *
 * Query secret is accepted for simple schtasks URLs:
 *   .../platform_returns/poll?secret=<ADS_WEBHOOK_SECRET>
 */
class Platform_returns extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('businesslogic/platform_returns_bl');
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
		$this->json_out(array(
			'ok' => true,
			'checked_at' => date('Y-m-d H:i:s'),
			'counts' => $this->platform_returns_bl->snapshot()
		));
	}

	function poll()
	{
		if (!$this->job_ok()) {
			return;
		}
		$only = strtolower(trim((string)$this->uri->segment(3)));
		if ($only !== '' && $only !== 'shopee' && $only !== 'lazada' && $only !== 'tiktok') {
			$this->json_out(array('ok' => false, 'error' => 'unknown_platform'), 400);
			return;
		}
		$days = (int)$this->input->get('days');
		if ($days < 1) {
			$days = 14;
		}
		$out = $this->platform_returns_bl->poll($only, $days);
		$fail = 0;
		foreach ($out as $row) {
			if (isset($row['ok']) && !$row['ok']) {
				$fail++;
			}
		}
		$this->json_out(array(
			'ok' => ($fail === 0),
			'checked_at' => date('Y-m-d H:i:s'),
			'days' => $days,
			'platforms' => $out
		));
	}

	function apply()
	{
		if (!$this->job_ok()) {
			return;
		}
		$only = strtolower(trim((string)$this->uri->segment(3)));
		if ($only !== '' && $only !== 'shopee' && $only !== 'lazada' && $only !== 'tiktok') {
			$this->json_out(array('ok' => false, 'error' => 'unknown_platform'), 400);
			return;
		}
		$out = $this->platform_returns_bl->apply_existing($only);
		$this->json_out(array(
			'ok' => true,
			'checked_at' => date('Y-m-d H:i:s'),
			'platforms' => $out
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
