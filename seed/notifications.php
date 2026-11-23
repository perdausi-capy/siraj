<?php
// Local lab: dummy notifications and one badge for layla. Local only; all content is made up.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/badgeslib.php');

$layla = $DB->get_record('user', ['username' => 'layla'], '*', MUST_EXIST);
$admin = get_admin();
$DB->delete_records('notifications', ['useridto' => $layla->id]);
$c = fn($short) => $DB->get_record('course', ['shortname' => $short], '*', MUST_EXIST);
$now = time();
$rows = [
    ['mod_assign', 'assign_notification', 'Information Security Essentials is due in 3 days',
     'You have 3 activities left in this mandatory course.', '/local/siraj/course.php?id=' . $c('SEC101')->id, 'Continue', $now - 2 * HOURSECS, 0],
    ['mod_quiz', 'attempt_overdue', 'Knowledge check closes on 14.11.2026',
     'One short quiz is left in Workplace Health & Safety. Finish it to complete the course.', '/local/siraj/course.php?id=' . $c('HSE101')->id, 'Open course', $now - 5 * HOURSECS, 0],
    ['moodle', 'coursecompleted', 'Your certificate is ready',
     'You completed Code of Conduct & Ethics. Your certificate is ready to view and download.', '/local/siraj/achievements.php', 'View certificate', $now - 26 * HOURSECS, 0],
    ['moodle', 'gradenotifications', 'Your reflection was reviewed',
     'Omar Rahman reviewed “Reflection: apply it to your role”.', '/local/siraj/course.php?id=' . $c('HSE101')->id, 'View', $now - 3 * DAYSECS, $now - 2 * DAYSECS],
    ['moodle', 'notices', 'New in the library: Leadership for Line Managers',
     'Coaching, feedback and effective one-to-ones. Open for self-enrolment.', '/local/siraj/explore.php', 'Explore', $now - 9 * DAYSECS, $now - 8 * DAYSECS],
];
foreach ($rows as [$component, $eventtype, $subject, $text, $url, $urlname, $created, $read]) {
    $DB->insert_record('notifications', (object)[
        'useridfrom' => $admin->id, 'useridto' => $layla->id, 'subject' => $subject, 'fullmessage' => $text,
        'fullmessageformat' => FORMAT_PLAIN, 'fullmessagehtml' => '', 'smallmessage' => $text, 'component' => $component,
        'eventtype' => $eventtype, 'contexturl' => (new moodle_url($url))->out(false), 'contexturlname' => $urlname,
        'timeread' => $read ?: null, 'timecreated' => $created, 'customdata' => null,
    ]);
}
echo count($rows) . " notifications\n";

set_config('enablebadges', 1);
if (!$DB->record_exists('badge', ['name' => 'Safety First'])) {
    $gen = phpunit_util::get_data_generator()->get_plugin_generator('core_badges');
    $badge = $gen->create_badge(['name' => 'Safety First', 'description' => 'Completed all compliance basics.',
        'status' => BADGE_STATUS_ACTIVE, 'type' => BADGE_TYPE_SITE, 'usercreated' => $admin->id]);
    $b = new badge($badge->id);
    $b->issue($layla->id, true);
    echo "badge issued\n";
}
purge_all_caches();
