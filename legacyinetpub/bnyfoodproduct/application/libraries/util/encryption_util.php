<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Encryption_util : for encryption string and data.
*  Create by peak. 13/09/2013
**/
class Encryption_util 
{
	private $CI;
	
	
	public function __construct() 
	{				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();		
				
		$this->CI->load->library('encrypt');		
		$this->CI->load->library('util/random_util');
		$this->CI->load->library('util/view_util');
    }

    function create_code($num){

		$ip = $this->get_client_ip();
		$datenow = date('Y-m-d h:i:s');
		$makeencode =  $ip.'\|/'.$datenow.'\|/'.GEN_CODE_KEY;
		$key_encript = $this->encrypt_decrypt('encrypt',$makeencode,GEN_CODE_KEY2,GEN_CODE_KEY3);

		$key_encript = substr($key_encript, 0, $num);
		return $key_encript;

	}
	
    
    public function encrypt_id($id,$key)
    {
    	
    	
    	$id = base_convert($id, 10, 36); // Save some space
    	$data = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $id, 'ecb');
    	$data = openssl_encrypt($id, "AES-128-ECB", $key);
    	$data = bin2hex($data);
    	
    	return $data;
        	
    }
    
    
     function decrypt_id($id, $key)
    {
    	/*$data = pack('H*', $id); // Translate back to binary
    	$data = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, 'ecb');
    	$data = base_convert($data, 36, 10);
        if($data==0)
        {
        	$this->CI->view_util->page_not_found();
        }
    	return $data;*/

    	$data = pack('H*', $id);

    	$data = openssl_decrypt($data, "AES-128-ECB", $key);
    	$data = base_convert($data, 36, 10);

    	if($data==0)
        {
        	$this->CI->view_util->page_not_found();
        }

        return $data;
    }

    function encrypt_decrypt($action, $string, $secret_key ,$secret_iv) 
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //$secret_key = 'xxxxxxxxxxxxxxxxxxxxxxxx';
        //$secret_iv = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        // hash
        $key = hash('sha256', $secret_key);    
        // iv - encrypt method AES-256-CBC expects 16 bytes 
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    function encrypt_ssl($string) 
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //$secret_key = 'xxxxxxxxxxxxxxxxxxxxxxxx';
        //$secret_iv = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        // hash
        $key = hash('sha256', API_TOKEN_KEY2);    
        // iv - encrypt method AES-256-CBC expects 16 bytes 
        $iv = substr(hash('sha256', API_TOKEN_KEY3), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    function decrypt_ssl($string) 
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        //$secret_key = 'xxxxxxxxxxxxxxxxxxxxxxxx';
        //$secret_iv = 'xxxxxxxxxxxxxxxxxxxxxxxxx';
        // hash
        $key = hash('sha256', API_TOKEN_KEY2);    
        // iv - encrypt method AES-256-CBC expects 16 bytes 
        $iv = substr(hash('sha256', API_TOKEN_KEY3), 0, 16);

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        
        return $output;
    }


    
	
	public function encrypt($data,$key)
	{		
		//$ip = '';
		$ip = $this->get_client_ip();
		
		$ip_len = (string)strlen($ip);
				
		$ip_len_digit = 1;
		
		if ($ip_len > 9){
			$ip_len_digit = 2;
		}
		
		$str_data_key = $data.$key;
		$str_md5_data_key = md5($str_data_key);
		
		$unix_second = $this->CI->random_util->get_unix_time();
		$rand_number = $this->CI->random_util->create_random_number(5);
		
		$data_for_encrypt = $data.$str_md5_data_key.$unix_second.$rand_number.$ip.$ip_len.$ip_len_digit;

		
		//$str_encrypt = $this->CI->encrypt->encode($data_for_encrypt,KEY_ENCRYPTION);
		
		//$str_encrypt_clear = $this->clear_special_character($str_encrypt,TRUE);

		$data_for_encrypt = base_convert($data_for_encrypt, 10, 36);
    	$data = openssl_encrypt($data_for_encrypt, "AES-128-ECB", $key);
    	$data = bin2hex($data);
		
		
		return $data;
	}
	
	
	public function decrypt($encrypt_data,$key)
	{
		/*echo "<br>";
		echo "<br>encrypt_data: ".$encrypt_data;
		echo "<br>encrypt_data_len: ".strlen($encrypt_data);*/
		
		$data = '';
		$character_extend = 50;
		
		$clien_ip = $this->get_client_ip();
		//$str_encrypt_clear = $this->clear_special_character($encrypt_data,FALSE);
		//$str_decrypt = $this->CI->encrypt->decode($str_encrypt_clear,KEY_ENCRYPTION);

		$data = pack('H*', $encrypt_data);

    	$data = openssl_decrypt($data, "AES-128-ECB", $key);
    	$str_decrypt = base_convert($data, 36, 10);

		$str_decrypt_len = strlen($str_decrypt);

		echo $str_decrypt;
		
		/*echo "<br>clien_ip: ".$clien_ip;
		echo "<br>str_encrypt_clear: ".$str_encrypt_clear;
		echo "<br>str_decrypt: ".$str_decrypt;
		echo "<br>str_decrypt_len: ".$str_decrypt_len;*/
		
		if ($str_decrypt_len > 0){
		
			$ip_len_digit = substr($str_decrypt,$str_decrypt_len-1,1);
			$ip_len = substr($str_decrypt, $str_decrypt_len - ($ip_len_digit + 1), $ip_len_digit);
			$after_ip_len = $ip_len+$ip_len_digit+1;
			$data_encrypt_ip =  substr($str_decrypt, $str_decrypt_len - $after_ip_len, $ip_len);
			$data_len = $str_decrypt_len - ($character_extend + $after_ip_len);
			
			/*echo "<br>ip_len_digit: ".$ip_len_digit;
			echo "<br>ip_len: ".$ip_len;
			echo "<br>after_ip_len: ".$after_ip_len;
			echo "<br>data_encrypt_ip: ".$data_encrypt_ip;
			echo "<br>data_len: ".$data_len;*/
			
			$security_data_key_start = $data_len;
			$security_data_key_end = 32;
			
			$data = substr($str_decrypt,0,$data_len);
			
			$security_data_key = substr($str_decrypt,$security_data_key_start,$security_data_key_end);
			
			$str_data_key = $data.$key;
			
			$str_md5_data_key = md5($str_data_key);
			
			/*echo "<br>data: ".$data;
			echo "<br>security_data_key: ".$security_data_key;
			echo "<br>str_data_key: ".$str_data_key;
			echo "<br>str_md5_data_key: ".$str_md5_data_key;*/
			
			
			if ($str_md5_data_key != $security_data_key){
				//$this->CI->view_util->page_not_found();	
				//and should keeep that data for investigate why can not decryption. 
			}
			/*
			if ($data_encrypt_ip != $clien_ip){
				$this->CI->view_util->page_not_found();	
				//and should keeep that data for investigate why can not decryption. 
			}*/
			
			/*echo '<br/><br/><br/>clien_ip : '.$clien_ip.' || len : '.strlen($clien_ip).'<br/>';
			echo '<br/>data_encrypt_ip : '.$data_encrypt_ip.' || len : '.strlen($data_encrypt_ip).'<br/>';
			echo '<br/>ip_len : '.$ip_len.' || len : '.strlen($ip_len).'<br/>';
			echo '<br/>encrypt_data : '.$encrypt_data.' || len : '.strlen($encrypt_data).'<br/>';
			echo '<br/>str_encrypt_clear : '.$str_encrypt_clear.' || len : '.strlen($str_encrypt_clear).'<br/>';
			echo '<br/>str_decrypt : '.$str_decrypt.' || len : '.strlen($str_decrypt).'<br/>';
			echo '<br/>data : '.$data.' || len : '.strlen($data).'<br/>';
			echo '<br/>str_data_key : '.$str_data_key.' || len : '.strlen($str_data_key).'<br/>';
			echo '<br/>str_md5_data_key : '.$str_md5_data_key.' || len : '.strlen($str_md5_data_key).'<br/>';	
			echo '<br/>security_data_key : '.$security_data_key.' || len : '.strlen($security_data_key).'<br/>';*/
		
		}else{
			//$this->CI->view_util->page_not_found();	
			//echo '<br/><br/> str_decrypt_len : '.$str_decrypt_len.'   HAVE SOME problem !!!.<br/>';
		}
		
				
		return $data;
	}
	
	
	private function clear_special_character($str_encrypt,$is_encryption)
	{
		$plus_char = 'As9L36basft3M';
		$equal_char = 'edsaB564sTp1t';
		$slash_char = 'saL9Suk3AbK8a';
		
		if ($is_encryption){
			
			$str_encrypt = str_replace('+',$plus_char,$str_encrypt); 
			$str_encrypt = str_replace('=',$equal_char,$str_encrypt); 
			$str_encrypt = str_replace('/',$slash_char,$str_encrypt); 
		
		}else{
			
			$str_encrypt = str_replace($plus_char,'+',$str_encrypt); 
			$str_encrypt = str_replace($equal_char,'=',$str_encrypt); 
			$str_encrypt = str_replace($slash_char,'/',$str_encrypt); 
			
		}
		
		return $str_encrypt;
	}
	
	
	private function get_client_ip()
	{
		$client_ip = '';
		
		if (isset($_SERVER['REMOTE_ADDR'])){
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}else{
			//redirect('http://www.phuketgazette.net/pagenotfound.asp');
			echo '<br/><br/> client_ip : '.$client_ip.'   CAN NOT GET client ip !!!.<br/>';
		}
		
		return $client_ip;
	}
	
	
	function simple_encryt_by_random(){
	
		$length = 20;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
	 
		for ($p = 1; $p <= $length; $p++) {
		
			$string .= $characters[mt_rand(0, 35)];
			
		}
	 
		return $string;
	
		
	
	}

}

/* End of file encryption_util.php */
/* Location: ./application/libraries/util/encryption_util.php */