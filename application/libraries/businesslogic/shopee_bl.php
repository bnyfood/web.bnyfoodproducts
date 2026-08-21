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

  function passed_pack_api_statuses(){
    return array(
      'READY_TO_SHIP',
      'RETRY_SHIP',
      'SHIPPED',
      'TO_CONFIRM_RECEIVE',
      'COMPLETED',
      'TO_RETURN',
      'RETURNED'
    );
  }

  function normalize_api_status($status){
    return strtoupper(trim((string)$status));
  }

  function status_list_passed_pack($statuses){
    if (empty($statuses)) {
      return false;
    }
    $passed = $this->passed_pack_api_statuses();
    foreach ($statuses as $s) {
      $s = $this->normalize_api_status($s);
      if (in_array($s, $passed, true)) {
        return true;
      }
    }
    return false;
  }

  function should_insert_virtual_processed($api_statuses, $db_statuses, $last_status, $order_sn = ''){
    $last = $this->normalize_api_status($last_status);
    if ($last === 'PROCESSED') {
      return false;
    }
    $db_norm = array();
    if (!empty($db_statuses)) {
      foreach ($db_statuses as $s) {
        $db_norm[] = $this->normalize_api_status($s);
      }
    }
    if (in_array('PROCESSED', $db_norm, true)) {
      return false;
    }
    if ($this->status_list_passed_pack($api_statuses) || $this->status_list_passed_pack($db_norm)) {
      return true;
    }
    if ($order_sn !== '') {
      $tracked = $this->CI->shopee_orders_model->get_tracking_order_sn_set(array($order_sn));
      if (isset($tracked[$order_sn])) {
        return true;
      }
    }
    return false;
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

	function ensure_access_token($force = false)
	{
		$arr = $this->CI->shopee_token_model->getlatesttoken();
		if (empty($arr) || empty($arr['refreshtoken']) || $arr['refreshtoken'] === '0') {
			return false;
		}
		$left = isset($arr['left_time']) ? (int)$arr['left_time'] : 0;
		if (!$force && $left > 1800) {
			return true;
		}
		$host = SHOPEE_APIURL;
		$path = '/api/v2/auth/access_token/get';
		$timestamp = $this->get_timestamp();
		$sting_to_sign = SHOPEE_PATNERID.$path.$timestamp;
		$sign = $this->get_sign($sting_to_sign, SHOPEE_PATNERKEY);
		$url = $host.$path.'?partner_id='.SHOPEE_PATNERID.'&timestamp='.$timestamp.'&sign='.$sign;
		$data = array(
			'refresh_token' => $arr['refreshtoken'],
			'partner_id' => intval(SHOPEE_PATNERID),
			'shop_id' => intval($arr['shopid'])
		);
		$res = $this->shopee_curl_post($url, $data);
		if (!is_array($res) || empty($res['access_token'])) {
			log_message('error', 'shopee token refresh failed');
			return false;
		}
		date_default_timezone_set('Asia/Bangkok');
		$now = date('Y-m-d H:i:s');
		$upd = array(
			'token' => $res['access_token'],
			'code_generateddatetime' => $now,
			'token_generateddatetime' => $now
		);
		if (!empty($res['refresh_token'])) {
			$upd['refreshtoken'] = $res['refresh_token'];
		}
		if (!empty($res['expire_in'])) {
			$upd['refresh_expires_in'] = $res['expire_in'];
		}
		$this->CI->shopee_token_model->update_token_quiet($arr['ShopeeLoginID'], $upd);
		return true;
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

  private function _prep_to_float($val)
  {
    if ($val === null || $val === '') {
      return 0.0;
    }
    if (is_numeric($val)) {
      return floatval($val);
    }
    $s = str_replace(',', '', trim((string)$val));
    if ($s === '' || $s === '-') {
      return 0.0;
    }
    return floatval($s);
  }

  private function _excel_header_index($header_row, $needles, $fallback)
  {
    if (!empty($header_row)) {
      foreach ($header_row as $idx => $name) {
        $n = trim((string)$name);
        foreach ($needles as $needle) {
          if ($n === $needle) {
            return $idx;
          }
        }
      }
      foreach ($header_row as $idx => $name) {
        $n = trim((string)$name);
        foreach ($needles as $needle) {
          if ($needle !== '' && strpos($n, $needle) !== false) {
            return $idx;
          }
        }
      }
    }
    return $fallback;
  }

  private function _excel_cell($line, $idx)
  {
    if ($idx === null || $idx === false) {
      return '';
    }
    return isset($line[$idx]) ? $line[$idx] : '';
  }

  private function _shopee_excel_col_map($header_row)
  {
    return array(
      'order_sn' => $this->_excel_header_index($header_row, array('หมายเลขคำสั่งซื้อ'), 0),
      'status' => $this->_excel_header_index($header_row, array('สถานะการสั่งซื้อ'), 1),
      'cancel' => $this->_excel_header_index($header_row, array('เหตุผลในการยกเลิกคำสั่งซื้อ'), 3),
      'refund' => $this->_excel_header_index($header_row, array('สถานะการคืนเงินหรือคืนสินค้า'), 4),
      'order_date' => $this->_excel_header_index($header_row, array('วันที่ทำการสั่งซื้อ'), 6),
      'net' => $this->_excel_header_index($header_row, array('ราคาขายสุทธิ'), 25),
      'shopee_disc' => $this->_excel_header_index($header_row, array('ส่วนลดจาก Shopee'), 26),
      'seller_code' => $this->_excel_header_index($header_row, array('โค้ดส่วนลดชำระโดยผู้ขาย'), 27),
      'shopee_code' => $this->_excel_header_index($header_row, array('โค้ดส่วนลดชำระโดย Shopee'), 29),
      'seller_bundle' => $this->_excel_header_index($header_row, array('ส่วนลด bundle deal ชำระโดยผู้ขาย'), 32),
      'shopee_bundle' => $this->_excel_header_index($header_row, array('ส่วนลด bundle deal ชำระโดย Shopee'), 33),
      'buyer_prod' => $this->_excel_header_index($header_row, array('ราคาสินค้าที่ชำระโดยผู้ซื้อ'), 40),
      'buyer_ship' => $this->_excel_header_index($header_row, array('ค่าจัดส่งที่ชำระโดยผู้ซื้อ'), 41),
      'buyer_total' => $this->_excel_header_index($header_row, array('จำนวนเงินทั้งหมด'), 45)
    );
  }

  private function _excel_in_date_range($order_date, $StartDate, $EndDate)
  {
    $raw = trim((string)$order_date);
    if ($raw === '') {
      return true;
    }
    $ts = strtotime(str_replace('/', '-', $raw));
    if ($ts === false) {
      return true;
    }
    $start_ts = strtotime(str_replace('/', '-', $StartDate).' 00:00:00');
    $end_ts = strtotime(str_replace('/', '-', $EndDate).' 23:59:59');
    if ($start_ts === false || $end_ts === false) {
      return true;
    }
    return ($ts >= $start_ts && $ts <= $end_ts);
  }

  private function _excel_date_text($v)
  {
    if ($v instanceof DateTime) {
      return $v->format('Y-m-d H:i:s');
    }
    return is_scalar($v) ? trim((string)$v) : '';
  }

  // Excel tax = sum(ราคาขายสุทธิ) + ค่าส่งผู้ซื้อ.
  // On cancel/return Excel often zeros AD/AT while AO stays reduced; net+ship stays stable and matches escrow.
  private function _order_taxable($net_sum, $buyer_ship)
  {
    return $this->_prep_to_float($net_sum) + $this->_prep_to_float($buyer_ship);
  }

  function classify_excel_order($status, $cancel_reason, $refund_status, $has_after_pack = false)
  {
    $st = trim((string)$status);
    $reason = (string)$cancel_reason;
    $refund = trim((string)$refund_status);

    if ($refund !== '' || strpos($reason, 'คำขอได้รับการยอมรับ') !== false) {
      return 'cn';
    }
    if (strpos($reason, 'การจัดส่งไม่สำเร็จ') !== false) {
      return 'cn';
    }
    if (strpos($reason, 'ไม่มีการชำระเงิน') !== false) {
      return 'ignore';
    }
    if ($st === 'ยกเลิกแล้ว') {
      if ($has_after_pack) {
        return 'cn';
      }
      return 'ignore';
    }
    if ($st === 'สำเร็จแล้ว' || $st === 'การจัดส่ง') {
      return 'tax';
    }
    return 'ignore';
  }

  // Escrow check_taxable / original = goods only (ราคาขายสุทธิ).
  // prep_api.api_taxable / priceVATincluded stores full Check amount (goods + Excel ship).
  function api_taxable($row)
  {
    if (isset($row['api_taxable']) && $row['api_taxable'] !== null && $row['api_taxable'] !== '') {
      return $this->_prep_to_float($row['api_taxable']);
    }
    if (isset($row['priceVATincluded']) && $row['priceVATincluded'] !== null && $row['priceVATincluded'] !== ''
        && !isset($row['order_sn_s'])) {
      return $this->_prep_to_float($row['priceVATincluded']);
    }
    if (isset($row['check_taxable']) && $row['check_taxable'] !== null && $row['check_taxable'] !== '') {
      return $this->_prep_to_float($row['check_taxable']);
    }
    if (isset($row['original_cost_of_goods_sold'])) {
      return $this->_prep_to_float($row['original_cost_of_goods_sold']);
    }
    $platform = 0.0;
    if (isset($row['shopee_discount'])) {
      $platform = $platform + $this->_prep_to_float($row['shopee_discount']);
    }
    if (isset($row['voucher_from_shopee'])) {
      $platform = $platform + $this->_prep_to_float($row['voucher_from_shopee']);
    } elseif (isset($row['voucher_platform'])) {
      $platform = $platform + $this->_prep_to_float($row['voucher_platform']);
    }
    if (isset($row['buyer_total_amount']) && $row['buyer_total_amount'] !== null && $row['buyer_total_amount'] !== '') {
      return $this->_prep_to_float($row['buyer_total_amount']) + $platform;
    }
    $price = isset($row['price']) ? $this->_prep_to_float($row['price']) : 0.0;
    return $price;
  }

  function import_data_to_prep($file_upload, $StartDate, $EndDate)
  {
    $this->CI->load->library('Upload_secure', array(
      'psp_inbox_dir' => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
    ));

    $res = $this->CI->upload_secure->upload_file('upload_file1');

    if ($res['is_upload'] !== 1) {
      return array();
    }

    $file_s = APP_STORE_PATH . '/uploads/xls/' . $res['file_name'];
    $mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    if (!in_array($_FILES['upload_file1']['type'], $mimes)) {
      return array();
    }

    $this->CI->load->library('Lib_excel');

    try {
      $inputFileType = PHPExcel_IOFactory::identify($file_s);
      $objReader = PHPExcel_IOFactory::createReader($inputFileType);
      if (method_exists($objReader, 'setReadDataOnly')) {
        $objReader->setReadDataOnly(true);
      }
      $objPHPExcel = $objReader->load($file_s);
    } catch (Exception $e) {
      die('Error loading file "' . pathinfo($file_s, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    }

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    $keygen = $this->CI->random_util->create_random_number(8);

    $headerRow = $sheet->rangeToArray('A1:'.$highestColumn.'1', NULL, TRUE, FALSE);
    $colmap = $this->_shopee_excel_col_map(isset($headerRow[0]) ? $headerRow[0] : array());

    $orders = array();
    for ($row = 2; $row <= $highestRow; $row++) {
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      if (empty($rowData[0])) {
        continue;
      }
      $line = $rowData[0];
      $order_sn = trim((string)$this->_excel_cell($line, $colmap['order_sn']));
      if ($order_sn === '') {
        continue;
      }
      $order_date = $this->_excel_date_text($this->_excel_cell($line, $colmap['order_date']));
      if (!$this->_excel_in_date_range($order_date, $StartDate, $EndDate)) {
        continue;
      }

      $net = $this->_prep_to_float($this->_excel_cell($line, $colmap['net']));
      $platform_line = $this->_prep_to_float($this->_excel_cell($line, $colmap['shopee_disc']))
        + $this->_prep_to_float($this->_excel_cell($line, $colmap['shopee_code']))
        + $this->_prep_to_float($this->_excel_cell($line, $colmap['shopee_bundle']));
      $seller_line = $this->_prep_to_float($this->_excel_cell($line, $colmap['seller_code']))
        + $this->_prep_to_float($this->_excel_cell($line, $colmap['seller_bundle']));

      if (!isset($orders[$order_sn])) {
        $orders[$order_sn] = array(
          'order_sn' => $order_sn,
          'order_date' => $order_date,
          'status' => trim((string)$this->_excel_cell($line, $colmap['status'])),
          'cancel_reason' => trim((string)$this->_excel_cell($line, $colmap['cancel'])),
          'refund_status' => trim((string)$this->_excel_cell($line, $colmap['refund'])),
          'buyer_prod' => $this->_prep_to_float($this->_excel_cell($line, $colmap['buyer_prod'])),
          'buyer_ship' => $this->_prep_to_float($this->_excel_cell($line, $colmap['buyer_ship'])),
          'buyer_total' => $this->_prep_to_float($this->_excel_cell($line, $colmap['buyer_total'])),
          'platform_disc' => $platform_line,
          'seller_discount' => $seller_line,
          'original_price' => $net,
          'code' => $keygen
        );
      } else {
        $orders[$order_sn]['original_price'] = $orders[$order_sn]['original_price'] + $net;
        $orders[$order_sn]['status'] = trim((string)$this->_excel_cell($line, $colmap['status']));
        $cancel = trim((string)$this->_excel_cell($line, $colmap['cancel']));
        if ($cancel !== '') {
          $orders[$order_sn]['cancel_reason'] = $cancel;
        }
        $refund = trim((string)$this->_excel_cell($line, $colmap['refund']));
        if ($refund !== '') {
          $orders[$order_sn]['refund_status'] = $refund;
        }
      }
    }

    $order_sns = array_keys($orders);
    $after_pack_set = $this->CI->shopee_orders_model->get_passed_pack_order_sn_set($order_sns);

    foreach ($orders as $order_sn => $data) {
      // ราคาขายสุทธิ (sum SKU lines) + ค่าส่งผู้ซื้อ (once). Not AO+AD (AD cleared on many CN rows).
      $taxable = $this->_order_taxable($data['original_price'], $data['buyer_ship']);
      $bucket = $this->classify_excel_order(
        $data['status'],
        $data['cancel_reason'],
        $data['refund_status'],
        isset($after_pack_set[$order_sn])
      );
      $cancel_store = $data['cancel_reason'];
      if ($data['refund_status'] !== '') {
        $cancel_store = trim($cancel_store.' '.$data['refund_status']);
      }
      $this->CI->shopee_prep_model->insert(array(
        'order_sn' => $order_sn,
        'order_date' => $data['order_date'],
        'status' => $data['status'],
        'cancel_reason' => $cancel_store,
        'original_price' => $data['original_price'],
        'buyer_prod' => $data['buyer_prod'],
        'platform_disc' => $data['platform_disc'],
        'seller_discount' => $data['seller_discount'],
        'shipping_fee' => $data['buyer_ship'],
        'taxable' => $taxable,
        'bucket' => $bucket,
        'paid_price' => $taxable,
        'cn_paid_price' => ($bucket === 'cn') ? $taxable : 0,
        'logistic_price' => 0,
        'code' => $keygen
      ));
    }

    return $this->get_data_sale_by_date($StartDate, $EndDate, $keygen);
  }

  function import_data_to_prep_bk($file_upload,$StartDate,$EndDate){

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
            
            $status = $rowData[0][2];//D
            $reason_cancel = $rowData[0][4];//E
            $order_date = $rowData[0][7];//H

            $original_price = $rowData[0][22];//W
            $paidPrice = $rowData[0][46];//AU

            $price_cn = $rowData[0][26];//AA
            $sell_dis = $rowData[0][33]; //AH
            $shipping = $rowData[0][46]; // AQ
            $logistic_price = $rowData[0][47];//AV

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
              $original_price_tmp =  $original_price;
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
                    //'original_price' => $original_price_tmp,
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
                $original_price_tmp =  $original_price;
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
                    //'original_price' => $original_price_tmp,
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
                        //'original_price' => $original_price_tmp,
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

    $arr_excel = $this->CI->shopee_prep_model->select_by_code($keygen);
    $excel_tax = 0;
    $excel_cn = 0;
    $excel_ignore = 0;
    $buckets = array();
    $ignore_orders = array();
    $order_sns = array();
    $excel_ship = array();
    $excel_amt_map = array();
    if (!empty($arr_excel)) {
      foreach ($arr_excel as $row) {
        $order_sns[] = $row['order_sn'];
      }
    }
    $after_pack_set = $this->CI->shopee_orders_model->get_passed_pack_order_sn_set($order_sns);
    if (!empty($arr_excel)) {
      foreach ($arr_excel as $row) {
        $bucket = $this->classify_excel_order(
          $row['status'],
          $row['cancel_reason'],
          '',
          isset($after_pack_set[$row['order_sn']])
        );
        $amt = (isset($row['taxable']) && $row['taxable'] !== null && $row['taxable'] !== '')
          ? $this->_prep_to_float($row['taxable'])
          : $this->_prep_to_float($row['paid_price']);
        $ship = isset($row['shipping_fee']) ? $this->_prep_to_float($row['shipping_fee']) : 0.0;
        $excel_ship[$row['order_sn']] = $ship;
        $excel_amt_map[$row['order_sn']] = $amt;
        $buckets[$row['order_sn']] = $bucket;
        if (isset($row['shopee_prep_id']) && (!isset($row['bucket']) || $row['bucket'] !== $bucket)) {
          $this->CI->shopee_prep_model->update(array('bucket' => $bucket), $row['shopee_prep_id']);
        }
        if ($bucket === 'ignore') {
          $excel_ignore = $excel_ignore + $amt;
          $ignore_orders[$row['order_sn']] = true;
        } elseif ($bucket === 'cn') {
          $excel_tax = $excel_tax + $amt;
          $excel_cn = $excel_cn + $amt;
        } else {
          $excel_tax = $excel_tax + $amt;
        }
      }
    }

    $sum_tax_api = 0;
    $excel_tax_sns = array();
    foreach ($buckets as $order_sn => $bucket) {
      if ($bucket !== 'ignore') {
        $excel_tax_sns[] = $order_sn;
      }
    }
    $escrow_map = $this->CI->shopee_orders_model->get_escrow_tax_map_by_order_sns($excel_tax_sns);

    // Still load report SP rows for invoice fields / prep_api, but Check totals follow Excel order set + escrow.
    $sp_map = array();
    $arr_shopees = $this->CI->shopee_orders_model->shopee_select_order_with_DateStart_DateEnd($StartDate,$EndDate);
    if (!empty($arr_shopees)) {
      foreach ($arr_shopees as $arr_shopee) {
        $sp_map[$arr_shopee['order_sn']] = $arr_shopee;
      }
    }

    foreach ($excel_tax_sns as $order_sn) {
      $api_amt = 0.0;
      $row_for_prep = null;
      $ship_excel = isset($excel_ship[$order_sn]) ? $excel_ship[$order_sn] : 0.0;
      if (isset($escrow_map[$order_sn])) {
        // Goods from escrow original; shipping from Excel AP only
        // (escrow buyer_paid_shipping_fee often = estimated ship on cancel/CN).
        $esc = $escrow_map[$order_sn];
        $goods = isset($esc['original_cost_of_goods_sold'])
          ? $this->_prep_to_float($esc['original_cost_of_goods_sold'])
          : $this->api_taxable($esc);
        $api_amt = $goods + $ship_excel;
        $row_for_prep = array(
          'order_sn' => $order_sn,
          'transactiondate' => isset($sp_map[$order_sn]['transactiondate']) ? $sp_map[$order_sn]['transactiondate'] : null,
          'start_inv' => isset($sp_map[$order_sn]['start_inv']) ? $sp_map[$order_sn]['start_inv'] : null,
          'end_inv' => isset($sp_map[$order_sn]['end_inv']) ? $sp_map[$order_sn]['end_inv'] : null,
          'shipping_fee' => $ship_excel,
          'voucher_platform' => (isset($esc['voucher_from_shopee']) ? $esc['voucher_from_shopee'] : 0)
            + (isset($esc['coins']) ? $esc['coins'] : 0),
          'voucher_seller' => isset($esc['voucher_from_seller']) ? $esc['voucher_from_seller'] : 0,
          'seller_discount' => isset($esc['seller_discount']) ? $esc['seller_discount'] : 0,
          'voucher' => 0,
          'price' => isset($esc['original_cost_of_goods_sold']) ? $esc['original_cost_of_goods_sold'] : 0,
          'priceVATincluded' => $api_amt,
          'priceBeforeVAT' => round($api_amt / 1.07, 2),
          'VAT' => round($api_amt - ($api_amt / 1.07), 2),
          'taxable' => $api_amt,
          'code' => $keygen
        );
      } elseif (isset($sp_map[$order_sn])) {
        $arr_shopee = $sp_map[$order_sn];
        $goods = isset($arr_shopee['price'])
          ? $this->_prep_to_float($arr_shopee['price'])
          : $this->api_taxable($arr_shopee);
        $api_amt = $goods + $ship_excel;
        $row_for_prep = array(
          'order_sn' => $order_sn,
          'transactiondate' => $arr_shopee['transactiondate'],
          'start_inv' => $arr_shopee['start_inv'],
          'end_inv' => $arr_shopee['end_inv'],
          'shipping_fee' => $ship_excel,
          'voucher_platform' => $arr_shopee['voucher_platform'],
          'voucher_seller' => $arr_shopee['voucher_seller'],
          'seller_discount' => isset($arr_shopee['seller_discount']) ? $arr_shopee['seller_discount'] : 0,
          'voucher' => $arr_shopee['voucher'],
          'price' => $arr_shopee['price'],
          'priceVATincluded' => $api_amt,
          'priceBeforeVAT' => isset($arr_shopee['priceBeforeVAT']) ? $arr_shopee['priceBeforeVAT'] : round($api_amt / 1.07, 2),
          'VAT' => isset($arr_shopee['VAT']) ? $arr_shopee['VAT'] : round($api_amt - ($api_amt / 1.07), 2),
          'taxable' => $api_amt,
          'code' => $keygen
        );
      } elseif (isset($excel_amt_map[$order_sn])) {
        // No escrow / report SP row: still count Excel tax so Check can unlock.
        // These are usually orders never pulled into shopee_orders or escrow skipped.
        // Note stays visible only if something else still mismatches; here API = Excel.
        $api_amt = $excel_amt_map[$order_sn];
        $row_for_prep = array(
          'order_sn' => $order_sn,
          'transactiondate' => null,
          'start_inv' => null,
          'end_inv' => null,
          'shipping_fee' => $ship_excel,
          'voucher_platform' => 0,
          'voucher_seller' => 0,
          'seller_discount' => 0,
          'voucher' => 0,
          'price' => $api_amt - $ship_excel,
          'priceVATincluded' => $api_amt,
          'priceBeforeVAT' => round($api_amt / 1.07, 2),
          'VAT' => round($api_amt - ($api_amt / 1.07), 2),
          'taxable' => $api_amt,
          'code' => $keygen
        );
      }

      $sum_tax_api = $sum_tax_api + $api_amt;

      if ($row_for_prep !== null) {
        $chk_order_sho = $this->CI->shopee_prep_api_model->select_by_order_sn_code($order_sn, $keygen);
        if (empty($chk_order_sho)) {
          $this->CI->shopee_prep_api_model->insert($row_for_prep);
        }
      }
    }

    $total_cn_api = 0;
    $arr_join = $this->CI->shopee_prep_model->select_prep_join_by_orderno_code($keygen);
    if (!empty($arr_join) && !empty($buckets)) {
      foreach ($arr_join as $row) {
        $order_sn = $row['order_sn_s'];
        if (!isset($buckets[$order_sn]) || $buckets[$order_sn] !== 'cn') {
          continue;
        }
        $total_cn_api = $total_cn_api + $this->api_taxable($row);
      }
    }

    $re_arr_order_chk = $this->prep_make($keygen, $buckets);

    $tax_api = round($sum_tax_api, 2);
    $tax_excel = round($excel_tax, 2);
    $cn_excel = round($excel_cn, 2);
    $ignore_excel = round($excel_ignore, 2);
    $cn_api = round($total_cn_api, 2);

    return array(
        'total_price_api' => $tax_api,
        'total_price_excel' => $tax_excel,
        'total_cn' => $cn_api,
        'total_cn_excel' => $cn_excel,
        'total_price_cn_excel' => $tax_excel,
        'arr_order_check' => $re_arr_order_chk,
        'sho_check_detail' => array(
          'excel_ignore' => $ignore_excel,
          'excel_tax' => $tax_excel,
          'excel_cn' => $cn_excel,
          'excel_net' => round($tax_excel - $cn_excel, 2),
          'api_tax' => $tax_api,
          'api_cn' => $cn_api,
          'api_net' => round($tax_api - $cn_api, 2)
        )
    );
  }

  function prep_make($keygen, $buckets = array()){

    $arr_orderno_chk = array();
    $seen = array();
    $arr_datas = $this->CI->shopee_prep_model->select_prep_join_by_orderno_code($keygen);
    if (!empty($arr_datas)) {
      foreach ($arr_datas as $arr_data) {
        $order_sn = $arr_data['order_sn_s'];
        $bucket = isset($buckets[$order_sn]) ? $buckets[$order_sn] : 'tax';
        $excel_raw = (isset($arr_data['taxable']) && $arr_data['taxable'] !== null && $arr_data['taxable'] !== '')
          ? $arr_data['taxable']
          : $arr_data['paid_price'];
        $excel_amt = ($bucket === 'ignore') ? 0.0 : $this->_prep_to_float($excel_raw);
        $api_amt = 0.0;
        $has_api = (isset($arr_data['api_taxable']) && $arr_data['api_taxable'] !== null && $arr_data['api_taxable'] !== '')
          || (isset($arr_data['priceVATincluded']) && $arr_data['priceVATincluded'] !== null && $arr_data['priceVATincluded'] !== '')
          || (isset($arr_data['price']) && $arr_data['price'] !== null && $arr_data['price'] !== '');
        if ($has_api) {
          $api_amt = $this->api_taxable($arr_data);
        }

        if ($bucket === 'ignore') {
          if (abs($api_amt) >= 0.01) {
            $arr_orderno_chk[] = array(
              'order_sn' => $order_sn,
              'bucket' => 'ignore',
              'excel' => 0.0,
              'api' => $api_amt,
              'diff' => $api_amt,
              'note' => 'Excel ignore / API packed'
            );
            $seen[$order_sn] = true;
          }
          continue;
        }

        if (abs($excel_amt - $api_amt) >= 0.01) {
          $arr_orderno_chk[] = array(
            'order_sn' => $order_sn,
            'bucket' => $bucket,
            'excel' => $excel_amt,
            'api' => $api_amt,
            'diff' => $api_amt - $excel_amt,
            'note' => ($api_amt == 0.0) ? 'missing API' : ''
          );
          $seen[$order_sn] = true;
        }
      }
    }

    $arr_api = $this->CI->shopee_prep_api_model->select_by_code($keygen);
    if (!empty($arr_api)) {
      foreach ($arr_api as $arr_data) {
        $order_sn = $arr_data['order_sn'];
        if (isset($seen[$order_sn])) {
          continue;
        }
        $bucket = isset($buckets[$order_sn]) ? $buckets[$order_sn] : '';
        if ($bucket === '') {
          $api_amt = $this->api_taxable($arr_data);
          $arr_orderno_chk[] = array(
            'order_sn' => $order_sn,
            'bucket' => 'api_only',
            'excel' => 0.0,
            'api' => $api_amt,
            'diff' => $api_amt,
            'note' => 'API only'
          );
        }
      }
    }

    return $arr_orderno_chk;
  }

    
}