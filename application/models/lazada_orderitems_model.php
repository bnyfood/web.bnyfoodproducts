<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_orderitems_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
function select_by_order_number($order_number){
	$this->db->select('*');
	$this->db->from('lazada_orderitems');
	$this->db->where('order_number',$order_number);
	$query = $this->db->get();
	return $query->result_array();
}

function select_by_order_number_cnmake($order_number,$orderitem_make_cn){
	$this->db->select('*');
	$this->db->from('lazada_orderitems');
	$this->db->where('order_number',$order_number);
	$this->db->where('orderitem_make_cn',$orderitem_make_cn);
	$query = $this->db->get();
	return $query->result_array();
}

function getlastElement($str,$spliter)
{

  $arr=exlode($spliter,$str);
  $num=count($arr);
  return $arr[$num-1];

}



function getdeliveryInfo($arr)
{
$num=count($arr["statuses"]);
  return $arr["statuses"][$num-1];


}

	function insertOrderitems($data)
	{

	    $this->db->insert('lazada_orderitems', $data); 

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
		print_r($arr_data);
		$this->db->insert_batch('lazada_orderitems', $arr_data); 
		//echo $this->db->last_query();
		//$rows =  $this->db->affected_rows();
		//return $rows;

	}

	function select_by_keygen($keygen){
		$this->db->select('COUNT(*) as cnt');
      $this->db->from('lazada_orderitems');
      $this->db->where('keygen',$keygen);
      $query = $this->db->get();
      return $query->row_array();
	}

	function update($data,$id){
    	$this->db->where('OrderItemID',$id);
		$this->db->update('lazada_orderitems',$data);
		//echo $this->db->last_query();
	}

	function delete_by_keygen($keygen){
		$this->db->where('keygen',$keygen);
		$this->db->delete('lazada_orderitems');
	}

	function delete($id){
		$this->db->where('OrderItemID',$id);
		$this->db->delete('lazada_orderitems');
	}

	function delete_order_number($order_number){
		$this->db->where('order_number',$order_number);
		$this->db->delete('lazada_orderitems');
	}

	function select_order_item_lazada_last_null($id){
		$this->db->select('*');
		$this->db->from('lazada_orderitems');
		$this->db->where('OrderItemID',$id);
		$this->db->where('order_number',NULL);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_order_item_lazada_last_null_bk(){
		$this->db->select('*');
		$this->db->from('lazada_orderitems');
		$this->db->where('order_number',NULL);
		$this->db->order_by('lazada_orderitems','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
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

	function select_by_order_number_in($arr_order_number)
	{

		$this->db->select('*');
		$this->db->from('lazada_orderitems');
		$this->db->where_in('order_number',$arr_order_number);
		$query = $this->db->get();
		return $query->result_array();


	}
	


}


