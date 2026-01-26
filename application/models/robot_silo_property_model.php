<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Robot_silo_property_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('robot_silo_property', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('robot_silo_property_id',$id);
		$this->db->update('robot_silo_property',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('robot_silo_property_id',$id);
		$this->db->delete('robot_silo_property');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('robot_silo_property');
		$this->db->where('robot_silo_property_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('robot_silo_property');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_robot_id($robot_id){

		$this->db->select('*');
		$this->db->from('robot_silo_property');
		$this->db->where('robotID',$robot_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function get_by_robot_id_latest($robotID){

		$this->db->select('*');
		$this->db->from('robot_silo_property');
		$this->db->where('robotID',$robotID);
		$this->db->limit(1);
		$this->db->order_by('update_date','desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */