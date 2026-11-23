<?php
/**
 * Sidebar items for the manager and admin dashboards (read by theme_siraj's shell).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Which dashboards this user can open.
 */
class nav {
    /**
     * Management items: [key, theme_siraj string, url, icon, active paths].
     *
     * @return array[]
     */
    public static function management(): array {
        global $USER;
        if (!isloggedin() || isguestuser()) {
            return [];
        }
        $system = \context_system::instance();
        $all = has_capability('local/siraj:viewreports', $system);
        $hasteam = (bool)team::members((int)$USER->id);
        $items = [];
        if ($hasteam) {
            $items[] = ['team', 'nav_team', '/local/siraj/team.php', 'users', ['/local/siraj/team.php', '/local/siraj/person.php']];
        }
        if ($hasteam || $all) {
            $items[] = ['insights', 'nav_insights', '/local/siraj/reports.php', 'chart', ['/local/siraj/reports.php']];
            $items[] = ['compliance', 'nav_compliance', '/local/siraj/matrix.php', 'shield', ['/local/siraj/matrix.php']];
        }
        if ($all) {
            $items[] = ['studio', 'nav_studio', '/local/siraj/studio.php', 'trending', ['/local/siraj/studio.php']];
        }
        return $items;
    }

    /**
     * Standard set-up for a dashboard page.
     *
     * @param \moodle_url $url
     * @param string $title
     * @param string $name short page name, used for the page type and body class
     */
    public static function page(\moodle_url $url, string $title, string $name): void {
        global $PAGE;
        $PAGE->set_url($url);
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_pagelayout('standard');
        $PAGE->set_pagetype('local-siraj-' . $name);
        $PAGE->add_body_class('siraj-page siraj-dash siraj-' . $name . 'page');
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $PAGE->set_secondary_navigation(false);
    }
}
