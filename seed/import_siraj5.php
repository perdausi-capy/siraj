<?php
// Restores the free siraj5 courses (Wellness + Business Essentials) from import/siraj5/*.mbz, backed up without
// user data. Skips a course whose shortname already exists, so it is safe to run again.
// Run: php siraj_seed/import_siraj5.php <folder with the .mbz files>
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');

$dir = $argv[1] ?? die("folder arg required\n");
$admin = get_admin();
\core\session\manager::set_user($admin);
$plan = ['Wellness' => [17 => 'Wellness Course', 5 => 'Wellness : Be Well and Thrive Program', 4 => 'Global Wellness'],
    'Business Essentials' => [12 => 'Presentation Skills', 11 => 'Communication Skills', 10 => 'Emotional Intelligence',
        9 => 'Information Security', 8 => 'Cyber Security Measures', 7 => 'Introduction to Cyber Security']];
$blurbs = ['Wellness' => 'Look after your body and mind: sleep, stress, movement and balance at work.',
    'Business Essentials' => 'Everyday skills for work: communication, presentations, emotional intelligence and staying safe online.'];
foreach ($plan as $catname => $courses) {
    $cat = $DB->get_record('course_categories', ['name' => $catname]) ?: core_course_category::create(
        ['name' => $catname, 'description' => "<p>{$blurbs[$catname]}</p>", 'descriptionformat' => FORMAT_HTML]);
    foreach ($courses as $oldid => $name) {
        if ($DB->record_exists('course', ['fullname' => $name])) {
            echo "$name: already here\n";
            continue;
        }
        $tmp = 'siraj5-' . $oldid;
        $path = make_backup_temp_directory($tmp);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname("$dir/course-$oldid.mbz", $path);
        $courseid = restore_dbops::create_new_course($name, 'tmp-' . $oldid, $cat->id);
        $rc = new restore_controller($tmp, $courseid, backup::INTERACTIVE_NO, backup::MODE_GENERAL, $admin->id,
            backup::TARGET_NEW_COURSE);
        foreach (['users' => 0, 'role_assignments' => 0, 'comments' => 0, 'userscompletion' => 0, 'logs' => 0,
                'grade_histories' => 0] as $setting => $value) {
            if ($rc->get_plan()->setting_exists($setting)) {
                $rc->get_plan()->get_setting($setting)->set_value($value);
            }
        }
        if (!$rc->execute_precheck()) {
            $r = $rc->get_precheck_results();
            if (!empty($r['errors'])) {
                echo "$name: precheck failed: " . json_encode($r['errors']) . "\n";
                $rc->destroy();
                delete_course($courseid, false);
                continue;
            }
        }
        $rc->execute_plan();
        $rc->destroy();
        fulldelete($path);
        $c = $DB->get_record('course', ['id' => $courseid]);
        echo "$name -> id $courseid ($c->shortname), " . $DB->count_records('course_modules', ['course' => $courseid]) . " activities\n";
    }
}

// Free courses: visible, open to self-enrolment, a one-line summary, optional (not mandatory).
// The source courses were hidden drafts with no summary; Wellness Course had no cover.
$summaries = [
    'Wellness Course' => 'Practical habits for a healthier working life: sleep, nutrition, movement and managing stress.',
    'Wellness : Be Well and Thrive Program' => 'A guided programme to build energy, resilience and balance, one small step at a time.',
    'Global Wellness' => 'Wellbeing across cultures: how people around the world stay healthy, connected and well.',
    'Presentation Skills' => 'Plan, structure and deliver presentations that are clear, confident and remembered.',
    'Communication Skills' => 'Listen well, speak clearly and write with purpose, in person and online.',
    'Emotional Intelligence' => 'Understand your emotions and other people\'s, and use that to work better together.',
    'Information Security' => 'The basics of keeping company information safe in your day-to-day work.',
    'Cyber Security Measures' => 'The everyday measures that stop most attacks, and what to do if something looks wrong.',
    'Introduction to Cyber Security' => 'What cyber threats look like, why they matter, and the first steps to protect yourself.',
];
\local_siraj\coursefields::ensure();
$handler = \core_course\customfield\course_handler::create();
$self = enrol_get_plugin('self');
$fs = get_file_storage();
foreach ($summaries as $name => $summary) {
    if (!$course = $DB->get_record('course', ['fullname' => $name])) {
        continue;
    }
    $DB->update_record('course', (object)['id' => $course->id, 'visible' => 1, 'visibleold' => 1,
        'summary' => "<p>$summary</p>", 'summaryformat' => FORMAT_HTML]);
    $handler->instance_form_save((object)['id' => $course->id, 'customfield_siraj_mandatory' => 0]);
    $inst = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'self']);
    if ($inst) {
        $self->update_status($inst, ENROL_INSTANCE_ENABLED);
    } else {
        $self->add_instance($course, ['status' => ENROL_INSTANCE_ENABLED]);
    }
    $ctx = context_course::instance($course->id);
    if (!$fs->get_area_files($ctx->id, 'course', 'overviewfiles', 0, 'id', false) && file_exists(__DIR__ . '/img/photo/wellness.jpg')) {
        $fs->create_file_from_pathname(['contextid' => $ctx->id, 'component' => 'course', 'filearea' => 'overviewfiles',
            'itemid' => 0, 'filepath' => '/', 'filename' => 'wellness.jpg'], __DIR__ . '/img/photo/wellness.jpg');
    }
    // Some SCORM lessons had completion tracking off, so progress could not be counted: complete when the
    // lesson reports completed or passed.
    $scorms = $DB->get_records_sql("SELECT cm.id, cm.instance FROM {course_modules} cm JOIN {modules} m ON m.id = cm.module
        WHERE cm.course = ? AND m.name = 'scorm' AND cm.completion = 0", [$course->id]);
    foreach ($scorms as $cm) {
        $DB->set_field('course_modules', 'completion', COMPLETION_TRACKING_AUTOMATIC, ['id' => $cm->id]);
        $DB->set_field('scorm', 'completionstatusrequired', 6, ['id' => $cm->instance]);
    }
    // Some lessons only opened for a few days in May/June 2026 on siraj5; free courses stay open.
    $DB->execute("UPDATE {scorm} SET timeopen = 0, timeclose = 0 WHERE course = ?", [$course->id]);
    echo "ready: $name" . ($scorms ? ' (completion on for ' . count($scorms) . ' SCORM)' : '') . "\n";
}
fix_course_sortorder();
rebuild_course_cache(0, true);
\cache_helper::purge_by_event('changesincourse');
