<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Product_map_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('product_map_model', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('product_map_model_id',$api_id);
		$this->db->update('product_map_model',$data);
	}

	function delete($id){
		$this->db->where('product_map_model_id',$id);
		$this->db->delete('product_map_model');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('product_map_model');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('product_map_model');
		$this->db->where('product_map_model_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_product_id($product_id){
		$this->db->select('*');
		$this->db->from('product_map_model');
		$this->db->where('parent_product_id',$product_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('product_map_model');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */