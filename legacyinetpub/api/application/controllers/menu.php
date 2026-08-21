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
		$this->load->library('businesslogic/data_bl');


		$this->load->model('menu_model');
		$this->load->model('groupmapmenu_model');
		$this->load->model('user_level_authen_model');
		$this->load->model('group_map_controller_model');
		$this->load->model('web_bny_customer_model');

		
		$this->load->model('bny_customer_activated_model');
		$this->load->model('bny_module_map_model');

		//$this->load->library('business_logic/auth_bl');
		
       	//$this->auth_bl->check_session_exists();
		
     }

     function get_menu(){

     	$group_id_en = $this->uri->segment(3);
     	$group_id = $this->encryption_util->decrypt_ssl($group_id_en);
	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $group_map_menus = $this->groupmapmenu_model->select_by_group($group_id);
	      $arr_menu_id = $this->data_bl->create_arr_id($group_map_menus,'menu_id');

	      $data_menus = $this->menu_model->select_by_parent_mapid('root',$arr_menu_id);

	      $cnt_mainmenu = count($data_menus);
	      if($cnt_mainmenu > 0){
	      	for($i=0;$i<=$cnt_mainmenu-1;$i++){
	      		$arr_submenus = $this->menu_model->select_by_parent_mapid($data_menus[$i]['menu_id'],$arr_menu_id);
	      		if(!empty($arr_submenus)){
	      			$data_menus[$i]['submenus'] = $arr_submenus;
	      			$cnt_submenu = count($arr_submenus);
	      			for($j=0;$j<=$cnt_submenu-1;$j++){
	      				$arr_lv3_submenus = $this->menu_model->select_by_parent_mapid($arr_submenus[$j]['menu_id'],$arr_menu_id);
	      				if(!empty($arr_lv3_submenus)){
	      					$data_menus[$i]['submenus'][$j]['lv3_submenus'] = $arr_lv3_submenus;
	      				}
	      			}
	      		}
	      	}
	      }
	    


	      if(!empty($data_menus)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_menu_group(){

     	$user_id_en = $this->uri->segment(3);
     	$user_id = $this->encryption_util->decrypt_ssl($user_id_en);
	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $data_groups = $this->web_bny_customer_model->select_multi_group($user_id);		

	      //$arr_group_id = $this->data_bl->create_arr_id($data_groups,'group_id');
	      $arr_group_id = array_column($data_groups, 'group_id');

	      $group_map_menus = $this->groupmapmenu_model->select_by_arr_group_id($arr_group_id);

	      //$arr_menu_id = $this->data_bl->create_arr_id($group_map_menus,'menu_id');
	      $arr_menu_id = array_column($group_map_menus, 'menu_id');

	      $data_menus = $this->menu_model->select_by_parent_mapid('root',$arr_menu_id);

	      $cnt_mainmenu = count($data_menus);
	      if($cnt_mainmenu > 0){
	      	for($i=0;$i<=$cnt_mainmenu-1;$i++){
	      		$arr_submenus = $this->menu_model->select_by_parent_mapid($data_menus[$i]['menu_id'],$arr_menu_id);
	      		if(!empty($arr_submenus)){
	      			$data_menus[$i]['submenus'] = $arr_submenus;
	      			$cnt_submenu = count($arr_submenus);
	      			for($j=0;$j<=$cnt_submenu-1;$j++){
	      				$arr_lv3_submenus = $this->menu_model->select_by_parent_mapid($arr_submenus[$j]['menu_id'],$arr_menu_id);
	      				if(!empty($arr_lv3_submenus)){
	      					$data_menus[$i]['submenus'][$j]['lv3_submenus'] = $arr_lv3_submenus;
	      				}
	      			}
	      		}
	      	}
	      }

	      $arr_data = array(
			'data_menus' => $data_menus,
			'arr_menu_id' => $arr_menu_id
		 );

	      //print_r($data_menus);
	      if(!empty($arr_data)){
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }
	}

	function get_menu_activated(){

	   $shop_id_en = $this->uri->segment(3);
	   $shop_id = $this->encryption_util->decrypt_ssl($shop_id_en);
	   $arr_header = $this->api_auth_bl->get_header();

	   $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	   //print_r($chk_auth);
	   //echo $chk_auth;
	   if($chk_auth['Status'] == "Success"){

		$arr_activated = $this->bny_customer_activated_model->chk_activated_by_shop_id($shop_id);
		$data_menus = "";
		if(!empty($arr_activated)){

			$arr_activate_menus = $this->bny_module_map_model->get_by_menu_module_set_id($arr_activated['bny_module_set_id']);

			$arr_menu_id = array_column($arr_activate_menus, 'menu_id');

			$data_menus = $this->menu_model->select_by_parent_mapid('root',$arr_menu_id);
		}

		 $cnt_mainmenu = count($data_menus);
		 if($cnt_mainmenu > 0){
			 for($i=0;$i<=$cnt_mainmenu-1;$i++){
				 $arr_submenus = $this->menu_model->select_by_parent_mapid($data_menus[$i]['menu_id'],$arr_menu_id);
				 if(!empty($arr_submenus)){
					 $data_menus[$i]['submenus'] = $arr_submenus;
					 $cnt_submenu = count($arr_submenus);
					 for($j=0;$j<=$cnt_submenu-1;$j++){
						 $arr_lv3_submenus = $this->menu_model->select_by_parent_mapid($arr_submenus[$j]['menu_id'],$arr_menu_id);
						 if(!empty($arr_lv3_submenus)){
							 $data_menus[$i]['submenus'][$j]['lv3_submenus'] = $arr_lv3_submenus;
						 }
					 }
				 }
			 }
		 }

		 //print_r($data_menus);

		 $arr_data = array(
			'data_menus' => $data_menus,
			'arr_menu_id' => $arr_menu_id
		 );
		 if(!empty($arr_data)){
		   $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Select Success',$arr_header['api_token']); 
		 }else{
		   $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Select No data',$arr_header['api_token']); 
		 }
		 
		 echo $data_json['view'];

	   }else{

		$chk_auth = $this->json_util->json_unicode($chk_auth);
		echo $chk_auth;
	   }

   }

	 function get_submenu(){

     	$group_id_en = $this->uri->segment(3);
     	$group_id = $this->encryption_util->decrypt_ssl($group_id_en);
	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	      $group_map_menus = $this->groupmapmenu_model->select_by_group($group_id);
	      $arr_menu_id = $this->data_bl->create_arr_id($group_map_menus,'menu_id');

	      $data_submenus = $this->menu_model->select_by_no_parent_mapid('root',$arr_menu_id);
	      $arr_summenu_id = $this->data_bl->create_arr_id($data_submenus,'menu_id');


	      if(!empty($data_menus)){
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_summenu_id,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$arr_summenu_id,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_submenu_activate(){

		$group_id_en = $this->uri->segment(3);
		$group_id = $this->encryption_util->decrypt_ssl($group_id_en);
	   $arr_header = $this->api_auth_bl->get_header();

	   $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	   //print_r($chk_auth);
	   //echo $chk_auth;
	   if($chk_auth['Status'] == "Success"){

		 $group_map_menus = $this->groupmapmenu_model->select_by_group($group_id);
		 $arr_menu_id = $this->data_bl->create_arr_id($group_map_menus,'menu_id');

		 $data_submenus = $this->menu_model->select_by_no_parent_mapid('root',$arr_menu_id);
		 $arr_summenu_id = $this->data_bl->create_arr_id($data_submenus,'menu_id');


		 if(!empty($data_menus)){
		   $data_json = $this->json_util->make_json('Select data','Success',$arr_summenu_id,'Select Success',$arr_header['api_token']); 
		 }else{
		   $data_json = $this->json_util->make_json('Select data','Success',$arr_summenu_id,'Select No data',$arr_header['api_token']); 
		 }
		 
		 echo $data_json['view'];

	   }else{

		$chk_auth = $this->json_util->json_unicode($chk_auth);
		echo $chk_auth;
	   }

   }

	function get_menu_all(){


	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){


	      $data_menus = $this->menu_model->select_by_parent('root');

	      $cnt_mainmenu = count($data_menus);
	      if($cnt_mainmenu > 0){
	      	for($i=0;$i<=$cnt_mainmenu-1;$i++){
	      		$arr_submenus = $this->menu_model->select_by_parent($data_menus[$i]['menu_id']);
	      		if(!empty($arr_submenus)){
	      			$data_menus[$i]['submenus'] = $arr_submenus;
	      			$cnt_submenu = count($arr_submenus);
	      			for($j=0;$j<=$cnt_submenu-1;$j++){
	      				$arr_lv3_submenus = $this->menu_model->select_by_parent($arr_submenus[$j]['menu_id']);
	      				if(!empty($arr_lv3_submenus)){
	      					$data_menus[$i]['submenus'][$j]['lv3_submenus'] = $arr_lv3_submenus;
	      				}
	      			}
	      		}

	      	}
	      }


	      if(!empty($data_menus)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function get_menu_customer(){


	    $arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){


	      $data_menus = $this->menu_model->select_by_parent_customer('root');

	      $cnt_mainmenu = count($data_menus);
	      if($cnt_mainmenu > 0){
	      	for($i=0;$i<=$cnt_mainmenu-1;$i++){
	      		$arr_submenus = $this->menu_model->select_by_parent_customer($data_menus[$i]['menu_id']);
	      		if(!empty($arr_submenus)){
	      			$data_menus[$i]['submenus'] = $arr_submenus;
	      			$cnt_submenu = count($arr_submenus);
	      			for($j=0;$j<=$cnt_submenu-1;$j++){
	      				$arr_lv3_submenus = $this->menu_model->select_by_parent_customer($arr_submenus[$j]['menu_id']);
	      				if(!empty($arr_lv3_submenus)){
	      					$data_menus[$i]['submenus'][$j]['lv3_submenus'] = $arr_lv3_submenus;
	      				}
	      			}
	      		}

	      	}
	      }


	      if(!empty($data_menus)){
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select Success',$arr_header['api_token']); 
	      }else{
	        $data_json = $this->json_util->make_json('Select data','Success',$data_menus,'Select No data',$arr_header['api_token']); 
	      }
	      
	      echo $data_json['view'];

	    }else{

	     $chk_auth = $this->json_util->json_unicode($chk_auth);
	     echo $chk_auth;
	    }

	}

	function check_page_permission(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $usergroup_id_en = $this->input->post('usergroup_id');
	   	  $usergroup_id = $this->encryption_util->decrypt_ssl($usergroup_id_en);

	   	  $user_id_en = $this->input->post('user_id');
	   	  $user_id = $this->encryption_util->decrypt_ssl($user_id_en);

	   	  $menu_id = $this->input->post('menu_id');

	   	  /*$data_groups = $this->web_bny_customer_model->select_multi_group($user_id);		

	      $arr_group_id = $this->data_bl->create_arr_id($data_groups,'group_id');

	      $data_re = $this->groupmapmenu_model->select_by_menu_id_arr_group_id($menu_id,$arr_group_id);

	      */

	      $data_re = $this->groupmapmenu_model->select_by_group_menu_id($usergroup_id,$menu_id);

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

	function check_allow_controller(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $controller = $this->uri->segment(3);

	      $data_re = $this->group_map_controller_model->select_by_controller($controller);

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

	function check_allow_controller_v1(){

		$arr_header = $this->api_auth_bl->get_header();

	    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
	    //print_r($chk_auth);
	    //echo $chk_auth;
	    if($chk_auth['Status'] == "Success"){

	   	  $controller = $this->input->post('controller');

	      $data_re = $this->user_level_authen_model->select_by_controller($controller);

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
}