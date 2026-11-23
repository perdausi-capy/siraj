<?php
// Local lab: a dummy English-speaking teacher (hana) who teaches Project Management and Health & Safety. Local only.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();
$pass = $argv[1] ?? die("password arg required\n");
$hana = $DB->get_record('user', ['username' => 'hana']) ?: $gen->create_user(['username' => 'hana', 'password' => $pass,
    'firstname' => 'Hana', 'lastname' => 'Yousef', 'email' => 'hana@example.com', 'department' => 'Learning & Development',
    'city' => 'Dubai', 'country' => 'AE', 'lang' => 'en']);
foreach (['PM201', 'HSE101'] as $short) {
    $course = $DB->get_record('course', ['shortname' => $short], '*', MUST_EXIST);
    if (!is_enrolled(context_course::instance($course->id), $hana)) {
        $gen->enrol_user($hana->id, $course->id, 'editingteacher');
    }
}
echo "teacher hana ready\n";
