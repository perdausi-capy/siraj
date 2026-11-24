#!/usr/bin/env bash
# Deploy theme_siraj + local_siraj into the local Moodle without a gap:
# copy to a staging folder, then swap folders with an atomic rename. Upgrade only when a version changed.
# Finally warm the caches so the first real page view is fast.
set -euo pipefail
cd "$(dirname "$0")"
swap() {  # $1 = source dir, $2 = target dir inside moodle/
    local src=$1 dst=moodle/$2 tmp=moodle/$2.new old=moodle/$2.old
    rm -rf "$tmp" "$old"
    cp -r "$src" "$tmp"
    if [ -d "$dst" ]; then mv "$dst" "$old"; fi
    mv "$tmp" "$dst"
    rm -rf "$old"
}
needs_upgrade=0
for p in theme_siraj:theme/siraj local_siraj:local/siraj; do
    src=${p%%:*}; dst=${p##*:}
    if ! cmp -s "$src/version.php" "moodle/$dst/version.php" 2>/dev/null; then needs_upgrade=1; fi
    swap "$src" "$dst"
done
if [ $needs_upgrade = 1 ]; then
    docker exec -w /var/www/html siraj-webserver-1 php admin/cli/upgrade.php --non-interactive | tail -1
else
    docker exec -w /var/www/html siraj-webserver-1 php admin/cli/purge_caches.php
fi
# Warm: theme CSS/JS and the main pages (anonymous + one logged-in pass).
jar=$(mktemp)
tok=$(curl -s -c "$jar" -b "$jar" http://localhost:8000/login/ | grep -o 'name="logintoken" value="[^"]*' | cut -d'"' -f4)
curl -s -c "$jar" -b "$jar" -o /dev/null --data-urlencode "username=layla" --data-urlencode "password@$HOME/.config/siraj-local/admin-pass" --data-urlencode "logintoken=$tok" http://localhost:8000/login/index.php
page=$(curl -s -b "$jar" http://localhost:8000/local/siraj/index.php)
for u in $(echo "$page" | grep -oE 'http://localhost:8000/(theme/styles\.php|lib/requirejs\.php|lib/javascript\.php)[^"]+' | sort -u); do curl -s -o /dev/null "$u"; done
for u in learning explore achievements inbox profile; do curl -s -b "$jar" -o /dev/null "http://localhost:8000/local/siraj/$u.php"; done
rm -f "$jar"
echo "deployed (upgrade: $needs_upgrade), caches warm"
