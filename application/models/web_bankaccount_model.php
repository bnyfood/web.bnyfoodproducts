<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_bankaccount_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }

    function get_all(){

    	$datas = $this->CI->cache_util->select_data('model','web_bankaccount','get_all','web_bankaccount/get_all');

		return $datas;
	}


    function get_by_shop($shop_id,$per_page){

    	$datas = $this->CI->cache_util->select_data('model','web_bankaccount','get_by_shop|'.$shop_id.'|'.$per_page,'web_bankaccount/get_by_shop/'.$shop_id.'/'.$per_page);

		return $datas;
	}

	function get_by_shop_join($shop_id){

    	$datas = $this->CI->cache_util->select_data('model','web_bankaccount','get_by_shop_join|'.$shop_id,'web_bankaccount/get_by_shop_join/'.$shop_id);

		return $datas;
	}

	function get_by_shop_nospin($shop_id){

    	$datas = $this->CI->cache_util->select_data_nospin('model','web_bankaccount','get_by_shop|'.$shop_id,'web_bankaccount/get_by_shop/'.$shop_id);

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','web_bankaccount','get_by_id|'.$id_en,'web_bankaccount/get_by_id/'.$id_en);

		return $datas;
	}

	function del_cache_by_shop($shop_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','web_bankaccount','get_by_shop|'.$shop_id);

	}

	function del_cache_by_id($id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','web_bankaccount','get_by_id|'.$id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



