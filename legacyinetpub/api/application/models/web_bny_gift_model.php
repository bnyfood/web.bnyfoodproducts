<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_bny_gift_model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	function clear_all_now()
	{
		$this->db->set('web_bny_gift_now', 0);
		$this->db->update('web_bny_gift');
		return $this->db->affected_rows();
	}

	function insert($data)
	{
		if (!empty($data['web_bny_gift_now']) && (int) $data['web_bny_gift_now'] === 1) {
			$this->clear_all_now();
		} else {
			$data['web_bny_gift_now'] = 0;
		}

		$this->db->insert('web_bny_gift', $data);
		return $this->db->insert_id();
	}

	function update($data, $id)
	{
		if (!empty($data['web_bny_gift_now']) && (int) $data['web_bny_gift_now'] === 1) {
			$this->clear_all_now();
		} else {
			$data['web_bny_gift_now'] = 0;
		}

		$this->db->where('web_bny_gift_id', $id);
		$this->db->update('web_bny_gift', $data);
		return $this->db->affected_rows();
	}

	function select_by_id($id)
	{
		$this->db->select('*');
		$this->db->from('web_bny_gift');
		$this->db->where('web_bny_gift_id', $id);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_search($bny_gift_search, $per_page, $offset, $sortby, $sorttype)
	{
		$this->db->select('*');
		$this->db->from('web_bny_gift');

		if ($bny_gift_search != "") {
			$this->db->like('web_bny_gift_detail', $bny_gift_search);
		}
		if ($sortby != "") {
			$this->db->order_by($sortby, $sorttype);
		} else {
			$this->db->order_by('web_bny_gift_id', 'desc');
		}
		if ($per_page != "") {
			$this->db->limit($per_page, $offset);
		}

		$query = $this->db->get();
		return $query->result_array();
	}

	function select_lasted()
	{
		$this->db->select('*');
		$this->db->from('web_bny_gift');
		$this->db->where('web_bny_gift_now', 1);
		$this->db->order_by('web_bny_gift_id', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row_array();
		if (!empty($row)) {
			return $row;
		}

		$this->db->select('*');
		$this->db->from('web_bny_gift');
		$this->db->order_by('web_bny_gift_id', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row_array();
	}
}
