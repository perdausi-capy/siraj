<?php
/**
 * Siraj theme config. A Boost child theme: Boost's layouts are inherited, and our own
 * layout/drawers.php and layout/login.php replace Boost's files of the same name.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->name = 'siraj';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->usefallback = true;
$THEME->scss = function($theme) {
    return theme_siraj_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_siraj_get_pre_scss';
$THEME->extrascsscallback = 'theme_boost_get_extra_scss';
$THEME->precompiledcsscallback = 'theme_boost_get_precompiled_css';
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->activityheaderconfig = ['notitle' => true];
