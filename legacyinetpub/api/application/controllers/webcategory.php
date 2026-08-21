<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Webcategory extends CI_Controller
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
		$this->load->library('businesslogic/data_bl');
		$this->load->library('businesslogic/webcategory_bl');
		$this->load->library('businesslogic/selectbox_bl');

		$this->load->model('web_category_model');


		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_cat(){
     	$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id = $this->uri->segment(3);

	      $data = $this->web_category_model->select_by_id($id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }
     }

     function build_cat(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    $shop_id_en = $this->uri->segment(3);
	    $shop_id = $this->encryption_util->decrypt_ssl($shop_id_en);
	    $class = $this->uri->segment(4);

	    //echo $shop_id_en;

	   	$data_cat = $this->webcategory_bl->bulid_cat_by_parent($class,0,$shop_id);	
	   	//$data_re = $this->selectbox_bl->get_list_cat($data_cat['arr_cat'][0]);	
	   	//print_r($data_cat);
	    $data_re =	$data_cat['arr_cat'][0];

	      if(!empty($data_re)){
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

     function get_cat_root(){
     	$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shop_id_en = $this->uri->segment(3);
	   	  $shop_id = $this->encryption_util->decrypt_ssl($shop_id_en);

	   	  $class = $this->uri->segment(4);

	      $data = $this->web_category_model->select_root_by_shopid($class,$shop_id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }
     }


     function category_add_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$Title = $this->input->post('Title');
	    	$componentclass = $this->input->post('componentclass');
			$f_parentcategory = $this->input->post('f_parentcategory');
			$Description = $this->input->post('Description');
			$ShopID_en = $this->input->post('ShopID');
			$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
			$class = $this->input->post('cat_class');

			$data_curl = array(
				'Title' => $Title,
				'componentclass' => $class,
				'f_parentcategory' => $f_parentcategory,
				'Description' => $Description,
				'ShopID' => $ShopID,

			);

	      $data_re = $this->web_category_model->insert($data_curl);

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

	function category_edit_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$Title = $this->input->post('Title');
			$cat_id = $this->input->post('cat_id');
			$Description = $this->input->post('Description');


			$data_curl = array(
				'Title' => $Title,
				'Description' => $Description
			);

	      $data_re = $this->web_category_model->update($data_curl,$cat_id);

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

	function category_del_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id = $this->uri->segment(3);


	      $this->web_category_model->delete($id);

	      $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

     
}