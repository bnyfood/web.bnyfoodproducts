<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Purchase_order extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/random_util');

		$this->load->library('businesslogic/curl_bl');

		$this->load->model('web_material_model');
		$this->load->model('web_supplier_model');

        //$this->auth_bl->check_session_exists();

     }
     
	public function material_list()
	{
		$sess_shop_id = $this->session->userdata('shop_id');
		//echo $sess_shop_id;

		$ran_num_pocode = $this->random_util->create_random_number(6);	

		$arr_materials = $this->web_material_model->get_by_shop($sess_shop_id,5);

		if($arr_materials['Status'] == "Success"){
			$max = sizeof($arr_materials['Data']);

			for($i=0;$i<$max;$i++){
				$arr_materials['Data'][$i]['web_material_id'] = $this->encryption_util->encrypt_ssl($arr_materials['Data'][$i]['web_material_id']);
			}
		}

		$arr_input = array(
			'title' => "Purchase order" 
		);

		$arr_css = array();
		
		$arr_js = array();		        

		$data = array(
			'arr_materials' => $arr_materials['Data'],
			'ran_num_pocode' => $ran_num_pocode
		);

				        
		$this->view_util->load_view_main('purchase_order/material_list',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);

	}

	function po_set(){
		
		$sess_shop_id = $this->session->userdata('shop_id');
		$ran_num_pocode = $this->random_util->create_random_number(6);	

		$arr_suppliers = $this->web_supplier_model->get_by_shop($sess_shop_id,5);

		$arr_input = array(
			'title' => "Purchase order"
		);

		$arr_css = array(
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'site_new' => base_url()."assets/css/site_new.css"
		);
		
		$arr_js = array(
			'jquery-1.9.1' => base_url().'global/vendor/jquery/jquery-1.9.1.js',
			//'jquery-migrate' => base_url().'global/vendor/jquery/jquery-migrate-1.0.0.js',
			'jquery-ui-1.10.3' => base_url().'global/vendor/jquery-ui/ui/jquery-ui-1.10.3.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			'fancy_main' => base_url().'resources/js/fancybox/fancy_main.js',
			'po_set' => base_url().'resources/js/purchase_order/po_set.js',
			/*'jquery1.8.3' => base_url().'global/vendor/jquery/jquery-1.8.3.js',
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			'fancy_main' => base_url().'resources/js/fancybox/fancy_main.js',*/
		
		);		        

		$data = array(
			'arr_suppliers' => $arr_suppliers['Data'],
			'ran_num_pocode' => $ran_num_pocode
		);
				        
		$this->view_util->load_view_main('purchase_order/po_set',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function material_search_by_name(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$txt_ma = $this->input->post('txt_ma');
		$web_supplier_id = $this->input->post('web_supplier_id');

		$data_curl = array(
			'txt_ma' => $txt_ma,
			'ShopID' => $sess_shop_id,
			'web_supplier_id' => $web_supplier_id
		);

		$arr_data_res = $this->curl_bl->CallApiNospi('POST','purchase_order/material_search_by_name',$data_curl);


		$arr_data = array(
			'arr_mat' => $arr_data_res['Data']['data_search']
			//'arr_compares' => $arr_data_res['Data']['data_compares']
		);

		echo json_encode($arr_data);
	}

	function material_search(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$krysearch = $this->input->post('krysearch');
		$web_supplier_id = $this->input->post('web_supplier_id');

		$data_curl = array(
			'web_supplier_id' => $web_supplier_id,
			'krysearch' => $krysearch,
			'ShopID' => $sess_shop_id
		);

		$arr_pos = $this->curl_bl->CallApiNospi('POST','purchase_order/material_search',$data_curl);
		$arr_mat = array();
		$name_mat = "";

		if(!empty($arr_pos['Data'])){
			foreach($arr_pos['Data'] as $material){
				$name_mat = $material['material_name']." ".$material['material_size']." ".$material['material_unit'];
				array_push($arr_mat,$name_mat);
			}
		}

		$arr_data = array(
			'availableTags' => $arr_mat
		);

		echo json_encode($arr_data);
	}

	function po_make(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$web_supplier_id = $this->input->post('web_supplier_id');
		$web_material_id = $this->input->post('web_material_id');
		$ran_num_pocode = $this->input->post('ran_num_pocode');


		$data_curl = array(
			'web_supplier_id' => $web_supplier_id,
			'web_material_id' => $web_material_id,
			'ran_num_pocode' => $ran_num_pocode,
			'ShopID' => $sess_shop_id
		);

		$arr_po_mats = $this->curl_bl->CallApiNospi('POST','purchase_order/po_make',$data_curl);

		$arr_data = array(
			'arr_mats' => $arr_po_mats['Data']['arr_mats'],
			'arr_suppliers' => $arr_po_mats['Data']['arr_suppliers']
		);

		echo json_encode($arr_data);

	}

	function po_del_by_code(){

		$ran_num_pocode = $this->input->post('ran_num_pocode');

		$data_curl = array(
			'ran_num_pocode' => $ran_num_pocode
		);

		$arr_res = $this->curl_bl->CallApiNospi('POST','purchase_order/po_del_by_code',$data_curl);

		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}
	}

	function material_compare(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$web_supplier_id = $this->input->post('web_supplier_id');
		$web_material_id = $this->input->post('web_material_id');

		$data_curl = array(
			'web_supplier_id' => $web_supplier_id,
			'web_material_id' => $web_material_id,
			'ShopID' => $sess_shop_id
		);

		$arr_po_mats = $this->curl_bl->CallApiNospi('POST','purchase_order/material_compare',$data_curl);

		$arr_data = array(
			'arr_po_compares' => $arr_po_mats['Data']
		);

		echo json_encode($arr_data);

	}

	function po_build(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$chk_material_id = $this->input->post('chk_material_id');
		$ran_num_pocode = $this->input->post('ran_num_pocode');
		
		//print_r($chk_material_id);

		$arr_curl = json_encode($chk_material_id);

		$data_curl = array(
			'arr_curl' => $arr_curl,
			'ran_num_pocode' => $ran_num_pocode,
			'ShopID' => $sess_shop_id
		);

		//print_r($data_curl);

		$arr_pos = $this->curl_bl->CallApi('POST','purchase_order/po_build',$data_curl);

		$arr_input = array(
			'title' => "Purchase order" 
		);

		$arr_css = array();
		
		$arr_js = array();		        

		$data = array(
			'arr_pos' => $arr_pos['Data']
		);


		redirect(base_url().'purchase_order/po_list_tmp/'.$ran_num_pocode,'refresh');
		//print_r($arr_pos['Data']);
				        
		//$this->view_util->load_view_main('purchase_order/po_build',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);
	}


	function po_list_tmp(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$ran_num_pocode  = $this->uri->segment(3);

		$data_curl = array(
			'ran_num_pocode' => $ran_num_pocode,
			'ShopID' => $sess_shop_id
		);

		//print_r($data_curl);

		$arr_pos = $this->curl_bl->CallApi('POST','purchase_order/po_list_tmp',$data_curl);

		$arr_input = array(
			'title' => "Purchase order" 
		);

		$arr_css = array();
		
		$arr_js = array();		        

		$data = array(
			'arr_pos' => $arr_pos['Data']
		);
				        
		$this->view_util->load_view_main('purchase_order/po_build',$data,NULL,NULL,$arr_input,MENU_CONFIG_USER);

	}

	function print_po(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$ran_num_pocode  = $this->uri->segment(3);

		$data_curl = array(
			'ran_num_pocode' => $ran_num_pocode,
			'ShopID' => $sess_shop_id
		);

		$arr_pos= $this->curl_bl->CallApi('POST','purchase_order/print_po',$data_curl);

		$validdata = 0;
	    if(!empty($arr_pos)){
	    	$validdata = 1;
	    }

		$data=array(
			'arr_pos'=>$arr_pos['Data'],
			'validdata'=>$validdata,
		);
	                       
	    $this->load->view('purchase_order/print_po',$data);
	}

	function del_material_action(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$web_purchase_material_id  = $this->uri->segment(3);
		$ran_num_pocode  = $this->uri->segment(4);

		$data_curl = array(
			'web_purchase_material_id' => $web_purchase_material_id,
			'ran_num_pocode' => $ran_num_pocode,
			'ShopID' => $sess_shop_id
		);

		$arr_pos= $this->curl_bl->CallApiNospi('POST','purchase_order/del_material_action',$data_curl);

		$arr_data = array(
			'arr_po_mats' => $arr_pos['Data']
		);

		echo json_encode($arr_data);

	}

	function sent_data_make_po(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$web_supplier_id = $this->input->post('web_supplier_id');
		$arr_po_adds = $this->input->post('arr_po_add');

		$arr_curl = json_encode($arr_po_adds);

		//print_r($arr_po_adds);

		

		$data_curl = array(
			'arr_po_adds' => $arr_curl,
			'web_supplier_id' => $web_supplier_id,
			'ShopID' => $sess_shop_id
		);

		$arr_po_mats = $this->curl_bl->CallApiNospi('POST','purchase_order/sent_data_make_po',$data_curl);

		$arr_data = array(
			'arr_mats' => $arr_po_mats['Data']['arr_mats'],
			'arr_suppliers' => $arr_po_mats['Data']['arr_suppliers']
		);

		echo json_encode($arr_data);



	}

	function change_material_price(){

		$sess_shop_id = $this->session->userdata('shop_id');
		$web_supplier_id = $this->input->post('web_supplier_id');
		$web_material_id = $this->input->post('web_material_id');
		$new_mat_price = $this->input->post('new_mat_price');

		$data_curl = array(
			'web_supplier_id' => $web_supplier_id,
			'web_material_id' => $web_material_id,
			'new_mat_price' => $new_mat_price,
			'status' => 1,
			'ShopID' => $sess_shop_id
		);

		$arr_new_price = $this->curl_bl->CallApiNospi('POST','purchase_order/change_material_price',$data_curl);

		$arr_data = array(
			'arr_new_price' => $arr_new_price['Data']
		);

		echo json_encode($arr_data);

	}


	function test_json(){

		$json_data = '
		[
		    {
		        "web_supplier_id": "1",
		        "ShopID": 5,
		        "supplier_name": "Macro",
		        "supplier_address": "45/99",
		        "supplier_headoffice": 1,
		        "supplier_branchid": null,
		        "supplier_tax": "456rr1",
		        "supplier_vat": 0,
		        "supplier_person": 1,
		        "supplier_line": "1L11",
		        "phoneno1": "08755544441",
		        "phoneno2": "0896655551",
		        "supplier_email": "eee@gmail.com1",
		        "supplier_province": "67",
		        "supplier_district": "817",
		        "supplier_subdistrict": "6553",
		        "supplier_zip": "83000",
		        "supplier_discription": "d1",
		        "cdate": "2022-09-05 12:11:46.943"
		    },
		    {
		        "web_supplier_id": "5",
		        "ShopID": 5,
		        "supplier_name": "ซุปเปอร์ชีพ",
		        "supplier_address": "",
		        "supplier_headoffice": 1,
		        "supplier_branchid": null,
		        "supplier_tax": "123",
		        "supplier_vat": 1,
		        "supplier_person": 1,
		        "supplier_line": "",
		        "phoneno1": "05585456",
		        "phoneno2": "",
		        "supplier_email": "",
		        "supplier_province": "0",
		        "supplier_district": "0",
		        "supplier_subdistrict": "0",
		        "supplier_zip": "0",
		        "supplier_discription": "",
		        "cdate": "2022-09-20 12:03:16.380"
		    },
		    {
		        "web_supplier_id": "6",
		        "ShopID": 5,
		        "supplier_name": "Lotus",
		        "supplier_address": "",
		        "supplier_headoffice": 1,
		        "supplier_branchid": null,
		        "supplier_tax": "",
		        "supplier_vat": 0,
		        "supplier_person": 0,
		        "supplier_line": "",
		        "phoneno1": "",
		        "phoneno2": "",
		        "supplier_email": "",
		        "supplier_province": "0",
		        "supplier_district": "0",
		        "supplier_subdistrict": "0",
		        "supplier_zip": "0",
		        "supplier_discription": "",
		        "cdate": "2023-02-09 08:30:44.977"
		    },
		    {
		        "web_supplier_id": "7",
		        "ShopID": 5,
		        "supplier_name": "BigC",
		        "supplier_address": "",
		        "supplier_headoffice": 1,
		        "supplier_branchid": null,
		        "supplier_tax": "",
		        "supplier_vat": 0,
		        "supplier_person": 0,
		        "supplier_line": "",
		        "phoneno1": "",
		        "phoneno2": "",
		        "supplier_email": "",
		        "supplier_province": "0",
		        "supplier_district": "0",
		        "supplier_subdistrict": "0",
		        "supplier_zip": "0",
		        "supplier_discription": "",
		        "cdate": "2023-02-09 10:05:39.390"
		    }
		]';

		$arr_data = array(
			'arr_new' => $json_data
		);

		echo json_encode($arr_data);
	}

}