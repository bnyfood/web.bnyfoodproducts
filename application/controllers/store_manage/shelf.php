<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Shelf extends CI_Controller
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

		$this->load->model('store_shelf_model');
		$this->load->model('store_sub_shelf_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function shelf_list()
	{
		$add_alt = $this->session->flashdata('add_shelf');
		$edit_alt = $this->session->flashdata('edit_shelf');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;


		$arr_shelfs = $this->store_shelf_model->get_by_shop($sess_shop_id,5);

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		//print_r($arr_sippliers);
		if($arr_shelfs['Status'] == "Success"){
			$max = sizeof($arr_shelfs['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shelfs['Data'][$i]['store_shelf_id'] = $this->encryption_util->encrypt_ssl($arr_shelfs['Data'][$i]['store_shelf_id']);
				if(!empty($arr_shelfs['Data'][$i]['arr_sub_shelfs'])){
					$max_sub = sizeof($arr_shelfs['Data'][$i]['arr_sub_shelfs']);
					for($j=0;$j<$max_sub;$j++){
						$arr_shelfs['Data'][$i]['arr_sub_shelfs'][$j]['store_sub_shelf_id'] = $this->encryption_util->encrypt_ssl($arr_shelfs['Data'][$i]['arr_sub_shelfs'][$j]['store_sub_shelf_id']);
					}
				}
			}
		}

		$data_search = array(
			'shelf_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);
		
		$data = array(
			'arr_shelfs' => $arr_shelfs['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "shelf"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/shelf_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

    	if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/shelf/shelf_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/shelf/shelf_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}
		

	} 

	function add_shelf_form(){

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		$arr_input = array(
			'title' => "shelf"
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
			$this->view_util->load_view_blankpage('store_manage/shelf/add_shelf_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/shelf/add_shelf_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}
		
	}

	function shelf_add(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$store_shelf_name = $this->input->post('store_shelf_name');
		$from_pop = $this->input->post('from_pop');

		$data_curl = array(
			'store_shelf_name' => $store_shelf_name,
			'ShopID' => $sess_shop_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/shelf/shelf_add',$data_curl);
		//print_r($data_curl);

		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_shelf','success');
		}else{
			$this->session->set_flashdata('add_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list/'.$from_pop,'refresh');
	}

	function shelf_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_shelf = $this->store_shelf_model->get_by_id($id_en);

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "shelf"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js"
    	);	


    	$data = array(
			'arr_shelf' => $arr_shelf['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('store_manage/shelf/edit_shelf_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
	}

	function shelf_edit(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->input->post('id_en');

		$store_shelf_name = $this->input->post('store_shelf_name');

		$data_curl = array(
			'store_shelf_name' => $store_shelf_name,
			'id_en' => $id_en

		);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/shelf/shelf_edit',$data_curl);
		//print_r($arr_res);

		$this->store_shelf_model->del_cache_by_id($id_en);
		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_shelf','success');
		}else{
			$this->session->set_flashdata('edit_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list','refresh');
	}


	function del_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','store_manage/shelf/del_action/'.$id_en);
		//print_r($arr_res);

		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_shelf','success');
		}else{
			$this->session->set_flashdata('add_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list','refresh');

	}

	function sub_shelf_add_form(){

		$shelf_id = $this->uri->segment(4);
		/*if($from_pop == ""){
			$from_pop = 0;
		}*/
		$from_pop = 0;

		$arr_input = array(
			'title' => "shelf"
		);

		$data = array(
			'shelf_id' => $shelf_id
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'validate_user' => base_url()."resources/js/validate/config_user.js",
        	'province' => base_url()."resources/js/provinces.js",
        	'config_user' => base_url()."resources/js/config_user.js"
    	);	
		
		if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/shelf/sub_shelf_add_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/shelf/sub_shelf_add_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}
		
	}

	function sub_shelf_add(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$store_sub_shelf_name = $this->input->post('store_sub_shelf_name');
		$store_shelf_id = $this->input->post('shelf_id');

		$data_curl = array(
			'store_sub_shelf_name' => $store_sub_shelf_name,
			'store_shelf_id' => $store_shelf_id,
			'ShopID' => $sess_shop_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/shelf/sub_shelf_add',$data_curl);
		//print_r($data_curl);

		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_shelf','success');
		}else{
			$this->session->set_flashdata('add_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list/'.$from_pop,'refresh');
	}

	function sub_shelf_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_shelf = $this->store_sub_shelf_model->get_by_id($id_en);

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "shelf"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js"
    	);	


    	$data = array(
			'arr_shelf' => $arr_shelf['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('store_manage/shelf/sub_shelf_edit_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
	}

	function sub_shelf_edit(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->input->post('id_en');

		$store_sub_shelf_name = $this->input->post('store_sub_shelf_name');

		$data_curl = array(
			'store_sub_shelf_name' => $store_sub_shelf_name,
			'id_en' => $id_en

		);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/sub_shelf/shelf_edit',$data_curl);
		//print_r($arr_res);

		$this->store_shelf_model->del_cache_by_id($id_en);
		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_shelf','success');
		}else{
			$this->session->set_flashdata('edit_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list','refresh');
	}

	function del_sub_action(){

		$sess_shop_id = $this->session->userdata('shop_id');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','store_manage/sub_shelf/del_action/'.$id_en);
		//print_r($arr_res);

		$this->store_shelf_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_shelf','success');
		}else{
			$this->session->set_flashdata('add_shelf','fail');
		}

		redirect(base_url().'store_manage/shelf/shelf_list','refresh');

	}

	function get_shelf_list(){

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_shelfs = $this->store_shelf_model->get_by_shop_nospin($sess_shop_id);
		$arr_list_cats = $this->curl_bl->CallApiNospi('GET','webcategory/build_cat/'.$sess_shop_id.'/material');

		//print_r($arr_shelfs);
		$data = $this->selectbox_bl->make_list_shelf($arr_shelfs['Data']);

		//print_r($data);
		$arr_data = array(
			'list_cat' => $data
		);
		echo json_encode($arr_data);

	}

	function loaddata_more(){

		$add_alt = $this->session->flashdata('add_shelf');
		$edit_alt = $this->session->flashdata('edit_shelf');

		$sess_shop_id = $this->session->userdata('shop_id');
		$shelf_search = $this->input->post('shelf_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'shelf_search' => $shelf_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 1,
			'per_page' => 5

		);

		$arr_shelfs = $this->curl_bl->CallApi('POST','store_manage/shelf/loaddata_more',$data_search);

		$from_pop = $this->uri->segment(4);
		if($from_pop == ""){
			$from_pop = 0;
		}

		//print_r($arr_sippliers);
		if($arr_shelfs['Status'] == "Success"){
			$max = sizeof($arr_shelfs['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shelfs['Data'][$i]['web_material_shelf_id'] = $this->encryption_util->encrypt_ssl($arr_shelfs['Data'][$i]['web_material_shelf_id']);
			}
		}
		
		$data = array(
			'arr_shelfs' => $arr_shelfs['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "shelf"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/shelf_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	if($from_pop == 1){
			$this->view_util->load_view_blankpage('store_manage/shelf/shelf_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}else{
			$this->view_util->load_view_main('store_manage/shelf/shelf_list',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
		}


	}

	function loaddata_more_ajax(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$shelf_search = $this->input->post('shelf_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'shelf_search' => $shelf_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_shelfs = $this->curl_bl->CallApiNospi('POST','store_manage/shelf/loaddata_more',$data);

		if($arr_shelfs['Status'] == "Success"){
			$max = sizeof($arr_shelfs['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shelfs['Data'][$i]['web_material_shelf_id'] = $this->encryption_util->encrypt_ssl($arr_shelfs['Data'][$i]['web_material_shelf_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_shelfs['Data']
		);
		echo json_encode($arr_data);


	}

}