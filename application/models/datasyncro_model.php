<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class datasyncro_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
	
function select_latest_datasyncro_by_platform($platform){

		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from('datasyncro');
		$this->db->where('platform',$platform);
		$this->db->order_by("syndatetime", "desc");
		$this->db->where('platform',$platform);
		
		$query = $this->db->get();
		//return $query->row_array();
		return $query->row();
}	



}


