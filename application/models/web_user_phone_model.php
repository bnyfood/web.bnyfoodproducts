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

    public function set_verified($web_user_login_id, $phone)
    {
        $this->db->where('web_user_login_id', $web_user_login_id);
        $this->db->where('web_user_phone', $phone);
        $this->db->update('web_user_phone', array('is_verify' => 1));
    }
}
