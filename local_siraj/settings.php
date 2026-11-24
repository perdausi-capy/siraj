<?php
/**
 * Siraj pages settings.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_siraj', get_string('pluginname', 'local_siraj'));
    $settings->add(new admin_setting_configtext('local_siraj/weeklygoal', get_string('weeklygoal', 'local_siraj'),
        get_string('weeklygoal_desc', 'local_siraj'), 4, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('local_siraj/courseoverview', get_string('courseoverview', 'local_siraj'),
        get_string('courseoverview_desc', 'local_siraj'), 1));
    // Blocks people may add to their dashboard (Home). The rest are left out of "Add a block" on Home only.
    $blocks = [];
    foreach (core_plugin_manager::instance()->get_plugins_of_type('block') as $name => $info) {
        $blocks[$name] = $info->displayname;
    }
    core_collator::asort($blocks);
    $settings->add(new admin_setting_configmulticheckbox('local_siraj/dashboardblocks', get_string('dashboardblocks', 'local_siraj'),
        get_string('dashboardblocks_desc', 'local_siraj'), array_fill_keys(\local_siraj\setup::DASHBOARD_BLOCKS, 1), $blocks));
    // Explore: the featured collection banner.
    $settings->add(new admin_setting_heading('local_siraj/featuredheading', get_string('featuredcollection', 'local_siraj'),
        get_string('featured_desc', 'local_siraj')));
    $collections = [0 => get_string('featured_auto', 'local_siraj'), -1 => get_string('featured_weekly', 'local_siraj')];
    foreach (core_course_category::top()->get_children() as $cat) {
        $collections[$cat->id] = $cat->get_formatted_name();
    }
    $settings->add(new admin_setting_configselect('local_siraj/featuredcat', get_string('featuredcat', 'local_siraj'),
        get_string('featuredcat_desc', 'local_siraj'), 0, $collections));
    $settings->add(new admin_setting_configstoredfile('local_siraj/featuredimage', get_string('featuredimage', 'local_siraj'),
        get_string('featuredimage_desc', 'local_siraj'), 'featuredimage', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.jpeg', '.png', '.webp']]));
    $settings->add(new admin_setting_configtext('local_siraj/featuredtitle', get_string('featuredtitle', 'local_siraj'),
        get_string('featuredtitle_desc', 'local_siraj'), '', PARAM_TEXT));
    $settings->add(new admin_setting_configtextarea('local_siraj/featuredtext', get_string('featuredtext', 'local_siraj'),
        get_string('featuredtext_desc', 'local_siraj'), '', PARAM_TEXT, 60, 3));
    $ADMIN->add('localplugins', $settings);
}

if ($hassiteconfig) {
    // Developer-only screens move off Site administration > Development into one "Developer tools" page there
    // (they keep their own addresses). Debugging and Purge caches stay in the tab.
    foreach (\local_siraj\devtools::PAGES as $devpage) {
        if ($node = $ADMIN->locate($devpage)) {
            $node->hidden = true;
        }
    }
    // MoodleNet (Moodle's public sharing network) is not used by company sites: its settings stay out of the menu.
    if ($node = $ADMIN->locate('moodlenet')) {
        $node->hidden = true;
    }
    $ADMIN->add('development', new admin_externalpage('local_siraj_devtools', get_string('devtools', 'local_siraj'),
        new moodle_url('/local/siraj/devtools.php')));
}
