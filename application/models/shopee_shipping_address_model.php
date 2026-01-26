<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_shipping_address_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big


	
function delete($id){
		$this->db->where('OrderID',$id);
		$this->db->delete('shopee_shipping_address');
	}

function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('shopee_shipping_address');
	}

function getlastElement($str,$spliter)
{

  $arr=exlode($spliter,$str);
  $num=count($arr);
  return $arr[$num-1];

}

function select_by_id($id){
	$this->db->select('*');
	$this->db->from('shopee_shipping_address');
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

	function insert_all($arr_data){
		$this->db->insert_batch('shopee_shipping_address', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function count_order()
	{

      $this->db->select('COUNT(*) as count');
      $this->db->from('shopee_shipping_address');
      $query = $this->db->get();
      return $query->row();

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('shopee_shipping_address');
      $this->db->where('keygen',$keygen);
      $query = $this->db->get();
      return $query->row_array();
	}
	
	



	function select_latest_record()
	{

		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('shopee_shipping_address');
		$this->db->order_by("ShipingAddressID", "desc");
				
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

   

	function update_shipping_address($order_sn,$data)
	{




$this->db->where('order_sn', $order_sn);
$this->db->update('shopee_shipping_address', $data);


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


}


