<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Machine_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('machine', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('machine_id',$id);
		$this->db->update('machine',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('machine_id',$id);
		$this->db->delete('machine');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('machine');
		$this->db->where('machine_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('machine');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('machine');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_map_id($map_id){

		$this->db->select('*');
		$this->db->from('machine');
		$this->db->where('amt_map_id',$map_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */