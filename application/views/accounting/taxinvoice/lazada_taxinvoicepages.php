<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Metrica - Responsive Bootstrap 4 Admin Dashboard</title>
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
tr { line-height:.005em; }

    body {
        padding-top:0.5em;
    }

    .bny{

   font-size: 3.5em;
   line-height: 1.2em;

    }

    .bny-address{

   font-size: 2.0em;
   line-height: 1.25em;

    }

    .taxinvoice{

   font-size: 2.0em;
   font-weight: bolder;
   height: 2.5em;

    }

    .taxinvoicenumber{

   font-size: 3em;
   line-height: 1.2em;

    }


   .transacdetail{

padding: 0.5em;
font-size: 2.25em;
overflow-wrap: normal;

   }

   tr.item-row {
     line-height: 1.0125em;
   }

   tr.item-row td.transacdetail {
     padding-top: 0.50625em;
     padding-bottom: 0.50625em;
     line-height: 1.0125em;
   }

   .conclution{

padding: 0.18em 0.025em !important;
line-height: 1.15em  !important;
font-size:3em;

   }

   tr.qty-row td.conclution {
     padding-top: 0.35em !important;
   }

   tr.totals-gap td {
     height: 0.4em;
     line-height: 0.4em;
     padding: 0;
   }

   .amt {
     text-align: right;
     padding-right: 0;
     font-variant-numeric: tabular-nums;
     font-feature-settings: "tnum";
     font-family: "Courier New", Courier, monospace;
     white-space: nowrap;
   }

   td.amt-cell {
     text-align: right;
     padding-right: 10px !important;
     font-variant-numeric: tabular-nums;
     font-feature-settings: "tnum";
     white-space: nowrap;
   }

   tr.colhead-row td {
     font-size: 2.2em;
     font-weight: bold;
     line-height: 1.2em;
     padding: 0.35em 0.2em !important;
     border-bottom: 1px solid #222;
   }

   td.item-name {
     text-align: left;
     padding-left: 0.2em;
   }

   td.item-qty {
     text-align: right;
     padding-right: 10px;
   }

   tr.date-row td.date-cell {
     text-align: right;
     padding-right: 10px !important;
     padding-top: 0.1em !important;
     padding-bottom: 0.15em !important;
     line-height: 1.2em;
     vertical-align: bottom;
   }

   tr.date-row .dateinfo {
     display: block;
     width: 100%;
     box-sizing: border-box;
     text-align: right;
     font-size: 2.5em;
     line-height: 1.1em;
     font-family: inherit;
     white-space: nowrap;
     margin: 0;
     padding: 0 0 0 0;
   }

   /* same right edge for date + all money columns */
   td.amt-cell {
     text-align: right;
     padding-right: 10px !important;
     font-variant-numeric: tabular-nums;
     font-feature-settings: "tnum";
     white-space: nowrap;
   }

   tr.item-row td.transacdetail.amt-cell {
     padding-top: 0.50625em !important;
     padding-bottom: 0.50625em !important;
     padding-left: 0.5em !important;
     padding-right: 10px !important;
   }

   tr.totals-row td.conclution.amt-cell,
   tr.qty-row td.conclution.amt-cell {
     padding-right: 10px !important;
   }

   tr.colhead-row td.amt-cell {
     padding-right: 10px !important;
   }

   tr.date-items-gap td {
     height: 0.85em;
     line-height: 0.85em;
     padding: 0;
   }

   tr.item-row.zebra-odd td {
     background-color: #f3f5f7;
   }

   tr.item-row.zebra-even td {
     background-color: #fff;
   }

   @media print {
     tr.item-row.zebra-odd td,
     tr.item-row.zebra-even td {
       -webkit-print-color-adjust: exact;
       print-color-adjust: exact;
     }
   }


</style>
        
    </head>

    <body >

<?php $this->load->view('accounting/taxinvoice/_abb_print_toolbar'); ?>
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
                                 
                                                           <!--Table-->
<!--Table-->

<table >
    
    <col style="width:40%">
    <col style="width:22%">
    <col style="width:13%">
    <col style="width:25%">
<!--Table head-->
  <thead>
   <tr>
     
      <td scope="row" colspan="4"><center><span class="bny"></span></center></td>
     </tr>
    <tr>
     
      <td scope="row" colspan="4"><center><span class="bny">บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</span></center></td>
     </tr>
    <tr>
     
      <td scope="row" colspan="4"><center><span class="bny-address">23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ.ภูเก็ต 83110 สำนักงานใหญ่ เลขประจำตัวผู้เสียภาษี 0835563000306</span></center></td>
     </tr>
    <tr>
     
      <td colspan="4" style="height: 5px;"><center><span class="bny"></span></center></td>
     </tr>
    <tr>
     
      <td colspan="4" class="taxinvoice"><center><span class="taxinvoice">ใบกำกับภาษีอย่างย่อ/ใบเสร็จรับเงิน</span>
                                                            </center></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="4" ><center><span class="taxinvoicenumber"><?php echo $row["taxinvoiceID"];?></span>
                                                            </center></td>
     
    
    
    </tr>
     <tr class="date-row">
      <td colspan="4" class="date-cell"><div class="dateinfo">วันที่: <?php

$date=date_create($row["created_at"]);
echo date_format($date,"d/F/Y");

       ?></div></td>
    </tr>
    
    <tr class="date-items-gap">
      <td colspan="4"></td>
    </tr>
  </thead>
  <!--Table head-->
  <!--Table body--><tbody>
    <tr class="colhead-row">
      <td class="transacdetail item-name">รายการ</td>
      <td class="transacdetail amt-cell"><div class="amt">ราคาต่อหน่วย</div></td>
      <td class="transacdetail item-qty">จำนวน</td>
      <td class="transacdetail amt-cell"><div class="amt">จำนวนเงิน</div></td>
    </tr>
  <?php
  $numcount=0;
  $qtyacc=0;
  $priceacc=0;
  $price_discount_seller = 0;
  $price_discount_seller_shipping = 0;
  $price_discount_seller_shipping_exclude_vat = 0;
  $price_discount_seller_shipping_vat = 0;

  $agg_items = array();
  if (!empty($row["suborder"])) {
    foreach ($row["suborder"] as $suborder_detail) {
      $sku = isset($suborder_detail["sku"]) ? trim((string)$suborder_detail["sku"]) : '';
      $name = isset($suborder_detail["ProductName"]) ? $suborder_detail["ProductName"] : '';
      $unit_price = (float)$suborder_detail["price"];
      $key = ($sku !== '') ? ('sku:'.$sku) : ('name:'.$name.'|'.number_format($unit_price, 2, '.', ''));
      if (!isset($agg_items[$key])) {
        $agg_items[$key] = array(
          'ProductName' => $name,
          'sku' => $sku,
          'unit_price' => $unit_price,
          'qty' => 0,
          'amount' => 0
        );
      }
      $agg_items[$key]['qty'] += 1;
      $agg_items[$key]['amount'] += $unit_price;
    }
  }

  foreach ($agg_items as $agg_item)
  {
    $zebra_class = (($numcount % 2) === 0) ? 'zebra-odd' : 'zebra-even';
  ?>   
    <tr class="item-row <?php echo $zebra_class; ?>">
          <td class="transacdetail item-name"><?php echo $agg_item["ProductName"];?></td>
          <td class="transacdetail amt-cell"><div class="amt"><?php echo number_format($agg_item["unit_price"],2);?></div></td>
          <td class="transacdetail item-qty"><div class="amt"><?php echo number_format($agg_item["qty"],0);?></div></td>
          <td class="transacdetail amt-cell"><div class="amt"><?php echo number_format($agg_item["amount"],2);?></div></td>
      </tr><!--end tr-->
    <?php
    $numcount++;
    $qtyacc += $agg_item["qty"];
    $priceacc=$priceacc+$agg_item["amount"];
    }


          //$price=$priceacc+$row["shipping_fee"]-$row["discount"];
          $price=$priceacc-$row["discount"];                                            
          $pricebeforeVAT=$price/1.07;
          $VAT=$price-$pricebeforeVAT;

          $price_discount_seller = $priceacc-$row["voucher_seller"];

          $price_discount_seller_shipping = $price_discount_seller + $row["shipping_fee"];
          $price_discount_seller_shipping_exclude_vat = $price_discount_seller/1.07;
          $price_discount_seller_shipping_vat = $price_discount_seller - $price_discount_seller_shipping_exclude_vat;

          //$price_discount_seller_shipping = $price_discount_seller + $row["shipping_fee"];
          //$price_discount_seller_shipping_exclude_vat = $price_discount_seller_shipping/1.07;
          //$price_discount_seller_shipping_vat = $price_discount_seller_shipping - $price_discount_seller_shipping_exclude_vat;

    ?>

      <tr>
     
      <td colspan="4" style="line-height:  <?php

$original_height=20;
$original_height=$original_height-$numcount;
echo $original_height;


      ?>em;"><center><span class="bny"></span></center></td>
     </tr>
      <tr class="totals-gap">
        <td colspan="4"></td>
      </tr>
                
    <tr class="qty-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">จำนวนรวม:</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($qtyacc, 0);?></div></td>
      
    </tr>

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">รวมทั้งสิ้น:</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($priceacc,2);?></div></td>
      
    </tr>

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ส่วนลด:</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($row["voucher_seller"],2);?></div></td>
      
    </tr>

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">มูลค่าหลังหักส่วนลด:</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($price_discount_seller,2);?></div></td>
      
    </tr>
    <!--<tr>
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ค่าขนส่ง:</div></td>
      <td class="conclution" ><div style="text-align: right; "><?php echo number_format($row["shipping_fee"],2);?></div></td>
      
    </tr>-->

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">จำนวนเงินภาษีมูลค่าเพิ่ม(Exclude VAT):</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($price_discount_seller_shipping_exclude_vat,2);?></div></td>
      
    </tr>

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ภาษีมูลค่าเพิ่ม (VAT):</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($price_discount_seller_shipping_vat,2);?></div></td>
      
    </tr>

    <tr class="totals-row">
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ยอดรวมทั้งสิ้น (Net Total):</div></td>
      <td class="conclution amt-cell" ><div class="amt"><?php echo number_format($price_discount_seller,2);?></div></td>
      
    </tr>

    <!-- <tr>
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ส่วนลดพิเศษ:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($row["voucher_platform"],2);?></div></td>
      
    </tr>

  <tr>
      
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ยอดชำระ:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($price,2);?></div></td>
      
    </tr>-->

      <tr>
     
      <td colspan="4" ><div style="text-align: center; font-size: 4em; line-height: 2em;"><span class="dateinfo">รวมทั้งสิ้น: <?php  echo number_format($price_discount_seller,2);?></span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="4" ><div style="text-align: center; font-size: 3em; line-height: 1em"><span class="dateinfo">VAT Included</span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="4" ><div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo">lazordernumber: <?php echo $row["order_number"];?></span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="4" ><div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo"></span>
                                                            </div></td>
     
    
    
    </tr>
                                                        
                                                    </tbody>
                                                </table>
                                                <!--end table-->
                                           <?php
                                           }
                                       }
                                           ?>     

    </center>   


       

    </body>
</html>