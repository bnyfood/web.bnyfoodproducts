<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class tiktok_token_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('tiktok_token', $data);
    	echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('tiktok_token_id',$id);
		$this->db->update('tiktok_token',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('tiktok_token_id',$id);
		$this->db->delete('tiktok_token');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('tiktok_token');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('tiktok_token');
			$this->db->where('tiktok_token_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_lasted_token(){
		$this->db->select('*');
		$this->db->from('tiktok_token');
		$this->db->order_by('tiktok_token_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}

	function get_litetime_token(){
		$sql = "SELECT TOP 1
			DATEDIFF(ss, GETDATE(), access_token_expire_in) AS lessthan,
			CONVERT(varchar(8), DATEADD(ss, CASE WHEN DATEDIFF(ss, GETDATE(), access_token_expire_in) < 0 THEN 0 ELSE DATEDIFF(ss, GETDATE(), access_token_expire_in) END, 0), 114) AS litetime,
			DATEDIFF(d, GETDATE(), access_token_expire_in) AS litetime_days
			FROM tiktok_token
			ORDER BY tiktok_token_id DESC";
		$query = $this->db->query($sql);
		return $query->row_array();
	}
}


