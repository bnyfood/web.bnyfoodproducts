<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_ProductCategory_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('web_ProductCategory', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('ProductCategoryID',$id);
		$this->db->update('web_ProductCategory',$data);
	}

	function delete($id){
		$this->db->where('ProductCategoryID',$id);
		$this->db->delete('web_ProductCategory');
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('web_ProductCategory');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_ProductCategory');
		$this->db->where('ProductCategoryID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_parent($parent_id,$shop_id){
		$this->db->select('*');
		$this->db->from('web_ProductCategory');
		$this->db->where('SuperCategoryID',$parent_id);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_root_by_shopid($shop_id){
		$this->db->select('*');
		$this->db->from('web_ProductCategory');
		$this->db->where('ShopID',$shop_id);
		$this->db->where('SuperCategoryID',0);
		$query = $this->db->get();
		return $query->row_array();
	}
}
