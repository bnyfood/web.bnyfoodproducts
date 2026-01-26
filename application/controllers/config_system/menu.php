<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Menu extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		
       	$this->auth_bl->check_session_exists();
		
     }

     public function menu_list()
	{
		$add_alt = $this->session->flashdata('add_menu');
		$edit_alt = $this->session->flashdata('edit_menu');
		//echo $add_alt;
		$arr_menus = $this->curl_bl->CallApi('GET','config_system/menu/get_menu_all');
		//print_r($arr_menus);
		if($arr_menus['Status'] == "Success"){
			$max = sizeof($arr_menus['Data']);

			for($i=0;$i<$max;$i++){
					$arr_menus['Data'][$i]['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menus['Data'][$i]['menu_id']);
							 
			}
		}
		
		$data = array(
			'arr_menus' => $arr_menus['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/menu/menu_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_MENU);

	} 

	function add_menu_form(){

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_menu' => base_url()."resources/js/validate/config_menu.js",
    	);	
		
		
		$this->view_util->load_view_main('config_system/menu/add_menu_form',NULL,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function menu_add(){
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$parent_menu = $this->encryption_util->encrypt_ssl('root');

		$data_curl = array(
			'parent_menu' => $parent_menu,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_add',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/menu_list','refresh');
	}

	function menu_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_menu = $this->curl_bl->CallApi('GET','config_system/menu/get_by_id/'.$id_en);

		if($arr_menu['Status'] == "Success"){

			$arr_menu['Data']['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menu['Data']['menu_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_menu' => base_url()."resources/js/validate/config_menu.js",
    	);		

    	$data = array(
			'arr_menu' => $arr_menu['Data']
		);
		
		
		$this->view_util->load_view_main('config_system/menu/edit_menu_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function menu_edit(){

		$id_en = $this->input->post('id_en');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'id_en' => $id_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_edit',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_menu','success');
		}else{
			$this->session->set_flashdata('edit_menu','fail');
		}

		redirect(base_url().'config_system/menu/menu_list','refresh');
	}

	public function sub_menu_list()
	{
		$add_alt = $this->session->flashdata('add_menu');
		$edit_alt = $this->session->flashdata('edit_menu');
		//echo $add_alt;

		$id_en = $this->uri->segment(4);
		$arr_menus = $this->curl_bl->CallApi('GET','config_system/menu/get_sub_menu_all/'.$id_en);
		//print_r($arr_menus);
		if($arr_menus['Status'] == "Success"){
			$max = sizeof($arr_menus['Data']);

			for($i=0;$i<$max;$i++){
					$arr_menus['Data'][$i]['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menus['Data'][$i]['menu_id']);
							 
			}
		}
		
		$data = array(
			'arr_menus' => $arr_menus['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'parentid' => $id_en
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/menu/sub_menu_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_MENU);

	} 

	function add_sub_menu_form(){

		$parentid_en = $this->uri->segment(4);

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_submenu' => base_url()."resources/js/validate/config_submenu.js",
    	);	

    	$data=array(
    		'parentid_en' => $parentid_en
    	);
		
		
		$this->view_util->load_view_main('config_system/menu/add_sub_menu_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function sub_menu_add(){

		$parentid_en = $this->input->post('parentid_en');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'parent_menu' => $parentid_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_add',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_list/'.$parentid_en,'refresh');
	}

	function sub_menu_edit_form(){

		$id_en = $this->uri->segment(4);
		$parentid = $this->uri->segment(5);

		$arr_menu = $this->curl_bl->CallApi('GET','config_system/menu/get_by_id/'.$id_en);

		if($arr_menu['Status'] == "Success"){

			$arr_menu['Data']['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menu['Data']['menu_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_submenu' => base_url()."resources/js/validate/config_submenu.js",
    	);		

    	$data = array(
			'arr_menu' => $arr_menu['Data'],
			'parentid' => $parentid
		);
		
		
		$this->view_util->load_view_main('config_system/menu/edit_sub_menu_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function sub_menu_edit(){

		$id_en = $this->input->post('id_en');
		$parentid = $this->input->post('parentid');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'id_en' => $id_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_edit',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_menu','success');
		}else{
			$this->session->set_flashdata('edit_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_list/'.$parentid,'refresh');
	}

	public function sub_menu_lv3_list()
	{
		$add_alt = $this->session->flashdata('add_menu');
		$edit_alt = $this->session->flashdata('edit_menu');
		//echo $add_alt;

		$id_en = $this->uri->segment(4);
		$id_lv2_en = $this->uri->segment(5);
		$arr_menus = $this->curl_bl->CallApi('GET','config_system/menu/get_sub_menu_all/'.$id_lv2_en);
		//print_r($arr_menus);
		if($arr_menus['Status'] == "Success"){
			$max = sizeof($arr_menus['Data']);

			for($i=0;$i<$max;$i++){
					$arr_menus['Data'][$i]['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menus['Data'][$i]['menu_id']);
							 
			}
		}
		
		$data = array(
			'arr_menus' => $arr_menus['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'parentid' => $id_en,
			'id_lv2_en' => $id_lv2_en
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/menu/sub_menu_lv3_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_MENU);

	} 

	function add_sub_menu_lv3_form(){

		$parentid_en = $this->uri->segment(4);
		$id_lv2_en = $this->uri->segment(5);

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_submenu' => base_url()."resources/js/validate/config_submenu.js",
    	);	

    	$data=array(
    		'parentid_en' => $parentid_en,
    		'id_lv2_en' => $id_lv2_en
    	);
		
		
		$this->view_util->load_view_main('config_system/menu/add_sub_menu_lv3_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function sub_menu_lv3_add(){

		$parentid_en = $this->input->post('parentid_en');
		$id_lv2_en = $this->input->post('id_lv2_en');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'parent_menu' => $id_lv2_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_add',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_lv3_list/'.$parentid_en.'/'.$id_lv2_en,'refresh');
	}

	function sub_menu_lv3_add_tmp(){

		$parentid_en = $this->input->post('parentid_en');
		$id_lv2_en = $this->input->post('id_lv2_en');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'parent_menu' => $id_lv2_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_add_tmp',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_lv3_list/'.$parentid_en.'/'.$id_lv2_en,'refresh');
	}


	function sub_menu_lv3_edit_form(){

		$id_en = $this->uri->segment(4);
		$parentid = $this->uri->segment(5);
		$id_lv2_en = $this->uri->segment(6);

		$arr_menu = $this->curl_bl->CallApi('GET','config_system/menu/get_by_id/'.$id_en);

		if($arr_menu['Status'] == "Success"){

			$arr_menu['Data']['menu_id'] = $this->encryption_util->encrypt_ssl($arr_menu['Data']['menu_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config Menu"
		);
		
		$arr_js = array(
        	'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_submenu' => base_url()."resources/js/validate/config_submenu.js",
    	);		

    	$data = array(
			'arr_menu' => $arr_menu['Data'],
			'parentid' => $parentid,
			'id_lv2_en' => $id_lv2_en
		);
		
		
		$this->view_util->load_view_main('config_system/menu/edit_sub_menu_lv3_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_MENU);
	}

	function sub_menu_lv3_edit(){

		$id_en = $this->input->post('id_en');
		$parentid = $this->input->post('parentid');
		$id_lv2_en = $this->input->post('id_lv2_en');
		$menu_name = $this->input->post('menu_name');
		$link = $this->input->post('link');
		$icon = $this->input->post('icon');
		$sort = $this->input->post('sort');
		$show_customer = $this->input->post('show_customer');

		$data_curl = array(
			'id_en' => $id_en,
			'menu_name' => $menu_name,
			'link' => $link,
			'icon' => $icon,
			'sort' => $sort,
			'show_customer' => $show_customer
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/menu/menu_edit',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_menu','success');
		}else{
			$this->session->set_flashdata('edit_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_lv3_list/'.$parentid.'/'.$id_lv2_en,'refresh');
	}

	function del_action(){

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/menu/del_action/'.$id_en);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/menu_list','refresh');

	}

	function del_sub_action(){

		$id_en = $this->uri->segment(4);
		$parentid_en = $this->uri->segment(5);


		$arr_res = $this->curl_bl->CallApi('GET','config_system/menu/del_action/'.$id_en);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_list/'.$parentid_en,'refresh');

	}

	function del_sub_lv3_action(){

		$id_en = $this->uri->segment(4);
		$parentid_en = $this->uri->segment(5);
		$id_lv2_en = $this->uri->segment(6);


		$arr_res = $this->curl_bl->CallApi('GET','config_system/menu/del_action/'.$id_en);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/menu/sub_menu_lv3_list/'.$parentid_en.'/'.$id_lv2_en,'refresh');

	}




 }