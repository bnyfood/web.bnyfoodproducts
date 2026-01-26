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

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/selectbox_bl');
		$this->load->library('businesslogic/data_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('user_group_model');
		$this->load->model('user_model');
		$this->load->model('web_shop_model');

        $this->auth_bl->check_session_exists();
     }
     
	public function usergroup_list()
	{

		$add_alt = $this->session->flashdata('add_usergroup');
		$edit_alt = $this->session->flashdata('edit_usergroup');
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
		//echo $customer_code;

		$arr_shops = $this->web_shop_model->get_shop_by_code($customer_code);
		//echo $add_alt;

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$id_shop = $this->encryption_util->decrypt_ssl($sess_shop_id);
		//echo "shop>>".$id_shop."<<";

		$arr_usergroups = $this->user_group_model->get_usergroup_by_shop($id_shop);
		//print_r($arr_usergroups);
		if($arr_usergroups['Status'] == "Success"){
			$max = sizeof($arr_usergroups['Data']);

			for($i=0;$i<$max;$i++){
				$arr_usergroups['Data'][$i]['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroups['Data'][$i]['usergroup_id']);
			}
		}
		
		$data = array(
			'arr_usergroups' => $arr_usergroups['Data'],
			'arr_shops' => $arr_shops['Data'],
			'id_shop' => $id_shop,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
        	'usergroup_list' => base_url()."resources/js/config_system/usergroup/usergroup_list.js"
    	);
		
		$this->view_util->load_view_main('config_system/usergroup/usergroup_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	} 

	function add_usergroup_form(){

		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	//'config_usergroup' => base_url()."resources/js/validate/config_usergroup.js"
    	);	
		
		$this->view_util->load_view_main('config_system/usergroup/add_usergroup_form',NULL,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function usergroup_add(){

		$group_name = $this->input->post('group_name');
		$is_add = $this->input->post('is_add');
		$is_edit = $this->input->post('is_edit');
		$is_del = $this->input->post('is_del');
		$is_read = $this->input->post('is_read');
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');

		$data_curl = array(
			'group_name' => $group_name,
			'is_add' => $is_add,
			'is_edit' => $is_edit,
			'is_del' => $is_del,
			'is_read' => $is_read,
			'ShopID' => $shop_id,
			'customer_code' => $customer_code
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_add',$data_curl);
		//print_r($arr_res);
		$this->user_group_model->del_cache_usergroup_by_shop($shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');
	}

	function usergroup_edit_form(){

		$id_en = $this->uri->segment(4);

		//$arr_usergroup = $this->curl_bl->CallApi('GET','config_system/usergroup/get_by_id/'.$id_en);

		$arr_usergroup = $this->user_group_model->get_by_id($id_en);

		if($arr_usergroup['Status'] == "Success"){

			$arr_usergroup['Data']['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroup['Data']['usergroup_id']);
							 
		}

		//print_r($arr_menu);

		$arr_input = array(
			'title' => "Config User"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'config_usergroup' => base_url()."resources/js/validate/config_usergroup_edit.js"
    	);

    	$data = array(
			'arr_usergroup' => $arr_usergroup['Data']
		);
		
		
		$this->view_util->load_view_main('config_system/usergroup/edit_usergroup_form',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function usergroup_edit(){

		$id_en = $this->input->post('id_en');
		$group_name = $this->input->post('group_name');
		$is_add = $this->input->post('is_add');
		$is_edit = $this->input->post('is_edit');
		$is_del = $this->input->post('is_del');
		$is_read = $this->input->post('is_read');

		$data_curl = array(
			'id_en' => $id_en,
			'group_name' => $group_name,
			'is_add' => $is_add,
			'is_edit' => $is_edit,
			'is_del' => $is_del,
			'is_read' => $is_read
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_edit',$data_curl);
		//print_r($arr_res);
		$this->user_group_model->del_cache_by_id($id_en);
		$this->user_group_model->del_cache_usergroup_by_shop($id_shop);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');

		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');
	}

	function user_list(){
		//echo $add_alt;
		$id_en = $this->uri->segment(4);

		$arr_users = $this->curl_bl->CallApi('GET','config_system/usergroup/get_user_group/'.$id_en);
		$arr_usergroups = $this->curl_bl->CallApi('GET','config_system/usergroup/get_usergroup_noid/'.$id_en);
		//print_r($arr_users);
		
		$data = array(
			'id_en' => $id_en,
			'arr_users' => $arr_users['Data'],
			'arr_usergroups' => $arr_usergroups['Data']
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
        	'user_map_group' => base_url()."resources/js/user_map_group.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/usergroup/user_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function get_usergroup(){

		$usergroup_id = $this->input->post('usergroup_id');
		$data_sel = $this->curl_bl->call_curl('GET','config_system/usergroup/get_usergroup/'.$usergroup_id);

		//print_r($arr_user);
		$data = $this->selectbox_bl->make_list_usergroup($data_sel['Data']);
		$arr_data = array(
			'usergroup' => $data
		);
		echo json_encode($arr_data);
	}

	function usergroup_map(){

		$id_en = $this->input->post('id_en');
		$usergroup_sel = $this->input->post('usergroup_sel');
		$user_sel = $this->input->post('user_sel');

		$data_curl = array(
			'id_en' => $id_en,
			'usergroup_sel' => $usergroup_sel,
			'user_sel' => $user_sel
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_map',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/user_list/'.$id_en,'refresh');

	}

	function groupmenu_list(){
		//echo $add_alt;
		$id_en = $this->uri->segment(4);

		$data_groupmenus = $this->curl_bl->CallApi('GET','menu/get_menu_all');
		//print_r($data_groupmenus);
		$arr_groupmenu = $this->curl_bl->CallApi('GET','config_system/usergroup/get_menu_select_by_group/'.$id_en);


       	$arr_menu_select = $this->data_bl->create_arr_id($arr_groupmenu['Data'],'menu_id');
   		
		//print_r($arr_menu_select);

		$data = array(
			'id_en' => $id_en,
			'data_groupmenus' => $data_groupmenus['Data'],
			'arr_menu_select' => $arr_menu_select
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
        	'group_map_menu' => base_url()."resources/js/group_map_menu.js"
    	);
		
		
		$this->view_util->load_view_main('config_system/usergroup/groupmenu_list',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function group_map_menu_action(){

		$id_en = $this->input->post('id_en');
		$groupmenu_id = $this->input->post('groupmenu_id');
		$is_chk_val = $this->input->post('is_chk_val');

		$data_curl = array(
			'id_en' => $id_en,
			'groupmenu_id' => $groupmenu_id,
			'is_chk_val' => $is_chk_val
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/group_map_menu_action',$data_curl);
		//print_r($arr_res);
		$this->user_group_model->del_cache_group_menu_by_group_id($id_en);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');

	}

	function usergroup_chk_username_invalid(){

		$group_name = $this->input->post('group_name');

		$data_curl = array(
			'group_name' => $group_name
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_chk_username_invalid',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}

	}

	function usergroup_chk_username_invalid_edit(){

		$group_name = $this->input->post('group_name');
		$group_id_en = $this->input->post('group_id_en');

		$data_curl = array(
			'group_name' => $group_name,
			'group_id_en' => $group_id_en
		);

		$arr_res = $this->curl_bl->CallApi('POST','config_system/usergroup/usergroup_chk_username_invalid_edit',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}

	}

	function del_action(){

		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$id_en = $this->uri->segment(4);

		$arr_res = $this->curl_bl->CallApi('GET','config_system/usergroup/del_action/'.$id_en);
		//print_r($arr_res);

		$this->user_group_model->del_cache_usergroup_by_shop($shop_id);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');

	}

	function set_shop_search(){
		$id_shop = $this->uri->segment(4);
		//echo $id_shop;
		$this->session->set_userdata('id_shop',$id_shop);
		redirect(base_url().'config_system/usergroup/usergroup_list','refresh');
	}

	function usergroup_manage(){

		$id_en = $this->uri->segment(4);
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
		$shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$arr_list_users = $this->user_model->get_user_by_code_noid($id_en,$customer_code);
		//print_r($arr_list_users );
		//$arr_list_users = $this->curl_bl->CallApi('GET','config_system/users/get_user_by_code_noid/'.$id_en."/".$customer_code."/3");
		$arr_users = $this->user_model->get_users_by_group_id($id_en);
		//print_r($arr_users);
		//$arr_users = $this->curl_bl->CallApi('GET','config_system/users/get_users_by_group_id/'.$id_en);
		$data_groupmenus = $this->menu_model->get_menu_activated($shop_id);
		//$data_groupmenus = $this->curl_bl->CallApi('GET','menu/get_menu_customer');
		//print_r($data_groupmenus);
		
		$arr_groupmenu = $this->user_group_model->get_menu_select_by_group($id_en);
		//$arr_groupmenu = $this->curl_bl->CallApi('GET','config_system/usergroup/get_menu_select_by_group/'.$id_en);
		//print_r($arr_groupmenu);
       	//$arr_menu_select = $this->data_bl->create_arr_id($arr_groupmenu['Data'],'menu_id');
       	$arr_menu_select = array_column($arr_groupmenu['Data'], 'menu_id');
       	//print_r($arr_menu_select);

       	$arr_group = $this->user_group_model->get_by_id_join_shop($id_en);
       	//$arr_group = $this->curl_bl->CallApi('GET','config_system/usergroup/get_by_id_join_shop/'.$id_en);

       	$arr_group['Data']['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_group['Data']['usergroup_id']);

       	$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$id_shop = $this->encryption_util->decrypt_ssl($sess_shop_id);

       	$arr_usergroups = $this->user_group_model->get_usergroup_by_shop($id_shop);
		//$arr_usergroups = $this->curl_bl->CallApi('GET','config_system/usergroup/get_usergroup_by_shop/'.$id_shop);
		if($arr_usergroups['Status'] == "Success"){
			$max = sizeof($arr_usergroups['Data']);

			for($i=0;$i<$max;$i++){
				$arr_usergroups['Data'][$i]['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroups['Data'][$i]['usergroup_id']);
			}
		}

		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
			'usergroup_list' => base_url()."resources/js/config_system/usergroup/usergroup_list.js",
        	'usergroup_manage' => base_url()."resources/js/config_system/usergroup/usergroup_manage_v2.js",
        	'group_map_menu' => base_url()."resources/js/group_map_menu.js"
    	);

    	$data = array(
			'id_en' => $id_en,
			'arr_group' => $arr_group['Data'],
			'arr_users' => $arr_users['Data'],
			'arr_list_users' => $arr_list_users['Data'],
			'data_groupmenus' => $data_groupmenus['Data']['data_menus'],
			'arr_menu_select' => $arr_menu_select,
			'arr_usergroups' => $arr_usergroups['Data']
		);
		
		$this->view_util->load_view_main('config_system/usergroup/usergroup_manage',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
	}

	function usergroup_manage_bk_v1(){
		
		$id_en = $this->uri->segment(4);
		$customer_code = $this->session->userdata(SESSION_PREFIX.'customer_code');
		$arr_list_users = $this->user_model->get_user_by_code_noid($id_en,$customer_code);
		//print_r($arr_list_users );
		//$arr_list_users = $this->curl_bl->CallApi('GET','config_system/users/get_user_by_code_noid/'.$id_en."/".$customer_code."/3");
		$arr_users = $this->user_model->get_users_by_group_id($id_en);
		//print_r($arr_users);
		//$arr_users = $this->curl_bl->CallApi('GET','config_system/users/get_users_by_group_id/'.$id_en);
		$data_groupmenus = $this->menu_model->get_menu_customer();
		//$data_groupmenus = $this->curl_bl->CallApi('GET','menu/get_menu_customer');
		//print_r($data_groupmenus);
		
		$arr_groupmenu = $this->user_group_model->get_menu_select_by_group($id_en);
		//$arr_groupmenu = $this->curl_bl->CallApi('GET','config_system/usergroup/get_menu_select_by_group/'.$id_en);
		//print_r($arr_groupmenu);

       	$arr_menu_select = $this->data_bl->create_arr_id($arr_groupmenu['Data'],'menu_id');
       	//print_r($arr_menu_select);

       	$arr_group = $this->user_group_model->get_by_id_join_shop($id_en);
       	//$arr_group = $this->curl_bl->CallApi('GET','config_system/usergroup/get_by_id_join_shop/'.$id_en);

       	$arr_group['Data']['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_group['Data']['usergroup_id']);

       	$sess_shop_id = $this->session->userdata('shop_id');
		$id_shop = $this->encryption_util->decrypt_ssl($sess_shop_id);

       	$arr_usergroups = $this->user_group_model->get_usergroup_by_shop($id_shop);
		//$arr_usergroups = $this->curl_bl->CallApi('GET','config_system/usergroup/get_usergroup_by_shop/'.$id_shop);
		if($arr_usergroups['Status'] == "Success"){
			$max = sizeof($arr_usergroups['Data']);

			for($i=0;$i<$max;$i++){
				$arr_usergroups['Data'][$i]['usergroup_id'] = $this->encryption_util->encrypt_ssl($arr_usergroups['Data'][$i]['usergroup_id']);
			}
		}

		$arr_input = array(
			'title' => "Config Usergroup"
		);
		
		$arr_js = array(
			'usergroup_list' => base_url()."resources/js/config_system/usergroup/usergroup_list.js",
        	'usergroup_manage' => base_url()."resources/js/config_system/usergroup/usergroup_manage.js",
        	'group_map_menu' => base_url()."resources/js/group_map_menu.js"
    	);

    	$data = array(
			'id_en' => $id_en,
			'arr_group' => $arr_group['Data'],
			'arr_users' => $arr_users['Data'],
			'arr_list_users' => $arr_list_users['Data'],
			'data_groupmenus' => $data_groupmenus['Data'],
			'arr_menu_select' => $arr_menu_select,
			'arr_usergroups' => $arr_usergroups['Data']
		);
		
		$this->view_util->load_view_main('config_system/usergroup/usergroup_manage',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	}

	function add_user_to_group(){

		$id_en = $this->input->post('id_en');
		$user_sel = $this->input->post('user_sel');

		$data_curl = array(
			'id_en' => $id_en,
			'user_sel' => $user_sel
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','config_system/usergroup/usergroup_map',$data_curl);
		//$this->user_model->del_cache_by_group_id($id_en);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function get_usergroup_ajax(){

		$id_en = $this->input->post('id_en');
		$arr_users = $this->user_model->get_users_by_group_id_nospin($id_en);
		//$arr_users = $this->curl_bl->CallApi('GET','config_system/users/get_users_by_group_id/'.$id_en);

		$arr_data = array(
			'arr_usergroups' => $arr_users['Data']
		);
		echo json_encode($arr_data);
	}

	function move_usergroup_map(){

		$id_en = $this->uri->segment(5);
		$user_id = $this->uri->segment(4);

		$data_curl = array(
			'user_id' => $user_id,
			'usergroup_id' => $id_en
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','config_system/usergroup/move_usergroup_map',$data_curl);
		$this->user_model->del_cache_by_group_id($id_en);
		
		redirect(base_url().'config_system/usergroup/usergroup_manage/'.$id_en,'refresh');
	}

	function move_usergroup_ajax(){

		$id_en = $this->uri->segment(5);
		$user_id = $this->uri->segment(4);

		$data_curl = array(
			'user_id' => $user_id,
			'usergroup_id' => $id_en
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','config_system/usergroup/move_usergroup_map',$data_curl);
		$this->user_model->del_cache_by_group_id($id_en);
		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function group_permission(){

		$id_en = $this->input->post('id_en');
		$is_name = $this->input->post('is_name');
		$is_chk_val = $this->input->post('is_chk_val');

		$data_curl = array(
			'id_en' => $id_en,
			'is_name' => $is_name,
			'is_chk_val' => $is_chk_val
		);

		//print_r($data_curl);
		$arr_res = $this->curl_bl->CallApiNospi('POST','config_system/usergroup/group_permission',$data_curl);
		//$this->user_model->del_cache_by_group_id($id_en);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}
}