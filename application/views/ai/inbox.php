<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$base = base_url();
$boot = (isset($boot) && is_array($boot)) ? $boot : array();
$boot_json = json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
?>
<div class="ai-chat-page" id="ai_inbox" data-base="<?php echo htmlspecialchars($base, ENT_QUOTES, 'UTF-8'); ?>" data-lang="<?php echo $is_en ? 'en' : 'th'; ?>">
  <?php if (!empty($flash)) { ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($t('ส่งแล้ว', 'Sent'), ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>

  <div class="ai-chat-head">
    <h2 class="ai-chat-title"><?php echo htmlspecialchars($t('พูดคุยกับผู้ซื้อ', 'Chat with buyers'), ENT_QUOTES, 'UTF-8'); ?></h2>
    <label class="ai-chat-search">
      <i class="fas fa-search" aria-hidden="true"></i>
      <input type="search" id="ai_chat_q" placeholder="<?php echo htmlspecialchars($t('ค้นหา', 'Search'), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
    </label>
  </div>

  <div class="ai-chat-banner" id="ai_chat_banner">
    <i class="fas fa-bell" aria-hidden="true"></i>
    <span><?php echo htmlspecialchars($t('ข้อความทั้งหมดจากผู้ซื้อที่ต้องตอบจะปรากฏที่นี่', 'All buyer messages that need a reply appear here'), ENT_QUOTES, 'UTF-8'); ?></span>
    <button type="button" class="ai-chat-banner-x" id="ai_chat_banner_x" aria-label="close">&times;</button>
  </div>

  <div class="ai-chat-pills ai-chat-plats" role="tablist">
    <button type="button" class="ai-pill is-on" data-plat="shopee">Shopee <span data-tab-count="shopee"></span></button>
    <button type="button" class="ai-pill" data-plat="lazada">Lazada <span data-tab-count="lazada"></span></button>
    <button type="button" class="ai-pill" data-plat="tiktok">TikTok <span data-tab-count="tiktok"></span></button>
  </div>

  <div class="ai-chat-pills ai-chat-filters" role="tablist">
    <button type="button" class="ai-pill" data-filter="all"><?php echo htmlspecialchars($t('ทั้งหมด', 'All'), ENT_QUOTES, 'UTF-8'); ?></button>
    <button type="button" class="ai-pill is-on" data-filter="wait"><?php echo htmlspecialchars($t('รอตอบกลับ', 'Waiting for reply'), ENT_QUOTES, 'UTF-8'); ?><span class="ai-pill-dot" data-filter-dot="wait" hidden></span></button>
    <button type="button" class="ai-pill" data-filter="follow"><?php echo htmlspecialchars($t('รอติดตาม', 'Follow up'), ENT_QUOTES, 'UTF-8'); ?></button>
    <button type="button" class="ai-pill" data-filter="replied"><?php echo htmlspecialchars($t('ตอบกลับแล้ว', 'Replied'), ENT_QUOTES, 'UTF-8'); ?></button>
  </div>

  <div class="ai-inbox-status" id="ai_inbox_status"></div>

  <div class="ai-thread-scroll" id="ai_thread_scroll">
    <button type="button" class="ai-scroll-hint ai-scroll-hint-up" id="ai_scroll_up" aria-hidden="true"><i class="fas fa-chevron-up"></i></button>
    <div class="ai-thread-list" id="ai_thread_list"></div>
    <button type="button" class="ai-scroll-hint ai-scroll-hint-down" id="ai_scroll_down" aria-hidden="true"><i class="fas fa-chevron-down"></i></button>
  </div>

  <details class="ai-manual">
    <summary><?php echo htmlspecialchars($t('เปิดห้องทดลองมือ (ไม่ส่งออกแพลตฟอร์ม)', 'Manual test thread (does not send to a platform)'), ENT_QUOTES, 'UTF-8'); ?></summary>
    <form method="post" action="<?php echo $base; ?>ai/new_thread" class="ai-thread" style="margin-top:10px;">
      <div class="form-group">
        <label><?php echo htmlspecialchars($t('แพลตฟอร์ม', 'Platform'), ENT_QUOTES, 'UTF-8'); ?></label>
        <select name="platform" class="form-control" style="max-width:220px;">
          <option value="shopee">Shopee</option>
          <option value="lazada">Lazada</option>
          <option value="tiktok">TikTok</option>
        </select>
      </div>
      <div class="form-group">
        <label><?php echo htmlspecialchars($t('ชื่อผู้ซื้อ (ถ้ามี)', 'Buyer name'), ENT_QUOTES, 'UTF-8'); ?></label>
        <input type="text" name="buyer_name" class="form-control" style="max-width:320px;">
      </div>
      <div class="form-group">
        <label><?php echo htmlspecialchars($t('ข้อความลูกค้า', 'Buyer message'), ENT_QUOTES, 'UTF-8'); ?></label>
        <textarea name="inbound" class="form-control" rows="3"></textarea>
      </div>
      <button type="submit" class="btn btn-default"><?php echo htmlspecialchars($t('เปิดห้องทดลอง', 'Open test thread'), ENT_QUOTES, 'UTF-8'); ?></button>
    </form>
  </details>
</div>
<script>
window.AI_INBOX_BOOT = <?php echo $boot_json ? $boot_json : '{}'; ?>;
</script>
