<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_prep_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('lazada_prep', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('lazada_prep_id',$id);
		$this->db->update('lazada_prep',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('lazada_prep_id',$id);
		$this->db->delete('shopee_prep');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('lazada_prep');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_order_sn($order_sn){
		$this->db->select('*');
		$this->db->from('lazada_prep');
		$this->db->where('order_number',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_prep_join_by_orderno(){

		$sql = "SELECT *, lazada_prep.order_number as order_sn_s FROM lazada_prep LEFT OUTER JOIN lazada_prep_api ON (lazada_prep.order_number = lazada_prep_api.order_number) where lazada_prep.status = 'confirmed'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_prep_join_by_orderno_status_code($code){

		$sql = "SELECT *, lazada_prep.order_number as order_sn_s FROM lazada_prep_api LEFT OUTER JOIN lazada_prep ON (lazada_prep.order_number = lazada_prep_api.order_number) where lazada_prep_api.code = '".$code."'";

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function select_by_code($code){
		$this->db->select('*');
		$this->db->from('lazada_prep');
		$this->db->where('code',$code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_prep_join_by_orderno_code($code){

		$sql = "SELECT lazada_prep.order_number as order_sn_s,
				ISNULL(lazada_prep.taxable, lazada_prep.paid_price) as paid_price,
				ISNULL(lazada_prep.taxable, lazada_prep.paid_price) as taxable,
				lazada_prep.bucket, lazada_prep.status, lazada_prep.initiator, lazada_prep.cancel_reason,
				lazada_prep_api.price, lazada_prep_api.voucher_seller, lazada_prep_api.voucher, lazada_prep_api.shipping_fee,
				ISNULL(lazada_prep_api.taxable, ISNULL(lazada_prep_api.price,0) - ISNULL(lazada_prep_api.voucher_seller,0)) as api_taxable
			FROM lazada_prep
			LEFT OUTER JOIN lazada_prep_api ON (lazada_prep.order_number = lazada_prep_api.order_number AND lazada_prep_api.code = lazada_prep.code)
			WHERE lazada_prep.code = ?";

		$query = $this->db->query($sql, array($code));
		return $query->result_array();
	}

	function select_by_complete($code,$arr_status){
		$this->db->select('sum(paid_price)+sum(shippingFee) as sum_sale');
		$this->db->from('lazada_prep');
		$this->db->where('code',$code);
		$this->db->where_not_in('status',$arr_status);
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
		//return $query->row();
	}	

	function select_by_complete_all($code){
		$this->db->select('sum(paid_price) as sum_sale');
		$this->db->from('lazada_prep');
		$this->db->where('code',$code);
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
		//return $query->row();
	}	

	function select_by_cn($code,$arr_status){
		$this->db->select('sum(paid_price)+sum(shippingFee) as sum_logis_cn');
		$this->db->from('lazada_prep');
		$this->db->where('code',$code);
		$this->db->where_in('status',$arr_status);
		$query = $this->db->get();
		return $query->row_array();
		//echo $this->db->last_query();
		//return $query->row();
	}
}


