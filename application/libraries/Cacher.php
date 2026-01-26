<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cacher{
  public function __construct(){
    $this->CI =& get_instance();
    $this->initiate_cache();

  }
  
  
  public function initiate_cache()
  {
    $this->CI->load->driver('cache',
        array('adapter' => 'file', 'backup' => 'file', 'key_prefix' => '')
      );
  }
  

  
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */