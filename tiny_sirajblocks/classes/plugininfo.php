<?php
/**
 * Siraj blocks: TinyMCE plugin registration.
 *
 * @package   tiny_sirajblocks
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_sirajblocks;

use context;
use editor_tiny\editor;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;

/**
 * The "Siraj blocks" button and Insert menu item.
 */
class plugininfo extends plugin implements plugin_with_buttons, plugin_with_menuitems {
    /**
     * Available to people who can edit rich text where the plugin is enabled.
     *
     * @param context $context
     * @param array $options
     * @param array $fpoptions
     * @param editor|null $editor
     * @return bool
     */
    public static function is_enabled(context $context, array $options, array $fpoptions, ?editor $editor = null): bool {
        return has_capability('tiny/sirajblocks:use', $context);
    }

    /**
     * Toolbar buttons.
     *
     * @return string[]
     */
    public static function get_available_buttons(): array {
        return ['tiny_sirajblocks'];
    }

    /**
     * Menu items.
     *
     * @return string[]
     */
    public static function get_available_menuitems(): array {
        return ['tiny_sirajblocks'];
    }
}
