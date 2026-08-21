<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_user_phone_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_by_login_and_phone($web_user_login_id, $phone)
    {
        if ($web_user_login_id === null || $web_user_login_id === '' || $phone === null || $phone === '') {
            return null;
        }
        $this->db->select('*');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('web_user_phone', $phone);
        $q = $this->db->get();
        return $q->row_array();
    }

    public function get_any_by_login($web_user_login_id)
    {
        if ($web_user_login_id === null || $web_user_login_id === '') {
            return null;
        }
        $this->db->select('*');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->order_by('web_user_phone_id', 'DESC');
        $q = $this->db->get();
        return $q->row_array();
    }

    public function insert($data)
    {
        $this->db->insert('web_user_phone', $data);
        return $this->db->insert_id();
    }

    public function update_key_otp_by_login_and_phone($web_user_login_id, $phone, $key_otp)
    {
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('web_user_phone', $phone);
        $this->db->update('web_user_phone', array('key_otp' => (int) $key_otp));
    }

    public function set_verified($web_user_login_id, $phone, $key_otp = null)
    {
        $this->db->where('web_user_login_id', (int) $web_user_login_id);
        $this->db->where('web_user_phone', $phone);
        if ($key_otp !== null && $key_otp !== '') {
            $this->db->where('key_otp', (int) $key_otp);
        }
        $this->db->update('web_user_phone', array('is_verify' => 1));
    }

    public function get_latest_verified_by_phone($phone)
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $this->db->select('*');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_phone', $phone);
        $this->db->where('is_verify', 1);
        $this->db->order_by('web_user_phone_id', 'DESC');
        $q = $this->db->get();
        return $q->row_array();
    }

    public function get_latest_by_phone($phone)
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $this->db->select('*');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_phone', $phone);
        $this->db->order_by('web_user_phone_id', 'DESC');
        $q = $this->db->get();
        return $q->row_array();
    }

    /** เบอร์ที่ยืนยันแล้วของ login นี้ (ใช้เช็กว่าเป็นผู้ชนะหรือไม่เมื่อมีหลายเบอร์ต่อคน) */
    public function get_verified_phones_by_login($web_user_login_id)
    {
        $web_user_login_id = (int) $web_user_login_id;
        if ($web_user_login_id <= 0) {
            return array();
        }

        $this->db->select('web_user_phone');
        $this->db->from('web_user_phone');
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('is_verify', 1);
        $q = $this->db->get();
        return $q->result_array();
    }

    public function get_latest_by_phone_flexible($phones)
    {
        if (empty($phones) || !is_array($phones)) {
            return null;
        }

        $escaped = array();
        foreach ($phones as $phone) {
            if ($phone === null || $phone === '') {
                continue;
            }
            $escaped[] = $this->db->escape((string) $phone);
        }

        if (empty($escaped)) {
            return null;
        }

        $expr = $this->normalized_phone_sql('web_user_phone');

        $this->db->select('*');
        $this->db->from('web_user_phone');
        $this->db->where($expr . ' IN (' . implode(',', $escaped) . ')', null, false);
        $this->db->order_by('web_user_phone_id', 'DESC');
        $q = $this->db->get();
        return $q->row_array();
    }

    private function normalized_phone_sql($column)
    {
        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CAST(ISNULL(" . $column . ", '') AS VARCHAR(50)), '-', ''), ' ', ''), '+', ''), '(', ''), ')', '')";
    }
}
