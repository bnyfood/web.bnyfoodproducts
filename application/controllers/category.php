<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Category extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/selectbox_bl');

		$this->load->model('web_shop_model');

        $this->auth_bl->check_session_exists();

     }
     
	public function manage_cat_bk()
	{


		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);
		//print_r($arr_list_cats['Data']);
		$arr_input = array(
			'title' => "Config Category"
		);
		
		$arr_js = array(
        	'category_manage' => base_url()."resources/js/category/category_manage.js"
    	);

    	$data = array(
			'arr_list_cats' => $arr_list_cats['Data']
		);
		
		
		$this->view_util->load_view_main('category/manage_cat',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	}

	function category_add_form(){

		$parent_id = $this->uri->segment(3);
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);
		//print_r($arr_list_cats['Data']);
		$arr_input = array(
			'title' => "Config Category"
		);
		
		$arr_js = array(
        	'category_manage' => base_url()."resources/js/category/category_manage.js"
    	);

    	$data = array(
    		'parent_id' => $parent_id,
			'arr_list_cats' => $arr_list_cats['Data']
		);
		
		
		$this->view_util->load_view_main('category/add_cat',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	}

	function category_add_action(){

		$cat_name = $this->input->post('cat_name');
		$cat_des = $this->input->post('cat_des');
		$parent_id = $this->input->post('parent_id');
		$sess_shop_id = $this->session->userdata('shop_id');

		$data_curl = array(
			'Title' => $cat_name,
			'SuperCategoryID' => $parent_id,
			'Description' => $cat_des,
			'ShopID' => $sess_shop_id,

		);

		$arr_res = $this->curl_bl->CallApi('POST','category/category_add_action',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'category/manage_cat','refresh');

	}

	function category_edit_form(){

		$parent_id = $this->uri->segment(3);
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$sess_shop_id);
		//print_r($arr_list_cats['Data']);

		$arr_cat = $this->curl_bl->CallApi('GET','category/get_cat/'.$parent_id);
		$arr_input = array(
			'title' => "Config Category"
		);
		
		$arr_js = array(
        	'category_manage' => base_url()."resources/js/category/category_manage.js"
    	);

    	$data = array(
    		'parent_id' => $parent_id,
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_cat' => $arr_cat['Data']
		);
		
		
		$this->view_util->load_view_main('category/edit_cat',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	}

	function category_edit_action(){

		$cat_name = $this->input->post('cat_name');
		$cat_des = $this->input->post('cat_des');
		$parent_id = $this->input->post('parent_id');

		$data_curl = array(
			'Title' => $cat_name,
			'cat_id' => $parent_id,
			'Description' => $cat_des
		);

		$arr_res = $this->curl_bl->CallApi('POST','category/category_edit_action',$data_curl);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'category/manage_cat','refresh');

	}

	function category_del_action(){

		$parent_id = $this->uri->segment(3);

		$arr_res = $this->curl_bl->CallApi('GET','category/category_del_action/'.$parent_id);
		//print_r($arr_res);

		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'category/manage_cat','refresh');

	}

	public function manage_cat()
	{

		$add_alt = $this->session->flashdata('add_cat');
		$edit_alt = $this->session->flashdata('edit_cat');

		$sess_shop_id = $this->session->userdata('shop_id');
		$customer_type = $this->session->userdata('customer_type');
		$customer_code = $this->session->userdata('customer_code');
		$usergroup_id = $this->session->userdata('usergroup_id');

		$from_pop = $this->uri->segment(3);
		if($from_pop == ""){
			$from_pop = 0;
		}


		$arr_shops = $this->web_shop_model->get_shop_by_customer_type($customer_type,$customer_code,$usergroup_id);

		if($arr_shops['Status'] == "Success"){
			$max = sizeof($arr_shops['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shops['Data'][$i]['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shops['Data'][$i]['ShopID']);
			}
		}

		//print_r($arr_shops);

		$id_shop = $this->session->userdata('id_shop_cat');

		//echo ">>>>>>>>>>>>>>".$id_shop;

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$id_shop);
		print_r($arr_list_cats['Data']);
		$arr_cat_root = $this->curl_bl->CallApi('GET','category/get_cat_root/'.$id_shop);
		
		$arr_input = array(
			'title' => "Config Category"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'manage_cat' => base_url()."resources/js/validate/manage_cat.js",
        	'category_manage' => base_url()."resources/js/category/category_manage.js",

    	);

    	$data = array(
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_cat_root' => $arr_cat_root['Data'],
			'arr_shops' => $arr_shops['Data'],
			'id_shop' => $id_shop,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'from_pop' => $from_pop
		);
		if($from_pop == 1){
			$this->view_util->load_view_blankpage('category/manage_cat',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
		}else{
			$this->view_util->load_view_main('category/manage_cat',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);
		}
		

	}

	public function manage_cat_pop()
	{

		$add_alt = $this->session->flashdata('add_cat');
		$edit_alt = $this->session->flashdata('edit_cat');

		$sess_shop_id = $this->session->userdata('shop_id');
		$customer_type = $this->session->userdata('customer_type');
		$customer_code = $this->session->userdata('customer_code');
		$usergroup_id = $this->session->userdata('usergroup_id');

		$arr_shops = $this->web_shop_model->get_shop_by_customer_type($customer_type,$customer_code,$usergroup_id);

		if($arr_shops['Status'] == "Success"){
			$max = sizeof($arr_shops['Data']);

			for($i=0;$i<$max;$i++){
				$arr_shops['Data'][$i]['ShopID'] = $this->encryption_util->encrypt_ssl($arr_shops['Data'][$i]['ShopID']);
			}
		}

		//print_r($arr_shops);

		$id_shop = $this->session->userdata('id_shop_cat');

		//echo ">>>>>>>>>>>>>>".$id_shop;

		$arr_list_cats = $this->curl_bl->CallApi('GET','category/build_cat/'.$id_shop);
		//print_r($arr_list_cats['Data']);
		$arr_cat_root = $this->curl_bl->CallApi('GET','category/get_cat_root/'.$id_shop);
		
		$arr_input = array(
			'title' => "Config Category"
		);
		
		$arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'manage_cat' => base_url()."resources/js/validate/manage_cat.js",
        	'category_manage' => base_url()."resources/js/category/category_manage_pop.js",

    	);

    	$data = array(
			'arr_list_cats' => $arr_list_cats['Data'],
			'arr_cat_root' => $arr_cat_root['Data'],
			'arr_shops' => $arr_shops['Data'],
			'id_shop' => $id_shop,
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt
		);
		
		
		$this->view_util->load_view_blankpage('category/manage_cat_pop',$data,NULL,$arr_js,$arr_input,MENU_CONFIG_USERGROUP);

	}


	function category_add(){

		$cat_name = $this->input->post('cat_name');
		$cat_des = $this->input->post('cat_des');
		$parent_id = $this->input->post('parent_id');
		$is_add = $this->input->post('is_add');
		$id_shop = $this->input->post('id_shop');
		$from_pop = $this->input->post('from_pop');
		

		if($is_add == 1){
			$data_curl = array(
				'Title' => $cat_name,
				'SuperCategoryID' => $parent_id,
				'Description' => $cat_des,
				'ShopID' => $id_shop,

			);

			$arr_res = $this->curl_bl->CallApi('POST','category/category_add_action',$data_curl);
			//print_r($arr_res);
			$this->session->set_flashdata('add_cat','success');

			/*if($arr_res['Status'] == "Success"){
				$this->session->set_flashdata('add_cat','success');
			}else{
				$this->session->set_flashdata('add_cat','fail');
			}*/

			redirect(base_url().'category/manage_cat/'.$from_pop,'refresh');
		}else if($is_add == 2){

			$data_curl = array(
				'Title' => $cat_name,
				'cat_id' => $parent_id,
				'Description' => $cat_des
			);

			$arr_res = $this->curl_bl->CallApi('POST','category/category_edit_action',$data_curl);
			//print_r($arr_res);
			$this->session->set_flashdata('edit_cat','success');
			/*if($arr_res['Status'] == "Success"){
				$this->session->set_flashdata('edit_cat','success');
			}else{
				$this->session->set_flashdata('edit_cat','fail');
			}*/

			redirect(base_url().'category/manage_cat/'.$from_pop,'refresh');

		}

	}

	function category_get_by_id(){

		$parent_id = $this->input->post('parent_cat');

		$arr_cat = $this->curl_bl->CallApiNospi('GET','category/get_cat/'.$parent_id);

		$arr_data = array(
			'cat_data' => $arr_cat['Data']
		);
		echo json_encode($arr_data);

	}

	function set_shop_search(){
		$id_shop = $this->uri->segment(3);
		//echo $id_shop;
		$this->session->set_userdata('id_shop_cat',$id_shop);
		redirect(base_url().'category/manage_cat','refresh');
	}

	function get_cat_list(){

		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;
		$arr_list_cats = $this->curl_bl->CallApiNospi('GET','category/build_cat/'.$sess_shop_id);

		//print_r($arr_user);
		$data = $this->selectbox_bl->make_list_cat($arr_list_cats['Data']);
		$arr_data = array(
			'list_cat' => $data
		);
		echo json_encode($arr_data);
	}

}