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
        <script src="<?php echo base_url();?>global/vendor/jquery-placeholder/jquery.placeholder.js"></script>
    
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
        <script src="<?php echo base_url();?>global/js/Plugin/jquery-placeholder.js"></script>
    
    <script>
      (function(document, window, $){
        'use strict';
    
        var Site = window.Site;
        $(document).ready(function(){
          Site.run();
        });
      })(document, window, jQuery);
    </script>
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