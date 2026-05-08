<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Social_login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util/View_util');
        $this->load->library('util/encryption_util');
        $this->load->library('util/random_util');
        $this->load->helper('cookie');
        $this->load->model('web_user_login_model');
        $this->load->model('web_user_phone_model');
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

    public function social_register()
    {
        $arr_input = array(
            'title' => 'Social Register'
        );

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png')
        );

        $this->view_util->social_login('social_login/social_register', $data, NULL, NULL, $arr_input);
    }

    public function bnyregister_form()
    {
        $login_row = $this->get_current_login_row();
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'Register Form'
        );

        $web_user_name = '';
        if (!empty($login_row['web_user_name'])) {
            $web_user_name = $login_row['web_user_name'];
        } elseif (!empty($login_row['facebook_name'])) {
            $web_user_name = $login_row['facebook_name'];
        } elseif (!empty($login_row['google_name'])) {
            $web_user_name = $login_row['google_name'];
        }

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'web_user_name' => $web_user_name,
            'web_user_phone' => !empty($login_row['web_user_phone']) ? $login_row['web_user_phone'] : '',
            'error_msg' => $this->session->flashdata('bnyregister_error')
        );

        $arr_js = array(
            'validate' => base_url()."assets/js/jquery.validate.min.js",
            'bnyregister_form' => base_url()."resources/js/validate/bnyregister_form.js"
        );

        $this->view_util->social_login('social_login/bnyregister_form', $data, NULL, $arr_js, $arr_input);
    }

    public function submit_bnyregister_form()
    {
        $login_row = $this->get_current_login_row();
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $web_user_name = trim((string)$this->input->post('web_user_name', true));
        $web_user_phone = preg_replace('/[^0-9]/', '', (string)$this->input->post('web_user_phone', true));

        if ($web_user_name === '' || $web_user_phone === '' || strlen($web_user_phone) < 9) {
            $this->session->set_flashdata('bnyregister_error', 'กรุณากรอกชื่อและเบอร์โทรให้ถูกต้อง');
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $current_id = (int)$login_row['web_user_login_id'];
        $key_otp = (int)$this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }

        $exists = $this->web_user_phone_model->get_by_login_and_phone($current_id, $web_user_phone);
        if (empty($exists)) {
            $this->web_user_phone_model->insert(array(
                'web_user_login_id' => $current_id,
                'web_user_phone' => $web_user_phone,
                'is_verify' => 0,
                'key_otp' => $key_otp,
                'cdate' => DATE_TIME_NOW,
            ));
        } else {
            $this->web_user_phone_model->update_key_otp_by_login_and_phone($current_id, $web_user_phone, $key_otp);
        }

        $this->session->set_userdata(SESSION_PREFIX . 'pending_register_name', $web_user_name);
        $this->session->set_userdata(SESSION_PREFIX . 'pending_register_phone', $web_user_phone);
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'pending_register_name',
            'value' => $this->encryption_util->encrypt_ssl($web_user_name),
            'expire' => 86400,
            'path' => '/',
            'secure' => false
        ));
        $this->input->set_cookie(array(
            'name' => COOKIE_PREFIX . 'pending_register_phone',
            'value' => $this->encryption_util->encrypt_ssl($web_user_phone),
            'expire' => 86400,
            'path' => '/',
            'secure' => false
        ));

        redirect(base_url('social_login/bnyregister_otp'), 'refresh');
    }

    public function bnyregister_otp()
    {
        $login_row = $this->get_current_login_row();
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $pending = $this->get_pending_register_data();
        if (empty($pending['web_user_phone']) || empty($pending['web_user_name'])) {
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $arr_input = array(
            'title' => 'Register OTP'
        );
        $arr_js = array(
            'validate' => base_url()."assets/js/jquery.validate.min.js",
            'bnyregister_otp' => base_url()."resources/js/validate/bnyregister_otp_form.js"
        );
        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'new_phone' => $pending['web_user_phone'],
            'validate_otp_url' => base_url('social_login/validate_bnyregister_otp'),
            'error_msg' => $this->session->flashdata('bnyregister_otp_error')
        );
        $this->view_util->social_login('social_login/bnyregister_otp', $data, NULL, $arr_js, $arr_input);
    }

    public function validate_bnyregister_otp()
    {
        $login_row = $this->get_current_login_row();
        $pending = $this->get_pending_register_data();
        $key_otp = trim((string)$this->input->post('key_otp', true));
        $ok = false;
        if (!empty($login_row) && !empty($pending['web_user_phone']) && strlen($key_otp) === 6 && ctype_digit($key_otp)) {
            $row = $this->web_user_phone_model->get_by_login_and_phone((int)$login_row['web_user_login_id'], $pending['web_user_phone']);
            if (!empty($row) && (string)$row['key_otp'] === $key_otp) {
                $ok = true;
            }
        }
        $this->output
            ->set_content_type('application/json; charset=utf-8')
            ->set_output(json_encode($ok ? true : 'รหัส OTP ไม่ถูกต้อง'));
    }

    public function submit_bnyregister_otp()
    {
        $login_row = $this->get_current_login_row();
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $pending = $this->get_pending_register_data();
        if (empty($pending['web_user_phone']) || empty($pending['web_user_name'])) {
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }
        $key_otp = trim((string)$this->input->post('key_otp', true));
        if (strlen($key_otp) !== 6 || !ctype_digit($key_otp)) {
            $this->session->set_flashdata('bnyregister_otp_error', 'กรุณากรอก OTP 6 หลัก');
            redirect(base_url('social_login/bnyregister_otp'), 'refresh');
            return;
        }

        $current_id = (int)$login_row['web_user_login_id'];
        $web_user_name = $pending['web_user_name'];
        $web_user_phone = $pending['web_user_phone'];
        $otp_row = $this->web_user_phone_model->get_by_login_and_phone($current_id, $web_user_phone);
        if (empty($otp_row) || (string)$otp_row['key_otp'] !== $key_otp) {
            $this->session->set_flashdata('bnyregister_otp_error', 'รหัส OTP ไม่ถูกต้อง');
            redirect(base_url('social_login/bnyregister_otp'), 'refresh');
            return;
        }

        $phone_row = $this->web_user_login_model->select_by_web_user_phone($web_user_phone);

        if (empty($phone_row)) {
            $this->web_user_login_model->update_by_id($current_id, array(
                'web_user_name' => $web_user_name,
                'web_user_phone' => $web_user_phone,
                'last_login_time' => DATE_TIME_NOW
            ));
            $this->web_user_phone_model->set_verified($current_id, $web_user_phone);
            $this->ensure_web_user_phone_record($current_id, $web_user_phone);
            $updated_row = $this->web_user_login_model->select_by_id($current_id);
            $this->set_social_session_cookie($updated_row);
            $this->clear_pending_register_data();
            redirect(base_url('bnyreward/bny_luckyresult'), 'refresh');
            return;
        }

        $phone_id = (int)$phone_row['web_user_login_id'];
        if ($phone_id === $current_id) {
            $this->web_user_login_model->update_by_id($current_id, array(
                'web_user_name' => $web_user_name,
                'web_user_phone' => $web_user_phone,
                'last_login_time' => DATE_TIME_NOW
            ));
            $this->web_user_phone_model->set_verified($current_id, $web_user_phone);
            $this->ensure_web_user_phone_record($current_id, $web_user_phone);
            $updated_row = $this->web_user_login_model->select_by_id($current_id);
            $this->set_social_session_cookie($updated_row);
            $this->clear_pending_register_data();
            redirect(base_url('bnyreward/bny_luckyresult'), 'refresh');
            return;
        }

        $merge_data = array(
            'web_user_name' => $web_user_name,
            'web_user_phone' => $web_user_phone,
            'last_login_time' => DATE_TIME_NOW,
        );

        $merge_fields = array(
            'facebook_id','facebook_name','facebook_email','facebook_phone',
            'google_id','google_name','google_email','google_phone'
        );
        foreach ($merge_fields as $f) {
            if (!empty($login_row[$f])) {
                $merge_data[$f] = $login_row[$f];
            }
        }

        $this->web_user_login_model->update_by_id($phone_id, $merge_data);
        $this->ensure_web_user_phone_record($phone_id, $web_user_phone);
        $this->web_user_phone_model->set_verified($phone_id, $web_user_phone);
        $this->web_user_login_model->delete_by_id($current_id);

        $target_row = $this->web_user_login_model->select_by_id($phone_id);
        $this->set_social_session_cookie($target_row);
        $this->clear_pending_register_data();
        redirect(base_url('bnyreward/bny_luckyresult'), 'refresh');
    }

    public function resend_bnyregister_otp()
    {
        $login_row = $this->get_current_login_row();
        if (empty($login_row)) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }
        $pending = $this->get_pending_register_data();
        if (empty($pending['web_user_phone'])) {
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $current_id = (int)$login_row['web_user_login_id'];
        $row = $this->web_user_phone_model->get_by_login_and_phone($current_id, $pending['web_user_phone']);
        if (empty($row)) {
            $this->session->set_flashdata('bnyregister_otp_error', 'ไม่พบข้อมูลเบอร์ กรุณากรอกใหม่อีกครั้ง');
            redirect(base_url('social_login/bnyregister_form'), 'refresh');
            return;
        }

        $key_otp = (int)$this->random_util->create_random_number(6);
        if ($key_otp < 100000) {
            $key_otp = 100000 + (abs($key_otp) % 900000);
        }
        $this->web_user_phone_model->update_key_otp_by_login_and_phone($current_id, $pending['web_user_phone'], $key_otp);
        $this->session->set_flashdata('bnyregister_otp_error', 'ส่ง OTP ใหม่แล้ว (ทดสอบ)');
        redirect(base_url('social_login/bnyregister_otp'), 'refresh');
    }

    private function get_current_login_row()
    {
        $id = $this->session->userdata(SESSION_PREFIX . 'web_user_login_id');
        if (empty($id)) {
            $id_cookie = get_cookie(COOKIE_PREFIX . 'web_user_login_id');
            if (!empty($id_cookie)) {
                $id = $this->encryption_util->decrypt_ssl($id_cookie);
            }
        }

        if (empty($id)) {
            return null;
        }

        return $this->web_user_login_model->select_by_id((int)$id);
    }

    private function set_social_session_cookie($row)
    {
        if (empty($row)) {
            return;
        }
        $keys = array(
            'web_user_login_id',
            'facebook_id','facebook_name','facebook_email','facebook_phone',
            'google_id','google_name','google_email','google_phone',
            'web_user_name','web_user_phone'
        );

        foreach ($keys as $k) {
            $v = isset($row[$k]) && $row[$k] !== null ? (string)$row[$k] : '';
            $this->session->set_userdata(SESSION_PREFIX.$k, $v);
            $this->input->set_cookie(array(
                'name' => COOKIE_PREFIX.$k,
                'value' => $this->encryption_util->encrypt_ssl($v),
                'expire' => 31536000,
                'path' => '/',
                'secure' => false
            ));
        }
    }

    private function ensure_web_user_phone_record($web_user_login_id, $web_user_phone)
    {
        if (empty($web_user_login_id) || empty($web_user_phone)) {
            return;
        }

        $exists = $this->web_user_phone_model->get_by_login_and_phone($web_user_login_id, $web_user_phone);
        if (!empty($exists)) {
            return;
        }

        $this->web_user_phone_model->insert(array(
            'web_user_login_id' => (int)$web_user_login_id,
            'web_user_phone' => $web_user_phone,
            'is_verify' => 1,
            'key_otp' => 0,
            'cdate' => DATE_TIME_NOW,
        ));
    }

    private function get_pending_register_data()
    {
        $name = (string)$this->session->userdata(SESSION_PREFIX . 'pending_register_name');
        $phone = (string)$this->session->userdata(SESSION_PREFIX . 'pending_register_phone');

        if ($name === '') {
            $c = get_cookie(COOKIE_PREFIX . 'pending_register_name');
            if (!empty($c)) {
                $name = (string)$this->encryption_util->decrypt_ssl($c);
            }
        }
        if ($phone === '') {
            $c = get_cookie(COOKIE_PREFIX . 'pending_register_phone');
            if (!empty($c)) {
                $phone = (string)$this->encryption_util->decrypt_ssl($c);
            }
        }
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return array(
            'web_user_name' => trim($name),
            'web_user_phone' => trim($phone),
        );
    }

    private function clear_pending_register_data()
    {
        $this->session->unset_userdata(SESSION_PREFIX . 'pending_register_name');
        $this->session->unset_userdata(SESSION_PREFIX . 'pending_register_phone');
        delete_cookie(COOKIE_PREFIX . 'pending_register_name');
        delete_cookie(COOKIE_PREFIX . 'pending_register_phone');
    }
}
