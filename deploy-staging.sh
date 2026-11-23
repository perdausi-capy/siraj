#!/usr/bin/env bash
# Deploy theme_siraj + local_siraj to the staging server (ssh host sirajv2-staging) without a gap:
# upload, swap folders with an atomic rename, upgrade only when a version.php changed, then purge caches.
set -euo pipefail
cd "$(dirname "$0")"
tgz=$(mktemp --suffix=.tgz)
tar czf "$tgz" theme_siraj local_siraj
scp -q "$tgz" sirajv2-staging:/tmp/siraj-plugins.tgz
rm -f "$tgz"
ssh sirajv2-staging 'set -euo pipefail
M=/var/www/moodle; s=$(mktemp -d); tar xzf /tmp/siraj-plugins.tgz -C "$s"
up=0
for p in theme_siraj:theme/siraj local_siraj:local/siraj; do
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
# JS is not purged (only theme, strings and caches), and the first visitor should not wait for the theme or
# the Moodle JS bundle to be rebuilt: fetch them once here.
rev=$(sudo -u www-data php -r '"'"'define("CLI_SCRIPT",1);require "config.php";echo theme_get_revision()."/".$CFG->jsrev;'"'"')
curl -s -o /dev/null -m 120 "https://sirajv2-staging.capy-dev.com/theme/styles.php/siraj/${rev%%/*}_1/all" || true
curl -s -o /dev/null -m 120 "https://sirajv2-staging.capy-dev.com/lib/requirejs.php/${rev##*/}/core/first.js" || true
# Both stylesheets, English and Arabic, at the exact addresses the pages link to (first switch to Arabic was ~10 s).
for l in en ar; do for css in $(curl -s -m 60 "https://sirajv2-staging.capy-dev.com/login/index.php?lang=$l" | grep -o "https://[^\"]*/theme/styles.php/[^\"]*"); do
  curl -s -o /dev/null -m 120 "$css" || true; done; done
echo "staging deployed (upgrade: $up), warm"'
