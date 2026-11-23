<?php
/**
 * Course cover images, kept web-sized: anything over 200 KB or wider than 1200 px is re-saved as a 1200 px JPEG
 * (covers are shown at most ~700 px wide). Runs on course create/update and from seed/optimise_covers.php.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

/**
 * Cover optimiser.
 */
class covers {
    /** Largest width kept. */
    const MAXWIDTH = 1200;
    /** Files under this size and width are left alone. */
    const MAXBYTES = 200 * 1024;

    /**
     * Optimise one course's cover files.
     *
     * @param int $courseid
     * @return int number of files changed
     */
    public static function optimise(int $courseid): int {
        if (!function_exists('imagecreatefromstring')) {
            return 0;
        }
        $context = \context_course::instance($courseid, IGNORE_MISSING);
        if (!$context) {
            return 0;
        }
        $fs = get_file_storage();
        $changed = 0;
        foreach ($fs->get_area_files($context->id, 'course', 'overviewfiles', 0, 'id', false) as $file) {
            if (strpos($file->get_mimetype(), 'image/') !== 0 || $file->get_mimetype() === 'image/svg+xml') {
                continue;
            }
            $info = $file->get_imageinfo();
            if (!$info || ($file->get_filesize() <= self::MAXBYTES && $info['width'] <= self::MAXWIDTH)) {
                continue;
            }
            $src = @imagecreatefromstring($file->get_content());
            if (!$src) {
                continue;
            }
            $w = min(self::MAXWIDTH, $info['width']);
            $h = (int)round($info['height'] * $w / $info['width']);
            $dst = imagecreatetruecolor($w, $h);
            // Transparent PNGs get the Siraj navy behind them rather than black.
            imagefill($dst, 0, 0, imagecolorallocate($dst, 11, 46, 79));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $info['width'], $info['height']);
            ob_start();
            imagejpeg($dst, null, 80);
            $jpeg = ob_get_clean();
            imagedestroy($src);
            imagedestroy($dst);
            if (strlen($jpeg) >= $file->get_filesize()) {
                continue;
            }
            $name = preg_replace('/\.[a-z0-9]+$/i', '', $file->get_filename()) . '.jpg';
            $record = ['contextid' => $context->id, 'component' => 'course', 'filearea' => 'overviewfiles', 'itemid' => 0,
                'filepath' => $file->get_filepath(), 'filename' => $name];
            $file->delete();
            if ($old = $fs->get_file($context->id, 'course', 'overviewfiles', 0, $record['filepath'], $name)) {
                $old->delete();
            }
            $fs->create_file_from_string($record, $jpeg);
            $changed++;
        }
        if ($changed) {
            \cache_helper::purge_by_event('changesincourse');
        }
        return $changed;
    }
}
