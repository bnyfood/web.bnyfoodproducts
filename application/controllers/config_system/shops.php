<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Shops extends CI_Controller
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

        $this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
     }
     
	public function shops_list()
	{
		$add_alt = $this->session->flashdata('add_shop');
		$edit_alt = $this->session->flashdata('edit_shop');
		//echo $add_alt;

		//echo "--->>>".$this->_customer_code;
		$arr_shops = $this->web_shop_model->get_shop_by_code($this->_customer_code);

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
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Shop"
		);
		
		$arr_js = array(
        	'init_main' => base_url()."resources/js/init/main.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/shops/shops_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_USERGROUP);

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