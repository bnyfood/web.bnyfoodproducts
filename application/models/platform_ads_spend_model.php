<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Platform_ads_spend_model extends CI_Model
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
		$q = $this->db->query("SELECT OBJECT_ID('dbo.platform_ads_spend') AS oid");
		$row = $q ? $q->row_array() : null;
		if (empty($row['oid'])) {
			$this->db->query("
				CREATE TABLE dbo.platform_ads_spend (
					ads_spend_id INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
					platform VARCHAR(20) NOT NULL,
					spend_date DATE NOT NULL,
					spend DECIMAL(18,2) NOT NULL DEFAULT 0,
					impressions BIGINT NULL,
					clicks INT NULL,
					conversions INT NULL,
					campaign_id VARCHAR(80) NULL,
					campaign_name NVARCHAR(255) NULL,
					currency VARCHAR(10) NULL DEFAULT 'THB',
					source VARCHAR(30) NULL,
					payload NVARCHAR(MAX) NULL,
					cdate DATETIME NOT NULL DEFAULT GETDATE()
				)
			");
			$this->db->query("CREATE INDEX IX_platform_ads_spend_plat_date ON dbo.platform_ads_spend (platform, spend_date)");
		}
		$this->db->db_debug = $debug;
	}

	function upsert_day($data)
	{
		$platform = strtolower(trim((string)$data['platform']));
		$spend_date = (string)$data['spend_date'];
		$campaign_id = isset($data['campaign_id']) ? (string)$data['campaign_id'] : '';
		$this->db->from('platform_ads_spend');
		$this->db->where('platform', $platform);
		$this->db->where('spend_date', $spend_date);
		if ($campaign_id === '') {
			$this->db->group_start();
			$this->db->where('campaign_id', '');
			$this->db->or_where('campaign_id IS NULL', null, false);
			$this->db->group_end();
		} else {
			$this->db->where('campaign_id', $campaign_id);
		}
		$existing = $this->db->get()->row_array();
		$save = array(
			'platform' => $platform,
			'spend_date' => $spend_date,
			'spend' => isset($data['spend']) ? (float)$data['spend'] : 0,
			'impressions' => isset($data['impressions']) ? (int)$data['impressions'] : null,
			'clicks' => isset($data['clicks']) ? (int)$data['clicks'] : null,
			'conversions' => isset($data['conversions']) ? (int)$data['conversions'] : null,
			'campaign_id' => $campaign_id,
			'campaign_name' => isset($data['campaign_name']) ? $data['campaign_name'] : null,
			'currency' => isset($data['currency']) ? $data['currency'] : 'THB',
			'source' => isset($data['source']) ? $data['source'] : 'webhook',
			'payload' => isset($data['payload']) ? $data['payload'] : null
		);
		if (!empty($existing['ads_spend_id'])) {
			$this->db->where('ads_spend_id', $existing['ads_spend_id']);
			$this->db->update('platform_ads_spend', $save);
			return (int)$existing['ads_spend_id'];
		}
		$this->db->insert('platform_ads_spend', $save);
		return (int)$this->db->insert_id();
	}

	function sum_by_date($start_date, $end_date)
	{
		$sql = "SELECT platform, CONVERT(varchar(10), spend_date, 23) AS d, SUM(spend) AS amt
			FROM platform_ads_spend
			WHERE spend_date >= ? AND spend_date <= ?
			GROUP BY platform, CONVERT(varchar(10), spend_date, 23)
			ORDER BY d";
		$q = $this->db->query($sql, array($start_date, $end_date));
		return $q ? $q->result_array() : array();
	}
}
