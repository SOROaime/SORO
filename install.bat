@echo off
REM ============================================================
REM install.bat — Script d'installation ShopLaravel (Windows)
REM Double-cliquez pour lancer l'installation
REM ============================================================

echo.
echo ==========================================
echo   Installation ShopLaravel
echo ==========================================
echo.

REM 1. Dépendances Composer
echo [1/6] Installation des dependances PHP...
call composer install --no-interaction --prefer-dist
echo.

REM 2. Fichier .env
if not exist ".env" (
    echo [2/6] Creation du fichier .env...
    copy .env.local.example .env
) else (
    echo [2/6] Fichier .env deja present.
)
echo.

REM 3. Clé d'application
echo [3/6] Generation de la cle d'application...
php artisan key:generate
echo.

REM 4. Migrations
echo [4/6] Creation des tables en base de donnees...
echo    Assurez-vous que MySQL tourne (XAMPP/Laragon) !
echo    ET que la base de donnees 'ecommerce' existe !
pause
php artisan migrate --force
echo.

REM 5. Seeders
echo [5/6] Insertion des donnees de test...
php artisan db:seed --force
echo.

REM 6. Storage link
echo [6/6] Lien symbolique storage...
php artisan storage:link
echo.

echo ==========================================
echo   Installation terminee !
echo ==========================================
echo.
echo   Lancez le serveur avec :
echo   php artisan serve
echo.
echo   Puis ouvrez : http://localhost:8000
echo.
echo   Comptes de test :
echo   Admin  : admin@shop.com    / Admin123!
echo   Client : alice@example.com / User123!
echo.
pause
