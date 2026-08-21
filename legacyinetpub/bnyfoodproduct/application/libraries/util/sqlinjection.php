<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// create by Man 
// 27/06/2013 
class sqlinjection 
{
	
	function __construct()
	{
		$this->CI =& get_instance();


		$allinput=$this->CI->input->raw_input_stream;

		$allinput="";

		foreach ($_POST as $key => $value)
		 {
			$allinput.=$key.$value;
         }

         foreach ($_GET as $key => $value)
		 {
			$allinput.=$key.$value;
         }

        foreach ($_COOKIE as $key => $value)
		 {
			$allinput.=$key.$value;
         }

         foreach ($_SERVER as $key => $value)
		 {
			$allinput.=$key.$value;
         }



       
         
        // $allinput.=$this->CI->input->cookie();
        // $allinput.=$this->CI->input->server();

       // echo $allinput;


		$injection_string=array();


		$handle = fopen("c:\\inetpub\\injection\\injection.injection", "r");

// Output one line until end-of-file
while(!feof($handle)) {
	array_push($injection_string,fgets($handle));
  
}
fclose($handle);

		

		foreach($injection_string as $injection)
		{
			//echo "<br>".strtolower($injection).":".stripos(strtolower($allinput),strtolower($injection));

           if(stripos(strtolower($allinput),strtolower($injection))>0)
           {

           	die("injection found");
           }

		}


		
	}

	



}