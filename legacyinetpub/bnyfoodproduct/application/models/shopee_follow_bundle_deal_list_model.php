<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_follow_bundle_deal_list_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_bundle_deal_list_list', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_bundle_deal_list_list', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}
	
	
	function update($data,$id){
    	$this->db->where('BundleDealListID',$id);
		$this->db->update('shopee_bundle_deal_list_list',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('BundleDealListID',$id);
		$this->db->delete('shopee_bundle_deal_list_list');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shopee_bundle_deal_list_list');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shopee_bundle_deal_list_list');
			$this->db->where('BundleDealListID',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

}


