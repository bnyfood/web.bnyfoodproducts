<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class address_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('address', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('address',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('address');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('address');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('address');
			$this->db->where('id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_company_id($company_id){
			$this->db->select('*');
			$this->db->from('address');
			$this->db->where('company_id',$company_id);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_company_id_main($company_id){
			$this->db->select('*');
			$this->db->from('address');
			$this->db->where('company_id',$company_id);
			$this->db->where('is_main',1);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	


}


