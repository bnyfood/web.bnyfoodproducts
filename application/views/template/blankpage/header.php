<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bnyfoodproducts">
    <meta name="author" content="">
    
    <title><?php if(!empty($arr_input['title'])){echo $arr_input['title'];}?></title>

    <link rel="shortcut icon" href="<?php echo base_url();?>resources/images/logo_ico.ico">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css'>
    <link rel="stylesheet" href="<?php echo base_url();?>global/css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo base_url();?>global/css/bootstrap-extend.css">
    <link rel="stylesheet" href="<?php echo base_url();?>resources/theme/css/style_main.css">

    <script>
      window.console = window.console || function(t) {};
    </script>

    <?php   
        if(!empty($arr_css)){
            foreach ($arr_css as $css) { ?>
            <link rel="stylesheet" href="<?php echo $css ; ?>?<?php echo time();?>" type="text/css" />
    <?php 
            }
        }
    ?>

<body translate="no">
<div class='dashboard'>