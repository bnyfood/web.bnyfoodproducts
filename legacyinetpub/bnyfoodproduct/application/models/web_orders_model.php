<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Web_orders_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
	
	function insert($data){
    	$this->db->insert('web_orders', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('OrderID',$id);
		$this->db->update('web_orders',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('OrderID',$id);
		$this->db->delete('web_orders');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('web_orders');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_orders');
		$this->db->where('OrderID',$id);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_all_by_shop($shop_id){
		$this->db->select('*');
		$this->db->from('web_orders');
		$this->db->join('web_customer', 'web_orders.CustomerID = web_customer.CustomerID');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
			$this->db->from('web_orders');
			$this->db->where("FORMAT ( [created_at] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->order_by('OrderID','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}
}

