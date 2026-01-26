<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class laztoken_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
	//get latest valid token, spare for 10 minutes before its expire


function getlatesttoken_bk(){
		$this->db->select('*, datediff(n,generateddatetime,getdate()) as totlife');
		$this->db->from('laztoken');
		$where = "refresh_expires_in-datediff(n,generateddatetime,getdate())>=10";
		$this->db->where($where);
		$this->db->limit(1);
		$this->db->order_by("LazLoginID", "desc");
		$query = $this->db->get();

		//echo $this->db->last_query();

		if($query->num_rows()==0)
		{
		    $arr=array('code'=>'0',
		    	       'token'=>'0',
		    	       'refreshtoken'=>'0'

		    );
		    return $arr;
		}
		else
 		{
 		return $query->row();	
 		}

		
}	


function getlatesttoken(){

  		$this->db->select('*, datediff(d,getdate(),DATEADD (ss , refresh_expires_in , generateddatetime))as litetime');
		$this->db->from('laztoken');
		$where = " refreshtoken IS NOT NULL";
		$this->db->where($where);
		$this->db->limit(1);
		$this->db->order_by("LazLoginID", "desc");
		$query = $this->db->get();

		//echo $this->db->last_query();

		if($query->num_rows()==0)
		{
		    $arr=array('code'=>'0',
		    	       'token'=>'0',
		    	       'refreshtoken'=>'0'

		    );
		    return $arr;
		}
		else
 		{
 		return $query->row();	
 		}

}


function insert_token($data)
{

$this->db->insert('laztoken', $data); 

}

function get_litetime_token(){

	$sql = "select top 1 refresh_expires_in,DATEADD (ss , refresh_expires_in , generateddatetime) as aa, 
			datediff(d,getdate(),DATEADD (ss , refresh_expires_in , generateddatetime))as litetime,*
			from laztoken order by LazLoginID desc";

  		$query = $this->db->query($sql);
		return $query->row_array();

}





}


