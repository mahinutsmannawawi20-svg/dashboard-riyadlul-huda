#!/bin/bash
# =====================================================
# DEPLOYMENT SCRIPT - Dashboard Riyadlul Huda
# =====================================================

# CONFIGURATION
DOMAIN="riyadlulhuda.my.id"  # ✅ DOMAIN BARU (Updated)
VPS_IP="72.62.124.123"      # ✅ IP SUDAH DISET
DB_PASSWORD="buat_password_db_baru" # 🔴 GANTI DENGAN PASSWORD BARU

set -e  # Exit on any error

echo "🚀 Starting Laravel Deployment to $DOMAIN ($VPS_IP)..."

# ===== 1. UPDATE SYSTEM =====
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# ===== 2. INSTALL NGINX =====
echo "🌐 Installing Nginx..."
apt install nginx -y
systemctl enable nginx
systemctl start nginx

# ===== 3. CHECK DATABASE =====
echo "🗄️ Checking Database Service..."
if ! command -v mysql &> /dev/null; then
    echo "Installing MariaDB Server..."
    apt install mariadb-server -y
    systemctl enable mariadb
    systemctl start mariadb
else
    echo "✅ Database server already installed."
fi

# ===== 4. INSTALL PHP 8.2 =====
echo "🐘 Installing PHP 8.2..."
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl -y
systemctl restart php8.2-fpm

# ===== 5. INSTALL COMPOSER =====
echo "🎼 Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
else
    echo "Composer already installed."
fi

# ===== 6. INSTALL NODE.JS =====
echo "📗 Installing Node.js..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt install nodejs -y
else
    echo "Node.js already installed."
fi

# ===== 7. INSTALL PHPMYADMIN (OPTIONAL) =====
echo "🗃️ Installing phpMyAdmin..."
if ! command -v phpmyadmin &> /dev/null; then
    # Pre-configure debconf for non-interactive install
    export DEBIAN_FRONTEND=noninteractive
    echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/app-password-confirm password $DB_PASSWORD" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/admin-pass password $DB_PASSWORD" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/mysql/app-pass password $DB_PASSWORD" | debconf-set-selections
    echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect none" | debconf-set-selections

    apt install phpmyadmin -y
else
    echo "phpMyAdmin already installed."
fi

# ===== 8. CLONE REPOSITORY =====
echo "📥 Setting up repository..."
cd /var/www
if [ ! -d "dashboard-riyadlul-huda" ]; then
    git clone https://github.com/mahinutsmannawawi20-svg/dashboard-riyadlul-huda.git
else
    echo "Directory exists, pulling changes..."
    cd dashboard-riyadlul-huda
    git stash
    git pull origin main
    cd ..
fi

cd dashboard-riyadlul-huda

# ===== 9. INSTALL DEPENDENCIES =====
echo "📚 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "📦 Installing Node dependencies & building assets..."
npm install
npm run build

# ===== 10. CONFIGURE LARAVEL =====
echo "⚙️ Configuring Laravel..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
    # Auto configure DB in .env if config vars are set
    sed -i "s/APP_NAME=Laravel/APP_NAME=\"Dashboard Riyadlul Huda\"/" .env
    sed -i "s/DB_DATABASE=laravel/DB_DATABASE=riyadlul_huda/" .env
    sed -i "s/DB_USERNAME=root/DB_USERNAME=admin/" .env
    sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env
    sed -i "s/APP_URL=http:\/\/localhost/APP_URL=http:\/\/$DOMAIN/" .env
fi

# ===== 11. AUTOMATE DATABASE SETUP =====
echo "🗄️ Setting up Database & User..."
mysql -e "CREATE DATABASE IF NOT EXISTS riyadlul_huda;"
mysql -e "CREATE USER IF NOT EXISTS 'admin'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"

# Run migrations automatically
echo "🔄 Running migrations..."
php artisan migrate --seed --force

# ===== 12. SET PERMISSIONS =====
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/dashboard-riyadlul-huda
chmod -R 755 /var/www/dashboard-riyadlul-huda
chmod -R 775 storage bootstrap/cache

# ===== 13. CREATE NGINX CONFIG =====
if [ ! -f /etc/nginx/sites-available/dashboard-riyadlul-huda ]; then
    echo "📝 Creating Nginx config..."
    cat > /etc/nginx/sites-available/dashboard-riyadlul-huda << EOF
server {
    listen 80;
    server_name $DOMAIN;
    root /var/www/dashboard-riyadlul-huda/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHPMYADMIN CONFIGURATION
    location /phpmyadmin {
        root /usr/share/;
        index index.php index.html index.htm;
        location ~ ^/phpmyadmin/(.+\.php)$ {
            try_files \$uri =404;
            root /usr/share/;
            fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
        }
        location ~* ^/phpmyadmin/(.+\.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt))$ {
            root /usr/share/;
        }
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

    ln -sf /etc/nginx/sites-available/dashboard-riyadlul-huda /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    nginx -t && systemctl reload nginx
else
    echo "⚠️ Nginx config already exists. Skipping overwrite to preserve SSL."
fi

ln -sf /etc/nginx/sites-available/dashboard-riyadlul-huda /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

echo ""
echo "✅ ============================================="
echo "✅ DEPLOYMENT COMPLETE!"
echo "✅ ============================================="
echo ""
echo "📌 ACCESS INFO:"
echo "   🌐 Website:    http://$DOMAIN"
echo "   🗃️ phpMyAdmin: http://$DOMAIN/phpmyadmin"
echo ""
echo "📌 CREDENTIALS:"
echo "   Database User: admin"
echo "   Database Pass: $DB_PASSWORD"
echo ""
echo "   Admin Login:   admin@riyadlulhuda.com"
echo "   Admin Pass:    password"
echo ""
echo "📌 NEXT STEPS:"
echo "   1. Setup SSL (HTTPS):"
echo "      apt install certbot python3-certbot-nginx"
echo "      certbot --nginx -d $DOMAIN"
echo ""
