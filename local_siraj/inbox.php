<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Inbox (Moodle notifications).
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
$url = new moodle_url('/local/siraj/inbox.php');

// Actions use Moodle's own message API, and only on the current user's notifications.
$open = optional_param('open', 0, PARAM_INT);
$read = optional_param('read', 0, PARAM_INT);
if ($open || $read) {
    require_sesskey();
    $n = $DB->get_record('notifications', ['id' => $open ?: $read, 'useridto' => $USER->id], '*', MUST_EXIST);
    if (empty($n->timeread)) {
        \core_message\api::mark_notification_as_read($n);
    }
    redirect($open && $n->contexturl ? new moodle_url($n->contexturl) : $url);
}
if (optional_param('readall', 0, PARAM_BOOL)) {
    require_sesskey();
    \core_message\api::mark_all_notifications_as_read($USER->id);
    redirect($url);
}

$PAGE->set_url($url);
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-inbox');
$PAGE->add_body_class('siraj-page siraj-inboxpage');
$PAGE->set_title(get_string('inbox', 'local_siraj'));
$PAGE->set_heading(get_string('inbox', 'local_siraj'));
$PAGE->set_secondary_navigation(false);

$page = new \local_siraj\inbox_page($USER);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/inbox', $page->export());
echo $OUTPUT->footer();
