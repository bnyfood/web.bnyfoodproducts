<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Groupmapuser_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('groupmapuser', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('groupmapuser_id',$id);
		$this->db->update('menu',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('groupmapuser');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->where('groupmapuser_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_user($user_id){

		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->where('BNYCustomerID',$user_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_bu_group_id($group_id){

		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->where('group_id',$group_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function del_by_group_user($group_id,$user_id){
		$this->db->where('group_id',$group_id);
		$this->db->where('BNYCustomerID',$user_id);
		$this->db->delete('groupmapuser');
	}

	function select_by_group_user($group_id,$user_id){

		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->where('group_id',$group_id);
		$this->db->where('BNYCustomerID',$user_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */