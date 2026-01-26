<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class cookie_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
	}
    

    function cookie_create($name,$value)
    {

      $arr_cookie= array(
          'name'   => $name,
          'value'  => $value,
          'expire' => 31536000,
          'path'   => '/',
          'secure' => FALSE
        );

      $this->CI->input->set_cookie($arr_cookie);

    }



}