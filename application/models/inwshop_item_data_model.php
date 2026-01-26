<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class inwshop_item_data_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('inwshop_item_data', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('inwshop_item_data_id',$id);
		$this->db->update('inwshop_item_data',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('inwshop_item_data_id',$id);
		$this->db->delete('inwshop_item_data');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('inwshop_item_data');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('inwshop_item_data');
			$this->db->where('inwshop_item_data_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_code($code){
			$this->db->select('*');
			$this->db->from('inwshop_item_data');
			$this->db->where('code',$code);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	


}


