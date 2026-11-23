<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Messages: the chat as a full page (conversations on the left, the open chat on the right).
 * Moodle's /message/index.php redirects here.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser() || empty($CFG->messaging)) {
    throw new moodle_exception('disabled', 'message');
}
$userid = optional_param('id', 0, PARAM_INT);
$convid = optional_param('convid', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/siraj/messages.php'));
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-siraj-messages');
$PAGE->add_body_class('siraj-page siraj-messages');
$PAGE->set_title(get_string('chat_title', 'theme_siraj'));
$PAGE->set_heading(get_string('chat_title', 'theme_siraj'));
$PAGE->set_secondary_navigation(false);

$context = \theme_siraj\output\shell::chat_context();
$context['page'] = true;
$context['openuser'] = $userid && $userid != $USER->id ? $userid : 0;
$context['openconv'] = $convid;
if ($context['openuser']) {
    $u = core_user::get_user($context['openuser'], '*', IGNORE_MISSING);
    $context['openusername'] = $u ? fullname($u) : '';
}
$context['prefsurl'] = (new moodle_url('/message/edit.php', ['id' => $USER->id]))->out(false);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_siraj/chat', $context);
echo $OUTPUT->footer();
