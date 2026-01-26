<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Bny_product_volume_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('bny_product_volume', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('bny_product_volume_id',$id);
		$this->db->update('bny_product_volume',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('bny_product_volume_id',$id);
		$this->db->delete('bny_product_volume');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('bny_product_volume');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('bny_product_volume');
			$this->db->where('bny_product_volume_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_name($sku_name){
			$this->db->select('*');
			$this->db->from('bny_product_volume');
			$this->db->where('bny_product_volume_name',$sku_name);
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}		


}


