<?php
// Restructures the free courses imported from siraj5 (seed/import_siraj5.php) like the catalogue courses: a welcome
// page written with Siraj blocks, one lesson section per SCORM module, a quiz with explanations that opens after the
// lessons, and a wrap-up with a feedback survey. Keeps the SCORM lessons (and any real quiz) listed in the definition,
// removes everything else (siraj5 test leftovers: empty quizzes, glossaries used as headings, test pages and files,
// other clients' packages). Definitions: seed/siraj5/*.php. Safe to run again (kept items are found again).
// Run: php siraj_seed/siraj5_restructure.php [shortname ...]
define('SB_LIB_ONLY', true);
require(__DIR__ . '/catalogue.php');

/**
 * Restructure one imported course.
 *
 * @param array $c Definition: shortname (as imported), fullname, category key, minutes, summary, outcomes, sections.
 *     Items are catalogue items, or ['type' => 'keep', 'mod' => 'scorm', 'ref' => part of the package file name or
 *     'match' => activity name, 'name' => new name, 'intro' => ..., 'after' => [...]].
 */
function sb_restructure(array $c): void {
    global $DB;
    $course = $DB->get_record('course', ['shortname' => $c['shortname']]);
    if (!$course) {
        echo "{$c['shortname']}: not here, skipped\n";
        return;
    }
    $catid = $DB->get_field('course_categories', 'id', ['idnumber' => 'siraj-' . $c['category']], MUST_EXIST);
    $summary = sb_p($c['summary']) . '<p><strong>By the end you will be able to:</strong></p>' . sb_ul($c['outcomes']);
    update_course((object)['id' => $course->id, 'fullname' => $c['fullname'], 'summary' => $summary,
        'summaryformat' => FORMAT_HTML, 'category' => $catid, 'enablecompletion' => 1, 'showcompletionconditions' => 1]);
    \local_siraj\coursefields::ensure();
    \core_course\customfield\course_handler::create()->instance_form_save((object)['id' => $course->id,
        'customfield_siraj_mandatory' => (int)!empty($c['mandatory']), 'customfield_siraj_duration' => (string)$c['minutes']]);
    $course = get_course($course->id);

    // 1. Find what we keep.
    $modinfo = get_fast_modinfo($course);
    $keep = [];
    foreach ($c['sections'] as $section) {
        foreach ($section['items'] as $a) {
            if ($a['type'] !== 'keep') {
                continue;
            }
            foreach ($modinfo->get_instances_of($a['mod']) as $cm) {
                if (in_array($cm->id, $keep, true)) {
                    continue;
                }
                $ref = $a['mod'] === 'scorm' ? (string)$DB->get_field('scorm', 'reference', ['id' => $cm->instance]) : '';
                if ((isset($a['ref']) && strpos($ref, $a['ref']) !== false) || (isset($a['match']) && $cm->name === $a['match'])
                        || (isset($a['name']) && $cm->name === $a['name'])) {
                    $keep[$a['name']] = $cm->id;
                    break;
                }
            }
            if (!isset($keep[$a['name']])) {
                throw new coding_exception("{$c['shortname']}: nothing to keep for {$a['name']}");
            }
        }
    }
    // 2. Kept activities to the top section first (one may sit inside a subsection, which goes in the next step and
    //    would take its activities with it), then remove the rest (question banks stay: Moodle manages those).
    $sec0 = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 0]);
    foreach ($keep as $cmid) {
        moveto_module(get_coursemodule_from_id('', $cmid), $sec0);
    }
    rebuild_course_cache($course->id, true);
    foreach (get_fast_modinfo($course)->get_cms() as $cm) {
        if (!in_array($cm->id, $keep, true) && $cm->modname !== 'qbank') {
            course_delete_module($cm->id);
        }
    }
    // 3. Old sections out, the new sections in.
    rebuild_course_cache($course->id, true);
    foreach (array_reverse(get_fast_modinfo($course)->get_section_info_all()) as $s) {
        if ($s->section > 0 && !$s->is_delegated()) {
            course_delete_section($course, $s, true);
        }
    }
    course_update_section($course, $sec0, ['name' => null, 'summary' => '']);
    $cms = [];
    $restricted = [];
    foreach ($c['sections'] as $i => $section) {
        $num = $i + 1;
        $sec = course_create_section($course->id, $num);
        course_update_section($course, $sec, ['name' => $section['name'], 'summary' => sb_p($section['summary'] ?? ''),
            'summaryformat' => FORMAT_HTML]);
        foreach ($section['items'] as $a) {
            if ($a['type'] === 'keep') {
                $cmid = $keep[$a['name']];
                moveto_module(get_coursemodule_from_id('', $cmid), $DB->get_record('course_sections', ['id' => $sec->id]));
                $cm = get_coursemodule_from_id('', $cmid);
                set_coursemodule_name($cmid, $a['name']);
                $DB->update_record($cm->modname, (object)['id' => $cm->instance, 'intro' => sb_p($a['intro'] ?? ''), 'introformat' => FORMAT_HTML]);
                $DB->set_field('course_modules', 'visible', 1, ['id' => $cmid]);
                $DB->set_field('course_modules', 'completion', COMPLETION_TRACKING_AUTOMATIC, ['id' => $cmid]);
                if ($cm->modname === 'scorm') {
                    $DB->set_field('scorm', 'completionstatusrequired', 6, ['id' => $cm->instance]);
                    $DB->set_field('scorm', 'timeopen', 0, ['id' => $cm->instance]);
                    $DB->set_field('scorm', 'timeclose', 0, ['id' => $cm->instance]);
                } else if ($cm->modname === 'quiz') {
                    // A kept quiz: passing it completes it (pass mark 7 out of 10).
                    $quiz = $DB->get_record('quiz', ['id' => $cm->instance]);
                    $DB->set_field('quiz', 'attempts', 0, ['id' => $quiz->id]);
                    $DB->set_field('grade_items', 'gradepass', round($quiz->grade * 0.7, 2),
                        ['itemtype' => 'mod', 'itemmodule' => 'quiz', 'iteminstance' => $quiz->id]);
                    $DB->set_field('course_modules', 'completionpassgrade', 1, ['id' => $cmid]);
                    $DB->set_field('course_modules', 'completiongradeitemnumber', 0, ['id' => $cmid]);
                }
                $made = (object)['cmid' => $cmid];
            } else {
                $made = sb_add_item($course, $num, $a);
            }
            if ($made) {
                $cms[$a['name']] = (int)$made->cmid;
                if (!empty($a['after'])) {
                    $restricted[$made->cmid] = $a['after'];
                }
            }
        }
    }
    foreach ($restricted as $cmid => $after) {
        $conds = array_map(fn($name) => ['type' => 'completion', 'cm' => $cms[$name], 'e' => COMPLETION_COMPLETE], $after);
        $DB->set_field('course_modules', 'availability', json_encode(['op' => '&', 'c' => $conds,
            'showc' => array_fill(0, count($conds), true)]), ['id' => $cmid]);
    }
    rebuild_course_cache($course->id, true);
    echo "restructured: {$c['fullname']} (id $course->id), " . count($cms) . " activities\n";
}

$only = array_slice($argv, 1);
foreach (glob(__DIR__ . '/siraj5/*.php') as $file) {
    $def = require($file);
    if ($only && !in_array($def['shortname'], $only, true)) {
        continue;
    }
    sb_restructure($def);
}
\cache::make('core', 'questiondata')->purge();
echo "siraj5 courses done\n";
