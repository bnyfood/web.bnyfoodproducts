<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Silo_sub_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('silo_sub', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('silo_sub_id',$id);
		$this->db->update('silo_sub',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('silo_sub_id',$id);
		$this->db->delete('silo_sub');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('silo_sub');
		$this->db->where('silo_sub_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('silo_sub');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_shop($shopid){

		$this->db->select('*');
		$this->db->from('silo_sub');
		$this->db->where('ShopID',$shopid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_silo_id($silo_id){

		$this->db->select('*');
		$this->db->from('silo_sub');
		$this->db->where('silo_id',$silo_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */