<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Domains Controller : manage domains.
**/
class Domains extends Auth_Controller
{
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

		$data_search = array(
			'domain_search' => '',
			'shopid_en' => $sess_shop_id,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);

		$arr_domains = $this->web_domain_model->get_by_shop($sess_shop_id,5);
		if($arr_domains['Status'] == "Success"){
			$max = sizeof($arr_domains['Data']);
			for($i=0;$i<$max;$i++){
				$arr_domains['Data'][$i]['web_domain_id'] = $this->encryption_util->encrypt_ssl($arr_domains['Data'][$i]['web_domain_id']);
			}
		}

		$data = array(
			'arr_domains' => $arr_domains['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'del_alt' => $del_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Manage Domains"
		);

		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/webs/domain_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
		);

		$this->view_util->load_view_main('webs/domains/domains_list',$data,NULL,$arr_js,$arr_input,NULL);
	}

	public function domains_list_search()
	{
		$add_alt = $this->session->flashdata('add_domain');
		$edit_alt = $this->session->flashdata('edit_domain');
		$del_alt = $this->session->flashdata('del_domain');
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$domain_search = $this->input->post('domain_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'domain_search' => $domain_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5
		);

		$arr_domains = $this->curl_bl->CallApi('POST','webs/domains/domain_search',$data_search);
		if($arr_domains['Status'] == "Success"){
			$max = sizeof($arr_domains['Data']);
			for($i=0;$i<$max;$i++){
				$arr_domains['Data'][$i]['web_domain_id'] = $this->encryption_util->encrypt_ssl($arr_domains['Data'][$i]['web_domain_id']);
			}
		}

		$data = array(
			'arr_domains' => $arr_domains['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'del_alt' => $del_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Manage Domains"
		);

		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/webs/domain_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
		);

		$this->view_util->load_view_main('webs/domains/domains_list',$data,NULL,$arr_js,$arr_input,NULL);
	}

	function loaddata_more_ajax(){
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$domain_search = $this->input->post('domain_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'domain_search' => $domain_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5
		);

		$arr_domains = $this->curl_bl->CallApiNospi('POST','webs/domains/domain_search',$data);

		if($arr_domains['Status'] == "Success"){
			$max = sizeof($arr_domains['Data']);

			for($i=0;$i<$max;$i++){
				$arr_domains['Data'][$i]['web_domain_id'] = $this->encryption_util->encrypt_ssl($arr_domains['Data'][$i]['web_domain_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_domains['Data']
		);
		echo json_encode($arr_data);
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
		

		$this->view_util->load_view_main('webs/domains/add_domain_form',NULL,NULL,$arr_js,$arr_input,NULL);
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
			'ShopID' => $shop_id
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_add',$data_curl);

		$this->web_domain_model->del_cache_by_shop($shop_id);

		if($arr_res['Status'] == "Success"){
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

		if($arr_domain['Status'] == "Success"){
			$arr_domain['Data']['web_domain_id'] = $this->encryption_util->encrypt_ssl($arr_domain['Data']['web_domain_id']);
		}

		$arr_input = array(
			'title' => "Edit Domain"
		);

		$data = array(
			'arr_domain' => $arr_domain['Data']
		);

		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",
			'domain' => base_url()."resources/js/validate/Webs/domain.js"
		);

		$this->view_util->load_view_main('webs/domains/edit_domain_form',$data,NULL,$arr_js,$arr_input,NULL);
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
			'web_domain_name' => $web_domain_name
		);

		$arr_res = $this->curl_bl->CallApi('POST','webs/domains/domain_edit',$data_curl);

		$this->web_domain_model->del_cache_by_id($id_en);

		if($arr_res['Status'] == "Success"){
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
		$arr_res = $this->curl_bl->CallApi('GET','webs/domains/del_action/'.$id_en);

		$this->web_domain_model->del_cache_by_shop($shop_id);

		if($arr_res['Status'] == "Success"){
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
}
