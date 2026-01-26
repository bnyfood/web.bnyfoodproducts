<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Robot_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('robot', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('robotID',$id);
		$this->db->update('robot',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('robotID',$id);
		$this->db->delete('robot');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('robot');
		$this->db->where('robotID',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('robot');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('robot');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_map_id($map_id){

		$this->db->select('*');
		$this->db->from('robot');
		$this->db->where('amt_map_id',$map_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_all_active(){

		$this->db->select('*');
		$this->db->from('robot');
		$this->db->where('robot_active',1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_all_active_latest(){
		$sql = "select * from (
				select * from robot_position  where RobotpostionID in (
				select RobotpostionID from (
				select  MAX(RobotpostionID) as RobotpostionID,robotID from robot_position group by robotID ) as aa)) position
				inner join robot b on (position.robotID = b.robotID)
				where b.robot_active = 1";
		$query = $this->db->query($sql);
		return $query->result_array();
	
	}

	function select_all_active_latest_by_id($robot_id){
		$sql = "select * from (
				select * from robot_position  where RobotpostionID in (
				select RobotpostionID from (
				select  MAX(RobotpostionID) as RobotpostionID,robotID from robot_position group by robotID ) as aa)) position
				inner join robot b on (position.robotID = b.robotID)
				where b.robot_active = 1 and  b.robotID = ".$robot_id;
		$query = $this->db->query($sql);
		return $query->row_array();
	
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */