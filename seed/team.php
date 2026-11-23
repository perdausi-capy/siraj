<?php
// Local lab, phase 2: a team of 12 for Omar Rahman with a realistic spread of progress, two extra mandatory courses
// (one already past its due date), job roles and compliance rules. Local only; every name is made up.
// Run: php siraj_seed/team.php <password-for-dummy-users>   (after seed.php, fields.php)
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/lib.php');

use local_siraj\setup;

$pass = $argv[1] ?? die("password arg required\n");
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();
\local_siraj\setup::ensure();
mt_srand(42);
$now = time();

$omar = $DB->get_record('user', ['username' => 'omar'], '*', MUST_EXIST);
$people = [
    // username, first, last, department, job role.
    ['layla', 'Layla', 'Hassan', 'Engineering', 'Engineer'],
    ['sara', 'Sara', 'Khalil', 'Engineering', 'Engineer'],
    ['yousef', 'Yousef', 'Nasser', 'Engineering', 'Engineer'],
    ['mariam', 'Mariam', 'Aziz', 'HR', 'HR officer'],
    ['adam', 'Adam', 'Farouk', 'Engineering', 'Engineer'],
    ['tariq', 'Tariq', 'Salem', 'Engineering', 'Engineer'],
    ['khalid', 'Khalid', 'Mansour', 'Operations', 'Site supervisor'],
    ['zaid', 'Zaid', 'Hamdan', 'Operations', 'Driver'],
    ['faisal', 'Faisal', 'Haddad', 'Operations', 'Driver'],
    ['noura', 'Noura', 'Saeed', 'Finance', 'Analyst'],
    ['reem', 'Reem', 'Abbas', 'Finance', 'Analyst'],
    ['huda', 'Huda', 'Karim', 'HR', 'HR officer'],
];
$u = [];
foreach ($people as [$username, $first, $last, $dept, $role]) {
    $user = $DB->get_record('user', ['username' => $username]) ?: $gen->create_user(['username' => $username, 'password' => $pass,
        'firstname' => $first, 'lastname' => $last, 'email' => "$username@example.com", 'city' => 'Dubai', 'country' => 'AE']);
    $DB->set_field('user', 'department', $dept, ['id' => $user->id]);
    setup::set_field(setup::FIELD_JOBROLE, $user->id, $role);
    // Yousef reports to Omar through the Line manager role; everyone else through the Manager profile field.
    if ($username === 'yousef') {
        role_assign($DB->get_field('role', 'id', ['shortname' => setup::ROLE]), $omar->id, context_user::instance($user->id)->id);
    } else {
        setup::set_field(setup::FIELD_MANAGER, $user->id, 'omar');
    }
    $u[$username] = $user;
}
setup::set_field(setup::FIELD_JOBROLE, $omar->id, 'Line manager');

// Two more mandatory courses: Anti-bribery (due 6 days ago) and Data protection (due in 9 days).
$cat = $DB->get_record('course_categories', ['name' => 'Compliance'], '*', MUST_EXIST);
$handler = \core_course\customfield\course_handler::create();
$extra = [
    ['ABC101', 'Anti-Bribery & Corruption', -6, 30, 'Gifts, hospitality and what to do if someone offers you something.'],
    ['DPE101', 'Data Protection Essentials', 9, 25, 'Personal data, consent and the UAE PDPL in plain language.'],
];
foreach ($extra as [$short, $name, $duedays, $minutes, $summary]) {
    if ($DB->record_exists('course', ['shortname' => $short])) {
        continue;
    }
    $course = $gen->create_course(['fullname' => $name, 'shortname' => $short, 'category' => $cat->id, 'summary' => $summary,
        'enablecompletion' => 1, 'numsections' => 3, 'format' => 'topics', 'startdate' => $now - 90 * DAYSECS]);
    $gen->create_module('page', ['course' => $course->id, 'section' => 1, 'name' => 'Why it matters',
        'content' => "<p>$summary</p>", 'completion' => COMPLETION_TRACKING_MANUAL]);
    $gen->create_module('page', ['course' => $course->id, 'section' => 2, 'name' => 'Real situations',
        'content' => '<p>Five short scenarios from day-to-day work.</p>', 'completion' => COMPLETION_TRACKING_MANUAL]);
    $gen->create_module('url', ['course' => $course->id, 'section' => 2, 'name' => 'Policy (PDF)',
        'externalurl' => 'https://example.com/policy.pdf', 'completion' => COMPLETION_TRACKING_MANUAL]);
    $gen->create_module('assign', ['course' => $course->id, 'section' => 3, 'name' => 'Declaration',
        'duedate' => $now + $duedays * DAYSECS, 'allowsubmissionsfromdate' => 0, 'completion' => COMPLETION_TRACKING_MANUAL,
        'assignsubmission_onlinetext_enabled' => 1]);
    $handler->instance_form_save((object)['id' => $course->id, 'customfield_siraj_mandatory' => 1,
        'customfield_siraj_duration' => (string)$minutes]);
    echo "course: $name\n";
}

$c = fn($short) => $DB->get_record('course', ['shortname' => $short], '*', MUST_EXIST);
// Activities done per course (in course order), and how many days ago they last made progress.
$plan = [
    'sara'   => [['HSE101', 5], ['COC101', 5], ['SEC101', 4], ['FIRE101', 4], ['ABC101', 4], 'last' => 1],
    'yousef' => [['HSE101', 5], ['COC101', 5], ['SEC101', 4], ['FIRE101', 4], ['ABC101', 4], 'last' => 2],
    'mariam' => [['HSE101', 5], ['COC101', 5], ['SEC101', 5], ['FIRE101', 3], ['DPE101', 3], 'last' => 0],
    'adam'   => [['HSE101', 3], ['COC101', 2], ['SEC101', 1], ['FIRE101', 0], 'last' => 20],
    'tariq'  => [['HSE101', 5], ['COC101', 5], ['SEC101', 4], ['FIRE101', 3], 'last' => 1],
    'khalid' => [['HSE101', 5], ['COC101', 5], ['FIRE101', 4], ['ABC101', 1], 'last' => 9],
    'zaid'   => [['HSE101', 2], ['ABC101', 0], 'last' => 25],
    'faisal' => [['HSE101', 0], 'last' => 0],
    'noura'  => [['HSE101', 5], ['COC101', 5], ['SEC101', 5], ['ABC101', 4], ['DPE101', 1], 'last' => 19],
    'reem'   => [['HSE101', 5], ['COC101', 5], ['SEC101', 5], ['ABC101', 4], ['DPE101', 2], 'last' => 3],
    'huda'   => [['HSE101', 5], ['COC101', 5], ['SEC101', 5], ['FIRE101', 5], ['DPE101', 4], 'last' => 4],
];
foreach ($plan as $username => $courses) {
    $user = $u[$username];
    $last = $courses['last'];
    unset($courses['last']);
    $stamps = [];
    foreach ($courses as [$short, $done]) {
        $course = $c($short);
        if (!is_enrolled(context_course::instance($course->id), $user)) {
            $gen->enrol_user($user->id, $course->id, 'student');
            $DB->set_field_select('user_enrolments', 'timecreated', $now - mt_rand(40, 80) * DAYSECS,
                'userid = ? AND enrolid IN (SELECT id FROM {enrol} WHERE courseid = ?)', [$user->id, $course->id]);
        }
        $completion = new completion_info($course);
        $i = 0;
        foreach (get_fast_modinfo($course)->get_cms() as $cm) {
            if (!$cm->completion) {
                continue;
            }
            if ($i++ >= $done) {
                break;
            }
            if (!$DB->record_exists('course_modules_completion', ['coursemoduleid' => $cm->id, 'userid' => $user->id])) {
                $completion->update_state($cm, COMPLETION_COMPLETE, $user->id);
            }
            $stamps[] = $cm->id;
        }
    }
    // Spread their completions over the last 11 weeks, ending "last" days ago.
    $n = count($stamps);
    foreach ($stamps as $k => $cmid) {
        $t = $now - $last * DAYSECS - (int)(($n - 1 - $k) * (75 - $last) * DAYSECS / max(1, $n)) - mt_rand(0, 5 * HOURSECS);
        $DB->set_field('course_modules_completion', 'timemodified', $t, ['coursemoduleid' => $cmid, 'userid' => $user->id]);
    }
    $DB->set_field('user', 'lastaccess', $now - max(0, $last - 1) * DAYSECS - mt_rand(600, 20000), ['id' => $user->id]);
    echo "$username: $n activities\n";
}
// Faisal has never signed in.
$DB->set_field('user', 'lastaccess', 0, ['id' => $u['faisal']->id]);

// Compliance rules per job role.
$rules = [
    'Engineer' => ['HSE101', 'COC101', 'SEC101', 'FIRE101'],
    'Site supervisor' => ['HSE101', 'FIRE101', 'ABC101', 'COC101'],
    'Driver' => ['HSE101', 'FIRE101'],
    'Analyst' => ['SEC101', 'DPE101', 'ABC101', 'COC101'],
    'HR officer' => ['COC101', 'DPE101'],
];
\local_siraj\matrix::set_rules(array_map(fn($shorts) => array_map(fn($s) => (int)$c($s)->id, $shorts), $rules));

purge_all_caches();
echo "team seed done\n";
