<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_volume_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_material_volume', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('web_material_volume_id',$id);
		$this->db->update('web_material_volume',$data);
	}

	function delete($id){
		$this->db->where('web_material_volume_id',$id);
		$this->db->delete('web_material_volume');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_material_volume');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_material_volume');
		$this->db->where('web_material_volume_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_web_material_volume_id_lasted($id){
		$this->db->select('*');
		$this->db->from('web_material_volume_history_lasted');
		$this->db->where('web_material_volume_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_id_history($id){
		$this->db->select('*');
		$this->db->from('web_material_volume_history');
		$this->db->where('web_material_volume_id',$id);
		$this->db->order_by('web_material_volume_history_id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_material_volume');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}


	function select_by_shop_id_join_limit_his($shop_id,$limit){

		$this->db->select('*');

		$this->db->from('web_material_history_lasted');
		$this->db->join('web_material_volume_history_lasted', 'web_material_volume_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_volume_type', 'web_material_volume_history_lasted.web_material_volume_type_id = web_material_volume_type.web_material_volume_type_id');
		$this->db->where('web_material_history_lasted.ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_shop_id_join_search($shopid,$material_search,$per_page,$offset,$sortby,$sorttype){

		$this->db->select('*');

		$this->db->from('web_material_history_lasted');
		$this->db->join('web_material_volume_history_lasted', 'web_material_volume_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_volume_type', 'web_material_volume_history_lasted.web_material_volume_type_id = web_material_volume_type.web_material_volume_type_id');
		$this->db->where('web_material_history_lasted.ShopID',$shop_id);
		if($material_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_join_search_his($shopid,$material_search,$per_page,$offset,$sortby,$sorttype){

		$this->db->select('*');

		$this->db->from('web_material_history_lasted');
		$this->db->join('web_material_volume_history_lasted', 'web_material_volume_history_lasted.web_material_id = web_material_history_lasted.web_material_id');
		$this->db->join('web_material_volume_type', 'web_material_volume_history_lasted.web_material_volume_type_id = web_material_volume_type.web_material_volume_type_id');
		$this->db->where('web_material_history_lasted.ShopID',$shop_id);
		if($material_search != ""){
			$this->db->like('web_material_history_lasted.material_name',$material_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */