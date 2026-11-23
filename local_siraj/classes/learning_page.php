<?php
/**
 * Siraj My learning: every course assigned to the learner, with filters and an agenda (by due date) view.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for My learning.
 */
class learning_page {
    /** @var home */
    protected $home;

    /**
     * Constructor.
     *
     * @param \stdClass $user
     */
    public function __construct(\stdClass $user) {
        $this->home = new home($user);
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        $now = time();
        $courses = array_values(array_filter($this->home->courses(), fn($c) => $c['total'] > 0));
        // Mandatory and due first, then by due date, then by name.
        usort($courses, fn($a, $b) => [$a['progress'] >= 100, !$a['mandatory'], $a['due'] ?? PHP_INT_MAX, $a['name']]
            <=> [$b['progress'] >= 100, !$b['mandatory'], $b['due'] ?? PHP_INT_MAX, $b['name']]);

        $items = [];
        $groups = ['overdue' => [], 'week' => [], 'later' => [], 'nodate' => [], 'done' => []];
        $counts = ['all' => 0, 'mandatory' => 0, 'optional' => 0, 'progress' => 0, 'duesoon' => 0, 'notstarted' => 0, 'done' => 0];
        foreach ($courses as $c) {
            $done = $c['progress'] >= 100;
            $status = $done ? 'done' : ($c['done'] > 0 ? 'progress' : 'notstarted');
            $duesoon = !$done && $c['due'] && $c['due'] < $now + 7 * DAYSECS;
            $overdue = !$done && $c['due'] && $c['due'] < $now;
            $chip = $done ? ['text' => get_string('completedon', 'local_siraj', userdate($c['completedon'], '%d.%m.%Y')), 'class' => 'green']
                : ($c['due'] ? ['text' => ($c['mandatory'] ? get_string('mandatory', 'local_siraj') . ' · ' : '')
                    . get_string('duewhen', 'local_siraj', $this->home->relative($c['due'])), 'class' => $overdue ? 'red' : ($duesoon ? 'amber' : '')]
                : ['text' => get_string($c['mandatory'] ? 'mandatory' : 'optional', 'local_siraj'), 'class' => $c['mandatory'] ? '' : 'blue']);
            $sub = [];
            if (!$done) {
                $sub[] = $c['nextname'] ? get_string('next', 'local_siraj', $c['nextname']) : '';
                $sub[] = get_string($c['remaining'] == 1 ? 'oneleft' : 'nleft', 'local_siraj', $c['remaining']);
            } else {
                $sub[] = get_string('nactivities', 'local_siraj', $c['total']);
            }
            if ($c['duration']) {
                $sub[] = plural::str('minutes', (int)$c['duration']);
            }
            $item = $c + [
                'status' => $status, 'isdone' => $done, 'duesoon' => $duesoon, 'lname' => \core_text::strtolower($c['name']),
                'chip' => $chip, 'sub' => implode(' · ', array_filter($sub)),
                'action' => get_string($done ? 'review' : ($status === 'progress' ? 'continue_short' : 'start_short'), 'local_siraj'),
                'actionurl' => $done ? $c['url'] : ($c['nexturl'] ?: $c['url']),
                'primary' => !$done && ($overdue || $duesoon),
                'duedate' => $c['due'] ? userdate($c['due'], '%d.%m.%Y') : '',
                'dueday' => $c['due'] ? userdate($c['due'], '%d') : '', 'duemonth' => $c['due'] ? userdate($c['due'], '%b') : '',
            ];
            $items[] = $item;
            $group = $done ? 'done' : ($overdue ? 'overdue' : (!$c['due'] ? 'nodate' : ($c['due'] < $now + 7 * DAYSECS ? 'week' : 'later')));
            $groups[$group][] = $item;
            $counts['all']++;
            $counts[$c['mandatory'] ? 'mandatory' : 'optional']++;
            $counts[$status]++;
            $counts['duesoon'] += $duesoon ? 1 : 0;
        }
        $agenda = [];
        foreach ($groups as $key => $list) {
            if ($list) {
                $agenda[] = ['key' => $key, 'title' => get_string('agenda_' . $key, 'local_siraj'), 'items' => $list, 'count' => count($list)];
            }
        }
        return [
            'items' => $items, 'hasitems' => !empty($items), 'agenda' => $agenda, 'counts' => $counts,
            'exploreurl' => (new \moodle_url('/local/siraj/explore.php'))->out(false),
            'classicurl' => (new \moodle_url('/my/courses.php'))->out(false),
            'icon_list' => icons::svg('menu'), 'icon_calendar' => icons::svg('calendar'), 'icon_arrow' => icons::svg('arrow-right'),
            'icon_search' => icons::svg('search'), 'icon_compass' => icons::svg('compass'),
        ];
    }
}
