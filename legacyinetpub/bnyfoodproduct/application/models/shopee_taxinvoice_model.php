<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_taxinvoice_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_taxinvoice', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('ShoTaxinvoiceID',$id);
		$this->db->update('shopee_taxinvoice',$data);
		//echo $this->db->last_query();
	}

	function update_by_order_id($data,$order_sn){
    	$this->db->where('shopee_orders_sn',$order_sn);
		$this->db->update('shopee_taxinvoice',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('ShoTaxinvoiceID',$id);
		$this->db->delete('shopee_taxinvoice');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('shopee_taxinvoice');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('shopee_taxinvoice');
		$this->db->where('ShoTaxinvoiceID',$id);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_order_id($order_sn){
		$this->db->select('*');
		$this->db->from('shopee_taxinvoice');
		$this->db->where('shopee_orders_sn',$order_sn);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function last_order_code($created_at,$OrderID){
		$this->db->select('*');
		$this->db->from('shopee_taxinvoice');
		$this->db->where("CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', shopee_order_date),RIGHT('0000000'+CAST(ISNULL(shopee_orders_OrderID,0) AS VARCHAR),7) ) < CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', '".$created_at."'),RIGHT('0000000'+CAST(ISNULL('".$OrderID."',0) AS VARCHAR),7) )" );
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', shopee_order_date),RIGHT('0000000'+CAST(ISNULL(shopee_orders_OrderID,0) AS VARCHAR),7) )AS bigint) desc");
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

}


