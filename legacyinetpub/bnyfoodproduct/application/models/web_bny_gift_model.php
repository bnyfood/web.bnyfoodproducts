<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_bny_gift_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function select_current()
    {
        $sql = "SELECT TOP 1 web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
            FROM dbo.web_bny_gift
            WHERE web_bny_gift_now = 1
            ORDER BY web_bny_gift_id DESC";
        return $this->safe_row_array($this->db->query($sql));
    }

    /**
     * รางวัล ณ เวลา web_bny_reward.cdate (raw SQL + temporal)
     */
    function select_for_reward_id($web_bny_reward_id)
    {
        $web_bny_reward_id = (int) $web_bny_reward_id;
        if ($web_bny_reward_id <= 0) {
            return null;
        }

        $sql = "SELECT TOP 1
                g.web_bny_gift_id,
                g.web_bny_gift_pic,
                g.web_bny_gift_detail,
                g.web_bny_gift_now
            FROM dbo.web_bny_reward r
            CROSS APPLY (
                SELECT gf.web_bny_gift_id, gf.web_bny_gift_pic, gf.web_bny_gift_detail, gf.web_bny_gift_now
                FROM dbo.web_bny_gift AS gf
                FOR SYSTEM_TIME AS OF r.cdate
                WHERE gf.web_bny_gift_id = r.web_bny_gift_id
            ) AS g
            WHERE r.web_bny_reward_id = ?";
        $row = $this->safe_row_array($this->db->query($sql, array($web_bny_reward_id)));
        if (!empty($row)) {
            return $row;
        }

        return $this->select_for_reward_id_from_history($web_bny_reward_id);
    }

    function select_by_id_at_time($web_bny_gift_id, $as_of = null)
    {
        $web_bny_gift_id = (int) $web_bny_gift_id;
        if ($web_bny_gift_id <= 0) {
            return null;
        }

        $as_of_sql = $this->normalize_as_of_datetime($as_of);
        if ($as_of_sql === null) {
            $sql = "SELECT web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
                FROM dbo.web_bny_gift
                WHERE web_bny_gift_id = ?";
            return $this->safe_row_array($this->db->query($sql, array($web_bny_gift_id)));
        }

        $sql = "SELECT web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
            FROM dbo.web_bny_gift
            FOR SYSTEM_TIME AS OF CAST(? AS datetime2(7))
            WHERE web_bny_gift_id = ?";
        $row = $this->safe_row_array($this->db->query($sql, array($as_of_sql, $web_bny_gift_id)));
        if (!empty($row)) {
            return $row;
        }

        $sql_hist = "SELECT TOP 1 web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
            FROM dbo.web_bny_gift_history
            WHERE web_bny_gift_id = ?
              AND ValidFrom <= CAST(? AS datetime2(7))
            ORDER BY ValidFrom DESC";
        return $this->safe_row_array($this->db->query($sql_hist, array($web_bny_gift_id, $as_of_sql)));
    }

    function select_active_gift_at_time($as_of)
    {
        $as_of_sql = $this->normalize_as_of_datetime($as_of);
        if ($as_of_sql === null) {
            return null;
        }

        $sql = "SELECT TOP 1 web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
            FROM dbo.web_bny_gift
            FOR SYSTEM_TIME AS OF CAST(? AS datetime2(7))
            WHERE web_bny_gift_now = 1
            ORDER BY web_bny_gift_id DESC";
        $row = $this->safe_row_array($this->db->query($sql, array($as_of_sql)));
        if (!empty($row)) {
            return $row;
        }

        $sql_hist = "SELECT TOP 1 web_bny_gift_id, web_bny_gift_pic, web_bny_gift_detail, web_bny_gift_now
            FROM dbo.web_bny_gift_history
            WHERE web_bny_gift_now = 1
              AND ValidFrom <= CAST(? AS datetime2(7))
            ORDER BY ValidFrom DESC";
        return $this->safe_row_array($this->db->query($sql_hist, array($as_of_sql)));
    }

    private function select_for_reward_id_from_history($web_bny_reward_id)
    {
        $sql = "SELECT TOP 1
                g.web_bny_gift_id,
                g.web_bny_gift_pic,
                g.web_bny_gift_detail,
                g.web_bny_gift_now
            FROM dbo.web_bny_reward r
            INNER JOIN dbo.web_bny_gift_history g
              ON g.web_bny_gift_id = r.web_bny_gift_id
             AND g.ValidFrom <= CAST(r.cdate AS datetime2(7))
             AND g.ValidTo > CAST(r.cdate AS datetime2(7))
            WHERE r.web_bny_reward_id = ?";
        return $this->safe_row_array($this->db->query($sql, array($web_bny_reward_id)));
    }

    private function safe_row_array($query)
    {
        if ($query === false || !is_object($query)) {
            return null;
        }
        $row = $query->row_array();
        return !empty($row) ? $row : null;
    }

    private function normalize_as_of_datetime($as_of)
    {
        $as_of = trim((string) $as_of);
        if ($as_of === '') {
            return null;
        }
        $ts = strtotime($as_of);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $ts);
    }
}
