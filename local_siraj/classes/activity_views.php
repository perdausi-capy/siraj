<?php
/**
 * Siraj's own views of the activity types Learning mode used to hand over to Moodle's pages: feedback, forum,
 * glossary, choice, folder, BigBlueButton and external tools are shown natively; lesson, wiki, workshop, database and
 * content packages run their Moodle pages inside the player stage (the theme hides its shell inside a frame).
 * Everything is read and written through each module's own functions, so completion, events, logs and reports stay
 * Moodle's.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Bodies and actions for the activity types above.
 */
class activity_views {
    /** Types shown by Siraj itself. */
    const NATIVE = ['feedback', 'forum', 'glossary', 'choice', 'folder', 'bigbluebuttonbn', 'lti'];
    /** Types whose Moodle page runs inside the player stage. */
    const STAGED = ['lesson', 'wiki', 'workshop', 'data', 'imscp'];
    /** Forum posts and glossary entries per page. */
    const PERPAGE = 20;

    /** @var \cm_info */
    protected $cm;
    /** @var \stdClass */
    protected $course;
    /** @var \stdClass */
    protected $instance;
    /** @var \context_module */
    protected $context;
    /** @var string */
    protected $intro;

    /**
     * Constructor.
     *
     * @param \cm_info $cm
     * @param \stdClass $course
     * @param \stdClass $instance the activity's own record
     * @param string $intro formatted description
     */
    public function __construct(\cm_info $cm, \stdClass $course, \stdClass $instance, string $intro) {
        global $CFG;
        if ($cm->modname === 'feedback') {
            require_once($CFG->dirroot . '/mod/feedback/lib.php');
        }
        $this->cm = $cm;
        $this->course = $course;
        $this->instance = $instance;
        $this->context = \context_module::instance($cm->id);
        $this->intro = $intro;
    }

    /**
     * Does this class handle the type?
     *
     * @param string $modname
     * @return bool
     */
    public static function handles(string $modname): bool {
        return in_array($modname, self::NATIVE, true) || in_array($modname, self::STAGED, true);
    }

    /**
     * Learning mode URL for this activity, with extra parameters.
     *
     * @param array $params
     * @return string
     */
    protected function url(array $params = []): string {
        return (new \moodle_url('/local/siraj/learn.php', ['cmid' => $this->cm->id] + $params))->out(false);
    }

    /**
     * Record the view the way the activity's own view.php does (logs, "view" completion).
     */
    public function record_view(): void {
        global $CFG;
        $i = $this->instance;
        switch ($this->cm->modname) {
            case 'feedback':
                $fc = new \mod_feedback_completion($i, $this->cm, $this->course->id);
                $fc->trigger_module_viewed();
                $fc->set_module_viewed();
                break;
            case 'forum':
                require_once($CFG->dirroot . '/mod/forum/lib.php');
                forum_view($i, $this->course, $this->cm, $this->context);
                break;
            case 'glossary':
                require_once($CFG->dirroot . '/mod/glossary/lib.php');
                glossary_view($i, $this->course, $this->cm, $this->context, 'letter');
                break;
            case 'choice':
                require_once($CFG->dirroot . '/mod/choice/lib.php');
                choice_view($i, $this->course, $this->cm, $this->context);
                break;
            case 'folder':
                require_once($CFG->dirroot . '/mod/folder/lib.php');
                folder_view($i, $this->course, $this->cm, $this->context);
                break;
            case 'lti':
                // The launch page records the view itself.
                break;
            case 'bigbluebuttonbn':
                (new \completion_info($this->course))->set_module_viewed($this->cm);
                break;
        }
    }

    /**
     * The activity body for Learning mode.
     *
     * @return array
     */
    public function body(): array {
        $m = $this->cm->modname;
        if (in_array($m, self::STAGED, true)) {
            return ['embed' => [
                'kind' => 'moodle', 'src' => $this->cm->url->out(false), 'allow' => '/mod/' . $m . '/',
                'title' => $this->cm->get_formatted_name(['escape' => false]), 'intro' => '',
            ]];
        }
        if ($m === 'lti') {
            // Tools set to open in a new window (often because they refuse to load inside a frame) get a launch card.
            global $CFG;
            require_once($CFG->dirroot . '/mod/lti/locallib.php');
            $container = (int)$this->instance->launchcontainer;
            if (!$container || $container === LTI_LAUNCH_CONTAINER_DEFAULT) {
                $container = (int)(lti_get_type_config_by_instance($this->instance)['launchcontainer'] ?? 0);
            }
            if (in_array($container, [LTI_LAUNCH_CONTAINER_WINDOW, LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW], true)) {
                $newtab = $container === LTI_LAUNCH_CONTAINER_WINDOW;
                return ['launch' => [
                    'url' => (new \moodle_url('/mod/lti/launch.php', ['id' => $this->cm->id, 'triggerview' => 1]))->out(false),
                    'type' => get_string('modulename', 'lti'), 'desc' => get_string('lti_newtab', 'local_siraj'),
                    'button' => get_string('openactivity', 'local_siraj', get_string('modulename', 'lti')),
                    'icon' => icons::svg('external'), 'intro' => $this->intro, 'newtab' => $newtab,
                ]];
            }
            return ['embed' => [
                'kind' => 'lti', 'allow' => '/mod/lti/',
                'src' => (new \moodle_url('/mod/lti/launch.php', ['id' => $this->cm->id, 'triggerview' => 1]))->out(false),
                'title' => $this->cm->get_formatted_name(['escape' => false]), 'intro' => $this->intro,
            ]];
        }
        return [$m => $this->$m()];
    }

    // ---------- Feedback ----------

    /**
     * Feedback: the questionnaire itself, or a thank-you once it is sent.
     *
     * @return array
     */
    protected function feedback(): array {
        $fc = new \mod_feedback_completion($this->instance, $this->cm, $this->course->id);
        $f = $this->instance;
        $base = ['intro' => $this->intro, 'action' => $this->url(), 'sesskey' => sesskey()];
        if (!$fc->can_complete()) {
            return $base + ['message' => get_string('fb_cannot', 'local_siraj')];
        }
        if (!$fc->is_open()) {
            return $base + ['message' => get_string('fb_closed', 'local_siraj')];
        }
        if (!$fc->can_submit()) {
            $after = $f->page_after_submit ? format_text(file_rewrite_pluginfile_urls($f->page_after_submit, 'pluginfile.php',
                $this->context->id, 'mod_feedback', 'page_after_submit', 0), $f->page_after_submitformat,
                ['context' => $this->context]) : '';
            return $base + ['done' => true, 'after' => $after];
        }
        $questions = [];
        $n = 0;
        foreach ($fc->get_items() as $item) {
            $q = $this->feedback_item($item, $n);
            if ($q === false) {
                // A type Siraj does not draw (captcha, a plugin type): use Moodle's form for this one.
                return $base + ['fallback' => (new \moodle_url('/mod/feedback/complete.php', ['id' => $this->cm->id]))->out(false)];
            }
            if ($q) {
                $questions[] = $q;
            }
        }
        return $base + [
            'questions' => $questions, 'form' => true,
            'anonymous' => $f->anonymous == FEEDBACK_ANONYMOUS_YES,
        ];
    }

    /**
     * One feedback item for the template.
     *
     * @param \stdClass $item
     * @param int $n running question number
     * @return array|null|false null to skip, false when Siraj cannot draw it
     */
    protected function feedback_item(\stdClass $item, int &$n) {
        $key = $item->typ . '_' . $item->id;
        $label = format_text($item->name, FORMAT_HTML, ['context' => $this->context, 'para' => false]);
        $q = ['key' => $key, 'label' => $label, 'required' => !empty($item->required), 'id' => 'fb' . $item->id];
        switch ($item->typ) {
            case 'pagebreak':
            case 'info':
                return null;
            case 'label':
                $text = file_rewrite_pluginfile_urls($item->presentation, 'pluginfile.php', $this->context->id, 'mod_feedback',
                    'item', $item->id);
                return ['text' => format_text($text, FORMAT_HTML, ['context' => $this->context, 'noclean' => true])];
            case 'textfield':
                [, $max] = array_pad(explode('|', $item->presentation), 2, 255);
                return $q + ['n' => ++$n, 'textfield' => true, 'maxlength' => (int)$max ?: 255];
            case 'textarea':
                return $q + ['n' => ++$n, 'textarea' => true];
            case 'numeric':
                [$min, $max] = array_pad(explode('|', $item->presentation), 2, '');
                return $q + ['n' => ++$n, 'numeric' => true, 'min' => $min, 'max' => $max];
            case 'multichoice':
            case 'multichoicerated':
                [$sub, $rest] = array_pad(explode('>>>>>', $item->presentation, 2), 2, '');
                $rest = preg_replace('/<<<<<.*$/s', '', $rest);
                $options = [];
                foreach (explode('|', $rest) as $i => $text) {
                    if ($item->typ === 'multichoicerated') {
                        $text = preg_replace('/^[^#]*####/', '', $text);
                    }
                    $options[] = ['value' => $i + 1, 'text' => format_string(trim($text), true, ['context' => $this->context, 'escape' => false]),
                        'id' => 'fb' . $item->id . '_' . ($i + 1)];
                }
                $type = $sub === 'c' ? 'checkbox' : ($sub === 'd' ? 'select' : 'radio');
                // Short answer scales (1 to 5, rated) read best as a row of pills.
                $scale = $type === 'radio' && count($options) <= 7 && max(array_map(fn($o) => \core_text::strlen($o['text']), $options)) <= 14;
                return $q + ['n' => ++$n, 'choices' => true, $type => true, 'options' => $options, 'scale' => $scale,
                    'multiple' => $type === 'checkbox'];
        }
        return false;
    }

    /**
     * Save a submitted feedback.
     *
     * @return \moodle_url where to go next
     */
    protected function act_feedback(): \moodle_url {
        $fc = new \mod_feedback_completion($this->instance, $this->cm, $this->course->id);
        if (!$fc->can_complete() || !$fc->is_open() || !$fc->can_submit()) {
            throw new \moodle_exception('nopermissions', 'error', '', 'feedback');
        }
        $data = new \stdClass();
        $missing = false;
        foreach ($fc->get_items(true) as $item) {
            $key = $item->typ . '_' . $item->id;
            $multi = $item->typ === 'multichoice' && strpos($item->presentation, 'c>>>>>') === 0;
            $value = $multi ? optional_param_array($key, [], PARAM_INT) : optional_param($key, '', PARAM_RAW_TRIMMED);
            if ($item->typ === 'numeric' && $value !== '') {
                $value = unformat_float($value);
            }
            if (!empty($item->required) && ($value === '' || $value === [] || $value === null)) {
                $missing = true;
            }
            $data->$key = $value;
        }
        if ($missing) {
            \core\notification::error(get_string('fb_required', 'local_siraj'));
            return new \moodle_url($this->url());
        }
        $fc->save_response_tmp($data);
        $fc->save_response();
        \core\notification::success(get_string('fb_thanks', 'local_siraj'));
        return new \moodle_url($this->url());
    }

    // ---------- Forum ----------

    /**
     * Forum: the discussion list, or one discussion when ?d= is given.
     *
     * @return array
     */
    protected function forum(): array {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        $forum = $this->instance;
        $d = optional_param('d', 0, PARAM_INT);
        $base = ['intro' => $this->intro, 'action' => $this->url(), 'sesskey' => sesskey(), 'cmid' => $this->cm->id,
            'listurl' => $this->url()];
        $groupmode = groups_get_activity_groupmode($this->cm, $this->course);
        $group = $groupmode ? groups_get_activity_group($this->cm, true) : -1;

        if ($d) {
            $discussion = $DB->get_record('forum_discussions', ['id' => $d, 'forum' => $forum->id], '*', MUST_EXIST);
            if (!forum_user_can_see_discussion($forum, $discussion, $this->context)) {
                throw new \moodle_exception('noviewdiscussionspermission', 'forum');
            }
            $posts = $DB->get_records('forum_posts', ['discussion' => $discussion->id], 'created ASC');
            $users = user_get_users_by_id(array_unique(array_column($posts, 'userid')));
            $items = [];
            foreach ($posts as $p) {
                if (!forum_user_can_see_post($forum, $discussion, $p, null, $this->cm)) {
                    continue;
                }
                $items[] = $this->forum_post($p, $users[$p->userid] ?? null, $p->id == $discussion->firstpost);
            }
            $canreply = forum_user_can_post($forum, $discussion, null, $this->cm, $this->course, $this->context);
            return $base + ['thread' => [
                'id' => $discussion->id, 'name' => format_string($discussion->name, true, ['context' => $this->context, 'escape' => false]),
                'posts' => $items, 'count' => count($items) - 1, 'canreply' => $canreply,
                'parent' => $discussion->firstpost,
            ]];
        }

        $page = optional_param('page', 0, PARAM_INT);
        $where = 'forum = :forum';
        $params = ['forum' => $forum->id];
        if ($group > 0) {
            $where .= ' AND (groupid = :g OR groupid = -1)';
            $params['g'] = $group;
        }
        $total = $DB->count_records_select('forum_discussions', $where, $params);
        $rows = $DB->get_records_select('forum_discussions', $where, $params, 'pinned DESC, timemodified DESC', '*',
            $page * self::PERPAGE, self::PERPAGE);
        $topics = [];
        if ($rows) {
            [$in, $ip] = $DB->get_in_or_equal(array_keys($rows));
            $counts = $DB->get_records_sql_menu("SELECT discussion, COUNT(*) - 1 FROM {forum_posts} WHERE discussion $in
                GROUP BY discussion", $ip);
            $firsts = $DB->get_records_list('forum_posts', 'id', array_column($rows, 'firstpost'));
            $users = user_get_users_by_id(array_unique(array_merge(array_column($rows, 'userid'),
                array_column($rows, 'usermodified'))));
            foreach ($rows as $r) {
                if (!forum_user_can_see_discussion($forum, $r, $this->context)) {
                    continue;
                }
                $first = $firsts[$r->firstpost] ?? null;
                $author = $users[$r->userid] ?? null;
                $replies = (int)($counts[$r->id] ?? 0);
                $topics[] = [
                    'url' => $this->url(['d' => $r->id]),
                    'name' => format_string($r->name, true, ['context' => $this->context, 'escape' => false]),
                    'excerpt' => $first ? shorten_text(html_to_text(format_text($first->message, $first->messageformat,
                        ['context' => $this->context]), 0, false), 160) : '',
                    'author' => $author ? fullname($author) : '',
                    'initials' => $author ? team::initials($author) : '',
                    'when' => userdate($r->timemodified, get_string('strftimedatetimeshort', 'langconfig')),
                    'replies' => $replies, 'noreplies' => $replies == 0,
                    'replytext' => $replies == 1 ? get_string('forum_onereply', 'local_siraj') :
                        get_string('forum_nreplies', 'local_siraj', $replies),
                    'pinned' => !empty($r->pinned),
                ];
            }
        }
        $canstart = forum_user_can_post_discussion($forum, $group > 0 ? $group : null, -1, $this->cm, $this->context);
        $isnews = $forum->type === 'news';
        return $base + ['list' => [
            'topics' => $topics, 'empty' => !$topics, 'canstart' => $canstart && $forum->type !== 'single',
            'isnews' => $isnews,
            'newlabel' => get_string($isnews ? 'forum_newannouncement' : 'forum_newtopic', 'local_siraj'),
            'prevurl' => $page > 0 ? $this->url(['page' => $page - 1]) : null,
            'nexturl' => ($page + 1) * self::PERPAGE < $total ? $this->url(['page' => $page + 1]) : null,
        ]];
    }

    /**
     * One forum post for the template.
     *
     * @param \stdClass $p
     * @param \stdClass|null $user
     * @param bool $first
     * @return array
     */
    protected function forum_post(\stdClass $p, ?\stdClass $user, bool $first): array {
        $text = file_rewrite_pluginfile_urls($p->message, 'pluginfile.php', $this->context->id, 'mod_forum', 'post', $p->id);
        $files = [];
        if ($p->attachment) {
            foreach (get_file_storage()->get_area_files($this->context->id, 'mod_forum', 'attachment', $p->id, 'filename',
                    false) as $f) {
                $files[] = ['name' => $f->get_filename(), 'size' => display_size($f->get_filesize()),
                    'url' => \moodle_url::make_pluginfile_url($this->context->id, 'mod_forum', 'attachment', $p->id,
                        $f->get_filepath(), $f->get_filename(), true)->out(false)];
            }
        }
        return [
            'id' => $p->id, 'first' => $first,
            'subject' => format_string($p->subject, true, ['context' => $this->context, 'escape' => false]),
            'message' => format_text($text, $p->messageformat, ['context' => $this->context]),
            'author' => $user ? fullname($user) : '', 'initials' => $user ? team::initials($user) : '',
            'when' => userdate($p->created, get_string('strftimedatetimeshort', 'langconfig')),
            'files' => $files, 'hasfiles' => !empty($files),
        ];
    }

    /**
     * Post a reply or start a discussion, then fire the same events and completion update as Moodle's post.php.
     *
     * @param string $action 'reply' or 'topic'
     * @return \moodle_url
     */
    protected function act_forum(string $action): \moodle_url {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        $forum = $this->instance;
        $message = trim(optional_param('message', '', PARAM_RAW));
        $message = $message === '' ? '' : '<p>' . nl2br(s($message)) . '</p>';
        if ($message === '') {
            \core\notification::error(get_string('forum_empty', 'local_siraj'));
            return new \moodle_url($this->url($action === 'reply' ? ['d' => required_param('d', PARAM_INT)] : []));
        }
        $groupmode = groups_get_activity_groupmode($this->cm, $this->course);
        $group = $groupmode ? groups_get_activity_group($this->cm, true) : -1;

        if ($action === 'reply') {
            $discussion = $DB->get_record('forum_discussions', ['id' => required_param('d', PARAM_INT), 'forum' => $forum->id],
                '*', MUST_EXIST);
            if (!forum_user_can_post($forum, $discussion, null, $this->cm, $this->course, $this->context)) {
                throw new \moodle_exception('nopostforum', 'forum');
            }
            $parent = $DB->get_record('forum_posts', ['id' => $discussion->firstpost], '*', MUST_EXIST);
            $post = (object)['discussion' => $discussion->id, 'parent' => $parent->id, 'course' => $this->course->id,
                'forum' => $forum->id, 'subject' => get_string('re', 'forum') . ' ' . $discussion->name,
                'message' => $message, 'messageformat' => FORMAT_HTML, 'messagetrust' => 0, 'itemid' => 0,
                'mailnow' => 0];
            $post->id = forum_add_new_post($post, null);
            $event = \mod_forum\event\post_created::create(['context' => $this->context, 'objectid' => $post->id,
                'other' => ['discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type]]);
            $event->add_record_snapshot('forum_discussions', $discussion);
            $event->trigger();
            $goto = ['d' => $discussion->id];
        } else {
            if (!forum_user_can_post_discussion($forum, $group > 0 ? $group : null, -1, $this->cm, $this->context)) {
                throw new \moodle_exception('nopostforum', 'forum');
            }
            $name = trim(required_param('subject', PARAM_TEXT));
            if ($name === '') {
                \core\notification::error(get_string('forum_nosubject', 'local_siraj'));
                return new \moodle_url($this->url(['compose' => 1]));
            }
            $discussion = (object)['course' => $this->course->id, 'forum' => $forum->id, 'name' => $name,
                'message' => $message, 'messageformat' => FORMAT_HTML, 'messagetrust' => 0, 'mailnow' => 0,
                'groupid' => $group > 0 ? $group : -1, 'timestart' => 0, 'timeend' => 0, 'pinned' => 0, 'itemid' => 0];
            $discussion->id = forum_add_discussion($discussion, null);
            $event = \mod_forum\event\discussion_created::create(['context' => $this->context,
                'objectid' => $discussion->id, 'other' => ['forumid' => $forum->id]]);
            $event->add_record_snapshot('forum_discussions', $DB->get_record('forum_discussions', ['id' => $discussion->id]));
            $event->trigger();
            $goto = ['d' => $discussion->id];
        }
        $completion = new \completion_info($this->course);
        if ($completion->is_enabled($this->cm) && ($forum->completionreplies || $forum->completionposts
                || $forum->completiondiscussions)) {
            $completion->update_state($this->cm, COMPLETION_COMPLETE);
        }
        \core\notification::success(get_string('forum_posted', 'local_siraj'));
        return new \moodle_url($this->url($goto));
    }

    // ---------- Glossary ----------

    /**
     * Glossary: searchable entries with an A-Z filter.
     *
     * @return array
     */
    protected function glossary(): array {
        global $DB, $USER;
        $g = $this->instance;
        $q = trim(optional_param('q', '', PARAM_TEXT));
        $letter = optional_param('letter', '', PARAM_ALPHA);
        $page = optional_param('page', 0, PARAM_INT);
        $where = 'glossaryid = :g AND (approved = 1 OR userid = :me)';
        $params = ['g' => $g->id, 'me' => $USER->id];
        if ($q !== '') {
            $where .= ' AND (' . $DB->sql_like('concept', ':q1', false) . ' OR ' . $DB->sql_like('definition', ':q2', false) . ')';
            $params['q1'] = $params['q2'] = '%' . $DB->sql_like_escape($q) . '%';
        } else if ($letter !== '') {
            $where .= ' AND ' . $DB->sql_like('concept', ':l', false);
            $params['l'] = $DB->sql_like_escape($letter) . '%';
        }
        $total = $DB->count_records_select('glossary_entries', $where, $params);
        $rows = $DB->get_records_select('glossary_entries', $where, $params, 'concept ASC', '*', $page * self::PERPAGE,
            self::PERPAGE);
        $entries = [];
        foreach ($rows as $e) {
            $text = file_rewrite_pluginfile_urls($e->definition, 'pluginfile.php', $this->context->id, 'mod_glossary', 'entry',
                $e->id);
            $entries[] = [
                'concept' => format_string($e->concept, true, ['context' => $this->context, 'escape' => false]),
                'definition' => format_text($text, $e->definitionformat, ['context' => $this->context]),
                'pending' => !$e->approved,
            ];
        }
        $any = $q !== '' || $letter !== '' || $DB->record_exists('glossary_entries', ['glossaryid' => $g->id]);
        $letters = [];
        if ($any) {
            $used = array_flip(array_map(fn($c) => \core_text::strtoupper(\core_text::substr(trim($c), 0, 1)),
                $DB->get_fieldset_select('glossary_entries', 'concept', 'glossaryid = ? AND approved = 1', [$g->id])));
            foreach (range('A', 'Z') as $l) {
                $letters[] = ['letter' => $l, 'url' => $this->url(['letter' => $l]), 'current' => $letter === $l,
                    'disabled' => !isset($used[$l])];
            }
        }
        $canadd = has_capability('mod/glossary:write', $this->context);
        $keep = array_filter(['q' => $q, 'letter' => $letter]);
        return [
            'intro' => $this->intro, 'entries' => $entries, 'hasentries' => !empty($entries),
            // A glossary with no entries at all is being used as a page (its description is the content).
            'asintro' => !$any, 'browse' => $any,
            'q' => $q, 'letters' => $letters, 'allurl' => $this->url(), 'all' => $q === '' && $letter === '',
            'searchurl' => (new \moodle_url('/local/siraj/learn.php'))->out(false), 'cmid' => $this->cm->id,
            'total' => $total,
            'addurl' => $canadd ? (new \moodle_url('/mod/glossary/edit.php', ['cmid' => $this->cm->id]))->out(false) : null,
            'prevurl' => $page > 0 ? $this->url($keep + ['page' => $page - 1]) : null,
            'nexturl' => ($page + 1) * self::PERPAGE < $total ? $this->url($keep + ['page' => $page + 1]) : null,
        ];
    }

    // ---------- Choice ----------

    /**
     * Choice: the question, your vote, and results when the teacher shows them.
     *
     * @return array
     */
    protected function choice(): array {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/mod/choice/lib.php');
        $c = $this->instance;
        $now = time();
        $options = $DB->get_records('choice_options', ['choiceid' => $c->id], 'id');
        $mine = array_column($DB->get_records('choice_answers', ['choiceid' => $c->id, 'userid' => $USER->id]), 'optionid');
        $counts = $DB->get_records_sql_menu('SELECT optionid, COUNT(*) FROM {choice_answers} WHERE choiceid = ?
            GROUP BY optionid', [$c->id]);
        $voters = (int)$DB->count_records_sql('SELECT COUNT(DISTINCT userid) FROM {choice_answers} WHERE choiceid = ?', [$c->id]);
        $notopen = $c->timeopen && $c->timeopen > $now;
        $closed = $c->timeclose && $c->timeclose < $now;
        $answered = !empty($mine);
        $canvote = has_capability('mod/choice:choose', $this->context) && !$notopen && !$closed
            && (!$answered || $c->allowupdate);
        $showresults = $c->showresults == CHOICE_SHOWRESULTS_ALWAYS
            || ($c->showresults == CHOICE_SHOWRESULTS_AFTER_ANSWER && $answered)
            || ($c->showresults == CHOICE_SHOWRESULTS_AFTER_CLOSE && $closed);
        $items = [];
        $max = max(1, ...array_values($counts ?: [0]));
        foreach ($options as $o) {
            $count = (int)($counts[$o->id] ?? 0);
            $full = $c->limitanswers && $o->maxanswers && $count >= $o->maxanswers && !in_array($o->id, $mine);
            $items[] = [
                'id' => $o->id, 'text' => format_string($o->text, true, ['context' => $this->context, 'escape' => false]),
                'checked' => in_array($o->id, $mine), 'disabled' => $full || !$canvote, 'full' => $full,
                'count' => $count, 'pct' => $voters ? (int)round($count / $voters * 100) : 0,
                'bar' => (int)round($count / $max * 100),
            ];
        }
        return [
            'intro' => $this->intro, 'options' => $items, 'multiple' => !empty($c->allowmultiple),
            'canvote' => $canvote, 'answered' => $answered, 'showresults' => $showresults, 'voters' => $voters,
            'notopen' => $notopen ? userdate($c->timeopen, get_string('strftimedatetimeshort', 'langconfig')) : null,
            'closed' => $closed, 'canchange' => $answered && $c->allowupdate && $canvote,
            'closes' => !$closed && $c->timeclose ? userdate($c->timeclose, get_string('strftimedatetimeshort', 'langconfig')) : null,
            'action' => $this->url(), 'sesskey' => sesskey(),
        ];
    }

    /**
     * Save a vote.
     *
     * @return \moodle_url
     */
    protected function act_choice(): \moodle_url {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/mod/choice/lib.php');
        require_capability('mod/choice:choose', $this->context);
        $c = $this->instance;
        $answer = $c->allowmultiple ? optional_param_array('answer', [], PARAM_INT) : optional_param('answer', 0, PARAM_INT);
        if (empty($answer)) {
            \core\notification::error(get_string('choice_pick', 'local_siraj'));
        } else {
            choice_user_submit_response($answer, $c, $USER->id, $this->course, $this->cm);
            \core\notification::success(get_string('choice_saved', 'local_siraj'));
        }
        return new \moodle_url($this->url());
    }

    // ---------- Folder ----------

    /**
     * Folder: its files as a list, with subfolders as headings.
     *
     * @return array
     */
    protected function folder(): array {
        $f = $this->instance;
        $groups = [];
        foreach (get_file_storage()->get_area_files($this->context->id, 'mod_folder', 'content', 0, 'filepath, filename',
                false) as $file) {
            $path = trim($file->get_filepath(), '/');
            $groups[$path] ??= ['path' => $path, 'files' => []];
            $mime = $file->get_mimetype();
            $groups[$path]['files'][] = [
                'name' => $file->get_filename(), 'size' => display_size($file->get_filesize()),
                'type' => \core_text::strtoupper(pathinfo($file->get_filename(), PATHINFO_EXTENSION)),
                'url' => \moodle_url::make_pluginfile_url($this->context->id, 'mod_folder', 'content', $f->revision,
                    $file->get_filepath(), $file->get_filename())->out(false),
                'download' => \moodle_url::make_pluginfile_url($this->context->id, 'mod_folder', 'content', $f->revision,
                    $file->get_filepath(), $file->get_filename(), true)->out(false),
                'icon' => icons::svg(strpos($mime, 'image/') === 0 ? 'grid' : 'file'),
            ];
        }
        $count = array_sum(array_map(fn($g) => count($g['files']), $groups));
        return ['intro' => $this->intro, 'groups' => array_values($groups), 'empty' => !$count,
            'count' => get_string($count == 1 ? 'folder_onefile' : 'folder_nfiles', 'local_siraj', $count)];
    }

    // ---------- BigBlueButton ----------

    /**
     * Live session: when it runs and a join button that opens the BigBlueButton room in a new tab. Follows the
     * activity's own rules: recordings-only rooms can't be joined, and when learners must wait for a moderator the
     * button only appears once the session is running.
     *
     * @return array
     */
    protected function bigbluebuttonbn(): array {
        $b = $this->instance;
        $bbb = \mod_bigbluebuttonbn\instance::get_from_cmid($this->cm->id);
        $fmt = get_string('strftimedatetimeshort', 'langconfig');
        $state = 'live_open';
        if ($bbb->before_start_time()) {
            $state = 'live_soon';
        } else if ($bbb->has_ended()) {
            $state = 'live_over';
        }
        $canjoin = $state === 'live_open' && $bbb->can_join();
        if ($canjoin && $bbb->user_must_wait_to_join()) {
            try {
                $canjoin = (new \mod_bigbluebuttonbn\meeting($bbb))->is_running();
            } catch (\Throwable $e) {
                $canjoin = false;
            }
            $state = $canjoin ? $state : 'live_wait';
        }
        return [
            'intro' => $this->intro,
            'opens' => $b->openingtime ? userdate($b->openingtime, $fmt) : null,
            'closes' => $b->closingtime ? userdate($b->closingtime, $fmt) : null,
            'state' => get_string($state, 'local_siraj'),
            'chip' => ['live_open' => 'green', 'live_soon' => 'amber', 'live_wait' => 'amber'][$state] ?? '',
            'waiting' => $state === 'live_wait',
            'canjoin' => $canjoin, 'url' => $bbb->get_join_url()->out(false),
            'manageurl' => $bbb->is_moderator() || $bbb->is_admin()
                ? (new \moodle_url('/mod/bigbluebuttonbn/view.php', ['id' => $this->cm->id, 'siraj' => 'classic']))->out(false) : null,
            'icon' => icons::svg('users'), 'icon_external' => icons::svg('external'),
        ];
    }

    // ---------- Actions ----------

    /**
     * Handle a POST from one of the views above.
     *
     * @param string $action
     * @return \moodle_url|null null when the action is not ours
     */
    public function act(string $action): ?\moodle_url {
        $m = $this->cm->modname;
        if ($m === 'feedback' && $action === 'fbsubmit') {
            return $this->act_feedback();
        }
        if ($m === 'forum' && in_array($action, ['reply', 'topic'], true)) {
            return $this->act_forum($action);
        }
        if ($m === 'choice' && $action === 'vote') {
            return $this->act_choice();
        }
        return null;
    }
}
