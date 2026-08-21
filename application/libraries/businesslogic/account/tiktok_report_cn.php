<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Tiktok_report_cn
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
          $cncode = "CNTTK".$yy.$mm.$run_num;

          $arr_data[$num]['cncode'] = $cncode;

          $start_num = $start_num +1;
          $num = $num+1;


        } 

      }

      return $arr_data;

    }

    function make_group_cn($arr_datas){
        $data_cn = array();
        if (empty($arr_datas)) {
          return $data_cn;
        }

        $date_point = '';
        $start_inv = '';
        $stop_inv = '';
        $ValueBeforeVAT = 0;
        $VAT = 0;
        $ValueBeforeVATPlatform = 0;
        $VATPlatform = 0;
        $cn_status = '';
        $status2 = '';

        foreach ($arr_datas as $data) {
          $row_date = isset($data['updated_at']) ? $data['updated_at'] : '';
          $row_cn = isset($data['cncode']) ? $data['cncode'] : '';
          if ($date_point === '') {
            $date_point = $row_date;
            $start_inv = $row_cn;
            $stop_inv = $row_cn;
            $ValueBeforeVAT = isset($data['ValueBeforeVAT']) ? $data['ValueBeforeVAT'] : 0;
            $VAT = isset($data['VAT']) ? $data['VAT'] : 0;
            $ValueBeforeVATPlatform = isset($data['ValueBeforeVATPlatform']) ? $data['ValueBeforeVATPlatform'] : 0;
            $VATPlatform = isset($data['VATPlatform']) ? $data['VATPlatform'] : 0;
            $cn_status = isset($data['cn_status']) ? $data['cn_status'] : '';
            $status2 = isset($data['status2']) ? $data['status2'] : '';
            continue;
          }
          if ($date_point == $row_date) {
            $ValueBeforeVAT = $ValueBeforeVAT + (isset($data['ValueBeforeVAT']) ? $data['ValueBeforeVAT'] : 0);
            $VAT = $VAT + (isset($data['VAT']) ? $data['VAT'] : 0);
            $ValueBeforeVATPlatform = $ValueBeforeVATPlatform + (isset($data['ValueBeforeVATPlatform']) ? $data['ValueBeforeVATPlatform'] : 0);
            $VATPlatform = $VATPlatform + (isset($data['VATPlatform']) ? $data['VATPlatform'] : 0);
            $stop_inv = $row_cn;
            continue;
          }
          $data_cn[] = array(
            'updated_at' => $date_point,
            'cncode' => $start_inv."-".$stop_inv,
            'cn_status' => $cn_status,
            'status2' => $status2,
            'ValueBeforeVAT' => $ValueBeforeVAT,
            'VAT' => $VAT,
            'ValueBeforeVATPlatform' => $ValueBeforeVATPlatform,
            'VATPlatform' => $VATPlatform
          );
          $date_point = $row_date;
          $start_inv = $row_cn;
          $stop_inv = $row_cn;
          $cn_status = isset($data['cn_status']) ? $data['cn_status'] : '';
          $status2 = isset($data['status2']) ? $data['status2'] : '';
          $ValueBeforeVAT = isset($data['ValueBeforeVAT']) ? $data['ValueBeforeVAT'] : 0;
          $VAT = isset($data['VAT']) ? $data['VAT'] : 0;
          $ValueBeforeVATPlatform = isset($data['ValueBeforeVATPlatform']) ? $data['ValueBeforeVATPlatform'] : 0;
          $VATPlatform = isset($data['VATPlatform']) ? $data['VATPlatform'] : 0;
        }

        if ($date_point !== '') {
          $data_cn[] = array(
            'updated_at' => $date_point,
            'cncode' => $start_inv."-".$stop_inv,
            'cn_status' => $cn_status,
            'status2' => $status2,
            'ValueBeforeVAT' => $ValueBeforeVAT,
            'VAT' => $VAT,
            'ValueBeforeVATPlatform' => $ValueBeforeVATPlatform,
            'VATPlatform' => $VATPlatform
          );
        }

        return $data_cn;
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