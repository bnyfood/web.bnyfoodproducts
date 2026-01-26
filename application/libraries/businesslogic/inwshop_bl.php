<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class inwshop_bl
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();
       // $this->CI->load->library('businesslogic/curl_bl'); 
       $this->CI->load->library('util/date_util');
       $this->CI->load->library("businesslogic/number_bl");


		
    }

    function get_inwshop_code($last_code,$cdate){
  
        //202103
        $laz_ymcode = substr($last_code, 2,6);
        $laz_code = substr($last_code, 8,5);
        $cdate = strtotime($cdate);
        $cdate_code = $newformat = date('Ym',$cdate);

        if($last_code == 'no'){
          $laz_newcode = "BG".$cdate_code."00001";
        }else{
          $laz_nextcode = $laz_code+1;
          $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
          $laz_newcode = "BG".$laz_ymcode.$laz_nextcode;
        }
       return $laz_newcode;
    }

  function get_inwshop_code_bk($last_code,$cdate){
  
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($last_code == 'no'){
      $laz_newcode = "Inw".$cdate_code."00001";
    }else{
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "Inw".$laz_ymcode.$laz_nextcode;
    }
   return $laz_newcode;
  }

}