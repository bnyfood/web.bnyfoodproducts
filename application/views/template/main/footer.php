<?php 
  if(!empty($arr_html)){
      foreach ($arr_html as $html) { ?>
        <div include-html="<?php echo $html; ?>"></div> 
  <?php 
      }
    }
  ?>

</div>
</div>
    <script src='<?php echo base_url();?>resources/theme/js/jquery_3_5_1.js'></script>
    <script src="<?php echo base_url();?>global/vendor/bootstrap/bootstrap.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
    <script src='<?php echo base_url();?>resources/js/datepicker/daterangepicker.min.js'></script>
    <script src="<?php echo base_url();?>resources/js/modal_script.js?<?php echo time();?>"></script>
    
    <script id="rendered-js" >
        const mobileScreen = window.matchMedia("(max-width: 990px )");
        $(document).ready(function () {
          
          $(".dashboard-nav-dropdown-toggle").click(function () {
            $(this).closest(".dashboard-nav-dropdown").
            toggleClass("show").
            find(".dashboard-nav-dropdown").
            removeClass("show");
            $(this).parent().
            siblings().
            removeClass("show");

          });
          $(".menu-toggle").click(function () {
            if (mobileScreen.matches) {
              $(".dashboard-nav").toggleClass("mobile-show");
            } else {
              $(".dashboard").toggleClass("dashboard-compact");
            }
            
          });

        });
        //# sourceURL=pen.js
    </script>

  <script src="<?php echo base_url();?>resources/js/modal_script.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/path_script.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/authen_script.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/datepicker/daterange_pages.js?<?php echo time();?>"></script>

   <!-- <script src="<?php echo base_url();?>resources/js/google/recaptcha.js?<?php echo time();?>"></script> -->

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


