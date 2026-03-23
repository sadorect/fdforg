Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$xamppBase = 'C:\xampp\mysql'
$mysqlAdmin = Join-Path $xamppBase 'bin\mysqladmin.exe'

$dbHost = '127.0.0.1'
$port = 3307
$user = 'root'

if (-not (Test-Path $mysqlAdmin)) {
    throw "Required file not found: $mysqlAdmin"
}

try {
    & $mysqlAdmin --protocol=tcp -h $dbHost -P $port -u $user ping *> $null
} catch {
    $LASTEXITCODE = 1
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "Local MariaDB is not running on ${dbHost}:${port}."
    exit 0
}

Write-Host "Stopping local MariaDB on ${dbHost}:${port} ..."
& $mysqlAdmin --protocol=tcp -h $dbHost -P $port -u $user shutdown

if ($LASTEXITCODE -ne 0) {
    throw "Failed to stop local MariaDB on ${dbHost}:${port}."
}

Write-Host 'Local MariaDB has been stopped.'
