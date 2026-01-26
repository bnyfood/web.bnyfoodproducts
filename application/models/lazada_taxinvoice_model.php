<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Lazada_taxinvoice_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
		    
    function insert($data){
    	$this->db->insert('lazada_taxinvoice', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('lazada_taxinvoice',$data);
		//echo $this->db->last_query();
	}
	
	function update_by_order_id($data,$order_id){
    	$this->db->where('lazada_orders_OrderID',$order_id);
		$this->db->update('lazada_taxinvoice',$data);
	}

	function update_by_order_no($data,$order_no){
    	$this->db->where('lazada_orders_number',$order_no);
		$this->db->update('lazada_taxinvoice',$data);
	}

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('lazada_taxinvoice');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('lazada_taxinvoice');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_order_id($order_id){
			$this->db->select('*');
			$this->db->from('lazada_taxinvoice');
			$this->db->where('lazada_orders_number',$order_id);
			$query = $this->db->get();
			//echo $this->db->last_query();
			return $query->row_array();
			//return $query->row();
	}	

	function select_bnycode($order_number){
		$sql = "SELECT top 1 CONCAT('BNY',datepart(yyyy,getdate()),FORMAT(datepart(m,getdate()),'00'),
				FORMAT((select count(*)+1 from lazada_taxinvoice WHERE FullTaxinvoiceID IS NOT NULL AND lazada_orders_OrderID <> ".$order_number." ),'00000')) as bnycode
  				FROM lazada_taxinvoice order by LazTaxinvoiceID desc";

  		$query = $this->db->query($sql);
		return $query->row_array();
				
		}

	function last_order_code($created_at,$OrderID){
		$this->db->select('*');
		$this->db->from('lazada_taxinvoice');
		$this->db->where("CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', lazada_order_date),RIGHT('0000000'+CAST(ISNULL(lazada_orders_OrderID,0) AS VARCHAR),7) ) < CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', '".$created_at."'),RIGHT('0000000'+CAST(ISNULL('".$OrderID."',0) AS VARCHAR),7) )" );
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', lazada_order_date),RIGHT('0000000'+CAST(ISNULL(lazada_orders_OrderID,0) AS VARCHAR),7) )AS bigint) desc");
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}


}