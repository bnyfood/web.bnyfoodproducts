<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php if(!empty($arr_input['title'])){echo $arr_input['title'];}?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by Mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.ico">

        <!--Morris Chart CSS -->
       
        <!-- App css -->
        <link href="<?php echo base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/css/style.css" rel="stylesheet" type="text/css" />


             <!-- Plugins css -->
        <link href="<?php echo base_url();?>assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" />
        <link href="<?php echo base_url();?>assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url();?>assets/plugins/timepicker/bootstrap-material-datetimepicker.css" rel="stylesheet">
        
        <?php   
        if(!empty($arr_css)){
            foreach ($arr_css as $css) { ?>
            <link rel="stylesheet" href="<?php echo $css ; ?>?<?php echo time();?>" type="text/css" />
    <?php 
            }
        }
    ?>

        
    </head>
    <body>
        <style>
            .page-wrapper {
             padding-top: 5px !important 
            }
        </style>
    <div class="page-wrapper">    