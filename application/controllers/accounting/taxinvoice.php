<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Taxinvoice extends CI_Controller
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

		$this->load->library("businesslogic/account/lazada_report_sale");
		$this->load->library("businesslogic/account/shopee_report_sale");
		$this->load->library("businesslogic/account/lazada_acc_bl");
		$this->load->library("businesslogic/account/shopee_acc_bl");

		$this->load->library("businesslogic/lazada_bl");

    	$this->load->model('lazada_orders_model');
   		$this->load->model('shopee_orders_model');
   		$this->load->model('tiktok_orders_model');
   		$this->load->model('inwshop_data_model');

   		$this->load->model('lazada_taxinvoice_model');
   		$this->load->model('shopee_taxinvoice_model');
   		$this->load->model('company_model');
   		$this->load->model('address_model');
		$this->load->model('web_shop_model');

        //$this->auth_bl->check_session_exists();

        $this->_customer_code = $this->session->userdata('customer_code');
     }

	public function taxinvoice_list()
	{
		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array();

		$arr_js = array(
	      'taxinvoice_js' => base_url().'resources/js/account/taxinvoice.js',
	    );  

	    $arr_search = array(
 			'taxinvoicetype' => "",
 			'platform' => "",
 			'search_type' => "",
 			'ordernumber' => "",
 			'daterange' => ""
	 	);

	    $data = array(
	    	'arr_search' => $arr_search	
	    );
              
        $this->view_util->load_view_main('accounting/taxinvoice/taxinvoice_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_TAXINVOICE);

	}

	public function taxinvoice_search()
	{
		$taxinvoicetype = $this->input->post('taxinvoicetype');
		$search_type = $this->input->post('search_type');
		$platform = $this->input->post('platform');
		$ordernumber = $this->input->post('order_number');
		$daterange = $this->input->post('daterange');

		$arr_search = array(
 			'taxinvoicetype' => $taxinvoicetype,
 			'platform' => $platform,
 			'search_type' => $search_type,
 			'ordernumber' => $ordernumber,
 			'daterange' => $daterange
	 	);

	 	//print_r($arr_search);

		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array(
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.css',
		);

		$arr_js = array(
			'fancybox' => base_url().'resources/fancybox/jquery.fancybox-1.3.4.js',
			'fancy_main' => base_url().'resources/js/fancybox/fancy_main.js',
	        'invoice_js' => base_url().'resources/js/account/taxinvoice.js'
	    );  

	    switch($platform)
            {
                case 0: //lazada+

                	$arr_orders = $this->lazada_acc_bl->getinvoice($arr_search);
                	$data = array(
				    	'arr_search' => $arr_search,
				    	'arr_orders' => $arr_orders,
				    	'page' => 1		
				    );
                	$this->view_util->load_view_main('accounting/taxinvoice/taxinvoice_list',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_TAXINVOICE);
            	break;

                case 1: //Shopee
                	$arr_orders = $this->shopee_acc_bl->getinvoice($arr_search);
                	$data = array(
				    	'arr_search' => $arr_search,
				    	'arr_orders' => $arr_orders,
				    	'page' => 1		
				    );
                	$this->view_util->load_view_main('accounting/taxinvoice/shopee_taxinvoice',$data,$arr_css,$arr_js,$arr_input,MENU_ACCOUNT_TAXINVOICE);
                break;

                case 2:

                break;    
            }    

	}


	public function shot_invoice()
	{

		 $platform=$this->uri->segment(4);
	     $ordernumber=$this->uri->segment(5);
	    
	     $copy=$this->uri->segment(7);
	     $search_type = $this->uri->segment(6);
	     $StartDate = "";
	     $EndDate = "";
	     if($this->uri->segment(8) != ""){
		     $StartDate=$this->date_util->getStartEndDate($this->uri->segment(8),"S");
		     $EndDate=$this->date_util->getStartEndDate($this->uri->segment(8),"E");
		 }

	     //echo $platform.">".$ordernumber.">".$StartDate.">".$EndDate.">".$copy.">".$search_type."<br>";

	     //echo "select_orders_with_modify_total_Between_Start_End_Date '".$StartDate."'".$EndDate."'";

	     switch($platform)
	       {
	         case 0: //lazada
	         //echo "lazada";
	     		$orders_orderitems=$this->lazada_orders_model->select_order_with_orderitems_by_DateStart_DateEnd_SearchType($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$orders=$this->order_util->getOrdersFromOdersOderItems($orders_orderitems);
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
			     $this->load->view('accounting/taxinvoice/lazada_taxinvoicepages',$data);

			 break;

		     case 1: //shopee-

		     	$orders_orderitems=$this->shopee_orders_model->shopee_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$orders=$this->order_util->ShopeegetOrdersFromOdersOderItems($orders_orderitems);
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
			     $this->load->view('accounting/taxinvoice/shopee_taxinvoicepages',$data);

		     break;

		     case 2: //tiktok

		     	$orders_orderitems=$this->tiktok_orders_model->tiktok_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$orders=$this->order_util->TiktokgetOrdersFromOdersOderItems($orders_orderitems);
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
			     $this->load->view('accounting/taxinvoice/tiktok_taxinvoicepages',$data);

		     break;

		     case 3: //Biggrill

		     	$orders_orderitems=$this->inwshop_data_model->inwshop_select_order_with_SearchType($StartDate,$EndDate,$search_type,$ordernumber);
	     		//print_r($orders_orderitems);
			     if(!empty($orders_orderitems))
			     {

			     	$orders=$this->order_util->TiktokgetOrdersFromOdersOderItems($orders_orderitems);
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
			     $this->load->view('accounting/taxinvoice/inwshop_taxinvoicepages',$data);

		     break;
	       }
	       
	}

	public function textinvoice_addform(){

		$order_number=$this->uri->segment(4);
		$platform=$this->uri->segment(5);

		$arr_input = array(
				'title' => "Accounting"
			);

		$arr_css = array();

		$arr_js = array(

			'validate' => base_url()."assets/js/jquery.validate.min.js",		
			'validate_addform' => base_url()."resources/js/validate/textinvoice_addform.js",
	        'textinvoice_addform' => base_url().'resources/js/account/textinvoice_addform.js',

	    );  

		if($platform == 0){
		    $arr_taxinvoice = $this->lazada_taxinvoice_model->select_by_order_id(intval($order_number));
		}elseif($platform == 1){
			$arr_taxinvoice = $this->shopee_taxinvoice_model->select_by_order_id($order_number);
		}
		//print_r($arr_taxinvoice);

	    $is_action = 1;
	    if(!empty($arr_taxinvoice)){
	    	$is_action = 2;
	    }

	    $data = array(
	    	'arr_taxinvoice' => $arr_taxinvoice,
	    	'order_number' => $order_number,
	    	'is_action' => $is_action,
	    	'platform' => $platform
	    ); 
              
        $this->view_util->load_view_blankpage('accounting/taxinvoice/textinvoice_addform',$data,$arr_css,$arr_js,$arr_input,MENU_MANUFACTURE_BRAND);
	}

	public function textinvoice_action(){

		$name = $this->input->post('name');
		$address1 = $this->input->post('address1');
		$address2 = $this->input->post('address2');
		$phone = $this->input->post('phone');
		$Taxno = $this->input->post('Taxno');
		$note = $this->input->post('note');
		$zip = $this->input->post('zip');
		$order_number = $this->input->post('order_number');
		$is_action = $this->input->post('is_action');
		$platform = $this->input->post('platform');

		$invoice_type = $this->input->post('invoice_type');
		$is_head_office = $this->input->post('is_head_office');
		$branch_number = $this->input->post('branch_number');
		//$FullTaxinvoiceID = 'BNY20210400002';

		if($invoice_type == 1){
			$is_head_office = 0;
			$branch_number = "";
		}

		if($is_head_office == 1){
			$branch_number = "";
		}

		if($is_action == 1){
			// Add
				if($platform == 0){ // Lazada
				//$arr_bnycode = $this->lazada_taxinvoice_model->select_bnycode($order_number);
				$arr_order = $this->lazada_orders_model->get_by_sn_status_one($order_number,'packed');
				//print_r($arr_order);
				if(!empty($arr_order)){

					$arr_lastorder = $this->lazada_taxinvoice_model->last_order_code($arr_order['updated_at'],$arr_order["OrderID"]);   
					//print_r($arr_lastorder);
					$last_order_date = $arr_order['updated_at'];
					$last_fulltax = 'no';
					if(!empty($arr_lastorder)){
						$last_fulltax = $arr_lastorder['FullTaxinvoiceID'];
					}
					$new_textinvoiceID = $this->lazada_bl->get_laz_fulltax_code($last_fulltax,$last_order_date);    
					$data = array(
						'lazada_orders_OrderID' => intval($arr_order['OrderID']),
						'lazada_orders_number' => intval($order_number),
						'FullTaxinvoiceID' => $new_textinvoiceID,
						'name' => $name,
						'address1' => $address1,
						'address2' => $address2,
						'phone' => $phone,
						'Taxno' => $Taxno,
						'Note' => $note,
						'zip' => $zip,
						'invoice_type' => $invoice_type,
						'lazada_order_date' => $arr_order['updated_at'],
						'is_head_office' => $is_head_office,
						'branch_number' => $branch_number
					);

					//print_r($data);

					$this->lazada_taxinvoice_model->insert($data);
				}
			}elseif($platform == 1){ // Shopee
				$arr_order = $this->shopee_orders_model->get_by_sn_status_one($order_number,'SHIPPED');
				$arr_lastorder = $this->shopee_taxinvoice_model->last_order_code($arr_order['update_time'],$arr_order["OrderID"]);  
				if(!empty($arr_order)){
					$last_order_date = $arr_order['update_time'];
					$last_fulltax = null;
					if(!empty($arr_lastorder)){
						$last_fulltax = $arr_lastorder['FullTaxinvoiceID'];
					}
					$new_textinvoiceID = $this->shopee_bl->get_shp_fulltax_code($last_fulltax,$last_order_date);  

					$data = array(
						'shopee_orders_OrderID' => intval($arr_order['OrderID']),
						'shopee_orders_sn' => $order_number,
						'FullTaxinvoiceID' => $new_textinvoiceID,
						'name' => $name,
						'address1' => $address1,
						'address2' => $address2,
						'phone' => $phone,
						'Taxno' => $Taxno,
						'Note' => $note,
						'zip' => $zip,
						'invoice_type' => $invoice_type,
						'shopee_order_date' => $arr_order['update_time'],
						'is_head_office' => $is_head_office,
						'branch_number' => $branch_number
					);

					//print_r($data);

					$this->shopee_taxinvoice_model->insert($data);
				}
			}
		}else{
			//Update
			$data = array(
				'name' => $name,
				'address1' => $address1,
				'address2' => $address2,
				'phone' => $phone,
				'Taxno' => $Taxno,
				'Note' => $note,
				'zip' => $zip,
				'invoice_type' => $invoice_type,
				'is_head_office' => $is_head_office,
				'branch_number' => $branch_number
			);

			if($platform == 0){ // Lazada
				$this->lazada_taxinvoice_model->update_by_order_no($data,$order_number);
			}elseif($platform == 1){ // Shopee
				$this->shopee_taxinvoice_model->update_by_order_id($data,$order_number);
			}	

		}

		$this->session->set_flashdata('alertbox', 1);

		redirect(base_url().'accounting/taxinvoice/textinvoice_addform/'.$order_number.'/'.$platform,'refresh');

	}

	public function taxinvoice_print(){
		$order_number=$this->uri->segment(4);

		$arr_order = $this->lazada_orders_model->get_by_sn_status_one($order_number,'packed');
		$arr_taxinvoice = $this->lazada_taxinvoice_model->select_by_order_id(intval($order_number));

		//print_r($arr_taxinvoice);

		$arr_company = $this->company_model->select_by_id(1);

		$arr_address = $this->address_model->select_by_company_id_main(1);


		
		//echo $sum_amount;
		//print_r($arr_taxinvoice);

		//---Calculate TAX----
		$shipping_fee = 0;
        $discount = 0;
		$sum_amount = 0;
		$sum_amount_vat = 0;
		$sum_before_vat = 0;
		$sum_before_vat_txt = 0;

		$cnt_items = 0;
		$num_page = 0;
		$last_page = 0;
		$last_item = 0;

		$arr_items = $this->lazada_orders_model->select_order_with_orderitems_by_Ordernumber($order_number);
		//print_r($arr_items);
		if(!empty($arr_items)){
		$cnt_items = count($arr_items);
		$num_page = ceil($cnt_items/10);
		$last_item = $cnt_items%10;
		$last_page = $num_page;
			foreach($arr_items as $arr_item){
				$sum_amount = $sum_amount + $arr_item['item_price'];
				$shipping_fee += $arr_item["shipping_fee"];
                $discount += $arr_item["discount"];
			}
			$price=$sum_amount+$shipping_fee-$discount;
            $pricebeforeVAT=$price/1.07;
            $sum_before_vat_txt = $this->number_bl->num_to_text($pricebeforeVAT);
		}

		//print_r($arr_order_items);

		$data = array(
			'arr_order' => $arr_order,
			'arr_taxinvoice' => $arr_taxinvoice,
			'arr_company' => $arr_company,
			'arr_address' => $arr_address,
			//'arr_order_items' => $arr_order_items,
			'cnt_items' => $cnt_items,
			'num_page' => $num_page,
			'last_page' => $last_page,
			'last_item' => $last_item,
			'sum_amount' => $sum_amount,
			'sum_amount_vat' => $sum_amount_vat,
			'sum_before_vat' => $sum_before_vat,
			'sum_before_vat_txt' => $sum_before_vat_txt,
			'arr_items' => $arr_items
		);

		$this->load->view('accounting/taxinvoice/taxinvoice_print',$data);
	}

	public function shopee_taxinvoice_print(){
		$order_number=$this->uri->segment(4);

		$arr_order = $this->shopee_orders_model->get_by_sn_status_one($order_number,'SHIPPED');
		$arr_taxinvoice = $this->shopee_taxinvoice_model->select_by_order_id($order_number);

		$arr_company = $this->company_model->select_by_id(1);

		$arr_address = $this->address_model->select_by_company_id_main(1);


		
		//echo $sum_amount;
		//print_r($arr_taxinvoice);

		//$arr_order_items = $this->shopee_orderitems_model->get_by_sn($order_number);
		//print_r($arr_order_items);
		

		//---Calculate TAX----

		$priceacc = 0;
		$shipping_fee = 0;
        $discount = 0;


		$sum_amount = 0;
		$sum_amount_vat = 0;
		$sum_before_vat = 0;
		$sum_before_vat_txt = 0;

		$cnt_items = 0;
		$num_page = 0;
		$last_page = 0;
		$last_item = 0;

		$arr_items = $this->shopee_orders_model->shopee_select_order_with_ordersn($order_number);

		if(!empty($arr_items)){
		$cnt_items = count($arr_items);
		$num_page = ceil($cnt_items/10);
		$last_item = $cnt_items%10;
		$last_page = $num_page;
			foreach($arr_items as $arr_item){
				$priceacc=$priceacc+$arr_item["amount"];
				$shipping_fee += $arr_item["shipping_fee"];
                $discount += $arr_item["discount"];
			}
			$price=$priceacc+$shipping_fee-$discount;
            $pricebeforeVAT=$price/1.07;
            $sum_before_vat_txt = $this->number_bl->num_to_text($pricebeforeVAT);
		}

		//print_r($arr_items);

		$data = array(
			'arr_order' => $arr_order,
			'arr_taxinvoice' => $arr_taxinvoice,
			'arr_company' => $arr_company,
			'arr_address' => $arr_address,
			//'arr_order_items' => $arr_order_items,
			'cnt_items' => $cnt_items,
			'num_page' => $num_page,
			'last_page' => $last_page,
			'last_item' => $last_item,
			'sum_amount' => $sum_amount,
			'sum_amount_vat' => $sum_amount_vat,
			'sum_before_vat' => $sum_before_vat,
			'sum_before_vat_txt' => $sum_before_vat_txt,
			'arr_items' => $arr_items
		);

		$this->load->view('accounting/taxinvoice/shopee_taxinvoice_print',$data);
	}

	
}