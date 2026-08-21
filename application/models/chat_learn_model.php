<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat_learn_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->ensure_tables();
	}

	function db_kind()
	{
		$d = strtolower((string)$this->db->dbdriver);
		if ($d === 'postgre' || $d === 'pgsql' || strpos($d, 'pgsql') !== false) {
			return 'pgsql';
		}
		return 'mssql';
	}

	function db_label()
	{
		return ($this->db_kind() === 'pgsql') ? 'PostgreSQL' : 'Microsoft SQL Server';
	}

	function tq($name)
	{
		return ($this->db_kind() === 'pgsql') ? $name : 'dbo.'.$name;
	}

	function sql_zero($expr)
	{
		return ($this->db_kind() === 'pgsql') ? 'COALESCE('.$expr.', 0)' : 'ISNULL('.$expr.', 0)';
	}

	function ensure_tables()
	{
		$debug = $this->db->db_debug;
		$this->db->db_debug = false;
		foreach (array('chat_thread', 'chat_message', 'chat_reply_example', 'chat_playbook', 'chat_coach') as $name) {
			$this->create_if_missing($name);
		}
		$this->add_column('chat_thread', 'buyer_id', 'VARCHAR(80) NULL');
		$this->add_column('chat_thread', 'last_preview', 'NVARCHAR(400) NULL', 'TEXT NULL');
		$this->add_column('chat_thread', 'unread', 'INT NOT NULL CONSTRAINT DF_chat_thread_unread DEFAULT (0)', 'INT NOT NULL DEFAULT 0');
		$this->add_column('chat_thread', 'extra_json', 'NVARCHAR(MAX) NULL', 'TEXT NULL');
		$this->add_column('chat_thread', 'buyer_avatar', 'NVARCHAR(1000) NULL', 'TEXT NULL');
		$this->add_column('chat_thread', 'last_from', 'VARCHAR(10) NULL');
		$this->add_column('chat_message', 'platform_msg_id', 'VARCHAR(80) NULL');
		$this->add_column('chat_message', 'msg_type', "VARCHAR(30) NOT NULL CONSTRAINT DF_chat_message_type DEFAULT ('text')", "VARCHAR(30) NOT NULL DEFAULT 'text'");
		$this->add_column('chat_message', 'extra_json', 'NVARCHAR(MAX) NULL', 'TEXT NULL');
		$this->add_column('chat_coach', 'suggest_reply', 'NVARCHAR(MAX) NULL', 'TEXT NULL');
		$this->add_column('chat_coach', 'source_json', 'NVARCHAR(MAX) NULL', 'TEXT NULL');
		$this->add_column('chat_coach', 'attach_json', 'NVARCHAR(MAX) NULL', 'TEXT NULL');
		$this->widen_conv_id();
		$this->db->db_debug = $debug;
	}

	function table_exists($name)
	{
		if ($this->db_kind() === 'pgsql') {
			$q = $this->db->query("SELECT to_regclass('public.".$name."') AS oid");
			$row = $q ? $q->row_array() : null;
			return !empty($row['oid']);
		}
		$q = $this->db->query("SELECT OBJECT_ID('dbo.".$name."') AS oid");
		$row = $q ? $q->row_array() : null;
		return !empty($row['oid']);
	}

	function column_exists($table, $column)
	{
		if ($this->db_kind() === 'pgsql') {
			$q = $this->db->query(
				"SELECT 1 AS ok FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? AND column_name = ?",
				array($table, $column)
			);
			$row = $q ? $q->row_array() : null;
			return !empty($row);
		}
		$q = $this->db->query("SELECT COL_LENGTH('dbo.".$table."', '".$column."') AS clen");
		$row = $q ? $q->row_array() : null;
		return !empty($row['clen']);
	}

	function add_column($table, $column, $mssql_type, $pgsql_type = '')
	{
		if ($this->column_exists($table, $column)) {
			return;
		}
		if ($this->db_kind() === 'pgsql') {
			$spec = $pgsql_type !== '' ? $pgsql_type : $mssql_type;
			$this->db->query('ALTER TABLE '.$table.' ADD COLUMN '.$column.' '.$spec);
			return;
		}
		$this->db->query('ALTER TABLE dbo.'.$table.' ADD '.$column.' '.$mssql_type);
	}

	function widen_conv_id()
	{
		$q = $this->db->query("SELECT CHARACTER_MAXIMUM_LENGTH AS ml FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chat_thread' AND COLUMN_NAME = 'platform_conv_id'");
		$row = $q ? $q->row_array() : null;
		if (empty($row) || (int)$row['ml'] <= 0 || (int)$row['ml'] >= 120) {
			return;
		}
		if ($this->db_kind() === 'pgsql') {
			$this->db->query('ALTER TABLE chat_thread ALTER COLUMN platform_conv_id TYPE VARCHAR(120)');
			return;
		}
		$this->db->query('ALTER TABLE dbo.chat_thread ALTER COLUMN platform_conv_id VARCHAR(120) NULL');
	}

	function create_if_missing($name)
	{
		if ($this->table_exists($name)) {
			return;
		}
		$sql = $this->ddl($name);
		if ($sql !== '') {
			$this->db->query($sql);
		}
	}

	function ddl($name)
	{
		$kind = $this->db_kind();
		$map = array(
			'chat_thread' => array(
				'mssql' => "CREATE TABLE dbo.chat_thread (
					thread_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					platform_conv_id VARCHAR(80) NULL,
					buyer_name NVARCHAR(200) NULL,
					status VARCHAR(20) NOT NULL CONSTRAINT DF_chat_thread_status DEFAULT ('open'),
					last_message_at DATETIME NULL,
					cdate DATETIME NOT NULL CONSTRAINT DF_chat_thread_cdate DEFAULT (GETDATE())
				)",
				'pgsql' => "CREATE TABLE chat_thread (
					thread_id SERIAL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					platform_conv_id VARCHAR(80) NULL,
					buyer_name TEXT NULL,
					status VARCHAR(20) NOT NULL DEFAULT 'open',
					last_message_at TIMESTAMP NULL,
					cdate TIMESTAMP NOT NULL DEFAULT NOW()
				)"
			),
			'chat_message' => array(
				'mssql' => "CREATE TABLE dbo.chat_message (
					message_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					thread_id INT NOT NULL,
					direction VARCHAR(10) NOT NULL,
					body NVARCHAR(MAX) NOT NULL,
					sender VARCHAR(20) NOT NULL,
					ai_draft NVARCHAR(MAX) NULL,
					cdate DATETIME NOT NULL CONSTRAINT DF_chat_message_cdate DEFAULT (GETDATE())
				)",
				'pgsql' => "CREATE TABLE chat_message (
					message_id SERIAL PRIMARY KEY,
					thread_id INT NOT NULL,
					direction VARCHAR(10) NOT NULL,
					body TEXT NOT NULL,
					sender VARCHAR(20) NOT NULL,
					ai_draft TEXT NULL,
					cdate TIMESTAMP NOT NULL DEFAULT NOW()
				)"
			),
			'chat_reply_example' => array(
				'mssql' => "CREATE TABLE dbo.chat_reply_example (
					example_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					thread_id INT NULL,
					inbound_text NVARCHAR(MAX) NOT NULL,
					outbound_text NVARCHAR(MAX) NOT NULL,
					ai_draft NVARCHAR(MAX) NULL,
					human_edited BIT NOT NULL CONSTRAINT DF_chat_example_edited DEFAULT (0),
					weight SMALLINT NOT NULL CONSTRAINT DF_chat_example_w DEFAULT (1),
					cdate DATETIME NOT NULL CONSTRAINT DF_chat_example_cdate DEFAULT (GETDATE())
				)",
				'pgsql' => "CREATE TABLE chat_reply_example (
					example_id SERIAL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					thread_id INT NULL,
					inbound_text TEXT NOT NULL,
					outbound_text TEXT NOT NULL,
					ai_draft TEXT NULL,
					human_edited SMALLINT NOT NULL DEFAULT 0,
					weight SMALLINT NOT NULL DEFAULT 1,
					cdate TIMESTAMP NOT NULL DEFAULT NOW()
				)"
			),
			'chat_playbook' => array(
				'mssql' => "CREATE TABLE dbo.chat_playbook (
					playbook_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					rules_text NVARCHAR(MAX) NULL,
					example_count INT NOT NULL CONSTRAINT DF_chat_playbook_cnt DEFAULT (0),
					updated_at DATETIME NOT NULL CONSTRAINT DF_chat_playbook_upd DEFAULT (GETDATE())
				)",
				'pgsql' => "CREATE TABLE chat_playbook (
					playbook_id SERIAL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					rules_text TEXT NULL,
					example_count INT NOT NULL DEFAULT 0,
					updated_at TIMESTAMP NOT NULL DEFAULT NOW()
				)"
			),
			'chat_coach' => array(
				'mssql' => "CREATE TABLE dbo.chat_coach (
					coach_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					thread_id INT NOT NULL,
					role VARCHAR(10) NOT NULL,
					body NVARCHAR(MAX) NOT NULL,
					suggest_reply NVARCHAR(MAX) NULL,
					source_json NVARCHAR(MAX) NULL,
					cdate DATETIME NOT NULL CONSTRAINT DF_chat_coach_cdate DEFAULT (GETDATE())
				)",
				'pgsql' => "CREATE TABLE chat_coach (
					coach_id SERIAL PRIMARY KEY,
					thread_id INT NOT NULL,
					role VARCHAR(10) NOT NULL,
					body TEXT NOT NULL,
					suggest_reply TEXT NULL,
					source_json TEXT NULL,
					cdate TIMESTAMP NOT NULL DEFAULT NOW()
				)"
			)
		);
		return (isset($map[$name][$kind]) ? $map[$name][$kind] : '');
	}

	function insert_thread($data)
	{
		$ok = $this->db->insert('chat_thread', $data);
		$id = (int)$this->db->insert_id();
		if (!$ok || $id < 1) {
			if (isset($data['last_message_at'])) {
				unset($data['last_message_at']);
				$ok = $this->db->insert('chat_thread', $data);
				$id = (int)$this->db->insert_id();
			}
		}
		if (!$ok || $id < 1) {
			$err = $this->db->error();
			log_message('error', 'chat_thread insert failed: '.json_encode($err));
			return 0;
		}
		return $id;
	}

	function update_thread($id, $data)
	{
		$this->db->where('thread_id', (int)$id);
		$this->db->update('chat_thread', $data);
	}

	function get_thread($id)
	{
		$this->db->from('chat_thread');
		$this->db->where('thread_id', (int)$id);
		$q = $this->db->get();
		return $q ? $q->row_array() : null;
	}

	function list_threads($limit = 50)
	{
		$this->db->from('chat_thread');
		$this->db->order_by('last_message_at', 'desc');
		$this->db->limit((int)$limit);
		$q = $this->db->get();
		return $q ? $q->result_array() : array();
	}

	function last_direction_map($ids)
	{
		$clean = array();
		foreach ((array)$ids as $id) {
			$id = (int)$id;
			if ($id > 0) {
				$clean[$id] = $id;
			}
		}
		if (empty($clean)) {
			return array();
		}
		$msg = $this->tq('chat_message');
		$sql = "SELECT m.thread_id, m.direction
			FROM ".$msg." m
			INNER JOIN (
				SELECT thread_id, MAX(message_id) AS mid
				FROM ".$msg."
				WHERE thread_id IN (".implode(',', $clean).")
				GROUP BY thread_id
			) x ON x.thread_id = m.thread_id AND x.mid = m.message_id";
		$q = $this->db->query($sql);
		$out = array();
		if ($q) {
			foreach ($q->result_array() as $row) {
				$out[(int)$row['thread_id']] = $row['direction'];
			}
		}
		return $out;
	}

	function unreplied_counts()
	{
		$th = $this->tq('chat_thread');
		$msg = $this->tq('chat_message');
		$unread = $this->sql_zero('t.unread');
		$sql = "SELECT t.platform,
				SUM(CASE
					WHEN lm.direction = 'in' THEN 1
					WHEN lm.direction = 'out' THEN 0
					WHEN t.last_from = 'buyer' THEN 1
					WHEN t.last_from = 'shop' THEN 0
					WHEN ".$unread." > 0 THEN 1
					ELSE 0
				END) AS waiting
			FROM ".$th." t
			LEFT JOIN (
				SELECT m.thread_id, m.direction
				FROM ".$msg." m
				INNER JOIN (
					SELECT thread_id, MAX(message_id) AS mid
					FROM ".$msg."
					GROUP BY thread_id
				) x ON x.thread_id = m.thread_id AND x.mid = m.message_id
			) lm ON lm.thread_id = t.thread_id
			GROUP BY t.platform";
		$out = array('shopee' => 0, 'lazada' => 0, 'tiktok' => 0, 'all' => 0);
		$q = $this->db->query($sql);
		if ($q) {
			foreach ($q->result_array() as $row) {
				$p = strtolower(trim((string)$row['platform']));
				$n = (int)$row['waiting'];
				if (isset($out[$p])) {
					$out[$p] = $n;
				}
				$out['all'] += $n;
			}
		}
		return $out;
	}

	function get_by_conv($platform, $conv_id)
	{
		$this->db->from('chat_thread');
		$this->db->where('platform', $platform);
		$this->db->where('platform_conv_id', (string)$conv_id);
		$q = $this->db->get();
		return $q ? $q->row_array() : null;
	}

	function upsert_thread($platform, $conv_id, $data)
	{
		$row = $this->get_by_conv($platform, $conv_id);
		if (isset($data['buyer_avatar']) && trim((string)$data['buyer_avatar']) === '') {
			unset($data['buyer_avatar']);
		}
		if (isset($data['last_from']) && trim((string)$data['last_from']) === '') {
			unset($data['last_from']);
		}
		if (isset($data['buyer_id'])) {
			$bid = trim((string)$data['buyer_id']);
			if ($bid === '' || $bid === '0') {
				unset($data['buyer_id']);
			}
		}
		if (empty($row)) {
			$data['platform'] = $platform;
			$data['platform_conv_id'] = (string)$conv_id;
			if (!isset($data['status'])) {
				$data['status'] = 'open';
			}
			return $this->insert_thread($data);
		}
		if (isset($data['extra_json']) && !empty($row['extra_json'])) {
			$old = json_decode($row['extra_json'], true);
			$new = json_decode($data['extra_json'], true);
			if (is_array($old) && is_array($new)) {
				foreach ($new as $k => $v) {
					if ($v === '' || $v === null) {
						unset($new[$k]);
					}
				}
				$data['extra_json'] = json_encode(array_merge($old, $new), JSON_UNESCAPED_UNICODE);
			}
		}
		$this->update_thread($row['thread_id'], $data);
		return (int)$row['thread_id'];
	}

	function message_exists($thread_id, $platform_msg_id)
	{
		if ($platform_msg_id === '' || $platform_msg_id === null) {
			return false;
		}
		$this->db->from('chat_message');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->where('platform_msg_id', (string)$platform_msg_id);
		return $this->db->count_all_results() > 0;
	}

	function insert_message($data)
	{
		$this->db->insert('chat_message', $data);
		return (int)$this->db->insert_id();
	}

	function messages($thread_id)
	{
		$this->db->from('chat_message');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->order_by('message_id', 'asc');
		$q = $this->db->get();
		return $q ? $q->result_array() : array();
	}

	function last_inbound($thread_id)
	{
		$this->db->from('chat_message');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->where('direction', 'in');
		$this->db->order_by('message_id', 'desc');
		$this->db->limit(1);
		$q = $this->db->get();
		return $q ? $q->row_array() : null;
	}

	function last_message($thread_id)
	{
		$this->db->from('chat_message');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->order_by('message_id', 'desc');
		$this->db->limit(1);
		$q = $this->db->get();
		return $q ? $q->row_array() : null;
	}

	function thread_messages_since($thread_id, $from_dt)
	{
		$this->db->from('chat_message');
		$this->db->where('thread_id', (int)$thread_id);
		if ($from_dt !== '') {
			$this->db->where('cdate >=', $from_dt);
		}
		$this->db->order_by('message_id', 'asc');
		$q = $this->db->get();
		return $q ? $q->result_array() : array();
	}

	function delete_messages($ids)
	{
		$clean = array();
		foreach ((array)$ids as $id) {
			$id = (int)$id;
			if ($id > 0) {
				$clean[$id] = $id;
			}
		}
		if (empty($clean)) {
			return 0;
		}
		$this->db->where_in('message_id', array_values($clean));
		$this->db->delete('chat_message');
		return (int)$this->db->affected_rows();
	}

	function insert_example($data)
	{
		$this->db->insert('chat_reply_example', $data);
		return (int)$this->db->insert_id();
	}

	function examples_for_platform($platform, $limit = 80)
	{
		$this->db->from('chat_reply_example');
		if ($platform !== '' && $platform !== 'all') {
			$this->db->group_start();
			$this->db->where('platform', $platform);
			$this->db->or_where('platform', 'all');
			$this->db->group_end();
		}
		$this->db->order_by('example_id', 'desc');
		$this->db->limit((int)$limit);
		$q = $this->db->get();
		return $q ? $q->result_array() : array();
	}

	function count_examples($platform)
	{
		$this->db->from('chat_reply_example');
		if ($platform !== 'all') {
			$this->db->where('platform', $platform);
		}
		return (int)$this->db->count_all_results();
	}

	function get_playbook($platform)
	{
		$this->db->from('chat_playbook');
		$this->db->where('platform', $platform);
		$q = $this->db->get();
		$row = $q ? $q->row_array() : null;
		if (empty($row)) {
			$this->db->insert('chat_playbook', array(
				'platform' => $platform,
				'rules_text' => '',
				'example_count' => 0
			));
			return $this->get_playbook($platform);
		}
		return $row;
	}

	function save_playbook($platform, $rules_text, $example_count)
	{
		$row = $this->get_playbook($platform);
		$this->db->where('playbook_id', $row['playbook_id']);
		$this->db->update('chat_playbook', array(
			'rules_text' => $rules_text,
			'example_count' => (int)$example_count,
			'updated_at' => date('Y-m-d H:i:s')
		));
	}

	function all_playbooks()
	{
		$this->db->from('chat_playbook');
		$this->db->order_by('platform', 'asc');
		$q = $this->db->get();
		return $q ? $q->result_array() : array();
	}

	function insert_coach($data)
	{
		$clean = array();
		foreach ((array)$data as $k => $v) {
			if ($v === null) {
				continue;
			}
			$clean[$k] = $v;
		}
		if (empty($clean['thread_id']) || empty($clean['role']) || !isset($clean['body'])) {
			return 0;
		}
		$this->db->insert('chat_coach', $clean);
		return (int)$this->db->insert_id();
	}

	function coach_messages($thread_id, $limit = 80)
	{
		$this->db->from('chat_coach');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->order_by('coach_id', 'asc');
		$q = $this->db->get();
		$rows = $q ? $q->result_array() : array();
		if ($limit > 0 && count($rows) > $limit) {
			$rows = array_slice($rows, -$limit);
		}
		return $rows;
	}

	function last_coach_suggest($thread_id)
	{
		$row = $this->last_coach_ai($thread_id);
		if (empty($row) || trim((string)$row['suggest_reply']) === '') {
			return '';
		}
		return (string)$row['suggest_reply'];
	}

	function last_coach_attach($thread_id)
	{
		$row = $this->last_coach_ai($thread_id);
		if (empty($row) || empty($row['attach_json'])) {
			return array();
		}
		$j = json_decode($row['attach_json'], true);
		return is_array($j) ? $j : array();
	}

	function decode_attach($raw)
	{
		if (is_array($raw)) {
			return $raw;
		}
		$j = json_decode((string)$raw, true);
		return is_array($j) ? $j : array();
	}

	function last_coach_ai($thread_id)
	{
		$this->db->from('chat_coach');
		$this->db->where('thread_id', (int)$thread_id);
		$this->db->where('role', 'ai');
		$this->db->order_by('coach_id', 'desc');
		$this->db->limit(20);
		$q = $this->db->get();
		$rows = $q ? $q->result_array() : array();
		foreach ($rows as $row) {
			if (trim((string)(isset($row['suggest_reply']) ? $row['suggest_reply'] : '')) !== '') {
				return $row;
			}
		}
		return !empty($rows[0]) ? $rows[0] : null;
	}
}
