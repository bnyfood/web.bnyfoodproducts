<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_return_order_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_return_order', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('shopee_return_order_id',$id);
		$this->db->update('shopee_return_order',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('shopee_return_order_id',$id);
		$this->db->delete('shopee_return_order');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('shopee_return_order');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_lasted_active($limit){
		$this->db->select('*');
		$this->db->from('shopee_return_order');
		$this->db->where('is_active',0);
		$this->db->limit($limit);
		$this->db->order_by('shopee_return_order_id','ASC');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_sn($order_sn){
		$this->db->select('*');
		$this->db->from('shopee_return_order');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	


}


