<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class lazada_bl
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();

    $this->CI->load->library("util/date_util");
    $this->CI->load->library("util/random_util");

		$this->CI->load->library("businesslogic/number_bl");

    $this->CI->load->model('lazada_orders_model');
    $this->CI->load->model('lazada_prep_model');
    $this->CI->load->model('lazada_prep_api_model');


    }

  function get_lazada_code($last_code,$cdate){
  
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($last_code == 'no'){
      $laz_newcode = "Laz".$cdate_code."00001";
    }else{
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "Laz".$laz_ymcode.$laz_nextcode;
    }
   return $laz_newcode;
  }

  function get_laz_fulltax_code($last_code,$cdate){

    //BNYLAZ20211100001

    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($last_code == 'no'){

      $laz_newcode = "BNYLAZ".$cdate_code."00001";

    }else{

      $laz_ymcode = substr($last_code, 6,6);
      $laz_code = substr($last_code, 12,5);

      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "BNYLAZ".$laz_ymcode.$laz_nextcode;

    }

     return $laz_newcode;
  }

  // Statuses that mean the order already passed pack. Used for virtual packed insert.
  // Do not use tax invoice number. Decide from Lazada API statuses only.
  function passed_pack_api_statuses(){
    return array(
      'packed',
      'repacked',
      'ready_to_ship',
      'ready_to_ship_pending',
      'shipped',
      'shipping',
      'delivered',
      'confirmed',
      'returned',
      'failed_delivery',
      'lost_by_3pl',
      'damaged_by_3pl',
      'shipped_back',
      'shipped_back_success',
      'shipped_back_failed',
      'package_scrapped'
    );
  }

  function normalize_api_status($status){
    return strtolower(trim((string)$status));
  }

  function collect_api_statuses($row, $last_status = ''){
    $list = array();
    if (isset($row['statuses'])) {
      if (is_array($row['statuses'])) {
        $list = $row['statuses'];
      } else {
        $list = array($row['statuses']);
      }
    }
    if ($last_status !== '' && $last_status !== null) {
      $list[] = $last_status;
    }
    $out = array();
    foreach ($list as $s) {
      $s = $this->normalize_api_status($s);
      if ($s !== '') {
        $out[] = $s;
      }
    }
    return array_values(array_unique($out));
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

  // Virtual packed is only for a missed packed row after API already went past pack.
  // canceled / unpaid / pending alone must not create packed.
  function should_insert_virtual_packed($api_statuses, $db_statuses, $last_status, $order_number = ''){
    $last = $this->normalize_api_status($last_status);
    if ($last === 'packed') {
      return false;
    }

    $db_norm = array();
    if (!empty($db_statuses)) {
      foreach ($db_statuses as $s) {
        $db_norm[] = $this->normalize_api_status($s);
      }
    }
    if (in_array('packed', $db_norm, true)) {
      return false;
    }

    if ($this->status_list_passed_pack($api_statuses) || $this->status_list_passed_pack($db_norm)) {
      return true;
    }

    if ($order_number !== '') {
      $tracked = $this->CI->lazada_orders_model->get_tracking_order_number_set(array($order_number));
      if (isset($tracked[$order_number])) {
        return true;
      }
    }

    return false;
  }

  function get_laz_fulltax_code_bk($last_code,$cdate){
    
    //202103
    $laz_ymcode = substr($last_code, 6,6);
    $laz_code = substr($last_code, 12,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($laz_ymcode == $cdate_code){
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "BNYLAZ".$laz_ymcode.$laz_nextcode;
    }else{

      $laz_newcode = "BNYLAZ".$cdate_code."00001";
    }

     return $laz_newcode;
  }

  function chk_prep($daterange,$file_upload){

    $arr_date = $this->CI->date_util->get_start_stop_from_date_range($daterange);
    $arr_data_return = $this->import_data_to_prep($file_upload,$arr_date['start'],$arr_date['stop']);

    return $arr_data_return;

  }

  function import_data_to_prep($file_upload,$StartDate,$EndDate){

    @set_time_limit(300);
    @ini_set('memory_limit', '1024M');

    $keygen = $this->CI->random_util->create_random_number(8);

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
          if (method_exists($objReader, 'setReadDataOnly')) {
            $objReader->setReadDataOnly(true);
          }
          $objPHPExcel = $objReader->load($file_s);
        } catch(Exception $e) {
          die('Error loading file "'.pathinfo($file_s,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $headerRow = $sheet->rangeToArray('A1:'.$highestColumn.'1', NULL, TRUE, FALSE);
        $colmap = $this->excel_col_map(isset($headerRow[0]) ? $headerRow[0] : array());

        $orders = array();
        for ($row = 2; $row <= $highestRow; $row++){
          $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
          if (empty($rowData[0])) {
            continue;
          }
          $line = $rowData[0];

          $order_sn = $this->excel_order_sn($this->excel_cell($line, $colmap['ordernumber']));
          if ($order_sn === '') {
            continue;
          }

          $createtime = $this->excel_createtime_text($this->excel_cell($line, $colmap['createtime']));
          if (!$this->excel_in_date_range($this->excel_cell($line, $colmap['createtime']), $StartDate, $EndDate)) {
            continue;
          }

          $unit_price = $this->excel_num($this->excel_cell($line, $colmap['unitprice']));
          $seller_discount = $this->excel_num($this->excel_cell($line, $colmap['sellerdiscount']));
          $taxable = $unit_price + $seller_discount;
          $shippingFee = $this->excel_num($this->excel_cell($line, $colmap['shippingfee']));
          $status = trim((string)$this->excel_cell($line, $colmap['status']));
          $initiator = trim((string)$this->excel_cell($line, $colmap['initiator']));
          $cancel_reason = trim((string)$this->excel_cell($line, $colmap['reason']));

          if (!isset($orders[$order_sn])) {
            $orders[$order_sn] = array(
              'createtime' => $createtime,
              'order_number' => $order_sn,
              'status' => $status,
              'cancel_reason' => $cancel_reason,
              'initiator' => $initiator,
              'unit_price' => $unit_price,
              'seller_discount' => $seller_discount,
              'taxable' => $taxable,
              'paid_price' => $taxable,
              'shippingFee' => $shippingFee,
              'code' => $keygen
            );
          } else {
            $orders[$order_sn]['unit_price'] = $orders[$order_sn]['unit_price'] + $unit_price;
            $orders[$order_sn]['seller_discount'] = $orders[$order_sn]['seller_discount'] + $seller_discount;
            $orders[$order_sn]['taxable'] = $orders[$order_sn]['taxable'] + $taxable;
            $orders[$order_sn]['paid_price'] = $orders[$order_sn]['paid_price'] + $taxable;
            $orders[$order_sn]['shippingFee'] = $orders[$order_sn]['shippingFee'] + $shippingFee;
            $orders[$order_sn]['status'] = $status;
            $orders[$order_sn]['initiator'] = $initiator;
            $orders[$order_sn]['cancel_reason'] = $cancel_reason;
            $orders[$order_sn]['createtime'] = $createtime;
          }
        }

        $order_sns = array_keys($orders);
        $packed_set = $this->CI->lazada_orders_model->get_packed_order_number_set($order_sns);
        $passed_pack_set = $this->CI->lazada_orders_model->get_passed_pack_order_number_set($order_sns);
        foreach ($orders as $order_sn => $data_insert) {
          $data_insert['bucket'] = $this->classify_excel_order(
            $data_insert['status'],
            $data_insert['initiator'],
            $data_insert['cancel_reason'],
            isset($packed_set[$order_sn]),
            isset($passed_pack_set[$order_sn])
          );
          $this->CI->lazada_prep_model->insert($data_insert);
        }
      }
    }

    return $this->get_data_sale_by_date($StartDate,$EndDate,$keygen);
  }

    function get_data_sale_by_date($StartDate,$EndDate,$keygen){

      $excel_sums = $this->sum_excel_by_bucket($keygen);
      $ignore_orders = array();
      if (!empty($excel_sums['buckets'])) {
        foreach ($excel_sums['buckets'] as $order_sn => $bucket) {
          if ($bucket === 'ignore') {
            $ignore_orders[$order_sn] = true;
          }
        }
      }

      $sum_tax_api = 0;

      $arr_datas = $this->CI->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
      if(!empty($arr_datas)){
        foreach($arr_datas as $arr_data){
          if (isset($ignore_orders[$arr_data['order_number']])) {
            continue;
          }
          $sum_tax_api = $sum_tax_api + $this->api_taxable($arr_data);

          $chk_data = $this->CI->lazada_prep_api_model->select_by_order_sn_code($arr_data['order_number'], $keygen);
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
              'taxable' => $this->api_taxable($arr_data),
              'code' => $keygen
            );
            $this->CI->lazada_prep_api_model->insert($data);
          }
        }
      }

      $total_cn_api = 0;
      $arr_join = $this->CI->lazada_prep_model->select_prep_join_by_orderno_code($keygen);
      if (!empty($arr_join) && !empty($excel_sums['buckets'])) {
        foreach ($arr_join as $row) {
          $order_sn = $row['order_sn_s'];
          if (!isset($excel_sums['buckets'][$order_sn]) || $excel_sums['buckets'][$order_sn] !== 'cn') {
            continue;
          }
          $total_cn_api = $total_cn_api + $this->api_taxable($row);
        }
      }

      $re_arr_order_chk = $this->prep_make($keygen, $excel_sums['buckets']);

      $tax_api = round($sum_tax_api, 2);
      $tax_excel = round($excel_sums['tax'], 2);
      $cn_excel = round($excel_sums['cn'], 2);
      $ignore_excel = round($excel_sums['ignore'], 2);
      $cn_api = round($total_cn_api, 2);

      return array(
        'total_price_api' => $tax_api,
        'total_price_excel' => $tax_excel,
        'total_cn' => $cn_api,
        'total_cn_excel' => $cn_excel,
        'total_price_cn_excel' => $tax_excel,
        'arr_order_check' => $re_arr_order_chk,
        'laz_check_detail' => array(
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

    // Tax gross = everything after pack (bucket tax + cn). CN is a subset of tax.
    function sum_excel_by_bucket($keygen){
      $tax = 0;
      $cn = 0;
      $ignore = 0;
      $buckets = array();

      $arr_excel = $this->CI->lazada_prep_model->select_by_code($keygen);
      if (empty($arr_excel)) {
        return array('tax' => 0, 'cn' => 0, 'ignore' => 0, 'buckets' => $buckets);
      }

      $order_numbers = array();
      foreach ($arr_excel as $row) {
        $order_numbers[] = $row['order_number'];
      }
      $packed_set = $this->CI->lazada_orders_model->get_packed_order_number_set($order_numbers);
      $passed_pack_set = $this->CI->lazada_orders_model->get_passed_pack_order_number_set($order_numbers);

      foreach ($arr_excel as $row) {
        $bucket = $this->classify_excel_order(
          $row['status'],
          $row['initiator'],
          $row['cancel_reason'],
          isset($packed_set[$row['order_number']]),
          isset($passed_pack_set[$row['order_number']])
        );
        if (isset($row['lazada_prep_id']) && (!isset($row['bucket']) || $row['bucket'] !== $bucket)) {
          $this->CI->lazada_prep_model->update(array('bucket' => $bucket), $row['lazada_prep_id']);
        }
        $amt = isset($row['taxable']) && $row['taxable'] !== null && $row['taxable'] !== ''
          ? floatval($row['taxable'])
          : floatval($row['paid_price']);
        $buckets[$row['order_number']] = $bucket;
        if ($bucket === 'cn') {
          $tax = $tax + $amt;
          $cn = $cn + $amt;
        } elseif ($bucket === 'ignore') {
          $ignore = $ignore + $amt;
        } else {
          $tax = $tax + $amt;
        }
      }

      return array('tax' => $tax, 'cn' => $cn, 'ignore' => $ignore, 'buckets' => $buckets);
    }

    function prep_make($keygen, $buckets = array()){

      $arr_orderno_chk = array();
      $seen = array();

      $arr_datas = $this->CI->lazada_prep_model->select_prep_join_by_orderno_code($keygen);
      if (!empty($arr_datas)) {
        foreach($arr_datas as $arr_data){
          $order_sn = $arr_data['order_sn_s'];
          $bucket = isset($buckets[$order_sn]) ? $buckets[$order_sn] : 'tax';
          $excel_raw = (isset($arr_data['taxable']) && $arr_data['taxable'] !== null && $arr_data['taxable'] !== '')
            ? $arr_data['taxable']
            : $arr_data['paid_price'];
          $excel_amt = ($bucket === 'ignore') ? 0 : floatval($excel_raw);
          $api_amt = 0;
          if (isset($arr_data['price']) && $arr_data['price'] !== null && $arr_data['price'] !== '') {
            $api_amt = $this->api_taxable($arr_data);
          }

          if ($bucket === 'ignore') {
            if ($api_amt != 0) {
              $arr_orderno_chk[] = $order_sn.' (Excel ignore / API packed)';
              $seen[$order_sn] = true;
            }
            continue;
          }

          if (round($excel_amt, 2) != round($api_amt, 2)) {
            $arr_orderno_chk[] = $order_sn;
            $seen[$order_sn] = true;
          }
        }
      }

      $arr_api = $this->CI->lazada_prep_api_model->select_by_code($keygen);
      if (!empty($arr_api)) {
        foreach ($arr_api as $arr_data) {
          $order_sn = $arr_data['order_number'];
          if (isset($seen[$order_sn])) {
            continue;
          }
          $bucket = isset($buckets[$order_sn]) ? $buckets[$order_sn] : '';
          if ($bucket === '') {
            $arr_orderno_chk[] = $order_sn.' (API only)';
          }
        }
      }

      return $arr_orderno_chk;
    }

    function filter_ignore_from_tax_orders($arr_orders){
      return $this->CI->lazada_orders_model->filter_orders_not_passed_pack($arr_orders);
    }

    function api_taxable($row){
      $price = isset($row['price']) ? floatval($row['price']) : 0;
      $voucher_seller = isset($row['voucher_seller']) ? floatval($row['voucher_seller']) : 0;
      return $price - $voucher_seller;
    }

    // ignore = before pack. tax = after pack. cn = after pack then reverse (still inside tax gross).
    // Packed row alone is not enough for cancel: unpaid timeout can have a synthetic packed row.
    function classify_excel_order($status, $initiator, $reason, $has_packed, $has_after_pack = false){
      $st = $this->normalize_status($status);
      $reason_text = is_string($reason) ? $reason : '';

      $cn_status = array(
        'package returned', 'returned', 'lost by 3pl', 'damaged by 3pl',
        'failed delivery', 'shipped back', 'shipped back success', 'shipped back failed',
        'package scrapped', 'lost', 'damaged'
      );
      if (in_array($st, $cn_status, true)) {
        return 'cn';
      }

      $payment_timeout = (strpos($reason_text, 'ชำระเงินไม่สำเร็จ') !== false);
      $after_pack_reason = (
        (strpos($reason_text, 'ปฏิเสธการรับ') !== false) ||
        (strpos($reason_text, 'ติดต่อลูกค้าไม่ได้') !== false) ||
        (strpos($reason_text, 'การจัดส่งไม่สำเร็จ') !== false)
      );

      if ($st === 'canceled' || $st === 'cancelled' || $st === 'cancel') {
        if ($payment_timeout) {
          return 'ignore';
        }
        if ($after_pack_reason || $has_after_pack) {
          return 'cn';
        }
        return 'ignore';
      }

      if ($st === 'unpaid' || $st === 'pending') {
        return 'ignore';
      }

      if ($has_packed) {
        return 'tax';
      }

      $tax_status = array(
        'packed', 'repacked', 'topack', 'toship', 'ready to ship', 'ready to ship pending',
        'shipped', 'shipping', 'delivered', 'confirmed'
      );
      if (in_array($st, $tax_status, true)) {
        return 'tax';
      }

      return 'ignore';
    }

    function normalize_status($status){
      $st = strtolower(trim((string)$status));
      $st = str_replace('_', ' ', $st);
      $st = preg_replace('/\s+/', ' ', $st);
      return $st;
    }

    function excel_col_map($header_row){
      $map = array();
      if (!empty($header_row)) {
        foreach ($header_row as $idx => $name) {
          $key = strtolower(preg_replace('/[^a-z0-9]/', '', (string)$name));
          $map[$key] = $idx;
        }
      }

      return array(
        'createtime' => $this->excel_col_pick($map, array('createtime', 'createdat', 'createdtime'), 8),
        'ordernumber' => $this->excel_col_pick($map, array('ordernumber', 'orderno', 'ordersn'), 12),
        'unitprice' => $this->excel_col_pick($map, array('unitprice'), 47),
        'sellerdiscount' => $this->excel_col_pick($map, array('sellerdiscounttotal', 'sellerdiscount'), 48),
        'shippingfee' => $this->excel_col_pick($map, array('shippingfee'), 50),
        'status' => $this->excel_col_pick($map, array('status'), 66),
        'initiator' => $this->excel_col_pick($map, array('buyerfaileddeliveryreturninitiator', 'initiator'), 67),
        'reason' => $this->excel_col_pick($map, array('buyerfaileddeliveryreason', 'cancelreason', 'reason'), 68)
      );
    }

    function excel_col_pick($map, $aliases, $fallback){
      foreach ($aliases as $alias) {
        if (isset($map[$alias])) {
          return $map[$alias];
        }
      }
      return $fallback;
    }

    function excel_cell($line, $idx){
      if ($idx === null || $idx === false) {
        return '';
      }
      return isset($line[$idx]) ? $line[$idx] : '';
    }

    function excel_order_sn($v){
      if ($v === null || $v === '') {
        return '';
      }
      if (is_numeric($v) && !is_string($v)) {
        return sprintf('%.0f', $v);
      }
      return trim((string)$v);
    }

    function excel_createtime_text($v){
      if ($v instanceof DateTime) {
        return $v->format('Y-m-d H:i:s');
      }
      $ts = $this->parse_excel_datetime($v);
      if ($ts !== false) {
        return date('Y-m-d H:i:s', $ts);
      }
      return is_scalar($v) ? (string)$v : '';
    }

    function excel_num($v){
      if ($v === null || $v === '') {
        return 0.0;
      }
      if (is_numeric($v)) {
        return floatval($v);
      }
      $s = str_replace(',', '', trim((string)$v));
      if ($s === '' || $s === '-') {
        return 0.0;
      }
      return floatval($s);
    }

    function excel_in_date_range($createtime, $StartDate, $EndDate){
      $ts = $this->parse_excel_datetime($createtime);
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

    function parse_excel_datetime($v){
      if ($v === null || $v === '') {
        return false;
      }
      if ($v instanceof DateTime) {
        return $v->getTimestamp();
      }
      if (is_numeric($v)) {
        if (class_exists('PHPExcel_Shared_Date')) {
          $dt = PHPExcel_Shared_Date::excelToDateTimeObject($v);
          if ($dt instanceof DateTime) {
            return $dt->getTimestamp();
          }
        }
        return false;
      }
      $s = trim((string)$v);
      $ts = strtotime($s);
      return ($ts === false) ? false : $ts;
    }

}