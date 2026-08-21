<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Lazada_tracking_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('lazada_tracking', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('lazada_tracking_id',$id);
		$this->db->update('lazada_tracking',$data);
	}

	function update_by_order_no($data,$order_number){
    	$this->db->where('order_number',$order_number);
		$this->db->update('lazada_tracking',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('lazada_tracking');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('lazada_tracking');
		$this->db->where('lazada_tracking_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_orderno_tracking_round($round){

		$this->db->select('*');
		$this->db->from('lazada_tracking');
		$this->db->where('api_round > 0');
		$this->db->where('api_round <= '.$round);
		$this->db->where('api_finish',0);
		$this->db->limit(10);
		$this->db->order_by('lazada_tracking_id','asc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function get_in_order_no_correct($arr_order_no){

		$this->db->select('*');
		$this->db->from('lazada_tracking');
		$this->db->where_in('order_number',$arr_order_no);
		$this->db->where('transaction_correct',1);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_order_groupby_orderno_tracking($order_start)
	{

         $sql="select_order_groupby_orderno_tracking '".$order_start."'";
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
