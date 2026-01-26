<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class Cache_util 
{

  private $_path;
	
	function __construct()
	{
		$this->CI =& get_instance();

    $this->_path = $this->CI->config->item('cache_path');

		
	}

  function select_data($m_dir,$dir,$cache_name,$api_url){

    $is_cache = $this->chk_cache($m_dir,$dir,$cache_name);
    if(!$is_cache){ 
      $datas = $this->CI->curl_bl->CallApi('GET',$api_url);
      if($datas['Status'] == "Success"){
        //print_r($data_menus['Data']);
        //$data = $datas['Data'];
        $this->CI->cache->save($m_dir,$dir,$cache_name, $datas, CAHCH_SEC);

      }
    }else{
      $datas = $this->CI->cache->get($m_dir,$dir,$cache_name);
    }

    return $datas;
  }

  function select_data_nospin($m_dir,$dir,$cache_name,$api_url){

    $is_cache = $this->chk_cache($m_dir,$dir,$cache_name);
    if(!$is_cache){ 
      $datas = $this->CI->curl_bl->CallApiNospi('GET',$api_url);
      if($datas['Status'] == "Success"){
        //print_r($data_menus['Data']);
        //$data = $datas['Data'];
        $this->CI->cache->save($m_dir,$dir,$cache_name, $datas, CAHCH_SEC);

      }
    }else{
      $datas = $this->CI->cache->get($m_dir,$dir,$cache_name);
    }

    return $datas;
  }
    

    function chk_cache($m_dir,$dir,$cache_name)
    { 

      //echo ">-------------------".DIRECTORY_SEPARATOR."-----------<";

      /*$this->CI->load->driver('cache',
        array('adapter' => 'file', 'backup' => 'file', 'key_prefix' => '')
      );*/

      $is_cache = true;
      if ( ! $cache_data = $this->CI->cache->get($m_dir,$dir,$cache_name)){

        //$cache_val = $this->cache->save($cache_name), $some_object, 120);
        $is_cache = false;
        //echo "------no";

     }



     return $is_cache;
    }



}