<?php
/**
 * Siraj pages: library callbacks.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serves the Explore banner image (Siraj settings > Featured collection). Anyone who can see Explore may load it.
 *
 * @param stdClass $course
 * @param stdClass|null $cm
 * @param context $context
 * @param string $filearea
 * @param array $args [revision, ...path, filename]
 * @param bool $forcedownload
 * @param array $options
 * @return bool false if not found
 */
function local_siraj_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel != CONTEXT_SYSTEM || $filearea !== 'featuredimage') {
        return false;
    }
    require_login(null, false);
    array_shift($args); // Revision, only there so a new image gets a new address.
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    $file = get_file_storage()->get_file($context->id, 'local_siraj', 'featuredimage', 0, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, YEARSECS, 0, $forcedownload, $options);
}
