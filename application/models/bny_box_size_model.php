<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Bny_box_size_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('bny_box_size', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('box_size_id',$id);
		$this->db->update('bny_box_size',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('box_size_id',$id);
		$this->db->delete('bny_box_size');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('bny_box_size');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('bny_box_size');
			$this->db->where('box_size_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_box_asc(){
			$this->db->select("*,'' as product_v");
			$this->db->from('bny_box_size');
			$this->db->order_by('boxsize_v','asc');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_mearch($mearch){
			$this->db->select("*");
			$this->db->from('bny_box_size');
			$this->db->where('box_mearch',$mearch);
			$this->db->order_by('boxsize_v','asc');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_instead($box_v){
			$this->db->select("*");
			$this->db->from('bny_box_size');
			$this->db->where('box_mearch',1);
			$this->db->where('boxsize_v >= ',$box_v);
			$this->db->order_by('boxsize_v','asc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_arr_id($arr_box_id){
			$this->db->select("*,'' as product_v");
			$this->db->from('bny_box_size');
			$this->db->where_in('box_size_id',$arr_box_id);
			$this->db->order_by('boxsize_v','asc');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	


}


