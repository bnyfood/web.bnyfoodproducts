<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Amt_map_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('amt_map', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('amt_map_id',$id);
		$this->db->update('amt_map',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('amt_map_id',$id);
		$this->db->delete('amt_map');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('amt_map');
		$this->db->where('amt_map_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('amt_map');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shop_id){

		$this->db->select('*');
		$this->db->from('amt_map');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */