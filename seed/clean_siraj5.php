<?php
// Removes siraj5 leftovers from our copies of the free courses: test items, AI-generated test quizzes, quizzes
// with no questions (and the banner pointing to them), and the Communication Skills test area. Safe to run again.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/course/lib.php');
\core\session\manager::set_user(get_admin());
// No recycle-bin copy of junk (it backs up each item first, slow for big SCORM files).
$bin = get_config('tool_recyclebin', 'coursebinenable');
set_config('coursebinenable', 0, 'tool_recyclebin');

$remove = function(stdClass $course, callable $match) {
    $n = 0;
    foreach (get_fast_modinfo($course)->get_cms() as $cm) {
        if ($cm->modname !== 'subsection' && $match($cm)) {
            echo "  - $cm->modname: $cm->name\n";
            course_delete_module($cm->id);
            $n++;
        }
    }
    return $n;
};
$emptyquiz = function($cm) use ($DB) {
    return $cm->modname === 'quiz' && !$DB->record_exists('quiz_slots', ['quizid' => $cm->instance]);
};
foreach (['Wellness Course', 'Global Wellness', 'Wellness : Be Well and Thrive Program', 'Presentation Skills',
        'Emotional Intelligence', 'Communication Skills'] as $name) {
    if (!$course = $DB->get_record('course', ['fullname' => $name])) {
        continue;
    }
    echo "$name\n";
    $hasquiz = (bool)array_filter(get_fast_modinfo($course)->get_cms(), fn($cm) => $cm->modname === 'quiz' && !$emptyquiz($cm));
    $remove($course, function($cm) use ($emptyquiz, $hasquiz, $name) {
        return $cm->name === 'adcb'
            || ($cm->modname === 'quiz' && in_array($cm->name, ['Generated Quiz', 'Quiz test 1', 'ComQuiz']))
            || $emptyquiz($cm)
            || ($cm->modname === 'glossary' && $cm->name === 'Quiz' && !$hasquiz)
            // Communication Skills: only the lesson itself is real; the rest is siraj5's test area.
            || ($name === 'Communication Skills' && $cm->modname !== 'scorm')
            || ($name === 'Communication Skills' && $cm->name === 'DPworld Test Course');
    });
    if ($name === 'Communication Skills') {
        // Move the lesson to the first section, then drop the emptied sections and subsections (Zoom placeholders).
        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);
        $first = $modinfo->get_section_info(0);
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->modname === 'scorm' && $cm->sectionnum != 0) {
                moveto_module($cm, $first);
            }
        }
        rebuild_course_cache($course->id, true);
        foreach (get_fast_modinfo($course)->get_cms() as $cm) {
            if ($cm->modname === 'subsection') {
                echo "  - subsection: $cm->name\n";
                course_delete_module($cm->id);
            }
        }
        rebuild_course_cache($course->id, true);
        foreach (array_reverse(get_fast_modinfo($course)->get_section_info_all()) as $s) {
            if ($s->section > 0 && empty(get_fast_modinfo($course)->sections[$s->section]) && !$s->component) {
                course_delete_section($course, $s, true);
            }
        }
        $DB->set_field('course_sections', 'name', '', ['course' => $course->id, 'section' => 0]);
    }
    rebuild_course_cache($course->id, true);
}
set_config('coursebinenable', $bin, 'tool_recyclebin');
