<?php
/**
 * Hook callbacks for local_siraj.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Hook callbacks.
 */
class hook_callbacks {
    /**
     * Offer Siraj Home as a choice in Site administration > Appearance > Navigation > Start page.
     *
     * @param \core_user\hook\extend_default_homepage $hook
     */
    public static function extend_default_homepage(\core_user\hook\extend_default_homepage $hook): void {
        $hook->add_option(new \core\url('/local/siraj/index.php'), get_string('pluginname', 'local_siraj'));
    }

    /**
     * Learners who open a course (or its enrolment page) go to the Siraj course overview, which also handles
     * enrolling. Teachers, admins, editing mode and "?siraj=classic" keep Moodle's own pages.
     *
     * @param \core\hook\output\before_http_headers $hook
     */
    public static function before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE, $SESSION;
        // A read-only page (local/siraj/readonly.php) that has a message waiting from a redirect ("Reminder sent")
        // takes the session lock this once, so showing the message also clears it.
        if (defined('READ_ONLY_SESSION') && READ_ONLY_SESSION && !empty($SESSION->notifications)) {
            \core\session\manager::restart_with_write_lock(false);
        }
        $pagetype = (string)$PAGE->pagetype;
        // A language picked on the sign-in page is remembered, so it can follow the person in (Moodle drops the
        // session language at login and uses the profile's). See observer::user_loggedin.
        if ($pagetype === 'login-index' && !isloggedin() && ($picked = optional_param('lang', '', PARAM_SAFEDIR))
                && get_string_manager()->translation_exists($picked, false)) {
            $GLOBALS['SESSION']->siraj_loginlang = $picked;
        }
        // Moodle's notifications and "my badges" pages open the Siraj versions (Inbox, Achievements).
        $replaced = ['message-output-popup-notifications' => '/local/siraj/inbox.php', 'badges-mybadges' => '/local/siraj/achievements.php'];
        // Your own profile opens the Siraj profile (other people's profiles stay Moodle's).
        if ($pagetype === 'user-profile' && optional_param('id', $GLOBALS['USER']->id, PARAM_INT) == $GLOBALS['USER']->id) {
            $replaced['user-profile'] = '/local/siraj/profile.php';
        }
        // Moodle's dashboard (/my/) is Siraj Home, which carries the same dashboard blocks. The site default
        // (/my/indexsys.php) stays Moodle's editor.
        if ($pagetype === 'my-index' && $PAGE->url->get_path() === (new \moodle_url('/my/index.php'))->get_path()) {
            $replaced['my-index'] = '/local/siraj/index.php';
        }
        // Moodle's messages page opens Siraj's (same conversations; ?id= opens a chat with that person).
        if ($pagetype === 'message-index' && !\core\session\manager::is_loggedinas()) {
            $replaced['message-index'] = (new \moodle_url('/local/siraj/messages.php', array_filter([
                'id' => optional_param('id', 0, PARAM_INT), 'convid' => optional_param('convid', 0, PARAM_INT)])))->out(false);
        }
        // Your own Preferences page: everything on it is in the Account panel of the Siraj profile.
        if ($pagetype === 'user-preferences' && optional_param('userid', $GLOBALS['USER']->id, PARAM_INT) == $GLOBALS['USER']->id
                && !\core\session\manager::is_loggedinas()) {
            $replaced['user-preferences'] = (new \moodle_url('/local/siraj/profile.php', null, 'account'))->out(false);
        }
        // Your own message preferences live in the Siraj chat's settings.
        if ($pagetype === 'message-edit' && optional_param('id', $GLOBALS['USER']->id, PARAM_INT) == $GLOBALS['USER']->id
                && !\core\session\manager::is_loggedinas()) {
            $replaced['message-edit'] = (new \moodle_url('/local/siraj/messages.php', null, 'settings'))->out(false);
        }
        // Grades: the overview and your own course report open Siraj Grades (teachers keep Moodle's gradebook).
        if ($pagetype === 'grade-report-overview-index' && optional_param('userid', $GLOBALS['USER']->id, PARAM_INT) == $GLOBALS['USER']->id) {
            $replaced[$pagetype] = '/local/siraj/grades.php';
        }
        if ($pagetype === 'grade-report-user-index' && ($gc = optional_param('id', 0, PARAM_INT))
                && !has_capability('moodle/grade:viewall', \context_course::instance($gc, IGNORE_MISSING) ?: \context_system::instance())
                && optional_param('userid', $GLOBALS['USER']->id, PARAM_INT) == $GLOBALS['USER']->id) {
            $replaced[$pagetype] = (new \moodle_url('/local/siraj/grades.php', ['id' => $gc]))->out(false);
        }
        if (isset($replaced[$pagetype]) && isloggedin() && !isguestuser() && optional_param('siraj', '', PARAM_ALPHA) !== 'classic') {
            redirect(new \moodle_url($replaced[$pagetype]));
        }
        $classic = optional_param('siraj', '', PARAM_ALPHA) === 'classic';
        $learner = isloggedin() && !isguestuser() && !$classic && !$PAGE->user_is_editing();
        // Moodle's course list and search: everyone browses the Siraj library (Explore) instead. In edit mode staff
        // keep Moodle's list (category tools); course management stays at Site administration > Courses.
        if ($learner && in_array($pagetype, ['course-index', 'course-index-category', 'course-search'], true)) {
            redirect(new \moodle_url('/local/siraj/explore.php', array_filter(['q' => optional_param('search', '', PARAM_TEXT)])));
        }
        // An activity's own view page: learners get it in Learning mode (teachers keep Moodle's page for editing).
        // Pages with an action (submitting an assignment, a quiz attempt, a forced download) stay on Moodle.
        $learnable = ['mod-page-view', 'mod-url-view', 'mod-resource-view', 'mod-book-view', 'mod-h5pactivity-view',
            'mod-assign-view', 'mod-quiz-view', 'mod-scorm-view'];
        if ($learner && get_config('local_siraj', 'courseoverview') && in_array($pagetype, $learnable, true) && $PAGE->cm
                && !array_intersect(array_keys($_GET), ['action', 'redirect', 'forceview', 'forcedownload', 'edit', 'mode'])
                && !has_capability('moodle/course:manageactivities', \context_module::instance($PAGE->cm->id))) {
            redirect(new \moodle_url('/local/siraj/learn.php', array_filter(['cmid' => $PAGE->cm->id,
                'chapterid' => $pagetype === 'mod-book-view' ? optional_param('chapterid', 0, PARAM_INT) : 0])));
        }
        if (!get_config('local_siraj', 'courseoverview') || (strpos($pagetype, 'course-view-') !== 0 && $pagetype !== 'enrol-index')) {
            return;
        }
        if (strpos($pagetype, 'course-view-') === 0 && isloggedin() && !isguestuser() && $PAGE->course->id != SITEID
                && has_capability('moodle/course:update', \context_course::instance($PAGE->course->id))) {
            // Staff work on Moodle's course page; a button in its header goes back to the Siraj course page.
            $PAGE->add_header_action(\html_writer::link(new \moodle_url('/local/siraj/course.php', ['id' => $PAGE->course->id]),
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" '
                . 'aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>' . s(get_string('coursepagebtn', 'local_siraj')),
                ['class' => 'siraj-coursepage-btn']));
        }
        if (!isloggedin() || isguestuser() || $PAGE->user_is_editing() || optional_param('siraj', '', PARAM_ALPHA) === 'classic'
                || $PAGE->course->id == SITEID || has_capability('moodle/course:update', \context_course::instance($PAGE->course->id))) {
            return;
        }
        redirect(new \moodle_url('/local/siraj/course.php', ['id' => $PAGE->course->id]));
    }
}
