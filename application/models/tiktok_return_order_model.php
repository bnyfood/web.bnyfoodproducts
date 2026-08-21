<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class tiktok_return_order_model extends CI_Model
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
		$q = $this->db->query("SELECT OBJECT_ID('dbo.tiktok_return_order') AS oid");
		$row = $q ? $q->row_array() : null;
		if (empty($row['oid'])) {
			$this->db->query("
				CREATE TABLE dbo.tiktok_return_order (
					tiktok_return_order_id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					return_id VARCHAR(80) NULL,
					order_id VARCHAR(80) NULL,
					record_type VARCHAR(20) NULL,
					status VARCHAR(80) NULL,
					refund_amount FLOAT NULL,
					reason NVARCHAR(MAX) NULL,
					start_time DATETIME NULL,
					end_time DATETIME NULL,
					is_active INT NULL CONSTRAINT DF_tiktok_return_order_is_active DEFAULT (0)
				)
			");
			$this->db->query("CREATE INDEX IX_tiktok_return_order_rid ON dbo.tiktok_return_order (return_id, record_type)");
			$this->db->query("CREATE INDEX IX_tiktok_return_order_oid ON dbo.tiktok_return_order (order_id)");
		}
		$this->db->db_debug = $debug;
	}

	function insert($data)
	{
		$this->db->insert('tiktok_return_order', $data);
		return $this->db->insert_id();
	}

	function update($data, $id)
	{
		$this->db->where('tiktok_return_order_id', $id);
		$this->db->update('tiktok_return_order', $data);
	}

	function select_by_return_id($return_id, $record_type)
	{
		$this->db->select('*');
		$this->db->from('tiktok_return_order');
		$this->db->where('return_id', $return_id);
		$this->db->where('record_type', $record_type);
		$query = $this->db->get();
		return $query->row_array();
	}

	function select_by_status_list($statuses)
	{
		if (empty($statuses)) {
			return array();
		}
		$this->db->select('*');
		$this->db->from('tiktok_return_order');
		$this->db->where_in('status', $statuses);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_all_recent($limit = 500)
	{
		$this->db->select('*');
		$this->db->from('tiktok_return_order');
		$this->db->order_by('tiktok_return_order_id', 'DESC');
		$this->db->limit((int)$limit);
		$query = $this->db->get();
		return $query->result_array();
	}

	function select_inactive($limit = 500)
	{
		$this->db->select('*');
		$this->db->from('tiktok_return_order');
		$this->db->where('is_active', 0);
		$this->db->order_by('tiktok_return_order_id', 'ASC');
		$this->db->limit((int)$limit);
		$query = $this->db->get();
		return $query->result_array();
	}
}
