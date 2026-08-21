<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$base = base_url();
?>
<div class="ai-wrap">
  <div class="ai-nav">
    <a href="<?php echo $base; ?>ai/inbox"><?php echo htmlspecialchars($t('แชท', 'Chat'), ENT_QUOTES, 'UTF-8'); ?></a>
    <a href="<?php echo $base; ?>ai/playbook"><?php echo htmlspecialchars($t('คู่มือการตอบ', 'Playbook'), ENT_QUOTES, 'UTF-8'); ?></a>
    <a href="<?php echo $base; ?>ai/settings"><?php echo htmlspecialchars($t('ระบบแชท', 'Chat AI'), ENT_QUOTES, 'UTF-8'); ?></a>
  </div>
  <h3><?php echo htmlspecialchars($t('คู่มือการตอบที่เรียนรู้จากคน', 'Reply playbook learned from humans'), ENT_QUOTES, 'UTF-8'); ?></h3>
  <?php if (!empty($flash)) { ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($t('อัปเดตคู่มือแล้ว', 'Playbook updated'), ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <?php if (!empty($distill_error)) {
    $err = (string)$distill_error;
    if ($err === 'missing_api_key') {
      $err = $t('ยังไม่มี API key — ไปใส่ที่ Settings โมเดล', 'API key is missing — add it in model settings');
    } elseif ($err === 'no_examples') {
      $err = $t('ยังไม่มีตัวอย่างการตอบ ส่งข้อความในกล่องแชทก่อน', 'No reply examples yet. Send a reply in the inbox first.');
    }
  ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <p><?php echo htmlspecialchars($t('คู่มือนี้เก็บในฐานข้อมูลร้าน แล้วสรุปจากตัวอย่างที่คนส่งจริง (ตาราง chat_reply_example → chat_playbook) ตอนปรึกษา AI ระบบจะดึงจากฐานนี้ไปวิเคราะห์ ไม่ได้จำจาก Cursor', 'This playbook is stored in the shop database. It is summarized from real human sends (chat_reply_example → chat_playbook). When you discuss with the AI, it reads this database — it does not remember Cursor chats.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <form method="post" action="<?php echo $base; ?>ai/distill" style="margin-bottom:14px;">
    <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars($t('สรุปคู่มือใหม่จากตัวอย่างทั้งหมด', 'Rebuild playbook from all examples'), ENT_QUOTES, 'UTF-8'); ?></button>
  </form>
  <?php
  if (empty($books)) {
    echo '<p>'.htmlspecialchars($t('ยังไม่มีคู่มือ ส่งคำตอบในกล่องแชทก่อน', 'No playbook yet. Send replies in the inbox first.'), ENT_QUOTES, 'UTF-8').'</p>';
  }
  foreach ($books as $b) {
    if (trim((string)$b['rules_text']) === '') {
      continue;
    }
  ?>
    <h4><?php echo htmlspecialchars($b['platform'], ENT_QUOTES, 'UTF-8'); ?>
      <small>(<?php echo (int)$b['example_count']; ?> <?php echo htmlspecialchars($t('ตัวอย่าง', 'examples'), ENT_QUOTES, 'UTF-8'); ?>)</small>
    </h4>
    <div class="ai-playbook"><?php echo htmlspecialchars($b['rules_text'], ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
</div>
