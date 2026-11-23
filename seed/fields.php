<?php
// Local lab: set the Siraj course fields (mandatory, duration) on the dummy courses.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
\local_siraj\coursefields::ensure();
$handler = \core_course\customfield\course_handler::create();
$values = ['HSE101' => [1, 32], 'COC101' => [1, 35], 'SEC101' => [1, 28], 'FIRE101' => [1, 25], 'PM201' => [0, 130], 'LEAD301' => [0, 80]];
foreach ($values as $short => [$mand, $dur]) {
    $course = $DB->get_record('course', ['shortname' => $short], '*', MUST_EXIST);
    $data = (object)['id' => $course->id, 'customfield_siraj_mandatory' => $mand, 'customfield_siraj_duration' => (string)$dur];
    $handler->instance_form_save($data);
    echo "$short: mandatory=$mand duration=$dur\n";
}
set_config('defaulthomepage', '/local/siraj/index.php');
purge_all_caches();
