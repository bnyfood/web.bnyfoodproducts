<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Shelf extends CI_Controller
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


    $this->load->model('store_shelf_model');
    $this->load->model('store_sub_shelf_model');
  

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

      $datas = $this->store_shelf_model->select_by_shop_id_limit($shopid,$per_page);

      if(!empty($datas)){
        $num = 0;
        foreach($datas as $data){
          $data_subs = $this->store_shelf_model->select_by_sub($data['store_shelf_id']);
          if(!empty($data_subs)){

            $datas[$num]['arr_sub_shelfs'] = $data_subs;
            
          }
          $num = $num+1;
        }
      }


      if(!empty($data)){
        $data_json = $this->json_util->make_json('Select data','Success',$datas,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$datas,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }

  function shelf_add(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $store_shelf_name = $this->input->post('store_shelf_name');


        $arr_data = array(
          'store_shelf_name' => $store_shelf_name,
          'ShopID' => $ShopID

        );

        $data_re = $this->store_shelf_model->insert($arr_data);

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

  function sub_shelf_add(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $store_shelf_id_en = $this->input->post('store_shelf_id');
        $store_shelf_id = $this->encryption_util->decrypt_ssl($store_shelf_id_en);

        $store_sub_shelf_name = $this->input->post('store_sub_shelf_name');


        $arr_data = array(
          'store_shelf_id' => $store_shelf_id,
          'store_sub_shelf_name' => $store_sub_shelf_name,
          'ShopID' => $ShopID

        );

        $data_re = $this->store_sub_shelf_model->insert($arr_data);

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

      $id_en = $this->uri->segment(4); 
      $id = $this->encryption_util->decrypt_ssl($id_en);

      $data = $this->store_shelf_model->select_by_id($id);

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

  function shelf_edit(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $id_en = $this->input->post('id_en');
        $id = $this->encryption_util->decrypt_ssl($id_en);

        $store_shelf_name = $this->input->post('store_shelf_name');


        $arr_data = array(
          'store_shelf_name' => $store_shelf_name
        );

        $data_re = $this->store_shelf_model->update($arr_data,$id);

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

  function del_action(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $id_en = $this->uri->segment(4);
        $id = $this->encryption_util->decrypt_ssl($id_en);

        $this->store_shelf_model->delete($id);

        $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

  }

  function loaddata_more(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('shopid_en'); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $shelf_search = $this->input->post('shelf_search');
      $sortby = $this->input->post('sortby');
      $sorttype = $this->input->post('sorttype');
      $offset = $this->input->post('offset');
      $per_page = $this->input->post('per_page');


     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->store_shelf_model->select_by_shop_id_search($shopid,$shelf_search,$per_page,$offset,$sortby,$sorttype);

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