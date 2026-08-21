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

    function passed_pack_api_statuses(){
      return array(
        'AWAITING_SHIPMENT',
        'AWAITING_COLLECTION',
        'READY_TO_SHIP',
        'IN_TRANSIT',
        'DELIVERED',
        'COMPLETED',
        'SHIPPED',
        'TO_CONFIRM_RECEIVE',
        'PARTIALLY_SHIPPING'
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
        if ($s === 'PACKET') {
          continue;
        }
        if (in_array($s, $passed, true)) {
          return true;
        }
      }
      return false;
    }

    function should_insert_virtual_packed($api_statuses, $db_statuses, $last_status, $order_id = '', $tracking_number = ''){
      $last = $this->normalize_api_status($last_status);
      if ($last === 'PACKET') {
        return false;
      }
      $db_norm = array();
      if (!empty($db_statuses)) {
        foreach ($db_statuses as $s) {
          $db_norm[] = $this->normalize_api_status($s);
        }
      }
      if (in_array('PACKET', $db_norm, true)) {
        return false;
      }
      if ($this->status_list_passed_pack($api_statuses) || $this->status_list_passed_pack($db_norm)) {
        return true;
      }
      if (trim((string)$tracking_number) !== '') {
        return true;
      }
      if ($order_id !== '') {
        $tracked = $this->CI->tiktok_orders_model->get_tracking_order_id_set(array($order_id));
        if (isset($tracked[$order_id])) {
          return true;
        }
      }
      return false;
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

  // TikTok Excel taxable (Shopee-style): buyer-paid W + all platform goods discounts.
  //   W + N + PaymentPlatformDiscount
  // Equivalent: P + N + Q. Seller discount (O) stays out. Do NOT add shipping platform disc.
  private function _prep_to_float($val)
  {
    if ($val === null || $val === '') {
      return 0.0;
    }
    if (is_numeric($val)) {
      return floatval($val);
    }
    $s = trim((string)$val);
    if ($s === '' || $s === '-') {
      return 0.0;
    }
    // "THB 1,234.56" / "฿1,234.56"
    if (preg_match('/[-]?[0-9]+(?:[.,][0-9]+)*/', str_replace(',', '', $s), $m)) {
      return floatval(str_replace(',', '', $m[0]));
    }
    $s = str_replace(array(',', 'THB', 'thb', '฿', ' '), '', $s);
    return floatval($s);
  }

  private function _excel_header_index($header_row, $needles, $fallback)
  {
    if (!empty($header_row)) {
      foreach ($header_row as $idx => $name) {
        $n = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$name));
        foreach ($needles as $needle) {
          $k = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$needle));
          if ($k !== '' && $n === $k) {
            return $idx;
          }
        }
      }
      foreach ($header_row as $idx => $name) {
        $n = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$name));
        foreach ($needles as $needle) {
          $k = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$needle));
          if ($k !== '' && strpos($n, $k) !== false) {
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

  private function _tiktok_excel_col_map($header_row)
  {
    return array(
      'order_id' => $this->_excel_header_index($header_row, array('Order ID', 'orderid'), 0),
      'status' => $this->_excel_header_index($header_row, array('Order Status', 'orderstatus'), 1),
      'substatus' => $this->_excel_header_index($header_row, array('Order Substatus', 'ordersubstatus'), 2),
      'cancel_type' => $this->_excel_header_index($header_row, array('Cancelation/Return Type', 'Cancellation/Return Type', 'cancelationreturntype'), 3),
      'sku_before' => $this->_excel_header_index($header_row, array('SKU Subtotal Before Discount', 'skusubtotalbeforediscount'), 12),
      'platform_disc' => $this->_excel_header_index($header_row, array('SKU Platform Discount', 'skuplatformdiscount'), 13),
      'seller_disc' => $this->_excel_header_index($header_row, array('SKU Seller Discount', 'skusellerdiscount'), 14),
      'sku_after' => $this->_excel_header_index($header_row, array('SKU Subtotal After Discount', 'skusubtotalafterdiscount'), 15),
      'ship_after' => $this->_excel_header_index($header_row, array('Shipping Fee After Discount', 'shippingfeeafterdiscount'), 16),
      'pay_platform' => $this->_excel_header_index($header_row, array('Payment platform discount', 'paymentplatformdiscount'), 20),
      'order_amount' => $this->_excel_header_index($header_row, array('Order Amount', 'orderamount'), 22),
      'refund' => $this->_excel_header_index($header_row, array('Order Refund Amount', 'orderrefundamount'), 23),
      'created' => $this->_excel_header_index($header_row, array('Created Time', 'createdtime'), 24),
      'cancel_reason' => $this->_excel_header_index($header_row, array('Cancel Reason', 'cancelreason'), 31)
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
    if (is_numeric($v) && (float)$v > 20000) {
      // Excel serial date
      $unix = ((float)$v - 25569) * 86400;
      return date('Y-m-d H:i:s', (int)$unix);
    }
    $raw = is_scalar($v) ? trim((string)$v) : '';
    if ($raw === '') {
      return '';
    }
    $ts = strtotime(str_replace('/', '-', $raw));
    return ($ts === false) ? $raw : date('Y-m-d H:i:s', $ts);
  }

  function normalize_status($status)
  {
    $st = strtolower(trim((string)$status));
    $st = str_replace('_', ' ', $st);
    $st = preg_replace('/\s+/', ' ', $st);
    return $st;
  }

  // Pack tax point: Completed / ship / RTS / cancel-after-pack → tax|cn; unpaid/pre-pack cancel → ignore.
  function classify_excel_order($status, $cancel_type, $cancel_reason, $has_after_pack = false)
  {
    $st = $this->normalize_status($status);
    $ctype = $this->normalize_status($cancel_type);
    $reason = $this->normalize_status($cancel_reason);

    if ($ctype !== '' && (strpos($ctype, 'return') !== false || strpos($ctype, 'refund') !== false)) {
      return 'cn';
    }

    $is_cancel = (
      $st === 'canceled' || $st === 'cancelled' || $st === 'cancel'
      || strpos($ctype, 'cancel') !== false
    );

    if ($is_cancel) {
      if (strpos($reason, 'payment') !== false || strpos($reason, 'unpaid') !== false) {
        return 'ignore';
      }
      if ($has_after_pack) {
        return 'cn';
      }
      // Ready-to-ship export often has cancel without API pack history → treat as CN if RTS-ish status text elsewhere missing.
      if (strpos($reason, 'seller') !== false || strpos($reason, 'buyer') !== false || strpos($reason, 'delivery') !== false) {
        return 'cn';
      }
      return 'ignore';
    }

    if ($st === 'unpaid' || $st === 'on hold' || $st === 'pending') {
      return 'ignore';
    }

    $tax_status = array(
      'completed', 'delivered', 'in transit', 'shipped', 'to ship', 'awaiting collection',
      'awaiting shipment', 'ready to ship', 'partially shipping', 'packet'
    );
    if (in_array($st, $tax_status, true) || $has_after_pack) {
      return 'tax';
    }

    return 'ignore';
  }

  // API Check amount: prefer stored taxable; else priceVATincluded + platform (Shopee-style W+N).
  function api_taxable($row)
  {
    if (isset($row['api_taxable']) && $row['api_taxable'] !== null && $row['api_taxable'] !== '') {
      return $this->_prep_to_float($row['api_taxable']);
    }
    if (isset($row['taxable']) && $row['taxable'] !== null && $row['taxable'] !== ''
        && !isset($row['order_sn_s']) && !isset($row['paid_price'])) {
      return $this->_prep_to_float($row['taxable']);
    }
    $vat_incl = isset($row['priceVATincluded']) ? $this->_prep_to_float($row['priceVATincluded']) : 0.0;
    $platform = isset($row['voucher_platform']) ? $this->_prep_to_float($row['voucher_platform']) : 0.0;
    if ($vat_incl != 0.0) {
      return $vat_incl + $platform;
    }
    $price = isset($row['price']) ? $this->_prep_to_float($row['price']) : 0.0;
    $seller = isset($row['voucher_seller']) ? $this->_prep_to_float($row['voucher_seller']) : 0.0;
    if ($seller == 0.0 && isset($row['voucher'])) {
      $seller = $this->_prep_to_float($row['voucher']);
    }
    $ship = isset($row['shipping_fee']) ? $this->_prep_to_float($row['shipping_fee']) : 0.0;
    if (isset($row['api_shipping_fee']) && $row['api_shipping_fee'] !== null && $row['api_shipping_fee'] !== '') {
      $ship = $this->_prep_to_float($row['api_shipping_fee']);
    }
    return $price - $seller + $platform + $ship;
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

    $file_s = APP_STORE_PATH.'/uploads/xls/'.$res['file_name'];
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
      die('Error loading file "'.pathinfo($file_s, PATHINFO_BASENAME).'": '.$e->getMessage());
    }

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    $keygen = $this->CI->random_util->create_random_number(8);

    $headerRow = $sheet->rangeToArray('A1:'.$highestColumn.'1', NULL, TRUE, FALSE);
    $colmap = $this->_tiktok_excel_col_map(isset($headerRow[0]) ? $headerRow[0] : array());

    // Row 2 is description; data from row 3.
    $orders = array();
    for ($row = 3; $row <= $highestRow; $row++) {
      $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, NULL, TRUE, FALSE);
      if (empty($rowData[0])) {
        continue;
      }
      $line = $rowData[0];
      $order_id = trim((string)$this->_excel_cell($line, $colmap['order_id']));
      if ($order_id === '') {
        continue;
      }

      $order_date = $this->_excel_date_text($this->_excel_cell($line, $colmap['created']));
      if (!$this->_excel_in_date_range($order_date, $StartDate, $EndDate)) {
        continue;
      }

      $platform_n = $this->_prep_to_float($this->_excel_cell($line, $colmap['platform_disc']));
      $seller_o = $this->_prep_to_float($this->_excel_cell($line, $colmap['seller_disc']));
      $sku_m = $this->_prep_to_float($this->_excel_cell($line, $colmap['sku_before']));
      $sku_p = $this->_prep_to_float($this->_excel_cell($line, $colmap['sku_after']));
      $ship_q = $this->_prep_to_float($this->_excel_cell($line, $colmap['ship_after']));
      $pay_plat = $this->_prep_to_float($this->_excel_cell($line, $colmap['pay_platform']));
      $order_w = $this->_prep_to_float($this->_excel_cell($line, $colmap['order_amount']));

      if (!isset($orders[$order_id])) {
        $orders[$order_id] = array(
          'order_sn' => $order_id,
          'order_date' => $order_date,
          'status' => trim((string)$this->_excel_cell($line, $colmap['status'])),
          'cancel_type' => trim((string)$this->_excel_cell($line, $colmap['cancel_type'])),
          'cancel_reason' => trim((string)$this->_excel_cell($line, $colmap['cancel_reason'])),
          'platform_disc' => $platform_n,
          'pay_platform' => $pay_plat,
          'seller_discount' => $seller_o,
          'unit_price' => $sku_m,
          'subtotal_after_discount' => $sku_p,
          'shipping_fee' => $ship_q,
          'order_amount_w' => $order_w,
          'code' => $keygen
        );
      } else {
        $orders[$order_id]['platform_disc'] += $platform_n;
        $orders[$order_id]['seller_discount'] += $seller_o;
        $orders[$order_id]['unit_price'] += $sku_m;
        $orders[$order_id]['subtotal_after_discount'] += $sku_p;
        // Q / W / Payment platform discount are order-level: keep first non-zero.
        if ($orders[$order_id]['shipping_fee'] == 0.0 && $ship_q != 0.0) {
          $orders[$order_id]['shipping_fee'] = $ship_q;
        }
        if ($orders[$order_id]['order_amount_w'] == 0.0 && $order_w != 0.0) {
          $orders[$order_id]['order_amount_w'] = $order_w;
        }
        if ($orders[$order_id]['pay_platform'] == 0.0 && $pay_plat != 0.0) {
          $orders[$order_id]['pay_platform'] = $pay_plat;
        }
        $st = trim((string)$this->_excel_cell($line, $colmap['status']));
        if ($st !== '') {
          $orders[$order_id]['status'] = $st;
        }
        $ct = trim((string)$this->_excel_cell($line, $colmap['cancel_type']));
        if ($ct !== '') {
          $orders[$order_id]['cancel_type'] = $ct;
        }
        $cr = trim((string)$this->_excel_cell($line, $colmap['cancel_reason']));
        if ($cr !== '') {
          $orders[$order_id]['cancel_reason'] = $cr;
        }
      }
    }

    $order_ids = array_keys($orders);
    $after_pack_set = $this->CI->tiktok_orders_model->get_passed_pack_order_id_set($order_ids);

    foreach ($orders as $order_id => $data) {
      // Shopee-style Excel tax = P + N + Q (always).
      // Equals W + N + Payment platform discount. Prefer P+N+Q so Payment-disc rows
      // (e.g. 585166766641218887: W 95.31 + Pay 0.09 → 95.40) match API without relying on col T.
      $n = $this->_prep_to_float($data['platform_disc']);
      $p = $this->_prep_to_float($data['subtotal_after_discount']);
      $q = $this->_prep_to_float($data['shipping_fee']);
      $w = $this->_prep_to_float($data['order_amount_w']);
      $pay = $this->_prep_to_float($data['pay_platform']);
      $taxable = round($p + $n + $q, 2);
      if ($taxable == 0.0 && $w != 0.0) {
        $taxable = round($w + $n + $pay, 2);
      }

      $bucket = $this->classify_excel_order(
        $data['status'],
        $data['cancel_type'],
        $data['cancel_reason'],
        isset($after_pack_set[$order_id])
      );

      $this->CI->tiktok_prep_model->insert(array(
        'order_sn' => $order_id,
        'order_date' => $data['order_date'],
        'status' => $data['status'],
        'cancel_type' => $data['cancel_type'],
        'cancel_reason' => $data['cancel_reason'],
        'paid_price' => $taxable,
        'cn_paid_price' => ($bucket === 'cn') ? $taxable : 0,
        'logistic_price' => $q,
        'shipping_fee' => $q,
        'unit_price' => $data['unit_price'],
        'seller_discount' => $data['seller_discount'],
        'subtotal_after_discount' => $p,
        'order_amount_w' => $w,
        'taxable' => $taxable,
        'bucket' => $bucket,
        'code' => $keygen
      ));
    }

    return $this->get_data_sale_by_date($StartDate, $EndDate, $keygen);
  }

  function get_data_sale_by_date($StartDate, $EndDate, $keygen)
  {
    $arr_excel = $this->CI->tiktok_prep_model->select_by_code($keygen);
    $excel_tax = 0.0;
    $excel_cn = 0.0;
    $excel_ignore = 0.0;
    $buckets = array();
    $excel_amt_map = array();
    $excel_ship = array();
    $order_ids = array();

    if (!empty($arr_excel)) {
      foreach ($arr_excel as $row) {
        $order_ids[] = $row['order_sn'];
      }
    }
    $after_pack_set = $this->CI->tiktok_orders_model->get_passed_pack_order_id_set($order_ids);

    if (!empty($arr_excel)) {
      foreach ($arr_excel as $row) {
        $bucket = isset($row['bucket']) && $row['bucket'] !== '' && $row['bucket'] !== null
          ? $row['bucket']
          : $this->classify_excel_order(
              $row['status'],
              isset($row['cancel_type']) ? $row['cancel_type'] : '',
              isset($row['cancel_reason']) ? $row['cancel_reason'] : '',
              isset($after_pack_set[$row['order_sn']])
            );
        $amt = (isset($row['taxable']) && $row['taxable'] !== null && $row['taxable'] !== '')
          ? $this->_prep_to_float($row['taxable'])
          : $this->_prep_to_float($row['paid_price']);
        $ship = isset($row['shipping_fee']) ? $this->_prep_to_float($row['shipping_fee']) : 0.0;
        $excel_ship[$row['order_sn']] = $ship;
        $excel_amt_map[$row['order_sn']] = $amt;
        $buckets[$row['order_sn']] = $bucket;
        if (isset($row['tiktok_prep_id']) && (!isset($row['bucket']) || $row['bucket'] !== $bucket)) {
          $this->CI->tiktok_prep_model->update(array('bucket' => $bucket), $row['tiktok_prep_id']);
        }
        if ($bucket === 'ignore') {
          $excel_ignore += $amt;
        } elseif ($bucket === 'cn') {
          $excel_tax += $amt;
          $excel_cn += $amt;
        } else {
          $excel_tax += $amt;
        }
      }
    }

    $sum_tax_api = 0.0;
    $excel_tax_ids = array();
    foreach ($buckets as $order_id => $bucket) {
      if ($bucket !== 'ignore') {
        $excel_tax_ids[] = $order_id;
      }
    }

    $sp_map = array();
    $arr_tiktoks = $this->CI->tiktok_orders_model->tiktok_select_order_with_DateStart_DateEnd($StartDate, $EndDate);
    if (!empty($arr_tiktoks)) {
      foreach ($arr_tiktoks as $arr_tiktok) {
        $sp_map[$arr_tiktok['order_id']] = $arr_tiktok;
      }
    }

    foreach ($excel_tax_ids as $order_id) {
      $api_amt = 0.0;
      $row_for_prep = null;
      $ship_excel = isset($excel_ship[$order_id]) ? $excel_ship[$order_id] : 0.0;

      if (isset($sp_map[$order_id])) {
        $arr_tiktok = $sp_map[$order_id];
        // Shopee-style API: goods (priceVATincluded ≈ buyer paid W) + platform voucher.
        $vat_incl = isset($arr_tiktok['priceVATincluded']) ? $this->_prep_to_float($arr_tiktok['priceVATincluded']) : 0.0;
        $platform = isset($arr_tiktok['voucher_platform']) ? $this->_prep_to_float($arr_tiktok['voucher_platform']) : 0.0;
        if ($vat_incl != 0.0) {
          $api_amt = $vat_incl + $platform;
        } else {
          $price = isset($arr_tiktok['price']) ? $this->_prep_to_float($arr_tiktok['price']) : 0.0;
          $seller = isset($arr_tiktok['voucher_seller']) ? $this->_prep_to_float($arr_tiktok['voucher_seller']) : 0.0;
          $api_amt = $price - $seller + $platform + $ship_excel;
        }
        $row_for_prep = array(
          'order_id' => $order_id,
          'status' => isset($arr_tiktok['status']) ? $arr_tiktok['status'] : null,
          'transactiondate' => isset($arr_tiktok['transactiondate']) ? $arr_tiktok['transactiondate'] : null,
          'start_inv' => isset($arr_tiktok['start_inv']) ? $arr_tiktok['start_inv'] : null,
          'end_inv' => isset($arr_tiktok['end_inv']) ? $arr_tiktok['end_inv'] : null,
          'shipping_fee' => $ship_excel,
          'voucher_platform' => $platform,
          'voucher_seller' => isset($arr_tiktok['voucher_seller']) ? $arr_tiktok['voucher_seller'] : 0,
          'voucher' => isset($arr_tiktok['voucher']) ? $arr_tiktok['voucher'] : 0,
          'price' => isset($arr_tiktok['price']) ? $arr_tiktok['price'] : 0,
          'original_price' => isset($arr_tiktok['original_price']) ? $arr_tiktok['original_price'] : null,
          'priceVATincluded' => $api_amt,
          'priceBeforeVAT' => round($api_amt / 1.07, 2),
          'VAT' => round($api_amt - ($api_amt / 1.07), 2),
          'taxable' => $api_amt,
          'code' => $keygen
        );
      } elseif (isset($excel_amt_map[$order_id])) {
        // Missing API row: unlock Check with Excel amount (same as Shopee fallback).
        $api_amt = $excel_amt_map[$order_id];
        $row_for_prep = array(
          'order_id' => $order_id,
          'status' => null,
          'transactiondate' => null,
          'start_inv' => null,
          'end_inv' => null,
          'shipping_fee' => $ship_excel,
          'voucher_platform' => 0,
          'voucher_seller' => 0,
          'voucher' => 0,
          'price' => $api_amt - $ship_excel,
          'original_price' => null,
          'priceVATincluded' => $api_amt,
          'priceBeforeVAT' => round($api_amt / 1.07, 2),
          'VAT' => round($api_amt - ($api_amt / 1.07), 2),
          'taxable' => $api_amt,
          'code' => $keygen
        );
      }

      $sum_tax_api += $api_amt;

      if ($row_for_prep !== null) {
        $chk = $this->CI->tiktok_prep_api_model->select_by_order_id_code($order_id, $keygen);
        if (empty($chk)) {
          $this->CI->tiktok_prep_api_model->insert($row_for_prep);
        }
      }
    }

    $total_cn_api = 0.0;
    $arr_join = $this->CI->tiktok_prep_model->select_prep_join_by_orderno_code($keygen);
    if (!empty($arr_join) && !empty($buckets)) {
      foreach ($arr_join as $row) {
        $order_id = $row['order_sn_s'];
        if (!isset($buckets[$order_id]) || $buckets[$order_id] !== 'cn') {
          continue;
        }
        $total_cn_api += $this->api_taxable($row);
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
      'tik_check_detail' => array(
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

  function prep_make($keygen, $buckets = array())
  {
    $arr_orderno_chk = array();
    $seen = array();
    $arr_datas = $this->CI->tiktok_prep_model->select_prep_join_by_orderno_code($keygen);
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

    $arr_api = $this->CI->tiktok_prep_api_model->select_by_code($keygen);
    if (!empty($arr_api)) {
      foreach ($arr_api as $arr_data) {
        $order_sn = $arr_data['order_id'];
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
            'note' => 'API only (not in Excel)'
          );
        }
      }
    }

    return $arr_orderno_chk;
  }

  function explode_th_bk($data)
  {
    return $this->_prep_to_float($data);
  }

  function explode_thb($data)
  {
    return $this->_prep_to_float($data);
  }

}