<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_orders_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big

function update_order_status($order_number,$status,$data)
	{

		$this->db->where('order_sn', $order_number);
		$this->db->where('order_status', $status);
		$this->db->update('shopee_orders', $data);

		//echo $this->db->last_query();

	}

	function update_by_order_sn($data,$order_sn)
	{

		$this->db->where('order_sn', $order_sn);
		$this->db->update('shopee_orders', $data);

		//echo $this->db->last_query();

	}
	
function delete($id){
		$this->db->where('OrderID',$id);
		$this->db->delete('lazada_orders');
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
	$this->db->from('lazada_orders');
	$this->db->where('OrderID',$id);
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


    $this->db->insert('lazada_orders', $data); 
    echo $this->db->last_query();
    $insert_id =NULL;
    $insert_id = $this->db->insert_id();
    $waiter=0;
    do{
    	$waiter++;
    } while(is_null($insert_id));

    

    
    $insert_id = "";
    $rows =  $this->db->affected_rows();
	    if($rows>0){
	    	$insert_id = $this->db->insert_id();
	    	$is_insert = true;
	    }else{
	      $is_insert = false;
	    }

	  $data = array(
	  	'insert_id' => $insert_id,
	  	'is_insert' => $is_insert
	  );  

	  return $data;

	}

	function insert($data){
    	$this->db->insert('shopee_orders', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_orders', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function count_order()
	{

      $this->db->select('COUNT(*) as count');
      $this->db->from('lazada_orders');
      $query = $this->db->get();
      return $query->row();

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('lazada_orders');
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



	function getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate)
	{

         $sql="select_order_groupby_Date_by_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	

	}

    function getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($taxinvoicetype,$ordernumber=NULL,$search_type=NULL,$StartDate=NULL,$EndDate=NULL,$page=NULL,$paginationsize=NULL)
    {


      

      	switch($taxinvoicetype)
      	{

      		case 1: //all taxinvoicetype
      		$sql="shopee_select_taxinvoice_by_startdate_enddate_taxinvoicetype_all '".$StartDate."','".$EndDate."','".$ordernumber."','".$search_type."','".$page."','".$paginationsize."'";
			break;
		
      		case 2: //ABB taxinvoicetype
      		
      		$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_ABB '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
      		
			break;

			case 3: //Full taxinvoicetype
			
			$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_full '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
      		
			break;
		}
      	
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	

    }

	function getOrderbyDateStartDateEnd($StartDate,$EndDate)
	{

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('created_at>=',$StartDate);
		$this->db->where('created_at<=',$EndDate);
		$this->db->where('status<>','canceled');
		$this->db->order_by("created_at", "asc");

		$query = $this->db->get();
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


function get_order_with_orderitems_by_DateStart_DateEnd($StartDate,$EndDate)
{





	$sql="select_order_with_orderitems_by_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
	//echo $sql; 
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	


}

function select_orders_with_modify_total_Between_Start_End_Date($StartDate,$EndDate)
	{
		$sql="select_orders_with_modify_total_Between_Start_End_Date '".$StartDate."','".$EndDate."'";
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
		else
			{
				return NULL;
			}	


	}

	function shopee_select_order_with_ordersn($order_sn)
	{
		$sql="shopee_select_order_with_ordersn '".$order_sn."'";
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
		else
			{
				return NULL;
			}	


	}


	function select_latest_record()
	{

		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('shopee_orders');
		$this->db->order_by("OrderID", "desc");
				
		$query = $this->db->get();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->row();
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
		$this->db->from('shopee_orders');
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

	function shopee_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="shopee_select_order_with_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
		else
			{
				return NULL;
			}	


	}

	function shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate)
	{

    $sql="shopee_select_order_with_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	

	}

	function get_next_sn(){
		$this->db->select('OrderID,order_sn,OrderListID');
		$this->db->from('shopee_orders');
		$this->db->where('OrderID not in (select OrderID from shopee_escrow_detail) and OrderID>23180');
		$this->db->order_by('OrderID','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		//return $query->row();
	}

	function get_next_sn_dup(){
		$this->db->select("OrderID,order_sn,OrderListID,order_status,create_time,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('shopee_orders');
		$this->db->where('OrderID not in (select OrderID from shopee_escrow_detail)');
		$this->db->order_by('OrderID','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->row_array();
		//return $query->row();
	}

	function getby_sn_status($sn){
		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$sn);
		//$this->db->where(" (order_status = 'COMPLETED' or order_status='CANCELLED') ");
		$this->db->where('order_status','CANCELLED');
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}


	function shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate)
	{

		$sql="shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN '".$StartDate."','".$EndDate."'";
		//echo $sql; 
		    //sqlsrv_configure("WarningsReturnAsErrors", 0);
			$query = $this->db->query($sql);
			if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
			else
			{
				return NULL;
			}	
	}

	function shopee_select_order_groupby_Date_by_DateStart_CN($StartDate)
	{

		$sql="shopee_select_order_groupby_Date_by_DateStart_CN '".$StartDate."'";
		//echo $sql; 
		    //sqlsrv_configure("WarningsReturnAsErrors", 0);
			$query = $this->db->query($sql);
			if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
			else
			{
				return NULL;
			}	
	}

	function select_next_invoice($limit){
		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('taxinvoiceID','');
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', create_time),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) )AS bigint) asc");
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function last_order_code($create_time,$OrderID){
		$this->db->select('*');
		$this->db->from('shopee_orders');
		//$this->db->where("create_time < CONVERT(datetime,'".$create_time."',121)" );
		$this->db->where("CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', create_time),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) ) < CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', '".$create_time."'),RIGHT('0000000'+CAST(ISNULL('".$OrderID."',0) AS VARCHAR),7) )" );
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', create_time),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) )AS bigint) desc");
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function update_by_id($data,$id){
    	$this->db->where('OrderID',$id);
		$this->db->update('shopee_orders',$data);
		//echo $this->db->last_query();
	}

	function shopee_select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="shopee_select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
		else
			{
				return NULL;
			}	


	}

	function shopee_manage_duplicates_orders()
	{

         $sql="shopee_manage_duplicates_orders";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	

	}

	function select_status_by_sn($arr_order_sn){
		$this->db->select('order_sn,order_status');
		$this->db->from('shopee_orders');
		$this->db->where_in("order_sn",$arr_order_sn);
		$query = $this->db->get();
		return $query->result_array();
	}

	function shopee_select_order_with_OrdernoStart_OrderEnd($order_start,$order_end){

		$sql="shopee_select_order_with_OrdernoStart_OrderEnd '".$order_start."','".$order_end."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}	
	}

	function select_chk_date()
	{
        $this->db->select('DATEDIFF(day, create_time, getdate()) as cntdate');
		$this->db->from('shopee_orders');
		$this->db->order_by('create_time','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		
		return $query->row_array();
	    
	}

	function select_chk_date_escrow()
	{
        $this->db->select('DATEDIFF(day, create_time, getdate()) as cntdate');
		$this->db->from('shopee_orders');
		$this->db->join('shopee_escrow_detail', 'shopee_orders.OrderID = shopee_escrow_detail.OrderID');
		$this->db->order_by('create_time','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		
		return $query->row_array();
	    
	}

	function get_by_status_in($order_sn,$arr_status){

		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$this->db->where_in('order_status',$arr_status);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_by_sn_status($order_sn,$status){

		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$this->db->where('order_status',$status);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function get_by_not_status($order_sn,$status){

		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$this->db->where('order_status <>',$status);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_by_sn_status_one($order_sn,$status){

		$this->db->select("*,FORMAT (update_time, 'dd/MM/yyyy ') as shipdate");
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$this->db->where('order_status',$status);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
		
	}

	function get_order_not_die(){

		$sql = "select top 100 *,DATEDIFF(minute, update_time, getdate()) AS date_to_now from shopee_orders a where a.order_status in ('SHIPPED', 'TO_CONFIRM_RECEIVE','PROCESSED','READY_TO_SHIP','UNPAID')  
				and (select count(*) from shopee_orders where order_sn = a.order_sn and order_status in ('COMPLETED','CANCELLED')) = 0
				order by update_time asc";	
					
	  		$query = $this->db->query($sql);
			return $query->result_array();

	}

	function get_orderno_tracking($top){

		$sql = "  select top ".$top." a.order_sn from (
    select top 100 order_sn as order_sn from shopee_orders where order_sn not in (select order_sn from shopee_tracking)   order by OrderID asc 
	) as a group by a.order_sn";

	  		$query = $this->db->query($sql);
			return $query->result_array();

	} 

	function get_by_order_sn($order_sn){

		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function get_by_order_sn_price($order_sn){

		$this->db->select('*,(shopee_escrow_order_income.original_cost_of_goods_sold+shopee_escrow_order_income.buyer_paid_shipping_fee-(shopee_escrow_order_income.voucher_from_shopee+shopee_escrow_order_income.coins+shopee_escrow_order_income.voucher_from_seller)) as priceVATincluded');
		$this->db->from('shopee_orders');
		$this->db->join('shopee_escrow_detail', 'shopee_orders.OrderID = shopee_escrow_detail.OrderID');
		$this->db->join('shopee_escrow_order_income', 'shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID');
		$this->db->where('shopee_orders.order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	
	function delete_shopee_order_by_year_month($year_month){

		$sql="delete_shopee_order_by_year_month '".$year_month."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);

	}

	function select_by_status_last_arr($arr_status,$limit){

		$this->db->select("*,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('shopee_orders');
		$this->db->where_not_in('order_status',$arr_status);
		$this->db->where('order_sn not in (select order_sn from Shopee_taxinvoiceid_v2)');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");
		$this->db->order_by('create_time','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();

	}


}


