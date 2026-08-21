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
  .bny-checkprize-gift-box {
    margin: 14px 0 18px;
    padding: 14px;
    border-radius: 14px;
    border: 1px solid #ece3da;
    background: #fff;
  }
  .bny-checkprize-gift-image-wrap {
    border-radius: 10px;
    background: #f2ebe7;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    overflow: hidden;
  }
  .bny-checkprize-gift-image-wrap img {
    max-width: 100%;
    max-height: 160px;
    object-fit: contain;
    display: block;
  }
  .bny-checkprize-gift-empty {
    color: #d39d7a;
    font-size: 42px;
    line-height: 1;
  }
  .bny-checkprize-gift-detail {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #444;
    line-height: 1.55;
    text-align: center;
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
      <p>ของรางวัลประจำสัปดาห์</p>
      <div class="bny-checkprize-gift-box">
        <div class="bny-checkprize-gift-image-wrap">
          <div id="bnyGiftPlaceholder" class="bny-checkprize-gift-empty">📦</div>
          <img id="bnyGiftImage" src="" alt="ของรางวัลล่าสุด" style="display:none;">
        </div>
        <p id="bnyGiftDetail" class="bny-checkprize-gift-detail">กำลังโหลดข้อมูลของรางวัล...</p>
      </div>
      <a class="bny-checkprize-back" href="<?php echo !empty($back_url) ? htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') : base_url('bnyreward/bny_luckyresult'); ?>">กลับหน้าแต้มสะสม</a>
    </div>
  </div>
</div>
<script>
(function () {
  var url = <?php echo json_encode(!empty($gift_lasted_url) ? $gift_lasted_url : ''); ?>;
  if (!url || typeof window.jQuery === 'undefined') return;

  var $img = $('#bnyGiftImage');
  var $placeholder = $('#bnyGiftPlaceholder');
  var $detail = $('#bnyGiftDetail');

  $.ajax({
    url: url,
    method: 'GET',
    dataType: 'json'
  }).done(function (res) {
    if (!res || res.status !== true) {
      $detail.text('ไม่พบข้อมูลของรางวัลล่าสุด');
      return;
    }

    var detail = (res.gift_detail || '').trim();
    $detail.text(detail !== '' ? detail : '-');

    var imgUrl = (res.gift_pic_url || '').trim();
    if (imgUrl !== '') {
      $img.attr('src', imgUrl).show();
      $placeholder.hide();
    }
  }).fail(function () {
    $detail.text('ไม่สามารถโหลดข้อมูลของรางวัลได้');
  });
})();
</script>
