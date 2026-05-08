<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>

    <h1 class="social-login-title">ยืนยัน OTP</h1>
    <p class="social-login-subtitle" style="margin-bottom:12px;">
      เบอร์ใหม่ <?php echo !empty($new_phone) ? htmlspecialchars($new_phone, ENT_QUOTES, 'UTF-8') : '-'; ?>
    </p>

    <?php if (!empty($error_msg)) { ?>
      <p style="color:#c00;text-align:center;margin:0 0 12px;"><?php echo $error_msg; ?></p>
    <?php } ?>

    <form id="bny_changephoneotp_form" action="<?php echo base_url('bnyreward/submit_bny_changephoneotp'); ?>" method="post" autocomplete="off">
      <input type="hidden" id="validate_otp_url" value="<?php echo !empty($validate_otp_url) ? htmlspecialchars($validate_otp_url, ENT_QUOTES, 'UTF-8') : ''; ?>">
      <input
        type="text"
        id="key_otp"
        name="key_otp"
        class="form-control"
        placeholder="กรอก OTP 6 หลัก"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="6"
        value=""
        oninput="this.value=this.value.replace(/[^0-9]/g,'');"
        required
        style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">

      <button type="submit" class="btn-social-custom btn-facebook" style="margin-bottom:8px;">
        ยืนยัน
      </button>
    </form>

    <a href="<?php echo base_url('bnyreward/resend_changephone_otp'); ?>" class="btn-social-custom btn-google" style="display:block;text-align:center;text-decoration:none;margin-bottom:8px;">
      ส่ง OTP อีกครั้ง
    </a>
    <a href="<?php echo base_url('bnyreward/bny_changephone'); ?>" class="btn-social-custom btn-google" style="display:block;text-align:center;text-decoration:none;">
      กลับไปแก้เบอร์ใหม่
    </a>
  </div>
</div>
