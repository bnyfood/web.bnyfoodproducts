<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Bny_expense_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('bny_expense', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('bny_expense_id',$api_id);
		$this->db->update('bny_expense',$data);
	}

	function delete($id){
		$this->db->where('bny_expense_id',$id);
		$this->db->delete('bny_expense');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('bny_expense');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->where('bny_expense_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->where('ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}


	function select_by_shop_search($shopid,$suppliertxt_search){
		//echo $sortby.'--'.$sorttype;
		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->where('ShopID',$shopid);
		if($suppliertxt_search != ""){
			$this->db->like('supplier_name',$suppliertxt_search);
		}
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_shop_id_search($shopid,$arr_search,$per_page,$offset,$sortby,$sorttype){
		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->join('web_purchase_order', 'web_purchase_order.web_purchase_order_id = bny_expense.web_purchase_order_id');
		$this->db->join('web_supplier', 'web_supplier.web_supplier_id = bny_expense.web_supplier_id');
		$this->db->where('bny_expense.ShopID',$shopid);
		if($arr_search['expense_search'] != ""){
			$this->db->like('po_number',$arr_search['expense_search']);
		}

		/*if(($arr_search['expense_start'] != "")and($arr_search['expense_stop'] != "")){
			$this->db->where('expense_date>=',$arr_search['expense_start']);
			$this->db->where('expense_date<=',$arr_search['expense_stop']);
		}*/

		if($sortby != ""){
			$this->db->order_by($sortby,$sorttype);
		}
		$this->db->limit($per_page,$offset);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
		
	}

	function get_by_expense_id_join_slip($bny_expense_id){

		$this->db->select('*');
		$this->db->from('bny_expense');
		$this->db->join('web_slip', 'bny_expense.bny_expense_id = web_slip.bny_expense_id','left outer');
		$this->db->join('web_invoice', 'bny_expense.bny_expense_id = web_invoice.bny_expense_id','left outer');
		$this->db->where('bny_expense.bny_expense_id',$bny_expense_id);
		$query = $this->db->get();
		return $query->row_array();
		
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */