<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Creditnote extends CI_Controller
{

	protected $_customer_code;

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');
		$this->load->library('util/order_util');
		$this->load->library('util/date_util');

		$this->load->model('web_shop_model');
		$this->load->model('lazada_orders_model');
		$this->load->model('shopee_orders_model');

        $this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata('customer_code');
     }

	public function creditnote_list()
	{
		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array();

		$arr_js = array(
	      'creditnote_js' => base_url().'resources/js/account/creditnote.js',
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'search_type' => "",
 			'platform' => "",
 			'ordernumber' => "",
 			'daterange' => ""
	 	);

	    $data = array(
	    	'arr_search' => $arr_search	
	    );
              
        $this->view_util->load_view_main('accounting/creditnote/creditnote_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_CREDITNOTE);

	}

	public function creditnote_make()
	{
		//echo ">>>";
		 $platform=$this->uri->segment(4);
	     $ordernumber=$this->uri->segment(5);
	     $StartDate=$this->date_util->getStartEndDate($this->uri->segment(6),"S");
	     $EndDate=$this->date_util->getStartEndDate($this->uri->segment(6),"E");
	     $copy=$this->uri->segment(7);
	     $search_type = $this->uri->segment(8);

	     //echo "platform>>".$platform."ordernumber>>".$ordernumber."StartDate>>".$StartDate."EndDate>>".$EndDate."search_type>>".$search_type;

	     //echo "select_orders_with_modify_total_Between_Start_End_Date '".$StartDate."'".$EndDate."'";

	     switch($platform)
	       {
	         case 0: //lazada
	         //echo "lazada";
	     		$orders_orderitems=$this->lazada_orders_model->select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$arr_orders=$this->order_util->getOrdersFromOdersOderItemsCN($orders_orderitems);

			     	$orders = $this->order_util->make_cn_no($arr_orders);

			     	$data=array(
			     		'orders'=>$orders,
			     		'copy'=>$copy
			     	);

			     }else{

			       $data=array(
			       	'orders'=>0,
			       	'copy'=>$copy
			       );   

			     }
			    	//print_r($data);
			     $this->load->view('accounting/creditnote/lazada_taxinvoicepagescn',$data);

			 break;

		      case 1: //shopee

		      $orders_orderitems=$this->shopee_orders_model->shopee_select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {



			     	$arr_orders=$this->order_util->getOrdersFromOdersOderItemsCNShopee($orders_orderitems);

			     	$orders = $this->order_util->make_cn_no_shopee($arr_orders);

			     	$data=array(
			     		'orders'=>$orders,
			     		'copy'=>$copy
			     	);

			     }else{

			       $data=array(
			       	'orders'=>0,
			       	'copy'=>$copy
			       );   

			     }
			    	//print_r($data);
			     $this->load->view('accounting/creditnote/shopee_taxinvoicepagescn',$data);

		      break;
	       }
	}
}