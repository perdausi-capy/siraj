<?php
/**
 * Siraj quiz renderer: adds a results card on top of Moodle's review page. Grades come from Moodle as-is.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_siraj\output;

use mod_quiz\output\attempt_summary_information;
use mod_quiz\question\display_options;
use mod_quiz\quiz_attempt;
use theme_siraj\icons;

/**
 * Quiz renderer override.
 */
class mod_quiz_renderer extends \mod_quiz\output\renderer {
    /**
     * Review page with the Siraj results card first (first page of a finished attempt, when marks may be shown).
     *
     * @param quiz_attempt $attemptobj
     * @param array $slots
     * @param int $page
     * @param bool $showall
     * @param bool $lastpage
     * @param display_options $displayoptions
     * @param attempt_summary_information|array $summarydata
     * @return string
     */
    public function review_page(quiz_attempt $attemptobj, $slots, $page, $showall,
            $lastpage, display_options $displayoptions, $summarydata) {
        $html = parent::review_page($attemptobj, $slots, $page, $showall, $lastpage, $displayoptions, $summarydata);
        if ($page > 0 || !$attemptobj->is_finished() || $displayoptions->marks < \question_display_options::MARK_AND_MAX) {
            return $html;
        }
        $card = $this->results_card($attemptobj, $displayoptions);
        // Place the card right after the page header (before the summary table).
        $pos = strpos($html, '<table class="generaltable generalbox quizreviewsummary');
        return $pos === false ? $card . $html : substr($html, 0, $pos) . $card . substr($html, $pos);
    }

    /**
     * Moodle's quiz page (what staff see, and learners outside Learning mode): the results card for the latest
     * finished attempt at the top, and a button back to the course page next to the attempt button.
     *
     * @param \stdClass $course
     * @param \stdClass $quiz
     * @param \cm_info|\stdClass $cm
     * @param \context_module $context
     * @param \mod_quiz\output\view_page $viewobj
     * @return string
     */
    public function view_page($course, $quiz, $cm, $context, $viewobj) {
        $card = '';
        $finished = array_filter($viewobj->attempts ?? [], fn($a) => $a->state === quiz_attempt::FINISHED);
        if ($finished) {
            $last = end($finished);
            $attemptobj = quiz_attempt::create($last->id);
            $options = $attemptobj->get_display_options(false);
            if ($options->marks >= \question_display_options::MARK_AND_MAX) {
                // On the quiz page "Review answers" opens that attempt's review (on the review page it scrolls down).
                $card = $this->results_card($attemptobj, $options, $attemptobj->review_url()->out(false));
            }
        }
        $html = parent::view_page($course, $quiz, $cm, $context, $viewobj);
        if ($card === '') {
            return $html;
        }
        // After the tertiary navigation (Quiz / Settings / ...), before the quiz information.
        $pos = strpos($html, '<div class="box py-3 quizinfo');
        $pos = $pos === false ? strpos($html, '<div class="activity-header') : $pos;
        return $pos === false ? $card . $html : substr($html, 0, $pos) . $card . substr($html, $pos);
    }

    /**
     * The attempt button, plus a way back to the course page.
     *
     * @param \mod_quiz\output\view_page $viewobj
     * @return string
     */
    public function view_page_buttons(\mod_quiz\output\view_page $viewobj) {
        $html = parent::view_page_buttons($viewobj);
        // With a finished attempt the results card above already has "Back to course".
        if (array_filter($viewobj->attempts ?? [], fn($a) => $a->state === quiz_attempt::FINISHED)) {
            return $html;
        }
        $courseid = $this->page->course->id;
        $courseurl = file_exists($GLOBALS['CFG']->dirroot . '/local/siraj/course.php') ?
            new \moodle_url('/local/siraj/course.php', ['id' => $courseid]) : new \moodle_url('/course/view.php', ['id' => $courseid]);
        return '<div class="siraj-quizview-actions">' . $html . \html_writer::link($courseurl,
            icons::svg('arrow-left') . s(get_string('results_backtocourse', 'theme_siraj')), ['class' => 'btn btn-secondary siraj-backtocourse']) . '</div>';
    }

    /**
     * The results card.
     *
     * @param quiz_attempt $attemptobj
     * @param display_options $displayoptions
     * @return string
     */
    protected function results_card(quiz_attempt $attemptobj, display_options $displayoptions, ?string $reviewurl = null): string {
        global $DB;
        $quiz = $attemptobj->get_quiz();
        $marks = (float)$attemptobj->get_sum_marks();
        $max = (float)$quiz->sumgrades;
        $pct = $max > 0 ? (int)round($marks / $max * 100) : 0;
        $grade = $max > 0 ? $marks / $max * $quiz->grade : 0;
        $gradepass = (float)$DB->get_field('grade_items', 'gradepass',
            ['itemtype' => 'mod', 'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'courseid' => $quiz->course]);
        $correct = 0;
        $slots = $attemptobj->get_slots();
        foreach ($slots as $slot) {
            $correct += $attemptobj->get_question_state($slot) == \question_state::$gradedright ? 1 : 0;
        }
        $haspass = $gradepass > 0;
        $passed = $haspass && $grade + 1e-9 >= $gradepass;
        $state = $haspass ? ($passed ? 'pass' : 'fail') : 'done';

        $allowed = $attemptobj->get_num_attempts_allowed();
        $used = $DB->count_records_select('quiz_attempts', 'quiz = ? AND userid = ? AND preview = 0 AND state <> ?',
            [$quiz->id, $attemptobj->get_userid(), quiz_attempt::ABANDONED]);
        $canretake = !$passed && (!$allowed || $used < $allowed) && !$attemptobj->is_preview();
        $courseurl = file_exists($GLOBALS['CFG']->dirroot . '/local/siraj/course.php') ?
            new \moodle_url('/local/siraj/course.php', ['id' => $quiz->course]) : new \moodle_url('/course/view.php', ['id' => $quiz->course]);

        return $this->render_from_template('theme_siraj/quiz_results', [
            'state' => $state, 'pass' => $state === 'pass', 'fail' => $state === 'fail',
            'title' => get_string('results_' . $state, 'theme_siraj'),
            'text' => get_string('results_' . $state . '_text', 'theme_siraj',
                quiz_format_grade($quiz, $gradepass) . ' / ' . quiz_format_grade($quiz, $quiz->grade)),
            'pct' => $pct, 'grade' => quiz_format_grade($quiz, $grade) . ' / ' . quiz_format_grade($quiz, $quiz->grade),
            'correct' => get_string('results_correct', 'theme_siraj', (object)['n' => $correct, 'total' => count($slots)]),
            'time' => format_time((int)$attemptobj->get_attempt()->timefinish - (int)$attemptobj->get_attempt()->timestart),
            'attempts' => $allowed ? get_string('results_attempts', 'theme_siraj', (object)['n' => $used, 'total' => $allowed]) : null,
            'retakeurl' => $canretake ? (new \moodle_url('/mod/quiz/view.php', ['id' => $attemptobj->get_cmid()]))->out(false) : null,
            'courseurl' => $courseurl->out(false), 'reviewurl' => $reviewurl,
            'icon_check' => icons::svg('check-circle'), 'icon_clock' => icons::svg('clock'), 'icon_arrow' => icons::svg('arrow-right'),
            'icon_back' => icons::svg('arrow-left'), 'icon_layers' => icons::svg('layers'),
        ]);
    }
}
