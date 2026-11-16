@echo off
chcp 65001 >nul
cd /d "%~dp0"

docker info >nul 2>&1
if errorlevel 1 (
  echo Kaivitan Docker Desktopi...
  start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
  :wait
  timeout /t 5 /nobreak >nul
  docker info >nul 2>&1
  if errorlevel 1 goto wait
)

echo Kaivitan projekti...
docker compose up -d --build
if errorlevel 1 (
  echo Viga: docker compose ei kaivitunud.
  pause
  exit /b 1
)

timeout /t 5 /nobreak >nul
start "" http://localhost:8080
echo Valmis: http://localhost:8080
pause
