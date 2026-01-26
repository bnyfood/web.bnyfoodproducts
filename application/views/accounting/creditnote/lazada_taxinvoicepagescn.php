<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Bny Accounting</title>
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />        
    <style>
     table {
        width: 100%;
        height: 100%; 
        background: #FFF;
        overflow:visible;
        page-break-before: always;
        table-layout:fixed;
        border: 0px solid white;
        border-spacing: 0px;
        border-collapse: separate;
        margin-top: 0.1em;
        margin-bottom: 0.1em;
      }

      td { 
        padding: 0.1em;
        font-size: 1em;
        height: 1em;
      }

      tr { line-height:.01em; }

      body {
            padding-top:0.5em;
      }

      .bny{
        font-size: 2em;
        line-height: 1.2em;
      }

      .taxinvoice{
        font-size: 1.6em;
        font-weight: bolder;
        height: 2.5em;
      }

      .taxinvoicenumber{
       font-size: 1.2em;
       line-height: 1.2em;
      }

      .taxinvoicenumberbold {
        font-size: 1.2em;
        line-height: 1.2em;
        font-weight: bold;
      }

      .transacdetail{
        padding: 1em;
        font-size: 1.2em;
        overflow-wrap: normal;
      }

      .conclution{
        padding: 1em !important;
        line-height: 0.001em  !important;
        font-size:3em;
      }

      .table_border{
        filter: alpha(opacity=40); 
        opacity: 0.95;
        border:1px black solid;
        border-radius:6px;
        -moz-border-radius:6px;
      }
    </style>
  </head>
<body >
  <center>                                          
  <?php  
    if($orders==0)
  {?>
    <center>ไม่พบข้อมูล กรุึณาระบุช่วงเวลาให้ถูกต้อง</center>
  <?php 
  }
    else
  {
    foreach($orders as $row)
    {
  ?>
  <table >
    <col style="width:20%">
    <col style="width:30%">
    <col style="width:10%">
    <col style="width:20%">
    <col style="width:20%">
  <thead>
    <tr>
      <td scope="row" colspan="5"><center><span class="bny"></span></center></td>
    </tr>
    <tr>
      <td scope="row" colspan="5"><center><span class="bny">บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</span></center></td>
    </tr>
    <tr>
      <td  scope="row" colspan="5"><center><span class="bny">23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ. ภูเก็ต 83110 สำนักงานใหญ่ เลขประจำตัวผู้่เสียภาษี 0835563000306</span></center></td>
    </tr>
    <tr>
      <td colspan="5" style="height: 5px;"><center><span class="bny"></span></center></td>
    </tr>
    <tr>
      <td colspan="5" class="taxinvoice"><center><span class="taxinvoice">ใบลดหนี้/ใบกำกับภาษี</span></center></td>
    </tr>
    <tr>
      <td colspan="5" ><div style="text-align: right;padding: 0px 10px 10px 0px;"><span class="taxinvoicenumber">เลขที่ใบลดหนี้: <?php echo $row["cncode"];?></span></div></td>
    </tr>
    <tr>
      <td colspan="5" ><div style="text-align: right;padding: 0px 10px 10px 0px;"><span class="taxinvoicenumber">วันที่: 
        <?php 
          $date=date_create($row["updated_at"]);
          echo date_format($date,"d/F/Y");?> 
        </span></div>
      </td>
    </tr>
    <tr>
      <td colspan="5" >
        <table width="100%">
          <tr>
            <td width="50%">
              <table class='table_border'>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">ชื่อ: <?php echo $row["customer_name"];?></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">ที่อยู่: <?php echo $row["address1"]." ".$row["address2"]." ".$row["customer_zip"];?></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">เบอร์โทร: <?php echo $row["customer_phone"];?> เลขที่ผู้เสียภาษี: <?php echo $row["TaxNo"];?></span>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
            <td width="50%">
              <table class='table_border'>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">เลขที่ใบกำกับภาษีเดิม: <?php echo $row["taxinvoiceID"];?></span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">วันที่ใบกำกับภาษี: 
                      <?php 
                          $date=date_create($row["created_at"]);
                          echo date_format($date,"d/F/Y");?> 
                    </span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div style="text-align: left;padding: 10px 10px 10px 10px;"><span class="taxinvoicenumber">หมายเลขคำสั่งซื้อ Lazada: <?php echo $row["order_number"]; ?>, สาเหตุ: 
                      <?php 
                        if($row["order_status"]== "returned"){
                          echo "ลูกค้าคืนสินค้า";
                        }else{
                          echo "สินค้าเสียหาย/ส่งไม่สำเร็จ";
                        }
                      ?> 
            
                      </span>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>  
        </table>
      </td>
    </tr>
  </thead>
<tbody>
  <tr style="height: 30px;"><td colspan="5"></td></tr>
  <tr>
    <td colspan="5" >
      <table class='table table-striped table_border table_border' width="100%">
        <tr class="taxinvoicenumberbold">
          <td width="40%">ชื่อสินค้า</td>
          <td width="10%">จำนวน</td>
          <td width="25%" style="text-align: right;padding-right: 10px">ราคา/หน่วย</td>
          <td width="25%" style="text-align: right;padding-right: 10px">จำนวนเงิน</td>
        </tr>
        <?php
          $latest_status = $row["latest_status"];
          $numcount=0;
          $priceacc=0;
          $sum_cn = 0;
          foreach($row["suborder"] as $suborder_detail)
          {
            if($latest_status == "returned"){
              $sum_cn = $sum_cn+$suborder_detail["price"];
            }
        ?>
          <tr>
            <td class="transacdetail" style="nth-of-type(odd){background:rgba(255,255,136,0.5);"><?php echo $suborder_detail["ProductName"];?></td>
            <td class="transacdetail">1</td>
            <td class="transacdetail" style="text-align: right;padding-right: 10px"><?php echo number_format($suborder_detail["price"],2);?></td>
            <td class="transacdetail" style="text-align: right;padding-right: 10px"><?php echo number_format($suborder_detail["price"],2);?></td>
          </tr>  
        <?php 
          $priceacc=$priceacc+$suborder_detail["price"];
          }

          $total_refund_val = $row["total_refund_val"];
          $shipping_fee = $row["shipping_fee"];
          $discount = $row["discount"];
          if($latest_status == "returned"){
            $shipping_fee = $total_refund_val - $sum_cn;
             $discount = 0;
          }

          $price=$priceacc+$shipping_fee-$discount;
          $pricebeforeVAT=$price/1.07;
          $VAT=$price-$pricebeforeVAT;

        ?> 
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">รวมค่าสินค้า</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($priceacc,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">ค่าขนส่ง</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($shipping_fee,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">ส่วนลด</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($discount,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">จำนวนเงินหลังหักส่วนลด</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($price,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">มูลค่าเอกสารเดิม</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($price,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">มูลค่าที่ถูกต้อง</td>
          <td style="text-align: right;padding-right: 10px">0.00</td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">ผลต่าง</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($price,2)?></td>
        </tr>
        <tr class="taxinvoicenumber">
          <td></td>
          <td colspan="2">ภาษีมูลค่าเพิ่ม</td>
          <td style="text-align: right;padding-right: 10px"><?php echo number_format($VAT,2)?></td>
        </tr>
      </table>  
    </td>
   </tr>
   <tr>
    <td colspan="5"> 
      <table class='table_border'>

        <tr><td style="text-align: right;padding: 40px 10px 20px 0px">อณุมัติโดย________________________</td></tr>
        <tr><td style="text-align: right;padding: 10px 10px 40px 0px">วันที่____________________________</td></tr>
      </table>  
    </td>
   </tr>
  </tbody>
</table>
                                                <!--end table-->
   <?php
      }// for loop order page
      }// else $orders==0
   ?>     

    </center>   
    </body>
</html>