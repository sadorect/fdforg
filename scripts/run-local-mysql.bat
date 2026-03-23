@echo off
setlocal

set "XAMPP_BASE=C:\xampp\mysql"
set "INSTANCE_BASE=%LOCALAPPDATA%\Codex\fdf-laravel-mysql-v2"

"%XAMPP_BASE%\bin\mysqld.exe" --standalone --basedir="%XAMPP_BASE%" --datadir="%INSTANCE_BASE%\data" --port=3307 --socket=MySQL-fdf --pid-file="%INSTANCE_BASE%\fdf-mysql.pid" --log-error="%INSTANCE_BASE%\mysql_error.log"
