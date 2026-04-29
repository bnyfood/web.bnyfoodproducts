<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facrbook_login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('businesslogic/fblogin_bl');
    }

    public function index()
    {
        $result = $this->fblogin_bl->get_login_url(base_url('Facrbook_login/callback'));

        if (!empty($result['status']) && !empty($result['login_url'])) {
            redirect($result['login_url'], 'refresh');
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function callback()
    {
        $result = $this->fblogin_bl->get_callback_data(base_url('Facrbook_login/callback'));
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
