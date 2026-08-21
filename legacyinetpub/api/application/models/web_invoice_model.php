<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_invoice_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_invoice', $data);
    	$insert_id = $this->db->insert_id();
    	//echo $this->db->last_query();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_invoice_id',$api_id);
		$this->db->update('web_invoice',$data);
	}

	function delete($id){
		$this->db->where('web_invoice_id',$id);
		$this->db->delete('web_invoice');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_invoice');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_invoice');
		$this->db->where('web_invoice_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_invoice');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('*');
		$this->db->from('web_invoice');
		$this->db->where('ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */