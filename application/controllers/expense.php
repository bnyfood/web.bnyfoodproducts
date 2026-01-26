<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Expense extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/random_util');
		$this->load->library("util/date_util");

		$this->load->library('businesslogic/curl_bl');
		$this->load->library('businesslogic/upload_bl');

		$this->load->model('web_material_model');
		$this->load->model('web_supplier_model');
		$this->load->model('web_purchase_order_model');
		$this->load->model('bny_expense_model');
		$this->load->model('bankaccount_model');
		$this->load->model('web_bankaccount_model');

        //$this->auth_bl->check_session_exists();

     }

    public function expense_list()
	{

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		//echo $sess_shop_id;

		//$arr_pos = $this->web_purchase_order_model->get_by_shop($sess_shop_id,CONTENT_PER_PAGE);

		$data_search = array(
			'expense_search' => '',
			'daterange' => ''
		);
		
		$data = array(
			'data_search' => $data_search
		);
		//print_r($data);
		
		$arr_input = array(
			'title' => "Expense"
		);
		
		$arr_js = array(
			'morecontent' => base_url()."resources/js/morecontent/expense/expense_list.js",
			'table_load_sort' => base_url()."resources/js/table_load_sort.js"
        	//'init_main' => base_url()."resources/js/init/main.js"
    	);

    	$arr_css = array(
			//'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
			'daterangepicker' => base_url().'resources/css/daterangepicker/daterangepicker.css'
			//'site_new' => base_url()."assets/css/site_new.css"
		);

		$this->view_util->load_view_main('expense/expense_list',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);

	}  

	function loaddata_more_ajax(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$expense_search = $this->input->post('expense_search');

		$daterange = $this->input->post('daterange');
		$arr_date = $this->date_util->get_start_stop_from_date_range($daterange);
		$expense_start = $arr_date['start'];
		$expense_stop = $arr_date['stop'];

		$offset = $this->input->post('offset');
		$sortby = $this->input->post('sortby');
		$sorttype = $this->input->post('sorttype');

		$data = array(
			'expense_search' => $expense_search,
			'expense_start' => $expense_start,
			'expense_stop' => $expense_stop,
			'shopid_en' => $sess_shop_id,
			'sortby' => $sortby,
			'sorttype' => $sorttype,
			'offset' => $offset,
			'per_page' => 5

		);

		$arr_expenses = $this->curl_bl->CallApiNospi('POST','expense/loaddata_more',$data);

		if($arr_expenses['Status'] == "Success"){
			$max = sizeof($arr_expenses['Data']);

			for($i=0;$i<$max;$i++){
				$arr_expenses['Data'][$i]['bny_expense_id'] = $this->encryption_util->encrypt_ssl($arr_expenses['Data'][$i]['bny_expense_id']);
			}
		}

		$arr_data = array(
			'list_data' => $arr_expenses['Data']
		);
		echo json_encode($arr_data);


	}

	function expense_add_form(){

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$arr_input = array(
			'title' => "Expense"
		);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/bootstrap-datepicker.css',
		);
		
		$arr_js = array(

        	'bootstrap-datepicker' => base_url()."resources/js/datepicker/bootstrap-datepicker.js",
        	'expense_add_form' => base_url()."resources/js/expense/expense_add_form.js"
    	);	
		
		$arr_suppliers = $this->web_supplier_model->get_by_shop($sess_shop_id,5);
		$arr_banks = $this->bankaccount_model->get_all();


		//print_r($arr_bankaccounts);


		$data = array(
			'arr_suppliers' => $arr_suppliers['Data'],
			'arr_banks' => $arr_banks['Data'],
		);
		
		$this->view_util->load_view_main('expense/expense_add_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function expense_add(){

		$upload_slip = 'upload_slip';
		$upload_slip_name = $this->upload_bl->upload_file_pic_path('./uploads/slip/',$upload_slip);
		$upload_invoice = 'upload_invoice';
		$upload_invoice_name = $this->upload_bl->upload_file_pic_path('./uploads/invoice/',$upload_invoice);

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$expense_date = $this->input->post('expense_date');
		$expen_time_hr = $this->input->post('expen_time_hr');
		$expen_time_min = $this->input->post('expen_time_min');
		$expense_date_time = $expense_date." ".$expen_time_hr.":".$expen_time_min;
		$supplier_type = $this->input->post('supplier_type');
		$supplier_name = $this->input->post('supplier_name');
		$expense_amount = $this->input->post('expense_amount');
		$expense_vat = $this->input->post('expense_vat');
		$account_type = $this->input->post('account_type');
		$bankaccount_id = $this->input->post('bankaccount_id');
		$bookbank_number = $this->input->post('bookbank_number');
		$bookbank_name = $this->input->post('bookbank_name');
		$slip_amount = $this->input->post('slip_amount');
		$slip_date = $this->input->post('slip_date');
		$slip_time_hr = $this->input->post('slip_time_hr');
		$slip_time_min = $this->input->post('slip_time_min');
		$slip_date_time = $slip_date." ".$slip_time_hr.":".$slip_time_min;

		$web_supplier_id = $this->input->post('web_supplier_id');
		$web_bankaccount_id = $this->input->post('web_bankaccount_id');
		$web_purchase_order_id = $this->input->post('web_purchase_order_id');

		/*if($supplier_type == 1){

			$web_supplier_id = $this->input->post('web_supplier_id');

		}elseif($supplier_type == 2){

			$supplier_name = $this->input->post('supplier_name');

			$data_sup = array(
				'supplier_name' => $supplier_name,
				'shop_id_en' => $shop_id_en
			);

			$arr_res = $this->curl_bl->CallApi('POST','web_supplier/supplier_add',$data_sup);
			$web_supplier_id = $arr_res['Data'];

		}


		$expense_amount = $this->input->post('expense_amount');
		$expense_vat = $this->input->post('expense_vat');
		$account_type = $this->input->post('account_type');

		if($account_type == 1){

			$web_bankaccount_id = $this->input->post('web_bankaccount_id');

		}elseif($account_type == 2){

			$bankaccount_id = $this->input->post('bankaccount_id');
			$bookbank_number = $this->input->post('bookbank_number');
			$bookbank_name = $this->input->post('bookbank_name');

			$data_sup = array(
				'bankaccount_id' => $bankaccount_id,
				'bookbank_number' => $bookbank_number,
				'bookbank_name' => $bookbank_name,
				'shop_id_en' => $shop_id_en
			);

			$arr_res = $this->curl_bl->CallApi('POST','web_bankaccount/bankaccount_add',$data_sup);
			$web_bankaccount_id = $arr_res['Data'];

		}

		$slip_amount = $this->input->post('slip_amount');
		$slip_date = $this->input->post('slip_date');
		$slip_time_hr = $this->input->post('slip_time_hr');
		$slip_time_min = $this->input->post('slip_time_min');
		$slip_date_time = $slip_date." ".$slip_time_hr.":".$slip_time_min;

		$data_expense = array(
			'expense_date' => $expense_date_time,
			'web_supplier_id' => $web_supplier_id,
			'web_bankaccount_id' => $web_bankaccount_id,
			'expense_amount' => $expense_amount,
			'expense_vat' => $expense_vat,
			'shop_id_en' => $shop_id_en
		);

		$arr_res = $this->curl_bl->CallApi('POST','web_bankaccount/bankaccount_add',$data_sup);
		*/

		$data_add = array(

			'shop_id_en' => $shop_id_en,
			'web_purchase_order_id' => $web_purchase_order_id,
			'expense_date' => $expense_date_time,
			'expense_amount' => $expense_amount,
			'expense_vat' => $expense_vat,
			'supplier_type' => $supplier_type,
			'web_supplier_id' => $web_supplier_id,
			'supplier_name' => $supplier_name,
			'account_type' => $account_type,
			'web_bankaccount_id' => $web_bankaccount_id,
			'bankaccount_id' => $bankaccount_id,
			'bookbank_number' => $bookbank_number,
			'bookbank_name' => $bookbank_name,
			//'slip_amount' => $slip_amount,
			//'slip_date_time' => $slip_date_time,
			'slip_pic' => $upload_slip_name,
			'invoice_pic' => $upload_invoice_name

		);

		print_r($data_add);

		$arr_res = $this->curl_bl->CallApi('POST','expense/expense_add',$data_add);

	}

	function expense_edit_form(){

		$bny_expense_id  = $this->uri->segment(3);

		$sess_shop_id = $this->session->userdata(SESSION_PREFIX.'shop_id');

		$arr_input = array(
			'title' => "Expense"
		);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/bootstrap-datepicker.css',
		);
		
		$arr_js = array(

        	'bootstrap-datepicker' => base_url()."resources/js/datepicker/bootstrap-datepicker.js",
        	'expense_add_form' => base_url()."resources/js/expense/expense_add_form.js"
    	);	

		$arr_expense = $this->curl_bl->CallApi('GET','expense/get_by_expense_id_join_slip/'.$bny_expense_id);

		$arr_bankaccount = $this->curl_bl->CallApi('GET','web_bankaccount/get_by_id_join/'.$arr_expense['Data']['web_bankaccount_id']);

		$arr_po_data = $this->curl_bl->CallApi('GET','purchase_order/get_by_web_purchase_order_id/'.$arr_expense['Data']['web_purchase_order_id']);


		$web_supplier_id_en = $this->encryption_util->encrypt_ssl($arr_expense['Data']['web_supplier_id']);
		$arr_supplier = $this->web_supplier_model->get_by_id($web_supplier_id_en);
		$arr_banks = $this->bankaccount_model->get_all();

		$arr_expense_date = explode(" ", $arr_expense['Data']['expense_date']);
		$expense_date =  $arr_expense_date[0];
		$arr_expense_time = explode(":", $arr_expense_date[1]);
		$expense_hr =  $arr_expense_time[0];
		$expense_min =  $arr_expense_time[1];

		$arr_slip_date = explode(" ", $arr_expense['Data']['slip_date']);
		$slip_date =  $arr_slip_date[0];
		$arr_slip_time = explode(":", $arr_slip_date[1]);
		$slip_hr =  $arr_slip_time[0];
		$slip_min =  $arr_slip_time[1];


		$data = array(
			'arr_expense' => $arr_expense['Data'],
			'arr_bankaccount' => $arr_bankaccount['Data'],
			'arr_po_data' => $arr_po_data['Data'],
			'arr_supplier' => $arr_supplier['Data'],
			'arr_banks' => $arr_banks['Data'],
			'expense_date' => $expense_date,
			'expense_hr' => $expense_hr,
			'expense_min' => $expense_min,
			'slip_date' => $slip_date,
			'slip_hr' => $slip_hr,
			'slip_min' => $slip_min
		);

		print_r($data);
		
		$this->view_util->load_view_main('expense/expense_edit_form',$data,$arr_css,$arr_js,$arr_input,MENU_CONFIG_USER);
	}

	function expense_edit(){

		$upload_slip = 'upload_slip';
		$upload_slip_name = $this->upload_bl->upload_file_pic_path('./uploads/expense/',$upload_slip);
		$upload_invoice = 'upload_invoice';
		$upload_invoice_name = $this->upload_bl->upload_file_pic_path('./uploads/expense/',$upload_invoice);

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$expense_date = $this->input->post('expense_date');
		$expen_time_hr = $this->input->post('expen_time_hr');
		$expen_time_min = $this->input->post('expen_time_min');
		$expense_date_time = $expense_date." ".$expen_time_hr.":".$expen_time_min;
		$supplier_type = $this->input->post('supplier_type');
		$supplier_name = $this->input->post('supplier_name');
		$expense_amount = $this->input->post('expense_amount');
		$expense_vat = $this->input->post('expense_vat');
		$account_type = $this->input->post('account_type');
		$bankaccount_id = $this->input->post('bankaccount_id');
		$bookbank_number = $this->input->post('bookbank_number');
		$bookbank_name = $this->input->post('bookbank_name');
		/*$slip_amount = $this->input->post('slip_amount');
		$slip_date = $this->input->post('slip_date');
		$slip_time_hr = $this->input->post('slip_time_hr');
		$slip_time_min = $this->input->post('slip_time_min');
		$slip_date_time = $slip_date." ".$slip_time_hr.":".$slip_time_min;*/

		$web_supplier_id = $this->input->post('web_supplier_id');
		$web_bankaccount_id = $this->input->post('web_bankaccount_id');
		$web_purchase_order_id = $this->input->post('web_purchase_order_id');

		$data_add = array(

			'shop_id_en' => $shop_id_en,
			'web_purchase_order_id' => $web_purchase_order_id,
			'expense_date' => $expense_date_time,
			'expense_amount' => $expense_amount,
			'expense_vat' => $expense_vat,
			'supplier_type' => $supplier_type,
			'web_supplier_id' => $web_supplier_id,
			'supplier_name' => $supplier_name,
			'account_type' => $account_type,
			'web_bankaccount_id' => $web_bankaccount_id,
			'bankaccount_id' => $bankaccount_id,
			'bookbank_number' => $bookbank_number,
			'bookbank_name' => $bookbank_name,
			//'slip_amount' => $slip_amount,
			//'slip_date_time' => $slip_date_time,
			'slip_pic' => $upload_slip_name,
			'invoice_pic' => $upload_invoice_name

		);

		print_r($data_add);

		//$arr_res = $this->curl_bl->CallApi('POST','expense/expense_add',$data_add);

	}

	function po_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$po_search = $this->input->post('po_search');

		$data_curl = array(
			'po_search' => $po_search,
			'shop_id_en' => $shop_id_en
		);

		//print_r($data_curl);

		$arr_pos = $this->curl_bl->CallApiNospi('POST','purchase_order/po_search',$data_curl);


		$arr_data = array(
			'arr_pos' => $arr_pos['Data']
		);
		echo json_encode($arr_data);
	}

	function bankaccount_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$accounttxt_search = $this->input->post('accounttxt_search');

		$data_curl = array(
			'accounttxt_search' => $accounttxt_search,
			'shop_id_en' => $shop_id_en
		);

		//print_r($data_curl);

		$arr_bankaccounts = $this->curl_bl->CallApiNospi('POST','web_bankaccount/bankaccount_search',$data_curl);


		$arr_data = array(
			'arr_bankaccounts' => $arr_bankaccounts['Data']
		);
		echo json_encode($arr_data);
	}

	function supplier_search(){

		$shop_id_en = $this->session->userdata(SESSION_PREFIX.'shop_id');
		$suppliertxt_search = $this->input->post('suppliertxt_search');

		$data_curl = array(
			'suppliertxt_search' => $suppliertxt_search,
			'shop_id_en' => $shop_id_en
		);

		//print_r($data_curl);

		$arr_suppliers = $this->curl_bl->CallApiNospi('POST','web_supplier/supplier_search',$data_curl);


		$arr_data = array(
			'arr_suppliers' => $arr_suppliers['Data']
		);
		echo json_encode($arr_data);
	}

}