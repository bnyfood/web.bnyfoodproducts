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
        <link rel="stylesheet" href="/assets/plugins/morris/morris.css">

        <!-- App css -->
        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />

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
                                <h4 class="page-title">DataSyncronization</h4>
                            </div><!--end page-title-box-->
                        </div><!--end col-->
                    </div>
                    <!-- end page title end breadcrumb -->
                    
       
                   
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="card">                                
                                <div class="card-body">
                                    <h4 class="mt-0 mb-3 header-title">การดึงข้อมูลล่าสุด</h4>           
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Lazada:</label><?php echo $lazada;?> คิดเป็น <?php echo $lazpass;?> <button type="button" class="btn btn-primary" id="sync_lazada">ดึงข้อมูล</button>
                                                                                     
                                        </div><!--end col-->

                                    </div> <!--end row-->

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Shopee:</label><?php echo $shopee;?> คิดเป็น <?php echo $shopeepass;?> <button type="button" class="btn btn-primary" id="lazSync">ดึงข้อมูล</button>
                                                                                     
                                        </div><!--end col-->

                                    </div> <!--end row-->
                                </div><!--end card-body-->                                                                                                        
                            </div><!--end card-->
                        </div><!--end col-->
                        
                    </div><!--end row-->

                    

                </div><!-- container -->
                <div id="return_div"></div>

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
        <script src="/assets/plugins/morris/morris.min.js"></script>
        <script src="/assets/plugins/raphael/raphael.min.js"></script>
        <script src="/assets/plugins/moment/moment.js"></script>
        <script src="/assets/plugins/apexcharts/apexcharts.min.js"></script>


        <script src="/assets/pages/jquery.projects_dashboard.init.js"></script>

        <!-- App js -->
        <script src="/assets/js/app.js"></script>

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
        data: {startdate: startdateval},
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
 


$('#sync_lazada').click(function(){
   getlatestrecorddate();
});


});



        </script>

    </body>
</html>