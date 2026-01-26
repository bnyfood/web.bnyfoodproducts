<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Shopee_tracking_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('shopee_tracking', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('shopee_tracking_id',$id);
		$this->db->update('shopee_tracking',$data);
	}

	function update_by_order_no($data,$order_sn){
    	$this->db->where('order_sn',$order_sn);
		$this->db->update('shopee_tracking',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('shopee_tracking');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('shopee_tracking');
		$this->db->where('shopee_tracking_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_orderno_tracking_round($round){

		$this->db->select('*');
		$this->db->from('shopee_tracking');
		$this->db->where('api_round > 0');
		$this->db->where('api_round <= '.$round);
		$this->db->where('api_finish',0);
		$this->db->limit(10);
		$this->db->order_by('shopee_tracking_id','asc');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function get_in_order_no_correct($arr_order_no){

		$this->db->select('*');
		$this->db->from('shopee_tracking');
		$this->db->where_in('order_sn',$arr_order_no);
		$this->db->where('transaction_correct',1);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_order_groupby_orderno_tracking($order_start)
	{

         $sql="shopee_select_order_groupby_orderno_tracking '".$order_start."'";
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
