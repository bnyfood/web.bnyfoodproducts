<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Shops extends Auth_Controller
{

	protected $_customer_code;

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/selectbox_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('web_shop_model');

        $this->_customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
     }
     
	public function shops_list()
	{
		$add_alt = $this->session->flashdata('add_shop');
		$edit_alt = $this->session->flashdata('edit_shop');
		//echo $add_alt;

		//echo "--->>>".$this->_customer_code;
		$data_search = array(
			'shop_search' => '',
			'customer_code' => $this->_customer_code,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);
		$arr_shops = $this->curl_bl->CallApi('POST','config_system/shops/shop_search',$data_search);

		//print_r($arr_shops);
		if($arr_shops['Status'] == "Success"){
			$max = sizeof($arr_shops['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shops['Data'][$i]['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shops['Data'][$i]['ShopID']);
			}
		}
		
		$data = array(
			'arr_shops' => $arr_shops['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Shop"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/config_system/shop_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/shops/shops_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	} 

	public function shops_list_search()
	{
		$add_alt = $this->session->flashdata('add_shop');
		$edit_alt = $this->session->flashdata('edit_shop');
		$shop_search = $this->input->post('shop_search');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data_search = array(
			'shop_search' => $shop_search,
			'customer_code' => $this->_customer_code,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => 1,
			'per_page' => 5
		);
		$arr_shops = $this->curl_bl->CallApi('POST','config_system/shops/shop_search',$data_search);

		if($arr_shops['Status'] == "Success"){
			$max = sizeof($arr_shops['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shops['Data'][$i]['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shops['Data'][$i]['ShopID']);
			}
		}

		$data = array(
			'arr_shops' => $arr_shops['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		$arr_input = array(
			'title' => "Config Shop"
		);

		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/config_system/shop_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
    	);
		
		$this->view_util->load_view_main('config_system/shops/shops_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function loaddata_more_ajax(){

		$shop_search = $this->input->post('shop_search');
		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'shop_search' => $shop_search,
			'customer_code' => $this->_customer_code,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5
		);

		$arr_shops = $this->curl_bl->CallApiNospi('POST','config_system/shops/shop_search',$data);

		if($arr_shops['Status'] == "Success"){
			$max = sizeof($arr_shops['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shops['Data'][$i]['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shops['Data'][$i]['ShopID']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_shops['Data']
		);
		echo json_encode($arr_data);

	}

	function add_shop_form(){

		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_usergroup.js"
    	);	
		
		$this->view_util->load_view_main('config_system/shops/add_shop_form',NULL,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function shop_add(){

		$ShopName = $this->input->post('ShopName');
		$domain = $this->input->post('domain');
		$URL_home = $this->input->post('URL_home');
		$ip = $this->input->post('ip');

		$data_curl = array(
			'ShopName' => $ShopName,
			'domain' => $domain,
			'URL_home' => $URL_home,
			'ip' => $ip,
			'customer_code' => $this->_customer_code
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/shops/shop_add',$data_curl);
		//print_r($arr_res);

		$this->web_shop_model->del_cache_shop_by_code($this->_customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/shops/shops_list','refresh');
	}

	function shop_edit_form(){

		$id_en = $this->uri->segment(4);

		$arr_shop = $this->web_shop_model->get_by_id($id_en);

		if($arr_shop['Status'] == "Success"){

			$arr_shop['Data']['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shop['Data']['ShopID']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_usergroup_edit.js"
    	);

    	$data = array(
			'arr_shop' => $arr_shop['Data']
		);
		
		
		$this->view_util->load_view_main('config_system/shops/edit_shop_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function shop_edit(){

		$id_en = $this->input->post('id_en');
		
		$ShopName = $this->input->post('ShopName');
		$domain = $this->input->post('domain');
		$URL_home = $this->input->post('URL_home');
		$ip = $this->input->post('ip');

		$data_curl = array(
			'id_en' => $id_en,
			'ShopName' => $ShopName,
			'domain' => $domain,
			'URL_home' => $URL_home,
			'ip' => $ip
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/shops/shop_edit',$data_curl);
		//print_r($arr_res);

		$this->web_shop_model->del_cache_shop_by_id($id_en);
		$this->web_shop_model->del_cache_shop_by_code($this->_customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/shops/shops_list','refresh');
	}

	function del_action(){

		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/shops/del_action/'.$id_en);
		//print_r($arr_res);
		$this->web_shop_model->del_cache_shop_by_code($this->_customer_code);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/shops/shops_list','refresh');

	}
}