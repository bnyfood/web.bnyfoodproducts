<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_shop_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_shop', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('ShopID',$api_id);
		$this->db->update('web_shop',$data);
	}

	function delete($id){
		$this->db->where('ShopID',$id);
		$this->db->delete('web_shop');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_shop');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_shop');
		$this->db->where('ShopID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_id_all($id){
		$this->db->select('*');
		$this->db->from('web_shop');
		$this->db->where('ShopID',$id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_code($code){
		$this->db->select('*');
		$this->db->from('web_shop');
		$this->db->where('customer_code',$code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_code_search($code,$shop_search,$per_page,$offset,$sortby,$sorttype){
		$this->db->select('*');
		$this->db->from('web_shop');
		$this->db->where('customer_code',$code);
		if($shop_search != ""){
			$this->db->group_start();
			$this->db->like('ShopName',$shop_search);
			$this->db->or_like('domain',$shop_search);
			$this->db->or_like('URL_home',$shop_search);
			$this->db->or_like('ip',$shop_search);
			$this->db->group_end();
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */