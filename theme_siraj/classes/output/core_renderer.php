<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

namespace theme_siraj\output;

/**
 * Siraj core renderer: Boost's, plus small fixes to what blocks print.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * A block. The site announcements block writes its links by hand (/mod/forum/discuss.php?d=4): point them at
     * Siraj Announcements, so they show the short address and open the Siraj page directly.
     *
     * @param \block_contents $bc
     * @param string $region
     * @return string
     */
    public function block(\block_contents $bc, $region) {
        if (($bc->attributes['data-block'] ?? '') === 'news_items') {
            $root = preg_quote($GLOBALS['CFG']->wwwroot, '~');
            $map = function(array $m): string {
                $params = $m[1] === 'discuss' ? ['id' => (int)$m[2]] : null;
                return 'href="' . (new \moodle_url('/local/siraj/announcements.php', $params))->out() . '"';
            };
            foreach (['content', 'footer'] as $part) {
                if (is_string($bc->$part) && $bc->$part !== '') {
                    $bc->$part = preg_replace_callback('~href="' . $root . '/mod/forum/(discuss|view)\.php\?[df]=([0-9]+)"~',
                        $map, $bc->$part);
                }
            }
        }
        return parent::block($bc, $region);
    }
}
