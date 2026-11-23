<?php
/**
 * Certificate codes: the reference printed on a Siraj certificate, recorded so anyone can check it on the
 * public verify page (Waymark's approach).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Issue and look up certificate codes.
 */
class certs {
    /**
     * The code for a learner, course and completion time (stable: the same inputs give the same code).
     *
     * @param int $userid
     * @param int $courseid
     * @param int $completedon
     * @return string e.g. 3F9A-0C21-77BD
     */
    public static function code(int $userid, int $courseid, int $completedon): string {
        global $CFG;
        $raw = strtoupper(substr(hash('sha256', $userid . '|' . $courseid . '|' . $completedon . '|' . $CFG->siteidentifier), 0, 12));
        return implode('-', str_split($raw, 4));
    }

    /**
     * Record a certificate (once) and return its code.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $completedon
     * @return string
     */
    public static function issue(int $userid, int $courseid, int $completedon): string {
        global $DB;
        $code = self::code($userid, $courseid, $completedon);
        if (!$DB->record_exists('local_siraj_cert', ['code' => $code])) {
            try {
                $DB->insert_record('local_siraj_cert', (object)['code' => $code, 'userid' => $userid, 'courseid' => $courseid,
                    'timecompleted' => $completedon, 'timecreated' => time()]);
            } catch (\dml_write_exception $e) {
                // Two requests issued it at once; the other one won.
                unset($e);
            }
        }
        return $code;
    }

    /**
     * Look up a code typed by anyone. Accepts it with or without dashes, any case.
     *
     * @param string $typed
     * @return \stdClass|null the record, with learner and course names
     */
    public static function find(string $typed): ?\stdClass {
        global $DB;
        $raw = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $typed));
        if (strlen($raw) !== 12) {
            return null;
        }
        $cert = $DB->get_record('local_siraj_cert', ['code' => implode('-', str_split($raw, 4))]);
        if (!$cert) {
            return null;
        }
        $user = \core_user::get_user($cert->userid);
        $course = $DB->get_record('course', ['id' => $cert->courseid], 'id, fullname');
        if (!$user || $user->deleted || !$course) {
            return null;
        }
        $cert->learner = fullname($user);
        $cert->course = format_string($course->fullname, true, ['context' => \context_course::instance($course->id), 'escape' => false]);
        return $cert;
    }
}
