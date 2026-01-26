<?php
$path = 'C:/inetpub/storage/bnyfoodproducts/cache/';
$start = microtime(true);
$f = $path . '__perm_test_' . uniqid() . '.tmp';
$testDir = $path . '__testdir_' . uniqid();

try {
    // ตรวจสอบว่ามีโฟลเดอร์หลักไหม
    if (!is_dir($path)) {
        die("NO_DIR");
    }

    // ตรวจสอบว่าสร้างโฟลเดอร์ย่อยได้ไหม
    $mkdir = mkdir($testDir);
    if (!$mkdir) {
        die("MKDIR_FAIL");
    }

    // เขียนไฟล์ในโฟลเดอร์หลัก
    if (!is_writable($path)) {
        die("NOT_WRITABLE");
    }
    $ok1 = file_put_contents($f, str_repeat('x', 1024));
    if ($ok1 === false) {
        die("WRITE_FAIL");
    }

    // อ่านไฟล์
    $ok2 = file_get_contents($f);
    if ($ok2 === false) {
        die("READ_FAIL");
    }

    /*// ลบไฟล์
    $ok3 = unlink($f);
    if (!$ok3) {
        die("DELETE_FAIL");
    }

    // ลบโฟลเดอร์ที่สร้าง
    $rmdir = rmdir($testDir);
    if (!$rmdir) {
        die("RMDIR_FAIL");
    }*/

    echo "OK in " . round((microtime(true) - $start) * 1000) . " ms";
} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage();
}
