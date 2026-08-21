<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$sum = isset($summary) && is_array($summary) ? $summary : array();
$shops = isset($sum['shops']) && is_array($sum['shops']) ? $sum['shops'] : array();
$laz = isset($shops['lazada']) ? $shops['lazada'] : array();
$sho = isset($shops['shopee']) ? $shops['shopee'] : array();
$tik = isset($shops['tiktok']) ? $shops['tiktok'] : array();
$shopee_href = isset($shopee_link) ? $shopee_link : '#';
$checked = isset($sum['checked_at']) ? $sum['checked_at'] : '';
$n = isset($sum['token_shops']) ? (int)$sum['token_shops'] : 0;
function alerts_shop_left($row) {
	return isset($row['left']) ? (string)$row['left'] : 'n/a';
}
function alerts_shop_ok($row) {
	return !empty($row['ok']);
}
function alerts_shop_note($row, $t) {
	if (alerts_shop_ok($row)) {
		return $t('API ใช้ได้', 'API ok');
	}
	$err = isset($row['error']) ? trim((string)$row['error']) : '';
	if ($err === '') {
		return $t('โทเค็นใช้ไม่ได้', 'Token unusable');
	}
	if (function_exists('mb_substr')) {
		$err = mb_substr($err, 0, 80);
	} else {
		$err = substr($err, 0, 80);
	}
	return $err;
}
?>
<div class="alerts-wrap">
  <h3>Token expire<?php echo $n > 0 ? ' ('.$n.')' : ''; ?></h3>
  <p><?php echo htmlspecialchars($t('ผลจากจ็อบ platform_token ที่รีเฟรชแล้วทดสอบ API จริง ไม่ใช่โทเค็นล็อกอินหลังบ้าน', 'From the platform_token job: refresh, then live API ping. Not the admin login token.'), ENT_QUOTES, 'UTF-8'); ?>
    <?php if ($checked !== '') { echo ' · '.$checked; } ?></p>
  <div class="dash-token-row alerts-token-row">
    <span class="dash-token-item<?php echo alerts_shop_ok($laz) ? '' : ' is-bad'; ?>">
      <span class="dash-token-name">Lazada</span>
      <span>Expire in <?php echo htmlspecialchars(alerts_shop_left($laz), ENT_QUOTES, 'UTF-8'); ?></span>
      <span class="alerts-api-note"><?php echo htmlspecialchars(alerts_shop_note($laz, $t), ENT_QUOTES, 'UTF-8'); ?></span>
      <a href="https://auth.lazada.com/oauth/authorize?response_type=code&amp;redirect_uri=https://www.bnyfoodproducts.com/lazcallback&amp;force_auth=true&amp;client_id=123793" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
    </span>
    <span class="dash-token-item<?php echo alerts_shop_ok($sho) ? '' : ' is-bad'; ?>">
      <span class="dash-token-name">Shopee</span>
      <span>Expire in <?php echo htmlspecialchars(alerts_shop_left($sho), ENT_QUOTES, 'UTF-8'); ?></span>
      <span class="alerts-api-note"><?php echo htmlspecialchars(alerts_shop_note($sho, $t), ENT_QUOTES, 'UTF-8'); ?></span>
      <a href="<?php echo htmlspecialchars($shopee_href, ENT_QUOTES, 'UTF-8'); ?>" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
    </span>
    <span class="dash-token-item<?php echo alerts_shop_ok($tik) ? '' : ' is-bad'; ?>">
      <span class="dash-token-name">TikTok</span>
      <span>Expire in <?php echo htmlspecialchars(alerts_shop_left($tik), ENT_QUOTES, 'UTF-8'); ?></span>
      <span class="alerts-api-note"><?php echo htmlspecialchars(alerts_shop_note($tik, $t), ENT_QUOTES, 'UTF-8'); ?></span>
      <a href="https://services.tiktokshop.com/open/authorize?service_id=7389572888133519109" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
    </span>
  </div>
</div>
