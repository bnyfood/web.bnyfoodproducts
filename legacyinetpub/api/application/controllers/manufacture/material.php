<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Material extends CI_Controller
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


    $this->load->model('web_material_model');
    $this->load->model('material_map_supplier_model');
    $this->load->model('web_material_unit_model');
    $this->load->model('web_material_subunit_model');

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

      $data = $this->web_material_model->select_by_shop_id_join_limit_his($shopid,$per_page);

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

  function material_search(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('shopid_en'); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $material_search = $this->input->post('material_search');
      $sortby = $this->input->post('sortby');
      $sorttype = $this->input->post('sorttype');
      $offset = $this->input->post('offset');
      $per_page = $this->input->post('per_page');

     // echo $shopid_en."-->".$shopid."<--";

      //$data = $this->web_material_model->select_by_shop_id_join_search_his($shopid,$material_search,$per_page,$offset,$sortby,$sorttype);
      $data = $this->web_material_model->select_unit_by_shop_search($shopid,$material_search,$per_page,$offset,$sortby,$sorttype);

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

  function material_search_v2(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('shopid_en'); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $material_search = $this->input->post('material_search');

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_material_model->select_by_shop_id_join_search_his_v2($shopid,$material_search);

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

   function material_add(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $sub_unit = $this->input->post('sub_unit');
        $material_name = $this->input->post('material_name');
        $material_sku = $this->input->post('material_sku');
        $web_category_id = $this->input->post('web_category_id');
        $web_material_brand_id = $this->input->post('web_material_brand_id');
        $material_size = $this->input->post('material_size');
        $material_unit_price = $this->input->post('material_unit_price');
        $description = $this->input->post('description');
        $ran_num_sup = $this->input->post('ran_num_sup');
        $price_set = $this->input->post('price_set');
        $web_material_unit_id = $this->input->post('web_material_unit_id');
        $web_material_subunit_type_id = $this->input->post('web_material_subunit_type_id');
        $main_web_material_subunit_type_id = $this->input->post('main_web_material_subunit_type_id');
        //$web_material_unit_type_history_id = $this->input->post('web_material_unit_type_history_id');
        $material_density = $this->input->post('material_density');
        $subunit_qty = $this->input->post('subunit_qty');
        $web_material_subunit_id = $this->input->post('web_material_subunit_id');
        $newsku = $this->input->post('newsku');


        $arr_curl = $this->input->post('arr_curl');
        $arr_web_supplier_id = json_decode($arr_curl);

        if($sub_unit == 2){

          $arr_data = array(
            'material_name' => $material_name,
            'material_sku' => $material_sku,
            'material_cat_id' => $web_category_id,
            'material_brand_id' => $web_material_brand_id,
            'web_material_unit_id' => $web_material_unit_id,
            'material_size' => $material_size,
            'material_unit_price' => $material_unit_price,
            'material_density' => $material_density,
            'description' => $description,
            'ShopID' => $ShopID

          );

          $web_material_id = $this->web_material_model->insert($arr_data);

          $arr_data_subunit = array(
            'web_material_id' => $web_material_id,
            'web_material_unit_type_history_id' => $main_web_material_subunit_type_id,
            'is_main_unit' => 1,
            'subunit_id' => 0,
            'subunit_volume' => 1,
            'sku_name' => $newsku
          );

          $this->web_material_subunit_model->insert($arr_data_subunit);


        }elseif($sub_unit == 1){

          $arr_id = explode("_",$web_material_subunit_id);
          $web_material_id_main = $arr_id[0];
          $web_material_subunit_id_main = $arr_id[1];

          $arr_data_subunit = array(
            'web_material_id' => $web_material_id_main,
            'web_material_unit_type_history_id' => $web_material_subunit_type_id,
            'is_main_unit' => 0,
            'subunit_id' => $web_material_subunit_id_main,
            'subunit_volume' => $subunit_qty
          );

          $this->web_material_subunit_model->insert($arr_data_subunit);


        }

        $arr_1 = explode("|",$price_set);

        $cnt_id = count($arr_1);

        if($cnt_id > 0){

          for($i=0;$i<=$cnt_id-1;$i++){

            $arr_2 = explode("_",$arr_1[$i]);

            $arr_data = array(
              'web_supplier_id' => $arr_2[0],
              'web_material_id' => $web_material_id,
              'unit_price' => $arr_2[1],
              'code' => '',
              'status' => 1,
              'ShopID' => $ShopID

            );

            $this->material_map_supplier_model->insert($arr_data);

          }
        }

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

  function material_add_bk(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $material_name = $this->input->post('material_name');
        $web_category_id = $this->input->post('web_category_id');
        $web_material_brand_id = $this->input->post('web_material_brand_id');
        $material_size = $this->input->post('material_size');
        $material_unit_price = $this->input->post('material_unit_price');
        $description = $this->input->post('description');
        $ran_num_sup = $this->input->post('ran_num_sup');
        $price_set = $this->input->post('price_set');


        $arr_curl = $this->input->post('arr_curl');
        $arr_web_supplier_id = json_decode($arr_curl);

        $arr_data = array(
          'material_name' => $material_name,
          'material_cat_id' => $web_category_id,
          'material_brand_id' => $web_material_brand_id,
          'material_size' => $material_size,
          'material_unit_price' => $material_unit_price,
          'description' => $description,
          'ShopID' => $ShopID

        );

        $data_re = $this->web_material_model->insert($arr_data);

        $cnt_id = count($arr_web_supplier_id);

        if($cnt_id > 0){

          for($i=0;$i<=$cnt_id-1;$i++){

            $arr_data = array(
              'web_supplier_id' => $arr_web_supplier_id[$i],
              'web_material_id' => $data_re,
              'unit_price' => $material_unit_price,
              'code' => '',
              'status' => 1,
              'ShopID' => $ShopID

            );

            $this->material_map_supplier_model->insert($arr_data);

          }
        }

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

  function material_edit(){
    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);
      
      $web_material_id = $this->input->post('web_material_id');
      //$material_id = $this->encryption_util->decrypt_ssl($material_id_en);

      $web_supplier_id = $this->input->post('web_supplier_id');
      $material_name = $this->input->post('material_name');
      $web_category_id = $this->input->post('web_category_id');
      $web_material_brand_id = $this->input->post('web_material_brand_id');
      $material_size = $this->input->post('material_size');
      $web_material_unit_id = $this->input->post('web_material_unit_id');
      $material_price = $this->input->post('material_price');
      $description = $this->input->post('description');
      $ran_num_sup = $this->input->post('ran_num_sup');

      $material_density = $this->input->post('material_density');


      $arr_data = array(
        'material_name' => $material_name,
        'material_cat_id' => $web_category_id,
        'material_brand_id' => $web_material_brand_id,
        'web_material_unit_id' => $web_material_unit_id,
        'material_size' => $material_size,
        'material_density' => $material_density,
        'description' => $description
      );

      $this->web_material_model->update($arr_data,$web_material_id);

      
      $arr_data_price = array(
        'unit_price' => $material_price
      );

      $this->material_map_supplier_model->update_by_mid_sid($arr_data_price,$web_material_id,$web_supplier_id);


      if(!empty($data_re)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }
  }

  function material_edit_bk(){
    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);
      
      $material_id_en = $this->input->post('material_id_en');
      $material_id = $this->encryption_util->decrypt_ssl($material_id_en);

      $material_name = $this->input->post('material_name');
      $web_category_id = $this->input->post('web_category_id');
      $web_material_brand_id = $this->input->post('web_material_brand_id');
      $material_size = $this->input->post('material_size');
      $web_material_unit_id = $this->input->post('web_material_unit_id');
      $material_price = $this->input->post('material_price');
      $description = $this->input->post('description');
      $ran_num_sup = $this->input->post('ran_num_sup');

      $web_material_unit_id = $this->input->post('web_material_unit_id');
      $ran_num_sup = $this->input->post('ran_num_sup');

      $arr_data = array(
        'material_name' => $material_name,
        'material_cat_id' => $web_category_id,
        'material_brand_id' => $web_material_brand_id,
        'web_material_unit_id' => $web_material_unit_id,
        'material_size' => $material_size,
        'description' => $description
      );

      $this->web_material_model->update($arr_data,$material_id);

      $arr_curl = $this->input->post('arr_curl');
      $arr_web_supplier_id = json_decode($arr_curl);

      $cnt_id = count($arr_web_supplier_id);

      if($cnt_id > 0){
        $this->material_map_supplier_model->del_not_in($material_id,$arr_web_supplier_id,$ShopID);

        for($i=0;$i<=$cnt_id-1;$i++){

          $arr_map_chk = $this->material_map_supplier_model->get_by_matid_supid($material_id,$arr_web_supplier_id[$i],$ShopID);

          if(empty($arr_map_chk)){
            $arr_data = array(
              'web_supplier_id' => $arr_web_supplier_id[$i],
              'web_material_id' => $material_id,
              'code' => '',
              'status' => 1,
              'ShopID' => $ShopID

            );

            $this->material_map_supplier_model->insert($arr_data);
          }
        }
      }

      if(!empty($data_re)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
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

        $this->web_material_model->delete($id);

        $data_json = $this->json_util->make_json('Select data','Success','','Insert Success',$arr_header['api_token']); 
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

  }

  function material_add_v1(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $material_name = $this->input->post('material_name');
        $web_category_id = $this->input->post('web_category_id');
        $web_material_brand_id = $this->input->post('web_material_brand_id');
        $material_size = $this->input->post('material_size');
        $material_unit_price = $this->input->post('material_unit_price');
        $description = $this->input->post('description');
        $ran_num_sup = $this->input->post('ran_num_sup');

        $arr_data = array(
          'material_name' => $material_name,
          'material_cat_id' => $web_category_id,
          'material_brand_id' => $web_material_brand_id,
          'material_size' => $material_size,
          'material_unit_price' => $material_unit_price,
          'description' => $description,
          'ShopID' => $ShopID

        );

        $data_re = $this->web_material_model->insert($arr_data);

        $arr_map = array(
          'web_material_id' => $data_re
        );
        $this->material_map_supplier_model->update_by_code($arr_map,$ran_num_sup);

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

  function add_supplier(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $ran_num_sup = $this->input->post('ran_num_sup');
        $web_supplier_id = $this->input->post('web_supplier_id');

        $arr_data = array(
          'web_supplier_id' => $web_supplier_id,
          'code' => $ran_num_sup,
          'status' => 1,
          'ShopID' => $ShopID

        );

        $data_re = $this->material_map_supplier_model->insert($arr_data);

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

  function get_supplier_by_code(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $code = $this->uri->segment(4); 

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->material_map_supplier_model->select_by_code($code);

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

  function move_supplier_map(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $map_id = $this->input->post('map_id');

        $data_re = $this->material_map_supplier_model->delete($map_id);

        if(!empty($data_re)){
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

  function get_by_id(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $id_en = $this->uri->segment(4); 
      $id = $this->encryption_util->decrypt_ssl($id_en);

      $data = $this->web_material_model->select_by_id($id);

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

  function get_by_id_lasted(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $id = $this->uri->segment(4); 
      //$id = $this->encryption_util->decrypt_ssl($id_en);

      $data = $this->web_material_model->select_by_id_lasted($id);

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

  function get_edit_material_data(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $material_map_supplier_id = $this->uri->segment(4); 
      //$id = $this->encryption_util->decrypt_ssl($id_en);

      $arr_id = $this->material_map_supplier_model->select_by_mapid($material_map_supplier_id);

      $data_material = $this->web_material_model->select_by_id($arr_id['web_material_id']);

      $arr_mat_map = $this->material_map_supplier_model->select_by_material_id_lasted($material_map_supplier_id);
      //print_r($arr_mat_map);
      
      $data = array(
        'data_material' => $data_material,
        'arr_mat_map' => $arr_mat_map
      );

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

  function material_get_unit(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $data = $this->web_material_unit_model->select_all();

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

  function material_update_price(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $material_map_supplier_id = $this->input->post('material_map_supplier_id');
      $unit_price_val = $this->input->post('unit_price_val');


      $arr_data = array(
        'unit_price' => $unit_price_val
      );

      $this->material_map_supplier_model->update($arr_data,$material_map_supplier_id);


      if(!empty($data_re)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Insert Unsuccess',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }

  function sub_unit_search(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID'); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $sub_unit_txt_search = $this->input->post('sub_unit_txt_search');

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_material_unit_model->select_unit_by_shop_search($sub_unit_txt_search);

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