#!/bin/bash

echo "====================================="
echo "Installation de l'API Blog Laravel"
echo "====================================="
echo ""

# Vérifier si composer existe
if ! command -v composer &> /dev/null
then
    echo "❌ Composer n'est pas installé. Veuillez installer Composer d'abord."
    exit 1
fi

# Vérifier si PHP existe
if ! command -v php &> /dev/null
then
    echo "❌ PHP n'est pas installé. Veuillez installer PHP 8.2 ou supérieur."
    exit 1
fi

echo "✅ PHP et Composer sont installés"
echo ""

# Installer les dépendances
echo "📦 Installation des dépendances Composer..."
composer install

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de l'installation des dépendances"
    exit 1
fi

echo "✅ Dépendances installées avec succès"
echo ""

# Copier le fichier .env s'il n'existe pas
if [ ! -f .env ]; then
    echo "📝 Création du fichier .env..."
    cp .env.example .env
    echo "✅ Fichier .env créé"
else
    echo "ℹ️  Le fichier .env existe déjà"
fi

echo ""

# Générer la clé d'application
echo "🔑 Génération de la clé d'application..."
php artisan key:generate

echo ""

# Créer la base de données SQLite si elle n'existe pas
if [ ! -f database/database.sqlite ]; then
    echo "📊 Création de la base de données SQLite..."
    touch database/database.sqlite
    echo "✅ Base de données créée"

    # Mettre à jour le .env pour utiliser SQLite
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    sed -i 's/^DB_HOST=/#DB_HOST=/' .env
    sed -i 's/^DB_PORT=/#DB_PORT=/' .env
    sed -i 's/^DB_DATABASE=/#DB_DATABASE=/' .env
    sed -i 's/^DB_USERNAME=/#DB_USERNAME=/' .env
    sed -i 's/^DB_PASSWORD=/#DB_PASSWORD=/' .env
fi

echo ""

# Exécuter les migrations
echo "🗄️  Exécution des migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Erreur lors de l'exécution des migrations"
    exit 1
fi

echo "✅ Migrations exécutées avec succès"
echo ""

# Afficher le récapitulatif
echo "====================================="
echo "✅ Installation terminée avec succès!"
echo "====================================="
echo ""
echo "Pour démarrer le serveur de développement, exécutez:"
echo "  php artisan serve"
echo ""
echo "L'API sera disponible sur: http://localhost:8000"
echo ""
echo "Pour tester l'API, consultez le fichier API_SETUP.md"
echo ""
echo "Endpoints disponibles:"
echo "  - POST /api/register"
echo "  - POST /api/login"
echo "  - GET  /api/user (protégé)"
echo "  - POST /api/logout (protégé)"
echo "  - GET  /api/health"
echo ""
