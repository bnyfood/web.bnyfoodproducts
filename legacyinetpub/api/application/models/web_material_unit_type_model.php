<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_unit_type_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material_unit_type', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_material_unit_type_id',$api_id);
		$this->db->update('web_material_unit_type',$data);
	}

	function delete($id){
		$this->db->where('web_material_unit_type_id',$id);
		$this->db->delete('web_material_unit_type');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_unit_type');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_history_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_unit_type_history_lasted');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material_unit_type');
		$this->db->where('web_material_unit_type_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */