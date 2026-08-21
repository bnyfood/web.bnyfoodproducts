<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Group_map_controller_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('group_map_controller', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('group_map_controller_id',$id);
		$this->db->update('group_map_controller',$data);
	}

	function delete($id){
		$this->db->where('group_map_controller_id',$id);
		$this->db->delete('group_map_controller');
	}

	
	function select_all(){

		$this->db->select('*');
		$this->db->from('group_map_controller');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_all_limit($limit){

		$this->db->select('*');
		$this->db->from('group_map_controller');
		$this->db->join('user_group', 'group_map_controller.group_id = user_group.usergroup_id');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('group_map_controller');
		$this->db->where('group_map_controller_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	function select_by_group_id($group_id){
		$this->db->select('*');
		$this->db->from('group_map_controller');
		$this->db->where('group_id',$group_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function select_by_controller($controller){
		$this->db->select('*');
		$this->db->from('group_map_controller');
		$this->db->where('controller',$controller);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_search($text_search,$per_page,$offset,$sortby,$sorttype){
		$this->db->select('*');
		$this->db->from('group_map_controller');
		$this->db->join('user_group', 'group_map_controller.group_id = user_group.usergroup_id');
		if($text_search != ""){
			$this->db->like('controller',$text_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */