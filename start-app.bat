@echo off
setlocal EnableDelayedExpansion
cd /d "%~dp0"
title Alcat-system

echo.
echo  =========================================
echo   Alcat-system  ^|  Local Dev Launcher
echo  =========================================
echo.

:: ── 1. Start queue worker via PowerShell (captures PID) ───────────────────
echo  [1/2] Starting Queue Worker...
for /f "usebackq delims=" %%P in (
    `powershell -NoProfile -Command "& { $p = Start-Process php -ArgumentList 'artisan','queue:work','--tries=5','--sleep=3' -WorkingDirectory '%CD%' -PassThru -WindowStyle Minimized; $p.Id }"`
) do set QUEUE_PID=%%P

if "%QUEUE_PID%"=="" (
    echo        WARNING: Could not determine queue worker PID.
    echo        Queue worker may still be running in background.
) else (
    echo        Queue worker started ^(PID: %QUEUE_PID%^)
    echo        Output → storage\logs\queue-worker.log
)

echo.

:: ── 2. Start web server (blocks until Ctrl+C) ────────────────────────────
echo  [2/2] Starting Web Server at http://127.0.0.1:8000
echo        Press Ctrl+C to stop both services.
echo.
php artisan serve

:: ── 3. Stop queue worker ─────────────────────────────────────────────────
echo.
echo  Stopping queue worker...
if not "%QUEUE_PID%"=="" (
    taskkill /PID %QUEUE_PID% /T /F >nul 2>&1
    echo  Queue worker ^(PID %QUEUE_PID%^) stopped.
) else (
    :: Fallback: kill any php.exe running queue:work
    wmic process where "commandline like '%%queue:work%%'" delete >nul 2>&1
    echo  Queue worker stopped.
)

echo.
echo  All services stopped.
pause
