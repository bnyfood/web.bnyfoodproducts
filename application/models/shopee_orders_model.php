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
		return $this->apply_passed_pack_report_filter($query->result_array());


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

	function shopee_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber,$voidtype=2)
	{
		$sql="shopee_select_order_with_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."',".$voidtype;
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		return $this->apply_passed_pack_report_filter($query->result_array());


	}

	function sql_shopee_taxinvoice_src($alias = 'tinv')
	{
		return "(
	SELECT order_sn, taxinvoiceID
	FROM Shopee_taxinvoiceid
	WHERE ISNULL(taxinvoiceID,'') <> ''
) ".$alias;
	}

	function shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate)
	{

    $sql="shopee_select_order_with_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		return $this->apply_passed_pack_report_filter($query->result_array());

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
			return $query->result_array();	
	}

	function shopee_select_order_groupby_Date_by_DateStart_CN($StartDate)
	{

		$sql="shopee_select_order_groupby_Date_by_DateStart_CN '".$StartDate."'";
		//echo $sql; 
		    //sqlsrv_configure("WarningsReturnAsErrors", 0);
			$query = $this->db->query($sql);
			return $query->result_array();	
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
		return $query->result_array();	


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

	function get_after_pack_order_sn_set($order_sns){
		$set = array();
		if (empty($order_sns)) {
			return $set;
		}
		$after = array(
			'READY_TO_SHIP', 'PROCESSED', 'SHIPPED', 'TO_CONFIRM_RECEIVE',
			'COMPLETED', 'TO_RETURN', 'RETURNED'
		);
		$this->db->select('order_sn, order_status');
		$this->db->from('shopee_orders');
		$this->db->where_in('order_sn', $order_sns);
		$this->db->where_in('order_status', $after);
		$query = $this->db->get();
		$rows = $query->result_array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				if (!empty($row['order_sn'])) {
					$set[$row['order_sn']] = true;
				}
			}
		}
		return $set;
	}

	function get_by_sn($order_sn){
		$this->db->select('*');
		$this->db->from('shopee_orders');
		$this->db->where('order_sn',$order_sn);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_tracking_order_sn_set($order_sns){
		$found = array();
		if (empty($order_sns)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_sns)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_sn');
			$this->db->from('shopee_tracking');
			$this->db->where_in('order_sn', $chunk);
			$this->db->where('LEN(LTRIM(RTRIM(ISNULL(tracking_number, \'\')))) >', 0);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_sn']] = true;
				}
			}
		}
		return $found;
	}

	function get_passed_pack_order_sn_set($order_sns){
		$found = array();
		if (empty($order_sns)) {
			return $found;
		}
		$after = array(
			'PROCESSED', 'READY_TO_SHIP', 'RETRY_SHIP', 'SHIPPED', 'TO_CONFIRM_RECEIVE',
			'COMPLETED', 'TO_RETURN', 'RETURNED'
		);
		$chunks = array_chunk(array_values(array_unique($order_sns)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_sn');
			$this->db->from('shopee_orders');
			$this->db->where_in('order_sn', $chunk);
			$this->db->where_in('order_status', $after);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_sn']] = true;
				}
			}
		}
		$tracked = $this->get_tracking_order_sn_set($order_sns);
		if (!empty($tracked)) {
			foreach ($tracked as $sn => $val) {
				$found[$sn] = true;
			}
		}
		return $found;
	}

	function get_prepack_death_order_sn_set($order_sns){
		$found = array();
		if (empty($order_sns)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_sns)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_sn');
			$this->db->from('shopee_orders');
			$this->db->where_in('order_sn', $chunk);
			$this->db->where_in('order_status', array('CANCELLED', 'UNPAID', 'INVOICE_PENDING'));
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_sn']] = true;
				}
			}
		}
		return $found;
	}

	// Path B: CANCELLED after sold (PROCESSED or tax invoice).
	// Path A: COMPLETED + is_return (set only when return REFUND_PAID/COMPLETED).
	function get_cn_eligible_order_sn_set($order_sns){
		$found = array();
		if (empty($order_sns)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_sns)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('b.order_sn');
			$this->db->from('shopee_orders b');
			$this->db->group_start();
			$this->db->where('b.order_status', 'CANCELLED');
			$this->db->or_group_start();
			$this->db->where('b.order_status', 'COMPLETED');
			$this->db->where('b.is_return', 1);
			$this->db->group_end();
			$this->db->group_end();
			$this->db->group_start();
			$this->db->where("EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = b.order_sn AND p.order_status = 'PROCESSED')", null, false);
			$this->db->or_where("EXISTS (SELECT 1 FROM Shopee_taxinvoiceid t WHERE t.order_sn = b.order_sn)", null, false);
			$this->db->group_end();
			$this->db->where_in('b.order_sn', $chunk);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (!empty($row['order_sn'])) {
						$found[$row['order_sn']] = true;
					}
				}
			}
		}
		return $found;
	}

	function _row_order_sn($row){
		if (!empty($row['order_sn'])) {
			return $row['order_sn'];
		}
		if (!empty($row['order_number'])) {
			return $row['order_number'];
		}
		return '';
	}

	function filter_orders_not_passed_pack($arr_orders){
		if (empty($arr_orders)) {
			return $arr_orders;
		}
		$order_sns = array();
		foreach ($arr_orders as $row) {
			$sn = $this->_row_order_sn($row);
			if ($sn !== '') {
				$order_sns[] = $sn;
			}
		}
		if (empty($order_sns)) {
			return $arr_orders;
		}
		$passed = $this->get_passed_pack_order_sn_set($order_sns);
		$death = $this->get_prepack_death_order_sn_set($order_sns);
		$cn = $this->get_cn_eligible_order_sn_set($order_sns);
		$kept = array();
		foreach ($arr_orders as $row) {
			$sn = $this->_row_order_sn($row);
			if ($sn === '') {
				$kept[] = $row;
				continue;
			}
			if (isset($death[$sn]) && !isset($passed[$sn]) && !isset($cn[$sn])) {
				continue;
			}
			$kept[] = $row;
		}
		return $kept;
	}

	function apply_passed_pack_report_filter($rows){
		if (empty($rows)) {
			return NULL;
		}
		$rows = $this->filter_orders_not_passed_pack($rows);
		if (empty($rows)) {
			return NULL;
		}
		return $rows;
	}

	// Any row in shopee_orders for these sns (escrow may still be missing).
	function get_existing_order_sn_set($order_sns){
		$set = array();
		if (empty($order_sns)) {
			return $set;
		}
		$order_sns = array_values(array_unique($order_sns));
		$chunks = array_chunk($order_sns, 800);
		foreach ($chunks as $chunk) {
			$this->db->select('order_sn');
			$this->db->from('shopee_orders');
			$this->db->where_in('order_sn', $chunk);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (!empty($row['order_sn'])) {
						$set[$row['order_sn']] = true;
					}
				}
			}
		}
		return $set;
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
		return $this->apply_passed_pack_report_filter($query->result_array());	
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

	// Check goods = original_cost_of_goods_sold (≈ Excel ราคาขายสุทธิ).
	// Tax report / CN goods = original_price (ราคาก่อนป้ายเหลือง).
	// One order_sn can have several status rows; cancelled escrow often zeros
	// original_price / seller_discount — keep the row with the highest original_price.
	function get_escrow_tax_map_by_order_sns($order_sns){
		$map = array();
		if (empty($order_sns)) {
			return $map;
		}
		$order_sns = array_values(array_unique($order_sns));
		$chunks = array_chunk($order_sns, 800);
		foreach ($chunks as $chunk) {
			$this->db->select("shopee_orders.order_sn,
				shopee_orders.order_status,
				shopee_escrow_detail.EscrowID,
				shopee_escrow_order_income.original_price,
				shopee_escrow_order_income.original_cost_of_goods_sold,
				shopee_escrow_order_income.seller_discount,
				shopee_escrow_order_income.voucher_from_seller,
				shopee_escrow_order_income.voucher_from_shopee,
				shopee_escrow_order_income.coins,
				shopee_escrow_order_income.shopee_discount,
				shopee_escrow_order_income.buyer_total_amount,
				shopee_escrow_order_income.buyer_paid_shipping_fee,
				shopee_escrow_order_income.cost_of_goods_sold,
				ISNULL(shopee_escrow_order_income.original_cost_of_goods_sold,0) as check_taxable", false);
			$this->db->from('shopee_orders');
			$this->db->join('shopee_escrow_detail', 'shopee_orders.OrderID = shopee_escrow_detail.OrderID');
			$this->db->join('shopee_escrow_order_income', 'shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID');
			$this->db->where_in('shopee_orders.order_sn', $chunk);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$sn = $row['order_sn'];
					if ($sn === '') {
						continue;
					}
					if (!isset($map[$sn]) || $this->_escrow_cn_row_score($row) > $this->_escrow_cn_row_score($map[$sn])) {
						$map[$sn] = $row;
					}
				}
			}
		}
		return $map;
	}

	function _escrow_cn_row_score($row)
	{
		$orig = isset($row['original_price']) ? floatval($row['original_price']) : 0;
		$cogs = isset($row['original_cost_of_goods_sold']) ? floatval($row['original_cost_of_goods_sold']) : 0;
		$ship = isset($row['buyer_paid_shipping_fee']) ? floatval($row['buyer_paid_shipping_fee']) : 0;
		$status = isset($row['order_status']) ? strtoupper(trim((string)$row['order_status'])) : '';
		$rank = 5;
		if ($status === 'SHIPPED') {
			$rank = 50;
		} elseif ($status === 'PROCESSED') {
			$rank = 40;
		} elseif (in_array($status, array('READY_TO_SHIP', 'RETRY_SHIP', 'TO_CONFIRM_RECEIVE', 'COMPLETED'), true)) {
			$rank = 30;
		} elseif (in_array($status, array('TO_RETURN', 'RETURNED'), true)) {
			$rank = 10;
		} elseif (in_array($status, array('CANCELLED', 'IN_CANCEL'), true)) {
			$rank = 0;
		}
		$goods = ($orig > $cogs) ? $orig : $cogs;
		if ($goods > 0.00001) {
			return 1000000000.0 + ($orig * 1000000.0) + ($cogs * 1000.0) + $rank;
		}
		return ($rank * 1000.0) + $ship;
	}

	function get_orderitem_original_map_by_order_sns($order_sns)
	{
		$map = array();
		if (empty($order_sns)) {
			return $map;
		}
		$order_sns = array_values(array_unique($order_sns));
		$chunks = array_chunk($order_sns, 800);
		foreach ($chunks as $chunk) {
			$this->db->select("order_sn, SUM(ISNULL(model_original_price,0) * ISNULL(model_quantity_purchased,1)) AS goods", false);
			$this->db->from('shopee_orderitems');
			$this->db->where_in('order_sn', $chunk);
			$this->db->group_by('order_sn');
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (!empty($row['order_sn'])) {
						$map[$row['order_sn']] = floatval($row['goods']);
					}
				}
			}
		}
		return $map;
	}

	function get_escrow_item_original_map_by_escrow_ids($escrow_ids)
	{
		$map = array();
		if (empty($escrow_ids)) {
			return $map;
		}
		$escrow_ids = array_values(array_unique($escrow_ids));
		$chunks = array_chunk($escrow_ids, 800);
		foreach ($chunks as $chunk) {
			$this->db->select("EscrowID, SUM(ISNULL(original_price,0)) AS goods", false);
			$this->db->from('shopee_escrow_items');
			$this->db->where_in('EscrowID', $chunk);
			$this->db->group_by('EscrowID');
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (isset($row['EscrowID']) && $row['EscrowID'] !== '' && $row['EscrowID'] !== null) {
						$map[$row['EscrowID']] = floatval($row['goods']);
					}
				}
			}
		}
		return $map;
	}

	function _fmt_sqlsrv_time($value)
	{
		if ($value instanceof DateTime) {
			return $value->format('Y-m-d H:i:s');
		}
		$text = trim((string)$value);
		if ($text === '') {
			return $text;
		}
		$ts = strtotime($text);
		return $ts ? date('Y-m-d H:i:s', $ts) : $text;
	}

	function _ymd_param($value)
	{
		$ts = strtotime((string)$value);
		return $ts ? date('Y-m-d', $ts) : (string)$value;
	}

	/**
	 * CN issue stamp:
	 * Path B = first CANCELLED.update_time
	 * Path A = first shopee_return_order.end_time at REFUND_PAID/COMPLETED
	 * Requires sold: PROCESSED or tax invoice. CN ⊂ sales report (tax invoice join).
	 */
	function get_first_cn_event_map($StartDate, $EndDate)
	{
		$start = $this->db->escape($this->_ymd_param($StartDate));
		$end = $this->db->escape($this->_ymd_param($EndDate));
		$sql = "
SELECT x.order_sn, x.cn_event_at
FROM (
	SELECT order_sn, MIN(cn_event_at) AS cn_event_at
	FROM (
		SELECT order_sn, update_time AS cn_event_at
		FROM shopee_orders
		WHERE order_status = 'CANCELLED'
		UNION ALL
		SELECT order_sn, end_time AS cn_event_at
		FROM shopee_return_order
		WHERE UPPER(status) IN ('REFUND_PAID', 'COMPLETED')
			AND end_time IS NOT NULL
	) e
	GROUP BY order_sn
) x
INNER JOIN ".$this->sql_shopee_taxinvoice_src('t')." ON t.order_sn = x.order_sn
WHERE CONVERT(date, x.cn_event_at) >= ".$start."
	AND CONVERT(date, x.cn_event_at) <= ".$end."
	AND (
		EXISTS (
			SELECT 1 FROM shopee_orders p
			WHERE p.order_sn = x.order_sn
			AND p.order_status = 'PROCESSED'
		)
		OR EXISTS (
			SELECT 1 FROM Shopee_taxinvoiceid inv
			WHERE inv.order_sn = x.order_sn
		)
	)
ORDER BY x.cn_event_at, x.order_sn
";
		$query = $this->db->query($sql);
		$map = array();
		if (!empty($query)) {
			foreach ($query->result_array() as $row) {
				if (!empty($row['order_sn'])) {
					$map[$row['order_sn']] = $this->_fmt_sqlsrv_time($row['cn_event_at']);
				}
			}
		}
		return $map;
	}

	function get_first_cn_event_for_sns($order_sns)
	{
		$map = array();
		if (empty($order_sns)) {
			return $map;
		}
		$in = array();
		foreach (array_unique($order_sns) as $sn) {
			$sn = trim((string)$sn);
			if ($sn === '') {
				continue;
			}
			$in[] = $this->db->escape($sn);
		}
		if (empty($in)) {
			return $map;
		}
		$sql = "
SELECT order_sn, MIN(cn_event_at) AS cn_event_at
FROM (
	SELECT order_sn, update_time AS cn_event_at
	FROM shopee_orders
	WHERE order_sn IN (".implode(',', $in).")
		AND order_status = 'CANCELLED'
	UNION ALL
	SELECT order_sn, end_time AS cn_event_at
	FROM shopee_return_order
	WHERE order_sn IN (".implode(',', $in).")
		AND UPPER(status) IN ('REFUND_PAID', 'COMPLETED')
		AND end_time IS NOT NULL
) e
GROUP BY order_sn
";
		$query = $this->db->query($sql);
		if (!empty($query)) {
			foreach ($query->result_array() as $row) {
				if (!empty($row['order_sn'])) {
					$map[$row['order_sn']] = $this->_fmt_sqlsrv_time($row['cn_event_at']);
				}
			}
		}
		return $map;
	}

	function stamp_cn_event_on_order_rows($rows)
	{
		if (empty($rows)) {
			return $rows;
		}
		$sns = array();
		foreach ($rows as $row) {
			$sn = '';
			if (!empty($row['order_sn'])) {
				$sn = $row['order_sn'];
			} elseif (!empty($row['order_number'])) {
				$sn = $row['order_number'];
			}
			if ($sn !== '') {
				$sns[] = $sn;
			}
		}
		$map = $this->get_first_cn_event_for_sns($sns);
		if (empty($map)) {
			return $rows;
		}
		foreach ($rows as $i => $row) {
			$sn = '';
			if (!empty($row['order_sn'])) {
				$sn = $row['order_sn'];
			} elseif (!empty($row['order_number'])) {
				$sn = $row['order_number'];
			}
			if ($sn !== '' && !empty($map[$sn])) {
				$rows[$i]['cn_event_at'] = $map[$sn];
				$rows[$i]['updated_at'] = $map[$sn];
			}
		}
		return $rows;
	}

	function _sns_in_sql($order_sns)
	{
		$in = array();
		foreach (array_unique($order_sns) as $sn) {
			$sn = trim((string)$sn);
			if ($sn === '') {
				continue;
			}
			$in[] = $this->db->escape($sn);
		}
		return $in;
	}

	function select_cn_order_items_by_sns($order_sns)
	{
		$in = $this->_sns_in_sql($order_sns);
		if (empty($in)) {
			return array();
		}
		$rows = $this->_select_cn_order_items_by_sns_statuses($in, array('SHIPPED'));
		$got = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				if (!empty($row['order_number'])) {
					$got[$row['order_number']] = true;
				}
			}
		}
		$missing = array();
		foreach ($order_sns as $sn) {
			$sn = trim((string)$sn);
			if ($sn !== '' && empty($got[$sn])) {
				$missing[] = $sn;
			}
		}
		if (!empty($missing)) {
			$in2 = $this->_sns_in_sql($missing);
			$rows2 = $this->_select_cn_order_items_by_sns_statuses($in2, array('PROCESSED', 'READY_TO_SHIP', 'RETRY_SHIP'));
			if (!empty($rows2)) {
				$rows = array_merge($rows, $rows2);
			}
		}
		return $rows;
	}

	function _select_cn_order_items_by_sns_statuses($in_escaped, $statuses)
	{
		if (empty($in_escaped) || empty($statuses)) {
			return array();
		}
		$st = array();
		$ord = array();
		$rank = 1;
		foreach ($statuses as $status) {
			$st[] = $this->db->escape($status);
			$ord[] = "WHEN ".$this->db->escape($status)." THEN ".$rank;
			$rank++;
		}
		$sql = "
SELECT
	v.order_sn AS order_number,
	tinv.taxinvoiceID,
	v.create_time AS created_at,
	v.update_time AS updated_at,
	shopee_escrow_order_income.buyer_paid_shipping_fee AS shipping_fee,
	shopee_escrow_order_income.voucher_from_shopee AS voucher_platform,
	shopee_escrow_order_income.voucher_from_seller AS voucher_seller,
	shopee_escrow_order_income.original_price
		- shopee_escrow_order_income.seller_discount
		- shopee_escrow_order_income.shopee_discount AS price,
	v.order_status,
	shopee_taxinvoice.FullTaxinvoiceID AS FullTaxinvoiceID,
	ISNULL(shopee_taxinvoice.TaxNo, '-') AS TaxNo,
	ISNULL(shopee_taxinvoice.name, '-') AS customer_name,
	ISNULL(shopee_taxinvoice.phone, '-') AS customer_phone,
	ISNULL(shopee_taxinvoice.zip, '-') AS customer_zip,
	ISNULL(address1, '-') AS address1,
	ISNULL(address2, '-') AS address2,
	shopee_orderitems.OrderItemID,
	shopee_orderitems.item_sku AS sku,
	lazada_skumap.ProductName,
	shopee_orderitems.item_name AS name,
	shopee_orderitems.model_discounted_price AS paid_price
FROM shopee_orders_cn_view v
INNER JOIN shopee_orderitems ON v.order_sn = shopee_orderitems.order_sn
INNER JOIN lazada_skumap ON shopee_orderitems.item_sku = lazada_skumap.sku
LEFT OUTER JOIN shopee_taxinvoice ON v.order_sn = shopee_taxinvoice.shopee_orders_OrderID
INNER JOIN shopee_escrow_detail ON v.OrderID = shopee_escrow_detail.OrderID
INNER JOIN shopee_escrow_order_income ON shopee_escrow_detail.EscrowID = shopee_escrow_order_income.EscrowID
INNER JOIN ".$this->sql_shopee_taxinvoice_src('tinv')." ON v.order_sn = tinv.order_sn
WHERE v.order_sn IN (".implode(',', $in_escaped).")
	AND v.OrderID IN (
		SELECT x.OrderID FROM (
			SELECT v2.order_sn, v2.OrderID,
				ROW_NUMBER() OVER (
					PARTITION BY v2.order_sn
					ORDER BY CASE v2.order_status
						".implode(' ', $ord)."
						ELSE 99
					END, v2.update_time
				) AS rn
			FROM shopee_orders_cn_view v2
			WHERE v2.order_sn IN (".implode(',', $in_escaped).")
				AND v2.order_status IN (".implode(',', $st).")
		) x
		WHERE x.rn = 1
	)
ORDER BY v.order_sn, shopee_orderitems.OrderItemID
";
		$query = $this->db->query($sql);
		if (empty($query)) {
			return array();
		}
		return $query->result_array();
	}

	function shopee_select_order_with_orderitems_by_cn_event_date($StartDate, $EndDate)
	{
		$map = $this->get_first_cn_event_map($StartDate, $EndDate);
		if (empty($map)) {
			return array();
		}
		$rows = $this->select_cn_order_items_by_sns(array_keys($map));
		if (empty($rows)) {
			return array();
		}
		foreach ($rows as $i => $row) {
			$sn = isset($row['order_number']) ? $row['order_number'] : '';
			if ($sn !== '' && !empty($map[$sn])) {
				$rows[$i]['cn_event_at'] = $map[$sn];
				$rows[$i]['updated_at'] = $map[$sn];
			}
		}
		return $rows;
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


