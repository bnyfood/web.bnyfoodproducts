<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_subunit_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material_subunit', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_material_subunit_id',$api_id);
		$this->db->update('web_material_subunit',$data);
	}

	function delete($id){
		$this->db->where('web_material_subunit_id',$id);
		$this->db->delete('web_material_subunit');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_subunit');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material_subunit');
		$this->db->where('web_material_subunit_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_search($material_unit_search){

		$this->db->select('*');
		$this->db->from('web_material_subunit');
		//$this->db->where('ShopID',$shop_id);

		if($material_unit_search != ""){
			$this->db->like('material_unit',$material_unit_search);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */