<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Tiktok_order_payment_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('tiktok_order_payment', $data);
    	//echo $this->db->last_query();
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('tiktok_order_payment_id',$id);
		$this->db->update('tiktok_order_payment',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('tiktok_order_payment_id',$id);
		$this->db->delete('tiktok_order_payment');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('tiktok_order_payment');
		$this->db->where('tiktok_order_payment_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('tiktok_order_payment');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_orderid($orderid){

		$this->db->select('*');
		$this->db->from('tiktok_order_payment');
		$this->db->where('order_id',$orderid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */