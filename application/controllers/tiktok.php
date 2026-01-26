<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Tiktok extends CI_Controller
{

    function __construct()
    {
        //:[Auto call parent construct]
        parent::__construct();
        //@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
        $this->load->library('util/View_util');

        //$this->load->library('businesslogic/curl_bl');
        $this->load->library("businesslogic/upload_bl");
        $this->load->library('util/encryption_util');
        $this->load->library('util/date_util');
        $this->load->library('businesslogic/tiktok_bl');

        $this->load->model('tiktok_token_model');
        $this->load->model('tiktok_data_model');

        $this->load->model('tiktok_orders_model');
        $this->load->model('tiktok_line_items_model');
        $this->load->model('tiktok_order_payment_model');
        $this->load->model('tiktok_taxinvoiceid_model');

        $this->load->model('lazada_orders_model');

     }

     function unix_time(){
        date_default_timezone_set('UTC');
        $start_time = strtotime('2024-07-24 18:16:12');
        echo ">>".$start_time."<<<br>";
        $timestamp=1721844972;
        echo gmdate("Y-m-d H:i:s", $start_time);

        echo "<br>";

        $currentTime = new DateTime();
        $currentTime = DateTime::createFromFormat( 'U', $timestamp );
        $formattedString = $currentTime->format( 'c' );

        //echo $formattedString."<br>";

     }

    function apicallback(){
        //https://services.tiktokshop.com/open/authorize?service_id=7389572888133519109
        $auth_code = $this->input->get('code');
        //echo $auth_code;
        $arr_token = $this->tiktok_bl->get_accesstoken($auth_code);

        $access_token_expire_in = gmdate("Y-m-d H:i:s", $arr_token['data']['access_token_expire_in']);
        $refresh_token_expire_in = gmdate("Y-m-d H:i:s", $arr_token['data']['refresh_token_expire_in']);
        //print_r($arr_token);

        $data_insert = array(
            'access_token' => $arr_token['data']['access_token'],
            'access_token_expire_in' => $access_token_expire_in,
            'refresh_token' => $arr_token['data']['refresh_token'],
            'refresh_token_expire_in' => $refresh_token_expire_in,
            'seller_name' => $arr_token['data']['seller_name']
        );

        //print_r($data_insert);

        $this->tiktok_token_model->insert($data_insert);
    } 

    function refresh_token(){

        $arr_token_db = $this->tiktok_token_model->select_lasted_token();

        print_r($arr_token_db);

        $arr_token = $this->tiktok_bl->CallApi('GET','https://auth.tiktok-shops.com/api/v2/token/refresh?app_key='.TIKTOK_KEY.'&app_secret='.TIKTOK_SECRET.'&refresh_token='.$arr_token_db['refresh_token'].'&grant_type=refresh_token',NULL);
        print_r($arr_token);

        $access_token_expire_in = gmdate("Y-m-d H:i:s", $arr_token['data']['access_token_expire_in']);
        $refresh_token_expire_in = gmdate("Y-m-d H:i:s", $arr_token['data']['refresh_token_expire_in']);

        $data_insert = array(
            'access_token' => $arr_token['data']['access_token'],
            'access_token_expire_in' => $access_token_expire_in,
            'refresh_token' => $arr_token['data']['refresh_token'],
            'refresh_token_expire_in' => $refresh_token_expire_in,
            'seller_name' => $arr_token['data']['seller_name']
        );

        //print_r($data_insert);

        $this->tiktok_token_model->insert($data_insert);
    }

    function test_256(){

        $txt = "f2101657ef5c119fb6343dfa1fbc2cf0844529f8/authorization/202309/shopsapp_key6d2girb78e6hgtimestamp1721809510f2101657ef5c119fb6343dfa1fbc2cf0844529f8";

        $has = bin2hex(hash_hmac('sha256', $txt, 'f2101657ef5c119fb6343dfa1fbc2cf0844529f8', true));

        //$has = hash('sha256', $txt, 'f2101657ef5c119fb6343dfa1fbc2cf0844529f8', true);

        echo $has;
    }

    function test_api(){

        $dateunix_ge = strtotime('2024-05-26 12:00:00');
        echo "ge->>>".$dateunix_ge."<br>";

        $dateunix_lt = strtotime('2024-05-27 12:00:00');
        echo "lt->>>".$dateunix_lt."<br>";

        //$timestamp=1721713477;
        //echo gmdate("Y-m-d h:i:s", $timestamp);

        //$timestamp = round(microtime(true) * 1000);
        $date = date('Y-m-d H:i:s');

        echo "<br>".$date;

        $dateunix = strtotime('2024-07-26 12:14:00');

        //$dateunix = strtotime($date);
        $dateunix_m = strtotime($date .'+ 1 minute');

        echo "<br>m----";

        echo gmdate("Y-m-d h:i:s", $dateunix_m);

        echo "<br>";

        //echo $date;

        //$api_url = "/api/shop/get_authorized_shop";
        $api_url = "/authorization/202309/shops";

        $sign = $this->tiktok_bl->signature($api_url,NULL,'GET',NULL,NULL);
        echo "<br>-------sign sha256--------------<br>";
        echo $sign;
        echo "<br>----------------------<br>";

        //$res = $this->tiktok_bl->CallApiToken('GET','https://open-api.tiktokglobalshop.com/authorization/202309/shops?app_key='.TIKTOK_KEY.'&sign='.$sign.'&timestamp='.$timestamp);
    //$url = "https://open-api.tiktokglobalshop.com/api/shop/get_authorized_shop?app_key=6d2girb78e6hg&timestamp=".$dateunix."&sign=".$sign;
        //$url = "https://open-api.tiktokglobalshop.com/authorization/202309/shops?app_key=".TIKTOK_KEY."&sign=".$sign."&timestamp=".$dateunix;

        $make_url = $this->tiktok_bl->make_url('GET',$api_url,$param,$sign);

        echo "<br>-------URL--------------<br>";
        echo $make_url;
        echo "<br>----------------------<br>";
        $res = $this->tiktok_bl->CallApiToken('GET',$make_url,NULL);
        print_r($res);
    }

    function test_get(){

        $api_url = "/authorization/202309/shops";

        //$make_url = $this->tiktok_bl->make_url('GET',$api_url,$param,$sign);

        $make_url = $this->tiktok_bl->make_url('GET',$api_url,NULL,NULL,NULL);

        $res = $this->tiktok_bl->CallApiToken('GET',$make_url,NULL);

        print_r($res);

    }

    function get_orders(){

        $api_url = "/order/202309/orders/search";

        $querys = array(
            'sort_order' => 'ASC',
            'sort_field' => 'create_time',
            'shop_cipher' => TIKTOK_SHOP_CIPHER,
            'page_size' => '100'
        );

        //$start_time = strtotime('2024-07-25 00:00:00');
        //$stop_time = strtotime('2024-07-25 20:14:00');

        $arr_last_order = $this->tiktok_orders_model->next_order_by_date();
        //$start_time = strtotime('2024-07-25 00:00:00');
        if(!empty($arr_last_order)){
            $start_time = strtotime($arr_last_order['create_time']);
        }

        //echo ">>".$arr_last_order['create_time'].">>".$start_time."<<";

        $bodys = array(
            //'order_status' => 'COMPLETED',
            'shipping_type' => 'TIKTOK',
            'create_time_ge' => $start_time
            //'create_time_lt' => $stop_time
        );

        $make_url = $this->tiktok_bl->make_url('POST',$api_url,$querys,$bodys,NULL);

        $res = $this->tiktok_bl->CallApiToken('POST',$make_url,$bodys);
        //print_r($res['data']['orders']);
        if(count($res['data']['orders']) > 0){

            foreach($res['data']['orders'] as $row){

                $array_status_not_death = array('ON_HOLD','UNPAID','PARTIALLY_SHIPPING','AWAITING_SHIPMENT','AWAITING_COLLECTION','IN_TRANSIT','DELIVERED');
                $array_status_death = array('COMPLETED', 'CANCELLED','Canceled');

                $order_death = true;
                if (in_array($row['status'], $array_status_not_death)){
                    $order_death = false;
                }

                if($order_death){
                    //-------------Order Death ----------------------

                    $chk_data_db = $this->tiktok_orders_model->get_by_id_status($row['id'],$row['status']);
                    if(empty($chk_data_db)){

                        $arr_dup_datas = array(
                            'order_id' => $row['id'],
                            'buyer_email' => $row['buyer_email'],
                            'buyer_message' => $row['buyer_message'],
                            'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                            'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                            'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                            'delivery_option_id' => $row['delivery_option_id'],
                            'delivery_option_name' => $row['delivery_option_name'],
                            'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                            'delivery_type' => $row['delivery_type'],
                            'fulfillment_type' => $row['fulfillment_type'],
                            'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                            'is_cod' => $row['is_cod'],
                            'is_on_hold_order' => $row['is_on_hold_order'],
                            'is_replacement_order' => $row['is_replacement_order'],
                            'is_sample_order' => $row['is_sample_order'],
                            'need_upload_invoice' => $row['need_upload_invoice'],
                            'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                            'payment_method_name' => $row['payment_method_name'],
                            'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                            'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                            'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                            'shipping_provider' => $row['shipping_provider'],
                            'shipping_provider_id' => $row['shipping_provider_id'],
                            'shipping_type' => $row['shipping_type'],
                            'status' => 'Packet',
                            'tracking_number' => $row['tracking_number'],
                            'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                            'update_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                            'user_id' => $row['user_id'],
                            'warehouse_id' => $row['warehouse_id']
                        );
                        //print_r($arr_dup_datas);
                        $this->tiktok_orders_model->insert($arr_dup_datas);   

                        /*$data_packed_dup = $this->chk_for_insert_packed($row,$row['status']);
                        if(!empty($data_packed_dup)){
                            $this->tiktok_orders_model->insert($data_packed_dup); 
                        }*/


                        $data_update_at = $row["update_time"];
                        if($status_order == 'canceled'){
                            $data_update_at = $row["create_time"];
                        }

                        $arr_datas = array(
                            'order_id' => $row['id'],
                            'buyer_email' => $row['buyer_email'],
                            'buyer_message' => $row['buyer_message'],
                            'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                            'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                            'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                            'delivery_option_id' => $row['delivery_option_id'],
                            'delivery_option_name' => $row['delivery_option_name'],
                            'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                            'delivery_type' => $row['delivery_type'],
                            'fulfillment_type' => $row['fulfillment_type'],
                            'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                            'is_cod' => $row['is_cod'],
                            'is_on_hold_order' => $row['is_on_hold_order'],
                            'is_replacement_order' => $row['is_replacement_order'],
                            'is_sample_order' => $row['is_sample_order'],
                            'need_upload_invoice' => $row['need_upload_invoice'],
                            'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                            'payment_method_name' => $row['payment_method_name'],
                            'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                            'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                            'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                            'shipping_provider' => $row['shipping_provider'],
                            'shipping_provider_id' => $row['shipping_provider_id'],
                            'shipping_type' => $row['shipping_type'],
                            'status' => $row['status'],
                            'tracking_number' => $row['tracking_number'],
                            'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                            'update_time' => $this->date_util->datetime_unix_to_dt($data_update_at),
                            'user_id' => $row['user_id'],
                            'warehouse_id' => $row['warehouse_id']
                        );
                        //print_r($arr_datas);
                        $this->tiktok_orders_model->insert($arr_datas);



                        //print_r($row['line_items']);
                        if(count($row['line_items']) > 0){
                            foreach($row['line_items'] as $line_item){
                                $chk_item = $this->tiktok_line_items_model->select_by_orderid_proid($row['id'],$line_item['product_id']);
                                
                                if(empty($chk_item)){
                                    $num_qty = $this->check_num_lineitem($row['line_items'],$line_item['product_id']);
                                    $data_line = array(
                                        'line_items_id' => $line_item['id'],
                                        'order_id' => $row['id'],
                                        'buyer_service_fee' => $line_item['buyer_service_fee'],
                                        'cancel_reason' => $line_item['cancel_reason'],
                                        'cancel_user' => $line_item['cancel_user'],
                                        'currency' => $line_item['currency'],
                                        'display_status' => $line_item['display_status'],
                                        'order_qty' => $num_qty,
                                        'is_gift' => $line_item['is_gift'],
                                        'original_price' => $line_item['original_price'],
                                        'package_id' => $line_item['package_id'],
                                        'package_status' => $line_item['package_status'],
                                        'platform_discount' => $line_item['platform_discount'],
                                        'product_id' => $line_item['product_id'],
                                        'product_name' =>$line_item['product_name'],
                                        'retail_delivery_fee' =>$line_item['retail_delivery_fee'],
                                        'rts_time' => $this->date_util->datetime_unix_to_dt($line_item['rts_time']),
                                        'sale_price' => $line_item['sale_price'],
                                        'seller_discount' => $line_item['seller_discount'],
                                        'seller_sku' => $line_item['seller_sku'],
                                        'shipping_provider_id' => $line_item['shipping_provider_id'],
                                        'shipping_provider_name' => $line_item['shipping_provider_name'],
                                        'sku_id' => $line_item['sku_id'],
                                        'sku_image' => $line_item['sku_image'],
                                        'sku_name' => $line_item['sku_name'],
                                        'sku_type' => $line_item['sku_type'],
                                        'small_order_fee' => $line_item['small_order_fee'],
                                        'tracking_number' => $line_item['tracking_number'],

                                    );

                                    //print_r($data_line);
                                    $this->tiktok_line_items_model->insert($data_line);
                                }//if(empty($chk_item)){
                            }//foreach($row['line_items'] as $line_item){
                        }//if(count($row['line_items']) > 0){

                        
                        //print_r($row['payment']);    
                            
                        if(!empty($row['payment'])){
                            $chk_payment = $this->tiktok_order_payment_model->select_by_orderid($row['id']);
                            if(empty($chk_payment)){
                                $data_payment = array(
                                    'order_id' => $row['id'],
                                    'buyer_service_fee' => $row['payment']['buyer_service_fee'],
                                    'currency' => $row['payment']['currency'],
                                    'original_shipping_fee' => $row['payment']['original_shipping_fee'],
                                    'original_total_product_price' => $row['payment']['original_total_product_price'],
                                    'platform_discount' => $row['payment']['platform_discount'],
                                    'product_tax' => $row['payment']['product_tax'],
                                    'retail_delivery_fee' => $row['payment']['retail_delivery_fee'],
                                    'seller_discount' => $row['payment']['seller_discount'],
                                    'shipping_fee' => $row['payment']['shipping_fee'],
                                    'shipping_fee_platform_discount' => $row['payment']['shipping_fee_platform_discount'],
                                    'shipping_fee_seller_discount' => $row['payment']['shipping_fee_seller_discount'],
                                    'shipping_fee_tax' => $row['payment']['shipping_fee_tax'],
                                    'small_order_fee' => $row['payment']['small_order_fee'],
                                    'sub_total' => $row['payment']['sub_total'],
                                    'tax' => $row['payment']['tax'],
                                    'total_amount' => $row['payment']['total_amount']
                                );

                                //print_r($data_payment);
                                $this->tiktok_order_payment_model->insert($data_payment);
                            }
                        }    

                        
                    }//if(empty($chk_data_db)){    
                }else{//if Death
                    //----------Order Not death----------------
                    $chk_data_db = $this->tiktok_orders_model->get_by_id_status($row['id'],$row['status']);
                    if(empty($chk_data_db)){

                        $arr_datas = array(
                            'order_id' => $row['id'],
                            'buyer_email' => $row['buyer_email'],
                            'buyer_message' => $row['buyer_message'],
                            'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                            'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                            'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                            'delivery_option_id' => $row['delivery_option_id'],
                            'delivery_option_name' => $row['delivery_option_name'],
                            'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                            'delivery_type' => $row['delivery_type'],
                            'fulfillment_type' => $row['fulfillment_type'],
                            'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                            'is_cod' => $row['is_cod'],
                            'is_on_hold_order' => $row['is_on_hold_order'],
                            'is_replacement_order' => $row['is_replacement_order'],
                            'is_sample_order' => $row['is_sample_order'],
                            'need_upload_invoice' => $row['need_upload_invoice'],
                            'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                            'payment_method_name' => $row['payment_method_name'],
                            'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                            'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                            'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                            'shipping_provider' => $row['shipping_provider'],
                            'shipping_provider_id' => $row['shipping_provider_id'],
                            'shipping_type' => $row['shipping_type'],
                            'status' => $row['status'],
                            'tracking_number' => $row['tracking_number'],
                            'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                            'update_time' => $this->date_util->datetime_unix_to_dt($row['update_time']),
                            'user_id' => $row['user_id'],
                            'warehouse_id' => $row['warehouse_id']
                        );
                        //print_r($arr_datas);
                        $this->tiktok_orders_model->insert($arr_datas);

                        $data_packed_dup = $this->chk_for_insert_packed($row,$row['status']);
                        if(!empty($data_packed_dup)){
                            $this->tiktok_orders_model->insert($data_packed_dup); 
                        }

                        if(count($row['line_items']) > 0){

                            foreach($row['line_items'] as $line_item){

                                $chk_item = $this->tiktok_line_items_model->select_by_orderid_proid($row['id'],$line_item['product_id']);
                                
                                if(empty($chk_item)){

                                    $num_qty = $this->check_num_lineitem($row['line_items'],$line_item['product_id']);

                                    $data_line = array(
                                        'line_items_id' => $line_item['id'],
                                        'order_id' => $row['id'],
                                        'buyer_service_fee' => $line_item['buyer_service_fee'],
                                        'cancel_reason' => $line_item['cancel_reason'],
                                        'cancel_user' => $line_item['cancel_user'],
                                        'currency' => $line_item['currency'],
                                        'display_status' => $line_item['display_status'],
                                        'order_qty' => $num_qty,
                                        'is_gift' => $line_item['is_gift'],
                                        'original_price' => $line_item['original_price'],
                                        'package_id' => $line_item['package_id'],
                                        'package_status' => $line_item['package_status'],
                                        'platform_discount' => $line_item['platform_discount'],
                                        'product_id' => $line_item['product_id'],
                                        'product_name' =>$line_item['product_name'],
                                        'retail_delivery_fee' =>$line_item['retail_delivery_fee'],
                                        'rts_time' => $this->date_util->datetime_unix_to_dt($line_item['rts_time']),
                                        'sale_price' => $line_item['sale_price'],
                                        'seller_discount' => $line_item['seller_discount'],
                                        'seller_sku' => $line_item['seller_sku'],
                                        'shipping_provider_id' => $line_item['shipping_provider_id'],
                                        'shipping_provider_name' => $line_item['shipping_provider_name'],
                                        'sku_id' => $line_item['sku_id'],
                                        'sku_image' => $line_item['sku_image'],
                                        'sku_name' => $line_item['sku_name'],
                                        'sku_type' => $line_item['sku_type'],
                                        'small_order_fee' => $line_item['small_order_fee'],
                                        'tracking_number' => $line_item['tracking_number'],

                                    );

                                    //print_r($data_line);
                                    $this->tiktok_line_items_model->insert($data_line);
                                }//if(empty($chk_item)){

                            }//foreach($row['line_items'] as $line_item){
                        }//if(count($row['line_items']) > 0){

                        if(!empty($row['payment'])){
                            $chk_payment = $this->tiktok_order_payment_model->select_by_orderid($row['id']);
                            if(empty($chk_payment)){
                                $data_payment = array(
                                    'order_id' => $row['id'],
                                    'buyer_service_fee' => $row['payment']['buyer_service_fee'],
                                    'currency' => $row['payment']['currency'],
                                    'original_shipping_fee' => $row['payment']['original_shipping_fee'],
                                    'original_total_product_price' => $row['payment']['original_total_product_price'],
                                    'platform_discount' => $row['payment']['platform_discount'],
                                    'product_tax' => $row['payment']['product_tax'],
                                    'retail_delivery_fee' => $row['payment']['retail_delivery_fee'],
                                    'seller_discount' => $row['payment']['seller_discount'],
                                    'shipping_fee' => $row['payment']['shipping_fee'],
                                    'shipping_fee_platform_discount' => $row['payment']['shipping_fee_platform_discount'],
                                    'shipping_fee_seller_discount' => $row['payment']['shipping_fee_seller_discount'],
                                    'shipping_fee_tax' => $row['payment']['shipping_fee_tax'],
                                    'small_order_fee' => $row['payment']['small_order_fee'],
                                    'sub_total' => $row['payment']['sub_total'],
                                    'tax' => $row['payment']['tax'],
                                    'total_amount' => $row['payment']['total_amount']
                                );

                                //print_r($data_payment);
                                $this->tiktok_order_payment_model->insert($data_payment);
                            }
                        }   

                    }//if(empty($chk_data_db)){

                }//else Not death
            }//foreach($res['data']['orders'] as $row){
        }//if(count($res['data']['orders']) > 0){

    }

    function check_num_lineitem($arr_lineitems,$product_id){

        $num_qty = 0;

        foreach($arr_lineitems as $lineitem){

            if($lineitem['product_id'] == $product_id ){

                $num_qty = $num_qty + 1;

            }

        }

        return $num_qty;
    }

    function chk_for_insert_packed($row,$status_order){

        $date_diff_status = $this->date_util->date_diff($row["create_time"],$row["update_time"]);
        $data_packed_dup = "";

        $array_status_cancel = array('CANCELLED', 'Canceled');

        if (!in_array($status_order, $array_status_cancel)){
            $arr_chk_packed = $this->tiktok_orders_model->get_by_id_status($row["id"],'Packet');
            if(empty($arr_chk_packed)){
                $data_packed_dup=array(
                    'order_id' => $row['id'],
                    'buyer_email' => $row['buyer_email'],
                    'buyer_message' => $row['buyer_message'],
                    'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                    'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                    'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                    'delivery_option_id' => $row['delivery_option_id'],
                    'delivery_option_name' => $row['delivery_option_name'],
                    'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                    'delivery_type' => $row['delivery_type'],
                    'fulfillment_type' => $row['fulfillment_type'],
                    'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                    'is_cod' => $row['is_cod'],
                    'is_on_hold_order' => $row['is_on_hold_order'],
                    'is_replacement_order' => $row['is_replacement_order'],
                    'is_sample_order' => $row['is_sample_order'],
                    'need_upload_invoice' => $row['need_upload_invoice'],
                    'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                    'payment_method_name' => $row['payment_method_name'],
                    'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                    'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                    'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                    'shipping_provider' => $row['shipping_provider'],
                    'shipping_provider_id' => $row['shipping_provider_id'],
                    'shipping_type' => $row['shipping_type'],
                    'status' => 'Packet',
                    'tracking_number' => $row['tracking_number'],
                    'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                    'update_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                    'user_id' => $row['user_id'],
                    'warehouse_id' => $row['warehouse_id']

                ); 
            }
        }

        if (in_array($status_order, $array_status_cancel)){
            //1800 = 30 hour

            $hour_cn = $this->check_date_holiday($this->date_util->datetime_unix_to_dt($row['create_time']));

            if($date_diff_status > $hour_cn){
                $arr_chk_packed = $this->tiktok_orders_model->get_by_id_status($row["id"],'Packet');
                if(empty($arr_chk_packed)){
                    $data_packed_dup=array(
                        'order_id' => $row['id'],
                        'buyer_email' => $row['buyer_email'],
                        'buyer_message' => $row['buyer_message'],
                        'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                        'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                        'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                        'delivery_option_id' => $row['delivery_option_id'],
                        'delivery_option_name' => $row['delivery_option_name'],
                        'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                        'delivery_type' => $row['delivery_type'],
                        'fulfillment_type' => $row['fulfillment_type'],
                        'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                        'is_cod' => $row['is_cod'],
                        'is_on_hold_order' => $row['is_on_hold_order'],
                        'is_replacement_order' => $row['is_replacement_order'],
                        'is_sample_order' => $row['is_sample_order'],
                        'need_upload_invoice' => $row['need_upload_invoice'],
                        'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                        'payment_method_name' => $row['payment_method_name'],
                        'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                        'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                        'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                        'shipping_provider' => $row['shipping_provider'],
                        'shipping_provider_id' => $row['shipping_provider_id'],
                        'shipping_type' => $row['shipping_type'],
                        'status' => 'Packet',
                        'tracking_number' => $row['tracking_number'],
                        'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                        'update_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                        'user_id' => $row['user_id'],
                        'warehouse_id' => $row['warehouse_id']

                    ); 
                }
            }

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

            //echo "next date name >> ".$next_date_name."<br>";

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

        return $time_re;

    }

    function get_order_more(){

        $arr_orders = $this->tiktok_orders_model->get_order_not_die();
        //print_r($arr_orders);
        $num = 0;
        if(!empty($arr_orders)){
          foreach($arr_orders as $arr_order){
              $updated_at = $arr_order['update_time'];
                  if($arr_order['status'] == 'Packet'){

                      if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_id'],$arr_order['status']);
                          $num = $num +1;
                      }

                  }elseif($arr_order['status'] == 'Shipped'){

                      //echo $arr_order['date_to_now']."<br>";
                      if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_id'],$arr_order['status']);
                          $num = $num +1;
                      }
                      
                  }else{
                    if($arr_order['date_to_now'] > 1440){
                          $data = $this->getOrder_insert_orderno($arr_order['order_id'],$arr_order['status']);
                          $num = $num +1;
                      }
                  }
                  if($num == 20){
                    break;
                  }
        }
      }
    }

    public function getOrder_insert_orderno($order_id,$status)
     {

        $api_url = "/order/202309/orders";

        $querys = array(
            'shop_cipher' => TIKTOK_SHOP_CIPHER,
            'ids' => $order_id
        );

        $make_url = $this->tiktok_bl->make_url('GET',$api_url,$querys,NULL,NULL);

        $res = $this->tiktok_bl->CallApiToken('GET',$make_url,NULL);

        if(!empty($res['data'])){
            //$res['data']['orders'][0]['buyer_email'];

            $row = $res['data']['orders'][0];

            $chk_data_db = $this->tiktok_orders_model->get_by_id_status($order_id,$status);
            if(empty($chk_data_db)){

                $arr_datas = array(
                    'order_id' => $row['id'],
                    'buyer_email' => $row['buyer_email'],
                    'buyer_message' => $row['buyer_message'],
                    'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                    'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                    'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                    'delivery_option_id' => $row['delivery_option_id'],
                    'delivery_option_name' => $row['delivery_option_name'],
                    'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                    'delivery_type' => $row['delivery_type'],
                    'fulfillment_type' => $row['fulfillment_type'],
                    'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                    'is_cod' => $row['is_cod'],
                    'is_on_hold_order' => $row['is_on_hold_order'],
                    'is_replacement_order' => $row['is_replacement_order'],
                    'is_sample_order' => $row['is_sample_order'],
                    'need_upload_invoice' => $row['need_upload_invoice'],
                    'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                    'payment_method_name' => $row['payment_method_name'],
                    'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                    'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                    'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                    'shipping_provider' => $row['shipping_provider'],
                    'shipping_provider_id' => $row['shipping_provider_id'],
                    'shipping_type' => $row['shipping_type'],
                    'status' => $row['status'],
                    'tracking_number' => $row['tracking_number'],
                    'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                    'update_time' => $this->date_util->datetime_unix_to_dt($data_update_at),
                    'user_id' => $row['user_id'],
                    'warehouse_id' => $row['warehouse_id']
                );
                //print_r($arr_datas);
                $this->tiktok_orders_model->insert($arr_datas);
            }else{//if(empty($chk_data_db)){  
                $data_up = array(
                    'update_time' => DATE_TIME_NOW
                );
                $this->tiktok_orders_model->update_order_status($row["id"],$status,$data_up);
                //print_r($data_up);
            }  

        }

        



     }

    function create_textinvoiceid_v1(){

       // $array_status_death =array('lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','delivered','failed_delivery','canceled','confirmed');
        $array_status_not_death = array('COMPLETED');

        //'lost_by_3pl','damaged_by_3pl','shipped_back_success','returned','failed_delivery','canceled','unpaid'

        $arr_ttk_taxs = $this->tiktok_orders_model->select_by_status_last_arr($array_status_not_death,50);

        foreach($arr_ttk_taxs as $arr_ttk_tax){

            $arr_chk_or = $this->tiktok_taxinvoiceid_model->select_taxinvoiceid_by_orderno($arr_ttk_tax['order_id']);
            if(empty($arr_chk_or)){
                $arr_lastorder = $this->tiktok_taxinvoiceid_model->last_order_code_by_yymm($arr_ttk_tax['yyyymm']);   
                print_r($arr_lastorder);

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->tiktok_bl->get_tiktok_code($arr_lastorder['taxinvoiceID'],$arr_ttk_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_ttk_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_ttk_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->tiktok_taxinvoiceid_model->insert($arr_new_invoice_id);


               }else{

                $new_textinvoiceID = $this->tiktok_bl->get_tiktok_code('no',$arr_ttk_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_ttk_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_ttk_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->tiktok_taxinvoiceid_model->insert($arr_new_invoice_id);

               }
            }

       }
    }

    function get_orders_bk(){

        $api_url = "/order/202309/orders/search";

        $querys = array(
            'sort_order' => 'ASC',
            'sort_field' => 'create_time',
            'shop_cipher' => TIKTOK_SHOP_CIPHER,
            'page_size' => '10'
        );

        $start_time = strtotime('2025-02-05 00:00:00');
        $stop_time = strtotime('2025-02-05 20:14:00');

        $bodys = array(
            'order_status' => 'COMPLETED',
            'shipping_type' => 'TIKTOK',
            'create_time_ge' => $start_time
            //'create_time_lt' => $stop_time
        );

        $make_url = $this->tiktok_bl->make_url('POST',$api_url,$querys,$bodys,NULL);

        $res = $this->tiktok_bl->CallApiToken('POST',$make_url,$bodys);
        //print_r($res);
        if(count($res['data']['orders']) > 0){

            foreach($res['data']['orders'] as $row){
               // echo $row['buyer_email']."<br>";

                //echo gmdate("Y-m-d h:i:s", $row['rts_sla_time']);
                
                //echo $this->date_util->datetime_unix_to_dt($row['rts_sla_time']);

            $arr_datas = array(
                'order_id' => $row['id'],
                'buyer_email' => $row['buyer_email'],
                'buyer_message' => $row['buyer_message'],
                'cancel_order_sla_time' => $this->date_util->datetime_unix_to_dt($row['cancel_order_sla_time']),
                'collection_time' => $this->date_util->datetime_unix_to_dt($row['collection_time']),
                'create_time' => $this->date_util->datetime_unix_to_dt($row['create_time']),
                'delivery_option_id' => $row['delivery_option_id'],
                'delivery_option_name' => $row['delivery_option_name'],
                'delivery_time' => $this->date_util->datetime_unix_to_dt($row['delivery_time']),
                'delivery_type' => $row['delivery_type'],
                'fulfillment_type' => $row['fulfillment_type'],
                'has_updated_recipient_address' => $row['has_updated_recipient_address'],
                'is_cod' => $row['is_cod'],
                'is_on_hold_order' => $row['is_on_hold_order'],
                'is_replacement_order' => $row['is_replacement_order'],
                'is_sample_order' => $row['is_sample_order'],
                'need_upload_invoice' => $row['need_upload_invoice'],
                'paid_time' => $this->date_util->datetime_unix_to_dt($row['paid_time']),
                'payment_method_name' => $row['payment_method_name'],
                'pick_up_cut_off_time' => $this->date_util->datetime_unix_to_dt($row['pick_up_cut_off_time']),
                'rts_sla_time' => $this->date_util->datetime_unix_to_dt($row['rts_sla_time']),
                'rts_time' => $this->date_util->datetime_unix_to_dt($row['rts_time']),
                'shipping_provider' => $row['shipping_provider'],
                'shipping_provider_id' => $row['shipping_provider_id'],
                'shipping_type' => $row['shipping_type'],
                'status' => $row['status'],
                'tracking_number' => $row['tracking_number'],
                'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                'update_time' => $this->date_util->datetime_unix_to_dt($row['update_time']),
                'user_id' => $row['user_id'],
                'warehouse_id' => $row['warehouse_id']
            );
            //print_r($arr_datas);
            //$this->tiktok_orders_model->insert($arr_datas);

            //print_r($row['line_items']);
            if(count($row['line_items']) > 0){
                print_r($row['line_items']);
                foreach($row['line_items'] as $line_item){

                    //echo $line_item['combined_listing_skus'][0]['seller_sku'];
                    //print_r($line_item);

                    $data_line = array(
                        'line_items_id' => $line_item['id'],
                        'order_id' => $row['id'],
                        'buyer_service_fee' => $line_item['buyer_service_fee'],
                        'cancel_reason' => $line_item['cancel_reason'],
                        'cancel_user' => $line_item['cancel_user'],
                        'currency' => $line_item['currency'],
                        'display_status' => $line_item['display_status'],
                        'is_gift' => $line_item['is_gift'],
                        'original_price' => $line_item['original_price'],
                        'package_id' => $line_item['package_id'],
                        'package_status' => $line_item['package_status'],
                        'platform_discount' => $line_item['platform_discount'],
                        'product_id' => $line_item['product_id'],
                        'product_name' =>$line_item['product_name'],
                        'retail_delivery_fee' =>$line_item['retail_delivery_fee'],
                        'rts_time' => $this->date_util->datetime_unix_to_dt($line_item['rts_time']),
                        'sale_price' => $line_item['sale_price'],
                        'seller_discount' => $line_item['seller_discount'],
                        'seller_sku' => $line_item['seller_sku'],
                        'shipping_provider_id' => $line_item['shipping_provider_id'],
                        'shipping_provider_name' => $line_item['shipping_provider_name'],
                        'sku_id' => $line_item['sku_id'],
                        'sku_image' => $line_item['sku_image'],
                        'sku_name' => $line_item['sku_name'],
                        'sku_type' => $line_item['sku_type'],
                        'small_order_fee' => $line_item['small_order_fee'],
                        'tracking_number' => $line_item['tracking_number'],

                    );
                }
            }

            //print_r($data_line);
            //$this->tiktok_line_items_model->insert($data_line);



            

            }
        }

    }

    function get_order_details(){

        $id = '579688864124078208';

        $api_url = "/order/202309/orders";

        //$make_url = $this->tiktok_bl->make_url('GET',$api_url,$param,$sign);

        $querys = array(
            'shop_cipher' => TIKTOK_SHOP_CIPHER,
            'ids' => $id
        );

        $make_url = $this->tiktok_bl->make_url('GET',$api_url,$querys,NULL,NULL);

        $res = $this->tiktok_bl->CallApiToken('GET',$make_url,NULL);

        $row = $res['data']['orders'][0];

        echo ">>>".$row['buyer_email']."<<<<br>";

        print_r($res);

        if(!empty($res['data'])){
            echo "not empty";
        }else{
            echo "empty";
        }

        


    }
     
    function tik_import_sale_chk(){
        $this->load->view('tiktok/tik_import_sale_chk');
     }

 function tik_import_sale_chk_action(){

    $file1_name = "";
    $this->load->library('Upload_secure', [
          'psp_inbox_dir'  => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
        ]);

        $res = $this->upload_secure->upload_file('upload_file1');

    if ($res['is_upload'] === 1) {
        //$file_s = "./uploads/xls/".$file1_name;
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
            $order_tmp = "";

            for ($row = 3; $row <= $highestRow; $row++){ 
                $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);
                $ctime = $rowData[0][25];//Z
                $order_id = $rowData[0][0];
                $order_status = $rowData[0][1];
                $cancel_type = $rowData[0][3];

                if($cancel_type == NULL){
                    $cancel_type = "";
                }
                $products = $rowData[0][6];
                $quantity = $rowData[0][9];

                $SubtotalAfterDiscount = $this->explode_thb($rowData[0][15]);//P
                $ShippingFeeAfterDiscount = $this->explode_thb($rowData[0][16]);//Q
                $OriginalShippingFee = $this->explode_thb($rowData[0][17]);//R
                $ShippingFeePlatformDiscount = $this->explode_thb($rowData[0][19]);//T

                $SmallOrderFee = $this->explode_thb($rowData[0][22]);//W
                $OrderAmount = $this->explode_thb($rowData[0][23]);//X
                $OrderRefundAmount = $this->explode_thb($rowData[0][24]);//Y
                $AmountExcludeVat = $OrderAmount/1.07;
                $Vat = $OrderAmount-$AmountExcludeVat;

                $date = str_replace('/', '-', $ctime);
                $date_to_db = date('Y/m/d H:i:s', strtotime($date));

                $PaidTime = $rowData[0][26];//AA
                $RTSTime = $rowData[0][27];//AB
                $ShippedTime = $rowData[0][28];//AC

                $date26 = str_replace('/', '-', $PaidTime);
                $date_to_db26 = date('Y/m/d H:i:s', strtotime($date26));

                $date27 = str_replace('/', '-', $RTSTime);
                $date_to_db27 = date('Y/m/d H:i:s', strtotime($date27));

                $date28 = str_replace('/', '-', $ShippedTime);
                $date_to_db28 = date('Y/m/d H:i:s', strtotime($date28));

                $CancelledTime = $rowData[0][30];//AE
                $date30 = str_replace('/', '-', $CancelledTime);
                $date_to_db30 = date('Y/m/d H:i:s', strtotime($date30));

                $CancelReason = $rowData[0][32];//AG
                $TrackingID = $rowData[0][35];//AJ

                

                if($order_id != $order_tmp){

                    $data = array(

                        'ctime' => $date_to_db,
                        'order_id' => $order_id,
                        'order_status' => $order_status,
                        'cancel_type' => $cancel_type,
                        'products' => $products,
                        'quantity' => $quantity,
                        'SubtotalAfterDiscount' => $SubtotalAfterDiscount,
                        'ShippingFeeAfterDiscount' => $ShippingFeeAfterDiscount,
                        'OriginalShippingFee' => $OriginalShippingFee,
                        'ShippingFeePlatformDiscount' => $ShippingFeePlatformDiscount,
                        'SmallOrderFee' => $SmallOrderFee,
                        'OrderAmount' => $OrderAmount,
                        'OrderRefundAmount' => $OrderRefundAmount,
                        'AmountExcludeVat' => round($AmountExcludeVat,2),
                        'Vat' => round($Vat,2),
                        'PaidTime' => $date_to_db26,
                        'RTSTime' => $date_to_db27,
                        'ShippedTime' => $date_to_db28,
                        'CancelledTime' => $date_to_db30,
                        'CancelReason' => $CancelReason,
                        'TrackingID' => $TrackingID,
                        'code' => $keygen

                    );

                    $this->tiktok_data_model->insert($data);

                    $order_tmp = $order_id;

                }else{

                    $data = array(

                        'ctime' => $date_to_db,
                        'order_id' => $order_id,
                        'order_status' => $order_status,
                        'cancel_type' => $cancel_type,
                        'products' => $products,
                        'quantity' => $quantity,
                        'SubtotalAfterDiscount' => $SubtotalAfterDiscount,
                        'ShippingFeeAfterDiscount' => 0,
                        'OriginalShippingFee' => $OriginalShippingFee,
                        'ShippingFeePlatformDiscount' => $ShippingFeePlatformDiscount,
                        'SmallOrderFee' => $SmallOrderFee,
                        'OrderAmount' => 0,
                        'OrderRefundAmount' => 0,
                        'AmountExcludeVat' => 0,
                        'Vat' => 0,
                        'PaidTime' => $date_to_db26,
                        'RTSTime' => $date_to_db27,
                        'ShippedTime' => $date_to_db28,
                        'CancelledTime' => $date_to_db30,
                        'CancelReason' => $CancelReason,
                        'TrackingID' => $TrackingID,
                        'code' => $keygen

                    );

                    $this->tiktok_data_model->insert($data);

                }
            
             

                 
            }//for ($row = 3; $row <= $highestRow; $row++){ 


        }   

        //export to excel
        $this->generateXls($keygen);

    }

 }

 public function generateXls($keygen) {

    $arr_datas =$this->tiktok_data_model->select_by_code($keygen);
        // create file name
        $fileName = 'data-'.time().'.xlsx';  
        // load excel library
        $this->load->library('Lib_excel');
        //$listInfo = $this->export->exportList();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->createSheet();
        $objPHPExcel->createSheet(0);
       // $objPHPExcel->createSheet(1);

        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');

        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Order Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Cancelation');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Products');       
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Quantity'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Subtotal'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ShippingFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Original'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'ShippingFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SmallOrderFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Order'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'OrderRefund'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Created Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Paid Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'RTS Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Shipped Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Cancelled Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U1', 'Cancel Reason'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Tracking ID'); 

        $objPHPExcel->getActiveSheet()->SetCellValue('D2', '/Return Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'ShippingFee');
        $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'PlatformDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M2', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O2', 'exclude Vat'); 
        // set Row

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);
        $rowCount = 3;
        $order_tmp = "";
        foreach($arr_datas as $arr_data){

            $CancelledTime ="";
            $CancelReason ="";
            $TrackingID ="";

            $PaidTime = $arr_data['PaidTime'];
            $RTSTime = $arr_data['RTSTime'];
            $ShippedTime = $arr_data['ShippedTime'];

            if($arr_data['order_status'] == "Canceled"){

                $CancelledTime = $arr_data['CancelledTime'];
                $CancelReason = $arr_data['CancelReason'];
                $TrackingID = $arr_data['TrackingID'];
                $PaidTime = "";
                $RTSTime = "";
                $ShippedTime = "";

            }

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $arr_data['ctime']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $arr_data['order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $arr_data['order_status']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $arr_data['cancel_type']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $arr_data['products']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $arr_data['quantity']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $arr_data['SubtotalAfterDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $arr_data['ShippingFeeAfterDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $arr_data['OriginalShippingFee']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $arr_data['ShippingFeePlatformDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $arr_data['SmallOrderFee']);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $arr_data['OrderAmount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $arr_data['OrderRefundAmount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $arr_data['Vat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $arr_data['AmountExcludeVat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $arr_data['ctime']);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $PaidTime);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $RTSTime);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $ShippedTime);
            $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $CancelledTime);
            $objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $CancelReason);
            $objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $TrackingID);
            

            if($arr_data['order_id'] == $order_tmp){
                $roll_bfo = $rowCount-1;
                $this->cellColor($objPHPExcel,'A'.$roll_bfo.':S'.$roll_bfo, '64f70b');
                $this->cellColor($objPHPExcel,'A'.$rowCount.':S'.$rowCount, '64f70b');
                $order_tmp = $arr_data['order_id'];
            }else{
                $order_tmp = $arr_data['order_id'];
            }

            /*if($arr_data['order_status'] == "Canceled"){
                $this->cellColor($objPHPExcel,'A'.$rowCount.':V'.$rowCount, 'f53a3a');
            }*/

           // $this->borderxls($objPHPExcel,$objPHPExcel,'A'.$rowCount.':O'.$rowCount);

                $rowCount = $rowCount+1;
            
        }

        $this->cellColor($objPHPExcel,'A1:V1', 'ffca2c');
        $this->cellColor($objPHPExcel,'A2:V2', 'ffca2c');
        $objPHPExcel->getActiveSheet()->setTitle("Order detail");

//----------------sheet 2-------

        $objPHPExcel->setActiveSheetIndex(1);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Products');       
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Quantity'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Subtotal'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'ShippingFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'SmallOrderFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Order'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Amount'); 

        $objPHPExcel->getActiveSheet()->SetCellValue('E2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'exclude Vat'); 
        // set Row

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);

        $rowCount2 = 3;
        $order_tmp = "";
        foreach($arr_datas as $arr_data){

            $CancelledTime ="";
            $CancelReason ="";
            $TrackingID ="";

            $PaidTime = $arr_data['PaidTime'];
            $RTSTime = $arr_data['RTSTime'];
            $ShippedTime = $arr_data['ShippedTime'];

            $insert_date = true;

            if($arr_data['order_status'] == "Canceled"){

                $CancelledTime = $arr_data['CancelledTime'];
                $CancelReason = $arr_data['CancelReason'];
                $TrackingID = $arr_data['TrackingID'];
                $PaidTime = "";
                $RTSTime = "";
                $ShippedTime = "";

                if($TrackingID == ""){
                    $insert_date = false;
                }

            }

            if($insert_date){

                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount2, $arr_data['ctime']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount2, $arr_data['order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount2, $arr_data['products']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount2, $arr_data['quantity']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount2, $arr_data['SubtotalAfterDiscount']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount2, $arr_data['ShippingFeeAfterDiscount']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount2, $arr_data['SmallOrderFee']);
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount2, $arr_data['OrderAmount']);
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount2, $arr_data['Vat']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount2, $arr_data['AmountExcludeVat']);
                

                if($arr_data['order_id'] == $order_tmp){
                    $roll_bfo = $rowCount2-1;
                    $this->cellColor($objPHPExcel,'A'.$roll_bfo.':J'.$roll_bfo, '64f70b');
                    $this->cellColor($objPHPExcel,'A'.$rowCount2.':J'.$rowCount2, '64f70b');
                    $order_tmp = $arr_data['order_id'];
                }else{
                    $order_tmp = $arr_data['order_id'];
                }

                $rowCount2 = $rowCount2+1;
            }
            
        }

        $this->cellColor($objPHPExcel,'A1:J1', 'ffca2c');
        $this->cellColor($objPHPExcel,'A2:J2', 'ffca2c');
        $objPHPExcel->getActiveSheet()->setTitle("Order detail filter");

//----------------sheet 3-------        

        $objPHPExcel->setActiveSheetIndex(2);
        // set Header
        
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');

        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Order Status');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Cancelation');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Products');       
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Quantity'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Subtotal'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ShippingFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Original'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'ShippingFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SmallOrderFee'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Order'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'OrderRefund'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Created Time'); 

        $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Cancelled Time'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Cancel Reason'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Tracking ID'); 

        $objPHPExcel->getActiveSheet()->SetCellValue('D2', '/Return Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'AfterDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'ShippingFee');
        $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'PlatformDiscount');
        $objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M2', 'Amount'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O2', 'exclude Vat'); 
        // set Row

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
        $rowCount3 = 3;
        $order_tmp = "";

        $arr_cancel_datas =$this->tiktok_data_model->select_by_code_status_trackid($keygen,"Canceled");
        foreach($arr_cancel_datas as $arr_data){



            $CancelledTime = $arr_data['CancelledTime'];
            $CancelReason = $arr_data['CancelReason'];
            $TrackingID = $arr_data['TrackingID'];


            

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount3, $arr_data['ctime']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount3, $arr_data['order_id'],PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount3, $arr_data['order_status']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount3, $arr_data['cancel_type']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount3, $arr_data['products']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount3, $arr_data['quantity']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount3, $arr_data['SubtotalAfterDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount3, $arr_data['ShippingFeeAfterDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount3, $arr_data['OriginalShippingFee']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount3, $arr_data['ShippingFeePlatformDiscount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount3, $arr_data['SmallOrderFee']);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount3, $arr_data['OrderAmount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount3, $arr_data['OrderRefundAmount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount3, $arr_data['Vat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount3, $arr_data['AmountExcludeVat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount3, $arr_data['ctime']);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount3, $CancelledTime);
            $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount3, $CancelReason);
            $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount3, $TrackingID);
            

            if($arr_data['order_id'] == $order_tmp){
                $roll_bfo = $rowCount3-1;
                $this->cellColor($objPHPExcel,'A'.$roll_bfo.':S'.$roll_bfo, '64f70b');
                $this->cellColor($objPHPExcel,'A'.$rowCount3.':S'.$rowCount3, '64f70b');
                $order_tmp = $arr_data['order_id'];
            }else{
                $order_tmp = $arr_data['order_id'];
            }

                $rowCount3 = $rowCount3+1;
            
        }

        $this->cellColor($objPHPExcel,'A1:S1', 'ffca2c');
        $this->cellColor($objPHPExcel,'A2:S2', 'ffca2c');

        $objPHPExcel->getActiveSheet()->setTitle("Order Cancel");


        $filename = "tiktok_". date("Y-m-d-H-i-s").".xls";
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
        $objWriter->save('php://output'); 

    }

    function cellColor($objPHPExcel,$cells,$color){

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                 'rgb' => $color
            )
        ));
    }

    function borderxls($objPHPExcel,$cells){
        $styleArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray($styleArray);

        /*$objPHPExcel->getActiveSheet()->getStyle(
            $cells . 
            $objPHPExcel->getActiveSheet()->getHighestColumn() . 
            $objPHPExcel->getActiveSheet()->getHighestRow()
        )->applyFromArray($styleArray);*/
    }

 function explode_thb_bk($data){

    $baht = 0;
    if(!empty($data)){
        $exp = explode(" ",$data);
        if(!empty($exp[1])){
            //$t1 = str_replace(',','',$exp[1]);
            $baht = (float)$exp[1];
         }
     }

     return $baht;
 }

 function explode_thb($data){

    $baht = 0;
    if(!empty($data)){
        $exp = explode(" ",$data);
        if(!empty($exp[1])){
            $t1 = str_replace(',','',$exp[1]);
            $baht = (float)$t1;
         }else{
            $baht = (float)$data;
         }
     }

     return $baht;
 }

 function floatvalue(){
    $data = "Thb 1,073.99";
    $exp = explode(" ",$data);

    $val = str_replace(",",".",$exp[1]);
    $val = preg_replace('/\.(?=.*\.)/', '', $val);

    echo $val;
    //return floatval($val);
}


 function test_exp(){

    $data = "Thb 1,073.99";

    $baht = 0;
    if(!empty($data)){
        $exp = explode(" ",$data);
        if(!empty($exp[1])){
            //$t1 = str_replace(',','',$exp[1]);
            $baht = (float)$exp[1];
            //$baht = round($t1,2);
         }
     }

     echo $baht;

 }

 function tik_import_sale_data(){ //insert into tiktok_orders
        $this->load->view('tiktok/tik_import_sale_data');
     }

 function tik_import_sale_data_action(){

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
            $row_data = $highestRow-2;

            $order_sn_old = "";

            $data_order_all = array();
            $data_item_all = array();
            $num =0;
            $keygen = $this->random_util->create_random_number(8);
            $totol_price = 0;
            $totol_ship = 0;
            $order_tmp = "";

            $original_shipping_fee_pay = 0;
            $original_total_product_price_pay = 0;
            $platform_discount_pay = 0;
            $seller_discount_pay = 0;
            $shipping_fee_pay = 0;
            $shipping_fee_platform_discount_pay = 0;
            $shipping_fee_seller_discount_pay = 0;
            $sub_total_pay = 0;
            $total_amount_pay = 0;
            $order_id_tmp ="";


            for ($row = 3; $row <= $highestRow; $row++){ 

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);

                $order_id = $rowData[0][0];
                $order_qty = $rowData[0][9];

                $num =$num+1;

                $cancel_order_sla_time = $rowData[0][29];
                $cancel_order_sla_time = str_replace('/', '-', $cancel_order_sla_time);
                $cancel_order_sla_time = date('Y/m/d H:i:s', strtotime($cancel_order_sla_time));

                $create_time = $rowData[0][24];
                $create_time = str_replace('/', '-', $create_time);
                $create_time = date('Y/m/d H:i:s', strtotime($create_time));

                $delivery_option_name = $rowData[0][35];
                $delivery_option_id = "";
                if($delivery_option_name == "การจัดส่งมาตรฐาน"){
                    $delivery_option_name = "Standard shipping";
                    $delivery_option_id = "7057024661824997122"; 
                }
                
                $delivery_time = $rowData[0][28];
                $delivery_time = str_replace('/', '-', $delivery_time);
                $delivery_time = date('Y/m/d H:i:s', strtotime($delivery_time));

                $delivery_type = "HOME_DELIVERY";
                $fulfillment_type = $rowData[0][32];

                if($fulfillment_type == "Fulfillment by seller"){
                    $fulfillment_type = "FULFILLMENT_BY_SELLER";
                }

                $paid_time = $rowData[0][25];
                $paid_time = str_replace('/', '-', $paid_time);
                $paid_time = date('Y/m/d H:i:s', strtotime($paid_time));

                $payment_method_name = $rowData[0][47];

                if($payment_method_name == "เก็บเงินปลายทาง"){
                    $payment_method_name = "Cash on delivery";
                }

                $rts_time = $rowData[0][26];
                $rts_time = str_replace('/', '-', $rts_time);
                $rts_time = date('Y/m/d H:i:s', strtotime($rts_time));

                $shipping_provider = $rowData[0][36];
                $shipping_provider_id="";
                if($shipping_provider == "J&T Express"){
                    $shipping_provider_id="6841743441349706241";
                }

                $shipping_type = "TIKTOK";
                $status = $rowData[0][1];
                $tracking_number = $rowData[0][34];
                $warehouse_id = "7085935865733695238";


                //----- Line Item----------

                $cancel_reason = $rowData[0][3];
                $original_price = $this->explode_thb($rowData[0][11]);
                $platform_discount = $this->explode_thb($rowData[0][13]);
                $seller_discount = $this->explode_thb($rowData[0][14]);
                $sale_price = $this->explode_thb($rowData[0][22]);
                $sub_total = $this->explode_thb($rowData[0][15]);

                $product_name = $rowData[0][7];
                $seller_sku = $rowData[0][6];
                $sku_id = $rowData[0][5];
                $small_order_fee = $rowData[0][21];

                //----- Line Item----------


                

                //echo $num.">>".$order_tmp.">>>>>".$order_id."<br>";

                echo $row_data.">>order>>".$num.">>>".$order_id."<br>";

                if($order_tmp != $order_id){

                    //echo $num.">>".$order_tmp.">>>>>".$order_id."<br>";

                    $arr_datas = array(
                        'order_id' => $order_id,
                        'cancel_order_sla_time' => $cancel_order_sla_time,
                        'create_time' => $create_time,
                        'delivery_option_id' => $delivery_option_id,
                        'delivery_option_name' => $delivery_option_name,
                        'delivery_time' => $delivery_time,
                        'delivery_type' => $delivery_type,
                        'fulfillment_type' => $fulfillment_type,
                        'paid_time' => $paid_time,
                        'payment_method_name' => $payment_method_name,
                        'rts_time' => $rts_time,
                        'shipping_provider' => $shipping_provider,
                        'shipping_provider_id' => $shipping_provider_id,
                        'shipping_type' => $shipping_type,
                        'status' => $status,
                        'tracking_number' => $tracking_number,
                        /*'tts_sla_time' => $this->date_util->datetime_unix_to_dt($row['tts_sla_time']),
                        'update_time' => $this->date_util->datetime_unix_to_dt($data_update_at),
                        'user_id' => $row['user_id'],*/
                        'warehouse_id' => $warehouse_id
                    );
                    //print_r($arr_datas);
                    

                    $data_line = array(

                        'order_id' => $order_id,
                        'cancel_reason' => $cancel_reason,
                        'currency' => "THB",
                        'display_status' => $status,
                        'original_price' => $original_price,
                        'order_qty' => $order_qty,
                        'platform_discount' => $platform_discount,
                        'product_name' =>$product_name,
                        'rts_time' => $rts_time,
                        'sale_price' => $sale_price,
                        'seller_discount' => $seller_discount,
                        'seller_sku' => $seller_sku,
                        'shipping_provider_id' => $shipping_provider_id,
                        'shipping_provider_name' => $shipping_provider,
                        'sku_id' => $sku_id,
                        'sku_name' => 'Default',
                        'small_order_fee' => $small_order_fee,
                        'tracking_number' => $tracking_number,

                    );

                    //print_r($data_line);

                    $arr_data_dups = array(
                        'order_id' => $order_id,
                        'cancel_order_sla_time' => $cancel_order_sla_time,
                        'create_time' => $create_time,
                        'delivery_option_id' => $delivery_option_id,
                        'delivery_option_name' => $delivery_option_name,
                        'delivery_time' => $delivery_time,
                        'delivery_type' => $delivery_type,
                        'fulfillment_type' => $fulfillment_type,
                        'paid_time' => $paid_time,
                        'payment_method_name' => $payment_method_name,
                        'rts_time' => $rts_time,
                        'shipping_provider' => $shipping_provider,
                        'shipping_provider_id' => $shipping_provider_id,
                        'shipping_type' => $shipping_type,
                        'status' => 'Packet',
                        'tracking_number' => $tracking_number,
                        'update_time' => $create_time,
                        'warehouse_id' => $warehouse_id
                    );

                    $chk_data = $this->tiktok_orders_model->get_by_order_id($order_id);

                    if(empty($chk_data)){
                        $this->tiktok_orders_model->insert($arr_datas);

                        if(!empty($tracking_number)){

                            $this->tiktok_orders_model->insert($arr_data_dups);

                        }

                        $this->tiktok_line_items_model->insert($data_line);
                    }

                    if($num == 1){

                    //--shipping fee--------
                    $order_id_tmp = $order_id;
                    $original_shipping_fee_pay = $this->explode_thb($rowData[0][17]);
                    $shipping_fee_pay = $this->explode_thb($rowData[0][16]);
                    $shipping_fee_platform_discount_pay = $this->explode_thb($rowData[0][19]);
                    $shipping_fee_seller_discount_pay = $this->explode_thb($rowData[0][18]);

                    $sub_total_pay = $sub_total_pay+$sub_total;
                    $original_total_product_price_pay = $original_total_product_price_pay+($original_price*$order_qty);
                    $platform_discount_pay = $platform_discount_pay+$platform_discount;
                    $seller_discount_pay = $seller_discount_pay+$seller_discount;
                    
                    $total_amount_pay = $sale_price;

                    //--shipping fee--------

                    }elseif(($num > 1)and($num < $row_data)){

                        $arr_pay_data = array(

                            'order_id' => $order_id_tmp,
                            'currency' => 'THB',
                            'original_shipping_fee' => $original_shipping_fee_pay,
                            'original_total_product_price' => $original_total_product_price_pay,
                            'platform_discount' => $platform_discount_pay,
                            'seller_discount' => $seller_discount_pay,
                            'shipping_fee' => $shipping_fee_pay,
                            'shipping_fee_platform_discount' => $shipping_fee_platform_discount_pay,
                            'shipping_fee_seller_discount' => $shipping_fee_seller_discount_pay,
                            'sub_total' => $sub_total_pay,
                            'total_amount' => $total_amount_pay

                        );
                        
                        $this->tiktok_order_payment_model->insert($arr_pay_data);

                        $original_shipping_fee_pay = 0;
                        $original_total_product_price_pay = 0;
                        $platform_discount_pay = 0;
                        $seller_discount_pay = 0;
                        $shipping_fee_pay = 0;
                        $shipping_fee_platform_discount_pay = 0;
                        $shipping_fee_seller_discount_pay = 0;
                        $sub_total_pay = 0;
                        $total_amount_pay = 0;

                        $order_id_tmp = $order_id;
                        $original_shipping_fee_pay = $this->explode_thb($rowData[0][17]);
                        $shipping_fee_pay = $this->explode_thb($rowData[0][16]);
                        $shipping_fee_platform_discount_pay = $this->explode_thb($rowData[0][19]);
                        $shipping_fee_seller_discount_pay = $this->explode_thb($rowData[0][18]);

                        $sub_total_pay = $sub_total_pay+$sub_total;
                        $original_total_product_price_pay = $original_total_product_price_pay+($original_price*$order_qty);
                        $platform_discount_pay = $platform_discount_pay+$platform_discount;
                        $seller_discount_pay = $seller_discount_pay+$seller_discount;
                        
                        $total_amount_pay = $sale_price;


                    }elseif($num == $row_data){

                        echo ">>last>>order>>".$num.">>>".$order_id;


                        $arr_pay_data = array(

                            'order_id' => $order_id_tmp,
                            'currency' => 'THB',
                            'original_shipping_fee' => $original_shipping_fee_pay,
                            'original_total_product_price' => $original_total_product_price_pay,
                            'platform_discount' => $platform_discount_pay,
                            'seller_discount' => $seller_discount_pay,
                            'shipping_fee' => $shipping_fee_pay,
                            'shipping_fee_platform_discount' => $shipping_fee_platform_discount_pay,
                            'shipping_fee_seller_discount' => $shipping_fee_seller_discount_pay,
                            'sub_total' => $sub_total_pay,
                            'total_amount' => $total_amount_pay

                        );

                        $this->tiktok_order_payment_model->insert($arr_pay_data);


                        $original_shipping_fee_pay = $this->explode_thb($rowData[0][17]);
                        $shipping_fee_pay = $this->explode_thb($rowData[0][16]);
                        $shipping_fee_platform_discount_pay = $this->explode_thb($rowData[0][19]);
                        $shipping_fee_seller_discount_pay = $this->explode_thb($rowData[0][18]);
                        $sub_total_pay = $this->explode_thb($rowData[0][15]);

                        $arr_pay_data = array(

                            'order_id' => $order_id,
                            'currency' => 'THB',
                            'original_shipping_fee' => $original_shipping_fee_pay,
                            'original_total_product_price' => $original_price,
                            'platform_discount' => $platform_discount,
                            'seller_discount' => $seller_discount,
                            'shipping_fee' => $shipping_fee_pay,
                            'shipping_fee_platform_discount' => $shipping_fee_platform_discount_pay,
                            'shipping_fee_seller_discount' => $shipping_fee_seller_discount_pay,
                            'sub_total' => $sub_total_pay,
                            'total_amount' => $sale_price

                        );

                        $this->tiktok_order_payment_model->insert($arr_pay_data);



                    }


                    $order_tmp = $order_id;

                }elseif($order_tmp == $order_id){
                    //echo "---dup---<br>";
                    //echo $order_tmp.">>>>>".$order_id."<br>";

                    $data_line = array(

                        'order_id' => $order_tmp,
                        'cancel_reason' => $cancel_reason,
                        'currency' => "THB",
                        'display_status' => $status,
                        'original_price' => $original_price,
                        'order_qty' => $order_qty,
                        'platform_discount' => $platform_discount,
                        'product_name' =>$product_name,
                        'rts_time' => $rts_time,
                        'sale_price' => $sale_price,
                        'seller_discount' => $seller_discount,
                        'seller_sku' => $seller_sku,
                        'shipping_provider_id' => $shipping_provider_id,
                        'shipping_provider_name' => $shipping_provider,
                        'sku_id' => $sku_id,
                        'sku_name' => 'Default',
                        'small_order_fee' => $small_order_fee,
                        'tracking_number' => $tracking_number,

                    );

                    //print_r($data_line);

                    $chk_data_item = $this->tiktok_line_items_model->get_by_order_id_sku($order_id,$seller_sku);

                    if(empty($chk_data_item)){
                        $this->tiktok_line_items_model->insert($data_line);
                    }

                    $order_id_tmp = $order_id;
                    $original_shipping_fee_pay = $this->explode_thb($rowData[0][17]);
                    $shipping_fee_pay = $this->explode_thb($rowData[0][16]);
                    $shipping_fee_platform_discount_pay = $this->explode_thb($rowData[0][19]);
                    $shipping_fee_seller_discount_pay = $this->explode_thb($rowData[0][18]);

                    $sub_total_pay = $sub_total_pay+$sub_total;
                    $original_total_product_price_pay = $original_total_product_price_pay+($original_price*$order_qty);
                    $platform_discount_pay = $platform_discount_pay+$platform_discount;
                    $seller_discount_pay = $seller_discount_pay+$seller_discount;
                    
                    $total_amount_pay = $sale_price;


                    if($num == $row_data){

                        $arr_pay_data = array(

                            'order_id' => $order_id_tmp,
                            'currency' => 'THB',
                            'original_shipping_fee' => $original_shipping_fee_pay,
                            'original_total_product_price' => $original_total_product_price_pay,
                            'platform_discount' => $platform_discount_pay,
                            'seller_discount' => $seller_discount_pay,
                            'shipping_fee' => $shipping_fee_pay,
                            'shipping_fee_platform_discount' => $shipping_fee_platform_discount_pay,
                            'shipping_fee_seller_discount' => $shipping_fee_seller_discount_pay,
                            'sub_total' => $sub_total_pay,
                            'total_amount' => $total_amount_pay

                        );

                        $this->tiktok_order_payment_model->insert($arr_pay_data);
                    }

                    $order_tmp = $order_id;
                }
                 
            }//for ($row = 3; $row <= $highestRow; $row++){ 
        }//if(in_array($_FILES['upload_file1']['type'],$mimes))
    }//if($is_upload1 == 1){

 }

    function create_textinvoiceid(){

       $array_status_death = array('COMPLETED','CANCELLED');

        $arr_sho_taxs = $this->tiktok_orders_model->select_by_status_last_arr($array_status_death,50);

        foreach($arr_sho_taxs as $arr_sho_tax){

            $arr_chk_or = $this->tiktok_taxinvoiceid_model->select_taxinvoiceid_by_orderno($arr_sho_tax['order_id']);
            if(empty($arr_chk_or)){
                $arr_lastorder = $this->tiktok_taxinvoiceid_model->last_order_code_by_yymm($arr_sho_tax['yyyymm']);   
                //print_r($arr_lastorder);

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->tiktok_bl->get_tiktok_code($arr_lastorder['taxinvoiceID'],$arr_sho_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_sho_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_sho_tax['create_time']
                 );

                 //print_r($arr_new_invoice_id);

                 $chk_taxid = $this->tiktok_taxinvoiceid_model->select_taxinvoiceid_id($new_textinvoiceID);

                 if(empty($chk_taxid)){
                     $this->tiktok_taxinvoiceid_model->insert($arr_new_invoice_id);
                 }else{
                    break;
                 }

               }else{

                $new_textinvoiceID = $this->tiktok_bl->get_tiktok_code('no',$arr_sho_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_sho_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_sho_tax['create_time']
                 );

                // print_r($arr_new_invoice_id);

                 $chk_taxid = $this->tiktok_taxinvoiceid_model->select_taxinvoiceid_id($new_textinvoiceID);

                if(empty($chk_taxid)){

                    $this->tiktok_taxinvoiceid_model->insert($arr_new_invoice_id);
                }else{
                    break;
                }

               }
            }
       }
    }

    function get_returns_search(){

        $id = '579688864124078208';

        $api_url = "/return_refund/202309/returns/search";

        //$make_url = $this->tiktok_bl->make_url('GET',$api_url,$param,$sign);


        $querys = array(
            'sort_order' => 'ASC',
            'sort_field' => 'create_time',
            'shop_cipher' => TIKTOK_SHOP_CIPHER,
            'page_size' => 20
        );

        $start_time = strtotime('2024-07-01 00:00:00');
        $stop_time = strtotime('2024-07-25 20:14:00');

        $bodys = array(
            'create_time_ge' => $start_time,
            'create_time_lt' => $stop_time
        );

        $make_url = $this->tiktok_bl->make_url('POST',$api_url,$querys,$bodys,NULL);

        $res = $this->tiktok_bl->CallApiToken('POST',$make_url,$bodys);



        print_r($res);

        if(!empty($res['data'])){
            echo "not empty";
        }else{
            echo "empty";
        }

    }


    function get_return_record(){

        $api_url = "/return_refund/202309/returns/4035453401880497007/records";

        
        $querys = array(
            'shop_cipher' => TIKTOK_SHOP_CIPHER
        );

        $make_url = $this->tiktok_bl->make_url('GET',$api_url,$querys,NULL,NULL);

        $res = $this->tiktok_bl->CallApiToken('GET',$make_url,NULL);


        print_r($res);

    }

    function get_tracking_update(){

        $id = '579688864124078208';

        $api_url = "/api/logistics/tracking";

        //$make_url = $this->tiktok_bl->make_url('GET',$api_url,$param,$sign);


        $querys = array(

            'shop_cipher' => TIKTOK_SHOP_CIPHER

        );

        $start_time = strtotime('2024-07-01 00:00:00');
        $stop_time = strtotime('2024-07-25 20:14:00');

        $bodys = array(
            'order_id' => '579688864124078208',
            'provider_id' => '6841743441349706241',
            'tracking_number' => '753482534582'
        );

        $make_url = $this->tiktok_bl->make_url('POST',$api_url,$querys,$bodys,NULL);

        $res = $this->tiktok_bl->CallApiToken('POST',$make_url,$bodys);



        print_r($res);

        if(!empty($res['data'])){
            echo "not empty";
        }else{
            echo "empty";
        }

    }
     

}