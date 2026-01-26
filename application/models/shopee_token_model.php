<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class shopee_token_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	


	
	
	//get latest valid token, spare for 10 minutes before its expire
function getlatesttoken(){
	//3600= 1 hour,5400 = 1.30 hour
		$this->db->select('*, datediff(ss,code_generateddatetime,getdate()) as totlife,refresh_expires_in-datediff(ss,code_generateddatetime,getdate()) as left_time');
		$this->db->from('shopee_token');
		//$where = "refresh_expires_in-datediff(ss,code_generateddatetime,getdate())>=3600 and refreshtoken IS NOT NULL";
		$where = " refreshtoken IS NOT NULL";
		$this->db->where($where);
		$this->db->limit(1);
		$this->db->order_by("ShopeeLoginID", "desc");
		$query = $this->db->get();

		//echo $this->db->last_query();

		if($query->num_rows()==0)
		{
		    $arr=array('code'=>'0',
		    	       'shopid'=>'0',
		    	       'token'=>'0',
		    	       'refreshtoken'=>'0',
		    	       'ShopeeLoginID' => '0'
		    );
		    return $arr;
		}
		else
 		{
 		return $query->row_array();	
 		}

		
}	



function insert_token($data)
{

$this->db->insert('shopee_token', $data); 

}

function get_just_inserted_id()
{



$sql="select_identity_shopee_token";
	    //sqlsrv_configure("WarningsReturnAsErrors", 0);
		$query = $this->db->query($sql);
		if(!empty($query->result_array()))
		{
			$row=$query->row();
			return $row->ShopeeLoginID;
		}
		else
		{
			return NULL;
		}	

}

function getlatestaccesstoken()
{




}




function update_by_ShopeeLoginID($ShopeeLoginID,$data){
    	$this->db->where('ShopeeLoginID',$ShopeeLoginID);
		$this->db->update('shopee_token',$data);
		echo $this->db->last_query();
}




function get_litetime_token(){

	$sql = " select top 1 refresh_expires_in-datediff(ss,code_generateddatetime,getdate()) as lessthan,
			 CONVERT(varchar(8),DATEADD(ss,refresh_expires_in-datediff(ss,code_generateddatetime,getdate()),0),114) as litetime
			from shopee_token order by ShopeeLoginID desc";

  		$query = $this->db->query($sql);
		return $query->row_array();

}





}


