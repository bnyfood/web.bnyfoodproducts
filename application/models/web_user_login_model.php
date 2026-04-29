<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_user_login_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function select_by_facebook_id($facebook_id)
    {
        $facebook_id = $this->db->escape_str((string)$facebook_id);
        $this->db->select('*');
        $this->db->from('web_user_login');
        // CAST to VARCHAR to prevent bigint overflow during comparison.
        $this->db->where("CAST(facebook_id AS VARCHAR(255)) = '".$facebook_id."'", null, false);
        $query = $this->db->get();
        return $query->row_array();
    }

    function select_by_google_id($google_id)
    {
        $google_id = $this->db->escape_str((string)$google_id);
        $this->db->select('*');
        $this->db->from('web_user_login');
        // CAST to VARCHAR to prevent bigint overflow during comparison.
        $this->db->where("CAST(google_id AS VARCHAR(255)) = '".$google_id."'", null, false);
        $query = $this->db->get();
        return $query->row_array();
    }

    function select_by_phone($phone)
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $this->db->select('*');
        $this->db->from('web_user_login');
        $this->db->group_start();
        $this->db->where('facebook_phone', $phone);
        $this->db->or_where('google_phone', $phone);
        $this->db->or_where('web_user_phone', $phone);
        $this->db->group_end();
        $this->db->order_by('web_user_login_id', 'DESC');
        $query = $this->db->get();
        return $query->row_array();
    }

    function insert($data)
    {
        $this->db->insert('web_user_login', $data);
        return $this->db->insert_id();
    }

    function update_by_id($id, $data)
    {
        $this->db->where('web_user_login_id', $id);
        $this->db->update('web_user_login', $data);
    }

    function select_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('web_user_login');
        $this->db->where('web_user_login_id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
}
