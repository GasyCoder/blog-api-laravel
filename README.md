# Blog Headless API - Laravel 12

API REST complète pour un système de blog headless avec authentification, gestion des rôles et modération.

## 🚀 Technologies

- Laravel 12
- MySQL 8+
- Laravel Sanctum (authentification API)
- PHP 8.2+

## 📋 Prérequis

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Extension PHP : mbstring, xml, bcmath, pdo_mysql

## ⚙️ Installation

```bash
# Cloner le projet
git clone <repository-url> blog-api
cd blog-api

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_headless
DB_USERNAME=root
DB_PASSWORD=

# Créer la base de données
mysql -u root -p -e "CREATE DATABASE blog_headless CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Exécuter les migrations
php artisan migrate

# Générer des données de test
php artisan db:seed

# Créer le lien symbolique pour le stockage
php artisan storage:link

# Lancer le serveur de développement
php artisan serve
```

L'API sera accessible sur `http://localhost:8000`

## 🔑 Comptes de test

Après le seeding, vous aurez :

- **Superadmin** : superadmin@blog.com / password
- **Writer 1** : writer1@blog.com / password
- **Writer 2** : writer2@blog.com / password
- **User** : user@blog.com / password

## 📚 Endpoints API

### Authentification

#### Inscription
```bash
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Réponse :**
```json
{
    "message": "Utilisateur créé avec succès",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-10-21T12:00:00.000000Z",
        "updated_at": "2025-10-21T12:00:00.000000Z"
    },
    "access_token": "1|xxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
}
```

#### Connexion
```bash
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Réponse :**
```json
{
    "message": "Connexion réussie",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "access_token": "2|xxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
}
```

#### Récupérer l'utilisateur authentifié
```bash
GET /api/user
Authorization: Bearer {token}
```

**Réponse :**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": null,
    "created_at": "2025-10-21T12:00:00.000000Z",
    "updated_at": "2025-10-21T12:00:00.000000Z"
}
```

#### Déconnexion
```bash
POST /api/auth/logout
Authorization: Bearer {token}
```

**Réponse :**
```json
{
    "message": "Déconnexion réussie"
}
```

### Exemples cURL

**Inscription :**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Connexion :**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Récupérer l'utilisateur authentifié :**
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

**Déconnexion :**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Tests avec couverture
php artisan test --coverage
```

## 🔒 Sécurité

- Rate limiting sur les endpoints publics (60 requêtes/minute)
- Rate limiting sur l'authentification (5 tentatives/minute)
- Validation stricte des entrées
- Protection CSRF désactivée pour l'API
- Tokens Sanctum avec expiration

## 📦 Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   ├── CommentController.php
│   │   │   ├── CategoryController.php
│   │   │   └── TagController.php
│   │   └── Admin/
│   │       ├── PostController.php
│   │       └── CommentController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   └── Requests/
│       ├── StorePostRequest.php
│       ├── UpdatePostRequest.php
│       └── StoreCommentRequest.php
├── Models/
│   ├── User.php
│   ├── Post.php
│   ├── Category.php
│   ├── Tag.php
│   └── Comment.php
└── Traits/
    └── ApiResponse.php
```

## 🌐 Déploiement

Voir le fichier `DEPLOYMENT.md` pour les instructions de déploiement sur VPS.

## 📄 Licence

MIT