<style>
  @keyframes color-change {
  0% {
    background-color: white;
  }
  100% {
    background-color: red;
  }
}
</style>
<?php
$is_en = (function_exists('admin_lang') && admin_lang() === 'en');
$t = function ($th, $en) use ($is_en) { return $is_en ? $en : $th; };
?>
<div class='dashboard-content'>
    <div class='container dash-overview'>
      <div class="dash-toolbar">
        <h3><?php echo htmlspecialchars($t('ภาพรวมยอดขายและค่าใช้จ่าย', 'Sales and expense overview'), ENT_QUOTES, 'UTF-8'); ?></h3>
        <div class="dash-range-btns">
          <button type="button" class="btn btn-default is-on" data-days="7">7 <?php echo htmlspecialchars($t('วัน', 'days'), ENT_QUOTES, 'UTF-8'); ?></button>
          <button type="button" class="btn btn-default" data-days="30">30 <?php echo htmlspecialchars($t('วัน', 'days'), ENT_QUOTES, 'UTF-8'); ?></button>
        </div>
        <label class="dash-date-label" for="dash_daterange"><?php echo htmlspecialchars($t('ช่วงวันที่', 'Date range'), ENT_QUOTES, 'UTF-8'); ?></label>
        <div class="input-group dash-dates" id="dash_date_wrap">
          <input type="text" class="form-control" name="dash_daterange" id="dash_daterange" readonly data-lang="<?php echo $is_en ? 'en' : 'th'; ?>" placeholder="<?php echo htmlspecialchars($t('เลือกช่วงวันที่', 'Select dates'), ENT_QUOTES, 'UTF-8'); ?>">
          <div class="input-group-append">
            <button type="button" class="input-group-text" id="dash_date_open" title="<?php echo htmlspecialchars($t('เลือกช่วงวันที่', 'Select dates'), ENT_QUOTES, 'UTF-8'); ?>">
              <i class="input-search-icon wb-calendar" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <button type="button" class="btn btn-primary" id="dash_search"><?php echo htmlspecialchars($t('ค้นหา', 'Search'), ENT_QUOTES, 'UTF-8'); ?></button>
      </div>
      <div class="dash-ai-links">
        <a href="<?php echo base_url(); ?>ai/inbox"><?php echo htmlspecialchars($t('แชท + เรียนรู้การตอบ', 'Chat + reply learning'), ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="<?php echo base_url(); ?>ai/playbook"><?php echo htmlspecialchars($t('คู่มือการตอบ', 'Reply playbook'), ENT_QUOTES, 'UTF-8'); ?></a>
        <a href="<?php echo base_url(); ?>ai/settings"><?php echo htmlspecialchars($t('ระบบแชท (ตั้งค่า AI)', 'Chat AI settings'), ENT_QUOTES, 'UTF-8'); ?></a>
      </div>

      <div class="dash-chart-card">
        <h4><?php echo htmlspecialchars($t('1. ยอดขายตามแพลตฟอร์ม (ซ้อนกัน) — จากออเดอร์ที่ดึงผ่าน API', '1. Stacked sales by platform — from API orders'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <div class="dash-kpis">
          <div class="dash-kpi"><span class="dash-kpi-name">Lazada</span><span class="dash-kpi-val" id="sales-laz">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">Shopee</span><span class="dash-kpi-val" id="sales-sho">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">TikTok</span><span class="dash-kpi-val" id="sales-tik">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('รวม', 'Total'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="sales-all">0.00</span></div>
        </div>
        <canvas id="dash_sales_chart" height="260"></canvas>
      </div>

      <div class="dash-chart-card">
        <h4><?php echo htmlspecialchars($t('2. เพจแอดตามแพลตฟอร์ม (เงินที่จ่ายไป)', '2. Page ads spend by platform'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <div class="dash-kpis">
          <div class="dash-kpi"><span class="dash-kpi-name">Lazada</span><span class="dash-kpi-val" id="ads-laz">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">Shopee</span><span class="dash-kpi-val" id="ads-sho">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">TikTok</span><span class="dash-kpi-val" id="ads-tik">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('รวม', 'Total'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="ads-all">0.00</span></div>
        </div>
        <canvas id="dash_ads_chart" height="260"></canvas>
        <p class="dash-note"><?php echo htmlspecialchars($t('กราฟนี้จะมีตัวเลขเมื่อส่งยอดเพจแอดรายวันเข้า webhook ด้านล่าง (แพลตฟอร์มไม่มี webhook ที่ยิงยอดโฆษณารายวันให้เอง ต้องดึง Ads Report API แล้ว POST เข้ามา)', 'This chart fills after daily page-ad spend is posted to the webhook below. Shop webhooks do not send daily ads spend — pull the Ads Report API, then POST here.'), ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="dash-chart-card">
        <h4><?php echo htmlspecialchars($t('3. ค่าธรรมเนียมตามแพลตฟอร์ม — จากข้อมูลที่ดึงผ่าน API แล้ว', '3. Platform fees — from ingested finance/escrow APIs'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <div class="dash-kpis">
          <div class="dash-kpi"><span class="dash-kpi-name">Lazada</span><span class="dash-kpi-val" id="fees-laz">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">Shopee</span><span class="dash-kpi-val" id="fees-sho">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name">TikTok</span><span class="dash-kpi-val" id="fees-tik">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('รวม', 'Total'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="fees-all">0.00</span></div>
        </div>
        <canvas id="dash_fees_chart" height="260"></canvas>
        <p class="dash-note"><?php echo htmlspecialchars($t('Lazada = Commission/Fee จาก finance (ไม่นับ Item Price และ Sponsored) · Shopee = commission + service + seller transaction จาก escrow · TikTok = small order fee จาก payment (ค่าคอมมิชชันร้านยังไม่มีในตารางนี้)', 'Lazada = commission/fee from finance (excludes Item Price and Sponsored) · Shopee = commission + service + seller transaction from escrow · TikTok = small-order fee from payment (shop commission is not in this table yet).'), ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="dash-chart-card">
        <h4><?php echo htmlspecialchars($t('4. รีเทิร์น = รายได้ ÷ ค่าใช้จ่าย (เพจแอด + ค่าธรรมเนียม) ซ้อนกับรายได้', '4. Return = revenue ÷ expense (page ads + fees), stacked with revenue'), ENT_QUOTES, 'UTF-8'); ?></h4>
        <div class="dash-kpis">
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('รายได้', 'Revenue'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="combo-sales">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('เพจแอด', 'Page ads'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="combo-ads">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('ค่าธรรมเนียม', 'Fees'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="combo-fees">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('ค่าใช้จ่าย', 'Expense'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="combo-exp">0.00</span></div>
          <div class="dash-kpi"><span class="dash-kpi-name"><?php echo htmlspecialchars($t('รีเทิร์น', 'Return'), ENT_QUOTES, 'UTF-8'); ?></span><span class="dash-kpi-val" id="combo-ret">0.00x</span></div>
        </div>
        <canvas id="dash_combo_chart" height="280"></canvas>
      </div>

      <div class="dash-token-row">
        <span class="dash-token-head"><?php echo htmlspecialchars($t('ต่ออายุโทเค็น', 'Renew token'), ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="dash-token-item" id="order_lazada">
          <img src="<?php echo base_url();?>resources/images/lazada-icon.png" alt="">
          <span class="dash-token-name">Lazada</span>
          <span id="txt_order_lazada_alert">Expire in 0 day</span>
          <a href="https://auth.lazada.com/oauth/authorize?response_type=code&amp;redirect_uri=https://www.bnyfoodproducts.com/lazcallback&amp;force_auth=true&amp;client_id=123793" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
        </span>
        <span class="dash-token-item" id="order_shopee">
          <img src="<?php echo base_url();?>resources/images/shopee-icon5.png" alt="">
          <span class="dash-token-name">Shopee</span>
          <span id="txt_order_shopee_alert">Expire in 0 day</span>
          <a href="<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
        </span>
        <span class="dash-token-item" id="order_tiktok">
          <span class="dash-token-mark dash-token-mark-tt" aria-hidden="true">TT</span>
          <span class="dash-token-name">TikTok</span>
          <span id="txt_order_tiktok_alert">Expire in 0 day</span>
          <a href="https://services.tiktokshop.com/open/authorize?service_id=7389572888133519109" target="_top"><?php echo htmlspecialchars($t('ต่ออายุ', 'Renew'), ENT_QUOTES, 'UTF-8'); ?></a>
        </span>
      </div>

      <div class="dash-chart-card">
        <div class="dash-webhook">
          <strong><?php echo htmlspecialchars($t('Webhook: เปิดทุก event ที่แพลตฟอร์มมีได้ — แต่เขาจะไม่ส่งข้อมูลทั้งก้อน', 'Webhooks: subscribe to every available event — platforms still do not dump full records'), ENT_QUOTES, 'UTF-8'); ?></strong>
          <p><?php echo htmlspecialchars($t('ติ๊ก/สมัครทุกหัวข้อใน Console ได้ ระบบจะรับและเก็บ payload ทุกครั้งที่มีการเปลี่ยนแปลง ออเดอร์/สินค้า/แพ็กเกจ ฯลฯ แต่ยอดเพจแอดรายวันและค่าธรรมเนียมทั้งก้อนไม่มีใน webhook ต้องดึง Ads/Finance API ต่อ', 'You can enable every topic in each console. We store every change payload (orders, products, packages, etc.). Daily page-ad spend and full fee totals are not in shop webhooks — those still come from Ads/Finance APIs.'), ENT_QUOTES, 'UTF-8'); ?></p>
          <div><?php echo htmlspecialchars($t('ทดสอบรับทุกแพลตฟอร์ม', 'Catch-all ping'), ENT_QUOTES, 'UTF-8'); ?>: <code><?php echo htmlspecialchars($platform_webhook_ping, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <div>Lazada URL: <code><?php echo htmlspecialchars($platform_webhook_lazada, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <div>Shopee URL: <code><?php echo htmlspecialchars($platform_webhook_shopee, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <div>TikTok URL: <code><?php echo htmlspecialchars($platform_webhook_tiktok, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <div><?php echo htmlspecialchars($t('เพจแอด (ยอดเงินรายวัน)', 'Page ads daily spend'), ENT_QUOTES, 'UTF-8'); ?>: <code><?php echo htmlspecialchars($ads_webhook_url, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <div>Header เพจแอด: <code>X-BNY-Ads-Secret: <?php echo htmlspecialchars($ads_webhook_secret, ENT_QUOTES, 'UTF-8'); ?></code></div>
          <ol>
            <li>
              <strong>Lazada</strong> —
              <?php echo htmlspecialchars($t('open.lazada.com → แอปเดิม → Message Service / Webhooks ใส่ URL Lazada ด้านบน แล้วเลือกทุก topic ที่มี (trade/order, fulfillment, product). ยอดเพจแอดยังต้องขอ Sponsored Solutions แล้วดึง report รายวันมาที่ URL เพจแอด', 'open.lazada.com → existing app → Message Service / Webhooks, paste the Lazada URL above, enable every topic (trade/order, fulfillment, product). Page-ad spend still needs Sponsored Solutions report posted to the ads URL.'), ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <li>
              <strong>Shopee</strong> —
              <?php echo htmlspecialchars($t('open.shopee.com → Console → Push Mechanism ใส่ URL Shopee ด้านบน แล้วเปิดทุก Push code (ออเดอร์, tracking, สินค้า,โปรโมชั่น, authorization). ยอด Ads ใช้ get_all_cpc_ads_daily_performance แล้ว POST ที่ URL เพจแอด ค่าธรรมเนียมใช้ escrow ที่มีอยู่', 'open.shopee.com → Console → Push Mechanism, paste the Shopee URL above, enable every push code (orders, tracking, product, promotion, authorization). Ads spend still uses get_all_cpc_ads_daily_performance posted to the ads URL. Fees stay on escrow.'), ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <li>
              <strong>TikTok</strong> —
              <?php echo htmlspecialchars($t('Shop Partner Center → Webhooks ใส่ URL TikTok ด้านบน แล้วสมัครทุก event type (order, reverse, package, product, auth). ยอดเพจแอดอยู่ที่ TikTok For Business Marketing API แล้ว POST ที่ URL เพจแอด', 'Shop Partner Center → Webhooks, paste the TikTok URL above, subscribe every event type (order, reverse, package, product, auth). Page-ad spend is TikTok For Business Marketing API, then POST to the ads URL.'), ENT_QUOTES, 'UTF-8'); ?>
            </li>
          </ol>
          <div><?php echo htmlspecialchars($t('ตัวอย่าง JSON', 'JSON example'), ENT_QUOTES, 'UTF-8'); ?>:</div>
          <pre>{
  "platform": "lazada",
  "spend_date": "2026-08-20",
  "spend": 1250.50,
  "impressions": 10000,
  "clicks": 320,
  "currency": "THB"
}</pre>
        </div>
      </div>

      <div class="row">
          <div class="col-lg-6" id="ecommerceRecentOrder">
            <div class="card card-shadow table-row">
              <div class="card-header card-header-transparent py-20">
                <div class="btn-group dropdown">
                  <a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">API LAZADA ORDER LOG</a>
                </div>
              </div>
              <div class="card-block bg-white table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Note</th>
                      <th>Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="lazada_api_list">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card card-shadow table-row">
              <div class="card-header card-header-transparent py-20">
                <div class="btn-group dropdown">
                  <a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">API LAZADA FINANCE LOG</a>
                </div>
              </div>
              <div class="card-block bg-white table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Note</th>
                      <th>Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="lazada_api_finance_list">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
    </div>
</div>

<div
      class="modal fade"
      id="ModalChangeStatus"
      tabindex="-1"
      role="dialog"
      aria-labelledby="ModalDelLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ModalDelLabel">Edit</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Are you sure to change status!!!</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close
            </button>
            <button
              id="change_status_btn"
              type="button"
              class="btn btn-primary"
              data-dismiss="modal"
            >
              Change Status
            </button>
          </div>
        </div>
      </div>
    </div>
