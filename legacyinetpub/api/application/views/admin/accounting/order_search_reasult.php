<?php
defined('BASEPATH') OR exit('No direct script access allowed');


                                    ?>
<style>

tr:nth-child(even) {
  background-color: #f2f2f2;
}
</style>                                 
                                                           <!--Table-->
<!--Table-->
<table >
    <col style="width:3%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:9%">
    <col style="width:7%">
<!--Table head-->
  <thead>
   <tr>
     <td scope="row" ><center><span class="bny">No</span></center></td>
      <td scope="row" ><center><span class="bny">LazOrderNumber</span></center></td>
      <td scope="row" ><center><span class="bny">TaxInvoice(ABB)</span></center></td>
      <td scope="row" ><center><span class="bny">TaxInvoice</span></center></td>
      <td scope="row" ><center><span class="bny">OrderDate</span></center></td>
      <td scope="row" ><center><span class="bny">Shipping Fee</span></center></td>
      <td scope="row" ><center><span class="bny">Voucher</span></center></td>
      <td scope="row" ><center><span class="bny">Amount</span></center></td>
      <td scope="row" ><center><span class="bny">WantTaxinvoice</span></center></td>
      <td scope="row" ><center><span class="bny">Status</span></center></td>
      <td scope="row" ><center><span class="bny">Action</span></center></td>
     </tr>
        </tr>
  </thead>
  <tbody>
     <?php
     $row_runer=1;
     $totalrows=0;
     foreach($orders as $row)
     {
      $totalrows=$row["TotalRows"];
      ?>
    <tr>
     
      <td scope="row" ><center><span class="bny"><?php echo $row_runer;?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["order_number"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["taxinvoiceID"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php $toecho=(!isset($row["taxinvoice"])?"-":$row["taxinvoice"]);
      echo $toecho;
      ?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php $datearr=explode(" ",$row["created_at"]); echo $datearr[0];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["shipping_fee"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["voucher"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["price"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["want_taxinvoice"];?></span></center></td>
      <td scope="row" ><center><span class="bny"><?php echo $row["status"];?></span></center></td>
      <td scope="row" ><center><?php
      if($toecho=="-")
      {
        ?>
        <a href="/admin/accounting/issuetaxionvoice/<?php echo $row["order_number"];?>">ออกใบกำกับเต็มรูป</a>
        <?php
       }else
       {
        ?>
        <a href="/admin/accounting/edittaxinvoice/<?php echo $row["order_number"];?>">แก้ไขใบกำกับ</a>
        <?php
       }
         ?>


      </center></td>

     </tr>
    
     <?php
     $row_runer+=1;
      }
      ?>
     <tr>
      <td scope="row" colspan="11">
        <?php
        if($totalrows>0)
          {
            $numofpage=ceil($totalrows/PAGINATION_SIZE);
            echo "numofpage:".$numofpage;
            ?>
<nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-end">
                                          <?php
                                          if($numofpage>1 && $page!=1)
                                          {
                                           ?>
                                            <li class="page-item disabled">
                                                <a class="page-link urlref" href="javascript:loadbyurl('a0')", value="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=1" tabindex="-1">Previous</a>
                                            </li>

                                            <?php
                                            }
                                            for($p=1;$p<=$numofpage;$p++)
                                            {
                                            ?>
                                            <li class="page-item"><a class="page-link urlref" href="javascript:loadbyurl('a<?php echo $p;?>')" value="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=<?php echo $p;?>"><?php echo $p;?></a></li>
                                            <?php
                                            }
                                            ?>

                                            <?php
                                          if($numofpage>1 && $page!=$numofpage)
                                          {
                                           ?>
                                            <li class="page-item">
                                                <a class="page-link urlref" href="javascript:loadbyurl('bb')" value="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=<?php echo $page+1;?>">Next</a>
                                            </li>
                                            <?php
                                             }
                                             ?>

                                        </ul><!--end pagination-->
                                    </nav>
             <?php
             }
             ?>

      </td>
     </tr> 
    </tbody>
     </tbody>
                                                </table>
                                                
    