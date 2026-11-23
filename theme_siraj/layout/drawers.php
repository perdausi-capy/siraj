<?php
/**
 * Siraj app layout: Boost's drawers layout inside the Siraj shell (sidebar, top bar, phone tab bar).
 * The page logic is Boost's own, so course index, blocks and activity headers keep working.
 *
 * @package   theme_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if (!isloggedin() || isguestuser()) {
    $extraclasses[] = 'siraj-loggedout';
}
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
// Learners on Moodle's own pages that stay in use (submitting an assignment, a quiz attempt, grades, account
// settings): no course index (Siraj has the learning path), a Siraj "back" link instead of breadcrumbs, and a
// body class the theme styles. Teachers and admins keep Moodle's full pages.
$sirajlearner = isloggedin() && !isguestuser() && !$PAGE->user_is_editing() && file_exists($CFG->dirroot . '/local/siraj/learn.php');
$sirajback = null;
if ($sirajlearner && $PAGE->cm) {
    $sirajlearner = !has_capability('moodle/course:manageactivities', context_module::instance($PAGE->cm->id));
    $sirajback = ['url' => new moodle_url('/local/siraj/learn.php', ['cmid' => $PAGE->cm->id]),
        'label' => get_string('backtolearning', 'theme_siraj', format_string($PAGE->cm->name))];
} else if ($sirajlearner && $PAGE->course && $PAGE->course->id != SITEID && strpos($PAGE->pagetype, 'local-siraj-') !== 0) {
    $sirajlearner = !has_capability('moodle/course:update', context_course::instance($PAGE->course->id));
    $sirajback = ['url' => new moodle_url('/local/siraj/course.php', ['id' => $PAGE->course->id]),
        'label' => get_string('backtocourse', 'theme_siraj', format_string($PAGE->course->fullname))];
} else if ($sirajlearner && (strpos($PAGE->pagetype, 'user-') === 0 || strpos($PAGE->pagetype, 'login-change') === 0
        || strpos($PAGE->pagetype, 'message-') === 0 || strpos($PAGE->pagetype, 'badges-') === 0)) {
    $sirajback = ['url' => new moodle_url('/local/siraj/profile.php'), 'label' => get_string('backtoprofile', 'theme_siraj')];
}
$sirajlearner = $sirajlearner && strpos($PAGE->pagetype, 'local-siraj-') !== 0 && strpos($PAGE->pagetype, 'admin-') !== 0
    && $PAGE->pagetype !== 'my-index'
    && !has_capability('moodle/site:config', context_system::instance());
if ($sirajlearner) {
    $extraclasses[] = 'siraj-moodlepage';
    $ownuser = optional_param('id', $USER->id, PARAM_INT) == $USER->id;
    if ($ownuser && (strpos($PAGE->pagetype, 'user-') === 0 || strpos($PAGE->pagetype, 'login-') === 0
            || strpos($PAGE->pagetype, 'message-') === 0 || strpos($PAGE->pagetype, 'badges-') === 0)) {
        $extraclasses[] = 'siraj-ownuser';
    }
}
// Siraj pages (course overview, Learning mode) have their own learning path, so no Moodle course index.
$courseindex = strpos($PAGE->pagetype, 'local-siraj-') === 0 || ($sirajlearner && $sirajback) ? '' : core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
    $extraclasses = array_values(array_diff($extraclasses, ['drawer-open-index']));
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'sirajback' => $sirajlearner && $sirajback ? ['url' => $sirajback['url']->out(false), 'label' => $sirajback['label']] : null,
];

$PAGE->requires->strings_for_js(['palette_searchfor'], 'theme_siraj');
$templatecontext['shell'] = \theme_siraj\output\shell::context($PAGE);

echo $OUTPUT->render_from_template('theme_siraj/drawers', $templatecontext);
