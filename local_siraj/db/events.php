<?php
/**
 * Event observers: keep the dashboard cache in step with Moodle.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [];
foreach (['\core\event\course_module_completion_updated', '\core\event\user_enrolment_created',
          '\core\event\user_enrolment_updated', '\core\event\user_enrolment_deleted', '\core\event\course_completed',
          '\core\event\user_updated', '\mod_quiz\event\attempt_submitted'] as $event) {
    $observers[] = ['eventname' => $event, 'callback' => '\local_siraj\observer::person_changed'];
}
foreach (['\core\event\course_module_created', '\core\event\course_module_updated', '\core\event\course_module_deleted',
          '\core\event\course_updated', '\core\event\course_deleted', '\core\event\role_assigned', '\core\event\role_unassigned'] as $event) {
    $observers[] = ['eventname' => $event, 'callback' => '\local_siraj\observer::course_changed'];
}
$observers[] = ['eventname' => '\\core\\event\\user_loggedin', 'callback' => '\\local_siraj\\observer::user_loggedin'];
foreach (['\\core\\event\\course_created', '\\core\\event\\course_updated'] as $event) {
    $observers[] = ['eventname' => $event, 'callback' => '\\local_siraj\\observer::course_saved'];
}
