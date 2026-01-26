<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Random library : for create random library class.
*  Create by peak. 13/09/2013
**/
class Random_util 
{
	private $CI;
	
	

	public function __construct() 
	{				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();		

    }

	
	
	/*[create_random_number][fn] : for create random number by digit
	  [use] "12345" = create_random_number(5);*/
	public function create_random_number($n)
	{
		$seed = $this->make_seed();
		srand($seed);
		
		$ran_num = '';
			
		for ($i = 0;$i<$n; $i++){
		
			$rand_val =  mt_rand(0,9);
			
			$ran_num .= (string)$rand_val;
		}
		
		return $ran_num;
	}

	public function create_activate_code($n)
	{

		$ran_num = '';
			
		for ($i = 0;$i<$n; $i++){
		
			$rand_val =  mt_rand(0,9);
			
			$ran_num .= (string)$rand_val;
		}
		
		return $ran_num;
	}
	
	
	/*[make_seed][fn] : for create seed for use in random number fn*/
	public function make_seed()
	{
	  list($usec, $sec) = explode(' ', microtime());
	  return (float) $sec + ((float) $usec * 100000);
	}


}

/* End of file random_util.php */
/* Location: ./application/libraries/util/random_util.php */