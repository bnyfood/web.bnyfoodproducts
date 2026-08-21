<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Admintoken extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]

		$this->load->library('util/View_util');

	    $this->load->library('util/date_util');

		
	    $this->load->model('laztoken_model');
	    $this->load->model('shopee_token_model');
	    $this->load->model('tiktok_token_model');

	    

		//$this->load->library('business_logic/auth_bl');

       // $this->auth_bl->check_session_exists();

     }

     public function token_litetime(){
     	$this->load->library('businesslogic/chat_platform_bl');
     	$this->chat_platform_bl->ensure_lazada_token();
     	$data_token = $this->laztoken_model->get_litetime_token();

		$data = array(
			'data_token' => $data_token
		);
		
		echo json_encode($data);
     }

     public function shopee_token_litetime(){
     	$this->load->library('businesslogic/shopee_bl');
     	$this->shopee_bl->ensure_access_token();
     	$data_token = $this->shopee_token_model->get_litetime_token();

		$data = array(
			'data_token' => $data_token
		);
		
		echo json_encode($data);
     }

     public function tiktok_token_litetime(){
     	$data_token = $this->tiktok_token_model->get_litetime_token();
     	if (empty($data_token)) {
     		$data_token = array(
     			'lessthan' => 0,
     			'litetime' => '00:00:00',
     			'litetime_days' => 0
     		);
     	}
		$data = array(
			'data_token' => $data_token
		);
		echo json_encode($data);
     }

}     