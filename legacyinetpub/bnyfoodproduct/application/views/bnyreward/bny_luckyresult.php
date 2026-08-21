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
  .bny-result-body {
    font-size: 15px;
    color: #333;
    line-height: 1.65;
    text-align: center;
  }
  .bny-result-body .bny-result-title {
    margin: 0 0 12px;
    font-size: 22px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.35;
    letter-spacing: -0.02em;
  }
  .bny-result-body .bny-week-range {
    margin: 0 0 24px;
    padding: 12px 14px;
    background: #f7f7f7;
    border-radius: 12px;
    border: 1px solid #ececec;
    font-weight: 600;
    color: #1f1f1f;
    text-align: center;
  }
  .bny-result-body .bny-points-block {
    margin: 0;
  }
  .bny-result-body .bny-points-label {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.3;
  }
  .bny-result-body .bny-points-value {
    margin: 0;
    font-size: 48px;
    font-weight: 800;
    color: #c62828;
    line-height: 1.1;
    letter-spacing: -0.03em;
  }
  .bny-check-prize-wrap {
    margin-top: 22px;
    display: flex;
    justify-content: center;
  }
  .bny-check-prize-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    width: 100%;
    max-width: 100%;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #c9a227;
    background: linear-gradient(180deg, #fffdf5 0%, #fff3c4 100%);
    color: #5c4700;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.22);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
  }
  .bny-check-prize-btn .bny-check-prize-label {
    font-size: 26px;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: #4a3a00;
  }
  .bny-check-prize-btn:hover {
    text-decoration: none;
    color: #3d2f00;
    border-color: #a88620;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201, 162, 39, 0.28);
  }
  .bny-check-prize-btn .bny-check-prize-icon-img {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    object-fit: contain;
    display: block;
    filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.14));
  }
  @media (max-width: 576px) {
    .bny-check-prize-btn .bny-check-prize-icon-img {
      width: 40px;
      height: 40px;
    }
    .bny-check-prize-btn .bny-check-prize-label {
      font-size: 22px;
    }
    .bny-result-user {
      text-align: left;
      width: 100%;
    }
    .bny-result-header {
      flex-direction: column;
    }
  }
</style>

<div class="social-login-page">
  <div class="social-login-card">
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

    <div class="bny-result-body">
      <p class="bny-result-title">แต้มสะสมของท่าน ประจำวันที่</p>
      <p class="bny-week-range"><?php echo !empty($week_range_label) ? htmlspecialchars($week_range_label, ENT_QUOTES, 'UTF-8') : ''; ?></p>
      <div class="bny-points-block">
        <p class="bny-points-label">แต้มของคุณคือ</p>
        <p class="bny-points-value"><?php echo isset($points_display) ? (int)$points_display : 0; ?></p>
        <div style="margin-top:16px;font-size:14px;color:#c62828;line-height:1.7;text-align:center;">
          <p style="margin:0;font-weight:700;">สะสมแต้มชิงรางวัลรายสัปดาห์</p>
          <p style="margin:4px 0 0;">ช้อปครบ 100 บาท ได้รับ 1 แต้ม</p>
          <p style="margin:4px 0 0;">ประกาศผลรางวัลทุกวันจันทร์เวลา 10:00</p>
        </div>
        <div class="bny-check-prize-wrap">
          <a class="bny-check-prize-btn" href="<?php echo base_url('bnyreward/bny_result'); ?>" aria-label="ตรวจผลรางวัล">
            <img
              class="bny-check-prize-icon-img"
              src="<?php echo base_url('resources/images/reward1.gif'); ?>"
              width="48"
              height="48"
              alt=""
              decoding="async">
            <span class="bny-check-prize-label">ตรวจผลรางวัล</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
