<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj certificate check: anyone (no sign-in) can type the code printed on a certificate and see whether it is genuine.
 * Shows only the learner's name, the course and the date.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Public page: no sign-in needed.
require(__DIR__ . '/../../config.php');

$code = trim(optional_param('code', '', PARAM_ALPHANUMEXT));
$PAGE->set_url(new moodle_url('/local/siraj/verify.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('login');
$PAGE->add_body_class('siraj-verifypage');
$PAGE->set_title(get_string('verifytitle', 'local_siraj'));

$result = null;
if ($code !== '') {
    $cert = \local_siraj\certs::find($code);
    $result = $cert ? [
        'valid' => true, 'learner' => $cert->learner, 'course' => $cert->course,
        'date' => userdate($cert->timecompleted, '%d.%m.%Y'), 'code' => $cert->code,
    ] : ['valid' => false];
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/verify', [
    'code' => s($code),
    'result' => $result,
    'site' => format_string($SITE->fullname),
    'action' => $PAGE->url->out(false),
    'flame' => \theme_siraj\icons::flame(),
    'icon_check' => \theme_siraj\icons::svg('check-circle'),
    'icon_x' => \theme_siraj\icons::svg('x'),
]);
echo $OUTPUT->footer();
