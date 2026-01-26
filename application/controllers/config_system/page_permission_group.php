<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Page_permission_group extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/selectbox_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('user_group_model');
		$this->load->model('group_map_controller_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function page_list()
	{
		$add_alt = $this->session->flashdata('add_data');
		$edit_alt = $this->session->flashdata('edit_data');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;


		$arr_datas = $this->group_map_controller_model->get_all(5);

		//print_r($arr_sippliers);
		if($arr_datas['Status'] == "Success"){
			$max = sizeof($arr_datas['Data']);

			for($i=0;$i<$max;$i++){
				$arr_datas['Data'][$i]['group_map_controller_id'] = $this->encryption_util->encrypt_ssl($arr_datas['Data'][$i]['group_map_controller_id']);
			}
		}

		$data_search = array(
			'text_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);
		
		$data = array(
			'arr_datas' => $arr_datas['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Permission"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/config_system/page_permission_group.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

    	
		$this->view_util->load_view_main('config_system/page_permission_group/page_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		
		

	} 

	function add_form(){

		$arr_input = array(
			'title' => "Permission"
		);

		$arr_usergroups = $this->user_group_model->get_usergroup_all();

		$data = array(
			'arr_usergroups' => $arr_usergroups['Data']
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js"
    	);	
		
		
		$this->view_util->load_view_main('config_system/page_permission_group/add_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		
		
		
	}

	function add_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$controller = $this->input->post('controller');
		$usergroup_id = $this->input->post('usergroup_id');

		$data_curl = array(
			'controller' => $controller,
			'usergroup_id' => $usergroup_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/page_permission_group/add_action',$data_curl);
		//print_r($data_curl);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_data','success');
		}else{
			$this->session->set_flashdata('add_data','fail');
		}

		redirect(base_url().'config_system/page_permission_group/page_list','refresh');
	}

	function edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_data = $this->group_map_controller_model->get_by_id($id_en);

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Permission"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js"
    	);	

    	$arr_usergroups = $this->user_group_model->get_usergroup_all();

    	$data = array(
    		'arr_data' => $arr_data['Data'],
			'arr_usergroups' => $arr_usergroups['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('config_system/page_permission_group/edit_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
	}

	function edit_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->input->post('id_en');

		$controller = $this->input->post('controller');
		$usergroup_id = $this->input->post('usergroup_id');

		$data_curl = array(
			'controller' => $controller,
			'usergroup_id' => $usergroup_id,
			'id_en' => $id_en

		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/page_permission_group/edit_action',$data_curl);
		//print_r($arr_res);


		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_data','success');
		}else{
			$this->session->set_flashdata('edit_data','fail');
		}

		redirect(base_url().'config_system/page_permission_group/page_list','refresh');
	}


	function del_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/page_permission_group/del_action/'.$id_en);
		//print_r($arr_res);


		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_brand','success');
		}else{
			$this->session->set_flashdata('add_brand','fail');
		}

		redirect(base_url().'config_system/page_permission_group/page_list','refresh');

	}

	function get_brand_list(){

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_brands = $this->web_material_brand_model->get_by_shop_nospin($sess_shop_id);
		$arr_list_cats = $this->curl_bl->CallApiNospi('GET','webcategory/build_cat/'.$sess_shop_id.'/material');

		//print_r($arr_brands);
		$data = $this->selectbox_bl->make_list_brand($arr_brands['Data']);

		//print_r($data);
		$arr_data = array(
			'list_cat' => $data
		);
		echo json_encode($arr_data);

	}

	function loaddata_more(){

		$add_alt = $this->session->flashdata('add_brand');
		$edit_alt = $this->session->flashdata('edit_brand');

		$sess_shop_id = $this->session->userdata('shop_id');
		$brand_search = $this->input->post('brand_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'brand_search' => $brand_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 1,
			'per_page' => 5

		);

		$arr_brands = $this->curl_bl->CallApi('POST','manufacture/brand/loaddata_more',$data_search);

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		//print_r($arr_sippliers);
		if($arr_brands['Status'] == "Success"){
			$max = sizeof($arr_brands['Data']);

			for($i=0;$i<$max;$i++){
				$arr_brands['Data'][$i]['web_material_brand_id'] = $this->encryption_util->encrypt_ssl($arr_brands['Data'][$i]['web_material_brand_id']);
			}
		}
		
		$data = array(
			'arr_brands' => $arr_brands['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Brand"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/manufacture/brand_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	if($from_pop == 1){
			$this->view_util->load_view_blankpage('manufacture/brand/brand_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('manufacture/brand/brand_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}


	}

	function loaddata_more_ajax(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$text_search = $this->input->post('text_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'text_search' => $text_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_datas = $this->curl_bl->CallApiNospi('POST','config_system/page_permission_group/loaddata_more',$data);

		if($arr_datas['Status'] == "Success"){
			$max = sizeof($arr_datas['Data']);

			for($i=0;$i<$max;$i++){
				$arr_datas['Data'][$i]['group_map_controller_id'] = $this->encryption_util->encrypt_ssl($arr_datas['Data'][$i]['group_map_controller_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_datas['Data']
		);
		echo json_encode($arr_data);


	}

}