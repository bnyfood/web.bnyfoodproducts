<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Shops extends CI_Controller
{
	private $arr_header = '';
	private $api_authen = true;
    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		$this->load->library('businesslogic/api_log_bl');
		$this->load->library('businesslogic/api_auth_bl');
		$this->load->library('businesslogic/data_bl');


		$this->load->model('web_shop_model');
		$this->load->model('user_group_model');

		$this->arr_header = $this->api_auth_bl->check_api_auth_token();
		
     }

     function get_shop_all(){

	      $data_res = $this->web_shop_model->select_all();

	      if(!empty($data_res)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select No data',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function get_shop_by_code(){


	      $code = $this->uri->segment(4);

	      $data_res = $this->web_shop_model->select_by_code($code);

	      if(!empty($data_res)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select No data',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function shop_search(){

	      $customer_code = $this->input->post('customer_code');
	      $shop_search = $this->input->post('shop_search');
	      $sortby = $this->input->post('sortby');
	      $sorttype = $this->input->post('sorttype');
	      $offset = $this->input->post('offset');
	      $per_page = $this->input->post('per_page');

	      $data_res = $this->web_shop_model->select_by_code_search($customer_code,$shop_search,$per_page,$offset,$sortby,$sorttype);

	      if(!empty($data_res)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select No data',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function get_shop_by_customer_type(){


	      $customer_type = $this->uri->segment(4);
	      $customer_code = $this->uri->segment(5);
	      

	      if($customer_type == "1"){
	      	$data_res = $this->web_shop_model->select_all();
	      }elseif($customer_type == "2"){
	      	$data_res = $this->web_shop_model->select_by_code($customer_code);
	      }else{
	      	$group_id_en = $this->uri->segment(6);
	      	$group_id = $this->encryption_util->decrypt_ssl($group_id_en);
	      	$data_group = $this->user_group_model->select_by_id($group_id);
	      	if(!empty($data_group)){
	      		$data_res = $this->web_shop_model->select_by_id_all($data_group['ShopID']);
	      	}
	      }

	      

	      if(!empty($data_res)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_res,'Select No data',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function get_by_id(){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data = $this->web_shop_model->select_by_id($id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function shop_add(){


	    	$ShopName = $this->input->post('ShopName');
	    	$domain = $this->input->post('domain');
			$URL_home = $this->input->post('URL_home');
			$ip = $this->input->post('ip');
			$customer_code = $this->input->post('customer_code');

			$arr_menu = array(
				'ShopName' => $ShopName,
				'domain' => $domain,
				'URL_home' => $URL_home,
				'ip' => $ip,
				'customer_code' => $customer_code
			);

	      $data_re = $this->web_shop_model->insert($arr_menu);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	function shop_edit(){

	    	$id_en = $this->input->post('id_en');
	    	$id = $this->encryption_util->decrypt_ssl($id_en);
	    	$ShopName = $this->input->post('ShopName');
	    	$domain = $this->input->post('domain');
			$URL_home = $this->input->post('URL_home');
			$ip = $this->input->post('ip');

			$arr_data = array(
				'ShopName' => $ShopName,
				'domain' => $domain,
				'URL_home' => $URL_home,
				'ip' => $ip,
			);

	      $data_re = $this->web_shop_model->update($arr_data,$id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];


	}

	function del_action(){

	      $id_en = $this->uri->segment(4);
	   	  $id = $this->encryption_util->decrypt_ssl($id_en);

	      $this->web_shop_model->delete($id);

	      $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$this->arr_header['api_token']); 
	      
	      echo $data_json['view'];


	}

}