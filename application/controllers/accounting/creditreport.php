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

		$arr_css = array();

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

	function creditreport_make(){

		$platform=$this->uri->segment(4);
		//echo "--->>".$this->uri->segment(4)."<<---";
	     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
	     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");
	     //prepdata
	    
	     //Lazada
	     //echo $platform."--".$StartDate."--".$EndDate;
	      
	  if($platform == "0"){
	      $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_lazada_make = $this->lazada_report_cn->make_cn($arr_lazada);
	      //print_r($arr_lazada_make);

	      $arr_laz_make_group = $this->lazada_report_cn->make_group_cn($arr_lazada_make);
	      //print_r($arr_laz_make_group);
	      $validdata = 0;
	      if(!empty($arr_lazada)){
	        $validdata = 1;
	      }
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'lazada_orders'=>$arr_laz_make_group

	      );
	                      
	    //$this->load->view('accounting/creditreport/lazada_creditreportpages',$data);
	    $this->load->view('accounting/creditreport/lazada_creditreportpages_group',$data);
	  }elseif($platform == "1"){ // Shopee

	    $arr_shopee=$this->shopee_orders_model->shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
	      //print_r($arr_lazada);
	      //echo "cnt>>".count($arr_lazada)."<br>";
	      $arr_shopee_make = $this->shopee_report_cn->make_cn($arr_shopee);
	      //print_r($arr_shopee_make);
	      $arr_shopee_make_group = $this->shopee_report_cn->make_group_cn($arr_shopee_make);

	      //print_r($arr_shopee_make_group);

	      $validdata = 0;
	      if(!empty($arr_shopee)){
	        $validdata = 1;
	      }
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'shopee_orders'=>$arr_shopee_make_group

	      );
	                      
	    $this->load->view('accounting/creditreport/shopee_creditreportpages_group',$data);

	  }elseif($platform == "2"){ // Tiktok

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
	      $data=array(
	        'validdata'=>$validdata,
	        'start_date'=>$StartDate,
	        'end_date'=>$EndDate,
	        'tiktok_orders'=>$arr_tiktok_make


	      );
	                      
	    $this->load->view('accounting/creditreport/tiktok_creditreportpages_group',$data);

	  }

	}

	function laz_make_cn_by_date(){
		$StartDate = $this->uri->segment(4);

		$arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn_date($StartDate);
	      //print_r($arr_lazada);
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
	        'end_date'=>$StartDate,
	        'lazada_orders'=>$arr_lazada_make

	      );

	      //print_r($data);
	                      
	    $this->load->view('accounting/creditreport/lazada_creditreportpages',$data);

	}

	function sho_make_cn_by_date(){
		$StartDate = $this->uri->segment(4);

		$arr_shopee=$this->shopee_orders_model->shopee_select_order_groupby_Date_by_DateStart_CN($StartDate);
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
      'end_date'=>$StartDate,
      'shopee_orders'=>$arr_shopee_make


    );
                    
  $this->load->view('accounting/creditreport/shopee_creditreportpages',$data);

	}

	
}