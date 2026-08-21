<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Tiktok_report_sale
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();
		
    }

    public function make_taxinvoice_group($arr_data)
	{
    if(!empty($arr_data)){
      $arr_make = array();
      $arr_pre1 = array();
      $num = 1;
      $date_tmp = '';
      $num_arr = 0;
      $cnt_arr = count($arr_data);

      //$sum_val = 0;

      //$num9 = 0;

      foreach($arr_data as $data){
        //$sum_val += $data['price']+$data['shipping_fee']-$data['voucher'];
        //$num9 = $num9+1;
        $num_arr = $num_arr+1;
        if($num_arr == 1){//if first record
          $date_tmp = $data['transactiondate']; 
        }

        if($date_tmp != $data['transactiondate']){ //if date change make all sum from arr_pre1 to arr_make and clear arr_pre1

          $date_tmp = $data['transactiondate']; 
          if(!empty($arr_pre1)){
                array_push($arr_make,$arr_pre1[0]);
                unset($arr_pre1);
                $arr_pre1 = array();
                $num=1;
              }
        }  

        

        if(!empty($data['start_tiv'])){ // check is full imvoice
            if(!empty($arr_pre1)){
              array_push($arr_make,$arr_pre1[0]);
              unset($arr_pre1);
              $arr_pre1 = array();
              $num=1;
            }
            array_push($arr_make,$data);

            if($num_arr == $cnt_arr){ // if last data
              array_push($arr_make,$arr_pre1[0]);
              unset($arr_pre1);
          }

        }else{ // not full invoice
          if($num == 1){
            array_push($arr_pre1,$data);
            $num = 2;
          }else{
            //print_r($arr_pre1);
            $arr_pre1[0]['end_inv'] = $data['end_inv'];
            $arr_pre1[0]['original_price'] = $arr_pre1[0]['original_price']+$data['original_price'];
            $arr_pre1[0]['price'] = $arr_pre1[0]['price']+$data['price'];
            $arr_pre1[0]['shipping_fee'] = $arr_pre1[0]['shipping_fee']+$data['shipping_fee'];
            $arr_pre1[0]['voucher_seller'] = $arr_pre1[0]['voucher_seller']+$data['voucher_seller'];
            $arr_pre1[0]['voucher_platform'] = $arr_pre1[0]['voucher_platform']+$data['voucher_platform'];
            $arr_pre1[0]['voucher'] = $arr_pre1[0]['voucher']+$data['voucher'];

            $arr_pre1[0]['priceVATincluded'] = $arr_pre1[0]['priceVATincluded']+$data['priceVATincluded'];
            $arr_pre1[0]['priceBeforeVAT'] = $arr_pre1[0]['priceBeforeVAT']+$data['priceBeforeVAT'];
            $arr_pre1[0]['VAT'] = $arr_pre1[0]['VAT']+$data['VAT'];

            
            /*$priceVATincluded = $arr_pre1[0]['price']-$arr_pre1[0]['voucher'];
            $arr_pre1[0]['priceVATincluded'] = $priceVATincluded;
            $priceBeforeVAT=round($priceVATincluded/1.07,2);
            $arr_pre1[0]['priceBeforeVAT'] = $priceBeforeVAT;
            $VAT=$priceVATincluded-$priceBeforeVAT;
            $arr_pre1[0]['VAT'] = $VAT;*/
            
          }
          

          if($num_arr == $cnt_arr){ // if last data
              array_push($arr_make,$arr_pre1[0]);
              unset($arr_pre1);
          }

          
         // print_r($arr_pre1);
        }
      

      }// foreach

      //echo "sum=".$sum_val;
      //echo "num=".$num9;

      //echo $num_arr."<>".$cnt_arr;
		
//print_r($arr_make);
        return $arr_make;
      }else{
        return null;
      }

	}
	
}