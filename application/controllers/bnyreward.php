<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bnyreward extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util/View_util');
        $this->load->library('util/random_util');
        $this->load->library('util/encryption_util');
        $this->load->helper('cookie');
        $this->load->model('web_user_login_model');
        $this->load->model('web_user_phone_model');
    }

    public function bny_luckydraw()
    {
        if (!$this->session->userdata(SESSION_PREFIX . 'web_user_login_id')) {
            redirect(base_url('social_login/index'), 'refresh');
            return;
        }

        $w = (string) $this->session->userdata(SESSION_PREFIX . 'web_user_phone');
        if (trim($w) !== '') {
            redirect(base_url(), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'ยืนยันเบอร์โทร',
        );
        $arr_js = array(
            'bny_luckydraw' => base_url() . 'resources/js/bnyreward/bny_luckydraw.js',
        );

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'bny_luckydraw_config' => array(
                'submitPhoneUrl' => site_url('bnyreward/submit_phone'),
                'verifyOtpUrl' => site_url('bnyreward/verify_otp'),
                'resendOtpUrl' => site_url('bnyreward/resend_otp'),
            ),
        );

        $this->view_util->social_login('bnyreward/bny_luckydraw', $data, null, $arr_js, $arr_input);
    }

    public function submit_phone()
    {
        $out = $this->json_init();
        $web_user_login_id = $this->get_login_id();
        if ($web_user_login_id === null) {
            $out['message'] = 'Session หมด กรุณา login ใหม่';
            $this->json_out($out);
            return;
        }
        $phone = $this->input->post('web_user_phone', true);
        $phone = $this->sanitize_phone($phone);
        if ($phone === null || strlen($phone) < 9) {
            $out['message'] = 'เบอร์โทรไม่ถูกต้อง';
            $this->json_out($out);
            return;
        }

        $key_otp = (int) $this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }

        $existing = $this->web_user_phone_model->get_by_login_and_phone($web_user_login_id, $phone);
        if (empty($existing)) {
            $this->web_user_phone_model->insert(array(
                'web_user_login_id' => $web_user_login_id,
                'web_user_phone' => $phone,
                'is_verify' => 0,
                'key_otp' => $key_otp,
                'cdate' => DATE_TIME_NOW,
            ));
        } else {
            $this->web_user_phone_model->update_key_otp_by_login_and_phone(
                $web_user_login_id,
                $phone,
                $key_otp
            );
        }

        $this->session->set_userdata(SESSION_PREFIX . 'pending_verify_phone', $phone);
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'pending_web_user_phone',
            'value' => $this->encryption_util->encrypt_ssl($phone),
            'expire' => 86400,
            'path' => '/',
            'secure' => false,
        ));

        $out['status'] = true;
        $out['message'] = 'บันทึกเบอร์แล้ว กรุณากรอกรหัส OTP 6 หลัก';
        $this->json_out($out);
    }

    public function verify_otp()
    {
        $out = $this->json_init();
        $web_user_login_id = $this->get_login_id();
        if ($web_user_login_id === null) {
            $out['message'] = 'Session หมด กรุณา login ใหม่';
            $this->json_out($out);
            return;
        }
        $phone = $this->input->post('web_user_phone', true);
        $phone = $this->sanitize_phone($phone);
        $key_in = $this->input->post('key_otp', true);
        if ($phone === null || $key_in === null || $key_in === '') {
            $out['message'] = 'ข้อมูลไม่ครบ';
            $this->json_out($out);
            return;
        }
        $key_in = trim((string) $key_in);
        if (strlen($key_in) !== 6 || !ctype_digit($key_in)) {
            $out['message'] = 'รหัส OTP ต้องเป็นตัวเลข 6 หลัก';
            $this->json_out($out);
            return;
        }

        $row = $this->web_user_phone_model->get_by_login_and_phone($web_user_login_id, $phone);
        if (empty($row) || (string) $row['key_otp'] !== $key_in) {
            $out['message'] = 'รหัส OTP ไม่ถูกต้อง กดเพื่อส่ง OTP อีกครั้ง';
            $out['resend_suggest'] = true;
            $out['status'] = false;
            $this->json_out($out);
            return;
        }

        $this->web_user_phone_model->set_verified($web_user_login_id, $phone);
        $this->web_user_login_model->update_by_id($web_user_login_id, array('web_user_phone' => $phone));
        $this->session->set_userdata(SESSION_PREFIX . 'web_user_phone', $phone);
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'web_user_phone',
            'value' => $this->encryption_util->encrypt_ssl($phone),
            'expire' => 31536000,
            'path' => '/',
            'secure' => false,
        ));
        $this->session->unset_userdata(SESSION_PREFIX . 'pending_verify_phone');

        $out['status'] = true;
        $out['message'] = 'ยืนยันเบอร์สำเร็จ';
        $out['redirect'] = base_url();
        $this->json_out($out);
    }

    public function resend_otp()
    {
        $out = $this->json_init();
        $web_user_login_id = $this->get_login_id();
        if ($web_user_login_id === null) {
            $out['message'] = 'Session หมด กรุณา login ใหม่';
            $this->json_out($out);
            return;
        }
        $phone = $this->input->post('web_user_phone', true);
        $phone = $this->sanitize_phone($phone);
        if ($phone === null) {
            $out['message'] = 'เบอร์ไม่ถูกต้อง';
            $this->json_out($out);
            return;
        }

        $row = $this->web_user_phone_model->get_by_login_and_phone($web_user_login_id, $phone);
        if (empty($row)) {
            $out['message'] = 'ไม่พบรายการเบอร์นี้ กรุณากรอกเบอร์อีกครั้ง';
            $this->json_out($out);
            return;
        }

        $key_otp = (int) $this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }
        $this->web_user_phone_model->update_key_otp_by_login_and_phone($web_user_login_id, $phone, $key_otp);

        $out['status'] = true;
        $out['message'] = 'ส่ง OTP ใหม่แล้ว';
        $this->json_out($out);
    }

    private function get_login_id()
    {
        $s = $this->session->userdata(SESSION_PREFIX . 'web_user_login_id');
        if ($s === null || $s === '') {
            return null;
        }
        return (int) $s;
    }

    private function sanitize_phone($p)
    {
        if ($p === null) {
            return null;
        }
        $d = preg_replace('/[^0-9]/', '', (string) $p);
        return $d === '' ? null : $d;
    }

    private function json_init()
    {
        return array('status' => false);
    }

    private function json_out($data, $http = 200)
    {
        $this->output
            ->set_status_header($http)
            ->set_content_type('application/json; charset=utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
