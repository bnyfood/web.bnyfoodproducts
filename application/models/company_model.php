<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class company_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 function insert($data){
    	$this->db->insert('company', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('company',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('company');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('company');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('company');
			$this->db->where('id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

}


