<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Page_permission extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('user_level_authen_model');

		$this->auth_bl->check_session_exists();

     }

	public function page_permission_list()
	{
		$add_alt = $this->session->flashdata('add_user');
		$edit_alt = $this->session->flashdata('edit_user');
		//echo $add_alt;
		$arr_levels = $this->user_level_authen_model->get_all();
		//print_r($arr_users);
		if($arr_levels['Status'] == "Success"){
			$max = sizeof($arr_levels['Data']);

			for($i=0;$i<$max;$i++){
				$arr_levels['Data'][$i]['user_level_authen_id'] = $this->encryption_util->encrypt_ssl($arr_levels['Data'][$i]['user_level_authen_id']);
			}
		}
		
		$data = array(
			'arr_levels' => $arr_levels['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Page permission"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/page_permission/page_permission_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);

	} 

	function add_permission_form(){

		$arr_input = array(
			'title' => "Page permission"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'validate_user' => base_url()."resources/js/validate/config_user.js"
    	);	
		
		$this->view_util->load_view_main('config_system/page_permission/add_permission_form',NULL,NULL,NULL,$arr_input,MENU_CONFIG_USER);
	}

	function page_permission_add(){

		$customer_code = $this->session->userdata('customer_code');

		$controller = $this->input->post('controller');
		$user_level = $this->input->post('user_level');
		$user_add = $this->input->post('user_add');
		$user_edit = $this->input->post('user_edit');
		$user_delete = $this->input->post('user_delete');

		$data_curl = array(
			'controller' => $controller,
			'user_level' => $user_level,
			'user_add' => $user_add,
			'user_edit' => $user_edit,
			'user_delete' => $user_delete,
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/page_permission/page_permission_add',$data_curl);
		//print_r($data_curl);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/page_permission/page_permission_list','refresh');
	}

	function edit_permission_form(){

		$id_en = $this->uri->segment(4);

		$arr_permission = $this->user_level_authen_model->get_by_id($id_en);

		if($arr_permission['Status'] == "Success"){

			$arr_permission['Data']['user_level_authen_id'] = $this->encryption_util->encrypt_ssl($arr_permission['Data']['user_level_authen_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Page permission"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js"
    	);	

    	$data = array(
			'arr_permission' => $arr_permission['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('config_system/page_permission/edit_permission_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function page_permission_edit(){

		$customer_code = $this->session->userdata('customer_code');

		$id_en = $this->input->post('id_en');
		$controller = $this->input->post('controller');
		$user_level = $this->input->post('user_level');
		$user_add = $this->input->post('user_add');
		$user_edit = $this->input->post('user_edit');
		$user_delete = $this->input->post('user_delete');

		$data_curl = array(
			'id_en' => $id_en,
			'controller' => $controller,
			'user_level' => $user_level,
			'user_add' => $user_add,
			'user_edit' => $user_edit,
			'user_delete' => $user_delete,
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/page_permission/page_permission_edit',$data_curl);
		//print_r($arr_res);


		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_menu','success');
		}else{
			$this->session->set_flashdata('edit_menu','fail');
		}

		redirect(base_url().'config_system/page_permission/page_permission_list','refresh');
	}

	function del_action(){

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/page_permission/del_action/'.$id_en);
		//print_r($arr_res);


		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/page_permission/page_permission_list','refresh');

	}

}