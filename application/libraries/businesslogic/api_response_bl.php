<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_response_bl{
	public function __construct(){
		$this->CI =& get_instance();
	}

	
	function make_response($arr_datas){

		$msg = "fail";

	    if(!empty($arr_datas)){
	      $msg = "success";
	    }

	    $arr = array(
	      'responseMsg' => $msg,
	      'datas' => $arr_datas,
	    );

	    $data_json = json_encode($arr);

    	echo $data_json;
	   
	}

	function make_del_response($arr_data){

		$is_del = 'fail';
	    if(empty($arr_data)){
	      $is_del = 'success';
	    }

	    $arr = array(
	      'responseMsg' => $msg,
	      'datas' => null,
	    );

	    $data_json = json_encode($arr);

    	echo $data_json;
	   
	}


}	