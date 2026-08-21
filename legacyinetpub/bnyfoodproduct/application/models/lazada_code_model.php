<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class lazada_code_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
	//get latest valid token, spare for 10 minutes before its expire
function getlatestcode(){
		$this->db->select('*');
		$this->db->from('lazada_code');
		$this->db->limit(1);
		$this->db->order_by("lazadacodeID", "desc");
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



function insert_code($data)
{

$this->db->insert('lazada_code', $data); 

}





}


