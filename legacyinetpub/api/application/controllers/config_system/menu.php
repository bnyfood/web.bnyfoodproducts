<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Menu extends CI_Controller
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


		$this->load->model('menu_model');

		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_menu_all(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $data_menu = $this->menu_model->select_all();

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_by_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data_menu = $this->menu_model->select_by_id($id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function menu_add(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$parent_menu = $this->input->post('parent_menu');
	       	$menu_name = $this->input->post('menu_name');
			$link = $this->input->post('link');
			$icon = $this->input->post('icon');
			$sort = $this->input->post('sort');
			$show_customer = $this->input->post('show_customer');

			$parent_menu = $this->encryption_util->decrypt_ssl($parent_menu);

			$arr_menu = array(
				'parent_menu' => $parent_menu,
				'menu_name' => $menu_name,
				'link' => $link,
				'icon' => $icon,
				'sort' => $sort,
				'show_customer' => $show_customer
			);

	      $data_menu = $this->menu_model->insert($arr_menu);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_menu,'Insert Unsuccess',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function menu_edit(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$id_en = $this->input->post('id_en');
	       	$menu_name = $this->input->post('menu_name');
			$link = $this->input->post('link');
			$icon = $this->input->post('icon');
			$sort = $this->input->post('sort');
			$show_customer = $this->input->post('show_customer');

			$id = $this->encryption_util->decrypt_ssl($id_en);

			$arr_menu = array(
				'menu_name' => $menu_name,
				'link' => $link,
				'icon' => $icon,
				'sort' => $sort,
				'show_customer' => $show_customer
			);

	      $this->menu_model->update($arr_menu,$id);

	      /*if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Insert Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Fail',$data_menu,'Insert Unsuccess',$arr_header['api_token']); 
	      }*/
	      $data_json = $this->json_util->make_json('Select data','Success','','Edit Success',$arr_header['api_token']); 
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_sub_menu_all(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data_menu = $this->menu_model->select_by_parent($id);

	      if(!empty($data_menu)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menu,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function del_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);
	   	  $id = $this->encryption_util->decrypt_ssl($id_en);

	      $this->menu_model->delete($id);

	      $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}
  

    //if()

    //$de_token = $this->api_auth_bl->de_token($token);
   // echo $de_token;
 
 }