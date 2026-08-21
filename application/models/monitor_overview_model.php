<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitor_overview_model extends CI_Model
{
	function sales_by_date($start_date, $end_date)
	{
		$out = array(
			'lazada' => $this->rows_to_map($this->lazada_sales($start_date, $end_date)),
			'shopee' => $this->rows_to_map($this->shopee_sales($start_date, $end_date)),
			'tiktok' => $this->rows_to_map($this->tiktok_sales($start_date, $end_date))
		);
		return $out;
	}

	function rows_to_map($rows)
	{
		$map = array();
		if (empty($rows)) {
			return $map;
		}
		foreach ($rows as $row) {
			$d = isset($row['d']) ? substr((string)$row['d'], 0, 10) : '';
			if ($d === '') {
				continue;
			}
			$map[$d] = (float)$row['amt'];
		}
		return $map;
	}

	function lazada_sales($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), created_at, 23) AS d,
				SUM(CAST(ISNULL(price,0) AS float) + CAST(ISNULL(shipping_fee,0) AS float) - CAST(ISNULL(voucher,0) AS float)) AS amt
			FROM (
				SELECT order_number,
					MAX(price) AS price,
					MAX(shipping_fee) AS shipping_fee,
					MAX(voucher) AS voucher,
					MIN(created_at) AS created_at
				FROM lazada_orders
				WHERE created_at >= ? AND created_at < DATEADD(day, 1, CAST(? AS date))
				GROUP BY order_number
				HAVING SUM(CASE WHEN LOWER(ISNULL(status,'')) IN ('canceled','unpaid','pending') THEN 1 ELSE 0 END) = 0
			) x
			GROUP BY CONVERT(varchar(10), created_at, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function shopee_sales($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), create_time, 23) AS d,
				SUM(CAST(ISNULL(total_amount,0) AS float)) AS amt
			FROM (
				SELECT order_sn,
					MAX(total_amount) AS total_amount,
					MIN(create_time) AS create_time
				FROM shopee_orders
				WHERE create_time >= ? AND create_time < DATEADD(day, 1, CAST(? AS date))
				GROUP BY order_sn
				HAVING SUM(CASE WHEN UPPER(ISNULL(order_status,'')) IN ('CANCELLED','IN_CANCEL','UNPAID','INVALID') THEN 1 ELSE 0 END) = 0
			) x
			GROUP BY CONVERT(varchar(10), create_time, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function tiktok_sales($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), o.create_time, 23) AS d,
				SUM(CAST(ISNULL(p.total_amount,0) AS float)) AS amt
			FROM (
				SELECT order_id, MIN(create_time) AS create_time
				FROM tiktok_orders
				WHERE create_time >= ? AND create_time < DATEADD(day, 1, CAST(? AS date))
				GROUP BY order_id
				HAVING SUM(CASE WHEN UPPER(ISNULL(status,'')) IN ('CANCELLED','UNPAID') THEN 1 ELSE 0 END) = 0
			) o
			LEFT JOIN (
				SELECT order_id, MAX(CAST(ISNULL(total_amount,0) AS float)) AS total_amount
				FROM tiktok_order_payment
				GROUP BY order_id
			) p ON p.order_id = o.order_id
			GROUP BY CONVERT(varchar(10), o.create_time, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function fees_by_date($start_date, $end_date)
	{
		return array(
			'lazada' => $this->rows_to_map($this->lazada_fees($start_date, $end_date)),
			'shopee' => $this->rows_to_map($this->shopee_fees($start_date, $end_date)),
			'tiktok' => $this->rows_to_map($this->tiktok_fees($start_date, $end_date))
		);
	}

	function lazada_fees($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), transaction_date, 23) AS d,
				SUM(ABS(CAST(ISNULL(amount,0) AS float))) AS amt
			FROM lazada_finance_transaction_details
			WHERE transaction_date >= ? AND transaction_date < DATEADD(day, 1, CAST(? AS date))
				AND LOWER(ISNULL(fee_name,'')) NOT LIKE '%item price%'
				AND LOWER(ISNULL(fee_name,'')) NOT LIKE '%sponsored%'
				AND LOWER(ISNULL(fee_name,'')) NOT LIKE '%affiliate%'
				AND (
					LOWER(ISNULL(fee_name,'')) LIKE '%commission%'
					OR LOWER(ISNULL(fee_name,'')) LIKE '%fee%'
					OR LOWER(ISNULL(transaction_type,'')) LIKE '%fee%'
				)
			GROUP BY CONVERT(varchar(10), transaction_date, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function shopee_fees($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), o.create_time, 23) AS d, SUM(i.fee) AS amt
			FROM (
				SELECT order_sn, MIN(create_time) AS create_time
				FROM shopee_orders
				WHERE create_time >= ? AND create_time < DATEADD(day, 1, CAST(? AS date))
				GROUP BY order_sn
				HAVING SUM(CASE WHEN UPPER(ISNULL(order_status,'')) IN ('CANCELLED','IN_CANCEL','UNPAID','INVALID') THEN 1 ELSE 0 END) = 0
			) o
			INNER JOIN (
				SELECT order_sn,
					MAX(
						ABS(CAST(ISNULL(commission_fee,0) AS float))
						+ ABS(CAST(ISNULL(service_fee,0) AS float))
						+ ABS(CAST(ISNULL(seller_transaction_fee,0) AS float))
					) AS fee
				FROM shopee_escrow_order_income
				GROUP BY order_sn
			) i ON i.order_sn = o.order_sn
			GROUP BY CONVERT(varchar(10), o.create_time, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function tiktok_fees($start_date, $end_date)
	{
		$sql = "SELECT CONVERT(varchar(10), o.create_time, 23) AS d, SUM(CAST(ISNULL(p.fee,0) AS float)) AS amt
			FROM (
				SELECT order_id, MIN(create_time) AS create_time
				FROM tiktok_orders
				WHERE create_time >= ? AND create_time < DATEADD(day, 1, CAST(? AS date))
				GROUP BY order_id
				HAVING SUM(CASE WHEN UPPER(ISNULL(status,'')) IN ('CANCELLED','UNPAID') THEN 1 ELSE 0 END) = 0
			) o
			LEFT JOIN (
				SELECT order_id,
					MAX(ABS(CAST(ISNULL(small_order_fee,0) AS float))) AS fee
				FROM tiktok_order_payment
				GROUP BY order_id
			) p ON p.order_id = o.order_id
			GROUP BY CONVERT(varchar(10), o.create_time, 23)
			ORDER BY d";
		return $this->safe_rows($sql, array($start_date, $end_date));
	}

	function safe_rows($sql, $binds)
	{
		$debug = $this->db->db_debug;
		$this->db->db_debug = false;
		$q = $this->db->query($sql, $binds);
		$this->db->db_debug = $debug;
		if (!$q) {
			return array();
		}
		return $q->result_array();
	}
}
