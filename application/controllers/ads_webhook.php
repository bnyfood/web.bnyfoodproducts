<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ads_webhook extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('platform_ads_spend_model');
	}

	public function ping()
	{
		$this->json_out(array(
			'ok' => true,
			'service' => 'ads_webhook',
			'ingest' => base_url().'ads_webhook/ingest'
		));
	}

	public function ingest()
	{
		$secret = defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '';
		$given = $this->input->get_request_header('X-BNY-Ads-Secret', true);
		if ($given === false || $given === null || $given === '') {
			$given = $this->input->get_request_header('X-Webhook-Secret', true);
		}
		if ($secret === '' || !hash_equals((string)$secret, (string)$given)) {
			$this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
			return;
		}

		$raw = file_get_contents('php://input');
		$payload = json_decode($raw, true);
		if (!is_array($payload)) {
			$payload = $this->input->post();
		}
		if (!is_array($payload) || empty($payload)) {
			$this->json_out(array('ok' => false, 'error' => 'empty_body'), 400);
			return;
		}

		$rows = $this->normalize_rows($payload);
		if (empty($rows)) {
			$this->json_out(array('ok' => false, 'error' => 'no_spend_rows'), 400);
			return;
		}

		$ids = array();
		foreach ($rows as $row) {
			$row['payload'] = $raw;
			$row['source'] = 'webhook';
			$ids[] = $this->platform_ads_spend_model->upsert_day($row);
		}

		$this->json_out(array(
			'ok' => true,
			'saved' => count($ids),
			'ids' => $ids
		));
	}

	private function normalize_rows($payload)
	{
		$rows = array();
		if (isset($payload[0]) && is_array($payload[0])) {
			foreach ($payload as $item) {
				$row = $this->normalize_one($item);
				if ($row) {
					$rows[] = $row;
				}
			}
			return $rows;
		}
		if (!empty($payload['data']) && is_array($payload['data']) && isset($payload['data'][0])) {
			foreach ($payload['data'] as $item) {
				$row = $this->normalize_one($item);
				if ($row) {
					$rows[] = $row;
				}
			}
			if (!empty($rows)) {
				return $rows;
			}
		}
		$one = $this->normalize_one($payload);
		if ($one) {
			$rows[] = $one;
		}
		return $rows;
	}

	private function normalize_one($item)
	{
		if (!is_array($item)) {
			return null;
		}
		$platform = strtolower(trim((string)(
			(isset($item['platform']) ? $item['platform'] : '')
			?: (isset($item['channel']) ? $item['channel'] : '')
			?: (isset($item['shop_type']) ? $item['shop_type'] : '')
		)));
		if ($platform === 'tt' || $platform === 'tiktokshop') {
			$platform = 'tiktok';
		}
		if ($platform === 'laz') {
			$platform = 'lazada';
		}
		if ($platform === 'shp' || $platform === 'shopee_shop') {
			$platform = 'shopee';
		}
		if (!in_array($platform, array('lazada', 'shopee', 'tiktok'), true)) {
			return null;
		}
		$date = '';
		foreach (array('spend_date', 'date', 'stat_date', 'report_date', 'day') as $k) {
			if (!empty($item[$k])) {
				$date = substr((string)$item[$k], 0, 10);
				break;
			}
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			return null;
		}
		$spend = 0;
		foreach (array('spend', 'ads_spend', 'cost', 'spend_amount', 'expense') as $k) {
			if (isset($item[$k]) && $item[$k] !== '') {
				$spend = (float)$item[$k];
				break;
			}
		}
		return array(
			'platform' => $platform,
			'spend_date' => $date,
			'spend' => $spend,
			'impressions' => isset($item['impressions']) ? $item['impressions'] : null,
			'clicks' => isset($item['clicks']) ? $item['clicks'] : null,
			'conversions' => isset($item['conversions']) ? $item['conversions'] : (isset($item['orders']) ? $item['orders'] : null),
			'campaign_id' => isset($item['campaign_id']) ? $item['campaign_id'] : '',
			'campaign_name' => isset($item['campaign_name']) ? $item['campaign_name'] : null,
			'currency' => isset($item['currency']) ? $item['currency'] : 'THB'
		);
	}

	private function json_out($data, $code = 200)
	{
		$this->output->set_status_header($code);
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data));
	}
}
