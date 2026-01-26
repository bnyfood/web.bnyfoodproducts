<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 class shopee extends CI_Controller {
    protected  $accesstoken;
    protected $global_arr;

	function __construct() 
	{
    set_time_limit(1500);
		//:[Auto call parent construct]
        parent::__construct();

		$this->load->library('session');
		$this->load->model('shopee_orders_model');
    $this->load->model('shopee_orderitems_model');
    $this->load->model('shopee_token_model');
    $this->load->model('shopee_order_list_model');
    $this->load->model('shopee_order_download_model');
    $this->load->model('shopee_shipping_address_model');
    $this->load->model('bnylog_model');
    $this->load->model('DataDownload_model');
    $this->load->model('shopee_discount_list_model');
    $this->load->model('shopee_discounts_model');
    $this->load->model('shopee_voucher_list_model');
    $this->load->model('shopee_voucher_model');
    $this->load->model('shopee_follow_prize_model');
    $this->load->model('shopee_follow_prize_list_model');
    $this->load->model('shopee_bundle_deal_list_model');
    $this->load->model('shopee_bundle_deal_model');
    $this->load->model('shopee_addon_deal_list_model');
    $this->load->model('shopee_escrow_detail_model');
    $this->load->model('shopee_escrow_order_income_model');
    $this->load->model('shopee_escrow_items_model');
    $this->load->model('shopee_taxinvoiceid_model');
    $this->load->model('shopee_taxinvoiceid_v2_model');
    $this->load->model('shp_orders_migrate_model');
    $this->load->model('shp_order_item_migrate_model');
    $this->load->model('shopee_tracking_model');
    $this->load->model('shopee_prep_model');
    $this->load->model('shopee_prep_api_model');
    $this->load->model('shopee_return_order_model');
    $this->load->model('free_model');

		$this->load->library("util/array_util");
    $this->load->library("util/common_util");
    $this->load->library("util/random_util");
    $this->load->library("util/db_util");
    $this->load->library("util/date_util");
    
    $this->load->library("businesslogic/shopeeapi");
    $this->load->library("businesslogic/number_bl");
    $this->load->library("businesslogic/shopee_bl");
    $this->load->library("businesslogic/upload_bl");

		
        //$this->global_arr=$this->laztoken_model->getlatesttoken();

		//$this->accesstoken=$this->global_arr->token;

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


    public function shopeeauthen()
    {
   $link=$this->shopee_bl->get_authenticatrion_link(SHOPEE_PATNERKEY,'1001849');
   $data=array('link'=>$link
   );

   $this->load->view('admin/shopeeauthen',$data);

    }


    public function authenticated()
    {

date_default_timezone_set('Asia/Bangkok');
$date = new DateTime();




        $data=array('code'=>$this->input->get('code'),
                     'shopid'=>$this->input->get('shop_id'),
                     'code_generateddatetime'=>$date->format('Y-m-d H:i:s')

        );
  $this->shopee_token_model->insert_token($data);

  $ShopeeLoginID=$this->shopee_token_model->get_just_inserted_id();

     //echo "code:".$this->input->get('code')."<br>";
      //echo "shopid:".$this->input->get('shop_id')."<br>";
      //echo "main_account_id:".$this->input->get('main_account_id')."<br>";

      //get access token update to record
      $data=$this->shopee_bl->get_accesstoken($this->input->get('code'),$this->input->get('shop_id'));

      //print_r($data);
      //print("<br><br>");
      //print($data['access_token']);
      //print("<br><br>");



      if($data["error"]=="")// no error
      {
         $data_toupdate=array('token'=>$data['access_token'],
                              'refreshtoken'=>$data['refresh_token'],
                               'token_generateddatetime'=>$date->format('Y-m-d H:i:s'),
                               'refresh_expires_in'=>$data['expire_in']

         );
         $this->shopee_token_model->update_by_ShopeeLoginID( $ShopeeLoginID,$data_toupdate);
         echo "DONE";
         print_r($data);


      }
    }

      
      
    


     public function refreshaccesstoken()
     {
     
     date_default_timezone_set('Asia/Bangkok');
     $date = new DateTime();
     $arr=$this->shopee_bl->refresh_accesstoken();

     if(!empty($arr)){

       //$shopee_return=$this->array_util->if_elemenmt_exist("access_token",$arr['shopee_return']);
       if(array_key_exists("access_token",$arr['shopee_return']))
        {
                echo "yes";
                print_r($arr['shopee_return']);

         if($arr!=0) //valid data so update token record
         {
      
      //print_r($arr['shopee_return']['request_id']);

            $arr_data=array(
                     'token'=>$arr['shopee_return']['access_token'],
                     'refreshtoken'=>$arr['shopee_return']['refresh_token'],
                     'code_generateddatetime'=>$date->format('Y-m-d H:i:s'),
                     'token_generateddatetime'=>$date->format('Y-m-d H:i:s'),
                     'refresh_expires_in'=>$arr['shopee_return']['expire_in']
            );
            //print_r($arr_data);
             $this->shopee_token_model->update_by_ShopeeLoginID($arr['ShopeeLoginID'],$arr_data);
           }
         }else{
          echo "No data api return.";
         }

       }

     }

     function getorders_test(){


   if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/order/get_order_list","get")))
   {
    die("canot init shopee api");
   }

        $data=array('time_range_field'=>'create_time',
               'time_from'=>intval(date_create('2022-06-28 00:00:00.000')->gettimestamp()),
                'time_to'=>intval(date_create(date("Y-m-d H:i:s", strtotime("+".strval((1*86400)-1)." seconds", strtotime('2022-06-28 00:00:00.000'))))->gettimestamp()),
                'page_size'=>intval(100),
                'cursor'=> ''
                );
          $this->shopeeapi->setData($data);

          $datas=$this->shopeeapi->execute();

           
       // print_r($datas['response']);

          $num =1;

           foreach($datas['response']['order_list'] as $row)
            {

             echo  $num." -- >> ".$row['order_sn']."<br>";
             $num = $num +1;
                            }
     }

function getOrderList_recheck(){

    $date_towdayago = date('Y-m-d 00:00:00',strtotime("-2 days"));
    $date_yesterday = date('Y-m-d 00:00:00',strtotime("-1 days"));

    $date_yesterday_ymd = date('ymd',strtotime("-1 days"));

    echo $date_towdayago;

    if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/order/get_order_list","get")))
       {
        die("canot init shopee api");
       }

    echo "<br>SHOPEE_order_start_date:".$date_towdayago;
    echo "<br>SHOPEE_order_end_date: ".date("Y-m-d H:i:s", strtotime("+".strval((1*86400)-1)." seconds", strtotime($date_yesterday)));
    echo "<br>ymd>>".$date_yesterday_ymd."<br>";



   $data=array('time_range_field'=>'create_time',
               'time_from'=>intval(date_create($date_towdayago)->gettimestamp()),
                'time_to'=>intval(date_create(date("Y-m-d H:i:s", strtotime("+".strval((1*86400)-1)." seconds", strtotime($date_yesterday))))->gettimestamp()),
                'page_size'=>intval(100),
                'cursor'=>''
                );
  $this->shopeeapi->setData($data);

  $datas=$this->shopeeapi->execute();   

  //print_r($datas['response']);
  $arr_order_yymmdd = array();
  $arr_data_list_dbs = $this->shopee_order_list_model->select_by_yymmdd($date_yesterday_ymd);

  foreach($arr_data_list_dbs as $arr_data_list_db){

    array_push($arr_order_yymmdd,$arr_data_list_db['order_sn']);

  }

  print_r($arr_order_yymmdd);

  foreach($datas['response']['order_list'] as $row){

    echo $row['order_sn']."<br>";

    if (!in_array($row['order_sn'], $arr_order_yymmdd)) {
        //echo "<br>".$row['order_sn']."<br>";

        $sn_chk = $this->shopee_order_list_model->select_by_ordersn($row['order_sn']);

        if(empty($sn_chk)){

            $arr_insert_sn = array(
              'order_sn' => $row['order_sn']
              );

            $this->shopee_order_list_model->insert($arr_insert_sn);

        }
    } 


  } 

  // print_r($datas['response']['order_list']);

}

public function getOrderList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

 if(empty($download_arr))  //never download so 
 {
   $data=array('BNY_SUBSCRIPTION_SHOPID'=>BNY_SUBSCRIPTION_SHOPID,
               'shopee_orderlist_start_date'=>BNY_ESTABLISHDATETIME,
               'shopee_orderlist_date_interval'=>intval(1),
               'shopee_orderlist_cursor'=>""
 );


   print_r($data);
   $this->DataDownload_model->insert($data);
   
   die("we just inserted");
 }
 else //have download instruction in db

 {
  $initial_interval=$download_arr["shopee_orderlist_date_interval"];
   
   if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/order/get_order_list","get")))
   {
    die("canot init shopee api");
   }

   echo "<br>SHOPEE_order_start_date: ".$download_arr["shopee_orderlist_start_date"];
   echo "<br>SHOPEE_order_end_date: ".date("Y-m-d H:i:s", strtotime("+".strval(($download_arr["shopee_orderlist_date_interval"]*86400)-1)." seconds", strtotime($download_arr["shopee_orderlist_start_date"])));



   $data=array('time_range_field'=>'create_time',
               'time_from'=>intval(date_create($download_arr["shopee_orderlist_start_date"])->gettimestamp()),
                'time_to'=>intval(date_create(date("Y-m-d H:i:s", strtotime("+".strval(($download_arr["shopee_orderlist_date_interval"]*86400)-1)." seconds", strtotime($download_arr["shopee_orderlist_start_date"]))))->gettimestamp()),
                'page_size'=>intval(100),
                'cursor'=>$download_arr["shopee_orderlist_cursor"]
                );
  $this->shopeeapi->setData($data);

  $datas=$this->shopeeapi->execute();

   
print_r($datas['response']);
//echo "ERROR:"; 
//print_r($datas['error']);
   $now_date=date_create(date("Y-m-d"));
   $now_date_his=date("Y-m-d H:i:s");

     if($datas['error']=="")
   {
      if(count($datas['response']['order_list'])==0) // there is no order selected--> shipt to nect period
      {

    /*
      $start_date=date_create(date("Y-m-d", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"]))));

      
      echo "##".intval(date_diff($start_date,$now_date)->format("%R%a"))."##";

         

         if(intval(date_diff($start_date,$now_date)->format("%R%a"))>30)
         {
          $interval=1;
         }
         else
         {
         $interval=1;
         }

         echo "interval: ".$interval;

        $data=array('shopee_orderlist_start_date'=>date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"]))),
                     'shopee_orderlist_date_interval'=>intval($interval)

        );

        //print_r($data);
        if(intval(date_diff($start_date,$now_date)->format("%R%a"))>=1)
         {
        $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);
         }
         */
        exit();
       }
       else //there is/are some orders
       {
        $aaa = date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"])));
                                echo "<br>".$aaa."<br>".date('Y-m-d H:i:s')."<br>";
       /* echo date_create(date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"]))));
        echo "---";
        echo date_create(date('Y-m-d H:i:s'));*/
      //  if(date_create(date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"]))))->gettimestamp()<=date_create(date('Y-m-d H:i:s'))->gettimestamp()){

                    echo "here";
                          $arr_datainsert = array();
                          $num_data=0;

                          foreach($datas['response']['order_list'] as $row)
                            {

                              $datainsert=array('order_sn'=>$row['order_sn']);

                                if(!is_null($row['order_sn']))
                                    {

                                      $chk_order_item_list = $this->shopee_order_list_model->select_by_ordersn($row['order_sn']);
                                      //print_r($chk_order_item_list);
                                      if(empty($chk_order_item_list)){
                                        array_push($arr_datainsert,$datainsert);
                                        $num_data = $num_data +1;
                                      }

                                    }
                                    else
                                    {

                                      $datalog = array(
                                      'log_type' => 1,
                                      'log_code' => 'NULLORnum',
                                      'log_note' => 'API LAZADA Order Insert NULL Value Order Number = '.$row['order_sn'],
                                      'log_status' => 1
                                      );

                                      $this->bnylog_model->insert($datalog);

                                      break;
                                    }
                          }// foreach
                          echo $num_data."==".count($arr_datainsert);
                          if($num_data == count($arr_datainsert)) // all success we insert
                            {
                              $arr_to_insert=array_reverse($arr_datainsert);

                              //print_r($arr_to_insert);

                              $sql_insert_list_batch = $this->db_util->insert_batch('shopee_order_list',$arr_to_insert);
                              $this->free_model->query_run($sql_insert_list_batch);
        
                             //$this->shopee_order_list_model->insert_all($arr_to_insert);

                             empty($arr_to_insert);
                             empty($arr_datainsert);



                              $date_start=$download_arr["shopee_orderlist_start_date"];
                              $date_start_as_date_type=date_create($date_start); 
                              $interval=$download_arr["shopee_orderlist_date_interval"];
                              //echo "<br>More date( )ata?: ".$datas['response']['more'];

                              if($datas['response']['next_cursor']=="") // no more data to download for this date range-> shipt to next period
                              {


                                //$bbb = date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"])));
                                //echo "<br>".$bbb."<br>".date('Y-m-d H:i:s')."<br>";

                                if(intval(date_diff(date_create($date_start),$now_date)->format("%R%a")) == 0){

                                  echo ">>>>".$now_date_his."<<<<";
                                  $date_start=$now_date_his;
                                }else{
                                  $date_start=date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"])));
                                }
                               // $date_start_as_date_type=date_create($date_start);  

                                       if(intval(date_diff($date_start_as_date_type,$now_date)->format("%R%a"))>30)
                                       {
                                        $interval=1;
                                       }
                                       else
                                       {
                                       $interval=1;
                                       }
                              }

                              $data=array('shopee_orderlist_start_date'=>$date_start,
                                          'shopee_orderlist_cursor'=>$cursor=$datas['response']['next_cursor'],
                                          'shopee_orderlist_date_interval'=>intval($interval)
                              );

                              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);
                               /* if(intval(date_diff($date_start_as_date_type,$now_date)->format("%R%a"))>=1)
                             {
                              echo "##update datadownload##";
                              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);
                            }*/
                          }
                       //}//if(date_create(date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_orderlist_date_interval"])." day", strtotime($download_arr["shopee_orderlist_start_date"]))))->gettimestamp()<=date_create(date('Y-m-d H:i:s'))->gettimestamp())
           }
         }




}


}

public function getOrderList_obsoleted() //input as date string
     {

      //we download 15 days block and move cursor along the records
      //we store done in shopee_orders_download


        $latest_order_download=$this->shopee_order_download_model->select_latest_record_job();  // created_at as date (not unix timestamp)
        $count_record=$this->shopee_order_download_model->count_record();
     //   print_r($latest_order_download);
      //  echo "pass";

        //turn to unix timestamp
        if($count_record->count==0)
        {


          $date_start=date_create(BNY_ESTABLISHDATE);    // unix time stamp
          $date_start_temp=$date_start;

          //echo "<br>date_start: ".date('Y-m-d',$date_start->getTimeStamp());


          date_add($date_start_temp,date_interval_create_from_date_string("15 days"));
          $date_end=$date_start_temp;

          //echo "<br>date_end: ".date('Y-m-d',$date_end->getTimeStamp());

          $cursor="";   

          

        }
        else
        {

          if(is_null($latest_order_download))
          {
            exit();
          }

        $date_start=date_create($latest_order_download['start_time']);       // unix time stamp
        $date_end=date_create($latest_order_download['end_time']);       // unix time stamp
        $cursor=$latest_order_download['cursor_string'];
        $OrderDownLoadID=$latest_order_download['OrderDownLoadID'];


        }
        


$date_start_num=$date_start->gettimestamp();
$date_end_num =$date_end->gettimestamp();

//echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_list";
  $timestamp=$this->shopee_bl->get_timestamp();
   
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$timestamp."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;

   $data=array('time_range_field'=>'create_time',
               'time_from'=>intval($date_start_num),
                'time_to'=>intval($date_end_num),
                'page_size'=>intval(100),
                'cursor'=>$cursor
                );
   
   foreach($data as $key => $value)
   {

$url=$url."&".$key."=".$value;

   }
  // echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
   $return_str=$this->shopee_bl->shopee_curl_get($url);
   $datas = json_decode($return_str,true);
//print_r($datas['response']);
//print_r($datas['error']);

      
   
   if($datas['error']=="")
   {
       
       
      if(count($datas['response']['order_list'])==0) // there is no order selected
      {

        
         
           

        $nextday=date('Y-M-d H:i:s',$date_end_num);
        date_add($date_end,date_interval_create_from_date_string("1 day"));
        //echo "<br>will recursive call for next 15 days ".date('Y-m-d',$date_end->gettimestamp());
         // flush();
       //sleep(3);
        //$this->getOrderList(date('Y-m-d',$date_end->gettimestamp()));



                  $cursor="";
                  $date_start=$date_end;
                  date_add($date_start,date_interval_create_from_date_string("1 day"));
                  $date_start_string=date('Y-m-d',$date_start->gettimestamp());

                  date_add($date_start,date_interval_create_from_date_string("3 days"));
                  $date_end_string=date('Y-m-d',$date_start->gettimestamp());

                  $data=array('start_time'=>$date_start_string,
                      'end_time'=>$date_end_string,
                      'cursor_string'=>""
                  );

                  //echo "<br>end date to compare: ".date_create($date_end_string)->getTimeStamp();
                  //echo "<br>now: ".date_create($date_end_string)->getTimeStamp();

                  echo "end date: ".$date_end_string;
                  if(date_create($date_end_string)->getTimeStamp()<date_create(date('Y-m-d H:i:s'))->gettimestamp())
                  {

                    echo "<br>I did 284";

                            if($count_record->count>0)
                            {
                                    //update downloaded
                          $data_update=array('downloaded'=>1
                          );
                          
                         $this->shopee_order_download_model->update_shopee_order_download($OrderDownLoadID,$data_update); 
                             }
                         


                 $this->shopee_order_download_model->insert_order_download($data);
                  }



      }
      else
      { 

        //we have some oreder list here so we 

        //$this->shopee_orders_model->insert($datas['response']['order_list']);
        $arr_datainsert = array();
        $num_data=0;

        foreach($datas['response']['order_list'] as $row)
          {

        $datainsert=array('order_sn'=>$row['order_sn']);

         



              if(!is_null($row['order_sn']))
              {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

               }
               else
               {

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API LAZADA Order Insert NULL Value Order Number = '.$row['order_sn'],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }




        }// foreach


        if($num_data == count($arr_datainsert)) // all success we insert
        {
         


          //echo "<br>More data?: ".$datas['response']['more'];
          if($datas['response']['more']) // more data to download for this date range
          {

            //echo "<br>more data to download";

                  //insert to shopee_orders_download

                  $cursor=$datas['response']['next_cursor'];
                  $date_start_string=date('Y-m-d',$date_start->gettimestamp());
                  $date_end_string=date('Y-m-d',$date_end->gettimestamp());

                  $data=array('start_time'=>$date_start_string,
                      'end_time'=>$date_end_string,
                      'cursor_string'=>$cursor
                  );

                  echo "end date: ".$date_end_string;

                  if(date_create($date_end_string)->getTimeStamp()<date_create(date('Y-m-d H:i:s'))->gettimestamp())
                  {
                    echo "<br>I did 373";

                     print_r($arr_datainsert);

                      //insert to shopee orderlist
                      $this->shopee_order_list_model->insert_all($arr_datainsert);

               
                             //update downloaded
                      $data_update=array('downloaded'=>1
                      );
                      
                     $this->shopee_order_download_model->update_shopee_order_download($OrderDownLoadID,$data_update); 
         

                      $this->shopee_order_download_model->insert_order_download($data);
                  }

                 

                  




          }
          else // no more data for this date range, we then shift to next date range
          {

                  $cursor="";
                  $date_start=$date_end;
                  date_add($date_start,date_interval_create_from_date_string("1 day"));
                  $date_start_string=date('Y-m-d',$date_start->gettimestamp());

                  date_add($date_start,date_interval_create_from_date_string("3 days"));
                  $date_end_string=date('Y-m-d',$date_start->gettimestamp());

                  $data=array('start_time'=>$date_start_string,
                      'end_time'=>$date_end_string,
                      'cursor_string'=>""

                  );
 
                 echo "end date: ".$date_end_string;
                  if(date_create($date_end_string)->getTimeStamp()<date_create(date('Y-m-d H:i:s'))->gettimestamp())
                  {
                    echo "<br>I did 419";
                     print_r($arr_datainsert);

                      //insert to shopee orderlist
                      $this->shopee_order_list_model->insert_all($arr_datainsert);

               
                             //update downloaded
                      $data_update=array('downloaded'=>1
                      );
                      
                     $this->shopee_order_download_model->update_shopee_order_download($OrderDownLoadID,$data_update); 
                 $this->shopee_order_download_model->insert_order_download($data);
                  }
                 



          }



          
        }


       }
     }
   
   //print_r($datas);
   //return $datas;






}

function chk_date_getOrders(){

    $arr_date = $this->shopee_orders_model->select_chk_date();
   // echo $arr_date['cntdate'];
    if(!empty($arr_date)){
        if($arr_date['cntdate'] < 1){
            //Get Now Data
             //$this->getOrders();
             $this->getOldOrders();
         // echo "getOders";
        }else{
            //Get Old Data
             $this->getOldOrders();
          //echo "getOldOrders";
        }
    }
} 

public function getOldOrders()
{
  $arr_shopee_order_list=$this->shopee_order_list_model->select_order_list_top_numrows_order_by_OrderListID(20);
  //print_r($arr_shopee_order_list);
  if(!empty($arr_shopee_order_list))
    {
      $arr_order_sn = array();
      $arr_order_status = array();

      foreach($arr_shopee_order_list as $arr_order_list){
        array_push($arr_order_sn,$arr_order_list['order_sn']);
      }
      $arr_data_order_status = $this->shopee_orders_model->select_status_by_sn($arr_order_sn);
      /*foreach($arr_data_order_status as $data_order_status){
        $arr_order_status[$data_order_status['order_sn']] = $data_order_status['order_status'];
      }*/

      //print_r($arr_order_status);
     $this->all_order_status_old($arr_shopee_order_list,$arr_data_order_status);
      
    }

}

function all_order_status_old($arr_order_list,$arr_order_status)
{
   print_r($arr_order_list);
  print_r($arr_order_status);
    //echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_detail";
  $timestamp=$this->shopee_bl->get_timestamp();
   
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


   $order_list_sn_key_id_value_arr=array();

   $order_sn_list="";
   foreach($arr_order_list as $rows)
   {
    $order_list_sn_key_id_value_arr[$rows['order_sn']]=$rows['OrderListID'];
     if($order_sn_list!="")
     {
      $order_sn_list=$order_sn_list.",".$rows['order_sn'];
     }
     else
     {
      $order_sn_list=$rows['order_sn'];
     }


   }


   $data=array('order_sn_list'=>$order_sn_list,
               'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                );
   
   foreach($data as $key => $value)                                                                                                                                                
   {

      $url=$url."&".$key."=".$value;

   }
   //echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
   $return_str=$this->shopee_bl->shopee_curl_get($url);
   $datas = json_decode($return_str,true);
   print_r($datas);


   $go_for_insert=true;
    if($datas['error']=="")
     {
       
       
      if(count($datas['response']['order_list'])>0) 
      {
        $all_orders=count($datas['response']['order_list']);
        $order_with_death_status=0;

      }
      else
      {
        $go_for_insert=$go_for_insert && false;
      }
    }
    else
    {
      $go_for_insert=$go_for_insert && false;
    }

    if($go_for_insert)
    {
      //echo "go for insert";
      //print_r($datas['response']['order_list']);

 
      $data_insert_order = array();
      $data_insert_shippinmg_address=array();
      $data_insertt_order_item=array();

       $data_insert_order_dup = array();
      $data_insert_shippinmg_address_dup=array();
      $data_insertt_order_item_dup=array();



      $keygen = $this->random_util->create_random_number(8);
      $num_data=0;

      foreach($datas['response']['order_list'] as $row)
        {
          // if here check same status

       /* $orcdr_sn_chk ="";
        if(!empty($arr_order_status[$row["order_sn"]])){
          $orcdr_sn_chk = $arr_order_status[$row["order_sn"]];
        }*/
        $chk_same_status = true;
      /*  foreach($arr_order_status as $order_status){
          if(($order_status['order_sn'] == $row['order_sn'] )and($order_status['order_status'] == $row['order_status'])){
            $chk_same_status = false;
            break;
          }
        }*/

          if($chk_same_status){

          echo $row["order_sn"]."<>".$row["order_status"]."<br>";

                //------------- main
              $data_order=array('order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>$row["order_status"],
                    'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                    'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                    'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                    'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                    'cancel_by'=>$row["cancel_by"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                    'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                    'keygen' => $keygen



            );

              
              $data_shipping=array('order_sn'=>$row["order_sn"],
                                   'name'=>$row["recipient_address"]["name"],
                                   'phone'=>$row["recipient_address"]["phone"],
                                   'town'=>$row["recipient_address"]["town"],
                                   'district'=>$row["recipient_address"]["district"],
                                   'city'=>$row["recipient_address"]["city"],
                                   'state'=>$row["recipient_address"]["state"],
                                   'zipcode'=>$row["recipient_address"]["zipcode"],
                                   'full_address'=>$row["recipient_address"]["full_address"],
                                   'keygen' => $keygen

              );


              if(!is_null($row["order_sn"])){

                    array_push($data_insert_shippinmg_address,$data_shipping);
                    

               }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API shopee shipping address Insert NULL Value Order Number = '.$row["order_sn"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }
               //--- end main
               //------------- dup

               $data_order_dup=array('order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>'SHIPPED',
                    'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                    'update_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                    'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                    'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                    'cancel_by'=>$row["cancel_by"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                    'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                    'keygen' => $keygen

            );

              foreach($row["item_list"] as $row_items)
              {

                     $data_orderitems=array('order_sn'=>$row["order_sn"],
                    'order_item_id'=>intval($row_items["order_item_id"]),
                    'item_sku'=>$row_items["item_sku"],
                    'item_name'=>$row_items["item_name"],
                    'model_id'=>intval($row_items["model_id"]),
                    'model_sku'=>$row_items["model_sku"],
                    'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                    'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                    'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                    'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                    'promotion_type'=>$row_items["promotion_type"],
                    'promotion_id'=>intval($row_items["promotion_id"]),
                    'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                    'keygen' => $keygen

                     );

                       if(!is_null($row["order_sn"])){

                        array_push($data_insertt_order_item,$data_orderitems);

                    //array_push($data_insertt_order_item_dup,$data_orderitems_dup);
                    

                       }else{

                        $datalog = array(
                            'log_type' => 1,
                            'log_code' => $log_code,
                            'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                            'log_status' => 1
                        );

                        $this->bnylog_model->insert($datalog);

                        break;
                       }




              }


            //$data_insert = $this->lazada_orderitems_model->insertOrderitems($data2);

            if(!is_null($row["order_sn"])){

                    array_push($data_insert_order,$data_order);
                    array_push($data_insert_order,$data_order_dup);
                    $num_data = $num_data +2;

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

             

        // } // complete or (cancle by system and reason=FailDelivery)

         } // end if check same status
        } // foreach


        if($num_data==count($data_insert_order))
        {

              $this->shopee_orders_model->insert_all($data_insert_order);
              $this->shopee_orderitems_model->insert_all($data_insertt_order_item);
              $this->shopee_shipping_address_model->insert_all($data_insert_shippinmg_address);

              //$this->shopee_orders_model->insert_all($data_insert_order_dup);
              //$this->shopee_orderitems_model->insert_all($data_insertt_order_item_dup);
              //$this->shopee_shipping_address_model->insert_all($data_insert_shippinmg_address_dup);
        }



    }

// procedure manage double status
    //$this->shopee_orders_model->shopee_manage_duplicates_orders();
//  procedure update order list status if death (COMPLETED, CANCELED)
    $this->shopee_order_list_model->update_status_complete();


}



public function getOrders_bk_v1()
{
        $arr_shopee_order_list=$this->shopee_order_list_model->select_order_list_top_numrows_order_by_OrderListID(5);
        //print_r($arr_shopee_order_list);
        if(!empty($arr_shopee_order_list))
          {
            $arr_order_sn = array();
            $arr_order_status = array();

            foreach($arr_shopee_order_list as $arr_order_list){
              array_push($arr_order_sn,$arr_order_list['order_sn']);
            }
            $arr_data_order_status = $this->shopee_orders_model->select_status_by_sn($arr_order_sn);
            /*foreach($arr_data_order_status as $data_order_status){
              $arr_order_status[$data_order_status['order_sn']] = $data_order_status['order_status'];
            }*/

            //print_r($arr_order_status);
           $this->all_death_order_status($arr_shopee_order_list,$arr_data_order_status);
            
          }

}



function getOrdertest(){

  $arr_order_list=$this->shopee_order_list_model->select_order_list_top_numrows_order_by_OrderListID(5);

  print_r($arr_order_list);
   //echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_detail";
  $timestamp=$this->shopee_bl->get_timestamp();
   
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


   $order_list_sn_key_id_value_arr=array();

   $order_sn_list="";
   foreach($arr_order_list as $rows)
   {
    $order_list_sn_key_id_value_arr[$rows['order_sn']]=$rows['OrderListID'];
     if($order_sn_list!="")
     {
      $order_sn_list=$order_sn_list.",".$rows['order_sn'];
     }
     else
     {
      $order_sn_list=$rows['order_sn'];
     }


   }


   $data=array('order_sn_list'=>$order_sn_list,
               'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                );
   
   foreach($data as $key => $value)                                                                                                                                                
   {

      $url=$url."&".$key."=".$value;

   }
   //echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
   $return_str=$this->shopee_bl->shopee_curl_get($url);
   $datas = json_decode($return_str,true);
   print_r($datas['response']['order_list']);
}



     



      function count_order(){

       return $this->lazada_orders_model->count_order()->count;


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

//shipping chanell api calls

    public function getShippingProviders()
    {
        //get access token
      
      print_r($this->shopee_bl->getShipingProviders());


      //print_r($arr);

    //https://partner.test-stable.shopeemobile.com/api/v2/logistics/get_channel_list  


    }

    public function setShipingProvider()
    {


        print_r($this->shopee_bl->setShipingProvider($this->input->get('logistics_channel_id')));
    }
    

function all_death_order_status($arr_order_list,$arr_order_status)
{
  //print_r($arr_order_status);
    //echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_detail";
  $timestamp=$this->shopee_bl->get_timestamp();
   
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


   $order_list_sn_key_id_value_arr=array();

   $order_sn_list="";
   foreach($arr_order_list as $rows)
   {
    $order_list_sn_key_id_value_arr[$rows['order_sn']]=$rows['OrderListID'];
     if($order_sn_list!="")
     {
      $order_sn_list=$order_sn_list.",".$rows['order_sn'];
     }
     else
     {
      $order_sn_list=$rows['order_sn'];
     }


   }


   $data=array('order_sn_list'=>$order_sn_list,
               'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                );
   
   foreach($data as $key => $value)                                                                                                                                                
   {

$url=$url."&".$key."=".$value;

   }
  // echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
   $return_str=$this->shopee_bl->shopee_curl_get($url);
   $datas = json_decode($return_str,true);
//print_r($datas['response']);


   $go_for_insert=true;
    if($datas['error']=="")
     {
       
       
      if(count($datas['response']['order_list'])>0) 
      {
        $all_orders=count($datas['response']['order_list']);
        $order_with_death_status=0;
        //foreach($datas['response']['order_list'] as $orders)
        //{

          
         // if($orders["order_status"]=="COMPLETED" || $orders["order_status"]=="CANCELLED")
         // {
         //   $order_with_death_status=$order_with_death_status+1;
         // }


        //}

         //if($all_orders!=$order_with_death_status)
         //{
          //$go_for_insert=$go_for_insert && false;
        // }


      }
      else
      {
        $go_for_insert=$go_for_insert && false;
      }
    }
    else
    {
      $go_for_insert=$go_for_insert && false;
    }

    if($go_for_insert)
    {
      //echo "go for insert";
      //print_r($datas['response']['order_list']);

 
      $data_insert_order = array();
      $data_insert_shippinmg_address=array();
      $data_insertt_order_item=array();


      $keygen = $this->random_util->create_random_number(8);
      $num_data=0;

      foreach($datas['response']['order_list'] as $row)
        {
          // if here check same status

       /* $orcdr_sn_chk ="";
        if(!empty($arr_order_status[$row["order_sn"]])){
          $orcdr_sn_chk = $arr_order_status[$row["order_sn"]];
        }*/
        $chk_same_status = true;
        foreach($arr_order_status as $order_status){
          if(($order_status['order_sn'] == $row['order_sn'] )and($order_status['order_status'] == $row['order_status'])){
            $chk_same_status = false;
            break;
          }
        }

          if($chk_same_status){

          echo $row["order_sn"]."<>".$row["order_status"]."<br>";

           // if($row["cancel_by"]=="" || ($row["cancel_by"]=="system" && $row["cancel_reason"]=="Failed Delivery"))
           // {


               /*
               if($num_data == 0){   //fix invoiceid

                   $arr_lastorder = $this->shopee_orders_model->last_order();   
                   $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],date('Y-m-d H:i:s',$row["create_time"]));    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->shopee_bl->get_shopee_code($last_id,date('Y-m-d H:i:s',$row["create_time"]));  
                    $last_id = $new_textinvoiceID;
                } 
                */

              $data_order=array('order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>$row["order_status"],
                    'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                    'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                    'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                    'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                    'cancel_by'=>$row["cancel_by"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                    'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                    'cancel_reason'=>$row["cancel_reason"],
                    'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                    'keygen' => $keygen



            );


              $data_shipping=array('order_sn'=>$row["order_sn"],
                                   'name'=>$row["recipient_address"]["name"],
                                   'phone'=>$row["recipient_address"]["phone"],
                                   'town'=>$row["recipient_address"]["town"],
                                   'district'=>$row["recipient_address"]["district"],
                                   'city'=>$row["recipient_address"]["city"],
                                   'state'=>$row["recipient_address"]["state"],
                                   'zipcode'=>$row["recipient_address"]["zipcode"],
                                   'full_address'=>$row["recipient_address"]["full_address"],
                                   'keygen' => $keygen

              );


              if(!is_null($row["order_sn"])){

                    array_push($data_insert_shippinmg_address,$data_shipping);
                    

               }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API shopee shipping address Insert NULL Value Order Number = '.$row["order_sn"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }



              foreach($row["item_list"] as $row_items)
              {

                     $data_orderitems=array('order_sn'=>$row["order_sn"],
                    'order_item_id'=>intval($row_items["order_item_id"]),
                    'item_sku'=>$row_items["item_sku"],
                    'item_name'=>$row_items["item_name"],
                    'model_id'=>intval($row_items["model_id"]),
                    'model_sku'=>$row_items["model_sku"],
                    'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                    'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                    'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                    'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                    'promotion_type'=>$row_items["promotion_type"],
                    'promotion_id'=>intval($row_items["promotion_id"]),
                    'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                    'keygen' => $keygen

                     );




                       if(!is_null($row["order_sn"])){

                    array_push($data_insertt_order_item,$data_orderitems);
                    

               }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
               }




              }

            //$data_insert = $this->lazada_orderitems_model->insertOrderitems($data2);

            if(!is_null($row["order_sn"])){

                    array_push($data_insert_order,$data_order);
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



        // } // complete or (cancle by system and reason=FailDelivery)

         } // end if check same status
        } // foreach


        if($num_data==count($data_insert_order))
        {

              $this->shopee_orders_model->insert_all($data_insert_order);
              $this->shopee_orderitems_model->insert_all($data_insertt_order_item);
              $this->shopee_shipping_address_model->insert_all($data_insert_shippinmg_address);
        }



    }

// procedure manage double status
    //$this->shopee_orders_model->shopee_manage_duplicates_orders();
//  procedure update order list status if death (COMPLETED, CANCELED)
    $this->shopee_order_list_model->update_status_complete();


}

function get_shipping_test(){

  if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/logistics/get_tracking_info","get")))
        {
          die("canot init shopee api");
        }


        $data=array(
          'order_sn' => '240324VNWUHED5' 
        );
        $this->shopeeapi->setData($data);
        

        $datas=$this->shopeeapi->execute();
       // print_r($datas);

        $make_cn = true;

        if(isset($datas['response']['tracking_info'])){
          $cnt = count($datas['response']['tracking_info']);

          if($cnt == 0){
            $make_cn = false;
          }else{

            $search_info = $this->searchArray('PICKED_UP',$datas['response']['tracking_info']);
           // $search_info2 = $this->searchArray('พัสดุถูกส่งมอบให้บริษัทขนส่งเรียบร้อยแล้ว',$datas['response']['tracking_info']);

            if($search_info){
               $make_cn = true;
            }else{
              $make_cn = false;
            }

          }
        }else{
          $make_cn = false;
        }

        if($make_cn)
        {
           echo "yes>>";
        }
        else
        {
            echo "no>>";
        }
        



       print_r($datas);

//211001MYUMG9SP-->shipped,211001P0BD9UPM ---> not ship
       
}

function get_tracking_number(){

  if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/logistics/get_tracking_number","get")))
        {
          die("canot init shopee api");
        }

    $arr_shopees = $this->shopee_orders_model->get_orderno_tracking(40);    

    foreach($arr_shopees as $arr_shopee){ 

        $data=array(
          'order_sn' => $arr_shopee['order_sn']
        );
        $this->shopeeapi->setData($data);
        

        $datas=$this->shopeeapi->execute();

      //print_r($datas);
       //echo $datas['response']['tracking_number'];

        if(!empty($datas)){

          if(!empty($datas['response'])){

            $tracking_number = $datas['response']['tracking_number'];

            if( $tracking_number != ""){

              $data_insert = array(
                'order_sn' => $arr_shopee['order_sn'],
                'tracking_number' => $tracking_number,
                'api_round' => 1,
                'api_finish' => 1
               );

               $this->shopee_tracking_model->insert($data_insert);
               echo "Yes->>".$tracking_number."<br>";

            }
          }else{

            $data_insert = array(
                'order_sn' => $arr_shopee['order_sn'],
                'tracking_number' => '',
                'api_round' => 1
            );

            $this->shopee_tracking_model->insert($data_insert);
            echo "No->>xxxxx<br>";
          }

        }

     }  
}


function get_tracking_number_more(){

        if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/logistics/get_tracking_number","get")))
        {
          die("canot init shopee api");
        }

        $arr_shopees = $this->shopee_tracking_model->get_orderno_tracking_round(10);

        foreach($arr_shopees as $arr_shopee){

            $round = $arr_shopee['api_round'];

            $api_round = $round +1;

             $data=array(
                'order_sn' => $arr_shopee['order_sn']
              );
              $this->shopeeapi->setData($data);
              

              $datas=$this->shopeeapi->execute();

            //print_r($datas);

            if(!empty($datas)){
                if(!empty($datas['response'])){

                    $tracking_number = $datas['response']['tracking_number'];

                   if( $tracking_number != ""){
                       $data_update = array(
                        'tracking_number' => $tracking_number,
                        'api_round' => $api_round,
                        'api_finish' => 1
                       );

                       $this->shopee_tracking_model->update($data_update,$arr_shopee['shopee_tracking_id']);
                       echo "Yes->>".$tracking_number."<br>";
                   }
               }else{

                    $data_update = array(
                        'api_round' => $api_round
                    );

                    $this->shopee_tracking_model->update($data_update,$arr_shopee['shopee_tracking_id']);
                    echo "No->>xxxxx<br>";
               }

            }



       }
    }


function searchArray($valsearch, $array) {
   foreach ($array as $key => $val) {
       if ($val['logistics_status'] === $valsearch) {
           return true;
       }
   }
   return false;
}

function get_shipping($order_sn,$status = NULL){

  if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/logistics/get_tracking_info","get")))
        {
          die("canot init shopee api");
        }


        $data=array(
          'order_sn' => $order_sn 
        );
        $this->shopeeapi->setData($data);

        $datas=$this->shopeeapi->execute();
        $make_cn = true;

        
        if(isset($datas['response']['tracking_info'])){
          $cnt = count($datas['response']['tracking_info']);
          if($cnt == 0){
            $make_cn = false;
          }else{

            $search_info = $this->searchArray('PICKED_UP',$datas['response']['tracking_info']);

            if($search_info){
               $make_cn = true;
            }else{
              $make_cn = false;
            }

          }
        }else{
          $make_cn = false;
        }

        if($status == "COMPLETED"){
          $make_cn = true;
        }

        return $make_cn;

       //print_r($datas);

//211001MYUMG9SP-->shipped,211001P0BD9UPM ---> not ship
       
}
//getorder>>>here
public function getOrders()
{
  $arr_shopee_order_list=$this->shopee_order_list_model->select_order_list_top_numrows_order_by_OrderListID(20);
  //print_r($arr_shopee_order_list);
  if(!empty($arr_shopee_order_list))
    {

     $this->get_all_status($arr_shopee_order_list);
      
    }else{
      echo "Empty Order List";
    }

}

function get_all_status($arr_order_list)
{
    //echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_detail";
  $timestamp=$this->shopee_bl->get_timestamp();
   //echo "timestamp:".$timestamp;
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


   $order_list_sn_key_id_value_arr=array();

   $order_sn_list="";
   foreach($arr_order_list as $rows)
   {
    $order_list_sn_key_id_value_arr[$rows['order_sn']]=$rows['OrderListID'];
     if($order_sn_list!="")
     {
      $order_sn_list=$order_sn_list.",".$rows['order_sn'];
     }
     else
     {
      $order_sn_list=$rows['order_sn'];
     }
   }

   $data=array('order_sn_list'=>$order_sn_list,
               'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                );
   
  foreach($data as $key => $value)                                                                                                                                                
  {

    $url=$url."&".$key."=".$value;

  }
  // echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
  $return_str=$this->shopee_bl->shopee_curl_get($url);
  $datas = json_decode($return_str,true);
  //print_r($datas);

  $data_insert_order = array();
  $data_insert_shippinmg_address=array();
  $data_insertt_order_item=array();
  $data_order_no_action=array();


  $keygen = $this->random_util->create_random_number(8);
  $num_data=0;

  foreach($datas['response']['order_list'] as $row)
    {
      $arr_data_chk = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
      if(empty($arr_data_chk)){
      $array_status_death = array();
      $array_status_death = array('COMPLETED','CANCELLED');
      //$array_status_death = array('COMPLETED','CANCELLED','PROCESSED','SHIPPED','TO_CONFIRM_RECEIVE');
      $array_cancel_reason = array('Failed Delivery','Parcel lost in transit. The eligible compensation has been credited into your seller wallet.');

      $orderlist_id_list = $order_list_sn_key_id_value_arr[$row["order_sn"]];
      $order_ctime = date('Y-m-d H:i:s',$row["create_time"]);
      $order_status = $row["order_status"];
      $estimated_shipping_fee = $row["estimated_shipping_fee"];

      $order_death = false;
      if (in_array($row["order_status"], $array_status_death)){
          $order_death = true;
      }

      if($order_death){
        echo "<br>Death>>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
        $data_order=array(
              'order_sn'=>$row["order_sn"],
              'taxinvoiceID'=>"",
              'order_status'=>$row["order_status"],
              'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
              'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
              'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
              'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
              'cancel_by'=>$row["cancel_by"],
              'cancel_reason'=>$row["cancel_reason"],
              'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
              'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
              'cancel_reason'=>$row["cancel_reason"],
              'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
              'keygen' => $keygen
            );

           $data_order_dup=array(
            'order_sn'=>$row["order_sn"],
            'taxinvoiceID'=>"",
            'order_status'=>'PROCESSED',
            'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
            'update_time'=>date('Y-m-d H:i:s',$row["create_time"]),
            'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
            'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
            'cancel_by'=>$row["cancel_by"],
            'cancel_reason'=>$row["cancel_reason"],
            'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
            'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
            'cancel_reason'=>$row["cancel_reason"],
            'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
            'keygen' => $keygen
          );

            if(!is_null($row["order_sn"])){

                  //array_push($data_insert_order,$data_order);
                  $order_id_pre = $this->shopee_orders_model->insert($data_order);
                  $this->getEscrowOld($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_status,$order_ctime,$estimated_shipping_fee,$row["order_status"]);
                  //array_push($data_insert_order,$data_order_dup);
                  //$make_cn = $this->get_shipping($row["order_sn"],'COMPLETED');

                  if($row["order_status"] != "CANCELLED"){
                    
                    $order_id_pre_dup = $this->shopee_orders_model->insert($data_order_dup);
                    $this->getEscrowOld($row["order_sn"],$order_id_pre_dup,$orderlist_id_list,'PROCESSED',$order_ctime,$estimated_shipping_fee,$row["order_status"]);
                    $num_data = $num_data +2;
                  
                  }

                  if($row["order_status"] == "CANCELLED"){

                    //echo "CANCELLED->>>>>".$row["cancel_reason"]."<<<<<<<";

                    $make_cn = false;
                    if (in_array($row["cancel_reason"], $array_cancel_reason)){
                        $make_cn = true;
                    }

                    if($make_cn){
                      //$date_diff_status = $this->date_util->date_diff($row["create_time"],$row["create_time"]); 
                      //1800 = 30 hour
                        //if($date_diff_status > 1800){
                          $order_id_pre_dup = $this->shopee_orders_model->insert($data_order_dup);
                          $this->getEscrowOld($row["order_sn"],$order_id_pre_dup,$orderlist_id_list,'PROCESSED',$order_ctime,$estimated_shipping_fee,$row["order_status"]);
                          $num_data = $num_data +2;  
                        //}
                    }
                  }  

                  array_push($data_order_no_action,$row["order_sn"]);
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

            /* $data_shipping=array(
               'order_sn'=>$row["order_sn"],
               'name'=>$row["recipient_address"]["name"],
               'phone'=>$row["recipient_address"]["phone"],
               'town'=>$row["recipient_address"]["town"],
               'district'=>$row["recipient_address"]["district"],
               'city'=>$row["recipient_address"]["city"],
               'state'=>$row["recipient_address"]["state"],
               'zipcode'=>$row["recipient_address"]["zipcode"],
               'full_address'=>$row["recipient_address"]["full_address"],
               'keygen' => $keygen
              );

              array_push($data_insert_shippinmg_address,$data_shipping);*/
              
             foreach($row["item_list"] as $row_items)
              {

                 $data_orderitems=array(
                  'order_sn'=>$row["order_sn"],
                  'order_item_id'=>intval($row_items["order_item_id"]),
                  'item_sku'=>$row_items["item_sku"],
                  'item_name'=>$row_items["item_name"],
                  'model_id'=>intval($row_items["model_id"]),
                  'model_sku'=>$row_items["model_sku"],
                  'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                  'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                  'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                  'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                  'promotion_type'=>$row_items["promotion_type"],
                  'promotion_id'=>intval($row_items["promotion_id"]),
                  'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                  'keygen' => $keygen
                 );

                if(!is_null($row["order_sn"])){

                    array_push($data_insertt_order_item,$data_orderitems);
                  
                }else{

                  $datalog = array(
                      'log_type' => 1,
                      'log_code' => $log_code,
                      'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                      'log_status' => 1
                  );

                  $this->bnylog_model->insert($datalog);

                  break;
                }
              } //end foreach($row["item_list"] as $row_items)

      }else{//if($order_death){

        echo "<br>Order not Death >>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
        $arr_data_not_death = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
        if(empty($arr_data_not_death)){

          $data_order=array(
            'order_sn'=>$row["order_sn"],
            'taxinvoiceID'=>"",
            'order_status'=>$row["order_status"],
            'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
            'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
            'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
            'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
            'cancel_by'=>$row["cancel_by"],
            'cancel_reason'=>$row["cancel_reason"],
            'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
            'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
            'cancel_reason'=>$row["cancel_reason"],
            'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
            'keygen' => $keygen
          );

          if(!is_null($row["order_sn"])){
              // reason----
               // array_push($data_insert_order,$data_order);
            $order_id_pre = $this->shopee_orders_model->insert($data_order);
            $this->getEscrow($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_ctime,$row["order_status"],0);
                $num_data = $num_data +1;

            array_push($data_order_no_action,$row["order_sn"]);    
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

           //insert data status PROCESSED

           if($row["order_status"] != "CANCELLED"){

            $arr_chk_process = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],"PROCESSED");
            if(empty($arr_chk_process)){

              $data_order_process=array(
                'order_sn'=>$row["order_sn"],
                'taxinvoiceID'=>"",
                'order_status'=>'PROCESSED',
                'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                'update_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                'cancel_by'=>$row["cancel_by"],
                'cancel_reason'=>$row["cancel_reason"],
                'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                'cancel_reason'=>$row["cancel_reason"],
                'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                'keygen' => $keygen
              );

              $order_id_pre_dup = $this->shopee_orders_model->insert($data_order_process);

              $this->getEscrow($row["order_sn"],$order_id_pre_dup,$orderlist_id_list,$order_ctime,$row["order_status"],0);

            }
           }
           
          /* $date_diff_status = $this->date_util->date_diff($row["create_time"],$row["create_time"]); 
           if($row["order_status"] == "CANCELLED"){
            $make_cn = false;
            if (in_array($row["cancel_reason"], $array_cancel_reason)){
                $make_cn = true;
            }

             if($make_cn){
              //1800 = 30 hour
              if($date_diff_status > 1800){
                  $arr_chk_process = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
                  if(empty($arr_chk_process)){
                    //arr insert 

                    $data_order_process=array(
                      'order_sn'=>$row["order_sn"],
                      'taxinvoiceID'=>"",
                      'order_status'=>'PROCESSED',
                      'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                      'update_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                      'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                      'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                      'cancel_by'=>$row["cancel_by"],
                      'cancel_reason'=>$row["cancel_reason"],
                      'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                      'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                      'cancel_reason'=>$row["cancel_reason"],
                      'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                      'keygen' => $keygen
                    );

                    $this->shopee_orders_model->insert($data_order_process);
                  }
                }
              }
            }*/

           

          $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

          if(empty($arr_chk_items)){
         /*   $data_shipping=array(
             'order_sn'=>$row["order_sn"],
             'name'=>$row["recipient_address"]["name"],
             'phone'=>$row["recipient_address"]["phone"],
             'town'=>$row["recipient_address"]["town"],
             'district'=>$row["recipient_address"]["district"],
             'city'=>$row["recipient_address"]["city"],
             'state'=>$row["recipient_address"]["state"],
             'zipcode'=>$row["recipient_address"]["zipcode"],
             'full_address'=>$row["recipient_address"]["full_address"],
             'keygen' => $keygen
            );

            array_push($data_insert_shippinmg_address,$data_shipping);*/
            
           foreach($row["item_list"] as $row_items)
            {

              $data_orderitems=array(
                'order_sn'=>$row["order_sn"],
                'order_item_id'=>intval($row_items["order_item_id"]),
                'item_sku'=>$row_items["item_sku"],
                'item_name'=>$row_items["item_name"],
                'model_id'=>intval($row_items["model_id"]),
                'model_sku'=>$row_items["model_sku"],
                'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                'promotion_type'=>$row_items["promotion_type"],
                'promotion_id'=>intval($row_items["promotion_id"]),
                'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                'keygen' => $keygen
              );

              if(!is_null($row["order_sn"])){

                  array_push($data_insertt_order_item,$data_orderitems);
                
              }else{

                $datalog = array(
                    'log_type' => 1,
                    'log_code' => $log_code,
                    'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                    'log_status' => 1
                );

                $this->bnylog_model->insert($datalog);

                break;
              }
            } //end foreach($row["item_list"] as $row_items)
          }//if(empty($arr_chk_items)){
        } //end if(empty($arr_data_not_death)){

      }//}else{//if($order_death){
      }//if(empty($arr_data_chk)){
    }//foreach($datas['response']['order_list'] as $row)

    $sql_insert_item_batch = $this->db_util->insert_batch('shopee_orderitems',$data_insertt_order_item);
   // $sql_insert_shippinmg_batch = $this->db_util->insert_batch('shopee_shipping_address',$data_insert_shippinmg_address);
   // echo $sql_insert_item_batch;
    $this->free_model->query_run($sql_insert_item_batch);
   // $this->free_model->query_run($sql_insert_shippinmg_batch);

    //$this->shopee_order_list_model->update_status_complete();
    $this->shopee_order_list_model->update_status_complete_by_no($data_order_no_action);


}



function get_all_status_bk_v1($arr_order_list)
{
    //echo "we are here";
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/order/get_order_detail";
  $timestamp=$this->shopee_bl->get_timestamp();
   
   $accesstoken=$this->shopee_bl->getaccesstoken();
   //echo "token:".$accesstoken;
   $shop_id=$this->shopee_bl->getshopid();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
   $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


   $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


   $order_list_sn_key_id_value_arr=array();

   $order_sn_list="";
   foreach($arr_order_list as $rows)
   {
    $order_list_sn_key_id_value_arr[$rows['order_sn']]=$rows['OrderListID'];
     if($order_sn_list!="")
     {
      $order_sn_list=$order_sn_list.",".$rows['order_sn'];
     }
     else
     {
      $order_sn_list=$rows['order_sn'];
     }
   }

   $data=array('order_sn_list'=>$order_sn_list,
               'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                );
   
   foreach($data as $key => $value)                                                                                                                                                
   {

    $url=$url."&".$key."=".$value;

   }
  // echo $url;
   //return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
   $return_str=$this->shopee_bl->shopee_curl_get($url);
   $datas = json_decode($return_str,true);
//print_r($datas);


   $go_for_insert=true;
    if($datas['error']=="")
     {
       
       
      if(count($datas['response']['order_list'])>0) 
      {
        $all_orders=count($datas['response']['order_list']);
        $order_with_death_status=0;
      }
      else
      {
        $go_for_insert=$go_for_insert && false;
      }
    }
    else
    {
      $go_for_insert=$go_for_insert && false;
    }

    if($go_for_insert)
    {
      //echo "go for insert";
      //print_r($datas['response']['order_list']);

 
      $data_insert_order = array();
      $data_insert_shippinmg_address=array();
      $data_insertt_order_item=array();


      $keygen = $this->random_util->create_random_number(8);
      $num_data=0;

      foreach($datas['response']['order_list'] as $row)
        {
          
          $array_status_death = array();
          $array_status_death = array('COMPLETED','CANCELLED');
          //$array_status_death = array('COMPLETED','CANCELLED','PROCESSED','SHIPPED','TO_CONFIRM_RECEIVE');

          $orderlist_id_list = $order_list_sn_key_id_value_arr[$row["order_sn"]];
          $order_ctime = date('Y-m-d H:i:s',$row["create_time"]);
          $order_status = $row["order_status"];
          $estimated_shipping_fee = $row["estimated_shipping_fee"];

          $order_death = false;
          if (in_array($row["order_status"], $array_status_death)){
              $order_death = true;
          }
          if($order_death){
            echo "<br>Death>>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
            //order death
            //$search_status = array_search($row["order_status"], $array_status_death);
            //unset($array_status_death[$search_status]);
            $arr_data_death = $this->shopee_orders_model->get_by_not_status($row["order_sn"],$row["order_status"]);
            
            if(empty($arr_data_death)){
              //insert order death archive
              
               $data_order=array(
                  'order_sn'=>$row["order_sn"],
                  'taxinvoiceID'=>"",
                  'order_status'=>$row["order_status"],
                  'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                  'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                  'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                  'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                  'cancel_by'=>$row["cancel_by"],
                  'cancel_reason'=>$row["cancel_reason"],
                  'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                  'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                  'cancel_reason'=>$row["cancel_reason"],
                  'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                  'keygen' => $keygen
                );

               $data_order_dup=array(
                'order_sn'=>$row["order_sn"],
                'taxinvoiceID'=>"",
                'order_status'=>'SHIPPED',
                'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                'update_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                'cancel_by'=>$row["cancel_by"],
                'cancel_reason'=>$row["cancel_reason"],
                'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                'cancel_reason'=>$row["cancel_reason"],
                'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                'keygen' => $keygen
              );

                if(!is_null($row["order_sn"])){

                      //array_push($data_insert_order,$data_order);
                      $order_id_pre = $this->shopee_orders_model->insert($data_order);
                      $this->getEscrowOld($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_status,$order_ctime,$estimated_shipping_fee,$row["order_status"]);
                      //array_push($data_insert_order,$data_order_dup);
                      $make_cn = $this->get_shipping($row["order_sn"]);
                      if($make_cn){
                        $order_id_pre_dup = $this->shopee_orders_model->insert($data_order_dup);
                        $this->getEscrowOld($row["order_sn"],$order_id_pre_dup,$orderlist_id_list,'SHIPPED',$order_ctime,$estimated_shipping_fee,$row["order_status"]);
                        $num_data = $num_data +2;

                      }else{
                        $arr_up_orderlist = array(
                          'is_death_status' => 1
                        );
                        $this->shopee_order_list_model->update_by_order_sn($arr_up_orderlist,$row["order_sn"]);
                        $num_data = $num_data +1;
                      }


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
                 $data_shipping=array(
                   'order_sn'=>$row["order_sn"],
                   'name'=>$row["recipient_address"]["name"],
                   'phone'=>$row["recipient_address"]["phone"],
                   'town'=>$row["recipient_address"]["town"],
                   'district'=>$row["recipient_address"]["district"],
                   'city'=>$row["recipient_address"]["city"],
                   'state'=>$row["recipient_address"]["state"],
                   'zipcode'=>$row["recipient_address"]["zipcode"],
                   'full_address'=>$row["recipient_address"]["full_address"],
                   'keygen' => $keygen
                  );

                  array_push($data_insert_shippinmg_address,$data_shipping);
                  
                 foreach($row["item_list"] as $row_items)
                  {

                     $data_orderitems=array(
                      'order_sn'=>$row["order_sn"],
                      'order_item_id'=>intval($row_items["order_item_id"]),
                      'item_sku'=>$row_items["item_sku"],
                      'item_name'=>$row_items["item_name"],
                      'model_id'=>intval($row_items["model_id"]),
                      'model_sku'=>$row_items["model_sku"],
                      'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                      'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                      'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                      'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                      'promotion_type'=>$row_items["promotion_type"],
                      'promotion_id'=>intval($row_items["promotion_id"]),
                      'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                      'keygen' => $keygen
                     );

                    if(!is_null($row["order_sn"])){

                        array_push($data_insertt_order_item,$data_orderitems);
                      
                    }else{

                      $datalog = array(
                          'log_type' => 1,
                          'log_code' => $log_code,
                          'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                          'log_status' => 1
                      );

                      $this->bnylog_model->insert($datalog);

                      break;
                    }
                  } //end foreach($row["item_list"] as $row_items)

            }else{//if(empty($arr_data_death)){
              //order death not death
              echo "<br>Death not Death >>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
              $arr_data_not_death = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
              if(empty($arr_data_not_death)){

                $data_order=array(
                  'order_sn'=>$row["order_sn"],
                  'taxinvoiceID'=>"",
                  'order_status'=>$row["order_status"],
                  'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                  'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                  'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                  'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                  'cancel_by'=>$row["cancel_by"],
                  'cancel_reason'=>$row["cancel_reason"],
                  'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                  'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                  'cancel_reason'=>$row["cancel_reason"],
                  'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                  'keygen' => $keygen
                );

                if(!is_null($row["order_sn"])){

                     // array_push($data_insert_order,$data_order);
                  $order_id_pre = $this->shopee_orders_model->insert($data_order);
                  $this->getEscrow($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_ctime,$row["order_status"],0);
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

                $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

                if(empty($arr_chk_items)){
                  $data_shipping=array(
                   'order_sn'=>$row["order_sn"],
                   'name'=>$row["recipient_address"]["name"],
                   'phone'=>$row["recipient_address"]["phone"],
                   'town'=>$row["recipient_address"]["town"],
                   'district'=>$row["recipient_address"]["district"],
                   'city'=>$row["recipient_address"]["city"],
                   'state'=>$row["recipient_address"]["state"],
                   'zipcode'=>$row["recipient_address"]["zipcode"],
                   'full_address'=>$row["recipient_address"]["full_address"],
                   'keygen' => $keygen
                  );

                  array_push($data_insert_shippinmg_address,$data_shipping);
                  
                 foreach($row["item_list"] as $row_items)
                  {

                     $data_orderitems=array(
                      'order_sn'=>$row["order_sn"],
                      'order_item_id'=>intval($row_items["order_item_id"]),
                      'item_sku'=>$row_items["item_sku"],
                      'item_name'=>$row_items["item_name"],
                      'model_id'=>intval($row_items["model_id"]),
                      'model_sku'=>$row_items["model_sku"],
                      'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                      'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                      'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                      'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                      'promotion_type'=>$row_items["promotion_type"],
                      'promotion_id'=>intval($row_items["promotion_id"]),
                      'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                      'keygen' => $keygen
                     );

                    if(!is_null($row["order_sn"])){

                        array_push($data_insertt_order_item,$data_orderitems);
                      
                    }else{

                      $datalog = array(
                          'log_type' => 1,
                          'log_code' => $log_code,
                          'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                          'log_status' => 1
                      );

                      $this->bnylog_model->insert($datalog);

                      break;
                    }
                  } //end foreach($row["item_list"] as $row_items)
                }//if(empty($arr_chk_items)){
              } //end if(empty($arr_data_not_death)){
            }

          }else{// else if($order_death){
            //order not death
            echo "<br>Not Death >>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
            $arr_data_not_death = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
            if(empty($arr_data_not_death)){

              $data_order=array(
                'order_sn'=>$row["order_sn"],
                'taxinvoiceID'=>"",
                'order_status'=>$row["order_status"],
                'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                'cancel_by'=>$row["cancel_by"],
                'cancel_reason'=>$row["cancel_reason"],
                'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                'cancel_reason'=>$row["cancel_reason"],
                'OrderListID'=>$order_list_sn_key_id_value_arr[$row["order_sn"]],
                'keygen' => $keygen
              );

              if(!is_null($row["order_sn"])){

                    //array_push($data_insert_order,$data_order);
                    $order_id_pre = $this->shopee_orders_model->insert($data_order);
                  $this->getEscrow($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_ctime,$row["order_status"],0);
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

              $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

              if(empty($arr_chk_items)){
                $data_shipping=array(
                 'order_sn'=>$row["order_sn"],
                 'name'=>$row["recipient_address"]["name"],
                 'phone'=>$row["recipient_address"]["phone"],
                 'town'=>$row["recipient_address"]["town"],
                 'district'=>$row["recipient_address"]["district"],
                 'city'=>$row["recipient_address"]["city"],
                 'state'=>$row["recipient_address"]["state"],
                 'zipcode'=>$row["recipient_address"]["zipcode"],
                 'full_address'=>$row["recipient_address"]["full_address"],
                 'keygen' => $keygen
                );

                array_push($data_insert_shippinmg_address,$data_shipping);
                
               foreach($row["item_list"] as $row_items)
                {

                   $data_orderitems=array(
                    'order_sn'=>$row["order_sn"],
                    'order_item_id'=>intval($row_items["order_item_id"]),
                    'item_sku'=>$row_items["item_sku"],
                    'item_name'=>$row_items["item_name"],
                    'model_id'=>intval($row_items["model_id"]),
                    'model_sku'=>$row_items["model_sku"],
                    'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                    'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                    'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                    'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                    'promotion_type'=>$row_items["promotion_type"],
                    'promotion_id'=>intval($row_items["promotion_id"]),
                    'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                    'keygen' => $keygen
                   );

                  if(!is_null($row["order_sn"])){

                      array_push($data_insertt_order_item,$data_orderitems);
                    
                  }else{

                    $datalog = array(
                        'log_type' => 1,
                        'log_code' => $log_code,
                        'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                        'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
                } //end foreach($row["item_list"] as $row_items)
              }//if(empty($arr_chk_items)){
            } //end if(empty($arr_data_not_death)){
          } // end if($order_death){
        } // end foreach($datas['response']['order_list'] as $row)

        //if($num_data==count($data_insert_order))
        //{

              //$this->shopee_orders_model->insert_all($data_insert_order);
              //$this->shopee_orderitems_model->insert_all($data_insertt_order_item);
              //$this->shopee_shipping_address_model->insert_all($data_insert_shippinmg_address);

              $sql_insert_item_batch = $this->db_util->insert_batch('shopee_orderitems',$data_insertt_order_item);
              $sql_insert_shippinmg_batch = $this->db_util->insert_batch('shopee_shipping_address',$data_insert_shippinmg_address);
             // echo $sql_insert_item_batch;
              $this->free_model->query_run($sql_insert_item_batch);
              $this->free_model->query_run($sql_insert_shippinmg_batch);
        //}
    }

    $this->shopee_order_list_model->update_status_complete();


}

function get_orders_from_dup()
{
    


   $go_for_insert=true;


    if($go_for_insert)
    {
      //echo "go for insert";
      //print_r($datas['response']['order_list']);

 
      $data_insert_order = array();
      $data_insert_shippinmg_address=array();
      $data_insertt_order_item=array();


      $keygen = $this->random_util->create_random_number(8);
      $num_data=0;

      $arr_orders = $this->shp_orders_migrate_model->select_list_sn(10);

      foreach($arr_orders as $row)
        {
          
          $array_status_death = array();
          $array_status_death = array('COMPLETED','CANCELLED');

          $orderlist_id_list = $order_list_sn_key_id_value_arr[$row["order_sn"]];
          $order_ctime = date('Y-m-d H:i:s',$row["create_time"]);
          $order_status = $row["order_status"];
          $estimated_shipping_fee = $row["estimated_shipping_fee"];

          $order_death = false;
          if (in_array($row["order_status"], $array_status_death)){
              $order_death = true;
          }
          if($order_death){
            echo "<br>Death>>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
            //order death
            //$search_status = array_search($row["order_status"], $array_status_death);
            //unset($array_status_death[$search_status]);
            $arr_data_death = $this->shopee_orders_model->get_by_not_status($row["order_sn"],$row["order_status"]);
            
            if(empty($arr_data_death)){
              //insert order death archive

               $data_order=array(
                'order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>$row["order_status"],
                    'estimated_shipping_fee'=> $row['estimated_shipping_fee'],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>$row["create_time"],
                    'keygen' => $keygen
                );

               $data_order_dup=array(
                'order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>'SHIPPED',
                    'estimated_shipping_fee'=> $row['estimated_shipping_fee'],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>$row["create_time"],
                    'keygen' => $keygen
                );

                if(!is_null($row["order_sn"])){

                      //array_push($data_insert_order,$data_order);
                      $order_id_pre = $this->shopee_orders_model->insert($data_order);
                      $this->getEscrowOld($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_status,$order_ctime,$estimated_shipping_fee);
                      //array_push($data_insert_order,$data_order_dup);
                      $make_cn = $this->get_shipping($row["order_sn"]);
                      if($make_cn){
                        $order_id_pre_dup = $this->shopee_orders_model->insert($data_order_dup);
                        $this->getEscrowOld($row["order_sn"],$order_id_pre_dup,$orderlist_id_list,'SHIPPED',$order_ctime,$estimated_shipping_fee);
                        $num_data = $num_data +2;

                      }else{
                        $arr_up_orderlist = array(
                          'is_death_status' => 1
                        );
                        $this->shopee_order_list_model->update_by_order_sn($arr_up_orderlist,$row["order_sn"]);
                        $num_data = $num_data +1;
                      }


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
                 $data_shipping=array(
                   'order_sn'=>$row["order_sn"],
                   'name'=>'',
                   'phone'=>'',
                   'town'=>'',
                   'district'=>'',
                   'city'=>'',
                   'state'=> '',
                   'zipcode'=> '',
                   'full_address'=> '',
                   'keygen' => $keygen
                  );

                  array_push($data_insert_shippinmg_address,$data_shipping);

                  $arr_order_items = $this->shp_order_item_migrate_model->select_by_sn($row["order_sn"]);
                  
                 foreach($arr_order_items as $row_items)
                  {

                    $data_orderitems=array(
                      'order_sn'=>$row["order_sn"],
                      'item_sku'=>$row_items["item_sku"],
                      'item_name'=>$row_items["item_name"],
                      'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                      'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                      'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                      'keygen' => $keygen

                     );

                    if(!is_null($row["order_sn"])){

                        array_push($data_insertt_order_item,$data_orderitems);
                      
                    }else{

                      $datalog = array(
                          'log_type' => 1,
                          'log_code' => $log_code,
                          'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                          'log_status' => 1
                      );

                      $this->bnylog_model->insert($datalog);

                      break;
                    }
                  } //end foreach($row["item_list"] as $row_items)

            }else{//if(empty($arr_data_death)){
              //order death not death
              echo "<br>Death not Death >>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
              $arr_data_not_death = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
              if(empty($arr_data_not_death)){

                $data_order=array(
                    'order_sn'=>$row["order_sn"],
                    'taxinvoiceID'=>"",
                    'order_status'=>$row["order_status"],
                    'estimated_shipping_fee'=> $row['estimated_shipping_fee'],
                    'cancel_reason'=>$row["cancel_reason"],
                    'create_time'=>$row["create_time"],
                    'keygen' => $keygen
                );

                if(!is_null($row["order_sn"])){

                     // array_push($data_insert_order,$data_order);
                  $order_id_pre = $this->shopee_orders_model->insert($data_order);
                  $this->getEscrow($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_ctime);
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

                $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

                if(empty($arr_chk_items)){
                  $data_shipping=array(
                   'order_sn'=>$row["order_sn"],
                   'name'=>'',
                   'phone'=>'',
                   'town'=>'',
                   'district'=>'',
                   'city'=>'',
                   'state'=> '',
                   'zipcode'=> '',
                   'full_address'=> '',
                   'keygen' => $keygen
                  );

                  array_push($data_insert_shippinmg_address,$data_shipping);

                  $arr_order_items = $this->shp_order_item_migrate_model->select_by_sn($row["order_sn"]);
                  
                 foreach($arr_order_items as $row_items)
                  {

                     $data_orderitems=array(
                      'order_sn'=>$row["order_sn"],
                      'item_sku'=>$row_items["item_sku"],
                      'item_name'=>$row_items["item_name"],
                      'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                      'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                      'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                      'keygen' => $keygen

                     );

                    if(!is_null($row["order_sn"])){

                        array_push($data_insertt_order_item,$data_orderitems);
                      
                    }else{

                      $datalog = array(
                          'log_type' => 1,
                          'log_code' => $log_code,
                          'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                          'log_status' => 1
                      );

                      $this->bnylog_model->insert($datalog);

                      break;
                    }
                  } //end foreach($row["item_list"] as $row_items)
                }//if(empty($arr_chk_items)){
              } //end if(empty($arr_data_not_death)){
            }

          }else{// else if($order_death){
            //order not death
            echo "<br>Not Death >>>".$row["order_sn"]."<>".$row["order_status"]."<br>";
            $arr_data_not_death = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);
            if(empty($arr_data_not_death)){

              $data_order=array(
                'order_sn'=>$row["order_sn"],
                'taxinvoiceID'=>"",
                'order_status'=>$row["order_status"],
                'estimated_shipping_fee'=> $row['estimated_shipping_fee'],
                'cancel_reason'=>$row["cancel_reason"],
                'create_time'=>$row["create_time"],
                'keygen' => $keygen
            );

              if(!is_null($row["order_sn"])){

                    //array_push($data_insert_order,$data_order);
                    $order_id_pre = $this->shopee_orders_model->insert($data_order);
                  $this->getEscrow($row["order_sn"],$order_id_pre,$orderlist_id_list,$order_ctime);
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

              $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

              if(empty($arr_chk_items)){
                $data_shipping=array(
                   'order_sn'=>$row["order_sn"],
                   'name'=>'',
                   'phone'=>'',
                   'town'=>'',
                   'district'=>'',
                   'city'=>'',
                   'state'=> '',
                   'zipcode'=> '',
                   'full_address'=> '',
                   'keygen' => $keygen
                  );

                array_push($data_insert_shippinmg_address,$data_shipping);

                $arr_order_items = $this->shp_order_item_migrate_model->select_by_sn($row["order_sn"]);
                
               foreach($arr_order_items as $row_items)
                {

                   $data_orderitems=array(
                      'order_sn'=>$row["order_sn"],
                      'item_sku'=>$row_items["item_sku"],
                      'item_name'=>$row_items["item_name"],
                      'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                      'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                      'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                      'keygen' => $keygen

                     );

                  if(!is_null($row["order_sn"])){

                      array_push($data_insertt_order_item,$data_orderitems);
                    
                  }else{

                    $datalog = array(
                        'log_type' => 1,
                        'log_code' => $log_code,
                        'log_note' => 'API shopee Order items Insert NULL Value Order Number = '.$row["order_sn"],
                        'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
                } //end foreach($row["item_list"] as $row_items)
              }//if(empty($arr_chk_items)){
            } //end if(empty($arr_data_not_death)){
          } // end if($order_death){
        } // end foreach($datas['response']['order_list'] as $row)

        //if($num_data==count($data_insert_order))
        //{

              //$this->shopee_orders_model->insert_all($data_insert_order);
              $this->shopee_orderitems_model->insert_all($data_insertt_order_item);
              $this->shopee_shipping_address_model->insert_all($data_insert_shippinmg_address);
        //}
    }



}

public function getDiscountList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_discountlist_is_finish'] == 0){
  if($download_arr['shopee_discountlist_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_discountlist_page_no'=>intval(1),
        'shopee_discountlist_page_size'=>intval(100)
      );

      //print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db
 
      $shopee_discount_page_no=$download_arr["shopee_discountlist_page_no"];
      $shopee_discount_page_size=$download_arr["shopee_discountlist_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/discount/get_discount_list","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'discount_status' => 'expired',
        'page_no' => $shopee_discount_page_no,
        'page_size' => $shopee_discount_page_size,
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();
       
    //print_r($datas['response']);
    //print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['discount_list'])==0){

              $data=array(
                'shopee_discountlist_page_no'=>intval(1),
                'shopee_discountlist_page_size'=>intval(100),
                'shopee_discountlist_is_finish' => intval(1)
              );

              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }else{

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['discount_list'] as $row)
              {

                $datainsert=array(
                  'status'=>$row['status'],
                  'discount_name'=>$row['discount_name'],
                  'discount_id'=>$row['discount_id'],
                  'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['end_time'])

                );

                if(!is_null($row['discount_id']))
                  {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

                  }else{

                    $datalog = array(
                    'log_type' => 2,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API Shopee Discount list Insert NULL Value ',
                    'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
              }
              if($num_data == count($arr_datainsert)) // all success we insert
                  {
                    $this->shopee_discount_list_model->insert_all($arr_datainsert);
                  }

              $page_no = 0;
              if($datas['response']['more'] == true){

                $page_no = $shopee_discount_page_no+1;

              }    

              $data=array(
                  'shopee_discountlist_page_no'=>$page_no,
                  'shopee_discountlist_page_size'=>intval(100),
                  'shopee_discountlist_is_finish' => intval(1)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

  function getDiscount(){
    $arr_discounts = $this->shopee_discount_list_model->select_all();
    if(!empty($arr_discounts)){
      $arr_discount_id=array();
      $discount_id_list="";
      foreach($arr_discounts as $arr_discount)
      {
        $arr_discount_id[$arr_discount['discount_id']]=$arr_discount['DiscountListID'];
        if($discount_id_list!="")
          {
            $discount_id_list=$discount_id_list.",".$arr_discount['discount_id'];
          }
        else
          {
            $discount_id_list=$arr_discount['discount_id'];
          }
      }
    }//if(!empty($arr_discount))


    print_r($arr_discount_id);

    echo $discount_id_list;

    if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/discount/get_discount","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'discount_id' => '1024172523',
        'page_no' => 1,
        'page_size' => 100,
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();

      print_r($datas['response']);

      if(count($datas['response']) > 0){

       // foreach($datas['response'] as $row)
        //  {
        $row = $datas['response'];

          $data_insert = array(
            'DiscountListID' => $arr_discount_id[$row['discount_id']],
            'discount_id' => $row['discount_id'],
            'status' => $row['status'],
            'discount_name' => $row['discount_name'],
            'start_time' => date("Y-m-d H:i:s",$row['start_time']),
            'end_time' => date("Y-m-d H:i:s",$row['end_time']),
            'source' => $row['source']
          );
       // }//foreach($datas['response'] as $row)
        print_r($data_insert);
        $this->shopee_discounts_model->insert($data_insert);
      } //if(count($datas['response']) > 0)
  }

public function getVoucherList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_voucherlist_is_finish'] == 0){
  if($download_arr['shopee_voucherlist_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_voucherlist_page_no'=>intval(1),
        'shopee_voucherlist_page_size'=>intval(100)
      );

      //print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db
 
      $shopee_voucherlist_page_no=$download_arr["shopee_voucherlist_page_no"];
      $shopee_voucherlist_page_size=$download_arr["shopee_voucherlist_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/voucher/get_voucher_list","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'status' => 'all',
        'page_no' => $shopee_voucherlist_page_no,
        'page_size' => $shopee_voucherlist_page_size,
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();
       
    print_r($datas['response']);
    //print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['voucher_list'])==0){

              $data=array(
                'shopee_voucherlist_page_no'=>intval(1),
                'shopee_voucherlist_page_size'=>intval(100),
                'shopee_voucherlist_is_finish' => intval(1)
              );

              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }else{

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['voucher_list'] as $row)
              {
                $discount_amount = 0;
                if(!empty($row['discount_amount'])){
                  $discount_amount = $row['discount_amount'];
                }

                $datainsert=array(
                  'voucher_id'=>$row['voucher_id'],
                  'voucher_code'=>$row['voucher_code'],
                  'voucher_name'=>$row['voucher_name'],
                  'voucher_type'=>$row['voucher_type'],
                  'reward_type'=>$row['reward_type'],
                  'usage_quantity'=>$row['usage_quantity'],
                  'current_usage'=>$row['current_usage'],
                  'is_admin'=>$row['is_admin'],
                  'voucher_purpose'=>$row['voucher_purpose'],
                  'discount_amount'=>$discount_amount,
                  'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['end_time'])

                );

                if(!is_null($row['voucher_id']))
                  {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

                  }else{

                    $datalog = array(
                    'log_type' => 2,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API Shopee Discount list Insert NULL Value ',
                    'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
              }
              if($num_data == count($arr_datainsert)) // all success we insert
                  {
                    $this->shopee_voucher_list_model->insert_all($arr_datainsert);
                  }

              $page_no = 1;
              if($datas['response']['more'] == true){

                $page_no = $shopee_discount_page_no+1;

              }    

              $data=array(
                  'shopee_voucherlist_page_no'=>$page_no,
                  'shopee_voucherlist_page_size'=>intval(100),
                  'shopee_voucherlist_is_finish' => intval(1)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

  function getVoucher(){
    $arr_vouchers = $this->shopee_voucher_list_model->select_all();
    if(!empty($arr_vouchers)){
      $arr_voucher_id=array();
      $voucher_id_list="";
      foreach($arr_vouchers as $arr_voucher)
      {
        $arr_voucher_id[$arr_voucher['voucher_id']]=$arr_voucher['VouchertListID'];
        if($voucher_id_list!="")
          {
            $voucher_id_list=$voucher_id_list.",".$arr_voucher['voucher_id'];
          }
        else
          {
            $voucher_id_list=$arr_voucher['voucher_id'];
          }
      }
    }//if(!empty($arr_voucher))


    //print_r($arr_voucher_id);

    echo $voucher_id_list;

    if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/voucher/get_voucher","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'voucher_id' => '84104038'
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();

      print_r($datas['response']);

      if(count($datas['response']) > 0){

        //foreach($datas['response'] as $row)
          //{
        $row = $datas['response'];

        $discount_amount = 0;
        if(!empty($row['discount_amount'])){
          $discount_amount = $row['discount_amount'];
        }

        $cmt_voucher_status = 0;
        if(!empty($row['cmt_voucher_status'])){
          $cmt_voucher_status = $row['cmt_voucher_status'];
        }

          $data_insert = array(
            'VouchertListID' => $arr_voucher_id[$row['voucher_id']],
            'voucher_id' => $row['voucher_id'],
            'voucher_code' => $row['voucher_code'],
            'voucher_name' => $row['voucher_name'],
            'voucher_type' => $row['voucher_type'],
            'reward_type' => $row['reward_type'],
            'usage_quantity' => $row['usage_quantity'],
            'current_usage' => $row['current_usage'],
            'is_admin' => $row['is_admin'],
            'voucher_purpose' => $row['voucher_purpose'],
            'discount_amount' => $discount_amount,
            'cmt_voucher_status' => $cmt_voucher_status,
            'min_basket_price' => $row['min_basket_price'],
            'start_time' => date("Y-m-d H:i:s",$row['start_time']),
            'end_time' => date("Y-m-d H:i:s",$row['end_time'])
          );
        //}//foreach($datas['response'] as $row)
        print_r($data_insert);
        $this->shopee_voucher_model->insert($data_insert);
      } //if(count($datas['response']) > 0)
  }

public function getFollowPrizeList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_follow_prize_is_finish'] == 0){
  if($download_arr['shopee_follow_prize_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_follow_prize_page_no'=>intval(1),
        'shopee_follow_prize_page_size'=>intval(100)
      );

      //print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db
 
      $shopee_follow_prize_page_no=$download_arr["shopee_follow_prize_page_no"];
      $shopee_follow_prize_page_size=$download_arr["shopee_follow_prize_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/follow_prize/get_follow_prize_list","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'status' => 'all',
        'page_no' => $shopee_follow_prize_page_no,
        'page_size' => $shopee_follow_prize_page_size,
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();
       
    print_r($datas['response']);
    //print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['follow_prize_list'])==0){

              $data=array(
                'shopee_follow_prize_page_no'=>intval(1),
                'shopee_follow_prize_page_size'=>intval(100),
                'shopee_follow_prize_is_finish' => intval(1)
              );

              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }else{

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['follow_prize_list'] as $row)
              {


                $datainsert=array(
                  'campaign_id'=>$row['campaign_id'],
                  'campaign_status'=>$row['campaign_status'],
                  'follow_prize_name'=>$row['follow_prize_name'],
                  'usage_quantity'=>$row['usage_quantity'],
                  'claimed'=>$row['claimed'],
                  'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['end_time'])

                );

                if(!is_null($row['campaign_id']))
                  {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

                  }else{

                    $datalog = array(
                    'log_type' => 2,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API Shopee Discount list Insert NULL Value ',
                    'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
              }
              if($num_data == count($arr_datainsert)) // all success we insert
                  {
                    $this->shopee_follow_prize_list_model->insert_all($arr_datainsert);
                  }

              $page_no = 1;
              if($datas['response']['more'] == true){

                $page_no = $shopee_discount_page_no+1;

              }    

              $data=array(
                  'shopee_follow_prize_page_no'=>$page_no,
                  'shopee_follow_prize_page_size'=>intval(100),
                  'shopee_follow_prize_is_finish' => intval(1)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

  function getFollowPrize(){
    $arr_follow_prizes = $this->shopee_follow_prize_list_model->select_all();
    if(!empty($arr_follow_prizes)){
      $arr_follow_prize_id=array();
      $follow_prize_id_list="";
      foreach($arr_follow_prizes as $arr_follow_prize)
      {
        $arr_follow_prize_id[$arr_follow_prize['campaign_id']]=$arr_follow_prize['FollowPrizeListID'];
        if($follow_prize_id_list!="")
          {
            $follow_prize_id_list=$follow_prize_id_list.",".$arr_follow_prize['campaign_id'];
          }
        else
          {
            $follow_prize_id_list=$arr_follow_prize['campaign_id'];
          }
      }
    }//if(!empty($arr_voucher))


    print_r($arr_follow_prize_id);

    echo $follow_prize_id_list;

    if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/follow_prize/get_follow_prize_detail","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'campaign_id' => '587029'
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();

      print_r($datas['response']);

      if(count($datas['response']) > 0){

        //foreach($datas['response'] as $row)
          //{
        $row = $datas['response'];

          $data_insert = array(
            'FollowPrizeListID' => $arr_follow_prize_id[$row['campaign_id']],
            'campaign_id' => $row['campaign_id'],
            'campaign_status' => $row['campaign_status'],
            'usage_quantity' => $row['usage_quantity'],
            'min_spend' => $row['min_spend'],
            'reward_type' => $row['reward_type'],
            'follow_prize_name' => $row['follow_prize_name'],
            'percentage' => $row['percentage'],
            'max_price' => $row['max_price'],
            'start_time' => date("Y-m-d H:i:s",$row['start_time']),
            'end_time' => date("Y-m-d H:i:s",$row['end_time'])
          );
        //}//foreach($datas['response'] as $row)
        print_r($data_insert);
        $this->shopee_follow_prize_model->insert($data_insert);
      } //if(count($datas['response']) > 0)
  }

public function getBundleDealList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_bundle_deallist_is_finish'] == 0){
  if($download_arr['shopee_bundle_deallist_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_bundle_deallist_page_no'=>intval(1),
        'shopee_bundle_deallist_page_size'=>intval(100)
      );

      //print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db
 
      $shopee_bundle_deallist_page_no=$download_arr["shopee_bundle_deallist_page_no"];
      $shopee_bundle_deallist_page_size=$download_arr["shopee_bundle_deallist_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/bundle_deal/get_bundle_deal_list","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'time_status' => 1,
        'page_no' => $shopee_bundle_deallist_page_no,
        'page_size' => $shopee_bundle_deallist_page_size,
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();
       
    print_r($datas['response']);
    //print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['bundle_deal_list'])==0){

              $data=array(
                'shopee_bundle_deallist_page_no'=>intval(1),
                'shopee_bundle_deallist_page_size'=>intval(100),
                'shopee_bundle_deallist_is_finish' => intval(1)
              );

              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }else{

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['bundle_deal_list'] as $row)
              {


                $datainsert=array(
                  'name'=>$row['name'],
                  'bundle_deal_id'=>$row['bundle_deal_id'],
                  'purchase_limit'=>$row['purchase_limit'],
                  'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['end_time'])

                );

                if(!is_null($row['bundle_deal_id']))
                  {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

                  }else{

                    $datalog = array(
                    'log_type' => 2,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API Shopee Discount list Insert NULL Value ',
                    'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
              }
              if($num_data == count($arr_datainsert)) // all success we insert
                  {
                    $this->shopee_bundle_deal_list_model->insert_all($arr_datainsert);
                  }

              $page_no = 1;
              if($datas['response']['more'] == true){

                $page_no = $shopee_discount_page_no+1;

              }    

              $data=array(
                  'shopee_bundle_deallist_page_no'=>$page_no,
                  'shopee_bundle_deallist_page_size'=>intval(100),
                  'shopee_bundle_deallist_is_finish' => intval(1)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

  function getBundleDeal(){
    $arr_bundle_deals = $this->shopee_bundle_deal_list_model->select_all();
    if(!empty($arr_bundle_deals)){
      $arr_bundle_deal_id=array();
      $bundle_deal_id_list="";
      foreach($arr_bundle_deals as $arr_bundle_deal)
      {
        $arr_bundle_deal_id[$arr_bundle_deal['bundle_deal_id']]=$arr_bundle_deal['BundleDealListID'];
        if($bundle_deal_id_list!="")
          {
            $bundle_deal_id_list=$bundle_deal_id_list.",".$arr_bundle_deal['bundle_deal_id'];
          }
        else
          {
            $bundle_deal_id_list=$arr_bundle_deal['bundle_deal_id'];
          }
      }
    }//if(!empty($arr_voucher))


    print_r($arr_bundle_deal_id);

    echo $bundle_deal_id_list;

    if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/bundle_deal/get_bundle_deal","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'bundle_deal_id' => '9388945'
      );
      $this->shopeeapi->setData($data);

      $datas=$this->shopeeapi->execute();

      print_r($datas['response']);

      if(count($datas['response']) > 0){

        //foreach($datas['response'] as $row)
          //{
        $row = $datas['response'];

          $data_insert = array(
            'BundleDealListID' => $arr_bundle_deal_id[$row['bundle_deal_id']],
            'bundle_deal_id' => $row['bundle_deal_id'],
            'name' => $row['name'],
            'purchase_limit' => $row['purchase_limit'],
            'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
            'end_time'=>date("Y-m-d H:i:s",$row['end_time'])
          );
        //}//foreach($datas['response'] as $row)
        print_r($data_insert);
        $this->shopee_bundle_deal_model->insert($data_insert);
      } //if(count($datas['response']) > 0)
  }

public function getAddonDealList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_addon_deallist_is_finish'] == 0){
  if($download_arr['shopee_addon_deallist_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_addon_deallist_page_no'=>intval(1),
        'shopee_addon_deallist_page_size'=>intval(100)
      );

      //print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db
 
      $shopee_addon_deallist_page_no=$download_arr["shopee_addon_deallist_page_no"];
      $shopee_addon_deallist_page_size=$download_arr["shopee_addon_deallist_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/bundle_deal/get_addon_deal_list","get")))
      {
        die("canot init shopee api");
      }


      $data=array(
        'promotion_status' => 'all',
        'page_no' => $shopee_addon_deallist_page_no,
        'page_size' => $shopee_addon_deallist_page_size,
      );
      $this->shopeeapi->setData($data);


      $datas=$this->shopeeapi->execute();
       
    print_r($datas['response']);
    print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['add_on_deal_list'])==0){

              $data=array(
                'shopee_addon_deallist_page_no'=>intval(1),
                'shopee_addon_deallist_page_size'=>intval(100),
                'shopee_addon_deallist_is_finish' => intval(1)
              );

              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }else{

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['add_on_deal_list'] as $row)
              {


                $datainsert=array(
                  'purchase_min_spend'=>$row['purchase_min_spend'],
                  'promotion_type'=>$row['promotion_type'],
                  'source'=>$row['source'],
                  'add_on_deal_id'=>$row['add_on_deal_id'],
                  'add_on_deal_name'=>$row['add_on_deal_name'],
                  'per_gift_num'=>$row['per_gift_num'],
                  'promotion_purchase_limit'=>$row['promotion_purchase_limit'],
                  'start_time'=>date("Y-m-d H:i:s",$row['start_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['end_time'])

                );

                if(!is_null($row['add_on_deal_id']))
                  {

                    array_push($arr_datainsert,$datainsert);
                    $num_data = $num_data +1;

                  }else{

                    $datalog = array(
                    'log_type' => 2,
                    'log_code' => 'NULLORnum',
                    'log_note' => 'API Shopee Discount list Insert NULL Value ',
                    'log_status' => 1
                    );

                    $this->bnylog_model->insert($datalog);

                    break;
                  }
              }
              if($num_data == count($arr_datainsert)) // all success we insert
                  {
                    $this->shopee_addon_deal_list_model->insert_all($arr_datainsert);
                  }

              $page_no = 1;
              if($datas['response']['more'] == true){

                $page_no = $shopee_discount_page_no+1;

              }    

              $data=array(
                  'shopee_addon_deallist_page_no'=>$page_no,
                  'shopee_addon_deallist_page_size'=>intval(100),
                  'shopee_addon_deallist_is_finish' => intval(1)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

function chk_date_getEscrow(){

    $arr_date = $this->shopee_orders_model->select_chk_date_escrow();
   // echo $arr_date['cntdate'];
    if(!empty($arr_date)){
        if($arr_date['cntdate'] < 1){
            //Get Now Data
            // $this->makeEscrow();
          $this->makeEscrowOld();
          //echo "makeEscrow";
        }else{
            //Get Old Data
             $this->makeEscrowOld();
          //echo "makeEscrowOld";
        }
    }
} 


  function makeEscrowOld(){
    for($i=1;$i<=20;$i++)
    {

      $this->getEscrowOld();
      ob_flush();
        //flush();
        usleep(1000);
      //sleep(1);
      
    }
    ob_end_flush();
  }

  //function escrowex($order_sn,$order_id_pre,$orderlist_id_list,$order_status,$order_ctime){
function escrowex(){
   $arr_escrow = $this->shopee_orders_model->last_order();
//print_r($arr_escrow);
   echo $arr_escrow['create_time']."<br>";
   $date=date_create($arr_escrow['create_time']);
   echo date_format($date,"Y-m");

  }

  function getEscrowOld($order_sn,$order_id_pre,$orderlist_id_list,$order_status,$order_ctime,$estimated_shipping_fee,$old_status){
   // $arr_escrow = $this->shopee_orders_model->get_next_sn_dup();

    if(!empty($order_sn)){

      //$download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);
      //$shopee_escrow_page_no=$download_arr["shopee_escrow_page_no"];
      //$shopee_escrow_page_size=$download_arr["shopee_escrow_page_size"];

     /* $order_sn=$arr_escrow['order_sn'];
      $order_id_pre=$arr_escrow['OrderID'];
      $orderlist_id_list=$arr_escrow['OrderListID'];
      $order_status=$arr_escrow['order_status'];
      $order_ctime = $arr_escrow['create_time'];*/

      $create_time=date_create($order_ctime);
      $order_yymm = date_format($create_time,"Y-m");

      $keygen = $this->random_util->create_random_number(8);

      echo "order_no>>".$order_sn."<<Orderlist_id>>".$orderlist_id_list;

      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/payment/get_escrow_detail","get")))
        {
          die("canot init shopee api");
        }


        $data=array(
          'order_sn' => $order_sn
        );
        $this->shopeeapi->setData($data);

        $datas=$this->shopeeapi->execute();

        //print_r($datas['response']);

        if(count($datas['response']) > 0){

          $row = $datas['response'];

            $data_insert = array(
              'OrderID' => $order_id_pre,
              'OrderListID' => $orderlist_id_list,
              'order_sn' => $row['order_sn'],
              'buyer_user_name' => $row['buyer_user_name'],
              'return_order_sn_list' => '',
              'keygen' => $keygen
            );

          //print_r($data_insert);
         $esceow_id =  $this->shopee_escrow_detail_model->insert($data_insert);

         if(!empty($esceow_id)){

            if(count($row['order_income']) > 0){
              $keygen_order_income = $this->random_util->create_random_number(8);
              $order_income = $row['order_income'];

              $buyer_paid_shipping_fee = "";  
              

              if($order_status == "CANCELLED"){
                $buyer_paid_shipping_fee = $estimated_shipping_fee;
              }

              if($order_status == "COMPLETED"){
                if(!is_null($order_income['buyer_paid_shipping_fee'])){
                    $buyer_paid_shipping_fee=$order_income['buyer_paid_shipping_fee'];
                  }
              }

              if($order_status == "PROCESSED"){

                if($old_status == "CANCELLED"){
                  $buyer_paid_shipping_fee = $estimated_shipping_fee;
                }else{
                 if(!is_null($order_income['buyer_paid_shipping_fee'])){
                    $buyer_paid_shipping_fee=$order_income['buyer_paid_shipping_fee'];
                  }
                }  

                /* $arr_lastorder = $this->shopee_taxinvoiceid_model->last_order_code_by_yymm($order_yymm);   

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$order_ctime);  

                 $arr_new_invoice_id = array(
                  'order_sn' => $order_sn,
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $order_ctime
                 );

                 print_r($arr_new_invoice_id);

                 $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                 if(empty($chk_taxinvoiceid)){
                     $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                 }


               }else{

                $new_textinvoiceID = $this->shopee_bl->get_shopee_code('no',$order_ctime);  

                 $arr_new_invoice_id = array(
                  'order_sn' => $order_sn,
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $order_ctime
                 );

                 print_r($arr_new_invoice_id);

                 $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                 if(empty($chk_taxinvoiceid)){
                     $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                 }

               }*/

               $arr_new_invoice_id = array(
                  'order_sn' => $order_sn,
                  'taxinvoiceID' => '',
                  'create_time' => $order_ctime
                );

               $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                 if(empty($chk_taxinvoiceid)){
                     $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                 }

              }
                $datainsert=array(
                  'OrderListID' => $orderlist_id_list,
                  'EscrowID' => $esceow_id,
                  'order_sn' => $row['order_sn'],
                  'escrow_amount'=>(!is_null($order_income['escrow_amount']))?$order_income['escrow_amount']:"",
                  'buyer_total_amount'=>(!is_null($order_income['buyer_total_amount']))?$order_income['buyer_total_amount']:"",
                  'original_price'=>(!is_null($order_income['original_price']))?$order_income['original_price']:"",
                  'seller_discount'=>(!is_null($order_income['seller_discount']))?$order_income['seller_discount']:"",
                  'shopee_discount'=>(!is_null($order_income['shopee_discount']))?$order_income['shopee_discount']:"",
                  'voucher_from_seller'=>(!is_null($order_income['voucher_from_seller']))?$order_income['voucher_from_seller']:"",
                  'voucher_from_shopee'=>(!is_null($order_income['voucher_from_shopee']))?$order_income['voucher_from_shopee']:"",
                  'coins'=>(!is_null($order_income['coins']))?$order_income['coins']:"",
                  'buyer_paid_shipping_fee'=>$buyer_paid_shipping_fee,
                  'buyer_transaction_fee'=>(!is_null($order_income['buyer_transaction_fee']))?$order_income['buyer_transaction_fee']:"",
                  'cross_border_tax'=>(!is_null($order_income['cross_border_tax']))?$order_income['cross_border_tax']:"",
                  'payment_promotion'=>(!is_null($order_income['payment_promotion']))?$order_income['payment_promotion']:"",
                  'commission_fee'=>(!is_null($order_income['commission_fee']))?$order_income['commission_fee']:"",
                  'service_fee'=>(!is_null($order_income['service_fee']))?$order_income['service_fee']:"",
                  'seller_transaction_fee'=>(!is_null($order_income['seller_transaction_fee']))?$order_income['seller_transaction_fee']:"",
                  'seller_lost_compensation'=>(!is_null($order_income['seller_lost_compensation']))?$order_income['seller_lost_compensation']:"",
                  'seller_coin_cash_back'=>(!is_null($order_income['seller_coin_cash_back']))?$order_income['seller_coin_cash_back']:"",
                  'escrow_tax'=>(!is_null($order_income['escrow_tax']))?$order_income['escrow_tax']:"",
                  'final_shipping_fee'=>(!is_null($order_income['final_shipping_fee']))?$order_income['final_shipping_fee']:"",
                  'actual_shipping_fee'=>(!is_null($order_income['actual_shipping_fee']))?$order_income['actual_shipping_fee']:"",
                  'shopee_shipping_rebate'=>(!is_null($order_income['shopee_shipping_rebate']))?$order_income['shopee_shipping_rebate']:"",
                  'shipping_fee_discount_from_3pl'=>(!is_null($order_income['shipping_fee_discount_from_3pl']))?$order_income['shipping_fee_discount_from_3pl']:"",
                  'seller_shipping_discount'=>(!is_null($order_income['seller_shipping_discount']))?$order_income['seller_shipping_discount']:"",
                  'estimated_shipping_fee'=>(!is_null($order_income['estimated_shipping_fee']))?$order_income['estimated_shipping_fee']:"",
                  'seller_voucher_code'=> "",
                  'drc_adjustable_refund'=>(!is_null($order_income['drc_adjustable_refund']))?$order_income['drc_adjustable_refund']:"",
                  'cost_of_goods_sold'=>(!is_null($order_income['cost_of_goods_sold']))?$order_income['cost_of_goods_sold']:"",
                  'original_cost_of_goods_sold'=>(!is_null($order_income['original_cost_of_goods_sold']))?$order_income['original_cost_of_goods_sold']:"",
                  'original_shopee_discount'=>(!is_null($order_income['original_shopee_discount']))?$order_income['original_shopee_discount']:"",
                  'seller_return_refund'=>(!is_null($order_income['seller_return_refund']))?$order_income['seller_return_refund']:"",
                  'keygen'=>$keygen_order_income
                );

                $esceow_income_id  = $this->shopee_escrow_order_income_model->insert($datainsert);

                if(!empty($esceow_income_id)){

                  $order_income_items = $order_income['items'];
                  $num_item = 0;
                  $arr_item_insert = array();
                  $cnt_item = count($order_income_items);

                  if($cnt_item > 0){
                    $keygen_item = $this->random_util->create_random_number(8);
                    foreach($order_income_items as $order_income_item){
                      $arr_item = array(
                        'OrderListID' => $orderlist_id_list,
                        'EscrowID' => $esceow_id,
                        'EscrowOrderIncomeID' => $esceow_income_id,
                        'order_sn' => $order_sn,
                        'item_id' =>(!is_null($order_income_item['item_id']))?$order_income_item['item_id']:"",
                        'item_name' =>(!is_null($order_income_item['item_name']))?$order_income_item['item_name']:"",
                        'item_sku' =>(!is_null($order_income_item['item_sku']))?$order_income_item['item_sku']:"",
                        'model_id' =>(!is_null($order_income_item['model_id']))?$order_income_item['model_id']:"",
                        'model_name' =>(!is_null($order_income_item['model_name']))?$order_income_item['model_name']:"",
                        'model_sku' =>(!is_null($order_income_item['model_sku']))?$order_income_item['model_sku']:"",
                        'original_price' =>(!is_null($order_income_item['original_price']))?$order_income_item['original_price']:"",
                        'discounted_price' =>(!is_null($order_income_item['discounted_price']))?$order_income_item['discounted_price']:"",
                        'discount_from_coin' =>(!is_null($order_income_item['discount_from_coin']))?$order_income_item['discount_from_coin']:"",
                        'discount_from_voucher_shopee' =>(!is_null($order_income_item['discount_from_voucher_shopee']))?$order_income_item['discount_from_voucher_shopee']:"",
                        'discount_from_voucher_seller' =>(!is_null($order_income_item['discount_from_voucher_seller']))?$order_income_item['discount_from_voucher_seller']:"",
                        'activity_type' =>(!is_null($order_income_item['activity_type']))?$order_income_item['activity_type']:"",
                        'activity_id' =>(!is_null($order_income_item['activity_id']))?$order_income_item['activity_id']:"",
                        'is_main_item' =>(!is_null($order_income_item['is_main_item']))?$order_income_item['is_main_item']:"",
                        'keygen'=>$keygen_item
                      );

                      array_push($arr_item_insert,$arr_item);

                      /*$chk_item = $this->shopee_escrow_items_model->get_by_id_sn($order_income_item['item_id'],$order_sn);

                      if(empty($chk_item)){
                        $this->shopee_escrow_items_model->insert($arr_item);
                      }*/
                      $num_item = $num_item+1;
                    }

                    if($cnt_item == $num_item){
                      $this->shopee_escrow_items_model->insert_all($arr_item_insert);

                    }else{

                      $this->shopee_escrow_detail_model->del_by_order_sn($order_sn);
                      $this->shopee_escrow_order_income_model->del_by_order_sn($order_sn);

                      $datalog = array(
                        'log_type' => 2,
                        'log_code' => 'NULLORnum',
                        'log_note' => 'API Shopee Not insert',
                        'log_status' => 1
                        );

                      $this->bnylog_model->insert($datalog);

                      die("API Shopee Not insert");
                    }

                  }//if($cnt_item > 0)

                }//if(!empty($esceow_income_id)){
            }//if(count($row['order_income']) > 0){
         }//if(!empty($esceow_id))
      } //if(count($datas['response']) > 0)
    }else{ //if(!empty($arr_escrow))

      $datalog = array(
        'log_type' => 2,
        'log_code' => 'NULLORnum',
        'log_note' => 'API Shopee No data from shopee data list',
        'log_status' => 1
        );

      $this->bnylog_model->insert($datalog);

      die("No data from shopee data list");

    }
  }

  function makeEscrow(){
    for($i=1;$i<=50;$i++)
    {

      $this->getEscrow();
      ob_flush();
        //flush();
        usleep(1000);
      //sleep(1);
      
    }
    ob_end_flush();
  }

  function getEscrow($order_sn,$order_id_pre,$orderlist_id_list,$order_ctime,$order_status,$from_more){
    $arr_escrow = $this->shopee_orders_model->get_next_sn();
    if(!empty($arr_escrow)){

      //$download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);
      //$shopee_escrow_page_no=$download_arr["shopee_escrow_page_no"];
      //$shopee_escrow_page_size=$download_arr["shopee_escrow_page_size"];

      /*$order_sn=$arr_escrow['order_sn'];
      $order_id_pre=$arr_escrow['OrderID'];
      $orderlist_id_list=$arr_escrow['OrderListID'];*/
      $create_time=date_create($order_ctime);
      $order_yymm = date_format($create_time,"Y-m");
      $keygen = $this->random_util->create_random_number(8);

      echo "order_no>>".$order_sn."<<Orderlist_id>>".$orderlist_id_list;

      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/payment/get_escrow_detail","get")))
        {
          die("canot init shopee api");
        }


        $data=array(
          'order_sn' => $order_sn
        );
        $this->shopeeapi->setData($data);

        $datas=$this->shopeeapi->execute();

        print_r($datas['response']);

        if(count($datas['response']) > 0){

          $row = $datas['response'];

            $data_insert = array(
              'OrderID' => $order_id_pre,
              'OrderListID' => $orderlist_id_list,
              'order_sn' => $row['order_sn'],
              'buyer_user_name' => $row['buyer_user_name'],
              'return_order_sn_list' => '',
              'keygen' => $keygen
            );

          print_r($data_insert);
         $esceow_id =  $this->shopee_escrow_detail_model->insert($data_insert);

         if(!empty($esceow_id)){

            if(count($row['order_income']) > 0){
              $keygen_order_income = $this->random_util->create_random_number(8);
              $order_income = $row['order_income'];

              $status_from = "SHIPPED";
              if($from_more == 1){
                $status_from = "PROCESSED";
              }

              if($from_more == 1){
                $arr_status = array('READY_TO_SHIP');

                 if (in_array($order_status, $arr_status)){

                /*$arr_lastorder = $this->shopee_taxinvoiceid_model->last_order_code_by_yymm($order_yymm);   

                 if(!empty($arr_lastorder)){

                   $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$order_ctime);  

                   $arr_new_invoice_id = array(
                    'order_sn' => $order_sn,
                    'taxinvoiceID' => $new_textinvoiceID,
                    'create_time' => $order_ctime
                   );

                   print_r($arr_new_invoice_id);

                   $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                    if(empty($chk_taxinvoiceid)){

                       $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                    }


                 }else{

                  $new_textinvoiceID = $this->shopee_bl->get_shopee_code('no',$order_ctime);  

                   $arr_new_invoice_id = array(
                    'order_sn' => $order_sn,
                    'taxinvoiceID' => $new_textinvoiceID,
                    'create_time' => $order_ctime
                   );

                   print_r($arr_new_invoice_id);
                   $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                    if(empty($chk_taxinvoiceid)){

                       $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                   }

                 }*/

                 $arr_new_invoice_id = array(
                    'order_sn' => $order_sn,
                    'taxinvoiceID' => '',
                    'create_time' => $order_ctime
                   );

                 $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                    if(empty($chk_taxinvoiceid)){

                       $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                   }

                }
              }


              
              if($from_more == 0){
                $arr_status = array('SHIPPED','PROCESSED','TO_CONFIRM_RECEIVE','READY_TO_SHIP');

                 if (in_array($order_status, $arr_status)){

                 /*  $arr_lastorder = $this->shopee_taxinvoiceid_model->last_order_code_by_yymm($order_yymm);   

                 if(!empty($arr_lastorder)){

                   $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$order_ctime);  

                   $arr_new_invoice_id = array(
                    'order_sn' => $order_sn,
                    'taxinvoiceID' => $new_textinvoiceID,
                    'create_time' => $order_ctime
                   );

                   print_r($arr_new_invoice_id);

                   $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                    if(empty($chk_taxinvoiceid)){

                       $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                   }


                 }else{

                  $new_textinvoiceID = $this->shopee_bl->get_shopee_code('no',$order_ctime);  

                   $arr_new_invoice_id = array(
                    'order_sn' => $order_sn,
                    'taxinvoiceID' => $new_textinvoiceID,
                    'create_time' => $order_ctime
                   );

                   print_r($arr_new_invoice_id);

                   $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                    if(empty($chk_taxinvoiceid)){

                       $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                   }

                 }*/

                 $arr_new_invoice_id = array(
                  'order_sn' => $order_sn,
                  'taxinvoiceID' => '',
                  'create_time' => $order_ctime
                 );

               $chk_taxinvoiceid = $this->shopee_taxinvoiceid_model->select_by_sn($order_sn);

                  if(empty($chk_taxinvoiceid)){

                     $this->shopee_taxinvoiceid_model->insert($arr_new_invoice_id);
                 }

                }

                
              }


                $datainsert=array(
                  'OrderListID' => $orderlist_id_list,
                  'EscrowID' => $esceow_id,
                  'order_sn' => $row['order_sn'],
                  'escrow_amount'=>(!is_null($order_income['escrow_amount']))?$order_income['escrow_amount']:"",
                  'buyer_total_amount'=>(!is_null($order_income['buyer_total_amount']))?$order_income['buyer_total_amount']:"",
                  'original_price'=>(!is_null($order_income['original_price']))?$order_income['original_price']:"",
                  'seller_discount'=>(!is_null($order_income['seller_discount']))?$order_income['seller_discount']:"",
                  'shopee_discount'=>(!is_null($order_income['shopee_discount']))?$order_income['shopee_discount']:"",
                  'voucher_from_seller'=>(!is_null($order_income['voucher_from_seller']))?$order_income['voucher_from_seller']:"",
                  'voucher_from_shopee'=>(!is_null($order_income['voucher_from_shopee']))?$order_income['voucher_from_shopee']:"",
                  'coins'=>(!is_null($order_income['coins']))?$order_income['coins']:"",
                  'buyer_paid_shipping_fee'=>(!is_null($order_income['buyer_paid_shipping_fee']))?$order_income['buyer_paid_shipping_fee']:"",
                  'buyer_transaction_fee'=>(!is_null($order_income['buyer_transaction_fee']))?$order_income['buyer_transaction_fee']:"",
                  'cross_border_tax'=>(!is_null($order_income['cross_border_tax']))?$order_income['cross_border_tax']:"",
                  'payment_promotion'=>(!is_null($order_income['payment_promotion']))?$order_income['payment_promotion']:"",
                  'commission_fee'=>(!is_null($order_income['commission_fee']))?$order_income['commission_fee']:"",
                  'service_fee'=>(!is_null($order_income['service_fee']))?$order_income['service_fee']:"",
                  'seller_transaction_fee'=>(!is_null($order_income['seller_transaction_fee']))?$order_income['seller_transaction_fee']:"",
                  'seller_lost_compensation'=>(!is_null($order_income['seller_lost_compensation']))?$order_income['seller_lost_compensation']:"",
                  'seller_coin_cash_back'=>(!is_null($order_income['seller_coin_cash_back']))?$order_income['seller_coin_cash_back']:"",
                  'escrow_tax'=>(!is_null($order_income['escrow_tax']))?$order_income['escrow_tax']:"",
                  'final_shipping_fee'=>(!is_null($order_income['final_shipping_fee']))?$order_income['final_shipping_fee']:"",
                  'actual_shipping_fee'=>(!is_null($order_income['actual_shipping_fee']))?$order_income['actual_shipping_fee']:"",
                  'shopee_shipping_rebate'=>(!is_null($order_income['shopee_shipping_rebate']))?$order_income['shopee_shipping_rebate']:"",
                  'shipping_fee_discount_from_3pl'=>(!is_null($order_income['shipping_fee_discount_from_3pl']))?$order_income['shipping_fee_discount_from_3pl']:"",
                  'seller_shipping_discount'=>(!is_null($order_income['seller_shipping_discount']))?$order_income['seller_shipping_discount']:"",
                  'estimated_shipping_fee'=>(!is_null($order_income['estimated_shipping_fee']))?$order_income['estimated_shipping_fee']:"",
                  'seller_voucher_code'=> "",
                  'drc_adjustable_refund'=>(!is_null($order_income['drc_adjustable_refund']))?$order_income['drc_adjustable_refund']:"",
                  'cost_of_goods_sold'=>(!is_null($order_income['cost_of_goods_sold']))?$order_income['cost_of_goods_sold']:"",
                  'original_cost_of_goods_sold'=>(!is_null($order_income['original_cost_of_goods_sold']))?$order_income['original_cost_of_goods_sold']:"",
                  'original_shopee_discount'=>(!is_null($order_income['original_shopee_discount']))?$order_income['original_shopee_discount']:"",
                  'seller_return_refund'=>(!is_null($order_income['seller_return_refund']))?$order_income['seller_return_refund']:"",
                  'keygen'=>$keygen_order_income
                );

                $esceow_income_id  = $this->shopee_escrow_order_income_model->insert($datainsert);

                if(!empty($esceow_income_id)){

                  $order_income_items = $order_income['items'];
                  $num_item = 0;
                  $arr_item_insert = array();
                  $cnt_item = count($order_income_items);

                  if($cnt_item > 0){
                    $keygen_item = $this->random_util->create_random_number(8);
                    foreach($order_income_items as $order_income_item){
                      $arr_item = array(
                        'OrderListID' => $orderlist_id_list,
                        'EscrowID' => $esceow_id,
                        'EscrowOrderIncomeID' => $esceow_income_id,
                        'order_sn' => $order_sn,
                        'item_id' =>(!is_null($order_income_item['item_id']))?$order_income_item['item_id']:"",
                        'item_name' =>(!is_null($order_income_item['item_name']))?$order_income_item['item_name']:"",
                        'item_sku' =>(!is_null($order_income_item['item_sku']))?$order_income_item['item_sku']:"",
                        'model_id' =>(!is_null($order_income_item['model_id']))?$order_income_item['model_id']:"",
                        'model_name' =>(!is_null($order_income_item['model_name']))?$order_income_item['model_name']:"",
                        'model_sku' =>(!is_null($order_income_item['model_sku']))?$order_income_item['model_sku']:"",
                        'original_price' =>(!is_null($order_income_item['original_price']))?$order_income_item['original_price']:"",
                        'discounted_price' =>(!is_null($order_income_item['discounted_price']))?$order_income_item['discounted_price']:"",
                        'discount_from_coin' =>(!is_null($order_income_item['discount_from_coin']))?$order_income_item['discount_from_coin']:"",
                        'discount_from_voucher_shopee' =>(!is_null($order_income_item['discount_from_voucher_shopee']))?$order_income_item['discount_from_voucher_shopee']:"",
                        'discount_from_voucher_seller' =>(!is_null($order_income_item['discount_from_voucher_seller']))?$order_income_item['discount_from_voucher_seller']:"",
                        'activity_type' =>(!is_null($order_income_item['activity_type']))?$order_income_item['activity_type']:"",
                        'activity_id' =>(!is_null($order_income_item['activity_id']))?$order_income_item['activity_id']:"",
                        'is_main_item' =>(!is_null($order_income_item['is_main_item']))?$order_income_item['is_main_item']:"",
                        'keygen'=>$keygen_item
                      );

                      array_push($arr_item_insert,$arr_item);


                    /*$chk_item = $this->shopee_escrow_items_model->get_by_id_sn($order_income_item['item_id'],$order_sn);

                      if(empty($chk_item)){
                        $this->shopee_escrow_items_model->insert($arr_item);
                      }*/
                      $num_item = $num_item+1;
                    }

                    if($cnt_item == $num_item){
                      $this->shopee_escrow_items_model->insert_all($arr_item_insert);

                    }else{

                      $this->shopee_escrow_detail_model->del_by_order_sn($order_sn);
                      $this->shopee_escrow_order_income_model->del_by_order_sn($order_sn);

                      $datalog = array(
                        'log_type' => 2,
                        'log_code' => 'NULLORnum',
                        'log_note' => 'API Shopee Not insert',
                        'log_status' => 1
                        );

                      $this->bnylog_model->insert($datalog);

                      die("API Shopee Not insert");
                    }

                  }//if($cnt_item > 0)

                }//if(!empty($esceow_income_id)){
            }//if(count($row['order_income']) > 0){
         }//if(!empty($esceow_id))
      } //if(count($datas['response']) > 0)
    }else{ //if(!empty($arr_escrow))

      $datalog = array(
        'log_type' => 2,
        'log_code' => 'NULLORnum',
        'log_note' => 'API Shopee No data from shopee data list',
        'log_status' => 1
        );

      $this->bnylog_model->insert($datalog);

      die("No data from shopee data list");

    }
  }

  function getEscrow_test(){

      $order_sn='241001C0739NT5';
     // $orderlist_id_list=$arr_escrow['OrderListID'];
      //$keygen = $this->random_util->create_random_number(8);

     // echo "order_no>>".$order_sn."<<Orderlist_id>>".$orderlist_id_list;

      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/payment/get_escrow_detail","get")))
        {
          die("canot init shopee api");
        }


        $data=array(
          'order_sn' => $order_sn
        );
        $this->shopeeapi->setData($data);

        $datas=$this->shopeeapi->execute();

        print_r($datas);
      }

    function get_order_more(){

        $arr_orders = $this->shopee_orders_model->get_order_not_die();
        //print_r($arr_orders);
        $num = 0;
        if(!empty($arr_orders)){
          foreach($arr_orders as $arr_order){
              $updated_at = $arr_order['update_time'];
                  if($arr_order['order_status'] == 'TO_CONFIRM_RECEIVE'){

                      if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }

                  }elseif($arr_order['order_status'] == 'SHIPPED'){

                      //echo $arr_order['date_to_now']."<br>";
                      if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }
                      
                  }elseif($arr_order['order_status'] == 'READY_TO_SHIP'){

                      //echo $arr_order['date_to_now']."<br>";
                      if($arr_order['date_to_now'] > 10){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }
                      
                  }elseif($arr_order['order_status'] == 'PROCESSED'){

                      //echo $arr_order['date_to_now']."<br>" ;
                      if($arr_order['date_to_now'] > 10){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }
                      
                  }elseif($arr_order['order_status'] == 'UNPAID'){

                      //echo $arr_order['date_to_now']."<br>";
                      if($arr_order['date_to_now'] > 10){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }
                      
                  }else{
                    if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_sn'],$arr_order['OrderListID'],$arr_order['order_status']);
                          $num = $num +1;
                      }
                  }
                  if($num == 20){
                    break;
                  }
        }
      }
    }

    public function getOrder_insert_orderno($order_sn,$OrderListID,$status)
     {
        //$OrderListID = '121212';
        $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
        $path="/api/v2/order/get_order_detail";
        $timestamp=$this->shopee_bl->get_timestamp();
         
         $accesstoken=$this->shopee_bl->getaccesstoken();
         //echo "token:".$accesstoken;
         $shop_id=$this->shopee_bl->getshopid();
         $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
         $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


         $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


         $order_list_sn_key_id_value_arr=array();

         $order_sn_list=$order_sn;

         $data=array('order_sn_list'=>$order_sn_list,
                     'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                      );
         
         foreach($data as $key => $value)                                                                                                                                                
         {

            $url=$url."&".$key."=".$value;

         }
         //echo $url;
         //return $this->shopee_curl_post($url,$data);
         //return htmlspecialchars($url);
         $return_str=$this->shopee_bl->shopee_curl_get($url);
         $datas = json_decode($return_str,true);
         //print_r($datas);
      
            if(isset($datas['response']['order_list']))
            {
                $row = $datas['response']['order_list'][0];
                $arr_data_chk = $this->shopee_orders_model->get_by_sn_status($row["order_sn"],$row["order_status"]);

                if(empty($arr_data_chk)){
                    $order_ctime = date('Y-m-d H:i:s',$row["create_time"]);
                    $data_order=array(
                      'order_sn'=>$row["order_sn"],
                      'taxinvoiceID'=>"",
                      'order_status'=>$row["order_status"],
                      'total_amount'=>$this->common_util->prep_float($row["total_amount"]),
                      'update_time'=>date('Y-m-d H:i:s',$row["update_time"]),
                      'actual_shipping_fee'=>$this->common_util->prep_float($row["actual_shipping_fee"]),
                      'actual_shipping_fee_confirmed'=>intval($row["actual_shipping_fee_confirmed"]),
                      'cancel_by'=>$row["cancel_by"],
                      'cancel_reason'=>$row["cancel_reason"],
                      'create_time'=>date('Y-m-d H:i:s',$row["create_time"]),
                      'estimated_shipping_fee'=>$row["estimated_shipping_fee"],
                      'cancel_reason'=>$row["cancel_reason"],
                      'OrderListID'=>$OrderListID,
                      'keygen' => ''
                    );
                    //--print_r($data_order);
                    if(!is_null($row["order_sn"])){

                      $order_id_pre = $this->shopee_orders_model->insert($data_order);
                      $this->getEscrow($row["order_sn"],$order_id_pre,$OrderListID,$order_ctime,$row["order_status"],1);

                      $arr_chk_items = $this->shopee_orderitems_model->get_by_sn($row["order_sn"]);

                      if(empty($arr_chk_items)){

                        /*$data_shipping=array(
                         'order_sn'=>$row["order_sn"],
                         'name'=>$row["recipient_address"]["name"],
                         'phone'=>$row["recipient_address"]["phone"],
                         'town'=>$row["recipient_address"]["town"],
                         'district'=>$row["recipient_address"]["district"],
                         'city'=>$row["recipient_address"]["city"],
                         'state'=>$row["recipient_address"]["state"],
                         'zipcode'=>$row["recipient_address"]["zipcode"],
                         'full_address'=>$row["recipient_address"]["full_address"],
                         'keygen' => $keygen
                        );

                        $this->shopee_shipping_address_model->insert($data_shipping);*/
                        
                       foreach($row["item_list"] as $row_items)
                        {

                           $data_orderitems=array(
                            'order_sn'=>$row["order_sn"],
                            'order_item_id'=>intval($row_items["order_item_id"]),
                            'item_sku'=>$row_items["item_sku"],
                            'item_name'=>$row_items["item_name"],
                            'model_id'=>intval($row_items["model_id"]),
                            'model_sku'=>$row_items["model_sku"],
                            'model_quantity_purchased'=>intval($row_items["model_quantity_purchased"]),
                            'model_original_price'=>$this->common_util->prep_float($row_items["model_original_price"]),
                            'model_discounted_price'=>$this->common_util->prep_float($row_items["model_discounted_price"]),
                            'add_on_deal_id'=>intval($row_items["add_on_deal_id"]),
                            'promotion_type'=>$row_items["promotion_type"],
                            'promotion_id'=>intval($row_items["promotion_id"]),
                            'promotion_group_id'=>intval($row_items["promotion_group_id"]),
                            'keygen' => $keygen
                           );

                           $this->shopee_orderitems_model->insert($data_orderitems);

                        } //end foreach($row["item_list"] as $row_items)
                      }//if(empty($arr_chk_items)){

                       // $this->shopee_order_list_model->update_status_complete();

                     }//if(!is_null($row["order_sn"])){
              }else{//if(empty($arr_data_chk)){  
                    $data_up = array(
                        'update_time' => DATE_TIME_NOW
                    );
                    $this->shopee_orders_model->update_order_status($row["order_sn"],$status,$data_up);
                    //print_r($data_up);
                }      
            }//if(isset($datas['response']['order_list']))


     }

  function load_bk(){
    /*if($datas['error']=="")
        {
        if(count($datas['response']['order_list'])==0) // there is no order selected--> shipt to nect period
          {

            $start_date=date_create(date("Y-m-d", strtotime("+".strval($download_arr["shopee_discount_date_interval"])." day", strtotime($download_arr["shopee_discount_start_date"]))));
            $now_date=date_create(date("Y-m-d"));

            if(intval(date_diff($start_date,$now_date)->format("%a"))>30)
              {
                $interval=15;
              }else{
                $interval=1;
              }

              echo "interval: ".$interval;

              $data=array(
                'shopee_discount_start_date'=>date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_discount_date_interval"])." day", strtotime($download_arr["shopee_discount_start_date"]))),
                         'shopee_discount_date_interval'=>intval($interval)
              );

              //print_r($data);
              $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);
              exit();
          } else { //there is/are some orders

            if(date_create(date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_discount_date_interval"])." day", strtotime($download_arr["shopee_discount_start_date"]))))->gettimestamp()<=date_create(date('Y-m-d H:i:s'))->gettimestamp())
              {
                $arr_datainsert = array();
                $num_data=0;

                foreach($datas['response']['order_list'] as $row)
                  {
                    $datainsert=array('order_sn'=>$row['order_sn']);
                    if(!is_null($row['order_sn']))
                    {

                      array_push($arr_datainsert,$datainsert);
                      $num_data = $num_data +1;

                    }else{

                      $datalog = array(
                      'log_type' => 1,
                      'log_code' => 'NULLORnum',
                      'log_note' => 'API LAZADA Order Insert NULL Value Order Number = '.$row['order_sn'],
                      'log_status' => 1
                      );

                      $this->bnylog_model->insert($datalog);

                      break;
                    }
                  }// foreach

                if($num_data == count($arr_datainsert)) // all success we insert
                  {

                    $arr_to_insert=array_reverse($arr_datainsert);
                    print_r($arr_to_insert);
                    $this->shopee_order_list_model->insert_all($arr_to_insert);
                    empty($arr_to_insert);
                    empty($arr_datainsert);
                    $date_start=$download_arr["shopee_discount_start_date"];
                    //echo "<br>More date( )ata?: ".$datas['response']['more'];
                    if($datas['response']['next_cursor']=="") // no more data to download for this date range-> shipt to next period
                      {
                        
                        $date_start=date("Y-m-d H:i:s", strtotime("+".strval($download_arr["shopee_discount_date_interval"])." day", strtotime($download_arr["shopee_discount_start_date"])));

                      }
                        $start_date=date_create(date("Y-m-d", strtotime("+".strval($download_arr["shopee_discount_date_interval"])." day", strtotime($download_arr["shopee_discount_start_date"]))));
                                       $now_date=date_create(date("Y-m-d"));
                     if(intval(date_diff($start_date,$now_date)->format("%a"))>30)
                      {
                        $interval=15;
                      }else{
                        $interval=1;
                      }
                              
                     if($datas['response']['next_cursor']=="" && $interval!=$initial_interval)
                       {
                       }else{
                        $interval=$initial_interval;
                       }
                        echo "interval: ".$interval;

                        $data=array('shopee_discount_start_date'=>$date_start,
                                    'shopee_discount_cursor'=>$cursor=$datas['response']['next_cursor'],
                                    'shopee_discount_date_interval'=>intval($interval)
                        );
                        $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);
                    }
                }
            } // if(count($datas['response']['order_list'])==0) 
         } //if($datas['error']=="")*/
  }

  /*
               if($num_data == 0){   //fix invoiceid

                   $arr_lastorder = $this->shopee_orders_model->last_order();   
                   $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],date('Y-m-d H:i:s',$row["create_time"]));    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->shopee_bl->get_shopee_code($last_id,date('Y-m-d H:i:s',$row["create_time"]));  
                    $last_id = $new_textinvoiceID;
                } 
                */

    function create_update_textinvoiceid(){

        $arr_datas = $this->shopee_taxinvoiceid_model->get_by_null_id(20);

        print_r($arr_datas);

        if(!empty($arr_datas)){

            foreach($arr_datas as $arr_data){

                $order_ctime = $arr_data['create_time'];
                $create_time=date_create($order_ctime);
                $order_yymm = date_format($create_time,"Y-m");

                $arr_lastorder = $this->shopee_taxinvoiceid_model->last_order_code_by_yymm_notnull($order_yymm);   

                if(!empty($arr_lastorder)){

                    $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$order_ctime);  

                    $arr_up = array(
                        'taxinvoiceID' => $new_textinvoiceID,
                    );

                    $this->shopee_taxinvoiceid_model->update($arr_up,$arr_data['Shopee_taxinvoiceid_id']);


                }else{

                    $new_textinvoiceID = $this->shopee_bl->get_shopee_code('no',$order_ctime);  

                    $arr_up = array(
                        'taxinvoiceID' => $new_textinvoiceID,
                    );

                    $this->shopee_taxinvoiceid_model->update($arr_up,$arr_data['Shopee_taxinvoiceid_id']);

                }
            }
        }
}           

   function create_textinvoiceid(){

       $array_status_death = array('COMPLETED','CANCELLED');

        $arr_sho_taxs = $this->shopee_orders_model->select_by_status_last_arr($array_status_death,50);

        foreach($arr_sho_taxs as $arr_sho_tax){

            $arr_chk_or = $this->shopee_taxinvoiceid_v2_model->select_taxinvoiceid_by_orderno($arr_sho_tax['order_sn']);
            if(empty($arr_chk_or)){
                $arr_lastorder = $this->shopee_taxinvoiceid_v2_model->last_order_code_by_yymm($arr_sho_tax['yyyymm']);   
                print_r($arr_lastorder);

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$arr_sho_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_sn' => $arr_sho_tax['order_sn'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_sho_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->shopee_taxinvoiceid_v2_model->insert($arr_new_invoice_id);


               }else{

                $new_textinvoiceID = $this->shopee_bl->get_shopee_code('no',$arr_sho_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_sn' => $arr_sho_tax['order_sn'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_sho_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->shopee_taxinvoiceid_v2_model->insert($arr_new_invoice_id);

               }
            }

       }
    }              

  function create_textinvoiceid_v2(){

        $arr_textinvoices = $this->shopee_orders_model->select_next_invoice(100); 

        //print_r($arr_textinvoices);


        if(!empty($arr_textinvoices)){ 


            foreach($arr_textinvoices as $arr_textinvoice){

               $arr_lastorder = $this->shopee_orders_model->last_order_code($arr_textinvoice['create_time'],$arr_textinvoice["OrderID"]);   

               if(!empty($arr_lastorder)){

               $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$arr_textinvoice["create_time"]);    

               // echo $arr_textinvoice["OrderID"].">>".$arr_textinvoice["create_time"].">>".$new_textinvoiceID."<br>";

                $arr_up = array(
                    'taxinvoiceID' => $new_textinvoiceID
                );

                $this->shopee_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                //sleep(1);
              }else{ // first row

                $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_textinvoice['taxinvoiceID'],$arr_textinvoice["create_time"]);    

               //echo $arr_textinvoice["OrderID"].">>".$arr_textinvoice["create_time"].">>".$new_textinvoiceID."<br>";

                $arr_up = array(
                    'taxinvoiceID' => $new_textinvoiceID
                );

                $this->shopee_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                
              }
            }        


        }
    }

    function create_textinvoiceid_v1(){

        $arr_textinvoices = $this->shopee_orders_model->select_next_invoice(500); 

        //print_r($arr_textinvoices);
        $num_data = 0;

        if(!empty($arr_textinvoices)){ 


            foreach($arr_textinvoices as $arr_textinvoice){

                if($num_data == 0){   

                   $arr_lastorder = $this->shopee_orders_model->last_order_code();   
                   $new_textinvoiceID = $this->shopee_bl->get_shopee_code($arr_lastorder['taxinvoiceID'],$arr_textinvoice["create_time"]);    
                   $last_id = $new_textinvoiceID;

                }else{

                    $new_textinvoiceID = $this->shopee_bl->get_shopee_code($last_id,$arr_textinvoice["create_time"]);  
                    $last_id = $new_textinvoiceID;

                } 

                $num_data = $num_data +1;
                //echo $arr_textinvoice["create_time"].">>".$last_id."<br>";

                $arr_up = array(
                    'taxinvoiceID' => $last_id
                );

                $this->shopee_orders_model->update_by_id($arr_up,$arr_textinvoice["OrderID"]);
                //sleep(1);
            }        


        }
    }

    function test_insert_all(){



        $data = array(
                    array(
                        'col1' => 'aaaa'
                    ),
                    array(
                        'col1' => "ssss"
                    ),
        );

        $data2 = $this->free_model->select_test(79);

        print_r($data2);

        $sql_insert_list_batch = $this->db_util->insert_batch('testtable',$data2);
                             $this->free_model->query_run($sql_insert_list_batch);
       echo $sql_insert_list_batch;




     }

     public function getOrder_no_test()
     {

        $dd = date('Y-m-d H:i:s','1755838239');
        echo $dd."<br>";
        //$OrderListID = '121212';
        $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
        $path="/api/v2/order/get_order_detail";
        $timestamp=$this->shopee_bl->get_timestamp();
         
         $accesstoken=$this->shopee_bl->getaccesstoken();
         echo "token:".$accesstoken;
         $shop_id=$this->shopee_bl->getshopid();
         $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
         $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


         $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


         $order_list_sn_key_id_value_arr=array();

         $order_sn_list='250822FMGX1DXW';

         $data=array('order_sn_list'=>$order_sn_list,
                     'response_optional_fields'=>'estimated_shipping_fee,recipient_address,actual_shipping_fee,item_list,cancel_by,cancel_reason,actual_shipping_fee_confirmed,total_amount,buyer_username,invoice_data'
                      );
         
         foreach($data as $key => $value)                                                                                                                                                
         {

            $url=$url."&".$key."=".$value;

         }
         //echo $url;
         //return $this->shopee_curl_post($url,$data);
         //return htmlspecialchars($url);
         $return_str=$this->shopee_bl->shopee_curl_get($url);
         $datas = json_decode($return_str,true);
         print_r($datas);
       }

  function sho_import_order_chk(){
        $this->load->view('sho_import_order_chk');
     }     

  function sho_import_order_chk_action(){

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
            $osn_tmp = "";
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
            $order_sn = $rowData[0][0];
            $status = $rowData[0][1];
            $paidPrice = $rowData[0][42];
            
            $arr_order_datas = $this->shopee_orders_model->get_by_order_sn_price($order_sn);


            if(!empty($arr_order_datas)){
                        
              //echo $num."<br>";

              $a=array();
              $price_t=0;
             foreach($arr_order_datas as $arr_order_data){

                  array_push($a,$arr_order_data['order_status']);
                  if($arr_order_data['order_status'] == 'PROCESSED'){
                      $price_t = $arr_order_data['priceVATincluded'];
                  }
             }

             if (!in_array("PROCESSED", $a)){

              if (!in_array("CANCELLED", $a)){

                echo "<br>".$order_sn;
                print_r($a);

                if($status == "สำเร็จแล้ว"){
                  
                  if($order_sn != $osn_tmp){
                    $totol_price = $totol_price+$paidPrice;
                    $osn_tmp = $order_sn;
                  }
                } 
              }
             }
              reset($a);
             }else{
                echo "<br>Nodata >> ".$order_sn;     
            }

             //echo $paidPrice."<br>";   
                
             
            if(($order_ex == "")or($order_ex == $order_sn)){
                  $order_ex = $order_sn;

                  $price_ex = $price_ex + $paidPrice;

                  $price_db = round($price_t,2);
              }else{
                  /*if(intval($price_ex) != intval($price_db)){
                      echo "<br>--------------------------------------------------<br>";
                  }

                  echo $order_ex.">>>".$price_ex.">>price from DB::".$price_db."<br>";

                  if(intval($price_ex) != intval($price_db)){
                      echo "<br>--------------------------------------------------<br>";
                  }*/

                  $order_ex = "";
                  $price_ex = 0;
                  $price_db = 0;

                  $order_ex = $order_sn;

                  $price_ex = $price_ex + $paidPrice;
                  $price_db = $price_t;
              }
                 
            }



            echo "paidPrice = ".round($totol_price,2)."<br>";

        }   

    }
 }   


  function sho_import_sale_chk(){
        $this->load->view('sho_import_sale_chk');
     }

 function sho_import_sale_chk_action(){

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
            $totol_cn = 0;
            $totol_logistic_cn = 0;
            $osn_tmp = "";

            for ($row = 2; $row <= $highestRow; $row++){ 
                $num =$num+1;

            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                //  Insert row data array into your database of choice here
                //print_r($rowData);
            $order_sn = $rowData[0][0];
            $status = $rowData[0][1];
            $reason_cancel = $rowData[0][2];
            $paidPrice = $rowData[0][42];

            $price_cn = $rowData[0][24];
            $logistic_cn = $rowData[0][43];

             //echo $paidPrice."<br>";   
             if($status == "สำเร็จแล้ว"){
                
                if($order_sn != $osn_tmp){
                  $totol_price = $totol_price+$paidPrice;
                  $osn_tmp = $order_sn;
                }
             }elseif($status == "ยกเลิกแล้ว"){

              $arr_exp_reason = explode(" ", $reason_cancel);

              $cnt_reason = count($arr_exp_reason);

              $cancel_reason = $arr_exp_reason[$cnt_reason-1];

              if($cancel_reason == "การจัดส่งไม่สำเร็จ"){
                //echo "<br>".$cancel_reason."<br>".$order_sn;

                $totol_cn = $totol_cn+$price_cn;
                if($order_sn != $osn_tmp){
                  $totol_logistic_cn = $totol_logistic_cn+$logistic_cn;
                  $osn_tmp = $order_sn;
                }
              }

             }     
             

                 
            }

            $all_price_cn = $totol_cn+$totol_logistic_cn;
            $total = $totol_price + $all_price_cn;

            echo "paidPrice = ".round($totol_price,2)."<br>";

            echo "Total CN = ".round($all_price_cn,2)."<br>";

            echo "Total = ".round($total,2)."<br>";

        }   

    }
 }       


 function sho_import_sale_chk_prep(){
        $this->load->view('sho_import_sale_chk_plep');
     }

 function sho_import_sale_chk_prep_action(){

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
            $totol_cn = 0;
            $totol_logistic_cn = 0;
            $osn_tmp = "";

            $keygen = $this->random_util->create_random_number(8);

            for ($row = 2; $row <= $highestRow; $row++){ 

                $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                //  Insert row data array into your database of choice here
                //print_r($rowData);
                $order_sn = $rowData[0][0];


               $chk_sho_data = $this->shopee_prep_model->select_by_order_sn($order_sn);
            if(empty($chk_sho_data)){    
            
            $status = $rowData[0][1];
            $reason_cancel = $rowData[0][2];
            $order_date = $rowData[0][5];
            $paidPrice = $rowData[0][42];

            $price_cn = $rowData[0][24];
            $logistic_price = $rowData[0][43];

            $arr_exp_reason = explode(" ", $reason_cancel);
            $cnt_reason = count($arr_exp_reason);
            $cancel_reason = $arr_exp_reason[$cnt_reason-1];


            echo $order_sn.">>>>>".$highestRow.">>".$num."-->>".$order_sn_tmp."<br>";

            //**********************************

            if($num == 1){

              $order_sn_tmp = $order_sn;
              $status_tmp = $status;
              $order_date_tmp = $order_date;
              $reason_cancel_tmp = $cancel_reason;
              $paidPrice_tmp = $paidPrice;
              $price_cn_tmp = $price_cn;
              $logistic_price_tmp = $logistic_price;
              $osn_tmp = $order_sn;
            }elseif(($num > 1)and($num < $row_data)){

              if($order_sn != $osn_tmp){

                $insert_data = true;

                if($status_tmp == "ยกเลิกแล้ว"){
                  if($reason_cancel_tmp != "การจัดส่งไม่สำเร็จ"){
                    $insert_data = false;
                  }
                }

                if($insert_data){
                  $arr_data = array(
                    'order_sn' => $order_sn_tmp,
                    'order_date' => $order_date_tmp,
                    'status' => $status_tmp,
                    'cancel_reason' => $reason_cancel_tmp,
                    'paid_price' => $paidPrice_tmp,
                    'cn_paid_price' => $price_cn_tmp,
                    'logistic_price' => $logistic_price_tmp,
                    'code' => $keygen
                  );
                  $this->shopee_prep_model->insert($arr_data);
                }

                $order_sn_tmp = $order_sn;
                $status_tmp = $status;
                $order_date_tmp = $order_date;
                $reason_cancel_tmp = $cancel_reason;
                $paidPrice_tmp = $paidPrice;
                $price_cn_tmp = $price_cn;
                $logistic_price_tmp = $logistic_price;
                $osn_tmp = $order_sn;

              }else{  

                if($status == "สำเร็จแล้ว"){

                  $osn_tmp = $order_sn;


                }elseif($status == "ยกเลิกแล้ว"){

                  if($reason_cancel_tmp == "การจัดส่งไม่สำเร็จ"){
                    $price_cn_tmp = $price_cn_tmp + $price_cn;
                    $osn_tmp = $order_sn;
                  }

                }
              }
            }elseif($num == $row_data){

              echo "Last--->".$num."-->>".$order_sn."-*-->>>".$row_data;

              $insert_data_last = true;

                if($status == "ยกเลิกแล้ว"){
                    if($reason_cancel != "การจัดส่งไม่สำเร็จ"){
                        $insert_data_last = false;
                    }
                }

              if($order_sn != $osn_tmp){

                $arr_data = array(
                    'order_sn' => $order_sn_tmp,
                    'order_date' => $order_date_tmp,
                    'status' => $status_tmp,
                    'cancel_reason' => $reason_cancel_tmp,
                    'paid_price' => $paidPrice_tmp,
                    'cn_paid_price' => $price_cn_tmp,
                    'logistic_price' => $logistic_price_tmp,
                    'code' => $keygen
                  );
                $this->shopee_prep_model->insert($arr_data);

                $price_cn_tmp = $price_cn;
                

              }else{
                $price_cn_tmp = $price_cn_tmp + $price_cn;

              }

              if($insert_data_last){  
                  $arr_data = array(
                        'order_sn' => $order_sn,
                        'order_date' => $order_date,
                        'status' => $status,
                        'cancel_reason' => $reason_cancel,
                        'paid_price' => $paidPrice,
                        'cn_paid_price' => $price_cn_tmp,
                        'logistic_price' => $logistic_price,
                        'code' => $keygen
                      );
                  $this->shopee_prep_model->insert($arr_data);
              }
            }

            //********************************
            }//if(empty($chk_sho_data)){  
                
             }      
            }
        }   

    $this->get_data_sho_from_excel($keygen);
 }    

 function get_data_sho_from_excel($keygen){

    $arr_data_complete = $this->shopee_prep_model->select_by_complete($keygen);
    $arr_data_cn = $this->shopee_prep_model->select_by_cancel($keygen);
    $arr_data_retuen_cn = $this->shopee_prep_model->select_by_retuen($keygen);

    $total_cn = $arr_data_cn['sum_cn']+$arr_data_cn['sum_logis_cn']+$arr_data_retuen_cn['sum_cn_return'];

    $total_sale = $arr_data_complete['sum_sale'] + $total_cn;
    echo "<br>Total Price = ".$total_sale."<br>";
    echo "Total CN = ".$total_cn."<br>";

    echo "Total = ".$arr_data_complete['sum_sale']."<br>";

 }

 function sho_import_sale_chk_prep_action_bk(){

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
            $num =1;
            $keygen = $this->random_util->create_random_number(8);
            $totol_price = 0;
            $totol_ship = 0;
            $totol_cn = 0;
            $totol_logistic_cn = 0;
            $osn_tmp = "";

            $keygen = $this->random_util->create_random_number(8);

            for ($row = 2; $row <= $highestRow; $row++){ 

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                //  Insert row data array into your database of choice here
                //print_r($rowData);
                $order_sn = $rowData[0][0];


               $chk_sho_data = $this->shopee_prep_model->select_by_order_sn($order_sn);
            if(empty($chk_sho_data)){    
                print_r($chk_sho_data);
            
            $status = $rowData[0][1];//B
            $reason_cancel = $rowData[0][3];//D
            $order_date = $rowData[0][6];//G
            $paidPrice = $rowData[0][42];

            $price_cn = $rowData[0][25];//Z
            $logistic_price = $rowData[0][43];

            $arr_exp_reason = explode(" ", $reason_cancel);
            $cnt_reason = count($arr_exp_reason);
            $cancel_reason = $arr_exp_reason[$cnt_reason-1];


            echo $order_sn.">>>>>".$row_data.">>".$num."-->>".$order_sn_tmp."<br>";

            //**********************************

            if($num == 1){

              $order_sn_tmp = $order_sn;
              $status_tmp = $status;
              $order_date_tmp = $order_date;
              $reason_cancel_tmp = $cancel_reason;
              $paidPrice_tmp = $paidPrice;
              $price_cn_tmp = $price_cn;
              $logistic_price_tmp = $logistic_price;
              $osn_tmp = $order_sn;
            }elseif(($num > 1)and($num < $row_data)){

              if($order_sn != $osn_tmp){

                $insert_data = true;

                if($status_tmp == "ยกเลิกแล้ว"){
                  if($reason_cancel_tmp != "การจัดส่งไม่สำเร็จ"){
                    $insert_data = false;
                  }
                }

                if($insert_data){
                  $arr_data = array(
                    'order_sn' => $order_sn_tmp,
                    'order_date' => $order_date_tmp,
                    'status' => $status_tmp,
                    'cancel_reason' => $reason_cancel_tmp,
                    'paid_price' => $paidPrice_tmp,
                    'cn_paid_price' => $price_cn_tmp,
                    'logistic_price' => $logistic_price_tmp,
                    'code' => $keygen
                  );
                  $this->shopee_prep_model->insert($arr_data);
                }

                $order_sn_tmp = $order_sn;
                $status_tmp = $status;
                $order_date_tmp = $order_date;
                $reason_cancel_tmp = $cancel_reason;
                $paidPrice_tmp = $paidPrice;
                $price_cn_tmp = $price_cn;
                $logistic_price_tmp = $logistic_price;
                $osn_tmp = $order_sn;

              }else{  

                if($status == "สำเร็จแล้ว"){

                  $osn_tmp = $order_sn;


                }elseif($status == "ยกเลิกแล้ว"){

                  if($reason_cancel_tmp == "การจัดส่งไม่สำเร็จ"){
                    $price_cn_tmp = $price_cn_tmp + $price_cn;
                    $osn_tmp = $order_sn;
                  }

                }
              }
            }elseif($num == $row_data){

              echo "Last--->".$num."-->>".$order_sn_tmp;

              if($status == "ยกเลิกแล้ว"){

                  if($reason_cancel_tmp == "การจัดส่งไม่สำเร็จ"){
                    $price_cn_tmp = $price_cn_tmp + $price_cn;

                  }
                }

              $arr_data = array(
                    'order_sn' => $order_sn_tmp,
                    'order_date' => $order_date_tmp,
                    'status' => $status_tmp,
                    'cancel_reason' => $reason_cancel_tmp,
                    'paid_price' => $paidPrice_tmp,
                    'cn_paid_price' => $price_cn_tmp,
                    'logistic_price' => $logistic_price_tmp,
                    'code' => $keygen
                  );
              $this->shopee_prep_model->insert($arr_data);

            }

            //********************************
            }
                $num =$num+1;
             }      
            }
        }   

    
 }

  function get_data_sale_by_date(){

    $StartDate = '2024-12-01';
    $EndDate = '2024-12-31';

    $arr_shopees =$this->shopee_orders_model->shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
  //print_r($arr_shopees);

  $keygen = $this->random_util->create_random_number(8);

    foreach($arr_shopees as $arr_shopee){

      $chk_data = $this->shopee_prep_api_model->select_by_order_sn($arr_shopee['order_sn']);  
      if(empty($chk_data)){
          $data = array(
            'order_sn' => $arr_shopee['order_sn'],
            'transactiondate' => $arr_shopee['transactiondate'],
            'start_inv' => $arr_shopee['start_inv'],
            'end_inv' => $arr_shopee['end_inv'],
            'shipping_fee' => $arr_shopee['shipping_fee'],
            'voucher_platform' => $arr_shopee['voucher_platform'],
            'voucher_seller' => $arr_shopee['voucher_seller'],
            'voucher' => $arr_shopee['voucher'],
            'price' => $arr_shopee['price'],
            'priceVATincluded' => $arr_shopee['priceVATincluded'],
            'priceBeforeVAT' => $arr_shopee['priceBeforeVAT'],
            'VAT' => $arr_shopee['VAT'],
            'code' => $keygen
          );

          $this->shopee_prep_api_model->insert($data);
      }

    }

    $this->prep_make();

  }

  function prep_make(){


    $arr_datas = $this->shopee_prep_model->select_prep_join_by_orderno_v2();
    //print_r($arr_datas);
    $num  = 1;
    foreach($arr_datas as $arr_data){

        $diffprice = $arr_data['paid_price']-$arr_data['priceVATincluded'];
      
      echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$arr_data['priceVATincluded'].">>diff>>".$diffprice."<br>";

      if($diffprice != 0){

        echo "-------- CHECK -----------<br>";
        echo $arr_data['order_sn_s']."<br>";
        echo "-------- CHECK -----------<br>";
      }

      
    $num = $num+1;
  }
}

public function getReturnList()
{

 $download_arr=$this->DataDownload_model->select_by_BNY_SUBSCRIPTION_SHOPID(BNY_SUBSCRIPTION_SHOPID);

if($download_arr['shopee_return_is_finish'] == 0){
  if($download_arr['shopee_return_page_no'] == 0)  //never download so 
   {
      $data=array(
        'shopee_return_page_no'=>intval(1),
        'shopee_return_page_size'=>intval(10)
      );

      print_r($data);
      $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

      die("we just inserted");
    } else { //have download instruction in db

      $page_no = $download_arr["shopee_return_page_no"];  
      $shopee_return_page_no=$download_arr["shopee_return_page_no"];
      $shopee_return_page_size=$download_arr["shopee_return_page_size"];
   
      if(empty($this->shopeeapi->initWithAppPath_Method("/api/v2/returns/get_return_list","get")))
      {
        die("canot init shopee api");
      }


      $data_api=array(
        //'status' => 'all',
        'page_no' => intval($shopee_return_page_no),
        'page_size' => intval($shopee_return_page_size),
      );
      print_r($data_api);
      $this->shopeeapi->setData($data_api);

      $datas=$this->shopeeapi->execute();
       
       print_r($datas);
    //print_r($datas['response']);
    //print_r($datas['error']);
      if($datas['error']=="")
        {
          if(count($datas['response']['return']) > 0){

            $arr_datainsert = array();
            $num_data=0;

            foreach($datas['response']['return'] as $row)
              {

                $datainsert=array(
                  'return_sn'=>$row['return_sn'],
                  'order_sn'=>$row['order_sn'],
                  'reason'=>$row['reason'],
                  'text_reason'=>$row['text_reason'],
                  'refund_amount'=>$row['refund_amount'],
                  'status'=>$row['status'],
                  'start_time'=>date("Y-m-d H:i:s",$row['create_time']),
                  'end_time'=>date("Y-m-d H:i:s",$row['update_time'])

                );

                $chk_re_dup = $this->shopee_return_order_model->select_by_order_sn($row['order_sn']);

                if(empty($chk_re_dup)){
                    $this->shopee_return_order_model->insert($datainsert);
                }
                
              }

              
              if($datas['response']['more'] == true){

                $page_no = $shopee_return_page_no+1;

              }    

              $data=array(
                  'shopee_return_page_no'=>$page_no,
                  'shopee_return_page_size'=>intval(10),
                  'shopee_return_is_finish' => intval(0)
                );
                $this->DataDownload_model->update_by_BNY_SUBSCRIPTION_SHOPID($data,BNY_SUBSCRIPTION_SHOPID);

          }//if(count($datas['response']['discount_list'])==0){
        }//    if($datas['error']=="")
      }//else if($download_arr['shopee_discount_page_no'] == 0) 
    }  //if($download_arr['shopee_discount_is_finish'] == 1)
  }

  function make_order_return(){

    $arr_datas = $this->shopee_return_order_model->select_lasted_active(50);

    if(!empty($arr_datas)){
        foreach($arr_datas as $arr_data){
            $return_status = $this->getOrder_return_no_status($arr_data['order_sn']);
            echo $arr_data['order_sn'].">>>".$return_status."<br>";

            if($return_status == "COMPLETED"){

                $data_order = array(
                    'is_return' => 1
                );

                $this->shopee_orders_model->update_by_order_sn($data_order,$arr_data['order_sn']);
            }

            $data_retrun = array(
                'is_active' => 1
            );

            $this->shopee_return_order_model->update($data_retrun,$arr_data['shopee_return_order_id']);

        }
    }else{

        echo "Empty Data";
    }
  }

  public function getOrder_return_no_status($order_sn)
     {
        //$OrderListID = '121212';
        $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
        $path="/api/v2/order/get_order_detail";
        $timestamp=$this->shopee_bl->get_timestamp();
         
         $accesstoken=$this->shopee_bl->getaccesstoken();
         //echo "token:".$accesstoken;
         $shop_id=$this->shopee_bl->getshopid();
         $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
         $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


         $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


         $order_list_sn_key_id_value_arr=array();

         $order_sn_list='200830DVEW49C8';

         $data=array('order_sn_list'=>$order_sn);
         
         foreach($data as $key => $value)                                                                                                                                                
         {

            $url=$url."&".$key."=".$value;

         }
         //echo $url;
         //return $this->shopee_curl_post($url,$data);
         //return htmlspecialchars($url);
         $return_str=$this->shopee_bl->shopee_curl_get($url);
         $datas = json_decode($return_str,true);
         //print_r($datas['response']['order_list'][0]['order_status']);
         return $datas['response']['order_list'][0]['order_status'];
       }

       public function getOrder_return_test()
     {
        //$OrderListID = '121212';
        $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
        $path="/api/v2/order/get_order_detail";
        $timestamp=$this->shopee_bl->get_timestamp();
         
         $accesstoken=$this->shopee_bl->getaccesstoken();
         //echo "token:".$accesstoken;
         $shop_id=$this->shopee_bl->getshopid();
         $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$accesstoken.$shop_id;
         $sign=$this->shopee_bl->get_sign($sting_to_sign,SHOPEE_PATNERKEY);


         $url= $host.$path."?timestamp=".$timestamp."&partner_id=".SHOPEE_PATNERID."&access_token=".$accesstoken."&shop_id=".$shop_id."&sign=".$sign;


         $order_list_sn_key_id_value_arr=array();

         $order_sn_list='240925U2JKUSN5';

         $data=array('order_sn_list'=>'241109S3XADKD3');
         
         foreach($data as $key => $value)                                                                                                                                                
         {

            $url=$url."&".$key."=".$value;

         }
         //echo $url;
         //return $this->shopee_curl_post($url,$data);
         //return htmlspecialchars($url);
         $return_str=$this->shopee_bl->shopee_curl_get($url);
         $datas = json_decode($return_str,true);
         print_r($datas['response']);
         //print_r($datas['response']['order_list'][0]['order_status']);
       }

       function del_order_by_date(){

        $year_month = $this->uri->segment(3);
        if(strlen($year_month) == 4){
        //$year_month = '2410';
        
        $this->shopee_orders_model->delete_shopee_order_by_year_month($year_month);
        }


       }


       function prep_chk(){

        $arr_orderno_chk = array();
        $arr_datas = $this->shopee_prep_model->select_prep_join_by_orderno_code('57322971');
        //print_r($arr_datas);
        $num  = 1;
        foreach($arr_datas as $arr_data){

                $diffprice = $arr_data['paid_price']-$arr_data['priceVATincluded'];
              
              echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$arr_data['priceVATincluded'].">>diff>>".$diffprice."<br>";

              if($diffprice != 0){

                array_push($arr_orderno_chk,$arr_data['order_sn_s']);

                //echo "-------- CHECK -----------<br>";
                //echo $arr_data['order_sn_s']."<br>";
                //echo "-------- CHECK -----------<br>";
              }

              
            $num = $num+1;
          }
          //return $arr_orderno_chk;
        }

}
