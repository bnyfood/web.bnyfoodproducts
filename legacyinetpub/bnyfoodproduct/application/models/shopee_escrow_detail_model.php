<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_escrow_detail_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_escrow_detail', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_escrow_detail', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}
	
	
	function update($data,$id){
    	$this->db->where('EscrowID',$id);
		$this->db->update('shopee_escrow_detail',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('EscrowID',$id);
		$this->db->delete('shopee_escrow_detail');
	}

	function del_by_order_sn($order_sn){
		$this->db->where('order_sn',$order_sn);
		$this->db->delete('shopee_escrow_detail');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shopee_escrow_detail');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shopee_escrow_detail');
			$this->db->where('EscrowID',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	

}


