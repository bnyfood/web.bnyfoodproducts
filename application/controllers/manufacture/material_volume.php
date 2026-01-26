<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material_volume extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->library('util/encryption_util');
		$this->load->library('util/random_util');

		$this->load->model('web_material_model');
		$this->load->model('web_material_brand_model');
		$this->load->model('web_supplier_model');
		$this->load->model('web_material_unit_model');
		$this->load->model('web_material_volume_model');
		$this->load->model('web_material_volume_type_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function material_volume_list()
	{
		$add_alt = $this->session->flashdata('add_material');
		$edit_alt = $this->session->flashdata('edit_material');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		//echo $sess_shop_id;

		$data_search = array(
			'material_search' => '',
			'shopid_en' => $sess_shop_id,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5

		);

		$arr_materials = $this->web_material_volume_model->get_by_shop($sess_shop_id,5);
		//print_r($arr_sippliers);
		if($arr_materials['Status'] == "Success"){
			$max = sizeof($arr_materials['Data']);

			for($i=0;$i<$max;$i++){
				$arr_materials['Data'][$i]['web_material_volume_id'] = $this->encryption_util->encrypt_ssl($arr_materials['Data'][$i]['web_material_volume_id']);
			}
		}
		
		$data = array(
			'arr_materials' => $arr_materials['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Material"
		);
		
		$arr_js = array(
        	'morecontent' => base_url()."resources/js/morecontent/manufacture/material_volume_list.js",
        	'material_list' => base_url()."resources/js/manufacture/material_volume/material_list.js",
        	'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$this->view_util->load_view_main('manufacture/material_volume/material_volume_list',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);

	} 

	function add_material_volume_form(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$ran_num_sup = $this->random_util->create_random_number(6);	

		$arr_material_volume_types = $this->web_material_volume_type_model->get_all_nospin($sess_shop_id);

		$arr_material_units = $this->web_material_unit_model->get_all_nospin();

		$arr_input = array(
			'title' => "Material"
		);

		$arr_css = array(
			'multi-select_css' => base_url().'global/vendor/multi-select/multi-select.css',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css'
		);
		
		$arr_js = array(
			
			'jquery1.8.3' => base_url().'global/vendor/jquery/jquery-1.8.3.js',
			'multi-select_js' => base_url().'global/vendor/multi-select/jquery.multi-select.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			'fancy_webcat' => base_url().'resources/js/fancybox/fancy_webcat.js',
			'fancy_brand' => base_url().'resources/js/fancybox/fancy_brand.js',
			'fancy_supplier' => base_url().'resources/js/fancybox/fancy_supplier.js',
			'fancy_supplier' => base_url().'resources/js/manufacture/material_volume/material_volume_add_form.js',
			//'manage_price' => base_url().'resources/js/manufacture/material/manage_price.js',
			//'manage_supplier' => base_url().'resources/js/manufacture/material/manage_supplier.js',
    	);	
		
		$data = array(
			'arr_material_volume_types' => $arr_material_volume_types['Data'],

			'ran_num_sup' => $ran_num_sup
		);
		
		$this->view_util->load_view_main('manufacture/material_volume/add_material_volume_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function material_volume_add(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$price_set = "";
		$tab = "";

		$web_material_id = $this->input->post('web_material_id');
		$web_material_volume = $this->input->post('web_material_volume');

		$vt_type = $this->input->post('vt_type');
		$web_material_volume_type_id = $this->input->post('web_material_volume_type_id');
		$web_material_volume_type = $this->input->post('web_material_volume_type');

		$unit_type = $this->input->post('unit_type');
		$web_material_unit_id = $this->input->post('web_material_unit_id');
		$material_unit = $this->input->post('material_unit');
	
		$data_curl = array(
			'web_material_id' => $web_material_id,
			'web_material_volume' => $web_material_volume,
			'vt_type' => $vt_type,
			'web_material_volume_type_id' => $web_material_volume_type_id,
			'web_material_volume_type' => $web_material_volume_type,
			'unit_type' => $unit_type,
			'web_material_unit_id' => $web_material_unit_id,
			'material_unit' => $material_unit,
			'ShopID' => $sess_shop_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material_volume/material_volume_add',$data_curl);
		//print_r($data_curl);

		$this->web_material_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_material','success');
		}else{
			$this->session->set_flashdata('add_material','fail');
		}

		redirect(base_url().'manufacture/material_volume/material_volume_list','refresh');
	}

	function edit_material_volume_form(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$material_volume_id_en  = $this->uri->segment(4);

		//echo ">>".$material_volume_id_en."<<";

		//$material_volume_id = $this->encryption_util->decrypt_ssl($material_volume_id_en);

		$arr_volume_type = $this->curl_bl->CallApi('GET','manufacture/material_volume/get_by_web_material_id_lasted/'.$material_volume_id_en);

		$arr_material = $this->curl_bl->CallApi('GET','manufacture/material/get_by_id_lasted/'.$arr_volume_type['Data']['web_material_id']);

		$arr_material_volume_type = $this->curl_bl->CallApi('GET','manufacture/material_volume_type/get_by_id/'.$arr_volume_type['Data']['web_material_volume_type_id']);

		$arr_material_unit = $this->curl_bl->CallApi('GET','manufacture/material_unit/get_by_id/'.$arr_volume_type['Data']['web_material_unit_id']);


		$arr_input = array(
			'title' => "Material"
		);

		$arr_css = array(
			'multi-select_css' => base_url().'global/vendor/multi-select/multi-select.css',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css'
		);
		
		$arr_js = array(
			
			'jquery1.8.3' => base_url().'global/vendor/jquery/jquery-1.8.3.js',
			'multi-select_js' => base_url().'global/vendor/multi-select/jquery.multi-select.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			'fancy_webcat' => base_url().'resources/js/fancybox/fancy_webcat.js',
			'fancy_brand' => base_url().'resources/js/fancybox/fancy_brand.js',
			'fancy_supplier' => base_url().'resources/js/fancybox/fancy_supplier.js',
			'fancy_supplier' => base_url().'resources/js/manufacture/material_volume/material_volume_add_form.js',
			//'manage_price' => base_url().'resources/js/manufacture/material/manage_price.js',
			//'manage_supplier' => base_url().'resources/js/manufacture/material/manage_supplier.js',
    	);	
		
		$data = array(
			'material_volume_id_en' => $material_volume_id_en,
			'arr_material' => $arr_material['Data'],
			'arr_volume_type' => $arr_volume_type['Data'],
			'arr_material_volume_type' => $arr_material_volume_type['Data'],
			'arr_material_unit' => $arr_material_unit['Data']

		);

		//print_r($data);
		
		$this->view_util->load_view_main('manufacture/material_volume/edit_material_volume_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function material_volume_edit(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$price_set = "";
		$tab = "";

		$material_volume_id_en = $this->input->post('material_volume_id_en');

		$web_material_id = $this->input->post('web_material_id');
		$web_material_volume = $this->input->post('web_material_volume');

		$vt_type = $this->input->post('vt_type');
		$web_material_volume_type_id = $this->input->post('web_material_volume_type_id');
		$web_material_volume_type = $this->input->post('web_material_volume_type');

		$unit_type = $this->input->post('unit_type');
		$web_material_unit_id = $this->input->post('web_material_unit_id');
		$material_unit = $this->input->post('material_unit');
	
		$data_curl = array(
			'material_volume_id_en' => $material_volume_id_en,
			'web_material_id' => $web_material_id,
			'web_material_volume' => $web_material_volume,
			'vt_type' => $vt_type,
			'web_material_volume_type_id' => $web_material_volume_type_id,
			'web_material_volume_type' => $web_material_volume_type,
			'unit_type' => $unit_type,
			'web_material_unit_id' => $web_material_unit_id,
			'material_unit' => $material_unit,
			'ShopID' => $sess_shop_id

		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material_volume/material_volume_edit',$data_curl);
		//print_r($data_curl);

		$this->web_material_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_material','success');
		}else{
			$this->session->set_flashdata('add_material','fail');
		}

		redirect(base_url().'manufacture/material_volume/material_volume_list','refresh');
	}

	function material_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$material_search = $this->input->post('material_search');

		$data_curl = array(
			'material_search' => $material_search,
			'shopid_en' => $shop_id_en
		);

		//print_r($data_curl);

		$arr_materials = $this->curl_bl->CallApiNospi('POST','manufacture/material/material_search_v2',$data_curl);


		$arr_data = array(
			'arr_materials' => $arr_materials['Data']
		);
		echo json_encode($arr_data);
	}

	function material_volume_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$material_volume_search = $this->input->post('material_volume_search');

		$data_curl = array(
			'material_volume_search' => $material_volume_search,
			'shop_id_en' => $shop_id_en
		);

		//print_r($data_curl);

		$arr_material_volumes = $this->curl_bl->CallApiNospi('POST','manufacture/material_volume_type/material_volume_search',$data_curl);


		$arr_data = array(
			'arr_material_volumes' => $arr_material_volumes['Data']
		);
		echo json_encode($arr_data);
	}

	function material_unit_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$material_unit_search = $this->input->post('material_unit_search');

		$data_curl = array(
			'material_unit_search' => $material_unit_search
		);

		//print_r($data_curl);

		$arr_material_units = $this->curl_bl->CallApiNospi('POST','manufacture/material_unit/material_unit_search',$data_curl);


		$arr_data = array(
			'arr_material_units' => $arr_material_units['Data']
		);
		echo json_encode($arr_data);
	}

}