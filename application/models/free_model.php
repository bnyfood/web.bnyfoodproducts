<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class free_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	
 	function query_run($sql){

	  	$query = $this->db->query($sql,true);
	  	echo $this->db->last_query();
		//return $query->row_array();

	} 

	function select_test($id){
		$this->db->select("col1,TaxINV");
		$this->db->from('testtable');
		$this->db->where('Id >',$id);
		$query = $this->db->get();
		return $query->result_array();
	} 

}


