<style>
  @import url('<?php echo base_url();?>global/vendor/bootstrap-datepicker/bootstrap-datepicker.css');

  .bny-archive-header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e8e8e8;
  }
  .bny-archive-logo img {
    width: 88px;
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    border: 1px solid #d9d9d9;
    background: #fff;
    object-fit: contain;
    display: block;
  }
  .bny-archive-user {
    text-align: right;
    flex: 1;
    min-width: 160px;
    font-size: 14px;
    color: #333;
    line-height: 1.5;
  }
  .bny-archive-user strong {
    display: block;
    font-size: 15px;
    color: #1f1f1f;
    margin-bottom: 4px;
  }
  .bny-archive-action-wrap {
    margin-top: 10px;
    display: inline-flex;
    gap: 8px;
    align-items: center;
  }
  .bny-archive-action-btn {
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
  .bny-archive-action-btn:hover {
    text-decoration: none;
    color: #1f1f1f;
    border-color: #8f8f8f;
  }
  .bny-archive-logout-btn {
    border-color: #d43f3a;
    color: #d43f3a;
  }
  .bny-archive-logout-btn:hover {
    color: #b52b27;
    border-color: #b52b27;
  }
  .bny-archive-title {
    margin: 0 0 12px;
    text-align: center;
    font-size: 24px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.3;
  }
  .bny-archive-form {
    margin: 0;
  }
  .bny-archive-search-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
  }
  .bny-archive-datepicker-wrap {
    margin-bottom: 0;
    flex: 1;
  }
  .bny-archive-date-input {
    width: 100%;
    height: 48px;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #1f1f1f;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    box-sizing: border-box;
    cursor: pointer;
  }
  .bny-archive-date-input:focus {
    outline: none;
    border-color: #d4a017;
    box-shadow: 0 0 0 2px rgba(212, 160, 23, 0.12);
  }
  .bny-archive-search-btn,
  .bny-archive-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    box-sizing: border-box;
  }
  .bny-archive-search-btn {
    width: 48px;
    min-width: 48px;
    height: 48px;
    padding: 0;
    border: 1px solid #d4a017;
    background: linear-gradient(180deg, #fff2b8 0%, #ffd95c 100%);
    color: #5b4300;
  }
  .bny-archive-search-btn:hover {
    text-decoration: none;
    color: #5b4300;
    opacity: 0.96;
  }
  .bny-archive-search-btn[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
  }
  .bny-archive-search-btn img {
    width: 24px;
    height: 24px;
    display: block;
    object-fit: contain;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    mix-blend-mode: multiply;
  }
  .bny-archive-empty {
    margin: 0 0 16px;
    padding: 14px 12px;
    border-radius: 12px;
    background: #fafafa;
    border: 1px solid #ececec;
    text-align: center;
    color: #666;
    font-size: 14px;
    line-height: 1.55;
  }
  .bny-archive-table-wrap {
    margin-top: 22px;
    overflow-x: auto;
    border: 1px solid #ececec;
    border-radius: 14px;
  }
  .bny-archive-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
  }
  .bny-archive-table th,
  .bny-archive-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #efefef;
    text-align: left;
    vertical-align: top;
    font-size: 14px;
    line-height: 1.5;
  }
  .bny-archive-table th {
    background: #faf7ef;
    color: #4b3a00;
    font-weight: 800;
  }
  .bny-archive-table tr:last-child td {
    border-bottom: 0;
  }
  .bny-archive-table td:last-child {
    font-weight: 700;
    color: #c62828;
  }
  .bny-archive-back-wrap {
    margin-top: 20px;
  }
  .bny-archive-back-btn {
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #1f1f1f;
  }
  .bny-archive-back-btn:hover {
    text-decoration: none;
    color: #1f1f1f;
    border-color: #bdbdbd;
  }
  .datepicker-dropdown {
    border-radius: 14px !important;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    border-color: #ead9a6 !important;
    padding: 8px;
  }
  .datepicker table tr td,
  .datepicker table tr th {
    width: 36px;
    height: 36px;
    border-radius: 10px;
  }
  .datepicker table tr td.active,
  .datepicker table tr td.active:hover,
  .datepicker table tr td.active:focus {
    background: #d4a017 !important;
    color: #fff !important;
  }
  .datepicker table tr td.bny-archive-has-period {
    background: #fff8e1;
    color: #6c4b00;
    font-weight: 700;
  }
  .datepicker table tr td.disabled,
  .datepicker table tr td.disabled:hover {
    color: #d7d7d7 !important;
    cursor: not-allowed;
    background: transparent !important;
  }
  @media (max-width: 576px) {
    .bny-archive-user {
      text-align: left;
      width: 100%;
    }
    .bny-archive-header {
      flex-direction: column;
    }
    .bny-archive-title {
      font-size: 22px;
    }
    .bny-archive-search-row {
      gap: 8px;
    }
  }
</style>

<div class="social-login-page">
  <div class="social-login-card">
    <div class="bny-archive-header">
      <div class="bny-archive-logo">
        <?php if (!empty($logo_url)) { ?>
          <img src="<?php echo $logo_url; ?>" alt="BNY Logo">
        <?php } else { ?>
          <span class="logo-fallback" style="font-size:12px;padding:6px 10px;">BNY</span>
        <?php } ?>
      </div>
      <div class="bny-archive-user">
        <strong><?php echo !empty($customer_name) ? htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8') : '-'; ?></strong>
        <span>เบอร์โทร <?php echo !empty($customer_phone) ? htmlspecialchars($customer_phone, ENT_QUOTES, 'UTF-8') : '-'; ?></span>
        <div class="bny-archive-action-wrap">
          <a class="bny-archive-action-btn" href="<?php echo base_url('bnyreward/bny_changephone'); ?>">เปลี่ยนเบอร์โทร</a>
          <a class="bny-archive-action-btn bny-archive-logout-btn" href="<?php echo base_url('bnyreward/bny_logout'); ?>">Logout</a>
        </div>
      </div>
    </div>

    <h1 class="bny-archive-title">ตรวจรางวัลย้อนหลัง</h1>

    <?php if (!empty($period_picker_items)) { ?>
      <form class="bny-archive-form" id="bnyArchiveForm" method="get" action="<?php echo base_url('bnyreward/bny_archive'); ?>">
        <input type="hidden" name="reward_id" id="reward_id" value="<?php echo !empty($selected_reward_id) ? (int) $selected_reward_id : ''; ?>">
        <div class="bny-archive-search-row">
          <div class="bny-archive-datepicker-wrap">
            <input
              type="text"
              name="selected_date"
              id="selected_date"
              class="bny-archive-date-input"
              value="<?php echo !empty($selected_date_display) ? htmlspecialchars($selected_date_display, ENT_QUOTES, 'UTF-8') : ''; ?>"
              placeholder="กดเลือกวันที่"
              autocomplete="off"
              readonly>
          </div>
          <button type="submit" class="bny-archive-search-btn" id="bnyArchiveSearchBtn" aria-label="Search">
            <img src="<?php echo base_url('resources/images/search1.gif'); ?>" alt="Search">
          </button>
        </div>
      </form>
    <?php } else { ?>
      <div class="bny-archive-empty">ยังไม่มีข้อมูลรางวัลย้อนหลัง</div>
    <?php } ?>

    <?php if (!empty($search_performed)) { ?>
      <?php if (!empty($archive_results)) { ?>
        <div class="bny-archive-table-wrap">
          <table class="bny-archive-table">
            <thead>
              <tr>
                <th>ช่วงเวลา</th>
                <th>เบอร์โทร</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($archive_results as $row) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['period_label'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($row['winner_phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="bny-archive-empty" style="margin-top:22px;">ไม่พบข้อมูลของวันที่ที่เลือก</div>
      <?php } ?>
    <?php } ?>

    <div class="bny-archive-back-wrap">
      <a class="bny-archive-back-btn" href="<?php echo !empty($back_url) ? htmlspecialchars($back_url, ENT_QUOTES, 'UTF-8') : base_url('bnyreward/bny_result'); ?>">กลับหน้าผลรางวัล</a>
    </div>
  </div>
</div>
<script>
window.bnyArchiveCalendarConfig = <?php echo json_encode(array(
    'periodItems' => !empty($period_picker_items) ? $period_picker_items : array(),
    'selectedRewardId' => !empty($selected_reward_id) ? (int) $selected_reward_id : 0,
    'selectedDate' => !empty($selected_date_display) ? $selected_date_display : '',
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
</script>
