<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Silo_sub_property_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('silo_sub_property', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('silo_sub_property_id',$id);
		$this->db->update('silo_sub_property',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('silo_sub_property_id',$id);
		$this->db->delete('silo_sub_property');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('silo_sub_property');
		$this->db->where('silo_sub_property_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('silo_sub_property');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_silo_id($silo_id){

		$this->db->select('*');
		$this->db->from('silo_sub_property');
		$this->db->where('silo_id',$silo_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function get_by_silo_id_latest($silo_id){

		$this->db->select('*');
		$this->db->from('silo_sub_property');
		$this->db->where('silo_id',$silo_id);
		$this->db->limit(1);
		$this->db->order_by('update_date','desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */