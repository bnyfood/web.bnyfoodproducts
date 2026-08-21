<?php
/**
 * Read-only: dump OBJECT_DEFINITION of Shopee tax-report / CN stored procedures.
 * Output written under sandbox/sql/ — no DML.
 */
defined('BASEPATH') OR define('BASEPATH', true);
require dirname(__DIR__, 2) . '/application/config/database.php';

$server = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$database = $db['default']['database'];

$outDir = dirname(__DIR__) . '/sql';
if (!is_dir($outDir)) {
	mkdir($outDir, 0777, true);
}

$conn = sqlsrv_connect($server, array(
	'Database' => $database,
	'UID' => $user,
	'PWD' => $pass,
	'CharacterSet' => 'UTF-8',
	'TrustServerCertificate' => true,
));
if ($conn === false) {
	file_put_contents($outDir . '/dump_error.txt', print_r(sqlsrv_errors(), true));
	fwrite(STDERR, "connect failed\n");
	exit(1);
}

$names = array(
	'shopee_select_order_with_OrdernoStart_OrderEnd',
	'shopee_select_order_with_DateStart_DateEnd',
	'shopee_select_order_with_orderitems_by_DateStart_DateEnd_SearchType_CN',
);

$sql = "SELECT OBJECT_NAME(object_id) AS name, OBJECT_DEFINITION(object_id) AS def
FROM sys.objects
WHERE type = 'P' AND name IN (?,?,?)";
$stmt = sqlsrv_query($conn, $sql, $names);
if ($stmt === false) {
	file_put_contents($outDir . '/dump_error.txt', print_r(sqlsrv_errors(), true));
	fwrite(STDERR, "query failed\n");
	exit(1);
}

$found = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
	$name = $row['name'];
	$found[] = $name;
	$file = $outDir . '/' . $name . '.sql';
	file_put_contents($file, $row['def'] === null ? "-- OBJECT_DEFINITION returned NULL (permission?)\n" : $row['def']);
	echo "wrote $file bytes=" . strlen((string)$row['def']) . "\n";
}

$missing = array_diff($names, $found);
if ($missing) {
	echo "MISSING: " . implode(',', $missing) . "\n";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
echo "done\n";
