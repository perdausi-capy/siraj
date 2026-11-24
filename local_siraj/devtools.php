<?php
/**
 * Developer tools: one page listing Moodle's developer-only admin screens (API reference, test tools, code tools).
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_siraj_devtools');
$root = admin_get_root();
$items = [];
foreach (\local_siraj\devtools::PAGES as $name) {
    $node = $root->locate($name);
    if (!$node || !$node->check_access()) {
        continue;
    }
    $items[] = ['name' => $node->visiblename, 'url' => \local_siraj\devtools::url($node)->out(false)];
    if ($node instanceof admin_category) {
        // A group (Experimental): list what is inside it too.
        foreach ($node->get_children() as $child) {
            if ($child->check_access() && !$child->is_hidden() && !($child instanceof admin_category)) {
                $items[] = ['name' => $child->visiblename, 'url' => \local_siraj\devtools::url($child)->out(false), 'sub' => true];
            }
        }
    }
}

echo $OUTPUT->header();
echo html_writer::tag('p', get_string('devtools_desc', 'local_siraj'), ['class' => 'siraj-devtools-intro']);
$rows = '';
foreach ($items as $item) {
    $rows .= html_writer::tag('li', html_writer::link($item['url'], s($item['name'])), ['class' => empty($item['sub']) ? '' : 'sub']);
}
echo html_writer::tag('ul', $rows, ['class' => 'siraj-devtools list-unstyled']);
echo $OUTPUT->footer();
