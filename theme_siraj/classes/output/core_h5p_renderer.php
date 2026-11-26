<?php
/**
 * H5P in Siraj: adds the theme's H5P stylesheet inside H5P's own frame.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_siraj\output;

/**
 * H5P renderer with the Siraj look (style/h5p.css).
 */
class core_h5p_renderer extends \core_h5p\output\renderer {
    /**
     * Add the Siraj H5P stylesheet after H5P's own (and any custom CSS set in the site settings).
     *
     * @param array $styles
     * @param array $libraries
     * @param string $embedtype
     */
    public function h5p_alter_styles(&$styles, array $libraries, string $embedtype) {
        global $CFG;
        parent::h5p_alter_styles($styles, $libraries, $embedtype);
        $styles[] = (object)['path' => $CFG->wwwroot . '/theme/siraj/style/h5p.css', 'version' => '?ver=' . theme_get_revision()];
    }
}
