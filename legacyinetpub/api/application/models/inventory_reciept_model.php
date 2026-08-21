<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Inventory_reciept_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('inventory_reciept', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('inventory_reciept_id',$api_id);
		$this->db->update('inventory_reciept',$data);
	}

	function delete($id){
		$this->db->where('inventory_reciept_id',$id);
		$this->db->delete('inventory_reciept');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('inventory_reciept');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('inventory_reciept');
		$this->db->where('inventory_reciept_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('inventory_reciept');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function last_order_code_by_yymm($yymm){
		$this->db->select('*');
		$this->db->from('inventory_reciept');
		$this->db->where("FORMAT ( [cdate] , 'yyyy-MM' ) = '".$yymm."'");
		$this->db->order_by('inventory_reciept_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */