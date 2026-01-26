<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Google_login_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('google_login', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('id',$id);
		$this->db->update('google_login',$data);
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('google_login');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('google_login');
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function getby_google_id($google_id){
		$this->db->select('*');
		$this->db->from('google_login');
		$this->db->where('google_id',$google_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function getby_email($email){
		$this->db->select('*');
		$this->db->from('google_login');
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}
