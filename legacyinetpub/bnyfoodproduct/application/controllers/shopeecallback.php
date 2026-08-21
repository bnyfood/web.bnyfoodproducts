<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 class shopeecallback extends CI_Controller {
    protected  $accesstoken;
    protected $global_arr;

	function __construct() 
	{
		//:[Auto call parent construct]
        parent::__construct();

		$this->load->library('session');
		$this->load->model('lazada_orders_model');
        $this->load->model('laztoken_model');
		$this->load->model('lazada_orderitems_model');
		$this->load->model('lazada_customers_model');
		$this->load->model('lazada_shipping_address_model');
		$this->load->model('lazada_billing_address_model');
        $this->load->model('bnylog_model');

		$this->load->library("util/array_util");
        $this->load->library("util/common_util");
        $this->load->library("util/random_util");
        $this->load->library("businesslogic/lazapi");
        $this->load->library("businesslogic/number_bl");
		
        $this->global_arr=$this->laztoken_model->getlatesttoken();

		$this->accesstoken=$this->global_arr->token;

        //print_r($this->global_arr);

        //echo $this->accesstoken;
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

    public function testit()
    {

    }

      public function getcode()
      {

        $this->load->view('admin/getcode');
      }
      
     function getaccesstoken($AccessCode=NULL)
     {

     	

     	 include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
     	 //echo $AccessCode."<br>";
         //echo $this->config->item('lazAuthAPI')."<br>";
         //echo $this->config->item('Appkey')."<br>";
         //echo $this->config->item('Secret')."<br>";
     	 $c = new LazopClient($this->config->item('lazAuthAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
         $request = new LazopRequest("/auth/token/create");
         if (is_null($AccessCode))
         {
           
         $request->addApiParam("code", LAZADA_CODE);   
         }
         else
         {
            echo "<br>it is not null so took from Param:".$AccessCode;
         $request->addApiParam("code", $AccessCode);   
         }
         
         $response = $c->execute($request);

         $row = json_decode($response,true);
          
         $data=array('code'=>$AccessCode,
                    'token'=>$row["access_token"],
                    'refreshtoken'=>$row["refresh_token"],
                    'refresh_expires_in'=>$row["refresh_expires_in"]
                   );

         

        $this->laztoken_model->insert_token($data);
        
     
       $this->load->view("admin/lazauthen",$data);

        //$this->config->set_item('accesstoken', $row["access_token"]);
        //$this->config->set_item('refresh_token', $row["refresh_token"]);
        //$this->config->set_item('refresh_expires_in', $row["refresh_expires_in"]);

     	
     }

     public function refreshtoken()
     {
        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';


     // echo "<br>life expirein: ".$this->global_arr->refresh_expires_in;
      //echo "<br>totlife: ".$this->global_arr->totlife;
      
      
        //if($this->global_arr->refresh_expires_in-$this->global_arr->totlife<=60)  
        //{
        //if($this->global_arr->refreshtoken!=0) //there is a valid token
        //{
//print_r($arr);
            
        $this->global_arr=$this->laztoken_model->getlatesttoken();

        $this->accesstoken=$this->global_arr->token;

         	$c = new LazopClient($this->config->item('lazAuthAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
    		$request = new LazopRequest('/auth/token/refresh');
    		$request->addApiParam('refresh_token',$this->global_arr->refreshtoken);
    		$response = $c->execute($request);

             $row = json_decode($response,true);

           

             $data=array('code'=>$this->global_arr->code,
                        'token'=>$row["access_token"],
                        'refreshtoken'=>$row["refresh_token"],
                        'refresh_expires_in'=>$row["refresh_expires_in"]

             );

             print_r($data);

            $this->laztoken_model->insert_token($data);
            //$this->session->set_userdata("accesstoken",$row["access_token"]);
            //$this->session->set_userdata("refresh_token",$row["refresh_token"]);
            //$this->session->set_userdata("refresh_expires_in",$row["refresh_expires_in"]);
        //}
       //}

   

     }


public function testfunction()
{

    $latest_order_date=$this->lazada_orders_model->select_latest_record();
    if(is_null($latest_order_date))
    {
 echo "empty order";
    }
    else

    {

    echo $latest_order_date;    
    }
    
}




public function getOrders()
     {


      //1. select latest order in db

      $latest_order_date=$this->lazada_orders_model->select_latest_record();

      //print_r($latest_order_date);
      
         include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
//echo $this->session->userdata("accesstoken");
        

		$c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
		$request = new LazopRequest('/orders/get','GET');
        $skipfirstrow=TRUE;
		if(is_null($latest_order_date))
		{
          //echo "here:".BNY_ESTABLISHDATETIME;  
		$request->addApiParam('created_after', BNY_ESTABLISHDATETIME);
        $skipfirstrow=FALSE;
	    }
	    else
	    {
	    $request->addApiParam('created_after', date(DATE_ISO8601, strtotime($latest_order_date)));	
        $skipfirstrow=TRUE;
	    }

		$request->addApiParam('sort_direction','ASC');
		$request->addApiParam('offset','0');
		$request->addApiParam('limit','20');
		$request->addApiParam('sort_by','created_at');
		//$orders=$c->execute($request,$accesstoken);
		$orders=$c->execute($request,$this->accesstoken);
        

		$datas = json_decode($orders,true);

       // print_r($datas);

          if($datas["code"]==0)
          {

            if(count($datas["data"]["orders"])==0)
            {
                echo "done";

            }
            else
            {

            //we count 1existing row

                
           $rowcount=0;
           $log_code = $this->random_util->create_random_number(8);
           //print_r($datas["data"]["orders"]);

           $data_insert = array();
           $num_data = 0;
           $keygen = $this->random_util->create_random_number(8);

          //$arr_last_textinvoiceID= $this->lazada_orders_model->get_last_taxinvoiceID();
          //$last_textinvoiceID = $arr_last_textinvoiceID['taxinvoiceID'];

          foreach($datas["data"]["orders"] as $row)
        	{
        

                if($rowcount>0 || !$skipfirstrow)
                {

                    if($this->array_util->getlastElement($row["statuses"])=='failed' || $this->array_util->getlastElement($row["statuses"])=='delivered' || $this->array_util->getlastElement($row["statuses"])=='returned')
                    {
        
                $existing_rows=$this->count_order();

                 //get order Items
            //$the_order=$row["order_number"];
            //$this->getOrderItems($the_order);
            
                //print_r($row);
        		//echo "====>".$row["order_number"];
         //prep order data

        		     $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);


            //$last_textinvoiceID = $last_textinvoiceID+1;
            //$next_textinvoiceID = $this->number_bl->add_font_digi($last_textinvoiceID,5);     
            //$new_textinvoiceID = $arr_last_textinvoiceID['code'].$next_textinvoiceID;   

            //------ Start Get New LAZ Order Number------------
            if($this->array_util->getlastElement($row["statuses"])=='delivered')
            {
                if($num_data == 0){

                   $arr_lastorder = $this->lazada_orders_model->last_order();   
                   $new_textinvoiceID = $this->lazapi->get_laz_code($arr_lastorder['taxinvoiceID'],$row["created_at"]);    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->lazapi->get_laz_code($last_id,$row["created_at"]);  
                    $last_id = $new_textinvoiceID;

                } 
            }  
            else
            {
                $new_textinvoiceID="";
            } 

            //------End Get New LAZ Order Number------------      

        	$data=array('order_number'=>$row["order_number"],
                    'taxinvoiceID' => $new_textinvoiceID,
        			'created_at'=>$this->common_util->getDbDate($row["created_at"]),
        			'shipping_fee_original'=>$this->common_util->prep_float($row["shipping_fee_original"]),
        			'shipping_fee_discount_platform'=>$this->common_util->prep_float($row["shipping_fee_discount_platform"]),
        			'shipping_fee_discount_seller'=>$this->common_util->prep_float($row["shipping_fee_discount_seller"]),
        			'shipping_fee'=>$this->common_util->prep_float($row["shipping_fee"]),
        			'voucher_platform'=>$this->common_util->prep_float($row["voucher_platform"]),
        			'voucher_seller'=>$this->common_util->prep_float($row["voucher_seller"]),
        			'voucher'=>$this->common_util->prep_float($row["voucher"]),
        			'price'=>$this->common_util->prep_float($row["price"]),
        			'tax_code'=>$tax_code,
        			'status'=>$this->array_util->getlastElement($row["statuses"]),
        			'delivery_info'=>$row["delivery_info"],
                    'updated_at'=>$this->common_util->getDbDate($row["updated_at"]),
                    'keygen' => $keygen

        	);


        

             // if($this->array_util->getlastElement($row["statuses"])=='failed' || $this->array_util->getlastElement($row["statuses"])=='delivered' || $this->array_util->getlastElement($row["statuses"])=='returned'){
               
              // $data_insert = $this->lazada_orders_model->insertOrder($data);
              
              

               if(!is_null($row["order_number"])){

                    array_push($data_insert,$data);
                    $num_data = $num_data +1;

               }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Order Insert NULL Value Order Number = '.$row["order_number"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }

               //sleep(5);

            //    if($this->count_order()-$existing_rows==0)
            //{
             //   exit(); //kill process if inset fail
            //}

            /* 
             //prep customer data  
        		$data=array('mobile'=>$row["address_billing"]["phone"],
        			'first_name'=>$row["address_billing"]["first_name"],
        			'last_name'=>$row["address_billing"]["last_name"],
        			'order_number'=>$row["order_number"],
        			'price'=>$this->common_util->prep_float($row["price"])
        			);	
        			
             

         	$this->lazada_customers_model->insertCustomers($data);

        
         	//prep shipping address data  
        		$data=array('first_name'=>$row["address_shipping"]["first_name"],
        			'first_name'=>$row["address_shipping"]["first_name"],
        			'last_name'=>$row["address_shipping"]["last_name"],
        			'address1'=>$row["address_shipping"]["address1"],
        			'address2'=>$row["address_shipping"]["address2"],
        			'address3'=>$row["address_shipping"]["address3"],
        			'address4'=>$row["address_shipping"]["address4"],
        			'address5'=>$row["address_shipping"]["address5"],
        			'phone'=>$row["address_shipping"]["phone"],
        			'city'=>$row["address_shipping"]["city"],
        			'post_code'=>$row["address_shipping"]["post_code"],
        			'order_number'=>$row["order_number"]
        			);	

        			

         	$this->lazada_shipping_address_model->insert_shippingaddress($data);

         	//prep billing address data  
        		$data=array('first_name'=>$row["address_billing"]["first_name"],
        			'first_name'=>$row["address_billing"]["first_name"],
        			'last_name'=>$row["address_billing"]["last_name"],
        			'address1'=>$row["address_billing"]["address1"],
        			'address2'=>$row["address_billing"]["address2"],
        			'address3'=>$row["address_billing"]["address3"],
        			'address4'=>$row["address_billing"]["address4"],
        			'address5'=>$row["address_billing"]["address5"],
        			'phone'=>$row["address_billing"]["phone"],
        			'city'=>$row["address_billing"]["city"],
        			'post_code'=>$row["address_billing"]["post_code"],
        			'order_number'=>$row["order_number"]
        			);	

        			

         	$this->lazada_billing_address_model->insert_billingaddress($data);
            */


            } // if statuses what we want 
        }

           $rowcount++;
       }// foreach

       //print_r($data_insert);
       $cnt_data = count($data_insert);
       //echo $num_data."<>".$cnt_data;
       if($cnt_data > 0){
        $this->lazada_orders_model->insert_all($data_insert);

        $arr_numrow = $this->lazada_orders_model->select_by_keygen($keygen);
        //echo $arr_numrow['cnt'];

        if($arr_numrow['cnt'] < $num_data){
            $this->lazada_orders_model->delete_by_keygen($keygen);
            $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Order Not Insert',
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);
        }
       }

    		}// number of order <>0

            }else{

                 $datalog = array(
                    'log_type' => 1,
                    'log_code' => 'No code',
                    'log_note' => 'API LAZADA Token Expired',
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

            } // data is valid
          

     }

      function count_order(){

       return $this->lazada_orders_model->count_order()->count;


     }


     public function test()
     {


        echo $this->lazada_orders_model->select_latest_record();

     }

          public function getOrderItems_test()
     {

        
         /*
         $this->lazapi->setAppPath('/order/items/get');
         $this->lazapi->setAppkey($this->config->item('Appkey'));
         $this->lazapi->setAppSecret($this->config->item('Secret'));

         $this->lazapi->addparam('order_id','342166995455049');
         $this->lazapi->addparam('app_key',$this->lazapi->getAppKey());
         $this->lazapi->addparam('sign_method','sha256');
         $this->lazapi->addparam('timestamp',$this->lazapi->getTimeStamp());
         $this->lazapi->addparam('access_token',$this->accesstoken);
          $return=$this->lazapi->callAPI();


          print_r($return);


     */

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/order/items/get','GET');
        $request2->addApiParam('order_id','342166995455049');
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);


 
        

        var_dump($datas2);

       

     }


     public function getOrderItemfromOrders()
     {


       $arr=$this->lazada_orders_model->getorder_no_suborders(20);

       print_r($arr);
       

       if(!is_null($arr))
       {
            foreach ($arr as $row) {


                $this->getOrderItems($row["order_number"]);

               // $data=array(
               //  'suborderinsereted'=>1       
               // );

               // $this->lazada_orders_model->update_order($row["order_number"],$data);
            }



       }


     }

     function getOrderItems($order_id)
     {

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/order/items/get','GET');
        $request->addApiParam('order_id',$order_id);
        $items=$c->execute($request,$this->accesstoken);

        $datas = json_decode($items,true);
        print_r($datas);

        $log_code = $this->random_util->create_random_number(8);
        

        if($datas["code"]==0)
        {

           $data_insert = array();
           $num_data = 0;
           $keygen = $this->random_util->create_random_number(8);

          foreach($datas["data"] as $row)
            {
               
                //echo "====>".$row["order_number"];
         //prep order data

                     

            $data2=array('order_number'=>$row["order_id"],
                    'sku'=>$row["sku"],
                    'name'=>$row["name"],
                    'item_price'=>$this->common_util->prep_float($row["item_price"]),
                    'voucher_seller'=>$this->common_util->prep_float($row["voucher_seller"]),
                    'voucher_platform'=>$this->common_util->prep_float($row["voucher_platform"]),
                    'paid_price'=>$this->common_util->prep_float($row["paid_price"]),
                    'tax_amount'=>$this->common_util->prep_float($row["tax_amount"]),
                    'keygen' => $keygen

            );

            //$data_insert = $this->lazada_orderitems_model->insertOrderitems($data2);

            if(!is_null($row["order_id"])){

                    array_push($data_insert,$data2);
                    $num_data = $num_data +1;

               }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Order Insert NULL Value Order Number = '.$row["order_id"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }


            }// for

                $cnt_data = count($data_insert);
               //echo $num_data."<>".$cnt_data;
               if($cnt_data > 0){
                $this->lazada_orderitems_model->insert_all($data_insert);

                $arr_numrow = $this->lazada_orderitems_model->select_by_keygen($keygen);
                //echo $arr_numrow['cnt'];

                if($arr_numrow['cnt'] < $num_data){
                    $this->lazada_orderitems_model->delete_by_keygen($keygen);
                    $datalog = array(
                            'log_type' => 1,
                            'log_code' => $log_code,
                            'log_note' => 'API LAZADA Order Not Insert',
                            'log_status' => 1
                        );

                        $this->bnylog_model->insert($datalog);
                }
               } 

        }//if
             

     }


public function getlatestrecorddate()
{

//echo $this->lazada_orders_model->select_latest_record();

    // if($this->lazada_orders_model->select_latest_record()=="")
   //  {

   //  	$this->getOrders(Null);
    // }
   //  else
   //  {
   //     $this->getOrders($this->lazada_orders_model->select_latest_record());	
   //  }
    // 
   return $this->lazada_orders_model->select_latest_record();


}



   public function lazauthen()
   {


$this->load->view("admin/lazauthen");

   }

	public function index()
	{
//sleep(10);

        echo "hello shopee";
//echo $this->input->get('code');
		//$this->getaccesstoken(trim($this->input->get('code')));  //get access token and store in $config["accesstoken"]
        //$this->getaccesstoken($this->input->get('code'));
		

		//$this->load->view("admin/lazdatacyncro");

        
		
/*
   include APPPATH . 'third_party\api\lazada\LazopSdk.php';

     /*$c = new LazopClient('https://api.lazada.test/rest', '123793', 'NPb6xpPp3EouAS0uhqYtVG0dNEXw6hAN');
    $request = new LazopRequest('/mock/api/get');
    $request->addApiParam('api_id',1);
    $request->addHttpHeaderParam('cx','test');
    
    var_dump($c->execute($request));
  


    $c = new LazopClient('https://api.lazada.test/rest','123793','NPb6xpPp3EouAS0uhqYtVG0dNEXw6hAN');
$request = new LazopRequest('/order/document/get','GET');
$request->addApiParam('doc_type','shippingLabel');
$request->addApiParam('order_item_ids','[279709, 279709]');
var_dump($c->execute($request, '50000900135c1pxTpiBUQHzlK9sVnthc2IxgoQcrrh6jCT61f3f4271pwceFRhUx'));
*/

    }


}
