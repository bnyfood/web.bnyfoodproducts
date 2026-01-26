<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class inwshop_data_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function insert($data){
    	$this->db->insert('inwshop_data', $data);
    	//echo $this->db->last_query();
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	
	function update($data,$id){
    	$this->db->where('inwshop_data_id',$id);
		$this->db->update('inwshop_data',$data);
		//echo $this->db->last_query();
	}
	

	function delete($id){
		$this->db->where('inwshop_data_id',$id);
		$this->db->delete('inwshop_data');
	}

	function select_all(){
			$this->db->select('*');
			$this->db->from('inwshop_data');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_id($id){
			$this->db->select('*');
			$this->db->from('inwshop_data');
			$this->db->where('inwshop_data_id',$id);
			$query = $this->db->get();
			return $query->row_array();
			//return $query->row();
	}	

	function select_by_code($code){
			$this->db->select('*');
			$this->db->from('inwshop_data');
			$this->db->where('code',$code);
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_code_join($code){
			$this->db->select('*');
			$this->db->from('inwshop_data');
			$this->db->join('inwshop_item_data', 'inwshop_data.order_id = inwshop_item_data.order_id');
			$this->db->where('inwshop_data.code',$code);
			$this->db->where('inwshop_item_data.code',$code);
			$this->db->order_by('inwshop_data.order_id','asc');
			$query = $this->db->get();
			return $query->result_array();
			//return $query->row();
	}	

	function select_by_status_last_arr($arr_status,$limit){

		$this->db->select("*,FORMAT ( [ctime] , 'yyyy-MM' ) as yyyymm , ctime as create_time");
		$this->db->from('inwshop_data');
		$this->db->where_not_in('status',$arr_status);
		$this->db->where('order_id not in (select order_id from inwshop_taxinvoiceid)');

		$this->db->order_by('ctime','asc');
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function inwshop_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber)
	{
		$sql="inwshop_select_order_with_SearchType '".$StartDate."','".$EndDate."','".$search_type."','".$ordernumber."'";
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



}


