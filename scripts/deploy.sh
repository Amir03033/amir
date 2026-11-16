#!/usr/bin/env bash
#
# One command after every update of amirs.life:
#
#   bash scripts/deploy.sh
#
# Or let Plesk run it for you on every deploy: Plesk → Git → (repository) → "Enable additional
# deployment actions" → put in:   bash scripts/deploy.sh
#
# What it does, in order (safe to run as often as you like):
#   1. composer install (production, no dev packages)
#   2. backs up the database, only when there are migrations to run
#   3. maintenance mode on, runs pending migrations, maintenance mode off (also if a step fails)
#   4. seeds the review drafts (skips everything that already exists; never overwrites your edits)
#   5. makes sure /storage is linked, rebuilds the caches
#   6. fixes file ownership (only when run as root, e.g. over SSH; Plesk's own run doesn't need it)
#
# Settings you can override when calling it, e.g.  PHP=/opt/plesk/php/8.3/bin/php bash scripts/deploy.sh
#   PHP            php binary                     (default: php)
#   COMPOSER       composer command               (default: composer)
#   OWNER          owner:group for chown          (default: amirs.life_jt630rcz25r:psacln)
#   SKIP_BACKUP=1  don't dump the database before migrating
#   SKIP_SEED=1    don't run the drafts seeder
#
set -Eeuo pipefail

cd "$(dirname "$0")/.."
APP_DIR="$(pwd)"
PHP="${PHP:-php}"
COMPOSER="${COMPOSER:-composer}"
OWNER="${OWNER:-amirs.life_jt630rcz25r:psacln}"

step() { printf '\n\033[1m==> %s\033[0m\n' "$*"; }
artisan() { "$PHP" artisan "$@"; }

[ -f .env ] || { echo "No .env in $APP_DIR, stopping." >&2; exit 1; }

# Never leave the site in maintenance mode, whatever happens below.
DOWN=0
finish() {
    local code=$?
    if [ "$DOWN" = 1 ]; then artisan up || true; fi
    if [ "$code" != 0 ]; then echo -e "\n\033[31mDeploy FAILED (exit $code). The site is up; check the output above.\033[0m" >&2; fi
}
trap finish EXIT

step "Composer install"
if command -v "$COMPOSER" >/dev/null 2>&1; then
    "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-progress
else
    echo "composer not found, skipped (set COMPOSER=/path/to/composer if you need it)."
fi

step "Clear old caches"
artisan optimize:clear

step "Pending migrations"
if STATUS="$(artisan migrate:status --pending 2>&1)"; then
    PENDING="$(grep -c 'Pending' <<<"$STATUS" || true)"
else
    PENDING=1   # can't tell (e.g. first run): treat as pending, so we back up and migrate
fi
echo "$PENDING pending"

if [ "$PENDING" -gt 0 ]; then
    if [ "${SKIP_BACKUP:-0}" != 1 ]; then
        step "Database backup"
        DB_INFO="$(artisan tinker --execute='$c = config("database.connections.".config("database.default")); echo implode("|", [$c["driver"], $c["host"] ?? "", $c["port"] ?? "", $c["database"], $c["username"] ?? ""]);' 2>/dev/null | tail -n1)"
        IFS='|' read -r DRIVER DB_HOST DB_PORT DB_NAME DB_USER <<<"$DB_INFO"
        BACKUP_DIR="storage/app/backups"   # not web-accessible
        mkdir -p "$BACKUP_DIR"
        FILE="$BACKUP_DIR/db-$(date +%Y%m%d-%H%M%S).sql.gz"
        if [ "$DRIVER" = "mysql" ] || [ "$DRIVER" = "mariadb" ]; then
            if command -v mysqldump >/dev/null 2>&1; then
                DB_PASS="$(artisan tinker --execute='echo config("database.connections.".config("database.default").".password");' 2>/dev/null | tail -n1)"
                MYSQL_PWD="$DB_PASS" mysqldump --single-transaction --quick --no-tablespaces \
                    -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "$DB_USER" "$DB_NAME" | gzip > "$FILE"
                chmod 600 "$FILE"
                echo "Saved $FILE"
            else
                echo "mysqldump not found. Make a backup in Plesk first, or run with SKIP_BACKUP=1." >&2
                exit 1
            fi
        elif [ "$DRIVER" = "sqlite" ]; then
            cp "$DB_NAME" "${FILE%.sql.gz}.sqlite" && echo "Saved ${FILE%.sql.gz}.sqlite"
        else
            echo "Unknown database driver '$DRIVER': make a backup yourself, or run with SKIP_BACKUP=1." >&2
            exit 1
        fi
        # Keep the 5 newest backups.
        ls -1t "$BACKUP_DIR"/db-* 2>/dev/null | tail -n +6 | xargs -r rm -f
    fi

    step "Migrate (maintenance mode on)"
    artisan down --retry=15 && DOWN=1
    artisan migrate --force
    artisan up && DOWN=0
else
    echo "Nothing to migrate."
fi

if [ "${SKIP_SEED:-0}" != 1 ]; then
    step "Review drafts (existing items are skipped)"
    artisan db:seed --class='Database\Seeders\ContentDraftsSeeder' --force
fi

step "Storage link"
if [ -e public/storage ]; then echo "public/storage exists."; else artisan storage:link; fi

step "Caches"
artisan config:cache
artisan route:cache
artisan view:cache
artisan event:cache

step "Ownership"
if [ "$(id -u)" = 0 ]; then
    chown -R "$OWNER" "$APP_DIR"
    echo "chown $OWNER done."
else
    echo "Not root: files already belong to $(id -un), nothing to do."
fi

printf '\n\033[32mDeploy finished.\033[0m Check https://amirs.life in a private window.\n'
