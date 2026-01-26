<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Lazada_finance_transaction_payout_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 function insert($data){
    	$this->db->insert('lazada_finance_transaction_payout', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('TransactionID',$id);
		$this->db->update('lazada_finance_transaction_payout',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('TransactionID',$id);
		$this->db->delete('lazada_finance_transaction_payout');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('lazada_finance_transaction_payout');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('lazada_finance_transaction_payout');
			$this->db->where('TransactionID',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function insert_all($arr_data){
		$this->db->insert_batch('lazada_finance_transaction_payout', $arr_data); 

	}

	function select_latest_record(){

		$this->db->select('convert(varchar,convert(varchar,created_at,23)) as start');
		$this->db->limit(1);
		$this->db->from('lazada_finance_transaction_payout');
		$this->db->order_by("created_at", "desc");
		$query = $this->db->get();
		return $query->row_array();
	    
	}

	function get_by_number($statement_number){
			$this->db->select('*');
			$this->db->from('lazada_finance_transaction_payout');
			$this->db->where('statement_number',$statement_number);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}

	function select_by_keygen($keygen){

		$this->db->select('COUNT(*) as cnt');
      	$this->db->from('lazada_finance_transaction_payout');
      	$this->db->where('keygen',$keygen);
      	$query = $this->db->get();
      	return $query->row_array();
	}

	function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('lazada_finance_transaction_payout');
	}

	function get_all_search($arr_search){
		$sql="select_payout_search '".$arr_search['statenumber_search']."'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function get_statement_number(){
		$sql="select_payout_statement_number ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function get_payout_by_date($datesearch){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_payout');
		$this->db->where("convert(date,convert(varchar, created_at, 23)) = '".$datesearch."'");
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		

	}

}


