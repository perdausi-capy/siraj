<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Learning mode.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

$cmid = required_param('cmid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$chapterid = optional_param('chapterid', 0, PARAM_INT);

[$course, $cmrecord] = get_course_and_cm_from_cmid($cmid);
require_login($course, false, $cmrecord);
$progress = new \local_siraj\progress($course, $USER->id);
$cm = $progress->modinfo->get_cm($cmid);
if (!$cm->uservisible) {
    throw new moodle_exception('activityiscurrentlyhidden');
}
$context = context_module::instance($cm->id);

// Manual "Mark as done" / "Mark as not done".
if ($action === 'done' || $action === 'undone') {
    require_sesskey();
    if ($progress->completion->is_enabled($cm) == COMPLETION_TRACKING_MANUAL) {
        $progress->completion->update_state($cm, $action === 'done' ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE);
    }
    $goto = optional_param('goto', '', PARAM_LOCALURL);
    redirect($goto ?: new moodle_url('/local/siraj/learn.php', ['cmid' => $cm->id]));
}

// Answers from Siraj's own activity views (feedback, forum, choice).
if ($action && in_array($action, ['fbsubmit', 'reply', 'topic', 'vote'], true)) {
    require_sesskey();
    $instance = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
    $views = new \local_siraj\activity_views($cm, $course, $instance, '');
    if ($goto = $views->act($action)) {
        redirect($goto);
    }
}

$PAGE->set_url(new moodle_url('/local/siraj/learn.php', array_filter(['cmid' => $cm->id, 'chapterid' => $chapterid,
    'd' => optional_param('d', 0, PARAM_INT)])));
$PAGE->set_cm($cm, $course);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-learn');
$PAGE->add_body_class('siraj-page siraj-learn');
$PAGE->set_title($cm->get_formatted_name());
$PAGE->set_heading($cm->get_formatted_name());
$PAGE->set_secondary_navigation(false);
$PAGE->activityheader->disable();

$player = new \local_siraj\player($progress, $cm, $chapterid);
$player->record_view();
// Viewing may have completed the activity: rebuild progress so the page shows the new state.
$player = new \local_siraj\player(new \local_siraj\progress($course, $USER->id), $cm, $chapterid);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/learn', $player->export());
echo $OUTPUT->footer();
