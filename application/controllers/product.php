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
		$this->load->library('util/random_util');
		$this->load->library('util/cookie_util');


		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/upload_bl');
		$this->load->library('pagination');

        $this->auth_bl->check_session_exists();

     }
     
	public function product_list()
	{	

		delete_cookie('cookie_product_cat_search');
		delete_cookie('cookie_txt_product_search');

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo ">>shop id>>>>>".$sess_shop_id;

		$arr_search = array(
 			'product_cat_search' => '',
 			'txt_product_search' => ''
 		);

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$data_curl_row = array(
			'product_cat_search' => '',
			'txt_product_search' => '',
			'shop_id_en' => $sess_shop_id
		);

		$arr_product_cnt = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search_cnt',$data_curl_row);

		$product_cnt = count($arr_product_cnt['Data']);

				
		$config['base_url'] =  base_url().'product/product_list_search';
		$config['total_rows'] = $product_cnt;
	    $config['first_url'] = base_url()."product/product_list_search/0/?search_type=2";
	    $config['suffix'] = "/?search_type=2";
		$config['per_page'] = 5;
	    $config['num_links'] = 3;
        $config['uri_segment']  = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="disabled page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active page-item"><a  class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $total_record = $config['total_rows'];
        $this->pagination->initialize($config);
        $pagination_link = $this->pagination->create_links();

        $offset = $this->uri->segment(3);
			
		if(empty($offset)){
			$offset = 0;
		}

        $data_curl = array(
			'product_cat_search' => '',
			'txt_product_search' => '',
			'per_page' => 5,
			'offset' => $offset,
			'shop_id_en' => $sess_shop_id
		);

		$arr_products = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search',$data_curl);

		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$sess_shop_id);

		if($arr_products['Status'] == "Success"){
			$max = sizeof($arr_products['Data']);

			for($i=0;$i<$max;$i++){
				$arr_products['Data'][$i]['ProductID'] = $this->encryption_util->encrypt_ssl($arr_products['Data'][$i]['ProductID']);
			}
		}

		$arr_input = array(
			'title' => "Home Admin BNY"
		);

		$arr_css = array(
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$arr_js = array(
			
			'product_cal_price' => base_url()."resources/js/product/product_cal_price.js",
        	'product_link' => base_url()."resources/js/product/product_link.js"
    	);	
				

		$data = array(
			'arr_products' => $arr_products['Data'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'pagination_link' => $pagination_link,
			'arr_search' => $arr_search
		);        

				        
		$this->view_util->load_view_main('product/product_list',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	public function product_list_search()
	{
		$search_type= $_REQUEST['search_type'];

		if($search_type == "1"){
			$product_cat_search = $this->input->post('product_cat_search');
			$txt_product_search = $this->input->post('txt_product_search');

			$arr_search = array(
	 			'product_cat_search' => $product_cat_search,
	 			'txt_product_search' => $txt_product_search
	 		);

			$this->cookie_util->cookie_create('cookie_product_cat_search',$product_cat_search);
			$this->cookie_util->cookie_create('cookie_txt_product_search',$txt_product_search);

			/*$cookie_product_cat_search = array(
			    'name'   => 'cookie_product_cat_search',
			    'value'  => $product_cat_search,
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);

			$cookie_txt_product_search = array(
			    'name'   => 'cookie_txt_product_search',
			    'value'  => $txt_product_search,
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);

			$this->input->set_cookie($cookie_product_cat_search);
			$this->input->set_cookie($cookie_txt_product_search);*/
		}elseif($search_type == "2"){

			$product_cat_search = get_cookie('cookie_product_cat_search');
			$txt_product_search = get_cookie('cookie_txt_product_search');

			$arr_search = array(
	 			'product_cat_search' => $product_cat_search,
	 			'txt_product_search' => $txt_product_search
	 		);

		}else{
			redirect(base_url().'product/product_list','refresh');
		}


		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$arr_input = array(
			'title' => "Home Admin BNY"
		);

		$arr_css = array(
			'pagination' => base_url()."assets/examples/css/structure/pagination.css",
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$arr_js = array(
			
			'product_cal_price' => base_url()."resources/js/product/product_cal_price.js",
        	'product_link' => base_url()."resources/js/product/product_link.js"
    	);	

		$data_curl_row = array(
			'product_cat_search' => $product_cat_search,
			'txt_product_search' => $txt_product_search,
			'shop_id_en' => $sess_shop_id
		);

		$arr_product_cnt = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search_cnt',$data_curl_row);

		$product_cnt = count($arr_product_cnt['Data']);

				
		$config['base_url'] =  base_url().'product/product_list_search';
		$config['total_rows'] = $product_cnt;
	    $config['first_url'] = base_url()."product/product_list_search/0/?search_type=2";
	    $config['suffix'] = "/?search_type=2";
		$config['per_page'] = 5;
	    $config['num_links'] = 3;
        $config['uri_segment']  = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="disabled page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active page-item"><a  class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $total_record = $config['total_rows'];
        $this->pagination->initialize($config);
        $pagination_link = $this->pagination->create_links();

        $offset = $this->uri->segment(3);
			
		if(empty($offset)){
			$offset = 0;
		}

        $data_curl = array(
			'product_cat_search' => $product_cat_search,
			'txt_product_search' => $txt_product_search,
			'per_page' => 5,
			'offset' => $offset,
			'shop_id_en' => $sess_shop_id
		);

		//print_r($data_curl);

		$arr_products = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search',$data_curl);

		//print_r($arr_products);

		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$sess_shop_id);

		if($arr_products['Status'] == "Success"){
			$max = sizeof($arr_products['Data']);

			for($i=0;$i<$max;$i++){
				$arr_products['Data'][$i]['ProductID'] = $this->encryption_util->encrypt_ssl($arr_products['Data'][$i]['ProductID']);
			}
		}



		$data = array(
			'arr_products' => $arr_products['Data'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_search' => $arr_search,
			'pagination_link' => $pagination_link,
			'total_record' => $total_record
			
		);        
        
		$this->view_util->load_view_main('product/product_list',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	public function add_product_form(){

		$parent_id = $this->uri->segment(3);

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$arr_input = array(
			'title' => "Product"
		);

		$arr_css = array(
			'boot' =>'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
			'summernote' => base_url().'global/vendor/summernote/summernote.css',
			'fileupload' => base_url().'global/vendor/fileupload/css/jquery.fileupload.css',
			'fileupload_ui' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui.css',
			'carbon' => base_url().'global/vendor/fileupload/css/vendor/carbon.css',
			'pintura' => base_url().'global/vendor/fileupload/css/vendor/pintura.min.css',
			'tokenfield' => base_url().'global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.css',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			//'fileupload-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-noscript.css',
			//'fileupload-ui-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui-noscript.css',
		);
		
		$arr_js = array(
			'jquery1.8.3' => base_url().'global/vendor/jquery/jquery-1.8.3.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			
			//'jquery_12' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'summernote' => base_url()."global/vendor/summernote/summernote.min.js",
        	//'add_product_js' => base_url()."resources/js/product/add_product_js.js",

        	'jquery-ui' => base_url()."global/vendor/fileupload/js/vendor/jquery.ui.widget.js",
        	'tmpl' => base_url()."global/vendor/blueimp-tmpl/tmpl.js",
        	'load-image' => base_url()."global/vendor/blueimp-load-image/load-image.all.min.js",
        	'canvas-to-blob' => base_url()."global/vendor/blueimp-canvas-to-blob/canvas-to-blob.js",
        	//'blueimp-gallery' => base_url()."global/vendor/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js",
        	
        	'iframe-transport' => base_url()."global/vendor/fileupload/js/jquery.iframe-transport.js",
        	'fileupload_js' => base_url()."global/vendor/fileupload/js/jquery.fileupload.js",
        	'fileupload_process' => base_url()."global/vendor/fileupload/js/jquery.fileupload-process.js",
        	'fileupload_image' => base_url()."global/vendor/fileupload/js/jquery.fileupload-image.js",
        	'fileupload_audio' => base_url()."global/vendor/fileupload/js/jquery.fileupload-audio.js",
        	'fileupload_video' => base_url()."global/vendor/fileupload/js/jquery.fileupload-video.js",
        	'fileupload_validate' => base_url()."global/vendor/fileupload/js/jquery.fileupload-validate.js",
        	'fileupload_ui' => base_url()."global/vendor/fileupload/js/jquery.fileupload-ui.js",
        	'tokenfield' => base_url()."global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.min.js",
        	'bootstrap-tokenfield' => base_url()."global/js/Plugin/bootstrap-tokenfield.js",
        	//'pintura_js' => base_url()."global/vendor/fileupload/js/pintura_js.js",
        	'pintura_product' => base_url()."resources/js/pintura/product.js",
        	'product_price_model' => base_url()."resources/js/product/product_price_model_v2.js",
        	'fancy_checkbox' => base_url().'resources/js/fancybox/fancy_checkbox.js',
        	'fancy_cat' => base_url().'resources/js/fancybox/fancy_cat.js',
        	'product_add' => base_url().'resources/js/validate/product_add.js'


    	);	

    	$datenow = date("YmdHis");			
		$ran_num = $this->random_util->create_random_number(6);	
		
		$data = array(
			'parent_id' => $parent_id,
			'arr_list_cats' => $arr_list_cats['Data'],
			'shop_id_en' => $sess_shop_id,
			'ran_id' => $datenow.$ran_num
		);

		
		$this->view_util->load_view_main('product/add_product_form',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);
	}

	public function add_product_form_bk(){

		$parent_id = $this->uri->segment(3);

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$arr_input = array(
			'title' => "Product"
		);

		$arr_css = array(
			//'boot' =>'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
			'summernote' => base_url().'global/vendor/summernote/summernote.css',
			'fileupload' => base_url().'global/vendor/fileupload/css/jquery.fileupload.css',
			'fileupload_ui' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui.css',
			'carbon' => base_url().'global/vendor/fileupload/css/vendor/carbon.css',
			'pintura' => base_url().'global/vendor/fileupload/css/vendor/pintura.min.css',
			//'fileupload-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-noscript.css',
			//'fileupload-ui-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui-noscript.css',
		);
		
		$arr_js = array(
			//'jquery_12' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'summernote' => base_url()."global/vendor/summernote/summernote.min.js",
        	'add_product_js' => base_url()."resources/js/product/add_product_js.js",

        	'jquery-ui' => base_url()."global/vendor/fileupload/js/vendor/jquery.ui.widget.js",
        	'tmpl' => base_url()."global/vendor/blueimp-tmpl/tmpl.js",
        	'load-image' => base_url()."global/vendor/blueimp-load-image/load-image.all.min.js",
        	'canvas-to-blob' => base_url()."global/vendor/blueimp-canvas-to-blob/canvas-to-blob.js",
        	//'blueimp-gallery' => base_url()."global/vendor/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js",
        	
        	'iframe-transport' => base_url()."global/vendor/fileupload/js/jquery.iframe-transport.js",
        	'fileupload_js' => base_url()."global/vendor/fileupload/js/jquery.fileupload.js",
        	'fileupload_process' => base_url()."global/vendor/fileupload/js/jquery.fileupload-process.js",
        	'fileupload_image' => base_url()."global/vendor/fileupload/js/jquery.fileupload-image.js",
        	'fileupload_audio' => base_url()."global/vendor/fileupload/js/jquery.fileupload-audio.js",
        	'fileupload_video' => base_url()."global/vendor/fileupload/js/jquery.fileupload-video.js",
        	'fileupload_validate' => base_url()."global/vendor/fileupload/js/jquery.fileupload-validate.js",
        	'fileupload_ui' => base_url()."global/vendor/fileupload/js/jquery.fileupload-ui.js",
        	//'pintura_js' => base_url()."global/vendor/fileupload/js/pintura_js.js",
        	'pintura_product' => base_url()."resources/js/pintura/product.js",

    	);	

    	$datenow = date("YmdHis");			
		$ran_num = $this->random_util->create_random_number(6);	
		
		$data = array(
			'parent_id' => $parent_id,
			'arr_list_cats' => $arr_list_cats['Data'],
			'shop_id_en' => $sess_shop_id,
			'ren_id' => $datenow.$ran_num
		);

		
		$this->view_util->load_view_main('product/add_product_form',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);
	}

	function product_add_model(){

		$json_model =$this->input->post('arr_model');
		$arr_model = json_decode($json_model);
		//print_r($arr_model);

		$json_model2 =$this->input->post('arr_model2');
		$arr_model2 = json_decode($json_model2);
		//print_r($arr_model2);
		$num = count($arr_model);
		$num2 = count($arr_model2);
		$num3 = 1;
		$arr_curl = array();
		if(($num > 0) and ($num2>0)){
			for ($i=0; $i < $num ; $i++) { 
				$txtval = "text".$arr_model[$i];
				$txt =$this->input->post($txtval);

				for ($j=0; $j < $num2; $j++) {
					$txtval2 = "text2_".$arr_model2[$j];
					$txt2 =$this->input->post($txtval2);
					$name_price = "price_".$num3;
					$name_quantity = "quantity_".$num3;
					$price = $this->input->post($name_price);
					$quantity = $this->input->post($name_quantity);

					//echo $txt."--".$txt2."<br>";
					$arr_data = array(
						'ProductModelGroup1' => 'สี',
						'ProductModelGroup2' => 'ขนาด',
						'title1' => $txt,
						'title2' => $txt2,
						'icon1' => 'file1',
						'icon2' => 'file2',
						'price' => $price,
						'quantity' => $quantity,
					);
					array_push($arr_curl,$arr_data);
					$num3 = $num3+1;
				}

				$filename = "file".$arr_model[$i];


				//$icon_name = $this->upload_bl->upload_file_pic($filename);

				//echo $icon_name."<br>"; 


			}

			//print_r($arr_curl);

			$arr_curl = json_encode($arr_curl);

			$data_curl = array(
				'arr_curl' => $arr_curl
			);

			$arr_res = $this->curl_bl->CallApi('POST','product/add_product_model_data_group',$data_curl);

			if(empty($arr_res['Data'])){
				echo 'true';
			}else{
				echo 'false';
			}
		}


	}

	function product_add(){

		$Title = $this->input->post('Title');
		$ProductCategoryID = $this->input->post('ProductCategoryID');
		$Sku = $this->input->post('Sku');
		$Unit = $this->input->post('Unit');
		$Price = $this->input->post('Price');
		$Pricesale = $this->input->post('Pricesale');
		$Weight = $this->input->post('Weight');
		$Dimension = $this->input->post('Dimension');
		$Condition = $this->input->post('Condition');
		$id_shop = $this->session->userdata('shop_id');
		$Description = $this->input->post('Description');
		$product_quality = $this->input->post('product_quality');
		$is_model = $this->input->post('is_model');

		$ran_id = $this->input->post('ran_id');
		//echo $Description;

		//$countfiles = count($_FILES['files']['name']);
		//print_r($_FILES['files']);
		//echo $countfiles;

		if (!empty($_FILES['files']['name'][0])) {
            if ($this->upload_bl->upload_multi_files('./uploads/products/', $_FILES['files']) === FALSE) {
                echo "Error up load";
            }else{
            	echo "Upload";
            }
        }else{
        	echo "Noimages";
        }

        $is_main_product = 1;

        //---- Data Model
        $variant1 =$this->input->post('variant1');
        $variant2 =$this->input->post('variant2');

        /*$json_model =$this->input->post('arr_model');
		$arr_model = json_decode($json_model);
		//print_r($arr_model);

		$json_model2 =$this->input->post('arr_model2');
		$arr_model2 = json_decode($json_model2);
		//print_r($arr_model2);
		$num = count($arr_model);
		$num2 = count($arr_model2);
		$num3 = 1;*/


		$variant1_val =$this->input->post('variant1_val');
		$variant2_val =$this->input->post('variant2_val');

		$val1_ex = explode(",",$variant1_val);
		$val2_ex = explode(",",$variant2_val);
		$num = count($val1_ex);
		$num2 = count($val2_ex);
		
		$num4 = 1;

        //-----

		$parent_no_child = 1;
		if(($num > 0) and ($num2>0)){
			$parent_no_child = 0;
		}

        $data_curl = array(
			'Title' => $Title,
			'product_choice' => '',
			'ProductCategoryID' => $ProductCategoryID,
			'Sku' => $Sku,
			'Unit' => $Unit,
			'Cost_price' => $Price,
			'Price' => $Pricesale,
			'is_model' => $is_model,
			'product_variant1' => $variant1,
			'product_variant2' => $variant2,
			'product_variant_value1' => $variant1_val,
			'product_variant_value2' => $variant2_val,
			'product_quality' => $product_quality,
			'Weight' => $Weight,
			'Dimension' => $Dimension,
			'Condition' => $Condition,
			'Description' => $Description,
			'ShopID' => $id_shop,
			'is_main_product' => 1,
			'parent_no_child' => $parent_no_child,
			'sku_ran_id' => $ran_id
		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','product/product_add',$data_curl);

		$product_parent_id = $arr_res['Data'];
		//print_r($arr_res);

		//-----add product model

		$arr_curl = array();
		if(($num > 0) and ($num2>0)){
			for ($i=0; $i < $num ; $i++) { 

				$txt = $val1_ex[$i];
				$num3 = 1;

				for ($j=0; $j < $num2; $j++) {
	
					$txt2 = $val2_ex[$j];
					$name_price = "price_".$num4."_".$num3;
					$name_quantity = "quantity_".$num4."_".$num3;
					$name_weight = "weight_".$num4."_".$num3;
					$price_m = $this->input->post($name_price);
					$quantity_m = $this->input->post($name_quantity);
					$weight_m = $this->input->post($name_weight);

					$product_choice = $txt."-".$txt2;

					//echo $txt."--".$txt2."<br>";
					$arr_data = array(
						'Title' => $Title,
						'product_choice' => $product_choice,
						'ProductCategoryID' => $ProductCategoryID,
						'Sku' => $Sku,
						'Unit' => $Unit,
						'Price' => $price_m,
						'product_variant1' => $variant1,
						'product_variant2' => $variant2,
						'product_quality' => $quantity_m,
						'Weight' => $weight_m,
						'Dimension' => $Dimension,
						'Condition' => $Condition,
						'Description' => $Description,
						'ShopID' => $id_shop,
						'is_main_product' => 0,
						'product_parent_id' => $product_parent_id
					);
					
					array_push($arr_curl,$arr_data);
					$num3 = $num3+1;
				}
				$num4 = $num4+1;
			}
			/*

			$Title = $arr_curl[$i]['Title'];
				$product_choice = $arr_curl[$i]['product_choice'];
				$ProductCategoryID = $arr_curl[$i]['ProductCategoryID'];
				$Sku = $arr_curl[$i]['Sku'];
				$Unit = $arr_curl[$i]['Unit'];
				$Price = $arr_curl[$i]['Price'];
				$product_variant1 = $arr_curl[$i]['product_variant1'];
				$product_variant2 = $arr_curl[$i]['product_variant2'];
				$product_quality = $arr_curl[$i]['product_quality'];
				$Weight = $arr_curl[$i]['Weight'];
				$Dimension = $arr_curl[$i]['Dimension'];
				$Condition = $arr_curl[$i]['Condition'];
				$Description = $arr_curl[$i]['Description'];
				$ShopID_en = $arr_curl[$i]['ShopID'];
				$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
				$is_main_product = $arr_curl[$i]['is_main_product'];
				$product_parent_id =  $arr_curl[$i]['product_parent_id'];

				$Title = $arr_curl['Title'];
				$product_choice = $arr_curl['product_choice'][$i];
				$ProductCategoryID = $arr_curl['ProductCategoryID'][$i];
				$Sku = $arr_curl['Sku'][$i];
				$Unit = $arr_curl['Unit'][$i];
				$Price = $arr_curl['Price'][$i];
				$product_variant1 = $arr_curl['product_variant1'][$i];
				$product_variant2 = $arr_curl['product_variant2'][$i];
				$product_quality = $arr_curl['product_quality'][$i];
				$Weight = $arr_curl['Weight'][$i];
				$Dimension = $arr_curl['Dimension'][$i];
				$Condition = $arr_curl['Condition'][$i];
				$Description = $arr_curl['Description'][$i];
				$ShopID_en = $arr_curl['ShopID'][$i];
				$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
				$is_main_product = $arr_curl['is_main_product'][$i];
				$product_parent_id =  $arr_curl['product_parent_id'][$i];

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

			*/

		//print_r($arr_curl);

				//$filename = "file".$arr_model[$i];

				$arr_curl = json_encode($arr_curl);

				$data_model_curl = array(
					'arr_curl' => $arr_curl
				);



				$this->curl_bl->CallApi('POST','product/product_add_model_set',$data_model_curl);
				//$icon_name = $this->upload_bl->upload_file_pic($filename);

				//echo $icon_name."<br>"; 
		}	



		//----- end add product model

		/*if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'product/product_list','refresh');*/
	}

	function edit_product_form(){

		$product_id_en = $this->uri->segment(3);

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$arr_product = $this->curl_bl->CallApi('GET','product/get_product_by_id/'.$product_id_en);

		$arr_product_models = $this->curl_bl->CallApi('GET','product/get_product_model_by_proid/'.$product_id_en);

		//print_r($arr_product_models);

		$arr_input = array(
			'title' => "Product"
		);

		$arr_css = array(
			'boot' =>'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
			'summernote' => base_url().'global/vendor/summernote/summernote.css',
			'fileupload' => base_url().'global/vendor/fileupload/css/jquery.fileupload.css',
			'fileupload_ui' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui.css',
			'carbon' => base_url().'global/vendor/fileupload/css/vendor/carbon.css',
			'pintura' => base_url().'global/vendor/fileupload/css/vendor/pintura.min.css',
			'tokenfield' => base_url().'global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.css',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			//'fileupload-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-noscript.css',
			//'fileupload-ui-noscript' => base_url().'global/vendor/fileupload/css/jquery.fileupload-ui-noscript.css',
		);
		
		$arr_js = array(
			'jquery1.8.3' => base_url().'global/vendor/jquery/jquery-1.8.3.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			
			//'jquery_12' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'summernote' => base_url()."global/vendor/summernote/summernote.min.js",
        	//'add_product_js' => base_url()."resources/js/product/add_product_js.js",

        	'jquery-ui' => base_url()."global/vendor/fileupload/js/vendor/jquery.ui.widget.js",
        	'tmpl' => base_url()."global/vendor/blueimp-tmpl/tmpl.js",
        	'load-image' => base_url()."global/vendor/blueimp-load-image/load-image.all.min.js",
        	'canvas-to-blob' => base_url()."global/vendor/blueimp-canvas-to-blob/canvas-to-blob.js",
        	//'blueimp-gallery' => base_url()."global/vendor/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js",
        	
        	'iframe-transport' => base_url()."global/vendor/fileupload/js/jquery.iframe-transport.js",
        	'fileupload_js' => base_url()."global/vendor/fileupload/js/jquery.fileupload.js",
        	'fileupload_process' => base_url()."global/vendor/fileupload/js/jquery.fileupload-process.js",
        	'fileupload_image' => base_url()."global/vendor/fileupload/js/jquery.fileupload-image.js",
        	'fileupload_audio' => base_url()."global/vendor/fileupload/js/jquery.fileupload-audio.js",
        	'fileupload_video' => base_url()."global/vendor/fileupload/js/jquery.fileupload-video.js",
        	'fileupload_validate' => base_url()."global/vendor/fileupload/js/jquery.fileupload-validate.js",
        	'fileupload_ui' => base_url()."global/vendor/fileupload/js/jquery.fileupload-ui.js",
        	'tokenfield' => base_url()."global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.min.js",
        	'bootstrap-tokenfield' => base_url()."global/js/Plugin/bootstrap-tokenfield.js",
        	//'pintura_js' => base_url()."global/vendor/fileupload/js/pintura_js.js",
        	'pintura_product' => base_url()."resources/js/pintura/product.js",
        	'product_price_model' => base_url()."resources/js/product/product_price_model_v2.js",
        	'fancy_checkbox' => base_url().'resources/js/fancybox/fancy_checkbox.js',
        	'fancy_cat' => base_url().'resources/js/fancybox/fancy_cat.js',
        	'product_add' => base_url().'resources/js/validate/product_add.js',
        	'edit_product_js' => base_url().'resources/js/product/edit_product_js.js'

    	);	


		
		$data = array(
			'product_id_en' => $product_id_en,
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_product' => $arr_product['Data'],
			'arr_product_models' => $arr_product_models['Data'],
			'shop_id_en' => $sess_shop_id
		);

		
		$this->view_util->load_view_main('product/edit_product_form',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);

	}

	function product_edit(){

		$Title = $this->input->post('Title');
		$ProductCategoryID = $this->input->post('ProductCategoryID');
		$Sku = $this->input->post('Sku');
		$Unit = $this->input->post('Unit');
		$Price = $this->input->post('Price');
		$Pricesale = $this->input->post('Pricesale');
		$Weight = $this->input->post('Weight');
		$Dimension = $this->input->post('Dimension');
		$Condition = $this->input->post('Condition');
		$id_shop = $this->session->userdata('shop_id');
		$Description = $this->input->post('Description');
		$product_quality = $this->input->post('product_quality');
		$is_model = $this->input->post('is_model');

		$ran_id = $this->input->post('ran_id');
		//echo $Description;

		//$countfiles = count($_FILES['files']['name']);
		//print_r($_FILES['files']);
		//echo $countfiles;

		if (!empty($_FILES['files']['name'][0])) {
            if ($this->upload_bl->upload_multi_files('./uploads/products/', $_FILES['files']) === FALSE) {
                echo "Error up load";
            }else{
            	echo "Upload";
            }
        }else{
        	echo "Noimages";
        }

        $is_main_product = 1;

        //---- Data Model
        $variant1 =$this->input->post('variant1');
        $variant2 =$this->input->post('variant2');

		$variant1_val =$this->input->post('variant1_val');
		$variant2_val =$this->input->post('variant2_val');

		$val1_ex = explode(",",$variant1_val);
		$val2_ex = explode(",",$variant2_val);
		$num = count($val1_ex);
		$num2 = count($val2_ex);
		
		$num4 = 1;

        //-----

		$parent_no_child = 1;
		if(($num > 0) and ($num2>0)){
			$parent_no_child = 0;
		}

        $data_curl = array(
			'Title' => $Title,
			'product_choice' => '',
			'ProductCategoryID' => $ProductCategoryID,
			'Sku' => $Sku,
			'Unit' => $Unit,
			'Cost_price' => $Price,
			'Price' => $Pricesale,
			'is_model' => $is_model,
			'product_variant1' => $variant1,
			'product_variant2' => $variant2,
			'product_variant_value1' => $variant1_val,
			'product_variant_value2' => $variant2_val,
			'product_quality' => $product_quality,
			'Weight' => $Weight,
			'Dimension' => $Dimension,
			'Condition' => $Condition,
			'Description' => $Description,
			'parent_no_child' => $parent_no_child
		);

		//print_r($data_curl);

		$arr_res = $this->curl_bl->CallApi('POST','product/product_add',$data_curl);

		$product_parent_id = $arr_res['Data'];
		//print_r($arr_res);

		//-----add product model

		$arr_curl = array();
		if(($num > 0) and ($num2>0)){
			for ($i=0; $i < $num ; $i++) { 

				$txt = $val1_ex[$i];
				$num3 = 1;

				for ($j=0; $j < $num2; $j++) {
	
					$txt2 = $val2_ex[$j];
					$name_price = "price_".$num4."_".$num3;
					$name_quantity = "quantity_".$num4."_".$num3;
					$name_weight = "weight_".$num4."_".$num3;
					$price_m = $this->input->post($name_price);
					$quantity_m = $this->input->post($name_quantity);
					$weight_m = $this->input->post($name_weight);

					$product_choice = $txt."-".$txt2;

					//echo $txt."--".$txt2."<br>";
					$arr_data = array(
						'Title' => $Title,
						'product_choice' => $product_choice,
						'ProductCategoryID' => $ProductCategoryID,
						'Sku' => $Sku,
						'Unit' => $Unit,
						'Price' => $price_m,
						'product_variant1' => $variant1,
						'product_variant2' => $variant2,
						'product_quality' => $quantity_m,
						'Weight' => $weight_m,
						'Dimension' => $Dimension,
						'Condition' => $Condition,
						'Description' => $Description,
					);
					
					array_push($arr_curl,$arr_data);
					$num3 = $num3+1;
				}
				$num4 = $num4+1;
			}
			
		}
		//print_r($arr_curl);

				//$filename = "file".$arr_model[$i];

				$arr_curl = json_encode($arr_curl);

				$data_model_curl = array(
					'arr_curl' => $arr_curl
				);



				$this->curl_bl->CallApi('POST','product/product_add_model_set',$data_model_curl);
				//$icon_name = $this->upload_bl->upload_file_pic($filename);

				//echo $icon_name."<br>"; 
		}	

	function edit_product_group(){
		$un_arr_id = $this->input->post('un_arr_id');
		$arr_id = $this->input->post('arr_id');
		$DiscountType = $this->input->post('DiscountType');
		$DiscountAmount = $this->input->post('DiscountAmount');
		$DiscountAmountType = $this->input->post('DiscountAmountType');
		$PriceMain = $this->input->post('PriceMain');
		$PriceMainType = $this->input->post('PriceMainType');
		$PriceMainAmount = $this->input->post('PriceMainAmount');

		$un_arr_id = json_encode($un_arr_id);
		$arr_id = json_encode($arr_id);

		$data_curl = array(
			'un_arr_id' => $un_arr_id,
			'arr_id' => $arr_id,
			'DiscountType' => $DiscountType,
			'DiscountAmount' => $DiscountAmount,
			'DiscountAmountType' => $DiscountAmountType,
			'PriceMain' => $PriceMain,
			'PriceMainType' => $PriceMainType,
			'PriceMainAmount' => $PriceMainAmount
		);

		$arr_res = $this->curl_bl->CallApi('POST','product/edit_product_group',$data_curl);

		echo 'true';

	}

	function get_product_model_ajax(){

		$shop_id_en = $this->input->post('shop_id_en');
		$arr_models = $this->curl_bl->CallApi('GET','product/get_product_model/'.$shop_id_en);

		$arr_data = array(
			'arr_models' => $arr_models['Data']
		);
		echo json_encode($arr_data);
	}

	function add_product_model(){

		$model_name = $this->input->post('model_name');
		$shop_id_en = $this->input->post('shop_id_en');

		$data_curl = array(
			'model_name' => $model_name,
			'shop_id_en' => $shop_id_en
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApi('POST','product/add_product_model',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}

	}

	function edit_product_model(){

		$model_name = $this->input->post('model_name');
		$mogel_group_id = $this->input->post('mogel_group_id');

		$data_curl = array(
			'model_name' => $model_name,
			'mogel_group_id' => $mogel_group_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApi('POST','product/edit_product_model',$data_curl);

		echo 'true';

	}

	function get_model_by_id(){

		$mogel_group_id = $this->input->post('mogel_group_id');
		$arr_model = $this->curl_bl->CallApi('GET','product/get_model_by_id/'.$mogel_group_id);

		$arr_data = array(
			'arr_model' => $arr_model['Data']
		);
		echo json_encode($arr_data);

	}

	function del_product_model(){

		$mogel_group_id = $this->input->post('mogel_group_id');
		$arr_model = $this->curl_bl->CallApi('GET','product/del_product_model/'.$mogel_group_id);

		$arr_data = array(
			'arr_model' => $arr_model['Data']
		);
		echo json_encode($arr_data);

	}

	function add_product_model_data(){

		$model1 = $this->input->post('model1');
		$model2 = $this->input->post('model2');
		$title1 = $this->input->post('title1');
		$title2 = $this->input->post('title2');
		$model_price = $this->input->post('model_price');
		$ren_id = $this->input->post('ren_id');

		$icon1 = 'fileUpload1';
		$icon2 = 'fileUpload2';

		$icon_name1 = $this->upload_bl->upload_file_pic($icon1);
		$icon_name2 = $this->upload_bl->upload_file_pic($icon2);


		$data_curl = array(
			'model1' => $model1,
			'model2' => $model2,
			'title1' => $title1,
			'title2' => $title2,
			'icon1' => $icon_name1,
			'icon2' => $icon_name2,
			'model_price' => $model_price,
			'ren_id' => $ren_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApi('POST','product/add_product_model_data',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}

	}

	function get_product_model_data_ajax(){

		$product_id = $this->input->post('product_id');
		$arr_model_datas = $this->curl_bl->CallApi('GET','product/get_product_model_data/'.$product_id);

		$arr_data = array(
			'arr_model_datas' => $arr_model_datas['Data']
		);
		echo json_encode($arr_data);
	}

	function get_model_data_by_id(){

		$model_id = $this->input->post('model_id');
		$arr_model = $this->curl_bl->CallApi('GET','product/get_model_data_by_id/'.$model_id);

		$arr_data = array(
			'arr_model_data' => $arr_model['Data']
		);
		echo json_encode($arr_data);

	}

	function edit_product_model_data(){

		$model1 = $this->input->post('model1');
		$model2 = $this->input->post('model2');
		$title1 = $this->input->post('title1');
		$title2 = $this->input->post('title2');
		$model_price = $this->input->post('model_price');
		$edit_model_id = $this->input->post('edit_model_id');

		$data_curl = array(
			'model1' => $model1,
			'model2' => $model2,
			'title1' => $title1,
			'title2' => $title2,
			'model_price' => $model_price,
			'model_id' => $edit_model_id
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApi('POST','product/edit_product_model_data',$data_curl);

		echo 'true';

	}

	function del_product_model_data(){

		$model_id = $this->input->post('edit_model_id');
		$arr_model = $this->curl_bl->CallApi('GET','product/del_product_model_data/'.$model_id);

		$arr_data = array(
			'arr_model' => $arr_model['Data']
		);
		echo json_encode($arr_data);

	}

	function product_stock_list()
	{	

		delete_cookie('cookie_product_stock_cat_search');
		delete_cookie('cookie_txt_product_stock_search');

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo ">>shop id>>>>>".$sess_shop_id;

		$arr_search = array(
 			'product_cat_search' => '',
 			'txt_product_search' => ''
 		);

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);

		$data_curl_row = array(
			'product_cat_search' => '',
			'txt_product_search' => '',
			'shop_id_en' => $sess_shop_id
		);

		$arr_product_cnt = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search_cnt',$data_curl_row);

		$product_cnt = count($arr_product_cnt['Data']);

				
		$config['base_url'] =  base_url().'product/product_stock_list_search';
		$config['total_rows'] = $product_cnt;
	    $config['first_url'] = base_url()."product/product_stock_list_search/0/?search_type=2";
	    $config['suffix'] = "/?search_type=2";
		$config['per_page'] = 5;
	    $config['num_links'] = 3;
        $config['uri_segment']  = 3;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="disabled page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active page-item"><a  class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $total_record = $config['total_rows'];
        $this->pagination->initialize($config);
        $pagination_link = $this->pagination->create_links();

        $offset = $this->uri->segment(3);
			
		if(empty($offset)){
			$offset = 0;
		}

        $data_curl = array(
			'product_cat_search' => '',
			'txt_product_search' => '',
			'per_page' => 5,
			'offset' => $offset,
			'shop_id_en' => $sess_shop_id
		);

		$arr_products = $this->curl_bl->CallApi('POST','product/get_product_by_shop_search',$data_curl);

		//$arr_products = $this->curl_bl->CallApi('GET','product/get_product_by_shop/'.$sess_shop_id);

		if($arr_products['Status'] == "Success"){
			$max = sizeof($arr_products['Data']);

			for($i=0;$i<$max;$i++){
				$arr_products['Data'][$i]['ProductID'] = $this->encryption_util->encrypt_ssl($arr_products['Data'][$i]['ProductID']);
			}
		}

		$arr_input = array(
			'title' => "Home Admin BNY"
		);

		$arr_css = array(
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$arr_js = array(
			
			'product_cal_stock' => base_url()."resources/js/product/product_cal_stock.js",
        	'product_link' => base_url()."resources/js/product/product_link.js"
    	);	
				

		$data = array(
			'arr_products' => $arr_products['Data'],
			'arr_list_cats' => $arr_list_cats['Data'],
			'pagination_link' => $pagination_link,
			'arr_search' => $arr_search
		);        

				        
		$this->view_util->load_view_main('product/product_stock_list',$data,$arr_css,$arr_js,$arr_input,MENU_DASHBOARD);

	}
}