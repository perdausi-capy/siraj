<?php
/**
 * A manager's team and how each person is doing on their mandatory training.
 * The status rules are Waymark's (Capytech), adapted from IOMAD companies to standard Moodle.
 *
 * Status, worst first:
 * - overdue: a due date in a mandatory course has passed and they have not finished it
 * - at risk: due within 30 days and either nothing done for 14 days, two or more tries at the quiz they are on,
 *   or more activities left than half the days left
 * - not started: nothing done yet
 * - on track, or done
 * People with no mandatory courses are judged on all their courses.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Team membership, per-person status and reminders.
 */
class team {
    /** @var string[] Status order, worst first. */
    const ORDER = ['overdue', 'atrisk', 'notstarted', 'ontrack', 'done', 'none'];
    /** @var int Days before a due date when pace starts to matter. */
    const RISK_WINDOW = 30;
    /** @var int Days without progress that count as stalled. */
    const STALLED = 14;
    /** @var int A second reminder within this many hours is skipped. */
    const REMIND_GAP = 24;

    /** @var array Request cache of members per manager. */
    protected static $members = [];
    /** @var array Request cache of people. */
    protected static $people = [];

    /**
     * The people who report to a manager: by the Line manager role in their user context, or by their
     * Manager profile field naming the manager's username or email. Never the manager themself.
     *
     * @param int $managerid
     * @return int[] user ids
     */
    public static function members(int $managerid): array {
        global $DB;
        if (isset(self::$members[$managerid])) {
            return self::$members[$managerid];
        }
        $ids = [];
        $roles = get_roles_with_capability('local/siraj:viewteam', CAP_ALLOW, \context_system::instance());
        if ($roles) {
            [$rin, $params] = $DB->get_in_or_equal(array_keys($roles), SQL_PARAMS_NAMED);
            $params += ['mid' => $managerid, 'lvl' => CONTEXT_USER];
            $ids = $DB->get_fieldset_sql("SELECT DISTINCT ctx.instanceid
                                            FROM {role_assignments} ra
                                            JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :lvl
                                           WHERE ra.userid = :mid AND ra.roleid $rin", $params);
        }
        $manager = $DB->get_record('user', ['id' => $managerid], 'id, username, email');
        if ($manager) {
            $data = $DB->sql_compare_text('d.data', 255);
            $ids = array_merge($ids, $DB->get_fieldset_sql("
                SELECT d.userid
                  FROM {user_info_data} d
                  JOIN {user_info_field} f ON f.id = d.fieldid
                 WHERE f.shortname = :short AND (LOWER($data) = :uname OR LOWER($data) = :email)",
                ['short' => setup::FIELD_MANAGER, 'uname' => \core_text::strtolower($manager->username),
                 'email' => \core_text::strtolower(trim($manager->email))]));
        }
        $ids = array_values(array_diff(array_unique(array_map('intval', $ids)), [$managerid]));
        if ($ids) {
            [$in, $params] = $DB->get_in_or_equal($ids);
            $ids = array_map('intval', $DB->get_fieldset_select('user', 'id', "id $in AND deleted = 0 AND suspended = 0", $params));
        }
        return self::$members[$managerid] = $ids;
    }

    /**
     * Can this viewer see this person's progress?
     *
     * @param int $viewerid
     * @param int $userid
     * @return bool
     */
    public static function can_view(int $viewerid, int $userid): bool {
        return has_capability('local/siraj:viewreports', \context_system::instance(), $viewerid)
            || in_array($userid, self::members($viewerid), true);
    }

    /**
     * Everyone a report can cover: active learners (not guests, not site admins), optionally one department.
     *
     * @param string $department '' for all
     * @return int[]
     */
    public static function everyone(string $department = ''): array {
        global $DB, $CFG;
        $params = ['guest' => $CFG->siteguest];
        $where = 'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id <> :guest';
        if ($department !== '') {
            $where .= ' AND department = :dept';
            $params['dept'] = $department;
        }
        $admins = array_map('intval', explode(',', $CFG->siteadmins));
        return array_values(array_diff(array_map('intval', $DB->get_fieldset_select('user', 'id', $where, $params)), $admins));
    }

    /**
     * Departments in use (Moodle's own Department field).
     *
     * @return string[]
     */
    public static function departments(): array {
        global $DB;
        return $DB->get_fieldset_sql("SELECT DISTINCT department FROM {user}
                                       WHERE deleted = 0 AND suspended = 0 AND department <> '' ORDER BY department");
    }

    /**
     * How one person is doing.
     *
     * @param int $userid
     * @param int|null $now
     * @return array
     */
    public static function person(int $userid, ?int $now = null): array {
        global $DB;
        if (isset(self::$people[$userid])) {
            return self::$people[$userid];
        }
        $now = $now ?? time();
        $cache = \cache::make('local_siraj', 'person');
        if (($hit = $cache->get($userid)) !== false) {
            // Status depends on the clock (a due date can pass), so work it out again from the cached numbers.
            $hit['status'] = self::status($hit['total'], $hit['done'], $hit['due'], $hit['last'], $hit['tries'], $now);
            $hit['reason'] = self::reason($hit['status'], $hit['total'] - $hit['done'], $hit['due'], $hit['last'], $hit['tries'], $now);
            foreach ($hit['courses'] as &$c) {
                $c['overdue'] = !$c['finished'] && $c['due'] && $c['due'] < $now;
            }
            unset($c);
            return self::$people[$userid] = $hit;
        }
        $user = \core_user::get_user($userid, '*', MUST_EXIST);
        $home = new home($user);
        $home->withcovers = false;
        $courses = [];
        foreach ($home->courses() as $c) {
            // Only courses where they learn (a teacher in a course is not judged on it).
            if ($c['total'] > 0 && has_capability('moodle/course:isincompletionreports', \context_course::instance($c['id']), $userid)) {
                $c['finished'] = $c['done'] >= $c['total'];
                $c['overdue'] = !$c['finished'] && $c['due'] && $c['due'] < $now;
                $courses[$c['id']] = $c;
            }
        }
        $mandatory = array_filter($courses, fn($c) => $c['mandatory']);
        $tracked = $mandatory ?: $courses;

        $total = 0;
        $done = 0;
        $due = 0;
        $nextcourse = null;
        foreach ($tracked as $c) {
            $total += $c['total'];
            $done += $c['done'];
            if (!$c['finished'] && $c['due'] && (!$due || $c['due'] < $due)) {
                $due = (int)$c['due'];
            }
        }
        // The course they should be on: unfinished, soonest due first.
        $open = array_filter($tracked, fn($c) => !$c['finished']);
        usort($open, fn($a, $b) => [$a['due'] ?: PHP_INT_MAX, -$a['progress']] <=> [$b['due'] ?: PHP_INT_MAX, -$b['progress']]);
        $nextcourse = $open[0] ?? null;

        $tries = 0;
        if ($nextcourse && $nextcourse['nextmod'] === 'quiz') {
            $quizid = $DB->get_field('course_modules', 'instance', ['id' => $nextcourse['nextcmid']]);
            $tries = $DB->count_records('quiz_attempts', ['quiz' => $quizid, 'userid' => $userid, 'state' => 'finished']);
        }
        $last = (int)$DB->get_field_sql('SELECT MAX(timemodified) FROM {course_modules_completion}
                                          WHERE userid = ? AND completionstate > 0', [$userid]);
        $status = self::status($total, $done, $due, $last, $tries, $now);
        $required = count($tracked);
        $finished = count(array_filter($tracked, fn($c) => $c['finished']));

        $person = [
            'userid' => $userid, 'user' => $user, 'fullname' => fullname($user),
            'department' => trim((string)$user->department), 'lastaccess' => (int)$user->lastaccess,
            'total' => $total, 'done' => $done, 'due' => $due, 'last' => $last, 'tries' => $tries,
            'required' => $required, 'finished' => $finished, 'mandatoryonly' => (bool)$mandatory,
            'pct' => $total ? (int)round(100 * $done / $total) : 0,
            'next' => $nextcourse ? ($nextcourse['nextname'] ?: $nextcourse['name']) : '',
            'nextcourse' => $nextcourse ? $nextcourse['name'] : '',
            'nexturl' => $nextcourse ? ($nextcourse['nexturl'] ?: $nextcourse['url']) : '',
            'status' => $status,
            'reason' => self::reason($status, $total - $done, $due, $last, $tries, $now),
            'courses' => $courses,
        ];
        $cache->set($userid, $person);
        return self::$people[$userid] = $person;
    }

    /**
     * The status from the rules above.
     *
     * @param int $total activities in tracked courses
     * @param int $done
     * @param int $due earliest due date of an unfinished tracked course, 0 for none
     * @param int $last last progress, 0 for never
     * @param int $tries finished tries at the quiz they are on
     * @param int $now
     * @return string
     */
    public static function status(int $total, int $done, int $due, int $last, int $tries, int $now): string {
        if ($total === 0) {
            return 'none';
        }
        if ($done >= $total) {
            return 'done';
        }
        if ($due && $due < $now) {
            return 'overdue';
        }
        if ($due && $due - $now <= self::RISK_WINDOW * DAYSECS) {
            $daysleft = max(1, ($due - $now) / DAYSECS);
            $stalled = !$last || $now - $last > self::STALLED * DAYSECS;
            if ($stalled || $tries >= 2 || ($total - $done) > $daysleft / 2) {
                return 'atrisk';
            }
        }
        return $done === 0 ? 'notstarted' : 'ontrack';
    }

    /**
     * Why, in a few words.
     *
     * @param string $status
     * @param int $left
     * @param int $due
     * @param int $last
     * @param int $tries
     * @param int $now
     * @return string
     */
    public static function reason(string $status, int $left, int $due, int $last, int $tries, int $now): string {
        if ($status === 'overdue') {
            return plural::str('reason_overdue', (int)ceil(($now - $due) / DAYSECS));
        }
        if ($status !== 'atrisk') {
            return '';
        }
        if ($tries >= 2) {
            return get_string('reason_tries', 'local_siraj', $tries);
        }
        if (!$last) {
            return get_string('reason_never', 'local_siraj');
        }
        if ($now - $last > self::STALLED * DAYSECS) {
            return plural::str('reason_stalled', (int)floor(($now - $last) / DAYSECS));
        }
        return get_string('reason_pace', 'local_siraj', $left);
    }

    /**
     * When each person was last sent a reminder.
     *
     * @param int[] $userids
     * @return array userid => time
     */
    public static function last_reminded(array $userids): array {
        global $DB;
        if (!$userids) {
            return [];
        }
        [$in, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        return array_map('intval', $DB->get_records_sql_menu("SELECT userid, MAX(timecreated)
                                                                 FROM {local_siraj_note}
                                                                WHERE kind = 'reminder' AND userid $in
                                                             GROUP BY userid", $params));
    }

    /**
     * Sends each person a reminder to carry on with their next step, from the manager, and logs it.
     * Skips people who are done, and people reminded in the last day.
     *
     * @param int[] $userids
     * @param \stdClass $from
     * @return array [sent, skipped]
     */
    public static function remind(array $userids, \stdClass $from): array {
        global $DB, $CFG;
        $sent = 0;
        $skipped = 0;
        $recent = self::last_reminded($userids);
        foreach ($userids as $userid) {
            $p = self::person((int)$userid);
            if (in_array($p['status'], ['done', 'none'], true) || ($recent[$userid] ?? 0) > time() - self::REMIND_GAP * HOURSECS) {
                $skipped++;
                continue;
            }
            // Written in the learner's language, not the manager's.
            $previous = force_current_language($p['user']->lang ?: $CFG->lang);
            $a = (object)['manager' => fullname($from), 'course' => $p['nextcourse'], 'next' => $p['next'],
                'due' => $p['due'] ? userdate($p['due'], '%d.%m.%Y') : ''];
            $message = new \core\message\message();
            $message->component = 'local_siraj';
            $message->name = 'reminder';
            $message->userfrom = $from;
            $message->userto = $p['user'];
            $message->subject = get_string('remind_subject', 'local_siraj', $a);
            $message->fullmessage = get_string($a->due ? 'remind_body_due' : 'remind_body', 'local_siraj', $a);
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml = '<p>' . s($message->fullmessage) . '</p>';
            $message->smallmessage = $message->subject;
            $message->notification = 1;
            $message->contexturl = $p['nexturl'];
            $message->contexturlname = $p['next'];
            force_current_language($previous);
            if (message_send($message)) {
                $DB->insert_record('local_siraj_note', (object)['userid' => $userid, 'authorid' => $from->id,
                    'kind' => 'reminder', 'note' => $p['next'], 'timecreated' => time()]);
                $sent++;
            }
        }
        return [$sent, $skipped];
    }

    /**
     * Status label, chip colour and order index.
     *
     * @param string $status
     * @return array
     */
    public static function chip(string $status): array {
        $class = ['overdue' => 'red', 'atrisk' => 'amber', 'notstarted' => '', 'ontrack' => 'blue', 'done' => 'green', 'none' => ''][$status];
        return ['status' => $status, 'label' => get_string('st_' . $status, 'local_siraj'), 'class' => $class];
    }

    /**
     * Initials for an avatar.
     *
     * @param \stdClass $user
     * @return string
     */
    public static function initials(\stdClass $user): string {
        return \core_text::strtoupper(\core_text::substr($user->firstname, 0, 1) . \core_text::substr($user->lastname, 0, 1));
    }

    /**
     * "today", "yesterday", "5 days ago", "never".
     *
     * @param int $time
     * @return string
     */
    public static function ago(int $time): string {
        if (!$time) {
            return get_string('never', 'local_siraj');
        }
        $days = (int)floor((usergetmidnight(time()) - usergetmidnight($time)) / DAYSECS);
        if ($days <= 0) {
            return get_string('today', 'local_siraj');
        }
        return $days == 1 ? get_string('yesterday', 'local_siraj') : plural::str('daysago', $days);
    }
}
