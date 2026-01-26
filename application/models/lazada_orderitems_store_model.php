<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_orderitems_store_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	

	function insert($data){
    	$this->db->insert('lazada_orderitems_store', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function insert_all($arr_data){
    	$this->db->insert_batch('lazada_orderitems_store', $arr_data); 
    	//echo $this->db->last_query();
	}
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('lazada_orderitems_store',$data);
		//echo $this->db->last_query();
	}
	
	function update_by_order_id($data,$order_id){
    	$this->db->where('id',$order_id);
		$this->db->update('lazada_orderitems_store',$data);
	}

	function update_by_order_itemid($data,$orderitem_id){
    	$this->db->where('OrderItemID',$orderitem_id);
		$this->db->update('lazada_orderitems_store',$data);
	}

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('lazada_orderitems_store');
	}

	function delete_by_order_number($order_number){
		$this->db->where('order_number',$order_number);
		$this->db->delete('lazada_orderitems_store');
	}

	function delete_by_order_number_in($arr_numder){
		$this->db->where_in('order_number',$arr_numder);
		$this->db->delete('lazada_orderitems_store');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('lazada_orderitems_store');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_sum_by_order_number($order_number){
		$this->db->select('sum(paid_price) as psum');
		$this->db->from('lazada_orderitems_store');
		$this->db->where('order_number',$order_number);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	
	

	function select_by_order_number($order_number){
		$this->db->select('*');
		$this->db->from('lazada_orderitems_store');
		$this->db->where('order_number',$order_number);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	


}


