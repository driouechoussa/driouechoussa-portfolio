#!/usr/bin/env bash
#
# Deploy the latest `production` branch on the server.
#
#   First time:  bash deploy.sh --first
#   Updates:     bash deploy.sh
#
# Run from the project directory, as the user that owns the files.
# Requires: PHP 8.4+, Composer 2, Node 20+ (for the asset build), git.

set -euo pipefail

BRANCH="${DEPLOY_BRANCH:-production}"
FIRST=false
[[ "${1:-}" == "--first" ]] && FIRST=true

cd "$(dirname "$0")"

step() { printf '\n\033[1;33m==> %s\033[0m\n' "$1"; }

if [[ ! -f .env ]]; then
  echo "Missing .env: copy .env.production.example to .env and fill it in first." >&2
  exit 1
fi

if ! $FIRST; then
  step "Maintenance mode on"
  php artisan down --retry=15 || true
  # Always come back up, even if a step below fails.
  trap 'php artisan up' EXIT
fi

step "Fetching $BRANCH"
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"

step "PHP dependencies (no dev packages)"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

step "Front-end build"
if command -v npm >/dev/null 2>&1; then
  npm ci --no-audit --no-fund
  npm run build
else
  echo "npm not found: build locally (npm run build) and upload public/build/ before continuing." >&2
  [[ -f public/build/manifest.json ]] || exit 1
fi

if $FIRST; then
  step "First deploy: app key, storage link, database content"
  grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force
  php artisan storage:link || true
fi

step "Database migrations"
php artisan migrate --force

if $FIRST; then
  # Fills the site with the starter content; skipped automatically once content exists.
  php artisan db:seed --force
fi

step "Caches (config, routes, views, events, Filament)"
php artisan optimize:clear
php artisan optimize
php artisan filament:optimize

step "Permissions"
chmod -R ug+rwX storage bootstrap/cache

if $FIRST; then
  step "Done. Create your admin account:"
  echo "  php artisan make:filament-user   (use an email listed in ADMIN_EMAILS)"
else
  step "Done"
fi
