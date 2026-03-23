Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$xamppBase = 'C:\xampp\mysql'
$mysqlInstallDb = Join-Path $xamppBase 'bin\mysql_install_db.exe'
$mysqld = Join-Path $xamppBase 'bin\mysqld.exe'
$mysql = Join-Path $xamppBase 'bin\mysql.exe'
$mysqlAdmin = Join-Path $xamppBase 'bin\mysqladmin.exe'
$launcher = Join-Path $PSScriptRoot 'run-local-mysql.bat'

$instanceBase = Join-Path $env:LOCALAPPDATA 'Codex\fdf-laravel-mysql-v2'
$dataDir = Join-Path $instanceBase 'data'
$pidFile = Join-Path $instanceBase 'fdf-mysql.pid'
$errorLog = Join-Path $instanceBase 'mysql_error.log'

$dbHost = '127.0.0.1'
$port = 3307
$user = 'root'
$database = 'fdf'
$socket = 'MySQL-fdf'

function Assert-FileExists {
    param([string] $Path)

    if (-not (Test-Path $Path)) {
        throw "Required file not found: $Path"
    }
}

function Test-MySqlReady {
    try {
        & $mysql --protocol=tcp -h $dbHost -P $port -u $user -e "SELECT 1" *> $null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

Assert-FileExists -Path $mysqlInstallDb
Assert-FileExists -Path $mysqld
Assert-FileExists -Path $mysql
Assert-FileExists -Path $mysqlAdmin
Assert-FileExists -Path $launcher

New-Item -ItemType Directory -Force -Path $instanceBase | Out-Null

if (-not (Test-Path (Join-Path $dataDir 'mysql'))) {
    Write-Host "Initializing local MariaDB data directory at $dataDir ..."
    & $mysqlInstallDb --datadir=$dataDir --port=$port --silent

    if ($LASTEXITCODE -ne 0) {
        throw "mysql_install_db.exe failed with exit code $LASTEXITCODE."
    }
}

if (-not (Test-MySqlReady)) {
    Write-Host "Starting local MariaDB on ${dbHost}:${port} ..."
    $launchCommand = "start `"FDF Local MySQL`" /min `"$launcher`""
    Start-Process -FilePath 'cmd.exe' -ArgumentList '/c', $launchCommand -WindowStyle Hidden | Out-Null

    $started = $false

    foreach ($attempt in 1..30) {
        Start-Sleep -Seconds 1

        if (Test-MySqlReady) {
            $started = $true
            break
        }
    }

    if (-not $started) {
        if (Test-Path $errorLog) {
            Write-Host ''
            Write-Host 'Last MariaDB error log lines:'
            Get-Content $errorLog -Tail 40
        }

        throw "MariaDB did not start on ${dbHost}:${port}."
    }
} else {
    Write-Host "Local MariaDB is already running on ${dbHost}:${port}."
}

& $mysql --protocol=tcp -h $dbHost -P $port -u $user -e "CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if ($LASTEXITCODE -ne 0) {
    throw "Failed to create or verify the '$database' database."
}

Write-Host ''
Write-Host "Local MariaDB is ready."
Write-Host "Host: $dbHost"
Write-Host "Port: $port"
Write-Host "Database: $database"
Write-Host "Data dir: $dataDir"
