<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Platform_token_bl
{
	const SHOPEE_REFRESH_SEC = 1800;
	const LAZADA_REFRESH_DAYS = 10;
	const TIKTOK_REFRESH_SEC = 1800;
	const MIN_HIT_SEC = 900;
	const FAIL_COOLDOWN_SEC = 1800;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('shopee_token_model');
		$this->CI->load->model('laztoken_model');
		$this->CI->load->model('tiktok_token_model');
	}

	function snapshot()
	{
		$tokens = array(
			'shopee' => $this->shopee_snap(),
			'lazada' => $this->lazada_snap(),
			'tiktok' => $this->tiktok_snap()
		);
		$probe = $this->last_probe();
		$saved = isset($probe['tokens']) && is_array($probe['tokens']) ? $probe['tokens'] : array();
		$at = isset($probe['checked_at']) ? $probe['checked_at'] : '';
		foreach ($tokens as $platform => $row) {
			$p = isset($saved[$platform]) && is_array($saved[$platform]) ? $saved[$platform] : array();
			if (array_key_exists('api_ok', $p)) {
				$tokens[$platform]['api_ok'] = !empty($p['api_ok']);
				$tokens[$platform]['api_error'] = isset($p['api_error']) ? (string)$p['api_error'] : '';
				$tokens[$platform]['api_at'] = $at;
			}
		}
		return $tokens;
	}

	function sql_snapshot()
	{
		return array(
			'shopee' => $this->shopee_snap(),
			'lazada' => $this->lazada_snap(),
			'tiktok' => $this->tiktok_snap()
		);
	}

	function refresh_due($only = '')
	{
		$only = strtolower(trim((string)$only));
		$all = $this->sql_snapshot();
		$out = array();
		foreach ($all as $platform => $snap) {
			if ($only !== '' && $only !== $platform) {
				continue;
			}
			$row = $this->refresh_one($platform, $snap);
			$out[$platform] = $this->probe_and_repair($platform, $row);
		}
		$this->write_probe($out);
		return $out;
	}

	function refresh_one($platform, $snap = null)
	{
		if ($snap === null) {
			$all = $this->sql_snapshot();
			$snap = isset($all[$platform]) ? $all[$platform] : array('ok' => false, 'error' => 'unknown');
		}
		if (empty($snap['ok'])) {
			$snap['action'] = 'skip';
			$snap['reason'] = isset($snap['error']) ? $snap['error'] : 'missing_token';
			return $snap;
		}
		if (empty($snap['due'])) {
			$snap['action'] = 'skip';
			$snap['reason'] = 'still_valid';
			$snap['hit_platform'] = 0;
			return $snap;
		}
		$cool = $this->hit_block($platform);
		if ($cool !== '') {
			$snap['action'] = 'skip';
			$snap['reason'] = $cool;
			$snap['hit_platform'] = 0;
			return $snap;
		}
		$ok = false;
		if ($platform === 'shopee') {
			$this->CI->load->library('businesslogic/shopee_bl');
			$ok = $this->CI->shopee_bl->ensure_access_token(true);
		} elseif ($platform === 'lazada') {
			$this->CI->load->library('businesslogic/chat_platform_bl');
			$ok = $this->CI->chat_platform_bl->ensure_lazada_token();
		} elseif ($platform === 'tiktok') {
			$ok = $this->tiktok_refresh();
		}
		$this->remember_hit($platform, $ok);
		$after = $this->sql_snapshot();
		$fresh = isset($after[$platform]) ? $after[$platform] : $snap;
		$fresh['action'] = $ok ? 'refreshed' : 'failed';
		$fresh['hit_platform'] = 1;
		$fresh['ok'] = $ok ? true : false;
		if (!$ok) {
			$fresh['reason'] = 'refresh_failed_reauth_needed';
		}
		return $fresh;
	}

	function shopee_snap()
	{
		$row = $this->CI->shopee_token_model->getlatesttoken();
		if (empty($row) || empty($row['refreshtoken']) || $row['refreshtoken'] === '0') {
			return array('ok' => false, 'error' => 'no_token', 'due' => false);
		}
		$left = isset($row['left_time']) ? (int)$row['left_time'] : 0;
		return array(
			'ok' => true,
			'shop_id' => isset($row['shopid']) ? $row['shopid'] : '',
			'left_sec' => $left,
			'due' => ($left <= self::SHOPEE_REFRESH_SEC),
			'threshold_sec' => self::SHOPEE_REFRESH_SEC
		);
	}

	function lazada_snap()
	{
		$row = $this->CI->laztoken_model->getlatesttoken();
		if (empty($row) || empty($row->refreshtoken) || $row->refreshtoken === '0') {
			return array('ok' => false, 'error' => 'no_token', 'due' => false);
		}
		$life = isset($row->litetime) ? (int)$row->litetime : 0;
		return array(
			'ok' => true,
			'left_days' => $life,
			'due' => ($life < self::LAZADA_REFRESH_DAYS),
			'threshold_days' => self::LAZADA_REFRESH_DAYS
		);
	}

	function tiktok_snap()
	{
		$row = $this->CI->tiktok_token_model->select_lasted_token();
		if (empty($row) || empty($row['refresh_token'])) {
			return array('ok' => false, 'error' => 'no_token', 'due' => false);
		}
		$left = $this->left_sec_from_dt(isset($row['access_token_expire_in']) ? $row['access_token_expire_in'] : '');
		$refresh_left = $this->left_sec_from_dt(isset($row['refresh_token_expire_in']) ? $row['refresh_token_expire_in'] : '');
		return array(
			'ok' => true,
			'left_sec' => $left,
			'refresh_left_sec' => $refresh_left,
			'due' => ($left <= self::TIKTOK_REFRESH_SEC),
			'threshold_sec' => self::TIKTOK_REFRESH_SEC
		);
	}

	function tiktok_refresh()
	{
		$row = $this->CI->tiktok_token_model->select_lasted_token();
		if (empty($row) || empty($row['refresh_token'])) {
			return false;
		}
		if (!defined('TIKTOK_KEY') || !defined('TIKTOK_SECRET')) {
			return false;
		}
		$url = 'https://auth.tiktok-shops.com/api/v2/token/refresh?app_key='.rawurlencode(TIKTOK_KEY)
			.'&app_secret='.rawurlencode(TIKTOK_SECRET)
			.'&refresh_token='.rawurlencode($row['refresh_token'])
			.'&grant_type=refresh_token';
		$raw = $this->curl_get($url);
		$res = json_decode($raw, true);
		if (!is_array($res) || empty($res['data']['access_token'])) {
			log_message('error', 'tiktok token refresh failed');
			return false;
		}
		$d = $res['data'];
		$upd = array(
			'access_token' => $d['access_token']
		);
		if (!empty($d['access_token_expire_in'])) {
			$upd['access_token_expire_in'] = gmdate('Y-m-d H:i:s', (int)$d['access_token_expire_in']);
		}
		if (!empty($d['refresh_token'])) {
			$upd['refresh_token'] = $d['refresh_token'];
		}
		if (!empty($d['refresh_token_expire_in'])) {
			$upd['refresh_token_expire_in'] = gmdate('Y-m-d H:i:s', (int)$d['refresh_token_expire_in']);
		}
		if (!empty($row['tiktok_token_id'])) {
			$this->CI->tiktok_token_model->update($upd, $row['tiktok_token_id']);
		} else {
			$this->CI->tiktok_token_model->insert($upd);
		}
		return true;
	}

	function probe_and_repair($platform, $snap)
	{
		if (!is_array($snap)) {
			$snap = array();
		}
		$probe = $this->probe_platform($platform);
		$snap['api_ok'] = !empty($probe['ok']);
		$snap['api_error'] = isset($probe['error']) ? (string)$probe['error'] : '';
		$snap['left'] = isset($probe['left']) ? (string)$probe['left'] : '';
		if (!empty($probe['ok'])) {
			return $snap;
		}
		if (!$this->is_token_error($snap['api_error'])) {
			$snap['api_ok'] = true;
			return $snap;
		}
		$already_hit = !empty($snap['hit_platform']);
		$already_failed = (isset($snap['action']) && $snap['action'] === 'failed');
		if ($already_failed) {
			$snap['reason'] = 'refresh_failed_reauth_needed';
			return $snap;
		}
		if (!$already_hit) {
			$ok = $this->force_refresh($platform);
			$snap['action'] = $ok ? 'refreshed' : 'failed';
			$snap['reason'] = $ok ? 'api_dead_refreshed' : 'refresh_failed_reauth_needed';
			$snap['hit_platform'] = 1;
		}
		$probe = $this->probe_platform($platform);
		$snap['api_ok'] = !empty($probe['ok']);
		$snap['api_error'] = isset($probe['error']) ? (string)$probe['error'] : '';
		$snap['left'] = isset($probe['left']) ? (string)$probe['left'] : $snap['left'];
		if (empty($probe['ok']) && $this->is_token_error($snap['api_error'])) {
			$snap['reason'] = 'refresh_failed_reauth_needed';
			if ($snap['action'] !== 'failed') {
				$snap['action'] = 'failed';
			}
		}
		return $snap;
	}

	function force_refresh($platform)
	{
		$ok = false;
		if ($platform === 'shopee') {
			$this->CI->load->library('businesslogic/shopee_bl');
			$ok = $this->CI->shopee_bl->ensure_access_token(true);
		} elseif ($platform === 'lazada') {
			$this->CI->load->library('businesslogic/chat_platform_bl');
			$ok = $this->CI->chat_platform_bl->ensure_lazada_token(true);
		} elseif ($platform === 'tiktok') {
			$ok = $this->tiktok_refresh();
		}
		$this->remember_hit($platform, $ok);
		return $ok;
	}

	function probe_platform($platform)
	{
		if ($platform === 'shopee') {
			return $this->probe_shopee();
		}
		if ($platform === 'lazada') {
			return $this->probe_lazada();
		}
		if ($platform === 'tiktok') {
			return $this->probe_tiktok();
		}
		return array('ok' => false, 'error' => 'unknown_platform', 'left' => '');
	}

	function probe_shopee()
	{
		$row = array('ok' => true, 'error' => '', 'left' => $this->shopee_left());
		$this->CI->load->library('businesslogic/chat_platform_bl');
		$res = $this->CI->chat_platform_bl->shopee_call('/api/v2/shop/get_shop_info', 'get', array());
		if (isset($res['_err'])) {
			$row['error'] = (string)$res['_err'];
			$row['ok'] = !$this->is_token_error($row['error']);
			return $row;
		}
		$err = !empty($res['error']) ? (string)$res['error'] : '';
		if ($err !== '' && $this->is_token_error($err)) {
			$row['error'] = $err;
			$row['ok'] = false;
		}
		return $row;
	}

	function probe_lazada()
	{
		$row = array('ok' => true, 'error' => '', 'left' => $this->lazada_left());
		$this->CI->load->library('businesslogic/chat_platform_bl');
		$res = $this->CI->chat_platform_bl->lazada_exec('/seller/get', 'GET', array());
		if (!empty($res['ok'])) {
			return $row;
		}
		$err = isset($res['error']) ? (string)$res['error'] : 'lazada_error';
		$row['error'] = $err;
		$row['ok'] = !$this->is_token_error($err);
		return $row;
	}

	function probe_tiktok()
	{
		$row = array('ok' => true, 'error' => '', 'left' => $this->tiktok_left());
		$this->CI->load->library('businesslogic/chat_platform_bl');
		$res = $this->CI->chat_platform_bl->tiktok_request('GET', '/authorization/202309/shops', array(), null);
		if (isset($res['_err'])) {
			$row['error'] = (string)$res['_err'];
			$row['ok'] = !$this->is_token_error($row['error']);
			return $row;
		}
		$code = isset($res['code']) ? (int)$res['code'] : 0;
		$msg = isset($res['message']) ? (string)$res['message'] : '';
		if ($code === 0) {
			return $row;
		}
		$row['error'] = $msg !== '' ? $msg : 'tiktok_code_'.$code;
		if ($this->is_token_error($row['error']) || $code === 105002 || $code === 104001 || $code === 36004003) {
			$row['ok'] = false;
		}
		return $row;
	}

	function is_token_error($msg)
	{
		$low = strtolower(trim((string)$msg));
		if ($low === '') {
			return false;
		}
		if ($low === 'shopee_token' || $low === 'lazada_token' || $low === 'tiktok_token' || $low === 'no_token') {
			return true;
		}
		if (strpos($low, 'token') !== false) {
			return true;
		}
		if (strpos($low, 'unauthorized') !== false) {
			return true;
		}
		if (strpos($low, 'expire') !== false) {
			return true;
		}
		return false;
	}

	function shopee_left()
	{
		$row = $this->CI->shopee_token_model->get_litetime_token();
		if (empty($row) || empty($row['litetime'])) {
			return 'n/a';
		}
		return (string)$row['litetime'];
	}

	function lazada_left()
	{
		$row = $this->CI->laztoken_model->get_litetime_token();
		if (empty($row) || !isset($row['litetime'])) {
			return 'n/a';
		}
		return ((string)$row['litetime']).' day';
	}

	function tiktok_left()
	{
		$row = $this->CI->tiktok_token_model->get_litetime_token();
		if (empty($row)) {
			return 'n/a';
		}
		$days = isset($row['litetime_days']) ? (int)$row['litetime_days'] : 0;
		if ($days >= 2) {
			return $days.' day';
		}
		return isset($row['litetime']) ? (string)$row['litetime'] : 'n/a';
	}

	function alert_summary()
	{
		$probe = $this->last_probe();
		$saved = isset($probe['tokens']) && is_array($probe['tokens']) ? $probe['tokens'] : array();
		$shops = array();
		$token_shops = 0;
		foreach (array('lazada', 'shopee', 'tiktok') as $platform) {
			$row = isset($saved[$platform]) && is_array($saved[$platform]) ? $saved[$platform] : array();
			$has = array_key_exists('api_ok', $row);
			$ok = $has ? !empty($row['api_ok']) : true;
			$left = isset($row['left']) ? (string)$row['left'] : $this->left_for($platform);
			$err = isset($row['api_error']) ? (string)$row['api_error'] : '';
			if ($has && !$ok) {
				$token_shops++;
			}
			$shops[$platform] = array(
				'ok' => $ok,
				'left' => $left,
				'error' => $err
			);
		}
		return array(
			'ok' => true,
			'at' => isset($probe['at']) ? (int)$probe['at'] : 0,
			'checked_at' => isset($probe['checked_at']) ? $probe['checked_at'] : '',
			'topics' => ($token_shops > 0) ? 1 : 0,
			'token_shops' => $token_shops,
			'shops' => $shops
		);
	}

	function left_for($platform)
	{
		if ($platform === 'shopee') {
			return $this->shopee_left();
		}
		if ($platform === 'lazada') {
			return $this->lazada_left();
		}
		if ($platform === 'tiktok') {
			return $this->tiktok_left();
		}
		return 'n/a';
	}

	function last_probe()
	{
		$path = $this->probe_path();
		if (!is_file($path)) {
			return array();
		}
		$j = json_decode((string)@file_get_contents($path), true);
		return is_array($j) ? $j : array();
	}

	function write_probe($tokens)
	{
		$path = $this->probe_path();
		$dir = dirname($path);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($path, json_encode(array(
			'at' => time(),
			'checked_at' => date('Y-m-d H:i:s'),
			'tokens' => $tokens
		)));
	}

	function probe_path()
	{
		$base = defined('APP_STORE_PATH') ? APP_STORE_PATH : APPPATH.'cache';
		return rtrim(str_replace('\\', '/', (string)$base), '/').'/platform_token_probe.json';
	}

	function left_sec_from_dt($dt)
	{
		$dt = trim((string)$dt);
		if ($dt === '') {
			return 0;
		}
		$t = strtotime($dt.' UTC');
		if ($t === false) {
			$t = strtotime($dt);
		}
		if ($t === false) {
			return 0;
		}
		return (int)$t - time();
	}

	function curl_get($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$raw = curl_exec($ch);
		curl_close($ch);
		return is_string($raw) ? $raw : '';
	}

	function hit_block($platform)
	{
		$state = $this->hit_state();
		$row = isset($state[$platform]) && is_array($state[$platform]) ? $state[$platform] : array();
		$now = time();
		$last = isset($row['at']) ? (int)$row['at'] : 0;
		$ok = !empty($row['ok']);
		if ($last < 1) {
			return '';
		}
		$ago = $now - $last;
		if (!$ok && $ago < self::FAIL_COOLDOWN_SEC) {
			return 'cooldown_fail';
		}
		if ($ok && $ago < self::MIN_HIT_SEC) {
			return 'cooldown_ok';
		}
		return '';
	}

	function remember_hit($platform, $ok)
	{
		$state = $this->hit_state();
		$state[$platform] = array(
			'at' => time(),
			'ok' => $ok ? 1 : 0
		);
		$path = $this->hit_path();
		$dir = dirname($path);
		if (!is_dir($dir)) {
			@mkdir($dir, 0755, true);
		}
		@file_put_contents($path, json_encode($state));
	}

	function hit_state()
	{
		$path = $this->hit_path();
		if (!is_file($path)) {
			return array();
		}
		$j = json_decode((string)@file_get_contents($path), true);
		return is_array($j) ? $j : array();
	}

	function hit_path()
	{
		$base = defined('APP_STORE_PATH') ? APP_STORE_PATH : APPPATH.'cache';
		return rtrim(str_replace('\\', '/', (string)$base), '/').'/platform_token_hits.json';
	}
}