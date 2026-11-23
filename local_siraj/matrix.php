<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj compliance matrix: required courses per job role, where everyone stands, and enrolling the missing in one go.
 * Managers see their team; people with local/siraj:viewreports see everyone (or one department);
 * local/siraj:managecompliance sets the rules and enrols.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

use local_siraj\matrix;
use local_siraj\team;
use theme_siraj\icons;

require_login(null, false);
$system = context_system::instance();
$all = has_capability('local/siraj:viewreports', $system);
$canmanage = has_capability('local/siraj:managecompliance', $system);
$teamids = team::members((int)$USER->id);
if (!$all && !$teamids) {
    throw new required_capability_exception($system, 'local/siraj:viewreports', 'nopermissions', '');
}
$scope = $all ? optional_param('scope', $teamids ? 'team' : 'all', PARAM_ALPHA) : 'team';
$scope = $scope === 'team' && $teamids ? 'team' : 'all';
$dept = $scope === 'all' ? optional_param('dept', '', PARAM_TEXT) : '';
$url = new moodle_url('/local/siraj/matrix.php', array_filter(['scope' => $all ? $scope : null, 'dept' => $dept ?: null]));
\local_siraj\nav::page($url, get_string('compliance', 'local_siraj'), 'matrix');
$userids = $scope === 'team' ? $teamids : team::everyone($dept);

if ($canmanage && data_submitted() && confirm_sesskey()) {
    $action = required_param('action', PARAM_ALPHA);
    if ($action === 'rules') {
        // Each ticked box is "<role index>:<course id>"; role names come in rolename[<role index>].
        $names = optional_param_array('rolename', [], PARAM_TEXT);
        $rules = array_fill_keys(array_map('trim', $names), []);
        foreach (optional_param_array('rule', [], PARAM_RAW_TRIMMED) as $pair) {
            [$i, $courseid] = array_map('intval', explode(':', $pair . ':0'));
            if (isset($names[$i])) {
                $rules[trim($names[$i])][] = $courseid;
            }
        }
        $newrole = trim(optional_param('newrole', '', PARAM_TEXT));
        if ($newrole !== '') {
            $rules[$newrole] = optional_param_array('newcourses', [], PARAM_INT);
        }
        matrix::set_rules($rules);
        redirect($url, get_string('rulessaved', 'local_siraj'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
    if ($action === 'enrol') {
        $courseid = required_param('course', PARAM_INT);
        $grid = matrix::grid($userids);
        $missing = [];
        foreach ($grid['rows'] as $row) {
            foreach ($row['cells'] as $cell) {
                if ($cell['courseid'] === $courseid && $cell['state'] === 'missing') {
                    $missing[] = $row['userid'];
                }
            }
        }
        $n = matrix::enrol($missing, $courseid);
        redirect($url, get_string('enrolledn', 'local_siraj', $n), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

$grid = matrix::grid($userids);
$rules = matrix::rules();
$courses = matrix::all_courses();
$ruleset = [];
foreach (matrix::roles() as $i => $role) {
    $ruleset[] = ['i' => $i, 'role' => $role, 'courses' => array_map(fn($cid, $name) => ['id' => $cid, 'name' => $name,
        'checked' => in_array($cid, $rules[$role] ?? [], true)], array_keys($courses), $courses),
        'count' => count($rules[$role] ?? [])];
}
$c = $grid['counts'];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/matrix', [
    'subline' => $scope === 'team' ? get_string('reports_sub_team', 'local_siraj', count($userids))
        : ($dept !== '' ? get_string('reports_sub_dept', 'local_siraj', (object)['n' => count($userids), 'dept' => $dept])
        : get_string('reports_sub_all', 'local_siraj', count($userids))),
    'hasscopes' => $all && $teamids,
    'scopes' => [
        ['text' => get_string('myteam', 'local_siraj'), 'active' => $scope === 'team',
            'url' => (new moodle_url('/local/siraj/matrix.php', ['scope' => 'team']))->out(false)],
        ['text' => get_string('everyone', 'local_siraj'), 'active' => $scope === 'all',
            'url' => (new moodle_url('/local/siraj/matrix.php', ['scope' => 'all']))->out(false)],
    ],
    'showdepts' => $scope === 'all',
    'deptoptions' => array_map(fn($d) => ['name' => $d, 'selected' => $d === $dept], team::departments()),
    'formurl' => (new moodle_url('/local/siraj/matrix.php'))->out(false),
    'counts' => [
        ['state' => 'missing', 'n' => $c['missing'], 'label' => get_string('mx_missing', 'local_siraj')],
        ['state' => 'overdue', 'n' => $c['overdue'], 'label' => get_string('mx_overdue', 'local_siraj')],
        ['state' => 'todo', 'n' => $c['todo'], 'label' => get_string('mx_todo', 'local_siraj')],
        ['state' => 'done', 'n' => $c['done'], 'label' => get_string('mx_done', 'local_siraj')],
    ],
    'columns' => array_map(fn($col) => $col + ['canenrol' => $canmanage && $col['missing'] > 0], $grid['columns']),
    'rows' => $grid['rows'],
    'hasrows' => (bool)$grid['rows'] && (bool)$grid['columns'],
    'canmanage' => $canmanage,
    'ruleset' => $ruleset,
    'hasrules' => (bool)$ruleset,
    'allcourses' => array_map(fn($cid, $name) => ['id' => $cid, 'name' => $name], array_keys($courses), $courses),
    'action' => $url->out(false),
    'sesskey' => sesskey(),
    'icon_users' => icons::svg('users'),
    'icon_shield' => icons::svg('shield'),
]);
echo $OUTPUT->footer();
