<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat_platform_bl
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('chat_learn_model');
		$this->CI->load->library('util/date_util');
	}

	function extra($thread)
	{
		$raw = isset($thread['extra_json']) ? $thread['extra_json'] : '';
		if ($raw === '' || $raw === null) {
			return array();
		}
		$j = json_decode($raw, true);
		return is_array($j) ? $j : array();
	}

	function merge_extra($thread, $add)
	{
		$cur = $this->extra($thread);
		foreach ($add as $k => $v) {
			$cur[$k] = $v;
		}
		return json_encode($cur, JSON_UNESCAPED_UNICODE);
	}

	function is_live($thread)
	{
		return !empty($thread['platform_conv_id']);
	}

	function sync_all()
	{
		$out = array(
			'shopee' => $this->sync_shopee_list(),
			'lazada' => $this->sync_lazada_list(),
			'tiktok' => $this->sync_tiktok_list()
		);
		return $out;
	}

	function sync_thread($thread)
	{
		if (empty($thread['platform_conv_id'])) {
			return array('ok' => true, 'error' => '', 'added' => 0);
		}
		$p = $thread['platform'];
		if ($p === 'shopee') {
			return $this->sync_shopee_messages($thread);
		}
		if ($p === 'lazada') {
			return $this->sync_lazada_messages($thread);
		}
		if ($p === 'tiktok') {
			return $this->sync_tiktok_messages($thread);
		}
		return array('ok' => false, 'error' => 'unknown_platform', 'added' => 0);
	}

	function ingest_push($platform, $payload)
	{
		$conv = $this->push_conv_id($platform, $payload);
		if ($conv === '') {
			return false;
		}
		if ($platform === 'shopee') {
			$this->CI->chat_learn_model->upsert_thread('shopee', $conv, array(
				'last_message_at' => date('Y-m-d H:i:s')
			));
			$row = $this->CI->chat_learn_model->get_by_conv('shopee', $conv);
			if (!empty($row)) {
				$this->sync_shopee_messages($row);
			}
			return true;
		}
		if ($platform === 'lazada') {
			$this->upsert_lazada_session(array('session_id' => $conv));
			$row = $this->CI->chat_learn_model->get_by_conv('lazada', $conv);
			if (!empty($row)) {
				$this->sync_lazada_messages($row);
			}
			return true;
		}
		if ($platform === 'tiktok') {
			$this->upsert_tiktok_conv(array('conversation_id' => $conv));
			$row = $this->CI->chat_learn_model->get_by_conv('tiktok', $conv);
			if (!empty($row)) {
				$this->sync_tiktok_messages($row);
			}
			return true;
		}
		return false;
	}

	function send_text($thread, $text)
	{
		$text = trim((string)$text);
		if ($text === '') {
			return array('ok' => false, 'error' => 'empty');
		}
		if (!$this->is_live($thread)) {
			return array('ok' => true, 'error' => '', 'local_only' => 1);
		}
		$p = $thread['platform'];
		if ($p === 'shopee') {
			return $this->shopee_send($thread, 'text', array('text' => $text));
		}
		if ($p === 'lazada') {
			return $this->lazada_send($thread, 1, array('txt' => $text));
		}
		if ($p === 'tiktok') {
			return $this->tiktok_send($thread, 'TEXT', array('content' => $text));
		}
		return array('ok' => false, 'error' => 'unknown_platform');
	}

	function send_product($thread, $item_id)
	{
		$item_id = trim((string)$item_id);
		if ($item_id === '') {
			return array('ok' => false, 'error' => 'empty_item');
		}
		if (!$this->is_live($thread)) {
			return array('ok' => false, 'error' => 'not_live');
		}
		$p = $thread['platform'];
		if ($p === 'shopee') {
			$item_id = $this->resolve_shopee_item_id($item_id);
			if ($item_id === '') {
				return array('ok' => false, 'error' => 'empty_item');
			}
			$r = $this->shopee_send($thread, 'item', array('item_id' => (int)$item_id));
			if (empty($r['ok'])) {
				$r = $this->shopee_send($thread, 'product', array('item_id' => (int)$item_id));
			}
			return $r;
		}
		if ($p === 'lazada') {
			return $this->lazada_send($thread, 10006, array('item_id' => $item_id));
		}
		if ($p === 'tiktok') {
			$r = $this->tiktok_send($thread, 'PRODUCT_CARD', array('product_id' => $item_id));
			if (empty($r['ok'])) {
				$r = $this->tiktok_send($thread, 'TEXT', array('content' => 'สินค้า: '.$item_id));
			}
			return $r;
		}
		return array('ok' => false, 'error' => 'unknown_platform');
	}

	function send_order($thread, $order_id)
	{
		$order_id = trim((string)$order_id);
		if ($order_id === '') {
			return array('ok' => false, 'error' => 'empty_order');
		}
		if (!$this->is_live($thread)) {
			return array('ok' => false, 'error' => 'not_live');
		}
		$p = $thread['platform'];
		if ($p === 'shopee') {
			return $this->shopee_send($thread, 'order', array('order_sn' => $order_id));
		}
		if ($p === 'lazada') {
			return $this->lazada_send($thread, 10007, array('order_id' => $order_id));
		}
		if ($p === 'tiktok') {
			$r = $this->tiktok_send($thread, 'ORDER_CARD', array('order_id' => $order_id));
			if (empty($r['ok'])) {
				$r = $this->tiktok_send($thread, 'TEXT', array('content' => 'ออเดอร์: '.$order_id));
			}
			return $r;
		}
		return array('ok' => false, 'error' => 'unknown_platform');
	}

	function search_products($platform, $q)
	{
		$q = trim((string)$q);
		if ($platform === 'shopee') {
			return $this->shopee_search_products($q);
		}
		if ($platform === 'lazada') {
			return $this->lazada_search_products($q);
		}
		if ($platform === 'tiktok') {
			return $this->tiktok_search_products($q);
		}
		return array();
	}

	function orders_for_thread($thread, $limit = 8)
	{
		$p = $thread['platform'];
		if ($p === 'shopee') {
			return $this->shopee_orders($thread, $limit);
		}
		if ($p === 'lazada') {
			return $this->lazada_orders($thread, $limit);
		}
		if ($p === 'tiktok') {
			return $this->tiktok_orders($thread, $limit);
		}
		return array();
	}

	function search_orders_for_thread($thread, $q, $limit = 40)
	{
		$rows = $this->orders_for_thread($thread, 50);
		$q = trim((string)$q);
		$out = array();
		foreach ($rows as $r) {
			if (!is_array($r) || empty($r['order_id'])) {
				continue;
			}
			if ($q !== '') {
				$hay = $r['order_id'].' '.(isset($r['status']) ? $r['status'] : '').' '.(isset($r['items']) ? $r['items'] : '');
				$hit = function_exists('mb_stripos')
					? (mb_stripos($hay, $q, 0, 'UTF-8') !== false)
					: (stripos($hay, $q) !== false);
				if (!$hit) {
					continue;
				}
			}
			$out[] = array(
				'kind' => 'order',
				'id' => (string)$r['order_id'],
				'name' => isset($r['items']) ? (string)$r['items'] : '',
				'status' => isset($r['status']) ? (string)$r['status'] : '',
				'items' => isset($r['items']) ? (string)$r['items'] : '',
				'amount' => isset($r['amount']) ? (string)$r['amount'] : '',
				'image' => ''
			);
			if (count($out) >= (int)$limit) {
				break;
			}
		}
		return $out;
	}

	function order_allowed($thread, $order_id)
	{
		$order_id = trim((string)$order_id);
		if ($order_id === '') {
			return false;
		}
		$rows = $this->orders_for_thread($thread, 50);
		foreach ($rows as $r) {
			if (is_array($r) && isset($r['order_id']) && (string)$r['order_id'] === $order_id) {
				return true;
			}
		}
		return false;
	}

	function orders_prompt($thread)
	{
		$rows = $this->orders_for_thread($thread, 6);
		if (empty($rows)) {
			return '';
		}
		$txt = "ออเดอร์ของลูกค้าบนช่องนี้ (ใช้รหัสนี้เท่านั้น ห้ามเดา):\n";
		foreach ($rows as $r) {
			$txt .= '- '.$r['order_id'].' สถานะ '.$r['status'];
			if (!empty($r['amount'])) {
				$txt .= ' ยอด '.$r['amount'];
			}
			if (!empty($r['items'])) {
				$txt .= ' สินค้า '.$r['items'];
			}
			$txt .= "\n";
		}
		return $txt;
	}

	function push_conv_id($platform, $payload)
	{
		if (!is_array($payload)) {
			return '';
		}
		$data = isset($payload['data']) && is_array($payload['data']) ? $payload['data'] : $payload;
		$keys = array('conversation_id', 'conversationId', 'session_id', 'sessionId');
		foreach ($keys as $k) {
			if (!empty($data[$k])) {
				return (string)$data[$k];
			}
			if (!empty($payload[$k])) {
				return (string)$payload[$k];
			}
		}
		if ($platform === 'shopee' && isset($payload['data']['content']['conversation_id'])) {
			return (string)$payload['data']['content']['conversation_id'];
		}
		return '';
	}

	function store_inbound_outbound($thread_id, $direction, $body, $sender, $platform_msg_id, $msg_type, $extra = null, $ai_draft = null)
	{
		if ($platform_msg_id && $this->CI->chat_learn_model->message_exists($thread_id, $platform_msg_id)) {
			return 0;
		}
		$body = trim((string)$body);
		if ($body === '') {
			$body = '['.$msg_type.']';
		}
		if ($platform_msg_id && $this->stamp_unmatched_local($thread_id, $msg_type, $extra, $body, $platform_msg_id)) {
			return 0;
		}
		$data = array(
			'thread_id' => (int)$thread_id,
			'direction' => $direction,
			'body' => $body,
			'sender' => $sender,
			'ai_draft' => $ai_draft,
			'platform_msg_id' => $platform_msg_id ? (string)$platform_msg_id : null,
			'msg_type' => $msg_type ? $msg_type : 'text',
			'extra_json' => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : null
		);
		return $this->CI->chat_learn_model->insert_message($data);
	}

	function stamp_unmatched_local($thread_id, $msg_type, $extra, $body, $platform_msg_id)
	{
		$key = '';
		$val = '';
		if (is_array($extra)) {
			if (!empty($extra['item_id'])) {
				$key = 'item_id';
				$val = (string)$extra['item_id'];
			} elseif (!empty($extra['order_sn'])) {
				$key = 'order_sn';
				$val = (string)$extra['order_sn'];
			} elseif (!empty($extra['order_id'])) {
				$key = 'order_id';
				$val = (string)$extra['order_id'];
			}
		}
		$this->CI->db->from('chat_message');
		$this->CI->db->where('thread_id', (int)$thread_id);
		$this->CI->db->group_start();
		$this->CI->db->where('platform_msg_id IS NULL', null, false);
		$this->CI->db->or_where('platform_msg_id', '');
		$this->CI->db->group_end();
		if ($msg_type !== '') {
			$this->CI->db->where('msg_type', $msg_type);
		}
		$this->CI->db->order_by('message_id', 'desc');
		$this->CI->db->limit(20);
		$q = $this->CI->db->get();
		$rows = $q ? $q->result_array() : array();
		foreach ($rows as $row) {
			$hit = false;
			$ex = json_decode(isset($row['extra_json']) ? $row['extra_json'] : '', true);
			if ($key !== '' && is_array($ex) && isset($ex[$key]) && (string)$ex[$key] === $val) {
				$hit = true;
			} elseif ($val !== '' && strpos((string)$row['body'], $val) !== false) {
				$hit = true;
			} elseif ($msg_type === 'text' && trim((string)$row['body']) === trim((string)$body)) {
				$hit = true;
			}
			if (!$hit) {
				continue;
			}
			$upd = array('platform_msg_id' => (string)$platform_msg_id);
			if (is_array($extra) && is_array($ex)) {
				$upd['extra_json'] = json_encode(array_merge($ex, $extra), JSON_UNESCAPED_UNICODE);
			}
			$this->CI->db->where('message_id', (int)$row['message_id']);
			$this->CI->db->update('chat_message', $upd);
			return true;
		}
		return false;
	}

	function send_result_msg_id($sent)
	{
		if (!is_array($sent)) {
			return '';
		}
		$raw = isset($sent['raw']) && is_array($sent['raw']) ? $sent['raw'] : $sent;
		if (isset($raw['response']['message_id'])) {
			return (string)$raw['response']['message_id'];
		}
		if (isset($raw['response']['msg_id'])) {
			return (string)$raw['response']['msg_id'];
		}
		if (isset($raw['message_id'])) {
			return (string)$raw['message_id'];
		}
		return '';
	}

	function shopee_msg_gone($m)
	{
		if (!is_array($m)) {
			return false;
		}
		foreach (array('is_deleted', 'deleted', 'is_unsent', 'unsent', 'is_recalled', 'recalled') as $k) {
			if (!isset($m[$k])) {
				continue;
			}
			$v = $m[$k];
			if ($v === true || $v === 1 || $v === '1' || $v === 'true') {
				return true;
			}
			if (is_string($v) && in_array(strtolower($v), array('deleted', 'unsent', 'recalled', 'revoke', 'revoked'), true)) {
				return true;
			}
		}
		foreach (array('status', 'message_status') as $k) {
			if (!isset($m[$k]) || !is_string($m[$k])) {
				continue;
			}
			if (in_array(strtolower($m[$k]), array('deleted', 'unsent', 'recalled', 'revoke', 'revoked', 'recall'), true)) {
				return true;
			}
		}
		return false;
	}

	function product_card_extra($platform, $item_id)
	{
		$item_id = trim((string)$item_id);
		$out = array('item_id' => $item_id);
		if ($item_id === '' || $platform !== 'shopee') {
			return $out;
		}
		$info = $this->shopee_items_info(array($item_id));
		if (isset($info[$item_id])) {
			$out['name'] = $info[$item_id]['name'];
			$out['sku'] = $info[$item_id]['sku'];
			$out['image'] = $info[$item_id]['image'];
		}
		return $out;
	}

	function decorate_messages($platform, $messages)
	{
		$ids = array();
		foreach ($messages as $i => $m) {
			$ex = json_decode(isset($m['extra_json']) ? $m['extra_json'] : '', true);
			if (!is_array($ex)) {
				$ex = array();
			}
			$item = isset($ex['item_id']) ? trim((string)$ex['item_id']) : '';
			if ($item === '' && preg_match('/\[สินค้า\s+(\d+)\]/u', isset($m['body']) ? $m['body'] : '', $mm)) {
				$item = $mm[1];
			}
			$messages[$i]['_ex'] = $ex;
			$messages[$i]['_item_id'] = $item;
			if ($item !== '' && preg_match('/^\d+$/', $item)) {
				$ids[$item] = $item;
			}
		}
		$info = array();
		if ($platform === 'shopee' && !empty($ids)) {
			$info = $this->shopee_items_info(array_values($ids));
		}
		foreach ($messages as $i => $m) {
			$ex = $messages[$i]['_ex'];
			$item = $messages[$i]['_item_id'];
			$card = array();
			if ($item !== '') {
				$row = isset($info[$item]) ? $info[$item] : array();
				$card = array(
					'id' => $item,
					'name' => !empty($ex['name']) ? $ex['name'] : (isset($row['name']) ? $row['name'] : ''),
					'sku' => !empty($ex['sku']) ? $ex['sku'] : (isset($row['sku']) ? $row['sku'] : ''),
					'image' => !empty($ex['image']) ? $ex['image'] : (isset($row['image']) ? $row['image'] : '')
				);
			}
			$messages[$i]['card'] = $card;
			unset($messages[$i]['_ex'], $messages[$i]['_item_id']);
		}
		return $messages;
	}

	function preview($text)
	{
		$text = $this->readable_text($text);
		$text = preg_replace('/\s+/u', ' ', trim((string)$text));
		if (function_exists('mb_substr')) {
			return mb_substr($text, 0, 180, 'UTF-8');
		}
		return substr($text, 0, 180);
	}

	function readable_text($raw, $depth = 0)
	{
		if ($depth > 5) {
			return '';
		}
		if (is_object($raw)) {
			$raw = (array)$raw;
		}
		if (is_array($raw)) {
			return $this->extract_payload_text($raw, $depth);
		}
		$s = trim((string)$raw);
		if ($s === '') {
			return '';
		}
		if ($s === '[bundle_message]' || strcasecmp($s, 'bundle_message') === 0) {
			return '[ชุดสินค้า/ข้อความ]';
		}
		$c0 = substr($s, 0, 1);
		if ($c0 === '{' || $c0 === '[') {
			$j = json_decode($s, true);
			if (is_array($j)) {
				$got = $this->extract_payload_text($j, $depth);
				if ($got !== '') {
					return $got;
				}
			}
			if (preg_match('/"text"\s*:\s*"((?:\\\\.|[^"\\\\])*)/s', $s, $m)) {
				$inner = json_decode('"'.$m[1].'"');
				if (is_string($inner) && $inner !== '') {
					return $inner;
				}
			}
		}
		if (strpos($s, '\\u') !== false) {
			$quoted = json_decode('"'.str_replace('"', '\\"', $s).'"');
			if (is_string($quoted) && $quoted !== '' && $quoted !== $s) {
				return $this->readable_text($quoted, $depth + 1);
			}
		}
		return $s;
	}

	function extract_payload_text($arr, $depth = 0)
	{
		if (!is_array($arr)) {
			return $this->readable_text($arr, $depth + 1);
		}
		$keys = array('text', 'txt', 'content', 'message', 'translated_text', 'last_message_content', 'latest_message_content', 'product_name', 'item_name', 'title');
		foreach ($keys as $k) {
			if (!isset($arr[$k])) {
				continue;
			}
			if (is_string($arr[$k]) && trim($arr[$k]) !== '') {
				$got = $this->readable_text($arr[$k], $depth + 1);
				if ($got !== '') {
					return $got;
				}
			}
			if (is_array($arr[$k])) {
				$got = $this->extract_payload_text($arr[$k], $depth + 1);
				if ($got !== '') {
					return $got;
				}
			}
		}
		if (isset($arr['item_id'])) {
			return '[สินค้า '.$arr['item_id'].']';
		}
		foreach (array('item_list', 'items', 'bundle_items', 'product_list') as $lk) {
			if (!isset($arr[$lk]) || !is_array($arr[$lk]) || empty($arr[$lk][0])) {
				continue;
			}
			$got = $this->extract_payload_text($arr[$lk][0], $depth + 1);
			if ($got !== '') {
				return $got;
			}
		}
		if (isset($arr['order_sn'])) {
			return '[ออเดอร์ '.$arr['order_sn'].']';
		}
		if (isset($arr['order_id'])) {
			return '[ออเดอร์ '.$arr['order_id'].']';
		}
		if (isset($arr['image_url']) || isset($arr['url']) || isset($arr['thumb_url'])) {
			return '[รูป]';
		}
		if (isset($arr['sticker_id']) || isset($arr['sticker_package_id'])) {
			return '[สติกเกอร์]';
		}
		return '';
	}

	function pick_avatar($row)
	{
		if (!is_array($row)) {
			return '';
		}
		$keys = array(
			'to_avatar', 'avatar', 'portrait', 'user_avatar', 'head_url', 'headUrl',
			'profile_picture', 'profile_pic', 'icon', 'avatar_url', 'buyer_avatar'
		);
		foreach ($keys as $k) {
			if (empty($row[$k]) || !is_string($row[$k])) {
				continue;
			}
			$url = trim($row[$k]);
			if ($url !== '' && preg_match('#^https?://#i', $url)) {
				return $url;
			}
		}
		foreach (array('user', 'buyer', 'participant', 'to_user', 'from_user') as $nk) {
			if (isset($row[$nk]) && is_array($row[$nk])) {
				$got = $this->pick_avatar($row[$nk]);
				if ($got !== '') {
					return $got;
				}
			}
		}
		if (isset($row['participants']) && is_array($row['participants'])) {
			foreach ($row['participants'] as $p) {
				if (!is_array($p)) {
					continue;
				}
				$role = isset($p['role']) ? strtoupper((string)$p['role']) : '';
				if ($role === 'SELLER' || $role === 'SHOP') {
					continue;
				}
				$got = $this->pick_avatar($p);
				if ($got !== '') {
					return $got;
				}
			}
		}
		return '';
	}

	function thread_avatar($th)
	{
		if (!empty($th['buyer_avatar'])) {
			return (string)$th['buyer_avatar'];
		}
		$ex = $this->extra($th);
		if (!empty($ex['avatar'])) {
			return (string)$ex['avatar'];
		}
		return '';
	}

	function conv_last_from($row, $shop_id, $buyer_id)
	{
		if (!is_array($row)) {
			return '';
		}
		if (isset($row['latest_message']['sender']['role'])) {
			$role = strtoupper((string)$row['latest_message']['sender']['role']);
			if ($role === 'SELLER' || $role === 'SHOP' || $role === 'AGENT') {
				return 'shop';
			}
			if ($role === 'BUYER' || $role === 'USER' || $role === 'CUSTOMER') {
				return 'buyer';
			}
		}
		if (isset($row['from_account_type'])) {
			return ((int)$row['from_account_type'] === 2) ? 'shop' : 'buyer';
		}
		$from = '';
		$keys = array('last_message_from_id', 'last_sender_id', 'from_id', 'sender_id', 'from_account_id', 'last_message_sender');
		foreach ($keys as $k) {
			if (!isset($row[$k]) || is_array($row[$k])) {
				continue;
			}
			$from = trim((string)$row[$k]);
			if ($from !== '') {
				break;
			}
		}
		$shop_id = (string)$shop_id;
		$buyer_id = (string)$buyer_id;
		if ($from !== '') {
			if ($shop_id !== '' && $from === $shop_id) {
				return 'shop';
			}
			if ($buyer_id !== '' && $from === $buyer_id) {
				return 'buyer';
			}
			if ($shop_id !== '' && $from !== $buyer_id) {
				return 'shop';
			}
			return 'buyer';
		}
		return '';
	}

	function thread_status($th, $last_dir)
	{
		$unread = isset($th['unread']) ? (int)$th['unread'] : 0;
		$from = isset($th['last_from']) ? strtolower(trim((string)$th['last_from'])) : '';
		if ($from === '') {
			$ex = $this->extra($th);
			$from = isset($ex['last_from']) ? strtolower(trim((string)$ex['last_from'])) : '';
		}
		$wait = 0;
		$replied = 0;
		$source = '';
		if ($last_dir === 'in') {
			$wait = 1;
			$from = 'buyer';
			$source = 'msg';
		} elseif ($last_dir === 'out') {
			$replied = 1;
			$from = 'shop';
			$source = 'msg';
		} elseif ($from === 'buyer') {
			$wait = 1;
			$source = 'flag';
		} elseif ($from === 'shop') {
			$replied = 1;
			$source = 'flag';
		} elseif ($unread > 0) {
			$wait = 1;
			$from = 'buyer';
			$source = 'unread';
		}
		$overdue = 0;
		if ($wait) {
			$at_raw = isset($th['last_message_at']) ? (string)$th['last_message_at'] : '';
			$ts = strtotime(preg_replace('/\.\d+$/', '', $at_raw));
			if ($ts && (time() - $ts) > (12 * 3600)) {
				$overdue = 1;
			}
		}
		$ex = $this->extra($th);
		$pinned = !empty($ex['pinned']) ? 1 : 0;
		return array(
			'needs_reply' => $wait,
			'replied' => $replied,
			'overdue' => $overdue,
			'pinned' => $pinned,
			'unread' => $unread,
			'last_from' => $from,
			'source' => $source
		);
	}

	function unix_to_dt($n)
	{
		if ($n === null || $n === '' || $n === false) {
			return date('Y-m-d H:i:s');
		}
		if (!is_numeric($n)) {
			$t = strtotime((string)$n);
			return ($t === false) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', $t);
		}
		$n = (float)$n;
		if ($n <= 0) {
			return date('Y-m-d H:i:s');
		}
		// Shopee last_message_timestamp is often nanoseconds.
		// SQL DATETIME cannot store year 55555 from a 16-digit leftover.
		while ($n > 9999999999) {
			$n = $n / 1000.0;
		}
		$sec = (int)$n;
		if ($sec < 946684800 || $sec > 4102444800) {
			return date('Y-m-d H:i:s');
		}
		return date('Y-m-d H:i:s', $sec);
	}

	function conv_id($row)
	{
		$cid = '';
		if (isset($row['conversation_id'])) {
			$cid = $row['conversation_id'];
		} elseif (isset($row['session_id'])) {
			$cid = $row['session_id'];
		}
		if (is_array($cid) || is_object($cid)) {
			return '';
		}
		return trim((string)$cid);
	}

	function shop_id_shopee()
	{
		$this->CI->load->model('shopee_token_model');
		$arr = $this->CI->shopee_token_model->getlatesttoken();
		if (empty($arr) || empty($arr['shopid'])) {
			return '';
		}
		return (string)$arr['shopid'];
	}

	function shopee_call($path, $method, $data)
	{
		$this->CI->load->library('businesslogic/shopeeapi');
		if (empty($this->CI->shopeeapi->initWithAppPath_Method($path, $method))) {
			return array('_err' => 'shopee_token');
		}
		$this->CI->shopeeapi->setData($data);
		if ($method === 'get') {
			$res = $this->CI->shopeeapi->execute_query();
		} else {
			$res = $this->CI->shopeeapi->execute_post();
		}
		if (!is_array($res)) {
			return array('_err' => 'shopee_empty');
		}
		$err = isset($res['error']) ? strtolower((string)$res['error']) : '';
		if ($err !== '' && (strpos($err, 'token') !== false || strpos($err, 'auth') !== false)) {
			$this->CI->load->library('businesslogic/shopee_bl');
			if ($this->CI->shopee_bl->ensure_access_token(true) && $this->CI->shopeeapi->initWithAppPath_Method($path, $method)) {
				$this->CI->shopeeapi->setData($data);
				$res = ($method === 'get') ? $this->CI->shopeeapi->execute_query() : $this->CI->shopeeapi->execute_post();
				if (!is_array($res)) {
					return array('_err' => 'shopee_empty');
				}
			}
		}
		return $res;
	}

	function sync_shopee_list()
	{
		$res = $this->shopee_call('/api/v2/sellerchat/get_conversation_list', 'get', array(
			'type' => 'all',
			'direction' => 'latest',
			'page_size' => 40
		));
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err'], 'n' => 0);
		}
		if (!empty($res['error']) && (string)$res['error'] !== '') {
			$res = $this->shopee_call('/api/v2/sellerchat/get_conversation_list', 'get', array(
				'type' => 'unread',
				'direction' => 'latest',
				'page_size' => 40
			));
		}
		if (!empty($res['error']) && (string)$res['error'] !== '') {
			return array('ok' => false, 'error' => $res['error'], 'n' => 0);
		}
		$list = array();
		if (isset($res['response']['conversations']) && is_array($res['response']['conversations'])) {
			$list = $res['response']['conversations'];
		} elseif (isset($res['response']['conversation_list']) && is_array($res['response']['conversation_list'])) {
			$list = $res['response']['conversation_list'];
		}
		$shop = $this->shop_id_shopee();
		$n = 0;
		foreach ($list as $row) {
			$cid = $this->conv_id($row);
			if ($cid === '') {
				continue;
			}
			$to_id = $this->pick_shopee_buyer_id($row, $shop);
			if ($to_id === '' && preg_match('/^\d+$/', $cid)) {
				$to_id = $cid;
			}
			$name = '';
			if (isset($row['to_name'])) {
				$name = $row['to_name'];
			} elseif (isset($row['name'])) {
				$name = $row['name'];
			}
			$last = '';
			if (isset($row['last_read_message_id'])) {
				$last = '';
			}
			$preview = '';
			if (isset($row['last_message_content'])) {
				$preview = $row['last_message_content'];
			} elseif (isset($row['latest_message_content'])) {
				$preview = $row['latest_message_content'];
			}
			$ts = 0;
			if (isset($row['last_message_timestamp'])) {
				$ts = $row['last_message_timestamp'];
			} elseif (isset($row['last_message_time'])) {
				$ts = $row['last_message_time'];
			}
			$unread = isset($row['unread_count']) ? (int)$row['unread_count'] : 0;
			$avatar = $this->pick_avatar($row);
			$last_from = $this->conv_last_from($row, $shop, $to_id);
			if ($last_from === '' && $unread > 0) {
				$last_from = 'buyer';
			}
			$extra = array();
			if ($to_id !== '') {
				$extra['to_id'] = $to_id;
			}
			if ($avatar !== '') {
				$extra['avatar'] = $avatar;
			}
			if ($last_from !== '') {
				$extra['last_from'] = $last_from;
			}
			if (!empty($row['pinned'])) {
				$extra['pinned'] = 1;
			}
			$upd = array(
				'buyer_name' => $name !== '' ? $name : null,
				'buyer_id' => $to_id !== '' ? (string)$to_id : null,
				'last_preview' => $this->preview($preview),
				'unread' => $unread,
				'last_message_at' => $ts ? $this->unix_to_dt($ts) : date('Y-m-d H:i:s'),
				'extra_json' => json_encode($extra, JSON_UNESCAPED_UNICODE)
			);
			if ($avatar !== '') {
				$upd['buyer_avatar'] = $avatar;
			}
			if ($last_from !== '') {
				$upd['last_from'] = $last_from;
			}
			$this->CI->chat_learn_model->upsert_thread('shopee', $cid, $upd);
			$n++;
		}
		return array('ok' => true, 'error' => '', 'n' => $n);
	}

	function shopee_to_id($thread)
	{
		$ex = $this->extra($thread);
		$to = $this->scalar_id(isset($ex['to_id']) ? $ex['to_id'] : '');
		if ($to === '') {
			$to = $this->scalar_id(isset($thread['buyer_id']) ? $thread['buyer_id'] : '');
		}
		if ($to !== '' && !preg_match('/^\d+$/', $to)) {
			return '';
		}
		return $to;
	}

	function scalar_id($v)
	{
		if (is_array($v)) {
			if (isset($v['user_id'])) {
				$v = $v['user_id'];
			} elseif (isset($v['id'])) {
				$v = $v['id'];
			} elseif (isset($v['to_id'])) {
				$v = $v['to_id'];
			} else {
				return '';
			}
		}
		if (is_object($v)) {
			$v = (array)$v;
			return $this->scalar_id($v);
		}
		$s = trim((string)$v);
		if ($s === '' || $s === '0' || strcasecmp($s, 'null') === 0 || strcasecmp($s, 'array') === 0) {
			return '';
		}
		return $s;
	}

	function pick_shopee_buyer_id($row, $shop = '', $depth = 0)
	{
		if (!is_array($row) || $depth > 2) {
			return '';
		}
		$shop = (string)$shop;
		$keys = array('to_id', 'user_id', 'to_user_id', 'buyer_id', 'buyer_user_id', 'opponent_id', 'last_message_from_id', 'from_id');
		foreach ($keys as $k) {
			if (!isset($row[$k])) {
				continue;
			}
			$v = $this->scalar_id($row[$k]);
			if ($v !== '' && $v !== $shop) {
				return $v;
			}
		}
		foreach (array('to_user', 'user', 'buyer', 'opponent', 'to') as $nk) {
			if (isset($row[$nk]) && is_array($row[$nk])) {
				$v = $this->pick_shopee_buyer_id($row[$nk], $shop, $depth + 1);
				if ($v !== '') {
					return $v;
				}
			}
		}
		return '';
	}

	function persist_shopee_to_id($thread, $to)
	{
		$to = $this->scalar_id($to);
		if ($to === '' || empty($thread['thread_id'])) {
			return $to;
		}
		$this->CI->chat_learn_model->update_thread($thread['thread_id'], array(
			'buyer_id' => $to,
			'extra_json' => $this->merge_extra($thread, array('to_id' => $to))
		));
		return $to;
	}

	function ensure_shopee_to_id($thread)
	{
		$to = $this->shopee_to_id($thread);
		if ($to !== '') {
			return $to;
		}
		$shop = $this->shop_id_shopee();
		$cid = isset($thread['platform_conv_id']) ? trim((string)$thread['platform_conv_id']) : '';
		if ($cid !== '') {
			$res = $this->shopee_call('/api/v2/sellerchat/get_one_conversation', 'get', array(
				'conversation_id' => $cid
			));
			$cands = array();
			if (isset($res['response']) && is_array($res['response'])) {
				$cands[] = $res['response'];
				if (isset($res['response']['conversation']) && is_array($res['response']['conversation'])) {
					$cands[] = $res['response']['conversation'];
				}
				if (isset($res['response']['conversations'][0]) && is_array($res['response']['conversations'][0])) {
					$cands[] = $res['response']['conversations'][0];
				}
			}
			foreach ($cands as $row) {
				$to = $this->pick_shopee_buyer_id($row, $shop);
				if ($to !== '') {
					break;
				}
			}
		}
		if ($to === '' && $cid !== '') {
			$msg = $this->shopee_call('/api/v2/sellerchat/get_message', 'get', array(
				'conversation_id' => $cid,
				'page_size' => 20
			));
			$msgs = array();
			if (isset($msg['response']['messages']) && is_array($msg['response']['messages'])) {
				$msgs = $msg['response']['messages'];
			} elseif (isset($msg['response']['message_list']) && is_array($msg['response']['message_list'])) {
				$msgs = $msg['response']['message_list'];
			}
			foreach ($msgs as $m) {
				if (!is_array($m)) {
					continue;
				}
				$from = $this->scalar_id(isset($m['from_id']) ? $m['from_id'] : (isset($m['from_user_id']) ? $m['from_user_id'] : ''));
				$from_shop = $this->scalar_id(isset($m['from_shop_id']) ? $m['from_shop_id'] : '');
				$is_shop = ($shop !== '' && ($from === $shop || $from_shop === $shop));
				if (!$is_shop && $from !== '' && $from !== $shop) {
					$to = $from;
					break;
				}
				$dest = $this->scalar_id(isset($m['to_id']) ? $m['to_id'] : '');
				if ($is_shop && $dest !== '' && $dest !== $shop) {
					$to = $dest;
					break;
				}
			}
		}
		if ($to === '' && $cid !== '' && preg_match('/^\d+$/', $cid)) {
			$to = $cid;
		}
		return $this->persist_shopee_to_id($thread, $to);
	}

	function sync_shopee_messages($thread)
	{
		$cid = $thread['platform_conv_id'];
		$res = $this->shopee_call('/api/v2/sellerchat/get_message', 'get', array(
			'conversation_id' => $cid,
			'page_size' => 60
		));
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err'], 'added' => 0);
		}
		if (!empty($res['error']) && (string)$res['error'] !== '') {
			return array('ok' => false, 'error' => $res['error'], 'added' => 0);
		}
		$msgs = array();
		if (isset($res['response']['messages']) && is_array($res['response']['messages'])) {
			$msgs = $res['response']['messages'];
		} elseif (isset($res['response']['message_list']) && is_array($res['response']['message_list'])) {
			$msgs = $res['response']['message_list'];
		}
		$shop = $this->shop_id_shopee();
		$added = 0;
		$last_body = '';
		$last_at = '';
		$last_from = '';
		$buyer_from = '';
		$live_ids = array();
		$live_items = array();
		$min_ts = 0;
		$max_ts = 0;
		foreach ($msgs as $m) {
			$mid = isset($m['message_id']) ? (string)$m['message_id'] : '';
			$gone = $this->shopee_msg_gone($m);
			if ($mid !== '' && !$gone) {
				$live_ids[$mid] = $mid;
			}
			$ts = 0;
			if (isset($m['created_timestamp']) && is_numeric($m['created_timestamp'])) {
				$ts = (int)$m['created_timestamp'];
				while ($ts > 9999999999) {
					$ts = (int)($ts / 1000);
				}
			}
			if ($ts > 0) {
				if ($min_ts === 0 || $ts < $min_ts) {
					$min_ts = $ts;
				}
				if ($ts > $max_ts) {
					$max_ts = $ts;
				}
			}
			if ($gone) {
				continue;
			}
			$from = $this->scalar_id(isset($m['from_id']) ? $m['from_id'] : (isset($m['from_user_id']) ? $m['from_user_id'] : ''));
			$from_shop = $this->scalar_id(isset($m['from_shop_id']) ? $m['from_shop_id'] : '');
			$is_out = ($shop !== '' && ($from === $shop || $from_shop === $shop));
			if (!$is_out && isset($m['source_type']) && (string)$m['source_type'] === 'web') {
				$is_out = true;
			}
			$last_from = $is_out ? 'shop' : 'buyer';
			if (!$is_out && $from !== '' && $from !== $shop) {
				$buyer_from = $from;
			}
			$dest = $this->scalar_id(isset($m['to_id']) ? $m['to_id'] : '');
			if ($is_out && $dest !== '' && $dest !== $shop) {
				$buyer_from = $dest;
			}
			$parsed = $this->parse_shopee_content($m);
			if (!empty($parsed['extra']['item_id'])) {
				$live_items[(string)$parsed['extra']['item_id']] = 1;
			}
			$id = $this->store_inbound_outbound(
				$thread['thread_id'],
				$is_out ? 'out' : 'in',
				$parsed['body'],
				$is_out ? 'shop' : 'buyer',
				$mid,
				$parsed['type'],
				$parsed['extra']
			);
			if ($id) {
				$added++;
				$last_body = $parsed['body'];
				if (isset($m['created_timestamp'])) {
					$last_at = $this->unix_to_dt($m['created_timestamp']);
				}
			}
		}
		$removed = $this->prune_shopee_deleted($thread, $live_ids, $live_items, $min_ts, $max_ts);
		if ($removed > 0) {
			$tail = $this->CI->chat_learn_model->last_message($thread['thread_id']);
			if (!empty($tail['body'])) {
				$last_body = $tail['body'];
			}
			if (!empty($tail['cdate'])) {
				$last_at = $tail['cdate'];
			}
			if (!empty($tail['direction'])) {
				$last_from = ($tail['direction'] === 'out') ? 'shop' : 'buyer';
			}
		}
		$upd = array();
		if ($last_from !== '') {
			$upd['last_from'] = $last_from;
		}
		if ($buyer_from !== '') {
			$upd['buyer_id'] = $buyer_from;
			$upd['extra_json'] = $this->merge_extra($thread, array('to_id' => $buyer_from));
		}
		if ($last_body !== '') {
			$upd['last_preview'] = $this->preview($last_body);
		}
		if ($last_at !== '') {
			$upd['last_message_at'] = $last_at;
		}
		if (!empty($upd)) {
			$this->CI->chat_learn_model->update_thread($thread['thread_id'], $upd);
		}
		return array('ok' => true, 'error' => '', 'added' => $added, 'removed' => isset($removed) ? $removed : 0);
	}

	function prune_shopee_deleted($thread, $live_ids, $live_items, $min_ts, $max_ts)
	{
		$tid = (int)$thread['thread_id'];
		if ($tid < 1) {
			return 0;
		}
		$live_ids = is_array($live_ids) ? $live_ids : array();
		if ($min_ts > 0) {
			$window_start = $min_ts - 600;
		} else {
			$window_start = time() - 86400;
		}
		$fresh_ts = time() - 20;
		$rows = $this->CI->chat_learn_model->messages($tid);
		$drop = array();
		foreach ($rows as $row) {
			$mid = isset($row['platform_msg_id']) ? trim((string)$row['platform_msg_id']) : '';
			$row_ts = $this->row_unix($row, 'cdate');
			$prod = $this->looks_like_shop_card($row);
			if ($prod) {
				if ($row_ts >= $fresh_ts && $mid === '') {
					continue;
				}
				if ($mid !== '' && isset($live_ids[$mid])) {
					continue;
				}
				if ($mid !== '' && $row_ts > 0 && $row_ts < $window_start) {
					continue;
				}
				$drop[] = (int)$row['message_id'];
				continue;
			}
			if ($mid === '' || isset($live_ids[$mid])) {
				continue;
			}
			if ($row_ts > 0 && $row_ts < $window_start) {
				continue;
			}
			if ($row_ts >= $fresh_ts) {
				continue;
			}
			$drop[] = (int)$row['message_id'];
		}
		return $this->CI->chat_learn_model->delete_messages($drop);
	}

	function row_unix($row, $key)
	{
		if (!is_array($row) || !isset($row[$key])) {
			return 0;
		}
		$v = $row[$key];
		if ($v instanceof DateTime) {
			return $v->getTimestamp();
		}
		$s = trim((string)$v);
		if ($s === '') {
			return 0;
		}
		$t = strtotime($s);
		return ($t === false) ? 0 : $t;
	}

	function looks_like_shop_card($row)
	{
		$type = strtolower(trim((string)(isset($row['msg_type']) ? $row['msg_type'] : '')));
		if (in_array($type, array('product', 'item', 'order'), true)) {
			return true;
		}
		$body = isset($row['body']) ? (string)$row['body'] : '';
		if (preg_match('/\[สินค้า\s*\d+\]/u', $body)) {
			return true;
		}
		$ex = json_decode(isset($row['extra_json']) ? $row['extra_json'] : '', true);
		if (is_array($ex) && (!empty($ex['item_id']) || !empty($ex['order_sn']) || !empty($ex['order_id']))) {
			return true;
		}
		return false;
	}

	function parse_shopee_content($m)
	{
		$type = isset($m['message_type']) ? strtolower((string)$m['message_type']) : 'text';
		$c = isset($m['content']) ? $m['content'] : '';
		if (is_string($c)) {
			$j = json_decode($c, true);
			if (is_array($j)) {
				$c = $j;
			}
		}
		$body = '';
		$extra = array();
		if (is_array($c)) {
			$extra = $c;
			$body = $this->extract_payload_text($c);
			if (isset($c['item_id'])) {
				$type = 'product';
			} elseif (isset($c['order_sn'])) {
				$type = 'order';
			} elseif (isset($c['image_url'])) {
				$type = 'image';
			}
		} else {
			$body = $this->readable_text($c);
		}
		if ($body === '' && $type !== '' && $type !== 'text') {
			if ($type === 'bundle_message') {
				$body = '[ชุดสินค้า/ข้อความ]';
			} else {
				$body = '['.$type.']';
			}
		}
		return array('body' => $body, 'type' => $type, 'extra' => $extra);
	}

	function shopee_send($thread, $message_type, $content)
	{
		$to = $this->ensure_shopee_to_id($thread);
		if ($to === '') {
			return array('ok' => false, 'error' => 'missing_to_id');
		}
		$payload = array(
			'to_id' => preg_match('/^\d+$/', (string)$to) ? (int)$to : $to,
			'message_type' => $message_type,
			'content' => $content
		);
		$res = $this->shopee_call('/api/v2/sellerchat/send_message', 'post', $payload);
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err']);
		}
		if (!empty($res['error']) && (string)$res['error'] !== '') {
			$msg = $res['error'];
			if (!empty($res['message'])) {
				$msg .= ' '.$res['message'];
			}
			return array('ok' => false, 'error' => $msg);
		}
		return array('ok' => true, 'error' => '', 'raw' => $res);
	}

	function shopee_search_products($q)
	{
		$q = trim((string)$q);
		$terms = $this->product_search_terms($q);
		$out = array();
		$seen = array();
		foreach ($terms as $term) {
			if ($term === '') {
				continue;
			}
			foreach (array('item_sku', 'item_name') as $field) {
				$res = $this->shopee_call('/api/v2/product/search_item', 'get', array(
					'page_size' => 20,
					'offset' => 0,
					$field => $term
				));
				$ids = array();
				if (isset($res['response']['item_id_list']) && is_array($res['response']['item_id_list'])) {
					$ids = $res['response']['item_id_list'];
				}
				foreach ($this->shopee_items_info($ids) as $row) {
					$this->push_product($out, $seen, $row);
				}
				if (count($out) >= 12) {
					break 2;
				}
			}
		}
		if (count($out) < 12) {
			foreach ($this->shopee_listed_products() as $row) {
				if ($q === '' || $this->product_matches($row, $terms)) {
					$this->push_product($out, $seen, $row);
				}
				if (count($out) >= 16) {
					break;
				}
			}
		}
		foreach ($this->shopee_products_from_shop_db($q) as $row) {
			$this->push_product($out, $seen, $row);
			if (count($out) >= 16) {
				break;
			}
		}
		$need = array();
		foreach ($out as $row) {
			if ((empty($row['image']) || $row['name'] === '') && !empty($row['id'])) {
				$need[] = $row['id'];
			}
		}
		if (!empty($need)) {
			$info = $this->shopee_items_info($need);
			foreach ($out as $i => $row) {
				$id = $row['id'];
				if (isset($info[$id])) {
					if ($out[$i]['name'] === '' && $info[$id]['name'] !== '') {
						$out[$i]['name'] = $info[$id]['name'];
					}
					if (empty($out[$i]['image']) && !empty($info[$id]['image'])) {
						$out[$i]['image'] = $info[$id]['image'];
					}
					if (empty($out[$i]['sku']) && !empty($info[$id]['sku'])) {
						$out[$i]['sku'] = $info[$id]['sku'];
					}
				}
			}
		}
		return array_slice(array_values($out), 0, 16);
	}

	function product_search_terms($q)
	{
		$q = trim((string)$q);
		$terms = array();
		if ($q !== '') {
			$terms[$q] = $q;
		}
		$alias = $this->sku_alias($q);
		if ($alias !== '' && !isset($terms[$alias])) {
			$terms[$alias] = $alias;
		}
		return array_values($terms);
	}

	function sku_alias($q)
	{
		$q = trim((string)$q);
		if ($q === '') {
			return '';
		}
		$map = array('เอส' => 'S', 'เอสทู' => 'S2');
		if (isset($map[$q])) {
			return $map[$q];
		}
		if (function_exists('mb_strpos') && mb_strpos($q, 'เอส', 0, 'UTF-8') === 0) {
			$rest = function_exists('mb_substr') ? mb_substr($q, 3, null, 'UTF-8') : substr($q, strlen('เอส'));
			return 'S'.$rest;
		}
		return $q;
	}

	function product_matches($row, $terms)
	{
		if (empty($terms)) {
			return true;
		}
		$hay = '';
		if (!empty($row['name'])) {
			$hay .= ' '.$row['name'];
		}
		if (!empty($row['sku'])) {
			$hay .= ' '.$row['sku'];
		}
		if (!empty($row['id'])) {
			$hay .= ' '.$row['id'];
		}
		$hay = trim($hay);
		if ($hay === '') {
			return false;
		}
		foreach ((array)$terms as $term) {
			$term = trim((string)$term);
			if ($term === '') {
				continue;
			}
			if (function_exists('mb_stripos')) {
				if (mb_stripos($hay, $term, 0, 'UTF-8') !== false) {
					return true;
				}
			} elseif (stripos($hay, $term) !== false) {
				return true;
			}
		}
		return false;
	}

	function like_escape($q)
	{
		return str_replace(array('[', '%', '_'), array('[[]', '[%]', '[_]'), (string)$q);
	}

	function resolve_shopee_item_id($raw)
	{
		$raw = trim((string)$raw);
		if ($raw === '') {
			return '';
		}
		if (preg_match('/^\d{6,}$/', $raw)) {
			return $raw;
		}
		$this->CI->db->select('item_id');
		$this->CI->db->from('shopee_escrow_items');
		$this->CI->db->where('item_sku', $raw);
		$this->CI->db->limit(1);
		$q = $this->CI->db->get();
		$row = $q ? $q->row_array() : null;
		if (!empty($row['item_id'])) {
			return (string)$row['item_id'];
		}
		$this->CI->db->select('item_id');
		$this->CI->db->from('shopee_escrow_items');
		$this->CI->db->like('item_sku', $raw, 'after');
		$this->CI->db->limit(1);
		$q2 = $this->CI->db->get();
		$row2 = $q2 ? $q2->row_array() : null;
		if (!empty($row2['item_id'])) {
			return (string)$row2['item_id'];
		}
		$found = $this->shopee_search_products($raw);
		if (!empty($found[0]['id']) && preg_match('/^\d+$/', (string)$found[0]['id'])) {
			return (string)$found[0]['id'];
		}
		return '';
	}

	function shopee_listed_products()
	{
		$res = $this->shopee_call('/api/v2/product/get_item_list', 'get', array(
			'offset' => 0,
			'page_size' => 30,
			'item_status' => 'NORMAL'
		));
		$ids = array();
		$list = array();
		if (isset($res['response']['item']) && is_array($res['response']['item'])) {
			$list = $res['response']['item'];
		} elseif (isset($res['response']['item_list']) && is_array($res['response']['item_list'])) {
			$list = $res['response']['item_list'];
		}
		foreach ($list as $it) {
			if (is_array($it) && isset($it['item_id'])) {
				$ids[] = $it['item_id'];
			} elseif (is_numeric($it)) {
				$ids[] = $it;
			}
		}
		return array_values($this->shopee_items_info($ids));
	}

	function push_product(&$out, &$seen, $row)
	{
		$id = isset($row['id']) ? (string)$row['id'] : '';
		if ($id === '' || isset($seen[$id])) {
			return;
		}
		$seen[$id] = 1;
		$out[] = array(
			'id' => $id,
			'name' => isset($row['name']) ? (string)$row['name'] : '',
			'sku' => isset($row['sku']) ? (string)$row['sku'] : '',
			'image' => isset($row['image']) ? (string)$row['image'] : '',
			'note' => isset($row['note']) ? (string)$row['note'] : ''
		);
	}

	function shopee_items_info($ids)
	{
		$out = array();
		$clean = array();
		foreach ((array)$ids as $id) {
			$id = trim((string)$id);
			if ($id !== '' && preg_match('/^\d+$/', $id)) {
				$clean[$id] = $id;
			}
		}
		if (empty($clean)) {
			return $out;
		}
		$info = $this->shopee_call('/api/v2/product/get_item_base_info', 'get', array(
			'item_id_list' => implode(',', array_slice(array_values($clean), 0, 20))
		));
		$list = array();
		if (isset($info['response']['item_list']) && is_array($info['response']['item_list'])) {
			$list = $info['response']['item_list'];
		}
		foreach ($list as $it) {
			$id = isset($it['item_id']) ? (string)$it['item_id'] : '';
			if ($id === '') {
				continue;
			}
			$img = '';
			if (isset($it['image']['image_url_list'][0])) {
				$img = $it['image']['image_url_list'][0];
			} elseif (isset($it['image']['image_url'])) {
				$img = $it['image']['image_url'];
			}
			$out[$id] = array(
				'id' => $id,
				'name' => isset($it['item_name']) ? (string)$it['item_name'] : '',
				'sku' => isset($it['item_sku']) ? (string)$it['item_sku'] : '',
				'image' => $img
			);
		}
		return $out;
	}

	function shopee_products_from_shop_db($q)
	{
		$out = array();
		$seen = array();
		$terms = $this->product_search_terms($q);
		if (empty($terms)) {
			$sql = "SELECT TOP 20 item_id,
					MAX(item_name) AS item_name,
					MAX(item_sku) AS item_sku
				FROM dbo.shopee_escrow_items
				WHERE item_id IS NOT NULL AND CAST(item_id AS VARCHAR(40)) <> ''
				GROUP BY item_id
				ORDER BY MAX(item_id) DESC";
			$qres = $this->CI->db->query($sql);
			$rows = $qres ? $qres->result_array() : array();
			foreach ($rows as $r) {
				$id = (string)$r['item_id'];
				if (isset($seen[$id])) {
					continue;
				}
				$seen[$id] = 1;
				$out[] = array(
					'id' => $id,
					'name' => isset($r['item_name']) ? $r['item_name'] : '',
					'sku' => isset($r['item_sku']) ? $r['item_sku'] : ''
				);
			}
			return $out;
		}
		foreach ($terms as $term) {
			$esc = $this->like_escape($term);
			$pre = $esc.'%';
			$mid = '%'.$esc.'%';
			$sql = "SELECT TOP 20 item_id,
					MAX(item_name) AS item_name,
					MAX(item_sku) AS item_sku
				FROM dbo.shopee_escrow_items
				WHERE item_id IS NOT NULL AND CAST(item_id AS VARCHAR(40)) <> ''
					AND (item_sku LIKE ? OR item_name LIKE ? OR item_sku LIKE ? OR item_name LIKE ?)
				GROUP BY item_id
				ORDER BY MIN(CASE
					WHEN item_sku LIKE ? THEN 0
					WHEN item_name LIKE ? THEN 1
					ELSE 2
				END)";
			$qres = $this->CI->db->query($sql, array($pre, $pre, $mid, $mid, $pre, $pre));
			$rows = $qres ? $qres->result_array() : array();
			foreach ($rows as $r) {
				$id = (string)$r['item_id'];
				if (isset($seen[$id])) {
					continue;
				}
				$seen[$id] = 1;
				$out[] = array(
					'id' => $id,
					'name' => isset($r['item_name']) ? $r['item_name'] : '',
					'sku' => isset($r['item_sku']) ? $r['item_sku'] : ''
				);
			}
		}
		if (count($out) >= 8) {
			return $out;
		}
		foreach ($terms as $term) {
			$this->CI->db->select('item_name, item_sku');
			$this->CI->db->from('shopee_orderitems');
			$this->CI->db->group_start();
			$this->CI->db->like('item_name', $term);
			$this->CI->db->or_like('item_sku', $term);
			$this->CI->db->group_end();
			$this->CI->db->limit(15);
			$q2 = $this->CI->db->get();
			$more = $q2 ? $q2->result_array() : array();
			foreach ($more as $r) {
				$sku = isset($r['item_sku']) ? trim((string)$r['item_sku']) : '';
				$id = '';
				if ($sku !== '') {
					$this->CI->db->select('item_id');
					$this->CI->db->from('shopee_escrow_items');
					$this->CI->db->where('item_sku', $sku);
					$this->CI->db->limit(1);
					$e = $this->CI->db->get();
					$er = $e ? $e->row_array() : null;
					if (!empty($er['item_id'])) {
						$id = (string)$er['item_id'];
					}
				}
				if ($id === '' || isset($seen[$id])) {
					continue;
				}
				$seen[$id] = 1;
				$out[] = array(
					'id' => $id,
					'name' => isset($r['item_name']) ? $r['item_name'] : '',
					'sku' => $sku
				);
			}
		}
		return $out;
	}

	function shopee_orders($thread, $limit)
	{
		$out = array();
		$name = isset($thread['buyer_name']) ? trim($thread['buyer_name']) : '';
		$sns = array();
		if ($name !== '') {
			$this->CI->db->select('order_sn, name');
			$this->CI->db->from('shopee_shipping_address');
			$this->CI->db->like('name', $name);
			$this->CI->db->order_by('order_sn', 'desc');
			$this->CI->db->limit(40);
			$q = $this->CI->db->get();
			$rows = $q ? $q->result_array() : array();
			foreach ($rows as $r) {
				$sns[$r['order_sn']] = 1;
			}
		}
		$hint = $this->order_hints_from_thread($thread['thread_id']);
		foreach ($hint as $h) {
			$sns[$h] = 1;
		}
		$n = 0;
		foreach (array_keys($sns) as $sn) {
			$this->CI->db->from('shopee_orders');
			$this->CI->db->where('order_sn', $sn);
			$this->CI->db->order_by('create_time', 'desc');
			$this->CI->db->limit(1);
			$oq = $this->CI->db->get();
			$orow = $oq ? $oq->row_array() : null;
			if (empty($orow)) {
				continue;
			}
			$items = $this->item_names('shopee_orderitems', 'order_sn', $sn, 'item_name');
			$out[] = array(
				'order_id' => $sn,
				'status' => $orow['order_status'],
				'amount' => isset($orow['total_amount']) ? $orow['total_amount'] : '',
				'when' => isset($orow['create_time']) ? $orow['create_time'] : '',
				'items' => $items
			);
			$n++;
			if ($n >= $limit) {
				break;
			}
		}
		return $out;
	}

	function ensure_lazada_token($force = false)
	{
		include_once APPPATH.'third_party/api/lazada/LazopSdk.php';
		$this->CI->load->model('laztoken_model');
		$row = $this->CI->laztoken_model->getlatesttoken();
		if (empty($row) || empty($row->refreshtoken) || $row->refreshtoken === '0') {
			return false;
		}
		$life = isset($row->litetime) ? (int)$row->litetime : 0;
		if (!$force && $life >= 10) {
			return true;
		}
		$c = new LazopClient($this->CI->config->item('lazAuthAPI'), $this->CI->config->item('Appkey'), $this->CI->config->item('Secret'));
		$request = new LazopRequest('/auth/token/refresh');
		$request->addApiParam('refresh_token', $row->refreshtoken);
		$response = $c->execute($request);
		$decoded = json_decode($response, true);
		if (!is_array($decoded) || empty($decoded['access_token'])) {
			log_message('error', 'lazada token refresh failed');
			return false;
		}
		$this->CI->laztoken_model->insert_token(array(
			'code' => $row->code,
			'token' => $decoded['access_token'],
			'refreshtoken' => isset($decoded['refresh_token']) ? $decoded['refresh_token'] : $row->refreshtoken,
			'refresh_expires_in' => isset($decoded['refresh_expires_in']) ? $decoded['refresh_expires_in'] : $row->refresh_expires_in
		));
		return true;
	}

	function lazada_client()
	{
		$this->ensure_lazada_token();
		include_once APPPATH.'third_party/api/lazada/LazopSdk.php';
		$this->CI->load->model('laztoken_model');
		$token_row = $this->CI->laztoken_model->getlatesttoken();
		$token = '';
		if (is_object($token_row) && !empty($token_row->token)) {
			$token = $token_row->token;
		} elseif (is_array($token_row) && !empty($token_row['token'])) {
			$token = $token_row['token'];
		}
		if ($token === '' || $token === '0') {
			return array('ok' => false, 'error' => 'lazada_token');
		}
		$c = new LazopClient($this->CI->config->item('lazAPI'), $this->CI->config->item('Appkey'), $this->CI->config->item('Secret'));
		return array('ok' => true, 'error' => '', 'c' => $c, 'token' => $token);
	}

	function lazada_exec($path, $http, $params)
	{
		$cli = $this->lazada_client();
		if (empty($cli['ok'])) {
			return $cli;
		}
		$request = new LazopRequest($path, $http);
		foreach ($params as $k => $v) {
			$request->addApiParam($k, (string)$v);
		}
		$raw = $cli['c']->execute($request, $cli['token']);
		$data = json_decode($raw, true);
		if (!is_array($data)) {
			return array('ok' => false, 'error' => 'lazada_empty', 'data' => array());
		}
		if (isset($data['code']) && (string)$data['code'] !== '0' && (string)$data['code'] !== '200') {
			$err = isset($data['message']) ? $data['message'] : (string)$data['code'];
			return array('ok' => false, 'error' => $err, 'data' => $data);
		}
		return array('ok' => true, 'error' => '', 'data' => $data);
	}

	function sync_lazada_list()
	{
		$now = (string)round(microtime(true) * 1000);
		$res = $this->lazada_exec('/im/session/list', 'GET', array(
			'start_time' => $now,
			'page_size' => '30'
		));
		if (empty($res['ok'])) {
			return array('ok' => false, 'error' => $res['error'], 'n' => 0);
		}
		$list = array();
		$d = $res['data'];
		if (isset($d['data']['session_list']) && is_array($d['data']['session_list'])) {
			$list = $d['data']['session_list'];
		} elseif (isset($d['data']['list']) && is_array($d['data']['list'])) {
			$list = $d['data']['list'];
		} elseif (isset($d['data']) && is_array($d['data']) && isset($d['data'][0])) {
			$list = $d['data'];
		}
		$n = 0;
		foreach ($list as $row) {
			$this->upsert_lazada_session($row);
			$n++;
		}
		return array('ok' => true, 'error' => '', 'n' => $n);
	}

	function upsert_lazada_session($row)
	{
		$cid = isset($row['session_id']) ? $row['session_id'] : '';
		if ($cid === '') {
			return 0;
		}
		$buyer = '';
		if (isset($row['title'])) {
			$buyer = $row['title'];
		} elseif (isset($row['buyer_nick'])) {
			$buyer = $row['buyer_nick'];
		} elseif (isset($row['user_nick'])) {
			$buyer = $row['user_nick'];
		}
		$buyer_id = '';
		if (isset($row['buyer_id'])) {
			$buyer_id = $row['buyer_id'];
		} elseif (isset($row['to_account_id'])) {
			$buyer_id = $row['to_account_id'];
		}
		$preview = '';
		if (isset($row['last_message'])) {
			$preview = is_string($row['last_message']) ? $row['last_message'] : '';
		} elseif (isset($row['summary'])) {
			$preview = $row['summary'];
		}
		$ts = 0;
		if (isset($row['last_message_time'])) {
			$ts = $row['last_message_time'];
		} elseif (isset($row['gmt_modified'])) {
			$ts = $row['gmt_modified'];
		}
		$unread = isset($row['unread_count']) ? (int)$row['unread_count'] : 0;
		$avatar = $this->pick_avatar($row);
		$last_from = $this->conv_last_from($row, '', $buyer_id);
		if ($last_from === '' && $unread > 0) {
			$last_from = 'buyer';
		}
		$extra = array('session_id' => $cid, 'buyer_id' => $buyer_id);
		if ($avatar !== '') {
			$extra['avatar'] = $avatar;
		}
		if ($last_from !== '') {
			$extra['last_from'] = $last_from;
		}
		$upd = array(
			'buyer_name' => $buyer !== '' ? $buyer : null,
			'buyer_id' => $buyer_id !== '' ? (string)$buyer_id : null,
			'last_preview' => $this->preview($preview),
			'unread' => $unread,
			'last_message_at' => $ts ? $this->unix_to_dt($ts) : date('Y-m-d H:i:s'),
			'extra_json' => json_encode($extra, JSON_UNESCAPED_UNICODE)
		);
		if ($avatar !== '') {
			$upd['buyer_avatar'] = $avatar;
		}
		if ($last_from !== '') {
			$upd['last_from'] = $last_from;
		}
		return $this->CI->chat_learn_model->upsert_thread('lazada', $cid, $upd);
	}

	function sync_lazada_messages($thread)
	{
		$cid = $thread['platform_conv_id'];
		$now = (string)round(microtime(true) * 1000);
		$res = $this->lazada_exec('/im/message/list', 'GET', array(
			'session_id' => $cid,
			'start_time' => $now,
			'page_size' => '50'
		));
		if (empty($res['ok'])) {
			return array('ok' => false, 'error' => $res['error'], 'added' => 0);
		}
		$list = array();
		$d = $res['data'];
		if (isset($d['data']['message_list']) && is_array($d['data']['message_list'])) {
			$list = $d['data']['message_list'];
		} elseif (isset($d['data']['list']) && is_array($d['data']['list'])) {
			$list = $d['data']['list'];
		} elseif (isset($d['data']) && is_array($d['data']) && isset($d['data'][0])) {
			$list = $d['data'];
		}
		$added = 0;
		$last_body = '';
		$last_at = '';
		$last_from = '';
		foreach ($list as $m) {
			$mid = isset($m['message_id']) ? (string)$m['message_id'] : (isset($m['msg_id']) ? (string)$m['msg_id'] : '');
			$from_type = isset($m['from_account_type']) ? (int)$m['from_account_type'] : 0;
			$is_out = ($from_type === 2);
			$last_from = $is_out ? 'shop' : 'buyer';
			$parsed = $this->parse_lazada_content($m);
			$id = $this->store_inbound_outbound(
				$thread['thread_id'],
				$is_out ? 'out' : 'in',
				$parsed['body'],
				$is_out ? 'shop' : 'buyer',
				$mid,
				$parsed['type'],
				$parsed['extra']
			);
			if ($id) {
				$added++;
				$last_body = $parsed['body'];
				if (isset($m['send_time'])) {
					$last_at = $this->unix_to_dt($m['send_time']);
				}
			}
		}
		$upd = array();
		if ($last_from !== '') {
			$upd['last_from'] = $last_from;
		}
		if ($last_body !== '') {
			$upd['last_preview'] = $this->preview($last_body);
		}
		if ($last_at !== '') {
			$upd['last_message_at'] = $last_at;
		}
		$this->CI->chat_learn_model->update_thread($thread['thread_id'], $upd);
		return array('ok' => true, 'error' => '', 'added' => $added);
	}

	function parse_lazada_content($m)
	{
		$tid = isset($m['template_id']) ? (int)$m['template_id'] : 1;
		$type = 'text';
		if ($tid === 10006) {
			$type = 'product';
		} elseif ($tid === 10007) {
			$type = 'order';
		} elseif ($tid === 3) {
			$type = 'image';
		}
		$content = isset($m['content']) ? $m['content'] : '';
		if (is_string($content)) {
			$j = json_decode($content, true);
			if (is_array($j)) {
				$content = $j;
			}
		}
		$body = '';
		$extra = array();
		if (is_array($content)) {
			$extra = $content;
			if (isset($content['txt'])) {
				$body = $content['txt'];
			} elseif (isset($content['item_id'])) {
				$body = '[สินค้า '.$content['item_id'].']';
			} elseif (isset($content['order_id'])) {
				$body = '[ออเดอร์ '.$content['order_id'].']';
			}
		} else {
			$body = (string)$content;
		}
		if ($body === '' && isset($m['txt'])) {
			$body = $m['txt'];
		}
		return array('body' => $body, 'type' => $type, 'extra' => $extra);
	}

	function lazada_send($thread, $template_id, $params)
	{
		$cid = $thread['platform_conv_id'];
		$body = array(
			'session_id' => $cid,
			'template_id' => (string)$template_id
		);
		foreach ($params as $k => $v) {
			$body[$k] = $v;
		}
		$res = $this->lazada_exec('/im/message/send', 'POST', $body);
		if (empty($res['ok'])) {
			return array('ok' => false, 'error' => $res['error']);
		}
		return array('ok' => true, 'error' => '', 'raw' => $res['data']);
	}

	function lazada_search_products($q)
	{
		$params = array(
			'filter' => 'all',
			'offset' => '0',
			'limit' => '20'
		);
		if ($q !== '') {
			$params['search'] = $q;
		}
		$res = $this->lazada_exec('/products/get', 'GET', $params);
		$out = array();
		if (empty($res['ok'])) {
			return $this->lazada_products_from_orders($q);
		}
		$list = array();
		$d = $res['data'];
		if (isset($d['data']['products']) && is_array($d['data']['products'])) {
			$list = $d['data']['products'];
		}
		foreach ($list as $it) {
			$id = isset($it['item_id']) ? (string)$it['item_id'] : '';
			$name = '';
			if (isset($it['attributes']['name'])) {
				$name = $it['attributes']['name'];
			} elseif (isset($it['skus'][0]['SellerSku'])) {
				$name = $it['skus'][0]['SellerSku'];
			}
			$sku = '';
			if (isset($it['skus'][0]['SellerSku'])) {
				$sku = $it['skus'][0]['SellerSku'];
			}
			if ($id === '') {
				continue;
			}
			$img = '';
			if (!empty($it['images'][0])) {
				$img = $it['images'][0];
			} elseif (!empty($it['skus'][0]['Images'][0])) {
				$img = $it['skus'][0]['Images'][0];
			} elseif (!empty($it['skus'][0]['images'][0])) {
				$img = $it['skus'][0]['images'][0];
			}
			$out[] = array('id' => $id, 'name' => $name, 'sku' => $sku, 'image' => $img);
		}
		if (empty($out) && $q !== '') {
			return $this->lazada_products_from_orders($q);
		}
		return $out;
	}

	function lazada_products_from_orders($q)
	{
		$out = array();
		$this->CI->db->select('name, sku');
		$this->CI->db->from('lazada_orderitems');
		if ($q !== '') {
			$this->CI->db->like('name', $q);
		}
		$this->CI->db->limit(15);
		$qr = $this->CI->db->get();
		$rows = $qr ? $qr->result_array() : array();
		foreach ($rows as $r) {
			$out[] = array(
				'id' => isset($r['sku']) ? (string)$r['sku'] : '',
				'name' => isset($r['name']) ? $r['name'] : '',
				'sku' => isset($r['sku']) ? $r['sku'] : '',
				'note' => 'จากออเดอร์ — ส่งการ์ดต้องใช้ item_id จากค้นหาบน Lazada'
			);
		}
		return $out;
	}

	function lazada_orders($thread, $limit)
	{
		$out = array();
		$hints = $this->order_hints_from_thread($thread['thread_id']);
		$name = isset($thread['buyer_name']) ? trim($thread['buyer_name']) : '';
		$sns = array();
		foreach ($hints as $h) {
			$sns[$h] = 1;
		}
		if ($name !== '') {
			$this->CI->db->select('order_number, first_name, last_name');
			$this->CI->db->from('lazada_shipping_address');
			$this->CI->db->like('first_name', $name);
			$this->CI->db->limit(20);
			$q = $this->CI->db->get();
			if ($q) {
				foreach ($q->result_array() as $r) {
					if (!empty($r['order_number'])) {
						$sns[$r['order_number']] = 1;
					}
				}
			}
		}
		$n = 0;
		foreach (array_keys($sns) as $sn) {
			$this->CI->db->from('lazada_orders');
			$this->CI->db->where('order_number', $sn);
			$this->CI->db->order_by('created_at', 'desc');
			$this->CI->db->limit(1);
			$oq = $this->CI->db->get();
			$orow = $oq ? $oq->row_array() : null;
			if (empty($orow)) {
				continue;
			}
			$out[] = array(
				'order_id' => (string)$sn,
				'status' => isset($orow['status']) ? $orow['status'] : '',
				'amount' => isset($orow['price']) ? $orow['price'] : '',
				'when' => isset($orow['created_at']) ? $orow['created_at'] : '',
				'items' => $this->item_names('lazada_orderitems', 'order_number', $sn, 'name')
			);
			$n++;
			if ($n >= $limit) {
				break;
			}
		}
		return $out;
	}

	function tiktok_request($method, $path, $query, $body = null)
	{
		$this->CI->load->library('businesslogic/tiktok_bl');
		$this->CI->load->model('tiktok_token_model');
		if (!defined('TIKTOK_KEY') || !defined('TIKTOK_SECRET') || !defined('TIKTOK_API_URL')) {
			return array('_err' => 'tiktok_config');
		}
		$timestamp = $this->CI->date_util->get_date_now_unix();
		if (empty($query) || !is_array($query)) {
			$query = array();
		}
		if ($path !== '/authorization/202309/shops' && !isset($query['shop_cipher']) && defined('TIKTOK_SHOP_CIPHER')) {
			$query['shop_cipher'] = TIKTOK_SHOP_CIPHER;
		}
		$sign_params = $query;
		$sign_params['app_key'] = TIKTOK_KEY;
		$sign_params['timestamp'] = $timestamp;
		ksort($sign_params);
		$input = '';
		foreach ($sign_params as $key => $value) {
			$input .= $key.$value;
		}
		$method_u = strtoupper($method);
		if ($method_u !== 'GET' && $body !== null) {
			$input .= json_encode($body);
		}
		$input = $path.$input;
		$input = TIKTOK_SECRET.$input.TIKTOK_SECRET;
		$sign = bin2hex(hash_hmac('sha256', $input, TIKTOK_SECRET, true));
		$url = TIKTOK_API_URL.$path;
		$qs = $sign_params;
		$qs['sign'] = $sign;
		$num = 1;
		foreach ($qs as $key => $value) {
			$url .= ($num === 1 ? '?' : '&').$key.'='.$value;
			$num++;
		}
		$res = $this->CI->tiktok_bl->CallApiToken($method_u, $url, $body);
		if (!is_array($res)) {
			return array('_err' => 'tiktok_empty');
		}
		return $res;
	}

	function sync_tiktok_list()
	{
		$res = $this->tiktok_request('GET', '/customer_service/202309/conversations', array(
			'page_size' => 20
		), null);
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err'], 'n' => 0);
		}
		if (isset($res['code']) && (int)$res['code'] !== 0) {
			$err = isset($res['message']) ? $res['message'] : 'tiktok_error';
			return array('ok' => false, 'error' => $err, 'n' => 0);
		}
		$list = array();
		if (isset($res['data']['conversations']) && is_array($res['data']['conversations'])) {
			$list = $res['data']['conversations'];
		} elseif (isset($res['data']['conversation_list']) && is_array($res['data']['conversation_list'])) {
			$list = $res['data']['conversation_list'];
		}
		$n = 0;
		foreach ($list as $row) {
			$this->upsert_tiktok_conv($row);
			$n++;
		}
		return array('ok' => true, 'error' => '', 'n' => $n);
	}

	function upsert_tiktok_conv($row)
	{
		$cid = isset($row['conversation_id']) ? $row['conversation_id'] : '';
		if ($cid === '') {
			return 0;
		}
		$name = '';
		$buyer_id = '';
		if (isset($row['participant']['user_id'])) {
			$buyer_id = $row['participant']['user_id'];
			if (isset($row['participant']['nickname'])) {
				$name = $row['participant']['nickname'];
			}
		} elseif (isset($row['buyer_user_id'])) {
			$buyer_id = $row['buyer_user_id'];
		}
		if (isset($row['participants']) && is_array($row['participants'])) {
			foreach ($row['participants'] as $p) {
				$role = isset($p['role']) ? strtoupper((string)$p['role']) : '';
				if ($role === 'BUYER' || $role === '') {
					if (!empty($p['user_id'])) {
						$buyer_id = $p['user_id'];
					}
					if (!empty($p['nickname'])) {
						$name = $p['nickname'];
					}
					if (!empty($p['name'])) {
						$name = $p['name'];
					}
				}
			}
		}
		$preview = '';
		if (isset($row['latest_message']['content'])) {
			$preview = is_string($row['latest_message']['content']) ? $row['latest_message']['content'] : '';
		}
		$ts = isset($row['update_time']) ? $row['update_time'] : (isset($row['create_time']) ? $row['create_time'] : 0);
		$unread = isset($row['unread_count']) ? (int)$row['unread_count'] : 0;
		$avatar = $this->pick_avatar($row);
		$last_from = $this->conv_last_from($row, '', $buyer_id);
		if ($last_from === '' && $unread > 0) {
			$last_from = 'buyer';
		}
		$extra = array('buyer_user_id' => $buyer_id);
		if ($avatar !== '') {
			$extra['avatar'] = $avatar;
		}
		if ($last_from !== '') {
			$extra['last_from'] = $last_from;
		}
		$upd = array(
			'buyer_name' => $name !== '' ? $name : null,
			'buyer_id' => $buyer_id !== '' ? (string)$buyer_id : null,
			'last_preview' => $this->preview($preview),
			'unread' => $unread,
			'last_message_at' => $ts ? $this->unix_to_dt($ts) : date('Y-m-d H:i:s'),
			'extra_json' => json_encode($extra, JSON_UNESCAPED_UNICODE)
		);
		if ($avatar !== '') {
			$upd['buyer_avatar'] = $avatar;
		}
		if ($last_from !== '') {
			$upd['last_from'] = $last_from;
		}
		return $this->CI->chat_learn_model->upsert_thread('tiktok', $cid, $upd);
	}

	function sync_tiktok_messages($thread)
	{
		$cid = $thread['platform_conv_id'];
		$path = '/customer_service/202309/conversations/'.$cid.'/messages';
		$res = $this->tiktok_request('GET', $path, array('page_size' => 10), null);
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err'], 'added' => 0);
		}
		if (isset($res['code']) && (int)$res['code'] !== 0) {
			$err = isset($res['message']) ? $res['message'] : 'tiktok_error';
			return array('ok' => false, 'error' => $err, 'added' => 0);
		}
		$list = array();
		if (isset($res['data']['messages']) && is_array($res['data']['messages'])) {
			$list = $res['data']['messages'];
		}
		$added = 0;
		$last_body = '';
		$last_at = '';
		$last_from = '';
		foreach ($list as $m) {
			$mid = isset($m['id']) ? (string)$m['id'] : (isset($m['message_id']) ? (string)$m['message_id'] : '');
			$role = '';
			if (isset($m['sender']['role'])) {
				$role = strtoupper((string)$m['sender']['role']);
			} elseif (isset($m['from'])) {
				$role = strtoupper((string)$m['from']);
			}
			$is_out = ($role === 'SELLER' || $role === 'SHOP' || $role === 'AGENT');
			$last_from = $is_out ? 'shop' : 'buyer';
			$parsed = $this->parse_tiktok_content($m);
			$id = $this->store_inbound_outbound(
				$thread['thread_id'],
				$is_out ? 'out' : 'in',
				$parsed['body'],
				$is_out ? 'shop' : 'buyer',
				$mid,
				$parsed['type'],
				$parsed['extra']
			);
			if ($id) {
				$added++;
				$last_body = $parsed['body'];
				if (isset($m['create_time'])) {
					$last_at = $this->unix_to_dt($m['create_time']);
				}
			}
		}
		$upd = array();
		if ($last_from !== '') {
			$upd['last_from'] = $last_from;
		}
		if ($last_body !== '') {
			$upd['last_preview'] = $this->preview($last_body);
		}
		if ($last_at !== '') {
			$upd['last_message_at'] = $last_at;
		}
		if (!empty($upd)) {
			$this->CI->chat_learn_model->update_thread($thread['thread_id'], $upd);
		}
		return array('ok' => true, 'error' => '', 'added' => $added);
	}

	function parse_tiktok_content($m)
	{
		$type = isset($m['type']) ? strtolower((string)$m['type']) : 'text';
		$c = isset($m['content']) ? $m['content'] : '';
		if (is_string($c)) {
			$j = json_decode($c, true);
			if (is_array($j)) {
				$c = $j;
			}
		}
		$body = '';
		$extra = array();
		if (is_array($c)) {
			$extra = $c;
			if (isset($c['content'])) {
				$body = $c['content'];
			} elseif (isset($c['text'])) {
				$body = $c['text'];
			} elseif (isset($c['product_id'])) {
				$body = '[สินค้า '.$c['product_id'].']';
				$type = 'product';
			} elseif (isset($c['order_id'])) {
				$body = '[ออเดอร์ '.$c['order_id'].']';
				$type = 'order';
			}
		} else {
			$body = (string)$c;
		}
		return array('body' => $body, 'type' => $type, 'extra' => $extra);
	}

	function tiktok_send($thread, $type, $content)
	{
		$cid = $thread['platform_conv_id'];
		$path = '/customer_service/202309/conversations/'.$cid.'/messages';
		$body = array(
			'type' => $type,
			'content' => json_encode($content)
		);
		$res = $this->tiktok_request('POST', $path, array(), $body);
		if (isset($res['_err'])) {
			return array('ok' => false, 'error' => $res['_err']);
		}
		if (isset($res['code']) && (int)$res['code'] !== 0) {
			$err = isset($res['message']) ? $res['message'] : 'tiktok_error';
			return array('ok' => false, 'error' => $err);
		}
		return array('ok' => true, 'error' => '', 'raw' => $res);
	}

	function tiktok_search_products($q)
	{
		$body = array(
			'page_size' => 20,
			'status' => 'ACTIVATE'
		);
		$res = $this->tiktok_request('POST', '/product/202309/products/search', array(), $body);
		$out = array();
		$list = array();
		if (isset($res['data']['products']) && is_array($res['data']['products'])) {
			$list = $res['data']['products'];
		}
		foreach ($list as $it) {
			$id = isset($it['id']) ? (string)$it['id'] : (isset($it['product_id']) ? (string)$it['product_id'] : '');
			$name = '';
			if (isset($it['title'])) {
				$name = $it['title'];
			} elseif (isset($it['name'])) {
				$name = $it['name'];
			}
			$sku = '';
			if (isset($it['skus'][0]['seller_sku'])) {
				$sku = $it['skus'][0]['seller_sku'];
			}
			if ($id === '') {
				continue;
			}
			$img = '';
			if (!empty($it['main_images'][0]['urls'][0])) {
				$img = $it['main_images'][0]['urls'][0];
			} elseif (!empty($it['images'][0]['url'])) {
				$img = $it['images'][0]['url'];
			} elseif (!empty($it['main_image'])) {
				$img = $it['main_image'];
			}
			$hay = $name.' '.$sku;
			if ($q !== '' && $hay !== '' && function_exists('mb_stripos') && mb_stripos($hay, $q, 0, 'UTF-8') === false) {
				continue;
			}
			$out[] = array('id' => $id, 'name' => $name, 'sku' => $sku, 'image' => $img);
			if (count($out) >= 20) {
				break;
			}
		}
		if (empty($out) && $q !== '') {
			return $this->tiktok_products_from_orders($q);
		}
		return $out;
	}

	function tiktok_products_from_orders($q)
	{
		$out = array();
		$this->CI->db->select('product_id, product_name, seller_sku');
		$this->CI->db->from('tiktok_line_items');
		$this->CI->db->like('product_name', $q);
		$this->CI->db->limit(15);
		$qr = $this->CI->db->get();
		$rows = $qr ? $qr->result_array() : array();
		$seen = array();
		foreach ($rows as $r) {
			$id = (string)$r['product_id'];
			if (isset($seen[$id])) {
				continue;
			}
			$seen[$id] = 1;
			$out[] = array(
				'id' => $id,
				'name' => $r['product_name'],
				'sku' => $r['seller_sku']
			);
		}
		return $out;
	}

	function tiktok_orders($thread, $limit)
	{
		$out = array();
		$uid = isset($thread['buyer_id']) ? trim($thread['buyer_id']) : '';
		$seen = array();
		if ($uid !== '') {
			$this->CI->db->from('tiktok_orders');
			$this->CI->db->where('user_id', $uid);
			$this->CI->db->order_by('create_time', 'desc');
			$this->CI->db->limit(40);
			$q = $this->CI->db->get();
			$rows = $q ? $q->result_array() : array();
			foreach ($rows as $r) {
				$oid = $r['order_id'];
				if (isset($seen[$oid])) {
					continue;
				}
				$seen[$oid] = 1;
				$out[] = array(
					'order_id' => (string)$oid,
					'status' => $r['status'],
					'amount' => '',
					'when' => isset($r['create_time']) ? $r['create_time'] : '',
					'items' => $this->item_names('tiktok_line_items', 'order_id', $oid, 'product_name')
				);
				if (count($out) >= $limit) {
					return $out;
				}
			}
		}
		foreach ($this->order_hints_from_thread($thread['thread_id']) as $h) {
			if (isset($seen[$h])) {
				continue;
			}
			$this->CI->db->from('tiktok_orders');
			$this->CI->db->where('order_id', $h);
			$this->CI->db->order_by('create_time', 'desc');
			$this->CI->db->limit(1);
			$oq = $this->CI->db->get();
			$r = $oq ? $oq->row_array() : null;
			if (empty($r)) {
				continue;
			}
			$seen[$h] = 1;
			$out[] = array(
				'order_id' => (string)$h,
				'status' => $r['status'],
				'amount' => '',
				'when' => isset($r['create_time']) ? $r['create_time'] : '',
				'items' => $this->item_names('tiktok_line_items', 'order_id', $h, 'product_name')
			);
			if (count($out) >= $limit) {
				break;
			}
		}
		return $out;
	}

	function item_names($table, $key, $val, $name_col)
	{
		$this->CI->db->select($name_col);
		$this->CI->db->from($table);
		$this->CI->db->where($key, $val);
		$this->CI->db->limit(6);
		$q = $this->CI->db->get();
		if (!$q) {
			return '';
		}
		$names = array();
		foreach ($q->result_array() as $r) {
			if (!empty($r[$name_col])) {
				$names[] = $r[$name_col];
			}
		}
		return implode(', ', $names);
	}

	function order_hints_from_thread($thread_id)
	{
		$out = array();
		$msgs = $this->CI->chat_learn_model->messages($thread_id);
		foreach ($msgs as $m) {
			if (preg_match_all('/\b(\d{10,20})\b/', $m['body'], $mm)) {
				foreach ($mm[1] as $n) {
					$out[$n] = 1;
				}
			}
			if (!empty($m['extra_json'])) {
				$j = json_decode($m['extra_json'], true);
				if (is_array($j)) {
					foreach (array('order_sn', 'order_id', 'order_number') as $k) {
						if (!empty($j[$k])) {
							$out[(string)$j[$k]] = 1;
						}
					}
				}
			}
		}
		return array_keys($out);
	}
}
