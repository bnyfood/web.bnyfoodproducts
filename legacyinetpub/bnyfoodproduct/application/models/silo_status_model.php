<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Silo_status_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('silo_status', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('silo_status_id',$id);
		$this->db->update('silo_status',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('silo_status_id',$id);
		$this->db->delete('silo_status');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('silo_status');
		$this->db->where('silo_status_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('silo_status');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_map_id($amt_map_id){

		$this->db->select('*');
		$this->db->from('silo_status');
		$this->db->where('amt_map_id',$amt_map_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_silo_id($silo_id){

		$this->db->select('*');
		$this->db->from('silo_status');
		$this->db->where('silo_id',$silo_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */