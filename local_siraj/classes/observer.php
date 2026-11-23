<?php
/**
 * Clears cached dashboard progress when Moodle data changes.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Event observers.
 */
class observer {
    /**
     * One person's progress changed.
     *
     * @param \core\event\base $event
     */
    public static function person_changed(\core\event\base $event): void {
        $userid = $event->relateduserid ?: $event->userid;
        if ($userid) {
            \cache::make('local_siraj', 'person')->delete((int)$userid);
        }
    }

    /**
     * A course's activities, settings or roles changed: everyone may be affected.
     *
     * @param \core\event\base $event
     */
    public static function course_changed(\core\event\base $event): void {
        \cache::make('local_siraj', 'person')->purge();
    }

    /**
     * Signed in after choosing a language on the sign-in page: keep that language and save it to the profile,
     * the same as switching language inside Siraj.
     *
     * @param \core\event\user_loggedin $event
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $SESSION, $USER, $DB;
        $lang = $SESSION->siraj_loginlang ?? '';
        unset($SESSION->siraj_loginlang);
        if (!$lang || (int)$event->userid !== (int)$USER->id || !get_string_manager()->translation_exists($lang, false)) {
            return;
        }
        $SESSION->lang = $lang;
        if ($USER->lang !== $lang) {
            $DB->set_field('user', 'lang', $lang, ['id' => $USER->id]);
            $USER->lang = $lang;
        }
    }

    /**
     * A course was created or updated: keep its cover image web-sized.
     *
     * @param \core\event\base $event
     */
    public static function course_saved(\core\event\base $event): void {
        covers::optimise((int)$event->courseid);
    }
}
