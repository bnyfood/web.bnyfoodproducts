<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_bl{
	public function __construct(){
		$this->CI =& get_instance();

	}
	
	
	function create_arr_id($datas,$val_name){
	
	$arr_newdata = array();	
	$cnt_val = count($datas);
	   if($cnt_val > 0){
	   		foreach($datas as $data){
	   			array_push($arr_newdata,$data[$val_name]);
	   		}
	   		$res = $arr_newdata;
	   }else{
	   		$res = array();
	   }	

	   return $res;
	}
	

	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */