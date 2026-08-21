  <?php 
  if(!empty($arr_html)){
      foreach ($arr_html as $html) { ?>
        <div include-html="<?php echo $html; ?>"></div> 
  <?php 
      }
    }
  ?>

  <?php if (!empty($ws_chrome)) { ?>
  </div><!-- bny-ws-page -->
</div><!-- bny-ws -->
  <?php } ?>

</div>
</div>

<!-- In-page <div> modal (Bootstrap) — NOT a new browser window -->
<div class="modal fade" id="session_timeout_modal" tabindex="-1" role="dialog" aria-labelledby="session_timeout_title" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="session_timeout_title">Login expired</h4>
      </div>
      <div class="modal-body">
        <p class="session-timeout-hint">Your login has expired. Sign in with Google to continue on this page.</p>
        <p id="session_relogin_error" class="session-timeout-error text-danger" style="display:none;"></p>
        <button type="button" class="btn btn-primary btn-block" id="session_google_relogin_btn" style="width:100%;">
          Sign in with Google
        </button>
        <details class="session-timeout-fallback" style="margin-top:14px;">
          <summary>Use email / password instead</summary>
          <div class="form-group" style="margin-top:12px;">
            <label for="session_relogin_email">Email</label>
            <input type="email" class="form-control" id="session_relogin_email" autocomplete="username" value="<?php echo htmlspecialchars((string)$this->session->userdata(SESSION_PREFIX.'email'), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="form-group">
            <label for="session_relogin_password">Password</label>
            <input type="password" class="form-control" id="session_relogin_password" autocomplete="current-password">
          </div>
          <button type="button" class="btn btn-default" id="session_relogin_btn">Sign in</button>
        </details>
      </div>
    </div>
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
    <script src="<?php echo base_url();?>resources/js/chat_badge.js?v=<?php echo @filemtime(FCPATH.'resources/js/chat_badge.js') ?: time(); ?>"></script>
    <script src="<?php echo base_url();?>resources/js/alert_badge.js?v=<?php echo @filemtime(FCPATH.'resources/js/alert_badge.js') ?: time(); ?>"></script>
    <script src="<?php echo base_url();?>resources/js/authen_script.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/datepicker/daterange_pages.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/session_keepalive.js?<?php echo time();?>"></script>
    <script src="<?php echo base_url();?>resources/js/workspace.js?v=<?php echo @filemtime(FCPATH.'resources/js/workspace.js') ?: time(); ?>"></script>
    <script src="<?php echo base_url();?>resources/js/nav_scroll_hint.js?v=<?php echo @filemtime(FCPATH.'resources/js/nav_scroll_hint.js') ?: time(); ?>"></script>

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


