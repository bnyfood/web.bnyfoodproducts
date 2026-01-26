<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class Lazada_finance_transaction_details_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
function delete($id){
		$this->db->where('TransactionID',$id);
		$this->db->delete('lazada_finance_transaction_details');
	}

function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('lazada_finance_transaction_details');
	}

function getlastElement($str,$spliter)
{

  $arr=exlode($spliter,$str);
  $num=count($arr);
  return $arr[$num-1];

}

function select_by_id($id){
	$this->db->select('*');
	$this->db->from('lazada_finance_transaction_details');
	$this->db->where('TransactionID',$id);
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


    $this->db->insert('lazada_finance_transaction_details', $data); 
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
		$this->db->insert('lazada_finance_transaction_details', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;

	}


	function insert_all($arr_data){
		$this->db->insert_batch('lazada_finance_transaction_details', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function count_order()
	{

      $this->db->select('COUNT(*) as count');
      $this->db->from('lazada_finance_transaction_details');
      $query = $this->db->get();
      return $query->row();

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('lazada_finance_transaction_details');
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

    function getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($taxinvoicetype,$ordernumber=NULL,$StartDate=NULL,$EndDate=NULL,$page=NULL,$paginationsize=NULL)
    {


      if($ordernumber!=NULL)
      {

      	switch($taxinvoicetype)
      	{

      		case 1: //all taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders');
			$this->db->where('order_number=',$ordernumber);
			break;
		
      		case 2: //ABB taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders');
			$this->db->where('order_number=',$ordernumber);
			break;

			case 3: //Full taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders');
			$this->db->where('order_number=',$ordernumber);
			break;
		}
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
      else
      {
      	switch($taxinvoicetype)
      	{

      		case 1: //all taxinvoicetype
      		$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_all '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
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

	function select_order_with_orderitems_by_DateStart_DateEnd_SearchType($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="select_order_with_orderitems_by_DateStart_DateEnd_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
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

		$this->db->select('convert(varchar,convert(varchar,DATEADD(DAY, 1, transaction_date),23)) as start,convert(varchar,convert(varchar,DATEADD(DAY, 2, transaction_date),23)) as next_day');
		$this->db->limit(1);
		$this->db->from('lazada_finance_transaction_details');
		$this->db->order_by("transaction_date", "desc");
				
		$query = $this->db->get();
		return $query->row_array();
	    

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




$this->db->where('TransactionID', $order_number);
$this->db->update('lazada_finance_transaction_details', $data);


	}

	function select_order_lazada_last_null($id){
		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('TransactionID',$id);
		$this->db->where('order_number',NULL);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_order_lazada_last_null_bk(){
		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
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

	function cnt_by_date($yymmdd){
		$this->db->select('count(*) as cntdate');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where("convert(varchar,transaction_date, 23) = '".$yymmdd."'");
		$query = $this->db->get();
		return $query->row_array();
	}

	function del_by_date($yymmdd){
		$this->db->where("convert(varchar,transaction_date, 23) = '".$yymmdd."'");
		$this->db->delete('lazada_finance_transaction_details');
	}

	function select_by_payment_id($payout_id){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where("TransactionID",$payout_id);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_all_search($arr_search){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->like("statement",$arr_search['statement_search']);
		$query = $this->db->get();
		return $query->result_array();

	}

	function update($data,$id){
    	$this->db->where('TransactionID',$id);
		$this->db->update('lazada_finance_transaction_details',$data);
		//echo $this->db->last_query();
	}

	function update_order_number($data,$order_number){
    	$this->db->where('order_number',$order_number);
		$this->db->update('lazada_finance_transaction_details',$data);
		//echo $this->db->last_query();
	}

	function get_statement(){
		$sql="select_payout_detail_statement ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function get_tran_nopaid($days,$limit){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('paid_status','Not paid');
		$this->db->where("DATEDIFF(day, transaction_date, getdate()) < ".$days);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_error_order($arr_search,$arr_feename){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('paid_status','Paid');
		$this->db->where_in('fee_name',$arr_feename);
		$this->db->limit(20);
		//$this->db->where("DATEDIFF(day, transaction_date, getdate()) < ".$days);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_by_trandate($datesearch){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('transaction_date',$datesearch);
		$query = $this->db->get();
		return $query->result_array();

	} 

	function get_order_by_trandate($datesearch){

		$this->db->select('order_number');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('transaction_date',$datesearch);
		$this->db->group_by('order_number');
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_order_by_trandate_all($datesearch){

		$this->db->select('*,DATEDIFF(day, transaction_date, getdate()) as out_date');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('transaction_date',$datesearch);
		$this->db->order_by('order_number');
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_by_orderno($order_number){

		$this->db->select('*,DATEDIFF(day, transaction_date, getdate()) as out_date');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('order_number',$order_number);
		$query = $this->db->get();
		return $query->result_array();

	} 

	function get_last_nopaid($api_round){

		$this->db->select('*,convert(varchar, transaction_date, 23) as start');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('paid_status','Not paid');
		$this->db->where('is_finish',0);
		$this->db->where('api_round < '.$api_round);
		$this->db->order_by('transaction_date','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();

	}

	function get_last_nopaid_limit($limit,$api_round){

		$this->db->select('*,convert(varchar, transaction_date, 23) as start');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('paid_status','Not paid');
		$this->db->where('is_finish',0);
		$this->db->where('api_round < '.$api_round);
		$this->db->order_by('transaction_date','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_orderno_nopaid($limit,$api_round){

		$this->db->select('order_number');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('api_round < '.$api_round);
		$this->db->where('is_finish',1);
		//$this->db->order_by('transaction_date','asc');
		$this->db->group_by('order_number');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_by_date($tran_date){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('transaction_date',$tran_date);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_by_orderno_paid($order_number){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('order_number',$order_number);
		$this->db->where('paid_status','Paid');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();

	}

	function select_by_orderno_no_paid($order_number){

		$this->db->select('*,convert(varchar, transaction_date, 23) as start');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('order_number',$order_number);
		$this->db->order_by('TransactionID','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();

	}

	function select_by_status_fee_amount($order_no,$fee_name,$amount,$paid_status){

		$this->db->select('*');
		$this->db->from('lazada_finance_transaction_details');
		$this->db->where('order_number',$order_no);
		$this->db->where('fee_name',$fee_name);
		$this->db->where('amount',$amount);
		$this->db->where('paid_status',$paid_status);
		$query = $this->db->get();
		return $query->result_array();
	}

}


