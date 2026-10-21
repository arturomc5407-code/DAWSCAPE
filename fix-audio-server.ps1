# 🚨 AUDIO SYSTEM - CRITICAL SERVER FIX FOR WINDOWS
# Ejecutar en PowerShell en el servidor

$ErrorActionPreference = "Stop"

Write-Host "📥 Pulling latest code from GitHub..." -ForegroundColor Green
git pull origin main

Write-Host "🔨 Building Vite assets (CRITICAL - This generates public/build/)..." -ForegroundColor Green
npm run build

if (!(Test-Path "public/build")) {
    Write-Host "❌ ERROR: public/build/ was NOT created!" -ForegroundColor Red
    Write-Host "npm install or npm run build failed. Check errors above." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Build successful! public/build/ exists" -ForegroundColor Green

Write-Host "🧹 Clearing Laravel caches..." -ForegroundColor Green
php artisan cache:clear
php artisan config:clear

Write-Host "✅ DONE! Audio system should work now." -ForegroundColor Green
Write-Host "" -ForegroundColor Green
Write-Host "📋 IMMEDIATE TEST:" -ForegroundColor Cyan
Write-Host "1. Reload website: Ctrl+F5 (hard refresh)" -ForegroundColor Cyan
Write-Host "2. Open F12 DevTools → Console tab" -ForegroundColor Cyan
Write-Host "3. Type: audioManager" -ForegroundColor Cyan
Write-Host "4. Should see: AudioManager { playMusic, playSFX, setVolume, ... }" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "❌ If still shows 'undefined':" -ForegroundColor Red
Write-Host "   Run: npm install" -ForegroundColor Red
Write-Host "   Then: npm run build" -ForegroundColor Red
