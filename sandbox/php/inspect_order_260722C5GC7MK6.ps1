# Read-only: inspect one Shopee order. Credentials come from application/config/database.php only.
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
$outFile = Join-Path $PSScriptRoot 'inspect_order_260722C5GC7MK6.txt'
try {
  $conn.Open()
  $queries = @(
    "SELECT order_sn, order_status, ISNULL(is_return,0) AS is_return, CONVERT(varchar(19), create_time, 120) AS create_time, CONVERT(varchar(19), update_time, 120) AS update_time, OrderID FROM shopee_orders WHERE order_sn = '260722C5GC7MK6' ORDER BY update_time, OrderID",
    "SELECT taxinvoiceID, order_sn FROM Shopee_taxinvoiceid WHERE order_sn = '260722C5GC7MK6'",
    "SELECT tracking_number FROM shopee_tracking WHERE order_sn = '260722C5GC7MK6'",
    "SELECT o.order_status, o.OrderID, o.is_virtual_packed, o.cancel_by, o.cancel_reason, i.original_price, i.original_cost_of_goods_sold, i.seller_discount, i.buyer_paid_shipping_fee FROM shopee_orders o LEFT JOIN shopee_escrow_detail d ON o.OrderID = d.OrderID LEFT JOIN shopee_escrow_order_income i ON d.EscrowID = i.EscrowID WHERE o.order_sn = '260722C5GC7MK6' ORDER BY o.update_time, o.OrderID",
    "SELECT * FROM Shopee_taxinvoiceid WHERE order_sn = '260722C5GC7MK6'",
    "SELECT * FROM Shopee_taxinvoiceid_v2 WHERE order_sn = '260722C5GC7MK6'",
    "SELECT OrderID, order_sn, order_status FROM shopee_orders_view WHERE order_sn = '260722C5GC7MK6'",
    "SELECT TOP 5 taxinvoiceID, order_sn, CONVERT(varchar(19), create_time, 120) AS create_time FROM Shopee_taxinvoiceid WHERE create_time >= '2026-07-22' AND create_time < '2026-07-24' ORDER BY taxinvoiceID",
    "SELECT i.item_sku, i.model_original_price, i.model_quantity_purchased, m.sku AS map_sku, m.ProductName FROM shopee_orderitems i LEFT JOIN lazada_skumap m ON i.item_sku = m.sku WHERE i.order_sn = '260722C5GC7MK6'",
    "SELECT taxinvoiceID, COUNT(*) AS cnt FROM Shopee_taxinvoiceid_v2 WHERE taxinvoiceID IN ('Shp20260701192','Shp20260701456','Shp20260601394') GROUP BY taxinvoiceID",
    "SELECT order_sn, taxinvoiceID FROM Shopee_taxinvoiceid_v2 WHERE taxinvoiceID = 'Shp20260701192'",
    "SELECT order_sn, taxinvoiceID FROM Shopee_taxinvoiceid WHERE taxinvoiceID IN ('Shp20260701192','Shp20260601394','Shp20260701394')"
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
