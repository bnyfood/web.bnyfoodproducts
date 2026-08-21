<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>

    <h1 class="social-login-title">เปลี่ยนเบอร์โทร</h1>

    <?php if (!empty($error_msg)) { ?>
      <p style="color:#c00;text-align:center;margin:0 0 12px;"><?php echo $error_msg; ?></p>
    <?php } ?>

    <form id="bny_changephone_form" action="<?php echo base_url('bnyreward/submit_bny_changephone'); ?>" method="post" autocomplete="off">
      <input type="hidden" id="current_phone" name="current_phone" value="<?php echo !empty($current_phone) ? htmlspecialchars($current_phone, ENT_QUOTES, 'UTF-8') : ''; ?>">
      <input
        type="text"
        id="web_user_phone_old"
        name="web_user_phone_old"
        class="form-control"
        placeholder="เบอร์โทรเก่า"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="10"
        value=""
        oninput="this.value=this.value.replace(/[^0-9]/g,'');"
        required
        style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">

      <input
        type="text"
        id="web_user_phone_new"
        name="web_user_phone_new"
        class="form-control"
        placeholder="เบอร์โทรใหม่"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="10"
        value=""
        oninput="this.value=this.value.replace(/[^0-9]/g,'');"
        required
        style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">

      <button type="submit" class="btn-social-custom btn-facebook" style="margin-bottom:8px;">
        บันทึกเบอร์ใหม่
      </button>

      <a href="<?php echo base_url('bnyreward/bny_luckyresult'); ?>" class="btn-social-custom btn-google" style="text-align:center;text-decoration:none;">
        กลับหน้าหลัก
      </a>
    </form>
  </div>
</div>
