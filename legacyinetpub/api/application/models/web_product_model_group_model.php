<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_product_model_group_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_product_model_group', $data);
    	//return $this->db->insert();
    	//echo $this->db->last_query();
	}
	
	function update($data,$id){
    	$this->db->where('ProductModelGroupID',$id);
		$this->db->update('web_product_model_group',$data);
	}

	function delete($id){
		$this->db->where('ProductModelGroupID',$id);
		$this->db->delete('web_product_model_group');
	}
	
	function select_all(){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_product_model_group');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_product_model_group');
		$this->db->where('ProductModelGroupID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_product_model_group');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */