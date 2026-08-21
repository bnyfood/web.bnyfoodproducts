<style>
  .bny-result-header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e8e8e8;
  }
  .bny-result-logo img {
    width: 88px;
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    border: 1px solid #d9d9d9;
    background: #fff;
    object-fit: contain;
    display: block;
  }
  .bny-result-user {
    text-align: right;
    flex: 1;
    min-width: 160px;
    font-size: 14px;
    color: #333;
    line-height: 1.5;
  }
  .bny-result-user strong {
    display: block;
    font-size: 15px;
    color: #1f1f1f;
    margin-bottom: 4px;
  }
  .bny-action-wrap {
    margin-top: 10px;
    display: inline-flex;
    gap: 8px;
    align-items: center;
  }
  .bny-action-btn {
    display: inline-block;
    padding: 7px 14px;
    border-radius: 10px;
    border: 1px solid #bdbdbd;
    background: #fff;
    color: #1f1f1f;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
  }
  .bny-action-btn:hover {
    text-decoration: none;
    color: #1f1f1f;
    border-color: #8f8f8f;
  }
  .bny-logout-btn {
    border-color: #d43f3a;
    color: #d43f3a;
  }
  .bny-logout-btn:hover {
    color: #b52b27;
    border-color: #b52b27;
  }

  /* กงล้อออกรางวัล — อยู่ในกรอบการ์ดเท่านั้น (ไม่ทับเต็มจอ) */
  .bny-result-card {
    position: relative;
    overflow: hidden;
  }
  .bny-wheel-overlay {
    position: absolute;
    inset: 0;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 16px 14px;
    box-sizing: border-box;
    border-radius: inherit;
    background: radial-gradient(ellipse 120% 100% at 50% 28%, #fffef9 0%, #fff3d4 42%, #ffd98c 100%);
    transition: opacity 0.55s ease, visibility 0.55s ease;
    touch-action: none;
  }
  .bny-wheel-overlay.bny-wheel-overlay--done {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }
  .bny-wheel-title {
    margin: 0 0 14px;
    font-size: clamp(15px, 3.8vw, 20px);
    font-weight: 800;
    color: #3d2e00;
    text-align: center;
    letter-spacing: -0.02em;
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.6);
    padding: 0 4px;
  }
  .bny-wheel-disc-wrap {
    position: relative;
    aspect-ratio: 1;
    width: min(300px, 86%);
    max-width: 100%;
    margin: 0 auto;
  }
  .bny-wheel-pointer {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 4;
    width: 0;
    height: 0;
    border-left: clamp(14px, 4vw, 18px) solid transparent;
    border-right: clamp(14px, 4vw, 18px) solid transparent;
    border-top: clamp(22px, 6vw, 28px) solid #c62828;
    filter: drop-shadow(0 3px 4px rgba(0, 0, 0, 0.28));
  }
  .bny-wheel-disc {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: conic-gradient(
      #e53935 0deg 45deg,
      #ffca28 45deg 90deg,
      #e53935 90deg 135deg,
      #ffca28 135deg 180deg,
      #e53935 180deg 225deg,
      #ffca28 225deg 270deg,
      #e53935 270deg 315deg,
      #ffca28 315deg 360deg
    );
    border: clamp(8px, 2vw, 12px) solid #b8860b;
    box-shadow:
      0 12px 40px rgba(0, 0, 0, 0.22),
      inset 0 0 0 4px rgba(255, 255, 255, 0.35),
      inset 0 -6px 16px rgba(0, 0, 0, 0.08);
    transform-origin: center center;
    will-change: transform;
  }
  .bny-wheel-disc.bny-wheel-disc--spin {
    animation: bny-wheel-rotate 5s cubic-bezier(0.12, 0.72, 0.18, 1) forwards;
  }
  @keyframes bny-wheel-rotate {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(2880deg);
    }
  }
  .bny-wheel-hub {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 3;
    width: 22%;
    height: 22%;
    min-width: 52px;
    min-height: 52px;
    border-radius: 50%;
    background: linear-gradient(160deg, #fff9e6 0%, #f0d78c 45%, #c9a227 100%);
    border: 3px solid #a67c00;
    box-shadow:
      0 4px 12px rgba(0, 0, 0, 0.2),
      inset 0 2px 6px rgba(255, 255, 255, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: clamp(20px, 9vmin, 30px);
    line-height: 1;
    pointer-events: none;
  }

  .bny-prize-result-wrap {
    position: relative;
    min-height: auto;
  }
  .bny-prize-result-body {
    text-align: center;
    font-size: 15px;
    color: #333;
    line-height: 1.65;
    padding-top: 4px;
  }
  .bny-prize-result-body .bny-prize-round-title {
    margin: 0 0 14px;
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.35;
    letter-spacing: -0.02em;
  }
  .bny-prize-result-body .bny-week-range {
    margin: 0 0 28px;
    padding: 12px 14px;
    background: #f7f7f7;
    border-radius: 12px;
    border: 1px solid #ececec;
    font-weight: 600;
    color: #1f1f1f;
    text-align: center;
    font-size: 15px;
  }
  .bny-prize-result-body .bny-prize-winner-label {
    margin: 0 0 10px;
    font-size: 20px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.3;
  }
  .bny-prize-result-body .bny-prize-winner-phone {
    margin: 0;
    font-size: 32px;
    font-weight: 800;
    color: #c62828;
    letter-spacing: 0.02em;
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 10px;
    max-width: 100%;
  }
  .bny-prize-result-body .bny-prize-winner-cer1 {
    display: inline-block;
    height: 40px;
    width: auto;
    max-width: min(120px, 28vw);
    vertical-align: middle;
    object-fit: contain;
  }
  .bny-prize-gift-wrap {
    margin: 20px auto 0;
    max-width: 260px;
    padding: 12px;
    background: #fffaf5;
    border: 1px solid #f0e6d8;
    border-radius: 14px;
    text-align: center;
  }
  .bny-prize-gift-img {
    display: block;
    width: auto;
    max-width: min(160px, 55vw);
    max-height: 120px;
    margin: 0 auto 10px;
    object-fit: contain;
    border-radius: 10px;
  }
  .bny-prize-gift-detail {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #333;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
  }
  .bny-prize-delivery-note {
    margin: 18px 0 0;
    font-size: 14px;
    font-weight: 600;
    color: #c62828;
    line-height: 1.5;
  }
  .bny-result-home-wrap {
    margin-top: 28px;
    padding-top: 4px;
  }
  .bny-result-archive-wrap {
    margin-top: 24px;
  }
  .bny-result-home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #1f1f1f;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    box-sizing: border-box;
  }
  .bny-result-home-btn:hover {
    text-decoration: none;
    border-color: #bdbdbd;
    color: #1f1f1f;
  }
  @media (max-width: 576px) {
    .bny-result-user {
      text-align: left;
      width: 100%;
    }
    .bny-result-header {
      flex-direction: column;
    }
    .bny-prize-result-body .bny-prize-winner-phone {
      font-size: 26px;
    }
    .bny-prize-result-body .bny-prize-winner-cer1 {
      height: 34px;
    }
    .bny-prize-gift-img {
      max-width: min(140px, 50vw);
      max-height: 100px;
    }
  }
  @media (prefers-reduced-motion: reduce) {
    .bny-wheel-overlay {
      transition: none;
    }
    .bny-wheel-disc.bny-wheel-disc--spin {
      animation: none !important;
      transform: rotate(0deg) !important;
    }
  }
</style>

<div class="social-login-page" id="bnyResultPage" data-bny-login-id="<?php echo isset($bny_result_login_id) ? (int) $bny_result_login_id : 0; ?>" data-bny-reward-id="<?php echo isset($bny_result_reward_id) ? (int) $bny_result_reward_id : 0; ?>">
  <div class="social-login-card bny-result-card">
    <div class="bny-result-header">
      <div class="bny-result-logo">
        <?php if (!empty($logo_url)) { ?>
          <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
        <?php } else { ?>
          <span class="logo-fallback" style="font-size:12px;padding:6px 10px;">BNY</span>
        <?php } ?>
      </div>
      <div class="bny-result-user">
        <strong><?php echo !empty($customer_name) ? htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') : '-'; ?></strong>
        <span>เบอร์โทร <?php echo !empty($customer_phone) ? htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8') : '-'; ?></span>
        <div class="bny-action-wrap">
          <a class="bny-action-btn" href="<?php echo base_url('bnyreward/bny_changephone'); ?>">เปลี่ยนเบอร์โทร</a>
          <a class="bny-action-btn bny-logout-btn" href="<?php echo base_url('bnyreward/bny_logout'); ?>">Logout</a>
        </div>
      </div>
    </div>

    <div class="bny-prize-result-wrap">
      <div class="bny-prize-result-body" id="bnyPrizeResultBody">
        <p class="bny-prize-round-title">ผลรางวัลรอบประจำวันที่</p>
        <p class="bny-week-range"><?php echo !empty($week_range_label) ? htmlspecialchars($week_range_label, ENT_QUOTES, 'UTF-8') : ''; ?></p>
        <?php
        $pwd = isset($prize_winner_phone_display) ? trim((string) $prize_winner_phone_display) : '';
        $bny_show_winner_cer1 = !empty($prize_winner_show_celebration);
        $bny_is_winner_view = $bny_show_winner_cer1;
        $bny_gift_pic = isset($prize_gift_pic_url) ? trim((string) $prize_gift_pic_url) : '';
        $bny_gift_detail = isset($prize_gift_detail) ? trim((string) $prize_gift_detail) : '';
        ?>
        <?php if (!$bny_is_winner_view) { ?>
        <p class="bny-prize-winner-label">ผู้โชคดีคือ</p>
        <?php } ?>
        <p class="bny-prize-winner-phone"><?php
          echo $pwd !== '' ? htmlspecialchars($pwd, ENT_QUOTES, 'UTF-8') : '-';
          if ($bny_show_winner_cer1) {
            ?><img class="bny-prize-winner-cer1" src="<?php echo base_url('resources/images/cer1.gif'); ?>" width="48" height="48" alt="" loading="lazy"><?php
          }
        ?></p>
        <?php if ($bny_is_winner_view) { ?>
          <?php if ($bny_gift_pic !== '' || $bny_gift_detail !== '') { ?>
        <div class="bny-prize-gift-wrap">
          <?php if ($bny_gift_pic !== '') { ?>
          <img class="bny-prize-gift-img" src=" <?php echo htmlspecialchars($bny_gift_pic, ENT_QUOTES, 'UTF-8'); ?>" alt="รางวัล" loading="lazy">
          <?php } ?>
          <?php if ($bny_gift_detail !== '') { ?>
          <p class="bny-prize-gift-detail"><?php echo nl2br(htmlspecialchars($bny_gift_detail, ENT_QUOTES, 'UTF-8')); ?></p>
          <?php } ?>
        </div>
          <?php } ?>
        <p class="bny-prize-delivery-note">Admin จะจัดส่งรางวัลให้กับท่านในการสั่งครั้งถัดไป</p>
        <?php } ?>
      </div>
    </div>

    <div class="bny-result-archive-wrap">
      <a class="bny-result-home-btn" href="<?php echo base_url('bnyreward/bny_archive'); ?>">ตรวจรางวัลย้อนหลัง</a>
    </div>

    <div class="bny-result-home-wrap">
      <a class="bny-result-home-btn" href="<?php echo base_url('bnyreward/bny_luckyresult'); ?>">กลับหน้าหลัก</a>
    </div>

    <div id="bnyWheelOverlay" class="bny-wheel-overlay" role="status" aria-live="polite" aria-busy="true">
      <p class="bny-wheel-title">กำลังหมุนออกรางวัล</p>
      <div class="bny-wheel-disc-wrap">
        <div class="bny-wheel-pointer" aria-hidden="true"></div>
        <div id="bnyWheelDisc" class="bny-wheel-disc"></div>
        <div class="bny-wheel-hub" aria-hidden="true">🏆</div>
      </div>
    </div>
  </div>
</div>

<noscript>
  <style>
    #bnyWheelOverlay { display: none !important; }
  </style>
</noscript>
<script>
(function () {
  var page = document.getElementById('bnyResultPage');
  var overlay = document.getElementById('bnyWheelOverlay');
  var disc = document.getElementById('bnyWheelDisc');
  if (!overlay || !disc) return;

  var loginId = page ? page.getAttribute('data-bny-login-id') : '0';
  var rewardId = page ? page.getAttribute('data-bny-reward-id') : '0';
  var storageKey = 'bnyResultWheelSeen_v1_' + loginId + '_' + rewardId;

  function markWheelSeenForThisRound() {
    if (!rewardId || rewardId === '0') return;
    try {
      if (window.localStorage) {
        localStorage.setItem(storageKey, '1');
      }
    } catch (e) {}
  }

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function finishReveal() {
    overlay.classList.add('bny-wheel-overlay--done');
    overlay.setAttribute('aria-busy', 'false');
    markWheelSeenForThisRound();
    window.setTimeout(function () {
      overlay.style.display = 'none';
      overlay.setAttribute('aria-hidden', 'true');
    }, 600);
  }

  function skipWheelImmediately() {
    overlay.style.display = 'none';
    overlay.setAttribute('aria-busy', 'false');
    overlay.setAttribute('aria-hidden', 'true');
    overlay.classList.add('bny-wheel-overlay--done');
  }

  var alreadySeen = false;
  try {
    alreadySeen = !!(window.localStorage && rewardId && rewardId !== '0' && localStorage.getItem(storageKey) === '1');
  } catch (e) {}

  if (reduced || alreadySeen) {
    if (reduced && !alreadySeen) {
      markWheelSeenForThisRound();
    }
    skipWheelImmediately();
    return;
  }

  disc.classList.add('bny-wheel-disc--spin');
  window.setTimeout(finishReveal, 5000);
})();
</script>
