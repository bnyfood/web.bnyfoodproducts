<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function baht_text($number) {
    $number = number_format($number, 2, ".", "");
    list($integer, $fraction) = explode(".", $number);

    $baht = convert_number($integer);
    $satang = convert_number($fraction);

    $result = "";
    if ((int)$integer > 0) {
        $result .= $baht . "บาท";
    }

    if ((int)$fraction > 0) {
        $result .= $satang . "สตางค์";
    } else {
        $result .= "ถ้วน";
    }

    return $result;
}

function convert_number($number) {
    $txtnum1 = ["", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า"];
    $txtnum2 = ["", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน"];
    $number = (string)(int)$number;
    $strlen = strlen($number);

    $result = "";
    for ($i = 0; $i < $strlen; $i++) {
        $n = $number[$i];
        $pos = $strlen - $i - 1;

        if ($n != "0") {
            if ($pos == 0 && $n == "1" && $strlen > 1) {
                $result .= "เอ็ด";
            } elseif ($pos == 1 && $n == "2") {
                $result .= "ยี่";
            } elseif ($pos == 1 && $n == "1") {
                $result .= "";
            } else {
                $result .= $txtnum1[$n];
            }
            $result .= $txtnum2[$pos % 6];
        }

        // handle "ล้าน" recursion
        if ($pos % 6 == 0 && $pos != 0) {
            $result .= "ล้าน";
        }
    }
    return $result;
}

?>