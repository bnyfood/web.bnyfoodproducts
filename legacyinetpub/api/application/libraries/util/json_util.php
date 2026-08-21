<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class Json_util 
{
	
	function __construct()
	{
		$this->CI =& get_instance();
		
	}

	function make_json($State,$Status,$Data,$Description,$Token){

		$arr_res_log = array(
          'State' => $State,
          'Status' => $Status,
          'Data' => $Data,
          'Description'=>$Description,
          'Token' => $Token
        );
       	$data_json_log = json_encode($arr_res_log,JSON_UNESCAPED_UNICODE);

       	$arr_res = array(
          'State' => $State,
          'Status' => $Status,
          'Data' => $Data,
          'Description'=>$Description
        );
       	$data_json = json_encode($arr_res,JSON_UNESCAPED_UNICODE);

       	$arr_json = array(
       		'log' => $data_json_log,
       		'view' => $data_json
       	);
      	return $arr_json;
	}

    function json_unicode($arr_data)
    {

    	$data_json = json_encode($arr_data,JSON_UNESCAPED_UNICODE);
      return $data_json;

    }



}