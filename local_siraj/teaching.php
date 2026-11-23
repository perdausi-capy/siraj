<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Teaching: the teacher's home (courses taught, learner progress, grading queue, due dates, quick tools).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

require_login(null, false);
if (!\local_siraj\teaching_page::courses_taught((int)$USER->id)) {
    redirect(new moodle_url('/local/siraj/index.php'));
}
\local_siraj\nav::page(new moodle_url('/local/siraj/teaching.php'), get_string('teaching', 'local_siraj'), 'teaching');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_siraj/teaching', (new \local_siraj\teaching_page($USER))->export());
echo $OUTPUT->footer();
