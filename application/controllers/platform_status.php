<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scan live platform statuses vs software catalog.
 *
 * Task Scheduler (daily):
 *   curl.exe -k --silent "https://www.bnyfoodproducts.com/platform_status/scan?secret=<ADS_WEBHOOK_SECRET>"
 *   curl.exe -k --silent "https://www.bnyfoodproducts.com/platform_status/status?secret=<ADS_WEBHOOK_SECRET>"
 */
class Platform_status extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('businesslogic/platform_status_bl');
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
		$rep = $this->platform_status_bl->last_report();
		if (empty($rep)) {
			$rep = $this->platform_status_bl->scan();
		}
		$this->json_out(array(
			'ok' => true,
			'checked_at' => isset($rep['checked_at']) ? $rep['checked_at'] : date('Y-m-d H:i:s'),
			'issue_n' => isset($rep['issue_n']) ? (int)$rep['issue_n'] : 0,
			'topic' => isset($rep['topic']) ? (int)$rep['topic'] : 0,
			'issues' => isset($rep['issues']) ? $rep['issues'] : array()
		));
	}

	function scan()
	{
		if (!$this->job_ok()) {
			return;
		}
		set_time_limit(120);
		$rep = $this->platform_status_bl->scan();
		$this->json_out(array(
			'ok' => true,
			'checked_at' => isset($rep['checked_at']) ? $rep['checked_at'] : date('Y-m-d H:i:s'),
			'issue_n' => isset($rep['issue_n']) ? (int)$rep['issue_n'] : 0,
			'topic' => isset($rep['topic']) ? (int)$rep['topic'] : 0,
			'issues' => isset($rep['issues']) ? $rep['issues'] : array()
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

	private function json_out($data)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data));
	}
}
