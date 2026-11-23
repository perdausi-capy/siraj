<?php
// This file is part of Siraj Learning, built on Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Siraj Home.
 *
 * @package   local_siraj
 * @copyright 2026 Capytech
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/readonly.php');
require(__DIR__ . '/../../config.php');

require_login(null, false);
if (isguestuser()) {
    redirect(new moodle_url('/'));
}

// Someone who only teaches (no learning of their own) starts on Teaching.
$home = new \local_siraj\home($USER);
if (!$home->courses() && \local_siraj\teaching_page::courses_taught((int)$USER->id) && !optional_param('learner', 0, PARAM_BOOL)) {
    redirect(new moodle_url('/local/siraj/teaching.php'));
}

require_once($CFG->dirroot . '/my/lib.php');

// Home is also the person's Moodle dashboard: blocks they add in Edit mode appear under the Siraj sections. Until they
// edit, they see the site default (Site administration > Appearance > Default Dashboard page).
$reset = optional_param('reset', null, PARAM_BOOL);
$context = context_user::instance($USER->id);
if (!$currentpage = my_get_page($USER->id, MY_PAGE_PRIVATE)) {
    throw new \moodle_exception('mymoodlesetup');
}
$PAGE->set_url(new moodle_url('/local/siraj/index.php'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('my-index');
$PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
$PAGE->set_subpage($currentpage->id);
$PAGE->add_body_class('siraj-page siraj-home');
$PAGE->set_title(get_string('home', 'local_siraj'));
$PAGE->set_heading(get_string('home', 'local_siraj'));
$PAGE->set_secondary_navigation(false);

$canedit = empty($CFG->forcedefaultmymoodle) && $PAGE->user_allowed_editing();
// Editing blocks keeps state in the session (the "Add a block" list, moves), so Edit mode needs a writable session.
if ($canedit && !empty($USER->editing) && defined('READ_ONLY_SESSION') && READ_ONLY_SESSION) {
    \core\session\manager::restart_with_write_lock(false);
}
// "content" is a custom region: Moodle remembers it in the session, and its block-move request reads it from there,
// so it is added once the session is writable.
$PAGE->blocks->add_region('content');
if (!$canedit) {
    $USER->editing = 0;
} else if ($reset !== null) {
    require_sesskey();
    my_reset_page($USER->id, MY_PAGE_PRIVATE);
    redirect($PAGE->url);
} else if (!$currentpage->userid && !empty($USER->editing)) {
    // First edit: the person gets their own copy of the default dashboard to change.
    $currentpage = my_copy_page($USER->id, MY_PAGE_PRIVATE);
    $PAGE->set_subpage($currentpage->id);
}

$home = new \local_siraj\home($USER);
$data = $home->export();

echo $OUTPUT->header();
$editing = $PAGE->user_is_editing();
if ($editing || $PAGE->blocks->region_has_content('content', $OUTPUT)) {
    $data['dashboard'] = [
        'editing' => $editing,
        'addblock' => $editing ? $OUTPUT->addblockbutton('content') : '',
        // "Add a block" on Home lists only the dashboard blocks chosen in the Siraj settings.
        // Blocks already on the dashboard are left out too (one of each), except Text, which people add several times.
        'hiddenblocks' => $editing ? implode(',', array_map(fn($b) => 'body.siraj-home .siraj-addblock-item[data-blockname="' . $b . '"]',
            array_unique(array_merge(
                array_diff(array_keys(core_plugin_manager::instance()->get_plugins_of_type('block')), \local_siraj\setup::dashboard_blocks()),
                array_diff(array_map(fn($bi) => $bi->name(), $PAGE->blocks->get_blocks_for_region('content')), ['html']))))) : '',
        'region' => $OUTPUT->custom_block_region('content'),
        'reset' => $editing && $currentpage->userid ? $OUTPUT->single_button(new moodle_url($PAGE->url, ['reset' => 1]),
            get_string('resetpage', 'my'), 'post', ['type' => single_button::BUTTON_SECONDARY]) : '',
        'sysurl' => $editing && has_capability('moodle/my:configsyspages', context_system::instance())
            ? (new moodle_url('/my/indexsys.php'))->out(false) : null,
    ];
}
echo $OUTPUT->render_from_template('local_siraj/home', $data);
echo $OUTPUT->footer();
