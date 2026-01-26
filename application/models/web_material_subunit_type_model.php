<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** [C]Example_user_type_model : Class Example_user_type_model extends from CI model.
**/
class Web_material_subunit_type_model extends CI_Model
{
	private $CI;

    function __construct()
    {
        //:[Call the Model constructor]
        parent::__construct();
        $this->CI =& get_instance();

        $this->CI->load->library('util/cache_util');
    }

    function get_all_nospin(){

    	$datas = $this->CI->cache_util->select_data_nospin('model','web_material_subunit_type','get_all','manufacture/web_material_subunit_type/get_all');

		return $datas;
	}


	function get_by_shop_nospin($shop_id){

    	$datas = $this->CI->cache_util->select_data_nospin('model','web_material_subunit_type','get_by_shop|'.$shop_id,'manufacture/web_material_subunit_type/get_by_shop/'.$shop_id);

		return $datas;
	}

	function get_by_id($id_en){

    	$datas = $this->CI->cache_util->select_data('model','web_material_subunit_type','get_by_id|'.$id_en,'manufacture/web_material_subunit_type/get_by_id/'.$id_en);

		return $datas;
	}

	function del_cache_by_shop($shop_id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','web_material_subunit_type','get_by_shop|'.$shop_id);

	}

	function del_cache_by_id($id){
		//echo 'get_by_id|'.$id_en;
		$this->CI->cache->delete('model','web_material_subunit_type','get_by_id|'.$id);

	}
	
	
}

/* End of file company_profile_model.php */
/* Location: ./application/models/company_profile_model.php */



