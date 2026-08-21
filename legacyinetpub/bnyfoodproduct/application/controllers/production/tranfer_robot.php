<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Tranfer_robot extends CI_Controller
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
     
	public function main()
	{

		$arr_input = array(
			'title' => "Tranfer robot" 
		);

		$arr_css = array();
		
		$arr_js = array();		        



				        
		$this->view_util->load_view_main('production/tranfer_robot/main',NULL,NULL,NULL,$arr_input,MENU_DASHBOARD);

	}

}