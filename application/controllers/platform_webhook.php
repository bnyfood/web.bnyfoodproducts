<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Platform_webhook extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('platform_webhook_event_model');
	}

	public function ping()
	{
		$this->json_out(array(
			'ok' => true,
			'service' => 'platform_webhook',
			'lazada' => base_url().'platform_webhook/lazada',
			'shopee' => base_url().'platform_webhook/shopee',
			'tiktok' => base_url().'platform_webhook/tiktok'
		));
	}

	public function index()
	{
		if (strtoupper((string)$this->input->server('REQUEST_METHOD')) === 'GET') {
			$this->ping();
			return;
		}
		$this->ingest();
	}

	public function lazada()
	{
		$this->receive('lazada');
	}

	public function shopee()
	{
		$this->receive('shopee');
	}

	public function tiktok()
	{
		$this->receive('tiktok');
	}

	public function ingest()
	{
		$this->receive('auto');
	}

	private function receive($platform_hint)
	{
		if (strtoupper((string)$this->input->server('REQUEST_METHOD')) === 'GET') {
			$this->ping();
			return;
		}

		$raw = file_get_contents('php://input');
		$raw = is_string($raw) ? $raw : '';
		$payload = json_decode($raw, true);
		if (!is_array($payload)) {
			$payload = $this->input->post();
			if (!is_array($payload)) {
				$payload = array();
			}
		}

		if ($raw === '' && empty($payload)) {
			$this->json_out(array('ok' => true, 'accepted' => 'empty'));
			return;
		}

		$auth = (string)$this->header_first(array('Authorization', 'X-BNY-Ads-Secret', 'X-Webhook-Secret'));
		$bny_ok = $this->bny_secret_ok($auth);
		$platform = $this->detect_platform($platform_hint, $payload);
		$verified = $bny_ok || $this->verify_platform($platform, $raw, $auth);

		if (!$verified && !$this->looks_like_platform($platform, $payload)) {
			$this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
			return;
		}

		$meta = $this->extract_meta($platform, $payload);
		$id = $this->platform_webhook_event_model->insert_event(array(
			'platform' => $platform,
			'event_code' => $meta['event_code'],
			'shop_id' => $meta['shop_id'],
			'verified' => $verified ? 1 : 0,
			'remote_ip' => $this->input->ip_address(),
			'headers' => json_encode($this->safe_headers()),
			'payload' => ($raw !== '') ? $raw : json_encode($payload)
		));

		if ($platform === 'lazada' || $platform === 'shopee' || $platform === 'tiktok') {
			try {
				$this->load->library('businesslogic/chat_platform_bl');
				$this->chat_platform_bl->ingest_push($platform, $payload);
			} catch (Exception $e) {
			}
		}

		$this->json_out(array(
			'ok' => true,
			'id' => $id,
			'platform' => $platform,
			'event_code' => $meta['event_code'],
			'verified' => $verified ? 1 : 0
		));
	}

	private function detect_platform($hint, $payload)
	{
		if ($hint === 'lazada' || $hint === 'shopee' || $hint === 'tiktok') {
			return $hint;
		}
		if (isset($payload['code']) && (isset($payload['shop_id']) || isset($payload['shopid']))) {
			return 'shopee';
		}
		if (isset($payload['type']) && isset($payload['shop_id'])) {
			return 'tiktok';
		}
		if (isset($payload['seller_id']) || isset($payload['message_type']) || isset($payload['msg_type'])) {
			return 'lazada';
		}
		$p = strtolower(trim((string)(
			(isset($payload['platform']) ? $payload['platform'] : '')
			?: (isset($payload['channel']) ? $payload['channel'] : '')
		)));
		if ($p === 'laz') {
			$p = 'lazada';
		}
		if ($p === 'tt' || $p === 'tiktokshop') {
			$p = 'tiktok';
		}
		if ($p === 'lazada' || $p === 'shopee' || $p === 'tiktok') {
			return $p;
		}
		return 'unknown';
	}

	private function looks_like_platform($platform, $payload)
	{
		if ($platform === 'shopee') {
			return isset($payload['code']);
		}
		if ($platform === 'tiktok') {
			return isset($payload['type']) || isset($payload['shop_id']);
		}
		if ($platform === 'lazada') {
			return isset($payload['seller_id']) || isset($payload['message_type']) || isset($payload['data']);
		}
		return false;
	}

	private function extract_meta($platform, $payload)
	{
		$code = '';
		$shop = '';
		if ($platform === 'shopee') {
			$code = isset($payload['code']) ? $payload['code'] : '';
			$shop = isset($payload['shop_id']) ? $payload['shop_id'] : (isset($payload['shopid']) ? $payload['shopid'] : '');
		} elseif ($platform === 'tiktok') {
			$code = isset($payload['type']) ? $payload['type'] : '';
			$shop = isset($payload['shop_id']) ? $payload['shop_id'] : '';
		} elseif ($platform === 'lazada') {
			$code = isset($payload['message_type']) ? $payload['message_type'] : (isset($payload['msg_type']) ? $payload['msg_type'] : '');
			$shop = isset($payload['seller_id']) ? $payload['seller_id'] : (isset($payload['user_id']) ? $payload['user_id'] : '');
		}
		if ($code === '' && isset($payload['event'])) {
			$code = $payload['event'];
		}
		return array(
			'event_code' => (string)$code,
			'shop_id' => (string)$shop
		);
	}

	private function bny_secret_ok($given)
	{
		$secret = defined('ADS_WEBHOOK_SECRET') ? ADS_WEBHOOK_SECRET : '';
		if ($secret === '' || $given === '') {
			return false;
		}
		$plain = $given;
		if (stripos($given, 'Bearer ') === 0) {
			$plain = trim(substr($given, 7));
		}
		return hash_equals((string)$secret, (string)$plain);
	}

	private function verify_platform($platform, $raw, $auth)
	{
		$sig = $this->normalize_sig($auth);
		if ($sig === '') {
			return false;
		}
		if ($platform === 'shopee') {
			$key = defined('SHOPEE_PATNERKEY') ? SHOPEE_PATNERKEY : '';
			return $this->hmac_match($raw, $key, $sig);
		}
		if ($platform === 'tiktok') {
			$key = defined('TIKTOK_KEY') ? TIKTOK_KEY : '';
			$secret = defined('TIKTOK_SECRET') ? TIKTOK_SECRET : '';
			$calc = hash_hmac('sha256', $key.$raw, $secret);
			return hash_equals($calc, $sig);
		}
		if ($platform === 'lazada') {
			$secret = (string)$this->config->item('Secret');
			return $this->hmac_match($raw, $secret, $sig);
		}
		return false;
	}

	private function hmac_match($raw, $key, $sig)
	{
		if ($key === '' || $sig === '') {
			return false;
		}
		$candidates = array(
			hash_hmac('sha256', $raw, $key),
			hash_hmac('sha256', $key.$raw, $key)
		);
		foreach ($candidates as $calc) {
			if (hash_equals($calc, $sig)) {
				return true;
			}
		}
		return false;
	}

	private function normalize_sig($auth)
	{
		$auth = trim((string)$auth);
		if ($auth === '') {
			return '';
		}
		if (stripos($auth, 'Bearer ') === 0) {
			$auth = trim(substr($auth, 7));
		}
		if (stripos($auth, 'sha256=') === 0) {
			$auth = trim(substr($auth, 7));
		}
		return strtolower($auth);
	}

	private function safe_headers()
	{
		$keep = array('Authorization', 'Content-Type', 'User-Agent', 'X-BNY-Ads-Secret', 'X-Webhook-Secret');
		$out = array();
		foreach ($keep as $name) {
			$v = $this->input->get_request_header($name, true);
			if ($v !== false && $v !== null && $v !== '') {
				$out[$name] = $v;
			}
		}
		return $out;
	}

	private function header_first($names)
	{
		foreach ($names as $name) {
			$v = $this->input->get_request_header($name, true);
			if ($v !== false && $v !== null && $v !== '') {
				return $v;
			}
		}
		return '';
	}

	private function json_out($data, $code = 200)
	{
		$this->output->set_status_header($code);
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data));
	}
}
