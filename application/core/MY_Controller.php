<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Public_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // ไม่ทำอะไร = public
    }
}

class Auth_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->auth_bl->check_session_exists();
    }
}
