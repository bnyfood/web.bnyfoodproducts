<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_taxinvoiceid_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('Shopee_taxinvoiceid', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('Shopee_taxinvoiceid_id',$id);
		$this->db->update('Shopee_taxinvoiceid',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('Shopee_taxinvoiceid_id',$id);
		$this->db->delete('Shopee_taxinvoiceid');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('Shopee_taxinvoiceid');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('Shopee_taxinvoiceid');
			$this->db->where('Shopee_taxinvoiceid_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function last_order_code(){
		$this->db->select('*');
			$this->db->from('Shopee_taxinvoiceid');
			$this->db->order_by('cdate','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
			$this->db->from('Shopee_taxinvoiceid');
			$this->db->where("FORMAT ( [create_time] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->order_by('taxinvoiceID','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			echo $this->db->last_query();
			return $query->row_array();
	}

	function last_order_code_by_yymm_notnull($yymm){
		$this->db->select('*');
			$this->db->from('Shopee_taxinvoiceid');
			$this->db->where("FORMAT ( [create_time] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->where('taxinvoiceID <>','');
			$this->db->order_by('taxinvoiceID','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			echo $this->db->last_query();
			return $query->row_array();
	}

	function select_by_sn($order_sn){
		$this->db->select('*');
		$this->db->from('Shopee_taxinvoiceid');
			$this->db->where('order_sn',$order_sn);
			$query = $this->db->get();
			return $query->result_array();
	}

	function select_taxinvoiceid_by_orderno($order_sn){

		$this->db->select('*');
		$this->db->from('Shopee_taxinvoiceid');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_by_null_id($limit){

		$this->db->select('*');
		$this->db->from('Shopee_taxinvoiceid');
		$this->db->where('Shopee_taxinvoiceid_id > ','107475');
		$this->db->where('taxinvoiceID','');
		$this->db->order_by('create_time','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
	}

}


