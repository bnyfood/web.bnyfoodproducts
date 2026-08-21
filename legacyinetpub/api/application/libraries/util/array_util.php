<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class array_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
	}
    

    function if_elemenmt_exist($key,$arr)
    {

    	if(array_key_exists($key, $arr))
			{
              return $arr[$key];
			}
			else

			{
              return NULL;
			}
    }


    function getlastElement($arr)
    {

      
      foreach($arr as $el)
      {
      	$final=$el;

      }

      return($final);


    }

}