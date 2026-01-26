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
		if(!empty($query->result_array()))
		{
			return $query->result_array();
		}
		else
		{
			return NULL;
		}
		
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
			if(!empty($query->result_array()))
			{
				return $query->result_array();
			}
			else
			{
				return NULL;
			}	
	}

	function select_by_status_last_arr($arr_status,$limit){

		$this->db->select("*,FORMAT ( [create_time] , 'yyyy-MM' ) as yyyymm");
		$this->db->from('tiktok_orders');
		//$this->db->where_not_in('status',$arr_status);
		$this->db->where('order_id not in (select order_id from tiktok_taxinvoiceid)');
		$this->db->where('tracking_number <>','');
		//$this->db->where("CONVERT(VARCHAR(7), created_at, 126) = '2021-10'");

		$this->db->order_by('create_time','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function tiktok_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="tiktok_select_order_with_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
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

	function tiktok_select_order_with_OrderStart_OrderEnd($order_start,$order_end)
	{

		$sql="tiktok_select_order_with_OrderStart_OrderEnd '".$order_start."','".$order_end."'";
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

	function get_order_not_die(){

		$sql = "select top 100 *,DATEDIFF(minute, update_time, getdate()) AS date_to_now from tiktok_orders a where a.status in ('Packet', 'Shipped')  and (select count(*) from tiktok_orders where order_id = a.order_id and status in ('Completed','CANCELLED','Canceled')) = 0
				order by update_time asc";	
					
	  		$query = $this->db->query($sql);
			return $query->result_array();

	}


}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */