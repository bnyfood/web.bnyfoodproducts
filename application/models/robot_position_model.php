<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Robot_position_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('robot_position', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('RobotpostionID',$id);
		$this->db->update('robot_position',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('RobotpostionID',$id);
		$this->db->delete('robot_position');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('robot_position');
		$this->db->where('RobotpostionID',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('robot_position');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_robotid($id){

		$this->db->select('*');
		$this->db->from('robot');
		$this->db->join('robot_position', 'robot.robotID = robot_position.robotID');
		$this->db->where('robot.robotID',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */