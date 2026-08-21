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
        body {
            font-family: "TH Sarabun New", sans-serif;
            margin: 40px;
        }
        .header, .footer {
            text-align: center;
        }
        .company-info, .po-info, .contact-info {
            display: inline-block;
            vertical-align: top;
        }
        .company-info img {
            width: 80px;
        }
        .po-info {
            float: right;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 18px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        .no-border {
            border: none;
        }
        .right-align {
            text-align: right;
        }
        .left-align {
            text-align: left;
        }
        .signature-section {
            margin-top: 40px;
            font-size: 18px;
        }
        .signature-section td {
            padding: 20px;
            text-align: center;
            vertical-align: bottom;
        }
        .bold {
            font-weight: bold;
        }
        .border-bottom-none {
          border-top: none; 
          border-bottom: none; 
          border-left: 1px solid black; 
          border-right: 1px solid black;
        }
    </style>
  </head>
  <body>

    <?php  
      if($validdata==0)
      {?>
      <center>ไม่พบข้อมูล</center>
      <?php 
      }
      else
      {
        $total_rows= count($arr_pos);                                                  
        $rows_per_page=50;
        $row_runner=1;  
        $page_runner=0;
        $totalpages=ceil($total_rows/$rows_per_page);
        $page_priceacc=0;
        $page_VATacc=0;

        $page_total_price=0;
        $page_total_qty=0;

        $page_priceacc=0;
         $page_voucher_seller=0;
         $page_voucher_platform=0;
         $page_voucher=0;
         $page_VATacc=0;
         $page_priceVATincluded=0;
         $page_priceBeforeVAT=0;
         $page_shipping_fee_acc = 0;

        //echo $row_runner."--".$total_rows;
        while($row_runner<=$total_rows)
        {
          foreach($arr_pos as $row)
          {         
            if(fmod($row_runner-1,$rows_per_page)==0 || $row_runner==1) // new page
            {

      ?>

    <div class="header">
      <h2>ใบสั่งซื้อ / (Purchase Order)</h2>
    </div>
    <div class="company-info">
      <img alt="logo" src="<?php echo base_url();?>resources/images/bnyfood.jpg"/>
      <p><strong>BNY Food Products CO.,LTD</strong></p>
    </div>
    <div class="po-info">
      <p><strong>PO No :</strong> <?php echo $po_number;?></p>
      <p>23/1 หมู่ 2 ตำบลศรีสุนทร อำเภอถลาง จังหวัดภูเก็ต 83110</p>
      <p>เลขประจำตัวผู้เสียภาษี 0835563000306</p>
      <p>ผู้ติดต่อ: นันทกานต์ สหรัตน์พงษ์ Mobile: 0866861119</p>
    </div>
    <div style="clear: both;"></div>

    <table>
      <tr>
        <td class="left-align" colspan="2" style="border-top: none; border-bottom: none; border-left: 1px solid black; border-right: 1px solid black;">  <strong>ผู้ขาย</strong><br/>
                        <?php echo $supplier_data['supplier_name']?><br/>
                        <?php echo $supplier_data['supplier_address']?><br/>

        </td>
        <td class="left-align" colspan="4" style="border-top: none; border-bottom: none; border-left: 1px solid black; border-right: 1px solid black;">  <strong>การชำระเงิน:</strong> เงินสด<br/>
          <strong>ผู้ติดต่อ:</strong> <?php echo $supplier_data['supplier_contact_name']?><br/>
                        เบอร์: <?php echo $supplier_data['phoneno1']?><br/>
                        Mail: <?php echo $supplier_data['supplier_email']?><br/>
                        Date: <?php echo $po_cdate?>
        </td>
      </tr>
      <tr>
        <th width="5%">ลำดับ</th>
        <th width="60%">รายละเอียด</th>
        <th width="5%">จำนวน</th>
        <th width="6%">หน่วย</th>
        <th width="12%">ราคาต่อหน่วย</th>
        <th width="12%">จำนวนเงิน</th>
      </tr>
      <?php }?>
      <?php $cal_price = $row["unit_price"]*$row["qty"];?>
      <tr>
        <td class="border-bottom-none"><?php echo $row_runner;?></td>
        <td class="border-bottom-none left-align"><?php echo $row["material_name"];?></td>
        <td class="border-bottom-none"><?php echo $row["qty"];?></td>
        <td class="border-bottom-none"><?php echo $row["material_unit"];?></td>
        <td class="border-bottom-none right-align"><?php echo number_format($row["unit_price"],2,".",",");?></td>
        <td class="border-bottom-none right-align"><?php echo number_format($cal_price,2,".",",");?></td>
      </tr>
      <?php
            $page_total_price=$page_total_price+$cal_price;

            if(fmod($row_runner,$rows_per_page)==0) // new page
            {
              $page_runner++;
            }
              $row_runner++;
 // echo "page_runner:".$page_runner;
 // echo "totalpages:".$totalpages;
            }

            $num_row_blank = 10-$row_runner;
            for($z=0;$z<=$num_row_blank;$z++){
            ?>
              <tr>
                <td class="border-bottom-none"></td>
                <td class="border-bottom-none left-align"></td>
                <td class="border-bottom-none"></td>
                <td class="border-bottom-none right-align"></td>
                <td class="border-bottom-none right-align"></td>
              </tr>
            <?php
            }
          }
          if(($page_runner+1)==$totalpages)
            { ?>       
      <tr>
        <td colspan="2" style="border-bottom: none;"></td>
        <td class="left-align" colspan="3" style="border: 1px solid black;"><strong>รวมเงิน</strong></td>
        <td class="right-align"  style="border: 1px solid black;"><?php echo number_format($page_total_price,2,".",","); ?></td>
      </tr>
      <?php 
        $total_vat = $page_total_price*0.07;
        $total_price_vat = $page_total_price+ $total_vat;

      ?>
      <tr>
        <td colspan="2" class="border-bottom-none"></td>
        <td class="left-align" colspan="3" style="border: 1px solid black;">ภาษีมูลค่าเพิ่ม VAT 7%</td>
        <td class="right-align" style="border: 1px solid black;"><?php echo number_format($total_vat,2,".",","); ?></td>
      </tr>
      <tr>
        <td colspan="2" >(<?php echo baht_text($total_price_vat); ?>)</td>
        <td class="left-align" colspan="3" style="border: 1px solid black;"><strong>รวมยอดทั้งสิ้น</strong></td>
        <td class="right-align" style="border: 1px solid black;"><strong><?php echo number_format($total_price_vat,2,".",","); ?></strong></td>
      </tr>
    </table>
    <?php }}?>
    <table class="signature-section" width="100%">
      <tr>
        <td width="40%" style="border: 1px solid black;border-right:none;">ลงชื่อ_____________________________ผู้มีอำนาจลงนาม<br/><br/><br/>
          (___นางพัทธิกาญจน์ สหรัตน์พงษ์___)<br/>
        วันที่ ___14/01/2568___</td>
        <td width="30%" style="border-left:none; border-right:none;" ></td>
        <td width="40%" style="border: 1px solid black;vertical-align: top;border-left:none;" >ประทับตราหน่วยงาน</td>
      </tr>
    </table>
  </body>
</html>
<script src="<?php echo base_url();?>global/vendor/jquery/jquery.js"></script>