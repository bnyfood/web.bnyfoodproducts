<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_product_model_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_product_model', $data);
    	return $this->db->insert_id();
    	//echo $this->db->last_query();
	}

	function insert_batch($data){
		$this->db->insert_batch('web_product_model', $data);
	}
	
	function update($data,$id){
    	$this->db->where('ProductModelID',$id);
		$this->db->update('web_product_model',$data);
	}

	function update_by_genid($data,$id){
    	$this->db->where('genid',$id);
		$this->db->update('web_product_model',$data);
	}

	function delete($id){
		$this->db->where('ProductModelID',$id);
		$this->db->delete('web_product_model');
	}
	
	function select_all(){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_product_model');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_product_model');
		$this->db->where('ProductModelID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_product_id($product_id){
		$this->db->select('*');
		$this->db->from('web_product_model');
		$this->db->where('ProductID_history',$product_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_product_id_join($product_id){
		$this->db->select('a.ProductModelID,b.Name as model1,c.Name as model2,a.title1 as title1,a.title2 as title2,a.price,a.icon1,a.icon2');
		$this->db->from('web_product_model a');
		$this->db->join('web_product_model_group b', 'a.ProductModelGroupID1 = b.ProductModelGroupID');
		$this->db->join('web_product_model_group c', 'a.ProductModelGroupID2 = c.ProductModelGroupID');
		$this->db->where('a.ProductID_history',$product_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */