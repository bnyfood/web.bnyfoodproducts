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
        border: 2px solid black;
        border-spacing: 0px;
    border-collapse: separate;
    margin-top: 5em;
    margin-bottom: 5em;


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

   font-size: 3.5em;
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
font-size: 3em;

   }

   .conclution{

padding: 1em !important;
line-height: 0.5em  !important;
font-size: 3em;

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
    <col style="width:10%">
    <col style="width:80%">
    <col style="width:10%">
<!--Table head-->
  <thead>
   <tr>
     
      <td scope="row" colspan="3"><center><span class="bny"></span></center></td>
     </tr>
    <tr>
     
      <td scope="row" colspan="3"><center><span class="bny">บริษัท บีเอ็นวายฟู้ด โพรดักส์ จำกัด</span></center></td>
     </tr>
    <tr>
     
      <td  scope="row" colspan="3"><center><span class="bny">23/1 หมู่ 2 ต.ศรีสุนทร อ.ถลาง จ. ภูเก็ต 83110</span>
                                                                    
                                                            </center></td>
     
    
    
    </tr>
     <tr>
     
      <td  scope="row" colspan="3"><center><span class="bny">สำนักงานใหญ่</span>
                                                                    
                                                            </center></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3"><center><span class="bny">เลขประจำตัวผู้่เสียภาษี 0835563000306</span>
                                                                    
                                                            </center></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3" style="height: 5px;"><center><span class="bny"></span></center></td>
     </tr>
    <tr>
     
      <td colspan="3" class="taxinvoice"><center><span class="taxinvoice">ใบกำกับภาษีอย่างย่อ</span>
                                                            </center></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3" ><center><span class="taxinvoicenumber"><?php echo $row["taxinvoiceID"];?></span>
                                                            </center></td>
     
    
    
    </tr>
     <tr>
     
      <td colspan="3" ><div style="text-align: right; font-size: 2.5em; line-height: 1em;"><span class="dateinfo">วันที่: <?php

$date=date_create($row["created_at"]);
echo date_format($date,"d/F/Y");

       ?></span>
                                                            </div></td>
     
    
    
    </tr>
    
    <tr>
     
      <td colspan="3" style="height: 5px;"><center><span class="bny"></span></center></td>
     </tr>
  </thead>
  <!--Table head-->
  <!--Table body--><tbody>
                                                    <?php
                                                    $numcount=0;
                                                    $priceacc=0;
                                                    foreach($row["suborder"] as $suborder_detail)
                                                    {
                                                    ?>   
                                                     <tr>
                                                        <td   class="transacdetail"><center>1</center></td>
                                                            
                                                            <td  class="transacdetail"><?php echo $suborder_detail["ProductName"];?> </td>
                                                            <td class="transacdetail"><?php echo $suborder_detail["price"];?></td>
                                                            
                                                            
                                                        </tr><!--end tr-->
                                                     <?php
                                                     $numcount++;
                                                     $priceacc=$priceacc+$suborder_detail["price"];
                                                      }
                                                      ?>
                                                      <tr>
     
      <td colspan="3" style="line-height:  <?php

$original_height=20;
$original_height=$original_height-$numcount;
echo $original_height;


      ?>em;"><center><span class="bny"></span></center></td>
     </tr>
     
                
      <tr>
        <td  ></td> 
      <td  colspan=3 class="conclution" ><div style="text-align: right; line-height: 0.7em;">
        จำนวนรวม: <?php echo $numcount;?>
        <br><br>รวมค่าสินค้า:<?php echo $priceacc;?>
          <br><br>ค่าขนส่ง:<?php echo $row["shipping_fee"];?>
          <br><br>ส่วนลด:<?php echo $row["discount"];?>
          <br><br>จำนวนเงินหลังหักส่วนลด:<?php

          $price=$priceacc+$row["shipping_fee"]-$row["discount"];
          $pricebeforeVAT=$price/1.07;
          $VAT=$price-$pricebeforeVAT;

            echo $price;?>
          <br><br>VAT:<?php echo round($VAT,2);?>
          <br><br>ราคาไม่รวม VAT:<?php echo round($pricebeforeVAT,2);?>
          


      </div></td>
      <td  ></td> 
      </tr>
      <tr>
     
      <td colspan="3" ><div style="text-align: center; font-size: 4em; line-height: 2em;"><span class="dateinfo">รวมทั้งสิ้น: <?php  echo $price;?></span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3" ><div style="text-align: center; font-size: 3em; line-height: 1em"><span class="dateinfo">VAT Included</span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3" ><div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo">lazordernumber: <?php echo $row["order_number"];?></span>
                                                            </div></td>
     
    
    
    </tr>
    <tr>
     
      <td colspan="3" ><div style="text-align: center; font-size: 3em; line-height: 1em;"><span class="dateinfo"></span>
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