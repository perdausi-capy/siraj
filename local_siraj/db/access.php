<?php
/**
 * Capabilities for the Siraj manager and admin dashboards.
 *
 * - viewteam: given by the "Line manager" role, assigned in each person's user context (Moodle's own
 *   "assign roles relative to this user"). The Manager profile field is the other way to build a team.
 * - viewreports: everyone's progress, reports, compliance matrix and Admin studio.
 * - managecompliance: set which job roles need which courses, and enrol the people who are missing.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/siraj:viewteam' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'archetypes' => [],
    ],
    'local/siraj:viewreports' => [
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => ['manager' => CAP_ALLOW],
    ],
    'local/siraj:managecompliance' => [
        'riskbitmask' => RISK_PERSONAL | RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => ['manager' => CAP_ALLOW],
    ],
];
