<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php if(!empty($arr_input['title'])){ echo $arr_input['title']; } ?></title>

  <link rel="shortcut icon" href="<?php echo base_url();?>assets/images/favicon.ico">
  <link rel="stylesheet" href="<?php echo base_url();?>global/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>resources/css/fonts/Roboto.css">

  <style>
    body {
      margin: 0;
      font-family: "Roboto", Arial, sans-serif;
      background: #ffffff;
      min-height: 100vh;
      color: #1f1f1f;
    }
    .social-login-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .social-login-card {
      width: 100%;
      max-width: 430px;
      background: #ffffff;
      border: 1px solid #e8e8e8;
      border-radius: 18px;
      padding: 28px 24px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    .logo-wrap {
      text-align: center;
      margin-bottom: 20px;
    }
    .logo-wrap img {
      width: 180px;
      max-width: 70%;
      height: auto;
      border-radius: 12px;
      border: 1px solid #d9d9d9;
      background: #fff;
      object-fit: contain;
    }
    .logo-fallback {
      display: inline-block;
      padding: 10px 20px;
      border: 2px solid #d0d0d0;
      border-radius: 12px;
      background: #fff;
      color: #202020;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    .social-login-title {
      margin: 0 0 18px;
      font-size: 36px;
      font-weight: 700;
      color: #1f1f1f;
      text-align: center;
      line-height: 1.15;
    }
    .social-login-subtitle {
      margin: 0 0 20px;
      text-align: center;
      color: #6d6d6d;
      font-size: 14px;
    }
    .btn-social-custom {
      display: block;
      width: 100%;
      border: 0;
      border-radius: 12px;
      padding: 13px 14px;
      font-size: 16px;
      font-weight: 700;
      text-decoration: none;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
      margin-bottom: 12px;
      border: 1px solid transparent;
    }
    .btn-social-custom:hover,
    .btn-social-custom:focus {
      text-decoration: none;
      opacity: .95;
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(0,0,0,.25);
    }
    .btn-google {
      background: #fff;
      color: #1f1f1f;
      border-color: #d9d9d9;
    }
    .btn-facebook {
      background: #1877f2;
      color: #fff;
    }
    .btn-social-icon {
      width: 20px;
      height: 20px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 20px;
    }
    .btn-social-icon svg {
      width: 100%;
      height: 100%;
      display: block;
    }
    /* Validate styles for social/bnyreward forms */
    .help-block,
    label.error {
      color: #c00000 !important;
      font-size: 13px;
      font-weight: 600;
      margin: 4px 0 8px;
      display: block;
    }
    input.error,
    .form-control.error {
      border-color: #c00000 !important;
      box-shadow: 0 0 0 1px rgba(192, 0, 0, 0.08);
    }
    @media (max-width: 576px) {
      .social-login-card {
        padding: 22px 16px;
        border-radius: 14px;
      }
      .social-login-title {
        font-size: 30px;
      }
    }
  </style>
</head>
<body>
