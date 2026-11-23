<?php
/**
 * Who reports to whom, in standard Moodle terms (no IOMAD):
 * - the "Line manager" role, assigned in a person's user context (Profile > Preferences > Assign roles relative to this user);
 * - or the "Manager" profile field, holding the manager's username or email (easy to fill from an HR file with Upload users);
 * - the person's department is Moodle's own Department field; their job role is the "Job role" profile field.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Creates the role and profile fields the dashboards read.
 */
class setup {
    /** @var string Line manager role shortname. */
    const ROLE = 'sirajlinemanager';
    /** @var string Profile field: the manager's username or email. */
    const FIELD_MANAGER = 'sirajmanager';
    /** @var string Profile field: job role, used by the compliance matrix. */
    const FIELD_JOBROLE = 'sirajjobrole';

    /**
     * Create anything missing. Safe to run again.
     */
    /** @var string[] Blocks offered on the dashboard (Home) by default; admins change it in the Siraj settings. */
    const DASHBOARD_BLOCKS = ['timeline', 'calendar_upcoming', 'news_items', 'calendar_month', 'myoverview', 'starredcourses',
        'badges', 'html'];

    /**
     * Blocks someone may add to their dashboard.
     *
     * @return string[]
     */
    public static function dashboard_blocks(): array {
        $config = get_config('local_siraj', 'dashboardblocks');
        return $config === false ? self::DASHBOARD_BLOCKS : array_filter(explode(',', $config));
    }

    public static function ensure(): void {
        global $DB, $CFG;
        require_once($CFG->libdir . '/accesslib.php');
        // The capabilities must exist before the role can be given them (install runs before Moodle reads db/access.php).
        update_capabilities('local_siraj');

        $system = \context_system::instance();
        $roleid = $DB->get_field('role', 'id', ['shortname' => self::ROLE]);
        if (!$roleid) {
            $roleid = create_role('Line manager', self::ROLE,
                'Sees the training progress of the people who report to them (Siraj My team). Assign it on each person\'s ' .
                'profile under Preferences > Assign roles relative to this user, or fill the person\'s Manager profile field instead.');
            set_role_contextlevels($roleid, [CONTEXT_USER]);
        }
        foreach (['local/siraj:viewteam', 'moodle/user:viewdetails', 'moodle/user:viewuseractivitiesreport'] as $cap) {
            assign_capability($cap, CAP_ALLOW, $roleid, $system->id, true);
        }

        $catid = $DB->get_field('user_info_category', 'id', ['name' => 'Siraj']);
        if (!$catid) {
            $catid = $DB->insert_record('user_info_category', (object)['name' => 'Siraj',
                'sortorder' => (int)$DB->count_records('user_info_category') + 1]);
        }
        $fields = [
            self::FIELD_MANAGER => ['Manager (username or email)',
                'Who this person reports to. Their manager sees them in My team.'],
            self::FIELD_JOBROLE => ['Job role', 'Used by the compliance matrix to decide which courses this person must do.'],
        ];
        foreach ($fields as $shortname => [$name, $description]) {
            if ($DB->record_exists('user_info_field', ['shortname' => $shortname])) {
                continue;
            }
            $DB->insert_record('user_info_field', (object)[
                'shortname' => $shortname, 'name' => $name, 'datatype' => 'text', 'description' => $description,
                'descriptionformat' => FORMAT_HTML, 'categoryid' => $catid,
                'sortorder' => (int)$DB->count_records('user_info_field', ['categoryid' => $catid]) + 1,
                'required' => 0, 'locked' => 1, 'visible' => 1, 'forceunique' => 0, 'signup' => 0,
                'defaultdata' => '', 'defaultdataformat' => 0, 'param1' => 30, 'param2' => 255, 'param3' => 0,
            ]);
        }
    }

    /**
     * Profile field values for some people.
     *
     * @param string $shortname
     * @param int[] $userids
     * @return array userid => value ('' when not set)
     */
    public static function field_values(string $shortname, array $userids): array {
        global $DB;
        $out = array_fill_keys($userids, '');
        if (!$userids) {
            return $out;
        }
        [$in, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['short'] = $shortname;
        $rows = $DB->get_records_sql("SELECT d.userid, d.data
                                        FROM {user_info_data} d
                                        JOIN {user_info_field} f ON f.id = d.fieldid
                                       WHERE f.shortname = :short AND d.userid $in", $params);
        foreach ($rows as $r) {
            $out[(int)$r->userid] = trim((string)$r->data);
        }
        return $out;
    }

    /**
     * Set a profile field value.
     *
     * @param string $shortname
     * @param int $userid
     * @param string $value
     */
    public static function set_field(string $shortname, int $userid, string $value): void {
        global $DB;
        $fieldid = $DB->get_field('user_info_field', 'id', ['shortname' => $shortname], MUST_EXIST);
        if ($id = $DB->get_field('user_info_data', 'id', ['userid' => $userid, 'fieldid' => $fieldid])) {
            $DB->set_field('user_info_data', 'data', $value, ['id' => $id]);
        } else {
            $DB->insert_record('user_info_data', (object)['userid' => $userid, 'fieldid' => $fieldid, 'data' => $value,
                'dataformat' => 0]);
        }
    }
}
