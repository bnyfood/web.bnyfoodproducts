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

	function attach_ai_debug($data, $extra = array())
	{
		if (!is_array($data)) {
			$data = array();
		}
		$data['bny_ai_debug'] = $this->view_util->build_ai_debug($extra);
		$data['bny_ai_debug_html'] = $this->view_util->ai_debug_html($data['bny_ai_debug']);
		return $data;
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

	public function saletaxreport_delete()
	{
		$arr_input = array(
			'title' => "Accounting"
		);

		$customer_type_en = get_cookie(COOKIE_PREFIX.'customer_type');
		$customer_type = $this->encryption_util->decrypt_ssl($customer_type_en);

	    $data = array(
	    	'customer_type' => $customer_type
	    );

        $this->view_util->load_view_main('accounting/saletaxreport/saletaxreport_delete',$data,NULL,NULL,$arr_input,MENU_ACCOUNT_SALETAX_DELETE);
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
              
        $this->view_util->load_view_main('accounting/saletaxreport/saletaxreport_history',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_SALETAX_PRINT);
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
		//echo "--->>".$this->uri->segment(4)."<<----";
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");
	     //prepdata
	     //Lazada
	     //echo $platform."--".$StartDate."--".$EndDate;
	     
	  if($platform == "0"){
	      //echo $StartDate."<>".$EndDate;
	      $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
	      $arr_lazada = $this->lazada_bl->filter_ignore_from_tax_orders($arr_lazada);
	      $arr_lazada_make = $this->lazada_report_sale->make_taxinvoice_group($arr_lazada);

	      $validdata = 0;
	      if(!empty($arr_lazada_make)){
	        $validdata = 1;
	      }

	      $data=$this->attach_ai_debug(array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'lazada_orders'=>$arr_lazada_make

	      ), array(
	        'platform' => $platform,
	        'StartDate' => $StartDate,
	        'EndDate' => $EndDate,
	        'source' => 'sale.laz'
	      ));
	                       
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
	      $data=$this->attach_ai_debug(array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'shopee_orders'=>$arr_shopee_make

	      ), array(
	        'platform' => $platform,
	        'StartDate' => $StartDate,
	        'EndDate' => $EndDate,
	        'source' => 'sale.sho.proc'
	      ));
	                      
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
	      $data=$this->attach_ai_debug(array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'tiktok_orders'=>$arr_tiktok_make
	      ), array(
	        'platform' => $platform,
	        'StartDate' => $StartDate,
	        'EndDate' => $EndDate,
	        'source' => 'sale.tik'
	      ));
	                      
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
	      $arr_lazada = $this->lazada_bl->filter_ignore_from_tax_orders($arr_lazada);
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
	    //$order_start = substr($ex1[0],3,11);
	    //$order_end = substr($ex1[1],3,11);

		$order_start = $ex1[0];
	    $order_end = $ex1[1];

		//echo "start>>".$order_start." Stop>>".$order_end;
	    $arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_with_OrderStart_OrderEnd($order_start,$order_end);
	    //print_r($arr_tiktok);
	    $validdata = 1;
	  }

	  $extra = array(
	    'orderval' => $orderval,
	    'source' => 'sale.tik.more'
	  );
	  if (!empty($arr_tiktok) && is_array($arr_tiktok)) {
	    $this->load->model('tiktok_order_payment_model');
	    $row0 = $arr_tiktok[0];
	    $oid = isset($row0['order_id']) ? $row0['order_id'] : '';
	    $extra['row1_price'] = isset($row0['price']) ? $row0['price'] : '';
	    $extra['row1_seller'] = isset($row0['voucher_seller']) ? $row0['voucher_seller'] : (isset($row0['seller_discount']) ? $row0['seller_discount'] : '');
	    $extra['row1_oid'] = $oid;
	      if ($oid !== '') {
	      $pays = $this->tiktok_order_payment_model->select_by_orderid($oid);
	      $pay = (!empty($pays) && is_array($pays)) ? $pays[0] : array();
	      if (!empty($pay)) {
	        $extra['pay_orig'] = isset($pay['original_total_product_price']) ? $pay['original_total_product_price'] : '';
	        $extra['pay_sub'] = isset($pay['sub_total']) ? $pay['sub_total'] : '';
	        $extra['pay_seller'] = isset($pay['seller_discount']) ? $pay['seller_discount'] : '';
	        $extra['pay_plat'] = isset($pay['platform_discount']) ? $pay['platform_discount'] : '';
	        $extra['pay_ship'] = isset($pay['shipping_fee']) ? $pay['shipping_fee'] : '';
	        $extra['pay_total'] = isset($pay['total_amount']) ? $pay['total_amount'] : '';
	      }
	      $this->db->select('original_price, order_qty, seller_sku');
	      $this->db->from('tiktok_line_items');
	      $this->db->where('order_id', $oid);
	      $lines = $this->db->get()->result_array();
	      $sum_orig = 0.0;
	      if (!empty($lines) && is_array($lines)) {
	        foreach ($lines as $ln) {
	          $qty = isset($ln['order_qty']) ? floatval($ln['order_qty']) : 1.0;
	          if ($qty <= 0) { $qty = 1.0; }
	          $sum_orig += (isset($ln['original_price']) ? floatval($ln['original_price']) : 0.0) * $qty;
	        }
	      }
	      $extra['line_sum_orig'] = round($sum_orig, 2);
	    }
	  }

	  $data=$this->attach_ai_debug(array(
	    'validdata'=>$validdata,
	    'order_start'=>$order_start,
	    'order_end'=>$order_end,
	    'tiktok_orders'=>$arr_tiktok
	  ), $extra);
	  //print_r($data);
	                   
	 // $this->load->library('pdf');
	 // $html = $this->load->view('admin/accounting/saletaxreportpages', $data, true);
	 // $this->pdf->createPDF($html, 'mypdf', false);

		$this->load->view('accounting/saletaxreport/tiktok_saletaxreportpagesmore',$data);
	}

	/**
	 * Secret probe: payment vs report price for one TikTok tax invoice.
	 * /accounting/saletaxreport/probe_tiktok_price/TTK20260700001?secret=...
	 */
	function probe_tiktok_price()
	{
		$secret = (string)$this->input->get('secret');
		if (!defined('ADS_WEBHOOK_SECRET') || $secret !== ADS_WEBHOOK_SECRET) {
			show_404();
			return;
		}
		$inv = trim((string)$this->uri->segment(4));
		header('Content-Type: application/json; charset=utf-8');
		if ($inv === '') {
			echo json_encode(array('error' => 'missing invoice'));
			return;
		}
		$this->load->model('tiktok_order_payment_model');
		$this->db->select('taxinvoiceID, order_id');
		$this->db->from('tiktok_taxinvoiceid');
		$this->db->where('taxinvoiceID', $inv);
		$invrow = $this->db->get()->row_array();
		if (empty($invrow)) {
			echo json_encode(array('error' => 'invoice not found', 'inv' => $inv));
			return;
		}
		$oid = $invrow['order_id'];
		$pays = $this->tiktok_order_payment_model->select_by_orderid($oid);
		$pay = (!empty($pays) && is_array($pays)) ? $pays[0] : null;
		$this->db->select('*');
		$this->db->from('tiktok_line_items');
		$this->db->where('order_id', $oid);
		$lines = $this->db->get()->result_array();
		$sum_orig = 0.0;
		foreach ($lines as $ln) {
			$qty = isset($ln['order_qty']) ? floatval($ln['order_qty']) : 1.0;
			if ($qty <= 0) { $qty = 1.0; }
			$sum_orig += (isset($ln['original_price']) ? floatval($ln['original_price']) : 0.0) * $qty;
		}
		$sp = $this->tiktok_orders_model->tiktok_select_order_with_OrderStart_OrderEnd($inv, $inv);
		$sp_row = (!empty($sp) && is_array($sp)) ? $sp[0] : null;
		$sp_def = null;
		$qdef = $this->db->query("SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_select_order_with_OrderStart_OrderEnd')) AS d");
		if ($qdef) {
			$rdef = $qdef->row_array();
			$sp_def = isset($rdef['d']) ? $rdef['d'] : null;
		}
		$sp_def_date = null;
		$qdef2 = $this->db->query("SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_select_order_with_DateStart_DateEnd')) AS d");
		if ($qdef2) {
			$rdef2 = $qdef2->row_array();
			$sp_def_date = isset($rdef2['d']) ? $rdef2['d'] : null;
		}
		$apply = (string)$this->input->get('apply');
		$applied = null;
		if ($apply === '1') {
			$sql = "
ALTER PROCEDURE [dbo].[tiktok_select_order_with_OrderStart_OrderEnd]
	@OrderStart varchar(50),
	@OrderEnd varchar(50)
AS
BEGIN
	SET NOCOUNT ON;
	SELECT
		CONVERT(varchar, a.[create_time], 23) AS transactiondate,
		c.taxinvoiceID AS start_inv,
		c.taxinvoiceID AS end_inv,
		d.FullTaxinvoiceID AS start_tiv,
		b.original_total_product_price AS original_price,
		b.shipping_fee AS shipping_fee,
		b.platform_discount AS voucher_platform,
		b.seller_discount AS voucher_seller,
		b.platform_discount + b.seller_discount AS voucher,
		b.original_total_product_price AS price,
		a.order_id AS order_sn,
		(b.original_total_product_price - b.seller_discount + b.shipping_fee) AS priceVATincluded,
		CAST(ROUND((b.original_total_product_price - b.seller_discount + b.shipping_fee)/1.07, 2) AS numeric(36,2)) AS priceBeforeVAT,
		CAST(ROUND(
			(b.original_total_product_price - b.seller_discount + b.shipping_fee)
			- CAST(ROUND((b.original_total_product_price - b.seller_discount + b.shipping_fee)/1.07, 2) AS numeric(36,2))
		, 2) AS numeric(36,2)) AS VAT
	FROM ((tiktok_orders a
		INNER JOIN tiktok_order_payment b ON (a.order_id = b.order_id))
		INNER JOIN tiktok_taxinvoiceid c ON (a.order_id = c.order_id))
		LEFT OUTER JOIN tiktok_fulltaxinvoice d ON (a.order_id = d.order_id)
	WHERE
		RTRIM(c.taxinvoiceID) >= @OrderStart AND RTRIM(c.taxinvoiceID) <= @OrderEnd
		AND a.[status] IN ('Packet')
	ORDER BY CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', a.create_time), RIGHT('0000000'+CAST(ISNULL(a.order_id,0) AS VARCHAR),7)) AS bigint) ASC
END
";
			$ok1 = $this->db->query($sql);
			// DateStart sibling — keep tiktok_orders_views + status column from live shape
			$sql2 = "
ALTER PROCEDURE [dbo].[tiktok_select_order_with_DateStart_DateEnd]
	@DateStart Datetime,
	@DateEnd Datetime
AS
BEGIN
	SET NOCOUNT ON;
	SELECT
		CONVERT(varchar, a.[create_time], 23) AS transactiondate,
		a.[status] AS status,
		c.taxinvoiceID AS start_inv,
		c.taxinvoiceID AS end_inv,
		d.FullTaxinvoiceID AS start_tiv,
		b.original_total_product_price AS original_price,
		b.shipping_fee AS shipping_fee,
		b.platform_discount AS voucher_platform,
		b.seller_discount AS voucher_seller,
		b.platform_discount + b.seller_discount AS voucher,
		b.original_total_product_price AS price,
		a.order_id AS order_id,
		(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0)) AS priceVATincluded,
		CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2)) AS priceBeforeVAT,
		CAST(ROUND(
			(b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))
			- CAST(ROUND((b.original_total_product_price - ISNULL(b.seller_discount,0) + ISNULL(b.shipping_fee,0))/1.07, 2) AS numeric(36,2))
		, 2) AS numeric(36,2)) AS VAT
	FROM ((tiktok_orders_views a
		INNER JOIN tiktok_order_payment b ON (a.order_id = b.order_id))
		INNER JOIN tiktok_taxinvoiceid c ON (a.order_id = c.order_id))
		LEFT OUTER JOIN tiktok_fulltaxinvoice d ON (a.order_id = d.order_id)
	WHERE
		CONVERT(date, CONVERT(varchar, a.[create_time], 23)) >= @DateStart
		AND CONVERT(date, CONVERT(varchar, a.[create_time], 23)) <= @DateEnd
		AND a.[status] IN ('Packet')
	ORDER BY CAST(CONCAT(DATEDIFF(s, '1970-01-01 00:00:00', a.create_time), RIGHT('0000000'+CAST(ISNULL(a.order_id,0) AS VARCHAR),7)) AS bigint) ASC
END
";
			$ok2 = $this->db->query($sql2);
			$applied = array(
				'order_end_sp' => ($ok1 ? 'ok' : 'fail'),
				'date_sp' => ($ok2 ? 'ok' : 'fail'),
				'db_error' => $this->db->error(),
			);
			$sp = $this->tiktok_orders_model->tiktok_select_order_with_OrderStart_OrderEnd($inv, $inv);
			$sp_row = (!empty($sp) && is_array($sp)) ? $sp[0] : null;
		}
		echo json_encode(array(
			'invoice' => $inv,
			'order_id' => $oid,
			'payment' => $pay,
			'line_sum_original_price_x_qty' => round($sum_orig, 2),
			'lines' => $lines,
			'sp_row' => $sp_row,
			'sp_definition' => $sp_def,
			'sp_definition_date' => $sp_def_date,
			'applied' => $applied,
			'note' => array(
				'display_price_was' => 'tiktok_order_payment.total_amount',
				'correct_goods' => 'tiktok_order_payment.original_total_product_price',
				'row1_expected_goods' => isset($pay['original_total_product_price']) ? $pay['original_total_product_price'] : null,
				'row1_expected_vat_incl' => (isset($pay['original_total_product_price']) ? floatval($pay['original_total_product_price']) : 0)
					- (isset($pay['seller_discount']) ? floatval($pay['seller_discount']) : 0)
					+ (isset($pay['shipping_fee']) ? floatval($pay['shipping_fee']) : 0),
			),
		), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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

	              $startDate = DateTime::createFromFormat('!ym', $inputDate);
	              if (!$startDate) {
	                  die("รูปแบบวันที่ไม่ถูกต้อง! ใช้ ym");
	              }

	              // ลบเดือนปัจจุบันก่อน ไล่ลงถึงเดือนที่เลือก
	              // เพื่อให้ DataDownload.shopee_orderlist_start_date = เดือนที่เลือก (รอบสุดท้าย)
	              $cursor = new DateTime('first day of this month');
	              $cursor->setTime(0, 0, 0);
	              while ($cursor >= $startDate) {
	              	$ym = $cursor->format('ym');
	              	//echo "Shopee del >> ".$ym."<br>";
	        		$this->shopee_orders_model->delete_shopee_order_by_year_month($ym);
	        		$cursor->modify('-1 month');
	        	}
	        }

		}elseif($platform_del == 2){

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

	        		$this->tiktok_orders_model->delete_tiktok_order_by_year_month($ym);

	        		$startDate->modify('+1 month');
	        	}
	        }

		}

		redirect(base_url().'accounting/saletaxreport/saletaxreport_delete', 'refresh');

	}

	
}