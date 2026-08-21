<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_msg_bl{
	public function __construct(){
		$this->CI =& get_instance();
	}

	
	function make_msg($arr_datas){

		$msg = "fail";

	    if(!empty($arr_datas)){
	      $msg = "success";
	    }

	    return $msg;
	   
	}

}	