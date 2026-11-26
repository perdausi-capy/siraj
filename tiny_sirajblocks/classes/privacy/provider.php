<?php
/**
 * Privacy: Siraj blocks stores no personal data.
 *
 * @package   tiny_sirajblocks
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_sirajblocks\privacy;

/**
 * Null provider.
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Reason.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
