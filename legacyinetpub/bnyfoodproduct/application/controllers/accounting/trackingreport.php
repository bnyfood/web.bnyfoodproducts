<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Trackingreport extends CI_Controller
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
    	$this->load->library("businesslogic/data_bl");

    	$this->load->model('lazada_orders_model');
   		$this->load->model('shopee_orders_model');
   		$this->load->model('lazada_tracking_model');
   		$this->load->model('shopee_tracking_model');
		$this->load->model('web_shop_model');

        //$this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata('customer_code');
     }

	public function tracking_list()
	{
		$arr_input = array(
			'title' => "Accounting"
		);

		$arr_css = array();

		$arr_js = array(
	      'trackingreport_js' => base_url().'resources/js/account/trackingreport.js',
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
              
        $this->view_util->load_view_main('accounting/trackingreport/tracking_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_TRACKINGREPORT);

	}

	public function trackingreport_make()
	{
		//echo ">>>ฬ";
		 $platform=$this->uri->segment(4);

		 //echo $this->uri->segment(5);
	     //$datesearch=$this->date_util->getStartEndDate($this->uri->segment(5),"S");
	     $datesearch = $this->uri->segment(5);


	     //echo "platform>>".$platform.">>StartDdatesearchate>>".$datesearch;

	     //echo "select_orders_with_modify_total_Between_Start_End_Date '".$StartDate."'".$EndDate."'";

	     switch($platform)
	       {
	         case 0: //lazada
	         //echo "lazada";
	     		$orders_tracks =$this->lazada_tracking_model->select_order_groupby_orderno_tracking($datesearch);
	     		//print_r($orders_orderitems);
	     		$arr_chk_correct = array();
	     		if(!empty($orders_tracks)){
		     		$arr_order_no = $this->data_bl->create_arr_id($orders_tracks,'order_number');

		     		$arr_tracking_id = $this->lazada_tracking_model->get_in_order_no_correct($arr_order_no);
		     		if(!empty($arr_tracking_id)){

			     		$arr_chk_correct = $this->data_bl->create_arr_id($arr_tracking_id,'order_number');
			     	}
		     	}

			    $data=array(
		     		'orders_tracks'=>$orders_tracks,
		     		'arr_chk_correct' => $arr_chk_correct,
		     		'datesearch' => $datesearch
		     	);
			    	//print_r($data);
			     $this->load->view('accounting/trackingreport/lazada_print_tracking',$data);

			 break;

		      case 1: //shopee

		      $orders_orderitems=$this->shopee_tracking_model->select_order_groupby_orderno_tracking($datesearch);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$arr_chk_correct = array();

			     	if(!empty($orders_orderitems)){
			     		$arr_order_no = $this->data_bl->create_arr_id($orders_orderitems,'order_sn');

			     		$arr_tracking_id = $this->shopee_tracking_model->get_in_order_no_correct($arr_order_no);
			     		if(!empty($arr_tracking_id)){

				     		$arr_chk_correct = $this->data_bl->create_arr_id($arr_tracking_id,'order_sn');
				     	}
			     	}

			     	$data=array(
			     		'shopee_orders'=>$orders_orderitems,
			     		'arr_chk_correct' => $arr_chk_correct,
			     		'datesearch' => $datesearch,
			     		'validdata'=>999,
			     	);

			     }else{

			       $data=array(
			       	'validdata'=>0,
			       );   

			     }
			    	//print_r($data);
			     $this->load->view('accounting/trackingreport/shopee_print_tracking',$data);


		      break;
	       }
	}

	
}