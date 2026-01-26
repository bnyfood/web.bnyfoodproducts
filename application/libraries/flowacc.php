<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Article_mockup_data : is library for load mockup data of article sub saction.
*  Create by peak. 16/04/2013
**/
class Flowacc 
{
	
	
	function __construct() 
	{
		//$this->prepare_data();
		
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		//require_once SYSTEMPATH . 'libraries/flowaccount-php-client/vendor/autoload.php';
		
		require_once('/application/system/libraries/flowaccount-php-client/vendor/autoload.php');
    }


}

/* End of file flowacc.php */
/* Location: ./application/libraries/flowacc.php */