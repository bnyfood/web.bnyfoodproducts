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

padding: 0.3em;
font-size: 3em;
overflow-wrap: normal;

   }

   .conclution{

padding: 0.025em !important;
line-height: 0.0005em  !important;
font-size:3em;

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
                                 
                                                           <!--Table-->
<!--Table-->
<table >
    <col style="width:5%">
    <col style="width:35%">
    <col style="width:28%">
    <col style="width:10%">
    <col style="width:22%">
<!--Table head-->
  <thead>
    <tr>
      <td scope="row" colspan="5"><center><span class="bny"></span></center></td>
    </tr>
    <tr>
      <td scope="row" colspan="5"><center><span class="bny">บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</span></center></td>
     </tr>
    <tr>
      <td  scope="row" colspan="5"><center><span class="bny">23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ. ภูเก็ต 83110</span></center></td>
    </tr>
    <tr>
      <td  scope="row" colspan="5"><center><span class="bny">สำนักงานใหญ่</span></center></td>
    </tr>
    <tr>
      <td colspan="5"><center><span class="bny">เลขประจำตัวผู้เสียภาษี 0835563000306</span></center></td>
    </tr>
    <tr>
      <td colspan="5" style="height: 5px;"><center><span class="bny"></span></center></td>
    </tr>
    <tr>
      <td colspan="5" class="taxinvoice"><center><span class="taxinvoice">ใบกำกับภาษีอย่างย่อ/ใบเสร็จรับเงิน</span></center></td>
    </tr>
    <tr>
      <td colspan="5" ><center><span class="taxinvoicenumber"><?php echo $row["taxinvoiceID"];?></span></center></td>
    </tr>
    <tr>
     <td colspan="4" >
      <div style="text-align: right; font-size: 2.5em; line-height: 1em;">
        <span class="dateinfo">วันที่: 
          <?php
            $date=date_create($row["created_at"]);
           echo date_format($date,"d/F/Y");
          ?>
         </span>
      </div>
    </td>
    <td ></td>
    </tr>
    <tr>
      <td colspan="5" style="height: 5px;"><center><span class="bny"></span></center></td>
    </tr>
  </thead>
  <!--Table head-->
  <!--Table body-->
    <tbody>
      <tr>
        <td class="transacdetail">qty</td>
        <td  colspan=2 class="transacdetail" style="text-align:center;">รายการ</td>
        <td class="transacdetail">ราคา</td>
        <td class="transacdetail">จำนวนเงิน</td>
      </tr>
      <?php
      $numcount=0;
      $priceacc=0;
      $acc_qty=0;
      foreach($row["suborder"] as $suborder_detail)
      {
      ?>   
        <tr> 
          <td  class="transacdetail" style="line-height: initial;vertical-align: text-top;"><?php echo $suborder_detail["qty"]?></td>
          <td  colspan=2 class="transacdetail" style="line-height: initial; vertical-align: text-top;" ><?php echo $suborder_detail["ProductName"];?> </td>
          <td  class="transacdetail" style="line-height: initial;vertical-align: text-top;"><div style="text-align: right; padding-right:10px;"><?php echo number_format($suborder_detail["item_price"],2);?></div></td>
          <td  class="transacdetail" style="line-height: initial;vertical-align: text-top;"><div style="text-align: right; padding-right:0px;"><?php echo number_format($suborder_detail["amount"],2);?></div></td>
        </tr><!--end tr-->
         <?php
         $numcount++;
         $priceacc=$priceacc+$suborder_detail["amount"];
         $acc_qty += $suborder_detail["qty"];
          }
          $price=$priceacc+$row["shipping_fee"]-$row["discount"];
          $pricebeforeVAT=$price/1.07;
          $VAT=$price-$pricebeforeVAT;
        ?>
        <tr>
     
      <td colspan="5" style="line-height:  <?php
      $original_height=20;
      $original_height=$original_height-$numcount;
      echo $original_height;
      ?>em;"><center><span class="bny"></span></center></td>
     </tr>
    <tr >
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">จำนวนรวม:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo $acc_qty;?></div></td>
    </tr>
    <tr>
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">รวมค่าสินค้า:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($priceacc,2);?></div></td>
    </tr>
    <tr>  
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ค่าขนส่ง:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($row["shipping_fee"],2);?></div></td>
    </tr>
    <tr>
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ส่วนลด:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($row["discount"],2);?></div></td>
    </tr>
    <tr>  
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">จำนวนเงินหลังหักส่วนลด:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($price,2);?></div></td>
    </tr>      
    <tr>
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">VAT:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($VAT,2);?></div></td>
    </tr>  
    <tr>
      <td  colspan=3 class="conclution" ><div style="text-align: right; ">ราคาไม่รวม VAT:</div></td>
      <td  colspan=2 class="conclution" ><div style="text-align: right; "><?php echo number_format($pricebeforeVAT,2);?></div></td>
    </tr>  
    </div></td>
      <td  ></td> 
      </tr>
      <tr>
        <td colspan="5" >
          <div style="text-align: center; font-size: 4em; line-height: 2em;"><span class="dateinfo">รวมทั้งสิ้น: <?php  echo $price;?></span></div>
        </td>
      </tr>
      <tr>
      <td colspan="5" >
        <div style="text-align: center; font-size: 3em; line-height: 1em"><span class="dateinfo">VAT Included</span></div>
      </td>
      </tr>
      <tr>
        <td colspan="5" >
          <div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo">ShopeeOrderNo: <?php echo $row["order_number"];?></span></div>
        </td>
      </tr>
      <tr>
        <td colspan="5" ><div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo"></span></div>
        </td>
      </tr>                                          
      </tbody>
    </table>
    <?php }}?>     
    </center>   
    </body>
</html>