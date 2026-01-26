<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Usergroup extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/selectbox_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('user_group_model');
		$this->load->model('user_model');
		$this->load->model('web_shop_model');

        $this->auth_bl->check_session_exists();
     }
     
	public function usergroup_list()
	{

		$add_alt = $this->session->flashdata('add_usergroup');
		$edit_alt = $this->session->flashdata('edit_usergroup');
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
		//echo $customer_code;

		$arr_shops = $this->web_shop_model->get_shop_by_code($customer_code);
		//echo $add_alt;

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$id_shop = $this->encryption_util->decrypt_ssl($sess_shop_id);
		//echo "shop>>".$id_shop."<<";

		$arr_usergroups = $this->user_group_model->get_usergroup_by_shop($id_shop);
		//print_r($arr_usergroups);
		if($arr_usergroups['Status'] == "Success"){
			$max = sizeof($arr_usergroups['Data']);

			for($i=0;$i<$max;$i++){
				$arr_usergroups['Data'][$i]['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroups['Data'][$i]['usergroup_id']);
			}
		}
		
		$data = array(
			'arr_usergroups' => $arr_usergroups['Data'],
			'arr_shops' => $arr_shops['Data'],
			'id_shop' => $id_shop,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
        	'usergroup_list' => base_url()."resources/js/config_system/usergroup/usergroup_list.js"
    	);
		
		$this->view_util->load_view_main('app_setting/usergroup/usergroup_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	} 

	function add_usergroup_form(){

		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	//'config_usergroup' => base_url()."resources/js/validate/config_usergroup.js"
    	);	
		
		$this->view_util->load_view_main('config_system/usergroup/add_usergroup_form',NULL,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function usergroup_add(){

		$group_name = $this->input->post('group_name');
		$is_add = $this->input->post('is_add');
		$is_edit = $this->input->post('is_edit');
		$is_del = $this->input->post('is_del');
		$is_read = $this->input->post('is_read');
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');

		$data_curl = array(
			'group_name' => $group_name,
			'is_add' => $is_add,
			'is_edit' => $is_edit,
			'is_del' => $is_del,
			'is_read' => $is_read,
			'ShopID' => $shop_id,
			'customer_code' => $customer_code
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_add',$data_curl);
		//print_r($arr_res);
		$this->user_group_model->del_cache_usergroup_by_shop($shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');
	}

	function usergroup_edit_form(){

		$id_en = $this->uri->segment(4);

		//$arr_usergroup = $this->curl_bl->CallApi('GET','config_system/usergroup/get_by_id/'.$id_en);

		$arr_usergroup = $this->user_group_model->get_by_id($id_en);

		if($arr_usergroup['Status'] == "Success"){

			$arr_usergroup['Data']['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroup['Data']['usergroup_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_usergroup_edit.js"
    	);

    	$data = array(
			'arr_usergroup' => $arr_usergroup['Data']
		);
		
		
		$this->view_util->load_view_main('config_system/usergroup/edit_usergroup_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function usergroup_edit(){

		$id_en = $this->input->post('id_en');
		$group_name = $this->input->post('group_name');
		$is_add = $this->input->post('is_add');
		$is_edit = $this->input->post('is_edit');
		$is_del = $this->input->post('is_del');
		$is_read = $this->input->post('is_read');

		$data_curl = array(
			'id_en' => $id_en,
			'group_name' => $group_name,
			'is_add' => $is_add,
			'is_edit' => $is_edit,
			'is_del' => $is_del,
			'is_read' => $is_read
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_edit',$data_curl);
		//print_r($arr_res);
		$this->user_group_model->del_cache_by_id($id_en);
		$this->user_group_model->del_cache_usergroup_by_shop($id_shop);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');

		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');
	}

}