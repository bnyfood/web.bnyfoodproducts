<style>
  .bny-checkprize-main-title {
    font-size: 42px;
    line-height: 1.12;
    margin-bottom: 22px;
  }
  @media (max-width: 576px) {
    .bny-checkprize-main-title {
      font-size: 34px;
    }
  }
  .bny-checkprize-page {
    text-align: center;
    padding: 8px 0 4px;
  }
  .bny-checkprize-page p {
    margin: 0 0 16px;
    font-size: 15px;
    color: #555;
    line-height: 1.55;
  }
  .bny-checkprize-back {
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
  }
  .bny-checkprize-back:hover {
    text-decoration: none;
    border-color: #bdbdbd;
    color: #1f1f1f;
  }
</style>

<div class="social-login-page">
  <div class="social-login-card">
    <div class="logo-wrap">
      <?php if (!empty($logo_url)) { ?>
        <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
      <?php } else { ?>
        <span class="logo-fallback">BNY LOGO</span>
      <?php } ?>
    </div>
    <h1 class="social-login-title bny-checkprize-main-title">ตรวจผลรางวัล</h1>
    <div class="bny-checkprize-page">
      <p>ระบบตรวจผลรางวัลกำลังเตรียมใช้งาน<br>โปรดกลับมาใหม่ในขั้นตอนถัดไป</p>
      <a class="bny-checkprize-back" href="<?php echo !empty($back_url) ? htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') : base_url('bnyreward/bny_luckyresult'); ?>">กลับหน้าแต้มสะสม</a>
    </div>
  </div>
</div>
