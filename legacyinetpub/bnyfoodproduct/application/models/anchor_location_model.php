<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Anchor_location_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('anchor_location', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('anchor_location_id',$id);
		$this->db->update('anchor_location',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('anchor_location_id',$id);
		$this->db->delete('anchor_location');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('anchor_location');
		$this->db->where('anchor_location_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('anchor_location');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('anchor_location');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */