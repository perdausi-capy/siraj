<?php
/**
 * Reports for managers and client admins: headline numbers, the pace of learning week by week, how each
 * department and course is doing, and a full export. Based on Waymark's reports, on standard Moodle data.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Builds report numbers for a set of people.
 */
class reports {
    /** @var int Weeks shown on the pace chart. */
    const WEEKS = 12;
    /** @var int Above this many people a live report gets slow; ask for a department. */
    const MAXPEOPLE = 400;

    /**
     * Everything the reports screen needs.
     *
     * @param int[] $userids
     * @param int|null $now
     * @return array
     */
    public static function build(array $userids, ?int $now = null): array {
        $now = $now ?? time();
        $people = [];
        $courses = [];
        foreach ($userids as $uid) {
            $p = team::person($uid, $now);
            $people[$uid] = $p;
            foreach ($p['courses'] as $c) {
                $row = &$courses[$c['id']];
                $row = $row ?? ['id' => $c['id'], 'name' => $c['name'], 'mandatory' => $c['mandatory'], 'enrolled' => 0,
                    'finished' => 0, 'inprogress' => 0, 'notstarted' => 0, 'overdue' => 0, 'progress' => 0];
                $row['enrolled']++;
                $row['finished'] += $c['finished'] ? 1 : 0;
                $row['inprogress'] += !$c['finished'] && $c['done'] > 0 ? 1 : 0;
                $row['notstarted'] += $c['done'] == 0 ? 1 : 0;
                $row['overdue'] += $c['overdue'] ? 1 : 0;
                $row['progress'] += $c['progress'];
                unset($row);
            }
        }
        foreach ($courses as &$c) {
            $c['pct'] = $c['enrolled'] ? (int)round(100 * $c['finished'] / $c['enrolled']) : 0;
            $c['avg'] = $c['enrolled'] ? (int)round($c['progress'] / $c['enrolled']) : 0;
        }
        unset($c);
        uasort($courses, fn($a, $b) => [!$a['mandatory'], $a['pct']] <=> [!$b['mandatory'], $b['pct']]);

        // Compliance: share of mandatory course places that are finished.
        $mand = array_filter($courses, fn($c) => $c['mandatory']);
        $places = array_sum(array_column($mand, 'enrolled'));
        $finished = array_sum(array_column($mand, 'finished'));
        $statuses = array_count_values(array_column($people, 'status'));
        $active = count(array_filter($people, fn($p) => $p['lastaccess'] > $now - 7 * DAYSECS));
        $completions = 0;
        foreach ($people as $p) {
            foreach ($p['courses'] as $c) {
                $completions += $c['finished'] && $c['completedon'] > $now - 30 * DAYSECS ? 1 : 0;
            }
        }

        return [
            'headline' => [
                'people' => count($people),
                'compliance' => $places ? (int)round(100 * $finished / $places) : 0,
                'places' => $places, 'placesdone' => $finished,
                'overdue' => $statuses['overdue'] ?? 0,
                'atrisk' => $statuses['atrisk'] ?? 0,
                'active' => $active,
                'completions' => $completions,
            ],
            'statuses' => $statuses,
            'weeks' => self::weeks($userids, $now),
            'departments' => self::departments($people),
            'courses' => array_values($courses),
            'people' => $people,
        ];
    }

    /**
     * Activities finished and people learning, each week, oldest first, ending with this week.
     *
     * @param int[] $userids
     * @param int $now
     * @return array[] each with start (timestamp), n (activities) and learners
     */
    public static function weeks(array $userids, int $now): array {
        global $DB;
        $tz = \core_date::get_user_timezone_object();
        $monday = (new \DateTimeImmutable('@' . $now))->setTimezone($tz)->modify('monday this week')->setTime(0, 0);
        $first = $monday->modify('-' . (self::WEEKS - 1) . ' weeks');
        $out = [];
        for ($i = 0; $i < self::WEEKS; $i++) {
            $out[] = ['start' => $first->modify('+' . $i . ' weeks')->getTimestamp(), 'n' => 0, 'learners' => []];
        }
        if ($userids) {
            [$in, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $params['from'] = $first->getTimestamp();
            $rs = $DB->get_recordset_select('course_modules_completion',
                "userid $in AND completionstate > 0 AND timemodified >= :from", $params, '', 'id, userid, timemodified');
            foreach ($rs as $r) {
                $i = (int)floor(((int)$r->timemodified - $first->getTimestamp()) / WEEKSECS);
                if (isset($out[$i])) {
                    $out[$i]['n']++;
                    $out[$i]['learners'][$r->userid] = true;
                }
            }
            $rs->close();
        }
        foreach ($out as &$w) {
            $w['learners'] = count($w['learners']);
        }
        return $out;
    }

    /**
     * Compliance by department.
     *
     * @param array $people userid => team::person()
     * @return array[]
     */
    protected static function departments(array $people): array {
        $out = [];
        foreach ($people as $p) {
            $name = $p['department'] !== '' ? $p['department'] : get_string('nodepartment', 'local_siraj');
            $d = &$out[$name];
            $d = $d ?? ['name' => $name, 'people' => 0, 'places' => 0, 'finished' => 0, 'overdue' => 0, 'atrisk' => 0];
            $d['people']++;
            foreach ($p['courses'] as $c) {
                if ($c['mandatory']) {
                    $d['places']++;
                    $d['finished'] += $c['finished'] ? 1 : 0;
                }
            }
            $d['overdue'] += $p['status'] === 'overdue' ? 1 : 0;
            $d['atrisk'] += $p['status'] === 'atrisk' ? 1 : 0;
            unset($d);
        }
        foreach ($out as &$d) {
            $d['pct'] = $d['places'] ? (int)round(100 * $d['finished'] / $d['places']) : 0;
        }
        unset($d);
        ksort($out);
        return array_values($out);
    }

    /**
     * Export columns.
     *
     * @return string[]
     */
    public static function export_columns(): array {
        $cols = [];
        foreach (['col_person', 'col_email', 'col_department', 'col_jobrole', 'col_course', 'col_mandatory', 'col_done',
                  'col_total', 'col_progress', 'col_due', 'col_coursestatus', 'col_completed', 'col_personstatus',
                  'col_lastseen'] as $s) {
            $cols[$s] = get_string($s, 'local_siraj');
        }
        return $cols;
    }

    /**
     * Export rows: one per person and course.
     *
     * @param array $people userid => team::person()
     * @return \Generator
     */
    public static function export_rows(array $people): \Generator {
        $roles = setup::field_values(setup::FIELD_JOBROLE, array_keys($people));
        $now = time();
        foreach ($people as $uid => $p) {
            foreach ($p['courses'] as $c) {
                $state = $c['finished'] ? 'cs_done' : ($c['overdue'] ? 'cs_overdue' : ($c['done'] ? 'cs_inprogress' : 'cs_notstarted'));
                yield [
                    $p['fullname'], $p['user']->email, $p['department'], $roles[$uid] ?? '', $c['name'],
                    get_string($c['mandatory'] ? 'yes' : 'no'), $c['done'], $c['total'], $c['progress'] . '%',
                    $c['due'] ? userdate($c['due'], '%Y-%m-%d') : '', get_string($state, 'local_siraj'),
                    $c['finished'] && $c['completedon'] ? userdate($c['completedon'], '%Y-%m-%d') : '',
                    get_string('st_' . $p['status'], 'local_siraj'),
                    $p['lastaccess'] ? userdate($p['lastaccess'], '%Y-%m-%d') : '',
                ];
            }
        }
    }

    /**
     * Chart data for the weekly pace chart (read by local_siraj/charts).
     *
     * @param array[] $weeks
     * @return string JSON
     */
    public static function weeks_chart(array $weeks): string {
        return json_encode([
            'type' => 'bar',
            'labels' => array_map(fn($w) => userdate($w['start'], '%d %b'), $weeks),
            'series' => [
                ['label' => get_string('chart_activities', 'local_siraj'), 'data' => array_column($weeks, 'n'), 'color' => 'mint'],
                ['label' => get_string('chart_learners', 'local_siraj'), 'data' => array_column($weeks, 'learners'),
                    'color' => 'blue', 'type' => 'line', 'axis' => 'y1'],
            ],
        ]);
    }

    /**
     * A status ring (CSS conic gradient) and its legend.
     *
     * @param array $statuses status => count
     * @return array
     */
    public static function ring(array $statuses): array {
        $colors = ['overdue' => 'var(--si-red)', 'atrisk' => '#E9A23B', 'notstarted' => 'var(--si-faint)',
            'ontrack' => 'var(--si-blue)', 'done' => 'var(--si-mint)'];
        $total = array_sum(array_intersect_key($statuses, $colors));
        $stops = [];
        $legend = [];
        $at = 0;
        foreach ($colors as $status => $color) {
            $n = $statuses[$status] ?? 0;
            $legend[] = team::chip($status) + ['n' => $n, 'color' => $color,
                'pct' => $total ? (int)round(100 * $n / $total) : 0];
            if ($n && $total) {
                $to = $at + 100 * $n / $total;
                $stops[] = sprintf('%s %.2f%% %.2f%%', $color, $at, $to);
                $at = $to;
            }
        }
        $done = $statuses['done'] ?? 0;
        $ontrack = $statuses['ontrack'] ?? 0;
        return [
            'style' => 'background: conic-gradient(' . ($stops ? implode(', ', $stops) : 'var(--si-surface-2) 0 100%') . ')',
            'legend' => $legend,
            'centre' => $total ? (int)round(100 * ($done + $ontrack) / $total) : 0,
            'total' => $total,
        ];
    }
}
