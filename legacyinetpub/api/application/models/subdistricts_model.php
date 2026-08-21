<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Subdistricts_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('Subdistricts', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('SubdistrictsId',$id);
		$this->db->update('Subdistricts',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('Subdistricts');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('Subdistricts');
		$this->db->where('SubdistrictsId',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_district_id($district_id){
		$this->db->select('*');
		$this->db->from('Subdistricts');
		$this->db->where('DistrictId',$district_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}
