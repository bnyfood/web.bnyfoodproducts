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
$outFile = Join-Path $PSScriptRoot 'inspect_returns_after_poll.txt'
function Run-Q([string]$title, [string]$sql) {
  try {
    $cmd = $conn.CreateCommand()
    $cmd.CommandText = $sql
    $cmd.CommandTimeout = 60
    $da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
    $dt = New-Object System.Data.DataTable
    [void]$da.Fill($dt)
    Add-Content $outFile "`n===== $title ($($dt.Rows.Count)) ====="
    if ($dt.Rows.Count -eq 0) { Add-Content $outFile '(empty)'; return }
    $cols = ($dt.Columns | ForEach-Object { $_.ColumnName }) -join ' | '
    Add-Content $outFile $cols
    $n = 0
    foreach ($row in $dt.Rows) {
      $n++
      if ($n -gt 25) { Add-Content $outFile '...'; break }
      $vals = @()
      foreach ($c in $dt.Columns) {
        $v = $row[$c.ColumnName]
        if ($v -is [datetime]) { $v = $v.ToString('yyyy-MM-dd HH:mm:ss') }
        if ($null -eq $v) { $v = 'NULL' }
        $s = [string]$v
        if ($s.Length -gt 160) { $s = $s.Substring(0,160) }
        $vals += $s
      }
      Add-Content $outFile ($vals -join ' | ')
    }
  } catch {
    Add-Content $outFile "`n===== $title ERROR ====="
    Add-Content $outFile $_.Exception.Message
  }
}
$conn.Open()
Set-Content $outFile ("inspected " + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'))
Run-Q 'table existence' @"
SELECT name FROM sys.tables
WHERE name IN ('shopee_return_order','lazada_return_order','tiktok_return_order')
ORDER BY name
"@
Run-Q 'counts' @"
SELECT
  (SELECT COUNT(*) FROM shopee_return_order) AS shopee_n,
  CASE WHEN OBJECT_ID('dbo.lazada_return_order') IS NULL THEN -1 ELSE (SELECT COUNT(*) FROM lazada_return_order) END AS lazada_n,
  CASE WHEN OBJECT_ID('dbo.tiktok_return_order') IS NULL THEN -1 ELSE (SELECT COUNT(*) FROM tiktok_return_order) END AS tiktok_n
"@
Run-Q 'shopee latest' @"
SELECT TOP 8 shopee_return_order_id, return_sn, order_sn, status, refund_amount,
  CONVERT(varchar(19), start_time, 120) AS start_time,
  CONVERT(varchar(19), end_time, 120) AS end_time, is_active
FROM shopee_return_order ORDER BY shopee_return_order_id DESC
"@
Run-Q 'shopee is_return after apply' @"
SELECT
  CASE WHEN EXISTS (SELECT 1 FROM shopee_orders WHERE order_sn='2606263CBE9JMP' AND ISNULL(is_return,0)=1)
    THEN 1 ELSE 0 END AS order_2606263CBE9JMP_is_return,
  (SELECT COUNT(DISTINCT order_sn) FROM shopee_orders WHERE ISNULL(is_return,0)=1) AS sn_with_is_return
"@
Run-Q 'lazada latest' @"
IF OBJECT_ID('dbo.lazada_return_order') IS NULL
  SELECT 'missing' AS note
ELSE
  SELECT TOP 8 lazada_return_order_id, reverse_order_id, reverse_order_line_id, order_number,
    request_type, reverse_status, refund_amount,
    CONVERT(varchar(19), start_time, 120) AS start_time,
    CONVERT(varchar(19), end_time, 120) AS end_time, is_active
  FROM lazada_return_order ORDER BY lazada_return_order_id DESC
"@
Run-Q 'tiktok latest' @"
IF OBJECT_ID('dbo.tiktok_return_order') IS NULL
  SELECT 'missing' AS note
ELSE
  SELECT TOP 8 tiktok_return_order_id, return_id, order_id, record_type, status, refund_amount,
    CONVERT(varchar(19), start_time, 120) AS start_time,
    CONVERT(varchar(19), end_time, 120) AS end_time, is_active
  FROM tiktok_return_order ORDER BY tiktok_return_order_id DESC
"@
Run-Q 'tiktok by record_type' @"
IF OBJECT_ID('dbo.tiktok_return_order') IS NULL
  SELECT 'missing' AS note
ELSE
  SELECT ISNULL(record_type,'NULL') AS record_type, ISNULL(status,'NULL') AS status, COUNT(*) AS n
  FROM tiktok_return_order GROUP BY record_type, status ORDER BY n DESC
"@
Run-Q 'lazada by type/status' @"
IF OBJECT_ID('dbo.lazada_return_order') IS NULL
  SELECT 'missing' AS note
ELSE
  SELECT ISNULL(request_type,'NULL') AS request_type, ISNULL(reverse_status,'NULL') AS reverse_status, COUNT(*) AS n
  FROM lazada_return_order GROUP BY request_type, reverse_status ORDER BY n DESC
"@
$conn.Close()
Write-Output $outFile
