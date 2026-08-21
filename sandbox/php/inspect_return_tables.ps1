# Read-only: inspect return/CN tables. Credentials from application/config/database.php only.
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
$outFile = Join-Path $PSScriptRoot 'inspect_return_tables.txt'
function Run-Q([string]$title, [string]$sql) {
  $cmd = $conn.CreateCommand()
  $cmd.CommandText = $sql
  $cmd.CommandTimeout = 60
  $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
  $dt = New-Object System.Data.DataTable
  [void]$da.Fill($dt)
  Add-Content -Path $outFile -Value "`n===== $title ($($dt.Rows.Count) rows) ====="
  if ($dt.Rows.Count -eq 0) { Add-Content -Path $outFile -Value '(empty)'; return }
  $cols = ($dt.Columns | ForEach-Object { $_.ColumnName }) -join ' | '
  Add-Content -Path $outFile -Value $cols
  foreach ($row in $dt.Rows) {
    $vals = @()
    foreach ($c in $dt.Columns) {
      $v = $row[$c.ColumnName]
      if ($v -is [datetime]) { $v = $v.ToString('yyyy-MM-dd HH:mm:ss') }
      if ($null -eq $v) { $v = 'NULL' }
      $s = [string]$v
      if ($s.Length -gt 180) { $s = $s.Substring(0,180) + '...' }
      $vals += $s
    }
    Add-Content -Path $outFile -Value ($vals -join ' | ')
  }
}
try {
  $conn.Open()
  Set-Content -Path $outFile -Value ("inspected at " + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'))
  Run-Q 'tables matching return/refund/reverse/cancel' @"
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_NAME LIKE '%return%' OR TABLE_NAME LIKE '%refund%'
   OR TABLE_NAME LIKE '%reverse%' OR TABLE_NAME LIKE '%cancel%'
ORDER BY TABLE_NAME
"@
  Run-Q 'shopee_return_order columns' @"
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='shopee_return_order' ORDER BY ORDINAL_POSITION
"@
  Run-Q 'shopee_return_order sample' @"
SELECT TOP 5 * FROM shopee_return_order ORDER BY shopee_return_order_id DESC
"@
  Run-Q 'lazada_orders order_make_cn column' @"
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME='lazada_orders' AND COLUMN_NAME IN ('order_make_cn','cn_status','status','order_number','is_virtual_packed','price','updated_at')
ORDER BY ORDINAL_POSITION
"@
  Run-Q 'lazada_orders order_make_cn distribution' @"
SELECT ISNULL(CONVERT(varchar(20), order_make_cn),'NULL') AS order_make_cn, COUNT(*) AS cnt
FROM lazada_orders GROUP BY order_make_cn
"@
  Run-Q 'view defs matching cn' @"
SELECT name, type_desc FROM sys.objects
WHERE name LIKE '%cn%' AND name LIKE '%lazada%'
ORDER BY name
"@
  Run-Q 'lazada cn view definition snippet' @"
SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.lazada_orders_cn_date_from_shipped_status_from_latest')) AS def
"@
  Run-Q 'shopee cn view definition snippet' @"
SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.shopee_orders_cn_date_from_shipped_status_from_latest')) AS def
"@
  Run-Q 'tiktok cn view definition snippet' @"
SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_orders_cn_date_latest')) AS def
"@
  Run-Q 'shopee_orders is_return distribution' @"
SELECT ISNULL(CONVERT(varchar(20), is_return),'NULL') AS is_return, COUNT(*) AS cnt
FROM shopee_orders GROUP BY is_return
"@
} catch {
  Set-Content -Path $outFile -Value $_.Exception.Message
} finally {
  if ($conn.State -eq 'Open') { $conn.Close() }
}
Write-Output $outFile
