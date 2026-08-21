<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Web_domain_model : Class Web_domain_model extends from CI model.
**/
class Web_domain_model extends CI_Model
{
	private $CI;

	function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
		$this->CI->load->library('util/cache_util');
	}

	function get_by_shop($shop_id,$per_page){
		$datas = $this->CI->cache_util->select_data('model','web_domain','get_by_shop|'.$shop_id.'|'.$per_page,'webs/domains/get_by_shop/'.$shop_id.'/'.$per_page);
		return $datas;
	}

	function get_by_id($id_en){
		$datas = $this->CI->cache_util->select_data('model','web_domain','get_by_id|'.$id_en,'webs/domains/get_by_id/'.$id_en);
		return $datas;
	}

	function del_cache_by_shop($shop_id){
		$this->CI->cache->delete('model','web_domain','get_by_shop|'.$shop_id);
	}

	function del_cache_by_id($id){
		$this->CI->cache->delete('model','web_domain','get_by_id|'.$id);
	}
}

/* End of file web_domain_model.php */
/* Location: ./application/models/web_domain_model.php */
