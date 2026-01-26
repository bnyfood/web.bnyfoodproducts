<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->library('businesslogic/unit_bl');
		$this->load->library('util/encryption_util');
		$this->load->library('util/random_util');

		$this->load->model('web_material_model');
		$this->load->model('web_material_brand_model');
		$this->load->model('web_supplier_model');
		$this->load->model('web_material_unit_model');
		$this->load->model('web_material_volume_model');
		$this->load->model('web_material_unit_type_model');
		$this->load->model('web_material_subunit_type_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function material_list()
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

		$arr_materials = $this->web_material_model->get_by_shop($sess_shop_id,5);
		//print_r($arr_sippliers);
		if($arr_materials['Status'] == "Success"){
			$max = sizeof($arr_materials['Data']);

			for($i=0;$i<$max;$i++){
				$arr_materials['Data'][$i]['web_material_id'] = $this->encryption_util->encrypt_ssl($arr_materials['Data'][$i]['web_material_id']);
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
        	'morecontent' => base_url()."resources/js/morecontent/manufacture/material_list.js",
        	'material_list' => base_url()."resources/js/manufacture/material/material_list.js",
        	'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$this->view_util->load_view_main('manufacture/material/material_list',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);

	} 

	public function material_list_search()
	{
		$add_alt = $this->session->flashdata('add_material');
		$edit_alt = $this->session->flashdata('edit_material');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		//echo $sess_shop_id;
		$material_search = $this->input->post('material_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'material_search' => $material_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 1,
			'per_page' => 5

		);

		$arr_materials = $this->curl_bl->CallApi('POST','manufacture/material/material_search',$data_search);

		//print_r($arr_sippliers);
		if($arr_materials['Status'] == "Success"){
			$max = sizeof($arr_materials['Data']);

			for($i=0;$i<$max;$i++){
				$arr_materials['Data'][$i]['web_material_id'] = $this->encryption_util->encrypt_ssl($arr_materials['Data'][$i]['web_material_id']);
			}
		}
		
		$data = array(
			'arr_materials' => $arr_materials['Data'],
			'data_search' => $data_search,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Material"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/manufacture/material_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$this->view_util->load_view_main('manufacture/material/material_list',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);

	} 

	function loaddata_more_ajax(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$material_search = $this->input->post('material_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');


		$data = array(
			'material_search' => $material_search,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_materials = $this->curl_bl->CallApiNospi('POST','manufacture/material/material_search',$data);

		if($arr_materials['Status'] == "Success"){
			$max = sizeof($arr_materials['Data']);

			for($i=0;$i<$max;$i++){
				$arr_materials['Data'][$i]['web_material_id'] = $this->encryption_util->encrypt_ssl($arr_materials['Data'][$i]['web_material_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_materials['Data']
		);
		echo json_encode($arr_data);

	}

	function add_material_form(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$ran_num_sup = $this->random_util->create_random_number(6);	

		$arr_list_cats = $this->curl_bl->CallApi('GET','webcategory/build_cat/'.$sess_shop_id.'/material');

		$arr_brands = $this->web_material_brand_model->get_by_shop_nospin($sess_shop_id);

		$arr_suppliers = $this->web_supplier_model->get_by_shop_nospin($sess_shop_id);

		$arr_units = $this->web_material_unit_model->get_all_nospin();

		$arr_unit_types = $this->web_material_unit_type_model->get_all_nospin();

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
			'manage_price' => base_url().'resources/js/manufacture/material/manage_price.js',
			'manage_supplier' => base_url().'resources/js/manufacture/material/add_material_form.js',
    	);	
		
		$data = array(
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_brands' => $arr_brands['Data'],
			'arr_suppliers' => $arr_suppliers['Data'],
			'arr_units' => $arr_units['Data'],
			'arr_unit_types' => $arr_unit_types['Data'],
			'ran_num_sup' => $ran_num_sup
		);
		
		$this->view_util->load_view_main('manufacture/material/add_material_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function material_add(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$web_supplier_id = $this->input->post('web_supplier_id');

		//print_r($web_supplier_id);

		$arr_curl = json_encode($web_supplier_id);

		$price_set = "";
		$tab = "";

		$sub_unit = $this->input->post('sub_unit');
		$material_name = $this->input->post('material_name');
		$material_sku = $this->input->post('material_sku');
		$web_category_id = $this->input->post('web_category_id');
		$web_material_brand_id = $this->input->post('web_material_brand_id');
		$material_size = $this->input->post('material_size');
		$material_unit = $this->input->post('material_unit');
		$material_unit_price = $this->input->post('material_unit_price');
		$description = $this->input->post('description');
		$ran_num_sup = $this->input->post('ran_num_sup');
		$material_density = $this->input->post('material_density');
		$web_material_subunit_type_id = $this->input->post('web_material_subunit_type_id');
		$main_web_material_subunit_type_id = $this->input->post('main_web_material_subunit_type_id');
		//$web_material_unit_type_history_id = $this->input->post('web_material_unit_type_history_id');
		$subunit_qty = $this->input->post('subunit_qty');
		$web_material_subunit_id = $this->input->post('web_material_subunit_id');

		$supprice = $this->input->post('supprice');

		$arr_1 = explode("|",$supprice);
		$cnt_arr1 = count($arr_1);
		if($cnt_arr1 > 0){
			for ($x = 0; $x <= $cnt_arr1-1; $x++) {

				$arr_2 = explode("_",$arr_1[$x]);
				$name_price = "supprice_".$arr_2[1];
				$price_val = $this->input->post($name_price);
				if($x > 0){
					$tab="|";
				}
				$price_set .= $tab.$arr_2[1]."_".$price_val;
			}
		}

		$data_curl = array(
			'sub_unit' => $sub_unit,
			'arr_curl' => $arr_curl,
			'material_name' => $material_name,
			'material_sku' => $material_sku,
			'web_category_id' => $web_category_id,
			'web_material_brand_id' => $web_material_brand_id,
			'web_material_unit_id' => $material_unit,
			//'web_material_unit_type_history_id' => $web_material_unit_type_history_id,
			'web_material_subunit_type_id' => $web_material_subunit_type_id,
			'main_web_material_subunit_type_id' => $main_web_material_subunit_type_id,
			'web_material_subunit_id' => $web_material_subunit_id,
			'material_size' => $material_size,
			'material_unit_price' => $material_unit_price,
			'description' => $description,
			'ShopID' => $sess_shop_id,
			'ran_num_sup' => $ran_num_sup,
			'price_set' => $price_set,
			'material_density' => $material_density,
			'subunit_qty' => $subunit_qty
		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material/material_add',$data_curl);
		//print_r($data_curl);

		$this->web_material_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_material','success');
		}else{
			$this->session->set_flashdata('add_material','fail');
		}

		redirect(base_url().'manufacture/material/material_list','refresh');
	}

	function edit_material_form(){

		$map_id = $this->uri->segment(4);

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$ran_num_sup = $this->random_util->create_random_number(6);	

		$arr_list_cats = $this->curl_bl->CallApi('GET','webcategory/build_cat/'.$sess_shop_id.'/material');

		$arr_brands = $this->web_material_brand_model->get_by_shop_nospin($sess_shop_id);

		$arr_suppliers = $this->web_supplier_model->get_by_shop_nospin($sess_shop_id);

		$arr_units = $this->web_material_unit_model->get_all_nospin();

		$arr_material = $this->curl_bl->CallApi('GET','manufacture/material/get_edit_material_data/'.$map_id);

		//print_r($arr_material);

		/*$data_supplier = "";
		if(!empty($arr_material['Data']['arr_mat_map'])){
			$data_supplier = $this->data_bl->create_arr_id($arr_material['Data']['arr_mat_map'],'web_supplier_id');
		}*/

		//print_r($data_supplier);

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
			//'manage_supplier' => base_url().'resources/js/manufacture/material/manage_supplier.js',
    	);	
		
		$data = array(
			'arr_material' => $arr_material['Data']['data_material'],
			'arr_supplier_map' => $arr_material['Data']['arr_mat_map'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_brands' => $arr_brands['Data'],
			'arr_suppliers' => $arr_suppliers['Data'],
			'arr_units' => $arr_units['Data'],
			'ran_num_sup' => $ran_num_sup,
			'map_id' => $map_id
		);
		
		$this->view_util->load_view_main('manufacture/material/edit_material_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function material_edit(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$web_supplier_id = $this->input->post('web_supplier_id');

		//print_r($web_supplier_id);
		$arr_curl = json_encode($web_supplier_id);

		$map_id = $this->input->post('map_id');
		$web_material_id = $this->input->post('web_material_id');
		$web_supplier_id = $this->input->post('web_supplier_id');
		$material_name = $this->input->post('material_name');
		$web_category_id = $this->input->post('web_category_id');
		$web_material_brand_id = $this->input->post('web_material_brand_id');
		$material_size = $this->input->post('material_size');
		$web_material_unit_id = $this->input->post('web_material_unit_id');
		$material_price = $this->input->post('material_price');
		$description = $this->input->post('description');
		$ran_num_sup = $this->input->post('ran_num_sup');
		$material_density = $this->input->post('material_density');

		$data_curl = array(
			'arr_curl' => $arr_curl,
			'web_material_id' => $web_material_id,
			'web_supplier_id' => $web_supplier_id,
			'material_name' => $material_name,
			'web_category_id' => $web_category_id,
			'web_material_brand_id' => $web_material_brand_id,
			'material_size' => $material_size,
			'web_material_unit_id' => $web_material_unit_id,
			'material_price' => $material_price,
			'description' => $description,
			'ShopID' => $sess_shop_id,
			'material_density' => $material_density
		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','manufacture/material/material_edit',$data_curl);
		//print_r($data_curl);

		$this->web_material_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_material','success');
		}else{
			$this->session->set_flashdata('add_material','fail');
		}

		redirect(base_url().'manufacture/material/material_list','refresh');
	}


	function del_action(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','manufacture/material/del_action/'.$id_en);
		//print_r($arr_res);

		$this->web_material_brand_model->del_cache_by_shop($sess_shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_brand','success');
		}else{
			$this->session->set_flashdata('add_brand','fail');
		}

		$this->web_material_model->del_cache_by_shop($sess_shop_id);

		redirect(base_url().'manufacture/material/material_list','refresh');

	}

	function add_supplier(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$ran_num_sup = $this->input->post('ran_num_sup');
		$web_supplier_id = $this->input->post('web_supplier_id');

		$data_curl = array(
			'ran_num_sup' => $ran_num_sup,
			'web_supplier_id' => $web_supplier_id,
			'ShopID' => $sess_shop_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','manufacture/material/add_supplier',$data_curl);

		echo 'true';
	}

	function get_supplier_ajax(){

		$ran_num_sup = $this->input->post('ran_num_sup');
		$arr_suppliers = $this->curl_bl->CallApiNospi('GET','manufacture/material/get_supplier_by_code/'.$ran_num_sup);

		$arr_data = array(
			'arr_suppliers' => $arr_suppliers['Data']
		);
		echo json_encode($arr_data);
	}

	function move_supplier_ajax(){

		$map_id = $this->input->post('map_id');

		$data_curl = array(
			'map_id' => $map_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','manufacture/material/move_supplier_map',$data_curl);

		echo 'true';
	}

	function material_update_price(){

		$material_map_supplier_id = $this->input->post('material_map_supplier_id');
		$unit_price_val = $this->input->post('unit_price_val');

		$data_curl = array(
			'material_map_supplier_id' => $material_map_supplier_id,
			'unit_price_val' => $unit_price_val
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','manufacture/material/material_update_price',$data_curl);

		echo 'true';

	}

	function sub_unit_search(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$sub_unit_txt_search = $this->input->post('sub_unit_txt_search');

		$data_curl = array(
			'sub_unit_txt_search' => $sub_unit_txt_search,
			'ShopID' => $sess_shop_id
		);

		$arr_data_sub_units = $this->curl_bl->CallApiNospi('POST','manufacture/material/sub_unit_search',$data_curl);

		$arr_data_units = $this->unit_bl->get_unit_with_subunit($arr_data_sub_units['Data']);


		$arr_data = array(
			'arr_sub_units' => $arr_data_sub_units['Data']
			//'arr_compares' => $arr_data_res['Data']['data_compares']
		);

		//print_r($arr_data);
		echo json_encode($arr_data);
	}

}