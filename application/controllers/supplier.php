<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Supplier extends CI_Controller
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
		$this->load->model('web_supplier_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function supplier_list()
	{
		$add_alt = $this->session->flashdata('add_supplier');
		$edit_alt = $this->session->flashdata('edit_supplier');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		//echo $sess_shop_id;
		$from_pop = $this->uri->segment(3);
		if($from_pop == ""){
			$from_pop = 0;
		}

		$data_search = array(
			'sipplier_search' => '',
			'shopid_en' => $sess_shop_id,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);

		$arr_sippliers = $this->web_supplier_model->get_by_shop($sess_shop_id,5);
		//print_r($arr_sippliers);
		if($arr_sippliers['Status'] == "Success"){
			$max = sizeof($arr_sippliers['Data']);

			for($i=0;$i<$max;$i++){
				$arr_sippliers['Data'][$i]['web_supplier_id'] = $this->encryption_util->encrypt_ssl($arr_sippliers['Data'][$i]['web_supplier_id']);
			}
		}
		
		$data = array(
			'arr_sippliers' => $arr_sippliers['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Supplier"
		);
		
		$arr_js = array(
        	'morecontent' => base_url()."resources/js/morecontent/supplier.js",
        	'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		if($from_pop == 1){
			$this->view_util->load_view_blankpage('supplier/supplier_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}else{
			$this->view_util->load_view_main('supplier/supplier_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}

	} 

	public function supplier_list_search()
	{
		$add_alt = $this->session->flashdata('add_supplier');
		$edit_alt = $this->session->flashdata('edit_supplier');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$sipplier_search = $this->input->post('sipplier_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'sipplier_search' => $sipplier_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5

		);

		$from_pop = $this->uri->segment(3);
		if($from_pop == ""){
			$from_pop = 0;
		}


		$arr_sippliers = $this->curl_bl->CallApi('POST','supplier/get_by_shop_search',$data_search);

		//print_r($arr_sippliers);
		if($arr_sippliers['Status'] == "Success"){
			$max = sizeof($arr_sippliers['Data']);

			for($i=0;$i<$max;$i++){
				$arr_sippliers['Data'][$i]['web_supplier_id'] = $this->encryption_util->encrypt_ssl($arr_sippliers['Data'][$i]['web_supplier_id']);
			}
		}
		
		$data = array(
			'arr_sippliers' => $arr_sippliers['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Supplier"
		);
		
		$arr_js = array(
        	'morecontent' => base_url()."resources/js/morecontent/supplier.js",
        	'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		if($from_pop == 1){
			$this->view_util->load_view_blankpage('supplier/supplier_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}else{
			$this->view_util->load_view_main('supplier/supplier_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}

	} 

	function supplier_list_search_ajax(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$sipplier_search = $this->input->post('sipplier_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'sipplier_search' => $sipplier_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_sippliers = $this->curl_bl->CallApiNospi('POST','supplier/get_by_shop_search',$data);

		if($arr_sippliers['Status'] == "Success"){
			$max = sizeof($arr_sippliers['Data']);

			for($i=0;$i<$max;$i++){
				$arr_sippliers['Data'][$i]['web_supplier_id'] = $this->encryption_util->encrypt_ssl($arr_sippliers['Data'][$i]['web_supplier_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_sippliers['Data']
		);
		echo json_encode($arr_data);

	}

	function add_supplier_form(){

		$from_pop = $this->uri->segment(3);
		if($from_pop == ""){
			$from_pop = 0;
		}
		
		$arr_input = array(
			'title' => "Supplier"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'validate_user' => base_url()."resources/js/validate/config_user.js",
        	'province' => base_url()."resources/js/provinces.js",
        	'config_user' => base_url()."resources/js/config_user.js",
        	'supplier_add' => base_url()."resources/js/supplier/supplier_add.js"
    	);	
		
		$arr_provinces = $this->curl_bl->CallApi('GET','Provinces/get_provinces_all');

		$data = array(
			'arr_provinces' => $arr_provinces['Data'],
			'from_pop' => $from_pop
		);

		if($from_pop == 1){
			$this->view_util->load_view_blankpage('supplier/add_supplier_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}else{
			$this->view_util->load_view_main('supplier/add_supplier_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
		}
		
	}

	function supplier_add(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$supplier_name = $this->input->post('supplier_name');
		$supplier_tax = $this->input->post('supplier_tax');
		$supplier_vat = $this->input->post('supplier_vat');
		$supplier_person = $this->input->post('supplier_person');
		$phoneno1 = $this->input->post('phoneno1');
		$phoneno2 = $this->input->post('phoneno2');
		$supplier_line = $this->input->post('supplier_line');
		$supplier_email = $this->input->post('supplier_email');

		$supplier_address = $this->input->post('supplier_address');
		$supplier_province = $this->input->post('province_sel');
		$supplier_district = $this->input->post('district_sel');
		$supplier_subdistrict = $this->input->post('subdistrict_sel');
		$supplier_zip = $this->input->post('Zip');
		$supplier_discription = $this->input->post('supplier_discription');
		$from_pop = $this->input->post('from_pop');

		$supplier_headoffice = $this->input->post('supplier_headoffice');
		//if($supplier_headoffice != "1"){
		//}
		$supplier_branchid = $this->input->post('supplier_branchid');

		$data_curl = array(
			'supplier_name' => $supplier_name,
			'supplier_tax' => $supplier_tax,
			'supplier_vat' => $supplier_vat,
			'supplier_person' => $supplier_person,
			'supplier_headoffice' => $supplier_headoffice,
			'supplier_branchid' => $supplier_branchid,
			'phoneno1' => $phoneno1,
			'phoneno2' => $phoneno2,
			'supplier_line' => $supplier_line,
			'supplier_email' => $supplier_email,
			'supplier_address' => $supplier_address,
			'supplier_province' => $supplier_province,
			'supplier_district' => $supplier_district,
			'supplier_subdistrict' => $supplier_subdistrict,
			'supplier_zip' => $supplier_zip,
			'supplier_discription' => $supplier_discription,
			'ShopID' => $sess_shop_id
			
		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','supplier/supplier_add',$data_curl);
		//print_r($data_curl);

		$this->web_supplier_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_supplier','success');
		}else{
			$this->session->set_flashdata('add_supplier','fail');
		}

		redirect(base_url().'supplier/supplier_list/'.$from_pop,'refresh');
	}

	function supplier_edit_form(){

		$id_en = $this->uri->segment(3);

		$arr_sipplier = $this->web_supplier_model->get_by_id($id_en);

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config User"
		);

    	$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_user_edit.js",
        	'province' => base_url()."resources/js/provinces.js",
        	'config_user' => base_url()."resources/js/config_user.js",
        	'supplier_add' => base_url()."resources/js/supplier/supplier_add.js"
    	);	

    	$arr_provinces = $this->curl_bl->CallApi('GET','Provinces/get_provinces_all');
    	$arr_districts = $this->curl_bl->CallApi('GET','Provinces/get_districts_all');
    	$arr_subdistricts = $this->curl_bl->CallApi('GET','Provinces/get_subdistricts_all');

    	$data = array(
			'arr_sipplier' => $arr_sipplier['Data'],
			'arr_provinces' => $arr_provinces['Data'],
			'arr_districts' => $arr_districts['Data'],
			'arr_subdistricts' => $arr_subdistricts['Data'],
			'id_en' => $id_en
		);
		
		
		$this->view_util->load_view_main('supplier/edit_supplier_form',$data,NULL,$arr_js,$arr_input,MENU_MANUFACTURE_SUPPLIER);
	}

	function supplier_edit(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$id_en = $this->input->post('id_en');

		$supplier_name = $this->input->post('supplier_name');
		$supplier_tax = $this->input->post('supplier_tax');
		$supplier_vat = $this->input->post('supplier_vat');
		$supplier_person = $this->input->post('supplier_person');
		$phoneno1 = $this->input->post('phoneno1');
		$phoneno2 = $this->input->post('phoneno2');
		$supplier_line = $this->input->post('supplier_line');
		$supplier_email = $this->input->post('supplier_email');

		$supplier_address = $this->input->post('supplier_address');
		$supplier_province = $this->input->post('province_sel');
		$supplier_district = $this->input->post('district_sel');
		$supplier_subdistrict = $this->input->post('subdistrict_sel');
		$supplier_zip = $this->input->post('Zip');
		$supplier_discription = $this->input->post('supplier_discription');

		$supplier_headoffice = $this->input->post('supplier_headoffice');

		$supplier_branchid = "";
		if($supplier_headoffice == 0){
			$supplier_branchid = $this->input->post('supplier_branchid');
		}
		


		$data_curl = array(
			'id_en' => $id_en,
			'supplier_name' => $supplier_name,
			'supplier_tax' => $supplier_tax,
			'supplier_vat' => $supplier_vat,
			'supplier_person' => $supplier_person,
			'supplier_headoffice' => $supplier_headoffice,
			'supplier_branchid' => $supplier_branchid,
			'phoneno1' => $phoneno1,
			'phoneno2' => $phoneno2,
			'supplier_line' => $supplier_line,
			'supplier_email' => $supplier_email,
			'supplier_address' => $supplier_address,
			'supplier_province' => $supplier_province,
			'supplier_district' => $supplier_district,
			'supplier_subdistrict' => $supplier_subdistrict,
			'supplier_zip' => $supplier_zip,
			'supplier_discription' => $supplier_discription
		);

		$arr_res = $this->curl_bl->CallApi('POST','supplier/supplier_edit',$data_curl);
		//print_r($arr_res);

		$this->web_supplier_model->del_cache_by_id($id_en);
		$this->web_supplier_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('edit_supplier','success');
		}else{
			$this->session->set_flashdata('edit_supplier','fail');
		}

		redirect(base_url().'supplier/supplier_list','refresh');
	}


	function del_action(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$id_en = $this->uri->segment(3);

		$arr_res = $this->curl_bl->CallApi('GET','supplier/del_action/'.$id_en);
		//print_r($arr_res);

		$this->web_supplier_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'supplier/supplier_list','refresh');

	}

	function get_supplier_list(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		//echo $sess_shop_id;
		$arr_sippliers = $this->web_supplier_model->get_by_shop($sess_shop_id);

		//print_r($arr_user);
		$data = $this->selectbox_bl->make_list_supplier($arr_sippliers['Data']);
		$arr_data = array(
			'list_cat' => $data
		);
		echo json_encode($arr_data);

	}


}