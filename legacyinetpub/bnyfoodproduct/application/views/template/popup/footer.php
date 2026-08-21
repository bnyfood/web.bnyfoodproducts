</div>
        <!-- end page-wrapper -->

        <!-- jQuery  -->
        <script src="<?php echo base_url();?>assets/js/jquery.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/metisMenu.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/waves.min.js"></script>
        <script src="<?php echo base_url();?>assets/js/jquery.slimscroll.min.js"></script>

        <!--Plugins-->
        
        <script src="<?php echo base_url();?>assets/plugins/raphael/raphael.min.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/moment/moment.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/apexcharts/apexcharts.min.js"></script>


        

        <!-- App js -->
        <script src="<?php echo base_url();?>assets/js/app.js"></script>



        
        
        <!-- Plugins js -->
        <script src="<?php echo base_url();?>assets/plugins/moment/moment.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/select2/select2.min.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/timepicker/bootstrap-material-datetimepicker.js"></script>
        <script src="<?php echo base_url();?>assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="<?php echo base_url();?>assets/pages/jquery.forms-advanced.js"></script>

        
        <?php 

if(!empty($arr_js)){
    foreach ($arr_js as $js) { ?>
      <script type="text/javascript" src="<?php echo $js; ?>?<?php echo time();?>"></script>
<?php 
    }
  }
?>
    </body>
</html>