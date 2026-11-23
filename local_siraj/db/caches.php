<?php
/**
 * Caches: each person's progress for the manager dashboards. Cleared by the observers in db/events.php whenever
 * their completion or enrolments change, or when a course's activities change; ten minutes at most otherwise.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$definitions = [
    'person' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
        'simpledata' => false,
        'ttl' => 600,
        'staticacceleration' => true,
        'staticaccelerationsize' => 200,
    ],
];
