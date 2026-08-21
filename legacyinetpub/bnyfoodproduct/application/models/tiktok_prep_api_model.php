<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class tiktok_prep_api_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('tiktok_prep_api', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('tiktok_prep_api_id',$id);
		$this->db->update('tiktok_prep_api',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('tiktok_prep_api_id',$id);
		$this->db->delete('tiktok_prep_api');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('tiktok_prep_api');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_id($order_id){
		$this->db->select('*');
		$this->db->from('tiktok_prep_api');
		$this->db->where('order_id',$order_id);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

}


