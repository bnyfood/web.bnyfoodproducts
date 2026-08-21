<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Domains Controller : manage domains.
**/
class Domains extends Auth_Controller
{
	private $allowed_per_page = array(5, 10, 25, 50, 100);

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');
		$this->load->model('web_domain_model');
	}

	public function domains_list()
	{
		$add_alt = $this->session->flashdata('add_domain');
		$edit_alt = $this->session->flashdata('edit_domain');
		$del_alt = $this->session->flashdata('del_domain');
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$per_page = $this->resolve_per_page($this->input->get_post('per_page'));
		$page = max(1, (int)$this->input->get_post('page'));
		$domain_search = trim((string)$this->input->get_post('domain_search'));
		$sortby = (string)$this->input->get_post('sortby');
		$sorttype = (string)$this->input->get_post('sorttype');
		$offset = ($page - 1) * $per_page;

		$data_search = array(
			'domain_search' => $domain_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => $per_page,
			'page' => $page
		);

		$parsed = $this->fetch_domain_page($data_search);
		$domain_rows = $parsed['rows'];
		$total = $parsed['total'];
		$total_pages = max(1, (int)ceil($total / $per_page));
		if($page > $total_pages){
			$page = $total_pages;
			$data_search['page'] = $page;
			$data_search['offset'] = ($page - 1) * $per_page;
			$parsed = $this->fetch_domain_page($data_search);
			$domain_rows = $parsed['rows'];
			$total = $parsed['total'];
		}

		$data = array(
			'arr_domains' => $domain_rows,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'del_alt' => $del_alt,
			'data_search' => $data_search,
			'total_rows' => $total,
			'total_pages' => $total_pages,
			'allowed_per_page' => $this->allowed_per_page
		);

		$arr_input = array(
			'title' => "Manage Domains"
		);

		$arr_js = array(
			'domain_list' => base_url()."resources/js/morecontent/webs/domain_list.js"
		);

		$this->view_util->load_view_main('webs/domains/domains_list',$data,NULL,$arr_js,$arr_input,MENU_WEBS_DOMAINS);
	}

	public function domains_list_search()
	{
		// Same renderer as list; keep URL compatible with existing form action
		$this->domains_list();
	}

	function loaddata_more_ajax(){
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$domain_search = trim((string)$this->input->post('domain_search'));
		$sortby = (string)$this->input->post('sortby');
		$sorttype = (string)$this->input->post('sorttype');
		$per_page = $this->resolve_per_page($this->input->post('per_page'));
		$page = max(1, (int)$this->input->post('page'));
		$offset = ($page - 1) * $per_page;

		$data = array(
			'domain_search' => $domain_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => $per_page
		);

		$parsed = $this->fetch_domain_page($data, true);
		$total_pages = max(1, (int)ceil($parsed['total'] / $per_page));

		echo json_encode(array(
			'list_data' => $parsed['rows'],
			'total' => $parsed['total'],
			'page' => $page,
			'per_page' => $per_page,
			'total_pages' => $total_pages
		));
	}

	public function add_domain_form()
	{
		$arr_input = array(
			'title' => "Add Domain"
		);

		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",
			'domain' => base_url()."resources/js/validate/Webs/domain.js"
		);

		$this->view_util->load_view_main('webs/domains/add_domain_form',NULL,NULL,$arr_js,$arr_input,MENU_WEBS_DOMAINS);
	}

	public function domain_add()
	{
		$web_domain_name = trim($this->input->post('web_domain_name'));
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		if($web_domain_name === ''){
			$this->session->set_flashdata('add_domain','fail');
			redirect(base_url().'webs/domains/add_domain_form','refresh');
			return;
		}

		$data_curl = array(
			'web_domain_name' => $web_domain_name,
			'ShopID' => $shop_id,
			'registrar_link' => trim((string)$this->input->post('registrar_link')),
			'ssl_link' => trim((string)$this->input->post('ssl_link')),
			'expire_date' => trim((string)$this->input->post('expire_date'))
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_add',$data_curl);

		$this->web_domain_model->del_cache_by_shop($shop_id);

		if(isset($arr_res['Status']) && $arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_domain','success');
		}else{
			$this->session->set_flashdata('add_domain','fail');
		}

		redirect(base_url().'webs/domains/domains_list','refresh');
	}

	public function domain_edit_form()
	{
		$id_en = $this->uri->segment(4);
		$arr_domain = $this->web_domain_model->get_by_id($id_en);
		$domain_row = array(
			'web_domain_id' => $id_en,
			'web_domain_name' => '',
			'registrar_link' => '',
			'ssl_link' => '',
			'expire_date' => ''
		);

		if(isset($arr_domain['Status']) && $arr_domain['Status'] == "Success" && !empty($arr_domain['Data'])){
			$domain_row = $arr_domain['Data'];
			$domain_row['web_domain_id'] = $this->encryption_util->encrypt_ssl($domain_row['web_domain_id']);
			$domain_row['expire_date'] = $this->format_expire_date_input(isset($domain_row['expire_date']) ? $domain_row['expire_date'] : '');
		}

		$arr_input = array(
			'title' => "Edit Domain"
		);

		$data = array(
			'arr_domain' => $domain_row
		);

		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",
			'domain' => base_url()."resources/js/validate/Webs/domain.js"
		);

		$this->view_util->load_view_main('webs/domains/edit_domain_form',$data,NULL,$arr_js,$arr_input,MENU_WEBS_DOMAINS);
	}

	public function domain_edit()
	{
		$id_en = $this->input->post('id_en');
		$web_domain_name = trim($this->input->post('web_domain_name'));

		if($web_domain_name === ''){
			$this->session->set_flashdata('edit_domain','fail');
			redirect(base_url().'webs/domains/domain_edit_form/'.$id_en,'refresh');
			return;
		}

		$data_curl = array(
			'id_en' => $id_en,
			'web_domain_name' => $web_domain_name,
			'registrar_link' => trim((string)$this->input->post('registrar_link')),
			'ssl_link' => trim((string)$this->input->post('ssl_link')),
			'expire_date' => trim((string)$this->input->post('expire_date'))
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_edit',$data_curl);

		$this->web_domain_model->del_cache_by_id($id_en);

		if(isset($arr_res['Status']) && $arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_domain','success');
		}else{
			$this->session->set_flashdata('edit_domain','fail');
		}

		redirect(base_url().'webs/domains/domains_list','refresh');
	}

	public function del_action()
	{
		$id_en = $this->uri->segment(4);
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$arr_res = $this->curl_bl->CallApiNospi('GET','webs/domains/del_action/'.$id_en);

		$this->web_domain_model->del_cache_by_shop($shop_id);
		$this->web_domain_model->del_cache_by_id($id_en);

		$ok = isset($arr_res['Status']) && $arr_res['Status'] == "Success";

		if($this->input->is_ajax_request()){
			echo json_encode(array(
				'Status' => $ok ? 'Success' : 'Fail'
			));
			return;
		}

		if($ok){
			$this->session->set_flashdata('del_domain','success');
		}else{
			$this->session->set_flashdata('del_domain','fail');
		}

		redirect(base_url().'webs/domains/domains_list','refresh');
	}

	function domain_chk_name_invalid(){
		$web_domain_name = $this->input->post('web_domain_name');
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$data_curl = array(
			'web_domain_name' => $web_domain_name,
			'ShopID' => $shop_id
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_chk_name_invalid',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function domain_chk_name_invalid_edit(){
		$web_domain_name = $this->input->post('web_domain_name');
		$id_en = $this->input->post('id_en');
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$data_curl = array(
			'web_domain_name' => $web_domain_name,
			'id_en' => $id_en,
			'ShopID' => $shop_id
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_chk_name_invalid_edit',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	private function resolve_per_page($value){
		$per_page = (int)$value;
		if(!in_array($per_page, $this->allowed_per_page, true)){
			return 10;
		}
		return $per_page;
	}

	private function fetch_domain_page($data_search, $nospin = false){
		if($nospin){
			$arr_domains = $this->curl_bl->CallApiNospi('POST','webs/domains/domain_search',$data_search);
		}else{
			$arr_domains = $this->curl_bl->CallApi('POST','webs/domains/domain_search',$data_search);
		}

		$rows = array();
		$total = 0;
		if(isset($arr_domains['Status']) && $arr_domains['Status'] == "Success" && !empty($arr_domains['Data'])){
			$payload = $arr_domains['Data'];
			if(isset($payload['rows']) && is_array($payload['rows'])){
				$rows = $payload['rows'];
				$total = isset($payload['total']) ? (int)$payload['total'] : count($rows);
			}elseif(is_array($payload) && isset($payload[0])){
				// backward compatible flat list
				$rows = $payload;
				$total = count($rows);
			}
		}

		$max = sizeof($rows);
		for($i=0;$i<$max;$i++){
			if(isset($rows[$i]['web_domain_id'])){
				$rows[$i]['web_domain_id'] = $this->encryption_util->encrypt_ssl($rows[$i]['web_domain_id']);
			}
			$rows[$i]['expire_date_display'] = $this->format_expire_date_display(isset($rows[$i]['expire_date']) ? $rows[$i]['expire_date'] : '');
		}

		return array('rows' => $rows, 'total' => $total);
	}

	private function format_expire_date_input($value){
		$value = trim((string)$value);
		if($value === ''){
			return '';
		}
		$ts = strtotime($value);
		if($ts === false){
			return '';
		}
		return date('Y-m-d', $ts);
	}

	private function format_expire_date_display($value){
		$value = trim((string)$value);
		if($value === ''){
			return '';
		}
		$ts = strtotime($value);
		if($ts === false){
			return $value;
		}
		return date('Y-m-d', $ts);
	}
}
