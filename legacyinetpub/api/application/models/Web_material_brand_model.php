<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_brand_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material_brand', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_material_brand_id',$api_id);
		$this->db->update('web_material_brand',$data);
	}

	function delete($id){
		$this->db->where('web_material_brand_id',$id);
		$this->db->delete('web_material_brand');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_brand');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material_brand');
		$this->db->where('web_material_brand_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_material_brand');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('*');
		$this->db->from('web_material_brand');
		$this->db->where('ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_search($shopid,$brand_search,$per_page,$offset,$sortby,$sorttype){
		$this->db->select('*');
		$this->db->from('web_material_brand');
		$this->db->where('ShopID',$shopid);
		if($brand_search != ""){
			$this->db->like('material_brand_name',$brand_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */