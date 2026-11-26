<?php
// After seed/catalogue.php: move people from the old demo courses to the new catalogue, give the demo learners
// realistic progress, move the compliance-matrix rules, then delete the old courses and their empty categories.
// Run: php siraj_seed/catalogue_people.php          (shows what it would do)
//      php siraj_seed/catalogue_people.php --apply  (does it)
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/lib.php');

$apply = in_array('--apply', $argv, true);
\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();

// Old course (shortname) => new course(s) its people and matrix rules move to.
$map = [
    'HSE101' => ['HSW201'], 'FIRE101' => ['HSW201'], 'COC101' => ['CND201'], 'ABC101' => ['CND201'],
    'SEC101' => ['CYB101'], 'DPE101' => ['DPR201'], 'LEAD301' => ['LDR301', 'FBK301'], 'PM201' => ['COM201', 'PRS201'],
    'AR-HSE' => ['AR-SAF101'], 'AR-CS' => ['AR-CSV101'], 'SIRAJ-QA' => [],
];
// The free courses imported from siraj5 (SCORM lessons) stay: they move into the catalogue's categories.
$keep = ['Wellness Course' => 'siraj-safety', 'Wellness : Be Well and Thrive Program' => 'siraj-safety',
    'Global Wellness' => 'siraj-safety', 'Presentation Skills' => 'siraj-skills', 'Communication Skills' => 'siraj-skills',
    'Emotional Intelligence' => 'siraj-skills', 'Information Security' => 'siraj-digital',
    'Cyber Security Measures' => 'siraj-digital', 'Introduction to Cyber Security' => 'siraj-digital'];
foreach ($keep as $short => $catidnumber) {
    $course = $DB->get_record('course', ['shortname' => $short]);
    $catid = $DB->get_field('course_categories', 'id', ['idnumber' => $catidnumber]);
    if ($course && $catid && $course->category != $catid) {
        echo "move: $short -> $catidnumber\n";
        if ($apply) {
            move_courses([$course->id], $catid);
        }
    }
}
// Everyone does the welcome course; recent joiners also do their first 30 days; a few have started on AI and wellbeing.
$extra = ['ONB101' => '*', 'ONB102' => ['zaid', 'faisal', 'tariq', 'huda'], 'AIW101' => ['admin', 'omar', 'sara', 'adam', 'mariam', 'yousef'],
    'WEL201' => ['admin', 'layla', 'noura', 'khalid', 'reem']];

$new = $DB->get_records_select_menu('course', 'idnumber <> ? AND id <> ?', ['', SITEID], '', 'shortname, id');
$old = $DB->get_records_list('course', 'shortname', array_keys($map), '', 'id, shortname, fullname');

// ---------- People and roles ----------
$plan = []; // newshort => [userid => role]
foreach ($old as $c) {
    $ctx = context_course::instance($c->id);
    foreach (get_enrolled_users($ctx, '', 0, 'u.id, u.username') as $u) {
        $role = has_capability('moodle/course:update', $ctx, $u->id) && !is_siteadmin($u) ? 'editingteacher' : 'student';
        foreach ($map[$c->shortname] as $short) {
            if (isset($new[$short])) {
                $plan[$short][$u->id] = ($plan[$short][$u->id] ?? '') === 'editingteacher' ? 'editingteacher' : $role;
            }
        }
    }
}
$learners = $DB->get_records_select('user', "deleted = 0 AND suspended = 0 AND username NOT IN ('guest') AND id > 2", null, '', 'id, username');
$learners[get_admin()->id] = get_admin();
foreach ($extra as $short => $who) {
    foreach ($learners as $u) {
        if (isset($new[$short]) && ($who === '*' || in_array($u->username, $who, true))) {
            $plan[$short][$u->id] = $plan[$short][$u->id] ?? 'student';
        }
    }
}

// Progress: a stable mix per person and course (so dashboards, reports and certificates have something real to show).
function sb_share(int $userid, string $short, bool $mandatory): float {
    $h = crc32("$userid|$short") % 100;
    if ($mandatory) {
        return $h < 30 ? 1.0 : ($h < 55 ? 0.7 : ($h < 80 ? 0.35 : 0.0));
    }
    return $h < 15 ? 1.0 : ($h < 40 ? 0.5 : ($h < 65 ? 0.2 : 0.0));
}

foreach ($plan as $short => $people) {
    $course = get_course($new[$short]);
    $fields = \local_siraj\coursefields::values([$course->id])[$course->id];
    $mandatory = !empty($fields['mandatory']);
    echo "$short: " . count($people) . " people\n";
    if (!$apply) {
        continue;
    }
    $completion = new completion_info($course);
    foreach ($people as $userid => $role) {
        $gen->enrol_user($userid, $course->id, $role);
        if ($role !== 'student') {
            continue;
        }
        $cms = array_values(array_filter(get_fast_modinfo($course, $userid)->get_cms(),
            fn($cm) => $cm->url && $completion->is_enabled($cm)));
        $n = (int)round(count($cms) * sb_share($userid, $short, $mandatory));
        $when = time() - (count($cms) + 3) * DAYSECS;
        foreach (array_slice($cms, 0, $n) as $i => $cm) {
            $completion->update_state($cm, COMPLETION_COMPLETE, $userid, true);
            $DB->set_field('course_modules_completion', 'timemodified', $when + $i * DAYSECS + ($userid % 7) * HOURSECS,
                ['coursemoduleid' => $cm->id, 'userid' => $userid]);
        }
        if ($n > 0) {
            $last = $when + $n * DAYSECS;
            if ($la = $DB->get_record('user_lastaccess', ['userid' => $userid, 'courseid' => $course->id])) {
                $DB->set_field('user_lastaccess', 'timeaccess', $last, ['id' => $la->id]);
            } else {
                $DB->insert_record('user_lastaccess', (object)['userid' => $userid, 'courseid' => $course->id, 'timeaccess' => $last]);
            }
        }
    }
}

// ---------- Compliance matrix rules ----------
foreach ($DB->get_records_list('local_siraj_rolereq', 'courseid', array_keys($old)) as $rule) {
    foreach ($map[$old[$rule->courseid]->shortname] as $short) {
        if (isset($new[$short]) && $apply && !$DB->record_exists('local_siraj_rolereq', ['role' => $rule->role, 'courseid' => $new[$short]])) {
            $DB->insert_record('local_siraj_rolereq', (object)['role' => $rule->role, 'courseid' => $new[$short], 'timecreated' => time()]);
        }
    }
    echo "rule: {$rule->role} -> " . implode(', ', $map[$old[$rule->courseid]->shortname]) . "\n";
}

// ---------- Retire the old courses and their categories ----------
foreach ($old as $c) {
    echo "delete course: {$c->shortname} ({$c->fullname})\n";
    if ($apply) {
        $DB->delete_records('local_siraj_rolereq', ['courseid' => $c->id]);
        $DB->delete_records('local_siraj_cert', ['courseid' => $c->id]);
        delete_course($c->id, false);
    }
}
if ($apply) {
    fix_course_sortorder();
    foreach ($DB->get_records_select('course_categories', "parent = 0 AND (idnumber IS NULL OR idnumber = '' OR idnumber NOT LIKE 'siraj-%')") as $cat) {
        if (!$DB->record_exists('course', ['category' => $cat->id]) && !$DB->record_exists('course_categories', ['parent' => $cat->id])) {
            core_course_category::get($cat->id, MUST_EXIST, true)->delete_full(false);
            echo "delete category: {$cat->name}\n";
        }
    }
    purge_all_caches();
}
echo $apply ? "done\n" : "(dry run: add --apply to do it)\n";
