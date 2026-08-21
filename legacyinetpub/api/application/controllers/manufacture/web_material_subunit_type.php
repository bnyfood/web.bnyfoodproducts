<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Web_material_subunit_type extends CI_Controller
{

    function __construct()
  {
    //:[Auto call parent construct]
    parent::__construct();
    //@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
    $this->load->library('util/View_util');
    $this->load->library('util/encryption_util');
    $this->load->library('businesslogic/api_log_bl');
    $this->load->library('businesslogic/api_auth_bl');
    $this->load->library('businesslogic/data_bl');


    $this->load->model('web_material_subunit_type_model');


    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function get_all(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_material_subunit_type_model->select_all();

      if(!empty($data)){
        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }

}