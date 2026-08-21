<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_purchase_material_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_purchase_material', $data);
    	$insert_id = $this->db->insert_id();
    	//echo $this->db->last_query();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_purchase_material_id',$api_id);
		$this->db->update('web_purchase_material',$data);
	}

	function delete($id){
		$this->db->where('web_purchase_material_id',$id);
		$this->db->delete('web_purchase_material');
		//echo $this->db->last_query();
	}

	function po_del_by_code($code){
		$this->db->where('pocode',$code);
		$this->db->delete('web_purchase_material');
	}

	function po_del_by_po_id($web_purchase_order_id){
		$this->db->where('web_purchase_order_id',$web_purchase_order_id);
		$this->db->delete('web_purchase_material');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_purchase_material');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_purchase_material');
		$this->db->where('web_purchase_material_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_purchase_material');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_po_id($po_id){
		$this->db->select('*');
		$this->db->from('web_purchase_material');
		$this->db->join('web_material', 'web_purchase_material.web_material_id = web_material.web_material_id');
		$this->db->join('web_material_brand', 'web_material.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->where('web_purchase_order_id',$po_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_id_history($web_purchase_material_id){

		$this->db->select('*');
		$this->db->from('web_purchase_material_history');
		$this->db->where('web_purchase_material_id',$web_purchase_material_id);
		$this->db->order_by('web_purchase_material_history_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();

	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */