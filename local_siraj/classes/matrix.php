<?php
/**
 * The compliance matrix: who must do which course because of their job role, and where each person stands.
 * Waymark's matrix (Capytech), on standard Moodle: job role from the "Job role" profile field, rules in
 * local_siraj_rolereq, progress from Moodle's completion data.
 *
 * Each cell is one person and one course:
 * - missing: their role needs it and they are not enrolled
 * - overdue / todo: enrolled and not finished
 * - done: finished
 * - na: not needed and not enrolled
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Compliance rules and grid.
 */
class matrix {
    /**
     * The rules.
     *
     * @return array role => courseid[]
     */
    public static function rules(): array {
        global $DB;
        $out = [];
        foreach ($DB->get_records('local_siraj_rolereq', null, 'role, courseid') as $r) {
            $out[$r->role][] = (int)$r->courseid;
        }
        return $out;
    }

    /**
     * Replace the rules.
     *
     * @param array $rules role => courseid[]
     */
    public static function set_rules(array $rules): void {
        global $DB;
        $tx = $DB->start_delegated_transaction();
        $DB->delete_records('local_siraj_rolereq');
        foreach ($rules as $role => $courseids) {
            $role = trim(clean_param((string)$role, PARAM_TEXT));
            if ($role === '') {
                continue;
            }
            foreach (array_unique(array_map('intval', $courseids)) as $courseid) {
                if ($courseid > SITEID && $DB->record_exists('course', ['id' => $courseid])) {
                    $DB->insert_record('local_siraj_rolereq', (object)['role' => $role, 'courseid' => $courseid,
                        'timecreated' => time()]);
                }
            }
        }
        $tx->allow_commit();
    }

    /**
     * Courses that can be put in rules: every visible course except the front page.
     *
     * @return array courseid => name
     */
    public static function all_courses(): array {
        global $DB;
        $out = [];
        foreach ($DB->get_records_select('course', 'id <> ?', [SITEID], 'fullname', 'id, fullname, visible') as $c) {
            $out[(int)$c->id] = format_string($c->fullname, true, ['context' => \context_course::instance($c->id), 'escape' => false]);
        }
        return $out;
    }

    /**
     * Job roles in use: from people's profiles and from the rules.
     *
     * @return string[]
     */
    public static function roles(): array {
        global $DB;
        $data = $DB->sql_compare_text('d.data', 255);
        $roles = $DB->get_fieldset_sql("SELECT DISTINCT $data
                                          FROM {user_info_data} d
                                          JOIN {user_info_field} f ON f.id = d.fieldid
                                         WHERE f.shortname = ? AND $data <> ''", [setup::FIELD_JOBROLE]);
        $roles = array_merge(array_map('trim', $roles), array_keys(self::rules()));
        $roles = array_values(array_unique(array_filter($roles)));
        sort($roles);
        return $roles;
    }

    /**
     * The whole grid for some people.
     *
     * @param int[] $userids
     * @param int|null $now
     * @return array columns, rows, counts
     */
    public static function grid(array $userids, ?int $now = null): array {
        global $DB;
        $now = $now ?? time();
        $rules = self::rules();
        $roles = setup::field_values(setup::FIELD_JOBROLE, $userids);
        $people = [];
        $columns = [];
        foreach ($userids as $uid) {
            $people[$uid] = team::person($uid, $now);
            foreach ($people[$uid]['courses'] as $c) {
                if ($c['mandatory']) {
                    $columns[$c['id']] = $c['name'];
                }
            }
        }
        foreach ($rules as $courseids) {
            foreach ($courseids as $cid) {
                if (!isset($columns[$cid]) && ($name = $DB->get_field('course', 'fullname', ['id' => $cid]))) {
                    $columns[$cid] = format_string($name, true, ['escape' => false]);
                }
            }
        }
        asort($columns);
        $counts = ['missing' => 0, 'overdue' => 0, 'todo' => 0, 'done' => 0];
        $missing = array_fill_keys(array_keys($columns), 0);
        $rows = [];
        foreach ($people as $uid => $p) {
            $role = $roles[$uid] ?? '';
            $needed = $rules[$role] ?? [];
            $cells = [];
            foreach ($columns as $cid => $cname) {
                $need = in_array($cid, $needed, true);
                $cell = self::cell($p['courses'][$cid] ?? null, $need);
                $cell['course'] = $cname;
                $cell['courseid'] = (int)$cid;
                $cell['optional'] = !$need && $cell['state'] !== 'na' && empty($p['courses'][$cid]['mandatory']);
                if (isset($counts[$cell['state']])) {
                    $counts[$cell['state']]++;
                }
                if ($cell['state'] === 'missing') {
                    $missing[$cid]++;
                }
                $cells[] = $cell;
            }
            $gaps = count(array_filter($cells, fn($c) => in_array($c['state'], ['missing', 'overdue'], true)));
            $rows[] = ['userid' => $uid, 'name' => $p['fullname'], 'initials' => team::initials($p['user']),
                'role' => $role, 'department' => $p['department'], 'cells' => $cells, 'gaps' => $gaps,
                'url' => (new \moodle_url('/local/siraj/person.php', ['id' => $uid]))->out(false)];
        }
        usort($rows, fn($a, $b) => [$b['gaps'], $a['name']] <=> [$a['gaps'], $b['name']]);
        $cols = [];
        foreach ($columns as $cid => $name) {
            $cols[] = ['id' => $cid, 'name' => $name, 'missing' => $missing[$cid]];
        }
        return ['columns' => $cols, 'rows' => $rows, 'counts' => $counts];
    }

    /**
     * One cell.
     *
     * @param array|null $course their course from team::person(), null when not enrolled
     * @param bool $needed whether their role needs it
     * @return array state, label, detail
     */
    public static function cell(?array $course, bool $needed): array {
        if ($course === null) {
            return $needed ? ['state' => 'missing', 'label' => get_string('mx_missing', 'local_siraj'), 'detail' => '']
                : ['state' => 'na', 'label' => '', 'detail' => ''];
        }
        if ($course['finished']) {
            return ['state' => 'done', 'label' => get_string('mx_done', 'local_siraj'),
                'detail' => $course['completedon'] ? userdate($course['completedon'], '%d.%m.%Y') : ''];
        }
        $label = get_string('mx_progress', 'local_siraj', (object)['n' => $course['done'], 'of' => $course['total']]);
        if ($course['overdue']) {
            return ['state' => 'overdue', 'label' => get_string('mx_overdue', 'local_siraj'), 'detail' => $label];
        }
        return ['state' => 'todo', 'label' => $label,
            'detail' => $course['due'] ? get_string('duedate', 'local_siraj', userdate($course['due'], '%d.%m')) : ''];
    }

    /**
     * Enrol people on a course as students with the manual enrolment method, adding the method if the course has none.
     *
     * @param int[] $userids
     * @param int $courseid
     * @return int how many were enrolled
     */
    public static function enrol(array $userids, int $courseid): int {
        global $DB;
        $plugin = enrol_get_plugin('manual');
        $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual'], '*', IGNORE_MULTIPLE);
        if (!$instance) {
            $plugin->add_instance(get_course($courseid));
            $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'manual'], '*', MUST_EXIST);
        }
        $roleid = (int)($instance->roleid ?: $DB->get_field('role', 'id', ['shortname' => 'student']));
        $context = \context_course::instance($courseid);
        $n = 0;
        foreach ($userids as $uid) {
            if (!is_enrolled($context, $uid)) {
                $plugin->enrol_user($instance, $uid, $roleid);
                $n++;
            }
        }
        return $n;
    }
}
