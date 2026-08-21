<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Purchase_order extends CI_Controller
{

    function __construct()
  {
    //:[Auto call parent construct]
    parent::__construct();
    //@@@:[Load Model, Business Logic (library) for prepare before use in controller function]
    $this->load->library('util/View_util');
    $this->load->library('util/encryption_util');
    $this->load->library('businesslogic/data_bl');
    $this->load->library('businesslogic/api_log_bl');
    $this->load->library('businesslogic/api_auth_bl');
    $this->load->library('businesslogic/material_bl');


    $this->load->model('Web_purchase_order_model');
    $this->load->model('Web_purchase_material_model');
    $this->load->model('Web_material_model');
    $this->load->model('web_supplier_model');
    $this->load->model('Material_map_supplier_model');

    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

     function sent_data_make_po(){

      $arr_header = $this->api_auth_bl->get_header();

        $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
        //print_r($chk_auth);
        //echo $chk_auth;
        if($chk_auth['Status'] == "Success"){

          $shopid_en = $this->input->post('ShopID');
          $ShopID = $this->encryption_util->decrypt_ssl($shopid_en); 

          $supplier_id = $this->input->post('web_supplier_id');
          $ran_num_pocode = $this->input->post('ran_num_pocode');
          $quotation_pic = $this->input->post('quotation_pic');

          $arr_po_adds_1 = $this->input->post('arr_po_adds');

          $arr_po_adds_2 = json_decode($arr_po_adds_1,true);
          $arr_po_adds = json_decode($arr_po_adds_2,true);
          //$arr_po_adds = json_decode($arr_po_adds);

          //print_r($arr_po_adds);

          $cnt_add = count($arr_po_adds);

          $num_mat = 0;

          if($cnt_add > 0){

            for($i=0;$i<=$cnt_add-1;$i++){

              //check qty > 0
              if(intval($arr_po_adds[$i][1]) > 0){

                if($arr_po_adds[$i][0]['is_del'] == "no"){
                  //check first material create new po number
                  if($num_mat == 0){
                  
                    $now_ym = date("Y-m");
                    $now_ymd = date("Y-m-d");
                    $arr_lastorder = $this->Web_purchase_order_model->last_order_code_by_yymm($now_ym,$ShopID);   
                    $lastorder = "no";
                    if(!empty($arr_lastorder)){
                      $lastorder = $arr_lastorder['po_number'];
                    }

                    $ponumber = $this->data_bl->get_pocode($lastorder,$now_ymd,"Po");  

                    $arr_insert = array(
                      'web_supplier_id' => $supplier_id,
                      'po_number' => $ponumber,
                      'ShopID' => $ShopID,
                      'pocode' => $ran_num_pocode,
                      'status' => 'Active',
                      'quotation_pic' => $quotation_pic
                    );

                    $web_purchase_order_id = $this->Web_purchase_order_model->insert($arr_insert);

                    $arr_insert_mat = array(
                      'web_purchase_order_id' => $web_purchase_order_id,
                      'web_material_id' => $arr_po_adds[$i][0]['web_material_id'],
                      'ShopID' => $ShopID,
                      'po_number' => $ponumber,
                      'qty' => intval($arr_po_adds[$i][1]),
                      'status' => 'Active',
                      'material_type' => 1
                      
                    );

                    $this->Web_purchase_material_model->insert($arr_insert_mat);

                  }else{

                    $arr_insert_mat = array(
                      'web_purchase_order_id' => $web_purchase_order_id,
                      'web_material_id' => $arr_po_adds[$i][0]['web_material_id'],
                      'ShopID' => $ShopID,
                      'po_number' => $ponumber,
                      'qty' => intval($arr_po_adds[$i][1]),
                      'status' => 'Active',
                      'material_type' => 1
                      
                    );

                    $this->Web_purchase_material_model->insert($arr_insert_mat);
                  }

                  $num_mat = $num_mat+1;

                }
              }
            }

            $arr_data = array(
              'po_number' => $ponumber
            );

            $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Select Success',$arr_header['api_token']); 
            echo $data_json['view'];

          }

        }else{

         $chk_auth = $this->json_util->json_unicode($chk_auth);
         echo $chk_auth;
        }

     }

     function get_by_shop(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->uri->segment(3); 
        $shopid = $this->encryption_util->decrypt_ssl($shopid_en);
        $per_page = $this->uri->segment(4); 

       // echo $shopid_en."-->".$shopid."<--";

        $data = $this->Web_purchase_order_model->select_by_shop_id_limit($shopid,$per_page);

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

    function loaddata_more(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('shopid_en'); 
        $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

        $po_search = $this->input->post('po_search');
        $po_status = $this->input->post('po_status');
        $start_date = $this->input->post('start_date');
        $stop_date = $this->input->post('stop_date');
        $sortby = $this->input->post('sortby');
        $sorttype = $this->input->post('sorttype');
        $offset = $this->input->post('offset');
        $per_page = $this->input->post('per_page');


       // echo $shopid_en."-->".$shopid."<--";

        $data = $this->Web_purchase_order_model->select_by_shop_id_search($shopid,$po_search,$po_status,$start_date,$stop_date,$per_page,$offset,$sortby,$sorttype);

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

    function po_search(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('shop_id_en'); 
        $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

        $po_search = $this->input->post('po_search');


       // echo $shopid_en."-->".$shopid."<--";

        $data = $this->Web_purchase_order_model->select_by_po_search($shopid,$po_search,'Active');

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

    function get_by_web_purchase_order_id(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $web_purchase_order_id = $this->uri->segment(3);

       // echo $shopid_en."-->".$shopid."<--";

        $data = $this->Web_purchase_order_model->select_by_web_purchase_order_id($web_purchase_order_id);

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

      function po_make(){

        $arr_header = $this->api_auth_bl->get_header();

        $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
        //print_r($chk_auth);
        //echo $chk_auth;
        if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  

        $supplier_id = $this->input->post('web_supplier_id');
        $material_id = $this->input->post('web_material_id');
        $ran_num_pocode = $this->input->post('ran_num_pocode');
        $ponumber = "";
        $arr_po_mats = "";
        $arr_mat = $this->Web_material_model->select_by_id($material_id);

        if(!empty($arr_mat)){

          $arr_mats = $this->material_bl->get_supplier_price($ShopID,$material_id,$supplier_id);

          $arr_suppliers = $this->web_supplier_model->select_by_shop_id($ShopID);

          $arr_data = array(
            'arr_mats' => $arr_mats,
            'arr_suppliers' => $arr_suppliers
          );


        }

        if(!empty($arr_data)){
          $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Insert Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Fail',$arr_data,'Insert Unsuccess',$arr_header['api_token']); 
        }
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

    }

    function po_make_bk(){

        $arr_header = $this->api_auth_bl->get_header();

        $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
        //print_r($chk_auth);
        //echo $chk_auth;
        if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  

        $supplier_id = $this->input->post('web_supplier_id');
        $material_id = $this->input->post('web_material_id');
        $po_qty = $this->input->post('po_qty');
        $ran_num_pocode = $this->input->post('ran_num_pocode');
        $po_price = $this->input->post('po_price');
        $arr_po_mats = "";
        $arr_mat = $this->Web_material_model->select_by_id($material_id);

        if(!empty($arr_mat)){
          //-----update new data material
          if($po_price != $arr_mat['material_unit_price']){
            $arr_mat_update = array(
              'material_unit_price' => $po_price
            );

            $this->Web_material_model->update($arr_mat_update,$arr_mat['web_material_id']);
          }
          //----- end update new data material

          //$arr_material_his = $this->Web_material_model->select_by_id_history($arr_mat['web_material_id']);

          $arr_po_chk = $this->Web_purchase_order_model->select_by_supp_code($ShopID,$supplier_id,$ran_num_pocode);
          if(empty($arr_po_chk)){
            $now_ym = date("Y-m");
            $now_ymd = date("Y-m-d");
            $arr_lastorder = $this->Web_purchase_order_model->last_order_code_by_yymm($now_ym,$ShopID);   
            $lastorder = "no";
            if(!empty($arr_lastorder)){
              $lastorder = $arr_lastorder['po_number'];
            }

            $new_ponumber = $this->data_bl->get_pocode($lastorder,$now_ymd,"Po");  

            $arr_insert = array(
              'web_supplier_id' => $supplier_id,
              'po_number' => $new_ponumber,
              'ShopID' => $ShopID,
              'status' => 'Temp',
              'pocode' => $ran_num_pocode
            );

            $web_purchase_order_id = $this->Web_purchase_order_model->insert($arr_insert);

            $arr_insert_mat = array(
              'web_purchase_order_id' => $web_purchase_order_id,
              'web_material_id' => $material_id,
              'ShopID' => $ShopID,
              'po_number' => $new_ponumber,
              'qty' => intval($po_qty),
              'status' => 'Temp',
              'material_type' => 1,
              'pocode' => $ran_num_pocode
              
            );

            $this->Web_purchase_material_model->insert($arr_insert_mat);

          }else{

            $arr_insert_mat = array(
              'web_purchase_order_id' => $arr_po_chk['web_purchase_order_id'],
              'web_material_id' => $material_id,
              'ShopID' => $ShopID,
              'po_number' => $arr_po_chk['po_number'],
              'qty' => intval($po_qty),
              'status' => 'Temp',
              'material_type' => 1,
              'pocode' => $ran_num_pocode
              
            );

            $this->Web_purchase_material_model->insert($arr_insert_mat);

          }

          $arr_po_mats = $this->Web_purchase_order_model->select_by_code_join($ShopID,$ran_num_pocode);

          $arr_mats = $this->material_bl->get_supplier_price($ShopID,$material_id,$supplier_id);

          $arr_suppliers = $this->web_supplier_model->select_by_shop_id($ShopID);

          $arr_data = array(
            'arr_po_mats' => $arr_po_mats,
            'arr_mats' => $arr_mats,
            'arr_suppliers' => $arr_suppliers
          );


        }

        if(!empty($arr_data)){
          $data_json = $this->json_util->make_json('Select data','Success',$arr_data,'Insert Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Fail',$arr_data,'Insert Unsuccess',$arr_header['api_token']); 
        }
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

    }

    function po_list_tmp(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
        $ran_num_pocode = $this->input->post('ran_num_pocode');

        $arr_pos = $this->Web_purchase_order_model->select_by_code($ShopID,$ran_num_pocode);

          if(!empty($arr_pos)){
            $num = 0;
            foreach($arr_pos as $arr_po){
              $arr_po_mats = $this->Web_purchase_material_model->select_by_po_id($arr_po['web_purchase_order_id']);

              if(!empty($arr_po_mats)){
                $arr_pos[$num]['arr_po_mats'] = $arr_po_mats;
              }

              $num = $num+1;
            }
          }

        if(!empty($arr_pos)){
          $data_json = $this->json_util->make_json('Select data','Success',$arr_pos,'Select Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Success',$arr_pos,'Select No data',$arr_header['api_token']); 
        }
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

    } 

     function po_build(){

        $arr_header = $this->api_auth_bl->get_header();

        $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
        //print_r($chk_auth);
        //echo $chk_auth;
        if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
        $arr_curl = $this->input->post('arr_curl');
        $ran_num_pocode = $this->input->post('ran_num_pocode');

        $chk_material_id = json_decode($arr_curl);

        //print_r($arr_curl);
        
        $cnt_id = count($chk_material_id);

        for($i=0;$i<=$cnt_id-1;$i++){
          //echo $chk_material_id[$i]."<br>"; 

          $arr_code_ex  = explode("-",$chk_material_id[$i]);

          $material_id_en = $arr_code_ex[0];
          $material_id = $this->encryption_util->decrypt_ssl($material_id_en);  

          $supplier_id = $arr_code_ex[1];

          $arr_mat = $this->Web_material_model->select_by_id($material_id);

          if(!empty($arr_mat)){
            $arr_po_chk = $this->Web_purchase_order_model->select_by_supp_code($ShopID,$supplier_id,$ran_num_pocode);
            if(empty($arr_po_chk)){
              $arr_insert = array(
                'web_supplier_id' => $supplier_id,
                'ShopID' => $ShopID,
                'status' => 'Temp',
                'pocode' => $ran_num_pocode
              );

              $web_purchase_order_id = $this->Web_purchase_order_model->insert($arr_insert);

              $arr_insert_mat = array(
                'web_purchase_order_id' => $web_purchase_order_id,
                'web_material_id' => $arr_mat['web_material_id'],
                'ShopID' => $ShopID,
                //'unitprice' => $arr_mat['material_unit_price'],
                'status' => 'Temp',
                'material_type' => 1
              );

              $this->Web_purchase_material_model->insert($arr_insert_mat);

            }else{

              $arr_insert_mat = array(
                'web_purchase_order_id' => $arr_po_chk['web_purchase_order_id'],
                'web_material_id' => $arr_mat['web_material_id'],
                'ShopID' => $ShopID,
                //'unitprice' => $arr_mat['material_unit_price'],
                'status' => 'Temp',
                'material_type' => 1
              );

              $this->Web_purchase_material_model->insert($arr_insert_mat);

            }

            /*$arr_pos = $this->Web_purchase_order_model->select_by_code($ShopID,$ran_num_pocode);

            if(!empty($arr_pos)){
              $num = 0;
              foreach($arr_pos as $arr_po){
                $arr_po_mats = $this->Web_purchase_material_model->select_by_po_id($arr_po['web_purchase_order_id']);

                if(!empty($arr_po_mats)){
                  $arr_pos[$num]['arr_po_mats'] = $arr_po_mats;
                }

                $num = $num+1;
              }
            }*/

          }

        }
          
          $arr_pos = "";
          if(!empty($arr_pos)){
            $data_json = $this->json_util->make_json('Select data','Success',$arr_pos,'Insert Success',$arr_header['api_token']); 
          }else{
            $data_json = $this->json_util->make_json('Select data','Fail',$arr_pos,'Insert Unsuccess',$arr_header['api_token']); 
          }
          
          echo $data_json['view'];

        }else{

         $chk_auth = $this->json_util->json_unicode($chk_auth);
         echo $chk_auth;
        }

    }

  function po_build_bk_v1(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
      $arr_curl = $this->input->post('arr_curl');
      $ran_num_pocode = $this->input->post('ran_num_pocode');

      $chk_material_id = json_decode($arr_curl);

      //print_r($arr_curl);
      
      $cnt_id = count($chk_material_id);

      for($i=0;$i<=$cnt_id-1;$i++){
        //echo $chk_material_id[$i]."<br>"; 

        $material_id_en  = $chk_material_id[$i];
        $material_id = $this->encryption_util->decrypt_ssl($material_id_en);  

        $arr_mat = $this->Web_material_model->select_by_id($material_id);

        if(!empty($arr_mat)){
          $arr_po_chk = $this->Web_purchase_order_model->select_by_supp_code($ShopID,$arr_mat['web_supplier_id'],$ran_num_pocode);
          if(empty($arr_po_chk)){
            $arr_insert = array(
              'web_supplier_id' => $arr_mat['web_supplier_id'],
              'ShopID' => $ShopID,
              'status' => 'Temp',
              'pocode' => $ran_num_pocode
            );

            $web_purchase_order_id = $this->Web_purchase_order_model->insert($arr_insert);

            $arr_insert_mat = array(
              'web_purchase_order_id' => $web_purchase_order_id,
              'web_material_id' => $arr_mat['web_material_id'],
              'ShopID' => $ShopID,
              'unitprice' => $arr_mat['material_unit_price'],
              'status' => 'Temp',
              'material_type' => 1
            );

            $this->Web_purchase_material_model->insert($arr_insert_mat);

          }else{

            $arr_insert_mat = array(
              'web_purchase_order_id' => $arr_po_chk['web_purchase_order_id'],
              'web_material_id' => $arr_mat['web_material_id'],
              'ShopID' => $ShopID,
              'unitprice' => $arr_mat['material_unit_price'],
              'status' => 'Temp',
              'material_type' => 1
            );

            $this->Web_purchase_material_model->insert($arr_insert_mat);

          }

          $arr_pos = $this->Web_purchase_order_model->select_by_code($ShopID,$ran_num_pocode);

          if(!empty($arr_pos)){
            $num = 0;
            foreach($arr_pos as $arr_po){
              $arr_po_mats = $this->Web_purchase_material_model->select_by_po_id($arr_po['web_purchase_order_id']);

              if(!empty($arr_po_mats)){
                $arr_pos[$num]['arr_po_mats'] = $arr_po_mats;
              }

              $num = $num+1;
            }
          }

        }

      }
        

        if(!empty($arr_pos)){
          $data_json = $this->json_util->make_json('Select data','Success',$arr_pos,'Insert Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Fail',$arr_pos,'Insert Unsuccess',$arr_header['api_token']); 
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

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
        $web_supplier_id = $this->input->post('web_supplier_id');
        $krysearch = $this->input->post('krysearch');

        $data = $this->Web_material_model->search_by_keyword_history($ShopID,$web_supplier_id,$krysearch);

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

  function material_get_all(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  

        $data = $this->Web_material_model->search_by_shop_history($ShopID);

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

  function material_search_by_name(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
      $txt_ma = $this->input->post('txt_ma');
      $web_supplier_id = $this->input->post('web_supplier_id');

      $exp1 = explode(" ", $txt_ma);
      $cnt_exp1 = count($exp1);

      $txt_size = trim($exp1[$cnt_exp1-2]);
      $txt_unit = trim($exp1[$cnt_exp1-1]);
      $txt_search = "";
      $txt_num = 1;

      for ($x = 0; $x <= $cnt_exp1-3; $x++) {

        if($txt_num > 1){
          $txt_search .= " ".$exp1[$x];
        }else{
          $txt_search = $exp1[$x];
        } 

        $txt_num = $txt_num+1;
        
      }

      $data_search = $this->Web_material_model->search_by_name_join_history($ShopID,$txt_search,$txt_size,$txt_unit,$web_supplier_id);

     // $data_compares = $this->Web_material_model->select_by_matid_no_supid($ShopID,$data_search['web_material_id'],$web_supplier_id);

      $data = array(
        'data_search' => $data_search,
       // 'data_compares' => $data_compares
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

  function material_compare(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
      $web_supplier_id = $this->input->post('web_supplier_id');
      $web_material_id = $this->input->post('web_material_id');

      $data = $this->Web_material_model->select_by_matid_no_supid($ShopID,$web_material_id,$web_supplier_id);

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

  function delete_po(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $web_purchase_order_id_en = $this->input->post('web_purchase_order_id');
        $web_purchase_order_id = $this->encryption_util->decrypt_ssl($web_purchase_order_id_en);  

        $data_up = array(
          'status' => 'Canceled'
        );
        $this->Web_purchase_order_model->update($data_up,$web_purchase_order_id);

        $data_json = $this->json_util->make_json('Select data','Success','','Del Success',$arr_header['api_token']); 
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

  }

  function po_del_by_code(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $ran_num_pocode = $this->input->post('ran_num_pocode');

        $this->Web_purchase_order_model->po_del_by_code($ran_num_pocode);
        $this->Web_purchase_material_model->po_del_by_code($ran_num_pocode);

        $data_json = $this->json_util->make_json('Select data','Success','','Del Success',$arr_header['api_token']); 
        
        echo $data_json['view'];

      }else{

       $chk_auth = $this->json_util->json_unicode($chk_auth);
       echo $chk_auth;
      }

  }

  function print_po_bk(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
        $ran_num_pocode = $this->input->post('ran_num_pocode');

        $data = $this->Web_purchase_order_model->select_by_code_join($ShopID,$ran_num_pocode);

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

  function print_po(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      
      if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  
        $po_number = $this->input->post('po_number');
        $web_supplier_id = $this->input->post('web_supplier_id');

        $data = $this->Web_purchase_order_model->select_by_code_join($ShopID,$po_number,$web_supplier_id);

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

  function del_material_action(){

        $arr_header = $this->api_auth_bl->get_header();

        $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
        //print_r($chk_auth);
        //echo $chk_auth;
        if($chk_auth['Status'] == "Success"){

        $shopid_en = $this->input->post('ShopID');
        $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  

        $web_purchase_material_id = $this->input->post('web_purchase_material_id');
        $ran_num_pocode = $this->input->post('ran_num_pocode');

        $this->Web_purchase_material_model->delete($web_purchase_material_id);
          
        $arr_po_mats = $this->Web_purchase_order_model->select_by_code_join($ShopID,$ran_num_pocode);

        if(!empty($arr_po_mats)){
          $data_json = $this->json_util->make_json('Select data','Success',$arr_po_mats,'Insert Success',$arr_header['api_token']); 
        }else{
          $data_json = $this->json_util->make_json('Select data','Fail',$arr_po_mats,'Insert Unsuccess',$arr_header['api_token']); 
        }
          
          echo $data_json['view'];

        }else{

         $chk_auth = $this->json_util->json_unicode($chk_auth);
         echo $chk_auth;
        }

    }

  function change_material_price(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('ShopID');
      $ShopID = $this->encryption_util->decrypt_ssl($shopid_en);  

      $web_supplier_id = $this->input->post('web_supplier_id');
      $web_material_id = $this->input->post('web_material_id');
      $status = $this->input->post('status');
      $new_mat_price = $this->input->post('new_mat_price');

      $data_add = array(
        'web_supplier_id' => $web_supplier_id,
        'web_material_id' => $web_material_id,
        'unit_price' => $new_mat_price,
        'status' => $status,
        'ShopID' => $ShopID
      );

      $insert_id = $this->Material_map_supplier_model->insert($data_add);
        
      $arr_new_mat_price = $this->Material_map_supplier_model->select_by_material_id_sup_id_lasted($web_material_id,$web_supplier_id);

      if(!empty($insert_id)){
        $data_json = $this->json_util->make_json('Select data','Success',$arr_new_mat_price,'Insert Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Fail',$arr_new_mat_price,'Insert Unsuccess',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;
    }

  }  
 
}