<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Lazada_finance_transaction_map_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 function insert($data){
    	$this->db->insert('lazada_finance_transaction_map', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('lazada_finance_transaction_map',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('lazada_finance_transaction_map');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('lazada_finance_transaction_map');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('lazada_finance_transaction_map');
			$this->db->where('id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function insert_all($arr_data){
		$this->db->insert_batch('lazada_finance_transaction_map', $arr_data); 

	}

	function select_payout_with_sum_finance(){
		$sql="select_payout_with_sum_finance ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_payout_finance_compare_orders($StartDate,$EndDate,$statement){
		$sql="select_payout_finance_compare_orders '".$StartDate."','".$EndDate."','".$statement."'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

}


