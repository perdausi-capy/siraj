<?php
/**
 * The Siraj app shell: left sidebar, top bar and phone tab bar around every page.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_siraj\output;

use theme_siraj\icons;

/**
 * Builds the template context for the shell.
 */
class shell {
    /**
     * Navigation items. Each item: key, string, url, icon, and the URL paths that make it "active".
     *
     * @return array[] sections, each with 'label' and 'items'
     */
    protected static function nav_definition(): array {
        global $CFG;
        $siraj = file_exists($CFG->dirroot . '/local/siraj/index.php');
        $home = $siraj ? '/local/siraj/index.php' : '/my/';
        $learn = [
            ['home', 'nav_home', $home, 'home', ['=' . $home, '=/my/index.php', '=/my/']],
            ['learning', 'nav_mylearning', $siraj ? '/local/siraj/learning.php' : '/my/courses.php', 'book',
                ['/local/siraj/learning.php', '/my/courses.php', '/course/view.php', '/mod/', '/local/siraj/learn.php', '/local/siraj/course.php']],
            ['explore', 'nav_explore', $siraj ? '/local/siraj/explore.php' : '/course/index.php', 'compass',
                ['/local/siraj/explore.php', '/course/index.php', '/course/search.php', '/enrol/']],
            ['calendar', 'nav_calendar', '/calendar/view.php?view=month', 'calendar', ['/calendar/']],
        ];
        if ($siraj && class_exists(\local_siraj\teaching_page::class) && isloggedin() && !isguestuser()
                && \local_siraj\teaching_page::courses_taught((int)$GLOBALS['USER']->id)) {
            // Teaching sits after My learning: the teacher's home, and where Moodle's grading tools are reached from.
            array_splice($learn, 2, 0, [['teaching', 'nav_teaching', '/local/siraj/teaching.php', 'users',
                ['/local/siraj/teaching.php', '/grade/report/grader/', '/user/index.php', '/report/progress/']]]);
        }
        if ($siraj || !empty($CFG->enablebadges)) {
            $learn[] = ['achievements', 'nav_achievements', $siraj ? '/local/siraj/achievements.php' : '/badges/mybadges.php', 'award',
                ['/local/siraj/achievements.php', '/local/siraj/certificate.php', '/badges/']];
        }
        $learn[] = ['inbox', 'nav_inbox', $siraj ? '/local/siraj/inbox.php' : '/message/output/popup/notifications.php', 'inbox',
            ['/local/siraj/inbox.php', '/message/']];
        $sections = [['label' => 'nav_workspace', 'items' => $learn]];

        $manage = $siraj && class_exists(\local_siraj\nav::class) ? \local_siraj\nav::management() : [];
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $manage[] = ['reports', 'nav_reports', '/reportbuilder/index.php', 'file', ['/reportbuilder/', '/report/']];
        }
        if ($manage) {
            $sections[] = ['label' => 'nav_management', 'items' => $manage];
        }
        return $sections;
    }

    /**
     * Context for the shell templates.
     *
     * @param \moodle_page $page
     * @return array
     */
    /**
     * Edit mode toggle for the bottom of the sidebar, where Moodle would show its switch: Moodle pages that allow
     * editing, and Siraj's Home (the dashboard). Posts to Moodle's own editmode.php.
     *
     * @param \moodle_page $page
     * @param bool $loggedin
     * @return array|null
     */
    protected static function editmode(\moodle_page $page, bool $loggedin): ?array {
        if (!$loggedin || !$page->theme->haseditswitch) {
            return null;
        }
        $siraj = strpos($page->pagetype, 'local-siraj-') === 0 && $page->pagetype !== 'my-index';
        if (!$page->user_allowed_editing() || $siraj) {
            // Staff keep the button in the same place on every page, greyed out where Moodle has nothing to edit,
            // so it doesn't seem to come and go. Learners never see it.
            if (!is_siteadmin() && !(class_exists(\local_siraj\teaching_page::class)
                    && \local_siraj\teaching_page::courses_taught((int)$GLOBALS['USER']->id))) {
                return null;
            }
            return ['disabled' => true, 'label' => get_string('editmode_na', 'theme_siraj'), 'icon' => icons::svg('pencil')];
        }
        $on = $page->user_is_editing();
        return [
            'on' => $on, 'url' => (new \moodle_url('/editmode.php'))->out(false), 'setmode' => $on ? 0 : 1,
            'context' => $page->context->id, 'pageurl' => $page->url->out_as_local_url(false), 'sesskey' => sesskey(),
            'label' => get_string($on ? 'editmode_off' : 'editmode_on', 'theme_siraj'),
            'icon' => icons::svg('pencil'),
        ];
    }

    /**
     * Siraj chat (replaces Moodle's message drawer; data and sending stay Moodle's messaging API).
     *
     * @param bool $loggedin
     * @return array|null
     */
    protected static function chat(bool $loggedin, \moodle_page $page): ?array {
        global $CFG;
        // The Messages page shows the chat in the page itself.
        if (!$loggedin || empty($CFG->messaging) || \core\session\manager::is_loggedinas()
                || $page->pagetype === 'local-siraj-messages') {
            return null;
        }
        return self::chat_context();
    }

    /**
     * Context for theme_siraj/ai (the Siraj AI panel), or null when Siraj AI is not available on this page.
     *
     * @return array|null
     */
    protected static function ai(): ?array {
        if (!class_exists(\local_siraj\ai::class) || !($config = \local_siraj\ai::config())) {
            return null;
        }
        $page = \local_siraj\ai::page();
        return [
            'config' => json_encode($config + ['page' => $page], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
            'context' => $page['activity'] ?? ($page['courseNames'][0] ?? ''),
            'inlesson' => !empty($page['cmid']), 'incourse' => !empty($page['courseId']),
            'icon_sparkles' => icons::svg('sparkles'), 'icon_x' => icons::svg('x'), 'icon_send' => icons::svg('arrow-right'),
            'icon_plus' => icons::svg('plus'), 'icon_check' => icons::svg('check'),
        ];
    }

    /**
     * Context for theme_siraj/chat (the panel, and the Messages page).
     *
     * @return array
     */
    public static function chat_context(): array {
        global $USER, $CFG;
        $unread = \core_message\api::count_unread_conversations($USER);
        $key = 'message_provider_moodle_instantmessage_enabled';
        $processors = (string)get_user_preferences($key, get_config('message', $key) ?: 'popup', $USER);
        $privacy = \core_message\api::get_user_privacy_messaging_preference($USER->id);
        $options = [
            ['value' => \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS, 'text' => get_string('contactableprivacy_onlycontacts', 'message')],
            ['value' => \core_message\api::MESSAGE_PRIVACY_COURSEMEMBER, 'text' => get_string('contactableprivacy_coursemember', 'message')],
        ];
        if (!empty($CFG->messagingallusers)) {
            $options[] = ['value' => \core_message\api::MESSAGE_PRIVACY_SITE, 'text' => get_string('contactableprivacy_site', 'message')];
        }
        foreach ($options as &$o) {
            $o['checked'] = $o['value'] == $privacy;
        }
        return [
            'userid' => $USER->id, 'unread' => $unread, 'hasunread' => $unread > 0,
            'privacy' => $options, 'processors' => $processors,
            'emailoffline' => in_array('email', explode(',', $processors)),
            'entertosend' => (bool)get_user_preferences('message_entertosend', true, $USER),
            'icon_message' => icons::svg('message'), 'icon_x' => icons::svg('x'), 'icon_search' => icons::svg('search'),
            'icon_back' => icons::svg('arrow-left'), 'icon_send' => icons::svg('arrow-right'), 'icon_plus' => icons::svg('plus'),
            'icon_star' => icons::svg('star'), 'icon_external' => icons::svg('external'), 'icon_settings' => icons::svg('settings'),
            'fullurl' => (new \moodle_url('/local/siraj/messages.php'))->out(false),
        ];
    }

    public static function context(\moodle_page $page): array {
        global $USER, $CFG;

        $path = $page->url->out_as_local_url(false);
        $loggedin = isloggedin() && !isguestuser();
        $sections = [];
        $mobile = [];
        $activetitle = '';
        // In a course this person teaches, Teaching is the active section (not My learning).
        $teachingcourse = $loggedin && $page->course && $page->course->id != SITEID && class_exists(\local_siraj\teaching_page::class)
            && isset(\local_siraj\teaching_page::courses_taught((int)$USER->id)[$page->course->id])
            && strpos((string)$page->pagetype, 'local-siraj-') !== 0;
        if ($loggedin) {
            foreach (self::nav_definition() as $section) {
                $items = [];
                foreach ($section['items'] as [$key, $str, $url, $icon, $match]) {
                    $active = false;
                    if ($teachingcourse) {
                        $match = $key === 'teaching' ? ['/'] : ($key === 'learning' ? [] : $match);
                    }
                    $bare = strtok($path, '?');
                    foreach ($match as $m) {
                        if ($m[0] === '=' ? $bare === substr($m, 1) : strpos($path, $m) === 0) {
                            $active = true;
                            break;
                        }
                    }
                    // Only the first matching item is active (course pages belong to "My learning").
                    if ($active && $activetitle !== '') {
                        $active = false;
                    }
                    $text = get_string($str, 'theme_siraj');
                    if ($active) {
                        $activetitle = $text;
                    }
                    $item = ['key' => $key, 'text' => $text, 'url' => (new \moodle_url($url))->out(false),
                        'icon' => icons::svg($icon), 'active' => $active];
                    $items[] = $item;
                    if (in_array($key, ['home', 'learning', 'explore', 'achievements', 'inbox'])) {
                        // On phones the bell is hidden; the Inbox tab carries the unread notifications count.
                        if ($key === 'inbox' && $loggedin) {
                            $n = \message_popup\api::count_unread_popup_notifications($USER->id);
                            $item['badge'] = $n > 99 ? '99+' : ($n ?: null);
                        }
                        $mobile[] = $item;
                    }
                }
                $sections[] = ['label' => get_string($section['label'], 'theme_siraj'), 'items' => $items];
            }
            // Teachers, managers and admins have more than the learner tabs: on phones the last tab opens the full
            // sidebar instead (Achievements is still in it).
            $learnerkeys = ['home', 'learning', 'explore', 'calendar', 'achievements', 'inbox'];
            $extra = array_filter(array_merge(...array_column($sections, 'items')), fn($i) => !in_array($i['key'], $learnerkeys));
            if ($extra) {
                $mobile = array_values(array_filter($mobile, fn($i) => $i['key'] !== 'achievements'));
                $mobile[] = ['key' => 'menu', 'text' => get_string('nav_menu', 'theme_siraj'), 'icon' => icons::svg('menu'),
                    'action' => 'sidenav', 'active' => $activetitle !== '' && !in_array(true, array_column($mobile, 'active'), true)];
            }
        }

        // Page title for the top bar trail: course name inside a course, otherwise the page heading.
        $title = '';
        if ($page->course && $page->course->id != SITEID) {
            $title = format_string($page->course->fullname, true, ['context' => \context_course::instance($page->course->id), 'escape' => false]);
        } else {
            $title = html_entity_decode(strip_tags($page->heading ?: $page->title), ENT_QUOTES, 'UTF-8');
        }
        if ($title === format_string(get_site()->fullname) || $title === format_string(get_site()->shortname)) {
            $title = $activetitle;
        }

        $user = null;
        if ($loggedin) {
            $initials = \core_text::substr($USER->firstname, 0, 1) . \core_text::substr($USER->lastname, 0, 1);
            $sub = array_filter([$USER->department ?? '', $USER->city ?? '']);
            $picture = null;
            if (!empty($USER->picture)) {
                $up = new \user_picture($USER);
                $up->size = 100;
                $picture = $up->get_url($page)->out(false);
            }
            $siraj = file_exists($CFG->dirroot . '/local/siraj/profile.php');
            // Moodle's own user menu items (profile, grades, calendar, private files, reports, preferences, switch
            // role...), with Siraj icons; the profile goes to Siraj's profile. Language has its own switch.
            $iconfor = ['profile' => 'user', 'grades' => 'chart', 'calendar' => 'calendar', 'privatefiles' => 'folder',
                'reports' => 'file', 'preferences' => 'settings', 'switchroleto' => 'users', 'mymoodle' => 'home'];
            $menu = [];
            require_once($CFG->dirroot . '/user/lib.php');
            foreach (user_get_user_navigation_info($USER, $page)->navitems as $item) {
                if ($item->itemtype !== 'link' || empty($item->url)) {
                    continue;
                }
                $id = explode(',', $item->titleidentifier ?? '')[0];
                if (in_array($id, ['logout', 'mymoodle'], true)) {
                    continue;
                }
                $url = $item->url instanceof \moodle_url ? $item->url->out(false) : (string)$item->url;
                if ($id === 'profile' && $siraj) {
                    $url = (new \moodle_url('/local/siraj/profile.php'))->out(false);
                }
                $menu[] = ['text' => $item->title, 'url' => $url, 'icon' => icons::svg($iconfor[$id] ?? 'list')];
            }
            if ($siraj) {
                array_splice($menu, 1, 0, [['text' => get_string('nav_achievements', 'theme_siraj'), 'icon' => icons::svg('award'),
                    'url' => (new \moodle_url('/local/siraj/achievements.php'))->out(false)]]);
            }
            $adminmenu = has_capability('moodle/site:config', \context_system::instance()) ? [
                'text' => get_string('administrationsite'), 'icon' => icons::svg('shield'),
                'url' => (new \moodle_url('/admin/search.php'))->out(false)] : null;
            $user = [
                'firstname' => format_string($USER->firstname),
                'email' => $USER->email,
                'picture' => $picture,
                'menu' => $menu,
                'adminmenu' => $adminmenu,
                'loggedinas' => \core\session\manager::is_loggedinas(),
                'logouturl' => (new \moodle_url('/login/logout.php', ['sesskey' => sesskey()]))->out(false),
                'icon_logout' => icons::svg('logout'),
                'icon_chevron' => icons::svg('chevron-right'),
                'fullname' => fullname($USER),
                'initials' => \core_text::strtoupper($initials),
                'sub' => implode(' · ', $sub),
                'profileurl' => (new \moodle_url(file_exists($CFG->dirroot . '/local/siraj/profile.php') ? '/local/siraj/profile.php' : '/user/profile.php'))->out(false),
            ];
        }

        // Language switcher: every installed language, shown in its own name.
        $languages = [];
        $translations = get_string_manager()->get_list_of_translations();
        if (count($translations) > 1 && file_exists($CFG->dirroot . '/local/siraj/lang.php')) {
            $current = current_language();
            $return = $page->url->out_as_local_url(false);
            foreach ($translations as $code => $name) {
                $languages[] = [
                    'code' => $code, 'name' => trim(preg_replace('/[\s\x{200E}\x{200F}]*\(' . preg_quote($code, '/') . '\)[\s\x{200E}\x{200F}]*$/u', '',
                        html_entity_decode($name, ENT_QUOTES, 'UTF-8'))),
                    'current' => $code === $current, 'short' => \core_text::strtoupper(substr($code, 0, 2)),
                    'url' => (new \moodle_url('/local/siraj/lang.php', ['code' => $code, 'return' => $return,
                        'sesskey' => sesskey()]))->out(false),
                ];
            }
        }

        $logo = $page->get_renderer('core')->get_compact_logo_url(null, 100);
        return [
            'loggedin' => $loggedin,
            'sections' => $sections,
            'mobile' => $mobile,
            'hasmobile' => !empty($mobile),
            'user' => $user,
            'editmode' => self::editmode($page, $loggedin),
            'trailroot' => get_string('trailroot', 'theme_siraj'),
            'pagetitle' => $title,
            'homeurl' => (new \moodle_url($CFG->defaulthomepage && $CFG->defaulthomepage[0] === '/' ?
                $CFG->defaulthomepage : '/my/'))->out(false),
            'logourl' => $logo ? $logo->out(false) : null,
            'flame' => icons::flame('si-brand-flame'),
            'topflame' => icons::flame('si-brand-flame'),
            'searchurl' => (new \moodle_url('/course/search.php'))->out(false),
            // Admins also get "Search settings for …" (Moodle's admin search), first on admin pages.
            'adminsearchurl' => $loggedin && has_capability('moodle/site:config', \context_system::instance()) ?
                (new \moodle_url('/admin/search.php'))->out(false) : null,
            'icon_search' => icons::svg('search'),
            'icon_globe' => icons::svg('globe'),
            'icon_moonplain' => icons::svg('moon'),
            'icon_check' => icons::svg('check'),
            'languages' => $languages,
            'chat' => self::chat($loggedin, $page),
            'ai' => self::ai(),
            'haslanguages' => (bool)$languages,
            'currentlang' => \core_text::strtoupper(substr(current_language(), 0, 2)),
            'icon_sun' => icons::svg('sun', 'si-show-light'),
            'icon_moon' => icons::svg('moon', 'si-show-dark'),
            'icon_collapse' => icons::svg('chevron-left'),
            'icon_navclose' => icons::svg('chevron-right'),
            'icon_menu' => icons::svg('menu'),
            'icon_arrow' => icons::svg('arrow-right'),
            'commands' => self::commands($sections),
        ];
    }

    /**
     * Command palette entries: every nav destination.
     *
     * @param array $sections
     * @return array
     */
    protected static function commands(array $sections): array {
        $out = [];
        foreach ($sections as $section) {
            foreach ($section['items'] as $item) {
                $out[] = ['text' => $item['text'], 'url' => $item['url'], 'icon' => $item['icon']];
            }
        }
        return $out;
    }
}
