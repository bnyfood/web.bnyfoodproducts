<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class tiktok_taxinvoiceid_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('tiktok_taxinvoiceid', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('tiktok_taxinvoiceid_id',$id);
		$this->db->update('tiktok_taxinvoiceid',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('tiktok_taxinvoiceid_id',$id);
		$this->db->delete('tiktok_taxinvoiceid');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('tiktok_taxinvoiceid');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('tiktok_taxinvoiceid');
			$this->db->where('tiktok_taxinvoiceid_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function last_order_code(){
		$this->db->select('*');
			$this->db->from('tiktok_taxinvoiceid');
			$this->db->order_by('cdate','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
			$this->db->from('tiktok_taxinvoiceid');
			$this->db->where("FORMAT ( [create_time] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->order_by('create_time desc,order_id desc');
			$this->db->limit(1);
			$query = $this->db->get();
			//echo $this->db->last_query();
			return $query->row_array();
	}

	function select_taxinvoiceid_by_orderno($order_id){

		$this->db->select('*');
		$this->db->from('tiktok_taxinvoiceid');
		$this->db->where('order_id',$order_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_taxinvoiceid_id($textinvoiceID){

		$this->db->select('*');
		$this->db->from('tiktok_taxinvoiceid');
		$this->db->where('taxinvoiceID',$textinvoiceID);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		//return $query->row();

	}

}


