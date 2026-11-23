<?php
// Re-save oversized course cover images as web-sized JPEGs (local_siraj\covers). Safe to run again.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
$n = 0;
foreach ($DB->get_fieldset_select('course', 'id', 'id <> ?', [SITEID]) as $id) {
    $n += \local_siraj\covers::optimise((int)$id);
}
echo "$n cover(s) optimised\n";
