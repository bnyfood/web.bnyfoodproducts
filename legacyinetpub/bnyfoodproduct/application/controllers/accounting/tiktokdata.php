<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tiktokdata extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('util/random_util');
		$this->load->model('tiktok_data_model');
		$this->auth_bl->check_session_exists();
	}

	public function tiktokdata_list()
	{
		$data = array(
			'import_alt' => $this->session->flashdata('import_tiktok')
		);

		$arr_input = array(
			'title' => 'TikTok Data'
		);

		$arr_css = array(
			'site_new' => base_url() . 'assets/css/site_new.css'
		);

		$this->view_util->load_view_main('accounting/tiktokdata/tiktokdata_list', $data, $arr_css, NULL, $arr_input, MENU_ACCOUNT);
	}

	private function explode_thb($data)
	{
		$baht = 0;
		if (!empty($data)) {
			$exp = explode(' ', $data);
			if (!empty($exp[1])) {
				$t1 = str_replace(',', '', $exp[1]);
				$baht = (float) $t1;
			} else {
				$baht = (float) $data;
			}
		}

		return $baht;
	}

	public function tiktok_import_data_action()
	{
		$this->load->library('Upload_secure', array(
			'psp_inbox_dir' => 'C:\\inetpub\\storage\\bnyfoodproducts\\uploads\\xls'
		));

		$res = $this->upload_secure->upload_file('upload_file1');
		$is_success = false;
		$keygen = '';

		if (!empty($res['is_upload']) && (int) $res['is_upload'] === 1) {
			$file_s = APP_STORE_PATH . '/uploads/xls/' . $res['file_name'];
			$mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

			if (!empty($_FILES['upload_file1']['type']) && in_array($_FILES['upload_file1']['type'], $mimes, TRUE)) {
				$this->load->library('Lib_excel');

				try {
					$input_file_type = PHPExcel_IOFactory::identify($file_s);
					$obj_reader = PHPExcel_IOFactory::createReader($input_file_type);
					$obj_php_excel = $obj_reader->load($file_s);

					$sheet = $obj_php_excel->getSheet(0);
					$highest_row = $sheet->getHighestRow();
					$highest_column = $sheet->getHighestColumn();

					$keygen = $this->random_util->create_random_number(8);
					$order_tmp = '';

					for ($row = 3; $row <= $highest_row; $row++) {
						$row_data = $sheet->rangeToArray('A' . $row . ':' . $highest_column . $row, NULL, TRUE, FALSE);

						$ctime = $row_data[0][24];
						$order_id = $row_data[0][0];
						$order_status = $row_data[0][1];
						$cancel_type = $row_data[0][3];

						if ($cancel_type == NULL) {
							$cancel_type = '';
						}

						$products = $row_data[0][6];
						$quantity = $row_data[0][9];
						$sku_unit_original_price = $row_data[0][11];
						$sku_seller_discount = $row_data[0][14];
						$subtotal_after_discount = $this->explode_thb($row_data[0][15]);
						$shipping_fee_after_discount = $this->explode_thb($row_data[0][16]);
						$original_shipping_fee = $this->explode_thb($row_data[0][17]);
						$shipping_fee_platform_discount = $this->explode_thb($row_data[0][19]);
						$small_order_fee = '';
						$order_refund_amount = $this->explode_thb($row_data[0][23]);

						$order_amount = ($sku_unit_original_price - $sku_seller_discount) + $shipping_fee_after_discount;
						$amount_exclude_vat = $order_amount / 1.07;
						$vat = $order_amount - $amount_exclude_vat;

						$date = str_replace('/', '-', $ctime);
						$date_to_db = date('Y/m/d H:i:s', strtotime($date));

						$paid_time = $row_data[0][25];
						$rts_time = $row_data[0][26];
						$shipped_time = $row_data[0][27];

						$date26 = str_replace('/', '-', $paid_time);
						$date_to_db26 = date('Y/m/d H:i:s', strtotime($date26));

						$date27 = str_replace('/', '-', $rts_time);
						$date_to_db27 = date('Y/m/d H:i:s', strtotime($date27));

						$date28 = str_replace('/', '-', $shipped_time);
						$date_to_db28 = date('Y/m/d H:i:s', strtotime($date28));

						$cancelled_time = $row_data[0][29];
						$date30 = str_replace('/', '-', $cancelled_time);
						$date_to_db30 = date('Y/m/d H:i:s', strtotime($date30));

						$cancel_reason = $row_data[0][31];
						$tracking_id = $row_data[0][34];

						if ($order_id != $order_tmp) {
							$data = array(
								'ctime' => $date_to_db,
								'order_id' => $order_id,
								'order_status' => $order_status,
								'cancel_type' => $cancel_type,
								'products' => $products,
								'quantity' => $quantity,
								'SKUUnitOriginalPrice' => $sku_unit_original_price,
								'SKUSellerDiscount' => $sku_seller_discount,
								'SubtotalAfterDiscount' => $subtotal_after_discount,
								'ShippingFeeAfterDiscount' => $shipping_fee_after_discount,
								'OriginalShippingFee' => $original_shipping_fee,
								'ShippingFeePlatformDiscount' => $shipping_fee_platform_discount,
								'SmallOrderFee' => $small_order_fee,
								'OrderAmount' => $order_amount,
								'OrderRefundAmount' => $order_refund_amount,
								'AmountExcludeVat' => round($amount_exclude_vat, 2),
								'Vat' => round($vat, 2),
								'PaidTime' => $date_to_db26,
								'RTSTime' => $date_to_db27,
								'ShippedTime' => $date_to_db28,
								'CancelledTime' => $date_to_db30,
								'CancelReason' => $cancel_reason,
								'TrackingID' => $tracking_id,
								'code' => $keygen
							);

							$this->tiktok_data_model->insert($data);
							$order_tmp = $order_id;
						} else {
							$data = array(
								'ctime' => $date_to_db,
								'order_id' => $order_id,
								'order_status' => $order_status,
								'cancel_type' => $cancel_type,
								'products' => $products,
								'quantity' => $quantity,
								'SKUUnitOriginalPrice' => $sku_unit_original_price,
								'SKUSellerDiscount' => $sku_seller_discount,
								'SubtotalAfterDiscount' => $subtotal_after_discount,
								'ShippingFeeAfterDiscount' => 0,
								'OriginalShippingFee' => $original_shipping_fee,
								'ShippingFeePlatformDiscount' => $shipping_fee_platform_discount,
								'SmallOrderFee' => $small_order_fee,
								'OrderAmount' => 0,
								'OrderRefundAmount' => 0,
								'AmountExcludeVat' => 0,
								'Vat' => 0,
								'PaidTime' => $date_to_db26,
								'RTSTime' => $date_to_db27,
								'ShippedTime' => $date_to_db28,
								'CancelledTime' => $date_to_db30,
								'CancelReason' => $cancel_reason,
								'TrackingID' => $tracking_id,
								'code' => $keygen
							);

							$this->tiktok_data_model->insert($data);
						}
					}

					$is_success = true;
				} catch (Exception $e) {
					$is_success = false;
				}
			}
		}

		if ($is_success) {
			$this->generateXls($keygen);
			return;
		}

		$this->session->set_flashdata('import_tiktok', 'fail');
		redirect(base_url() . 'accounting/tiktokdata/tiktokdata_list', 'refresh');
	}

	public function generateXls($keygen)
	{
		$arr_datas = $this->tiktok_data_model->select_by_code($keygen);
		$this->load->library('Lib_excel');
		$objPHPExcel = new PHPExcel();

		$objPHPExcel->createSheet();
		$objPHPExcel->createSheet(0);

		$objPHPExcel->setActiveSheetIndex(0);
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

		foreach (range('A', 'V') as $col) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(25);
		}

		$rowCount = 3;
		$order_tmp = '';
		foreach ($arr_datas as $arr_data) {
			$CancelledTime = '';
			$CancelReason = '';
			$TrackingID = '';

			$PaidTime = $arr_data['PaidTime'];
			$RTSTime = $arr_data['RTSTime'];
			$ShippedTime = $arr_data['ShippedTime'];

			if ($arr_data['order_status'] == 'Canceled') {
				$CancelledTime = $arr_data['CancelledTime'];
				$CancelReason = $arr_data['CancelReason'];
				$TrackingID = $arr_data['TrackingID'];
				$PaidTime = '';
				$RTSTime = '';
				$ShippedTime = '';
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $arr_data['ctime']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount, $arr_data['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
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

			if ($arr_data['order_id'] == $order_tmp) {
				$roll_bfo = $rowCount - 1;
				$this->cellColor($objPHPExcel, 'A' . $roll_bfo . ':S' . $roll_bfo, '64f70b');
				$this->cellColor($objPHPExcel, 'A' . $rowCount . ':S' . $rowCount, '64f70b');
				$order_tmp = $arr_data['order_id'];
			} else {
				$order_tmp = $arr_data['order_id'];
			}

			$rowCount = $rowCount + 1;
		}

		$this->cellColor($objPHPExcel, 'A1:V1', 'ffca2c');
		$this->cellColor($objPHPExcel, 'A2:V2', 'ffca2c');
		$objPHPExcel->getActiveSheet()->setTitle('Order detail');

		$objPHPExcel->setActiveSheetIndex(1);
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

		foreach (range('A', 'J') as $col) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(25);
		}

		$rowCount2 = 3;
		$order_tmp = '';
		foreach ($arr_datas as $arr_data) {
			$CancelledTime = '';
			$CancelReason = '';
			$TrackingID = '';

			$PaidTime = $arr_data['PaidTime'];
			$RTSTime = $arr_data['RTSTime'];
			$ShippedTime = $arr_data['ShippedTime'];

			$insert_date = true;

			if ($arr_data['order_status'] == 'Canceled') {
				$CancelledTime = $arr_data['CancelledTime'];
				$CancelReason = $arr_data['CancelReason'];
				$TrackingID = $arr_data['TrackingID'];
				$PaidTime = '';
				$RTSTime = '';
				$ShippedTime = '';

				if ($TrackingID == '') {
					$insert_date = false;
				}
			}

			if ($insert_date) {
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount2, $arr_data['ctime']);
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount2, $arr_data['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount2, $arr_data['products']);
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount2, $arr_data['quantity']);
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount2, $arr_data['SubtotalAfterDiscount']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount2, $arr_data['ShippingFeeAfterDiscount']);
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount2, $arr_data['SmallOrderFee']);
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount2, $arr_data['OrderAmount']);
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount2, $arr_data['Vat']);
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount2, $arr_data['AmountExcludeVat']);

				if ($arr_data['order_id'] == $order_tmp) {
					$roll_bfo = $rowCount2 - 1;
					$this->cellColor($objPHPExcel, 'A' . $roll_bfo . ':J' . $roll_bfo, '64f70b');
					$this->cellColor($objPHPExcel, 'A' . $rowCount2 . ':J' . $rowCount2, '64f70b');
					$order_tmp = $arr_data['order_id'];
				} else {
					$order_tmp = $arr_data['order_id'];
				}

				$rowCount2 = $rowCount2 + 1;
			}
		}

		$this->cellColor($objPHPExcel, 'A1:J1', 'ffca2c');
		$this->cellColor($objPHPExcel, 'A2:J2', 'ffca2c');
		$objPHPExcel->getActiveSheet()->setTitle('Order detail filter');

		$objPHPExcel->setActiveSheetIndex(2);
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

		foreach (range('A', 'S') as $col) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(25);
		}

		$rowCount3 = 3;
		$order_tmp = '';

		$arr_cancel_datas = $this->tiktok_data_model->select_by_code_status_trackid($keygen, 'Canceled');
		foreach ($arr_cancel_datas as $arr_data) {
			$CancelledTime = $arr_data['CancelledTime'];
			$CancelReason = $arr_data['CancelReason'];
			$TrackingID = $arr_data['TrackingID'];

			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount3, $arr_data['ctime']);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $rowCount3, $arr_data['order_id'], PHPExcel_Cell_DataType::TYPE_STRING);
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

			if ($arr_data['order_id'] == $order_tmp) {
				$roll_bfo = $rowCount3 - 1;
				$this->cellColor($objPHPExcel, 'A' . $roll_bfo . ':S' . $roll_bfo, '64f70b');
				$this->cellColor($objPHPExcel, 'A' . $rowCount3 . ':S' . $rowCount3, '64f70b');
				$order_tmp = $arr_data['order_id'];
			} else {
				$order_tmp = $arr_data['order_id'];
			}

			$rowCount3 = $rowCount3 + 1;
		}

		$this->cellColor($objPHPExcel, 'A1:S1', 'ffca2c');
		$this->cellColor($objPHPExcel, 'A2:S2', 'ffca2c');
		$objPHPExcel->getActiveSheet()->setTitle('Order Cancel');

		$filename = 'tiktok_' . date('Y-m-d-H-i-s') . '.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	private function cellColor($objPHPExcel, $cells, $color)
	{
		$objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => $color
			)
		));
	}
}
