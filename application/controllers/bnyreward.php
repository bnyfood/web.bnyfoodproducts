<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bnyreward extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('util/View_util');
        $this->load->library('util/random_util');
        $this->load->library('util/encryption_util');
        $this->load->library('businesslogic/curl_bl');
        $this->load->helper('cookie');
        $this->load->model('web_user_login_model');
        $this->load->model('web_user_phone_model');
        $this->load->model('biggrill_data_model');
        $this->load->model('web_bny_reward_model');
    }

    public function bny_luckydraw()
    {
        $login_id = $this->get_login_id_from_session_or_cookie();
        if ($login_id === null) {
            redirect(base_url('social_login'), 'refresh');
            return;
        }

        $profile = $this->get_customer_profile_for_display();
        if (trim((string) $profile['web_user_phone']) !== '') {
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

    public function bny_checkprize()
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
            'title' => 'ตรวจผลรางวัล',
        );
        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'back_url' => base_url('bnyreward/bny_luckyresult'),
        );
        $this->view_util->social_login('bnyreward/bny_checkprize', $data, null, null, $arr_input);
    }

    /**
     * แสดงผลรางวัลจาก period ล่าสุดใน web_bny_reward
     */
    public function bny_result()
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
            'title' => 'ผลรางวัล',
        );
        $latest_reward = $this->web_bny_reward_model->select_latest();
        $winner_phone_raw = !empty($latest_reward['web_user_phone']) ? $latest_reward['web_user_phone'] : '';
        $current_reward_id = !empty($latest_reward['web_bny_reward_id']) ? (int) $latest_reward['web_bny_reward_id'] : 0;
        $is_current_user_winner = $this->reward_winner_is_current_user(
            (int) $login_id,
            $profile['web_user_phone'],
            $winner_phone_raw
        );
        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'customer_name' => $profile['web_user_name'],
            'customer_phone' => $profile['web_user_phone'],
            'week_range_label' => $this->format_reward_period_label($latest_reward),
            'prize_winner_phone_display' => $this->format_prize_winner_display_for_user(
                (int) $login_id,
                $profile['web_user_phone'],
                $winner_phone_raw
            ),
            'prize_winner_show_celebration' => $is_current_user_winner,
            'bny_result_reward_id' => $current_reward_id,
            'bny_result_login_id' => (int) $login_id,
        );
        $this->view_util->social_login('bnyreward/bny_result', $data, null, null, $arr_input);
    }

    public function bny_archive()
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
            'title' => 'ตรวจรางวัลย้อนหลัง',
        );
        $arr_js = array(
            'bootstrap_datepicker' => base_url() . 'global/vendor/bootstrap-datepicker/bootstrap-datepicker.js',
            'bootstrap_datepicker_th' => base_url() . 'global/vendor/bootstrap-datepicker/bootstrap-datepicker.th.min.js',
            'bny_archive' => base_url() . 'resources/js/bnyreward/bny_archive.js',
        );

        $selected_reward_id = (int) $this->input->get('reward_id', true);
        $selected_date = trim((string) $this->input->get('selected_date', true));
        $reward_rows = $this->web_bny_reward_model->select_all_latest_first();
        $selected_reward = null;

        if ($selected_reward_id > 0) {
            $selected_reward = $this->web_bny_reward_model->select_by_id($selected_reward_id);
        } else if ($selected_date !== '') {
            $selected_reward = $this->find_reward_by_selected_date($reward_rows, $selected_date);
            if (!empty($selected_reward['web_bny_reward_id'])) {
                $selected_reward_id = (int) $selected_reward['web_bny_reward_id'];
            }
        }

        $period_picker_items = array();
        foreach ($reward_rows as $row) {
            $reward_id = isset($row['web_bny_reward_id']) ? (int) $row['web_bny_reward_id'] : 0;
            if ($reward_id <= 0) {
                continue;
            }

            $period_picker_items[] = array(
                'reward_id' => $reward_id,
                'start_date' => substr((string) $row['web_bny_reward_period_start'], 0, 10),
                'end_date' => substr((string) $row['web_bny_reward_period_stop'], 0, 10),
                'period_label' => $this->format_reward_period_date_label($row),
            );
        }

        $selected_date_display = '';
        if ($selected_date !== '') {
            $selected_date_display = $this->format_datepicker_display($selected_date);
        } else if (!empty($selected_reward['web_bny_reward_period_start'])) {
            $selected_date_display = $this->format_datepicker_display(substr((string) $selected_reward['web_bny_reward_period_start'], 0, 10));
        } else if (!empty($period_picker_items[0]['start_date'])) {
            $selected_date_display = $this->format_datepicker_display($period_picker_items[0]['start_date']);
        }

        $archive_results = array();
        if (!empty($selected_reward)) {
            $winner_raw = !empty($selected_reward['web_user_phone']) ? $selected_reward['web_user_phone'] : '';
            $archive_results[] = array(
                'period_label' => $this->format_reward_period_date_label($selected_reward),
                'winner_phone' => $this->format_prize_winner_display_for_user(
                    (int) $login_id,
                    $profile['web_user_phone'],
                    $winner_raw
                ),
            );
        }

        $data = array(
            'logo_url' => base_url('resources/images/logo_700.png'),
            'customer_name' => $profile['web_user_name'],
            'customer_phone' => $profile['web_user_phone'],
            'period_picker_items' => $period_picker_items,
            'selected_reward_id' => $selected_reward_id,
            'selected_date_display' => $selected_date_display,
            'archive_results' => $archive_results,
            'search_performed' => ($selected_reward_id > 0 || $selected_date !== ''),
            'back_url' => base_url('bnyreward/bny_result'),
        );
        $this->view_util->social_login('bnyreward/bny_archive', $data, null, $arr_js, $arr_input);
    }

    /**
     * สุ่มผู้โชคดี 1 คนจากรอบสัปดาห์ที่ผ่านมา
     * เงื่อนไขสิทธิ์: SUM(price) GROUP BY cus_phone แล้ว FLOOR(total_price / 500)
     * เรียกใช้งานจาก job ทุกวันจันทร์ 10:00 และจะไม่ insert ซ้ำหาก period นี้มีข้อมูลแล้ว
     */
    public function reward_random()
    {
        $period = $this->get_previous_biggrill_period_range();
        $period_start = $period['start']->format('Y-m-d H:i:s');
        $period_stop = $period['end']->format('Y-m-d H:i:s');

        $existing_reward = $this->web_bny_reward_model->select_by_period($period_start, $period_stop);
        if (!empty($existing_reward)) {
            $this->json_out(array(
                'status' => true,
                'inserted' => false,
                'message' => 'รอบนี้มีการออกรางวัลแล้ว',
                'period_start' => $period_start,
                'period_stop' => $period_stop,
                'reward_id' => isset($existing_reward['web_bny_reward_id']) ? (int) $existing_reward['web_bny_reward_id'] : null,
                'winner_phone' => isset($existing_reward['web_user_phone']) ? $existing_reward['web_user_phone'] : null,
            ));
            return;
        }

        $candidates = $this->biggrill_data_model->select_reward_candidates_by_period($period_start, $period_stop);
        $ticket_pool = $this->build_reward_ticket_pool($candidates);
        if (empty($ticket_pool)) {
            $this->json_out(array(
                'status' => true,
                'inserted' => false,
                'message' => 'ไม่พบผู้มีสิทธิ์สำหรับรอบนี้',
                'period_start' => $period_start,
                'period_stop' => $period_stop,
                'candidate_count' => 0,
                'ticket_count' => 0,
            ));
            return;
        }

        $winner_phone = $ticket_pool[array_rand($ticket_pool)];
        $winner_login_id = $this->resolve_reward_login_id_by_phone($winner_phone);

        $insert_data = array(
            'web_user_login_id' => $winner_login_id !== null ? (int) $winner_login_id : null,
            'web_bny_reward_period_start' => $period_start,
            'web_bny_reward_period_stop' => $period_stop,
            'web_user_phone' => $winner_phone,
            'cdate' => DATE_TIME_NOW,
        );

        $stored_reward = null;
        $inserted = false;

        $this->db->trans_start();
        $existing_in_tx = $this->web_bny_reward_model->select_by_period($period_start, $period_stop);
        if (empty($existing_in_tx)) {
            $this->web_bny_reward_model->insert($insert_data);
            $inserted = true;
            $stored_reward = $this->web_bny_reward_model->select_by_period($period_start, $period_stop);
        } else {
            $stored_reward = $existing_in_tx;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->json_out(array(
                'status' => false,
                'inserted' => false,
                'message' => 'บันทึกผลรางวัลไม่สำเร็จ',
                'period_start' => $period_start,
                'period_stop' => $period_stop,
            ), 500);
            return;
        }

        if (empty($stored_reward)) {
            $this->json_out(array(
                'status' => false,
                'inserted' => false,
                'message' => 'ไม่สามารถอ่านข้อมูลผลรางวัลหลังบันทึกได้',
                'period_start' => $period_start,
                'period_stop' => $period_stop,
            ), 500);
            return;
        }

        $this->json_out(array(
            'status' => true,
            'inserted' => $inserted,
            'message' => $inserted ? 'สุ่มผู้โชคดีและบันทึกผลรางวัลแล้ว' : 'รอบนี้มีการออกรางวัลแล้ว',
            'period_start' => $period_start,
            'period_stop' => $period_stop,
            'candidate_count' => count($candidates),
            'ticket_count' => count($ticket_pool),
            'reward_id' => isset($stored_reward['web_bny_reward_id']) ? (int) $stored_reward['web_bny_reward_id'] : null,
            'winner_phone' => isset($stored_reward['web_user_phone']) ? $stored_reward['web_user_phone'] : $winner_phone,
            'winner_login_id' => isset($stored_reward['web_user_login_id']) ? $stored_reward['web_user_login_id'] : $winner_login_id,
        ));
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

        if (!$this->curl_bl->send_otp_sms($new_phone, (string) $key_otp)) {
            $this->session->set_flashdata('bny_changephoneotp_error', 'ส่ง SMS ไม่สำเร็จ กรุณากดส่ง OTP อีกครั้งหลังเข้าหน้ายืนยัน');
        }

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
        if (!$this->curl_bl->send_otp_sms($new_phone, (string) $key_otp)) {
            $this->session->set_flashdata('bny_changephoneotp_error', 'ส่ง SMS ไม่สำเร็จ กรุณากดส่ง OTP อีกครั้ง');
        }
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
        $web_user_login_id = $this->get_login_id_from_session_or_cookie();
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

        $sms_ok = $this->curl_bl->send_otp_sms($phone, (string) $key_otp);
        if (!$sms_ok) {
            log_message('error', 'submit_phone: SMS send failed');
        }

        $out['status'] = true;
        $out['message'] = $sms_ok
            ? 'บันทึกเบอร์แล้ว กรุณากรอกรหัส OTP 6 หลักที่ส่งทาง SMS'
            : 'บันทึกเบอร์แล้ว แต่ส่ง SMS ไม่สำเร็จ กรุณากดส่ง OTP อีกครั้ง';
        $this->json_out($out);
    }

    public function verify_otp()
    {
        $out = $this->json_init();
        $web_user_login_id = $this->get_login_id_from_session_or_cookie();
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

        $this->web_user_phone_model->set_verified($web_user_login_id, $phone, $key_in);
        $this->web_user_login_model->update_by_id($web_user_login_id, array('web_user_phone' => $phone));
        $this->session->set_userdata(SESSION_PREFIX . 'web_user_login_id', (string) $web_user_login_id);
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
        $web_user_login_id = $this->get_login_id_from_session_or_cookie();
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

        $sms_ok = $this->curl_bl->send_otp_sms($phone, (string) $key_otp);
        if (!$sms_ok) {
            log_message('error', 'resend_otp: SMS send failed');
        }

        $out['status'] = true;
        $out['message'] = $sms_ok ? 'ส่ง OTP ใหม่ทาง SMS แล้ว' : 'ส่ง SMS ไม่สำเร็จ กรุณาลองอีกครั้ง';
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
        $id_cookie = get_cookie(COOKIE_PREFIX . 'web_user_login_id');
        if (!empty($id_cookie)) {
            $dec = $this->encryption_util->decrypt_ssl($id_cookie);
            if ($dec !== null && $dec !== '') {
                $from_cookie = (int) $dec;
                if ($from_cookie > 0) {
                    $this->session->set_userdata(SESSION_PREFIX . 'web_user_login_id', (string) $from_cookie);
                    return $from_cookie;
                }
            }
        }

        return $this->get_login_id();
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
        $range = $this->get_current_biggrill_period_range();
        return $range['start']->format('d/m/Y H:i') . ' - ' . $range['end']->format('d/m/Y H:i');
    }

    /** รอบแต้มสัปดาห์ที่ผ่านมา (ก่อนรอบปัจจุบัน 1 รอบ) — ใช้หน้าผลรางวัล */
    private function get_previous_week_range_label()
    {
        $range = $this->get_previous_biggrill_period_range();
        return $range['start']->format('d/m/Y H:i') . ' - ' . $range['end']->format('d/m/Y H:i');
    }

    /**
     * รอบที่แล้ว: เลื่อนจาก get_current_biggrill_period_range ย้อนหลัง 7 วัน (จันทร์ 10:00–จันทร์ 09:59 เช่นเดิม)
     */
    private function get_previous_biggrill_period_range()
    {
        $current = $this->get_current_biggrill_period_range();
        $start = clone $current['start'];
        $start->modify('-7 days');
        $end = clone $current['end'];
        $end->modify('-7 days');
        return array('start' => $start, 'end' => $end);
    }

    private function format_reward_period_label($reward_row)
    {
        if (empty($reward_row['web_bny_reward_period_start']) || empty($reward_row['web_bny_reward_period_stop'])) {
            return $this->get_previous_week_range_label();
        }

        try {
            $tz = new DateTimeZone('Asia/Bangkok');
            $start = new DateTime($reward_row['web_bny_reward_period_start'], $tz);
            $end = new DateTime($reward_row['web_bny_reward_period_stop'], $tz);
            return $start->format('d/m/Y H:i') . ' - ' . $end->format('d/m/Y H:i');
        } catch (Exception $e) {
            return $this->get_previous_week_range_label();
        }
    }

    private function format_reward_period_date_label($reward_row)
    {
        if (empty($reward_row['web_bny_reward_period_start']) || empty($reward_row['web_bny_reward_period_stop'])) {
            $range = $this->get_previous_biggrill_period_range();
            return $range['start']->format('d/m/Y') . ' - ' . $range['end']->format('d/m/Y');
        }

        try {
            $tz = new DateTimeZone('Asia/Bangkok');
            $start = new DateTime($reward_row['web_bny_reward_period_start'], $tz);
            $end = new DateTime($reward_row['web_bny_reward_period_stop'], $tz);
            return $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        } catch (Exception $e) {
            $range = $this->get_previous_biggrill_period_range();
            return $range['start']->format('d/m/Y') . ' - ' . $range['end']->format('d/m/Y');
        }
    }

    private function find_reward_by_selected_date($reward_rows, $selected_date)
    {
        $selected_date = $this->normalize_selected_date($selected_date);
        if ($selected_date === null || empty($reward_rows)) {
            return null;
        }

        foreach ($reward_rows as $row) {
            if (empty($row['web_bny_reward_period_start']) || empty($row['web_bny_reward_period_stop'])) {
                continue;
            }

            $start_date = substr((string) $row['web_bny_reward_period_start'], 0, 10);
            $end_date = substr((string) $row['web_bny_reward_period_stop'], 0, 10);
            if ($selected_date >= $start_date && $selected_date <= $end_date) {
                return $row;
            }
        }

        return null;
    }

    private function normalize_selected_date($selected_date)
    {
        $selected_date = trim((string) $selected_date);
        if ($selected_date === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected_date)) {
            return $selected_date;
        }

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $selected_date, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }

        return null;
    }

    private function format_datepicker_display($date_ymd)
    {
        $date_ymd = $this->normalize_selected_date($date_ymd);
        if ($date_ymd === null) {
            return '';
        }

        $parts = explode('-', $date_ymd);
        if (count($parts) !== 3) {
            return '';
        }

        return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    }

    private function build_reward_ticket_pool($candidates)
    {
        $ticket_pool = array();
        foreach ($candidates as $candidate) {
            $phone = $this->sanitize_phone(isset($candidate['cus_phone']) ? $candidate['cus_phone'] : null);
            $ticket_count = isset($candidate['ticket_count']) ? (int) $candidate['ticket_count'] : 0;
            if ($phone === null || $ticket_count <= 0) {
                continue;
            }

            for ($i = 0; $i < $ticket_count; $i++) {
                $ticket_pool[] = $phone;
            }
        }

        return $ticket_pool;
    }

    private function resolve_reward_login_id_by_phone($phone)
    {
        $phone = $this->sanitize_phone($phone);
        if ($phone === null) {
            return null;
        }

        $phone_variants = $this->get_phone_match_variants($phone);

        $phone_row = $this->web_user_phone_model->get_latest_verified_by_phone($phone);
        if (!empty($phone_row['web_user_login_id'])) {
            return (int) $phone_row['web_user_login_id'];
        }

        $phone_row = $this->web_user_phone_model->get_latest_by_phone($phone);
        if (!empty($phone_row['web_user_login_id'])) {
            return (int) $phone_row['web_user_login_id'];
        }

        $phone_row = $this->web_user_phone_model->get_latest_by_phone_flexible($phone_variants);
        if (!empty($phone_row['web_user_login_id'])) {
            return (int) $phone_row['web_user_login_id'];
        }

        $login_row = $this->web_user_login_model->select_by_phone_flexible($phone_variants);
        if (!empty($login_row['web_user_login_id'])) {
            return (int) $login_row['web_user_login_id'];
        }

        return null;
    }

    private function get_phone_match_variants($phone)
    {
        $phone = $this->sanitize_phone($phone);
        if ($phone === null) {
            return array();
        }

        $variants = array();
        $variants[$phone] = $phone;

        if (strpos($phone, '0') === 0 && strlen($phone) >= 10) {
            $without_zero = substr($phone, 1);
            if ($without_zero !== '') {
                $variants[$without_zero] = $without_zero;
                $variants['66' . $without_zero] = '66' . $without_zero;
            }
        }

        if (strpos($phone, '66') === 0 && strlen($phone) >= 11) {
            $local_phone = '0' . substr($phone, 2);
            if ($local_phone !== '0') {
                $variants[$local_phone] = $local_phone;
                $without_zero = substr($local_phone, 1);
                if ($without_zero !== '') {
                    $variants[$without_zero] = $without_zero;
                }
            }
        }

        if (strpos($phone, '0') !== 0 && strpos($phone, '66') !== 0 && strlen($phone) === 9) {
            $variants['0' . $phone] = '0' . $phone;
            $variants['66' . $phone] = '66' . $phone;
        }

        return array_values($variants);
    }

    /**
     * รอบแต้ม: จันทร์ 10:00 ถึง จันทร์ถัดไป 09:59:59 (ชนรอบกัน)
     */
    private function get_current_biggrill_period_range()
    {
        $tz = new DateTimeZone('Asia/Bangkok');
        $now = new DateTime('now', $tz);
        $n = (int) $now->format('N');
        $start = clone $now;
        $start->modify('-' . ($n - 1) . ' days');
        $start->setTime(10, 0, 0);
        if ($now < $start) {
            $start->modify('-7 days');
        }
        $end = clone $start;
        $end->modify('+7 days');
        $end->modify('-1 second');

        return array('start' => $start, 'end' => $end);
    }

    /**
     * ข้อความแสดงผู้ชนะ: ถ้าเป็นผู้ใช้ปัจจุบัน (รวมเบอร์อื่นที่ยืนยันแล้วของ account เดียวกัน) แสดงข้อความแทนเบอร์
     */
    private function format_prize_winner_display_for_user($login_id, $profile_phone, $winner_phone_raw)
    {
        if (trim((string) $winner_phone_raw) === '') {
            return '-';
        }
        if ($this->reward_winner_is_current_user((int) $login_id, $profile_phone, $winner_phone_raw)) {
            return 'คุณคือผู้ได้รับรางวัล';
        }

        return (string) $winner_phone_raw;
    }

    private function reward_winner_is_current_user($login_id, $profile_phone, $winner_phone_raw)
    {
        $key_set = $this->build_login_phone_norm_key_set($login_id, $profile_phone);
        if (empty($key_set)) {
            return false;
        }
        foreach ($this->get_phone_match_variants($winner_phone_raw) as $v) {
            $n = $this->sanitize_phone($v);
            if ($n !== null && isset($key_set[$n])) {
                return true;
            }
        }

        return false;
    }

    /** รวมเบอร์ทุกแบบที่ normalize แล้วของ login (โปรไฟล์ + web_user_login + web_user_phone ที่ verify) */
    private function build_login_phone_norm_key_set($login_id, $profile_phone)
    {
        $login_id = (int) $login_id;
        $candidates = array();
        if ($profile_phone !== null && trim((string) $profile_phone) !== '') {
            $candidates[] = $profile_phone;
        }
        if ($login_id > 0) {
            $login_row = $this->web_user_login_model->select_by_id($login_id);
            if (!empty($login_row['web_user_phone'])) {
                $candidates[] = $login_row['web_user_phone'];
            }
            foreach ($this->web_user_phone_model->get_verified_phones_by_login($login_id) as $r) {
                if (!empty($r['web_user_phone'])) {
                    $candidates[] = $r['web_user_phone'];
                }
            }
        }

        $keys = array();
        foreach ($candidates as $c) {
            foreach ($this->get_phone_match_variants($c) as $v) {
                $n = $this->sanitize_phone($v);
                if ($n !== null) {
                    $keys[$n] = true;
                }
            }
        }

        return $keys;
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
