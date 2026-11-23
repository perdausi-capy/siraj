<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Admin studio: the whole site at a glance (learners, activity, trends, course health) and shortcuts to the
 * admin tasks people use most.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

use local_siraj\studio;
use theme_siraj\icons;

require_login(null, false);
require_capability('local/siraj:viewreports', context_system::instance());
$url = new moodle_url('/local/siraj/studio.php');
\local_siraj\nav::page($url, get_string('studio', 'local_siraj'), 'studio');
$PAGE->requires->js_call_amd('local_siraj/charts', 'init');

$studio = new studio();
$h = $studio->headline();
$weeks = $studio->weeks();
$courses = $studio->courses();
$attention = array_values(array_filter($courses, fn($c) => $c['hasflags']));
$site = has_capability('moodle/site:config', context_system::instance());

$shortcuts = [];
foreach ([
    ['moodle/user:create', '/admin/user.php', 'users', 'sc_users'],
    ['moodle/site:uploadusers', '/admin/tool/uploaduser/index.php', 'upload', 'sc_upload'],
    ['moodle/course:create', '/course/management.php', 'book', 'sc_courses'],
    ['moodle/cohort:manage', '/cohort/index.php', 'layers', 'sc_cohorts'],
    ['moodle/reportbuilder:view', '/reportbuilder/index.php', 'file', 'sc_customreports'],
    ['moodle/site:config', '/admin/settings.php?section=themesettingsiraj', 'settings', 'sc_brand'],
] as [$cap, $path, $icon, $str]) {
    if (has_capability($cap, context_system::instance())) {
        $shortcuts[] = ['url' => (new moodle_url($path))->out(false), 'icon' => icons::svg($icon),
            'text' => get_string($str, 'local_siraj'), 'sub' => get_string($str . '_sub', 'local_siraj')];
    }
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/studio', [
    'subline' => get_string('studio_sub', 'local_siraj', format_string($SITE->fullname)),
    'kpis' => [
        ['value' => $h['learners'], 'label' => get_string('kpi_learners', 'local_siraj'),
            'sub' => get_string('kpi_activepct', 'local_siraj', $h['activepct']), 'icon' => icons::svg('users'), 'tone' => 'blue'],
        ['value' => $h['active7'], 'label' => get_string('kpi_active7label', 'local_siraj'),
            'sub' => get_string('kpi_active30', 'local_siraj', $h['active30']), 'icon' => icons::svg('trending'), 'tone' => 'mint'],
        ['value' => $h['activities30'], 'label' => get_string('kpi_activities30', 'local_siraj'),
            'sub' => get_string('last30', 'local_siraj'), 'icon' => icons::svg('check-circle'), 'tone' => 'mint'],
        ['value' => $h['finished30'], 'label' => get_string('kpi_completions', 'local_siraj'),
            'sub' => get_string('kpi_finishedall', 'local_siraj', $h['finished']), 'icon' => icons::svg('award'), 'tone' => 'blue'],
        ['value' => count($attention), 'label' => get_string('kpi_attention', 'local_siraj'),
            'sub' => get_string('kpi_attention_sub', 'local_siraj', count($courses)), 'icon' => icons::svg('bell'), 'tone' => count($attention) ? 'amber' : ''],
    ],
    'trendchart' => studio::weeks_chart($weeks),
    'trendtable' => array_map(fn($w) => ['week' => userdate($w['start'], '%d %b'), 'n' => $w['n'], 'learners' => $w['learners'],
        'enrolments' => $w['enrolments']], $weeks),
    'attention' => array_map(fn($c) => $c + ['summary' => get_string('enrolledfinished', 'local_siraj',
        (object)['n' => $c['enrolled'], 'f' => $c['finished']])], array_slice($attention, 0, 6)),
    'hasattention' => (bool)$attention,
    'courses' => $courses,
    'shortcuts' => $shortcuts,
    'hasshortcuts' => (bool)$shortcuts,
    'reportsurl' => (new moodle_url('/local/siraj/reports.php', ['scope' => 'all']))->out(false),
    'matrixurl' => (new moodle_url('/local/siraj/matrix.php', ['scope' => 'all']))->out(false),
    'adminurl' => $site ? (new moodle_url('/admin/search.php'))->out(false) : null,
    'icon_chart' => icons::svg('chart'),
    'icon_shield' => icons::svg('shield'),
    'icon_settings' => icons::svg('settings'),
    'icon_arrow' => icons::svg('arrow-right'),
    'icon_check' => icons::svg('check-circle'),
]);
echo $OUTPUT->footer();
