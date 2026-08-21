<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Billing_store_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 function insert($data){
    	$this->db->insert('billing_store', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('billing_store',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('billing_store');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('billing_store');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('billing_store');
			$this->db->where('id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function delete_by_yymm($yymm){

		$this->db->where('order_ym',$yymm);
		$this->db->delete('billing_store');
		
	}

}


