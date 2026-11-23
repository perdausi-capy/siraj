<?php
/**
 * Number-aware strings. Moodle strings take a number but have no plural forms, and Arabic needs five: يوم واحد,
 * يومان, 3-10 أيام, 11-99 يومًا, 100 يوم. A key "x" can have "x_one", "x_two", "x_few", "x_many" and "x_other"
 * versions; the right one is picked for the current language, falling back to "x_other" and then to "x".
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Plural-aware get_string.
 */
class plural {
    /**
     * Plural form of a number in a language (CLDR rules, cardinal).
     *
     * @param int $n
     * @param string $lang
     * @return string one|two|few|many|other
     */
    public static function form(int $n, string $lang): string {
        $n = abs($n);
        if (strpos($lang, 'ar') === 0) {
            $mod = $n % 100;
            if ($n === 1) {
                return 'one';
            } else if ($n === 2) {
                return 'two';
            } else if ($mod >= 3 && $mod <= 10) {
                return 'few';
            } else if ($mod >= 11 && $mod <= 99) {
                return 'many';
            }
            return 'other';
        }
        return $n === 1 ? 'one' : 'other';
    }

    /**
     * The string for a number.
     *
     * @param string $key
     * @param int $n
     * @return string
     */
    public static function str(string $key, int $n): string {
        $sm = get_string_manager();
        foreach ([$key . '_' . self::form($n, current_language()), $key . '_other'] as $k) {
            if ($sm->string_exists($k, 'local_siraj')) {
                return get_string($k, 'local_siraj', $n);
            }
        }
        return get_string($key, 'local_siraj', $n);
    }
}
