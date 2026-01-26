<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage_val_bl{
	public function __construct(){
		$this->CI =& get_instance();
		

	}
	
	
	function make_id_in($arr_val,$id){

		$arr_res = array();
		if(!empty($arr_val)){
			foreach($arr_val as $val){
				array_push($arr_res,$val[$id]);
			}
		}

		return $arr_res;
	}
	
}