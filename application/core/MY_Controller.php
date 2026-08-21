<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Public_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->_init_admin_language();
    }

    protected function _init_admin_language()
    {
        $this->load->helper('admin_lang');
        $lang = admin_lang();
        // CI language folder names
        $ci_lang = ($lang === 'en') ? 'english' : 'thai';
        $this->config->set_item('language', $ci_lang);
        $this->lang->load('admin', $ci_lang);
    }
}

class Auth_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->auth_bl->check_session_exists();
        $this->_init_admin_language();
    }

    protected function _init_admin_language()
    {
        $this->load->helper('admin_lang');
        $lang = admin_lang();
        $ci_lang = ($lang === 'en') ? 'english' : 'thai';
        $this->config->set_item('language', $ci_lang);
        $this->lang->load('admin', $ci_lang);
    }
}
