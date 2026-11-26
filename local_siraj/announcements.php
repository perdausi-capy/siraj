<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Announcements: the site announcements as cards (/announcements) and one announcement to read (/announcements/4).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect(new moodle_url('/'));
}
$id = optional_param('id', 0, PARAM_INT);
$url = new moodle_url('/local/siraj/announcements.php', $id ? ['id' => $id] : null);

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-announcements');
$PAGE->add_body_class('siraj-page siraj-announcepage');
$PAGE->set_title(get_string('announcements', 'local_siraj'));
$PAGE->set_heading(get_string('announcements', 'local_siraj'));
$PAGE->set_secondary_navigation(false);

$page = new \local_siraj\announcements_page();
$data = $page->export($id);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/announcements', $data);
echo $OUTPUT->footer();
