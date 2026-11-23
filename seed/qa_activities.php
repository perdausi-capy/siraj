<?php
// QA course (hidden, admins only) with one of each activity type Siraj draws itself or stages, with sample content,
// to check Learning mode. Dummy content only. Safe to run again (skips if the course exists).
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
\core\session\manager::set_user(get_admin());
if ($DB->record_exists('course', ['shortname' => 'SIRAJ-QA'])) {
    die("SIRAJ-QA exists\n");
}
$gen = phpunit_util::get_data_generator();
$course = $gen->create_course(['fullname' => 'Siraj QA: every activity type', 'shortname' => 'SIRAJ-QA', 'visible' => 0,
    'enablecompletion' => 1, 'summary' => '<p>Internal check of Learning mode for each activity type.</p>']);
$admin = get_admin();
$gen->enrol_user($admin->id, $course->id, 'student');
$c = ['course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL];

$forum = $gen->create_module('forum', $c + ['name' => 'Team discussion', 'intro' => '<p>Share one thing you will try this week.</p>']);
$fg = $gen->get_plugin_generator('mod_forum');
foreach ([['Breathing breaks that actually work', 'I set a timer for three slow breaths every hour. Small, but it helps.'],
        ['Walking meetings', 'Has anyone tried walking one-to-ones? How do you take notes?']] as [$s, $m]) {
    $d = $fg->create_discussion(['course' => $course->id, 'forum' => $forum->id, 'userid' => $admin->id, 'name' => $s,
        'message' => "<p>$m</p>"]);
    $fg->create_post(['discussion' => $d->id, 'parent' => $d->firstpost, 'userid' => $admin->id,
        'message' => '<p>Great idea, I will try it tomorrow.</p>']);
}
$glossary = $gen->create_module('glossary', $c + ['name' => 'Wellbeing terms', 'intro' => '<p>Key words used in this course.</p>']);
$gg = $gen->get_plugin_generator('mod_glossary');
foreach (['Resilience' => 'The ability to recover from setbacks and adapt to change.',
        'Mindfulness' => 'Paying attention to the present moment, on purpose and without judgement.',
        'Burnout' => 'Exhaustion caused by long-term stress at work.',
        'Gratitude' => 'Noticing and appreciating the good things in your life.'] as $concept => $def) {
    $gg->create_content($glossary, ['concept' => $concept, 'definition' => "<p>$def</p>", 'approved' => 1]);
}
$gen->create_module('choice', $c + ['name' => 'Best time for team learning', 'intro' => '<p>When should we run the next live session?</p>',
    'option' => ['Tuesday morning', 'Wednesday lunch', 'Thursday afternoon'], 'showresults' => 1, 'allowupdate' => 1]);
$folder = $gen->create_module('folder', $c + ['name' => 'Course handouts', 'intro' => '<p>Printable handouts for this course.</p>']);
$fs = get_file_storage();
$fctx = context_module::instance($folder->cmid);
foreach (['/' => ['Wellbeing checklist.txt', 'Weekly plan.txt'], '/Extra/' => ['Reading list.txt']] as $path => $names) {
    foreach ($names as $n) {
        $fs->create_file_from_string(['contextid' => $fctx->id, 'component' => 'mod_folder', 'filearea' => 'content',
            'itemid' => 0, 'filepath' => $path, 'filename' => $n], "Demo file: $n\n");
    }
}
$feedback = $gen->create_module('feedback', $c + ['name' => 'Quick feedback', 'anonymous' => 1]);
$fbg = $gen->get_plugin_generator('mod_feedback');
$fbg->create_item_multichoice($feedback, ['name' => 'How useful was this course?', 'values' => "1\n2\n3\n4\n5", 'required' => 1]);
$fbg->create_item_multichoice($feedback, ['name' => 'Which parts did you use? (pick any)', 'subtype' => 'c',
    'values' => "Videos\nReading\nQuizzes\nDiscussion"]);
$fbg->create_item_textarea($feedback, ['name' => 'What should we change?']);
$lesson = $gen->create_module('lesson', $c + ['name' => 'Guided lesson']);
$lg = $gen->get_plugin_generator('mod_lesson');
$lg->create_content($lesson, ['title' => 'Welcome', 'contents_editor' => ['text' => '<p>This is a short guided lesson.</p>',
    'format' => FORMAT_HTML]]);
$lg->create_question_truefalse($lesson, ['title' => 'Check', 'contents_editor' => ['text' => '<p>Sleep affects focus.</p>',
    'format' => FORMAT_HTML]]);
$wiki = $gen->create_module('wiki', $c + ['name' => 'Team wiki', 'firstpagetitle' => 'Our tips']);
$gen->get_plugin_generator('mod_wiki')->create_first_page($wiki, ['content' => '<p>Add your best wellbeing tip here.</p>']);
$gen->create_module('workshop', $c + ['name' => 'Peer review']);
$data = $gen->create_module('data', $c + ['name' => 'Ideas board']);
$gen->get_plugin_generator('mod_data')->create_field((object)['name' => 'Idea', 'type' => 'text'], $data);
$gen->create_module('imscp', $c + ['name' => 'Content package']);
rebuild_course_cache($course->id, true);
echo "SIRAJ-QA id $course->id\n";
foreach (get_fast_modinfo($course)->get_cms() as $cm) {
    echo "$cm->id $cm->modname\n";
}
