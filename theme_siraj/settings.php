<?php
/**
 * Siraj theme settings.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingsiraj', get_string('configtitle', 'theme_siraj'));
    $page = new admin_settingpage('theme_siraj_brand', get_string('brandsettings', 'theme_siraj'));

    $setting = new admin_setting_configselect('theme_siraj/brandpreset', get_string('brandpreset', 'theme_siraj'),
        get_string('brandpreset_desc', 'theme_siraj'), 'siraj', theme_siraj_presets());
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile('theme_siraj/panellogo', get_string('panellogo', 'theme_siraj'),
        get_string('panellogo_desc', 'theme_siraj'), 'panellogo', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.svg', '.jpg']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_configtext('theme_siraj/logintagline', get_string('logintagline', 'theme_siraj'),
        get_string('logintagline_desc', 'theme_siraj'), '', PARAM_TEXT));
    $page->add(new admin_setting_configtext('theme_siraj/loginsubline', get_string('loginsubline', 'theme_siraj'),
        get_string('loginsubline_desc', 'theme_siraj'), '', PARAM_TEXT));

    $setting = new admin_setting_scsscode('theme_siraj/scss', get_string('rawscss', 'theme_boost'),
        get_string('rawscss_desc', 'theme_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
