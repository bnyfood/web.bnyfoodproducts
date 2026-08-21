<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_products_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('web_products', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('ProductID',$id);
		$this->db->update('web_products',$data);
		//echo $this->db->last_query();
	}

	function update_custom($data,$id){
		if($data['PriceMainType'] == 0){
			$this->db->set('Price', 'Price+(Price*'.$data['PriceMainAmount'].')/100', FALSE);
		}else{
			$this->db->set('Price', 'Price-(Price*'.$data['PriceMainAmount'].')/100', FALSE);
		}
    	

    	$this->db->set('OnDiscount', $data['OnDiscount']);
    	$this->db->set('DiscountType', $data['DiscountType']);
    	$this->db->set('DiscountAmount', $data['DiscountAmount']);
    	$this->db->set('DiscountAmountType', $data['DiscountAmountType']);
		$this->db->where('ProductID', $id);
		$this->db->update('web_products');
	}

	function update_custom2($data,$id){
    	if($data['PriceMainType'] == 0){
			$this->db->set('Price', 'Price+(Price*'.$data['PriceMainAmount'].')/100', FALSE);
		}else{
			$this->db->set('Price', 'Price-(Price*'.$data['PriceMainAmount'].')/100', FALSE);
		}
    	$this->db->set('OnDiscount', $data['OnDiscount']);
		$this->db->update('web_products');
	}

	function delete($id){
		$this->db->where('ProductID',$id);
		$this->db->delete('web_products');
	}
	
	function select_all(){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('web_products');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ProductID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_shop_id($shop_id){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ShopID',$shop_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_id_cat($shop_id,$cat_id,$pro_name){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ShopID',$shop_id);
		$this->db->where('ProductCategoryID',$cat_id);
		if($pro_name != ""){
			$this->db->like('Title',$pro_name);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_shop_search($shop_id,$product_cat_search,$txt_product_search,$per_page,$offset){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ShopID',$shop_id);
		$this->db->where('is_main_product',1);
		if($product_cat_search != ""){
			$this->db->where('ProductCategoryID',$product_cat_search);
		}

		if($txt_product_search != ""){
			$this->db->like('Title',$txt_product_search);
			$this->db->or_like('Sku',$txt_product_search);
		}

		$this->db->limit($per_page, $offset);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_shop_search_cnt($shop_id,$product_cat_search,$txt_product_search){
		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ShopID',$shop_id);
		if($product_cat_search != ""){
			$this->db->where('ProductCategoryID',$product_cat_search);
		}

		if($txt_product_search != ""){
			$this->db->like('Title',$txt_product_search);
			$this->db->or_like('Sku',$txt_product_search);
		}

		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_map_product($arr_id){

		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where_in('ProductID',$arr_id);
		$query = $this->db->get();
		return $query->result_array();

	}

	function select_by_parent($parent_id){

		$this->db->select('*');
		$this->db->from('web_products');
		$this->db->where('ParentProductID',$parent_id);
		$query = $this->db->get();
		return $query->result_array();

	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */