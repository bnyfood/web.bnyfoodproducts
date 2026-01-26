<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class User_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_user_by_code_noid($id_en,$customer_code){

    	$datas = $this->CI->cache_util->select_data('model','user','get_user_by_code_noid|'.$id_en.'|'.$customer_code.'|5','config_system/users/get_user_by_code_noid/'.$id_en.'/'.$customer_code.'/5');

		return $datas;
	}

	function get_users_by_group_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','user','get_users_by_group_id|'.$id_en,'config_system/users/get_users_by_group_id/'.$id_en);

		return $datas;
	}

	function get_users_by_group_id_nospin($id_en){

    	$datas = $this->CI->cache_util->select_data_nospin('model','user','get_users_by_group_id|'.$id_en,'config_system/users/get_users_by_group_id/'.$id_en);

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','user','get_by_id|'.$id_en,'config_system/users/get_by_id/'.$id_en);

		return $datas;
	}

	function get_users_by_code($customer_code){

    	$datas = $this->CI->cache_util->select_data('model','user','get_users_by_code|'.$customer_code,'config_system/users/get_users_by_code/'.$customer_code);

		return $datas;
	}

	function del_cache_by_group_id($group_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user','get_users_by_group_id|'.$group_id);

	}

	function del_cache_by_cus_id($cus_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user','get_users_by_code|'.$cus_id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



