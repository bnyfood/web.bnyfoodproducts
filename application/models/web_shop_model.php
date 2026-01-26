<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_shop_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');

    }
		    
    function get_shop_by_code($customer_code){

    	$datas = $this->CI->cache_util->select_data('model','web_shop','get_shop_by_code|'.$customer_code,'config_system/shops/get_shop_by_code/'.$customer_code);

		return $datas;
	}

	function get_shop_by_customer_type($customer_type,$customer_code,$usergroup_id){

    	$datas = $this->CI->cache_util->select_data('model','web_shop','get_shop_by_customer_type|'.$customer_type.'|'.$customer_code.'|'.$usergroup_id,'config_system/shops/get_shop_by_customer_type/'.$customer_type.'/'.$customer_code.'/'.$usergroup_id);

		return $datas;
	}

	function get_by_id($id){

    	$datas = $this->CI->cache_util->select_data('model','web_shop','get_by_id|'.$id,'config_system/shops/get_by_id/'.$id);

		return $datas;
	}

	function del_cache_shop_by_code($customer_code){

		$this->CI->cache->delete('model','web_shop','get_shop_by_code|'.$customer_code);

	}

	function del_cache_shop_by_id($id){

		$this->CI->cache->delete('model','web_shop','get_by_id|'.$id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



