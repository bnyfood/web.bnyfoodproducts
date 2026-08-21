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

	function select_by_code($code){
		$this->db->select('*');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_complete($code){
		$this->db->select('sum(ISNULL(taxable, paid_price)) as sum_sale');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->group_start();
		$this->db->where('bucket','tax');
		$this->db->or_where('bucket','cn');
		$this->db->or_group_start();
		$this->db->where('bucket IS NULL', null, false);
		$this->db->where('status <>','Canceled');
		$this->db->group_end();
		$this->db->group_end();
		$query = $this->db->get();
		return $query->row_array();
	}	

	function select_by_cancel($code){
		$this->db->select('sum(ISNULL(taxable, cn_paid_price)) as sum_cn,sum(logistic_price) as sum_logis_cn');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->group_start();
		$this->db->where('bucket','cn');
		$this->db->or_group_start();
		$this->db->where('bucket IS NULL', null, false);
		$this->db->where('status','Canceled');
		$this->db->group_end();
		$this->db->group_end();
		$query = $this->db->get();
		return $query->row_array();
	}	

	function select_by_retuen($code){
		$this->db->select('sum(ISNULL(taxable, cn_paid_price)) as sum_cn_return');
		$this->db->from('tiktok_prep');
		$this->db->where('code',$code);
		$this->db->where('bucket','cn');
		$query = $this->db->get();
		return $query->row_array();
	}	


	function select_prep_join_by_orderno(){
		$this->db->select('tiktok_prep.order_sn as order_sn_s,*');
		$this->db->from('tiktok_prep');
		$this->db->join('tiktok_prep_api', 'tiktok_prep.order_sn = tiktok_prep_api.order_id','left outer');
		$query = $this->db->get();
		return $query->result_array();
	}	


	function select_prep_join_by_orderno_code($code){

		$sql = "SELECT
				tiktok_prep.tiktok_prep_id,
				tiktok_prep.order_sn as order_sn_s,
				tiktok_prep.order_date,
				tiktok_prep.status,
				tiktok_prep.cancel_type,
				tiktok_prep.cancel_reason,
				tiktok_prep.bucket,
				ISNULL(tiktok_prep.taxable, tiktok_prep.paid_price) as paid_price,
				ISNULL(tiktok_prep.taxable, tiktok_prep.paid_price) as taxable,
				tiktok_prep.unit_price,
				tiktok_prep.seller_discount,
				tiktok_prep.shipping_fee,
				tiktok_prep.code,
				tiktok_prep_api.order_id,
				tiktok_prep_api.transactiondate,
				tiktok_prep_api.price,
				tiktok_prep_api.voucher_seller,
				tiktok_prep_api.voucher_platform,
				tiktok_prep_api.voucher,
				tiktok_prep_api.shipping_fee as api_shipping_fee,
				tiktok_prep_api.priceVATincluded,
				ISNULL(
					tiktok_prep_api.taxable,
					ISNULL(tiktok_prep_api.priceVATincluded,
						ISNULL(tiktok_prep_api.price,0) - ISNULL(tiktok_prep_api.voucher_seller, ISNULL(tiktok_prep_api.voucher,0))
					)
				) as api_taxable
			FROM tiktok_prep
			LEFT OUTER JOIN tiktok_prep_api
			  ON (tiktok_prep.order_sn = tiktok_prep_api.order_id AND tiktok_prep.code = tiktok_prep_api.code)
			WHERE tiktok_prep.code = ?";

		$query = $this->db->query($sql, array($code));
		return $query->result_array();
	}




}


