<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_sku_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_sku', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_sku_id',$api_id);
		$this->db->update('web_sku',$data);
	}

	function delete($id){
		$this->db->where('web_sku_id',$id);
		$this->db->delete('web_sku');
	}

	function update_by_ran_id($data,$temp_key){
    	$this->db->where('temp_key',$temp_key);
		$this->db->update('web_sku',$data);
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_sku');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_sku');
		$this->db->where('web_sku_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_sku');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_temp_key($shop_id,$temp_key){
		$this->db->select('*');
		$this->db->from('web_sku');
		$this->db->join('web_sku_map_product', 'web_sku.web_sku_id = web_sku_map_product.web_sku_id');
		$this->db->join('web_products', 'web_products.ProductID = web_sku_map_product.product_id');
		$this->db->where('web_sku.ShopID',$shop_id);
		$this->db->where('web_sku.temp_key',$temp_key);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_product_id($shop_id,$product_id){
		$this->db->select('*');
		$this->db->from('web_sku');
		$this->db->join('web_sku_map_product', 'web_sku.web_sku_id = web_sku_map_product.web_sku_id');
		$this->db->join('web_products', 'web_products.ProductID = web_sku_map_product.product_id');
		$this->db->where('web_sku.ShopID',$shop_id);
		$this->db->where('web_sku.web_product_id',$product_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */