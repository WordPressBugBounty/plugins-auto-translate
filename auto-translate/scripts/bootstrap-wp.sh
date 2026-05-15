#!/usr/bin/env bash
set -euo pipefail

# Idempotent local bootstrap for Dockerized WordPress + this plugin.

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

SITE_URL="${SITE_URL:-http://localhost:8080}"
SITE_TITLE="${SITE_TITLE:-Automatic Translator Local}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-admin}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"

compose() {
  if command -v docker >/dev/null 2>&1 && docker compose version >/dev/null 2>&1; then
    docker compose "$@"
  else
    docker-compose "$@"
  fi
}

wp() {
  compose run --rm wpcli "$@"
}

echo "Starting Docker services..."
compose up -d db wordpress

echo "Waiting for database connectivity..."
for _ in {1..90}; do
  if compose exec -T db sh -lc 'mysqladmin ping -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --silent' >/dev/null 2>&1; then
    break
  fi
  sleep 2
done

if ! compose exec -T db sh -lc 'mysqladmin ping -h127.0.0.1 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --silent' >/dev/null 2>&1; then
  echo "Database did not become ready in time."
  exit 1
fi

echo "Waiting for WordPress core files..."
for _ in {1..60}; do
  if wp core is-installed >/dev/null 2>&1; then
    break
  fi

  if wp core version >/dev/null 2>&1; then
    break
  fi

  sleep 2
done

if ! wp core is-installed >/dev/null 2>&1; then
  echo "Installing WordPress..."
  wp core install \
    --url="$SITE_URL" \
    --title="$SITE_TITLE" \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASSWORD" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email
else
  echo "WordPress is already installed."
fi

echo "Activating plugin..."
wp plugin activate auto-translate >/dev/null 2>&1 || true

echo "Bootstrap complete."
echo "URL: $SITE_URL"
echo "Admin user: $ADMIN_USER"
