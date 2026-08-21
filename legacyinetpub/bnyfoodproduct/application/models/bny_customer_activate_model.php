<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Bny_customer_activate_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();

    }
		    
    function insert($data){
    	$this->db->insert('bny_customer_activate', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('bny_customer_activate_id',$id);
		$this->db->update('bny_customer_activate',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('bny_customer_activate_id',$id);
		$this->db->delete('bny_customer_activate');
	}

    function del_by_bny_module_set_id($bny_module_set_id){
		$this->db->where('bny_module_set_id',$bny_module_set_id);
		$this->db->delete('bny_customer_activate');
	}
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('bny_customer_activate');
		$this->db->where('bny_customer_activate_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('bny_customer_activate');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */