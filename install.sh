#!/bin/bash
# ============================================================
# install.sh — Script d'installation automatique ShopLaravel
# Usage : bash install.sh
# ============================================================

echo ""
echo "╔══════════════════════════════════════╗"
echo "║   Installation ShopLaravel           ║"
echo "╚══════════════════════════════════════╝"
echo ""

# 1. Dépendances Composer
echo "📦 Installation des dépendances PHP..."
composer install --no-interaction --prefer-dist
echo ""

# 2. Fichier .env
if [ ! -f ".env" ]; then
    echo "⚙️  Création du fichier .env..."
    cp .env.local.example .env
else
    echo "✅ Fichier .env déjà présent."
fi
echo ""

# 3. Clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate
echo ""

# 4. Migrations
echo "🗄️  Création des tables en base de données..."
echo "   → Assurez-vous que MySQL tourne et que la DB 'ecommerce' existe !"
read -p "   Appuyez sur ENTRÉE pour continuer..."
php artisan migrate --force
echo ""

# 5. Seeders
echo "🌱 Insertion des données de test (comptes + produits)..."
php artisan db:seed --force
echo ""

# 6. Storage link
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link
echo ""

# 7. Permissions
echo "🔐 Application des permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo ""

echo "╔══════════════════════════════════════╗"
echo "║   ✅ Installation terminée !         ║"
echo "╚══════════════════════════════════════╝"
echo ""
echo "  Lancez le serveur avec :"
echo "  php artisan serve"
echo ""
echo "  Puis ouvrez : http://localhost:8000"
echo ""
echo "  Comptes de test :"
echo "  👤 Admin  : admin@shop.com   / Admin123!"
echo "  👤 Client : alice@example.com / User123!"
echo ""
