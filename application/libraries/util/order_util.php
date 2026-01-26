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
				//$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				$discount=$suborder["voucher"];
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'price'=>$suborder["item_price"]
								    );
				$runer++;



			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{

                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
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
					//$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$discount=$suborder["voucher"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];

	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
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
			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];

				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];
				$amount = $suborder["item_price"]*$suborder["qty"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
										'amount'=>$suborder["item_price"],
								    );
				$runer++;


			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{
					$amount = $suborder["item_price"]*$suborder["qty"];
                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
										'amount'=>$suborder["item_price"],
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
					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];
	

					$amount = $suborder["item_price"]*$suborder["qty"];


	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
										'amount'=>$suborder["item_price"],
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
			if($runer==0) // first record of each order-> collect order data
			{
				$orders[$main_runer]["order_number"]=$suborder["order_number"];
				$orders[$main_runer]["created_at"]=$suborder["created_at"];
				$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];

				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];
				$amount = $suborder["item_price"]*$suborder["qty"];

				$order_number=$suborder["order_number"];


				unset($suborder_detail);
				$suborder_detail=array();
				$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
									'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
										'amount'=>$amount,
								    );
				$runer++;


			}
			else
			{
				if($suborder["order_number"]==$order_number) //still in the same order
				{
					$amount = $suborder["item_price"]*$suborder["qty"];
                  $suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
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
					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];
	

					$amount = $suborder["item_price"]*$suborder["qty"];


	            
				    unset($suborder_detail);	
					$suborder_detail=array();
					$suborder_detail[$runer]=array('ProductName'=>$suborder["ProductName"],
										'item_price'=>$suborder["item_price"],
										'qty'=>$suborder["qty"],
										'item_price'=>$suborder["item_price"],
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
				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				$orders[$main_runer]["discount"]=$discount;
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
					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
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

				if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
				}else{
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
				}

				$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				$orders[$main_runer]["discount"]=$discount;
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

					if($suborder["FullTaxinvoiceID"] != ""){
					$orders[$main_runer]["taxinvoiceID"]=$suborder["FullTaxinvoiceID"];
					}else{
						$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					}
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
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

           return $orders;

	} // end function

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


}