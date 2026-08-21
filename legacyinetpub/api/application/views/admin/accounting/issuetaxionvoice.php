<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Metrica - Responsive Bootstrap 4 Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.ico">

        <!--Morris Chart CSS -->
       

        <!-- App css -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />


             <!-- Plugins css -->
        <link href="/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" />
        <link href="/assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/plugins/timepicker/bootstrap-material-datetimepicker.css" rel="stylesheet">
        
        

        
    </head>

    <body>

        <!-- Top Bar Start -->
        <div class="topbar">

            <!-- LOGO -->
            <div class="topbar-left">
                <a href="/admin" class="logo">
                    <span>
                        <img src="/assets/images/logo-sm.png" alt="logo-small" class="logo-sm">
                    </span>
                    <span>
                        <img src="/assets/images/logo-dark.png" alt="logo-large" class="logo-lg">
                    </span>
                </a>
            </div>
            <!--end logo-->
            <!-- Navbar -->
           <?php
            $this->load->view('admin/nav/top-nav');
            ?>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->

        <div class="page-wrapper">
            <!-- Left Sidenav -->
            <?php
            $this->load->view('admin/nav/left-sidenav');
            ?>
            <!-- end left-sidenav-->

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
        </div>
        <!-- end page-wrapper -->

        <!-- jQuery  -->
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/js/metisMenu.min.js"></script>
        <script src="/assets/js/waves.min.js"></script>
        <script src="/assets/js/jquery.slimscroll.min.js"></script>

        <!--Plugins-->
        
        <script src="/assets/plugins/raphael/raphael.min.js"></script>
        <script src="/assets/plugins/moment/moment.js"></script>
        <script src="/assets/plugins/apexcharts/apexcharts.min.js"></script>


        

        <!-- App js -->
        <script src="/assets/js/app.js"></script>



        
        
        <!-- Plugins js -->
        <script src="/assets/plugins/moment/moment.js"></script>
        <script src="/assets/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="/assets/plugins/select2/select2.min.js"></script>
        <script src="/assets/plugins/timepicker/bootstrap-material-datetimepicker.js"></script>
        <script src="/assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="/assets/pages/jquery.forms-advanced.js"></script>

        





        <script>
var call_couinter=0;
function getlatestrecorddate()
{

    alert("hello");
    $.ajax({ 
        url: 'https://www.bnyfoodproducts.com/lazcallback/getlatestrecorddate',
        cache: false,
        success: function(response)
        {
            
             $("#res").html(response);
            if(response=="")
            {

              alert("never download");
            }
            else
            {
              alert("use to download:"+response);
            }
         getorders(response);   
        }
  });



}


function  getorders(startdateval){     
$.ajax({ 
        url: 'https://www.bnyfoodproducts.com/lazcallback/getorders',
        cache: false,            
        type : "GET",            
        data: {startdate: $("#ordernumber").val()},
        dataType: 'json',
        success: function(response)
        {
 $("#resault").HTML(response);
              call_couinter++;
            if(response!="done")
            {
                  if(call_couinter<=2)
                  {
                    alert("will download: "+call_couinter+":"+response);
                   getorders(response); 
                   }
            }
        else
        {
            $("#return_div").HTML("DONE");
            
        }
        }
  });
}

$( document ).ready(function() {

<?php
if(isset($orders))
{
 ?> 
$("#platform").val(<?php echo $platform;?>);
$("#ordernumber").val('<?php echo $ordernumber;?>');
  var daterange='<?php echo $daterange;?>';
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sl", "/");
   daterange = daterange.replace("sp", " ");
   daterange = daterange.replace("sp", " ");
   daterange = daterange.replace("hp", "-");

$("#daterange").val(daterange);
<?php
}
?>



 
$("#resault").click(function(){
    var daterange=$("#daterange").val();
  
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace("-", "hp");

var formdata = { platform: $("#platform").val(), ordernumber: $("#ordernumber").val(), daterange: daterange };
alert(formdata);
$.ajax({ 
      method:'POST',
      contentType:'application/json',
      url:'https://www.bnyfoodproducts.com/admin/accounting/taxinvoice/searchtoissue',

      data: JSON.stringify(formdata),
      success:function(response){
      $("#resault").HTML(response);
      }

});
});



$('#search').click(function(){
   var w=1200;
   var h=800;
   var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  var platform=$("select#platform").val();
  var ordernumber=$("#ordernumber").val();
  if(ordernumber=="")
  {
   ordernumber="none"; 
  }
  var daterange=$("#daterange").val();
  
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace("-", "hp");
  

  return window.open('http://www.bnyfoodproducts.com/admin/accounting/taxinvoice/loadpages/'+platform+'/'+ordernumber+'/'+daterange, '', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  return false;
});


$('#ftaxinv').click(function(){


    var daterange=$("#daterange").val();
  
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace("/", "sl");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace(" ", "sp");
    daterange = daterange.replace("-", "hp");
   
//alert($('input[name="taxinvoicetype"]:checked').val());

//================
if($("#ordernumber").val()=="")
{
    

}
else
{
$("#daterange").val();


}


var formdata = {taxinvoicetype:$('input[name="taxinvoicetype"]:checked').val(),platform:$("select#platform").val(),ordernumber:$("#ordernumber").val(),daterange:daterange};

var url='https://www.bnyfoodproducts.com/admin/accounting/taxinvoice/getordersbyplatformordernumberdaterange?taxinvoicetype='+$('input[name="taxinvoicetype"]:checked').val()+'&platform='+$("select#platform").val()+'&ordernumber='+$("#ordernumber").val()+'&daterange='+daterange+'&page=1';

//alert(url);
 //return window.open(url, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  //return false;

window.location.href = url;

//================



});






});

function loadbyurl(url)
{


    alert(url);
}

        </script>

    </body>
</html>