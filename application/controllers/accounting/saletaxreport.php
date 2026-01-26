<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Saletaxreport extends CI_Controller
{

	protected $_customer_code;

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/order_util');
		$this->load->library('util/encryption_util');

		$this->load->library("businesslogic/account/lazada_report_sale");
		$this->load->library("businesslogic/account/shopee_report_sale");
		$this->load->library("businesslogic/account/tiktok_report_sale");


		$this->load->library("businesslogic/lazada_bl");
		$this->load->library("businesslogic/shopee_bl");
		$this->load->library("businesslogic/tiktok_bl");

    	$this->load->model('lazada_orders_model');
   		$this->load->model('shopee_orders_model');
		$this->load->model('web_shop_model');
		$this->load->model('tiktok_orders_model');

        //$this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata('customer_code');
     }

	public function saletaxreport_list()
	{
		$arr_input = array(
			'title' => "Accounting"
		);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/daterangepicker.css',
		);

		$arr_js = array(
	      'saletaxreport_js' => base_url().'resources/js/account/saletaxreport.js'
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'platform' => "",
 			'ordernumber' => "",
 			'daterange' => ""
	 	);

	 	$customer_type_en = get_cookie(COOKIE_PREFIX.'customer_type');
		$customer_type = $this->encryption_util->decrypt_ssl($customer_type_en);

	    $data = array(
	    	'customer_type' => $customer_type,
	    	'arr_search' => $arr_search,
	    	'is_chk' => 'NO'
	    );
              
        $this->view_util->load_view_main('accounting/saletaxreport/saletaxreport_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_SALETAXREPORT);
	}

	public function saletaxreport_history()
	{
		$arr_input = array(
			'title' => "Accounting"
		);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/daterangepicker.css',
		);

		$arr_js = array(
	      'saletaxreport_js' => base_url().'resources/js/account/saletaxreport.js'
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'platform' => "",
 			'ordernumber' => "",
 			'daterange' => ""
	 	);

	 	$customer_type_en = get_cookie(COOKIE_PREFIX.'customer_type');
		$customer_type = $this->encryption_util->decrypt_ssl($customer_type_en);

	    $data = array(
	    	'customer_type' => $customer_type,
	    	'arr_search' => $arr_search,
	    	'is_chk' => 'NO'
	    );
              
        $this->view_util->load_view_main('accounting/saletaxreport/saletaxreport_history',$data,$arr_css,$arr_js,$arr_input,56);
	}

	function saletaxreport_prep(){

		$platform=$this->input->post('platform');
		$daterange=$this->input->post('daterange');
		$file_upload = $_FILES['upload_file1']['name'];

		//echo $platform.">>".$daterange;

		if($platform == "0"){ // lazada
			//echo "lazada";

			$arr_data_prep = $this->lazada_bl->chk_prep($daterange,$file_upload);

			//print_r($arr_sho_prep);

		}elseif($platform == "1"){ // Shopee

			$arr_data_prep = $this->shopee_bl->chk_prep($daterange,$file_upload);

			//print_r($arr_sho_prep);


		}elseif($platform == "2"){ // Tiktok
			//echo "Tiktok";

			$arr_data_prep = $this->tiktok_bl->chk_prep($daterange,$file_upload);

			//print_r($arr_data_prep);

			/*$arr_data_prep=array(
		        'total_price_api' => 0,
		        'total_price_excel' => 0,
		        'total_cn_excel' => 0,
		        'total_price_cn_excel' => 0,
		        'arr_order_check' => array()
		    );*/
		}


		$arr_input = array(
			'title' => "Accounting"
		);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/daterangepicker.css',
		);

		$arr_js = array(
	      'saletaxreport_js' => base_url().'resources/js/account/saletaxreport.js'
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'platform' => $platform,
 			'ordernumber' => "",
 			'daterange' => $daterange
	 	);

	 	$customer_type_en = get_cookie(COOKIE_PREFIX.'customer_type');
		$customer_type = $this->encryption_util->decrypt_ssl($customer_type_en);

	    $data = array(
	    	'customer_type' => $customer_type,
	    	'arr_data_prep' => $arr_data_prep,
	    	'arr_search' => $arr_search,
	    	'is_chk' => 'YES'	
	    );
              
        $this->view_util->load_view_main('accounting/saletaxreport/saletaxreport_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_SALETAXREPORT);


	}

	function saletaxreport_make(){

		$platform=$this->uri->segment(4);
		//echo "--->>".$this->uri->segment(4)."<<---";
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");
	     //prepdata
	     //Lazada
	     //echo $platform."--".$StartDate."--".$EndDate;
	     
	  if($platform == "0"){
	      //echo $StartDate."<>".$EndDate;
	      $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_lazada_make = $this->lazada_report_sale->make_taxinvoice_group($arr_lazada);
	      //print_r($arr_lazada_make);

	      $validdata = 0;
	      if(!empty($arr_lazada)){
	        $validdata = 1;
	      }

	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'lazada_orders'=>$arr_lazada_make

	      );
	                       
	    $this->load->view('accounting/saletaxreport/lazada_saletaxreportpages',$data);

	  }elseif($platform == "1"){ // Shopee

	    $platform=$this->uri->segment(4);
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");

	     //prepdat

	      $arr_shopee=$this->shopee_orders_model->shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
	      //print_r($arr_shopee);
	      //echo "cnt>>".count($arr_shopee)."<br>";
	      $arr_shopee_make = $this->shopee_report_sale->make_taxinvoice_group($arr_shopee);
	      //print_r($arr_lazada_make);
	      $validdata = 0;
	      if(!empty($arr_shopee)){
	        $validdata = 1;
	      }
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'shopee_orders'=>$arr_shopee_make

	      );
	                      
	    $this->load->view('accounting/saletaxreport/shopee_saletaxreportpages',$data);
	  }elseif($platform == "2"){ // Tiktok

	    $platform=$this->uri->segment(4);
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");

	     //prepdata

	      $arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_tiktok_make = $this->tiktok_report_sale->make_taxinvoice_group($arr_tiktok);
	      //print_r($arr_lazada_make);
	      $validdata = 0;
	      if(!empty($arr_tiktok)){
	        $validdata = 1;
	      }
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'tiktok_orders'=>$arr_tiktok_make

	      );
	                      
	    $this->load->view('accounting/saletaxreport/tiktok_saletaxreportpages',$data);
	  }

	}

	function laz_salereport_more(){

	  $orderval = $this->uri->segment(4);
	  $order_start = "";
	  $order_end = "";
	  $validdata = 0;
	  $arr_lazada = "";
	  //$date_delivery_out = '2024-02-01';

	 // echo $orderval;
	  $ex1 = explode("-",$orderval);
	  if( (!empty($ex1[0])) and (!empty($ex1[1])) ){
	  //echo substr($ex1[0],3,11);
	      $order_start = substr($ex1[0],3,11);
	      $order_end = substr($ex1[1],3,11);
	      //echo $order_start."<>".$order_end;

	      $arr_lazada=$this->lazada_orders_model->select_order_groupby_orderno($order_start,$order_end);
	      $validdata = 1;
	  }

	    $data=array(
	      'validdata'=>$validdata,
	      'order_start'=>$order_start,
	      'order_end'=>$order_end,
	      'lazada_orders'=>$arr_lazada
	    );
	                     
	   // $this->load->library('pdf');
	   // $html = $this->load->view('admin/accounting/saletaxreportpages', $data, true);
	   // $this->pdf->createPDF($html, 'mypdf', false);

	  $this->load->view('accounting/saletaxreport/laz_saletaxreportpagesmore',$data);
	}

	function sho_salereport_more(){

	  $orderval = $this->uri->segment(4);
	  $order_start = "";
	  $order_end = "";
	  $validdata = 0;
	  $arr_shopee = "";

	 // echo $orderval;
	  $ex1 = explode("-",$orderval);
	  if( (!empty($ex1[0])) and (!empty($ex1[1])) ){
	  //echo substr($ex1[0],3,11);
	    $order_start = substr($ex1[0],3,11);
	    $order_end = substr($ex1[1],3,11);
	    $arr_shopee=$this->shopee_orders_model->shopee_select_order_with_OrdernoStart_OrderEnd($order_start,$order_end);
	    $validdata = 1;
	  }

	  $data=array(
	    'validdata'=>$validdata,
	    'order_start'=>$order_start,
	    'order_end'=>$order_end,
	    'shopee_orders'=>$arr_shopee
	  );
	  //print_r($data);
	                   
	 // $this->load->library('pdf');
	 // $html = $this->load->view('admin/accounting/saletaxreportpages', $data, true);
	 // $this->pdf->createPDF($html, 'mypdf', false);

		$this->load->view('accounting/saletaxreport/shopee_saletaxreportpagesmore',$data);
	}

	function tik_salereport_more(){

	  $orderval = $this->uri->segment(4);
	  $order_start = "";
	  $order_end = "";
	  $validdata = 0;
	  $arr_tiktok = "";

	 // echo $orderval;
	  $ex1 = explode("-",$orderval);
	  if( (!empty($ex1[0])) and (!empty($ex1[1])) ){
	  //echo substr($ex1[0],3,11);
	    $order_start = substr($ex1[0],3,11);
	    $order_end = substr($ex1[1],3,11);
	    $arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_with_OrderStart_OrderEnd($order_start,$order_end);
	    //print_r($arr_tiktok);
	    $validdata = 1;
	  }

	  $data=array(
	    'validdata'=>$validdata,
	    'order_start'=>$order_start,
	    'order_end'=>$order_end,
	    'tiktok_orders'=>$arr_tiktok
	  );
	  //print_r($data);
	                   
	 // $this->load->library('pdf');
	 // $html = $this->load->view('admin/accounting/saletaxreportpages', $data, true);
	 // $this->pdf->createPDF($html, 'mypdf', false);

		$this->load->view('accounting/saletaxreport/tiktok_saletaxreportpagesmore',$data);
	}

	function del_data_platform(){

		$platform_del=$this->input->post('platform_del');
		$inputDate=$this->input->post('del_ym');
		
		if($platform_del == 0){//lazada

			if(strlen($inputDate) == 4){
	        	//$year_month = '2410';

	              $startDate = DateTime::createFromFormat('ym', $inputDate);
	              if (!$startDate) {
	                  die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
	              }

	              $currentDate = new DateTime();
	              while ($startDate <= $currentDate) {
	              	$ym = $startDate->format('ym');

	              	//echo "Lazada del >> ".$ym."<br>";

	        		$this->lazada_orders_model->delete_lazada_order_by_year_month($ym);

	        		$startDate->modify('+1 month');
	        	}
	        }

		}elseif($platform_del == 1){//shopee

			if(strlen($inputDate) == 4){
	        	//$year_month = '2410';

	              $startDate = DateTime::createFromFormat('ym', $inputDate);
	              if (!$startDate) {
	                  die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
	              }

	              $currentDate = new DateTime();
	              while ($startDate <= $currentDate) {
	              	$ym = $startDate->format('ym');
	              	//echo "Shopee del >> ".$ym."<br>";

	        		$this->shopee_orders_model->delete_shopee_order_by_year_month($ym);

	        		$startDate->modify('+1 month');
	        	}
	        }

		}

		redirect(base_url().'accounting/saletaxreport/saletaxreport_list', 'refresh');

	}

	
}