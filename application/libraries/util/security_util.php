<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class security_util 
{
	
	private $CI;
	
	
	function __construct() 
	{
				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		//$this->CI->load->library('session');
		//$this->CI->load->library('util/view/View_display');
		//$this->CI->load->library('business_logic/banner_bl');
		//$this->CI->load->library('business_logic/navigation_bl');
		//$this->CI->load->library('util/breadcrumb_component');
		//$this->CI->load->library('util/image_util');
    }
	
	
	
	
	
	function md5_hash($text)
	{


	return hash_hmac('md5',$text,KEY_ENCRYPTION,false);

	}
	
	
	
}

/* End of file view_util.php */
/* Location: ./application/libraries/util/view_util.php */