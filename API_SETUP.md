# Guide d'installation et d'utilisation de l'API d'authentification

## Installation

### 1. Installer les dépendances
```bash
composer install
```

### 2. Configurer l'environnement
```bash
# Copier le fichier .env.example
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 3. Configurer la base de données
Modifiez le fichier `.env` avec vos paramètres de base de données :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api_laravel
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 4. Exécuter les migrations
```bash
php artisan migrate
```

### 5. Démarrer le serveur
```bash
php artisan serve
```

L'API sera disponible sur `http://localhost:8000`

## Endpoints de l'API

### Routes publiques

#### 1. Health Check
**GET** `/api/health`
```bash
curl http://localhost:8000/api/health
```

**Réponse :**
```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2024-01-01 12:00:00"
}
```

#### 2. Inscription
**POST** `/api/register`

**Body (JSON) :**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Exemple cURL :**
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

**Réponse (201) :**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-01T12:00:00.000000Z"
    },
    "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

#### 3. Connexion
**POST** `/api/login`

**Body (JSON) :**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Exemple cURL :**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-01T12:00:00.000000Z"
    },
    "access_token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Routes protégées (nécessitent un token)

#### 4. Obtenir l'utilisateur connecté
**GET** `/api/user`

**Headers :**
- `Authorization: Bearer {token}`

**Exemple cURL :**
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer 2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

**Réponse (200) :**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-01T12:00:00.000000Z"
    }
  }
}
```

#### 5. Déconnexion
**POST** `/api/logout`

**Headers :**
- `Authorization: Bearer {token}`

**Exemple cURL :**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer 2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

#### 6. Déconnexion de tous les appareils
**POST** `/api/logout-all`

**Headers :**
- `Authorization: Bearer {token}`

**Exemple cURL :**
```bash
curl -X POST http://localhost:8000/api/logout-all \
  -H "Authorization: Bearer 2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

**Réponse (200) :**
```json
{
  "success": true,
  "message": "Logged out from all devices successfully"
}
```

## Test avec Postman

### 1. Configuration de l'environnement
Créez un environnement Postman avec les variables suivantes :
- `base_url`: `http://localhost:8000`
- `token`: (sera défini automatiquement après la connexion)

### 2. Collection de tests

#### Register
- **Method**: POST
- **URL**: `{{base_url}}/api/register`
- **Body (raw JSON)**:
```json
{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
- **Method**: POST
- **URL**: `{{base_url}}/api/login`
- **Body (raw JSON)**:
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```
- **Tests** (pour sauvegarder le token automatiquement):
```javascript
pm.test("Login successful", function () {
    pm.response.to.have.status(200);
    var jsonData = pm.response.json();
    pm.environment.set("token", jsonData.data.access_token);
});
```

#### Get User (Protected)
- **Method**: GET
- **URL**: `{{base_url}}/api/user`
- **Headers**:
  - Key: `Authorization`
  - Value: `Bearer {{token}}`

#### Logout (Protected)
- **Method**: POST
- **URL**: `{{base_url}}/api/logout`
- **Headers**:
  - Key: `Authorization`
  - Value: `Bearer {{token}}`

## Gestion des erreurs

### Erreurs de validation (422)
```json
{
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Erreurs d'authentification (401)
```json
{
  "message": "Unauthenticated."
}
```

### Identifiants incorrects (422)
```json
{
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

## Sécurité

- Les tokens sont révoqués lors de la déconnexion
- Les mots de passe sont hashés avec bcrypt
- Les anciennes sessions sont supprimées lors de la connexion
- Support de la déconnexion de tous les appareils

## Prochaines étapes

Pour étendre cette API, vous pouvez ajouter :
- Réinitialisation de mot de passe
- Vérification d'email
- Gestion de profil utilisateur
- Ressources API supplémentaires (posts, commentaires, etc.)
- Rate limiting
- Pagination
- Filtres et recherche

## Support

Pour toute question ou problème, veuillez créer une issue sur le dépôt GitHub.
