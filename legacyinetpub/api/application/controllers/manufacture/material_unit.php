<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material_unit extends CI_Controller
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


    $this->load->model('web_material_unit_model');


    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function material_unit_search(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $material_unit_search = $this->input->post('material_unit_search');
      $sortby = $this->input->post('sortby');
      $sorttype = $this->input->post('sorttype');
      $offset = $this->input->post('offset');
      $per_page = $this->input->post('per_page');

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_material_unit_model->select_by_search($material_unit_search,$per_page,$offset,$sortby,$sorttype);

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

  function material_unit_add(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    if($chk_auth['Status'] == "Success"){

      $material_unit = $this->input->post('material_unit');

      $arr_data = array(
        'material_unit' => $material_unit
      );

      $data_re = $this->web_material_unit_model->insert($arr_data);

      if(!empty($data_re)){
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

  function material_unit_edit(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    if($chk_auth['Status'] == "Success"){

      $id_en = $this->input->post('id_en');
      $material_unit = $this->input->post('material_unit');

      $arr_data = array(
        'material_unit' => $material_unit
      );

      $data_re = $this->web_material_unit_model->update($arr_data,$id_en);

      if(!empty($data_re)){
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

  function get_by_id(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $web_material_unit_id = $this->uri->segment(4);  
        //$material_volume_id = $this->encryption_util->decrypt_ssl($material_volume_id_en);  

        $data_re = $this->web_material_unit_model->select_by_id($web_material_unit_id);

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