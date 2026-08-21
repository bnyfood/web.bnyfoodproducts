<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Compare live statuses in our DB (fed by platform APIs) against the catalog
 * our software logic already knows. New/unknown values surface in Alerts
 * as "Status change" so admin can discuss logic updates.
 *
 * Task Scheduler (daily is enough; weekly also fine):
 *   curl.exe -k --silent "https://www.bnyfoodproducts.com/platform_status/scan?secret=..."
 */
class Platform_status_bl
{
	function __construct()
	{
		$this->CI =& get_instance();
	}

	function catalog()
	{
		return array(
			'shopee_order' => array(
				'label' => 'Shopee order',
				'logic' => 'CN รีฟันด์เฉพาะ REFUND_PAID/COMPLETED · CN แคนเซิลวันที่ CANCELLED หลังตัดขาย/มีใบกำกับ',
				'known' => array(
					'UNPAID', 'PENDING', 'READY_TO_SHIP', 'PROCESSED', 'RETRY_SHIP',
					'SHIPPED', 'TO_CONFIRM_RECEIVE', 'IN_CANCEL', 'CANCELLED',
					'TO_RETURN', 'COMPLETED'
				)
			),
			'shopee_return' => array(
				'label' => 'Shopee return/refund',
				'logic' => 'CN รีฟันด์เฉพาะ REFUND_PAID/COMPLETED (ไม่ใช่ REQUESTED/ACCEPTED)',
				'known' => array(
					'REQUESTED', 'PROCESSING', 'ACCEPTED', 'JUDGING', 'SELLER_DISPUTE',
					'REFUND_PAID', 'COMPLETED', 'CANCELLED', 'CLOSED'
				)
			),
			'lazada_order' => array(
				'label' => 'Lazada order',
				'logic' => 'ภาษีขายที่ packed · CN จาก canceled/returned/failed_delivery/shipped_back_success/เสียหาย/หาย (ไม่นับ shipped_back ค้างคาว)',
				'known' => array(
					'unpaid', 'pending', 'packed', 'repacked', 'topack', 'toship',
					'ready_to_ship', 'shipped', 'delivered', 'confirmed',
					'canceled', 'cancelled', 'failed_delivery',
					'shipped_back', 'shipped_back_success', 'shipped_back_failed',
					'lost_by_3pl', 'damaged_by_3pl', 'returned', 'package_scrapped'
				)
			),
			'lazada_return' => array(
				'label' => 'Lazada reverse',
				'logic' => 'CN รีฟันด์เมื่อ reverse คอมพลีท (ไม่นับ REFUND_PENDING)',
				'known' => array(
					'REQUEST_INITIATE', 'REQUEST_REJECT', 'REQUEST_CANCEL',
					'CANCEL_SUCCESS', 'REFUND_PENDING', 'REFUND_SUCCESS',
					'RETURN_SUCCESS', 'SUCCESS', 'COMPLETE', 'COMPLETED'
				)
			),
			'tiktok_order' => array(
				'label' => 'TikTok order',
				'logic' => 'ภาษีขายที่ Packet · CN จาก CANCELLED หลังตัดขาย',
				'known' => array(
					'UNPAID', 'ON_HOLD', 'AWAITING_SHIPMENT', 'AWAITING_COLLECTION',
					'PARTIALLY_SHIPPING', 'IN_TRANSIT', 'DELIVERED', 'COMPLETED',
					'CANCELLED', 'Packet', 'Shipped', 'Completed', 'Canceled'
				)
			),
			'tiktok_return' => array(
				'label' => 'TikTok return/cancel',
				'logic' => 'CN รีฟันด์เฉพาะ REFUND_COMPLETE · Cancel เฉพาะ CANCELLATION_REQUEST_COMPLETE หลัง Packet',
				'known' => array(
					'REFUND_COMPLETE',
					'RETURN_OR_REFUND_REQUEST_SUCCESS',
					'RETURN_OR_REFUND_REQUEST_COMPLETE',
					'RETURN_OR_REFUND_REQUEST_CANCEL',
					'CANCELLATION_REQUEST_SUCCESS',
					'CANCELLATION_REQUEST_COMPLETE',
					'CANCELLATION_REQUEST_CANCEL',
					'SUCCESS', 'COMPLETED', 'PENDING', 'REJECT'
				)
			)
		);
	}

	function scan()
	{
		$catalog = $this->catalog();
		$live = $this->live_statuses();
		$issues = array();
		$groups = array();
		foreach ($catalog as $key => $meta) {
			$known_map = array();
			foreach ($meta['known'] as $k) {
				$known_map[$this->norm($k)] = (string)$k;
			}
			$seen = isset($live[$key]) ? $live[$key] : array();
			$unknown = array();
			$known_hit = array();
			foreach ($seen as $st => $n) {
				$nkey = $this->norm($st);
				if ($nkey === '') {
					continue;
				}
				if (!isset($known_map[$nkey])) {
					$unknown[] = array(
						'status' => $st,
						'count' => (int)$n
					);
				} else {
					$known_hit[] = array(
						'status' => $st,
						'count' => (int)$n
					);
				}
			}
			usort($unknown, array($this, 'sort_by_count'));
			usort($known_hit, array($this, 'sort_by_count'));
			$row = array(
				'key' => $key,
				'label' => $meta['label'],
				'logic' => $meta['logic'],
				'unknown' => $unknown,
				'known_seen' => $known_hit,
				'unknown_n' => count($unknown)
			);
			$groups[$key] = $row;
			if (!empty($unknown)) {
				foreach ($unknown as $u) {
					$issues[] = array(
						'key' => $key,
						'label' => $meta['label'],
						'logic' => $meta['logic'],
						'status' => $u['status'],
						'count' => $u['count']
					);
				}
			}
		}
		$out = array(
			'ok' => true,
			'at' => time(),
			'checked_at' => date('Y-m-d H:i:s'),
			'issue_n' => count($issues),
			'topic' => (count($issues) > 0) ? 1 : 0,
			'issues' => $issues,
			'groups' => $groups
		);
		$this->write_report($out);
		return $out;
	}

	function alert_slice()
	{
		$report = $this->last_report();
		$n = isset($report['issue_n']) ? (int)$report['issue_n'] : 0;
		return array(
			'topic' => ($n > 0) ? 1 : 0,
			'issue_n' => $n,
			'checked_at' => isset($report['checked_at']) ? $report['checked_at'] : '',
			'issues' => isset($report['issues']) && is_array($report['issues']) ? $report['issues'] : array(),
			'groups' => isset($report['groups']) && is_array($report['groups']) ? $report['groups'] : array()
		);
	}

	function live_statuses()
	{
		return array(
			'shopee_order' => $this->distinct_counts('shopee_orders', 'order_status'),
			'shopee_return' => $this->distinct_counts('shopee_return_order', 'status'),
			'lazada_order' => $this->distinct_counts('lazada_orders', 'status'),
			'lazada_return' => $this->distinct_counts_if('lazada_return_order', 'reverse_status'),
			'tiktok_order' => $this->distinct_counts('tiktok_orders', 'status'),
			'tiktok_return' => $this->distinct_counts_if('tiktok_return_order', 'status')
		);
	}

	function distinct_counts_if($table, $col)
	{
		$table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
		$col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$col);
		if ($table === '' || $col === '') {
			return array();
		}
		$q = $this->CI->db->query("SELECT OBJECT_ID('dbo.".$table."') AS oid");
		$row = $q ? $q->row_array() : array();
		if (empty($row['oid'])) {
			return array();
		}
		return $this->distinct_counts($table, $col);
	}

	function distinct_counts($table, $col)
	{
		$table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
		$col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$col);
		if ($table === '' || $col === '') {
			return array();
		}
		$sql = 'SELECT CAST('.$col.' AS NVARCHAR(200)) AS st, COUNT(*) AS n FROM '.$table.' GROUP BY CAST('.$col.' AS NVARCHAR(200))';
		return $this->run_distinct($sql);
	}

	function run_distinct($sql)
	{
		$out = array();
		$q = $this->CI->db->query($sql);
		if (!$q) {
			return $out;
		}
		foreach ($q->result_array() as $row) {
			$st = isset($row['st']) ? trim((string)$row['st']) : '';
			if ($st === '') {
				continue;
			}
			$out[$st] = isset($row['n']) ? (int)$row['n'] : 0;
		}
		return $out;
	}

	function norm($s)
	{
		return strtoupper(trim((string)$s));
	}

	function sort_by_count($a, $b)
	{
		$ca = isset($a['count']) ? (int)$a['count'] : 0;
		$cb = isset($b['count']) ? (int)$b['count'] : 0;
		if ($ca === $cb) {
			return strcmp((string)$a['status'], (string)$b['status']);
		}
		return ($ca > $cb) ? -1 : 1;
	}

	function last_report()
	{
		$path = $this->report_path();
		if (!is_file($path)) {
			return array();
		}
		$j = json_decode((string)@file_get_contents($path), true);
		return is_array($j) ? $j : array();
	}

	function write_report($data)
	{
		$path = $this->report_path();
		$dir = dirname($path);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($path, json_encode($data));
	}

	function report_path()
	{
		$base = defined('APP_STORE_PATH') ? APP_STORE_PATH : APPPATH.'cache';
		return rtrim(str_replace('\\', '/', (string)$base), '/').'/platform_status_scan.json';
	}
}
