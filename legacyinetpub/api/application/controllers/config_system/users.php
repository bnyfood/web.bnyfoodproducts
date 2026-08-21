<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Users extends CI_Controller
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


		$this->load->model('api_authen_model');
		$this->load->model('web_bny_customer_model');
		$this->load->model('groupmapuser_model');

		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_users_all(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $data_user = $this->web_bny_customer_model->select_all();

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_users_by_group_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $groupid_en = $this->uri->segment(4);	
	      $groupid = $this->encryption_util->decrypt_ssl($groupid_en);

	      $data_user = $this->web_bny_customer_model->select_by_group_id($groupid);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_user_by_shop_id_join_usergroup(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    if($chk_auth['Status'] == "Success"){

	      $shopid_en = $this->uri->segment(4);
	      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

	      $data_user = $this->web_bny_customer_model->select_by_shop_id_join_usergroup($shopid);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']);
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']);
	      }

	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_users_by_shop_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shopid_en = $this->uri->segment(4);	
	      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

	      $data_user = $this->web_bny_customer_model->select_by_shop_id_group($shopid);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_usersgroup_by_user_id(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $user_id = $this->uri->segment(4);	

	      $data_user = $this->groupmapuser_model->select_by_user($user_id);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_users_by_code(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $customer_code = $this->uri->segment(4);	

	      $data_user = $this->web_bny_customer_model->select_by_code($customer_code);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function user_search(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    if($chk_auth['Status'] == "Success"){

	      $customer_code = $this->input->post('customer_code');	
	      $user_search = $this->input->post('user_search');
	      $sortby = $this->input->post('sortby');
	      $sorttype = $this->input->post('sorttype');
	      $offset = $this->input->post('offset');
	      $per_page = $this->input->post('per_page');

	      $data_user = $this->web_bny_customer_model->select_by_code_search($customer_code,$user_search,$per_page,$offset,$sortby,$sorttype);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_user_by_code_noid(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $groupid_en = $this->uri->segment(4);	
	      $groupid = $this->encryption_util->decrypt_ssl($groupid_en);
	      $customer_code = $this->uri->segment(5);	
	      $customer_type = $this->uri->segment(6);	

	      $data_user = $this->web_bny_customer_model->select_by_code_no_groupid($customer_code,$groupid,$customer_type);
	      //print_r($data_user);

	      if(!empty($data_user)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
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

	      $data = $this->web_bny_customer_model->select_by_id($id);

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

	function user_add(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$text_name = $this->input->post('text_name');
			$CompanyName = $this->input->post('CompanyName');
			$address1 = $this->input->post('address1');
			$province_sel = $this->input->post('province_sel');
			$district_sel = $this->input->post('district_sel');
			$subdistrict_sel = $this->input->post('subdistrict_sel');
			$Zip = $this->input->post('Zip');
			$Tax = $this->input->post('Tax');
			$Mobile = $this->input->post('Mobile');
			$Mobile2 = $this->input->post('Mobile2');
			$Line = $this->input->post('Line');
			$email = $this->input->post('email');
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$password = md5(SALT_PASSWORD.$password);
			$usergroup_id = $this->input->post('usergroup_id');
			$customer_type = $this->input->post('customer_type');
			$customer_code = $this->input->post('customer_code');

			$arr_user = array(
				'usergroup_id' => $usergroup_id,
				'Name' => $text_name,
				'CompanyName' => $CompanyName,
				'address1' => $address1,
				'ProvinceID' => $province_sel,
				'SubdistrictsId' => $district_sel,
				'DistrictId' => $subdistrict_sel,
				'Zip' => $Zip,
				'Tax' => $Tax,
				'Mobile' => $Mobile,
				'Mobile2' => $Mobile2,
				'Line' => $Line,
				'email' => $email,
				'customer_type' => $customer_type,
				'customer_code' => $customer_code
			);

	      $data_re = $this->web_bny_customer_model->insert($arr_user);

	      $arr_auth_user = array(
	      	'BNYCustomerID' => $data_re,
	      	'email' => $email,
	      	'password' => $password
	      );

	      $this->api_authen_model->insert($arr_auth_user);

	      $data_map_group = array(
			'group_id' =>$usergroup_id,
			'BNYCustomerID' => $data_re,
			'user_level_id' => 1,
			'is_main_group' => 1
		  );

		$this->groupmapuser_model->insert($data_map_group);

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

	function user_edit(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$id_en = $this->input->post('id_en');
	    	$id = $this->encryption_util->decrypt_ssl($id_en);
	    	$text_name = $this->input->post('text_name');
			$CompanyName = $this->input->post('CompanyName');
			$address1 = $this->input->post('address1');
			$province_sel = $this->input->post('province_sel');
			$district_sel = $this->input->post('district_sel');
			$subdistrict_sel = $this->input->post('subdistrict_sel');
			$Zip = $this->input->post('Zip');
			$Tax = $this->input->post('Tax');
			$Mobile = $this->input->post('Mobile');
			$Mobile2 = $this->input->post('Mobile2');
			$Line = $this->input->post('Line');
			//$email = $this->input->post('email');

			$arr_data = array(
				'Name' => $text_name,
				'CompanyName' => $CompanyName,
				'address1' => $address1,
				'ProvinceID' => $province_sel,
				'SubdistrictsId' => $district_sel,
				'DistrictId' => $subdistrict_sel,
				'Zip' => $Zip,
				'Tax' => $Tax,
				'Mobile' => $Mobile,
				'Mobile2' => $Mobile2,
				'Line' => $Line,
				//'email' => $email,
			);

	      $data_re = $this->web_bny_customer_model->update($arr_data,$id);

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


	function user_chk_username_invalid(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $txt_username = $this->input->post('txt_username');

	      $data_re = $this->api_authen_model->select_by_username($txt_username);

	      if(!empty($data_usergroup)){
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

	function user_chk_username_invalid_code(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $txt_email = $this->input->post('txt_email');
	   	  $customer_code = $this->input->post('customer_code');


	      $data_re = $this->web_bny_customer_model->select_by_email_code($txt_email,$customer_code);

	      if(!empty($data_usergroup)){
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

	function del_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);
	   	  $id = $this->encryption_util->decrypt_ssl($id_en);

	      $this->web_bny_customer_model->delete($id);
	      $this->api_authen_model->delete_by_customer_id($id);

	      $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}


 
 }