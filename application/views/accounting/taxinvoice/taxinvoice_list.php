<?php
$tax_type = isset($arr_search['taxinvoicetype']) && $arr_search['taxinvoicetype'] !== '' ? (string)$arr_search['taxinvoicetype'] : '1';
$void_type = isset($arr_search['voidtype']) && $arr_search['voidtype'] !== '' ? (string)$arr_search['voidtype'] : '2';
?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
  .taxinvoice-host .taxinvoice-results {
    padding: 8px 12px 16px;
  }
  .taxinvoice-host .taxinvoice-results .table thead th {
    vertical-align: middle;
    white-space: nowrap;
  }
  .taxinvoice-host .bny-doc-search .filter-block {
    width: 100%;
    margin-bottom: 0.35em;
  }
  .taxinvoice-host .bny-doc-search .filter-block hr {
    margin: 0.4em 0 0.5em;
  }
</style>
<div class="dashboard-content bny-doc-host taxinvoice-host" data-bny-doc-host="1" data-make-url="<?php echo base_url();?>accounting/taxinvoice/shot_invoice/" data-make-path="{platform}/{ordernumber}/{search_type}/{voidtype}/original/{daterange}">
  <div class="bny-doc-toolbar">
    <h4 class="bny-doc-title">ค้นหาใบกำกับภาษี</h4>
    <form class="bny-doc-search" role="form" name="taxinvoice_search_form" id="taxinvoice_search_form" action="<?php echo base_url()."accounting/taxinvoice/taxinvoice_search";?>" method="post">
      <span class="bny-doc-radios filter-block">
        TaxinvoiceType:
        <label><input type="radio" name="taxinvoicetype" value="1" <?php if($tax_type === "1"){echo "checked";}?>> All type</label>
        <label><input type="radio" name="taxinvoicetype" value="2" <?php if($tax_type === "2"){echo "checked";}?>> ABB</label>
        <label><input type="radio" name="taxinvoicetype" value="3" <?php if($tax_type === "3"){echo "checked";}?>> Full Taxinvoice</label>
      </span>
      <span class="bny-doc-radios filter-block">
        Void:
        <label><input type="radio" name="voidtype" value="2" <?php if($void_type === "2"){echo "checked";}?>> All</label>
        <label><input type="radio" name="voidtype" value="0" <?php if($void_type === "0"){echo "checked";}?>> No Voice</label>
        <label><input type="radio" name="voidtype" value="1" <?php if($void_type === "1"){echo "checked";}?>> Voice</label>
      </span>
      <select name="platform" id="platform" class="form-control">
        <option value="0" <?php if($arr_search['platform'] == "0"){echo "selected";}?>>Lazada</option>
        <option value="1" <?php if($arr_search['platform'] == "1"){echo "selected";}?>>Shopee</option>
        <option value="2" <?php if($arr_search['platform'] == "2"){echo "selected";}?>>Tiktok</option>
        <option value="3" <?php if($arr_search['platform'] == "3"){echo "selected";}?>>Biggrill</option>
        <option value="4" <?php if($arr_search['platform'] == "4"){echo "selected";}?>>BigSauces</option>
      </select>
      <select name="search_type" id="search_type" class="form-control" onchange="set_search(this);">
        <option value="0" <?php if($arr_search['search_type'] == "0"){echo "selected";}?>>Select one</option>
        <option value="1" <?php if($arr_search['search_type'] == "1" || $arr_search['search_type'] === ""){echo "selected";}?>>Search by DateRange</option>
        <option value="2" <?php if($arr_search['search_type'] == "2"){echo "selected";}?>>Search by OrderNumber</option>
      </select>
      <div class="bny-doc-order" style="display: none" id="order_search">
        <input type="text" name="order_number" placeholder="Order Number" id="ordernumber" class="form-control" value="<?php echo htmlspecialchars((string)$arr_search['ordernumber'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>
      <div class="input-group bny-doc-dates" style="display: none" id="date_search">
        <input type="text" class="form-control" name="daterange" id="daterange" value="<?php echo htmlspecialchars((string)$arr_search['daterange'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="input-group-append">
          <span class="input-group-text"><i class="input-search-icon wb-calendar" aria-hidden="true"></i></span>
        </div>
      </div>
      <span id="button_search1" style="display: none">
        <input type="button" class="btn btn-primary" value="พิมพ์ใบกำกับภาษีอย่างย่อ" id="search" data-bny-doc-search="1">
      </span>
      <span id="button_search2" style="display: none">
        <button type="submit" id="but_invoice" class="btn btn-primary">ออกใบกำกับภาษีเต็มรูป</button>
      </span>
    </form>
  </div>
  <div class="bny-doc-tabs" id="bny_doc_tabs"></div>
  <div class="bny-doc-stage" id="bny_doc_stage">
    <div class="bny-doc-empty">เลือกช่วงวันที่ แล้วกดพิมพ์ใบกำกับภาษีอย่างย่อ เพื่อเปิดเอกสารในแท็บนี้</div>
    <div class="bny-doc-loading" id="bny_doc_loading">กำลังโหลดเอกสาร…</div>
  </div>
  <div class="taxinvoice-results bny-list-print-hide">
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>LazOrderNumber</th>
            <th>TaxInvoice(ABB)</th>
            <th>TaxInvoice</th>
            <th>OrderDate</th>
            <th>Shipping Fee</th>
            <th>Voucher</th>
            <th>Amount</th>
            <th>WantTaxinvoice</th>
            <th>Status</th>
            <th class="text-nowrap">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
              if(!empty($arr_orders)){
                $row_runer=1;
              foreach($arr_orders as $arr_order){
          ?>
            <tr>
              <td><?php echo $row_runer;?></td>
              <td><?php echo $arr_order['order_number']?></td>
              <td><?php echo $arr_order['taxinvoiceID']?></td>
              <td>
                <?php
                 if(!empty($arr_order["FullTaxinvoiceID"])){echo $arr_order["FullTaxinvoiceID"];}else{echo "-";}
                ?>
              </td>
              <td><?php $datearr=explode(" ",$arr_order["created_at"]); echo $datearr[0];?></td>
              <td><?php echo $arr_order['shipping_fee']?></td>
              <td><?php echo $arr_order['voucher']?></td>
              <td><?php echo $arr_order['price']?></td>
              <td><?php echo $arr_order['want_taxinvoice']?></td>
              <td><?php echo $arr_order['status']?></td>
              <td class="text-nowrap">
                <a href="<?php echo base_url();?>accounting/taxinvoice/textinvoice_addform/<?php echo $arr_order["order_number"];?>/<?php echo $arr_search['platform'];?>" id="fancy_main" class="btn btn-outline btn-primary">ออกใบกำกับเต็มรูป</a>
                <a href="javascript:void(0)" class="btn btn-outline btn-primary" onclick="window.open('<?php echo base_url();?>accounting/taxinvoice/taxinvoice_print/<?php echo $arr_order["order_number"];?>', '_blank', 'location=yes,height=1000,width=1200,scrollbars=yes,status=yes');">พิมพ์ใบ</a>
              </td>
            </tr>
          <?php $row_runer+=1;}}?>
        </tbody>
      </table>
    </div>
  </div>
</div>
