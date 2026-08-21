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
$outFile = Join-Path $PSScriptRoot 'inspect_return_tables2.txt'
function Run-Q([string]$title, [string]$sql) {
  try {
    $cmd = $conn.CreateCommand()
    $cmd.CommandText = $sql
    $cmd.CommandTimeout = 90
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
        if ($s.Length -gt 12000) { $s = $s.Substring(0,12000) + '...[truncated]' }
        $vals += $s
      }
      Add-Content -Path $outFile -Value ($vals -join " `n---row---`n ")
    }
  } catch {
    Add-Content -Path $outFile -Value "`n===== $title ERROR ====="
    Add-Content -Path $outFile -Value $_.Exception.Message
  }
}
$conn.Open()
Set-Content -Path $outFile -Value ("inspected at " + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'))
Run-Q 'full lazada cn view' "SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.lazada_orders_cn_date_from_shipped_status_from_latest')) AS def"
Run-Q 'full shopee cn date view' "SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.shopee_orders_cn_date_from_shipped_status_from_latest')) AS def"
Run-Q 'full shopee_orders_cn_view' "SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.shopee_orders_cn_view')) AS def"
Run-Q 'full tiktok cn view' "SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_orders_cn_date_latest')) AS def"
Run-Q 'return vs is_return mismatch' @"
SELECT r.status, COUNT(*) AS returns_n,
  SUM(CASE WHEN x.has_is_return=1 THEN 1 ELSE 0 END) AS has_is_return,
  SUM(CASE WHEN x.has_completed=1 THEN 1 ELSE 0 END) AS has_completed,
  SUM(CASE WHEN x.has_processed=1 THEN 1 ELSE 0 END) AS has_processed,
  SUM(CASE WHEN x.has_cancelled=1 THEN 1 ELSE 0 END) AS has_cancelled
FROM shopee_return_order r
OUTER APPLY (
  SELECT
    CASE WHEN EXISTS (SELECT 1 FROM shopee_orders o WHERE o.order_sn=r.order_sn AND ISNULL(o.is_return,0)=1) THEN 1 ELSE 0 END AS has_is_return,
    CASE WHEN EXISTS (SELECT 1 FROM shopee_orders o WHERE o.order_sn=r.order_sn AND o.order_status='COMPLETED') THEN 1 ELSE 0 END AS has_completed,
    CASE WHEN EXISTS (SELECT 1 FROM shopee_orders o WHERE o.order_sn=r.order_sn AND o.order_status='PROCESSED') THEN 1 ELSE 0 END AS has_processed,
    CASE WHEN EXISTS (SELECT 1 FROM shopee_orders o WHERE o.order_sn=r.order_sn AND o.order_status='CANCELLED') THEN 1 ELSE 0 END AS has_cancelled
) x
GROUP BY r.status
"@
Run-Q '2606263CBE9JMP return rows' "SELECT * FROM shopee_return_order WHERE order_sn='2606263CBE9JMP'"
Run-Q 'tiktok_orders status counts' "SELECT status, COUNT(*) cnt FROM tiktok_orders GROUP BY status ORDER BY cnt DESC"
Run-Q 'lazada_orders status counts' "SELECT status, COUNT(*) cnt FROM lazada_orders GROUP BY status ORDER BY cnt DESC"
Run-Q 'shopee_return_order status counts' "SELECT status, COUNT(*) cnt FROM shopee_return_order GROUP BY status ORDER BY cnt DESC"
$conn.Close()
Write-Output $outFile
