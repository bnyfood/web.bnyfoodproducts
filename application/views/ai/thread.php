<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$base = base_url();
$draft_val = isset($draft) ? (string)$draft : '';
$original_draft_val = isset($original_draft) ? (string)$original_draft : $draft_val;
$live = !empty($live);
$orders = isset($orders) && is_array($orders) ? $orders : array();
$tid = (int)$thread['thread_id'];
$attach = isset($attach) && is_array($attach) ? $attach : array();
$attach_json = json_encode($attach, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
$coach = isset($coach) && is_array($coach) ? $coach : array();
$db_label = isset($db_label) ? (string)$db_label : 'Microsoft SQL Server';
$suggest_attach = isset($suggest_attach) && is_array($suggest_attach) ? $suggest_attach : array();
$suggest_attach_json = json_encode($suggest_attach, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
$obj_attr = function ($list) {
	return htmlspecialchars(json_encode($list, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8');
};
$render_objs = function ($list) use ($t) {
	if (empty($list) || !is_array($list)) {
		return;
	}
	foreach ($list as $ob) {
		if (!is_array($ob) || empty($ob['id'])) {
			continue;
		}
		$kind = (isset($ob['kind']) && $ob['kind'] === 'order') ? 'order' : 'product';
		$label = $kind === 'order'
			? (!empty($ob['id']) ? $ob['id'] : $t('ออเดอร์', 'Order'))
			: (!empty($ob['sku']) ? $ob['sku'] : $ob['id']);
		$name = $kind === 'order'
			? (isset($ob['status']) ? $ob['status'] : $t('ออเดอร์', 'Order'))
			: (isset($ob['name']) ? $ob['name'] : '');
		echo '<div class="ai-obj-card" data-kind="'.htmlspecialchars($kind, ENT_QUOTES, 'UTF-8').'" data-id="'.htmlspecialchars($ob['id'], ENT_QUOTES, 'UTF-8').'" data-name="'.htmlspecialchars(isset($ob['name']) ? $ob['name'] : '', ENT_QUOTES, 'UTF-8').'" data-sku="'.htmlspecialchars(isset($ob['sku']) ? $ob['sku'] : '', ENT_QUOTES, 'UTF-8').'" data-image="'.htmlspecialchars(isset($ob['image']) ? $ob['image'] : '', ENT_QUOTES, 'UTF-8').'" data-status="'.htmlspecialchars(isset($ob['status']) ? $ob['status'] : '', ENT_QUOTES, 'UTF-8').'" data-items="'.htmlspecialchars(isset($ob['items']) ? $ob['items'] : '', ENT_QUOTES, 'UTF-8').'">';
		if ($kind === 'product' && !empty($ob['image'])) {
			echo '<img src="'.htmlspecialchars($ob['image'], ENT_QUOTES, 'UTF-8').'" alt="">';
		} else {
			echo '<div class="ai-prod-ph">'.($kind === 'order' ? '🧾' : '📦').'</div>';
		}
		echo '<div><div class="ai-prod-sku">'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</div>';
		if ($name !== '') {
			echo '<div class="ai-prod-name">'.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'</div>';
		}
		echo '</div></div>';
	}
};
?>
<div class="ai-wrap" id="ai_thread_page"
     data-base="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>"
     data-thread="<?php echo $tid; ?>"
     data-live="<?php echo $live ? '1' : '0'; ?>"
     data-lang="<?php echo $is_en ? 'en' : 'th'; ?>"
     data-attach="<?php echo htmlspecialchars($attach_json, ENT_QUOTES, 'UTF-8'); ?>"
     data-suggest-attach="<?php echo htmlspecialchars($suggest_attach_json, ENT_QUOTES, 'UTF-8'); ?>"
     data-original-draft="<?php echo htmlspecialchars($original_draft_val, ENT_QUOTES, 'UTF-8'); ?>">
  <h3>#<?php echo $tid; ?> · <?php echo htmlspecialchars($thread['platform'], ENT_QUOTES, 'UTF-8'); ?>
    <?php if (!empty($thread['buyer_name'])) { echo ' · '.htmlspecialchars($thread['buyer_name'], ENT_QUOTES, 'UTF-8'); } ?>
    <?php if ($live) { ?>
      <small><?php echo htmlspecialchars($t('ส่งออกไปแพลตฟอร์มจริง', 'Sends to the live platform'), ENT_QUOTES, 'UTF-8'); ?></small>
    <?php } ?>
  </h3>
  <?php if ($live && !empty($sync_at)) { ?>
    <p class="ai-tool-note"><?php echo htmlspecialchars($t('ดึงแชทจาก Shopee ตอนเปิดหน้านี้ '.$sync_at.' ไม่ได้มาจากแคชในเครื่อง — ถ้าลบข้อความในแอปแล้ว รายการนั้นจะหายจากหน้านี้ด้วย', 'Chat is pulled from Shopee when this page opens at '.$sync_at.', not from browser cache. Messages you deleted in the app are removed here too.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <?php } ?>
  <?php if (!empty($flash)) { ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($live ? $t('ส่งถึงลูกค้าบนแพลตฟอร์มแล้ว', 'Sent to the buyer on the platform') : $t('บันทึกในระบบแล้ว', 'Saved locally'), ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <?php if (!empty($sync_err)) { ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($t('ดึงข้อความล่าสุดไม่ครบ: ', 'Could not refresh messages: ').$sync_err, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <?php if (!empty($draft_error)) {
    $err = (string)$draft_error;
    if ($err === 'no_inbound') {
      $err = $t('ยังไม่มีข้อความลูกค้าในห้องนี้', 'This thread has no buyer message yet');
    } elseif ($err === 'missing_api_key') {
      $err = $t('ยังไม่มี API key — ไปใส่ที่ตั้งค่าโมเดลแชท', 'API key is missing — add it in Chat AI settings');
    } elseif ($err === 'http_fail') {
      $err = $t('เรียกโมเดลไม่สำเร็จ ตรวจเน็ตหรือคีย์', 'Model request failed. Check network or API key.');
    } elseif ($err === 'not_live') {
      $err = $t('ห้องนี้ยังไม่ได้เชื่อมแพลตฟอร์ม จึงส่งการ์ดสินค้า/ออเดอร์ไม่ได้', 'This thread is not linked to a platform, so product/order cards cannot be sent.');
    } elseif ($err === 'shopee_token' || $err === 'lazada_token' || $err === 'tiktok_config') {
      $err = $t('โทเค็นแพลตฟอร์มยังไม่พร้อม', 'Platform token is not ready');
    } elseif ($err === 'missing_to_id') {
      $err = $t('ส่งไม่ได้ เพราะยังไม่มีรหัสผู้ซื้อของห้องนี้ ลองรีเฟรชหน้านี้แล้วส่งอีกครั้ง', 'Cannot send: this thread has no buyer id yet. Refresh the page and try again.');
    } elseif ($err === 'empty_item') {
      $err = $t('ไม่พบรหัสสินค้านี้ในร้านบนช่องนี้', 'This product id was not found in this shop.');
    } elseif ($err === 'empty_order') {
      $err = $t('ไม่มีเลขออเดอร์', 'Order id is empty');
    } elseif ($err === 'order_not_this_buyer') {
      $err = $t('ส่งออเดอร์นี้ไม่ได้ เพราะไม่ใช่ออเดอร์ของลูกค้าในห้องนี้', 'This order cannot be sent because it does not belong to the buyer in this thread.');
    } elseif ($err === 'empty_reply') {
      $err = $t('พิมพ์ข้อความ หรือใส่การ์ดสินค้าในช่องตอบก่อน แล้วค่อยกดส่งถึงลูกค้า', 'Type a reply or add a product card first, then click Send to buyer.');
    } elseif ($err === 'empty_coach') {
      $err = $t('พิมพ์ข้อความถึง AI ในกล่องปรึกษาก่อน', 'Type a message to the AI in the private box first.');
    }
  ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>

  <div class="ai-split">
    <div class="ai-thread">
      <div class="ai-msg-list">
      <?php foreach ($messages as $m) {
        $cls = ($m['direction'] === 'in') ? 'in' : 'out';
        $who = ($m['direction'] === 'in') ? $t('ลูกค้า', 'Buyer') : $t('ร้าน', 'Shop');
        $mt = isset($m['msg_type']) ? $m['msg_type'] : 'text';
        $card = (isset($m['card']) && is_array($m['card']) && !empty($m['card']['id'])) ? $m['card'] : null;
      ?>
        <div class="ai-msg <?php echo $cls; ?>">
          <div class="ai-msg-col">
            <div class="ai-msg-who"><?php echo htmlspecialchars($who, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="ai-msg-bubble ai-copy-src" data-text="<?php echo htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8'); ?>" data-attach="<?php echo $obj_attr($card ? array(array(
              'kind' => ($mt === 'order') ? 'order' : 'product',
              'id' => $card['id'],
              'name' => isset($card['name']) ? $card['name'] : '',
              'sku' => isset($card['sku']) ? $card['sku'] : '',
              'image' => isset($card['image']) ? $card['image'] : ''
            )) : array()); ?>">
              <?php if ($card) { ?>
                <?php $render_objs(array(array(
                  'kind' => ($mt === 'order') ? 'order' : 'product',
                  'id' => $card['id'],
                  'name' => isset($card['name']) ? $card['name'] : '',
                  'sku' => isset($card['sku']) ? $card['sku'] : '',
                  'image' => isset($card['image']) ? $card['image'] : ''
                ))); ?>
              <?php } else { ?>
                <?php if ($mt !== '' && $mt !== 'text') { ?>
                  <span class="ai-chip"><?php echo htmlspecialchars($mt, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php } ?>
                <div><?php echo nl2br(htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8')); ?></div>
              <?php } ?>
              <button type="button" class="btn btn-default btn-xs ai-copy-to-send"><?php echo htmlspecialchars($t('คัดลอกไปช่องส่งลูกค้า', 'Copy to send box'), ENT_QUOTES, 'UTF-8'); ?></button>
            </div>
          </div>
        </div>
      <?php } ?>
      </div>
      <?php if (!$live) { ?>
      <form method="post" action="<?php echo $base; ?>ai/add_inbound" style="margin-bottom:12px;">
        <input type="hidden" name="thread_id" value="<?php echo $tid; ?>">
        <label><?php echo htmlspecialchars($t('ข้อความลูกค้าเพิ่ม', 'Add buyer message'), ENT_QUOTES, 'UTF-8'); ?></label>
        <textarea name="body" class="form-control" rows="2"></textarea>
        <button type="submit" class="btn btn-default" style="margin-top:6px;"><?php echo htmlspecialchars($t('ใส่ข้อความลูกค้า', 'Add inbound'), ENT_QUOTES, 'UTF-8'); ?></button>
      </form>
      <?php } ?>

      <div class="ai-coach" id="ai_coach_box">
        <h4><?php echo htmlspecialchars($t('ปรึกษา AI (แอดมินเท่านั้น)', 'Private AI discussion (admin only)'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <p class="ai-tool-note"><?php echo htmlspecialchars($t('กล่องนี้คุยกันไปมาได้ ลูกค้าและแพลตฟอร์มจะไม่เห็น ระบบดึงคู่มือ ตัวอย่างคำตอบ ออเดอร์ และสินค้าจากฐาน '.$db_label.' มาให้ AI วิเคราะห์ ไม่ได้ตอบจาก Cursor', 'This is a back-and-forth with the AI. Buyers never see it. The AI reads playbook, reply examples, orders, and products from '.$db_label.', not from Cursor.'), ENT_QUOTES, 'UTF-8'); ?></p>
        <?php if (empty($has_api_key)) { ?>
        <div class="alert alert-danger" style="margin:8px 0;">
          <?php echo htmlspecialchars($t('ยังใส่ API key ของโมเดลไม่ได้ จึงตอบไม่ได้', 'The language-model API key is missing, so the AI cannot reply.'), ENT_QUOTES, 'UTF-8'); ?>
          <a href="<?php echo $base; ?>ai/settings" style="font-weight:700;margin-left:6px;"><?php echo htmlspecialchars($t('ไปหน้าตั้งค่าโมเดลแชท', 'Open Chat AI settings'), ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
        <?php } ?>
        <div class="ai-coach-list" id="ai_coach_list">
          <?php foreach ($coach as $cm) {
            $is_admin = (isset($cm['role']) && $cm['role'] === 'admin');
            $cls = $is_admin ? 'admin' : 'ai';
            $who = $is_admin ? $t('แอดมิน', 'Admin') : 'AI';
            $cm_objs = array();
            if (!empty($cm['attach_json'])) {
              $tmp = json_decode($cm['attach_json'], true);
              if (is_array($tmp)) {
                $cm_objs = $tmp;
              }
            }
            $copy_text = $is_admin ? '' : (isset($cm['suggest_reply']) && trim((string)$cm['suggest_reply']) !== '' ? $cm['suggest_reply'] : $cm['body']);
          ?>
            <div class="ai-msg <?php echo $cls; ?>">
              <div class="ai-msg-col">
                <div class="ai-msg-who"><?php echo htmlspecialchars($who, ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="ai-msg-bubble ai-copy-src" data-text="<?php echo htmlspecialchars($copy_text, ENT_QUOTES, 'UTF-8'); ?>" data-attach="<?php echo $obj_attr($cm_objs); ?>">
                  <div><?php echo nl2br(htmlspecialchars($cm['body'], ENT_QUOTES, 'UTF-8')); ?></div>
                  <?php $render_objs($cm_objs); ?>
                  <button type="button" class="btn btn-default btn-xs ai-copy-to-send"><?php echo htmlspecialchars($t('คัดลอกไปช่องส่งลูกค้า', 'Copy to send box'), ENT_QUOTES, 'UTF-8'); ?></button>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="ai-coach-status is-on" id="ai_coach_status"><?php echo htmlspecialchars($t('พิมพ์แล้วกด Enter หรือปุ่มส่งถึง AI — Shift+Enter เพื่อขึ้นบรรทัดใหม่', 'Press Enter or Send to AI. Shift+Enter for a new line.'), ENT_QUOTES, 'UTF-8'); ?></div>
        <form id="ai_coach_form" method="post" action="<?php echo $base; ?>ai/coach">
          <input type="hidden" name="thread_id" value="<?php echo $tid; ?>">
          <input type="hidden" name="attach_json" id="ai_coach_attach_json" value="">
          <div id="ai_coach_attach" class="ai-compose-attach"></div>
          <textarea name="body" id="ai_coach_body" class="form-control" rows="3"
                    placeholder="<?php echo htmlspecialchars($t('พิมพ์ถึง AI เช่น ควรตอบว่าหากทำหม่าล่าให้แนะนำซอส แล้วใส่การ์ดสินค้าด้านขวา', 'Teach the AI, then attach a product card from the right'), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
          <p class="ai-tool-note"><?php echo htmlspecialchars($t('Enter = ส่งถึง AI · Shift+Enter = ขึ้นบรรทัดใหม่ · กด + เพื่อใส่สินค้าหรือออเดอร์ของลูกค้าคนนี้', 'Enter = send to AI · Shift+Enter = new line · Press + to insert a product or this buyer’s order'), ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="ai-send-row">
            <button type="button" class="ai-plus-btn" data-plus="coach" aria-label="+">+</button>
            <button type="submit" class="btn btn-primary" id="ai_coach_send"><?php echo htmlspecialchars($t('ส่งถึง AI', 'Send to AI'), ENT_QUOTES, 'UTF-8'); ?></button>
          </div>
        </form>
        <form method="post" action="<?php echo $base; ?>ai/draft" style="display:inline;margin-top:6px;">
          <input type="hidden" name="thread_id" value="<?php echo $tid; ?>">
          <button type="submit" class="btn btn-default"><?php echo htmlspecialchars($t('ให้ AI วิเคราะห์จากฐานร้าน แล้วร่างคำตอบ', 'Analyze shop database and draft a reply'), ENT_QUOTES, 'UTF-8'); ?></button>
        </form>
      </div>

      <div class="ai-hitl">
        <div class="ai-hitl-col ai-suggest">
          <label><?php echo htmlspecialchars($t('AI เตรียมไว้ (ยังไม่ส่งถึงลูกค้า)', 'AI prepared (not sent to the buyer)'), ENT_QUOTES, 'UTF-8'); ?></label>
          <p class="ai-tool-note"><?php echo htmlspecialchars($t('แก้ข้อความนี้ได้ กด + เพื่อใส่สินค้าหรือออเดอร์ แล้วค่อยส่งถึงลูกค้า ระบบจะเก็บต้นฉบับ AI กับข้อความที่ส่งจริงไว้ในฐานร้าน', 'You can edit this draft. Press + to add a product or order, then send. The shop database stores the AI draft and the text you actually sent.'), ENT_QUOTES, 'UTF-8'); ?></p>
          <div id="ai_suggest_attach" class="ai-compose-attach"></div>
          <textarea id="ai_suggest_box" class="form-control ai-draft"><?php echo htmlspecialchars($draft_val, ENT_QUOTES, 'UTF-8'); ?></textarea>
          <div class="ai-send-row">
            <button type="button" class="ai-plus-btn" data-plus="suggest" aria-label="+">+</button>
            <button type="button" class="btn btn-primary" id="ai_use_suggest"><?php echo htmlspecialchars($t('ใช้ข้อความนี้ แล้วส่งถึงลูกค้า', 'Use this reply and send to buyer'), ENT_QUOTES, 'UTF-8'); ?></button>
          </div>
        </div>
        <div class="ai-hitl-col">
          <form method="post" action="<?php echo $base; ?>ai/send" id="ai_send_form">
            <input type="hidden" name="thread_id" value="<?php echo $tid; ?>">
            <input type="hidden" name="attach_json" id="ai_attach_json" value="">
            <input type="hidden" name="ai_original_draft" id="ai_original_draft" value="<?php echo htmlspecialchars($original_draft_val, ENT_QUOTES, 'UTF-8'); ?>">
            <textarea name="ai_draft" id="ai_draft_hidden" style="display:none;"><?php echo htmlspecialchars($original_draft_val, ENT_QUOTES, 'UTF-8'); ?></textarea>
            <label><?php echo htmlspecialchars($live ? $t('ส่งถึงลูกค้า (คนพิมพ์/แก้เอง)', 'Send to buyer (human writes or edits)') : $t('บันทึกในระบบ (คนพิมพ์/แก้เอง)', 'Save locally (human writes or edits)'), ENT_QUOTES, 'UTF-8'); ?></label>
            <p class="ai-tool-note"><?php echo htmlspecialchars($t('กด + เพื่อใส่สินค้าทั้งร้าน หรือออเดอร์ของลูกค้าคนนี้ แล้วค่อยกดส่งถึงลูกค้า คัดลอกข้อความหรือการ์ดจากบล็อกอื่นแล้วนำมาวางได้', 'Press + to add a shop product or this buyer’s order, then send. You can copy text or cards from any block and paste them here.'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div id="ai_compose_attach" class="ai-compose-attach"></div>
            <textarea name="body" id="ai_buyer_body" class="form-control ai-draft"></textarea>
            <div class="ai-send-row">
              <button type="button" class="ai-plus-btn" data-plus="buyer" aria-label="+">+</button>
              <button type="submit" class="btn btn-primary" id="ai_send_btn"><?php echo htmlspecialchars($live ? $t('ส่งถึงลูกค้า', 'Send to buyer') : $t('บันทึกในระบบ', 'Save locally'), ENT_QUOTES, 'UTF-8'); ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="ai-tools">
      <h4><?php echo htmlspecialchars($t('เครื่องมือของช่องนี้', 'Tools for this channel'), ENT_QUOTES, 'UTF-8'); ?></h4>
      <p class="ai-tool-note"><?php echo htmlspecialchars($t('รหัสสินค้าและออเดอร์เป็นของแพลตฟอร์มที่ลูกค้าคุยอยู่เท่านั้น', 'Product and order IDs belong only to the platform this buyer is on.'), ENT_QUOTES, 'UTF-8'); ?></p>

      <h5><?php echo htmlspecialchars($t('ออเดอร์', 'Orders'), ENT_QUOTES, 'UTF-8'); ?></h5>
      <?php if (empty($orders)) { ?>
        <p><?php echo htmlspecialchars($t('ยังจับออเดอร์ของลูกค้าคนนี้ไม่ได้ จากชื่อ ผู้ซื้อ หรือเลขในแชท', 'No matching orders yet from this buyer name or numbers in chat.'), ENT_QUOTES, 'UTF-8'); ?></p>
      <?php } else { foreach ($orders as $o) { ?>
        <div class="ai-card ai-order-card"
             data-kind="order"
             data-id="<?php echo htmlspecialchars($o['order_id'], ENT_QUOTES, 'UTF-8'); ?>"
             data-name="<?php echo htmlspecialchars(isset($o['items']) ? $o['items'] : '', ENT_QUOTES, 'UTF-8'); ?>"
             data-status="<?php echo htmlspecialchars((string)$o['status'], ENT_QUOTES, 'UTF-8'); ?>"
             data-items="<?php echo htmlspecialchars(isset($o['items']) ? $o['items'] : '', ENT_QUOTES, 'UTF-8'); ?>">
          <div><strong><?php echo htmlspecialchars($o['order_id'], ENT_QUOTES, 'UTF-8'); ?></strong> · <?php echo htmlspecialchars((string)$o['status'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php if (!empty($o['items'])) { ?><div><?php echo htmlspecialchars($o['items'], ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
          <button type="button" class="btn btn-default btn-sm ai-order-insert"><?php echo htmlspecialchars($t('ใส่การ์ดออเดอร์', 'Insert order card'), ENT_QUOTES, 'UTF-8'); ?></button>
        </div>
      <?php } } ?>
      <?php if ($live) { ?>
      <form method="post" action="<?php echo $base; ?>ai/send_order" class="ai-inline">
        <input type="hidden" name="thread_id" value="<?php echo $tid; ?>">
        <input type="text" name="order_id" class="form-control" placeholder="<?php echo htmlspecialchars($t('เลขออเดอร์บนช่องนี้', 'Order ID on this channel'), ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-default"><?php echo htmlspecialchars($t('ส่งออเดอร์', 'Send order'), ENT_QUOTES, 'UTF-8'); ?></button>
      </form>
      <?php } ?>

      <h5><?php echo htmlspecialchars($t('ค้นหาสินค้าในร้านนี้', 'Search products in this shop'), ENT_QUOTES, 'UTF-8'); ?></h5>
      <div class="ai-insert-target" id="ai_insert_target">
        <span><?php echo htmlspecialchars($t('ใส่การ์ดไปที่', 'Insert cards into'), ENT_QUOTES, 'UTF-8'); ?></span>
        <label><input type="radio" name="ai_insert_target" value="coach" checked> <?php echo htmlspecialchars($t('ปรึกษา AI', 'AI discussion'), ENT_QUOTES, 'UTF-8'); ?></label>
        <label><input type="radio" name="ai_insert_target" value="suggest"> <?php echo htmlspecialchars($t('AI เตรียมไว้', 'AI prepared'), ENT_QUOTES, 'UTF-8'); ?></label>
        <label><input type="radio" name="ai_insert_target" value="buyer"> <?php echo htmlspecialchars($t('ส่งถึงลูกค้า', 'Send to buyer'), ENT_QUOTES, 'UTF-8'); ?></label>
      </div>
      <p class="ai-tool-note"><?php echo htmlspecialchars($t('พิมพ์รหัสหรือชื่อ เช่น S หรือ S2000 แล้วกดสินค้า การ์ดจะไปกล่องที่เลือกไว้ ยังไม่ส่งออกแพลตฟอร์ม', 'Type a SKU or name such as S or S2000, then click a product. The card goes to the selected box and is not sent yet.'), ENT_QUOTES, 'UTF-8'); ?></p>
      <div class="ai-prod-search">
        <input type="search" id="ai_prod_q" class="form-control" autocomplete="off"
               placeholder="<?php echo htmlspecialchars($t('เช่น S2000', 'e.g. S2000'), ENT_QUOTES, 'UTF-8'); ?>">
        <div class="ai-prod-status" id="ai_prod_status"></div>
        <div class="ai-prod-strip" id="ai_prod_strip"></div>
      </div>
    </div>
  </div>

  <div class="ai-plus-modal" id="ai_plus_modal" hidden>
    <div class="ai-plus-dialog" role="dialog" aria-modal="true" aria-labelledby="ai_plus_title">
      <button type="button" class="ai-plus-close" id="ai_plus_close" aria-label="<?php echo htmlspecialchars($t('ปิด', 'Close'), ENT_QUOTES, 'UTF-8'); ?>">&times;</button>
      <div class="ai-plus-home" id="ai_plus_home">
        <h4 id="ai_plus_title"><?php echo htmlspecialchars($t('เลือกสิ่งที่จะใส่', 'Choose what to insert'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <p class="ai-tool-note"><?php echo htmlspecialchars($t('สินค้าค้นได้ทั้งร้าน ออเดอร์เฉพาะที่ลูกค้าในห้องนี้สั่ง', 'Products search the whole shop. Orders are only this buyer’s.'), ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="ai-plus-grid">
          <button type="button" class="ai-plus-tile" data-kind="product">
            <span class="ai-plus-ico" aria-hidden="true">👕</span>
            <span><?php echo htmlspecialchars($t('สินค้า', 'Products'), ENT_QUOTES, 'UTF-8'); ?></span>
          </button>
          <button type="button" class="ai-plus-tile" data-kind="order">
            <span class="ai-plus-ico" aria-hidden="true">📋</span>
            <span><?php echo htmlspecialchars($t('คำสั่งซื้อ', 'Orders'), ENT_QUOTES, 'UTF-8'); ?></span>
          </button>
        </div>
      </div>
      <div class="ai-plus-search" id="ai_plus_search" hidden>
        <button type="button" class="btn btn-default btn-sm" id="ai_plus_back"><?php echo htmlspecialchars($t('กลับ', 'Back'), ENT_QUOTES, 'UTF-8'); ?></button>
        <h4 id="ai_plus_search_title"></h4>
        <input type="search" id="ai_plus_q" class="form-control" autocomplete="off">
        <div class="ai-plus-status" id="ai_plus_status"></div>
        <div class="ai-plus-list" id="ai_plus_list"></div>
        <button type="button" class="btn btn-primary" id="ai_plus_apply"><?php echo htmlspecialchars($t('ใส่ที่เลือก', 'Insert selected'), ENT_QUOTES, 'UTF-8'); ?></button>
      </div>
    </div>
  </div>
</div>
