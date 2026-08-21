<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_order_download_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big


	
function delete($id){
		$this->db->where('OrderDownLoadID',$id);
		$this->db->delete('shopee_order_download');
	}

function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('lazada_orders');
	}

function getlastElement($str,$spliter)
{

  $arr=exlode($spliter,$str);
  $num=count($arr);
  return $arr[$num-1];

}

function select_by_id($id){
	$this->db->select('*');
	$this->db->from('shopee_order_download');
	$this->db->where('OrderDownLoadID',$id);
	$query = $this->db->get();

	return $query->row_array();
}





	function insert_order_download($data)
	{

    $this->db->insert('shopee_order_download', $data); 
    echo $this->db->last_query();

	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_order_download', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function count_record()
	{

      $this->db->select('COUNT(*) as count');
      $this->db->from('shopee_order_download');
      $query = $this->db->get();
      return $query->row();

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('shopee_order_download');
      $this->db->where('keygen',$keygen);
      $query = $this->db->get();
      return $query->row_array();
	}
	
	

	function select_latest_record_job()
	{

		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('shopee_order_download');
		$this->db->where('downloaded',0);
		$this->db->order_by("OrderDownLoadID", "desc");
				
		$query = $this->db->get();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->row_array();
	    }

	}

    

	function update_shopee_order_download($OrderDownLoadID,$data)
	{




$this->db->where('OrderDownLoadID', $OrderDownLoadID);
$this->db->update('shopee_order_download', $data);


	}

	

	function last_order(){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('status','delivered');
		$this->db->order_by('OrderID','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_by_yymm($yymm){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_sum_by_month($yymm){
		$this->db->select('ISNULL(sum(price)+sum(shipping_fee)-sum(voucher),0) as summonth');
		$this->db->from('lazada_orders');
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$query = $this->db->get();
		return $query->row_array();
	}


}


