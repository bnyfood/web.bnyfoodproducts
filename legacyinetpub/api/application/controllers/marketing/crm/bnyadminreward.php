<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bnyadminreward extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		$this->load->library('businesslogic/api_log_bl');
		$this->load->library('businesslogic/api_auth_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->model('web_bny_gift_model');
		$this->load->model('web_bny_reward_model');
	}

	function bny_gift_search()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$bny_gift_search = $this->input->post('bny_gift_search');
			$sortby = $this->input->post('sortby');
			$sorttype = $this->input->post('sorttype');
			$offset = $this->input->post('offset');
			$per_page = $this->input->post('per_page');

			$data = $this->web_bny_gift_model->select_by_search($bny_gift_search, $per_page, $offset, $sortby, $sorttype);

			if (!empty($data)) {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data, 'Select Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data, 'Select No data', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function bny_gift_add()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$web_bny_gift_pic = $this->input->post('web_bny_gift_pic');
			$web_bny_gift_detail = $this->input->post('web_bny_gift_detail');
			$web_bny_gift_now = $this->input->post('web_bny_gift_now');

			$arr_data = array(
				'web_bny_gift_pic' => $web_bny_gift_pic,
				'web_bny_gift_detail' => $web_bny_gift_detail,
				'web_bny_gift_now' => ($web_bny_gift_now == '1' || $web_bny_gift_now === 1) ? 1 : 0
			);

			$data_re = $this->web_bny_gift_model->insert($arr_data);

			if (!empty($data_re)) {
				$data_json = $this->json_util->make_json('Insert data', 'Success', $data_re, 'Insert Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Insert data', 'Fail', $data_re, 'Insert Unsuccess', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function bny_gift_edit()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$id_en = $this->input->post('id_en');
			$web_bny_gift_pic = $this->input->post('web_bny_gift_pic');
			$web_bny_gift_detail = $this->input->post('web_bny_gift_detail');
			$web_bny_gift_now = $this->input->post('web_bny_gift_now');

			$arr_data = array(
				'web_bny_gift_pic' => $web_bny_gift_pic,
				'web_bny_gift_detail' => $web_bny_gift_detail,
				'web_bny_gift_now' => ($web_bny_gift_now == '1' || $web_bny_gift_now === 1) ? 1 : 0
			);

			$data_re = $this->web_bny_gift_model->update($arr_data, $id_en);

			if ($data_re !== false && $data_re !== null) {
				$data_json = $this->json_util->make_json('Update data', 'Success', $data_re, 'Update Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Update data', 'Fail', $data_re, 'Update Unsuccess', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function get_by_id()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$web_bny_gift_id = $this->uri->segment(5);
			$data_re = $this->web_bny_gift_model->select_by_id($web_bny_gift_id);

			if (!empty($data_re)) {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data_re, 'Select Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data_re, 'Select No data', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function get_gift_lasted()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {
			$data_re = $this->web_bny_gift_model->select_lasted();
			if (!empty($data_re)) {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data_re, 'Select Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data_re, 'Select No data', $arr_header['api_token']);
			}
			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function lucky_winner_search()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$lucky_search = $this->input->post('lucky_search');
			$sortby = $this->input->post('sortby');
			$sorttype = $this->input->post('sorttype');
			$offset = $this->input->post('offset');
			$per_page = $this->input->post('per_page');

			$data = $this->web_bny_reward_model->select_lucky_list($lucky_search, $per_page, $offset, $sortby, $sorttype);

			if (!empty($data)) {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data, 'Select Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Select data', 'Success', $data, 'Select No data', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function update_gift_send()
	{
		$arr_header = $this->api_auth_bl->get_header();
		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);

		if ($chk_auth['Status'] == "Success") {

			$web_bny_reward_id = $this->input->post('web_bny_reward_id');
			$web_bny_gift_send = $this->input->post('web_bny_gift_send');

			$data_re = $this->web_bny_reward_model->update_gift_send($web_bny_reward_id, $web_bny_gift_send);

			if ($data_re !== false && $data_re !== null) {
				$data_json = $this->json_util->make_json('Update data', 'Success', $data_re, 'Update Success', $arr_header['api_token']);
			} else {
				$data_json = $this->json_util->make_json('Update data', 'Fail', $data_re, 'Update Unsuccess', $arr_header['api_token']);
			}

			echo $data_json['view'];
		} else {
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}
}
