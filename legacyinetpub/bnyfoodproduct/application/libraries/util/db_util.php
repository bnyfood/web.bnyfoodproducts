<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
** Random library : for create random library class.
*  Create by peak. 13/09/2013
**/
class Db_util 
{
	private $CI;
	
	
	public function __construct() 
	{				
		//:[Get instance codeigniter object for use method or attribute of codeigniter]
		$this->CI =& get_instance();		
	

    }

	
	
	/*[create_random_number][fn] : for create random number by digit
	  [use] "12345" = create_random_number(5);*/
	public function insert_batch($table,$arr_data)
	{
		//print_r($arr_data);
		if(!empty($arr_data)){
			if(isset($arr_data[0])){
				$arr_key = array_keys($arr_data[0]);
				//print_r($arr_key);
				$key = implode(",",$arr_key);
				$key = "(".$key.")";
				//echo $key;
			}	
			$cnt_arr = count($arr_data);
			//echo ">".$cnt_arr."<";
			$num = 1;
			$sql = "";
			$value = "";
			for($i=0;$i<=$cnt_arr-1;$i++){
				if(isset($arr_data[$i])){
					//print_r($arr_data[0]);
					$value_im = "#squotes#".implode("#squotes#,#squotes#",$arr_data[$i])."#squotes#";
					//$value_im = "'".implode("','",$arr_data[$i])."'";
					//echo $value_im."<br>";
					$value_im = str_replace("'", "''", $value_im);
					$value_im = str_replace("#squotes#", "'", $value_im);
					$value .= "(".$value_im.")";
					if($i < $cnt_arr-1){
						$value .= ",";
					}
					$num = $num +1;
				}
			}
			$sql = "INSERT INTO ".$table." ".$key." VALUES ".$value;

			//echo $sql;
			return $sql;
		}

	}

	function add_quotes($str) {
	    return sprintf("'%s'", $str);
	}

			

}

/* End of file random_util.php */
/* Location: ./application/libraries/util/random_util.php */