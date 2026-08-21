<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
            <!-- Page Content-->
            <div class="page-content">

                <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="float-right">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Metrica</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Projects</a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>
                                <h4 class="page-title">TaxInvoice</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
       
                   
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="card">                                
                                <div class="card-body"><form name="searchform">
                                    <h4 class="mt-0 mb-3 header-title">ค้นหาใบกำกับภาษี</h4>TaxinvoiceType: <input type="radio" name="taxinvoicetype" value=1 checked="checked"> All type <input type="radio" name="taxinvoicetype" value=2> ABB  <input type="radio" name="taxinvoicetype" value=3> Full Taxinvoice                 


                                    <div class="table-responsive">
                                        <table class="table mb-0 table-centered">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <select name="platform" id="platform">
                                                        <option value="0">Lazada
                                                        <option value="1">Shopee
                                                        <option value="2">Biggrill
                                                        <option value="3">BigSauces

                                                     </select>



                                                </th>
                                                <th><input type="text" name="order_number" placeholder="Order Number" id="ordernumber"></th>
                                                <th><div class="input-group">                                            
                                        <input type="text" class="form-control" name="daterange" id="daterange">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                        </div>
                                    </div></th>
                                                <th><input type="button" value="พิมพ์ใบกำกับภาษีอย่างย่อ" id="search">
                                                    &nbsp;
                                                    <input type="button" value="ออกใบกำกับภาษีเต็มรูป" id="ftaxinv"></th>
                                                
                                            </tr>
                                            </thead>
                                           
                                        </table><!--end /table-->
                                    </form>
                                    </div>
                                    <div id="theresault">
                                    </div>

                                   

                                    
                                </div><!--end card-body-->                                                                                                        
                            </div><!--end card-->
                        </div><!--end col-->
                        
                    </div><!--end row-->

                    

                </div><!-- container -->
                <div id="return_div">
                    
<?php
if(isset($orders))
{
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
            //echo "numofpage:".$numofpage;
            ?>
<nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-end">
                                          <?php
                                          if($numofpage>1 && $page!=1)
                                          {
                                           ?>
                                            <li class="page-item ">
                                                <a class="page-link urlref" href="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                                            </li>

                                            <?php
                                            }
                                            for($p=1;$p<=$numofpage;$p++)
                                            {
                                            ?>
                                            <li class="page-item"><a class="page-link urlref" href="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=<?php echo $p;?>"><?php if($page==$p)
                                            {
                                            ?>
                                            <b>
                                             <?php
                                             }
                                             ?>   
                                                <?php echo $p;?>
                                             <?php if($page==$p)
                                            {
                                            ?>
                                        </b>
                                        <?php
                                    }
                                    ?>

                                            </a></li>
                                            <?php
                                            }
                                            ?>

                                            <?php
                                          if($numofpage>1 && $page!=$numofpage)
                                          {
                                           ?>
                                            <li class="page-item">
                                                <a class="page-link urlref" href="/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype=<?php echo $taxinvoicetype;?>&platform=<?php echo $platform;?>&ordernumber=<?php echo $ordernumber;?>&daterange=<?php echo $daterange;?>&page=<?php echo $page+1;?>">Next</a>
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
   
<?php
}
?>
                </div>

                <footer class="footer text-center text-sm-left">
                    &copy; 2019 Metrica <span class="text-muted d-none d-sm-inline-block float-right">Crafted with <i class="mdi mdi-heart text-danger"></i> by Mannatthemes</span>
                </footer><!--end footer-->
            </div>
            <!-- end page content -->
        