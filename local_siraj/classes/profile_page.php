<?php
/**
 * Siraj profile (the learner's own): who they are, their learning at a glance, and account shortcuts.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use theme_siraj\icons;

/**
 * Template context for the profile page.
 */
class profile_page {
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
        global $OUTPUT, $PAGE;
        $u = $this->user;
        $context = \context_user::instance($u->id);
        $home = new home($u);
        $courses = array_values(array_filter($home->courses(), fn($c) => $c['total'] > 0));
        usort($courses, fn($a, $b) => [$a['progress'] >= 100, $b['lastaccess']] <=> [$b['progress'] >= 100, $a['lastaccess']]);
        $done = count(array_filter($courses, fn($c) => $c['progress'] >= 100));
        $avg = $courses ? (int)round(array_sum(array_column($courses, 'progress')) / count($courses)) : 0;

        $picture = null;
        if ($u->picture) {
            $picture = (new \user_picture($u))->get_url($PAGE, $OUTPUT)->out(false);
            $picture = str_replace('/f2', '/f3', $picture);
        }
        $initials = \core_text::strtoupper(\core_text::substr($u->firstname, 0, 1) . \core_text::substr($u->lastname, 0, 1));
        $details = array_values(array_filter([
            ['k' => get_string('email'), 'v' => s($u->email)],
            $u->department ? ['k' => get_string('department'), 'v' => s($u->department)] : null,
            $u->institution ? ['k' => get_string('institution'), 'v' => s($u->institution)] : null,
            $u->city ? ['k' => get_string('city'), 'v' => s($u->city) . ($u->country ? ', ' . get_string($u->country, 'countries') : '')] : null,
            ['k' => get_string('timezone'), 'v' => \core_date::get_localised_timezone(\core_date::get_user_timezone($u))],
            ['k' => get_string('membersince', 'local_siraj'), 'v' => userdate($u->firstaccess ?: $u->timecreated, '%d.%m.%Y')],
        ]));
        $link = fn($url, $str, $icon, $comp = 'local_siraj') => ['url' => (new \moodle_url($url))->out(false),
            'text' => get_string($str, $comp), 'icon' => icons::svg($icon)];
        $canedit = has_capability('moodle/user:editownprofile', \context_system::instance());
        $actions = array_values(array_filter([
            $canedit ? $link('/user/edit.php', 'editprofile', 'settings') : null,
            $link('/login/change_password.php', 'changepassword', 'lock'),
            $link('/user/language.php', 'language', 'globe'),
            $link('/message/notificationpreferences.php', 'notificationprefs', 'bell'),
        ]));
        // Everything Moodle lists on its Preferences page (forum, editor, calendar, badges, blogs, plugins...),
        // grouped the same way, so nothing needs Moodle's own page.
        $prefgroups = self::preference_groups(array_column($actions, 'url'));

        return [
            'fullname' => fullname($u), 'initials' => $initials, 'picture' => $picture,
            'sub' => implode(' · ', array_filter([s($u->department), s($u->city)])),
            'description' => $u->description ? format_text($u->description, $u->descriptionformat, ['context' => $context]) : null,
            'details' => $details, 'actions' => $actions, 'prefgroups' => $prefgroups, 'hasprefgroups' => !empty($prefgroups),
            'stats' => [
                ['value' => count($courses), 'label' => get_string('stat_courses', 'local_siraj')],
                ['value' => $done, 'label' => get_string('stat_completed', 'local_siraj')],
                ['value' => $avg . '%', 'label' => get_string('stat_avgprogress', 'local_siraj')],
            ],
            'courses' => array_map(fn($c) => ['name' => $c['name'], 'url' => $c['url'], 'progress' => $c['progress'],
                'coverclass' => $c['coverclass'], 'cover' => $c['cover'], 'category' => $c['category'], 'done' => $c['progress'] >= 100],
                array_slice($courses, 0, 6)),
            'hascourses' => !empty($courses),
            'learningurl' => (new \moodle_url('/local/siraj/learning.php'))->out(false),
            'achievementsurl' => (new \moodle_url('/local/siraj/achievements.php'))->out(false),
            'classicurl' => (new \moodle_url('/user/profile.php', ['id' => $u->id, 'siraj' => 'classic']))->out(false),
            'logouturl' => (new \moodle_url('/login/logout.php', ['sesskey' => sesskey()]))->out(false),
            'icon_arrow' => icons::svg('arrow-right'), 'icon_logout' => icons::svg('logout'), 'icon_check' => icons::svg('check'),
        ];
    }

    /**
     * Moodle's preference links for the current user, grouped as on /user/preferences.php.
     *
     * @param string[] $skip URLs already shown in the main account list
     * @return array
     */
    protected static function preference_groups(array $skip): array {
        global $PAGE;
        $settings = $PAGE->settingsnav->find('usercurrentsettings', null);
        if (!$settings) {
            return [];
        }
        $iconfor = ['forum' => 'message', 'editor' => 'type', 'calendar' => 'calendar', 'badge' => 'award', 'backpack' => 'award',
            'blog' => 'file', 'message' => 'message', 'notification' => 'bell', 'contentbank' => 'folder', 'language' => 'globe',
            'password' => 'lock', 'security' => 'shield', 'key' => 'lock', 'grade' => 'chart', 'course' => 'book', 'competenc' => 'target'];
        $skipped = array_map(fn($u) => preg_replace('/[?#].*$/', '', $u), $skip);
        $item = function(\navigation_node $n) use ($iconfor, $skipped) {
            if (!$n->action) {
                return null;
            }
            $url = $n->action instanceof \moodle_url ? $n->action->out(false) : (string)$n->action;
            if (in_array(preg_replace('/[?#].*$/', '', $url), $skipped, true)) {
                return null;
            }
            $text = strip_tags($n->get_content());
            $icon = 'settings';
            foreach ($iconfor as $k => $i) {
                if (stripos($url . ' ' . $n->key, $k) !== false) {
                    $icon = $i;
                    break;
                }
            }
            return ['text' => $text, 'url' => $url, 'icon' => icons::svg($icon)];
        };
        $groups = [];
        $loose = [];
        foreach ($settings->children as $node) {
            if ($node->has_children()) {
                $items = array_values(array_filter(array_map($item, iterator_to_array($node->children))));
                if ($items) {
                    $groups[] = ['title' => strip_tags($node->get_content()), 'items' => $items];
                }
            } else if ($i = $item($node)) {
                $loose[] = $i;
            }
        }
        if ($loose) {
            $groups[] = ['title' => get_string('preferences'), 'items' => $loose];
        }
        return $groups;
    }
}
