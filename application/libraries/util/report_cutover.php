<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Accounting report cutover: periods on or before this date use legacy
 * screening + money rules (as of ~mid-July 2026 / pre Aug rewrite).
 * Periods after this date use current rules.
 *
 * Cutover end-of-day: 2026-06-30 (inclusive = legacy).
 */
class Report_cutover
{
	const CUTOVER_YMD = '2026-06-30';

	/** @var bool|null */
	protected $legacy = null;

	/** @var string */
	protected $start_ymd = '';

	/** @var string */
	protected $end_ymd = '';

	function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * @param string $startDate Y-m-d or datetime
	 * @param string $endDate   Y-m-d or datetime
	 */
	function set_range($startDate, $endDate)
	{
		$this->start_ymd = $this->to_ymd($startDate);
		$this->end_ymd = $this->to_ymd($endDate);
		// Legacy when the report window ends on/before cutover (typical: whole month ≤ June).
		$this->legacy = ($this->end_ymd !== '' && $this->end_ymd <= self::CUTOVER_YMD);
		return $this->legacy;
	}

	/**
	 * Infer from sale/CN row dates (e.g. invoice-range "more" pages).
	 * Uses max transaction/updated date in the set.
	 */
	function set_from_rows($rows, $date_key = 'transactiondate')
	{
		$max = '';
		$min = '';
		if (!empty($rows) && is_array($rows)) {
			foreach ($rows as $row) {
				if (!is_array($row) || !isset($row[$date_key])) {
					continue;
				}
				$ymd = $this->to_ymd($row[$date_key]);
				if ($ymd === '') {
					continue;
				}
				if ($min === '' || $ymd < $min) {
					$min = $ymd;
				}
				if ($max === '' || $ymd > $max) {
					$max = $ymd;
				}
			}
		}
		if ($max === '') {
			$this->legacy = false;
			$this->start_ymd = '';
			$this->end_ymd = '';
			return false;
		}
		return $this->set_range($min !== '' ? $min : $max, $max);
	}

	function use_legacy()
	{
		return $this->legacy === true;
	}

	/** True once set_range / set_from_rows has been called. */
	function is_set()
	{
		return $this->legacy !== null;
	}

	function cutover_ymd()
	{
		return self::CUTOVER_YMD;
	}

	function range_info()
	{
		return array(
			'legacy' => $this->use_legacy(),
			'cutover' => self::CUTOVER_YMD,
			'start' => $this->start_ymd,
			'end' => $this->end_ymd,
			'mode' => $this->use_legacy() ? 'legacy' : 'current',
		);
	}

	function to_ymd($value)
	{
		if ($value instanceof DateTime) {
			return $value->format('Y-m-d');
		}
		$text = trim((string)$value);
		if ($text === '') {
			return '';
		}
		if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $text, $m)) {
			return $m[1];
		}
		$ts = strtotime($text);
		if ($ts) {
			return date('Y-m-d', $ts);
		}
		return '';
	}

	/**
	 * Remap TikTok sale SP rows to pre-fix money fields:
	 * price = total_amount, priceVATincluded = total_amount - (platform + seller).
	 */
	function remap_tiktok_sale_rows_legacy($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return $rows;
		}
		$ids = array();
		foreach ($rows as $row) {
			$oid = '';
			if (!empty($row['order_sn'])) {
				$oid = (string)$row['order_sn'];
			} elseif (!empty($row['order_id'])) {
				$oid = (string)$row['order_id'];
			}
			if ($oid !== '') {
				$ids[$oid] = true;
			}
		}
		$pay_map = array();
		if (!empty($ids)) {
			$chunks = array_chunk(array_keys($ids), 400);
			foreach ($chunks as $chunk) {
				$this->CI->db->select('order_id, total_amount, original_total_product_price, seller_discount, platform_discount, shipping_fee');
				$this->CI->db->from('tiktok_order_payment');
				$this->CI->db->where_in('order_id', $chunk);
				$q = $this->CI->db->get();
				if ($q) {
					foreach ($q->result_array() as $p) {
						$pay_map[(string)$p['order_id']] = $p;
					}
				}
			}
		}
		$out = array();
		foreach ($rows as $row) {
			$oid = '';
			if (!empty($row['order_sn'])) {
				$oid = (string)$row['order_sn'];
			} elseif (!empty($row['order_id'])) {
				$oid = (string)$row['order_id'];
			}
			$pay = ($oid !== '' && isset($pay_map[$oid])) ? $pay_map[$oid] : null;
			$total = $pay ? floatval($pay['total_amount']) : (isset($row['price']) ? floatval($row['price']) : 0);
			$platform = isset($row['voucher_platform']) ? floatval($row['voucher_platform']) : ($pay ? floatval($pay['platform_discount']) : 0);
			$seller = isset($row['voucher_seller']) ? floatval($row['voucher_seller']) : (isset($row['seller_discount']) ? floatval($row['seller_discount']) : ($pay ? floatval($pay['seller_discount']) : 0));
			$incl = $total - ($platform + $seller);
			$before = round($incl / 1.07, 2);
			$row['price'] = $total;
			$row['priceVATincluded'] = $incl;
			$row['priceBeforeVAT'] = $before;
			$row['VAT'] = round($incl - $before, 2);
			$out[] = $row;
		}
		return $out;
	}
}
