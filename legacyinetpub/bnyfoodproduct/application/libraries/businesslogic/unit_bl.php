<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Unit_bl{
	public function __construct(){
		$this->CI =& get_instance();

	}
	
	
	function get_unit_with_subunit($datas){
	
		if(!empty($datas)){

			$cnt = count($datas);

			for($i=0;$i<=$cnt-1;$i++){

				$unit_id = $datas[$i]['web_material_id'];

				$this->get_sub_unit($unit_id);
			}

		}

	   return $res;
	}
	
	function get_sub_unit($unit_id){

		
	}
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */