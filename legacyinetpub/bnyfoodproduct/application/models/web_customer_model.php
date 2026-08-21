<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Web_customer_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
	
	function insert($data){
    	$this->db->insert('web_customer', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('CustomerID',$id);
		$this->db->update('web_customer',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('CustomerID',$id);
		$this->db->delete('web_customer');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('web_customer');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
				$this->db->select('*');
				$this->db->from('web_customer');
				$this->db->where('CustomerID',$id);
				$query = $this->db->get();
				return $query->row_array();
				//return $query->row();
	}	
}

