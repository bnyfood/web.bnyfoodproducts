<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_taxinvoiceid_v2_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_taxinvoiceid_v2', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('shopee_taxinvoiceid_v2_id',$id);
		$this->db->update('shopee_taxinvoiceid_v2',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('shopee_taxinvoiceid_v2_id',$id);
		$this->db->delete('shopee_taxinvoiceid_v2');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('shopee_taxinvoiceid_v2');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('shopee_taxinvoiceid_v2');
			$this->db->where('shopee_taxinvoiceid_v2_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function last_order_code(){
		$this->db->select('*');
			$this->db->from('shopee_taxinvoiceid_v2');
			$this->db->order_by('cdate','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
			$this->db->from('shopee_taxinvoiceid_v2');
			$this->db->where("FORMAT ( [create_time] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->order_by('create_time','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function select_by_sn($order_sn){
		$this->db->select('*');
			$this->db->from('shopee_taxinvoiceid_v2');
			$this->db->where('order_sn',$order_sn);
			$query = $this->db->get();
			return $query->result_array();
	}

	function select_taxinvoiceid_by_orderno($order_sn){

		$this->db->select('*');
		$this->db->from('shopee_taxinvoiceid_v2');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->row_array();
	}

}


