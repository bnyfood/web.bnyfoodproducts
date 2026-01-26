<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_bny_customer_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){

    	$this->db->insert('web_bny_customer', $data);
    	$insert_id = $this->db->insert_id();
    	return $insert_id;
	}
	
	function update($data,$id){
    	$this->db->where('BNYCustomerID',$id);
		$this->db->update('web_bny_customer',$data);
	}

	function update_by_phone($data,$phone){
		$this->db->where('Mobile',$phone);
		$this->db->update('web_bny_customer',$data);
	}

	function delete($id){
		$this->db->where('BNYCustomerID',$id);
		$this->db->delete('web_bny_customer');
	}
	
	function select_all(){

		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_id($id){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('BNYCustomerID',$id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function get_by_email($email){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->row_array();
	}


	function select_by_id_join($id){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->join('user_group', 'web_bny_customer.usergroup_id = user_group.usergroup_id');
		$this->db->join('api_authen', 'web_bny_customer.BNYCustomerID = api_authen.BNYCustomerID');
		$this->db->where('web_bny_customer.BNYCustomerID',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_by_id_join_profile($id){
		$this->db->select('web_bny_customer.BNYCustomerID,web_bny_customer.usergroup_id,CompanyName,Name,web_bny_customer.email,Mobile,api_authen.token,web_bny_customer.customer_code,ShopID,customer_type');
		$this->db->from('web_bny_customer');
		$this->db->join('user_group', 'web_bny_customer.usergroup_id = user_group.usergroup_id');
		$this->db->join('api_authen', 'web_bny_customer.BNYCustomerID = api_authen.BNYCustomerID');
		$this->db->join('groupmapuser', 'user_group.usergroup_id = groupmapuser.group_id');
		//$this->db->join('user_level', 'groupmapuser.user_level_id=user_level.user_level_id');
		//$this->db->join('groupuser_permission', 'groupmapuser.groupmapuser_id=groupuser_permission.groupmapuser_id');
		$this->db->where('web_bny_customer.BNYCustomerID',$id);
		$this->db->group_by('web_bny_customer.BNYCustomerID,web_bny_customer.usergroup_id,CompanyName,Name,web_bny_customer.email,Mobile,api_authen.token,web_bny_customer.customer_code,ShopID,customer_type ');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	function select_multi_group($id){
		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->join('user_group', 'groupmapuser.group_id = user_group.usergroup_id');
		//$this->db->join('groupuser_permission', 'groupmapuser.groupmapuser_id=groupuser_permission.groupmapuser_id');
		$this->db->where('groupmapuser.BNYCustomerID',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_multi_group_bny($id){
		$this->db->select('*');
		$this->db->from('groupmapuser');
		$this->db->join('user_group', 'groupmapuser.group_id = user_group.usergroup_id');
		//$this->db->join('groupuser_permission', 'groupmapuser.groupmapuser_id=groupuser_permission.groupmapuser_id');
		$this->db->where('groupmapuser.bny_user_id',$id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_group_id($group_id){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->join('groupmapuser', 'web_bny_customer.BNYCustomerID = groupmapuser.BNYCustomerID');
		$this->db->where('group_id',$group_id);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_id_in($arr_id){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where_in('BNYCustomerID',$arr_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_code($customer_code){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('customer_code',$customer_code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_email_code($email,$customer_code){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('email',$email);
		$this->db->where('customer_code',$customer_code);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_by_code_noid($customer_code,$groupid,$customer_type){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('customer_code',$customer_code);
		$this->db->where('customer_type',$customer_type);
		$this->db->where('usergroup_id <>',$groupid);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_code_no_groupid($customer_code,$groupid,$customer_type){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('customer_code',$customer_code);
		$this->db->where('customer_type',$customer_type);
		$this->db->where(" BNYCustomerID not in (select BNYCustomerID from groupmapuser where group_id = ".$groupid.")");
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_code_noid_v2($customer_code,$groupid){
		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->join('api_authen', 'web_bny_customer.BNYCustomerID = api_authen.BNYCustomerID');
		$this->db->join('groupmapuser', 'api_authen.ApiAuthenID = groupmapuser.ApiAuthenID');
		$this->db->where('groupmapuser.group_id <>',$groupid);
		$this->db->where('web_bny_customer.customer_code',$customer_code);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	function select_by_phone_otp($phone_no,$txt_otp){

		$this->db->select('web_bny_customer.BNYCustomerID as BNYCustomerID,ApiAuthenID,DATEDIFF(ss, otp_cdate, GETDATE()) as otp_time');
		$this->db->from('web_bny_customer');
		$this->db->join('api_authen', 'web_bny_customer.BNYCustomerID = api_authen.BNYCustomerID');
		$this->db->where('Mobile',$phone_no);
		$this->db->where('otp',$txt_otp);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_phone($phone_no){

		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('Mobile',$phone_no);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_email($email){

		$this->db->select('*');
		$this->db->from('web_bny_customer');
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */