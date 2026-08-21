<div class='dashboard-content'>
    <div class='container'>
      <div class="row">
        <div class='card'>
          <div class='card-body'>
            <?php if($customer_type == 2){ ?>
            <form role="form" name="del_order" id="del_order" action="<?php echo base_url()."accounting/saletaxreport/del_data_platform"?>" method="post" enctype="multipart/form-data">
              <div class="panel-body">
                <h4 class="example-title">ลบข้อมูล API Platform</h4>
                <p class="text-muted">Lazada: การลบเดือน เช่น 2607 จะลบจากเดือนนั้นถึงวันนี้ ไม่ใช่เดือนเดียว</p>
                <hr>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <select name="platform_del" id="platform_del" class="form-control">
                          <option value="0">Lazada
                          <option value="1">Shopee
                          <option value="2">Tiktok
                       </select>
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group">
                      <?php
                        $startYear = 2023;
                        $currentYear = (int) date('Y');
                        $selectedYear = $currentYear;
                      ?>
                      ปี
                      <select id="del_year" class="form-control">
                        <?php for ($y = $startYear; $y <= $currentYear; $y++) {
                          $yy = substr((string) $y, -2);
                        ?>
                          <option value="<?php echo $yy; ?>" <?php echo ($y === $selectedYear) ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      เดือน
                      <select id="del_month" class="form-control">
                        <?php for ($m = 1; $m <= 12; $m++) {
                          $mm = str_pad((string) $m, 2, '0', STR_PAD_LEFT);
                        ?>
                          <option value="<?php echo $mm; ?>"><?php echo $mm; ?></option>
                        <?php } ?>
                      </select>
                      <input type="hidden" name="del_ym" id="del_ym" value="">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <div class="col-md-3 offset-md-0">
                        <input type="submit" class="btn-danger btn" value="Delete" id="del_btn" onclick="return confirm('Are you sure you want to delete?');">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
<script>
(function () {
  function syncDelYm() {
    var y = document.getElementById('del_year');
    var m = document.getElementById('del_month');
    var ym = document.getElementById('del_ym');
    if (y && m && ym) {
      ym.value = y.value + m.value;
    }
  }
  var yearEl = document.getElementById('del_year');
  var monthEl = document.getElementById('del_month');
  var formEl = document.getElementById('del_order');
  if (yearEl) yearEl.addEventListener('change', syncDelYm);
  if (monthEl) monthEl.addEventListener('change', syncDelYm);
  if (formEl) formEl.addEventListener('submit', syncDelYm);
  syncDelYm();
})();
</script>
            </form>
            <?php }else{ ?>
              <p>เมนูนี้สำหรับ Admin เท่านั้น</p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
</div>
