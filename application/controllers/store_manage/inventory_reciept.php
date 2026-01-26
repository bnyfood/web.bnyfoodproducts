<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Inventory_reciept extends CI_Controller
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
		$this->load->library('businesslogic/permission_bl');
		$this->load->library('util/encryption_util');

		$this->load->model('web_purchase_order_model');
		$this->load->model('inventory_reciept_model');

		$this->auth_bl->check_session_exists();

     }
     
	public function po_material_list()
	{
		$add_alt = $this->session->flashdata('add_location');
		$edit_alt = $this->session->flashdata('edit_location');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;

		$data_search = array(
			'po_search' => '',
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);
		
		$data = array(
			'arr_purchases' => '',
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);

		//print_r($data);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

		$arr_user_level = array('1','2','3');


		$this->view_util->load_view_main('store_manage/inventory_reciept/po_material_list',$data,$arr_css,NULL,$arr_input,MENU_MANUFACTURE_BRAND,$arr_user_level);

	}

	public function po_material_search()
	{
		$add_alt = $this->session->flashdata('add_location');
		$edit_alt = $this->session->flashdata('edit_location');
		//echo $add_alt;
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;

		$po_search = $this->input->post('po_search');

		$data_search = array(
			'po_search' => $po_search,
			'sortby' => '',
			'sorttype' => '',
			'offset' => 1,
			'per_page' => 5
		);

		//print_r($data_search);

		$arr_purchases = $this->web_purchase_order_model->get_by_ponumber($sess_shop_id,$data_search);
		
		$data = array(
			'arr_purchases' => $arr_purchases['Data'],
			'add_alt' => $add_alt,
			'edit_alt' => $edit_alt,
			'data_search' => $data_search
		);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

		$arr_user_level = array('1','2','3');


		$this->view_util->load_view_main('store_manage/inventory_reciept/po_material_list',$data,$arr_css,NULL,$arr_input,MENU_MANUFACTURE_BRAND,$arr_user_level);

	}

	function reciept_add_form(){
		$arr_permission = $this->permission_bl->check_allow_controller('reciept_add_form');

		$po_number = $this->uri->segment(4);
		$sess_shop_id = $this->session->userdata('shop_id');

		//print_r($data_search);

		$arr_data = $this->inventory_reciept_model->get_by_no_join($sess_shop_id,$po_number);

		$arr_purchase_material_id = $this->data_bl->create_arr_id($arr_data['Data']['data_po'],'purchase_material_id');

		//print_r($arr_purchase_material_id);
		
		$data = array(
			'data_po_profile' => $arr_data['Data']['data_po_profile'],
			'data_pos' => $arr_data['Data']['data_po'],
			'data_locations' => $arr_data['Data']['data_locations'],
			'data_shelfs' => $arr_data['Data']['data_shelfs'],
			'arr_purchase_material_id' => json_encode($arr_purchase_material_id,true),
			'arr_permission' => $arr_permission
		);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			//'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

		$arr_user_level = array('1','2','3');


		$this->view_util->load_view_main('store_manage/inventory_reciept/reciept_add_form',$data,$arr_css,NULL,$arr_input,MENU_MANUFACTURE_BRAND,$arr_user_level);

	}

	function reciept_add(){

		$arr_data = array();

		$sess_shop_id = $this->session->userdata('shop_id');
		$arr_purchase_material_id = $this->input->post('arr_purchase_material_id');
		$po_number = $this->input->post('po_number');
		$web_purchase_order_id = $this->input->post('web_purchase_order_id');

		$arr_purchase_material_id_de = json_decode($arr_purchase_material_id);

		//print_r($arr_purchase_material_id_de);
		$arr_data['ShopID'] = $sess_shop_id;
		$arr_data['arr_purchase_material_id'] = $arr_purchase_material_id;
		$arr_data['web_purchase_order_id'] = $web_purchase_order_id;
		$arr_data['po_number'] = $po_number;


		$cnt_arr = count($arr_purchase_material_id_de) -1 ;
		

		for($i=0;$i<=$cnt_arr;$i++){
			//echo $arr_purchase_material_id_de[$i];
			$arr_data['web_material_id_'.$arr_purchase_material_id_de[$i]] =  $this->input->post('web_material_id_'.$arr_purchase_material_id_de[$i]);
			$arr_data['unit_'.$arr_purchase_material_id_de[$i]] =  $this->input->post('unit_'.$arr_purchase_material_id_de[$i]);
			$arr_data['qty_'.$arr_purchase_material_id_de[$i]] =  $this->input->post('qty_'.$arr_purchase_material_id_de[$i]);
			$arr_data['location_'.$arr_purchase_material_id_de[$i]] =  $this->input->post('location_'.$arr_purchase_material_id_de[$i]);
			$arr_data['shelf_'.$arr_purchase_material_id_de[$i]] =  $this->input->post('shelf_'.$arr_purchase_material_id_de[$i]);
		}

		//print_r($arr_data);

		//$data_curl = json_encode($arr_data);

		$arr_res = $this->curl_bl->CallApi('POST','store_manage/inventory_reciept/reciept_add',$arr_data);


		if($arr_res['Status'] == "Success"){
			$this->session->set_flashdata('add_menu','success');
		}else{
			$this->session->set_flashdata('add_menu','fail');
		}

		redirect(base_url().'store_manage/inventory_reciept/reciept_data/'.$po_number,'refresh');
	}

	function reciept_edit_form(){

		$arr_permission = $this->permission_bl->check_allow_controller('reciept_edit_form');

		$po_number = $this->uri->segment(4);
		$sess_shop_id = $this->session->userdata('shop_id');
		print_r($arr_permission);

		$arr_data = $this->inventory_reciept_model->get_by_no_join($sess_shop_id,$po_number);

		$arr_purchase_material_id = $this->data_bl->create_arr_id($arr_data['Data']['data_po'],'purchase_material_id');

		//print_r($arr_purchase_material_id);
		
		$data = array(
			'data_po_profile' => $arr_data['Data']['data_po_profile'],
			'data_pos' => $arr_data['Data']['data_po'],
			'data_locations' => $arr_data['Data']['data_locations'],
			'data_shelfs' => $arr_data['Data']['data_shelfs'],
			'arr_purchase_material_id' => json_encode($arr_purchase_material_id,true),
			'arr_permission' => $arr_permission
		);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/store_manage/location_list.js",
			//'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);

		$this->view_util->load_view_main('store_manage/inventory_reciept/reciept_add_form',$data,$arr_css,NULL,$arr_input,MENU_MANUFACTURE_BRAND);

	}

	function reciept_data(){

		$po_number = $this->uri->segment(4);
		$sess_shop_id = $this->session->userdata('shop_id');

		//print_r($data_search);

		$arr_data = $this->inventory_reciept_model->get_by_no_pro_join($sess_shop_id,$po_number);

		$arr_purchase_material_id = $this->data_bl->create_arr_id($arr_data['Data']['data_po'],'purchase_material_id');

		//print_r($arr_purchase_material_id);
		
		$data = array(
			'data_po_profile' => $arr_data['Data']['data_po_profile'],
			'data_pos' => $arr_data['Data']['data_po'],
			'po_number' => $po_number
		);
		
		$arr_input = array(
			'title' => "location"
		);
		
		$arr_js = array(
			'reciept_data' => base_url()."resources/js/inventory_reciept/reciept_data.js",
			//'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);


		$this->view_util->load_view_main('store_manage/inventory_reciept/reciept_data',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);

	}

	function print_reciept(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$po_number = $this->uri->segment(4);

		$arr_reciepts= $this->inventory_reciept_model->get_by_no_pro_join($sess_shop_id,$po_number);

		$validdata = 0;
	    if(!empty($arr_reciepts)){
	    	$validdata = 1;
	    }

		$data=array(
			'arr_reciepts'=>$arr_reciepts['Data']['data_po'],
			'validdata'=>$validdata
		);
	                       
	    $this->load->view('store_manage/inventory_reciept/print_reciept',$data);
	}
}