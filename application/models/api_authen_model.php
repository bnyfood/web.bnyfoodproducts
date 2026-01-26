<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Api_authen_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }
		    
    function insert($data){
    	$this->db->insert('api_authen', $data);
    	return $this->db->insert_id();
	}
	
	function update($data,$user_id){
    	$this->db->where('ApiAuthenID',$user_id);
		$this->db->update('api_authen',$data);
		//echo $this->db->last_query();
	}

	function delete_by_customer_id($customer_id){
		$this->db->where('BNYCustomerID',$customer_id);
		$this->db->delete('api_authen');
	}
	
	
	function select_by_user_password($user_name,$password){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('username',$user_name);
		$this->db->where('password',$password);
		//$this->db->where('status',1);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_email_password($email,$password){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('email',$email);
		$this->db->where('password',$password);
		//$this->db->where('status',1);

		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_google_id($google_id){

		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('google_id',$google_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}


	function chk_tokentime($token){
		//$this->db->cache_on();
		$this->db->select('DATEDIFF(GETDATE(), token_cdate) as tokentime ',FALSE);
		$this->db->from('api_authen');
		$this->db->where('token',$token);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_authen_id($ApiAuthenID){
		//$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('ApiAuthenID',$ApiAuthenID);
		$query = $this->db->get();
		return $query->row_array();
		
	}

	function select_by_token_id($token_id,$ApiAuthenID,$is_cache = false){
		if($is_cache){
			$this->db->cache_on();
		}
		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('ApiAuthenID',$ApiAuthenID);
		$this->db->where('token',$token_id);
		$query = $this->db->get();
		return $query->row_array();
		
	}

	function select_by_username($user_name){

		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('username',$user_name);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_username_code($user_name,$customer_code){

		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('username',$user_name);
		$this->db->where('customer_code',$customer_code);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_email_code($email,$customer_code){

		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('email',$email);
		$this->db->where('customer_code',$customer_code);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_email($email){

		$this->db->select('*');
		$this->db->from('api_authen');
		$this->db->where('email',$email);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */