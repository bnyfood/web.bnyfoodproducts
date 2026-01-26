<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class bnylog_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 function insert($data){
    	$this->db->insert('bnylog', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('bnylog',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('bnylog');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('bnylog');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_logtype($logtype){
			$this->db->select('*');
			$this->db->from('bnylog');
			$this->db->where('log_type',$logtype);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_chk_order_lazada_last20(){
		$sql = "select cnt from (
				select count(log_code)as cnt from bnylog where log_type=1   group by log_code )as aa where cnt < 5";

  		$query = $this->db->query($sql);
		return $query->result_array();
				
		}

	function select_order_lazada_status_last10($status){
		$this->db->select('*');
		$this->db->from('bnylog');
		$this->db->where('log_type',1);
		$this->db->where('log_status',$status);
		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$query = $this->db->get();
		return $query->result_array();
	}	

	function select_order_lazada_last10($log_type){
		$this->db->select('*');
		$this->db->from('bnylog');
		$this->db->where('log_type',$log_type);
		$this->db->order_by('id','desc');
		$this->db->limit(10);
		$query = $this->db->get();
		return $query->result_array();
	}


}


