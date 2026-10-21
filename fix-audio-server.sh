#!/bin/bash
# 🚨 AUDIO SYSTEM - CRITICAL SERVER FIX
# Ejecutar AHORA en el servidor para que funcione la música

cd /path/to/DAWSCAPE  # CAMBIAR ESTO A LA RUTA REAL

echo "📥 Pulling latest code from GitHub..."
git pull origin main

echo "🔨 Compiling Vite assets (CRITICAL!)..."
npm run build

if [ ! -d "public/build" ]; then
  echo "❌ ERROR: public/build/ was not created!"
  echo "npm run build failed. Check errors above."
  exit 1
fi

echo "✅ Build successful! public/build/ exists"

echo "🧹 Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear

echo "🔍 Verifying audioManager is available..."
php artisan tinker << 'EOF'
echo "Check public/build/manifest.json exists: ";
echo file_exists('public/build/manifest.json') ? 'YES ✅' : 'NO ❌';
echo "\n";
EOF

echo "✅ DONE! Audio system should work now."
echo ""
echo "📋 Next steps:"
echo "1. Reload the website in browser (Ctrl+F5 for hard refresh)"
echo "2. Open DevTools (F12)"
echo "3. Console: audioManager"
echo "4. Should show: AudioManager { ... }"
echo ""
echo "If still broken, run: npm install && npm run build"
