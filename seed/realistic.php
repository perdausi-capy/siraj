<?php
// Local lab: make the demo activities realistic. Assignments get real instructions, accept a PDF/Word upload or typed
// text, and complete on submission; quizzes get a proper description; the Golf sample SCORM is replaced by a short
// Siraj lesson (seed/scorm-kickoff.zip) that resumes without a prompt. Local only; content is made up.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/lib.php');
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();

$intro = '<p>Think about one situation from your own work where this course applies.</p>';
$activity = '<p><b>Write 150–300 words</b> covering:</p><ol><li>The situation and why it matters in your role.</li>'
    . '<li>What you would do differently after this course.</li><li>One thing you will start doing this week.</li></ol>'
    . '<p>Type your answer below, or upload it as a PDF or Word document. Your manager will review it and may add feedback.</p>';
foreach ($DB->get_records('assign', ['name' => 'Reflection: apply it to your role']) as $a) {
    $DB->update_record('assign', (object)['id' => $a->id, 'intro' => $intro, 'introformat' => FORMAT_HTML,
        'activity' => $activity, 'activityformat' => FORMAT_HTML, 'submissiondrafts' => 0,
        'completionsubmit' => 1]);
    $set = function($plugin, $name, $value) use ($DB, $a) {
        $where = ['assignment' => $a->id, 'plugin' => $plugin, 'subtype' => 'assignsubmission', 'name' => $name];
        if ($id = $DB->get_field('assign_plugin_config', 'id', $where)) {
            $DB->set_field('assign_plugin_config', 'value', $value, ['id' => $id]);
        } else {
            $DB->insert_record('assign_plugin_config', (object)($where + ['value' => $value]));
        }
    };
    $set('onlinetext', 'enabled', 1);
    $set('onlinetext', 'wordlimit', 300);
    $set('onlinetext', 'wordlimitenabled', 1);
    $set('file', 'enabled', 1);
    $set('file', 'maxfilesubmissions', 1);
    $set('file', 'maxsubmissionsizebytes', 10 * 1024 * 1024);
    $set('file', 'filetypeslist', '.pdf,.docx');
    $cm = get_coursemodule_from_instance('assign', $a->id, 0, false, MUST_EXIST);
    $DB->update_record('course_modules', (object)['id' => $cm->id, 'completion' => COMPLETION_TRACKING_AUTOMATIC]);
    rebuild_course_cache($a->course, true);
}
echo "assignments updated\n";

foreach ($DB->get_records('quiz', ['name' => 'Knowledge check']) as $q) {
    $DB->set_field('quiz', 'intro', '<p>Three short questions to check the key points. You need 2 of 3 to pass, '
        . 'and you can try again.</p>', ['id' => $q->id]);
}
echo "quizzes updated\n";

$course = $DB->get_record('course', ['shortname' => 'PM201'], '*', MUST_EXIST);
if ($golf = $DB->get_record('scorm', ['course' => $course->id, 'name' => 'Golf explained (SCORM)'])) {
    $cm = get_coursemodule_from_instance('scorm', $golf->id, $course->id, false, MUST_EXIST);
    course_delete_module($cm->id);
    echo "removed golf sample\n";
}
if (!$DB->record_exists('scorm', ['course' => $course->id, 'name' => 'Project kick-off essentials'])) {
    $gen->create_module('scorm', ['course' => $course->id, 'section' => 2, 'name' => 'Project kick-off essentials',
        'intro' => '<p>A 3-minute interactive lesson. Your place is saved if you leave part-way.</p>',
        'packagefilepath' => __DIR__ . '/scorm-kickoff.zip', 'completion' => COMPLETION_TRACKING_AUTOMATIC,
        'completionstatusrequired' => 4, 'popup' => 0, 'skipview' => 0, 'hidetoc' => 3, 'nav' => 0]);
    echo "added Siraj SCORM lesson\n";
}
$DB->set_field('h5pactivity', 'intro', '<p>One quick question. Pick an answer and press Check.</p>', ['course' => $course->id]);
$DB->set_field('book', 'intro', '<p>A short handbook in three chapters.</p>', ['course' => $course->id]);
rebuild_course_cache($course->id, true);
purge_all_caches();
echo "realistic done\n";
// Calendar entries keep the description they were created with: refresh them from the activity descriptions.
foreach (['assign', 'quiz'] as $mod) {
    foreach ($DB->get_records($mod) as $a) {
        $DB->set_field('event', 'description', $a->intro, ['modulename' => $mod, 'instance' => $a->id]);
    }
}
echo "calendar descriptions refreshed\n";
