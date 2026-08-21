<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    
    <title><?php if(!empty($arr_input['title'])){echo $arr_input['title'];}?></title>
    
    <link rel="apple-touch-icon" href="<?php echo base_url();?>assets/images/apple-touch-icon.png">
    <link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.ico">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo base_url();?>global/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/css/bootstrap-extend.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/site.min.css">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/animsition/animsition.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/asscrollable/asScrollable.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/switchery/switchery.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/intro-js/introjs.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/slidepanel/slidePanel.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/jquery-mmenu/jquery-mmenu.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/flag-icon-css/flag-icon.css">
        <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/chartist/chartist.css">
        <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/jvectormap/jquery-jvectormap.css">
        <link rel="stylesheet" href="<?php echo base_url();?>global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css">
        <link rel="stylesheet" href="<?php echo base_url();?>assets/examples/css/dashboard/v1.css">
    
    
    <!-- Fonts -->
        <link rel="stylesheet" href="<?php echo base_url();?>global/fonts/weather-icons/weather-icons.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/fonts/web-icons/web-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/fonts/brand-icons/brand-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resources/css/fonts/Roboto.css">

    <link rel="stylesheet" href="<?php echo base_url();?>resources/css/style_addon.css">
    
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>global/vendor/html5shiv/html5shiv.min.js"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="<?php echo base_url();?>global/vendor/media-match/media.match.min.js"></script>
    <script src="<?php echo base_url();?>global/vendor/respond/respond.min.js"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="<?php echo base_url();?>global/vendor/breakpoints/breakpoints.js"></script>
    <script>
      Breakpoints();
    </script>

    <?php   
        if(!empty($arr_css)){
            foreach ($arr_css as $css) { ?>
            <link rel="stylesheet" href="<?php echo $css ; ?>?<?php echo time();?>" type="text/css" />
    <?php 
            }
        }
    ?>

  </head>

    <body class="animsition site-navbar-small app-documents">
        