<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Web_products_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
	
	function insert($data){
    	$this->db->insert('web_products', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('ProductID',$id);
		$this->db->update('web_products',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('ProductID',$id);
		$this->db->delete('web_products');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('web_products');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ProductID',$id);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	
}

