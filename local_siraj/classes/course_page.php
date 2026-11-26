<?php
/**
 * Siraj course overview: cover, progress, the learning path (sections as steps) and course details.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for the course overview.
 */
class course_page {
    /** @var progress */
    protected $p;
    /** @var array|null Preview for people not enrolled: ['canenrol' => bool, 'formurl' => string, 'enrolpageurl' => ?string]. */
    protected $preview;

    /**
     * Constructor.
     *
     * @param progress $p
     * @param array|null $preview set when the user is not enrolled (outline only, with an enrol action)
     */
    public function __construct(progress $p, ?array $preview = null) {
        $this->p = $p;
        $this->preview = $preview;
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        global $DB;
        $course = $this->p->course;
        $context = \context_course::instance($course->id);
        [$total, $done, $next] = $this->p->totals();
        if ($done == 0) {
            // Not started yet: begin at the first step (introductions often have no completion tracking), skipping
            // the course's announcements forum, which is news rather than a step.
            foreach ($this->p->steps() as $cm) {
                if (!($cm->modname === 'forum' && $cm->sectionnum == 0 &&
                        $DB->get_field('forum', 'type', ['id' => $cm->instance]) === 'news')) {
                    $next = $cm;
                    break;
                }
            }
        }
        $fields = coursefields::values([$course->id])[$course->id];
        $pct = $total ? (int)round($done / $total * 100) : 0;

        // Sections become the learning path.
        $steps = [];
        $current = false;
        foreach ($this->p->modinfo->get_section_info_all() as $section) {
            if (!$section->uservisible || $section->is_delegated()) {
                continue;
            }
            $acts = [];
            $tracked = 0;
            $sdone = 0;
            foreach ($this->p->modinfo->sections[$section->section] ?? [] as $cmid) {
                $cm = $this->p->modinfo->cms[$cmid];
                if (!$cm->url || $cm->deletioninprogress || (!$cm->uservisible && !$cm->is_visible_on_course_page())) {
                    continue;
                }
                // Restricted ("available after you complete ..."): shown locked, with the reason, not left out.
                if (!$cm->uservisible) {
                    $acts[] = [
                        'name' => $cm->get_formatted_name(['escape' => false]),
                        'type' => get_string('modulename', $cm->modname),
                        'icon' => icons::svg(progress::icon($cm->modname)),
                        'modname' => $cm->modname, 'url' => null, 'done' => false, 'isnext' => false, 'todo' => true,
                        'locked' => true, 'lockinfo' => progress::lock_reason($cm, $course),
                    ];
                    $tracked += $this->p->completion->is_enabled($cm) ? 1 : 0;
                    continue;
                }
                $isdone = $this->p->is_done($cm);
                $tracking = $this->p->completion->is_enabled($cm);
                $tracked += $tracking ? 1 : 0;
                $sdone += $isdone ? 1 : 0;
                $acts[] = [
                    'name' => $cm->get_formatted_name(['escape' => false]),
                    'type' => get_string('modulename', $cm->modname),
                    'icon' => icons::svg(progress::icon($cm->modname)),
                    'modname' => $cm->modname,
                    'url' => $this->preview ? null : progress::open_url($cm),
                    'done' => $isdone,
                    'isnext' => !$this->preview && $next && $cm->id == $next->id,
                    'todo' => $tracking && !$isdone,
                ];
            }
            if (!$acts) {
                continue;
            }
            $sectiondone = $tracked && $sdone == $tracked;
            $iscurrent = !$this->preview && !$sectiondone && !$current;
            $current = $current || $iscurrent;
            $steps[] = [
                'num' => count($steps) + 1,
                'name' => get_section_name($course, $section),
                'count' => count($acts) == 1 ? get_string('oneactivity', 'local_siraj') : get_string('nactivities', 'local_siraj', count($acts)),
                'done' => $sectiondone,
                'current' => $iscurrent,
                'state' => $sectiondone ? 'done' : ($iscurrent ? 'current' : 'todo'),
                'activities' => $acts,
                'open' => $iscurrent || ($this->preview && !$steps),
            ];
        }
        if ($steps) {
            $steps[count($steps) - 1]['last'] = true;
        }

        $due = null;
        $events = $DB->get_records_sql("SELECT e.modulename, e.instance, e.timestart FROM {event} e
             WHERE e.courseid = :c AND e.modulename <> '' AND e.visible = 1 AND e.eventtype IN ('due', 'close')
          ORDER BY e.timestart", ['c' => $course->id]);
        foreach ($events as $e) {
            $cm = $this->p->modinfo->instances[$e->modulename][$e->instance] ?? null;
            if ($cm && $cm->uservisible && !$this->p->is_done($cm)) {
                $due = (int)$e->timestart;
                break;
            }
        }

        $meta = [];
        if ($fields['duration']) {
            $meta[] = ['icon' => icons::svg('clock'), 'text' => plural::str('minutes', (int)$fields['duration'])];
        }
        $meta[] = ['icon' => icons::svg('layers'), 'text' => count($steps) == 1 ? get_string('onestep', 'local_siraj') : get_string('nsteps', 'local_siraj', count($steps))];
        if ($due) {
            $meta[] = ['icon' => icons::svg('calendar'), 'text' => get_string('duedate', 'local_siraj', userdate($due, '%d.%m.%Y'))];
        }

        $category = \core_course_category::get($course->category, IGNORE_MISSING, true);
        $summary = format_text(file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null),
            $course->summaryformat, ['context' => $context]);
        $cover = \core_course\external\course_summary_exporter::get_course_image($course);
        $canedit = has_capability('moodle/course:update', $context);

        return [
            'name' => format_string($course->fullname, true, ['context' => $context, 'escape' => false]),
            'eyebrow' => $fields['mandatory'] ? get_string('mandatorylearning', 'local_siraj')
                : ($category ? $category->get_formatted_name(['escape' => false]) : ''),
            'summary' => $summary,
            'summarytext' => progress::lead($summary, 220),
            'cover' => $cover ?: null,
            'meta' => $meta,
            'progress' => $pct,
            'done' => $done,
            'total' => $total,
            'started' => $done > 0,
            'complete' => $total && $done == $total,
            'nexturl' => $this->preview ? null : ($next ? progress::open_url($next) : ($steps ? $steps[0]['activities'][0]['url'] : null)),
            'preview' => $this->preview,
            'sesskey' => sesskey(),
            'nextname' => !$this->preview && $next ? $next->get_formatted_name(['escape' => false]) : '',
            'steps' => $steps,
            'details' => array_values(array_filter([
                $category ? ['k' => get_string('category'), 'v' => $category->get_formatted_name(['escape' => false])] : null,
                ['k' => get_string('type', 'local_siraj'), 'v' => get_string($fields['mandatory'] ? 'mandatory' : 'optional', 'local_siraj')],
                $fields['duration'] ? ['k' => get_string('duration', 'local_siraj'), 'v' => plural::str('minutes', (int)$fields['duration'])] : null,
                ['k' => get_string('activities'), 'v' => (string)$total],
                $due ? ['k' => get_string('due', 'local_siraj'), 'v' => userdate($due, '%d.%m.%Y')] : null,
            ])),
            'classicurl' => $canedit ? (new \moodle_url('/course/view.php', ['id' => $course->id, 'siraj' => 'classic']))->out(false) : null,
            'learningurl' => (new \moodle_url($this->preview ? '/local/siraj/explore.php' : '/local/siraj/learning.php'))->out(false),
            'backlabel' => get_string($this->preview ? 'explore' : 'mylearning', 'local_siraj'),
            'icon_arrow' => icons::svg('arrow-right'),
            'icon_check' => icons::svg('check'),
            'icon_play' => icons::svg('play'),
            'icon_award' => icons::svg('award'),
            'icon_settings' => icons::svg('settings'),
            'icon_lock' => icons::svg('lock'),
            'icon_sparkles' => icons::svg('sparkles'), 'aiready' => ai::available(),
        ];
    }
}
