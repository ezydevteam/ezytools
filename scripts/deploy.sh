#!/bin/bash
# =============================================================
# EzyTools Deployment Script
# Run for every deployment: sudo bash scripts/deploy.sh
# =============================================================

set -e

PROJECT_DIR="/var/www/ezytools"
BACKUP_DIR="/var/backups/ezytools"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "╔══════════════════════════════════════╗"
echo "║   EzyTools Deployment                ║"
echo "║   $TIMESTAMP                          ║"
echo "╚══════════════════════════════════════╝"
echo ""

cd $PROJECT_DIR

# ─── 1. Backup Database ───
echo "--- [1/9] Backing up database ---"
mkdir -p $BACKUP_DIR

# Source DB credentials from .env
DB_NAME=$(grep ^DB_DATABASE .env | cut -d '=' -f2)
DB_USER=$(grep ^DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep ^DB_PASSWORD .env | cut -d '=' -f2)

mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
  | gzip > "$BACKUP_DIR/db_$TIMESTAMP.sql.gz"

# Keep only last 7 backups
ls -t $BACKUP_DIR/db_*.sql.gz 2>/dev/null | tail -n +8 | xargs -r rm
echo "  ✅ Database backed up"

# ─── 2. Maintenance Mode ───
echo "--- [2/9] Enabling maintenance mode ---"
php artisan down --secret="ezytools-maintenance-bypass-$(date +%Y)" --retry=60
echo "  ✅ Maintenance mode enabled"

# ─── 3. Pull Latest Code ───
echo "--- [3/9] Pulling latest code ---"
git pull origin main
echo "  ✅ Code updated"

# ─── 4. Install PHP Dependencies ───
echo "--- [4/9] Installing PHP dependencies ---"
composer install --no-dev --optimize-autoloader --no-interaction --quiet
echo "  ✅ Composer dependencies installed"

# ─── 5. Build Frontend ───
echo "--- [5/9] Building frontend assets ---"
npm ci --production=false --silent
npm run build
echo "  ✅ Frontend built"

# ─── 6. Run Migrations ───
echo "--- [6/9] Running migrations ---"
php artisan migrate --force
echo "  ✅ Migrations complete"

# ─── 7. Clear & Rebuild Caches ───
echo "--- [7/9] Rebuilding caches ---"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache 2>/dev/null || true
echo "  ✅ Caches rebuilt"

# ─── 8. Restart Services ───
echo "--- [8/9] Restarting services ---"
php artisan queue:restart
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
sudo supervisorctl restart ezytools-worker:* 2>/dev/null || true
echo "  ✅ Services restarted"

# ─── 9. Disable Maintenance Mode ───
echo "--- [9/9] Going live ---"
php artisan up
echo "  ✅ Site is live!"

echo ""
echo "╔══════════════════════════════════════╗"
echo "║   ✅ Deployment complete!             ║"
echo "║   Timestamp: $TIMESTAMP               ║"
echo "╚══════════════════════════════════════╝"
