<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Webs → Products → Category (per domain, max 3 tiers).
 */
class Category extends Auth_Controller
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
		return APP_STORE_PATH . '/uploads/category/';
	}

	private function thumb_pic_base_url()
	{
		return base_url('webs/products/category/thumb_pic/');
	}

	/**
	 * Serve category thumbnail from APP_STORE_PATH (basename only).
	 */
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

		// Same API as Domains list (webs/domains/domain_search) — reliable route + shopid_en
		$arr_domains = $this->curl_bl->CallApi('POST', 'webs/domains/domain_search', array(
			'shopid_en' => $sess_shop_id,
			'domain_search' => '',
			'sortby' => 'web_domain_name',
			'sorttype' => 'asc',
			'offset' => 0,
			'per_page' => 500
		));

		$domains = array();
		if(isset($arr_domains['Status']) && $arr_domains['Status'] == 'Success' && !empty($arr_domains['Data'])){
			$payload = $arr_domains['Data'];
			$rows = array();
			if(isset($payload['rows']) && is_array($payload['rows'])){
				$rows = $payload['rows'];
			}elseif(is_array($payload) && isset($payload[0])){
				$rows = $payload;
			}
			foreach($rows as $d){
				if(isset($d['web_domain_id'])){
					$d['web_domain_id_en'] = $this->encryption_util->encrypt_ssl($d['web_domain_id']);
					$domains[] = $d;
				}
			}
		}

		$selected = '';
		if(!empty($domains)){
			$selected = $domains[0]['web_domain_id_en'];
		}

		$data = array(
			'arr_domains' => $domains,
			'selected_domain_en' => $selected,
			'shopid_en' => $sess_shop_id,
			'thumb_pic_base_url' => $this->thumb_pic_base_url()
		);

		$arr_input = array('title' => 'Product Categories');
		// summernote-lite = standalone WYSIWYG (no Bootstrap plugin deps)
		$arr_css = array(
			'cropper' => base_url().'global/vendor/cropper/cropper.css',
			'summernote' => base_url().'global/vendor/summernote/summernote-lite.min.css'
		);
		$arr_js = array(
			'cropper' => base_url().'global/vendor/cropper/cropper.min.js',
			'summernote' => base_url().'global/vendor/summernote/summernote-lite.min.js',
			'category_manage' => base_url().'resources/js/webs/products/category_manage.js'
		);

		$this->view_util->load_view_main('webs/products/category/manage', $data, $arr_css, $arr_js, $arr_input, MENU_WEBS_PRODUCTS_CATEGORY);
	}

	function list_ajax(){
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$domain_en = $this->input->post('web_domain_id_en');

		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_category/list_by_domain', array(
			'ShopID' => $sess_shop_id,
			'web_domain_id_en' => $domain_en
		));

		$rows = array();
		if(isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])){
			foreach($arr['Data'] as $row){
				$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_domain_category_id']);
				$this->attach_thumbnail_url($row);
				$rows[] = $row;
			}
		}

		echo json_encode(array('Status' => 'Success', 'list_data' => $rows));
	}

	function get_ajax(){
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_category/get_by_id', array(
			'id_en' => $id_en
		));

		$row = array();
		if(isset($arr['Status']) && $arr['Status'] == 'Success' && !empty($arr['Data'])){
			$row = $arr['Data'];
			$row['id_en'] = $this->encryption_util->encrypt_ssl($row['web_domain_category_id']);
			$this->attach_thumbnail_url($row);
		}
		echo json_encode(array('Status' => 'Success', 'cat_data' => $row));
	}

	function save_ajax(){
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$is_add = (int)$this->input->post('is_add');
		$Title = trim((string)$this->input->post('Title'));
		$Description = (string)$this->input->post('Description');
		$parent_id = (int)$this->input->post('parent_id');
		$domain_en = $this->input->post('web_domain_id_en');
		$id_en = $this->input->post('id_en');
		$clear_thumbnail = (int)$this->input->post('clear_thumbnail');
		$is_visible = (int)$this->input->post('is_visible') ? 1 : 0;
		$seo_title = trim((string)$this->input->post('seo_title'));
		$seo_description = trim((string)$this->input->post('seo_description'));
		$seo_keywords = trim((string)$this->input->post('seo_keywords'));
		$seo_slug = trim((string)$this->input->post('seo_slug'));

		if($Title === ''){
			echo json_encode(array('Status' => 'Fail', 'Message' => 'Title required'));
			return;
		}

		$pic_name = '';
		if (!empty($_FILES['thumbnail']['name'])) {
			$upload_dir = APP_STORE_PATH . '/uploads/category/';
			if (!is_dir($upload_dir)) {
				@mkdir($upload_dir, 0755, true);
			}
			// Capture upload library echoes so they do not break JSON
			ob_start();
			$pic_name = $this->upload_bl->upload_file_pic_path('/uploads/category/', 'thumbnail');
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
			'Description' => $Description,
			'parent_id' => $parent_id,
			'is_visible' => $is_visible,
			'seo_title' => $seo_title,
			'seo_description' => $seo_description,
			'seo_keywords' => $seo_keywords,
			'seo_slug' => $seo_slug
		);
		if ($pic_name !== '') {
			$payload['thumbnail'] = $pic_name;
		}

		if($is_add === 1){
			$payload['web_domain_id_en'] = $domain_en;
			$payload['ShopID'] = $sess_shop_id;
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_category/category_add', $payload);
		}else{
			$payload['id_en'] = $id_en;
			if ($clear_thumbnail === 1 && $pic_name === '') {
				$payload['clear_thumbnail'] = 1;
			}
			$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_category/category_edit', $payload);
		}

		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if(!$ok){
			if(!empty($arr['Description'])){
				$msg = $arr['Description'];
			}elseif(!empty($arr['Message'])){
				$msg = $arr['Message'];
			}elseif(isset($arr['Status']) && $arr['Status'] === 'HttpError'){
				$msg = 'API HTTP '.(isset($arr['Code']) ? $arr['Code'] : '?');
			}elseif(isset($arr['Status']) && $arr['Status'] === 'JsonError'){
				$msg = 'API JSON error';
			}else{
				$msg = 'Save failed';
			}
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $ok ? '' : $msg
		));
	}

	function del_ajax(){
		$id_en = $this->input->post('id_en');
		$arr = $this->curl_bl->CallApiNospi('GET', 'webs/domain_category/category_del/'.$id_en);
		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if(!$ok){
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Delete failed');
			if(stripos($msg, 'children') !== false || stripos($msg, 'has children') !== false){
				$msg = 'Delete subcategories first';
			}
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $msg
		));
	}

	function visibility_ajax(){
		$id_en = $this->input->post('id_en');
		$is_visible = (int)$this->input->post('is_visible') ? 1 : 0;
		$arr = $this->curl_bl->CallApiNospi('POST', 'webs/domain_category/set_visible', array(
			'id_en' => $id_en,
			'is_visible' => $is_visible
		));
		$ok = isset($arr['Status']) && $arr['Status'] == 'Success';
		$msg = '';
		if(!$ok){
			$msg = !empty($arr['Description']) ? $arr['Description'] : (!empty($arr['Message']) ? $arr['Message'] : 'Update failed');
		}
		echo json_encode(array(
			'Status' => $ok ? 'Success' : 'Fail',
			'Message' => $msg,
			'is_visible' => $is_visible
		));
	}
}
