<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_discount_list_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_discount_list', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_discount_list', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}
	
	
	function update($data,$id){
    	$this->db->where('DiscountListID',$id);
		$this->db->update('shopee_discount_list',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('DiscountListID',$id);
		$this->db->delete('shopee_discount_list');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shopee_discount_list');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shopee_discount_list');
			$this->db->where('DiscountListID',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

}


