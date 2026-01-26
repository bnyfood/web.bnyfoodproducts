<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 class lazcallback extends CI_Controller {
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
        $this->load->model('lazada_prep_model');
        $this->load->model('lazada_prep_api_model');
        $this->load->model('free_model');


        $this->load->library("util/array_util");
        $this->load->library("util/common_util");
        $this->load->library("util/random_util");
        $this->load->library("util/date_util");
        $this->load->library("util/db_util");
        
        $this->load->library("businesslogic/lazapi");
        $this->load->library("businesslogic/number_bl");
        $this->load->library("businesslogic/lazada_bl");
        $this->load->library("businesslogic/data_bl");
        $this->load->library("businesslogic/upload_bl");
		
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

         print_r($data);

        $this->laztoken_model->insert_token($data);
        
     
       //$this->load->view("admin/lazauthen",$data);

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
        $arr_laz = $this->laztoken_model->getlatesttoken();

        print_r($arr_laz);

        if(!empty($arr_laz)){

            $this->global_arr=$arr_laz;

            if($this->global_arr->litetime < 10){
            
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
   
        }
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
        //$request->addApiParam('trans_type','-1');
        

        $start_time="2022-10-07";
        $end_time="2022-10-07";

       // $start_time = date(DATE_ISO8601, strtotime($start_time));
       // $end_time = date(DATE_ISO8601, strtotime($end_time));
      //  echo $start_time."<br>";

        $offset=0;
        
        $request->addApiParam('start_time', $start_time);  
        $request->addApiParam('end_time', $end_time);  
        $request->addApiParam('trade_order_id',strval("589989289940946"));
        $request->addApiParam('limit','50');
       // $request->addApiParam('offset',$offset);
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

function chk_date_getOrders(){

    $arr_date = $this->lazada_orders_model->select_chk_date();
   // echo $arr_date['cntdate'];
    if(!empty($arr_date)){
        if($arr_date['cntdate'] < 1){
            //Get Now Data
             $this->getOrders();
        }else{
            //Get Old Data
             $this->getOldOrders();
        }
    }
}
//getorder>>here
public function getOrders()
     {

        //$chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');


      $arr_latest_order_date=$this->lazada_orders_model->get_next_download_time_last_order();    
     // print_r($arr_latest_order_date);
      $arr_all_order = $this->lazada_orders_model->select_cnt_all();
        
        if($arr_all_order['cnt_orders'] == 0){
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

     // echo $last_order_by_date['created_at']."<br>";

      echo $arr_all_order['cnt_orders'].">>".$latest_order_date;
      
         include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
            //echo $this->session->userdata("accesstoken");
        

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/orders/get','GET');
        $skipfirstrow=TRUE;

        if($latest_order_date == 1)
        {
          echo "here:".BNY_ESTABLISHDATE;  
            $request->addApiParam('created_after', date(DATE_ISO8601, strtotime(BNY_ESTABLISHDATE)));
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
            $skipfirstrow=FALSE;
        }

        $request->addApiParam('sort_direction','ASC');
        $request->addApiParam('offset','0');
        $request->addApiParam('limit','50');
        $request->addApiParam('sort_by','created_at');
        //$orders=$c->execute($request,$accesstoken);
        $orders=$c->execute($request,$this->accesstoken);
        

        $datas = json_decode($orders,true);

        print_r($datas);

         if($datas["code"]==0)
          {

            if (array_key_exists("data",$datas))
            {

            if(count($datas["data"]["orders"])==0)
            {
                echo "done";

            }
            else //if(count($datas["data"]["orders"])==0)
            {
               $rowcount=0;
               $log_code = $this->random_util->create_random_number(8);
               //print_r($datas["data"]["orders"]);

               $data_insert = array();
               $data_chk_insert = array();
               $num_data = 0;
               $keygen = $this->random_util->create_random_number(8);

              foreach($datas["data"]["orders"] as $row)
                {
                
                

                $array_status_death = array();
                $array_status_not_death = array();

                $status_order= $this->array_util->getlastElement($row["statuses"]);

                $val_order_chk =  $row["order_number"]."_".$status_order;   
                if($rowcount>0 || !$skipfirstrow)
                    {
                        $num_data = $num_data +1;

                        //chk order status death
                        $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed');
                        $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship','packed');

                        $order_death = true;
                        if (in_array($status_order, $array_status_not_death)){
                            $order_death = false;
                        }

                        if($order_death){
                            echo "<br>1->Order Death!!<br>";
                            echo $status_order."<br>";
                            echo intval($row["order_number"])."<br>";
                            $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      
                            
                             $data_update_at = $row["updated_at"];
                            if($status_order == 'canceled'){
                                $data_update_at = $row["created_at"];
                            }
                            $data1=array(
                                'order_number'=>$row["order_number"],
                                //'taxinvoiceID' => '',
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
                                'status'=>$status_order,
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($data_update_at),
                               // 'keygen' => $keygen

                            );   

                            // dup shippet
                            $data_dup=array(
                                'order_number'=>$row["order_number"],
                               // 'taxinvoiceID' => '',
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
                                'status'=>'packed',
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                               // 'keygen' => $keygen

                            );     

                           if(!is_null($row["order_number"])){

                                $arr_data_chk = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$status_order);
                                if(empty($arr_data_chk)){

                                    if (!in_array($val_order_chk, $data_chk_insert)){
                                        array_push($data_insert,$data1);
                                    }
                                   // array_push($data_insert,$data_dup);
                                    $data1 = array();

                                    // check if real cencle

                                    /*$make_cn =   $this->get_orertrace($row["order_number"]);
                                    if($make_cn){
                                        if (!in_array($val_order_chk, $data_chk_insert)){
                                            array_push($data_insert,$data_dup);
                                        }
                                        $data_dup = array();
                                    }*/

                                    $data_packed_dup = $this->chk_for_insert_packed($row,$status_order,$tax_code);
                                    if(!empty($data_packed_dup)){
                                        if (!in_array($val_order_chk, $data_chk_insert)){
                                            array_push($data_insert,$data_packed_dup);
                                        }
                                    }

                                }

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
                        }else{//if($order_death){  //chk order status not death
                            echo "<br>3->order not death<br>";
                            echo $status_order."<br>";
                            echo intval($row["order_number"])."<br>";
                            $arr_data_not_death = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$status_order);
                            if(empty($arr_data_not_death)){
                                $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                                    //------End Get New LAZ Order Number------------      

                                    $data3=array(
                                        'order_number'=>$row["order_number"],
                                      //  'taxinvoiceID' => '0',
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
                                        'status'=>$status_order,
                                        'delivery_info'=>$row["delivery_info"],
                                        'updated_at'=>$this->common_util->getDbDate($row["updated_at"]),
                                        //'keygen' => $keygen

                                    );     

                                   $data_packed_dup = $this->chk_for_insert_packed($row,$status_order,$tax_code);
                                   if(!empty($data_packed_dup)){
                                        if (!in_array($val_order_chk, $data_chk_insert)){
                                            array_push($data_insert,$data_packed_dup);
                                        }
                                    }

                                   if(!is_null($row["order_number"])){
                                        if (!in_array($val_order_chk, $data_chk_insert)){
                                            array_push($data_insert,$data3);
                                        }
                                        $data3 = array();
                                        
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
                            }//if(empty($arr_data_not_death)){
                        }//}else{//if($order_death){
                    }//if($rowcount>0 || !$skipfirstrow)

                    
                    array_push($data_chk_insert,$val_order_chk);

                }//foreach($datas["data"]["orders"] as $row)
                   $cnt_data = count($data_insert);
                   echo "<br>".$num_data."<>".$cnt_data;
                   if($cnt_data > 0){

                    echo "<---insert all---->";
                    //$this->lazada_orders_model->insert_batch($data_insert);

                    $sql_insert_batch = $this->db_util->insert_batch('lazada_orders',$data_insert);
                    echo $sql_insert_batch;
                    $this->free_model->query_run($sql_insert_batch);


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
            }// else //if(count($datas["data"]["orders"])==0)

            }//(array_key_exists("orders",$datas['data']))

        }//if($datas["code"]==0)
    }

    function test_diff(){
        $date_diff_status = $this->date_util->date_diff('2023-03-28 10:41:18.000','2023-03-28 12:04:11.000');
        echo $date_diff_status;
    }

    function chk_for_insert_packed($row,$status_order,$tax_code){

        $date_diff_status = $this->date_util->date_diff($row["created_at"],$row["updated_at"]);
        $array_status_chk = array('damaged_by_3pl', 'delivered', 'failed_delivery', 'lost_by_3pl','ready_to_ship','ready_to_ship_pending','returned','shipped','shipped_back','shipped_back_success');
        $data_packed_dup = "";
        //if (in_array($status_order, $array_status_chk)){
        if($status_order != "canceled"){
            $arr_chk_packed = $this->lazada_orders_model->get_by_sn_status($row["order_number"],'packed');
            if(empty($arr_chk_packed)){
                $data_packed_dup=array(
                    'order_number'=>$row["order_number"],
                   // 'taxinvoiceID' => '',
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
                    'status'=>'packed',
                    'delivery_info'=>$row["delivery_info"],
                    'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                   // 'keygen' => $keygen

                ); 
            }
        }
       // }

        if($status_order == "canceled"){
            //1800 = 30 hour

            //$hour_cn = $this->check_date_holiday($row["created_at"]);

            //if($date_diff_status > $hour_cn){
                $arr_chk_packed = $this->lazada_orders_model->get_by_sn_status($row["order_number"],'packed');
                if(empty($arr_chk_packed)){
                    $data_packed_dup=array(
                        'order_number'=>$row["order_number"],
                       // 'taxinvoiceID' => '',
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
                        'status'=>'packed',
                        'delivery_info'=>$row["delivery_info"],
                        'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                       // 'keygen' => $keygen

                    ); 
                }
            //} //if($date_diff_status > $hour_cn){

        }

        return $data_packed_dup;

    }

    public function check_date_holiday($order_datetime){

        //$order_datetime = '2024-07-02 15:31';
        $explode_day = explode(" ",$order_datetime);
        $order_date = $explode_day[0];
        $order_time = $explode_day[1];
        $order_datetime_use = $order_date." ".$order_time;

        $time_re = 1800;

        $arr_holiday = $this->lazada_orders_model->get_time_holiday($order_datetime_use,BNY_LAZ_CUT_ORDER_TIME,'2025');

        if(!empty($arr_holiday)){

            $min_to_add = $arr_holiday ['min_to_add']*1440;

            $time_re = $time_re + $min_to_add;

        }else{

            //check if next date is Sunday

        

            $order_next_day = date('Y-m-d', strtotime($order_datetime_use. ' + 1 days'));
            $next_date_name = date("D", strtotime($order_next_day));

            echo "next date name >> ".$next_date_name."<br>";

            if($next_date_name == "Sun"){

                $order_cut_datetime = $order_date." ".BNY_LAZ_CUT_ORDER_TIME;
                $start = new DateTime($order_cut_datetime);
                $end = new DateTime($order_datetime_use);

                $diff_in_seconds = $end->getTimestamp() - $start->getTimestamp();
                $minutes = floor($diff_in_seconds / 60);

                if($minutes > 0){
                    $time_re = $time_re + 1440;
                }
            }
        }

        echo $time_re;

    }

public function getOrders_v1()
     {

        //$chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');


      $arr_latest_order_date=$this->lazada_orders_model->get_next_download_time();    
      print_r($arr_latest_order_date);
      $arr_all_order = $this->lazada_orders_model->select_cnt_all();

        
        if($arr_all_order['cnt_orders'] == 0){
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

     // echo $last_order_by_date['created_at']."<br>";

      echo $arr_all_order['cnt_orders'].">>".$latest_order_date;
      
         include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
            //echo $this->session->userdata("accesstoken");
        

        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/orders/get','GET');
        $skipfirstrow=TRUE;

        if($latest_order_date == 1)
        {
          echo "here:".BNY_ESTABLISHDATE;  
            $request->addApiParam('created_after', date(DATE_ISO8601, strtotime(BNY_ESTABLISHDATE)));
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
        $request->addApiParam('limit','50');
        $request->addApiParam('sort_by','created_at');
        //$orders=$c->execute($request,$accesstoken);
        $orders=$c->execute($request,$this->accesstoken);
        

        $datas = json_decode($orders,true);

        //print_r($datas);

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

          /* if(!$this->lazada_order_status_checker_ready_to_insert($datas))
           {

            $data=array(
               'is_laz_orders_busy'=>0
            );

            $this->api_prepare_data_model->update_by_shopid($data,'123456');

            exit();
           }*/

           //echo "data all clear";

          foreach($datas["data"]["orders"] as $row)
            {
                $array_status_death = array();
                $array_status_not_death = array();

                $status_order= $this->array_util->getlastElement($row["statuses"]);
                if($rowcount>0 || !$skipfirstrow)
                {
                    $num_data = $num_data +1;

                    //chk order status death
                    $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled');
                    $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship');

                    $order_death = true;
                    if (in_array($status_order, $array_status_not_death)){
                        $order_death = false;
                    }

                    if($order_death){

                       // $search_status = array_search($status_order, $array_status_death);
                        //unset($array_status_death[$search_status]);
                       //order death 
                        $arr_data_death = $this->lazada_orders_model->get_by_not_status($row["order_number"],$status_order);

                        if(empty($arr_data_death)){
                            //insert order death archive
                            echo "<br>1->Order Death!!<br>";
                            echo $status_order."<br>";
                            echo intval($row["order_number"])."<br>";
                            $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      
                            
                             $data_update_at = $row["updated_at"];
                            if($status_order == 'canceled'){
                                $data_update_at = $row["created_at"];
                            }
                            $data1=array(
                                'order_number'=>$row["order_number"],
                                //'taxinvoiceID' => '',
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
                                'status'=>$status_order,
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($data_update_at),
                               // 'keygen' => $keygen

                            );   

                            // dup shippet
                            $data_dup=array(
                                'order_number'=>$row["order_number"],
                               // 'taxinvoiceID' => '',
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
                               // 'keygen' => $keygen

                            );     

                           if(!is_null($row["order_number"])){

                                $arr_data_chk = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$status_order);
                                if(empty($arr_data_chk)){
                                    array_push($data_insert,$data1);
                                   // array_push($data_insert,$data_dup);
                                    $data1 = array();

                                    // check if real cencle
                                    $make_cn =   $this->get_orertrace($row["order_number"]);
                                    if($make_cn){
                                        array_push($data_insert,$data_dup);
                                        $data_dup = array();
                                    }
                                }

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
                       }else{//if(empty($arr_data_death)){
                        //order not death
                        echo "<br>2->order death not death<br>";
                        echo $status_order."<br>";
                        echo intval($row["order_number"])."<br>";
                        $arr_data_not_death = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$status_order);
                        //print_r($arr_data_not_death);
                        if(empty($arr_data_not_death)){
                            $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                                //------End Get New LAZ Order Number------------      

                                $data2=array(
                                    'order_number'=> intval($row["order_number"]),
                                   // 'taxinvoiceID' => '',
                                    'created_at'=>$this->common_util->getDbDate($row["created_at"]),
                                    'shipping_fee_original'=>$this->common_util->prep_float($row["shipping_fee_original"]),
                                    'shipping_fee_discount_platform'=>$this->common_util->prep_float($row["shipping_fee_discount_platform"]),
                                    'shipping_fee_discount_seller'=>$this->common_util->prep_float($row["shipping_fee_discount_seller"]),
                                    'shipping_fee'=>$this->common_util->prep_float($row["shipping_fee"]),
                                    'voucher_platform'=>$this->common_util->prep_float($row["voucher_platform"]),
                                    'voucher_seller'=>$this->common_util->prep_float($row["voucher_seller"]),
                                    'voucher'=>$this->common_util->prep_float($row["voucher"]),
                                    'price'=>$this->common_util->prep_float($row["price"]),
                                    'tax_code'=>strval($tax_code),
                                    'status'=>$status_order,
                                    'delivery_info'=>strval($row["delivery_info"]),
                                    'updated_at'=>$this->common_util->getDbDate($row["updated_at"]),
                                    //'keygen' => (string)$keygen

                                );      

                               if(!is_null($row["order_number"])){

                                        array_push($data_insert,$data2);
                                        $data2 = array();
                                        print_r($data_insert);
                                    
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

                   }else{
                    //order not death
                    echo "<br>3->order not death<br>";
                    echo $status_order."<br>";
                    echo intval($row["order_number"])."<br>";
                    $arr_data_not_death = $this->lazada_orders_model->get_by_sn_status($row["order_number"],$status_order);
                    if(empty($arr_data_not_death)){
                        $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      

                            $data3=array(
                                'order_number'=>$row["order_number"],
                              //  'taxinvoiceID' => '0',
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
                                'status'=>$status_order,
                                'delivery_info'=>$row["delivery_info"],
                                'updated_at'=>$this->common_util->getDbDate($row["updated_at"]),
                                //'keygen' => $keygen

                            );      

                           if(!is_null($row["order_number"])){

                                    array_push($data_insert,$data3);
                                    $data3 = array();
                                
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

      // print_r($data_insert);
       $cnt_data = count($data_insert);
       echo "<br>".$num_data."<>".$cnt_data;
       if($cnt_data > 0){

        echo "<---insert all---->";
        //$this->lazada_orders_model->insert_batch($data_insert);

        $sql_insert_batch = $this->db_util->insert_batch('lazada_orders',$data_insert);
        echo $sql_insert_batch;
        $this->free_model->query_run($sql_insert_batch);


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

     function test_insert_all(){
        $data = array(
                    array(
                        'order_number' => '479001827624400',
                        'taxinvoiceID' => '',
                        'created_at' => '2021-12-12 07:19:19',
                        'shipping_fee_original' => 44 ,
                        'shipping_fee_discount_platform' => 40 ,
                        'shipping_fee_discount_seller' => 0 ,
                        'shipping_fee' => 4 ,
                        'voucher_platform' => 0 ,
                        'voucher_seller' => 0 ,
                        'voucher' => 0 ,
                        'price' => 104.17 ,
                        'tax_code' => '',
                        'status' => 'delivered' ,
                        'delivery_info' => '',
                        'updated_at' => '2021-12-17 14:50:02' ,
                        'keygen' => '76266767',
                    ),
                    array(
                        'order_number' => '478938082098550' ,
                        'taxinvoiceID' => '',
                        'created_at' => '2021-12-12 07:26:10' ,
                        'shipping_fee_original' => 64 ,
                        'shipping_fee_discount_platform' => 40 ,
                        'shipping_fee_discount_seller' => 0 ,
                        'shipping_fee' => 24 ,
                        'voucher_platform' => 0 ,
                        'voucher_seller' => 0 ,
                        'voucher' => 0 ,
                        'price' => 189.23 ,
                        'tax_code' => '',
                        'status' => 'canceled' ,
                        'delivery_info' => '',
                        'updated_at' => '2021-12-12 07:26:10' ,
                        'keygen' => '76266767',
                    )
        );
        print_r($data);

        $this->lazada_orders_model->insert_batch($data);
     }

     public function getOrders_bk()
     {

        //$chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');


      $arr_latest_order_date=$this->lazada_orders_model->get_next_download_time();    
      //print_r($arr_latest_order_date);
      $arr_all_order = $this->lazada_orders_model->select_cnt_all();

        
        if($arr_all_order['cnt_orders'] == 0){
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

      

      echo $arr_all_order['cnt_orders'].">>".$latest_order_date;
      
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

        //print_r($datas);

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
                $status_order= $this->array_util->getlastElement($row["statuses"]);
                if($rowcount>0 || !$skipfirstrow)
                {
                    $num_data = $num_data +1;

                    //chk order status death
                    $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled');
                    $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship');

                    $order_death = true;
                    if (in_array($status_order, $array_status_not_death)){
                        $order_death = false;
                    }

                    if($order_death){
                       //order death 
                        $arr_data_death = $this->lazada_orders_model->get_by_status_in($row["order_number"],$array_status_death);
                        if(empty($arr_data_death)){
                            //insert order death 
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
                                'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                                'keygen' => $keygen

                            );   

                           if(!is_null($row["order_number"])){

                                array_push($data_insert,$data);

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

                           if(!is_null($row["order_number"])){

                                array_push($data_insert,$data);
                                
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

public function getOldOrders()
     {

        //$chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');


      $arr_latest_order_date=$this->lazada_orders_model->get_next_download_time();    
      //print_r($arr_latest_order_date);
      $arr_all_order = $this->lazada_orders_model->select_cnt_all();

      //echo $arr_all_order['cnt_orders'];
        
        if($arr_all_order['cnt_orders'] == 0){
            //empty order data
            $latest_order_date = 1;
        }else{
            if(is_null($arr_latest_order_date['last_cdate'])){
                //no next time
                $latest_order_date = 2;
                $last_order_by_date = $this->lazada_orders_model->next_order_by_date();
                echo $last_order_by_date['created_at'];

              }else{
                //next time
                $latest_order_date = 3;
              }
        }

      

      echo $arr_all_order['cnt_orders'].">>".$latest_order_date;
      
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

        //print_r($datas);

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
                $status_order= $this->array_util->getlastElement($row["statuses"]);
                if($rowcount>0 || !$skipfirstrow)
                {
 
                    $num_data = $num_data +1;

                    //chk order status death
                    $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled');
                    $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship');

                    $order_death = true;
                    if (in_array($status_order, $array_status_not_death)){
                        $order_death = false;
                    }

                    //echo $order_death;

                    if($order_death){
                       //order death 
                        //echo "death>>";
                        $arr_data_death = $this->lazada_orders_model->get_by_status_in($row["order_number"],$array_status_death);
                        if(empty($arr_data_death)){
                            //insert order death 
                            $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$row);
                            //------End Get New LAZ Order Number------------      
                            $data_update_at = $row["updated_at"];
                            if($status_order == 'canceled'){
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
                                // check if real cencle
                              $make_cn =   $this->get_orertrace($row["order_number"]);
                                if($make_cn){
                                    array_push($data_insert,$data_dup);
                                }

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

public function getOrders_bk_v2($monthyeardate_to_fix=NULL)
     {



        $chk_is_busy = $this->api_prepare_data_model->select_by_shopid('123456');

      if($chk_is_busy['is_laz_orders_busy']==1)
      {
        $datalog = array(
                    'log_type' => 1,
                    'log_code' => '',
                    'log_note' => 'orders insertion attemp to do on a busy status',
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);


                $data=array(
           'is_laz_orders_busy'=>0
        );

        $this->api_prepare_data_model->update_by_shopid($data,'123456');

        exit('process busy');
      }
      else
      {
        $data=array(
           'is_laz_orders_busy'=>1
        );

        $this->api_prepare_data_model->update_by_shopid($data,'123456');

      }

      //1. select latest order in db

  
if(is_null($monthyeardate_to_fix))
        {
      $latest_order_date=$this->lazada_orders_model->select_latest_record();      
        }
      else
      {
        if($this->lazada_orders_model->select_count_record_by_month_yesr($monthyeardate_to_fix)==0)
        {
           $latest_order_date=$this->date_util->getFirstDateofMonth($monthyeardate_to_fix);
        }
        else
        {
        $latest_order_date=$this->lazada_orders_model->select_latest_record_by_month_year($monthyeardate_to_fix);      
        }

      }

      print_r($latest_order_date);
      
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

        if(! is_null($monthyeardate_to_fix))
        {
        $request->addApiParam('created_before', date(DATE_ISO8601, strtotime($this->date_util->getnextmonth_firstdate($latest_order_date))));  
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

                    if($this->array_util->getlastElement($row["statuses"])!='canceled')
                    {

                        //unpaid, pending, canceled, ready_to_ship, delivered, returned, shipped and failed
        
                //$existing_rows=$this->count_order();

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
            //if($this->array_util->getlastElement($row["statuses"])=='delivered')
            //{
              /*  if($num_data == 0){   //fix invoiceid

                   $arr_lastorder = $this->lazada_orders_model->last_order();   
                   $new_textinvoiceID = $this->lazapi->get_laz_code($arr_lastorder['taxinvoiceID'],$row["created_at"]);    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->lazapi->get_laz_code($last_id,$row["created_at"]);  
                    $last_id = $new_textinvoiceID;

                } 
                */
           // }  
           // else
           // {
           //     $new_textinvoiceID="";
           // } 

            //------End Get New LAZ Order Number------------      

        	$data=array('order_number'=>$row["order_number"],
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
            else
            {

                echo "<br>Cancle: ".$row["order_number"];
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
        $request2->addApiParam('order_id','247258539914760');
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);


 
        

        var_dump($datas2);

       

     }

  //474256512073241

     public function test_order_trace()
     {




         include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/logistic/order/trace','GET');
        $request2->addApiParam('order_id','778978684843079');
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas = json_decode($items2,true);

         $make_cn = true;
         print_r($datas);

        if(isset($datas['result']['module'][0]['package_detail_info_list'][0])){
          $cnt = count($datas['result']['module'][0]['package_detail_info_list'][0]);
         // print_r($datas['result']['module'][0]['package_detail_info_list'][0]);
         // echo "cnt>>".$cnt;
          if($cnt == 0){
            $make_cn = false;
          }else{

            $search_info = $this->searchArray('100100',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);
            print_r($datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list'][0]);

            if($search_info){
               $make_cn = true;
               echo "found cn true";
            }else{
                echo "not found cn false";
              $make_cn = false;
            }

          }
        }else{
          $make_cn = false;
        }

        if($make_cn){
            echo "Make CN";
        }else{
            echo "No Make CN";
        }


     }
     
     public function get_orertrace($order_number)
     {

        
        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/logistic/order/trace','GET');
        $request2->addApiParam('order_id',$order_number);
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas = json_decode($items2,true);

        $make_cn = true;

        if(isset($datas['result']['module'][0]['package_detail_info_list'][0])){

              $cnt = count($datas['result']['module'][0]['package_detail_info_list'][0]);
             // print_r($datas['result']['module'][0]['package_detail_info_list'][0]);
             // echo "cnt>>".$cnt;
              if($cnt == 0){
                $make_cn = false;
              }else{

                $search_info100 = $this->searchArray('100100',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                $search_info101 = $this->searchArray('100101',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                $search_info102 = $this->searchArray('100102',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                if(($search_info100)or($search_info101)or($search_info102)){
                    
                   $make_cn = true;
                }else{
                    
                  $make_cn = false;
                }

              }
        
        }else{
          $make_cn = false;
        }
       
        return $make_cn;
     }

     function searchArray($valsearch, $array) {
       foreach ($array as $key => $val) {
           if ($val['status_code'] === $valsearch) {
            //echo "found cn";
               return true;
           }
       }
       return false;
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

                //$this->lazada_orderitems_model->insert_all($data_insert);
                $sql_insert_batch = $this->db_util->insert_batch('lazada_orderitems',$data_insert);
                $this->free_model->query_run($sql_insert_batch);

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

    function get_order_more(){
        $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed');
        $array_status_not_death = array('unpaid', 'pending', 'ready_to_ship', 'shipped','topack','toship','packed');
        $arr_orders = $this->lazada_orders_model->get_oeder_not_die();
        print_r($arr_orders);
        $num = 0;
        if(!empty($arr_orders)){
            foreach($arr_orders as $arr_order){
                $updated_at = $arr_order['updated_at'];
                    if($arr_order['status'] == 'ready_to_ship'){

                        if($arr_order['date_to_now'] > 10){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }

                    }elseif($arr_order['status'] == 'unpaid'){

                        //echo $arr_order['date_to_now']."<br>";
                        if($arr_order['date_to_now'] > 10){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }
                        
                    }elseif($arr_order['status'] == 'pending'){

                        //echo $arr_order['date_to_now']."<br>";
                        if($arr_order['date_to_now'] > 10){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }
                        
                    }elseif($arr_order['status'] == 'packed'){

                        //echo $arr_order['date_to_now']."<br>";
                        if($arr_order['date_to_now'] > 10){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }
                        
                    }elseif($arr_order['status'] == 'shipped'){

                        //echo $arr_order['date_to_now']."<br>";
                        if($arr_order['date_to_now'] > 1440){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }
                        
                    }else{

                        if($arr_order['date_to_now'] > 1440){
                            $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                            $num = $num +1;
                        }
                    }
                    if($num == 50){
                      break;
                    }
            }
        }


    }

    function get_order_more_v2(){
        
        $arr_stp_orders = $this->lazada_orders_model->get_oeder_ready_to_ship();
        //print_r($arr_orders);
        foreach($arr_stp_orders as $arr_stp_order){
            $updated_stp_at = $arr_stp_order['updated_at'];
                if($arr_stp_order['status'] == 'ready_to_ship'){

                    if($arr_stp_order['date_to_now'] > 5){
                        $data = $this->getOrder_insert_orderno($arr_stp_order['order_number'],$arr_stp_order['status']);
                    }

                }
        }


        $arr_orders = $this->lazada_orders_model->get_oeder_not_die();
        //print_r($arr_orders);
        foreach($arr_orders as $arr_order){
            $updated_at = $arr_order['updated_at'];
                if($arr_order['status'] == 'shipped'){

                    //echo $arr_order['date_to_now']."<br>";
                    if($arr_order['date_to_now'] > 60){
                        $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                    }
                    
                }else{

                    if($arr_order['date_to_now'] > 60){
                        $data = $this->getOrder_insert_orderno($arr_order['order_number'],$arr_order['status']);
                    }
                }
        }


    }

    public function chk_order_die($orderno){

        $arr_order = $this->lazada_orders_model->get_by_status_order_date($orderno);
        $order_death = false;
        if(!empty($arr_order)){
            $last_status = $arr_order['status'];
            $array_status_death = array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled');

            if (in_array($last_status, $array_status_death)){
                $order_death = true;
            }
        }

        return $order_death;

    }

    public function getOrder_insert_orderno($orderno,$status)
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/order/get','GET');
        $request2->addApiParam('order_id',$orderno);
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas = json_decode($items2,true);

        //var_dump($datas2);

       // print_r($datas["data"]);

            if(isset($datas["data"]))
            {
                $status_order = $this->array_util->getlastElement($datas["data"]["statuses"]);
                $arr_data_chk = $this->lazada_orders_model->get_by_sn_status($datas["data"]["order_number"],$status_order);

                if(empty($arr_data_chk)){
                    $tax_code=$this->array_util->if_elemenmt_exist("tax_code",$datas["data"]);
                    $data_insert=array(
                        'order_number'=>$datas["data"]["order_number"],
                        //'taxinvoiceID' => '',
                        'created_at'=>$this->common_util->getDbDate($datas["data"]["created_at"]),
                        'shipping_fee_original'=>$this->common_util->prep_float($datas["data"]["shipping_fee_original"]),
                        'shipping_fee_discount_platform'=>$this->common_util->prep_float($datas["data"]["shipping_fee_discount_platform"]),
                        'shipping_fee_discount_seller'=>$this->common_util->prep_float($datas["data"]["shipping_fee_discount_seller"]),
                        'shipping_fee'=>$this->common_util->prep_float($datas["data"]["shipping_fee"]),
                        'voucher_platform'=>0,
                        'voucher_seller'=>0,
                        'voucher'=>$this->common_util->prep_float($datas["data"]["voucher"]),
                        'price'=>$this->common_util->prep_float($datas["data"]["price"]),
                        'tax_code'=>$tax_code,
                        'status'=>$status_order,
                        'delivery_info'=>$datas["data"]["delivery_info"],
                        'updated_at'=>$this->common_util->getDbDate($datas["data"]["updated_at"]),
                       // 'keygen' => $keygen

                    );  

                    $this->lazada_orders_model->insert($data_insert);
                    print_r($data_insert);

                }else{
                    $data_up = array(
                        'updated_at' => DATE_TIME_NOW
                    );
                    $this->lazada_orders_model->update_order_status($datas["data"]["order_number"],$status,$data_up);
                    //print_r($data_up);
                }


            }


     }

     public function getOrder_orderno($orderno)
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/order/get','GET');
        $request2->addApiParam('order_id',$orderno);
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);

        //var_dump($datas2);

        if($datas2["code"]==0){

            if(isset($datas["data"]))
            {
                $data = $datas["data"];

            }

        }

        return $data;

     }

    public function get_tranctiondetail()
    {
       //we check <<<

        $data_api_prepare = $this->api_prepare_data_model->select_by_shopid('123456','lazada_finance_transaction_details');
        //print_r($data_api_prepare);
        if(!empty($data_api_prepare)){


            $start_time = $data_api_prepare['start'];
            $next_day = $data_api_prepare['next_day'];

            //$end_time = $data_api_prepare['stop'];
            $offset = $data_api_prepare['offset'];
            $cnt_date = $data_api_prepare['cnt_date'];

        $today = date('Y-m-d');

        echo $start_time."--".$today;
        if($start_time != $today){
        
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
           // print_r($datas);

            $arr_datainsert = array();

            if(count($datas["data"]) == 0){ //no more data to download from this day

                $data_preapre_update  = array(
                    'action_date' => $next_day,
                    'offset' => 0,
                    'cnt_date' => 0,
                    'is_complete' => 1
                ); 
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456','lazada_finance_transaction_details');

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
                $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456','lazada_finance_transaction_details');


                foreach($datas["data"] as $data){
                    $arr_chk_data = $this->lazada_finance_transaction_details_model->select_by_status_fee_amount($data['order_no'],$data['fee_name'],$data['amount'],$data['paid_status']);

                    if(empty($arr_chk_data)){

                        $transaction_date = date_create($data['transaction_date']);
                        $transaction_date = date_format($transaction_date,"Y-m-d");

                        $date_close = "";
                        if($data['paid_status'] == "Paid"){
                            $date_close = DATE_TIME_NOW;
                        }

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
                            'date_close' => $date_close,
                            'keygen' => $keygen

                        );

                        $this->lazada_finance_transaction_details_model->insert($data_insert);
                    }

                }


            } // if count($datas["data"]) == 0

        }else{
            echo "Today";
        } //if($start_time == $today){

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

    public function get_tranctiondetail_tmp(){

        $data_api_prepare = $this->api_prepare_data_model->select_by_shopid('123456','lazada_finance_transaction_details_tmp');

        //print_r($data_api_prepare);
        
        if(!empty($data_api_prepare)){

            if($data_api_prepare['action_date'] == "1900-01-01 00:00:00.000"){
                $data_up = array(
                    'action_date' => DATE_NOW,
                    'offset' => 0,
                    'is_complete' => 0
                );

                $this->api_prepare_data_model->update_by_shopid($data_up,'123456','lazada_finance_transaction_details_tmp');

                $data_api_prepare = $this->api_prepare_data_model->select_by_shopid('123456','lazada_finance_transaction_details_tmp');
            }

            $stop_day = $data_api_prepare['start'];
            $start_day = $data_api_prepare['last_day'];
            $offset = $data_api_prepare['offset'];


            if(($data_api_prepare['difftoday'] == 0 ) and ($data_api_prepare['is_complete'] == 0 )){
                // continul call api 

                include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

                $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
                $request = new LazopRequest('/finance/transaction/detail/get','GET');
                $request->addApiParam('trans_type','-1');

                $request->addApiParam('start_time', $start_day);  
                $request->addApiParam('end_time', $stop_day);  
                $request->addApiParam('limit','300');
                $request->addApiParam('offset',$offset);
                $orders=$c->execute($request,$this->accesstoken);
                
                echo $start_day ."--". $stop_day;

                $datas = json_decode($orders,true);
               // print_r($datas);

                $arr_datainsert = array();

                if(count($datas["data"]) == 0){ 

                    $data_up = array(
                        'offset' => 0,
                        'is_complete' => 1
                    );

                    $this->api_prepare_data_model->update_by_shopid($data_up,'123456','lazada_finance_transaction_details_tmp');

                }else{


                    $log_code = $this->random_util->create_random_number(8);
                    $keygen = $this->random_util->create_random_number(8);

                    $data_preapre_update  = array(

                        'offset' => $offset+300,

                    ); 
                    $this->api_prepare_data_model->update_by_shopid($data_preapre_update,'123456','lazada_finance_transaction_details_tmp');

                    foreach($datas["data"] as $data){

                        $arr_chk_data = $this->lazada_finance_transaction_details_model->select_by_status_fee_amount($data['order_no'],$data['fee_name'],$data['amount'],$data['paid_status']);

                        if(empty($arr_chk_data)){
                            $date_close = "";
                            if($data['paid_status'] == "Paid"){
                                $date_close = DATE_TIME_NOW;
                            }

                            $data_insert = array(
                                'order_number' => $data['order_no'],
                                'transaction_date' => $this->common_util->getDbDate($data['transaction_date']),
                                'amount' => $this->common_util->prep_float($data['amount']),
                                'paid_status' => $data['paid_status'],
                                'shipping_provider' => $data['shipping_provider'],
                                'WHT_included_in_amount' => $data['WHT_included_in_amount'],
                                'payment_ref_id' => '',
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
                                'date_close' => $date_close,
                                'keygen' => $keygen,
                                'is_after_api' => 1

                            );

                            //print_r($data_insert);

                            $this->lazada_finance_transaction_details_model->insert($data_insert);
                        }

                    }

                }
                

            }else{
                if(($data_api_prepare['difftoday'] > 0 ) and ($data_api_prepare['is_complete'] == 1 )){

                    $data_up = array(
                        'action_date' => DATE_NOW,
                        'offset' => 0,
                        'is_complete' => 0
                    );

                    $this->api_prepare_data_model->update_by_shopid($data_up,'123456','lazada_finance_transaction_details_tmp');

                }
            }
        }

    }

    public function get_tranctiondetail_more(){



        //echo $start_time."--".$today;
        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/finance/transaction/detail/get','GET');

        $arr_ordernumbers = $this->lazada_finance_transaction_details_model->get_orderno_nopaid(10,30);

        if(!empty($arr_ordernumbers)){

            foreach($arr_ordernumbers as $arr_ordernumber){

                $arr_chk_paid= $this->lazada_finance_transaction_details_model->select_by_orderno_paid($arr_ordernumber['order_number']);

                if(empty($arr_chk_paid)){ // order no paid

                    $arr_tran_last_nopaid = $this->lazada_finance_transaction_details_model->select_by_orderno_no_paid($arr_ordernumber['order_number']);

                    $today = date('Y-m-d');
                    $datestart = $arr_tran_last_nopaid['api_date'];

                    if($arr_tran_last_nopaid['api_date'] == ""){

                        $datestart = DATE_NOW;

                        $arr_data_date = array(
                            'api_date' => DATE_NOW
                        );
                        $this->lazada_finance_transaction_details_model->update($arr_data_date,$arr_tran_last_nopaid['TransactionID']);
                    }

                    $datestart = strtotime($datestart);
                    $today = strtotime($today);
                    $datediff = $today - $datestart;

                    $diff_date = round($datediff / (60 * 60 * 24));

                   // echo $today.">>>".$datestart."<br>";
                   // echo $arr_tran_last_nopaid['TransactionID']."-".$diff_date."<br>";

                    if($diff_date == $arr_tran_last_nopaid['api_round']){
                        //call api

                        $api_round = $arr_tran_last_nopaid['api_round'] + 1;
                        $data_round = array(
                            'api_round' => $api_round
                        );


                        $this->lazada_finance_transaction_details_model->update_order_number($data_round,$arr_tran_last_nopaid['order_number']);
                    }

                }    //if(empty($arr_tran_last_nopaid)){

            }
        }//if(!empty($arr_tran_last_nopaids)){



    }

    public function get_tranctiondetail_more_bk(){

        $today = date('Y-m-d');

        //echo $start_time."--".$today;
        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';
        $c = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request = new LazopRequest('/finance/transaction/detail/get','GET');

        $arr_tran_last_nopaid = $this->lazada_finance_transaction_details_model->get_last_nopaid(10);

        if(!empty($arr_tran_last_nopaid)){

            if($arr_tran_last_nopaid['start'] != $today){

            $start_time=$arr_tran_last_nopaid['start'];
            $end_time=$arr_tran_last_nopaid['start'];

            $request->addApiParam('start_time', $start_time);  
            $request->addApiParam('end_time', $end_time);  

            $orders=$c->execute($request,$this->accesstoken);
            $datas = json_decode($orders,true);

            //print_r($datas);
            $is_finish = 0;

            foreach($datas["data"] as $data){

                if(($data['order_no'] == $arr_tran_last_nopaid['order_number'])and($data['fee_name'] == $arr_tran_last_nopaid['fee_name'])){
                    if($data['paid_status'] == 'Paid'){

                        $is_finish = 1;
                        //echo $data['order_no'];
                        $keygen = $this->random_util->create_random_number(8);
                        $date_close = "";
                        if($data['paid_status'] == "Paid"){
                            $date_close = DATE_TIME_NOW;
                        }

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
                            'date_close' => $date_close,
                            'keygen' => $keygen

                        );
                        print_r($data_insert);

                       // $this->lazada_finance_transaction_details_model->insert($data_insert);

                        break;
                    }
                }

            }

            
            $round_plus = $arr_tran_last_nopaid['api_round'] +1;
            $data_up = array(
                'api_round' => $round_plus,
                'is_finish' => $is_finish
            );

           // $this->lazada_finance_transaction_details_model->update($data_up,$arr_tran_last_nopaid['TransactionID']);
            
            }
        }


    }

    public function get_payout_test()
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/finance/payout/status/get','GET');
        $request2->addApiParam('created_after', date(DATE_ISO8601, strtotime($yesterday)));
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);


 
        

        var_dump($datas2);

     }

     public function get_payout()
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $yesterday = date('Y-m-d',strtotime("-1 days"));

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/finance/payout/status/get','GET');
        $request2->addApiParam('created_after', date(DATE_ISO8601, strtotime($yesterday)));
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);

        //var_dump($datas2);

        if($datas2["code"]==0){


            $keygen = $this->random_util->create_random_number(8);


            foreach($datas2["data"] as $row){

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

                    $chk_dup = $this->lazada_finance_transaction_payout_model->get_by_number($row["statement_number"]);

                    if(empty($chk_dup)){
                        $this->lazada_finance_transaction_payout_model->insert($data);
                    }

                   // print_r($data);

            }
        }

     }

     public function get_payout_all()
     {



            include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

            $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
            $request2 = new LazopRequest('/finance/payout/status/get','GET');
            $request2->addApiParam('created_after', date(DATE_ISO8601, strtotime(BNY_ESTABLISHDATE)));
            $items2=$c2->execute($request2,$this->accesstoken);

            $datas2 = json_decode($items2,true);

            //var_dump($datas2);
            
            if($datas2["code"]==0){


            $keygen = $this->random_util->create_random_number(8);


            foreach($datas2["data"] as $row){

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

                    $this->lazada_finance_transaction_payout_model->insert($data);

            }
        }
     }


    function chk_report(){

        $arr_orders = $this->lazada_orders_model->get_orderno_by_date();

        foreach($arr_orders as $arr_order){

            $arr_order_datas = $this->lazada_orders_model->select_by_orderno($arr_order['order_number']);
            $cnt = count($arr_order_datas);
            if($cnt > 1){
                //echo $arr_order['order_number']."<br>";
                $arr_chk_status = $this->lazada_orders_model->get_by_sn_status($arr_order['order_number'],'packed');
                if(empty($arr_chk_status)){
                    echo "error>>>>".$arr_order['order_number'];
                }
            }
        }
    } 

    function chk_report_2(){

        $arr_orders = $this->lazada_orders_model->get_orderno_by_date_status();



        foreach($arr_orders as $arr_order){

            $arr_order_datas = $this->lazada_orders_model->select_by_orderno($arr_order['order_number']);
            $cnt = count($arr_order_datas);
            if($cnt > 1){
                //echo $arr_order['order_number']."<br>";
                $arr_chk_status = $this->lazada_orders_model->get_by_sn_status($arr_order['order_number'],'packed');
                if(empty($arr_chk_status)){
                    echo "error>>>>".$arr_order['order_number'];
                }
            }else{

                echo $arr_order['order_number']."<br>";
            }
        }
    } 


     public function getOrder_orderno_test()
     {

        

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/order/get','GET');
        $request2->addApiParam('order_id','834197382746708');
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas2 = json_decode($items2,true);

        print_r($datas2);



     }

     function import_order_no(){
        $this->load->view('import_lazada_no');
     }

     function lazada_import_action(){

        $file1_name = "";
        if (!empty($_FILES['upload_file1']['name'])) {
            $upload_file1 = 'upload_file1';
            $arr_file = $this->upload_bl->upload_file_xls($upload_file1);
            $file1_name = $arr_file['file_name'];
            $is_upload1 = $arr_file['is_upload'];

            //echo $file1_name;
        }

        if($is_upload1 == 1){
            $file_s = "./uploads/xls/".$file1_name;
            $mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if(in_array($_FILES['upload_file1']['type'],$mimes))
            {
                $this->load->library('Lib_excel');

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($file_s);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($file_s);
                } catch(Exception $e) {
                    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }

                $sheet = $objPHPExcel->getSheet(0); 
                $highestRow = $sheet->getHighestRow(); 
                $highestColumn = $sheet->getHighestColumn();
                $row_data = $highestRow-1;

                $order_sn_old = "";

                $data_order_all = array();
                $data_item_all = array();
                $num =0;
                $keygen = $this->random_util->create_random_number(8);

                $totol_price = 0;
                $totol_ship = 0;
                $price_ex = 0;
                $price_db = 0;
                $order_ex = "";

                for ($row = 2; $row <= $highestRow; $row++){ 
                    $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);
                $order_sn = $rowData[0][12];
                $paidPrice = $rowData[0][46];
                $shippingFee = $rowData[0][49];
                $status = $rowData[0][65];

                 //echo $order_sn."<br>";       
                $arr_order_datas = $this->lazada_orders_model->select_by_orderno_price($order_sn);

                    if(!empty($arr_order_datas)){

                        $cnt_or = count($arr_order_datas);

                        
                        //echo $num."<br>";

                        $a=array();
                        $price = 0;
                        $vs=0;
                        $price_t=0;
                       foreach($arr_order_datas as $arr_order_data){

                            array_push($a,$arr_order_data['status']);
                            if($arr_order_data['status'] == 'packed'){
                                $price_t = $arr_order_data['lprice'];
                            }

                       }

                       if($cnt_or > 2){
                        echo "<br>".$order_sn;
                        print_r($a);

                        //echo "<br>".$order_sn.">>>".$cnt_or.">>price::".$price_t."<br>";

                        }



                       if (!in_array("packed", $a))
                          {

                          if (!in_array("canceled", $a)){

                                //echo "here";
                                echo "<br>".$order_sn;
                                print_r($a);

                                $row = $this->lazada_orders_model->get_by_sn_status_one($order_sn,'confirmed');

                                if(!empty($row)){

                                    $data_dup=array(
                                        'order_number'=>$row["order_number"],
                                       // 'taxinvoiceID' => '',
                                        'created_at'=>$this->common_util->getDbDate($row["created_at"]),
                                        'shipping_fee_original'=>$this->common_util->prep_float($row["shipping_fee_original"]),
                                        'shipping_fee_discount_platform'=>$this->common_util->prep_float($row["shipping_fee_discount_platform"]),
                                        'shipping_fee_discount_seller'=>$this->common_util->prep_float($row["shipping_fee_discount_seller"]),
                                        'shipping_fee'=>$this->common_util->prep_float($row["shipping_fee"]),
                                        'voucher_platform'=>$this->common_util->prep_float($row["voucher_platform"]),
                                        'voucher_seller'=>$this->common_util->prep_float($row["voucher_seller"]),
                                        'voucher'=>$this->common_util->prep_float($row["voucher"]),
                                        'price'=>$this->common_util->prep_float($row["price"]),
                                        'tax_code'=>'',
                                        'status'=>'packed',
                                        'delivery_info'=>$row["delivery_info"],
                                        'updated_at'=>$this->common_util->getDbDate($row["created_at"]),
                                       // 'keygen' => $keygen

                                    );    

                                    $make_cn =   $this->get_orertrace($row["order_number"]);
                                    if($make_cn){
                                      //$this->lazada_orders_model->insert($data_dup);
                                    }

                                }



                              if($status == "confirmed"){
                                    $totol_price = $totol_price+$paidPrice;
                                    $totol_ship = $totol_ship + $shippingFee;
                                 }
                            }
                          }
                        
                          reset($a);
                    }else{
                        echo "<br>Nodata >> ".$order_sn;
                    }   

                    if(($order_ex == "")or($order_ex == $order_sn)){
                        $order_ex = $order_sn;

                        $price_ex = $price_ex + $paidPrice;

                        $price_db = round($price_t,2);
                    }else{
                        if(intval($price_ex) != intval($price_db)){
                            echo "<br>--------------------------------------------------<br>";
                        }

                        echo $order_ex.">>>".$price_ex.">>price from DB::".$price_db."<br>";

                        if(intval($price_ex) != intval($price_db)){
                            echo "<br>--------------------------------------------------<br>";
                        }

                        $order_ex = "";
                        $price_ex = 0;
                        $price_db = 0;

                        $order_ex = $order_sn;

                        $price_ex = $price_ex + $paidPrice;
                        $price_db = $price_t;
                    }
                }

                $total_price = 0;

                $total_price = $totol_price + $totol_ship;

                echo "<br>Price = ".round($total_price,2);
            }   

        }



     }


     function import_sale_chk(){
        $this->load->view('import_sale_chk');
     }

     function import_sale_chk_action(){

        $this->load->library('Upload_secure', [
          'psp_inbox_dir'  => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
        ]);

        $res = $this->upload_secure->upload_file('upload_file1');

        if ($res['is_upload'] === 1) {
            //echo 'accepted: ' . $res['file_name'];
            $file_s = APP_STORE_PATH."/uploads/xls/".$res['file_name'];
            $mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if(in_array($_FILES['upload_file1']['type'],$mimes))
            {
                $this->load->library('Lib_excel');

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($file_s);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($file_s);
                } catch(Exception $e) {
                    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }

                $sheet = $objPHPExcel->getSheet(0); 
                $highestRow = $sheet->getHighestRow(); 
                $highestColumn = $sheet->getHighestColumn();
                $row_data = $highestRow-1;

                $order_sn_old = "";

                $data_order_all = array();
                $data_item_all = array();
                $num =0;
                $keygen = $this->random_util->create_random_number(8);
                $totol_price = 0;
                $totol_ship = 0;

                for ($row = 2; $row <= $highestRow; $row++){ 
                    $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);
                $order_sn = $rowData[0][12];
                $paidPrice = $rowData[0][46];
                $shippingFee = $rowData[0][49];
                $status = $rowData[0][65];
                

                 //echo $paidPrice."<br>";   
                 if($status == "confirmed"){
                    $totol_price = $totol_price+$paidPrice;
                    $totol_ship = $totol_ship + $shippingFee;
                 }    
                 

                     
                }

                $total_price = 0;

                $total_price = $totol_price + $totol_ship;

                echo "paidPrice = ".round($totol_price,2)."<br>";
                echo "shippingFee = ".round($totol_ship,2)."<br>";
                echo "total_price = ".round($total_price,2)."<br>";


            }   

        }



     }

     function import_sale_chk_prep(){
        $this->load->view('import_sale_chk_prep');
     }

     function import_sale_chk_prep_action(){

        $file1_name = "";
        $this->load->library('Upload_secure', [
          'psp_inbox_dir'  => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
        ]);

        $res = $this->upload_secure->upload_file('upload_file1');

        if ($res['is_upload'] === 1) {
            $file_s = APP_STORE_PATH."/uploads/xls/".$res['file_name'];
            $mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if(in_array($_FILES['upload_file1']['type'],$mimes))
            {
                $this->load->library('Lib_excel');

                try {
                    $inputFileType = PHPExcel_IOFactory::identify($file_s);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($file_s);
                } catch(Exception $e) {
                    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
                }

                $sheet = $objPHPExcel->getSheet(0); 
                $highestRow = $sheet->getHighestRow(); 
                $highestColumn = $sheet->getHighestColumn();
                $row_data = $highestRow-1;

                $order_sn_old = "";

                $data_order_all = array();
                $data_item_all = array();
                $num =0;
                $keygen = $this->random_util->create_random_number(8);
                $totol_price = 0;
                $totol_ship = 0;
                $keygen = $this->random_util->create_random_number(8);

                $order_tmp = "";
                $data_no = 1;
                $paid_price = 0;
                $ship_price = 0;
                $createtime_tmp = "";
                $status_tmp = "";
                $initiator_tmp = "";
                $cancel_reason_tmp = "";

                for ($row = 2; $row <= $highestRow; $row++){ 
                    $num =$num+1;

                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);
                    $createtime = $rowData[0][8];
                    $order_sn = $rowData[0][12];
                    $paidPrice = $rowData[0][46];
                    $shippingFee = $rowData[0][49];
                    $status = $rowData[0][65];
                    $initiator = $rowData[0][66];
                    $cancel_reason = $rowData[0][67];

                    $arr_chk = $this->lazada_prep_model->select_by_order_sn($order_sn);
                    
                    if(empty($arr_chk)){

                
                
                echo $order_sn.">>>>>".$highestRow.">>".$num."-->>".$order_tmp."<br>";
                
                if($num == 1){
                    $order_tmp = $order_sn;
                    $createtime_tmp = $createtime;
                    $status_tmp = $status;
                    $initiator_tmp = $initiator;
                    $cancel_reason_tmp = $cancel_reason;

                    $paid_price = $paid_price + $paidPrice;
                    $ship_price = $ship_price + $shippingFee;

                }else{

                    if(($num > 1)and($num < $row_data)){

                        

                        if($order_sn != $order_tmp){

                            $data_insert = array(
                                'createtime' => $createtime_tmp,
                                'order_number' => $order_tmp,
                                'status' => $status_tmp,
                                'cancel_reason' => $cancel_reason_tmp,
                                'initiator' => $initiator_tmp,
                                'paid_price' => $paid_price,
                                'shippingFee' => $ship_price,
                                'code' => $keygen
                            );

                            $this->lazada_prep_model->insert($data_insert);

                            $order_tmp = $order_sn;
                            $createtime_tmp = $createtime;
                            $status_tmp = $status;
                            $initiator_tmp = $initiator;
                            $cancel_reason_tmp = $cancel_reason;
                            $paid_price = $paidPrice;
                            $ship_price = $shippingFee;
                        }else{

                            $paid_price = $paid_price + $paidPrice;
                            $ship_price = $ship_price + $shippingFee;

                        }

                        $order_tmp = $order_sn;

                    }elseif($num == $row_data){

                        echo "Last-->>".$num."<---Order--->".$order_sn."<br>";

                        if($order_sn != $order_tmp){

                            $data_insert = array(
                                'createtime' => $createtime_tmp,
                                'order_number' => $order_tmp,
                                'status' => $status_tmp,
                                'cancel_reason' => $cancel_reason_tmp,
                                'initiator' => $initiator_tmp,
                                'paid_price' => $paid_price,
                                'shippingFee' => $ship_price,
                                'code' => $keygen
                            );

                            $this->lazada_prep_model->insert($data_insert);

                            $data_insert = array(
                                'createtime' => $createtime,
                                'order_number' => $order_sn,
                                'status' => $status,
                                'cancel_reason' => $cancel_reason,
                                'initiator' => $initiator,
                                'paid_price' => $paidPrice,
                                'shippingFee' => $shippingFee,
                                'code' => $keygen
                            );

                            $this->lazada_prep_model->insert($data_insert);

                        }else{

                            $paid_price = $paid_price + $paidPrice;
                            $ship_price = $ship_price + $shippingFee;

                            $data_insert = array(
                                'createtime' => $createtime_tmp,
                                'order_number' => $order_tmp,
                                'status' => $status_tmp,
                                'cancel_reason' => $cancel_reason_tmp,
                                'initiator' => $initiator_tmp,
                                'paid_price' => $paid_price,
                                'shippingFee' => $ship_price,
                                'code' => $keygen
                            );

                            $this->lazada_prep_model->insert($data_insert);
                        }

                        

                    }
                 }



                 //$data_no = $data_no +1;
                    }
                     
                }//for ($row = 2; $row <= $highestRow; $row++){ 




            }   

        }

     }

     function get_data_sale_by_date(){

        $StartDate = '2024-12-01';
        $EndDate = '2024-12-31';

        $arr_datas =$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
      //print_r($arr_datas);

      $keygen = $this->random_util->create_random_number(8);

        foreach($arr_datas as $arr_data){

          $chk_data = $this->lazada_prep_api_model->select_by_order_sn($arr_data['order_number']);  
          if(empty($chk_data)){
              $data = array(
                'order_number' => $arr_data['order_number'],
                'transactiondate' => $arr_data['transactiondate'],
                'start_inv' => $arr_data['start_inv'],
                'end_inv' => $arr_data['end_inv'],
                'shipping_fee' => $arr_data['shipping_fee'],
                'voucher_platform' => $arr_data['voucher_platform'],
                'voucher_seller' => $arr_data['voucher_seller'],
                'voucher' => $arr_data['voucher'],
                'price' => $arr_data['price'],
                'priceVATincluded' => $arr_data['priceVATincluded'],
                'priceBeforeVAT' => $arr_data['priceBeforeVAT'],
                'VAT' => $arr_data['VAT'],
                'code' => $keygen
              );

              $this->lazada_prep_api_model->insert($data);
          }

        }

        $this->prep_make();

      }

      function prep_make(){

        $arr_datas = $this->lazada_prep_model->select_prep_join_by_orderno();
        //print_r($arr_datas);
        $num  = 1;
        foreach($arr_datas as $arr_data){

          $ref_price = $arr_data["price"] - $arr_data["voucher_seller"];

          $diffprice = $arr_data['paid_price']-$ref_price;

          
          echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$ref_price.">>diff>>".intval($diffprice)."<br>";

          //echo intval($diffprice);
          //echo "<br>";
          if(intval($diffprice) != 0){

            echo "-------- CHECK -----------<br>";
            echo $arr_data['order_sn_s']."<br>";
            echo "-------- CHECK -----------<br>";
          }

          
        $num = $num+1;
      }
    }

    function prep_make_code(){

        $arr_datas = $this->lazada_prep_model->select_prep_join_by_orderno_status_code('27272393');
        //print_r($arr_datas);
        $num  = 1;
        $total_api = 0;
        $total_prep = 0;
        foreach($arr_datas as $arr_data){

          $ref_price = $arr_data["price"] - $arr_data["voucher_seller"];

          $diffprice = $arr_data['paid_price']-$ref_price;

          $total_api = $total_api + $ref_price;
          $total_prep = $total_prep + $arr_data['paid_price'];

          
          echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$ref_price.">>diff>>".intval($diffprice)."<br>";

          //echo intval($diffprice);
          //echo "<br>";
          if(intval($diffprice) != 0){

            echo "-------- CHECK -----------<br>";
            echo $arr_data['order_sn_s']."<br>";
            echo "-------- CHECK -----------<br>";
          }

          
        $num = $num+1;
      }

      echo "total API >> ".$total_api."<br>";
      echo "total Excel >> ".$total_prep."<br>";
    }

     function chk_track(){

        $order_number = $this->uri->segment(3);

        include_once APPPATH . 'third_party\api\lazada\LazopSdk.php';

        $c2 = new LazopClient($this->config->item('lazAPI'), $this->config->item('Appkey'), $this->config->item('Secret'));
        $request2 = new LazopRequest('/logistic/order/trace','GET');
        $request2->addApiParam('order_id',$order_number);
        $items2=$c2->execute($request2,$this->accesstoken);

        $datas = json_decode($items2,true);

        print_r($datas);

        $make_cn = true;

        if(isset($datas['result']['module'][0]['package_detail_info_list'][0])){

              $cnt = count($datas['result']['module'][0]['package_detail_info_list'][0]);
             // print_r($datas['result']['module'][0]['package_detail_info_list'][0]);
             // echo "cnt>>".$cnt;
              if($cnt == 0){
                $make_cn = false;
              }else{

                $search_info100 = $this->searchArray('100100',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                $search_info101 = $this->searchArray('100101',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                $search_info102 = $this->searchArray('100102',$datas['result']['module'][0]['package_detail_info_list'][0]['logistic_detail_info_list']);

                if(($search_info100)or($search_info101)or($search_info102)){
                    
                   $make_cn = true;
                }else{
                    
                  $make_cn = false;
                }

              }
        
        }else{
          $make_cn = false;
        }
       
       if($make_cn){
        echo "true";
       }else{
        echo "false";
       }
        
     }

     function dei_dup(){

        //$row = $this->lazada_orders_model->get_order_for_del_dup();

     }


}
