<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class User_level_authen_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('user_level_authen', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('user_level_authen_id',$api_id);
		$this->db->update('user_level_authen',$data);
	}

	function delete($id){
		$this->db->where('user_level_authen_id',$id);
		$this->db->delete('user_level_authen');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('user_level_authen');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('user_level_authen');
		$this->db->where('user_level_authen_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_controller($controller){
		$this->db->select('*');
		$this->db->from('user_level_authen');
		$this->db->where('controller',$controller);
		$query = $this->db->get();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */