<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
$base = base_url();
?>
<div class="ai-wrap">
  <div class="ai-nav">
    <a href="<?php echo $base; ?>ai/inbox"><?php echo htmlspecialchars($t('แชท', 'Chat'), ENT_QUOTES, 'UTF-8'); ?></a>
    <a href="<?php echo $base; ?>ai/playbook"><?php echo htmlspecialchars($t('คู่มือการตอบ', 'Playbook'), ENT_QUOTES, 'UTF-8'); ?></a>
    <a href="<?php echo $base; ?>ai/settings"><?php echo htmlspecialchars($t('ระบบแชท', 'Chat AI'), ENT_QUOTES, 'UTF-8'); ?></a>
  </div>
  <h3><?php echo htmlspecialchars($t('โมเดลภาษาสำหรับแชท', 'Language model for chat'), ENT_QUOTES, 'UTF-8'); ?></h3>
  <?php if (!empty($flash)) { ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($t('บันทึกแล้ว', 'Saved'), ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <p><?php echo htmlspecialchars($t('เลือกโมเดลที่ต้องการผลลัพธ์ดี ไม่จำเป็นต้องรุ่นฟรี คู่มือการตอบจะถูกสร้างจากข้อความที่คนส่งจริง', 'Pick the model you want for quality. Free tier is not required. The reply playbook is built from messages humans actually send.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <p><?php echo htmlspecialchars($t('กล่องปรึกษา AI ใช้คีย์นี้เรียกโมเดล ข้อมูลร้านยังดึงจาก SQL Server ของเรา ไอคอน Gemini ในช่องพิมพ์เป็นส่วนขยายของเบราว์เซอร์ ไม่ใช่คีย์ของร้าน', 'The private AI box uses this key to call the model. Shop data still comes from our SQL Server. A Gemini icon in the text box is a browser extension, not the shop key.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <?php if (empty($key_hint)) { ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($t('ยังไม่มี API key — ใส่แล้วกดบันทึก แล้วค่อยกลับไปปรึกษา AI ในห้องแชท', 'No API key yet. Paste it, save, then go back to the chat thread.'), ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>
  <form method="post" action="<?php echo $base; ?>ai/save_settings" style="max-width:640px;">
    <div class="form-group">
      <label><?php echo htmlspecialchars($t('ผู้ให้บริการ', 'Provider'), ENT_QUOTES, 'UTF-8'); ?></label>
      <select name="provider" id="ai_provider" class="form-control">
        <?php
        $p = isset($settings['provider']) ? $settings['provider'] : 'openai';
        foreach (array('openai' => 'OpenAI', 'gemini' => 'Gemini', 'anthropic' => 'Anthropic') as $val => $lab) {
          $sel = ($p === $val) ? ' selected' : '';
          echo '<option value="'.htmlspecialchars($val, ENT_QUOTES, 'UTF-8').'"'.$sel.'>'.htmlspecialchars($lab, ENT_QUOTES, 'UTF-8').'</option>';
        }
        ?>
      </select>
    </div>
    <div class="form-group">
      <label><?php echo htmlspecialchars($t('ชื่อโมเดล', 'Model name'), ENT_QUOTES, 'UTF-8'); ?></label>
      <input type="text" class="form-control" name="model_name" id="ai_model_name" value="<?php echo htmlspecialchars(isset($settings['model_name']) ? $settings['model_name'] : '', ENT_QUOTES, 'UTF-8'); ?>">
      <small><?php echo htmlspecialchars($t('Gemini ใช้ gemini-2.0-flash · OpenAI ใช้ gpt-4o-mini · Anthropic ใช้ claude-sonnet-4-5', 'Gemini: gemini-2.0-flash. OpenAI: gpt-4o-mini. Anthropic: claude-sonnet-4-5.'), ENT_QUOTES, 'UTF-8'); ?></small>
    </div>
    <div class="form-group">
      <label>API key<?php if (!empty($key_hint)) { echo ' ('.$key_hint.')'; } ?></label>
      <input type="password" class="form-control" name="api_key" value="" placeholder="<?php echo htmlspecialchars($t('วางคีย์แล้วกดบันทึก', 'Paste the key, then save'), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
      <small>
        <?php echo htmlspecialchars($t('คุณเลือก Gemini — ออกคีย์ที่ Google AI Studio แล้ววางที่นี่ อย่าส่งคีย์มาในแชท', 'You picked Gemini — create a key in Google AI Studio and paste it here. Do not send the key in chat.'), ENT_QUOTES, 'UTF-8'); ?>
        · <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">Google AI Studio</a>
        · <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">OpenAI</a>
        · <a href="https://console.anthropic.com/settings/keys" target="_blank" rel="noopener">Anthropic</a>
      </small>
    </div>
    <script>
    (function () {
      var models = { openai: "gpt-4o-mini", gemini: "gemini-2.0-flash", anthropic: "claude-sonnet-4-5" };
      var sel = document.getElementById("ai_provider");
      var input = document.getElementById("ai_model_name");
      if (!sel || !input) { return; }
      sel.addEventListener("change", function () {
        var next = models[sel.value];
        if (next) { input.value = next; }
      });
    })();
    </script>
    <div class="form-group">
      <label><input type="checkbox" name="observe_chat" value="1" <?php echo !empty($settings['observe_chat']) ? 'checked' : ''; ?>>
        <?php echo htmlspecialchars($t('ดูลักษณะการตอบของคนแล้วบันทึกเป็นตัวอย่างเรียนรู้', 'Watch human replies and store them as learning examples'), ENT_QUOTES, 'UTF-8'); ?></label>
    </div>
    <div class="form-group">
      <label><input type="checkbox" name="auto_distill" value="1" <?php echo !empty($settings['auto_distill']) ? 'checked' : ''; ?>>
        <?php echo htmlspecialchars($t('หลังคนส่งข้อความ ให้อัปเดตคู่มือการตอบทันที', 'After a human send, refresh the playbook immediately'), ENT_QUOTES, 'UTF-8'); ?></label>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo htmlspecialchars($t('บันทึก', 'Save'), ENT_QUOTES, 'UTF-8'); ?></button>
  </form>

  <h3 style="margin-top:28px;"><?php echo htmlspecialchars($t('สิทธิ์แชทแต่ละแพลตฟอร์ม', 'Chat access per platform'), ENT_QUOTES, 'UTF-8'); ?></h3>
  <p><?php echo htmlspecialchars($t('โทเค็นออเดอร์ที่มีอยู่ดึงสินค้าและออเดอร์ได้แล้ว แต่แชทต้องเปิดสิทธิ์ Chat/IM/Customer Service แยก แล้วให้ร้านอนุญาตแอปอีกครั้ง', 'Order tokens already pull products and orders. Chat needs a separate Chat/IM/Customer Service permission, then the shop must authorize the app again.'), ENT_QUOTES, 'UTF-8'); ?></p>
  <ol class="ai-access">
    <li>
      <strong>Shopee</strong> —
      <?php echo htmlspecialchars($t('แอป Seller In-House ของเราเรียก Chat API ได้แล้ว หน้ารายการแชทรีเฟรชอัตโนมัติ', 'Our Seller In-House app can already call Chat API. The chat list refreshes automatically.'), ENT_QUOTES, 'UTF-8'); ?>
      <div class="ai-tool-note"><a href="https://open.shopee.com/myconsole/management/app" target="_blank" rel="noopener">open.shopee.com</a></div>
    </li>
    <li>
      <strong>Lazada</strong> —
      <?php echo htmlspecialchars($t('เข้า open.lazada.com → App Console → แอปเดิม (client_id 123793) → API Permission Group → หา IM / Chat แล้วกด Apply ถ้าหมวดแอปไม่มีกลุ่ม IM ให้สมัครหมวด In-house IM Chat รออนุมัติ แล้วยืนยันร้านอีกครั้งจากลิงก์ต่ออายุโทเค็นที่แดชบอร์ด', 'open.lazada.com → App Console → current app (client_id 123793) → API Permission Group → apply for IM / Chat. If that group is missing, apply for the In-house IM Chat category, wait for approval, then re-authorize from the dashboard token link.'), ENT_QUOTES, 'UTF-8'); ?>
      <div class="ai-tool-note">
        <a href="https://open.lazada.com" target="_blank" rel="noopener">open.lazada.com</a>
        ·
        <a href="https://auth.lazada.com/oauth/authorize?response_type=code&amp;redirect_uri=https://www.bnyfoodproducts.com/lazcallback&amp;force_auth=true&amp;client_id=123793" target="_blank" rel="noopener"><?php echo htmlspecialchars($t('ต่ออายุโทเค็น Lazada', 'Renew Lazada token'), ENT_QUOTES, 'UTF-8'); ?></a>
      </div>
    </li>
    <li>
      <strong>TikTok</strong> —
      <?php echo htmlspecialchars($t('เข้า Partner Center → แอปเดิม → เปิดสโคป seller.customer_service (Customer Service) รออนุมัติถ้าติ๊กต็อกขอดู แล้วให้ร้านกดอนุญาตแอปใหม่ (รีเฟรชโทเค็นอย่างเดียวไม่พอ)', 'Partner Center → current app → enable seller.customer_service (Customer Service). Wait if TikTok must approve. Then the shop must authorize the app again (refreshing the token is not enough).'), ENT_QUOTES, 'UTF-8'); ?>
      <div class="ai-tool-note">
        <a href="https://partner.tiktokshop.com" target="_blank" rel="noopener">partner.tiktokshop.com</a>
        ·
        <a href="https://services.tiktokshop.com/open/authorize?service_id=7389572888133519109" target="_blank" rel="noopener"><?php echo htmlspecialchars($t('อนุญาตแอป TikTok อีกครั้ง', 'Re-authorize TikTok app'), ENT_QUOTES, 'UTF-8'); ?></a>
      </div>
    </li>
  </ol>
</div>
