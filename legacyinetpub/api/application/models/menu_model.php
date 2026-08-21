<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Menu_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('menu', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('menu_id',$id);
		$this->db->update('menu',$data);
	}

	function delete($id){
		$this->db->where('menu_id',$id);
		$this->db->delete('menu');
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu','root');
		$this->db->order_by('sort','asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('menu_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_parent($parent_menu){

		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu',$parent_menu);
		$this->db->order_by('sort','asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_parent_customer($parent_menu){

		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu',$parent_menu);
		$this->db->where('show_customer',1);
		$this->db->order_by('sort','asc');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_parent_mapid($parent_menu,$arr_menu_id){

		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu',$parent_menu);
		$this->db->where_in('menu_id',$arr_menu_id);
		$this->db->order_by('sort','asc');
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_by_no_parent_mapid($parent_menu,$arr_menu_id){

		$this->db->select('*');
		$this->db->from('menu');
		$this->db->where('parent_menu <>',$parent_menu);
		$this->db->where_in('menu_id',$arr_menu_id);
		$this->db->order_by('sort','asc');
		$query = $this->db->get();
		return $query->result_array();

	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */