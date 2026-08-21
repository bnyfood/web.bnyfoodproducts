<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Inventory_reciept extends CI_Controller
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


    $this->load->model('web_purchase_order_model');
    $this->load->model('store_location_model');
    $this->load->model('store_shelf_model');
    $this->load->model('inventory_reciept_model');
    $this->load->model('inventory_reciept_po_model');
    $this->load->model('web_material_model');
    $this->load->model('web_purchase_material_model');
    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function get_by_ponumber(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(4); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);
      $po_number = $this->uri->segment(5); 

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_purchase_order_model->select_by_ponumber($shopid,$po_number);

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

  function get_by_no_join(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(4); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);
      $po_number = $this->uri->segment(5); 

     // echo $shopid_en."-->".$shopid."<--";

      $data_po_profile = $this->web_purchase_order_model->select_by_ponumber_one($shopid,$po_number);

      $data_po = $this->web_purchase_order_model->select_by_ponumber_join_reciept($shopid,$po_number);

      $data_locations = $this->store_location_model->select_by_shop_id($shopid);

      //$data_shelfs = $this->store_shelf_model->select_by_shop_id($shopid);

      $data_shelfs = $this->store_shelf_model->select_by_shop_id($shopid);

      if(!empty($data_shelfs)){
        $num = 0;
        foreach($data_shelfs as $data_shelf){
          $data_subs = $this->store_shelf_model->select_by_sub($data_shelf['store_shelf_id']);
          if(!empty($data_subs)){

            $data_shelfs[$num]['arr_sub_shelfs'] = $data_subs;
            
          }
          $num = $num+1;
        }
      }


      $data = array(
        'data_po_profile' => $data_po_profile,
        'data_po' => $data_po,
        'data_locations' => $data_locations,
        'data_shelfs' => $data_shelfs
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

  function get_by_no_pro_join(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->uri->segment(4); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);
      $po_number = $this->uri->segment(5); 

     // echo $shopid_en."-->".$shopid."<--";

      $data_po_profile = $this->web_purchase_order_model->select_by_ponumber_one($shopid,$po_number);

      $data_po = $this->web_purchase_order_model->select_by_ponumber_join_reciept_location($shopid,$po_number);


      $data = array(
        'data_po_profile' => $data_po_profile,
        'data_po' => $data_po
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

  function reciept_add(){
    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);

        $arr_purchase_material_id = $this->input->post('arr_purchase_material_id');

        $arr_purchase_material_id_de = json_decode($arr_purchase_material_id);

        $web_purchase_order_id = $this->input->post('web_purchase_order_id');

        $now_ym = date("Y-m");
        $now_ymd = date("Y-m-d");
        $arr_lastorder = $this->inventory_reciept_model->last_order_code_by_yymm($now_ym);   
        $lastorder = "no";
        if(!empty($arr_lastorder)){
          $lastorder = $arr_lastorder['ir_number'];
        }

        $new_irnumber = $this->data_bl->get_ircode($lastorder,$now_ymd);  

        $arr_data = array(
          'web_purchase_order_id' => $web_purchase_order_id,
          'po_number' =>  $this->input->post('po_number'),
          'ir_number' =>  $new_irnumber,
          'ShopID' => $ShopID
        );

        $inventory_reciept_id = $this->inventory_reciept_model->insert($arr_data);

        $cnt_arr = count($arr_purchase_material_id_de) -1 ;
        
        for($i=0;$i<=$cnt_arr;$i++){

          $web_material_id_val =  $this->input->post('web_material_id_'.$arr_purchase_material_id_de[$i]);
          $unit_val =  $this->input->post('unit_'.$arr_purchase_material_id_de[$i]);
          $qty_val =  $this->input->post('qty_'.$arr_purchase_material_id_de[$i]);

          //Start Process web_material price history 
          $arr_mat = $this->web_material_model->select_by_id($web_material_id_val);
          if(!empty($arr_mat)){

          //-----start update new data material
            if($unit_val != $arr_mat['material_unit_price']){
              $arr_mat_update = array(
                'material_unit_price' => $unit_val
              );

              $this->web_material_model->update($arr_mat_update,$web_material_id_val);

              $arr_material_his = $this->web_material_model->select_by_id_history($arr_mat['web_material_id']);

              $arr_p_m = array(
                'web_material_history_id' => $arr_material_his['web_material_history_id']
              );

              $this->web_purchase_material_model->update($arr_p_m,$arr_purchase_material_id_de[$i]);

            }
          }
          //End Process web_material price history 

          //Start Process web_purchase_material qty history 
          $arr_pre_mat =  $this->web_purchase_material_model->select_by_id($arr_purchase_material_id_de[$i]);
          if(!empty($arr_pre_mat)){
            if($qty_val != $arr_pre_mat['qty']){

              $arr_p_m = array(
                'qty' => $qty_val
              );

              $this->web_purchase_material_model->update($arr_p_m,$arr_purchase_material_id_de[$i]);

            }
          }
          //End Process web_purchase_material qty history 

          $arr_purchase_material_his = $this->web_purchase_material_model->select_by_id_history($arr_purchase_material_id_de[$i]);
          $purchase_material_his_id = $arr_purchase_material_his['web_purchase_material_history_id'];  
            
          $arr_inventory_reciept = array();
          $arr_inventory_reciept['inventory_reciept_id'] = $inventory_reciept_id;
          //$arr_inventory_reciept['web_purchase_material_id'] = $arr_purchase_material_id_de[$i];
          $arr_inventory_reciept['web_purchase_material_history_id'] = $purchase_material_his_id;
          $arr_inventory_reciept['store_location_id'] =  $this->input->post('location_'.$arr_purchase_material_id_de[$i]);
          $arr_inventory_reciept['store_sub_shelf_id'] =  $this->input->post('shelf_'.$arr_purchase_material_id_de[$i]);
          $arr_inventory_reciept['po_number'] =  $this->input->post('po_number');
          $arr_inventory_reciept['ir_number'] =  $new_irnumber;
          $arr_inventory_reciept['ShopID'] =  $ShopID;

          $data_re = $this->inventory_reciept_po_model->insert($arr_inventory_reciept);
          
          $data_web_purchase_up = array(
            'reciept_yet' => 1
          );

          $this->web_purchase_order_model->update($data_web_purchase_up,$web_purchase_order_id);
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
}