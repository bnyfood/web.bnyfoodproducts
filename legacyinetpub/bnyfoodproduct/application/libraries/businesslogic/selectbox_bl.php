<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Selectbox_bl{
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	function make_list_district($arr_lists){

		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_lists);
		foreach($arr_lists as $arr_list_db){ 
			$arr_list .= "<option value='".$arr_list_db['DistrictId']."'>".$arr_list_db['NameInThai']."</option>";
		}
		


	  $data = array(
		  'arr_district_list' => $arr_list
	  );
	   return $data;
	}
	
	function make_list_subdistrict($arr_lists){
		//$arr_lists = $this->CI->subdistricts_model->select_by_district_id($district_id);
		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_lists);
		foreach($arr_lists as $arr_list_db){ 
			$arr_list .= "<option value='".$arr_list_db['SubdistrictsId']."'>".$arr_list_db['NameInThai']."</option>";
		}
		


	  $data = array(
		  'arr_subdistrict_list' => $arr_list
	  );
	   return $data;
	}

	function make_list_usergroup($arr_lists){
		//$arr_lists = $this->CI->subdistricts_model->select_by_district_id($district_id);
		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_lists);
		foreach($arr_lists as $arr_list_db){ 
			$arr_list .= "<option value='".$arr_list_db['BNYCustomerID']."'>".$arr_list_db['Name']."</option>";
		}
		


	  $data = array(
		  'arr_usergroup_list' => $arr_list
	  );
	   return $data;
	}
	

}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */