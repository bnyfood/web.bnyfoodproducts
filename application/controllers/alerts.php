<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alerts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->auth_bl->check_session_exists();
		$this->load->helper('admin_lang');
		$lang = admin_lang();
		$ci_lang = ($lang === 'en') ? 'english' : 'thai';
		$this->config->set_item('language', $ci_lang);
		$this->lang->load('admin', $ci_lang);
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/shopee_bl');
		$this->load->library('businesslogic/alerts_bl');
	}

	function index()
	{
		$sum = $this->alerts_bl->summary(false);
		$data = array(
			'summary' => $sum,
			'shopee_link' => $this->shopee_bl->get_authenticatrion_link()
		);
		$this->render('alerts/index', $data, 'อะเลิร์ท', MENU_ALERTS);
	}

	function token_expire()
	{
		$sum = $this->alerts_bl->summary(false);
		$data = array(
			'summary' => $sum,
			'shopee_link' => $this->shopee_bl->get_authenticatrion_link()
		);
		$this->render('alerts/token_expire', $data, 'Token expire', MENU_ALERTS_TOKEN);
	}

	function status_change()
	{
		$sum = $this->alerts_bl->summary(false);
		$data = array(
			'summary' => $sum
		);
		$this->render('alerts/status_change', $data, 'Status change', MENU_ALERTS_STATUS);
	}

	function summary()
	{
		$out = $this->alerts_bl->summary(false);
		$this->json_out(array(
			'ok' => true,
			'checked_at' => isset($out['checked_at']) ? $out['checked_at'] : '',
			'topics' => isset($out['topics']) ? (int)$out['topics'] : 0,
			'token_shops' => isset($out['token_shops']) ? (int)$out['token_shops'] : 0,
			'status_issues' => isset($out['status_issues']) ? (int)$out['status_issues'] : 0,
			'shops' => isset($out['shops']) ? $out['shops'] : array()
		));
	}

	private function render($view, $data, $title, $menu_id)
	{
		$arr_input = array('title' => $title);
		$arr_css = array(
			'monitor_overview' => base_url().'resources/css/monitor_overview.css',
			'alerts' => base_url().'resources/css/alerts.css'
		);
		$this->view_util->load_view_main($view, $data, $arr_css, array(), $arr_input, $menu_id);
	}

	private function json_out($data)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data));
	}
}
