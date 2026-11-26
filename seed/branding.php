<?php
// Keeps Moodle's name out of what learners see: no links to Moodle's documentation (help pop-ups, page footer).
// The theme hides the MoodleNet profile field and the docs link under error messages.
// Run: php siraj_seed/branding.php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');

set_config('docroot', '');
purge_caches(['muc' => true]);
echo "docs links off\n";
