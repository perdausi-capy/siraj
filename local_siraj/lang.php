<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj language switcher (top bar globe): sets the language for this visit and, when signed in, saves it as the
 * person's preferred language, then goes back to the page they were on.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

$lang = required_param('code', PARAM_SAFEDIR);
$return = optional_param('return', '/', PARAM_LOCALURL);
require_sesskey();

if (get_string_manager()->translation_exists($lang, false)) {
    $SESSION->lang = $lang;
    if (isloggedin() && !isguestuser() && $USER->lang !== $lang) {
        $DB->set_field('user', 'lang', $lang, ['id' => $USER->id]);
        $USER->lang = $lang;
        \core\event\user_updated::create_from_userid($USER->id)->trigger();
    }
}
redirect(new moodle_url($return));
