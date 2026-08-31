#!/usr/bin/env bash

# Build the theme and synchronize runtime files to the confirmed Bluehost
# staging installation. A run without --deploy only previews the changes.
set -euo pipefail

readonly REMOTE_HOST="project-roadmap-bluehost"
readonly REMOTE_THEME="/home4/gixwigmy/public_html/staging/4590/wp-content/themes/project-roadmap/"
readonly STAGING_URL="https://projectroadmaptta.com/staging/4590"
readonly THEME_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

cd "$THEME_ROOT"

# Keep at least one option in the array for macOS's Bash 3 strict mode.
rsync_mode=(--archive)

case "${1:-}" in
  "")
    rsync_mode+=(--dry-run)
    action_label="Previewing"
    ;;
  --deploy)
    action_label="Deploying"
    ;;
  *)
    printf 'Usage: %s [--deploy]\n' "$0" >&2
    exit 2
    ;;
esac

# Refuse to run if the destination ever stops pointing at the verified stage.
case "$REMOTE_THEME" in
  */staging/4590/*) ;;
  *)
    printf 'Refusing to deploy outside the verified staging directory.\n' >&2
    exit 1
    ;;
esac

printf '%s Project Roadmap to %s\n' "$action_label" "$STAGING_URL"

# Compile source files locally so only browser-ready assets reach WordPress.
npm run gulpstyles
npm run gulpscripts

rsync \
  "${rsync_mode[@]}" \
  --compress \
  --human-readable \
  --itemize-changes \
  --relative \
  --chmod='Du=rwx,Dgo=rx,Fu=rw,Fgo=r' \
  --exclude='.DS_Store' \
  --exclude='assets/img/raw/' \
  ./404.php \
  ./footer.php \
  ./front-page.php \
  ./functions.php \
  ./header.php \
  ./index.php \
  ./page-resources.php \
  ./style.css \
  ./screenshot.png \
  ./assets/fonts/ \
  ./assets/img/ \
  ./assets/js/scripts-bundled.js \
  "${REMOTE_HOST}:${REMOTE_THEME}"

if [[ "${1:-}" == "--deploy" ]]; then
  # Normalize the whole staging theme, including unchanged legacy files that
  # rsync may skip while comparing content and timestamps.
  ssh "$REMOTE_HOST" find "$REMOTE_THEME" -type d -exec chmod 755 '{}' +
  ssh "$REMOTE_HOST" find "$REMOTE_THEME" -type f -exec chmod 644 '{}' +
  printf 'Deployment complete: %s\n' "$STAGING_URL"
else
  printf 'Dry run complete. Run npm run deploy:staging to upload these changes.\n'
fi
