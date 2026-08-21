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
$outFile = Join-Path $PSScriptRoot 'inspect_return_tables3.txt'
function Run-Q([string]$title, [string]$sql) {
  $cmd = $conn.CreateCommand(); $cmd.CommandText = $sql; $cmd.CommandTimeout = 60
  $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
  $dt = New-Object System.Data.DataTable
  [void]$da.Fill($dt)
  Add-Content $outFile "`n===== $title ($($dt.Rows.Count)) ====="
  if ($dt.Rows.Count -eq 0) { Add-Content $outFile '(empty)'; return }
  $cols = ($dt.Columns | ForEach-Object { $_.ColumnName }) -join ' | '
  Add-Content $outFile $cols
  $n=0
  foreach ($row in $dt.Rows) {
    $n++
    if ($n -gt 30) { Add-Content $outFile '...'; break }
    $vals = @()
    foreach ($c in $dt.Columns) {
      $v = $row[$c.ColumnName]
      if ($v -is [datetime]) { $v = $v.ToString('yyyy-MM-dd HH:mm:ss') }
      if ($null -eq $v) { $v = 'NULL' }
      $s = [string]$v
      if ($s.Length -gt 200) { $s = $s.Substring(0,200) }
      $vals += $s
    }
    Add-Content $outFile ($vals -join ' | ')
  }
}
$conn.Open()
Set-Content $outFile ("inspected " + (Get-Date -Format 's'))
Run-Q 'lazada_orders columns' @"
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='lazada_orders' ORDER BY ORDINAL_POSITION
"@
Run-Q 'tiktok_orders columns' @"
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='tiktok_orders' ORDER BY ORDINAL_POSITION
"@
Run-Q 'lazada sample packed+canceled' @"
SELECT TOP 3 order_number, status, CONVERT(varchar(19), created_at, 120) created_at, CONVERT(varchar(19), updated_at, 120) updated_at
FROM lazada_orders WHERE order_number IN (
  SELECT TOP 1 order_number FROM lazada_orders WHERE status='canceled' AND order_number IN (SELECT order_number FROM lazada_orders WHERE status='packed')
)
ORDER BY OrderID
"@
$conn.Close()
Write-Output $outFile
