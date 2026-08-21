# Read-only: compare July 2026 Shopee CN invoices vs sales-tax issued ABB.
$cfgPath = Join-Path $PSScriptRoot '..\..\application\config\database.php'
$cfg = Get-Content -Raw $cfgPath
function Get-CfgValue([string]$text, [string]$key) {
  if ($text -match "(?s)'$key'\s*=>\s*""([^""]*)""") { return $Matches[1] }
  if ($text -match "(?s)'$key'\s*=>\s*'([^']*)'") { return $Matches[1] }
  return ''
}
$hostName = Get-CfgValue $cfg 'hostname'
if ($hostName -eq 'localhost') { $hostName = '192.168.1.252' }
$user = Get-CfgValue $cfg 'username'
$pwd = Get-CfgValue $cfg 'password'
$db = Get-CfgValue $cfg 'database'

Add-Type -AssemblyName System.Data
$builder = New-Object System.Data.SqlClient.SqlConnectionStringBuilder
$builder['Data Source'] = $hostName
$builder['Initial Catalog'] = $db
$builder['User ID'] = $user
$builder['Password'] = $pwd
$builder['TrustServerCertificate'] = $true
$conn = New-Object System.Data.SqlClient.SqlConnection $builder.ConnectionString
$outFile = Join-Path $PSScriptRoot 'compare_cn_vs_saletax_july.txt'
try {
  $conn.Open()
  $queries = @(
@"
SELECT
  (SELECT COUNT(*) FROM Shopee_taxinvoiceid WHERE create_time >= '2026-07-01' AND create_time < '2026-08-01' AND ISNULL(taxinvoiceID,'') <> '') AS v1_july,
  (SELECT COUNT(*) FROM Shopee_taxinvoiceid_v2 WHERE create_time >= '2026-07-01' AND create_time < '2026-08-01' AND ISNULL(taxinvoiceID,'') <> '') AS v2_july
"@,
@"
SELECT TOP 30
  cn.order_sn,
  CONVERT(varchar(19), cn.cn_event_at, 120) AS cn_event_at,
  CONVERT(varchar(19), o.create_time, 120) AS order_create,
  v1.taxinvoiceID AS inv_v1,
  v2.taxinvoiceID AS inv_v2,
  CASE WHEN ISNULL(v1.taxinvoiceID,'') <> '' THEN v1.taxinvoiceID ELSE v2.taxinvoiceID END AS inv_used
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
INNER JOIN (
  SELECT order_sn, MIN(create_time) AS create_time
  FROM shopee_orders
  GROUP BY order_sn
) o ON o.order_sn = cn.order_sn
LEFT JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
LEFT JOIN Shopee_taxinvoiceid_v2 v2 ON v2.order_sn = cn.order_sn AND ISNULL(v2.taxinvoiceID,'') <> ''
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND (ISNULL(v1.taxinvoiceID,'') <> '' OR ISNULL(v2.taxinvoiceID,'') <> '')
ORDER BY cn.cn_event_at, cn.order_sn
"@,
@"
SELECT COUNT(*) AS cn_july,
  SUM(CASE WHEN ISNULL(v1.taxinvoiceID,'') = '' THEN 1 ELSE 0 END) AS cn_v2_only,
  SUM(CASE WHEN ISNULL(v1.taxinvoiceID,'') <> '' THEN 1 ELSE 0 END) AS cn_has_v1
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
LEFT JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
LEFT JOIN Shopee_taxinvoiceid_v2 v2 ON v2.order_sn = cn.order_sn AND ISNULL(v2.taxinvoiceID,'') <> ''
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND (ISNULL(v1.taxinvoiceID,'') <> '' OR ISNULL(v2.taxinvoiceID,'') <> '')
"@,
@"
SELECT COUNT(*) AS cn_july_not_in_july_saletax_v1
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
INNER JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND NOT EXISTS (
    SELECT 1 FROM shopee_orders_view v
    INNER JOIN Shopee_taxinvoiceid t ON t.order_sn = v.order_sn AND ISNULL(t.taxinvoiceID,'') <> ''
    WHERE v.order_sn = cn.order_sn
      AND v.order_status = 'PROCESSED'
      AND CONVERT(date, CONVERT(varchar, v.create_time, 23)) >= '2026-07-01'
      AND CONVERT(date, CONVERT(varchar, v.create_time, 23)) <= '2026-07-31'
  )
"@,
@"
SELECT
  cn.order_sn,
  CASE WHEN ISNULL(v1.taxinvoiceID,'') <> '' THEN v1.taxinvoiceID ELSE v2.taxinvoiceID END AS inv_used,
  CONVERT(varchar(10), cn.cn_event_at, 23) AS cn_day,
  CONVERT(varchar(10), o.create_time, 23) AS tax_day,
  inc.original_price AS tax_goods,
  inc.seller_discount AS tax_seller,
  inc.buyer_paid_shipping_fee AS tax_ship
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
INNER JOIN (SELECT order_sn, MIN(create_time) AS create_time FROM shopee_orders GROUP BY order_sn) o ON o.order_sn = cn.order_sn
LEFT JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
LEFT JOIN Shopee_taxinvoiceid_v2 v2 ON v2.order_sn = cn.order_sn AND ISNULL(v2.taxinvoiceID,'') <> ''
INNER JOIN shopee_orders_view pv ON pv.order_sn = cn.order_sn AND pv.order_status = 'PROCESSED'
INNER JOIN shopee_escrow_detail d ON d.OrderID = pv.OrderID
INNER JOIN shopee_escrow_order_income inc ON inc.EscrowID = d.EscrowID
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND (ISNULL(v1.taxinvoiceID,'') <> '' OR ISNULL(v2.taxinvoiceID,'') <> '')
ORDER BY cn.cn_event_at
"@,
@"
SELECT cn.order_sn, v2.taxinvoiceID AS inv_v2, CONVERT(varchar(10), cn.cn_event_at, 23) AS cn_day
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
LEFT JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
INNER JOIN Shopee_taxinvoiceid_v2 v2 ON v2.order_sn = cn.order_sn AND ISNULL(v2.taxinvoiceID,'') <> ''
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND ISNULL(v1.taxinvoiceID,'') = ''
ORDER BY cn.cn_event_at, cn.order_sn
"@,
@"
SELECT cn.order_sn, v1.taxinvoiceID AS inv_v1,
  CONVERT(varchar(10), cn.cn_event_at, 23) AS cn_day,
  CONVERT(varchar(10), v1.create_time, 23) AS inv_day
FROM (
  SELECT order_sn, MIN(update_time) AS cn_event_at
  FROM shopee_orders
  WHERE order_status = 'CANCELLED'
     OR order_status IN ('TO_RETURN','RETURNED')
     OR (order_status = 'COMPLETED' AND ISNULL(is_return,0) = 1)
  GROUP BY order_sn
) cn
INNER JOIN Shopee_taxinvoiceid v1 ON v1.order_sn = cn.order_sn AND ISNULL(v1.taxinvoiceID,'') <> ''
WHERE CONVERT(date, cn.cn_event_at) >= '2026-07-01'
  AND CONVERT(date, cn.cn_event_at) <= '2026-07-31'
  AND EXISTS (SELECT 1 FROM shopee_orders p WHERE p.order_sn = cn.order_sn AND p.order_status = 'PROCESSED')
  AND v1.create_time < '2026-07-01'
ORDER BY cn.cn_event_at, cn.order_sn
"@
  )
  $out = New-Object System.Collections.Generic.List[string]
  foreach ($q in $queries) {
    $cmd = $conn.CreateCommand()
    $cmd.CommandText = $q
    $adapter = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
    $dt = New-Object System.Data.DataTable
    [void]$adapter.Fill($dt)
    $cols = @(); foreach ($c in $dt.Columns) { $cols += $c.ColumnName }
    [void]$out.Add("=== " + ($cols -join ' | ') + " ===")
    [void]$out.Add("count=$($dt.Rows.Count)")
    foreach ($r in $dt.Rows) {
      $line = @()
      foreach ($c in $dt.Columns) { $line += [string]$r[$c] }
      [void]$out.Add(($line -join "`t"))
    }
    [void]$out.Add("")
  }
  $out | Set-Content -Encoding UTF8 $outFile
  Write-Output "ok"
} catch {
  Write-Output ("ERR: " + $_.Exception.Message)
  Set-Content -Encoding UTF8 $outFile ("ERR: " + $_.Exception.Message)
} finally {
  if ($conn.State -eq 'Open') { $conn.Close() }
}
