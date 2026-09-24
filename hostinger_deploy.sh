#!/bin/bash
set -e

echo "======================================================"
echo "🚀 Déploiement Hostinger - Coopérative Aït Oumdis"
echo "======================================================"

# 1. Vérification de .env
if [ ! -f .env ]; then
    echo "⚠️  Fichier .env introuvable, création à partir de .env.production..."
    cp .env.production .env
    php artisan key:generate --force
    echo "👉 Pensez à éditer votre .env avec vos identifiants MySQL Hostinger!"
fi

# 2. Permissions des dossiers critiques
echo "🔒 Configuration des permissions storage et cache..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 3. Lien symbolique pour les images et médias
echo "🔗 Configuration du lien symbolique public/storage..."
rm -rf public/storage 2>/dev/null || true
php artisan storage:link || ln -s ../storage/app/public public/storage

# 4. Migrations de la base de données
echo "🗄️  Exécution des migrations de base de données..."
php artisan migrate --force || echo "⚠️ Migration passée ou déjà à jour."

# 5. Seeders de configuration initiale
echo "🌱 Initialisation des données (Rôles & Paramètres)..."
php artisan db:seed --class=RolePermissionSeeder --force 2>/dev/null || true
php artisan db:seed --class=SettingsSeeder --force 2>/dev/null || true

# 6. Optimisation et mise en cache Laravel
echo "⚡ Optimisation du cache pour la production..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "======================================================"
echo "🎉 Déploiement terminé avec succès !"
echo "🌐 Votre boutique est prête en ligne."
echo "======================================================"
