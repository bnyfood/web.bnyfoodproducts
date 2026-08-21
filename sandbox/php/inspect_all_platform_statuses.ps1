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
$b = New-Object System.Data.SqlClient.SqlConnectionStringBuilder
$b['Data Source'] = $hostName; $b['Initial Catalog'] = $db; $b['User ID'] = $user; $b['Password'] = $pwd; $b['TrustServerCertificate'] = $true
$conn = New-Object System.Data.SqlClient.SqlConnection $b.ConnectionString
$conn.Open()
$outFile = Join-Path $PSScriptRoot 'inspect_all_platform_statuses.txt'
Set-Content $outFile '' -Encoding UTF8
function Run-Q([string]$title, [string]$sql) {
  $cmd = $conn.CreateCommand(); $cmd.CommandText = $sql; $cmd.CommandTimeout = 120
  $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
  $dt = New-Object System.Data.DataTable
  try {
    [void]$da.Fill($dt)
    Add-Content $outFile "`n===== $title ($($dt.Rows.Count)) ====="
    if ($dt.Rows.Count -eq 0) { Add-Content $outFile '(empty)'; return }
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

Run-Q 'shopee_orders_status' "SELECT order_status, COUNT(*) n FROM shopee_orders GROUP BY order_status ORDER BY n DESC"
Run-Q 'shopee_return_status' "SELECT status, COUNT(*) n FROM shopee_return_order GROUP BY status ORDER BY n DESC"
Run-Q 'lazada_orders_status' "SELECT status, COUNT(*) n FROM lazada_orders GROUP BY status ORDER BY n DESC"
Run-Q 'lazada_return_status' "IF OBJECT_ID('lazada_return_order') IS NULL SELECT 'missing' AS note ELSE SELECT reverse_status, request_type, COUNT(*) n FROM lazada_return_order GROUP BY reverse_status, request_type ORDER BY n DESC"
Run-Q 'tiktok_orders_status' "SELECT status, COUNT(*) n FROM tiktok_orders GROUP BY status ORDER BY n DESC"
Run-Q 'tiktok_return_status' "SELECT record_type, status, COUNT(*) n FROM tiktok_return_order GROUP BY record_type, status ORDER BY record_type, n DESC"

$conn.Close()
Write-Host "Wrote $outFile"
Get-Content $outFile
