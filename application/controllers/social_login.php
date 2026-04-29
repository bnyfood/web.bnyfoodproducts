<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Social_login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util/View_util');
    }

    public function index()
    {
        $arr_input = array(
            'title' => 'Social Login'
        );

        // Put your uploaded logo at assets/images/social-login-logo.png
        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png')
        );

        $this->view_util->social_login('social_login/social_login', $data, NULL, NULL, $arr_input);
    }
}
