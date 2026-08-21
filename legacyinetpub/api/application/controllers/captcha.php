<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class captcha extends CI_Controller {

	function __construct() 
	{
		//:[Auto call parent construct]
        parent::__construct();
		$this->load->library('session');
		$this->load->helper('captcha');
    }

   
		    public function refresh()

		   {

		       				$config = array(
					        'word'          => '',
					        'img_path'      => './captcha/',
					        'img_url'       => 'http://wwwdev.bnyfoodproducts.com//captcha/',
					        'font_path'     => '../assets/fonts/EDMuzazhi.ttf',
					        'img_width'     => 180,
					        'img_height'    => 45,
					        'expiration'    => 7200,
					        'word_length'   => 8,
					        'font_size'     => 20,
					        'img_id'        => 'Imageid',
					        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

					        // White background and border, black text and red grid
					        'colors'        => array(
					                'background' => array(255, 255, 255),
					                'border' => array(255, 255, 255),
					                'text' => array(0, 0, 0),
					                'grid' => array(rand(0,255), rand(0,255), rand(0,255))
					        )
							);

		       $captcha = create_captcha($config);

		       $this->session->unset_userdata('valuecaptchaCode');

		       $this->session->set_userdata('valuecaptchaCode', $captcha['word']);



	
		$this->session->unset_userdata('valuecaptchaCode');

		$this->session->set_userdata('valuecaptchaCode', $captcha['word']);

		

//		Load the View

//		   $this->load->view('captcha/index', $data);
				       echo $captcha['image'];

		}
	
       


}
