<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj person page: one learner's progress, certificates, reminders and their manager's notes.
 * Open to their manager (My team) and to people who can see all reports.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

use local_siraj\certs;
use local_siraj\reports;
use local_siraj\setup;
use local_siraj\team;
use theme_siraj\icons;

$id = required_param('id', PARAM_INT);
require_login(null, false);
if (!team::can_view((int)$USER->id, $id)) {
    throw new required_capability_exception(context_system::instance(), 'local/siraj:viewreports', 'nopermissions', '');
}
$url = new moodle_url('/local/siraj/person.php', ['id' => $id]);
$p = team::person($id);
\local_siraj\nav::page($url, $p['fullname'], 'person');

if (data_submitted() && confirm_sesskey()) {
    $action = required_param('action', PARAM_ALPHA);
    if ($action === 'remind') {
        [$sent] = team::remind([$id], $USER);
        redirect($url, $sent ? get_string('reminded', 'local_siraj', 1) : get_string('remindskipped', 'local_siraj', 1), null,
            $sent ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_INFO);
    }
    if ($action === 'note') {
        $text = trim(required_param('note', PARAM_TEXT));
        if ($text !== '') {
            $DB->insert_record('local_siraj_note', (object)['userid' => $id, 'authorid' => $USER->id, 'kind' => 'note',
                'note' => core_text::substr($text, 0, 2000), 'timecreated' => time()]);
        }
        redirect($url);
    }
}

$now = time();
$user = $p['user'];
$role = setup::field_values(setup::FIELD_JOBROLE, [$id])[$id];
$chip = team::chip($p['status']);

$courses = array_values($p['courses']);
usort($courses, fn($a, $b) => [!$a['mandatory'], $a['finished'], $a['due'] ?: PHP_INT_MAX] <=> [!$b['mandatory'], $b['finished'], $b['due'] ?: PHP_INT_MAX]);
$rows = [];
$certificates = [];
foreach ($courses as $c) {
    $state = $c['finished'] ? 'done' : ($c['overdue'] ? 'overdue' : ($c['done'] ? 'ontrack' : 'notstarted'));
    $meta = [];
    if ($c['finished']) {
        $code = certs::issue($id, (int)$c['id'], (int)$c['completedon']);
        $meta[] = get_string('completedon', 'local_siraj', userdate($c['completedon'], '%d.%m.%Y'));
        $certificates[] = ['name' => $c['name'], 'date' => userdate($c['completedon'], '%d.%m.%Y'), 'code' => $code];
    } else {
        if ($c['due']) {
            $meta[] = $c['overdue'] ? \local_siraj\plural::str('reason_overdue', (int)ceil(($now - $c['due']) / DAYSECS))
                : get_string('duedate', 'local_siraj', userdate($c['due'], '%d.%m.%Y'));
        }
        if ($c['nextname']) {
            $meta[] = get_string('next', 'local_siraj', $c['nextname']);
        }
    }
    $rows[] = [
        'name' => $c['name'], 'mandatory' => $c['mandatory'], 'pct' => $c['progress'],
        'count' => get_string('nofm', 'local_siraj', (object)['n' => $c['done'], 'of' => $c['total']]),
        'meta' => implode(' · ', $meta), 'chip' => [
            'label' => get_string($state === 'done' ? 'cs_done' : ($state === 'overdue' ? 'cs_overdue' : ($state === 'ontrack' ? 'cs_inprogress' : 'cs_notstarted')), 'local_siraj'),
            'class' => ['done' => 'green', 'overdue' => 'red', 'ontrack' => 'blue', 'notstarted' => ''][$state]],
        'coverclass' => 'cv' . ($c['id'] % 6),
        'reporturl' => (new moodle_url('/report/outline/user.php', ['id' => $id, 'course' => $c['id'], 'mode' => 'outline']))->out(false),
    ];
}

// Learning rhythm: activities finished per week, as small bars.
$weeks = reports::weeks([$id], $now);
$peak = max(1, max(array_column($weeks, 'n')));
$bars = array_map(fn($w) => ['h' => max(4, (int)round(100 * $w['n'] / $peak)), 'n' => $w['n'], 'zero' => !$w['n'],
    'label' => get_string('weekof', 'local_siraj', (object)['date' => userdate($w['start'], '%d %b'), 'n' => $w['n']])], $weeks);

// Notes and reminders, newest first.
$timeline = [];
foreach ($DB->get_records('local_siraj_note', ['userid' => $id], 'timecreated DESC', '*', 0, 30) as $n) {
    $author = core_user::get_user($n->authorid);
    $timeline[] = [
        'reminder' => $n->kind === 'reminder',
        'text' => $n->kind === 'reminder' ? get_string('remindernote', 'local_siraj', s($n->note)) : s($n->note),
        'who' => $author ? fullname($author) : '',
        'when' => userdate($n->timecreated, '%d.%m.%Y %H:%M'),
    ];
}

$backurl = in_array($id, team::members((int)$USER->id), true) ? new moodle_url('/local/siraj/team.php') : new moodle_url('/local/siraj/reports.php');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/person', [
    'backurl' => $backurl->out(false),
    'backtext' => get_string($backurl->get_path() === '/local/siraj/team.php' ? 'backtoteam' : 'backtoreports', 'local_siraj'),
    'name' => $p['fullname'],
    'initials' => team::initials($user),
    'sub' => implode(' · ', array_filter([$role, $p['department'], $user->city])),
    'email' => $user->email,
    'chip' => $chip,
    'reason' => $p['reason'],
    'remindable' => !in_array($p['status'], ['done', 'none'], true),
    'kpis' => [
        ['value' => $p['pct'] . '%', 'label' => get_string($p['mandatoryonly'] ? 'kpi_mandatory' : 'kpi_allcourses', 'local_siraj'),
            'sub' => get_string('nofm', 'local_siraj', (object)['n' => $p['done'], 'of' => $p['total']]) . ' ' . get_string('activities', 'local_siraj'), 'tone' => 'mint', 'icon' => icons::svg('shield')],
        ['value' => count($certificates), 'label' => get_string('stat_certificates', 'local_siraj'),
            'sub' => get_string('ncoursesdone', 'local_siraj', (object)['n' => count($certificates), 'of' => count($courses)]), 'tone' => 'blue', 'icon' => icons::svg('award')],
        ['value' => team::ago($p['last']), 'label' => get_string('lastprogress', 'local_siraj'),
            'sub' => get_string('lastseenwhen', 'local_siraj', team::ago($p['lastaccess'])), 'tone' => '', 'icon' => icons::svg('clock')],
    ],
    'courses' => $rows,
    'hascourses' => (bool)$rows,
    'certificates' => $certificates,
    'hascertificates' => (bool)$certificates,
    'bars' => $bars,
    'barsfirst' => userdate($weeks[0]['start'], '%d %b'),
    'timeline' => $timeline,
    'hastimeline' => (bool)$timeline,
    'action' => $url->out(false),
    'sesskey' => sesskey(),
    'profileurl' => (new moodle_url('/user/view.php', ['id' => $id, 'course' => SITEID]))->out(false),
    'verifyurl' => (new moodle_url('/local/siraj/verify.php'))->out(false),
    'icon_back' => icons::svg('arrow-left'),
    'icon_bell' => icons::svg('bell'),
    'icon_external' => icons::svg('external'),
    'icon_award' => icons::svg('award'),
    'icon_message' => icons::svg('message'),
]);
echo $OUTPUT->footer();
