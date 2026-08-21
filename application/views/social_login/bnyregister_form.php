<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>

    <h1 class="social-login-title">Register</h1>


    <?php if (!empty($error_msg)) { ?>
      <p style="color:#c00;text-align:center;margin:0 0 12px;"><?php echo $error_msg; ?></p>
    <?php } ?>

    <form id="bnyregister_form" action="<?php echo base_url('social_login/submit_bnyregister_form'); ?>" method="post" autocomplete="off">
      <input
        type="text"
        id="web_user_name"
        name="web_user_name"
        class="form-control"
        placeholder="ชื่อผู้ใช้"
        maxlength="255"
        value="<?php echo !empty($web_user_name) ? $web_user_name : ''; ?>"
        required
        style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">

      <input
        type="text"
        id="web_user_phone"
        name="web_user_phone"
        class="form-control"
        placeholder="เบอร์โทรศัพท์"
        inputmode="numeric"
        pattern="[0-9]*"
        maxlength="10"
        value="<?php echo !empty($web_user_phone) ? $web_user_phone : ''; ?>"
        oninput="this.value=this.value.replace(/[^0-9]/g,'');"
        required
        style="height:48px;font-size:16px;border-radius:12px;margin-bottom:12px;">

      <button type="submit" class="btn-social-custom btn-facebook" style="margin-bottom:0;">
        Submit
      </button>
    </form>
  </div>
</div>
