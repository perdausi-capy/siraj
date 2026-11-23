<?php
// Local lab: a book, a SCORM package and an H5P activity in Project Management Fundamentals, to test them inside
// Learning mode. Packages are Moodle's own test fixtures. Local only.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();
$course = $DB->get_record('course', ['shortname' => 'PM201'], '*', MUST_EXIST);
$has = fn($mod, $name) => $DB->record_exists($mod, ['course' => $course->id, 'name' => $name]);

if (!$has('book', 'Project planning handbook')) {
    $book = $gen->create_module('book', ['course' => $course->id, 'section' => 2, 'name' => 'Project planning handbook',
        'intro' => 'A short handbook in three chapters.', 'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1]);
    $bg = $gen->get_plugin_generator('mod_book');
    foreach ([
        ['Define the scope', '<p>Write down what the project will deliver and, just as important, what it will not.</p><ul><li>Goals and success measures</li><li>Deliverables</li><li>Out of scope</li></ul>'],
        ['Build the schedule', '<p>Break the work into tasks, estimate each one, and find the critical path.</p>'],
        ['Track and report', '<p>Compare progress with the plan every week and report early when something slips.</p>'],
    ] as $i => [$title, $content]) {
        $bg->create_chapter(['bookid' => $book->id, 'title' => $title, 'content' => $content, 'pagenum' => $i + 1]);
    }
    echo "book\n";
}
if (!$has('scorm', 'Golf explained (SCORM)')) {
    $gen->create_module('scorm', ['course' => $course->id, 'section' => 2, 'name' => 'Golf explained (SCORM)',
        'packagefilepath' => $CFG->dirroot . '/mod/scorm/tests/packages/RuntimeBasicCalls_SCORM20043rdEdition.zip',
        'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionstatusrequired' => 4, 'popup' => 0]);
    echo "scorm\n";
}
if (!$has('h5pactivity', 'Quick check (H5P)')) {
    $gen->create_module('h5pactivity', ['course' => $course->id, 'section' => 2, 'name' => 'Quick check (H5P)',
        'packagefilepath' => $CFG->dirroot . '/h5p/tests/fixtures/multiple-choice-2-6.h5p',
        'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1, 'enabletracking' => 1, 'grademethod' => 1]);
    echo "h5p\n";
}
rebuild_course_cache($course->id, true);
echo "content done\n";
