<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shp_orders_migrate_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shp_orders_migrate', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shp_orders_migrate', $arr_data); 

	}
	
	
	function update($data,$id){
    	$this->db->where('shp_orders_migrate_id',$id);
		$this->db->update('shp_orders_migrate',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('shp_orders_migrate_id',$id);
		$this->db->delete('shp_orders_migrate');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shp_orders_migrate');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shp_orders_migrate');
			$this->db->where('shp_orders_migrate_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_sn($order_sn){
			$this->db->select('*');
			$this->db->from('shp_orders_migrate');
			$this->db->where('order_sn',$order_sn);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_list_sn($limit){
		$this->db->select('*');
		$this->db->from('shp_orders_migrate');
		$this->db->where("order_sn not in (select order_sn from shopee_orders group by order_sn)");
		$this->db->limit($limit);
		$this->db->order_by('create_time','asc');
		$query = $this->db->get();
		return $query->result_array();
	}


}


