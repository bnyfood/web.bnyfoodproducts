<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Sub_menu_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_submenu_by_group_id($usergroup_id){

    	$datas = $this->CI->cache_util->select_data('model','sub_menus','get_submenu_by_group_id|'.$usergroup_id,'menu/get_submenu/'.$usergroup_id);

		return $datas;
	}

	function delete_menu(){

		$this->CI->cache->delete('model','sub_menus','get_submenu_by_group_id|7');
	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



