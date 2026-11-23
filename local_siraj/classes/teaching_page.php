<?php
/**
 * Siraj Teaching: the teacher's home. The courses they teach with how the learners are doing, what is waiting to be
 * graded, what is due soon, and one-click ways into Moodle's tools (grading, gradebook, participants, completion,
 * editing). Built from Moodle's own data and grading APIs.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for Teaching.
 */
class teaching_page {
    /** @var \stdClass */
    protected $user;
    /** @var int */
    protected $now;

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
     * Courses this person teaches (can grade or edit).
     *
     * @param int $userid
     * @return \stdClass[]
     */
    public static function courses_taught(int $userid): array {
        static $cache = [];
        if (isset($cache[$userid])) {
            return $cache[$userid];
        }
        $out = [];
        foreach (enrol_get_users_courses($userid, true, 'enablecompletion') as $course) {
            $context = \context_course::instance($course->id);
            if (has_capability('moodle/grade:viewall', $context, $userid) || has_capability('moodle/course:update', $context, $userid)) {
                $out[$course->id] = $course;
            }
        }
        return $cache[$userid] = $out;
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $courses = self::courses_taught((int)$this->user->id);
        $cards = [];
        $tograde = [];
        $due = [];
        $totals = ['learners' => 0, 'tograde' => 0, 'atrisk' => 0];
        $fields = coursefields::values(array_keys($courses));
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            $modinfo = get_fast_modinfo($course, $this->user->id);
            $learnerids = array_map('intval', array_keys(get_enrolled_users($context, 'moodle/course:isincompletionreports', 0,
                'u.id', null, 0, 0, true)));
            $n = count($learnerids);

            // Learner progress and status, from the cached per-person numbers the manager dashboards use.
            $finished = 0;
            $progress = 0;
            $statuses = [];
            if ($n <= 300) {
                foreach ($learnerids as $uid) {
                    $c = team::person($uid, $this->now)['courses'][$course->id] ?? null;
                    if ($c) {
                        $finished += $c['finished'] ? 1 : 0;
                        $progress += $c['progress'];
                        $state = $c['finished'] ? 'done' : ($c['overdue'] ? 'overdue' : ($c['done'] ? 'ontrack' : 'notstarted'));
                        $statuses[$state] = ($statuses[$state] ?? 0) + 1;
                    }
                }
            }
            $behind = ($statuses['overdue'] ?? 0);

            // Waiting to be graded: assignment submissions, and quiz attempts with questions to mark by hand.
            $coursegrade = 0;
            foreach ($modinfo->get_instances_of('assign') as $cm) {
                if (!$cm->uservisible || !has_capability('mod/assign:grade', $cm->context, $this->user->id)) {
                    continue;
                }
                $assign = new \assign($cm->context, $cm, $course);
                $count = $assign->count_submissions_need_grading();
                if ($count) {
                    $coursegrade += $count;
                    $tograde[] = ['name' => $cm->get_formatted_name(['escape' => false]), 'course' => format_string($course->fullname, true, ['escape' => false]),
                        'count' => $count, 'icon' => icons::svg('upload'),
                        'label' => get_string($count == 1 ? 'onetograde' : 'ntograde', 'local_siraj', $count),
                        'url' => (new \moodle_url('/mod/assign/view.php', ['id' => $cm->id, 'action' => 'grader']))->out(false),
                        'listurl' => (new \moodle_url('/mod/assign/view.php', ['id' => $cm->id, 'action' => 'grading']))->out(false)];
                }
                $duedate = (int)$assign->get_instance()->duedate;
                if ($duedate > $this->now && $duedate < $this->now + 14 * DAYSECS) {
                    $due[] = ['name' => $cm->get_formatted_name(['escape' => false]), 'course' => format_string($course->fullname, true, ['escape' => false]), 'time' => $duedate,
                        'when' => userdate($duedate, '%a %d.%m'), 'url' => $cm->url->out(false)];
                }
            }
            foreach ($modinfo->get_instances_of('quiz') as $cm) {
                if (!$cm->uservisible || !has_capability('mod/quiz:grade', $cm->context, $this->user->id)) {
                    continue;
                }
                $count = $DB->count_records_select('quiz_attempts', "quiz = ? AND state = 'finished' AND sumgrades IS NULL AND preview = 0",
                    [$cm->instance]);
                if ($count) {
                    $coursegrade += $count;
                    $tograde[] = ['name' => $cm->get_formatted_name(['escape' => false]), 'course' => format_string($course->fullname, true, ['escape' => false]),
                        'count' => $count, 'icon' => icons::svg('help'),
                        'label' => get_string($count == 1 ? 'onetograde' : 'ntograde', 'local_siraj', $count),
                        'url' => (new \moodle_url('/mod/quiz/report.php', ['id' => $cm->id, 'mode' => 'grading']))->out(false),
                        'listurl' => null];
                }
            }
            $totals['learners'] += $n;
            $totals['tograde'] += $coursegrade;
            $totals['atrisk'] += $behind;

            $pct = $n ? (int)round(100 * $finished / $n) : 0;
            $url = fn($path, $params) => (new \moodle_url($path, $params))->out(false);
            $cards[] = [
                'id' => $course->id,
                'name' => format_string($course->fullname, true, ['context' => $context, 'escape' => false]),
                'category' => \core_course_category::get($course->category, IGNORE_MISSING, true)?->get_formatted_name(['escape' => false]) ?? '',
                'mandatory' => $fields[$course->id]['mandatory'] ?? false,
                'cover' => (new home($this->user))->cover($course), 'coverclass' => 'cv' . ($course->id % 6),
                'learners' => get_string('nlearners', 'local_siraj', $n),
                'pct' => $pct, 'avg' => $n ? (int)round($progress / $n) : 0,
                'finished' => get_string('nofm', 'local_siraj', (object)['n' => $finished, 'of' => $n]),
                'behind' => $behind, 'tograde' => $coursegrade,
                'status' => [
                    ['label' => get_string('st_done', 'local_siraj'), 'n' => $statuses['done'] ?? 0, 'class' => 'green'],
                    ['label' => get_string('cs_inprogress', 'local_siraj'), 'n' => $statuses['ontrack'] ?? 0, 'class' => 'blue'],
                    ['label' => get_string('st_notstarted', 'local_siraj'), 'n' => $statuses['notstarted'] ?? 0, 'class' => ''],
                    ['label' => get_string('st_overdue', 'local_siraj'), 'n' => $statuses['overdue'] ?? 0, 'class' => 'red'],
                ],
                'links' => [
                    ['text' => get_string('editcoursecontent', 'local_siraj'), 'icon' => icons::svg('settings'),
                        'url' => $url('/course/view.php', ['id' => $course->id])],
                    ['text' => get_string('gradebook', 'local_siraj'), 'icon' => icons::svg('chart'),
                        'url' => $url('/grade/report/grader/index.php', ['id' => $course->id])],
                    ['text' => get_string('participants', 'local_siraj'), 'icon' => icons::svg('users'),
                        'url' => $url('/user/index.php', ['id' => $course->id])],
                    ['text' => get_string('completionreport', 'local_siraj'), 'icon' => icons::svg('check-circle'),
                        'url' => $url('/report/progress/index.php', ['course' => $course->id])],
                    ['text' => get_string('learnerview', 'local_siraj'), 'icon' => icons::svg('book'),
                        'url' => $url('/local/siraj/course.php', ['id' => $course->id])],
                ],
            ];
        }
        usort($tograde, fn($a, $b) => $b['count'] <=> $a['count']);
        usort($due, fn($a, $b) => $a['time'] <=> $b['time']);
        return [
            'greeting' => get_string('teaching_greeting', 'local_siraj', s($this->user->firstname)),
            'subline' => $totals['tograde'] ? get_string('teaching_sub_grade', 'local_siraj', $totals['tograde'])
                : get_string('teaching_sub_clear', 'local_siraj'),
            'kpis' => [
                ['value' => count($cards), 'label' => get_string('coursestaught', 'local_siraj'), 'icon' => icons::svg('book'), 'tone' => 'blue'],
                ['value' => $totals['learners'], 'label' => get_string('learnersenrolled', 'local_siraj'), 'icon' => icons::svg('users'), 'tone' => 'mint'],
                ['value' => $totals['tograde'], 'label' => get_string('waitingtograde', 'local_siraj'), 'icon' => icons::svg('upload'),
                    'tone' => $totals['tograde'] ? 'amber' : ''],
                ['value' => $totals['atrisk'], 'label' => get_string('learnersoverdue', 'local_siraj'), 'icon' => icons::svg('bell'),
                    'tone' => $totals['atrisk'] ? 'red' : ''],
            ],
            'courses' => $cards, 'hascourses' => (bool)$cards,
            'tograde' => array_slice($tograde, 0, 8), 'hastograde' => (bool)$tograde,
            'due' => array_slice($due, 0, 6), 'hasdue' => (bool)$due,
            'createurl' => has_capability('moodle/course:create', \context_system::instance(), $this->user->id)
                ? (new \moodle_url('/course/edit.php', ['category' => \core_course_category::get_default()->id]))->out(false) : null,
            'icon_arrow' => icons::svg('arrow-right'),
            'icon_check' => icons::svg('check-circle'),
            'icon_calendar' => icons::svg('calendar'),
        ];
    }
}
