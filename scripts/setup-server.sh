#!/bin/bash
set -e

# ============================================================
#  GiaVangHN — Server Setup Script
#  Ubuntu 22.04 + Nginx + PHP 8.2-FPM + SSL (Let's Encrypt)
# ============================================================

DOMAIN="giavanghn.com"
WWW_DOMAIN="www.giavanghn.com"
APP_DIR="/var/www/giavanghn"
NGINX_CONF="/etc/nginx/sites-available/${DOMAIN}"
PHP_VERSION="8.2"
DB_NAME="giavanghn"
DB_USER="giavanghn"
SWAP_SIZE="2G"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

log()  { echo -e "${GREEN}[✓]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
err()  { echo -e "${RED}[✗]${NC} $1"; exit 1; }
step() { echo -e "\n${CYAN}══════════════════════════════════════${NC}"; echo -e "${CYAN}  $1${NC}"; echo -e "${CYAN}══════════════════════════════════════${NC}"; }

# Must run as root
[[ $EUID -ne 0 ]] && err "Chạy script với quyền root: sudo bash setup-server.sh"

# ──────────────────────────────────────
step "1/12 — Cập nhật hệ thống"
# ──────────────────────────────────────
apt update && apt upgrade -y
apt install -y software-properties-common curl git unzip ufw fail2ban
log "Hệ thống đã cập nhật"

# ──────────────────────────────────────
step "2/12 — Cài PHP ${PHP_VERSION} + Extensions"
# ──────────────────────────────────────
if ! command -v php${PHP_VERSION} &>/dev/null; then
    add-apt-repository ppa:ondrej/php -y
    apt update
fi
apt install -y php${PHP_VERSION}-fpm php${PHP_VERSION}-cli php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-gd \
    php${PHP_VERSION}-bcmath php${PHP_VERSION}-intl php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-readline
log "PHP ${PHP_VERSION} đã cài xong"

# ──────────────────────────────────────
step "3/12 — Tối ưu PHP-FPM & OPcache"
# ──────────────────────────────────────
FPM_POOL="/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf"
sed -i 's/^pm = .*/pm = dynamic/' "$FPM_POOL"
sed -i 's/^pm\.max_children = .*/pm.max_children = 10/' "$FPM_POOL"
sed -i 's/^pm\.start_servers = .*/pm.start_servers = 3/' "$FPM_POOL"
sed -i 's/^pm\.min_spare_servers = .*/pm.min_spare_servers = 2/' "$FPM_POOL"
sed -i 's/^pm\.max_spare_servers = .*/pm.max_spare_servers = 5/' "$FPM_POOL"

# Thêm pm.max_requests nếu chưa có
grep -q '^pm\.max_requests' "$FPM_POOL" && \
    sed -i 's/^pm\.max_requests = .*/pm.max_requests = 500/' "$FPM_POOL" || \
    echo 'pm.max_requests = 500' >> "$FPM_POOL"

PHP_INI="/etc/php/${PHP_VERSION}/fpm/php.ini"
sed -i 's/^memory_limit = .*/memory_limit = 128M/' "$PHP_INI"
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 10M/' "$PHP_INI"
sed -i 's/^post_max_size = .*/post_max_size = 12M/' "$PHP_INI"
sed -i 's/^max_execution_time = .*/max_execution_time = 30/' "$PHP_INI"
sed -i 's/^;*opcache\.enable=.*/opcache.enable=1/' "$PHP_INI"
sed -i 's/^;*opcache\.memory_consumption=.*/opcache.memory_consumption=128/' "$PHP_INI"
sed -i 's/^;*opcache\.interned_strings_buffer=.*/opcache.interned_strings_buffer=16/' "$PHP_INI"
sed -i 's/^;*opcache\.max_accelerated_files=.*/opcache.max_accelerated_files=10000/' "$PHP_INI"
sed -i 's/^;*opcache\.validate_timestamps=.*/opcache.validate_timestamps=0/' "$PHP_INI"

systemctl restart php${PHP_VERSION}-fpm
log "PHP-FPM đã tối ưu (10 workers, OPcache ON)"

# ──────────────────────────────────────
step "4/12 — Cài MySQL"
# ──────────────────────────────────────
if ! command -v mysql &>/dev/null; then
    apt install -y mysql-server
    systemctl enable mysql
fi

# Tạo database nếu chưa có
if ! mysql -u root -e "USE ${DB_NAME}" 2>/dev/null; then
    read -sp "$(echo -e "${YELLOW}Nhập mật khẩu cho MySQL user '${DB_USER}': ${NC}")" DB_PASS
    echo
    [[ -z "$DB_PASS" ]] && err "Mật khẩu không được để trống"
    mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
    log "Database '${DB_NAME}' và user '${DB_USER}' đã tạo"
else
    warn "Database '${DB_NAME}' đã tồn tại, bỏ qua"
fi

# ──────────────────────────────────────
step "5/12 — Cài Nginx"
# ──────────────────────────────────────
apt install -y nginx
systemctl enable nginx
log "Nginx đã cài"

# ──────────────────────────────────────
step "6/12 — Cài Composer"
# ──────────────────────────────────────
if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi
log "Composer $(composer --version 2>/dev/null | head -1)"

# ──────────────────────────────────────
step "7/12 — Cài Node.js 20"
# ──────────────────────────────────────
if ! command -v node &>/dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
fi
log "Node $(node -v), npm $(npm -v)"

# ──────────────────────────────────────
step "8/12 — Cấu hình Nginx cho ${DOMAIN}"
# ──────────────────────────────────────
cat > "$NGINX_CONF" <<'NGINX'
server {
    listen 80;
    listen [::]:80;
    server_name DOMAIN_PLACEHOLDER WWW_PLACEHOLDER;
    root APP_DIR_PLACEHOLDER/public;
    index index.php;

    charset utf-8;

    # ── Gzip ──
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 256;
    gzip_types
        text/plain text/css text/javascript text/xml
        application/json application/javascript application/xml
        application/rss+xml image/svg+xml font/woff2;

    # ── Static cache: Vite hashed → immutable ──
    location /build/ {
        expires max;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    location ~* \.(ico|jpg|jpeg|png|webp|gif|svg|woff2|woff|ttf|css|js)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public";
    }

    # ── Security headers ──
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/phpVERSION_PLACEHOLDER-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffering on;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 16 16k;
    }

    # Block hidden files (except .well-known for SSL)
    location ~ /\.(?!well-known) {
        deny all;
    }
}
NGINX

# Replace placeholders
sed -i "s|DOMAIN_PLACEHOLDER|${DOMAIN}|g" "$NGINX_CONF"
sed -i "s|WWW_PLACEHOLDER|${WWW_DOMAIN}|g" "$NGINX_CONF"
sed -i "s|APP_DIR_PLACEHOLDER|${APP_DIR}|g" "$NGINX_CONF"
sed -i "s|phpVERSION_PLACEHOLDER|php${PHP_VERSION}|g" "$NGINX_CONF"

ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
log "Nginx config cho ${DOMAIN} đã tạo"

# ──────────────────────────────────────
step "9/12 — Cài SSL (Let's Encrypt)"
# ──────────────────────────────────────
apt install -y certbot python3-certbot-nginx

echo ""
warn "Trước khi tiếp tục, hãy đảm bảo DNS đã trỏ:"
echo "   A record:  ${DOMAIN}     → $(curl -s ifconfig.me)"
echo "   A record:  ${WWW_DOMAIN} → $(curl -s ifconfig.me)"
echo ""
read -p "$(echo -e "${YELLOW}DNS đã trỏ xong? (y/n): ${NC}")" DNS_READY

if [[ "$DNS_READY" == "y" || "$DNS_READY" == "Y" ]]; then
    read -p "$(echo -e "${YELLOW}Email cho Let's Encrypt (nhận thông báo hết hạn): ${NC}")" LE_EMAIL
    [[ -z "$LE_EMAIL" ]] && err "Email không được để trống"

    certbot --nginx \
        -d "${DOMAIN}" -d "${WWW_DOMAIN}" \
        --non-interactive --agree-tos \
        --email "${LE_EMAIL}" \
        --redirect

    # Verify auto-renew
    certbot renew --dry-run
    log "SSL đã cài + auto-renew OK"
else
    warn "Bỏ qua SSL. Chạy lại sau: certbot --nginx -d ${DOMAIN} -d ${WWW_DOMAIN}"
fi

# ──────────────────────────────────────
step "10/12 — Deploy ứng dụng Laravel"
# ──────────────────────────────────────
mkdir -p "$APP_DIR"

if [[ ! -f "${APP_DIR}/artisan" ]]; then
    warn "Chưa có source code tại ${APP_DIR}"
    echo "  Upload bằng 1 trong 2 cách:"
    echo "  a) git clone <repo_url> ${APP_DIR}"
    echo "  b) rsync từ máy local"
    echo ""
    read -p "$(echo -e "${YELLOW}Source đã có tại ${APP_DIR}? (y/n): ${NC}")" SRC_READY
    [[ "$SRC_READY" != "y" && "$SRC_READY" != "Y" ]] && err "Hãy upload source rồi chạy lại script"
fi

cd "$APP_DIR"

# Composer
composer install --no-dev --optimize-autoloader

# Node build
if [[ -f "package.json" ]]; then
    npm ci
    npm run build
    rm -rf node_modules
    log "Assets đã build"
fi

# .env
if [[ ! -f ".env" ]]; then
    cp .env.example .env
    php artisan key:generate

    # Nếu có DB_PASS từ bước trước
    if [[ -n "$DB_PASS" ]]; then
        sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
        sed -i "s|^# DB_HOST=.*|DB_HOST=127.0.0.1|" .env
        sed -i "s|^# DB_PORT=.*|DB_PORT=3306|" .env
        sed -i "s|^# DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
        sed -i "s|^# DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
        sed -i "s|^# DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
        # Nếu không phải dạng comment
        sed -i "s|^DB_HOST=.*|DB_HOST=127.0.0.1|" .env
        sed -i "s|^DB_PORT=.*|DB_PORT=3306|" .env
        sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
        sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
        sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env
    fi

    sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
    sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" .env
    sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|" .env
    log ".env đã cấu hình"
else
    warn ".env đã tồn tại, bỏ qua"
fi

# Migrate
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Permissions
chown -R www-data:www-data "$APP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "${APP_DIR}/storage"
chmod -R 775 "${APP_DIR}/bootstrap/cache"
log "Laravel đã deploy & tối ưu"

# ──────────────────────────────────────
step "11/12 — Cron + Queue Worker"
# ──────────────────────────────────────

# Cron scheduler
CRON_LINE="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -l 2>/dev/null | grep -qF 'schedule:run') || \
    (crontab -l 2>/dev/null; echo "$CRON_LINE") | crontab -
log "Cron scheduler đã thêm"

# Queue worker (systemd)
cat > /etc/systemd/system/giavanghn-worker.service <<UNIT
[Unit]
Description=GiaVangHN Queue Worker
After=network.target mysql.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable giavanghn-worker
systemctl start giavanghn-worker
log "Queue worker đã chạy"

# ──────────────────────────────────────
step "12/12 — Firewall + Swap"
# ──────────────────────────────────────

# Swap
if [[ ! -f /swapfile ]]; then
    fallocate -l ${SWAP_SIZE} /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    grep -qF '/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
    grep -qF 'vm.swappiness' /etc/sysctl.conf || echo 'vm.swappiness=10' >> /etc/sysctl.conf
    sysctl -p
    log "Swap ${SWAP_SIZE} đã tạo"
else
    warn "Swap đã tồn tại"
fi

# Firewall
ufw --force enable
ufw allow OpenSSH
ufw allow 'Nginx Full'
log "Firewall đã bật (SSH + Nginx)"

# ──────────────────────────────────────
echo ""
echo -e "${GREEN}══════════════════════════════════════${NC}"
echo -e "${GREEN}  SETUP HOÀN TẤT!${NC}"
echo -e "${GREEN}══════════════════════════════════════${NC}"
echo ""
echo "  Domain:  https://${DOMAIN}"
echo "  App dir: ${APP_DIR}"
echo "  Nginx:   ${NGINX_CONF}"
echo ""
echo "  Các lệnh hữu ích:"
echo "    systemctl status nginx"
echo "    systemctl status php${PHP_VERSION}-fpm"
echo "    systemctl status giavanghn-worker"
echo "    tail -f ${APP_DIR}/storage/logs/laravel.log"
echo ""
echo "  Deploy lần sau: bash /var/www/deploy.sh"
echo ""
