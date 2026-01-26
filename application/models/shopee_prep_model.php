<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_prep_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('shopee_prep', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('shopee_prep_id',$id);
		$this->db->update('shopee_prep',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('shopee_prep_id',$id);
		$this->db->delete('shopee_prep');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('shopee_prep');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_sn($order_sn){
		$this->db->select('*');
		$this->db->from('shopee_prep');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_complete($code){
		$this->db->select('sum(paid_price) as sum_sale');
		$this->db->from('shopee_prep');
		$this->db->where('code',$code);
		$this->db->where('status <> ','ยกเลิกแล้ว');
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
		//return $query->row();
	}	

	function select_by_cancel($code){
		$this->db->select('sum(cn_paid_price) as sum_cn,sum(logistic_price) as sum_logis_cn');
		$this->db->from('shopee_prep');
		$this->db->where('code',$code);
		$this->db->where('status','ยกเลิกแล้ว');
		$this->db->where('paid_price',0);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_retuen($code){
		$this->db->select('sum(cn_paid_price) as sum_cn_return');
		$this->db->from('shopee_prep');
		$this->db->where('code',$code);
		$this->db->where('status','สำเร็จแล้ว');
		$this->db->where('paid_price',0);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	


	function select_prep_join_by_orderno(){
		$this->db->select('shopee_prep.order_sn as order_sn_s,*');
		$this->db->from('shopee_prep');
		$this->db->join('shopee_prep_api', 'shopee_prep.order_sn = shopee_prep_api.order_sn','left outer');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
		//return $query->row();
	}	

	function select_prep_join_by_orderno_v2(){

		$sql = "SELECT *, shopee_prep.order_sn as order_sn_s FROM shopee_prep LEFT OUTER JOIN shopee_prep_api ON (shopee_prep.order_sn = shopee_prep_api.order_sn)";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_prep_join_by_orderno_v3(){

		$sql = "SELECT *, shopee_prep.order_sn as order_sn_s FROM shopee_prep LEFT OUTER JOIN shopee_prep_api ON (shopee_prep.order_sn = shopee_prep_api.order_sn)";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_prep_join_by_orderno_code($code){

		$sql = "SELECT *, shopee_prep.order_sn as order_sn_s FROM shopee_prep LEFT OUTER JOIN shopee_prep_api ON (shopee_prep.order_sn = shopee_prep_api.order_sn) where shopee_prep.code = '".$code."'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_prep_join_by_orderno_code_v1($code){

		$sql = "SELECT *, shopee_prep.order_sn as order_sn_s FROM shopee_prep LEFT OUTER JOIN shopee_prep_api ON (shopee_prep.order_sn = shopee_prep_api.order_sn) where shopee_prep.code = '".$code."' and shopee_prep_api.code = '".$code."'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}


}


