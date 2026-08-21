<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat_learn_bl
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('chat_learn_model');
		$this->CI->load->model('ai_settings_model');
		$this->CI->load->library('businesslogic/llm_bl');
		$this->CI->load->library('businesslogic/chat_platform_bl');
	}

	function record_reply($platform, $thread_id, $inbound, $outbound, $ai_draft)
	{
		$inbound = trim((string)$inbound);
		$outbound = trim((string)$outbound);
		if ($inbound === '' || $outbound === '') {
			return 0;
		}
		$edited = 0;
		$weight = 2;
		if ($ai_draft !== null && trim((string)$ai_draft) !== '' && trim((string)$ai_draft) !== $outbound) {
			$edited = 1;
			$weight = 3;
		}
		return $this->CI->chat_learn_model->insert_example(array(
			'platform' => $platform,
			'thread_id' => $thread_id ? (int)$thread_id : null,
			'inbound_text' => $inbound,
			'outbound_text' => $outbound,
			'ai_draft' => $ai_draft,
			'human_edited' => $edited,
			'weight' => $weight
		));
	}

	function similar_examples($platform, $inbound, $limit = 5)
	{
		$rows = $this->CI->chat_learn_model->examples_for_platform($platform, 80);
		if (count($rows) < 8) {
			$rows = $this->CI->chat_learn_model->examples_for_platform('all', 80);
		}
		$tokens = $this->tokens($inbound);
		$scored = array();
		foreach ($rows as $row) {
			$score = (int)$row['weight'] * 2;
			$hay = $this->tokens($row['inbound_text']);
			foreach ($tokens as $t) {
				if (isset($hay[$t])) {
					$score += 3;
				}
			}
			if ($row['human_edited']) {
				$score += 4;
			}
			$scored[] = array('score' => $score, 'row' => $row);
		}
		usort($scored, array($this, 'cmp_score'));
		$out = array();
		$n = 0;
		foreach ($scored as $item) {
			$out[] = $item['row'];
			$n++;
			if ($n >= $limit) {
				break;
			}
		}
		return $out;
	}

	function cmp_score($a, $b)
	{
		if ($a['score'] == $b['score']) {
			return 0;
		}
		return ($a['score'] > $b['score']) ? -1 : 1;
	}

	function tokens($text)
	{
		$text = mb_strtolower(trim((string)$text), 'UTF-8');
		$parts = preg_split('/[^\p{L}\p{N}]+/u', $text);
		$map = array();
		if (!is_array($parts)) {
			return $map;
		}
		foreach ($parts as $p) {
			if ($p === '' || mb_strlen($p, 'UTF-8') < 2) {
				continue;
			}
			$map[$p] = 1;
		}
		return $map;
	}

	function clip($text, $max)
	{
		$text = trim((string)$text);
		if (mb_strlen($text, 'UTF-8') <= $max) {
			return $text;
		}
		return mb_substr($text, 0, $max, 'UTF-8').'…';
	}

	function shop_facts($thread, $inbound)
	{
		$platform = isset($thread['platform']) ? $thread['platform'] : '';
		$play_all = $this->CI->chat_learn_model->get_playbook('all');
		$play_p = $this->CI->chat_learn_model->get_playbook($platform);
		$examples = $this->similar_examples($platform, $inbound, 5);
		$orders_txt = $this->CI->chat_platform_bl->orders_prompt($thread);
		$orders = $this->CI->chat_platform_bl->orders_for_thread($thread, 6);
		$q = trim((string)$inbound);
		if (mb_strlen($q, 'UTF-8') > 80) {
			$q = mb_substr($q, 0, 80, 'UTF-8');
		}
		$products = array();
		if ($q !== '') {
			$p = strtolower((string)$platform);
			if ($p === 'shopee') {
				$products = $this->CI->chat_platform_bl->shopee_products_from_shop_db($q);
			} else {
				$products = $this->CI->chat_platform_bl->search_products($platform, $q);
			}
			if (count($products) > 8) {
				$products = array_slice($products, 0, 8);
			}
		}
		$ex_txt = '';
		$i = 1;
		foreach ($examples as $ex) {
			$ex_txt .= "สไตล์ตัวอย่าง ".$i." (example_id ".(isset($ex['example_id']) ? $ex['example_id'] : '').")\nลูกค้า: ".$this->clip($ex['inbound_text'], 400)."\nร้าน: ".$this->clip($ex['outbound_text'], 400)."\n\n";
			$i++;
		}
		$prod_txt = '';
		foreach ($products as $p) {
			$prod_txt .= '- id '.(isset($p['id']) ? $p['id'] : '').' sku '.(isset($p['sku']) ? $p['sku'] : '').' '.(isset($p['name']) ? $p['name'] : '')."\n";
		}
		$engine = $this->CI->chat_learn_model->db_label();
		$text = "แหล่งข้อมูล: ฐานข้อมูลร้าน BNY (".$engine.") ไม่ใช่ความรู้จาก Cursor หรือโมเดลอย่างเดียว\n".
			"ใช้ได้เฉพาะข้อความด้านล่าง ถ้าไม่มีในนี้ให้บอกแอดมินว่ายังไม่มีในฐาน ห้ามเดา\n\n".
			"คู่มือรวม (chat_playbook):\n".$this->clip(isset($play_all['rules_text']) ? $play_all['rules_text'] : '', 2500)."\n\n".
			"คู่มือช่อง ".$platform.":\n".$this->clip(isset($play_p['rules_text']) ? $play_p['rules_text'] : '', 2500)."\n\n".
			($ex_txt !== '' ? "ตัวอย่างคำตอบที่คนร้านเคยส่ง (chat_reply_example):\n".$ex_txt : "ยังไม่มีตัวอย่างคำตอบในฐาน\n\n").
			($orders_txt !== '' ? $orders_txt."\n" : "ยังจับออเดอร์ของลูกค้าคนนี้ในฐานไม่ได้\n\n").
			($prod_txt !== '' ? "สินค้าในร้านที่ใกล้กับข้อความนี้:\n".$prod_txt."\n" : "ยังไม่พบสินค้าในฐานที่ตรงกับข้อความนี้\n\n");
		$meta = array(
			'engine' => $engine,
			'playbook' => (trim((string)(isset($play_p['rules_text']) ? $play_p['rules_text'] : '')) !== '' || trim((string)(isset($play_all['rules_text']) ? $play_all['rules_text'] : '')) !== '') ? 1 : 0,
			'examples' => count($examples),
			'orders' => count($orders),
			'products' => count($products)
		);
		return array('text' => $text, 'meta' => $meta);
	}

	function draft($platform, $inbound, $history_text, $tools_text = '')
	{
		$thread = array('platform' => $platform, 'thread_id' => 0, 'buyer_name' => '');
		$facts = $this->shop_facts($thread, $inbound);
		if ($tools_text !== '') {
			$facts['text'] .= $tools_text."\n";
		}
		return $this->coach_prompt($platform, $inbound, $history_text, $facts, array(), 'ช่วยวิเคราะห์และร่างคำตอบให้ลูกค้าคนนี้');
	}

	function parse_coach_blocks($text)
	{
		$text = trim((string)$text);
		$objects_raw = '';
		if (preg_match('/##\s*OBJECTS\s*\r?\n(.*)$/is', $text, $om)) {
			$objects_raw = trim($om[1]);
			$text = trim(preg_replace('/##\s*OBJECTS\s*\r?\n.*$/is', '', $text));
		}
		$reply = '';
		$discuss = $text;
		if (preg_match('/##\s*REPLY\s*\r?\n(.*)$/is', $text, $m)) {
			$reply = trim($m[1]);
			$discuss = trim(preg_replace('/##\s*REPLY\s*\r?\n.*$/is', '', $text));
		}
		$discuss = trim(preg_replace('/^##\s*DISCUSS\s*\r?\n?/i', '', $discuss));
		if ($reply === '' && $discuss !== '') {
			$reply = $discuss;
		}
		return array(
			'discuss' => $discuss,
			'reply' => $reply,
			'objects' => $this->parse_object_lines($objects_raw)
		);
	}

	function parse_object_lines($raw)
	{
		$out = array();
		$lines = preg_split('/\r?\n/', trim((string)$raw));
		if (!is_array($lines)) {
			return $out;
		}
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '') {
				continue;
			}
			$parts = explode('|', $line);
			$kind = strtolower(trim($parts[0]));
			if ($kind === 'product' && count($parts) >= 2) {
				$out[] = array(
					'kind' => 'product',
					'id' => trim($parts[1]),
					'sku' => isset($parts[2]) ? trim($parts[2]) : '',
					'name' => isset($parts[3]) ? trim($parts[3]) : '',
					'image' => ''
				);
			} elseif ($kind === 'order' && count($parts) >= 2) {
				$out[] = array(
					'kind' => 'order',
					'id' => trim($parts[1]),
					'name' => isset($parts[2]) ? trim($parts[2]) : '',
					'sku' => '',
					'image' => ''
				);
			}
		}
		return $out;
	}

	function objects_prompt($attach)
	{
		if (empty($attach)) {
			return '';
		}
		$txt = "แอดมินแนบวัตถุเหล่านี้มาในบทปรึกษา (ใช้รหัสนี้เท่านั้นตอนแนะนำ):\n";
		foreach ((array)$attach as $p) {
			$kind = isset($p['kind']) ? $p['kind'] : 'product';
			$txt .= '- '.$kind.' id '.(isset($p['id']) ? $p['id'] : '');
			if (!empty($p['sku'])) {
				$txt .= ' sku '.$p['sku'];
			}
			if (!empty($p['name'])) {
				$txt .= ' '.$p['name'];
			}
			$txt .= "\n";
		}
		return $txt;
	}

	function merge_objects($preferred, $extra)
	{
		$out = array();
		$seen = array();
		foreach (array($preferred, $extra) as $list) {
			foreach ((array)$list as $row) {
				if (!is_array($row) || empty($row['id'])) {
					continue;
				}
				$kind = isset($row['kind']) ? $row['kind'] : 'product';
				$key = $kind.':'.$row['id'];
				if (isset($seen[$key])) {
					continue;
				}
				$seen[$key] = 1;
				$out[] = array(
					'kind' => $kind,
					'id' => (string)$row['id'],
					'name' => isset($row['name']) ? (string)$row['name'] : '',
					'sku' => isset($row['sku']) ? (string)$row['sku'] : '',
					'image' => isset($row['image']) ? (string)$row['image'] : '',
					'status' => isset($row['status']) ? (string)$row['status'] : '',
					'items' => isset($row['items']) ? (string)$row['items'] : ''
				);
			}
		}
		return $out;
	}

	function coach_prompt($platform, $inbound, $history_text, $facts, $coach_rows, $admin_text, $attach = array())
	{
		$settings = $this->CI->ai_settings_model->get();
		$facts_text = is_array($facts) && isset($facts['text']) ? $facts['text'] : (string)$facts;
		$coach_txt = '';
		foreach ((array)$coach_rows as $row) {
			$who = (isset($row['role']) && $row['role'] === 'admin') ? 'แอดมิน' : 'AI';
			$coach_txt .= $who.': '.(isset($row['body']) ? $row['body'] : '')."\n";
			$objs = $this->CI->chat_learn_model->decode_attach(isset($row['attach_json']) ? $row['attach_json'] : '');
			if (!empty($objs)) {
				$coach_txt .= $this->objects_prompt($objs);
			}
		}
		$system = "คุณเป็นที่ปรึกษาแชทส่วนตัวของแอดมินร้าน BNY Food Products ลูกค้าจะไม่เห็นข้อความในบล็อก DISCUSS\n".
			"วิเคราะห์จากข้อมูลที่ระบบดึงจากฐานข้อมูลร้านเท่านั้น ห้ามใช้ความรู้ทั่วไปหรือบทสนทนา Cursor ถ้าไม่มีในข้อมูลที่ให้\n".
			"ถ้าแอดมินสอนว่าควรตอบอย่างไร ให้เขียน ## REPLY ใหม่ให้ล้อตามคำสอนล่าสุด ห้ามยึดคำตอบรอบก่อน\n".
			"ถ้าแอดมินแนบสินค้าหรือออเดอร์มา ให้ใช้รหัสนั้นใน ## OBJECTS\n".
			"ห้ามสัญญาของที่ไม่มี เสนอสินค้า/ออเดอร์ได้เฉพาะรหัสที่มีในข้อมูลหรือที่แอดมินแนบมา\n".
			"ตอบสามบล็อกนี้เท่านั้น:\n".
			"## DISCUSS\n".
			"คุยกับแอดมิน: รับทราบคำสอน ทำไมตอบแบบนี้ สินค้า/ออเดอร์ที่ควรหรือไม่ควรส่ง\n".
			"## REPLY\n".
			"ข้อความเดียวที่จะส่งถึงลูกค้า ตามคำสอนล่าสุด ไม่มีหัวข้อ\n".
			"## OBJECTS\n".
			"บรรทัดละชิ้น PRODUCT|id|sku|ชื่อ หรือ ORDER|เลขออเดอร์ ถ้าไม่มีให้เว้นว่าง";
		$user = "แพลตฟอร์ม: ".$platform."\n\n".
			$facts_text.
			$this->objects_prompt($attach).
			"บทสนทนากับลูกค้า (chat_message):\n".$history_text."\n\n".
			"ข้อความลูกค้าล่าสุด:\n".$inbound."\n\n".
			($coach_txt !== '' ? "บทสนทนาส่วนตัวกับแอดมินที่เก็บในฐาน (chat_coach):\n".$coach_txt."\n\n" : '').
			"แอดมินเพิ่งพิมพ์:\n".($admin_text !== '' ? $admin_text : 'ช่วยวิเคราะห์และร่างคำตอบให้ลูกค้าคนนี้');
		$res = $this->CI->llm_bl->chat($settings, $system, $user);
		if (empty($res['ok'])) {
			return $res;
		}
		$blocks = $this->parse_coach_blocks($res['text']);
		$res['discuss'] = $blocks['discuss'];
		$res['reply'] = $blocks['reply'];
		$res['objects'] = $blocks['objects'];
		$res['text'] = $blocks['reply'] !== '' ? $blocks['reply'] : $res['text'];
		$res['meta'] = is_array($facts) && isset($facts['meta']) ? $facts['meta'] : array();
		return $res;
	}

	function thread_history_text($thread_id)
	{
		$hist = '';
		$rows = $this->CI->chat_learn_model->messages($thread_id);
		$start = 0;
		if (count($rows) > 30) {
			$start = count($rows) - 30;
		}
		for ($i = $start; $i < count($rows); $i++) {
			$m = $rows[$i];
			$who = ($m['direction'] === 'in') ? 'ลูกค้า' : 'ร้าน';
			$hist .= $who.': '.$this->clip($m['body'], 500)."\n";
		}
		return $hist;
	}

	function coach_turn($thread, $admin_text, $attach = array())
	{
		$tid = (int)$thread['thread_id'];
		$admin_text = trim((string)$admin_text);
		$attach = $this->merge_objects($attach, array());
		$last_in = $this->CI->chat_learn_model->last_inbound($tid);
		$inbound = $last_in ? $last_in['body'] : '';
		if ($inbound === '' && $admin_text === '' && empty($attach)) {
			return array('ok' => false, 'error' => 'no_inbound', 'text' => '', 'discuss' => '', 'reply' => '', 'meta' => array(), 'objects' => array());
		}
		if ($admin_text === '' && !empty($attach)) {
			$admin_text = 'แอดมินแนบสินค้า/ออเดอร์นี้เพื่อใช้ตอบลูกค้า';
		}
		$hist = $this->thread_history_text($tid);
		$query = trim($inbound.' '.$admin_text);
		$facts = $this->shop_facts($thread, $query);
		$prev = $this->CI->chat_learn_model->coach_messages($tid, 40);
		if ($admin_text !== '') {
			$admin_row = array(
				'thread_id' => $tid,
				'role' => 'admin',
				'body' => $admin_text
			);
			if (!empty($attach)) {
				$admin_row['attach_json'] = json_encode($attach, JSON_UNESCAPED_UNICODE);
			}
			$this->CI->chat_learn_model->insert_coach($admin_row);
			$prev[] = $admin_row;
		}
		$res = $this->coach_prompt(
			$thread['platform'],
			$inbound,
			$hist,
			$facts,
			$prev,
			$admin_text !== '' ? $admin_text : 'ช่วยวิเคราะห์และร่างคำตอบให้ลูกค้าคนนี้ จากข้อมูลในฐานร้านเท่านั้น',
			$attach
		);
		if (empty($res['ok'])) {
			$res['meta'] = $facts['meta'];
			$res['objects'] = $attach;
			return $res;
		}
		$discuss = isset($res['discuss']) ? $res['discuss'] : $res['text'];
		$reply = isset($res['reply']) ? $res['reply'] : '';
		$objects = $this->merge_objects($attach, isset($res['objects']) ? $res['objects'] : array());
		$ai_row = array(
			'thread_id' => $tid,
			'role' => 'ai',
			'body' => $discuss,
			'suggest_reply' => $reply !== '' ? $reply : null,
			'source_json' => json_encode($facts['meta'], JSON_UNESCAPED_UNICODE)
		);
		if (!empty($objects)) {
			$ai_row['attach_json'] = json_encode($objects, JSON_UNESCAPED_UNICODE);
		}
		$this->CI->chat_learn_model->insert_coach($ai_row);
		$res['discuss'] = $discuss;
		$res['reply'] = $reply;
		$res['objects'] = $objects;
		$res['meta'] = $facts['meta'];
		return $res;
	}

	function distill($platform)
	{
		$settings = $this->CI->ai_settings_model->get();
		$examples = $this->CI->chat_learn_model->examples_for_platform($platform, 40);
		if (empty($examples)) {
			return array('ok' => false, 'error' => 'no_examples');
		}
		$play = $this->CI->chat_learn_model->get_playbook($platform);
		$block = '';
		$n = 0;
		foreach ($examples as $ex) {
			$n++;
			$note = $ex['human_edited'] ? ' (คนแก้จากร่าง AI)' : '';
			$block .= "#".$n.$note."\nลูกค้า: ".$ex['inbound_text']."\nร้าน: ".$ex['outbound_text']."\n\n";
			if ($n >= 25) {
				break;
			}
		}
		$engine = $this->CI->chat_learn_model->db_label();
		$system = "คุณสรุปสไตล์การตอบแชทร้านอาหารออนไลน์เป็นคู่มือสั้น เป็นข้อๆ ภาษาไทย ".
			"ใช้เฉพาะตัวอย่างจากฐานข้อมูลร้าน (".$engine." ตาราง chat_reply_example) ".
			"เน้นน้ำเสียง ความยาว คำทักทาย การเสนอสินค้า สิ่งที่ห้ามพูด ".
			"อย่าแต่งนโยบายใหม่ที่ไม่มีในตัวอย่าง และห้ามก็อปข้อความตัวอย่างทั้งก้อน";
		$user = "คู่มือเดิมในฐาน (chat_playbook):\n".(isset($play['rules_text']) ? $play['rules_text'] : '')."\n\nตัวอย่างการตอบจริงจากฐาน:\n".$block.
			"\nเขียนคู่มือใหม่ทั้งฉบับ กระชับ ใช้ได้ตอนร่างข้อความครั้งหน้า แล้วระบบจะบันทึกกลับลงฐานร้าน";
		$res = $this->CI->llm_bl->chat($settings, $system, $user);
		if (empty($res['ok'])) {
			return $res;
		}
		$cnt = $this->CI->chat_learn_model->count_examples($platform);
		$this->CI->chat_learn_model->save_playbook($platform, $res['text'], $cnt);
		return array('ok' => true, 'error' => '', 'text' => $res['text']);
	}

	function distill_all()
	{
		$out = array();
		foreach (array('all', 'lazada', 'shopee', 'tiktok') as $p) {
			$rows = $this->CI->chat_learn_model->examples_for_platform($p, 5);
			if (empty($rows)) {
				continue;
			}
			$out[$p] = $this->distill($p);
		}
		return $out;
	}
}
