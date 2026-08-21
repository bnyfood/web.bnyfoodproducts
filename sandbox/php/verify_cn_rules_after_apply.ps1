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
$b = New-Object System.Data.SqlClient.SqlConnectionStringBuilder
$b['Data Source'] = $hostName; $b['Initial Catalog'] = $db; $b['User ID'] = $user; $b['Password'] = $pwd; $b['TrustServerCertificate'] = $true
$conn = New-Object System.Data.SqlClient.SqlConnection $b.ConnectionString
$conn.Open()
function Run-Q([string]$title, [string]$sql) {
  $cmd = $conn.CreateCommand(); $cmd.CommandText = $sql; $cmd.CommandTimeout = 60
  $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
  $dt = New-Object System.Data.DataTable
  [void]$da.Fill($dt)
  Write-Host "`n===== $title ($($dt.Rows.Count)) ====="
  foreach ($r in $dt.Rows) {
    $line = @(); foreach ($c in $dt.Columns) { $line += "$($c.ColumnName)=$($r[$c])" }
    Write-Host ($line -join ' | ')
  }
}
Run-Q 'order' "SELECT order_status, ISNULL(is_return,0) is_return FROM shopee_orders WHERE order_sn='$sn' ORDER BY OrderID"
Run-Q 'return' "SELECT status, CONVERT(varchar(19), end_time, 120) end_time, ISNULL(is_active,0) is_active FROM shopee_return_order WHERE order_sn='$sn'"
Run-Q 'cn_event' @"
SELECT order_sn, CONVERT(varchar(19), MIN(cn_event_at), 120) cn_event_at FROM (
  SELECT order_sn, update_time AS cn_event_at FROM shopee_orders WHERE order_sn='$sn' AND order_status='CANCELLED'
  UNION ALL
  SELECT order_sn, end_time FROM shopee_return_order WHERE order_sn='$sn' AND UPPER(status) IN ('REFUND_PAID','COMPLETED') AND end_time IS NOT NULL
) e GROUP BY order_sn
"@
Run-Q 'is_return_left' "SELECT COUNT(DISTINCT order_sn) n FROM shopee_orders WHERE ISNULL(is_return,0)=1"
Run-Q 'refund_paid_is_return' @"
SELECT COUNT(DISTINCT o.order_sn) n
FROM shopee_orders o
INNER JOIN shopee_return_order r ON r.order_sn=o.order_sn AND UPPER(r.status) IN ('REFUND_PAID','COMPLETED')
WHERE ISNULL(o.is_return,0)=1
"@
$conn.Close()
