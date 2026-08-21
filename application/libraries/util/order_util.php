<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class order_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->library("util/date_util");
	}
    


	function getStartEndDate($startEndDate,$SorE)
	{
		$arr=explode("hp",$startEndDate);


		$pattern = '/sp/i';
        $arr[0] =preg_replace($pattern, '', $arr[0]);
        $arr[1] =preg_replace($pattern, '', $arr[1]);

        		$pattern = '/sl/i';
        $arr[0] =preg_replace($pattern, '/', $arr[0]);
        $arr[1] =preg_replace($pattern, '/', $arr[1]);




		switch($SorE)
		{
		case "S":

		return $arr[0];
		

		break;

		case "E":

        return $arr[1];
         
		break;

		}




	}


	function  getOrdersFromOdersOderItems($orders_orderitems)
	{

$orders=array();
$main_runer=0;
$runer=0;
$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
				$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
				//$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				$discount=$suborder["voucher"];
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'sku'=>isset($suborder["sku"]) ? $suborder["sku"] : '',
									'price'=>$suborder["item_price"]
								    );
				$runer++;



			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{

                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'sku'=>isset($suborder["sku"]) ? $suborder["sku"] : '',
									'price'=>$suborder["item_price"]
								    );




				  $runer++;					



				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
					$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
					//$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$discount=$suborder["voucher"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>isset($suborder["sku"]) ? $suborder["sku"] : '',
										'price'=>$suborder["item_price"]
									    );
					$runer++;

				} // entering new record


			}

           


			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $orders;

	} // end function

	function  ShopeegetOrdersFromOdersOderItems($orders_orderitems)
	{

		$orders=array();
		$main_runer=0;
		$runer=0;
		$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
			$item_price = (float)$suborder["item_price"];
			$qty = (float)$suborder["qty"];
			$amount = $item_price * $qty;
			$sku = isset($suborder["sku"]) ? $suborder["sku"] : '';

			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
				$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
				$orders[$main_runer]["seller_discount"]=$seller_discount;

				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'sku'=>$sku,
									'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );
				$runer++;


			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{
                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );


				  $runer++;					


				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
					$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
					$orders[$main_runer]["seller_discount"]=$seller_discount;

					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
									    );
					$runer++;

				} // entering new record


			}

			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $orders;

	} // end function

	function  TiktokgetOrdersFromOdersOderItems($orders_orderitems)
	{

		$orders=array();
		$main_runer=0;
		$runer=0;
		$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
			$item_price = (float)$suborder["item_price"];
			$qty = (float)$suborder["qty"];
			$amount = $item_price * $qty;
			$sku = isset($suborder["sku"]) ? $suborder["sku"] : '';
			$is_tracking = isset($suborder["is_tracking"]) ? $suborder["is_tracking"] : 1;

			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["is_tracking"]=$is_tracking;
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
				$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
				$orders[$main_runer]["seller_discount"]=$seller_discount;

				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'sku'=>$sku,
									'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );
				$runer++;


			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{
                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );



				  $runer++;					



				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["is_tracking"]=$is_tracking;
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$orders[$main_runer]["voucher_platform"]=$suborder["voucher_platform"];
					$orders[$main_runer]["voucher_seller"]=$suborder["voucher_seller"];
					$orders[$main_runer]["seller_discount"]=$seller_discount;

					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];
	

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
									    );
					$runer++;

				} // entering new record


			}

			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $orders;

	} // end function

	function  BiggrillgetOrdersFromOdersOderItems($orders_orderitems)
	{

		$orders=array();
		$main_runer=0;
		$runer=0;
		$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
			$item_price = (float)$suborder["item_price"];
			$qty = (float)$suborder["qty"];
			$amount = $item_price * $qty;
			$sku = isset($suborder["sku"]) ? $suborder["sku"] : '';
			$is_tracking = isset($suborder["is_tracking"]) ? $suborder["is_tracking"] : 1;
			$voucher_platform = isset($suborder["voucher_platform"]) ? $suborder["voucher_platform"] : 0;

			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["is_tracking"]=$is_tracking;
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$orders[$main_runer]["voucher_platform"]=$voucher_platform;
				$orders[$main_runer]["seller_discount"]=$seller_discount;
				

				$discount=$voucher_platform+$seller_discount;
				
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'sku'=>$sku,
									'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );
				$runer++;


			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{
                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
								    );



				  $runer++;					



				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["is_tracking"]=$is_tracking;
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$orders[$main_runer]["voucher_platform"]=$voucher_platform;
					$orders[$main_runer]["seller_discount"]=$seller_discount;

					$discount=$voucher_platform+$seller_discount;
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'sku'=>$sku,
										'item_price'=>$item_price,
										'qty'=>$qty,
										'amount'=>$amount,
									    );
					$runer++;

				} // entering new record


			}

			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $orders;

	} // end function

	function  getOrdersFromOdersOderItemsCN($orders_orderitems)
	{

		$orders=array();
		$main_runer=0;
		$runer=0;
		$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			$do_it = true;
	          $date_diff_status = $this->CI->date_util->date_diff($suborder["created_at"],$suborder["updated_at"]);
	          if($suborder['latest_status'] == "canceled"){
	            $do_it = false;
	            //1800 = 30 hour
	            if($date_diff_status > 1800){
	              $do_it = true;
	            }
	          }
	       if($do_it){  
			if($runer==0) // first record of each order-> collect order data
			{	
				
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["updated_at"]=$suborder["updated_at"];

				if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
				}else{
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				}

				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
				$orders[$main_runer]["seller_discount"]=$seller_discount;
				$orders[$main_runer]["voucher_seller"]=isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : $seller_discount;
				$orders[$main_runer]["voucher_platform"]=isset($suborder["voucher_platform"]) ? $suborder["voucher_platform"] : 0;
				$orders[$main_runer]["discount"]=$seller_discount;
				$orders[$main_runer]["price"]=$suborder["price"];
				$orders[$main_runer]["order_status"]=$suborder["order_status"];

				$order_number=$suborder["order_number"];

				$orders[$main_runer]["TaxNo"]=$suborder["TaxNo"];
				$orders[$main_runer]["customer_name"]=$suborder["customer_name"];
				$orders[$main_runer]["customer_phone"]=$suborder["customer_phone"];
				$orders[$main_runer]["customer_zip"]=$suborder["customer_zip"];
				$orders[$main_runer]["address1"]=$suborder["address1"];
				$orders[$main_runer]["address2"]=$suborder["address2"];

				$orders[$main_runer]["latest_status"]=$suborder["latest_status"];
				$orders[$main_runer]["total_refund_val"]=$suborder["total_refund_val"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'price'=>$suborder["paid_price"]
								    );
				$runer++;



			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{

                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'price'=>$suborder["paid_price"]
								    );




				  $runer++;					



				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["updated_at"]=$suborder["updated_at"];

					if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
					}else{
						$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					}
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
					$orders[$main_runer]["seller_discount"]=$seller_discount;
					$orders[$main_runer]["voucher_seller"]=isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : $seller_discount;
					$orders[$main_runer]["voucher_platform"]=isset($suborder["voucher_platform"]) ? $suborder["voucher_platform"] : 0;
					$orders[$main_runer]["discount"]=$seller_discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$orders[$main_runer]["order_status"]=$suborder["order_status"];
					$order_number=$suborder["order_number"];

					$orders[$main_runer]["TaxNo"]=$suborder["TaxNo"];
					$orders[$main_runer]["customer_name"]=$suborder["customer_name"];
					$orders[$main_runer]["customer_phone"]=$suborder["customer_phone"];
					$orders[$main_runer]["customer_zip"]=$suborder["customer_zip"];
					$orders[$main_runer]["address1"]=$suborder["address1"];
					$orders[$main_runer]["address2"]=$suborder["address2"];

					$orders[$main_runer]["latest_status"]=$suborder["latest_status"];
					$orders[$main_runer]["total_refund_val"]=$suborder["total_refund_val"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
						'price'=>$suborder["paid_price"]
				    );
					$runer++;

				} // entering new record


			}

           
			}else{
				$runer++;
			}

			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $orders;

	} // end function

	function make_cn_no($arr_orders){
		$next_month = "";
		if(count($arr_orders) > 0 ){
			$num = 0;
			foreach($arr_orders as $order){
				$yymm_ex1 = explode(' ',$order['updated_at']);
				$yymm_ex2 = explode('-',$yymm_ex1[0]);
				$yymm1 = $yymm_ex2[0]."-".$yymm_ex2[1];

				if($num == 0){
					$start_yymm = $yymm1;
					$start_num = 1;
					$arr_explode = explode('-',$start_yymm);
					$yy = $arr_explode[0];
					$mm = $arr_explode[1];
					//echo "1>>".$start_num."<br>";
				}

				//echo "num->".$num.">>".$start_yymm."<br>";
				//echo "created_at->".$yymm1.">>".$start_yymm."<br>";

				//$yymm_ex_n1 = explode(' ',$order['created_at']);
				//$yymm_ex_n2 = explode('-',$yymm_ex_n1[0]);
				//$yymm_n1 = $yymm_ex_n2[0]."-".$yymm_ex_n2[1];

				if($yymm1 != $start_yymm){
					$start_yymm = $yymm1;
					$start_num = 1;
					$arr_explode = explode('-',$start_yymm);
					$yy = $arr_explode[0];
					$mm = $arr_explode[1];
					//echo "2>>".$start_num."<br>";
				}
				

				
				$run_num = $this->add_font_digi($start_num ,5);
				//echo "3>>".$run_num."<br>";
				$cncode = "CNLAZ".$yy.$mm.$run_num;
				$arr_orders[$num]['cncode'] = $cncode;
				//echo $cncode."<br>";
				$start_num = $start_num +1;
				$num = $num+1;
				
			}
		}

		return $arr_orders;
	}

	function  getOrdersFromOdersOderItemsCNShopee($orders_orderitems)
	{

		$orders=array();
		$main_runer=0;
		$runer=0;
		$order_number=0;
		foreach($orders_orderitems as $suborder)
		{
			if($runer==0) // first record of each order-> collect order data
			{	
				
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["updated_at"]=$suborder["updated_at"];
				if (!empty($suborder["cn_event_at"])) {
					$orders[$main_runer]["cn_event_at"]=$suborder["cn_event_at"];
					$orders[$main_runer]["updated_at"]=$suborder["cn_event_at"];
				}

				if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
				}else{
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				}

				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
				$orders[$main_runer]["seller_discount"]=$seller_discount;
				$orders[$main_runer]["voucher_seller"]=isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : $seller_discount;
				$orders[$main_runer]["voucher_platform"]=isset($suborder["voucher_platform"]) ? $suborder["voucher_platform"] : 0;
				$orders[$main_runer]["discount"]=$seller_discount;
				$orders[$main_runer]["price"]=$suborder["price"];
				$orders[$main_runer]["order_status"]=$suborder["order_status"];

				$order_number=$suborder["order_number"];

				$orders[$main_runer]["TaxNo"]=$suborder["TaxNo"];
				$orders[$main_runer]["customer_name"]=$suborder["customer_name"];
				$orders[$main_runer]["customer_phone"]=$suborder["customer_phone"];
				$orders[$main_runer]["customer_zip"]=$suborder["customer_zip"];
				$orders[$main_runer]["address1"]=$suborder["address1"];
				$orders[$main_runer]["address2"]=$suborder["address2"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'price'=>$suborder["paid_price"]
								    );
				$runer++;



			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{

                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'price'=>$suborder["paid_price"]
								    );




				  $runer++;					



				}
				else //entering new order record

				{
					$orders[$main_runer]["suborder"]=$suborder_detail;

	                $main_runer++;	
					$runer=0;
					
					$orders[$main_runer]["order_number"]=$suborder["order_number"];
					$orders[$main_runer]["created_at"]=$suborder["created_at"];
					$orders[$main_runer]["updated_at"]=$suborder["updated_at"];
					if (!empty($suborder["cn_event_at"])) {
						$orders[$main_runer]["cn_event_at"]=$suborder["cn_event_at"];
						$orders[$main_runer]["updated_at"]=$suborder["cn_event_at"];
					}

					if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
					}else{
						$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					}
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$seller_discount = isset($suborder["seller_discount"]) ? $suborder["seller_discount"] : (isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : 0);
					$orders[$main_runer]["seller_discount"]=$seller_discount;
					$orders[$main_runer]["voucher_seller"]=isset($suborder["voucher_seller"]) ? $suborder["voucher_seller"] : $seller_discount;
					$orders[$main_runer]["voucher_platform"]=isset($suborder["voucher_platform"]) ? $suborder["voucher_platform"] : 0;
					$orders[$main_runer]["discount"]=$seller_discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$orders[$main_runer]["order_status"]=$suborder["order_status"];
					$order_number=$suborder["order_number"];

					$orders[$main_runer]["TaxNo"]=$suborder["TaxNo"];
					$orders[$main_runer]["customer_name"]=$suborder["customer_name"];
					$orders[$main_runer]["customer_phone"]=$suborder["customer_phone"];
					$orders[$main_runer]["customer_zip"]=$suborder["customer_zip"];
					$orders[$main_runer]["address1"]=$suborder["address1"];
					$orders[$main_runer]["address2"]=$suborder["address2"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'price'=>$suborder["paid_price"]
									    );
					$runer++;

				} // entering new record


			}

           


			} //foreach

           $orders[$main_runer]["suborder"]=$suborder_detail;

           return $this->align_shopee_cn_with_tax_report($orders);

	} // end function

	/**
	 * Same goods/seller/shipping as รายงานภาษีขาย:
	 * มูลค่าสินค้า = escrow original_price (ราคาก่อนป้ายเหลือง)
	 * ราคารวม VAT = original_price − seller_discount + shipping.
	 */
	function align_shopee_cn_with_tax_report($orders)
	{
		if (empty($orders)) {
			return $orders;
		}
		$this->CI->load->model('shopee_orders_model');
		$sns = array();
		foreach ($orders as $o) {
			if (!empty($o['order_number'])) {
				$sns[] = $o['order_number'];
			}
		}
		$map = $this->CI->shopee_orders_model->get_escrow_tax_map_by_order_sns($sns);
		$item_map = $this->CI->shopee_orders_model->get_orderitem_original_map_by_order_sns($sns);
		$escrow_ids = array();
		foreach ($map as $escrow_row) {
			if (isset($escrow_row['EscrowID']) && $escrow_row['EscrowID'] !== '' && $escrow_row['EscrowID'] !== null) {
				$escrow_ids[] = $escrow_row['EscrowID'];
			}
		}
		$escrow_item_map = $this->CI->shopee_orders_model->get_escrow_item_original_map_by_escrow_ids($escrow_ids);
		foreach ($orders as $i => $o) {
			$sn = isset($o['order_number']) ? $o['order_number'] : '';
			if ($sn === '') {
				continue;
			}
			$esc = isset($map[$sn]) ? $map[$sn] : array();
			$orig = isset($esc['original_price']) ? floatval($esc['original_price']) : 0;
			$cogs = isset($esc['original_cost_of_goods_sold']) ? floatval($esc['original_cost_of_goods_sold']) : 0;
			$escrow_line = 0;
			if (isset($esc['EscrowID']) && isset($escrow_item_map[$esc['EscrowID']])) {
				$escrow_line = floatval($escrow_item_map[$esc['EscrowID']]);
			}
			$item_goods = isset($item_map[$sn]) ? floatval($item_map[$sn]) : 0;
			$seller = isset($esc['seller_discount']) ? floatval($esc['seller_discount']) : 0;
			if ($seller == 0.0 && isset($esc['voucher_from_seller'])) {
				$seller = floatval($esc['voucher_from_seller']);
			}
			$goods = 0;
			$seller_is_net = false;
			$goods_from_income = false;
			if ($orig > 0.00001) {
				$goods = $orig;
				$goods_from_income = true;
			} elseif ($cogs > 0.00001) {
				$goods = $cogs;
				$seller_is_net = true;
				$goods_from_income = true;
			} elseif ($escrow_line > 0.00001) {
				$goods = $escrow_line;
			} elseif ($item_goods > 0.00001) {
				$goods = $item_goods;
			}
			if ($goods > 0.00001) {
				$orders[$i]['price'] = $goods;
				if ($goods_from_income && isset($esc['buyer_paid_shipping_fee']) && $esc['buyer_paid_shipping_fee'] !== '' && $esc['buyer_paid_shipping_fee'] !== null) {
					$orders[$i]['shipping_fee'] = floatval($esc['buyer_paid_shipping_fee']);
				}
				if (!empty($orders[$i]['suborder'])) {
					$n = count($orders[$i]['suborder']);
					if ($n == 1) {
						$k = key($orders[$i]['suborder']);
						$orders[$i]['suborder'][$k]['price'] = $goods;
					} else {
						$sum = 0;
						foreach ($orders[$i]['suborder'] as $line) {
							$sum = $sum + floatval($line['price']);
						}
						if ($sum > 0) {
							foreach ($orders[$i]['suborder'] as $k => $line) {
								$orders[$i]['suborder'][$k]['price'] = floatval($line['price']) / $sum * $goods;
							}
						}
					}
				}
			}
			if ($seller_is_net) {
				$seller = 0;
			}
			if (!empty($esc) || $seller_is_net) {
				$orders[$i]['seller_discount'] = $seller;
				$orders[$i]['voucher_seller'] = $seller;
				$orders[$i]['discount'] = $seller;
			}
		}
		return $orders;
	}

	function make_cn_no_shopee($arr_orders){
		$next_month = "";
		if(count($arr_orders) > 0 ){
			$num = 0;
			foreach($arr_orders as $order){
				$yymm_ex1 = explode(' ',$order['updated_at']);
				$yymm_ex2 = explode('-',$yymm_ex1[0]);
				$yymm1 = $yymm_ex2[0]."-".$yymm_ex2[1];

				if($num == 0){
					$start_yymm = $yymm1;
					$start_num = 1;
					$arr_explode = explode('-',$start_yymm);
					$yy = $arr_explode[0];
					$mm = $arr_explode[1];
					//echo "1>>".$start_num."<br>";
				}

				//echo "num->".$num.">>".$start_yymm."<br>";
				//echo "created_at->".$yymm1.">>".$start_yymm."<br>";

				//$yymm_ex_n1 = explode(' ',$order['created_at']);
				//$yymm_ex_n2 = explode('-',$yymm_ex_n1[0]);
				//$yymm_n1 = $yymm_ex_n2[0]."-".$yymm_ex_n2[1];

				if($yymm1 != $start_yymm){
					$start_yymm = $yymm1;
					$start_num = 1;
					$arr_explode = explode('-',$start_yymm);
					$yy = $arr_explode[0];
					$mm = $arr_explode[1];
					//echo "2>>".$start_num."<br>";
				}
				

				
				$run_num = $this->add_font_digi($start_num ,5);
				//echo "3>>".$run_num."<br>";
				$cncode = "CNSHOPEE".$yy.$mm.$run_num;
				$arr_orders[$num]['cncode'] = $cncode;
				//echo $cncode."<br>";
				$start_num = $start_num +1;
				$num = $num+1;
				
			}
		}

		return $arr_orders;
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


	function skumap($sku)
	{
		$arr=$this->skumap_model->getSkuMap();
	}

	/**
	 * Tax / CN amounts.
	 * Shopee/TikTok: ราคารวม VAT = มูลค่าสินค้า − ส่วนลดร้านค้า + ค่าขนส่ง (ไม่หักส่วนลดแพลตฟอร์ม).
	 * Lazada: same without ค่าขนส่ง in the tax base.
	 * VAT is taken from the inclusive amount (/ 1.07).
	 */
	function cn_tax_amounts($row, $include_shipping = false)
	{
		$CI =& get_instance();
		$legacy = (isset($CI->report_cutover) && $CI->report_cutover->use_legacy());
		if ($legacy && isset($row['ValueBeforeVAT']) && $row['ValueBeforeVAT'] !== '' && $row['ValueBeforeVAT'] !== null) {
			$incl = floatval($row['ValueBeforeVAT']);
			$excl = isset($row['priceBeforeVAT']) ? floatval($row['priceBeforeVAT']) : ($incl / 1.07);
			if (!isset($row['priceBeforeVAT']) && isset($row['VAT'])) {
				$excl = $incl - floatval($row['VAT']);
			} elseif (!isset($row['priceBeforeVAT'])) {
				$excl = $incl / 1.07;
			}
			$vat = isset($row['VAT']) ? floatval($row['VAT']) : ($incl - $excl);
			return array(
				'price' => isset($row['price']) ? floatval($row['price']) : $incl,
				'seller_discount' => isset($row['seller_discount']) ? floatval($row['seller_discount']) : (isset($row['voucher_seller']) ? floatval($row['voucher_seller']) : 0),
				'shipping_fee' => $include_shipping && isset($row['shipping_fee']) ? floatval($row['shipping_fee']) : 0,
				'vatincl' => $incl,
				'vatexcl' => $excl,
				'vat' => $vat,
			);
		}

		$price = isset($row['price']) ? floatval($row['price']) : 0;
		$seller = 0;
		if (isset($row['seller_discount']) && $row['seller_discount'] !== '' && $row['seller_discount'] !== null) {
			$seller = floatval($row['seller_discount']);
		} elseif (isset($row['voucher_seller'])) {
			$seller = floatval($row['voucher_seller']);
		}
		$shipping = isset($row['shipping_fee']) ? floatval($row['shipping_fee']) : 0;

		$has_price = array_key_exists('price', $row) && $row['price'] !== '' && $row['price'] !== null;
		// Grouped daily rows always set ValueBeforeVAT (often 0). Do not treat that as
		// the amount when price/seller/shipping from the per-order rows are the source.
		$use_fallback = abs($price) < 0.00001 && abs($seller) < 0.00001 && abs($shipping) < 0.00001
			&& isset($row['ValueBeforeVAT']);
		$use_price = $has_price && !$use_fallback;
		if ($use_price) {
			$incl = $price - $seller;
			if ($include_shipping) {
				$incl = $incl + $shipping;
			}
		} else {
			$incl = isset($row['ValueBeforeVAT']) ? floatval($row['ValueBeforeVAT']) : 0;
			if (!$include_shipping && isset($row['shipping_fee'])) {
				$incl = $incl - $shipping;
			}
		}

		$excl = $incl / 1.07;
		$vat = $incl - $excl;
		return array(
			'price' => $price,
			'seller_discount' => $seller,
			'shipping_fee' => $include_shipping ? $shipping : 0,
			'vatincl' => $incl,
			'vatexcl' => $excl,
			'vat' => $vat,
		);
	}

}
