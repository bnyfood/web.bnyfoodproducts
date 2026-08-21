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
        margin-top: 3em;
        width: 100%;
        height: 100%;      
        background: #FFF;
        overflow:visible;
        page-break-before: always;
        table-layout:fixed;
        border: 0px solid black;
        border-spacing: 0px;
        border-collapse: separate;
        font-size: 1em;
      }
      .table2 {
        width: 100%;
        height: 100%; 
        background: #FFF;
        overflow:visible;
        table-layout:fixed;
        border: 0px solid black;
        border-spacing: 0px;
        border-collapse: collapse;
        margin-top: 1.2em;
      }

      td { 
        padding: 0.25em 0.4em;
        font-size: 1em;
        height: auto;
      }
      tr { line-height: 1.25; }

      body {
        padding-top: 0.5em;
        padding-left: 14px;
        padding-right: 18px;
        padding-bottom: 1em;
      }
      .bny{
        font-size: 0.8em;
        line-height: 1.45em;
        padding: 0.15em 0;
      }
      .tbhead{
        font-size: 0.8em;
        line-height: 1.2em;
      }
      .taxinvoice{
       font-size: 2.5em;
       font-weight: bolder;
       height: 2.5em;
      }
      .taxinvoicenumber{
        font-size: 3em;
        line-height: 1.2em;
      }
     .transacdetail{
        padding: 0.2em 0.4em;
        font-size: 0.8em;
        text-align: right;
     }
     .conclution{
      padding: 1em !important;
      line-height: 0.5em  !important;
      font-size: 1em;
     }
      .table2 tbody tr.data-row.zebra-odd td {
        background-color: #f3f5f7;
      }
      .table2 tbody tr.data-row.zebra-even td {
        background-color: #fff;
      }
      .table2 thead tr.colhead th {
        border-bottom: 1px solid #c8cdd3;
        padding: 0.35em 0.4em 0.45em;
        font-size: 0.8em;
        vertical-align: bottom;
        box-sizing: border-box;
      }
      .table2 thead tr.colhead th.amt-head {
        text-align: right !important;
      }
      .table2 tbody td.transacdetail {
        padding: 0.2em 0.4em;
        box-sizing: border-box;
      }
      .table2 tbody tr.total-row td {
        background-color: #e8f0fe;
        border-top: 1px solid #222;
        border-bottom: 1px solid #222;
      }
      .table1 {
        margin-bottom: 0.5em;
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
      .col-picker button {
        margin-right: 6px;
        margin-bottom: 6px;
      }
      .report-panel { display: none; }
      .report-panel.is-on { display: block; }
      @media print {
        .no-print { display: none !important; }
        .report-panel { display: none !important; }
        .report-panel.is-on { display: block !important; }
        @page {
          size: A4 landscape;
          margin: 7mm 6mm;
        }
        html, body {
          font-size: 9pt !important;
          padding: 0 !important;
          margin: 0 !important;
        }
        td, th {
          overflow: visible !important;
        }
        .table1 {
          margin-top: 0 !important;
          page-break-before: auto !important;
        }
        .table1 h1 {
          font-size: 14pt !important;
          margin: 2pt 0 4pt !important;
        }
        .table1 th,
        .table1 .bny,
        .table2 th,
        .table2 td,
        .table2 .transacdetail {
          font-size: 8.5pt !important;
          line-height: 1.25 !important;
          padding: 2pt 3pt !important;
        }
        .table2 tbody tr.data-row td,
        .table2 thead tr.colhead th,
        .table2 tbody tr.total-row td {
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }
      }
    </style>   
  </head>
  <body style="background: #FFF;">
    <div class="col-picker no-print">
      <button type="button" id="cols_orders">รายออเดอร์ประจำเดือน</button>
      <button type="button" id="cols_print_orders">พิมพ์รายออเดอร์</button>
      <button type="button" id="cols_daily">สรุปรวมรายวันทั้งเดือน</button>
      <button type="button" id="cols_print_daily">พิมพ์สรุปรวมรายวันทั้งเดือน</button>
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
        $total_rows= count($tiktok_orders);                                                  
        $rows_per_page=32;
        $row_runner=1;  
        $page_runner=0;
        $totalpages=ceil($total_rows/$rows_per_page);
        $page_priceacc=0;
        $page_VATacc=0;
        $page_inclacc=0;
        $page_goodsacc=0;
        $page_selleracc=0;
        $page_priceacc_tot=0;
        $page_VATacc_tot=0;
      ?>
      <div id="report_orders" class="report-panel is-on report-orders">
      <?php
        while($row_runner<=$total_rows)
        {
          if(!empty($tiktok_orders)){
          foreach($tiktok_orders as $row)
          {
            if(fmod($row_runner-1,$rows_per_page)==0 || $row_runner==1) // new page
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
              }
            ?> (วันที่จาก <?php echo $start_date_date->format("d/m/Y"); ?> ถึง <?php echo $end_date_date->format("d/m/Y"); ?>) </th>
            <th colspan="2" style="text-align: right;"> วันที่: <?php echo date("Y/m/d");?></th>  
          </tr>
          <tr>
            <th colspan="8"><center><h1>รายงานใบลดหนี้(Tiktok) รายออเดอร์ประจำเดือน</h1></center></th>
          </tr>  
          <tr>
            <th colspan="7" class="bny">ชื่อผู้ประกอบการ: บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</th>
            <th colspan="1" style="text-align: right;" class="bny">หน้า: <?php echo $page_runner+1;?></th>
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
          <col style="width:4%">
          <col style="width:8%">
          <col style="width:12%">
          <col style="width:11%">
          <col style="width:12%">
          <col style="width:9%">
          <col style="width:9%">
          <col style="width:7%">
          <col style="width:7%">
          <col style="width:8%">
          <col style="width:6%">
          <col style="width:7%">
        <thead>   
            <tr class="colhead">    
                <th style="text-align:center">ลำดับ</th>
                <th style="text-align:center">ปี/เดือน/วัน</th>
                <th style="text-align:center">เลขที่ใบกำกับภาษี</th>
                <th style="text-align:center">เลขที่ใบลดหนี้</th>
                <th style="text-align:center">TTKOrderNumber</th>
                <th style="text-align:center">ชื่อผู้ซื้อสินค้า</th>
                <th style="text-align:center">เลขประจำตัวผู้เสียภาษี</th>
                <th class="amt-head" style="text-align:right">มูลค่าสินค้า</th>
                <th class="amt-head" style="text-align:right">ส่วนลดร้านค้า</th>
                <th class="amt-head" style="text-align:right">ราคาไม่รวม VAT</th>
                <th class="amt-head" style="text-align:right">VAT</th>
                <th class="amt-head" style="text-align:right">ราคารวม VAT</th>
            </tr>
        </thead>
        <?php
          }   //new page
        ?>
        <tbody>                                   
          <tr class="data-row <?php echo ($row_runner % 2) ? 'zebra-odd' : 'zebra-even'; ?>">
            <td class="transacdetail" style="text-align:center"><center><?php echo $row_runner;?></center></td>
            <td class="transacdetail" style="text-align:center"><?php echo $row["updated_at"];?> </td>
            <?php if(!empty($row['start_tiv'])){
              $taxno = $row["start_tiv"];
            }else{
              $taxno = $row["start_inv"];
            }
             ?>
            <td class="transacdetail" style="text-align:center"><?php echo $taxno;?></td>
            <td class="transacdetail" style="text-align:center"><?php echo $row["cncode"];?></td>
            <td class="transacdetail" style="text-align:center"><?php echo $row["order_number"];?></td>
            <td class="transacdetail" style="text-align:center" ><?php if(!empty($row['start_tiv'])){echo $row["cus_name"];}else{echo "-";}?></td>
            <td class="transacdetail" style="text-align:center"><?php if(!empty($row['TaxNo'])){echo $row["TaxNo"];}else{echo "-";}?></td>
            <?php
              $tax = $this->order_util->cn_tax_amounts($row, true);
              $goods = $tax['price'];
              $seller_disc = $tax['seller_discount'];
              $amount_incl = $tax['vatincl'];
              $VAT = $tax['vat'];
              $amount_excl = $tax['vatexcl'];
            ?>
            <td class="transacdetail"><?php echo number_format($goods,2,".",",");?></td>
            <td class="transacdetail"><?php echo number_format($seller_disc,2,".",",");?></td>
            <td class="transacdetail"><?php echo number_format($amount_excl,2,".",",");?></td>
            <td class="transacdetail"><?php echo number_format($VAT,2,".",",");?></td>
            <td class="transacdetail"><?php echo number_format($amount_incl,2,".",",");?></td>
          </tr>
         <?php
            $page_goodsacc=$page_goodsacc+$goods;
            $page_selleracc=$page_selleracc+$seller_disc;
            $page_priceacc=$page_priceacc+$amount_excl;
            $page_VATacc=$page_VATacc+$VAT;
            $page_inclacc=$page_inclacc+$amount_incl;                                         
            if(fmod($row_runner,$rows_per_page)==0) // new page
            {
              $page_runner++;
            }
              $row_runner++;
          }
        }
          if(($page_runner+1)==$totalpages)
            { ?> 
              <tr class="total-row">  
                <td colspan="7" class="transacdetail" style="text-align:right;">รวมทั้งหมด:</td>  
                <td class="transacdetail"><?php echo number_format($page_goodsacc,2,".",","); ?></td>
                <td class="transacdetail"><?php echo number_format($page_selleracc,2,".",","); ?></td>
                <td class="transacdetail"><?php echo number_format($page_priceacc,2,".",","); ?></td>
                <td class="transacdetail"><?php echo number_format($page_VATacc,2,".",","); ?></td>
                <td class="transacdetail"><?php echo number_format($page_inclacc,2,".",","); ?></td>
              </tr>
          </tbody>
        </table>
      <?php } } ?>
      </div>
      <div id="report_daily" class="report-panel report-daily">
        <?php
          $daily_orders = !empty($tiktok_orders_daily) ? $tiktok_orders_daily : array();
          $d_price = 0;
          $d_vat = 0;
          $d_incl = 0;
          $d_n = 0;
        ?>
        <table class="table1 table-borderless">
          <thead>
            <tr>
              <th colspan="6" style="text-align: left;">เดือน/ปี ภาษี: <?php
                $start_date_date=date_create($start_date);
                $end_date_date=date_create($end_date);
                echo $start_date_date->format("m/Y");
              ?> (วันที่จาก <?php echo $start_date_date->format("d/m/Y"); ?> ถึง <?php echo $end_date_date->format("d/m/Y"); ?>)</th>
              <th colspan="2" style="text-align: right;"> วันที่: <?php echo date("Y/m/d");?></th>
            </tr>
            <tr>
              <th colspan="8"><center><h1>รายงานใบลดหนี้(Tiktok) สรุปรวมรายวันทั้งเดือน</h1></center></th>
            </tr>
            <tr>
              <th colspan="6" class="bny">ชื่อผู้ประกอบการ: บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</th>
              <th colspan="2" style="text-align: right;" class="bny">หน้า: 1</th>
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
          <col style="width:8%">
          <col style="width:14%">
          <col style="width:38%">
          <col style="width:14%">
          <col style="width:12%">
          <col style="width:14%">
          <thead>
            <tr class="colhead">
              <th style="text-align:center">ลำดับ</th>
              <th style="text-align:center">ปี/เดือน/วัน</th>
              <th style="text-align:center">เลขที่ใบลดหนี้</th>
              <th class="amt-head" style="text-align:right">Amount(VAT Excluded)</th>
              <th class="amt-head" style="text-align:right">VAT</th>
              <th class="amt-head" style="text-align:right">Amount(VAT included)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($daily_orders as $drow) {
              $d_n++;
              $tax = $this->order_util->cn_tax_amounts($drow, true);
              $d_price += $tax['vatexcl'];
              $d_vat += $tax['vat'];
              $d_incl += $tax['vatincl'];
            ?>
            <tr class="data-row <?php echo ($d_n % 2) ? 'zebra-odd' : 'zebra-even'; ?>">
              <td class="transacdetail" style="text-align:center"><?php echo $d_n;?></td>
              <td class="transacdetail" style="text-align:center"><?php echo $drow["updated_at"];?></td>
              <td class="transacdetail" style="text-align:center;word-break:break-all;"><?php echo htmlspecialchars($drow["cncode"], ENT_QUOTES, 'UTF-8');?></td>
              <td class="transacdetail"><?php echo number_format($tax['vatexcl'],2,".",",");?></td>
              <td class="transacdetail"><?php echo number_format($tax['vat'],2,".",",");?></td>
              <td class="transacdetail"><?php echo number_format($tax['vatincl'],2,".",",");?></td>
            </tr>
            <?php } ?>
            <tr class="total-row">
              <td colspan="3" class="transacdetail" style="text-align:right;">รวมทั้งหมด:</td>
              <td class="transacdetail"><?php echo number_format($d_price,2,".",","); ?></td>
              <td class="transacdetail"><?php echo number_format($d_vat,2,".",","); ?></td>
              <td class="transacdetail"><?php echo number_format($d_incl,2,".",","); ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <?php } ?>
    </center>
    <script>
      (function() {
        function showPanel(id) {
          var panels = document.querySelectorAll(".report-panel");
          for (var i = 0; i < panels.length; i++) {
            panels[i].classList.remove("is-on");
          }
          var el = document.getElementById(id);
          if (el) { el.classList.add("is-on"); }
        }
        function bind(id, fn) {
          var el = document.getElementById(id);
          if (el) { el.onclick = fn; }
        }
        bind("cols_orders", function() { showPanel("report_orders"); });
        bind("cols_daily", function() { showPanel("report_daily"); });
        bind("cols_print_orders", function() {
          showPanel("report_orders");
          window.print();
        });
        bind("cols_print_daily", function() {
          showPanel("report_daily");
          window.print();
        });
      })();
    </script>
  </body>
</html>
