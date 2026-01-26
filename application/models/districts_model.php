<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Districts_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('Districts', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('DistrictId',$id);
		$this->db->update('Districts',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('Districts');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('Districts');
		$this->db->where('DistrictId',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_province_id($province_id){
		$this->db->select('*');
		$this->db->from('Districts');
		$this->db->where('ProvinceID',$province_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}
