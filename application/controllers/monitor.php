<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Monitor extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library("businesslogic/shopee_bl");

		$this->load->model('menu_model');
		$this->load->model('bnylog_model');

        //$this->auth_bl->check_session_exists();

    }
     
	public function main()
	{

		$arr_input = array(
			'title' => "Monitor" 
		);

		$arr_css = array();
		
		$arr_js = array(
	      'lazada_api' => base_url().'resources/js/monitor/lazada_api.js',
	      //'lazada_api_finance' => base_url().'resources/js/monitor/lazada_api_finance.js',
	      'litetime_token' => base_url().'resources/js/monitor/litetime_token.js',
	    ); 	        

		$link=$this->shopee_bl->get_authenticatrion_link(SHOPEE_PATNERKEY,'1001849');

	    $data=array(
	    	'link'=>$link
   		);
				        
		$this->view_util->load_view_main('monitor/main',$data,NULL,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	public function api_order_log(){

		$log_type = $this->uri->segment(3);
		
		$data_apis = $this->bnylog_model->select_order_lazada_last10($log_type);

		$data = array(
			'data_apis' => $data_apis
		);
		
		echo json_encode($data);
	}

	public function change_status_log(){

		$id=$this->uri->segment(3);
		$data = array(
			'log_status' => 2
		);
		$this->bnylog_model->update($data,$id);
	}


}