<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Included by Siraj pages before config.php: a plain view of the page (GET, no action, no sesskey, no language
 * change) does not need to write the session, so it opens it read-only. Moodle then does not hold the user's session
 * lock for it, and other requests from the same person (a click while a page, a SCORM or H5P player, or a prefetch is
 * still loading) no longer queue behind it. Needs $CFG->enable_read_only_sessions = true in config.php; without it
 * Moodle ignores the flag.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && !defined('READ_ONLY_SESSION')
        && !array_intersect(array_keys($_GET), ['sesskey', 'action', 'lang', 'download', 'open', 'read'])) {
    define('READ_ONLY_SESSION', true);
}
