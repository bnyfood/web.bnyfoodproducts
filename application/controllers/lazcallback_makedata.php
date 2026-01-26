<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 class lazcallback_makedata extends CI_Controller {
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
        $this->load->model('lazada_finance_transaction_details_model');
        $this->load->model('api_prepare_data_model');
        $this->load->model('lazada_finance_transaction_payout_model');
        $this->load->model('lazada_taxinvoiceid_model');
        $this->load->model('lazada_tracking_model');


		$this->load->library("util/array_util");
        $this->load->library("util/common_util");
        $this->load->library("util/random_util");
        $this->load->library("util/date_util");
        
        $this->load->library("businesslogic/lazapi");
        $this->load->library("businesslogic/number_bl");
        $this->load->library("businesslogic/lazada_bl");
		
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

public function getFinanceDetailTest()
{


    include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
     $num_date = 0;
//echo $this->session->userdata("accesstoken");

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/finance/transaction/detail/get','GET');
        $request->addApiParam('trans_type','-1');

        $start_time="2021-02-04";
        $end_time="2021-02-04";

        //$start_time = date(DATE_ISO8601, strtotime($start_time));
        //$end_time = date(DATE_ISO8601, strtotime($end_time));
      //  echo $start_time."<br>";

        $offset=500;
        $request->addApiParam('start_time', $start_time);  
        $request->addApiParam('end_time', $end_time);  
        $request->addApiParam('limit','500');
        $request->addApiParam('offset',$offset);
        $orders=$c->execute($request,$this->accesstoken);
    

        $datas = json_decode($orders,true);
        print_r($datas);


}


public function getFinancedetail()
{
   //we check 

    $data_api_prepare = $this->api_prepare_data_model->select_by_shopid('123456');
    //print_r($data_api_prepare);
    if(!empty($data_api_prepare)){


        $start_time = $data_api_prepare['start'];
        $next_day = $data_api_prepare['next_day'];

        //$end_time = $data_api_prepare['stop'];
        $offset = $data_api_prepare['offset'];
        $cnt_date = $data_api_prepare['cnt_date'];

    
    
    
     include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
     $num_date = 0;
//echo $this->session->userdata("accesstoken");

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/finance/transaction/detail/get','GET');
        $request->addApiParam('trans_type','-1');

        //$start_time = date(DATE_ISO8601, strtotime($start_time));
        //$end_time = date(DATE_ISO8601, strtotime($end_time));
        echo $start_time."<br>";

        $request->addApiParam('start_time', $start_time);  
        $request->addApiParam('end_time', $start_time);  
        $request->addApiParam('limit','500');
        $request->addApiParam('offset',$offset);
        $orders=$c->execute($request,$this->accesstoken);
    

        $datas = json_decode($orders,true);
        print_r($datas);

        $arr_datainsert = array();

        if(count($datas["data"]) == 0){ //no more data to download from this day

            echo "done";
            
            $chk_cnt_db = $this->lazada_finance_transaction_details_model->cnt_by_date($start_time);

            if($chk_cnt_db < $num_date){
                $this->lazada_finance_transaction_details_model->del_by_date($start_time);
                $datalog = array(
                        'log_type' => 2,
                        'log_code' => 'Nsdfaswe',
                        'log_note' => 'API LAZADA Finance Insert Not Complete',
                        'log_status' => 1
                    );

                $this->bnylog_model->insert($datalog);

                $data_preapre_update  = array(
                        'action_date' => $start_time,
                        'offset' => 0,
                        'cnt_date' => 0,
                        'is_complete' => 0
                    ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');
            }else{

                $data_preapre_update  = array(
                    'action_date' => $next_day,
                    'offset' => 0,
                    'cnt_date' => 0,
                    'is_complete' => 1
                ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');
            }

        }else{

            $rowcount=0;
            $log_code = $this->random_util->create_random_number(8);
            $keygen = $this->random_util->create_random_number(8);
            $num_data = 0;
            $cnt_data = count($datas["data"]);
            $num_date  = $cnt_date + $cnt_data;

            $data_preapre_update  = array(
                'action_date' => $start_time,
                'offset' => $offset+500,
                'cnt_date' => $num_date,
                'is_complete' => 0
            ); 
            $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');


            foreach($datas["data"] as $data){

                $transaction_date = date_create($data['transaction_date']);
                $transaction_date = date_format($transaction_date,"Y-m-d");

                $data_insert = array(
                    'order_number' => $data['order_no'],
                    'transaction_date' => $this->common_util->getDbDate($data['transaction_date']),
                    'amount' => $this->common_util->prep_float($data['amount']),
                    'paid_status' => $data['paid_status'],
                    'shipping_provider' => $data['shipping_provider'],
                    'WHT_included_in_amount' => $data['WHT_included_in_amount'],
                    'payment_ref_id' => $data['payment_ref_id'],
                    'lazada_sku' => $data['lazada_sku'],
                    'transaction_type' => $data['transaction_type'],
                    'orderItem_no' => $data['orderItem_no'],
                    'orderItem_status' => $data['orderItem_status'],
                    'reference' => $data['reference'],
                    'fee_name' => $data['fee_name'],
                    'shipping_speed' => $data['shipping_speed'],
                    'WHT_amount' => $this->common_util->prep_float($data['WHT_amount']),
                    'transaction_number' => $data['transaction_number'],
                    'seller_sku' => $data['seller_sku'],
                    'statement' => $data['statement'],
                    'details' => $data['details'],
                    'VAT_in_amount' => $this->common_util->prep_float($data['VAT_in_amount']),
                    'shipment_type' => $data['shipment_type'],
                    'keygen' => $keygen

                );

                if(!is_null($data["order_no"])){

                    array_push($arr_datainsert,$data_insert);
                    $num_data = $num_data +1;

               }else{

                $datalog = array(
                    'log_type' => 2,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Finance Insert NULL Value Order Number = '.$data["order_no"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }

            }

            $cnt_data = count($arr_datainsert);
           //echo $num_data."<>".$cnt_data;
           if($cnt_data > 0){
            $this->lazada_finance_transaction_details_model->insert_all($arr_datainsert);

           }


        } // if count($datas["data"]) == 0

    }else{ // if !empty($data_api_prepare)

        $datalog = array(
            'log_type' => 2,
            'log_code' => 'sfrwerwr',
            'log_note' => 'API LAZADA Finance No Shop id',
            'log_status' => 1
        );

        $this->bnylog_model->insert($datalog);
    }

}


public function getFinancedetail_v2()
{
   //we check 

    $data_api_prepare = $this->api_prepare_data_model->select_by_shopid('123456');
    //print_r($data_api_prepare);
    if(!empty($data_api_prepare)){

        //$start_time = BNY_ESTABLISHDATE;
        //$end_time = BNY_ESTABLISHDATE;
        $is_last_formdb = 0;

        if($data_api_prepare['is_complete'] == 0){

            $start_time = $data_api_prepare['start'];
            $next_day = $data_api_prepare['next_day'];

            //$end_time = $data_api_prepare['stop'];
            $offset = $data_api_prepare['offset'];
            $cnt_date = $data_api_prepare['cnt_date'];

        }else{
            $latest_finance_date=$this->lazada_finance_transaction_details_model->select_latest_record();
            //print_r($latest_finance_date);
            if(!empty($latest_finance_date)){
                $data_preapre_update  = array(
                    'action_date' => $latest_finance_date['start'],
                    'offset' => 0,
                    'cnt_date' => 0,
                    'is_complete' => 0
                ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');

                $start_time = $latest_finance_date['start'];
                $next_day = $latest_finance_date['next_day'];
                $is_last_formdb  = 1;

            }else{
                //for first record
                $data_preapre_update  = array(
                    'action_date' => $data_api_prepare['start'],
                    'offset' => 0,
                    'cnt_date' => 0,
                    'is_complete' => 0
                ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');

                $start_time = $data_api_prepare['start'];
                $next_day = $data_api_prepare['next_day'];
            }

            $offset = 0;
            $cnt_date = 0;
        }    
    
    
     include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
     $num_date = 0;
//echo $this->session->userdata("accesstoken");

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/finance/transaction/detail/get','GET');
        $request->addApiParam('trans_type','-1');

        //$start_time = date(DATE_ISO8601, strtotime($start_time));
        //$end_time = date(DATE_ISO8601, strtotime($end_time));
        echo $start_time."<br>";

        $request->addApiParam('start_time', $start_time);  
        $request->addApiParam('end_time', $start_time);  
        $request->addApiParam('limit','100');
        $request->addApiParam('offset',$offset);
        $orders=$c->execute($request,$this->accesstoken);
    

        $datas = json_decode($orders,true);
        print_r($datas);

        $arr_datainsert = array();

        if(count($datas["data"]) == 0){ //no more data to download from this day

            echo "done";
            
            $chk_cnt_db = $this->lazada_finance_transaction_details_model->cnt_by_date($start_time);

            if($chk_cnt_db < $num_date){
                $this->lazada_finance_transaction_details_model->del_by_date($start_time);
                $datalog = array(
                        'log_type' => 2,
                        'log_code' => 'Nsdfaswe',
                        'log_note' => 'API LAZADA Finance Insert Not Complete',
                        'log_status' => 1
                    );

                $this->bnylog_model->insert($datalog);

                $data_preapre_update  = array(
                        'action_date' => $start_time,
                        'offset' => 0,
                        'cnt_date' => 0,
                        'is_complete' => 0
                    ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');
            }

            $data_preapre_update  = array(
                'action_date' => $next_day,
                'offset' => 0,
                'cnt_date' => 0,
                'is_complete' => 1
            ); 
            $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');

        }else{

            $rowcount=0;
            $log_code = $this->random_util->create_random_number(8);
            $keygen = $this->random_util->create_random_number(8);
            $num_data = 0;
            $cnt_data = count($datas["data"]);
            $num_date  = $cnt_date + $cnt_data;

            $data_preapre_update  = array(
                'action_date' => $start_time,
                'offset' => $offset+100,
                'cnt_date' => $num_date,
                'is_complete' => 0
            ); 
            $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456');


            foreach($datas["data"] as $data){

                $transaction_date = date_create($data['transaction_date']);
                $transaction_date = date_format($transaction_date,"Y-m-d");

                $data_insert = array(
                    'order_number' => $data['order_no'],
                    'transaction_date' => $this->common_util->getDbDate($data['transaction_date']),
                    'amount' => $this->common_util->prep_float($data['amount']),
                    'paid_status' => $data['paid_status'],
                    'shipping_provider' => $data['shipping_provider'],
                    'WHT_included_in_amount' => $data['WHT_included_in_amount'],
                    'payment_ref_id' => $data['payment_ref_id'],
                    'lazada_sku' => $data['lazada_sku'],
                    'transaction_type' => $data['transaction_type'],
                    'orderItem_no' => $data['orderItem_no'],
                    'orderItem_status' => $data['orderItem_status'],
                    'reference' => $data['reference'],
                    'fee_name' => $data['fee_name'],
                    'shipping_speed' => $data['shipping_speed'],
                    'WHT_amount' => $this->common_util->prep_float($data['WHT_amount']),
                    'transaction_number' => $data['transaction_number'],
                    'seller_sku' => $data['seller_sku'],
                    'statement' => $data['statement'],
                    'details' => $data['details'],
                    'VAT_in_amount' => $this->common_util->prep_float($data['VAT_in_amount']),
                    'shipment_type' => $data['shipment_type'],
                    'keygen' => $keygen

                );

                if(!is_null($data["order_no"])){

                    array_push($arr_datainsert,$data_insert);
                    $num_data = $num_data +1;

               }else{

                $datalog = array(
                    'log_type' => 2,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Finance Insert NULL Value Order Number = '.$data["order_no"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }

            }

            $cnt_data = count($arr_datainsert);
           //echo $num_data."<>".$cnt_data;
           if($cnt_data > 0){
            $this->lazada_finance_transaction_details_model->insert_all($arr_datainsert);

           }


        }

    }else{

        $datalog = array(
            'log_type' => 2,
            'log_code' => 'sfrwerwr',
            'log_note' => 'API LAZADA Finance No Shop id',
            'log_status' => 1
        );

        $this->bnylog_model->insert($datalog);
    }

}




public function getPayout(){

    $latest_order_date=$this->lazada_finance_transaction_payout_model->select_latest_record();


    include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
    $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
    $request = new LazopRequest('/finance/payout/status/get','GET');
    if(!empty($latest_finance_date)){

        $request->addApiParam('created_after', $latest_order_date['start']);

    }else{

        $request->addApiParam('created_after', BNY_ESTABLISHDATE);
    }

    //$request->addApiParam('sort_direction','ASC');
    //$request->addApiParam('offset','0');
    //$request->addApiParam('limit','20');
    //$request->addApiParam('sort_by','created_at');

    $orders=$c->execute($request,$this->accesstoken);
        

    $datas = json_decode($orders,true);

    print_r($datas);

    //lazada_finance_transaction_payout_model
    $data_insert = array();
    if($datas["code"]==0){

        if(count($datas["data"])==0){
            echo "done";

        }else{

            $rowcount=0;
            $log_code = $this->random_util->create_random_number(8);
            //print_r($datas["data"]["orders"]);

            $data_insert = array();
            $num_data = 0;
            $keygen = $this->random_util->create_random_number(8);
            $num_data = 0;

            foreach($datas["data"] as $row){

                if($row['paid'] == 1){

                    $data=array(

                    'subtotal2' => $this->common_util->prep_float($row["subtotal2"]),
                    'subtotal1' => $this->common_util->prep_float($row["subtotal1"]),
                    'shipment_fee_credit' => $this->common_util->prep_float($row["shipment_fee_credit"]),
                    'payout' => $row["payout"],
                    'item_revenue' => intval($row["item_revenue"]),
                    'created_at' => $this->common_util->getDbDate($row["created_at"]),
                    'other_revenue_total'=> intval($row["other_revenue_total"]),
                    'fees_total' => $this->common_util->prep_float($row["fees_total"]),
                    'refunds' => intval($row["refunds"]),
                    'guarantee_deposit'=> intval($row["guarantee_deposit"]),
                    'updated_at'=>$this->common_util->getDbDate($row["updated_at"]),
                    'fees_on_refunds_total' => intval($row["fees_on_refunds_total"]),
                    'closing_balance' => $this->common_util->prep_float($row["closing_balance"]),
                    'paid' => intval($row["paid"]),   
                    'opening_balance' => $this->common_util->prep_float($row["opening_balance"]),
                    'statement_number' => $row["statement_number"],
                    'shipment_fee' => $this->common_util->prep_float($row["shipment_fee"]),
                    'keygen' => $keygen
                );

                    if(!is_null($row["statement_number"])){

                        array_push($data_insert,$data);
                        $num_data = $num_data +1;

                    }else{

                        $datalog = array(
                            'log_type' => 3,
                            'log_code' => $log_code,
                            'log_note' => 'API LAZADA Transaction Payout Insert NULL Value Order Number = '.$row["statement_number"],
                            'log_status' => 1
                        );

                        $this->bnylog_model->insert($datalog);

                        break;
                   }
                } // if paid =1

            } // for    
        }

        $cnt_data = count($data_insert);
       //echo $num_data."<>".$cnt_data;
       if($cnt_data > 0){
        $this->lazada_finance_transaction_payout_model->insert_all($data_insert);

        $arr_numrow = $this->lazada_finance_transaction_payout_model->select_by_keygen($keygen);
        //echo $arr_numrow['cnt'];

        if($arr_numrow['cnt'] < $num_data){
            $this->lazada_finance_transaction_payout_model->delete_by_keygen($keygen);
            $datalog = array(
                    'log_type' => 3,
                    'log_code' => $log_code,
                    'log_note' => 'API LAZADA Transaction Payout Not Insert',
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);
        }//if $arr_numrow['cnt'] < $num_data
       }//if $cnt_data > 0)
    }else{ //$datas["code"]==0  

         $datalog = array(
            'log_type' => 3,
            'log_code' => 'No code',
            'log_note' => 'API LAZADA Token Expired',
            'log_status' => 1
        );

        $this->bnylog_model->insert($datalog);

    }   
}

function order_next(){
    $latest_order_date=$this->lazada_orders_model->get_next_download_time();  
    if(is_null($latest_order_date['last_cdate'])){
        echo "noooo";
    }
    print_r($latest_order_date);

    $last_order_by_date = $this->lazada_orders_model->next_order_by_date();
    print_r($last_order_by_date);

    $arr_all_order = $this->lazada_orders_model->select_cnt_all();
    echo $arr_all_order;


}



public function getOrders()
     {

        $chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');


      $arr_latest_order_date=$this->lazada_orders_model->get_next_download_time();    
      print_r($arr_latest_order_date);
      $arr_all_order = $this->lazada_orders_model->select_cnt_all();

        
        if($arr_all_order == 0){
            //empty order data
            $latest_order_date = 1;
        }else{
            if(is_null($arr_latest_order_date['last_cdate'])){
                //no next time
                $latest_order_date = 2;
                $last_order_by_date = $this->lazada_orders_model->next_order_by_date();

              }else{
                //next time
                $latest_order_date = 3;
              }
        }

      

      echo ">>".$latest_order_date;
      
         include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
            //echo $this->session->userdata("accesstoken");
        

		$c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
		$request = new LazopRequest('/orders/get','GET');
        $skipfirstrow=TRUE;

		if($latest_order_date == 1)
		{
          echo "here:".BNY_ESTABLISHDATETIME;  
    		$request->addApiParam('created_after', date(DATE_ISO8601, strtotime(BNY_ESTABLISHDATETIME)));
            $skipfirstrow=FALSE;
	    }
	    else if($latest_order_date == 2)
	    {
    	    $request->addApiParam('created_after', date(DATE_ISO8601, strtotime($last_order_by_date['created_at'])));	
            $skipfirstrow=TRUE;
	    }
        else if($latest_order_date == 3)
        {
            $request->addApiParam('created_after', date(DATE_ISO8601, strtotime($arr_latest_order_date['last_cdate'])));    
            $skipfirstrow=TRUE;
        }

		$request->addApiParam('sort_direction','ASC');
		$request->addApiParam('offset','0');
		$request->addApiParam('limit','100');
		$request->addApiParam('sort_by','created_at');
		//$orders=$c->execute($request,$accesstoken);
		$orders=$c->execute($request,$this->accesstoken);
        

		$datas = json_decode($orders,true);

        print_r($datas);

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

           if(!$this->lazada_order_status_checker_ready_to_insert($datas))
           {

            $data=array(
               'is_laz_orders_busy'=>0
            );

            $this->api_prepare_data_model->update_by_shopid($data,'123456');

            exit();
           }

           echo "data all clear";

          foreach($datas["data"]["orders"] as $row)
        	{
                if($rowcount>0 || !$skipfirstrow)
                {
                    $num_data = $num_data +1;

                    //chk order status death
                    $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled');
                    $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship');

                    $order_death = true;
                    if (in_array($row["statuses"], $array_status_not_death)){
                        $order_death = false;
                    }

                    if($order_death){
                       //order death 
                        $arr_data_death = $this->lazada_orders_model->get_by_status_in($row["order_number"],$array_status_death);
                        if(empty($arr_data_death)){
                            //insert order death 
                		    $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      
                            $data_update_at = $row["updated_at"];
                            if($row["statuses"] == 'canceled'){
                                $data_update_at = $row["created_at"];
                            }
                        	$data=array(
                                'order_number'=>$row["order_number"],
                                'taxinvoiceID' => '',
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
                                'updated_at'=>$this->common_util->getDbDate($data_update_at),
                                'keygen' => $keygen

                        	);   

                            // dup shippet
                            $data_dup=array(
                                'order_number'=>$row["order_number"],
                                'taxinvoiceID' => '',
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
                                'status'=>'shipped',
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                                'keygen' => $keygen

                            );   


                           if(!is_null($row["order_number"])){

                                array_push($data_insert,$data);
                                array_push($data_insert,$data_dup);
                                

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

                       }
                   }else{
                    //order not death
                    $arr_data_not_death = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$row["statuses"]);
                    if(empty($arr_data_not_death)){
                        $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      

                            $data=array(
                                'order_number'=>$row["order_number"],
                                'taxinvoiceID' => '',
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

                            $data_dup=array(
                                'order_number'=>$row["order_number"],
                                'taxinvoiceID' => '',
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
                                'status'=>'shipped',
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                                'keygen' => $keygen

                            );   

                           if(!is_null($row["order_number"])){

                                array_push($data_insert,$data);
                                array_push($data_insert,$data_dup);
                                

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
                        }

                   }
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
        else
        {

             $data=array(
           'is_laz_orders_busy'=>0
        );

        $this->api_prepare_data_model->update_by_shopid($data,'123456');
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


       $arr=$this->lazada_orders_model->getorder_no_suborders(50);

       //print_r($arr);
       

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

     function getOrderItems_val()
     {

        $order_id = '246862209823584';

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/order/items/get','GET');
        $request->addApiParam('order_id',$order_id);
        $items=$c->execute($request,$this->accesstoken);

        $datas = json_decode($items,true);
        print_r($datas);

        $log_code = $this->random_util->create_random_number(8);
        

      /*  if($datas["code"]==0)
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

               // $this->bnylog_model->insert($datalog);

                break;
               }


            }// for



        }//if
             
*/
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
//echo $this->input->get('code');
		//$this->getaccesstoken(trim($this->input->get('code')));  //get access token and store in $config["accesstoken"]
        $this->getaccesstoken($this->input->get('code'));
		

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


    function lazada_order_status_checker_ready_to_insert($orders_in)
    {

       $total_rows=count($orders_in["data"]["orders"]);  //count total rows of orders
        $death_status_rows=0;
        foreach($orders_in["data"]["orders"] as $row)
            {
              
             if($this->array_util->getlastElement($row["statuses"])!='unpaid' && $this->array_util->getlastElement($row["statuses"])!='pending'  && $this->array_util->getlastElement($row["statuses"])!='shipped'  && $this->array_util->getlastElement($row["statuses"])!='ready_to_ship'  && $this->array_util->getlastElement($row["statuses"])!='packed')
                    {
              $death_status_rows=$death_status_rows+1;
                    }
            }
            echo "total_rows: ".$total_rows; 
            echo "death_status_rows: ".$death_status_rows;

 
            if($death_status_rows==$total_rows)
            {
                return true;
            }
            else
            {
             return false;   
            }

        
    }

    function create_textinvoiceid(){

       // $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed');
        $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship','packed');
        //$array_status_not_death = array('packed');

        //'lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','failed_delivery','canceled','unpaid'

        $arr_laz_taxs = $this->lazada_orders_model->select_by_status_last_arr($array_status_not_death,50);

        foreach($arr_laz_taxs as $arr_laz_tax){

            $arr_chk_or = $this->lazada_taxinvoiceid_model->select_taxinvoiceid_by_orderno($arr_laz_tax['order_number']);
            if(empty($arr_chk_or)){
                $arr_lastorder = $this->lazada_taxinvoiceid_model->last_order_code_by_yymm($arr_laz_tax['yyyymm']);   
                print_r($arr_lastorder);

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->lazada_bl->get_lazada_code($arr_lastorder['taxinvoiceID'],$arr_laz_tax['created_at']);  

                 $arr_new_invoice_id = array(
                  'order_number' => $arr_laz_tax['order_number'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'created_at' => $arr_laz_tax['created_at']
                 );

                 print_r($arr_new_invoice_id);

                 $this->lazada_taxinvoiceid_model->insert($arr_new_invoice_id);


               }else{

                $new_textinvoiceID = $this->lazada_bl->get_lazada_code('no',$arr_laz_tax['created_at']);  

                 $arr_new_invoice_id = array(
                  'order_number' => $arr_laz_tax['order_number'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'created_at' => $arr_laz_tax['created_at']
                 );

                 print_r($arr_new_invoice_id);

                 $this->lazada_taxinvoiceid_model->insert($arr_new_invoice_id);

               }
            }

       }
    }

    function create_textinvoiceid_v2(){

        $arr_textinvoices = $this->lazada_orders_model->select_next_invoice(100); 

        //print_r($arr_textinvoices);
        $num_data = 0;

        if(!empty($arr_textinvoices)){ 


            foreach($arr_textinvoices as $arr_textinvoice){

               $arr_lastorder = $this->lazada_orders_model->last_order_code($arr_textinvoice['created_at'],$arr_textinvoice["OrderID"]);   

               if(!empty($arr_lastorder)){

                   $new_textinvoiceID = $this->lazapi->get_laz_code($arr_lastorder['taxinvoiceID'],$arr_textinvoice["created_at"]);    
        
                    //echo $arr_textinvoice["OrderID"].">>".$new_textinvoiceID."<br>";

                    $arr_up = array(
                        'taxinvoiceID' => $new_textinvoiceID
                    );

                    $this->lazada_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                    //sleep(1);
                }else{ // first data

                    $new_textinvoiceID = $this->lazapi->get_laz_code($arr_textinvoice['taxinvoiceID'],$arr_textinvoice["created_at"]);    
        
                   // echo $arr_textinvoice["OrderID"].">>".$new_textinvoiceID."<br>";

                    $arr_up = array(
                        'taxinvoiceID' => $new_textinvoiceID
                    );

                    $this->lazada_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                }
            }        


        }
    }

    function create_textinvoiceid_v1(){

        $arr_textinvoices = $this->lazada_orders_model->select_next_invoice(500); 

        //print_r($arr_textinvoices);
        $num_data = 0;

        if(!empty($arr_textinvoices)){ 


            foreach($arr_textinvoices as $arr_textinvoice){

                if($num_data == 0){   

                   $arr_lastorder = $this->lazada_orders_model->last_order_code();   
                   $new_textinvoiceID = $this->lazapi->get_laz_code($arr_lastorder['taxinvoiceID'],$arr_textinvoice["created_at"]);    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->lazapi->get_laz_code($last_id,$arr_textinvoice["created_at"]);  
                    $last_id = $new_textinvoiceID;

                } 

                $num_data = $num_data +1;
                //echo $arr_textinvoice["created_at"].">>".$last_id."<br>";

                $arr_up = array(
                    'taxinvoiceID' => $last_id
                );

                $this->lazada_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                //sleep(1);
            }        


        }
    }

    public function getOrder_test()
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/order/get','GET');
        $request2->addApiParam('order_id','483356583288800');
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);


 
        

        var_dump($datas2);

       

     }

     function get_tracking_number(){

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/logistic/order/trace','GET');
       /* $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
            $request2 = new LazopRequest('/logistic/order/trace','GET');
            $request2->addApiParam('order_id','594178181065522');
            $items2=$c2->execute($request2,$this->accesstoken);

            $datas2 = json_decode($items2,true);

            if(!empty($datas2)){
                echo $datas2['result']['module'][0]['package_detail_info_list'][0]['tracking_number'];
            }*/

        //var_dump($datas2);

        

        $arr_lazorders = $this->lazada_orders_model->get_orderno_tracking(40);

        foreach($arr_lazorders as $arr_lazorder){

            
            $request2->addApiParam('order_id',$arr_lazorder['order_number']);
            $items2=$c2->execute($request2,$this->accesstoken);

            $datas2 = json_decode($items2,true);

            //print_r($datas2);

            if(!empty($datas2)){
                if(!empty($datas2['result']['module'])){
                    //echo $datas2['result']['module'][0]['package_detail_info_list'][0]['tracking_number'];
                   $tracking_number = $datas2['result']['module'][0]['package_detail_info_list'][0]['tracking_number'];

                   if( $tracking_number != ""){
                       $data_insert = array(
                        'order_number' => $arr_lazorder['order_number'],
                        'tracking_number' => $tracking_number,
                        'api_round' => 1,
                        'api_finish' => 1
                       );

                       $this->lazada_tracking_model->insert($data_insert);
                       echo "Yes->>".$tracking_number."<br>";
                   }
               }else{

                    $data_insert = array(
                        'order_number' => $arr_lazorder['order_number'],
                        'tracking_number' => '',
                        'api_round' => 1
                    );

                    $this->lazada_tracking_model->insert($data_insert);
                    echo "No->>xxxxx<br>";
               }

            }



       }
    }

    function get_tracking_number_more(){

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/logistic/order/trace','GET');

        $arr_lazorders = $this->lazada_tracking_model->get_orderno_tracking_round(50);

        foreach($arr_lazorders as $arr_lazorder){

            $round = $arr_lazorder['api_round'];

            $api_round = $round +1;

            $request2->addApiParam('order_id',$arr_lazorder['order_number']);
            $items2=$c2->execute($request2,$this->accesstoken);

            $datas2 = json_decode($items2,true);

            //print_r($datas2);

            if(!empty($datas2)){
                if(!empty($datas2['result']['module'])){
                    //echo $datas2['result']['module'][0]['package_detail_info_list'][0]['tracking_number'];
                   $tracking_number = $datas2['result']['module'][0]['package_detail_info_list'][0]['tracking_number'];

                   if( $tracking_number != ""){
                       $data_update = array(
                        'tracking_number' => $tracking_number,
                        'api_round' => $api_round,
                        'api_finish' => 1
                       );

                       $this->lazada_tracking_model->update($data_update,$arr_lazorder['lazada_tracking_id']);
                       echo "Yes->>".$tracking_number."<br>";
                   }
               }else{

                    $data_update = array(
                        'api_round' => $api_round
                    );

                    $this->lazada_tracking_model->update($data_update,$arr_lazorder['lazada_tracking_id']);
                    echo "No->>xxxxx<br>";
               }

            }



       }
    }

}
