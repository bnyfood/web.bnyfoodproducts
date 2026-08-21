<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$sum = isset($summary) && is_array($summary) ? $summary : array();
$status = isset($sum['status']) && is_array($sum['status']) ? $sum['status'] : array();
$issues = isset($status['issues']) && is_array($status['issues']) ? $status['issues'] : array();
$n = isset($sum['status_issues']) ? (int)$sum['status_issues'] : count($issues);
$checked = isset($status['checked_at']) ? $status['checked_at'] : (isset($sum['checked_at']) ? $sum['checked_at'] : '');
?>
<div class="alerts-wrap">
  <h3>Status change<?php echo $n > 0 ? ' ('.$n.')' : ''; ?></h3>
  <p><?php echo htmlspecialchars($t('จ็อบเทียบสถานะที่มีจริงในฐาน (มาจาก API) กับรายการที่ซอฟต์แวร์รู้จักแล้ว ถ้ามีค่าใหม่ให้นำมาคุยก่อนแก้ลอจิก', 'The job compares live DB statuses (from APIs) with the catalog our software already handles. New values need a talk before logic changes.'), ENT_QUOTES, 'UTF-8'); ?>
    <?php if ($checked !== '') { echo ' · '.$checked; } ?></p>
  <?php if ($n < 1) { ?>
    <p class="alerts-ok"><?php echo htmlspecialchars($t('ยังไม่พบสถานะใหม่', 'No unknown statuses right now.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <?php } else { ?>
    <table class="alerts-status-table">
      <thead>
        <tr>
          <th><?php echo htmlspecialchars($t('กลุ่ม', 'Group'), ENT_QUOTES, 'UTF-8'); ?></th>
          <th>Status</th>
          <th><?php echo htmlspecialchars($t('จำนวน', 'Count'), ENT_QUOTES, 'UTF-8'); ?></th>
          <th><?php echo htmlspecialchars($t('ลอจิกที่อาจกระทบ', 'Logic that may change'), ENT_QUOTES, 'UTF-8'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($issues as $row) { ?>
          <tr>
            <td><?php echo htmlspecialchars(isset($row['label']) ? $row['label'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><code><?php echo htmlspecialchars(isset($row['status']) ? $row['status'] : '', ENT_QUOTES, 'UTF-8'); ?></code></td>
            <td><?php echo isset($row['count']) ? (int)$row['count'] : 0; ?></td>
            <td><?php echo htmlspecialchars(isset($row['logic']) ? $row['logic'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } ?>
</div>
