<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Api_log_model extends CI_Model
{

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
    }

    function insert($data){
    	$this->db->insert('api_log', $data);
    	//return $this->db->insert();
    	//echo $this->db->last_query();
	}
	
	function update($data,$api_id){
    	$this->db->where('ApiLogID',$api_id);
		$this->db->update('api_log',$data);
	}
	
	function select_all(){
		$this->db->cache_on();
		$this->db->select('*');
		$this->db->from('api_log');
		$query = $this->db->get();
		$rowcount = $query->result_array();
		//echo $this->db->last_query();
    	return $rowcount;
	}

	function check_call_cnt($token,$sampling_period){
		$this->db->cache_on();
		$this->db->select('count(*) as access_cnt');
		$this->db->from('api_log');
		$this->db->where('ApiToken',$token);
		$this->db->where('DATEDIFF(MINUTE,ApiCdate,GETDATE()) <=',$sampling_period);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->row_array();
	}

	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */