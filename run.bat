@echo off
title TUBES CLOUD COMPUTING - Launcher
cls

echo ==========================================================
echo    TUBES CLOUD COMPUTING LAUNCHER
echo ==========================================================
echo.

:: Check if Docker is installed and running
echo [*] Checking Docker status...
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Docker is not running or not installed.
    echo Please start Docker Desktop first, then run this script again.
    echo.
    pause
    exit /b 1
)
echo [OK] Docker is running.
echo.

:: Spin up containers
echo [*] Starting microservices via Docker Compose...
docker-compose up -d --build
if %errorlevel% neq 0 (
    echo [ERROR] Failed to start Docker Compose.
    pause
    exit /b 1
)
echo [OK] Containers started successfully.
echo.

:: Wait for DB to initialize
echo [*] Waiting 10 seconds for database service to initialize...
timeout /t 10 /nobreak >nul

:: Run database migrations
echo [*] Running migrations in auth-service container...
docker exec tubes_cloud_auth_service php artisan migrate --force
if %errorlevel% neq 0 (
    echo [WARNING] Auth service migration failed. Retrying...
    timeout /t 5 /nobreak >nul
    docker exec tubes_cloud_auth_service php artisan migrate --force
)

echo [*] Running migrations in project-service container...
docker exec tubes_cloud_project_service php artisan migrate --force
if %errorlevel% neq 0 (
    echo [WARNING] Project service migration failed. Retrying...
    timeout /t 5 /nobreak >nul
    docker exec tubes_cloud_project_service php artisan migrate --force
)

echo.
echo ==========================================================
echo    SYSTEM LAUNCHED SUCCESSFULLY
echo ==========================================================
echo.
echo  You can access the system through the following links:
echo.
echo  [+] Main Web Gateway:   http://localhost/login
echo  [+] Auth Service API:   http://localhost:8001/api
echo  [+] Project Service API:http://localhost:8002/api
echo.
echo  To stop the system, run: docker-compose down
echo.
echo ==========================================================
pause
