<?php
/**
 * Short addresses for Siraj's pages: /home, /courses/12, /learn/40 instead of /local/siraj/index.php and friends,
 * /login/ for the sign-in page, and a few of Moodle's own pages: /calendar/month, /quiz/35 (any activity), /user/8,
 * /course/12, /forum/discuss/4, /preferences, /profile/edit, /badges/mine (FIXED), /admin/logos (a settings page) and
 * /admin/search.
 * Moodle calls url_rewrite() for every link it prints; the web server maps the short address back to the real page
 * (rules in README, "Short addresses"). The old /local/siraj/... addresses keep working.
 *
 * Switched on with $CFG->urlrewriteclass = '\local_siraj\clean_urls' (set_config), only where the server rules exist.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Moodle URL rewriter for Siraj's pages.
 */
class clean_urls implements \core\output\url_rewriter {
    /** @var array Page script => [short path, parameter that becomes the last part of the path (optional)]. */
    const PAGES = [
        'index' => ['home'],
        'learning' => ['learning'],
        'explore' => ['explore'],
        'course' => ['courses', 'id'],
        'learn' => ['learn', 'cmid'],
        'achievements' => ['achievements'],
        'certificate' => ['certificates', 'id'],
        'verify' => ['verify', 'code'],
        'grades' => ['grades', 'id'],
        'inbox' => ['inbox'],
        'messages' => ['messages'],
        'profile' => ['profile'],
        'team' => ['team'],
        'person' => ['people', 'id'],
        'teaching' => ['teaching'],
        'studio' => ['studio'],
        'reports' => ['reports'],
        'matrix' => ['matrix'],
        'announcements' => ['announcements', 'id'],
    ];

    /** @var string[] Activity types whose view page is /<type>/<cmid>. Keep in step with the server rules. */
    const ACTIVITIES = ['assign', 'bigbluebuttonbn', 'book', 'choice', 'data', 'feedback', 'folder', 'forum', 'glossary',
        'h5pactivity', 'imscp', 'lesson', 'lti', 'page', 'quiz', 'resource', 'scorm', 'url', 'wiki', 'workshop'];

    /** @var string[] Calendar views with a short address: /calendar/<view>. */
    const CALENDAR_VIEWS = ['month', 'day', 'upcoming'];

    /** @var array Moodle pages a learner reaches from Siraj's menus => their short address. Keep in step with the server rules. */
    const FIXED = [
        '/user/preferences.php' => '/preferences',
        '/user/language.php' => '/preferences/language',
        '/user/forum.php' => '/preferences/forum',
        '/user/editor.php' => '/preferences/editor',
        '/user/calendar.php' => '/preferences/calendar',
        '/user/contentbank.php' => '/preferences/contentbank',
        '/message/edit.php' => '/preferences/messages',
        '/message/notificationpreferences.php' => '/preferences/notifications',
        '/badges/preferences.php' => '/preferences/badges',
        '/blog/preferences.php' => '/preferences/blog',
        '/user/edit.php' => '/profile/edit',
        '/login/change_password.php' => '/profile/password',
        '/badges/mybadges.php' => '/badges/mine',
        '/badges/mybackpack.php' => '/badges/backpack',
        '/user/files.php' => '/myfiles',
        '/message/output/popup/notifications.php' => '/notifications',
        '/message/index.php' => '/chat',
        '/grade/report/overview/index.php' => '/grades/overview',
        '/admin/tool/dataprivacy/summary.php' => '/data-retention',
        '/login/forgot_password.php' => '/login/forgot',
    ];

    /**
     * The short address for one of Moodle's own pages, or null.
     *
     * @param string $path Path under the site root, e.g. /mod/quiz/view.php
     * @param array $params
     * @return array|null [short path, remaining params]
     */
    protected static function moodle_page(string $path, array $params): ?array {
        global $CFG;
        $id = isset($params['id']) && preg_match('/^[0-9]+$/', (string)$params['id']) ? $params['id'] : null;
        $rest = $params;
        unset($rest['id']);
        if (preg_match('~^/mod/([a-z0-9]+)/view\.php$~', $path, $m) && in_array($m[1], self::ACTIVITIES) && $id) {
            return ['/' . $m[1] . '/' . $id, $rest];
        }
        if ($path === '/user/profile.php' && $id && count($params) === 1) {
            return ['/user/' . $id, []];
        }
        if (isset(self::FIXED[$path])) {
            // These pages default to the front page as their course: no need to say so in the address.
            if ((string)($params['course'] ?? '') === (string)SITEID) {
                unset($params['course']);
            }
            return [self::FIXED[$path], $params];
        }
        if ($path === '/course/view.php' && $id && !isset($params['section'])) {
            return ['/course/' . $id, $rest];
        }
        if ($path === '/course/section.php' && $id) {
            return ['/course/section/' . $id, $rest];
        }
        $d = $params['d'] ?? null;
        if ($path === '/mod/forum/discuss.php' && $d !== null && preg_match('/^[0-9]+$/', (string)$d)) {
            unset($params['d']);
            return ['/forum/discuss/' . $d, $params];
        }
        if ($path === '/calendar/view.php' && in_array($params['view'] ?? '', self::CALENDAR_VIEWS, true)) {
            $view = $params['view'];
            unset($params['view']);
            return ['/calendar/' . $view, $params];
        }
        if ($path === '/admin/search.php') {
            return ['/admin/search', $params];
        }
        $section = (string)($params['section'] ?? '');
        if ($path === '/admin/settings.php' && preg_match('/^[a-z0-9_]+$/', $section) && $section !== 'search'
                && !file_exists($CFG->dirroot . '/admin/' . $section)) {
            unset($params['section']);
            return ['/admin/' . $section, $params];
        }
        return null;
    }

    /**
     * The short address for a Siraj page, or the url unchanged.
     *
     * @param \moodle_url $url
     * @return \moodle_url
     */
    public static function url_rewrite(\moodle_url $url) {
        global $CFG;
        static $root = null;
        if ($root === null) {
            $root = parse_url($CFG->wwwroot) + ['host' => '', 'path' => ''];
            $root['path'] = rtrim($root['path'], '/');
        }
        $path = $url->get_path(false);
        if ($path === $root['path'] . '/login/index.php' && ($url->get_host() === '' || $url->get_host() === $root['host'])) {
            // Sign-in page: /login/ (the folder's own index page, so no server rule is needed).
            return new \moodle_url('/login/', $url->params());
        }
        if ($url->get_host() !== '' && $url->get_host() !== $root['host']) {
            return $url;
        }
        $prefix = $root['path'] . '/local/siraj/';
        if (strpos($path, $prefix) !== 0) {
            $local = $root['path'] !== '' && strpos($path, $root['path'] . '/') === 0 ? substr($path, strlen($root['path'])) : $path;
            if ($short = self::moodle_page($local, $url->params())) {
                return self::build($short[0], $short[1], $url);
            }
            return $url;
        }
        $page = self::PAGES[basename(substr($path, strlen($prefix)), '.php')] ?? null;
        if (!$page || substr($path, -4) !== '.php' || strpos(substr($path, strlen($prefix)), '/') !== false) {
            return $url;
        }
        $params = $url->params();
        $short = '/' . $page[0];
        if (isset($page[1]) && isset($params[$page[1]]) && preg_match('/^[A-Za-z0-9_-]+$/', (string)$params[$page[1]])) {
            $short .= '/' . $params[$page[1]];
            unset($params[$page[1]]);
        }
        return self::build($short, $params, $url);
    }

    /**
     * The short url with the original's anchor.
     *
     * @param string $short
     * @param array $params
     * @param \moodle_url $url The original
     * @return \moodle_url
     */
    protected static function build(string $short, array $params, \moodle_url $url): \moodle_url {
        $out = new \moodle_url($short, $params);
        $anchor = parse_url($url->raw_out(false), PHP_URL_FRAGMENT);
        if ($anchor !== null && $anchor !== false) {
            $out->set_anchor($anchor);
        }
        return $out;
    }

    /**
     * Nothing to add to the page head.
     *
     * @return string
     */
    public static function html_head_setup() {
        return '';
    }
}
