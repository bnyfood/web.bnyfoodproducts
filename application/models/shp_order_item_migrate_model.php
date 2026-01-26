<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shp_order_item_migrate_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shp_order_item_migrate', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shp_order_item_migrate', $arr_data); 

	}

	
	
	function update($data,$id){
    	$this->db->where('shp_order_item_migrate_id',$id);
		$this->db->update('shp_order_item_migrate',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('shp_order_item_migrate_id',$id);
		$this->db->delete('shp_order_item_migrate');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shp_order_item_migrate');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shp_order_item_migrate');
			$this->db->where('shp_order_item_migrate_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_sn($order_sn){

		$this->db->select('*');
		$this->db->from('shp_order_item_migrate');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();

	}


}


