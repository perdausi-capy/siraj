<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj My team: who is on track, who needs a reminder, and how the team is doing course by course.
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
$url = new moodle_url('/local/siraj/team.php');
\local_siraj\nav::page($url, get_string('myteam', 'local_siraj'), 'team');

$memberids = team::members((int)$USER->id);
$PAGE->requires->strings_for_js(['nselected'], 'local_siraj');

if (data_submitted() && confirm_sesskey()) {
    $ids = array_values(array_intersect(array_map('intval', optional_param_array('users', [], PARAM_INT)), $memberids));
    [$sent, $skipped] = team::remind($ids, $USER);
    $msg = get_string('reminded', 'local_siraj', $sent) . ($skipped ? ' ' . get_string('remindskipped', 'local_siraj', $skipped) : '');
    redirect($url, $msg, null, $sent ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_INFO);
}

$now = time();
$report = reports::build($memberids, $now);
$people = $report['people'];
$reminded = team::last_reminded($memberids);
$roles = \local_siraj\setup::field_values(\local_siraj\setup::FIELD_JOBROLE, $memberids);

uasort($people, fn($a, $b) => [array_search($a['status'], team::ORDER), $a['fullname']]
    <=> [array_search($b['status'], team::ORDER), $b['fullname']]);
$rows = [];
foreach ($people as $p) {
    $chip = team::chip($p['status']);
    $rows[] = [
        'id' => $p['userid'], 'name' => $p['fullname'], 'initials' => team::initials($p['user']),
        'sub' => implode(' · ', array_filter([$roles[$p['userid']] ?? '', $p['department']])),
        'pct' => $p['pct'], 'done' => $p['done'], 'total' => $p['total'],
        'progresslabel' => get_string('ncoursesdone', 'local_siraj', (object)['n' => $p['finished'], 'of' => $p['required']]),
        'next' => $p['status'] === 'done' ? get_string('allcomplete', 'local_siraj') : $p['next'],
        'nextcourse' => $p['status'] === 'done' ? '' : $p['nextcourse'],
        'due' => $p['due'] && $p['status'] !== 'done' ? userdate($p['due'], '%d.%m') : '',
        'chip' => $chip, 'reason' => $p['reason'],
        'attention' => in_array($p['status'], ['overdue', 'atrisk', 'notstarted'], true),
        'remindable' => !in_array($p['status'], ['done', 'none'], true),
        'lastseen' => team::ago($p['lastaccess']),
        'reminded' => isset($reminded[$p['userid']]) ? get_string('remindedago', 'local_siraj', team::ago($reminded[$p['userid']])) : '',
        'url' => (new moodle_url('/local/siraj/person.php', ['id' => $p['userid']]))->out(false),
        'search' => core_text::strtolower($p['fullname'] . ' ' . $p['department'] . ' ' . ($roles[$p['userid']] ?? '')),
    ];
}
$counts = $report['statuses'];
$h = $report['headline'];

// Deadlines in the next 30 days for people who have not finished.
$deadlines = [];
foreach ($people as $p) {
    foreach ($p['courses'] as $c) {
        if (!$c['finished'] && $c['due'] && $c['due'] < $now + 30 * DAYSECS) {
            $deadlines[] = ['name' => $p['fullname'], 'course' => $c['name'], 'time' => $c['due'],
                'when' => $c['due'] < $now ? \local_siraj\plural::str('overdue_by', (int)ceil(($now - $c['due']) / DAYSECS))
                    : userdate($c['due'], '%a %d.%m'),
                'late' => $c['due'] < $now, 'initials' => team::initials($p['user'])];
        }
    }
}
usort($deadlines, fn($a, $b) => $a['time'] <=> $b['time']);

$soonest = 0;
foreach ($people as $p) {
    if ($p['due'] >= $now && $p['status'] !== 'done' && (!$soonest || $p['due'] < $soonest)) {
        $soonest = $p['due'];
    }
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/team', [
    'empty' => !$people,
    'subline' => get_string('team_sub', 'local_siraj', count($people)),
    'kpis' => [
        ['value' => $h['compliance'] . '%', 'label' => get_string('kpi_compliance', 'local_siraj'),
            'sub' => get_string('nofm', 'local_siraj', (object)['n' => $h['placesdone'], 'of' => $h['places']]), 'icon' => icons::svg('shield'), 'tone' => 'mint'],
        ['value' => ($counts['ontrack'] ?? 0) + ($counts['done'] ?? 0), 'label' => get_string('kpi_ontrack', 'local_siraj'),
            'sub' => get_string('kpi_ontrack_sub', 'local_siraj', $counts['done'] ?? 0), 'icon' => icons::svg('check-circle'), 'tone' => 'blue'],
        ['value' => $counts['atrisk'] ?? 0, 'label' => get_string('st_atrisk', 'local_siraj'),
            'sub' => get_string('kpi_atrisk_sub', 'local_siraj'), 'icon' => icons::svg('clock'), 'tone' => 'amber'],
        ['value' => $counts['overdue'] ?? 0, 'label' => get_string('st_overdue', 'local_siraj'),
            'sub' => get_string('kpi_overdue_sub', 'local_siraj'), 'icon' => icons::svg('bell'), 'tone' => 'red'],
        ['value' => $soonest ? userdate($soonest, '%d.%m') : '–', 'label' => get_string('kpi_nextdeadline', 'local_siraj'),
            'sub' => $soonest ? userdate($soonest, '%A') : get_string('nodeadlines', 'local_siraj'), 'icon' => icons::svg('calendar'), 'tone' => ''],
    ],
    'filters' => [
        ['key' => 'all', 'text' => get_string('all', 'local_siraj'), 'n' => count($rows), 'active' => true],
        ['key' => 'attention', 'text' => get_string('needsattention', 'local_siraj'),
            'n' => ($counts['overdue'] ?? 0) + ($counts['atrisk'] ?? 0) + ($counts['notstarted'] ?? 0)],
        ['key' => 'overdue', 'text' => get_string('st_overdue', 'local_siraj'), 'n' => $counts['overdue'] ?? 0],
        ['key' => 'atrisk', 'text' => get_string('st_atrisk', 'local_siraj'), 'n' => $counts['atrisk'] ?? 0],
        ['key' => 'ontrack', 'text' => get_string('st_ontrack', 'local_siraj'), 'n' => $counts['ontrack'] ?? 0],
        ['key' => 'done', 'text' => get_string('st_done', 'local_siraj'), 'n' => $counts['done'] ?? 0],
    ],
    'rows' => $rows,
    'ring' => reports::ring($counts),
    'courses' => array_map(fn($c) => $c + ['good' => $c['pct'] >= 80, 'low' => $c['pct'] < 40,
        'sub' => get_string('nofm', 'local_siraj', (object)['n' => $c['finished'], 'of' => $c['enrolled']])],
        array_slice($report['courses'], 0, 8)),
    'deadlines' => array_slice($deadlines, 0, 6),
    'hasdeadlines' => (bool)$deadlines,
    'action' => $url->out(false),
    'sesskey' => sesskey(),
    'reportsurl' => (new moodle_url('/local/siraj/reports.php'))->out(false),
    'matrixurl' => (new moodle_url('/local/siraj/matrix.php'))->out(false),
    'icon_search' => icons::svg('search'),
    'icon_bell' => icons::svg('bell'),
    'icon_chart' => icons::svg('chart'),
    'icon_shield' => icons::svg('shield'),
    'icon_arrow' => icons::svg('arrow-right'),
    'icon_users' => icons::svg('users'),
]);
echo $OUTPUT->footer();
