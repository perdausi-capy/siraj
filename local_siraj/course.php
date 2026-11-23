<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj course overview. People who can't access the course yet see an outline with an enrol action.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$course = get_course($id);
$context = context_course::instance($course->id);
$url = new moodle_url('/local/siraj/course.php', ['id' => $course->id]);

require_login(null, false);
$preview = null;
if (!can_access_course($course)) {
    if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $context)) {
        require_login($course); // Moodle's own message for hidden courses.
    }
    // Self-enrolment without a key can happen right here; anything else uses Moodle's enrolment page.
    $plugin = enrol_get_plugin('self');
    $instance = null;
    foreach (enrol_get_instances($course->id, true) as $i) {
        if ($i->enrol === 'self' && $plugin && empty($i->password) && $plugin->can_self_enrol($i) === true) {
            $instance = $i;
            break;
        }
    }
    if ($action === 'enrol' && $instance) {
        require_sesskey();
        $plugin->enrol_self($instance);
        redirect($url);
    }
    $preview = [
        'canenrol' => (bool)$instance,
        'formurl' => $url->out(false),
        'enrolpageurl' => !$instance && enrol_get_instances($course->id, true) ?
            (new moodle_url('/enrol/index.php', ['id' => $course->id, 'siraj' => 'classic']))->out(false) : null,
    ];
} else {
    require_login($course);
}

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-course');
$PAGE->add_body_class('siraj-page siraj-coursepage');
$PAGE->set_title(format_string($course->fullname, true, ['context' => $context]));
$PAGE->set_heading(format_string($course->fullname, true, ['context' => $context]));
$PAGE->set_secondary_navigation(false);

if (!$preview) {
    // Same event Moodle records when a course is opened, so reports keep working.
    \core\event\course_viewed::create(['context' => $context])->trigger();
}

$page = new \local_siraj\course_page(new \local_siraj\progress($course, $USER->id), $preview);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/course', $page->export());
echo $OUTPUT->footer();
