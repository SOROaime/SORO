# 🛍️ ShopLaravel — Application E-Commerce Laravel

Application e-commerce complète construite avec **Laravel 11**, **Blade**, **Bootstrap 5** et **MySQL**.

---

## 📋 Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

| Outil | Version minimale | Vérification |
|-------|-----------------|--------------|
| PHP | 8.2+ | `php --version` |
| Composer | 2.x | `composer --version` |
| MySQL / MariaDB | 8.0+ / 10.x | `mysql --version` |
| Git | toute version | `git --version` |

### Outils recommandés selon votre OS

| OS | Outil recommandé | Lien |
|----|-----------------|------|
| Windows | **XAMPP** ou **Laragon** | https://laragon.org (recommandé) |
| macOS | **MAMP** ou **Homebrew** | https://www.mamp.info |
| Linux | PHP + MySQL natif | `sudo apt install php8.2 mysql-server` |

---

## 🚀 Installation en local (étape par étape)

### Étape 1 — Cloner le projet

```bash
git clone <URL_DU_REPO> shoplaravel
cd shoplaravel
```

Ou téléchargez le `.zip` et extrayez-le dans votre dossier de projets.

---

### Étape 2 — Installer les dépendances PHP

```bash
composer install
```

> ⏱️ Cela peut prendre 1 à 2 minutes selon votre connexion.

---

### Étape 3 — Créer le fichier `.env`

```bash
# Copier le fichier d'exemple
cp .env.local.example .env

# Ou sur Windows (Command Prompt) :
copy .env.local.example .env
```

---

### Étape 4 — Générer la clé d'application

```bash
php artisan key:generate
```

Vous verrez : `Application key set successfully.`

---

### Étape 5 — Créer la base de données MySQL

#### Avec XAMPP / WAMP / Laragon
1. Ouvrez **phpMyAdmin** → http://localhost/phpmyadmin
2. Cliquez sur **"Nouvelle base de données"**
3. Nom : `ecommerce` → Interclassement : `utf8mb4_unicode_ci`
4. Cliquez **Créer**

#### En ligne de commande
```sql
mysql -u root -p

-- Dans MySQL :
CREATE DATABASE ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
QUIT;
```

---

### Étape 6 — Configurer le `.env`

Ouvrez le fichier `.env` et modifiez la section base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=           # Votre mot de passe MySQL (souvent vide avec XAMPP)
```

> **XAMPP/WAMP :** `DB_USERNAME=root` et `DB_PASSWORD=` (vide)
> **MAMP :** `DB_PORT=8889`, `DB_PASSWORD=root`
> **Laragon :** `DB_USERNAME=root` et `DB_PASSWORD=` (vide)

---

### Étape 7 — Exécuter les migrations (créer les tables)

```bash
php artisan migrate
```

Vous verrez toutes les tables créées :
```
✓ users
✓ products
✓ carts
✓ cart_items
✓ orders
✓ order_items
✓ payments
```

---

### Étape 8 — Insérer les données de test (comptes + produits)

```bash
php artisan db:seed
```

Cela crée automatiquement :
- ✅ **1 compte Admin** : `admin@shop.com` / `Admin123!`
- ✅ **2 comptes Clients** : `alice@example.com` / `User123!`
- ✅ **12 produits** dans 4 catégories (Électronique, Mode, Maison, Sport)

---

### Étape 9 — Créer le lien symbolique pour les images

```bash
php artisan storage:link
```

---

### Étape 10 — Lancer le serveur de développement

```bash
php artisan serve
```

L'application est accessible sur : **http://localhost:8000**

---

## 🔐 Comptes de connexion

| Rôle | Email | Mot de passe | Accès |
|------|-------|--------------|-------|
| **Administrateur** | `admin@shop.com` | `Admin123!` | Dashboard admin + boutique |
| **Client 1** | `alice@example.com` | `User123!` | Boutique, panier, commandes |
| **Client 2** | `bob@example.com` | `User123!` | Boutique, panier, commandes |

---

## 📦 Résumé des commandes (copier-coller)

```bash
# Cloner et entrer dans le projet
git clone <URL_DU_REPO> shoplaravel && cd shoplaravel

# Installer les dépendances
composer install

# Configuration
cp .env.local.example .env
php artisan key:generate

# Base de données (après avoir créé la DB 'ecommerce' dans MySQL)
php artisan migrate
php artisan db:seed

# Lien storage
php artisan storage:link

# Démarrer
php artisan serve
```

Puis ouvrez → **http://localhost:8000**

---

## 🌐 Pages de l'application

| Page | URL |
|------|-----|
| Accueil | http://localhost:8000 |
| Catalogue produits | http://localhost:8000/produits |
| Connexion | http://localhost:8000/connexion |
| Inscription | http://localhost:8000/inscription |
| Panier | http://localhost:8000/panier |
| Mes commandes | http://localhost:8000/mes-commandes |
| **Dashboard Admin** | http://localhost:8000/admin |

---

## ❓ Problèmes courants

### ❌ `SQLSTATE[HY000] [1045] Access denied`
→ Votre mot de passe MySQL est incorrect dans `.env`. Vérifiez `DB_USERNAME` et `DB_PASSWORD`.

### ❌ `Unknown database 'ecommerce'`
→ La base de données n'a pas été créée. Exécutez l'étape 5.

### ❌ `php artisan` ne fonctionne pas
→ PHP n'est pas dans votre PATH. Utilisez le chemin complet : `C:\xampp\php\php artisan` (Windows)

### ❌ Page blanche ou erreur 500
→ Vérifiez les permissions : `chmod -R 775 storage bootstrap/cache` (Linux/Mac)

### ❌ Pas de produits affichés
→ Exécutez `php artisan db:seed` (étape 8).

---

## 🗂️ Structure du projet

```
shoplaravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # AuthController, ProductController, CartController...
│   │   ├── Middleware/         # AdminMiddleware
│   │   └── Requests/           # StoreProductRequest, ProcessPaymentRequest...
│   ├── Models/                 # User, Product, Cart, Order, Payment...
│   └── Providers/
├── database/
│   ├── migrations/             # Tables : users, products, carts, orders...
│   └── seeders/                # Données de test (comptes + 12 produits)
├── resources/views/
│   ├── layouts/                # app.blade.php, admin.blade.php
│   ├── auth/                   # login.blade.php, register.blade.php
│   ├── products/               # index.blade.php, show.blade.php
│   ├── cart/                   # index.blade.php
│   ├── orders/                 # index.blade.php, show.blade.php, checkout.blade.php
│   ├── payment/                # success.blade.php, failed.blade.php
│   ├── admin/                  # dashboard, products, orders, payments, users
│   └── components/             # product-card.blade.php
├── routes/
│   └── web.php                 # Toutes les routes avec middlewares
└── .env                        # Configuration (à adapter)
```

---

## 🛠️ Tech Stack

- **Backend** : Laravel 11 (PHP 8.2)
- **Frontend** : Blade + Bootstrap 5 + Bootstrap Icons
- **Base de données** : MySQL / MariaDB
- **Architecture** : MVC avec Eloquent ORM
- **Sécurité** : CSRF, hashage bcrypt, middleware, Form Requests

---

*Développé avec Laravel 11 — © 2024 ShopLaravel*
