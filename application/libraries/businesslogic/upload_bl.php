<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_bl{
	public function __construct(){
		$this->CI =& get_instance();
		

	}
	
	
	function upload_file_pic($file){
		
	   	$config['upload_path'] = './uploads/products/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '3072';
        //$config['max_width']  = '2048';
        //$config['max_height']  = '768';
        $new_name = "";
        if (!empty($_FILES[$file]['name'])) {
         $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        }
		$config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
        $this->CI->upload->initialize($config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
        }
        
        return $file_name;
        
	}

    function upload_file_pic_path($path,$file){
        
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '3072';
        //$config['max_width']  = '2048';
        //$config['max_height']  = '768';
        $new_name = "";
        if (!empty($_FILES[$file]['name'])) {
         $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        }
        $config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
        $this->CI->upload->initialize($config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
        }
        
        return $file_name;
        
    }

    private function upload_multi_files($path, $files)
    {
        $new_name = "";
        $config = array(
            'upload_path'   => $path,
            'allowed_types' => 'jpg|gif|png',
            'overwrite'     => 1,                       
        );

        $this->CI->load->library('upload', $config);

        $images = array();

        foreach ($files['name'] as $key => $image) {
            $_FILES['images[]']['name']= $files['name'][$key];
            $_FILES['images[]']['type']= $files['type'][$key];
            $_FILES['images[]']['tmp_name']= $files['tmp_name'][$key];
            $_FILES['images[]']['error']= $files['error'][$key];
            $_FILES['images[]']['size']= $files['size'][$key];

            if (!empty($files['name'][$key])) {
                $random_digi = $this->create_random_number(8);
                $temp = explode(".", $files['name'][$key]);
                $new_name = time().$random_digi.'.'. end($temp);
            }

            $fileName = $new_name;

            $images[] = $fileName;

            $config['file_name'] = $fileName;

            $this->CI->upload->initialize($config);

            if ($this->CI->upload->do_upload('images[]')) {
                $this->CI->upload->data();
            } else {
                return false;
            }
        }

        return $images;
    }
	
	function upload_file_pdf($file){
		
	   	$config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = '0';
        $new_name = "";
       // echo $_FILES[$file]['type'];
        if (!empty($_FILES[$file]['name'])) {
   
        $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        
        }
		$config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
       $this->CI->upload->initialize($config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
        }
        
        return $file_name;
        
	}

    function upload_file($file){
        
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xls|xlsx|docx|txt';
        $config['max_size'] = '3072';
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';
        $new_name = "";
        
        if (!empty($_FILES[$file]['name'])) {
        $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        
        }
        $config['file_name'] = $new_name;

        //echo $config['file_name']."<br>";

       $this->CI->load->library('upload', $config);
       $this->CI->upload->initialize($config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           $is_upload = 0;
         //  echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
            $is_upload = 1;
        }

        //echo "--F--".$config['file_name']."<br>";

        $data = array(
            'file_name' => $file_name,
            'is_upload' => $is_upload
        );
        
        return $data;
        
    }

    function upload_file2($file){
        
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'xls|xlsx|docx|txt';
        $config['max_size'] = '3072';
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';
        $new_name = "";
        
        if (!empty($_FILES[$file]['name'])) {
        $random_digi = $this->create_random_number2(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        
        }
        $config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           $is_upload = 0;
         //  echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
            $is_upload = 1;
        }

        $data = array(
            'file_name' => $file_name,
            'is_upload' => $is_upload
        );
        
        return $data;
        
    }
	
	function upload_file_txt($file){
		
	   	$config['upload_path'] = './uploads/files/';
        $config['allowed_types'] = 'xls|xlsx|docx|txt';
        $config['max_size'] = '3072';
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';
        $new_name = "";
		
		if (!empty($_FILES[$file]['name'])) {
        $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        
        }
		$config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           $is_upload = 0;
         //  echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
            $is_upload = 1;
        }
        
        return $file_name;
        
	}
	
	function upload_file_path($file,$path){

		
		$config['upload_path'] = $path;
        $config['allowed_types'] = '*';
        $config['max_size'] = '3072';
       /* $config['max_size'] = '1024';
        $config['max_width']  = '1024';
        $config['max_height']  = '768';
        */
        $new_name = "";
        if (!empty($_FILES[$file]['name'])) {
        $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $datetime_now = date("Y-m-d_H-i-s");
        $filename_new = trim($_FILES[$file]['name']);
        $filename_new = str_replace(" ","-",$filename_new);
        $new_name = $datetime_now."_".$filename_new;
        
        }
		$config['file_name'] = $new_name;

       $this->CI->load->library('upload', $config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           $is_upload = 0;
           //echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
            $is_upload = 1;
        }
        
        $data = array(
        	'file_name' => $file_name,
        	'is_upload' => $is_upload
        );
        
        return $data;
        
	}

    function upload_file_xls($file){
        
        $config['upload_path'] = APP_STORE_PATH."/uploads/xls/";
        $config['allowed_types'] = 'xls|xlsx|docx|txt';
        $config['max_size'] = '3072';
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';
        $new_name = "";
        
        if (!empty($_FILES[$file]['name'])) {
        $random_digi = $this->create_random_number(8);
        $temp = explode(".", $_FILES[$file]['name']);
        $new_name = time().$random_digi.'.'. end($temp);
        
        }
        $config['file_name'] = $new_name;

        //echo $config['file_name']."<br>";

       $this->CI->load->library('upload', $config);
       $this->CI->upload->initialize($config);
        if ( ! $this->CI->upload->do_upload($file))
        {
           $file_name = "";
           $is_upload = 0;
         //  echo $this->CI->upload->display_errors();
        }
        else
        {
            $file_name = $new_name;
            $is_upload = 1;
        }

        //echo "--F--".$config['file_name']."<br>";

        $data = array(
            'file_name' => $file_name,
            'is_upload' => $is_upload
        );
        
        return $data;
        
    }
	
	
	public function create_random_number($n)
	{
		
		$ran_num = '';
			
		for ($i = 0;$i<$n; $i++){
		
			$rand_val =  mt_rand(0,9);
			
			$ran_num .= (string)$rand_val;
		}
		
		return $ran_num;
	}

    public function create_random_number2($n)
    {
        
        $ran_num = '';
            
        for ($i = 0;$i<$n; $i++){
        
            $rand_val =  mt_rand(0,9);
            
            $ran_num .= (string)$rand_val;
        }
        
        return $ran_num;
    }
	
	
}

/* End of file guide_bl.php */
/* Location: ./application/libraries/business_logic/member_bl.php */