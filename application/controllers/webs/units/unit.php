<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Webs → Units (shop-scoped, bilingual).
 */
class Unit extends Auth_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');
	}

	public function manage()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$data = array(
			'shopid_en' => $sess_shop_id,
			'admin_lang' => admin_lang()
		);
		$arr_input = array('title' => alang('units', 'Units'));
		$arr_js = array(
			'unit_manage' => base_url().'resources/js/webs/units/unit_manage.js'
		);
		$this->view_util->load_view_main('webs/units/unit/manage', $data, array(), $arr_js, $arr_input, MENU_WEBS_UNITS);
	}

	function list_ajax()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/shop_unit/list_by_shop', array(
			'ShopID' => $sess_shop_id,
			'include_inactive' => 1
		));

		$rows = array();
		if (isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])) {
			foreach ($arr['Data'] as $row) {
				$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_shop_unit_id']);
				$row['display_name'] = bilingual($row, 'name');
				$rows[] = $row;
			}
		}
		echo json_encode(array(
			'Status' => 'Success',
			'list_data' => $rows,
			'admin_lang' => admin_lang()
		));
	}

	function get_ajax()
	{
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/shop_unit/get_by_id', array('id_en' => $id_en));
		$row = array();
		if (isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])) {
			$row = $arr['Data'];
			$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_shop_unit_id']);
			$row['display_name'] = bilingual($row, 'name');
		}
		echo json_encode(array('Status' => 'Success', 'unit_data' => $row));
	}

	function save_ajax()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$is_add = (int)$this->input->post('is_add');
		$payload = array(
			'name_th' => trim((string)$this->input->post('name_th')),
			'name_en' => trim((string)$this->input->post('name_en')),
			'sort_order' => (int)$this->input->post('sort_order'),
			'Status' => (int)$this->input->post('Status') ? 1 : 0
		);

		if ($payload['name_th'] === '' && $payload['name_en'] === '') {
			echo json_encode(array('Status' => 'Fail', 'Message' => 'Name required'));
			return;
		}

		if ($is_add === 1) {
			$payload['ShopID'] = $sess_shop_id;
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/shop_unit/unit_add', $payload);
		} else {
			$payload['id_en'] = $this->input->post('id_en');
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/shop_unit/unit_edit', $payload);
		}

		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if (!$ok) {
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Save failed');
		}
		echo json_encode(array('Status' => $ok ? 'Success' : 'Fail', 'Message' => $msg));
	}

	function del_ajax()
	{
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('GET', 'webs/shop_unit/unit_del/'.$id_en);
		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if (!$ok) {
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Delete failed');
		}
		echo json_encode(array('Status' => $ok ? 'Success' : 'Fail', 'Message' => $msg));
	}
}
