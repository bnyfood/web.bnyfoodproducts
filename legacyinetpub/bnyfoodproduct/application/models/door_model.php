<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Door_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('door', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('doorid',$id);
		$this->db->update('door',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('doorid',$id);
		$this->db->delete('door');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('door');
		$this->db->where('doorid',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('door');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */