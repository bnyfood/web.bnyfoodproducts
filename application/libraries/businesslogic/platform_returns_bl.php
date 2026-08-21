<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Poll reverse/return/refund/cancel lists and map them onto the existing
 * tax-invoice vs credit-note flags.
 *
 * Path A (refund): Shopee REFUND_PAID/COMPLETED; TikTok REFUND_COMPLETE;
 *   Lazada reverse SUCCESS/COMPLETE (not PENDING).
 * Path B (cancel/logistics): Shopee CANCELLED after sold; Lazada death statuses;
 *   TikTok CANCELLATION_REQUEST_COMPLETE after Packet.
 * Does not rewrite escrow or sale amounts.
 * Does not call Shopee execute() used by order crons.
 */
class Platform_returns_bl
{
	const MAX_PAGES = 20;
	const PAGE_SIZE = 50;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('businesslogic/chat_platform_bl');
		$this->CI->load->model('shopee_return_order_model');
		$this->CI->load->model('shopee_orders_model');
		$this->CI->load->model('lazada_return_order_model');
		$this->CI->load->model('lazada_orders_model');
		$this->CI->load->model('tiktok_return_order_model');
		$this->CI->load->model('tiktok_orders_model');
	}

	function poll($only, $days)
	{
		$days = (int)$days;
		if ($days < 1) {
			$days = 14;
		}
		if ($days > 30) {
			$days = 30;
		}
		$only = strtolower(trim((string)$only));
		$out = array();
		if ($only === '' || $only === 'all' || $only === 'shopee') {
			$out['shopee'] = $this->poll_shopee($days);
		}
		if ($only === '' || $only === 'all' || $only === 'lazada') {
			$out['lazada'] = $this->poll_lazada($days);
		}
		if ($only === '' || $only === 'all' || $only === 'tiktok') {
			$out['tiktok'] = $this->poll_tiktok($days);
		}
		return $out;
	}

	function apply_existing($only)
	{
		$only = strtolower(trim((string)$only));
		$out = array();
		if ($only === '' || $only === 'all' || $only === 'shopee') {
			$out['shopee'] = $this->apply_shopee_existing();
		}
		if ($only === '' || $only === 'all' || $only === 'lazada') {
			$out['lazada'] = $this->apply_lazada_existing();
		}
		if ($only === '' || $only === 'all' || $only === 'tiktok') {
			$out['tiktok'] = $this->apply_tiktok_existing();
		}
		return $out;
	}

	function snapshot()
	{
		$shopee_n = 0;
		$q = $this->CI->db->query("SELECT COUNT(*) AS n FROM shopee_return_order");
		if ($q) {
			$row = $q->row_array();
			$shopee_n = isset($row['n']) ? (int)$row['n'] : 0;
		}
		$laz_n = 0;
		$q = $this->CI->db->query("SELECT COUNT(*) AS n FROM lazada_return_order");
		if ($q) {
			$row = $q->row_array();
			$laz_n = isset($row['n']) ? (int)$row['n'] : 0;
		}
		$tt_n = 0;
		$q = $this->CI->db->query("SELECT COUNT(*) AS n FROM tiktok_return_order");
		if ($q) {
			$row = $q->row_array();
			$tt_n = isset($row['n']) ? (int)$row['n'] : 0;
		}
		return array(
			'shopee_return_order' => $shopee_n,
			'lazada_return_order' => $laz_n,
			'tiktok_return_order' => $tt_n
		);
	}

	function poll_shopee($days)
	{
		$out = array('ok' => true, 'error' => '', 'fetched' => 0, 'upserted' => 0, 'flagged' => 0);
		$shopee_days = $days;
		if ($shopee_days > 14) {
			$shopee_days = 14;
		}
		$to = time();
		$from = $to - ($shopee_days * 86400);
		for ($page = 1; $page <= self::MAX_PAGES; $page++) {
			$res = $this->CI->chat_platform_bl->shopee_call('/api/v2/returns/get_return_list', 'get', array(
				'page_no' => $page,
				'page_size' => self::PAGE_SIZE,
				'update_time_from' => $from,
				'update_time_to' => $to
			));
			if (isset($res['_err'])) {
				$out['ok'] = false;
				$out['error'] = $res['_err'];
				break;
			}
			if (!empty($res['error']) && (string)$res['error'] !== '') {
				$out['ok'] = false;
				$out['error'] = (string)$res['error'];
				break;
			}
			$list = array();
			if (isset($res['response']['return']) && is_array($res['response']['return'])) {
				$list = $res['response']['return'];
			} elseif (isset($res['response']['return_list']) && is_array($res['response']['return_list'])) {
				$list = $res['response']['return_list'];
			}
			if (empty($list)) {
				break;
			}
			foreach ($list as $row) {
				$out['fetched']++;
				if ($this->upsert_shopee_return($row)) {
					$out['upserted']++;
				}
				$sn = isset($row['order_sn']) ? (string)$row['order_sn'] : '';
				$st = isset($row['status']) ? (string)$row['status'] : '';
				if ($this->apply_shopee_flags($sn, $st)) {
					$out['flagged']++;
				}
			}
			$more = false;
			if (isset($res['response']['more'])) {
				$more = ($res['response']['more'] === true || $res['response']['more'] === 1 || $res['response']['more'] === 'true');
			}
			if (!$more) {
				break;
			}
		}
		$exist = $this->apply_shopee_existing();
		$out['flagged'] += isset($exist['flagged']) ? (int)$exist['flagged'] : 0;
		return $out;
	}

	function poll_lazada($days)
	{
		$out = array('ok' => true, 'error' => '', 'fetched' => 0, 'upserted' => 0, 'flagged' => 0);
		$end = (int)round(microtime(true) * 1000);
		$start = $end - ($days * 86400 * 1000);
		for ($page = 1; $page <= self::MAX_PAGES; $page++) {
			$res = $this->CI->chat_platform_bl->lazada_exec('/reverse/getreverseordersforseller', 'GET', array(
				'page_no' => (string)$page,
				'page_size' => (string)self::PAGE_SIZE,
				'ReverseOrderLineModifiedTimeRangeStart' => (string)$start,
				'ReverseOrderLineModifiedTimeRangeEnd' => (string)$end
			));
			if (empty($res['ok'])) {
				$out['ok'] = false;
				$out['error'] = isset($res['error']) ? $res['error'] : 'lazada_error';
				break;
			}
			$items = array();
			$data = isset($res['data']) ? $res['data'] : array();
			if (isset($data['result']['items']) && is_array($data['result']['items'])) {
				$items = $data['result']['items'];
			} elseif (isset($data['data']['items']) && is_array($data['data']['items'])) {
				$items = $data['data']['items'];
			}
			if (empty($items)) {
				break;
			}
			foreach ($items as $item) {
				$lines = array();
				if (isset($item['reverse_order_lines']) && is_array($item['reverse_order_lines'])) {
					$lines = $item['reverse_order_lines'];
				} elseif (isset($item['reverseOrderLineDTOList']) && is_array($item['reverseOrderLineDTOList'])) {
					$lines = $item['reverseOrderLineDTOList'];
				}
				if (empty($lines)) {
					$lines = array($item);
				}
				foreach ($lines as $line) {
					$out['fetched']++;
					$mapped = $this->map_lazada_line($item, $line);
					if ($this->upsert_lazada_return($mapped)) {
						$out['upserted']++;
					}
					if ($this->apply_lazada_flags($mapped)) {
						$out['flagged']++;
					}
				}
			}
			$total = 0;
			if (isset($data['result']['total'])) {
				$total = (int)$data['result']['total'];
			}
			if ($total > 0 && ($page * self::PAGE_SIZE) >= $total) {
				break;
			}
			if (count($items) < self::PAGE_SIZE) {
				break;
			}
		}
		$exist = $this->apply_lazada_existing();
		$out['flagged'] += isset($exist['flagged']) ? (int)$exist['flagged'] : 0;
		return $out;
	}

	function poll_tiktok($days)
	{
		$out = array('ok' => true, 'error' => '', 'fetched' => 0, 'upserted' => 0, 'flagged' => 0);
		$ret = $this->poll_tiktok_search('/return_refund/202309/returns/search', 'return', 'return_orders', $days);
		$can = $this->poll_tiktok_search('/return_refund/202309/cancellations/search', 'cancel', 'cancellations', $days);
		$out['fetched'] = $ret['fetched'] + $can['fetched'];
		$out['upserted'] = $ret['upserted'] + $can['upserted'];
		$out['flagged'] = $ret['flagged'] + $can['flagged'];
		if (!$ret['ok'] || !$can['ok']) {
			$out['ok'] = false;
			$parts = array();
			if ($ret['error'] !== '') {
				$parts[] = 'return:'.$ret['error'];
			}
			if ($can['error'] !== '') {
				$parts[] = 'cancel:'.$can['error'];
			}
			$out['error'] = implode('; ', $parts);
		}
		$exist = $this->apply_tiktok_existing();
		$out['flagged'] += isset($exist['flagged']) ? (int)$exist['flagged'] : 0;
		return $out;
	}

	function poll_tiktok_search($path, $record_type, $list_key, $days)
	{
		$out = array('ok' => true, 'error' => '', 'fetched' => 0, 'upserted' => 0, 'flagged' => 0);
		$to = time();
		$from = $to - ($days * 86400);
		$token = '';
		for ($page = 1; $page <= self::MAX_PAGES; $page++) {
			$query = array(
				'page_size' => self::PAGE_SIZE,
				'sort_field' => 'update_time',
				'sort_order' => 'DESC'
			);
			if ($token !== '') {
				$query['page_token'] = $token;
			}
			$body = array(
				'update_time_ge' => $from,
				'update_time_lt' => $to
			);
			$res = $this->CI->chat_platform_bl->tiktok_request('POST', $path, $query, $body);
			if (isset($res['_err'])) {
				$out['ok'] = false;
				$out['error'] = $res['_err'];
				break;
			}
			if (isset($res['code']) && (int)$res['code'] !== 0) {
				$out['ok'] = false;
				$out['error'] = isset($res['message']) ? (string)$res['message'] : 'tiktok_error';
				break;
			}
			$list = array();
			if (isset($res['data'][$list_key]) && is_array($res['data'][$list_key])) {
				$list = $res['data'][$list_key];
			} elseif (isset($res['data']['return_orders']) && is_array($res['data']['return_orders'])) {
				$list = $res['data']['return_orders'];
			} elseif (isset($res['data']['cancellation_list']) && is_array($res['data']['cancellation_list'])) {
				$list = $res['data']['cancellation_list'];
			}
			if (empty($list)) {
				break;
			}
			foreach ($list as $row) {
				$out['fetched']++;
				$mapped = $this->map_tiktok_row($row, $record_type);
				if ($this->upsert_tiktok_return($mapped)) {
					$out['upserted']++;
				}
				if ($this->apply_tiktok_flags($mapped)) {
					$out['flagged']++;
				}
			}
			$token = '';
			if (isset($res['data']['next_page_token'])) {
				$token = (string)$res['data']['next_page_token'];
			} elseif (isset($res['data']['page_token'])) {
				$token = (string)$res['data']['page_token'];
			}
			if ($token === '') {
				break;
			}
		}
		return $out;
	}

	function upsert_shopee_return($row)
	{
		$return_sn = isset($row['return_sn']) ? (string)$row['return_sn'] : '';
		$order_sn = isset($row['order_sn']) ? (string)$row['order_sn'] : '';
		if ($return_sn === '' || $order_sn === '') {
			return false;
		}
		$create = isset($row['create_time']) ? $row['create_time'] : 0;
		$update = isset($row['update_time']) ? $row['update_time'] : $create;
		$data = array(
			'return_sn' => $return_sn,
			'order_sn' => $order_sn,
			'reason' => isset($row['reason']) ? (string)$row['reason'] : '',
			'text_reason' => isset($row['text_reason']) ? (string)$row['text_reason'] : '',
			'refund_amount' => isset($row['refund_amount']) ? (float)$row['refund_amount'] : 0,
			'status' => isset($row['status']) ? (string)$row['status'] : '',
			'start_time' => $this->unix_to_dt($create),
			'end_time' => $this->unix_to_dt($update)
		);
		$exist = $this->CI->shopee_return_order_model->select_by_return_sn($return_sn);
		if (empty($exist)) {
			$this->CI->shopee_return_order_model->insert($data);
			return true;
		}
		$this->CI->shopee_return_order_model->update($data, $exist['shopee_return_order_id']);
		return true;
	}

	function apply_shopee_flags($order_sn, $status)
	{
		$order_sn = trim((string)$order_sn);
		if ($order_sn === '' || !$this->shopee_return_settled($status)) {
			return false;
		}
		if (!$this->shopee_was_sold($order_sn)) {
			return false;
		}
		$completed = $this->CI->shopee_orders_model->get_by_sn_status($order_sn, 'COMPLETED');
		if (empty($completed)) {
			return false;
		}
		if ($this->shopee_order_is_return($order_sn)) {
			return false;
		}
		$this->CI->shopee_orders_model->update_by_order_sn(array('is_return' => 1), $order_sn);
		return true;
	}

	function apply_shopee_existing()
	{
		$out = array('ok' => true, 'flagged' => 0, 'cleared' => 0);
		$rows = $this->CI->shopee_return_order_model->select_by_status_list(array('REFUND_PAID', 'COMPLETED'));
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$sn = isset($row['order_sn']) ? $row['order_sn'] : '';
				$st = isset($row['status']) ? $row['status'] : '';
				$did = $this->apply_shopee_flags($sn, $st);
				if ($did) {
					$out['flagged']++;
				}
				if ($did || $this->shopee_order_is_return($sn)) {
					if (!empty($row['shopee_return_order_id'])) {
						$this->CI->shopee_return_order_model->update(array('is_active' => 1), $row['shopee_return_order_id']);
					}
				}
			}
		}
		$out['cleared'] = $this->reconcile_shopee_is_return();
		$this->CI->db->query("
UPDATE shopee_return_order
SET is_active = 0
WHERE UPPER(status) NOT IN ('REFUND_PAID', 'COMPLETED')
	AND ISNULL(is_active, 0) = 1
");
		$this->CI->db->query("
UPDATE shopee_return_order
SET is_active = 1
WHERE UPPER(status) IN ('REFUND_PAID', 'COMPLETED')
");
		return $out;
	}

	function reconcile_shopee_is_return()
	{
		$sql = "
UPDATE o
SET o.is_return = 0
FROM shopee_orders o
WHERE ISNULL(o.is_return, 0) = 1
	AND NOT EXISTS (
		SELECT 1 FROM shopee_return_order r
		WHERE r.order_sn = o.order_sn
			AND UPPER(r.status) IN ('REFUND_PAID', 'COMPLETED')
	)
";
		$this->CI->db->query($sql);
		return (int)$this->CI->db->affected_rows();
	}

	function shopee_was_sold($order_sn)
	{
		$order_sn = trim((string)$order_sn);
		if ($order_sn === '') {
			return false;
		}
		$processed = $this->CI->shopee_orders_model->get_by_sn_status($order_sn, 'PROCESSED');
		if (!empty($processed)) {
			return true;
		}
		$q = $this->CI->db->query(
			"SELECT TOP 1 1 AS ok FROM Shopee_taxinvoiceid WHERE order_sn = ?",
			array($order_sn)
		);
		$row = $q ? $q->row_array() : array();
		return !empty($row);
	}

	function shopee_order_is_return($order_sn)
	{
		$all = $this->CI->shopee_orders_model->get_by_order_sn($order_sn);
		if (empty($all)) {
			return false;
		}
		foreach ($all as $row) {
			if (isset($row['is_return']) && (int)$row['is_return'] === 1) {
				return true;
			}
		}
		return false;
	}

	function shopee_return_settled($status)
	{
		$st = strtoupper(trim((string)$status));
		return in_array($st, array('REFUND_PAID', 'COMPLETED'), true);
	}

	function map_lazada_line($item, $line)
	{
		$rev = '';
		if (isset($item['reverse_order_id'])) {
			$rev = (string)$item['reverse_order_id'];
		} elseif (isset($line['reverse_order_id'])) {
			$rev = (string)$line['reverse_order_id'];
		}
		$line_id = '';
		if (isset($line['reverse_order_line_id'])) {
			$line_id = (string)$line['reverse_order_line_id'];
		}
		$order_number = '';
		if (isset($item['trade_order_id'])) {
			$order_number = (string)$item['trade_order_id'];
		} elseif (isset($line['trade_order_id'])) {
			$order_number = (string)$line['trade_order_id'];
		}
		$request_type = '';
		if (isset($item['request_type'])) {
			$request_type = (string)$item['request_type'];
		} elseif (isset($line['request_type'])) {
			$request_type = (string)$line['request_type'];
		}
		$status = '';
		if (isset($line['reverse_status'])) {
			$status = (string)$line['reverse_status'];
		} elseif (isset($item['reverse_status'])) {
			$status = (string)$item['reverse_status'];
		}
		$reason = '';
		if (isset($line['reason_text'])) {
			$reason = (string)$line['reason_text'];
		}
		$refund = 0;
		if (isset($line['refund_amount'])) {
			$refund = (float)$line['refund_amount'];
			if ($refund >= 1000 && abs($refund - round($refund)) < 0.001) {
				$refund = $refund / 100;
			}
		}
		$create = 0;
		if (isset($line['return_order_line_gmt_create'])) {
			$create = $line['return_order_line_gmt_create'];
		}
		$update = $create;
		if (isset($line['return_order_line_gmt_modified'])) {
			$update = $line['return_order_line_gmt_modified'];
		}
		return array(
			'reverse_order_id' => $rev,
			'reverse_order_line_id' => $line_id,
			'order_number' => $order_number,
			'request_type' => $request_type,
			'reverse_status' => $status,
			'refund_amount' => $refund,
			'reason' => $reason,
			'start_time' => $this->unix_to_dt($create),
			'end_time' => $this->unix_to_dt($update)
		);
	}

	function upsert_lazada_return($mapped)
	{
		if ($mapped['reverse_order_id'] === '' && $mapped['order_number'] === '') {
			return false;
		}
		$exist = $this->CI->lazada_return_order_model->select_by_reverse_line($mapped['reverse_order_id'], $mapped['reverse_order_line_id']);
		if (empty($exist)) {
			$this->CI->lazada_return_order_model->insert($mapped);
			return true;
		}
		$this->CI->lazada_return_order_model->update($mapped, $exist['lazada_return_order_id']);
		return true;
	}

	function apply_lazada_flags($mapped)
	{
		$sn = isset($mapped['order_number']) ? trim((string)$mapped['order_number']) : '';
		if ($sn === '' || !$this->lazada_reverse_settled(isset($mapped['reverse_status']) ? $mapped['reverse_status'] : '')) {
			return false;
		}
		$packed = $this->CI->lazada_orders_model->get_by_sn_status($sn, 'packed');
		if (empty($packed)) {
			return false;
		}
		if ($this->lazada_has_cn_death($sn)) {
			return true;
		}
		$death = $this->lazada_death_status(isset($mapped['request_type']) ? $mapped['request_type'] : '');
		$src = $packed[0];
		$data = $this->row_copy($src, array('OrderID', 'taxinvoiceID', 'keygen'));
		$data['status'] = $death;
		$data['updated_at'] = isset($mapped['end_time']) ? $mapped['end_time'] : date('Y-m-d H:i:s');
		$data['is_virtual_packed'] = 0;
		$data['order_make_cn'] = 1;
		$this->CI->lazada_orders_model->insert($data);
		return true;
	}

	function apply_lazada_existing()
	{
		$out = array('ok' => true, 'flagged' => 0);
		$rows = $this->CI->lazada_return_order_model->select_inactive(500);
		if (empty($rows)) {
			return $out;
		}
		foreach ($rows as $row) {
			if ($this->apply_lazada_flags($row)) {
				$out['flagged']++;
				if (!empty($row['lazada_return_order_id'])) {
					$this->CI->lazada_return_order_model->update(array('is_active' => 1), $row['lazada_return_order_id']);
				}
			}
		}
		return $out;
	}

	function lazada_reverse_settled($status)
	{
		$st = strtoupper(trim((string)$status));
		if ($st === '') {
			return false;
		}
		if (strpos($st, 'REJECT') !== false || strpos($st, 'INITIATE') !== false) {
			return false;
		}
		if ($st === 'REQUEST_CANCEL' || $st === 'REFUND_PENDING') {
			return false;
		}
		return (strpos($st, 'SUCCESS') !== false || strpos($st, 'COMPLETE') !== false);
	}

	function lazada_death_status($request_type)
	{
		$t = strtoupper(trim((string)$request_type));
		if ($t === 'CANCEL') {
			return 'canceled';
		}
		return 'returned';
	}

	function lazada_has_cn_death($order_number)
	{
		$deaths = array(
			'lost_by_3pl', 'damaged_by_3pl', 'shipped_back_success', 'shipped_back',
			'returned', 'failed_delivery', 'canceled'
		);
		return $this->CI->lazada_orders_model->has_status_in($order_number, $deaths);
	}

	function map_tiktok_row($row, $record_type)
	{
		$id = '';
		if ($record_type === 'cancel') {
			if (isset($row['cancel_id'])) {
				$id = (string)$row['cancel_id'];
			}
		} else {
			if (isset($row['return_id'])) {
				$id = (string)$row['return_id'];
			}
		}
		$order_id = '';
		if (isset($row['order_id'])) {
			$order_id = (string)$row['order_id'];
		}
		$status = '';
		if ($record_type === 'cancel' && isset($row['cancel_status'])) {
			$status = (string)$row['cancel_status'];
		} elseif (isset($row['return_status'])) {
			$status = (string)$row['return_status'];
		} elseif (isset($row['status'])) {
			$status = (string)$row['status'];
		}
		$reason = '';
		if (isset($row['return_reason'])) {
			$reason = (string)$row['return_reason'];
		} elseif (isset($row['cancel_reason'])) {
			$reason = (string)$row['cancel_reason'];
		} elseif (isset($row['reason'])) {
			$reason = (string)$row['reason'];
		}
		$refund = 0;
		if (isset($row['refund_amount']['refund_total'])) {
			$refund = (float)$row['refund_amount']['refund_total'];
		} elseif (isset($row['refund_amount']) && !is_array($row['refund_amount'])) {
			$refund = (float)$row['refund_amount'];
		}
		$create = 0;
		if (isset($row['create_time'])) {
			$create = $row['create_time'];
		}
		$update = $create;
		if (isset($row['update_time'])) {
			$update = $row['update_time'];
		}
		return array(
			'return_id' => $id,
			'order_id' => $order_id,
			'record_type' => $record_type,
			'status' => $status,
			'refund_amount' => $refund,
			'reason' => $reason,
			'start_time' => $this->unix_to_dt($create),
			'end_time' => $this->unix_to_dt($update)
		);
	}

	function upsert_tiktok_return($mapped)
	{
		if ($mapped['return_id'] === '' && $mapped['order_id'] === '') {
			return false;
		}
		$exist = $this->CI->tiktok_return_order_model->select_by_return_id($mapped['return_id'], $mapped['record_type']);
		if (empty($exist)) {
			$this->CI->tiktok_return_order_model->insert($mapped);
			return true;
		}
		$this->CI->tiktok_return_order_model->update($mapped, $exist['tiktok_return_order_id']);
		return true;
	}

	function apply_tiktok_flags($mapped)
	{
		$oid = isset($mapped['order_id']) ? trim((string)$mapped['order_id']) : '';
		$st = isset($mapped['status']) ? $mapped['status'] : '';
		$rtype = isset($mapped['record_type']) ? strtolower(trim((string)$mapped['record_type'])) : '';
		if ($oid === '') {
			return false;
		}
		$path_a = $this->tiktok_refund_settled($st);
		$path_b = ($rtype === 'cancel' && $this->tiktok_cancel_settled($st));
		if (!$path_a && !$path_b) {
			return false;
		}
		$packet = $this->CI->tiktok_orders_model->get_by_id_status($oid, 'Packet');
		if (empty($packet)) {
			return false;
		}
		$cancelled = $this->CI->tiktok_orders_model->get_by_id_status($oid, 'CANCELLED');
		$canceled = $this->CI->tiktok_orders_model->get_by_id_status($oid, 'Canceled');
		$event_at = isset($mapped['end_time']) ? $mapped['end_time'] : date('Y-m-d H:i:s');
		if (!empty($cancelled) || !empty($canceled)) {
			$row = !empty($cancelled) ? $cancelled[0] : $canceled[0];
			$id = isset($row['tiktok_orders_id']) ? $row['tiktok_orders_id'] : 0;
			if ($id) {
				$this->CI->tiktok_orders_model->update(array('update_time' => $event_at), $id);
			}
			return true;
		}
		$src = $packet[0];
		$all = $this->CI->tiktok_orders_model->get_by_order_id($oid);
		$tracking = isset($src['tracking_number']) ? (string)$src['tracking_number'] : '';
		if ($tracking === '' && !empty($all)) {
			foreach ($all as $row) {
				if (!empty($row['tracking_number'])) {
					$tracking = (string)$row['tracking_number'];
					break;
				}
			}
		}
		$data = $this->row_copy($src, array('tiktok_orders_id'));
		$data['status'] = 'CANCELLED';
		$data['tracking_number'] = $tracking;
		$data['update_time'] = isset($mapped['end_time']) ? $mapped['end_time'] : date('Y-m-d H:i:s');
		if (isset($data['is_virtual_packed'])) {
			$data['is_virtual_packed'] = 0;
		}
		$this->CI->tiktok_orders_model->insert($data);
		return true;
	}

	function apply_tiktok_existing()
	{
		$out = array('ok' => true, 'flagged' => 0);
		$rows = $this->CI->tiktok_return_order_model->select_inactive(500);
		if (empty($rows)) {
			return $out;
		}
		foreach ($rows as $row) {
			if ($this->apply_tiktok_flags($row)) {
				$out['flagged']++;
				if (!empty($row['tiktok_return_order_id'])) {
					$this->CI->tiktok_return_order_model->update(array('is_active' => 1), $row['tiktok_return_order_id']);
				}
			}
		}
		return $out;
	}

	function tiktok_refund_settled($status)
	{
		return (strtoupper(trim((string)$status)) === 'REFUND_COMPLETE');
	}

	function tiktok_cancel_settled($status)
	{
		return (strtoupper(trim((string)$status)) === 'CANCELLATION_REQUEST_COMPLETE');
	}

	function tiktok_return_settled($status)
	{
		return $this->tiktok_refund_settled($status) || $this->tiktok_cancel_settled($status);
	}

	function unix_to_dt($ts)
	{
		$n = (int)$ts;
		if ($n <= 0) {
			return date('Y-m-d H:i:s');
		}
		if ($n > 20000000000) {
			$n = (int)floor($n / 1000);
		}
		return date('Y-m-d H:i:s', $n);
	}

	function sql_dt($v)
	{
		if ($v === null || $v === '') {
			return null;
		}
		if (is_object($v) && method_exists($v, 'format')) {
			return $v->format('Y-m-d H:i:s');
		}
		$t = strtotime((string)$v);
		return $t ? date('Y-m-d H:i:s', $t) : (string)$v;
	}

	function row_copy($row, $skip)
	{
		$out = array();
		if (empty($row) || !is_array($row)) {
			return $out;
		}
		foreach ($row as $k => $v) {
			if (is_int($k)) {
				continue;
			}
			if (in_array($k, $skip, true)) {
				continue;
			}
			if (is_object($v) && method_exists($v, 'format')) {
				$out[$k] = $v->format('Y-m-d H:i:s');
			} else {
				$out[$k] = $v;
			}
		}
		return $out;
	}
}
