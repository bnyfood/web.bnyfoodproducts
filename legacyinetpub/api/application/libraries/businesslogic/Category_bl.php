<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_bl{
	
	var $obarray, $list;
	
	public function __construct(){
		$this->CI =& get_instance();
		
		$this->CI->load->model('web_ProductCategory_model');
	}

	public $arr_sub_cat = array();

function get_cat_sud_by_id($parentcategory,$shop_id){
	$this->arr_sub_cat = [];
		//$main_arr_cat = $this->CI->categories_model->select_by_catid($cat_id);
		$main_cat = array();
		$num= 0;

			$sub_cat = $this->getsub_cat_by_id($parentcategory,$shop_id);
			//print_r($sub_cat);
			$main_cat[$num] = $sub_cat;
			$num = $num+1;
		
		//print_r($main_cat);
		
		return $main_cat;
		
	}
	
	function getsub_cat_by_id($parentcategory,$shop_id){
		
		$sub_arr_cats = $this->CI->web_ProductCategory_model->select_by_parent($parentcategory,$shop_id);

		if(!empty($sub_arr_cats)){
			foreach($sub_arr_cats as $sub_arr_cat){
				//echo ">>>".$sub_arr_cat['name']."<br>";
				$arr_cat = $this->CI->web_ProductCategory_model->select_by_id($sub_arr_cat['ProductCategoryID']);
				array_push($this->arr_sub_cat,$arr_cat['ProductCategoryID']);
				$this->getsub_cat_by_id($sub_arr_cat['ProductCategoryID'],$shop_id);
			}
		}
		//print_r($this->arr_sub_cat);
		
		return $this->arr_sub_cat;
	}

	function get_cat_by_parent($parent,$shop_id){
		$main_arr_cats = $this->CI->web_ProductCategory_model->select_by_parent($parent,$shop_id);
		//print_r($main_arr_cats);
		$arr_sub_cat = $this->get_cat_sud_by_class($parent,$shop_id);
		//print_r($arr_sub_cat);
		$sub_cnt = count($arr_sub_cat)-1;
		
		foreach($main_arr_cats as $main_arr_cat){
			for($i=0;$i<=$sub_cnt;$i++){
				$cnt = count($arr_sub_cat[$i]);
				if($cnt > 0){
					if(!empty($arr_sub_cat[$i][$main_arr_cat['ProductCategoryID']])){
						$arr_group_sub = $arr_sub_cat[$i][$main_arr_cat['ProductCategoryID']];
						$cnt_sub_last = count($arr_group_sub)-1;
						for($j=0;$j<=$cnt_sub_last;$j++){
							//echo $arr_group_sub[$j];
							$arr_cat_sub_last = $this->CI->web_ProductCategory_model->select_by_id($arr_group_sub[$j]);
							array_push($main_arr_cats,$arr_cat_sub_last);
						}
						//print_r($arr_sub_cat[$i][$main_arr_cat['id']]);
					}
					
				}
				
			}
		}
		
		//print_r($main_arr_cats);
		//print_r($arr_sub_cat[0]['3p9azlF4-3mkN-Kj6V-nwuy-7RqiJEoeqJmW']);
		//print_r($arr_sub_cat);
		return $main_arr_cats;
	}

	function get_cat_sud_by_class($parent,$shop_id){
		$main_arr_cats = $this->CI->web_ProductCategory_model->select_by_parent($parent,$shop_id);
		
		$main_cat = array();
		$num= 0;
		foreach($main_arr_cats as $main_arr_cat){
			//$arr_cat = $this->CI->categories_model->select_by_catid($main_arr_cat['id']);
			$sub_cat = $this->getsub_cat($main_arr_cat['ProductCategoryID'],$shop_id);
			//print_r($sub_cat);
			$main_cat[$num] = $sub_cat;
			$num = $num+1;
		}
		
		//print_r($main_cat);
		
		return $main_cat;
		
	}
	
	function getsub_cat($parentcategory,$shop_id){
		
		$sub_arr_cats = $this->CI->web_ProductCategory_model->select_by_parent($parentcategory,$shop_id);
		$sub_cat = array();
		
		if(!empty($sub_arr_cats)){
			$num= 0;
			foreach($sub_arr_cats as $sub_arr_cat){
				//echo ">>>".$sub_arr_cat['Title']."<br>";
				$arr_cat = $this->CI->web_ProductCategory_model->select_by_id($sub_arr_cat['ProductCategoryID']);
				$sub_cat[$parentcategory][$num] = $arr_cat;
				$this->getsub_cat($sub_arr_cat['ProductCategoryID'],$shop_id);
				$num = $num+1;
			}
		}
		//print_r($sub_cat);
		
		return $sub_cat;
	}

	function bulid_cat_by_parent($parentcategory,$shop_id){
	$this->arr_sub_cat = [];
		//$main_arr_cat = $this->CI->categories_model->select_by_catid($cat_id);
		$main_cat = array();
		$num= 0;
		$num_lv = 0;

			$sub_cat = $this->bulid_sub_cat_by_parent($parentcategory,$shop_id,$num_lv);
			//print_r($sub_cat);
			$main_cat[$num] = $sub_cat;
			$num = $num+1;
		
		//print_r($main_cat);

			$arr_data = array(
				'arr_cat' => $main_cat
			);
		
		return $arr_data;
		
	}
	
	function bulid_sub_cat_by_parent($parentcategory,$shop_id,$num_lv){
		
		$sub_arr_cats = $this->CI->web_ProductCategory_model->select_by_parent($parentcategory,$shop_id);
		//print_r($sub_arr_cats);
		if(!empty($sub_arr_cats)){
			$num_lv = $num_lv+1;
			foreach($sub_arr_cats as $sub_arr_cat){
				//echo $num_lv.">>>".$sub_arr_cat['Title']."<br>";
				$arr_cat = $this->CI->web_ProductCategory_model->select_by_id($sub_arr_cat['ProductCategoryID']);
				$arr_cat['level'] = $num_lv;
				//if($parentcategory <> 0){
					array_push($this->arr_sub_cat,$arr_cat);
				//}
				$this->bulid_sub_cat_by_parent($sub_arr_cat['ProductCategoryID'],$shop_id,$num_lv);
				
			}


		}
		//print_r($this->arr_sub_cat);
		
		return $this->arr_sub_cat;
	}

}