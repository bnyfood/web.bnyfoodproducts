<div class='dashboard-content bny-doc-host' data-bny-doc-host="1" data-make-url="<?php echo base_url();?>accounting/saletaxreport/saletaxreport_make/">
  <div class="bny-doc-toolbar">
    <h4 class="bny-doc-title">พิมพ์รายงานภาษีขาย</h4>
    <form class="bny-doc-search" role="form" name="product_search" id="product_search" action="#" method="post">
      <select name="platform" id="platform" class="form-control">
        <option value="0" <?php if($arr_search['platform'] == 0){echo "selected";}?>>Lazada</option>
        <option value="1" <?php if($arr_search['platform'] == 1){echo "selected";}?>>Shopee</option>
        <option value="2" <?php if($arr_search['platform'] == 2){echo "selected";}?>>Tiktok</option>
        <option value="3" <?php if($arr_search['platform'] == 3){echo "selected";}?>>BigSauces</option>
      </select>
      <div class="input-group bny-doc-dates">
        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo htmlspecialchars((string)$arr_search['daterange'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="input-group-append">
          <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
        </div>
      </div>
      <input type="button" class="btn btn-primary" value="Search" id="search" data-bny-doc-search="1">
    </form>
  </div>
  <div class="bny-doc-tabs" id="bny_doc_tabs"></div>
  <div class="bny-doc-stage" id="bny_doc_stage">
    <div class="bny-doc-empty">กด Search เพื่อเปิดรายงานในหน้านี้ (ไม่เปิดหน้าต่างใหม่)</div>
    <div class="bny-doc-loading" id="bny_doc_loading">กำลังโหลดรายงาน…</div>
  </div>
</div>
