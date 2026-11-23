<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Learning mode: current completion of one activity and its course, as JSON, so the player can update its ring and
 * "Done" chip while a SCORM or other framed activity runs (Moodle records completion when the package reports it).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

$cmid = required_param('cmid', PARAM_INT);
[$course, $cmrecord] = get_course_and_cm_from_cmid($cmid);
require_login($course, false, $cmrecord, false, true);
$progress = new \local_siraj\progress($course, $USER->id);
$cm = $progress->modinfo->get_cm($cmid);
[$total, $done] = $progress->totals();
echo json_encode([
    'isdone' => $progress->is_done($cm),
    'done' => $done, 'total' => $total,
    'pct' => $total ? (int)round($done / $total * 100) : 0,
    'text' => get_string('doneoftotal', 'local_siraj', (object)['done' => $done, 'total' => $total]),
]);
