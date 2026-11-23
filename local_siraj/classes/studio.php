<?php
/**
 * Admin studio: the whole site at a glance, from aggregate SQL so it stays fast on large sites.
 * Active learners, completions, enrolments, trends, and the health of every course (completion, drop-off,
 * courses nobody is using, courses without completion tracking).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Site-wide numbers.
 */
class studio {
    /** @var int Weeks on the trend chart. */
    const WEEKS = 12;

    /** @var int */
    protected $now;

    /**
     * Constructor.
     *
     * @param int|null $now
     */
    public function __construct(?int $now = null) {
        $this->now = $now ?? time();
    }

    /**
     * Headline numbers.
     *
     * @return array
     */
    public function headline(): array {
        global $DB, $CFG;
        $admins = array_map('intval', explode(',', $CFG->siteadmins));
        [$notin, $params] = $DB->get_in_or_equal(array_merge($admins, [(int)$CFG->siteguest]), SQL_PARAMS_NAMED, 'a', false);
        $base = "deleted = 0 AND suspended = 0 AND id $notin";
        $learners = $DB->count_records_select('user', $base, $params);
        $active7 = $DB->count_records_select('user', "$base AND lastaccess >= :t", $params + ['t' => $this->now - 7 * DAYSECS]);
        $active30 = $DB->count_records_select('user', "$base AND lastaccess >= :t", $params + ['t' => $this->now - 30 * DAYSECS]);
        $acts30 = $DB->count_records_select('course_modules_completion', 'completionstate > 0 AND timemodified >= ?',
            [$this->now - 30 * DAYSECS]);
        $finished = $this->finished();
        $finished30 = count(array_filter($finished, fn($t) => $t >= $this->now - 30 * DAYSECS));
        return [
            'learners' => $learners, 'active7' => $active7, 'active30' => $active30,
            'activepct' => $learners ? (int)round(100 * $active30 / $learners) : 0,
            'activities30' => $acts30, 'finished30' => $finished30, 'finished' => count($finished),
        ];
    }

    /**
     * Course places finished: every tracked activity complete (or Moodle course completion recorded).
     *
     * @return array "userid-courseid" => time finished
     */
    public function finished(): array {
        global $DB;
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $totals = $this->tracked_totals();
        $cache = [];
        $rs = $DB->get_recordset_sql("
            SELECT cmc.userid, cm.course, COUNT(1) AS n, MAX(cmc.timemodified) AS last
              FROM {course_modules_completion} cmc
              JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
             WHERE cmc.completionstate > 0 AND cm.completion > 0 AND cm.deletioninprogress = 0 AND cm.visible = 1
          GROUP BY cmc.userid, cm.course");
        foreach ($rs as $r) {
            if (!empty($totals[$r->course]) && $r->n >= $totals[$r->course]) {
                $cache[$r->userid . '-' . $r->course] = (int)$r->last;
            }
        }
        $rs->close();
        foreach ($DB->get_recordset_select('course_completions', 'timecompleted IS NOT NULL', null, '',
                'id, userid, course, timecompleted') as $r) {
            $cache[$r->userid . '-' . $r->course] = $cache[$r->userid . '-' . $r->course] ?? (int)$r->timecompleted;
        }
        return $cache;
    }

    /**
     * Tracked (completion-enabled, visible) activities per course.
     *
     * @return array courseid => n
     */
    protected function tracked_totals(): array {
        global $DB;
        return array_map('intval', $DB->get_records_sql_menu("
            SELECT course, COUNT(1) FROM {course_modules}
             WHERE completion > 0 AND deletioninprogress = 0 AND visible = 1
          GROUP BY course"));
    }

    /**
     * Weekly trend: activities completed, people learning, new enrolments.
     *
     * @return array[]
     */
    public function weeks(): array {
        global $DB;
        $tz = \core_date::get_user_timezone_object();
        $first = (new \DateTimeImmutable('@' . $this->now))->setTimezone($tz)->modify('monday this week')->setTime(0, 0)
            ->modify('-' . (self::WEEKS - 1) . ' weeks');
        $start = $first->getTimestamp();
        $out = [];
        for ($i = 0; $i < self::WEEKS; $i++) {
            $out[] = ['start' => $first->modify('+' . $i . ' weeks')->getTimestamp(), 'n' => 0, 'learners' => [], 'enrolments' => 0];
        }
        $rs = $DB->get_recordset_select('course_modules_completion', 'completionstate > 0 AND timemodified >= ?', [$start],
            '', 'id, userid, timemodified');
        foreach ($rs as $r) {
            $i = (int)floor(($r->timemodified - $start) / WEEKSECS);
            if (isset($out[$i])) {
                $out[$i]['n']++;
                $out[$i]['learners'][$r->userid] = true;
            }
        }
        $rs->close();
        $rs = $DB->get_recordset_select('user_enrolments', 'timecreated >= ?', [$start], '', 'id, timecreated');
        foreach ($rs as $r) {
            $i = (int)floor(($r->timecreated - $start) / WEEKSECS);
            if (isset($out[$i])) {
                $out[$i]['enrolments']++;
            }
        }
        $rs->close();
        foreach ($out as &$w) {
            $w['learners'] = count($w['learners']);
        }
        return $out;
    }

    /**
     * Trend chart JSON.
     *
     * @param array[] $weeks
     * @return string
     */
    public static function weeks_chart(array $weeks): string {
        return json_encode([
            'type' => 'line',
            'labels' => array_map(fn($w) => userdate($w['start'], '%d %b'), $weeks),
            'series' => [
                ['label' => get_string('chart_activities', 'local_siraj'), 'data' => array_column($weeks, 'n'), 'color' => 'mint', 'fill' => true],
                ['label' => get_string('chart_learners', 'local_siraj'), 'data' => array_column($weeks, 'learners'), 'color' => 'blue'],
                ['label' => get_string('chart_enrolments', 'local_siraj'), 'data' => array_column($weeks, 'enrolments'), 'color' => 'amber'],
            ],
        ]);
    }

    /**
     * Every course's health, worst first.
     *
     * @return array[]
     */
    public function courses(): array {
        global $DB;
        $student = (int)$DB->get_field('role', 'id', ['shortname' => 'student']);
        $enrolled = $DB->get_records_sql_menu("
            SELECT ctx.instanceid, COUNT(DISTINCT ue.userid)
              FROM {user_enrolments} ue
              JOIN {enrol} e ON e.id = ue.enrolid AND e.status = 0
              JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = :lvl
              JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = ue.userid AND ra.roleid = :student
              JOIN {user} u ON u.id = ue.userid AND u.deleted = 0 AND u.suspended = 0
             WHERE ue.status = 0
          GROUP BY ctx.instanceid", ['lvl' => CONTEXT_COURSE, 'student' => $student]);
        $started = $DB->get_records_sql_menu("
            SELECT cm.course, COUNT(DISTINCT cmc.userid)
              FROM {course_modules_completion} cmc
              JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
             WHERE cmc.completionstate > 0
          GROUP BY cm.course");
        $lastact = $DB->get_records_sql_menu("
            SELECT cm.course, MAX(cmc.timemodified)
              FROM {course_modules_completion} cmc
              JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
             WHERE cmc.completionstate > 0
          GROUP BY cm.course");
        $percm = $DB->get_records_sql_menu("
            SELECT cmc.coursemoduleid, COUNT(1)
              FROM {course_modules_completion} cmc
             WHERE cmc.completionstate > 0
          GROUP BY cmc.coursemoduleid");
        $tracked = $this->tracked_totals();
        $finished = [];
        foreach (array_keys($this->finished()) as $key) {
            $cid = (int)explode('-', $key)[1];
            $finished[$cid] = ($finished[$cid] ?? 0) + 1;
        }
        $fields = coursefields::values(array_map('intval', $DB->get_fieldset_select('course', 'id', 'id <> ?', [SITEID])));

        $out = [];
        foreach ($DB->get_records_select('course', 'id <> ?', [SITEID], 'fullname', 'id, fullname, visible, category') as $course) {
            $n = (int)($enrolled[$course->id] ?? 0);
            $fin = min($n, (int)($finished[$course->id] ?? 0));
            $pct = $n ? (int)round(100 * $fin / $n) : 0;
            $last = (int)($lastact[$course->id] ?? 0);
            $flags = [];
            if (empty($tracked[$course->id])) {
                $flags[] = ['text' => get_string('flag_notracking', 'local_siraj'), 'class' => 'amber'];
            } else if ($n && $pct < 30 && !empty($fields[$course->id]['mandatory'])) {
                $flags[] = ['text' => get_string('flag_lowcompletion', 'local_siraj'), 'class' => 'red'];
            }
            if ($n && $course->visible && (!$last || $last < $this->now - 30 * DAYSECS)) {
                $flags[] = ['text' => get_string('flag_quiet', 'local_siraj'), 'class' => ''];
            }
            if (!$course->visible) {
                $flags[] = ['text' => get_string('flag_hidden', 'local_siraj'), 'class' => ''];
            }
            $out[] = [
                'id' => (int)$course->id,
                'name' => format_string($course->fullname, true, ['context' => \context_course::instance($course->id), 'escape' => false]),
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'mandatory' => !empty($fields[$course->id]['mandatory']),
                'enrolled' => $n, 'started' => min($n, (int)($started[$course->id] ?? 0)), 'finished' => $fin, 'pct' => $pct,
                'last' => $last ? team::ago($last) : get_string('never', 'local_siraj'),
                'dropoff' => $n && !empty($tracked[$course->id]) ? $this->dropoff((int)$course->id, $percm) : '',
                'flags' => $flags, 'hasflags' => (bool)$flags,
                'reportsurl' => (new \moodle_url('/report/progress/index.php', ['course' => $course->id]))->out(false),
            ];
        }
        usort($out, fn($a, $b) => [-count($a['flags']), !$a['mandatory'], $a['pct']] <=> [-count($b['flags']), !$b['mandatory'], $b['pct']]);
        return $out;
    }

    /**
     * The activity where the most learners stop: the biggest fall in completions from one tracked activity to the next.
     *
     * @param int $courseid
     * @param array $percm cmid => completions
     * @return string activity name, '' when nobody stops
     */
    protected function dropoff(int $courseid, array $percm): string {
        $modinfo = get_fast_modinfo($courseid, -1);
        $prev = null;
        $worst = 0;
        $name = '';
        foreach ($modinfo->get_cms() as $cm) {
            if (!$cm->completion || !$cm->visible || $cm->deletioninprogress) {
                continue;
            }
            $n = (int)($percm[$cm->id] ?? 0);
            if ($prev !== null && $prev - $n > $worst) {
                $worst = $prev - $n;
                $name = format_string($cm->name, true, ['context' => $cm->context, 'escape' => false]);
            }
            $prev = $n;
        }
        return $name;
    }
}
