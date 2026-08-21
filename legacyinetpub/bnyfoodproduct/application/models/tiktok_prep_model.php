<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class tiktok_prep_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('tiktok_prep', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('tiktok_prep_id',$id);
		$this->db->update('tiktok_prep',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('tiktok_prep_id',$id);
		$this->db->delete('tiktok_prep');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('tiktok_prep');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_sn($order_sn){
		$this->db->select('*');
		$this->db->from('tiktok_prep');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_complete($code){
		$this->db->select('sum(paid_price) as sum_sale');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->where('status <>','Canceled');
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
		//return $query->row();
	}	

	function select_by_cancel($code){
		$this->db->select('sum(cn_paid_price) as sum_cn,sum(logistic_price) as sum_logis_cn');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->where('status','Canceled');
		//$this->db->where('paid_price',0);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_retuen($code){
		$this->db->select('sum(cn_paid_price) as sum_cn_return');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->where('status','สำเร็จแล้ว');
		$this->db->where('paid_price',0);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	


	function select_prep_join_by_orderno(){
		$this->db->select('tiktok_prep.order_sn as order_sn_s,*');
		$this->db->from('tiktok_prep');
		$this->db->join('tiktok_prep_api', 'tiktok_prep.order_sn = tiktok_prep_api.order_id','left outer');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
		//return $query->row();
	}	


	function select_prep_join_by_orderno_code($code){

		$sql = "SELECT *, tiktok_prep.order_sn as order_sn_s FROM tiktok_prep LEFT OUTER JOIN tiktok_prep_api ON (tiktok_prep.order_sn = tiktok_prep_api.order_id) where tiktok_prep.code = '".$code."'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}




}


