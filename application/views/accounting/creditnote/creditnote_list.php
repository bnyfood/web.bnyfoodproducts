<div class='dashboard-content bny-doc-host' data-bny-doc-host="1" data-make-url="<?php echo base_url();?>accounting/creditnote/creditnote_make/" data-make-path="{platform}/{ordernumber}/{daterange}/original/{search_type}">
  <div class="bny-doc-toolbar">
    <h4 class="bny-doc-title">ค้นหาใบลดหนี้</h4>
    <form class="bny-doc-search" role="form" name="product_search" id="product_search" action="#" method="post">
      <span class="bny-doc-radios">
        TaxinvoiceType:
        <label><input type="radio" name="taxinvoicetype" value="1" checked="checked"> All type</label>
        <label><input type="radio" name="taxinvoicetype" value="2"> ABB</label>
        <label><input type="radio" name="taxinvoicetype" value="3"> Full Taxinvoice</label>
      </span>
      <select name="platform" id="platform" class="form-control">
        <option value="0" <?php if($arr_search['platform'] == "0"){echo "selected";}?>>Lazada</option>
        <option value="1" <?php if($arr_search['platform'] == "1"){echo "selected";}?>>Shopee</option>
        <option value="2" <?php if($arr_search['platform'] == "2"){echo "selected";}?>>Biggrill</option>
        <option value="3" <?php if($arr_search['platform'] == "3"){echo "selected";}?>>BigSauces</option>
      </select>
      <select name="search_type" id="search_type" class="form-control" onchange="set_search(this);">
        <option value="0" <?php if($arr_search['search_type'] == "0"){echo "selected";}?>>Select one</option>
        <option value="1" <?php if($arr_search['search_type'] == "1" || $arr_search['search_type'] === ""){echo "selected";}?>>Date Range</option>
        <option value="2" <?php if($arr_search['search_type'] == "2"){echo "selected";}?>>ABB Number</option>
        <option value="3" <?php if($arr_search['search_type'] == "3"){echo "selected";}?>>ABB Number &amp; End Date</option>
        <option value="4" <?php if($arr_search['search_type'] == "4"){echo "selected";}?>>PlatformOrderNumber</option>
      </select>
      <div class="bny-doc-order" id="order_search" style="display:none">
        <input type="text" name="order_number" placeholder="Order Number" id="ordernumber" class="form-control" value="<?php echo htmlspecialchars((string)$arr_search['ordernumber'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>
      <div class="input-group bny-doc-dates" id="date_search" style="display:none">
        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo htmlspecialchars((string)$arr_search['daterange'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="input-group-append">
          <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
        </div>
      </div>
      <span id="button_search" style="display:none">
        <input type="button" class="btn btn-primary" value="<?php echo htmlspecialchars(admin_lang() === 'en' ? 'Search' : 'ค้นหา', ENT_QUOTES, 'UTF-8'); ?>" id="search" data-bny-doc-search="1">
      </span>
    </form>
  </div>
  <div class="bny-doc-tabs" id="bny_doc_tabs"></div>
  <div class="bny-doc-stage" id="bny_doc_stage">
    <div class="bny-doc-empty"><?php echo htmlspecialchars(admin_lang() === 'en' ? 'Click Search to open the document on this page (no new window)' : 'กดค้นหาเพื่อเปิดเอกสารในหน้านี้ (ไม่เปิดหน้าต่างใหม่)', ENT_QUOTES, 'UTF-8'); ?></div>
    <div class="bny-doc-loading" id="bny_doc_loading">กำลังโหลดเอกสาร…</div>
  </div>
</div>
