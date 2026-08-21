<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** View_util : is view utility library for load and render view.
*  Create by peak. 9/04/2013
**/
class Admin_check_authen 
{

	
	function __construct() 
	{
		
			//echo '<br/>banner_bl constructor running <br/><br/>';
			
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();
		$this->CI->load->model('logintoken_model');
		
		
	 	//$this->prepare_data();
		
		//$this->edge_of_box = array(6,12,18,24,30,36);
		//$this->arr_banner_setting = array('edge_of_box' => $edge_of_box);
		
	/*	echo '<br/>';
		print_r($this->edge_of_box);
		echo '<br/>';*/
		
    }
	
	
	public function check_admin_token_session()
	{

		
		//check if token session is exist
		$token=$this->CI->session->userdata(SESSION_PREFIX.'token');
   	     
		if(!empty($token) )
		{
              
			if($this->check_encrypted_token($token,$this->CI->security_util->md5_hash($token)))
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


	function insert_token()
	{

      $return=$this->CI->logintoken_model->insert_token();
      $this->CI->session->set_userdata(SESSION_PREFIX.'token', $return->tokenid);

      $encrypted_token=$this->CI->security_util->md5_hash($return->tokenid);


      $return=$this->CI->logintoken_model->update_logintoken_by_id_encrypted_token($return->tokenid,$encrypted_token);

      
	}


	 function check_encrypted_token($token,$encryptedtoken)
	{

		return $this->CI->logintoken_model->select_token_by_id_and_encrypted_token($token,$encryptedtoken);


	}

	 function exten_token($token)
	{
       $this->CI->logintoken_model->exten_token($token);

        
	}
	
	
	
	
	
}

/* End of file article_bl.php */
/* Location: ./application/libraries/bussiness_logic/article_bl.php */