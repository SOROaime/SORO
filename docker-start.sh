#!/bin/bash

# Générer APP_KEY si absent
php artisan key:generate --force 2>/dev/null || true

# Lancer les migrations
php artisan migrate --force 2>/dev/null || true

# Optimiser
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Démarrer Apache
apache2-foreground
