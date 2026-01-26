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
		$this->load->library('util/View_util');
		$this->load->library('util/random_util');
		$this->load->library('util/encryption_util');

     }
    
    public function permission()
	{

		$arr_input = array(
			'title' => "User permission"
		);
      
	        
		$this->view_util->load_view_permission('users/permission');

	}

	function test(){
		echo "123456";
	}

	public function login()
	{
		
		//$ck_red_redirect_path = $this->input->cookie('ck_red_redirect_path');
		//echo $ck_red_redirect_path;

		//$password = md5(SALT_PASSWORD.'12345678');
		//echo $password;

		//$is_login = $this->uri->segment(3);
		$is_login = $this->session->flashdata('login_msg');
		$arr_input = array(
			'title' => "User login"
		);
				        	
        $arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'login' => base_url()."resources/js/validate/login.js",
        	'recaptcha' => base_url()."resources/js/google/recaptcha.js",
        	'login_js' => base_url()."resources/js/users/login_js.js"
    	);	
        	        
		$data = array(
			'is_login' => $is_login
		);		        
		$this->view_util->load_view_login('users/login_form', $data,NULL,$arr_js,$arr_input);

	}

	public function logined($section=NULL){

		$logon_res = false;

		$cha_response = $this->input->post('cha_response');

		if(isset($cha_response)){
			$captcha=$cha_response;
			$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".GOOGLE_CAPTCHA_SECRETKEY."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
			if(!$captcha){
				$missinginputsecret = ["The response parameter is missing."];
				print_r($missinginputsecret[0]);
			}        
		}

		/*if($response['success'] != true){
			$logon_res = false;
		}else{*/

	if($response['success'] == true){
		$txt_email = $this->input->post('txt_email');
		$password = $this->input->post('txt_password');
		//$password = md5(SALT_PASSWORD.$password);

		//$user_login = $this->users_model->select_by_user_password($user_name,$password);
		$data_curl = array('txt_email' => $txt_email, 'txt_password' => $password);
		//print_r($data_curl);
		$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/user_login',$data_curl);

		//print_r($arr_user_login);

		$user_login  = $arr_user_login['Data']['data_user'];

		$data_multigroup = $arr_user_login['Data']['data_group'];
		//echo ">>>>>user>>>>>";
		//print_r($user_login);
		//echo "<<<<<user<<<<<";
		
		$this->load->helper('cookie');
		if(empty($user_login)){
			//echo "login fail";
			$this->session->set_flashdata('login_msg','fail');
			//redirect(base_url().'users/login','refresh');
			$logon_res = false;
		}else{
			//echo "login success";
			$autologin = $this->input->post('remember');
			//echo $autologin;


			$datenow = date("YmdHis");			
			$ran_num = $this->random_util->create_random_number(6);	
			$session_id = $user_login['BNYCustomerID'].$datenow.$ran_num;
			$cookie_sessionid = array(
		    'name'   => COOKIE_PREFIX.'cookie_sessionid',
		    'value'  => $session_id,
		    'expire' => 86400,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$cookie_bnyu = array(
		    'name'   => COOKIE_PREFIX.'cookie_bnyu',
		    'value'  => $this->encryption_util->encrypt_ssl($txt_email),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$cookie_bnyp = array(
		    'name'   => COOKIE_PREFIX.'cookie_bnyp',
		    'value'  => $this->encryption_util->encrypt_ssl($password),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			//echo $autologin;
			$cookie_userid = array(
		    'name'   => COOKIE_PREFIX.'cookie_userid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_usergroupid = array(
		    'name'   => COOKIE_PREFIX.'cookie_usergroupid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['usergroup_id']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_multigroup = array(
		    'name'   => COOKIE_PREFIX.'cookie_multigroup',
		    'value'  => $this->encryption_util->encrypt_ssl(json_encode($data_multigroup)),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_company = array(
		    'name'   => COOKIE_PREFIX.'cookie_company',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['CompanyName']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_user_name = array(
		    'name'   => COOKIE_PREFIX.'cookie_user_name',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['Name']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_email = array(
		    'name'   => COOKIE_PREFIX.'cookie_email',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['email']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_mobile = array(
		    'name'   => COOKIE_PREFIX.'cookie_mobile',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['Mobile']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_token= array(
		    'name'   => COOKIE_PREFIX.'cookie_token',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['token']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_customer_code= array(
		    'name'   => COOKIE_PREFIX.'cookie_customer_code',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_code']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_shopid= array(
		    'name'   => COOKIE_PREFIX.'cookie_shopid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['ShopID']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_customer_type= array(
		    'name'   => COOKIE_PREFIX.'cookie_customer_type',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_type']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$cookie_user_level_id= array(
		    'name'   => COOKIE_PREFIX.'cookie_user_level_id',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['level_id']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);


			$this->input->set_cookie($cookie_sessionid);
			$this->input->set_cookie($cookie_userid);
			$this->input->set_cookie($cookie_usergroupid);
			$this->input->set_cookie($cookie_multigroup);
			$this->input->set_cookie($cookie_company);
			$this->input->set_cookie($cookie_user_name);
			$this->input->set_cookie($cookie_email);
			$this->input->set_cookie($cookie_mobile);
			$this->input->set_cookie($cookie_token);
			$this->input->set_cookie($cookie_customer_code);
			$this->input->set_cookie($cookie_shopid);
			$this->input->set_cookie($cookie_customer_type);
			$this->input->set_cookie($cookie_user_level_id);

			$this->input->set_cookie($cookie_bnyu);
			$this->input->set_cookie($cookie_bnyp);


			$this->session->set_userdata(SESSION_PREFIX.'session_gen_id',$session_id);
			$this->session->set_userdata(SESSION_PREFIX.'session_bnyu',$txt_email);
			$this->session->set_userdata(SESSION_PREFIX.'session_bnyp',$password);
			$this->session->set_userdata(SESSION_PREFIX.'user_id',$this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']));
			$this->session->set_userdata(SESSION_PREFIX.'usergroup_id',$this->encryption_util->encrypt_ssl($user_login['usergroup_id']));
			$this->session->set_userdata(SESSION_PREFIX.'multigroup',$data_multigroup);
			$this->session->set_userdata(SESSION_PREFIX.'company',$user_login['CompanyName']);
			$this->session->set_userdata(SESSION_PREFIX.'username',$user_login['Name']);
			$this->session->set_userdata(SESSION_PREFIX.'email',$user_login['email']);
			$this->session->set_userdata(SESSION_PREFIX.'mobile',$user_login['Mobile']);
			$this->session->set_userdata(SESSION_PREFIX.'token',$user_login['token']);
			$this->session->set_userdata(SESSION_PREFIX.'customer_code',$user_login['customer_code']);
			$sess_shop_id = $this->encryption_util->encrypt_ssl($user_login['ShopID']);
			$this->session->set_userdata(SESSION_PREFIX.'shop_id',$sess_shop_id);
			$this->session->set_userdata(SESSION_PREFIX.'customer_type',$user_login['customer_type']);
			$this->session->set_userdata(SESSION_PREFIX.'user_level_id',$user_login['level_id']);
			//echo $user_login['customer_code']."---".$user_login['BNYCustomerID'];

			$shopid = $this->encryption_util->decrypt_ssl($sess_shop_id);

			//echo $user_login['ShopID']."---".$sess_shop_id."---".$shopid;
			//redirect(base_url()."monitor/main",'refresh');

			$logon_res = true;
		}

		}

		$data = array(
			'logon_res' => $logon_res
		);

		echo json_encode($data);
		
	}
	
	public function logout(){ 
	//$redirec_path = $this->session->userdata("red_redirect_path");
	//echo $redirec_path;
		//$session_id = $this->session->userdata('session_gen_id');
		//$user_id = $this->session->userdata('user_id');

		
		$this->session->unset_userdata(SESSION_PREFIX.'session_gen_id');
		$this->session->unset_userdata(SESSION_PREFIX.'user_id');
		$this->session->unset_userdata(SESSION_PREFIX.'usergroup_id');
		$this->session->unset_userdata(SESSION_PREFIX.'company');
		$this->session->unset_userdata(SESSION_PREFIX.'username');
		$this->session->unset_userdata(SESSION_PREFIX.'email');
		$this->session->unset_userdata(SESSION_PREFIX.'mobile');
		$this->session->unset_userdata(SESSION_PREFIX.'token');
		$this->session->unset_userdata(SESSION_PREFIX.'customer_code');
		$this->session->unset_userdata(SESSION_PREFIX.'shop_id');
		$this->session->unset_userdata(SESSION_PREFIX.'customer_type');
		$this->session->unset_userdata(SESSION_PREFIX.'user_level_id');

		$this->session->sess_destroy();

		$this->load->helper('cookie');

		delete_cookie(COOKIE_PREFIX.'sessionid');
		delete_cookie(COOKIE_PREFIX.'userid');
		delete_cookie(COOKIE_PREFIX.'usergroupid');
		delete_cookie(COOKIE_PREFIX.'company');  
		delete_cookie(COOKIE_PREFIX.'user_name');
		delete_cookie(COOKIE_PREFIX.'email');
		delete_cookie(COOKIE_PREFIX.'mobile');
		delete_cookie(COOKIE_PREFIX.'token');
		delete_cookie(COOKIE_PREFIX.'customer_code');
		delete_cookie(COOKIE_PREFIX.'shopid');
		delete_cookie(COOKIE_PREFIX.'customer_type');
		delete_cookie(COOKIE_PREFIX.'user_level_id');
		
		redirect(base_url().'users/login_with_google', 'refresh');
	}

	public function login_phone()
	{
		
		$is_login = $this->session->flashdata('login_msg');
		$arr_input = array(
			'title' => "User login"
		);
				        	
        $arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'login_phone' => base_url()."resources/js/validate/login_phone.js"
    	);	
        	        
		$data = array(
			'is_login' => $is_login
		);		        
		$this->view_util->load_view_login('users/login_phone_form', $data,NULL,$arr_js,$arr_input);

	}

	public function login_req_otp(){

		$txt_phone = $this->input->post('txt_phone');

		$data_curl = array(
			'txt_phone' => $txt_phone
		);

			//print_r($data);

			$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/login_req_otp',$data_curl);

			redirect(base_url().'users/login_phone_otp_form/'.$txt_phone, 'refresh');
	}

	public function login_phone_otp_form()
	{
		$phone_no = $this->uri->segment(3);

		$is_login = $this->session->flashdata('login_msg');
		$arr_input = array(
			'title' => "User login"
		);
				        	
        $arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'login_otp' => base_url()."resources/js/validate/login_otp.js"
    	);	
        	        
		$data = array(
			'phone_no' => $phone_no
		);		        
		$this->view_util->load_view_login('users/login_phone_otp_form', $data,NULL,$arr_js,$arr_input);

	}

	public function login_send_otp(){

		$txt_otp = $this->input->post('txt_otp');
		$phone_no = $this->input->post('phone_no');

		$data_curl = array(
			'txt_otp' => $txt_otp,
			'phone_no' => $phone_no
		);

		//print_r($data);

		$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/login_send_otp',$data_curl);

		//print_r($arr_user_login);


		$user_login  = $arr_user_login['Data']['data_user'];

		$data_group = $arr_user_login['Data']['data_group'];
		//echo "------------";
		//print_r($data_group);

		if(empty($user_login)){
			echo "login fail";
				$this->session->set_flashdata('login_msg',$arr_user_login['Description']);
				redirect(base_url().'users/login_phone','refresh');
		}else{
			echo "login success";

			$datenow = date("YmdHis");			
				$ran_num = $this->random_util->create_random_number(6);	
				$session_id = $user_login['BNYCustomerID'].$datenow.$ran_num;
				$cookie_sessionid = array(
			    'name'   => 'cookie_sessionid',
			    'value'  => $session_id,
			    'expire' => 86400,
			    'path'   => '/',
			    'secure' => FALSE
				);
				//echo $autologin;
				$cookie_userid = array(
			    'name'   => 'cookie_userid',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_usergroupid = array(
			    'name'   => 'cookie_usergroupid',
			    'value'  => $this->encryption_util->encrypt_ssl($data_group[0]['usergroup_id']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_user_name = array(
			    'name'   => 'cookie_user_name',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['Name']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_email = array(
			    'name'   => 'cookie_email',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['email']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_mobile = array(
			    'name'   => 'cookie_mobile',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['Mobile']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_token= array(
			    'name'   => 'cookie_token',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['token']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);



				$this->input->set_cookie($cookie_sessionid);
				$this->input->set_cookie($cookie_userid);
				$this->input->set_cookie($cookie_usergroupid);
				$this->input->set_cookie($cookie_user_name);
				$this->input->set_cookie($cookie_email);
				$this->input->set_cookie($cookie_mobile);
				$this->input->set_cookie($cookie_token);

				$this->session->set_userdata('session_gen_id',$session_id);
				$this->session->set_userdata('user_id',$this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']));
				$this->session->set_userdata('usergroup_id',$this->encryption_util->encrypt_ssl($data_group[0]['usergroup_id']));
				$this->session->set_userdata('username',$user_login['Name']);
				$this->session->set_userdata('email',$user_login['email']);
				$this->session->set_userdata('mobile',$user_login['Mobile']);
				$this->session->set_userdata('token',$user_login['token']);


				/*$token_en = get_cookie('cookie_token');
		$token = $this->encryption_util->decrypt_ssl($token_en);
		echo ">>>>".$token."<<<<";*/

				redirect(base_url()."monitor/main",'refresh');

		}

		//redirect(base_url().'users/login', 'refresh');

	}

	public function register()
	{

		$term_alt = $this->session->flashdata('term_alt');

		$arr_input = array(
			'title' => "User login"
		);
				        

        $arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	//'register' => base_url()."resources/js/validate/register.js"
    	);	

        $data = array(
        	'term_alt' => $term_alt
        );
        	        
		        
		$this->view_util->load_view_register('users/register_form', $data,NULL,$arr_js,$arr_input);

	}

	public function registed(){

		$txt_name = $this->input->post('txt_name');
		$txt_email = $this->input->post('txt_email');
		$txt_password = $this->input->post('txt_password');
		$term = $this->input->post('term');
		$usergroup_id = USER_GROUP_DEFULT_GROUP;

		$customer_code = $this->encryption_util->create_code(20);

		if($term == "1"){
			$data_curl = array(
				'txt_name' => $txt_name,
				'txt_password' => $txt_password,
				'txt_email' => $txt_email,
				'usergroup_id' => $usergroup_id,
				'customer_type' => 2,
				'customer_code' => $customer_code
			);

			//print_r($data);

			$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/user_register',$data_curl);

			redirect(base_url().'users/login', 'refresh');

		}else{

			$this->session->set_flashdata('term_alt','fail');
			redirect(base_url().'users/register', 'refresh');
		}
	}

	public function register_with_phone()
	{

		$term_alt = $this->session->flashdata('term_alt');

		$arr_input = array(
			'title' => "User login"
		);
				        

        $arr_js = array(
			'validate' => base_url()."assets/js/jquery.validate.min.js",	
        	'register_with_phone' => base_url()."resources/js/validate/register_with_phone.js"
    	);	

        $data = array(
        	'term_alt' => $term_alt
        );
        	        
		        
		$this->view_util->load_view_register('users/register_with_phone', $data,NULL,$arr_js,$arr_input);

	}

	public function registed_phone(){

		$txt_name = $this->input->post('txt_name');
		$txt_phone = $this->input->post('txt_phone');
		$txt_email = $this->input->post('txt_email');
		
		$term = $this->input->post('term');
		$usergroup_id = USER_GROUP_DEFULT_GROUP;

		$customer_code = $this->encryption_util->create_code(20);

		if($term == "1"){
			$data_curl = array(
				'txt_name' => $txt_name,
				'txt_phone' => $txt_phone,
				'txt_email' => $txt_email,
				'usergroup_id' => $usergroup_id,
				'customer_type' => 2,
				'customer_code' => $customer_code
			);

			//print_r($data);

			$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/user_register_phone',$data_curl);

			redirect(base_url().'users/login_phone', 'refresh');

		}else{

			$this->session->set_flashdata('term_alt','fail');
			redirect(base_url().'users/register_with_phone', 'refresh');
		}
	}

	function user_chk_username_invalid(){

		$txt_email = $this->input->post('txt_email');

		$data_curl = array(
			'txt_email' => $txt_email
		);

		$arr_res = $this->curl_bl->call_curl_notoken('POST','users/user_chk_username_invalid',$data_curl);
		//print_r($arr_res['Data']);
		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}


	}


	function user_chk_phone_invalid(){

		$txt_phone = $this->input->post('txt_phone');

		$data_curl = array(
			'txt_phone' => $txt_phone
		);

		$arr_res = $this->curl_bl->call_curl_notoken('POST','users/user_chk_phone_invalid',$data_curl);
		print_r($arr_res['Data']);
		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}


	}

	function user_chk_email_invalid(){

		$txt_email = $this->input->post('txt_email');

		$data_curl = array(
			'txt_email' => $txt_email
		);

		$arr_res = $this->curl_bl->call_curl_notoken('POST','users/user_chk_email_invalid',$data_curl);
		print_r($arr_res['Data']);
		if(empty($arr_res['Data'])){
			echo 'true';
		}else{
			echo 'false';
		}


	}

	function login_with_google(){

		$usergroup_id_en = $this->uri->segment(3);

		if($usergroup_id_en != ""){

			$this->load->helper('cookie');
			$google_usergroup_id_en = array(
			    'name'   => COOKIE_PREFIX.'google_usergroup_id_en',
			    'value'  => $usergroup_id_en,
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
			);

			$this->input->set_cookie($google_usergroup_id_en);
		}

		require __DIR__ . "/../../resources/api/google-api/vendor/autoload.php";

		//require 'google-api/vendor/autoload.php';
		// Creating new google client instance
		$client = new Google_Client();
		// Enter your Client ID
		$client->setClientId(GOOGLE_Client_ID);
		// Enter your Client Secrect
		$client->setClientSecret(GOOGLE_SECRET_KEY);
		// Enter the Redirect URL
		$client->setRedirectUri(GOOGLE_LOGIN_REDIRECT);
		// Adding those scopes which we want to get (email & profile Information)
		$client->addScope("email");
		$client->addScope("profile");


		if(isset($_GET['code'])):
	    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	    if(!isset($token["error"])){
	        $client->setAccessToken($token['access_token']);
	        // getting profile information
	        $google_oauth = new Google_Service_Oauth2($client);
	        $google_account_info = $google_oauth->userinfo->get();
	    
	        // Storing data into database
	        $id = $google_account_info->id;
	        $full_name = trim($google_account_info->name);
	        $email = $google_account_info->email;
	        $profile_pic = $google_account_info->picture;

	        // checking user already exists or not
	        
	        //$get_user = mysqli_query($db_connection, "SELECT `google_id` FROM `users` WHERE `google_id`='$id'");

	        //$get_user = $this->google_login_model->getby_google_id->($id);
	        $data_curl = array(
	        	'id' => $id
	        );
	        $get_user = $this->curl_bl->call_curl_notoken('POST','users/getby_google_id',$data_curl);
	        if(!empty($get_user['Data'])){
	            $_SESSION['login_id'] = $id; 

	            //echo $id;
	            //header('Location: home.php');
	            redirect(base_url()."users/logined_with_google",'refresh');
	            exit;
	        }
	        else{
	        	$google_usergroup_id_en = get_cookie(COOKIE_PREFIX.'google_usergroup_id_en');
	        	//echo "----".$google_usergroup_id_en;
	        	
	        	//------------------
	            // if user not exists we will insert the user
	            //$insert = mysqli_query($db_connection, "INSERT INTO `users`(`google_id`,`name`,`email`,`profile_image`) VALUES('$id','$full_name','$email','$profile_pic')");
	            $arr_data_insert = array(
	            	'google_id' => $id,
	            	'name' => $full_name,
	            	'email' => $email,
	            	'profile_image' => $profile_pic,
	            	'usergroup_id_en' => $google_usergroup_id_en
	            );
	           //$insert = $this->google_login_model->insert($arr_data_insert);

	           $insert = $this->curl_bl->call_curl_notoken('POST','users/insert_google',$arr_data_insert);

	           delete_cookie(COOKIE_PREFIX.'google_usergroup_id_en');

	           $_SESSION['login_id'] = $id; 
	                //header('Location: home.php');
	                redirect(GOOGLE_LOGIN_SUCCESS_REDIRECT,'refresh');

	          // print_r($insert);

	            if(!empty($insert['Data'])){
	                $_SESSION['login_id'] = $id; 
	                //header('Location: home.php');
	                //users/logined_with_google
	                redirect(GOOGLE_LOGIN_SUCCESS_REDIRECT,'refresh');
	                exit;
	            }
	            else{
	            	print_r($insert);
	                echo "Sign up failed!(Something went wrong).";
	            }
	            //--------------------------
	            
	        }
	    }
	    else{
	        //header('Location: login.php');
	        redirect(GOOGLE_LOGIN_REDIRECT,'refresh');
	        exit;
	    }

	    else: 

	    	$arr_input = array(
				'title' => "User login"
			);

	    	$data = array(
	    		'client' => $client
	    	);

	    	$this->view_util->load_view_login('users/login_with_google', $data,NULL,NULL,$arr_input);
	    endif;	

	}

	function login_with_google_pop(){

		$id = $this->input->post('id');
        $full_name = $this->input->post('full_name');
        $email = $this->input->post('email');
        $profile_pic = $this->input->post('profile_pic');

		$is_login = false;

	    if(!empty($id)){

	        $data_curl = array(
	        	'id' => $id
	        );
	        //print_r($data_curl);
	        $get_user = $this->curl_bl->call_curl_notoken('POST','users/getby_google_id',$data_curl);
	        if(!empty($get_user['Data'])){
	            $_SESSION['login_id'] = $id; 

	            $this->logined_with_google();
	            $is_login = true;
	        }else{
	            // if user not exists we will insert the user
	            //$insert = mysqli_query($db_connection, "INSERT INTO `users`(`google_id`,`name`,`email`,`profile_image`) VALUES('$id','$full_name','$email','$profile_pic')");
	            $arr_data_insert = array(
	            	'google_id' => $id,
	            	'name' => $full_name,
	            	'email' => $email,
	            	'profile_image' => $profile_pic
	            );
	           //$insert = $this->google_login_model->insert($arr_data_insert);

	           $insert = $this->curl_bl->call_curl_notoken('POST','users/insert_google',$arr_data_insert);

	           $_SESSION['login_id'] = $id; 

	            if(!empty($insert['Data'])){
	                $_SESSION['login_id'] = $id; 
	                //header('Location: home.php');
	                $this->logined_with_google();
	                $is_login = true;
	                //<script> parent.window.close();</script>
	            }
	            else{
	            	//print_r($insert);
	                $is_login = false;
	            }
	        }
	    }else{
	        //header('Location: login.php');
	        $is_login = false;
	    }

	    $data = array(
			'is_login' => $is_login
		);

		echo json_encode($data);


	}

	function login_with_google_pop_bk(){

		require __DIR__ . "/../../resources/api/google-api/vendor/autoload.php";

		//require 'google-api/vendor/autoload.php';
		// Creating new google client instance
		$client = new Google_Client();
		// Enter your Client ID
		$client->setClientId(GOOGLE_Client_ID);
		// Enter your Client Secrect
		$client->setClientSecret(GOOGLE_SECRET_KEY);
		// Enter the Redirect URL
		$client->setRedirectUri(base_url().'users/login_with_google_pop');
		// Adding those scopes which we want to get (email & profile Information)
		$client->addScope("email");
		$client->addScope("profile");

		$is_login = false;

		if(isset($_GET['code'])):
	    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	    if(!isset($token["error"])){
	        $client->setAccessToken($token['access_token']);
	        // getting profile information
	        $google_oauth = new Google_Service_Oauth2($client);
	        $google_account_info = $google_oauth->userinfo->get();
	   
	        // Storing data into database
	        $id = $google_account_info->id;
	        $full_name = trim($google_account_info->name);
	        $email = $google_account_info->email;
	        $profile_pic = $google_account_info->picture;
	        // checking user already exists or not
	        
	        //$get_user = mysqli_query($db_connection, "SELECT `google_id` FROM `users` WHERE `google_id`='$id'");

	        //$get_user = $this->google_login_model->getby_google_id->($id);
	        $data_curl = array(
	        	'id' => $id
	        );
	        //print_r($data_curl);
	        $get_user = $this->curl_bl->call_curl_notoken('POST','users/getby_google_id',$data_curl);
	        if(!empty($get_user['Data'])){
	            $_SESSION['login_id'] = $id; 
	            //echo "here_1";
	            //header('Location: home.php');
	            //redirect(base_url()."users/logined_with_google",'refresh');
	            $this->logined_with_google();
	            //$is_login = false;
	            exit;
	        }
	        else{
	            // if user not exists we will insert the user
	            //$insert = mysqli_query($db_connection, "INSERT INTO `users`(`google_id`,`name`,`email`,`profile_image`) VALUES('$id','$full_name','$email','$profile_pic')");
	            $arr_data_insert = array(
	            	'google_id' => $id,
	            	'name' => $full_name,
	            	'email' => $email,
	            	'profile_image' => $profile_pic
	            );
	           //$insert = $this->google_login_model->insert($arr_data_insert);

	           $insert = $this->curl_bl->call_curl_notoken('POST','users/insert_google',$arr_data_insert);

	           $_SESSION['login_id'] = $id; 
	                //header('Location: home.php');
	                //redirect(base_url()."users/logined_with_google",'refresh');

	          // print_r($insert);

	            if(!empty($insert['Data'])){
	                $_SESSION['login_id'] = $id; 
	                //header('Location: home.php');
	                $this->logined_with_google();
	                $is_login = true;
	                //<script> parent.window.close();</script>
	                exit;
	            }
	            else{
	            	print_r($insert);
	                $is_login = false;
	            }
	        }
	    }
	    else{
	        //header('Location: login.php');
	        $is_login = false;
	        exit;
	    }

	    else: 

	    	$arr_input = array(
				'title' => "User login"
			);

	    	$data = array(
	    		'client' => $client
	    	);

	    	$this->view_util->load_view_blankpage('users/login_with_google_pop', $data,NULL,NULL,$arr_input);
	    endif;	

	}

	function logined_with_google(){
		//echo "Login Google Sucess";	

		//echo $_SESSION['login_id'];

		$google_id = $_SESSION['login_id'];

		$data_curl = array('google_id' => $google_id);
		//print_r($data_curl);
		$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/user_login_google',$data_curl);

		//print_r($arr_user_login);

		$user_login  = $arr_user_login['Data']['data_user'];

		$data_multigroup = $arr_user_login['Data']['data_group'];
		/*echo ">>>>>user>>>>>";
		print_r($user_login);
		echo "<<<<<user<<<<<";*/
		
		$this->load->helper('cookie');
		if(empty($user_login)){
			//echo "login fail";
				$this->session->set_flashdata('login_msg','fail');
				//redirect(base_url().'users/login_with_google','refresh');
		}else{
			//echo "login success";
			$autologin = $this->input->post('remember');
			//echo $autologin;


				$datenow = date("YmdHis");			
				$ran_num = $this->random_util->create_random_number(6);	
				$session_id = $user_login['BNYCustomerID'].$datenow.$ran_num;
				$cookie_sessionid = array(
			    'name'   => COOKIE_PREFIX.'sessionid',
			    'value'  => $session_id,
			    'expire' => 86400,
			    'path'   => '/',
			    'secure' => FALSE
				);
				//echo $autologin;
				$cookie_userid = array(
			    'name'   => COOKIE_PREFIX.'userid',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_usergroupid = array(
			    'name'   => COOKIE_PREFIX.'usergroupid',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['usergroup_id']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_multigroup = array(
			    'name'   => COOKIE_PREFIX.'multigroup',
			    'value'  => $this->encryption_util->encrypt_ssl(json_encode($data_multigroup)),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_company = array(
			    'name'   => COOKIE_PREFIX.'company',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['CompanyName']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_user_name = array(
			    'name'   => COOKIE_PREFIX.'user_name',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['Name']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_email = array(
			    'name'   => COOKIE_PREFIX.'email',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['email']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_mobile = array(
			    'name'   => COOKIE_PREFIX.'mobile',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['Mobile']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_token= array(
			    'name'   => COOKIE_PREFIX.'token',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['token']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_customer_code= array(
			    'name'   => COOKIE_PREFIX.'customer_code',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_code']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_shopid= array(
			    'name'   => COOKIE_PREFIX.'shopid',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['ShopID']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_customer_type= array(
			    'name'   => COOKIE_PREFIX.'customer_type',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_type']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);
				$cookie_user_level_id= array(
			    'name'   => COOKIE_PREFIX.'cookie_user_level_id',
			    'value'  => $this->encryption_util->encrypt_ssl($user_login['level_id']),
			    'expire' => 31536000,
			    'path'   => '/',
			    'secure' => FALSE
				);



				$this->input->set_cookie($cookie_sessionid);
				$this->input->set_cookie($cookie_userid);
				$this->input->set_cookie($cookie_usergroupid);
				$this->input->set_cookie($cookie_multigroup);
				$this->input->set_cookie($cookie_company);
				$this->input->set_cookie($cookie_user_name);
				$this->input->set_cookie($cookie_email);
				$this->input->set_cookie($cookie_mobile);
				$this->input->set_cookie($cookie_token);
				$this->input->set_cookie($cookie_customer_code);
				$this->input->set_cookie($cookie_shopid);
				$this->input->set_cookie($cookie_customer_type);
				$this->input->set_cookie($cookie_user_level_id);

				$this->session->set_userdata(SESSION_PREFIX.'gen_id',$session_id);
				$this->session->set_userdata(SESSION_PREFIX.'user_id',$this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']));
				$this->session->set_userdata(SESSION_PREFIX.'usergroup_id',$this->encryption_util->encrypt_ssl($user_login['usergroup_id']));
				$this->session->set_userdata(SESSION_PREFIX.'multigroup',$data_multigroup);
				$this->session->set_userdata(SESSION_PREFIX.'company',$user_login['CompanyName']);
				$this->session->set_userdata(SESSION_PREFIX.'username',$user_login['Name']);
				$this->session->set_userdata(SESSION_PREFIX.'email',$user_login['email']);
				$this->session->set_userdata(SESSION_PREFIX.'mobile',$user_login['Mobile']);
				$this->session->set_userdata(SESSION_PREFIX.'token',$user_login['token']);
				$this->session->set_userdata(SESSION_PREFIX.'customer_code',$user_login['customer_code']);
				$sess_shop_id = $this->encryption_util->encrypt_ssl($user_login['ShopID']);
				$this->session->set_userdata(SESSION_PREFIX.'shop_id',$sess_shop_id);
				$this->session->set_userdata(SESSION_PREFIX.'customer_type',$user_login['customer_type']);
				$this->session->set_userdata(SESSION_PREFIX.'user_level_id',$user_login['level_id']);
				//echo $user_login['customer_code']."---".$user_login['BNYCustomerID'];

				$shopid = $this->encryption_util->decrypt_ssl($sess_shop_id);

				//echo $user_login['ShopID']."---".$sess_shop_id."---".$shopid;
				redirect(base_url()."monitor/main",'refresh');
		}

	}

	function authen_user(){

		$user_id = $this->session->userdata('user_id');

		if(empty($user_id))
		   {
				echo 'false';
		   }

	}

	function chk_user_token(){

		//echo ">>>token";
		$token_expire = false;

		$data = $this->curl_bl->call_curl('GET','users/chk_token');

		//echo $data['Status'];

		if($data['Status'] != "Success"){
			$token_expire = true;
		}
		//print_r($data);

		//chk_token

		return $token_expire;


	}

	public function chk_authen_user_login(){

		$user_id = get_cookie('cookie_userid');
		$is_popup = true;

		if(!empty($user_id)){

			//check token expire or not
			$chk_token_expire = $this->chk_user_token();
			if($chk_token_expire){
				//if token expire
				$this->user_login_logined();
			}

			$is_popup = false;

		};

		$data = array(
			'is_popup' => $is_popup
		);

		echo json_encode($data);
		
	}

	function user_login_logined(){
		$this->load->helper('cookie');

		$from_pop_login = $this->input->post('from_pop_login');
		$cha_response = $this->input->post('cha_response');

		if(isset($cha_response)){
			$captcha=$cha_response;
			$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".GOOGLE_CAPTCHA_SECRETKEY."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
			if(!$captcha){
				$missinginputsecret = ["The response parameter is missing."];
				print_r($missinginputsecret[0]);
			}        
		}

		if($response['success'] == true){
			//echo "The response parameter is missing.";
			

			if($from_pop_login == '1'){

				$txt_email = $this->encryption_util->encrypt_ssl($this->input->post('bnyusername'));
				$password = $this->encryption_util->encrypt_ssl($this->input->post('bnypassword'));

			}else{
				$txt_email = get_cookie('cookie_bnyu');
				$password = get_cookie('cookie_bnyp');
			}

			

			//echo ">>>>".$txt_email."<<<<";
			//$password = md5(SALT_PASSWORD.$password);

			//$user_login = $this->users_model->select_by_user_password($user_name,$password);
			$data_curl = array('txt_email' => $txt_email, 'txt_password' => $password);
			//print_r($data_curl);
			$arr_user_login = $this->curl_bl->call_curl_notoken('POST','users/authen_user_login',$data_curl);

			//print_r($arr_user_login);

			$user_login  = $arr_user_login['Data']['data_user'];

			$data_multigroup = $arr_user_login['Data']['data_group'];
			//echo ">>>>>user>>>>>";
			//print_r($user_login);
			//echo "<<<<<user<<<<<";
		
		

		if(empty($user_login)){
			//echo "login fail";
			$this->session->set_flashdata('login_msg','fail');
			$login_status = false;
			//redirect(base_url().'users/login','refresh');
		}else{

			$login_status = true;
			//echo "login success";
			$autologin = $this->input->post('remember');
			//echo $autologin;

			$agent_id = "";
			$agent_name = "";
			$agent_password = "";

			$datenow = date("YmdHis");			
			$ran_num = $this->random_util->create_random_number(6);	
			$session_id = $user_login['BNYCustomerID'].$datenow.$ran_num;
			$cookie_sessionid = array(
		    'name'   => 'cookie_sessionid',
		    'value'  => $session_id,
		    'expire' => 86400,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$cookie_bnyu = array(
		    'name'   => 'cookie_bnyu',
		    'value'  => $txt_email,
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$cookie_bnyp = array(
		    'name'   => 'cookie_bnyp',
		    'value'  => $password,
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			//echo $autologin;
			$cookie_userid = array(
		    'name'   => 'cookie_userid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_usergroupid = array(
		    'name'   => 'cookie_usergroupid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['usergroup_id']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_multigroup = array(
		    'name'   => 'cookie_multigroup',
		    'value'  => $this->encryption_util->encrypt_ssl(json_encode($data_multigroup)),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_company = array(
		    'name'   => 'cookie_company',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['CompanyName']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_user_name = array(
		    'name'   => 'cookie_user_name',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['Name']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_email = array(
		    'name'   => 'cookie_email',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['email']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_mobile = array(
		    'name'   => 'cookie_mobile',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['Mobile']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_token= array(
		    'name'   => 'cookie_token',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['token']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_customer_code= array(
		    'name'   => 'cookie_customer_code',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_code']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_shopid= array(
		    'name'   => 'cookie_shopid',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['ShopID']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);
			$cookie_customer_type= array(
		    'name'   => 'cookie_customer_type',
		    'value'  => $this->encryption_util->encrypt_ssl($user_login['customer_type']),
		    'expire' => 31536000,
		    'path'   => '/',
		    'secure' => FALSE
			);

			$this->input->set_cookie($cookie_sessionid);
			$this->input->set_cookie($cookie_userid);
			$this->input->set_cookie($cookie_usergroupid);
			$this->input->set_cookie($cookie_multigroup);
			$this->input->set_cookie($cookie_company);
			$this->input->set_cookie($cookie_user_name);
			$this->input->set_cookie($cookie_email);
			$this->input->set_cookie($cookie_mobile);
			$this->input->set_cookie($cookie_token);
			$this->input->set_cookie($cookie_customer_code);
			$this->input->set_cookie($cookie_shopid);
			$this->input->set_cookie($cookie_customer_type);

			$this->input->set_cookie($cookie_bnyu);
			$this->input->set_cookie($cookie_bnyp);


			$this->session->set_userdata('session_gen_id',$session_id);
			$this->session->set_userdata('session_bnyu',$txt_email);
			$this->session->set_userdata('session_bnyp',$password);
			$this->session->set_userdata('user_id',$this->encryption_util->encrypt_ssl($user_login['BNYCustomerID']));
			$this->session->set_userdata('usergroup_id',$this->encryption_util->encrypt_ssl($user_login['usergroup_id']));
			$this->session->set_userdata('multigroup',$data_multigroup);
			$this->session->set_userdata('company',$user_login['CompanyName']);
			$this->session->set_userdata('username',$user_login['Name']);
			$this->session->set_userdata('email',$user_login['email']);
			$this->session->set_userdata('mobile',$user_login['Mobile']);
			$this->session->set_userdata('token',$user_login['token']);
			$this->session->set_userdata('customer_code',$user_login['customer_code']);
			$sess_shop_id = $this->encryption_util->encrypt_ssl($user_login['ShopID']);
			$this->session->set_userdata('shop_id',$sess_shop_id);
			$this->session->set_userdata('customer_type',$user_login['customer_type']);

		}

			$data = array(
				'login_status' => $login_status
			);
		}
	}

	function gen_link_user_regis(){
		$usergroup_id_en = $this->uri->segment(4);
	    $usergroup_id = $this->encryption_util->decrypt_ssl($usergroup_id_en);
	    //echo $shop_id."<br>";

	    
	    echo base_url().'users/login_with_google/'.$usergroup_id_en;
	}

	function gen_link_test(){

		$shop_id_en = $this->encryption_util->encrypt_ssl(55);
		echo base_url().'users/login_with_google/'.$shop_id_en;

	}

	function test_curl(){

		$data_curl = array(
	        	'id' => '114225561451049225495'
	        );
	        $get_user = $this->curl_bl->call_curl_notoken('POST','users/getby_google_id',$data_curl);

			//print_r($get_user);

	}

}