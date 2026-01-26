<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Tiktok_bk extends CI_Controller
{

    function __construct()
	{
		//:[Auto call parent construct]
		parent::__construct();
		//@@@:[Load Model, Business Logic (library) for prepare before use in controller function]

     }

     function unix_time(){
        date_default_timezone_set('UTC');
        $start_time = strtotime('2024-07-24 18:16:12');
        echo ">>".$start_time."<<<br>";
        $timestamp=1721844972;
        echo gmdate("Y-m-d H:i:s", $start_time);

        echo "<br>";



     }

    

}