<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class tiktok_bl
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();
       // $this->CI->load->library('businesslogic/curl_bl'); 
       $this->CI->load->library('util/date_util');
       $this->CI->load->library("util/random_util");

       $this->CI->load->library("businesslogic/number_bl");
       $this->CI->load->library("businesslogic/upload_bl");

       $this->CI->load->model('tiktok_token_model');
       $this->CI->load->model('tiktok_orders_model');
       $this->CI->load->model('tiktok_data_model');
       $this->CI->load->model('tiktok_prep_model');
       $this->CI->load->model('tiktok_prep_api_model');

		
    }

    function get_tiktok_code($last_code,$cdate){
  
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($last_code == 'no'){
      $laz_newcode = "TTK".$cdate_code."00001";
    }else{
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "TTK".$laz_ymcode.$laz_nextcode;
    }
   return $laz_newcode;
  }
    
    function get_accesstoken($code)
    {
        $host="https://auth.tiktok-shops.com/api/v2/token/get";
    
        $url= $host."?app_key=".TIKTOK_KEY."&app_secret=".TIKTOK_SECRET."&auth_code=".$code."&grant_type=authorized_code";
    
        return $this->CallApi("GET",$url);
        //return htmlspecialchars($url);
    // return $this->shopee_curl_get($url);
    }

    function CallApi($method,$url_api,$param=null){
		
		$ch = curl_init();

		$headers = array(
	   	 'Authorization: application/json'
	    );


		curl_setopt($ch, CURLOPT_URL,$url_api);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


		// CURLOPT_SSL_VERIFYHOST=>false,
            //CURLOPT_SSL_VERIFYPEER=>false,
		if($method == "POST"){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		if ($server_output === false)
		{
		    print_r('Curl error: ' . curl_error($ch));
		}
		curl_close ($ch);
		print_r($server_output);

		return json_decode($server_output,true);
	   
	}

    function CallApiToken($method,$url_api,$bodys=null){

        $arr_token = $this->CI->tiktok_token_model->select_lasted_token();
		
		$ch = curl_init();

		$headers = array(
            'x-tts-access-token:'.$arr_token['access_token'],
	   	    'Content-Type: application/json'
	    );

		curl_setopt($ch, CURLOPT_URL,$url_api);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// CURLOPT_SSL_VERIFYHOST=>false,
            //CURLOPT_SSL_VERIFYPEER=>false,
		if($method == "POST"){

            $object = json_encode ($bodys);

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$object);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$server_output = curl_exec ($ch);
		if ($server_output === false)
		{
		    print_r('Curl error: ' . curl_error($ch));
		}
		curl_close ($ch);
		//print_r($server_output);
		return json_decode($server_output,true);
	   
	}

    function make_url($method,$api_url,$url_params = array(),$bodys = array(),$requet_type = "normal"){

        $timestamp = $this->CI->date_util->get_date_now_unix();

        $sign = $this->signature($api_url,$url_params,$method,$requet_type,$bodys,$timestamp);

        $url = TIKTOK_API_URL.$api_url;
        $url_params['app_key'] = TIKTOK_KEY;
        $url_params['timestamp'] = $timestamp;
        $url_params['sign'] = $sign;

        $num = 1;
        $p="?";
        foreach($url_params as $key => $value)
        {   
            if($num > 1){
                $p = "&";
            }
            $url .= $p.$key."=".$value;
            $num = $num + 1;
        }

        echo "<br>----URL----<br>";
        echo $url;
        echo "<br>--------<br><br>";

        return $url;

    }

    function signature($path, $arr_params = array(), $requet_method = 'GET', $requet_type = "normal", $bodys = array(),$timestamp)
    {

        //$timestamp = $this->CI->date_util->get_date_now_add_min('1');

        $arr_params['app_key'] = TIKTOK_KEY;
        $arr_params['timestamp'] = $timestamp;
        ksort($arr_params);

        $input = '';
        foreach($arr_params as $key => $value)
        {
            $input .= $key . $value;
        }

        //echo "1----------------<br>";
        //echo $input;

        //-----------POST METHOD----------
        if ($requet_method !='GET' && $requet_type != 'multipart/form-data') {
            $object = json_encode ($bodys);
            $input .= (string)$object;

        }
        //-----------END POST METHOD----------

        $input = $path . $input;

        //echo "<br>2----------------<br>";
        //echo $input;

        $input = TIKTOK_SECRET . $input . TIKTOK_SECRET;
        echo "-------Secert before encript---------<br>";
        echo $input;
        echo "<br>----------------<br>";

        $input_sha256 = bin2hex(hash_hmac('sha256', $input, TIKTOK_SECRET, true));

        echo "-------Secert encript---------<br>";
        echo $input_sha256;
        echo "<br>----------------<br>";

        return $input_sha256;
    }

    function make_url_bk($method,$api_url,$url_params = array(),$sign){

        $url = TIKTOK_API_URL.$api_url;

        if($method == "GET"){

            $timestamp = $this->CI->date_util->get_date_now_add_min('1');

            $url_params['app_key'] = TIKTOK_KEY;
            $url_params['timestamp'] = $timestamp;
            $url_params['sign'] = $sign;

            $num = 1;
            $p="?";
            foreach($url_params as $key => $value)
            {   
                if($num > 1){
                    $p = "&";
                }
                $url .= $p.$key."=".$value;
                $num = $num + 1;
            }
        }

        echo "<br>----URL----<br>";
        echo $url;
        echo "<br>--------<br><br>";

        return $url;

    }

    function tiktok_curl_get($url)
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

     function chk_prep($daterange,$file_upload){

        //echo $daterange."<br>";

        $arr_date = $this->CI->date_util->get_start_stop_from_date_range($daterange);

        //echo $arr_date['start']."<br>";
        //echo $arr_date['stop']."<br>";    

        $arr_data_return = $this->import_data_to_prep($file_upload,$arr_date['start'],$arr_date['stop']);

        /*echo "---re---";
        print_r($arr_data_return);
        echo "---re---";*/

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
            $order_tmp = "";

            for ($row = 3; $row <= $highestRow; $row++){ 
                $num =$num+1;

                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                    NULL,
                                                    TRUE,
                                                    FALSE);
                    //  Insert row data array into your database of choice here
                    //print_r($rowData);
                $ctime = $rowData[0][24];
                $order_id = $rowData[0][0];
                $order_status = $rowData[0][1];
                $cancel_type = $rowData[0][3];

                if($cancel_type == NULL){
                    $cancel_type = "";
                }
                $products = $rowData[0][6];
                $quantity = $rowData[0][9];

                $SubtotalAfterDiscount = $this->explode_thb($rowData[0][15]);
                $ShippingFeeAfterDiscount = $this->explode_thb($rowData[0][16]);
                $OriginalShippingFee = $this->explode_thb($rowData[0][17]);
                $ShippingFeePlatformDiscount = $this->explode_thb($rowData[0][19]);
                $SmallOrderFee = $this->explode_thb($rowData[0][21]);
                $OrderAmount = $this->explode_thb($rowData[0][22]);
                $OrderRefundAmount = $this->explode_thb($rowData[0][23]);
                $AmountExcludeVat = $OrderAmount/1.07;
                $Vat = $OrderAmount-$AmountExcludeVat;

                $date = str_replace('/', '-', $ctime);
                $date_to_db = date('Y/m/d H:i:s', strtotime($date));

                $PaidTime = $rowData[0][25];
                $RTSTime = $rowData[0][26];
                $ShippedTime = $rowData[0][27];

                $date26 = str_replace('/', '-', $PaidTime);
                $date_to_db26 = date('Y/m/d H:i:s', strtotime($date26));

                $date27 = str_replace('/', '-', $RTSTime);
                $date_to_db27 = date('Y/m/d H:i:s', strtotime($date27));

                $date28 = str_replace('/', '-', $ShippedTime);
                $date_to_db28 = date('Y/m/d H:i:s', strtotime($date28));

                $CancelledTime = $rowData[0][29];
                $date30 = str_replace('/', '-', $CancelledTime);
                $date_to_db30 = date('Y/m/d H:i:s', strtotime($date30));

                $CancelReason = $rowData[0][31];
                $TrackingID = $rowData[0][34];

                

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

                    $this->CI->tiktok_data_model->insert($data);

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

                    $this->CI->tiktok_data_model->insert($data);

                }
            
             

                 
            }//for ($row = 3; $row <= $highestRow; $row++){ 


        }//if(in_array($_FILES['upload_file1']['type'],$mimes))
            
    }//if($is_upload1 == 1){

    $arr_data = $this->get_data_sale_by_date($StartDate,$EndDate,$keygen);

    return $arr_data;

  }  

  function get_data_sale_by_date($StartDate,$EndDate,$keygen){

    //echo $StartDate;

    //$StartDate = '2024-12-01';
    //$EndDate = '2024-12-31';

    $arr_tiktoks =$this->CI->tiktok_orders_model->tiktok_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
  //print_r($arr_tiktoks);


    $price_sum = 0;
    foreach($arr_tiktoks as $arr_tiktok){

    /*$priceVATincluded = $arr_tiktok['price'] - $arr_tiktok['voucher'] +$arr_tiktok["shipping_fee"];
    $priceBeforeVAT = $priceVATincluded/1.07;
    $VAT=$priceVATincluded-$priceBeforeVAT;*/

    $price_sum = $price_sum + $arr_tiktok['price'];

      $chk_order_tik = $this->CI->tiktok_prep_api_model->select_by_order_id($arr_tiktok['order_id']);

      if(empty($chk_order_tik)){

          $data = array(
            'order_id' => $arr_tiktok['order_id'],
            'status' => $arr_tiktok['status'],
            'transactiondate' => $arr_tiktok['transactiondate'],
            'start_inv' => $arr_tiktok['start_inv'],
            'end_inv' => $arr_tiktok['end_inv'],
            'shipping_fee' => $arr_tiktok['shipping_fee'],
            'voucher_platform' => $arr_tiktok['voucher_platform'],
            'voucher_seller' => $arr_tiktok['voucher_seller'],
            'voucher' => $arr_tiktok['voucher'],
            'price' => $arr_tiktok['price'],
            'original_price' => $arr_tiktok['original_price'],
            'priceVATincluded' => $arr_tiktok['priceVATincluded'],
            'priceBeforeVAT' => $arr_tiktok['priceBeforeVAT'],
            'VAT' => $arr_tiktok['VAT'],
            'code' => $keygen
          );

          $this->CI->tiktok_prep_api_model->insert($data);
      }
      
    }

    $re_arr_order_chk = $this->prep_make($keygen);

    //------ Sum Price From EXcel --------
    $arr_data_complete = $this->CI->tiktok_prep_model->select_by_complete($keygen);
    $arr_data_cn = $this->CI->tiktok_prep_model->select_by_cancel($keygen);
    //$arr_data_retuen_cn = $this->CI->tiktok_prep_model->select_by_retuen($keygen);

    $total_cn = $arr_data_cn['sum_cn'];

    $total_sale = $arr_data_complete['sum_sale'] + $total_cn;
    //------ Sum Price From EXcel --------

    $array_prep_re=array(
        'total_price_api' => number_format($price_sum,2,".",","),
        'total_price_excel' => number_format($arr_data_complete['sum_sale'],2,".",","),
        'total_cn_excel' => number_format($total_cn,2,".",","),
        'total_price_cn_excel' => number_format($arr_data_complete['sum_sale'],2,".",","),
        //'total_price_cn_excel' => number_format($total_sale,2,".",","),
        'arr_order_check' => $re_arr_order_chk
    );

    /*echo "--------<br>";
    print_r($array_prep_re);
    echo "--------<br>";
    */


    return $array_prep_re;

  }

  function prep_make(){

    $arr_orderno_chk = array();
    $arr_datas = $this->CI->tiktok_prep_model->select_prep_join_by_orderno_code($keygen);
    //print_r($arr_datas);
    $num  = 1;
    foreach($arr_datas as $arr_data){

            $diffprice = $arr_data['paid_price']-$arr_data['price'];
          
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

  function explode_th_bk($data){

    $baht = 0;
    if(!empty($data)){
        $exp = explode(" ",$data);
        if(!empty($exp[1])){
            $t1 = str_replace(',','',$exp[1]);
            $baht = (float)$t1;
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

}