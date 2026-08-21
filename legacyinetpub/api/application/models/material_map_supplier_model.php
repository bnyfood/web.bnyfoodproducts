<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Material_map_supplier_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('material_map_supplier', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('material_map_supplier_id',$api_id);
		$this->db->update('material_map_supplier',$data);
	}

	function update_by_code($data,$code){
    	$this->db->where('code',$code);
		$this->db->update('material_map_supplier',$data);
	}

	function update_by_mid_sid($data,$material_id,$web_supplier_id){

		$this->db->where('web_material_id',$material_id);
		$this->db->where('web_supplier_id',$web_supplier_id);
		$this->db->update('material_map_supplier',$data);

	}

	function delete($id){
		$this->db->where('material_map_supplier_id',$id);
		$this->db->delete('material_map_supplier');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->where('material_map_supplier_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_code($code){
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('code',$code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_material_id($web_material_id){
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->where('web_material_id',$web_material_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_code_shop($code,$shop_id){
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('code',$code);
		$this->db->where('material_map_supplier.ShopID',$shop_id);
		$this->db->where('status',1);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_by_matid_supid($material_id,$supplier_id,$shop_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->where('web_material_id',$material_id);
		$this->db->where('web_supplier_id',$supplier_id);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();

	}

	function del_not_in($material_id,$arr_web_supplier_id,$shop_id){

		$this->db->where('web_material_id',$material_id);
		$this->db->where('ShopID',$shop_id);
		$this->db->where_not_in('web_supplier_id',$arr_web_supplier_id);
		$this->db->delete('material_map_supplier');
	}

	function select_by_material_id_lasted($id){
		$this->db->select('*,web_supplier.web_supplier_id as web_supplierid');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('material_map_supplier_history_id',$id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_mapid($material_map_supplier_id){
		$this->db->select('*');
		$this->db->from('material_map_supplier_history');
		$this->db->where('material_map_supplier_history_id',$material_map_supplier_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_material_id_sup_id_lasted($material_id,$supplier_id){
		$this->db->select('*,datediff(minute, cdate,getdate()) as last_change_min');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->where('web_material_id',$material_id);
		$this->db->where('web_supplier_id',$supplier_id);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */