<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Metrica - Responsive Bootstrap 4 Admin Dashboard</title>
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
    border-collapse: separate;


    }

td { 
    padding: 0.1em;
    font-size: 1em;
    height: 3em;
}
tr { line-height:.01em; }

    body {
        padding-top:0.5em;
    }

    .bny{

   font-size: 0.8em;
   line-height: 1.2em;

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

padding: 0px;
font-size: 0.8em;
text-align: right;

   }

   .conclution{

padding: 1em !important;
line-height: 0.5em  !important;
font-size: 0.85em;

   }


</style>
        
    </head>

    <body style="background: #FFF;">

<center>

             
                                           
<?php  
if(empty($orders_tracks))
{?>
<center>ไม่พบข้อมูล กรุึณาระบุช่วงเวลาให้ถูกต้อง</center>
<?php 
}
else
{
  $total_rows= count($orders_tracks);                                                  
  $rows_per_page=50;
  $row_runner=1;  
  $page_runner=0;
  $totalpages=ceil($total_rows/$rows_per_page);
  $page_priceacc=0;
  $page_VATacc=0;

  $page_priceacc_tot=0;
  $page_VATacc_tot=0;

  $page_priceacc=0;
   $page_voucher_seller=0;
   $page_voucher_platform=0;
   $page_voucher=0;
   $page_VATacc=0;
   $page_priceVATincluded=0;
   $page_priceBeforeVAT=0;
   $page_shipping_fee_acc = 0;


    while($row_runner<$total_rows)
    {

   


    foreach($orders_tracks as $row)
    {
                               
 if(fmod($row_runner-1,$rows_per_page)==0 || $row_runner==1) // new page
 {

  ?>
                                                           <!--Table-->
<!--Table-->

<table class="table1 table-borderless"">
    <col style="width:5%">
    <col style="width:10%">
    <col style="width:20%">
    <col style="width:20%">
    <col style="width:15%">
    <col style="width:10%">
    <col style="width:15%">
    <col style="width:5%">

<!--Table head-->
  <thead>
    <tr>
        <th colspan="6" style="text-align: left;">รายงานส่งของ วันที่ : <?php echo $datesearch;?></th>
     <th colspan="2" style="text-align: right;"> 
     
     </th>  
</tr>
<tr>

<th colspan="8"><center><h1>รายงานภาษีขาย(Lazada)</h1></center></th>
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
<table class="table2  table-striped">
    <col style="width:4%">
    <col style="width:10%">
    <col style="width:10%">
    <col style="width:10%">
    <col style="width:15%">
    <col style="width:15%">
    <col style="width:10%">
    <col style="width:7%">

    <!--<col style="width:7%">
    <col style="width:7%">-->

    <col style="width:7%">
    <col style="width:7%">
    <col style="width:7%">
    <col style="width:7%">
    <col style="width:8%">


<!--Table head-->
<thead>   
<tr>
<th colspan="8" class="tbhead"><hr></th>
</tr>  
    <tr>    
    <th style="text-align:center">ลำดับ</th>
    <th style="text-align:center">วัน/เดือน/ปี</th>
    <th style="text-align:center">เลขที่ใบกำกับภาษี</th>
    <th style="text-align:center">LazOrderNumber</th>
    <th style="text-align:center">Tacking No.</th>
    <th style="text-align:center;line-height: initial;">ชื่อผู้ซื้อสินค้า<br>ผู้รับบริการ</th>
    <th style="text-align:center;line-height: initial;">เลขประจำตัว<br>ผู้เสียภาษี</th>
    <th style="text-align:right;line-height: initial;">มูลค่า<br>สินค้า</th>

    <!--<th style="text-align:right;line-height: initial;">ส่วนลด<br>ร้านค้า</th>
    <th style="text-align:right;line-height: initial;">ส่วนลด<br>Lazada</th>-->

    <th style="text-align:right;line-height: initial;">ส่วนลด<br>รวม</th>
    <th style="text-align:right;line-height: initial;">ค่า<br>ขนส่ง</th>
    <th style="text-align:right;line-height: initial;">ราคารวม<br>VAT</th>
    <th style="text-align:right">VAT</th>
    <th style="text-align:right;line-height: initial;">ราคาไม่รวม<br>VAT</th>
</tr>
</thead>
                                                    <?php
                                                    }   //new page
                                                    ?>
<tbody>                                       
   <tr>
       <td class="transacdetail" style="text-align:center"><center>
        <?php echo $row_runner;?>
          <input type="checkbox" name="order_number_correct[]" id="order_number_correct"  value="<?php echo $row['order_number']?>" <?php if (in_array($row['order_number'], $arr_chk_correct)){echo "checked";}?>>
        </center></td>
       <td class="transacdetail" style="text-align:center"><?php echo $row["transactiondate"];?> </td>
       <td class="transacdetail" style="text-align:center"><?php echo $row["start_inv"];?></td>
       <td class="transacdetail" style="text-align:center"><?php echo $row["order_number"];?></td>
       <td class="transacdetail" style="text-align:center"><?php echo $row["tracking_number"];?></td>
       <td class="transacdetail" style="text-align:center" ><?php if(!empty($row['start_tiv'])){echo $row["cus_name"];}else{echo "-";}?></td>
       <td class="transacdetail" style="text-align:center"><?php if(!empty($row['TaxNo'])){echo $row["TaxNo"];}else{echo "-";}?></td>
       <td class="transacdetail"><?php echo number_format($row["price"],2,".",",");?></td>

       <!--<td class="transacdetail"><?php echo number_format($row["voucher_seller"],2,".",",");?></td>
       <td class="transacdetail"><?php echo number_format($row["voucher_platform"],2,".",",");?></td>-->

       <td class="transacdetail"><?php echo number_format($row["voucher"],2,".",",");?></td>
       <td class="transacdetail"><?php echo number_format($row["shipping_fee"],2,".",",");?></td>
       <td class="transacdetail"><?php echo number_format($row["priceVATincluded"],2,".",",");?></td>
       <td class="transacdetail"><?php echo number_format($row["VAT"],2,".",",");?></td>
       <td class="transacdetail"><?php echo number_format($row["priceBeforeVAT"],2,".",",");?></td>
       <td class="transacdetail"></td>                       
    </tr><!--end tr-->
   <?php
   
   $page_priceacc=$page_priceacc+$row["price"];
   $page_voucher_seller=$page_voucher_seller+$row["voucher_seller"];
   $page_voucher_platform=$page_voucher_platform+$row["voucher_platform"];
   $page_voucher=$page_voucher+$row["voucher"];
   $page_VATacc=$page_VATacc+$row["VAT"];
   $page_priceVATincluded=$page_priceVATincluded+$row["priceVATincluded"];
   $page_priceBeforeVAT=$page_priceBeforeVAT+$row["priceBeforeVAT"];
   $page_shipping_fee_acc=$page_shipping_fee_acc+$row["shipping_fee"];

        if(fmod($row_runner,$rows_per_page)==0) // new page
        {
      
       $page_runner++;
      
        }
       $row_runner++;

 // echo "page_runner:".$page_runner;
 // echo "totalpages:".$totalpages;

                                           }
                                       }


if(($page_runner+1)==$totalpages)
       {
       ?> 
      <tr>  
      <td  colspan="6" ><div style="text-align: right;">รวมทั้งหมด: </div></td>  
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_priceacc,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher_seller,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher_platform,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_voucher,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_shipping_fee_acc,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_priceVATincluded,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_VATacc,2,".",","); ?></div></td>
      <td  class="conclution" ><div style="text-align: right;"><?php echo number_format($page_priceBeforeVAT,2,".",","); ?></div></td>
      <td  ></td>
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

<script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url();?>resources/js/account/transaction_chk.js?<?php echo time();?>"></script>