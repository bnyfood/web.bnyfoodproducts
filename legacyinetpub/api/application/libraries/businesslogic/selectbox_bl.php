<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Selectbox_bl{
	public function __construct(){
		$this->CI =& get_instance();
		

		$this->CI->load->model('provinces_model');
		$this->CI->load->model('districts_model');
		$this->CI->load->model('subdistricts_model');


	}
	
	function get_list_aumper($province_id){
		$arr_lists = $this->CI->districts_model->select_by_province_id($province_id);
		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_lists);
		foreach($arr_lists as $arr_list_db){ 
			$arr_list .= "<option value='".$arr_list_db['DistrictId']."'>".$arr_list_db['NameInThai']."</option>";
		}
		


	  $data = array(
		  'arr_aumper_list' => $arr_list
	  );
	   return $data;
	}
	
	function get_list_district($district_id){
		$arr_lists = $this->CI->subdistricts_model->select_by_district_id($district_id);
		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_lists);
		foreach($arr_lists as $arr_list_db){ 
			$arr_list .= "<option value='".$arr_list_db['SubdistrictsId']."'>".$arr_list_db['NameInThai']."</option>";
		}
		


	  $data = array(
		  'arr_district_list' => $arr_list
	  );
	   return $data;
	}

	function get_list_cat($arr_cats){

		$arr_list = "";

		$arr_list .= "<option value=''>Select one...</option>";
		
		//print_r($arr_cats);
		foreach($arr_cats as $arr_cat){ 
			$blank = "";
			for($i=0;$i<=$arr_cat['level'];$i++){
				$blank.= "-";
			}
			$arr_list .= "<option value='".$arr_cat['ProductCategoryID']."'>".$blank.$arr_cat['Title']."</option>";
		}
		


	  $data = array(
		  'arr_cat_list' => $arr_list
	  );
	   return $data;
	}
	

}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */