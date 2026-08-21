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
$cmd = $conn.CreateCommand()
$cmd.CommandText = "SELECT status, COUNT(*) n FROM shopee_return_order GROUP BY status ORDER BY n DESC"
$da = New-Object System.Data.SqlClient.SqlDataAdapter $cmd
$dt = New-Object System.Data.DataTable
[void]$da.Fill($dt)
foreach ($r in $dt.Rows) { Write-Host ("{0}`t{1}" -f $r.status, $r.n) }
$conn.Close()
