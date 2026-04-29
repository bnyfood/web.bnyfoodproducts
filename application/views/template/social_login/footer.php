<script src="<?php echo base_url();?>global/vendor/jquery/jquery.js"></script>
<script src="<?php echo base_url();?>global/vendor/bootstrap/bootstrap.js"></script>
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
