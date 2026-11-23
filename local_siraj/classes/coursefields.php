<?php
/**
 * Siraj course fields: "Mandatory" and "Duration (minutes)", as standard Moodle course custom fields.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_siraj;

use core_course\customfield\course_handler;
use core_customfield\field_controller;

/**
 * Creates and reads the Siraj course custom fields.
 */
class coursefields {
    /** @var string */
    const MANDATORY = 'siraj_mandatory';
    /** @var string */
    const DURATION = 'siraj_duration';

    /**
     * Create the category and fields when missing.
     */
    public static function ensure(): void {
        global $DB;
        $handler = course_handler::create();
        $existing = [];
        foreach ($handler->get_fields() as $f) {
            $existing[$f->get('shortname')] = true;
        }
        if (isset($existing[self::MANDATORY]) && isset($existing[self::DURATION])) {
            return;
        }
        $catid = $DB->get_field('customfield_category', 'id',
            ['component' => 'core_course', 'area' => 'course', 'name' => 'Siraj']);
        if (!$catid) {
            $catid = $handler->create_category('Siraj');
        }
        $category = \core_customfield\category_controller::create($catid);
        $defs = [
            self::MANDATORY => ['checkbox', 'Mandatory', ['required' => 0, 'uniquevalues' => 0, 'checkbydefault' => 0,
                'locked' => 0, 'visibility' => 2]],
            self::DURATION => ['text', 'Duration (minutes)', ['required' => 0, 'uniquevalues' => 0, 'defaultvalue' => '',
                'displaysize' => 5, 'maxlength' => 4, 'ispassword' => 0, 'link' => '', 'locked' => 0, 'visibility' => 2]],
        ];
        foreach ($defs as $shortname => [$type, $name, $config]) {
            if (isset($existing[$shortname])) {
                continue;
            }
            $field = field_controller::create(0, (object)['type' => $type], $category);
            $handler->save_field_configuration($field, (object)[
                'name' => $name, 'shortname' => $shortname, 'description' => '', 'descriptionformat' => FORMAT_HTML,
                'configdata' => $config,
            ]);
        }
    }

    /**
     * Siraj field values for some courses.
     *
     * @param int[] $courseids
     * @return array courseid => ['mandatory' => bool, 'duration' => int|null]
     */
    public static function values(array $courseids): array {
        $out = [];
        if (!$courseids) {
            return $out;
        }
        $data = course_handler::create()->get_instances_data($courseids, true);
        foreach ($courseids as $id) {
            $out[$id] = ['mandatory' => false, 'duration' => null];
            foreach ($data[$id] ?? [] as $d) {
                $short = $d->get_field()->get('shortname');
                if ($short === self::MANDATORY) {
                    $out[$id]['mandatory'] = (bool)$d->get_value();
                } else if ($short === self::DURATION && (int)$d->get_value() > 0) {
                    $out[$id]['duration'] = (int)$d->get_value();
                }
            }
        }
        return $out;
    }
}
