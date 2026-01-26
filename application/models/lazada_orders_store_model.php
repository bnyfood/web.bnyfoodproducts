<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_orders_store_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	

	function insert($data){
    	$this->db->insert('lazada_orders_store', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}

	function insert_all($arr_data){
    	$this->db->insert_batch('lazada_orders_store', $arr_data); 
    	//echo $this->db->last_query();
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('lazada_orders_store',$data);
		//echo $this->db->last_query();
	}
	
	function update_by_order_id($data,$order_id){
    	$this->db->where('OrderID',$order_id);
		$this->db->update('lazada_orders_store',$data);
	}

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('lazada_orders_store');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('lazada_orders_store');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function delete_from_lastid($lastid,$yymm){
		$this->db->where('OrderID > ',$lastid);
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$this->db->delete('lazada_orders_store');
		//echo $this->db->last_query();
	}

	function delete_yymm($yymm){
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$this->db->delete('lazada_orders_store');
	}

	function get_old_order_yymm($yymm){
		$this->db->select('order_number');
		$this->db->from('lazada_orders_store');
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$this->db->group_by('order_number');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function get_old_order_yymm_one($yymm){
		$this->db->select('order_number');
		$this->db->from('lazada_orders_store');
		$this->db->where("convert(varchar(7), created_at, 126) = '".$yymm."'");
		$this->db->group_by('order_number');
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function get_order_with_orderitems_by_DateStart_DateEnd($StartDate,$EndDate)
	{
		$sql="select_store_order_with_orderitems_by_DateStart_DateEnd '".$StartDate."','".$EndDate."'";
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

	function getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($taxinvoicetype,$ordernumber=NULL,$StartDate=NULL,$EndDate=NULL,$page=NULL,$paginationsize=NULL)
    {


      if($ordernumber!=NULL)
      {

      	switch($taxinvoicetype)
      	{

      		case 1: //all taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders_store');
			$this->db->where('order_number=',$ordernumber);
			break;
		
      		case 2: //ABB taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders_store');
			$this->db->where('order_number=',$ordernumber);
			break;

			case 3: //Full taxinvoicetype
      		$this->db->select('*');
			$this->db->from('lazada_orders_store');
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
      		$sql="select_taxinvoice_by_startdate_enddate_taxinvoicetype_store_all '".$StartDate."','".$EndDate."',".$page.",".$paginationsize;
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

    function getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate)
	{

         $sql="select_order_groupby_Date_by_DateStart_DateEnd_store '".$StartDate."','".$EndDate."'";
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



}


