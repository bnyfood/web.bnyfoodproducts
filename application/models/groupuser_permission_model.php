<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Groupuser_permission_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('groupuser_permission', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('groupuser_permission_id',$id);
		$this->db->update('groupuser_permission',$data);
	}

	function delete($id){
		$this->db->where('groupuser_permission_id',$id);
		$this->db->delete('groupuser_permission');
	}

	
	function select_all(){

		$this->db->select('*');
		$this->db->from('groupuser_permission');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('groupuser_permission');
		$this->db->where('groupuser_permission_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_groupmapuser_id($groupmapuser_id){
		$this->db->select('*');
		$this->db->from('groupuser_permission');
		$this->db->where('groupmapuser_id',$groupmapuser_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */