<?php
// Turn off the "Admin bookmarks" block: it was the only block in the admin pages' side drawer, so with it off the
// drawer and its toggle button disappear. Run once per site: php siraj_seed/adminblocks.php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
\core\plugininfo\block::enable_plugin('admin_bookmarks', 0);
purge_all_caches();
echo "admin bookmarks block: off\n";
