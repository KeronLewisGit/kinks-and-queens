#!/usr/bin/env bash
# Builds a production zip that extracts straight into Hostinger's public_html/.
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/dist"
STAGE="$OUT/stage"
APP="$STAGE/kq-app"
ZIP="$OUT/kinksandqueens-hostinger.zip"

cd "$ROOT"
echo "→ Building front-end assets"
npm run build >/dev/null

echo "→ Staging application (tracked files only)"
rm -rf "$STAGE" "$ZIP"; mkdir -p "$APP"
# Only what is committed ships: local editor and tooling files never reach the server.
git ls-files -z -- . ':!:tests' ':!:deploy' ':!:scripts' ':!:public' ':!:dist' ':!:.github' ':!:*.md' ':!:phpunit.xml' ':!:.env.example' \
  | rsync -a --from0 --files-from=- "$ROOT/" "$APP/"

echo "→ Installing production PHP dependencies (PHP 8.4)"
cp "$ROOT/composer.json" "$ROOT/composer.lock" "$APP/"
( cd "$APP" && composer install --no-dev --optimize-autoloader --no-interaction --quiet --ignore-platform-req=php )

# Vendor docs are not needed at runtime.
find "$APP/vendor" -type f -iname "*.md" -delete

mkdir -p "$APP/storage/framework/"{cache/data,sessions,views,testing} "$APP/storage/logs" "$APP/storage/app/private" "$APP/bootstrap/cache"
touch "$APP/storage/logs/.gitkeep"
cp "$ROOT/deploy/env.production.example" "$APP/.env.example"
cp "$ROOT/deploy/app.htaccess" "$APP/.htaccess"

echo "→ Staging public files"
cp -R "$ROOT/public/build" "$STAGE/build"
cp -R "$ROOT/public/assets" "$STAGE/assets"
mkdir -p "$STAGE/uploads" && cp "$ROOT/public/uploads/.htaccess" "$STAGE/uploads/.htaccess"
cp "$ROOT/public/favicon.svg" "$STAGE/favicon.svg"
cp "$ROOT/deploy/index.php" "$STAGE/index.php"
cp "$ROOT/deploy/.htaccess" "$STAGE/.htaccess"
cp "$ROOT/deploy/setup.php" "$STAGE/setup.php"

echo "→ Zipping"
( cd "$STAGE" && zip -qr "$ZIP" . -x '*.DS_Store' )
rm -rf "$STAGE"
echo "✓ Package ready: $ZIP ($(du -h "$ZIP" | cut -f1))"
