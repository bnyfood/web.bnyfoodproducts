<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_category_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('web_category', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('web_category_id',$id);
		$this->db->update('web_category',$data);
	}

	function delete($id){
		$this->db->where('web_category_id',$id);
		$this->db->delete('web_category');
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('web_category');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_category');
		$this->db->where('web_category_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_parent($parent_id,$shop_id){
		$this->db->select('*');
		$this->db->from('web_category');
		$this->db->where('f_parentcategory',$parent_id);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_class_parent($class,$parent_id,$shop_id){
		$this->db->select('*');
		$this->db->from('web_category');
		$this->db->where('componentclass',$class);
		$this->db->where('f_parentcategory',$parent_id);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_root_by_shopid($class,$shop_id){
		$this->db->select('*');
		$this->db->from('web_category');
		$this->db->where('componentclass',$class);
		$this->db->where('ShopID',$shop_id);
		$this->db->where('f_parentcategory',0);
		$query = $this->db->get();
		return $query->row_array();
	}
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */