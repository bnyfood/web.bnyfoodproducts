<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Web_domain_model : Class Web_domain_model extends from CI model.
**/
class Web_domain_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_domain', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_domain_id',$api_id);
		$this->db->update('web_domain',$data);
		return $this->db->affected_rows();
	}

	function delete($id){
		$this->db->where('web_domain_id',$id);
		$this->db->delete('web_domain');
	}
	
	function select_by_id($id){
		$this->db->select('web_domain_id, web_domain_name');
		$this->db->from('web_domain');
		$this->db->where('web_domain_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('web_domain_id, web_domain_name');
		$this->db->from('web_domain');
		$this->db->where('ShopID',$shop_id);
		$this->db->order_by('web_domain_id','DESC');
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_search($shopid,$domain_search,$per_page,$offset,$sortby,$sorttype){
		$this->db->select('web_domain_id, web_domain_name');
		$this->db->from('web_domain');
		$this->db->where('ShopID',$shopid);
		if($domain_search != ""){
			$this->db->like('web_domain_name',$domain_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_name($name,$shop_id){
		$this->db->select('web_domain_id, web_domain_name');
		$this->db->from('web_domain');
		$this->db->where('web_domain_name',$name);
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_name_except_id($name,$shop_id,$id){
		$this->db->select('web_domain_id, web_domain_name');
		$this->db->from('web_domain');
		$this->db->where('web_domain_name',$name);
		$this->db->where('ShopID',$shop_id);
		$this->db->where('web_domain_id !=',$id);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}

/* End of file web_domain_model.php */
/* Location: ./application/models/web_domain_model.php */
