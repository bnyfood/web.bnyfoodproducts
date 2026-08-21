<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Robot_silo_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('robot_silo', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('robot_silo_id',$id);
		$this->db->update('robot_silo',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('robot_silo_id',$id);
		$this->db->delete('robot_silo');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('robot_silo');
		$this->db->where('robot_silo_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('robot_silo');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('robot_silo');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_robot_id($robotid){

		$this->db->select('*');
		$this->db->from('robot_silo');
		$this->db->where('robotID',$robotid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */