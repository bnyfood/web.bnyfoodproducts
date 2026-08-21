<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_bankaccount_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_bankaccount', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$api_id){
    	$this->db->where('web_bankaccount_id',$api_id);
		$this->db->update('web_bankaccount',$data);
	}

	function delete($id){
		$this->db->where('web_bankaccount_id',$id);
		$this->db->delete('web_bankaccount');
	}
	
	function select_all(){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_bankaccount');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_bankaccount');
		$this->db->where('web_bankaccount_id',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_bankaccount');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_limit($shop_id,$limit){
		$this->db->select('*');
		$this->db->from('web_bankaccount');
		$this->db->where('ShopID',$shop_id);
		$this->db->limit($limit);
		$query = $this->db->get();
		return $query->result_array();
		//echo $this->db->last_query();
	}

	function select_by_shop_id_join($shop_id){

		$this->db->select('*');
		$this->db->from('web_bankaccount');
        $this->db->join('bankaccount', 'bankaccount.bankaccount_id = web_bankaccount.bankaccount_id');
		$this->db->where('ShopID',$shop_id);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_shop_search($shop_id,$accounttxt_search){

		$this->db->select('*');
		$this->db->from('web_bankaccount');
        $this->db->join('bankaccount', 'bankaccount.bankaccount_id = web_bankaccount.bankaccount_id');
		$this->db->where('ShopID',$shop_id);

		if($accounttxt_search != ""){
			$this->db->like('bookbank_name',$accounttxt_search);
			$this->db->or_like('bookbank_number',$accounttxt_search);
		}

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();

	}

	function select_by_id_join($web_bankaccount_id){

		$this->db->select('*');
		$this->db->from('web_bankaccount');
        $this->db->join('bankaccount', 'bankaccount.bankaccount_id = web_bankaccount.bankaccount_id');
		$this->db->where('web_bankaccount.web_bankaccount_id',$web_bankaccount_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();

	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */