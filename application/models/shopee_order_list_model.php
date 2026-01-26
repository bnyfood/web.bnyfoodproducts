<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_order_list_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big


	
function delete($id){
		$this->db->where('OrderListID',$id);
		$this->db->delete('shopee_order_list');
	}

function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('shopee_order_list');
	}

function update($data,$id){
    	$this->db->where('OrderListID',$id);
		$this->db->update('shopee_order_list',$data);
		//echo $this->db->last_query();
	}	

	function update_by_order_sn($data,$order_sn){
    	$this->db->where('order_sn',$order_sn);
		$this->db->update('shopee_order_list',$data);
		//echo $this->db->last_query();
	}

function update_status_complete(){
	$data = array(
		'is_death_status' => 1
	);
	$this->db->where("order_sn in (select order_sn from shopee_orders where order_status in ('COMPLETED','CANCELLED') group by order_sn)");
	$this->db->update('shopee_order_list',$data);
	//echo $this->db->last_query();
}

function update_status_complete_by_no($arr_order_sn){

		$data = array(
			'is_death_status' => 1
		);

    	$this->db->where_in('order_sn',$arr_order_sn);
		$this->db->update('shopee_order_list',$data);
		//echo $this->db->last_query();
	}

function getlastElement($str,$spliter)
{

  $arr=exlode($spliter,$str);
  $num=count($arr);
  return $arr[$num-1];

}

function select_by_id($id){
	$this->db->select('*');
	$this->db->from('shopee_order_list');
	$this->db->where('OrderListID',$id);
	$query = $this->db->get();
	return $query->row_array();
}

function select_by_escrow_last(){
	$this->db->select('*');
	$this->db->from('shopee_order_list');
	$this->db->where('is_escrow_dowloaded',0);
	$this->db->limit(1);
	$this->db->order_by('OrderListID','asc');
	$query = $this->db->get();
	return $query->row_array();
}

function getdeliveryInfo($arr)
{
$num=count($arr["statuses"]);
  return $arr["statuses"][$num-1];


}

	function insertOrder($data)
	{


    $this->db->insert('shopee_order_list', $data); 
    

	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_order_list', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function count_order()
	{

      $this->db->select('COUNT(*) as count');
      $this->db->from('shopee_order_list');
      $query = $this->db->get();
      return $query->row();

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('shopee_order_list');
      $this->db->where('keygen',$keygen);
      $query = $this->db->get();
      return $query->row_array();
	}
	
	function getorder_no_suborders($rowlimit)
	{

         


		//$this->db->select('*');
		//$this->db->limit($rowlimit);
		//$this->db->from('lazada_orders');
		//$this->db->where('suborderinsereted','0');
		//$this->db->order_by("created_at", "asc");
				
		//$query = $this->db->get();

         $sql="select_order_no_sub ".$rowlimit;
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);

		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->result_array();
	    }


	}



	function select_order_list_top_numrows_order_by_OrderListID($numrows)
	{
        $this->db->select('*');
		$this->db->limit($numrows);
		$this->db->from('shopee_order_list');
		//$this->db->where('OrderListID > (select ISNULL(MAX(OrderListID),0) from shopee_orders)');
		$this->db->where('is_death_status=0');
		$this->db->order_by("OrderListID", "ASC");
				
		$query = $this->db->get();

		//echo $this->db->last_query();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->result_array();
	    }


	}

	function select_order_list_top_numrows_order_by_OrderListID_tmp($numrows)
	{
        $this->db->select('*');
		$this->db->limit($numrows);
		$this->db->from('shopee_order_list_tmp');
		$this->db->where('OrderListID > (select ISNULL(MAX(OrderListID),0) from shopee_orders)');
		$this->db->where('is_death_status=0');
		$this->db->order_by("OrderListID", "ASC");
				
		$query = $this->db->get();

		//echo $this->db->last_query();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->result_array();
	    }


	}



	function select_latest_record()
	{

		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('shopee_order_list');
		$this->db->order_by("OrderListID", "desc");
				
		$query = $this->db->get();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->row('OrderListID');
	    }

	}

    function update_logintoken_by_id_encrypted_token($token_id,$encrypted_token)
    {

    $sql="update_token_by_tokenid ".$token_id.",'".$encrypted_token."'";
    $this->db->query($sql);    

    }

function select_adminUsers_by_email_password($email,$password){
		$this->db->select('*');
		$this->db->from('AdminUsers');
		$this->db->where('email',$email);
		$this->db->where('password',$password);
		$query = $this->db->get();
		//return $query->row_array();
		return $query->row();
}	

function validate_token($token)
{

       $this->db->select('*');
       $this->db->from('logintoken');
       $this->db->where('tokenid',$token);


}


function select_token_by_id_and_encrypted_token($tokenid,$encrypted_token)
{

	$sql="check_encrypted_token ".$tokenid.",'".$encrypted_token."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->row_array()))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}	
}


function delete_token_by_tokenid($tokenid)
{

	$sql="delete_token_by_tokenid ".$tokenid;
	$this->db->query($sql);
}

function exten_token($token)
	{


        $sql="exten_encrypted_token ".$token;
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);

	}

	function update_order($order_number,$data)
	{




$this->db->where('order_number', $order_number);
$this->db->update('lazada_orders', $data);


	}

	function select_order_lazada_last_null($id){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('OrderID',$id);
		$this->db->where('order_number',NULL);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_order_lazada_last_null_bk(){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',NULL);
		$this->db->order_by('OrderID','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	
	function get_last_taxinvoiceID(){

		$sql = "select CONCAT('Laz',datepart(yyyy,(select top 1 created_at from lazada_orders order by OrderID desc)),
				FORMAT(datepart(m,(select top 1 created_at from lazada_orders order by OrderID desc )),'00')) as code,
				(select top 1 SUBSTRING(taxinvoiceID,13,5) from lazada_orders order by OrderID desc ) as taxinvoiceID";

	  		$query = $this->db->query($sql);
			return $query->row_array();

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

	function select_by_ordersn($order_sn){
		$this->db->select('*');
		$this->db->from('shopee_order_list');
		$this->db->where('order_sn',$order_sn);
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_yymmdd($yymmdd){

		$sql = "select * from shopee_order_list where LEFT(order_sn,6) = '".$yymmdd."'";

	  		$query = $this->db->query($sql);
			return $query->result_array();

	} 


}


