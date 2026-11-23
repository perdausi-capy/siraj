<?php
/**
 * Siraj Grades: every course with its total, and one course's graded items with feedback. Reads Moodle's
 * gradebook (grade_item / grade_grade), respecting hidden grades and items, so it matches Moodle's own reports.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for grades.php.
 */
class grades_page {
    /** @var \stdClass */
    protected $user;

    /**
     * Constructor.
     *
     * @param \stdClass $user
     */
    public function __construct(\stdClass $user) {
        $this->user = $user;
    }

    /**
     * A grade as text and as a percentage of its range (null when not graded).
     *
     * @param \grade_item $item
     * @param \grade_grade|null $grade
     * @return array [text, pct|null, max text]
     */
    protected function show(\grade_item $item, ?\grade_grade $grade): array {
        if (!$grade || $grade->finalgrade === null) {
            return ['-', null, ''];
        }
        // Natural (sum) aggregation gives each learner their own maximum (ungraded items left out), like Moodle's report.
        $max = $grade->get_grade_max();
        $min = $grade->get_grade_min();
        $range = $max - $min;
        $pct = $range > 0 ? (int)round(($grade->finalgrade - $min) / $range * 100) : null;
        $text = grade_format_gradevalue($grade->finalgrade, $item, true, GRADE_DISPLAY_TYPE_REAL);
        return [$text, $pct, format_float($max, 0)];
    }

    /**
     * Template context.
     *
     * @param int $courseid 0 for the list of courses
     * @return array
     */
    public function export(int $courseid = 0): array {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/lib.php');
        $courses = enrol_get_users_courses($this->user->id, true, 'showgrades');
        $list = [];
        $detail = null;
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            if (!$course->showgrades || !has_capability('moodle/grade:view', $context, $this->user)
                    || !has_capability('moodle/course:isincompletionreports', $context, $this->user->id)) {
                continue;
            }
            $total = \grade_item::fetch_course_item($course->id);
            $grade = $total ? \grade_grade::fetch(['itemid' => $total->id, 'userid' => $this->user->id]) : null;
            $hidden = $total && ($total->is_hidden() || ($grade && $grade->is_hidden()))
                && !has_capability('moodle/grade:viewhidden', $context);
            [$text, $pct, $max] = $hidden ? ['-', null, ''] : ($total ? $this->show($total, $grade ?: null) : ['-', null, '']);
            $name = format_string($course->fullname, true, ['context' => $context, 'escape' => false]);
            $row = [
                'id' => $course->id, 'name' => $name, 'grade' => $text . ($max !== '' ? ' / ' . $max : ''), 'pct' => $pct, 'haspct' => $pct !== null,
                'band' => $pct === null ? '' : ($pct >= 80 ? 'high' : ($pct >= 50 ? 'mid' : 'low')),
                'url' => (new \moodle_url('/local/siraj/grades.php', ['id' => $course->id]))->out(false),
                'cover' => (new home($this->user))->cover($course), 'coverclass' => 'cv' . ($course->id % 6),
                'current' => $course->id == $courseid,
            ];
            $list[] = $row;
            if ($course->id == $courseid) {
                $detail = $row + ['items' => $this->items($course, $context),
                    'courseurl' => (new \moodle_url('/local/siraj/course.php', ['id' => $course->id]))->out(false),
                    'fullurl' => (new \moodle_url('/grade/report/user/index.php', ['id' => $course->id, 'siraj' => 'classic']))->out(false)];
                $detail['hasitems'] = !empty($detail['items']);
            }
        }
        usort($list, fn($a, $b) => [$a['pct'] === null, $a['name']] <=> [$b['pct'] === null, $b['name']]);
        $graded = array_filter($list, fn($r) => $r['pct'] !== null);
        return [
            'courses' => $list, 'hascourses' => !empty($list), 'detail' => $detail, 'isdetail' => (bool)$detail,
            'average' => $graded ? (int)round(array_sum(array_column($graded, 'pct')) / count($graded)) : null,
            'gradedcount' => count($graded), 'coursecount' => count($list),
            'summary' => get_string('grades_summary', 'local_siraj', (object)['graded' => count($graded), 'total' => count($list)]),
            'backurl' => (new \moodle_url('/local/siraj/grades.php'))->out(false),
            'icon_arrow' => icons::svg('arrow-right'), 'icon_back' => icons::svg('arrow-left'), 'icon_chart' => icons::svg('chart'),
            'icon_external' => icons::svg('external'), 'icon_message' => icons::svg('message'),
        ];
    }

    /**
     * One course's graded activities, in gradebook order, with feedback.
     *
     * @param \stdClass $course
     * @param \context_course $context
     * @return array
     */
    protected function items(\stdClass $course, \context_course $context): array {
        $viewhidden = has_capability('moodle/grade:viewhidden', $context);
        $modinfo = get_fast_modinfo($course, $this->user->id);
        $out = [];
        $items = \grade_item::fetch_all(['courseid' => $course->id]) ?: [];
        uasort($items, fn($a, $b) => $a->sortorder <=> $b->sortorder);
        foreach ($items as $item) {
            if ($item->itemtype !== 'mod' || ($item->is_hidden() && !$viewhidden)) {
                continue;
            }
            $cm = $modinfo->instances[$item->itemmodule][$item->iteminstance] ?? null;
            if ($cm && !$cm->uservisible) {
                continue;
            }
            $grade = \grade_grade::fetch(['itemid' => $item->id, 'userid' => $this->user->id]) ?: null;
            if ($grade && $grade->is_hidden() && !$viewhidden) {
                $grade = null;
            }
            [$text, $pct, $max] = $this->show($item, $grade);
            $feedback = $grade && $grade->feedback ? format_text($grade->feedback, $grade->feedbackformat, ['context' => $context]) : '';
            $out[] = [
                'name' => $cm ? $cm->get_formatted_name(['escape' => false]) : format_string($item->itemname, true, ['escape' => false]),
                'type' => get_string('modulename', $item->itemmodule),
                'icon' => icons::svg(progress::icon($item->itemmodule)),
                'grade' => $text, 'max' => $max, 'pct' => $pct, 'haspct' => $pct !== null,
                'band' => $pct === null ? '' : ($pct >= 80 ? 'high' : ($pct >= 50 ? 'mid' : 'low')),
                'feedback' => $feedback,
                'when' => $grade && $grade->timemodified ? userdate($grade->timemodified, get_string('strftimedatefullshort', 'langconfig')) : '',
                'url' => $cm ? progress::open_url($cm) : null,
            ];
        }
        return $out;
    }
}
