<?php
// Compiles the Siraj theme's SCSS the way Moodle does and fails loudly if it can't. Needed because when the build
// fails, Moodle quietly keeps serving the last stylesheet that worked, so a broken rule looks like "no change".
// Run (local docker): docker exec -i -u www-data siraj-webserver-1 php < tools/check_scss.php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
$theme = theme_config::load('siraj');
$build = new ReflectionMethod($theme, 'get_css_content_from_scss');
try {
    $css = $build->invoke($theme, false);
} catch (Throwable $e) {
    $css = false;
    fwrite(STDERR, get_class($e) . ': ' . $e->getMessage() . "\n");
}
if (!is_string($css) || $css === '') {
    fwrite(STDERR, "SCSS BUILD FAILED: the theme stylesheet does not compile (Moodle would keep serving the old one)\n");
    exit(1);
}
echo 'scss ok (' . round(strlen($css) / 1024) . " KB)\n";
