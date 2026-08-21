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

	private function filter_sort_skus($arr_skus, $sku_search, $sortby, $sorttype, $offset, $per_page)
	{
		$filtered = array();
		$sku_search = trim($sku_search);

		if(!empty($arr_skus)){
			foreach ($arr_skus as $arr_sku){
				if($sku_search === ''){
					$filtered[] = $arr_sku;
					continue;
				}

				$sku_name = !empty($arr_sku['sku_name']) ? $arr_sku['sku_name'] : '';
				$sku_value = !empty($arr_sku['sku_value']) ? $arr_sku['sku_value'] : '';
				if(stripos($sku_name, $sku_search) !== false || stripos($sku_value, $sku_search) !== false){
					$filtered[] = $arr_sku;
				}
			}
		}

		if(!empty($sortby) && in_array($sortby, array('sku_name','sku_value'))){
			usort($filtered, function($a, $b) use ($sortby, $sorttype) {
				$val_a = !empty($a[$sortby]) ? $a[$sortby] : '';
				$val_b = !empty($b[$sortby]) ? $b[$sortby] : '';
				$result = strcasecmp($val_a, $val_b);
				return ($sorttype === 'desc') ? ($result * -1) : $result;
			});
		}

		$offset = intval($offset);
		$per_page = intval($per_page);
		$paged = array_slice($filtered, $offset, $per_page);

		return array(
			'filtered' => $filtered,
			'paged' => $paged
		);
	}

	private function encrypt_sku_ids($arr_skus)
	{
		if(!empty($arr_skus)){
			$max = sizeof($arr_skus);
			for($i=0;$i<$max;$i++){
				$arr_skus[$i]['web_sku_id'] = $this->encryption_util->encrypt_ssl($arr_skus[$i]['web_sku_id']);
			}
		}
		return $arr_skus;
	}
     
	public function sku_list()
	{
		$add_alt = $this->session->flashdata('add_user');
		$edit_alt = $this->session->flashdata('edit_user');
		//echo $add_alt;

		$ran_id = $this->uri->segment(3);
		$shop_id = $this->session->userdata('shop_id');
		$data_search = array(
			'sku_search' => '',
			'shopid_en' => $shop_id,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 0,
			'per_page' => 5
		);

		$arr_skus = $this->sku_model->get_sku_by_shop_id($shop_id);
		$list_data = array();
		if($arr_skus['Status'] == "Success"){
			$processed = $this->filter_sort_skus($arr_skus['Data'],'','', '',0,5);
			$list_data = $this->encrypt_sku_ids($processed['paged']);
		}
		
		$data = array(
			'arr_skus' => $list_data,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Sku"
		);
		
		$arr_js = array(
        	'suk_main' => base_url()."resources/js/sku/suk_main.js",
			'morecontent' => base_url()."resources/js/morecontent/sku/sku_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		
		$this->view_util->load_view_main('sku/sku_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);

	} 

	public function sku_list_search()
	{
		$add_alt = $this->session->flashdata('add_user');
		$edit_alt = $this->session->flashdata('edit_user');
		$shop_id = $this->session->userdata('shop_id');
		$sku_search = $this->input->post('sku_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'sku_search' => $sku_search,
			'shopid_en' => $shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 0,
			'per_page' => 5
		);

		$arr_skus = $this->sku_model->get_sku_by_shop_id($shop_id);
		$list_data = array();
		if($arr_skus['Status'] == "Success"){
			$processed = $this->filter_sort_skus($arr_skus['Data'],$sku_search,$sortby,$sorttype,0,5);
			$list_data = $this->encrypt_sku_ids($processed['paged']);
		}

		$data = array(
			'arr_skus' => $list_data,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Config Sku"
		);
		
		$arr_js = array(
        	'suk_main' => base_url()."resources/js/sku/suk_main.js",
			'morecontent' => base_url()."resources/js/morecontent/sku/sku_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		$this->view_util->load_view_main('sku/sku_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function loaddata_more_ajax(){
		$shop_id = $this->session->userdata('shop_id');
		$sku_search = $this->input->post('sku_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$arr_skus = $this->sku_model->get_sku_by_shop_id($shop_id);
		$list_data = array();
		if($arr_skus['Status'] == "Success"){
			$processed = $this->filter_sort_skus($arr_skus['Data'],$sku_search,$sortby,$sorttype,$offset,5);
			$list_data = $this->encrypt_sku_ids($processed['paged']);
		}

		$arr_data = array(
			'list_data' => $list_data
		);
		echo json_encode($arr_data);
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

		
		$this->view_util->load_view_main('sku/add_sku_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
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

		
		$this->view_util->load_view_main('sku/add_sku_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USER);
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