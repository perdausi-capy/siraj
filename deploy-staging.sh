#!/usr/bin/env bash
# Deploy theme_siraj + local_siraj to the staging server (ssh host sirajv2-staging) without a gap:
# upload, swap folders with an atomic rename, upgrade only when a version.php changed, then purge caches.
set -euo pipefail
cd "$(dirname "$0")"
# The theme must compile (checked on the local copy, deployed there first): a broken stylesheet would not show as an
# error on staging, Moodle would just keep the old one.
docker exec -i -u www-data siraj-webserver-1 php < tools/check_scss.php
tgz=$(mktemp --suffix=.tgz)
tar czf "$tgz" theme_siraj local_siraj tiny_sirajblocks
scp -q "$tgz" sirajv2-staging:/tmp/siraj-plugins.tgz
rm -f "$tgz"
ssh sirajv2-staging 'set -euo pipefail
M=/var/www/moodle; s=$(mktemp -d); tar xzf /tmp/siraj-plugins.tgz -C "$s"
up=0
for p in theme_siraj:theme/siraj local_siraj:local/siraj tiny_sirajblocks:lib/editor/tiny/plugins/sirajblocks; do
    src=$s/${p%%:*}; dst=$M/${p##*:}
    cmp -s "$src/version.php" "$dst/version.php" || up=1
    sudo rm -rf "$dst.new" "$dst.old"; sudo cp -r "$src" "$dst.new"; sudo chown -R root:root "$dst.new"
    [ -d "$dst" ] && sudo mv "$dst" "$dst.old"
    sudo mv "$dst.new" "$dst"; sudo rm -rf "$dst.old"
done
rm -rf "$s" /tmp/siraj-plugins.tgz
cd $M
if [ $up = 1 ]; then sudo -u www-data php admin/cli/upgrade.php --non-interactive | tail -1
else sudo -u www-data php admin/cli/purge_caches.php --muc --theme --lang --other; fi
# Build the theme CSS (English and Arabic) before anyone asks for it: a cold build makes every click wait ~6 s.
sudo -u www-data php admin/cli/build_theme_css.php --themes=siraj >/dev/null
# JS is not purged (only theme, strings and caches), and the first visitor should not wait for the theme or
# the Moodle JS bundle to be rebuilt: fetch them once here.
rev=$(sudo -u www-data php -r '"'"'define("CLI_SCRIPT",1);require "config.php";echo theme_get_revision()."/".$CFG->jsrev;'"'"')
curl -s -o /dev/null -m 120 "https://sirajv2-staging.capy-dev.com/theme/styles.php/siraj/${rev%%/*}_1/all" || true
curl -s -o /dev/null -m 120 "https://sirajv2-staging.capy-dev.com/lib/requirejs.php/${rev##*/}/core/first.js" || true
# Both stylesheets, English and Arabic, at the exact addresses the pages link to (first switch to Arabic was ~10 s).
for l in en ar; do for css in $(curl -s -m 60 "https://sirajv2-staging.capy-dev.com/login/index.php?lang=$l" | grep -o "https://[^\"]*/theme/styles.php/[^\"]*"); do
  curl -s -o /dev/null -m 120 "$css" || true; done; done
echo "staging deployed (upgrade: $up), warm"'
