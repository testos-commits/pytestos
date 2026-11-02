#!/usr/bin/env bash
# TEST SERVER ONLY - sets up the PY dashboard with made-up demo data.
# Run as root on a fresh Ubuntu 24.04 droplet, from inside the cloned repo:
#   bash deploy/setup-test-server.sh
# It never connects to the live database, AWS buckets, email or push notifications.
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
IP="$(curl -s -4 https://ifconfig.me || hostname -I | awk '{print $1}')"
DB_PASS="$(openssl rand -hex 16)"
ADMIN_PASS="$(openssl rand -base64 18 | tr -d '/+=' | cut -c1-16)"
export DEBIAN_FRONTEND=noninteractive

echo ">>> Installing system packages (PHP 8.3, MariaDB, Redis, Nginx, Node)"
apt-get update -q
apt-get install -y -q nginx mariadb-server redis-server unzip git curl ufw \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-redis
if ! command -v node >/dev/null; then
  curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
  apt-get install -y -q nodejs
fi
if ! command -v composer >/dev/null; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo ">>> Firewall: allow SSH and web only"
ufw allow OpenSSH >/dev/null; ufw allow 'Nginx Full' >/dev/null; ufw --force enable >/dev/null

echo ">>> Test database"
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS pycasting;
  CREATE USER IF NOT EXISTS 'py'@'localhost' IDENTIFIED BY '${DB_PASS}';
  ALTER USER 'py'@'localhost' IDENTIFIED BY '${DB_PASS}';
  GRANT ALL ON pycasting.* TO 'py'@'localhost'; FLUSH PRIVILEGES;"

echo ">>> Test settings file (fake values only)"
cat > "$APP_DIR/.env" <<ENV
APP_NAME="PY Casting TEST"
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=http://${IP}
LOG_CHANNEL=stack
LOG_LEVEL=warning
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pycasting
DB_USERNAME=py
DB_PASSWORD=${DB_PASS}
DB_OLD_DATABASE=pycasting
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=600
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
MAIL_MAILER=log
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="PY Casting TEST"
AWS_ACCESS_KEY_ID=test
AWS_SECRET_ACCESS_KEY=test
AWS_DEFAULT_REGION=eu-central-1
AWS_DEFAULT_REGION_2=eu-central-1
AWS_ADMIN_BUCKET=test-admin
AWS_ADMIN_BUCKET_REGION=eu-central-1
AWS_ADMIN_BASE_URL=http://${IP}/storage/
AWS_USER_BUCKET=test-user
AWS_USER_BUCKET_REGION=eu-central-1
AWS_USER_BUCKET_URL=http://${IP}/storage/
AWS_EU_BUCKET=test-eu
AWS_EU_BUCKET_REGION=eu-central-1
AWS_EU_BUCKET_URL=http://${IP}/storage/
AWS_RAW_VIDEO_BUCKET=test-raw
AWS_RAW_VIDEO_BUCKET_REGION=eu-central-1
AWS_RAW_VIDEO_BUCKET_URL=http://${IP}/storage/
AWS_COMPRESSED_VIDEO_BUCKET=test-compressed
AWS_COMPRESSED_VIDEO_BUCKET_REGION=eu-central-1
AWS_COMPRESSED_VIDEO_BUCKET_URL=http://${IP}/storage/
AWS_DATABASE_BACKUP_BUCKET=test-backup
AWS_DATABASE_BACKUP_BUCKET_REGION=eu-central-1
ENABLE_PUSH_NOTIFICATIONS=false
TELESCOPE_ENABLED=false
PULSE_ENABLED=false
TEST_LOCAL_DISKS=true
TEST_ADMIN_PASSWORD=${ADMIN_PASS}
ENV

echo ">>> Installing app libraries and building"
cd "$APP_DIR"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-interaction --no-progress --no-dev --optimize-autoloader
php artisan key:generate --force
npm install --no-audit --no-fund
npx vite build

echo ">>> Database tables and demo data"
php artisan migrate --force
php artisan db:seed --class=PermissionSeeder --force
php artisan permission:cache-reset || true
php artisan db:seed --class=RolePermissionSeeder --force
php artisan db:seed --class=TestDemoSeeder --force
php artisan storage:link || true
php artisan config:cache

chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo ">>> Web server"
cat > /etc/nginx/sites-available/py <<NGINX
server {
    listen 80 default_server;
    server_name _;
    root ${APP_DIR}/public;
    index index.php;
    client_max_body_size 20M;
    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    location ~ /\.(?!well-known) { deny all; }
}
NGINX
ln -sf /etc/nginx/sites-available/py /etc/nginx/sites-enabled/py
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
chmod o+x /root "$APP_DIR" 2>/dev/null || true

echo
echo "=============================================="
echo " TEST DASHBOARD READY"
echo " Open:      http://${IP}/admin"
echo " Email:     admin@test.local"
echo " Password:  ${ADMIN_PASS}"
echo " (Super director test login: director@test.local, same password)"
echo "=============================================="
