<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class User_group_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('user_group', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('usergroup_id',$id);
		$this->db->update('user_group',$data);
	}

	function delete($id){
		$this->db->where('usergroup_id',$id);
		$this->db->delete('user_group');
	}

	
	function select_all(){

		$this->db->select('*');
		$this->db->from('user_group');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('usergroup_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_not_id($id){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('usergroup_id <>',$id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_group_name($group_name){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('group_name',$group_name);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_group_name_not_id($group_name,$group_id){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('group_name',$group_name);
		$this->db->where('usergroup_id <>',$group_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop($shop_id){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_lasted($shop_id){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('ShopID',$shop_id);
		$this->db->order_by('usergroup_id','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_temp_by_shop($shop_id){

		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->where('ShopID',$shop_id);
		$this->db->where('is_temp_group','1');
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function get_by_id_join_shop($id){
		$this->db->select('*');
		$this->db->from('user_group');
		$this->db->join('web_shop', 'user_group.ShopID = web_shop.ShopID');
		$this->db->where('usergroup_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */