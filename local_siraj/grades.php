<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Grades (Moodle's grades overview and user report redirect here for learners).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

require_login(null, false);
$courseid = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/siraj/grades.php', array_filter(['id' => $courseid])));
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-grades');
$PAGE->add_body_class('siraj-page siraj-grades');
$PAGE->set_title(get_string('grades_title', 'local_siraj'));
$PAGE->set_heading(get_string('grades_title', 'local_siraj'));
$PAGE->set_secondary_navigation(false);

$data = (new \local_siraj\grades_page($USER))->export($courseid);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/grades', $data);
echo $OUTPUT->footer();
