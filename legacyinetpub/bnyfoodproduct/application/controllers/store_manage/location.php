<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Location extends CI_Controller
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

		$this->load->model('store_location_model');

		//$this->auth_bl->check_session_exists();

     }
     
	public function location_list()
	{
		$add_alt = $this->session->flashdata('add_location');
		$edit_alt = $this->session->flashdata('edit_location');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;


		$arr_locations = $this->store_location_model->get_by_shop($sess_shop_id,5);

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		//print_r($arr_sippliers);
		if($arr_locations['Status'] == "Success"){
			$max = sizeof($arr_locations['Data']);

			for($i=0;$i<$max;$i++){
				$arr_locations['Data'][$i]['store_location_id'] = $this->encryption_util->encrypt_ssl($arr_locations['Data'][$i]['store_location_id']);
			}
		}

		$data_search = array(
			'location_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);
		
		$data = array(
			'arr_locations' => $arr_locations['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

    	if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/location/location_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/location/location_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}
		

	} 

	function add_location_form(){

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		$arr_input = array(
			'title' => "location"
		);

		$data = array(
			'from_pop' => $from_pop
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'validate_user' => base_url()."resources/js/validate/config_user.js",
        	'province' => base_url()."resources/js/provinces.js",
        	'config_user' => base_url()."resources/js/config_user.js"
    	);	
		
		if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/location/add_location_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/location/add_location_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}
		
		
	}

	function location_add(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$store_location_name = $this->input->post('store_location_name');
		$from_pop = $this->input->post('from_pop');

		$data_curl = array(
			'store_location_name' => $store_location_name,
			'ShopID' => $sess_shop_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/location/location_add',$data_curl);
		//print_r($data_curl);

		$this->store_location_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_location','success');
		}else{
			$this->session->set_flashdata('add_location','fail');
		}

		redirect(base_url().'store_manage/location/location_list/'.$from_pop,'refresh');
	}

	function location_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_location = $this->store_location_model->get_by_id($id_en);

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "location"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js"
    	);	


    	$data = array(
			'arr_location' => $arr_location['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('store_manage/location/edit_location_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
	}

	function location_edit(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->input->post('id_en');

		$store_location_name = $this->input->post('store_location_name');

		$data_curl = array(
			'store_location_name' => $store_location_name,
			'id_en' => $id_en

		);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/location/location_edit',$data_curl);
		//print_r($arr_res);

		$this->store_location_model->del_cache_by_id($id_en);
		$this->store_location_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_location','success');
		}else{
			$this->session->set_flashdata('edit_location','fail');
		}

		redirect(base_url().'store_manage/location/location_list','refresh');
	}


	function del_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','store_manage/location/del_action/'.$id_en);
		//print_r($arr_res);

		$this->store_location_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_location','success');
		}else{
			$this->session->set_flashdata('add_location','fail');
		}

		redirect(base_url().'store_manage/location/location_list','refresh');

	}

	function get_location_list(){

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_locations = $this->store_location_model->get_by_shop_nospin($sess_shop_id);
		$arr_list_cats = $this->curl_bl->CallApiNospi('GET','webcategory/build_cat/'.$sess_shop_id.'/material');

		//print_r($arr_locations);
		$data = $this->selectbox_bl->make_list_location($arr_locations['Data']);

		//print_r($data);
		$arr_data = array(
			'list_cat' => $data
		);
		echo json_encode($arr_data);

	}

	function loaddata_more(){

		$add_alt = $this->session->flashdata('add_location');
		$edit_alt = $this->session->flashdata('edit_location');

		$sess_shop_id = $this->session->userdata('shop_id');
		$location_search = $this->input->post('location_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'location_search' => $location_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 1,
			'per_page' => 5

		);

		$arr_locations = $this->curl_bl->CallApi('POST','store_manage/location/loaddata_more',$data_search);

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		//print_r($arr_sippliers);
		if($arr_locations['Status'] == "Success"){
			$max = sizeof($arr_locations['Data']);

			for($i=0;$i<$max;$i++){
				$arr_locations['Data'][$i]['web_material_location_id'] = $this->encryption_util->encrypt_ssl($arr_locations['Data'][$i]['web_material_location_id']);
			}
		}
		
		$data = array(
			'arr_locations' => $arr_locations['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/location/location_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/location/location_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}


	}

	function loaddata_more_ajax(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$location_search = $this->input->post('location_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'location_search' => $location_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_locations = $this->curl_bl->CallApiNospi('POST','store_manage/location/loaddata_more',$data);

		if($arr_locations['Status'] == "Success"){
			$max = sizeof($arr_locations['Data']);

			for($i=0;$i<$max;$i++){
				$arr_locations['Data'][$i]['web_material_location_id'] = $this->encryption_util->encrypt_ssl($arr_locations['Data'][$i]['web_material_location_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_locations['Data']
		);
		echo json_encode($arr_data);


	}

}