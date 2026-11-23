<?php
/**
 * Siraj Explore: the course library as collections (categories) with rows of course tiles and instant search.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for Explore.
 */
class explore_page {
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
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        global $DB;
        $home = new home($this->user);
        $mine = [];
        foreach ($home->courses() as $c) {
            $mine[$c['id']] = $c;
        }
        $selfenrol = $DB->get_records_menu('enrol', ['enrol' => 'self', 'status' => ENROL_INSTANCE_ENABLED], '', 'courseid, id');

        $rows = [];
        $total = 0;
        foreach (\core_course_category::top()->get_children() as $cat) {
            $list = $cat->get_courses(['recursive' => true, 'sort' => ['fullname' => 1]]);
            if (!$list) {
                continue;
            }
            $ids = array_keys($list);
            $fields = coursefields::values($ids);
            $tiles = [];
            foreach ($list as $el) {
                $course = get_course($el->id);
                if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', \context_course::instance($course->id))) {
                    continue;
                }
                $count = 0;
                foreach (get_fast_modinfo($course, $this->user->id)->get_cms() as $cm) {
                    $count += ($cm->completion && $cm->visible) ? 1 : 0;
                }
                $m = $mine[$course->id] ?? null;
                $state = $m ? ($m['progress'] >= 100 ? 'done' : ($m['done'] > 0 ? 'progress' : 'enrolled')) :
                    (isset($selfenrol[$course->id]) ? 'open' : 'locked');
                $name = format_string($course->fullname, true, ['context' => \context_course::instance($course->id), 'escape' => false]);
                $tiles[] = [
                    'id' => $course->id, 'name' => $name, 'lname' => \core_text::strtolower($name . ' ' . $cat->name),
                    'summary' => progress::lead(format_text($course->summary, $course->summaryformat), 110),
                    'url' => (new \moodle_url('/local/siraj/course.php', ['id' => $course->id]))->out(false),
                    'cover' => $home->cover($course), 'coverclass' => 'cv' . ($course->id % 6),
                    'duration' => $fields[$course->id]['duration'] ? plural::str('minutes', (int)$fields[$course->id]['duration']) : null,
                    'mandatory' => $fields[$course->id]['mandatory'],
                    'lessons' => $count == 1 ? get_string('oneactivity', 'local_siraj') : get_string('nactivities', 'local_siraj', $count),
                    'progress' => $m['progress'] ?? 0, 'inprogress' => $state === 'progress', 'isdone' => $state === 'done',
                    'enrolled' => (bool)$m,
                    'statelabel' => get_string('state_' . $state, 'local_siraj'), 'state' => $state,
                    'hidden' => !$course->visible,
                ];
            }
            if (!$tiles) {
                continue;
            }
            $total += count($tiles);
            $rows[] = [
                'id' => $cat->id, 'name' => $cat->get_formatted_name(['escape' => false]),
                'description' => shorten_text(html_to_text(format_text($cat->description, $cat->descriptionformat), 0, false), 160),
                'tiles' => $tiles, 'count' => count($tiles),
            ];
        }
        // The featured collection is one people can see: not a category of hidden (staff-only) courses.
        $open = array_filter($rows, fn($r) => array_filter($r['tiles'], fn($t) => !$t['hidden']));
        $featured = reset($open) ?: ($rows[0] ?? null);
        return [
            'featured' => $featured ? [
                'name' => $featured['name'], 'description' => $featured['description'], 'anchor' => '#siraj-cat-' . $featured['id'],
                'count' => get_string('ncourses', 'local_siraj', $featured['count']),
                'photo' => current(array_filter(array_column($featured['tiles'], 'cover'))) ?: null,
            ] : null,
            'rows' => $rows, 'hasrows' => !empty($rows), 'total' => $total,
            'icon_search' => icons::svg('search'), 'icon_clock' => icons::svg('clock'), 'icon_layers' => icons::svg('layers'),
            'icon_arrow' => icons::svg('arrow-right'), 'icon_check' => icons::svg('check'), 'flame' => icons::flame(),
        ];
    }
}
