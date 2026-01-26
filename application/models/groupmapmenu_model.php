<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Groupmapmenu_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('groupmapmenu', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_batch($arr_data){

    	$this->db->insert_batch('groupmapmenu', $arr_data);
	}
	
	function update($data,$id){
    	$this->db->where('groupmapmenu_id',$id);
		$this->db->update('groupmapmenu',$data);
	}

	function del_by_group_menu($group_id,$menu_id){
		$this->db->where('group_id',$group_id);
		$this->db->where('menu_id',$menu_id);
		$this->db->delete('groupmapmenu');
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where('groupmapmenu_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_group($group_id){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where('group_id',$group_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_arr_group_id($arr_group_id){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where_in('group_id',$arr_group_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_menu_id_arr_group_id($menu_id,$arr_group_id){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where('menu_id',$menu_id);
		$this->db->where_in('group_id',$arr_group_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_menu($menu_id){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where('menu_id',$menu_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_group_menu_id($group_id,$menu_id){

		$this->db->select('*');
		$this->db->from('groupmapmenu');
		$this->db->where('group_id',$group_id);
		$this->db->where('menu_id',$menu_id);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */