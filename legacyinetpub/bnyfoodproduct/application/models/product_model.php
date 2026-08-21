<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Product_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_product_by_shop($shop_id){

    	$datas = $this->CI->cache_util->select_data('model','product','get_product_by_shop|'.$shop_id,'product/get_product_by_shop/'.$shop_id);

		return $datas;
	}

    function get_product_by_shop_cat($shop_id,$cat_name,$search_pro_name){

        $datas = $this->CI->cache_util->select_data('model','product','get_product_by_shop_cat|'.$shop_id.'|'.$cat_name.'|'.$search_pro_name,'product/get_product_by_shop_cat/'.$shop_id.'/'.$cat_name.'/'.$search_pro_name);

        return $datas;
    }
    
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



