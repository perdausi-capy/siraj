<?php
// Dummy data for the local Siraj theme lab. Local only; all names are made up.
// Run: php siraj_seed/seed.php <password-for-dummy-users>
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/course/lib.php');

$pass = $argv[1] ?? die("password arg required\n");
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();

set_config('enablecompletion', 1);

function mkuser($gen, $username, $first, $last, $dept, $pass) {
    global $DB;
    if ($u = $DB->get_record('user', ['username' => $username])) {
        return $u;
    }
    return $gen->create_user(['username' => $username, 'password' => $pass, 'firstname' => $first,
        'lastname' => $last, 'email' => "$username@example.com", 'department' => $dept, 'city' => 'Dubai', 'country' => 'AE']);
}
$learner = mkuser($gen, 'layla', 'Layla', 'Hassan', 'Engineering', $pass);
$manager = mkuser($gen, 'omar', 'Omar', 'Rahman', 'HR', $pass);
$others = [];
foreach ([['sara','Sara','Khalil'],['yousef','Yousef','Nasser'],['mariam','Mariam','Aziz'],['adam','Adam','Farouk']] as $o) {
    $others[] = mkuser($gen, $o[0], $o[1], $o[2], 'Engineering', $pass);
}

$cats = [];
foreach (['Compliance', 'Professional Development'] as $c) {
    $cats[$c] = $DB->get_record('course_categories', ['name' => $c]) ?: $gen->create_category(['name' => $c]);
}

$courses = [
  ['hse', 'Workplace Health & Safety', 'HSE101', 'Compliance', 0.75,
   'Site rules, risk assessment and reporting near misses. Mandatory for all staff every year.'],
  ['conduct', 'Code of Conduct & Ethics', 'COC101', 'Compliance', 1.0,
   'How we work with clients, partners and each other. Includes anti-bribery and conflicts of interest.'],
  ['infosec', 'Information Security Essentials', 'SEC101', 'Compliance', 0.4,
   'Phishing, passwords, data handling and what to do if something looks wrong.'],
  ['fire', 'Fire Safety & Emergency Response', 'FIRE101', 'Compliance', 0.0,
   'Evacuation routes, extinguisher types and the role of fire wardens.'],
  ['pm', 'Project Management Fundamentals', 'PM201', 'Professional Development', 0.2,
   'Scope, schedule, cost and stakeholders: the basics every project team member needs.'],
  ['lead', 'Leadership for Line Managers', 'LEAD301', 'Professional Development', null,
   'Coaching, feedback and running effective one-to-ones. Open for self-enrolment.'],
];
$sections = ['Introduction', 'Key rules', 'Practice', 'Assessment'];
$fs = get_file_storage();

foreach ($courses as [$key, $name, $short, $cat, $progress, $summary]) {
    if ($DB->record_exists('course', ['shortname' => $short])) {
        continue;
    }
    $course = $gen->create_course(['fullname' => $name, 'shortname' => $short, 'category' => $cats[$cat]->id,
        'summary' => $summary, 'enablecompletion' => 1, 'numsections' => 4, 'format' => 'topics', 'startdate' => time() - 30 * DAYSECS]);
    foreach ($sections as $i => $sname) {
        $sec = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $i + 1]);
        course_update_section($course, $sec, ['name' => $sname]);
    }
    // Cover image.
    $ctx = context_course::instance($course->id);
    $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => 'course', 'filearea' => 'overviewfiles',
        'itemid' => 0, 'filepath' => '/', 'filename' => "$key.png"], __DIR__ . "/img/$key.png");

    $mods = [];
    $mods[] = $gen->create_module('page', ['course' => $course->id, 'section' => 1, 'name' => 'Welcome and objectives',
        'content' => "<p>$summary</p>", 'completion' => COMPLETION_TRACKING_MANUAL]);
    $mods[] = $gen->create_module('page', ['course' => $course->id, 'section' => 2, 'name' => 'The rules in practice',
        'content' => '<p>Short reading with examples from day-to-day work.</p>', 'completion' => COMPLETION_TRACKING_MANUAL]);
    $mods[] = $gen->create_module('url', ['course' => $course->id, 'section' => 2, 'name' => 'Policy document (PDF)',
        'externalurl' => 'https://example.com/policy.pdf', 'completion' => COMPLETION_TRACKING_MANUAL]);
    $mods[] = $gen->create_module('assign', ['course' => $course->id, 'section' => 3, 'name' => 'Reflection: apply it to your role',
        'duedate' => time() + (3 + count($mods) * 2 + strlen($key)) * DAYSECS, 'completion' => COMPLETION_TRACKING_MANUAL,
        'assignsubmission_onlinetext_enabled' => 1]);
    $quiz = $gen->create_module('quiz', ['course' => $course->id, 'section' => 4, 'name' => 'Knowledge check',
        'timeclose' => time() + (7 + strlen($short)) * DAYSECS, 'completion' => COMPLETION_TRACKING_MANUAL, 'grade' => 10, 'sumgrades' => 3]);
    $mods[] = $quiz;
    $qgen = $gen->get_plugin_generator('core_question');
    $qcat = $qgen->create_question_category(['contextid' => context_module::instance($quiz->cmid)->id]);
    $qs = [
        $qgen->create_question('multichoice', 'one_of_four', ['category' => $qcat->id, 'name' => 'Q1',
            'questiontext' => ['text' => "Which statement best matches the main rule in \"$name\"?", 'format' => FORMAT_HTML]]),
        $qgen->create_question('truefalse', null, ['category' => $qcat->id, 'name' => 'Q2',
            'questiontext' => ['text' => 'You should report a near miss even if nobody was hurt.', 'format' => FORMAT_HTML]]),
        $qgen->create_question('shortanswer', null, ['category' => $qcat->id, 'name' => 'Q3',
            'questiontext' => ['text' => 'Name the person you report to first.', 'format' => FORMAT_HTML]]),
    ];
    foreach ($qs as $q) {
        quiz_add_quiz_question($q->id, $DB->get_record('quiz', ['id' => $quiz->id]));
    }

    if ($progress === null) {
        $DB->set_field('course', 'visible', 1, ['id' => $course->id]);
        $gen->create_module('page', ['course' => $course->id, 'section' => 1, 'name' => 'About this course', 'content' => '<p>Self-enrol to start.</p>']);
        $enrol = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'self']);
        if ($enrol) { $DB->set_field('enrol', 'status', ENROL_INSTANCE_ENABLED, ['id' => $enrol->id]); }
        continue;
    }
    $gen->enrol_user($learner->id, $course->id, 'student');
    $gen->enrol_user($manager->id, $course->id, 'editingteacher');
    foreach ($others as $o) { $gen->enrol_user($o->id, $course->id, 'student'); }

    $completion = new completion_info($DB->get_record('course', ['id' => $course->id]));
    $done = (int) round($progress * count($mods));
    foreach (array_slice($mods, 0, $done) as $m) {
        $cm = get_coursemodule_from_id(null, $m->cmid, 0, false, MUST_EXIST);
        $completion->update_state($cm, COMPLETION_COMPLETE, $learner->id);
    }
    // Give the "recently accessed" block something to show.
    $DB->insert_record('user_lastaccess', ['userid' => $learner->id, 'courseid' => $course->id, 'timeaccess' => time() - rand(60, 86400 * 5)]);
    echo "course: $name ($done/" . count($mods) . ")\n";
}

// Front page / site settings.
set_config('frontpage', '6');          // course search + list
set_config('frontpageloggedin', '5,6');
set_config('defaulthomepage', HOMEPAGE_MY);
purge_all_caches();
echo "seed done\n";
