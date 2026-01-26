<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Group_map_controller_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }

    function get_all($per_page){

    	$datas = $this->CI->cache_util->select_data('model','page_permission_group','get_all|'.$per_page,'config_system/page_permission_group/get_all/'.$per_page);

		return $datas;
	}

    function get_by_controller($controller){

    	$datas = $this->CI->cache_util->select_data('model','page_permission_group','get_by_controller|'.$controller,'config_system/page_permission_group/get_by_controller/'.$controller);

		return $datas;
	}

	function get_by_shop_nospin($shop_id){

    	$datas = $this->CI->cache_util->select_data_nospin('model','page_permission_group','get_by_shop|'.$shop_id,'config_system/page_permission_group/get_by_shop/'.$shop_id);

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','page_permission_group','get_by_id|'.$id_en,'config_system/page_permission_group/get_by_id/'.$id_en);

		return $datas;
	}


	function del_cache_by_id($id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','config_system','get_by_id|'.$id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



