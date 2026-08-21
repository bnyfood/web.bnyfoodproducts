<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Creditreport extends CI_Controller
{

	protected $_customer_code;

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/order_util');
		$this->load->library('util/report_cutover');

		$this->load->library("businesslogic/account/lazada_report_cn");
    	$this->load->library("businesslogic/account/shopee_report_cn");
    	$this->load->library("businesslogic/account/tiktok_report_cn");

    	$this->load->model('lazada_orders_model');
   		$this->load->model('shopee_orders_model');
   	$this->load->model('tiktok_orders_model');
		$this->load->model('web_shop_model');

        $this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata('customer_code');
     }

	public function creditreport_list()
	{
		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array(
			'daterangepicker' => base_url().'resources/css/daterangepicker/daterangepicker.css',
		);

		$arr_js = array(
	      'creditreport_js' => base_url().'resources/js/account/creditreport.js',
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'platform' => "",
 			'ordernumber' => "",
 			'daterange' => ""
	 	);

	    $data = array(
	    	'arr_search' => $arr_search	
	    );
              
        $this->view_util->load_view_main('accounting/creditreport/creditreport_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_CREDITREPORT);

	}

	function creditreport_search(){

		if($platform == "0"){
          $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
         // print_r($arr_lazada);
          //echo "cnt>>".count($arr_lazada)."<br>";
          $arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
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
                          
        $this->load->view('admin/accounting/creditreportpages',$data);
      }elseif($platform == "1"){ //Shopee

        $arr_shopee=$this->shopee_orders_model->shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
          //print_r($arr_lazada);
          //echo "cnt>>".count($arr_lazada)."<br>";
          $arr_shopee_make = $this->shopee_report_cn->make_cn($arr_shopee);
          //print_r($arr_shopee_make);
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
                          
        $this->load->view('admin/accounting/shopee_creditreportpages',$data);

      }elseif($platform == "2"){ // Tiktok

        $arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
          //print_r($arr_lazada);
          //echo "cnt>>".count($arr_lazada)."<br>";
          $arr_tiktok_make = $this->tiktok_report_cn->make_cn($arr_tiktok);
          //print_r($arr_tiktok_make);
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
                          
        $this->load->view('admin/accounting/tiktok_creditreportpages',$data);

      }


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

	function creditreport_make(){

		$platform=$this->uri->segment(4);
		//echo "--->>".$this->uri->segment(4)."<<----";
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");
	     $this->report_cutover->set_range($StartDate, $EndDate);
	     $legacy = $this->report_cutover->use_legacy();
	     //prepdata
	    
	     //Lazada
	     //echo $platform."--".$StartDate."--".$EndDate;
	      
	  if($platform == "0"){
	      $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
	      $arr_lazada_daily = array();
	      if(!empty($arr_lazada_make)){
	        $arr_lazada_daily = $this->lazada_report_cn->make_group_cn($arr_lazada_make);
	      }
	      $validdata = 0;
	      if(!empty($arr_lazada)){
	        $validdata = 1;
	      }
	      $data=$this->attach_ai_debug(array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'report_legacy'=>$legacy,
	        'lazada_orders'=>$arr_lazada_make,
	        'lazada_orders_daily'=>$arr_lazada_daily
	      ), array(
	        'platform' => $platform,
	        'StartDate' => $StartDate,
	        'EndDate' => $EndDate,
	        'source' => 'cn.laz',
	        'cutover' => $this->report_cutover->range_info()
	      ));
	    $this->load->view('accounting/creditreport/lazada_creditreportpages',$data);
	  }elseif($platform == "1"){ // Shopee — same source as ใบลดหนี้รายตัว, grouped by date

	    $loaded = $this->load_shopee_cn_report_orders($StartDate, $EndDate);
	    $arr_orders = $loaded['orders'];
	    $arr_group = $loaded['daily'];
	    $validdata = 0;
	    if(!empty($arr_orders)){
	      $validdata = 1;
	    }
	    $data=$this->attach_ai_debug(array(
	      'validdata'=>$validdata,
	      'start_date'=>$StartDate,
	      'end_date'=>$EndDate,
	      'report_legacy'=>$legacy,
	      'shopee_orders'=>$arr_orders,
	      'shopee_orders_daily'=>$arr_group
	    ), array(
	      'platform' => $platform,
	      'StartDate' => $StartDate,
	      'EndDate' => $EndDate,
	      'rows' => is_array($arr_orders) ? count($arr_orders) : 0,
	      'source' => $legacy ? 'cn.sho.legacy' : 'cn.sho.evt',
	      'cutover' => $this->report_cutover->range_info()
	    ));

	    $this->load->view('accounting/creditreport/shopee_creditreportpages',$data);

	  }elseif($platform == "2"){ // Tiktok-

	    $arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_tiktok_make = $this->tiktok_report_cn->make_cn($arr_tiktok);

	      $arr_tiktok_make_group = $this->tiktok_report_cn->make_group_cn($arr_tiktok_make);
	      //print_r($arr_shopee_make);
	      $validdata = 0;
	      if(!empty($arr_tiktok)){
	        $validdata = 1;
	      }
	      $data=$this->attach_ai_debug(array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'report_legacy'=>$legacy,
	        'tiktok_orders'=>$arr_tiktok_make,
	        'tiktok_orders_daily'=>$arr_tiktok_make_group
	      ), array(
	        'platform' => $platform,
	        'StartDate' => $StartDate,
	        'EndDate' => $EndDate,
	        'source' => 'cn.tik',
	        'cutover' => $this->report_cutover->range_info()
	      ));
	                      
	    $this->load->view('accounting/creditreport/tiktok_creditreportpages',$data);

	  }

	}

	function laz_make_cn_daily(){
		$StartDate = substr((string) $this->uri->segment(4), 0, 10);
		$EndDate = substr((string) $this->uri->segment(5), 0, 10);
		$arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
		$arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
		$arr_laz_make_group = $this->lazada_report_cn->make_group_cn($arr_lazada_make);
		$validdata = 0;
		if(!empty($arr_laz_make_group)){
			$validdata = 1;
		}
		$data=array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'lazada_orders'=>$arr_laz_make_group
		);
		$this->load->view('accounting/creditreport/lazada_creditreportpages_group',$data);
	}

	function sho_make_cn_daily(){
		$StartDate = substr((string) $this->uri->segment(4), 0, 10);
		$EndDate = substr((string) $this->uri->segment(5), 0, 10);
		$this->report_cutover->set_range($StartDate, $EndDate);
		$loaded = $this->load_shopee_cn_report_orders($StartDate, $EndDate);
		$arr_group = $loaded['daily'];
		$validdata = 0;
		if(!empty($arr_group)){
			$validdata = 1;
		}
		$data=$this->attach_ai_debug(array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'report_legacy'=>$this->report_cutover->use_legacy(),
			'shopee_orders'=>$arr_group
		), array(
			'StartDate' => $StartDate,
			'EndDate' => $EndDate,
			'source' => 'cn.sho.day',
			'cutover' => $this->report_cutover->range_info()
		));
		$this->load->view('accounting/creditreport/shopee_creditreportpages_group',$data);
	}

	function tik_make_cn_daily(){
		$StartDate = substr((string) $this->uri->segment(4), 0, 10);
		$EndDate = substr((string) $this->uri->segment(5), 0, 10);
		$this->report_cutover->set_range($StartDate, $EndDate);
		$arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
		$arr_tiktok_make = $this->tiktok_report_cn->make_cn($arr_tiktok);
		$arr_tiktok_make_group = $this->tiktok_report_cn->make_group_cn($arr_tiktok_make);
		$validdata = 0;
		if(!empty($arr_tiktok_make_group)){
			$validdata = 1;
		}
		$data=$this->attach_ai_debug(array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'report_legacy'=>$this->report_cutover->use_legacy(),
			'tiktok_orders'=>$arr_tiktok_make_group
		), array(
			'StartDate' => $StartDate,
			'EndDate' => $EndDate,
			'source' => 'cn.tik.day',
			'cutover' => $this->report_cutover->range_info()
		));
		$this->load->view('accounting/creditreport/tiktok_creditreportpages_group',$data);
	}

	function laz_make_cn_month(){
		$StartDate = $this->uri->segment(4);
		$EndDate = $this->uri->segment(5);
		$arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
		$arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
		$validdata = 0;
		if(!empty($arr_lazada_make)){
			$validdata = 1;
		}
		$data=array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'lazada_orders'=>$arr_lazada_make
		);
		$this->load->view('accounting/creditreport/lazada_creditreportpages',$data);
	}

	function sho_make_cn_month(){
		$StartDate = $this->uri->segment(4);
		$EndDate = $this->uri->segment(5);
		$loaded = $this->load_shopee_cn_report_orders($StartDate, $EndDate);
		$arr_orders = $loaded['orders'];
		$validdata = 0;
		if(!empty($arr_orders)){
			$validdata = 1;
		}
		$data=$this->attach_ai_debug(array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'shopee_orders'=>$arr_orders
		), array(
			'StartDate' => $StartDate,
			'EndDate' => $EndDate,
			'rows' => is_array($arr_orders) ? count($arr_orders) : 0,
			'source' => 'cn.sho.mo'
		));
		$this->load->view('accounting/creditreport/shopee_creditreportpages',$data);
	}

	function tik_make_cn_month(){
		$StartDate = $this->uri->segment(4);
		$EndDate = $this->uri->segment(5);
		$arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
		$arr_tiktok_make = $this->tiktok_report_cn->make_cn($arr_tiktok);
		$validdata = 0;
		if(!empty($arr_tiktok_make)){
			$validdata = 1;
		}
		$data=array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$EndDate,
			'tiktok_orders'=>$arr_tiktok_make
		);
		$this->load->view('accounting/creditreport/tiktok_creditreportpages',$data);
	}

	function laz_make_cn_by_date(){
		$StartDate = $this->uri->segment(4);
		$month = $this->month_start_end($StartDate);

		$arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($month['start'], $month['end']);
	      $arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
	      $arr_lazada_make = $this->filter_cn_rows_by_date($arr_lazada_make, $StartDate);

	      $validdata = 0;
	      if(!empty($arr_lazada_make)){
	        $validdata = 1;
	      }
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$StartDate,
	        'lazada_orders'=>$arr_lazada_make

	      );

	    $this->load->view('accounting/creditreport/lazada_creditreportpages',$data);

	}

	function load_shopee_cn_report_orders($StartDate, $EndDate)
	{
		$this->report_cutover->set_range($StartDate, $EndDate);
		if ($this->report_cutover->use_legacy()) {
			// Legacy: daily CN from SP groupby (shipped/invoice_date path), ValueBeforeVAT money.
			$arr_shopee = $this->shopee_orders_model->shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate, $EndDate);
			$arr_orders = array();
			$arr_group = array();
			if (!empty($arr_shopee)) {
				$arr_orders = $this->shopee_report_cn->make_cn($arr_shopee);
				$arr_group = $this->shopee_report_cn->make_group_cn_legacy($arr_orders);
			}
			return array(
				'orders' => $arr_orders,
				'daily' => $arr_group
			);
		}

		$orders_orderitems = $this->shopee_orders_model->shopee_select_order_with_orderitems_by_cn_event_date($StartDate, $EndDate);
		$arr_orders = array();
		$arr_group = array();
		if (!empty($orders_orderitems)) {
			$arr_orders = $this->order_util->getOrdersFromOdersOderItemsCNShopee($orders_orderitems);
			$arr_orders = $this->map_shopee_cn_orders_for_report($arr_orders);
			usort($arr_orders, array($this, 'cmp_shopee_cn_report_row'));
			$arr_orders = $this->order_util->make_cn_no_shopee($arr_orders);
			$arr_group = $this->shopee_report_cn->make_group_cn($arr_orders);
		}
		return array(
			'orders' => $arr_orders,
			'daily' => $arr_group
		);
	}

	function cmp_shopee_cn_report_row($a, $b)
	{
		$da = isset($a['updated_at']) ? (string)$a['updated_at'] : '';
		$dbt = isset($b['updated_at']) ? (string)$b['updated_at'] : '';
		if ($da === $dbt) {
			$oa = isset($a['order_number']) ? (string)$a['order_number'] : '';
			$ob = isset($b['order_number']) ? (string)$b['order_number'] : '';
			return strcmp($oa, $ob);
		}
		return strcmp($da, $dbt);
	}

	function map_shopee_cn_orders_for_report($orders)
	{
		if (empty($orders)) {
			return $orders;
		}
		foreach ($orders as $i => $o) {
			$orders[$i]['start_inv'] = isset($o['taxinvoiceID']) ? $o['taxinvoiceID'] : '';
			$orders[$i]['start_tiv'] = isset($o['FullTaxinvoiceID']) ? $o['FullTaxinvoiceID'] : '';
			$orders[$i]['cus_name'] = isset($o['customer_name']) ? $o['customer_name'] : '';
			$event = '';
			if (!empty($o['cn_event_at'])) {
				$event = $o['cn_event_at'];
			} elseif (!empty($o['updated_at'])) {
				$event = $o['updated_at'];
			} else {
				$event = isset($o['created_at']) ? $o['created_at'] : '';
			}
			$orders[$i]['updated_at'] = $this->sql_date_ymd($event);
		}
		return $orders;
	}

	function sql_date_ymd($value)
	{
		if ($value instanceof DateTime) {
			return $value->format('Y-m-d');
		}
		$text = trim((string)$value);
		if ($text === '') {
			return $text;
		}
		$ts = strtotime($text);
		if ($ts) {
			return date('Y-m-d', $ts);
		}
		return substr($text, 0, 10);
	}

	function month_start_end($date)
	{
		$ymd = $this->sql_date_ymd($date);
		$ts = strtotime($ymd);
		if (!$ts) {
			$ts = time();
		}
		return array(
			'start' => date('Y-m-01', $ts),
			'end' => date('Y-m-t', $ts)
		);
	}

	function filter_cn_rows_by_date($rows, $date)
	{
		$want = $this->sql_date_ymd($date);
		if (empty($rows) || $want === '') {
			return array();
		}
		$out = array();
		foreach ($rows as $row) {
			$row_date = $this->sql_date_ymd(isset($row['updated_at']) ? $row['updated_at'] : '');
			if ($row_date === $want) {
				$out[] = $row;
			}
		}
		return $out;
	}

	function sho_make_cn_by_date(){
		$StartDate = $this->uri->segment(4);
		$month = $this->month_start_end($StartDate);

		$loaded = $this->load_shopee_cn_report_orders($month['start'], $month['end']);
		$arr_orders = $this->filter_cn_rows_by_date($loaded['orders'], $StartDate);

		$validdata = 0;
		if(!empty($arr_orders)){
			$validdata = 1;
		}
		$data=$this->attach_ai_debug(array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$StartDate,
			'shopee_orders'=>$arr_orders
		), array(
			'StartDate' => $StartDate,
			'month_start' => $month['start'],
			'month_end' => $month['end'],
			'rows' => is_array($arr_orders) ? count($arr_orders) : 0,
			'source' => 'cn.sho.1d'
		));

		$this->load->view('accounting/creditreport/shopee_creditreportpages',$data);

	}

	function tik_make_cn_by_date(){
		$StartDate = $this->uri->segment(4);
		$month = $this->month_start_end($StartDate);

		$arr_tiktok=$this->tiktok_orders_model->tiktok_select_order_groupby_Date_by_DateStart_DateEnd_CN($month['start'],$month['end']);
		$arr_tiktok_make = $this->tiktok_report_cn->make_cn($arr_tiktok);
		$arr_tiktok_make = $this->filter_cn_rows_by_date($arr_tiktok_make, $StartDate);

		$validdata = 0;
		if(!empty($arr_tiktok_make)){
			$validdata = 1;
		}
		$data=array(
			'validdata'=>$validdata,
			'start_date'=>$StartDate,
			'end_date'=>$StartDate,
			'tiktok_orders'=>$arr_tiktok_make
		);

		$this->load->view('accounting/creditreport/tiktok_creditreportpages',$data);
	}

	
}