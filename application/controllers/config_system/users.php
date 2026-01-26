<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Users extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('user_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function user_list()
	{
		$add_alt = $this->session->flashdata('add_user');
		$edit_alt = $this->session->flashdata('edit_user');
		//echo $add_alt;
		$customer_code = $this->session->userdata('customer_code');
		$arr_users = $this->user_model->get_users_by_code($customer_code);
		//print_r($arr_users);
		if($arr_users['Status'] == "Success"){
			$max = sizeof($arr_users['Data']);

			for($i=0;$i<$max;$i++){
				$arr_users['Data'][$i]['BNYCustomerID'] = $this->encryption_util->encrypt_ssl($arr_users['Data'][$i]['BNYCustomerID']);
			}
		}
		
		$data = array(
			'arr_users' => $arr_users['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/users/user_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);

	} 

	function add_user_form(){

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'validate_user' => base_url()."resources/js/validate/config_user.js",
        	'province' => base_url()."resources/js/provinces.js",
        	'config_user' => base_url()."resources/js/config_user.js"
    	);	
		
		$arr_provinces = $this->curl_bl->CallApi('GET','Provinces/get_provinces_all');

		$data = array(
			'arr_provinces' => $arr_provinces['Data'],
		);
		
		$this->view_util->load_view_main('config_system/users/add_user_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function user_add(){

		$customer_code = $this->session->userdata('customer_code');

		$text_name = $this->input->post('text_name');
		$CompanyName = $this->input->post('CompanyName');
		$address1 = $this->input->post('address1');
		$province_sel = $this->input->post('province_sel');
		$district_sel = $this->input->post('district_sel');
		$subdistrict_sel = $this->input->post('subdistrict_sel');
		$Zip = $this->input->post('Zip');
		$Tax = $this->input->post('Tax');
		$Mobile = $this->input->post('Mobile');
		$Mobile2 = $this->input->post('Mobile2');
		$Line = $this->input->post('Line');
		$email = $this->input->post('txt_email');
		$usergroup_id = USER_GROUP_USERTMP;
		$password = $this->input->post('txt_password');
		$password = $this->input->post('txt_password');
		$customer_code = $this->session->userdata('customer_code');

		$data_curl = array(
			'text_name' => $text_name,
			'CompanyName' => $CompanyName,
			'address1' => $address1,
			'province_sel' => $province_sel,
			'district_sel' => $district_sel,
			'subdistrict_sel' => $subdistrict_sel,
			'Zip' => $Zip,
			'Tax' => $Tax,
			'Mobile' => $Mobile,
			'Mobile2' => $Mobile2,
			'Line' => $Line,
			'email' => $email,
			'password' => $password,
			'usergroup_id' => $usergroup_id,
			'customer_type' => 3,
			'customer_code' => $customer_code

		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/users/user_add',$data_curl);
		//print_r($data_curl);
		
		$this->user_model->del_cache_by_cus_id($customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/users/user_list','refresh');
	}

	function user_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_user = $this->user_model->get_by_id($id_en);

		if($arr_user['Status'] == "Success"){

			$arr_user['Data']['BNYCustomerID'] = $this->encryption_util->encrypt_ssl($arr_user['Data']['BNYCustomerID']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config User"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js"
    	);	

    	$arr_provinces = $this->curl_bl->CallApi('GET','Provinces/get_provinces_all');
    	$arr_districts = $this->curl_bl->CallApi('GET','Provinces/get_districts_all');
    	$arr_subdistricts = $this->curl_bl->CallApi('GET','Provinces/get_subdistricts_all');

    	$data = array(
			'arr_user' => $arr_user['Data'],
			'arr_provinces' => $arr_provinces['Data'],
			'arr_districts' => $arr_districts['Data'],
			'arr_subdistricts' => $arr_subdistricts['Data']
		);
		
		
		$this->view_util->load_view_main('config_system/users/edit_user_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function user_edit(){

		$customer_code = $this->session->userdata('customer_code');

		$id_en = $this->input->post('id_en');
		$text_name = $this->input->post('text_name');
		$CompanyName = $this->input->post('CompanyName');
		$address1 = $this->input->post('address1');
		$province_sel = $this->input->post('province_sel');
		$district_sel = $this->input->post('district_sel');
		$subdistrict_sel = $this->input->post('subdistrict_sel');
		$Zip = $this->input->post('Zip');
		$Tax = $this->input->post('Tax');
		$Mobile = $this->input->post('Mobile');
		$Mobile2 = $this->input->post('Mobile2');
		$Line = $this->input->post('Line');


		$data_curl = array(
			'id_en' => $id_en,
			'text_name' => $text_name,
			'CompanyName' => $CompanyName,
			'address1' => $address1,
			'province_sel' => $province_sel,
			'district_sel' => $district_sel,
			'subdistrict_sel' => $subdistrict_sel,
			'Zip' => $Zip,
			'Tax' => $Tax,
			'Mobile' => $Mobile,
			'Mobile2' => $Mobile2,
			'Line' => $Line
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/users/user_edit',$data_curl);
		//print_r($arr_res);

		$this->user_model->del_cache_by_cus_id($customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_menu','success');
		}else{
			$this->session->set_flashdata('edit_menu','fail');
		}

		redirect(base_url().'config_system/users/user_list','refresh');
	}

	function user_chk_username_invalid_code(){

		$txt_email = $this->input->post('txt_email');
		$customer_code = $this->session->userdata('customer_code');

		$data_curl = array(
			'txt_email' => $txt_email,
			'customer_code' => $customer_code
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/users/user_chk_username_invalid_code',$data_curl);
		//print_r($arr_res['Data']);
		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}


	}

	function del_action(){

		$customer_code = $this->session->userdata('customer_code');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/users/del_action/'.$id_en);
		//print_r($arr_res);

		$this->user_model->del_cache_by_cus_id($customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/users/user_list','refresh');

	}

}