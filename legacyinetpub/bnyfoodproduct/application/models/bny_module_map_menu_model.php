<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Bny_module_map_menu_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();

    }
		    
    function insert($data){
    	$this->db->insert('bny_module_map_menu', $data);
		//echo $this->db->last_query();
    	return $this->db->insert_id();
	}
	
	function update($data,$id){
    	$this->db->where('bny_module_map_menu_id',$id);
		$this->db->update('bny_module_map_menu',$data);
		//echo $this->db->last_query();
	}

	function delete($id){
		$this->db->where('bny_module_map_menu_id',$id);
		$this->db->delete('bny_module_map_menu');
	}

	function del_by_bny_module_id($bny_module_id){
		$this->db->where('bny_module_id',$bny_module_id);
		$this->db->delete('bny_module_map_menu');
	}
	
	
	function select_by_id($id){

		$this->db->select('*');
		$this->db->from('bny_module_map_menu');
		$this->db->where('bny_module_map_menu_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_all(){

		$this->db->select('*');
		$this->db->from('bny_module_map_menu');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function get_by_module_id($bny_module_id){
		$this->db->select('*');
		$this->db->from('menu');
		$this->db->join('bny_module_map_menu', 'menu.menu_id = bny_module_map_menu.menu_id');
		$this->db->where('bny_module_map.bny_module_id',$bny_module_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

}

/* End of file block_model.php */
/* Location: ./application/models/block_model.php */