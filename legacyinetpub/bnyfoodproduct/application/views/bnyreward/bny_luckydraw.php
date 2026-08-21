<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>

    <h1 class="social-login-title">ยืนยันเบอร์โทร</h1>
    <p class="bny-err-msg" id="bny_msg" style="display:none;color:#c00;font-size:14px;margin:0 0 12px;text-align:center;"></p>

    <div id="step_phone">
      <form id="form_phone" action="#" method="post" autocomplete="off">
        <input
          type="text"
          id="web_user_phone"
          name="web_user_phone"
          class="form-control"
          placeholder="กรอกเบอร์โทรศัพท์"
          inputmode="numeric"
          pattern="[0-9]*"
          maxlength="10"
          required
          style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">
        <button type="submit" class="btn-social-custom btn-facebook" style="margin-bottom:0;">
          ยืนยัน
        </button>
      </form>
    </div>

    <div id="step_otp" style="display:none;">
      <p style="text-align:center;color:#555;font-size:14px;margin:0 0 12px;">รหัส OTP 6 หลัก</p>
      <form id="form_otp" action="#" method="post" autocomplete="off">
        <input
          type="text"
          id="key_otp"
          name="key_otp"
          class="form-control"
          placeholder="กรอก OTP 6 หลัก"
          inputmode="numeric"
          pattern="[0-9]*"
          maxlength="6"
          required
          style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">
        <button type="submit" class="btn-social-custom btn-facebook" style="margin-bottom:8px;">
          ยืนยัน OTP
        </button>
        <p style="text-align:center;margin:0;">
          <a href="#" id="bny_resend_otp" style="color:#1877f2;font-size:14px;display:none;">กดเพื่อส่ง OTP อีกครั้ง</a>
        </p>
      </form>
    </div>
  </div>
</div>
<?php if (!empty($bny_luckydraw_config)) { ?>
<script>var bnyLuckyDrawConfig = <?php echo json_encode($bny_luckydraw_config, JSON_UNESCAPED_SLASHES); ?>;</script>
<?php } ?>
