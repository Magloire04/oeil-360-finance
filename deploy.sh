#!/usr/bin/env bash
#
# Déploiement production — Oeil360 Finance
# À lancer SUR LE SERVEUR, depuis le dossier de l'app :
#   cd ~/apps/oeil360finance && bash deploy.sh
#
# Pré-requis : la branche `main` contient déjà la version à déployer
# (workflow : feature -> PR vers v2 -> release v2 -> main).

set -euo pipefail
cd "$(dirname "$0")"

PHP=/usr/local/bin/php
COMPOSER="$HOME/bin/composer"

# En cas d'échec, on ressort toujours du mode maintenance.
trap '$PHP artisan up >/dev/null 2>&1 || true' EXIT

echo "==> Mode maintenance"
$PHP artisan down --retry=15 || true

echo "==> Récupération du code (main)"
git pull origin main

echo "==> Dépendances (production)"
$COMPOSER install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrations de base de données"
$PHP artisan migrate --force

echo "==> Reconstruction des caches"
$PHP artisan config:clear
$PHP artisan config:cache
$PHP artisan view:cache
# NB : pas de `route:cache` — la route landing "/" est une closure (incompatible).

echo "==> Fin du mode maintenance"
$PHP artisan up

echo "✅ Déploiement terminé"
