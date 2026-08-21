<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Tiktok_line_items_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('tiktok_line_items', $data);
    	//echo $this->db->last_query();
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('tiktok_line_items_id',$id);
		$this->db->update('tiktok_line_items',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('tiktok_line_items_id',$id);
		$this->db->delete('tiktok_line_items');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('tiktok_line_items');
		$this->db->where('tiktok_line_items_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('tiktok_line_items');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_order_id_sku($order_id,$seller_sku){

		$this->db->select('*');
		$this->db->from('tiktok_line_items');
		$this->db->where('order_id',$order_id);
		$this->db->where('seller_sku',$seller_sku);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_orderid_proid($order_id,$product_id){

		$this->db->select('*');
		$this->db->from('tiktok_line_items');
		$this->db->where('order_id',$order_id);
		$this->db->where('product_id',$product_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}
}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */