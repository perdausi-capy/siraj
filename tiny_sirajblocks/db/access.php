<?php
/**
 * Capabilities.
 *
 * @package   tiny_sirajblocks
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'tiny/sirajblocks:use' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => ['user' => CAP_ALLOW],
    ],
];
