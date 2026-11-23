<?php
/**
 * Siraj AI: Siraj's own panel in front of the Capybots assistant (local_capybots + the Capybots hub). Capybots does
 * the answering (course knowledge, role-aware live lookups, quiz protection); Siraj only draws the panel. The browser
 * talks to the hub directly with the short-lived signed pass local_capybots makes for this person and page.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Siraj AI settings for the current page.
 */
class ai {
    /** @var string[] Pages without the assistant: quiz attempts and their review (as Capybots' own widget). */
    const HIDDEN_PAGETYPES = ['mod-quiz-attempt', 'mod-quiz-summary', 'mod-quiz-review', 'mod-quiz-startattempt'];

    /** @var array|null|false Cached for the request (false = not worked out yet). */
    protected static $config = false;

    /**
     * Hub, bot and pass for this person on this page, or null when Siraj AI is not available here (Capybots not
     * installed or connected, the person's role not allowed, a quiz page, or a guest).
     *
     * @return array|null {hub, bot, token, role}
     */
    public static function config(): ?array {
        global $PAGE;
        if (self::$config !== false) {
            return self::$config;
        }
        self::$config = null;
        if (!isloggedin() || isguestuser() || in_array($PAGE->pagetype, self::HIDDEN_PAGETYPES, true)
                || !class_exists('\local_capybots\widget') || !method_exists('\local_capybots\widget', 'pass')) {
            return null;
        }
        try {
            $pass = \local_capybots\widget::pass();
        } catch (\Throwable $e) {
            debugging('Siraj AI: Capybots pass failed: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return null;
        }
        if (empty($pass['hub']) || empty($pass['bot']) || empty($pass['token'])) {
            return null;
        }
        $role = (string)($pass['role'] ?? 'student');
        self::$config = [
            'hub' => rtrim((string)$pass['hub'], '/'),
            'bot' => (string)$pass['bot'],
            'token' => (string)$pass['token'],
            'role' => in_array($role, ['admin', 'manager'], true) ? 'admin' : ($role === 'teacher' ? 'teacher' : 'student'),
        ];
        return self::$config;
    }

    /**
     * Whether Siraj AI works on this page.
     *
     * @return bool
     */
    public static function available(): bool {
        return self::config() !== null;
    }

    /**
     * What the assistant should know about the page: course, activity and the interface language. The lesson's
     * visible text is added in the browser (it is what the learner sees, so it stays an unsigned hint).
     *
     * @return array
     */
    public static function page(): array {
        global $PAGE;
        $page = ['title' => format_string($PAGE->title, true, ['escape' => false]), 'lang' => substr(current_language(), 0, 2)];
        $course = $PAGE->course;
        if ($course && $course->id != SITEID) {
            $context = \context_course::instance($course->id);
            $page['courseId'] = (int)$course->id;
            $page['courseNames'] = array_values(array_filter([
                format_string($course->fullname, true, ['context' => $context, 'escape' => false]),
                format_string($course->shortname, true, ['context' => $context, 'escape' => false]),
            ]));
        }
        if ($PAGE->cm) {
            $page['cmid'] = (int)$PAGE->cm->id;
            $page['activity'] = $PAGE->cm->get_formatted_name(['escape' => false]);
            $page['modname'] = $PAGE->cm->modname;
        }
        return $page;
    }
}
