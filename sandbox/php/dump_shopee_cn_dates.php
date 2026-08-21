<?php
/**
 * Read-only: dump CN-related views and July 2026 Shopee CN date comparison.
 */
defined('BASEPATH') OR define('BASEPATH', true);
require dirname(__DIR__, 2) . '/application/config/database.php';

$server = $db['default']['hostname'];
$user = $db['default']['username'];
$pass = $db['default']['password'];
$database = $db['default']['database'];
$outDir = dirname(__DIR__) . '/sql';

$conn = sqlsrv_connect($server, array(
	'Database' => $database,
	'UID' => $user,
	'PWD' => $pass,
	'CharacterSet' => 'UTF-8',
	'TrustServerCertificate' => true,
));
if ($conn === false) {
	file_put_contents($outDir . '/dump_cn_error.txt', print_r(sqlsrv_errors(), true));
	fwrite(STDERR, "connect failed\n");
	exit(1);
}

$sql = "SELECT o.name, o.type_desc, OBJECT_DEFINITION(o.object_id) AS def
FROM sys.objects o
WHERE o.name IN (
	'shopee_orders_cn_view',
	'shopee_orders_cn_date_from_shipped_status_from_latest'
)";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
	file_put_contents($outDir . '/dump_cn_error.txt', print_r(sqlsrv_errors(), true));
	fwrite(STDERR, "view query failed\n");
	exit(1);
}
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
	$name = $row['name'];
	file_put_contents($outDir . '/' . $name . '.sql', $row['def'] === null ? "-- NULL definition\n" : $row['def']);
	echo "wrote $name type=".$row['type_desc']." bytes=".strlen((string)$row['def'])."\n";
}
sqlsrv_free_stmt($stmt);

$q = "
SELECT TOP 30
	s.order_sn,
	CONVERT(varchar(19), MIN(s.create_time), 120) AS create_time,
	CONVERT(varchar(19), MIN(CASE WHEN s.order_status = 'SHIPPED' THEN s.update_time END), 120) AS shipped_at,
	CONVERT(varchar(19), MIN(CASE WHEN s.order_status = 'PROCESSED' THEN s.update_time END), 120) AS processed_at,
	CONVERT(varchar(19), MIN(CASE WHEN s.order_status = 'CANCELLED' THEN s.update_time END), 120) AS cancelled_at,
	CONVERT(varchar(19), MIN(CASE WHEN s.order_status IN ('TO_RETURN','RETURNED') THEN s.update_time END), 120) AS return_at,
	CONVERT(varchar(19), MIN(CASE WHEN s.order_status = 'COMPLETED' AND ISNULL(s.is_return,0)=1 THEN s.update_time END), 120) AS completed_return_at,
	MAX(ISNULL(s.is_return,0)) AS is_return,
	MAX(t.taxinvoiceID) AS taxinvoiceID
FROM shopee_orders s
INNER JOIN Shopee_taxinvoiceid t ON t.order_sn = s.order_sn
WHERE s.order_sn IN (
	SELECT DISTINCT order_sn FROM shopee_orders
	WHERE order_status = 'CANCELLED'
	  AND CONVERT(date, create_time) >= '2026-07-01'
	  AND CONVERT(date, create_time) <= '2026-07-31'
)
GROUP BY s.order_sn
ORDER BY MIN(s.create_time)
";
$stmt = sqlsrv_query($conn, $q);
if ($stmt === false) {
	file_put_contents($outDir . '/dump_cn_error.txt', print_r(sqlsrv_errors(), true));
	fwrite(STDERR, "sample query failed\n");
	exit(1);
}
$lines = array("order_sn\tcreate_time\tshipped_at\tprocessed_at\tcancelled_at\treturn_at\tcompleted_return_at\tis_return\ttaxinvoiceID");
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
	$lines[] = implode("\t", array(
		$row['order_sn'],
		$row['create_time'],
		$row['shipped_at'],
		$row['processed_at'],
		$row['cancelled_at'],
		$row['return_at'],
		$row['completed_return_at'],
		$row['is_return'],
		$row['taxinvoiceID'],
	));
}
sqlsrv_free_stmt($stmt);
file_put_contents($outDir . '/shopee_cn_july2026_sample.tsv', implode("\n", $lines));
echo "sample rows=".(count($lines)-1)."\n";

sqlsrv_close($conn);
echo "done\n";
