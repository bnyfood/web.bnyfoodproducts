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
      font-size: 1em;
    }
  </style>   
</head>
<body style="background: #FFF;">
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
      $rows_per_page=42;
      $row_runner=1;  
      $page_runner=0;
      $totalpages=ceil($total_rows/$rows_per_page);
      $page_priceacc=0;
      $page_VATacc=0;
      $page_priceacc_tot=0;
      $page_VATacc_tot=0;

      while($row_runner<$total_rows)
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
      <col style="width:10%">
      <col style="width:15%">
      <col style="width:10%">
      <col style="width:15%">
      <col style="width:10%">
      <col style="width:15%">
      <col style="width:8%">
      <col style="width:2%">
      <thead>
        <tr>
          <th colspan="10" style="text-align: left;">เดือน/ปี ภาษี: 
            <?php
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
            ?> (วันที่จาก <?php echo $start_date_date->format("d/m/Y"); ?> ถึง <?php echo $end_date_date->format("d/m/Y"); ?>) 
          </th>
          <th colspan="2" style="text-align: right;"> วันที่: <?php echo date("Y/m/d");?></th>  
        </tr>
        <tr>
          <th colspan="10"><center><h1>รายงานใบลดหนี้(Tiktok)</h1></center></th>
        </tr>  
        <tr>
          <th colspan="8" class="bny">ชื่อผู้ประกอบการ: บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</th>
          <th colspan="1" style="text-align: right;" class="bny">หน้า: <?php echo $page_runner+1;?></th>
          <th colspan="1" style="text-align: right;" class="bny"></th>
        </tr>  
        <tr>
          <th colspan="10" class="bny">ที่อยู่: 23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ.ภูเก็ต 83110</th>
        </tr>   
        <tr>
          <th colspan="10" class="bny">เลขประจำตัวผู้เสียภาษี: 0835563000306 สำนักงานใหญ่</th>
        </tr> 
      </thead>
    </table>
    <table class="table2  table-striped">
         <col style="width:5%">
        <col style="width:10%">
        <col style="width:10%">
        <col style="width:15%">
        <col style="width:10%">
        <col style="width:15%">
        <col style="width:10%">
        <col style="width:15%">
        <col style="width:8%">
        <col style="width:2%">
        <thead>   
          <tr>
            <th colspan="10" class="tbhead"><hr></th>
          </tr>
          <tr>    
            <th style="text-align:center">ลำดับ</th>
            <th style="text-align:center">วัน/เดือน/ปี</th>
            <th style="text-align:center">เลขที่ใบกำกับภาษี</th>
            <th style="text-align:center">เลขที่ใบลดหนี้</th>
            <th style="text-align:center">ShpOrderNO</th>
            <th style="text-align:center">ชื่อผู้ซื้อสินค้า</th>
            <th style="text-align:center">TaxID</th>
            <th style="text-align:right">Amount(VAT included)</th>
            <th style="text-align:right">VAT</th>
            <th style="text-align:right"></th>
          </tr>
        </thead>
        <?php
          }   //new page
        ?>
        <tbody>                                   
          <tr>
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
            <td class="transacdetail"><?php echo number_format($row["ValueBeforeVAT"],2,".",",");?></td>
            <td class="transacdetail"><?php echo number_format($row["VAT"],2,".",",");?></td>
            <td class="transacdetail"></td>
          </tr>
         <?php
            $page_priceacc=$page_priceacc+$row["ValueBeforeVAT"];
            $page_VATacc=$page_VATacc+$row["VAT"];                                         
            if(fmod($row_runner,$rows_per_page)==0) // new page
            {
              $page_runner++;
            }
              $row_runner++;
          }
        }
          if(($page_runner+1)==$totalpages)
            { ?> 
              <tr>  
                <td  colspan="7" ><div style="text-align: right;">รวมทั้งหมด: </div></td>  
                <td  class="conclution" ><div style="text-align: right;">
          <?php echo number_format($page_priceacc,2,".",","); ?>
                </div></td>
                <td  class="conclution" ><div style="text-align: right;">
          <?php echo number_format($page_VATacc,2,".",","); ?>
                </div></td>
                <td  ></td>
              </tr>
          </tbody>
        </table>
      <?php }}} ?>     
    </center>
  </body>
</html>