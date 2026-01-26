<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class web_orderitems_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
	
	function insert($data){
    	$this->db->insert('web_orderitems', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('OrderItemID',$id);
		$this->db->update('web_orderitems',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('OrderItemID',$id);
		$this->db->delete('web_orderitems');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('web_orderitems');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_orderitems');
		$this->db->where('OrderItemID',$id);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_order_id($order_id){
		$this->db->select('*');
		$this->db->from('web_orderitems');
		$this->db->join('web_products', 'web_orderitems.ProductID_history = web_products.ProductID');
		$this->db->where('OrderID',$order_id);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_all_by_shop($shop_id){
		$this->db->select('*');
		$this->db->from('web_orderitems');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}
}

