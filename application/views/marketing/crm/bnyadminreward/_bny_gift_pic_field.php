<?php
$existing_pic_url = !empty($existing_pic_url) ? $existing_pic_url : '';
$existing_pic_name = !empty($existing_pic_name) ? $existing_pic_name : 'รูปปัจจุบัน';
$has_existing = ($existing_pic_url !== '');
?>
<div class="bny-gift-pic-upload">
  <div class="panel panel-bordered" style="margin-bottom:0;border:1px dashed #e4eaec;background:#fafafa;">
    <div class="panel-body" style="padding:15px;">
      <div class="row">
        <div class="col-sm-4 col-md-3 text-center">
          <div id="bny_gift_pic_preview_box" class="thumbnail" style="margin:0 auto 10px;max-width:180px;<?php echo $has_existing ? '' : 'display:none;'; ?>">
            <img id="bny_gift_pic_preview"
              src="<?php echo $has_existing ? htmlspecialchars($existing_pic_url, ENT_QUOTES, 'UTF-8') : ''; ?>"
              alt="ตัวอย่างรูป"
              data-existing-src="<?php echo $has_existing ? htmlspecialchars($existing_pic_url, ENT_QUOTES, 'UTF-8') : ''; ?>"
              data-existing-name="<?php echo htmlspecialchars($existing_pic_name, ENT_QUOTES, 'UTF-8'); ?>"
              style="max-height:140px;max-width:100%;object-fit:contain;">
          </div>
          <div id="bny_gift_pic_placeholder" class="text-muted" style="padding:20px 10px;<?php echo $has_existing ? 'display:none;' : ''; ?>">
            <i class="icon wb-image" style="font-size:42px;opacity:.35;"></i>
            <div style="margin-top:8px;font-size:13px;">ยังไม่มีรูป</div>
          </div>
        </div>
        <div class="col-sm-8 col-md-9">
          <label class="btn btn-primary btn-file" style="margin-bottom:8px;">
            <i class="icon wb-upload" aria-hidden="true"></i> เลือกรูป
            <input type="file" name="web_bny_gift_pic" id="web_bny_gift_pic" accept="image/*" style="display:none;">
          </label>
          <p id="bny_gift_pic_filename" class="help-block" style="margin-bottom:0;">
            <?php echo $has_existing ? htmlspecialchars($existing_pic_name, ENT_QUOTES, 'UTF-8') : 'รองรับ JPG, PNG, GIF'; ?>
          </p>
          <?php if (!empty($show_keep_hint)) { ?>
            <p class="text-muted" style="margin-top:6px;margin-bottom:0;font-size:12px;">เว้นว่างหากไม่เปลี่ยนรูป</p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
