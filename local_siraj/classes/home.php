<?php
/**
 * Siraj Home: what should this learner do now? Built only from standard Moodle data
 * (enrolments, activity completion, activity due dates, logs, course fields).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Builds the Home page template context for one user.
 */
class home {
    /** @var int Days ahead that count as "due soon". */
    const DUESOON_DAYS = 14;

    /** @var \stdClass */
    protected $user;
    /** @var int */
    protected $now;
    /** @var bool Look up course images (off for dashboards, which don't show them). */
    public $withcovers = true;
    /** @var array|null Completion rows for another user, by cmid. */
    protected $states = null;

    /**
     * Constructor.
     *
     * @param \stdClass $user
     */
    public function __construct(\stdClass $user) {
        $this->user = $user;
        $this->now = time();
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        $courses = $this->courses();
        $mine = array_filter($courses, fn($c) => $c['total'] > 0);

        // The single most important next step: incomplete mandatory first, then earliest due, then most progress.
        $open = array_filter($mine, fn($c) => $c['progress'] < 100);
        usort($open, function($a, $b) {
            return [!$a['mandatory'], $a['due'] ?? PHP_INT_MAX, -$a['progress']]
                <=> [!$b['mandatory'], $b['due'] ?? PHP_INT_MAX, -$b['progress']];
        });
        $hero = $open ? array_shift($open) : null;

        // Due strip: the next most urgent course with a date in the next two weeks.
        $duestrip = null;
        foreach ($open as $c) {
            if ($c['due'] && $c['due'] < $this->now + self::DUESOON_DAYS * DAYSECS) {
                $duestrip = $c;
                break;
            }
        }

        // Pick up where you left off: started, not finished, most recently opened first.
        $continue = array_values(array_filter($mine, fn($c) => $c['progress'] > 0 && $c['progress'] < 100
            && (!$hero || $c['id'] != $hero['id'])));
        usort($continue, fn($a, $b) => $b['lastaccess'] <=> $a['lastaccess']);
        $continue = array_slice($continue, 0, 3);

        $completed = array_values(array_filter($mine, fn($c) => $c['progress'] >= 100));
        usort($completed, fn($a, $b) => $b['completedon'] <=> $a['completedon']);

        $overdue = count(array_filter($mine, fn($c) => $c['progress'] < 100 && $c['due'] && $c['due'] < $this->now));
        $hour = (int)userdate($this->now, '%H', \core_date::get_user_timezone($this->user));
        $greet = $hour < 12 ? 'greet_morning' : ($hour < 17 ? 'greet_afternoon' : 'greet_evening');

        // Concept A: hero slides (next step, due soon, a recommendation), stat strip, photo cards, coming up.
        $rhythm = $this->rhythm();
        $slides = [];
        if ($hero) {
            $slides[] = $this->hero($hero) + ['eyebrow2' => ''];
        }
        if ($duestrip) {
            $slides[] = [
                'eyebrow' => get_string('slide_duesoon', 'local_siraj'),
                'title' => get_string('hero_continue', 'local_siraj', $duestrip['name']),
                'text' => $duestrip['nextname'] ? get_string('next', 'local_siraj', $duestrip['nextname']) : '',
                'meta' => array_values(array_filter([
                    $duestrip['duration'] ? ['icon' => icons::svg('clock'), 'text' => plural::str('minutes', (int)$duestrip['duration'])] : null,
                    ['icon' => icons::svg('calendar'), 'text' => get_string('duedate', 'local_siraj', userdate($duestrip['due'], '%d.%m.%Y'))],
                ])),
                'url' => $duestrip['nexturl'] ?: $duestrip['url'],
                'button' => get_string('continue', 'local_siraj'),
                'photo' => $duestrip['cover'] ?? null,
            ];
        }
        $reco = $this->recommended();
        if ($reco && count($slides) < 3) {
            $r = $reco[0];
            $slides[] = [
                'eyebrow' => get_string('slide_reco', 'local_siraj'),
                'title' => get_string('hero_start', 'local_siraj', $r['name']),
                'text' => $this->row($r)['sub'],
                'meta' => [], 'url' => $r['url'], 'button' => get_string('preview', 'local_siraj'), 'photo' => $r['cover'],
            ];
        }
        foreach ($slides as $n => &$slide) {
            $slide['n'] = $n;
            $slide['first'] = $n === 0;
        }
        unset($slide);

        $inprogress = count(array_filter($mine, fn($c) => $c['progress'] > 0 && $c['progress'] < 100));
        $duesoon = count(array_filter($mine, fn($c) => $c['progress'] < 100 && $c['due'] && $c['due'] >= $this->now
            && $c['due'] < $this->now + self::DUESOON_DAYS * DAYSECS));
        $stats = [
            ['icon' => icons::svg('book'), 'value' => $inprogress, 'label' => get_string('stat_inprogress', 'local_siraj')],
            ['icon' => icons::svg('check-circle'), 'value' => count($completed), 'label' => get_string('stat_completed', 'local_siraj')],
            ['icon' => icons::svg('calendar'), 'value' => $duesoon, 'label' => get_string('stat_duesoon', 'local_siraj')],
            ['icon' => icons::svg('trending'), 'value' => $rhythm['count'] . '/' . $rhythm['goal'], 'label' => get_string('stat_days', 'local_siraj')],
        ];

        // Photo cards: what they are in the middle of, topped up with recommendations.
        $cards = array_map(fn($c) => $this->row($c) + ['inprogress' => true, 'tag' => get_string($c['mandatory'] ? 'mandatory' : 'optional', 'local_siraj')],
            array_slice($continue, 0, 3));
        foreach ($reco as $r) {
            if (count($cards) >= 3) {
                break;
            }
            $cards[] = $this->row($r) + ['inprogress' => false, 'tag' => get_string('slide_reco', 'local_siraj')];
        }

        $upcoming = array_values(array_filter($mine, fn($c) => $c['progress'] < 100 && $c['due']));
        usort($upcoming, fn($a, $b) => $a['due'] <=> $b['due']);
        $upcoming = array_map(fn($c) => [
            'day' => userdate($c['due'], '%d'), 'month' => \core_text::strtoupper(userdate($c['due'], '%b')),
            'name' => $c['name'], 'url' => $c['url'], 'when' => $this->relative($c['due']), 'late' => $c['due'] < $this->now,
        ], array_slice($upcoming, 0, 3));

        return [
            'slides' => $slides, 'hasslides' => !empty($slides), 'manyslides' => count($slides) > 1, 'slidecount' => count($slides),
            'stats' => $stats, 'cards' => $cards, 'hascards' => !empty($cards),
            'upcoming' => $upcoming, 'hasupcoming' => !empty($upcoming),
            'calendarurl' => (new \moodle_url('/calendar/view.php', ['view' => 'upcoming']))->out(false),
            'icon_chevl' => icons::svg('chevron-left'), 'icon_chevr' => icons::svg('chevron-right'),
            'eyebrow' => userdate($this->now, '%A, %d %B %Y') . ($this->user->city ? ' · ' . s($this->user->city) : ''),
            'greeting' => get_string($greet, 'local_siraj', s($this->user->firstname)),
            'subline' => get_string('subline', 'local_siraj'),
            'status' => $overdue ? get_string('status_overdue', 'local_siraj', $overdue) : get_string('status_ontrack', 'local_siraj'),
            'statusclass' => $overdue ? 'amber' : 'green',
            'overdue' => $overdue > 0,
            'hero' => $hero ? $this->hero($hero) : null,
            'noactive' => !$hero,
            'duestrip' => $duestrip ? [
                'name' => $duestrip['name'],
                'text' => get_string('duestrip', 'local_siraj', (object)[
                    'when' => $this->relative($duestrip['due']), 'left' => $duestrip['remaining']]),
                'url' => $duestrip['url'],
            ] : null,
            'continue' => array_map(fn($c) => $this->row($c), $continue),
            'hascontinue' => !empty($continue),
            'recommended' => array_map(fn($c) => $this->row($c), $this->recommended()),
            'hasrecommended' => !empty($this->recommended()),
            'rhythm' => $rhythm,
            'earned' => $completed ? [
                'name' => $completed[0]['name'],
                'date' => userdate($completed[0]['completedon'], '%d.%m.%Y'),
                'url' => $completed[0]['url'],
                'count' => count($completed),
            ] : null,
            'learningurl' => (new \moodle_url('/local/siraj/learning.php'))->out(false),
            'exploreurl' => (new \moodle_url('/local/siraj/explore.php'))->out(false),
            'icon_arrow' => icons::svg('arrow-right'),
            'icon_clock' => icons::svg('clock'),
            'icon_layers' => icons::svg('layers'),
            'icon_calendar' => icons::svg('calendar'),
            'icon_award' => icons::svg('award'),
            'icon_sparkles' => icons::svg('sparkles'), 'aiready' => ai::available(),
            'flame' => icons::flame(),
        ];
    }

    /**
     * Hero card context.
     *
     * @param array $c course data
     * @return array
     */
    protected function hero(array $c): array {
        $last = $c['remaining'] == 1;
        $meta = [];
        if ($c['duration']) {
            $meta[] = ['icon' => icons::svg('clock'), 'text' => plural::str('minutes', (int)$c['duration'])];
        }
        $meta[] = ['icon' => icons::svg('layers'), 'text' => get_string($last ? 'oneleft' : 'nleft', 'local_siraj', $c['remaining'])];
        if ($c['due']) {
            $meta[] = ['icon' => icons::svg('calendar'), 'text' => get_string('duedate', 'local_siraj', userdate($c['due'], '%d.%m.%Y'))];
        }
        return [
            'eyebrow' => get_string($c['mandatory'] ? 'nextstep_mandatory' : 'nextstep', 'local_siraj'),
            'title' => get_string($last ? 'hero_finish' : ($c['progress'] > 0 ? 'hero_continue' : 'hero_start'), 'local_siraj', $c['name']),
            'text' => get_string($last ? 'hero_finish_text' : 'hero_next_text', 'local_siraj', $c['nextname']),
            'meta' => $meta,
            'url' => $c['nexturl'] ?: $c['url'],
            'button' => get_string($c['progress'] > 0 ? 'continue' : 'start', 'local_siraj'),
            'progress' => $c['progress'],
            'photo' => $c['cover'] ?? null,
        ];
    }

    /**
     * List row context.
     *
     * @param array $c
     * @return array
     */
    protected function row(array $c): array {
        $sub = [];
        if (!empty($c['enrolled'])) {
            $sub[] = $c['nextname'] ? get_string('next', 'local_siraj', $c['nextname']) : '';
        } else {
            $sub[] = get_string($c['mandatory'] ? 'mandatory' : 'optional', 'local_siraj');
            $sub[] = $c['total'] == 1 ? get_string('oneactivity', 'local_siraj') : get_string('nactivities', 'local_siraj', $c['total']);
        }
        if ($c['duration']) {
            $sub[] = plural::str('minutes', (int)$c['duration']);
        }
        return $c + ['sub' => implode(' · ', array_filter($sub))];
    }

    /**
     * The user's courses with progress, next activity and due date (also used by My learning).
     *
     * @return array[]
     */
    public function courses(): array {
        global $DB;
        $courses = enrol_get_users_courses($this->user->id, true, 'enablecompletion, summary');
        if (!$courses) {
            return [];
        }
        $fields = coursefields::values(array_keys($courses));
        $lastaccess = $DB->get_records_menu('user_lastaccess', ['userid' => $this->user->id], '', 'courseid, timeaccess');
        $due = $this->due_dates(array_keys($courses));

        $out = [];
        foreach ($courses as $course) {
            // Courses they teach are on Teaching, not in their own learning.
            if (!has_capability('moodle/course:isincompletionreports', \context_course::instance($course->id), $this->user->id)) {
                continue;
            }
            $modinfo = get_fast_modinfo($course, $this->user->id);
            $completion = new \completion_info($course);
            $total = 0;
            $done = 0;
            $next = null;
            $completedon = 0;
            foreach ($modinfo->get_cms() as $cm) {
                if (!$cm->uservisible || $cm->deletioninprogress || !$completion->is_enabled($cm)) {
                    continue;
                }
                $total++;
                $data = $this->state($completion, $cm);
                if (in_array($data->completionstate, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS])) {
                    $done++;
                    $completedon = max($completedon, (int)$data->timemodified);
                } else if (!$next) {
                    $next = $cm;
                }
            }
            $coursedue = null;
            foreach ($due[$course->id] ?? [] as [$cmid, $time]) {
                if (isset($modinfo->cms[$cmid]) && $modinfo->cms[$cmid]->uservisible) {
                    $state = $this->state($completion, $modinfo->cms[$cmid])->completionstate;
                    if (!in_array($state, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS])) {
                        $coursedue = $coursedue ? min($coursedue, $time) : $time;
                    }
                }
            }
            $context = \context_course::instance($course->id);
            $out[] = [
                'id' => $course->id,
                'enrolled' => true,
                'name' => format_string($course->fullname, true, ['context' => $context, 'escape' => false]),
                'url' => (new \moodle_url('/local/siraj/course.php', ['id' => $course->id]))->out(false),
                'total' => $total,
                'done' => $done,
                'remaining' => $total - $done,
                'progress' => $total ? (int)round($done / $total * 100) : 0,
                'nextname' => $next ? format_string($next->name, true, ['context' => $next->context, 'escape' => false]) : '',
                'nexturl' => $next ? progress::open_url($next) : '',
                'nextcmid' => $next ? (int)$next->id : 0,
                'nextmod' => $next ? $next->modname : '',
                'due' => $coursedue,
                'mandatory' => $fields[$course->id]['mandatory'] ?? false,
                'duration' => $fields[$course->id]['duration'] ?? null,
                'lastaccess' => (int)($lastaccess[$course->id] ?? 0),
                'completedon' => $completedon,
                'cover' => $this->withcovers ? $this->cover($course) : null,
                'coverclass' => 'cv' . ($course->id % 6),
                'category' => \core_course_category::get($course->category, IGNORE_MISSING, true)?->get_formatted_name(['escape' => false]) ?? '',
            ];
        }
        return $out;
    }

    /**
     * Completion state of an activity for this user. For the signed-in user it is Moodle's own (cached) data; for
     * someone else (manager dashboards) all their completion rows are read in one query instead of one per activity.
     *
     * @param \completion_info $completion
     * @param \cm_info $cm
     * @return \stdClass with completionstate and timemodified
     */
    protected function state(\completion_info $completion, \cm_info $cm): \stdClass {
        global $DB, $USER;
        if ((int)$this->user->id === (int)$USER->id) {
            return $completion->get_data($cm, true, $this->user->id);
        }
        if ($this->states === null) {
            $this->states = $DB->get_records('course_modules_completion', ['userid' => $this->user->id], '',
                'coursemoduleid, completionstate, timemodified');
        }
        return $this->states[$cm->id] ?? (object)['completionstate' => COMPLETION_INCOMPLETE, 'timemodified' => 0];
    }

    /**
     * Activity due/close dates from the calendar, per course.
     *
     * @param int[] $courseids
     * @return array courseid => [[cmid, time], ...]
     */
    protected function due_dates(array $courseids): array {
        global $DB;
        [$in, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['from'] = $this->now - 60 * DAYSECS;
        $rows = $DB->get_records_sql("
            SELECT e.id, e.courseid, e.modulename, e.instance, e.timestart
              FROM {event} e
             WHERE e.courseid $in AND e.modulename <> '' AND e.visible = 1
                   AND e.eventtype IN ('due', 'close', 'expectcompletionon') AND e.timestart >= :from
          ORDER BY e.timestart", $params);
        $out = [];
        foreach ($rows as $r) {
            $modinfo = get_fast_modinfo($r->courseid, $this->user->id);
            $cm = $modinfo->instances[$r->modulename][$r->instance] ?? null;
            if ($cm) {
                $out[$r->courseid][] = [$cm->id, (int)$r->timestart];
            }
        }
        return $out;
    }

    /**
     * Courses open for self-enrolment that the user isn't in.
     *
     * @return array[]
     */
    protected function recommended(): array {
        global $DB;
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $rows = $DB->get_records_sql("
            SELECT DISTINCT c.*
              FROM {course} c
              JOIN {enrol} e ON e.courseid = c.id AND e.enrol = 'self' AND e.status = 0
             WHERE c.visible = 1 AND c.id <> :site
                   AND NOT EXISTS (SELECT 1 FROM {user_enrolments} ue JOIN {enrol} e2 ON e2.id = ue.enrolid
                                    WHERE e2.courseid = c.id AND ue.userid = :userid)
          ORDER BY c.timecreated DESC", ['site' => SITEID, 'userid' => $this->user->id], 0, 2);
        $fields = coursefields::values(array_keys($rows));
        $cache = [];
        foreach ($rows as $course) {
            $count = 0;
            foreach (get_fast_modinfo($course, $this->user->id)->get_cms() as $cm) {
                $count += $cm->completion ? 1 : 0;
            }
            $cache[] = [
                'id' => $course->id, 'enrolled' => false,
                'name' => format_string($course->fullname, true, ['context' => \context_course::instance($course->id), 'escape' => false]),
                'url' => (new \moodle_url('/local/siraj/course.php', ['id' => $course->id]))->out(false),
                'total' => max($count, 1), 'mandatory' => $fields[$course->id]['mandatory'] ?? false,
                'duration' => $fields[$course->id]['duration'] ?? null,
                'cover' => $this->cover($course), 'coverclass' => 'cv' . ($course->id % 6), 'preview' => true,
            ];
        }
        return $cache;
    }

    /**
     * Weekly rhythm: which days this week the user did any learning (from the standard log).
     *
     * @return array
     */
    protected function rhythm(): array {
        global $DB;
        $tz = \core_date::get_user_timezone_object($this->user);
        $today = new \DateTime('now', $tz);
        $monday = (clone $today)->modify('monday this week')->setTime(0, 0);
        $goal = (int)(get_config('local_siraj', 'weeklygoal') ?: 4);
        $times = $DB->get_fieldset_sql("
            SELECT timecreated FROM {logstore_standard_log}
             WHERE userid = :u AND timecreated >= :from AND courseid <> :site AND crud IN ('r', 'c', 'u')",
            ['u' => $this->user->id, 'from' => $monday->getTimestamp(), 'site' => SITEID]);
        $active = [];
        foreach ($times as $t) {
            $active[(new \DateTime('@' . $t))->setTimezone($tz)->format('N')] = true;
        }
        $days = [];
        $labels = explode(',', get_string('weekdays_short', 'local_siraj'));
        $todayn = (int)$today->format('N');
        foreach (range(1, 7) as $n) {
            $state = !empty($active[$n]) ? 'done' : ($n == $todayn ? 'today' : ($n < $todayn ? 'missed' : 'later'));
            $days[] = ['label' => $labels[$n - 1], 'state' => $state, 'done' => $state === 'done', 'today' => $state === 'today'];
        }
        $count = count($active);
        $left = max(0, $goal - $count);
        return [
            'count' => $count, 'goal' => $goal, 'pct' => min(100, (int)round($count / $goal * 100)),
            'title' => $left ? plural::str('rhythm_n', $left)
                : get_string('rhythm_done', 'local_siraj'),
            'text' => $left ? plural::str('rhythm_text_n', $left)
                : get_string('rhythm_done_text', 'local_siraj'),
            'chip' => $count >= $goal ? get_string('rhythm_chip_done', 'local_siraj') :
                ($count >= $todayn * $goal / 7 ? get_string('rhythm_chip_good', 'local_siraj') : get_string('rhythm_chip_catchup', 'local_siraj')),
            'days' => $days,
        ];
    }

    /**
     * Course image URL, if the course has one.
     *
     * @param \stdClass $course
     * @return string|null
     */
    public function cover(\stdClass $course): ?string {
        $url = \core_course\external\course_summary_exporter::get_course_image($course);
        return $url ?: null;
    }

    /**
     * "today", "in 3 days", "3 days ago".
     *
     * @param int $time
     * @return string
     */
    public function relative(int $time): string {
        $days = (int)floor(($time - $this->now) / DAYSECS);
        if ($days < 0) {
            return plural::str('overdue_by', -$days);
        }
        if ($days == 0) {
            return get_string('due_today', 'local_siraj');
        }
        return ($days == 1 ? get_string('due_tomorrow', 'local_siraj') : plural::str('due_in', $days));
    }
}
