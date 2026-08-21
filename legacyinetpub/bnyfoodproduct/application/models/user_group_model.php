<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class User_group_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }

    function get_usergroup_all(){

    	$datas = $this->CI->cache_util->select_data('model','user_group','get_usergroup_all','config_system/usergroup/get_usergroup_all');

		return $datas;
	}
		    
    function get_usergroup_by_shop($id_shop){

    	$datas = $this->CI->cache_util->select_data('model','user_group','get_usergroup_by_shop|'.$id_shop,'config_system/usergroup/get_usergroup_by_shop/'.$id_shop);

		return $datas;
	}

	function get_menu_select_by_group($group_id){

    	$datas = $this->CI->cache_util->select_data('model','user_group','get_menu_select_by_group|'.$group_id,'config_system/usergroup/get_menu_select_by_group/'.$group_id);

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','user_group','get_by_id|'.$id_en,'config_system/usergroup/get_by_id/'.$id_en);

		return $datas;
	}

	function get_by_id_join_shop($id_en){

    	$datas = $this->CI->cache_util->select_data('model','user_group','get_by_id_join_shop|'.$id_en,'config_system/usergroup/get_by_id_join_shop/'.$id_en);

		return $datas;
	}

	function del_cache_group_menu_by_group_id($group_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user_group','get_menu_select_by_group|'.$group_id);

	}

	function del_cache_usergroup_by_shop($id_shop){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user_group','get_usergroup_by_shop|'.$id_shop);

	}

	function del_cache_by_id($id_en){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','user_group','get_by_id|'.$id_en);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



