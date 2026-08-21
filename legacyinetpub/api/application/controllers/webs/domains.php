<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Domains Controller : manage domains.
**/
class Domains extends CI_Controller
{
	private $arr_header = '';

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		$this->load->library('businesslogic/api_log_bl');
		$this->load->library('businesslogic/api_auth_bl');
		$this->load->library('businesslogic/data_bl');

		$this->load->model('web_domain_model');

		$this->arr_header = $this->api_auth_bl->check_api_auth_token();
	}

	function get_by_shop(){
		$shopid_en = $this->uri->segment(4);
		$per_page = $this->uri->segment(5);
		$shopid = $this->encryption_util->decrypt_ssl($shopid_en);

		$data = $this->web_domain_model->select_by_shop_id_limit($shopid,$per_page);

		if(!empty($data)){
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$this->arr_header['api_token']); 
		}
		
		echo $data_json['view'];
	}

	function get_by_id(){
		$id_en = $this->uri->segment(4);
		$id = $this->encryption_util->decrypt_ssl($id_en);

		$data = $this->web_domain_model->select_by_id($id);

		if(!empty($data)){
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$this->arr_header['api_token']); 
		}
		
		echo $data_json['view'];
	}

	function domain_add(){
		$web_domain_name = trim($this->input->post('web_domain_name'));
		$ShopID = $this->input->post('ShopID');
		$ShopID = $this->encryption_util->decrypt_ssl($ShopID);

		if($web_domain_name === ''){
			$data_json = $this->json_util->make_json('Select data','Fail','','web_domain_name required',$this->arr_header['api_token']); 
			echo $data_json['view'];
			return;
		}

		$arr_data = array(
			'web_domain_name' => $web_domain_name,
			'ShopID' => $ShopID
		);

		$data_re = $this->web_domain_model->insert($arr_data);

		if(!empty($data_re)){
			$data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$this->arr_header['api_token']); 
		}
		
		echo $data_json['view'];
	}

	function domain_edit(){
		$id_en = $this->input->post('id_en');
		$id = $this->encryption_util->decrypt_ssl($id_en);
		$web_domain_name = trim($this->input->post('web_domain_name'));

		if($web_domain_name === ''){
			$data_json = $this->json_util->make_json('Select data','Fail','','web_domain_name required',$this->arr_header['api_token']); 
			echo $data_json['view'];
			return;
		}

		$arr_data = array(
			'web_domain_name' => $web_domain_name
		);

		$data_re = $this->web_domain_model->update($arr_data,$id);

		if(!empty($data_re)){
			$data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$this->arr_header['api_token']); 
		}
		
		echo $data_json['view'];
	}

	function del_action(){
		$id_en = $this->uri->segment(4);
		$id = $this->encryption_util->decrypt_ssl($id_en);

		$this->web_domain_model->delete($id);

		$data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$this->arr_header['api_token']); 
		
		echo $data_json['view'];
	}

	function domain_search(){
		$arr_header = $this->api_auth_bl->get_header();

		$chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
		if($chk_auth['Status'] == "Success"){
			$shopid_en = $this->input->post('shopid_en'); 
			$shopid = $this->encryption_util->decrypt_ssl($shopid_en);

			$domain_search = $this->input->post('domain_search');
			$sortby = $this->input->post('sortby');
			$sorttype = $this->input->post('sorttype');
			$offset = $this->input->post('offset');
			$per_page = $this->input->post('per_page');

			$data = $this->web_domain_model->select_by_shop_id_search($shopid,$domain_search,$per_page,$offset,$sortby,$sorttype);

			if(!empty($data)){
				$data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$arr_header['api_token']); 
			}else{
				$data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$arr_header['api_token']); 
			}
			
			echo $data_json['view'];
		}else{
			$chk_auth = $this->json_util->json_unicode($chk_auth);
			echo $chk_auth;
		}
	}

	function domain_chk_name_invalid(){
		$web_domain_name = $this->input->post('web_domain_name');
		$ShopID = $this->input->post('ShopID');
		$ShopID = $this->encryption_util->decrypt_ssl($ShopID);

		$data = $this->web_domain_model->select_by_name($web_domain_name,$ShopID);

		if(!empty($data)){
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$this->arr_header['api_token']); 
		}

		echo $data_json['view'];
	}

	function domain_chk_name_invalid_edit(){
		$web_domain_name = $this->input->post('web_domain_name');
		$id_en = $this->input->post('id_en');
		$ShopID = $this->input->post('ShopID');
		$id = $this->encryption_util->decrypt_ssl($id_en);
		$ShopID = $this->encryption_util->decrypt_ssl($ShopID);

		$data = $this->web_domain_model->select_by_name_except_id($web_domain_name,$ShopID,$id);

		if(!empty($data)){
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$this->arr_header['api_token']); 
		}else{
			$data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$this->arr_header['api_token']); 
		}

		echo $data_json['view'];
	}
}
