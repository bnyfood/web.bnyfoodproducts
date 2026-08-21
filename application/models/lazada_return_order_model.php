<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class lazada_return_order_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->ensure_table();
	}

	function ensure_table()
	{
		$debug = $this->db->db_debug;
		$this->db->db_debug = false;
		$q = $this->db->query("SELECT OBJECT_ID('dbo.lazada_return_order') AS oid");
		$row = $q ? $q->row_array() : null;
		if (empty($row['oid'])) {
			$this->db->query("
				CREATE TABLE dbo.lazada_return_order (
					lazada_return_order_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					reverse_order_id VARCHAR(50) NULL,
					reverse_order_line_id VARCHAR(50) NULL,
					order_number VARCHAR(50) NULL,
					request_type VARCHAR(50) NULL,
					reverse_status VARCHAR(80) NULL,
					refund_amount FLOAT NULL,
					reason NVARCHAR(MAX) NULL,
					start_time DATETIME NULL,
					end_time DATETIME NULL,
					is_active INT NULL CONSTRAINT DF_lazada_return_order_is_active DEFAULT (0)
				)
			");
			$this->db->query("CREATE INDEX IX_lazada_return_order_rev ON dbo.lazada_return_order (reverse_order_id, reverse_order_line_id)");
			$this->db->query("CREATE INDEX IX_lazada_return_order_sn ON dbo.lazada_return_order (order_number)");
		}
		$this->db->db_debug = $debug;
	}

	function insert($data)
	{
		$this->db->insert('lazada_return_order', $data);
		return $this->db->insert_id();
	}

	function update($data, $id)
	{
		$this->db->where('lazada_return_order_id', $id);
		$this->db->update('lazada_return_order', $data);
	}

	function select_by_reverse_line($reverse_order_id, $reverse_order_line_id)
	{
		$this->db->select('*');
		$this->db->from('lazada_return_order');
		$this->db->where('reverse_order_id', $reverse_order_id);
		if ($reverse_order_line_id !== '') {
			$this->db->where('reverse_order_line_id', $reverse_order_line_id);
		}
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_status_list($statuses)
	{
		if (empty($statuses)) {
			return array();
		}
		$this->db->select('*');
		$this->db->from('lazada_return_order');
		$this->db->where_in('reverse_status', $statuses);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_all_recent($limit = 500)
	{
		$this->db->select('*');
		$this->db->from('lazada_return_order');
		$this->db->order_by('lazada_return_order_id', 'DESC');
		$this->db->limit((int)$limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_inactive($limit = 500)
	{
		$this->db->select('*');
		$this->db->from('lazada_return_order');
		$this->db->where('is_active', 0);
		$this->db->order_by('lazada_return_order_id', 'ASC');
		$this->db->limit((int)$limit);
		$query = $this->db->get();
		return $query->result_array();
	}
}
