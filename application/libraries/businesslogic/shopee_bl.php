<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class shopee_bl
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();

        $this->CI->load->library("util/date_util");
        $this->CI->load->library("util/random_util");
        
        $this->CI->load->library("businesslogic/number_bl");
        $this->CI->load->library("businesslogic/upload_bl");
        
        $this->CI->load->model('shopee_orders_model');
        $this->CI->load->model('shopee_token_model');
        $this->CI->load->model('shopee_prep_model');
        $this->CI->load->model('shopee_prep_api_model');
		
    }


      function shopee_curl_post($url,$data)
      {
//echo $url."<br>";
//print_r($data);


   $ch = curl_init();
  
  
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
    array(
        'Content-Type:application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ));

  curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
  curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($data));
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
  $response = curl_exec($ch);
  
  curl_close ($ch);
  return  json_decode($response,true);

      }



     function shopee_curl_get($url)
     {

         $ch = curl_init();
  
  
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        
      // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

      // Return response instead of outputting
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

       // Execute the POST request
       $result = curl_exec($ch);

        // Close cURL resource
       curl_close($ch);

       return $result;

     }


    function get_code($parthner_key,$pathner_id)
    {
    $path="/api/v2/shop/auth_partner";
    $redirectURL="https://www.bnyfoodproducts.com/shopee/authenticated";
    $sign=$this->get_sign($parthner_key,$pathner_id,$path);


    }

   function get_authenticatrion_link()
   {

    $host=SHOPEE_APIURL;
    $path="/api/v2/shop/auth_partner";
    $redirectURL="https://www.bnyfoodproducts.com/shopee/authenticated";
    $timestamp=$this->get_timestamp();
    $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp;
    $sign=$this->get_sign($sting_to_sign,SHOPEE_PATNERKEY);

  return $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$this->get_timestamp()."&sign=".$sign."&redirect=".$redirectURL;

   }


   function get_accesstoken($code,$shop_id){
   $host=SHOPEE_APIURL;
   //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
   $path="/api/v2/auth/token/get";

   $timestamp=$this->get_timestamp();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp;
   $sign=$this->get_sign($sting_to_sign,SHOPEE_PATNERKEY);

   $url= $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$timestamp."&sign=".$sign;

   $data=array('code'=>$code,
               'shop_id'=>intval($shop_id),
                'partner_id'=>intval(SHOPEE_PATNERID)
                );
   return $this->shopee_curl_post($url,$data);
   //return htmlspecialchars($url);
  // return $this->shopee_curl_get($url);
   }

    function refresh_accesstoken(){ // called every 3 hours to keep the access token alive for all the time
    //we get valid tokenrecord from db
        $arr=$this->CI->shopee_token_model->getlatesttoken();
        print_r($arr); 
        //echo $arr['refreshtoken'];


      //if($arr->refreshtoken!='0') // there is a valid token record
      if($arr['refreshtoken']!="0") // there is a valid token record
      {

        if($arr['left_time'] < 5400){
        //echo "we are here";
         $host=SHOPEE_APIURL;
         //https://partner.test-stable.shopeemobile.com/api/v2/auth/access_token/get
         $path="/api/v2/auth/access_token/get";

         $timestamp=$this->get_timestamp();
         $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp;
         $sign=$this->get_sign($sting_to_sign,SHOPEE_PATNERKEY);

         $url= $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$timestamp."&sign=".$sign;

         $data=array('refresh_token'=>$arr['refreshtoken'],
                      'partner_id'=>intval(SHOPEE_PATNERID),
                      'shop_id'=>intval($arr['shopid'])
                      );
         //print_r($data);
         $return_data=array(
            'ShopeeLoginID'=>intval($arr['ShopeeLoginID']),
            'shopee_return'=>$this->shopee_curl_post($url,$data)
         );
         //print_r($return_data);
         return $return_data;
       }else{
        return 0;  
       }
      }
      else
      {
         return 0;   
      }
   //return htmlspecialchars($url);
  // return $this->shopee_curl_get($url);
   }

   function getaccesstoken()
    {
        //get access token
      $arr=$this->CI->shopee_token_model->getlatesttoken();

      //echo $arr['token'];
      return $arr['token'];
      unset($arr);

    }


    function getshopid()
    {
    //get access token
      $arr=$this->CI->shopee_token_model->getlatesttoken();
      return $arr['shopid'];
      unset($arr);

    }


    function get_sign($sting_to_sign,$key)

    {


return  hash_hmac('sha256', $sting_to_sign,$key);

    }

    


    function get_timestamp()
    {

        //$timestamp = $this->CI->date_util->get_date_now_unix();
      $timestamp = $this->CI->date_util->get_date_now_add_min('1');
        //return time();
        return $timestamp;
    }


function num_to_text($amount_number)
{
    $amount_number = number_format($amount_number, 2, ".","");
    $pt = strpos($amount_number , ".");
    $number = $fraction = "";
    if ($pt === false) 
        $number = $amount_number;
    else
    {
        $number = substr($amount_number, 0, $pt);
        $fraction = substr($amount_number, $pt + 1);
    }
    
    $ret = "";
    $baht = $this->ReadNumber($number);
    if ($baht != "")
        $ret .= $baht . "บาท";
    
    $satang = $this->ReadNumber($fraction);
    if ($satang != "")
        $ret .=  $satang . "สตางค์";
    else 
        $ret .= "ถ้วน";
    return $ret;
}

function ReadNumber($number)
{
    $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
    $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $number = $number + 0;
    $ret = "";
    if ($number == 0) return $ret;
    if ($number > 1000000)
    {
        $ret .= $this->ReadNumber(intval($number / 1000000)) . "ล้าน";
        $number = intval(fmod($number, 1000000));
    }
    
    $divider = 100000;
    $pos = 0;
    while($number > 0)
    {
        $d = intval($number / $divider);
        $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
            ((($divider == 10) && ($d == 1)) ? "" :
            ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
        $ret .= ($d ? $position_call[$pos] : "");
        $number = $number % $divider;
        $divider = $divider / 10;
        $pos++;
    }
    return $ret;
}

function add_font_digi($hc_code,$digi){
        $insertplus =  trim($hc_code);
    
        if(strlen($insertplus )<$digi)
        {   
    
            $m = 0;
            $lentxt = $digi-(strlen($insertplus));
            $hc_code = '';
                for ($m=1;$m<=$lentxt;$m++) 
                {
                    $hc_code = $hc_code ."0";
                }
            $code = $hc_code.$insertplus;       
        }else{
            $code = $insertplus;
        }
    
        return $code;
    }
// shipping providers
    function getShipingProviders()
    {
       $token=$this->getaccesstoken();
       $shopid=$this->getshopid();
       $host=SHOPEE_APIURL;
       $path="/api/v2/logistics/get_channel_list";

       $timestamp=$this->get_timestamp();
       $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$token.$shopid;
       $sign=$this->get_sign($sting_to_sign,SHOPEE_PATNERKEY);

       $url= $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$timestamp."&access_token=".$token."&shop_id=". $shopid."&sign=".$sign;

  // $data=array('code'=>$code,
  //             'shop_id'=>intval($shop_id),
  //              'partner_id'=>intval(SHOPEE_PATNERID)
  //              );
   return $this->shopee_curl_get($url);


    }


    function setShipingProvider($logistics_channel_id) //7032
    {

       $token=$this->getaccesstoken();
       $shopid=$this->getshopid();
       $host=SHOPEE_APIURL;
   
       $path="/api/v2/logistics/update_channel";

   $timestamp=$this->get_timestamp();
   $sting_to_sign=SHOPEE_PATNERID.$path.$timestamp.$token.$shopid;
   $sign=$this->get_sign($sting_to_sign,SHOPEE_PATNERKEY);

   $url= $host.$path."?partner_id=".SHOPEE_PATNERID."&timestamp=".$timestamp."&access_token=".$token."&shop_id=".$shopid."&sign=".$sign;

   $data=array('logistics_channel_id'=>intval($logistics_channel_id),
               'enabled'=>true,
                'preferred'=>false,
                'cod_enabled'=>true
                );
   return $this->shopee_curl_post($url,$data);


    }

function get_shopee_code($last_code,$cdate){
    
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($last_code == 'no'){
      $laz_newcode = "Shp".$cdate_code."00001";
    }else{
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "Shp".$laz_ymcode.$laz_nextcode;
    }
     return $laz_newcode;
  }

  function get_shopee_code_v1($last_code,$cdate){
    
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($laz_ymcode == $cdate_code){
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "Shp".$laz_ymcode.$laz_nextcode;
    }else{

      $laz_newcode = "Shp".$cdate_code."00001";
    }

     return $laz_newcode;
  }

  function get_shp_fulltax_code($last_code,$cdate){
    
    //202103
    $shp_ymcode = substr($last_code, 6,6);
    $shp_code = substr($last_code, 12,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($shp_ymcode == $cdate_code){
      $shp_nextcode = $shp_code+1;
      $shp_nextcode = $this->CI->number_bl->add_font_digi($shp_nextcode,5);
      $shp_newcode = "BNYSHP".$shp_ymcode.$shp_nextcode;
    }else{

      $shp_newcode = "BNYSHP".$cdate_code."00001";
    }

     return $shp_newcode;
  }

  function chk_prep($daterange,$file_upload){

    //echo $daterange."<br>";

    $arr_date = $this->CI->date_util->get_start_stop_from_date_range($daterange);

    //echo $arr_date['start']."<br>";
    //echo $arr_date['stop']."<br>";    

    $arr_data_return = $this->import_data_to_prep($file_upload,$arr_date['start'],$arr_date['stop']);

    return $arr_data_return;

  } 

  function import_data_to_prep($file_upload,$StartDate,$EndDate){

    $file1_name = "";
    $this->CI->load->library('Upload_secure', [
          'psp_inbox_dir'  => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
        ]);

        $res = $this->CI->upload_secure->upload_file('upload_file1');

        if ($res['is_upload'] === 1) {
            $file_s = APP_STORE_PATH."/uploads/xls/".$res['file_name'];
        $mimes = array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if(in_array($_FILES['upload_file1']['type'],$mimes))
        {
            $this->CI->load->library('Lib_excel');

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
            $keygen = $this->CI->random_util->create_random_number(8);
            $totol_price = 0;
            $totol_ship = 0;
            $totol_cn = 0;
            $totol_logistic_cn = 0;
            $osn_tmp = "";

            $keygen = $this->CI->random_util->create_random_number(8);

            for ($row = 2; $row <= $highestRow; $row++){ 

                $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                //  Insert row data array into your database of choice here
                //print_r($rowData);
                $order_sn = $rowData[0][0];


              // $chk_sho_data = $this->CI->shopee_prep_model->select_by_order_sn($order_sn);
            //if(empty($chk_sho_data)){    
            
            $status = $rowData[0][1];//B
            $reason_cancel = $rowData[0][3];//D
            $order_date = $rowData[0][6];//G
            $paidPrice = $rowData[0][45];//AT

            $price_cn = $rowData[0][25];//Z
            $logistic_price = $rowData[0][46];//AU

            $arr_exp_reason = explode(" ", $reason_cancel);
            $cnt_reason = count($arr_exp_reason);
            $cancel_reason = $arr_exp_reason[$cnt_reason-1];


            //echo $order_sn.">>>>>".$highestRow.">>".$num."-->>".$order_sn_tmp."<br>";

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
                  $this->CI->shopee_prep_model->insert($arr_data);
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

              //echo "Last--->".$num."-->>".$order_sn."-*-->>>".$row_data;

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
                $this->CI->shopee_prep_model->insert($arr_data);

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
                  $this->CI->shopee_prep_model->insert($arr_data);
              }
            }

            //********************************
            //}//if(empty($chk_sho_data)){  
                
             }      
            }
        }

       // $this->get_data_sho_from_excel($keygen);

       $arr_data = $this->get_data_sale_by_date($StartDate,$EndDate,$keygen);

        //echo "----arr_data----<br>";
        //print_r($arr_data);
        //echo "--------<br>";

       return $arr_data;

  }

  function get_data_sho_from_excel($keygen){

    $arr_data_complete = $this->CI->shopee_prep_model->select_by_complete($keygen);
    $arr_data_cn = $this->CI->shopee_prep_model->select_by_cancel($keygen);
    $arr_data_retuen_cn = $this->CI->shopee_prep_model->select_by_retuen($keygen);

    $total_cn = $arr_data_cn['sum_cn']+$arr_data_cn['sum_logis_cn']+$arr_data_retuen_cn['sum_cn_return'];

    $total_sale = $arr_data_complete['sum_sale'] + $total_cn;
    //echo "<br>Total Price = ".$total_sale."<br>";
    //echo "Total CN = ".$total_cn."<br>";

    //echo "Total = ".$arr_data_complete['sum_sale']."<br>";

 }

 function get_data_sale_by_date($StartDate,$EndDate,$keygen){

    //$StartDate = '2024-12-01';
    //$EndDate = '2024-12-31';

    $arr_shopees =$this->CI->shopee_orders_model->shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
  //print_r($arr_shopees);


    $priceVATincluded = 0;
    foreach($arr_shopees as $arr_shopee){

    $priceVATincluded = $priceVATincluded + $arr_shopee['priceVATincluded'];

      $chk_order_sho = $this->CI->shopee_prep_api_model->select_by_order_sn($arr_shopee['order_sn']);

      if(empty($chk_order_sho)){

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

          $this->CI->shopee_prep_api_model->insert($data);
      }
      
    }

    $re_arr_order_chk = $this->prep_make($keygen);

    $arr_shopee_cns = $this->CI->shopee_orders_model->shopee_select_order_groupby_Date_by_DateStart_DateEnd_CN($StartDate,$EndDate);
    $data_cn_DB = 0;

    if(!empty($arr_shopee_cns)){
        foreach($arr_shopee_cns as $arr_shopee_cn){
            $data_cn_DB = $data_cn_DB+$arr_shopee_cn['ValueBeforeVAT'];
        }
    }

    $priceVATincluded_with_cn = $priceVATincluded;
    $priceVATincluded_no_cn = $priceVATincluded-$data_cn_DB;

    //------ Sum Price From EXcel --------
    $arr_data_complete = $this->CI->shopee_prep_model->select_by_complete($keygen);
    $arr_data_cn = $this->CI->shopee_prep_model->select_by_cancel($keygen);
    $arr_data_retuen_cn = $this->CI->shopee_prep_model->select_by_retuen($keygen);

    $total_cn = $arr_data_cn['sum_cn']+$arr_data_cn['sum_logis_cn']+$arr_data_retuen_cn['sum_cn_return'];

    $total_sale = $arr_data_complete['sum_sale'] + $total_cn;
    //------ Sum Price From EXcel --------

    $array_prep_re=array(
        'total_price_api' => $priceVATincluded_with_cn,
        'total_price_excel' => $arr_data_complete['sum_sale'],
        'total_cn_excel' => $total_cn,
        'total_price_cn_excel' => $total_sale,
        'arr_order_check' => $re_arr_order_chk
    );

    //echo "--------<br>";
    //print_r($array_prep_re);
    //echo "--------<br>";

    return $array_prep_re;

  }

  function prep_make(){

    $arr_orderno_chk = array();
    $arr_datas = $this->CI->shopee_prep_model->select_prep_join_by_orderno_code($keygen);
    //print_r($arr_datas);
    $num  = 1;
    foreach($arr_datas as $arr_data){

            $diffprice = $arr_data['paid_price']-$arr_data['priceVATincluded'];
          
          //echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$arr_data['priceVATincluded'].">>diff>>".$diffprice."<br>";

          if($diffprice != 0){

            array_push($arr_orderno_chk,$arr_data['order_sn_s']);

            //echo "-------- CHECK -----------<br>";
            //echo $arr_data['order_sn_s']."<br>";
            //echo "-------- CHECK -----------<br>";
          }

          
        $num = $num+1;
      }
      return $arr_orderno_chk;
    }

    
}