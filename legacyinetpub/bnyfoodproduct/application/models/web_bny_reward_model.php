<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_bny_reward_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function insert($data)
    {
        $this->db->insert('web_bny_reward', $data);
        return $this->db->insert_id();
    }

    function select_by_period($period_start, $period_stop)
    {
        if (empty($period_start) || empty($period_stop)) {
            return null;
        }

        $this->db->select('*');
        $this->db->from('web_bny_reward');
        $this->db->where('web_bny_reward_period_start', $period_start);
        $this->db->where('web_bny_reward_period_stop', $period_stop);
        $this->db->order_by('web_bny_reward_id', 'DESC');
        $query = $this->db->get();
        return $query->row_array();
    }

    function select_latest()
    {
        $this->db->select('*');
        $this->db->from('web_bny_reward');
        $this->db->order_by('web_bny_reward_period_stop', 'DESC');
        $this->db->order_by('web_bny_reward_id', 'DESC');
        $query = $this->db->get();
        return $query->row_array();
    }

    function select_all_latest_first()
    {
        $this->db->select('*');
        $this->db->from('web_bny_reward');
        $this->db->order_by('web_bny_reward_period_stop', 'DESC');
        $this->db->order_by('web_bny_reward_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function select_by_id($reward_id)
    {
        $reward_id = (int) $reward_id;
        if ($reward_id <= 0) {
            return null;
        }

        $this->db->select('*');
        $this->db->from('web_bny_reward');
        $this->db->where('web_bny_reward_id', $reward_id);
        $query = $this->db->get();
        return $query->row_array();
    }
}
