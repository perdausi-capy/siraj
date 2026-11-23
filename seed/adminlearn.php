<?php
// Enrols the admin as a learner in a few demo courses with partial progress, so Home shows the "next step"
// banner, "Pick up where you left off" and a finished course. Demo data only.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');

$admin = get_admin();
\core\session\manager::set_user($admin);
$gen = phpunit_util::get_data_generator();
$now = time();
// Course shortname => activities done (and days ago it was last opened).
$plan = ['HSE101' => [3, 1], 'SEC101' => [2, 2], 'DPE101' => [1, 4], 'COC101' => [99, 9]];
foreach ($plan as $short => [$done, $ago]) {
    if (!$course = $DB->get_record('course', ['shortname' => $short])) {
        echo "$short: not found\n";
        continue;
    }
    if (!is_enrolled(context_course::instance($course->id), $admin)) {
        $gen->enrol_user($admin->id, $course->id, 'student');
    }
    $completion = new completion_info($course);
    $i = 0;
    foreach (get_fast_modinfo($course, $admin->id)->get_cms() as $cm) {
        if (!$cm->completion || $i++ >= $done) {
            continue;
        }
        if (!$DB->record_exists('course_modules_completion', ['coursemoduleid' => $cm->id, 'userid' => $admin->id])) {
            $completion->update_state($cm, COMPLETION_COMPLETE, $admin->id);
        }
        $DB->set_field('course_modules_completion', 'timemodified', $now - $ago * DAYSECS - $i * HOURSECS,
            ['coursemoduleid' => $cm->id, 'userid' => $admin->id]);
    }
    $la = $DB->get_record('user_lastaccess', ['userid' => $admin->id, 'courseid' => $course->id]);
    $la ? $DB->set_field('user_lastaccess', 'timeaccess', $now - $ago * DAYSECS, ['id' => $la->id])
        : $DB->insert_record('user_lastaccess', ['userid' => $admin->id, 'courseid' => $course->id, 'timeaccess' => $now - $ago * DAYSECS]);
    echo "$short: " . min($done, $i) . "/$i done\n";
}
