<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class common_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
	}
    
function get_start_end_date($daterangein)
{

$arr=explode("hp",$daterangein);

$arr[0]=str_replace("sp","",$arr[0]);
$arr[1]=str_replace("sp","",$arr[1]);

$startdate=explode("sl",$arr[0]);

$mm=$startdate[0];
$dd=$startdate[1];
$yyyy=$startdate[2];


$startdate[0]=$yyyy."-".$mm."-".$dd;



$enddate=explode("sl",$arr[1]);


}
    
    function getDbDate($datein)
    {

$format='Y-m-d H:i:s';

$time = strtotime($datein);

return date($format,$time);


      }


      	function hmac_sha256($data, $key){
	    return hash_hmac('sha256', $data, $key);
	}


	function prep_float($num_text_in)
	{

$pattern = '/,/i';
return preg_replace($pattern, '', $num_text_in)/1;


	}


}