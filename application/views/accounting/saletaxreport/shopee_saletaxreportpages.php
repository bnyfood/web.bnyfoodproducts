<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Bny Accounting</title>
    <link href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url();?>assets/css/style.css" rel="stylesheet" type="text/css" />
    <style>
      .table1 {
        margin-top: 1em;
        width: 100%;
        height: auto;
        background: #FFF;
        overflow:visible;
        page-break-before: auto;
        table-layout:fixed;
        border: 0px solid black;
        border-spacing: 0px;
        border-collapse: separate;
        font-size: 1em;
      }
      .table2 {
        width: 100%;
        height: auto;
        background: #FFF;
        overflow: visible;
        table-layout: fixed;
        border: 0px solid black;
        border-spacing: 0px;
        border-collapse: collapse;
      }
      td {
        padding: 0.15em 4px;
        font-size: 1em;
        height: auto;
        overflow: hidden;
      }
      tr { line-height: 1.2; }
      body {
        padding-top: 0.5em;
        padding-left: 12px;
        padding-right: 18px;
        padding-bottom: 1em;
      }
      .bny{
        font-size: 0.8em;
        line-height: 1.2em;
      }
      .tbhead{
        font-size: 0.8em;
        line-height: 1.2em;
      }
      .transacdetail{
        text-decoration: none !important;
        padding: 0 4px;
        font-size: 0.8em;
        text-align: right;
        white-space: nowrap;
        overflow: hidden;
      }
      .conclution{
        padding: 0.25em 4px !important;
        line-height: 1.2  !important;
        font-size: 0.85em;
        white-space: nowrap;
      }
      .table2 th,
      .table2 td {
        white-space: nowrap;
        overflow: hidden;
        vertical-align: middle;
        border-right: 1px solid #cfd4dc;
        border-bottom: 1px solid #e4e7ec;
      }
      .table2 thead tr.colhead th {
        border-bottom: 1px solid #222;
        line-height: 1.2;
      }
      .table2 td[data-col="invoice"],
      .table2 th[data-col="invoice"] {
        white-space: nowrap;
        overflow: hidden;
        line-height: 1.2;
        font-size: 0.72em;
      }
      .table2 td[data-col="invoice"] a {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        line-height: 1.2;
      }
      .table2 tbody tr.data-row.zebra-odd td {
        background-color: #f3f5f7;
      }
      .table2 tbody tr.data-row.zebra-even td {
        background-color: #fff;
      }
      .table2 tbody tr.total-row td {
        background-color: #fff;
        border-top: 1px solid #222;
        border-bottom: 1px solid #222;
      }
      .col-picker {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #f4f6f8;
        border: 1px solid #d0d5dd;
        border-radius: 6px;
        padding: 10px 14px;
        margin: 0 auto 12px auto;
        max-width: 1100px;
        text-align: left;
        font-size: 13px;
      }
      .bny-ai-debug-wrap {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px 8px;
        margin: 0 0 0 8px;
        padding: 0;
        vertical-align: middle;
      }
      .bny-ai-debug-tog {
        margin: 0;
        color: #b0b8c0;
        font-size: 11px;
        font-weight: 400;
        cursor: pointer;
        white-space: nowrap;
        user-select: none;
      }
      .bny-ai-debug-tog input {
        margin: 0 3px 0 0;
        vertical-align: -1px;
      }
      .bny-ai-debug {
        display: none;
        color: #9aa3ad;
        font-size: 11px;
        line-height: 1.35;
        word-break: break-all;
        user-select: text;
        margin-left: 8px;
      }
      .bny-ai-debug-wrap.is-on .bny-ai-debug {
        display: inline;
      }
      .col-picker label {
        display: inline-block;
        margin: 4px 10px 4px 0;
        font-weight: normal;
        cursor: pointer;
        white-space: nowrap;
      }
      .col-picker .preset-btn {
        margin-right: 6px;
        margin-bottom: 6px;
      }
      @media print {
        .no-print { display: none !important; }
        @page {
          size: A4 landscape;
          margin: 5mm 6mm;
        }
        html, body {
          width: 100% !important;
          height: auto !important;
          font-size: 8pt !important;
          padding: 0 !important;
          margin: 0 !important;
        }
        .table1 {
          margin-top: 0 !important;
          page-break-before: auto !important;
          page-break-inside: avoid !important;
          width: 100% !important;
          height: auto !important;
          font-size: 8pt !important;
        }
        .table1 h1 {
          font-size: 13pt !important;
          margin: 1pt 0 2pt !important;
          line-height: 1.15 !important;
        }
        .table1 h1 + div {
          display: none !important;
        }
        .table1 th,
        .table1 .bny,
        .table1 div {
          font-size: 8pt !important;
          line-height: 1.2 !important;
          padding: 0 2pt !important;
        }
        .table2 {
          width: 100% !important;
          height: auto !important;
          font-size: 8pt !important;
          table-layout: fixed !important;
          border-collapse: collapse !important;
        }
        .table2 th,
        .table2 td,
        .table2 .transacdetail,
        .table2 .conclution {
          font-size: 8pt !important;
          line-height: 1.15 !important;
          padding: 1.4pt 2.2pt !important;
          white-space: nowrap !important;
          overflow: hidden !important;
          vertical-align: middle !important;
          border-right: 0.4pt solid #999 !important;
          border-bottom: 0.3pt solid #ccc !important;
        }
        .table2 thead tr.colhead th {
          font-size: 7.5pt !important;
          line-height: 1.15 !important;
          border-bottom: 0.8pt solid #222 !important;
        }
        .table2 td[data-col="invoice"],
        .table2 th[data-col="invoice"],
        .table2 td[data-col="invoice"] a {
          font-size: 7pt !important;
          white-space: nowrap !important;
          overflow: hidden !important;
        }
        .table2 td[data-col="price"],
        .table2 td[data-col="seller"],
        .table2 td[data-col="shopee"],
        .table2 td[data-col="lazada"],
        .table2 td[data-col="tiktok"],
        .table2 td[data-col="totaldisc"],
        .table2 td[data-col="ship"],
        .table2 td[data-col="vatexcl"],
        .table2 td[data-col="vat"],
        .table2 td[data-col="vatincl"],
        .table2 .conclution {
          font-variant-numeric: tabular-nums;
        }
        .table2 tbody tr.data-row td,
        .table2 thead tr.colhead th {
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }
      }
    </style>
  </head>
  <body style="background: #FFF;">
    <div class="col-picker no-print">
      <div>
        <strong>คอลัมน์:</strong>
        <button type="button" class="preset-btn" id="cols_standard">มาตรฐาน (สรรพากร)</button>
        <button type="button" class="preset-btn" id="cols_full">แสดงทั้งหมด</button>
        <button type="button" class="preset-btn" id="cols_orders">รายออเดอร์ประจำเดือน</button>
        <button type="button" class="preset-btn" id="cols_print">พิมพ์</button>
        <?php if (!empty($bny_ai_debug_html)) { echo $bny_ai_debug_html; } ?>
      </div>
      <div id="col_checks">
        <label><input type="checkbox" data-toggle-col="no" checked> ลำดับ</label>
        <label><input type="checkbox" data-toggle-col="date" checked> วัน/เดือน/ปี</label>
        <label><input type="checkbox" data-toggle-col="invoice" checked> เลขที่ใบกำกับภาษี</label>
        <label><input type="checkbox" data-toggle-col="buyer" checked> ชื่อผู้ซื้อ</label>
        <label><input type="checkbox" data-toggle-col="taxid" checked> เลขประจำตัวผู้เสียภาษี</label>
        <label><input type="checkbox" data-toggle-col="price" checked> มูลค่าสินค้า</label>
        <label><input type="checkbox" data-toggle-col="seller" checked> ส่วนลดร้านค้า</label>
        <label><input type="checkbox" data-toggle-col="shopee"> ส่วนลด Shopee</label>
        <label><input type="checkbox" data-toggle-col="totaldisc"> ส่วนลดรวม</label>
        <label><input type="checkbox" data-toggle-col="ship" checked> ค่าขนส่ง</label>
        <label><input type="checkbox" data-toggle-col="vatexcl" checked> ราคาไม่รวม VAT</label>
        <label><input type="checkbox" data-toggle-col="vat" checked> VAT</label>
        <label><input type="checkbox" data-toggle-col="vatincl" checked> ราคารวม VAT</label>
      </div>
      <div style="color:#555;margin-top:4px;">มาตรฐาน (ส่งสรรพากร) = ลำดับ, วัน, ใบกำกับ, ผู้ซื้อ, เลขผู้เสียภาษี, มูลค่าสินค้า, ส่วนลดร้านค้า, ค่าขนส่ง, ราคาไม่รวม VAT, VAT, ราคารวม VAT — พิมพ์ A4 แนวนอน หน้าเดียวทั้งเดือน — แถบนี้ไม่ถูกพิมพ์</div>
    </div>
    <center>
      <?php
      if($validdata==0)
      {?>
      <center>ไม่พบข้อมูล กรุึณาระบุช่วงเวลาให้ถูกต้อง</center>
      <?php
      }
      else
      {
        $total_rows= count($shopee_orders);
        $rows_per_page=50;
        $row_runner=1;
        $page_runner=0;
        $totalpages=ceil($total_rows/$rows_per_page);
        $sum_ref_price = 0;
        $page_priceacc=0;
        $page_voucher_seller=0;
        $page_voucher_platform=0;
        $page_voucher=0;
        $page_VATacc=0;
        $page_priceBeforeVAT=0;
        $page_shipping_fee_acc = 0;

        while($row_runner<=$total_rows)
        {
          foreach($shopee_orders as $row)
          {
            if(fmod($row_runner-1,$rows_per_page)==0 || $row_runner==1)
            {
      ?>
      <table class="table1 table-borderless">
        <col style="width:5%">
        <col style="width:10%">
        <col style="width:20%">
        <col style="width:20%">
        <col style="width:15%">
        <col style="width:10%">
        <col style="width:15%">
        <col style="width:5%">
        <thead>
          <tr>
              <th colspan="6" style="text-align: left;">เดือน/ปี ภาษี: <?php
                $start_date_date=date_create($start_date);
                $end_date_date=date_create($end_date);
                if($start_date_date->format("m/Y") === $end_date_date->format("m/Y"))
                {
                  echo $start_date_date->format("m/Y");
                }
                else
                {
                  echo $start_date_date->format("m/Y")." - ".$end_date_date->format("m/Y");
                } ?>
                 (วันที่จาก <?php echo $start_date_date->format("d/m/Y"); ?> ถึง <?php echo $end_date_date->format("d/m/Y"); ?>)
              </th>
              <th colspan="2" style="text-align: right;"> วันที่: <?php echo date("Y/m/d");?></th>
          </tr>
          <tr>
            <th colspan="8"><center><h1>รายงานภาษีขาย(Shopee)</h1></center></th>
          </tr>
          <tr>
            <th colspan="6" class="bny">ชื่อผู้ประกอบการ: บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</th>
            <th colspan="2" style="text-align: right;" class="bny">หน้า: <?php echo $page_runner+1;?></th>
          </tr>
          <tr>
            <th colspan="8" class="bny">ที่อยู่: 23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ.ภูเก็ต 83110</th>
          </tr>
          <tr>
            <th colspan="8" class="bny">เลขประจำตัวผู้เสียภาษี: 0835563000306 สำนักงานใหญ่</th>
          </tr>
        </thead>
      </table>
      <table class="table2">
        <col data-col="no" style="width:4%">
        <col data-col="date" style="width:9%">
        <col data-col="invoice" style="width:26%">
        <col data-col="buyer" style="width:8%">
        <col data-col="taxid" style="width:9%">
        <col data-col="price" style="width:7%">
        <col data-col="seller" style="width:7%">
        <col data-col="shopee" style="width:7%">
        <col data-col="totaldisc" style="width:7%">
        <col data-col="ship" style="width:6%">
        <col data-col="vatexcl" style="width:7%">
        <col data-col="vat" style="width:5%">
        <col data-col="vatincl" style="width:7%">
        <thead>
          <tr class="colhead">
            <th data-col="no" style="text-align:center">ลำดับ</th>
            <th data-col="date" style="text-align:center">วัน/เดือน/ปี</th>
            <th data-col="invoice" style="text-align:center">เลขที่ใบกำกับภาษี</th>
            <th data-col="buyer" style="text-align:center;line-height: initial;">ชื่อผู้ซื้อสินค้า<br>ผู้รับบริการ</th>
            <th data-col="taxid" style="text-align:center;line-height: initial;">เลขประจำตัว<br>ผู้เสียภาษี</th>
            <th data-col="price" style="text-align:right;line-height: initial;">มูลค่า<br>สินค้า</th>
            <th data-col="seller" style="text-align:right;line-height: initial;">ส่วนลด<br>ร้านค้า</th>
            <th data-col="shopee" style="text-align:right;line-height: initial;">ส่วนลด<br>Shopee</th>
            <th data-col="totaldisc" style="text-align:right;line-height: initial;">ส่วนลด<br>รวม</th>
            <th data-col="ship" style="text-align:right;line-height: initial;">ค่า<br>ขนส่ง</th>
            <th data-col="vatexcl" style="text-align:right;line-height: initial;">ราคาไม่รวม<br>VAT</th>
            <th data-col="vat" style="text-align:right">VAT</th>
            <th data-col="vatincl" style="text-align:right;line-height: initial;">ราคารวม<br>VAT</th>
          </tr>
        </thead>
      <?php } ?>
        <tbody>
          <tr class="data-row <?php echo ($row_runner % 2) ? 'zebra-odd' : 'zebra-even'; ?>">
            <td data-col="no" class="transacdetail" style="text-align:center"><center><?php echo $row_runner;?></center></td>
            <td data-col="date" class="transacdetail" style="text-align:center"><?php echo $row["transactiondate"];?> </td>
            <?php if(!empty($row['start_tiv'])){
            $taxno = $row["start_tiv"];
            $taxno_display = htmlspecialchars($taxno, ENT_QUOTES, 'UTF-8');
            }else{
            $start_inv = (string) $row["start_inv"];
            $end_inv = (string) $row["end_inv"];
            $taxno = $start_inv."-".$end_inv;
            $taxno_display = htmlspecialchars($taxno, ENT_QUOTES, 'UTF-8');
            }
            $seller_disc = isset($row["seller_discount"]) ? floatval($row["seller_discount"]) : 0;
            $shipping_fee = isset($row["shipping_fee"]) ? floatval($row["shipping_fee"]) : 0;
            $legacy_money = !empty($report_legacy);
            if ($legacy_money && isset($row["priceVATincluded"])) {
              $ref_price = floatval($row["priceVATincluded"]);
              $priceBeforeVAT = $ref_price / 1.07;
              $VAT = $ref_price - $priceBeforeVAT;
            } else {
              $ref_price = floatval($row["price"]) - $seller_disc + $shipping_fee;
              $priceBeforeVAT = $ref_price / 1.07;
              $VAT = $ref_price - $priceBeforeVAT;
            }
            $voucher_platform = isset($row["voucher_platform"]) ? floatval($row["voucher_platform"]) : 0;
            $voucher = isset($row["voucher"]) ? floatval($row["voucher"]) : ($seller_disc + $voucher_platform);
            ?>
            <td data-col="invoice" class="transacdetail" style="text-align:center"><a href="#" style="text-decoration: none" id="<?php echo base_url()."accounting/saletaxreport/sho_salereport_more/".$taxno?>" class="moretax" rel="nofollow"><?php echo $taxno_display;?></a></td>
            <td data-col="buyer" class="transacdetail" style="text-align:center" ><?php if(!empty($row['start_tiv'])){echo $row["cus_name"];}else{echo "-";}?></td>
            <td data-col="taxid" class="transacdetail" style="text-align:center"><?php if(!empty($row['TaxNo'])){echo $row["TaxNo"];}else{echo "-";}?></td>
            <td data-col="price" class="transacdetail"><?php echo number_format($row["price"],2,".",",");?></td>
            <td data-col="seller" class="transacdetail"><?php echo number_format($seller_disc,2,".",",");?></td>
            <td data-col="shopee" class="transacdetail"><?php echo number_format($voucher_platform,2,".",",");?></td>
            <td data-col="totaldisc" class="transacdetail"><?php echo number_format($voucher,2,".",",");?></td>
            <td data-col="ship" class="transacdetail"><?php echo number_format($shipping_fee,2,".",",");?></td>
            <td data-col="vatexcl" class="transacdetail"><?php echo number_format($priceBeforeVAT,2,".",",");?></td>
            <td data-col="vat" class="transacdetail"><?php echo number_format($VAT,2,".",",");?></td>
            <td data-col="vatincl" class="transacdetail"><?php echo number_format($ref_price,2,".",",");?></td>
          </tr>
          <?php
            $page_priceacc=$page_priceacc+floatval($row["price"]);
            $page_voucher_seller=$page_voucher_seller+$seller_disc;
            $page_voucher_platform=$page_voucher_platform+$voucher_platform;
            $page_voucher=$page_voucher+$voucher;
            $page_VATacc=$page_VATacc+$VAT;
            $page_priceBeforeVAT=$page_priceBeforeVAT+$priceBeforeVAT;
            $page_shipping_fee_acc=$page_shipping_fee_acc+$shipping_fee;
            $sum_ref_price = $sum_ref_price+$ref_price;
            if(fmod($row_runner,$rows_per_page)==0)
            {
              $page_runner++;
            }
              $row_runner++;
            }
          }
          if(($page_runner+1)==$totalpages)
            { ?>
            <tr class="total-row">
              <td data-col="no" colspan="5"><div style="text-align: right;">รวมทั้งหมด: </div></td>
              <td data-col="price" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_priceacc,2,".",","); ?></div></td>
              <td data-col="seller" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher_seller,2,".",","); ?></div></td>
              <td data-col="shopee" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher_platform,2,".",","); ?></div></td>
              <td data-col="totaldisc" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher,2,".",","); ?></div></td>
              <td data-col="ship" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_shipping_fee_acc,2,".",","); ?></div></td>
              <td data-col="vatexcl" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_priceBeforeVAT,2,".",","); ?></div></td>
              <td data-col="vat" class="conclution" ><div style="text-align: right;"><?php echo number_format($page_VATacc,2,".",","); ?></div></td>
              <td data-col="vatincl" class="conclution" ><div style="text-align: right;"><?php echo number_format($sum_ref_price,2,".",","); ?></div></td>
            </tr>
          </tbody>
        </table>
  <?php }}?>
    </center>
  </body>
</html>
<script src="<?php echo base_url();?>global/vendor/jquery/jquery.js"></script>
<script>
  $("a.moretax").on("click", function() {
    var share_link = $(this).prop('id');
    window.open(share_link, "_blank", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,top=500,left=500,width=1200,height=800");
    return false;
  });

  var COL_STORE = "shopee_tax_report_cols_v4";
  var STANDARD_ON = ["no","date","invoice","buyer","taxid","price","seller","ship","vatexcl","vat","vatincl"];
  var ALL_COLS = ["no","date","invoice","buyer","taxid","price","seller","shopee","totaldisc","ship","vatexcl","vat","vatincl"];

  function setChecks(onCols) {
    $("#col_checks input[type=checkbox]").each(function() {
      var col = $(this).attr("data-toggle-col");
      this.checked = onCols.indexOf(col) !== -1;
    });
  }

  var COL_WEIGHT = {
    no: 4, date: 9, invoice: 26, buyer: 8, taxid: 10,
    price: 8, seller: 7, shopee: 7, lazada: 7, tiktok: 7,
    totaldisc: 7, ship: 7, vatexcl: 8, vat: 6, vatincl: 8
  };

  function redistributeCols() {
    var total = 0;
    $("table.table2 col[data-col]").each(function() {
      if ($(this).css("display") === "none") return;
      total += COL_WEIGHT[$(this).attr("data-col")] || 8;
    });
    if (!total) return;
    $("table.table2 col[data-col]").each(function() {
      if ($(this).css("display") === "none") return;
      var w = COL_WEIGHT[$(this).attr("data-col")] || 8;
      this.style.width = (w / total * 100).toFixed(3) + "%";
    });
  }

  function applyCols() {
    var shown = {};
    $("#col_checks input[type=checkbox]").each(function() {
      shown[$(this).attr("data-toggle-col")] = this.checked;
    });
    $("table.table2 th[data-col], table.table2 td[data-col], table.table2 col[data-col]").each(function() {
      var col = $(this).attr("data-col");
      $(this).toggle(!!shown[col]);
    });
    redistributeCols();
    try {
      localStorage.setItem(COL_STORE, JSON.stringify(shown));
    } catch (e) {}
  }

  function loadSaved() {
    try {
      var saved = JSON.parse(localStorage.getItem(COL_STORE) || "null");
      if (saved) {
        $("#col_checks input[type=checkbox]").each(function() {
          var col = $(this).attr("data-toggle-col");
          if (typeof saved[col] !== "undefined") {
            this.checked = !!saved[col];
          }
        });
      }
    } catch (e) {}
  }

  $(function() {
    loadSaved();
    applyCols();
    window.addEventListener("beforeprint", redistributeCols);
    $("#col_checks").on("change", "input[type=checkbox]", applyCols);
    $("#cols_standard").on("click", function() {
      setChecks(STANDARD_ON);
      applyCols();
    });
    $("#cols_full").on("click", function() {
      setChecks(ALL_COLS);
      applyCols();
    });
    $("#cols_print").on("click", function() {
      window.print();
    });
    $("#cols_orders").on("click", function() {
      var nums = [];
      $("a.moretax").each(function() {
        var id = $(this).attr("id") || "";
        var matches = id.match(/(?:Shp|Sho)\d{11}/gi);
        if (matches) {
          for (var i = 0; i < matches.length; i++) {
            nums.push(matches[i].replace(/^(?:Shp|Sho)/i, ""));
          }
        }
      });
      if (!nums.length) {
        alert("ไม่พบเลขที่ใบกำกับภาษี");
        return;
      }
      nums.sort();
      var url = <?php echo json_encode(base_url()."accounting/saletaxreport/sho_salereport_more/"); ?> + "Shp" + nums[0] + "-Shp" + nums[nums.length - 1];
      window.open(url, "_blank", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no,top=80,left=80,width=1200,height=800");
    });
  });
</script>
