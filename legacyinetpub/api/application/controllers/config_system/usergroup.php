<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Usergroup extends CI_Controller
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


		$this->load->model('user_group_model');
		$this->load->model('web_bny_customer_model');
		$this->load->model('groupmapuser_model');
		$this->load->model('groupmapmenu_model');
		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_usergroup_all(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $data_usergroup = $this->user_group_model->select_all();

	      if(!empty($data_usergroup)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_usergroup,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_usergroup,'Select No data',$arr_header['api_token']); 
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

	      $data = $this->user_group_model->select_by_id($id);

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

	function get_by_id_join_shop(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data = $this->user_group_model->get_by_id_join_shop($id);

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

	function get_usergroup_by_shop(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $shop_id = $this->uri->segment(4);

	      $data = $this->user_group_model->select_by_shop($shop_id);

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

	function usergroup_add(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$group_name = $this->input->post('group_name');
	    	$is_add = $this->input->post('is_add');
			$is_edit = $this->input->post('is_edit');
			$is_del = $this->input->post('is_del');
			$is_read = $this->input->post('is_read');
	    	$ShopID_en = $this->input->post('ShopID');
			$ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);
	    	$customer_code = $this->input->post('customer_code');

			$arr_data = array(
				'group_name' => $group_name,
				'is_add' => $is_add,
				'is_edit' => $is_edit,
				'is_del' => $is_del,
				'is_read' => $is_read,
				'ShopID' => $ShopID,
				'customer_code' => $customer_code
			);

	      $data_re = $this->user_group_model->insert($arr_data);

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

	function usergroup_edit(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$id_en = $this->input->post('id_en');
	    	$id = $this->encryption_util->decrypt_ssl($id_en);
	    	$group_name = $this->input->post('group_name');
	    	$is_add = $this->input->post('is_add');
			$is_edit = $this->input->post('is_edit');
			$is_del = $this->input->post('is_del');
			$is_read = $this->input->post('is_read');

			$arr_data = array(
				'group_name' => $group_name,
				'is_add' => $is_add,
				'is_edit' => $is_edit,
				'is_del' => $is_del,
				'is_read' => $is_read
			);

	      $data_re = $this->user_group_model->update($arr_data,$id);

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

	function get_user_group(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      /*$group_map_user = $this->groupmapuser_model->select_bu_group_id($id);

	      $arr_user_id = $this->data_bl->create_arr_id($group_map_user,'ApiAuthenID');

	      $data = $this->web_bny_customer_model->select_by_id_in($arr_user_id);*/

	      $data = $this->web_bny_customer_model->select_by_group_id($id);

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

	function get_usergroup(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id = $this->uri->segment(4);

	      /*$group_map_user = $this->groupmapuser_model->select_bu_group_id($id);

	      $arr_user_id = $this->data_bl->create_arr_id($group_map_user,'ApiAuthenID');

	      $data = $this->web_bny_customer_model->select_by_id_in($arr_user_id);*/

	      $data = $this->web_bny_customer_model->select_by_group_id($id);

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

	function get_usergroup_noid(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $id_en = $this->uri->segment(4);

	      $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data_usergroup = $this->user_group_model->select_not_id($id);

	      if(!empty($data_usergroup)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_usergroup,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_usergroup,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function usergroup_map(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$id_en = $this->input->post('id_en');
	    	$id = $this->encryption_util->decrypt_ssl($id_en);
	    	//$usergroup_sel = $this->input->post('usergroup_sel');
	    	$user_sel = $this->input->post('user_sel');

			$chk_user = $this->groupmapuser_model->select_by_group_user($id,$user_sel);

			$data_re = "";

			if(empty($chk_user)){

				$arr_data = array(
					'group_id' => $id,
					'BNYCustomerID' => $user_sel,
					'user_level_id' => 2,
					'is_main_group' => 0,
					'cdate' => date('Y-m-d H:i:s')
				);

				$data_re = $this->groupmapuser_model->insert($arr_data);

				$arr_data = array(
					'usergroup_id' => $id
				);

				$this->web_bny_customer_model->update($arr_data,$user_sel);
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

	function get_menu_select_by_group(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	     // $id_en = $this->uri->segment(4);

	     // $id = $this->encryption_util->decrypt_ssl($id_en);

	   	  $id_en = $this->uri->segment(4);
	   	  $id = $this->encryption_util->decrypt_ssl($id_en);

	      $data_re = $this->groupmapmenu_model->select_by_group($id);

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

	function group_map_menu_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	    	$id_en = $this->input->post('id_en');
	    	$id = $this->encryption_util->decrypt_ssl($id_en);
	    	$groupmenu_id = $this->input->post('groupmenu_id');
	    	$is_chk_val = $this->input->post('is_chk_val');

	    	$data_re = "";

	    	if($is_chk_val == 1){

	    		$arr_data = array(
					'group_id' => $id,
					'menu_id' => $groupmenu_id
				);

				$data_re = $this->groupmapmenu_model->insert($arr_data);

			}if($is_chk_val == 2){

				$this->groupmapmenu_model->del_by_group_menu($id,$groupmenu_id);

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

	function usergroup_chk_username_invalid(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $group_name = $this->input->post('group_name');

	      $data_re = $this->user_group_model->select_by_group_name($group_name);

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

	function usergroup_chk_username_invalid_edit(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $group_name = $this->input->post('group_name');
	   	  $group_id_en = $this->input->post('group_id_en');
	   	  $group_id = $this->encryption_util->decrypt_ssl($group_id_en);

	      $data_re = $this->user_group_model->select_by_group_name_not_id($group_name,$group_id);

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

	      $this->user_group_model->delete($id);

	      $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function move_usergroup_map(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $user_id = $this->input->post('user_id');

	   	  $usergroup_id_en = $this->input->post('usergroup_id');
	   	  $usergroup_id = $this->encryption_util->decrypt_ssl($usergroup_id_en);

	   	  /*$data = array(
				'usergroup_id' => ''
			);

	      $data_re = $this->web_bny_customer_model->update($data,$user_id);*/

	      $data_re = $this->groupmapuser_model->del_by_group_user($usergroup_id,$user_id);


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

	function group_permission(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

		    $id_en = $this->input->post('id_en');
		    $id = $this->encryption_util->decrypt_ssl($id_en);
		    $is_name = $this->input->post('is_name');
			$is_chk_val = $this->input->post('is_chk_val');

			$arr_data = array(
				$is_name => $is_chk_val
			);


	      $data = $this->user_group_model->update($arr_data,$id);

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

	function map_user_action(){

	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $group_id = $this->input->post('group_id');
	      $user_id = $this->input->post('user_id');
	      $val_user_lv = $this->input->post('val_user_lv');

	      $data_insert = array(
	  			'group_id' => $group_id,
	  			'BNYCustomerID' => $user_id,
	  			'user_level_id' => $val_user_lv
	  		);

	      $chk_user = $this->groupmapuser_model->select_by_group_user_one($group_id,$user_id);

	      if(!empty($chk_user)){
	      	if($chk_user['user_level_id'] != $val_user_lv){

	      		$this->groupmapuser_model->delete($chk_user['groupmapuser_id']);

	      		$this->groupmapuser_model->insert($data_insert);
	      	}
	      }else{

	      		$this->groupmapuser_model->insert($data_insert);

	      }

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

 
 }