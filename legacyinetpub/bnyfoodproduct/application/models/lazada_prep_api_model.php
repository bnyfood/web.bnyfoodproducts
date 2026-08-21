<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_prep_api_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('lazada_prep_api', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('lazada_prep_api_id',$id);
		$this->db->update('lazada_prep_api',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('lazada_prep_api_id',$id);
		$this->db->delete('lazada_prep_api');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('lazada_prep_api');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_sn($order_sn){
		$this->db->select('*');
		$this->db->from('lazada_prep_api');
		$this->db->where('order_number',$order_sn);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		//return $query->row();
	}	

}


