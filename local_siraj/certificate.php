<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj certificate of completion: a printable page (Print / Save as PDF in the browser).
 * Shown only to the learner, and only once every tracked activity in the course is complete.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course, false);
require_once($CFG->dirroot . '/theme/siraj/lib.php');
$context = context_course::instance($course->id);

$progress = new \local_siraj\progress($course, $USER->id);
[$total, $done] = $progress->totals();
if (!$total || $done < $total) {
    throw new moodle_exception('notcompleted', 'local_siraj', new moodle_url('/local/siraj/course.php', ['id' => $course->id]));
}
$completedon = 0;
foreach ($progress->steps() as $cm) {
    if ($progress->completion->is_enabled($cm)) {
        $completedon = max($completedon, (int)$progress->completion->get_data($cm, false, $USER->id)->timemodified);
    }
}
$fields = \local_siraj\coursefields::values([$course->id])[$course->id];
// A stable reference for this learner + course + completion, printed on the certificate and checkable on verify.php.
$code = \local_siraj\certs::issue((int)$USER->id, (int)$course->id, $completedon);

$PAGE->set_url(new moodle_url('/local/siraj/certificate.php', ['id' => $course->id]));
$PAGE->set_context($context);
$PAGE->set_pagelayout('embedded');
$PAGE->add_body_class('siraj-certificate-page');
$PAGE->set_title(get_string('certificateofcompletion', 'local_siraj') . ': ' . format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/certificate', [
    'learner' => fullname($USER),
    'course' => format_string($course->fullname, true, ['context' => $context, 'escape' => false]),
    'date' => userdate($completedon, '%d.%m.%Y'),
    'duration' => $fields['duration'] ? \local_siraj\plural::str('minutes', (int)$fields['duration']) : null,
    'site' => format_string($SITE->fullname),
    'code' => $code,
    'verifyurl' => (new moodle_url('/local/siraj/verify.php'))->out(false),
    'logo' => $OUTPUT->get_logo_url(null, 300) ? $OUTPUT->get_logo_url(null, 300)->out(false) : null,
    'backurl' => (new moodle_url('/local/siraj/achievements.php'))->out(false),
    'flame' => \theme_siraj\icons::flame(),
    'panellogo' => function_exists('theme_siraj_panel_logo_url') ? theme_siraj_panel_logo_url() : null,
    'icon_back' => \theme_siraj\icons::svg('arrow-left'),
]);
echo $OUTPUT->footer();
