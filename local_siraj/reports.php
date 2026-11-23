<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Reports: headline numbers, weekly pace, by department and by course, people, and a CSV / Excel export.
 * Managers see their team; people with local/siraj:viewreports see everyone, or one department.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

use local_siraj\reports;
use local_siraj\team;
use theme_siraj\icons;

require_login(null, false);
$all = has_capability('local/siraj:viewreports', context_system::instance());
$teamids = team::members((int)$USER->id);
if (!$all && !$teamids) {
    throw new required_capability_exception(context_system::instance(), 'local/siraj:viewreports', 'nopermissions', '');
}
$scope = $all ? optional_param('scope', $teamids ? 'team' : 'all', PARAM_ALPHA) : 'team';
$scope = $scope === 'team' && $teamids ? 'team' : 'all';
$dept = $scope === 'all' ? optional_param('dept', '', PARAM_TEXT) : '';
$download = optional_param('download', '', PARAM_ALPHA);

$params = array_filter(['scope' => $all ? $scope : null, 'dept' => $dept ?: null]);
$url = new moodle_url('/local/siraj/reports.php', $params);
\local_siraj\nav::page($url, get_string('reports', 'local_siraj'), 'reports');

$userids = $scope === 'team' ? $teamids : team::everyone($dept);
$toomany = count($userids) > reports::MAXPEOPLE;

if ($download && in_array($download, ['csv', 'excel'], true) && confirm_sesskey()) {
    \core_php_time_limit::raise(300);
    raise_memory_limit(MEMORY_HUGE);
    $people = [];
    foreach ($userids as $uid) {
        $people[$uid] = team::person($uid);
    }
    $name = 'siraj-report-' . ($dept !== '' ? clean_filename($dept) . '-' : '') . userdate(time(), '%Y%m%d');
    \core\dataformat::download_data($name, $download, reports::export_columns(), reports::export_rows($people));
    die();
}

$data = $toomany ? null : reports::build($userids);
$PAGE->requires->js_call_amd('local_siraj/charts', 'init');

$context = [
    'all' => $all,
    'scopes' => $all && $teamids ? [
        ['key' => 'team', 'text' => get_string('myteam', 'local_siraj'), 'active' => $scope === 'team',
            'url' => (new moodle_url('/local/siraj/reports.php', ['scope' => 'team']))->out(false)],
        ['key' => 'all', 'text' => get_string('everyone', 'local_siraj'), 'active' => $scope === 'all',
            'url' => (new moodle_url('/local/siraj/reports.php', ['scope' => 'all']))->out(false)],
    ] : [],
    'hasscopes' => $all && $teamids,
    'showdepts' => $scope === 'all',
    'deptoptions' => array_map(fn($d) => ['name' => $d, 'selected' => $d === $dept], team::departments()),
    'scope' => $scope,
    'formurl' => (new moodle_url('/local/siraj/reports.php'))->out(false),
    'subline' => $scope === 'team' ? get_string('reports_sub_team', 'local_siraj', count($userids))
        : ($dept !== '' ? get_string('reports_sub_dept', 'local_siraj', (object)['n' => count($userids), 'dept' => $dept])
        : get_string('reports_sub_all', 'local_siraj', count($userids))),
    'csvurl' => (new moodle_url($url, ['download' => 'csv', 'sesskey' => sesskey()]))->out(false),
    'excelurl' => (new moodle_url($url, ['download' => 'excel', 'sesskey' => sesskey()]))->out(false),
    'toomany' => $toomany,
    'maxpeople' => reports::MAXPEOPLE,
    'icon_download' => icons::svg('download'),
    'icon_users' => icons::svg('users'),
];

if ($data) {
    $h = $data['headline'];
    $s = $data['statuses'];
    $context += [
        'hasdata' => (bool)$data['people'],
        'kpis' => [
            ['value' => $h['compliance'] . '%', 'label' => get_string('kpi_compliance', 'local_siraj'),
                'sub' => get_string('nofm', 'local_siraj', (object)['n' => $h['placesdone'], 'of' => $h['places']]), 'icon' => icons::svg('shield'), 'tone' => 'mint'],
            ['value' => $h['people'], 'label' => get_string('kpi_people', 'local_siraj'),
                'sub' => get_string('kpi_active7', 'local_siraj', $h['active']), 'icon' => icons::svg('users'), 'tone' => 'blue'],
            ['value' => $h['completions'], 'label' => get_string('kpi_completions', 'local_siraj'),
                'sub' => get_string('last30', 'local_siraj'), 'icon' => icons::svg('award'), 'tone' => 'mint'],
            ['value' => $h['atrisk'], 'label' => get_string('st_atrisk', 'local_siraj'),
                'sub' => get_string('kpi_atrisk_sub', 'local_siraj'), 'icon' => icons::svg('clock'), 'tone' => 'amber'],
            ['value' => $h['overdue'], 'label' => get_string('st_overdue', 'local_siraj'),
                'sub' => get_string('kpi_overdue_sub', 'local_siraj'), 'icon' => icons::svg('bell'), 'tone' => 'red'],
        ],
        'weekschart' => reports::weeks_chart($data['weeks']),
        'weekstable' => array_map(fn($w) => ['week' => userdate($w['start'], '%d %b'), 'n' => $w['n'], 'learners' => $w['learners']], $data['weeks']),
        'weekstotal' => array_sum(array_column($data['weeks'], 'n')),
        'ring' => reports::ring($s),
        'departments' => array_map(fn($d) => $d + ['low' => $d['pct'] < 40,
            'url' => $all ? (new moodle_url('/local/siraj/reports.php', ['scope' => 'all', 'dept' => $d['name']]))->out(false) : null],
            $data['departments']),
        'courses' => array_map(fn($c) => $c + ['low' => $c['pct'] < 40], $data['courses']),
        'people' => array_values(array_map(fn($p) => [
            'name' => $p['fullname'], 'initials' => team::initials($p['user']), 'department' => $p['department'],
            'pct' => $p['pct'], 'chip' => team::chip($p['status']), 'reason' => $p['reason'],
            'finished' => get_string('nofm', 'local_siraj', (object)['n' => $p['finished'], 'of' => $p['required']]),
            'lastseen' => team::ago($p['lastaccess']), 'order' => array_search($p['status'], team::ORDER),
            'url' => (new moodle_url('/local/siraj/person.php', ['id' => $p['userid']]))->out(false),
        ], $data['people'])),
    ];
    usort($context['people'], fn($a, $b) => [$a['order'], $a['name']] <=> [$b['order'], $b['name']]);
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/reports', $context);
echo $OUTPUT->footer();
