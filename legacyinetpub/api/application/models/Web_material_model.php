<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_material_id',$api_id);
		$this->db->update('web_material',$data);
	}

	function delete($id){
		$this->db->where('web_material_id',$id);
		$this->db->delete('web_material');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material');
		$this->db->where('web_material_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_id_lasted($id){
		$this->db->select('*,web_material_history_lasted.web_material_id as material_id');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->where('web_material_history_lasted.web_material_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_id_history($id){
		$this->db->select('*');
		$this->db->from('web_material_history');
		$this->db->where('web_material_id',$id);
		$this->db->order_by('web_material_history_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_material');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_join($shop_id){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->join('web_material_brand', 'web_material.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->where('material_map_supplier.ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_join_limit($shop_id,$limit){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->join('web_material_brand', 'web_material.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->where('material_map_supplier.ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_join_limit_his($shop_id,$limit){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier_history_lasted.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier_history_lasted');
		
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_brand', 'web_material_history_lasted.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_shop_id_join_limit_his_v1($shop_id,$limit){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier_history_lasted.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_brand', 'web_material_history_lasted.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_shop_id_join_search($shopid,$material_search,$per_page,$offset,$sortby,$sorttype){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->join('web_material_brand', 'web_material.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->where('material_map_supplier.ShopID',$shopid);
		if($material_search != ""){
			$this->db->like('web_material.material_name',$material_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_unit_by_shop_search_v1($material_unit_search){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,ISNULL(unit_type_name_v, web_material_unit_type.unit_type_name) as unit_type_name_v,ISNULL(subunit_volume_v, subunit_volume) as subunit_volume_v,web_material_history_lasted.web_material_id as web_material_id_main,material_map_supplier_history_lasted.web_material_id as a_id ');
		$this->db->from('material_map_supplier_history_lasted');

		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_subunit', 'web_material_subunit.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->join('web_material_unit_type_history', 'web_material_unit_type_history.web_material_unit_type_history_id = web_material_subunit.web_material_unit_type_history_id');
		$this->db->join('web_material_unit_type', 'web_material_unit_type_history.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');

		$this->db->join('web_sub_unit_view', 'web_sub_unit_view.web_material_subunit_id_v = web_material_subunit.web_material_subunit_id','left outer');

		//$this->db->join('web_material_unit_type', 'web_material_history_lasted.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');
		//$this->db->where('web_material_subunit.is_main_unit',$is_main_unit);
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shopid);

		if($material_unit_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}

		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_unit_by_shop_search($shopid,$material_search,$per_page,$offset,$sortby,$sorttype){
		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,ISNULL(unit_type_name_v, web_material_unit_type.unit_type_name) as unit_type_name_v,ISNULL(subunit_volume_v, subunit_volume) as subunit_volume_v,web_material_history_lasted.web_material_id as web_material_id_main ');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_subunit', 'web_material_subunit.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->join('web_material_unit_type_history', 'web_material_unit_type_history.web_material_unit_type_history_id = web_material_subunit.web_material_unit_type_history_id');
		$this->db->join('web_material_unit_type', 'web_material_unit_type_history.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');

		$this->db->join('web_sub_unit_view', 'web_sub_unit_view.web_material_subunit_id_v = web_material_subunit.web_material_subunit_id','left outer');

		//$this->db->join('web_material_unit_type', 'web_material_history_lasted.web_material_unit_type_id = web_material_unit_type.web_material_unit_type_id');
		//$this->db->where('web_material_subunit.is_main_unit',$is_main_unit);
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shopid);

		if($material_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}

		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}



	function select_by_shop_id_join_search_his($shopid,$material_search,$per_page,$offset,$sortby,$sorttype){

		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier_history_lasted.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_brand', 'web_material_history_lasted.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shopid);
		if($material_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_shop_id_join_search_his_v2($shopid,$material_search){

		//$this->db->select('supplier_name,material_name,material_brand_name,material_map_supplier.web_material_id as web_material_id');
		$this->db->select('*,material_map_supplier_history_lasted.web_material_id as web_material_id,web_supplier.web_supplier_id');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_brand', 'web_material_history_lasted.material_brand_id = web_material_brand.web_material_brand_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('material_map_supplier_history_lasted.ShopID',$shopid);
		if($material_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
	function search_by_keyword($ShopID,$web_supplier_id,$krysearch){

		$this->db->select('*');
		$this->db->from('web_material');
		$this->db->join('material_map_supplier', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->where('web_material.ShopID',$ShopID);
		$this->db->where('web_supplier_id',$web_supplier_id);
		$this->db->like('material_name',$krysearch);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function search_by_keyword_history($ShopID,$web_supplier_id,$krysearch){

		$this->db->select('*');
		$this->db->from('web_material_history_lasted');
		$this->db->join('material_map_supplier_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$this->db->where('material_map_supplier_history_lasted.web_supplier_id',$web_supplier_id);
		$this->db->like('web_material_history_lasted.material_name',$krysearch);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function search_by_shop_history($ShopID){

		$this->db->select('*');
		$this->db->from('web_material_history_lasted');
		$this->db->join('material_map_supplier_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function select_by_matid_no_supid($ShopID,$web_material_id,$web_supplier_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->join('web_material', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_material.ShopID',$ShopID);
		$this->db->where('web_material.web_material_id',$web_material_id);
		$this->db->where('material_map_supplier.web_supplier_id <>',$web_supplier_id);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function select_by_matid($ShopID,$web_material_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier');
		$this->db->join('web_material', 'material_map_supplier.web_material_id = web_material.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_material.ShopID',$ShopID);
		$this->db->where('web_material.web_material_id',$web_material_id);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function select_by_matid_his($ShopID,$web_material_id,$supplier_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$this->db->where('web_material_history_lasted.web_material_id',$web_material_id);
		$this->db->where('material_map_supplier_history_lasted.web_supplier_id',$web_supplier_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function select_by_matid_supid($ShopID,$web_material_id,$web_supplier_id){

		$this->db->select('*');
		$this->db->from('material_map_supplier_history');
		$this->db->join('web_material', 'material_map_supplier_history.web_material_id = web_material.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier_history.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->where('web_material.ShopID',$ShopID);
		$this->db->where('web_material.web_material_id',$web_material_id);
		$this->db->where('web_supplier.web_supplier_id',$web_supplier_id);
		$this->db->limit(1);
		$this->db->order_by('material_map_supplier_history_id','desc');
		$query = $this->db->get();
		return $query->row_array();
		
	}

	function select_by_matid_supid_his($ShopID,$web_material_id,$web_supplier_id){

		$this->db->select('*,datediff(minute, material_map_supplier_history_lasted.cdate,getdate()) as last_change_min');
		$this->db->from('material_map_supplier_history_lasted');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_supplier', 'material_map_supplier_history_lasted.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$this->db->where('web_material_history_lasted.web_material_id',$web_material_id);
		$this->db->where('web_supplier.web_supplier_id',$web_supplier_id);
		$this->db->limit(1);
		$this->db->order_by('material_map_supplier_history_id','desc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		
	}


	function search_by_name($ShopID,$name){

		$this->db->select('*');
		$this->db->from('web_material');
		$this->db->where('ShopID',$ShopID);
		$this->db->where('material_name',$name);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		
	}

	function search_by_name_join($ShopID,$name,$web_supplier_id){

		$this->db->select('*,web_material.web_material_id as web_material_id_main');
		$this->db->from('material_map_supplier_history');
		//$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material', 'material_map_supplier_history.web_material_id = web_material.web_material_id');
		$this->db->where('web_material.ShopID',$ShopID);
		$this->db->where('web_material.material_name',$name);
		$this->db->where('material_map_supplier_history.web_supplier_id',$web_supplier_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		
	}

	function search_by_name_join_history($ShopID,$txt_search,$txt_size,$txt_unit,$web_supplier_id){

		$this->db->select('*,web_material_history_lasted.web_material_id as web_material_id_main');
		$this->db->from('material_map_supplier_history_lasted');
		//$this->db->join('web_supplier', 'material_map_supplier.web_supplier_id = web_supplier.web_supplier_id');
		$this->db->join('web_material_history_lasted', 'material_map_supplier_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_unit', 'web_material_history_lasted.web_material_unit_id = web_material_unit.web_material_unit_id');
		$this->db->where('web_material_history_lasted.ShopID',$ShopID);
		$this->db->where('web_material_history_lasted.material_name',$txt_search);
		$this->db->where('web_material_history_lasted.material_size',$txt_size);
		$this->db->where('web_material_unit.material_unit',$txt_unit);
		$this->db->where('material_map_supplier_history_lasted.web_supplier_id',$web_supplier_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		
	}
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */