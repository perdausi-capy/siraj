<?php
/**
 * Course structure and progress for one learner: sections, activities, completion state, next step.
 * Uses only Moodle's modinfo and completion APIs, so it always agrees with Moodle's own completion data.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Learner's view of one course.
 */
class progress {
    /** @var string[] Activity types Learning mode shows inside the player. */
    const NATIVE = ['page', 'url', 'resource'];

    /** @var \stdClass */
    public $course;
    /** @var \course_modinfo */
    public $modinfo;
    /** @var \completion_info */
    public $completion;
    /** @var int */
    public $userid;

    /**
     * Constructor.
     *
     * @param \stdClass $course
     * @param int $userid
     */
    public function __construct(\stdClass $course, int $userid) {
        $this->course = $course;
        $this->userid = $userid;
        $this->modinfo = get_fast_modinfo($course, $userid);
        $this->completion = new \completion_info($course);
    }

    /**
     * Is this activity complete for the learner?
     *
     * @param \cm_info $cm
     * @return bool
     */
    public function is_done(\cm_info $cm): bool {
        if (!$this->completion->is_enabled($cm)) {
            return false;
        }
        $state = $this->completion->get_data($cm, false, $this->userid)->completionstate;
        return in_array($state, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS]);
    }

    /**
     * Activities the learner can open, in course order (labels and hidden items left out).
     *
     * @return \cm_info[]
     */
    public function steps(): array {
        $out = [];
        foreach ($this->modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible || $section->is_delegated()) {
                continue;
            }
            foreach ($this->modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $this->modinfo->cms[$cmid];
                if ($cm->uservisible && $cm->url && !$cm->deletioninprogress) {
                    $out[] = $cm;
                }
            }
        }
        return $out;
    }

    /**
     * Totals: tracked activities, completed, next incomplete activity.
     *
     * @return array [total, done, next cm_info|null]
     */
    public function totals(): array {
        $total = 0;
        $done = 0;
        $next = null;
        foreach ($this->steps() as $cm) {
            if (!$this->completion->is_enabled($cm)) {
                continue;
            }
            $total++;
            if ($this->is_done($cm)) {
                $done++;
            } else if (!$next) {
                $next = $cm;
            }
        }
        return [$total, $done, $next];
    }

    /**
     * Where a learner goes for an activity: always Learning mode (native types play inside it,
     * others get a launch card that opens Moodle's own page).
     *
     * @param \cm_info $cm
     * @return string
     */
    public static function open_url(\cm_info $cm): string {
        return (new \moodle_url('/local/siraj/learn.php', ['cmid' => $cm->id]))->out(false);
    }

    /**
     * Icon name for an activity type.
     *
     * @param string $modname
     * @return string
     */
    public static function icon(string $modname): string {
        return [
            'page' => 'file', 'resource' => 'file', 'url' => 'external', 'book' => 'book', 'quiz' => 'help',
            'assign' => 'upload', 'forum' => 'message', 'scorm' => 'layers', 'h5pactivity' => 'play', 'lesson' => 'book',
            'feedback' => 'chart', 'choice' => 'check-circle', 'customcert' => 'award', 'bigbluebuttonbn' => 'users',
            'glossary' => 'type', 'folder' => 'folder', 'wiki' => 'file', 'workshop' => 'users', 'data' => 'grid',
            'lti' => 'external', 'imscp' => 'layers',
        ][$modname] ?? 'layers';
    }

    /**
     * A course's one-line description for cards and headers: the first paragraph of its summary only (the rest,
     * such as "By the end you will be able to" lists, belongs on the course page).
     *
     * @param string $html formatted summary
     * @param int $length
     * @return string plain text
     */
    public static function lead(string $html, int $length): string {
        if (preg_match('~<p[^>]*>(.*?)</p>~is', $html, $m) && trim(strip_tags($m[1])) !== '') {
            $html = $m[1];
        }
        return shorten_text(html_to_text($html, 0, false), $length);
    }
}
