<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class order_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
		
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
				$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
				$orders[$main_runer]["discount"]=$discount;
				$orders[$main_runer]["price"]=$suborder["price"];

				$order_number=$suborder["order_number"];


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
					$orders[$main_runer]["taxinvoiceID"]=$suborder["taxinvoiceID"];
					$orders[$main_runer]["shipping_fee"]=$suborder["shipping_fee"];
					$discount=$suborder["voucher_platform"]+$suborder["voucher_seller"];
					$orders[$main_runer]["discount"]=$discount;
					$orders[$main_runer]["price"]=$suborder["price"];
					$order_number=$suborder["order_number"];

	            
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

   function skumap($sku)
   {


     $arr=$this->skumap_model->getSkuMap();


 

   }


}