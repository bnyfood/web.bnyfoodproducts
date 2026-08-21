<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_supplier_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_supplier', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_supplier_id',$api_id);
		$this->db->update('web_supplier',$data);
	}

	function delete($id){
		$this->db->where('web_supplier_id',$id);
		$this->db->delete('web_supplier');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_supplier');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_supplier');
		$this->db->where('web_supplier_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_supplier');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('*');
		$this->db->from('web_supplier');
		$this->db->where('ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_search($shopid,$sipplier_search,$per_page,$offset,$sortby,$sorttype){
		//echo $sortby.'--'.$sorttype;
		$this->db->select('*');
		$this->db->from('web_supplier');
		$this->db->where('ShopID',$shopid);
		if($sipplier_search != ""){
			$this->db->like('supplier_name',$sipplier_search);
		}
		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

		
	}

	function select_by_shop_search($shopid,$suppliertxt_search){
		//echo $sortby.'--'.$sorttype;
		$this->db->select('*');
		$this->db->from('web_supplier');
		$this->db->where('ShopID',$shopid);
		if($suppliertxt_search != ""){
			$this->db->like('supplier_name',$suppliertxt_search);
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_id_unit($id){
		$this->db->select('*,unit_price=0',false);
		$this->db->from('web_supplier');
		$this->db->where('web_supplier_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */