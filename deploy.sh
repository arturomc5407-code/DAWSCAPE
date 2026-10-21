#!/bin/bash

# ==============================================================================
# Script de Despliegue Automatizado DAWSCAPE (CI/CD Local)
# Entorno: AlmaLinux, Nginx, PHP-FPM, MariaDB, Laravel + Vite
# Uso: sudo bash deploy.sh [--skip-npm] [--skip-migrations] [--rollback]
# ==============================================================================

set -euo pipefail

# ── Rutas y usuario ──
APP_DIR="/var/www/DAWSCAPE"
LARAVEL_USER="nginx"
BACKUP_DIR="/var/backups/dawscape"
LOG_FILE="/var/log/dawscape-deploy.log"
LOCK_FILE="/tmp/dawscape-deploy.lock"
MAX_BACKUPS=5

# ── PATH para sudo ──
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:$PATH"

# ── Colores ──
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# ── Flags de CLI ──
SKIP_NPM=false
SKIP_MIGRATIONS=false
DO_ROLLBACK=false

for arg in "$@"; do
    case "$arg" in
        --skip-npm)        SKIP_NPM=true ;;
        --skip-migrations) SKIP_MIGRATIONS=true ;;
        --rollback)        DO_ROLLBACK=true ;;
        *) echo -e "${RED}Flag desconocido: $arg${NC}"; exit 1 ;;
    esac
done

# ── Logging dual: terminal + archivo ──
exec > >(tee -a "$LOG_FILE") 2>&1

# ── Timestamp de inicio ──
DEPLOY_START=$(date +%s)
DEPLOY_DATE=$(date +"%Y-%m-%d %H:%M:%S")

log_step() {
    echo -e "${YELLOW}[$1] $2${NC}"
}

log_ok() {
    echo -e "${GREEN}    -> OK${NC}"
}

# ── Lock: prevenir despliegues simultaneos ──
if [ -f "$LOCK_FILE" ]; then
    LOCK_PID=$(cat "$LOCK_FILE" 2>/dev/null)
    if kill -0 "$LOCK_PID" 2>/dev/null; then
        echo -e "${RED}Otro despliegue esta en curso (PID $LOCK_PID). Abortando.${NC}"
        exit 1
    else
        echo -e "${YELLOW}Lock huerfano encontrado (PID $LOCK_PID ya no existe). Limpiando...${NC}"
        rm -f "$LOCK_FILE"
    fi
fi
echo $$ > "$LOCK_FILE"
trap 'rm -f "$LOCK_FILE"' EXIT

# ── Funcion de rollback ──
rollback() {
    echo -e "${RED}================================================${NC}"
    echo -e "${RED}ROLLBACK: Restaurando estado anterior...${NC}"
    echo -e "${RED}================================================${NC}"

    cd "$APP_DIR"

    # Revertir git al commit anterior
    if [ -n "${PREV_COMMIT:-}" ]; then
        echo -e "${YELLOW}Revirtiendo a commit $PREV_COMMIT...${NC}"
        git reset --hard "$PREV_COMMIT"
    fi

    # Restaurar BD si hay backup
    LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/db_*.sql.gz 2>/dev/null | head -1)
    if [ -n "${LATEST_BACKUP:-}" ]; then
        echo -e "${YELLOW}Restaurando base de datos desde $LATEST_BACKUP...${NC}"
        DB_NAME=$(grep -oP "^DB_DATABASE=\K.*" "$APP_DIR/.env")
        gunzip -c "$LATEST_BACKUP" | mysql "$DB_NAME"
    fi

    # Recachear y levantar
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    chown -R "$LARAVEL_USER":"$LARAVEL_USER" storage bootstrap/cache
    systemctl reload php-fpm
    php artisan up

    echo -e "${RED}Rollback completado. Revisa los logs en $LOG_FILE${NC}"
    exit 1
}

# Si se invoco con --rollback, ejecutar rollback directo
if [ "$DO_ROLLBACK" = true ]; then
    rollback
fi

# ── Manejo de errores con rollback automatico ──
manejar_error() {
    echo -e "${RED}ERROR CRITICO en linea $1. Iniciando rollback...${NC}"
    rollback
}
trap 'manejar_error $LINENO' ERR

# ==============================================================================
# PRE-FLIGHT CHECKS
# ==============================================================================
echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}DESPLIEGUE DAWSCAPE - $DEPLOY_DATE${NC}"
echo -e "${BLUE}================================================${NC}"

log_step "PRE" "Ejecutando comprobaciones previas..."

# Verificar ejecucion como root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Este script debe ejecutarse como root (sudo).${NC}"
    exit 1
fi

# Verificar directorio
if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}No existe el directorio $APP_DIR${NC}"
    exit 1
fi
cd "$APP_DIR"

# Verificar .env
if [ ! -f ".env" ]; then
    echo -e "${RED}No se encuentra .env en $APP_DIR${NC}"
    exit 1
fi

# Verificar espacio en disco (minimo 500MB)
AVAIL_KB=$(df "$APP_DIR" --output=avail | tail -1 | tr -d ' ')
if [ "$AVAIL_KB" -lt 512000 ]; then
    echo -e "${RED}Espacio insuficiente: $(( AVAIL_KB / 1024 ))MB disponibles (minimo 500MB).${NC}"
    exit 1
fi

# Verificar servicios activos
for svc in nginx php-fpm mariadb; do
    if ! systemctl is-active --quiet "$svc"; then
        echo -e "${RED}El servicio $svc no esta activo.${NC}"
        exit 1
    fi
done

# Verificar binarios necesarios
for bin in php composer node npm git mysql; do
    if ! command -v "$bin" &>/dev/null; then
        echo -e "${RED}Binario '$bin' no encontrado en PATH.${NC}"
        exit 1
    fi
done

echo -e "${CYAN}    PHP:      $(php -r 'echo PHP_VERSION;')${NC}"
echo -e "${CYAN}    Node:     $(node -v)${NC}"
echo -e "${CYAN}    Composer: $(composer -V 2>/dev/null | head -1)${NC}"
log_ok

# ==============================================================================
# DESPLIEGUE
# ==============================================================================

# 1. MODO MANTENIMIENTO
log_step "1/9" "Activando modo mantenimiento..."
php artisan down --retry=30 --refresh=15 || true
log_ok

# 2. BACKUP DE BASE DE DATOS
log_step "2/9" "Creando backup de base de datos..."
mkdir -p "$BACKUP_DIR"
DB_NAME=$(grep -oP "^DB_DATABASE=\K.*" .env)
DB_USER=$(grep -oP "^DB_USERNAME=\K.*" .env)
DB_PASS=$(grep -oP "^DB_PASSWORD=\K.*" .env)
BACKUP_FILE="$BACKUP_DIR/db_$(date +%Y%m%d_%H%M%S).sql.gz"
mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" --single-transaction --quick | gzip > "$BACKUP_FILE"
# Rotar backups: mantener solo los ultimos MAX_BACKUPS
ls -t "$BACKUP_DIR"/db_*.sql.gz 2>/dev/null | tail -n +$((MAX_BACKUPS + 1)) | xargs -r rm -f
log_ok

# 3. GUARDAR COMMIT ACTUAL (para rollback)
PREV_COMMIT=$(git rev-parse HEAD)

# 4. OBTENER CODIGO NUEVO
log_step "3/9" "Descargando cambios de Git..."
# Detectar si hay cambios en frontend antes del pull
OLD_FRONTEND_HASH=$(find resources/js resources/css resources/views package.json vite.config.* -type f 2>/dev/null | sort | xargs md5sum 2>/dev/null | md5sum | cut -d' ' -f1)
git pull origin main
NEW_FRONTEND_HASH=$(find resources/js resources/css resources/views package.json vite.config.* -type f 2>/dev/null | sort | xargs md5sum 2>/dev/null | md5sum | cut -d' ' -f1)
log_ok

# 5. DEPENDENCIAS PHP
log_step "4/9" "Actualizando dependencias de Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-progress
log_ok

# 6. COMPILAR FRONTEND (solo si cambio algo o se fuerza)
if [ "$SKIP_NPM" = true ]; then
    log_step "5/9" "Compilacion frontend OMITIDA (--skip-npm)"
elif [ "$OLD_FRONTEND_HASH" = "$NEW_FRONTEND_HASH" ]; then
    log_step "5/9" "Sin cambios en frontend, omitiendo compilacion Vite"
else
    log_step "5/9" "Compilando assets (NPM + Vite)..."
    npm ci --no-audit --no-fund
    npm run build
fi
log_ok

# 7. MIGRACIONES
if [ "$SKIP_MIGRATIONS" = true ]; then
    log_step "6/9" "Migraciones OMITIDAS (--skip-migrations)"
else
    log_step "6/9" "Ejecutando migraciones..."
    php artisan migrate --force
fi
log_ok

# 8. CACHE Y OPTIMIZACION
log_step "7/9" "Optimizando Laravel (cache de config, rutas, vistas, eventos)..."
php artisan optimize:clear
php artisan optimize
log_ok

# 9. PERMISOS Y SELINUX
log_step "8/9" "Restaurando permisos y contexto SELinux..."
chown -R "$LARAVEL_USER":"$LARAVEL_USER" storage bootstrap/cache public/build
chmod -R 775 storage bootstrap/cache
chmod -R 755 public/build
# SELinux: permitir que nginx/php-fpm escriban en storage
if command -v restorecon &>/dev/null; then
    restorecon -Rv storage bootstrap/cache public/build > /dev/null 2>&1
    setsebool -P httpd_can_network_connect 1 2>/dev/null || true
    setsebool -P httpd_unified 1 2>/dev/null || true
fi
log_ok

# 10. RELOAD DE SERVICIOS (reload, no restart = zero downtime)
log_step "9/9" "Recargando PHP-FPM y Nginx..."
systemctl reload php-fpm
nginx -t && systemctl reload nginx
log_ok

# FIN
php artisan up

DEPLOY_END=$(date +%s)
DEPLOY_DURATION=$(( DEPLOY_END - DEPLOY_START ))

echo ""
echo -e "${GREEN}================================================${NC}"
echo -e "${GREEN}DESPLIEGUE COMPLETADO - $(date +"%H:%M:%S")${NC}"
echo -e "${GREEN}Duracion: ${DEPLOY_DURATION}s${NC}"
echo -e "${GREEN}Commit:   $(git rev-parse --short HEAD)${NC}"
echo -e "${GREEN}Backup:   $BACKUP_FILE${NC}"
echo -e "${GREEN}Log:      $LOG_FILE${NC}"
echo -e "${GREEN}================================================${NC}"
