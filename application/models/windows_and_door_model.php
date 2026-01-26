<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Windows_and_door_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('windows_and_door', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('windows_and_door_id',$id);
		$this->db->update('windows_and_door',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('windows_and_door_id',$id);
		$this->db->delete('windows_and_door');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('windows_and_door');
		$this->db->where('windows_and_door_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('windows_and_door');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('windows_and_door');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_map_id($map_id){

		$this->db->select('*');
		$this->db->from('windows_and_door');
		$this->db->where('amt_map_id',$map_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */