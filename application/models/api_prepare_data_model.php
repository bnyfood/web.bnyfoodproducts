<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class api_prepare_data_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
	function insert($data){
    	$this->db->insert('api_prepare_data', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('api_prepare_data',$data);
		//echo $this->db->last_query();
	}

	function update_by_shopid($data,$shop_id,$api_name){
    	$this->db->where('shop_id',$shop_id);
    	$this->db->where('api_name',$api_name);
		$this->db->update('api_prepare_data',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('id',$id);
		$this->db->delete('api_prepare_data');
	}

	function select_all(){
		$this->db->select('*');
		$this->db->from('api_prepare_data');
		$query = $this->db->get();
		return $query->result_array();
		//return $query->row();
	}	

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('api_prepare_data');
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row_array();
		//return $query->row();
	}	

	function select_by_shopid($shop_id,$api_name){
		$this->db->select('*,convert(varchar, action_date, 23) as start,convert(varchar,DATEADD(DAY, 1, action_date),23) as next_day,convert(varchar,DATEADD(DAY, -15, action_date),23) as last_day,DATEDIFF(day,action_date,getdate()) as difftoday',false);
		$this->db->from('api_prepare_data');
		$this->db->where('shop_id',$shop_id);
		$this->db->where('api_name',$api_name);
		$query = $this->db->get();
		return $query->row_array();
			//return $query->row();
	}	

}


