<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Sku_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_sku_by_shop_id($shop_id){

    	$datas = $this->CI->cache_util->select_data('model','sku','get_sku_by_shop_id|'.$shop_id,'sku/get_sku_by_shop_id/'.$shop_id);

		return $datas;
	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



