<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //$this->load->model('pic_gallery_model');
        $this->load->helper(array('form', 'url', 'file'));
    }

    public function index() {
    	
    	$pic_id_ref = $this->uri->segment(3);
    	//print_r($arr_pics);
    	$data = array(
    		'pic_id_ref' => $pic_id_ref,
    		'error' => ''
    	);
        $this->load->view('upload/upload', $data);
    }

    public function do_upload() {

        $upload_path_url = base_url() . 'uploads/';

        $config['upload_path'] = FCPATH . 'uploads/';
       $config['allowed_types'] = '*';
        $config['max_size'] = '30000';
	   $pic_id_ref = $this->input->post('pic_id_ref');
	//echo ">>>".$pic_id_ref;
        $this->load->library('upload', $config);
       // echo "h1";
        if (!$this->upload->do_upload()) {
            //echo "h2";
            //$error = array('error' => $this->upload->display_errors());
            //$this->load->view('upload', $error);
            
            //Load the list of existing files in the upload directory
          //  $existingFiles = get_dir_file_info($config['upload_path']);
            $foundFiles = array();
            $f=0;
    		
            /*$arr_pics = $this->pic_gallery_model->select_by_ref($pic_id_ref);


             foreach ($arr_pics as $arr_pic) {
             	$fileName = $arr_pic['pic_gallery_name'];
              if($fileName!='thumbs'){//Skip over thumbs directory
                //set the data for the json array   
                $arr_file_type = explode("/",$arr_pic['pic_type']);
                $file_type = $arr_file_type[0];
                $thumb_pic = $upload_path_url . 'thumbs/' . $fileName;
                if($file_type != "image"){
                	$thumb_pic = $this->get_thumb_no_pic($arr_file_type[1]);
		 }
                $foundFiles[$f]['name'] = $fileName;
                $foundFiles[$f]['size'] = intval($arr_pic['pic_size']);
                $foundFiles[$f]['url'] = $upload_path_url . $fileName;
                $foundFiles[$f]['thumbnailUrl'] = $thumb_pic;	
                $foundFiles[$f]['deleteUrl'] = base_url() . 'upload/deleteImage/' . $arr_pic['pic_gallery_id'];
                $foundFiles[$f]['deleteType'] = 'DELETE';
                $foundFiles[$f]['error'] = null;

                $f++;
              }
            }*/
            /*$this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('files' => $foundFiles)));*/
        } else {
            //echo "h3";
            $data = $this->upload->data();

           /* $data_pic = array(
            	'pic_gallery_ref' => $pic_id_ref,
            	'pic_gallery_name' => $data['file_name'],
            	'pic_size' => $data['file_size'] * 1024,
            	'pic_type' => $data['file_type'],
            	'pic_error' => '',
            	'cdate' => DATE_TIME_NOW 
            	
            );*/
           // print_r($data_pic);
          // $pic_gallery_id = $this->pic_gallery_model->insert($data_pic);
            
            /*
             * Array
              (
              [file_name] => png1.jpg
              [file_type] => image/jpeg
              [file_path] => /home/ipresupu/public_html/uploads/
              [full_path] => /home/ipresupu/public_html/uploads/png1.jpg
              [raw_name] => png1
              [orig_name] => png.jpg
              [client_name] => png.jpg
              [file_ext] => .jpg
              [file_size] => 456.93
              [is_image] => 1
              [image_width] => 1198
              [image_height] => 1166
              [image_type] => jpeg
              [image_size_str] => width="1198" height="1166"
              )
             */
            // to re-size for thumbnail images un-comment and set path here and in json array
            $config = array();
            $config['image_library'] = 'gd2';
            $config['source_image'] = $data['full_path'];
            $config['create_thumb'] = TRUE;
            $config['new_image'] = $data['file_path'] . 'thumbs/';
            $config['maintain_ratio'] = TRUE;
            $config['thumb_marker'] = '';
            $config['width'] = 75;
            $config['height'] = 50;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();

		$arr_file_type = explode("/",$data['file_type']);
                $file_type = $arr_file_type[0];
                $thumb_pic = $upload_path_url . 'thumbs/' . $data['file_name'];
                if($file_type != "image"){
		 	$thumb_pic = $this->get_thumb_no_pic($arr_file_type[1]);
		 }
		 
            //set the data for the json array
            $info = new StdClass;
            $info->name = $data['file_name'];
            $info->size = $data['file_size'] * 1024;
            $info->type = $data['file_type'];
            $info->url = $upload_path_url . $data['file_name'];
            // I set this to original file since I did not create thumbs.  change to thumbnail directory if you do = $upload_path_url .'/thumbs' .$data['file_name']
            $info->thumbnailUrl = $thumb_pic;
            $info->deleteUrl = base_url() . 'upload/deleteImage/';
            $info->deleteType = 'DELETE';
            $info->error = null;

            $files[] = $info;
            //this is why we put this in the constants to pass only json data
            if (IS_AJAX) {
                echo json_encode(array("files" => $files));
                //this has to be the only data returned or you will get an error.
                //if you don't give this a json array it will give you a Empty file upload result error
                //it you set this without the if(IS_AJAX)...else... you get ERROR:TRUE (my experience anyway)
                // so that this will still work if javascript is not enabled
            } else {
                $file_data['upload_data'] = $this->upload->data();
                $this->load->view('upload/upload_success', $file_data);
            }
            
        }
    }

    public function deleteImage() {//gets the job done but you might want to add error checking and security
    
    	$pic_id = $this->uri->segment(3);
	$arr_pic = $this->pic_gallery_model->select_by_id($pic_id);
   	$file = $arr_pic['pic_gallery_name'];
        $success = unlink(FCPATH . 'uploads/' . $file);
        $arr_file_type = explode(".",$file);
        $file_type = $arr_file_type[1];
        if(($file_type == "jpg")or ($file_type == "jpeg") or ($file_type == "gif") or ($file_type == "png") ){
		$success = unlink(FCPATH . 'uploads/thumbs/' . $file);
	}
        
        //info to see if it is doing what it is supposed to
        
        $this->pic_gallery_model->delete_by_name($file);
    $info = new StdClass;
        $info->sucess = $success;
        $info->path = base_url() . 'uploads/' . $file;
        $info->file = is_file(FCPATH . 'uploads/' . $file);

        if (IS_AJAX) {
            //I don't think it matters if this is set but good for error checking in the console/firebug
            echo json_encode(array($info));
        } else {
            //here you will need to decide what you want to show for a successful delete        
            $file_data['delete_data'] = $file;
            $this->load->view('upload/delete_success', $file_data);
        }
    }
	
	public function str_encode($fileName){
		/*$fileName = str_replace(".","_dot_",$fileName);
		$fileName = str_replace("(","_op_",$fileName);
		$fileName = str_replace(")","_cl_",$fileName);*/
		$fileName = str_replace(".","_dot_",$fileName);
		$fileName = urlencode($fileName);
		return $fileName;
	}
	
	public function str_decode($fileName){
		/*$fileName = str_replace("_dot_",".",$fileName);
		$fileName = str_replace("_op_","(",$fileName);
		$fileName = str_replace("_cl_",")",$fileName);*/
		$fileName = str_replace("_dot_",".",$fileName);
		$fileName = htmlentities($fileName);
		return $fileName;
	}
    
    public function get_thumb_no_pic($type){
  	
  	$thumb = null;
  	if(($type == "vnd.ms-excel") or ($type == "vnd.openxmlformats-officedocument.spreadsheetml.sheet")){
		$thumb = base_url() . 'uploads/thumbs/excel.gif';
	}elseif(($type == "msword") or ($type == "vnd.openxmlformats-officedocument.wordprocessingml.document")){
		$thumb = base_url() . 'uploads/thumbs/word.gif';
	}elseif(($type == "vnd.ms-powerpoint") or ($type == "vnd.openxmlformats-officedocument.presentationml.presentation")){
		$thumb = base_url() . 'uploads/thumbs/pptx.png';
	}elseif($type == "plain"){
		$thumb = base_url() . 'uploads/thumbs/txt.png';
	}elseif($type == "pdf"){
		$thumb = base_url() . 'uploads/thumbs/pdf.png';
	}elseif(($type == "octet-stream")or($type == "zip")){
		$thumb = base_url() . 'uploads/thumbs/zip.png';
	}
	
	return $thumb;
  }

}