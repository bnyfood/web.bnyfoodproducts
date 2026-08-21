<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// create by big 
class DataDownload_model extends CI_Model
{
   
	
	function __construct()
	{
		parent::__construct();

	}
	
	// get news for spotlight in hompage
	// created by big
	
function delete($id){
		$this->db->where('DataDownloadID',$id);
		$this->db->delete('DataDownload');
	}




function select_by_BNY_SUBSCRIPTION_SHOPID($BNY_SUBSCRIPTION_SHOPID){
	$this->db->select('*');
	$this->db->from('DataDownload');
	$this->db->where('BNY_SUBSCRIPTION_SHOPID',$BNY_SUBSCRIPTION_SHOPID);
	$query = $this->db->get();
	return $query->row_array();
}

function update_by_BNY_SUBSCRIPTION_SHOPID($data,$BNY_SUBSCRIPTION_SHOPID)
{


	$this->db->where('BNY_SUBSCRIPTION_SHOPID',$BNY_SUBSCRIPTION_SHOPID);
	$this->db->update("DataDownload",$data);
}


	function insert($data)
	{


    $this->db->insert('DataDownload', $data); 
    

	}

	
	

}


