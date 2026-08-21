<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Door extends CI_Controller
{
	private $arr_header = '';
	private $api_authen = true;
    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		$this->load->library('businesslogic/api_log_bl');
		$this->load->library('businesslogic/api_auth_bl');
		$this->load->library('businesslogic/data_bl');


		$this->load->model('bny_door_model');


		$this->arr_header = $this->api_auth_bl->check_api_auth_token();
		
     }

	function door_active(){


	    	$bny_user_id_en = $this->input->post('bny_user_id');
	    	$bny_user_id = $this->encryption_util->decrypt_ssl($bny_user_id_en);
	    	$bny_door_active = $this->input->post('bny_door_active');

	    	$bny_door_status = 0;

	    	if($bny_door_active == 0){
	    		$bny_door_status = 2;
	    	}


			$arr_menu = array(
				'bny_user_id' => $bny_user_id,
				'bny_door_active' => $bny_door_active,
				'bny_door_status' => $bny_door_status
			);

	      $data_re = $this->bny_door_model->insert($arr_menu);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$this->arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$this->arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	}

	

}