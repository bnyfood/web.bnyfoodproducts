
<footer class="site-footer">
  <div class="site-footer-legal">© 2021 <a href="http://themeforest.net/item/remark-responsive-bootstrap-admin-template/11989202">Remark</a></div>
  <div class="site-footer-right">
    Crafted with <i class="red-600 wb wb-heart"></i> by <a href="https://themeforest.net/user/creation-studio">Creation Studio</a>
  </div>
</footer>
    <!-- Core  -->
    <script src="<?php echo base_url();?>global/vendor/babel-external-helpers/babel-external-helpers.js"></script>
    <script src="<?php echo base_url();?>global/vendor/jquery/jquery.js"></script>
    <script src="<?php echo base_url();?>global/vendor/popper-js/umd/popper.min.js"></script>
    <script src="<?php echo base_url();?>global/vendor/bootstrap/bootstrap.js"></script>
    <script src="<?php echo base_url();?>global/vendor/animsition/animsition.js"></script>
    <script src="<?php echo base_url();?>global/vendor/mousewheel/jquery.mousewheel.js"></script>
    <script src="<?php echo base_url();?>global/vendor/asscrollbar/jquery-asScrollbar.js"></script>
    <script src="<?php echo base_url();?>global/vendor/asscrollable/jquery-asScrollable.js"></script>
    
    <!-- Plugins -->
    <script src="<?php echo base_url();?>global/vendor/jquery-mmenu/jquery.mmenu.min.all.js"></script>
    <script src="<?php echo base_url();?>global/vendor/switchery/switchery.js"></script>
    <script src="<?php echo base_url();?>global/vendor/intro-js/intro.js"></script>
    <script src="<?php echo base_url();?>global/vendor/screenfull/screenfull.js"></script>
    <script src="<?php echo base_url();?>global/vendor/slidepanel/jquery-slidePanel.js"></script>
    <script src="<?php echo base_url();?>global/vendor/skycons/skycons.js"></script>
   <!-- <script src="<?php echo base_url();?>global/vendor/chartist/chartist.min.js"></script>
    <script src="<?php echo base_url();?>global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.js"></script>
    <script src="<?php echo base_url();?>global/vendor/aspieprogress/jquery-asPieProgress.min.js"></script>
-->
    
    <script src="<?php echo base_url();?>global/vendor/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="<?php echo base_url();?>global/vendor/jvectormap/maps/jquery-jvectormap-au-mill-en.js"></script>
    <script src="<?php echo base_url();?>global/vendor/matchheight/jquery.matchHeight-min.js"></script>
    
    <!-- Scripts -->
    <script src="<?php echo base_url();?>global/js/Component.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin.js"></script>
    <script src="<?php echo base_url();?>global/js/Base.js"></script>
    <script src="<?php echo base_url();?>global/js/Config.js"></script>
    
    <script src="<?php echo base_url();?>assets/js/Section/Menubar.js"></script>
    <script src="<?php echo base_url();?>assets/js/Section/Sidebar.js"></script>
    <script src="<?php echo base_url();?>assets/js/Section/PageAside.js"></script>
    <script src="<?php echo base_url();?>assets/js/Section/GridMenu.js"></script>
    
    <!-- Config -->
    <script src="<?php echo base_url();?>global/js/config/colors.js"></script>
    <script src="<?php echo base_url();?>assets/js/config/tour.js"></script>
    <script>Config.set('assets', '<?php echo base_url();?>assets');</script>
    
    <!-- Page -->
    <script src="<?php echo base_url();?>assets/js/Site.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin/asscrollable.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin/slidepanel.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin/switchery.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin/matchheight.js"></script>
    <script src="<?php echo base_url();?>global/js/Plugin/jvectormap.js"></script>

    <script src="<?php echo base_url();?>global/js/Plugin/table.js"></script>

    <script src="<?php echo base_url();?>resources/js/path_script.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>assets/examples/js/dashboard/team.js"></script>
    <script src="<?php echo base_url();?>resources/js/modal_script.js?<?php echo time();?>"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
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


