<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Random library : for create random library class.
*  Create by peak. 13/09/2013
**/
class Random_util 
{
	private $CI;
	
	
	public function __construct() 
	{				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();		
			

    }
	
	
	public function create_admin_auth_user($username)
	{
		$user = $this->CI->token_model->select_value_token($token);
		
		if (! empty($user)) {
		
		}
		//print_r($user);
	}
	
	
	/*[create_random_number][fn] : for create random number by digit
	  [use] "12345" = create_random_number(5);*/
	public function create_random_number($n)
	{
		//$seed = $this->make_seed();
		//srand($seed);
		
		$ran_num = '';
			
		for ($i = 0;$i<$n; $i++){
		
			$rand_val =  mt_rand(0,9);
			
			$ran_num .= (string)$rand_val;
		}
		
		return $ran_num;
	}
	
	
	/*[make_seed][fn] : for create seed for use in random number fn*/
	public function make_seed()
	{
	  list($usec, $sec) = explode(' ', microtime());
	  return (float) $sec + ((float) $usec * 100000);
	}
			
	
	/*[get_milli_second][fn] : for get milli second from time now*/
	public function get_milli_second()
	{
		$unix_seconds = $this->get_unix_time();
		$milli_len = strlen($unix_seconds);
		
		$milli_seconds = substr($unix_seconds,$milli_len-4,4);
				
		return $milli_seconds;
	}
	
	
	public function get_micro_second()
	{
		list($usec, $sec) = explode(" ", microtime());
		$micro_second = $usec * 100000000;
		
		
		/*[START][edited]*/		
		$str_micro_time = strval($micro_second);		
		$digits = strlen($str_micro_time);
		
		if ($digits != 8){
			if ($digits < 8){
				while ($digits < 8){
					$str_micro_time = $str_micro_time.'0';
					$digits++;
				}
			}else if ($digits > 8){
				$str_micro_time	= substr($str_micro_time, 0, 8); 
			}
			
			$micro_second = intval($str_micro_time);
		}		
		/*[END][edited]*/		
				
		return $micro_second;
	}
	
	
	public function get_unix_micro_time()
	{
		$micro_time = (string)$this->get_micro_second();
		
		$unix_second = (string)$this->get_unix_time();
		
		$unix_micro_time = $unix_second.$micro_time;
				
		return $unix_micro_time;
	}
	
	
	public function get_unix_time()
	{
		$unix_seconds = round(microtime(true) * 1000);
		$str_unix_seconds = strlen($unix_seconds);
		
		return $unix_seconds;
	}
	
	
	public function gen_id_lca()
	{
		$ran_1 = $this->generateRandomString(8);
		$ran_2 = $this->generateRandomString(4);
		$ran_3 = $this->generateRandomString(4);
		$ran_4 = $this->generateRandomString(4);
		$ran_5 = $this->generateRandomString(12);
		
		$id = $ran_1."-".$ran_2."-".$ran_3."-".$ran_4."-".$ran_5;
		return $id;
	}
	
	public function generateRandomString($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

}

/* End of file random_util.php */
/* Location: ./application/libraries/util/random_util.php */