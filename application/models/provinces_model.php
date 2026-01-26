<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Provinces_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('Provinces', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('ProvinceID',$id);
		$this->db->update('Provinces',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('Provinces');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('Provinces');
		$this->db->where('ProvinceID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_zip($zip){

		$this->db->select('Provinces.ProvinceID as province_id,Provinces.NameInThai as province_name,Districts.DistrictId as district_id,Districts.NameInThai as district_name,Subdistricts.NameInThai as subdistrict_name,Subdistricts.SubdistrictsId as subdistrict_id,Subdistricts.ZipCode as zipcode');
		$this->db->from('Provinces');
		$this->db->join('Districts', 'Provinces.ProvinceID = Districts.ProvinceID');
		$this->db->join('Subdistricts', 'Districts.DistrictId = Subdistricts.DistrictId');
		$this->db->where('Subdistricts.ZipCode',$zip);
		$query = $this->db->get();
		return $query->result_array();
	}

	
}
