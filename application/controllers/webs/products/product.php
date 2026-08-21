<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Webs → Products → Products (per domain, category filter, pagination).
 */
class Product extends Auth_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/upload_bl');
		$this->load->library('util/encryption_util');
	}

	private function thumb_pic_storage_path()
	{
		return APP_STORE_PATH . '/uploads/product/';
	}

	private function thumb_pic_base_url()
	{
		return base_url('webs/products/product/thumb_pic/');
	}

	public function thumb_pic()
	{
		$filename = $this->uri->segment(5);
		if ($filename === false || $filename === '') {
			show_404();
			return;
		}

		$filename = basename(urldecode($filename));
		$filepath = $this->thumb_pic_storage_path() . $filename;

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

	private function attach_thumbnail_url(&$row)
	{
		$row['thumbnail_url'] = '';
		if (!empty($row['thumbnail'])) {
			$row['thumbnail_url'] = $this->thumb_pic_base_url() . rawurlencode(basename($row['thumbnail']));
		}
	}

	public function manage()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$arr_domains = $this->curl_bl->CallApi('POST', 'webs/domains/domain_search', array(
			'shopid_en' => $sess_shop_id,
			'domain_search' => '',
			'sortby' => 'web_domain_name',
			'sorttype' => 'asc',
			'offset' => 0,
			'per_page' => 500
		));

		$domains = array();
		if (isset($arr_domains['Status']) && $arr_domains['Status'] == 'Success' && !empty($arr_domains['Data'])) {
			$payload = $arr_domains['Data'];
			$rows = array();
			if (isset($payload['rows']) && is_array($payload['rows'])) {
				$rows = $payload['rows'];
			} elseif (is_array($payload) && isset($payload[0])) {
				$rows = $payload;
			}
			foreach ($rows as $d) {
				if (isset($d['web_domain_id'])) {
					$d['web_domain_id_en'] = $this->encryption_util->encrypt_ssl($d['web_domain_id']);
					$domains[] = $d;
				}
			}
		}

		$selected = '';
		if (!empty($domains)) {
			$selected = $domains[0]['web_domain_id_en'];
		}

		$data = array(
			'arr_domains' => $domains,
			'selected_domain_en' => $selected,
			'shopid_en' => $sess_shop_id,
			'thumb_pic_base_url' => $this->thumb_pic_base_url()
		);

		$arr_input = array('title' => 'Products');
		$arr_css = array(
			'cropper' => base_url().'global/vendor/cropper/cropper.css',
			'summernote' => base_url().'global/vendor/summernote/summernote-lite.min.css',
			'step_switch' => base_url().'resources/css/webs/products/step_switch.css'
		);
		$arr_js = array(
			'cropper' => base_url().'global/vendor/cropper/cropper.min.js',
			'summernote' => base_url().'global/vendor/summernote/summernote-lite.min.js',
			'product_manage' => base_url().'resources/js/webs/products/product_manage.js?v='.time()
		);

		$this->view_util->load_view_main('webs/products/product/manage', $data, $arr_css, $arr_js, $arr_input, MENU_WEBS_PRODUCTS);
	}

	function units_ajax()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/shop_unit/list_by_shop', array(
			'ShopID' => $sess_shop_id,
			'include_inactive' => 0
		));
		$rows = array();
		if (isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])) {
			foreach ($arr['Data'] as $row) {
				$row['display_name'] = bilingual($row, 'name');
				$rows[] = $row;
			}
		}
		echo json_encode(array('Status' => 'Success', 'list_data' => $rows, 'admin_lang' => admin_lang()));
	}

	function list_ajax()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$domain_en = $this->input->post('web_domain_id_en');
		$category_id = (int)$this->input->post('web_domain_category_id');
		$q = trim((string)$this->input->post('q'));
		$page = max(1, (int)$this->input->post('page'));
		$per_page = (int)$this->input->post('per_page');
		if ($per_page <= 0) {
			$per_page = 20;
		}

		$payload = array(
			'ShopID' => $sess_shop_id,
			'web_domain_id_en' => $domain_en,
			'web_domain_category_id' => $category_id,
			'q' => $q,
			'page' => $page,
			'per_page' => $per_page
		);

		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_product/search', $payload);

		$rows = array();
		$total = 0;
		$total_pages = 1;
		$out_page = $page;
		$out_per = $per_page;

		if (isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])) {
			$data = $arr['Data'];
			$list = isset($data['rows']) && is_array($data['rows']) ? $data['rows'] : array();
			foreach ($list as $row) {
				$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_domain_product_id']);
				$row['display_title'] = bilingual($row, 'Title');
				$this->attach_thumbnail_url($row);
				$rows[] = $row;
			}
			$total = isset($data['total']) ? (int)$data['total'] : count($rows);
			$total_pages = isset($data['total_pages']) ? (int)$data['total_pages'] : 1;
			$out_page = isset($data['page']) ? (int)$data['page'] : $page;
			$out_per = isset($data['per_page']) ? (int)$data['per_page'] : $per_page;
		}

		echo json_encode(array(
			'Status' => 'Success',
			'list_data' => $rows,
			'total' => $total,
			'page' => $out_page,
			'per_page' => $out_per,
			'total_pages' => max(1, $total_pages)
		));
	}

	function get_ajax()
	{
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_product/get_by_id', array(
			'id_en' => $id_en
		));

		$row = array();
		if (isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])) {
			$row = $arr['Data'];
			$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_domain_product_id']);
			$this->attach_thumbnail_url($row);
		}
		echo json_encode(array('Status' => 'Success', 'product_data' => $row));
	}

	function save_ajax()
	{
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$is_add = (int)$this->input->post('is_add');
		$Title_th = trim((string)$this->input->post('Title_th'));
		$Title_en = trim((string)$this->input->post('Title_en'));
		$Title = ($Title_en !== '') ? $Title_en : $Title_th;
		$domain_en = $this->input->post('web_domain_id_en');
		$id_en = $this->input->post('id_en');
		$clear_thumbnail = (int)$this->input->post('clear_thumbnail');

		if ($Title === '') {
			echo json_encode(array('Status' => 'Fail', 'Message' => 'Title required (EN or TH)'));
			return;
		}

		$pic_name = '';
		if (!empty($_FILES['thumbnail']['name'])) {
			$upload_dir = APP_STORE_PATH . '/uploads/product/';
			if (!is_dir($upload_dir)) {
				@mkdir($upload_dir, 0755, true);
			}
			ob_start();
			$pic_name = $this->upload_bl->upload_file_pic_path('/uploads/product/', 'thumbnail');
			$upload_noise = trim(ob_get_clean());
			if ($pic_name === '') {
				$up_msg = 'Thumbnail upload failed';
				if ($upload_noise !== '') {
					$up_msg .= ': ' . strip_tags($upload_noise);
				}
				echo json_encode(array('Status' => 'Fail', 'Message' => $up_msg));
				return;
			}
		}

		$payload = array(
			'Title' => $Title,
			'Title_th' => $Title_th,
			'Title_en' => $Title_en,
			'Sku' => trim((string)$this->input->post('Sku')),
			'Barcode' => trim((string)$this->input->post('Barcode')),
			'web_shop_unit_id' => (int)$this->input->post('web_shop_unit_id'),
			'Unit' => '',
			'Description' => (string)$this->input->post('Description_en'),
			'Description_th' => (string)$this->input->post('Description_th'),
			'Description_en' => (string)$this->input->post('Description_en'),
			'web_domain_category_id' => (int)$this->input->post('web_domain_category_id'),
			'Cost_price' => $this->input->post('Cost_price'),
			'Price' => $this->input->post('Price'),
			'is_visible' => (int)$this->input->post('is_visible') ? 1 : 0,
			'is_atomic' => (int)$this->input->post('is_atomic') ? 1 : 0,
			'is_salable' => (int)$this->input->post('is_salable') ? 1 : 0,
			'entry_type' => trim((string)$this->input->post('entry_type')),
			'seo_title_th' => trim((string)$this->input->post('seo_title_th')),
			'seo_title_en' => trim((string)$this->input->post('seo_title_en')),
			'seo_description_th' => trim((string)$this->input->post('seo_description_th')),
			'seo_description_en' => trim((string)$this->input->post('seo_description_en')),
			'seo_keywords_th' => trim((string)$this->input->post('seo_keywords_th')),
			'seo_keywords_en' => trim((string)$this->input->post('seo_keywords_en')),
			'seo_slug_th' => trim((string)$this->input->post('seo_slug_th')),
			'seo_slug_en' => trim((string)$this->input->post('seo_slug_en')),
			'sort_order' => (int)$this->input->post('sort_order'),
			'width_cm' => $this->input->post('width_cm'),
			'length_cm' => $this->input->post('length_cm'),
			'height_cm' => $this->input->post('height_cm'),
			'weight_g' => $this->input->post('weight_g'),
			'max_load_axis_x_g' => $this->input->post('max_load_axis_x_g'),
			'max_load_axis_y_g' => $this->input->post('max_load_axis_y_g'),
			'max_load_axis_z_g' => $this->input->post('max_load_axis_z_g')
		);
		if ($pic_name !== '') {
			$payload['thumbnail'] = $pic_name;
		}

		if ($is_add === 1) {
			$payload['web_domain_id_en'] = $domain_en;
			$payload['ShopID'] = $sess_shop_id;
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_product/product_add', $payload);
		} else {
			$payload['id_en'] = $id_en;
			if ($clear_thumbnail === 1 && $pic_name === '') {
				$payload['clear_thumbnail'] = 1;
			}
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_product/product_edit', $payload);
		}

		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if (!$ok) {
			if (!empty($arr['Description'])) {
				$msg = $arr['Description'];
			} elseif (!empty($arr['Message'])) {
				$msg = $arr['Message'];
			} elseif (isset($arr['Status']) && $arr['Status'] === 'HttpError') {
				$msg = 'API HTTP '.(isset($arr['Code']) ? $arr['Code'] : '?');
			} elseif (isset($arr['Status']) && $arr['Status'] === 'JsonError') {
				$msg = 'API JSON error';
			} else {
				$msg = 'Save failed';
			}
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $ok ? '' : $msg
		));
	}

	/**
	 * Ajax: Google Cloud Translation (EN↔TH).
	 */
	function translate_ajax()
	{
		$this->load->library('businesslogic/google_translate_bl');
		$text = (string)$this->input->post('text');
		$target = strtolower(trim((string)$this->input->post('target')));
		$source = strtolower(trim((string)$this->input->post('source')));
		$format = strtolower(trim((string)$this->input->post('format')));
		if ($format !== 'html') {
			$format = 'text';
		}

		$result = $this->google_translate_bl->translate($text, $target, $source, $format);
		if (!empty($result['ok'])) {
			echo json_encode(array(
				'Status' => 'Success',
				'translated' => $result['text'],
				'detected' => isset($result['detected']) ? $result['detected'] : $source
			));
			return;
		}
		echo json_encode(array(
			'Status' => 'Fail',
			'Message' => !empty($result['error']) ? $result['error'] : 'Translate failed'
		));
	}

	function del_ajax()
	{
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('GET', 'webs/domain_product/product_del/'.$id_en);
		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if (!$ok) {
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Delete failed');
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $msg
		));
	}

	function visibility_ajax()
	{
		$id_en = $this->input->post('id_en');
		$is_visible = (int)$this->input->post('is_visible') ? 1 : 0;
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_product/set_visible', array(
			'id_en' => $id_en,
			'is_visible' => $is_visible
		));
		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if (!$ok) {
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Update failed');
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $msg,
			'is_visible' => $is_visible
		));
	}
}
