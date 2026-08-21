<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$sum = isset($summary) && is_array($summary) ? $summary : array();
$token_n = isset($sum['token_shops']) ? (int)$sum['token_shops'] : 0;
$status_n = isset($sum['status_issues']) ? (int)$sum['status_issues'] : 0;
$base = base_url();
?>
<div class="alerts-wrap">
  <h3><?php echo htmlspecialchars($t('อะเลิร์ท', 'Alerts'), ENT_QUOTES, 'UTF-8'); ?></h3>
  <p><?php echo htmlspecialchars($t('รวมเรื่องที่แอดมินต้องดู', 'Issues the admin should check.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <ul class="alerts-topics">
    <li>
      <a href="<?php echo htmlspecialchars($base.'alerts/token_expire', ENT_QUOTES, 'UTF-8'); ?>">Token expire<?php echo $token_n > 0 ? ' ('.$token_n.')' : ''; ?></a>
      <span><?php echo htmlspecialchars($t('โทเค็น API ของร้านใช้ไม่ได้หลังจ็อบรีเฟรช', 'Shop API token still fails after the refresh job.'), ENT_QUOTES, 'UTF-8'); ?></span>
    </li>
    <li>
      <a href="<?php echo htmlspecialchars($base.'alerts/status_change', ENT_QUOTES, 'UTF-8'); ?>">Status change<?php echo $status_n > 0 ? ' ('.$status_n.')' : ''; ?></a>
      <span><?php echo htmlspecialchars($t('สถานะใหม่จาก API/ฐานข้อมูล ที่ลอจิกซอฟต์แวร์ยังไม่รู้จัก ต้องมาคุยปรับ', 'New API/DB statuses our software logic does not know yet — discuss before changing code.'), ENT_QUOTES, 'UTF-8'); ?></span>
    </li>
  </ul>
</div>
