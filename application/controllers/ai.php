<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ai extends Auth_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('util/View_util');
		$this->load->library('businesslogic/chat_learn_bl');
		$this->load->library('businesslogic/chat_platform_bl');
		$this->load->model('ai_settings_model');
		$this->load->model('chat_learn_model');
	}

	function settings()
	{
		$flash = $this->session->flashdata('ai_msg');
		$row = $this->ai_settings_model->get();
		$data = array(
			'settings' => $row,
			'flash' => $flash,
			'key_hint' => $this->key_hint(isset($row['api_key']) ? $row['api_key'] : '')
		);
		$this->render('ai/settings', $data, 'ระบบแชท');
	}

	function save_settings()
	{
		$provider = strtolower(trim((string)$this->input->post('provider')));
		if (!in_array($provider, array('openai', 'gemini', 'anthropic'), true)) {
			$provider = 'openai';
		}
		$model = trim((string)$this->input->post('model_name'));
		if ($model === '') {
			$model = 'gpt-4o-mini';
		}
		$save = array(
			'provider' => $provider,
			'model_name' => $model,
			'observe_chat' => $this->input->post('observe_chat') ? 1 : 0,
			'auto_distill' => $this->input->post('auto_distill') ? 1 : 0
		);
		$key = trim((string)$this->input->post('api_key'));
		if ($key !== '' && $key !== '********') {
			$save['api_key'] = $key;
		}
		if ($provider === 'gemini' && (strpos($model, 'gpt-') === 0 || strpos($model, 'claude') === 0)) {
			$save['model_name'] = 'gemini-2.0-flash';
		} elseif ($provider === 'openai' && (strpos($model, 'gemini') === 0 || strpos($model, 'claude') === 0)) {
			$save['model_name'] = 'gpt-4o-mini';
		} elseif ($provider === 'anthropic' && (strpos($model, 'gpt-') === 0 || strpos($model, 'gemini') === 0)) {
			$save['model_name'] = 'claude-sonnet-4-5';
		}
		$this->ai_settings_model->save($save);
		$this->session->set_flashdata('ai_msg', 'saved');
		redirect(base_url().'ai/settings');
	}

	function inbox()
	{
		$data = array(
			'boot' => $this->inbox_payload(array()),
			'flash' => $this->session->flashdata('ai_msg'),
			'sync' => $this->session->flashdata('ai_sync')
		);
		$this->render('ai/inbox', $data, 'แชท', array('ai_inbox' => base_url().'resources/js/ai_inbox.js'));
	}

	function poll_inbox()
	{
		set_time_limit(180);
		$sync = array();
		$did = false;
		if ($this->list_sync_due()) {
			$sync = $this->chat_platform_bl->sync_all();
			$did = true;
		}
		$out = $this->inbox_payload($sync);
		$out['synced'] = $did ? 1 : 0;
		$this->json_out($out);
	}

	function unreplied()
	{
		$this->json_out(array(
			'ok' => true,
			'counts' => $this->chat_learn_model->unreplied_counts()
		));
	}

	function sync_inbox()
	{
		set_time_limit(180);
		$out = $this->chat_platform_bl->sync_all();
		$names = array('shopee' => 'Shopee', 'lazada' => 'Lazada', 'tiktok' => 'TikTok');
		$parts = array();
		foreach ($out as $p => $r) {
			$lab = isset($names[$p]) ? $names[$p] : $p;
			if (!empty($r['ok'])) {
				$parts[] = $lab.' ดึงได้ '.(int)$r['n'].' ห้อง';
			} else {
				$err = isset($r['error']) ? (string)$r['error'] : 'fail';
				$low = strtolower($err);
				if (strpos($low, 'token') !== false || strpos($low, 'auth') !== false) {
					$parts[] = $lab.' โทเค็นหมดอายุ/ไม่ถูกต้อง: '.$err;
				} else {
					$parts[] = $lab.' ยังไม่มีสิทธิ์แชท: '.$err;
				}
			}
		}
		$this->session->set_flashdata('ai_sync', implode(' · ', $parts));
		redirect(base_url().'ai/inbox');
	}

	function thread()
	{
		$id = (int)$this->uri->segment(3);
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			redirect(base_url().'ai/inbox');
			return;
		}
		$sync_err = '';
		$sync_at = '';
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		if (!empty($thread['platform_conv_id'])) {
			set_time_limit(90);
			$syn = $this->chat_platform_bl->sync_thread($thread);
			$sync_at = date('H:i:s');
			if (empty($syn['ok']) && !empty($syn['error'])) {
				$sync_err = $syn['error'];
			}
			$thread = $this->chat_learn_model->get_thread($id);
		}
		$messages = $this->chat_learn_model->messages($id);
		foreach ($messages as $i => $m) {
			$messages[$i]['body'] = $this->chat_platform_bl->readable_text($m['body']);
		}
		$messages = $this->chat_platform_bl->decorate_messages($thread['platform'], $messages);
		if (strtolower((string)$thread['platform']) === 'shopee' && $this->chat_platform_bl->shopee_to_id($thread) === '') {
			$this->chat_platform_bl->ensure_shopee_to_id($thread);
			$thread = $this->chat_learn_model->get_thread($id);
		}
		$flash_draft = $this->session->flashdata('ai_draft');
		$original_draft = $this->chat_learn_model->last_coach_suggest($id);
		$suggest = ($flash_draft !== false && $flash_draft !== null && $flash_draft !== '')
			? (string)$flash_draft
			: $original_draft;
		$data = array(
			'thread' => $thread,
			'messages' => $messages,
			'coach' => $this->chat_learn_model->coach_messages($id, 80),
			'draft' => $suggest,
			'original_draft' => $original_draft,
			'draft_error' => $this->session->flashdata('ai_draft_error'),
			'flash' => $this->session->flashdata('ai_msg'),
			'sync_err' => $sync_err,
			'sync_at' => $sync_at,
			'live' => $this->chat_platform_bl->is_live($thread),
			'orders' => $this->chat_platform_bl->orders_for_thread($thread, 8),
			'attach' => $this->session->flashdata('ai_attach'),
			'db_label' => $this->chat_learn_model->db_label(),
			'has_api_key' => $this->ai_settings_model->has_api_key(),
			'suggest_attach' => $this->chat_learn_model->last_coach_attach($id)
		);
		$this->render('ai/thread', $data, 'แชท', array('ai_thread' => base_url().'resources/js/ai_thread.js'));
	}

	function product_suggest()
	{
		$id = (int)$this->input->get('thread_id');
		$q = trim((string)$this->input->get('q'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			$this->json_out(array('ok' => false, 'items' => array()));
			return;
		}
		$items = $this->chat_platform_bl->search_products($thread['platform'], $q);
		$this->json_out(array('ok' => true, 'items' => $items));
	}

	function order_suggest()
	{
		$id = (int)$this->input->get('thread_id');
		$q = trim((string)$this->input->get('q'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			$this->json_out(array('ok' => false, 'items' => array()));
			return;
		}
		$items = $this->chat_platform_bl->search_orders_for_thread($thread, $q, 40);
		$this->json_out(array('ok' => true, 'items' => $items));
	}

	function new_thread()
	{
		$platform = strtolower(trim((string)$this->input->post('platform')));
		if (!in_array($platform, array('lazada', 'shopee', 'tiktok'), true)) {
			$platform = 'shopee';
		}
		$buyer = trim((string)$this->input->post('buyer_name'));
		$inbound = trim((string)$this->input->post('inbound'));
		$tid = $this->chat_learn_model->insert_thread(array(
			'platform' => $platform,
			'buyer_name' => $buyer !== '' ? $buyer : null,
			'status' => 'open',
			'last_message_at' => date('Y-m-d H:i:s')
		));
		if ($inbound !== '') {
			$this->chat_learn_model->insert_message(array(
				'thread_id' => $tid,
				'direction' => 'in',
				'body' => $inbound,
				'sender' => 'buyer'
			));
		}
		redirect(base_url().'ai/thread/'.$tid);
	}

	function add_inbound()
	{
		$id = (int)$this->input->post('thread_id');
		$body = trim((string)$this->input->post('body'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread) || $body === '') {
			redirect(base_url().'ai/inbox');
			return;
		}
		$this->chat_learn_model->insert_message(array(
			'thread_id' => $id,
			'direction' => 'in',
			'body' => $body,
			'sender' => 'buyer'
		));
		$this->chat_learn_model->update_thread($id, array('last_message_at' => date('Y-m-d H:i:s')));
		redirect(base_url().'ai/thread/'.$id);
	}

	function send()
	{
		$id = (int)$this->input->post('thread_id');
		$body = trim((string)$this->input->post('body'));
		$ai_draft = $this->input->post('ai_draft');
		$original_draft = $this->input->post('ai_original_draft');
		if ($original_draft !== false && $original_draft !== null && trim((string)$original_draft) !== '') {
			$ai_draft = trim((string)$original_draft);
		}
		$attach = $this->_parse_attach($this->input->post('attach_json'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			redirect(base_url().'ai/inbox');
			return;
		}
		if ($body === '' && empty($attach)) {
			$this->session->set_flashdata('ai_draft_error', 'empty_reply');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$last_preview = '';
		if ($body !== '') {
			$sent = $this->chat_platform_bl->send_text($thread, $body);
			if (empty($sent['ok'])) {
				$this->session->set_flashdata('ai_draft_error', isset($sent['error']) ? $sent['error'] : 'send_fail');
				$this->session->set_flashdata('ai_draft', $body);
				$this->session->set_flashdata('ai_attach', $attach);
				redirect(base_url().'ai/thread/'.$id);
				return;
			}
			$this->chat_learn_model->insert_message(array(
				'thread_id' => $id,
				'direction' => 'out',
				'body' => $body,
				'sender' => 'human',
				'ai_draft' => ($ai_draft !== false && $ai_draft !== null && $ai_draft !== '') ? $ai_draft : null,
				'msg_type' => 'text',
				'platform_msg_id' => $this->chat_platform_bl->send_result_msg_id($sent)
			));
			$last_preview = $this->chat_platform_bl->preview($body);
			$settings = $this->ai_settings_model->get();
			$last_in = $this->chat_learn_model->last_inbound($id);
			$inbound = $last_in ? $last_in['body'] : '';
			$draft_for_learn = ($ai_draft !== false && $ai_draft !== null && $ai_draft !== '')
				? $ai_draft
				: $this->chat_learn_model->last_coach_suggest($id);
			if (!empty($settings['observe_chat'])) {
				$this->chat_learn_bl->record_reply($thread['platform'], $id, $inbound, $body, $draft_for_learn);
				if (!empty($settings['auto_distill'])) {
					$this->chat_learn_bl->distill($thread['platform']);
					$this->chat_learn_bl->distill('all');
				}
			}
		}
		$remain = $attach;
		foreach ($attach as $i => $card) {
			$item_id = isset($card['id']) ? trim((string)$card['id']) : '';
			$kind = (isset($card['kind']) && $card['kind'] === 'order') ? 'order' : 'product';
			if ($kind === 'order') {
				if (!$this->chat_platform_bl->order_allowed($thread, $item_id)) {
					$this->session->set_flashdata('ai_draft_error', 'order_not_this_buyer');
					$this->session->set_flashdata('ai_draft', $body);
					$this->session->set_flashdata('ai_attach', array_values($remain));
					redirect(base_url().'ai/thread/'.$id);
					return;
				}
				$sent = $this->chat_platform_bl->send_order($thread, $item_id);
				if (empty($sent['ok'])) {
					$this->session->set_flashdata('ai_draft_error', isset($sent['error']) ? $sent['error'] : 'send_fail');
					$this->session->set_flashdata('ai_draft', $body);
					$this->session->set_flashdata('ai_attach', array_values($remain));
					redirect(base_url().'ai/thread/'.$id);
					return;
				}
				$label = '[ออเดอร์ '.$item_id.']';
				$this->chat_learn_model->insert_message(array(
					'thread_id' => $id,
					'direction' => 'out',
					'body' => $label,
					'sender' => 'human',
					'msg_type' => 'order',
					'platform_msg_id' => $this->chat_platform_bl->send_result_msg_id($sent),
					'extra_json' => json_encode(array('order_id' => $item_id), JSON_UNESCAPED_UNICODE)
				));
				$last_preview = $label;
				unset($remain[$i]);
				continue;
			}
			$sent = $this->chat_platform_bl->send_product($thread, $item_id);
			if (empty($sent['ok'])) {
				$this->session->set_flashdata('ai_draft_error', isset($sent['error']) ? $sent['error'] : 'send_fail');
				$this->session->set_flashdata('ai_draft', $body);
				$this->session->set_flashdata('ai_attach', array_values($remain));
				redirect(base_url().'ai/thread/'.$id);
				return;
			}
			$extra = $this->chat_platform_bl->product_card_extra($thread['platform'], $item_id);
			if (!empty($card['name'])) {
				$extra['name'] = $card['name'];
			}
			if (!empty($card['sku'])) {
				$extra['sku'] = $card['sku'];
			}
			if (!empty($card['image'])) {
				$extra['image'] = $card['image'];
			}
			$label = !empty($extra['sku']) ? $extra['sku'] : '[สินค้า '.$item_id.']';
			if (!empty($extra['name'])) {
				$label = trim($extra['sku'].' '.$extra['name']);
			}
			$this->chat_learn_model->insert_message(array(
				'thread_id' => $id,
				'direction' => 'out',
				'body' => $label,
				'sender' => 'human',
				'msg_type' => 'product',
				'platform_msg_id' => $this->chat_platform_bl->send_result_msg_id($sent),
				'extra_json' => json_encode($extra, JSON_UNESCAPED_UNICODE)
			));
			$last_preview = $label;
			unset($remain[$i]);
		}
		$this->chat_learn_model->update_thread($id, array(
			'last_message_at' => date('Y-m-d H:i:s'),
			'last_preview' => $last_preview !== '' ? $last_preview : $this->chat_platform_bl->preview($body),
			'last_from' => 'shop',
			'unread' => 0
		));
		$this->session->set_flashdata('ai_msg', 'sent_live');
		redirect(base_url().'ai/thread/'.$id);
	}

	private function _parse_attach($raw)
	{
		if (is_array($raw)) {
			$list = $raw;
		} else {
			$list = json_decode((string)$raw, true);
		}
		if (!is_array($list)) {
			return array();
		}
		$out = array();
		$seen = array();
		foreach ($list as $row) {
			if (!is_array($row)) {
				continue;
			}
			$id = isset($row['id']) ? trim((string)$row['id']) : '';
			$kind = (isset($row['kind']) && $row['kind'] === 'order') ? 'order' : 'product';
			$key = $kind.':'.$id;
			if ($id === '' || isset($seen[$key])) {
				continue;
			}
			$seen[$key] = 1;
		$out[] = array(
				'kind' => (isset($row['kind']) && $row['kind'] === 'order') ? 'order' : 'product',
				'id' => $id,
				'name' => isset($row['name']) ? (string)$row['name'] : '',
				'sku' => isset($row['sku']) ? (string)$row['sku'] : '',
				'image' => isset($row['image']) ? (string)$row['image'] : '',
				'status' => isset($row['status']) ? (string)$row['status'] : '',
				'items' => isset($row['items']) ? (string)$row['items'] : ''
			);
		}
		return $out;
	}

	function send_product()
	{
		$id = (int)$this->input->post('thread_id');
		$item_id = trim((string)$this->input->post('item_id'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			redirect(base_url().'ai/inbox');
			return;
		}
		$sent = $this->chat_platform_bl->send_product($thread, $item_id);
		if (empty($sent['ok'])) {
			$this->session->set_flashdata('ai_draft_error', isset($sent['error']) ? $sent['error'] : 'send_fail');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$label = '[สินค้า '.$item_id.']';
		$this->chat_learn_model->insert_message(array(
			'thread_id' => $id,
			'direction' => 'out',
			'body' => $label,
			'sender' => 'human',
			'msg_type' => 'product',
			'extra_json' => json_encode(array('item_id' => $item_id))
		));
		$this->chat_learn_model->update_thread($id, array(
			'last_message_at' => date('Y-m-d H:i:s'),
			'last_preview' => $label,
			'last_from' => 'shop',
			'unread' => 0
		));
		$this->session->set_flashdata('ai_msg', 'sent_live');
		redirect(base_url().'ai/thread/'.$id);
	}

	function send_order()
	{
		$id = (int)$this->input->post('thread_id');
		$order_id = trim((string)$this->input->post('order_id'));
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			redirect(base_url().'ai/inbox');
			return;
		}
		if ($order_id === '') {
			$this->session->set_flashdata('ai_draft_error', 'empty_order');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		if (!$this->chat_platform_bl->order_allowed($thread, $order_id)) {
			$this->session->set_flashdata('ai_draft_error', 'order_not_this_buyer');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$sent = $this->chat_platform_bl->send_order($thread, $order_id);
		if (empty($sent['ok'])) {
			$this->session->set_flashdata('ai_draft_error', isset($sent['error']) ? $sent['error'] : 'send_fail');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$label = '[ออเดอร์ '.$order_id.']';
		$this->chat_learn_model->insert_message(array(
			'thread_id' => $id,
			'direction' => 'out',
			'body' => $label,
			'sender' => 'human',
			'msg_type' => 'order',
			'extra_json' => json_encode(array('order_id' => $order_id))
		));
		$this->chat_learn_model->update_thread($id, array(
			'last_message_at' => date('Y-m-d H:i:s'),
			'last_preview' => $label,
			'last_from' => 'shop',
			'unread' => 0
		));
		$this->session->set_flashdata('ai_msg', 'sent_live');
		redirect(base_url().'ai/thread/'.$id);
	}

	function draft()
	{
		$id = (int)$this->input->post('thread_id');
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			redirect(base_url().'ai/inbox');
			return;
		}
		set_time_limit(120);
		if (!$this->ai_settings_model->has_api_key()) {
			$this->session->set_flashdata('ai_draft_error', 'missing_api_key');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$res = $this->chat_learn_bl->coach_turn($thread, '');
		if (empty($res['ok'])) {
			$this->session->set_flashdata('ai_draft_error', $res['error']);
		}
		redirect(base_url().'ai/thread/'.$id);
	}

	function coach()
	{
		$id = (int)$this->input->post('thread_id');
		$body = trim((string)$this->input->post('body'));
		$ajax = $this->input->is_ajax_request();
		$thread = $this->chat_learn_model->get_thread($id);
		if (empty($thread)) {
			if ($ajax) {
				$this->json_out(array('ok' => false, 'error' => 'missing_thread'));
				return;
			}
			redirect(base_url().'ai/inbox');
			return;
		}
		$attach = $this->_parse_attach($this->input->post('attach_json'));
		if ($body === '' && empty($attach)) {
			if ($ajax) {
				$this->json_out(array('ok' => false, 'error' => 'empty_coach'));
				return;
			}
			$this->session->set_flashdata('ai_draft_error', 'empty_coach');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		if (!$this->ai_settings_model->has_api_key()) {
			if ($ajax) {
				$this->json_out(array('ok' => false, 'error' => 'missing_api_key'));
				return;
			}
			$this->session->set_flashdata('ai_draft_error', 'missing_api_key');
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		set_time_limit(120);
		$res = $this->chat_learn_bl->coach_turn($thread, $body, $attach);
		$err = empty($res['ok']) ? (isset($res['error']) ? $res['error'] : 'fail') : '';
		if (!$ajax) {
			if ($err !== '') {
				$this->session->set_flashdata('ai_draft_error', $err);
			}
			redirect(base_url().'ai/thread/'.$id);
			return;
		}
		$this->json_out(array(
			'ok' => !empty($res['ok']),
			'error' => $err,
			'admin' => $body,
			'discuss' => isset($res['discuss']) ? $res['discuss'] : '',
			'reply' => isset($res['reply']) ? $res['reply'] : '',
			'objects' => isset($res['objects']) ? $res['objects'] : $attach,
			'meta' => isset($res['meta']) ? $res['meta'] : array()
		));
	}

	function playbook()
	{
		$data = array(
			'books' => $this->chat_learn_model->all_playbooks(),
			'flash' => $this->session->flashdata('ai_msg'),
			'distill_error' => $this->session->flashdata('ai_draft_error')
		);
		$this->render('ai/playbook', $data, 'คู่มือการตอบ');
	}

	function distill()
	{
		$out = $this->chat_learn_bl->distill_all();
		$err = '';
		foreach ($out as $row) {
			if (empty($row['ok']) && isset($row['error'])) {
				$err = $row['error'];
				break;
			}
		}
		if ($err !== '') {
			$this->session->set_flashdata('ai_draft_error', $err);
		} else {
			$this->session->set_flashdata('ai_msg', 'distilled');
		}
		redirect(base_url().'ai/playbook');
	}

	private function key_hint($key)
	{
		$key = (string)$key;
		if ($key === '') {
			return '';
		}
		$len = strlen($key);
		if ($len <= 4) {
			return '****';
		}
		return '********'.substr($key, -4);
	}

	private function inbox_payload($sync)
	{
		$grouped = array('shopee' => array(), 'lazada' => array(), 'tiktok' => array());
		$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
		$threads = $this->chat_learn_model->list_threads(240);
		$ids = array();
		foreach ($threads as $th) {
			$ids[] = (int)$th['thread_id'];
		}
		$dirs = $this->chat_learn_model->last_direction_map($ids);
		foreach ($threads as $th) {
			$p = strtolower(trim((string)$th['platform']));
			if (!isset($grouped[$p])) {
				continue;
			}
			$tid = (int)$th['thread_id'];
			$dir = isset($dirs[$tid]) ? $dirs[$tid] : '';
			$st = $this->chat_platform_bl->thread_status($th, $dir);
			$at_raw = isset($th['last_message_at']) ? (string)$th['last_message_at'] : '';
			$grouped[$p][] = array(
				'thread_id' => $tid,
				'buyer_name' => isset($th['buyer_name']) ? (string)$th['buyer_name'] : '',
				'avatar' => $this->chat_platform_bl->thread_avatar($th),
				'last_preview' => $this->chat_platform_bl->preview(isset($th['last_preview']) ? $th['last_preview'] : ''),
				'last_message_at' => $at_raw,
				'time_label' => $this->inbox_time_label($at_raw, $is_en),
				'live' => !empty($th['platform_conv_id']) ? 1 : 0,
				'last_dir' => $st['last_from'] === 'shop' ? 'out' : ($st['last_from'] === 'buyer' ? 'in' : $dir),
				'needs_reply' => $st['needs_reply'],
				'replied' => $st['replied'],
				'overdue' => $st['overdue'],
				'pinned' => $st['pinned'],
				'unread' => $st['unread']
			);
		}
		foreach ($grouped as $p => $list) {
			$grouped[$p] = array_slice($list, 0, 80);
		}
		return array(
			'ok' => true,
			'checked_at' => date('Y-m-d H:i:s'),
			'counts' => $this->chat_learn_model->unreplied_counts(),
			'sync' => is_array($sync) ? $sync : array(),
			'threads' => $grouped
		);
	}

	private function inbox_time_label($s, $is_en)
	{
		$s = preg_replace('/\.\d+$/', '', trim((string)$s));
		if ($s === '') {
			return '';
		}
		$t = strtotime($s);
		if ($t === false) {
			return $s;
		}
		if ($t >= strtotime('today')) {
			return date('H:i', $t);
		}
		if ($t >= strtotime('yesterday')) {
			return $is_en ? 'Yesterday' : 'เมื่อวาน';
		}
		if (date('Y', $t) === date('Y')) {
			return date('d/m', $t);
		}
		return date('d/m/Y', $t);
	}

	private function list_sync_due()
	{
		$path = APPPATH.'cache/chat_list_sync.json';
		$now = time();
		$last = 0;
		if (is_file($path)) {
			$j = json_decode(@file_get_contents($path), true);
			$last = (is_array($j) && isset($j['at'])) ? (int)$j['at'] : 0;
		}
		if ($last > 0 && ($now - $last) < 20) {
			return false;
		}
		@file_put_contents($path, json_encode(array('at' => $now)));
		return true;
	}

	private function json_out($data)
	{
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
		$this->output->_display();
		exit;
	}

	private function render($view, $data, $title, $arr_js = array())
	{
		$arr_input = array('title' => $title);
		$arr_css = array('ai' => base_url().'resources/css/ai_chat.css');
		$menu_id = MENU_SALES_CHAT;
		if ($view === 'ai/settings') {
			$menu_id = MENU_CONFIG_CHAT;
		} elseif ($view === 'ai/playbook') {
			$menu_id = MENU_SALES_PLAYBOOK;
		}
		$this->view_util->load_view_main($view, $data, $arr_css, $arr_js, $arr_input, $menu_id);
	}
}
