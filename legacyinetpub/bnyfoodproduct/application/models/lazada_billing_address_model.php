<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_billing_address_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	function insertCustomers($datas)
	{
      $this->db->insert('lazada_customers', $data); 

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

	function insert_billingaddress($data)
	{
    $this->db->insert('lazada_billing_address', $data); 
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
         return "";
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
	


}


