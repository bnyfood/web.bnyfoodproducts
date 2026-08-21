<?php

if (!defined('BASEPATH'))     exit('No direct script access allowed');
require_once APPPATH . "/third_party/PHPExcel/IOFactory.php";
/*
 * PHPExcel Lib For CI
 *
 * Class Excel
 *
 * Using PHP Excel Class
 */
class Lib_excel extends PHPExcel_IOFactory {
    public function __construct() {
    }
}
?>