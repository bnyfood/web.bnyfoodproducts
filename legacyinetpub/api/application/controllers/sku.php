<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Sku extends CI_Controller
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


    $this->load->model('web_sku_model');
    $this->load->model('web_sku_map_product_model');
  

    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function get_sku_by_shop_id(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(3); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $data_sku = $this->web_sku_model->select_by_shop_id(5);

      if(!empty($data_sku)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }

  function sku_add(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $sku_name = $this->input->post('sku_name');
        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);
        $sku_data = $this->input->post('sku_data');
        $temp_key = $this->input->post('temp_key');

        $arr_data = array(
          'sku_name' => $sku_name,
          'temp_key' => $temp_key,
          'ShopID' => $ShopID
        );

        $sku_id = $this->web_sku_model->insert($arr_data);

        $arr_ex1 = explode("|",$sku_data);
        $cnt_1 = count($arr_ex1);
        if($cnt_1 > 0){
          for ($i = 0; $i <= $cnt_1-1; $i++) {
            $arr_ex2 = explode("_",$arr_ex1[$i]);
            $pro_id_en = $arr_ex2[0];
            $pro_id = $this->encryption_util->decrypt_ssl($pro_id_en);
            $quan = $arr_ex2[1];

            $data_map = array(
              'web_sku_id' => $sku_id,
              'product_id' => $pro_id,
              'quantity' => $quan,
              'ShopID' => $ShopID
            );

            $this->web_sku_map_product_model->insert($data_map);

          }
        }

        if(!empty($data_menu)){
          $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Fail',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
        }
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }
  }

  function get_sku_by_temp_key(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(3); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $temp_key = $this->uri->segment(4); 

      $data_sku = $this->web_sku_model->select_by_temp_key($shopid,$temp_key);

      if(!empty($data_sku)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }

  function get_sku_by_product_id(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(3); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $product_id_en = $this->uri->segment(4); 
      $product_id = $this->encryption_util->decrypt_ssl($product_id_en);

      $data_sku = $this->web_sku_model->select_by_product_id($shopid,$product_id);

      if(!empty($data_sku)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_sku,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }
 
}