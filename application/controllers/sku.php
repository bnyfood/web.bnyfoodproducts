<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Sku extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('sku_model');
		$this->load->model('product_model');

		//$this->auth_bl->check_session_exists();

		$this->_customer_code = $this->session->userdata('customer_code');
		$this->_shop_id = $this->session->userdata('shop_id');

     }
     
	public function sku_list()
	{
		$add_alt = $this->session->flashdata('add_user');
		$edit_alt = $this->session->flashdata('edit_user');
		//echo $add_alt;

		$ran_id = $this->uri->segment(3);
		$shop_id = $this->session->userdata('shop_id');
		$arr_skus = $this->sku_model->get_sku_by_shop_id($shop_id);
		//print_r($arr_users);
		if($arr_skus['Status'] == "Success"){
			$max = sizeof($arr_skus['Data']);

			for($i=0;$i<$max;$i++){
				$arr_skus['Data'][$i]['web_sku_id'] = $this->encryption_util->encrypt_ssl($arr_skus['Data'][$i]['web_sku_id']);
			}
		}
		
		$data = array(
			'arr_skus' => $arr_skus['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Sku"
		);
		
		$arr_js = array(
        	'suk_main' => base_url()."resources/js/sku/suk_main.js"
    	);
		
		
		$this->view_util->load_view_main('sku/sku_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);

	} 

	function add_sku_form(){

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_css = array(
        	'touchspin_css' => base_url()."global/vendor/bootstrap-touchspin/bootstrap-touchspin.css"
    	);

    	$arr_js = array(
        	'touchspin' => base_url()."global/vendor/bootstrap-touchspin/bootstrap-touchspin.min.js",
        	'bootstrap_touchspin' => base_url()."global/js/Plugin/bootstrap-touchspin.js",
        	'sku_script' => base_url()."resources/js/sku/sku_script.js"

    	);

    	$arr_search = array(
 			'product_cat_search' => '',
 			'search_pro_name' => ''
 		);

    	$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$this->_shop_id);

		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$sess_shop_id);

		if($arr_products['Status'] == "Success"){

			//print_r($arr_products['Data']);
			$max = sizeof($arr_products['Data']);

			for($i=0;$i<$max;$i++){
				$arr_products['Data'][$i]['ProductID'] = $this->encryption_util->encrypt_ssl($arr_products['Data'][$i]['ProductID']);
			}
		}

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$this->_shop_id);

		$data = array(
			'arr_products' => $arr_products['Data'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_search' => $arr_search,
			'is_search' => 0
		);  

		
		$this->view_util->load_view_blankpage('sku/add_sku_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function add_sku_form_search(){

		$cat_id = $this->input->post('product_cat_search');
		$search_pro_name = $this->input->post('search_pro_name');

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_css = array(
        	'touchspin_css' => base_url()."global/vendor/bootstrap-touchspin/bootstrap-touchspin.css"
    	);

    	$arr_js = array(
        	'touchspin' => base_url()."global/vendor/bootstrap-touchspin/bootstrap-touchspin.min.js",
        	'bootstrap_touchspin' => base_url()."global/js/Plugin/bootstrap-touchspin.js",
        	'sku_script' => base_url()."resources/js/sku/sku_script.js"

    	);

    	$arr_search = array(
 			'product_cat_search' => $cat_id,
 			'search_pro_name' => $search_pro_name
 		);

 		$data_curl_search = array(
			'shop_id' => $this->_shop_id,
			'cat_id' => $cat_id,
			'search_pro_name' => $search_pro_name
		);

    	if($cat_id == "All"){
    		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$this->_shop_id);
    		$arr_products = $this->product_model->get_product_by_shop($this->_shop_id);
    	}else{
    		$arr_products = $this->curl_bl->CallApi('POST','product/get_product_by_shop_cat/',$data_curl_search);
    	}
    	

		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$sess_shop_id);

		if($arr_products['Status'] == "Success"){

			//print_r($arr_products['Data']);
			$max = sizeof($arr_products['Data']);

			for($i=0;$i<$max;$i++){
				$arr_products['Data'][$i]['ProductID'] = $this->encryption_util->encrypt_ssl($arr_products['Data'][$i]['ProductID']);
			}
		}

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$this->_shop_id);

		$data = array(
			'arr_products' => $arr_products['Data'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_search' => $arr_search,
			'is_search' => 1
		);  

		
		$this->view_util->load_view_blankpage('sku/add_sku_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function sku_add_ajax(){

		$sku_name = $this->input->post('sku_name');
		$sku_data = $this->input->post('sku_data');
		$ran_id = $this->input->post('ran_id');

		$data_curl = array(
			'sku_name' => $sku_name,
			'sku_data' => $sku_data,
			'temp_key' => $ran_id,
			'ShopID' => $this->_shop_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','sku/sku_add',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function get_sku_by_temp_key(){

		$ran_id = $this->input->post('ran_id');

		$arr_list_sku = $this->curl_bl->CallApiNospi('GET','sku/get_sku_by_temp_key/'.$this->_shop_id.'/'.$ran_id);

		$arr_data = array(
			'arr_list_sku' => $arr_list_sku['Data']
		);
		echo json_encode($arr_data);

	}

	function get_sku_by_product_id(){

		$product_id_en = $this->input->post('product_id_en');

		$arr_list_sku = $this->curl_bl->CallApiNospi('GET','sku/get_sku_by_product_id/'.$this->_shop_id.'/'.$product_id_en);

		$arr_data = array(
			'arr_list_sku' => $arr_list_sku['Data']
		);
		echo json_encode($arr_data);
	}
}