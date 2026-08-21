<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Menu_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }
		    
    function get_menu_by_group_id($user_id){

    	$datas = $this->CI->cache_util->select_data('model','menus','get_menu_by_group_id|'.$user_id,'menu/get_menu_group/'.$user_id);

		return $datas;
	}

	function get_menu_activated($shop_id){

    	$datas = $this->CI->cache_util->select_data('model','menus','get_menu_activated|'.$shop_id,'menu/get_menu_activated/'.$shop_id);

		return $datas;
	}

	function get_menu_customer(){
		$datas = $this->CI->cache_util->select_data('model','menus','get_menu_customer','menu/get_menu_customer');

		return $datas;
	}

	function delete_menu(){

		$this->CI->cache->delete('model','menus','get_menu_by_group_id|7');
		$this->CI->cache->clean('businesslogic','Permission_bl');
	}
	
	function get_by_parent($parent){
		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu',$parent);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
	function get_by_parent_module_id($parent,$module_id){

		$sql = "select * from menu
		where menu_id in (
		select menu_id from  bny_module_map_menu where bny_module_id = ".$module_id.")
		and parent_menu = '".$parent."'";
		
		$query = $this->db->query($sql);
		return $query->result_array();	
	}
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



