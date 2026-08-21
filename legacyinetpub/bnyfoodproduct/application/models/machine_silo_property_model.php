<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Machine_silo_property_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('machine_silo_property', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('machine_silo_property_id',$id);
		$this->db->update('machine_silo_property',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('machine_silo_property_id',$id);
		$this->db->delete('machine_silo_property');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('machine_silo_property');
		$this->db->where('machine_silo_property_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('machine_silo_property');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_machine_id($machine_id){

		$this->db->select('*');
		$this->db->from('machine_silo_property');
		$this->db->where('machine_id',$machine_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function get_by_machine_id_latest($machine_id){

		$this->db->select('*');
		$this->db->from('machine_silo_property');
		$this->db->where('machine_id',$machine_id);
		$this->db->limit(1);
		$this->db->order_by('update_date','desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */