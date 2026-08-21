<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class lazapi 
{

	
	function __construct() 
	{
		
			//echo '<br/>banner_bl constructor running <br/><br/>';
			
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		
		require_once APPPATH.'third_party/api/lazada/LazopSdk.php';
		
	 	
    }
	
	
	
	
	
	
	
	
}

/* End of file article_bl.php */
/* Location: ./application/libraries/bussiness_logic/article_bl.php */