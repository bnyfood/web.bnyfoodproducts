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
$sn = '2606263CBE9JMP'
Add-Type -AssemblyName System.Data
$builder = New-Object System.Data.SqlClient.SqlConnectionStringBuilder
$builder['Data Source'] = $hostName
$builder['Initial Catalog'] = $db
$builder['User ID'] = $user
$builder['Password'] = $pwd
$builder['TrustServerCertificate'] = $true
$conn = New-Object System.Data.SqlClient.SqlConnection $builder.ConnectionString
$conn.Open()
$outFile = Join-Path $PSScriptRoot 'inspect_order_2606263CBE9JMP.txt'
Set-Content -Path $outFile -Value '' -Encoding UTF8
function Run-Q([string]$title, [string]$sql) {
  try {
    $cmd = $conn.CreateCommand()
    $cmd.CommandText = $sql
    $cmd.CommandTimeout = 60
    $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
    $dt = New-Object System.Data.DataTable
    [void]$da.Fill($dt)
    Add-Content $outFile "`n===== $title ($($dt.Rows.Count)) ====="
    if ($dt.Rows.Count -eq 0) { return }
    $cols = @(); foreach ($c in $dt.Columns) { $cols += $c.ColumnName }
    Add-Content $outFile ($cols -join ' | ')
    foreach ($r in $dt.Rows) {
      $line = @(); foreach ($c in $dt.Columns) { $line += [string]$r[$c] }
      Add-Content $outFile ($line -join "`t")
    }
  } catch {
    Add-Content $outFile "`n===== $title ERR: $($_.Exception.Message) ====="
  }
}

Run-Q 'orders' "SELECT OrderID, order_sn, order_status, ISNULL(is_return,0) AS is_return, CONVERT(varchar(19), create_time, 120) AS create_time, CONVERT(varchar(19), update_time, 120) AS update_time, ISNULL(taxinvoiceID,'') AS taxinvoiceID_on_order FROM shopee_orders WHERE order_sn = '$sn' ORDER BY OrderID"
Run-Q 'return_order' "SELECT shopee_return_order_id, return_sn, order_sn, status, ISNULL(is_active,0) AS is_active FROM shopee_return_order WHERE order_sn = '$sn'"
Run-Q 'taxinvoiceid' "SELECT taxinvoiceID, order_sn FROM Shopee_taxinvoiceid WHERE order_sn = '$sn'"
Run-Q 'cn_view' "SELECT order_sn, latest_status, ISNULL(is_return,0) AS is_return, CONVERT(varchar(19), invoice_date, 120) AS invoice_date FROM shopee_orders_cn_date_from_shipped_status_from_latest WHERE order_sn = '$sn'"
Run-Q 'escrow' "SELECT i.EscrowID, i.original_price, i.seller_discount, i.escrow_amount FROM shopee_escrow_order_income i INNER JOIN shopee_escrow_detail d ON d.EscrowID = i.EscrowID INNER JOIN shopee_orders o ON o.OrderID = d.OrderID WHERE o.order_sn = '$sn'"
Run-Q 'cn_eligible' "SELECT CASE WHEN EXISTS (SELECT 1 FROM shopee_orders_cn_date_from_shipped_status_from_latest v INNER JOIN Shopee_taxinvoiceid t ON t.order_sn = v.order_sn WHERE v.order_sn = '$sn' AND (v.latest_status = 'CANCELLED' OR ISNULL(v.is_return,0) = 1)) THEN 1 ELSE 0 END AS cn_eligible"
Run-Q 'cn_group_sample' @"
SELECT TOP 3 convert(varchar, v.invoice_date, 23) as transactiondate, v.order_sn, v.latest_status, ISNULL(v.is_return,0) AS is_return,
  (i.original_price+i.buyer_paid_shipping_fee-i.seller_discount) as ValueBeforeVAT
FROM shopee_orders_cn_date_from_shipped_status_from_latest v
INNER JOIN Shopee_taxinvoiceid t ON t.order_sn = v.order_sn
INNER JOIN shopee_escrow_detail d ON d.OrderID = v.OrderID
INNER JOIN shopee_escrow_order_income i ON i.EscrowID = d.EscrowID
WHERE v.order_sn = '$sn'
  AND (v.latest_status IN ('CANCELLED') OR ISNULL(v.is_return,0) = 1)
"@

$conn.Close()
Write-Host "Wrote $outFile"
Get-Content $outFile
