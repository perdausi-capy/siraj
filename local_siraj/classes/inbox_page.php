<?php
/**
 * Siraj Inbox: Moodle notifications grouped by day, filtered by kind, with one clear action each.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for the Inbox.
 */
class inbox_page {
    /** @var \stdClass */
    protected $user;

    /**
     * Constructor.
     *
     * @param \stdClass $user
     */
    public function __construct(\stdClass $user) {
        $this->user = $user;
    }

    /**
     * Which tab a notification belongs to.
     *
     * @param \stdClass $n
     * @return string training|achievements|updates
     */
    protected static function kind(\stdClass $n): string {
        if (in_array($n->eventtype, ['coursecompleted', 'badgerecipientnotice']) || $n->component === 'moodle' && $n->eventtype === 'badgerecipientnotice') {
            return 'achievements';
        }
        if (in_array($n->component, ['mod_assign', 'mod_quiz', 'mod_forum', 'mod_feedback', 'mod_lesson', 'mod_scorm', 'local_siraj'])
                || in_array($n->eventtype, ['gradenotifications', 'enrolcoursewelcomemessage'])) {
            return 'training';
        }
        return 'updates';
    }

    /**
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        global $DB;
        $rows = $DB->get_records('notifications', ['useridto' => $this->user->id], 'timecreated DESC', '*', 0, 100);
        $tz = \core_date::get_user_timezone_object($this->user);
        $today = (new \DateTime('today', $tz))->getTimestamp();
        $groups = ['today' => [], 'week' => [], 'earlier' => []];
        $counts = ['all' => 0, 'training' => 0, 'achievements' => 0, 'updates' => 0, 'unread' => 0];
        $icons = ['training' => 'clock', 'achievements' => 'award', 'updates' => 'bell'];
        foreach ($rows as $n) {
            $kind = self::kind($n);
            $unread = empty($n->timeread);
            $item = [
                'id' => $n->id, 'kind' => $kind, 'unread' => $unread,
                'subject' => format_string($n->subject, true, ['escape' => false]),
                'text' => shorten_text(trim(html_to_text($n->smallmessage ?: $n->fullmessage, 0, false)), 180),
                'when' => $n->timecreated >= $today ? userdate($n->timecreated, '%H:%M') : userdate($n->timecreated, '%d.%m.%Y'),
                'ago' => format_time(time() - $n->timecreated),
                'icon' => icons::svg($icons[$kind]),
                'actionurl' => $n->contexturl ? (new \moodle_url('/local/siraj/inbox.php', ['open' => $n->id, 'sesskey' => sesskey()]))->out(false) : null,
                'actionname' => $n->contexturlname ? format_string($n->contexturlname, true, ['escape' => false]) : get_string('open', 'local_siraj'),
                'readurl' => $unread ? (new \moodle_url('/local/siraj/inbox.php', ['read' => $n->id, 'sesskey' => sesskey()]))->out(false) : null,
            ];
            $group = $n->timecreated >= $today ? 'today' : ($n->timecreated >= $today - 6 * DAYSECS ? 'week' : 'earlier');
            $groups[$group][] = $item;
            $counts['all']++;
            $counts[$kind]++;
            $counts['unread'] += $unread ? 1 : 0;
        }
        $out = [];
        foreach ($groups as $key => $items) {
            if ($items) {
                $out[] = ['title' => get_string('inbox_' . $key, 'local_siraj'), 'items' => $items];
            }
        }
        return [
            'groups' => $out, 'hasitems' => !empty($rows), 'counts' => $counts, 'hasunread' => $counts['unread'] > 0,
            'readallurl' => (new \moodle_url('/local/siraj/inbox.php', ['readall' => 1, 'sesskey' => sesskey()]))->out(false),
            'prefsurl' => (new \moodle_url('/message/notificationpreferences.php'))->out(false),
            'icon_check' => icons::svg('check'), 'icon_settings' => icons::svg('settings'), 'icon_arrow' => icons::svg('arrow-right'),
            'icon_inbox' => icons::svg('inbox'),
        ];
    }
}
