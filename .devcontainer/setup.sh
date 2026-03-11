#!/bin/bash
set -e

echo "==== Vérification des versions ===="
php -v
composer --version
symfony -v
node -v
yarn -v

# Optionnel : Installation automatique des dépendances du projet
# if [ -f "composer.json" ]; then
#     composer install
# fi

echo "==== Environnement prêt ! ===="