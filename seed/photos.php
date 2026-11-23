<?php
// Replaces the abstract course covers with stock photos (CC0, StockSnap; see img/photo/CREDITS.txt).
// Demo courses only; safe to run again.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');

$fs = get_file_storage();
$map = ['HSE101' => 'hse', 'COC101' => 'conduct', 'SEC101' => 'infosec', 'FIRE101' => 'fire', 'PM201' => 'pm',
    'LEAD301' => 'lead', 'ABC101' => 'abc', 'DPE101' => 'dpe', 'AR-HSE' => 'ar-safety', 'AR-CS' => 'ar-service'];
foreach ($map as $short => $img) {
    if (!$course = $DB->get_record('course', ['shortname' => $short])) {
        echo "$short: not found\n";
        continue;
    }
    $ctx = context_course::instance($course->id);
    $fs->delete_area_files($ctx->id, 'course', 'overviewfiles');
    $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => 'course', 'filearea' => 'overviewfiles',
        'itemid' => 0, 'filepath' => '/', 'filename' => "$img.jpg"], __DIR__ . "/img/photo/$img.jpg");
    echo "cover: $short\n";
}
// Category blurbs (the generator leaves Lorem ipsum on fresh installs).
foreach (['Compliance' => 'Mandatory training every employee completes, renewed each year.',
        'Professional Development' => 'Optional courses to grow your skills and career.'] as $name => $text) {
    if ($cat = $DB->get_record('course_categories', ['name' => $name])) {
        if (stripos($cat->description, 'lorem') !== false) {
            $DB->set_field('course_categories', 'description', "<p>$text</p>", ['id' => $cat->id]);
        }
    }
}
// Course images are cached with the course list.
\cache_helper::purge_by_event('changesincourse');
rebuild_course_cache(0, true);
