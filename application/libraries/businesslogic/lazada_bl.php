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
                $totol_price = 0;
                $totol_ship = 0;
                $keygen = $this->CI->random_util->create_random_number(8);

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

                    //$arr_chk = $this->CI->lazada_prep_model->select_by_order_sn($order_sn);
                    
                    

                
                
                //echo $order_sn.">>>>>".$highestRow.">>".$num."-->>".$order_tmp."<br>";
                
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

                            $this->CI->lazada_prep_model->insert($data_insert);

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

                        //echo "Last-->>".$num."<---Order--->".$order_sn."<br>";

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

                            $this->CI->lazada_prep_model->insert($data_insert);

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

                            $this->CI->lazada_prep_model->insert($data_insert);

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

                            $this->CI->lazada_prep_model->insert($data_insert);
                        }

                        

                    }
                 } //if($num == 1){

                 //$data_no = $data_no +1;
                    
                     
                }//for ($row = 2; $row <= $highestRow; $row++){ 

            }   

        }

        $arr_data = $this->get_data_sale_by_date($StartDate,$EndDate,$keygen);

        //echo "----arr_data----<br>";
        //print_r($arr_data);
        //echo "--------<br>";

       return $arr_data;
    }

    function get_data_sale_by_date($StartDate,$EndDate,$keygen){

        //$StartDate = '2024-12-01';
        //$EndDate = '2024-12-31';
      $ref_price = 0;
      $sum_ref_price = 0;
      $shipping_fee = 0;
      $total_price_api_cn = 0;

      $arr_datas = $this->CI->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
      //print_r($arr_datas);

        foreach($arr_datas as $arr_data){

          $ref_price = $arr_data["price"] - $arr_data["voucher_seller"];
          $sum_ref_price = $sum_ref_price + $ref_price;
          $shipping_fee = $shipping_fee + $arr_data['shipping_fee'];

          $chk_data = $this->CI->lazada_prep_api_model->select_by_order_sn($arr_data['order_number']);  
          //print_r($chk_data);
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

            $this->CI->lazada_prep_api_model->insert($data);
          }

        }

        $total_price_api_cn = $sum_ref_price+$shipping_fee;
        //------- LAZ CN -----
        $total_cn = 0; 

        $arr_cn_datas =$this->CI->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDateCn($StartDate,$EndDate);
        if(!empty($arr_cn_datas)){
          foreach($arr_cn_datas as $arr_cn_data){
            $total_cn = $total_cn+$arr_cn_data['ValueBeforeVAT'];
          }
        }
        //------- LAZ CN -----

        $total_price_api = intval($total_price_api_cn) - intval($total_cn);

        //$arr_lazada_cn =$this->CI->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);

        $re_arr_order_chk = $this->prep_make($keygen);

        //------ Sum Price From EXcel -------

        $arr_data_complete = $this->CI->lazada_prep_model->select_by_complete_all($keygen);
        //$arr_data_complete = $this->CI->lazada_prep_model->select_by_complete($keygen,array('returned','Package Returned','canceled','Lost by 3PL'));
        //$arr_data_cn = $this->CI->lazada_prep_model->select_by_cn($keygen,array('returned','Package Returned','canceled'));
        //$arr_data_retuen_cn = $this->CI->shopee_prep_model->select_by_retuen($keygen);

        //$total_cn = 0;

        $total_sale = intval($arr_data_complete['sum_sale']);
        //$total_sale = intval($arr_data_complete['sum_sale']) + intval($total_cn);
        //------ Sum Price From EXcel --------

        $array_prep_re=array(
          'total_price_api' => $total_price_api_cn,
          'total_price_excel' => $arr_data_complete['sum_sale'],
          'total_cn' => $total_cn,
          'total_cn_excel' => $total_cn,
          'total_price_cn_excel' => $total_sale,
          'arr_order_check' => $re_arr_order_chk
        );

        //echo "--------<br>";
        //print_r($array_prep_re);
        //echo "--------<br>";

        return $array_prep_re;

      }

      function prep_make($keygen){

        $arr_orderno_chk = array();

        $arr_datas = $this->CI->lazada_prep_model->select_prep_join_by_orderno_code($keygen);
        //print_r($arr_datas);
        $num  = 1;
        foreach($arr_datas as $arr_data){

          $ref_price = $arr_data["price"] - $arr_data["voucher_seller"];

          $diffprice = $arr_data['paid_price']-$ref_price;

          
          //echo $num.">>order no".$arr_data['order_sn_s'].">>excel>>>".$arr_data['paid_price'].">>API>>".$ref_price.">>diff>>".intval($diffprice)."<br>";

          //echo intval($diffprice);
          //echo "<br>";
          if(intval($diffprice) != 0){

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