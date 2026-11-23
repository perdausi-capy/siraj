<?php
/**
 * Hook callbacks for local_siraj.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core_user\hook\extend_default_homepage::class,
        'callback' => [\local_siraj\hook_callbacks::class, 'extend_default_homepage'],
    ],
    [
        'hook' => \core\hook\output\before_http_headers::class,
        'callback' => [\local_siraj\hook_callbacks::class, 'before_http_headers'],
    ],
];
