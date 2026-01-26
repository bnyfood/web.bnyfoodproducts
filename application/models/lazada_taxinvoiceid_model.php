<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_taxinvoiceid_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('lazada_taxinvoiceid', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('lazada_taxinvoiceid_id',$id);
		$this->db->update('lazada_taxinvoiceid',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('lazada_taxinvoiceid_id',$id);
		$this->db->delete('lazada_taxinvoiceid');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('lazada_taxinvoiceid');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('lazada_taxinvoiceid');
			$this->db->where('lazada_taxinvoiceid_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function last_order_code(){
		$this->db->select('*');
			$this->db->from('lazada_taxinvoiceid');
			$this->db->order_by('cdate','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
			$this->db->from('lazada_taxinvoiceid');
			$this->db->where("FORMAT ( [created_at] , 'yyyy-MM' ) = '".$yymm."'");
			$this->db->order_by('lazada_taxinvoiceid_id','desc');
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->row_array();
	}

	function select_taxinvoiceid_by_orderno($order_no){

		$this->db->select('*');
		$this->db->from('lazada_taxinvoiceid');
		$this->db->where('order_number',$order_no);
		$query = $this->db->get();
		return $query->row_array();
	}

}


