<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Door extends CI_Controller
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
		$this->load->helper('cookie');

		$this->load->model('web_shop_model');

        //$this->auth_bl->check_session_exists();

     }
     
	public function door_remote()
	{


		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Shop"
		);
		
		$arr_js = array(
        	'door_js' => base_url()."resources/js/inventory/door.js"
    	);
		
		
		$this->view_util->load_view_main('inventory/door/door_remote',NULL,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	} 

	function door_active(){
		$door_val = $this->input->post('door_val');

		$cookie_userid = get_cookie('cookie_userid');

		$data_curl = array(
			'bny_user_id' => $cookie_userid,
			'bny_door_active' => $door_val,
		);

		$arr_res = $this->curl_bl->CallApi('POST','inventory/door/door_active',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}

	}

	
}