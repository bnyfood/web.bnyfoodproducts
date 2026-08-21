<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Platform_webhook_event_model extends CI_Model
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
		$q = $this->db->query("SELECT OBJECT_ID('dbo.platform_webhook_event') AS oid");
		$row = $q ? $q->row_array() : null;
		if (empty($row['oid'])) {
			$this->db->query("
				CREATE TABLE dbo.platform_webhook_event (
					event_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					event_code VARCHAR(80) NULL,
					shop_id VARCHAR(80) NULL,
					verified BIT NOT NULL CONSTRAINT DF_platform_webhook_event_verified DEFAULT (0),
					remote_ip VARCHAR(45) NULL,
					headers NVARCHAR(MAX) NULL,
					payload NVARCHAR(MAX) NULL,
					cdate DATETIME NOT NULL CONSTRAINT DF_platform_webhook_event_cdate DEFAULT (GETDATE())
				)
			");
			$this->db->query("CREATE INDEX IX_platform_webhook_event_plat_date ON dbo.platform_webhook_event (platform, cdate)");
			$this->db->query("CREATE INDEX IX_platform_webhook_event_code ON dbo.platform_webhook_event (event_code)");
		}
		$this->db->db_debug = $debug;
	}

	function insert_event($data)
	{
		$this->db->insert('platform_webhook_event', array(
			'platform' => isset($data['platform']) ? $data['platform'] : 'unknown',
			'event_code' => isset($data['event_code']) ? substr((string)$data['event_code'], 0, 80) : null,
			'shop_id' => isset($data['shop_id']) ? substr((string)$data['shop_id'], 0, 80) : null,
			'verified' => !empty($data['verified']) ? 1 : 0,
			'remote_ip' => isset($data['remote_ip']) ? substr((string)$data['remote_ip'], 0, 45) : null,
			'headers' => isset($data['headers']) ? $data['headers'] : null,
			'payload' => isset($data['payload']) ? $data['payload'] : null
		));
		return (int)$this->db->insert_id();
	}
}
