<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_unit_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material_unit', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_material_unit_id',$api_id);
		$this->db->update('web_material_unit',$data);
		return $this->db->affected_rows();
	}

	function delete($id){
		$this->db->where('web_material_unit_id',$id);
		$this->db->delete('web_material_unit');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_unit');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material_unit');
		$this->db->where('web_material_unit_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_search($material_unit_search){

		$this->db->select('*');
		$this->db->from('web_material_unit');
		//$this->db->where('ShopID',$shop_id);

		if($material_unit_search != ""){
			$this->db->like('material_unit',$material_unit_search);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_search($material_unit_search,$per_page,$offset,$sortby,$sorttype){

		$this->db->select('*');
		$this->db->from('web_material_unit');

		if($material_unit_search != ""){
			$this->db->like('material_unit',$material_unit_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		if($per_page != ""){
			$this->db->limit($per_page,$offset);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_unit_by_shop_search($material_unit_search){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,ISNULL(unit_type_name_v, web_material_unit_type.unit_type_name) as unit_type_name_v,ISNULL(subunit_volume_v, subunit_volume) as subunit_volume_v,web_material_history_lasted.web_material_id as web_material_id_main,web_material_subunit.sku_name as sku ');
		$this->db->from('web_material_history_lasted');
		$this->db->join('web_material_brand', 'web_material_brand.web_material_brand_id = web_material_history_lasted.material_brand_id');
		$this->db->join('web_material_subunit', 'web_material_subunit.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->join('web_material_unit_type_history', 'web_material_unit_type_history.web_material_unit_type_history_id = web_material_subunit.web_material_unit_type_history_id');
		$this->db->join('web_material_unit_type', 'web_material_unit_type_history.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');

		$this->db->join('web_sub_unit_view', 'web_sub_unit_view.web_material_subunit_id_v = web_material_subunit.web_material_subunit_id','left outer');
		//

		//$this->db->join('web_material_unit_type', 'web_material_history_lasted.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');
		//$this->db->where('web_material_subunit.is_main_unit',$is_main_unit);

		if($material_unit_search != ""){
			$this->db->like('material_name',$material_unit_search);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */