<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Windows_and_door_prop_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('windows_and_door_prop', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('windows_and_door_prop_id',$id);
		$this->db->update('windows_and_door_prop',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('windows_and_door_prop_id',$id);
		$this->db->delete('windows_and_door_prop');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('windows_and_door_prop');
		$this->db->where('windows_and_door_prop_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('windows_and_door_prop');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_robot_id($windows_and_door_id){

		$this->db->select('*');
		$this->db->from('windows_and_door_prop');
		$this->db->where('windows_and_door_id',$robot_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function get_by_wandd_id_latest($windows_and_door_id){

		$this->db->select('*');
		$this->db->from('windows_and_door_prop');
		$this->db->where('windows_and_door_id',$windows_and_door_id);
		$this->db->limit(1);
		$this->db->order_by('update_date','desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */