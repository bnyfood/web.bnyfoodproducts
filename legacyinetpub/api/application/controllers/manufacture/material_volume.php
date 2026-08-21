<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material_volume extends CI_Controller
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


    $this->load->model('web_material_volume_model');
    $this->load->model('web_material_volume_type_model');
    $this->load->model('web_material_unit_model');


    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function get_by_shop(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(4); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);
      $per_page = $this->uri->segment(5); 

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_material_volume_model->select_by_shop_id_join_limit_his($shopid,$per_page);

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

  function material_volume_add(){

    /*

    $data_curl = array(
      'web_material_id' => $web_material_id,
      'web_material_volume' => $web_material_volume,
      'vt_type' => $vt_type,
      'web_material_volume_type_id' => $web_material_volume_type_id,
      'web_material_volume_type' => $web_material_volume_type,
      'unit_type' => $unit_type,
      'web_material_unit_id' => $web_material_unit_id,
      'material_unit' => $material_unit,
      'ShopID' => $sess_shop_id

    );

    */

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $web_material_id = $this->input->post('web_material_id');
        $web_material_volume = $this->input->post('web_material_volume');

        $vt_type = $this->input->post('vt_type');
        $web_material_volume_type_id = $this->input->post('web_material_volume_type_id');
        $web_material_volume_type = $this->input->post('web_material_volume_type');

        $unit_type = $this->input->post('unit_type');
        $web_material_unit_id = $this->input->post('web_material_unit_id');
        $material_unit = $this->input->post('material_unit');
        

        $ShopID_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);


        if($unit_type == 2){

          $data_unit = array(
            'material_unit' => $material_unit
          );

          $web_material_unit_id = $this->web_material_unit_model->insert($data_unit);

        }

        if($vt_type == 2){

          $data_volume_type = array(
            'web_material_volume_type' => $web_material_volume_type,
            'ShopID' => $ShopID
          );

          $web_material_volume_type_id = $this->web_material_volume_type_model->insert($data_volume_type);

        }

        $data_material_volume = array(
          'web_material_id' => $web_material_id,
          'web_material_volume_type_id' => $web_material_volume_type_id,
          'web_material_unit_id' => $web_material_unit_id,
          'web_material_volume' => $web_material_volume,
          'ShopID' => $ShopID
        );

        $data_re = $this->web_material_volume_model->insert($data_material_volume);

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

  function material_volume_edit(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $material_volume_id_en = $this->input->post('material_volume_id_en');
        $material_volume_id = $this->encryption_util->decrypt_ssl($material_volume_id_en);

        $web_material_id = $this->input->post('web_material_id');
        $web_material_volume = $this->input->post('web_material_volume');

        $vt_type = $this->input->post('vt_type');
        $web_material_volume_type_id = $this->input->post('web_material_volume_type_id');
        $web_material_volume_type = $this->input->post('web_material_volume_type');

        $unit_type = $this->input->post('unit_type');
        $web_material_unit_id = $this->input->post('web_material_unit_id');
        $material_unit = $this->input->post('material_unit');
        

        $ShopID_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);


        if($unit_type == 2){

          $data_unit = array(
            'material_unit' => $material_unit
          );

          $web_material_unit_id = $this->web_material_unit_model->insert($data_unit);

        }

        if($vt_type == 2){

          $data_volume_type = array(
            'web_material_volume_type' => $web_material_volume_type,
            'ShopID' => $ShopID
          );

          $web_material_volume_type_id = $this->web_material_volume_type_model->insert($data_volume_type);

        }

        $data_material_volume = array(
          'web_material_id' => $web_material_id,
          'web_material_volume_type_id' => $web_material_volume_type_id,
          'web_material_unit_id' => $web_material_unit_id,
          'web_material_volume' => $web_material_volume
        );

        $data_re = $this->web_material_volume_model->update($data_material_volume,$material_volume_id);

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

  function get_by_web_material_id_lasted(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $material_volume_id_en = $this->uri->segment(4);  
        $material_volume_id = $this->encryption_util->decrypt_ssl($material_volume_id_en);  

        $data_re = $this->web_material_volume_model->select_by_web_material_volume_id_lasted($material_volume_id);

        if(!empty($data_user)){
          $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',$arr_header['api_token']); 
        }
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }
  }

}