<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class tiktok_data_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('tiktok_data', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('tiktok_data_id',$id);
		$this->db->update('tiktok_data',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('tiktok_data_id',$id);
		$this->db->delete('tiktok_data');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('tiktok_data');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('tiktok_data');
			$this->db->where('tiktok_data_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_code($code){
			$this->db->select('*');
			$this->db->from('tiktok_data');
			$this->db->where('code',$code);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_code_status($code,$status){
			$this->db->select('*');
			$this->db->from('tiktok_data');
			$this->db->where('code',$code);
			$this->db->where('order_status',$status);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_code_status_trackid($code,$status){
			$this->db->select('*');
			$this->db->from('tiktok_data');
			$this->db->where('code',$code);
			$this->db->where('order_status',$status);
			$this->db->where('TrackingID is not null');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	


}


