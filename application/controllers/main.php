<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Main extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');

		$this->load->model('menu_model');

        $this->auth_bl->check_session_exists();

     }
     
	public function index()
	{

		$arr_input = array(
			'title' => "Home Admin BNY" 
		);

		$arr_css = array();
		
		$arr_js = array();		        



				        
		$this->view_util->load_view_main('main',NULL,NULL,NULL,$arr_input,MENU_DASHBOARD);

	}

	function test_class()
	{

		$arr_input = array(
			'title' => "Home Admin BNY" 
		);

		$arr_css = array();
		
		$arr_js = array(
			'test_class' => base_url()."resources/js/test_class.js"
		);		        



				        
		$this->view_util->load_view_main('test_class',NULL,NULL,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	function del(){
		$this->menu_model->delete_menu();
	}

	function test_sms(){

		$this->curl_bl->curl_sms('0875824451','test_by_mon','Demo','corporate');



	}
}