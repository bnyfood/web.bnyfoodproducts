<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Random library : for create random library class.
*  Create by peak. 13/09/2013
**/
class Get_address_util 
{
	private $CI;

	public function __construct() 
	{				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();		

    }
	
	/*[make_seed][fn] : for create seed for use in random number fn*/
	public function get_macaddress()
	{
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $macAddr=false;

        #run the external command, break output into lines
        $arp=`arp -a $ipAddress`;
        $lines=explode("\n", $arp);

        #look for the output line describing our IP address
        foreach($lines as $line)
        {
            $cols=preg_split('/\s+/', trim($line));
            if ($cols[0]==$ipAddress)
            {
                $macAddr=$cols[1];
            }
        }

        return $macAddr;

	}


}

/* End of file random_util.php */
/* Location: ./application/libraries/util/random_util.php */