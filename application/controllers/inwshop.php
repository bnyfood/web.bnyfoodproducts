<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class inwshop extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
		$this->load->library('util/View_util');

		$this->load->library('businesslogic/curl_bl');
		$this->load->library("businesslogic/upload_bl");
        $this->load->library('businesslogic/inwshop_bl');

		$this->load->library('util/encryption_util');

        $this->load->model('inwshop_data_model');
		$this->load->model('inwshop_item_data_model');
        $this->load->model('inwshop_taxinvoiceid_model');

     }
     
	function inwshop_import_sale_chk(){
        $this->load->view('inwshop/inwshop_import_sale_chk');
     }

 function inwshop_import_sale_chk_action(){

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
            //$sheetNames = $excel->getSheetNames();
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

            for ($row = 2; $row <= $highestRow; $row++){ 
                $num =$num+1;

	            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
	                                                NULL,
	                                                TRUE,
	                                                FALSE);
	                //  Insert row data array into your database of choice here
	                //print_r($rowData);
	            $ctime = $rowData[0][10];
                $order_id = $rowData[0][1];
                $status = $rowData[0][7];
                $cus_name = $rowData[0][3];

                $price = $rowData[0][17];
                $delivery = $rowData[0][18];
                $discount = $rowData[0][19];
                $discount = str_replace("-","",$discount);
                
                $amount_include_vat = ($price+$delivery)-$discount;
                $amount_exclude_vat = $amount_include_vat/1.07;
                $amount_exclude_vat = round($amount_exclude_vat,2);
                $vat = $amount_include_vat-$amount_exclude_vat;
                $vat = round($vat,2); 

                $date = str_replace('/', '-', $ctime);
                $date_to_db = date('Y/m/d H:i:s', strtotime($date));

            	$data = array(

            		'ctime' => $date_to_db,
		            'order_id' => $order_id,
		            'cus_name' => $cus_name,
		            'status' => $status,
		            'price' => $price,
		            'delivery' => $delivery,
		            'discount' => $discount,
		            'amount_include_vat' => $amount_include_vat,
		            'amount_exclude_vat' => $amount_exclude_vat,
		            'vat' => $vat,
		            'code' => $keygen

            	);

            	$this->inwshop_data_model->insert($data);

  
            }


        }   

        $sheet2 = $objPHPExcel->getSheet(1); 
        $highestRow2 = $sheet2->getHighestRow(); 
        $highestColumn2 = $sheet2->getHighestColumn();

        for ($row2 = 2; $row2 <= $highestRow2; $row2++){ 

            $rowData2 = $sheet2->rangeToArray('A' . $row2 . ':' . $highestColumn2 . $row2,
                                                NULL,
                                                TRUE,
                                                FALSE);
            
            $order_id = $rowData2[0][0];
            $sku = $rowData2[0][2];
            $product_name = $rowData2[0][3];
            $procuct_price = $rowData2[0][4];
            $qty = $rowData2[0][5];
            $total_price_item = $rowData2[0][6];

            $data2 = array(
                        'order_id' => $order_id,
                        'sku' => $sku,
                        'product_name' => $product_name,
                        'procuct_price' => $procuct_price,
                        'qty' => $qty,
                        'total_price_item' => $total_price_item,
                        'code' => $keygen

                    );
            $this->inwshop_item_data_model->insert($data2);
        }

        //export to excel
        $this->generateXls($keygen);

    }

 }

 public function generateXls($keygen) {

 	$arr_datas =$this->inwshop_data_model->select_by_code_join($keygen);
		// create file name
        $fileName = 'data-'.time().'.xlsx';  
		// load excel library
        $this->load->library('Lib_excel');
        //$listInfo = $this->export->exportList();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->createSheet(0);
        //$objPHPExcel->createSheet(1);
       // $objPHPExcel->createSheet(2);

        $objPHPExcel->setActiveSheetIndex(0);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');

        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'ชื่อผู้สั่งซื้อ');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'สถานะ');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'ชื่อสินค้า');      
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'รหัสสินค้า');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'ค่าสินค้า'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'ค่าส่ง'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'ส่วนลด'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Amount include vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Amount exclude Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Vat'); 
        // set Row

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);

        $rowCount = 2;

        $order_tmp = "";

        foreach($arr_datas as $arr_data){

        	$delivery =0;
        	$amount_include_vat =0;
        	$amount_exclude_vat =0;
            $vat = 0;
            $status = "";

            $order_id = $arr_data['order_id'];

            if($order_id != $order_tmp){

                $delivery = $arr_data['delivery'];
                $amount_include_vat = $arr_data['amount_include_vat'];
                $amount_exclude_vat = $arr_data['amount_exclude_vat'];
                $vat = $arr_data['vat'];
                $status = $arr_data['status'];
            }

            $ctime = date("Y-m-d H:i:s", strtotime($arr_data['ctime']));

        	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $ctime);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $arr_data['order_id']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $arr_data['cus_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $status);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $arr_data['product_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $arr_data['sku']);      
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $arr_data['total_price_item']);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $delivery);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $arr_data['discount']);
            $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $amount_include_vat);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $amount_exclude_vat);
            $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $vat);

            if($arr_data['order_id'] == $order_tmp){
                $roll_bfo = $rowCount-1;
                    if($arr_data['status'] == "ยกเลิก"){
                        $this->cellColor($objPHPExcel,'A'.$roll_bfo.':L'.$roll_bfo, 'f53a3a');
                        $this->cellColor($objPHPExcel,'A'.$rowCount.':L'.$rowCount, 'f53a3a');
                    }else{
                        $this->cellColor($objPHPExcel,'A'.$roll_bfo.':L'.$roll_bfo, '64f70b');
                        $this->cellColor($objPHPExcel,'A'.$rowCount.':L'.$rowCount, '64f70b');
                    }

                $order_tmp = $arr_data['order_id'];
            }else{
                $order_tmp = $arr_data['order_id'];
            }

            if($arr_data['status'] == "ยกเลิก"){
                $this->cellColor($objPHPExcel,'A'.$rowCount.':L'.$rowCount, 'f53a3a');
            }
            

	       $rowCount = $rowCount+1;
	        
        }

        $this->cellColor($objPHPExcel,'A1:L1', 'ffca2c');
        $objPHPExcel->getActiveSheet()->setTitle("Order detail");

//----------------sheet 2-------

        $arr_data2s =$this->inwshop_data_model->select_by_code($keygen);

        $objPHPExcel->setActiveSheetIndex(1);
        // set Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Created Time');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Order ID');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Name');       
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'สถานะ'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Amount exclude Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Vat'); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Amount include Vat'); 


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);


        $rowCount2 = 2;
        $order_tmp = "";
        foreach($arr_data2s as $arr_data2){

            $ctime2 = date("Y-m-d", strtotime($arr_data2['ctime']));

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount2, $ctime2);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount2, $arr_data2['order_id']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount2, $arr_data2['cus_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount2, $arr_data2['status']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount2, $arr_data2['amount_exclude_vat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount2, $arr_data2['vat']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount2, $arr_data2['amount_include_vat']);
            
            if($arr_data2['status'] == "ยกเลิก"){
                $this->cellColor($objPHPExcel,'A'.$rowCount2.':G'.$rowCount2, 'f53a3a');
            }

            $rowCount2 = $rowCount2+1;
            
        }

        $this->cellColor($objPHPExcel,'A1:G1', 'ffca2c');
        $objPHPExcel->getActiveSheet()->setTitle("Order detail finance");

        $filename = "inwshop_". date("Y-m-d-H-i-s").".xls";
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

 function create_textinvoiceid(){

       $array_status_death = array('ยกเลิก');


        $arr_taxs = $this->inwshop_data_model->select_by_status_last_arr($array_status_death,50);

        foreach($arr_taxs as $arr_tax){

            $arr_chk_or = $this->inwshop_taxinvoiceid_model->select_taxinvoiceid_by_orderno($arr_tax['order_id']);
            if(empty($arr_chk_or)){
                $arr_lastorder = $this->inwshop_taxinvoiceid_model->last_order_code_by_yymm($arr_tax['yyyymm']);   
                print_r($arr_lastorder);

               if(!empty($arr_lastorder)){

                 $new_textinvoiceID = $this->inwshop_bl->get_inwshop_code($arr_lastorder['taxinvoiceID'],$arr_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->inwshop_taxinvoiceid_model->insert($arr_new_invoice_id);


               }else{

                $new_textinvoiceID = $this->inwshop_bl->get_inwshop_code('no',$arr_tax['create_time']);  

                 $arr_new_invoice_id = array(
                  'order_id' => $arr_tax['order_id'],
                  'taxinvoiceID' => $new_textinvoiceID,
                  'create_time' => $arr_tax['create_time']
                 );

                 print_r($arr_new_invoice_id);

                 $this->inwshop_taxinvoiceid_model->insert($arr_new_invoice_id);

               }
            }

       }
    }

  

}