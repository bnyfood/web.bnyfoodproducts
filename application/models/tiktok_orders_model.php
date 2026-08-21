<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Tiktok_orders_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('tiktok_orders', $data);
    	//echo $this->db->last_query();
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('tiktok_orders_id',$id);
		$this->db->update('tiktok_orders',$data);
		//echo $this->db->last_query();
	}

	function update_order_status($order_id,$status,$data)
	{

		$this->db->where('order_id', $order_id);
		$this->db->where('status', $status);
		$this->db->update('tiktok_orders', $data);

		//echo $this->db->last_query();

	}

	function delete($id){
		$this->db->where('tiktok_orders_id',$id);
		$this->db->delete('tiktok_orders');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('tiktok_orders');
		$this->db->where('tiktok_orders_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('tiktok_orders');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_order_id($order_id){

		$this->db->select('*');
		$this->db->from('tiktok_orders');
		$this->db->where('order_id',$order_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_id_status($order_id,$status){

		$this->db->select('*');
		$this->db->from('tiktok_orders');
		$this->db->where('order_id',$order_id);
		$this->db->where('status',$status);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function next_order_by_date(){
		$this->db->select('*');
		$this->db->from('tiktok_orders');
		$this->db->order_by('create_time','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function tiktok_select_order_with_DateStart_DateEnd($StartDate,$EndDate){

		$sql="tiktok_select_order_with_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		return $this->apply_passed_pack_report_filter($query->result_array());
		
	}


	function select_by_status_last_arr_v1($arr_status,$limit){

		$this->db->select("*,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('tiktok_orders');
		$this->db->where_in('status',$arr_status);
		$this->db->where('order_id not in (select order_id from tiktok_taxinvoiceid)');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");
		$this->db->order_by('create_time','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();

	}

	function tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate)
	{

		$sql="tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN '".$StartDate."','".$EndDate."'";
		//echo $sql; 
		    //sqlsrv_configure("WarningsReturnAsErrors", 0);
			$query = $this->db->query($sql);
			return $query->result_array();	
	}

	function select_by_status_last_arr_V2($arr_status,$limit){

		$this->db->select("*,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('tiktok_orders');
		//$this->db->where_not_in('status',$arr_status);
		$this->db->where('order_id not in (select order_id from tiktok_taxinvoiceid)');
		$this->db->where('tracking_number <>','');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");

		$this->db->order_by('create_time','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_status_last_arr($arr_status,$limit){

		$sql = "select top 50 *,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm 
		from tiktok_orders a where a.tiktok_orders_id > 1004004 
		and a.order_id not in (select order_id from tiktok_taxinvoiceid) 
		and (select count(*) from tiktok_orders 
		where order_id = a.order_id and status in ('CANCELLED','Canceled')) = 0 
		";	
					
	  		$query = $this->db->query($sql);
			echo $this->db->last_query();
			return $query->result_array();

	}

	function tiktok_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber,$voicetype=2)
	{
		$sql="tiktok_select_order_with_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."',".$voicetype;
		//echo $sql; 
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		return $this->apply_passed_pack_report_filter($query->result_array());	


	}

	function tiktok_select_order_with_OrderStart_OrderEnd($order_start,$order_end)
	{

		$sql="tiktok_select_order_with_OrderStart_OrderEnd '".$order_start."','".$order_end."'";
		//echo $sql; 
		    //sqlsrv_configure("WarningsReturnAsErrors", 0);
			$query = $this->db->query($sql);
			return $this->apply_passed_pack_report_filter($query->result_array());	
	}

	function get_order_not_die(){

		$sql = "select top 20 *,DATEDIFF(minute, update_time, getdate()) AS date_to_now 
		from tiktok_orders a 
		where a.status in ('Packet', 'Shipped')  
		and (select count(*) from tiktok_orders where order_id = a.order_id 
		and status in ('Completed','CANCELLED','Canceled','IN_TRANSIT','DELIVERED')) = 0 
		and a.update_time <> '' 
		and DATEDIFF(minute, update_time, getdate()) < 10080
		";	
					
	  		$query = $this->db->query($sql);
			return $query->result_array();

	}

	function delete_tiktok_order_by_year_month($year_month){

		$sql="delete_tiktok_order_by_year_month '".$year_month."'";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);

	}

	// Any historical status at/after pack for these order_ids (tax point = pack).
	function get_after_pack_order_id_set($order_ids){
		$set = array();
		if (empty($order_ids)) {
			return $set;
		}
		$after = array(
			'AWAITING_SHIPMENT', 'AWAITING_COLLECTION', 'READY_TO_SHIP',
			'IN_TRANSIT', 'DELIVERED', 'COMPLETED', 'Completed',
			'Packet', 'Shipped', 'SHIPPED', 'TO_CONFIRM_RECEIVE'
		);
		$order_ids = array_values(array_unique($order_ids));
		$chunks = array_chunk($order_ids, 800);
		foreach ($chunks as $chunk) {
			$this->db->select('order_id, status');
			$this->db->from('tiktok_orders');
			$this->db->where_in('order_id', $chunk);
			$this->db->where_in('status', $after);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (!empty($row['order_id'])) {
						$set[$row['order_id']] = true;
					}
				}
			}
		}
		return $set;
	}

	function get_tracking_order_id_set($order_ids){
		$found = array();
		if (empty($order_ids)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_ids)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_id');
			$this->db->from('tiktok_orders');
			$this->db->where_in('order_id', $chunk);
			$this->db->where('LEN(LTRIM(RTRIM(ISNULL(tracking_number, \'\')))) >', 0);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_id']] = true;
				}
			}
			$this->db->distinct();
			$this->db->select('order_id');
			$this->db->from('tiktok_line_items');
			$this->db->where_in('order_id', $chunk);
			$this->db->where('LEN(LTRIM(RTRIM(ISNULL(tracking_number, \'\')))) >', 0);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_id']] = true;
				}
			}
		}
		return $found;
	}

	function get_passed_pack_order_id_set($order_ids){
		$found = array();
		if (empty($order_ids)) {
			return $found;
		}
		$after = array(
			'AWAITING_SHIPMENT', 'AWAITING_COLLECTION', 'READY_TO_SHIP',
			'IN_TRANSIT', 'DELIVERED', 'COMPLETED', 'Completed',
			'Shipped', 'SHIPPED', 'TO_CONFIRM_RECEIVE', 'PARTIALLY_SHIPPING'
		);
		$chunks = array_chunk(array_values(array_unique($order_ids)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_id');
			$this->db->from('tiktok_orders');
			$this->db->where_in('order_id', $chunk);
			$this->db->where_in('status', $after);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_id']] = true;
				}
			}
		}
		$tracked = $this->get_tracking_order_id_set($order_ids);
		if (!empty($tracked)) {
			foreach ($tracked as $oid => $val) {
				$found[$oid] = true;
			}
		}
		return $found;
	}

	function get_prepack_death_order_id_set($order_ids){
		$found = array();
		if (empty($order_ids)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_ids)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('order_id');
			$this->db->from('tiktok_orders');
			$this->db->where_in('order_id', $chunk);
			$this->db->where_in('status', array('CANCELLED', 'Canceled', 'UNPAID', 'ON_HOLD'));
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					$found[$row['order_id']] = true;
				}
			}
		}
		return $found;
	}

	// Same join as dbo.tiktok_orders_cn_date_latest: Packet + cancelled.
	function get_cn_eligible_order_id_set($order_ids){
		$found = array();
		if (empty($order_ids)) {
			return $found;
		}
		$chunks = array_chunk(array_values(array_unique($order_ids)), 500);
		foreach ($chunks as $chunk) {
			$this->db->distinct();
			$this->db->select('a.order_id');
			$this->db->from('tiktok_orders a');
			$this->db->join('tiktok_orders b', 'a.order_id = b.order_id');
			$this->db->join('tiktok_order_payment d', 'a.order_id = d.order_id');
			$this->db->where('a.status', 'Packet');
			$this->db->where_in('b.status', array('CANCELLED', 'Canceled'));
			$this->db->where_in('a.order_id', $chunk);
			$query = $this->db->get();
			$rows = $query->result_array();
			if (!empty($rows)) {
				foreach ($rows as $row) {
					if (!empty($row['order_id'])) {
						$found[$row['order_id']] = true;
					}
				}
			}
		}
		return $found;
	}

	function _row_order_id($row){
		if (!empty($row['order_id'])) {
			return $row['order_id'];
		}
		if (!empty($row['order_number'])) {
			return $row['order_number'];
		}
		if (!empty($row['order_sn'])) {
			return $row['order_sn'];
		}
		return '';
	}

	function filter_orders_not_passed_pack($arr_orders){
		if (empty($arr_orders)) {
			return $arr_orders;
		}
		$order_ids = array();
		foreach ($arr_orders as $row) {
			$oid = $this->_row_order_id($row);
			if ($oid !== '') {
				$order_ids[] = $oid;
			}
		}
		if (empty($order_ids)) {
			return $arr_orders;
		}
		$passed = $this->get_passed_pack_order_id_set($order_ids);
		$death = $this->get_prepack_death_order_id_set($order_ids);
		$cn = $this->get_cn_eligible_order_id_set($order_ids);
		$kept = array();
		foreach ($arr_orders as $row) {
			$oid = $this->_row_order_id($row);
			if ($oid === '') {
				$kept[] = $row;
				continue;
			}
			if (isset($death[$oid]) && !isset($passed[$oid]) && !isset($cn[$oid])) {
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


}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */