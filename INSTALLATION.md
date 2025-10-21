# Guide d'installation - Blog API Laravel

Ce guide vous accompagne dans l'installation et la configuration du backend API Laravel avec authentification.

## Prérequis

Avant de commencer, assurez-vous d'avoir :

- PHP >= 8.2
- Composer
- MySQL >= 8.0 ou MariaDB >= 10.3
- Extensions PHP requises :
  - mbstring
  - xml
  - bcmath
  - pdo_mysql
  - openssl
  - tokenizer
  - json

## Installation pas à pas

### 1. Cloner le projet

```bash
git clone <repository-url> blog-api-laravel
cd blog-api-laravel
```

### 2. Installer les dépendances PHP

```bash
composer install
```

Cette commande va installer toutes les dépendances nécessaires, y compris Laravel Sanctum pour l'authentification API.

### 3. Configurer l'environnement

Le fichier `.env` a déjà été créé. Vous devez le modifier selon votre configuration :

```bash
# Ouvrir le fichier .env avec votre éditeur préféré
nano .env
```

Configurez les paramètres suivants :

```env
APP_NAME="Blog API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuration de la base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api_laravel
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

Cette commande va générer une clé unique pour votre application et la sauvegarder dans le fichier `.env`.

### 5. Créer la base de données

Connectez-vous à MySQL et créez la base de données :

```bash
mysql -u root -p
```

Dans la console MySQL :

```sql
CREATE DATABASE blog_api_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Exécuter les migrations

```bash
php artisan migrate
```

Cette commande va créer toutes les tables nécessaires :
- `users` - Table des utilisateurs
- `personal_access_tokens` - Table pour les tokens Sanctum
- `password_reset_tokens` - Table pour la réinitialisation de mot de passe
- `sessions` - Table des sessions
- `cache` - Table de cache
- `jobs` - Table des tâches en arrière-plan

### 7. (Optionnel) Générer des données de test

Si vous souhaitez créer des utilisateurs de test :

```bash
php artisan db:seed
```

### 8. Lancer le serveur de développement

```bash
php artisan serve
```

L'API sera accessible sur : `http://localhost:8000`

## Vérification de l'installation

Pour vérifier que tout fonctionne correctement, testez l'endpoint de health check :

```bash
curl http://localhost:8000/up
```

Vous devriez recevoir une réponse indiquant que l'application est opérationnelle.

## Test de l'authentification

### 1. Inscription d'un utilisateur

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

Vous devriez recevoir un token d'accès dans la réponse.

### 2. Connexion

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### 3. Récupérer l'utilisateur authentifié

Remplacez `YOUR_TOKEN` par le token reçu lors de l'inscription ou de la connexion :

```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## Configuration CORS (pour les applications frontend)

Si vous développez un frontend séparé (React, Vue, Angular, etc.), vous devrez peut-être configurer CORS.

Modifiez le fichier `config/cors.php` :

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'], // Ajoutez l'URL de votre frontend
```

## Dépannage

### Erreur "Class 'Laravel\Sanctum\HasApiTokens' not found"

Si vous obtenez cette erreur, assurez-vous que Laravel Sanctum est bien installé :

```bash
composer require laravel/sanctum
```

### Erreur de connexion à la base de données

Vérifiez que :
- MySQL est démarré : `sudo service mysql status`
- Les identifiants dans `.env` sont corrects
- La base de données existe

### Permission denied sur storage/logs

Donnez les permissions nécessaires :

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Prochaines étapes

Maintenant que votre backend est opérationnel, vous pouvez :

1. Créer des endpoints API supplémentaires (posts, commentaires, etc.)
2. Mettre en place un système de rôles et permissions
3. Ajouter des middlewares personnalisés
4. Créer des tests unitaires et d'intégration
5. Déployer sur un serveur de production

Consultez le fichier `README.md` pour plus d'informations sur l'utilisation de l'API.

## Support

Pour toute question ou problème, créez une issue sur le repository GitHub du projet.
