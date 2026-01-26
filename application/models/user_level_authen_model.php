<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class User_level_authen_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_all(){

    	$datas = $this->CI->cache_util->select_data('model','user_level_authen','get_all','config_system/page_permission/get_all');

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','user_level_authen','get_by_id|'.$id_en,'config_system/page_permission/get_by_id/'.$id_en);

		return $datas;
	}



	function del_cache_by_cus_id($cus_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user','get_users_by_code|'.$cus_id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



