<?php
/**
 * Learning time, estimated from what Moodle records: course activity in the standard log grouped into study
 * sessions (a gap over 30 minutes starts a new one; each session counts one extra minute for its last page), plus
 * the time SCORM lessons report themselves (cmi total_time), which the log does not see.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Learning time estimate.
 */
class learning_time {
    /** Longest gap still counted as the same session (seconds). */
    const GAP = 30 * MINSECS;
    /** Time added for the last page of each session (seconds). */
    const TAIL = MINSECS;

    /**
     * Minutes spent learning since a time (0 = ever).
     *
     * @param int $userid
     * @param int $since
     * @return int
     */
    public static function minutes(int $userid, int $since = 0): int {
        global $DB;
        $rs = $DB->get_recordset_sql("SELECT id, timecreated FROM {logstore_standard_log}
            WHERE userid = :u AND courseid <> :site AND courseid <> 0 AND timecreated >= :since
         ORDER BY timecreated", ['u' => $userid, 'site' => SITEID, 'since' => $since]);
        $seconds = 0;
        $last = null;
        foreach ($rs as $r) {
            $t = (int)$r->timecreated;
            if ($last === null || $t - $last > self::GAP) {
                $seconds += $last === null ? 0 : self::TAIL;
            } else {
                $seconds += $t - $last;
            }
            $last = $t;
        }
        $rs->close();
        if ($last !== null) {
            $seconds += self::TAIL;
        }
        return (int)round(($seconds + self::scorm_seconds($userid, $since)) / MINSECS);
    }

    /**
     * Time reported by SCORM lessons (SCORM 1.2 cmi.core.total_time, SCORM 2004 cmi.total_time).
     *
     * @param int $userid
     * @param int $since
     * @return int seconds
     */
    protected static function scorm_seconds(int $userid, int $since): int {
        global $DB;
        $values = $DB->get_fieldset_sql("SELECT v.value FROM {scorm_scoes_value} v
            JOIN {scorm_attempt} a ON a.id = v.attemptid
            JOIN {scorm_element} e ON e.id = v.elementid
           WHERE a.userid = :u AND e.element IN ('cmi.core.total_time', 'cmi.total_time') AND v.timemodified >= :since",
            ['u' => $userid, 'since' => $since]);
        $total = 0;
        foreach ($values as $v) {
            if (preg_match('/^(\d+):(\d{2}):(\d{2})(\.\d+)?$/', trim($v), $m)) {
                $total += $m[1] * 3600 + $m[2] * 60 + $m[3];
            } else if (preg_match('/^P(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:([\d.]+)S)?)?$/', trim($v), $m)) {
                $total += ($m[1] ?? 0) * 86400 + ($m[2] ?? 0) * 3600 + ($m[3] ?? 0) * 60 + (int)($m[4] ?? 0);
            }
        }
        return $total;
    }

    /**
     * "1 h 25 min" style text.
     *
     * @param int $minutes
     * @return string
     */
    public static function format(int $minutes): string {
        if ($minutes < 60) {
            return get_string('minutes_short', 'local_siraj', $minutes);
        }
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return $m ? get_string('hm_short', 'local_siraj', (object)['h' => $h, 'm' => $m]) : get_string('hours_short', 'local_siraj', $h);
    }
}
