<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class admin extends CI_Controller {

	function __construct() 
	{
		//:[Auto call parent construct]
        parent::__construct();
		$this->load->library('session');
		$this->load->library('util/security_util');
        $this->load->library('util/common_util');
        $this->load->library('util/order_util');
		$this->load->library('util/view_util');
		$this->load->model('adminusers_model');
		$this->load->model('logintoken_model');
		$this->load->library('businesslogic/admin_check_authen');
		$this->load->helper('captcha');
		$this->check_if_loggedin();

    }


    function logout()
    {

    	//delete session token

    	if(!empty($this->session->userdata('token')))
    	{
    	$this->logintoken_model->delete_token_by_tokenid($this->session->userdata('token'));
    	$this->session->unset_userdata('token');
    	redirect('/admin', 'refresh'); 
    	}



    }
    

    function check_if_loggedin()
    {
       if($this->uri->segment(3)==ADMINBYPADDKEY)
       {
       	       //$this->loginrequired();
               $this->load->view('admin/auth-login');
		       
		}
		else
		{
			if(Empty($this->input->get('captchaword')))
			{

				if(!$this->admin_check_authen->check_admin_token_session())
			       {
							redirect('/admin/loginrequired/'.ADMINBYPADDKEY, 'refresh'); 
			       }
		   }
			             
		}

    }

   


    public function logined($token=NULL)
    {

     $username=  $this->input->get('username');
     $userpassword=  $this->input->get('userpassword');
     $captchaword=  $this->input->get('captchaword');

     //check captcha

    if($captchaword!=$this->session->userdata('valuecaptchaCode'))
    {
    	$this->load->view('admin/auth-login'); 	 
    	 //redirect('/admin/loginrequired', 'refresh');
    
    }
    else
    {
    	$return = $this->adminusers_model->select_adminUsers_by_email_password($username,$userpassword);
    	if($return !="")
    	{

         $this->admin_check_authen->insert_token();
          redirect('/admin', 'refresh');


    	}
    	else
    	{
    		 $this->load->view('admin/auth-login');
             //redirect('/admin/loginrequired', 'refresh');

    	}

    }



    }

    public function loginrequired()
    {
    	//$this->load->view('admin/auth-login');
    }

    public function testcaptcha(){



    	$vals = array(
        'word'          => '',
        'img_path'      => './captcha/',
        'img_url'       => 'http://www.bnyfoodproducts.com/captcha/',
        'font_path'     => '../assets/fonts/EDMuzazhi.ttf',
        'img_width'     => 180,
        'img_height'    => 45,
        'expiration'    => 7200,
        'word_length'   => 8,
        'font_size'     => 20,
        'img_id'        => 'Imageid',
        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

        // White background and border, black text and red grid
        'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(rand(0,255), rand(0,255), rand(0,255))
        )
);

$cap = create_captcha($vals);
echo $cap['image'];

    }
	
       public function index2(){
		if(!$this->admin_check_authen->check_admin_token_session)
		{



		}
		else
		{

		}
		$data = array('title' => 'Phuket Gazette Login',
					  'loginfail' => $loginfail);
		$this->view_util->load_view('login/login',$data,$arr_css,NULL);

		  $user_name = $this->input->post('txt_username');
		$password = $this->input->post('txt_password');
		$user_login = $this->users_model->select_by_email_and_password($user_name,$password);
		//print_r($user_login);
		if(empty($user_login)){
			redirect(base_url().'login/index/fail','refresh');
		}
		else{
			$this->session->set_userdata('user_id',$user_login['UserId']);
			$this->session->set_userdata('email',$user_login['Email']);
			$this->session->set_userdata('name',$user_login['FirstName'] . " " . $user_login['LastName']);
			$redirec_path = $this->session->userdata("redirect_path");
			$this->session->unset_userdata("redirect_path");
			redirect($redirec_path,'refresh');
		}
		
	}


   


    public function index()

	{

		if(!$this->admin_check_authen->check_admin_token_session())
		{

         // redirect('/admin/loginrequired', 'refresh');

		}
		else
		{
		     $this->load->view('admin/index');	
		}

		
	}

	public function testit()
	{
		

		echo "hello wortld!";
		//$arr = $this->test_model->select_Orders();
		//print_r($arr);
		//$this->load->view('welcome_message');




$return_arr = array("id" => '1234',
                   "username" => 'phuketvending',
                   "name" => 'seubsak sahaworaphan',
                  "email" => 'phuketvending@gmail.com');


// Encoding array in JSON format
echo json_encode($return_arr);


	}


//accounting

  public function getordersbyplatformordernumberdaterange()
{




}

public function accounting_bk()
{

    $this->load->model('lazada_orders_model');
    $this->load->model('laztoken_model');
    $this->load->model('lazada_orderitems_model');
    $this->load->model('lazada_customers_model');
    $this->load->model('lazada_shipping_address_model');
    $this->load->model('lazada_billing_address_model');
    $this->load->library("util/array_util");
    $this->load->library("util/common_util");
    $this->load->library("businesslogic/lazapi");




$section = $this->uri->segment(3);

switch ($section) {
  case "taxinvoice":
            if($this->uri->segment(4)!=NULL)
            {
                 switch ($this->uri->segment(4)) {

                    case "loadpages":
                     $platform=$this->uri->segment(5);
                     $ordernumber=$this->uri->segment(6);
                     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(7),"S");
                     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(7),"E");
                     


                               switch($platform)
                               {
                                 case 0: //lazada
                        
                                


                             
                             $orders_orderitems=$this->lazada_orders_model->get_order_with_orderitems_by_DateStart_DateEnd($StartDate,$EndDate);

                             

                             if(!empty($orders_orderitems))
                             {

                             $orders=$this->order_util->getOrdersFromOdersOderItems($orders_orderitems);

                             $data=array('orders'=>$orders
                             );

                              }
                              else

                              {

                               $data=array('orders'=>0
                             );   
                              }

                            print_r($data);

                            // $this->load->view('admin/accounting/taxinvoicepages',$data);
                             
                             

                                    



                                 break;

                                 case 1: //shopee

                                 break;


                               }


                    break;

                    case "getordersbyplatformordernumberdaterange":


                             //echo "<br>Platform: ".$this->input->post('platform');
                              //echo "<br>ordernumber: ".$this->input->post('ordernumber');
                              //echo "<br>daterange: ".$this->input->post('daterange');

                              switch($this->input->get('platform'))
                              {

                              case 0: //lazada
                              if($this->input->get('ordernumber')!="")
                              {

                              $arr=$this->lazada_orders_model->getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($this->input->get('taxinvoicetype'),$this->input->post('ordernumber'),NULL,NULL,NULL,NULL);
                              }
                              else
                              {
                                
                                  $date_arr=explode("sphpsp",$this->input->get('daterange'));
                              $StartDate=explode("sl",$date_arr[0]);
                              $EndDate=explode("sl",$date_arr[1]);

                              $page=1;

                              if($this->input->get('page')!==null)
                              {
                                  $page=$this->input->get('page');                                  
                              }  

                              $arr=$this->lazada_orders_model->getOrderByTaxinvoiceTypeOrdernumberOrDateStartDateEnd($this->input->get('taxinvoicetype'),NULL,$StartDate[2]."-".$StartDate[0]."-".$StartDate[1],$EndDate[2]."-".$EndDate[0]."/".$EndDate[1],$page,PAGINATION_SIZE);

                              }

                              if($arr==NULL)
                              {

                                  echo "<center>No Order Match your search</center>";
                              }
                              else
                              {
                                      
                              $data=array('orders'=>$arr,
                                          'taxinvoicetype'=>$this->input->get('taxinvoicetype'),
                                          'page'=>$page,  
                                          'platform'=>$this->input->get('platform'),
                                          'ordernumber'=>$this->input->get('ordernumber'),
                                          'daterange'=>$this->input->get('daterange')
                            );

                              
                              //print_r($data);
                              $this->load->view("admin/accounting/taxinvoice",$data);

                              }


                              break;

                              case 1:


                              break;

                              case 2:


                              break;



                              }




                    break;
                }
            }
            else //entry page no data, this is landing page at first click to taxinvoice 
            {
              $data=array();
              $arr_css = array(
    );  
      
    $arr_js = array(
      'invoice_js' => base_url().'resources/js/account/textinvoice.js',
    );  
              $this->view_util->load_view_main('admin/accounting/taxinvoice',$data,$arr_css,$arr_js);


            }

     
  break;

  case "saletaxreport":
                if($this->uri->segment(4)!=NULL)
                {

                     $StartDate=$this->order_util->getStartEndDate($this->uri->segment(5),"S");
                     $EndDate=$this->order_util->getStartEndDate($this->uri->segment(5),"E");

                     //prepdata

                     //Lazada
                      

                      $arr_lazada=$this->lazada_orders_model->getOrderbyDateStartDateEndGroupbyDate($StartDate,$EndDate);
                      $data=array(
                        'validdata'=>1,
                        'start_date'=>$StartDate,
                        'end_date'=>$EndDate,
                        'lazada_orders'=>$arr_lazada


                      );
                                       
   //print_r($data);


                $this->load->view('admin/accounting/saletaxreportpages',$data);

                }
                else
                {
                    $data=array('validdata'=>0);

              $this->load->view('admin/accounting/saletaxreport',$data);

                }


  break;

  case "issuetaxionvoice":

  $data=array(
    'ordernumber'=>$this->uri->segment(4)

  );
$this->load->view('admin/accounting/issuetaxionvoice',$data);


  break;
       } //end switch

}


//settings

public function datasyncro()
{

$this->load->model('datasyncro_model');
$lastSyncroLaz=$this->datasyncro_model->select_latest_datasyncro_by_platform('lazada');
$lastSyncroShopee=$this->datasyncro_model->select_latest_datasyncro_by_platform('shopee');
$now=date_create(date("Y-m-d H:i:s"));

        //lazada
        if(empty($lastSyncroLaz))
        {
            $lazada="Never";
            $lazpass="";
        }
        else

        {
        $lazada=date_format(date_create($lastSyncroLaz->syndatetime),"d M, Y H:i:s");
        $lazadaDate=date_create($lastSyncroLaz->syndatetime);
        $diff=date_diff($lazadaDate,$now);
        $lazpass=$diff->format("%d วัน %h:%i:%s");
        }

        //shopee
        if(empty($lastSyncroShopee))
        {
            $shopee="Never";
            $shopeepass="-";
        }
        else

        {
            
        $shopee=date_format(date_create($lastSyncroShopee->syndatetime),"d M, Y H:i:s");
        $shopeeDate=date_create($lastSyncroShopee->syndatetime); 
        $diff=date_diff($shopeeDate,$now);
        $shopeepass=$diff->format("%d วัน %h:%i:%s");



        }

$data=array("lazada"=>$lazada,
            "lazpass"=>$lazpass,    
            "shopee"=>$shopee,
            "shopeepass"=>$shopeepass
            );

$this->load->view('admin/datasyncro',$data);

}



}
