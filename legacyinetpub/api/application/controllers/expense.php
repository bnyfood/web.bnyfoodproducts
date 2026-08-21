<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Expense extends CI_Controller
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

    $this->load->model('bny_expense_model');
    $this->load->model('web_bankaccount_model');
    $this->load->model('web_supplier_model');
    $this->load->model('web_slip_model');
    $this->load->model('web_invoice_model');

    //$this->load->library('business_logic/auth_bl');
    
        //$this->auth_bl->check_session_exists();
    
     }

   function bankaccount_search(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shop_id_en = $this->input->post('shop_id_en');
      $shopid = $this->encryption_util->decrypt_ssl($shop_id_en);
      $accounttxt_search = $this->input->post('accounttxt_search');

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->web_bankaccount_model->select_by_shop_search($shopid,$accounttxt_search);

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

  function expense_add(){

      $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $expense_date = $this->input->post('expense_date');
        $expense_amount = $this->input->post('expense_amount');
        $expense_vat = $this->input->post('expense_vat');

        $supplier_type = $this->input->post('supplier_type');
        $web_supplier_id = $this->input->post('web_supplier_id');
        $supplier_name = $this->input->post('supplier_name');

        $account_type = $this->input->post('account_type');
        $web_bankaccount_id = $this->input->post('web_bankaccount_id');
        $bankaccount_id = $this->input->post('bankaccount_id');
        $bookbank_number = $this->input->post('bookbank_number');
        $bookbank_name = $this->input->post('bookbank_name');

        //$slip_amount = $this->input->post('slip_amount');
        //$slip_date_time = $this->input->post('slip_date_time');
        $slip_pic = $this->input->post('slip_pic');

        $invoice_pic = $this->input->post('invoice_pic');

        $ShopID_en = $this->input->post('shop_id_en');
        $ShopID = $this->encryption_util->decrypt_ssl($ShopID_en);

        $web_purchase_order_id = $this->input->post('web_purchase_order_id');

        if($supplier_type == 2){

          $data_sup = array(
            'supplier_name' => $supplier_name,
            'ShopID' => $ShopID
          );

          $web_supplier_id = $this->web_supplier_model->insert($data_sup);

        }

        if($account_type == 2){

          $bankaccount_id = $this->input->post('bankaccount_id');
          $bookbank_number = $this->input->post('bookbank_number');
          $bookbank_name = $this->input->post('bookbank_name');

          $data_book = array(
            'bankaccount_id' => $bankaccount_id,
            'bookbank_number' => $bookbank_number,
            'bookbank_name' => $bookbank_name,
            'ShopID' => $ShopID
          );

          $web_bankaccount_id = $this->web_bankaccount_model->insert($data_book);

        }

        $data_expense = array(
          'expense_date' => $expense_date,
          'web_purchase_order_id' => $web_purchase_order_id,
          'web_supplier_id' => $web_supplier_id,
          'web_bankaccount_id' => $web_bankaccount_id,
          'expense_amount' => $expense_amount,
          'expense_vat' => $expense_vat,
          'ShopID' => $ShopID
        );

        $expense_id = $this->bny_expense_model->insert($data_expense);

        $data_slip = array(
          'bny_expense_id' => $expense_id,
          'slip_type' => 'Out',
          //'slip_amount' => $slip_amount,
          //'slip_date' => $slip_date_time,
          'slip_pic' => $slip_pic,
          'ShopID' => $ShopID
        );

       $this->web_slip_model->insert($data_slip);

       $data_invoice = array(
          'bny_expense_id' => $expense_id,
          'invoice_pic' => $invoice_pic,
          'ShopID' => $ShopID
        );

       $data_re = $this->web_invoice_model->insert($data_invoice);

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

  function get_by_expense_id_join_slip(){

    $arr_header = $this->api_auth_bl->get_header();

      $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
      //print_r($chk_auth);
      //echo $chk_auth;
      if($chk_auth['Status'] == "Success"){

        $bny_expense_id_en = $this->uri->segment(3);  
        $bny_expense_id = $this->encryption_util->decrypt_ssl($bny_expense_id_en);  

        $data_re = $this->bny_expense_model->get_by_expense_id_join_slip($bny_expense_id);

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

  function loaddata_more(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $shopid_en = $this->input->post('shopid_en'); 
      $shopid = $this->encryption_util->decrypt_ssl($shopid_en);

      $expense_search = $this->input->post('expense_search');
      $expense_start = $this->input->post('expense_start');
      $expense_stop = $this->input->post('expense_stop');

      $sortby = $this->input->post('sortby');
      $sorttype = $this->input->post('sorttype');
      $offset = $this->input->post('offset');
      $per_page = $this->input->post('per_page');

      $arr_search = array(
        'expense_search' => $expense_search,
        'expense_start' => $expense_start,
        'expense_stop' => $expense_stop
      );

     // echo $shopid_en."-->".$shopid."<--";

      $data = $this->bny_expense_model->select_by_shop_id_search($shopid,$arr_search,$per_page,$offset,$sortby,$sorttype);

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