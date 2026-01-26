<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class date_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
		
	}
    
   function getnextmonth_firstdate($datein)
   {

       
$datein=date("Y-m-d", strtotime ( '+1 month' , strtotime ($datein ) )) ;
$month_start=date("m",strtotime($datein));

$date_run=$datein;

while($month_start==date("m",strtotime($date_run)))
{
$date_run=date("Y-m-d", strtotime ( '+1 day' , strtotime ($date_run ) )) ;
}
$date_run=date("Y-m-d", strtotime ( '-1 day' , strtotime ($date_run ) )) ;
return date('Y-m-d H:i:s.u',strtotime($date_run));

   }


   function getFirstDateofMonth($datein)
   {

   	$month_start=date("m",strtotime($datein));

$date_run=$datein;

while($month_start==date("m",strtotime($date_run)))
{
$date_run=date("Y-m-d", strtotime ( '-1 day' , strtotime ($date_run ) )) ;
}
$date_run=date("Y-m-d", strtotime ( '+1 day' , strtotime ($date_run ) )) ;
return date('Y-m-d H:i:s.u',strtotime($date_run));
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

	function date_diff($start,$stop){
		
		$to_time = strtotime($stop);
		$from_time = strtotime($start);
		
		//echo round(abs($to_time - $from_time) / 60,2). " minute";
		$min = round(abs($to_time - $from_time) / 60);
		return $min;
	}

	function get_date_now_add_min($min_add){

		$min = '+ '.$min_add.' minute';

		$date = date('Y-m-d H:i:s');
      $dateunix_m = strtotime($date .$min);

      return $dateunix_m;

	}

	

	function get_date_now_unix(){

		date_default_timezone_set('UTC');

		$date = date('Y-m-d H:i:s');
      $dateunix = strtotime($date);

      return $dateunix;

	}

	function datetime_unix_to_dt($unit_time){

		return gmdate("Y-m-d H:i:s", $unit_time);

	}

	function get_start_stop_from_date_range($daterange){

		$arr_exp1 = explode("-",$daterange);
		$startdate = trim($arr_exp1[0]);
		$stopdate = trim($arr_exp1[1]);

		$startdate = date_create($startdate);
		$startdate =date_format($startdate,"Y/m/d");

		$stopdate = date_create($stopdate);
		$stopdate =date_format($stopdate,"Y/m/d");

		$arr_date = array(
			'start' => $startdate,
			'stop' => $stopdate
		);

		return $arr_date;
	}

}