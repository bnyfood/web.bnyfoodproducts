<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class lazapi 
{

private $params = array();
private $APIPath;
private $AppKey;
private $AppSecret;
private $timeStamp;
	
	function __construct() 
	{
		
			//echo '<br/>banner_bl constructor running <br/><br/>';
			
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		//$this->CI->load->model('logintoken_model');
		$this->CI->load->library("util/common_util");
		$this->CI->load->library('curl'); 
    $this->CI->load->library("businesslogic/number_bl");
		
	 	//$this->prepare_data();
		
		//$this->edge_of_box = array(6,12,18,24,30,36);
		//$this->arr_banner_setting = array('edge_of_box' => $edge_of_box);
		
	/*	echo '<br/>';
		print_r($this->edge_of_box);
		echo '<br/>';*/
		
    }
	
public function init()
{
	return TRUE;
}


public function setAppSecret($val)
     {

        $this->AppSecret=$val;
         return TRUE;
     }


     public function getAppSecret()
     {

        return $this->AppSecret;

     }


     
	public function setAppPath($val)
     {

        $this->APIPath=$val;
        return TRUE;

     }


     public function getAppPath()
     {

        return $this->APIPath;

     }



     public function setAppkey($val)
     {

        $this->AppKey=$val;
        return TRUE;

     }


     public function getAppkey()
     {

        return $this->AppKey;

     }

    public function getTimeStamp()
    {
    	if(is_null($this->timeStamp))
    	{
		
$milliseconds = round(microtime(true) * 1000);



        $this->timeStamp=$milliseconds;
    	}

    	return $this->timeStamp;

    }


    
	public function addparam($paramname,$pramval)
	{

       $this->params = $this->array_push_assoc($this->params, $paramname, $pramval);


	}
	

	function array_push_assoc($array, $key, $value){
   $array[$key] = $value;
   return $array;
   }

   function sortParams()
   {
     $this->params=ksort($this->params);

   }

   public function callAPI()
   {
       
       

       
       $URL=$this->CI->config->item('lazAPInonsecure').$this->APIPath."?";
       
       $firstloop=0;
       foreach ($this->params as $key => $value)
    	{
    		if($firstloop==0)
    		{
    	     $URL=$URL.$key."=".$value;		
    		}
    		else
    		{
			 $URL=$URL."&".$key."=".$value;		

    		}
          $firstloop++;

    	}

       $sign=$this->getSign(); 
      
      $URL=$URL."&sign=".$sign;
    

 return $this->CI->curl->simple_get($URL);
 
    }


    function getSign()
    {
		$textToSign=$this->APIPath;
		 ksort($this->params);
		 		 
		foreach ($this->params as $key => $value)
    	{
 
          $textToSign=$textToSign.$key.$value;
 
    	}
       // $textToSign="/order/items/getaccess_token50000901b10sf8qdYkBU9O1b160bf80YFfithzluwmg0FPraMtVxNQy09XdqO3hkapp_key123793order_id342166995455049sign_methodsha256timestamp1606415182697";


    	
       $textToSign=strtoupper(hash_hmac('sha256',$textToSign,$this->AppSecret));
       

        return $textToSign;    



    }




	public function check_admin_token_session()
	{

		
		//check if token session is exist
		$token=$this->session->userdata('token');
   	     
		if(!empty($token) )
		{
              
			if($this->check_encrypted_token($token,$this->security_util->md5_hash($token)))
			{
			$this->exten_token($token);
			return TRUE;
		     }
		     else
		     {
		     return TRUE;	
		     }


			
		} 

		else
		{

			return FALSE;	
		}
		

	
	}


	function get_laz_code($last_code,$cdate){
    
    //202103
    $laz_ymcode = substr($last_code, 3,6);
    $laz_code = substr($last_code, 9,5);
    $cdate = strtotime($cdate);
    $cdate_code = $newformat = date('Ym',$cdate);

    if($laz_ymcode == $cdate_code){
      $laz_nextcode = $laz_code+1;
      $laz_nextcode = $this->CI->number_bl->add_font_digi($laz_nextcode,5);
      $laz_newcode = "Laz".$laz_ymcode.$laz_nextcode;
    }else{

      $laz_newcode = "Laz".$cdate_code."00001";
    }

     return $laz_newcode;
  }
	
	
	
}

/* End of file article_bl.php */
/* Location: ./application/libraries/bussiness_logic/article_bl.php */