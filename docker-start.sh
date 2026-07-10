#!/bin/bash

# Créer le .env depuis les variables d'environnement Render
cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-ShopCI}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://shopci.onrender.com}

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-28249}
DB_DATABASE=${DB_DATABASE:-defaultdb}
DB_USERNAME=${DB_USERNAME:-avnadmin}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-smtp.gmail.com}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@shopci.site}
MAIL_FROM_NAME=${MAIL_FROM_NAME:-ShopCI}

GENIUSPAY_API_KEY=${GENIUSPAY_API_KEY}
GENIUSPAY_API_SECRET=${GENIUSPAY_API_SECRET}
GENIUSPAY_WEBHOOK_SECRET=${GENIUSPAY_WEBHOOK_SECRET}
GENIUSPAY_BASE_URL=${GENIUSPAY_BASE_URL:-https://pay.genius.ci/api/v1/merchant}

GROQ_API_KEY=${GROQ_API_KEY}
EOF

# Permissions
chown www-data:www-data /var/www/html/.env
chmod 640 /var/www/html/.env

# Vérifier le .env créé
echo "=== .env DB config ==="
grep "^DB_" /var/www/html/.env

# Vider le cache de config AVANT migrate
php artisan config:clear 2>/dev/null || true

# Migrations
php artisan migrate --force 2>/dev/null || true

# Créer le compte admin si absent
php artisan make:admin --name="Admin" --email="admin@shopci.com" --password="Admin123!" 2>/dev/null || true

# Lien storage public
php artisan storage:link 2>/dev/null || true

# Optimiser
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true
php artisan event:cache 2>/dev/null || true

# Démarrer Apache
apache2-foreground
