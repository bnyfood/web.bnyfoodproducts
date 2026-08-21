<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_authorize_signature_model extends CI_Model
{
	function get_active()
	{
		if (!$this->db->table_exists('web_authorize_signature')) {
			return null;
		}
		$this->db->from('web_authorize_signature');
		$this->db->order_by('web_authorize_signature_id', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row_array();
		return empty($row) ? null : $row;
	}

	function get_history()
	{
		if (!$this->db->table_exists('web_authorize_signature')) {
			return array();
		}
		$this->db->from('web_authorize_signature');
		$this->db->order_by('web_authorize_signature_id', 'desc');
		$query = $this->db->get();
		$rows = $query->result_array();
		return empty($rows) ? array() : $rows;
	}

	function save($file_name, $file_path)
	{
		$now = date('Y-m-d H:i:s');
		$data = array(
			'file_name' => $file_name,
			'file_path' => $file_path,
			'created_at' => $now,
			'updated_at' => $now
		);
		$this->db->insert('web_authorize_signature', $data);
		return $this->db->insert_id();
	}

	function update_latest($file_name, $file_path)
	{
		$latest = $this->get_active();
		if (empty($latest)) {
			return $this->save($file_name, $file_path);
		}
		$this->db->where('web_authorize_signature_id', $latest['web_authorize_signature_id']);
		$this->db->update('web_authorize_signature', array(
			'file_name' => $file_name,
			'file_path' => $file_path,
			'updated_at' => date('Y-m-d H:i:s')
		));
		return $latest['web_authorize_signature_id'];
	}

	function snapshot_url($doc_type, $doc_code, $ref_number = '', $platform = null)
	{
		$file = $this->snapshot_file($doc_type, $doc_code, $ref_number, $platform);
		if ($file === '') {
			return '';
		}
		return base_url().'uploads/authorize_signature/'.$file;
	}

	function snapshot_url_for_cn($cncode, $order_number, $platform)
	{
		return $this->snapshot_url('cn', $cncode, $order_number, $platform);
	}

	function snapshot_file($doc_type, $doc_code, $ref_number = '', $platform = null)
	{
		$latest = $this->get_active();
		$latest_id = empty($latest['web_authorize_signature_id']) ? 0 : (int)$latest['web_authorize_signature_id'];
		$latest_file = empty($latest['file_name']) ? '' : $latest['file_name'];
		if ($doc_code === '' || !$this->db->table_exists('web_document_signature_snapshot')) {
			return $latest_file;
		}

		$this->db->from('web_document_signature_snapshot');
		$this->db->where('doc_type', $doc_type);
		$this->db->where('doc_code', $doc_code);
		$this->db->limit(1);
		$existing = $this->db->get()->row_array();
		if (!empty($existing)) {
			$bound_id = (int)$existing['web_authorize_signature_id'];
			if ($latest_id > 0 && $bound_id === $latest_id) {
				if (!empty($latest_file) && $existing['file_name'] !== $latest_file) {
					$this->db->where('doc_type', $doc_type);
					$this->db->where('doc_code', $doc_code);
					$this->db->update('web_document_signature_snapshot', array(
						'file_name' => $latest['file_name'],
						'file_path' => $latest['file_path']
					));
				}
				return $latest_file;
			}
			return !empty($existing['file_name']) ? $existing['file_name'] : $latest_file;
		}

		if ($latest_file === '') {
			return '';
		}

		$this->db->insert('web_document_signature_snapshot', array(
			'doc_type' => $doc_type,
			'doc_code' => $doc_code,
			'ref_number' => $ref_number,
			'platform' => $platform,
			'web_authorize_signature_id' => $latest_id,
			'file_name' => $latest['file_name'],
			'file_path' => $latest['file_path'],
			'used_at' => date('Y-m-d H:i:s')
		));
		return $latest_file;
	}
}
