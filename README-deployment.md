# 🚀 EzyTools — Deployment Guide

> **Target Server:** Hostinger KVM-2 VPS (Ubuntu 22.04 LTS)  
> **Stack:** PHP 8.2 · Nginx · MySQL 8.0 · Redis · Node 20

---

## 📋 Table of Contents

1. [Server Specs](#server-specs)
2. [Initial Server Setup](#initial-server-setup)
3. [Project Deployment](#project-deployment)
4. [SSL Certificate](#ssl-certificate)
5. [Post-Deployment Checks](#post-deployment-checks)
6. [Security Hardening](#security-hardening)
7. [Scheduled Tasks](#scheduled-tasks)
8. [Monitoring & Maintenance](#monitoring--maintenance)
9. [Rollback Procedure](#rollback-procedure)
10. [Troubleshooting](#troubleshooting)

---

## Server Specs

| Resource | Value |
|----------|-------|
| OS       | Ubuntu 22.04 LTS |
| RAM      | 8 GB |
| CPU      | 2 vCPU |
| SSD      | 100 GB NVMe |
| PHP      | 8.2 |
| Web      | Nginx |
| Database | MySQL 8.0 |
| Cache    | Redis |

---

## Initial Server Setup

> **Run once** on a fresh Hostinger KVM-2 VPS.

### 1. SSH into Server

```bash
ssh root@YOUR_SERVER_IP
```

### 2. Run Setup Script

```bash
# Upload and run the server setup script
scp scripts/server-setup.sh root@YOUR_SERVER_IP:/root/
ssh root@YOUR_SERVER_IP "bash /root/server-setup.sh"
```

This script installs:
- Nginx, MySQL 8.0, Redis, PHP 8.2 with all required extensions
- Composer, Node.js 20
- Ghostscript, qpdf (for PDF tools)
- UFW firewall, Fail2Ban
- Supervisor (for queue workers)
- Certbot (for SSL)

### 3. Save Generated Credentials

The setup script outputs:
- **MySQL root password** — save securely
- **MySQL app password** (`ezytools_user`) — use in `.env`
- **Redis password** — use in `.env`

---

## Project Deployment

### First-Time Deployment

```bash
# 1. Clone the repository
cd /var/www
git clone YOUR_REPO_URL ezytools
cd ezytools

# 2. Set ownership
chown -R www-data:www-data /var/www/ezytools

# 3. Create .env from example
cp .env.example .env

# 4. Edit .env with production values
nano .env
```

#### Production `.env` — Critical Values

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ezytools.app

DB_DATABASE=ezytools_db
DB_USERNAME=ezytools_user
DB_PASSWORD=<generated-password>

REDIS_PASSWORD=<generated-password>

SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

```bash
# 5. Generate app key
php artisan key:generate

# 6. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 7. Run migrations & seed
php artisan migrate --force
php artisan db:seed --force  # If needed

# 8. Create storage symlink
php artisan storage:link

# 9. Set permissions
chmod -R 775 storage bootstrap/cache
chmod 600 .env
chown -R www-data:www-data storage bootstrap/cache

# 10. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Nginx Configuration

```bash
# Copy config
cp scripts/nginx-ezytools.conf /etc/nginx/sites-available/ezytools

# Enable site
ln -s /etc/nginx/sites-available/ezytools /etc/nginx/sites-enabled/

# Remove default
rm -f /etc/nginx/sites-enabled/default

# Test and reload
nginx -t && systemctl reload nginx
```

### Supervisor (Queue Workers)

```bash
# Copy config
cp scripts/supervisor-ezytools-worker.conf /etc/supervisor/conf.d/

# Start workers
supervisorctl reread
supervisorctl update
supervisorctl start ezytools-worker:*
```

### Fail2Ban

```bash
cp scripts/fail2ban-jail.conf /etc/fail2ban/jail.local
systemctl restart fail2ban
```

### Subsequent Deployments

```bash
cd /var/www/ezytools
sudo bash scripts/deploy.sh
```

The deploy script handles:
1. Database backup
2. Maintenance mode
3. `git pull`
4. `composer install` + `npm run build`
5. Migrations
6. Cache rebuild
7. Service restart
8. Go live

---

## SSL Certificate

```bash
# Get free SSL from Let's Encrypt
certbot --nginx -d ezytools.app -d www.ezytools.app \
  --non-interactive --agree-tos -m admin@ezytools.app

# Verify auto-renewal is set up
certbot renew --dry-run

# Check crontab
crontab -l | grep certbot
# Should show: 0 12 * * * certbot renew --quiet
```

---

## Post-Deployment Checks

### Run Pre-Production Audit

```bash
cd /var/www/ezytools
php artisan audit:pre-production
```

This runs 4 sub-audits:
1. **Security** — APP_DEBUG, keys, sessions, passwords
2. **SQL Injection** — code scanning for unsafe queries
3. **API Key Exposure** — checks frontend build for leaked secrets
4. **SEO** — meta tags, FAQs, sitemaps for all tools

### Quick Verification

```bash
# Test site is responding
curl -I https://ezytools.app
# Expected: HTTP/2 200

# Test redirect
curl -I http://ezytools.app
# Expected: 301 → https://

# Test .env is blocked
curl -I https://ezytools.app/.env
# Expected: 404

# Check queue workers
supervisorctl status

# Check cron
crontab -u www-data -l
```

---

## Security Hardening

### Firewall Rules (UFW)

```bash
ufw status verbose
# Should show:
#   22/tcp    ALLOW IN
#   80/tcp    ALLOW IN
#   443/tcp   ALLOW IN
#   3306/tcp  DENY IN
#   6379/tcp  DENY IN
```

### File Permissions

```bash
# Application files
chown -R www-data:www-data /var/www/ezytools
chmod -R 755 /var/www/ezytools

# Storage & cache (writable)
chmod -R 775 /var/www/ezytools/storage
chmod -R 775 /var/www/ezytools/bootstrap/cache

# .env (owner-only read)
chmod 600 /var/www/ezytools/.env

# Private uploads
chmod -R 750 /var/www/ezytools/storage/app/private/
```

### Individual Audit Commands

```bash
php artisan audit:security       # Security config
php artisan audit:sql-injection  # Code scanning
php artisan audit:api-keys       # Secret exposure
php artisan audit:seo            # SEO completeness
```

---

## Scheduled Tasks

All tasks are registered in `routes/console.php`:

| Schedule | Command | Purpose |
|----------|---------|---------|
| Every 15 min | `cleanup:temp-files` | Delete expired PDFs, voice files, stale DB records |
| Hourly | `cleanup:uploads --force` | Clean orphan upload files |
| Daily 3 AM | `cleanup:uploads --hours=48` | Deep cleanup of old logs |
| Daily 9 AM | `subscriptions:notify-expiring` | Email expiring subscription users |
| Hourly | `cache:prune-stale-tags` | Clear expired cache tags |

### Cron Entry

```bash
# Verify cron is set for www-data:
crontab -u www-data -l
# Should show:
# * * * * * cd /var/www/ezytools && php artisan schedule:run >> /dev/null 2>&1
```

---

## Monitoring & Maintenance

### Log Files

| Log | Location |
|-----|----------|
| Laravel | `storage/logs/laravel.log` |
| Queue Workers | `storage/logs/worker.log` |
| Nginx Access | `/var/log/nginx/ezytools-access.log` |
| Nginx Error | `/var/log/nginx/ezytools-error.log` |
| Fail2Ban | `/var/log/fail2ban.log` |

### Database Backups

Automatic backups run during each deployment. Manual backup:

```bash
mysqldump -u ezytools_user -p ezytools_db | gzip > /var/backups/ezytools/manual_$(date +%Y%m%d).sql.gz
```

### Disk Space

```bash
df -h /
du -sh /var/www/ezytools/storage/
du -sh /var/backups/ezytools/
```

### Service Status

```bash
systemctl status nginx
systemctl status php8.2-fpm
systemctl status mysql
systemctl status redis
systemctl status supervisor
supervisorctl status
```

---

## Rollback Procedure

### Quick Rollback (Code)

```bash
cd /var/www/ezytools
php artisan down

# Rollback to previous commit
git log --oneline -5        # Find the commit hash
git checkout <commit-hash>

composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache

php artisan up
```

### Database Rollback

```bash
# Rollback last migration
php artisan migrate:rollback --step=1

# Restore from backup
gunzip < /var/backups/ezytools/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u ezytools_user -p ezytools_db
```

---

## Troubleshooting

### 502 Bad Gateway

```bash
# Check PHP-FPM
systemctl status php8.2-fpm
systemctl restart php8.2-fpm

# Check Nginx error log
tail -20 /var/log/nginx/ezytools-error.log
```

### Queue Not Processing

```bash
supervisorctl status ezytools-worker:*
supervisorctl restart ezytools-worker:*

# Check worker log
tail -50 /var/www/ezytools/storage/logs/worker.log
```

### Permission Denied

```bash
chown -R www-data:www-data /var/www/ezytools/storage
chmod -R 775 /var/www/ezytools/storage
```

### Cache Issues

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Then rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### High Memory Usage

```bash
# Check processes
htop

# Restart services
systemctl restart php8.2-fpm
supervisorctl restart ezytools-worker:*

# Check for runaway queries
mysql -e "SHOW PROCESSLIST;"
```

---

## 📝 Version History

| Version | Date | Notes |
|---------|------|-------|
| v1.0.0  | TBD  | Initial production release |

---

*Generated by EzyTools Pre-Production Audit System*
