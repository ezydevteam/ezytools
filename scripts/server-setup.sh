#!/bin/bash
# =============================================================
# EzyTools Server Setup Script
# Target: Hostinger KVM-2 VPS — Ubuntu 22.04 LTS
# Run once on a fresh server: sudo bash scripts/server-setup.sh
# =============================================================

set -e

echo "╔══════════════════════════════════════╗"
echo "║   EzyTools Server Setup              ║"
echo "║   Hostinger KVM-2 · Ubuntu 22.04     ║"
echo "╚══════════════════════════════════════╝"
echo ""

# ─── System Update ───
echo "--- Updating system packages ---"
apt update && apt upgrade -y

# ─── Core Packages ───
echo "--- Installing core packages ---"
apt install -y \
  nginx \
  mysql-server \
  redis-server \
  git \
  curl \
  unzip \
  supervisor \
  certbot \
  python3-certbot-nginx \
  ufw \
  fail2ban \
  htop \
  cron \
  ghostscript \
  qpdf

# ─── PHP 8.2 ───
echo "--- Installing PHP 8.2 ---"
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y \
  php8.2-fpm \
  php8.2-mysql \
  php8.2-redis \
  php8.2-curl \
  php8.2-mbstring \
  php8.2-xml \
  php8.2-zip \
  php8.2-gd \
  php8.2-imagick \
  php8.2-intl \
  php8.2-bcmath \
  php8.2-fileinfo \
  php8.2-opcache

# ─── PHP Config Tweaks ───
echo "--- Configuring PHP ---"
PHP_INI="/etc/php/8.2/fpm/php.ini"
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 55M/' $PHP_INI
sed -i 's/post_max_size = .*/post_max_size = 60M/' $PHP_INI
sed -i 's/memory_limit = .*/memory_limit = 256M/' $PHP_INI
sed -i 's/max_execution_time = .*/max_execution_time = 300/' $PHP_INI
sed -i 's/max_input_time = .*/max_input_time = 300/' $PHP_INI

# OPcache settings
cat >> $PHP_INI <<'OPCACHE'

; OPcache Production Settings
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
OPCACHE

# ─── Composer ───
echo "--- Installing Composer ---"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# ─── Node.js 20 ───
echo "--- Installing Node.js 20 ---"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# ─── MySQL Secure ───
echo "--- Securing MySQL ---"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$(openssl rand -base64 24)';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "FLUSH PRIVILEGES;"
echo "⚠️  Root MySQL password changed — save it from output above!"

# ─── Create App User ───
echo "--- Creating app database ---"
DB_PASS=$(openssl rand -base64 24)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ezytools_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER IF NOT EXISTS 'ezytools_user'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ezytools_db.* TO 'ezytools_user'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"
echo "✅ Database user created — password: ${DB_PASS}"

# ─── Redis Config ───
echo "--- Configuring Redis ---"
REDIS_PASS=$(openssl rand -base64 16)
sed -i "s/# requirepass .*/requirepass ${REDIS_PASS}/" /etc/redis/redis.conf
sed -i 's/bind 127.0.0.1/bind 127.0.0.1/' /etc/redis/redis.conf
systemctl restart redis

echo "✅ Redis password set: ${REDIS_PASS}"

# ─── Firewall ───
echo "--- Configuring UFW firewall ---"
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 80/tcp
ufw allow 443/tcp
ufw deny 3306/tcp
ufw deny 6379/tcp
ufw --force enable

# ─── Fail2Ban ───
echo "--- Configuring Fail2Ban ---"
cat > /etc/fail2ban/jail.local <<'F2B'
[DEFAULT]
bantime  = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port    = ssh
logpath = /var/log/auth.log

[nginx-req-limit]
enabled  = true
filter   = nginx-req-limit
logpath  = /var/log/nginx/error.log
maxretry = 10

[nginx-botsearch]
enabled  = true
filter   = nginx-botsearch
logpath  = /var/log/nginx/access.log
maxretry = 5
F2B
systemctl restart fail2ban

# ─── Create Project Directory ───
echo "--- Setting up project directory ---"
mkdir -p /var/www/ezytools
chown -R www-data:www-data /var/www/ezytools

# ─── Fonts Directory ───
mkdir -p /var/www/ezytools/public/fonts/bangla

# ─── Cron Job ───
echo "--- Setting up Laravel scheduler cron ---"
(crontab -u www-data -l 2>/dev/null; echo "* * * * * cd /var/www/ezytools && php artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   ✅ Server setup complete!              ║"
echo "║                                          ║"
echo "║   Next steps:                            ║"
echo "║   1. Upload project files to /var/www/   ║"
echo "║   2. Copy .env and configure             ║"
echo "║   3. Run: bash scripts/deploy.sh         ║"
echo "║   4. Set up SSL with certbot             ║"
echo "╚══════════════════════════════════════════╝"
