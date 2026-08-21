<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Main extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]

		$this->load->library('util/View_util');
		$this->load->library("util/array_util");
	    $this->load->library("util/common_util");
	    $this->load->library("businesslogic/lazapi");
		
		$this->load->model('lazada_orders_model');
	    $this->load->model('laztoken_model');
	    $this->load->model('lazada_orderitems_model');
	    $this->load->model('lazada_customers_model');
	    $this->load->model('lazada_shipping_address_model');
	    $this->load->model('lazada_billing_address_model');
	    

		//$this->load->library('business_logic/auth_bl');

       // $this->auth_bl->check_session_exists();

     }

     public function taxinvoice()
	{
		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array();

		$arr_js = array(
	      'invoice_js' => base_url().'resources/js/account/textinvoice.js',
	    );  
              
        $this->view_util->load_view_main('admin/accounting/taxinvoice',$data,$arr_css,$arr_js,$arr_input);

	}
 }