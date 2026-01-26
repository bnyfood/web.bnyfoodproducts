<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Shopee_report_cn
{

	
	function __construct() 
	{
		
		$this->CI =& get_instance();
		
    }

    public function make_cn($arr_data){

      if(count($arr_data) > 0 ){
        $num = 0;
        foreach($arr_data as $data){

          $yymm_ex = explode('-',$data['updated_at']);
          $yymm1 = $yymm_ex[0]."-".$yymm_ex[1];

          if($num == 0){
            $start_yymm = $yymm1;
            $start_num = 1;
            $arr_explode = explode('-',$start_yymm);
            $yy = $arr_explode[0];
            $mm = $arr_explode[1];
            //echo "1>>".$start_num."<br>";
          }

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
          $cncode = "CNShp".$yy.$mm.$run_num;

          $arr_data[$num]['cncode'] = $cncode;

          $start_num = $start_num +1;
          $num = $num+1;


        } 

      }

      return $arr_data;

    }

    function make_group_cn($arr_datas){

        if(!empty($arr_datas)){

        $data_cn = array();
        $date_num = 1;
        $date_point = "";
        $start_inv = "";
        $stop_inv = "";

        $ValueBeforeVAT = 0;
        $VAT = 0;
        $ValueBeforeVATPlatform = 0;
        $VATPlatform = 0;

        $cnt_data = count($arr_datas);

        foreach($arr_datas as $data){

          //echo $date_num."---".$cnt_data."---".$data['updated_at']."<br>";

          if($date_num == 1){

            $date_point = $data['updated_at'];
            $start_inv = $data['cncode'];
            $stop_inv = $data['cncode'];

            $ValueBeforeVAT = $data['ValueBeforeVAT'];
            $VAT = $data['VAT'];
            $ValueBeforeVATPlatform = $data['ValueBeforeVATPlatform'];
            $VATPlatform = $data['VATPlatform'];

            $cn_status = $data['cn_status'];
            $status2 = $data['status2'];

            $date_num = $date_num+1;
          }elseif($date_num > 1){
              // old date
              if($date_point == $data['updated_at']){

                $ValueBeforeVAT = $ValueBeforeVAT+$data['ValueBeforeVAT'];
                $VAT = $VAT+$data['VAT'];
                $ValueBeforeVATPlatform = $ValueBeforeVATPlatform+$data['ValueBeforeVATPlatform'];
                $VATPlatform = $VATPlatform+$data['VATPlatform'];

                $stop_inv = $data['cncode'];
                

                // last data
                if($date_num == $cnt_data){

                  $data_group = array(
                    'updated_at' => $date_point,
                    'cncode' => $start_inv."-".$stop_inv,
                    'cn_status' => $cn_status,
                    'status2' => $status2,
                    'ValueBeforeVAT' => $ValueBeforeVAT,
                    'VAT' => $VAT,
                    'ValueBeforeVATPlatform' => $ValueBeforeVATPlatform,
                    'VATPlatform' => $VATPlatform
                  );

                    array_push($data_cn,$data_group);

                }

                $date_num = $date_num+1;

              }else{
                // new date
                $data_group = array(
                  'updated_at' => $date_point,
                  'cncode' => $start_inv."-".$stop_inv,
                  'cn_status' => $cn_status,
                  'status2' => $status2,
                  'ValueBeforeVAT' => $ValueBeforeVAT,
                  'VAT' => $VAT,
                  'ValueBeforeVATPlatform' => $ValueBeforeVATPlatform,
                  'VATPlatform' => $VATPlatform
                );

                array_push($data_cn,$data_group);

                $date_point = $data['updated_at'];
                $start_inv = $data['cncode'];
                $stop_inv = $data['cncode'];

                $cn_status = $data['cn_status'];
                $status2 = $data['status2'];

                $ValueBeforeVAT = 0;
                $VAT = 0;
                $ValueBeforeVATPlatform = 0;
                $VATPlatform = 0;  

                $ValueBeforeVAT = $ValueBeforeVAT+$data['ValueBeforeVAT'];
                $VAT = $VAT+$data['VAT'];
                $ValueBeforeVATPlatform = $ValueBeforeVATPlatform+$data['ValueBeforeVATPlatform'];
                $VATPlatform = $VATPlatform+$data['VATPlatform'];

                


                if($date_num == $cnt_data){

                    

                  $data_group = array(
                    'updated_at' => $date_point,
                    'cncode' => $start_inv."-".$stop_inv,
                    'cn_status' => $cn_status,
                    'status2' => $status2,
                    'ValueBeforeVAT' => $ValueBeforeVAT,
                    'VAT' => $VAT,
                    'ValueBeforeVATPlatform' => $ValueBeforeVATPlatform,
                    'VATPlatform' => $VATPlatform
                  );

                    array_push($data_cn,$data_group);

                }

                $date_num = $date_num+1;

              }
              
          }

          //echo $date_num."---".$cnt_data."---".$data['updated_at']."\n";

        }

      }

      return($data_cn);
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

	
}