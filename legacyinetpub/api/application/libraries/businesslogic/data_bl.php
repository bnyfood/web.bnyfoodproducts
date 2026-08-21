<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_bl{
	public function __construct(){
		$this->CI =& get_instance();

		$this->CI->load->library("businesslogic/number_bl");

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

	function get_pocode($last_code,$cdate){
    
	    //202103
	    $laz_ymcode = substr($last_code, 2,6);
	    $laz_code = substr($last_code, 8,5);
	    $cdate = strtotime($cdate);
	    $cdate_code = $newformat = date('Ym',$cdate);

	    if($last_code == 'no'){
	      $laz_newcode = "PO".$cdate_code."00001";
	    }else{
	      $laz_nextcode = $laz_code+1;
	      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
	      $laz_newcode = "PO".$laz_ymcode.$laz_nextcode;
	    }
	     return $laz_newcode;
	  }

	function get_ircode($last_code,$cdate){
    
	    //202103
	    $laz_ymcode = substr($last_code, 2,6);
	    $laz_code = substr($last_code, 8,5);
	    $cdate = strtotime($cdate);
	    $cdate_code = $newformat = date('Ym',$cdate);

	    if($last_code == 'no'){
	      $laz_newcode = "IR".$cdate_code."00001";
	    }else{
	      $laz_nextcode = $laz_code+1;
	      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
	      $laz_newcode = "IR".$laz_ymcode.$laz_nextcode;
	    }
	     return $laz_newcode;
	  }
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */