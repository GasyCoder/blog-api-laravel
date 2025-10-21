# Blog API - Laravel 12

API REST pour un système de blog avec authentification complète via Laravel Sanctum.

## 🚀 Technologies

- Laravel 12
- MySQL 8+ / SQLite
- Laravel Sanctum (authentification API)
- PHP 8.2+

## 📋 Prérequis

- PHP >= 8.2
- Composer
- MySQL >= 8.0 ou SQLite
- Extensions PHP : mbstring, xml, bcmath, pdo_mysql (ou pdo_sqlite)

## ⚙️ Installation rapide

```bash
# Cloner le projet
git clone <repository-url> blog-api-laravel
cd blog-api-laravel

# Option 1: Installation automatique avec SQLite
chmod +x install.sh
./install.sh

# Option 2: Installation manuelle
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Lancer le serveur de développement
php artisan serve
```

L'API sera accessible sur `http://localhost:8000`

## 🔧 Configuration MySQL (optionnel)

Si vous préférez utiliser MySQL au lieu de SQLite, modifiez votre fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api_laravel
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

Puis créez la base de données :

```bash
mysql -u root -p -e "CREATE DATABASE blog_api_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

## 📚 Endpoints de l'API

### Health Check
- `GET /api/health` - Vérifier l'état de l'API

### Authentification (Public)
- `POST /api/register` - Inscription d'un nouvel utilisateur
- `POST /api/login` - Connexion

### Authentification (Protégé - nécessite un token)
- `GET /api/user` - Obtenir le profil de l'utilisateur connecté
- `POST /api/logout` - Déconnexion (révoque le token actuel)
- `POST /api/logout-all` - Déconnexion de tous les appareils (révoque tous les tokens)

### Blog Posts (Protégé - nécessite un token)
- `GET /api/posts` - Liste des articles
- `POST /api/posts` - Créer un article
- `GET /api/posts/{id}` - Détails d'un article
- `PUT /api/posts/{id}` - Modifier un article
- `DELETE /api/posts/{id}` - Supprimer un article

## 📖 Documentation détaillée

Pour plus de détails sur l'utilisation de l'API, consultez le fichier **[API_SETUP.md](API_SETUP.md)** qui contient :
- Exemples de requêtes cURL
- Guide d'utilisation avec Postman
- Exemples de réponses JSON
- Gestion des erreurs

## 🧪 Tester l'API

### Exemple : Inscription et connexion

1. **Inscription** :
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

2. **Connexion** :
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

3. **Utiliser le token** (remplacez `{token}` par le token reçu) :
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

## 🔒 Sécurité

- ✅ Authentification via Laravel Sanctum
- ✅ Tokens API sécurisés
- ✅ Validation stricte des entrées
- ✅ Hashage des mots de passe avec bcrypt
- ✅ Protection contre les injections SQL via Eloquent ORM
- ✅ Révocation de tokens lors de la déconnexion

## 📦 Structure du projet

```
app/
├── Http/
│   └── Controllers/
│       └── Api/
│           ├── AuthController.php    # Authentification
│           └── BlogController.php    # Gestion des posts (template)
├── Models/
│   └── User.php                      # Modèle utilisateur avec HasApiTokens
config/
│   ├── auth.php                      # Configuration de l'authentification
│   └── sanctum.php                   # Configuration de Sanctum
database/
│   └── migrations/
│       ├── ..._create_users_table.php
│       └── ..._create_personal_access_tokens_table.php
routes/
│   └── api.php                       # Routes de l'API
```

## 🚀 Prochaines étapes

Ce projet est configuré avec l'authentification de base. Vous pouvez l'étendre en ajoutant :
- ✨ Modèles et contrôleurs pour les articles de blog (Post)
- ✨ Catégories et tags
- ✨ Commentaires
- ✨ Upload d'images
- ✨ Pagination
- ✨ Filtres et recherche
- ✨ Rate limiting
- ✨ Permissions et rôles utilisateurs

## 📄 Licence

MIT

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou à proposer une pull request.