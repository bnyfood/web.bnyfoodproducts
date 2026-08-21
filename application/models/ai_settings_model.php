<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ai_settings_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->ensure_table();
	}

	function db_kind()
	{
		$d = strtolower((string)$this->db->dbdriver);
		if ($d === 'postgre' || $d === 'pgsql' || strpos($d, 'pgsql') !== false) {
			return 'pgsql';
		}
		return 'mssql';
	}

	function ensure_table()
	{
		$debug = $this->db->db_debug;
		$this->db->db_debug = false;
		$exists = false;
		if ($this->db_kind() === 'pgsql') {
			$q = $this->db->query("SELECT to_regclass('public.ai_settings') AS oid");
			$row = $q ? $q->row_array() : null;
			$exists = !empty($row['oid']);
		} else {
			$q = $this->db->query("SELECT OBJECT_ID('dbo.ai_settings') AS oid");
			$row = $q ? $q->row_array() : null;
			$exists = !empty($row['oid']);
		}
		if (!$exists) {
			if ($this->db_kind() === 'pgsql') {
				$this->db->query("
					CREATE TABLE ai_settings (
						settings_id SERIAL PRIMARY KEY,
						provider VARCHAR(30) NOT NULL DEFAULT 'openai',
						model_name VARCHAR(80) NOT NULL DEFAULT 'gpt-4o-mini',
						api_key TEXT NULL,
						observe_chat SMALLINT NOT NULL DEFAULT 1,
						auto_distill SMALLINT NOT NULL DEFAULT 1,
						updated_at TIMESTAMP NOT NULL DEFAULT NOW()
					)
				");
			} else {
				$this->db->query("
					CREATE TABLE dbo.ai_settings (
						settings_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
						provider VARCHAR(30) NOT NULL CONSTRAINT DF_ai_settings_provider DEFAULT ('openai'),
						model_name VARCHAR(80) NOT NULL CONSTRAINT DF_ai_settings_model DEFAULT ('gpt-4o-mini'),
						api_key NVARCHAR(500) NULL,
						observe_chat BIT NOT NULL CONSTRAINT DF_ai_settings_observe DEFAULT (1),
						auto_distill BIT NOT NULL CONSTRAINT DF_ai_settings_distill DEFAULT (1),
						updated_at DATETIME NOT NULL CONSTRAINT DF_ai_settings_upd DEFAULT (GETDATE())
					)
				");
			}
			$this->db->insert('ai_settings', array(
				'provider' => 'openai',
				'model_name' => 'gpt-4o-mini'
			));
		}
		$this->widen_api_key();
		$this->db->db_debug = $debug;
	}

	function has_api_key()
	{
		$row = $this->get();
		return trim((string)(isset($row['api_key']) ? $row['api_key'] : '')) !== '';
	}

	function widen_api_key()
	{
		if ($this->db_kind() === 'pgsql') {
			return;
		}
		$q = $this->db->query("SELECT CHARACTER_MAXIMUM_LENGTH AS ml FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'ai_settings' AND COLUMN_NAME = 'api_key'");
		$row = $q ? $q->row_array() : null;
		if (!empty($row) && (int)$row['ml'] > 0 && (int)$row['ml'] < 4000) {
			$this->db->query('ALTER TABLE dbo.ai_settings ALTER COLUMN api_key NVARCHAR(MAX) NULL');
		}
	}

	function get()
	{
		$this->db->from('ai_settings');
		$this->db->order_by('settings_id', 'asc');
		$this->db->limit(1);
		$q = $this->db->get();
		$row = $q ? $q->row_array() : null;
		if (empty($row)) {
			$this->db->insert('ai_settings', array(
				'provider' => 'openai',
				'model_name' => 'gpt-4o-mini'
			));
			return $this->get();
		}
		return $row;
	}

	function save($data)
	{
		$row = $this->get();
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->db->where('settings_id', $row['settings_id']);
		$this->db->update('ai_settings', $data);
	}
}
