<?php
/**
 * Developer tools: Moodle's developer-only admin screens, kept off Site administration > Development and listed on
 * one "Developer tools" page instead (local/siraj/devtools.php).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * The list of developer screens and where each one opens.
 */
class devtools {
    /** @var string[] Admin tree names of the developer screens, in the order they are listed. */
    const PAGES = ['swaggerui', 'testclient', 'experimental', 'profiling', 'thirdpartylibs', 'hooksoverview',
        'tooltemplatelibrary', 'toolxmld', 'toolbehat', 'toolphpunit', 'toolgeneratorcourse', 'toolgeneratortestplan',
        'toolgenerator_runtestscenario'];

    /**
     * Address of an admin tree node.
     *
     * @param \part_of_admin_tree $node
     * @return \moodle_url
     */
    public static function url(\part_of_admin_tree $node): \moodle_url {
        if ($node instanceof \admin_externalpage) {
            return new \moodle_url($node->url);
        }
        if ($node instanceof \admin_category) {
            return new \moodle_url('/admin/category.php', ['category' => $node->name]);
        }
        return new \moodle_url('/admin/settings.php', ['section' => $node->name]);
    }
}
