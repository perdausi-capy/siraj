<?php
/**
 * Siraj icon set: inline SVG outline icons (paths from Lucide, ISC licence) plus the Siraj flame.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_siraj;

/**
 * Inline SVG icons, so every Siraj screen uses one consistent set.
 */
class icons {
    /** @var array icon name => SVG inner markup */
    private const PATHS = [
        'home' => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'book' => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>',
        'compass' => '<circle cx="12" cy="12" r="10"/><path d="m16.2 7.8-2.1 6.3-6.3 2.1 2.1-6.3z"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="M15.5 12.9 17 22l-5-3-5 3 1.5-9.1"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.8 4H7.2a2 2 0 0 0-1.7 1.1z"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="m7 14 4-4 3 3 5-6"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M6.3 17.7l-1.4 1.4M19.1 4.9l-1.4 1.4"/>',
        'moon' => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9z"/>',
        'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.9 1.9 0 0 0 3.4 0"/>',
        'message' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/>',
        'chevron-left' => '<path d="m15 18-6-6 6-6"/>',
        'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
        'arrow-right' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'layers' => '<path d="m12 2 10 5-10 5L2 7z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'sparkles' => '<path d="M9.9 15.5A2 2 0 0 0 8.5 14l-6.1-1.6a.5.5 0 0 1 0-1L8.5 10A2 2 0 0 0 10 8.5l1.6-6.1a.5.5 0 0 1 1 0L14 8.5A2 2 0 0 0 15.5 10l6.1 1.6a.5.5 0 0 1 0 1L15.5 14a2 2 0 0 0-1.4 1.4l-1.6 6.1a.5.5 0 0 1-1 0z"/><path d="M20 3v4M22 5h-4"/>',
        'settings' => '<path d="M12.2 2h-.4a2 2 0 0 0-2 2v.2a2 2 0 0 1-1 1.7l-.4.3a2 2 0 0 1-2 0l-.2-.1a2 2 0 0 0-2.7.7l-.2.4a2 2 0 0 0 .7 2.7l.2.1a2 2 0 0 1 1 1.7v.5a2 2 0 0 1-1 1.8l-.2.1a2 2 0 0 0-.7 2.7l.2.4a2 2 0 0 0 2.7.7l.2-.1a2 2 0 0 1 2 0l.4.3a2 2 0 0 1 1 1.7v.2a2 2 0 0 0 2 2h.4a2 2 0 0 0 2-2v-.2a2 2 0 0 1 1-1.7l.4-.3a2 2 0 0 1 2 0l.2.1a2 2 0 0 0 2.7-.7l.2-.4a2 2 0 0 0-.7-2.7l-.2-.1a2 2 0 0 1-1-1.8v-.4a2 2 0 0 1 1-1.8l.2-.1a2 2 0 0 0 .7-2.7l-.2-.4a2 2 0 0 0-2.7-.7l-.2.1a2 2 0 0 1-2 0l-.4-.3a2 2 0 0 1-1-1.7V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'pencil' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'play' => '<path d="m6 3 14 9-14 9z"/>',
        'target' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/>',
        'shield' => '<path d="M20 13c0 5-3.5 7.5-7.7 9a1 1 0 0 1-.7 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.2-2.7a1.2 1.2 0 0 1 1.6 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"/>',
        'panel' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/>',
        'external' => '<path d="M15 3h6v6M10 14 21 3M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'trending' => '<path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>',
        'file' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4M10 9H8M16 13H8M16 17H8"/>',
        'help' => '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/>',
        'upload' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>',
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'arrow-left' => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'star' => '<path d="M11.5 2.9a.6.6 0 0 1 1 0l2.3 4.7 5.2.8a.6.6 0 0 1 .3 1l-3.8 3.7.9 5.2a.6.6 0 0 1-.8.6L12 16.4l-4.6 2.5a.6.6 0 0 1-.8-.6l.9-5.2-3.8-3.7a.6.6 0 0 1 .3-1l5.2-.8z"/>',
        'tag' => '<path d="M12.6 2.6A2 2 0 0 0 11.2 2H4a2 2 0 0 0-2 2v7.2a2 2 0 0 0 .6 1.4l8.7 8.7a2.4 2.4 0 0 0 3.4 0l6.6-6.6a2.4 2.4 0 0 0 0-3.4z"/><circle cx="7.5" cy="7.5" r="1"/>',
        'bookmark' => '<path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>',
        'type' => '<path d="M4 7V4h16v3M9 20h6M12 4v16"/>',
        'megaphone' => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>',
        'folder' => '<path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.7-.9l-.8-1.2A2 2 0 0 0 7.9 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
    ];

    /** @var string[] Dashboard block => icon (block headers and the "Add a block" cards). */
    const BLOCKS = [
        'admin_bookmarks' => 'bookmark', 'badges' => 'award', 'calendar_month' => 'calendar', 'calendar_upcoming' => 'clock',
        'comments' => 'message', 'course_list' => 'list', 'globalsearch' => 'search', 'html' => 'type', 'lp' => 'target',
        'mentees' => 'users', 'myoverview' => 'grid', 'myprofile' => 'user', 'navigation' => 'compass', 'news_items' => 'megaphone',
        'online_users' => 'globe', 'private_files' => 'folder', 'recentlyaccessedcourses' => 'play',
        'recentlyaccesseditems' => 'play', 'glossary_random' => 'sparkles', 'settings' => 'settings', 'starredcourses' => 'star',
        'tags' => 'tag', 'timeline' => 'trending', 'rss_client' => 'external', 'feedback' => 'message', 'tag_flickr' => 'tag',
    ];

    /**
     * CSS giving every block its icon as a --si-bicon mask (one source for the paths: this class).
     *
     * @return string
     */
    public static function block_icon_css(): string {
        $url = function(string $name): string {
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" '
                . 'stroke-linecap="round" stroke-linejoin="round">' . (self::PATHS[$name] ?? self::PATHS['layers']) . '</svg>';
            return 'url("data:image/svg+xml,' . rawurlencode($svg) . '")';
        };
        $css = '.block, .siraj-bicon { --si-bicon: ' . $url('layers') . "; }\n";
        foreach (self::BLOCKS as $block => $icon) {
            $css .= ".block_{$block}, .siraj-bicon[data-block=\"{$block}\"] { --si-bicon: " . $url($icon) . "; }\n";
        }
        return $css;
    }

    /** @var string[] Icons that are mirrored for right-to-left languages. */
    const DIRECTIONAL = ['arrow-right', 'arrow-left', 'chevron-left', 'chevron-right', 'logout', 'external'];

    /**
     * Inline SVG for an icon.
     *
     * @param string $name icon name
     * @param string $class extra CSS class
     * @return string HTML
     */
    public static function svg(string $name, string $class = ''): string {
        $paths = self::PATHS[$name] ?? self::PATHS['layers'];
        // Arrows point the other way in right-to-left languages.
        if (in_array($name, self::DIRECTIONAL, true)) {
            $class = trim('si-dir ' . $class);
        }
        return '<svg class="si-icon ' . s($class) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'
            . $paths . '</svg>';
    }

    /**
     * The Siraj flame mark (gradient, not an outline icon).
     *
     * @param string $class extra CSS class
     * @return string HTML
     */
    public static function flame(string $class = ''): string {
        $id = 'siflame' . substr(md5($class . random_int(0, PHP_INT_MAX)), 0, 6);
        return '<svg class="si-flame ' . s($class) . '" viewBox="0 0 30 36" aria-hidden="true" focusable="false">'
            . '<defs><linearGradient id="' . $id . '" x1="0.2" y1="0" x2="0.8" y2="1">'
            . '<stop offset="0" stop-color="#00E69A"/><stop offset=".5" stop-color="#33CCFF"/><stop offset="1" stop-color="#3A70B8"/>'
            . '</linearGradient></defs>'
            . '<path fill="url(#' . $id . ')" d="M17 0C10 5 6 11 8.5 16.5c1.3 2.8 4.6 4.3 7.6 5.3 4.4 1.5 6.2 4.6 3.4 9.1-1 1.7-2.6 3.3-4.5 5.1 6.3-2.6 11.4-7.6 12.3-13 .8-4.8-2-8.6-7.7-10.3-3.4-1-6-2.6-6.3-5.4C13.1 5 14.5 2.6 17 0Z"/>'
            . '</svg>';
    }
}
