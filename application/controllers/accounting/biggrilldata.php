<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Biggrilldata extends CI_Controller
{

	const LIST_PER_PAGE = 20;

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');
		$this->load->library('util/random_util');
		$this->load->model('biggrill_data_model');
		$this->load->model('inwshop_data_model');
		$this->load->model('inwshop_item_data_model');
		$this->auth_bl->check_session_exists();
	}

	private function default_data_search()
	{
		return array(
			'search_field' => 'order_id',
			'search_text' => '',
			'is_void' => '',
			'daterange' => '',
			'date_start' => '',
			'date_end' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 0,
			'per_page' => self::LIST_PER_PAGE
		);
	}

	private function parse_daterange($daterange)
	{
		$result = array('date_start' => '', 'date_end' => '');
		if ($daterange === '' || $daterange === NULL) {
			return $result;
		}

		$parts = preg_split('/\s*-\s*/', $daterange, 2);
		if (count($parts) !== 2) {
			return $result;
		}

		$start = strtotime(trim($parts[0]));
		$end = strtotime(trim($parts[1]));
		if ($start && $end) {
			$result['date_start'] = date('Y-m-d 00:00:00', $start);
			$result['date_end'] = date('Y-m-d 23:59:59', $end);
		}

		return $result;
	}

	private function get_search_params_from_input($offset = 0)
	{
		$daterange = $this->input->post('daterange');
		if ($daterange === FALSE || $daterange === NULL) {
			$daterange = $this->input->get('daterange');
		}
		if ($daterange === FALSE || $daterange === NULL) {
			$daterange = '';
		}

		$dates = $this->parse_daterange($daterange);

		$search_field = $this->input->post('search_field');
		if ($search_field === FALSE || $search_field === NULL) {
			$search_field = 'order_id';
		}

		$search_text = $this->input->post('search_text');
		if ($search_text === FALSE || $search_text === NULL) {
			$search_text = '';
		}

		$is_void = $this->input->post('is_void');
		if ($is_void === FALSE || $is_void === NULL) {
			$is_void = '';
		}

		$sortby = $this->input->post('sortby');
		if ($sortby === FALSE || $sortby === NULL) {
			$sortby = '';
		}

		$sorttype = $this->input->post('sorttype');
		if ($sorttype === FALSE || $sorttype === NULL) {
			$sorttype = '';
		}

		return array(
			'search_field' => $search_field,
			'search_text' => $search_text,
			'is_void' => $is_void,
			'daterange' => $daterange,
			'date_start' => $dates['date_start'],
			'date_end' => $dates['date_end'],
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => (int) $offset,
			'per_page' => self::LIST_PER_PAGE
		);
	}

	private function load_biggrilldata_page($data, $active_tab)
	{
		$data['active_tab'] = $active_tab;
		$data['list_per_page'] = self::LIST_PER_PAGE;

		$arr_input = array(
			'title' => 'BigGrill Data'
		);

		$arr_css = array(
			'site_new' => base_url() . 'assets/css/site_new.css'
		);

		$arr_js = NULL;

		if ($active_tab === 'manage') {
			$arr_css['daterangepicker'] = base_url() . 'resources/css/daterangepicker/daterangepicker.css';
			$arr_js = array(
				'morecontent' => base_url() . 'resources/js/morecontent/accounting/biggrilldata_list.js'
			);
		}

		$this->view_util->load_view_main('accounting/biggrilldata/biggrilldata_list', $data, $arr_css, $arr_js, $arr_input, MENU_ACCOUNT);
	}

	public function biggrilldata_list()
	{
		$active_tab = $this->input->get('tab');
		if ($active_tab !== 'manage' && $active_tab !== 'import' && $active_tab !== 'sale_report') {
			$active_tab = 'import';
		}

		$import_alt = $this->session->flashdata('import_biggrill');
		$add_alt = $this->session->flashdata('add_biggrilldata');
		$edit_alt = $this->session->flashdata('edit_biggrilldata');

		$data = array(
			'import_alt' => $import_alt,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'arr_rows' => array(),
			'data_search' => $this->default_data_search()
		);

		if ($active_tab === 'manage') {
			$data_search = $this->default_data_search();
			$data['data_search'] = $data_search;
			$data['arr_rows'] = $this->biggrill_data_model->select_by_search($data_search);
		}

		$this->load_biggrilldata_page($data, $active_tab);
	}

	public function biggrilldata_list_search()
	{
		$data_search = $this->get_search_params_from_input(0);
		$arr_rows = $this->biggrill_data_model->select_by_search($data_search);

		$data = array(
			'import_alt' => '',
			'add_alt' => $this->session->flashdata('add_biggrilldata'),
			'edit_alt' => $this->session->flashdata('edit_biggrilldata'),
			'arr_rows' => $arr_rows,
			'data_search' => $data_search
		);

		$this->load_biggrilldata_page($data, 'manage');
	}

	public function loaddata_more_ajax()
	{
		$offset = $this->input->post('offset');
		$data_search = $this->get_search_params_from_input($offset);
		$arr_rows = $this->biggrill_data_model->select_by_search($data_search);

		echo json_encode(array(
			'list_data' => $arr_rows
		));
	}

	public function update_void_ajax()
	{
		$biggrill_data_id = (int) $this->input->post('biggrill_data_id');
		$is_void = ((int) $this->input->post('is_void') === 1) ? 1 : 0;

		if ($biggrill_data_id <= 0) {
			echo json_encode(array('status' => 'fail'));
			return;
		}

		$row = $this->biggrill_data_model->select_by_id($biggrill_data_id);
		if (empty($row)) {
			echo json_encode(array('status' => 'fail'));
			return;
		}

		$this->biggrill_data_model->update(array('is_void' => $is_void), $biggrill_data_id);
		echo json_encode(array('status' => 'success'));
	}

	public function add_biggrilldata_form()
	{
		$data = array(
			'add_alt' => $this->session->flashdata('add_biggrilldata')
		);

		$this->load_biggrilldata_form_page('accounting/biggrilldata/add_biggrilldata_form', $data, 'เพิ่มข้อมูลลูกค้า');
	}

	private function load_biggrilldata_form_page($view, $data, $page_title)
	{
		$data['page_title'] = $page_title;

		$arr_input = array(
			'title' => 'BigGrill Data'
		);

		$arr_css = array(
			'site_new' => base_url() . 'assets/css/site_new.css',
			'daterangepicker' => base_url() . 'resources/css/daterangepicker/daterangepicker.css'
		);

		$arr_js = array(
			'biggrilldata_form' => base_url() . 'resources/js/accounting/biggrilldata_form.js'
		);

		$this->view_util->load_view_main($view, $data, $arr_css, $arr_js, $arr_input, MENU_ACCOUNT);
	}

	private function parse_ctime_input($ctime_input)
	{
		$ctime_db = date('Y/m/d H:i:s');
		$ctime_input = trim((string) $ctime_input);
		if ($ctime_input === '') {
			return $ctime_db;
		}

		$dt = DateTime::createFromFormat('d/m/Y H:i', $ctime_input);
		if ($dt === FALSE) {
			$dt = DateTime::createFromFormat('d/m/Y H:i:s', $ctime_input);
		}
		if ($dt !== FALSE) {
			$ctime_db = $dt->format('Y/m/d H:i:s');
		}

		return $ctime_db;
	}

	private function build_record_from_post()
	{
		$price = (float) $this->input->post('price');
		$delivery = (float) $this->input->post('delivery');
		$discount = (float) $this->input->post('discount');
		$amount_include_vat = ($price + $delivery) - $discount;
		$amount_exclude_vat = round($amount_include_vat / 1.07, 2);
		$vat = round($amount_include_vat - $amount_exclude_vat, 2);

		return array(
			'ctime' => $this->parse_ctime_input($this->input->post('ctime')),
			'order_id' => trim((string) $this->input->post('order_id')),
			'cus_name' => trim((string) $this->input->post('cus_name')),
			'cus_phone' => trim((string) $this->input->post('cus_phone')),
			'status' => trim((string) $this->input->post('status')),
			'price' => $price,
			'delivery' => $delivery,
			'discount' => $discount,
			'amount_include_vat' => $amount_include_vat,
			'amount_exclude_vat' => $amount_exclude_vat,
			'vat' => $vat,
			'trackingid' => trim((string) $this->input->post('trackingid')),
			'is_void' => ($this->input->post('is_void') == '1') ? 1 : 0
		);
	}

	public function edit_biggrilldata_form()
	{
		$id = (int) $this->uri->segment(4);
		$row = $this->biggrill_data_model->select_by_id($id);
		if (empty($row)) {
			redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=manage', 'refresh');
			return;
		}

		$data = array(
			'arr_row' => $row,
			'edit_alt' => $this->session->flashdata('edit_biggrilldata')
		);

		$this->load_biggrilldata_form_page('accounting/biggrilldata/edit_biggrilldata_form', $data, 'แก้ไขข้อมูลลูกค้า');
	}

	public function biggrilldata_edit()
	{
		$id = (int) $this->input->post('biggrill_data_id');
		if ($id <= 0) {
			$this->session->set_flashdata('edit_biggrilldata', 'fail');
			redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=manage', 'refresh');
			return;
		}

		$row = $this->biggrill_data_model->select_by_id($id);
		if (empty($row)) {
			$this->session->set_flashdata('edit_biggrilldata', 'fail');
			redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=manage', 'refresh');
			return;
		}

		$data = $this->build_record_from_post();
		if ($data['order_id'] === '') {
			$this->session->set_flashdata('edit_biggrilldata', 'fail');
			redirect(base_url() . 'accounting/biggrilldata/edit_biggrilldata_form/' . $id, 'refresh');
			return;
		}

		$existing = $this->biggrill_data_model->select_by_order_id($data['order_id']);
		if (!empty($existing) && (int) $existing['biggrill_data_id'] !== $id) {
			$this->session->set_flashdata('edit_biggrilldata', 'duplicate');
			redirect(base_url() . 'accounting/biggrilldata/edit_biggrilldata_form/' . $id, 'refresh');
			return;
		}

		$this->biggrill_data_model->update($data, $id);
		$this->session->set_flashdata('edit_biggrilldata', 'success');
		redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=manage', 'refresh');
	}

	public function biggrilldata_add()
	{
		$data = $this->build_record_from_post();

		if ($data['order_id'] === '') {
			$this->session->set_flashdata('add_biggrilldata', 'fail');
			redirect(base_url() . 'accounting/biggrilldata/add_biggrilldata_form', 'refresh');
			return;
		}

		$existing = $this->biggrill_data_model->select_by_order_id($data['order_id']);
		if (!empty($existing)) {
			$this->session->set_flashdata('add_biggrilldata', 'duplicate');
			redirect(base_url() . 'accounting/biggrilldata/add_biggrilldata_form', 'refresh');
			return;
		}

		$this->biggrill_data_model->insert($data);
		$this->session->set_flashdata('add_biggrilldata', 'success');
		redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=manage', 'refresh');
	}

	private function build_tracking_map($objPHPExcel)
	{
		$tracking_map = array();

		if ($objPHPExcel->getSheetCount() <= 3) {
			return $tracking_map;
		}

		$track_sheet = $objPHPExcel->getSheet(3);
		$highest_row = $track_sheet->getHighestRow();

		for ($row = 2; $row <= $highest_row; $row++) {
			$row_data = $track_sheet->rangeToArray('A' . $row . ':D' . $row, NULL, TRUE, FALSE);
			$order_id = isset($row_data[0][0]) ? trim((string) $row_data[0][0]) : '';
			$tracking_code = isset($row_data[0][3]) ? trim((string) $row_data[0][3]) : '';

			if ($order_id === '' || $tracking_code === '') {
				continue;
			}

			if (isset($tracking_map[$order_id])) {
				$tracking_map[$order_id] .= ',' . $tracking_code;
			} else {
				$tracking_map[$order_id] = $tracking_code;
			}
		}

		foreach ($tracking_map as $order_id => $trackingid) {
			if (strlen($trackingid) > 255) {
				$tracking_map[$order_id] = substr($trackingid, 0, 255);
			}
		}

		return $tracking_map;
	}

	public function biggrill_import_data_action()
	{
		$this->load->library('Upload_secure', array(
			'psp_inbox_dir' => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
		));

		$res = $this->upload_secure->upload_file('upload_file1');
		$is_success = false;

		if (!empty($res['is_upload']) && (int) $res['is_upload'] === 1) {
			$file_s = APP_STORE_PATH . '/uploads/xls/' . $res['file_name'];
			$mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

			if (!empty($_FILES['upload_file1']['type']) && in_array($_FILES['upload_file1']['type'], $mimes, TRUE)) {
				$this->load->library('Lib_excel');

				try {
					$input_file_type = PHPExcel_IOFactory::identify($file_s);
					$obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
					$obj_php_excel = $obj_reader->load($file_s);

					$tracking_map = $this->build_tracking_map($obj_php_excel);

					$sheet = $obj_php_excel->getSheet(0);
					$highest_row = $sheet->getHighestRow();
					$highest_column = $sheet->getHighestColumn();

					for ($row = 2; $row <= $highest_row; $row++) {
						$row_data = $sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, NULL, TRUE, FALSE);

						$ctime = $row_data[0][10];
						$order_id = $row_data[0][1];
						$status = $row_data[0][7];
						$cus_name = $row_data[0][3];
						$cus_phone = $row_data[0][5];

						$price = (float) $row_data[0][17];
						$delivery = (float) $row_data[0][18];
						$discount = str_replace('-', '', $row_data[0][19]);
						$discount = (float) $discount;

						$amount_include_vat = ($price + $delivery) - $discount;
						$amount_exclude_vat = round($amount_include_vat / 1.07, 2);
						$vat = round($amount_include_vat - $amount_exclude_vat, 2);

						$date = str_replace('/', '-', $ctime);
						$date_to_db = date('Y/m/d H:i:s', strtotime($date));

						$trackingid = isset($tracking_map[$order_id]) ? $tracking_map[$order_id] : '';

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
							'vat' => $vat,
							'trackingid' => $trackingid
						);

						$arr_order = $this->biggrill_data_model->select_by_order_id($order_id);
						if (empty($arr_order)) {
							$this->biggrill_data_model->insert($data);
						} elseif ($trackingid !== '') {
							$this->biggrill_data_model->update_by_order_id(array('trackingid' => $trackingid), $order_id);
						}
					}

					$is_success = true;
				} catch (Exception $e) {
					$is_success = false;
				}
			}
		}

		$this->session->set_flashdata('import_biggrill', $is_success ? 'success' : 'fail');
		redirect(base_url() . 'accounting/biggrilldata/biggrilldata_list?tab=import', 'refresh');
	}

	function inwshop_import_sale_chk()
	{
		$data = array(
			'import_alt' => '',
			'add_alt' => '',
			'edit_alt' => '',
			'arr_rows' => array(),
			'data_search' => $this->default_data_search()
		);
		$this->load_biggrilldata_page($data, 'sale_report');
	}

	function inwshop_import_sale_chk_action()
	{
		$file1_name = '';
		$this->load->library('Upload_secure', array(
			'psp_inbox_dir' => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
		));

		$res = $this->upload_secure->upload_file('upload_file1');

		if ($res['is_upload'] === 1) {
			$file_s = APP_STORE_PATH.'/uploads/xls/'.$res['file_name'];
			$mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			if (in_array($_FILES['upload_file1']['type'], $mimes)) {
				$this->load->library('Lib_excel');

				try {
					$inputFileType = PHPExcel_IOFactory::identify($file_s);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($file_s);
				} catch (Exception $e) {
					die('Error loading file "'.pathinfo($file_s, PATHINFO_BASENAME).'": '.$e->getMessage());
				}

				$sheet = $objPHPExcel->getSheet(0);
				$highestRow = $sheet->getHighestRow();
				$highestColumn = $sheet->getHighestColumn();

				$keygen = $this->random_util->create_random_number(8);

				for ($row = 2; $row <= $highestRow; $row++) {
					$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
						NULL,
						TRUE,
						FALSE);

					$ctime = $rowData[0][10];
					$order_id = $rowData[0][1];
					$status = $rowData[0][7];
					$cus_name = $rowData[0][3];

					$price = $rowData[0][17];
					$delivery = $rowData[0][18];
					$discount = $rowData[0][19];
					$discount = str_replace('-', '', $discount);

					$amount_include_vat = ($price + $delivery) - $discount;
					$amount_exclude_vat = $amount_include_vat / 1.07;
					$amount_exclude_vat = round($amount_exclude_vat, 2);
					$vat = $amount_include_vat - $amount_exclude_vat;
					$vat = round($vat, 2);

					$date = str_replace('/', '-', $ctime);
					$date_to_db = date('Y/m/d H:i:s', strtotime($date));

					$data = array(
						'ctime' => $date_to_db,
						'order_id' => $order_id,
						'cus_name' => $cus_name,
						'status' => $status,
						'price' => $price,
						'delivery' => $delivery,
						'discount' => $discount,
						'amount_include_vat' => $amount_include_vat,
						'amount_exclude_vat' => $amount_exclude_vat,
						'vat' => $vat,
						'code' => $keygen
					);

					$this->inwshop_data_model->insert($data);
				}

				$sheet2 = $objPHPExcel->getSheet(1);
				$highestRow2 = $sheet2->getHighestRow();
				$highestColumn2 = $sheet2->getHighestColumn();

				for ($row2 = 2; $row2 <= $highestRow2; $row2++) {
					$rowData2 = $sheet2->rangeToArray('A' . $row2 . ':' . $highestColumn2 . $row2,
						NULL,
						TRUE,
						FALSE);

					$order_id = $rowData2[0][0];
					$sku = $rowData2[0][2];
					$product_name = $rowData2[0][3];
					$procuct_price = $rowData2[0][4];
					$qty = $rowData2[0][5];
					$total_price_item = $rowData2[0][6];

					$data2 = array(
						'order_id' => $order_id,
						'sku' => $sku,
						'product_name' => $product_name,
						'procuct_price' => $procuct_price,
						'qty' => $qty,
						'total_price_item' => $total_price_item,
						'code' => $keygen
					);
					$this->inwshop_item_data_model->insert($data2);
				}

				$this->generateXls($keygen);
			}
		}
	}

	public function generateXls($keygen)
	{
		$arr_datas = $this->inwshop_data_model->select_by_code_join($keygen);
		$fileName = 'data-'.time().'.xlsx';
		$this->load->library('Lib_excel');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->createSheet(0);

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'ชื่อผู้สั่งซื้อ');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'สถานะ');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'ชื่อสินค้า');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'รหัสสินค้า');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'ค่าสินค้า');
		$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ค่าส่ง');
		$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'ส่วนลด');
		$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Amount include vat');
		$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Amount exclude Vat');
		$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Vat');

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);

		$rowCount = 2;
		$order_tmp = '';

		foreach ($arr_datas as $arr_data) {
			$delivery = 0;
			$amount_include_vat = 0;
			$amount_exclude_vat = 0;
			$vat = 0;
			$status = '';

			$order_id = $arr_data['order_id'];

			if ($order_id != $order_tmp) {
				$delivery = $arr_data['delivery'];
				$amount_include_vat = $arr_data['amount_include_vat'];
				$amount_exclude_vat = $arr_data['amount_exclude_vat'];
				$vat = $arr_data['vat'];
				$status = $arr_data['status'];
			}

			$ctime = date('Y-m-d H:i:s', strtotime($arr_data['ctime']));

			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $ctime);
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $arr_data['order_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $arr_data['cus_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $status);
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $arr_data['product_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $arr_data['sku']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $arr_data['total_price_item']);
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $delivery);
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $arr_data['discount']);
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $amount_include_vat);
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $amount_exclude_vat);
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $vat);

			if ($arr_data['order_id'] == $order_tmp) {
				$roll_bfo = $rowCount - 1;
				if ($arr_data['status'] == 'ยกเลิก') {
					$this->cellColor($objPHPExcel, 'A'.$roll_bfo.':L'.$roll_bfo, 'f53a3a');
					$this->cellColor($objPHPExcel, 'A'.$rowCount.':L'.$rowCount, 'f53a3a');
				} else {
					$this->cellColor($objPHPExcel, 'A'.$roll_bfo.':L'.$roll_bfo, '64f70b');
					$this->cellColor($objPHPExcel, 'A'.$rowCount.':L'.$rowCount, '64f70b');
				}

				$order_tmp = $arr_data['order_id'];
			} else {
				$order_tmp = $arr_data['order_id'];
			}

			if ($arr_data['status'] == 'ยกเลิก') {
				$this->cellColor($objPHPExcel, 'A'.$rowCount.':L'.$rowCount, 'f53a3a');
			}

			$rowCount = $rowCount + 1;
		}

		$this->cellColor($objPHPExcel, 'A1:L1', 'ffca2c');
		$objPHPExcel->getActiveSheet()->setTitle('Order detail');

		$arr_data2s = $this->inwshop_data_model->select_by_code($keygen);

		$objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
		$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Name');
		$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'สถานะ');
		$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Amount exclude Vat');
		$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Vat');
		$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Amount include Vat');

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

		$rowCount2 = 2;
		foreach ($arr_data2s as $arr_data2) {
			$ctime2 = date('Y-m-d', strtotime($arr_data2['ctime']));

			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount2, $ctime2);
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount2, $arr_data2['order_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount2, $arr_data2['cus_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount2, $arr_data2['status']);
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount2, $arr_data2['amount_exclude_vat']);
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount2, $arr_data2['vat']);
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount2, $arr_data2['amount_include_vat']);

			if ($arr_data2['status'] == 'ยกเลิก') {
				$this->cellColor($objPHPExcel, 'A'.$rowCount2.':G'.$rowCount2, 'f53a3a');
			}

			$rowCount2 = $rowCount2 + 1;
		}

		$this->cellColor($objPHPExcel, 'A1:G1', 'ffca2c');
		$objPHPExcel->getActiveSheet()->setTitle('Order detail finance');

		$filename = 'inwshop_'. date('Y-m-d-H-i-s').'.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	function cellColor($objPHPExcel, $cells, $color)
	{
		$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => $color
			)
		));
	}
}
