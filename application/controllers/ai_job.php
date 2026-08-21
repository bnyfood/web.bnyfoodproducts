<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ai_job extends CI_Controller
{
	function distill()
	{
		$secret = defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '';
		$given = $this->input->get_request_header('X-BNY-Ads-Secret', true);
		if ($secret === '' || !hash_equals((string)$secret, (string)$given)) {
			$this->output->set_status_header(401);
			$this->output->set_content_type('application/json', 'utf-8');
			$this->output->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
			return;
		}
		$this->load->library('businesslogic/chat_learn_bl');
		$out = $this->chat_learn_bl->distill_all();
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode(array('ok' => true, 'playbooks' => $out)));
	}

	function sync_chat()
	{
		if (!$this->job_ok()) {
			return;
		}
		$this->load->library('businesslogic/chat_platform_bl');
		$out = $this->chat_platform_bl->sync_all();
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode(array('ok' => true, 'sync' => $out)));
	}

	private function job_ok()
	{
		$secret = defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '';
		$given = $this->input->get_request_header('X-BNY-Ads-Secret', true);
		if ($secret === '' || !hash_equals((string)$secret, (string)$given)) {
			$this->output->set_status_header(401);
			$this->output->set_content_type('application/json', 'utf-8');
			$this->output->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
			return false;
		}
		return true;
	}
}
