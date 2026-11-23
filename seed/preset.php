<?php
// Switch the local lab to a brand preset: sets theme settings and the logo files. Run: php siraj_seed/preset.php siraj|ssh
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
$preset = $argv[1] ?? 'siraj';
$files = [
    'siraj' => ['logo' => 'siraj-logo.png', 'logocompact' => 'siraj-logo-white.png', 'panellogo' => 'siraj-logo-white.png',
                'site' => 'Siraj Learning'],
    'ssh'   => ['logo' => 'ssh-logo-placeholder.png', 'logocompact' => 'ssh-logo-compact-placeholder.png',
                'panellogo' => 'ssh-logo-placeholder.png', 'site' => 'SSH Learning'],
][$preset];
$ctx = context_system::instance();
$fs = get_file_storage();
foreach (['logo' => 'core_admin', 'logocompact' => 'core_admin', 'panellogo' => 'theme_siraj'] as $area => $component) {
    $fs->delete_area_files($ctx->id, $component, $area, 0);
    $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => $component, 'filearea' => $area, 'itemid' => 0,
        'filepath' => '/', 'filename' => $files[$area]], __DIR__ . '/' . $files[$area]);
    set_config($area, '/' . $files[$area], $component);
}
set_config('brandpreset', $preset, 'theme_siraj');
$DB->set_field('course', 'fullname', $files['site'], ['id' => SITEID]);
$DB->set_field('course', 'shortname', $files['site'], ['id' => SITEID]);
theme_reset_all_caches();
purge_all_caches();
echo "preset: $preset\n";
