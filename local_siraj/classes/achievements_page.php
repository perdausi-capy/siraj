<?php
/**
 * Siraj Achievements: certificates for completed courses, earned badges, and learning milestones.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for Achievements.
 */
class achievements_page {
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
     * Template context.
     *
     * @return array
     */
    public function export(): array {
        global $CFG, $DB;
        $home = new home($this->user);
        $courses = array_filter($home->courses(), fn($c) => $c['total'] > 0);
        $done = array_values(array_filter($courses, fn($c) => $c['progress'] >= 100));
        usort($done, fn($a, $b) => $b['completedon'] <=> $a['completedon']);
        $next = array_values(array_filter($courses, fn($c) => $c['progress'] < 100));
        usort($next, fn($a, $b) => [!$a['mandatory'], -$a['progress']] <=> [!$b['mandatory'], -$b['progress']]);

        $certs = [];
        foreach ($done as $c) {
            $certs[] = [
                'name' => $c['name'], 'date' => userdate($c['completedon'], '%d.%m.%Y'), 'category' => $c['category'],
                'mandatory' => $c['mandatory'],
                'code' => certs::issue((int)$this->user->id, (int)$c['id'], (int)$c['completedon']),
                'url' => (new \moodle_url('/local/siraj/certificate.php', ['id' => $c['id']]))->out(false),
                'courseurl' => $c['url'],
            ];
        }

        $badges = [];
        if (!empty($CFG->enablebadges)) {
            require_once($CFG->libdir . '/badgeslib.php');
            foreach (badges_get_user_badges($this->user->id) as $b) {
                $context = $b->type == BADGE_TYPE_SITE ? \context_system::instance() : \context_course::instance($b->courseid);
                $hasimage = get_file_storage()->file_exists($context->id, 'badges', 'badgeimage', $b->id, '/', 'f3.png');
                $badges[] = [
                    'name' => format_string($b->name, true, ['escape' => false]), 'date' => userdate($b->dateissued, '%d.%m.%Y'),
                    'image' => $hasimage ? \moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $b->id, '/', 'f3', false)->out(false) : null,
                    'icon' => icons::svg('award'),
                    // Moodle's badge page fails for a badge without an image, so only link when there is one.
                    'url' => $hasimage ? (new \moodle_url('/badges/badge.php', ['hash' => $b->uniquehash]))->out(false) : null,
                ];
            }
        }

        // Milestones from data Moodle already has.
        $minutes = learning_time::minutes((int)$this->user->id);
        $activities = array_sum(array_map(fn($c) => $c['done'], $courses));
        $days = (int)$DB->get_field_sql("SELECT COUNT(DISTINCT FLOOR((timecreated - :tz) / 86400)) FROM {logstore_standard_log}
            WHERE userid = :u AND courseid <> :site AND timecreated > :from",
            ['u' => $this->user->id, 'site' => SITEID, 'from' => time() - 30 * DAYSECS,
             'tz' => -(new \DateTime('now', \core_date::get_user_timezone_object($this->user)))->getOffset()]);
        $stats = [
            ['value' => count($certs), 'label' => get_string('stat_certificates', 'local_siraj'), 'icon' => icons::svg('award')],
            ['value' => $activities, 'label' => get_string('stat_activities', 'local_siraj'), 'icon' => icons::svg('check-circle')],
            ['value' => learning_time::format($minutes), 'label' => get_string('stat_hours', 'local_siraj'), 'icon' => icons::svg('clock')],
            ['value' => $days, 'label' => get_string('stat_days', 'local_siraj'), 'icon' => icons::svg('calendar')],
        ];

        return [
            'certs' => $certs, 'hascerts' => !empty($certs), 'count' => count($certs) + count($badges),
            'badges' => $badges, 'hasbadges' => !empty($badges),
            'stats' => $stats,
            'next' => array_map(fn($c) => [
                'name' => $c['name'], 'progress' => $c['progress'], 'url' => $c['nexturl'] ?: $c['url'],
                'left' => get_string($c['remaining'] == 1 ? 'oneleft' : 'nleft', 'local_siraj', $c['remaining']),
            ], array_slice($next, 0, 3)),
            'hasnext' => !empty($next),
            'flame' => icons::flame(), 'icon_download' => icons::svg('download'), 'icon_arrow' => icons::svg('arrow-right'),
            'icon_award' => icons::svg('award'),
        ];
    }
}
