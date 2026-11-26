<?php
/**
 * Siraj Learning mode: one activity at a time, with previous/next, progress and completion.
 * Pages, links, files and books play inside the player and are marked viewed exactly like Moodle's own view pages.
 * SCORM runs in Moodle's own SCORM player, framed inside Learning mode, and H5P in Moodle's own H5P player, so their
 * tracking (SCORM data model, H5P xAPI results) is Moodle's. Other types (quiz, assignment...) get a launch card.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for Learning mode.
 */
class player {
    /** @var progress */
    protected $p;
    /** @var \cm_info */
    protected $cm;
    /** @var int Book chapter asked for (0 = first). */
    protected $chapterid;
    /** @var array|null Book chapters, loaded once. */
    protected $chapters = null;

    /**
     * Constructor.
     *
     * @param progress $p
     * @param \cm_info $cm
     */
    public function __construct(progress $p, \cm_info $cm, int $chapterid = 0) {
        $this->p = $p;
        $this->cm = $cm;
        $this->chapterid = $chapterid;
    }

    /**
     * Book chapters the learner can see, in order, and the current one.
     *
     * @return array [chapters (id => record), current chapter record|null]
     */
    protected function book_chapters(): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/book/locallib.php');
        if ($this->chapters === null) {
            $book = $this->instance();
            $this->chapters = array_filter(book_preload_chapters($book), fn($ch) => empty($ch->hidden));
        }
        $current = $this->chapters[$this->chapterid] ?? reset($this->chapters) ?: null;
        return [$this->chapters, $current];
    }

    /**
     * The activity's own record.
     *
     * @return \stdClass
     */
    protected function instance(): \stdClass {
        global $DB;
        static $cache = [];
        return $cache[$this->cm->id] ??= $DB->get_record($this->cm->modname, ['id' => $this->cm->instance], '*', MUST_EXIST);
    }

    /**
     * Record the view (completion "view" rule and logs), the same way the activity's own view.php does.
     */
    public function record_view(): void {
        global $DB, $CFG;
        $cm = $this->cm;
        $context = \context_module::instance($cm->id);
        $instance = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
        switch ($cm->modname) {
            case 'page':
                require_once($CFG->dirroot . '/mod/page/lib.php');
                page_view($instance, $this->p->course, $cm, $context);
                break;
            case 'url':
                require_once($CFG->dirroot . '/mod/url/lib.php');
                url_view($instance, $this->p->course, $cm, $context);
                break;
            case 'resource':
                require_once($CFG->dirroot . '/mod/resource/lib.php');
                resource_view($instance, $this->p->course, $cm, $context);
                break;
            case 'book':
                // Like Moodle's book: viewing the last chapter is what completes "view" completion.
                require_once($CFG->dirroot . '/mod/book/lib.php');
                [$chapters, $chapter] = $this->book_chapters();
                if ($chapter) {
                    book_view($instance, $chapter, \mod_book\helper::is_last_visible_chapter($chapter->id, $chapters),
                        $this->p->course, $cm, $context);
                } else {
                    book_view($instance, null, false, $this->p->course, $cm, $context);
                }
                break;
            case 'h5pactivity':
                \mod_h5pactivity\local\manager::create_from_coursemodule($cm)->set_module_viewed($this->p->course);
                break;
            case 'scorm':
                require_once($CFG->dirroot . '/mod/scorm/lib.php');
                scorm_view($instance, $this->p->course, $cm, $context);
                break;
            default:
                if (activity_views::handles($cm->modname) && !in_array($cm->modname, activity_views::STAGED, true)) {
                    (new activity_views($cm, $this->p->course, $instance, ''))->record_view();
                }
        }
    }

    /**
     * The activity body.
     *
     * @return array
     */
    protected function body(): array {
        global $DB, $CFG;
        $cm = $this->cm;
        $context = \context_module::instance($cm->id);
        $instance = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
        $intro = !empty($instance->intro) ? format_module_intro($cm->modname, $instance, $cm->id) : '';

        if ($cm->modname === 'page') {
            $content = file_rewrite_pluginfile_urls($instance->content, 'pluginfile.php', $context->id, 'mod_page', 'content',
                $instance->revision);
            return ['reading' => format_text($content, $instance->contentformat,
                ['noclean' => true, 'overflowdiv' => true, 'context' => $context])];
        }
        if ($cm->modname === 'url') {
            require_once($CFG->dirroot . '/mod/url/locallib.php');
            $host = (string)parse_url($instance->externalurl, PHP_URL_HOST);
            $rest = preg_replace('~^[a-z][a-z0-9+.-]*://[^/?#]*~i', '', $instance->externalurl);
            return ['link' => [
                'url' => url_get_full_url($instance, $cm, $this->p->course),
                'external' => $instance->externalurl,
                'host' => preg_replace('/^www\./', '', $host),
                'rest' => $rest === '/' ? '' : $rest,
                'initial' => \core_text::strtoupper(\core_text::substr(preg_replace('/^www\./', '', $host) ?: '?', 0, 1)),
                'intro' => $intro,
            ]];
        }
        if ($cm->modname === 'resource') {
            $files = get_file_storage()->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
            $file = reset($files);
            if ($file) {
                $url = \moodle_url::make_pluginfile_url($context->id, 'mod_resource', 'content', $instance->revision,
                    $file->get_filepath(), $file->get_filename())->out(false);
                $mime = $file->get_mimetype();
                return ['file' => [
                    'url' => $url, 'name' => $file->get_filename(), 'size' => display_size($file->get_filesize()),
                    'pdf' => $mime === 'application/pdf', 'image' => strpos($mime, 'image/') === 0,
                    'video' => strpos($mime, 'video/') === 0, 'intro' => $intro,
                ]];
            }
        }
        if ($cm->modname === 'book') {
            [$chapters, $chapter] = $this->book_chapters();
            if ($chapter) {
                $ids = array_keys($chapters);
                $at = array_search($chapter->id, $ids);
                $url = fn($id) => (new \moodle_url('/local/siraj/learn.php', ['cmid' => $cm->id, 'chapterid' => $id]))->out(false);
                $text = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book', 'chapter',
                    $chapter->id);
                $toc = [];
                foreach ($chapters as $ch) {
                    $toc[] = ['title' => format_string($ch->title, true, ['context' => $context, 'escape' => false]), 'url' => $url($ch->id),
                        'current' => $ch->id == $chapter->id, 'sub' => !empty($ch->subchapter),
                        'number' => !empty($ch->number) ? $ch->number : ''];
                }
                return ['book' => [
                    'title' => format_string($chapter->title, true, ['context' => $context, 'escape' => false]),
                    'content' => format_text($text, $chapter->contentformat, ['noclean' => true, 'overflowdiv' => true,
                        'context' => $context]),
                    'toc' => $toc, 'count' => count($chapters), 'many' => count($chapters) > 1,
                    'position' => get_string('chapterof', 'local_siraj', (object)['n' => $at + 1, 'total' => count($chapters)]),
                    'prevurl' => $at > 0 ? $url($ids[$at - 1]) : null,
                    'nexturl' => $at < count($ids) - 1 ? $url($ids[$at + 1]) : null,
                    'nexttitle' => $at < count($ids) - 1 ? format_string($chapters[$ids[$at + 1]]->title, true, ['context' => $context, 'escape' => false]) : '',
                ]];
            }
        }
        if ($cm->modname === 'scorm') {
            require_once($CFG->dirroot . '/mod/scorm/locallib.php');
            [$available, $warnings] = scorm_get_availability_status($instance);
            if ($available && $instance->launch) {
                return ['embed' => [
                    'kind' => 'scorm',
                    'src' => (new \moodle_url('/mod/scorm/player.php', ['a' => $instance->id, 'scoid' => $instance->launch,
                        'currentorg' => '', 'mode' => 'normal', 'display' => 'popup']))->out(false),
                    'title' => $cm->get_formatted_name(['escape' => false]),
                    'intro' => $intro,
                    'hint' => get_string('scorm_hint', 'local_siraj'),
                ]];
            }
        }
        if ($cm->modname === 'h5pactivity') {
            $files = get_file_storage()->get_area_files($context->id, 'mod_h5pactivity', 'package', 0, 'id', false);
            if ($file = reset($files)) {
                $manager = \mod_h5pactivity\local\manager::create_from_coursemodule($cm);
                $core = (new \core_h5p\factory())->get_core();
                $config = \core_h5p\helper::decode_display_options($core, $instance->displayoptions);
                $fileurl = \moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
                return ['h5p' => [
                    'html' => \core_h5p\player::display($fileurl, $config, true, 'mod_h5pactivity', true),
                    'intro' => $intro,
                    'preview' => !$manager->can_submit() && !isguestuser(),
                ]];
            }
        }
        if ($cm->modname === 'assign') {
            return ['assign' => $this->assign_status($intro)];
        }
        if ($cm->modname === 'quiz') {
            return ['quiz' => $this->quiz_status($intro)];
        }
        if (activity_views::handles($cm->modname)) {
            return (new activity_views($cm, $this->p->course, $instance, $intro))->body();
        }
        // Everything else: Moodle's own page does the work. Say what kind of activity it is and open it.
        $sm = get_string_manager();
        $desc = $sm->string_exists('launch_' . $cm->modname, 'local_siraj') ? 'launch_' . $cm->modname : 'launch_other';
        return ['launch' => [
            'url' => $cm->url->out(false),
            'type' => get_string('modulename', $cm->modname),
            'desc' => get_string($desc, 'local_siraj'),
            'button' => get_string('openactivity', 'local_siraj', get_string('modulename', $cm->modname)),
            'icon' => icons::svg(progress::icon($cm->modname)),
            'intro' => $intro,
        ]];
    }

    /**
     * Assignment at a glance: what to submit, when, where it stands, the grade and feedback, and one next action.
     * Submitting itself happens on Moodle's assignment page (restyled), which keeps every submission plugin working.
     *
     * @param string $intro
     * @return array
     */
    protected function assign_status(string $intro): array {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->libdir . '/gradelib.php');
        $cm = $this->cm;
        $context = \context_module::instance($cm->id);
        $assign = new \assign($context, $cm, $this->p->course);
        $instance = $assign->get_instance();
        $submission = $assign->get_user_submission($USER->id, false);
        $status = $submission ? $submission->status : ASSIGN_SUBMISSION_STATUS_NEW;
        $now = time();

        // What to submit.
        $types = [];
        foreach ($assign->get_submission_plugins() as $plugin) {
            if (!$plugin->is_enabled() || !$plugin->is_visible() || $plugin->get_type() === 'comments') {
                continue;
            }
            $detail = '';
            if ($plugin->get_type() === 'file') {
                $max = (int)$plugin->get_config('maxfilesubmissions');
                $ftypes = trim((string)$plugin->get_config('filetypeslist'));
                $detail = implode(' · ', array_filter([
                    $max ? get_string($max == 1 ? 'onefile' : 'upton_files', 'local_siraj', $max) : '',
                    $ftypes !== '' ? str_replace(',', ', ', $ftypes) : '',
                ]));
            } else if ($plugin->get_type() === 'onlinetext') {
                $limit = (int)$plugin->get_config('wordlimit');
                $detail = $limit ? plural::str('wordlimit', $limit) : get_string('typeanswer', 'local_siraj');
            }
            $types[] = ['name' => $plugin->get_name(), 'detail' => $detail, 'icon' => icons::svg($plugin->get_type() === 'file' ? 'upload' : 'file')];
        }

        // Grade and feedback, only once released in the gradebook.
        $grade = null;
        $grades = grade_get_grades($this->p->course->id, 'mod', 'assign', $instance->id, $USER->id);
        $item = $grades->items[0] ?? null;
        if ($item && isset($item->grades[$USER->id]) && empty($item->grades[$USER->id]->hidden)) {
            $g = $item->grades[$USER->id];
            if ($g->grade !== null || !empty($g->feedback)) {
                $grade = ['value' => $g->grade !== null ? $g->str_long_grade : '', 'feedback' => $g->feedback ? format_text($g->feedback,
                    $g->feedbackformat, ['context' => $context]) : ''];
            }
        }

        $submitted = $status === ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $state = $grade ? 'graded' : ($submitted ? 'submitted' : ($status === ASSIGN_SUBMISSION_STATUS_DRAFT ? 'draft' : 'new'));
        $chip = ['graded' => 'green', 'submitted' => 'blue', 'draft' => 'amber', 'new' => ''][$state];
        $due = (int)$instance->duedate;
        $late = $due && $due < $now && !$submitted;
        $canedit = $assign->can_edit_submission($USER->id, $USER->id) && $assign->submissions_open($USER->id);
        $viewurl = new \moodle_url('/mod/assign/view.php', ['id' => $cm->id, 'action' => 'view']);
        // siraj=learn: after saving (or cancelling) Moodle's submission form, go back to this step in Learning mode.
        $editurl = new \moodle_url('/mod/assign/view.php', ['id' => $cm->id, 'action' => 'editsubmission', 'siraj' => 'learn']);
        if ($state === 'new' && $canedit) {
            $action = ['text' => get_string('addsubmission', 'local_siraj'), 'url' => $editurl->out(false), 'primary' => true];
        } else if ($state === 'draft' && $canedit) {
            $action = ['text' => get_string('continuesubmission', 'local_siraj'), 'url' => $viewurl->out(false), 'primary' => true];
        } else {
            $action = ['text' => get_string($grade ? 'viewfeedback' : 'viewsubmission', 'local_siraj'), 'url' => $viewurl->out(false),
                'primary' => false];
        }
        $activity = !empty($instance->activity) ? format_text(file_rewrite_pluginfile_urls($instance->activity, 'pluginfile.php',
            $context->id, 'mod_assign', ASSIGN_ACTIVITYATTACHMENT_FILEAREA, 0), $instance->activityformat, ['context' => $context]) : '';

        return [
            'intro' => $intro, 'activity' => $activity,
            'state' => get_string('as_' . $state, 'local_siraj'), 'chip' => $chip,
            'due' => $due ? userdate($due, get_string('strftimedaydatetime', 'langconfig')) : '',
            'duerel' => $due ? ($late ? plural::str('overdue_by', (int)ceil(($now - $due) / DAYSECS))
                : (new home(\core_user::get_user($USER->id)))->relative($due)) : '',
            'late' => $late,
            'types' => $types, 'hastypes' => (bool)$types,
            'submittedon' => $submission && $submitted ? userdate($submission->timemodified, get_string('strftimedaydatetime', 'langconfig')) : '',
            'grade' => $grade,
            'action' => $action,
            'editurl' => $canedit && in_array($state, ['submitted', 'draft'], true) && $instance->submissiondrafts == 0 ? $editurl->out(false) : null,
        ];
    }

    /**
     * Quiz at a glance: questions are answered on Moodle's quiz pages (restyled); this shows the rules, attempts,
     * best grade and one next action (start, continue, try again, review).
     *
     * @param string $intro
     * @return array
     */
    protected function quiz_status(string $intro): array {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/quiz/lib.php');
        $cm = $this->cm;
        $quizobj = \mod_quiz\quiz_settings::create($cm->instance, $USER->id);
        $quiz = $quizobj->get_quiz();
        $attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all');
        $finished = array_filter($attempts, fn($a) => $a->state === \mod_quiz\quiz_attempt::FINISHED);
        $inprogress = array_values(array_filter($attempts, fn($a) => in_array($a->state,
            [\mod_quiz\quiz_attempt::IN_PROGRESS, \mod_quiz\quiz_attempt::OVERDUE], true)));
        $best = quiz_get_best_grade($quiz, $USER->id);
        $now = time();
        $access = $quizobj->get_access_manager($now);
        $closed = $quiz->timeclose && $quiz->timeclose < $now;
        // Moodle counts a closed quiz as "finished" too; that case has its own message, so only say
        // "no more attempts" when the attempts (or another rule) actually ran out.
        $nomore = !$closed && $access->is_finished(count($finished), end($finished) ?: null);
        $last = $finished ? end($finished) : null;

        $rules = [];
        $rules[] = ['label' => get_string('questions', 'local_siraj'), 'value' => count($quizobj->get_structure()->get_slots())];
        if ($quiz->timelimit) {
            $rules[] = ['label' => get_string('timelimit', 'local_siraj'), 'value' => format_time($quiz->timelimit)];
        }
        $rules[] = ['label' => get_string('attemptsallowed', 'local_siraj'),
            'value' => $quiz->attempts ? get_string('nofm', 'local_siraj', (object)['n' => count($finished), 'of' => $quiz->attempts])
                : get_string('unlimitedused', 'local_siraj', count($finished))];
        if ($quiz->timeclose) {
            $rules[] = ['label' => get_string($closed ? 'closed' : 'closes', 'local_siraj'),
                'value' => userdate($quiz->timeclose, get_string('strftimedaydatetime', 'langconfig'))];
        }
        if ($best !== null && quiz_has_grades($quiz)) {
            $rules[] = ['label' => get_string('bestgrade', 'local_siraj'),
                'value' => quiz_format_grade($quiz, $best) . ' / ' . quiz_format_grade($quiz, $quiz->grade)];
        }

        $start = new \moodle_url('/mod/quiz/startattempt.php', ['cmid' => $cm->id, 'sesskey' => sesskey()]);
        if ($inprogress) {
            $action = ['text' => get_string('continueattempt', 'local_siraj'), 'url' => (new \moodle_url('/mod/quiz/attempt.php',
                ['attempt' => $inprogress[0]->id, 'cmid' => $cm->id]))->out(false), 'primary' => true];
        } else if (!$nomore && !$closed && has_capability('mod/quiz:attempt', \context_module::instance($cm->id))) {
            $action = ['text' => get_string($finished ? 'tryagain' : 'startquiz', 'local_siraj'), 'url' => $start->out(false),
                'primary' => true, 'post' => true];
        } else {
            $action = null;
        }
        return [
            'intro' => $intro, 'rules' => $rules, 'action' => $action,
            'state' => get_string($inprogress ? 'qs_inprogress' : ($finished ? 'qs_done' : 'qs_new'), 'local_siraj'),
            'chip' => $inprogress ? 'amber' : ($finished ? 'green' : ''),
            'reviewurl' => $last ? (new \moodle_url('/mod/quiz/review.php', ['attempt' => $last->id, 'cmid' => $cm->id]))->out(false) : null,
            'nomore' => $nomore && !$inprogress, 'closed' => $closed && !$inprogress,
        ];
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        $cm = $this->cm;
        $steps = $this->p->steps();
        $index = 0;
        foreach ($steps as $i => $s) {
            if ($s->id == $cm->id) {
                $index = $i;
            }
        }
        $prev = $steps[$index - 1] ?? null;
        $next = $steps[$index + 1] ?? null;
        [$total, $done] = $this->p->totals();
        $pct = $total ? (int)round($done / $total * 100) : 0;

        $tracking = $this->p->completion->is_enabled($cm);
        $isdone = $this->p->is_done($cm);
        $section = $this->p->modinfo->get_section_info($cm->sectionnum);
        $courseurl = (new \moodle_url('/local/siraj/course.php', ['id' => $this->p->course->id]))->out(false);
        $formurl = (new \moodle_url('/local/siraj/learn.php', ['cmid' => $cm->id]))->out(false);

        // Course outline beside the lesson: every step by section, with where you are and what is done.
        $outline = [];
        // The outline also lists restricted activities (locked, with the reason); Previous / Next skip them.
        $all = [];
        foreach ($this->p->modinfo->get_section_info_all() as $sec) {
            if (!$sec->uservisible || $sec->is_delegated()) {
                continue;
            }
            foreach ($this->p->modinfo->sections[$sec->section] ?? [] as $id) {
                $x = $this->p->modinfo->cms[$id];
                if ($x->url && !$x->deletioninprogress && ($x->uservisible || $x->is_visible_on_course_page())) {
                    $all[] = $x;
                }
            }
        }
        foreach ($all as $s) {
            if (!$s->uservisible) {
                $key = $s->sectionnum;
                $outline[$key] = $outline[$key] ?? ['name' => get_section_name($this->p->course,
                    $this->p->modinfo->get_section_info($key)), 'items' => [], 'done' => 0, 'total' => 0, 'current' => false];
                $outline[$key]['items'][] = ['name' => $s->get_formatted_name(['escape' => false]), 'url' => null,
                    'type' => progress::lock_reason($s, $this->p->course), 'done' => false, 'current' => false, 'locked' => true,
                    'icon' => icons::svg(progress::icon($s->modname))];
                $outline[$key]['total'] += $this->p->completion->is_enabled($s) ? 1 : 0;
                continue;
            }
            $key = $s->sectionnum;
            if (!isset($outline[$key])) {
                $outline[$key] = ['name' => get_section_name($this->p->course, $this->p->modinfo->get_section_info($key)),
                    'items' => [], 'done' => 0, 'total' => 0, 'current' => false];
            }
            $tracked = $this->p->completion->is_enabled($s);
            $sdone = $tracked && $this->p->is_done($s);
            $outline[$key]['items'][] = ['name' => $s->get_formatted_name(['escape' => false]), 'url' => progress::open_url($s),
                'type' => get_string('modulename', $s->modname), 'done' => $sdone, 'current' => $s->id == $cm->id,
                'icon' => icons::svg(progress::icon($s->modname))];
            $outline[$key]['total'] += $tracked ? 1 : 0;
            $outline[$key]['done'] += $sdone ? 1 : 0;
            $outline[$key]['current'] = $outline[$key]['current'] || $s->id == $cm->id;
        }
        // Reading time for text steps (pages and book chapters), about 200 words a minute.
        $body = $this->body();
        $text = $body['reading'] ?? ($body['book']['content'] ?? '');
        $minutes = $text !== '' ? max(1, (int)round(count(preg_split('/\s+/u', trim(strip_tags($text)))) / 200)) : 0;

        return [
            'outline' => array_values($outline),
            'readtime' => $minutes ? get_string('minread', 'local_siraj', $minutes) : '',
            'icon_list' => icons::svg('list'),
            'cmid' => $cm->id,
            'name' => $cm->get_formatted_name(['escape' => false]),
            'coursename' => format_string($this->p->course->fullname, true,
                ['context' => \context_course::instance($this->p->course->id), 'escape' => false]),
            'sectionname' => get_section_name($this->p->course, $section),
            'type' => get_string('modulename', $cm->modname),
            'position' => get_string('position', 'local_siraj', (object)['n' => $index + 1, 'total' => count($steps)]),
            'steppct' => count($steps) ? (int)round(($index + 1) / count($steps) * 100) : 0,
            'progress' => $pct,
            'progresstext' => get_string('doneoftotal', 'local_siraj', (object)['done' => $done, 'total' => $total]),
            'body' => $body,
            'manual' => $tracking && $cm->completion == COMPLETION_TRACKING_MANUAL,
            'manualquiet' => in_array($cm->modname, ['assign', 'quiz'], true),
            'automatic' => $tracking && $cm->completion == COMPLETION_TRACKING_AUTOMATIC,
            'isdone' => $isdone,
            'formurl' => $formurl,
            'sesskey' => sesskey(),
            'prevurl' => $prev ? progress::open_url($prev) : null,
            'prevname' => $prev ? $prev->get_formatted_name(['escape' => false]) : '',
            'nexturl' => $next ? progress::open_url($next) : null,
            'nextname' => $next ? $next->get_formatted_name(['escape' => false]) : '',
            'courseurl' => $courseurl,
            'icon_arrow' => icons::svg('arrow-right'),
            'icon_back' => icons::svg('arrow-left'),
            'icon_check' => icons::svg('check'),
            'icon_external' => icons::svg('external'),
            'icon_copy' => icons::svg('copy'),
            'icon_download' => icons::svg('download'),
            'icon_x' => icons::svg('x'),
            'icon_sparkles' => icons::svg('sparkles'), 'aiready' => ai::available(),
            'icon_award' => icons::svg('award'),
            'icon_calendar' => icons::svg('calendar'),
            'icon_expand' => icons::svg('panel'),
            'icon_search' => icons::svg('search'),
            'icon_message' => icons::svg('message'),
            'icon_folder' => icons::svg('folder'),
            'icon_lock' => icons::svg('lock'),
            'icon_plus' => icons::svg('plus'),
        ];
    }
}
