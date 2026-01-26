<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_orders_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
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

function select_cnt_all(){

	$this->db->select('count(*) as cnt_orders');
	$this->db->from('lazada_orders');
	$query = $this->db->get();
	return $query->row_array();
}

function update_by_id($data,$id){
    	$this->db->where('OrderID',$id);
		$this->db->update('lazada_orders',$data);
		//echo $this->db->last_query();
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
    	$this->db->insert('lazada_orders', $data);
    	//echo $this->db->last_query();
    	//$insert_id = $this->db->insert_id();
    	//return $insert_id;
	}

	function insert_all($arr_data){
		$this->db->insert_batch('lazada_orders', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//echo "---->".$rows;
		//return $rows;

	}

	function insert_batch($arr_data){
		$this->db->insert_batch('lazada_orders', $arr_data); 
		echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//echo "---->".$rows;
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

	function getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate)
	{

        $sql="select_order_groupby_Date_by_DateStart_DateEnd_CN '".$StartDate."','".$EndDate."'";

        // echo $sql;
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

	function getOrderbyDateStartDateEndGroupbyDateCn_date($StartDate)
	{

         $sql="select_order_groupby_Date_by_DateStart_CN '".$StartDate."'";

        // echo $sql;
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

	function select_order_groupby_Date_by_DateStart_DateEnd_CN_STATUS($status,$StartDate,$EndDate)
	{

         $sql="select_order_groupby_Date_by_DateStart_DateEnd_CN_STATUS '".$status."','".$StartDate."','".$EndDate."'";
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
      		$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_all '".$StartDate."','".$EndDate."','".$ordernumber."','".$search_type."','".$page."','".$paginationsize."'";
			break;
		
      		case 2: //ABB taxinvoicetype
      		
      		$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_ABB '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
      		
			break;

			case 3: //Full taxinvoicetype
			
			$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_full '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
      		
			break;
		}
		//echo $sql;
      	
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

	function select_order_with_orderitems_by_Ordernumber($ordernumber)
	{
		$sql="select_order_with_orderitems_by_Ordernumber '".$ordernumber."'";
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

	function select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
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
		$this->db->from('lazada_orders');
		$this->db->order_by("created_at", "desc");
				
		$query = $this->db->get();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->row('created_at');
	    }

	}


	function select_count_record_by_month_yesr($datein)
	{
        $this->db->select('count(*) as total_rows');
		$this->db->from('lazada_orders');
		$this->db->where(" datediff(m,'".$datein."',created_at)=0");

		$query = $this->db->get();
		
		return $query->row('total_rows');
	    



	}

	function select_latest_record_by_month_year($datestart)
	{
        
		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('lazada_orders');
		$this->db->where(" datediff(m,'".$datestart."',created_at)=0");

		$this->db->order_by("created_at", "desc");
				
		$query = $this->db->get();
		echo $this->db->last_query();
		$rowcount = $query->num_rows();
        if($rowcount==0)
        {
         return NULL;
        }
        else
        {
		return $query->row('created_at');
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

	function update_order_status($order_number,$status,$data)
	{

		$this->db->where('order_number', $order_number);
		$this->db->where('status', $status);
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

	function select_next_invoice($limit){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('taxinvoiceID','');
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', created_at),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) )AS bigint) asc");
		//$this->db->order_by('created_at','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function last_order_code($created_at,$OrderID){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where("CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', created_at),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) ) < CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', '".$created_at."'),RIGHT('0000000'+CAST(ISNULL('".$OrderID."',0) AS VARCHAR),7) )" );
		//$this->db->where("created_at < CONVERT(datetime,'".$created_at."',121)" );
		$this->db->order_by("CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', created_at),RIGHT('0000000'+CAST(ISNULL(OrderID,0) AS VARCHAR),7) )AS bigint) desc");
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function first_order(){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->order_by('OrderID','asc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_order_groupby_orderno($order_start,$order_end)
	{

    $sql="select_order_groupby_orderno '".$order_start."','".$order_end."'";
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

	function get_next_download_time(){
		/*$sql = "select max(created_at) from lazada_orders
				where created_at<(
				select min(created_at) from lazada_orders
				where status in ('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship'))";*/
		/*$sql = "select max(created_at) as last_cdate from lazada_orders
				where created_at<(
				select min(created_at)
				from lazada_orders as a
				where (select count(*)  from lazada_orders
				where order_number = a.order_number and status in ('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled')) = 0
				and
				(select count(*) from lazada_orders
				where order_number = a.order_number and status in ('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship'))>0)";		*/

			$sql = "select max(created_at) as last_cdate from lazada_orders
				where created_at<(
				select min(created_at)
				from lazada_orders as a
				where (select count(*)  from lazada_orders
				where order_number = a.order_number and status in ('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled', 'ready_to_ship', 'shipped','topack','toship')) = 0
				and
				(select count(*) from lazada_orders
				where order_number = a.order_number and status in ('unpaid', 'pending'))>0)";	
					
	  		$query = $this->db->query($sql);
			return $query->row_array();


		/*$this->db->select('max(created_at) as last_cdate');
		$this->db->from('lazada_orders');
		$this->db->where("created_at<(
				select min(created_at) from lazada_orders
				where status in ('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship'))");
		$query = $this->db->get();
		return $query->row_array();		*/

	}

	function get_next_download_time_last_order(){

		$this->db->select('created_at as last_cdate');
		$this->db->from('lazada_orders');
		$this->db->order_by('created_at','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_by_status($order_sn){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_by_status_order_date($order_sn){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$this->db->order_by('updated_at','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->result_array();
	}


	function get_by_status_in_arr_limit($arr_status,$limit){

		$this->db->select('*,DATEDIFF(minute, updated_at, getdate()) AS date_to_now');
		$this->db->from('lazada_orders');
		$this->db->where_in('status',$arr_status);
		$this->db->order_by('updated_at','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_oeder_ready_to_ship(){

		$sql = "select top 20 *,DATEDIFF(minute, updated_at, getdate()) AS date_to_now from lazada_orders a where a.status in ('ready_to_ship')  
				and (select count(*) from lazada_orders where order_number = a.order_number and status in ('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled')) = 0
				order by updated_at asc";	
					
	  		$query = $this->db->query($sql);
			return $query->result_array();

	}

	function get_oeder_not_die(){
		//$array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship','packed');
		//$array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed');
		$sql = "select *,DATEDIFF(minute, updated_at, getdate()) AS date_to_now from lazada_orders a where a.status in ('unpaid','ready_to_ship', 'pending', 'shipped','topack','toship','packed')  
				and (select count(*) from lazada_orders where order_number = a.order_number and status in ('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed')) = 0
				order by updated_at asc";	
					
	  		$query = $this->db->query($sql);
			return $query->result_array();

	}


	function get_by_status_in_arr($arr_status){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where_in('status',$arr_status);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_by_status_in($order_sn,$arr_status){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$this->db->where_in('status',$arr_status);
		$query = $this->db->get();
		return $query->result_array();
	}

	

	function get_by_sn_status_one($order_sn,$status){

		$this->db->select("*,FORMAT (updated_at, 'dd/MM/yyyy ') as shipdate");
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$this->db->where('status',$status);
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
		
	}

	function get_by_sn_status($order_sn,$status){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$this->db->where('status',$status);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}



	function get_by_not_status($order_sn,$status){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_sn);
		$this->db->where('status <>',$status);
		$query = $this->db->get();
		return $query->result_array();
		
	}

	function next_order_by_date(){
		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->order_by('created_at','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_status_last($status,$limit){

		$this->db->select("*,FORMAT ( [updated_at] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('lazada_orders');
		$this->db->where('status',$status);
		$this->db->where('order_number not in (select order_number from lazada_taxinvoiceid)');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");
		$this->db->order_by('updated_at','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_by_status_last_arr($arr_status,$limit){

		$this->db->select("*,FORMAT ( [created_at] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('lazada_orders');
		$this->db->where_in('status',$arr_status);
		$this->db->where('order_number not in (select order_number from lazada_taxinvoiceid)');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");
		$this->db->order_by('created_at','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_chk_date()
	{
        $this->db->select('DATEDIFF(day, created_at, getdate()) as cntdate');
		$this->db->from('lazada_orders');
		$this->db->order_by('created_at','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		
		return $query->row_array();
	    

	}

	function query_run($sql){

	  	$query = $this->db->query($sql,true);
		//return $query->row_array();

	} 

	function get_orderno_tracking($top){

		//$sql = "select top ".$top." order_number from lazada_orders  where order_number not in (select order_number from lazada_tracking) group by order_number";

		$sql = "  select top ".$top." a.order_number from (
    select top 100 order_number as order_number from lazada_orders where order_number not in (select order_number from lazada_tracking)   order by OrderID asc 
	) as a group by a.order_number";

	  		$query = $this->db->query($sql);
			return $query->result_array();

	} 
	function get_orderno_by_date(){

		$sql = "  select  order_number from lazada_orders
where convert(date,convert(varchar, lazada_orders.created_at, 23))>='2023-03-01' 
and convert(date,convert(varchar, lazada_orders.created_at, 23))<='2023-03-31'
group by order_number";

	  		$query = $this->db->query($sql);
			return $query->result_array();
	}

	function get_orderno_by_date_status(){

		$sql = "  select order_number from lazada_orders
where 
status = 'delivered'
and convert(date,convert(varchar, lazada_orders.created_at, 23))>='2023-03-01' 
and convert(date,convert(varchar, lazada_orders.created_at, 23))<='2023-03-31'
group by order_number";

	  		$query = $this->db->query($sql);
			return $query->result_array();
	}

	function select_by_orderno($order_no){

		$this->db->select('*');
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_no);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_taxinvoiceid_by_orderno($order_no){

		$this->db->select('*');
		$this->db->from('lazada_taxinvoiceid');
		$this->db->where('order_number',$order_no);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_orderno_price($order_no){

		$this->db->select('order_number,status,price-voucher_seller as lprice',false);
		$this->db->from('lazada_orders');
		$this->db->where('order_number',$order_no);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_time_holiday($order_date,$order_cut_time,$year){

		$sql = "select *,
				DATEDIFF(minute, CONCAT(DATEADD(day, -1, start_date), ' ".$order_cut_time."'), '".$order_date."') AS DateDiff,
				DATEDIFF(day, '".$order_date."',stop_date) AS min_to_add,
				DATEDIFF(day, DATEADD(day, -1, start_date), '".$order_date."')  as start_holiday,
				DATEDIFF(day, stop_date, '".$order_date."')  as stop_holiday
				from bny_holiday

				where 
				year = '".$year."' 
				and DATEDIFF(minute, CONCAT(DATEADD(day, -1, start_date), ' ".$order_cut_time."'), '".$order_date."') > 0 
				and DATEDIFF(day, DATEADD(day, -1, start_date), '".$order_date."') >= 0 
				and DATEDIFF(day, stop_date, '".$order_date."') <= 0";	
					
	  		$query = $this->db->query($sql);
			return $query->row_array();

	}

	function delete_lazada_order_by_year_month($year_month){

		$sql="delete_lazada_order_by_year_month '".$year_month."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);

	}

}


