<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class method_checker 
{
	
	function __construct()
	{
		$this->CI =& get_instance();


	

	$http_method = $_SERVER['REQUEST_METHOD'];
	
	$current_url = str_replace(base_url(),'',current_url());
	//echo $current_url;
			switch($current_url)
			{

             case "users/get_profile": if($http_method!="GET")  { 	die("wrong method"); } break;

             case "config_system/menu/get_menu_all": if($http_method!="GET")  { 	die("wrong method"); } break;
             /*case "users/getprofile": if($http_method!="GET")  { 	die("wrong method"); } break;
             case "users/getprofile": if($http_method!="GET")  { 	die("wrong method"); } break;
             case "users/getprofile": if($http_method!="GET")  { 	die("wrong method"); } break;*/



			}	
		
	}

	



}