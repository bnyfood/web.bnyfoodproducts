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
        $this->load->model('biggrill_data_model');
    }

    public function bny_luckydraw()
    {
        if (!$this->session->userdata(SESSION_PREFIX . 'web_user_login_id')) {
            redirect(base_url('social_login'), 'refresh');
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

    /**
     * หน้าผลลัพธ์หลัง login ครบ (ต้องมี session/cookie web_user_login_id และเบอร์โทร)
     */
    public function bny_luckyresult()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $profile = $this->get_customer_profile_for_display();
        if (empty($profile['web_user_phone'])) {
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'ผลลัพธ์แต้มสะสม',
        );

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'customer_name' => $profile['web_user_name'],
            'customer_phone' => $profile['web_user_phone'],
            'week_range_label' => $this->get_current_week_range_label(),
            'points_display' => $this->biggrill_data_model->select_point_by_phone($login_id),
        );

        $this->view_util->social_login('bnyreward/bny_luckyresult', $data, null, null, $arr_input);
    }

    public function bny_changephone()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $profile = $this->get_customer_profile_for_display();
        if (empty($profile['web_user_phone'])) {
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'เปลี่ยนเบอร์โทรศัพท์',
        );
        $arr_js = array(
            'validate' => base_url() . "assets/js/jquery.validate.min.js",
            'bny_changephone' => base_url() . "resources/js/validate/bny_changephone_form.js",
        );

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'old_phone' => $profile['web_user_phone'],
            'current_phone' => $profile['web_user_phone'],
            'error_msg' => $this->session->flashdata('bny_changephone_error'),
        );

        $this->view_util->social_login('bnyreward/bny_changephone', $data, null, $arr_js, $arr_input);
    }

    public function submit_bny_changephone()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $old_phone_input = $this->sanitize_phone($this->input->post('web_user_phone_old', true));
        $new_phone = $this->sanitize_phone($this->input->post('web_user_phone_new', true));
        if ($old_phone_input === null || $new_phone === null || strlen($old_phone_input) < 10 || strlen($new_phone) < 10) {
            $this->session->set_flashdata('bny_changephone_error', 'กรุณากรอกเบอร์เก่าและเบอร์ใหม่ให้ถูกต้อง');
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }

        $login_row = $this->web_user_login_model->select_by_id($login_id);
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $current_phone = $this->sanitize_phone(isset($login_row['web_user_phone']) ? $login_row['web_user_phone'] : '');
        if ($current_phone === null || $old_phone_input !== $current_phone) {
            $this->session->set_flashdata('bny_changephone_error', 'เบอร์โทรเก่าไม่ถูกต้อง');
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }
        if ($new_phone === $current_phone) {
            $this->session->set_flashdata('bny_changephone_error', 'เบอร์โทรใหม่ต้องไม่ซ้ำเบอร์เดิม');
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }

        $key_otp = (int) $this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }

        $exists = $this->web_user_phone_model->get_by_login_and_phone($login_id, $new_phone);
        if (empty($exists)) {
            $this->web_user_phone_model->insert(array(
                'web_user_login_id' => (int) $login_id,
                'web_user_phone' => $new_phone,
                'is_verify' => 0,
                'key_otp' => $key_otp,
                'cdate' => DATE_TIME_NOW,
            ));
        } else {
            $this->web_user_phone_model->update_key_otp_by_login_and_phone($login_id, $new_phone, $key_otp);
        }

        $this->session->set_userdata(SESSION_PREFIX . 'pending_changephone_new', $new_phone);
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'pending_changephone_new',
            'value' => $this->encryption_util->encrypt_ssl($new_phone),
            'expire' => 86400,
            'path' => '/',
            'secure' => false,
        ));

        redirect(base_url('bnyreward/bny_changephoneotp'), 'refresh');
    }

    public function bny_changephoneotp()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $new_phone = $this->get_pending_changephone_new();
        if (empty($new_phone)) {
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'ยืนยัน OTP เปลี่ยนเบอร์',
        );
        $arr_js = array(
            'validate' => base_url() . "assets/js/jquery.validate.min.js",
            'bny_changephoneotp' => base_url() . "resources/js/validate/bny_changephoneotp_form.js",
        );
        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'new_phone' => $new_phone,
            'validate_otp_url' => base_url('bnyreward/validate_changephone_otp'),
            'error_msg' => $this->session->flashdata('bny_changephoneotp_error'),
        );
        $this->view_util->social_login('bnyreward/bny_changephoneotp', $data, null, $arr_js, $arr_input);
    }

    public function validate_changephone_otp()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        $new_phone = $this->get_pending_changephone_new();
        $key_otp = trim((string) $this->input->post('key_otp', true));
        $ok = false;
        if ($login_id !== null && $new_phone !== null && strlen($key_otp) === 6 && ctype_digit($key_otp)) {
            $row = $this->web_user_phone_model->get_by_login_and_phone($login_id, $new_phone);
            if (!empty($row) && (string) $row['key_otp'] === $key_otp) {
                $ok = true;
            }
        }
        $this->output
            ->set_content_type('application/json; charset=utf-8')
            ->set_output(json_encode($ok ? true : 'รหัส OTP ไม่ถูกต้อง'));
    }

    public function submit_bny_changephoneotp()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $new_phone = $this->get_pending_changephone_new();
        $key_otp = trim((string) $this->input->post('key_otp', true));
        if (empty($new_phone) || strlen($key_otp) !== 6 || !ctype_digit($key_otp)) {
            $this->session->set_flashdata('bny_changephoneotp_error', 'กรุณากรอก OTP 6 หลัก');
            redirect(base_url('bnyreward/bny_changephoneotp'), 'refresh');
            return;
        }
        $row = $this->web_user_phone_model->get_by_login_and_phone($login_id, $new_phone);
        if (empty($row) || (string) $row['key_otp'] !== $key_otp) {
            $this->session->set_flashdata('bny_changephoneotp_error', 'รหัส OTP ไม่ถูกต้อง');
            redirect(base_url('bnyreward/bny_changephoneotp'), 'refresh');
            return;
        }

        $this->web_user_phone_model->set_verified($login_id, $new_phone);
        $this->web_user_login_model->update_by_id($login_id, array(
            'web_user_phone' => $new_phone,
            'last_login_time' => DATE_TIME_NOW,
        ));
        $this->session->set_userdata(SESSION_PREFIX . 'web_user_phone', $new_phone);
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'web_user_phone',
            'value' => $this->encryption_util->encrypt_ssl($new_phone),
            'expire' => 31536000,
            'path' => '/',
            'secure' => false,
        ));
        $this->session->unset_userdata(SESSION_PREFIX . 'pending_changephone_new');
        delete_cookie(COOKIE_PREFIX . 'pending_changephone_new');

        redirect(base_url('bnyreward/bny_luckyresult'), 'refresh');
    }

    public function resend_changephone_otp()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $new_phone = $this->get_pending_changephone_new();
        if (empty($new_phone)) {
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }
        $row = $this->web_user_phone_model->get_by_login_and_phone($login_id, $new_phone);
        if (empty($row)) {
            $this->session->set_flashdata('bny_changephoneotp_error', 'ไม่พบข้อมูลเบอร์ใหม่ กรุณากรอกใหม่อีกครั้ง');
            redirect(base_url('bnyreward/bny_changephone'), 'refresh');
            return;
        }
        $key_otp = (int) $this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }
        $this->web_user_phone_model->update_key_otp_by_login_and_phone($login_id, $new_phone, $key_otp);
        $this->session->set_flashdata('bny_changephoneotp_error', 'ส่ง OTP ใหม่แล้ว (ทดสอบ)');
        redirect(base_url('bnyreward/bny_changephoneotp'), 'refresh');
    }

    public function bny_logout()
    {
        $session_keys = array(
            'web_user_login_id',
            'facebook_id', 'facebook_name', 'facebook_email', 'facebook_phone',
            'google_id', 'google_name', 'google_email', 'google_phone',
            'web_user_name', 'web_user_phone',
            'pending_verify_phone',
            'pending_changephone_new',
        );
        foreach ($session_keys as $k) {
            $this->session->unset_userdata(SESSION_PREFIX . $k);
        }

        $cookie_keys = array(
            'web_user_login_id',
            'facebook_id', 'facebook_name', 'facebook_email', 'facebook_phone',
            'google_id', 'google_name', 'google_email', 'google_phone',
            'web_user_name', 'web_user_phone',
            'pending_web_user_phone',
            'pending_changephone_new',
        );
        foreach ($cookie_keys as $k) {
            delete_cookie(COOKIE_PREFIX . $k);
        }

        $this->session->sess_destroy();
        redirect(base_url('social_login'), 'refresh');
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

    private function get_login_id_from_session_or_cookie()
    {
        $id = $this->get_login_id();
        if ($id !== null) {
            return $id;
        }
        $id_cookie = get_cookie(COOKIE_PREFIX . 'web_user_login_id');
        if (empty($id_cookie)) {
            return null;
        }
        $dec = $this->encryption_util->decrypt_ssl($id_cookie);
        if ($dec === null || $dec === '') {
            return null;
        }
        return (int) $dec;
    }

    private function get_customer_profile_for_display()
    {
        $name = (string) $this->session->userdata(SESSION_PREFIX . 'web_user_name');
        $phone = (string) $this->session->userdata(SESSION_PREFIX . 'web_user_phone');

        if ($name === '') {
            $c = get_cookie(COOKIE_PREFIX . 'web_user_name');
            if (!empty($c)) {
                $name = (string) $this->encryption_util->decrypt_ssl($c);
            }
        }
        if ($phone === '') {
            $c = get_cookie(COOKIE_PREFIX . 'web_user_phone');
            if (!empty($c)) {
                $phone = (string) $this->encryption_util->decrypt_ssl($c);
            }
        }

        if ($name === '' || $phone === '') {
            $login_id = $this->get_login_id_from_session_or_cookie();
            if ($login_id !== null) {
                $row = $this->web_user_login_model->select_by_id($login_id);
                if (!empty($row)) {
                    if ($name === '' && !empty($row['web_user_name'])) {
                        $name = (string) $row['web_user_name'];
                    }
                    if ($phone === '' && !empty($row['web_user_phone'])) {
                        $phone = (string) $row['web_user_phone'];
                    }
                }
            }
        }

        return array(
            'web_user_name' => trim($name),
            'web_user_phone' => trim($phone),
        );
    }

    private function get_current_week_range_label()
    {
        $tz = new DateTimeZone('Asia/Bangkok');
        $now = new DateTime('now', $tz);
        $n = (int) $now->format('N');
        $monday = clone $now;
        $monday->modify('-' . ($n - 1) . ' days');
        $monday->setTime(10, 0, 0);
        $sunday = clone $monday;
        $sunday->modify('+6 days');
        $sunday->setTime(23, 59, 59);

        return $monday->format('d/m/Y H:i') . ' - ' . $sunday->format('d/m/Y H:i');
    }

    private function sanitize_phone($p)
    {
        if ($p === null) {
            return null;
        }
        $d = preg_replace('/[^0-9]/', '', (string) $p);
        return $d === '' ? null : $d;
    }

    private function get_pending_changephone_new()
    {
        $phone = (string) $this->session->userdata(SESSION_PREFIX . 'pending_changephone_new');
        if ($phone !== '') {
            return $phone;
        }
        $enc = get_cookie(COOKIE_PREFIX . 'pending_changephone_new');
        if (empty($enc)) {
            return null;
        }
        $dec = $this->encryption_util->decrypt_ssl($enc);
        $phone = $this->sanitize_phone($dec);
        return $phone === null ? null : $phone;
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
