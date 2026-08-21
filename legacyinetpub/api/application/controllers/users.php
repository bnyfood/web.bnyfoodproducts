<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
** [C]Member Controller : is controller about Member of phuketgoodjob.
**/
class Users extends CI_Controller
{

    function __construct()
  {
    //:[Auto call parent construct]
    parent::__construct();
    //@@@:[Load Model, Business Logic (library) for prepare before use in controller function]

    $this->load->library('util/random_util');
    $this->load->library('util/encryption_util');
    $this->load->library('util/json_util');
    $this->load->library('businesslogic/api_log_bl');
    $this->load->library('businesslogic/api_auth_bl');
    $this->load->library('businesslogic/data_prepare_bl');
    $this->load->library('businesslogic/curl_bl');

    $this->load->model('api_authen_model');
    $this->load->model('web_bny_customer_model');
    $this->load->model('google_login_model');
        
     }
  
  function user_login(){
    
    $txt_email = $this->input->post('txt_email');
    $password = $this->input->post('txt_password');

    //$user_name = 'admin';
    //$password = '1234567';

    $password = md5(SALT_PASSWORD.$password);
   // echo $password;

    $user_login = $this->api_authen_model->select_by_email_password($txt_email,$password);
    //print_r($user_login);
    $token = "";

    if(empty($user_login)){
 
    $data_json = $this->json_util->make_json('Login','Fail','','Invalid Login',$token); 

    }else{

      $token = $this->api_auth_bl->create_token($user_login);
      //echo $token;

      $arr_user = array(
        'token' => $token,
        'token_cdate' => DATE_TIME_NOW,
      );     

      $this->api_authen_model->update($arr_user,$user_login['ApiAuthenID']);

      $data_user = $this->web_bny_customer_model->select_by_id_join_profile($user_login['BNYCustomerID']);

      $data_group = $this->web_bny_customer_model->select_multi_group($user_login['BNYCustomerID']);

      $data_re = array(
        'data_user' => $data_user,
        'data_group' => $data_group
      );

      $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',$token); 

      $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],$token);
    } 

    
    echo $data_json['view'];
    
  }

  function user_register(){
    
    $txt_name = $this->input->post('txt_name');
    $password = $this->input->post('txt_password');
    $password = md5(SALT_PASSWORD.$password);
    //$usergroup_id = $this->input->post('usergroup_id');
    $txt_email = $this->input->post('txt_email');
    $customer_type = $this->input->post('customer_type');
    $customer_code = $this->input->post('customer_code');

    $arr_user = array(
      'usergroup_id' => '',
      'Name' => $txt_name,
      'email' => $txt_email,
      'customer_type' => $customer_type,
      'customer_code' => $customer_code,
      'customer_type' => 5
    );

    $data_re = $this->web_bny_customer_model->insert($arr_user);

    $this->data_prepare_bl->setdata_register($data_re,$txt_name,$customer_code);

    $arr_auth_user = array(
      'BNYCustomerID' => $data_re,
      'email' => $txt_email,
      'password' => $password
    );

   $data_au_re =  $this->api_authen_model->insert($arr_auth_user);

   $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',''); 

   $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],'');

   echo $data_json['view'];
    
  }


  function user_register_phone(){
    
    $txt_name = $this->input->post('txt_name');
    $txt_phone = $this->input->post('txt_phone');
    $txt_email = $this->input->post('txt_email');
    $customer_code = $this->encryption_util->create_code(20);

    $arr_user = array(
      'Name' => $txt_name,
      'Mobile' => $txt_phone,
      'email' => $txt_email,
      'customer_code' => $customer_code,
      'customer_type' => 5
    );

    $data_re = $this->web_bny_customer_model->insert($arr_user);

    $this->data_prepare_bl->setdata_register($data_re,$txt_name,$customer_code);

    $arr_auth_user = array(
      'BNYCustomerID' => $data_re,
      'email' => $txt_email
    );

   $data_au_re =  $this->api_authen_model->insert($arr_auth_user);

   $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',''); 

   $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],'');

   echo $data_json['view'];
    
  }

  function login_req_otp(){
    $txt_phone = $this->input->post('txt_phone');

    $ran_otp = $this->random_util->create_random_number(6); 

    //$this->curl_bl->curl_sms('0875824451','test_by_mon','Demo','corporate');

    $data = array(
      'otp' => $ran_otp,
      'otp_cdate' => DATE_TIME_NOW
    );

    $this->web_bny_customer_model->update_by_phone($data,$txt_phone);

    $data_json = $this->json_util->make_json('Login','Success','','Login Success',''); 

   $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],'');

   echo $data_json['view'];

  }

  function login_send_otp(){

    $txt_otp = $this->input->post('txt_otp');
    $phone_no = $this->input->post('phone_no');

    $user_login = $this->web_bny_customer_model->select_by_phone_otp($phone_no,$txt_otp);
    $token = $this->api_auth_bl->create_token($user_login);

    if(!empty($user_login)){

      if($user_login['otp_time'] < 60){

      $arr_user = array(
        'token' => $token,
        'token_cdate' => DATE_TIME_NOW,
      );     

      $this->api_authen_model->update($arr_user,$user_login['ApiAuthenID']);

      $data_user = $this->web_bny_customer_model->select_by_id_join_profile($user_login['BNYCustomerID']);

      $data_group = $this->web_bny_customer_model->select_multi_group($user_login['BNYCustomerID']);

      $data_re = array(
        'data_user' => $data_user,
        'data_group' => $data_group
      );

      $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',$token); 

      $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],$token);

      echo $data_json['view'];

      
      }else{

        $data_json = $this->json_util->make_json('Login','Fail',NULL,'Login OTP Expire',$token); 

        $this->api_log_bl->create_log('Login','Fail',current_url(),$data_json['log'],$token);

        echo $data_json['view'];

      }
    }else{

      $data_json = $this->json_util->make_json('Login','Fail',NULL,'Login Fail',$token); 

      $this->api_log_bl->create_log('Login','Fail',current_url(),$data_json['log'],$token);

      echo $data_json['view'];

    }

  }

  function get_profile(){
    

    //$token = "Unl0OTVPbDh3a2g5dUdjdU00L0hRaU50bktnMU5YdW0yTkVRakhveGZaZUZ1bnBTWjVxZWcvVjdIdnpZdlNaZHU0bVpZRjNPMTNISW5VZ3FmYitDNXVWbEtTVW9MVkRXYWhic3BiT0ozelk9";

    $id =  $this->uri->segment(3);

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    //echo $chk_auth;
    if($chk_auth['Status'] == "Success"){

      $data_user = $this->api_authen_model->select_by_authen_id($id);
      if(!empty($data_user)){
        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',$arr_header['api_token']); 
      }else{
        $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select No data',$arr_header['api_token']); 
      }
      
      echo $data_json['view'];

    }else{

     $chk_auth = $this->json_util->json_unicode($chk_auth);
     echo $chk_auth;

    }
  

    //if()

    //$de_token = $this->api_auth_bl->de_token($token);
   // echo $de_token;
  }

  function user_chk_username_invalid(){

    $txt_email = $this->input->post('txt_email');

    $data_re = $this->api_authen_model->select_by_email($txt_email);

    if(!empty($data_usergroup)){
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',''); 
    }else{
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',''); 
    }
    
    echo $data_json['view'];

  }

  function user_chk_phone_invalid(){

    $txt_phone = $this->input->post('txt_phone');

    $data_re = $this->web_bny_customer_model->select_by_phone(trim($txt_phone));

    //print_r($data_re);

    if(!empty($data_re)){
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',''); 
    }else{
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',''); 
    }
    
    echo $data_json['view'];

  }

  function user_chk_email_invalid(){

    $txt_email = $this->input->post('txt_email');

    $data_re = $this->web_bny_customer_model->select_by_email(trim($txt_email));

    //print_r($data_re);

    if(!empty($data_re)){
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select Success',''); 
    }else{
      $data_json = $this->json_util->make_json('Select data','Success',$data_re,'Select No data',''); 
    }
    
    echo $data_json['view'];

  }

  public function permission()
  {

    echo "412757";
    $api_status = $this->session->flashdata('api_status');

    echo ">>>aaa<<<>>>".$api_status;

  }

  public function getby_google_id(){

    $id = $this->input->post('id');


    $data_user = $this->google_login_model->getby_google_id($id);

    if(!empty($data_user)){
      $data_json = $this->json_util->make_json('Select data','Success',$data_user,'Select Success',''); 
    }else{
      $data_json = $this->json_util->make_json('Select data','Fail',NULL,'Select No data',''); 
    }
    
    echo $data_json['view'];


  }

  function insert_google(){
    
    $google_id = $this->input->post('google_id');
    $name = $this->input->post('name');
    $email = $this->input->post('email');
    $profile_image = $this->input->post('profile_image');
    $usergroup_id_en = $this->input->post('usergroup_id_en');


    $arr_data_google = array(
      'google_id' => $google_id,
      'name' => $name,
      'email' => $email,
      'profile_image' => $profile_image,
      'usergroup_id_en' => $usergroup_id_en
    );

    //print_r($arr_data_google);

    $arr_data_insert = array(
      'google_id' => $google_id,
      'name' => $name,
      'email' => $email,
      'profile_image' => $profile_image
    );

    $google_chk = $this->google_login_model->getby_email($email);

    if(empty($google_chk)){

      $data_re = $this->google_login_model->insert($arr_data_insert);

     // echo $data_re;

      $this->create_from_google($arr_data_google);
    }else{
      $data_re = $this->web_bny_customer_model->get_by_email($email);
    }

   $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',''); 

   $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],'');

   echo $data_json['view'];
    
  }

  function create_from_google($arr_data){

    $chk_google_email = $this->web_bny_customer_model->get_by_email($arr_data['email']);

    if(empty($chk_google_email)){

      $customer_code = $this->encryption_util->create_code(20);

      $user_lv= 1;
      if($arr_data['usergroup_id_en'] != ""){
        $user_lv= 2;
      }

      $arr_user = array(
        'google_id' => $arr_data['google_id'],
        //'user_level_id' => $user_lv,
        'Name' => $arr_data['name'],
        'email' => $arr_data['email'],
        'customer_type' => 5,
        'customer_code' => $customer_code
      );

      $data_re = $this->web_bny_customer_model->insert($arr_user);

      if($arr_data['usergroup_id_en'] != ""){
        $this->data_prepare_bl->setdata_register_user($data_re,$arr_data['usergroup_id_en'],$user_lv);
      }else{
        //create new shop
        $this->data_prepare_bl->setdata_register($data_re,$arr_data['name'],$customer_code,$user_lv);
      }

      $arr_auth_user = array(
        'BNYCustomerID' => $data_re,
        'google_id' => $arr_data['google_id'],
        'email' => $arr_data['email']
      );

     $this->api_authen_model->insert($arr_auth_user);
   }

  }

  function user_login_google(){

    $google_id = $this->input->post('google_id');

    $user_login = $this->api_authen_model->select_by_google_id($google_id);
    //print_r($user_login);
    $token = "";

    if(empty($user_login)){
 
    $data_json = $this->json_util->make_json('Login','Fail','','Invalid Login',$token); 

    }else{

      $token = $this->api_auth_bl->create_token($user_login);
      //echo $token;

      $arr_user = array(
        'token' => $token,
        'token_cdate' => DATE_TIME_NOW,
      );     

      $this->api_authen_model->update($arr_user,$user_login['ApiAuthenID']);

      $data_user = $this->web_bny_customer_model->select_by_id_join_profile($user_login['BNYCustomerID']);

      $data_group = $this->web_bny_customer_model->select_multi_group($user_login['BNYCustomerID']);

      $data_re = array(
        'data_user' => $data_user,
        'data_group' => $data_group
      );

      $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',$token); 

      //print_r($data_json);

      $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],$token);
    } 
    
    echo $data_json['view'];

  }

  function authen_user_login(){
    
    $txt_email_en = $this->input->post('txt_email');
    $password_en = $this->input->post('txt_password');

    $txt_email = $this->encryption_util->decrypt_ssl($txt_email_en);
    $password = $this->encryption_util->decrypt_ssl($password_en);

    //$user_name = 'admin';
    //$password = '1234567';

    $password = md5(SALT_PASSWORD.$password);
   // echo $password;

    $user_login = $this->api_authen_model->select_by_email_password($txt_email,$password);
    //print_r($user_login);
    $token = "";

    if(empty($user_login)){
 
    $data_json = $this->json_util->make_json('Login','Fail','','Invalid Login',$token); 

    }else{

      $token = $this->api_auth_bl->create_token($user_login);
      //echo $token;

      $arr_user = array(
        'token' => $token,
        'token_cdate' => DATE_TIME_NOW,
      );     

      $this->api_authen_model->update($arr_user,$user_login['ApiAuthenID']);

      $data_user = $this->web_bny_customer_model->select_by_id_join_profile($user_login['BNYCustomerID']);

      $data_group = $this->web_bny_customer_model->select_multi_group($user_login['BNYCustomerID']);

      $data_re = array(
        'data_user' => $data_user,
        'data_group' => $data_group
      );

      $data_json = $this->json_util->make_json('Login','Success',$data_re,'Login Success',$token); 

      $this->api_log_bl->create_log('Login','Success',current_url(),$data_json['log'],$token);
    } 

    
    echo $data_json['view'];
    
  }

  function chk_token(){

    $arr_header = $this->api_auth_bl->get_header();

    $chk_auth = $this->api_auth_bl->authen_token($arr_header['api_token']);
    //print_r($chk_auth);
    
    $chk_auth = $this->json_util->json_unicode($chk_auth);
    echo $chk_auth;
    
  }

  
}
/* End of file users.php */
/* Location: ./application/controllers/users.php */


