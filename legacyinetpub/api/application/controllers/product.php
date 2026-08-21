<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Product extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/encryption_util');
		$this->load->library('businesslogic/api_log_bl');
		$this->load->library('businesslogic/api_auth_bl');


		$this->load->model('web_products_model');
		$this->load->model('web_product_model_model');
		$this->load->model('web_product_model_group_model');
		$this->load->model('product_map_model');
		$this->load->model('web_sku_model');

		$this->load->library('businesslogic/manage_val_bl');

		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_product_by_shop(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shopid_en = $this->uri->segment(3);	
	      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);	

	      $data_re = $this->web_products_model->select_by_shop_id($shopid);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_by_shop_cat(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$shopid_en = $this->input->post('shop_id');
	    	$shopid = $this->encryption_util->decrypt_ssl($shopid_en);

	    	$cat_id = $this->input->post('cat_id');
	    	$search_pro_name = $this->input->post('search_pro_name');

	      $data_re = $this->web_products_model->select_by_shop_id_cat($shopid,$cat_id,$search_pro_name);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_by_shop_search(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shop_id_en = $this->input->post('shop_id_en');
	      $shopid = $this->encryption_util->decrypt_ssl($shop_id_en);	
	      $product_cat_search = $this->input->post('product_cat_search');
	      $txt_product_search = $this->input->post('txt_product_search');
	      $per_page = $this->input->post('per_page');
	      $offset = $this->input->post('offset');

	      $arr_products = $this->web_products_model->select_by_shop_search($shopid,$product_cat_search,$txt_product_search,$per_page,$offset);

	      if(!empty($arr_products)){
	      	$num = 0;
	      	foreach($arr_products as $arr_product){
	      		$arr_pro_models = $this->web_products_model->select_by_parent($arr_product['ProductID']);

	      		if(!empty($arr_pro_models)){
	      			$arr_products[$num]['arr_pro_models'] = $arr_pro_models;
	      		}

	      		$num = $num+1;
	      	}
	      }

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_products,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_products,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_by_shop_search_cnt(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shop_id_en = $this->input->post('shop_id_en');
	      $shopid = $this->encryption_util->decrypt_ssl($shop_id_en);	
	      $product_cat_search = $this->input->post('product_cat_search');
	      $txt_product_search = $this->input->post('txt_product_search');

	      $data_re = $this->web_products_model->select_by_shop_search_cnt($shopid,$product_cat_search,$txt_product_search);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

     function product_add(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$Title = $this->input->post('Title');
	    	$product_choice = $this->input->post('product_choice');
			$ProductCategoryID = $this->input->post('ProductCategoryID');
			$Sku = $this->input->post('Sku');
			$Unit = $this->input->post('Unit');
			$Cost_price = $this->input->post('Cost_price');
			$Price = $this->input->post('Price');
			$product_variant1 = $this->input->post('product_variant1');
			$product_variant2 = $this->input->post('product_variant2');
			$product_variant_value1 = $this->input->post('product_variant_value1');
			$product_variant_value2 = $this->input->post('product_variant_value2');
			$product_quality = $this->input->post('product_quality');
			$Weight = $this->input->post('Weight');
			$Dimension = $this->input->post('Dimension');
			$Condition = $this->input->post('Condition');
			$Description = $this->input->post('Description');
			$ShopID_en = $this->input->post('ShopID');
			$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
			$is_main_product = $this->input->post('is_main_product');
			$is_model = $this->input->post('is_model');
			$sku_ran_id =  $this->input->post('sku_ran_id');

			$parent_no_child = $this->input->post('parent_no_child');

			$arr_data = array(
				'Title' => $Title,
				'product_choice' => $product_choice,
				'ProductCategoryID' => $ProductCategoryID,
				'Sku' => $Sku,
				'Unit' => $Unit,
				'Cost_price' => $Cost_price,
				'Price' => $Price,
				'is_model' => $is_model,
				'product_variant1' => $product_variant1,
				'product_variant_value1' => $product_variant_value1,
				'product_variant2' => $product_variant2,
				'product_variant_value2' => $product_variant_value2,
				'product_quality' => $product_quality,
				'Weight' => $Weight,
				'Dimension' => $Dimension,
				'Condition' => $Condition,
				'Description' => $Description,
				'ShopID' => $ShopID,
				'is_main_product' => $is_main_product
			);



	      $data_re = $this->web_products_model->insert($arr_data);

	      $data_sku = array(
				'web_product_id' => $data_re
			);

			$this->web_sku_model->update_by_ran_id($data_sku,$sku_ran_id);


	      if($parent_no_child == 1){
				$data_map_model = array(
					'parent_product_id' => $data_re,
					'product_id' => $data_re,
					'ShopID' => $ShopID
				);

				$this->product_map_model->insert($data_map_model);
			}

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	 function product_add_model_set(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$arr_curl = $this->input->post('arr_curl');

			$arr_curl = json_decode($arr_curl,true);

			//$num = count($arr_curl);

			foreach ($arr_curl as  $val) { 

				$Title = $val['Title'];
				$product_choice = $val['product_choice'];
				$ProductCategoryID = $val['ProductCategoryID'];
				$Sku = $val['Sku'];
				$Unit = $val['Unit'];
				$Price = $val['Price'];
				$product_variant1 = $val['product_variant1'];
				$product_variant2 = $val['product_variant2'];
				$product_quality = $val['product_quality'];
				$Weight = $val['Weight'];
				$Dimension = $val['Dimension'];
				$Condition = $val['Condition'];
				$Description = $val['Description'];
				$ShopID_en = $val['ShopID'];
				$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
				$is_main_product = $val['is_main_product'];
				$product_parent_id =  $val['product_parent_id'];
				

				$arr_data = array(
					'Title' => $Title,
					'product_choice' => $product_choice,
					'ProductCategoryID' => $ProductCategoryID,
					'Sku' => $Sku,
					'Unit' => $Unit,
					'Price' => $Price,
					'product_variant1' => $product_variant1,
					'product_variant2' => $product_variant2,
					'product_quality' => $product_quality,
					'Weight' => $Weight,
					'Dimension' => $Dimension,
					'Condition' => $product_quality,
					'Description' => $Description,
					'ShopID' => $ShopID,
					'is_main_product' => $is_main_product
				);

	      		$data_re = $this->web_products_model->insert($arr_data);

	      
				$data_map_model = array(
					'parent_product_id' => $product_parent_id,
					'product_id' => $data_re,
					'ShopID' => $ShopID
				);

				$this->product_map_model->insert($data_map_model);

			}

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function edit_product_group(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$un_arr_id = $this->input->post('un_arr_id');
			$arr_id = $this->input->post('arr_id');
			$DiscountType = $this->input->post('DiscountType');
			$DiscountAmount = $this->input->post('DiscountAmount');
			$DiscountAmountType = $this->input->post('DiscountAmountType');
			$PriceMain = $this->input->post('PriceMain');
			$PriceMainType = $this->input->post('PriceMainType');
			$PriceMainAmount = $this->input->post('PriceMainAmount');

			$un_arr_id = json_decode($un_arr_id);
			$arr_id = json_decode($arr_id);

			$cnt_proid = count($arr_id);
			$cnt_unproid = count($un_arr_id);
			$product_id = "";
			
			if($cnt_proid > 0){
				for($i=0;$i<$cnt_proid;$i++){
					//echo $arr_id[$i]."<br>";
					$ex_id = explode("_",$arr_id[$i]);
					$product_id = $ex_id[0];
					$product_id = $this->encryption_util->decrypt_ssl($product_id);
					//echo $product_id."<br>";
					//update data
					if($PriceMain == "1"){

						$data_dis = array(
							'PriceMainType' => intval($PriceMainType),
							'PriceMainAmount' => intval($PriceMainAmount),
							'OnDiscount' => 0,
							'DiscountType' => intval($DiscountType),
							'DiscountAmount' => intval($DiscountAmount),
							'DiscountAmountType' => intval($DiscountAmountType)

						);
						$this->web_products_model->update_custom($data_dis,$product_id);

					}else{

						$data_dis = array(

							'OnDiscount' => 0,
							'DiscountType' => intval($DiscountType),
							'DiscountAmount' => intval($DiscountAmount),
							'DiscountAmountType' => intval($DiscountAmountType)

						);
						$this->web_products_model->update($data_dis,$product_id);
					}
					

					
				}
			}

			if($cnt_unproid > 0){
				for($j=0;$j<$cnt_unproid;$j++){

					$unex_id = explode("_",$un_arr_id[$j]);
					$unproduct_id = $unex_id[0];

					if($PriceMain == "1"){

						$data_dis = array(
							'PriceMainType' => intval($PriceMainType),
							'PriceMainAmount' => intval($PriceMainAmount),
							'OnDiscount' => 1

						);
						$this->web_products_model->update_custom2($data_dis,$unproduct_id);

					}else{

						$data_nodis = array(
							'OnDiscount' => 1	
						);
						$unproduct_id = $this->encryption_util->decrypt_ssl($unproduct_id);
						$this->web_products_model->update($data_nodis,$unproduct_id);
					}
				}
			}	


	      $data_json = $this->json_util->make_json('Select data','Success','No','Insert Success',$arr_header['api_token']); 
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_model(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shopid_en = $this->uri->segment(3);	
	      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);	

	      $data_re = $this->web_product_model_group_model->select_by_shop_id($shopid);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function add_product_model(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$shop_id_en = $this->input->post('shop_id_en');
	    	$ShopID = $this->encryption_util->decrypt_ssl($shop_id_en);
	    	$model_name = $this->input->post('model_name');

			$arr_data = array(
				'Name' => $model_name,
				'ShopID' => $ShopID
			);

	      $data_re = $this->web_product_model_group_model->insert($arr_data);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function edit_product_model(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$mogel_group_id = $this->input->post('mogel_group_id');
	    	$model_name = $this->input->post('model_name');

			$arr_data = array(
				'Name' => $model_name
			);

	      $data_re = $this->web_product_model_group_model->update($arr_data,$mogel_group_id);

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function del_product_model(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $mogel_group_id = $this->uri->segment(3);

	      $data_re = $this->web_product_model_group_model->delete($mogel_group_id);

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_model_by_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $mogel_group_id = $this->uri->segment(3);	

	      $data_re = $this->web_product_model_group_model->select_by_id($mogel_group_id);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function add_product_model_data_group(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){


			$arr_curl = $this->input->post('arr_curl');

			$arr_curl = json_decode($arr_curl);

			//print_r($arr_curl);
			$this->web_product_model_model->insert_batch($arr_curl);
			$data_re = "";
	      

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function add_product_model_data(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$ren_id = $this->input->post('ren_id');
	    	$model1 = $this->input->post('model1');
			$model2 = $this->input->post('model2');
			$title1 = $this->input->post('title1');
			$title2 = $this->input->post('title2');
			$icon1 = $this->input->post('icon1');
			$icon2 = $this->input->post('icon2');

			$model_price = $this->input->post('model_price');

			$arr_data = array(
				'ProductModelGroupID1' => $model1,
				'ProductModelGroupID2' => $model2,
				'title1' => $title1,
				'title2' => $title2,
				'icon1' => $icon1,
				'icon2' => $icon2,
				'price' => $model_price,
				'genid' => $ren_id
			);

	      $data_re = $this->web_product_model_model->insert($arr_data);

	      if(!empty($data_re)){
	      	$data_up = array(
	      		'ProductID_history' => 	1
	      	);
	      	$this->web_product_model_model->update_by_genid($data_up,$ren_id);
	      }
	      

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_model_data(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $product_id = $this->uri->segment(3);	

	      $data_re = $this->web_product_model_model->select_by_product_id_join($product_id);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_model_data_by_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $model_id = $this->uri->segment(3);	

	      $data_re = $this->web_product_model_model->select_by_id($model_id);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function edit_product_model_data(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$model_id = $this->input->post('model_id');
	    	$model1 = $this->input->post('model1');
			$model2 = $this->input->post('model2');
			$title1 = $this->input->post('title1');
			$title2 = $this->input->post('title2');
			$model_price = $this->input->post('model_price');

			$arr_data = array(
				'ProductModelGroupID1' => $model1,
				'ProductModelGroupID2' => $model2,
				'title1' => $title1,
				'title2' => $title2,
				'price' => $model_price
			);

	      $data_re = $this->web_product_model_model->update($arr_data,$model_id);

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function del_product_model_data(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $model_id = $this->uri->segment(3);

	      $data_re = $this->web_product_model_model->delete($model_id);

	      if(!empty($data_re)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_by_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $product_id_en = $this->uri->segment(3);	
	      $product_id = $this->encryption_util->decrypt_ssl($product_id_en);	

	      $data_re = $this->web_products_model->select_by_id($product_id);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_product_model_by_proid(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $product_id_en = $this->uri->segment(3);	
	      $product_id = $this->encryption_util->decrypt_ssl($product_id_en);	

	      $data_map = $this->product_map_model->select_by_product_id($product_id);

	      $data_re = "";

	      if(!empty($data_map)){
	      	$arr_map = $this->manage_val_bl->make_id_in($data_map,'product_id');

	      	$data_re = $this->web_products_model->select_by_map_product($arr_map);
	      }
	      

	      

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}


 }