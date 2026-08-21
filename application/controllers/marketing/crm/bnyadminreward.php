<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bnyadminreward extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/upload_bl');
		$this->load->library('util/encryption_util');
		$this->load->helper(array('form', 'url', 'file'));
		$this->load->model('web_bny_gift_model');
		$this->load->model('biggrill_data_model');
		$current_method = $this->router->fetch_method();
		if (!in_array($current_method, array('get_gift_lasted', 'gift_pic'), true)) {
			$this->auth_bl->check_session_exists();
		}
	}

	private function gift_pic_storage_path()
	{
		return APP_STORE_PATH . '/uploads/reward/';
	}

	private function gift_pic_base_url()
	{
		return base_url('marketing/crm/bnyadminreward/gift_pic/');
	}

	public function gift_pic()
	{
		$filename = $this->uri->segment(5);
		if ($filename === false || $filename === '') {
			show_404();
			return;
		}

		$filename = basename(urldecode($filename));
		$filepath = $this->gift_pic_storage_path() . $filename;

		if (!is_file($filepath)) {
			show_404();
			return;
		}

		$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
		$mime_map = array(
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif'
		);
		$content_type = isset($mime_map[$ext]) ? $mime_map[$ext] : 'application/octet-stream';

		header('Content-Type: ' . $content_type);
		header('Content-Length: ' . filesize($filepath));
		readfile($filepath);
		exit;
	}

	private function upload_gift_pic()
	{
		if (empty($_FILES['web_bny_gift_pic']['name'])) {
			return '';
		}

		// upload_bl->upload_file_pic_path ต่อ APP_STORE_PATH ให้อยู่แล้ว — ส่งแค่ ./uploads/reward/
		$pic_name = $this->upload_bl->upload_file_pic_path('./uploads/reward/', 'web_bny_gift_pic');
		if ($pic_name === '') {
			return FALSE;
		}

		return $pic_name;
	}

	public function bny_gift_list()
	{
		$add_alt = $this->session->flashdata('add_bny_gift');
		$edit_alt = $this->session->flashdata('edit_bny_gift');
		$delete_alt = $this->session->flashdata('delete_bny_gift');
		$import_alt = $this->session->flashdata('import_biggrill');
		$active_tab = $this->input->get('tab') === 'import' ? 'import' : 'reward';

		$data_search = array(
			'bny_gift_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 0,
			'per_page' => 5
		);

		$arr_gifts = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/bny_gift_search', $data_search);

		$data = array(
			'arr_gifts' => !empty($arr_gifts['Data']) ? $arr_gifts['Data'] : array(),
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'delete_alt' => $delete_alt,
			'import_alt' => $import_alt,
			'active_tab' => $active_tab,
			'data_search' => $data_search,
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);

		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$arr_js = array(
			'morecontent' => base_url() . "resources/js/morecontent/marketing/crm/bny_gift_list.js",
			'table_load_sort' => base_url() . "resources/js/table_load_sort.js"
		);

		$arr_css = array(
			'site_new' => base_url() . "assets/css/site_new.css"
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/bny_gift_list', $data, $arr_css, $arr_js, $arr_input, 10068);
	}

	public function bny_gift_list_search()
	{
		$add_alt = $this->session->flashdata('add_bny_gift');
		$edit_alt = $this->session->flashdata('edit_bny_gift');
		$delete_alt = $this->session->flashdata('delete_bny_gift');
		$import_alt = $this->session->flashdata('import_biggrill');
		$bny_gift_search = $this->input->post('bny_gift_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'bny_gift_search' => $bny_gift_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5
		);

		$arr_gifts = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/bny_gift_search', $data_search);

		$data = array(
			'arr_gifts' => !empty($arr_gifts['Data']) ? $arr_gifts['Data'] : array(),
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'delete_alt' => $delete_alt,
			'import_alt' => $import_alt,
			'active_tab' => 'reward',
			'data_search' => $data_search,
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);

		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$arr_js = array(
			'morecontent' => base_url() . "resources/js/morecontent/marketing/crm/bny_gift_list.js",
			'table_load_sort' => base_url() . "resources/js/table_load_sort.js"
		);

		$arr_css = array(
			'site_new' => base_url() . "assets/css/site_new.css"
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/bny_gift_list', $data, $arr_css, $arr_js, $arr_input, 10068);
	}

	function loaddata_more_ajax()
	{
		$bny_gift_search = $this->input->post('bny_gift_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'bny_gift_search' => $bny_gift_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5
		);

		$arr_gifts = $this->curl_bl->CallApiNospi('POST', 'marketing/crm/bnyadminreward/bny_gift_search', $data);

		$arr_data = array(
			'list_data' => !empty($arr_gifts['Data']) ? $arr_gifts['Data'] : array(),
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);
		echo json_encode($arr_data);
	}

	function add_bny_gift_form()
	{
		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$arr_js = array(
			'bny_gift_form' => base_url() . 'resources/js/marketing/crm/bny_gift_form.js'
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/add_bny_gift_form', NULL, NULL, $arr_js, $arr_input, 10068);
	}

	function bny_gift_add()
	{
		$web_bny_gift_detail = $this->input->post('web_bny_gift_detail');
		$web_bny_gift_now = $this->input->post('web_bny_gift_now');

		$pic_name = $this->upload_gift_pic();
		if ($pic_name === FALSE) {
			$this->session->set_flashdata('add_bny_gift', 'fail');
			redirect(base_url() . 'marketing/crm/bnyadminreward/add_bny_gift_form', 'refresh');
			return;
		}

		$data_curl = array(
			'web_bny_gift_pic' => $pic_name,
			'web_bny_gift_detail' => $web_bny_gift_detail,
			'web_bny_gift_now' => ($web_bny_gift_now == '1') ? 1 : 0
		);

		$arr_res = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/bny_gift_add', $data_curl);

		if (!empty($arr_res['Status']) && $arr_res['Status'] == "Success") {
			$this->session->set_flashdata('add_bny_gift', 'success');
		} else {
			$this->session->set_flashdata('add_bny_gift', 'fail');
		}

		redirect(base_url() . 'marketing/crm/bnyadminreward/bny_gift_list', 'refresh');
	}

	function edit_bny_gift_form()
	{
		$id = $this->uri->segment(5);

		$arr_gift = $this->curl_bl->CallApi('GET', 'marketing/crm/bnyadminreward/get_by_id/' . $id);

		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$data = array(
			'arr_gift' => !empty($arr_gift['Data']) ? $arr_gift['Data'] : array(),
			'id_en' => $id,
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);

		$arr_js = array(
			'bny_gift_form' => base_url() . 'resources/js/marketing/crm/bny_gift_form.js'
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/edit_bny_gift_form', $data, NULL, $arr_js, $arr_input, 10068);
	}

	function bny_gift_edit()
	{
		$id_en = $this->input->post('id_en');
		$web_bny_gift_detail = $this->input->post('web_bny_gift_detail');
		$web_bny_gift_now = $this->input->post('web_bny_gift_now');
		$web_bny_gift_pic_old = $this->input->post('web_bny_gift_pic_old');

		$pic_name = $this->upload_gift_pic();
		if ($pic_name === FALSE) {
			$this->session->set_flashdata('edit_bny_gift', 'fail');
			redirect(base_url() . 'marketing/crm/bnyadminreward/edit_bny_gift_form/' . $id_en, 'refresh');
			return;
		}
		if ($pic_name === '') {
			$pic_name = $web_bny_gift_pic_old;
		}

		$data_curl = array(
			'id_en' => $id_en,
			'web_bny_gift_pic' => $pic_name,
			'web_bny_gift_detail' => $web_bny_gift_detail,
			'web_bny_gift_now' => ($web_bny_gift_now == '1') ? 1 : 0
		);

		$arr_res = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/bny_gift_edit', $data_curl);

		if (!empty($arr_res['Status']) && $arr_res['Status'] == "Success") {
			$this->session->set_flashdata('edit_bny_gift', 'success');
		} else {
			$this->session->set_flashdata('edit_bny_gift', 'fail');
		}

		redirect(base_url() . 'marketing/crm/bnyadminreward/bny_gift_list', 'refresh');
	}

	function get_gift_lasted()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization');
		if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
			exit;
		}

		$data = $this->web_bny_gift_model->select_current();
		if (empty($data)) {
			$this->db->select('*');
			$this->db->from('web_bny_gift');
			$this->db->order_by('web_bny_gift_id', 'DESC');
			$this->db->limit(1);
			$q = $this->db->get();
			$data = $q->row_array();
		}
		$pic_url = '';
		if (!empty($data['web_bny_gift_pic'])) {
			$pic_url = $this->gift_pic_base_url() . rawurlencode(basename($data['web_bny_gift_pic']));
		}

		echo json_encode(array(
			'status' => !empty($data),
			'gift_pic_url' => $pic_url,
			'gift_detail' => !empty($data['web_bny_gift_detail']) ? $data['web_bny_gift_detail'] : ''
		));
	}

	public function lucky_list()
	{
		$data_search = array(
			'lucky_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 0,
			'per_page' => 5
		);

		$arr_winners = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/lucky_winner_search', $data_search);

		$data = array(
			'arr_winners' => !empty($arr_winners['Data']) ? $arr_winners['Data'] : array(),
			'active_tab' => 'lucky',
			'data_search' => $data_search,
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);

		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$arr_js = array(
			'morecontent' => base_url() . "resources/js/morecontent/marketing/crm/lucky_list.js",
			'table_load_sort' => base_url() . "resources/js/table_load_sort.js"
		);

		$arr_css = array(
			'site_new' => base_url() . "assets/css/site_new.css"
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/lucky_list', $data, $arr_css, $arr_js, $arr_input, 10068);
	}

	public function lucky_list_search()
	{
		$lucky_search = $this->input->post('lucky_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'lucky_search' => $lucky_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5
		);

		$arr_winners = $this->curl_bl->CallApi('POST', 'marketing/crm/bnyadminreward/lucky_winner_search', $data_search);

		$data = array(
			'arr_winners' => !empty($arr_winners['Data']) ? $arr_winners['Data'] : array(),
			'active_tab' => 'lucky',
			'data_search' => $data_search,
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);

		$arr_input = array(
			'title' => "BNY Admin Reward"
		);

		$arr_js = array(
			'morecontent' => base_url() . "resources/js/morecontent/marketing/crm/lucky_list.js",
			'table_load_sort' => base_url() . "resources/js/table_load_sort.js"
		);

		$arr_css = array(
			'site_new' => base_url() . "assets/css/site_new.css"
		);

		$this->view_util->load_view_main('marketing/crm/bnyadminreward/lucky_list', $data, $arr_css, $arr_js, $arr_input, 10068);
	}

	function loaddata_lucky_more_ajax()
	{
		$lucky_search = $this->input->post('lucky_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'lucky_search' => $lucky_search,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5
		);

		$arr_winners = $this->curl_bl->CallApiNospi('POST', 'marketing/crm/bnyadminreward/lucky_winner_search', $data);

		$arr_data = array(
			'list_data' => !empty($arr_winners['Data']) ? $arr_winners['Data'] : array(),
			'gift_pic_base_url' => $this->gift_pic_base_url()
		);
		echo json_encode($arr_data);
	}

	function update_gift_send_ajax()
	{
		$web_bny_reward_id = $this->input->post('web_bny_reward_id');
		$web_bny_gift_send = $this->input->post('web_bny_gift_send');

		$data_curl = array(
			'web_bny_reward_id' => $web_bny_reward_id,
			'web_bny_gift_send' => $web_bny_gift_send
		);

		$arr_res = $this->curl_bl->CallApiNospi('POST', 'marketing/crm/bnyadminreward/update_gift_send', $data_curl);

		$ok = !empty($arr_res['Status']) && $arr_res['Status'] === 'Success';
		echo json_encode(array('status' => $ok ? 'success' : 'fail'));
	}

	function biggrill_import_data()
	{
		redirect(base_url() . 'marketing/crm/bnyadminreward/bny_gift_list?tab=import', 'refresh');
	}

	function biggrill_import_data_action()
	{
		$this->load->library('Upload_secure', array(
			'psp_inbox_dir' => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
		));

		$res = $this->upload_secure->upload_file('upload_file1');
		$is_success = false;

		if (!empty($res['is_upload']) && (int)$res['is_upload'] === 1) {
			$file_s = APP_STORE_PATH . "/uploads/xls/" . $res['file_name'];
			$mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			if (!empty($_FILES['upload_file1']['type']) && in_array($_FILES['upload_file1']['type'], $mimes, true)) {
				$this->load->library('Lib_excel');
				try {
					$inputFileType = PHPExcel_IOFactory::identify($file_s);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($file_s);

					$sheet = $objPHPExcel->getSheet(0);
					$highestRow = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();

					for ($row = 2; $row <= $highestRow; $row++) {
						$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, true, false);
						$ctime = $rowData[0][10];
						$order_id = $rowData[0][1];
						$status = $rowData[0][7];
						$cus_name = $rowData[0][3];
						$cus_phone = $rowData[0][5];

						$price = (float)$rowData[0][17];
						$delivery = (float)$rowData[0][18];
						$discount = str_replace("-", "", $rowData[0][19]);
						$discount = (float)$discount;

						$amount_include_vat = ($price + $delivery) - $discount;
						$amount_exclude_vat = round($amount_include_vat / 1.07, 2);
						$vat = round($amount_include_vat - $amount_exclude_vat, 2);

						$date = str_replace('/', '-', $ctime);
						$date_to_db = date('Y/m/d H:i:s', strtotime($date));

						$data = array(
							'ctime' => $date_to_db,
							'order_id' => $order_id,
							'cus_name' => $cus_name,
							'cus_phone' => $cus_phone,
							'status' => $status,
							'price' => $price,
							'delivery' => $delivery,
							'discount' => $discount,
							'amount_include_vat' => $amount_include_vat,
							'amount_exclude_vat' => $amount_exclude_vat,
							'vat' => $vat
						);

						$arr_order = $this->biggrill_data_model->select_by_order_id($order_id);
						if (empty($arr_order)) {
							$this->biggrill_data_model->insert($data);
						}
					}
					$is_success = true;
				} catch (Exception $e) {
					$is_success = false;
				}
			}
		}

		$this->session->set_flashdata('import_biggrill', $is_success ? 'success' : 'fail');
		redirect(base_url() . 'marketing/crm/bnyadminreward/bny_gift_list?tab=import', 'refresh');
	}
}
