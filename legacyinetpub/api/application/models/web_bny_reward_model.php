<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_bny_reward_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	private function _format_lucky_row($row)
	{
		$name = isset($row['web_user_name']) ? trim($row['web_user_name']) : '';
		if ($name === '') {
			if (!empty($row['facebook_name'])) {
				$name = trim($row['facebook_name']);
			} elseif (!empty($row['google_name'])) {
				$name = trim($row['google_name']);
			}
		}

		$phone = isset($row['web_user_phone']) ? trim($row['web_user_phone']) : '';
		if ($phone === '' && !empty($row['reward_phone'])) {
			$phone = trim($row['reward_phone']);
		}
		if ($phone === '') {
			if (!empty($row['facebook_phone'])) {
				$phone = trim($row['facebook_phone']);
			} elseif (!empty($row['google_phone'])) {
				$phone = trim($row['google_phone']);
			}
		}

		$row['winner_name'] = $name;
		$row['winner_phone'] = $phone;
		$row['web_bny_gift_send'] = isset($row['web_bny_gift_send']) ? (int) $row['web_bny_gift_send'] : 0;

		return $row;
	}

	function select_lucky_list($lucky_search, $per_page, $offset, $sortby, $sorttype)
	{
		$this->db->select('r.web_bny_reward_id, r.web_bny_gift_send, r.web_user_phone AS reward_phone, r.cdate,
			g.web_bny_gift_id, g.web_bny_gift_pic, g.web_bny_gift_detail,
			u.web_user_name, u.web_user_phone, u.facebook_name, u.google_name, u.facebook_phone, u.google_phone', false);
		$this->db->from('web_bny_reward r');
		$this->db->join('web_user_login u', 'u.web_user_login_id = r.web_user_login_id', 'left');
		$this->db->join('web_bny_gift g', 'g.web_bny_gift_id = r.web_bny_gift_id', 'left');

		if ($lucky_search != '') {
			$this->db->group_start();
			$this->db->like('g.web_bny_gift_detail', $lucky_search);
			$this->db->or_like('u.web_user_name', $lucky_search);
			$this->db->or_like('u.web_user_phone', $lucky_search);
			$this->db->or_like('r.web_user_phone', $lucky_search);
			$this->db->or_like('u.facebook_name', $lucky_search);
			$this->db->or_like('u.google_name', $lucky_search);
			$this->db->group_end();
		}

		if ($sortby != '') {
			$this->db->order_by($sortby, $sorttype);
		} else {
			$this->db->order_by('r.web_bny_reward_id', 'desc');
		}

		if ($per_page != '') {
			$this->db->limit($per_page, $offset);
		}

		$query = $this->db->get();
		$rows = $query->result_array();
		$out = array();
		foreach ($rows as $row) {
			$out[] = $this->_format_lucky_row($row);
		}
		return $out;
	}

	function update_gift_send($reward_id, $gift_send)
	{
		$reward_id = (int) $reward_id;
		if ($reward_id <= 0) {
			return 0;
		}

		$this->db->where('web_bny_reward_id', $reward_id);
		$this->db->update('web_bny_reward', array(
			'web_bny_gift_send' => ((int) $gift_send === 1) ? 1 : 0
		));
		return 1;
	}
}
