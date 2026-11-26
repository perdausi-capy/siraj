<?php
// Siraj course catalogue: the categories and the designed demo courses, built from the definitions in catalogue/*.php.
// Each course has an introduction, lessons written with Siraj blocks (tiny_sirajblocks), practice, an assessment and a
// wrap-up, using a range of activity types. Rebuilding a course deletes it first (its enrolments and progress too).
// Run: php siraj_seed/catalogue.php            (categories, then every course in catalogue/)
//      php siraj_seed/catalogue.php CYB101     (categories, then just these courses)
define('CLI_SCRIPT', true);
require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/vendor/autoload.php');
require_once($CFG->dirroot . '/lib/phpunit/classes/util.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/choice/lib.php');

\core\session\manager::set_user(get_admin());
$gen = phpunit_util::get_data_generator();
set_config('enablecompletion', 1);
set_config('enableavailability', 1);
// Designed course text shouldn't sprout links: Moodle's activity-name auto-linking turns words like "Spotting phishing"
// in a summary into links to that activity.
require_once($CFG->libdir . '/filterlib.php');
filter_set_global_state('activitynames', TEXTFILTER_DISABLED);

// ---------- Siraj blocks (the same HTML the editor button inserts; styles in tiny_sirajblocks/styles.css) ----------

function sb_p(string ...$paras): string {
    return implode('', array_map(fn($t) => "<p>$t</p>", $paras));
}
function sb_ul(array $items): string {
    return '<ul>' . implode('', array_map(fn($t) => "<li>$t</li>", $items)) . '</ul>';
}
function sb_banner(string $eyebrow, string $title, string $lead): string {
    return "<div class=\"sb sb-banner\"><p class=\"sb-eyebrow\">$eyebrow</p><h2>$title</h2><p class=\"sb-lead\">$lead</p></div>";
}
/** @param string $kind info, tip, warn or summary */
function sb_note(string $kind, string $title, string $html): string {
    return "<div class=\"sb sb-note sb-$kind\"><p class=\"sb-title\">$title</p>$html</div>";
}
function sb_keypoints(string $title, array $points): string {
    return "<div class=\"sb sb-keypoints\"><p class=\"sb-title\">$title</p>" . sb_ul($points) . '</div>';
}
/** @param array $steps [[title, text], ...] */
function sb_steps(array $steps): string {
    return '<ol class="sb sb-steps">' . implode('', array_map(fn($s) => "<li><p class=\"sb-title\">{$s[0]}</p><p>{$s[1]}</p></li>", $steps)) . '</ol>';
}
function sb_dodont(array $do, array $dont, string $dolabel = 'Do', string $dontlabel = 'Don’t'): string {
    return "<div class=\"sb sb-dodont\"><div class=\"sb-do\"><p class=\"sb-title\">$dolabel</p>" . sb_ul($do) .
        "</div><div class=\"sb-dont\"><p class=\"sb-title\">$dontlabel</p>" . sb_ul($dont) . '</div></div>';
}
function sb_scenario(string $text, string $ask, string $label = 'Scenario'): string {
    return "<div class=\"sb sb-scenario\"><p class=\"sb-title\">$label</p>" . sb_p($text) . "<p class=\"sb-ask\">$ask</p></div>";
}
function sb_quote(string $text, string $cite): string {
    return "<blockquote class=\"sb sb-quote\"><p>$text</p><p class=\"sb-cite\">$cite</p></blockquote>";
}
/** @param array $stats [[value, label], ...] */
function sb_stats(array $stats): string {
    return '<div class="sb sb-stats">' . implode('', array_map(fn($s) => "<div class=\"sb-stat\"><p class=\"sb-value\">{$s[0]}</p><p>{$s[1]}</p></div>", $stats)) . '</div>';
}
/** @param array $qa [[question, answer], ...] */
function sb_faq(array $qa): string {
    return implode('', array_map(fn($x) => "<details class=\"sb sb-faq\"><summary>{$x[0]}</summary><p>{$x[1]}</p></details>", $qa));
}
/** @param array $cols [[title, html], ...] */
function sb_columns(array $cols): string {
    return '<div class="sb sb-columns">' . implode('', array_map(fn($c) => "<div class=\"sb-col\"><h3>{$c[0]}</h3>{$c[1]}</div>", $cols)) . '</div>';
}

// ---------- Categories ----------

if (!defined('SB_LIB_ONLY')) {

$categories = [
    'onboarding' => ['Onboarding', 'Everything a new starter needs in their first weeks: who we are, how we work and where to find help.'],
    'compliance' => ['Compliance & Policy', 'Mandatory training on how we do business: conduct, data protection and anti-bribery. Renewed every year.'],
    'safety' => ['Health, Safety & Wellbeing', 'Keeping yourself and others safe at work, and looking after your physical and mental health.'],
    'leadership' => ['Leadership & Management', 'Practical skills for people who lead teams: feedback, one-to-ones, meetings and decisions.'],
    'skills' => ['Professional Skills', 'Everyday skills that make work easier: clear communication, presenting and managing your time.'],
    'digital' => ['Digital & Security', 'Working safely and confidently with technology, from cyber security basics to using AI at work.'],
];
$catids = [];
$order = 0;
foreach ($categories as $key => [$name, $desc]) {
    $idnumber = 'siraj-' . $key;
    if ($rec = $DB->get_record('course_categories', ['idnumber' => $idnumber])) {
        $cat = core_course_category::get($rec->id);
        $cat->update(['name' => $name, 'description' => "<p>$desc</p>", 'descriptionformat' => FORMAT_HTML]);
    } else {
        $cat = core_course_category::create(['name' => $name, 'idnumber' => $idnumber, 'parent' => 0,
            'description' => "<p>$desc</p>", 'descriptionformat' => FORMAT_HTML]);
    }
    $catids[$key] = $cat->id;
    $DB->set_field('course_categories', 'sortorder', 10000 * (++$order), ['id' => $cat->id]);
}
fix_course_sortorder();
echo "categories: " . implode(', ', array_column($categories, 0)) . "\n";
}

// ---------- Building a course ----------

/**
 * A file from seed/files (or another seed path) in a fresh draft area, for activity file fields.
 */
function sb_draft(string $path, ?string $filename = null): int {
    global $USER;
    $draft = file_get_unused_draft_itemid();
    get_file_storage()->create_file_from_pathname(['contextid' => context_user::instance($USER->id)->id, 'component' => 'user',
        'filearea' => 'draft', 'itemid' => $draft, 'filepath' => '/', 'filename' => $filename ?? basename($path)], $path);
    return $draft;
}

/**
 * An H5P package made from one of Moodle's sample packages (which carry the libraries) with our own content.
 *
 * @param string $fixture e.g. 'multiple-choice-2-6' or 'filltheblanks'
 * @param string $title
 * @param array $content merged into the sample's content.json
 * @return string path of the built .h5p
 */
function sb_h5p(string $fixture, string $title, array $content): string {
    global $CFG;
    $src = "$CFG->dirroot/h5p/tests/fixtures/$fixture.h5p";
    // A plain file name (letters, digits, hyphens): characters like ? would break the package's web address.
    $out = make_request_directory() . '/' . trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($title)), '-') . '.h5p';
    copy($src, $out);
    $zip = new ZipArchive();
    $zip->open($out);
    $json = json_decode($zip->getFromName('content/content.json'), true);
    global $SB_LANG;
    $ar = $SB_LANG === 'ar';
    if ($ar) {
        // Arabic courses: H5P's own buttons and messages in Arabic, and the text right to left.
        $labels = ['UI' => ['checkAnswerButton' => 'تحقّق', 'showSolutionButton' => 'اعرض الحل', 'tryAgainButton' => 'أعد المحاولة',
                'scoreBarLabel' => 'حصلت على :num من :total', 'noInput' => 'أجب أولاً لعرض الحل'],
            'checkAnswer' => 'تحقّق', 'showSolutions' => 'اعرض الحل', 'tryAgain' => 'أعد المحاولة',
            'notFilledOut' => 'املأ كل الفراغات لعرض الحل', 'scoreBarLabel' => 'حصلت على :num من :total'];
        $json = array_replace_recursive($json, array_intersect_key($labels, $json));
        $rtl = fn($html) => '<div dir="rtl">' . $html . '</div>';
        foreach (['question', 'text'] as $k) {
            if (isset($content[$k])) {
                $content[$k] = $rtl($content[$k]);
            }
        }
        foreach ($content['answers'] ?? [] as $i => $answer) {
            $content['answers'][$i]['text'] = $rtl($answer['text']);
        }
        foreach ($content['questions'] ?? [] as $i => $q) {
            $content['questions'][$i] = $rtl($q);
        }
    }
    $zip->addFromString('content/content.json', json_encode(array_replace_recursive($json, $content)));
    $meta = json_decode($zip->getFromName('h5p.json'), true);
    $meta['title'] = $title;
    $meta['language'] = $ar ? 'ar' : 'en';
    $zip->addFromString('h5p.json', json_encode($meta));
    $zip->close();
    return $out;
}

/**
 * Add one activity from a definition to a course section (all the activity types the catalogue uses).
 *
 * @param stdClass $course
 * @param int $num Section number
 * @param array $a Activity definition
 * @param string $lang Course language ('' or 'ar')
 * @return stdClass|null The new activity (with ->cmid)
 */
function sb_add_item(stdClass $course, int $num, array $a, string $lang = ''): ?stdClass {
    $gen = phpunit_util::get_data_generator();
    $auto = ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1];
    $base = ['course' => $course->id, 'section' => $num, 'name' => $a['name'], 'intro' => sb_p($a['intro'] ?? ''),
        'introformat' => FORMAT_HTML, 'showdescription' => 0];
    $made = null;
    switch ($a['type']) {
        case 'page':
            $made = $gen->create_module('page', $base + $auto + ['content' => $a['content'], 'contentformat' => FORMAT_HTML,
                'printintro' => 0, 'printlastmodified' => 0]);
            break;
        case 'book':
            $made = $book = $gen->create_module('book', $base + $auto + ['numbering' => 1]);
            foreach ($a['chapters'] as $n => [$title, $html]) {
                $gen->get_plugin_generator('mod_book')->create_chapter(['bookid' => $book->id, 'title' => $title,
                    'content' => $html, 'contentformat' => FORMAT_HTML, 'pagenum' => $n + 1]);
            }
            break;
        case 'url':
            $made = $gen->create_module('url', $base + $auto + ['externalurl' => $a['url'], 'display' => 0]);
            break;
        case 'file':
            $made = $gen->create_module('resource', $base + $auto + ['files' => sb_draft(__DIR__ . '/' . $a['file']), 'display' => 0]);
            break;
        case 'h5p':
            $made = $gen->create_module('h5pactivity', $base + $auto + ['packagefile' => sb_draft($a['package']),
                'enabletracking' => 1, 'grademethod' => 1]);
            break;
        case 'glossary':
            $made = $g = $gen->create_module('glossary', $base + $auto + ['displayformat' => 'fullwithoutauthor']);
            foreach ($a['entries'] as [$concept, $definition]) {
                $gen->get_plugin_generator('mod_glossary')->create_content($g, ['concept' => $concept,
                    'definition' => $definition, 'definitionformat' => FORMAT_HTML, 'approved' => 1, 'usedynalink' => 0]);
            }
            break;
        case 'choice':
            $made = $gen->create_module('choice', $base + ['option' => $a['options'], 'showresults' => CHOICE_SHOWRESULTS_AFTER_ANSWER,
                'allowupdate' => 1, 'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1]);
            break;
        case 'forum':
            $made = $f = $gen->create_module('forum', $base + $auto + ['type' => 'general']);
            foreach ($a['posts'] ?? [] as [$subject, $message]) {
                $gen->get_plugin_generator('mod_forum')->create_discussion(['course' => $course->id, 'forum' => $f->id,
                    'userid' => get_admin()->id, 'name' => $subject, 'message' => sb_p($message), 'messageformat' => FORMAT_HTML]);
            }
            break;
        case 'assign':
            $made = $gen->create_module('assign', $base + ['activity' => $a['instructions'], 'activityformat' => FORMAT_HTML,
                'assignsubmission_onlinetext_enabled' => 1, 'assignsubmission_onlinetext_wordlimit' => $a['words'] ?? 0,
                'assignsubmission_onlinetext_wordlimit_enabled' => !empty($a['words']) ? 1 : 0,
                'assignsubmission_file_enabled' => 0, 'submissiondrafts' => 0, 'grade' => 100,
                'duedate' => usergetmidnight(time()) + ($a['dueindays'] ?? 21) * DAYSECS + 17 * HOURSECS,
                'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionsubmit' => 1]);
            break;
        case 'quiz':
            $made = sb_quiz($gen, $base, $a + ['lang' => $lang]);
            break;
        case 'scorm':
            // A SCORM package from seed/ (e.g. scorm-kickoff.zip): completes when the package reports it.
            $made = $gen->create_module('scorm', $base + ['packagefilepath' => __DIR__ . '/' . $a['package'],
                'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionstatusrequired' => 6]);
            break;
        case 'live':
            // A live session (BigBlueButton) in N days at 10:00, one hour.
            $start = usergetmidnight(time()) + ($a['indays'] ?? 7) * DAYSECS + 10 * HOURSECS;
            $made = $gen->create_module('bigbluebuttonbn', $base + $auto + ['type' => 0, 'openingtime' => $start,
                'closingtime' => $start + HOURSECS, 'wait' => 0]);
            break;
        case 'feedback':
            $made = $fb = $gen->create_module('feedback', $base + ['anonymous' => 1, 'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionsubmit' => 1, 'page_after_submit' => sb_p($a['thanks']), 'page_after_submitformat' => FORMAT_HTML]);
            $fgen = $gen->get_plugin_generator('mod_feedback');
            foreach ($a['questions'] as $q) {
                if ($q[0] === 'text') {
                    $fgen->create_item_textarea($fb, ['name' => $q[1], 'label' => '', 'required' => 0, 'presentation' => '40|4']);
                } else {
                    $fgen->create_item_multichoice($fb, ['name' => $q[1], 'label' => '', 'required' => 1, 'subtype' => 'r',
                        'values' => implode("\n", $q[2])]);
                }
            }
            break;
    }
    return $made;
}

/**
 * Build (or rebuild) one course from its definition.
 *
 * @param array $c Definition (see catalogue/CYB101.php)
 * @param array $catids
 */
function sb_build_course(array $c, array $catids): void {
    global $DB, $CFG;
    $gen = phpunit_util::get_data_generator();
    if ($old = $DB->get_record('course', ['shortname' => $c['shortname']])) {
        delete_course($old->id, false);
    }
    $ableto = ($c['lang'] ?? '') === 'ar' ? 'بنهاية هذه الدورة ستكون قادرًا على:' : 'By the end you will be able to:';
    $summary = sb_p($c['summary']) . "<p><strong>$ableto</strong></p>" . sb_ul($c['outcomes']);
    $course = $gen->create_course(['fullname' => $c['fullname'], 'shortname' => $c['shortname'], 'idnumber' => $c['shortname'],
        'category' => $catids[$c['category']], 'summary' => $summary, 'summaryformat' => FORMAT_HTML, 'format' => 'topics',
        'numsections' => count($c['sections']), 'enablecompletion' => 1, 'showcompletionconditions' => 1,
        'startdate' => usergetmidnight(time()) - 60 * DAYSECS, 'lang' => $c['lang'] ?? '']);
    $context = context_course::instance($course->id);

    // Duration and mandatory flag (the Siraj course fields), cover photo.
    \local_siraj\coursefields::ensure();
    \core_course\customfield\course_handler::create()->instance_form_save((object)['id' => $course->id,
        'customfield_siraj_mandatory' => (int)!empty($c['mandatory']), 'customfield_siraj_duration' => (string)$c['minutes']]);
    if (!empty($c['cover']) && is_readable(__DIR__ . '/' . $c['cover'])) {
        get_file_storage()->create_file_from_pathname(['contextid' => $context->id, 'component' => 'course',
            'filearea' => 'overviewfiles', 'itemid' => 0, 'filepath' => '/', 'filename' => basename($c['cover'])], __DIR__ . '/' . $c['cover']);
    }

    $auto = ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => 1];
    $cms = [];
    $restricted = [];
    foreach ($c['sections'] as $i => $section) {
        $num = $i + 1;
        $sec = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $num]);
        course_update_section($course, $sec, ['name' => $section['name'], 'summary' => sb_p($section['summary'] ?? ''),
            'summaryformat' => FORMAT_HTML]);
        foreach ($section['items'] as $a) {
            $made = sb_add_item($course, $num, $a, $c['lang'] ?? '');
            if ($made) {
                $cms[$a['name']] = (int)$made->cmid;
                if (!empty($a['after'])) {
                    $restricted[$made->cmid] = $a['after'];
                }
            }
        }
    }
    // Restrictions: "available after you complete ...", shown (greyed, with the reason) until then.
    foreach ($restricted as $cmid => $after) {
        $conds = array_map(fn($name) => ['type' => 'completion', 'cm' => $cms[$name], 'e' => COMPLETION_COMPLETE], $after);
        $DB->set_field('course_modules', 'availability', json_encode(['op' => '&', 'c' => $conds,
            'showc' => array_fill(0, count($conds), true)]), ['id' => $cmid]);
    }
    rebuild_course_cache($course->id, true);

    // People: the demo learners as students (dashboards and reports have something to show), a teacher.
    foreach (array_merge(['admin'], $c['learners'] ?? ['layla', 'sara', 'yousef', 'mariam', 'adam']) as $username) {
        if ($u = $DB->get_record('user', ['username' => $username])) {
            $gen->enrol_user($u->id, $course->id, 'student');
        }
    }
    if ($t = $DB->get_record('user', ['username' => $c['teacher'] ?? 'hana'])) {
        $gen->enrol_user($t->id, $course->id, 'editingteacher');
    }
    echo "course: {$c['shortname']} {$c['fullname']} (id $course->id)\n";
}

/**
 * A quiz with real questions and feedback.
 * Questions: ['mc', text, correct, [wrong...], explanation] or ['tf', statement, true|false, explanation].
 */
function sb_quiz($gen, array $base, array $a): stdClass {
    global $DB;
    $n = count($a['questions']);
    $quiz = $gen->create_module('quiz', $base + ['grade' => 10, 'sumgrades' => $n, 'gradepass' => $a['pass'] ?? 7,
        'attempts' => 0, 'questionsperpage' => 1, 'preferredbehaviour' => 'deferredfeedback',
        'completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionusegrade' => 1, 'completionpassgrade' => 1]);
    $qgen = $gen->get_plugin_generator('core_question');
    $cat = $qgen->create_question_category(['contextid' => context_module::instance($quiz->cmid)->id]);
    $ar = ($a['lang'] ?? '') === 'ar';
    $right = $ar ? '<p>إجابة صحيحة.</p>' : '<p>Correct.</p>';
    $wrong = $ar ? '<p>ليست الإجابة الصحيحة.</p>' : '<p>Not quite.</p>';
    $partly = $ar ? '<p>إجابة صحيحة جزئيًا.</p>' : '<p>Partly right.</p>';
    foreach ($a['questions'] as $i => $q) {
        $name = 'Q' . ($i + 1);
        if ($q[0] === 'mc') {
            $question = $qgen->create_question('multichoice', 'one_of_four', ['category' => $cat->id, 'name' => $name,
                'questiontext' => ['text' => "<p>{$q[1]}</p>", 'format' => FORMAT_HTML],
                'generalfeedback' => ['text' => "<p>{$q[4]}</p>", 'format' => FORMAT_HTML]]);
            $answers = array_values($DB->get_records('question_answers', ['question' => $question->id], 'fraction DESC, id'));
            foreach (array_merge([$q[2]], $q[3]) as $k => $text) {
                if (isset($answers[$k])) {
                    $DB->update_record('question_answers', (object)['id' => $answers[$k]->id, 'answer' => "<p>$text</p>",
                        'answerformat' => FORMAT_HTML, 'feedback' => '', 'fraction' => $k === 0 ? 1 : 0]);
                }
            }
            $DB->set_field('qtype_multichoice_options', 'correctfeedback', $right, ['questionid' => $question->id]);
            $DB->set_field('qtype_multichoice_options', 'partiallycorrectfeedback', $partly, ['questionid' => $question->id]);
            $DB->set_field('qtype_multichoice_options', 'incorrectfeedback', $wrong, ['questionid' => $question->id]);
            $DB->set_field('qtype_multichoice_options', 'shuffleanswers', 1, ['questionid' => $question->id]);
        } else {
            $question = $qgen->create_question('truefalse', null, ['category' => $cat->id, 'name' => $name,
                'questiontext' => ['text' => "<p>{$q[1]}</p>", 'format' => FORMAT_HTML], 'correctanswer' => $q[2] ? 1 : 0,
                'generalfeedback' => ['text' => "<p>{$q[3]}</p>", 'format' => FORMAT_HTML]]);
            foreach ($DB->get_records('question_answers', ['question' => $question->id]) as $ans) {
                $DB->update_record('question_answers', (object)['id' => $ans->id, 'feedback' => $ans->fraction >= 1 ? $right : $wrong,
                    'feedbackformat' => FORMAT_HTML]);
            }
        }
        quiz_add_quiz_question($question->id, $DB->get_record('quiz', ['id' => $quiz->id]));
    }
    \mod_quiz\quiz_settings::create($quiz->id)->get_grade_calculator()->recompute_quiz_sumgrades();
    return $quiz;
}

// ---------- Run ----------

if (!defined('SB_LIB_ONLY')) {

$only = array_slice($argv, 1);
foreach (glob(__DIR__ . '/catalogue/*.php') as $file) {
    $short = basename($file, '.php');
    if ($only && !in_array($short, $only, true)) {
        continue;
    }
    $SB_LANG = preg_match("/'lang'\s*=>\s*'ar'/", file_get_contents($file)) ? 'ar' : 'en';
    sb_build_course(require($file), $catids);
}
\cache::make('core', 'questiondata')->purge();
echo "catalogue done\n";
}
