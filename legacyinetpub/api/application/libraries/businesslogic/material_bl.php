<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Material_bl{
	public function __construct(){
		$this->CI =& get_instance();

		$this->CI->load->model('Web_material_model');

		$this->CI->load->model('web_supplier_model');
		$this->CI->load->model('Web_purchase_order_model');

	}

	function get_po_supplier_price($ShopID,$web_material_id,$supplier_id,$ponumber){

	$arr_sups = $this->CI->web_supplier_model->select_by_shop_id($ShopID);
	$arr_datas = $this->CI->Web_material_model->select_by_matid_his($ShopID,$web_material_id,$supplier_id,$ponumber);
	
	
	$cnt_mat = count($arr_datas);
	$loop = $cnt_mat-1;

	if($cnt_mat > 0){
		for($i=0;$i<=$loop;$i++){
			$arr_price = array();
			foreach($arr_sups as $arr_sup){
				$arr_data_price = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$arr_sup['web_supplier_id']);

				if(!empty($arr_data_price)){
					//array_push($arr_price,$arr_data_price['material_unit_price']);
					$arr_datas[$i]['data_price'][$arr_sup['supplier_name']] = $arr_data_price['unit_price'];
				}else{
					$arr_datas[$i]['data_price'][$arr_sup['supplier_name']] = 0;
				}
			}

			//$arr_datas[$i]['data_price'] = $arr_price;
		}
	}

	return $arr_datas;

	}
	
	
	function get_supplier_price($ShopID,$web_material_id,$supplier_id){

	$arr_sups = $this->CI->web_supplier_model->select_by_shop_id($ShopID);
	$arr_datas = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$supplier_id);
	

	$arr_price = array();
	foreach($arr_sups as $arr_sup){
		$arr_data_price = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$arr_sup['web_supplier_id']);

		if(!empty($arr_data_price)){
			//array_push($arr_price,$arr_data_price['material_unit_price']);
			$arr_datas['data_price'][$arr_sup['supplier_name']] = $arr_data_price;
		}else{
			$arr_supplier = $this->CI->web_supplier_model->select_by_id_unit($arr_sup['web_supplier_id']);
			$arr_datas['data_price'][$arr_supplier['supplier_name']] = $arr_supplier;
		}
	}

	//$arr_datas[$i]['data_price'] = $arr_price;
	
	

	return $arr_datas;

	}

	function get_supplier_price_bk2($ShopID,$web_material_id,$supplier_id){

	$arr_sups = $this->CI->web_supplier_model->select_by_shop_id($ShopID);
	$arr_datas = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$supplier_id);
	

	$arr_price = array();
	foreach($arr_sups as $arr_sup){
		$arr_data_price = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$arr_sup['web_supplier_id']);

		if(!empty($arr_data_price)){
			//array_push($arr_price,$arr_data_price['material_unit_price']);
			$arr_datas['data_price'][$arr_sup['supplier_name']] = $arr_data_price['unit_price'];
		}else{
			$arr_datas['data_price'][$arr_sup['supplier_name']] = 0;
		}
	}

	//$arr_datas[$i]['data_price'] = $arr_price;
	
	

	return $arr_datas;

	}

	function get_supplier_price_bk($ShopID,$web_material_id,$supplier_id){

	$arr_sups = $this->CI->web_supplier_model->select_by_shop_id($ShopID);
	$arr_datas = $this->CI->Web_material_model->select_by_matid_his($ShopID,$web_material_id,$supplier_id);
	
	
	$cnt_mat = count($arr_datas);
	$loop = $cnt_mat-1;

	if($cnt_mat > 0){
		for($i=0;$i<=$loop;$i++){
			$arr_price = array();
			foreach($arr_sups as $arr_sup){
				$arr_data_price = $this->CI->Web_material_model->select_by_matid_supid_his($ShopID,$web_material_id,$arr_sup['web_supplier_id']);

				if(!empty($arr_data_price)){
					//array_push($arr_price,$arr_data_price['material_unit_price']);
					$arr_datas[$i]['data_price'][$arr_sup['supplier_name']] = $arr_data_price['unit_price'];
				}else{
					$arr_datas[$i]['data_price'][$arr_sup['supplier_name']] = 0;
				}
			}

			//$arr_datas[$i]['data_price'] = $arr_price;
		}
	}

	return $arr_datas;

	}


	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */